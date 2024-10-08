<?php
/**
 * @var \App\Kernel\View\View $view
 * @var \App\Kernel\Session\Session $session
 */
?>

<?php $view->component('start'); ?>
<h1>Add currency to show</h1>

<form action="/admin/currencies/add" method="post">
    <div id="currenciesList">
        <label>Currencies list</label>
        <ul>
            <li><input type="checkbox" name="currencies[]" value="USD">USD</li>
            <li><input type="checkbox" name="currencies[]" value="EUR">EUR</li>
            <li><input type="checkbox" name="currencies[]" value="RUB">RUB</li>
            <li><input type="checkbox" name="currencies[]" value="BYN">BYN</li>
        </ul>
    </div>
    <?php if ($session->has(key: 'currencies')) { ?>
        <ul>
            <?php foreach ($session->getFlash(key: 'currencies') as $error) { ?>
                <li style="color: red;"><?php echo $error ?></li>
            <?php } ?>
        </ul>
    <?php } ?>
    <div>
        <p>
            <button>Set</button>
        </p>
    </div>
</form>
<?php $view->component('end'); ?>
