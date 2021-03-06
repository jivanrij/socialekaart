<div class="container">
  <div class="row">
    <div class="col-sm-5 col-md-7 col-lg-8 header">
      <?php global $base_url; ?>
      <a class="hidden-md hidden-lg" href="<?php echo $base_url; ?>" title="socialekaart.care"><img src="/<?php echo drupal_get_path('theme', 'gojiratheme') . '/img/logo_small.png'; ?>" alt="logo" /></a>
      <a class="hidden-xs hidden-sm" href="<?php echo $base_url; ?>" title="socialekaart.care"><img src="/<?php echo drupal_get_path('theme', 'gojiratheme') . '/img/logo.png'; ?>" alt="logo" /></a>
    </div>
    <div class="col-sm-7 col-md-5 col-lg-4 header">
        <?php if (!user_is_logged_in()) : ?>
            <?php print @drupal_render(drupal_get_form('gojira_login_form')); ?>
            <?php $message = Messages::getFormMessage(); $error = '<a class="grey_password_reset" href="'.url('passwordreset').'">Nieuw wachtwoord instellen</a>'; ?>
            <?php if($message && $_GET['q'] !== 'register' && $_GET['q'] !== 'passwordreset'): ?>
                <?php $error = 'Uw inloggegevens kloppen niet, probeer het opnieuw of <a href="'.url('passwordreset').'">vraag een nieuw wachtwoord aan.</a>'; ?>
            <?php endif; ?>
            <div class="errormessage"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
  </div>
</div>
<?php include(drupal_get_path('theme', 'gojiratheme') . '/'.Template::getFrontPage()); ?>
<div class="container">
  <div class="row">
    <div class="col-sm-12">
      <div id="frontpage_footer"><a href="/AlgemeneVoorwaarden">Algemene voorwaarden</a> - SocialeKaart.care is een intiatief van Blijnder VOF - <a href="/contact" title="Contacteer">Contact</a></div>
    </div>
  </div>
</div>
