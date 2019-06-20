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
          d($data_for_js);

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

// Genesis activation hook - if statement in function has it run only on a given page
add_action('genesis_before_content','save_data');
/*
 * Calls the insert function from the class judg_db to insert exemplar data into the table
 */
function save_data() {
    $page_slug = 'judgment-test';
    // test data
    $comp_num = 2;
    $task_num = 9;
    $learner_level = 1;
    $learner_rationale = 'i chose this for a reason';
    $judg_time = '2:00:00';
    global $current_user;
    if(is_page($page_slug)) {
        $db = new judg_db;
        $cpt_data = pull_data_cpts($comp_num,$task_num);
        for ($i=0;$i<sizeof($cpt_data['exIds']);$i++) {
            $ex_id = $cpt_data['exIds'][$i];
            $gold_level = $cpt_data['exGoldLevels'][$ex_id];
            if($learner_level==$gold_level){
                $judg_corr = 1;
            } else {
                $judg_corr = 0;
            }
            $db_data = array(
                'learner_id' => $current_user->ID,
                'trial_num' => $i+1,
                'comp_num' => $comp_num,
                'task_num' => $task_num,
                'ex_title' => get_the_title($ex_id),
                'learner_level' => $learner_level,
                'gold_level' => $gold_level,
                'judg_corr' => $judg_corr,
                'judg_time'  => $judg_time,
                'learner_rationale' => $learner_rationale,
            );
            $db->insert($db_data);
        }
    }
}


require_once( 'assets/lib/plugin-page.php' );


?>
