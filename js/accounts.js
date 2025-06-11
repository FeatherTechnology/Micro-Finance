$(document).ready(function(){
    $('input[name=accounts_type]').click(function () {
        let accountsType = $(this).val();
        if (accountsType == '1') { //Collection List
            $('#coll_card').show(); $('#loan_issued_card').hide(); $('#expenses_card').hide(); $('#other_transaction_card').hide();$('#customer_savings_card').hide();
            getBankName('#coll_bank_name');
        } else if (accountsType == '2') { //Loan Issued
            $('#coll_card').hide(); $('#loan_issued_card').show(); $('#expenses_card').hide(); $('#other_transaction_card').hide();$('#customer_savings_card').hide();
            getBankName('#issue_bank_name');
        } else if (accountsType == '3') { //Expenses
            $('#coll_card').hide(); $('#loan_issued_card').hide(); $('#expenses_card').show(); $('#other_transaction_card').hide();$('#customer_savings_card').hide();
            expensesTable('#accounts_expenses_table');
        } else if (accountsType == '4') { //Other Transaction
            $('#coll_card').hide(); $('#loan_issued_card').hide(); $('#expenses_card').hide(); $('#other_transaction_card').show();$('#customer_savings_card').hide();
            otherTransTable('#accounts_other_trans_table');
        }
        else if (accountsType == '5') { //Other Transaction
            $('#coll_card').hide(); $('#loan_issued_card').hide(); $('#expenses_card').hide(); $('#other_transaction_card').hide();$('#customer_savings_card').show();
            $("input[name='savings_type'][value='1']").prop('checked', true).trigger('click');
        }
    });

    $('input[name=savings_type]').click(function () {
        let savings_type = $(this).val();
        if(savings_type === '1'){
            savingsTable('#customer_savings_table');
            getCentreId();
            $('.Customer_based').show();
            $('.centre_based').hide();
        }else if(savings_type === '2'){
            $(".centre_based_div").show();
            $("#customer_details_div").hide();
            centreBasedlist()
            $('.Customer_based').hide();
            $('.centre_based').show();
        }
    });
  
    $(document).on('click', '.View_centre', function (e) {
        console.log("hlo");
        $('#cntr_bsd_cus_savings').modal('show');
        let cus_id = $(this).val();
        centrelist(cus_id);
    });

    $(document).on('click', '.centre_info_back', function (e) {
        $(".centre_based_div").show();
        $("#customer_details_div").hide();
    });

    $(document).on('click', '#submit_savings', function (e) {
        let centre_id=$('#centreId').val();
        let savingsData = [];
        $('.customer_list tbody tr').each(function () {
                var row = $(this); // Get current row
                // let balance_amount = row.find('td:nth-child(6)').text();
                let aadhar_num = row.find('td:nth-child(11)').text();
                let cus_id = row.find('td:nth-child(2)').text();
                let cus_name = row.find('td:nth-child(3)').text();
                let credit_debit = row.find('select.credit_debit').val();
                // let savings_amount = row.find('input.savings_amount').val();
                let balance_amount = parseFloat(row.find('td:nth-child(6)').text()) || 0;
                let savings_amount = parseFloat(row.find('input.savings_amount').val()) || 0;


                if (!savings_amount || isNaN(savings_amount) || savings_amount <= 0) {
                    return; // Continue to next row
                }

                if (!credit_debit) {
                    return; // Continue to next row
                }
                 
                if (credit_debit === "2" && savings_amount > balance_amount) {
                    return; // Continue to next row
                }

                savingsData.push({
                    credit_debit: credit_debit,
                    savings_amount: savings_amount,
                    aadhar_num: aadhar_num,
                    cus_id: cus_id,
                    cus_name: cus_name
                });   
            });
        if(savingsData.length > 0){
            $.ajax({
                url: 'api/accounts_files/accounts/submit_savings.php',
                method: 'POST',
                data: {
                    centre_id: centre_id,
                    savingsData: savingsData
                },
                success: function (response) {
                    response = typeof response === 'string' ? JSON.parse(response) : response;
                    if (response == 1) {
                        swalSuccess('Success', "Savings Added Successfully");
                        centreBasedCusList(centre_id);
                        getClosingBal();
                    } else {
                        swalError('Warning', 'Failed to save the collection details');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                }
            });

        }else{
            swalError('Warning', 'Please Fill The Savings Details ');
        }
    });

    $(document).on('click', '.savings_chart_back', function (e) {
        let value=$('#savings_chart_hidden_id').val();
                $('#Savings_chart_model').modal('hide'); // Show the modal
            if(value === '1'){
                $('#cntr_bsd_cus_savings').modal('show');
            }   
    });

    $(document).on('click', '.centre_view', function (e) {
        $("#customer_details_div").show();
        $(".centre_based_div").hide();
        let centre_id = $(this).val();
        centreBasedCusList(centre_id);
    });

    $("input[name='coll_cash_type']").click(function(){
        let collCashType = $(this).val();
        if (collCashType == '2') {
            $('#coll_bank_name').val('').attr('disabled', false);
            $('#accounts_collection_table').DataTable().destroy();
            $('#accounts_collection_table tbody').empty()
        }else{
            $('#coll_bank_name').val('').attr('disabled', true);
            getCollectionList();
        }
    });

    $('#coll_bank_name').change(function(){
        getCollectionList();
    });
    
    $(document).on('click', '.collect-money', function(event){
        event.preventDefault();
        let collectTableRowVal = {
            'username' : $(this).closest('tr').find('td:nth-child(2)').text(),
            'id' : $(this).attr('value'),
            'branch' : $(this).closest('tr').find('td:nth-child(3)').text(),
            'no_of_bills' : $(this).closest('tr').find('td:nth-child(4)').text(),
            'collected_amnt' : $(this).closest('tr').find('td:nth-child(5)').text().replace(/,/g, ''),
            'cash_type' : $("input[name='coll_cash_type']:checked").val(),
            'bank_id' : $('#coll_bank_name :selected').val()
        };
        swalConfirm('Collect', `Do you want to collect Money from ${collectTableRowVal.username}?`, submitCollect, collectTableRowVal);
    });
    
    $("input[name='issue_cash_type']").click(function(){
        let collCashType = $(this).val();
        if (collCashType == '2') {
            $('#issue_bank_name').val('').attr('disabled', false);
            $('#accounts_loanissue_table').DataTable().destroy();
            $('#accounts_loanissue_table tbody').empty()
        }else{
            $('#issue_bank_name').val('').attr('disabled', true);
            getLoanIssueList();
        }
    });
    
    $('#issue_bank_name').change(function(){
        getLoanIssueList();
    });

    $('#expenses_add').click(function(){
        getInvoiceNo();
        getBranchList();
        expensesTable('#expenses_creation_table');
    });
    
    $("input[name='expenses_cash_type']").click(function(){
        let expCashType = $(this).val();
        if (expCashType == '2') {
            $('#expenses_bank_name').val('').attr('disabled', false);
            $('.exp_trans_div').show();
        }else{
            $('.exp_trans_div').hide();
            $('#expenses_bank_name').val('').attr('disabled', true);
        }
    });


    $('#submit_expenses_creation').click(function (event) {
        event.preventDefault();

        let expensesData = {
            'coll_mode': $("input[name='expenses_cash_type']:checked").val(),
            'invoice_id': $('#invoice_id').val(),
            'branch_name': $('#branch_name :selected').val(),
            'expenses_category': $('#expenses_category :selected').val(),
            'description': $('#description').val(),
            'expenses_amnt': $('#expenses_amnt').val(),
        };

        // Fetch closing balance and validate the expense amount before submitting
        getClosingBal(function (hand_cash_balance, bank_cash_balance) {
            let expensesAmount = parseFloat(expensesData.expenses_amnt);
            let collMode = expensesData.coll_mode;

            // Check if cash mode is 1 (Hand Cash) and expenses amount is greater than hand cash balance
            if (collMode == '1' && expensesAmount > hand_cash_balance) {
                swalError('Warning', 'Insufficient Hand cash balance');
                return;
            }

            // Check if cash mode is 2 (Bank Transaction) and expenses amount is greater than bank cash balance
            if (collMode == '2' && expensesAmount > bank_cash_balance) {
                swalError('Warning', 'Insufficient Bank cash balance.');
                return;
            }

            // Proceed if the balance check passes
            if (expensesFormValid(expensesData)) {
                $.post('api/accounts_files/accounts/submit_expenses.php', expensesData, function (response) {
                    if (response == '1') {
                        swalSuccess('Success', 'Expenses added successfully.');
                        expensesTable('#expenses_creation_table');
                        getInvoiceNo();
                        getClosingBal(); // Update the closing balance after submission
                    } else {
                        swalError('Error', 'Failed to add expenses.');
                    }
                }, 'json');
            } else {
                swalError('Warning', 'Kindly Fill Mandatory Fields.');
            }
        });
    });


    
    $(document).on('click', '.expDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this Expenses?', deleteExp, id);
    });

    $(document).on('click', '.exp-clse', function(){
        expensesTable('#accounts_expenses_table');
    });

    $(document).on('click', '.cus-savings ,.cus-clse', function(){
        $('#cntr_bsd_cus_savings').modal('hide');
    });
    
    $('#other_trans_add').click(function(){
        otherTransTable('#other_transaction_table');
    });
    
    $("input[name='othertransaction_cash_type']").click(function(){
        let otherCashType = $(this).val();
        $('#other_trans_id').val('');

        if (otherCashType == '2') {
            $('#othertransaction_bank_name').val('').attr('disabled', false);
            $('.other_trans_div').show();
            getBankName('#othertransaction_bank_name');
        }else{
            $('.other_trans_div').hide();
            $('#othertransaction_bank_name').val('').attr('disabled', true);
        }
    });

    $('#trans_category').change(function(){
        let category = $(this).val();
        if (category != '') {
            $('#trans_cat').val($(this).find(':selected').text());
            $('#trans_cat').attr('data-id',$(this).val());
            $('#name_modal_btn')
                .attr('data-toggle', 'modal')
                .attr('data-target', '#add_name_modal');
        } else {
            $('#name_modal_btn')
                .removeAttr('data-toggle')
                .removeAttr('data-target');
        }

        nameDropDown(); //To show name based on transaction category.

        let catTypeOptn ='';
            catTypeOptn +="<option value=''>Select Type</option>";
        if(category == '1' || category == '2' || category == '3' || category == '4' || category == '9' ){ //credit / debit
            catTypeOptn +="<option value='1'>Credit</option>";
            catTypeOptn +="<option value='2'>Debit</option>";

        } else if(category == '5'|| category == '7' ){ //debit || category == '7'
            catTypeOptn +="<option value='2'>Debit</option>";            
        
        } else if(category == '6' || category == '8'){ //credit
            catTypeOptn +="<option value='1'>Credit</option>";
        }

        $('#cat_type').empty().append(catTypeOptn); //To show Type based on transaction category.

        getRefId(category);
    });
    
    $('#name_modal_btn').click(function () {
        if ($(this).attr('data-target')) {
            $('#add_other_transaction_modal').hide();
            getOtherTransNameTable();
        }else{
            swalError('Warning', 'Kindly select Transaction Category.');
        }
    });

    $('.name_close').click(function () {
        $('#add_other_transaction_modal').show();
        nameDropDown();
        $('#other_name').val('');
    });

    $('.clse-trans').click(function(){
        otherTransTable('#accounts_other_trans_table');
    });

    $('#submit_name').click(function(event){
        event.preventDefault();
        let transCat = $('#trans_cat').attr('data-id');
        let name = $('#other_name').val();
        if(transCat =='' || name ==''){
            swalError('Warning', 'Kindly fill all the fields.');
            return false;
        }
        $.post('api/accounts_files/accounts/submit_other_trans_name.php',{transCat, name},function(response){
            if(response == '1'){
                swalSuccess('Success', 'Transaction Name Added Successfully.');
                getOtherTransNameTable();
                $('#other_name').val('');
            }else{
                swalError('Error', 'Transaction Name Not Added. Try Again Later.');
            }
        },'json');
    });

    $('#submit_other_transaction').click(function(event){
        event.preventDefault();
        let otherTransData = {
            'coll_mode' : $("input[name='othertransaction_cash_type']:checked").val(),
            'bank_id' : $('#othertransaction_bank_name :selected').val(),
            'trans_category' : $('#trans_category :selected').val(),
            'other_trans_name' : $('#other_trans_name :selected').val(),
            'cat_type' : $('#cat_type :selected').val(),
            'other_ref_id' : $('#other_ref_id').val(),
            'other_trans_id' : $('#other_trans_id').val(),
            'other_amnt' : $('#other_amnt').val(),
            'other_remark' : $('#other_remark').val()
        }
        let otherAmount = otherTransData.other_amnt;
        let collMode = otherTransData.coll_mode;
        let catType = otherTransData.cat_type; // 1 = Credit, 2 = Debit
        let transCategory = parseInt(otherTransData.trans_category);
        // Fetch user's total credit and debit amounts
        $.post('api/accounts_files/accounts/get_user_transactions.php', {
            // 'coll_mode': otherTransData.coll_mode,
            'other_trans_name': otherTransData.other_trans_name
        }, function (response) {
            let totalCredit = parseFloat(response.total_type_1_amount || 0); // Total Credit
            let totalDebit = parseFloat(response.total_type_2_amount || 0);  // Total Debit

            let balance;
            if (catType == '2') { // Debit Transaction
                balance = totalCredit - totalDebit; // Calculate balance
            } else if (catType == '1') { // Credit Transaction
                balance = totalDebit - totalCredit; // Calculate balance  
            }

            // Validate Debit Transactions
            if (transCategory >= 3 && transCategory <= 9) {
                if (catType == '2') { // Debit Transaction
                    if (balance > 0) {
                        // Allow debit if balance is zero or negative, as long as debit amount does not exceed the absolute value of the balance
                        if (otherAmount > Math.abs(balance)) {
                            const formattedBalance = moneyFormatIndia(Math.abs(balance));
                            swalError('Warning', 'You may only debit up to: ' + formattedBalance);
                            return;
                        }
                    }
                } else if (catType == '1') { // Credit Transaction
                    // Allow credit if balance is negative or zero
                    if (balance > 0) {
                        if (otherAmount > Math.abs(balance)) {
                            const formattedBalance = moneyFormatIndia(Math.abs(balance));
                            swalError('Warning', 'You may only credit up to: ' + formattedBalance);
                            return;
                        }
                    }
                }
            } else if (transCategory <= 2) {
                if (catType == '2' && totalCredit < totalDebit + otherAmount) {
                    const formattedBalance = moneyFormatIndia(Math.abs(balance));
                    swalError('Warning', 'You may only debit up to: ' + formattedBalance);
                    return;
                }
            }

            // Fetch hand cash and bank cash balances for validation
            getClosingBal(function (hand_cash_balance, bank_cash_balance) {
                if (catType == '2') { // Debit Transaction
                    if (collMode == '1' && otherAmount > hand_cash_balance) {
                        swalError('Warning', 'Insufficient hand cash balance.');
                        return;
                    }
                    if (collMode == '2' && otherAmount > bank_cash_balance) {
                        swalError('Warning', 'Insufficient bank cash balance.');
                        return;
                    }
                }
                //    Proceed if all validations pass
                if (otherTransFormValid(otherTransData)) {
                    $.post('api/accounts_files/accounts/submit_other_transaction.php', otherTransData, function (response) {
                        if (response == '1') {
                            swalSuccess('Success', 'Other Transaction added successfully.');
                            otherTransTable('#other_transaction_table');
                            getClosingBal(); // Update closing balance after submission
                            $('#name_id_cont').show();
                            $('#name_modl_btn').show();
                        } else {
                            swalError('Error', 'Failed to add transaction.');
                        }
                    }, 'json');
                }
                else {
                    swalError('Warning', 'Please fill all required fields.');
                }
            });
        }, 'json');
    });

    $('#aadhar_num').change(function(){
        let aadhar_num = $("#aadhar_num").val();
        console.log("aadhar ",aadhar_num);
        existingCustmerProfile(aadhar_num);
    });
     $('#centre_id').change(function(){
        let centre_id = $('#centre_id').val();
        $('#aadhar_num').val("");
        $('#cus_name').val("");
        getCustomerID(centre_id);

    });

    $('#submit_savings_creation').click(function(event){
        event.preventDefault();
        let centre_id = $('#centre_id').val();
        let cus_id = $('#cus_id').val();
        let cat_type =  $('#catType').val();
        let aadhar_num = $('#aadhar_num').val();
        let savings_amount = $('#savings_amnt').val();
        let cus_name = $('#cus_name').val();
       getCusPreAmt(cus_id, centre_id, function(amounts, error) {
        if (error) {
            alert(error); // or use Swal.fire(error); for SweetAlert
            return;
        }

        let creditAmount = amounts.credit;
        let debitAmount = amounts.debit;
        let difference = creditAmount - debitAmount; 
        if (cat_type === '2' && difference < savings_amount) {
                swalError('Warning', 'Insufficient  balance');
                return;
        }
        let savingsData = {
            'aadhar_number' : aadhar_num ,
            'cus_id' : cus_id,
            'centre_id' : centre_id,
            'savings_amnt' : savings_amount,
            'cat_type' : cat_type,
            'cus_name' : cus_name
        }
        if (Savingsvalidation(savingsData)) {

            $.post('api/accounts_files/accounts/submit_savings.php', savingsData, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Savings added successfully.');
                    savingsTable('#customer_savings_table');
                    // getInvoiceID()
                    getClosingBal();
                } else {
                    swalError('Error', 'Failed to add transaction.');
                }
            }, 'json');
        }
        else {
            swalError('Warning', 'Please fill required fields.');
        }
        })    
    });
    
    $(document).on('click', '.transDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this Other Transaction?', deleteTrans, id);
    });

    $(document).on('click', '.Savings-chart, #Savings-chart', function (e) {
        e.preventDefault(); // Prevent default anchor behavior
    
    // Set hidden value based on the clicked element
        if ($(this).hasClass('Savings-chart')) {
            $('#savings_chart_hidden_id').val('1');
        } else if ($(this).attr('id') === 'Savings-chart') {
            $('#savings_chart_hidden_id').val('2');
        }
        var cus_id = $(this).attr('value');
        var centre_id = $(this).attr('centre_id');

        $('#Savings_chart_model').modal('show');
        $('#cntr_bsd_cus_savings').modal('hide');

        savingsChartList(cus_id, centre_id);
    });

    //Balance sheet
    
    $('#IDE_type').change(function () {
        $('#blncSheetDiv').empty();
        $('.IDE_nameDiv').hide();
        $('#IDE_view_type').val(''); $('#IDE_name_list').val('');
    });

    $('#IDE_view_type').change(function () {
        $('#blncSheetDiv').empty()

        var view_type = $(this).val();//overall/Individual
        var type = $('#IDE_type').val(); //investment/Deposit/EL

        if (view_type == 1 && type != '') {
            $('#IDE_name_list').val(''); //reset name value when using overall
            $('.IDE_nameDiv').hide() // hide name list div
            getIDEBalanceSheet();
        } else if (view_type == 2 && type != '') {
            balNameDropDown();
            $('.IDE_nameDiv').show()
        } else {
            $('.IDE_nameDiv').hide()
        }
    });

    $('#IDE_name_list').change(function () {
        var name_id = $(this).val();
        if (name_id != '') {
            getIDEBalanceSheet();
        }
    });
   
});  /////Document END.

