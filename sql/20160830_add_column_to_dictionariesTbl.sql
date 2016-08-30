/* 簡易入力テーブルにソート順を持つ */
ALTER TABLE t_dictionaries ADD COLUMN sort int DEFAULT 999 COMMENT 'ソート順' AFTER `type`;
