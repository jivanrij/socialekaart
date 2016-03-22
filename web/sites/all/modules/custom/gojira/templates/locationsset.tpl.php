<?php
drupal_add_js(array('gojira' => array('locationsset_has_filter' => 0)), 'setting');
$oLocationset = Locationsets::getInstance()->getCurrentLocationset();
if ($oLocationset) {
    $filter = null;
    if(isset($_GET['filter'])){
        $filter = $_GET['filter'];
    }
    $aLocations = Locationsets::getInstance()->getLocations(null,null,$filter);
    $sBody = $oLocationset->body[LANGUAGE_NONE][0]['value'];
    $sTitle = $oLocationset->title;
    drupal_add_js(array('gojira' => array('locationsset_title' => $sTitle)), 'setting');
    drupal_add_js(array('gojira' => array('locationsset_id' => $oLocationset->nid)), 'setting');
} else {
    $currentPractice = Location::getCurrentLocationNodeObjectOfUser();
    if (isset($_GET['filter'])) {
        
        $aLocations = Search::searchInOwnMap($_GET['filter']);

        $plotInfo = array();
        foreach ($aLocations as $aLocation) {
            $plotInfo[] = array(
                    'd' => 0,
                    's' => 0,
                    'n' => $aLocation->nid,
                    't' => $aLocation->title,
                    'lo' => $aLocation->longitude,
                    'la' => $aLocation->latitude
            );
        }
        drupal_add_js(array('gojira' => array('locationsset_filter_results_count' => count($plotInfo))), 'setting');
        drupal_add_js(array('gojira' => array('locationsset_filter_results' => $plotInfo)), 'setting');
        drupal_add_js(array('gojira' => array('locationsset_has_filter' => 1)), 'setting');
    } else {
        $aLocations = Favorite::getInstance()->getAllFavoriteLocations($currentPractice->nid);
    }

    $sBody = t('Your own personal map\'s decription.');
    $sTitle = 'Sociale kaart ' . $currentPractice->title;
    drupal_add_js(array('gojira' => array('locationsset_id' => "favorites")), 'setting');
}
$aCategories = Locationsets::getInstance()->getCategoriesFromLocationsArray($aLocations);

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
        <button class="close_box" title="Sluiten"></button>
        <h2><?php echo $sTitle; ?></h2>
        
        <form id="search_ownmap_form">
            <input class="rounded unshadow" placeholder="Zoek in uw sociale kaart" name="search_ownmap" id="search_ownmap" />
            <button class="fa"></button>
        </form>
        
        <p><?php echo $sBody; ?></p>
        <?php if (count($aCategories) > 0): ?>
            <label id="locations"><?php echo t("Show locations of specific category:"); ?></label>
            <ul id="locationsset_categories">
                <?php foreach ($aCategories as $aCategorie): ?>
                    <li rel="<?php echo $aCategorie->nid; ?>"><a class="locationset_show_cat"><?php echo $aCategorie->title; ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php elseif (!isset($_GET['filter'])): ?>
            <img style="border:1px #4d4d4d inset;" src="sites/all/modules/custom/gojira/img/search_result.png" alt="Zorgverlener" />
        <?php elseif (isset($_GET['filter'])): ?>
            <p><i><?php echo t("No locations found with this specific search term."); ?></i></p>
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
