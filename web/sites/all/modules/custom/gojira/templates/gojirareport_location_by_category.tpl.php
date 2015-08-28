<h1><?php echo drupal_get_title(); ?></h1>
<p><?php echo t('Get a list of all locations based on the category.'); ?></p>
<form>
  <input name="q" type="hidden" value="admin/reports/gojirareport_location_by_category" />
  <select name="category">
  <?php foreach($categories as $location): ?>
      <?php
      $selected = '';
      if(isset($_GET['category']) && $_GET['category'] == $location->nid) $selected = 'selected';
      ?>
      <option <?php echo $selected; ?> value="<?php echo $location->nid; ?>"><?php echo $location->title; ?></option>
      
  <?php endforeach; ?>
  </select>
  <input style="border-color:black; border-width: 1px; border-style: solid; cursor:pointer;" type="submit" />
</form>

<?php if(count($locations)>0): ?>
<p><?php echo t('Zoekresultaten:'); ?></p>
<ul>
  <?php foreach($locations as $location): ?>
    <li>
      <?php echo $location->title; ?>
      &nbsp;
      <a href="/node/<?php echo $location->nid; ?>/edit&destination=admin/reports/gojira" title="backend link">backend</a>&nbsp;
      <a href="/?loc=<?php echo $location->nid; ?>" title="frontend link">frontend</a>
    </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>