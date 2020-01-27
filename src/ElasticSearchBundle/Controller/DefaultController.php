<?php

namespace SaltId\ElasticSearchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        return new Response('Hello world from elastic_search');
    }
}
