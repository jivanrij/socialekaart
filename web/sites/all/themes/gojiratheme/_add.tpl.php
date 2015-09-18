<?php
if(!user_access(helper::PERMISSION_HIDE_ADDS)){
$node = Add::getRandomAdd();
if($node){
  $url = helper::value($node, GojiraSettings::CONTENT_TYPE_ADD_URL_FIELD);
  ?>
    <div id="add_wrapper" class="rounded">
      <a target="_new" href="<?php echo $url; ?>" alt="add link" title="<?php echo $node->title; ?>" class="wide_add"><?php echo Add::getImageUrl($node, GojiraSettings::IMAGE_STYLE_ADD_WIDE); ?></a>
      <a target="_new" href="<?php echo $url; ?>" alt="add link" title="<?php echo $node->title; ?>" class="small_add"><?php echo Add::getImageUrl($node, GojiraSettings::IMAGE_STYLE_ADD_SMALL); ?></a>
    </div>
  <?php 
  }
}