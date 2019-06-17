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

          $args = array(
              'post_type' => 'exemplar',
              'category_name' => 'a_ex1'
          );

          $exemplars = get_posts($args);
          foreach ($exemplars as $exemplar) {
              $ex_id = $exemplar->ID;
              $ex_ids[] = $ex_id;
              $ex_contents[$ex_id] = $exemplar->post_content;
              $exemplar_gold_levels[$ex_id] = get_field("gold_level", $ex_id, false);
          }

          $percent_correct = get_user_meta( $current_user->ID, 'percent_correct', true);
          if ( $percent_correct == null ) {
              $percent_correct = 0;
          }

          $data_for_js = array(
              'ajax_url' => admin_url('admin-ajax.php'),
              'nonce' => wp_create_nonce('gcap_scores_nonce'),
              'exIds' => $ex_ids,
              'exemplars' => $ex_contents,
              'exGoldLevels' => $exemplar_gold_levels,
              'percent_correct' => $percent_correct
          );
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
