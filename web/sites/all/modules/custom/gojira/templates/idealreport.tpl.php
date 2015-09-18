<?php $aFields = array('id', 'uid', 'name', 'description', 'gid', 'ideal_id', 'ideal_code', 'status', 'period_start', 'period_end', 'warning_send', 'warning_ended', 'increment', 'discount', 'amount', 'tax', 'payed', 'callback_times', 'bank'); ?>
<h1><?php echo drupal_get_title(); ?></h1>
<table style="width:100%">
    <tr>
        <?php foreach ($aFields as $sFields): ?>
            <th><?php echo $sFields; ?></th>
        <?php endforeach; ?>
    </tr>
    <?php foreach ($rPayments as $oReport): ?>
        <tr>
            <?php foreach ($aFields as $sFields): ?>
            <td>
                <?php if ($sFields == 'period_start' || $sFields == 'period_end'): ?>
                    <?php echo date('d-m-Y',$oReport->$sFields); ?>
                <?php else: ?>
                    <?php echo $oReport->$sFields; ?>
                <?php endif; ?>
            </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
</table>