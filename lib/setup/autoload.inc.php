<?php
//Carrega o arquivo de funções genéricas
include_once("common.php");

/**
 * Carregamento automatico de classes conforme são instanciadas no sistema
 * @param String $classe
 */
spl_autoload_register(function($classe) {
  $classe = strtolower($classe);

  if (file_exists(__DIR__."/../../lib/classes/class.{$classe}.php"))
    include_once __DIR__."/../../lib/classes/class.{$classe}.php";

  if (file_exists(__DIR__."/../../model/class.{$classe}.php"))
    include_once __DIR__."/../../model/class.{$classe}.php";

  if (file_exists(__DIR__."/../../controller/class.{$classe}.php"))
    include_once __DIR__."/../../controller/class.{$classe}.php";
});
