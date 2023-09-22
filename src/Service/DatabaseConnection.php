<?php
declare(strict_types=1);
//connection database class
namespace App\Service;
class DatabaseConnection
{
    public string $host = 'mysql:host=localhost:3306;dbname=blog;charset=utf8';

    public string $user = 'root';
    public string $password = '';

    public ?\PDO $database = null;
    public function getConnection():\PDO {
        if ($this->database === null) {
            $this->database = new \PDO($this->host, $this->user, $this->password, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING));
        }
        return $this->database;
    }
}
