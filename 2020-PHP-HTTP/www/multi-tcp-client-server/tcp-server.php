<?php
require_once __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$local_socket = getenv('HTTP_TCP_SOCK_DEV') ?: 'localhost:8000';

// PHPの実行時間を３分にしておく。
set_time_limit(180);

