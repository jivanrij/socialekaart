<h1><?php echo drupal_get_title(); ?></h1>
ADHOC DATA:<br />
<?php 
echo 'Need to be imported: '.$adhoc_need_import.'<br />';
echo 'Are imported: '.$adhoc_imported.'<br />';  
?>
SPIDER:<br />
<?php
echo 'Need to be imported: '.$spider_need_import.'<br />';
echo 'Are imported: '.$spider_imported.'<br />';
echo 'Not allowed because they are from a specific category: '.$spider_notallowed.'<br />';
echo 'Not imported, because adhocdata had them: '.$spider_double.'<br />';
?>
<hr />
<br />
<?php
// IMPORT AdhocdataImportLocations
?>
<form method="get" id="importadhocdata" accept-charset="UTF-8">
  <input type="hidden" name="q" value="admin/config/system/gojiratools" />
  <input type="hidden" name="importadhocdata" value="1" />
  <input type="submit" id="edit-submit--4" name="op" value="<?php echo t('Import the data from adhocdata table into the system'); ?>" class="form-submit">
  <input type="text" name="importadhocdata_amount" value="500" style="border:1px;border-color: #666; border-style:solid; " />
</form>


<!--<hr />
<br />-->
<?php
// find coordinates
?>
<!--<form method="get" id="findimportedcoordinates" accept-charset="UTF-8">
  <input type="hidden" name="q" value="admin/config/system/gojiratools" />
  <input type="hidden" name="findimportedcoordinates" value="1" />
  <input type="submit" id="edit-submit--4" name="op" value="<?php echo t('find coordinates for imported csv for the given amount of items'); ?>" class="form-submit">
  <input type="text" name="findimportedcoordinates_amount" value="250" style="border:1px;border-color: #666; border-style:solid; " />
</form>-->




<hr />
<?php
// INDEXING OPTIONS
?>
<br />
<p>
<form method="get" id="reindex_some" accept-charset="UTF-8">
  <input type="hidden" name="q" value="admin/config/system/gojiratools" />
  <input type="text" name="index_some" value="" style="border:1px;border-color: #666; border-style:solid; " />
  <input type="submit" id="edit-submit--4" name="op" value="<?php echo t('Reindex locations based on there nid\'s.'); ?>" class="form-submit">
  <br />
  <i>Index some nodes, enter the nid's like this => 1,2,3,4,5</i>
  <br />
  <i>Only admin can do this for security reasons.</i>
