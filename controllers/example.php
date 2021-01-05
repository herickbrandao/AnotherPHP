<?php

use Another\System\Database;
use Another\System\Session;
use Another\System\Request;
use Another\System\Response;

/*
 * AnotherPHP v1.1 - Example Methods
 * See more: https://github.com/herickbrandao/AnotherPHP
 */
class example {
	public function index() {
		include_once '../views/Welcome.php';
	}

	public function databaseExample() {
		$data = Database::query(
			db: "my_db", # set in ../config/databases.php
			sql: "SELECT * FROM users WHERE id = :id",
			args: [":id"=>"1"]
		);
		
		# you can also bind values like:
		$data = Database::query(
			db: "my_db", # set in ../config/databases.php
			sql: "SELECT * FROM users WHERE username = ? AND password = ?",
			args: ["admin", "1234"]
		);

		print_r($data);
	}

	public function sessionExample() {
		if( $access ) {
			Session::create("language", $access); # method to create a session
			Response::redirect('logged'); # redirects to the logged page
		} else {
			Response::redirect('/?e');
		}

		# then in "logged" page:
		# Session::start("language"); # set in ../config/sessions.php
	}

	public function restfulApiExample() {
		# ::take checks the rest method to capture the attributes (get,post,put,delete,patch)
		$request = Request::take(["username", "password"]);
		$method  = Request::getMethod();

		$sql = match($method) {
			'get'	 => "SELECT * FROM users WHERE username = :username",
			'post'	 => "INSERT INTO users (username,password) VALUES (:username, :password)",
			'put'	 => "UPDATE users SET (username = :username, password = :password) WHERE username = :username",
			'patch'	 => "UPDATE users SET (password = :password) WHERE username = :username",
			'delete' => "DELETE FROM users WHERE username = :username AND password = :password",
			default  => throw new \Exception('Unsupported')
		};

		try {
			$data = Database::query(
				db: "my_db", # set in ../config/databases.php
				sql: $sql,
				args: [
					":username" => $request['username'],
					":password" => $request['password']
				]
			);

			Response::status(200)::json($data);
		} catch(PDOException $err) {
			Response::status(404)::json(["error" => $err]);
		}
	}
}