<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>NBRB Exchange</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proxy-list/0.2.0/proxy-list.min.js"></script>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<header class="header">
    <div class="container-fluid d-flex justify-content-between align-items-center" style="height: 80px;">
        <div class="logo">
            <span class="logo-text">NBRB</span>
            <span class="logo-exchange">EXCHANGE</span>
        </div>
        <nav class="navbar">
            <ul class="navbar-nav d-flex flex-row align-items-center">
                <li class="nav-item"><a class="nav-link" href="/exchange">Exchange Rate</a></li>
                <li class="nav-item"><a class="nav-link" href="/currencies">Converter</a></li>
                <li class="nav-item dropdown" id="languageButton">
                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button">
                        <i class="fas fa-globe-americas fa-2x"></i><span>&nbsp;EN</span><i class="fas fa-chevron-down ml-1"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="languageDropdown">
                        <a class="dropdown-item" href="#" data-lang="en" id="languageOption">English <i class="fas fa-check"></i></a>
                        <a class="dropdown-item" href="#" data-lang="ru" id="languageOption">Russian</a>
                    </div>
                </li>
                <li class="nav-item dropdown" id="mobileButton">
                    <a class="nav-link dropdown-toggle" href="#" role="button" id="mobileDropdown" data-toggle="dropdown">
                        <i class="fas fa-bars fa-2x" id="mobileDropdownIcon"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="mobileDropdown">
                        <a class="dropdown-item" href="/exchange">Exchange Rate</a>
                        <a class="dropdown-item" href="/currencies">Converter</a>
                        <div class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Language</a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" data-lang="en" id="languageOption">English</a>
                                <a class="dropdown-item" href="#" data-lang="ru" id="languageOption">Russian</a>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>