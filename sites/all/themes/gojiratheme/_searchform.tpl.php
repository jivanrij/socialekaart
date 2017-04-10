<?php
if(count(Location::getUsersLocations(true)) == 0) {
    $userModel = \Models\User::loadCurrent();
    $userModel->assureLocation();
}
?>
<?php if (count(Location::getUsersLocations(true)) !== 0 && user_access(helper::PERM_BASIC_ACCESS)): ?>
    <div id="search_form" class="rounded">
        <form>
            <input type="text" id="gojirasearch_search_term" placeholder="<?php echo (helper::globalSearchCheck() ? 'Zoek landelijk' : 'Zoek in de regio'); ?>" value="" />
            <!--<input type="submit" class="fa fa-search" value="" />-->
            <button class="fa fa-search"></button>
        </form>
    </div>
<?php endif; ?>