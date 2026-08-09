<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('api/ingrediente/(:any)', 'Api::ingrediente/$1');

$routes->get('api/testeTraducao', 'Api::testeTraducao');

$routes->get('api/pesquisar/(:any)', 'Api::pesquisar/$1');

$routes->get('api/informacoes/(:num)', 'Api::informacoes/$1');

$routes->get('login', 'Auth::login');

$routes->get('cadastro', 'Auth::cadastro');

$routes->post('usuario/cadastrar', 'UsuarioController::cadastrar');

$routes->post('usuario/logar', 'UsuarioController::logar');

$routes->get('entrar', 'Auth::entrar');

$routes->get('/receita/cadastrar', 'ReceitaController::create');

$routes->post('/receita/salvar', 'ReceitaController::salvar');

$routes->get('receita/visualizar/(:num)', 'ReceitaController::visualizarReceita/$1');

$routes->get('receita/editar/(:num)', 'ReceitaController::editar/$1');

$routes->get('visualizarCadUsuario', 'ReceitaController::visualizarCadUsuario');

$routes->get('logout', 'Auth::logout');

$routes->get('receita/visualizar/(:num)', 'ReceitaController::visualizar/$1');

$routes->get('feed', 'ReceitaController::listar');

$routes->get('perfil', 'ReceitaController::listarReceitaUsuario');

$routes->post('receita/atualizar/(:num)', 'ReceitaController::atualizar/$1');