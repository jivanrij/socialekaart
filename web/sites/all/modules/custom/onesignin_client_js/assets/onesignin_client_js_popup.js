/**
 * @file
 * js popup function for the Onesignin-client module
 */
(function ($) {
  Drupal.behaviors.onesignin_popup = {
    attach: function(context, settings) {
      // If clicked on the link, open the link url in a popup
      $('.popup-link').click(function(){
        newwindow = window.open($(this).attr('href'), Drupal.t('Login'), 'height=450,width=600,scrollbars=yes');
        if (window.focus) {
          newwindow.focus()
        }
        return false;
      });
    }
  }
})(jQuery);
