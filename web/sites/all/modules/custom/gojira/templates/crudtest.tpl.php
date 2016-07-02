<section>
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <br />
            <h1>Dit is een voorbeeld van een nieuw formulier voor op een pagina zonder kaart.</h1>
            <br /><br />
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <?php include(drupal_get_path('theme', 'gojiratheme') . '/_messages.tpl.php'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <div class="col-md-offset-3 col-md-6">
                <?php helper::renderFormAsBootstrap('gojira_crudtest_form'); ?>
            </div>
        </div>
    </div>
</section>
