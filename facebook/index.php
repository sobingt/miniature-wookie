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
    $workplaceWithMaxFriends=array();
    foreach($friends['data'] as $friend) {

        if(array_key_exists('work',$friend)){
            $temp = false; //if friend have work details but don't have employer
            //foreach($friend['work'] as $work){
                $work=$friend['work'][0];
                if(array_key_exists('employer',$work)){
                    $FriendHaveTitle[$work['employer']['name']][]=array(
                        'id'=>$friend['id'],
                        'name'=>$friend['name'],
                        'empid'=>$work['employer']['id'],

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
    $workplaceWithMaxFriends[0]=array(
        'id'=>123,
        'name'=>"",
        'count'=>1,
    );
    $maxCount=1;
    $secondMaxCount=1;
    if(count($FriendHaveTitle)> 0){
        foreach($FriendHaveTitle as $title=>$friends) {
            
            if(count($friends)>$maxCount)
            {
                if($maxCount!=1)
                {
                    $workplaceWithMaxFriends[1]['id']=$workplaceWithMaxFriends[0]['id'];
                    $workplaceWithMaxFriends[1]['name']=$workplaceWithMaxFriends[0]['name'];
                    $workplaceWithMaxFriends[1]['count']=$workplaceWithMaxFriends[0]['count'];
                    $secondMaxCount = $workplaceWithMaxFriends[0]['count'];
                }
                $workplaceWithMaxFriends[0]['id']=$friends[0]['empid'];
                $workplaceWithMaxFriends[0]['name']=$title;
                $workplaceWithMaxFriends[0]['count']=count($friends);
                $maxCount = count($friends);
            }
            else if(count($friends)>$secondMaxCount)
            {
                $workplaceWithMaxFriends[1]['id']=$friends[0]['empid'];
                $workplaceWithMaxFriends[1]['name']=$title;
                $workplaceWithMaxFriends[1]['count']=count($friends);
                $secondMaxCount = count($friends);
                
            }
        }
    }
    
     $msg = 'Some message would go here.';
 $title = 'Some title would go here';
 $uri = 'http://sobingt.com';
 $desc = 'Some detailed message that shows beneath the title. You can include a decent amount of text here.';
 $pic = 'http://graph.facebook.com/sobingt/picture';
 $action_name = 'Action Link in the Footer of the Post';
 $action_link = 'http://www.sobingt.com';
    
    
    	 // you're logged in, and we'll get user acces token for posting on the wall
	 try {
	 	
		 if (!empty( $accessToken )) {
				 $attachment = array(
					 'access_token' => $accessToken,
					 'message' => $msg,
					 'name' => $title,
					 'link' => $uri,
					 'description' => $desc,
					 'picture'=>$pic,
					 'actions' => json_encode(array('name' => $action_name,'link' => $action_link))
			 	  );
 
				$status = $facebook->api("/me/feed", "post", $attachment);
                
	 	 } else {
	 			$status = 'No access token recieved';
	 	 }
         echo $status;
         echo "<br>THE<br>";
 	 } catch (FacebookApiException $e) {
 				error_log($e);
 				$user = null;
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
        <script type="text/javascript" src="js/jquery.isotope.min.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
          <script>
    $(function(){
      
      var $container = $('.companyContainer');
      
      $container.isotope({
        itemSelector: '.company'
      });
      
    });
  </script>
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
    <p>Here is your Friends Clusters According to their workplace:</p>
    <h3>You got <span><?php echo $workplaceWithMaxFriends[0]['count'] ?></span> working in <span><a href="https://facebook.com/<?php echo $workplaceWithMaxFriends[0]['id'] ?>" ><?php echo $workplaceWithMaxFriends[0]['name'] ?></a><span> and <span><?php echo $workplaceWithMaxFriends[1]['count'] ?></span> working in <span><a href="https://facebook.com/<?php echo $workplaceWithMaxFriends[1]['id'] ?>" ><?php echo $workplaceWithMaxFriends[1]['name'] ?></a><span> </h3>
    
    
<?php } else { ?>
    <div class="row">
    <div class="span4">
    </div>
    <div class="span4" style="text-align: center;">
    <fb:login-button size="xlarge" onlogin="Log.info('onlogin callback')" data-scope="user_photos,friends_work_history,photo_upload,user_status,publish_stream,user_photos,manage_pages">
    Request Permission
    </fb:login-button>



    </div>
    <div class="span4">
    </div>
    </div>
    
    <div class="companyContainer">
<?php } 

if ($user_id) {

if(count($FriendHaveTitle)> 0){
    foreach($FriendHaveTitle as $title=>$friends) {
    ?>
        

            <div class="span4 companypage">
                <div class="row">
                    <a href="https://facebook.com/<?php echo he($friends[0]['empid']); ?>">
                        <h4><b><?php echo $title;?></b></h4>
                    </a>
                </div>
                <div class="row">
                <?php foreach ($friends as $friend) {
                    // Extract the pieces of info we need from the requests above
                    $id = idx($friend, 'id');
                    $name = idx($friend, 'name');
                ?>
                <a href="https://facebook.com/<?php echo he($id); ?>"><img class="facebook-profile-picture" src="https://graph.facebook.com/<?php echo he($id); ?>/picture?width=50&height=50"></a>
                <?php }?>
                </div>
                <div class="row">
                    <div style="float:left;"><span class="iconf-user"><?php echo count($friends); ?></span> <b>work here</b></div>
                </div>
                <div class="row">
                    <div style="float:left;">
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
    </div>
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
            
            <?php
}
?>
		</div>
	</body>
</html>