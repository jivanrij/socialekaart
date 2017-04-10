<h1><?php echo drupal_get_title(); ?></h1>
<p><?php echo t('This are un-moderated locations that are not published. Probably imported or suggested by users.'); ?></p>
<ul>
  <?php foreach($suggested_inactive_locations as $location): ?>
    <?php
    $status = '';
    switch($location->field_moderated_status[LANGUAGE_NONE][0]['value']){
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
    <li>
      <?php echo $location->title; ?> (<?php echo $status; ?>)
      &nbsp;
      <a href="/node/<?php echo $location->nid; ?>/edit&destination=admin/reports/gojira" title="backend link">backend</a>
    </li>
  <?php endforeach; ?>
</ul>

<h2><?php echo t('Suggested active locations'); ?></h2>
<p><?php echo t('This are un-moderated locations that are published. Probably imported or suggested by users.'); ?></p>
<ul>
  <?php foreach($suggested_active_locations as $location): ?>
    <?php
    $status = '';
    switch($location->field_moderated_status[LANGUAGE_NONE][0]['value']){
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
    <li>
      <?php echo $location->title; ?> (<?php echo $status; ?>)
      &nbsp;
      <a href="/node/<?php echo $location->nid; ?>/edit&destination=admin/reports/gojira" title="backend link">backend</a>
      &nbsp;
      <a href="/?loc=<?php echo $location->nid; ?>" title="frontend link">frontend</a>
    </li>
  <?php endforeach; ?>
</ul>

<h2><?php echo t('Double locations'); ?></h2>
<p><?php echo t('This are locations with the same coordinates'); ?></p>
<ul>
  <?php foreach($double_coordinates as $doubles): ?>
    <li>
      <?php foreach($doubles as $nid): ?>
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
      <?php endforeach; ?>
    </li>
  <?php endforeach; ?>
</ul>
