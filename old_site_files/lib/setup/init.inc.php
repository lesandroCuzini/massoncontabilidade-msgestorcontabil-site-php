<?php
date_default_timezone_set('America/Sao_Paulo');

//ini_set('display_errors',1);
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE | E_COMPILE_ERROR);
//ini_set('display_errors',0);
//error_reporting(0);

// Carrega as classes se necessário
require_once("autoload.inc.php");

// Configurações do Banco de Dados
define("BD_HOST", "localhost");
define("BD_USER", "msgestor_site");
define("BD_PASS", "cataF2@r");
define("BD_DB", "msgestor_site");

// Carrega constantes do projeto
$config = new Configuracao();
$config->getListAll();

// verifica o protocolo utilizado
$protocolo = (strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false) ? 'http://' : 'https://';
$protocolo = 'https://';
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
