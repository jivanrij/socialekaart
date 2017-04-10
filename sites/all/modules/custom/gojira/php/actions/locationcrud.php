<?php
/**
 * Edit page of a location
 *
 * @global type $user
 * @return type
 */
function locationcrud() {

    // set default form values
    $formData['nid'] = '';
    $formData['crudMode'] = 'new';
    $formData['errors'] = array();

    $formData = loadFormdata($formData);

    $formData['activePracticeNid']  = Location::getCurrentLocationObjectOfUser()->nid;

    $formData['pagetitle'] = 'Nieuwe zorgverlener';
    $formData['pagesubtitle'] = 'Vul het onderstaande formulier in om een nieuwe zorgverlener toe te voegen.';
    if (isset($_GET['nid'])) {
        // nid in url, so we are in the edit mode
        $formData['crudMode'] = 'edit';
        $formData['pagetitle'] = $formData['title'];
        $formData['pagesubtitle'] = 'Gebruik het onderstaande formulier om de gegevens van deze zorgverlener aan te passen.';
    }

    if (isset($_POST['nid'])) {

        // post, let's validate & save
        $formData = validateLocationcrud($formData);

        if (count($formData['errors']) == 0) {
            // no errors, save & redirect
            $nid = saveLocation($formData);
            if($formData['crudMode'] === 'new') {
                $thanksType = 1; // thanks for adding a new location
            } else {
                $thanksType = 2; // thanks for editing an existing location
            }
            header('Location: /locationcrud?tt=' . $thanksType . '&nid=' . $nid);
            exit;
        }
    }

    $showThanks = false;
    $thanksModel = null;
    if (isset($_GET['tt'])) {
        $thanksModel = \Models\Location::load($_GET['nid']);
        $showThanks = true;
    }

    $tt = '';
    if(isset($_GET['tt'])) {
        $tt = $_GET['tt'];
    }

    return theme('locationcrud', array('formData' => $formData, 'hasErrors' => count($formData['errors']), 'showThanks' => $showThanks, 'thanksType' => $tt, 'thanksModel' => $thanksModel));
}

function validateLocationcrud(&$formData) {

    $errors = array();

    if (user_access(helper::PERM_HUISARTS_LABELS)) {
        $location = null;
        if ((trim($formData['city']) !== '') &&
            (trim($formData['street']) !== '') &&
            (trim($formData['housenumber']) !== '') &&
            (trim($formData['postcode']) !== '')
        ) {
            $location = Location::getLocationForAddress(
                Location::formatAddress(
                    $formData['city'], $formData['street'], $formData['housenumber'], $formData['postcode']
                )
            );
        }

        if (!$location) {
            $errors['adres'] = 'Er is geen plaats gevonden op het opgegeven adres.';
        }
    }

    if ($formData['crudMode'] == 'new') {
        $knownTitle = db_query("select title from {node} where title = :t1", array(':t1' => $formData['title']))->fetchField();
    } else {
        $knownTitle = db_query("select title from {node} where title = :t1 and nid != :nid", array(':t1' => $formData['title'], ':nid' => $formData['nid']))->fetchField();
    }
    if ($knownTitle) {
        $errors['title'] = 'Deze titel is al in gebruik voor een andere zorgverlener.';
    }
    if (trim($formData['title']) == '') {
        $errors['title'] = 'De opgegeven titel is leeg.';
    }

    // if a location is new, we need to check the coordinates
    $doubleLocations = array();
    if ($formData['doubleCheck'] === '1') {
        $doubleLocations = array();
        if ($location) {
            $rResults = db_query("select nid, title, X(point) as x, Y(point) as y from {node} where type = 'location' and status = 1 and X(point) = :longitude and Y(point) = :latitude",
                array(':longitude' => $location->longitude, ':latitude' => $location->latitude))->fetchAll();
            foreach ($rResults as $oResult) {
                $locationModel = \Models\Location::load($oResult->nid);
                if ($locationModel->getCategoryName() !== 'Huisarts') {
                    $doubleLocations[$locationModel->nid] = $locationModel;
                }
            }
        }
        if (count($doubleLocations) >= 1) {
            $errors['double'] = 'Er zijn al zorgverleners op dit adres gevonden.';
        }
    }

    $formData['doubleLocations'] = $doubleLocations;
    $formData['errors'] = $errors;

    return $formData;
}

/*
 * Saves a existing or a new location based on the form data
 *
 * @param $formData
 * @return string
 */
