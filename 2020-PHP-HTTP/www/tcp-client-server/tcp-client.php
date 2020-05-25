<?php
require_once __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$remote_socket = getenv('HTTP_TCP_SOCK_DEV') ?: 'localhost:8000';

// TCPクライアントソケットを生成
$con = stream_socket_client("tcp://$local_socket", $errno, $errstr, 30);
if (!$socket) {
    echo "message: $errstr\n";
    exit(1);
}

// 終端まで読み出す
while (false !== $line = fgets($con)) {
    echo "Received: $line";
}

// TCPコネクションを切断する
fclose($con);