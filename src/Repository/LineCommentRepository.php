<?php

namespace App\Repository;

use App\Entity\LineComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LineComment>
 */
class LineCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LineComment::class);
    }

    public function findBySnippetGroupedByLine(int $snippetId): array
    {
        $comments = $this->createQueryBuilder('lc')
            ->leftJoin('lc.author', 'a')
            ->addSelect('a')
            ->where('lc.snippet = :snippet')
            ->setParameter('snippet', $snippetId)
            ->orderBy('lc.lineNumber', 'ASC')
            ->addOrderBy('lc.createdAt', 'ASC')
            ->getQuery()
            ->getResult();

        $grouped = [];
        foreach ($comments as $comment) {
            $grouped[$comment->getLineNumber()][] = $comment;
        }
        return $grouped;
    }
}
