<?php
require_once dirname(dirname(__FILE__)) . "/OAuth.php";

class ApontadorApi {

  private $config;
  private $urls;
  private $wp_http;

  public function __construct($config = null, $wp_http = null) {
    if (is_array($config)) {
      $this->setConfig($config);
    }

    $this->urls = array(
      'request_token' => "http://api.apontador.com.br/v1/oauth/request_token",
      'authorize' => "http://api.apontador.com.br/v1/oauth/authorize",
    );

    if (is_null($wp_http)) {
      $this->wp_http = new WP_Http();
    } else {
      $this->wp_http = $wp_http;
    }
  }

  /**
   * Redirects the user to the Apontador site,
   * in order to obtain the authorization
   */
  function redirectToAuth($callback) {
    header("Location: " . $this->getAuthUrl($callback));
  }

  /**
   * Returns the authorization url
   *
   * @param string $callback A callback URL to redirect back
   * @return string
   */
  function getAuthUrl($callback) {
    $consumer = new OAuthConsumer($this->config['key'], $this->config['secret']);

    $request = OAuthRequest::from_consumer_and_token(
      $consumer,
      null,
      'GET',
      $this->urls['request_token']
    );

    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, null);

    $response = $this->wp_http->request((string)$request);
    parse_str($response['body'], $response_data);

    $oauth_callback_url = http_build_query(array(
      'key' => $this->config['key'],
      'secret' => $this->config['secret'],
      'token' => $response_data['oauth_token'],
      'token_secret' => $response_data['oauth_token_secret'],
      'endpoint' => urlencode($this->urls['authorize']),
    ));

    $oauth_callback_url = strpos($callback, "?") !== false
      ? $callback . "&" . $oauth_callback_url
      : $callback . "?" . $oauth_callback_url;

    return $this->urls['authorize'] . "?" . http_build_query(array(
      'oauth_token' => $response_data['oauth_token'],
      'oauth_callback' => urlencode($oauth_callback_url),
    ));
  }

  /**
   * Sets the configuration data to the private attribute
   * @param array $config The configuration data (email, key, secret, callback_url)
   */
  private function setConfig($config) {
    if (!is_array($this->config)) {
      $this->config = array(
        'email' => "",
        'key' => "",
        'secret' => "",
      );
    }

    $this->config = array_merge($this->config, $config);
  }
}
