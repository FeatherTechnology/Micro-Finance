$(document).ready(function () {
    $('input[name=customer_data_type]').click(function () {
        let centreType = $(this).val();
        if (centreType == 'cen_current') {
            $('.centre_loan_current').show();
            $('.centre_loan_closed').hide();
        } else if (centreType == 'cen_closed') {
            $('.centre_loan_closed').show();
            $('.centre_loan_current').hide();
        }
    })


    $(document).on('click', '#centre_current', function () {
        getCentreCurrentTable();
    })
    $(document).on('click', '#centre_closed', function () {
        getCentreClosedTable();
    })
    $(document).on('click', '#back_btn', function () {
        getCentreCurrentTable();
        $('#back_btn').hide();
        $('.centre_loan_current').show()
        $('#curr_closed').show()
        $('.ledger_view_chart_model').hide();
        $('.loan_info_table_content').hide();
    })
    $(document).on('click', '#close_back_btn', function () {
        getCentreCurrentTable();
        $('#close_back_btn').hide();
        $('.centre_loan_closed').show()
        $('#curr_closed').show()
        $('.ledger_view_chart_model').hide();
        $('.loan_info_table_content').hide();
    })
});
/////////////////// Current Ledger View//////////////////
$(document).on('click', '.ledgerViewBtn', function (event) {
    event.preventDefault();
    $('.ledger_view_chart_model').show();
    $('#back_btn').show();
    $('.centre_loan_current').hide()
    $('#curr_closed').hide()
    let loan_id = $(this).attr('value');
    getLedgerViewChart(loan_id);
});

$(document).on('click', '.currentViewBtn', function (event) {
    event.preventDefault();
    $('.loan_info_table_content').show();
    $('#back_btn').show();
    $('.centre_loan_current').hide();
    $('#curr_closed').hide();

    let current_loan_id = $(this).data('loan-id');
    let current_centre_id = $(this).data('centre-id');
    loanEntryInfo(current_loan_id, current_centre_id);
});

//////////////////////////Ledger View/////////////////////////////
$(document).on('click', '.closeledgerViewBtn', function (event) {
    event.preventDefault();
    $('.ledger_view_chart_model').show();
    $('#close_back_btn').show();
    $('.centre_loan_closed').hide()
    $('#curr_closed').hide()
    let loan_id= $(this).attr('value');
    getLedgerViewChart(loan_id);
});

/////////////////// Closed View//////////////////
$(document).on('click', '.closedViewBtn', function (event) {
    event.preventDefault();
    $('.loan_info_table_content').show();
    $('#close_back_btn').show();
    $('.centre_loan_closed').hide()
    $('#curr_closed').hide()
    
    let current_loan_id = $(this).data('loan-id');
    let current_centre_id = $(this).data('centre-id');
    loanEntryInfo(current_loan_id, current_centre_id);
});

//////////////////////////Ledger View/////////////////////////////
//////////////////////////////////////////Due Chart start//////////////////////////////

$(document).on('click', '.due-chart', async function () {
    const cus_mapping_id = $(this).data('id');
    const loan_id = $('#loan_id').val();

    $('#due_chart_model').modal('show');

    try {
        await dueChartList(cus_mapping_id, loan_id); // Wait for due chart to fully load

        // Now bind the click event for print buttons
        $('.print_due_coll').off('click').on('click', function () {
            var id = $(this).attr('value');

            Swal.fire({
                title: 'Print',
                text: 'Do you want to print this collection?',
                imageUrl: 'img/printer.png',
                imageWidth: 300,
                imageHeight: 210,
                imageAlt: 'Custom image',
                showCancelButton: true,
                confirmButtonColor: '#009688',
                cancelButtonColor: '#d33',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'api/collection_files/print_collection.php',
                        data: { 'coll_id': id },
                        type: 'POST',
                        cache: false,
                        success: function (html) {
                            $('#printcollection').html(html);
                        }
                    });
                }
            });
        });
    } catch (err) {
        console.error("Error in loading due chart or binding print:", err);
    }
});

//////////////////////////Due Chart End/////////////////////////////////////////

//////////////////////////////////////Document End
$(function () {
    getCentreCurrentTable();
});

function getCentreCurrentTable() {
    serverSideTable('#centre_current_table', '', 'api/centre_summary_files/centre_current_list.php');
}
function getCentreClosedTable() {
    serverSideTable('#centre_close_table', '', 'api/centre_summary_files/centre_closed_list.php');
}
function getLedgerViewChart(loan_id) {
    $.post('api/collection_files/ledger_view_data.php', { loan_id: loan_id }, function (response) {
        $('#ledger_view_table_div').empty();
        $('#ledger_view_table_div').html(response);

    });
}

