<?php
// src/Controller/StatsController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\GameRepository;
use App\Entity\Game;
use App\Entity\User;
use App\Entity\Move;

/**
 * Class for handle statistics Requests and Responses
 */
class StatsController extends Controller
{
    /**
     * show game statistics
     *
     * @return Response
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        // get stats for all time
        $stats = [
            'users'      => $em->getRepository(User::class)->getCount(),
            'games'      => $em->getRepository(Game::class)->getCount(),
            'circles'    => $em->getRepository(Game::class)->getCountCirclesWin(),
            'crosses'    => $em->getRepository(Game::class)->getCountCrossesWin(),
            'draws'      => $em->getRepository(Game::class)->getCountDraw(),
            'unfinished' => $em->getRepository(Game::class)->getCountUnfinished(),
        ];

        // get stats for last week
        $stats_last_week = [
            'users'      => $em->getRepository(User::class)->getLastWeek(),
            'games'      => $em->getRepository(Game::class)->getLastWeek(),
            'circles'    => $em->getRepository(Game::class)->getLastWeekCirclesWin(),
            'crosses'    => $em->getRepository(Game::class)->getLastWeekCrossesWin(),
            'draws'      => $em->getRepository(Game::class)->getLastWeekDraw(),
            'unfinished' => $em->getRepository(Game::class)->getLastWeekUnfinished(),
        ];

        return $this->render('stats/index.html.twig', [
            'stats' => $stats,
            'stats_last_week' => $stats_last_week,
            'lines' => $em->getRepository(Game::class)->getGamesByWinningLines()
        ]);
    }
}
