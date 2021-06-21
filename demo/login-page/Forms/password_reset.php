<?php

if (isset($_POST['password_reset'])) {
    $passResetID = mysqli_real_escape_string($db, $_GET['reset']);
    $company_name = mysqli_real_escape_string($db, $_GET['cloud']);
    $password1 = mysqli_real_escape_string($db, $_POST['password1']);
    $password2 = mysqli_real_escape_string($db, $_POST['password2']);
    if (empty($password1) || empty($password2)) {
        $_SESSION['warn'] = $DEFAULT_LANGUAGE['PASS_REQUIRE'];
    } else if ($password1 != $password2) {
        $_SESSION['warn'] = $DEFAULT_LANGUAGE['MATCH_PASS'];
    } else if ($password1 === $password2) {
        $_SESSION['alert'] = $DEFAULT_LANGUAGE['PASS_CHANGE'];
        $to = $result['email'];
        $password = md5($password1);
        $newResetId = RandomString();
        $query = "UPDATE $company_name SET resetID = '$newResetId', password_md5 = '$password' WHERE resetID = '$passResetID'";
        $results = mysqli_query($db, $query);

        $subject = $DEFAULT_LANGUAGE['RESTARTED_PASS'];
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
        $message .= '<p style="color:#080;font-size:18px;">Yout password is changed.</p>';
        $message .= '</body></html>';
        mail($to, $subject, $message, $headers);
        unset($_SESSION['valid_reset_token']);
        header('location: https://creepy-corp.eu/git/jsupa/calendar/demo/login-page/php');
        die();
    } else {
        $_SESSION['warn'] = $DEFAULT_LANGUAGE['ERROR'];
    }
}
