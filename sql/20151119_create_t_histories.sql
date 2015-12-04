DROP TABLE `t_histories`;
CREATE TABLE `t_histories` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
  `m_companies_id` INT NOT NULL COMMENT "企業マスタID",
  `m_customers_id` INT DEFAULT NULL COMMENT "訪問者ID",
  `tmp_customers_id` varchar(50) DEFAULT NULL COMMENT "訪問者一時ID",
  `ip_address` varchar(15) DEFAULT NULL COMMENT "IPアドレス",
  `tab_id` varchar(50) NOT NULL COMMENT "タブID",
  `user_agent` varchar(100) NOT NULL COMMENT "ユーザーエージェント",
  `access_date` datetime DEFAULT NULL COMMENT "アクセス開始日時",
  `out_date` datetime DEFAULT NULL COMMENT "アクセス終了日時",
  `referrer_url` varchar(300) COMMENT "リファラー情報",
  `del_flg` int DEFAULT 0 COMMENT "削除フラグ",
  `created` datetime DEFAULT NULL COMMENT "登録日",
  `created_user_id` int DEFAULT NULL COMMENT "登録実行ユーザ",
  `modified` datetime DEFAULT NULL COMMENT "更新日",
  `modified_user_id` int DEFAULT NULL COMMENT "更新実行ユーザ",
  `deleted` datetime DEFAULT NULL COMMENT "削除日",
  `deleted_user_id` int DEFAULT NULL COMMENT "削除実行ユーザ"
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
