<?php
	namespace Parser;
	use PDO;
    
	class Model
	{
		protected static $pdo;
		
		public function __construct()
		{
			if ( ! self::$pdo ) { // если свойство не задано, то подключаемся
                self::$pdo = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPT);
			}
		}
		
		protected function findOne($query, $args)
		{
			$res = self::$pdo->prepare($query);
            $res->execute($args);
            $row = $res->fetch();
            return $row;
		}
		
		protected function findMany($query, $args = [], $mode = PDO::FETCH_DEFAULT)
		{
			$res = self::$pdo->prepare($query);
            $res->execute($args);
            $row = $res->fetchAll($mode);
            return $row;
		}
		
		protected function findColumn($query, $args = [])
		{
			$res = self::$pdo->prepare($query);
            $res->execute($args);
            $row = $res->fetchColumn();
            return $row;
		}
		
		protected function findClassOne($query, $className, $args = [])
		{
			$res = self::$pdo->prepare($query);
            $res->execute($args);
            $res->setFetchMode(PDO::FETCH_CLASS, $className);
            $row = $res->fetch();
            return $row;
		}
		
		protected function findClassMany($query, $className, $args = [])
		{
			$res = self::$pdo->prepare($query);
            $res->execute($args);
            $rows = $res->fetchAll(PDO::FETCH_CLASS, $className);
            return $rows;
		}
		
		protected function insert($query, $args = [])
		{
			$res = self::$pdo->prepare($query);
            return $res->execute($args);
		}
	}