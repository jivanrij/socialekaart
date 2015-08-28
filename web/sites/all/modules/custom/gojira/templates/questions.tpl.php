<h1><?php echo t('FAQ'); ?></h1>
<p>
    Welkom op onze FAQ pagina. Wij hopen hier zoveel mogelijk vragen van u te kunnen beantwoorden. Als u geen antwoord kunt vinden op uw vraag kunt u ons uw vraag e-mailen. Onder de pagina is een formulier te vinden waarmee u dit kunt doen. Wij proberen u dan zo snel mogelijk te helpen.
</p>
<hr />
<div id="faq_sections">
    <section>
        <h1>Welke voordelen heeft een abonnement voor mij?</h1>
        <p class="accordion_content">
            Met een abonnement kunt u:
        </p>
        <ul class="accordion_content">
            <li>uw assistent en/of waarnemend huisarts toegang geven tot socialekaart.care</li>
            <li>een andere praktijk opvoeren en deze gebruiken als centraal punt voor uw zoekopdrachten</li>
            <li>zoeken naar zorgverleners in heel Nederland</li>
            <li>reclame vrij gebruik maken van socialekaart.care</li>
        </ul>
    </section>
    <section>
        <h1>Hoe kan ik een abonnement afsluiten?</h1>
        <p class="accordion_content">In het menu <i>Account</i> kunt u de optie <i>Abonneren</i> kiezen. Het abonnement is geldig voor 1 jaar en de betaling vindt plaats via ideal.</p>
    </section>
    <section>
        <h1>Wordt mijn abonnement automatisch verlengd?</h1>
        <p class="accordion_content">Nee, wij informeren u enkele weken voordat uw abonnement verloopt hierover en u krijgt daarbij de mogelijkheid om het abonnement te verlengen.</p>
    </section>
    <section>
        <h1>Hoe kan ik mijn assistent of waarnemer toevoegen? *</h1>
        <p class="accordion_content">
            Via het menu <i>Account/Gebruikers</i> kunt u deze (en andere gebruikers binnen uw praktijk) toevoegen. U kunt lees- en/of schrijfrechten toekennen. Leesrechten geeft de gebruiker de mogelijkheid zorglocaties te zoeken en gebruik te maken van <i>mijn kaart</i>. Met schrijfrechten heeft de gebruiker de mogelijkheid de gegevens te beheren. Dit houdt in: zorglocaties toevoegen, het toekennen van kenmerken aan de zorglocaties  en punten hieraan toekennen.
        </p>
    </section>
    <section>
        <h1>Hoe zoek ik naar zorgaanbieders?</h1>
        <p class="accordion_content">
            Typ één of meerdere zoektermen in de zoekbalk en klik op het vergrootglas (of druk op Enter). Standaard wordt er gezocht binnen een straal van enkele kilometers vanaf uw praktijk. 
        </p>
    </section>
    <section>
        <h1>Waar zoekt SocialeKaart.care op?</h1>
        <p class="accordion_content">
           SocialeKaart.care zoekt met een geavanceerd algoritme in de beschikbare gegevens van de zorgverlener. De kenmerken die door alle gebruikers van SocialeKaart.care zijn opgegeven worden daarbij ook gebruikt. Hoe hoger zorgverleners in de zoekresultaten verschijnen hoe beter de match. 
        </p>
    </section>
    <section>
        <h1>Hoe zoek ik naar zorglocaties in andere steden/dorpen?</h1>
        <p class="accordion_content">
            Typ de de naam van een stad of dorp achter de zoekterm. De zoekfunctie beperkt zich nu tot de opgegeven locatie.
        </p>
    </section>
    <section>
        <h1>Kan ik een lijst samenstellen van veelgebruikte zorgverleners binnen mijn praktijk?</h1>
        <p class="accordion_content">
            Ja, u kunt uw eigen persoonlijke lijst van zorgverleners samenstellen. In de detailinformatie van de zorgverlener heeft u de mogelijkheid deze toe te voegen aan uw favorietenlijst. Deze lijst wordt <i>mijn kaart</i> genoemd en is alleen benaderbaar voor de gebruikers binnen uw praktijk.
        </p>
    </section>
    <section>
        <h1>Kan ik zoeken binnen mijn favorietenlijst?</h1>
        <p class="accordion_content">
            U kunt ook zoeken binnen uw eigen kaart. Dit doet u door de knop <i>Zoeken binnen uw kaart</i> aan te klikken. De zoekresultaten worden dan beperkt tot de favorieten.
        </p>
    </section>
    <section>
        <h1>Hoe kan ik mijn favorietenlijst in het geheel bekijken?</h1>
        <p class="accordion_content">
            Als u op <i>mijn kaart</i>  klikt in de menubalk dan wordt de favorietenlijst getoond. Vanuit deze lijst kunt u categorieën aanklikken of individuele zorgverleners.
        </p>
    </section>
    <section>
        <h1>Kan ik landelijk zoeken? *</h1>
        <p class="accordion_content">
            Klik op de knop <i>Landelijk zoeken</i> in de menubalk om uw zoekopdracht toe te passen op alle zorgverleners in Nederland. Vanwege praktische redenen zullen de getoonde zoekresultaten worden beperkt tot 500. 
        </p>
    </section>
    <section>
        <h1>Ik werk in verschillende praktijken, hoe gaat het socialekaart.care hiermee om? *</h1>
        <p class="accordion_content">
            Als u werkzaam bent bij meerdere praktijken kunt u deze invoeren. Dit heeft als voordeel dat de zoekfunctie de betreffende praktijk waar u op dat moment werkt als uitgangspunt neemt. Klik in het menu <i>Account</i> op <i>Instellingen</i> om de praktijken op te voeren. U kunt dan vervolgens vanuit de menubalk één van de praktijken selecteren die als centraal punt dient voor de zoekmachine.
        </p>
    </section>
    <section>
        <h1>Waar worden persoonlijke e-mailadressen voor gebruikt?</h1>
        <p class="accordion_content">
            Als u een gebruiker aanmaakt moet u ook een e-mailadres opgeven zodat we deze gebruiker een login link kunnen sturen. Via deze link kan de gebruiker zijn/haar wachtwoord instellen. Ook als uw abonnement verloopt zal deze gebruiker een e-mail krijgen met de de boodschap dat hij/zij niet meer kan inloggen in het systeem.
        </p>
    </section>
    <p style="font-size:10px;">
        * = Deze functionaliteiten zijn door u te gebruiken als u beschikt over een abonnement.
    </p>
</div>
<br />
<?php
$f = drupal_get_form('gojira_help_form');
echo render($f);
?>