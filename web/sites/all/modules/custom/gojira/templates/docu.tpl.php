<h1>Documentatie</h1>

<h2>Gebruikers, Rollen Groepen & Praktijken</h2>
<p>
    <img src="/sites/all/themes/gojiratheme/img/datagojira.png" alt="/sites/all/themes/gojiratheme/img/datagojira.png">
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

<h2>SSO registratie</h2>
<p>
    Bij acceptatie van de algemene voorwaarden krijgt de gebruiker 3 maanden abonnements voordeel. In dit geval worden er geen activatie e-mails verstuurd naar de gebruikers van de groep.
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
<style>
    h2{
        font-size:14px;
        margin-top:25px;
    }
</style>
