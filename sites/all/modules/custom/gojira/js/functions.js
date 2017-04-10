function l(log) {
    console.log(log)
}

/**
 * Tells you if we are in a mobile view
 *
 * @returns Boolean
 */
function onMobileView() {
    if (jQuery(window).width() >= 668) {
        return false;
    }
    return true;
}

// get's you the needed height of the window based on the content
function getHeightPx() {

    if (onMobileView()) { // mobile view

        var crud_holder_height = 0;
        if (typeof jQuery('#crud_holder').css('height') !== 'undefined') {
            crud_holder_height = parseInt(jQuery('#crud_holder').css('height').replace('px', ''));
        }

        var mobileheader_height = 0;
        if (typeof jQuery('#mobileheader').css('height') !== 'undefined') {
            mobileheader_height = parseInt(jQuery('#mobileheader').css('height').replace('px', ''));
        }

        var content_height = crud_holder_height + mobileheader_height;

        var window_height = parseInt(jQuery(window).height());

        return (window_height - content_height);
    } else { // no mobile view
        var has_crud_holder_element = false;
        if (typeof jQuery('#crud_holder') !== 'undefined') {
            has_crud_holder_element = true;
        }

        if (Drupal.settings.gojira.page == 'locationset') {
            var selected_location_info = 0;
            if (typeof jQuery('#selected_location_info').css('height') !== 'undefined') {
                selected_location_info = parseInt(jQuery('#selected_location_info').css('height').replace('px', ''));
            }
            minimal_needed_height = selected_location_info + parseInt(jQuery('#locationset_wrapper').css('height').replace('px', '')) + 85;

        } else if (jQuery('#selected_location_info').length) {
            var selected_location_info = parseInt(jQuery('#selected_location_info').css('height').replace('px', ''));
            var search_results = 1;
            if (typeof jQuery('#search_results').css('height') !== 'undefined') {
                search_results = parseInt(jQuery('#search_results').css('height').replace('px', ''));
            }
            var minimal_needed_height = selected_location_info + search_results + 70;
        } else if (has_crud_holder_element) {
            if (typeof jQuery('#crud_holder').css('height') == 'undefined') {
                var minimal_needed_height = 70;
            } else {
                var minimal_needed_height = parseInt(jQuery('#crud_holder').css('height').replace('px', '')) + 70;
            }
        } else {
            minimal_needed_height = 70;
        }

        // default height of the window when there is no extra needed space
        var default_height = parseInt(jQuery(window).height() - 29);

        // if we need less then we have, just use the space we have.
        if (minimal_needed_height < default_height) {
            minimal_needed_height = default_height;
        }

        return minimal_needed_height;
    }
}

// show the first step of the tutorial
function showTutorial() {
    if (!onMobileView()) {
        jQuery.colorbox({
            href: '/?q=ajax/showtutorial',
            closeButton: false,
            escKey: false,
            overlayClose: false,
            width: '600px',
            opacity: 0.5,
            onComplete: function () {
                bindTutorialButtons();
            }
        });
    }
}

// bind the tutorial buttons
function bindTutorialButtons() {
    jQuery("#cboxLoadedContent #close_tutorial_button").click(function (e) {
        e.preventDefault();
        tutorialButtonClick(this);
    });
    jQuery("#cboxLoadedContent #back_tutorial_button").click(function (e) {
        e.preventDefault();
        tutorialButtonClick(this);
    });
    jQuery("#cboxLoadedContent #forward_tutorial_button").click(function (e) {
        e.preventDefault();
        tutorialButtonClick(this);
    });
}
// handle a click on a tutorial button
function tutorialButtonClick(button) {
    var ref = jQuery(button).attr('ref');
    if (ref == 'quit') {
        // if it's a quit button, tell the server and close the tutorial
        jQuery.ajax({
            url: "/?q=ajax/showtutorial&step=" + ref,
            type: 'POST',
            success: function (data) {
                jQuery.colorbox.close();
            }
        });
    } else {
        // show next step and bind new buttons
        var width = '600px';
        if (onMobileView()) {
            var width = '80%';
        }
        jQuery.colorbox({escKey: false, closeButton: false, width: width, opacity: 0.5, overlayClose: false, href: "/?q=ajax/showtutorial&step=" + ref, onComplete: function () {
                bindTutorialButtons()
            }});
    }
}

