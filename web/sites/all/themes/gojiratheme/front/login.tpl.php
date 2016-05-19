<div class="container">
    <div class="row">
        <div class="col-sm-8 frontpage_block text">
            <div>
                <h1><?php echo helper::getText('FRONTPAGE_LOGIN', true); ?></h1>
                <p><?php echo helper::getText('FRONTPAGE_LOGIN'); ?></p>
                <a href="/introduction" type="button" class="intro btn btn-danger"><?php echo t('More information please'); ?> <i class="fa fa-info-circle" aria-hidden="true"></i></a>
            </div>
        </div>

        <div class="col-sm-4 frontpage_block form-block login">
            <div>
                <a class="btn btn-danger" title="<?php echo t('Register'); ?>" href="/registreer" id="splash_register"><?php echo t('Register a new account'); ?></a>
                <br />
                <br />
                <h1><?php echo t('Login'); ?></h1>
                <?php print @drupal_render(drupal_get_form('gojira_login_form')); ?>
                <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
            </div>
        </div>
    </div>
</div>
