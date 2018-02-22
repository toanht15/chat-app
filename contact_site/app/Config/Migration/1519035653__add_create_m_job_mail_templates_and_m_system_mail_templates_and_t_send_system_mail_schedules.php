<?php
class AddCreateMJobMailTemplatesAndMSystemMailTemplatesAndTSendSystemMailSchedules extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = '_add_create_m_job_mail_templates_and_m_system_mail_templates_and_t_send_system_mail_schedules';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_agreements' => array(
					'business_model' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'after' => 'm_companies_id'),
				),
			),
			'create_table' => array(
				'm_job_mail_templates' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'mail_type_cd' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名称', 'charset' => 'utf8'),
					'days_after' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '日にち'),
					'time' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '時間'),
					'subject' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メールタイトル', 'charset' => 'utf8'),
					'mail_body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メール本文', 'charset' => 'utf8'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'm_system_mail_templates' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'mail_type_cd' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'タイプ', 'charset' => 'utf8'),
					'subject' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '件名', 'charset' => 'utf8'),
					'mail_body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '本文', 'charset' => 'utf8'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				't_send_system_mail_schedules' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'sending_datetime' => array('type' => 'datetime', 'null' => false, 'default' => null, 'comment' => '日付、日時'),
					'subject' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メールタイトル', 'charset' => 'utf8'),
					'mail_body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メール本文', 'charset' => 'utf8'),
					'mail_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => 'メールアドレス', 'charset' => 'utf8'),
					'send-mail_flg' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メール送信フラグ'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_agreements' => array('business_model', 'trial_start_day', 'trial_end_day')
			),
			'drop_table' => array(
				'm_job_mail_templates', 'm_system_mail_templates', 't_send_system_mail_schedules'
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
      $data[0][$targetTable]['mail_type_cd'] = "無料トライアル申し込み お客様向け";
      $data[0][$targetTable]['subject'] = "【sinclo（シンクロ）】無料トライアル登録完了のお知らせ";
      $data[0][$targetTable]['mail_body'] = "##COMPANY_NAME##
 ##USER_NAME##様
 
 この度はsinclo（シンクロ）の無料トライアルにお申込み頂き
 誠にありがとうございます。
 
 無料トライアルの登録が完了いたしましたので、本日より
 2週間無料でお試しいただけます。
 
 ■ID
 ##MAIL_ADDRESS##
 
 ■パスワード
 ##PASSWORD##
 
 ■ログインURL
 https://sinclo.jp/Login
 
 
 上記のアカウント情報をご確認いただきログインをお願いいたします。
 操作マニュアルは、ログイン後「ヘルプ」よりご確認頂くことが
 できますので是非ご利用ください。
 
 ★今だけ無料サポート実施中★
 今ならトライアル期間中に操作方法や設定に関するサポートをお電話にて
 １５分程度無料で実施させて頂いております。
 ご希望のお客様は下記必要事項を明記の上、お気軽にご連絡ください。
 
 【連絡先】cloud-service@medialink-ml.co.jp
 【件　名】sinclo無料電話サポート希望
 【メール本文】
 　＜会社名＞
 　＜お名前＞
 　＜電話番号＞
 
 テストアカウントは3IDまでご利用いただけますので、
 残り2IDはログイン後自由にご登録可能です。
 
 
 本メールは、システムによる自動配信メールとなっております。
 お心当たりのない場合、その他ご不明な点がございましたら、
 お手数ですが下記よりご連絡いただけますようお願い申し上げます。
 ────────────────────────────
 メディアリンク株式会社
 【WEB接客ツール：sinclo】
 URL:  https://sinclo.medialink-ml.co.jp/lp/
 Mail: cloud-service@medialink-ml.co.jp
 ────────────────────────────";

      $data[1][$targetTable]['mail_type_cd'] = "無料トライアル申し込み 会社向け";
      $data[1][$targetTable]['subject'] = "【sinclo】無料トライアルのお申し込みがありました";
      $data[1][$targetTable]['mail_body'] = "関係者各位
 
 sincloに無料トライアルのお申込みがありました
 
 ──以下申し込み内容です──
 
 ■会社名
 ##COMPANY_NAME##
 
 ■ビジネスモデル
 ##BUSINESS_MODEL##
 
 ■お名前
 ##USER_NAME##
 
 ■部署名
 ##DEPARTMENT##
 
 ■役職
 ##POSITION##
 
 ■会社用メールアドレス
 ##MAIL_ADDRESS##
 
 ■電話番号
 ##PHONE_NUMBER##
 
 ■導入を検討しているサイトURL
 ##URL##
 
 ■その他ご要望など
 ##OTHER##
 ";
      $data[1][$targetTable]['mail_type_cd'] = "初期パスワード変更 会社向け";
      $data[1][$targetTable]['subject'] = "【sinclo】初期パスワードの変更がありました";
      $data[1][$targetTable]['mail_body'] = "関係者各位
 
 sincloの初期パスワードが変更されました
 
 ──以下お客様内容です──
 
 ■会社名
 ##COMPANY_NAME##
 
 ■ビジネスモデル
 ##BUSINESS_MODEL##
 
 ■お名前
 ##USER_NAME##
 
 ■部署名
 ##DEPARTMENT##
 
 ■役職
 ##POSITION##
 
 ■会社用メールアドレス
 ##MAIL_ADDRESS##
 
 ■電話番号
 ##PHONE_NUMBER##
 
 ■導入を検討しているサイトURL
 ##URL##
 
 ■その他ご要望など
 ##OTHER##
 ";
      $MSystemMailTemplate->create();
      if ($MSystemMailTemplate->saveAll($data)) {
        $this->callback->out('m_system_mail_templates table inital data is added.');
      }
    }
		return true;
	}
}
