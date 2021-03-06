<?php

/**
 * This class provides static methods that return pieces of data specific to
 * your app
 */
class AppInfo {

  /*****************************************************************************
   *
   * These functions provide the unique identifiers that your app users.  These
   * have been pre-populated for you, but you may need to change them at some
   * point.  They are currently being stored in 'Environment Variables'.  To
   * learn more about these, visit
   *   'http://php.net/manual/en/function.getenv.php'
   *    $fbconfig['appid' ] = '1405691073005953';
    $fbconfig['secret'] = ';

    $fbconfig['baseUrl']    =   "https://where-my-friends-work.herokuapp.com/";
    $fbconfig['appBaseUrl'] =   "https://apps.facebook.com/wheremyfriendswork";
   ****************************************************************************/

  /**
   * @return the appID for this app
   */
  public static function appID() {
     return '1405691073005953';
   // return getenv('FACEBOOK_APP_ID');
  }

  /**
   * @return the appSecret for this app
   */
  public static function appSecret() {
      return '281d8de437d9953b762f9869947011bc';
//    return getenv('FACEBOOK_SECRET');
  }

  /**
   * @return the url
   */
  public static function getUrl($path = '/') {
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
      || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
    ) {
      $protocol = 'https://';
    }
    else {
      $protocol = 'http://';
    }

    return $protocol . $_SERVER['HTTP_HOST'] . $path;
  }

}
