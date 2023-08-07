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
        return null;
    }
    public function findOneBy(array $criteria, array $orderBy = null): ?User
    {
        $userQuery=$this->databaseConnection->getConnection()->prepare("SELECT * FROM user WHERE email=:email");
        $userQuery->execute($criteria);
        $data=$userQuery->fetch(\PDO::FETCH_ASSOC);

        return ($data === null || $data === false) ? null : new User(
            (int)$data['ID'],
            $data['name'],
            $data['firstName'],
            $data['email'],
            $data['password'],
            $data['role'],
            $data['status']);
    }
    public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): ?array
    {
        return null;
    }
    public function findAll(): ?array
    {
        $usersQuery=$this->databaseConnection->getConnection()->prepare("SELECT * FROM user");
        $usersQuery->execute();
        $data=$usersQuery->fetchAll(\PDO::FETCH_ASSOC);

        if ($data === null)
        {
            return null;
        }

        $users=[];
        foreach ($data as $user)
        {
            $users[]=new User(
                (int)$user['ID'],
                $user['name'],
                $user['firstName'],
                $user['email'],
                $user['password'],
                $user['role'],
                $user['status']);
        }
        return $users;
        
    }
    public function create(object $entity): bool
    {
        return false;
    }
    public function update(object $entity): bool
    {
        return false;
    }
    public function delete(object $entity): bool
    {
        return false;
    }
}

