-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016 年 5 朁E10 日 18:29
-- サーバのバージョン： 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `sinclo_db`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `m_companies`
--

CREATE TABLE IF NOT EXISTS `m_companies` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `company_key` varchar(100) DEFAULT NULL COMMENT '企業キー',
  `company_name` varchar(200) NOT NULL COMMENT '企業名',
  `admin_mail_address` varchar(100) NOT NULL COMMENT '管理者アドレス',
  `admin_password` varchar(100) NOT NULL COMMENT '管理者パスワード',
  `m_contact_types_id` int(11) DEFAULT NULL COMMENT '契約タイプ',
  `limit_users` int(11) NOT NULL DEFAULT '1' COMMENT '契約ID数',
  `del_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `m_users`
--

CREATE TABLE IF NOT EXISTS `m_users` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `m_companies_id` int(11) NOT NULL COMMENT '企業マスタID',
  `user_name` varchar(100) NOT NULL COMMENT 'ユーザー名',
  `display_name` varchar(100) DEFAULT NULL COMMENT '表示名',
  `mail_address` varchar(200) NOT NULL COMMENT 'メールアドレス',
  `password` varchar(100) NOT NULL COMMENT 'パスワード',
  `permission_level` int(11) DEFAULT NULL COMMENT '権限レベル',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `m_widget_settings`
--

CREATE TABLE IF NOT EXISTS `m_widget_settings` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `m_companies_id` int(11) NOT NULL COMMENT '企業マスタID',
  `display_type` int(11) NOT NULL COMMENT '表示種別',
  `title` varchar(50) NOT NULL COMMENT 'タイトル',
  `tel` varchar(30) NOT NULL COMMENT 'お問い合わせ先電話番号',
  `content` varchar(100) NOT NULL COMMENT '内容',
  `display_time_flg` int(11) NOT NULL COMMENT '受付時間の表示フラグ',
  `time_text` varchar(15) DEFAULT NULL COMMENT '受付時間テキスト',
  `del_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
  `created` datetime DEFAULT NULL COMMENT '登録日',
  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
  `modified` datetime DEFAULT NULL COMMENT '更新日',
  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
  `deleted` datetime DEFAULT NULL COMMENT '削除日',
  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `t_histories`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `t_history_chat_logs`
--

CREATE TABLE IF NOT EXISTS `t_history_chat_logs` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `t_histories_id` int(11) NOT NULL COMMENT '履歴ID',
  `visitors_id` varchar(20) NOT NULL COMMENT '訪問者ID',
  `m_users_id` int(11) DEFAULT NULL COMMENT '対応ユーザーID',
  `message` varchar(500) NOT NULL COMMENT 'メッセージ',
  `message_type` int(11) NOT NULL COMMENT 'メッセージ種別（1:訪問者から、2:企業側から）',
  `message_read_flg` int(11) DEFAULT '0' COMMENT '既読フラグ',
  `created` datetime NOT NULL COMMENT '登録日'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `t_history_stay_logs`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `m_companies`
--
ALTER TABLE `m_companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_users`
--
ALTER TABLE `m_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `m_companies_id` (`m_companies_id`),
  ADD KEY `m_companies_id_2` (`m_companies_id`);

--
-- Indexes for table `m_widget_settings`
--
ALTER TABLE `m_widget_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `m_companies_id` (`m_companies_id`);

--
-- Indexes for table `t_histories`
--
ALTER TABLE `t_histories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_history_chat_logs`
--
ALTER TABLE `t_history_chat_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_histories_id` (`t_histories_id`);

--
-- Indexes for table `t_history_stay_logs`
--
ALTER TABLE `t_history_stay_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `m_companies`
--
ALTER TABLE `m_companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT for table `m_users`
--
ALTER TABLE `m_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT for table `m_widget_settings`
--
ALTER TABLE `m_widget_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT for table `t_histories`
--
ALTER TABLE `t_histories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT for table `t_history_chat_logs`
--
ALTER TABLE `t_history_chat_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT for table `t_history_stay_logs`
--
ALTER TABLE `t_history_stay_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `m_users`
--
ALTER TABLE `m_users`
  ADD CONSTRAINT `m_users_ibfk_1` FOREIGN KEY (`m_companies_id`) REFERENCES `m_companies` (`id`);

--
-- テーブルの制約 `m_widget_settings`
--
ALTER TABLE `m_widget_settings`
  ADD CONSTRAINT `m_widget_settings_ibfk_1` FOREIGN KEY (`m_companies_id`) REFERENCES `m_companies` (`id`);

--
-- テーブルの制約 `t_history_chat_logs`
--
ALTER TABLE `t_history_chat_logs`
  ADD CONSTRAINT `t_history_chat_logs_ibfk_1` FOREIGN KEY (`t_histories_id`) REFERENCES `t_histories` (`id`);
