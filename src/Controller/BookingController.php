<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookingRepository;


class BookingController extends AbstractController
{
    private $bookingRepository;

    public  function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * @Route("/services", name="app_services", methods={"POST"})
     */
    public function services(): Response
    {
        $services = $this->bookingRepository->findServices();
        //dd($bookings);
        return $this->json([
            'services' => $services
        ]);
    }

    /**
     * @Route("/appointments", name="app_appointments", methods={"POST"})
     */
    public function appointments(): Response
    {
        $appointments = $this->bookingRepository->activeAppointements();
        //dd($bookings);
        return $this->json([
            'appointments' => $appointments
        ]);
    }

    /**
     * @Route("/historyappointments", name="app_historyappointments", methods={"POST"})
     */
    public function historyAppointements(): Response
    {
        $historyappointments = $this->bookingRepository->historyAppointements();
        //dd($bookings);
        return $this->json([
            'historyappointments' => $historyappointments
        ]);
    }

    /**
     * @Route("/login/{email}/{password}", name="app_login", methods={"POST"})
     */
    public function login($email, $password): Response
    {
        $check = $this->bookingRepository->checkEmail($email);
        if (empty($check)){
            //sign up
        } else {
            if($check[0]['password'] === null) {
                dd($check[0]['password']);
                //Update password for existing account
            }
            //log in
            
        }
        return $this->json([
            'email' => $email,
            'password' => $password,
            'check' => empty($check)
        ]);
    }
}
