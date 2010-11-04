<?php
/*
Plugin Name: wp-apontador
Plugin URI: http://labs.apontador.com.br/projects/wp-apontador
Description: Exposes the Apontador API to wordpress
Version: 0.1
Author: RandD@lbslocal.com
Author URI: http://labs.lbslocal.com
	License: Apache
*/
require dirname(__file__) . '/ApontadorApiLib.php';
require dirname(__file__) . '/class/widget.php';

// default CONSUMERKEY and CONSUMERSECRET
define('WPAPONTADORKEY','cXZLsC40GBhRjC7Op1htM9YGExyxnEPk-NjjLaPvdhs~');
define('WPAPONTADORSECRET','FvUU-24mvQBTZEqMPFlAztpJAFQ~');

add_action('widgets_init', 'ApontadorInit');
add_filter('init','ApontadorGetParms');
add_action('wp_footer', 'wp_apontador_footer');
// create custom plugin settings menu
add_action('admin_menu', 'apontador_create_menu');
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'wp-apontador', null, $plugin_dir );

function wp_apontador_footer() {
  echo '<div id="apontadorfooter" align="center"><small>Powered by</small><a href="http://www.apontador.com.br"><img border="0" src="' . plugins_url('/images/apontador.png', __FILE__) . '"></a></div>' ;
}


function cutstr($str, $length, $ellipsis=''){
  $cut=explode("\n",wordwrap($str,$length));
  return $cut[0].((strlen($cut[0])<strlen($str))?$ellipsis:'');
}


/**
 * Register Hello World widget.
 *
 * Calls 'widgets_init' action after the Hello World widget has been registered.
 */
function ApontadorInit() {
  register_widget('ApontadorWidget');
}
function ApontadorGetParms() {
    $config['key']=get_option('consumer_key');
    $config['secret']=get_option('consumer_secret');
  if ( isset( $_GET['request_auth'] ) ) {
    $config["callbackurl"] = wp_nonce_url(get_admin_url() . "admin.php?page=wp-apontador/wp-apontador.php&apontador_oauth=1");
    apontadorRedirectAutorizacao($config);
  }
  
  if ( isset( $_GET['oauth_token'] ) ) {

    $access_token=apontadorProcessaAutorizacao($config);

    update_option('oauth_token',$access_token['oauth_token']);
    update_option('oauth_secret',$access_token['oauth_token_secret']);
    update_option('user_id',$access_token['user_id']);
   
    //had to do this in order to get rid of the GET parameters in the URl or "save changes" would trigger the authorization again and again, there's probably a better way to do it (tm)
    header("Location: " . get_admin_url() . 'admin.php?page=wp-apontador/wp-apontador.php');
  }

}


function apontador_create_menu() {

  //create new top-level menu
  add_menu_page('Apontador Settings', 'Apontador Settings', 'administrator', __FILE__, 'apontador_settings_page',plugins_url('/images/icon.png', __FILE__));

  //call register settings function
  add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
  //register our settings
  register_setting( 'apontador-settings-group', 'consumer_key' );
  register_setting( 'apontador-settings-group', 'consumer_secret' );
  register_setting( 'apontador-settings-group', 'oauth_token' );
  register_setting( 'apontador-settings-group', 'oauth_secret' );
}

function apontador_settings_page() {
  settings_fields( 'apontador-settings-group' ); 

  $oauth_token=get_option('oauth_token');
  $oauth_secret=get_option('oauth_secret');
  $consumer_key=get_option('consumer_key');
  $consumer_secret=get_option('consumer_secret');

  if (!$consumer_key and ! $consumer_secret) {
    // use the default consumer key and secret

    $consumer_key=WPAPONTADORKEY;
    $consumer_secret=WPAPONTADORSECRET;
    update_option('consumer_key',$consumer_key);
    update_option('consumer_secret',$consumer_secret);
  }

  if ($oauth_secret) {
    $config['key']=$consumer_key;
    $config['secret']=$consumer_secret;
    $metodo='users/self';
    $params['type']='json';

    $content=apontadorChamaApi("GET", $metodo, $params, $oauth_token, $oauth_secret,$config);

    $user=json_decode($content,true);
  }

  include dirname(__FILE__) . "/admin/settings.php";
}
