<?php

require_once __DIR__.'/functions.php';
require_unlogined_session();

// 事前に生成したユーザごとのパスワードハッシュの配列
$hashes = [
    'user' => '$2y$10$QXGjP1A8SrC25hL74qNCi.sYjAdFvjtC4sTknedtS8spEByeZFTmu'
];

// Userから受け取ったユーザ名とパスワード
$username = filter_input(INPUT_POST, 'username');
$password = filter_input(INPUT_POST, 'password');

// POSTメソッドの時のみ実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        // CSRFトークンの検証
        validate_token(filter_input(INPUT_POST, 'token')) &&
        // 入力されたパスワードの検証
        password_verify(
            $password,
            isset($hashes[$username])
                ? $hashes[$username]
                : '$2y$10$abcdefghijklmnopqrstuv' // ユーザ名が存在しないときだけ極端に速くなるのを防ぐ
        )
    ) {
        // 認証成功時
        session_regenerate_id(true); // SessionIDの追跡を防ぐ。
        $_SESSION['username'] = $username; // usernameのセット。
        header('Location: /'); // トップ画面へ遷移
        exit; 
    }
    // 認証失敗時
    http_response_code(403);
}
header('Content-Type: text/html; charset=UTF-8');
?>

<!DOCTYPE html>
<title>Login</title>
<h1>Please Login</h1>
<form method="post" action="">
    username: <input type="text" name="username" value="" >
    password: <input type="password" name="password" value="" >
    <input type="hidden" name="token" value="<?=h(generate_token())?>">
    <input type="submit" value="login" />
</form>
<?php if (http_response_code() === 403): ?>
<p style="color: red;">ユーザ名またはパスワードが違います</p>
<?php endif; ?>