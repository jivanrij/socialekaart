<?php
$oLocationset = Locationsets::getInstance()->getCurrentLocationset();
if($oLocationset){
    $aLocations = Locationsets::getInstance()->getLocations();
    $sBody = $oLocationset->body[LANGUAGE_NONE][0]['value'];
    $sTitle = $oLocationset->title;
    drupal_add_js(array('gojira' => array('locationsset_id' => $oLocationset->nid)), 'setting');
}else{
    $aLocations = Favorite::getInstance()->getAllFavoriteLocations(true);
    $sBody = t('Your own personal map\'s decription.');
    $sTitle = t('Your own personal map.');
    drupal_add_js(array('gojira' => array('locationsset_id' => "favorites")), 'setting');
}
$aCategories = Locationsets::getCategoriesFromLocationsArray($aLocations);
drupal_add_js(array('gojira' => array('page' => 'locationsset')), 'setting');

?>
<div id="locationset_wrapper" class="rounded">
    <h2><?php echo $sTitle; ?></h2>
    <p><?php echo $sBody; ?></p>
    <?php if(count($aCategories)>0): ?>
    <label id="locations"><?php echo t("Show locations of specific category:"); ?></label>
    <ul id="locationsset_categories">
        <?php foreach($aCategories as $aCategorie): ?>
            <li rel="<?php echo $aCategorie->nid; ?>"><a class="locationset_show_cat"><?php echo $aCategorie->title; ?></a></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <?php if(count($aLocations)>0): ?>
        <label id="locations"><?php echo t("Select a location:"); ?></label>
        <ul id="locationsset_locations">
            <?php foreach($aLocations as $oLocation): $oCategory = Category::getCategoryOfLocation($oLocation); ?>
                <li rel="<?php echo $oCategory->nid; ?>">
                    <a class="locationset_show_loc" href="#<?php echo $oLocation->nid; ?>"><?php echo $oLocation->title; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div id="locationsset_location_details">

    </div>
</div>