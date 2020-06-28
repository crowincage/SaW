<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AController
 *
 * @package App\Controller
 * @author Christian Ruppel < post@christianruppel.de >
 */
abstract class AController extends AbstractController
{
    /**
     * @return JsonResponse
     */
    protected function methodNotImplemented()
    {
        return new JsonResponse([
            'success' => false,
            'message' => 'method not available!'
        ], 400);
    }
}