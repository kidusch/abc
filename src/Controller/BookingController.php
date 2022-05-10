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
     * @Route("/booking", name="app_booking", methods={"GET"})
     */
    public function index(): Response
    {
        $bookings = $this->bookingRepository->findAppointements();
        dd($bookings);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/BookingController.php',
        ]);
    }
}
