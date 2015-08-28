<?php
function gojirareport_suggested_active_locations(){
  set_time_limit(300);
  
  $query = new EntityFieldQuery();
  $query->entityCondition('entity_type', 'node')
          ->propertyCondition('status', 1)
          ->entityCondition('bundle', GojiraSettings::CONTENT_TYPE_LOCATION)
          ->fieldCondition(GojiraSettings::CONTENT_TYPE_MODERATED_STATUS_FIELD, 'value', array(2, 3), 'in');
  $result = $query->execute();
  $suggestedActiveLocations = array();
  if (isset($result['node'])) {
    foreach ($result['node'] as $node) {
      $suggestedActiveLocations[] = node_load($node->nid);
    }
  }
  
  return theme('gojirareport_suggested_active_locations', array('locations' => $suggestedActiveLocations));
}