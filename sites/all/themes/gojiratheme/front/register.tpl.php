<div class="container">
    <div class="row equal_background">
        <div class="col-sm-6 frontpage_block">
            <div>
                <h1><?php echo helper::getText('FRONTPAGE_REGISTER', true); ?></h1>
                <p><?php echo helper::getText('FRONTPAGE_REGISTER'); ?></p>
            </div>
        </div>
        <div class="col-sm-6 frontpage_block form-block">
            <div>
                <h1><?php echo t('Register'); ?></h1>
                <?php helper::renderFormAsBootstrap('gojira_register_form'); ?>
                <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
            </div>
        </div>
    </div>
</div>
