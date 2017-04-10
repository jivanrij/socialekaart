<h1><?php echo t('Password successfully changed.'); ?></h1>
<p>
  <?php echo t('You have successfully set your password.'); ?>
</p>
<p>
  <?php if(count(Location::getUsersLocations(true))==0): ?>
    <p>Bij het registreren heeft u een adres opgegeven. Helaas hebben we dit adres niet kunnen vinden op de kaart.</p>
    <p><span>Om gebruik te maken van de zoekfunctie is het nodig om uw eigen praktijk in te voeren. Deze zal dienen als middelpunt voor zoekacties, tenzij u zoekt in een specifiek opgegeven stad of dorp.</span></p>
    <p>U kunt via de pagina <i><a href="/settings">instellingen</a></i> een goed adres opgeven.</p>
  <?php endif; ?>
</p>