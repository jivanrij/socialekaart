<h1>Information about group: <i><?php echo $group->title; ?></i></h1>
<h2>Member(s):</h2>
<table>
    <thead>
        <tr>
            <th>username</th>
            <th>title</th>
            <th>from haweb</th>
            <th>big number</th>
            <th>e-mail</th>
            <th>created</th>
            <th>last login</th>
            <th>role(s)</th>
            <th>options</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($members as $member) : ?>
            <tr>
                <td><?php echo $member->name; ?></td>
                <td><?php echo helper::value($member,'field_user_title'); ?></td>
                <td><?php echo ((bool) helper::value($member,'field_user_not_imported') ? 'no' : 'yes'); ?></td>
                <td><?php echo helper::value($member,'field_big'); ?></td>
                <td><?php echo $member->mail; ?></td>
                <td><?php echo date('d-m-Y', $member->created); ?></td>
                <td><?php echo date('d-m-Y', $member->login); ?></td>
                <td><?php foreach($member->roles as $role): ?><?php echo $role; ?><br /><?php endforeach; ?></td>
                <td><a href="/?q=user/<?php echo $member->uid; ?>/edit" target="_new">bewerk</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<h2>Practice(s):</h2>
<table>
    <thead>
        <tr>
            <th>name</th>
            <th>view</th>
            <th>addres + google maps</th>
            <th>created</th>
            <th width="40%">favorite locations</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($practices as $practice) : ?>
            <tr>
                <td><?php echo $practice->title; ?></td>
                <td><a href="/?loc=<?php echo $practice->nid; ?>" target="_new">show on map</a></td>
                <td><a target="_new" title="google maps" href="<?php echo $practice->mapslink; ?>"><?php echo Location::getAddressString($practice); ?></a></td>
                <td><?php echo date('d-m-Y',$practice->created); ?></td>
                <td>
                    <?php $favorites = Favorite::getInstance()->getAllFavoriteLocations($practice->nid); ?>
                    The group has <?php echo count($favorites); ?> locations stored as favorite on this practice:<br />
                    <?php foreach($favorites as $favorite) : ?><a href="/?loc=<?php echo $favorite->nid; ?>" target="_new"><?php echo $favorite->title; ?></a>&nbsp;/&nbsp;<?php endforeach; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<h2>Payment(s):</h2>
<table>
    <thead>
        <tr>
            <th>user</th>
            <th>username</th>
            <th>description</th>
            <th>payed</th>
            <th>payment id</th>
            <th>period start</th>
            <th>period end</th>
            <th>created</th>
            <th>invoice id</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($payments as $payment) : ?>
            <tr>
                <td><?php echo $payment->uid; ?></td>
                <td><?php echo $payment->name; ?></td>
                <td><?php echo $payment->description; ?></td>
                <td><?php echo $payment->payed; ?></td>
                <td><?php echo $payment->ideal_id; ?></td>
                <td><?php echo date('d-m-Y',$payment->period_start); ?></td>
                <td><?php echo date('d-m-Y',$payment->period_end); ?></td>
                <td><?php echo $payment->created_at; ?></td>
                <td><?php echo $payment->increment; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
