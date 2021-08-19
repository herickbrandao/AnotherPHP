<?php namespace Another;

use \PDO;

/**
 * 
 */
class Database
{
	private $connection;
	private string $firstJoinTable;
	private string $join = "";
	private int|bool $last_id;

	function __construct(string $connection, string $username, string $password)
	{
		$this->connection = new PDO($connection, $username, $password);
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, $this->connection::ERRMODE_EXCEPTION);
	}

	public function query(string $sql, array|object $data = []): mixed
	{
		$query = $this->connection->prepare($sql);

		/*
		 * Bind values (prevents SQL injection)
		 */ 
		if(!empty($data))
		{
			foreach ($data as $k => $a)
			{
				if(is_integer($k) && substr_count($sql, "?") <= $k+1) {
					$query->bindValue($k+1,$a);
				} else if(strpos($sql, ":".$k) !== false) {
					$query->bindValue(":".$k,$a);
				} else if(strpos($k, ":") === 0 && strpos($sql, $k) !== false) {
					$query->bindValue($k,$a);
				}
			}
		}

		$query->execute();

		/*
		 * Sets the last id (primary key)
		 */ 
		if (stripos($sql, 'insert') === 0) {
			$this->last_id = $this->connection->lastInsertId();
		} else {
			$this->last_id = false;
		}

		/*
		 * Return if query has been successful
		 */ 
		if (stripos($sql, 'update') === 0 || stripos($sql, 'delete') === 0) {
			$count = $query->rowCount();
			return $count =='0' ? false : true;
		}

		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	public function queryRow(string $sql, array|object $data = []): mixed
	{
		$query = $this->query($sql, $data);
		return isset($query[0]) ? $query[0] : false;
	}

	public function select(string $table, string $select = "*", string $where = "1", array|object $data = []): mixed
	{
		$query = $this->query("SELECT {$select} FROM {$table} WHERE {$where}", $data);
		return isset($query[0]) ? $query : false;
	}

	public function selectRow(string $table, string $select = "*", string $where = "1", array|object $data = []): mixed
	{
		$query = $this->query("SELECT {$select} FROM {$table} WHERE {$where}", $data);
		return isset($query[0]) ? $query[0] : false;
	}

	public function insert(string $table, array|object $data): mixed
	{
		$keys = array();
		$values = array();

		foreach ($data as $key => $value) {
			$keys[] = $key;
			$values[] = ":{$key}";
		}

		$sql = "INSERT INTO {$table} (".implode(",", $keys).") VALUES (".implode(",",$values).");";

		$this->query($sql, $data);
		
		return $this->last_id;
	}

	public function update(string $table, string $where, array|object $data): mixed
	{
		$values = array();

		foreach ($data as $key => $value) {
			$values[] = $key." = :".$key;
		}

		$sql = "UPDATE {$table} SET ".implode(',', $values)." WHERE {$where}";

		return $this->query($sql, $data);
	}

	public function delete(string $table, string $where, array|object $data): mixed
	{
		$sql = "DELETE FROM {$table} WHERE {$where}";

		return $this->query($sql, $data);
	}

	public function lastInsertId(): int|bool
	{
		return $this->last_id;
	}

	public function iJoin(array $on): self
	{
		$tables = array(
			explode('.', $on[0])[0],
			explode('.', $on[1])[0]
		);

		$this->mergeJoin("INNER JOIN", $tables, $on);
		return $this;
	}

	public function oJoin(array $on): self
	{
		$tables = array(
			explode('.', $on[0])[0],
			explode('.', $on[1])[0]
		);

		$this->mergeJoin("OUTER JOIN", $tables, $on);
		return $this;
	}

	public function lJoin(array $on): self
	{
		$tables = array(
			explode('.', $on[0])[0],
			explode('.', $on[1])[0]
		);

		$this->mergeJoin("LEFT JOIN", $tables, $on);
		return $this;
	}

	public function rJoin(array $on): self
	{
		$tables = array(
			explode('.', $on[0])[0],
			explode('.', $on[1])[0]
		);

		$this->mergeJoin("RIGHT JOIN", $tables, $on);
		return $this;
	}

	public function fJoin(array $on): self
	{
		$tables = array(
			explode('.', $on[0])[0],
			explode('.', $on[1])[0]
		);

		$this->mergeJoin("FULL JOIN", $tables, $on);
		return $this;
	}

	public function cJoin(array $on): self
	{
		$tables = array(
			explode('.', $on[0])[0],
			explode('.', $on[1])[0]
		);

		$this->mergeJoin("CROSS JOIN", $tables, $on);
		return $this;
	}

	public function run(string $select = "*", string $where = "1", array|object $data = []): mixed
	{
		$this->join = "SELECT {$select} FROM {$this->firstJoinTable}" . $this->join . " WHERE {$where}";

		$query = $this->query($this->join, $data);

		$this->join = "";
		$this->firstJoinTable = "";

		return isset($query[0]) ? $query : false;
	}
	
	public function runRow(string $select = "*", string $where = "1", array|object $data = []): mixed
	{
		$this->join = "SELECT {$select} FROM {$this->firstJoinTable}" . $this->join . " WHERE {$where}";

		$query = $this->query($this->join, $data);

		$this->join = "";
		$this->firstJoinTable = "";

		return isset($query[0]) ? $query[0] : false;
	}

	public function close(): void
	{
		$this->connection = null;
	}
	
	public function filterKeys(array $keys, array|object $data, bool $nulls = true): array
	{
		$new_array = array();

		if($nulls) {
			foreach ($keys as $key) {
				$new_array[$key] = isset($data[$key]) ? $data[$key] : NULL;
			}
		} else {
			foreach ($keys as $key) {
				if(isset($data[$key])) {
					$new_array[$key] = $data[$key];
				}
			}
		}

		return $new_array;
	}

	private function mergeJoin(string $join, array $tables, array $on): void
	{
		if(empty($this->join)) {
			$this->firstJoinTable = $tables[0];
		}

		$this->join .= " {$join} {$tables[1]} ON {$on[0]} = {$on[1]}";
	}
}
