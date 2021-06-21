<?php

date_default_timezone_set('Europe/Bratislava');
setlocale(LC_ALL, 'sk_SK');

session_start();

$db = mysqli_connect($DB_HOST, $DB_LOGIN, $DB_PASSWORD, $DB_DATABASE);

include('functions.php');
include('Forms/GET_reset.php');
include('Forms/log_out.php');
include('Forms/password_request.php');
include('Forms/password_reset.php');
include('Forms/sign_up.php');
