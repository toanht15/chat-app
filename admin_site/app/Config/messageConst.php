<?php
// 初期化
$message = [];

// 共通
$const = [];
$const['saveSuccessful'] = "保存処理に成功しました";
$const['saveFailed'] = "保存処理に失敗しました";
$const['deleteSuccessful'] = "削除処理に成功しました";
$const['deleteFailed'] = "削除処理に失敗しました";
$const['doubleLoginFailed'] = "他のブラウザでログインされたため、自動ログアウトを行いました";
$const['notFoundId'] = "対象のIDは削除されたか、アクセスできません";
$message['const'] = $const;

// セット
$config['message'] = $message;
