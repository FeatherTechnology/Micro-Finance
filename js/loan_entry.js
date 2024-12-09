$(document).ready(function () {
    //Move Loan Entry 
    $(document).on('click', '.move-loan-entry', function () {
        let cus_sts_id = $(this).attr('value');
        swalConfirm('Delete', 'Are you ready to move to the Approval Screen?', moveToNext, cus_sts_id);
        return;
    });
    // Loan Entry Tab Change Radio buttons


});

$(document).on("click", "#add_loan,#back_btn", function () {
    swapTableAndCreation();
    callLoanCaculationFunctions();
    getCentreId();
    getLoanCategoryName();
});
function swapTableAndCreation() {
    if ($(".loan_table_content").is(":visible")) {
        $(".loan_table_content").hide();
        $("#add_loan").hide();
        $("#loan_entry_content").show();
        $("#back_btn").show();
        getCentreId();
    } else {
        $(".loan_table_content").show();
        $("#add_loan").show();
        $("#loan_entry_content").hide();
        $("#back_btn").hide();
        $("#loan_amount_calc").val("");
        $("#total_cus").val("");
        $("#loan_amount_per_cus").val("");
        $("#profit_type_calc").val("");
        $("#centre_no").val("");
        $("#centre_name").val("");
        $("#centre_mobile").val("");
        $("#loan_category_calc2").val("");
        $("#Centre_id_edit").val("");
    }
}


// function swapTableAndCreation() {
//     if ($('.loan_table_content').is(':visible')) {
//         $('.loan_table_content').hide();
//         $('#add_loan').hide();
//         $('#loan_entry_content').show();
//         $('#back_btn').show();

//     } else {
//         $('.loan_table_content').show();
//         $('#add_loan').show();
//         $('#loan_entry_content').hide();
//         $('#back_btn').hide();
//     }
// }
$("#Centre_id").change(function () {
    if ($(this).val() != "") {
        getCentreDetails($(this).val());
        $("#Centre_id_edit").val($(this).val());
    } else {
        $("#centre_no").val("");
        $("#centre_name").val("");
        $("#centre_mobile").val("");
    }
});
$("#total_cus").on("blur", function () {
    let loam_amount = $("#loan_amount_calc").val();
    let total_cus = $("#total_cus").val();
    let loan_amount_per_cus = loam_amount / total_cus;
    $("#loan_amount_per_cus").val(loan_amount_per_cus);
});

function getCentreDetails(id) {
    $.post("api/loan_entry_files/get_centre_details.php", { id }, function (response) {
        $("#centre_no").val(response[0].centre_no);
        $("#centre_name").val(response[0].centre_name);
        $("#centre_mobile").val(response[0].mobile1);
    }, "json");
}
$(function () {
    getLoanEntryTable();
});

