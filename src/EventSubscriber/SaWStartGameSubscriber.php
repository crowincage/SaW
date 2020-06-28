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
 * Class SaWStartGameSubscriber
 *
 * @package App\EventSubscriber
 * @author Christian Ruppel < post@christianruppel.de >
 */
class SaWStartGameSubscriber implements EventSubscriberInterface
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
            if ($method === 'POST' && $action === 'startGame') {
                $validationResult = $this->validateParams($event);

                if ($validationResult !== true) {
                    $event->setController(function () use ($validationResult) {
                        return new JsonResponse([
                            'success' => false,
                            'message' => implode("\n", $validationResult)
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

        if (!isset($json['playerName'])) {
            return ['playerName not set!'];
        }

        $validator = Validation::createValidator();
        $violations = $validator->validate($json['playerName'], [
            new Length(['min' => 3]),
            new Type(['type' => 'string']),
            new NotBlank()
        ]);

        if (count($violations) !== 0) {
            $messages = [];

            foreach ($violations as $violation) {
                $messages[] = $violation->getMessage();
            }

            $this->logger->debug('startGame validation failed!', [
                'messages' => $messages
            ]);

            return $messages;
        }

        $request->request->set('playerName', $json['playerName']);

        return true;
    }
}
