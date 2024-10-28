<?php
/**
 * @var \App\Kernel\View\ViewInterface $view
 * @var \App\Kernel\Session\SessionInterface $session
 */
?>

<?php $view->component('start'); ?>
<div class="auth-container">
    <h1 class="auth-title">Register</h1>
    <form action="/register" method="post" class="auth-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="Your email">
            <?php if ($session->has(key: 'email')) { ?>
                <ul class="auth-error-list">
                    <?php foreach ($session->getFlash(key: 'email') as $error) { ?>
                        <li class="auth-error"><?php echo $error ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>

        <div class="form-group">
            <label for="name">Username</label>
            <input type="text" id="name" name="name" class="form-control" required placeholder="Your username">
            <?php if ($session->has(key: 'name')) { ?>
                <ul class="auth-error-list">
                    <li class="auth-error"><?php echo $session->getFlash(key: 'name')[0]?></li>
                </ul>
            <?php } ?>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required placeholder="Your password">
            <?php if ($session->has(key: 'password')) { ?>
                <ul class="auth-error-list">
                    <?php foreach ($session->getFlash(key: 'password') as $error) { ?>
                        <li class="auth-error"><?php echo $error ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Confirm password">
            <?php if ($session->has(key: 'password_confirmation')) { ?>
                <ul class="auth-error-list">
                    <?php foreach ($session->getFlash(key: 'password_confirmation') as $error) { ?>
                        <li class="auth-error"><?php echo $error ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>

        <button type="submit" class="auth-btn">Register</button>
        <p class="auth-helper">Already have an account? <a href="/login" class="auth-link">Login</a></p>
    </form>
</div>
<?php $view->component('end'); ?>
