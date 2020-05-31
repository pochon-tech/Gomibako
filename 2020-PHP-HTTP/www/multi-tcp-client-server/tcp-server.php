<?php
require_once __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$local_socket = getenv('HTTP_TCP_SOCK_DEV') ?: 'localhost:8000';

// PHPの実行時間を３分にしておく。
set_time_limit(180);

// 子プロセスの終了
declare(ticks = 1);
pcntl_signal(SIGCHLD, function ($sig) {
    echo "pcntl_signal";
    if ($sig !== SIGCHLD) return;
    $ignore = null;
    while (0 < $rc = pcntl_waitpid(-1, $ignore, WNOHANG));
});

// TCPサーバソケットを生成する。
$socket = stream_socket_server("tcp://$local_socket", $errno, $errstr);
if (!$socket) {
    echo "message: $errstr\n";
    exit(1);
}
echo "Listening TCP connection on $local_socket...\n";

// TCPクライアントソケットを受け入れる
do if ($con = stream_socket_accept($socket, -1)) {
    // プロセスを分岐する
    // 親プロセスは直ちに次の stream_socket_accept の待機に戻る
    // 子プロセスはそのまま下の for に続く
    if (pcntl_fork()) {
        continue;
    }
    // 3回繰り返してメッセージを送信
    // 複数人同時に処理される
    for ($i = 0; $i < 3; ++$i) {
        $msg = "Hello, World [$i]\n";
        echo "Sent: $msg";
        fwrite($con, $msg);
        sleep(1);
    }
    // TCPコネクションを切断
    fclose($con);
    // 子プロセスを終了
    exit(0);
} while (true);