async function dueChartList(cus_mapping_id, loan_id) {
    // First request: get_due_chart_list.php
    await new Promise((resolve, reject) => {
        $.ajax({
            url: 'api/collection_files/get_due_chart_list.php',
            data: { 'cus_mapping_id': cus_mapping_id },
            type: 'POST',
            cache: false,
            success: function (response) {
                $('#due_chart_table_div').empty().html(response);
                resolve();
            },
            error: function (xhr, status, error) {
                console.error('Error in get_due_chart_list:', error);
                reject(error);
            }
        });
    });

    // Second request: get_due_method_name.php
    await new Promise((resolve, reject) => {
        $.post('api/collection_files/get_due_method_name.php', { cus_mapping_id: cus_mapping_id }, function (response) {
            $('#dueChartTitle').text('Due Chart ( ' + response['due_month'] + ' - ' + response['loan_type'] + ' ) - Customer ID: '
                + response['cus_id'] + ' - Customer Name: ' + response['cus_name'] + ' - Loan ID: ' + response['loan_id']
                + ' - Centre ID: ' + response['centre_id'] + ' - Centre Name: ' + response['centre_name']);
            resolve();
        }, 'json').fail((xhr, status, error) => {
            console.error('Error in get_due_method_name:', error);
            reject(error);
        });
    });
}

function closeChartsModal() {
    $('#due_chart_model').modal('hide');
}



function loanEntryInfo(current_loan_id, current_centre_id) {
    $.post('api/common_files/loan_entry_data.php', { current_loan_id, current_centre_id }, function (response) {

        $('#loan_id_calc').val(response[0].loan_id);
        $('#loan_category_calc').val(response[0].loan_category);
        $('#Centre_id').val(response[0].centre_id);
        $('#centre_no').val(response[0].centre_no);
        $('#centre_name').val(response[0].centre_name);
        $('#centre_mobile').val(response[0].mobile1);
        $('#total_cus').val(response[0].total_customer);
        $('#loan_amount_calc').val(moneyFormatIndia(response[0].loan_amount));
        $('#profit_type_calc').val(response[0].profit_type);
        $('#scheme_name_calc').val(response[0].scheme_name);
        $('#profit_method_calc').val(response[0].benefit_method);
        $('#interest_rate_calc').val(response[0].interest_rate);
        $('#due_period_calc').val(response[0].due_period);
        $('#doc_charge_calc').val(response[0].doc_charge);
        $('#processing_fees_calc').val(response[0].processing_fees);
        $('#loan_amnt_calc').val(moneyFormatIndia(response[0].loan_amount_calc));
        $('#principal_amnt_calc').val(moneyFormatIndia(response[0].principal_amount_calc));
        $('#interest_amnt_calc').val(moneyFormatIndia(response[0].intrest_amount_calc));
        $('#total_amnt_calc').val(moneyFormatIndia(response[0].total_amount_calc));
        $('#due_amnt_calc').val(moneyFormatIndia(response[0].due_amount_calc));
        $('#doc_charge_calculate').val(moneyFormatIndia(response[0].document_charge_cal));
        $('#processing_fees_calculate').val(moneyFormatIndia(response[0].processing_fees_cal));
        $('#net_cash_calc').val(moneyFormatIndia(response[0].net_cash_calc));
        $('#scheme_day_calc').val(response[0].scheme_day_calc);
        $('#scheme_date_calc').val(response[0].scheme_date);

        let dueMethod = '';

        if (response[0].profit_type == '1') { // Calculation
            dueMethod = response[0].category_due_method;
        } else if (response[0].profit_type == '2') { // Scheme
            dueMethod = response[0].scheme_due_method;
        }

        let dueMethodText = (dueMethod == '1') ? 'Monthly' : (dueMethod == '2') ? 'Weekly' : '';
        $('#due_method_calc').val(dueMethodText);

        // Show/hide divs
        if (dueMethod == '1') {
            $('.scheme_day').hide();
            $('.calc').show();
        } else if (dueMethod == '2') {
            $('.calc').hide();
            $('.scheme_day').show();
        } else {
            $('.calc').hide();
            $('.scheme_day').hide();
        }


        $('.calc_scheme_title').text((response[0].profit_type == '0') ? 'Calculation' : 'Scheme');
        $('#profit_type_calc_scheme').show();

        if (response[0].profit_type == '1') {//Loan Calculation
            $('.scheme').hide();
        } else if (response[0].profit_type == '2') { //Scheme
            $('.scheme').show();
        }
    }, 'json');
}