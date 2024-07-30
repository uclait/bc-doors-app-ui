<?php
$config = array (
  'debug' => 2,
  'App' => 
  array (
    'fullBaseUrl' => 'http://localhost',
    'imageBaseUrl' => 'img/',
    'cssBaseUrl' => 'css/',
    'jsBaseUrl' => 'js/',
    'base' => false,
    'baseUrl' => false,
    'dir' => 'app',
    'webroot' => 'webroot',
    'www_root' => 'C:\\xampp\\htdocs\\bruin-card\\app\\webroot\\',
    'encoding' => 'UTF-8',
  ),
  'Error' => 
  array (
    'handler' => 'ErrorHandler::handleError',
    'level' => 24575,
    'trace' => true,
  ),
  'Exception' => 
  array (
    'handler' => 'ErrorHandler::handleException',
    'renderer' => 'ExceptionRenderer',
    'log' => true,
  ),
  'Routing' => 
  array (
    'admin' => 'admin',
  ),
  'Session' => 
  array (
    'defaults' => 'php',
  ),
  'Security' => 
  array (
    'salt' => 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mia',
    'cipherSeed' => '768593096574535424967496836450',
  ),
  'Acl' => 
  array (
    'classname' => 'DbAcl',
    'database' => 'default',
  ),
  'Dispatcher' => 
  array (
    'filters' => 
    array (
      0 => 'AssetDispatcher',
      1 => 'CacheDispatcher',
    ),
  ),
);