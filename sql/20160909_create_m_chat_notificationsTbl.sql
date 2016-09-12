CREATE TABLE `m_chat_notifications` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
  `m_companies_id` int NOT NULL COMMENT "企業ID",
  `name` varchar(100) NOT NULL COMMENT "通知名",
  `type` int NOT NULL COMMENT "対象",
  `keyword` varchar(100) DEFAULT NULL COMMENT "ｷｰﾜｰﾄﾞ",
  `image` varchar(100) NOT NULL COMMENT "アイコン画像",
  `del_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ',
  FOREIGN KEY (`m_companies_id`) REFERENCES m_companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
