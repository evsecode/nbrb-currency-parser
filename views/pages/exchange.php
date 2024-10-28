<?php
/**
 * @var \App\Kernel\View\View $view
 * @var array<\App\Models\ExchangeRate> $exchangeRates
 */
?>

<?php $view->component('start'); ?>
<main>
    <section class="main-section">

        <div class="d-flex justify-content-between align-items-center">
            <h1 id="big-label">Exchange Rates</h1>
        </div>
        <div id="exchangeRates" class="table-responsive mt-4">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark header-yellow">
                <tr>
                    <th>Code</th>
                    <th>Currency</th>
                    <th>Units</th>
                    <th>Rate</th>
                </tr>
                </thead>
                <tbody class="body-white" id="exchangeRatesTable">
                <?php foreach ($exchangeRates as $rate) { ?>
                    <tr>
                        <td><?= $rate->Cur_Abbreviation() ?></td>
                        <td><?= $rate->Cur_Name() ?></td>
                        <td><?= $rate->Cur_Scale() ?></td>
                        <td><?= $rate->Cur_OfficialRate() ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php $view->component('end'); ?>
