<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Game;
use App\SaW\GridFactory;
use App\SaW\Play;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class SaWController
 *
 * @package App\Controller
 * @author Christian Ruppel < post@christianruppel.de >
 *
 * @Route("/api/saw")
 */
class SaWController extends AController
{
    /**
     * @var Play
     */
    protected $gamePlay;

    /**
     * @var GridFactory
     */
    protected $gridFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * SaWController constructor.
     *
     * @param Play $gamePlay
     * @param GridFactory $gridFactory
     * @param NormalizerInterface $normalizer
     */
    public function __construct(Play $gamePlay, GridFactory $gridFactory, LoggerInterface $logger, NormalizerInterface $normalizer)
    {
        $this->gamePlay = $gamePlay;
        $this->gridFactory = $gridFactory;
        $this->logger = $logger;
        $this->normalizer = $normalizer;
    }

    /**
     * @param Request $request
     *
     * @Route(
     *     path="/start",
     *     methods={"POST"},
     *     defaults={
     *          "_api_resource_class"=Game::class
     *     }
     * )
     *
     * @return Game|JsonResponse
     */
    public function startGame (Request $request)
    {
        try {
            $game = $this->gamePlay->startNewGame($request->request->get('playerName'));
            $normalized = $this->normalizer->normalize($game);

            return $this->json($normalized);
        }
        catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @Route(
     *     path="/{id}",
     *     methods={"GET"},
     *     defaults={
     *          "_api_resource_class"=Game::class
     *     }
     * )
     */
    public function getGame (int $id)
    {
        try {
            $game = $this->gamePlay->setGame($id);
        }
        catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        $normalized = $this->normalizer->normalize($game);

        return $this->json($normalized);
    }

    /**
     * @Route(
     *     path="/{id}/place_ship",
     *     methods={"PUT"},
     *     defaults={
     *          "_api_resource_class"=Board::class
     *     }
     * )
     *
     * @todo fix grid / board sync
     */
    public function placeShip (Request $request, int $id)
    {
        $this->gamePlay->setGame($id);

        $shipId = $request->request->get('shipId');
        $coordinate = $request->request->get('coordinate');
        $direction = $request->request->get('direction', 'horizontal');

        try {
            $board = $this->gamePlay->placeShip($shipId, $coordinate, $direction);
            $normalized = $this->normalizer->normalize($board);

            return $this->json($normalized);
        }
        catch (\Exception $e) {
            $this->logger->debug($e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route(
     *     path="/{id}/shot",
     *     methods={"POST"}
     * )
     */
    public function shot (Request $request, int $id)
    {
        $this->gamePlay->setGame($id);
        try {
            if ($request->getMethod() === 'POST') {
                $json = json_decode($request->getContent(), true);

                if (!isset($json['coordinate'])) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Invalid coordinate or not set!'
                    ], 500);
                }

                $result = $this->gamePlay->shot($json['coordinate']);

                return $this->json([$result]);
            }
            else if ($request->getMethod() === 'GET') {
                return $this->json('@todo - get a bot shot (formerly board / grid update)');
            }
        }
        catch (\Exception $e) {
            $this->logger->debug($e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route(
     *     path="/{id}/salve",
     *     methods={"GET|POST"}
     * )
     */
    public function salve (Request $request, int $id)
    {
        $this->gamePlay->setGame($id);

        if ($request->getMethod() === 'POST') {
            $json = json_decode($request->getContent(), true);

            if (!isset($json['coordinates']) || !is_array($json['coordinates'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid coordinates or not set!'
                ], 500);
            }

            // $result = $this->gamePlay->salve($json['coordinates']);

            return $this->json([
                '@todo - get a bot shot (formerly board / grid update)',
                'json' => $json
            ]);
        }
        else if ($request->getMethod() === 'GET') {
            return $this->json('@todo - get a bot shot (formerly board / grid update)');
        }
    }

    /**
     * @Route(
     *     path="/{id}/replace_ships",
     *     methods={"GET|POST"}
     * )
     */
    public function replaceShips ($id)
    {
        return parent::methodNotImplemented();
    }
}