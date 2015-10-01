<h1>Documentatie</h1>

<h2>Gebruikers, Rollen Groepen & Praktijken</h2>
<p>
    <img src="/sites/all/themes/gojiratheme/img/datagojira.png" alt="/sites/all/themes/gojiratheme/img/datagojira.png">
</p>
<p>
    Alle bovenstaande objecten zijn nodes.<br />
    <b>Groep</b>, is het object wat gebruikers & praktijken aan elkaar verbind:
    <ul>
        <li>Titel, de titel van de groep - geen technische waarde</li>
        <li>Master user - Een groep wordt aangemaakt bij registratie van een huisarts. Dit is een link naar deze huisarts.</li>
        <li>Betaalde groep - Een flag die aangeeft dat een groep op dit moment een abonnement heeft. Als deze aan staat MOET de groep ook payment info hebben in de tabel gojira_payments.</li>
    </ul>
    <b>Gebruikers</b>, alle gebruikers in het systeem. Opgeslagen in de Users tabel. Users hebben naast de standaard informatie de volgende gegevens:
    <ul>
        <li>Is doctor-  of de gebruiker bij registratie heeft aangegeven dat hij huisarts is.</li>
        <li>Search in favorites - gebruiker zijn zoeken is gelimiteerd op zijn kaart (kan gebruiker zelf aanpassen);</li>
        <li>Multiple locations - gebruiker wil meerdere locaties hebben (kan gebruiker zelf aanpassen als hij een abonnement heeft);</li>
        <li>Akkoord op conditions - gebruiker is akkoort gegaan op de condities na registratie;</li>
        <li>Search global - gebruiker wil landelijk zoeken (kan hij zelf aanpassen);</li>
        <li>User has seen Tutorial - gebruiker heeft tutorial gezien;</li>
        <li>Gebruiker is niet geimporteerd - gebruiker heeft zich zelf geregistreerd, dus is niet binnengekomen via HAweb.</li>
    </ul>
    <b>Praktijken/Locaties/Zorgverleners</b>, representeerd een zorgverlener of huisarts in het systeem. Naast standaard adresgegevens bevat dit object de volgende velden:
    <ul>
        <li>Location group - link naar de groep die gebruikt wordt als het een huisartsenpraktijk is;</li>
        <li>Visible - geeft aan of een locatie te vinden is in het systeem, huisartspraktijken hebben hier 0 staan;</li>
        <li>Labels - de labels van een locatie, in de tabel <i>group_location_term</i> houden we bij hoeveel punten het label per groep heeft;</li>
        <li>Category - de categorie van de locatie, als de catagorie Huisarts is zal visible op 0 gezet worden.</li>
    </ul>
</p>
<p>
    Verschillende rollen (naast de standaard) zijn:
    <ul>
        <li>gojira employee (subscribed) - gebruiker aangemaakt door huisarts met weinig rechten;</li>
        <li>gojira employer (subscribed) - gebruiker aangemaakt door huisarts met veel rechten;</li>
        <li>gojira master employer - de huisarts;</li>
        <li>gojira master employer (subscribed) - de huisarts met abonnement.</li>
    </ul>
</p>
<p>
    <img src="/sites/all/themes/gojiratheme/img/rechten.png" alt="/sites/all/themes/gojiratheme/img/rechten.png">
</p>

<h2>E-mails die verzonden worden</h2>
<p>
    <b>sendInvoiceOfNewSubscription</b> heeft de titel 'Invoice SocialeKaart.care - [INVOICE_NUMBER]'. Bij het aanmaken van een nieuwe abonnementsperiode door een huisarts 
    wordt deze e-mail verzonden. Hier bij krijgt de huisarts ook direct de factuur. Deze e-mail wordt verzonden door de actie subscribe in de tools page & op de return page van iDeal als er een geslaagde betaling is geweest.
</p>
<p>
    <b>sendWelcomeMailToEmployee</b> en <b>sendWelcomeMailToEmployer</b> hebben de titel 'SocialeKaart.care account created by [DOCTOR_NAAM]' en worden verzonden aan de nieuw aangemaakte gebruikers door de ingelogde huisarts.
</p>
<p>
    <b>sendUnsubscribeMail</b> verstuurd een e-mail met de titel 'Your account on SocialeKaart.care is deactivated' wanneer een account van een gebruiker (tenzij het de huisarts zelf is) wordt ge-deactiveerd.
</p>
<p>
    <b>sendSubscribeActivationMail</b> heeft de titel 'Your account on SocialeKaart.care is activated' en wordt verstuurd naar de gebruikers van een groep, exclusief de huisarts, bij het aanmaken van een nieuw abonnements periode.
