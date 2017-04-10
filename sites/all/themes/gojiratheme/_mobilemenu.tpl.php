<div id="mobileheader">
    <div>
        <?php if (helper::agreedToConditions()): ?>
            <form action="/" method="GET" id="form-mobile-search">
                <input type="text" placeholder="<?php echo t('Search'); ?>" id="search_term_mobile" name="s" />
                <input type="hidden" name="m" value="1" />
                <i class="fa fa-search" id="search_submit_mobile" title="Zoeken"></i>
            </form>
        <?php endif; ?>
        <?php if (user_access(helper::PERMISSION_PERSONAL_LIST)): ?>
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
        
        <?php if(false): ?>
            <div class="select">
                <label>Zoeken in:</label>
                <select id="search_type_select_mobile">
                    <?php if (false && ser_access(helper::PERMISSION_LOCATIONSETS) && Locationsets::onLocationset()): ?><option <?php echo (Locationsets::onLocationset() ? 'selected="selected" ' : ''); ?>value="<?php echo helper::SEARCH_TYPE_LOCATIONSET; ?>"><?php echo Locationsets::getCurrentLocationsetTitle(); ?></option><?php endif; ?>
                    <option <?php echo ((Search::getSearchTypeBasedOnQuery()==helper::SEARCH_TYPE_REGION) ? 'selected="selected" ' : ''); ?>value="<?php echo helper::SEARCH_TYPE_REGION; ?>">praktijk regio</option>
                    <?php if (false && user_access(helper::PERMISSION_PERSONAL_LIST)): ?><option <?php echo ((Locationsets::onOwnMap() && !Locationsets::onLocationset()) ? 'selected="selected" ' : ''); ?>value="<?php echo helper::SEARCH_TYPE_OWNLIST; ?>">uw kaart</option><?php endif; ?>
                    <?php if (user_access(helper::PERMISSION_SEARCH_GLOBAL)): ?><option <?php echo ((Search::getSearchTypeBasedOnQuery()==helper::SEARCH_TYPE_COUNTRY) ? 'selected="selected" ' : ''); ?>value="<?php echo helper::SEARCH_TYPE_COUNTRY; ?>">het hele land</option><?php endif; ?>
                </select>
            </div>
        <?php endif; ?>
        
        <?php endif; ?>

    </div>
    <div>
        <?php print render($page['mobile_menu']); ?>
    </div>
</div>