<h1><?php echo drupal_get_title(); ?></h1>
<p><?php echo t('This are un-moderated locations that are not published. Probably imported or suggested by users.'); ?></p>
<ul>
  <?php foreach($locations as $location): ?>
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