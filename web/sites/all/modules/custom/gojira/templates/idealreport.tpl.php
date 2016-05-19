<?php $aFields = array('id'=>'Database id', 'uid'=>'User id', 'name'=>'Name of the user', 'description'=>'Transaction description', 'gid'=>'Id of the group the user belongs to', 'ideal_id'=>'Id of the ideal transaction', 'status'=>'Payment status: 0=open, 1=completed, 2=failed', 'period_start'=>'Start day of the payed period', 'period_end'=>'End day of the period', 'warning_send'=>'The system has send a warning the the subscription is going to end', 'warning_ended'=>'The system has send a warning the subscription is ended', 'increment'=>'Related invoice Id', 'discount'=>'Given discount', 'amount'=>'Original amount', 'tax'=>'Amount of tax payed', 'payed'=>'Payed amount inc. tax', 'callback_times'=>'Amount of times the callback was used for this payment. Max is 6 times after 5, 10, 30, 60, 120 and 300 minutes.', 'method'=>'The payment method the user has used'); ?>
<h1><?php echo drupal_get_title(); ?></h1>
<span style="font-size:10px;">
    Hover of the head title to see the description.
</span>
<table style="width:100%">
    <tr>
        <?php foreach ($aFields as $sName=>$sDescription): ?>
            <th title="<?php echo $sDescription; ?>"><?php echo $sName; ?></th>
        <?php endforeach; ?>
    </tr>
    <?php foreach ($rPayments as $oReport): ?>
        <tr>
            <?php foreach ($aFields as $sName=>$sDescription): ?>
            <td>
                <?php if ($sName == 'period_start' || $sName == 'period_end'): ?>
                    <?php echo date('d-m-Y',$oReport->$sName); ?>
                <?php else: ?>
                    <?php echo $oReport->$sName; ?>
                <?php endif; ?>
            </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
</table>
