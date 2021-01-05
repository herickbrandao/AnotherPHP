<?php use Another\System\Router;

/*
 * AnotherPHP v1.1 - Routing Examples
 * Router::add(request: "/contact", callback: "views/page1.html");
 * Router::add(request: "/contact", callback: "controllers/myClass::Method", title: "Contact Page");
 *
 * # function built in (example)
 * Router::put("/user/:id", function() {
 *	$id = \system\Request::params("id");
 *
 *	if(is_numeric($id) && (int)$id == $id)
 *		return "controllers/example::index";
 *	else
 *		Router::call404();
 * });
 *
 * Router
 * 		::get(request: "", callback: "")
 * 		::post(request: "", callback: "")
 * 		::put(request: "", callback: "")
 * 		::delete(request: "", callback: "")
 * 		::patch(request: "", callback: "");
 *
 * # multiple methods
 * Router::match(['get', 'post'], "/page", "controllers/example::index");
 *
 * # aliases
 * Router
 * 		::pos(request: "", callback: "") # post
 * 		::del(request: "", callback: "") # delete
 * 		::pat(request: "", callback: "") # patch
 * 		::mat(method: [''], request: "", callback: "") # match
 *
 * See more: https://github.com/herickbrandao/AnotherPHP
 */

# home page
Router::get("/", "controllers/example::index");

# default 404 page
Router::set404(callback: "system/404.html", title: "Page 404 - Error");