$(function(){
    getOpeningBal();
    getAccountAccess();
});

function getAccountAccess() {
    $.post('api/accounts_files/accounts/account_access.php', function(response) {
        if (Array.isArray(response) && response.length > 0) {
            let accessString = response[0].account_access; 
            let accessArray = accessString.split(",").map(Number);
            $(".selector-item").hide();
            accessArray.forEach(value => {
                if (value === 1) {
                    $("#collection").closest(".selector-item").show();
                } 
                if (value === 2) {
                    $("#loan_issued").closest(".selector-item").show();
                } 
                if (value === 3) {
                    $("#expenses").closest(".selector-item").show();
                }
                if (value === 4) {
                    $("#other_transaction").closest(".selector-item").show();
                }
                if (value === 5) {
                    $("#customer_savings").closest(".selector-item").show();
                }
            });
        }
    }, 'json');
}

function getOpeningBal(){
    $.post('api/accounts_files/accounts/opening_balance.php',function(response){
        if(response.length > 0){
            $('.opening_val').text(response[0]['opening_balance']);
            $('.op_hand_cash_val').text(response[0]['hand_cash']);
            $('.op_bank_cash_val').text(response[0]['bank_cash']);
        }
    },'json').then(function(){
        getClosingBal();
    });
}

function getClosingBal(callback) {
    $.post('api/accounts_files/accounts/closing_balance.php', function (response) {
        if (response.length > 0) {
            let close = parseInt($('.opening_val').text()) + parseInt(response[0]['closing_balance']);
            let hand = parseInt($('.op_hand_cash_val').text()) + parseInt(response[0]['hand_cash']);
            let bank = parseInt($('.op_bank_cash_val').text()) + parseInt(response[0]['bank_cash']);

            $('.closing_val').text(close);
            $('.clse_hand_cash_val').text(hand);
            $('.clse_bank_cash_val').text(bank);

            // Call the callback function if defined
            if (typeof callback === "function") {
                callback(hand, bank, close);
            }
        }
    }, 'json');
}

