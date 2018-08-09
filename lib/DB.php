<?php

/*
Данный класс является некой оберткой для PDO
todo: переписать функции согласно PDO prepare
*/

Class DB {

	private static $_init = false;
    private static $_database = false;
    private static $_transaction = false;
	
	public static $profile_list = array();

	public static function _local_db(){	
	
		if (!self::$_init){
    
			$conf = \Config::load('database');
			$conf = $conf[$conf['use']];
			
			if ($conf){
				$db = new \PDO($conf['type'].':host='.$conf['hostname'].';dbname='.$conf['database'].';charset=UTF8', $conf['username'], $conf['password'], array(\PDO::ATTR_PERSISTENT => true));
				$db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
				$db->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAME 'utf8'; SET GLOBAL innodb_stats_on_metadata=0;");
				
				return self::$_database = $db;				
			}
			self::$_init = true;			
		}
		else
		{
			if (self::$_database)
				return self::$_database;
			else 
				return false;
		}
			
		return false;
    
    }
	
	public static function query($qr){
		
		if (!$db = self::_local_db())
			Exception('Not initial DataBase');
			
		try {
			
			$qr = (string) $qr;
			
			if (SITE_MODE === 'DEV') $start = microtime(true);
		
			if ( $res = $db->query($qr)){
				if (SITE_MODE === 'DEV') self::$profile_list[] = array('time'=> microtime(true)-$start, 'qery'=>$qr);
				if (!(strpos(trim($qr),'INSERT')===false) && $res){
					return array('0'=>$db->lastInsertId());
				}
				
				return $res;
			}
			else {
				$error = $db->errorInfo();
				if (isset($error[2])){
					throw new Exception($error[2]);
				}
				else {
					throw new Exception($e->getMessage());
				}
			}
			return false;
		} 
		catch (Exception $e) {	
			if (self::$_transaction){
				self::$_database->rollBack();
			}
			
			$error = self::$_database->errorInfo();
			if (isset($error[2])){
				throw new Exception($error[2]);
			}
			else {
				throw new Exception($e->getMessage());
			}
		}
	}
	
	public static function oneSelect($qr){
		
		if (!$db = self::_local_db())
			Exception('Not initial DataBase');
		
		try {
			
			$qr = (string) $qr;
			$qr = $qr.' LIMIT 1';
			
			if (SITE_MODE === 'DEV') $start = microtime(true);
		
			if ($res = $db->query($qr))
			{
				if (SITE_MODE === 'DEV') self::$profile_list[] = array('time'=> microtime(true)-$start, 'qery'=>$qr);
				
				return $res->fetch();
			}
			else 
			{
				$error = $db->errorInfo();
				if (isset($error[2]))
				{
					throw new Exception($error[2]);
				}
				else 
				{
					throw new Exception($e->getMessage());
				}
			}
			return false;
		} 
		catch (Exception $e) 
		{
			
			if (self::$_transaction)
			{
				self::$_database->rollBack();
			}
			
			$error = self::$_database->errorInfo();
			if (isset($error[2]))
			{
				throw new Exception($error[2]);
			}
			else 
			{
				throw new Exception($e->getMessage());
			}
		}
	}
	
	public static function insert($table_name = '', $data = false){
		
		if (!$db = self::_local_db())
			Exception('Not initial DataBase');
		
		$table_name = trim($table_name);
		if (empty($table_name) && empty($data) && empty($where))
		{
			throw new Exception('no data for insert into "'.$table_name.'"');
		}
		else {
			
			$q = 'INSERT into `'.trim($db->quote($table_name),"'").'`';
			$items = ''; $values = '';
			foreach ($data as $key => $value)
			{
				$items .= trim($key).', ';
				$values .= $db->quote((string) $value).', ';
			}
			$q .= ' ('.rtrim($items, ', ').') VALUES ('.rtrim($values, ', ').')';
			if ($res = self::query($q))
			{
				return $res;
			}
			return false;
		}
	}
    
	public static function update($table_name = '', $data = false, $where = false){
		
		if (!$db = self::_local_db())
			Exception('Not initial DataBase');
		
		
		$table_name = trim($table_name);
		if (empty($table_name) && empty($data) && empty($where))
		{
			throw new Exception('no data for update');
		}
		else {
			
			$q = 'UPDATE `'.trim($db->quote($table_name),"'").'`';
			$q .= ' SET ';
			$s = '';
			foreach ($data as $key => $value)
			{
				$s .= '`'.trim($key).'` = '.$db->quote(trim((string) $value)).', ';
			}
			$q .= rtrim($s, ', ').' WHERE '.trim((string) $where);
			return self::query($q);
		}
	}
    
    public static function quote($qr){
		if (!$db = self::_local_db())
			Exception('Not initial DataBase');
		
		return $db->quote($qr);
    }
	
	public static function beginTransaction()
	{
		return self::$_transaction = self::$_database->beginTransaction();
	}
	
	public static function commitTransaction()
	{
		if (self::$_transaction)
		{
			return self::$_database->commit();
		}
		return false;
	}
}

?>