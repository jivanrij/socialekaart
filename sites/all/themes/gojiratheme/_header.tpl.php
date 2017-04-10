<div id="header">

    <?php global $user;
    if ($user->uid):
        ?>
        <div id="header_options">

            <?php if (user_access(helper::PERM_HUISARTS_MORE_PRACTICES) && Location::userHasMultipleLocationsStored()): ?>
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
                    <?php if(count(Locationsets::getInstance()->getMapSetsForCurrentUser())>0) : ?>
                        <a id="maps_hover_icon" title="<?php echo t('Your maps'); ?>" href="<?php echo url('ownlist'); ?>"><?php echo t('Your maps'); ?></a>
                        <ul>
                            <?php if(user_access(helper::PERM_MY_MAP)) : ?>
                                <li><a href="<?php echo url('ownlist'); ?>">Mijn kaart</a></li>
                                <li class="subtitle">(<?php echo t('Your own Social Map'); ?>)</li>
                            <?php endif; ?>
                            <?php foreach(Locationsets::getInstance()->getMapSetsForCurrentUser() as $map): ?>
                                <li><a href="<?php echo url('node/'. $map->nid); ?>"><?php echo $map->title; ?></a></li>
                                <?php if(trim(helper::value($map, GojiraSettings::CONTENT_TYPE_LOCATIONSET_SUBTITLE)) !== ''): ?>
                                    <li class="subtitle">(<?php echo helper::value($map, GojiraSettings::CONTENT_TYPE_LOCATIONSET_SUBTITLE); ?>)</li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if (user_access(helper::PERM_MANAGE_MAPS)) : ?>
                                <li><a href="<?php echo url('locationsetlist'); ?>">Beheer kaarten</a></li>
                                <li class="subtitle">(Hier kunt u losse kaarten voor gebruikers beheren)</li>
                            <?php endif; ?>
                        </ul>
                    <?php elseif(user_access(helper::PERM_MY_MAP)): ?>
                        <a id="direct_link" title="<?php echo t('Your map'); ?>" href="<?php echo url('ownlist'); ?>"><?php echo t('Your map'); ?></a>
                    <?php endif; ?>
                </li>
            </ul>

        <?php if (user_access(helper::PERM_ADD_LOCATION)): ?>
            <div class="menu_bar_icon">
                <a href="/locationcrud" title="<?php echo t('Add location if you are missing one.'); ?>"><?php echo t('Add location'); ?><i class="fa fa-plus-square"></i></a>
            </div>
        <?php endif; ?>

        </div>
    <?php endif; ?>
<?php print render($page['header_menu']); ?>
</div>
