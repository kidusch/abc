<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/login", name="app_login", methods={"POST"})
     */
    public function login(Request $request): Response
    {
        $request_data = json_decode($request->getContent(), true);
        $email = $request_data["email"];
        $password = $request_data["password"];

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

    /**
     * @Route("/signup", name="sign_up", methods={"POST"})
     */
    public function signup(Request $request): Response{
        $request_data = json_decode($request->getContent(), true);
        $user_data = $this->userRepository->checkEmail($request_data["email"]);
        if (empty($user_data)){
            $r = $this->userRepository->register($request_data["firstName"], $request_data["lastName"], $request_data["phoneNo"], $request_data["email"], $request_data["password"]);
            $result = $this->userRepository->fetchId($request_data["email"]);
            return $this->json(['Success' => 'Successfuly Registered.', 'user_id' => $result[0]["id"]]);
        }
        else{
            return $this->json(['Failure' => 'Email already exists!']);
        }
    }

    /**
     * @Route("/barbers", name="fetch_barbers", methods={"GET"})
     */
    public function fetchBarbers(): Response{
        $result = $this->userRepository->fetchBarbers();

        return $this->json($result);
    }
}
