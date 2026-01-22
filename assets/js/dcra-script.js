jQuery(document).ready(function($) {

    /*$(document).on("keydown", "input[type='text'], input[type='email']", function (e) {
        if (e.key === "Enter") {
            e.preventDefault(); // Prevent default action
        }
    });*/

    $('.dc_clone_row').hide();

    // Add new row when the button is clicked
    $('.dc_add_new_row').on('click', function() {
        // Clone, show the new row, and remove the template class
        let newRow = $('.dc_clone_row').clone().removeClass('dc_clone_row').show();

        newRow.find('input').val('');

        // Append after the current row
        $(this).closest('tr').after(newRow);

        // Reapply input mask to new inputs
        applyInputMask(newRow.find('.dc_iznos'));
    });

    $(document).on("click", ".dc_send_invoice", function () {
        var email = $(this).siblings(".dc-email-input").val();
        var reservation_id = $(this).siblings(".dc-email-input").data('reservation-id');
        var invoice_number = $(this).siblings(".dc-email-input").data('invoice-number');

        if (email == '') {
            $(this).siblings(".dc-email-input").css('border-color', 'red');
            return; // Stop execution if validation fails
        }

        if (confirm("Jeste li sigurni da želite poslati račun " + invoice_number + " na email adresu " + email + "?")) {

            // Create a form element dynamically
            var form = $('<form action="#" method="post"></form>');

            // Append input fields to the form
            form.append('<input type="hidden" name="invoice" value="send_to_mail">');
            form.append('<input type="hidden" name="email" value="' + email + '">');
            form.append('<input type="hidden" name="reservation_id" value="' + reservation_id + '">');
            form.append('<input type="hidden" name="invoice_number" value="' + invoice_number + '">');

            // Append form to body and submit
            $('body').append(form);
            form.submit();

        } else {
            return false;
        }

    });

    $(".dc-send-button").click(function () {
        var row = $(this).closest("tr"); // Get the closest <tr>

        // Check if the next row is already the email input row
        if (row.next().hasClass("dc-email-row")) {
            row.next().remove(); // Remove the row
        } else {
            // Create a new row with the email input field
            var invoiceNumber = row.data("invoice-number");
            var reservationID = row.data("reservation-id");
            var emailRow = `
                <tr class="dc-email-row">
                    <td colspan="4">
                        <input type="email" class="dc-email-input" data-reservation-id="${reservationID}" data-invoice-number="${invoiceNumber}" placeholder="Upišite email adresu za slanje ${invoiceNumber}" style="width: 50%;"/>
                        <span class="page-title-action dc_send_invoice">Pošalji račun ${invoiceNumber}</span>
                    </td>
                </tr>
            `;
            row.after(emailRow); // Insert after the current row
        }
    });

    function storno_invoice(invoice_id, payment_id) {
        if (confirm("Jeste li sigurni da želite stornirati račun " + invoice_id + "?")) {
            var form = $('<form action="#" method="post"></form>');

            // Append input fields to the form
            form.append('<input type="hidden" name="invoice" value="storno_invoice">');
            form.append('<input type="hidden" name="invoice_id" value="' + invoice_id + '">');
            form.append('<input type="hidden" name="payment_id" value="' + payment_id + '">');

            // Append form to body and submit
            $('body').append(form);
            form.submit();
            return true;
        } else {
            return false;
        }
    }

    $('#storno_invoices').click(function() {
        $('.dc-storno-col').toggle();
    })

    $(document).on("click", ".storno-btn", function() {
        var invoiceNumber = $(this).closest('.dc-inv').data('invoice-number');
        var paymentID = $(this).closest('.dc-inv').data('payment-id');
        return storno_invoice(invoiceNumber, paymentID);
    });

    $('.remove_payment').click(function() {
        const $span = $(this);
        const paymentId = $span.data('payment-id');
        const reservation_id = $span.data('reservation-id');
        const amount = $span.data('amount');

        if (confirm("Jeste li sigurni da želite izbrisati uplatu u iznosu od " + amount + " za rezervaciju " + reservation_id + "? NAPOMENA: Ova funkcija samo uklanja uplatu iz prikaza!")) {
            var form = $('<form action="#" method="post"></form>');

            // Append input fields to the form
            form.append('<input type="hidden" name="payments" value="remove_payment">');
            form.append('<input type="hidden" name="payment_id" value="' + paymentId + '">');
            form.append('<input type="hidden" name="remove_reservation_id" value="' + reservation_id + '">');
            form.append('<input type="hidden" name="remove_amount" value="' + amount + '">');

            // Append form to body and submit
            $('body').append(form);
            form.submit();
            return true;
        } else {
            return false;
        }
    })

    $('#document_type').change(function(){
        var selected = $(this).val();
        if(selected == 'offer_invoice') {
            $('#send_mail').prop('disabled', false);
            $('#methodOfPayment').prop('disabled', false);
        } else {
            $('#send_mail').prop('disabled', true);
            $('#methodOfPayment').prop('disabled', true);
        }
    });

    $('.submit_offer_invoice').click(function(e) {
        e.preventDefault(); // Prevent default action

        // Get values from input and select
        var ukupniIznos = parseFloat($('#ukupni_iznos').val()); // Convert to number
        var methodOfPayment = $('#methodOfPayment').val();
        var ukupni_iznos_putovanja = $('#ukupni_iznos_putovanja').val();
        var document_type = $('#document_type').val();
        var invoice_send_email = $('#send_mail').is(':checked') ? 1 : 0;

        // Reset previous error styling
        $('#ukupni_iznos').css('border-color', '');

        // Validation: Check if ukupniIznos is a valid number and greater than 0
        if (isNaN(ukupniIznos) || ukupniIznos <= 0) {
            $('#ukupni_iznos').css('border-color', 'red');
            return; // Stop execution if validation fails
        }

        // Create a form element dynamically
        var form = $('<form action="#" method="post"></form>');

        // Append input fields to the form
        form.append('<input type="hidden" name="eracuni_new_payment" value="offer_invoice">');
        form.append('<input type="hidden" name="ukupni_iznos_putovanja" value="' + ukupni_iznos_putovanja + '">');
        form.append('<input type="hidden" name="ukupni_iznos" value="' + ukupniIznos + '">');
        form.append('<input type="hidden" name="methodOfPayment" value="' + methodOfPayment + '">');
        form.append('<input type="hidden" name="send_mail" value="' + invoice_send_email + '">');
        form.append('<input type="hidden" name="document_type" value="' + document_type + '">');

        // Append form to body and submit
        $('body').append(form);
        form.submit();
    });

    $('.submit_contract').click(function(e) {

        var contract_email = $('#contract_email').val();
        if (contract_email == '') {
            $('#contract_email').css('border-color', 'red');
            return; // Stop execution if validation fails
        }
        
        if (confirm("Jeste li sigurni da želite poslati ugovor na mail " + contract_email + "?")) {

            e.preventDefault(); // Prevent default action

            var iznos_putovanja = $('#iznos_putovanja').val();
            var reservation_id = $('#reservation_id').val();

            // Create a form element dynamically
            var form = $('<form action="#" method="post"></form>');

            // Append input fields to the form
            form.append('<input type="hidden" name="contract" value="send_to_mail">');
            form.append('<input type="hidden" name="iznos_putovanja" value="' + iznos_putovanja + '">');
            form.append('<input type="hidden" name="contract_email" value="' + contract_email + '">');
            form.append('<input type="hidden" name="reservation_id" value="' + reservation_id + '">');

            // Append form to body and submit
            $('body').append(form);
            form.submit();

        } else {
            return false;
        }

    });

    $('.dc-plus-page, .dc-minus-page').click(function() {
        var input = $('#pa');
        var currentValue = parseInt(input.val());
        var disabledClass = 'disabled';

        if (!isNaN(currentValue)) {
            if (!$(this).hasClass(disabledClass)) {
                if ($(this).hasClass('dc-plus-page')) {
                    input.val(currentValue + 1);
                } else {
                    input.val(currentValue - 1);
                }
                $('#filter').submit();
            }
        }
    });

    function applyInputMask(target) {
        $(target).inputmask({
            alias: 'numeric',
            radixPoint: '.',
            allowMinus: false,
            rightAlign: false,
            numericInput: true,
            placeholder: '0.00',
            groupSeparator: '',
            autoUnmask: true,
            onBeforeWrite: function(event, buffer, caretPos, opts) {
                // Prevent more than two decimals
                if (buffer.indexOf('.') !== -1 && buffer.length - buffer.indexOf('.') > 3) {
                    return false;
                }
            }
        });
    }

    // Apply mask on initial load
    applyInputMask("#akontacija, #ukupni_iznos, #ukupni_iznos_kartica, .dc_iznos");

    // popust u EUR
    $("#popust").inputmask({
        alias: 'numeric',
        min: 0,               // Minimalna vrijednost
        max: 1000000,         // Maksimalna vrijednost (prilagodi po potrebi)
        allowMinus: false,    // Nema negativnih vrijednosti
        rightAlign: false,    // Lijevo poravnanje
        digits: 2,            // Dvije decimale
        digitsOptional: false,// Obavezne decimale
        suffix: ' EUR',         // Prefiks za euro
        placeholder: '0',     // Placeholder
        autoUnmask: true,     // Automatsko uklanjanje maske
        showMaskOnHover: false,
        showMaskOnFocus: false
    });

    /*
    // popust u POSTOTKU - deaktivirano 18.10.2025.
    $("#popust").inputmask({
        alias: 'numeric',
        min: 0, // Minimum value allowed
        max: 100, // Maximum value allowed
        allowMinus: false, // No negative values
        rightAlign: false, // Left align input
        digits: 0, // No decimal places
        suffix: ' %', // Add % sign after input
        placeholder: '0', // Placeholder
        autoUnmask: true, // Automatically unmask the value
        showMaskOnHover: false, // Hide mask when not focused
        showMaskOnFocus: false // Hide mask when focused
    });
    */

    $("#radno_vrijeme_od, #radno_vrijeme_do").inputmask({
        mask: '99:99', // Define the mask for the input
        placeholder: '__:__',
        numericInput: true,
        clearIncomplete: true,
    });

    $("#broj_putnika").inputmask({
        mask: '999', // '9' indicates only numbers are allowed
        greedy: false,
        definitions: {
            '999': {
                validator: "[1-999]",
            }
        }
    });

    $('.select-school').select2({
        tags: true,
        placeholder: "Naziv škole",
        language: {
            noResults: function() {
                return "Nema rezultata.";
            }
        }
    });

    var selectElement = $('.select-grade');
    selectElement.select2({
        tags: true,
        placeholder: "Razredi",
        language: {
            noResults: function() {
                return "Nema rezultata.";
            }
        }
    });

    // Attach event handler for the unselect event
    selectElement.on('select2:unselect', function (e) {
        var deselectedRazredID = e.params.data.element.dataset.razredId;

        // Remove the input field corresponding to the deselected option
        $('input[name="razrednici[' + e.params.data.element.value + ']"]').remove();
        $('input[name="razredi[' + e.params.data.element.value + ']"]').remove();

        // Check if there are no remaining razrednici[] or razredi[] inputs
        if ($('input[name^="razrednici"]').length === 0 && $('input[name^="razredi"]').length === 0) {
            $('.dc_input_razrednici, .dc_input_razredi').remove() // ukloni sve postojeće razrede i razrednike
            //refresh_grades(); // Call the test() function if none exist
        }

    });

    $('.filter_tour_name_putovanja').on('change', function () {
        $('#filter').submit();
    });

    $('.filter-school, .filter-grade, .filter-tour_name, .filter_sifra, .filter_sk_godina').select2({
        language: {
            noResults: function() {
                return "Nema rezultata.";
            }
        }
    });

    $('#skola_id, #skolska_godina').change(function() {
        $('#naziv_skole').val($('#skola_id option:selected').data('naziv'));
        $('#adresa_skole').val($('#skola_id option:selected').data('adresa'));
        $('#mjesto_skole').val($('#skola_id option:selected').data('mjesto'));
        $('#post_skole').val($('#skola_id option:selected').data('post'));
        $('.podaci_o_skoli').show();

        selectElement.empty();
        selectElement.off('select2:select');
        selectElement.off('select2:unselect');
        $('.dc_input_razrednici, .dc_input_razredi').remove()
        refresh_grades();
    });

    $('body').on('click', '.remove_document', function() {
        $('#id_plana_putovanja').val('')
        $('#uploaded_file_link').html('')
    })

    refresh_grades();

    function refresh_grades() {

        var selectedSchoolId = $('#skola_id').val();
        var selectedSchoolYear = $('#skolska_godina').val();

        if (selectedSchoolId !== '' && selectedSchoolYear !== '') {
            $.ajax({
                url: ajax_object.ajax_url, // AJAX URL passed from wp_localize_script()
                type: 'GET',
                data: {
                    action: 'get_razredi_by_school_id',
                    skola_id: selectedSchoolId,
                    sk_godina: selectedSchoolYear
                },
                dataType: 'json',
                success: function (data) {
                    //selectElement.empty();

                    // Unbind any previous event listeners
                    //selectElement.off('select2:select');
                    //selectElement.off('select2:unselect');

                    $.each(data, function (index, item) {
                        //console.log(item)
                        if (selectElement.find(`option[value='${item.razred_id}']`).length === 0) {
                            selectElement.append($('<option>', {
                                value: item.razred_id,
                                text: item.razred_naziv,
                                'data-razred-id': item.razred_id,
                                'data-razrednik': item.razred_razrednik
                            }));
                        }
                    });

                    selectElement.select2({
                        tags: true,
                        tokenSeparators: [',', ' '],
                        multiple: true,
                        language: customTranslationsSelect2
                    });

                    var counter = 1;
                    // Add an event listener for the select2:select event
                    selectElement.on('select2:select', function (e) {
                        //console.log(e)
                        var selectedGrade = e.params.data.text;
                        var selectedRazredID = e.params.data.element ? e.params.data.element.dataset.razredId : 'novi-' + counter++;
                        var selectedRazrednik = e.params.data.element ? e.params.data.element.dataset.razrednik : '';

                        // Create input field dynamically
                        var inputField = '' +
                            '<div style="padding-top: 8px;" class="dc_input_razrednici">' +
                            '<label>Razrednik ' + selectedGrade + '</label><br>' +
                            '<input type="text" placeholder="Razrednik ' + selectedGrade + '" name="razrednici[' + selectedRazredID + ']" value="' + selectedRazrednik + '">' +
                            '</div>';

                        var inputFields = $('<input>', {
                            type: 'hidden',
                            name: 'razredi[' + selectedRazredID + ']',
                            value: selectedGrade
                        }).addClass('dc_input_razredi');

                        // Append input field to a container (replace '.input-container' with your container selector)
                        $('.input_razrednici').append(inputField);
                        $('.input_razrednici').append(inputFields);

                        $('.razrednik_tr').show();
                    });

                    selectElement.on('select2:unselect', function (e) {
                        //console.log(e.params.data.element)
                        console.log('test');
                        var deselectedGrade = e.params.data.text;
                        var deselectedRazredID = e.params.data.element.dataset.razredId;

                        // Remove the input field corresponding to the deselected grade
                        $('.input_razrednici [name="razrednici[' + deselectedRazredID + ']"]').closest('div').remove();
                    });

                    $('.razred_tr').show();
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            })
        }
    }

    $('.dc_filter_btn').on('click', function() {
        $('.dc_collapse').fadeToggle();
        $('.dc_filter_btn').toggleText('Prikaži filtere', 'Sakrij filtere')
    })

    $.fn.toggleText = function(t1, t2){
        if (this.text() == t1) this.text(t2);
        else this.text(t1);
        return this;
    };
    
    var customTranslationsSelect2 = {
        // Your custom translations
        "noResults": function () {
            return "Nema razreda u bazi. Dodajte nove.";
        }
    };

    $('.reorder').click(function() {
        var orderValue = $(this).data('order');
        $('#or').val(orderValue);
        $('#so').val($('#so').val() == 'desc' ? 'asc' : 'desc');
        $('#filter').submit();
    });

    $('#searchInput').on('keyup', function(){
        var searchText = $(this).val().toLowerCase();
        $('#dc_data_table tbody tr').each(function(){
            var found = false;
            $(this).find('td').each(function(){
                var cellText = $(this).text().toLowerCase();
                if(cellText.indexOf(searchText) !== -1){
                    found = true;
                    return false; // break out of inner loop
                }
            });
            if(found){
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $('#novo-putovanje, #form-postavke').submit(function(event) {
        // Prevent form submission
        event.preventDefault();

        if (validateInput()) {
            this.submit();
        }
    });

    $('.dc_nav_tabs span').on('click', function () {
        $('table.dc_tabs').hide()
        $('.dc_nav_tabs span.nav-tab').removeClass('nav-tab-active')
        $(this).addClass('nav-tab-active')
        $('table#' + $(this).data('tab')).show()
    })

    function validateInput() {

        var isValid = true;

        $('.validate').each(function() {
            // Check if the parent row is visible
            if ($(this).closest('tr').is(':visible')) {
                if ($(this).is('select')) {
                    if ($(this).val() === '' || parseInt($(this).val()) <= 0) {
                        $(this).css('border-color', 'red');
                        isValid = false;
                    } else {
                        $(this).css('border-color', '');
                    }
                } else {
                    if ($(this).val().trim() === '') {
                        $(this).css('border-color', 'red');
                        isValid = false;
                    } else {
                        $(this).css('border-color', '');
                    }
                }
            }
        });

        return isValid;
    }

    // Show the popup (you can trigger it with your custom logic)

    $(".dc-open-popup").on("click", function () {
        $(".dc-popup-bubble-container").fadeIn();
    });

    // Close the popup when the close button is clicked
    $(".dc-close-popup").on("click", function () {
        $(".dc-popup-bubble-container").fadeOut();
    });

    // Optional: Close the popup when clicking outside of it
    $(".dc-popup-bubble-container").on("click", function (e) {
        if ($(e.target).is(".dc-popup-bubble-container")) {
            $(".dc-popup-bubble-container").fadeOut();
        }
    });

    $('.export_filter_btn').on('click', function() {
        // Check if the hidden input already exists to avoid duplicates
        if ($('#filter input[name="export"]').length === 0) {
            // Append the hidden input field
            $('#filter').append('<input type="hidden" name="export" value="csv-filter">');
        }

        // Submit the form
        $('#filter').submit();
    });

    const selectedOption = $('#skola_id').find('option:selected');
    $('#adresa_skole').val(selectedOption.data('adresa'))
    $('#mjesto_skole').val(selectedOption.data('mjesto'))
    $('#post_skole').val(selectedOption.data('post'))
    
    $('.dc_btn_log_sh').on('click', function() {
        $(this).closest('td').find('pre').slideToggle();
    })

});

function confirmSubmit() {
    // Display a confirmation dialog
    if (confirm("Jeste li sigurni da želite spremiti promjene?")) {
        return true;
    } else {
        return false;
    }
}