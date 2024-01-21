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
        return $result ? new AuthView($result) : null;
    }

    public function findByEmail(string $email): ?ShortView
    {
        $stmt = $this->connection->executeQuery("
            select u.id as id,
            u.email as email,
            u.role as role,
            u.status as status
            from user_users u
            where u.email = :email", ['email' => $email]);
        $result = $stmt->fetchAssociative();
        return $result ? new ShortView($result) : null;
    }

    public function findDetail(string $id): ?DetailView
    {
        $stmt = $this->connection->executeQuery("
            select 
            u.id as id,
            u.email as email,
            u.date as date,
            u.role as role,
            u.status as status
            from user_users u
            where u.id = :id", ['id' => $id]);
        $result = $stmt->fetchAssociative();
        $detailView = $result ? new DetailView($result) : null;

        if (!$detailView) {
            return null;
        }

        $stmt = $this->connection->executeQuery("
            select network, identity
            from user_user_networks
            where user_id = :id", ['id' => $id]);
        $result = $stmt->fetchAllAssociative();
        $detailView->setNetworks($result);
        return $detailView;
    }

}