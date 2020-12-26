# AnotherPHP
Framework for PHP8 or Higher!

## Getting Started
1. Download this repo;
1. Rename /htaccess and /public/htaccess to .htaccess (Apache control);
1. Have fun!

## Minimum Knowledges
1. PHP;
1. Classes;
1. Namespaces;

## Routing
**/config/routes.php**:
```php
# page example (common view or class call)
Router::add(request: "/contact", callback: "views/page1.html");
Router::add(request: "/contact", callback: "controllers/myClass::Method", title: "Contact Page");

# dynamic routes
Router::get("/api/:req", "controllers/example::index");
Router::get("/user/:id", function() {
  $id = \system\Request::params("id");

  if(is_numeric($id) && (int)$id == $id)
    return "controllers/example::index";
  else
    Router::call404();
});

# restApi's functions
Router
  ::get(request: "", callback: "")
  ::post(request: "", callback: "")
  ::put(request: "", callback: "")
  ::delete(request: "", callback: "")
  ::patch(request: "", callback: "");
  
  # Aliases
  ::pos(request: "", callback: "") # post
  ::del(request: "", callback: "") # delete
  ::pat(request: "", callback: "") # patch

# calling 404 page (this gonna end the entire script)
Router::call404();
```

## Controllers (class methods)
Default template for **/controllers/*.php**:
```php
<?php

use \system\Database;
use \system\Session;
use \system\Request;
use \system\Response;

class example {
  public function index() {
    Response::echo("Hello World");
  }
}
```

Example methods:
```php
<?php

use \system\Database;
use \system\Session;
use \system\Request;
use \system\Response;

class example {
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
      'get'    => "SELECT * FROM users WHERE username = :username",
      'post'   => "INSERT INTO users (username,password) VALUES (:username, :password)",
      'put'    => "UPDATE users SET (username = :username, password = :password) WHERE username = :username",
      'patch'  => "UPDATE users SET (password = :password) WHERE username = :username",
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
```
