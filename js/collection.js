$(document).ready(function () {
    $(document).on('click', '#back_to_coll_list', function (event) {
        event.preventDefault();
        $('#pageHeaderName').text(` - Collection`);
        $('#collection_list').show();
        getCollectionListTable();
        $('#back_to_coll_list ,#coll_main_container').hide();
        $('.ledger_view_chart_model').hide();
    });

    ///////////////////////////////////////////////Pay Due Start////////////////////////////////////
    $(document).on('click', '.pay-due', function (event) {
        event.preventDefault();

        // Hide and show the appropriate sections
        $('#collection_list').hide();
        $('#back_to_coll_list ,#coll_main_container').show();

        let dataValue = $(this).data('value');
        let dataParts = dataValue.split('_');
        let loan_id = dataParts[0];
        let centre_id = dataParts[1];
        let centre_name = dataParts[2];
        let sub_status = dataParts[3];
        let status = dataParts[4];
        $('#loan_id').val(loan_id)
        $('#pageHeaderName').text(` - Collection - Loan ID: ${loan_id}, Centre ID: ${centre_id}, Centre Name: ${centre_name}`);
        collectionCustomerList(loan_id)
        collectionLoanDetails(loan_id)


        $('#submit_collection').unbind('click').click(function (event) {
            event.preventDefault();
            $(this).attr('disabled', true);
            // Initialize an empty array to hold the collected data
            let collectionData = [];
            let loanTotalAmnt = $('#tot_amt').val().replace(/,/g, '');
            let loanPaidAmnt = $('#paid_amt').val().replace(/,/g, ''); // Parse as float for numerical comparison
            let loanBalance = $('#bal_amt').val().replace(/,/g, '');
            let loanDueAmnt = $('#due_amt').val().replace(/,/g, '');
            let loanPendingAmnt = $('#pending_amt').val().replace(/,/g, '');
            let loanPayableAmnt = $('#payable_amt').val().replace(/,/g, '');
            let loanPenaltyAmnt = $('#penalty').val().replace(/,/g, ''); // Round off payable amount
            let loanFineAmnt = $('#fine_charge').val().replace(/,/g, ''); // Round off chit amount
            // Loop through each row in the customer list table
            $('#customer_list_table tbody tr').each(function () {
                var row = $(this); // Get current row

                // Get values from the table for the current row
                let rowId = row.find('input.total_collection').data('id');
                let individualAmount = row.find('td:nth-child(4)').text().replace(/,/g, '');
                let pendingAmount = row.find('td:nth-child(5)').text().replace(/,/g, '');
                let payableAmount = row.find('td:nth-child(6)').text().replace(/,/g, '');
                let penaltyAmount = row.find('td:nth-child(7)').text().replace(/,/g, '');
                let fineChargeAmount = row.find('td:nth-child(8)').text().replace(/,/g, '');
                let collectionDate = row.find('td:nth-child(9)').text()
                let collectionDue = parseFloat(row.find('input.collection_due').val()) || 0;
                let collectionPenalty = parseFloat(row.find('input.collection_penalty').val()) || 0;
                let collectionFine = parseFloat(row.find('input.collection_fine').val()) || 0;
                let totalCollection = parseFloat(row.find('input.total_collection').val()) || 0;

                // Skip this row if total_collection is empty or zero
                if (totalCollection === 0) {
                    return; // Skip this iteration and move to the next row
                }

                // Add this row's data to the collectionData array
                collectionData.push({
                    id: rowId,
                    individual_amount: individualAmount,
                    pending: pendingAmount,
                    payable: payableAmount,
                    penalty: penaltyAmount,
                    fine_charge: fineChargeAmount,
                    collection_date: collectionDate,
                    collection_due: collectionDue,
                    collection_penalty: collectionPenalty,
                    collection_fine: collectionFine,
                    total_collection: totalCollection
                });
            });

            // Check if there's any valid data to submit
            if (collectionData.length === 0) {
                swalError('Warning', 'Please fill in the total collection.');
                return; // Exit if no rows have data to submit
            } else {
                $('#submit_collection').attr('disabled', false);
            }

            // Send the collected data to the server using AJAX
            $.ajax({
                url: 'api/collection_files/submit_collection.php',
                method: 'POST',
                data: {
                    loan_id: loan_id,
                    status: status,
                    sub_status: sub_status,
                    loanTotalAmnt: loanTotalAmnt,
                    loanPaidAmnt: loanPaidAmnt,
                    loanBalance: loanBalance,
                    loanDueAmnt: loanDueAmnt,
                    loanPayableAmnt: loanPayableAmnt,
                    loanPendingAmnt: loanPendingAmnt,
                    loanPenaltyAmnt: loanPenaltyAmnt,
                    loanFineAmnt: loanFineAmnt,
                    collectionData: collectionData
                },
                success: function (response) {
                    // Ensure the response is parsed correctly
                    $('#submit_collection').attr('disabled', false);
                    response = typeof response === 'string' ? JSON.parse(response) : response;
                    if (response == 1) {
                        swalSuccess('Success', "Collected Successfully");
                        $('#pageHeaderName').text(` - Collection`);
                        $('#collection_list').show();
                        getCollectionListTable();
                        $('#back_to_coll_list ,#coll_main_container').hide();
                    } else {
                        swalError('Warning', 'Failed to save the collection details');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                }
            });
        });


    });
    /////////////////////////////////////////////////Pay Due End////////////////////////////////////////////////
    //////////////////////////////////////////////Fine Start ////////////////////////////////////////////
    $(document).on('click', '.pay-fine', function (e) {
        let loan_id = $(this).attr('value');
        $('#fine_loan_id').val(loan_id);
        $('#fine_form_modal').modal('show');
        collectDate();
        getCustomerList(loan_id)
        getFineFormTable(loan_id)

    });
    $('#fine_form_submit').click(function (event) {
        event.preventDefault();
        let fine_loan_id = $('#fine_loan_id').val();
        let cus_id = $('#add_customer').val();
        let fine_date = $('#fine_date').val();
        let fine_purpose = $('#fine_purpose').val();
        let fine_Amnt = $('#fine_Amnt').val();
        var data = ['fine_loan_id', 'add_customer', 'fine_date', 'fine_purpose', 'fine_Amnt']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });

        if (isValid) {
            $.post('api/collection_files/submit_fine_form.php', { fine_loan_id, cus_id, fine_date, fine_purpose, fine_Amnt }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Fine Added Successfully.');
                    getFineFormTable(fine_loan_id);
                } else {
                    swalError('Error', 'Failed to Add Fine');
                }
            }, 'json');
        }
    })
    $(document).on('click', '.fine-chart', function (e) {
        e.preventDefault(); // Prevent default anchor behavior
        var cus_mapping_id = $(this).attr('data-id'); // Capture data-id from the clicked element
        $('#fine_model').modal('show'); // Show the modal
        fineChartList(cus_mapping_id); // Call the function and pass the cus_mapping_id
    });


    //////////////////////////////////////////////Fine End//////////////////////////////////////////////
    ////////////////////////////////////////////Penalty Chart////////////////////////
    $(document).on('click', '.penalty-chart', function (e) {
        e.preventDefault(); // Prevent default anchor behavior
        var cus_mapping_id = $(this).attr('data-id'); // Capture data-id from the clicked element
        $('#penalty_model').modal('show'); // Show the modal
        penaltyChartList(cus_mapping_id);
    });
    ///////////////////////////////////////////////Penalty cahrt End///////////////////////////////////////////
    //////////////////////////////////////////Due Chart start//////////////////////////////
    $(document).on('click', '.due-chart', function () {
        var cus_mapping_id = $(this).attr('data-id');
        let loan_id = $('#loan_id').val()
        $('#due_chart_model').modal('show');
        dueChartList(cus_mapping_id, loan_id); // To show Due Chart List.
        setTimeout(() => {
            $('.print_due_coll').click(function () {
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
                            type: 'post',
                            cache: false,
                            success: function (html) {
                                $('#printcollection').html(html)
                                // Get the content of the div element
                                var content = $("#printcollection").html();
                            }
                        })
                    }
                })
            })
        }, 1000)
    });
    //////////////////////////Due Chart End/////////////////////////////////////////
    //////////////////////////////Ledger View Start///////////////////////////////////
    $(document).on('click', '.ledgerViewBtn', function (event) {
        event.preventDefault();
        $('.ledger_view_chart_model').show();
        $('#back_to_coll_list').show();
        $('#collection_list').hide();
        let loan_id = $(this).attr('value');
        getLedgerViewChart(loan_id);
    });
    ////////////////////////////////Ledger View End/////////////////////////

    $(document).on('click', '.move_to_error', function () {
        var id = $(this).attr('data-id');
        var loan_id = $(this).attr('value');
        var action="move";   
    Swal.fire({
        title: "Are you sure?",
        text: "Do You Want To Move This Cuatomer To Error?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#009688',
		cancelButtonColor: '#d33',
        confirmButtonText: "Yes",
        cancelButtonText: "No"
    }).then((result) => {
        if (result.isConfirmed) {
            moverToError(id,loan_id,action);
        }
    });
});

    $(document).on('click', '.back_to_collection', function () {
        var id = $(this).attr('data-id');
        var loan_id = $(this).attr('value');
        var action='back';  
        moverToError(id,loan_id,action);
        collectionCustomerList(loan_id);
});


});

