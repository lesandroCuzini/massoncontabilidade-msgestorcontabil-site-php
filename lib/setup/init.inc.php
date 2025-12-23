<?php
date_default_timezone_set('America/Sao_Paulo');

ini_set('display_errors',0);
error_reporting(0);
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE | E_COMPILE_ERROR);

// Carrega as classes se necessário
require_once("autoload.inc.php");

// Configurações do Banco de Dados
define("BD_HOST", "localhost");
define("BD_USER", "msgestorcontabil_novosite");
define("BD_PASS", "ASUgMA7o}~jD");
define("BD_DB", "msgestorcontabil_novosite");

// Carrega constantes do projeto
$config = new Configuracao();
$config->getListAll();

// verifica o protocolo utilizado
$protocolo = 'https://';//(strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false) ? 'http://' : 'https://';
define("PROTOCOLO", $protocolo);

// define a urls básicas para utilização no site
define("BASE_SITE_URL", PROTOCOLO . SITE_URL);
define("UPLOADS_URL", BASE_SITE_URL . "/uploads");
define("BASE_CSS_URL", BASE_SITE_URL . "/view/css");
define("BASE_JS_URL", BASE_SITE_URL . "/view/js");
define("BASE_IMAGES_URL", BASE_SITE_URL . "/view/images");
define("BASE_AJAX_URL", BASE_SITE_URL . "/view/ajax");

// define a urls básicas para utilização no admin
define("BASE_ADMIN_URL", PROTOCOLO . SITE_URL . "/gestao");
define("BASE_ADMIN_HELP", BASE_ADMIN_URL . "/modules");
define("ADMIN_CSS_URL", BASE_ADMIN_URL . "/css");
define("ADMIN_JS_URL", BASE_ADMIN_URL . "/js");
define("ADMIN_IMAGES_URL", BASE_ADMIN_URL . "/images");
define("RPP", 50);