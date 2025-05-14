//On Load function 
$(document).ready(function () {
    $(document).on('click', '.edit-loan-issue', function () {
        $('#submit_doc_info').prop('disabled', false);
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
        $('#submit_loan_issue').prop('disabled', false);
        $('#submit_doc_info').prop('disabled', false);
        let loanIssueType = $(this).val();
        if (loanIssueType == 'loandoc') {
            $('#documentation_form').show(); $('#loan_issue_form').hide();
        } else if (loanIssueType == 'loanissue') {
            $('#documentation_form').hide(); $('#loan_issue_form').show();
            callLoanCaculationFunctions();
        }
    })

    $("#customer_mapping").change(function () {
        let customer_mapping = $(this).val();
        getAddCustomerList(customer_mapping);
    });

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
            $('#submit_doc_info').prop('disabled', true);
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
                    $('#submit_doc_info').prop('disabled', false);
                    getAutoGenDocId('');
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
        $('#refresh_cal').trigger('click'); //For calculate once again if user missed to refresh calculation
        // Initialize an array to hold the loan issue data for submission
        var loanIssueData = [];

        // Collect the additional loan-related fields
        let loan_id = $('#loan_id').val();
        let loan_amnt_calc = $('#loan_amnt_calc').val().replace(/,/g, '');
        let loan_amount = $('#loan_amount').val().replace(/,/g, '');
        let loan_date = $('#loan_date_calc').val();
        let due_startdate_calc = $('#due_startdate_calc').val();
        let maturity_date_calc = $('#maturity_date_calc').val();
        let total_amnt_calc = $('#total_amnt_calc').val().replace(/,/g, '');
        let principal_amnt_calc = $('#principal_amnt_calc').val().replace(/,/g, '');
        let interest_amnt_calc = $('#interest_amnt_calc').val().replace(/,/g, '');
        let due_amnt_calc = $('#due_amnt_calc').val().replace(/,/g, '');
        let doc_charge_calculate = $('#doc_charge_calculate').val().replace(/,/g, '');
        let processing_fees_calculate = $('#processing_fees_calculate').val().replace(/,/g, '');
        let net_cash_calc = $('#net_cash_calc').val().replace(/,/g, '');
        var isValid = true;

        // Validate each field
        if (!validateField(loan_date, 'loan_date')) {
            isValid = false;
        }
        if (!validateField(due_startdate_calc, 'due_startdate_calc')) {
            isValid = false;
        }

        // Customer Mapping Count validation
        let submitCurrentCustomerCount = $('#issue_info_table tbody tr').length;
        let total_cus = parseInt($('#total_cus').val() || 0); // Assuming total_cus is in an input field

        if (submitCurrentCustomerCount < total_cus) {
            swalError('Warning', 'The Number of Mapped Customers Is Less Than The Total Number Of Customers.');
            return; // Stop the process if mapping count is insufficient
        }

        // Loop through all the rows in the issue info table to collect loan issue data
        $('#issue_info_table tbody tr').each(function () {
            var row = $(this);

            // Get the values from the row (checkbox, payment type, issue type, issue amount)
            var id = row.find('.form-check-input').data('id');
            var cusId = row.find('.cus_id').text();
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
                    issue_date: issueDate,
                    cus_id: cusId

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
            loan_amount: loan_amount,
            loan_date: loan_date,
            due_startdate_calc: due_startdate_calc,
            maturity_date_calc: maturity_date_calc,
            loan_issue_data: loanIssueData, // The array of loan issues
            principal_amnt_calc: principal_amnt_calc,
            interest_amnt_calc: interest_amnt_calc,
            total_amnt_calc: total_amnt_calc,
            due_amnt_calc: due_amnt_calc,
            doc_charge_calculate: doc_charge_calculate,
            processing_fees_calculate: processing_fees_calculate,
            net_cash_calc: net_cash_calc,
            total_cus: total_cus

        };
        if (isValid) {
            $('#submit_loan_issue').prop('disabled', true);
            // Send the data to the backend via AJAX
            $.post('api/loan_issue_files/submit_loan_issue.php', dataToSubmit, function (response) {
                $('#submit_doc_info').prop('disabled', false);
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

    $(document).on('click', '.cusMapDeleteBtn', function () {
        let id = $(this).data('id'); // Get the customer ID from the button
        let loan_id = $(this).data('loan_id');
        // Show the confirmation dialog
        let TableRowVal = {
            'id': id,
            'loan_id': loan_id
        };

        let issueAmountText = $(this).closest('tr').find('td:nth-child(6)').text(); // 5 = Issue Amount column
        let issueAmount = parseFloat(issueAmountText.replace(/,/g, '')) || 0;

        if (issueAmount > 0) {
            swalError('Warning', 'Customer mapping cannot be deleted because the loan amount is issued');
        } else {
            // Show the confirmation dialog
            swalConfirm('Delete', 'Do you want to remove this customer mapping?', removeCusMap, TableRowVal, '');
        }
    });

    $('#submit_cus_map').click(function (event) {
        event.preventDefault(); // Prevent the default form submission
        let loan_id_calc = $('#loan_id_calc').val();
        let centre_id = $('#centre_id').val();
        let add_customer = $('#add_customer').val().trim(); // Trim to remove any extra spaces
        let customer_mapping = $('#customer_mapping').val().trim();
        let total_cus = $('#total_cus').val().trim(); // Total members limit
        let designation = $('#designation').val().trim(); // Get the designation value
        let customer_amount = parseFloat($('#customer_amount').val()) || 0;
        let customerLoanAmount = parseFloat($('#customerLoanAmount').val()) || 0;
        let loan_amount = parseFloat($('#loan_amount').val().replace(/,/g, '')) || 0;

        var isValid = true;
        // Validate fields
        if (!add_customer) {
            validateField(add_customer, 'add_customer');
            isValid = false;
        }
        if (!customer_mapping) {
            validateField(customer_mapping, 'customer_mapping');
            isValid = false;
        }
        if (!total_cus) {
            swalError('Warning', 'Please fill the total members.');
            isValid = false;
        }
        if (!centre_id) {
            swalError('Warning', 'Please select the centre ID.');
            isValid = false;
        }
        if (!customer_amount) {
            swalError('Warning', 'Please fill the Customer Amount.');
            isValid = false;
        }
        // Proceed only if all fields are valid
        if (isValid) {
            // Check if the number of customers already mapped is greater than or equal to total_cus
            let currentCustomerCount = $('#issue_info_table tbody tr').length;
            if (currentCustomerCount >= total_cus) {
                swalError('Warning', 'The Customer Mapping Limit is Exceed.');
                $('#add_customer').val('');
                $('#designation').val('');
                $('#customer_mapping').val('');
                $('#customer_amount').val('');
                return; // Stop the process if customer count exceeds total_cus
            }

            // Calculate the new total after adding this customer
            let expectedTotal = customerLoanAmount + customer_amount;

            // Check if it exceeds the total loan limit
            if (expectedTotal > loan_amount) {
                swalError('Warning', 'Total Customer Amount exceeds the allowed limit.');
                return; // Stop the process
            }

            var totalCustomerAmount = 0;
            var loanAmount = $('#loan_amt_calc').val();
            var loanAmountCalc = parseFloat(loanAmount.replace(/,/g, '')) || 0;

            $('#issue_info_table tbody tr').each(function () {
                var cus_mapping = parseFloat($(this).find('td:nth-child(4)').text().replace(/,/g, '')) || 0;
                totalCustomerAmount += cus_mapping;
            });

            totalCustomerAmount += customer_amount;
            let increasedCustomerCount = currentCustomerCount + 1;

            // FINAL CHECK
            if (totalCustomerAmount !== loanAmountCalc && increasedCustomerCount == total_cus) {
                swalError('Warning', 'Total Customer Amount does not match Loan Amount Calculation!');
                return;
            }

            var proc_type = $('#proc_type').val();
            var int_rate = parseFloat($('#interest_rate_calc').val());
            var due_period = parseFloat($('#due_period_calc').val());
            let doc_charge = parseFloat($('#doc_charge_calc').val());
            let proc_fee = parseFloat($('#processing_fees_calc').val());

            if (customer_amount && int_rate && due_period) {
                let benefit_method = $('#profit_method_calc').val();
                let result;
                if (benefit_method == 1 || benefit_method === 'Pre Benefit') {
                    result = getLoanPreInterest(customer_amount, int_rate, due_period, doc_charge, proc_fee, proc_type);
                } else if (benefit_method == 2 || benefit_method === 'After Benefit') {
                    result = getLoanAfterInterest(customer_amount, int_rate, due_period, doc_charge, proc_fee, proc_type);
                } else {
                    swalError('Warning', 'Kindly fill the calculation fields.');
                    return;
                }

                // Accumulate
                loan = result.loan;
                principal = result.principal;
                interest = result.interest;
                interestDiff = result.interestDiff;
                total = result.total;
                due = result.due;
                dueDiff = result.dueDiff;
                doc = result.doc;
                docDiff = result.docDiff;
                proc = result.proc;
                procDiff = result.procDiff;
                net = result.net;
            }

            $.post('api/loan_issue_files/submit_add_cus_mappings.php', {
                add_customer: add_customer,
                centre_id: centre_id,
                loan_id_calc: loan_id_calc,
                customer_mapping: customer_mapping,
                total_cus: total_cus,
                designation: designation,
                intrest_amount: interest,
                principle: principal,
                net_cash: net,
                processingfees: proc,
                documentcharge: doc,
                due_amount: due,
                customer_amount: customer_amount
            }, function (response) {
                if (response.result == 4) {
                    swalError('Warning', response.message); // Customer already mapped
                }
                issueList(); // Refresh the table
                // Clear fields after success
                $('#add_customer').val('');
                $('#designation').val('');
                $('#customer_mapping').val('');
                $('#customer_amount').val('');
                $('#customer_mapping, #add_customer').css('border', '1px solid #CECECE');
            }, 'json');
        }
    });

    $('#refresh_cal').click(function () {

        // Reset
        $('.int-diff').text('*');
        $('.due-diff').text('*');
        $('.doc-diff').text('*');
        $('.proc-diff').text('*');
        $('.refresh_loan_calc').val('');

        let int_rate = parseFloat($('#interest_rate_calc').val()) || 0;
        let due_period = parseInt($('#due_period_calc').val()) || 0;
        let doc_charge = parseFloat($('#doc_charge_calc').val()) || 0;
        let proc_fee = parseFloat($('#processing_fees_calc').val()) || 0;
        let proc_type = $('#proc_type').val();
        let totalCus = parseInt($('#total_cus').val()) || 0;
        let cusMappingtable = $('#issue_info_table tbody tr').length;

        if (totalCus === 0 || cusMappingtable === 0 || totalCus !== cusMappingtable) {
            swalError('Warning', 'Please Map The Customers Before Calculating.');
            return;
        }

        // Accumulators
        let total = {
            loan: 0,
            principal: 0,
            interest: 0,
            interestDiff: 0,
            total: 0,
            due: 0,
            dueDiff: 0,
            doc: 0,
            docDiff: 0,
            proc: 0,
            procDiff: 0,
            net: 0
        };

        $('#issue_info_table tbody tr').each(function () {
            let loan_amt = parseFloat($(this).find('td:nth-child(4)').text()) || 0;
            if (loan_amt && int_rate && due_period) {
                let benefit_method = $('#profit_method_calc').val();
                let result;
                if (benefit_method == 1 || benefit_method === 'Pre Benefit') {
                    result = getLoanPreInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee, proc_type);
                } else if (benefit_method == 2 || benefit_method === 'After Benefit') {
                    result = getLoanAfterInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee, proc_type);
                } else {
                    swalError('Warning', 'Kindly fill the calculation fields.');
                    return;
                }

                // Accumulate
                total.loan += result.loan;
                total.principal += result.principal;
                total.interest += result.interest;
                total.interestDiff += result.interestDiff;
                total.total += result.total;
                total.due += result.due;
                total.dueDiff += result.dueDiff;
                total.doc += result.doc;
                total.docDiff += result.docDiff;
                total.proc += result.proc;
                total.procDiff += result.procDiff;
                total.net += result.net;
            }
        });

        // Fill totals into final fields
        $('#loan_amnt_calc').val(moneyFormatIndia(Math.round(total.loan)));
        $('#principal_amnt_calc').val(moneyFormatIndia(Math.round(total.principal)));
        $('#interest_amnt_calc').val(moneyFormatIndia(Math.round(total.interest)));
        $('.int-diff').text('* (Difference: +' + Math.round(total.interestDiff) + ')');
        $('#total_amnt_calc').val(moneyFormatIndia(Math.round(total.total)));
        $('#due_amnt_calc').val(moneyFormatIndia(Math.round(total.due)));
        $('.due-diff').text('* (Difference: +' + Math.round(total.dueDiff) + ')');
        $('#doc_charge_calculate').val(moneyFormatIndia(Math.round(total.doc)));
        $('.doc-diff').text('* (Difference: +' + Math.round(total.docDiff) + ')');
        $('#processing_fees_calculate').val(moneyFormatIndia(Math.round(total.proc)));
        $('.proc-diff').text('* (Difference: +' + Math.round(total.procDiff) + ')');
        $('#net_cash_calc').val(moneyFormatIndia(Math.round(total.net)));
    });
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

async function callLoanCaculationFunctions() {
    try {
        await loanInfo();        // Step 1: Wait for loan data to load
        await issueList();       // Step 2: Wait for issue list to load
    } catch (err) {
        console.error("Error in loan calculation flow:", err);
    }
}


function getAddCustomerList(customer_mapping) {
    $.post(
        "api/loan_entry_files/get_customer_list.php",
        { customer_mapping },
        function (response) {
            let cusOptn = "";
            cusOptn = '<option value="">Select Customer Name</option>';
            response.forEach((val) => {
                cusOptn +=
                    '<option value="' +
                    val.id +
                    '">' +
                    val.first_name +
                    " - " +
                    val.mobile1 +
                    " - " +
                    val.areaname +
                    "</option>";
            });
            $("#add_customer").empty().append(cusOptn);
        },
        "json"
    );
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


// Function to remove row
function removeCusMap(TableRowVal) {
    // Find the row with the matching ID and remove it
    let id = TableRowVal.id; // Extract the 'id' value
    let loan_id = TableRowVal.loan_id;

    $.post('api/approval/delete_cus_mappings.php', { id, loan_id }, function (response) {
        if (response == 1) {
            swalSuccess('Success', 'Customer mapping removed successfully.')
            issueList();
        } else {
            swalError('Alert', 'Customer mapping remove failed.')
        }
    }, 'json');
}

function getLoanAfterInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee, proc_type) {

    // Calculate Interest amount 
    var interest_rate = (parseInt(loan_amt) * (parseFloat(int_rate) / 100) * parseInt(due_period));

    // Calculate Total amount 
    var tot_amt = parseInt(loan_amt) + parseFloat(interest_rate);

    // Calculate Due amount 
    var due_amt = parseInt(tot_amt) / parseInt(due_period);
    var roundDue = Math.ceil(due_amt / 5) * 5;
    if (roundDue < due_amt) {
        roundDue += 5;
    }
    let dueDiff = parseInt(roundDue - due_amt);

    // Calculate New Total amount 
    var new_tot = parseInt(roundDue) * due_period;

    // Calculate Interest amount 
    let new_int = (roundDue * due_period) - loan_amt;
    let roundedInterest = Math.ceil(new_int / 5) * 5;
    if (roundedInterest < new_int) {
        roundedInterest += 5;
    }
    let interestDiff = parseInt(roundedInterest - interest_rate);

    // Calculate New Principal amount 
    var new_princ = parseInt(new_tot) - parseInt(roundedInterest);

    // Calculate Document charge 
    var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100);
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5;
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    let docDiff = parseInt(roundeddoccharge) - parseInt(doc_charge);

    // Calculate Processing fee
    if (proc_type.includes('rupee')) {
        var proc_fee = parseInt(proc_fee);
    } else if (proc_type.includes('percentage')) {
        var proc_fee = parseInt(loan_amt) * (parseInt(proc_fee) / 100);
    }
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5;
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    let procDiff = parseInt(roundeprocfee) - parseInt(proc_fee);

    // Calculate Net Cash
    var net_cash = parseInt(loan_amt) - parseFloat(roundeddoccharge) - parseFloat(roundeprocfee);

    return {
        loan: loan_amt,
        principal: new_princ,
        interest: roundedInterest,
        total: new_tot,
        due: roundDue,
        dueDiff: dueDiff,
        interestDiff: interestDiff,
        doc: roundeddoccharge,
        docDiff: docDiff,
        proc: roundeprocfee,
        procDiff: procDiff,
        net: net_cash
    };
}

function getLoanPreInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee, proc_type) {

    // Calculate Interest amount 
    let interest_rate = (parseInt(loan_amt) * (parseFloat(int_rate) / 100) * parseInt(due_period));

    // Calculate Principal amount 
    let princ_amt = parseInt(loan_amt) - parseInt(interest_rate);

    // Calculate Total amount 
    let tot_amt = parseInt(princ_amt) + parseFloat(interest_rate);

    // Calculate Due amount 
    var due_amt = parseInt(tot_amt) / parseInt(due_period);
    var roundDue = Math.ceil(due_amt / 5) * 5;
    if (roundDue < due_amt) {
        roundDue += 5;
    }
    let dueDiff = parseInt(roundDue - due_amt);

    // Calculate New Total amount 
    let new_tot = parseInt(roundDue) * due_period;

    // Calculate Interest amount 
    let new_int = (roundDue * due_period) - princ_amt;
    let roundedInterest = Math.ceil(new_int / 5) * 5;
    if (roundedInterest < new_int) {
        roundedInterest += 5;
    }
    let interestDiff = parseInt(roundedInterest - interest_rate);

    // Calculate New Principal amount 
    let new_princ = parseInt(new_tot) - parseInt(roundedInterest);

    // Calculate Document charge 
    var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100);
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5;
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    let docDiff = roundeddoccharge - doc_charge;

    // Calculate Processing fee
    if (proc_type.includes('rupee')) {
        var proc_fee = parseInt(proc_fee);
    } else if (proc_type.includes('percentage')) {
        var proc_fee = parseInt(loan_amt) * (parseInt(proc_fee) / 100);
    }
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5;
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    let procDiff = parseInt(roundeprocfee - proc_fee);

    // Calculate Net Cash
    let net_cash = parseInt(princ_amt) - parseInt(roundeddoccharge) - parseInt(roundeprocfee);

    return {
        loan: loan_amt,
        principal: new_princ,
        interest: roundedInterest,
        total: new_tot,
        due: roundDue,
        dueDiff: dueDiff,
        interestDiff: interestDiff,
        doc: roundeddoccharge,
        docDiff: docDiff,
        proc: roundeprocfee,
        procDiff: procDiff,
        net: net_cash
    };
}

async function loanInfo() {
    let loan_id = $('#loan_id').val();

    return new Promise((resolve, reject) => {
        $.post('api/loan_issue_files/loan_issue_data.php', { loan_id }, function (response) {
            if (!response || response.length === 0) {
                reject("No loan data returned");
                return;
            }

            $('#centre_id').val(response[0].centre_id);
            $('#centre_no').val(response[0].centre_no);
            $('#centre_name').val(response[0].centre_name);
            $('#loan_id_calc').val(response[0].loan_id);
            $('#cus_data').val(response[0].cus_data);
            $('#loan_amount').val(response[0].loan_amount_calc);
            $('#centre_mobile').val(response[0].mobile1);
            $('#total_cus').val(response[0].total_customer);
            $('#profit_method_calc').val(response[0].benefit_method);
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

            // Handle read-only toggle based on loan_date
            if (response[0].loan_date !== '' && response[0].loan_date !== null) {
                $('#due_startdate_calc').prop('readonly', true);
                $('#maturity_date_calc').prop('readonly', true);
            } else {
                $('#due_startdate_calc').prop('readonly', false);
            }

            $('#due_startdate_calc').val(response[0].due_start);
            $('#maturity_date_calc').val(response[0].due_end);
            $('#due_period_calc').val(response[0].due_period);
            $('#profit_type_calc').val(response[0].profit_type);
            $('#due_method_calc').val(response[0].due_month);
            $('#scheme_day_calc').val(response[0].scheme_day);
            $('#due_startdate_calc').attr('min', response[0].loan_date);
            $('#proc_type').val(response[0].procrssing_fees_type);
            $('#loan_amount_calc').val(response[0].loan_amount_calc);
            $('#interest_rate_calc').val(response[0].interest_rate);
            $('#processing_fees_calc').val(response[0].processing_fees);
            $('#doc_charge_calc').val(response[0].doc_charge);

            let path = "uploads/loan_entry/cus_pic/";
            $('#per_pic').val(response[0].pic);
            $('#imgshow').attr('src', path + response[0].pic);

            // Nested call to issueList()
            issueList().then(() => {
                let totalCus = parseInt($('#total_cus').val()) || 0;
                let cusMappingtable = $('#issue_info_table tbody tr').length;
                if (totalCus > 0 && cusMappingtable > 0 && totalCus === cusMappingtable) {
                    $('#refresh_cal').trigger('click');
                }
                resolve(); // Only resolve after issueList completes
            }).catch((err) => {
                reject("Error in issueList: " + err);
            });

        }, 'json').fail((xhr, status, error) => {
            reject(`AJAX Error: ${status} - ${error}`);
        });
    });
}

var formattedDate = '';
var currentDate = new Date();
var day = ("0" + currentDate.getDate()).slice(-2); // Get day and pad with 0 if needed
var month = ("0" + (currentDate.getMonth() + 1)).slice(-2); // Get month and pad with 0 if needed
var year = currentDate.getFullYear(); // Get the year

var formattedDate = day + '/' + month + '/' + year; // Format as dd-mm-yyyy

async function issueList() {
    return new Promise((resolve, reject) => {
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
                let customerLoanAmount = 0;
                let individualLoanAmount = 0;

                $.each(response, function (index, item) {

                    if (item.loan_amount) {
                        customerLoanAmount += parseFloat(item.loan_amount);
                    }
                    if (item.loan_amount) {
                        individualLoanAmount = parseFloat(item.loan_amount);
                    }
                    var paymentTypeOptions = `
                    <select class="form-control payment-type" data-id="${item.id}" ${item.issue_status === '1' ? 'disabled' : ''}>
                        <option value="1" ${item.payment_type == 1 ? 'selected' : ''}>Single</option>
                        <option value="2" ${item.payment_type == 2 ? 'selected' : ''}>Split</option>
                    </select>
                `;
                    var issueTypeOptions = `
                    <select class="form-control issue-type" data-id="${item.id}" ${item.issue_status === '1' ? 'disabled' : ''}>
                        <option value="1" ${item.issue_type == 1 ? 'selected' : ''}>Cash</option>
                    </select>
                `;
                    var netcashCalc = item.individual_amount ? moneyFormatIndia(item.individual_amount) : '';
                    var issuedAmount = item.issued_amount ? moneyFormatIndia(item.issued_amount) : '0';
                    var loan_date = item.loan_date; // Get the loan date
                    if (loan_date === '' || loan_date === null || loan_date === undefined) {
                        var status = 'In Issue';
                    } else {
                        var status = (item.issue_status === '1' ? 'Issued' : 'Pending');
                    }

                    var actionHtml = `<div style="display: flex; align-items: center; gap: 20px; justify-content: center;">
                            <input type="checkbox" class="form-check-input" data-id="${item.id}" ${item.issue_status === '1' ? 'disabled' : ''} />
                            <span class="icon-trash-2 cusMapDeleteBtn" data-id="${item.id}" data-loan_id="${item.loan_id}" data-loan_status="${item.loan_status}"
                            data-centre_id="${item.centre_id}" style="cursor:pointer; font-size: 1rem;"></span> </div>`;

                    // Checkbox disabled if Issued
                    let issueAmountInput;
                    let remainingAmount = parseFloat(item.individual_amount) - parseFloat(item.issued_amount || 0);
                    let Amount = moneyFormatIndia(remainingAmount)
                    issueAmountInput = `
                        <input type="text" class="form-control issue-amount" 
                            value="${Amount}" readonly data-id="${item.id}"   ${item.issue_status === '1' ? 'readonly' : ''}/>
                    `;

                    var row = '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td class="cus_id">' + item.cus_id + '</td>' +
                        '<td>' + item.first_name + '</td>' +
                        '<td style="display:none">' + item.loan_amount + '</td>' +
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

                // Update hidden field
                $('#customerLoanAmount').val(customerLoanAmount);
                $('#individualLoanAmount').val(individualLoanAmount);

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
                            value="" data-id="${id}" ${issueStatus === '1' ? 'readonly' : ''} />
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
                // Resolve the promise when all processing is done
                resolve();
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error: ' + status + error);
                reject(error);
            }
        });
    });
}
