<?php
/*
Plugin Name: Runtastic Widget
Plugin URI: http://www.daniel-papenfuss.de
Description: Das Widget ermöglicht es dir in deinem Blog deine letzte Runtastic Aktivität anzeigen zu lassen
Version: 1.1
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
        $DisplayLanguage = esc_attr($instance['display_language']);
        $Table_Width = esc_attr($instance['table_width']);
        $Map_Height = esc_attr($instance['map_height']);
        $Map_Width = esc_attr($instance['map_width']);
        
        if(empty($Table_Width)){
            $Table_Width = "165";
        }
        if(empty($Map_Height)){
            $Map_Height = "125";
        }
        if(empty($Map_Width)){
            $Map_Width = "150";
        }
        echo '<p>Anzeigesprache/Display Language:<select name="'. $this->get_field_name('display_language') .'" id="'. $this->get_field_id('select') .'" class="widefat">';
        if($DisplayLanguage == "Deutsch" OR $DisplayLanguage == "German"){
            $options = array('Deutsch','Englisch');
            $DisplayText = array("Benutzername","Passwort","Cache Refresh (in Minuten)", "Breite der Anzeige (in Pixel)","Map Höhe (in Pixel)","Map Breite (in Pixel)");
        }else{
            $options = array('German','English');
            $DisplayText = array("Username","Password","Cache Refresh (Minutes)" , "Display Width (Pixel)","Map Hight (Pixel)","Map Width (Pixel)");
        }
        foreach($options as $option){
            If(($DisplayLanguage == "Deutsch" OR $DisplayLanguage == "German")AND($option == "Deutsch" OR $option == "German")){
                $selected = 'selected="selected"';
            }elseif(($DisplayLanguage == "Englisch" OR $DisplayLanguage == "English")AND($option == "Englisch" OR $option == "English")){
                $selected = 'selected="selected"';
            }else{
                $selected = '';
            }
            echo '<option value="' . $option . '" id="' . $option . '"', $selected , '>', $option, '</option>';
        }
        echo '</select></p>';
        echo '<p>'.$DisplayText[0].': <input type="text" class="widefat" name="' . $this->get_field_name('username') . '" value="' . $Username . '">';
        echo '<p>'.$DisplayText[1].': <input type="password" class="widefat" name="' . $this->get_field_name('password') . '" value="' . $Password . '">';
        echo '<p>'.$DisplayText[2].': <input type="text" class="widefat" name="' . $this->get_field_name('cache_refresh') . '" value="' . $CacheRefresh . '">';
       
        echo '<p>'.$DisplayText[3].': <input type="text" class="widefat" name="' . $this->get_field_name('table_width') . '" value="' . $Table_Width . '">';
        echo '<p>'.$DisplayText[4].': <input type="text" class="widefat" name="' . $this->get_field_name('map_height') . '" value="' . $Map_Height . '">';
        echo '<p>'.$DisplayText[5].': <input type="text" class="widefat" name="' . $this->get_field_name('map_width') . '" value="' . $Map_Width . '">';
    }
    
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['username'] = strip_tags($new_instance['username']);
        $instance['password'] = strip_tags($new_instance['password']);
        $instance['cache_refresh'] = strip_tags($new_instance['cache_refresh']);
        $instance['display_language'] = strip_tags($new_instance['display_language']);
        $instance['table_width'] = strip_tags($new_instance['table_width']);
        $instance['map_height'] = strip_tags($new_instance['map_height']);
        $instance['map_width'] = strip_tags($new_instance['map_width']);
        // Vorlage
        //$instance[''] = strip_tags($new_instance['']);
        return $instance;
    }
    
    public function widget($args, $instance) {
        if(!empty($instance['username']) &&  !empty($instance['password'])){
            wp_enqueue_style('test', plugin_dir_url( __FILE__ ) . 'style.css');
            global $wpdb;
            include_once ('Update_Cache.php');
            echo $args['before_widget'];
            $LastActivity = $this->GetLastActivityFromCacheDB();
            if($instance['display_language'] == "Englisch" OR $instance['display_language'] == "English"){
                include('lang/anzeige_Englisch.php'); 
            }else{
                include('lang/anzeige_Deutsch.php'); 
            }           
            echo $args['after_widget'];
            
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