/**
 * Binds the event's of the remove label buttons
 * This is the button on the search result to remove a label if ithas a score of 0
 *
 * @param {type} selector
 * @returns {undefined}
 */
function bindLabelRemoveButtons(selector) {

    if (typeof selector == 'undefined') {
        selector = 'button.labelremovebutton';
    }

    jQuery(selector).click(function (e) {
        e.preventDefault();

        var element = this;

        var labeldiv = jQuery(element).closest('div.label');

        var tid = jQuery(labeldiv).attr('id').replace('label_', '');
        var nid = jQuery(element).closest('div.search_result_wrapper').attr('id').replace('location_', '');

        jQuery.ajax({
            url: '/?q=ajax/removelabel&nid=' + nid + '&tid=' + tid + '&mid=' + Drupal.settings.gojira.selected_map,
            type: 'POST',
            success: function (data) {
                jQuery(element).closest('div.label_wrapper').remove();
                // check if we need to resize
                jQuery(window).trigger('resize');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                somethingWrongMessage();
            }
        });
    });
}

/**
 * Attaches the events of the label button. You are able to add the event on a specific label button by giving a selector.
 * Used when a label is added by ajax.
 */
function bindLabelButtons(selector) {

    if (typeof selector == 'undefined') {
        selector = 'button.labelbutton';
    }

    jQuery(selector).click(function (e) {

        e.preventDefault();

        var labeldiv = jQuery(this).closest('div.label');
        var tid = jQuery(labeldiv).attr('id').replace('label_', '');
        var nid = jQuery(this).closest('div.search_result_wrapper').attr('id').replace('location_', '');

        if (jQuery(labeldiv).hasClass('plus')) {
            jQuery('#label_' + tid, '#location_' + nid).removeClass('plus').addClass('minus');
            jQuery('#label_' + tid + ' button.labelbutton span', '#location_' + nid).html('+');
            var amount = parseInt(jQuery(' #label_' + tid + ' div.labelnumber', '#location_' + nid).html()) - 1;
            jQuery('#label_' + tid + ' div.labelnumber', '#location_' + nid).html(amount);

            if (amount == 0) {
                // add remove button if score == 0
                var html = '<button class="labelremovebutton"><span class="tooltip_not" title="Verwijderen">Verwijderen</span></button>';
                jQuery('#label_' + tid, '#location_' + nid).append(html);
                // add event to let the button work
                bindLabelRemoveButtons('#label_' + tid + ' button.labelremovebutton', '#location_' + nid);
            }

            // 0 i'm viewing the global map, -1 my map, and > 0 means viewing a locationset
            //Drupal.settings.gojira.selected_map

            jQuery.ajax({
                url: '/?q=ajax/unlikelabel&nid=' + nid + '&tid=' + tid + '&mid=' + Drupal.settings.gojira.selected_map,
                type: 'POST',
                success: function (data) {
                    focusLocation(nid);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    somethingWrongMessage();
                }
            });

        } else {

            jQuery('#label_' + tid, '#location_' + nid).removeClass('minus');
            jQuery('#label_' + tid, '#location_' + nid).addClass('plus');

            jQuery('div.labels #label_' + tid + ' button.labelbutton span', '#location_' + nid).html('-');
            var amount = parseInt(jQuery('#location_' + nid + ' div.labels #label_' + tid + ' div.labelnumber').html()) + 1;
            jQuery('#location_' + nid + ' div.labels #label_' + tid + ' div.labelnumber').html(amount);
            if (amount >= 1) {
                jQuery(' #label_' + tid + ' button.labelremovebutton', '#location_' + nid).remove();
            }

            // 0 i'm viewing the global map, -1 my map, and > 0 means viewing a locationset
            //Drupal.settings.gojira.selected_map

            jQuery.ajax({
                url: '/?q=ajax/likelabel&nid=' + nid + '&tid=' + tid + '&mid=' + Drupal.settings.gojira.selected_map,
                type: 'POST',
                success: function (data) {
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    somethingWrongMessage();
                }
            });
        }
    });
}

/*
 * This is the favorite switch on the location detail form
 */
