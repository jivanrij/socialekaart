<div id="mobileheader">
    <div>
        <?php if (helper::agreedToConditions()): ?>
            <form action="/" method="GET" id="form-mobile-search">
                <input type="text" placeholder="<?php echo t('Search'); ?>" id="search_term_mobile" name="s" />
                <input type="hidden" name="m" value="1" />
                <i class="fa fa-search" id="search_submit_mobile" title="Zoeken"></i>
            </form>
        <?php endif; ?>
        <?php if (user_access(helper::PERM_MY_MAP)): ?>
            <button class="mymap" title="Naar mijn kaart" />
        <?php endif; ?>
        <button class="suggestlocation" title="Zorgverlener toevoegen" />
        <button class="tomobilemenu" title="Menu" />
    </div>
</div>
<div id="mobilemenu">
    <div class="options">
        <?php if (Subscriptions::currentGroupHasPayed() && Location::userHasMultipleLocationsStored()): ?>
            <div class="select">
                <label for="select_location_mobile"><?php echo t('select practice'); ?>:</label>
                <?php $oCurrentLocation = Location::getCurrentLocationObjectOfUser(); ?>
                <select id="select_location_mobile" name="select_location_mobile">
                    <?php foreach (Location::getUsersLocations() as $oLocation): ?>
                        <?php if ($oLocation->status): ?><option <?php echo ($oCurrentLocation->nid == $oLocation->nid ? 'selected="selected"' : ''); ?>value="<?php echo $oLocation->nid; ?>"><?php echo $oLocation->title; ?></option><?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

    </div>
    <div>
        <?php print render($page['mobile_menu']); ?>
    </div>
</div>
