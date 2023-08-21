<?php

declare(strict_types=1);
namespace App\Model\Repository;

use App\Model\Entity\Comment;
use App\Service\DatabaseConnection;
use App\Model\Repository\Interfaces\EntityRepositoryInterface;

class CommentRepository implements EntityRepositoryInterface
{
    public function __construct(private DatabaseConnection $databaseConnection)
    {
    }
    public function find(int $id): ?Comment
    {
        return null;
    }
    public function findOneBy(array $criteria, array $orderBy = null): ?Comment
    {
        $commentQuery=$this->databaseConnection->getConnection()->prepare("SELECT * FROM comment WHERE ID=:ID AND status=:status");
        $commentQuery->execute($criteria);
        $data=$commentQuery->fetch(\PDO::FETCH_ASSOC);

        return ($data === null || $data === false) ? null : new Comment(
            (int) $data['ID'],
            $data['content'],
            $data['idAuthor'],
            $data['creationDate'],
            $data['idPost'],
            $data['status'],
        );
    }
    public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): ?array
    {
        $commentsPostQuery=$this->databaseConnection->getConnection()->prepare("SELECT * FROM comment WHERE IDPost = :id_post AND status = :status");
        $commentsPostQuery->execute($criteria);
        $data=$commentsPostQuery->fetchAll(\PDO::FETCH_ASSOC);

        if ($data === null)
        {
            return null;
        }

        $comments=[];
        foreach ($data as $comment)
        {
            $comments[]= new Comment(
                (int) $comment['ID'],
                $comment['content'],
                $comment['idAuthor'],
                $comment['creationDate'],
                $comment['idPost'],
                $comment['status'],
            );
        }
        return $comments;
    }
    public function findAll(): ?array
    {
        $commentsQuery=$this->databaseConnection->getConnection()->prepare("SELECT * FROM comment ORDER BY creation_date DESC");
        $commentsQuery->execute();
        $data=$commentsQuery->fetchAll(\PDO::FETCH_ASSOC);

        if ($data === null)
        {
            return null;
        }

        $comments=[];
        foreach ($data as $comment)
        {
            $comments[]=new Comment(
                (int) $comment['ID'],
                $comment['content'],
                $comment['idAuthor'],
                $comment['creationDate'],
                $comment['idPost'],
                $comment['status']
            );
        }
        return $comments;

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

