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
