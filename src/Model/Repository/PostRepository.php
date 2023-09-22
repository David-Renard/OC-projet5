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

    private function where(array $criteria = []): ?string
    {
        $sCriteria = '';
        $countCriteria = 0;
        foreach ($criteria as $key => $value) {
            $countCriteria++;
            if ($countCriteria === 1) {
                $sCriteria = " WHERE p.$key = :$key";
            } else {
                $sCriteria = $sCriteria . " AND p.$key = :$key";
            }
        }
        return $sCriteria;
    }

    private function orderBy(array $criteria = null): ?string
    {
        $sCriteria = '';
        $countCriteria = 0;
        if ($criteria != null) {
            foreach ($criteria as $key => $value) {
                $countCriteria++;
                if ($countCriteria === 1) {
                    $sCriteria = " ORDER BY p.$key $value";
                } else {
                    $sCriteria = $sCriteria . " AND p.$key $value";
                }
            }
        }
        return $sCriteria;
    }

    private function setLimit(int $limit = null): string
    {
        $sLimit = "";
        if ($limit != null) {
            $sLimit = " LIMIT $limit";
        }
        return $sLimit;
    }

    private function setOffset(int $offset = null): string
    {
        $sOffset = "";
        if ($offset != null) {
            $sOffset = " OFFSET $offset";
        }
        return $sOffset;
    }

    public function find(int $id): ?Post
    {
        $postQuery = $this->databaseConnection->getConnection()->prepare("SELECT p.id, p.title, p.creationDate, p.lede, p.content, p.lastUpdateDate, p.idAuthor, u.name, u.firstname 
    FROM post p 
    LEFT JOIN user u 
    ON p.idAuthor=u.id 
    WHERE p.id = :id");
        $postQuery->bindValue(':id', $id, \PDO::PARAM_INT);
        $postQuery->execute();
        $data = $postQuery->fetch(\PDO::FETCH_ASSOC);

        $post = new Post();
        if ($data === null || $data === false) {
            return null;
        } else {
            $post->fromArray($data);
            return $post;
        }
    }

    public function findOneBy(array $criteria, array $orderBy = null): ?Post
    {
        $query = "SELECT p.id, p.title, p.creationDate, p.lede, p.content, p.lastUpdateDate, p.idAuthor, u.name, u.firstname 
FROM post p 
    LEFT JOIN user u 
        ON p.idAuthor=u.id";
        $where = $this->where($criteria);

        $concatenatedQuery = $query . $where;
        $postQuery = $this->databaseConnection->getConnection()->prepare($concatenatedQuery);
        foreach ($criteria as $key => $value) {
            $param_type = is_string($value) ? \PDO::PARAM_STR : \PDO::PARAM_INT;
            $postQuery->bindValue(":$key", $value, $param_type);
        }
        $postQuery->execute();
        $data = $postQuery->fetch(\PDO::FETCH_ASSOC);
        $post = new Post();
        if ($data === null || $data === false) {
            return null;
        } else {
            $post->fromArray($data);
            return $post;
        }
    }

    public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): ?array
    {
        $query = 'SELECT p.id, p.title, p.creationDate, p.lede, p.content, p.lastUpdateDate, p.idAuthor, u.name, u.firstname
    FROM post p
    LEFT JOIN user u
    ON p.idAuthor = u.id';
        $where = $this->where($criteria);
        $sortBy = $this->orderBy($orderBy);
        $sLimit = $this->setLimit($limit);
        $sOffset = $this->setOffset($offset);

        $concatenatedQuery = $query . $where . $sortBy . $sLimit . $sOffset;
        $publishedPostsQuery = $this->databaseConnection->getConnection()->prepare($concatenatedQuery);

        foreach ($criteria as $key => $value) {
            $param_type = is_string($value) ? \PDO::PARAM_STR : \PDO::PARAM_INT;
            $publishedPostsQuery->bindValue(":$key", $value, $param_type);
        }

        $publishedPostsQuery->execute();
//        $data = $publishedPostsQuery->fetchAll(\PDO::FETCH_CLASS,"Post");
        $data = $publishedPostsQuery->fetchAll(\PDO::FETCH_ASSOC);

        if ($data === null) {
            return null;
        }

        $posts = [];
        foreach ($data as $arrayPost) {
            $post = new Post();
            $post->fromArray($arrayPost);
            $posts[] = $post;
        }
        return $posts;
    }

    public function findAll(int $limit = null, int $offset = null): ?array
    {
        $query = "SELECT p.id, p.title, p.creationDate, p.lede, p.content, p.lastUpdateDate, p.idAuthor, u.name, u.firstname 
    FROM post p 
    LEFT JOIN user u 
    ON p.idAuthor=u.id
    ORDER BY p.creationDate DESC";
        $sLimit = $this->setLimit($limit);
        $sOffset = $this->setOffset($offset);
        $concatenatedQuery = $query . $sLimit . $sOffset;

        $postsQuery = $this->databaseConnection->getConnection()->prepare($concatenatedQuery);
        $postsQuery->execute([]);
        $data = $postsQuery->fetchAll(\PDO::FETCH_ASSOC);

        if ($data === null) {
            return null;
        }

        $posts = [];
        foreach ($data as $arrayPost) {
            $post = new Post();
            $post->fromArray($arrayPost);
            $posts[] = $post;
        }
        return $posts;
    }

    public function create(object $entity): bool
    {
        $addPostQuery = $this->databaseConnection->getConnection()->prepare("INSERT INTO post 
        (title, creationDate, lede, content, lastUpdateDate, idAuthor)
        VALUES (:title, :creationDate, :lede, :content, :lastUpdateDate, :idAuthor)");
        $creationDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $creationDate = $creationDate->format('Y-m-d H:i:s');

        $addPostQuery->bindValue(':title', htmlspecialchars($entity->getTitle()));
        $addPostQuery->bindValue(':idAuthor', $entity->getIdAuthor(), \PDO::PARAM_INT);
        $addPostQuery->bindValue(':creationDate', $creationDate);
        $addPostQuery->bindValue(':lastUpdateDate', $creationDate);
        $addPostQuery->bindValue(':lede', $entity->getLede());
        $addPostQuery->bindValue(':content', $entity->getContent());

        return $addPostQuery->execute();
    }

    public function update(object $entity): bool
    {
        $updateQuery = $this->databaseConnection->getConnection()->prepare("UPDATE post
        SET title = :title, lede = :lede, content = :content, lastUpdateDate = :lastUpdateDate, idAuthor = :idAuthor
        WHERE id = :id");
        $updateDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $updateDate = $updateDate->format('Y-m-d H:i:s');

        $updateQuery->bindValue(':title', $entity->getTitle());
        $updateQuery->bindValue(':lede', $entity->getLede());
        $updateQuery->bindValue(':content', $entity->getContent());
        $updateQuery->bindValue(':lastUpdateDate', $updateDate);
        $updateQuery->bindValue(':idAuthor', $entity->getIdAuthor(), \PDO::PARAM_INT);
        $updateQuery->bindValue(':id', $entity->getId(), \PDO::PARAM_INT);

        return $updateQuery->execute();
    }

    public function delete(object $entity): bool
    {
        $deleteQuery = $this->databaseConnection->getConnection()->prepare("DELETE FROM post
        WHERE id = :id");
        $deleteQuery->bindValue(':id', $entity->getId());

        return $deleteQuery->execute();
    }

    public function count(): int
    {
        $countQuery = $this->databaseConnection->getConnection()->prepare("SELECT COUNT(id) as Total 
        FROM post");
        $countQuery->execute();
        $data = $countQuery->fetch(\PDO::FETCH_NUM)[0];
        return $data;
    }
}

