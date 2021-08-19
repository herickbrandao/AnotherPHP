# AnotherPHP v2.0
A Framework built for PHP8+

## Getting Started
1. Download this repo;
1. Rename /htaccess and /public/htaccess to .htaccess (Apache control);
1. Have fun!

## Minimum Knowledges
1. PHP;
1. Classes;
1. Namespaces;
1. Rest Api;

## Routing
**app/config/routes.php**:
```php
/*
 * Instantiates the router class
 */
$router = new \Another\Router;

/*
 * Default Controller Method
 * The class name needs to be the same as file name for Controller Routing
 * There's: ->get(), ->post(), ->put(), ->delete() and ->patch()
 *
 * You can also route a php file normally, like:
 *    $router->get("/", "app/myPage.php");
 */
$router->get("/", "myController::index");
$router->get("/:page", "myController::index");

/*
 * You can also create a dynamic route and/or bind the route with a function:
 */
$router->get("/user/:id", function() use ($router) {
    if(is_numeric($_GET['ARGS']['id'])) {
        return "myController::anotherPageExample";
    } else {
        $router->call404();
    }
});

/* Required commands for routing system */
$router->define404('system/404.html');
$router->resolveRoute();
$router->callRoute();
```

## Database example (class method)
Default template:
```php
<?php

/**
 * Extendable class for DB connection (PDO) - Example
 */
class baseController
{
	/**
	 * The following line is required in an extended class constructor:
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
	 *    data: ['name' => 'Example Name'], // (optional, but required if you want to use 'bindValue()' in PDO)
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
	 *    data: ['id' => 1] // (optional, but required if you want to use 'bindValue()' in PDO)
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
	 *    data: ['id' => 1] // (optional, but required if you want to use 'bindValue()' in PDO)
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
```

Session example
```php
<?php

/**
 * Example class use case's of Session
 * There is 2 available methods: JWT and COOKIE
 */
class baseSession
{
	private $session;

	/**
	 * The following line is required in a extended class constructor:
	 *    parent::__construct(); 
	 */
	function __construct()
	{
		/**
		 * Session by JWT method:
		 * new Session(secret: "YOUR-JWT-SECRET-HERE")
		 * 
		 * Generates a new JWT code (returns a string)
		 * $this->session->jwt(payload: "YOUR-DATA (JSON OR ARRAY)");
		 * 
		 * JWT Validation (returns true/false)
		 * $this->session->start("YOUR-JWT-FOR-VALIDATION");
		 */
		$this->session = new Session('$%(@))%*!)$#!%)!)*!21ve,WGOGWPKE');
		$MY_JWT_HASH =  $this->session->jwt(['id' => 1, 'name' => 'YourName']);
		var_dump( $this->session->start($MY_JWT_HASH) );

		/**
		 * Session by Cookie method
		 * It's the basic session from PHP
		 * 
		 * Examples:
		 * $this->session->set(["name" => "YourName"]); is equal to $_SESSION["name"] = "YourName";
		 * $this->session->get("name"); is equal to $_SESSION["name"];
		 */
		$this->session = new Session();
		$this->session->start("myOwnCookieSession");
		$this->session->set(['id' => 1, 'name' => 'YourName']);
		var_dump( $_SESSION );
	}
}
```
