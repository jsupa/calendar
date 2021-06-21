<?php

if (isset($_POST['log_out'])) {
    session_destroy();
    session_start();
    $_SESSION['alert'] = "successful signed out";
    header('location: https://creepy-corp.eu/git/jsupa/calendar/demo/login-page/php');
    die();
}
