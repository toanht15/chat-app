<?php
class AddRecordMJobMailTemplates extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = '_add_record_m_job_mail_templates';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(),
		'down' => array(),
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
	  $targetTable = 'MJobMailTemplate';
      $MJobMailTemplate = ClassRegistry::init($targetTable);
      $data[0][$targetTable]['id'] = 6;
      $data[0][$targetTable]['mail_type_cd'] = "'いきなり本契約申込時 会社向け";
      $data[0][$targetTable]['sender'] = "sinclo（シンクロ）";
      $data[0][$targetTable]['subject'] = "【sinclo】本契約のお申し込みがありました";
      $data[0][$targetTable]['mail_body'] = "関係者各位

sincloに本契約のお申込みがありました

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
     $MJobMailTemplate->create();
      if ($MJobMailTemplate->saveAll($data)) {
        $this->callback->out('m_job_mail_template table inital data is added.');
      }
    }
		return true;
	}
}
