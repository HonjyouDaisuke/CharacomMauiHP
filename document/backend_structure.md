# CharacomMaui backend 　アーキテクチャ

## 各層の役割

| 層             | 内容                                                                   | 呼ばれ方                    |
| -------------- | ---------------------------------------------------------------------- | --------------------------- |
| domain         | Entity・Value Object。ビジネスルールだけ。                             | usecases から               |
| usecases       | アプリケーションの処理単位。例：ユーザ作成・ログイン                   | interfaces/controllers から |
| interfaces     | API や CLI、フロントからの呼び口。リポジトリのインターフェースを受ける | public/api/\*.php から      |
| infrastructure | DB や外部 API の具体実装                                               | usecases が依存する         |

## フォルダ構成例

```
backend/
├─ domain/                   ← ドメイン層（ビジネスルール）
│   ├─ entities/
│   │   └─ User.php
│   └─ value_objects/
│
├─ usecases/                 ← ユースケース層
│   ├─ CreateUser.php
│   ├─ LoginUser.php
│   └─ FetchUserList.php
│
├─ interfaces/               ← APIやフロントからの入り口
│   └─ controllers/          ← public/api/*.php から呼ばれる
│       └─ LoginController.php
│
├─ infrastructure/           ← DBや外部サービスの実装
│   ├─ PDOUserRepository.php
│   └─ DB/
│       └─ Connection.php
│
├─ config.php                ← 共通設定（定数など）
├─ functions.php             ← 共通関数（軽量な補助関数）
└─ sql/                      ← 非公開 SQL ファイル（必要に応じて）
    └─ fetch_user_list.sql
```

### API から Backend のユースケースを呼ぶ

```
public/api/login.php
    ↓ require interfaces/controllers/LoginController.php
LoginController
    ↓ usecases/LoginUser.php
LoginUser (ユースケース)
    ↓ infrastructure/PDOUserRepository.php
PDOUserRepository
    ↓ DB/Connection.php
Connection (PDO接続)

```
