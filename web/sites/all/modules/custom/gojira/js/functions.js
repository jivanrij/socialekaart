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

        if (Drupal.settings.gojira.page == 'locationsset') {
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
    var width = '600px';
    if (onMobileView()) {
        var width = '80%';
    }

    jQuery.colorbox({
        href: '/?q=ajax/showtutorial',
        closeButton: false,
        escKey: false,
        overlayClose: false,
        width: width,
        opacity: 0.5,
        onComplete: function () {
            bindTutorialButtons();
        }
    });
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
            url: '/?q=ajax/removelabel&nid=' + nid + '&tid=' + tid,
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

            jQuery.ajax({
                url: '/?q=ajax/unlikelabel&nid=' + nid + '&tid=' + tid,
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

            jQuery.ajax({
                url: '/?q=ajax/likelabel&nid=' + nid + '&tid=' + tid,
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
    jQuery(".in_favorites").click(function (e) {
        e.preventDefault();
        var button = this;
        var nid = jQuery(this).closest('div.search_result_wrapper').attr('id').replace('location_', '');

        if (jQuery(this).hasClass('true')) {
            // turn it off
            jQuery.ajax({
                url: '/?q=ajax/setfavorite&turn=off&nid=' + nid,
                type: 'POST',
                success: function (data) {
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    somethingWrongMessage();
                }
            });
            jQuery(button).removeClass('true');
            jQuery(button).addClass('false');
        } else {
            // turn it on
            jQuery.ajax({
                url: '/?q=ajax/setfavorite&turn=on&nid=' + nid,
                type: 'POST',
                success: function (data) {
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    somethingWrongMessage();
                }
            });
            jQuery(button).removeClass('false');
            jQuery(button).addClass('true');
        }
    });
}

function addNewLabel(element) {
    var nid = jQuery(element).closest('div.search_result_wrapper').attr('id').replace('location_', '');
    var label = jQuery('#new_label_' + nid).val();

    if (label == 'label toevoegen') {
        return;
    }

    jQuery.ajax({
        url: '/?q=ajax/savenewlabel&nid=' + nid + '&label=' + label,
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

    window.blackIcon = L.icon({
        iconUrl: '/sites/all/modules/custom/gojira/js/images/gojira_marker_self.png',
        shadowUrl: '/sites/all/modules/custom/gojira/js/images/markers-shadow.png',
        iconSize: [22, 30], // size of the icon
        shadowSize: [24, 15], // size of the shadow
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

    window.map.attributionControl.setPrefix("<a href='/algemene-voorwaarden'>Algemene Voorwaarden</a> <a href='/user/logout'>Uitloggen</a>");

    window.markers = new L.FeatureGroup();
    window.map.addLayer(window.markers);

    // let's add self to the map, but not to the featuregroup, self will always be displayed
    if (Drupal.settings.gojira.show_self) {
        var marker = L.marker([Drupal.settings.gojira.latitude, Drupal.settings.gojira.longitude], {icon: window.blackIcon})
                .setBouncingOptions({bounceHeight: 1, contractHeight: 3, bounceSpeed: 20, contractSpeed: 150})
                .on('click', function () {
                    this.toggleBouncing();
                }).addTo(window.map);

        window.markers.addLayer(marker);
    }
    jQuery(window).trigger('resize');
}

// trigger the ajax search call & handle it and then binds new func. by calling the bind function.
// Also set's the map up with the setupMapDefault function
function bindGojirasearch() {
    jQuery("#search_form form").submit(function (e) {
        e.preventDefault();
        doSearchCall(encodeURIComponent(jQuery('#gojirasearch_search_term').val()), 0);
    });
}

function doSearchCall(searchFor, search_own_area, extra_ajax_info) {

    if (typeof extra_ajax_info == 'undefined') {
        var extra_ajax_info = "";
    }

    openOverlay();

    if (searchFor == 'ownlist') {
        jQuery('#search_result_info').hide();
    } else {
        jQuery('#crud_holder').hide();
    }
    if (searchFor !== 'locationsset') {
        jQuery('#locationset_wrapper').hide();
        jQuery('#search_result_info').hide();
    }

    // force a search in the area of the user
    if (search_own_area) {
        searchFor = searchFor + '&check_city=0';
    }

    if (searchFor != 'favorites') {
        jQuery('ul.menu li a.active').removeClass('active');
    }

    window.map.removeLayer(window.markers);
    window.markers = new L.FeatureGroup();
    window.map.addLayer(window.markers);

    jQuery.ajax({
        url: '/?q=ajax/search&tags=' + searchFor + extra_ajax_info,
        type: 'GET',
        dataType: 'json',
        success: function (data) {

            window.markerMapping = new Array(); // let's store the leaflet id's with the nid's

            if (typeof data.mapSearchResultsCount == 'undefined') {
                somethingWrongMessage();
            }

            if (searchFor != 'ownlist') {
                jQuery('#ajax_search_results').html(data.results_html);
            }

            for (var i = 0; i < data.mapSearchResultsCount; i++) {

                var thisResult = data.mapSearchResults[i];

                if (thisResult.x && thisResult.c > 0) {
                    // I am a merged marker with self as a part of my items
                    var marker = L.marker([thisResult.la, thisResult.lo], {icon: window.mixedIcon}).setBouncingOptions({bounceHeight: 1, contractHeight: 3, bounceSpeed: 20, contractSpeed: 150}).addTo(window.map);
                    marker.bindPopup(thisResult.h).on('popupopen', function () {
                        window.map.panTo(this._latlng);
                    });
                    
                    window.markers.addLayer(marker);
                }
                if (thisResult.x && !thisResult.c) {
                    // I am just self, and not merged
                    var marker = L.marker([thisResult.la, thisResult.lo], {icon: window.blackIcon}).setBouncingOptions({bounceHeight: 1, contractHeight: 3, bounceSpeed: 20, contractSpeed: 150}).addTo(window.map);
                    marker.bindPopup('<span class="self_popup_link">' + data.your_location + '</span>').on('popupopen', function () {
                        jQuery('#selected_location_info > div').hide();
                    });
                    
                    window.markers.addLayer(marker);
                }
                if (!thisResult.x && thisResult.c > 0) {
                    // Not self, but i am a merged one
                    var marker = L.marker([thisResult.la, thisResult.lo], {icon: window.redIcon}).setBouncingOptions({bounceHeight: 1, contractHeight: 3, bounceSpeed: 20, contractSpeed: 150}).addTo(window.map);
                    marker.bindPopup(thisResult.h).on('popupopen', function () {
                        window.map.panTo(this._latlng);
                        jQuery('#selected_location_info > div').hide();
                    });
                    
                    window.markers.addLayer(marker);
                }
                if (!thisResult.x && !thisResult.c) {
                    // Not self, and not a merged one
                    var marker = L.marker([thisResult.la, thisResult.lo], {icon: window.redIcon}).setBouncingOptions({bounceHeight: 1, contractHeight: 3, bounceSpeed: 20, contractSpeed: 150}).addTo(window.map);
                    marker.bindPopup('<span class="hidden open_location_popup">' + thisResult.n + '</span>').on('popupopen', function () {
                        focusLocation();
                    });
                    
                    window.markers.addLayer(marker);
                }
                window.markerMapping[thisResult.n] = marker._leaflet_id;
            }

//var featureLayer = L.mapbox.featureLayer().addTo(window.map);
//featureLayer.on('ready', function() {
//    window.map.fitBounds(featureLayer.getBounds());
//});
            if (data.boxInfo === null) {
                window.map.setView([data.latitude, data.longitude], data.zoom);
            } else {
                window.map.fitBounds([
                    [data.boxInfo.latLow, data.boxInfo.lonLow],
                    [data.boxInfo.latHigh, data.boxInfo.lonHigh]
                ]);
            }

            if (searchFor !== 'locationsset') {
                bindAfterSearch();
            }

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
function bindAfterSearch(bind_list, bind_details) {

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

        jQuery("a.close_button").click(function () {
            jQuery("#selected_location_info").html('');
        });

    }

    jQuery("input.new_label").click(function () {
        if (jQuery(this).val() == 'label toevoegen') {
            jQuery(this).val('');
            jQuery(this).css('color', '#b7072a');
//            jQuery(this).css('font-weight','bold');
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

    if (Drupal.settings.gojira.page != 'showlocation' && Drupal.settings.gojira.page != 'locationsset') {
        //do some paging in the list, not if we are in the showlocation/locationsset
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
                jQuery('#locationsset_locations li').removeClass('active');
                jQuery('#locationsset_locations li a[href=#' + nid + ']').closest('li').addClass('active');
            }


            // move to it
            if (!onMobileView()) {
                window.map.panTo([data.latitude, data.longitude]);
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




function bindEmployeelist() {
    jQuery("a.delete_employee").click(function (e) {
        e.preventDefault();
        if (confirm(Drupal.settings.gojira.delete_warning))
        {
            window.location = jQuery(this).attr('href');
            return;
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
            $.getJSON('/?q=ajax/locationtags', {
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

    jQuery('a.global_search_header').click(function (e) {

        e.preventDefault();
        if (!jQuery(this).hasClass('on')) {
            jQuery('a.global_search_header').addClass('on');
            jQuery('a.global_search_header').removeClass('off');
            jQuery.ajax({
                url: '/?q=ajax/switchglobalsearch&turn=on',
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    //window.map.setView([data.latitude, data.longitude], data.zoom);
                    jQuery("#gojirasearch_search_term").attr('placeholder', 'Zoek landelijk');
                    if (jQuery('#gojirasearch_search_term').val() != '') {
                        doSearchCall(encodeURIComponent(jQuery('#gojirasearch_search_term').val()), 0);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    somethingWrongMessage();
                }
            });
        } else {
            jQuery('a.global_search_header').addClass('off');
            jQuery('a.global_search_header').removeClass('on');
            jQuery.ajax({
                url: '/?q=ajax/switchglobalsearch&turn=off',
                type: 'POST',
                dataType: 'json',
                success: function (data) {
//                    window.map.setView([data.latitude, data.longitude], data.zoom);
                    jQuery("#gojirasearch_search_term").attr('placeholder', 'Zoek in de regio');
                    if (jQuery('#gojirasearch_search_term').val() != '') {
                        doSearchCall(encodeURIComponent(jQuery('#gojirasearch_search_term').val()), 0);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    somethingWrongMessage();
                }
            });

        }
    });

//    jQuery('a.favorite_header').click(function (e) {
//        e.preventDefault();
//        if (!jQuery(this).hasClass('on')) {
//            jQuery('a.favorite_header').addClass('on');
//            jQuery('a.favorite_header').removeClass('off');
//            jQuery.ajax({
//                url: '/?q=ajax/switchfavorites&turn=on',
//                type: 'POST',
//                success: function (data) {
//                    if (jQuery('#gojirasearch_search_term').val() != '') {
//                        doSearchCall(encodeURIComponent(jQuery('#gojirasearch_search_term').val()), 0);
//                    }
//                },
//                error: function (jqXHR, textStatus, errorThrown) {
//                    somethingWrongMessage();
//                }
//            });
//        } else {
//            jQuery('a.favorite_header').addClass('off');
//            jQuery('a.favorite_header').removeClass('on');
//            jQuery.ajax({
//                url: '/?q=ajax/switchfavorites&turn=off',
//                type: 'POST',
//                success: function (data) {
//                    if (jQuery('#gojirasearch_search_term').val() != '') {
//                        doSearchCall(encodeURIComponent(jQuery('#gojirasearch_search_term').val()), 0);
//                    }
//                },
//                error: function (jqXHR, textStatus, errorThrown) {
//                    somethingWrongMessage();
//                }
//            });
//        }
//    });

    jQuery('#edit-submit').click(function (e) {
        e.preventDefault();
        openOverlay();
        jQuery(this).closest('form').submit();
    });

    bindGojirasearch();

    jQuery('#location_selector').change(function () {
        openOverlay();
        jQuery.ajax({
            type: "POST",
            url: '/?q=ajax/picklocation',
            data: {nid: jQuery(this).val()}
        }).done(function (msg) {
            if (Drupal.settings.gojira.page == 'ownlist' || jQuery("#gojirasearch_search_term").val().trim().length <= 0) {
                location.reload();
            } else {
                jQuery("#search_form form").trigger('submit');
            }
        });
    });

}

function bindSuggestlocation() {
    jQuery('a.double_locs').click(function () {
        var nid = jQuery(this).attr('id').replace('double_loc_', '');
        jQuery.ajax({
            url: '/?q=ajax/locationinfo&nid=' + nid,
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


//function bindOwnlist() {
//    // select different category to filter on
//    jQuery("a.ownlist_cat").click(function (e) {
//        e.preventDefault();
//
//        jQuery("a.ownlist_cat").removeClass('active');
//        jQuery(this).addClass('active');
//
//        if (jQuery(this).hasClass('show_all')) {
//            jQuery("#ownlist_content ul li").css('display', 'block');
//        } else {
//            jQuery("ul li", "#ownlist_content").css('display', 'none');
//            var cat_id = jQuery(this).attr('rel');
//            jQuery("li.ownlist_cat_" + cat_id, "#ownlist_content").css('display', 'block');
//        }
//
//        jQuery('a.location_category_list').removeClass('active');
//        jQuery("#ajax_search_results").html('');
//
//        jQuery(window).trigger('resize');
//    });
//
//    // show a location
//
//
//    doSearchCall('ownlist', 0);
//}

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
        url: '/?q=ajax/checklocation',
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
        window.location = '/mijn-kaart';
    });

    // link to add location page
    jQuery('#mobileheader > div > button.suggestlocation').click(function () {
        window.location = '/suggestlocation';
    });


    //  switch global search on or off for mobile menu
    jQuery('a.search_global').click(function () {
        if (jQuery(this).hasClass('on')) {
            jQuery(this).addClass('off');
            jQuery(this).removeClass('on');
            jQuery.ajax({
                url: '/?q=ajax/switchglobalsearch&turn=off',
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                }
            });
        } else {
            jQuery(this).addClass('on');
            jQuery(this).removeClass('off');
            jQuery.ajax({
                url: '/?q=ajax/switchglobalsearch&turn=on',
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                }
            });
        }
    });

    //  switch favorites search on or off for mobile menu
    jQuery('a.search_favorite').click(function () {
        if (jQuery(this).hasClass('on')) {
            jQuery(this).addClass('off');
            jQuery(this).removeClass('on');
            jQuery.ajax({
                url: '/?q=ajax/switchfavorites&turn=off',
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                }
            });
        } else {
            jQuery(this).addClass('on');
            jQuery(this).removeClass('off');
            jQuery.ajax({
                url: '/?q=ajax/switchfavorites&turn=on',
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                }
            });
        }
    });

    // select another main location
    jQuery('#select_location_mobile').change(function () {
        jQuery.ajax({
            type: "POST",
            url: '/?q=ajax/picklocation',
            data: {nid: jQuery(this).val()}
        });
    });
}

function bindLocationsset() {
    jQuery(".locationset_show_cat").click(function (e) {
        e.preventDefault();
        jQuery("#ajax_search_results").html("");
        jQuery("#locationsset_categories li").removeClass("active");
        jQuery(this).closest("li").addClass("active");
        var cat_id = jQuery(this).closest("li").attr("rel");

        doSearchCall("locationsset", 0, "&id=" + Drupal.settings.gojira.locationsset_id + '&cat_id=' + cat_id);

        if (cat_id == "all") {
            jQuery(".locationset_show_loc").closest("li").show();
        } else {
            jQuery(".locationset_show_loc").closest("li").hide();
            jQuery("li[rel=" + cat_id + "]").show();
        }
        jQuery(window).trigger('resize');
    });

    jQuery("#locationsset_categories li:first-child a").trigger('click');

    jQuery('a.locationset_show_loc').click(function (e) {
        e.preventDefault();
        var location_id = jQuery(this).attr('href').replace('#', '');
        var button = this;

        openOverlay();

        jQuery.ajax({
            url: '/?q=ajax/singlesearchresult&wrap_it=1&nid=' + location_id,
            type: 'POST',
            dataType: 'json',
            success: function (data) {

                jQuery("#ajax_search_results").html(data.html);

                correctHeightForLocationsetSearchResult();

                jQuery('#locationsset_locations li').removeClass('active');
                jQuery(button).closest("li").addClass('active');

                //console.log(window.markers);

                // move to it
                //window.map.setView([data.latitude, (data.longitude - 0.004)], data.zoom);
                window.map.panTo([data.latitude, data.longitude]);

                //bindAfterSearch(false, true);

                jQuery("#search_result_info").css('top', top + 'px');

                jQuery(window).trigger('resize');

                L.Marker.stopAllBouncingMarkers();
                if ((window.markerMapping[location_id] !== undefined) && (window.markers._layers[window.markerMapping[location_id]] !== undefined)) {
                    window.markers._layers[window.markerMapping[location_id]].toggleBouncing();
                }

                closeOverlay();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                somethingWrongMessage();
            }
        });
    });

}

/**
 * Corrects the height of the searchresult of the locationset
 */
function correctHeightForLocationsetSearchResult() {
    var top = 90 + parseInt(jQuery('#locationset_wrapper').css('height').replace('px', ''));
    jQuery("#search_result_info").css("top", top + "px");
}