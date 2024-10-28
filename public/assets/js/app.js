document.addEventListener('DOMContentLoaded', async () => {
    function filterInput(value) {
        let formattedValue = value.replace(/[^0-9.,]/g, '').replace(',', '.');
        if (formattedValue.startsWith('.')) {
            formattedValue = '0' + formattedValue;
        }
        const parsedValue = parseFloat(formattedValue);
        return !isNaN(parsedValue) ? parsedValue.toFixed(2) : '';
    }

    const sendAmountInput = document.getElementById('send-amount');
    const receiveAmountInput = document.getElementById('receive-amount');
    const sendCurrencyButton = document.getElementById('send-currency');
    const receiveCurrencyButton = document.getElementById('receive-currency');
    const searchInput = document.querySelector('.dropdown-search');
    const dropdownItems = document.querySelectorAll('#currencyButton .dropdown-item');
    const exchangeRateElement = document.getElementById('exchange-rate');
    const errorMessageElement = document.getElementById('error-message');
    const errorMessageBodyElement = errorMessageElement ? errorMessageElement.querySelector('.error-body') : null;
    const chartCanvas = document.getElementById('currencyChart');
    const timeframeButtons = document.querySelectorAll('.timeframe-btn');
    const toggleArchiveBtn = document.getElementById('toggle-archive-btn');
    const archiveDiv = document.getElementById('archive-currencies');
    let currencyChart;

    if (toggleArchiveBtn) {
        toggleArchiveBtn.addEventListener('click', async function () {
            if (archiveDiv.style.display === 'none') {
                try {
                    const response = await fetch('/admin/archive-currencies');
                    const data = await response.json();

                    if (data.error) {
                        alert('Error loading archived currencies.');
                    } else {
                        archiveDiv.innerHTML = `
                            <div style="display: flex; align-items: center; margin-top:10px; margin-bottom: 10px;">
                                <span style="flex: 1;"><strong>Name</strong></span>
                                <span style="flex: 1;"><strong>Started at</strong></span>
                                <span style="flex: 1;"><strong>Ended at</strong></span>
                            </div>
                            ${data.map(currency => `
                                <div style="display: flex; align-items: center; margin-bottom: 20px;">
                                    <span style="flex: 1; margin-right: 5px;">${currency.abbreviation}(${currency.name})</span>
                                    <span style="flex: 1;">${currency.start_date}</span>
                                    <span style="flex: 1;">${currency.end_date}</span>
                                </div>
                            `).join('')}
                        `;
                        archiveDiv.style.display = 'block';
                        toggleArchiveBtn.innerHTML = 'Hide Archived Currencies';
                    }
                } catch (error) {
                    alert('Failed to fetch archive currencies');
                }
            } else {
                archiveDiv.style.display = 'none';
                toggleArchiveBtn.innerHTML = 'Show Archived Currencies<br>(admin option)';
            }
        });
    }

    async function fetchHistoricalRates(currencyAbbr, startDate, endDate) {
        try {
            const response = await fetch(`/currencies/historical?currency_abbr=${currencyAbbr}&start_date=${startDate}&end_date=${endDate}`);
            const data = await response.json();
            return data;
        } catch (error) {
            return [];
        }
    }

    async function drawCurrencyChart(currencyAbbr, startDate = null, endDate = null, days = 365) {
        if (!currencyAbbr) {
            console.log("Currency abbreviation is null, cannot draw chart.");
            return;
        }

        if (!startDate || !endDate) {
            const startDateObj = new Date();
            startDateObj.setFullYear(startDateObj.getFullYear() - 1);
            startDate = startDateObj.toISOString().split('T')[0];
            endDate = new Date().toISOString().split('T')[0];
            days = 365;
        }

        const historicalRates = await fetchHistoricalRates(currencyAbbr, startDate, endDate);

        if (historicalRates.length === 0) {
            console.log('No historical data available for currency:', currencyAbbr);
            return;
        }

        const labels = historicalRates.map(rate => rate.Date);
        const rates = historicalRates.map(rate => rate.Cur_OfficialRate);

        const ctx = chartCanvas.getContext('2d');

        if (currencyChart) {
            currencyChart.destroy();
        }

        currencyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: `Exchange Rate (${currencyAbbr})`,
                    data: rates,
                    borderColor: "#000",
                    backgroundColor: "#FFDD00",
                    borderWidth: 2,
                    fill: true,
                    pointRadius: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: determineTimeUnit(days)
                        }
                    },
                    y: {
                        beginAtZero: false
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    },
                    crosshair: {
                        line: {
                            color: '#FFD700',
                            width: 2
                        }
                    }
                },
                hover: {
                    mode: 'index',
                    intersect: false,
                    onHover: function (event, chartElement) {
                        event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                    }
                }
            }
        });
    }

    async function getExchangeRate(toCurrency) {
        try {
            const fromCurrency = sendCurrencyButton.getAttribute('data-currency');

            const response = await fetch(`/convert`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    from_currency: fromCurrency,
                    to_currency: toCurrency
                })
            });

            const text = await response.text();

            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.statusText}`);
            }

            const data = JSON.parse(text);

            if (data.error) {
                throw new Error(data.error);
            }

            return { rate: data.rate, reverseRate: data.reverseRate, fromScale: data.fromScale, toScale: data.toScale};
        } catch (error) {
            showError(`Failed to fetch exchange rate: ${error.message}`);
            return null;
        }
    }

    async function convertCurrency(reverse = false) {
        const sendAmount = parseFloat(filterInput(sendAmountInput.value));
        const receiveAmount = parseFloat(filterInput(receiveAmountInput.value));
        const sendCurrency = sendCurrencyButton.getAttribute('data-currency');
        const receiveCurrency = receiveCurrencyButton.getAttribute('data-currency');

        if (reverse) {
            if (isNaN(receiveAmount) || receiveAmount <= 0) {
                showError('Please enter a valid amount.');
                return;
            }
        } else {
            if (isNaN(sendAmount) || sendAmount <= 0) {
                showError('Please enter a valid amount.');
                return;
            }
        }

        const rateInfo = await getExchangeRate(receiveCurrency);
        if (!rateInfo || isNaN(rateInfo.rate) || isNaN(rateInfo.reverseRate) || isNaN(rateInfo.fromScale) || isNaN(rateInfo.toScale)) {
            showError('Invalid rate info.');
            return;
        }

        const rate = parseFloat(rateInfo.rate);
        const reverseRate = parseFloat(rateInfo.reverseRate);
        const fromScale = parseFloat(rateInfo.fromScale);
        const toScale = parseFloat(rateInfo.toScale);

        if (reverse) {
            const convertedAmount = (receiveAmount * reverseRate) / fromScale;
            sendAmountInput.value = convertedAmount.toFixed(2);
        } else {
            const convertedAmount = (sendAmount * rate) / fromScale;
            receiveAmountInput.value = convertedAmount.toFixed(2);
        }

        const fxLabel = `
            <div class="fx-label">FX</div>
            <div class="fx-rate">${fromScale} ${sendCurrency} = ${(rate / fromScale).toFixed(4)} ${receiveCurrency}</div>
            <div class="fx-rate">${toScale} ${receiveCurrency} = ${(toScale / rate).toFixed(4)} ${sendCurrency}</div>
        `;
        exchangeRateElement.innerHTML = fxLabel;
        hideError();
    }

    function renderExchangeRates(exchangeRates) {
        const tableBody = document.getElementById('exchangeRatesTable');
        tableBody.innerHTML = '';

        exchangeRates.forEach(rate => {
            const row = `<tr>
            <td>${rate.Cur_Abbreviation}</td>
            <td>${rate.Cur_Name}</td>
            <td>${rate.Cur_Scale}</td>
            <td>${rate.Cur_OfficialRate}</td>
        </tr>`;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    }

    sendAmountInput.addEventListener('blur', (e) => {
        const value = filterInput(e.target.value);
        if (!isNaN(value)) {
            e.target.value = value;
        }
        convertCurrency();
    });

    receiveAmountInput.addEventListener('blur', (e) => {
        const value = filterInput(e.target.value);
        if (!isNaN(value)) {
            e.target.value = value;
        }
        convertCurrency(true);
    });

    searchInput.addEventListener('input', () => {
        const filter = searchInput.value.toLowerCase();
        dropdownItems.forEach(item => {
            const currencyText = item.textContent.toLowerCase();
            item.style.display = currencyText.includes(filter) ? '' : 'none';
        });
    });


    dropdownItems.forEach(item => {
        item.addEventListener('click', (event) => {
            event.preventDefault();
            const selectedCurrency = item.getAttribute('data-currency');
            const flagUrl = `https://flagsapi.com/${selectedCurrency.slice(0, 2)}/shiny/64.png`;
            receiveCurrencyButton.setAttribute('data-currency', selectedCurrency);
            receiveCurrencyButton.innerHTML = `<img src="${flagUrl}" alt="${selectedCurrency}"> ${selectedCurrency}`;
            convertCurrency();
        });
    });

    function determineTimeUnit(days) {
        if (days <= 30) {
            return 'day';
        } else if (days <= 365) {
            return 'month';
        } else {
            return 'year';
        }
    }

    timeframeButtons.forEach(button => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();
            const days = parseInt(button.getAttribute('data-days'), 10);
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(endDate.getDate() - days);

            const currencyAbbr = receiveCurrencyButton.getAttribute('data-currency');
            await drawCurrencyChart(currencyAbbr, startDate.toISOString().split('T')[0], endDate.toISOString().split('T')[0], days);
        });
    });

    const initialCurrencyAbbr = receiveCurrencyButton.getAttribute('data-currency');
    if (initialCurrencyAbbr) {
        await drawCurrencyChart(initialCurrencyAbbr);
    }

    function resetSearchAndDropdown() {
        searchInput.value = '';
        dropdownItems.forEach(item => item.style.display = '');
    }

    dropdownItems.forEach(item => {
        item.addEventListener('click', (event) => {
            event.preventDefault();
            receiveCurrencyButton.setAttribute('data-currency', item.getAttribute('data-currency'));
            convertCurrency();
            resetSearchAndDropdown();
        });
    });

    searchInput.addEventListener('input', () => {
        const filter = searchInput.value.toLowerCase();
        dropdownItems.forEach(item => item.style.display = item.textContent.toLowerCase().includes(filter) ? '' : 'none');
    });

    document.querySelectorAll('.timeframe-btn').forEach(button => {
        button.addEventListener('click', async () => {
            const days = button.getAttribute('data-days');
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(endDate.getDate() - days);

            await drawCurrencyChart(currencyAbbr, startDate.toISOString().split('T')[0], endDate.toISOString().split('T')[0]);
        });
    });

    function showError(message) {
        errorMessageBodyElement.textContent = message;
        errorMessageElement.style.display = 'block';
    }

    function hideError() {
        errorMessageElement.style.display = 'none';
    }

    await convertCurrency();
});
