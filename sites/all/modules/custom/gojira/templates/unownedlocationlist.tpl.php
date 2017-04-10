<p><a href="/?q=unownedlocation/edit" title="<?php echo t('Add new other location'); ?>"><?php echo t('Add new other location'); ?></a></p>
<?php if(count($output['locations']) > 0): ?>
  <table>
    <tr><th><?php echo t('Location'); ?></th><th><?php echo t('Actions'); ?></th></tr>
    <?php foreach($output['locations'] as $location): ?>
      <tr>
        <td><?php echo $location->title; ?></td>
        <td>
          <a href="/?q=unownedlocation/edit&id=<?php echo $location->nid; ?>" title="<?php echo t('Edit location @title', array('@title'=>$location->title)); ?>"><?php echo t('edit'); ?></a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>
<p>
  Het gaat hier om locaties die eigenlijk niet in het beheer van de huidige user vallen maar waarvan de user wel denkt dat hij ze nodig heeft. Als er hier een locatie
  wordt aangemaakt zal het opgegeven e-mail adres een uitnodiging krijgen voor het systeem. Als deze persoon hier op in gaat zal het beheer van deze locatie overgedragen worden.
</p>