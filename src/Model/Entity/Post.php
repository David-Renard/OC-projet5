<?php
declare(strict_types=1);
namespace App\Model\Entity;

class Post
{
    public function __construct(
        private int $id,
        private string $title,
        private $creation_date,
        private string $lede,
        private string $content,
        private $last_update_date,
        private int $id_author,
        private string $status,
    )
    {
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): self
    {
        $this->title=$title;
        return $this;
    }
    public function getCreationDate(): string
    {
        return $this->creation_date;
    }
    public function setLede(string $lede): self
    {
        $this->lede=$lede;
        return $this;
    }
    public function getLede(): string
    {
        return $this->lede;
    }
    public function setContent(string $content): self
    {
        $this->content=$content;
        return $this;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function setLastUpdateDate(string $last_update_date): self
    {
        $this->last_update_date=$last_update_date;
        return $this;
    }
    public function getUpdateDate(): string
    {
        return $this->last_update_date;
    }
    public function setIdAuthor(int $id_author): self
    {
        $this->id_author=$id_author;
        return $this;
    }
    public function getIdAuthor(): int
    {
        return $this->id_author;
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