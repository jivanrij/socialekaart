<ul>
<?php
foreach($groups as $group)
{
    echo '<li><a href="/?q=admin/config/system/groupdetail&gid='.$group->gid.'">'.str_replace('Group made by ','',$group->title).'</a></li>';
}
?>
</ul>
