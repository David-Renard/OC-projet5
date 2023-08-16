<?php
declare(strict_types=1);
namespace App\Model\Entity;

class Comment
{
    public function __construct(
        private int $id,
        private string $content,
        private int $idAuthor,
        private string $creationDate,
        private int $idPost,
        private string $status,
    )
    {
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function setContent(string $content): self
    {
        $this->content=$content;
        return $this;
    }
    public function getIdAuthor(): int
    {
        return $this->idAuthor;
    }
    public function setIdAuthor(int $idAuthor): self
    {
        $this->idAuthor=$idAuthor;
        return $this;
    }
    public function getCreationDate(): string
    {
        return $this->creationDate;
    }
    public function setCreationDate(string $creationDate): self
    {
        $this->creationDate=$creationDate;
        return $this;
    }
    public function getIDPost(): int
    {
        return $this->idPost;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function setStatus(string $status): self
    {
        $this->status=$status;
        return $this;
    }
}