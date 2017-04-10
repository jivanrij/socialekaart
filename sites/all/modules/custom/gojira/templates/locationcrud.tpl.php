
<section class="container bootstrapform">
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
        </div>
    </div>
    <?php if($showThanks) : ?>
        <div class="row">
            <div class="col-md-offset-3 col-md-6 header">
                <?php if($thanksType === '1') : ?>
                    <h1>Toegevoegd: <?php echo $thanksModel->title; ?></h1>
                    <p>
                        Bedankt voor het toevoegen van een nieuwe zorgverlener. We hebben de gegevens ontvangen en goed kunnen verwerken in het systeem.
                    </p>
                <?php elseif($thanksType === '2') : ?>
                    <h1>Aangepast: <?php echo $thanksModel->title; ?></h1>
                    <p>
                        Bedankt voor het aanpassen van de zorgverlener.
                    </p>
                <?php endif; ?>

            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-3 col-md-6 table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th colspan="2">Gegevens van de zorgverlener</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Categorie</td>
                            <td><?php echo $thanksModel->getCategoryName(); ?></td>
                        </tr>
                        <tr>
                            <td>Adres</td>
                            <td>
                                <?php echo $thanksModel->get(\Models\Location::ADDRESS_STREET_FIELD); ?> <?php echo $thanksModel->get(\Models\Location::ADDRESS_HOUSENUMBER_FIELD); ?><br />
                                <?php echo $thanksModel->get(\Models\Location::ADDRESS_POSTCODE_FIELD); ?> <?php echo $thanksModel->get(\Models\Location::ADDRESS_CITY_FIELD); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>E-mailadres</td>
                            <td><?php echo $thanksModel->get(\Models\Location::EMAIL_FIELD); ?></td>
                        </tr>
                        <tr>
                            <td>Telefoonnummer</td>
                            <td><?php echo $thanksModel->get(\Models\Location::TELEPHONE_FIELD); ?></td>
                        </tr>
                        <tr>
                            <td>Faxnummer</td>
                            <td><?php echo $thanksModel->get(\Models\Location::FAX_FIELD); ?></td>
                        </tr>
                        <tr>
                            <td>Website</td>
                            <td><a href="<?php echo $thanksModel->get(\Models\Location::URL_FIELD); ?>" title="ga naar de website" target="_blank"><?php echo $thanksModel->get(\Models\Location::URL_FIELD); ?></a></td>
                        </tr>
                        <tr>
                            <td>Opties</td>
                            <td>
                                <p><a href="/?loc=<?php echo $thanksModel->nid; ?>" class="btn btn-danger">Weergeven op de kaart</a></p>
                                <?php if (user_access(helper::PERM_CORRECT_LOCATION)) : ?>
                                    <p><a href="/locationcrud?nid=<?php echo $thanksModel->nid; ?>" class="btn btn-danger">Bewerk gegevens</a></p>
                                <?php endif; ?>
                                <?php if (user_access(helper::PERM_ADD_LOCATION)) : ?>
                                    <p><a href="/locationcrud" class="btn btn-danger">Voeg nieuwe zorgverlener toe</a></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-offset-3 col-md-6 controls">
                <a onclick="window.history.back()"><i class="fa fa-arrow-left" aria-hidden="true"></i> terug </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-3 col-md-6 header">
                <h1><?php echo $formData['pagetitle']; ?></h1>
                <p>
                    <?php echo $formData['pagesubtitle']; ?>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-3 col-md-6">
                <div class="alert alert-danger" <?php echo ($hasErrors ? '' : ' style="display:none;" '); ?>>
                    Iets met de opgegeven gegevens klopt niet:<br />
                    <?php foreach($formData['errors'] as $error) : ?>
                        - <?php echo $error; ?><br />
                    <?php endforeach; ?>

                    <?php if(count($formData['doubleLocations']) > 0) : ?>
                        <?php if(count($formData['doubleLocations']) > 1) : ?>
                            <br />De volgende zorgverleners zijn gevonden op hetzelfde adres:
                            <ul class="doubleLocations">
                                <?php foreach($formData['doubleLocations'] as $doubleLocation) : ?>
                                    <li><a href="/?loc=<?php echo $doubleLocation->nid; ?>" target="_blank"><?php echo $doubleLocation->title; ?> <i class="fa fa-external-link-square" aria-hidden="true"></i></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php elseif (count($formData['doubleLocations']) == 1) : ?>
                            De volgende zorgverlener is gevonden op dit adres:
                            <ul class="doubleLocations single"><li><a href="/?loc=<?php echo $doubleLocation->nid; ?>" target="_blank"><?php echo $doubleLocation->title; ?> <i class="fa fa-external-link-square" aria-hidden="true"></i></a></li></ul>
                        <?php endif; ?>
                        <p>Het kan voorkomen dat er meerdere zorgverleners op een locatie werkzaam zijn. U kunt er voor kiezen deze melding te negeren.</p>
                        <button class="btn btn-danger ignore_double_check">Toch toevoegen</button>
                    <?php endif; ?>

                </div>
                <form class="bootstrap locationcrud" method="POST">
                    <input type="hidden" class="required" name="longitude" value="<?php echo $formData['longitude']; ?>" data-validation-required="Er is geen plaats gevonden op het opgegeven adres." />
                    <input type="hidden" name="latitude" value="<?php echo $formData['latitude']; ?>" />
                    <input type="hidden" name="nid" value="<?php echo $formData['nid']; ?>" />
                    <input type="hidden" name="crudMode" value="<?php echo $formData['crudMode']; ?>" />
                    <input type="hidden" name="doubleCheck" value="<?php echo $formData['doubleCheck']; ?>" />
                    <div class="form-group title-group">
                        <label for="title">Titel</label>
                        <input type="text" name="title" class="form-control required" id="title" value="<?php echo $formData['title']; ?>" data-validation-required="De titel ontbreekt.">
                    </div>
                    <div class="form-group category-group">
                        <label for="exampleSelect1">Categorie</label>
                        <select name="category" class="form-control" id="category">
                            <?php foreach(Category::getCategoryTitles() as $nid=>$title) : ?>
                                <option <?php echo ($formData['category'] == $nid ? 'selected="selected"' : ''); ?>value="<?php echo $nid; ?>"><?php echo $title; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group email-group">
                        <label for="email">E-mailadres</label>
                        <input type="text" name="email" class="form-control" id="email" value="<?php echo $formData['email']; ?>">
                        <small class="text-muted">Dit e-mailadres zal alleen gebruikt worden om in SocialeKaart.care weer te geven.</small>
                    </div>
                    <div class="form-group url-group">
                        <label for="url">Website</label>
                        <input type="text" name="url" class="form-control" id="url" value="<?php echo $formData['url']; ?>" placeholder="">
                    </div>
                    <div class="form-group phone-group">
                        <label for="phone">Telefoonnummer</label>
                        <input type="text" name="phone" class="form-control" id="phone" value="<?php echo $formData['phone']; ?>" placeholder="">
                    </div>
                    <div class="form-group fax-group">
                        <label for="fax">Faxnummer</label>
                        <input type="text" name="fax" class="form-control" id="fax" value="<?php echo $formData['fax']; ?>" placeholder="">
                    </div>

                    <fieldset class="address">
                        <div class="form-group street-group">
                            <label for="street">Straat</label>
                            <input type="text" name="street" class="form-control required" id="street" value="<?php echo $formData['street']; ?>" data-validation-required="De straat is niet ingevuld.">
                        </div>
                        <div class="form-group housenumber-group">
                            <label for="housenumber">Huisnummer</label>
                            <input type="text" name="housenumber" class="form-control required" id="housenumber" value="<?php echo $formData['housenumber']; ?> "data-validation-required="Het huisnummer is niet ingevuld.">
                        </div>
                        <div class="form-group postcode-group">
                            <label for="postcode">Postcode</label>
                            <input type="text" name="postcode" class="form-control required" id="postcode" value="<?php echo $formData['postcode']; ?>" data-validation-required="De postcode is niet ingevuld.">
                        </div>
                        <div class="form-group city-group">
                            <label for="city">Plaats</label>
                            <input type="text" name="city" class="form-control required" id="city" value="<?php echo $formData['city']; ?>" data-validation-required="De stad is niet ingevuld.">
                        </div>
                    </fieldset>
                    <div class="form-group crudmap-group">
                        <div id="crudmap" style="position:relative;width:100%;height:200px;"></div>
                    </div>
                    <?php if (user_access(helper::PERM_HUISARTS_LABELS)) : ?>
                        <div class="form-group labels-group">
                            <label for="labels">Pas hier de labels van de zorgverlener</label>
                            <select name="labels" id="labels" multiple="multiple" class="tokenize-sample">
                                <?php foreach(Labels::getAllLabels() as $tid=>$title) : ?>
                                    <option <?php echo (is_array($formData['tags']) && array_key_exists($tid, $formData['tags']) ? 'selected="selected"' : ''); ?>value="<?php echo $tid; ?>"><?php echo $title; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Labels worden gebruikt om kenmerken van zorgverleners te definiëren, waar vervolgens op gezocht kan worden.</small>
                        </div>
                        <input type="hidden" name="tags" id="tags" />
                    <?php endif; ?>
                    <?php if (user_access(helper::PERM_MY_MAP)) : ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="ownmap" <?php echo (Favorite::getInstance()->isFavorite($formData['nid'], $formData['activePracticeNid']) ? 'checked="checked"' : ''); ?> value="1">
                                Weergeven op mijn eigen sociale kaart.
                            </label>
                        </div>
                    <?php endif; ?>
                    <?php foreach(Locationsets::getInstance()->getViewableOrModeratedLocationsets() as $set) : ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="locationsets" <?php echo ($set->hasLocation($formData['nid']) ? 'checked="checked"' : ''); ?> name="locationsets[]" value="<?php echo $set->nid; ?>">
                                Weergeven op de kaart <i><?php echo $set->title; ?></i>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-danger">Versturen</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-3 col-md-6 controls">
                <a onclick="window.history.back()"><i class="fa fa-arrow-left" aria-hidden="true"></i> terug </a>
            </div>
        </div>
    <?php endif; ?>
