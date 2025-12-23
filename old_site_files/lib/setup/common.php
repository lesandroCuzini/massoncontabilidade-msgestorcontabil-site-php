<?php
/**
 * Limpa requests de sql injections
 * @param String $data
 * @return String
 */
function clearInjection($data)
{

    $data = strip_tags($data);
    $data = trim($data);
    $data = get_magic_quotes_gpc() == 0 ? addslashes($data) : $data;
    $data = preg_replace('@(--|\#|\*|;)@s', "", $data);

    return $data;
}

/**
 * Função para formatar a data de várias formas tanto para usuário quanto para o banco de dados
 * @author Dheyson Wildny
 *
 * @param String $tipo user['dd/mm/aaaa hh:mm:ss'], user2['dd/mm/aaaa'], bd['aaaa-mm-dd'], bd2['aaaa-mm-dd hh:mm:ss'], data['dd/mm/aaaa']
 * @param $data
 * @return String
 */
function formatarData($tipo, $data)
{

    if ($data != "") {
        if ($tipo == "user") {
            $data_hora = explode(" ", $data);
            $array_data = explode("-", $data_hora[0]);
            $data_formatada = $array_data[2] . "/" . $array_data[1] . "/" . $array_data[0] . " " . $data_hora[1];
        } elseif ($tipo == "user2") {
            $data_hora = explode(" ", $data);
            $array_data = explode("-", $data_hora[0]);
            $data_formatada = $array_data[2] . "/" . $array_data[1] . "/" . $array_data[0];
        } elseif ($tipo == "bd") {
            $array_data = explode("/", $data);
            $data_formatada = $array_data[2] . "-" . $array_data[1] . "-" . $array_data[0];
        } elseif ($tipo == "bd2") {
            $data_hora = explode(" ", $data);
            $array_data = explode("/", $data_hora[0]);
            $data_formatada = $array_data[2] . "-" . $array_data[1] . "-" . $array_data[0] . " " . $data_hora[1];
        } elseif ($tipo == "data") {
            if (strpos($data, ' ')) {
                $data = explode(' ', $data);
                $data = $data[0];
            }
            if (strpos($data, '-')) {
                $array_data = explode('-', $data);
                $data_formatada = $array_data['2'] . '/' . $array_data['1'] . '/' . $array_data['0'];
            } else {
                $data_formatada = $data;
            }
        } else
            $data_formatada = $data;
        return $data_formatada;
    }
}

/**
 * Função para formatar a hora para o usuário
 * @author Dheyson Wildny
 *
 * @param string $hora
 * @return string
 */
function formatarHora($hora)
{
    $array_hora = explode(":", $hora);
    $hora_formatada = $array_hora['0'] . ":" . $array_hora['1'];
    return $hora_formatada;
}

/**
 * Função para formatar o telefone
 * @author Dheyson Wildny
 *
 * @param String $telefone
 * @param string $tipo
 * @return String
 */
function formatarTelefone($telefone, $tipo = "user")
{
    $fone_formatado = "";
    if ($tipo == "user") {
        if (strlen($telefone) == 10) {
            $fone_formatado = "(" . substr($telefone, 0, 2) . ")" . substr($telefone, 2, 4) . "-" . substr($telefone, -4);
        } elseif (strlen($telefone) == 11) {
            $fone_formatado = "(" . substr($telefone, 0, 2) . ")" . substr($telefone, 2, 5) . "-" . substr($telefone, -4);
        }
    } elseif ($tipo == "bd") {
        $caracteres = array('(', ')', ' ', '-');
        $fone_formatado = str_replace($caracteres, "", $telefone);
    }
    return $fone_formatado;
}

/**
 * Remover os acentos de uma string
 *
 * @param string $str
 * @return string
 */
function removerAcento($str)
{

    $from = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ',
        'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç',
        'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù',
        'Ü', 'Ú', 'Ÿ');

    $to = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n',
        'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C',
        'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U',
        'U', 'U', 'Y');

    $str_format = str_replace($from, $to, $str);

    return $str_format;
}

/**
 * Remover os caracteres especiais
 *
 * @param string $str
 * @return string
 */
