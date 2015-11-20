<?php $oLocationset = Locationsets::getInstance()->getCurrentLocationset(); ?>
<?php $aLocationsets = Locationsets::getInstance()->getLocations(); ?>
<div id="locationset_wrapper" class="rounded">
    <h2><?php echo $oLocationset->title; ?></h2>
    <p><?php echo $oLocationset->body[LANGUAGE_NONE][0]['value']; ?></p>
    
    <?php if(count($aLocationsets)>0): ?>
        <ul>
            <?php foreach($aLocationsets as $oLocationset): ?>
                <li>
                    <?php echo Category::getCategoryName($oLocationset['node']).' / '.$oLocationset['node']->title; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>