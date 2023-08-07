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
        $commentQuery=$this->databaseConnection->getConnection()->prepare("SELECT * FROM comment WHERE ID=:ID");
        $commentQuery->execute($criteria);
        $data=$commentQuery->fetch(\PDO::FETCH_ASSOC);

        return ($data === null || $data === false) ? null : new Comment(
            (int)$data['ID'],
            $data['content'],
            $data['id_author'],
            $data['creation_date'],
            $data['id_post'],
            $data['status'],
            $data['id_evaluator']);
    }
    public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): ?array
    {
        return null;
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
                (int)$comment['ID'],
                $comment['content'],
                $comment['id_author'],
                $comment['creation_date'],
                $comment['id_post'],
                $comment['status'],
                $comment['id_evaluator']);
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

