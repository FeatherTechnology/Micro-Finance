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
}
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
        $('#balance_net_cash').val(response[0].net_cash_calc);
        $('#due_startdate_calc').val(response[0].due_start);
        $('#maturity_date_calc').val(response[0].due_end);
        $('#due_period_calc').val(response[0].due_period);
        $('#profit_type_calc').val(response[0].profit_type);
        $('#due_method_calc').val(response[0].due_month);
        $('#scheme_day_calc').val(response[0].scheme_day);
        getIssuePerson(response[0].cus_name);
        $('#due_startdate_calc').attr('min', response[0].loan_date);

        let path = "uploads/loan_entry/cus_pic/";
        $('#per_pic').val(response[0].pic);
        var img = $('#imgshow');
        img.attr('src', path + response[0].pic);

    }, 'json');
}
