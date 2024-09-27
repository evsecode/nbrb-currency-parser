<?php
/**
 * @var \App\Kernel\View\ViewInterface $view
 * @var \App\Kernel\Session\SessionInterface $session
 */
?>

<?php $view->component('start'); ?>
<h1>Login</h1>
<form action="/login" method="post">
    <div style="display: flex; flex-direction: column; align-items: flex-start;">
        <?php if ($session->has(key: 'error')) { ?>
            <p style="color: red">
                <?php echo $session->get(key: 'error') ?>
            </p>
        <?php } ?>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <button>Login</button>
    </div>
</form>
<?php $view->component('end'); ?>
