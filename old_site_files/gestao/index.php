<?php
session_start();
if(!isset($_SESSION['info_user']['id_usuario']) || !$_SESSION['info_user']['id_usuario']){
    header('Location: login.html');
    die;
}else{
    header('Location: home.html');
    die;
}