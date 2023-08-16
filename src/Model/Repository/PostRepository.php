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
        $postQuery=$this->databaseConnection->getConnection()->prepare("SELECT * FROM post WHERE ID=:ID");
        $postQuery->execute($criteria);
        $data=$postQuery->fetch(\PDO::FETCH_ASSOC);

        return ($data === null || $data === false) ? null : new Post(
            (int)$data['ID'],
            $data['title'],
            $data['creationDate'],
            $data['lede'],
            $data['content'],
            $data['lastUpdateDate'],
            $data['idAuthor'],
            $data['status']);
    }
    public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): ?array
    {
        //$publishedPostsQuery=$this->databaseConnection->getConnection()->prepare('SELECT * FROM post WHERE status = :status');
        $publishedPostsQuery=$this->databaseConnection->getConnection()->prepare('SELECT * FROM post ORDER BY lastUpdateDate DESC');
        $publishedPostsQuery->execute();
        $data=$publishedPostsQuery->fetchAll(\PDO::FETCH_ASSOC);

        if ($data === null)
        {
            return null;
        }

        $posts=[];
        foreach ($data as $post)
        {
            $posts[]=new Post(
                (int)$post['ID'],
                $post['title'],
                $post['creationDate'],
                $post['lede'],
                $post['content'],
                $post['lastUpdateDate'],
                $post['idAuthor'],
                $post['status']);
        }
        return $posts;
    }
    public function findAll(): ?array
    {
        $postsQuery=$this->databaseConnection->getConnection()->prepare("SELECT * FROM post ORDER BY creation_date DESC");
        $postsQuery->execute();
        $data=$postsQuery->fetchAll(\PDO::FETCH_ASSOC);

        if ($data === null)
        {
            return null;
        }

        $posts=[];
        foreach ($data as $post)
        {
            $posts[]=new Post(
                (int)$post['ID'],
                $post['title'],
                $post['creationDate'],
                $post['lede'],
                $post['content'],
                $post['lastUpdateDate'],
                $post['idAuthor'],
                $post['status']);
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

