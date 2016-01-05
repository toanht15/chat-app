/* ウィジェット設定マスタ */
CREATE TABLE `m_widget_settings` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "ID",
  `m_companies_id` INT NOT NULL COMMENT "企業マスタID",
  `display_type` INT NOT NULL COMMENT "表示種別",
  `title` VARCHAR(50) NOT NULL COMMENT "タイトル",
  `tel` VARCHAR(30) NOT NULL COMMENT "お問い合わせ先電話番号",
  `content` VARCHAR(100) NOT NULL COMMENT "内容",
  `display_time_flg` INT NOT NULL COMMENT "受付時間の表示フラグ",
  `time_text` VARCHAR(15) DEFAULT NULL COMMENT "受付時間テキスト",
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

/* ウィジェット設定 */
INSERT INTO m_widget_settings (m_companies_id, display_type, title, tel, content, display_time_flg, time_text)
 VALUES (1, 1, "お電話でのお問い合わせ", "00-0000-0000", "お気軽にお問い合わせください。オペレータに下記番号をお伝え頂くと、リモートでのサポートも可能です。", 1, "平日9:00-19:00");