</form>
</p>
<?php if(!isset($_GET['show_reindex'])): echo count($need_indexing).' need re-indexing (<a href="/?q=admin/config/system/gojiratools&show_reindex=1">show them</a>).'; endif; ?>
<?php if(isset($_GET['show_reindex'])): echo count($need_indexing).' need re-indexing (<a href="/admin/config/system/gojiratools">hide them</a>):'; endif; ?>
<p>
<?php if(isset($_GET['show_reindex'])): foreach($need_indexing as $nid=>$title){ echo $nid.','; } endif; ?>
</p>
<p>
  <?php if(isset($_GET['show_reindex'])): foreach($need_indexing as $nid=>$title){ ?><a target="_new" href="/?loc=<?php echo $nid; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a><br /><? } endif; ?>
</p>
<hr />









<?php // SET REINDEX FLAGS  ?>
<p>
    If you push this button the howl site will be flagged for reindexing.
</p>
<form id="set_reindex_flags" method="POST" action="/?q=admin/config/system/gojiratools&set_reindex_all=on">
  <input class="form-submit" type="submit" value="Set flags for reindexing" />
</form>
<hr />











<?php // BACKUP SYSTEM  ?>
<p>
  If you push this button backup all the location into the practices_backup table.<br />
  It will backup max 10.000 locations a time. It backups all locations from the node table with a change & nid value not present in the backup table.<br />
  This can take up to 15 minutes max.
</p>
<form id="backup_practices" method="POST" action="/?q=admin/config/system/gojiratools&backup_practices=on">
  <input class="form-submit" type="submit" value="Make Backup" />
</form>
<script>
  jQuery("#backup_practices").submit(function(e){
    e.preventDefault();
    if (confirm('Are you sure you want to BACKUP the system?')) {
      window.location = '/?q=admin/config/system/gojiratools&backup_practices=on';
    }else{
      alert('pffff....');
    }
  });
</script>
<?php
// IMPORT practice_backup locations
?>
<p>
  If you push this button all locations in the practices_backup table with the import_it flag on 1 will be imported.<br />
  It will import a maximum of 10.00 a time.<br />
  These locations will be flagged for indexing.<br />
</p>
<form id="restore_backup" method="POST" action="/?q=admin/config/system/gojiratools&restore_backup=on">
  <input class="form-submit" type="submit" value="Restore backup" />
</form>
<script>
  jQuery("#restore_backup").submit(function(e){
    e.preventDefault();
    if (confirm('Are you sure you want to RESTORE all the flagged locations in the backup table')) {
      window.location = '/?q=admin/config/system/gojiratools&restore_backup=on';
    }else{
      alert('pffff....');
    }
  });
</script>





<hr />
<?php // EMPTY THE SYSTEM OPTIONS ?>
<p>
    This will remove all the locations from the system.
</p>
<form method="get" id="empty" accept-charset="UTF-8">
  <input type="hidden" name="empty_all" value="shit!" />
  <input type="submit" name="op" value="!Empty the system!" class="form-submit" />
</form>
<script>
  jQuery("#empty").submit(function(e){
    e.preventDefault();
    if (confirm('Are you sure you want to EMPTY the system?!?!?!?!')) {
      //alert('Sorry, won\'t allow this...');
      window.location = '/admin/config/system/gojiratools&empty_all=shit!';
    }else{
      alert('pffff....');
    }
  });
</script>
<hr />












<?php // RUN CRON  ?>
<p>
    Put the Cron url in this form and run it. A javascript will continue running the cron until you push <a href="/admin/config/system/gojiratools" title="stop cron">stop cron</a> or after 500 times.
</p>
<p>
    The cron:
    <ul>
        <li>removes all the not linked taxonomy terms</li>
        <li>imports locations from the adhocdata_addresses table that need importing (max 250 a time)</li>
        <li>put's locations in the searchindex that need to be re-indexed (max 250 a time)</li>
</ul>
You can find the cron url <a href="/admin/config/system/cron" title="cron page">here</a>.
</p>
<form method="get" id="cron" accept-charset="UTF-8">
  <input type="text" id="cron_url" name="cron_url" style='border:1px; border-style: solid; ' />
  <br />
  <input type="submit" value="Run cron" class="form-submit" />
</form>
<p>
    Amount of times the cron has run: <span id='cron_run'>0</span>.
    <br /><a href="/admin/config/system/gojiratools" title="stop cron">stop cron</a>
</p>
<script>
  jQuery("#cron").submit(function(e){
    e.preventDefault();
    var run_times = parseInt(jQuery("#cron_run").text());
    jQuery("#cron_run").html(run_times);
    run_gojira_cron();
  });
  
  function run_gojira_cron(){
    jQuery.get(jQuery("#cron_url").val(),function(data,status) {
      var run_times = parseInt(jQuery("#cron_run").text())+1;
      jQuery("#cron_run").html(run_times);
      setTimeout(function(){
        if(run_times < 501){
          run_gojira_cron();
        }else{
          alert('Stopped cron after 500 times.');
        }
      }, 2000);
    },'html');
  }
</script>
<hr />
<br />







<?php // MERGE OR RENAME CATEGORIES  ?>
<?php if(isset($_GET['change_category'])): ?>
<p>
    <?php if(!$changed_category_locations): ?>
      <span style="color:red;">
        Tryed to change <?php echo $_GET['change_category_nid']; ?> into <?php echo $_GET['new_category_name']; ?> but there where to many location linked.
        Only changed the first 500. Run the job a couple of times more, untill you don't get this message anymore. Check out the remaining locations on this category
        <a href="/?q=admin/reports/gojirareport_location_by_category&category=<?php echo $_GET['change_category_nid']; ?>" title="category linked locations report">here</a>.
      </span>
      <?php else: ?>
      <span style="color:red;"> 
        Changed <?php echo $_GET['change_category_nid']; ?> into <?php echo $_GET['new_category_name']; ?>.
        Changed the following locations for this:
      <?php foreach($changed_category_locations as $changed_category_location): ?>
        <?php echo $changed_category_location->nid; ?>, 
      <?php endforeach; ?>
      </span>
    <?php endif; ?>
</p>
<?php endif; ?>
<p>
    This function let's you change a category to another one. It does not rename an exiting one. It get's all the locations connected to the original one, then removes the original one, then adds or get's the new one, and links the locations to the new category.
    So this way you can merge 'gehandicaptenzorg' & 'gehandicapten thuiszorg' & 'gehandicapten dokters' into 'handicap'. With 3 actions.
</p>
<form id="change_category_form" action="">
  <label for="category">change category</label>
  <select id="change_category_nid" name="change_category_nid" style='border:1px; border-style: solid; '>
  <?php foreach($categories as $location): ?>
      <?php
      $selected = '';
      if(isset($_GET['change_category_nid']) && $_GET['change_category_nid'] == $location->nid) $selected = 'selected';
      ?>
      <option <?php echo $selected; ?> value="<?php echo $location->nid; ?>"><?php echo $location->title; ?></option>
  <?php endforeach; ?>
  </select>
  <label for="new_name">into this new one</label>
  <input type="text" id="new_category_name" value="<?php echo (isset($_GET['new_category_name']) ? $_GET['new_category_name'] : ''); ?>" name="new_category_name" style='border:1px; border-style: solid;' />
  <br />
  <input class="form-submit" type="submit" />
</form>
<script>
  jQuery("#change_category_form").submit(function(e){
    e.preventDefault();
    if (confirm('Are you sure, you can a lot of damage here... ?')) {
      var change_category_nid = jQuery("#change_category_nid").val();
      var new_category_name = jQuery("#new_category_name").val();
      window.location = '/?q=admin/config/system/gojiratools&change_category=1&change_category_nid='+change_category_nid+'&new_category_name='+new_category_name;
    }
  });
</script>
<br />
<hr />
<br />






<?php // REMOVE LOCATIONS OF SPECIFIC CATEGORY  ?>
<p>
  Removes a category and all linked locations.
</p>
<form id="remove_locations_of_cat_form" action="">
  <label for="category">select category</label>
  <select id="remove_category_locations_nid" name="remove_category_locations_nid" style='border:1px; border-style: solid; '>
  <?php foreach($categories as $location): ?>
      <option <?php echo $selected; ?> value="<?php echo $location->nid; ?>"><?php echo $location->title; ?></option>
  <?php endforeach; ?>
  </select>
  <input class="form-submit" type="submit" />
</form>
<script>
  jQuery("#remove_locations_of_cat_form").submit(function(e){
    e.preventDefault();
    if (confirm('Are you sure, you can a lot of damage here... ??!!?!?')) {
      var remove_category_locations_nid = jQuery("#remove_category_locations_nid").val();
      window.location = '/?q=admin/config/system/gojiratools&remove_category=1&remove_category_locations_nid='+remove_category_locations_nid;
    }
  });
</script>
<br />
<hr />







<?php // SEND TEST MAIL ?>
<p>
  Send some of the system e-mails to your account's e-mail.
</p>
<form id="gojira_send_mail" method="POST" action="/?q=admin/config/system/gojiratools">
    <select name="gojira_send_mail" style='border:1px; border-style: solid;'>
        <option value="sendAccountMergeRequest">Send a account merge request to a user.</option>
        <option value="sendSubscriptionEnded">Send the e-mail that a doctor get's when the subscription is ended.</option>
        <option value="sendSubscriptionEndWarning">Send the e-mail that a doctor get's when the subscription is going to end in 30 days.</option>
        <option value="sendInvoiceOfNewSubscription">Send the e-mail that a customer get's after a subscription is payed for.</option>
        <option value="sendWelcomeMailToEmployee">Send the welcome e-mail that get's send to a new employee</option>
        <option value="sendWelcomeMailToEmployer">Send the welcome e-mail that get's send to a new employer</option>
        <option value="sendUnsubscribeMail">Send the e-mail a employer & employee recieve when unsubscribed</option>
        <option value="sendSubscribeActivationMail">Send the e-mail a employee & employer recieves when the account get's activated after it's unsubscribed</option>
        <option value="accountActivatedByAdmin">Send the mail that gets send when an admin activates the account</option>
        <option value="newAccountThroughSSO">Sends e-mail that gets send when a new account is created through sso to the user of that account</option>
        <option value="sendAccountNeedsValidation">Sends the e-mail to the admin to tell hem an account needs activation</option>
        <option value="sendDoubleAccountWarning">Sends the e-mail to the user that he has 2 accounts, one in Haweb and one in SK. Not linked.</option>
    </select>
    <input type="text" id="email" value="<?php echo (isset($_POST['email']) ? $_POST['email'] : ''); ?>" name="email" style='border:1px; border-style: solid;' />
  <input class="form-submit" type="submit" />
</form>






<?php if(false): ?>
<hr />
<?php // SET GROUP PAYED ?>
<p>
  Subscribe this group
</p>
<form action="">
  <label for="set_payed_group">select group</label>
  <select id="set_payed_group" name="set_payed_group" style='border:1px; border-style: solid; '>
  <?php foreach($groups as $node): ?>
      <option value="<?php echo $node->nid; ?>"><?php echo $node->title; ?> <?php echo $node->nid; ?></option>
  <?php endforeach; ?>
  </select>
  <input class="form-submit" type="submit" />
</form>
<?php endif; ?>




<hr />
<?php // SET GROUP NOT PAYED ?>
<p>
  Unsubscribe this group.<br />
  Removes all the roles that give the group extra rights a payed group get's.
</p>
<form action="">
  <label for="set_not_payed_group">select group</label>
  <input type="hidden" name="q" value="admin/config/system/gojiratools" />
  <select id="set_not_payed_group" name="set_not_payed_group" style='border:1px; border-style: solid; '>
  <?php foreach($groups as $node): ?>
      <option value="<?php echo $node->nid; ?>"><?php echo $node->title; ?> <?php echo $node->nid; ?></option>
  <?php endforeach; ?>
  </select>
  <input class="form-submit" type="submit" />
</form>

<hr />
<?php // SET GROUP  PAYED ?>
<p>
  Subscribe this group.<br />
</p>
<form action="">
  <label for="set_payed_group">select group</label>
  <input type="hidden" name="q" value="admin/config/system/gojiratools" />
  <select id="set_not_payed_group" name="set_payed_group" style='border:1px; border-style: solid; '>
  <?php foreach($groups as $node): ?>
      <option value="<?php echo $node->nid; ?>"><?php echo $node->title; ?> <?php echo $node->nid; ?></option>
  <?php endforeach; ?>
  </select>
  <input class="form-submit" type="submit" />
</form>


<hr />
<?php // set tags on category  ?>
<p>
  Replaces the given tags on all locations of the selected category
</p>
<form id="replace_categories_labels" action="">
    <input type="hidden" name="q" value="admin/config/system/gojiratools" />
  <label for="replace_labels_cat_id">select category</label>
  <select id="replace_labels_cat_id" name="replace_labels_cat_id" style='border:1px; border-style: solid;'>
  <?php foreach($categories as $location): ?>
      <option value="<?php echo $location->nid; ?>"><?php echo $location->title; ?></option>
  <?php endforeach; ?>
  </select>
  <input name="labels" value="" style='border:1px; border-style: solid;' />
  <input class="form-submit" type="submit" />
</form>
