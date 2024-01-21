<?php

namespace App\ReadModel\User;

class ShortView
{
    public string $id;
    public string $email;
    public string $role;
    public string $status;

    public function __construct(array $result)
    {
        $this->id = $result['id'];
        $this->email = $result['email'];
        $this->role = $result['role'];
        $this->status = $result['status'];
    }

}