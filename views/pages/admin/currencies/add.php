<?php
/**
 * @var \App\Kernel\View\View $view
 */
?>

<?php $view->component('start'); ?>
<h1>Add currency to show</h1>

<form action="/admin/currencies/add" method="post">
    <div id="currenciesList">
        <label>Currencies list</label>
        <ul>
            <li><input type="checkbox" name="currencies[]" value="rub">rub</li>
            <li><input type="checkbox" name="currencies[]" value="byn">byn</li>
            <li><input type="checkbox" name="currencies[]" value="usd">usd</li>
            <li><input type="checkbox" name="currencies[]" value="eur">eur</li>
        </ul>
    </div>
    <div>
        <button>Set</button>
    </div>
</form>
<?php $view->component('end'); ?>