function moverToError(id,loan_id,action) {
    console.log("loan idddd",loan_id);
    $.post('api/collection_files/move_to_error.php', { id: id, action: action}, function (response) {
    if(response === 0){
    swalSuccess('Success', 'Customer Moved successfully.');
    console.log("loan id   ",loan_id)
    collectionCustomerList(loan_id);
    }else{
        swalError('Warning', "Not Moved to Error");
    }
     }, 'json');
    
}
$(function () {
    getCollectionListTable();
});

function getCollectionListTable() {
    serverSideTable('#collection_list_table', '', 'api/collection_files/collection_list.php');
}
function closeFineChartModal() {
    $('#fine_form_modal').modal('hide');
    let cus_id = $('#cus_id').val();
    //OnLoadFunctions(cus_id);
}
function getCustomerList(loan_id) {
    $.post('api/collection_files/get_customer_list.php', { loan_id: loan_id }, function (response) {
        let cusOptn = '';
        cusOptn = '<option value="">Select Customer Name</option>';
        response.forEach(val => {
            cusOptn += '<option value="' + val.id + '">' + val.first_name + ' - ' + val.mobile1 + ' - ' + val.areaname + '</option>';
        });
        $('#add_customer').empty().append(cusOptn);
    }, 'json');
}
function collectDate() {
    var today = new Date();
    var day = String(today.getDate()).padStart(2, '0');
    var month = String(today.getMonth() + 1).padStart(2, '0'); // January is 0
    var year = today.getFullYear();

    var currentDate = day + '-' + month + '-' + year;
    $('#fine_date').val(currentDate);
}
function getFineFormTable(loan_id) {
    $.post('api/collection_files/fine_form_list.php', { loan_id }, function (response) {
        let fineColumn = [
            'sno',
            'first_name',
            'fine_date',
            'fine_purpose',
            'fine_charge'
        ];
        appendDataToTable('#fine_form_table', response, fineColumn);
        setdtable('#fine_form_table');

        $('#fine_purpose').val('');
        $('#fine_Amnt').val('');
        $('#add_customer').val('');
        $('#fine_purpose').css('border', '1px solid #cecece');
        $('#fine_Amnt').css('border', '1px solid #cecece');
        $('#add_customer').css('border', '1px solid #cecece');
    }, 'json');
}
var formattedDate = '';
var currentDate = new Date();
var day = ("0" + currentDate.getDate()).slice(-2); // Get day and pad with 0 if needed
var month = ("0" + (currentDate.getMonth() + 1)).slice(-2); // Get month and pad with 0 if needed
var year = currentDate.getFullYear(); // Get the year

