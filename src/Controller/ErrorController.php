<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController {

    /**
     * Generates the Error page. The error shown depends on the code passed.
     *
     * @Route("/error/{errorCode}", name="error")
     * @param $errorCode
     * @return Response
     */
    public function error($errorCode = 0){
        switch ($errorCode) {
            case 401:
                $error = 'Unauthorized';
                break;
            case 403:
                $error = 'Forbidden';
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