function getLoanEntryTable() {
    serverSideTable('#loan_entry_table', '', 'api/loan_entry/loan_entry_list.php');
    // setDropdownScripts();   
}
/////////////////////////////////////////////////////////////// Loan Calculation START //////////////////////////////////////////////////////////////////////
$(document).ready(function () {

    // $('#loan_category_calc').change(function () {
    //     if ($(this).val() != '') {
    //         let loanCatId = $('#loan_category_calc').val();
    //         $('#loan_amount_calc').val('')

    //         $('#profit_type_calc').val('').trigger('change');
    //         $('#loan_category_calc2').val($(this).val())
    //     }
    // });
    $("#loan_category_calc").change(function () {
        if ($(this).val() != "") {
            const selectedOption = $("#loan_category_calc option:selected");
            const limitValue = selectedOption.attr("limit");
            //  console.log("loan Limit " + limitValue);
            let loanCatId = $("#loan_category_calc").val();
            $("#loan_amount_calc").val("");
            $("#loan_amount_calc").attr(
                "onChange",
                `if( parseFloat($(this).val()) > '` +
                limitValue +
                `' ){ alert("Enter Lesser than '${limitValue}'"); $(this).val(""); }`
            ); //To check value between range
            $("#profit_type_calc").val("").trigger("change");
            $("#loan_category_calc2").val($(this).val());
        }
    });
    /////////////////////////////////////////////////////////submit customer Mapping Start///////////////////////////////////////////////////
    $('#submit_cus_map').click(function (event) {
        event.preventDefault(); // Prevent the default form submission

        let add_customer = $('#add_customer').val().trim(); // Trim to remove any extra spaces
        let loan_id_calc = $('#loan_id_calc').val().trim();
        let customer_mapping = $('#customer_mapping').val().trim();
        let total_cus = $('#total_cus').val().trim();
        let designation = $('#designation').val();
        // Fields that are required for validation
        var isValid = true;

        // Validate add_customer
        if (!add_customer) {
            validateField(add_customer, 'add_customer'); // Assuming validateField sets a warning
            isValid = false;
        }

        // Submit only if all fields are valid
        if (isValid) {
            $.post('api/loan_entry_files/submit_cus_mappings.php', {
                add_customer: add_customer,
                loan_id_calc: loan_id_calc,
                customer_mapping: customer_mapping,
                total_cus: total_cus,
                designation: designation,
            }, function (response) {
                let result = response.result;

                if (result === 1) {
                    // Success: Refresh the customer mapping table and clear inputs
                    getCusMapTable();
                    $('#add_customer').val(''); // Clear add_customer field
                    $('#designation').val(''); // Clear loan_id_calc field
                    // Reset borders
                    $('#loan_id_calc, #add_customer').css('border', '1px solid #cecece');
                } else if (result === 2) {
                    // Failure: Show error message
                    swalError('Error', 'An error occurred while processing the request.');
                } else if (result === 3) {
                    // Limit Exceeded: Show warning message
                    swalError('Warning', response.message);
                }
            }, 'json');
        }
    });
    $(document).on('click', '.cusMapDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to remove this customer mapping?', removeCusMap, id, '');
    });

    /////////////////////////////////////////////////////////Submit Customer Mapping End//////////////////////////////////////////////////
    $('#profit_type_calc').change(function () {
        let profitType = $(this).val();
        // Check if the loan category is selected
        let id = $('#loan_category_calc').val();
        if (id == '') {
            swalError('Alert', 'Kindly select Loan Category');
            $(this).val('');
            return;
        }
        //  clearCalcSchemeFields(profitType);
        $('#profit_type_calc_scheme').show();
        $('.calc_scheme_title').text((profitType == '1') ? 'Calculation' : 'Scheme');

        if (profitType == '1') { // Loan Calculation
            $('.calc').show();
            $('.scheme').hide();
            $('.scheme_day').hide();
            $('.scheme_date').hide();
            getLoanCatDetails(id, profitType);
        } else if (profitType == '2') { // Scheme
            dueMethodScheme(id,profitType);
            $('.calc').show();
            $('.scheme').show();
            $('.scheme_day').hide();
            $('.scheme_date').hide();
            $('#due_method_calc').each(function () {
                $(this).val($(this).find('option:first').val());
        
            });
            $('#profit_method_calc').each(function () {
                $(this).val($(this).find('option:first').val());
        
            });
        } else {
            $('#profit_type_calc_scheme').hide();
        }

        $('#due_startdate_calc').val('');
        $('#maturity_date_calc').val('');
        $('.int-diff').text('*');
        $('.due-diff').text('*');
        $('.doc-diff').text('*');
        $('.proc-diff').text('*');
        $('.refresh_loan_calc').val('');
    });

    //  Modify due_method_calc change handler
    $('#due_method_calc').change(function () {
        let schemeDueMethod = $(this).val()
        let loanCatId = $('#loan_category_calc').val();
        let profitType = $('#profit_type_calc').val(); // Get the current profit type value
        // Check both profitType and dueMethodCalc values
        if (profitType == '2' && schemeDueMethod == '2') {
            $('.scheme_day').show();
          //  dueMethodScheme(schemeDueMethod, loanCatId);
        } else {

            $('.scheme_day').hide();
            $('.scheme_day_calc').val(''); // Clear value if hidden

        }

        if (profitType == '2' && schemeDueMethod == '1') {
            $('.scheme_date').show();
          //  dueMethodScheme(schemeDueMethod, loanCatId);
            getDateDropDown()
        } else {

            $('.scheme_date').hide();
            $('.scheme_date_calc').val('');
        }
        $('#due_startdate_calc').val('');
        $('#maturity_date_calc').val('');
    });

    $('#scheme_name_calc').change(function () { //Scheme Name change event
        let scheme_id = $(this).val();
        schemeCalAjax(scheme_id);
        CalculationMethod(scheme_id)
        $('#due_startdate_calc').val('');
        $('#maturity_date_calc').val('');
    });
    // $('#profit_type_calc').on('change', function () {
    //     resetValidation();
    // });

    $('#refresh_cal').click(function () {
        $('.int-diff').text('*'); $('.due-diff').text('*'); $('.doc-diff').text('*'); $('.proc-diff').text('*'); $('.refresh_loan_calc').val('');
        let loan_amt = $('#loan_amount_calc').val(); let int_rate = $('#interest_rate_calc').val(); let due_period = $('#due_period_calc').val(); let doc_charge = $('#doc_charge_calc').val(); let proc_fee = $('#processing_fees_calc').val();

        if (loan_amt != '' && int_rate != '' && due_period != '' && doc_charge != '' && proc_fee != '') {
            let benefit_method = $('#profit_method_calc').val(); //If Changes not found in profit method, calculate loan amt for monthly basis
            if (benefit_method == 1 || benefit_method == 'Pre Benefit') {
                getLoanPreInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee)

            } else if (benefit_method == 2 || benefit_method == 'After Benefit') {
                getLoanAfterInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee);
            }

            // let due_method_scheme = $('#due_method_calc').val();
            // if (due_method_scheme == '1') {//Monthly scheme as 1
            //     getLoanMonthly(loan_amt, int_rate, due_period, doc_charge, proc_fee);

            // } else if (due_method_scheme == '2') {//Weekly scheme as 2
            //     getLoanWeekly(loan_amt, int_rate, due_period, doc_charge, proc_fee);

            // }
            //   changeInttoBen();
        }
        // else {
        //     swalError('Warning', 'Kindly Fill the Calculation fields.')
        // }
    });

    {
        // Get today's date
        var today = new Date().toISOString().split('T')[0];
        //Due start date -- set min date = current date.
        $('#due_startdate_calc').attr('min', today);
    }

    $('#scheme_day_calc').change(function () {
        $('#due_start_from').val('');
        $('#maturity_month').val('');
    })

    $('#due_startdate_calc').change(function () {
        var due_start_from = $('#due_startdate_calc').val(); // get start date to calculate maturity date
        var due_period = parseInt($('#due_period_calc').val()); //get due period to calculate maturity date
        var profit_type = $('#profit_type_calc').val()
        if (profit_type == '1') { //Based on the profit method choose due method from input box
            var due_method = $('#due_method_calc').val()
        } else if (profit_type == '2') {
            var due_method = $('#due_method_calc').val()
        }

        if (due_period == '' || isNaN(due_period)) {
            swalError('Warning', 'Kindly Fill the Due Period field.');
            $(this).val('');
        } else {
            if (due_method == 'Monthly' || due_method == '1') { // if due method is monthly or 1(for scheme) then calculate maturity by month

                var maturityDate = moment(due_start_from, 'YYYY-MM-DD').add(due_period, 'months').subtract(1, 'month').format('YYYY-MM-DD');//subract one month because by default its showing extra one month
                $('#maturity_date_calc').val(maturityDate);

            } else if (due_method == '2') {//if Due method is weekly then calculate maturity by week

                var due_day = parseInt($('#scheme_day_calc').val());

                var momentStartDate = moment(due_start_from, 'YYYY-MM-DD').startOf('day').isoWeekday(due_day);//Create a moment.js object from the start date and set the day of the week to the due day value

                var weeksToAdd = Math.floor(due_period - 1);//Set the weeks to be added by giving due period. subract 1 because by default it taking extra 1 week

                momentStartDate.add(weeksToAdd, 'weeks'); //Add the calculated number of weeks to the start date.

                if (momentStartDate.isBefore(due_start_from)) {
                    momentStartDate.add(1, 'week'); //If the resulting maturity date is before the start date, add another week.
                }

                var maturityDate = momentStartDate.format('YYYY-MM-DD'); //Get the final maturity date as a formatted string.

                $('#maturity_date_calc').val(maturityDate);

            }
        }
    });


    // $('#submit_loan_calculation').click(function (event) {
    //     event.preventDefault();
    //     let customerProfileId = $('#customer_profile_id').val();
    //     if (customerProfileId != '') {
    //         $('#refresh_cal').trigger('click'); //For calculate once again if user missed to refresh calculation
    //         let formData = {
    //             'customer_profile_id': customerProfileId,
    //             'cus_id': $('#cus_id').val().trim().replace(/\s/g, ''),
    //             'loan_id_calc': $('#loan_id_calc').val(),
    //             'loan_category_calc': $('#loan_category_calc').val(),
    //             'category_info_calc': $('#category_info_calc').val(),
    //             'loan_amount_calc': $('#loan_amount_calc').val(),
    //             'profit_type_calc': $('#profit_type_calc').val(),
    //             'due_method_calc': $('#due_method_calc').val(),
    //             'due_type_calc': $('#due_type_calc').val(),
    //             'profit_method_calc': $('#profit_method_calc').val(),
    //             'scheme_due_method_calc': $('#scheme_due_method_calc').val(),
    //             'scheme_day_calc': $('#scheme_day_calc').val(),
    //             'scheme_name_calc': $('#scheme_name_calc').val(),
    //             'interest_rate_calc': $('#interest_rate_calc').val(),
    //             'due_period_calc': $('#due_period_calc').val(),
    //             'doc_charge_calc': $('#doc_charge_calc').val(),
    //             'processing_fees_calc': $('#processing_fees_calc').val(),
    //             'loan_amnt_calc': $('#loan_amnt_calc').val(),
    //             'principal_amnt_calc': $('#principal_amnt_calc').val(),
    //             'interest_amnt_calc': $('#interest_amnt_calc').val(),
    //             'total_amnt_calc': $('#total_amnt_calc').val(),
    //             'due_amnt_calc': $('#due_amnt_calc').val(),
    //             'doc_charge_calculate': $('#doc_charge_calculate').val(),
    //             'processing_fees_calculate': $('#processing_fees_calculate').val(),
    //             'net_cash_calc': $('#net_cash_calc').val(),
    //             'loan_date_calc': $('#loan_date_calc').val(),
    //             'due_startdate_calc': $('#due_startdate_calc').val(),
    //             'maturity_date_calc': $('#maturity_date_calc').val(),
    //             'referred_calc': $('#referred_calc').val(),
    //             'agent_id_calc': $('#agent_id_calc').val(),
    //             'agent_name_calc': $('#agent_name_calc').val(),
    //             'id': $('#loan_calculation_id').val(),
    //             'cus_status': '2'
    //         }
    //         if (isFormDataValid(formData)) {
    //             $.post('api/loan_entry/loan_calculation/submit_loan_calculation.php', formData, function (response) {
    //                 if (response.status == '1') {
    //                     swalSuccess('Success', 'Loan Calculation Added Successfully!');
    //                     if ($('.page-content').length) {
    //                         $('html, body').animate({
    //                             scrollTop: $('.page-content').offset().top
    //                         }, 3000);
    //                     } 
    //                 } else if (response.status == '2') {
    //                     swalSuccess('Success', 'Loan Calculation Updated Successfully!')
    //                     if ($('.page-content').length) {
    //                         $('html, body').animate({
    //                             scrollTop: $('.page-content').offset().top
    //                         }, 3000);
    //                     }                        
    //                 } else {
    //                     swalError('Error', 'Error Occurs!')
    //                 }

    //                 $('#loan_calculation_id').val(response.last_id);
    //             }, 'json');
    //         }

    //     } else {
    //         swalError('Submit Customer Profile', 'Before Loan Calculation')
    //     }

    // });

    // $('#clear_loan_calc_form').click(function (event) {
    //     event.preventDefault();
    //     clearLoanCalcForm();
    // })

}); //Document END.