function getCollectionList(){
    let cash_type = $("input[name='coll_cash_type']:checked").val();
    let bank_id = $('#coll_bank_name :selected').val();
    $.post('api/accounts_files/accounts/accounts_collection_list.php', {cash_type, bank_id},function(response){
        let columnMapping = [
            'sno',
            'name',
            'linename',
            'branch_name',
            'no_of_bills',
            'total_amount',
            'action'
        ];
        appendDataToTable('#accounts_collection_table', response, columnMapping);
        setdtable('#accounts_collection_table');
    },'json');
}

function submitCollect(values){
    $.post('api/accounts_files/accounts/submit_collect.php', values, function(response){
        if (response == '1') {
            swalSuccess('Success', `Successfully collected ₹${moneyFormatIndia(values.collected_amnt)} for ${values.no_of_bills} bills from ${values.username}.`);
            getCollectionList();
            getClosingBal();
        }else{
            swalError('Error', 'Something went wrong.');
        }
    },'json');
}

function getLoanIssueList(){
    let cash_type = $("input[name='issue_cash_type']:checked").val();
    let bank_id = $('#issue_bank_name :selected').val();
    $.post('api/accounts_files/accounts/accounts_loan_issue_list.php', {cash_type, bank_id},function(response){
        let columnMapping = [
            'sno',
            'name',
            'branch_name',
            'no_of_loans',
            'issueAmnt'
        ];
        appendDataToTable('#accounts_loanissue_table', response, columnMapping);
        setdtable('#accounts_loanissue_table');
    },'json');
}