function removerCaracteres($str)
{

    $from = array('?', '#', ',', '\'', '"', '-', '$', '*', '!', ".", "/", "+", ":");

    $str_format = str_replace($from, "", $str);

    return $str_format;
}

/**
 *  Monta a URL Amigavel da string
 */
function url_amigavel($str)
{
    return str_replace(" ", "-", str_replace("  ", " ", strtolower(removerCaracteres(removerAcento($str)))));
}

/**
 * Encapsula a obtenção de valor de uma sessão, testando se a mesma existe, podendo ser uma sessao unica, ou uma array de sessoes
 * @author Eduardo
 *
 * @param mixed $campo
 * @param mixed $array
 * @return string
 */
function getSession($campo, $array = "")
{
    if ($array != "")
        return isset($_SESSION[$array][$campo]) ? $_SESSION[$array][$campo] : "";
    return isset($_SESSION[$campo]) ? $_SESSION[$campo] : "";
}

/**
 * Encapsula a inclusão/alteração de valor de uma sessão, testando se a mesma existe, podendo ser uma sessao unica, ou uma array de sessoes
 * @author Eduardo
 *
 * @param mixed $valor
 * @param mixed $campo
 * @param mixed $array
 */
function setSession($valor, $campo, $array = "")
{
    if ($array != "") {
        if ($valor == null) unset($_SESSION[$array][$campo]);
        else $_SESSION[$array][$campo] = $valor;
    } else {
        if ($valor == null) unset($_SESSION[$campo]);
        else $_SESSION[$campo] = $valor;
    }
}

/**
 * Encapsula a obtenção de valores vindos de submits, sejam POST ou GET
 * @author Eduardo
 *
 * @param string $index
 * @return String
 */
function getRequest($index)
{
    return isset($_POST[$index]) ? clearInjection($_POST[$index]) : (isset($_GET[$index]) ? urldecode(clearInjection($_GET[$index])) : "");
}

/**
 * Encapsula a obtenção de valores vindos arrays de POST
 * @author Eduardo
 *
 * @param string $index
 * @return array|string
 */
function getArrayPost($index)
{
    if (isset($_POST[$index]) && is_array($_POST[$index])) {
        $ret = array();
        foreach ($_POST[$index] as $post)
            $ret[] = is_array($post) ? $post : clearInjection($post);
        return $ret;
    }
    return "";
}

/**
 * Cria paginação de acordo com os números passados.
 * @author Dheyson Wildny
 *
 * @param $total_reg
 * @param int $pag_atual
 * @param $rpp
 * @param $url
 * @internal param int $total_pag
 * @return bool|string
 */
function paginacao($total_reg, $pag_atual, $rpp, $url)
{
    $resto = $total_reg % $rpp;
    if ($resto > 0)
        $total_pag = (($total_reg - $resto) / $rpp) + 1;
    else
        $total_pag = $total_reg / $rpp;

    $str_paginacao = "";
    if ($total_pag > 1) {
        if ($pag_atual < 5) {
            $total_laco = ($total_pag > 5 ? 5 : $total_pag);
            for ($i = 1; $i <= $total_laco; $i++) {
                $str_paginacao .= "<a href=\"$url" . $i . "\" " . ($i == $pag_atual ? 'class="ativo"' : '') . ">" . $i . "</a>\n";
            }
            if ($total_pag > 5)
                $str_paginacao .= "..\n<a href=\"$url" . $total_pag . "\">" . $total_pag . "</a>\n";

        } elseif ($pag_atual > $total_pag - 4) {
            $i = 1;
            $str_paginacao .= "<a href=\"$url" . $i . "\">" . $i . "</a>\n..\n";
            for ($i = $total_pag - 4; $i <= $total_pag; $i++) {
                $str_paginacao .= "<a href=\"$url" . $i . "\" " . ($i == $pag_atual ? 'class="ativo"' : '') . ">" . $i . "</a>\n";
            }

        } else {
            $i = 1;
            $str_paginacao .= "<a href=\"$url" . $i . "\">" . $i . "</a>\n..\n";
            for ($i = $pag_atual - 2; $i <= $pag_atual + 2; $i++) {
                $str_paginacao .= "<a href=\"$url" . $i . "\" " . ($i == $pag_atual ? 'class="ativo"' : '') . ">" . $i . "</a>\n";
            }
            $str_paginacao .= "..\n<a href=\"$url" . $total_pag . "\">" . $total_pag . "</a>\n";
        }
        return $str_paginacao;

    } else {
        return false;
    }
}

