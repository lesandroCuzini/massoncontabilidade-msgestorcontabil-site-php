<?php
/* @copyright 2021 3dots
*
* DISCLAIMER
*
* Do not edit or add anything to this file
* If you need to edit this file consult 3dots.
*
*  @author 3dots <contato@3dots.com.br>
*  @copyright 2021 3dots
*  @version  1.0
*
*/

#armazena o arquivo informado
$file = $_GET["file"];

if(isset($file) && file_exists($file)){
  $extension = strtolower(substr(strrchr(basename($file),"."),1));
  $name_file = str_replace($extension, "", basename($file));

  switch($extension) {
    case "pdf": $tipo="application/pdf"; break;
    case "exe": $tipo="application/octet-stream"; break;
    case "zip": $tipo="application/zip"; break;
    case "doc": $tipo="application/msword"; break;
    case "docx": $tipo="application/msword"; break;
    case "xls": $tipo="application/vnd.ms-excel"; break;
    case "xlsx": $tipo="application/vnd.ms-excel"; break;
    case "ppt": $tipo="application/vnd.ms-powerpoint"; break;
    case "pptx": $tipo="application/vnd.ms-powerpoint"; break;
    case "gif": $tipo="image/gif"; break;
    case "png": $tipo="image/png"; break;
    case "jpg": $tipo="image/jpg"; break;
    case "mp3": $tipo="audio/mpeg"; break;
    case "php": // deixar vazio por seurança
    case "phtml": // deixar vazio por seurança
    case "htm": // deixar vazio por seurança
    case "html": // deixar vazio por seurança
  }

  header("Content-Type: ".$tipo);
  header("Content-Length: ".filesize($file));
  header("Content-Disposition: attachment; filename=".basename($name_file));
  header("Pragma: no-cache");
  readfile($file);
  die;
}