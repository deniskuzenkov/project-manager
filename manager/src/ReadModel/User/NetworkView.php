<?php

namespace App\ReadModel\User;

class NetworkView
{
    public string $network;
    public string $identity;

    public function __construct(array $result)
    {
        $this->network = $result['network'];
        $this->identity = $result['identity'];
    }
}