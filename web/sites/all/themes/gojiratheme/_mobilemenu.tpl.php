<div id="mobileheader">
    <div>
        <form action="/" method="GET" id="form-mobile-search">
            <input type="text" placeholder="<?php echo t('Search'); ?>" name="tags" />
            <i class="fa fa-search" onClick="jQuery(this).closest('form').submit();"></i>
        </form>
        <button class="fa fa-bars" />
    </div>
</div>
<div id="mobilemenu">
    <div>
        <?php print render($page['mobile_menu']); ?>
    </div>
</div>