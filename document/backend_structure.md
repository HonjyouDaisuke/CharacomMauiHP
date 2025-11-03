# CharacomMaui backend 　アーキテクチャ

## 各層の役割

| 層             | 内容                       | 依存元                     |
| -------------- | -------------------------- | -------------------------- |
| domain         | ルール・概念・エンティティ | どこにも依存しない         |
| application    | ユースケース（操作の流れ） | domain, infrastructure     |
| infrastructure | DB・外部 API・現実世界     | application から利用される |

## フォルダ構成例

```
backend/
   ├── domain/
   │   └── User.php
   │
   ├── application/
   │   └── CreateUserService.php
   │
   ├── infrastructure/
   │   ├── Database.php
   │   └── UserRepository.php
   │
   ├─ sql/                      ← 非公開 SQL ファイル（必要に応じて）
   │    └─ fetch_user_list.sql   └── config/
   │
   └─ config/
        └── env.php   ← DBパスワードなど
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
