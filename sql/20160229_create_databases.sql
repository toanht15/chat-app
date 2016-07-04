ALTER TABLE `t_histories` DROP COLUMN `m_customers_id`;
ALTER TABLE `t_histories` CHANGE COLUMN `tmp_customers_id` `visitors_id` varchar(20) NOT NULL COMMENT "訪問者ID";

CREATE TABLE `t_history_chat_logs` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
  `t_histories_id` int NOT NULL COMMENT "履歴ID",
  `visitors_id` varchar(20) NOT NULL COMMENT "訪問者ID",
  `m_users_id` int DEFAULT NULL COMMENT "対応ユーザーID",
  `message` varchar(500) NOT NULL COMMENT "メッセージ",
  `message_type` int NOT NULL COMMENT "メッセージ種別（1:訪問者から、2:企業側から）",
  `message_read_flg` int DEFAULT 0 COMMENT "既読フラグ",
  `created` datetime(2) NOT NULL COMMENT "登録日",
  FOREIGN KEY (`t_histories_id`) REFERENCES t_histories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `t_history_share_displays` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
  `t_histories_id` int NOT NULL COMMENT "履歴ID",
  `m_users_id` int NOT NULL COMMENT "対応ユーザーID",
  FOREIGN KEY (`t_histories_id`) REFERENCES t_histories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


