//On Load function 
$(document).ready(function () {
    $(document).on('click', '.edit-loan-issue', function () {
        let id = $(this).attr('value'); //Customer Profile id From List page.
        $('#loan_id').val(id);
        swapTableAndCreation();
        getAutoGenDocId('')
        getCustomerList()
        getDocCreationTable()
    });

    $('#back_btn').click(function () {
        swapTableAndCreation();
    });
    $('input[name=loan_issue_type]').click(function () {
        let loanIssueType = $(this).val();
        if (loanIssueType == 'loandoc') {
            $('#documentation_form').show(); $('#loan_issue_form').hide();
        } else if (loanIssueType == 'loanissue') {
            $('#documentation_form').hide(); $('#loan_issue_form').show();
            callLoanCaculationFunctions();
        }
    })

    ////////////////////////////////////////////Doc Info Start//////////////////////////////////////////////
    $('#submit_doc_info').click(function (event) {
        event.preventDefault();
        let loan_id = $('#loan_id').val();
        let doc_id = $('#doc_id').val();
        let customer_name = $('#customer_name').val();
        let doc_name = $('#doc_name').val();
        let doc_type = $('#doc_type').val();
        let doc_count = $('#doc_count').val();
        let remark = $('#remark').val();
        let doc_upload = $('#doc_upload')[0].files[0];
        let doc_upload_edit = $('#doc_upload_edit').val();

        var data = ['doc_name', 'doc_type', 'doc_id', 'customer_name', 'doc_count']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (isValid) {
            let docInfo = new FormData();
            docInfo.append('loan_id', loan_id);
            docInfo.append('doc_id', doc_id);
            docInfo.append('customer_name', customer_name);
            docInfo.append('doc_name', doc_name);
            docInfo.append('doc_type', doc_type);
            docInfo.append('doc_count', doc_count);
            docInfo.append('remark', remark);
            docInfo.append('doc_upload', doc_upload);
            docInfo.append('doc_upload_edit', doc_upload_edit);
            $.ajax({
                url: 'api/loan_issue_files/submit_document_info.php',
                type: 'post',
                data: docInfo,
                contentType: false,
                processData: false,
                cache: false,
                success: function (response) {
                    if (response == '1') {
                        swalSuccess('Success', 'Document Info Added Successfully')
                    } else {
                        swalError('Alert', 'Failed')
                    }
                    getDocCreationTable();
                }
            });
        }
    });
    $(document).on('click', '.docDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this document?', deleteDocInfo, id);
    });
    ///////////////////////////////////////////Doc Info End/////////////////////////////////////////////

    /////////////////////////////////////////Loan Issue submission ///////////////////////////////////////

    $('#submit_loan_issue').click(function (event) {
        event.preventDefault();

        // Initialize an array to hold the loan issue data for submission
        var loanIssueData = [];

        // Collect the additional loan-related fields
        let loan_id = $('#loan_id').val();
        let loan_amnt_calc = $('#loan_amnt_calc').val().replace(/,/g, '');
        let loan_date = $('#loan_date_calc').val();
        let due_startdate_calc = $('#due_startdate_calc').val();
        let maturity_date_calc = $('#maturity_date_calc').val();
        var isValid = true;

        // Validate each field
        if (!validateField(loan_date, 'loan_date')) {
            isValid = false;
        }
        if (!validateField(due_startdate_calc, 'due_startdate_calc')) {
            isValid = false;
        }

        // Loop through all the rows in the issue info table to collect loan issue data
        $('#issue_info_table tbody tr').each(function () {
            var row = $(this);

            // Get the values from the row (checkbox, payment type, issue type, issue amount)
            var id = row.find('.form-check-input').data('id');
            var paymentType = row.find('.payment-type').val();
            var issueType = row.find('.issue-type').val();
            var issueAmount = row.find('.issue-amount').val().replace(/,/g, '');
            var netCashCalc = row.find('.netcash').text().replace(/,/g, ''); // Remove commas
            var issueDate = formattedDate;
            // Only collect data if the checkbox is checked
            if (row.find('.form-check-input').is(':checked')) {
                loanIssueData.push({
                    id: id,
                    payment_type: paymentType,
                    issue_type: issueType,
                    issue_amount: issueAmount,
                    net_cash: netCashCalc,
                    issue_date: issueDate

                });
            }
        });

        // Ensure that there is data to submit
        if (loanIssueData.length === 0) {
            swalError('Warning', 'No customer is selected');
            return;
        }

        // Prepare the data object to send to the backend
        var dataToSubmit = {
            loan_id: loan_id,
            loan_amnt_calc: loan_amnt_calc,
            loan_date: loan_date,
            due_startdate_calc: due_startdate_calc,
            maturity_date_calc: maturity_date_calc,
            loan_issue_data: loanIssueData // The array of loan issues
        };
        if (isValid) {
            // Send the data to the backend via AJAX
            $.post('api/loan_issue_files/submit_loan_issue.php', dataToSubmit, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Loan Issued Successfully');
                    swapTableAndCreation(); // Swap table content or perform any action you need
                    getLoanIssueTable(); // Refresh or get the updated loan issue table
                } else {
                    swalError('Warning', 'Loan Issue Failed.');
                }
            }).fail(function (xhr, status, error) {
                swalError('Error', 'An error occurred while submitting the loan issue.');
                console.error('AJAX Error:', status, error);
            });
        }
    });



    //////////////////////////////////////////////////////////////////////////////////////////////////////
});
$(function () {
    getLoanIssueTable();
});

