<?php

namespace App\Repository;

use App\Entity\Snippet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Snippet>
 */
class SnippetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Snippet::class);
    }

    public function findPendingWithFilters(?string $tag = null, ?string $language = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.tags', 't')
            ->leftJoin('s.author', 'a')
            ->addSelect('t', 'a')
            ->orderBy('s.createdAt', 'DESC');

        if ($tag) {
            $qb->andWhere('t.name = :tag')->setParameter('tag', $tag);
        }
        if ($language) {
            $qb->andWhere('s.language = :language')->setParameter('language', $language);
        }

        return $qb->getQuery()->getResult();
    }

    public function findRecentByTag(string $tagName, int $limit = 20): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.tags', 't')
            ->leftJoin('s.author', 'a')
            ->addSelect('t', 'a')
            ->where('t.name = :tag')
            ->setParameter('tag', $tagName)
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
