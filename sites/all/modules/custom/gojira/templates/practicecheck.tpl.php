<form method="POST" action="/practicecheck">
    <input type="input" name="terms" value="<?php echo $sTerms; ?>" />
    <input type="submit" value="<?php echo t('Search'); ?>" />
</form>

<p>
    Controleer hier of uw zorginstelling of praktijk ook in ons systeem aanwezig is. Huisartsen kunnen op deze manier beter naar u verwijzen. Als u zichzelf niet kunt vinden kunt u op <a href="/?q=practicecheck">dit</a> formulier uw instelling toevoegen.
</p>

<?php if (count($aResults)): ?>
    <div id="accordion">
        <?php foreach ($aResults as $sCity => $aLocationsPerCity): ?>
            <h2><?php echo $aLocationsPerCity['sCity']; ?></h2>
            <div>
                <?php foreach ($aLocationsPerCity['aLocations'] as $aLocation): ?>
                    <p>
                        <?php echo $aLocation->title; ?> - <?php echo helper::value($aLocation, GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD); ?> <?php echo helper::value($aLocation, GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD); ?>
                    </p>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
    jQuery(function () {
        jQuery("#accordion").accordion();
    });
</script>
<style>
#accordion{
    width:900px;
    margin-left: 25px;
}
#accordion div.ui-accordion-content p{
    margin:0 !important;
    padding:0 !important;
}
#accordion div.ui-accordion-content{
    height: auto !important;
}
    </style>