function getBankName(dropdowndId) {
    $.post('api/common_files/bank_name_list.php', function (response) {
        var bankName = '<option value="">Select Bank Name</option>';
        $.each(response, function (index, value) {
            bankName += '<option value="' + value.id + '" data-id="' + value.account_number + '">' + value.bank_name + '</option>';
        });
        $(dropdowndId).empty().html(bankName);
    }, 'json');
}

function getInvoiceNo(){
    $.post('api/accounts_files/accounts/get_invoice_no.php',{},function(response){
        $('#invoice_id').val(response);
    },'json');
}

function getBranchList(){
    $.post('api/common_files/user_mapped_branches.php',function(response){
        let branchOption;
        branchOption += '<option value="">Select Branch Name</option>';
        $.each(response,function(index,value){
            branchOption += '<option value="'+value.id+'">'+value.branch_name+'</option>';
            });
        $('#branch_name').empty().html(branchOption);
    },'json');
}


function expensesFormValid(expensesData){
    for(key in expensesData){
        if(key !='expenses_total_issued' && key !='expenses_total_amnt' && key !='bank_id' && key !='expenses_trans_id'){
            if(expensesData[key] =='' || expensesData[key] ==null || expensesData[key] ==undefined){
                return false;
            }
        }
    }
    return true;
}

