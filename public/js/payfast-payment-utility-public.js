(function ($) {
    'use strict';

    $(document).ready(function () {

        var formElement = $('#payfast-payment-form');
        
        formElement.submit(function (e) {
            var amount = $("#payfast-TXNAMT").val();
            var successUrl = $('#SUCCESS_URL').val();
            successUrl = successUrl + "&txnamt=" + amount;
            $('#SUCCESS_URL').val(successUrl);

        });
    });
})(jQuery);