</p>
<p>
    <b>sendSubscriptionEndWarning</b> wordt verstuurd naar de huisarts 30 dagen voor de huisarts zijn laatst genomen abonnement vervalt. Deze e-mail heeft de titel 'Your subscription on SocialeKaart.care is going to expire within 30 days'. Deze e-mail wordt verzonden vanuit de cron.
</p>
<p>
    <b>sendSubscriptionEnded</b> deze e-mail word verstuurd naar een huisarts als zijn laatst genomen abonnement afgelopen is. Deze e-mail heeft de titel 'Your subscription on SocialeKaart.care is expired' en wordt verzonden vanuit de cron.
</p>
<p>
    <b>sendQuestion</b> deze e-mail wordt verzonden naar de admin van het systeem als er een gebruiker een vraag heeft gesteld via de help pagina. Het e-mailadres van de admin is in de settings pagina in te stellen.
</p>
<p>
    <b>sendImproveSuggestion</b> wordt verzonden naar de admin van het systeem als een gebruiker via een locatiezoekresultaat een verbetersuggestie heeft opgegeven.
</p>
<p>
    <b>sendLocationAddedByUserToAdmin</b> wordt verzonden naar de admin met de informatie van een locatie die een gebruiker heeft toegevoegd aan het systeem.
</p>
<p>
    Als er iemand via HAweb voor het eerst probeerd in te loggen en er is al een account in het systeem met hetzelfde e-mailadres wordt <b>sendDoubleAccountWarning</b> verstuurd. Hierin wordt uitgelegd wat de gebruiker moet doen om de SSO werkend te krijgen.
</p>
<p>
    Na een handmatige registratie van een nieuwe gebruiker/doctor zal de e-mail <b>sendAccountNeedsValidation</b> worden verzonden naar de admin. Hierin staat wat de admin moet doen om de account te activeren/controlleren.
</p>
<p>
    <b>newAccountThroughSSO</b> Na de eerste keer dat iemand inlogt via de SSO met HAweb wordt deze e-mail verstuurd naar de gebruiker.
</p>
<p>
    <b>accountActivatedByAdmin</b> wordt verstuurd naar een account die geactiveerd is door de admin via de tools pagina. Wordt meestal gebruikt voor accounts die zich handmatig hebben geregistreerd.
</p>
<p>
    <b>locationWithoutCoordinatesAdded</b> wordt verstuurd naar de admin van het systeem als er een gebruiker is geweest die een locatie of praktijk heeft toegevoegd waar geen coordinaten van zijn gevonden.
</p>
<p>
    <b>checkSubscriptionFail</b> wordt verstuurd naar de admin als blijkt dat er een group is waarop is aangegeven dat ze betaald zijn maar geen betaal informatie van te vinden is.
</p>
<p>
    <b>informAdminAboutDoubleLocations</b> wordt verstuurd naar de admin als een gebruiker ergens dubbele locaties ziet. De admin krijgt dan hier het lijstje van.
</p>

<h2>SSO registratie</h2>
<p>
    Bij acceptatie van de algemene voorwaarden krijgt de gebruiker 3 maanden abonnements voordeel. In dit geval worden er geen activatie e-mails verstuurd naar de gebruikers van de groep.
</p>
<p>
    Als iemand in zowel HAweb als in Sociale Kaart een account heeft met hetzelfde e-mailadres kan hij/zij niet inloggen via HAweb. In dit geval krijgt deze gebruiker op dit e-mailadres een e-mail met de uitleg wat de situatie is. Ook wordt er in de database een flag gezet op het veldje users.haweb_sso_dublicate_warning_send om aan te geven dat deze mail is verzonden. Met deze flag aan zal deze mail nooit meer verzonden worden.
</p>
<p>
    Gebruikers krijgen via HAweb een account in Sociale Kaart. Deze account kan nog niet veel van zichzelf omdat deze nog niet de benodigde rechten heeft. Pas als de gebruiker akkoort gaat met de algemene voorwaarden wordt de account voorzien van alle benodigde rechten via rollen. Ook wordt er dan een flag gezet in de tabel users.haweb_sso_setup_done. Als deze flag gezet is zal het systeem nooit meer automatisch de user voorzien van deze rechten.
</p>

<h2>Verbetersuggesties & andere input van de gebruikers</h2>
<p>
    Vanuit een zoekresultaat kan een gebruiker ervoor kiezen om naar het 'verbetersuggestie' formulier te gaan. Vanaf dit formulier kan de gebruiker een verbetersuggestie sturen naar de admin's van het systeem. Deze suggesties kunnen van verschillende soorten zijn, een daarvan is het corrigeren van de titel van een locatie. Als de gebruiker hiervoor kiest zal de titel ook direct vervangen worden.
