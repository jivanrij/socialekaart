<?php if($bDoubleLocationsWarning): ?>
<p>
<ul>
    <?php foreach($aPossibleDoubles as $iNid=>$oPossibleDouble): ?>
    <li><a class="double_locs" id="double_loc_<?php echo $iNid; ?>"><?php echo $oPossibleDouble.$iNid; ?></a></li>
    <?php endforeach; ?>
</ul>
</p>
<div id="shot_double_info"></div>
<?php endif; ?>
<h1><?php echo drupal_get_title(); ?></h1>
<?php echo render($fForm); ?>
