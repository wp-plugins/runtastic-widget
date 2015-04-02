<?php
/*
Plugin Name: Runtastic Widget
Plugin URI: http://www.daniel-papenfuss.de
Description: Das Widget ermöglicht es dir in deinem Blog deine letzte Runtastic Aktivität anzeigen zu lassen
Version: 1.4
Author: Daniel Papenfuß
Author URI: http://www.daniel-papenfuss.de
License: GPL3
*/ 

define( 'RUNTASTIC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

add_action("widgets_init", "runtastic_widget_init");


register_activation_hook( __FILE__, array( 'Runtastic_Widget', 'install' ) );
register_deactivation_hook( __FILE__, array( 'Runtastic_Widget', 'uninstall' ) );


function runtastic_widget_init(){
    register_widget(Runtastic_Widget);
}

class Runtastic_Widget extends WP_Widget{
    
    function runtastic_widget(){
        $widget_options = array(
            'classname' => 'runtastic_widget_class',
            'description' => 'Ein Widget welches die letzte Runtastic Aktivität darstellt.'
        );
        load_plugin_textdomain('runtastic-widget', false, dirname(plugin_basename(__FILE__)) . '/lang');
        $this->WP_Widget('runtastic_widget_id', 'Runtastic Widget', $widget_options);
    }
    
    public function form($instance) {
        include('forms/admin.php');
    }
    
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['username'] = strip_tags($new_instance['username']);
        $instance['password'] = strip_tags($new_instance['password']);
        $instance['cache_refresh'] = strip_tags($new_instance['cache_refresh']);
        $instance['color'] = strip_tags($new_instance['color']);
        $instance['table_width'] = strip_tags($new_instance['table_width']);
        $instance['map_height'] = strip_tags($new_instance['map_height']);
        $instance['map_width'] = strip_tags($new_instance['map_width']);
        $instance['display_unit'] = strip_tags($new_instance['display_unit']);
        // Vorlage
        //$instance[''] = strip_tags($new_instance[''])
        return $instance;
    }

    public function widget($args, $instance) {
        if(!empty($instance['username']) &&  !empty($instance['password'])){
            wp_enqueue_style('test', plugin_dir_url( __FILE__ ) . 'style.css');
            global $wpdb;
            $this->update_cache($instance);
            echo $args['before_widget'];
            $last_activity = $this->get_last_activity_from_cache_db();
            include('forms/widget.php'); 
            echo $args['after_widget'];
            
        } 
   }
    
    
    public function install(){
        global $wpdb;
        $database_table_name = $wpdb->prefix . 'RuntasticWidgetCache'; 
       if($wpdb->get_var( "SHOW TABLES LIKE '$database_table_name'" ) != $database_table_name){
            $wpdb->query('CREATE TABLE ' .  $database_table_name. ' (id MEDIUMINT NOT NULL AUTO_INCREMENT, json_data TEXT, timestamp int(30),PRIMARY KEY (ID));');  
            $wpdb->query('INSERT INTO ' . $database_table_name. ' (json_data,timestamp) VALUES ("Default",0)');
        }
        
    }
    
    public function uninstall(){
       global $wpdb;
        $database_table_name= $wpdb->prefix . 'RuntasticWidgetCache';
        if($wpdb->get_var( "SHOW TABLES LIKE '$database_table_name'" ) == $database_table_name){
            $wpdb->query('DROP TABLE ' . $database_table_name);  
       } 
    }
    
    

    public function get_last_activity_from_cache_db(){
        global $wpdb;
        $last_activity_json = $wpdb->get_Var('SELECT json_data FROM `' . $wpdb->prefix . 'RuntasticWidgetCache` LIMIT 1;');
        $last_activity = json_decode($last_activity_json, true);
        return $last_activity;
    }
    
    public function get_last_cache_refresh_from_cache_db(){
        global $wpdb;
        $last_activity_refresh = $wpdb->get_Var('SELECT timestamp FROM `' . $wpdb->prefix . 'RuntasticWidgetCache` LIMIT 1;');
        return $last_activity_refresh;
    }
    
    public function update_cache($instance){
        $last_refresh = $this->get_last_cache_refresh_from_cache_db();
        $refresh_interval =  $instance['cache_refresh'];

        if(($last_refresh + $refresh_interval*60) < time()){
            include_once("controller.runtastic.php");
            global $wpdb;
            $i = 0;
            do{
                $runtastic = New Runtastic_Controller($instance['username'],$instance['password'], $instance['display_language'], $instance['map_height'], $instance['map_width'], $instance['display_unit']);
                $last_activity = $runtastic->get_last_activity(§display_unit);
                $i++;
                if($last_activity['distance'] != "0 km" AND $last_activity['duration'] != "00:00:00"){
                    $i = 3;
                }
            }while($i < 3);
            $last_activity_json = json_encode($last_activity);
            $wpdb->query("UPDATE `" . $wpdb->prefix . "RuntasticWidgetCache` SET `json_data` = '" . $last_activity_json . "', `timestamp` = '" . time() . "'");
        }
    }
    
    private function load_javascript(){
        if( is_admin() ){
            // Add the color picker css file       
            wp_enqueue_style( 'wp-color-picker' ); 

            // Include our custom jQuery file with WordPress Color Picker dependency
            wp_enqueue_script( 'custom-script-handle', plugins_url( 'custom-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
        }
        
    }
    
}

?>