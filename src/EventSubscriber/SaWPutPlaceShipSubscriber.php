<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Controller\SaWController;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;

/**
 * Class SaWPostPlaceShipSubscriber
 *
 * @package App\EventSubscriber
 * @author Christian Ruppel < post@christianruppel.de >
 */
class SaWPutPlaceShipSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * SaWStartGameSubscriber constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $action = null;
        $controller = $event->getController();

        if (is_array($controller)) {
            $action = $controller[1];
            $controller = $controller[0];
        }

        $method = $event->getRequest()->getMethod();

        /* @var SaWController $controller */
        if ($controller instanceof SaWController) {
            if ($method === 'PUT' && $action === 'placeShip') {
                $paramsValid = $this->validateParams($event);

                if ($paramsValid !== true) {
                    $event->setController(function () use ($paramsValid) {
                        return new JsonResponse([
                            'success' => false,
                            'message' => sprintf(
                                'Params %s not valid',
                                implode(', ', array_keys(array_filter(
                                    $paramsValid,
                                    function ($e) { return !$e; }
                                )))
                            ),
                            'params' => $paramsValid
                        ], 400);
                    });
                }
            }
        }
    }

    /**
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController'
        ];
    }

    /**
     * @param ControllerEvent $event
     *
     * @return array|bool
     */
    private function validateParams (ControllerEvent $event)
    {
        $request = $event->getRequest();
        $json = json_decode($request->getContent(), true);

        $validator = Validation::createValidator();
        $result = [];
        foreach (['shipId', 'coordinate', 'direction'] as $key) {
            if (!isset($json[$key])) {
                $result[$key] = false;
            }
            else {
                switch ($key)
                {
                    case 'coordinate': {
                        $constraints = [
                            new Length(['min' => 2]),
                            new Type(['type' => 'string'])
                        ];
                    } break;

                    case 'shipId': {
                        $constraints = [
                            // new Type(['type' => 'integer'])
                        ];
                    } break;

                    case 'direction': {
                        $constraints = [
                            new Type(['type' => 'string'])
                        ];
                    } break;
                }

                $violations = $validator->validate($json[$key], array_merge([
                    new NotBlank()
                ], $constraints));

                if (count($violations) !== 0) {
                    $result[$key] = false;

                    $messages = [];

                    foreach ($violations as $violation) {
                        $messages[] = $violation->getMessage();
                    }

                    $this->logger->debug('placeShip validation failed!', [
                        'messages' => $messages
                    ]);
                }
                else {
                    $request->request->set($key, $json[$key]);
                    $result[$key] = true;
                }
            }
        }

        return count(array_unique($result)) === 1 ?: $result;
    }
}
