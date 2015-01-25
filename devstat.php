<?php

date_default_timezone_set('Asia/Tehran');

if(isset($_GET['_url']))
    $url = $_GET['_url'];
else
    $url = 'main';

if($url == '/admin'){
    require 'admin.php';
}
else
{
    require 'main.php';
}