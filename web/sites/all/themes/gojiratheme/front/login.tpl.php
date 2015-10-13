<div class="container">
    <div class="row">
        <div class="col-sm-9 frontpage_block">
            <div>
                <h1><?php echo helper::getText('FRONTPAGE_LOGIN', true); ?></h1> 
                <p><?php echo helper::getText('FRONTPAGE_LOGIN'); ?></p>
                <?php //print render($page['content']); ?>
                <?php if (variable_get('gojira_subscribe_possible')): ?>
                    <p><a class="register gbutton rounded noshadow left gbutton_widest" title="<?php echo t('Register'); ?>" href="/register" id="splash_register"><span><?php echo t('Register a new account'); ?></span></a></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-sm-3 frontpage_block hidden-xs form-block">
            <div>
                <?php if (variable_get('gojira_haweb_sso_button_visible')): ?>
                    <a class="haweb_link hidden-sm hidden-md" href="<?php echo $base_url; ?>/user/login" title="socialekaart.care"><img src="/<?php echo drupal_get_path('theme', 'gojiratheme') . '/img/inloggen_haweb.png'; ?>" alt="Logo HAweb" /></a>
                    <a class="haweb_link hidden-lg hidden-sm" href="<?php echo $base_url; ?>/user/login" title="socialekaart.care"><img src="/<?php echo drupal_get_path('theme', 'gojiratheme') . '/img/inloggen_haweb_md.png'; ?>" alt="Logo HAweb" /></a>
                    <a class="haweb_link hidden-lg hidden-md" href="<?php echo $base_url; ?>/user/login" title="socialekaart.care"><img src="/<?php echo drupal_get_path('theme', 'gojiratheme') . '/img/inloggen_haweb_sm.png'; ?>" alt="Logo HAweb" /></a>
                <?php endif; ?>
                <h1><?php echo t('Login'); ?></h1>
                <?php print @drupal_render(drupal_get_form('gojira_login_form')); ?>        
                <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
            </div>
        </div>

        <div class="col-sm-3 frontpage_block hidden-md hidden-sm hidden-lg">
            <div>

                <h1><?php echo t('Unfortunately!'); ?></h1>
                <p>
                    <?php echo t('SocialeKaart.care is not developed for small mobile devices. Please return to the site with a tablet of PC or find another way to raise your resolution.'); ?>
                </p>

            </div>
        </div>
    </div>
</div>