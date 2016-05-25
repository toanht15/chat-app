/* オートメッセージ機能追加 */
CREATE TABLE `t_auto_messages` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
  `m_companies_id` int NOT NULL COMMENT "企業ID",
  `name` varchar(50) DEFAULT NULL COMMENT "オートメッセージ名称",
  `trigger_type` int NOT NULL COMMENT "トリガーの種類",
  `activity` text DEFAULT NULL COMMENT "オートメッセージ設定内容",
  `action_type` int NOT NULL COMMENT "アクションの種類",
  `active_flg` int NOT NULL COMMENT "0:有効、1:無効",
  `del_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
