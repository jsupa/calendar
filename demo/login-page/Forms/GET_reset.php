<?php

if (isset($_GET['reset'])) {
    $passResetID = mysqli_real_escape_string($db, $_GET['reset']);
    $company_name = mysqli_real_escape_string($db, $_GET['cloud']);
    $getUserSQL = "SELECT * FROM $company_name WHERE resetID = '$passResetID'";
    $result = mysqli_query($db, $getUserSQL);
    $result = mysqli_fetch_assoc($result);
    if ($result['resetTime'] <= microtime(true)) {
        $_SESSION['warn'] = $DEFAULT_LANGUAGE['INVALID_LINK'];
        header('location: https://creepy-corp.eu/git/jsupa/calendar/demo/login-page/php');
        die();
    } else {
        $_SESSION['valid_reset_token'] = true;
        $_SESSION['alert'] = "{$result['first_name']} {$result['last_name']}";
    }
}
