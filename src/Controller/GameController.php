<?php
// src/Controller/GameController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Game;
use App\Entity\Move;
use App\Utils\Bitboard;

/**
 * Class for handle game request
 */
class GameController extends Controller
{
    /**
     * get an already started game from database and show at this state
     *
     * @param integer $game game id to show
     * @Route("/game/{game}")
     */
    public function game($game)
    {
        // get doctrince manager
        $entityManager = $this->getDoctrine()->getManager();

        // get playing game info
        $game = $entityManager->find('App\Entity\Game', $game);

        // get bitboards as array for javascript
        $bitboard = Bitboard::bitboards2array(
            $game->getBitboardOne(),
            $game->getBitboardTwo()
        );

        // get all games played in this series
        $games = $entityManager->getRepository(Game::class)->getHistory($game->getGroup());

        // get serie scoreboard
        $score = $entityManager->getRepository(Game::class)->getHistoryScore($game->getGroup());

        return $this->render('game/board.html.twig', [
            'game' => $game,
            'games' => $games,
            'results' => $score,
            'bitboard' => $bitboard
        ]);
    }

    /**
     * create new game human vs machine
     *
     * @param integer $turn = 1 : player who open game
     * @param integer|null $group = null : original game when rematch
     * @Route("/game/new/human")
     */
    public function human($turn = 1, $group = null)
    {
        // get doctrince manager
        $entityManager = $this->getDoctrine()->getManager();

        $game = new Game();
        $game->setPlayerOne(0)      // anonymous (human) player
            ->setPlayerTwo(0)       // anonymous (human) player
            ->setTurn($turn)
            ->setMode('human')
            ->setGroup($group);

        // save game
        $entityManager->persist($game);

        // executes the queries
        $entityManager->flush();

        // if first game in a series then set this own game id as group
        if (!$group) {
            $game->setGroup($game->getId());
            $entityManager->flush();
        }

        return $this->redirectToRoute('game', ['game' =>$game->getId()]);
    }

    /**
     * create new game human vs machine
     *
     * @param integer $turn = 1 : player who open game
     * @param integer|null $group = null : original game when rematch
     * @Route("/game/new/machine")
     */
    public function machine($turn = 1, $group = null)
    {
        // get doctrince manager
        $entityManager = $this->getDoctrine()->getManager();

        $game = new Game();
        $game->setPlayerOne(0)      // anonymous (human) player
            ->setPlayerTwo(null)    // machine no player
            ->setTurn($turn)
            ->setMode('machine')
            ->setGroup($group);

        // save game
        $entityManager->persist($game);

        // executes the queries
        $entityManager->flush();

        // if first game in a series then set this own game id as group
        if (!$group) {
            $game->setGroup($game->getId());
            $entityManager->flush();
        }

        // if game against machine then find machine move
        if ($game->getTurn() == 2) {
            $bitboard = $this->machineMove($game);
        }

        return $this->redirectToRoute('game', ['game' =>$game->getId()]);
    }

    /**
     * start new game with same players and switch turn
     *
     * @param  Game $game
     * @return Response
     * @Route("/game/rematch/{game}")
     */
    public function rematch($game)
    {
        $repository = $this->getDoctrine()->getRepository(Move::class);
        $move = $repository->findOneByGame($game);
        $turn = $move->getPlayer() == 1 ? 2 : 1;

        // get doctrince manager
        $entityManager = $this->getDoctrine()->getManager();
        $game = $entityManager->find('App\Entity\Game', $game);

        // build new bame depending on game mode
        if ($move->getGame()->getMode() == 'machine') {
            return $this->machine($turn, $game->getGroup());
        } else {
            return $this->human($turn, $game->getGroup());
        }
    }

