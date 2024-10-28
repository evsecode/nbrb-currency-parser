<?php
/**
 * @var \App\Kernel\Auth\AuthInterface $auth
 */
$user = $auth->user();
?>

<header class="header">
    <div class="container-fluid d-flex justify-content-between align-items-center" style="height: 80px;">
        <a style="color: inherit; text-decoration: none" href="/currencies">
            <div class="logo">
                <span class="logo-text">NBRB</span>
                <span class="logo-exchange">EXCHANGE</span>
            </div>
        </a>
        <nav class="navbar">
            <ul class="navbar-nav d-flex flex-row align-items-center">
                <li class="nav-item"><a class="nav-link" href="/exchange">Exchange Rate</a></li>
                <li class="nav-item"><a class="nav-link" href="/currencies">Converter</a></li>
<!--                <li class="nav-item dropdown" id="languageButton">-->
<!--                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button">-->
<!--                        <i class="fas fa-globe-americas fa-2x"></i><span>&nbsp;EN</span><i class="fas fa-chevron-down ml-1"></i>-->
<!--                    </a>-->
<!--                    <div class="dropdown-menu" aria-labelledby="languageDropdown">-->
<!--                        <a class="dropdown-item" href="#" data-lang="en" id="languageOption">English <i class="fas fa-check"></i></a>-->
<!--                        <a class="dropdown-item" href="#" data-lang="ru" id="languageOption">Russian</a>-->
<!--                    </div>-->
                <?php if (! $auth->check()) {?>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                <?php } else {?>
                    <li class="nav-item dropdown" id="accountDropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="accountLink" role="button" data-toggle="dropdown">
                            Account
                        </a>
                        <div class="dropdown-menu" aria-labelledby="accountLink">
                            <a class="dropdown-item" href="/user">Profile</a>
                            <form action="/logout" method="POST" style="margin: 0;">
                                <button class="dropdown-item" type="submit">Logout</button>
                            </form>
                        </div>
                    </li>
                <?php } ?>
                <li class="nav-item dropdown" id="mobileButton">
                    <a class="nav-link dropdown-toggle" href="#" role="button" id="mobileDropdown" data-toggle="dropdown">
                        <i class="fas fa-bars fa-2x" id="mobileDropdownIcon"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="mobileDropdown">
                        <a class="dropdown-item" href="/exchange">Exchange Rate</a>
                        <a class="dropdown-item" href="/currencies">Converter</a>
                        <?php if (! $auth->check()) {?>
                            <a class="dropdown-item" href="/login">Login</a>
                        <?php } else {?>
                            <a class="dropdown-item dropdown-toggle" href="#" id="accountDropdown" role="button" data-toggle="dropdown">Account</a>
                            <div class="dropdown-menu" aria-labelledby="accountDropdown">
                                <a class="dropdown-item" href="/user" data-lang="en" id="accountOption">Profile</a>
                                <form action="/logout" method="POST" style="margin: 0;">
                                    <button class="dropdown-item" type="submit" data-lang="ru" id="accountOption">Logout</button>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>