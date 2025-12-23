<?php

date_default_timezone_set('America/Sao_Paulo');
date('Y-m-d H:i:s');

$dataIp = date('Y-m-d H:i:s') . "," . $_SERVER["REMOTE_ADDR"];

setcookie('cookieDataIp', $dataIp, time() + (86400 * 365), "/");

exit('ok');