function callLoanCaculationFunctions() {
    getLoanCategoryName();
    // getCustomerList();
    setTimeout(() => {
        getCusMapTable();
     }, 500);
    let loan_calc_id = $('#loan_calculation_id').val();
    getAutoGenLoanId(loan_calc_id);
    let cus_profile_id = $('#customer_profile_id').val();
    let loanCalcId = $('#loan_calculation_id').val();
    // loanCalculationEdit(loanCalcId);
}

function getAutoGenLoanId(id) {
    $.post('api/loan_entry_files/get_autoGen_loan_id.php', { id }, function (response) {
        $('#loan_id_calc').val(response);
    }, 'json');
}
$('#customer_mapping').change(function () {
    let customer_mapping = $(this).val();
        getCustomerList(customer_mapping);
        $('#profit_type_calc').val('').trigger('change');

});
// function getLoanCategoryName() {
//     $.post('api/common_files/get_loan_category_creation.php', function (response) {
//         let appendLoanCatOption = '';
//         appendLoanCatOption += '<option value="">Select Loan Category</option>';
//         $.each(response, function (index, val) {
//             let selected = '';
//             let loan_category_calc2 = $('#loan_category_calc2').val();
//             if (val.id == loan_category_calc2) {
//                 selected = 'selected';
//             }
//             appendLoanCatOption += '<option value="' + val.id + '" ' + selected + '>' + val.loan_category + '</option>';
//         });
//         $('#loan_category_calc').empty().append(appendLoanCatOption);
//     }, 'json');
// }
function getLoanCategoryName() {
    $.post(
        "api/common_files/get_loan_category_creation.php",
        function (response) {
            let appendLoanCatOption = "";
            appendLoanCatOption += '<option value=" ">Select Loan Category</option>';
            $.each(response, function (index, val) {
                let selected = "";
                let loan_category_calc2 = $("#loan_category_calc2").val();
                if (val.id == loan_category_calc2) {
                    selected = "selected";
                }
                appendLoanCatOption +=
                    '<option value="' +
                    val.id +
                    '" limit="' +
                    val.loan_limit +
                    '" ' +
                    selected +
                    ">" +
                    val.loan_category +
                    "</option>";
            });
            $("#loan_category_calc").empty().append(appendLoanCatOption);
        },
        "json"
    );
}