var formattedDate = day + '-' + month + '-' + year; // Format as dd-mm-yyyy
function collectionCustomerList(loan_id) {
    $.ajax({
        url: 'api/collection_files/get_customer_details.php',
        type: 'POST',
        dataType: 'json',
        data: {
            loan_id: loan_id,
        },
        success: function (response) {
            var tbody = $('#customer_list_table tbody');
            tbody.empty(); // Clear existing rows

            var hasRows = false;
            var serialNo = 1;
            $.each(response, function (index, item) {
                var isReadOnly = (!item.issue_status || item.issue_status === "" || item.cus_status===12) ? "disabled" : "";
                var individual_amount = item.individual_amount ? item.individual_amount : 0;
                var pending = item.pending ? item.pending : 0;
                var payable = item.payable ? item.payable : 0;
                var penalty = item.penalty ? item.penalty : 0;
                var fine_charge = item.fine_charge ? item.fine_charge : 0;
                var customer_amnt = item.total_cus_amnt ? item.total_cus_amnt : '';
                var dropdownContent = "<div class='dropdown'>" +
                    "<button class='btn btn-outline-secondary' " + (isReadOnly ? "disabled" : "") + "><i class='fa'>&#xf107;</i></button>" +
                    "<div class='dropdown-content'>" +
                    "<a href='#' class='due-chart' data-id='" + item.id + "'>Due Chart</a>" +
                    "<a href='#' class='penalty-chart' data-id='" + item.id + "'>Penalty Chart</a>" +
                    "<a href='#' class='fine-chart' data-id='" + item.id + "'>Fine Chart</a>" +
                    "</div>" +
                    "</div>";
                    if(item.cus_status === 12){
                        var action = "<div class='dropdown'>" +
                    "<button class='btn btn-outline-secondary' "  + "><i class='fa'>&#xf107;</i></button>" +
                    "<div class='dropdown-content'>" +
                    "<a href='#' class='back_to_collection' data-id='" + item.id + "' value='" + item.loan_id + "'>Back To Collection</a>" +
                    "</div>" +
                    "</div>";
                    }else{
                        var action = "<div class='dropdown'>" +
                    "<button class='btn btn-outline-secondary' " + (isReadOnly ? "disabled" : "") + "><i class='fa'>&#xf107;</i></button>" +
                    "<div class='dropdown-content'>" +
                    "<a href='#' class='move_to_error' data-id='" + item.id + "' value='" + item.loan_id + "'>Move To Error</a>" +
                    "</div>" +
                    "</div>";
                    }
                var row = '<tr>' +
                    '<td>' + serialNo  + '</td>' +
                    '<td>' + item.cus_id + '</td>' +
                    '<td>' + item.first_name + '</td>' +
                    '<td>' + moneyFormatIndia(individual_amount) + '</td>' +
                    '<td>' + moneyFormatIndia(pending) + '</td>' +
                    '<td>' + moneyFormatIndia(payable) + '</td>' +
                    '<td>' + moneyFormatIndia(penalty) + '</td>' +
                    '<td>' + moneyFormatIndia(fine_charge) + '</td>' +
                    '<td>' + formattedDate + '</td>' +
                    // Input fields for collection due amount, penalty, and fine

                    '<td><input type="number" class="form-control collection_due" data-id="' + item.id + '" data-individual-amount="' + item.total_cus_amnt + '" value="" ' + isReadOnly + '></td>' +
                    '<td><input type="number" class="form-control collection_penalty" data-id="' + item.id + '" data-penalty-amount="' + item.penalty + '" value=" " ' + isReadOnly + '></td>' +
                    '<td><input type="number" class="form-control collection_fine" data-id="' + item.id + '" data-fine-amount="' + item.fine_charge + '" value=" " ' + isReadOnly + '></td>' +
                    // Calculated total collection
                    '<td><input type="number" class="form-control total_collection" data-id="' + item.id + '" value=" " readonly></td>' +
                    '<td>' + dropdownContent + '</td>' +
                    '<td>' + action + '</td>' +
                    '</tr>';

                tbody.append(row);
                serialNo++;
                hasRows = true;
            });

            if (!hasRows) {
                tbody.append('<tr><td colspan="13">No data available</td></tr>'); // Adjust colspan as necessary
            }
            setDropdownScripts();
       
            // Bind input changes for calculating Total Collection
            $('input.collection_due, input.collection_penalty, input.collection_fine').on('input', function () {
                var rowId = $(this).data('id');  // Use item.id here instead of index
                var collectionDue = parseFloat($('input.collection_due[data-id="' + rowId + '"]').val()) || 0;
                var collectionPenalty = parseFloat($('input.collection_penalty[data-id="' + rowId + '"]').val()) || 0;
                var collectionFine = parseFloat($('input.collection_fine[data-id="' + rowId + '"]').val()) || 0;

                // Calculate Total Collection and update it
                var totalCollection = collectionDue + collectionPenalty + collectionFine;
                $('input.total_collection[data-id="' + rowId + '"]').val(totalCollection);

                // Validation: Ensure collection_due is not greater than individual_amount
                var payable = parseFloat($('input.collection_due[data-id="' + rowId + '"]').data('individual-amount'));
                var overPay = moneyFormatIndia(payable);
                var penaltyAmount = parseFloat($('input.collection_penalty[data-id="' + rowId + '"]').data('penalty-amount'));
                var fineAmount = parseFloat($('input.collection_fine[data-id="' + rowId + '"]').data('fine-amount'));

                if (collectionDue > payable) {
                    // Show warning using Swal or any other method
                    swalError('Warning', 'Collection Due exceeds the Payable Amount: ' + overPay);
                    // Optionally, reset the collection_due field to its previous value or clear it
                    $('input.collection_due[data-id="' + rowId + '"]').val("");
                    $('input.total_collection[data-id="' + rowId + '"]').val("");
                }

                if (collectionPenalty > penaltyAmount) {
                    // Show warning using Swal or any other method
                    swalError('Warning', "Penalty Exceeds Penalty Amount");
                    // Optionally, reset the collection_due field to its previous value or clear it
                    $('input.collection_penalty[data-id="' + rowId + '"]').val("");
                    $('input.total_collection[data-id="' + rowId + '"]').val("");
                }
                if (collectionFine > fineAmount) {
                    // Show warning using Swal or any other method
                    swalError('Warning', "Fine Exceeds Fine Amount");
                    // Optionally, reset the collection_due field to its previous value or clear it
                    $('input.collection_fine[data-id="' + rowId + '"]').val("");
                    $('input.total_collection[data-id="' + rowId + '"]').val("");
                }
            });
            collectionLoanDetails(loan_id);
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error: ' + status + error);
        }
    });
}
function collectionLoanDetails(loan_id) {
    $.ajax({
        url: 'api/collection_files/collection_details.php',
        type: 'POST',
        data: {
            loan_id: loan_id,
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                // Populate the form fields with the fetched and rounded data
                $('#tot_amt').val(moneyFormatIndia(response.total_amount_calc));
                $('#paid_amt').val(moneyFormatIndia(response.total_paid));
                $('#bal_amt').val(moneyFormatIndia(response.balance));
                $('#due_amt').val(moneyFormatIndia(response.due_amount_calc));
                $('#pending_amt').val(moneyFormatIndia(response.total_pending));
                $('#payable_amt').val(moneyFormatIndia(response.total_payable));
                $('#penalty').val(moneyFormatIndia(response.penalty));
                $('#fine_charge').val(moneyFormatIndia(response.fine_charge));

            } else {
                console.error('Required data fields are missing in the response.');
                swalError('Warning', 'Failed to retrieve the required details.');
            }

        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
            swalError('Error', 'An error occurred while fetching payment details.');
        }
    });
}
function closeChartsModal() {
    $('#due_chart_model').modal('hide');
    $('#penalty_model').modal('hide');
    $('#fine_model').modal('hide');
}
//Fine Chart List
function fineChartList(cus_mapping_id) {
    $.ajax({
        url: 'api/collection_files/get_fine_chart_list.php',
        data: { 'cus_mapping_id': cus_mapping_id },
        type: 'post',
        cache: false,
        success: function (response) {
            $('#fine_chart_table_div').empty()
            $('#fine_chart_table_div').html(response)
        }
    });//Ajax End.
}
//Penalty chart
function penaltyChartList(cus_mapping_id) {
    $.ajax({
        url: 'api/collection_files/get_penalty_chart_list.php',
        data: { 'cus_mapping_id': cus_mapping_id },
        type: 'post',
        cache: false,
        success: function (response) {
            $('#penalty_chart_table_div').empty()
            $('#penalty_chart_table_div').html(response)
        }
    });//Ajax End.
}
////////////////////Due Chart List
function dueChartList(cus_mapping_id, loan_id) {
    $.ajax({
        url: 'api/collection_files/get_due_chart_list.php',
        data: { 'cus_mapping_id': cus_mapping_id },
        type: 'post',
        cache: false,
        success: function (response) {
            $('#due_chart_table_div').empty();
            $('#due_chart_table_div').html(response);
        }
    }).then(function () {
        $.post('api/collection_files/get_due_method_name.php', { loan_id: loan_id, cus_mapping_id: cus_mapping_id }, function (response) {
            $('#dueChartTitle').text('Due Chart ( ' + response['due_month'] + ' - ' + response['loan_type'] + ' ) - Customer ID: '
                + response['cus_id'] + ' - Customer Name: ' + response['cus_name'] + ' - Loan ID: ' + response['loan_id']
                + ' - Centre ID: ' + response['centre_id'] + ' - Centre Name: ' + response['centre_name']);
        }, 'json');
    })

}
function getLedgerViewChart(loan_id) {
    $.post('api/collection_files/ledger_view_data.php', { loan_id: loan_id }, function (response) {
        $('#ledger_view_table_div').empty();
        $('#ledger_view_table_div').html(response);

    });
}
