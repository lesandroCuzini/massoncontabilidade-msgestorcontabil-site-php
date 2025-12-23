<?php

# Diretório de CSS do site
$path = getcwd().'/view/css';

# Nome do arquivo compactado
$file_compact = "3dots.min.css";

# Data/hora última alteração do arquivo compactado
if (file_exists($path.DIRECTORY_SEPARATOR.$file_compact))
    $time_last_update = filemtime($path.DIRECTORY_SEPARATOR.$file_compact);
else
    $time_last_update = 0;
# Ler todos arquivos de um diretório dentro dos selecionados neste array
$types = array('css');

# Desconsiderar arquivos padrão
$files_default = array(
    $file_compact,
    'fonts.css',
    'font-awesome.css',
    'style.css'
);
# Recuperam todos arquivos dentre os tipos autorizados dentro do diretório
$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);

$atualizar = false;
$files = array('/fonts.css', '/font-awesome.css');
foreach($files as $file) {
    if (filemtime($path . $file) > $time_last_update)
        $atualizar = true;
}
foreach ($dir as $fileInfo) {
    $ext = strtolower($fileInfo->getExtension());
    if(in_array($ext, $types) && !in_array($fileInfo->getFilename(), $files_default)) {
        if (file_exists($fileInfo->getPathname())) {
            $files[] = str_replace($path, '', $fileInfo->getPathname());
            if (filemtime($fileInfo->getPathname()) > $time_last_update)
                $atualizar = true;
        }
    }
}
if (filemtime($path.'/style.css') > $time_last_update)
    $atualizar = true;

$files[] = '/style.css';
# Cria o novo arquivo compacto
if ($atualizar) {
    $debug = false;
    $screen = true;
    $date_atu = date('Y-m-d');
    $expires = date('Y-m-d', strtotime("+7 days",strtotime($date_atu)));
    $m = new MinifyFile($debug, $screen, $path);
    $m->compression(true);             // can be true/false. enables the gzip compression
    $m->cache(true);                   // can be true/false. enables header for caching
    $m->uglify(false);                 // can be true/false. uglify js codes
    $m->expires($expires);                  // a string that defines the expiration date
    $m->charset('utf-8');           // the charset. default is utf-8
    if (count($files) > 1)
        $m->files($files);                  // an array of strings containing files paths
    else
        $m->file($files[0]);                // when only one file, a string with file path
    $css_output = $m->render(true);  // renders the output.

    # Cria o novo arquivo com novo conteúdo
    $fp = fopen($path.DIRECTORY_SEPARATOR.$file_compact, "w");
    $escrita = fwrite($fp, $css_output);
    fclose($fp);
}