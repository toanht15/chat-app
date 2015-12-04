CREATE TABLE `m_companies` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
  `company_key` varchar(100) NOT NULL COMMENT "企業コード",
  `company_name` varchar(200) NOT NULL COMMENT "企業名",
  `admin_mail_address` varchar(100) NOT NULL COMMENT "管理者アドレス",
  `admin_password` varchar(100) NOT NULL COMMENT "管理者パスワード",
  `m_contact_types_id` int COMMENT "契約タイプ",
  `del_flg` int DEFAULT 0 COMMENT "削除フラグ",
  `created` datetime DEFAULT NULL COMMENT "登録日",
  `created_user_id` int DEFAULT NULL COMMENT "登録実行ユーザ",
  `modified` datetime DEFAULT NULL COMMENT "更新日",
  `modified_user_id` int DEFAULT NULL COMMENT "更新実行ユーザ",
  `deleted` datetime DEFAULT NULL COMMENT "削除日",
  `deleted_user_id` int DEFAULT NULL COMMENT "削除実行ユーザ"
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `m_users` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
  `m_companies_id` INT NOT NULL COMMENT "企業マスタID",
  `user_name` VARCHAR(100) NOT NULL COMMENT "ユーザー名",
  `display_name` VARCHAR(100) NOT NULL COMMENT "表示名",
  `mail_address` VARCHAR(200) NOT NULL COMMENT "メールアドレス",
  `password` VARCHAR(100) NOT NULL COMMENT "パスワード",
  `permission_level` INT NOT NULL COMMENT "権限レベル",
  `operation_list_columns` VARCHAR(100) DEFAULT NULL COMMENT "リアルタイムモニタ一覧表示項目リスト",
  `history_list_columns` VARCHAR(100) DEFAULT NULL COMMENT "履歴一覧表示項目リスト",
  `del_flg` int DEFAULT 0 COMMENT "削除フラグ",
  `created` datetime DEFAULT NULL COMMENT "登録日",
  `created_user_id` int DEFAULT NULL COMMENT "登録実行ユーザ",
  `modified` datetime DEFAULT NULL COMMENT "更新日",
  `modified_user_id` int DEFAULT NULL COMMENT "更新実行ユーザ",
  `deleted` datetime DEFAULT NULL COMMENT "削除日",
  `deleted_user_id` int DEFAULT NULL COMMENT "削除実行ユーザ",
  index(`m_companies_id`),
  FOREIGN KEY (`m_companies_id`) REFERENCES m_companies(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;