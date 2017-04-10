<?php
drupal_add_js(array('gojira' => array('locationset_has_filter' => 0)), 'setting');
$oLocationset = Locationsets::getInstance()->getCurrentLocationset();
if ($oLocationset) {
    $filter = null;
    if(isset($_GET['filter'])){
        $filter = $_GET['filter'];
    }
    $oLocations = Locationsets::getInstance()->getLocations(null,null,$filter);

    $aCategories = Locationsets::getInstance()->getCategoriesFromLocationsArray($oLocations);

    $sBody = $oLocationset->body[LANGUAGE_NONE][0]['value'];
    $sTitle = $oLocationset->title;
    drupal_add_js(array('gojira' => array('locationset_title' => $sTitle)), 'setting');
    drupal_add_js(array('gojira' => array('locationset_id' => $oLocationset->nid)), 'setting');
} else {
    $currentPractice = Location::getCurrentLocationNodeObjectOfUser();
    if (isset($_GET['filter'])) {

        $aLocations = Search::searchInOwnMap($_GET['filter']);

        $plotInfo = array();
        foreach ($aLocations as $aLocation) {
            $plotInfo[] = array(
                    'd' => 0,
                    's' => 0,
                    'n' => $aLocation['nid'],
                    't' => $aLocation['title'],
                    'lo' => $aLocation['longitude'],
                    'la' => $aLocation['latitude']
            );
        }
        drupal_add_js(array('gojira' => array('locationset_filter_results_count' => count($plotInfo))), 'setting');
        drupal_add_js(array('gojira' => array('locationset_filter_results' => $plotInfo)), 'setting');
        drupal_add_js(array('gojira' => array('locationset_has_filter' => 1)), 'setting');

        $oLocations = array();
        foreach($aLocations as $aLocation){
            $oLocations[] = node_load($aLocation['nid']);
        }

        $aCategories = Locationsets::getInstance()->getCategoriesFromLocationsArray($oLocations);
    } else {
        $oLocations = Favorite::getInstance()->getAllFavoriteLocations($currentPractice->nid);
        $aLocations = array();


        foreach($oLocations as $oLocation)
        {
            $aLocations[$oLocation->nid]['node'] = $oLocation;
            $aLocations[$oLocation->nid]['title'] = $oLocation->title;
            $aLocations[$oLocation->nid]['nid'] = $oLocation->nid;
        }

        $aCategories = Locationsets::getInstance()->getCategoriesFromLocationsArray($oLocations);
    }

    $sBody = t('Your own personal map\'s decription.');
    $sTitle = 'Sociale kaart ' . $currentPractice->title;
    drupal_add_js(array('gojira' => array('locationset_id' => "favorites")), 'setting');
}


$aCategoriesSorted = array();
foreach ($aCategories as $aCategorie) {
    $aCategoriesSorted[$aCategorie->title] = $aCategorie;
}
ksort($aCategoriesSorted);
$aCategories = $aCategoriesSorted;


drupal_add_js(array('gojira' => array('page' => 'locationset')), 'setting');
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
            <ul id="locationset_categories">
                <?php foreach ($aCategories as $aCategorie): ?>
                    <li rel="<?php echo $aCategorie->nid; ?>"><a class="locationset_show_cat"><?php echo $aCategorie->title; ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php elseif (!isset($_GET['filter'])): ?>
            <img style="border:1px #4d4d4d inset;" src="sites/all/modules/custom/gojira/img/search_result.png" alt="Zorgverlener" />
        <?php elseif (isset($_GET['filter'])): ?>
            <p><i><?php echo t("No locations found with this specific search term."); ?></i></p>
        <?php endif; ?>

        <?php if (count($oLocations) > 0): ?>
            <label id="locations"><?php echo t("Select a location:"); ?></label>
            <ul id="locationset_locations">
                <?php foreach ($oLocations as $location): ?>
                    <?php $oCategory = Category::getCategoryOfLocation($location); ?>
                    <li rel="<?php echo $oCategory->nid; ?>">
                        <a class="locationset_show_loc" href="#<?php echo $location->nid; ?>"><?php echo $location->title; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
