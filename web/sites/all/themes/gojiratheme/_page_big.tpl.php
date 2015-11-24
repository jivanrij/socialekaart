  <?php include(drupal_get_path('theme', 'gojiratheme') . '/_header.tpl.php'); ?>
  <?php include(drupal_get_path('theme', 'gojiratheme') . '/_mobilemenu.tpl.php'); ?>
  <div id="content_holder" class="big">
    <?php include(drupal_get_path('theme', 'gojiratheme') . '/_searchform.tpl.php'); ?>
    <div id="crud_holder" class="rounded">
      <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
      <?php print render($page['content']); ?>
    </div>
    <div id="ajax_search_results"></div>
    <div id="map"></div>
  </div>