SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `m_chat_notifications` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `m_companies_id` int(11) NOT NULL COMMENT '企業ID',
  `name` varchar(100) NOT NULL COMMENT '通知名',
  `type` int(11) NOT NULL COMMENT '対象',
  `keyword` varchar(100) DEFAULT NULL COMMENT 'ｷｰﾜｰﾄﾞ',
  `image` varchar(100) NOT NULL COMMENT 'アイコン画像',
  `del_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `m_companies` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `company_key` varchar(100) DEFAULT NULL COMMENT '企業キー',
  `company_name` varchar(200) NOT NULL COMMENT '企業名',
  `admin_mail_address` varchar(100) NOT NULL COMMENT '管理者アドレス',
  `admin_password` varchar(100) NOT NULL COMMENT '管理者パスワード',
  `m_contact_types_id` int(11) DEFAULT NULL COMMENT '契約タイプ',
  `limit_users` int(11) NOT NULL DEFAULT '1' COMMENT '契約ID数',
  `core_settings` varchar(200) DEFAULT NULL COMMENT '仕様機能内容',
  `del_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `m_companies` VALUES(1, 'medialink', 'メディアリンク株式会社', 'rena.hidaka@medialink-ml.co.jp', '932bf293fa6bb87600ccbe46636526d53f59a8b5', 1, 25, '{"chat": true, "synclo": true,"videochat": true}', 0, '2015-10-23 18:55:13', NULL, '2015-10-23 18:55:13', NULL, NULL, NULL);

CREATE TABLE IF NOT EXISTS `m_customers` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `m_companies_id` int(11) NOT NULL COMMENT '企業ID',
  `visitors_id` varchar(20) DEFAULT NULL COMMENT 'ユーザーID',
  `informations` text NOT NULL COMMENT '情報',
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `m_users` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `m_companies_id` int(11) NOT NULL COMMENT '企業マスタID',
  `user_name` varchar(100) NOT NULL COMMENT 'ユーザー名',
  `display_name` varchar(100) DEFAULT NULL COMMENT '表示名',
  `mail_address` varchar(200) NOT NULL COMMENT 'メールアドレス',
  `password` varchar(100) NOT NULL COMMENT 'パスワード',
  `permission_level` int(11) DEFAULT NULL COMMENT '権限レベル',
  `settings` text COMMENT 'その他個別設定',
  `operation_list_columns` varchar(100) DEFAULT NULL COMMENT 'リアルタイムモニタ一覧表示項目リスト',
  `history_list_columns` varchar(100) DEFAULT NULL COMMENT '履歴一覧表示項目リスト',
  `session_rand_str` varchar(20) DEFAULT NULL COMMENT '多重ログイン防止用文字列',
  `del_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `m_users` VALUES(1, 1, 'ほげ', 'ほげ', 'hoge@gmail.com', '6f364de0b69b7279a296c5b7075335ea00452009', 1, '{"sendPattarn":"false"}', NULL, NULL, '196052499', 0, '2015-10-23 19:02:54', NULL, '2016-01-26 13:01:52', NULL, NULL, NULL);

CREATE TABLE IF NOT EXISTS `m_widget_settings` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `m_companies_id` int(11) NOT NULL COMMENT '企業マスタID',
  `display_type` int(11) NOT NULL COMMENT '表示種別',
  `style_settings` text COMMENT 'スタイル設定',
  `del_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `m_widget_settings` VALUES(1, 1, 1, '{"showTime":"4","maxShowTime":"2","showPosition":"2","title":"\\u3069\\u3061\\u3089\\u3082","showSubtitle":"1","subTitle":"\\u30e1\\u30c7\\u30a3\\u30a2\\u30ea\\u30f3\\u30af\\u682a\\u5f0f\\u4f1a\\u793e","showDescription":"2","mainColor":"#70B8A0","stringColor":"#FFFFFF","mainImage":"\\/\\/socket.localhost:8080\\/img\\/widget\\/op01.jpg","showMainImage":"1","radiusRatio":"10","tel":"030-3455-7700","displayTimeFlg":"2","content":"\\u3054\\u8a2a\\u554f\\u6709\\u96e3\\u3046\\u3054\\u3056\\u3044\\u307e\\u3059\\u3002\\r\\n\\r\\n\\u96fb\\u8a71\\u3067\\u306e\\u30b5\\u30dd\\u30fc\\u30c8\\u3082\\u53d7\\u3051\\u4ed8\\u3051\\u3066\\u304a\\u308a\\u307e\\u3059\\u3002","chatTrigger":"2","showName":"1"}', 0, NULL, NULL, '2016-09-20 14:30:36', 1, NULL, NULL);

