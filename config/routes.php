<?php

return [
    '/home' => function () {
        include_once APP_PATH.'/views/pages/home.php';
    },
    '/currencies' => function () {
        include_once APP_PATH.'/views/pages/currencies.php';
    },
];
