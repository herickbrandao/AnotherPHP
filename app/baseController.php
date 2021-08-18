<?php

/**
 * Extendable class for DB connection (PDO) - Example
 */
class baseController
{
	/**
	 * The following line is required in a extended class constructor:
	 *    parent::__construct(); 
	 */
	function __construct() {
		/**
		 * Helpful class that uses PDO
		 * See: https://www.php.net/manual/pdo.construct.php
		 */
		$this->db = new Another\Database('mysql:host=localhost;dbname=example', 'root', '');
	}

	/**
	 * Get all users from table 'users' or false if it's empty
	 * 
	 * Full example:
	 * 
	 * $this->db->select(
	 *    table: 'users', 
	 *    select: '*', // (optional)
	 *    where: 'name = :name', // (optional)
	 *    data: ['name' => 'Example Name'], // (optional, but required if you want use 'bindValue()' in PDO)
	 * );
	 */
	public function getUsers() {
		return $this->db->select('users');
	}

	/**
	 * Returns the first row from select or false if it's empty
	 * 
	 * Full example:
	 * 
	 * $this->db->selectRow(
	 *    table: 'users', 
	 *    select: '*', // (optional)
	 *    where: 'id = :id', // (optional)
	 *    data: ['id' => 1] // (optional, but required if you want use 'bindValue()' in PDO)
	 * );
	 */
	public function getOneUser($id) {
		return $this->db->selectRow('users', '*', 'id = :id', ['id' => $id]);
	}

	/**
	 * Inserts a new user on 'users'
	 * Returns true if successful, and false for error
	 * 
	 * Full example:
	 * 
	 * $this->db->insert(
	 *    table: 'users',
	 *    data: ['name' => 'Example Name']
	 * );
	 */
	public function insertUser($data) {
		return $this->db->insert('users', $data);
	}

	/**
	 * Updates a user (where condition is required)
	 * Returns true if some data has changed in database and false if it's not
	 * 
	 * Full example:
	 * 
	 * $this->db->update(
	 *    table: 'users', 
	 *    where: 'id = :id',
	 *    data: ['id' => 1, 'name' => 'Example Name']
	 * );
	 */
	public function updateUser($data) {
		return $this->db->update('users', 'id = :id', $data);
	}

	/**
	 * 'DELETE' query from SQL
	 * Returns true if some data has been deleted in database and false if it's not
	 * 
	 * Full example:
	 * 
	 * $this->db->delete(
	 *    table: 'users',
	 *    where: 'id = :id',
	 *    data: ['id' => 1]
	 * );
	 */
	public function deleteUser($data) {
		return $this->db->delete('users', 'id = :id', $data);
	}

	/**
	 * Create your own custom query for PDO instance
	 * 
	 * Full example:
	 * 
	 * $this->db->query(
	 *    sql: "SELECT * FROM users WHERE id = :id",
	 *    data: ['id' => 1] // (optional, but required if you want use 'bindValue()' in PDO)
	 * );
	 */
	public function customQuery() {
		return $this->db->query("SELECT * FROM users WHERE id = :id", ['id' => 4]);
	}

	/**
	 * For SQL Foreign Keys, you can merge JOINS with these functions (always 1 array with 2 strings as arguments):
	 * 
	 * // INNER JOIN
	 * - iJoin(on: ['table1.id','table2.id']); 
	 * 
	 * // OUTER JOIN
	 * - oJoin(on: ['table1.id','table2.id']);
	 * 
	 * // LEFT JOIN
	 * - lJoin(on: ['table1.id','table2.id']); 
	 * 
	 * // RIGHT JOIN
	 * - rJoin(on: ['table1.id','table2.id']);
	 * 
	 * // FULL JOIN
	 * - fJoin(on: ['table1.id','table2.id']); 
	 * 
	 * // CROSS JOIN
	 * - cJoin(on: ['table1.id','table2.id']);
	 * 
	 * Then, after set your merges, you should use the 'run()' function:
	 * 
	 * $this->db
	 * 		->iJoin(['table1.id','table2.id'])
	 * 		->iJoin(['table2.table3_id','table3.id'])
	 * 		->run(select: '*', where: 'id = :id', data: ['id' => 1]);
	 */
	public function joinTables() {
		return $this->db
			->iJoin(['table1.id','table2.id'])
			->iJoin(['table2.table3_id','table3.id'])
			->run(); // optional args
	}

	/**
	 * Rest Api example with database select
	 */
	public function printData(): void {
		$data = $this->db->select('users');

		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
}