<?php

declare(strict_types=1);
namespace App\Model\Repository;

use App\Model\Entity\User;
use App\Service\DatabaseConnection;
use App\Model\Repository\Interfaces\EntityRepositoryInterface;

class UserRepository implements EntityRepositoryInterface
{
    public function __construct(private DatabaseConnection $databaseConnection)
    {
    }

    public function find(int $id): ?User
    {
        $userQuery = $this->databaseConnection->getConnection()->prepare("SELECT * FROM user WHERE id = $id");
        $userQuery->execute();
        $data=$userQuery->fetch(\PDO::FETCH_ASSOC);

        $user = new User();
        if ($data === null || $data === false)
        {
            return null;
        }
        else
        {
            $user->fromArray($data);
            return $user;
        }
    }

    public function findOneBy(array $criteria, array $orderBy = null): ?User
    {
        $userQuery = $this->databaseConnection->getConnection()->prepare("SELECT * FROM user WHERE email=:email");
        $userQuery->execute($criteria);
        $data = $userQuery->fetch(\PDO::FETCH_ASSOC);

        $user = new User();
        if ($data === null || $data === false)
        {
            return null;
        }
        else
        {
            $user->fromArray($data);
            return $user;
        }
    }

    public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): ?array
    {
        $usersQuery = $this->databaseConnection->getConnection()->prepare("SELECT * 
        FROM user
        WHERE role = :role");
        $usersQuery->execute($criteria);
        $data = $usersQuery->fetchAll(\PDO::FETCH_ASSOC);

        if ($data === null)
        {
            return null;
        }

        $users=[];
        foreach ($data as $arrayUser)
        {
            $user = new User();
            $user->fromArray($arrayUser);
            $users[] = $user;
        }
        return $users;
    }

    public function findAll(): ?array
    {
        $usersQuery = $this->databaseConnection->getConnection()->prepare("SELECT * FROM user");
        $usersQuery->execute();
        $data=$usersQuery->fetchAll(\PDO::FETCH_ASSOC);

        if ($data === null)
        {
            return null;
        }

        $users=[];
        foreach ($data as $arrayPost)
        {
            $user= new User();
            $user->fromArray($arrayPost);
            $users[] = $user;
        }
        return $users;
        
    }

    public function create(object $entity): bool
    {
        $registration=$this->databaseConnection->getConnection()->prepare("INSERT INTO user
        (name, firstname, email, password, role)
        VALUES (:name, :firstname, :email, :password, :role)");

        $registration->bindValue(':name',$entity->getName());
        $registration->bindValue(':firstname',$entity->getFirstname());
        $registration->bindValue(':email',$entity->getEmail());
        $registration->bindValue(':password',$entity->getPassword());
        $registration->bindValue(':role',$entity->getRole());

        return $registration->execute();
    }

    public function update(object $entity): bool
    {
        $updateUserQuery = $this->databaseConnection->getConnection()->prepare("UPDATE user 
        SET role = :role
        WHERE id = :id");
        $updateUserQuery->bindValue(':role',$entity->getRole());
        $updateUserQuery->bindValue(':id',$entity->getId());

        return $updateUserQuery->execute();
    }

    public function delete(object $entity): bool
    {
        $deleteUserQuery = $this->databaseConnection->getConnection()->prepare("DELETE FROM user
        WHERE id = :id");
        $deleteUserQuery->bindValue(':id',$entity->getId());

        return $deleteUserQuery->execute();
    }

    public function count(): int
    {
        $countQuery = $this->databaseConnection->getConnection()->prepare("SELECT COUNT(id) as Total 
        FROM user");
        $countQuery->execute();
        $data = $countQuery->fetch(\PDO::FETCH_ASSOC);

        return $data;
    }
}

