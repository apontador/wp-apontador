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
require dirname(__file__) . '/widget/widget_class.php';
require dirname(__file__) . '/class/apontadorLoader.php';

$apontadorLoader = new ApontadorLoader();
add_filter('init', array($apontadorLoader, 'init'));
add_action('widgets_init', array($apontadorLoader, 'initWidgets'));
