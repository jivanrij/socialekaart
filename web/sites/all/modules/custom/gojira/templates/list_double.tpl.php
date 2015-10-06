<h1>a<?php echo drupal_get_title(); ?>b</h1>

<?php foreach($aDoubleLocations as $aDoubleLocation): ?>
    <hr />
    <ul>
        <?php foreach($aDoubleLocation as $aOneDouble): ?>
            <li><?php echo $aOneDouble->title; ?></li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>
