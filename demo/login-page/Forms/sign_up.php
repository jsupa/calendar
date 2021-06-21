<?php

if (isset($_POST['sign_up'])) {
    $company_name = mysqli_real_escape_string($db, $_POST['company_name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if (empty($company_name)) {
        $_SESSION['warn'] = $DEFAULT_LANGUAGE['DOMAIN_REQUIRE'];
    } else if (empty($email)) {
        $_SESSION['warn'] = $DEFAULT_LANGUAGE['USER_REQUIRE'];
    } else if (empty($password)) {
        $_SESSION['warn'] = $DEFAULT_LANGUAGE['PASS_REQUIRE'];
    } else {
        $searchCompanyName = 'SELECT * FROM ' . $company_name;
        $searchCompanyName = mysqli_query($db, $searchCompanyName);
        if (empty($searchCompanyName)) {
            $_SESSION['warn'] = $DEFAULT_LANGUAGE['DOMAIN_NOT_EXIST'];
        } else {
            $searchCompanyName = mysqli_num_rows($searchCompanyName);
            $validateEmailSQL = "SELECT * FROM $company_name WHERE email = '$email'";
            $result = mysqli_query($db, $validateEmailSQL);
            if (mysqli_num_rows($result) == 0) {
                $_SESSION['warn'] = $DEFAULT_LANGUAGE['USER_NOT_EXIST'];
            } else {
                $password = md5($password);
                $validateEmailSQL = "SELECT * FROM $company_name WHERE email = '$email' AND password_md5 = '$password'";
                $result = mysqli_query($db, $validateEmailSQL);
                if (mysqli_num_rows($result) == 0) {
                    $_SESSION['warn'] = $DEFAULT_LANGUAGE['WRONG_PASS'];
                    $_SESSION['wrongPassShow'] = 1;
                } else {
                    $result = mysqli_fetch_assoc($result);
                    $_SESSION['alert'] = "Logged in as {$result['first_name']} {$result['last_name']}";
                    $_SESSION['user'] = $result['email'];
                    $_SESSION['first_name'] = $result['first_name'];
                    $_SESSION['last_name'] = $result['last_name'];
                    $_SESSION['cloud'] = $company_name;
                    header('location: https://creepy-corp.eu/git/jsupa/calendar/demo/login-page/php');
                    die();
                }
            }
        }
    }
}
