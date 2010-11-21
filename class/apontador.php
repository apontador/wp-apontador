<?php
class Apontador {

  function __construct() {
    // default CONSUMERKEY and CONSUMERSECRET
    define('WPAPONTADORKEY','cXZLsC40GBhRjC7Op1htM9YGExyxnEPk-NjjLaPvdhs~');
    define('WPAPONTADORSECRET','FvUU-24mvQBTZEqMPFlAztpJAFQ~');
  }

  function init() {
    load_plugin_textdomain( "wp-apontador", false, dirname(dirname(plugin_basename(__FILE__))) . "/languages" );

    $config = array(
      'key'    => get_option('consumer_key'),
      'secret' => get_option('consumer_secret'),
    );

    if ( isset( $_GET['request_auth'] ) ) {
      $config["callbackurl"] = wp_nonce_url(admin_url('?page=apontador-settings') . "&apontador_oauth=1");
      apontadorRedirectAutorizacao($config);
    }

    if ( isset($_GET['oauth_token']) ) {
      $this->updateAuth($config);
    }

    add_action('admin_menu', array($this, 'createMenu'));
  }

  function initWidgets() {
    register_widget('ApontadorWidget');
  }

  function createMenu() {

    //create new top-level menu
    $n = add_menu_page(
      __('Apontador Settings', "wp-apontador"),
      __("Apontador", "wp-apontador"),
      'administrator',
      "apontador-settings",
      array($this, 'settingsPage'),
      plugins_url('/images/icon.png', dirname(__FILE__))
    );

    //call register settings function
    add_action( 'admin_init', array($this, 'registerSettings') );
  }

  function registerSettings() {
    //register our settings
    register_setting( 'apontador-settings-group', 'consumer_key' );
    register_setting( 'apontador-settings-group', 'consumer_secret' );
    register_setting( 'apontador-settings-group', 'oauth_token' );
    register_setting( 'apontador-settings-group', 'oauth_secret' );
  }

  function settingsPage() {
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

    include dirname(dirname(__FILE__)) . "/admin/settings_page.php";
  }

  private function updateAuth($config) {
      $access_token = apontadorProcessaAutorizacao($config);

      update_option('oauth_token',$access_token['oauth_token']);
      update_option('oauth_secret',$access_token['oauth_token_secret']);
      update_option('user_id',$access_token['user_id']);

      //had to do this in order to get rid of the GET parameters in the URl or "save changes" would trigger the authorization again and again, there's probably a better way to do it (tm)
      header("Location: " . admin_url('?page=apontador-settings'));
  }
}