function saveLocation($formData) {

    // get a model to work with
    if ($formData['crudMode'] == 'new' ) {
        $locationModel = \Models\Location::create($formData['title'], false);
    } else {
        $locationModel = \Models\Location::load($formData['nid']);
    }

    $locationModel->setVisible(true);
    $locationModel->setCategory($formData['category']);
    $locationModel->setEmail($formData['email']);
    $locationModel->setUrl($formData['url']);
    $locationModel->setTelephone($formData['phone']);
    $locationModel->setFax($formData['fax']);
    $locationModel->setCity($formData['city']);
    $locationModel->setStreet($formData['street']);
    $locationModel->setHousenumber($formData['housenumber']);
    $locationModel->setPostcode($formData['postcode']);



    // save all the tags to the location
    if (user_access(helper::PERM_HUISARTS_LABELS)) {
        if (trim($formData['tags']) !== '') {
            $tags = explode(',', $formData['tags']);

            if (count($tags) > 0) {
                $allTags = array();
                foreach ($tags as $tag) {
                    if (is_numeric(trim($tag))) {
                        $tid = $tag;
                    } else {
                        if (!helper::inBlacklist($tag)) {
                            $tag = Labels::prepairLabel($tag);
                            $tid = Labels::saveLabel($tag);
                        }
                    }
                    $allTags[] = $tid;
                }

                $locationModel->setLabelTids($allTags);
            }
        }
    }

    if (user_access(helper::PERM_MY_MAP)) {
        if ($formData['ownmap'] === 1) {
            Favorite::getInstance()->setFavorite($locationModel->nid);
        } else {
            Favorite::getInstance()->removeFromFavorite($locationModel->nid);
        }
    }

    // all the locationsets linked to this location
    $oldLinked = \Models\Locationset::getLocationsetsConnectedToLocation($locationModel->nid);
    // all locations sets able to get a link with
    $optionalLocationsets = Locationsets::getInstance()->getViewableOrModeratedLocationsets();
    // get all new linked locationsets based onthe form data
    $newLinked = $formData['locationsets'];
    // loop through all possible connections

    // loop through each available
    foreach($optionalLocationsets as $nid => $optionalLocationset){

        // continue if the connection existed & stays
        if(array_key_exists($nid, $oldLinked) && in_array($nid, $newLinked)) {
            continue;
        }

        // remove if the connects existed, but is cancled
        if(array_key_exists($nid, $oldLinked) && !in_array($nid, $newLinked)) {
            // disconnect the current location to this locationset
            $locationset = $oldLinked[$nid];
            $locationset->removeLocation($locationModel);
        }

        // add connection it it was not there, but is wanted based on the form data
        if(!array_key_exists($nid, $oldLinked) && in_array($nid, $newLinked)) {
            // connect the current location to this locationset
            $locationset = \Models\Locationset::load($nid);
            $locationset->addLocation($locationModel);
        }
    }

    $locationModel->save();

    return $locationModel->nid;
}

// loads all the required data in the $formData depending on the situation
function loadFormdata($formData) {
    $formData['doubleLocations'] = array();

    if (isset($_GET['nid']) && !isset($_POST['nid'])) {
        // load initial data for edit
        $locationModel = \Models\Location::load($_GET['nid']);
        $formData['nid'] = $locationModel->nid;
        $formData['title'] = $locationModel->title;
        $formData['category'] = $locationModel->get(\Models\Location::CATEGORY_FIELD);
        $formData['email'] = $locationModel->get(\Models\Location::EMAIL_FIELD);
        $formData['url'] = $locationModel->get(\Models\Location::URL_FIELD);
        $formData['phone'] = $locationModel->get(\Models\Location::TELEPHONE_FIELD);
        $formData['fax'] = $locationModel->get(\Models\Location::FAX_FIELD);
        $formData['city'] = $locationModel->get(\Models\Location::ADDRESS_CITY_FIELD);
        $formData['street'] = $locationModel->get(\Models\Location::ADDRESS_STREET_FIELD);
        $formData['housenumber'] = $locationModel->get(\Models\Location::ADDRESS_HOUSENUMBER_FIELD);
        $formData['postcode'] = $locationModel->get(\Models\Location::ADDRESS_POSTCODE_FIELD);
        $formData['longitude'] = $locationModel->longitude;
        $formData['latitude'] = $locationModel->latitude;
        $formData['tags'] = $formData['labels'] = $locationModel->labels;
        $formData['activePracticeNid'] = Location::getCurrentLocationObjectOfUser()->nid;
        $formData['doubleCheck'] = 0; // we don't need to check this when we are editing
        $formData['locationsets'] = array(); // not used in this situation
        return $formData;
    }

    if (isset($_POST['nid'])) {
        // load data in POST, for edit AND add mode
        $formData['nid'] = $_POST['nid'];
        $formData['title'] = trim($_POST['title']);
        $formData['category'] = trim($_POST['category']);
        $formData['email'] = trim($_POST['email']);
        $formData['url'] = trim($_POST['url']);
        $formData['phone'] = trim($_POST['phone']);
        $formData['fax'] = trim($_POST['fax']);
        $formData['city'] = trim($_POST['city']);
        $formData['street'] = trim($_POST['street']);
        $formData['housenumber'] = trim($_POST['housenumber']);
        $formData['postcode'] = trim($_POST['postcode']);
        $formData['longitude'] = trim($_POST['longitude']);
        $formData['latitude'] = trim($_POST['latitude']);

        if(isset($_POST['labels'])) {
            $formData['labels'] = $_POST['labels'];
        } else {
            $formData['labels'] = array();
        }

        $formData['tags'] = $_POST['tags'];
        $formData['doubleCheck'] = $_POST['doubleCheck'];
        if(isset($_POST['locationsets'])) {
            $formData['locationsets'] = $_POST['locationsets'];
        } else {
            $formData['locationsets'] = array();
        }
        if (!empty($_POST['ownmap'])) {
            $formData['ownmap'] = 1;
        } else {
            $formData['ownmap'] = 0;
        }
        return $formData;
    }

    if (!isset($_GET['nid']) && !isset($_POST['nid'])) {
        // new location
        $formData['nid'] = '';
        $formData['title'] = '';
        $formData['category'] = '';
        $formData['email'] = '';
        $formData['url'] = '';
        $formData['phone'] = '';
        $formData['fax'] = '';
        $formData['city'] = '';
        $formData['street'] = '';
        $formData['housenumber'] = '';
        $formData['postcode'] = '';
        $formData['longitude'] = '';
        $formData['latitude'] = '';
        $formData['labels'] = '';
        $formData['tags'] = array();
        $formData['doubleCheck'] = 1;
        $formData['locationsets'] = array(); // not used in this situation
        $formData['ownmap'] = 0;
        return $formData;
    }
}
