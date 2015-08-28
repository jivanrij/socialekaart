<p>
  <?php echo $output['txt']; ?>
</p>
<p>
  <?php if(count(Location::getUsersLocations())==0): echo t('To make use of the functionality you need to enter your own location.'); endif; ?>
</p>