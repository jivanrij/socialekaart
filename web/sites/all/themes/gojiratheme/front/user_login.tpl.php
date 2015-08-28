<div class="container">
    <div class="row">
        <div class="col-sm-12 frontpage_block">
            <div>
                <h1><?php echo t('SSO Login'); ?></h1>
                <?php print @drupal_render(drupal_get_form('user_login')); ?>        
                <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
            </div>
        </div>
    </div>
</div>