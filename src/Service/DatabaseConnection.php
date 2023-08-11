<?php
declare(strict_types=1);
//connection database class
namespace App\Service;
class DatabaseConnection
{
    public ?\PDO $database=null;
    public function getConnection():\PDO {
        if ($this->database === null)
        {
            $this->database = new \PDO('mysql:host=localhost;dbname=blog;charset=utf8', 'root','');
        }
        return $this->database;
    }
}
