<?php
declare(strict_types=1);
namespace App\Model\Entity;

class Post
{
        private ?int $id;
        private string $title='';
        private string $creationDate='';
        private string $lede='';
        private string $content='';
        private string $lastUpdateDate='';
        private ?int $idAuthor;
        private string $status;
        private array $comments=[];
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
        return $this->creationDate;
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
    public function setLastUpdateDate(string $lastUpdateDate): self
    {
        $this->lastUpdateDate=$lastUpdateDate;
        return $this;
    }
    public function getUpdateDate(): string
    {
        return $this->lastUpdateDate;
    }
    public function setIdAuthor(int $idAuthor): self
    {
        $this->idAuthor=$idAuthor;
        return $this;
    }
    public function getIdAuthor(): int
    {
        return $this->idAuthor;
    }

    private function setId(int $id): void
    {
        $this->id = $id;
    }

    private function setCreationDate($creationDate): void
    {
        $this->creationDate = $creationDate;
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

    public function setComments(array $comments):void
    {
        $this->comments=$comments;
    }

    /**
     * @return Comment[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    public function fromArray(array $data): void
    {
        $this->setId((int) $data['ID']);
        $this->setTitle($data['title']);
        $this->setCreationDate($data['creationDate']);
        $this->setLede($data['lede']);
        $this->setContent($data['content']);
        $this->setLastUpdateDate($data['lastUpdateDate']);
        $this->setIdAuthor($data['idAuthor']);
        $this->setStatus($data['status']);
    }

}