<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/login/{email}/{password}", name="app_login", methods={"POST"})
     */
    public function login($email, $password): Response
    {
        $isAuthenticated = $this->userRepository->authenticate(strval($email), strval($password));
        if (empty($isAuthenticated)) {
            $check = $this->userRepository->checkEmail(strval($email));
            if (empty($check)) {
                return $this->json(['Failure' => 'Wrong email or password.']);
            } else {
                if ($check[0]['password'] === null) {
                    //dd($check[0]['password']);
                    //Update password for existing account
                    $this->userRepository->updatePassword(strval($email), strval($password));
                    return $this->json(['Success' => 'Password Updated.']);
                } else {
                    return $this->json(['Failure' => 'Wrong email or password.']);
                }
            }
        } else {
            return $this->json(['Success' => 'Successfuly LoggedIn.']);
        }
    }
}