function bindFavoriteSwitch() {

    jQuery('ul.locationset_selector a').on('click', function(e){
        e.preventDefault();
        var element = this;
        var locationNid = jQuery(element).closest('div.search_result_wrapper').attr('id').replace('location_', '');
        var locationsetNid = jQuery(element).attr('ref');

        var url = '';

        if (jQuery(element).hasClass('checked')) {
            if(jQuery(element).hasClass('mymap')) {
                // set favorite off
                url = '/?q=ajax/setfavorite&turn=off&nid=' + locationNid;
            }else{
                url = '/?q=ajax/setonlocationset&turn=off&location=' + locationNid + '&locationset=' + locationsetNid;
                // set locationset off
            }
        }else{
            if(jQuery(element).hasClass('mymap')) {
                // set favorite on
                url = '/?q=ajax/setfavorite&turn=on&nid=' + locationNid;
            }else{
                // set locationset on
                url = '/?q=ajax/setonlocationset&turn=on&location=' + locationNid + '&locationset=' + locationsetNid;
            }
        }
        jQuery(element).toggleClass('checked');
        jQuery.ajax({
            url: url,
            type: 'POST',
            success: function (data) {
                if(data != 'success'){
                    jQuery(element).toggleClass('checked');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                jQuery(element).toggleClass('checked');
            }
        });

    });
}

function addNewLabel(element) {
    var nid = jQuery(element).closest('div.search_result_wrapper').attr('id').replace('location_', '');
    var label = jQuery('#new_label_' + nid).val();

    if (label == 'label toevoegen') {
        return;
    }

    // 0 i'm viewing the global map, -1 my map, and > 0 means viewing a locationset
    //Drupal.settings.gojira.selected_map

    jQuery.ajax({
        url: '/?q=ajax/savenewlabel&nid=' + nid + '&label=' + label + '&mid=' + Drupal.settings.gojira.selected_map,
        type: 'POST',
        dataType: 'json',
        success: function (data) {
            if (data.success == false) {
//                alert(data.error);
            } else {
                focusLocation(nid);
                // check if we need to resize
                jQuery(window).trigger('resize');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            somethingWrongMessage();
        }
    });
}

// draws the default map with the self location on it
function setupMapDefault() {

    if (jQuery('#map').length == 0) {
        return;
    }

    window.blackIcon = L.icon({
        iconUrl: '/sites/all/modules/custom/gojira/js/images/gojira_marker_self.png',
        shadowUrl: '/sites/all/modules/custom/gojira/js/images/markers-shadow.png',
        iconSize: [24, 32], // size of the icon
        shadowSize: [26, 17], // size of the shadow
        iconAnchor: [11, 23], // point of the icon which will correspond to marker's location
        shadowAnchor: [4, 8], // the same for the shadow
        popupAnchor: [-2, -23] // point from which the popup should open relative to the iconAnchor
    });

    window.redIcon = L.icon({
        iconUrl: '/sites/all/modules/custom/gojira/js/images/gojira_marker_location.png',
        shadowUrl: '/sites/all/modules/custom/gojira/js/images/markers-shadow.png',
        iconSize: [22, 30], // size of the icon
        shadowSize: [24, 15], // size of the shadow
        iconAnchor: [11, 23], // point of the icon which will correspond to marker's location
        shadowAnchor: [4, 8], // the same for the shadow
        popupAnchor: [-2, -23] // point from which the popup should open relative to the iconAnchor
    });

    window.mixedIcon = L.icon({
        iconUrl: '/sites/all/modules/custom/gojira/js/images/gojira_marker_mixed.png',
        shadowUrl: '/sites/all/modules/custom/gojira/js/images/markers-shadow.png',
        iconSize: [22, 30], // size of the icon
        shadowSize: [24, 15], // size of the shadow
        iconAnchor: [11, 23], // point of the icon which will correspond to marker's location
        shadowAnchor: [4, 8], // the same for the shadow
        popupAnchor: [-2, -23] // point from which the popup should open relative to the iconAnchor
    });

    jQuery('#map').css('height', getHeightPx());

    window.map = new L.Map('map', {zoomControl: false, center: new L.LatLng(Drupal.settings.gojira.latitude, Drupal.settings.gojira.longitude), zoom: Drupal.settings.gojira.zoom});

    new L.Control.Zoom({position: 'bottomright'}).addTo(window.map);

    //L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(window.map);

    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: '',
        zoom: Drupal.settings.gojira.zoom,
        id: Drupal.settings.gojira.mapbox_projectid,
        accessToken: Drupal.settings.gojira.mapbox_accesstoken
    }).addTo(window.map);

    window.map.attributionControl.setPrefix("<a target='_new' href='/AlgemeneVoorwaarden'>Algemene Voorwaarden</a> <a href='/user/logout'>Uitloggen</a>");

    window.markers = new L.FeatureGroup();
    window.map.addLayer(window.markers);

    window.self_marker = new L.FeatureGroup();
    window.map.addLayer(window.self_marker);

    if (Drupal.settings.gojira.show_self) {
        var self_marker = L.marker([Drupal.settings.gojira.latitude, Drupal.settings.gojira.longitude], {icon: window.blackIcon})
                .setBouncingOptions({bounceHeight: 1, contractHeight: 3, bounceSpeed: 20, contractSpeed: 150})
                .on('click', function () {
                    this.toggleBouncing();
                }).addTo(window.map);

        window.self_marker.addLayer(self_marker);
    }
    jQuery(window).trigger('resize');
}


