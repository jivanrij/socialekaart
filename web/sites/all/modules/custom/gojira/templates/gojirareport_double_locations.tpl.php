<h1><?php echo drupal_get_title(); ?></h1>
<ul>
    <?php foreach($no_coordinates as $nid): ?>
      <li>
        <?php $node = node_load($nid); ?>
        <?php
        $status = '';
        switch($node->field_moderated_status[LANGUAGE_NONE][0]['value']){
          case 1:
            $status = 'moderated';
            break;
          case 2:
            $status = 'not moderated, no invite send';
            break;
          case 3:
            $status = 'not moderated, invite send';
            break;
          case 4:
            $status = 'moderated, after invite';
            break;
        }
        ?>
        <?php echo $node->title; ?> (<?php echo $status; ?>)
        &nbsp;
        <a href="/node/<?php echo $node->nid; ?>/edit&destination=admin/reports/gojira" title="backend link">backend</a>
        &nbsp;
        <a href="/?loc=<?php echo $node->nid; ?>" title="frontend link">frontend</a><br />
      </li>
    <?php endforeach; ?>
</ul>
<h2><?php echo t('Double locations'); ?></h2>
<p>
    Found <?php echo count($locations); ?> situations of locations that have the same coordinates.
</p>
<ul>
  <?php foreach($locations as $doubles): ?>
    <li>
      <?php foreach($doubles as $location): ?>
        <?php echo $location->title; ?>
        &nbsp;
        <a href="/node/<?php echo $location->nid; ?>/edit&destination=admin/reports/gojira" title="backend link">backend</a>
        &nbsp;
        <a href="/showlocation&loc=<?php echo $location->nid; ?>" title="frontend link">weergeven</a><br />
      <?php endforeach; ?>
    </li>
  <?php endforeach; ?>
</ul>
