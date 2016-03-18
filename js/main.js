/* WordPress Posted Display */
(function ($) {
  'use strict';

  $(function () {
    /*
     * Type item switching
     *
     * @since 1.1.0
     * @see   includes/wp-posted-display-admin-post.php
     */
    var
      displayTypeId = $("#wp-posted-display-type"),
      displayAreaId = {
        "Cookie"    : $("#wp-posted-display-type-cookie"),
        "Posts"     : $("#wp-posted-display-type-posts"),
        "Categories": $("#wp-posted-display-type-categories"),
        "Tags"      : $("#wp-posted-display-type-tags"),
        "Users"     : $("#wp-posted-display-type-users")
      };

    // Initial display
    displayAreaId[displayTypeId.val()].css("display", "block");

    displayTypeId.on("change", function () {
      for (var key in displayAreaId) {
        if (key === $(this).val()) {
          displayAreaId[key].css("display", "block");
        } else {
          displayAreaId[key].css("display", "none");
        }
      }
    });
  });
})(jQuery);