document.addEventListener('DOMContentLoaded', async () => {

    if (document.getElementById('exchangeRates')) {
        await fetchExchangeRates(); // Load exchange rates when page loads
    } else {
        console.log('exchangeRates element not found, executing other scripts');  // Debug output

        const sendAmountInput = document.getElementById('send-amount');
        const sendCurrencyButton = document.getElementById('send-currency');
        const receiveAmountInput = document.getElementById('receive-amount');
        const receiveCurrencyButton = document.getElementById('receive-currency');
        const exchangeRateElement = document.getElementById('exchange-rate');
        const errorMessageElement = document.getElementById('error-message');
        const errorMessageBodyElement = errorMessageElement.querySelector('.error-body');
        const transferButton = document.querySelector('.transfer-button');

        const translations = {
            "currencyConverter": {
                "en": "Currency Converter",
                "ru": "Конвертер валют"
            },
        };

        async function getExchangeRate(toCurrency) {
            try {
                const cacheKey = `exchange_rate_${toCurrency}`;
                const cachedRate = localStorage.getItem(cacheKey);

                if (cachedRate) {
                    const parsedRate = JSON.parse(cachedRate);
                    return parsedRate;
                }

                const response = await fetch(`https://www.nbrb.by/API/ExRates/Rates/${toCurrency}?ParamMode=2`);
                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();

                const rateInfo = {
                    rate: data.Cur_OfficialRate,
                    scale: data.Cur_Scale ? data.Cur_Scale : 1
                };

                localStorage.setItem(cacheKey, JSON.stringify(rateInfo));
                return rateInfo;
            } catch (error) {
                showError(`Failed to fetch exchange rate: ${error.message}`);
                return null;
            }
        }

        async function loadCurrencies() {
            try {
                const response = await fetch('https://www.nbrb.by/API/ExRates/Currencies');
                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();
                const dropdownMenu = document.querySelector('.dropdown-currencies-container');
                data.forEach(currency => {
                    if (!loadedCurrencies.has(currency.Cur_Abbreviation)) {
                        const currencyItem = document.createElement('a');
                        currencyItem.classList.add('dropdown-item');
                        currencyItem.href = '#';
                        currencyItem.setAttribute('data-currency', currency.Cur_Abbreviation);
                        currencyItem.innerHTML = `<img src="https://flagsapi.com/${currency.Cur_Abbreviation.slice(0,2)}/shiny/64.png" alt="${currency.Cur_Abbreviation}"> ${currency.Cur_Abbreviation} – ${currency.Cur_Name}`;
                        dropdownMenu.appendChild(currencyItem);

                        currencyItem.addEventListener('click', (e) => {
                            e.preventDefault();
                            const button = document.querySelector('#receive-currency');
                            button.innerHTML = `<img src="https://flagsapi.com/${currency.Cur_Abbreviation.slice(0,2)}/shiny/64.png" alt="${currency.Cur_Abbreviation}"> ${currency.Cur_Abbreviation}`;
                            button.setAttribute('data-currency', currencyItem.getAttribute('data-currency'));
                            convertCurrency();
                        });

                        loadedCurrencies.add(currency.Cur_Abbreviation);
                    }
                });
            } catch (error) {
                showError(`Failed to load currencies: ${error.message}`);
                document.querySelector('.currency-button').disabled = true;
            }
        }

        async function convertCurrency(reverse = false) {
            const sendAmount = parseFloat(sendAmountInput.value.replace(',', '.'));
            const receiveAmount = parseFloat(receiveAmountInput.value.replace(',', '.'));
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

            if (rateInfo && typeof rateInfo === 'object' && rateInfo.rate && rateInfo.scale) {
                const rate = parseFloat(rateInfo.rate);
                const scale = parseFloat(rateInfo.scale);

                if (!isNaN(rate) && !isNaN(scale)) {
                    if (reverse) {
                        const convertedAmount = (receiveAmount * rate) / scale;
                        console.log('Converted amount (reverse):', convertedAmount); // Debug output
                        sendAmountInput.value = convertedAmount.toFixed(2).replace('.', ',');
                    } else {
                        const convertedAmount = (sendAmount * scale) / rate;
                        receiveAmountInput.value = convertedAmount.toFixed(2).replace('.', ',');
                    }
                    exchangeRateElement.textContent = `FX: 1.00 ${sendCurrency} = ${(scale / rate).toFixed(4)} ${receiveCurrency}`;
                    hideError();
                } else {
                    showError('Invalid rate or scale value.');
                }
            } else {
                showError('Invalid rate info.');
            }
        }

        function showError(message) {
            errorMessageBodyElement.textContent = message;
            errorMessageElement.style.display = 'block';
        }

        function hideError() {
            errorMessageElement.style.display = 'none';
        }

        function translatePage(lang) {
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                el.textContent = translations[key][lang];
            });
            document.querySelector('#languageDropdown span').textContent = '\u00A0'+lang.toUpperCase();
        }

        function filterDropdown(event) {
            const input = event.target;
            const filter = input.value.toLowerCase();
            const dropdownMenu = input.closest('.dropdown-menu');
            const items = dropdownMenu.querySelectorAll('.dropdown-item');

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(filter) ? '' : 'none';
            });
        }

        const loadedCurrencies = new Set();

        sendAmountInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9.]/g, '');
        });

        sendAmountInput.addEventListener('blur', (e) => {
            const value = parseFloat(e.target.value);
            if (!isNaN(value)) {
                e.target.value = value.toFixed(2);
            }
            convertCurrency();
        });

        receiveAmountInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9.]/g, '');
        });

        receiveAmountInput.addEventListener('blur', (e) => {
            const value = parseFloat(e.target.value);
            if (!isNaN(value)) {
                e.target.value = value.toFixed(2);
            }
            convertCurrency(true);
        });

        document.querySelectorAll('.currency-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const dropdown = button.nextElementSibling;
                dropdown.classList.toggle('show');
            });
        });

        document.addEventListener('click', (e) => {
            if (!e.target.matches('.currency-button') && !e.target.matches('.dropdown-search')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu.classList.contains('show')) {
                        menu.classList.remove('show');
                    }
                });
            }
        });

        document.querySelectorAll('.dropdown-search').forEach(input => {
            input.addEventListener('input', filterDropdown);
        });

        document.querySelectorAll("[id^='languageOption']").forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                translatePage(item.getAttribute('data-lang'));
            });
        });

        document.querySelectorAll('.dropdown-submenu > a').forEach(el => {
            el.addEventListener('click', function(e) {
                e.stopPropagation();
                const submenu = this.nextElementSibling;
                submenu.classList.toggle('show');
                this.querySelector('.fas.fa-chevron-down').classList.toggle('rotate-180');
                if (submenu.classList.contains('show')) {
                    submenu.style.top = `${submenu.offsetParent.scrollTop}px`;
                    submenu.style.height = "auto";
                }
            });
        });

        localStorage.setItem('exchange_rate_test', JSON.stringify({ rate: 0.3154, scale: 1 }));
        const testRate = JSON.parse(localStorage.getItem('exchange_rate_test'));

        await loadCurrencies();
        convertCurrency();
    }
});

async function fetchExchangeRates() {
    try {
        const response = await fetch('https://www.nbrb.by/api/exrates/rates?periodicity=0');
        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();

        let exchangeRatesTable = `
            <table class="table table-bordered table-striped">
                <thead class="thead-dark header-yellow">
                    <tr>
                        <th>Code</th>
                        <th>Currency</th>
                        <th>Units</th>
                        <th>Rate</th>
                    </tr>
                </thead>
                <tbody class="body-white">`;

        data.forEach((rate, index) => {
            exchangeRatesTable += `
                <tr>
                    <td>${rate.Cur_Abbreviation}</td>
                    <td>${rate.Cur_Name}</td>
                    <td>${rate.Cur_Scale}</td>
                    <td>${rate.Cur_OfficialRate}</td>
                </tr>`;
        });

        exchangeRatesTable += `
                </tbody>
            </table>`;

        document.getElementById('exchangeRates').innerHTML = exchangeRatesTable;
    } catch (error) {
        console.error('Failed to fetch exchange rates:', error);
    }
}