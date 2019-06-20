<?php
/*
 * The base db functions for creating and interacting with the judgments db table, as well as the cpts.
 */
global $db_version;
$db_version = '1.0';

global $table_postfix;
$table_postfix = 'judgments';

// this function is called in the main plugin file, because otherwise it doesn't work.
/*
 * Creates the table "wp_judgments" in the database.
 */
function gcap_create_table() {
    global $wpdb;
    global $db_version;
    global $table_postfix;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_name = $wpdb->prefix . $table_postfix;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        judg_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        learner_id mediumint(9) UNSIGNED NOT NULL,
        trial_num smallint(2) UNSIGNED NOT NULL,
		comp_num smallint(2) UNSIGNED NOT NULL,
		task_num smallint(2) UNSIGNED NOT NULL,
		ex_title tinytext NOT NULL,
		learner_level smallint(1) UNSIGNED NOT NULL,
		gold_level longtext NOT NULL,
		judg_corr smallint(1) UNSIGNED NOT NULL,
	    judg_time time NOT NULL,
	    learner_rationale longtext NOT NULL,
        PRIMARY KEY (judg_id)
	) $charset_collate;";

    dbDelta($sql);
    $success = empty( $wpdb->last_error );
    update_option($table_name . '_db_version',$db_version);
    return $success;
}

/*
 * Pulls relevant data from the CPTs using given $comp_num and $task_num.
 */
function pull_data_cpts($comp_num, $task_num) {
    global $current_user;

    $percent_correct = get_user_meta( $current_user->ID, 'percent_correct', true);
    if ( $percent_correct == null ) {
        $percent_correct = 0;
    }

    $ex_args = array(
        'numberposts' => -1,
        'post_type' => 'exemplar',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'comp_num',
                'value' => $comp_num,
                'compare' => '=',
            ),
            array(
                'meta_key' => 'task_num',
                'meta_value' => $task_num,
                'compare' => '=',
            ),
        )
    );

    $exemplars = get_posts($ex_args);
    foreach ($exemplars as $exemplar) {
        $ex_id = $exemplar->ID;
        $ex_ids[] = $ex_id;
        $ex_contents[$ex_id] = $exemplar->post_content;
        $exemplar_gold_levels[$ex_id] = get_field("gold_level", $ex_id, false);
        $ex_gold_rationales[$ex_id] = get_field("gold_rationale",$ex_id);
    }

    $s_args = array(
        'post_type' => 'scenario',
        'meta_key' => 'task_num',
        'meta_value' => $task_num
    );

    $scenario = get_posts($s_args);
    $s_content = $scenario[0]->post_content;

    $c_args = array(
        'post_type' => 'competency',
        'meta_key' => 'comp_num',
        'meta_value' => $comp_num
    );

    $competencies = get_posts($c_args);
    foreach ($competencies as $competency) {
        $j = get_field('comp_part',$competency->ID);
        $c_defs[$j] = $competency->post_content;
    }

    $data_for_js = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('gcap_scores_nonce'),
        'sContent' => $s_content,
        'cDefinitions' => $c_defs,
        'exIds' => $ex_ids,
        'exemplars' => $ex_contents,
        'exGoldLevels' => $exemplar_gold_levels,
        'exGoldRationales' => $ex_gold_rationales,
        'percent_correct' => $percent_correct,
    );

    return $data_for_js;
}

/*
 * The class which defines the generic functions for working with the database
 */
class judg_db {
    static $primary_key = 'id';

    // Private methods
    /*
     * Returns the name of the table
     */
    private static function _table() {
        global $wpdb;
        global $table_postfix;
        return $wpdb->prefix . $table_postfix;
    }

    /*
     * Returns the row with the given key
     */
    private static function _fetch_sql($value) {
        global $wpdb;
        $sql = sprintf("SELECT * FROM %s WHERE %s = %%s",self::_table(),static::$primary_key);
        return $wpdb->prepare($sql,$value);
    }

    // Public methods
    /*
     * Returns the row with the given key
     */
    static function get($value) {
        global $wpdb;
        return $wpdb->get_row( self::_fetch_sql( $value ) );
    }

    /*
     * Inserts a row
     */
    static function insert($data) {
        global $wpdb;
        $wpdb->insert(self::_table(),$data);
    }

    /*
     * Updates the specified row
     */
    static function update($data,$where) {
        global $wpdb;
        $wpdb->update(self::_table(),$data,$where);
    }

    /*
     * Deletes the specified row
     */
    static function delete($value) {
        global $wpdb;
        $sql = sprintf('DELETE FROM %s WHERE %s = %%s',self::_table(),static::$primary_key);
        return $wpdb->query($wpdb->prepare($sql,$value));
    }

    /*
     * Retrieves the specified data
     */
    static function fetch($value) {
        global $wpdb;
        $value = intval($value);
        $sql   = "SELECT * FROM " . self::_table() . " WHERE id = {$value}";
        return $wpdb->get_results( $sql );
    }

    /*
     * Returns an array of the columns and their formats
     */
    public function get_columns() {
        return array(
            'judg_id' => '%d',
            'learner_id' => '%d',
            'trial_num' => '%d',
            'comp_num' => '%d',
            'task_num' => '%d',
            'ex_title' => '%s',
            'learner_level' => '%d',
            'learner_rationale' => '%s',
            'gold_level' => '%s',
            'judg_corr' => '%d',
            'judg_time'  => '%s',
        );
    }

