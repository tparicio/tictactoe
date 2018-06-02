<?php
// src/Controller/HomeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * show home view
     *
     * @return Response
     * @Route("/{_locale}")
     */
    public function index(Request $request)
    {
        return $this->render('home/index.html.twig', []);
    }
}
