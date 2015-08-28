/**
 * @file
 * JS function closing the popup and refresh the parent page
 */
(function ($) {
  Drupal.behaviors.onesignin_ajaxcall = {
    attach: function(context, settings) {
      // Close the popup
      self.close();
      // Refresh the parent page
      if (window.opener && !window.opener.closed) {
        window.opener.location.reload();
      }

      return false;
    }
  }
})(jQuery);
