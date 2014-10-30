<?php
$this->load_javascript();

$instance = wp_parse_args((array) $instance);
$username = esc_attr($instance['username']);
$password = esc_attr($instance['password']);
$cache_refresh = esc_attr($instance['cache_refresh']);
$color = esc_attr($instance['color']);
$table_width = esc_attr($instance['table_width']);
$map_height = esc_attr($instance['map_height']);
$map_width = esc_attr($instance['map_width']);

if(empty($table_width)){
    $table_width = "165";
}
if(empty($map_height)){
    $map_height = "125";
}
if(empty($map_width)){
    $map_width = "150";
}

echo '<p>'. __('Username', 'runtastic-widget') .': <input type="text" class="widefat" name="' . $this->get_field_name('username') . '" value="' . $username . '">';
echo '<p>'. __('Password', 'runtastic-widget') .': <input type="password" class="widefat" name="' . $this->get_field_name('password') . '" value="' . $password . '">';
echo '<p>'. __('Cache Refresh (Minutes)', 'runtastic-widget') .': <input type="text" class="widefat" name="' . $this->get_field_name('cache_refresh') . '" value="' . $cache_refresh . '">';

echo '<div class="runtastic-advanced-options" style="display:none;">';
echo '<p>'. __('Border Color (RGB Code)', 'runtastic-widget') . ':<br> <input type="text" class="color-field" name="' . $this->get_field_name('color') . '" value="' . $color . '">';
echo '<p>'. __('Table Width (Pixel)', 'runtastic-widget') .': <input type="text" class="widefat" name="' . $this->get_field_name('table_width') . '" value="' . $table_width . '">';
echo '<p>'. __('Map Height (Pixel)', 'runtastic-widget') .': <input type="text" class="widefat" name="' . $this->get_field_name('map_height') . '" value="' . $map_height . '">';
echo '<p>'. __('Map Width (Pixel)', 'runtastic-widget') .': <input type="text" class="widefat" name="' . $this->get_field_name('map_width') . '" value="' . $map_width . '">';
echo '</div>';
echo '<p><a href="#show-advanced-options" id="runtastic-advanced-options-show">'. __('Show Advanced Options','runtastic-widget') .'</a></p>';
echo '<p><a href="#hide-advanced-options" id="runtastic-advanced-options-hide" style="display:none;">'. __('Hide Advanced Options','runtastic-widget') .'</a></p>';
?>