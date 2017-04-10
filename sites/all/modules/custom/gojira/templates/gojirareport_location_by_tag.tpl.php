<h1><?php echo drupal_get_title(); ?></h1>
<p><?php echo t('Get a list of all location with the given public tag.'); ?></p>
<form>
  <input name="q" type="hidden" value="admin/reports/gojirareport_location_by_tag" />
  <input style="border-color:black; border-width: 1px; border-style: solid;" name="tag" type="input" />
  <input style="border-color:black; border-width: 1px; border-style: solid; cursor:pointer;" type="submit" value="Backend" />
</form>
<?php if(count($locations)>0){ ?>
<p><?php echo t('Zoekresultaten:'); ?></p>
<ul>
  <?php foreach($locations as $location): ?>
    <li>
      <?php echo $location->title; ?>
      &nbsp;
      <a href="/node/<?php echo $location->nid; ?>/edit&destination=admin/reports/gojira" title="backend link">backend</a>
    </li>
  <?php endforeach; ?>
</ul>
<?php } ?>
<p><?php echo t('Beschikbare labels:'); ?></p>
<ul>
  <?php foreach($labels as $label): ?>
    <li>
      <a target="_new" href="/?s=allwithtag:<?php echo $label->name; ?>" /><?php echo $label->name; ?></a>
    </li>
  <?php endforeach; ?>
</ul>