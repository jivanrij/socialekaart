<h1><?php echo drupal_get_title(); ?></h1>
<?php
$location = $output['location'];
?>
<p>
  <?php echo t('Do you think something about the global information of <i>%location_name%</i> is incorrect? Please tell us through this form so we can correct this.', array('%location_name%'=>$location->title)); ?>
</p>
<p>
  
    <p>
      <?php echo $location->title; ?><br />
      <?php echo helper::value($location, GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD); ?> <?php echo helper::value($location, GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD); ?><br />
      <?php echo helper::value($location, GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD); ?>, <?php echo helper::value($location, GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD); ?><br />
      <?php echo t('Tel.:'); ?> <?php echo helper::value($location, GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD); ?><br />
      <br />
      <?php echo t('Category:'); ?> <?php echo Category::getCategoryName($location); ?>
    </p>


</p>
<?php echo render($output['form']); ?>