function expensesTable(tableId){
    $.post('api/accounts_files/accounts/get_expenses_list.php',function(response){
        let expensesColumn = [
            'sno',
            'coll_mode',
            'invoice_id',
            'branch',
            'expenses_category',
            'description',
            'amount',
            // 'trans_id',
            'action'
        ];

        appendDataToTable(tableId, response, expensesColumn);
        setdtable(tableId);
        clearExpForm();
    },'json');
}

function clearExpForm(){
    $('#expenses_total_issued').val('');
    $('#expenses_total_amnt').val('');
    $('#expenses_amnt').val('');
    $('#expenses_form select').val('');
    $('#expenses_form textarea').val('');
    $('.agentDiv').hide();
}

function deleteExp(id) {
    $.post('api/accounts_files/accounts/delete_expenses.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Expenses Deleted Successfully');
            expensesTable('#expenses_creation_table');
            expensesTable('#accounts_expenses_table');
            getInvoiceNo();
            getClosingBal();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}

function getOtherTransNameTable(){
    let transCat = $('#trans_category :selected').val();
    $.post('api/accounts_files/accounts/get_other_trans_name_table.php',{transCat},function(response){
        let nameColumns = [
            'sno',
            'trans_cat',
            'name'
        ];
        appendDataToTable('#other_trans_name_table',response,nameColumns);
        setdtable('#other_trans_name_table');
    },'json');
}

