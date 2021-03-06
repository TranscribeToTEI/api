<?php

namespace AppBundle\Repository;

/**
 * CommentLogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentLogRepository extends \Doctrine\ORM\EntityRepository
{
    public function getCommentsByThreadId($threadType, $values) {
        $qb = $this->createQueryBuilder('c')
            ->join('c.thread', 't');
        $qb->where($qb->expr()->like('t.id', $qb->expr()->literal('%'.$threadType.'%')));

        foreach ($values as $key => $value) {
            $qb->andWhere($qb->expr()->like('t.id',$qb->expr()->literal('%'.$value.'%')));
        }

        return $qb->orderBy('c.id', 'DESC')->getQuery()->getResult();
    }
}
