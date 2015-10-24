<div id="mobileheader">
    <div>
        <?php if (helper::agreedToConditions()): ?>
            <form action="/" method="GET" id="form-mobile-search">
                <input type="text" placeholder="<?php echo t('Search'); ?>" name="tags" />
                <i class="fa fa-search" onClick="jQuery(this).closest('form').submit();" title="Zoeken"></i>
            </form>
        <?php endif; ?>
        <?php if (user_access(helper::PERMISSION_PERSONAL_LIST)): ?>
            <button class="fa fa-map-o" title="Naar mijn kaart" />
        <?php endif; ?>


        <button class="fa fa-plus-square" title="Zorgverlener toevoegen" />    
        <button class="fa fa-bars" title="Menu" />
    </div>
</div>
<div id="mobilemenu">
    <div class="options">
        <?php if (Subscriptions::currentGroupHasPayed() && Location::userHasMultipleLocationsStored()): ?>
            <label for="select_location_mobile"><?php echo t('select practice'); ?>:</label>
            <?php $oCurrentLocation = Location::getCurrentLocationObjectOfUser(); ?>
            <select id="select_location_mobile" name="select_location_mobile">
                <?php foreach (Location::getUsersLocations() as $oLocation): ?>
                    <?php if ($oLocation->status): ?><option <?php echo ($oCurrentLocation->nid == $oLocation->nid ? 'selected="selected"' : ''); ?>value="<?php echo $oLocation->nid; ?>"><?php echo $oLocation->title; ?></option><?php endif; ?>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <?php if (Subscriptions::currentGroupHasPayed()): ?>
            <a class="search_global <?php echo (helper::value($user, GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD) ? 'on' : 'off'); ?>" title="<?php echo t('Search over the entire country but limits the amount of results to 500'); ?>" >  <?php echo t('Search entire country'); ?></a>
        <?php endif; ?>

        <?php if (user_access(helper::PERMISSION_PERSONAL_LIST)): ?>
            <a title="<?php echo t('Only search on your favorites'); ?>" class="search_favorite <?php echo (helper::value($user, GojiraSettings::CONTENT_TYPE_SEARCH_FAVORITES_FIELD) ? 'on' : 'off'); ?>"> <?php echo t('Filter search on favorites'); ?></a>
        <?php endif; ?>

    </div>
    <div>
        <?php print render($page['mobile_menu']); ?>
    </div>
</div>