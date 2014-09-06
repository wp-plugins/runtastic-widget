<h1 class="widget-title">Meine letzte Aktivität</h1>
<!-- Widget erstellt von Daniel Papenfuß (www.daniel-papenfuss.de) -->
<div id="RuntasticWidget" style="width:<?php echo $instance['table_width'] ?>px;">
  <table>
    <tr>
      <td>Art:</td>
      <td><?php echo $LastActivity['type']; ?></td>
    </tr>
    <tr>
      <td>Distanz:</td>
      <td><?php echo $LastActivity["distance"]; ?></td>
    </tr>
    <tr>
      <td>Dauer:</td>
      <td><?php echo $LastActivity["duration"]; ?></td>
    </tr>
    <tr>
      <td>Pace:</td>
      <td><?php echo $LastActivity["pace"]; ?></td>
    </tr>
    <?php
    if($LastActivity["map"]!="http://"){
    ?>
    <tr>
      <td colspan="2">
        <img src="<?php echo $LastActivity["map"]; ?>">
      </td>
    </tr>
    <?php
    }
    ?>
  </table>
</div><!-- #RuntasticWidget -->   