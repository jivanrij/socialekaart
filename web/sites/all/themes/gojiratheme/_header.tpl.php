<div id="header">

    <?php global $user;
    if ($user->uid):
        ?>
        <div id="header_options">
            <?php if (Subscriptions::currentGroupHasPayed()): ?>
                <a title="<?php echo t('Search over the entire country but limits the amount of results to 500'); ?>" class="global_search_header <?php echo (helper::value($user, GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD) ? 'on' : 'off'); ?>"><?php echo t('Search entire country'); ?></a>
            <?php endif; ?>

            <?php if (false && user_access(helper::PERMISSION_PERSONAL_LIST)): ?>
                <a title="<?php echo t('Only search on your favorites'); ?>" class="favorite_header <?php echo (helper::value($user, GojiraSettings::CONTENT_TYPE_SEARCH_FAVORITES_FIELD) ? 'on' : 'off'); ?>"><?php echo t('Filter search on favorites'); ?></a>
            <?php endif; ?>

                <ul class="maplist">
                    <li>
                        <?php if(Locationsets::getInstance()->userHasRightToLocationssets() && count(Locationsets::getInstance()->getMapSetsForCurrentUser())>0): ?>
                            <a id="maps_hover_icon" title="<?php echo t('Your maps'); ?>" href="<?php echo url('ownlist'); ?>"></a>
                            <ul>
                                <li><a href="<?php echo url('ownlist'); ?>">Mijn kaart</a></li>
                                <li class="subtitle">(<?php echo t('Your own Social Map'); ?>)</li>
                                <?php foreach(Locationsets::getInstance()->getMapSetsForCurrentUser() as $map): ?>
                                    <li><a href="<?php echo url('node/'. $map->nid); ?>"><?php echo $map->title; ?></a></li>
                                    <?php if(trim(helper::value($map, GojiraSettings::CONTENT_TYPE_LOCATIONSET_SUBTITLE)) !== ''): ?>
                                        <li class="subtitle">(<?php echo helper::value($map, GojiraSettings::CONTENT_TYPE_LOCATIONSET_SUBTITLE); ?>)</li>
                                    <?php endif; ?>
                                    
                                    
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <a id="direct_link" title="<?php echo t('Your map'); ?>" href="<?php echo url('ownlist'); ?>"></a>
                        <?php endif; ?>
                    </li>
                </ul>


            <?php if (Subscriptions::currentGroupHasPayed() && Location::userHasMultipleLocationsStored()): ?>
                <label for="location_selector"><?php echo t('select practice'); ?></label>
                    <?php $oCurrentLocation = Location::getCurrentLocationObjectOfUser(); ?>
                <select id="location_selector" name="location_selector">
                    <?php foreach (Location::getUsersLocations() as $oLocation): ?>
                        <?php if ($oLocation->status): ?><option <?php echo ($oCurrentLocation->nid == $oLocation->nid ? 'selected="selected"' : ''); ?>value="<?php echo $oLocation->nid; ?>"><?php echo $oLocation->title; ?></option><?php endif; ?>
                <?php endforeach; ?>
                </select>
        <?php endif; ?>
        </div>
    <?php endif; ?>
<?php print render($page['header_menu']); ?>
</div>