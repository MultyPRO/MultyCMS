<?php
define('R', $_SERVER['DOCUMENT_ROOT']   .DIRECTORY_SEPARATOR);
define('ROOT_DOMAIN', 'multy.cms');

define('M', $_SERVER['DOCUMENT_ROOT']   .DIRECTORY_SEPARATOR .  "__M__"             .DIRECTORY_SEPARATOR);
define('V', $_SERVER['DOCUMENT_ROOT']   .DIRECTORY_SEPARATOR .  "__V__"             .DIRECTORY_SEPARATOR);
define('C', $_SERVER['DOCUMENT_ROOT']   .DIRECTORY_SEPARATOR .  "__C__"             .DIRECTORY_SEPARATOR);
define('I', $_SERVER['DOCUMENT_ROOT']   .DIRECTORY_SEPARATOR .  "__I__"             .DIRECTORY_SEPARATOR);

define('P', $_SERVER['DOCUMENT_ROOT']   .DIRECTORY_SEPARATOR .  "__P__"     .DIRECTORY_SEPARATOR);
define('S', $_SERVER['DOCUMENT_ROOT']   .DIRECTORY_SEPARATOR .  "__S__"    .DIRECTORY_SEPARATOR);

// Ползва се от __autoload за да си търси класовете
define('FOLDERS', 'M,C,I,P,S');

define('U', $_SERVER['DOCUMENT_ROOT']   .DIRECTORY_SEPARATOR .  "__upload__"    .DIRECTORY_SEPARATOR);

define('Inc', $_SERVER['DOCUMENT_ROOT'] .DIRECTORY_SEPARATOR .  "_include_"     .DIRECTORY_SEPARATOR);

////////////////////////////////////////////////////////////////////////////////

ini_set("session.cookie_httponly", 1);
ini_set('session.cookie_domain', '.'.$_SERVER['HTTP_HOST'] );

mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");
mb_http_input("UTF-8");

ini_set('display_errors', 1);
error_reporting(1);
    
session_start();

require_once Inc.'functions.php';