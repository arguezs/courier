<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController {

    /**
     * @Route("/error/{errorCode}", name="error")
     * @param $errorCode
     * @return Response
     */
    public function error($errorCode){
        switch ($errorCode) {
            case 401:
                $error = 'Unauthorized';
                break;
            case 403:
                $error = 'Forbbiden';
                break;
            case 404:
                $error = 'Not found';
                break;
            default:
                $error = 'Unknown';
                break;
        }

        return $this->render('error/error.html.twig', [ 'error' => $error ]);

    }
}