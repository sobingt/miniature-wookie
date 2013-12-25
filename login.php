<?php

require_once('AppInfo.php');
?>
<html>
    <head>
        <title>Login</title>
        <meta name="description" content="starplus" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="css/bootstrap.min.css" />
        <link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" href="css/font-awesome.css" />
        <link rel="stylesheet" href="css/style.css" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
    <div id="fb-root"></div>
<script type="text/javascript">

    window.fbAsyncInit = function() {
        FB.init({
            appId      : '<?php echo AppInfo::appID(); ?>', // App ID
            channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
            status     : true, // check login status
            cookie     : true, // enable cookies to allow the server to access the session
            xfbml      : true // parse XFBML
        });

        FB.Event.subscribe('auth.login', function(response) {
            window.location = window.location;
        });

        FB.Canvas.setAutoGrow();
    };

    // Load the SDK Asynchronously
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

        <div class="container">
                <header class="row">
             <div class="col-md-3 ">
             </div>
             <div class="col-md-6 ">
                <div id="logo"></div>
             </div>
             <div class="col-md-3 ">
             </div>
            
        </header>
            <div class="col-md-3">
            </div>
            <div class="col-md-3">
                <a class="btn btn-block btn-lg btn-social btn-linkedin">
                    <i class="fa fa-linkedin"></i> Sign in with LinkedIn
                </a>
            </div>
            <div class="col-md-3">
                <a class="btn btn-block btn-lg btn-social btn-facebook">
                    <i class="fa fa-facebook"></i> Sign in with Facebook
                </a>
            </div>
            <div class="col-md-3">

            </div>
        </div>
        <footer id="footer" style="position:absolute;bottom:0;height:60px;width:100%;text-align:right;">
            <section>With love<img src="images/logo.png" height="20px" width="20px" />
            </section>
        </footer>
        
        <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/main.js"></script>
    </body>
</html>