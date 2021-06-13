<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400" />
    <link rel="stylesheet" href="./style/style.css" />
    <link rel="stylesheet" href="./style/all.css" />
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
                <h1>Welcome</h1>
                <h4>Sing up to continue.</h4>
            </div>
            <div class="login_form">
                <form action="GET">
                    <div class="input alert">
                        <i class="fa-regular fa-triangle-exclamation"></i>
                        <p>wrong pass</p>
                    </div>
                    <div class="input">
                        <h6>cloud</h6>
                        <i class="fa-regular fa-briefcase"></i>
                        <input type="text" placeholder="company_name" />
                    </div>
                    <div class="input">
                        <h6>user email</h6>
                        <i class="fa-regular fa-envelopes-bulk"></i>
                        <input type="email" placeholder="example@email.email" />
                    </div>
                    <div class="input">
                        <h6>password</h6>
                        <i class="fa-regular fa-lock-keyhole"></i>
                        <input type="password" placeholder="password" />
                    </div>
                    <button type="submit">SIGN UP</button>
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