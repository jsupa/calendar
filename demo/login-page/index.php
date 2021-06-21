<?
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

include('load.php');

?>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400" />
    <link rel="stylesheet" href="./style/style.css?v=<? echo RandomString(); ?>" />
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
                        <span><? echo date("M"); ?></span>
                    </div>
                    <div class="calendar-inner__day">
                        <span><? echo date("j"); ?></span>
                    </div>
                </div>
            </div>
            <div class="welcome_text">
                <? if (!$_SESSION['valid_reset_token'] && !$_SESSION['user']) { ?>
                    <h1>Welcome</h1>
                    <h4>Sing up to continue.</h4>
                <? } else if ($_SESSION['valid_reset_token']) { ?>
                    <h1>Password Reset</h1>
                    <h4>Enter your new password below.</h4>
                <? } else if ($_SESSION['user']) { ?>
                    <h1>Welcome</h1>
                    <h4><? echo "{$_SESSION['first_name']} {$_SESSION['last_name']}"; ?></h4>
                <? } ?>
            </div>
            <div class="login_form">
                <form method="POST" autocomplete="off">

                    <? if (isset($_SESSION['warn'])) { ?>
                        <div class="input warn">
                            <i class="fa-regular fa-triangle-exclamation"></i>
                            <p><? echo $_SESSION['warn']; ?></p>
                        </div>
                    <? } else if (isset($_SESSION['alert'])) { ?>
                        <div class="input alert">
                            <i class="fa-regular fa-circle-question"></i>
                            <p><? echo $_SESSION['alert']; ?></p>
                        </div>
                    <? } ?>

                    <? if (!$_SESSION['valid_reset_token'] && !$_SESSION['user']) { ?>
                        <div class="input">
                            <h6>cloud</h6>
                            <i class="fa-regular fa-briefcase"></i>
                            <input type="text" placeholder="company_name" name="company_name" value="<? if (!empty($company_name)) {
                                                                                                            echo $company_name;
                                                                                                        } ?>" />
                        </div>
                        <div class="input">
                            <h6>user email</h6>
                            <i class="fa-regular fa-envelopes-bulk"></i>
                            <input type="text" placeholder="example@email.email" name="email" value="<? if (!empty($email)) {
                                                                                                            echo $email;
                                                                                                        } ?>" />
                        </div>
                        <div class="input">
                            <h6>password</h6>
                            <i class="fa-regular fa-lock-keyhole"></i>
                            <input type="password" placeholder="password" name="password" />
                        </div>
                        <button type="submit" name="sign_up">SIGN UP</button>
                        <? if (isset($_SESSION['wrongPassShow'])) { ?>
                            <button type="submit" name="password_request" class="resetPass">FORGOT PASSWORD?</button>
                        <? }
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
                        <button type="submit" name="password_reset">SUBMIT</button>
                    <? } else if ($_SESSION['user']) { ?>
                        LOGEDIN :D

                        <button type="submit" name="log_out" style="background: #c12940; color:#e0a5ae">SIGN OUT</button>
                    <? } ?>
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
<?
unset($_SESSION['warn']);
unset($_SESSION['valid_reset_token']);
unset($_SESSION['alert']);
?>