function getLoanIssueTable() {
    serverSideTable('#loan_issue_table', '', 'api/loan_issue_files/loan_issue_list.php');
}
function swapTableAndCreation() {
    if ($('.loanissue_table_content').is(':visible')) {
        $('.loanissue_table_content').hide();
        $('#loan_issue_content').show();
        $('#back_btn').show();

    } else {
        $('.loanissue_table_content').show();
        $('#loan_issue_content').hide();
        $('#back_btn').hide();
        $('#documentation').trigger('click');
    }
}
function getAutoGenDocId(id) {
    $.post('api/loan_issue_files/get_doc_id.php', { id }, function (response) {
        $('#doc_id').val(response);
    }, 'json');
}
function getCustomerList() {
    let loan_id = $('#loan_id').val()
    $.post('api/loan_issue_files/get_loancustomer_list.php', { loan_id }, function (response) {
        let cusOptn = '';
        cusOptn = '<option value="">Select Customer Name</option>';
        response.forEach(val => {
            cusOptn += '<option value="' + val.id + '">' + val.first_name + ' - ' + val.mobile1 + ' - ' + val.areaname + '</option>';
        });
        $('#customer_name').empty().append(cusOptn);
    }, 'json');
}

function getDocCreationTable() {
    let loan_id = $('#loan_id').val()
    $.post('api/loan_issue_files/doc_info_list.php', { loan_id }, function (response) {
        let docInfoColumn = [
            "sno",
            "doc_id",
            "cus_id",
            "first_name",
            "doc_name",
            "doc_type",
            "count",
            "remark",
            "upload",
            "action"
        ]
        appendDataToTable('#document_info', response, docInfoColumn);
        setdtable('#document_info')
        $('#documentation_form input').each(function () {
            var id = $(this).attr('id');
            if (id !== 'doc_id' && id !== 'loan_id') {
                $(this).val('');
            }
        });
        $('#documentation_form textarea').val('');
        $('#documentation_form input').css('border', '1px solid #cecece');
        $('#documentation_form select').css('border', '1px solid #cecece');
        $('#documentation_form select').each(function () {
            $(this).val($(this).find('option:first').val());
        });
    }, 'json');
}
function deleteDocInfo(id) {
    $.post('api/loan_issue_files/delete_doc_info.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Doc Info Deleted Successfully');
            getDocCreationTable();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}
