<?php
/**
 * @var \App\Kernel\View\View $view
 */
?>

<?php $view->component('start'); ?>
<main>
    <section class="main-section">
        <h1>Exchange Rates</h1>
        <div id="exchangeRates" class="table-responsive">
            <!-- Exchange rates table will be populated here -->
        </div>
    </section>
</main>
<?php $view->component('end'); ?>
