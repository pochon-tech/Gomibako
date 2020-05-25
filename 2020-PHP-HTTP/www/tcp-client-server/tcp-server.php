<?php
require_once __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$local_socket = getenv('HTTP_TCP_SOCK_DEV') ?: 'localhost:8000';

// PHPの実行時間を３分にしておく。
set_time_limit(180);

// TCPサーバソケットを生成する。
$socket = stream_socket_server("tcp://$local_socket", $errno, $errstr);
if (!$socket) {
    echo "message: $errstr\n";
    exit(1);
}
echo "Listening TCP connection on $local_socket...\n";

// TCPクライアントソケットを受け入れる
while($con = stream_socket_accept($socket, -1)) {
    for ($i = 0; $i < 3; ++$i) {
        $msg = "Hello $i !!\n";
        echo "Server Sent: $msg";
        fwrite($con, $msg);
        sleep(1);
    }
    // TCPコネクションを切断
    fclose($con);
}
