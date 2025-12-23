<?php

include('../../../lib/setup/init.inc.php');

$objeto = new $_POST['classe']();
$objeto->setIdTable(getRequest('id_image'));
if($objeto->excluir()){
    exit('OK');
}else{
    die;
}
exit;