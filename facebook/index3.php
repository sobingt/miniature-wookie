<?php

require_once('AppInfo.php');

// Enforce https on production
if (substr(AppInfo::getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
    header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');


/*****************************************************************************
 *
 * The content below provides examples of how to fetch Facebook data using the
 * Graph API and FQL.  It uses the helper functions defined in 'utils.php' to
 * do so.  You should change this section so that it prepares all of the
 * information that you want to display to the user.
 *
 ****************************************************************************/

require_once('sdk/src/facebook.php');


$facebook = new Facebook(array(
    'appId'  => AppInfo::appID(),
    'secret' => AppInfo::appSecret(),
//  'sharedSession' => true,
//  'trustForwarded' => true,
));

$user_id = $facebook->getUser();
if ($user_id) {
    try {
        // Fetch the viewer's basic information
        $basic = $facebook->api('/me');
    } catch (FacebookApiException $e) {
        // If the call fails we check if we still have a user. The user will be
        // cleared if the error is because of an invalid accesstoken
        if (!$facebook->getUser()) {
            header('Location: '. AppInfo::getUrl($_SERVER['REQUEST_URI']));
            exit();
        }
    }

    // This fetches some things that you like . 'limit=*" only returns * values.
    // To see the format of the data you are retrieving, use the "Graph API
    // Explorer" which is at https://developers.facebook.com/tools/explorer/

    $accessToken = $facebook->getAccessToken();
    $friends = $facebook->api('me/friends?fields=id,name,work&access_token='.$accessToken.'');


    $FriendHaveTitle = array();
    $FriendDontHaveTitle=array();

    foreach($friends['data'] as $friend) {

        if(array_key_exists('work',$friend)){
            $temp = false; //if friend have work details but don't have employer
            //foreach($friend['work'] as $work){
                $work=$friend['work'][0];
                if(array_key_exists('employer',$work)){
                    $FriendHaveTitle[$work['employer']['name']][]=array(
                        'id'=>$friend['id'],
                        'name'=>$friend['name']
                    );
                    $temp = true;
                }

            //}
            if($temp===false){
                $FriendDontHaveTitle[]=array(
                    'id'=>$friend['id'],
                    'name'=>$friend['name'],
                );
            }
        }
        else{
            $FriendDontHaveTitle[]=array(
                'id'=>$friend['id'],
                'name'=>$friend['name'],
            );
        }


    }

    // using this app
    $app_using_friends = $facebook->api(array(
        'method' => 'fql.query',
        'query' => 'SELECT uid, name FROM user WHERE uid IN(SELECT uid2 FROM friend WHERE uid1 = me()) AND is_app_user = 1'
    ));
}

// Fetch the basic info of the app that they are using
$app_info = $facebook->api('/'. AppInfo::appID());
$app_name = idx($app_info, 'name', '');

?>
<!DOCTYPE html>
<html lang="en" class="demo-4">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <meta name="description" content="codeniti" />
        <meta name="author" content="sobingt" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="css/bootstrap.min.css" />
        <link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
		<title>Where my friend works?</title>
        <meta property="og:title" content="<?php echo he($app_name); ?>" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="<?php echo AppInfo::getUrl(); ?>" />
        <meta property="og:image" content="<?php echo AppInfo::getUrl('/logo.png'); ?>" />
        <meta property="og:site_name" content="<?php echo he($app_name); ?>" />
        <meta property="og:description" content="My first app" />
        <meta property="fb:app_id" content="<?php echo AppInfo::appID(); ?>" />
        
		<meta name="author" content="Codrops" />
		<link rel="shortcut icon" href="../favicon.ico"> 
		<link rel="stylesheet" type="text/css" href="css/style.css" />
                <link rel="stylesheet" href="css/fontello.css" />
                <link rel="stylesheet" href="css/fonts.css" />
		<script src="js/modernizr.custom.63321.js"></script>
                <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="js/jquery.ui.map.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Racing+Sans+One' rel='stylesheet' type='text/css'>
		<!--[if lte IE 9]><style>.main{display:none;} .support-note .note-ie{display:block;}</style><![endif]-->
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
        ['Language', 'Speakers (in millions)'],
        ]);

        var options = {
          title: 'Indian Language Use',
          legend: 'none',
          pieSliceText: 'label',
          slices: {  4: {offset: 0.2},
                    12: {offset: 0.3},
                    14: {offset: 0.4},
                    15: {offset: 0.5},
          },
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
      }
    </script>
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

        // Listen to the auth.login which will be called when the user logs in
        // using the Login button
        FB.Event.subscribe('auth.login', function(response) {
            // We want to reload the page now so PHP can read the cookie that the
            // Javascript SDK sat. But we don't want to use
            // window.location.reload() because if this is in a canvas there was a
            // post made to this page and a reload will trigger a message to the
            // user asking if they want to send data again.
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
			<header>
			
				<h1>Where my friend<strong> works?</strong></h1>
				<h2>Find out all about your friends workplace</h2>

				<div class="support-note">
					<span class="note-ie">Sorry, only modern browsers.</span>
				</div>
				
			</header>
            <?php if (isset($basic)) { ?>
    <h2>Welcomes <strong><?php echo he(idx($basic, 'name')); ?></strong></h2>
    <p>Here is your Friends Clusters According to their Titles:</p>
<?php } else { ?>
    <div>
        <h2>Welcome</h2>
        <div class="fb-login-button" data-scope="user_photos,friends_work_history"></div>
    </div>
<?php } ?>

<?php
if ($user_id) {
    ?>
    <!-- here we gone display list -->
    <?php
if(count($FriendHaveTitle)> 0){
    foreach($FriendHaveTitle as $title=>$friends) {?>
        

            <div class="span4">
                <div class="row">
                    <a href="https://facebook.com/189804254401656">
                        <h4><b><?php echo $title;?></b></h4>
                    </a>
                </div>
                <div class="row">
                <?php foreach ($friends as $friend) {
                    // Extract the pieces of info we need from the requests above
                    $id = idx($friend, 'id');
                    $name = idx($friend, 'name');
                ?>
                <a href="https://facebook.com/<?php echo he($id); ?>"><img src="https://graph.facebook.com/<?php echo he($id); ?>/picture?width=50&height=50"></a>
                <?php }?>
                </div>
                <div class="row">
                    <div style="float:left;"><span class="iconf-user"><?php echo count($friends); ?></span> <b>work here</b></div>
                    <div style="float:right;">
                        <div class="rating">
                            <span>☆</span><span>☆</span><span>☆</span><span>☆</span><span>☆</span>
                        </div>
                    </div>
                </div>
            </div>

        
        <?php }
    }
    else{
        echo "no one specified their title/titles in your friend list";
    }
    ?>
			<section class="main">
				<!-- the component 
				<div class="wrapper">
					<div class="inner">
                        <span>L</span>
						<span>o</span>
						<span>a</span>
						<span>d</span>
						<span>i</span>
						<span>n</span>
						<span>g</span>
					</div>
				</div>-->
			</section>
		</div>
	</body>
</html>