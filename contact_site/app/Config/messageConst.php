<?php
// 初期化
$message = [];

// 共通
$const = [];
$const['saveSuccessful'] = "保存処理に成功しました";
$const['saveFailed'] = "保存処理に失敗しました";
$const['deleteSuccessful'] = "削除処理に成功しました";
$const['deleteFailed'] = "削除処理に失敗しました";
$const['fileSaveFailed'] = "ファイルアップロード処理に失敗しました";
$const['doubleLoginFailed'] = "他のブラウザでログインされたため、自動ログアウトを行いました";
$const['notFoundId'] = "対象のIDは削除されたか、アクセスできません";
$const['configChanged'] = "他のユーザーによって設定が変更されました。再度設定して下さい。";
$const['deletedHistory'] = "既に削除済みのデータです";

$const['chatStartConfirm'] = "※ お客様に「許可しますか？」のダイアログが表示されるので、<br>　「許可する」をクリックするよう誘導してください<br>";

$message['const'] = $const;

// セット
$config['message'] = $message;
