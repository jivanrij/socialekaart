  <?php include(drupal_get_path('theme', 'gojiratheme') . '/_header.tpl.php'); ?>
  <?php include(drupal_get_path('theme', 'gojiratheme') . '/_mobilemenu.tpl.php'); ?>
  <div id="content_holder">
    <?php include(drupal_get_path('theme', 'gojiratheme') . '/_searchform.tpl.php'); ?>
    <div id="crud_holder" class="rounded">
      <div>
        <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
        <h1><?php echo drupal_get_title(); ?></h1>
        <?php print render($page['content']); ?>
      </div>
    </div>
    <div id="ajax_search_results"></div>
    <div id="map"></div>
  </div>