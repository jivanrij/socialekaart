<div class="container">
    <div class="row">
        <div class="col-sm-12 frontpage_block">
            <div class="introduction_wrapper">

                <button type="button" id="introduction_func" class="intro btn btn-danger">Functionaliteiten</button>
                <button type="button" id="introduction_abon" class="intro btn btn-secondary">Abonnement</button>
                <button type="button" id="introduction_price" class="intro btn btn-secondary">Pricing</button>
                <script>
                    jQuery(document).ready(function(){
                        jQuery("button.intro").click(function(){
                            jQuery("button.intro.btn-danger").removeClass('btn-danger').addClass('btn-secondary');
                            jQuery(this).addClass('btn-danger').removeClass('btn-secondary');
                            jQuery("div.introduction_wrapper div.row").hide();
                            var row_class = jQuery(this).attr('id');
                            jQuery("div.row." + row_class).show();
                        });
                    });
                </script>
                <div class="row introduction_func">
                    <div class="col-sm-6 frontpage_block">
                        <div>
                            <h2>Functionaliteiten</h2>
                            <br />
                            Tekst over de basis functionaliteiten. Nullam id dolor id nibh ultricies vehicula ut id elit. Nulla vitae elit libero, a pharetra augue. Donec sed odio dui. Cras mattis consectetur purus sit amet fermentum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas faucibus mollis interdum. Maecenas faucibus mollis interdum. Nullam id dolor id nibh ultricies vehicula ut id elit. Nulla vitae elit libero, a pharetra augue. Donec sed odio dui. Cras mattis consectetur purus sit amet fermentum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas faucibus mollis interdum. Maecenas faucibus mollis interdum.
                        </div>
                    </div>
                    <div class="col-sm-6 frontpage_block">
                        <div>
                            <img class="img-responsive" src="sites/all/modules/custom/gojira/img/introduction/landelijk_zoeken.png" />
                        </div>
                    </div>
                </div>
                <div class="row introduction_abon" style="display:none;">
                    <div class="col-sm-6 frontpage_block">
                        <div>
                            <h2>Abonnement</h2>
                            <br />
                            Tekst over abonnement. Met als voorbeeld afbeelding landelijk zoeken. Nullam id dolor id nibh ultricies vehicula ut id elit. Nulla vitae elit libero, a pharetra augue. Donec sed odio dui. Cras mattis consectetur purus sit amet fermentum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas faucibus mollis interdum. Maecenas faucibus mollis interdum. Nullam id dolor id nibh ultricies vehicula ut id elit. Nulla vitae elit libero, a pharetra augue. Donec sed odio dui. Cras mattis consectetur purus sit amet fermentum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas faucibus mollis interdum. Maecenas faucibus mollis interdum.
                        </div>
                    </div>
                    <div class="col-sm-6 frontpage_block">
                        <div>
                            <img class="img-responsive" src="sites/all/modules/custom/gojira/img/introduction/mijn_kaart.png" />
                        </div>
                    </div>
                </div>
                <div class="row introduction_price"  style="display:none;">
                    <div class="col-sm-12 frontpage_block">
                        <div>
                            <h2>Pricing</h2>
                            <br />
                            Tekst over de kosten van een abonnement. Met als voorbeeld afbeelding landelijk zoeken. Nullam id dolor id nibh ultricies vehicula ut id elit. Nulla vitae elit libero, a pharetra augue. Donec sed odio dui. Cras mattis consectetur purus sit amet fermentum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas faucibus mollis interdum. Maecenas faucibus mollis interdum. Nullam id dolor id nibh ultricies vehicula ut id elit. Nulla vitae elit libero, a pharetra augue. Donec sed odio dui. Cras mattis consectetur purus sit amet fermentum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas faucibus mollis interdum. Maecenas faucibus mollis interdum.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
