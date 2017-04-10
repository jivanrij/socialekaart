<div>
    <h1><?php echo t('FAQ'); ?></h1>
    <p>
        Welkom op onze FAQ pagina. Als u klikt op één van de vragen verschijnt het antwoord eronder. Als u geen antwoord kunt vinden op uw vraag kunt u ons uw vraag e-mailen. Onder de pagina is een formulier te vinden waarmee u dit kunt doen. Wij proberen u dan zo snel mogelijk te helpen.
    </p>
    <hr />
    <div id="faq_sections">
        <section>
            <h1>Hoe zoek ik naar zorgverleners?</h1>
            <p class="accordion_content">
                Typ één of meerdere zoektermen in de zoekbalk en klik op het vergrootglas (of druk op Enter). Standaard wordt er gezocht binnen een straal van enkele kilometers vanaf uw praktijk.<br />
                Als u meerdere zoektermen opgeeft zult u alleen zorgverleners krijgen die gevonden worden op beide zoektermen.
            </p>
        </section>
        <section>
            <h1>Waar zoekt SocialeKaart.care op?</h1>
            <p class="accordion_content">
                SocialeKaart.care zoekt met een geavanceerd algoritme in de beschikbare gegevens van de zorgverlener. De kenmerken die door alle gebruikers van SocialeKaart.care zijn opgegeven worden daarbij ook gebruikt.
            </p>
        </section>
        <section>
            <h1>Hoe zoek ik naar zorgverleners in andere steden/dorpen?</h1>
            <p class="accordion_content">
                Een naam van een stad of dorp kan ook als zoekterm gebruikt worden. Als u bijvoorbeeld een fysiotherapeut in Zwolle zoekt kunt u zoeken op:"fysiotherapie zwolle".
            </p>
        </section>
        <section>
            <h1>Kan ik een lijst samenstellen van veelgebruikte zorgverleners binnen mijn praktijk?</h1>
            <p class="accordion_content">
                Ja dit noemen we binnen het systeem uw sociale kaart. Als u een zorgverlener hebt gevonden kunt in het raampje met de gegevens van de zorgverlener het vinkje <i>In mijn kaart</i> aanvinken. Deze zorgverlener zal dan voortaan op uw eigen sociale kaart verschijnen.
            </p>
        </section>
        <section>
            <h1>Kan ik zoeken binnen mijn sociale kaart?</h1>
            <p class="accordion_content">
                Door in de rode balk op <i>Mijn sociale kaart te klikken. Daar krijgt u een apart zoek formulier om te zoeken binnen uw eigen sociale kaart.</i>
            </p>
        </section>
        <section>
            <h1>Hoe kan ik <i>mijn sociale kaart</i> in het geheel bekijken?</h1>
            <p class="accordion_content">
                Als u op <i>Mijn sociale kaart</i> klikt dan wordt uw sociale kaart getoond. Vanuit deze lijst kunt u categorieën aanklikken of individuele zorgverleners. Ook kunt u vanuit deze weergave direct via het standaard zoekformulier uw sociale kaart doorzoeken.
            </p>
        </section>
        <section>
            <h1>Hoe kan ik landelijk zoeken?</h1>
            <p class="accordion_content">
                Bij het zoeken krijgt u altijd onder de zoekresultaten de optie om te wisselen tussen het zoeken in uw regio of in het gehele land.
            </p>
        </section>
        <section>
            <h1>Ik werk in verschillende praktijken, hoe gaat het socialekaart.care hiermee om? *</h1>
            <p class="accordion_content">
                Als u werkzaam bent bij meerdere praktijken kunt u deze opvoeren. Dit heeft als voordeel dat de zoekfunctie de betreffende praktijk waar u op dat moment werkt als uitgangspunt neemt. Klik in het menu <i>Account</i> op <i>Instellingen</i> om de praktijken op te voeren. Als u dit gedaan heeft kunt u vervolgens vanuit de menubalk één van de praktijken selecteren die als centraal punt dient voor de zoekmachine.
                <br />
                <br />
                Ook zullen de zorgverleners die u toevoegd aan uw sociale kaart allee weergegeven worden als u de praktijk hebt geselecteerd van waar u ze hebt toegevoegd. U kunt zo per praktijk een aparte eigen sociale kaart beheren.
            </p>
        </section>
        <section>
            <h1>Waar worden e-mailadressen voor gebruikt?</h1>
            <p class="accordion_content">
                E-mailadressen die u op een zorgverlener opslaat worden weergegeven bij de zoekresultaten.
            </p>
        </section>
        <section>
            <h1>Wie kan de notitie zien die ik toevoeg aan een zorgverlener?</h1>
            <p class="accordion_content">
                Alleen u.
            </p>
        </section>
        <section>
            <h1>Wat moet ik doen als er iets met de betaling van mijn abonnement verkeerd is gegaan?</h1>
            <p class="accordion_content">
                Heeft u nog wel een succesvolle betaling kunnen doen en ging er daarna iets mis? Zo ja, wacht nog 2 uur, binnen deze tijd zal uw betaling waarschijnlijk alsnog worden afgerond binnen socialekaart.care. Als dit niet gebeurd of er ging iets anders mis neem dan contact met ons op via <a href="mailto:info@socialekaart.care">info@socialekaart.care</a>.
            </p>
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
            <h1>Welke voornaamste voordelen heeft een abonnement voor mij?</h1>
            <p class="accordion_content">
                Met een abonnement kunt u:
            </p>
            <ul class="accordion_content">
                <li>meerdere praktijken opvoeren om vanuit te werken</li>
                <li>uw eigen sociale kaart beheren</li>
            </ul>
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
</div>
