<?php
$oLocationset = Locationsets::getInstance()->getCurrentLocationset();
if ($oLocationset) {
    $aLocations = Locationsets::getInstance()->getLocations();
    $sBody = $oLocationset->body[LANGUAGE_NONE][0]['value'];
    $sTitle = $oLocationset->title;
    drupal_add_js(array('gojira' => array('locationsset_id' => $oLocationset->nid)), 'setting');
} else {
    $currentPractice = Location::getCurrentLocationNodeObjectOfUser();
    $aLocations = Favorite::getInstance()->getAllFavoriteLocations(true, $currentPractice->nid);
    $sBody = t('Your own personal map\'s decription.');
    $sTitle = 'Sociale kaart ' . $currentPractice->title;
    drupal_add_js(array('gojira' => array('locationsset_id' => "favorites")), 'setting');
}
$aCategories = Locationsets::getCategoriesFromLocationsArray($aLocations);

$aCategoriesSorted = array();
foreach ($aCategories as $aCategorie) {
    $aCategoriesSorted[$aCategorie->title] = $aCategorie;
}
ksort($aCategoriesSorted);
$aCategories = $aCategoriesSorted;

drupal_add_js(array('gojira' => array('page' => 'locationsset')), 'setting');
?>
<div id="locationset_wrapper" class="rounded">
    <div>
        <h2><?php echo $sTitle; ?></h2>
        <p><?php echo $sBody; ?></p>
        <?php if (count($aCategories) > 0): ?>
            <label id="locations"><?php echo t("Show locations of specific category:"); ?></label>
            <ul id="locationsset_categories">
                <?php foreach ($aCategories as $aCategorie): ?>
                    <li rel="<?php echo $aCategorie->nid; ?>"><a class="locationset_show_cat"><?php echo $aCategorie->title; ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <img style="border:1px #4d4d4d inset;" src="sites/all/modules/custom/gojira/img/search_result.png" alt="Zorgverlener" />
        <?php endif; ?>

        <?php if (count($aLocations) > 0): ?>
            <label id="locations"><?php echo t("Select a location:"); ?></label>
            <ul id="locationsset_locations">
                <?php foreach ($aLocations as $oLocation): $oCategory = Category::getCategoryOfLocation($oLocation); ?>
                    <li rel="<?php echo $oCategory->nid; ?>">
                        <a class="locationset_show_loc" href="#<?php echo $oLocation->nid; ?>"><?php echo $oLocation->title; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
