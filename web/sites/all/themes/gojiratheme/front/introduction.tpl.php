<div class="container">
    <div class="row">
        <div class="col-sm-12 frontpage_block">
            <div class="introduction_wrapper">

                <button type="button" id="introduction_func" class="intro btn btn-danger">Functionaliteiten</button>
                <button type="button" id="introduction_abon" class="intro btn btn-secondary">Abonnement</button>
                <button type="button" id="introduction_price" class="intro btn btn-secondary">Pricing</button>
                <script type="text/javascript" src="http://socialekaart.dev/sites/all/modules/custom/gojira/js/jquery.elevateZoom-3.0.8.min.js"></script>
                <script>
                    jQuery(document).ready(function(){
                        jQuery("button.intro").click(function(){
                            jQuery("button.intro.btn-danger").removeClass('btn-danger').addClass('btn-secondary');
                            jQuery(this).addClass('btn-danger').removeClass('btn-secondary');
                            jQuery("div.introduction_wrapper div.row").hide();
                            var row_class = jQuery(this).attr('id');
                            jQuery("div.row." + row_class).show();
                        });
                        jQuery("img.zoom").elevateZoom();
                    });
                </script>
                <div class="row introduction_func first">
                    <div class="col-sm-5 frontpage_block">
                        <div>
                            <img data-zoom-image="sites/all/modules/custom/gojira/img/introduction/landelijk_zoeken.png" style="max-height:200px;margin: 0 auto;" class="img-responsive img-rounded zoom" src="sites/all/modules/custom/gojira/img/introduction/landelijk_zoeken_magnify.png" />
                        </div>
                    </div>
                    <div class="col-sm-7 frontpage_block">
                        <div>
                            <h2>Zorgverleners vinden</h2>
                            <p>
                                Hoe vindt u snel de juiste zorgverlener? Met SocialeKaart.care!<br />
                                Vindt alle zorgverleners in uw regio door te zoeken:
                            </p>
                            <ul>
                                <li>op beroepsgroep (bijv. GGZ en Fysio)</li>
                                <li>op naam (bijv. buurtzorg, fysioplus)</li>
                                <li>op specialisatie (bijv. bekkenfysiotherapeut, autisme)</li>
                            </ul>
                            <p>
                                Krijg toegang tot zorgverleners in heel Nederland door:
                            </p>
                            <ul>
                                <li>een plaatsnaam mee te geven (bijv. psycholoog breda)</li>
                                <li>landelijk te zoeken</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row introduction_func">
                    <div class="col-sm-5 frontpage_block">
                        <div>
                            <img style="max-height:200px;margin: 0 auto;" class="img-responsive" src="sites/all/modules/custom/gojira/img/introduction/labels.png" />
                        </div>
                    </div>
                    <div class="col-sm-7 frontpage_block">
                        <div>
                            <h2>Waardevol netwerk</h2>
                            <p>
                                U kunt zelf aan alle zorgverleners in uw regio specialismen en kwaliteiten toekennen. Deze informatie wordt gedeeld met andere huisartsen. Zo bouwt u samen met uw collega’s een waardevol verwijsnetwerk op.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row introduction_func">
                    <div class="col-sm-5 frontpage_block">
                        <div>
                            <img style="max-height:200px;margin: 0 auto;" class="img-responsive" src="sites/all/modules/custom/gojira/img/introduction/up-to-date.png" />
                        </div>
                    </div>
                    <div class="col-sm-7 frontpage_block">
                        <div>
                            <h2>Up-to-date</h2>
                            <p>
                                SocialeKaart.care is up-to-date omdat u en uw collega’s in de regio dezelfde verwijsinformatie delen en bijwerken. De inspanning die nodig is om de sociale kaart actueel te houden wordt hierdoor tot een minimum beperkt.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row introduction_func">
                    <div class="col-sm-5 frontpage_block">
                        <div>
                            <img style="max-height:200px;margin: 0 auto;" class="img-responsive" src="sites/all/modules/custom/gojira/img/introduction/mobile.png" />
                        </div>
                    </div>
                    <div class="col-sm-7 frontpage_block">
                        <div>
                            <h2>Toegankelijk</h2>
                            <p>
                                Vanaf elke plek kunt u online de verwijsgegevens benaderen, ook via een tablet of mobiel.
                            </p>
                            <p>
                                SocialeKaart.care kunt u starten vanuit het online platform van HAweb.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row introduction_abon"  style="display:none;">
                    <div class="col-sm-12 frontpage_block">
                        <div>
                            <h2>Abonnement</h2>
                            <br />
                            Tekst over een abonnement. Met als voorbeeld afbeelding landelijk zoeken. Nullam id dolor id nibh ultricies vehicula ut id elit. Nulla vitae elit libero, a pharetra augue. Donec sed odio dui. Cras mattis consectetur purus sit amet fermentum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas faucibus mollis interdum. Maecenas faucibus mollis interdum. Nullam id dolor id nibh ultricies vehicula ut id elit. Nulla vitae elit libero, a pharetra augue. Donec sed odio dui. Cras mattis consectetur purus sit amet fermentum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas faucibus mollis interdum. Maecenas faucibus mollis interdum.
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
<style>
    div.introduction_wrapper div.row {
        border-top: 1px solid #4d4d4d;
    }
    div.introduction_wrapper div.row.first {
        border-top: none;
    }
</style>
