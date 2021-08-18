<?php

/*
 * Instantiates the router class
 */
$router = new \Another\Router;

/*
 * Default Controller Method
 * The class name needs to be the same as file name for Controller Routing
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