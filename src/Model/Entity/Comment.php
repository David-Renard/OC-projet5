<?php
declare(strict_types=1);

namespace App\Model\Entity;

class Comment
{
    private ?int $id;
    private string $content = '';
    private ?int $idAuthor;
    private string $name = '';
    private string $firstname = '';
    private string $creationDate = '';
    private ?int $idPost;
    private string $status;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return html_entity_decode($this->content);
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getIdAuthor(): int
    {
        return $this->idAuthor;
    }

    public function setIdAuthor(int $idAuthor): self
    {
        $this->idAuthor = $idAuthor;
        return $this;
    }

    public function getNameAuthor(): string
    {
        return $this->name;
    }

    public function setNameAuthor(string $nameAuthor): self
    {
        $this->name = $nameAuthor;
        return $this;
    }

    public function getFirstnameAuthor(): string
    {
        return $this->firstname;
    }

    public function setFirstnameAuthor(string $firstnameAuthor): self
    {
        $this->firstname = $firstnameAuthor;
        return $this;
    }

    public function getCreationDate(): string
    {
        return $this->creationDate;
    }

    public function setCreationDate(string $creationDate): self
    {
        $this->creationDate = $creationDate;
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
        $this->status = $status;
        return $this;
    }

    public function setIdPost(int $idPost): void
    {
        $this->idPost = $idPost;
    }

    public function fromArray(array $data): void
    {
        $this->setId((int)$data['id']);
        $this->setContent($data['content']);
        $this->setIdAuthor($data['idAuthor']);
        $this->setNameAuthor($data['name']);
        $this->setFirstnameAuthor($data['firstname']);
        $this->setCreationDate($data['creationDate']);
        $this->setIdPost((int)$data['idPost']);
        $this->setStatus($data['status']);
    }
}