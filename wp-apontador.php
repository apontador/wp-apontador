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
require dirname(__file__) . '/class/apontador.php';

$apontador = new Apontador();
add_filter('init', array($apontador, 'init'));
add_action('widgets_init', array($apontador, 'initWidgets'));