CREATE TABLE IF NOT EXISTS `t_auto_messages` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `m_companies_id` int(11) NOT NULL COMMENT '企業ID',
  `name` varchar(50) DEFAULT NULL COMMENT 'オートメッセージ名称',
  `trigger_type` int(11) NOT NULL COMMENT 'トリガーの種類',
  `activity` text COMMENT 'オートメッセージ設定内容',
  `action_type` int(11) NOT NULL COMMENT 'アクションの種類',
  `active_flg` int(11) NOT NULL DEFAULT '0' COMMENT '0:有効、1:無効',
  `del_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `t_dictionaries` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `m_companies_id` int(11) NOT NULL COMMENT '企業ID',
  `m_users_id` int(11) NOT NULL COMMENT 'ユーザーID',
  `word` text COMMENT '文章',
  `type` int(11) DEFAULT NULL COMMENT 'タイプ',
  `sort` int(11) DEFAULT '999' COMMENT 'ソート順',
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `t_histories` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `m_companies_id` int(11) NOT NULL COMMENT '企業マスタID',
  `visitors_id` varchar(20) NOT NULL COMMENT '訪問者ID',
  `ip_address` varchar(15) DEFAULT NULL COMMENT 'IPアドレス',
  `tab_id` varchar(50) NOT NULL COMMENT 'タブID',
  `user_agent` varchar(300) DEFAULT NULL COMMENT 'ユーザーエージェント',
  `access_date` datetime DEFAULT NULL COMMENT 'アクセス開始日時',
  `out_date` datetime DEFAULT NULL COMMENT 'アクセス終了日時',
  `referrer_url` varchar(300) DEFAULT NULL COMMENT 'リファラー情報',
  `del_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `t_history_chat_logs` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `t_histories_id` int(11) NOT NULL COMMENT '履歴ID',
  `t_history_stay_logs_id` int(11) DEFAULT NULL COMMENT '移動履歴TBLのID',
  `visitors_id` varchar(20) NOT NULL COMMENT '訪問者ID',
  `m_users_id` int(11) DEFAULT NULL COMMENT '対応ユーザーID',
  `message` varchar(500) NOT NULL COMMENT 'メッセージ',
  `message_type` int(11) NOT NULL COMMENT 'メッセージ種別（1:訪問者から、2:企業側から）',
  `message_read_flg` int(11) DEFAULT '0' COMMENT '既読フラグ',
  `created` datetime(2) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `t_history_share_displays` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `t_histories_id` int(11) NOT NULL COMMENT '履歴ID',
  `m_users_id` int(11) NOT NULL COMMENT '対応ユーザーID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `t_history_stay_logs` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `t_histories_id` int(11) NOT NULL COMMENT '履歴ID',
  `title` varchar(100) NOT NULL COMMENT 'ページタイトル',
  `url` varchar(300) NOT NULL COMMENT 'URL',
  `stay_time` time NOT NULL COMMENT '滞在時間',
  `del_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `m_chat_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `m_companies_id` (`m_companies_id`);

ALTER TABLE `m_companies`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `m_customers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `m_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `m_companies_id` (`m_companies_id`),
  ADD KEY `m_companies_id_2` (`m_companies_id`);

ALTER TABLE `m_widget_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `m_companies_id` (`m_companies_id`);

ALTER TABLE `t_auto_messages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `t_dictionaries`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `t_histories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `t_history_chat_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_histories_id` (`t_histories_id`);

ALTER TABLE `t_history_share_displays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_histories_id` (`t_histories_id`);

ALTER TABLE `t_history_stay_logs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `m_chat_notifications`
  ADD CONSTRAINT `m_chat_notifications_ibfk_1` FOREIGN KEY (`m_companies_id`) REFERENCES `m_companies` (`id`);

ALTER TABLE `m_users`
  ADD CONSTRAINT `m_users_ibfk_1` FOREIGN KEY (`m_companies_id`) REFERENCES `m_companies` (`id`);

ALTER TABLE `m_widget_settings`
  ADD CONSTRAINT `m_widget_settings_ibfk_1` FOREIGN KEY (`m_companies_id`) REFERENCES `m_companies` (`id`);

ALTER TABLE `t_history_chat_logs`
  ADD CONSTRAINT `t_history_chat_logs_ibfk_1` FOREIGN KEY (`t_histories_id`) REFERENCES `t_histories` (`id`);

ALTER TABLE `t_history_share_displays`
  ADD CONSTRAINT `t_history_share_displays_ibfk_1` FOREIGN KEY (`t_histories_id`) REFERENCES `t_histories` (`id`);
COMMIT;