</section>
<script>
    var redIcon = L.icon({
        iconUrl: '/sites/all/modules/custom/gojira/js/images/gojira_marker_location.png',
        shadowUrl: '/sites/all/modules/custom/gojira/js/images/markers-shadow.png',
        iconSize: [22, 30], // size of the icon
        shadowSize: [24, 15], // size of the shadow
        iconAnchor: [11, 23], // point of the icon which will correspond to marker's location
        shadowAnchor: [4, 8], // the same for the shadow
        popupAnchor: [-2, -23] // point from which the popup should open relative to the iconAnchor
    });

    <?php if($formData['crudMode'] == 'edit') : ?>
        var map = L.map('crudmap', { zoomControl:false, scrollWheelZoom: false, touchZoom: false, dragging: false, doubleClickZoom: false}).setView([<?php echo $formData['latitude']; ?>, <?php echo $formData['longitude']; ?>], 16);
        var marker = L.marker([<?php echo $formData['latitude']; ?>, <?php echo $formData['longitude']; ?>], {icon: redIcon}).addTo(map);
    <?php else: ?>
        var map = L.map('crudmap', { zoomControl:false, scrollWheelZoom: false, touchZoom: false, dragging: false, doubleClickZoom: false}).setView([Drupal.settings.gojira.latitude, Drupal.settings.gojira.longitude], 8);
    <?php endif; ?>

    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: '',
        zoom: Drupal.settings.gojira.zoom,
        id: Drupal.settings.gojira.mapbox_projectid,
        accessToken: Drupal.settings.gojira.mapbox_accesstoken
    }).addTo(window.map);

    map.attributionControl.setPrefix("");

    jQuery('#labels').tokenize();

    jQuery("fieldset.address input").on('change',function() {
        if (jQuery("fieldset.address streetinput#street").val() !== '' &&
            jQuery("fieldset.address input#housenumber").val() !== '' &&
            jQuery("fieldset.address input#city").val() !== '' &&
            jQuery("fieldset.address input#postcode").val() !== '') {
                jQuery.ajax({
                    type: 'POST',
                    url: '/?q=ajax/checklocation',
                    dataType: 'json',
                    data: {
                        pc: jQuery("fieldset.address input#postcode").val(),
                        pcnumber: jQuery("fieldset.address input#housenumber").val(),
                        city: jQuery("fieldset.address input#city").val(),
                        street: jQuery("fieldset.address input#street").val(),
                    },
                    success: function (data) {
                        console.log(data);
                        if (data == 'fail') {
                            jQuery("fieldset.address .street-group, fieldset.address .housenumber-group, fieldset.address .city-group, fieldset.address .postcode-group").addClass('has-error');
                        } else {
                            jQuery("fieldset.address .street-group, fieldset.address .housenumber-group, fieldset.address .city-group, fieldset.address .postcode-group").removeClass('has-error');
                            map.setView([data.latitude, data.longitude], 16);
                            jQuery("form.locationcrud input[name=longitude]").val(data.longitude);
                            jQuery("form.locationcrud input[name=latitude]").val(data.latitude);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // setDangerLocationForm();
                    }
                });
        }
    });

    jQuery('button.ignore_double_check').on('click',function(){
        jQuery("form.locationcrud input[name=doubleCheck]").val(0);
        jQuery('form.bootstrap button[type=submit]').trigger('click');
    });

    jQuery('form.bootstrap button[type=submit]').on('click',function(e){
        e.preventDefault();

        var success = true;

        // remove error class from all fields & hide error box
        jQuery('form.bootstrap .form-group').removeClass('has-error');
        jQuery('div.alert').hide();
        jQuery('div.alert').html('<b>Iets met de opgegeven gegevens klopt niet:</b><br />');

        jQuery('form.bootstrap input').each(function(e){
            if(jQuery(this).hasClass('required')) {

                if(jQuery(this).val().trim() == '') {

                    success = false;
                    jQuery('div.alert').append(' - ' + jQuery(this).data('validation-required') + '<br />');

                    // text input specific error handling
                    if(jQuery(this).attr('type') == 'text') {
                        jQuery(this).closest('.form-group').addClass('has-error');
                    }

                    // hidden input specific error handling
                    if(jQuery(this).attr('type') == 'hidden') {
                    }
                }


            }

        });
        if(success) {
            jQuery('div.alert').hide();
            jQuery('div.has-error').removeClass('has-error');

            if (jQuery('#labels').val() !== null) {
                jQuery('#tags').val(jQuery('#labels').val().toString());
            } else {
                jQuery('#tags').val('');
            }

            jQuery('form.bootstrap').submit();
        } else {
            jQuery('html, body').animate({scrollTop:0}, 'slow');
            jQuery('div.alert').show();
        }
    });
</script>
