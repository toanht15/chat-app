		CREATE TABLE IF NOT EXISTS `t_campaigns` (
		  `id` int(11)  NOT NULL auto_increment COMMENT 'ID',
		  `m_companies_id` int(11) NOT NULL COMMENT '���ID',
		  `name` varchar(100) DEFAULT NULL COMMENT '�L�����y�[����',
		  `parameter` varchar(100) DEFAULT NULL COMMENT 'URL�p�����[�^',
		  `comment` varchar(300) DEFAULT NULL COMMENT '�R�����g',
		  `created` datetime DEFAULT NULL COMMENT '�o�^��',
		  `created_user_id` int(11) DEFAULT NULL COMMENT '�o�^���s���[�U',
		  `modified` datetime DEFAULT NULL COMMENT '�X�V��',
		  `modified_user_id` int(11) DEFAULT NULL COMMENT '�X�V���s���[�U',
		  `deleted` datetime DEFAULT NULL COMMENT '�폜��',
		  `deleted_user_id` int(11) DEFAULT NULL COMMENT '�폜���s���[�U',
		   PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;