function callLoanCaculationFunctions() {
    loanInfo();
    issueList();
}
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

        } else if (due_method == 'Weekly' || due_method == '2') {//if Due method is weekly then calculate maturity by week

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

function loanInfo() {
    let loan_id = $('#loan_id').val();
    $.post('api/loan_issue_files/loan_issue_data.php', { loan_id }, function (response) {
        $('#centre_id').val(response[0].centre_id);
        $('#centre_no').val(response[0].centre_no);
        $('#centre_name').val(response[0].centre_name);
        $('#loan_id_calc').val(response[0].loan_id);
        $('#cus_data').val(response[0].cus_data);
        $('#centre_mobile').val(response[0].mobile1);
        $('#area').val(response[0].areaname);
        $('#branch').val(response[0].branch_name);
        $('#loan_category_calc').val(response[0].loan_category);
        $('#loan_amnt_calc').val(moneyFormatIndia(response[0].loan_amount_calc));
        $('#principal_amnt_calc').val(moneyFormatIndia(response[0].principal_amount_calc));
        $('#interest_amnt_calc').val(moneyFormatIndia(response[0].intrest_amount_calc));
        $('#total_amnt_calc').val(moneyFormatIndia(response[0].total_amount_calc));
        $('#due_amnt_calc').val(moneyFormatIndia(response[0].due_amount_calc));
        $('#doc_charge_calculate').val(moneyFormatIndia(response[0].document_charge_cal));
        $('#processing_fees_calculate').val(moneyFormatIndia(response[0].processing_fees_cal));
        $('#net_cash_calc').val(moneyFormatIndia(response[0].net_cash_calc));
        $('#loan_date_calc').val(response[0].loan_date);

        // Check if loan_date is not empty and not null
        if (response[0].loan_date !== '' && response[0].loan_date !== null) {
            // Make 'Due Start Date' and 'Maturity Date' read-only
            $('#due_startdate_calc').prop('readonly', true);
            $('#maturity_date_calc').prop('readonly', true);
        } else {
            // If loan_date is empty or null, these fields can be editable
            $('#due_startdate_calc').prop('readonly', false);
        }


        $('#due_startdate_calc').val(response[0].due_start);
        $('#maturity_date_calc').val(response[0].due_end);
        $('#due_period_calc').val(response[0].due_period);
        $('#profit_type_calc').val(response[0].profit_type);
        $('#due_method_calc').val(response[0].due_month);
        $('#scheme_day_calc').val(response[0].scheme_day);
        $('#due_startdate_calc').attr('min', response[0].loan_date);

        let path = "uploads/loan_entry/cus_pic/";
        $('#per_pic').val(response[0].pic);
        var img = $('#imgshow');
        img.attr('src', path + response[0].pic);

    }, 'json');
}
var formattedDate = '';
var currentDate = new Date();
var day = ("0" + currentDate.getDate()).slice(-2); // Get day and pad with 0 if needed
var month = ("0" + (currentDate.getMonth() + 1)).slice(-2); // Get month and pad with 0 if needed
var year = currentDate.getFullYear(); // Get the year

var formattedDate = day + '/' + month + '/' + year; // Format as dd-mm-yyyy

function issueList() {
    let loan_id = $('#loan_id').val();

    $.ajax({
        url: 'api/loan_issue_files/get_issue_details.php',
        type: 'POST',
        dataType: 'json',
        data: {
            loan_id: loan_id,
        },
        success: function (response) {
            var tbody = $('#issue_info_table tbody');
            tbody.empty(); // Clear existing rows

            var hasRows = false;

            $.each(response, function (index, item) {
                var paymentTypeOptions = `
                    <select class="form-control payment-type" data-id="${item.id}" ${item.issue_status === 'Issued' ? 'disabled' : ''}>
                        <option value="1" ${item.payment_type == 1 ? 'selected' : ''}>Single</option>
                        <option value="2" ${item.payment_type == 2 ? 'selected' : ''}>Split</option>
                    </select>
                `;
                var issueTypeOptions = `
                    <select class="form-control issue-type" data-id="${item.id}" ${item.issue_status === 'Issued' ? 'disabled' : ''}>
                        <option value="1" ${item.issue_type == 1 ? 'selected' : ''}>Cash</option>
                        <option value="2" ${item.issue_type == 2 ? 'selected' : ''}>Bank Transfer</option>
                    </select>
                `;
                var netcashCalc = item.individual_amount ? moneyFormatIndia(item.individual_amount) : '';
                var issuedAmount = item.issued_amount ? moneyFormatIndia(item.issued_amount) : '0';
                var loan_date = item.loan_date; // Get the loan date
                if (loan_date === '' || loan_date === null || loan_date === undefined) {
                    var status = 'In Issue';
                } else {
                    var status = (item.issue_status === 'Issued' ? 'Issued' : 'Pending');
                }

                var actionHtml = `<input type="checkbox" class="form-check-input" data-id="${item.id}" ${item.issue_status === 'Issued' ? 'disabled' : ''} />`; // Checkbox disabled if Issued
                let issueAmountInput;
                let remainingAmount = parseFloat(item.individual_amount) - parseFloat(item.issued_amount || 0);
                let Amount = moneyFormatIndia(remainingAmount)
                issueAmountInput = `
                        <input type="text" class="form-control issue-amount" 
                            value="${Amount}" readonly data-id="${item.id}"   ${item.issue_status === 'Issued' ? 'readonly' : ''}/>
                    `;

                var row = '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + item.cus_id + '</td>' +
                    '<td>' + item.first_name + '</td>' +
                    '<td class="netcash">' + netcashCalc + '</td>' +
                    '<td class="issued-amount">' + issuedAmount + '</td>' +
                    '<td>' + paymentTypeOptions + '</td>' +  // Payment Type dropdown
                    '<td>' + issueTypeOptions + '</td>' +    // Issue Type dropdown
                    '<td>' + formattedDate + '</td>' +         // Current date in Issue Date column
                    '<td>' + issueAmountInput + '</td>' +    // Issue Amount input field
                    '<td>' + actionHtml + '</td>' +
                    '<td>' + status + '</td>' +
                    '</tr>';

                tbody.append(row);
                hasRows = true;
            });

            if (!hasRows) {
                tbody.append('<tr><td colspan="10">No data available</td></tr>'); // Update colspan to 10
            }
            $('.payment-type').on('change', function () {
                var id = $(this).data('id');
                var paymentType = $(this).val();
                var row = $(this).closest('tr');
                var issueAmountInput;

                // Get the net cash and issued amount for the current row
                var netCashAmount = parseFloat(row.find('.netcash').text().replace(/[^0-9.-]+/g, ""));
                var issuedAmount = parseFloat(row.find('.issued-amount').text().replace(/[^0-9.-]+/g, ""));

                // Find if the issue status is 'Issued' from the current row
                var issueStatus = row.find('.issue-status').text().trim(); // Assuming issue status is in a cell with class "issue-status"

                if (paymentType == 1) { // Single payment
                    let remainingAmount = netCashAmount - issuedAmount;
                    let Amount = moneyFormatIndia(remainingAmount)
                    issueAmountInput = `
                        <input type="text" class="form-control issue-amount" 
                            value="${Amount}" readonly data-id="${id}" />
                    `;
                } else { // Split payment
                    // Clear the value for split payment, allowing the user to enter a new value
                    issueAmountInput = `
                        <input type="number" class="form-control issue-amount" 
                            value="" data-id="${id}" ${issueStatus === 'Issued' ? 'readonly' : ''} />
                    `;
                }

                // Update the Issue Amount input field
                row.find('.issue-amount').replaceWith(issueAmountInput);
            });


            // Checkbox change event
            $('.form-check-input').on('change', function () {
                var isChecked = $(this).is(':checked');
                var id = $(this).data('id');
                var paymentType = $('.payment-type[data-id="' + id + '"]').val();
                var issueType = $('.issue-type[data-id="' + id + '"]').val();
                var issueAmount = $('.issue-amount[data-id="' + id + '"]').val();
                var loan_date = $('#loan_date_calc').val(); // Get the loan date

                if (paymentType === '' || issueType === '' || issueAmount === '') {
                    swalError('Warning', "Please fill Issue Amount!");
                    $(this).prop('checked', false);
                    return;
                }

                if (!loan_date) {
                    loan_date = $('#loan_date_calc').val(formattedDate);  // Use the current formatted date
                }
            });

            $(document).on('input', '.issue-amount', function () {
                var enteredAmount = parseFloat($(this).val());
                var netCashAmount = parseFloat($(this).closest('tr').find('td').eq(3).text().replace(/[^0-9.-]+/g, "")); // Net Cash column value
                var id = $(this).data('id');
                var $this = $(this);
            
                function checkBalance(id) {
                    $.ajax({
                        url: 'api/loan_issue_files/get_balance_amount.php',
                        type: 'POST',
                        data: { "id": id },
                        dataType: 'json',
                        success: function (response) {
                            if (response && response.issue_amount !== undefined) {
                                let balanceAmount = response.issue_amount;
                                let remainingAmount = netCashAmount - balanceAmount;
                                let moneyAmount = moneyFormatIndia(remainingAmount);
                                if (balanceAmount === '0' || balanceAmount === 0) {
                                    if (enteredAmount > netCashAmount) {
                                        swalError('Warning', 'Entered Issue Amount is greater than Net Cash');
                                        $this.val('');
                                    }
                                } else {
                                    if (enteredAmount > remainingAmount) {
                                        swalError('Warning', 'Entered Issue Amount is greater than Balance Amount ' + moneyAmount);
                                        $this.val('');
                                    }
                                }
                            } else {
                                console.error('Balance amount not found in response');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                        }
                    });
                }
            
                checkBalance(id);
            });
            
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error: ' + status + error);
        }
    });
}
