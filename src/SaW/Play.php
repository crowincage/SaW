<?php
declare(strict_types=1);

namespace App\SaW;

use App\Entity\Board;
use App\Entity\Game;
use App\Entity\Player;
use App\Entity\Ship;
use App\SaW\GridFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Play - handles game flow
 *
 * @package App\SaW
 * @author Christian Ruppel < post@christianruppel.de >
 */
class Play
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var GridFactory
     */
    protected $gridFactory;

    /**
     * @var Game
     */
    protected $game;

    /**
     * @var Ship[]|ArrayCollection
     */
    protected $ships;

    /**
     * @var Grid[]|ArrayCollection
     */
    protected $grids;


    /**
     * Play constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param GridFactory $gridFactory
     * @param int|null $gameId
     *
     * @throws \Exception
     */
    public function __construct(EntityManagerInterface $entityManager, GridFactory $gridFactory, int $gameId = null)
    {
        $this->entityManager = $entityManager;
        $this->gridFactory = $gridFactory;

        if (!is_null($gameId)) {
            $this->loadGame($gameId);
        }

        $this->grids = new ArrayCollection();
        $this->loadShips();
    }

    /**
     * Start a new game - create player & game entities
     *
     * @param string $playerName
     * @param int $boardSize
     *
     * @return Game
     */
    public function startNewGame (string $playerName, int $boardSize = 10): Game
    {
        $player = $this->createPlayer($playerName);
        $this->game = $this->createGame($player);

        $this->createGrids($boardSize);

        // auto place ships

        return $this->game;
    }

    /**
     * @param int $gameId
     *
     * @return Game
     *
     * @throws \Exception
     */
    public function setGame (int $gameId): Game
    {
        $this->loadGame($gameId);

        return $this->game;
    }

    /**
     * @param $shipId
     * @param string $coordinate
     * @param $direction
     * @param string $who
     *
     * @return Board
     *
     * @throws \Exception
     *
     * @todo - return game, check / fix placement
     */
    public function placeShip ($shipId, string $coordinate, $direction, string $who = Board::BOARD_USER_PLAYER)
    {
        if (!isset($this->ships[$shipId])) {
            throw new \InvalidArgumentException('shipId not valid');
        }

        $key = implode('_', [$who, Board::BOARD_TYPE_SHIP]);

        /* @var \App\Entity\Board $board */
        $board = $this->grids->get($key);

        /* @var \App\SaW\Grid $grid */
        $coordinate = Grid\Coordinate::fromCoordinate($coordinate);
        $grid = call_user_func($this->gridFactory, $board);

        if (!$grid->isCoordinateInGridBounds($coordinate)) {
            throw new \OutOfBoundsException('Coordinate ist out of bounds!');
        }
        else {
            $direction = Grid\Direction::VERTICAL === $direction
                ? Grid\Direction::vertical()
                : Grid\Direction::horizontal();


            $grid->placeShip(
                $this->ships[$shipId],
                $coordinate,
                $direction
            );

            $board->setLayout($grid->toString());
            $this->entityManager->flush();
        }

        return $board;
    }

    public function placeShipsAutomated ($shipBoard)
    {
        $directions = [
            Grid\Direction::vertical(),
            Grid\Direction::horizontal()
        ];

        /* @var Board $board */
        foreach ($shipBoard as $board) {
            // $grid = Grid::loadBoard($board);
            $grid = call_user_func($this->gridFactory, $board);

            // check grid

            /* @var Ship $ship */
            foreach ($this->ships as $ship) {
                $placed = false;
                while (!$placed) {
                    $coordinate = $this->findRandomCoordinate($ship->getSize());

                    try {
                        $grid->placeShip(
                            $ship,
                            Grid\Coordinate::fromCoordinate($coordinate),
                            $directions[array_rand($directions, 1)]
                        );
                    }
                    catch (\InvalidArgumentException $e) {
                        continue;
                    }
                    catch (\OutOfBoundsException $e) {
                        continue;
                    }

                    $placed = true;
                }
            }
        }
    }

    private function findRandomCoordinate (int $length)  { return 'B3'; }

    /**
     * @param $coordinate
     * @param string $who
     *
     * @return array
     */
    public function shot ($coordinate, string $who = Board::BOARD_USER_BOT)
    {
        $key = implode('_', [$who, Board::BOARD_TYPE_SHIP]);
        /* @var Board $board */
        $board = $this->grids->get($key);

        /* @var \App\SaW\Grid $grid */
        $grid = call_user_func($this->gridFactory, $board);

        if (!$grid->isCoordinateInGridBounds(Grid\Coordinate::fromCoordinate($coordinate))) {
            throw new \OutOfBoundsException('Coordinate ist out of bounds!');
        }

        $shot = $grid->shot(Grid\Coordinate::fromCoordinate($coordinate));

        // save the shot on Board::BOARD_TYPE_SHOT types
        $this->saveShot(
            Board::BOARD_USER_BOT === $who ? Board::BOARD_USER_BOT : Board::BOARD_USER_PLAYER,
            Grid\Coordinate::fromCoordinate($coordinate)
        );

        if ($shot === Grid::TYPE_HIT) {
            return [ $coordinate => true ];
        }

        return [ $coordinate => false ];
    }

    /**
     * @param string $who
     * @param Grid\Coordinate $coordinate
     */
    private function saveShot (string $who, Grid\Coordinate $coordinate)
    {
        if (!in_array($who, [Board::BOARD_USER_BOT, Board::BOARD_USER_PLAYER])) {
            throw new \InvalidArgumentException('Unknown board user!');
        }

        $key = implode('_', [$who, Board::BOARD_TYPE_SHOT]);
        /* @var Board $board */
        $board = $this->grids->get($key);

        /* @var \App\SaW\Grid $grid */
        $grid = call_user_func($this->gridFactory, $board);
        $grid->markShot($coordinate);

        $board->setLayout($grid->toString());
        $this->entityManager->flush();
    }

    public function salve ()
    {
        //
    }

    /**
     * Create empty grid
     *
     * @param int $size
     */
    protected function createGrids (int $size = Board::BOARD_DEFAULT_SIZE): void
    {
        foreach ([Board::BOARD_USER_BOT, Board::BOARD_USER_PLAYER] as $boardUser) {

            foreach ([Board::BOARD_TYPE_SHIP, Board::BOARD_TYPE_SHOT] as $boardType) {
                // create an empty grid
                $gridBoard = call_user_func($this->gridFactory);

                $board = new Board();
                $board->setBoardSize($size);
                $board->setBoardType($boardType);
                $board->setBoardUser($boardUser);
                $board->setGame($this->game);
                $board->setLayout($gridBoard->toString());

                $this->game->addBoard($board);

                $this->entityManager->persist($board);
                $this->entityManager->flush();

                $this->grids->offsetSet(implode('_', [
                    $boardUser,
                    $boardType
                ]), $board);
            }
        }
    }

    /**
     * @param int $gameId
     *
     * @throws \Exception
     */
    protected function loadGame (int $gameId): void
    {
        /* @var Game $game */
        $game = $this->entityManager->getRepository(Game::class)->find($gameId);

        if (!$game) {
            throw new \Exception(sprintf('Game ID %s not found!', $gameId));
        }

        /* @var Board $board */
        foreach ($game->getBoards(false) as $board) {
            $this->grids->offsetSet(implode('_', [
                $board->getBoardUser(),
                $board->getBoardType()
            ]), $board);
        }

        $this->game = $game;
    }

    /**
     * @return void
     */
    protected function loadShips (): void
    {
        $ships = $this->entityManager->getRepository(Ship::class)->findAll();

        /* @var Ship $ship */
        foreach ($ships as $ship) {
            $this->ships[$ship->getId()] = $ship;
        }
    }

    /**
     * @param string $playerName
     *
     * @return Player
     */
    protected function createPlayer(string $playerName): Player
    {
        $player = new Player();
        $player->setName($playerName);

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $player;
    }

    /**
     * @param Player $player
     *
     * @return Game
     */
    protected function createGame(Player $player): Game
    {
        $game = new Game();
        $game->setPlayer($player);

        $this->entityManager->persist($game);

        $player->setGame($game);

        $this->entityManager->flush();

        return $game;
    }

}