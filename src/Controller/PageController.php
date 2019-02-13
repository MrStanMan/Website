<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class PageController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('pages/index.html.twig');
    }

    /**
     * @return Response
     */
    public function admin()
    {
        return new Response('<h3>Yeet</h3>');
    }

}