// trigger the ajax search call & handle it and then binds new func. by calling the bind function.
// Also set's the map up with the setupMapDefault function
function bindGojirasearch() {
    jQuery("#search_form form").submit(function (e) {
        e.preventDefault();
        if(Drupal.settings.gojira.page !== 'locationset'){
            // not on my map
           doSearchCall(encodeURIComponent(jQuery('#gojirasearch_search_term').val()));
        }else{
            // on my map, so go off it because this is a normal search action
            var s = encodeURIComponent(jQuery('#gojirasearch_search_term').val());
            window.location = '/?s=' + s;
        }

    });
}

function doSearchCall(searchFor) {
    openOverlay();

    if (searchFor == 'ownlist') {
        jQuery('#search_result_info').hide();
    } else {
        jQuery('#crud_holder').hide();
    }
    if (searchFor !== 'locationset') {
        jQuery('#locationset_wrapper').remove();
        jQuery('#search_result_info').hide();
    }

    window.map.removeLayer(window.markers);
    window.markers = new L.FeatureGroup();
    window.map.addLayer(window.markers);

    jQuery.ajax({
        url: '/?q=ajax/search&s=' + searchFor + '&mid=' + Drupal.settings.gojira.selected_map,
        type: 'GET',
        dataType: 'json',
        success: function (data) {

            if (typeof data.mapSearchResultsCount == 'undefined') {
                somethingWrongMessage();
            }

            jQuery('#ajax_search_results').html(data.results_html);

            populateMap(data.mapSearchResults, data.mapSearchResultsCount);

            if (data.boxInfo === null || typeof data.boxInfo == 'undefined') {
                window.map.setView([data.latitude, data.longitude], data.zoom);
            } else {
                window.map.fitBounds([
                    [data.boxInfo.latLow, data.boxInfo.lonLow],
                    [data.boxInfo.latHigh, data.boxInfo.lonHigh]
                ]);
            }

            bindAfterSearch(true,true,data.s);

            if (data.single_location) {
                jQuery('#loc_' + data.by_id).click();
                jQuery('#search_results').hide();
            }

            closeOverlay();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            somethingWrongMessage();
        }
    });
}

