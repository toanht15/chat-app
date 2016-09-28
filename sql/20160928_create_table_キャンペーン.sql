		CREATE TABLE IF NOT EXISTS `t_campaigns` (
		  `id` int(11)  NOT NULL auto_increment COMMENT 'ID',
		  `m_companies_id` int(11) NOT NULL COMMENT '企業ID',
		  `name` varchar(100) DEFAULT NULL COMMENT 'キャンペーン名',
		  `parameter` varchar(100) DEFAULT NULL COMMENT 'URLパラメータ',
		  `comment` varchar(300) DEFAULT NULL COMMENT 'コメント',
		  `created` datetime DEFAULT NULL COMMENT '登録日',
		  `created_user_id` int(11) DEFAULT NULL COMMENT '登録実行ユーザ',
		  `modified` datetime DEFAULT NULL COMMENT '更新日',
		  `modified_user_id` int(11) DEFAULT NULL COMMENT '更新実行ユーザ',
		  `deleted` datetime DEFAULT NULL COMMENT '削除日',
		  `deleted_user_id` int(11) DEFAULT NULL COMMENT '削除実行ユーザ',
		   PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;