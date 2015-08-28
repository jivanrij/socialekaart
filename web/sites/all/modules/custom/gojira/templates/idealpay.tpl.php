<h1><?php echo drupal_get_title(); ?></h1>
<?php if(Subscriptions::canExtend()): ?>
    <p><?php echo helper::getText('IDEAL_PAY_PAGE'); ?></p>
    
    
    <?php
    if($paymentinfo['discount']){
        echo '<p>'.t('You will get :discount discount on the price of :amount because you are a member of HAWeb.', array(':discount'=>'€ '.helper::formatMoney($paymentinfo['discount']), ':amount'=>'€ '.helper::formatMoney($paymentinfo['amount']))).'</p>';
    }
    
    echo '<p>'.t('The total amount will be :total from that :tax is tax.',array(':total'=>'€ '.helper::formatMoney($paymentinfo['total']),':tax'=>'€ '.helper::formatMoney($paymentinfo['tax']))).'</p>';
    
    ?>
    <?php echo render($gojira_idealpay_form); ?>
<?php else: ?>
    <p><?php echo t('The system does not allow you to subscribe for more than one year ahead.'); ?></p>
<?php endif; ?>

