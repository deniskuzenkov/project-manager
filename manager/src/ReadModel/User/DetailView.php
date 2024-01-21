<?php

namespace App\ReadModel\User;

class DetailView
{
    public string $id;
    public string $date;
    public string $email;
    public string $role;
    public string $status;

    /** @var NetworkVIew[] */
    public array $networks = [];

    public function __construct(array $result)
    {
        $this->id = $result['id'];
        $this->date = $result['date'];
        $this->email = $result['email'];
        $this->role = $result['role'];
        $this->status = $result['status'];
    }

    public function setNetworks(array $results): void
    {
        foreach ($results as $result) {
            $this->networks [] = new NetworkView($result);
        }
    }

}