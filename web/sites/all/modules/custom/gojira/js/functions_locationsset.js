/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function focusLocationsset(nid) {
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

function bindLocationsset() {

    bindLocationsetSearch();

    jQuery(".locationset_show_cat").click(function (e) {
        e.preventDefault();
        jQuery("#ajax_search_results").html("");
        jQuery("#locationsset_categories li").removeClass("active");
        jQuery(this).closest("li").addClass("active");
        var cat_id = jQuery(this).closest("li").attr("rel");

        if (Drupal.settings.gojira.locationsset_has_filter == 0) {
            getCategoryLocations(Drupal.settings.gojira.locationsset_id, cat_id);
        }

        if (cat_id == "all") {
            jQuery(".locationset_show_loc").closest("li").show();
        } else {
            jQuery(".locationset_show_loc").closest("li").hide();
            jQuery("li[rel=" + cat_id + "]").show();
        }
        jQuery(window).trigger('resize');
    });

    jQuery('a.locationset_show_loc').click(function (e) {
        e.preventDefault();

        L.Marker.stopAllBouncingMarkers();

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

                if (typeof data.latitude == 'string') {
                    window.map.panTo([data.latitude, data.longitude]);

                    if ((window.markerMapping[location_id] !== undefined) && (window.markers._layers[window.markerMapping[location_id]] !== undefined)) {
                        window.markers._layers[window.markerMapping[location_id]].toggleBouncing();
                    }
                }

                bindAfterSearch(false, true);

                jQuery("#search_result_info").css('top', top + 'px');

                jQuery(window).trigger('resize');

                closeOverlay();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                somethingWrongMessage();
            }
        });
    });

    if (Drupal.settings.gojira.locationsset_has_filter == 0) {
        jQuery("#locationsset_categories li:first-child a").trigger('click');
    } else {
        populateMap(Drupal.settings.gojira.locationsset_filter_results, Drupal.settings.gojira.locationsset_filter_results_count);
    }

    bindCloseButtons();

}

/**
 * Corrects the height of the searchresult of the locationset
 */
function correctHeightForLocationsetSearchResult() {
    var top = 90 + parseInt(jQuery('#locationset_wrapper').css('height').replace('px', ''));
    jQuery("#search_result_info").css("top", top + "px");
}

function getCategoryLocations(locationsset_id, cat_id) {
    openOverlay();

    jQuery('#crud_holder').hide();
//    jQuery('ul.menu li a.active').removeClass('active');

    window.map.removeLayer(window.markers);
    window.markers = new L.FeatureGroup();
    window.map.addLayer(window.markers);


    jQuery.ajax({
        url: '/?q=ajax/search&s=locationsset&id=' + locationsset_id + '&cat_id=' + cat_id,
        type: 'GET',
        dataType: 'json',
        success: function (data) {


            window.markerMapping = new Array(); // let's store the leaflet id's with the nid's

            if (typeof data.mapSearchResultsCount == 'undefined') {
                somethingWrongMessage();
            }

//            if (searchFor != 'ownlist') {
//                jQuery('#ajax_search_results').html(data.results_html);
//            }

            if (data.mapSearchResultsCount == 1) {
                // no results, only our own practice
                closeOverlay();
                return;
            }

            populateMap(data.mapSearchResults, data.mapSearchResultsCount);

            if (data.boxInfo === null || typeof data.boxInfo == 'undefined') {
                window.map.setView([data.latitude, data.longitude], data.zoom);
            } else {
                window.map.fitBounds([
                    [data.boxInfo.latLow, data.boxInfo.lonLow],
                    [data.boxInfo.latHigh, data.boxInfo.lonHigh]
                ]);
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
function bindLocationsetSearch() {
    jQuery("#search_form form").submit(function (e) {
        e.preventDefault();
        doLocationsetSearchCall();
    });
}

function doLocationsetSearchCall() {
    openOverlay();

    var s = encodeURIComponent(jQuery('#gojirasearch_search_term').val());

    var url = '';

    if (jQuery("#search_type_select").val() == 'locationset') {
        url = '/?q=HW' + '&filter=' + s; // TODO REMOVE
        //url = window.location.pathname + '?filter=' + s;
    }
    if (jQuery("#search_type_select").val() == 'ownlist') {
        url = '/?q=ownlist' + '&filter=' + s; // TODO REMOVE
        //url = window.location.pathname + '?filter=' + s;
    }
    if (jQuery("#search_type_select").val() == 'country') {
        url = '/?s=' + s + '&type=country';
    }
    if (jQuery("#search_type_select").val() == 'region') {
        url = '/?s=' + s + '&type=region';
    }

    window.location = url
}
