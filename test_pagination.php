<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$paginator = new Illuminate\Pagination\LengthAwarePaginator([], 1000, 10, 1);
$paginator->onEachSide(1);
print_r(Illuminate\Pagination\UrlWindow::make($paginator));

$paginator2 = new Illuminate\Pagination\LengthAwarePaginator([], 1000, 10, 1);
$paginator2->onEachSide(0);
print_r(Illuminate\Pagination\UrlWindow::make($paginator2));
