<?php

/*
	Класс реализует подключение к базе данных
*/

namespace Libs\Database;

abstract class Database
{	
	private $connect;
	private $host = "mysql-srv:3306";
	private $dbName = "db";
	private $user = "user";
	private $pass = "123";
	public $rowCount;
	
	protected function __construct() // Подключение к базе данных
	{
		try {
			$this->connect = new \PDO("mysql:host=$this->host; dbname=$this->dbName; charset=utf8", $this->user, $this->pass);
		} catch (PDOException $Exception) {
			return "Не удалось подключиться к базе данных";
		}
	}
	
	protected function transferSql($sql) //Отправка sql запроса
	{
		try {
			$result = array();
			$request = $this->connect->prepare($sql);
			$request->execute();
			
			while($value = $request->fetchObject()) {
				$result[] = $value;
			}
			
			return $result;
		} catch (PDOException $Exception) {
			return 'Нет подлкючение к базе данных';
		}
	}
}
