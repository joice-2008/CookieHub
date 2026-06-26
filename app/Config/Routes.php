<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');


$routes->get('api/ingrediente/(:any)', 'Api::ingrediente/$1');
$routes->get('api/testeTraducao', 'Api::testeTraducao');