<?php

/**
 * ログイン状態によってリダイレクトを行う。
 * session_startのラッパー関数
 * 初回時もしくは失敗時にはヘッダを送信する。
 */
function require_unlogined_session()
{
    // セッション開始
    session_start();
    // ログインしていればトップに遷移
    if (isset($_SESSION['username'])) {
        header('Location: /');
        exit;
    }
}
function require_logined_session()
{
    // セッション開始
    session_start();
    // ログインしていなければ /login.php に遷移
    if (!isset($_SESSION['username'])) {
        header('Location: /login.php');
        exit;
    }
}
/**
 * CSRFトークン発行
 */
function generate_token()
{
    return hash('sha256', session_id());
}
/**
 * CSRFトークンの検証
 * 
 * @param string $token
 * @return bool 検証結果
 */
function validate_token($token)
{
    // 送信されたTokenとこちらで生成したハッシュと一致するか検証
    return $token === generate_token();
}
/**
 * htmlspecialcharsのラッパー関数
 *
 * @param string $str
 * @return string
 */
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}