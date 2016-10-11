# 次世代型コミュニケーションツール『sinclo』
---

## データーベース設定方法

### Nodeサーバー

`sinclo/socket/routes/`直下に、`database.js`ファイルを作成します。
以下を例に、`database.js`にホスト名・ユーザー名・パスワード・ＤＢ名を記入します。

```
/* DataBase Settings */
process.env.DB_HOST = 'localhost';
process.env.DB_USER = 'root';
process.env.DB_PASS = 'password',
process.env.DB_NAME = 'sinclo_db'
```

### 企業管理画面

`sinclo/contact_site/app/Config/`直下に、`database.php.default`のファイルを複製し `database.php`ファイルを作成します。
以下を例に、ホスト名・ユーザー名・パスワード・ＤＢ名を記入します。

```
/* DataBase Settings */
	public $default = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'root',
		'password' => 'password',
		'database' => 'sinclo_db',
		'prefix' => '',
		'encoding' => 'utf8',
	);
```


