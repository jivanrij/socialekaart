<h1><?php echo t('Password successfully changed.'); ?></h1>
<p>
  <?php echo t('You have successfully set your password.'); ?>
</p>
<p>
  <?php if(count(Location::getUsersLocations())==0): echo t('To make use of the functionality you need to enter your own location.'); endif; ?>
</p>