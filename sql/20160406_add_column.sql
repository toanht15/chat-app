-- 契約ID数追加
ALTER TABLE m_companies ADD COLUMN limit_users int DEFAULT 1 NOT NULL COMMENT "契約ID数" AFTER `m_contact_types_id`;

-- 多重ログイン防止用文字列追加
ALTER TABLE m_users ADD COLUMN session_rand_str varchar(20) DEFAULT NULL COMMENT "多重ログイン防止用文字列" AFTER `history_list_columns`;