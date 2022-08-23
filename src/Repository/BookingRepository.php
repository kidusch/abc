<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function add(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function fetchServices()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM wp_795628_amelia_services";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function activeAppointments($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * 
        FROM wp_795628_amelia_customer_bookings INNER JOIN wp_795628_amelia_appointments ON wp_795628_amelia_customer_bookings.appointmentId=wp_795628_amelia_appointments.id 
        WHERE wp_795628_amelia_appointments.bookingStart >= NOW() and wp_795628_amelia_customer_bookings.customerId = ${id}";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function allActiveAppointments()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * 
        FROM wp_795628_amelia_customer_bookings INNER JOIN wp_795628_amelia_appointments ON wp_795628_amelia_customer_bookings.appointmentId=wp_795628_amelia_appointments.id 
        WHERE wp_795628_amelia_appointments.bookingStart";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function freeWeekdays()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT weekday_off 
        FROM wp_795628_amelia_daysoff";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function barbersDaysoff()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * 
        FROM wp_795628_amelia_providers_to_daysoff WHERE wp_795628_amelia_providers_to_daysoff.endDate <= DATE(NOW());";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function fetchWorkingTime()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM `wp_795628_amelia_working _time`";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function historyAppointments($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * 
        FROM wp_795628_amelia_customer_bookings INNER JOIN wp_795628_amelia_appointments ON wp_795628_amelia_customer_bookings.appointmentId=wp_795628_amelia_appointments.id 
        WHERE wp_795628_amelia_appointments.bookingStart < NOW() and wp_795628_amelia_customer_bookings.customerId = ${id}";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function bookAppointments($booking_start, $booking_end, $service_id, $provider_id, $service_price, $customer_id){
        $conn = $this->getEntityManager()->getConnection();
        //statement 1
        $sql = "INSERT INTO wp_795628_amelia_appointments (status, bookingStart, bookingEnd, notifyParticipants, serviceId, providerId) 
        VALUES ('approved', '$booking_start', '$booking_end', 1 , '$service_id', '$provider_id')";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        
        //statement 2
        $sql1 = "SELECT MAX(id) FROM wp_795628_amelia_appointments";
        $stmt1 = $conn->prepare($sql1);
        $resultSet1 = $stmt1->executeQuery();
        $result1 = $resultSet1->fetchAllAssociative();
        $appointment_id = $result1[0]["MAX(id)"];
        
        //statement 3
        $sql2 = "INSERT INTO wp_795628_amelia_customer_bookings (appointmentId, customerId, status, price, persons, token, aggregatedPrice) 
        VALUES ('$appointment_id', '$customer_id', 'approved', '$service_price', 1, TIME(NOW()), 1)";
        $stmt2 = $conn->prepare($sql2);
        $resultSet2 = $stmt2->executeQuery();
        $result2 = $resultSet2->fetchAllAssociative();

        return $result1;
    }
    
//    /**
//     * @return Booking[] Returns an array of Booking objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Booking
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
