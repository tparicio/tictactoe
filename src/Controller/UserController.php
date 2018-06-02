<?php
// src/Controller/UserController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
  /**
   * show login
   *
   * @param  Request $request [description]
   * @return Response
   */
  public function login(Request $request) {
      // call authentication service
      $authenticationUtils = $this->get('security.authentication_utils');

      // get login error
      $error = $authenticationUtils->getLastAuthenticationError();

      // last username login try
      $lastUsername = $authenticationUtils->getLastUsername();

      return $this->render(
              'user/login.html.twig', array(
                  'last_username' => $lastUsername,
                  'error' => $error,
              ));
  }
}
