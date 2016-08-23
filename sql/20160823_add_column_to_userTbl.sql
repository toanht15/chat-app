/* その他個別設定 */
ALTER TABLE m_users ADD COLUMN settings text DEFAULT "" COMMENT "その他個別設定" AFTER permission_level;