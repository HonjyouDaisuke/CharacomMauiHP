<?php
// backend/domain/entities/User.php

namespace Domain\Entities;

class User
{
    public string $id;
    public string $email;
    public string $password;
    public ?string $name;     // 任意で追加
    public ?string $role;     // 管理者・一般ユーザなど

    // コンストラクタ（任意の初期値をセット可能）
    public function __construct(
        string $id = '',
        string $email = '',
        string $password = '',
        ?string $name = null,
        ?string $role = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->role = $role;
    }

    // パスワード検証メソッド
    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }

    // パスワードをハッシュ化してセット
    public function setPassword(string $plainPassword): void
    {
        $this->password = password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    public function getId(): string
    {
      return $this->id;
    }

    public function getName(): string
    {
      return $this->name;
    }
}
