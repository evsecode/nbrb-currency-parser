<?php
/**
 * @var \App\Kernel\View\View $view
 */
?>

<?php $view->component('start'); ?>
    <main>
        <section class="main-section">
            <h1 data-i18n="currencyConverter">Currency Converter</h1>
            <p class="subtitle">Convert popular currencies at effective exchange rates with our currency converter calculator.</p>
            <div class="converter-container">
                <div class="converter-form">
                    <div class="form-group">
                        <label for="send-amount">From</label>
                        <div class="input-container">
                            <input type="text" id="send-amount" value="1.00" placeholder="">
                            <button class="btn currency-button" id="send-currency" data-currency="BYN" disabled>
                                <img src="https://flagsapi.com/BY/shiny/64.png" alt="BYN"> BYN
                            </button>
                        </div>
                    </div>
                    <button class="transfer-button"><i class="fas fa-exchange-alt"></i></button>
                    <div class="form-group">
                        <label for="receive-amount">To</label>
                        <div class="input-container">
                            <input type="text" id="receive-amount" value="" placeholder="">
                            <div class="dropdown" id="currencyButton">
                                <button class="btn dropdown-toggle currency-button" id="receive-currency" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-currency="USD">
                                    <img src="https://flagsapi.com/US/shiny/64.png" alt="USD"> USD
                                </button>
                                <div class="dropdown-menu">
                                    <div class="dropdown-search-container">
                                        <i class="fas fa-search input-icon"></i>
                                        <input type="text" class="form-control dropdown-search" placeholder="">
                                    </div>
                                    <div class="dropdown-currencies-container">
                                        <!-- Currency items will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="exchange-rate" id="exchange-rate"></p>
                <div id="error-message" class="error-message" style="display: none;">
                    <div class="error-header">ERROR</div>
                    <div class="error-body"></div>
                </div>
            </div>
        </section>
    </main>
<?php $view->component('end'); ?>