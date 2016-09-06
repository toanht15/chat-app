/* アクセスユーザーの情報 */
CREATE TABLE `m_customers` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
  `m_companies_id` int NOT NULL COMMENT "企業ID",
  `visitors_id` varchar(20) DEFAULT NULL COMMENT "ユーザーID",
  `informations` text NOT NULL COMMENT "情報",
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;