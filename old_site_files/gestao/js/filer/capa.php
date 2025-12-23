<?php

include('../../../lib/setup/init.inc.php');

$objeto = new $_POST['classe']();
if($objeto->setCapa(getRequest('id_image'))){
    exit('OK');
}else{
    die;
}

exit;