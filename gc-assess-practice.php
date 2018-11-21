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

      $args = array(
          'post_type' => 'exemplar',
          'category_name' => 'a_ex1'
//          'orderby' => 'rand'
      );

      $exemplars = get_posts( $args );
      foreach ($exemplars as $exemplar) {
        $ex_id = $exemplar->ID;
        $ex_ids[] = $ex_id;
        $ex_contents[$ex_id] = $exemplar->post_content;
        $exemplar_gold_levels[$ex_id] = get_field( "gold_level", $ex_id, false);
      }

	  $data_for_js = array(
	      'exIds'=> $ex_ids,
          'exemplars'=> $ex_contents,
          'exGoldLevels' => $exemplar_gold_levels
      );
      wp_localize_script('gcap-main-js', 'exObj', $data_for_js );
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



// jsforwp likes with axios

$likes = get_option( 'jsforwp_likes' );
if ( null == $likes  ) {
    add_option( 'jsforwp_likes', 0 );
    $likes = 0;
}

function jsforwp_frontend_scripts() {

    wp_enqueue_script(
        'axios-js',
        'https://unpkg.com/axios/dist/axios.min.js',
        [],
        '',
        true
    );

    // Make dependent on 'axios-js'
    wp_enqueue_script(
        'jsforwp-frontend-js',
        plugins_url( '/assets/js/frontend-main.js', __FILE__ ),
        [ 'jquery', 'axios-js'],
        time(),
        true
    );

    // Change the value of 'ajax_url' to admin_url( 'admin-ajax.php' )
    // Change the value of 'total_likes' to get_option( 'jsforwp_likes' )
    // Change the value of 'nonce' to wp_create_nonce( 'jsforwp_likes_nonce' )
    wp_localize_script(
        'jsforwp-frontend-js',
        'jsforwp_globals',
        [
            'ajax_url'    => admin_url( 'admin-ajax.php' ),
            'total_likes' => get_option( 'jsforwp_likes' ),
            'nonce'       => wp_create_nonce( 'jsforwp_likes_nonce' )
        ]
    );

}
add_action( 'wp_enqueue_scripts', 'jsforwp_frontend_scripts' );

function jsforwp_add_like( $data ) {

    // Change the parameter to 'jsforwp_likes_nonce'
    check_ajax_referer( 'jsforwp_likes_nonce' );

    $likes = intval( get_option( 'jsforwp_likes' ) );
    $new_likes = $likes + 1;
    $success = update_option( 'jsforwp_likes', $new_likes );

    if( true == $success ) {
        $response['total_likes'] = $new_likes;
        $response['type'] = 'success';
    }

    $response = json_encode( $response );
    echo $response;
    die();

}
add_action( 'wp_ajax_jsforwp_add_like', 'jsforwp_add_like' );


// Remove version number from CDN urls (adopted from https://goo.gl/WMJ1dH)
function jsforwp_remove_ver_from_cdn( $src ) {
    if ( strpos( $src, 'unpkg.com' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'script_loader_src', 'jsforwp_remove_ver_from_cdn', 9999 );


require_once( 'assets/lib/plugin-page.php' );


?>
