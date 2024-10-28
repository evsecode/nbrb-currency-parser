<?php
/**
 * @var \App\Kernel\View\View $view
 * @var array $currencies
 */
?>

<?php $view->component('start'); ?>
<main>
    <section class="main-section">
        <h1 id="big-label">Currency Converter</h1>
        <p class="subtitle">Convert popular currencies at effective exchange rates with our currency converter calculator.</p>
        <div class="converter-container">
            <div class="converter-form">
                <!-- Поле для ввода суммы и выбора валюты отправителя -->
                <div class="form-group">
                    <label for="send-amount">From</label>
                    <div class="input-container">
                        <input type="text" id="send-amount" name="send-amount" value="1.00" placeholder="">
                        <button class="btn currency-button" id="send-currency" data-currency="BYN" disabled>
                            <img src="https://flagsapi.com/BY/shiny/64.png" alt="BYN"> BYN
                        </button>
                    </div>
                </div>

                <div class="transfer-button"><i class="fas fa-exchange-alt"></i></div>

                <!-- Поле для выбора валюты получателя -->
                <div class="form-group">
                    <label for="receive-amount">To</label>
                    <div class="input-container">
                        <input type="text" id="receive-amount" value="" placeholder="">
                        <div class="dropdown" id="currencyButton">
                            <button class="btn dropdown-toggle currency-button" id="receive-currency" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-currency="USD">
                                <img src="https://flagsapi.com/US/shiny/64.png" alt="USD"> USD
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-search-container">
                                    <i class="fas fa-search input-icon"></i>
                                    <input type="text" class="form-control dropdown-search" placeholder="Search currency...">
                                </div>
                                <div class="dropdown-currencies-container">
                                    <!-- Список валют из базы -->
                                    <?php foreach ($currencies as $currency) { ?>
                                        <a class="dropdown-item" href="#" data-currency="<?= $currency['abbreviation']; ?>">
                                            <img src="https://flagsapi.com/<?= substr($currency['abbreviation'], 0, 2); ?>/shiny/64.png" alt="<?= $currency['abbreviation']; ?>">
                                            <?= $currency['abbreviation']; ?> – <?= $currency['name']; ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </section>
    <p id="exchange-rate" class="fx-block"></p>
    <div id="error-message" class="error-message" style="display: none;">
        <div class="error-header">ERROR</div>
        <div class="error-body"></div>
    </div>
    <section id="currencyChartContainer">
        <h1 id="big-label">Charts</h1>
        <div id="timeframe-controls">
            <button class="timeframe-btn" data-days="7">Last Week</button>
            <button class="timeframe-btn" data-days="30">Last Month</button>
            <button class="timeframe-btn" data-days="365">Last Year</button>
            <button class="timeframe-btn" data-days="730">Last 2 Years</button>
            <button class="timeframe-btn" data-days="1825">Last 5 Years</button>
        </div>
        <canvas id="currencyChart" width="400" height="200"></canvas>
    </section>
</main>
<?php $view->component('end'); ?>
