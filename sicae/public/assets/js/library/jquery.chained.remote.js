/*
 * Remote Chained - jQuery AJAX(J) chained selects plugin
 *
 * Copyright (c) 2010-2011 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 */

(function($) {

    $.fn.remoteChained = function(options) {

        return this.each(function() {

            /* Save this to self because this changes when scope changes. */
            var self   = this;
            var backup = $(self).clone();

            var settings = $.extend({
                parent:null,
                url:null,
                after:null,
                include_blank: true,
                disable_if_default: false,
                default_value: ""
            }, options||{})  ;

            /* Handles maximum two parents now. */
            $(settings.parent).each(function() {
                $(this).bind("change", function() {

                    /* Build data array from parents values. */
                    var data = {};
                    $(settings.parent).each(function() {
                        var id = $(this).attr("id");
                        var value = $(":selected", this).val();
                        data[id] = value;
                    });

                    $.getJSON(settings.url, data, function(json) {

                        /* Clear the select. */
                        $("option", self).remove();

                        /* Add new options from json. */
                        for (var index in json) {
                            if (!json.hasOwnProperty(index)) {
                                continue;
                            }

                            /* This sets the default selected. */
                            if ("selected" == index) {
                                continue;
                            }

                            var option = $("<option />").val(index).append(json[index]);

                            $(self).append(option); 


                        }

                        /* Loop option again to set selected. IE needed this... */
                        $(self).children().each(function() {
                            if ($(this).val() == settings["default_value"]) {
                                $(this).attr("selected", "selected");
                            }
                        });

                        /* If we have only the default value disable select. */
                        if (1 == $("option", self).size() && $(self).val() === "" && settings.disable_if_default == true) {
                            $(self).attr("disabled", "disabled");
                        } else {
                            $(self).removeAttr("disabled");
                        }

                        /* Force updating the children.
                        // Not really necessary, I think.
                        $(self).trigger("change");
                        console.debug(self.id + ' triggered')
                        */
                        if (settings.after)
                        {
                            settings.after(self);
                        }

                    });
                });

                /* Force updating the children. */
//                $(this).trigger("change");
            });
        });
    };

    /* Alias for those who like to use more English like syntax. */
    $.fn.remoteChainedTo = $.fn.remoteChained;

})(jQuery);
