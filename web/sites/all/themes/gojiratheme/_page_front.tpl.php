<div class="container">
  <div class="row">
    <div class="col-sm-6 header header_small hidden-md hidden-sm hidden-lg">
      <?php global $base_url; ?>
      <a href="<?php echo $base_url; ?>" title="socialekaart.care"><img src="/<?php echo drupal_get_path('theme', 'gojiratheme') . '/img/logo_small.png'; ?>" alt="logo" /></a>
    </div>
    <div class="col-sm-6 header hidden-xs">
      <?php global $base_url; ?>
      <a href="<?php echo $base_url; ?>" title="socialekaart.care"><img src="/<?php echo drupal_get_path('theme', 'gojiratheme') . '/img/logo.png'; ?>" alt="logo" /></a>
    </div>
    <div class="col-sm-6 header hidden-sm hidden-xs">
      <span class="subtitle"><?php echo t('The digital social map for practitioners'); ?></span>
    </div>
  </div>
</div>
<?php include(drupal_get_path('theme', 'gojiratheme') . '/'.Template::getFrontPage()); ?>
<div class="container">
  <div class="row">
    <div class="col-sm-12">
      <div id="frontpage_footer"><?php echo t('SocialeKaart.care is an initiative of Blijnder'); ?></div>
    </div>
  </div>
</div>