function getLoanCatDetails(id, profitType) {
    $.post('api/loan_entry_files/getLoanCatDetails.php', { id, profitType }, function (response) {
        if (response && response.length > 0) {
            $('#due_method_calc').val(response[0].due_method);
            $('#profit_method_calc').val(response[0].benefit_method);
            $('#due_method_calc').prop('disabled', true);
            $('#profit_method_calc').prop('disabled', true);
            var int_rate_upd = ($('#int_rate_upd').val()) ? $('#int_rate_upd').val() : '';
            var due_period_upd = ($('#due_period_upd').val()) ? $('#due_period_upd').val() : '';
            var doc_charge_upd = ($('#doc_charge_upd').val()) ? $('#doc_charge_upd').val() : '';
            var proc_fee_upd = ($('#proc_fees_upd').val()) ? $('#proc_fees_upd').val() : '';
            //To set min and maximum 
            $('.min-max-int').text('* (' + response[0].interest_rate_min + '% - ' + response[0].interest_rate_max + '%) ');
            $('#interest_rate_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + response[0].interest_rate_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '` + response[0].interest_rate_min + `' && parseFloat($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
            $('#interest_rate_calc').val(int_rate_upd);
            $('.min-max-due').text('* (' + response[0].due_period_min + ' - ' + response[0].due_period_max + ') ');
            $('#due_period_calc').attr('onChange', `if( parseInt($(this).val()) > '` + response[0].due_period_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseInt($(this).val()) < '` + response[0].due_period_min + `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
            $('#due_period_calc').val(due_period_upd);

            $('.min-max-doc').text('* (' + response[0].doc_charge_min + '% - ' + response[0].doc_charge_max + '%) ');
            $('#doc_charge_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + response[0].doc_charge_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '` + response[0].doc_charge_min + `' && parseFloat($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
            $('#doc_charge_calc').val(doc_charge_upd);

            $('.min-max-proc').text('* (' + response[0].processing_fee_min + '% - ' + response[0].processing_fee_max + '%) ');
            $('#processing_fees_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + response[0].processing_fee_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '` + response[0].processing_fee_min + `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
            $('#processing_fees_calc').val(proc_fee_upd);

            if (profitType == 2) {
                $('#interest_rate_calc').val('');
                $('#due_period_calc').val('');
                $('#doc_charge_calc').val('');
                $('#processing_fees_calc').val('');

            }
        }
        else {
            swalError('Warning', " No Calculation is found")
            // Hide the scheme element if the response is empty or null
            $('#profit_type_calc_scheme').hide();
        }
    }, 'json');
}

function clearCalcSchemeFields(type) {
    $('.to_clear').val('');
    $('.min-max-int').text('*');
    $('.min-max-due').text('*');
    $('.min-max-doc').text('*');
    $('.min-max-proc').text('*');
    if (type == '2') { //Scheme

    } else {
        $('#interest_rate_calc').prop('readonly', false);
        $('#due_period_calc').prop('readonly', false);
    }
}

function dueMethodScheme(id, profitType) {
    $.post('api/common_files/get_due_method_scheme.php', { id, profitType }, function (response) {
        if (response && response.length > 0) {
            clearCalcSchemeFields('1'); // Clear fields
            
            // Initialize options with default
            let appendSchemeNameOption = '<option value="">Select Scheme Name</option>';

            // Iterate through the response and append options
            $.each(response, function (index, val) {
                let selected = '';
                let scheme_edit_it = $('#scheme_name_edit').val();

                // If the scheme matches the edit value, mark it as selected
                if (val.id == scheme_edit_it) {
                    selected = 'selected';
                }
                
                // Build the option elements
                appendSchemeNameOption += '<option value="' + val.id + '" ' + selected + '>' + val.scheme_name + '</option>';
            });

            // Set the options once after the loop
            $('#scheme_name_calc').empty().append(appendSchemeNameOption);

        } else {
            swalError('Warning', "No scheme is found");
            $('#profit_type_calc_scheme').hide(); // Hide scheme if no response
        }
    }, 'json').fail(function () {
        swalError('Warning', "No scheme is found");
        $('#profit_type_calc_scheme').hide(); // Hide scheme in case of failure
    });


        // let profitType = $('#profit_type_calc').val(); // Get the current profit type value
        let schemeDueMethod = $('#due_method_calc').val();
    if (profitType == '2' && schemeDueMethod == '2') {
        $('.scheme_day').show();

    } else {
        $('.scheme_day').hide()
        $('.scheme_day_calc').val('');

    }
    if (profitType == '2' && schemeDueMethod == '1') {
        $('.scheme_date').show();
    } else {

        $('.scheme_date').hide();
        $('.scheme_date_calc').val('');
    }

}


function CalculationMethod(scheme_id) {
    $.post('api/loan_entry_files/get_profit_method.php', {scheme_id }, function (response) {
        // Check if the response is null, empty, or not valid
        if (response && response.length > 0) {
            // Set the values of the form fields based on the response
            $('#due_method_calc').val(response[0].due_method).trigger('change');
            $('#profit_method_calc').val(response[0].benefit_method);
            $('#profit_type_calc_scheme').show(); // Ensure the scheme is shown if data is valid
            $('#due_method_calc').prop('disabled', true);
            $('#profit_method_calc').prop('disabled', true);
        } else {
            swalError('Warning', " No scheme is found")
            // Hide the scheme element if the response is empty or null
            $('#profit_type_calc_scheme').hide();
        }
    }, 'json')
        .fail(function () {
            swalError('Warning', "No scheme is found")
            $('#profit_type_calc_scheme').hide(); // Hide the scheme element in case of a failure
        });
}

function getDateDropDown(editDId) {
    let dateOption = '<option value="">Select Date</option>';
    for (let i = 1; i <= 31; i++) {
        let selected = '';
        if (i == editDId) {
            selected = 'selected';
        }
        dateOption += '<option value="' + i + '" ' + selected + '>' + i + '</option>';
    }
    $('#scheme_date_calc').empty().append(dateOption);
}
function schemeCalAjax(id) {

    if (id != '') {
        var int_rate_upd = ($('#int_rate_upd').val()) ? $('#int_rate_upd').val() : '';
        var due_period_upd = ($('#due_period_upd').val()) ? $('#due_period_upd').val() : '';
        var doc_charge_upd = ($('#doc_charge_upd').val()) ? $('#doc_charge_upd').val() : '';
        var proc_fee_upd = ($('#proc_fees_upd').val()) ? $('#proc_fees_upd').val() : '';
        $.post('api/loan_category_creation/get_scheme_data.php', { id }, function (response) {
            //To set min and maximum
            $('.min-max-int').text('* (' + response[0].interest_rate_percent_min + '% - ' + response[0].interest_rate_percent_max + '%) ');
            $('#interest_rate_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + response[0].interest_rate_percent_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '` + response[0].interest_rate_percent_min + `' && parseFloat($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
            $('#interest_rate_calc').val(int_rate_upd);
            $('.min-max-due').text('* (' + response[0].due_period_percent_min + ' - ' + response[0].due_period_percent_max + ') ');
            $('#due_period_calc').attr('onChange', `if( parseInt($(this).val()) > '` + response[0].due_period_percent_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseInt($(this).val()) < '` + response[0].due_period_percent_min + `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
            $('#due_period_calc').val(due_period_upd);

            $('.min-max-doc').text('* (' + response[0].doc_charge_min + '% - ' + response[0].doc_charge_max + '%) ');
            $('#doc_charge_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + response[0].doc_charge_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '` + response[0].doc_charge_min + `' && parseFloat($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
            $('#doc_charge_calc').val(doc_charge_upd);

            $('.min-max-proc').text('* (' + response[0].processing_fee_min + '% - ' + response[0].processing_fee_max + '%) ');
            $('#processing_fees_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + response[0].processing_fee_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '` + response[0].processing_fee_min + `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
            $('#processing_fees_calc').val(proc_fee_upd);

        }, 'json');

    } else {
        clearCalcSchemeFields('2')
    }
}

//To Get Loan Calculation for After Interest
function getLoanAfterInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee) {

    $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card
    $('#principal_amnt_calc').val(parseInt(loan_amt).toFixed(0)); // principal amt as same as loan amt for after interest

    var interest_rate = (parseInt(loan_amt) * (parseFloat(int_rate) / 100) * parseInt(due_period)).toFixed(0); //Calculate interest rate
    $('#interest_amnt_calc').val(parseInt(interest_rate));

    var tot_amt = parseInt(loan_amt) + parseFloat(interest_rate); //Calculate total amount from principal/loan amt and interest rate
    $('#total_amnt_calc').val(parseInt(tot_amt).toFixed(0));

    var due_amt = parseInt(tot_amt) / parseInt(due_period);//To calculate due amt by dividing total amount and due period given on loan info
    var roundDue = Math.ceil(due_amt / 5) * 5; //to increase Due Amt to nearest multiple of 5
    if (roundDue < due_amt) {
        roundDue += 5;
    }
    $('.due-diff').text('* (Difference: +' + parseInt(roundDue - due_amt) + ')'); //To show the difference amount
    $('#due_amnt_calc').val(parseInt(roundDue).toFixed(0));

    ////////////////////recalculation of total, principal, interest///////////////////
    var new_tot = parseInt(roundDue) * due_period;
    $('#total_amnt_calc').val(new_tot)

    //to get new interest rate using round due amt
    let new_int = (roundDue * due_period) - loan_amt;
    var roundedInterest = Math.ceil(new_int / 5) * 5;
    if (roundedInterest < new_int) {
        roundedInterest += 5;
    }

    $('.int-diff').text('* (Difference: +' + parseInt(roundedInterest - interest_rate) + ')'); //To show the difference amount from old to new
    $('#interest_amnt_calc').val(parseInt(roundedInterest));

    var new_princ = parseInt(new_tot) - parseInt(roundedInterest);
    $('#principal_amnt_calc').val(new_princ);

    //////////////////////////////////////////////////////////////////////////////////

    var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100); //Get document charge from loan info and multiply with loan amt to get actual doc charge
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
    $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

    var proc_fee = parseInt(loan_amt) * (parseFloat(proc_fee) / 100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
    $('#processing_fees_calculate').val(parseInt(roundeprocfee));

    var net_cash = parseInt(loan_amt) - parseFloat(roundeddoccharge) - parseFloat(roundeprocfee); //Net cash will be calculated by subracting other charges
    $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
}
function getLoanPreInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee) {

    // Principal Amount for Pre-Interest is the loan amount itself
    $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); // get loan amt from loan info card
    $('#principal_amnt_calc').val(parseInt(loan_amt).toFixed(0)); // principal amt same as loan amt for pre-interest

    // Calculate interest based on the loan amount and period
    var interest_rate = (parseInt(loan_amt) * (parseFloat(int_rate) / 100) * parseInt(due_period)).toFixed(0);
    $('#interest_amnt_calc').val(parseInt(interest_rate));

    // Calculate total amount: loan amount + interest
    var tot_amt = parseInt(loan_amt) + parseFloat(interest_rate);
    $('#total_amnt_calc').val(parseInt(tot_amt).toFixed(0));

    // Calculate due amount by dividing the total amount by the due period
    var due_amt = parseInt(tot_amt) / parseInt(due_period);
    var roundDue = Math.ceil(due_amt / 5) * 5; // rounding due amount to nearest multiple of 5
    if (roundDue < due_amt) {
        roundDue += 5;
    }
    $('.due-diff').text('* (Difference: +' + parseInt(roundDue - due_amt) + ')'); // show the difference amount
    $('#due_amnt_calc').val(parseInt(roundDue).toFixed(0));

    //////////////////// recalculation of total, interest, and principal /////////////////////

    // New total amount based on rounded due amount
    var new_tot = parseInt(roundDue) * due_period;
    $('#total_amnt_calc').val(new_tot);

    // Recalculate interest based on the new total amount and rounded due
    let new_int = (roundDue * due_period) - loan_amt;
    var roundedInterest = Math.ceil(new_int / 5) * 5;
    if (roundedInterest < new_int) {
        roundedInterest += 5;
    }

    $('.int-diff').text('* (Difference: +' + parseInt(roundedInterest - interest_rate) + ')'); // show interest difference
    $('#interest_amnt_calc').val(parseInt(roundedInterest));

    // Principal amount remains unchanged for Pre-Interest, so it's still the loan amount
    var new_princ = loan_amt;
    $('#principal_amnt_calc').val(new_princ);

    ///////////////////////////////////////////////////////////////////////////////////////////

    // Calculate document charge based on loan amount
    var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100);
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; // round to nearest multiple of 5
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); // show doc charge difference
    $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

    // Calculate processing fee based on loan amount
    var proc_fee = parseInt(loan_amt) * (parseFloat(proc_fee) / 100);
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5; // round to nearest multiple of 5
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); // show proc fee difference
    $('#processing_fees_calculate').val(parseInt(roundeprocfee));

    // Calculate net cash: loan amount - document charge - processing fee
    var net_cash = parseInt(loan_amt) - parseFloat(roundeddoccharge) - parseFloat(roundeprocfee);
    $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
}


// //To Get Loan Calculation for Interest due type
// function getLoanInterest(loan_amt, int_rate, doc_charge, proc_fee) {

//     $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card
//     $('#principal_amnt_calc').val(parseInt(loan_amt).toFixed(0));

//     $('#total_amnt_calc').val('');
//     $('#due_amnt_calc').val('');//Due period will be monthly by default so no need of due amt

//     var int_amt = (parseInt(loan_amt) * (parseFloat(int_rate) / 100)).toFixed(0); //Calculate interest rate

//     var roundedInterest = Math.ceil(int_amt / 5) * 5;
//     if (roundedInterest < int_amt) {
//         roundedInterest += 5;
//     }
//     $('.int-diff').text('* (Difference: +' + parseInt(roundedInterest - int_amt) + ')'); //To show the difference amount
//     $('#interest_amnt_calc').val(parseInt(roundedInterest));

//     var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100); //Get document charge from loan info and multiply with loan amt to get actual doc charge
//     var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
//     if (roundeddoccharge < doc_charge) {
//         roundeddoccharge += 5;
//     }
//     $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
//     $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

//     var proc_fee = parseInt(loan_amt) * (parseFloat(proc_fee) / 100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
//     var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
//     if (roundeprocfee < proc_fee) {
//         roundeprocfee += 5;
//     }
//     $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
//     $('#processing_fees_calculate').val(parseInt(roundeprocfee));

//     var net_cash = parseInt(loan_amt) - parseInt(doc_charge) - parseInt(proc_fee); //Net cash will be calculated by subracting other charges
//     $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
// }

// //To Get Loan Calculation for Monthly Scheme method
// function getLoanMonthly(loan_amt, int_rate, due_period, doc_charge, proc_fee) {

//     $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card

//     var int_amt = (parseInt(loan_amt) * (parseFloat(int_rate) / 100)).toFixed(0); //Calculate interest rate
//     // $('#interest_amnt_calc').val(parseInt(int_amt));

//     var princ_amt = parseInt(loan_amt) - parseInt(int_amt); // Calculate principal amt by subracting interest amt from loan amt
//     // $('#principal_amnt_calc').val(princ_amt);

//     var tot_amt = parseInt(princ_amt) + parseFloat(int_amt); //Calculate total amount from principal/loan amt and interest rate
//     // $('#total_amnt_calc').val(parseInt(tot_amt).toFixed(0));

//     var due_amt = parseInt(tot_amt) / parseInt(due_period);//To calculate due amt by dividing total amount and due period given on loan info
//     var roundDue = Math.ceil(due_amt / 5) * 5; //to increase Due Amt to nearest multiple of 5
//     if (roundDue < due_amt) {
//         roundDue += 5;
//     }
//     $('.due-diff').text('* (Difference: +' + parseInt(roundDue - due_amt) + ')'); //To show the difference amount
//     $('#due_amnt_calc').val(parseInt(roundDue).toFixed(0));

//     ////////////////////recalculation of total, principal, interest///////////////////

//     var new_tot = parseInt(roundDue) * due_period;
//     $('#total_amnt_calc').val(new_tot)

//     //to get new interest rate using round due amt
//     let new_int = (roundDue * due_period) - princ_amt;

//     var roundedInterest = Math.ceil(new_int / 5) * 5;
//     if (roundedInterest < new_int) {
//         roundedInterest += 5;
//     }

//     $('.int-diff').text('* (Difference: +' + parseInt(roundedInterest - int_amt) + ')'); //To show the difference amount
//     $('#interest_amnt_calc').val(parseInt(roundedInterest));

//     var new_princ = parseInt(new_tot) - parseInt(roundedInterest);
//     $('#principal_amnt_calc').val(new_princ);

//     //////////////////////////////////////////////////////////////////////////////////

//     var doc_type = $('.min-max-doc').text(); //Scheme may have document charge in rupees or percentage . so getting symbol from span
//     if (doc_type.includes('₹')) {
//         var doc_charge = parseInt(doc_charge); //Get document charge from loan info and directly show the document charge provided because of it is in rupees
//     } else if (doc_type.includes('%')) {
//         var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100); //Get document charge from loan info and multiply with loan amt to get actual doc charge
//     }
//     var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
//     if (roundeddoccharge < doc_charge) {
//         roundeddoccharge += 5;
//     }
//     $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
//     $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

//     var proc_type = $('.min-max-proc').text(); //Scheme may have Processing fee in rupees or percentage . so getting symbol from span
//     if (proc_type.includes('₹')) {
//         var proc_fee = parseInt(proc_fee);//Get processing fee from loan info and directly show the Processing Fee provided because of it is in rupees
//     } else if (proc_type.includes('%')) {
//         var proc_fee = parseInt(loan_amt) * (parseInt(proc_fee) / 100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
//     }
//     var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
//     if (roundeprocfee < proc_fee) {
//         roundeprocfee += 5;
//     }
//     $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
//     $('#processing_fees_calculate').val(parseInt(roundeprocfee));

//     var net_cash = parseInt(princ_amt) - parseInt(doc_charge) - parseInt(proc_fee); //Net cash will be calculated by subracting other charges
//     $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
// }

// //To Get Loan Calculation for Weekly Scheme method
// function getLoanWeekly(loan_amt, int_rate, due_period, doc_charge, proc_fee) {

//     $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card

//     var int_amt = (parseInt(loan_amt) * (parseFloat(int_rate) / 100)).toFixed(0); //Calculate interest rate
//     // $('#interest_amnt_calc').val(parseInt(int_amt));

//     var princ_amt = parseInt(loan_amt) - parseInt(int_amt); // Calculate principal amt by subracting interest amt from loan amt
//     $('#principal_amnt_calc').val(parseInt(princ_amt).toFixed(0));

//     var tot_amt = parseInt(princ_amt) + parseFloat(int_amt); //Calculate total amount from principal/loan amt and interest rate
//     $('#total_amnt_calc').val(parseInt(tot_amt).toFixed(0));

//     var due_amt = parseInt(tot_amt) / parseInt(due_period);//To calculate due amt by dividing total amount and due period given on loan info
//     var roundDue = Math.ceil(due_amt / 5) * 5; //to increase Due Amt to nearest multiple of 5
//     if (roundDue < due_amt) {
//         roundDue += 5;
//     }
//     $('.due-diff').text('* (Difference: +' + parseInt(roundDue - due_amt) + ')'); //To show the difference amount
//     $('#due_amnt_calc').val(parseInt(roundDue).toFixed(0));

//     ////////////////////recalculation of total, principal, interest///////////////////

//     var new_tot = parseInt(roundDue) * due_period;
//     $('#total_amnt_calc').val(new_tot)

//     //to get new interest rate using round due amt
//     let new_int = (roundDue * due_period) - princ_amt;

//     var roundedInterest = Math.ceil(new_int / 5) * 5;
//     if (roundedInterest < new_int) {
//         roundedInterest += 5;
//     }

//     $('.int-diff').text('* (Difference: +' + parseInt(roundedInterest - int_amt) + ')'); //To show the difference amount
//     $('#interest_amnt_calc').val(parseInt(roundedInterest));

//     var new_princ = parseInt(new_tot) - parseInt(roundedInterest);
//     $('#principal_amnt_calc').val(new_princ);

//     //////////////////////////////////////////////////////////////////////////////////

//     var doc_type = $('.min-max-doc').text(); //Scheme may have document charge in rupees or percentage . so getting symbol from span
//     if (doc_type.includes('₹')) {
//         var doc_charge = parseInt(doc_charge); //Get document charge from loan info and directly show the document charge provided because of it is in rupees
//     } else if (doc_type.includes('%')) {
//         var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100); //Get document charge from loan info and multiply with loan amt to get actual doc charge
//     }
//     var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
//     if (roundeddoccharge < doc_charge) {
//         roundeddoccharge += 5;
//     }
//     $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
//     $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

//     var proc_type = $('.min-max-proc').text();//Scheme may have Processing fee in rupees or percentage . so getting symbol from span
//     if (proc_type.includes('₹')) {
//         var proc_fee = parseInt(proc_fee);//Get processing fee from loan info and directly show the Processing Fee provided because of it is in rupees
//     } else if (proc_type.includes('%')) {
//         var proc_fee = parseInt(loan_amt) * (parseInt(proc_fee) / 100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
//     }
//     var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
//     if (roundeprocfee < proc_fee) {
//         roundeprocfee += 5;
//     }
//     $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
//     $('#processing_fees_calculate').val(parseInt(roundeprocfee));

//     var net_cash = parseInt(princ_amt) - parseInt(doc_charge) - parseInt(proc_fee); //Net cash will be calculated by subracting other charges
//     $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
// }

//To Get Loan Calculation for Daily Scheme method
// function getLoanDaily(loan_amt, int_rate, due_period, doc_charge, proc_fee) {

//     $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card

//     var int_amt = (parseInt(loan_amt) * (parseFloat(int_rate) / 100)).toFixed(0); //Calculate interest rate
//     $('#interest_amnt_calc').val(parseInt(int_amt));

//     var princ_amt = parseInt(loan_amt) - parseInt(int_amt); // Calculate principal amt by subracting interest amt from loan amt
//     $('#principal_amnt_calc').val(parseInt(princ_amt).toFixed(0));

//     var tot_amt = parseInt(princ_amt) + parseFloat(int_amt); //Calculate total amount from principal/loan amt and interest rate
//     $('#total_amnt_calc').val(parseInt(tot_amt).toFixed(0));

//     var due_amt = parseInt(tot_amt) / parseInt(due_period);//To calculate due amt by dividing total amount and due period given on loan info
//     var roundDue = Math.ceil(due_amt / 5) * 5; //to increase Due Amt to nearest multiple of 5
//     if (roundDue < due_amt) {
//         roundDue += 5;
//     }
//     $('.due-diff').text('* (Difference: +' + parseInt(roundDue - due_amt) + ')'); //To show the difference amount
//     $('#due_amnt_calc').val(parseInt(roundDue).toFixed(0));

//     ////////////////////recalculation of total, principal, interest///////////////////

//     var new_tot = parseInt(roundDue) * due_period;
//     $('#total_amnt_calc').val(new_tot)

//     //to get new interest rate using round due amt
//     let new_int = (roundDue * due_period) - princ_amt;

//     var roundedInterest = Math.ceil(new_int / 5) * 5;
//     if (roundedInterest < new_int) {
//         roundedInterest += 5;
//     }

//     $('.int-diff').text('* (Difference: +' + parseInt(roundedInterest - int_amt) + ')'); //To show the difference amount
//     $('#interest_amnt_calc').val(parseInt(roundedInterest));

//     var new_princ = parseInt(new_tot) - parseInt(roundedInterest);
//     $('#principal_amnt_calc').val(new_princ);

//     //////////////////////////////////////////////////////////////////////////////////

//     var doc_type = $('.min-max-doc').text(); //Scheme may have document charge in rupees or percentage . so getting symbol from span
//     if (doc_type.includes('₹')) {
//         var doc_charge = parseInt(doc_charge); //Get document charge from loan info and directly show the document charge provided because of it is in rupees
//     } else if (doc_type.includes('%')) {
//         var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100); //Get document charge from loan info and multiply with loan amt to get actual doc charge
//     }
//     var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
//     if (roundeddoccharge < doc_charge) {
//         roundeddoccharge += 5;
//     }
//     $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
//     $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

//     var proc_type = $('.min-max-proc').text();//Scheme may have Processing fee in rupees or percentage . so getting symbol from span
//     if (proc_type.includes('₹')) {
//         var proc_fee = parseInt(proc_fee);//Get processing fee from loan info and directly show the Processing Fee provided because of it is in rupees
//     } else if (proc_type.includes('%')) {
//         var proc_fee = parseInt(loan_amt) * (parseInt(proc_fee) / 100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
//     }
//     var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
//     if (roundeprocfee < proc_fee) {
//         roundeprocfee += 5;
//     }
//     $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
//     $('#processing_fees_calculate').val(parseInt(roundeprocfee));

//     var net_cash = parseInt(princ_amt) - parseInt(doc_charge) - parseInt(proc_fee); //Net cash will be calculated by subracting other charges
//     $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
// }
// function resetValidation() {
//     const fieldsToReset = [
//         'due_method_calc', 'due_type_calc', 'profit_method_calc',
//         'interest_rate_calc', 'due_period_calc', 'doc_charge_calc', 'processing_fees_calc',
//         'scheme_due_method_calc', 'scheme_name_calc', 'scheme_day_calc',
//         'agent_id_calc', 'agent_name_calc', 'doc_need_calc'
//     ];

//     fieldsToReset.forEach(fieldId => {
//         $('#' + fieldId).css('border', '1px solid #cecece');

//     });
// }
// // Function to check if all values in an object are not empty
// function isFormDataValid(formData) {
//     let isValid = true;
//     const excludedFields = [
//         'loan_amnt_calc', 'principal_amnt_calc', 'interest_amnt_calc', 'total_amnt_calc',
//         'processing_fees_calculate', 'net_cash_calc',
//         'due_amnt_calc', 'doc_charge_calculate',
//         'id', 'category_info_calc', 'due_method_calc', 'due_type_calc', 'profit_method_calc',
//         'scheme_due_method_calc', 'scheme_day_calc', 'scheme_name_calc', 'agent_id_calc', 'due_period_calc', 'interest_rate_calc', 'processing_fees_calc', 'doc_charge_calc',
//         'agent_name_calc', 'customer_profile_id', 'cus_status'
//     ];

//     // Validate all fields except the excluded ones
//     for (let key in formData) {
//         if (!excludedFields.includes(key)) {
//             if (!validateField(formData[key], key)) {
//                 isValid = false;
//             }
//         }
//     }

//     // Additional validation based on specific conditions
//     if (formData['profit_type_calc'] == '1') { // Calculation
//         let validationResults = [
//             validateField(formData['due_method_calc'], 'due_method_calc'),
//             validateField(formData['due_type_calc'], 'due_type_calc'),
//             validateField(formData['profit_method_calc'], 'profit_method_calc'),
//             validateField(formData['interest_rate_calc'], 'interest_rate_calc'),
//             validateField(formData['due_period_calc'], 'due_period_calc'),
//             validateField(formData['doc_charge_calc'], 'doc_charge_calc'),
//             validateField(formData['processing_fees_calc'], 'processing_fees_calc')
//         ];
//         if (!validationResults.every(result => result)) {
//             isValid = false;
//         }
//     }
//     else if (formData['profit_type_calc'] == '2') {
//         let validationResults = [
//             validateField(formData['due_method_calc'], 'due_method_calc'),
//             validateField(formData['scheme_name_calc'], 'scheme_name_calc'),
//             validateField(formData['doc_charge_calc'], 'doc_charge_calc'),
//             validateField(formData['processing_fees_calc'], 'processing_fees_calc')
//         ];

//         if (formData['due_method_calc'] == '2') {
//             validationResults.push(validateField(formData['scheme_day_calc'], 'scheme_day_calc'));
//         }

//         // Check if all validations passed
//         if (!validationResults.every(result => result)) {
//             isValid = false;
//         }
//     }


//     return isValid;
// }



// function changeInttoBen() {
//     let dueType = document.getElementById('due_type_calc');
//     let intLabel = document.querySelector('label[for="interest_amnt_calc"]');
//     if (dueType.value == 'Interest') {
//         intLabel.textContent = 'Benefit Amount';
//     } else {
//         intLabel.textContent = 'Interest Amount';
//     }
// }

// function clearLoanCalcForm() {
//     // Clear input fields except those with IDs 'loan_id_calc' and 'loan_date_calc'
//     $('#loan_entry_loan_calculation').find('input').each(function () {
//         var id = $(this).attr('id');
//         if (id !== 'loan_id_calc' && id !== 'loan_date_calc' && id != 'profit_method_calc' && id != 'refresh_cal' && id != 'submit_doc_need') {
//             $(this).val('');
//         }
//     });
//     $('#loan_entry_loan_calculation input').css('border', '1px solid #cecece');
//     $('#loan_entry_loan_calculation select').css('border', '1px solid #cecece');
//     $('#loan_entry_loan_calculation textarea').css('border', '1px solid #cecece');
//     $('.min-max-int').text('*'); $('.min-max-due').text('*'); $('.min-max-doc').text('*'); $('.min-max-proc').text('*');
//     $('.int-diff').text('*'); $('.due-diff').text('*'); $('.doc-diff').text('*'); $('.proc-diff').text('*');

//     // Clear all textarea fields within the specific form
//     $('#loan_entry_loan_calculation').find('textarea').val('');

//     // Reset all select fields within the specific form
//     $('#loan_entry_loan_calculation').find('select').each(function () {
//         $(this).val($(this).find('option:first').val());
//     });
// }
function getCustomerList(customer_mapping) {
    $.post('api/loan_entry_files/get_customer_list.php',{customer_mapping}, function (response) {
        let cusOptn = '';
        cusOptn = '<option value="">Select Customer Name</option>';
        response.forEach(val => {
            cusOptn += '<option value="' + val.id + '">' + val.first_name  + ' - ' + val.mobile1 +' - ' + val.areaname + '</option>';
        });
        $('#add_customer').empty().append(cusOptn);
    }, 'json');
}
function getCusMapTable() {
    let loan_id_calc = $('#loan_id_calc').val();
    $.post('api/loan_entry_files/get_cus_map_details.php', { loan_id_calc }, function (response) {
        let cusMapColumn = [
            "sno",
            "cus_id",
            "first_name",
            "cus_data",
            "aadhar_number",
            "mobile1",
            "areaname",
             "designation",
            "action"
           
        ]
        appendDataToTable('#cus_mapping_table', response, cusMapColumn);
        setdtable('#cus_mapping_table');
    }, 'json');
}
function removeCusMap(id) {
    $.post('api/loan_entry_files/delete_cus_mapping.php', { id }, function (response) {
        if (response == 1) {
            swalSuccess('Success', 'Customer mapping removed successfully.')

            getCusMapTable();
        } else {
            swalError('Alert', 'Customer mapping remove failed.')
        }
    }, 'json');
}
// function loanCalculationEdit(id) {
//     $.post('api/loan_entry/loan_calculation/loan_calculation_data.php', { id }, function (response) {
//         if (response.length > 0) {
//             $('#loan_id_calc').val(response[0].loan_id);
//             $('#loan_category_calc').val(response[0].loan_category);
//             $('#loan_category_calc2').val(response[0].loan_category);
//             $('#category_info_calc').val(response[0].category_info);
//             $('#loan_amount_calc').val(response[0].loan_amount);
//             $('#profit_type_calc').val(response[0].profit_type);
//             $('#due_method_calc').val(response[0].due_method);
//             $('#due_type_calc').val(response[0].due_type);
//             $('#profit_method_calc').val(response[0].profit_method);
//             $('#due_method_calc').val(response[0].scheme_due_method);
//             $('#scheme_name_edit').val(response[0].scheme_name);
//             $('#int_rate_upd').val(response[0].interest_rate);
//             $('#due_period_upd').val(response[0].due_period);
//             $('#doc_charge_upd').val(response[0].doc_charge);
//             $('#proc_fees_upd').val(response[0].processing_fees);
//             $('#loan_amnt_calc').val(response[0].loan_amnt);
//             $('#principal_amnt_calc').val(response[0].principal_amnt);
//             $('#interest_amnt_calc').val(response[0].interest_amnt);
//             $('#total_amnt_calc').val(response[0].total_amnt);
//             $('#due_amnt_calc').val(response[0].due_amnt);
//             $('#doc_charge_calculate').val(response[0].doc_charge_calculate);
//             $('#processing_fees_calculate').val(response[0].processing_fees_calculate);
//             $('#net_cash_calc').val(response[0].net_cash);
//             $('#loan_date_calc').val(response[0].loan_date);
//             $('#due_startdate_calc').val(response[0].due_startdate);
//             $('#maturity_date_calc').val(response[0].maturity_date);
//             $('#referred_calc').val(response[0].referred);
//             $('#referred_calc').trigger('change');

//             $('#profit_type_calc_scheme').show();
//             if (response[0].profit_type == '0') {//Loan Calculation
//                 $('.calc').show();
//                 $('.scheme').hide();
//                 $('.scheme_day').hide();
//                 getLoanCatDetails(response[0].loan_category, 2);
//             } else if (response[0].profit_type == '1') { //Scheme
//                 dueMethodScheme(response[0].scheme_due_method, response[0].loan_category)
//                 $('.calc').hide();
//                 $('.scheme').show();
//                 setTimeout(() => {
//                     schemeCalAjax(response[0].scheme_name)
//                 }, 500);

//             }


//             setTimeout(() => {
//                 $('#scheme_day_calc').val(response[0].scheme_day);
//                 $('#agent_id_calc').val(response[0].agent_id);
//                 $('#agent_name_calc').val(response[0].agent_name);
//                 $('#refresh_cal').trigger('click');
//             }, 2000);
//         }
//     }, 'json');
// }
function getCentreId() {
    $.post("api/loan_entry_files/get_centre_id.php", function (response) {
        let appendLoanCatOption = "";
        appendLoanCatOption += '<option value="">Select Centre ID</option>';
        $.each(response, function (index, val) {
            let selected = "";
            let Centre_id_edit = $("#Centre_id_edit").val();
            if (val.centre_id == Centre_id_edit) {
                selected = "selected";
            }
            appendLoanCatOption += '<option value="' + val.centre_id + '" ' + selected + ">" + val.centre_id + "</option>";
        });
        $("#Centre_id").empty().append(appendLoanCatOption);
    }, "json");
}
//////////////////////////////////////////////////////////////// Loan Calculation END //////////////////////////////////////////////////////////////////////