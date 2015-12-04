/* 会社１ */
INSERT INTO m_companies ( company_key, company_name, m_contact_types_id, created, modified) VALUES ( "medialink", "メディアリンク株式会社", 1, now(), now());
/* 社員１ */
INSERT INTO m_users ( m_companies_id, user_name, display_name, mail_address, password, permission_level, created, modified) VALUES ( 1, "日高 玲菜", "日高", "aaaaaa@gmail.com", "6f364de0b69b7279a296c5b7075335ea00452009", 1, now(), now());
/* 会社２ */
INSERT INTO m_companies ( company_key, company_name, m_contact_types_id, created, modified) VALUES ( "demo", "デモサイト", 1, now(), now());
/* 社員２ */
INSERT INTO m_users ( m_companies_id, user_name, display_name, mail_address, password, permission_level, created, modified) VALUES ( 2, "山田 太郎", "山田", "bbbbbb@gmail.com", "6f364de0b69b7279a296c5b7075335ea00452009", 1, now(), now());