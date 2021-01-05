<?php namespace Another\System;

use \PDO;

interface iDatabase {
	public static function set(string $name, array $data);
	public static function close();
	public static function lastId();
	public static function query(string $db, string $sql, array|bool $args = false);
	public static function queryRow(string $db, string $sql, array|bool $args = false);
}

class Database implements iDatabase {
	# database connection and all db informations
	private static $connection=[], $databases=[];

	# id from last insert
	private static int|string $last_id;

	# called in config/databases.php
	public static function set(string $name, array $data) {
		self::$databases[$name] = $data;
	}

	private static function connect(string $db) {
		if ( !empty(self::$databases[$db]) ) {
			$port = '';
			if(!empty(self::$databases[$db][5])) { $port = 'port='.self::$databases[$db][5].';'; }
			
			# DB's Type
			switch ( strtolower(self::$databases[$db][0]) ) {
				case 'mysql':
					# Concatenates PDO
					$pdoConcatenate  = strtolower( self::$databases[$db][0] );
					$pdoConcatenate .= ':host=' . self::$databases[$db][1] .';';
					$pdoConcatenate .= 'charset=utf8;'.$port;
					$pdoConcatenate .= 'dbname=' . self::$databases[$db][2];

					# Brings the connection
					self::$connection[$db] = new PDO($pdoConcatenate, self::$databases[$db][3], self::$databases[$db][4]);
					self::$connection[$db]->setAttribute(PDO::ATTR_ERRMODE, self::$connection[$db]::ERRMODE_EXCEPTION);
					break;

				case 'pgsql':
					# Concatenates PDO
					$pdoConcatenate  = strtolower( self::$databases[$db][0] );
					$pdoConcatenate .= ':host=' . self::$databases[$db][1] .';';
					$pdoConcatenate .= empty($port) ? 'port=5432;' : $port;
					$pdoConcatenate .= 'dbname=' . self::$databases[$db][2];

					# Brings the connection
					self::$connection[$db] = new PDO($pdoConcatenate, self::$databases[$db][3], self::$databases[$db][4]);
					self::$connection[$db]->setAttribute(PDO::ATTR_ERRMODE, self::$connection[$db]::ERRMODE_EXCEPTION);
					break;
				
				default:
					echo ("<b>WARNING: </b> The database type '<i>". self::$databases[$db][0] ."</i>' was not found in <i>'system/databases.php'</i> - Database::set('". $db ."').");
					break;
			}
		} else {
			echo ("<b>WARNING: </b> there's no '<i>". $db ."</i>' database in <i>'config/databases.php'</i>.");
		}
	}

	public static function close() {
		# Ends the Connections
		self::$connection = null;
	}

	public static function lastId() {
		return isset(self::$last_id) ? self::$last_id : null;
	}

	public static function query(string $db, string $sql, array|bool $args = false) {
		# Executes a Database connection
		if(!isset(self::$connection[$db]))
			self::connect( $db );

		try {
			# Starts the Query
			$query = self::$connection[$db]->prepare($sql);

			# Bind values (args)
			if($args && is_array($args)) {
				foreach ($args as $k => $a) {
					if(is_integer($k)) {
						if(substr_count($sql, "?") <= $k+1) {
							$query->bindValue($k+1,$a);
						}
					} else {
						if(strpos($sql, $k) !== false) {
							$query->bindValue($k,$a);
						}
					}
				}
			}

			$query->execute();

			# Sets the last id ( primary key )
			if (stripos($sql, 'insert') !== false AND stripos($sql, 'insert') === 0)
				self::$last_id = self::$connection[$db]->lastInsertId();

			# Get the Return
			$fetchAll = $query->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			$fetchAll = $e->getMessage();
		}

		# Returns into the Controller
		return $fetchAll;
	}

	public static function queryRow(string $db, string $sql, array|bool $args = false) {
		$data = self::query( $db, $sql, $args );
		return ( isset($data[0]) ) ? $data[0] : array();
	}
}