    /**
     * receive move from AJAX
     * check move and return machine move when game vs machine
     * return JSON response for handle in javascript
     *
     * @param  Request $request [description]
     * @return ResponseJSON
     */
    public function move(Request $request)
    {
        // get bitboard for selected square
        $bitboard = new Bitboard((int)$request->request->get('bitboard'));
        $player   = $request->request->get('player');
        $machine  = null;           // machine move bitboard

        // get doctrince manager
        $entityManager = $this->getDoctrine()->getManager();

        $game = $entityManager->find('App\Entity\Game', $request->request->get('game'));

        if (!$this->validateMove($game, $bitboard, $player)) {
            return $this->json([
                'result' => false,
                'message' => $this->get('translator')->trans('Move is not allowed!')
            ]);
        }

        // save move
        $move = new Move();
        $move->setGame($game);
        $move->setPlayer($player);
        $move->setBitboard($request->request->get('bitboard'));

        $entityManager->persist($move);

        // get turn for next move
        $turn = $player == 1 ? 2 : 1;
        // make move by player1
        if ($player == 1) {
            $game->setBitboardOne($game->getBitboardOne()->or($bitboard));
        } else {
            $game->setBitboardTwo($game->getBitboardTwo()->or($bitboard));
        }
        $game->setTurn($turn);

        // update game with new board status
        $entityManager->flush();

        // check if player1 wins
        $bitboard = $player == 1 ? $game->getBitboardOne() : $game->getBitboardTwo();

        if ($bitboard->isLine()) {
            $game->setResult($player);
            $game->setLine($bitboard->getLine());

            // update game with new board status
            $entityManager->flush();

            return $this->json([
                'result' => true,
                'move' => null,
                'winner' => $player,
                'line' => $bitboard->getLine()
            ]);
        }

        // draw when all squares are taked
        if ( $game->getBitboardOne()->or($game->getBitboardTwo())->isFull() ) {
            $game->setResult(3);

            // update game with new board status
            $entityManager->flush();

            return $this->json(['result' => true, 'winner' => 3]);
        }

        // make move for machine
        if ($game->getMode() == 'machine') {
          $machine = $this->machineMove($game);

          // check if player2 wins
          $bitboard = $game->getBitboardTwo();
          if ($bitboard->isLine()) {
              $game->setResult(2);
              $game->setLine($bitboard->getLine());

              // update game with new board status
              $entityManager->flush();

              return $this->json([
                'result' => true,
                'move' => $machine ? $machine->getInteger() : null,
                'winner' => 2,
                'line' => $bitboard->getLine()
              ]);
          }
        }

        // draw when all squares are taked
        if ( $game->getBitboardOne()->or($game->getBitboardTwo())->isFull() ) {
            $game->setResult(3);

            // update game with new board status
            $entityManager->flush();

            return $this->json([
                'result' => true,
                'move' => $machine ? $machine->getInteger() : null,
                'winner' => 3
            ]);
        }

        // if no winner or draw send regular move
        return $this->json([
            'result' => true,
            'move' => $machine ? $machine->getInteger() : null
        ]);
    }

    /**
     * [validateMove description]
     * @param  Game $game game to validate move
     * @param  Bitboard $bitboard move to validate
     * @param  integer $turn user how move
     * @return boolean
     */
    private function validateMove($game, $bitboard, $turn) {
        // check game is still not finished
        if ($game->getResult()) {
            return false;
        }
        // check turn is right
        if ($game->getTurn() != $turn) {
            return false;
        }
        // check square is free
        $all = $game->getBitboardOne()->or($game->getBitboardTwo());
        if (!$all->and($bitboard)->isEmpty()) {
            return false;
        }
        return true;
    }

    /**
     * calculate and execute (and save) machine move
     *
     * @param  App\Entity\Game $game for get machine move
     * @return integer $bitboard with move
     */
    private function machineMove($game)
    {
        // find move by machine
        $bitboard = $this->findMove(
            $game->getBitboardTwo(),
            $game->getBitboardOne()
        );

        // save move into database
        $move = new Move();
        $move->setGame($game)
             ->setPlayer(2)
             ->setBitboard($bitboard);

        // get doctrince manager
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($move);

        // update game status with player2 move and change game turn
        $game->setBitboardTwo($game->getBitboardTwo()->or($bitboard));
        $game->setTurn(1);

        $entityManager->flush();

        // return bitboard with move
        return $bitboard;
    }

    /**
     * find best move for a player
     * in order try to get:
     *   · square to win
     *   · square to avoid lost
     *   · center
     *   · random
     *
     * @param  Bitboard $bitboard player how moves bitboard
     * @param  Bitboard $rival rival bitboard
     * @return Bitboard $bitboard to move
     */
    private function findMove($player, $rival)
    {
        // get empty squares in bitboard
        // join 2 players pieces and XOR with full board to get only empty squares
        $all     = $player->or($rival);
        $empty   = $all->xor(new Bitboard(Bitboard::BOARD));
        $block   = null;
        $empties = [];

        // check moves to win and moves to block enemy win
        for ($i = 0 ; $i < 9 ; $i++) {
            $move = Bitboard::index($i);
            if ( $move->and($empty)->isEmpty() ) //($empty & $move) == 0) {
                continue;

            // check if a piece on this square make player win
            if ($player->or($move)->isLine()) {
                // no need search any other because already win
                return $move;
            }

            // check if a piece on this square block a rival win chance
            if (!$block && $rival->or($move)->isLine()) {
                // need search others, maybe still can win
                $block = $move;
            }

            $empties[$i] = $move;
        }

        // if any move block a enemy attack then return this move
        if ($block)
            return $block;

        // if still not found move then try to take center
        // check if center is available (in empty bitboard)
        if ( $empty->hasCenter() )  // 0b000010000
            return new Bitboard(Bitboard::CENTER);

        // if still not found move then make random
        $index = array_rand($empties, 1);

        return $empties[$index];
    }

    /**
     * test method for any debug operations
     *
     * @Route("/gametest")
     */
    public function test()
    {
        $player1 = new Bitboard(16);
        $player2 = new Bitboard(175);

        $player1->draw();
        $player2->draw();

        //$move = $this->findMove(bindec('000010000'), bindec('000000000'));
        $move = $this->findMove($player2, $player1);

        $move->draw();

        return new Response();
    }
}
