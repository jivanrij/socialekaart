<h1><?php echo drupal_get_title(); ?></h1>
<p>
    Via deze pagina kan je dubbele locaties samenvoegen/verwijderen/goedkeuren.<br />
    Goedkeuren: Er zal een flag gezet worden dat ondanks de coordinaten dubbel zijn, dit een aparte zorgverlener is. Hij zal daarna niet meer hier weergegeven worden.<br />
    Verwijderen: De zorgverlener zal in-actief gezet worden.<br />
    Samenvoegen: De geselecteerde zorgverleners zullen samengevoegd worden.<br />
</p>
<?php foreach ($aDoubleLocations as $key => $aDoubleLocation): ?>
    <form id="double_key_<?php echo $key; ?>">
        <hr />
        <table class="double_locations_table">
            <?php foreach ($aDoubleLocation as $aOneDouble): ?>
            <?php $adres = Location::getAddressString(node_load($aOneDouble->nid)); ?>
            <tr>
                <td style="width:5%;">
                    <input type="checkbox" class="marked_locations location_mark_<?php echo $key; ?>" value="<?php echo $aOneDouble->nid; ?>">
                </td>
                <td style="width:20%;">
                    <?php echo $aOneDouble->x; ?> <?php echo $aOneDouble->y; ?>
                </td>
                <td style="width:10%;">
                    <a title="bekijk op sociale kaart" target="_new" href="/?loc=<?php echo $aOneDouble->nid; ?>"><?php echo $aOneDouble->nid; ?></a>
                </td>
                <td style="width:25%;">
                    <?php echo $aOneDouble->title; ?>
                </td>
                <td style="width:10%;">
                    <?php echo $aOneDouble->source; ?>
                </td>
                <td style="width:30%;">
                    <a href="https://www.google.nl/maps/search/<?php echo $adres; ?>" target="_new"><?php echo $adres; ?> op Google Maps</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="6">
                    <a href="#" class="select_all_locations">select all</a> | <a href="#" class="remove_selected_locations">verwijderen</a> | <a href="#" class="merge_selected_locations">samenvoegen</a> | <a href="#" class="mark_as_checked">goedkeuren</a>
                </td>
            </tr>
        </table>
    </form>
<?php endforeach; ?>
<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.11.3.min.js"></script>
<script>
    jQuery(".remove_selected_locations").on('click', function (e) {
        e.preventDefault();

            var button = jQuery(this);
            var key = jQuery(this).closest('form').attr('id').replace('double_key_', '');

            var ids = '';

            jQuery.each(jQuery(".marked_locations:checked", jQuery(button).closest('form')), function () {
                if (ids == '') {
                    ids = jQuery(this).val();
                } else {
                    ids = ids + '-' + jQuery(this).val();
                }
            });

            jQuery.ajax({
                url: "/?q=ajax/doublehandler_remove&ids=" + ids,
                type: 'POST',
                success: function (data) {
                    jQuery(button).css('color', 'red');
                    jQuery('td',jQuery(".marked_locations:checked", jQuery(button).closest('table')).closest('tr')).css('color', 'red');
                    jQuery(".marked_locations:checked", jQuery(button).closest('table')).remove();
                }
            });
        
    });
    jQuery(".merge_selected_locations").on('click', function (e) {
        e.preventDefault();



        var button = jQuery(this);
        var key = jQuery(this).closest('form').attr('id').replace('double_key_', '');

        var ids = '';
        var counter = 0;

        jQuery.each(jQuery(".marked_locations:checked", jQuery(button).closest('form')), function () {
            if (ids == '') {
                ids = jQuery(this).val();
            } else {
                ids = ids + '-' + jQuery(this).val();
            }
            counter = counter + 1;
        });


        if (counter > 1) {

                jQuery.ajax({
                    url: "/?q=ajax/doublehandler_merge&ids=" + ids,
                    type: 'POST',
                    success: function (data) {
                        jQuery(button).css('color', 'orange');                
                        jQuery('td',jQuery(".marked_locations:checked", jQuery(button).closest('table')).closest('tr')).css('color', 'orange');
                        jQuery(".marked_locations:checked", jQuery(button).closest('table')).remove();
                    }
                });

            
        } else {
            alert('Only one selected.');
        }
    });
    jQuery(".mark_as_checked").on('click', function (e) {
        e.preventDefault();
        var button = jQuery(this);
        var key = jQuery(this).closest('form').attr('id').replace('double_key_', '');

        var ids = '';

        jQuery.each(jQuery(".marked_locations:checked", jQuery(button).closest('form')), function () {
            if (ids == '') {
                ids = jQuery(this).val();
            } else {
                ids = ids + '-' + jQuery(this).val();
            }
        });

        jQuery.ajax({
            url: "/?q=ajax/doublehandler_checked&ids=" + ids,
            type: 'POST',
            success: function (data) {
                jQuery(button).css('color', 'blue');
                
                jQuery('td',jQuery(".marked_locations:checked", jQuery(button).closest('table')).closest('tr')).css('color', 'blue');
                jQuery(".marked_locations:checked", jQuery(button).closest('table')).remove();
            }
        });
    });
    jQuery(".select_all_locations").on('click', function (e) {
        e.preventDefault();
        var key = jQuery(this).closest('form').attr('id').replace('double_key_', '');

        jQuery(".location_mark_"+key).prop('checked', true);
    });
</script>
<style>
    .double_locations_table{
        margin:0;
        padding:0;        
    }
    .double_locations_table tr{
        margin:0;
        padding:0;
    }
    .double_locations_table tr td{
        margin:0;
        padding:5px;        
    }
</style>