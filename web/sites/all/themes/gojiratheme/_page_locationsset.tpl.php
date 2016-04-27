<?php include(drupal_get_path('theme', 'gojiratheme') . '/_header.tpl.php'); ?>
<?php include(drupal_get_path('theme', 'gojiratheme') . '/_mobilemenu.tpl.php'); ?>
<?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
<div id="content_holder">
    <?php include(drupal_get_path('theme', 'gojiratheme') . '/_searchform.tpl.php'); ?>
    <?php include(drupal_get_path('module', 'gojira') . '/templates/locationsset.tpl.php'); ?>
    <div id="ajax_search_results"></div>
    <div id="map"></div>
</div>
