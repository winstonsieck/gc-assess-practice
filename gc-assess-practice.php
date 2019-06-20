<?php
/*
   Plugin Name: GC Assess Practice
   Version: 1.0.0
   Author: Global Cognition
   Author URI: https://www.globalcognition.org
   Description: Serve up exemplars to practice competency assessments
   Text Domain: gc-assess-prac
   License: GPLv3
*/

defined( 'ABSPATH' ) or die( 'No direct access!' );

include_once 'assets/lib/cpt-setup.php';

include_once 'assets/lib/judgments-db.php';
// Call gcap_create_table on plugin activation.
register_activation_hook(__FILE__,'gcap_create_table'); // this function call has to happen here

function gc_assess_prac_enqueue_scripts() {

  if( is_page( 'react-in-wp' ) ) {

      global $current_user;
      get_currentuserinfo();
      if ( $current_user->ID) {

          wp_enqueue_script(
              'gcap-main-js',
              plugins_url('/assets/js/main.js', __FILE__),
              ['wp-element', 'wp-components', 'jquery'],
              time(),
              true
          );

          $comp_num = 2;
          $task_num = 9;
          $data_for_js = pull_data_cpts($comp_num,$task_num);

          wp_localize_script('gcap-main-js', 'exObj', $data_for_js);

      } else {
          echo "please log in";
      }

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


function gcap_add_scores( ) {

    global $current_user;
    get_currentuserinfo();
    if ( $current_user->ID) {

        check_ajax_referer('gcap_scores_nonce');

        $scores = $_POST['scores'];
        $percent_correct = round(array_sum($scores) / count($scores) , 2);
        update_user_meta($current_user->ID, 'percent_correct', $percent_correct );
        $retrieved_pc = get_user_meta($current_user->ID, 'percent_correct', true);

        if ($percent_correct == $retrieved_pc) {
            echo $percent_correct;
        }
    }
    die();

}
add_action( 'wp_ajax_gcap_add_scores', 'gcap_add_scores' );


require_once( 'assets/lib/plugin-page.php' );


?>
