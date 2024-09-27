<?php
/**
 * @var \App\Kernel\View\ViewInterface $view
 * @var \App\Kernel\Session\SessionInterface $session
 */
?>

<?php $view->component('start'); ?>
<h1>Register</h1>
<form action="/register" method="post">
    <div style="display: flex; flex-direction: column; align-items: flex-start;">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        <?php if ($session->has(key: 'email')) { ?>
            <ul>
                <?php foreach ($session->getFlash(key: 'email') as $error) { ?>
                    <li style="color: red;"><?php echo $error ?></li>
                <?php } ?>
            </ul>
        <?php } ?>
        <label for="name">Username</label>
        <input type="text" id="name" name="name" required>
        <?php if ($session->has(key: 'name')) { ?>
            <ul>
                <?php foreach ($session->getFlash(key: 'name') as $error) { ?>
                    <li style="color: red;"><?php echo $error ?></li>
                <?php } ?>
            </ul>
        <?php } ?>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <?php if ($session->has(key: 'password')) { ?>
            <ul>
                <?php foreach ($session->getFlash(key: 'password') as $error) { ?>
                    <li style="color: red;"><?php echo $error ?></li>
                <?php } ?>
            </ul>
        <?php } ?>
        <button>Register</button>
    </div>
</form>
<?php $view->component('end'); ?>
