<?php
// if (!(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' ||
//     $_SERVER['HTTPS'] == 1) ||
//     isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
//     $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
//     $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//     header('HTTP/1.1 301 Moved Permanently');
//     header('Location: ' . $redirect);
//     exit();
// }

// demo - admin - admin

include('config.php');

date_default_timezone_set('Europe/Bratislava');
setlocale(LC_ALL, 'sk_SK');

session_start();

function RandomString($length = 20)
{
    $keys = array_merge(range(0, 9), range('a', 'z'));
    $key = "";
    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[mt_rand(0, count($keys) - 1)];
    }
    return $key;
}

$db = mysqli_connect($DB_HOST, $DB_LOGIN, $DB_PASSWORD, $DB_DATABASE);
// $db = mysqli_connect('localhost', 'root', 'root', 'planning_calendar');

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

if (isset($_POST['password_reset'])) {
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
if (isset($_POST['log_out'])) {
    session_destroy();
    session_start();
    $_SESSION['alert'] = "successful signed out";
    header('location: https://creepy-corp.eu/git/jsupa/calendar/demo/login-page/php');
    die();
}
if (isset($_POST['password_change'])) {
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
?>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400" />
    <link rel="stylesheet" href="./style/style.css?v=<?php echo RandomString(); ?>" />
    <link rel="stylesheet" href="./style/all.css" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1990 12:00:00 GMT" />
    <title>Planning calendar</title>
</head>

<body>
    <div id="main">
        <div class="left_side">
            <div class="calendar_logo">
                <div class="calendar-inner">
                    <div class="calendar-inner__month">
                        <span><?php echo date("M"); ?></span>
                    </div>
                    <div class="calendar-inner__day">
                        <span><?php echo date("j"); ?></span>
                    </div>
                </div>
            </div>
            <div class="welcome_text">
                <?php if (!$_SESSION['valid_reset_token'] && !$_SESSION['user']) { ?>
                    <h1>Welcome</h1>
                    <h4>Sing up to continue.</h4>
                <?php } else if ($_SESSION['valid_reset_token']) { ?>
                    <h1>Password Reset</h1>
                    <h4>Enter your new password below.</h4>
                <?php } else if ($_SESSION['user']) { ?>
                    <h1>Welcome</h1>
                    <h4><?php echo "{$_SESSION['first_name']} {$_SESSION['last_name']}"; ?></h4>
                <?php } ?>
            </div>
            <div class="login_form">
                <form method="POST">

                    <?php if (isset($_SESSION['warn'])) { ?>
                        <div class="input warn">
                            <i class="fa-regular fa-triangle-exclamation"></i>
                            <p><?php echo $_SESSION['warn']; ?></p>
                        </div>
                    <?php } else if (isset($_SESSION['alert'])) { ?>
                        <div class="input alert">
                            <i class="fa-regular fa-circle-question"></i>
                            <p><?php echo $_SESSION['alert']; ?></p>
                        </div>
                    <?php } ?>

                    <?php if (!$_SESSION['valid_reset_token'] && !$_SESSION['user']) { ?>
                        <div class="input">
                            <h6>cloud</h6>
                            <i class="fa-regular fa-briefcase"></i>
                            <input type="text" placeholder="company_name" name="company_name" value="<?php if (!empty($company_name)) {
                                                                                                            echo $company_name;
                                                                                                        } ?>" />
                        </div>
                        <div class="input">
                            <h6>user email</h6>
                            <i class="fa-regular fa-envelopes-bulk"></i>
                            <input type="text" placeholder="example@email.email" name="email" value="<?php if (!empty($email)) {
                                                                                                            echo $email;
                                                                                                        } ?>" />
                        </div>
                        <div class="input">
                            <h6>password</h6>
                            <i class="fa-regular fa-lock-keyhole"></i>
                            <input type="password" placeholder="password" name="password" />
                        </div>
                        <button type="submit" name="sign_up">SIGN UP</button>
                        <?php if (isset($_SESSION['wrongPassShow'])) { ?>
                            <button type="submit" name="password_reset" class="resetPass">FORGOT PASSWORD?</button>
                        <?php }
                    } else if ($_SESSION['valid_reset_token']) { ?>
                        <div class="input">
                            <h6>new password</h6>
                            <i class="fa-regular fa-lock-keyhole"></i>
                            <input type="password" placeholder="password" name="password1" />
                        </div>
                        <div class="input">
                            <h6>re-enter new password</h6>
                            <i class="fa-regular fa-lock-keyhole"></i>
                            <input type="password" placeholder="password" name="password2" />
                        </div>
                        <button type="submit" name="password_change">SUBMIT</button>
                    <?php } else if ($_SESSION['user']) { ?>
                        LOGEDIN :D

                        <button type="submit" name="log_out" style="background: #c12940; color:#e0a5ae">SIGN OUT</button>
                    <?php } ?>
                </form>
            </div>
        </div>
        <div class="right_side">
            <div id="scene">
                <img src="./img/Hodiny.png" class="layer layer1" data-depth="-0.60" />
                <img src="./img/Mobil.png" class="layer layer2" data-depth="0.20" />
                <img src="./img/Kalendar.png" class="layer layer3" data-depth="-0.20" />
                <img src="./img/Shere_icon.png" class="layer layer4" data-depth="0.5" />
                <img src="./img/Office_postavicky.png" class="layer layer5" data-depth="0.1" />
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="./parallax.js"></script>
    <script>
        $("#scene").parallax();

        $(".input").click(function() {
            $(this).find("input").focus();
        });
    </script>
</body>

</html>
<?php
unset($_SESSION['warn']);
unset($_SESSION['valid_reset_token']);
unset($_SESSION['alert']);
?>