/**
 * Corta a imagem , modificado por Eduardo Galvani
 *
 * @param $imgSrc
 * @param $thumbnail_width
 * @param $thumbnail_height
 * @param $ext
 * @return null
 */
function CroppedThumbnail($imgSrc, $thumbnail_width, $thumbnail_height, $ext)
{
    list($width_orig, $height_orig) = getimagesize($imgSrc);

    if ($ext == 'jpg' || $ext == 'jpeg')
        $myImage = imagecreatefromjpeg($imgSrc);
    elseif ($ext == 'png')
        $myImage = imagecreatefrompng($imgSrc);
    elseif ($ext == 'gif')
        $myImage = imagecreatefromgif($imgSrc);
    else
        return null;

    $ratio_orig = $width_orig / $height_orig;

    if ($thumbnail_width / $thumbnail_height > $ratio_orig) {
        $new_height = $thumbnail_width / $ratio_orig;
        $new_width = $thumbnail_width;
    } else {
        $new_width = $thumbnail_height * $ratio_orig;
        $new_height = $thumbnail_height;
    }

    $x_mid = $new_width / 2;
    $y_mid = $new_height / 2;

    $process = imagecreatetruecolor(round($new_width), round($new_height));

    imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
    $thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
    imagecopyresampled($thumb, $process, 0, 0, ($x_mid - ($thumbnail_width / 2)), ($y_mid - ($thumbnail_height / 2)), $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);

    imagedestroy($process);
    imagedestroy($myImage);

    return $thumb;
}

/**
 * Função que realiza o upload de arquivos, com ou nao redimensionamento, com a opcao de subir para servidores remotos
 * @author Eduardo
 *
 * @param $file
 * @param string $dir_file
 * @param string $prefixo_nome
 * @param boolean $thumb
 * @param array $dir_thumb
 * @param array $sizes
 *
 * @return string, o nome do arquivo resultante
 */
function uploadArquivo($file, $dir_file, $prefixo_nome, $thumb = false, $dir_thumb = array(), $sizes = array()/*, $remoto = false, $dir_remoto = ""*/)
{

    $nome_imagem = $file['name'];
    preg_match('/\.(gif|png|jpg|jpeg|pdf|doc|docx|rtf|txt|xls|xlsx|ppt|pptx|cdr|mp3|wma|aac|ogg|ac3|wav|mp4|avi|rmvb|mkv|bmp|zip|rar|7z){1}$/i', $nome_imagem, $ext);
    $ext = isset($ext[1]) ? strtolower($ext[1]) : '';
    $nome_imagem_formatado = $prefixo_nome . "_" . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8)) . "." . $ext;
    $caminho = $dir_file . $nome_imagem_formatado;
    move_uploaded_file($file['tmp_name'], $caminho);
    if ($thumb) {
        for ($i = 0; $i < count($dir_thumb); $i++) {
            $caminho_thumb = $dir_file . $dir_thumb[$i] . $nome_imagem_formatado;
            $imagem_file = CroppedThumbnail($caminho, $sizes[$i][0], $sizes[$i][1], $ext);
            imagejpeg($imagem_file, $caminho_thumb, 100);
        }
        // apaga original depois de recortar tudo o que precisa
        deleteArquivo($caminho);
    }
    /*if ($remoto) {
      $conn = ftp_connect(FTP_HOST);
      if (ftp_login($conn, FTP_USER, FTP_PASS)) {
        ftp_chdir($conn, $dir_remoto);
        ftp_put($conn, $nome_imagem_formatado, $caminho, FTP_ASCII);
        if ($thumb) {
          if ($dir_thumb != "")
            ftp_chdir($conn, $dir_thumb);
          ftp_put($conn, $nome_imagem_formatado, $dir_thumb, FTP_ASCII);
        }
      }
      ftp_close($conn);
    }*/

    return $nome_imagem_formatado;
}

