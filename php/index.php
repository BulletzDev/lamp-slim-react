<?php
use Slim\Factory\AppFactory;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/controllers/AlunniController.php';

$app = AppFactory::create();

$app->get('/alunni','AlunniController:display');
$app->get('/alunni/{id}','AlunniController:show');
$app->post('/alunni','AlunniController:create');
$app->put('/alunni/{id}','AlunniController:update');
$app->delete('/alunni/{id}','AlunniController:destroy');

$app->run();
