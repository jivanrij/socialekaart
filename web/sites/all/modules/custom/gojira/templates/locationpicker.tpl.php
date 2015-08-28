<p>
  <?php echo t('Selecteer hier een locatie van waaruit u nu wilt zoeken.'); ?>
</p>
<ul>
  <?php foreach($locations as $location): ?>
  <li><a href="/" class="location_selector" rel="<?php echo $location->nid; ?>" title="<?php echo $location->title; ?>"><?php echo $location->title; ?></a></li>
  <?php endforeach; ?>
</ul>