/**
 * Função que realiza o upload de arquivos, com ou nao redimensionamento, com a opcao de subir para servidores remotos
 * @author Eduardo
 *
 * @param $file
 * @param string $dir_file
 * @param string $nome
 * @param boolean $thumb
 * @param array $dir_thumb
 * @param array $sizes
 *
 * @return string, o nome do arquivo resultante
 */
function uploadImagem($file, $dir_file, $nome, $thumb = false, $dir_thumb = array(), $sizes = array())
{
    $nome_imagem = $file['name'];
    preg_match('/\.(gif|png|jpg|jpeg|pdf|doc|docx|rtf|txt|xls|xlsx|ppt|pptx|cdr|mp3|wma|aac|ogg|ac3|wav|mp4|avi|rmvb|mkv|bmp|zip|rar|7z){1}$/i', $nome_imagem, $ext);
    $ext = isset($ext[1]) ? strtolower($ext[1]) : '';
    $nome_imagem_formatado = $nome . "." . $ext;
    $caminho = $dir_file . $nome_imagem_formatado;
    move_uploaded_file($file['tmp_name'], $caminho);
    if ($thumb) {
        for ($i = 0; $i < count($dir_thumb); $i++) {
            $caminho_thumb = $dir_file . $dir_thumb[$i] . $nome_imagem_formatado;
            $imagem_file = CroppedThumbnail($caminho, $sizes[$i][0], $sizes[$i][1], $ext);
            imagejpeg($imagem_file, $caminho_thumb, 100);
        }
        deleteArquivo($caminho);
    }
    return $nome_imagem_formatado;
}

/**
 * Deleta arquivos do servidor local ou remoto
 * @author Eduardo
 *
 * @param string $path
 * @internal param bool $remoto
 */
function deleteArquivo($path/*, $remoto = false*/)
{
    /*if ($remoto) {
      $conn = ftp_connect(FTP_HOST);
      if (ftp_login($conn, FTP_USER, FTP_PASS)) {
        ftp_delete($conn, $path);
      }
      ftp_close($conn);
    } else {*/
    if (file_exists($path))
        unlink($path);
    //}
}

/**
 * Pega a página atual da paginação, ou redireciona em certos casos
 * @author Eduardo
 *
 * @param $total_paginas
 * @param $url
 * @param string $params opcional (para buscas)
 * @return int|String
 */
function getPagAtual($total_paginas, $url, $params = "")
{
    $pag_atual = getRequest("pag");
    if ($total_paginas < 1 && $pag_atual > 0) {
        if ($params != "")
            $params = "?" . $params;
        header("Location: " . $url . $params);
        die;
    }
    if ($pag_atual > $total_paginas) {
        if ($params != "")
            $params = "&" . $params;
        header("Location: " . $url . "?pag=" . $total_paginas . $params);
        die;
    }
    if (getRequest("pag") != "" && $pag_atual < 1) {
        if ($params != "")
            $params = "&" . $params;
        header("Location: " . $url . "?pag=1" . $params);
        die;
    }
    if ($pag_atual == "")
        $pag_atual = 1;
    return $pag_atual;
}

/**
 * Retorna a quantidade de páginas necessárias para total de itens fornecidos
 * @author Eduardo
 *
 * @param $total_registros
 * @param $rpp
 * @return float
 */
function getTotalPaginas($total_registros, $rpp)
{
    $resto = $total_registros % $rpp;
    $total_paginas = ($resto > 0) ? (($total_registros - $resto) / $rpp) + 1 : $total_registros / $rpp;
    return $total_paginas;
}

/**
 * Retorna o html e status de uma url final
 * @author Eduardo
 *
 * @param $url
 * @param $dados (referência)
 * @param $status (referência)
 */
function getUrlInfo($url, &$dados, &$status)
{
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_NOBODY => 0,
        CURLOPT_TIMEOUT => 5
    );
    $call = curl_init();
    curl_setopt_array($call, $options);
    $dados = curl_exec($call);
    $status = curl_getinfo($call);
    curl_close($call);
}

