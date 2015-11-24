<?php
$oLocationset = Locationsets::getInstance()->getCurrentLocationset();
if($oLocationset){
    $aLocationsets = Locationsets::getInstance()->getLocations();
    $sBody = $oLocationset->body[LANGUAGE_NONE][0]['value'];
    $sTitle = $oLocationset->title;
}else{
    $aLocationsets = Favorite::getInstance()->getAllFavoriteLocations(true);
    $sBody = t('Your own personal map\'s decription.');
    $sTitle = t('Your own personal map.');
}

?>
<div id="locationset_wrapper" class="rounded">
    <h2><?php echo $sTitle; ?></h2>
    <p><?php echo $sBody; ?></p>
    <?php if(count($aLocationsets)>0): ?>
        <ul>
            <?php foreach($aLocationsets as $oLocationset): ?>
                <li>
                    <?php echo Category::getCategoryName($oLocationset).' / '.$oLocationset->title; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>