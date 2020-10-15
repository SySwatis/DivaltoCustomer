define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
    ], function($){
        'use strict';
        return function() {
            $.validator.addMethod(
                "siret",
                function(value, element) {
                    // Siret au format 254 851 369 54218
                    var str =value;
                    var patt = new RegExp("[0-9]{3}[ \.\-]?[0-9]{3}[ \.\-]?[0-9]{3}[ \.\-]?[0-9]{5}");
                    return patt.test(str);
                },
                $.mage.__("Please enter a valid Siret")
            );
            $.validator.addMethod(
                "ape",
                function(value, element) {
                    // Code APE au format 52.4Z
                    var str =value;
                    var patt = new RegExp("[0-9]{2}[ \.\-]?[0-9]{1} ?[a-zA-Z]");
                    return patt.test(str);
                },
                $.mage.__("Please enter a valid Ape")
            );
    }
});