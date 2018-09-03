<?php

use Illuminate\Database\Connection;
use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Log\LoggerInterface;
use Slim\Container;

$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("It works! This is the default welcome page.");

    return $response;
})->setName('root');

$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});

$app->get('/time', function (Request $request, Response $response) {
   $viewData = [
       'now' => date('Y-m-d H:i:s')
   ];

   return $this->get('view')->render($response, 'time.twig', $viewData);
});

$app->get('/logger-test', function (Request $request, Response $response) {
    /** @var Container $this */
    $logger = $this->get('logger');
    $logger->error('My error message!');

    $response->getBody()->write("Success");

    return $response;
});

$app->get('/users', function (Request $request, Response $response) {
    /** @var Container $this */
    /** @var Connection $db */

    $db = $this->get('db');
    $rows = $db->table('users')->get();
    return $response->withJson($rows);
});

$app->get('/users/first', function (Request $request, Response $response) {
    /** @var Container $this */
    /** @var Connection $db */

    $db = $this->get('db');
    $user = $db->table('users')->select('id', 'username', 'email')->limit(1)->first();
    return $response->withJson($user);
});

$app->get('/users/array', function (Request $request, Response $response) {
    /** @var Container $this */
    /** @var Connection $db */

    $db = $this->get('db');
    $users = $db->table('users')->select('id', 'username', 'email')->get()->toArray();
    return $response->withJson($users);
});

$app->get('/users/create/{username}', function (Request $request, Response $response) {
    $username = $request->getAttribute('username');

    /** @var Container $this */
    /** @var Connection $db */
    $db = $this->get('db');

    $results = $db->table('users')->insert(['username' => "$username", 'email' => "$username@example.com"]);
    return $response->withJson($results);
});

$app->get('/users/create/{username}/withdata', function (Request $request, Response $response) {
    $username = $request->getAttribute('username');

    /** @var Container $this */
    /** @var Connection $db */
    $db = $this->get('db');

    $userId = $db->table('users')->insertGetId(['username' => "$username", 'email' => "$username@example.com"]);
    $user = $db->table('users')->where('id', '=', $userId)->first();
    return $response->withJson($user);
});

$app->get('/users/delete/{id}', function (Request $request, Response $response) {
    $userId = $request->getAttribute('id');

    /** @var Container $this */
    /** @var Connection $db */
    $db = $this->get('db');

    $deleted = $db->table('users')->delete($userId);
    return $response->withJson($deleted);
});

$app->get('/users/remove-with-less-than-100-votes', function (Request $request, Response $response) {
    /** @var Container $this */
    /** @var Connection $db */
    $db = $this->get('db');

    $deleted = $db->table('users')->where('votes', '<', 100)->delete();
    return $response->withJson($deleted);
});



