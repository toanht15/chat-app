<?php
class CreateTResetPasswordInformation extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_t_reset_password_information';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				't_reset_password_informations' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'mail_address' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'parameter' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'authentication_code' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 6, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'delete_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '0:無効、1:有効'),
					'expire' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB', 'comment' => 'PWDリマインダー設定の管理テーブル'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				't_reset_password_informations'
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
	if(strcmp($direction, 'up') === 0) {
	  $targetTable = 'MSystemMailTemplate';
      $MSystemMailTemplate = ClassRegistry::init($targetTable);
      $data[0][$targetTable]['id'] = 8;
      $data[0][$targetTable]['mail_type_cd'] = "'パスワード変更 お客様向け";
      $data[0][$targetTable]['sender'] = "sinclo（シンクロ）";
      $data[0][$targetTable]['subject'] = "【sinclo】パスワード再設定のお知らせ";
      $data[0][$targetTable]['mail_body'] = " ##COMPANY_NAME##
 ##USER_NAME## 様

 sincloをご利用いただき誠にありがとうございます。

 パスワード再設定のリクエストを承りました。24時間以内に、
 以下のURLから再設定をお願い致します。

 ##URL##

 本メールは、システムによる自動配信メールとなっております。
 お心当たりのない場合、その他ご不明な点がございましたら、
 お手数ですが下記よりご連絡いただけますようお願い申し上げます。
 ────────────────────────────
  メディアリンク株式会社
  【WEB接客ツール：sinclo】
  URL:  https://sinclo.medialink-ml.co.jp/lp/
  Mail: cloud-service@medialink-ml.co.jp
  ────────────────────────────

";
     $MSystemMailTemplate->create();
      if ($MSystemMailTemplate->saveAll($data)) {
        $this->callback->out('m_job_mail_template table inital data is added.');
      }
    }
		return true;
	}
}
