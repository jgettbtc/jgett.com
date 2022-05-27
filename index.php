<?php
require 'app/MvcApp.php';

$app = new MvcApp();

$app->registerRoute(array(
	'path' => 'api/guid',
	'controller' => 'api',
	'action' => 'guid',
	'methods' => array('GET')
));

$app->registerRoute(array(
	'path' => 'api/sha256',
	'controller' => 'api',
	'action' => 'sha256'
));

$app->registerRoute(array(
	'path' => 'api/pods',
	'controller' => 'api',
	'action' => 'pods'
));

$app->registerRoute(array(
    'path' => 'api/price',
    'controller' => 'api',
    'action' => 'price'
));

$app->registerRoute(array(
	'path' => '',
	'controller' => 'home',
	'action' => 'index',
	'methods' => array('GET')
));

$app->registerRoute(array(
	'path' => 'about',
	'controller' => 'home',
	'action' => 'about'
));

$app->registerRoute(array(
	'path' => 'pods',
	'controller' => 'pods',
	'action' => 'index',
	'methods' => array('GET')
));

$app->registerRoute(array(
    'path' => 'faq',
    'controller' => 'faq',
    'action' => 'index',
    'methods' => array('GET')
));

$app->registerRoute(array(
    'path' => 'faq/21-million-limit',
    'controller' => 'faq',
    'action' => 'twentyone_million_limit',
    'methods' => array('GET')
));

$app->registerRoute(array(
    'path' => 'faq/bitcoin-is-myspace',
    'controller' => 'faq',
    'action' => 'bitcoin_is_myspace',
    'methods' => array('GET')
));

$app->registerRoute(array(
	'path' => 'bitcoin/price',
    'controller' => 'bitcoin',
    'action' => 'price',
    'methods' => array('GET')
));

$app->handleRequest();
