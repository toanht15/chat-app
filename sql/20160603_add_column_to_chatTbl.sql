/* チャットTBLに移動履歴TBLのIDを持たせる */
ALTER TABLE t_history_chat_logs ADD COLUMN t_history_stay_logs_id int DEFAULT NULL COMMENT "移動履歴TBLのID" AFTER t_histories_id