// function to bind everything after a search is done
function bindAfterSearch(bind_list, bind_details, query) {

    if (typeof bind_list == 'undefined') {
        bind_list = true;
    }
    if (typeof bind_details == 'undefined') {
        bind_details = true;
    }

    if (bind_list) {
        jQuery("#search_results ul li a").click(function (e) {
            e.preventDefault();
            var nid = jQuery(this).attr('id').toString().replace('loc_', '');
            focusLocation(nid);
        });
        jQuery('div.results_paging a').on('click', function (e) {
            e.preventDefault();
            jQuery('#search_results > div > div.results_paging').hide();
            jQuery('#search_results ul').hide();
            var page_class = jQuery(this).attr('ref');
            jQuery('ul.' + page_class).show();
            jQuery('div.results_paging.' + page_class).show();

            jQuery(window).trigger('resize');

        });
        jQuery('#search_own_area').click(function (e) {
            e.preventDefault();
            doSearchCall(encodeURIComponent(jQuery('#gojirasearch_search_term').val()), 1);
        });

        jQuery("#switch_to_region_search").on('click',function(e){
            e.preventDefault();
            window.location = '/?s='+query+'&search_type=region';
        });
        jQuery("#switch_to_country_search").on('click',function(e){
            e.preventDefault();
            window.location = '/?s='+query+'&search_type=country';
        });
    }

    if (bind_details) {
        bindLabelButtons();
        bindLabelRemoveButtons();
        bindFavoriteSwitch();
        bindAutocompleteAllTags("input.new_label");
        // add new label to location on form submit
        jQuery("form.new_label_form").submit(function (e) {
            e.preventDefault();
            addNewLabel(this);
        });
        // add new label to location on add button click
        jQuery("button.add_new_label").click(function (e) {
            e.preventDefault();
            addNewLabel(this);
        });
    }

    bindCloseButtons();

    jQuery("input.new_label").click(function () {
        if (jQuery(this).val() == 'label toevoegen') {
            jQuery(this).val('');
            jQuery(this).css('color', '#b7072a');
        }
    });
}


// get's the current active popup, retrieves the nid of the
// location and refers to that location, while hiding the popup
function gotoLocation(nid) {
    if (typeof nid == 'undefined') {
        var nid = jQuery("span.open_location_popup").text();
    }
    jQuery("div.leaflet-popup").css('display', 'none');
    window.location = '/?loc=' + nid;
}

