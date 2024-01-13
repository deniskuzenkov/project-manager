<?php

namespace App\ReadModel\User;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class UserFetcher
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function existsByResetToken(string $token): bool
    {
        return (bool)$this->connection->executeQuery("
                select('*')
                from('user_users')
                where('reset_token_token = ?')",
                [1],
                [$token])
            ->rowCount();
    }

    /**
     * @throws Exception
     */
    public function findForAuth(string $email): ?AuthView
    {
        $stmt = $this->connection->executeQuery("
            select u.id as id,
            u.email as email,
            u.password_hash as password,
            u.role as role,
            u.status as status
            from user_users u
            where u.email = :email", ['email' => $email]);
        $result = $stmt->fetchAssociative();
        return  $result ? new AuthView($result) : null;
    }

}