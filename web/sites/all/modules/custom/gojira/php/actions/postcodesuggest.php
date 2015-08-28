<?php
/**
 * postcode suggest via ajax
 */
function postcodesuggest() {
  $info = Postcode::getInstance()->getGeoInfo($_POST['pc'], $_POST['pcnumber']);
  $info->zoom = GojiraSettings::MAP_ZOOMLEVEL_STREET;
  if ($info) {
    echo json_encode($info);
  } else {
    echo json_encode('fail');
  }
  exit;
}