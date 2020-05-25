<?php
require_once __DIR__.'/vendor/autoload.php';

// 読み方が、new Dotenv(...) -> Dotenv::create(...) -> Dotenv::createImmutable(...)に変わってる
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$remote_socket = getenv('HTTP_TCP_SOCK_DEV') ?: 'localhost:8000';
echo $remote_socket;
