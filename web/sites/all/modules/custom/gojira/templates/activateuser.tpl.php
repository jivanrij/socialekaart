Klik op een van de onderstaande gebruikers om hem te activeren. De gebruiker zal dan hierover een e-mail ontvangen.<br />
Als de gebruiker geimporteerd is van HAweb zal hij ook nu zijn eerste gratis periode krijgen.<br />
<?php foreach($aUsers as $oUser): ?>
    <?php echo $oUser->uid; ?> - <?php echo $oUser->name; ?> - <a href="/?q=user/<?php echo $oUser->uid; ?>/edit&destination=admin/config/system/gojiraactivateuser">backend</a> - <a class="activate_user" href="/?q=admin/config/system/gojiraactivateuser&uid=<?php echo $oUser->uid; ?>">activeren</a><br />
<?php endforeach; ?>
<script>
    jQuery("a.activate_user").click(function(e){
        e.preventDefault();
        if (confirm('Are you sure you want to activate this user?')) {
            window.location = jQuery(this).attr('href');
        }
    });
</script>