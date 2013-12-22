<?php

/*
    Documentation : http://studio.jacksay.com/tutoriaux/php/connection-mysql-avec-pdo
*/

class Database {
    private $server;
    private $user;
    private $pass;
    private $database;
    private $connection;

    function Database()
    {
        $this->server = 'localhost';
        $this->user = 'root';
        $this->pass = '';
        $this->database = 'webdrac';

        $this->has_transaction = false;
        try {
          $this->connection = new PDO( 'mysql:host='.$this->server.';dbname='.$this->database, $this->user, $this->pass );
        } catch ( Exception $e ) {
          echo "Connection à MySQL impossible : ", $e->getMessage();
          die();
        }
    }

   /* function __construct($server,$user,$pass,$database)
    {
        $this->server = $server;
        $this->user = $user;
        $this->pass = $pass;
        $this->database = $database;
        $this->transaction = false;
        try {
          $this->connection = new PDO( 'mysql:host='.$this->server.';dbname='.$this->database, $this->user, $this->pass );
        } catch ( Exception $e ) {
          echo "Connection à MySQL impossible : ", $e->getMessage();
          die();
        }
    }
*/
    function last_id()
    {
        return $this->connection->lastInsertId();
    }

    function transaction_start()
    {
        try {
            $this->has_transaction = true;
            $this->connection->beginTransaction();    
        } catch ( Exception $e ) {
          echo "Une erreur est survenue lors du lancement de la transaction";
        }
    }
    function transaction_finish()
    {
        try {
            $this->connection->commit();
            $this->has_transaction = false;

        } catch ( Exception $e ) {
          echo "Une erreur est survenue lors du commit";
        }
    }
    function transaction_rollback()
    {
        try {
            $this->connection->rollback();
            $this->has_transaction = false;

        } catch ( Exception $e ) {
          echo "Une erreur est survenue lors du rollback";
        }
    }

    function query($sql)
    {
        try {
            $select = $this->connection->query($sql);    
            $results = $select->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch ( Exception $e ) {
          echo "Une erreur est survenue lors de l'execution de la requete";
        }
    }

    function execute($sql)
    {
        try {
            $nb_lines  = $this->connection->exec($sql);

            //if transaction management, return only true/false
            if($this->has_transaction == true)
            {
                if($nb_lines === false)
                {
                    return false;
                }
                return true;
            }

            return $nb_lines;
        } catch ( Exception $e ) {
          echo "Une erreur est survenue lors de l'execution de la requête";
        }
    }
}