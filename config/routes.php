<?php use \system\Router;

# pages
Router::get("/", "controllers/example::index", "Welcome to Another!");

Router::get("/user/:id", function() {
	$id = \system\Request::params("id");

	if(is_numeric($id) && (int)$id == $id)
		return "controllers/example::index";
	else
		Router::call404();
});

# default 404 page
Router::set404(callback: "system/404.html", title: "Page 404 - Error");

/*****************

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

# multiple methods
Router::match(['get', 'post'], "/page", "controllers/example::index");

# restApi's functions
Router
	::get(request: "", callback: "")
	::post(request: "", callback: "")
	::put(request: "", callback: "")
	::delete(request: "", callback: "")
	::patch(request: "", callback: "")
	::match(method: [''], request: "", callback: "");
	
	# Aliases
	::pos(request: "", callback: "") # post
	::del(request: "", callback: "") # delete
	::pat(request: "", callback: "") # patch
	::mat(method: [''], request: "", callback: "") # match

# calling 404 page (this gonna end the entire script)
Router::call404();

*****************/