function nameDropDown(){
    let transCat = $('#trans_category :selected').val();
    $.post('api/accounts_files/accounts/get_other_trans_name_table.php',{transCat},function(response){
        let nameOptn='';
            nameOptn +="<option value=''>Select Name</option>";
            $.each(response, function(index, val){
                nameOptn += "<option value='"+val.id+"'>"+val.name+"</option>";
            });
        $('#other_trans_name').empty().append(nameOptn);
    },'json');
}


function otherTransFormValid(data){
    for(key in data){
        if(key !='expenses_total_amnt' && key !='bank_id' && key !='other_trans_id'){
            if(data[key] =='' || data[key] ==null || data[key] ==undefined){
                return false;
            }
        }
    }
    
    if(data['coll_mode'] =='2'){
        if(data['bank_id'] =='' || data['bank_id'] ==null || data['bank_id'] == undefined || data['other_trans_id'] =='' || data['other_trans_id'] ==null || data['other_trans_id'] == undefined){
            return false;
        }
    }
    return true;
}

function otherTransTable(tableId){
    $.post('api/accounts_files/accounts/get_other_trans_list.php',function(response){
        let expensesColumn = [
            'sno',
            'coll_mode',
            'trans_cat',
            'name',
            'type',
            'ref_id',
            'amount',
            'remark',
            'action'
        ];

        appendDataToTable(tableId, response, expensesColumn);
        setdtable(tableId);
        clearTransForm();
    },'json');
}

