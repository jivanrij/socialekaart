<script type="text/javascript" src="http://socialekaart.dev/sites/all/modules/custom/gojira/js/jquery.elevateZoom-3.0.8.min.js"></script>
<script>
    jQuery(document).ready(function(){
        // jQuery("button.intro").click(function(){
        //     jQuery("button.intro.btn-danger").removeClass('btn-danger').addClass('btn-secondary');
        //     jQuery(this).addClass('btn-danger').removeClass('btn-secondary');
        //     jQuery("div.introduction_wrapper div.row").hide();
        //     var row_class = jQuery(this).attr('id');
        //     jQuery("div.row." + row_class).show();
        // });
        jQuery("img.zoom").elevateZoom();
    });
</script>
<div class="container">
    <div class="row">
        <div class="col-sm-12 frontpage_block">
            <div class="introduction_wrapper">
                <!-- <button type="button" id="introduction_func" class="intro btn btn-danger">Functionaliteiten</button>
                <button type="button" id="introduction_abon" class="intro btn btn-secondary">Abonnement</button>
                <button type="button" id="introduction_price" class="intro btn btn-secondary">Pricing</button> -->
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
                                Hoe vindt u de juiste zorgverleners voor uw patienten? Met SocialeKaart.care kunt u snel en intuïtief
                                zorgverleners vinden binnen uw regio en in heel Nederland. Het verwijzen vanuit uw praktijk wordt
                                efficiënt en doeltreffend.<br/>
                                Voorbeelden van zoekopdrachten:
                            </p>
                            <ul>
                                <li>GGZ (zoek op beroepsgroep)</li>
                                <li>Buurtzorg rotterdam (zoek op naam en plaats)</li>
                                <li>Bekkenfysiotherapeut (zoek op specialisme)</li>
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
                                U wilt uw kennis over zorgverleners graag delen met collega’s. In SocialeKaart.care voegt u eenvoudig specialismen en kwaliteiten toe aan zorgverleners in uw regio. Omdat deze informatie wordt gedeeld met andere huisartsen bouwt u samen met uw collega’s een waardevol verwijs-netwerk op.
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
                                De zorg verandert snel, hoe blijft uw verwijsinformatie actueel? SocialeKaart.care is up-to-date omdat u en uw collega’s dezelfde verwijsinformatie delen en bijwerken. De inspanning die nodig is om de sociale kaart actueel te houden wordt tot een minimum beperkt.
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
                                SocialeKaart.care is vanaf elke plek online te benaderen. Zo heeft u uw verwijsgegevens altijd bij de hand, ook tablet en mobiel.
                            </p>
                            <p>
                                U vindt SocialeKaart.care ook op de startpagina van HAweb, het online platform voor huisartsen.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row introduction_func">
                    <div class="col-sm-5 frontpage_block">
                        <div>
                            <h2>Basisversie</h2>
                            <p>
                                SocialeKaart.care is gratis. De basisversie biedt alle functies die u nodig heeft om efficiënt te zoeken en te verwijzen.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-7 frontpage_block">
                        <div>
                            <h2>Plusversie</h2>
                            <p>
                                Met een abonnement krijgt u toegang tot de extra mogelijkheden. Voor € 5,- per maand kunt u uw eigen kaart opbouwen, werken vanuit diverse praktijken en medewerkers toevoegen.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- <div class="row introduction_func">
                    <div class="col-sm-12 frontpage_block">
                        <div>
                            <p style="font-size:12px;">
                                SocialeKaart.care is een initiatief van Blijnder.
                            </p>
                        </div>
                    </div>
                </div> -->
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
