<?php
// src/Repository/GameRepository.php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class GameRepository extends EntityRepository
{
    /**
     * return total games count
     *
     * @return integer $count
     */
    public function getCount()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('count(game.id)')
            ->from('App\Entity\Game','game');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * return total games count
     *
     * @return integer $count
     */
    public function getCountCirclesWin()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('count(game.id)')
            ->where('game.result = 1')
            ->from('App\Entity\Game','game');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * return total games count
     *
     * @return integer $count
     */
    public function getCountCrossesWin()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('count(game.id)')
            ->where('game.result = 2')
            ->from('App\Entity\Game','game');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * return total games count
     *
     * @return integer $count
     */
    public function getCountDraw()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('count(game.id)')
            ->where('game.result = 3')
            ->from('App\Entity\Game','game');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * return total games count
     *
     * @return integer $count
     */
    public function getCountUnfinished()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('count(game.id)')
            ->where('game.result IS NULL')
            ->from('App\Entity\Game','game');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * return games last week in percentage of total games
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
            ->select('count(game.id)')
            ->where('game.created_at >= :last_week')
            ->from('App\Entity\Game','game')
            ->setParameter('last_week', date("Y-m-d", strtotime('-7 day')));

        // return value formatted as percentage
        return (int)(100 * $queryBuilder->getQuery()->getSingleScalarResult() / $total);
    }

    /**
    * return games wins by circles last week in percentage of all games
    * won by circles
    *
    * @return integer $percentage
     */
    public function getLastWeekCirclesWin()
    {
        // if no games then return zero
        if (($total = $this->getCountCirclesWin()) == 0)
            return 0;

        // build query for get last week games won by circles
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('count(game.id)')
            ->where('game.result = 1')
            ->andWhere('game.created_at >= :last_week')
            ->from('App\Entity\Game','game')
            ->setParameter('last_week', date("Y-m-d", strtotime('-7 day')));

        // return value formatted as percentage
        return (int)(100 * $queryBuilder->getQuery()->getSingleScalarResult() / $total);
    }

    /**
    * return games wins by crosses last week in percentage of all games
    * won by crosses
     *
     * @return integer $percentage
     */
    public function getLastWeekCrossesWin()
    {
        // if no games then return zero
        if (($total = $this->getCountCrossesWin()) == 0)
            return 0;

        // build query for get last week games won by circles
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('count(game.id)')
            ->where('game.result = 2')
            ->andWhere('game.created_at >= :last_week')
            ->from('App\Entity\Game','game')
            ->setParameter('last_week', date("Y-m-d", strtotime('-7 day')));

        // return value formatted as percentage
        return (int)(100 * $queryBuilder->getQuery()->getSingleScalarResult() / $total);
    }

    /**
     * return games draw last week in percentage of all draws
     *
     * @return integer $percentage
     */
    public function getLastWeekDraw()
    {
        // if no games then return zero
        if (($total = $this->getCountDraw()) == 0)
            return 0;

        // build query for get last week games won by circles
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('count(game.id)')
            ->where('game.result = 3')
            ->andWhere('game.created_at >= :last_week')
            ->from('App\Entity\Game','game')
            ->setParameter('last_week', date("Y-m-d", strtotime('-7 day')));

        // return value formatted as percentage
        return (int)(100 * $queryBuilder->getQuery()->getSingleScalarResult() / $total);
    }

    /**
     * return unfinished games last week in percentage of all unfinished games
     *
     * @return integer $percentage
     */
    public function getLastWeekUnfinished()
    {
        // if no games then return zero
        if (($total = $this->getCountUnfinished()) == 0)
            return 0;

        // build query for get last week games won by circles
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('count(game.id)')
            ->where('game.result IS NULL')
            ->andWhere('game.created_at >= :last_week')
            ->from('App\Entity\Game','game')
            ->setParameter('last_week', date("Y-m-d", strtotime('-7 day')));

        // return value formatted as percentage
        return (int)(100 * $queryBuilder->getQuery()->getSingleScalarResult() / $total);
    }

    /**
     * return count games group by victory lines
     *
     * @return integer $percentage
     */
    public function getGamesByWinningLines()
    {
      // build query for get last week games won by circles
      $queryBuilder = $this->getEntityManager()->createQueryBuilder()
          ->select('count(game.id), game.line')
          ->where('game.line IS NOT NULL')
          ->groupBy('game.line')
          ->from('App\Entity\Game','game');

      return $queryBuilder->getQuery()->getResult();
    }

    /**
     * return games of one serie
     *
     * @return integer $group
     */
    public function getHistory($group)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('game.result, game.created_at')
            ->where('game.group = :group')
            ->from('App\Entity\Game','game')
            ->setParameter('group',$group);;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * return score for a game series
     *
     * @return array indexed by result 
     */
    public function getHistoryScore($group)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('game.result, count(game.id)')
            ->where('game.result IS NOT NULL')
            ->andWhere('game.group = :group')
            ->from('App\Entity\Game','game', 'game.result')
            ->groupBy('game.result')
            ->setParameter('group',$group);;

        return $queryBuilder->getQuery()->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}
