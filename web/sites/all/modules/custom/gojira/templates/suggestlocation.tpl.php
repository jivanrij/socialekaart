<?php if (DoubleLocationFormHelper::getInstance()->bErrorShown && count(DoubleLocationFormHelper::getInstance()->getDoubleLocations($_POST[GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD], $_POST[GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD], $_POST[GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD], $_POST[GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD]))): ?>
    <div id="doubles_wrapper">
        <div id="show_doubles">
            <p>
            <ul>
                <?php foreach (DoubleLocationFormHelper::getInstance()->getDoubleLocations($_POST[GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD], $_POST[GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD], $_POST[GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD], $_POST[GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD]) as $iNid => $oPossibleDouble): ?>
                    <li><a class="double_locs" id="double_loc_<?php echo $iNid; ?>"><?php echo $oPossibleDouble; ?></a></li>
                <?php endforeach; ?>
            </ul>
            </p>
        </div>
        <div id="show_double_info"></div>
    </div>
    <div class="gbutton_wrapper">
        <span class="gbutton rounded noshadow right">
            <input id="save_double_location" name="op" value="<?php echo t('Save anyways'); ?>" class="form-submit" type="submit">
        </span>
    </div>
<?php endif; ?>
<h1><?php echo drupal_get_title(); ?></h1>
<?php echo render($fForm); ?>
