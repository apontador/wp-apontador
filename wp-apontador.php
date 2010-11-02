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

// default CONSUMERKEY and CONSUMERSECRET
define('WPAPONTADORKEY','cXZLsC40GBhRjC7Op1htM9YGExyxnEPk-NjjLaPvdhs~');
define('WPAPONTADORSECRET','FvUU-24mvQBTZEqMPFlAztpJAFQ~');

require_once("ApontadorApiLib.php");


add_action('widgets_init', 'ApontadorInit');
add_filter('init','ApontadorGetParms');
// create custom plugin settings menu
add_action('admin_menu', 'apontador_create_menu');
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'wp-apontador', null, $plugin_dir );


class ApontadorWidget extends WP_Widget
{
  /**
   * Declares the ApontadorWidget class.
   *
   */
  function ApontadorWidget(){
    $widget_ops = array('classname' => 'apontador_widget', 'description' => __( "Apontador Widget") );
    $control_ops = array('width' => 300, 'height' => 300);
    $this->WP_Widget('apontador', __('Apontador Widget'), $widget_ops, $control_ops);
  }
  
  /**
   * Displays the Widget
   *
   */
  function widget($args, $instance){
    extract($args);
    $title = apply_filters('widget_title', empty($instance['title']) ? 'No Apontador' : $instance['title']);
    $howMany = empty($instance['howMany']) ? '5' : $instance['howMany'];
    $maxChars = empty($instance['maxChars']) ? '160' : $instance['maxChars'];
    $oauth_token    = get_option('oauth_token');
    $oauth_secret   = get_option('oauth_secret');
    
    # Before the widget
      echo $before_widget;
    
    # The title
      if ( $title )
	echo $before_title . $title . $after_title;
    
    # Make the Hello World Example widget
      echo '<div style="text-align:left;padding:10px;" id="apontador-widget">' ;
    
    $params['limit']=$howMany;
    $params['type']='json';
    $metodo='users/self/reviews';
    $config['key']=get_option('consumer_key');
    $config['secret']=get_option('consumer_secret');
    
    if (! $oauth_token) {
      _e('please, edit wp-apontador settings in your admin page');
    } else {
      $content=apontadorChamaApi($verbo="GET", $metodo, $params, $oauth_token, $oauth_secret,$config);
      $reviews=json_decode($content,true);
      
      foreach ( $reviews['user']['reviews'] as $item) {
	$params=array();
	$params['type']='json';
	$metodo='places/' . $item['review']['place']['id'];
	$content=apontadorChamaApi("GET", $metodo, $params,null,null,$config);
	$place=json_decode($content,true);
	
	echo '<a href="' . $place['place']['main_url'] . '"><b>' . $place['place']['name'] . '</b></a><br>';
	for ($cunt=0;$cunt<5;$cunt++) {
	  if ($cunt< $item['review']['rating']) {
	    echo "<img border=0 src=";
	    echo plugins_url('/images/star.png', __FILE__);
	    echo ">";
	  } else {
	    echo "<img border=0 src=";
	    echo plugins_url('/images/mptystar.png', __FILE__);
	    echo ">";
	  }
	};
	echo "<br>";
	echo cutstr($item['review']['content'],$maxChars,'...');
	echo '<a href="http://www.apontador.com.br/local/review/' . $item['review']['place']['id'] . '/' . $item['review']['id'] .'.html"> <small>[Veja Mais!]</small></a><br>';
	echo "<br>";
      };
      echo "</div>";
    }
    
    # After the widget
      echo $after_widget;
  }
  
  /**
   * Saves the widgets settings.
   *
   */
  function update($new_instance, $old_instance){
    $instance = $old_instance;
    $instance['title'] = strip_tags(stripslashes($new_instance['title']));
    $instance['howMany'] = strip_tags(stripslashes($new_instance['howMany']));
    $instance['maxChars'] = strip_tags(stripslashes($new_instance['maxChars']));
    
    return $instance;
  }
  