</p>
<p>
    Via het formulier 'Voeg een zorgverlener toe' kan een gebruiker met genoeg rechten (doctor & employee) een locatie van een zorgverlener toevoegen. Er wordt dan wel een check gedaan of er niets wordt toegevoegd op een bekende plaats (gebaseerd op de coordinaten). Als dit het geval is wordt er gevraagd of de gebruiker het zeker weet en wordt de locatie(s) die al bekend zijn weergegeven. Daarbij krijgt de gebruiker ook een directe link naar het verbetersuggestie formulier van de al bestaande locaties.
</p>

<h2>Abonnement</h2>
<p>
    Een abonnement duurt 1 jaar, deze periode is wel instelbaar in de configuratie pagina. 30 dagen voor het vervallen van de abonnementsperiode krijgt de huisarts een waarschuwingsmail. Tijdens een abonnementsperiode kan een huisarts alvast een volgende periode kopen. Bij het vervallen van een abonnement worden de accounts van de (niet huisarts) gebruikers gedeactiveerd. Bij heractivering worden deze weer geactiveerd.
</p>
<p>
    Een groep of doctor kan zich in verschillende statussen bevinden in relatie tot het abonnement.
<ul>
    <li>Geen abonnement (kan extra jaar afsluiten);</li>
    <li>Wel een abonnement, zonder verlenging (kan extra jaar afsluiten);</li>
    <li>Wel een abonnement, met verlenging (kan geen extra jaar afsluiten).</li>
</ul>
Een doctor met abonnement zonder verlenging (een extra betaalde periode die nog niet is aangebroken maar wel betaald) kan 1x voor 1 extra jaar aan abonnement afsluiten. Hij kan dit maar 1x doen, dus voor maximaal 1 extra jaar. De teksten op de site die gerelateerd zijn aan de abonnementen verschillen ook per status van de groep/doctor.
</p>
<p>
    Elke nacht wordt via de <a href="https://www.drupal.org/documentation/modules/system">cron</a> gecontrolleerd of abonnementen niet verlopen zijn of binnenkort gaan verlopen. In beide gevallen worden de gebruikers op de hoogte gesteld via een e-mail.
</p>
<h2>
    iDeal
</h2>
<p>
    De gebruiker kan een abonnement afnemen door middel van een iDeal betaling. Deze betaling zal voltrokken worden zodra de gebruiker vanuit iDeal terugkomt op de idealreturn pagina. Als hier iets verkeerd gaat zal er nog vanuit iDeal een callback call gedaan worden. Hier schrijft Easy iDeal het volgende over: <i>Om er zeker van te zijn dat u de correcte betaalstatus ontvangt kunt u een callback URL (beginnend met http:// of https://) opgeven in de Qantani backoffice onder “Instellingen”. Deze URL wordt 5, 10, 30, 60, 120 en 300 minuten na het starten van de transactie aangeroepen. De Query string zal automatisch worden toegevoegd aan de door u opgegeven callback URL. De callback URL wordt alleen aangeroepen wanneer de betaalstatus definitief is.</i>. Er is een <a href='/?q=admin/config/system/idealreport' title="iDeal rapportage pagina">iDeal rapportage pagina</a> om de gegevens uit te lezen van alle transacties.
</p>
<h2>Nieuwsbrief</h2>
<p>
    Bij het akkoort gaan met de Algemene Voorwaarden krijgt de gebruiker ook de mogelijkheid om zich te abonneren op de Mailchimp nieuwsbrief. Daarnaast is er een registratie formulier op de /nieuwsbrief pagina. Verder zit alles in Mailchimp.
</p>
<h2>
    Lokaal, Regionaal & Landelijk zoeken
</h2>
<p>
    Gebruikers zonder abonnement kunnen alleen in een radius van hun praktijk zoeken. Als zij dan bijvoorbeeld zoeken op 'tandarts zwolle' krijgen ze de resultaten vanuit hun eigen omgeving. Wel krijgen ze dan de tekst 'Alleen met een abonnement is het mogelijk specifiek in andere dorpen en steden te zoeken.' te zien.
</p>
<p>
    Gebruikers met abonnement kunnen altijd een naam van een stad of dorp megeven in de zoektermen. Het zoekresultaat zal zich dan altijd limiteren op de zorgverleners in het betreffende stadje/dorp. Wel krijgt de gebruiker alsnog de mogelijkheid om lokaal te zoeken met de op gegeven zoektermen. Het is dus nog mogelijk om in oud-beijerland te zoeken op 'zwolle tandarts'.
</p>
<style>
    h2{
        font-size:14px;
        margin-top:25px;
    }
</style>
