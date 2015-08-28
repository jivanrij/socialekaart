<div class="container">
    <div class="row">
        <div class="col-sm-9 frontpage_block">
            <div>
                <h1><?php echo helper::getText('FRONTPAGE_REGISTER', true); ?></h1> 
                <p><?php echo helper::getText('FRONTPAGE_REGISTER'); ?></p>
            </div>
        </div>
        <div class="col-sm-3 frontpage_block">
            <div>
                <h1><?php echo t('Register'); ?></h1>
                <?php print @drupal_render(drupal_get_form('gojira_register_form')); ?>        
                <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
            </div>
        </div> 
    </div>
</div>