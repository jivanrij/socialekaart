<div class="container">
    <div class="row">
        <div class="col-sm-8 frontpage_block text">
            <div>
                <h1><?php echo helper::getText('FRONTPAGE_LOGIN_HAWEB', true); ?></h1> 
                <p><?php echo helper::getText('FRONTPAGE_LOGIN_HAWEB'); ?></p>
            </div>
        </div>
        <div class="col-sm-4 frontpage_block form-block login">
            <div>
                <a class="register gbutton rounded noshadow left gbutton_widest" title="<?php echo t('Register'); ?>" href="/registreer?ha=web" id="splash_register"><span><?php echo t('Register a new account'); ?></span></a>
                <h1><?php echo t('Login'); ?></h1>
                <?php print @drupal_render(drupal_get_form('gojira_login_form')); ?>        
                <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
            </div>
        </div>
    </div>
</div>