<?php
require_once('sdk/src/facebook.php');
require_once('AppInfo.php');
require_once('utils.php');

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
    'appId'  => AppInfo::appID(),
    'secret' => AppInfo::appSecret()
));


// Get User ID
$user = $facebook->getUser();

//Check for logged in user
if ($user) {

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
                $workplacename = $work['employer']['name'];
                $workplacename = str_replace('"'," ",$workplacename);
                    $FriendHaveTitle[$workplacename][]=array(
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
    
    // using this app
    $app_using_friends = $facebook->api(array(
        'method' => 'fql.query',
        'query' => 'SELECT uid, name FROM user WHERE uid IN(SELECT uid2 FROM friend WHERE uid1 = me()) AND is_app_user = 1'
    ));
}
   ?>