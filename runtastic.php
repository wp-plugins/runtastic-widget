<?php
/*
Plugin Name: Runtastic Widget
Plugin URI: http://www.daniel-papenfuss.de
Description: Das Widget ermöglicht es dir in deinem Blog deine letzte Runtastic Aktivität anzeigen zu lassen
Version: 1.0
Author: Daniel Papenfuß
Author URI: http://www.daniel-papenfuss.de
License: GPL3
*/
define( 'RUNTASTIC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

add_action("widgets_init", "runtastic_widget_init");


register_activation_hook( __FILE__, array( 'Runtastic_Widget', 'Install' ) );
register_deactivation_hook( __FILE__, array( 'Runtastic_Widget', 'Uninstall' ) );


function runtastic_widget_init(){
    register_widget(Runtastic_Widget);
}

class Runtastic_Widget extends WP_Widget{
    function Runtastic_Widget(){
        $widget_options = array(
            'classname' => 'runtastic_widget_class',
            'description' => 'Ein Widget welches die letzte Runtastic Aktivität darstellt.'
        );
        
        $this->WP_Widget('runtastic_widget_id', 'Runtastic Widget', $widget_options);
    }
    
    public function form($instance) {
        $instance = wp_parse_args((array) $instance);
        $Username = esc_attr($instance['username']);
        $Password = esc_attr($instance['password']);
        $CacheRefresh = esc_attr($instance['cache_refresh']);
        echo '<p>Benutzername: <input type="text" class="widefat" name="' . $this->get_field_name('username') . '" value="' . $Username . '">';
        echo '<p>Passwort: <input type="password" class="widefat" name="' . $this->get_field_name('password') . '" value="' . $Password . '">';
        echo '<p>Cache Refresh (in Minuten): <input type="text" class="widefat" name="' . $this->get_field_name('cache_refresh') . '" value="' . $CacheRefresh . '">';
    }
    
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['username'] = strip_tags($new_instance['username']);
        $instance['password'] = strip_tags($new_instance['password']);
        $instance['cache_refresh'] = strip_tags($new_instance['cache_refresh']);
        return $instance;
    }
    
    public function widget($args, $instance) {
        if(!empty($instance['username']) &&  !empty($instance['password'])){
            wp_enqueue_style('test', plugin_dir_url( __FILE__ ) . 'style.css');
            global $wpdb;
            echo $args['before_widget'];
            $LastActivity = $this->GetLastActivityFromCacheDB();
            include("anzeige.php");
            echo $args['after_widget'];
            include_once ('Update_Cache.php');
        } 
   }
    
    
    public function Install(){
        global $wpdb;
        $DBTableName = $wpdb->prefix . 'RuntasticWidgetCache';
        if($wpdb->get_var( "SHOW TABLES LIKE '$DBTableName'" ) != $DBTableName){
            $wpdb->query('CREATE TABLE ' .  $DBTableName . ' (id MEDIUMINT NOT NULL AUTO_INCREMENT, json_data TEXT, timestamp int(30),PRIMARY KEY (ID));');  
            $wpdb->query('INSERT INTO ' . $DBTableName . ' (json_data,timestamp) VALUES ("Default",0)');
        }
        
    }
    
    public function Uninstall(){
       global $wpdb;
        $DBTableName = $wpdb->prefix . 'RuntasticWidgetCache';
        if($wpdb->get_var( "SHOW TABLES LIKE '$DBTableName'" ) == $DBTableName){
            $wpdb->query('DROP TABLE ' . $DBTableName);  
       } 
    }
    
    


    public function GetLastActivityFromCacheDB(){
        global $wpdb;
        $LastActivity_JSON = $wpdb->get_Var('SELECT json_data FROM `' . $wpdb->prefix . 'RuntasticWidgetCache` LIMIT 1;');
        $LastActivity = json_decode($LastActivity_JSON, true);
        return $LastActivity;
    }
    
    public function GetLastCacheRefreshFromCacheDB(){
        global $wpdb;
        $LastActivityRefresh = $wpdb->get_Var('SELECT timestamp FROM `' . $wpdb->prefix . 'RuntasticWidgetCache` LIMIT 1;');
        return $LastActivityRefresh;
    }
    
}
?>
