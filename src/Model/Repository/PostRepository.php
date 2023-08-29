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
        return null;
    }
    public function findOneBy(array $criteria, array $orderBy = null): ?Post
    {
        $postQuery=$this->databaseConnection->getConnection()->prepare("SELECT * FROM post WHERE ID = :ID");
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
        $publishedPostsQuery=$this->databaseConnection->getConnection()->prepare('SELECT * FROM post WHERE STATUS = :status ORDER BY creationDate DESC');
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
        $postsQuery=$this->databaseConnection->getConnection()->prepare("SELECT * FROM post ORDER BY creationDate DESC");
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

