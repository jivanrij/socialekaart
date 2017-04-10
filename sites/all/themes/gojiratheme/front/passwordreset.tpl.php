<div class="container">
    <div class="row equal_background">
        <div class="col-sm-8 frontpage_block">
            <div>
                <h1><?php echo helper::getText('FRONTPAGE_PASSWORD_RESET', true); ?></h1>
                <p><?php echo helper::getText('FRONTPAGE_PASSWORD_RESET'); ?></p>
            </div>
        </div>
        <div class="col-sm-4 frontpage_block form-block">
            <div>
                <h1><?php echo t('Request new password'); ?></h1>
                <?php helper::renderFormAsBootstrap('gojira_passwordreset_form'); ?>
                <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
            </div>
        </div>
    </div>
</div>
