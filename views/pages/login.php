<?php
/**
 * @var \App\Kernel\View\ViewInterface $view
 * @var \App\Kernel\Session\SessionInterface $session
 */
?>

<?php $view->component('start'); ?>
<div class="auth-container">
    <h1 class="auth-title">Login</h1>
    <form action="/login" method="post" class="auth-form">
        <?php if ($session->has(key: 'error')) { ?>
            <div class="auth-error">
                <p><?php echo $session->get(key: 'error') ?></p>
            </div>
        <?php } ?>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="Your email">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required placeholder="Your password">
        </div>
        <button type="submit" class="auth-btn">Login</button>
        <p class="auth-helper">Don't have an account? <a href="/register" class="auth-link">Register</a></p>
    </form>
</div>
<?php $view->component('end'); ?>
