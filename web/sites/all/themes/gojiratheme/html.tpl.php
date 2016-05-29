<?php if(Template::getView() == 'ajax'): ?>
  <?php print $page; ?>
<?php else: ?>
  <!DOCTYPE html>
  <html lang="nl">
  <head profile="<?php print $grddl_profile; ?>">
    <?php if(Template::getView() == Template::VIEWTYPE_FRONT): ?>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
    <?php endif; ?>
    <?php print $head; ?>
    <title><?php print $head_title; ?></title>
    <?php print $styles; ?>
    <?php print $scripts; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <?php
  if(Template::getView() == Template::VIEWTYPE_FRONT){
    $class = 'front';
  }else{
    $class = 'gojirasearch';
    if(isset($_GET['q'])){
      $class = str_replace('/','',$_GET['q']);
      if($class == 'welcome'){
        $class = 'gojirasearch';
      }
    }
  }

  ?>
  <body class="<?php echo $class; ?> <?php echo Template::getMobileType(); ?>">
      <?php print $page; ?>
      <?php if(Template::getView() == Template::VIEWTYPE_FRONT && helper::getIEVersion() != 8): ?><script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script><?php endif; ?>
        <script>
        var $buoop = {c:2};
        function $buo_f(){
         var e = document.createElement("script");
         e.src = "//browser-update.org/update.min.js";
         document.body.appendChild(e);
        };
        try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
        catch(e){window.attachEvent("onload", $buo_f)}
        </script>
  </body>
  </html>
<?php endif; ?>
