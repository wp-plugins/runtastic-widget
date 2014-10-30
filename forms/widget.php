<h1 class="widget-title">Meine letzte Aktivität</h1>
<!-- Widget erstellt von Daniel Papenfuß (www.daniel-papenfuss.de) -->
<div id="RuntasticWidget" style="width:<?php echo $instance['table_width'] ?>px;border-color:<?php echo $instance['color'] ?>;">
  <table>
    <tr>
      <td><?php _e('Type','runtastic-widget'); ?>:</td>
      <td><?php echo $last_activity['type']; ?></td>
    </tr>
    <tr>
      <td><?php _e('Distance','runtastic-widget') ?>:</td>
      <td><?php echo $last_activity["distance"]; ?></td>
    </tr>
    <tr>
      <td><?php _e('Duration','runtastic-widget') ?>:</td>
      <td><?php echo $last_activity["duration"]; ?></td>
    </tr>
    <tr>
      <td><?php _e('Pace','runtastic-widget') ?>:</td>
      <td><?php echo $last_activity["pace"]; ?></td>
    </tr>
    <?php
    if($last_activity["map"]!="http://"){
    ?>
    <tr>
      <td colspan="2">
        <img src="<?php echo $last_activity["map"]; ?>">
      </td>
    </tr>
    <?php
    }
    ?>
  </table>
</div><!-- #RuntasticWidget -->   