<script type="text/javascript" src="/sites/all/modules/custom/gojira/js/jquery.elevateZoom-3.0.8.min.js"></script>
<script>
    jQuery(document).ready(function(){
        jQuery("img.zoom").elevateZoom({zoomWindowWidth:300, zoomWindowHeight:200});
    });
</script>
<div class="container">
    <div class="row">
        <div class="col-sm-12 frontpage_block introduction">
            <div>

                    <h1>De interactieve sociale kaart met landelijke dekking voor Nederlandse huisartsen.</h1>
                    <p>
                        Lees verder om de grootste voordelen voor uw praktijk te ontdekken, of registreer u als gebruiker.
                    </p>
                    <div id="arrows">
                        <img id="arrow_left" src="/<?php echo drupal_get_path('theme', 'gojiratheme') . '/img/arrow_left.png'; ?>" alt="read more" />
                        <img id="arrow_right" src="/<?php echo drupal_get_path('theme', 'gojiratheme') . '/img/arrow_right.png'; ?>" alt="register" />
                    </div>
                    <div id="buttons_intro">
                        <a id="register_intro" class="btn btn-danger" title="<?php echo t('Register'); ?>" href="/registreer">Registreer <i class="fa fa-pencil" aria-hidden="true"></i></a>
                        <a id="read_more_intro" class="btn btn-danger" title="<?php echo t('Read more'); ?>" href="#more">Lees meer <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                    </div>
            </div>
        </div>
    </div>
    <a name="more"></a>
            <div class="introduction_wrapper">
                <div class="row frontpage_block first">
                    <div class="col-sm-5">
                        <div>
                            <img data-zoom-image="sites/all/modules/custom/gojira/img/introduction/landelijk_zoeken.png" style="max-height:200px;margin: 0 auto;" class="img-responsive img-rounded zoom" src="sites/all/modules/custom/gojira/img/introduction/landelijk_zoeken_magnify.png" />
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div>
                            <h2>Zorgverleners vinden</h2>
                            <p>
                                Hoe vindt u de juiste zorgverleners voor uw patiënten? Met SocialeKaart.care kunt u snel en intuïtief
                                zorgverleners vinden binnen uw regio en in heel Nederland. Het verwijzen vanuit uw praktijk wordt
                                efficiënt en doeltreffend.<br/>
                                Voorbeelden van zoekopdrachten:
                            </p>
                            <ul>
                                <li>GGZ (zoek op beroepsgroep)</li>
                                <li>Buurtzorg Rotterdam (zoek op naam en plaats)</li>
                                <li>Bekkenfysiotherapeut (zoek op specialisme)</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row frontpage_block">
                    <div class="col-sm-5">
                        <div>
                            <img style="max-height:200px;margin: 0 auto;" class="img-responsive" src="sites/all/modules/custom/gojira/img/introduction/labels.png" />
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div>
                            <h2>Waardevol netwerk</h2>
                            <p>
                                U wilt uw kennis over zorgverleners graag delen met collega’s. In SocialeKaart.care voegt u eenvoudig specialismen en kwaliteiten toe aan zorgverleners in uw regio. Omdat deze informatie wordt gedeeld met andere huisartsen bouwt u samen met uw collega’s een waardevol verwijsnetwerk op.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row frontpage_block">
                    <div class="col-sm-5">
                        <div>
                            <img style="max-height:200px;margin: 0 auto;" class="img-responsive" src="sites/all/modules/custom/gojira/img/introduction/up-to-date.png" />
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div>
                            <h2>Up-to-date</h2>
                            <p>
                                De zorg verandert snel, hoe blijft uw verwijsinformatie actueel? Samen met huisartsen in de regio deelt u dezelfde gegevens. Wijzigingen die door u en uw collega’s worden gedaan zijn direct zichtbaar. De inspanning die nodig is om de sociale kaart actueel te houden wordt zo tot een minimum beperkt.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row frontpage_block">
                    <div class="col-sm-5">
                        <div>
                            <img style="max-height:200px;margin: 0 auto;" class="img-responsive" src="sites/all/modules/custom/gojira/img/introduction/mobile.png" />
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div>
                            <h2>Toegankelijk</h2>
                            <p>
                                SocialeKaart.care is vanaf elke plek online te benaderen. Zo heeft u uw verwijsgegevens altijd bij de hand, ook op tablet en mobiel.
                            </p>
                            <p>
                                U vindt SocialeKaart.care ook op de startpagina van HAweb, het online platform voor huisartsen.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row frontpage_block first">
                    <div class="col-sm-5">
                        <div>
                            <img style="max-height:200px;margin: 0 auto;" class="img-responsive img-rounded" src="sites/all/modules/custom/gojira/img/introduction/person.png" />
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div style="vertical-align: middle;">
                            <h2>Voor wie is SocialeKaart.care?</h2>
                            <p>
                                Huisartsen met een BIG-registratie kunnen gratis gebruik maken van SocialeKaart.care. De verwijsinformatie wordt alleen gedeeld met huisartsen die zich hebben aangemeld. SocialeKaart.care is een Nederlands product en een initatief van Blijnder VOF.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row frontpage_block">
                    <div class="col-sm-5">
                        <div>
                            <h2>Basisversie</h2>
                            <p>
                                SocialeKaart.care is gratis. De basisversie biedt alle functies die u nodig heeft om efficiënt te zoeken en te verwijzen.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div>
                            <h2>Plusversie</h2>
                            <p>
                                Met een abonnement krijgt u toegang tot de extra mogelijkheden. Voor €5,- per maand kunt u uw eigen kaart opbouwen, werken vanuit diverse praktijken en medewerkers toevoegen.
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
