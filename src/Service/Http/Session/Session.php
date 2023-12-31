<?php
declare(strict_types=1);
namespace App\Service\Http\Session;

final class Session
{
    private SessionParametersBag $sessionParametersBag;
    public function __construct()
    {
        session_start();
        $this->sessionParametersBag=new SessionParametersBag($_SESSION);
    }
    public function set(string $name, mixed $value):void
    {
        $this->sessionParametersBag->set($name, $value);
    }
    public function get(string $name): mixed
    {
        return $this->sessionParametersBag->get($name);
    }
    public function toArray(): ?array
    {
        return $this->sessionParametersBag->all();
    }
    public function remove(string $name): void
    {
        $this->sessionParametersBag->unset($name);
    }
    public function addFlashes(string $type, string $message): void
    {
        $flashes = $this->getFlashes();
        $flashes[$type][] = $message;
        $this->set('flashes',$flashes);
    }
    public function getFlashes(): ?array
    {
        $flashes = $this->get('flashes');
        $this->remove('flashes');

        return $flashes;
    }
}