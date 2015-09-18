<p>
  <?php if(count(Location::getUsersLocations(true))==0): ?>
    <?php echo helper::getText('LOCATIONCHECK'); ?>
  <?php endif; ?>
</p>

