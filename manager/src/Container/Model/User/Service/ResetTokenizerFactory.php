<?php

namespace App\Container\Model\User\Service;

use App\Model\User\Service\ResetTokenizer;

class ResetTokenizerFactory
{
    /**
     * @throws \Exception
     */
    public function create(string $interval): ResetTokenizer
    {
        return new ResetTokenizer(new \DateInterval($interval));
    }
}