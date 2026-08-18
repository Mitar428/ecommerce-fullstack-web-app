<?php

namespace Ecommerce\Shop\Core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;

    private PDO $connection;


    private function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "ecommerce_prodavnica";


        try {

            $this->connection = new PDO(
                "mysql:host=$servername;dbname=$dbname;charset=utf8",
                $username,
                $password
            );

            $this->connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );


        } catch (PDOException $e) {

            die("Greška konekcije: " . $e->getMessage());

        }
    }


    public static function getInstance(): Database
    {
        if (self::$instance === null) {

            self::$instance = new Database();

        }

        return self::$instance;
    }


    public function getConnection(): PDO
    {
        return $this->connection;
    }
}