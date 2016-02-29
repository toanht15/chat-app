ALTER TABLE `t_histories` CHANGE COLUMN `m_customers_id` `t_visitors_id` int NOT NULL COMMENT "訪問者ID";

CREATE TABLE `t_visitors` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
    `m_companies_id(int)` int NOT NULL COMMENT "企業ID",
    `browser_id` varchar(20) NOT NULL COMMENT "ブラウザに保持しているID"
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `t_history_chat_logs` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
  `t_histories_id` int NOT NULL COMMENT "履歴ID",
  `t_visitors_id` int NOT NULL COMMENT "訪問者ID",
  `m_users_id` int NOT NULL COMMENT "対応ユーザーID",
  `message` int NOT NULL COMMENT "メッセージ",
  FOREIGN KEY (`t_histories_id`) REFERENCES t_histories(id),
  FOREIGN KEY (`t_visitors_id`) REFERENCES t_visitors(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `t_history_share_displays` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
  `t_histories_id` int NOT NULL COMMENT "履歴ID",
  `m_users_id` int NOT NULL COMMENT "対応ユーザーID",
  FOREIGN KEY (`t_histories_id`) REFERENCES t_histories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


