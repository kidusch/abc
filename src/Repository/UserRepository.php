<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository 
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function checkEmail(string $email)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * 
        FROM wp_795628_amelia_users 
        WHERE email = '$email'";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function fetchId($email){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT id 
        FROM wp_795628_amelia_users 
        WHERE email = '$email'";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function authenticate(string $email, string $password){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * 
        FROM wp_795628_amelia_users 
        WHERE email = '$email' AND password = '$password'";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function register($first_name, $last_name, $phone_num, $email, $password){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "INSERT INTO wp_795628_amelia_users (status, type, firstName, lastName, phone, email, password) 
        VALUES ('visible', 'customer', '$first_name', '$last_name', '$phone_num', '$email', '$password')";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $conn->close();
        return $resultSet;
    }

    public function updateUser($first_name, $last_name, $phone_num, $email, $password){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "UPDATE wp_795628_amelia_users 
        SET firstName = '$first_name', lastName = '$last_name', phone = '$phone_num', password = '$password' WHERE email = '$email'";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $conn->close();
        return $resultSet;
    }

    public function updatePassword(string $email, string $password)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "UPDATE wp_795628_amelia_users SET password = '$password' WHERE email = '$email'";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $conn->close();
    }

    public function fetchBarbers(){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM wp_795628_amelia_users WHERE wp_795628_amelia_users.type = 'provider'";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $conn->close();
        return $resultSet->fetchAllAssociative();
    }

    public function fetchUser($email){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM wp_795628_amelia_users WHERE wp_795628_amelia_users.email = '$email'";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $conn->close();
        return $resultSet->fetchAllAssociative();
    }

    public function deleteUser($email){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "DELETE FROM wp_795628_amelia_users WHERE wp_795628_amelia_users.email = '$email'";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $conn->close();
    }

    public function cancelAppointment($id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "DELETE FROM wp_795628_amelia_appointments WHERE id = '$id'";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $conn->close();
    }
}
