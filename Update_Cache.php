<?php
$LastRefresh = $this->GetLastCacheRefreshFromCacheDB();
$RefreshInterval =  $instance['cache_refresh'];

if(($LastRefresh + $RefreshInterval*60) < time()){
    include_once("controller.runtastic.php");
    global $wpdb;
    $Runtastic = New Runtastic_Controller($instance['username'],$instance['password']);
    $LastActivity = $Runtastic->GetLastActivity();
    $LastActivityJSON = json_encode($LastActivity);
    $wpdb->query("UPDATE `wp_RuntasticWidgetCache` SET `json_data` = '" . $LastActivityJSON . "', `timestamp` = '" . time() . "'");
}
?>
