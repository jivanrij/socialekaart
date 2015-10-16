<div id="header">
  
  <?php global $user;
  if ($user->uid): ?>
    <div id="header_options">
        
        
        
      <?php $mobileDetect = new Mobile_Detect(); ?>
      <?php if(Subscriptions::currentGroupHasPayed()): ?>
        &nbsp;
        <a title="<?php echo t('Search over the entire country but limits the amount of results to 500'); ?>" class="global_search_header <?php echo (helper::value($user, GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD) ? 'on' : 'off'); ?>"><?php echo t('Search entire country'); ?></a>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <?php endif; ?>
            
      <?php if (user_access(helper::PERMISSION_PERSONAL_LIST)): ?>
          &nbsp;
        <a title="<?php echo t('Only search on your favorites'); ?>" class="favorite_header <?php echo (helper::value($user, GojiraSettings::CONTENT_TYPE_SEARCH_FAVORITES_FIELD) ? 'on' : 'off'); ?>"><?php echo t('Filter search on favorites'); ?></a>
      <?php endif; ?>

        <?php if(Subscriptions::currentGroupHasPayed() && Location::userHasMultipleLocationsStored()): ?>
          <label for="location_selector"><?php echo t('select practice'); ?></label>
            <?php $oCurrentLocation = Location::getCurrentLocationObjectOfUser(); ?>
            <select id="location_selector" name="location_selector">
                <?php foreach(Location::getUsersLocations() as $oLocation): ?>
                    <?php if($oLocation->status): ?><option <?php echo ($oCurrentLocation->nid == $oLocation->nid ? 'selected="selected"' : ''); ?>value="<?php echo $oLocation->nid; ?>"><?php echo $oLocation->title; ?></option><?php endif; ?>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>
  <?php endif; ?>
<?php print render($page['header_right']); ?>
<?php print render($page['header_left']); ?>
</div>