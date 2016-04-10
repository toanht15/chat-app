ALTER TABLE `t_histories` DROP COLUMN `m_customers_id`;
ALTER TABLE `t_histories` CHANGE COLUMN `tmp_customers_id` `visitors_id` varchar(20) NOT NULL COMMENT "訪問者ID";