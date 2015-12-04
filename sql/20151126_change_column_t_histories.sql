/* ユーザーエージェントのカラム文字数変更 */
ALTER TABLE t_histories CHANGE COLUMN user_agent user_agent varchar(300) DEFAULT NULL COMMENT 'ユーザーエージェント';