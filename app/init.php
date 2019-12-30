<?php

use \lib\Router;

Router::registerRoute('/', 'Index.index');
Router::registerRoute('/other', 'Index.other');

Router::registerRoute('/news', 'News.list');
Router::registerRoute('/news/{id}/', 'News.detail', ['id' => '[0-9]+']);

Router::registerRoute('/user/{group}/{username}/', 'User.get', [
    'group' => '[a-z]{3,}',
    'username' => '[A-Za-z][A-Za-z0-9_]{3,}',
]);

// print_r(Router::getInstance());
