<?php
declare(strict_types=1);
namespace App\Model\Entity;

class User
{
    private ?int $id;
    private string $name = '';
    private string $firstname = '';
    private string $email = '';
    private string $password = '';
    private string $role = 'user';

    public function setId(int $id): void
    {
        $this->id=$id;
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
    public function getFirstname(): string
    {
        return $this->firstname;
    }
    public function setFirstname(string $firstname): self
    {
        $this->firstname=$firstname;
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

    public function fromArray(array $data): void
    {
        $this->setId((int) $data['id']);
        $this->setName($data['name']);
        $this->setFirstname($data['firstname']);
        $this->setEMail($data['email']);
        $this->setPassword($data['password']);
        $this->setRole($data['role']);
    }
}