CREATE TABLE `t_dictionaries` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
  `m_companies_id` int NOT NULL COMMENT "企業ID",
  `m_users_id` int DEFAULT NULL COMMENT "ユーザーID",
  `word` text NOT NULL COMMENT "文章",
  `type` int NOT NULL COMMENT "タイプ",
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;