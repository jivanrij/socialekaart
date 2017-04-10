<?php

function websites() {
  $result = db_query("SELECT field_url_value FROM `field_data_field_url` group by field_url_value");
  return theme('websites', array("websites"=>$result));
}
