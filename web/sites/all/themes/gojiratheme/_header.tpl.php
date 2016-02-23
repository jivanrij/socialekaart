<div id="header">

    <?php global $user;
    if ($user->uid):
        ?>
        <div id="header_options">
                
            <div class="header_select">
                <label>Zoeken in:</label>
                <select id="search_type_select">
                    <?php if (user_access(helper::PERMISSION_LOCATIONSETS) && Locationsets::onLocationset()): ?>
                        <option <?php echo (Search::searchTypeIsSelected(helper::SEARCH_TYPE_LOCATIONSET) ? 'selected="selected" ' : ''); ?>value="<?php echo helper::SEARCH_TYPE_LOCATIONSET; ?>">
                            <?php echo Locationsets::getCurrentLocationsetTitle(); ?>
                        </option>
                    <?php endif; ?>
                    <option <?php echo (Search::searchTypeIsSelected(helper::SEARCH_TYPE_REGION) ? 'selected="selected" ' : ''); ?>value="<?php echo helper::SEARCH_TYPE_REGION; ?>">
                        <?php echo t('Practice region'); ?>
                    </option>
                    <?php if (user_access(helper::PERMISSION_PERSONAL_LIST)): ?>
                        <option <?php echo (Search::searchTypeIsSelected(helper::SEARCH_TYPE_OWNLIST) ? 'selected="selected" ' : ''); ?>value="<?php echo helper::SEARCH_TYPE_OWNLIST; ?>">
                            <?php echo t('Your own map'); ?>
                        </option>
                    <?php endif; ?>
                    <?php if (user_access(helper::PERMISSION_SEARCH_GLOBAL)): ?>
                        <option <?php echo (Search::searchTypeIsSelected(helper::SEARCH_TYPE_COUNTRY) ? 'selected="selected" ' : ''); ?>value="<?php echo helper::SEARCH_TYPE_COUNTRY; ?>">
                            <?php echo t('The howl country'); ?>
                        </option>
                    <?php endif; ?>
                </select>
            </div>
            
            <?php if (Subscriptions::currentGroupHasPayed() && Location::userHasMultipleLocationsStored()): ?>
                <div class="header_select">
                    <label for="location_selector"><?php echo t('select practice'); ?></label>
                        <?php $oCurrentLocation = Location::getCurrentLocationObjectOfUser(); ?>
                    <select id="location_selector" name="location_selector">
                        <?php foreach (Location::getUsersLocations() as $oLocation): ?>
                            <?php if ($oLocation->status): ?><option <?php echo ($oCurrentLocation->nid == $oLocation->nid ? 'selected="selected"' : ''); ?>value="<?php echo $oLocation->nid; ?>"><?php echo $oLocation->title; ?></option><?php endif; ?>
                    <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            
            
            <ul class="maplist">
                <li>
                    <?php if(Locationsets::getInstance()->userHasRightToLocationssets() && count(Locationsets::getInstance()->getMapSetsForCurrentUser())>0): ?>
                        <a id="maps_hover_icon" title="<?php echo t('Your maps'); ?>" href="<?php echo url('ownlist'); ?>"><?php echo t('Your maps'); ?></a>
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
                    <?php elseif(Subscriptions::currentGroupHasPayed()): ?>
                        <a id="direct_link" title="<?php echo t('Your map'); ?>" href="<?php echo url('ownlist'); ?>"><?php echo t('Your map'); ?></a>
                    <?php endif; ?>
                </li>
            </ul>
                
        <div class="menu_bar_icon">
            <a href="/suggestlocation" title="<?php echo t('Add location if you are missing one.'); ?>"><?php echo t('Add location'); ?><i class="fa fa-plus-square"></i></a>
        </div>
                
        </div>
    <?php endif; ?>
<?php print render($page['header_menu']); ?>
</div>