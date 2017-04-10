<?php foreach($websites as $website): ?>
<a target="_new" href="http://<?php echo $website->field_url_value; ?>"><?php echo $website->field_url_value; ?></a><br />
<?php endforeach; ?>
