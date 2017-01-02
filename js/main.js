/* WordPress Posted Display */
(function ($) {
  "use strict";

  $(function () {
    /**
     * Type item switching
     *
     * @version 2.0.0
     * @since   1.1.0
     * @see     includes/wp-posted-display-admin-post.php
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

    displayTypeId.on("change", function () {
      var displayFlag = false;
      for (var key in displayAreaId) {
        if (key == $(this).val()) {
          displayFlag = true;
          displayAreaId[key].css("display", "block");
        } else {
          displayAreaId[key].css("display", "none");
        }
      }
      if ( !displayFlag ) {
        displayAreaId["Posts"].css("display", "block");
      }
    });
    displayTypeId.trigger("change");

    /**
     * Textarea insert items
     *
     * @since 1.1.0
     * @param item
     * @see   includes/wp-posted-display-admin-post.php
     */
    function insertItems (item) {
      var obj = $("#template");

      obj.focus();

      var
        data = obj.val(),
        cursor = obj.get(0).selectionStart,
        np = cursor + item.length;

      obj.val(data.substr(0, cursor) + item + data.substr(cursor));
      obj.get(0).setSelectionRange(np, np);
    }
    $("#template_item").on("click", "span", function (e) {
      e.preventDefault();
      insertItems($(this).text());
    });
  });
})(jQuery);