function focusLocation(nid) {
    L.Marker.stopAllBouncingMarkers();

    if (typeof nid == 'undefined') {
        var nid = jQuery("span.open_location_popup").text();
        jQuery("div.leaflet-popup").css('display', 'none');
    }

    openOverlay();

    if (Drupal.settings.gojira.page != 'showlocation' && Drupal.settings.gojira.page != 'locationset') {
        //do some paging in the list, not if we are in the showlocation/locationset
        jQuery('li.active').removeClass('active');
        jQuery('a#loc_' + nid).closest('li').addClass('active');
        var page_number = jQuery('a#loc_' + nid).closest('ul').attr('class').replace('page_', '').replace(' rl', '');
        jQuery('a[ref=page_' + page_number + ']').click();
    }

    jQuery.ajax({
        url: '/?q=ajax/singlesearchresult&nid=' + nid,
        type: 'POST',
        dataType: 'json',
        success: function (data) {

            if (jQuery("#ajax_search_results > div#search_result_info").length == 0) {
                // sometimes we don't have a search_result_info, let's create it
                jQuery("#ajax_search_results").html('<div id="search_result_info"><div id="selected_location_info" class="rounded">' + data.html + '</div></div>');
            } else {
                // default, just hang the result in the dom
                jQuery("#selected_location_info").html(data.html);
            }

            if (jQuery("#content_holder #locationset_wrapper").length == 1) {
                // and when we are on a locationset page, let's add some stuff for that....
                correctHeightForLocationsetSearchResult();
                jQuery('#locationset_locations li').removeClass('active');
                jQuery('#locationset_locations li a[href=#' + nid + ']').closest('li').addClass('active');
            }


            // move to it
            if (!onMobileView()) {
                if (typeof data.latitude == 'string') {
                    window.map.panTo([data.latitude, data.longitude]);
                }
            }

            bindAfterSearch(false, true);

            jQuery(window).trigger('resize');
            jQuery('#selected_location_info').removeClass('hidden');
            closeOverlay();
            if ((window.markerMapping[nid] !== undefined) && (window.markers._layers[window.markerMapping[nid]] !== undefined)) {
                window.markers._layers[window.markerMapping[nid]].toggleBouncing();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            somethingWrongMessage();
        }
    });
}



function bindSettings() {
    jQuery("a.delete_location").click(function (e) {
        e.preventDefault();
        if (confirm(Drupal.settings.gojira.delete_warning))
        {
            window.location = jQuery(this).attr('href');
            return;
        }
    });

    jQuery("#settings_payment_info_switch").click(function (e) {
        e.preventDefault();
        jQuery("#settings_payment_info").show('slow', function () {
            jQuery(window).trigger('resize');
        });
        jQuery("#settings_payment_info_switch").hide();
    });
}

function bindFaq() {

    jQuery("#faq_sections section h1").click(function () {
        jQuery('.accordion_content.open').hide('fast', function () {
            jQuery(this).removeClass('open');
            jQuery(window).trigger('resize');
        });
        jQuery('.accordion_content', jQuery(this).closest('section')).show('fast', function () {
            jQuery(this).addClass('open');
            jQuery(window).trigger('resize');
        });
    });
}

function bindAutocompleteAllTags(element_selector) {
    jQuery(element_selector)
            .bind("keydown", function (event) {

                // this part gives the UL of the autosuggest a custom unique class. if this is not done we can't assign css for each seporate one
                var menu_class = "ul_" + element_selector.replace('#', '').replace('.', '').replace(']', '').replace('[', '').replace('=', '');
                var ul_id = "#" + jQuery($(this).data("ui-autocomplete").menu.activeMenu[0]).attr('id');
                if (jQuery(ul_id).hasClass(menu_class) == false) {
                    jQuery(ul_id).addClass(menu_class);
                }

                // unable to input a point
                if (event.keyCode == 190) {
                    event.preventDefault();
                }
                // unable to input a comma
                if (event.keyCode == 188) {
                    event.preventDefault();
                }
                // no space
                if (event.keyCode == 32) {
                    if ('input.new_label' == element_selector) {
                        if (hasWhiteSpace(this.value)) {
                            event.preventDefault();
                        }
                    }
                }

                if (event.keyCode === $.ui.keyCode.TAB &&
                        $(this).data("ui-autocomplete").menu.active) {
                    event.preventDefault();
                }
            }).autocomplete({
        source: function (request, response) {
            $.getJSON('/?q=ajax/locationtags' + '&mid=' + Drupal.settings.gojira.selected_map, {
                term: extractLast(request.term)
            }, response);
        },
        search: function () {
            // custom minLength
            var term = extractLast(this.value);
            if (term.length < 1) {
                return false;
            }
        },
        focus: function () {
            // prevent value inserted on focus
            return false;
        },
        select: function (event, ui) {
            var terms = split(this.value);
            // remove the current input
            terms.pop();
            // add the selected item
            terms.push(ui.item.value);
            // add placeholder to get the comma-and-space at the end
            terms.push("");
            this.value = terms.join(" ");
            return false;
        }
    });
}

// check if the string has a whitespace
function hasWhiteSpace(s) {
    return /\s/g.test(s);
}

// gets the nid based on the lat & long in the search result
function getLocationIdByLatLng(lat, lng) {
    if (Drupal.settings.gojira.searchresults) {
        for (var i = 0; i < Drupal.settings.gojira.resultcount; i++) {
            var thisResult = Drupal.settings.gojira.searchresults[i];
            if (thisResult.lo = lng && thisResult.la == lat) {
                return thisResult.n;
            }
        }
    }
    return 0;
}

// needed by bindLocationPropertiesAutocomplete()
function split(val) {
    return val.split(/ \s*/);
}
// needed by bindLocationPropertiesAutocomplete()
function extractLast(term) {
    return split(term).pop();
}

function bindGlobal() {

    setupMapDefault();

    bindAutocompleteAllTags("#gojirasearch_search_term");

    // prevent the menu with a dropdown option to do something on a click
    jQuery('ul.menu li.expanded > a, #maps_hover_icon').click(function (e) {
        e.preventDefault();
    });

    jQuery('#edit-submit').click(function (e) {
        e.preventDefault();
        openOverlay();
        jQuery(this).closest('form').submit();
    });

    if (Drupal.settings.gojira.page != 'locationset') {
        bindGojirasearch();
    }

    jQuery('#location_selector').change(function () {
        openOverlay();

        jQuery.ajax({
            type: "POST",
            url: '/?q=ajax/picklocation&nid=' + jQuery(this).val() + '&mid=' + Drupal.settings.gojira.selected_map,
            dataType: 'json',
            success: function (data) {
                if (Drupal.settings.gojira.page == 'ownlist' || jQuery("#gojirasearch_search_term").val().trim().length <= 0) {
                    location.reload();
                } else {
                    window.map.removeLayer(window.self_marker);
                    window.self_marker = new L.FeatureGroup();

                    var self_marker = L.marker([data.latitude, data.longitude], {icon: window.blackIcon})
                            .setBouncingOptions({bounceHeight: 1, contractHeight: 3, bounceSpeed: 20, contractSpeed: 150}).addTo(window.map);
                    window.self_marker.addLayer(self_marker);

                    jQuery("#search_form form").trigger('submit');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                somethingWrongMessage();
            }
        });
    });
}

function bindCloseButtons(){
    jQuery(".close_box").click(function () {
        if(jQuery(this).parent().hasClass('search_result_wrapper')){
            // search result close button
            jQuery("#selected_location_info").html('');
        }else if(jQuery(this).parent().parent().attr('id') == 'locationset_wrapper'){
            jQuery("#locationset_wrapper").remove();
        }else{
            // result list close button
            jQuery("#search_results").html('');
        }
    });
}

function bindSuggestlocation() {
    jQuery('a.double_locs').click(function () {
        var nid = jQuery(this).attr('id').replace('double_loc_', '');
        jQuery.ajax({
            url: '/?q=ajax/locationinfo&nid=' + nid + '&mid=' + Drupal.settings.gojira.selected_map,
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                jQuery('#show_double_info').html('<p><b>' + data['sTitle'] + '</b> uit ' + data['sCity'] + '<br /><i>Categorie:</i> ' + data['sCategory'] + '<br /><i>Labels:</i> ' + data['sLabels'] + '<br /><a href="/?loc=' + data['iNode'] + '" title="Locatie weergeven in ander window" target="_new">Weergeven in nieuw window</a><br /><br />' + data['sImproveLink'] + '</p>');
                jQuery(window).trigger('resize');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                somethingWrongMessage();
            }
        });
    });

    jQuery('#save_double_location').click(function (e) {
        e.preventDefault();
        jQuery('input[name=save_double_location]').val('screwit');
        jQuery('form#gojira-suggestlocation-form').submit();
    });
}

function bindLocationFinder() {
    jQuery('#edit-field-address-streetnumber, #edit-field-address-city, #edit-field-address-street, #edit-field-address-postcode').change(function () {

        if ((jQuery('#edit-field-address-city').val().trim().length != 0) && (jQuery('#edit-field-address-streetnumber').val().trim().length != 0)
                && (jQuery('#edit-field-address-street').val().trim().length != 0) && (jQuery('#edit-field-address-postcode').val().trim().length != 0)) {
            addressLookup();
        }

//        if ((jQuery('#edit-field-address-city').val().trim().length == 0) && (jQuery('#edit-field-address-street').val().trim().length == 0)) {
//            postcodeLookup()
//        } else {
//            addressLookup();
//        }
    });
}


/**
 * Bind the functions to show and hide the specific fields of the inform form
 */
function bindInformForm() {
    if (jQuery('#gojira-inform-form #edit-type-of-problem').val() == 'wrong_title') {
        jQuery('#gojira-inform-form div.form-item-title').show();
    } else {
        jQuery('#gojira-inform-form div.form-item-title').hide();
    }

    jQuery('#gojira-inform-form #edit-type-of-problem').change(function () {
        if (jQuery('#gojira-inform-form #edit-type-of-problem').val() == 'wrong_title') {
            jQuery('#gojira-inform-form div.form-item-title').show('slow');
        } else {
            jQuery('#gojira-inform-form div.form-item-title').hide('slow');
        }
    });

    //form-item-title
}

function postcodeLookup() {
    jQuery.ajax({
        type: 'POST',
        url: '/?q=ajax/postcodesuggest',
        dataType: 'json',
        data: {
            pc: jQuery('#edit-field-address-postcode').val(),
            pcnumber: jQuery('#edit-field-address-streetnumber').val(),
        },
        success: function (data) {
            if (data != 'fail') {
                window.map.setView([data.latitude, data.longitude], data.zoom);
                jQuery('#edit-field-address-city').val(data.city);
                jQuery('#edit-field-address-street').val(data.street);
                jQuery('#edit-field-telephone').focus();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            //somethingWrongMessage();
        }
    });
}

function addressLookup() {
    jQuery.ajax({
        type: 'POST',
        url: '/?q=ajax/checklocation' + '&mid=' + Drupal.settings.gojira.selected_map,
        dataType: 'json',
        data: {
            pc: jQuery('#edit-field-address-postcode').val(),
            pcnumber: jQuery('#edit-field-address-streetnumber').val(),
            city: jQuery('#edit-field-address-city').val(),
            street: jQuery('#edit-field-address-street').val(),
        },
        success: function (data) {
            if (data != 'fail') {
                window.map.setView([data.latitude, data.longitude], data.zoom);
            } else {
                window.map.setView([Drupal.settings.gojira.latitude, Drupal.settings.gojira.longitude], window.zoomlevel_country);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            //somethingWrongMessage();
        }
    });
}

function somethingWrongMessage() {
    closeOverlay();
    alert('Er is iets verkeerd gegaan, als dit het geval blijft. Neem contact op met de beheerder.');
}

function openOverlay() {
    jQuery('.overlay_wait').css('display', 'block');
}
function closeOverlay() {
    jQuery('.overlay_wait').css('display', 'none');
}

/**
 * This function is used to inform the admins that a user thinks
 * that there are double locations in the system.
 *
 * @param string nids
 * @returns null
 */
function reportDoublePractices(nids, uid) {
    openOverlay();
    jQuery.ajax({
        url: "/?q=ajax/reportdouble&nids=" + nids,
        type: 'POST',
        success: function (data) {
            jQuery('#report_double_' + uid).html('<p>Bedankt! We zullen dit controleren.</p>');
            closeOverlay();
        }
    });
}

function bindMobileMenu() {
    // menu button
    jQuery('#mobileheader > div > button.tomobilemenu').click(function () {
        if (jQuery('#mobileheader > div > button.tomobilemenu').hasClass('active')) {
            jQuery('#mobileheader > div > button.tomobilemenu').removeClass('active');
            jQuery('#mobilemenu').css('right', '100%');
        } else {
            jQuery('#mobileheader > div > button.tomobilemenu').addClass('active');
            jQuery('#mobilemenu').css('right', '0%');
        }
    });

    // link to my map button
    jQuery('#mobileheader > div > button.mymap').click(function () {
        window.location = '/ownlist';
    });

    // link to add location page
    jQuery('#mobileheader > div > button.suggestlocation').click(function () {
        window.location = '/suggestlocation';
    });
    // select another main location
    jQuery('#select_location_mobile').change(function () {
        jQuery.ajax({
            type: "POST",
            url: '/?q=ajax/picklocation',
            data: {nid: jQuery(this).val()}
        });
    });

    jQuery("#search_submit_mobile").on('click', function () {
        var url = '/?s=' + encodeURIComponent(jQuery('#search_term_mobile').val()) + '&m=1';
        console.log(url);
        window.location = url;
    });

}

function populateMap(searchresults, count) {

    window.markerMapping = new Array(); // let's store the leaflet id's with the nid's

    for (var i = 0; i < count; i++) {

        var thisResult = searchresults[i];

        if (typeof thisResult.la == 'string') {
            if (thisResult.c > 0) {
                // Not self, but i am a merged one
                var marker = L.marker([thisResult.la, thisResult.lo], {icon: window.redIcon}).setBouncingOptions({bounceHeight: 1, contractHeight: 3, bounceSpeed: 20, contractSpeed: 150}).addTo(window.map);
                marker.bindPopup(thisResult.h).on('popupopen', function () {
                    window.map.panTo(this._latlng);
                    jQuery('#selected_location_info > div').hide();
                });

                window.markers.addLayer(marker);
            }
            if (!thisResult.c) {
                // Not self, and not a merged one
                var marker = L.marker([thisResult.la, thisResult.lo], {icon: window.redIcon}).setBouncingOptions({bounceHeight: 1, contractHeight: 3, bounceSpeed: 20, contractSpeed: 150}).addTo(window.map);
                marker.bindPopup('<span class="hidden open_location_popup">' + thisResult.n + '</span>').on('popupopen', function () {
                    focusLocation();
                });

                window.markers.addLayer(marker);
            }

            window.markerMapping[thisResult.n] = marker._leaflet_id;
        }
    }
}
