<?php

declare(strict_types=1);
namespace App\Model\Repository;

use App\Model\Entity\Post;
use App\Service\DatabaseConnection;
use App\Model\Repository\Interfaces\EntityRepositoryInterface;

class PostRepository implements EntityRepositoryInterface
{
    public function __construct(private DatabaseConnection $databaseConnection)
    {
    }

    public function find(int $id): ?Post
    {
        $postQuery = $this->databaseConnection->getConnection()->prepare("SELECT p.id, p.title, p.creationDate, p.lede, p.content, p.lastUpdateDate, p.idAuthor, u.name, u.firstname, p.status FROM post p LEFT JOIN user u on p.idAuthor=u.id WHERE p.id = $id");
        $postQuery->execute();
        $data=$postQuery->fetch(\PDO::FETCH_ASSOC);

        $post = new Post();
        if ($data === null || $data === false)
        {
            return null;
        }
        else
        {
            $post->fromArray($data);
            return $post;
        }
    }

    public function findOneBy(array $criteria, array $orderBy = null): ?Post
    {
        $postQuery=$this->databaseConnection->getConnection()->prepare("SELECT p.id, p.title, p.creationDate, p.lede, p.content, p.lastUpdateDate, p.idAuthor, u.name, u.firstname, p.status FROM post p LEFT JOIN user u on p.idAuthor=u.id WHERE p.id = :id");
        $postQuery->execute($criteria);
        $data=$postQuery->fetch(\PDO::FETCH_ASSOC);

        $post = new Post();
        if ($data === null || $data === false)
        {
            return null;
        }
        else
        {
            $post->fromArray($data);
            return $post;
        }
    }

    public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): ?array
    {
        if ($limit == null)
        {
            $sLimit='';
        }
        else
        {
            $sLimit = ' LIMIT ' . $limit;
        }
        if ($offset == null)
        {
            $sOffset='';
        }
        else
        {
            $sOffset = ' OFFSET ' . $offset;
        }

        $query = 'SELECT p.id, p.title, p.creationDate, p.lede, p.content, p.lastUpdateDate, p.idAuthor, u.name, u.firstname, p.status
    FROM post p
    LEFT JOIN user u
    ON p.idAuthor=u.id
    WHERE p.status = :status
    ORDER BY p.creationDate DESC';
        $publishedPostsQuery=$this->databaseConnection->getConnection()->prepare($query . $sLimit . $sOffset);
        $publishedPostsQuery->execute($criteria);
        $data=$publishedPostsQuery->fetchAll(\PDO::FETCH_ASSOC);

        if ($data === null)
        {
            return null;
        }

        $posts=[];
        foreach ($data as $arrayPost)
        {
            $post = new Post();
            $post->fromArray($arrayPost);
            $posts[] = $post;
        }
        return $posts;
    }

    public function findAll(): ?array
    {
        $postsQuery=$this->databaseConnection->getConnection()->prepare("SELECT  p.id, p.title, p.creationDate, p.lede, p.content, p.lastUpdateDate, p.idAuthor, u.name, u.firstname, p.status FROM post p LEFT JOIN user u ON p.idAuthor=u.id ORDER BY creationDate DESC");
        $postsQuery->execute();
        $data=$postsQuery->fetchAll(\PDO::FETCH_ASSOC);

        if ($data === null)
        {
            return null;
        }

        $posts=[];
        foreach ($data as $arrayPost)
        {
            $post = new Post();
            $post->fromArray($arrayPost);
            $posts[] = $post;
        }
        return $posts;
    }

    public function create(object $entity): bool
    {
        $addPostQuery=$this->databaseConnection->getConnection()->prepare("INSERT INTO post 
        (title, creationDate, lede, content, lastUpdateDate, idAuthor, status)
        VALUES (:title, :creationDate, :lede, :content, :lastUpdateDate, :idAuthor, :status)");
        $creationDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $creationDate = $creationDate->format('Y-m-d H:i:s');

        $addPostQuery->bindValue(':title',htmlspecialchars($entity->getTitle()));
        $addPostQuery->bindValue(':idAuthor',$entity->getIdAuthor());
        $addPostQuery->bindValue(':creationDate',$creationDate);
        $addPostQuery->bindValue(':lastUpdateDate',$creationDate);
        $addPostQuery->bindValue(':lede',$entity->getLede());
        $addPostQuery->bindValue(':status',$entity->getStatus());
        $addPostQuery->bindValue(':content',$entity->getContent());

        if ($addPostQuery->execute())
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
        $updateQuery = $this->databaseConnection->getConnection()->prepare("UPDATE post
        SET title = :title, lede = :lede, content = :content, lastUpdateDate = :lastUpdateDate, status = :status
        WHERE id = :id");
        $updateDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $updateDate = $updateDate->format('Y-m-d H:i:s');

        $updateQuery->bindValue(':title', $entity->getTitle());
        $updateQuery->bindValue(':lede', $entity->getLede());
        $updateQuery->bindValue(':content', $entity->getContent());
        $updateQuery->bindValue(':status', $entity->getStatus());
        $updateQuery->bindValue(':lastUpdateDate', $updateDate);
        $updateQuery->bindValue(':id', $entity->getId());
        if ($updateQuery->execute())
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
        $countQuery = $this->databaseConnection->getConnection()->prepare("SELECT COUNT(id) as Total 
        FROM post
        WHERE STATUS='published'");
        $countQuery->execute();
        $data=$countQuery->fetch(\PDO::FETCH_NUM)[0];
        return $data;
    }
}

