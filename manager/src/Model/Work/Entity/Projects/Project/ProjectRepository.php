<?php

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\Work\Entity\Projects\Id;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

class ProjectRepository
{
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Project::class);
        $this->em = $em;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function get(Id $id): Project
    {
        $project = $this->repo->createQueryBuilder('t')
            ->andWhere('t.id = :id')
            ->setParameter(':id', $id->getValue())
            ->getQuery()->getResult();

        if (!$project) {
            throw new EntityNotFoundException('Project is not found.');
        }
        return reset($project);
    }

    public function add(Project $project): void
    {
        $this->em->persist($project);
    }

    public function remove(Project $project): void
    {
        $this->em->remove($project);
    }
}