function savingsTable(tableId){
    $.post('api/accounts_files/accounts/get_savings_list.php',function(response){
        let expensesColumn = [
            'sno',
            'cus_id',
            'cus_name',
            'aadhar_num',
            'balance',
            'action'
        ];

        appendDataToTable(tableId, response, expensesColumn);
        setdtable(tableId);
        clearsavingsForm();
    },'json');
}

function clearTransForm(){
    $('#other_ref_id').val('');
    $('#other_trans_id').val('');
    $('#other_amnt').val('');
    $('#other_transaction_form select').val('');
    $('#other_transaction_form textarea').val('');
}
function clearsavingsForm(){
    $('#aadhar_num').val('');
    $('#cus_id').val('');
    $('#savings_amnt').val('');
    $('#catType').val('');
    $('#cus_name').val('');
    $('#centre_id').val('');
}

function deleteTrans(id) {
    $.post('api/accounts_files/accounts/delete_other_transaction.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Other Transaction Deleted Successfully');
            otherTransTable('#other_transaction_table');
            otherTransTable('#accounts_other_trans_table');
            getClosingBal();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}

function getRefId(trans_cat){
    $.post('api/accounts_files/accounts/get_ref_id.php', {trans_cat}, function (response){
        $('#other_ref_id').val(response)
    },'json');
}

function balNameDropDown(){
    let transCat = $('#IDE_type :selected').val();
    $.post('api/accounts_files/accounts/get_other_trans_name_table.php',{transCat},function(response){
        let nameOptn='';
            nameOptn +="<option value=''>Select Name</option>";
            $.each(response, function(index, val){
                nameOptn += "<option value='"+val.id+"'>"+val.name+"</option>";
            });
        $('#IDE_name_list').empty().append(nameOptn);
    },'json');
}

function getIDEBalanceSheet() {
    var type = $('#IDE_type').val(); //investment/Deposit/EL
    var view_type = $('#IDE_view_type').val();//overall/Individual
    var IDE_name_id = $('#IDE_name_list').val();//show by name wise

    $.ajax({
        url: 'api/accounts_files/accounts/dep_bal_sheet.php',
        data: { 'IDEview_type': view_type, 'IDEtype': type, 'IDE_name_id': IDE_name_id },
        type: 'post',
        cache: false,
        success: function (response) {
            $('#blncSheetDiv').empty()
            $('#blncSheetDiv').html(response)
        }
    })
}

function resetBlncSheet() {
    $('#IDE_type').val('');
    $('#IDE_view_type').val('');
    $('#IDE_name_list').val('');
}

function Savingsvalidation(data){
    for(key in data){
        if(data[key] =='' || data[key] ==null || data[key] ==undefined){
            return false;
        }    
    }
    return true;
}

function existingCustmerProfile(aadhar_num) {
  $.post(
    "api/accounts_files/accounts/get_customer_id.php",
    { aadhar_num },
    function (response) {
      if (response === "New") {
        $("#cus_id").val("");
        $("#cus_name").val("");
      }
      else{
        $("#cus_id").val(response[0].cus_id); 
        $("#cus_name").val(response[0].first_name); 
      }
    },
  "json" )
}
function getCustomerID(centre_id) {
    console.log("centre_id ",centre_id);
  $.post('api/accounts_files/accounts/get_customer_id.php', {centre_id } ,function(response){
        let aadhar_number='';
            aadhar_number +="<option value=''>Select Centre ID</option>";
            $.each(response, function(index, val){
                aadhar_number += "<option value='"+val.aadhar_number+"'>"+val.aadhar_number+"</option>";
            });
        $('#aadhar_num').empty().append(aadhar_number);
    },'json');
}

