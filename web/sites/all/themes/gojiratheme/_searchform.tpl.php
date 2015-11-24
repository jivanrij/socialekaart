<?php if (helper::agreedToConditions() && (count(Location::getUsersLocations(true)) > 0)): ?>
    <div id="search_form" class="rounded">
        <form>
            <input type="text" id="gojirasearch_search_term" placeholder="<?php echo (helper::globalSearchCheck() ? 'Zoek landelijk' : 'Zoek in de regio'); ?>" value="" />
            <input type="submit" value="" />
        </form>
    </div>
<?php endif; ?>