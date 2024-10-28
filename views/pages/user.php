<?php
/**
 * @var \App\Kernel\View\View $view
 * @var \App\Kernel\Auth\User $user
 * @var bool $isAdmin
 */
?>

<?php $view->component('start'); ?>
    <div class="auth-container">
        <h1 class="auth-title">User Profile</h1>
        <div class="profile-info">
            <p><strong>Email:</strong> <?= htmlspecialchars($user->email()) ?></p>
            <p><strong>Username:</strong> <?= htmlspecialchars($user->name()) ?></p>
            <?php if (isset($isAdmin) && $isAdmin): ?>
                <button id="toggle-archive-btn" class="auth-btn">Show Archived Currencies<br>(admin option)</button>
                <div id="archive-currencies" style="display: none;"></div>
            <?php endif; ?>
            <form action="/logout" method="POST">
                <button class="auth-btn">Logout</button>
            </form>
        </div>
    </div>
<?php $view->component('end'); ?>