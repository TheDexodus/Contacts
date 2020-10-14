<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app.admin.index")
     *
     * @return Response
     */
    public function index(): Response
    {
        return new Response('ok');
    }
}
