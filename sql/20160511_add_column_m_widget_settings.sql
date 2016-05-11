/* スタイル設定 */
ALTER TABLE m_widget_settings ADD COLUMN style_settings text DEFAULT NULL COMMENT 'スタイル設定' AFTER time_text;
