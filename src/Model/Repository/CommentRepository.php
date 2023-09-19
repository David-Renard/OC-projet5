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

    private function where(array $criteria = []): ?string
    {
        $sCriteria = '';
        $countCriteria = 0;
        foreach ($criteria as $key => $value)
        {
            $countCriteria++;
            if ($countCriteria === 1)
            {
                $sCriteria = " WHERE c.$key = :$key";
            }
            else
            {
                $sCriteria = $sCriteria . " AND c.$key = :$key";
            }
        }
        return $sCriteria;
    }

    private function orderBy(array $criteria = null): ?string
    {
        $sCriteria = '';
        $countCriteria = 0;
        if ($criteria != null)
        {
            foreach ($criteria as $key => $value)
            {
                $countCriteria++;
                if ($countCriteria === 1)
                {
                    $sCriteria = " ORDER BY c.$key $value";
                }
                else
                {
                    $sCriteria = $sCriteria . " AND c.$key $value";
                }
            }
        }
        return $sCriteria;
    }

    public function find(int $id): ?Comment
    {
        return null;
    }

    public function findOneBy(array $criteria, array $orderBy = null): ?Comment
    {
        $query = "SELECT c.id,c.content,c.idAuthor,u.name,u.firstname,c.creationDate,c.idPost,c.status 
FROM comment c 
    LEFT JOIN user u 
        ON c.idAuthor=u.id";
        $where = $this->where($criteria);
        $sortBy = $this->orderBy($orderBy);

        $concatenatedQuery = $query . $where . $sortBy;
        $commentQuery=$this->databaseConnection->getConnection()->prepare($concatenatedQuery);
        foreach ($criteria as $key => $value)
        {
            $commentQuery->bindValue($key,$value);
        }
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
        $query = "SELECT c.id,c.content,c.idAuthor,u.name,u.firstname,c.creationDate,c.idPost,c.status 
FROM comment c 
    LEFT JOIN user u 
        ON c.idAuthor=u.id";
        $where = $this->where($criteria);
        $sortBy = $this->orderBy($orderBy);

        $concatenatedQuery = $query . $where . $sortBy;
        $commentsPostQuery=$this->databaseConnection->getConnection()->prepare($concatenatedQuery);
        foreach ($criteria as $key => $value)
        {
            $commentsPostQuery->bindValue($key,$value);
        }
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

