<?php
/*
 * Sets up the Exemplar, Competency, and Scenario CPTs.
 */
namespace GC\Custom;
add_action('init',__NAMESPACE__ . '\register_cpt_exemplar');
/*
 * Adds the "Exemplar" custom post type
 */
function register_cpt_exemplar() {
    $labels = array(
        'name' => _x('Exemplars','exemplars'),
        'singular_name' => _x('Exemplar','exemplar'),
        'all_items' => ('All Exemplars'),
        'add_new_item' => ('Add New Exemplar'),
        'edit_item' => ('Edit Exemplar'),
        'search_items' => ('Search Exemplars'),
        'view_item' => ('View Exemplar'),
    );
    $args = array(
        'label' => __('Exemplars', 'exemplars'),
        'labels' => $labels,
        'public' => true,
        'taxonomies' => array('category'),
    );
    register_post_type('exemplar',$args);
}
add_action('init',__NAMESPACE__ . '\register_cpt_competency');
/*
 * Adds the "Competency" custom post type
 */
function register_cpt_competency() {
    $labels = array(
        'name' => _x('Competencies','competencies'),
        'singular_name' => _x('Competency','competency'),
        'all_items' => ('All Competencies'),
        'add_new_item' => ('Add New Competency'),
        'edit_item' => ('Edit Competency'),
        'search_items' => ('Search Competencies'),
        'view_item' => ('View Competency'),
    );
    $args = array(
        'label' => __('Competencies', 'competencies'),
        'labels' => $labels,
        'public' => true,
        'taxonomies' => array('category'),
    );
    register_post_type('competency',$args);
}
add_action('init',__NAMESPACE__ . '\register_cpt_scenario');
/*
 * Adds the "Scenario" custom post type
 */
function register_cpt_scenario() {
    $labels = array(
        'name' => _x('Scenarios','scenarios'),
        'singular_name' => _x('Scenario','scenario'),
        'all_items' => ('All Scenarios'),
        'add_new_item' => ('Add New Scenario'),
        'edit_item' => ('Edit Scenario'),
        'search_items' => ('Search Scenarios'),
        'view_item' => ('View Scenario'),
    );
    $args = array(
        'label' => __('Scenarios', 'scenarios'),
        'labels' => $labels,
        'public' => true,
        'taxonomies' => array('category'),
    );
    register_post_type('scenario',$args);
}