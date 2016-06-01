/* 使用機能を保存する */
ALTER TABLE m_companies ADD COLUMN core_settings VARCHAR(200) DEFAULT NULL COMMENT '使用機能内容' AFTER `limit_users`;
/*  */
ALTER TABLE m_widget_settings DROP COLUMN tel;
ALTER TABLE m_widget_settings DROP COLUMN content;
ALTER TABLE m_widget_settings DROP COLUMN display_time_flg;
ALTER TABLE m_widget_settings DROP COLUMN time_text;