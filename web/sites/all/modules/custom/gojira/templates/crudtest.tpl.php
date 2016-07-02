<section>
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <h1><?php echo drupal_get_title(); ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <?php $f = drupal_get_form('gojira_crudtest_form'); echo render($f); ?>
        </div>
    </div>
</section>
