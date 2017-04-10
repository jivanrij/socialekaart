<h1><?php echo drupal_get_title(); ?></h1>

<?php if($output['subscribed']): ?>
    <div id="settings_payment_info">
        <hr />
        <h2><?php echo t('Payment information'); ?></h2>
        <?php if($output['subscribed'] && Subscriptions::getEndCurrentPeriod('d-m-Y')): ?>
            <p><?php echo t('You have a payed account.'); ?></p>
            <p><?php echo t('The period you payed for will end on: '); ?><?php echo Subscriptions::getEndCurrentPeriod('d-m-Y'); ?></p>
            <?php if(Subscriptions::canExtend()): ?>
                <p><?php echo helper::getText('PAYED_TEXT_SETTINGS_PAGE'); ?></p>
                <?php $f = drupal_get_form('gojira_to_subscribe_page_form'); echo render($f); ?>
            <?php endif; ?>
        <?php else: ?>
            <p>Er zijn geen betalingsgegevens gevonden.</p>
        <?php endif; ?>
        <hr />
        <h2><?php echo t('Settings & Location'); ?></h2>
    </div>
    <a href="" id="settings_payment_info_switch"><?php echo t('Show payment information'); ?></a>
<?php endif; ?>

<?php echo render($output['gojira_settings_form']); ?>

<?php if(!$output['subscribed']): ?>
    <p><?php echo helper::getText('NOT_PAYED_TEXT_SETTINGS_PAGE'); ?></p>
    <?php $f = drupal_get_form('gojira_to_subscribe_page_form'); echo render($f); ?>
<?php endif; ?>

  <p class="show_intro">
      Klik <a rel="1" onClick="showTutorial();" title="introductie bekijken">hier</a> als u de introductie nog een keer wilt zien.
  </p>
<?php if($output['multiple_locations']): ?>
  <p>
      <a href="/location/edit" title="<?php echo t('Add new location'); ?>"><?php echo t('Add new location'); ?></a><br />
  </p>
  <?php if(count($output['user_locations']) > 0): ?>
    <table>
      <tr><th><?php echo t('Practice'); ?></th><th><?php echo t('Actions'); ?></th></tr>
      <?php foreach($output['user_locations'] as $location): ?>
        <tr>
          <td>
              <?php if($location->status == 0): ?>
                <label style="color:#d8d8d8;" class="has_help" title="<?php echo t('This practice is not usable because of incomplete data.'); ?>"><?php echo $location->title; ?></label
              <?php else: ?>
                <?php echo $location->title; ?>
              <?php endif; ?>
          </td>
          <td>
            <a class="delete_location" href="/?q=location/delete&id=<?php echo $location->nid; ?>" title="<?php echo t('Remove location @title', array('@title'=>$location->title)); ?>"><?php echo t('remove'); ?></a>
            &nbsp;
            <a href="/?q=location/edit&id=<?php echo $location->nid; ?>" title="<?php echo t('Edit location @title', array('@title'=>$location->title)); ?>"><?php echo t('edit'); ?></a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
<?php endif; ?>
