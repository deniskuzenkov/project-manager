<?php

namespace App\Model\Work\Entity\Members\Member;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class MemberRepository
{
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Member::class);
        $this->em = $em;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function has(Id $id): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.id = :id')
            ->setParameter(':id', $id->getValue())
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function get(Id $id): Member
    {
        /** Member $member */
        if (!$member = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Member not found.');
        }
        return $member;
    }

    public function add(Member $member): void
    {
        $this->em->persist($member);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function hasByGroup(\App\Model\Work\Entity\Members\Group\Id $id): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.group = :group')
            ->setParameter(':group', $id->getValue())
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}