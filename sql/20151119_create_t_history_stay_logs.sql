CREATE TABLE `t_history_stay_logs` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
  `t_histories_id` INT NOT NULL COMMENT "履歴ID",
  `title` varchar(100) NOT NULL COMMENT "ページタイトル",
  `url` varchar(300) NOT NULL COMMENT "URL",
  `stay_time` time NOT NULL COMMENT "滞在時間",
  `del_flg` int DEFAULT 0 COMMENT "削除フラグ",
  `created` datetime DEFAULT NULL COMMENT "登録日",
  `created_user_id` int DEFAULT NULL COMMENT "登録実行ユーザ",
  `modified` datetime DEFAULT NULL COMMENT "更新日",
  `modified_user_id` int DEFAULT NULL COMMENT "更新実行ユーザ",
  `deleted` datetime DEFAULT NULL COMMENT "削除日",
  `deleted_user_id` int DEFAULT NULL COMMENT "削除実行ユーザ"
) ENGINE=InnoDB DEFAULT CHARSET=utf8;