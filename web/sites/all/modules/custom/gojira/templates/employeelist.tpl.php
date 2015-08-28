<h1><?php echo drupal_get_title(); ?></h1>
<p>
  <?php 
    echo t('An account can be one of 2 types. A type employee can only see the data managed by you, an employer account can do as much as you can. You are also an employer account.');
  ?>
</p>
<p><a href="/employee/edit" title="<?php echo t('Add new employee'); ?>"><?php echo t('Add new employee'); ?></a></p>
<?php if(count($employees) > 0): ?>
  <table>
    <tr><th><?php echo t('User'); ?></th><th><?php echo t('type of user'); ?></th><th><?php echo t('Actions'); ?></th></tr>
    <?php foreach($employees as $employee): ?>
      <tr>
        <td><?php echo helper::value($employee, GojiraSettings::CONTENT_TYPE_USER_TITLE); ?></td>
        <td>
          <?php echo t(helper::getGojiraRole($employee->uid).' role'); // 'Employer role'?>
        </td>
        <td>
          <a href="/employee/edit&id=<?php echo $employee->uid; ?>" title="<?php echo t('Edit user @name', array('@name'=>$employee->name)); ?>"><?php echo t('edit'); ?></a>
          <?php if($uid != $employee->uid): ?>
            &nbsp;
            <a class="delete_employee" href="/?q=employee/delete&id=<?php echo $employee->uid; ?>" title="<?php echo t('Remove user @name', array('@name'=>$employee->name)); ?>"><?php echo t('remove'); ?></a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>