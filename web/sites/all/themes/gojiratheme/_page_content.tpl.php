<?php include(drupal_get_path('theme', 'gojiratheme') . '/_header.tpl.php'); ?>
<?php include(drupal_get_path('theme', 'gojiratheme') . '/_mobilemenu.tpl.php'); ?>
<?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
<?php print render($page['content']); ?>
<div id="ajax_search_results"></div>
<div id="map"></div>