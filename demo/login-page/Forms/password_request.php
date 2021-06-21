<?php


if (isset($_POST['password_request'])) {
    $company_name = mysqli_real_escape_string($db, $_POST['company_name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);

    if (empty($company_name)) {
        $_SESSION['warn'] = $DEFAULT_LANGUAGE['DOMAIN_REQUIRE'];
    } else if (empty($email)) {
        $_SESSION['warn'] = $DEFAULT_LANGUAGE['USER_REQUIRE'];
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
                $result = mysqli_fetch_assoc($result);
                $code = $result['resetID'];

                $to = $email;
                $subject = $DEFAULT_LANGUAGE['RESTART_PASS'];
                $from = $DEFAULT_LANGUAGE['EMAIL_ADDRESS'];

                // To send HTML mail, the Content-type header must be set
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

                // Create email headers
                $headers .= 'From: ' . $DEFAULT_LANGUAGE['EMAIL_NAME'] . ' <' . $from . ">\r\n" .
                    'Reply-To: ' . $DEFAULT_LANGUAGE['EMAIL_REPLY'] . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

                // Compose a simple HTML email message
                $message = '<html><body>';
                $message .= '<h1 style="color:#f40;">Hi! ' . $result['first_name'] . ' ' . $result['last_name'] . '</h1>';
                $message .= '<p style="color:#080;font-size:18px;">Here is your restart link valid for 5 min.</p>';
                $message .= '<a href="https://creepy-corp.eu/git/jsupa/calendar/demo/login-page/php/?reset=' . $code . '&cloud=' . $company_name . '">RESET</a>';
                $message .= '</body></html>';

                // Sending email
                if (mail($to, $subject, $message, $headers) && $result['resetID']) {
                    $_SESSION['alert'] = $DEFAULT_LANGUAGE['EMAIL_SENT'];
                    unset($_SESSION['wrongPassShow']);
                    $resetTime5min = microtime(true) + 300;
                    $query = "UPDATE $company_name SET resetTime = '$resetTime5min' WHERE email = '$email'";
                    $results = mysqli_query($db, $query);
                    header('location: https://creepy-corp.eu/git/jsupa/calendar/demo/login-page/php');
                    die();
                } else {
                    $_SESSION['warn'] = $DEFAULT_LANGUAGE['EMAIL_ERROR'];
                }
            }
        }
    }
}
