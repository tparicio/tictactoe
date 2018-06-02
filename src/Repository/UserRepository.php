<?php
// src/Repository/UserRepository.php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * return total games count
     *
     * @return integer $count
     */
    public function getCount()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('count(user.id)')
            ->from('App\Entity\User','user');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * return user last week in percentage of total users
     *
     * @return integer $percentage
     */
    public function getLastWeek()
    {
        // if no games then return zero
        if (($total = $this->getCount()) == 0)
            return 0;

        // build query for get last week games
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('count(user.id)')
            ->where('user.created_at >= :last_week')
            ->from('App\Entity\User','user')
            ->setParameter('last_week', date("Y-m-d", strtotime('-7 day')));

        // return value formatted as percentage
        return (int)(100 * $queryBuilder->getQuery()->getSingleScalarResult() / $total);
    }
}
