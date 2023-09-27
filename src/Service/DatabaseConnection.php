<?php
declare(strict_types=1);
//connection database class
namespace App\Service;

use App\Service\Http\Request;
use Symfony\Component\Dotenv\Dotenv;
class DatabaseConnection
{

//    public string $host = 'mysql:host=localhost:3306;dbname=blog;charset=utf8';
//
//    public string $user = 'root';
//    public string $password = '';
//
    public ?\PDO $database = null;
    public function getConnection():\PDO {

        $dotenv = new Dotenv();
        $getEnv = new Environment($dotenv);

        $dbDSN = $getEnv->getEnv('MYSQL_DSN');
        $dbUser = $getEnv->getEnv('MYSQL_USER');
        $dbPassword = $getEnv->getEnv('MYSQL_PASSWORD');

        if ($this->database === null) {
            $this->database = new \PDO($dbDSN, $dbUser, $dbPassword, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING));
        }
        return $this->database;
    }
}
