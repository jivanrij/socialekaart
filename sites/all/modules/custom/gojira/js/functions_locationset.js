/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function focusLocationset(nid) {
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
        url: '/?q=ajax/singlesearchresult&nid=' + nid + '&mid=' + Drupal.settings.gojira.selected_map,
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

function bindLocationset() {

    bindLocationsetSearch();

    jQuery(".locationset_show_cat").click(function (e) {
        e.preventDefault();
        jQuery("#ajax_search_results").html("");
        jQuery("#locationset_categories li").removeClass("active");
        jQuery(this).closest("li").addClass("active");
        var cat_id = jQuery(this).closest("li").attr("rel");

        if (Drupal.settings.gojira.locationset_has_filter == 0) {
            getCategoryLocations(Drupal.settings.gojira.locationset_id, cat_id);
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
            url: '/?q=ajax/singlesearchresult&wrap_it=1&nid=' + location_id + '&mid=' + Drupal.settings.gojira.selected_map,
            type: 'POST',
            dataType: 'json',
            success: function (data) {

                jQuery("#ajax_search_results").html(data.html);

                correctHeightForLocationsetSearchResult();

                jQuery('#locationset_locations li').removeClass('active');
                jQuery(button).closest("li").addClass('active');

                if (typeof data.latitude == 'string') {
                    window.map.panTo([data.latitude, data.longitude]);

                    if ((window.markerMapping[location_id] !== undefined) && (window.markers._layers[window.markerMapping[location_id]] !== undefined)) {
                        window.markers._layers[window.markerMapping[location_id]].toggleBouncing();
                    }
                }

                bindAfterSearch(false, true);

                //jQuery("#search_result_info").css('top', top + 'px');

                jQuery(window).trigger('resize');

                closeOverlay();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                somethingWrongMessage();
            }
        });
    });

    if (Drupal.settings.gojira.locationset_has_filter == 0) {
        jQuery("#locationset_categories li:first-child a").trigger('click');
    } else {
        populateMap(Drupal.settings.gojira.locationset_filter_results, Drupal.settings.gojira.locationset_filter_results_count);
    }

    bindCloseButtons();

}

/**
 * Corrects the height of the searchresult of the locationset
 */
function correctHeightForLocationsetSearchResult() {
    var top = 54 + parseInt(jQuery('#locationset_wrapper').css('height').replace('px', ''));
    jQuery("#search_result_info").css("top", top + "px");
}

function getCategoryLocations(locationset_id, cat_id) {
    openOverlay();

    jQuery('#crud_holder').hide();
//    jQuery('ul.menu li a.active').removeClass('active');

    window.map.removeLayer(window.markers);
    window.markers = new L.FeatureGroup();
    window.map.addLayer(window.markers);


    jQuery.ajax({
        url: '/?q=ajax/search&s=locationset&id=' + locationset_id + '&cat_id=' + cat_id + '&mid=' + Drupal.settings.gojira.selected_map,
        type: 'GET',
        dataType: 'json',
        success: function (data) {


            window.markerMapping = new Array(); // let's store the leaflet id's with the nid's

            if (typeof data.mapSearchResultsCount == 'undefined') {
                somethingWrongMessage();
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
    jQuery("form#search_ownmap_form").on('submit', function(e){
        e.preventDefault();

        var s = encodeURIComponent(jQuery('#search_ownmap').val());
        url = window.location.pathname + '?filter=' + s;

        window.location = url;
    });
}
