<div class="container">
    <div class="row">
        <div class="col-sm-12 frontpage_block">
            <div>
                <h1><?php echo drupal_get_title(); ?></h1>
                <?php
                print render($page['content']);
                ?>
            </div>
        </div>
    </div>
</div>