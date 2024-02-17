<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Project;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class DepartmentFetcher
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function listOfProject(string $project): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name'
            )
            ->from('work_projects_project_departments')
            ->andWhere('project_id = :project')
            ->setParameter('project', $project)
            ->orderBy('name')
            ->executeQuery();

        return $stmt->fetchAllKeyValue();
    }
}