    /*
     * Returns an array with all results from the database with the given parameter
     * If $count is set to true, just returns the number of results
     */
    public function get_judgments($args=array(),$count=false) {
        global $wpdb;
        $defaults = array(
            'learner_id' => 0,
            'trial_num' => 0,
            'comp_num' => 0,
            'task_num' => 0,
            'ex_title' => '',
            'learner_level' => 0,
            'judg_corr' => 0,
            'offset' => 0,
            'order_by' => 'learner_id',
            'order' => 'DESC',
            'number' => PHP_INT_MAX,
        );
        $args = wp_parse_args($args,$defaults);
        $where = '';
        if(!empty($args['learner_id'])) {
            if(is_array($args['learner_id'])) {
                $where .= " trial_num IN ('{$args['learner_id'][0]}'";
                for($i=1;$i<sizeof($args['learner_id']);$i++) {
                    $where .= ", '{$args['learner_id'][$i]}'";
                }
                $where .= ")";
            } else {
                $learner_ids = $args['learner_id'];
                $where .= " learner_id = '{$learner_ids}'";
            }
        }
        if(!empty($args['trial_num'])) {
            if(empty($where)) {
                $where .= " WHERE";
            } else {
                $where .= " AND";
            }
            if(is_array($args['trial_num'])) {
                $where .= " trial_num IN ('{$args['trial_num'][0]}'";
                for($i=1;$i<sizeof($args['trial_num']);$i++) {
                    $where .= ", '{$args['trial_num'][$i]}'";
                }
                $where .= ")";
            } else {
                $trial_nums = $args['trial_num'];
                $where .= " trial_num = '{$trial_nums}'";
            }
        }
        if(!empty($args['comp_num'])) {
            if(empty($where)) {
                $where .= " WHERE";
            } else {
                $where .= " AND";
            }
            if(is_array($args['comp_num'])) {
                $where .= " comp_num IN ('{$args['comp_num'][0]}'";
                for($i=1;$i<sizeof($args['comp_num']);$i++) {
                    $where .= ", '{$args['comp_num'][$i]}'";
                }
                $where .= ")";
            } else {
                $comp_nums = $args['comp_num'];
                $where .= " comp_num = '{$comp_nums}'";
            }
        }

        if(!empty($args['task_num'])) {
            if(empty($where)) {
                $where .= " WHERE";
            } else {
                $where .= " AND";
            }
            if(is_array($args['task_num'])) {
                $where .= " task_num IN ('{$args['task_num'][0]}'";
                for($i=1;$i<sizeof($args['task_num']);$i++) {
                    $where .= ", '{$args['task_num'][$i]}'";
                }
                $where .= ")";
            } else {
                $task_nums = $args['task_num'];
                $where .= " task_num = '{$task_nums}'";
            }
        }

        if(!empty($args['ex_title'])) {
            if(empty($where)) {
                $where .= " WHERE";
            } else {
                $where .= " AND";
            }
            if(is_array($args['ex_title'])) {
                $where .= " ex_title IN ('{$args['ex_title'][0]}'";
                for($i=1;$i<sizeof($args['ex_title']);$i++) {
                    $where .= ", '{$args['ex_title'][$i]}'";
                }
                $where .= ")";
            } else {
                $ex_titles = $args['ex_title'];
                $where .= " ex_title = '{$ex_titles}'";
            }
        }

        if(!empty($args['learner_level'])) {
            if(empty($where)) {
                $where .= " WHERE";
            } else {
                $where .= " AND";
            }
            if(is_array($args['learner_level'])) {
                $where .= " learner_level IN ('{$args['learner_level'][0]}'";
                for($i=1;$i<sizeof($args['learner_level']);$i++) {
                    $where .= ", '{$args['learner_level'][$i]}'";
                }
                $where .= ")";
            } else {
                $learner_levels = $args['learner_level'];
                $where .= " learner_level = '{$learner_levels}'";
            }
        }

        if(!empty($args['judg_corr'])) {
            if(empty($where)) {
                $where .= " WHERE";
            } else {
                $where .= " AND";
            }
            if(is_array($args['judg_corr'])) {
                $where .= " judg_corr IN ('{$args['judg_corr'][0]}'";
                for($i=1;$i<sizeof($args['judg_corr']);$i++) {
                    $where .= ", '{$args['judg_corr'][$i]}'";
                }
                $where .= ")";
            } else {
                $judg_corrs = $args['judg_corr'];
                $where .= " judg_corr = '{$judg_corrs}'";
            }
        }

        $args['order_by'] = ! array_key_exists($args['order_by'],$this->get_columns()) ? static::$primary_key :
            $args['order_by'];

        $cache_key = (true === $count) ? md5('judg_count' . serialize($args)) :
            md5('judg_' . serialize($args));

        $results = wp_cache_get($cache_key,'judgments');
        if(false === $results) {
            if(true === $count) {
                $results = absint($wpdb->get_var("SELECT COUNT(" . static::$primary_key . ") FROM ". self::_table() .
                    "{$where};"));
            } else {
                $results = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM " . self::_table() . " {$where} ORDER BY %s %s LIMIT %d,%d;",
                    $args['order_by'], $args['order'], absint($args['offset']), absint($args['number'])
                ));
            }
        }

        wp_cache_set($cache_key,$results,'judgments',3600);
        return $results;
    }
}