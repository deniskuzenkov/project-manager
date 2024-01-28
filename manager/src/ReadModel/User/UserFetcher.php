<?php

namespace App\ReadModel\User;


use App\ReadModel\User\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class UserFetcher
{
    private Connection $connection;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
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

    public function findBySignUpConfirmToken(string $token): ?ShortView
    {
        $stmt = $this->connection->executeQuery("
            select u.id as id,
            u.email as email,
            u.role as role,
            u.status as status
            from user_users u
            where u.confirm_token = :token", ['token' => $token]);
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
            u.status as status,
            name_first as first_name,
            name_last as last_name
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

    public function getDetail(string $id): DetailView
    {
        if (!$detail = $this->findDetail($id)) {
            throw new \LogicException('User is not found');
        }
        return $detail;
    }

    /**
     * @throws Exception
     */
    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $db = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'date',
                'role',
                'status',
                'TRIM(CONCAT(name_first , \' \',  name_last))  as name'
            )
            ->from('user_users');

        if ($filter->name) {
            $db->andWhere($db->expr()->like('LOWER(CONCAT(name_first, \'\', name_last))', ':name'));
            $db->setParameter('name', '%' . mb_strtolower($filter->name) . '%');
        }

        if ($filter->email) {
            $db->andWhere($db->expr()->like('LOWER(email)', ':email'));
            $db->setParameter('email', '%' . mb_strtolower($filter->email) . '%');
        }

        if ($filter->status) {
            $db->andWhere('status = :status');
            $db->setParameter('status', $filter->status);
        }

        if ($filter->role) {
            $db->andWhere('role = :role');
            $db->setParameter('role', $filter->role);
        }

        if (!\in_array($sort, ['date', 'name', 'email', 'role', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $db->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($db, $page, $size);
    }

}