/**
 * Converte número em extenso
 *
 * @param int $number
 * @param char $genero a ou o
 *
 * @return String $ordem
 */
function getOrderByNumber($number, $genero)
{
    $string = "";
    switch ($number) {
        case 1:
            $string = "Primeir" . $genero;
            break;
        case 2:
            $string = "Segund" . $genero;
            break;
        case 3:
            $string = "Terceir" . $genero;
            break;
        case 4:
            $string = "Quart" . $genero;
            break;
        case 5:
            $string = "Quint" . $genero;
            break;
        case 6:
            $string = "Sext" . $genero;
            break;
        case 7:
            $string = "Sétim" . $genero;
            break;
        case 8:
            $string = "Oitav" . $genero;
            break;
        case 9:
            $string = "Non" . $genero;
            break;
        case 10:
            $string = "Décim" . $genero;
            break;
    }
    return $string;
}

/**
 * Quebra o ID em diretórios
 *
 * @param int $id
 *
 * @return String $diretorios
 */
function splitIdDiretorios($id)
{
    $string = "";
    if (strlen($id) > 0)
        for ($i = 0; $i < strlen($id); $i++)
            $string .= "/" . substr($id, $i, 1);

    return $string;
}


//função para trazer o mes por extenso
function mesExtenso($mes)
{
    switch ($mes) {
        case"1":
            $mesAbreviado = "Janeiro";
            break;
        case"2":
            $mesAbreviado = "Fevereiro";
            break;
        case"3":
            $mesAbreviado = "Março";
            break;
        case"4":
            $mesAbreviado = "Abril";
            break;
        case"5":
            $mesAbreviado = "Maio";
            break;
        case"6":
            $mesAbreviado = "Junho";
            break;
        case"7":
            $mesAbreviado = "Julho";
            break;
        case"8":
            $mesAbreviado = "Agosto";
            break;
        case"9":
            $mesAbreviado = "Setembro";
            break;
        case"10":
            $mesAbreviado = "Outubro";
            break;
        case"11":
            $mesAbreviado = "Novembro";
            break;
        case"12":
            $mesAbreviado = "Dezembro";
            break;
        default:
            $mesAbreviado = "";
    }
    return $mesAbreviado;
}

//função para trazer o dia da semana de determinada data
function diaSemana($dia, $mes, $ano)
{
    $diasemana = date("w", mktime(0, 0, 0, $mes, $dia, $ano));
    switch ($diasemana) {
        case"0":
            $diasemana = "dom";
            break;
        case"1":
            $diasemana = "seg";
            break;
        case"2":
            $diasemana = "ter";
            break;
        case"3":
            $diasemana = "qua";
            break;
        case"4":
            $diasemana = "qui";
            break;
        case"5":
            $diasemana = "sex";
            break;
        case"6":
            $diasemana = "sab";
            break;
    }
    return $diasemana;
}

//função para gerar senha automática aleatória
function geraSenha($tamanho = 8, $maiusculas = true, $numeros = true)
{
    $lmin = 'abcdefghijklmnopqrstuvwxyz';
    $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $num = '1234567890';
    $simb = '!@#$%*-';

    $retorno = '';
    $caracteres = '';
    $caracteres .= $lmin;

    if ($maiusculas)
        $caracteres .= $lmai;

    if ($numeros)
        $caracteres .= $num;

    $len = strlen($caracteres);
    for ($n = 1; $n <= $tamanho; $n++) {
        $rand = mt_rand(1, $len);
        $retorno .= $caracteres[$rand - 1];

    }

    return $retorno;
}

function getMedia($path, $id_table, $url_imagem = false)
{
    $dir = '';
    $dir .= $path;
    $dir .= splitIdDiretorios($id_table) . '/';
    if ($url_imagem)
        $dir .= $url_imagem;
    return $dir;
}

function createMedia($path, $id_table)
{
    $dir = '';
    $dir .= $path;
    $dir .= splitIdDiretorios($id_table) . '/';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    return $dir;
}
