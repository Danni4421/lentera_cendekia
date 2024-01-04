<?php

abstract class Database
{
  private string $host, $port, $username, $password, $database;
  private PDO $connection;
  private PDOStatement $statement;
  private string $error;

  /**
   * Connect into database
   *
   * @return void
   */
  public function connect()
  {
    $this->connection = new PDO("");
  }

  /**
   * Query
   *
   * @param string $query
   * @param array $options
   * @return void
   */
  public abstract function query($query, $options = []);

  /**
   * Result of the queries
   *
   * @return mixed
   */
  public function result()
  {
  }
}
