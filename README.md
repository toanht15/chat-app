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
process.env.DB_PASS = 'password';
process.env.DB_NAME = 'sinclo_db';
process.env.WS_PORT = '9090';
```

### メンテナンス画面

`sinclo/admin_site/app/Config/`直下に、`database.php.default`のファイルを複製し `database.php`ファイルを作成します。
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

`sinclo/contact_site/app/Config`直下に、`myConst.php`というファイルを用意し、以下の内容を記入します。

```
<?php

define('C_AWS_S3_KEY', 'AKXXXXXXXXXXXXXXXXXX'); // AWSのS3専用キー
define('C_AWS_S3_SECURITY', 'XXXXXX+XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'); // AWSのS3専用シークレットキー
define('C_AWS_S3_BUCKET', 'XXXXXXXXXXXXXXXXXXX'); // AWSのBucket名

```

## マイグレーションの設定

### マイグレーションのサブモジュールをダウンロード

`sinclo/contact_site/`直下に移動し、下記コマンドでGitのサブモジュールをダウンロードする

```
$ git submodule add git://github.com/CakeDC/migrations.git app/Plugin/Migrations
```

### マイグレーション管理用のテーブルを作成

```
$ Console/cake Migrations.migration run all -p
```

### マイグレーションコマンド

`sinclo`直下に、`migration`エイリアスを用意しています。

```
$ ./migration status
$ ./migration run up
$ ./migration run down
```

## その他

### 企業側管理画面について

こちらでは「fontawesome」を利用させて頂いております。
アイコンを追加で試用したい場合は[こちら](http://fontawesome.io/)より確認、利用してください。
ライセンスは *MITライセンス* になっております。
