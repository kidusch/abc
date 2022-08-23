<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;

class BookingController extends AbstractController
{
    private $bookingRepository;

    public  function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * @Route("/services", name="app_services", methods={"GET"})
     */
    public function services(): Response
    {
        $services = $this->bookingRepository->fetchServices();
        //dd($bookings);
        return $this->json($services);
    }

    /**
     * @Route("/appointments/{id}", name="app_appointments", methods={"GET"})
     */
    public function appointments(int $id): Response
    {
        $appointments = $this->bookingRepository->activeAppointments($id);
        //dd($bookings);
        return $this->json($appointments);
    }

    /**
     * @Route("/allappointments/", name="all_appointments", methods={"GET"})
     */
    public function allAppointments() : Response { 
        $appointments = $this->bookingRepository->allActiveAppointments();
        //dd($bookings);
        return $this->json($appointments);
    }

    /**
     * @Route("/freeweekdays/", name="freeweekdays", methods={"GET"})
     */
    public function freeWeekdays() : Response { 
        $appointments = $this->bookingRepository->freeWeekdays();
        //dd($bookings);
        return $this->json($appointments);
    }

    /**
     * @Route("/workingtime/", name="workingtime", methods={"GET"})
     */
    public function workingTime() : Response { 
        $appointments = $this->bookingRepository->fetchWorkingTime();
        //dd($bookings);
        return $this->json($appointments);
    }

    /**
     * @Route("/daysoff/", name="daysoff", methods={"GET"})
     */
    public function barbersDaysoff() : Response { 
        $appointments = $this->bookingRepository->barbersDaysoff();
        //dd($bookings);
        return $this->json($appointments);
    }

    /**
     * @Route("/historyappointments/{id}", name="app_historyappointments", methods={"GET"})
     */
    public function historyAppointements(int $id): Response
    {
        $historyappointments = $this->bookingRepository->historyAppointments($id);
        //dd($bookings);
        return $this->json($historyappointments);
    }

    /**
     * @Route("/bookappointments", name="app_bookappointments", methods={"POST"})
     */
    public function bookAppointments(Request $request ): Response{
        $request_data = json_decode($request->getContent(), true);

        $result = $this->bookingRepository->bookAppointments($request_data["bookingStart"], $request_data["bookingEnd"], $request_data["serviceId"], $request_data["providerId"], $request_data["servicePrice"], $request_data["customerId"]);

        return $this->json(['Success' => 'Appointment Successfully Booked!']);
    }
}
