-- 契約ID数追加
ALTER TABLE m_companies ADD COLUMN limit_users int DEFAULT 1 NOT NULL COMMENT "契約ID数" AFTER m_contact_types_id;