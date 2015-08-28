<h1><?php echo drupal_get_title(); ?></h1>
<?php $f = drupal_get_form('gojira_configuration_form'); echo render($f); ?>