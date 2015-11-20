<?php include(drupal_get_path('theme', 'gojiratheme') . '/_header.tpl.php'); ?>
<?php include(drupal_get_path('theme', 'gojiratheme') . '/_mobilemenu.tpl.php'); ?>
<?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
<div id="content_holder">
    <div id="search_form" class="rounded">
        <form>
            <input type="text" id="gojirasearch_search_term" placeholder="<?php echo t('Search'); ?>" value="" />
            <input type="submit" value="" />
        </form>
    </div>
    <?php include(drupal_get_path('module', 'gojira') . '/templates/locationset.tpl.php'); ?>
    <div id="ajax_search_results"></div>
    <div id="map"></div>
</div>