  /**
   * Creates the edit form for the widget.
   *
   */
  function form($instance){
    //Defaults
    $instance = wp_parse_args( (array) $instance, array('title'=>'', 'howMany'=>'5') );
    
    $title = htmlspecialchars($instance['title']);
    $howMany = htmlspecialchars($instance['howMany']);
    $maxChars = htmlspecialchars($instance['maxChars']);
    
    # Output the options
      echo '<p style="text-align:right;"><label for="' . $this->get_field_name('title') . '">' . __('Title:') . ' <input style="width: 250px;" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></label></p>';
    # How Many
      echo '<p style="text-align:right;"><label for="' . $this->get_field_name('howMany') . '">' . __('How Many:') . ' <input style="width: 200px;" id="' . $this->get_field_id('howMany') . '" name="' . $this->get_field_name('howMany') . '" type="text" value="' . $howMany . '" /></label></p>';
      echo '<p style="text-align:right;"><label for="' . $this->get_field_name('maxChars') . '">' . __('Max Lenght:') . ' <input style="width: 200px;" id="' . $this->get_field_id('maxChars') . '" name="' . $this->get_field_name('maxChars') . '" type="text" value="' . $maxChars . '" /></label></p>';
  }
  
}// END class

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
  echo '<div class="wrap"> <h2>Apontador Plugin</h2> <form method="post" action="options.php">';
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
  if (! $oauth_secret) {
    echo __('click') . ' <a href="' . get_admin_url() . 'admin.php?page=wp-apontador/wp-apontador.php&request_auth=1">'.__('here') . '</a> ' . __('to request an authorization token');
  } else {
    echo __('Authenticated as') . '<BR>';
    $config['key']=$consumer_key;
    $config['secret']=$consumer_secret;
    $metodo='users/self';
    $params['type']='json';
    
    $content=apontadorChamaApi("GET", $metodo, $params, $oauth_token, $oauth_secret,$config);

    $user=json_decode($content,true);
    echo '<div >';
    echo '<img style="margin-right:10px;" align="left" width="64" height="64" src="' . $user['user']['photo_url'] . '">';
    echo '<p><b>';
    echo $user['user']['name'] .'</b><br>';
    echo $user['user']['stats']['reviews']. __(' reviews') . '<BR/>';
    echo $user['user']['stats']['photos'] . __(' photos')  . '<BR/>';
    echo $user['user']['stats']['places'] . __(' places')  . '<BR/>';
    echo '</p></div>';
    echo __('click') . '<a href="' . get_admin_url() . 'admin.php?page=wp-apontador/wp-apontador.php&request_auth=1">'.__(' here ') . '</a>' . __('to request a new authorization token');
  }
  echo '<table class="form-table">';
  echo '<tr><th colspan=2>'. __("Getting your own Consumer Key and Consumer secret for your blog is highly recommended.") . '<BR/>'. __("Create a 'new application' by editting your profile at apontador") .',<BR/>' . __("there you can enter your website data and get your own key and secret pair");
  echo '</th></tr>';
  echo '<tr valign="top"> <th scope="row"><b>Consumer Key</b></th> <td><input type="text" name="consumer_key" value="';
  echo $consumer_key; 
  echo '"/></td> </tr>';
  
  echo '<tr valign="top"> <th scope="row"><b>Consumer Secret</b></th> <td><input type="text" name="consumer_secret" value="';
  echo $consumer_secret; 
  echo '"/></td> </tr>';
  echo '<tr><th colspan=2>'. __("The following codes are automagically generated, you don't need to worry about them"). '</th></tr>';
  echo '<tr valign="top"> <th scope="row"><b>OAuth Token</b></th> <td><input type="text" name="oauth_token" value="';
  echo $oauth_token;
  echo '" /></td></tr>';

  echo '<tr valign="top"> <th scope="row"><b>OAuth Secret</b></th> <td><input type="text" name="oauth_secret" value="';
  echo $oauth_secret; 
  echo '"/></td> </tr>';
  
  echo '</table>';
  echo '<p class="submit"> <input type="submit" class="button-primary" value="';
  _e('Save Changes');
  echo '>" /> </p> </form> </div>';
}


?>
