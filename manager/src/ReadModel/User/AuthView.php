<?php

namespace App\ReadModel\User;

class AuthView
{
    public string $id;
    public string $email;
    public string $password_hash;
    public string $role;
    public string $status;

    public function __construct(array $result)
    {
        $this->id = $result['id'];
        $this->email = $result['email'];
        $this->password_hash = $result['password'];
        $this->role = $result['role'];
        $this->status = $result['status'];
    }

}