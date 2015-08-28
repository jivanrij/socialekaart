  <?php include(drupal_get_path('theme', 'gojiratheme') . '/_header.tpl.php'); ?>
  <div id="content_holder" class="big">
    <div id="search_form" class="rounded">
      <form>
        <input type="text" id="gojirasearch_search_term" value="" />
        <input type="submit" value="" />
      </form>
    </div>
    <div id="crud_holder" class="rounded">
      <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
      <?php print render($page['content']); ?>
    </div>
    <div id="ajax_search_results"></div>
    <div id="map"></div>
  </div>