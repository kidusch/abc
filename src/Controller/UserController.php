<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;

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
    public function signup(Request $request): Response
    {
        $request_data = json_decode($request->getContent(), true);
        $user_data = $this->userRepository->checkEmail($request_data["email"]);
        if (empty($user_data)) {
            $r = $this->userRepository->register($request_data["firstName"], $request_data["lastName"], $request_data["phoneNo"], $request_data["email"], $request_data["password"]);
            $result = $this->userRepository->fetchId($request_data["email"]);
            return $this->json(['Success' => 'Successfuly Registered.', 'user_id' => $result[0]["id"]]);
        } else {
            return $this->json(['Failure' => 'Email already exists!']);
        }
    }

    /**
     * @Route("/updateUser", name="update", methods={"POST"})
     */
    public function updateUser(Request $request): Response
    {
        $request_data = json_decode($request->getContent(), true);
        $user_data = $this->userRepository->checkEmail($request_data["email"]);
        if (!empty($user_data)) {
            $r = $this->userRepository->updateUser($request_data["firstName"], $request_data["lastName"], $request_data["phoneNo"], $request_data["email"], $request_data["password"]);
            $result = $this->userRepository->fetchId($request_data["email"]);
            return $this->json(['Success' => 'Successfuly Updated.', 'user_id' => $result[0]["id"]]);
        } else {
            return $this->json(['Failure' => 'User Does not exist!']);
        }
    }

    /**
     * @Route("/barbers", name="fetch_barbers", methods={"GET"})
     * lists the barbers
     */
    public function fetchBarbers(): Response
    {
        $result = $this->userRepository->fetchBarbers();

        return $this->json($result);
    }

    /**
     * @Route("/delete", name="delete_user", methods={"POST"})
     */
    public function deleteUser(Request $request): Response
    {
        $request_data = json_decode($request->getContent(), true);
        $email = $request_data["email"];
        $result = $this->userRepository->deleteUser($email);

        return $this->json($result);
    }

    /**
     * @Route("/user", name="fetch_user", methods={"POST"})
     */
    public function fetchUserInfo(Request $request): Response
    {
        $request_data = json_decode($request->getContent(), true);
        $email = $request_data["email"];

        $result = $this->userRepository->fetchUser($email);

        return $this->json($result);
    }

    /**
     * @Route("/cancel", name="cancel_appointment", methods={"POST"})
     */
    public function cancelAppointment(Request $request): Response
    {
        $request_data = json_decode($request->getContent(), true);
        $id = $request_data["id"];
        $result = $this->userRepository->cancelAppointment($id);

        return $this->json($result);
    }

    /**
     * @Route("/forget", name="forget_password", methods={"POST"})
     */
    public function forgetPassword(Request $request, MailerInterface $mailer): Response
    {
        $transport = Transport::fromDsn('smtp://localhost');
        $mailer = new Mailer($transport);
        $request_data = json_decode($request->getContent(), true);
        $email = $request_data["email"];
        //dd($email);
        $check = $this->userRepository->fetchUser($email);
        if ($check){
            $email = (new Email())
            ->from('info@abc-barber.ch')
            ->to($email)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('ABC Barber - Réinitialiserr mot de passe')
            ->text('ABC Barber - Réinitialiser mot de passe')
            ->html("<h1>Réinitialiser ton mot de passe</h1></br></br><p>Clique sur le lien pour réinitialiser ton mot de passe: https://api.abc-barber.ch/forget/".$email);

            $mailer->send($email);
        } else {
            $email = (new Email())
            ->from('info@abc-barber.ch')
            ->to($email)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject("Couldn't find your email")
            ->text("Couldn't find your email in our Database. Please sign up if you have an account yet!")
            ->html("<p>Couldn't find your email in our Database. Please sign up if you have an account yet!</p>");

            $mailer->send($email);
        }
        
        return $this->json($email);
    }


    /**
     * @Route("/email", name="email")
     */
    public function email( MailerInterface $mailer): Response
    {
        $transport = Transport::fromDsn('smtp://localhost');
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from('info@abc-barber.ch')
            ->to('kidus.dejen@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
    }

    
}