function getCusPreAmt(cus_id, centre_id,callback) {
  $.post("api/accounts_files/accounts/get_customer_debit_credit.php",
    { cus_id ,centre_id },
    function (response) {
      if (response === 0) {
        // Indicates an error
        callback(null, "Error retrieving data.");
      } else {
        let credit = response[0].total_credit || 0;
        let debit = response[0].total_debit || 0;
        callback({ credit, debit }, null);
      }
    },
    "json"
  ).fail(function () {
    // Network or server error
    callback(null, "Server error occurred.");
  });
}

function savingsChartList(cus_id,centre_id) {
    $.ajax({
        url: 'api/collection_files/get_savings_chart_list.php',
        data: { 'cus_id': cus_id , 'centre_id':centre_id },
        type: 'post',
        cache: false,
        success: function (response) {
            $('#savings_chart_table_div').empty()
            $('#savings_chart_table_div').html(response)
        }
    });//Ajax End.
}
function centrelist(cus_id) {
    $.ajax({
        url: 'api/accounts_files/accounts/get_centre_list.php',
        data: { 'cus_id': cus_id },
        type: 'post',
        cache: false,
        success: function (response) {
            $('#customer_savings_centre_list').empty()
            $('#customer_savings_centre_list').html(response)
           
        }
    });//Ajax End.
}
function centreBasedlist() {
    $.ajax({
        url: 'api/accounts_files/accounts/get_centres.php',
        data: { },
        type: 'post',
        cache: false,
        success: function (response) {
            $('#centre_based_table').empty();
            $('#centre_based_table').html(response);
        }
    });//Ajax End.
}

function getCentreId(){
    $.post('api/accounts_files/accounts/get_centre_id.php'," ",function(response){
        let centre_id='';
            centre_id +="<option value=''>Select Centre ID</option>";
            $.each(response, function(index, val){
                centre_id += "<option value='"+val.centre_id+"'>"+val.centre_id+"</option>";
            });
        $('#centre_id').empty().append(centre_id);
    },'json');
}

function centreBasedCusList(centre_id){
     $.ajax({
        url: 'api/accounts_files/accounts/get_centre_based_cus_list.php',
        type: 'POST',
        dataType: 'json',
        data: {
            centre_id: centre_id,
        },
        success: function (response) {
            var tbody = $('.customer_list tbody');
            tbody.empty();
            $("#centreId").val(response[0].centre_id);
            $("#centre_no").val(response[0].centre_no);
            $("#centre_name").val(response[0].centre_name);
            $("#mobile1").val(response[0].mobile1);
            $("#mobile2").val(response[0].mobile2);
            $("#area").val(response[0].areaname);
            $("#branch").val(response[0].branch_name);
            $("#latlong").val(response[0].latlong);
            var hasRows = false;
            var serialNo = 1;
            $.each(response, function (index, item) {
                var formattedDate = '';
                var currentDate = new Date();
                var day = ("0" + currentDate.getDate()).slice(-2); // Get day and pad with 0 if needed
                var month = ("0" + (currentDate.getMonth() + 1)).slice(-2); // Get month and pad with 0 if needed
                var year = currentDate.getFullYear(); // Get the year
                var formattedDate = day + '-' + month + '-' + year; // Format as dd-mm-yyyy

               var dropdownSelect = "<select class='form-control credit_debit'>" +
                                    "<option value=''>Select Credit / Debit</option>" +
                                    "<option value='1'>Credit</option>" +
                                    "<option value='2'>Debit</option>" +
                                    "</select>";
                var action = "<button class='btn btn-primary' id='Savings-chart' value='" + item.cus_id + "'  centre_id='" + item.centre_id + "'>Savings Chart</button>";


                var row = '<tr>' +
                    '<td>' + serialNo  + '</td>' +
                    '<td>' + item.cus_id + '</td>' +
                    '<td>' + item.first_name + '</td>' +
                    '<td>' + item.total_credit + '</td>' +
                    '<td>' + item.total_debit + '</td>' +
                    '<td>' + item.balance + '</td>' +
                    '<td>' + formattedDate + '</td>' +
                    '<td>' + dropdownSelect + '</td>' +
                    '<td><input type="number" class="form-control savings_amount"></td>' +
                    '<td>' + action + '</td>' +
                    '<td style="display:none">' + item.aadhar_number + '</td>' +
                    '</tr>';

                tbody.append(row);
                serialNo++;
                hasRows = true;
            })
            }
        })
}
