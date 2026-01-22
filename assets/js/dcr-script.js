jQuery(document).ready(function($) {

    $('#ime_dodatni, #prezime_dodatni, #rodenje_dodatni').on('input', function() {
        // Check if any of the fields is not empty
        if ($('#ime_dodatni').val().trim() !== "" || $('#prezime_dodatni').val().trim() !== "" || $('#rodenje_dodatni').val().trim() !== "") {
            // Add class to both
            $('#ime_dodatni, #prezime_dodatni, #rodenje_dodatni').addClass('validate');
        } else {
            // Remove class if both are empty
            $('#ime_dodatni, #prezime_dodatni, #rodenje_dodatni').removeClass('validate');
        }
    });

    $('#razred').change(function(){
        var selectedOption = $(this).find('option:selected');
        var razrednikValue = selectedOption.data('razrednik');
        $('#razrednik').val(razrednikValue);
        $('#dc_razred').val(selectedOption.text())
    });

    $('#vrsta_isprave').change(function(){
        var vrstaSelectedOption = $(this).find('option:selected');
        var vrstaValue = vrstaSelectedOption.data('vrsta_isprave');
        if(vrstaValue == 'nema_putne_isprave') {
            $('#putna_vrijedi_do_putnika, #broj_isprave_putnika').removeClass('validate')
            $('.putna_vrijedi_do_putnika_col, .broj_isprave_putnika_col').hide()

            $('#broj_zdravstvene_putnika').addClass('validate')
            $('.broj_zdravstvene_putnika_col').show()
        } else {
            $('#putna_vrijedi_do_putnika, #broj_isprave_putnika').addClass('validate')
            $('.putna_vrijedi_do_putnika_col, .broj_isprave_putnika_col').show()

            $('#broj_zdravstvene_putnika').removeClass('validate')
            $('.broj_zdravstvene_putnika_col').hide()
        }
    });

    $('#rodenje_putnika, #datum_glavni, #rodenje_dodatni').inputmask('99.99.9999.');
    $('#putna_vrijedi_do_putnika').inputmask('99.99.9999.');
    $('#postanski_putnika, #postanski_glavni').inputmask('99999');
    $('#oib_putnika, #oib_glavni, #oib_dodatni').inputmask('99999999999');
    $("#amount").inputmask({
        alias: 'numeric',
        radixPoint: '.',
        allowMinus: false,
        rightAlign: false,
        numericInput: true,
        placeholder: '0.00',
        groupSeparator: '',
        autoUnmask: true,
        onBeforeWrite: function (event, buffer, caretPos, opts) {
            if (buffer.indexOf('.') !== -1 && buffer.length - buffer.indexOf('.') > 3) {
                return false;
            }
        }
    });

    $('.payment-type').click(function() {
        $('.payment-type').removeClass('set_bg_color');
        $('.payment-title').removeClass('selected');
        $('span.dc-icon-selected').hide();
        $('span.dc-icon-nonselected').show();

        $(this).addClass('set_bg_color');
        $(this).find('span.dc-icon-nonselected').hide();
        $(this).find('span.dc-icon-selected').show();
        $(this).find('.payment-title').addClass('selected');

        recalculate($(this).data('nacin'))
    });

    function recalculate(nacin_placanja)
    {
        $('#nacin_placanja').val($('.set_bg_color').data('nacin'));

        if(nacin_placanja == 3 || nacin_placanja == 5) {
            $('.dc_rate_selector').fadeIn()
            var selectedRate = $('#dc_rate').val();
            if(selectedRate === 'Jednokratno') {
                /*
                // promijenjeno 18.10.2025. - postavljena kartična cijena uvijek kad je kartica odabrana
                var iznos_gotovina = (parseFloat($('.set_bg_color').data('iznos-gotovina'))).toFixed(2);
                var iznos_dodaci = ((parseFloat($('.set_bg_color').data('iznos-gotovina')) + parseFloat($('#totalAdditionals').val()))).toFixed(2);

                $('span.dc_iznos_putovanja').text(iznos_gotovina + ' €');
                */

                var iznos_kartice = (parseFloat($('.set_bg_color').data('iznos-kartice'))).toFixed(2);
                var iznos_dodaci = ((parseFloat($('.set_bg_color').data('iznos-kartice')) + parseFloat($('#totalAdditionals').val()))).toFixed(2);

                $('span.dc_iznos_putovanja').text(iznos_kartice + ' €');
                $('span.dc_za_naplatu').text(iznos_dodaci + ' €');
                $('span.dc_iznos_sveukupno').text(iznos_dodaci + ' €');
                $('#ukupni_iznos_putovanja').val(iznos_dodaci);
                $('#nacin_placanja').val(3);
            } else {
                var iznos_kartice = parseFloat($('.set_bg_color').data('iznos-kartice')).toFixed(2)
                var iznos_dodaci = (parseFloat($('.set_bg_color').data('iznos-kartice')) + parseFloat($('#totalAdditionals').val())).toFixed(2);

                $('span.dc_iznos_putovanja').text(iznos_kartice + ' €');
                $('span.dc_za_naplatu').text(iznos_dodaci + ' €');
                $('span.dc_iznos_sveukupno').text(iznos_dodaci + ' €');
                $('#ukupni_iznos_putovanja').val(iznos_dodaci);
                $('#nacin_placanja').val(5);
            }
        } else {

            var iznos_putovanja = parseFloat($('.set_bg_color').data('iznos-putovanja')).toFixed(2);
            var sveukupno = (parseFloat($('.set_bg_color').data('iznos-putovanja')) + parseFloat($('#totalAdditionals').val())).toFixed(2);

            $('.dc_rate_selector').fadeOut()
            $('span.dc_iznos_putovanja').text(iznos_putovanja + ' €');
            $('span.dc_za_naplatu').text($('.set_bg_color').data('za-naplatu') + ' €');
            $('span.dc_iznos_sveukupno').text(sveukupno + ' €');
            $('#ukupni_iznos_putovanja').val(iznos_putovanja);
        }
    }

    $('.dc-chck-checkbox').on('change', function () {
        var totalAdditionals = calculateTotalAmount();
        $('#totalAdditionals').val(totalAdditionals);
        $('.dc_iznos_dodataka').text('+ ' + totalAdditionals + ' €');

        recalculate($('#nacin_placanja').val())
    });

    function calculateTotalAmount() {
        var total = 0;
        var totalPassengers = $('#totalPassengers').val();

        $('.dc-chck-checkbox:checked').each(function () {
            total += parseFloat($(this).data('amount'));
        });

        return (total * totalPassengers).toFixed(2); // Return the total here
    }

    $('#dc_kartica, #dc_rate').change(function(){
        recalculate(3)
    });

    function validateInput() {
        var isValid = true;
        $('input').removeClass('input-error');

        $('.validate').each(function () {
            if ($(this).is('select')) {
                // Validate dropdowns
                if ($(this).val() === '' || parseInt($(this).val()) <= 0) {
                    $(this).addClass('input-error');
                    isValid = false;
                } else {
                    $(this).removeClass('input-error');
                }
            } else if ($(this).is(':checkbox')) {
                // Validate checkboxes
                if (!$(this).prop('checked')) {
                    $('label[for="' + $(this).attr('id') + '"]').addClass('label-error');
                    isValid = false;
                } else {
                    $('label[for="' + $(this).attr('id') + '"]').removeClass('label-error');
                }
            } else {
                // Validate text inputs
                if ($(this).val().trim() === '') {
                    $(this).addClass('input-error');
                    isValid = false;
                } else {
                    $(this).removeClass('input-error');
                }
            }
        });

        return isValid;
    }

// Validate on Button Click
    $('body').on('click', '#reservation_button', function () {
        $('.mandatory').hide(); // Hide warning by default
        if (validateInput()) {
            $('#reservation_form').submit(); // Proceed with submission
        } else {
            $('.mandatory').fadeIn(); // Show error message
        }
    });


    $('body').on('click', '#reservation_button', function() {
        $('.mandatory_scroll').hide();
        if (validateInput()) {
            $('#reservation_form').submit();
        } else {
            $('.mandatory').fadeIn();
        }
    });

    $('body').on('click', '#reservation_button_disabled', function() {
        $('.mandatory_scroll').fadeIn();
    });


    var scrollbox = $(".dc-scrollbox");
    var button = $("#reservation_button");

    scrollbox.on("scroll", function () {
        var scrollTop = $(this).scrollTop();
        var innerHeight = $(this).innerHeight();
        var scrollHeight = $(this)[0].scrollHeight - 5;

        // Check if scrolled to bottom
        if (scrollTop + innerHeight >= scrollHeight) {
            $('#reservation_button_disabled').attr('id', 'reservation_button')
        } else {
            //alert('test')
        }
    });

    $('#dc_payment_form').submit(function(event) {
        // Prevent form submission
        event.preventDefault();

        // Validate input fields
        if (validateInput()) {
            // If all input fields are valid, submit the form
            this.submit();
        } else {
            // Display a message or perform any other action for invalid form
            console.log('Form is invalid. Please fill in all required fields.');
        }
    });

    // Define a mapping of kartica values to their corresponding options for rate
    var karticaOptions = {
        'VISA': ['Jednokratno', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
        'DINERS': ['Jednokratno', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
        'AMEX': ['Jednokratno'],
        'MASTERCARD': ['Jednokratno', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
        'MAESTRO': ['Jednokratno'],
        'MAESTRO (Erste)': ['Jednokratno', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12']
    };

    // Function to update options of #dc_rate based on selected #dc_kartica
    $('#dc_kartica').change(function(){
        var selectedKartica = $(this).val();
        var options = karticaOptions[selectedKartica];

        // Clear existing options
        $('#dc_rate').empty();

        // Add new options
        $.each(options, function(index, value){
            $('#dc_rate').append($('<option>').text(value).attr('value', value));
        });
    });

});

document.addEventListener('DOMContentLoaded', function() {

    var emailInput1 = document.getElementById('email_ugovaratelja');
    applyEmailValidation(emailInput1);

    var emailInput2 = document.getElementById('email_putnika');
    applyEmailValidation(emailInput2);

    function applyEmailValidation(inputElement) {
        inputElement.addEventListener('input', function() {
            var inputValue = this.value.trim();
            if (isValidEmail(inputValue)) {
                this.style.borderColor = ''; // Valid email format
            } else {
                this.style.borderColor = 'red'; // Invalid email format
            }
        });
    }

    function isValidEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});