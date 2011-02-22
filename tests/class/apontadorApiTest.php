<?php
$plugin_root = dirname(dirname(dirname(__FILE__))) . "/";
require_once "PHPUnit/Framework.php";
require_once $plugin_root . "tests/MockPress/mockpress.php";
require_once $plugin_root . "plugin/class/apontadorApi.php";

class ApontadorApiTest extends PHPUnit_Framework_TestCase {

  private $api;
  private $mockHttp;

  function setUp() {
    $this->mockHttp = $this->getMock('WP_Http', array('request'));

    $this->api = new ApontadorApi(
      array(
        'email'  => "email@example.com",
        'key'    => "key",
        'secret' => "secret",
      ),
      $this->mockHttp
    );
  }

  function testAuthUrlShouldHaveQueryStringParameters() {
    $this->mockHttp->expects($this->once())
         ->method('request')
         ->will($this->returnValue(array(
             'body' => "oauth_token=token&oauth_token_secret=secret",
           )));

    $parsed_url = parse_url($this->api->getAuthUrl("test.url"));
    parse_str($parsed_url['query'], $querystrings);

    $this->assertArrayHasKey('oauth_token', $querystrings);
    $this->assertArrayHasKey('oauth_callback', $querystrings);
  }
}
