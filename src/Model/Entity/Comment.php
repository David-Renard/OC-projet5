<?php
declare(strict_types=1);
namespace App\Model\Entity;

class Comment
{
    public function __construct(
        private int $id,
        private string $content,
        private int $id_author,
        private string $creation_date,
        private int $id_post,
        private string $status,
        private int $id_evaluator,
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
        return $this->id_author;
    }
    public function setIdAuthor(int $id_author): self
    {
        $this->id_author=$id_author;
        return $this;
    }
    public function getCreationDate(): string
    {
        return $this->creation_date;
    }
    public function setCreationDate(string $creation_date): self
    {
        $this->creation_date=$creation_date;
        return $this;
    }
    public function getIDPost(): int
    {
        return $this->id_post;
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
    public function getIDEvaluator(): int
    {
        return $this->id_evaluator;
    }
    public function setIDEvaluator(int $id_evaluator): self
    {
        $this->id_evaluator=$id_evaluator;
        return $this;
    }
}