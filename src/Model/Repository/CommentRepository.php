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
        $commentQuery=$this->databaseConnection->getConnection()->prepare("SELECT c.id,c.content,c.idAuthor,u.name,u.firstname,c.creationDate,c.idPost,c.status FROM comment c LEFT JOIN user u ON c.idAuthor=u.id WHERE c.id=:id AND c.status=:status");
        $commentQuery->execute($criteria);
        $data=$commentQuery->fetch(\PDO::FETCH_ASSOC);

        $comment = new Comment();
        if ($data === null || $data === false)
        {
            return null;
        }
        else
        {
            $comment->fromArray($data);
            return $comment;
        }
    }
    public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): ?array
    {
        $commentsPostQuery=$this->databaseConnection->getConnection()->prepare("SELECT c.id,c.content,c.idAuthor,u.name,u.firstname,c.creationDate,c.idPost,c.status FROM comment c LEFT JOIN user u ON c.idAuthor=u.id WHERE c.idPost = :id_post AND c.status = :status ORDER BY creationDate DESC");
        $commentsPostQuery->execute($criteria);
        $data=$commentsPostQuery->fetchAll(\PDO::FETCH_ASSOC);

        if ($data === null)
        {
            return null;
        }

        $comments=[];
        foreach ($data as $arrayComment)
        {
            $comment = new Comment();
            $comment->fromArray($arrayComment);
            $comments[] = $comment;
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
        foreach ($data as $arrayComment)
        {
            $comment = new Comment();
            $comment->fromArray($arrayComment);
            $comments[] = $comment;
        }
        return $comments;

    }
    public function create(object $entity): bool
    {
        $addCommentQuery=$this->databaseConnection->getConnection()->prepare("INSERT INTO comment (content, idAuthor, creationDate, idPost)
        VALUES (:content, :idAuthor, :creationDate, :idPost)");
        $creationDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $creationDate = $creationDate->format('Y-m-d H:i:s');

        $addCommentQuery->bindValue(':content',htmlspecialchars($entity->getContent()));
        $addCommentQuery->bindValue(':idAuthor',$entity->getIdAuthor());
        $addCommentQuery->bindValue(':creationDate',$creationDate);
        $addCommentQuery->bindValue(':idPost',$entity->getIdPost());
//        $addCommentQuery->execute();

        if ($addCommentQuery->execute())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function update(object $entity): bool
    {
        $updateCommentQuery = $this->databaseConnection->getConnection()->prepare("UPDATE comment SET status = :commentStatus WHERE id = :id");
        $updateCommentQuery->bindValue(':commentStatus',$entity->getStatus());
        $updateCommentQuery->bindValue(':id',$entity->getId());
        if ($updateCommentQuery->execute())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function delete(object $entity): bool
    {
        return false;
    }

    public function count(): int
    {
        $countQuery = $this->databaseConnection->getConnection()->prepare("SELECT idPost, COUNT(id) as Total 
        FROM comment
        WHERE STATUS='valided'
        GROUP BY idPost");
        $countQuery->execute();
        $data=$countQuery->fetch(\PDO::FETCH_ASSOC);

        return $data;
    }
}

