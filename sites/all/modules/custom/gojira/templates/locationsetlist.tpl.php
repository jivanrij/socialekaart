<section class="container">
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <br />
            <h1><?php echo drupal_get_title(); ?></h1>
            <br /><br />
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-3 col-md-6 table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Zorgverleners</th>
                        <th>Gebruikers</th>
                        <th>Beheerders</th>
                        <th>Opties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($setModels as $setModel) : ?>
                        <tr>
                            <td><?php echo $setModel->title; ?></td>
                            <td><?php echo $setModel->getLocationsCount(); ?></td>
                            <td><?php echo $setModel->getUsersCount(); ?></td>
                            <td><?php echo $setModel->getModeratorsCount(); ?></td>
                            <td>
                                <?php if(user_access('administer permissions')) : ?><a href="/?q=node/<?php echo $setModel->nid; ?>/edit&destination=locationsetlist" title="<?php echo t('edit in backend'); ?>"><i class="fa fa-wrench" aria-hidden="true"></i></a><?php endif; ?>
                                <a href="<?php echo url('node/'. $setModel->nid); ?>" title="<?php echo t('show'); ?>"><i class="fa fa-map" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
