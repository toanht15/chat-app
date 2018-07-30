<?php
App::uses('AppModel', 'Model');
/**
 * MUser Model
 *
 * @property MCompanies $MCompanies
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class MUser extends AppModel
{

  public $name = "MUser";

  /**
   * Validation rules
   *
   * @var array
   */
  public $validate = array(
    'user_name' => array(
      'maxLength' => array(
        'rule' => array('maxLength', 50),
        'allowEmpty' => false,
        'message' => 'ユーザー名は５０文字以内で設定してください。'
      ),
      'prohibitedCharacters' => array(
        'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
        'message' => '<,>,&.",\'を含まずに設定してください。'
      )
    ),
    'display_name' => array(
      'maxLength' => array(
        'rule' => array('maxLength', 10),
        'allowEmpty' => false,
        'message' => '表示名は１０文字以内で設定してください。'
      ),
      'prohibitedCharacters' => array(
        'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
        'message' => '<,>,&.",\'を含まずに設定してください。'
      )
    ),
    'mail_address' => array(
      'email' => array(
        'rule' => 'email',
        'message' => 'メールアドレスの形式が不正です。'
      ),
      'isUniqueChk' => array(
        'rule' => 'isUniqueChk',
        'message' => '既に登録されているアドレスです。'
      ),
      'isFreeAddressChk' => array(
        'rule' => 'isFreeAddressChk',
        'message' => 'フリーアドレスのご利用はできません。'
      ),
      'isCareerDomainChk' => array(
        'rule' => 'isCareerDomainChk',
        'message' => '携帯電話のメールアドレスのご利用はできません。'
      ),
    ),
    'new_password' => array(
      'minLength' => array(
        'rule' => array('between', 8, 24),
        'allowEmpty' => false,
        'message' => 'パスワードは８～２４文字の間で設定してください。'
      ),
      'checkLargeAlphabet' => array(
        'rule' => '/[A-Z]/',//半角英大小文字、数字のみ
        'message' => 'パスワードは英大小文字、数字を含んで設定してください。'
      ),
      'checkSmallAlphabet' => array(
        'rule' => '/[a-z]/',//半角英大小文字、数字のみ
        'message' => 'パスワードは英大小文字、数字を含んで設定してください。'
      ),
      'checkNumber' => array(
        'rule' => '/[0-9]/',//半角英大小文字、数字のみ
        'message' => 'パスワードは英大小文字、数字を含んで設定してください。'
      ),
      'checkOverlapMail' => array(
        'rule' => 'notOverlapMail',
        'message' => 'メールアドレスを含めずに設定してください'
      ),
    ),
    'permission_level' => array(
      'notBlank' => array(
        'rule' => 'notBlank',
        'message' => '権限レベルを選択してください。'
      )
    ),
    'sc_num' => array(
      'range' => array(
        'rule' => array('range', -1, 100),
        'allowEmpty' => false,
        'message' => '０～９９以内で設定してください。'
      )
    ),
    'settings' => array(
      'prohibitedCharacters' => array(
        'rule' => '/^(?!.*(<|>|&|\')).*$/',
        'message' => '<,>,&,\'を含まずに設定してください。'
      )
    )
  );

  public $updateValidate = array(
    'user_name' => array(
      'maxLength' => array(
        'rule' => array('maxLength', 50),
        'allowEmpty' => false,
        'message' => 'ユーザー名は５０文字以内で設定してください。'
      ),
      'prohibitedCharacters' => array(
        'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
        'message' => '<,>,&.",\'を含まずに設定してください。'
      )
    ),
    'display_name' => array(
      'maxLength' => array(
        'rule' => array('maxLength', 10),
        'allowEmpty' => false,
        'message' => '表示名は１０文字以内で設定してください。'
      ),
      'prohibitedCharacters' => array(
        'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
        'message' => '<,>,&.",\'を含まずに設定してください。'
      )
    ),
    'mail_address' => array(
      'email' => array(
        'rule' => 'email',
        'message' => 'メールアドレスの形式が不正です。'
      )
    ),
    'new_password' => array(
      'minLength' => array(
        'rule' => array('between', 8, 24),
        'allowEmpty' => false,
        'message' => 'パスワードは８～２４文字の間で設定してください。'
      ),
      'checkLargeAlphabet' => array(
        'rule' => '/[A-Z]/',//半角英大小文字、数字のみ
        'message' => 'パスワードは英大小文字、数字を含んで設定してください。'
      ),
      'checkSmallAlphabet' => array(
        'rule' => '/[a-z]/',//半角英大小文字、数字のみ
        'message' => 'パスワードは英大小文字、数字を含んで設定してください。'
      ),
      'checkNumber' => array(
        'rule' => '/[0-9]/',//半角英大小文字、数字のみ
        'message' => 'パスワードは英大小文字、数字を含んで設定してください。'
      ),
      'checkOverlapMail' => array(
        'rule' => 'notOverlapMail',
        'message' => 'メールアドレスを含めずに設定してください'
      ),
    ),
    'permission_level' => array(
      'notBlank' => array(
        'rule' => 'notBlank',
        'message' => '権限レベルを選択してください。'
      )
    ),
    'current_password' => array(
      'checkCurrentPw' => array(
        'rule' => 'isCurrentPw',
        'allowEmpty' => false,
        'message' => '現在のパスワードが一致しません。'
      )
    ),
    'confirm_password' => array(
      'checkConfirmPw' => array(
        'rule' => 'canMatchConfirmPw',
        'allowEmpty' => false,
        'message' => '新しいパスワードが一致しません。'
      )
    ),
    'settings' => array(
      'prohibitedCharacters' => array(
        'rule' => '/^(?!.*(<|>|&|\')).*$/',
        'message' => '<,>,&,\'を含まずに設定してください。'
      )
    )
  );

  // フリーメールアドレスドメイン
  private $freeMailaddressDomains = array(
    'gmail.com',
    'yahoo.co.jp',
    'outlook.jp',
    'outlook.com',
    'hotmail.co.jp',
    'excite.co.jp',
    'aol.jp',
    'biglobe.ne.jp',
    'zoho.com',
    'yandex.com',
    'mail.ru',
    'inbox.ru',
    'list.ru',
    'bk.ru',
    'dqnwara.com'
  );

  // 携帯電話ドメイン
  private $careerDomains = array(
    'ezweb.ne.jp',
    'ido.ne.jp',
    'biz.ezweb.ne.jp',
    'augps.ezweb.ne.jp',
    'uqmobile.jp',
    'docomo.ne.jp',
    'mopera.net',
    'dwmail.jp',
    'pdx.ne.jp',
    'wcm.ne.jp',
    'willcom.com',
    'y-mobile.ne.jp',
    'emnet.ne.jp',
    'emobile-s.ne.jp',
    'emobile.ne.jp',
    'ymobile1.ne.jp',
    'ymobile.ne.jp',
    'jp-c.ne.jp',
    'jp-d.ne.jp',
    'jp-h.ne.jp',
    'jp-k.ne.jp',
    'jp-n.ne.jp',
    'jp-q.ne.jp',
    'jp-r.ne.jp',
    'jp-s.ne.jp',
    'jp-t.ne.jp',
    'sky.tkc.ne.jp',
    'sky.tkk.ne.jp',
    'sky.tu-ka.ne.jp',
    'disney.ne.jp',
    'i.softbank.jp',
    'softbank.ne.jp',
    'vodafone.ne.jp'
  );

  public function isCurrentPw($currentPw)
  {
    $data = $this->data['MUser'];
    if (empty($currentPw['current_password'])) return false;

    $params = array(
      'fields' => '*',
      'conditions' => array(
        'id' => $data['id'],
        'del_flg' => 0,
        'password' => $this->makePassword($currentPw['current_password'])
      ),
      'limit' => 1,
      'recursive' => -1
    );
    $ret = $this->find('all', $params);
    if (!empty($ret)) {
      return true;
    }
    return false;
  }

  public function notOverlapMail()
  {
    $data = $this->data['MUser'];
    if (!empty($data['new_password'])) {
      if (strpos($data['new_password'], (substr($data['mail_address'], 0, strpos($data['mail_address'], '@')))) === false) {
        return true;
      }
      return false;
    }
  }

  public function canMatchConfirmPw()
  {
    $data = $this->data['MUser'];
    if (!empty($data['new_password']) && !empty($data['confirm_password'])) {
      if (strcmp($data['new_password'], $data['confirm_password']) === 0) {
        return true;
      }
    }
    return false;
  }

  /**
   * belongsTo associations
   *
   * @var array
   */
  public $belongsTo = array(
    'MCompany' => array(
      'className' => 'MCompany',
      'foreignKey' => 'm_companies_id',
      'conditions' => '',
      'fields' => '',
      'order' => ''
    )
  );

  public function beforeSave($options = array())
  {
    if (empty($this->data['MUser'])) return true;
    $data = $this->data['MUser'];
    if (!empty($data['new_password'])) {
      $data['password'] = $this->makePassword($data['new_password']);
    }
    $this->data['MUser'] = $data;
    return true;
  }

  public function passwordHash($pass)
  {
    if (!empty($pass)) {
      $data = $this->makePassword($pass);
    }
    $password = $data;
    return $password;
  }

  public function makePassword($str)
  {
    $passwordHasher = new SimplePasswordHasher();
    return $passwordHasher->hash($str);
  }


  public function isUniqueChk($str)
  {
    $str[$this->name . '.del_flg'] = 0;
    if (!empty($this->id)) {
      $str[$this->name . '.id !='] = $this->id;
    }
    $ret = $this->find('all', array('fields' => $this->name . '.*', 'conditions' => $str, 'recursive' => -1));
    if (!empty($ret)) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * getUser ユーザーの情報を単一か複数取得する
   * @param $id int(default:null) nullの場合は所属ユーザーを全て取得
   * @return array 単一ユーザーの場合は 'first', 所属ユーザーの場合は 'all' の結果
   * */
  public function getUser($id = null)
  {
    $conditions = array(
      'fields' => array(
        'id',
        'display_name',
        'settings'
      ),
      'conditions' => array(
        'm_companies_id' => Configure::read('logged_company_id'),
        'permission_level != ' => C_AUTHORITY_SUPER,
        'del_flg' => 0
      ),
      'recursive' => -1
    );

    if (!empty($id)) {
      $conditions['conditions']['id'] = $id;
      return $this->find('first', $conditions);
    } else {
      return $this->find('all', $conditions);
    }

  }

  public function isFreeAddressChk($field = array())
  {
    foreach ($this->freeMailaddressDomains as $k => $v) {
      if (preg_match('/(@' . $v . '$)/', $field['mail_address'])) {
        return false;
      }
    }
    return true;
  }

  public function isCareerDomainChk($field = array())
  {
    forEach ($this->careerDomains as $index => $domain) {
      if (preg_match('/(@' . $domain . '$)/', $field['mail_address'])) {
        return false;
      }
    }
    return true;
  }

  public function incrementErrorCount($target)
  {
    if ($target['error_count'] + 1 >= 10) {
      $target['locked_datetime'] = date('Y-m-d H:i:s', time());
    } else {
      $target['locked_datetime'] = null;
    }
    $data = array('id' => $target['id'], 'error_count' => $target['error_count'] + 1, 'locked_datetime' => $target['locked_datetime']);
    $fields = array('error_count', 'locked_datetime');
    $this->save($data, false, $fields);
  }

  public function resetErrorCount($target)
  {
    $target['locked_datetime'] = null;
    $target['error_count'] = 0;
    $data = array('id' => $target['id'], 'error_count' => $target['error_count'], 'locked_datetime' => $target['locked_datetime']);
    $fields = array('error_count', 'locked_datetime');
    $this->save($data, false, $fields);
  }
}
