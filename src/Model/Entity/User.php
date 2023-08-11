<?php
declare(strict_types=1);
namespace App\Model\Entity;

class User
{
    public function __construct(
        private int $id,
        private string $name,
        private string $firstName,
        private string $email,
        private string $password,
        private string $role,
        private string $status,
    )
    {
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name=$name;
        return $this;
    }
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    public function setFirstName(string $firstName): self
    {
        $this->firstName=$firstName;
        return $this;
    }
    public function getEMail(): string
    {
        return $this->email;
    }
    public function setEMail(string $email): self
    {
        $this->email=$email;
        return $this;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password=$password;
        return $this;
    }
    public function getRole(): string
    {
        return $this->role;
    }
    public function setRole(string $role): self
    {
        $this->role=$role;
        return $this;
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