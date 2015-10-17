<?php include(drupal_get_path('theme', 'gojiratheme') . '/_header.tpl.php'); ?>
<?php include(drupal_get_path('theme', 'gojiratheme') . '/_mobilemenu.tpl.php'); ?>
<div id="content_holder" class="big">
    <div id="search_form" class="rounded">
        <form>
            <input type="text" id="gojirasearch_search_term" value="" />
            <input type="submit" value="" />
        </form>
    </div>
    <div id="crud_holder" class="rounded">
        <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
        <h1><?php echo drupal_get_title(); ?></h1>
        <?php print render($page['content']); ?>
    </div>
    <div id="ajax_search_results"></div>
    <div id="map"></div>
</div>