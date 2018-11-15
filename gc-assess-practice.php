<?php
/*
   Plugin Name: GC Assess Practice
   Version: 1.0.0
   Author: Winston Sieck
   Author URI: https://www.globalcognition.org
   Description: Serve up exemplars to practice competency assessments
   Text Domain: gc-assess-prac
   License: GPLv3
*/

defined( 'ABSPATH' ) or die( 'No direct access!' );

function gc_assess_prac_enqueue_scripts() {

  if( is_page( 'react-in-wp' ) ) {

	  wp_enqueue_script(
		  'gcap-main-js',
		  plugins_url( '/assets/js/main.js', __FILE__ ),
		  ['wp-element', 'wp-components'],
		  time(),
		  true
	  );
  }
}
add_action( 'wp_enqueue_scripts', 'gc_assess_prac_enqueue_scripts' );


function gc_assess_prac_enqueue_styles() {

  wp_enqueue_style(
    'gcap-main-css',
    plugins_url( '/assets/css/main.css', __FILE__ ),
    [],
    time(),
    'all'
  );

}
add_action( 'wp_enqueue_scripts', 'gc_assess_prac_enqueue_styles' );

?>
