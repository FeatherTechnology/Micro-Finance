$(document).ready(function () {
    //Move Loan Entry 
    $(document).on('click', '#add_customer, #back_btn', function () {
        $('#cus_id').val('');
        swapTableAndCreation();
       
        setTimeout(() => {
        getAreaName()
        getFamilyInfoTable()
        }, 1000);
        $('.cus_status_div').hide();
        $('#imgshow').attr('src', 'img/avatar.png');

    });
    $('#pic').change(function () {
        let pic = $('#pic')[0];
        let img = $('#imgshow');
        img.attr('src', URL.createObjectURL(pic.files[0]));
        checkInputFileSize(this, 200, img)
    })
    $('input[data-type="adhaar-number"]').keyup(function () {
        var value = $(this).val();
        value = value.replace(/\D/g, "").split(/(?:([\d]{4}))/g).filter(s => s.length > 0).join(" ");
        $(this).val(value);
    });

    $('input[data-type="adhaar-number"]').change(function () {
        let len = $(this).val().length;
        if (len < 14) {
            $(this).val('');
            swalError('Warning', 'Kindly Enter Valid Aadhaar Number');
        }
    });
    $('#dob').on('change', function () {
        var dob = new Date($(this).val());
        var today = new Date();
        var age = today.getFullYear() - dob.getFullYear();
        var m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        $('#age').val(age);
    });
    $('input[name="mobile_whatsapp"]').on('change', function () {
        let selectedValue = $(this).val();
        let mobileNumber;

        if (selectedValue === 'mobile1') {
            mobileNumber = $('#mobile1').val();
        } else if (selectedValue === 'mobile2') {
            mobileNumber = $('#mobile2').val();
        }

        $('#whatsapp').val(mobileNumber);
    });
    $('#mobile1, #mobile2, #whatsapp, #fam_mobile').change(function () {
        checkMobileNo($(this).val(), $(this).attr('id'));
    });
    ///////////////////////////////////////////////////family Modal//////////////////////////////////////////////////////////////////////////////////
    $('#submit_family').click(function (event) {
        event.preventDefault();
        // Validation
        let cus_id = $('#cus_id').val();// Remove spaces from cus_id
        let fam_name = $('#fam_name').val();
        let fam_relationship = $('#fam_relationship').val();
        let fam_age = $('#fam_age').val();
        let fam_occupation = $('#fam_occupation').val();
        let fam_aadhar = $('#fam_aadhar').val().replace(/\s/g, '');
        let fam_mobile = $('#fam_mobile').val();
        let family_id = $('#family_id').val();

        var data = ['fam_name', 'fam_relationship','fam_mobile','fam_aadhar']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });

        if (isValid) {
            $.post('api/customer_creation_files/submit_family_info.php', { cus_id, fam_name, fam_relationship, fam_age, fam_occupation, fam_aadhar, fam_mobile, family_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Family Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Family Info Updated Successfully!');
                }
                // Refresh the family table
                getFamilyTable();
            });
        }
    });
    $(document).on('click', '.familyActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/customer_creation_files/family_creation_data.php', { id: id }, function (response) {
            $('#family_id').val(id);
            $('#fam_name').val(response[0].fam_name);
            $('#fam_relationship').val(response[0].fam_relationship);
            $('#fam_age').val(response[0].fam_age);
            $('#fam_occupation').val(response[0].fam_occupation);
            $('#fam_aadhar').val(response[0].fam_aadhar);
            $('#fam_mobile').val(response[0].fam_mobile);
        }, 'json');
    });

    $(document).on('click', '.familyDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Family Details?', getFamilyDelete, id);
        return;
    });
    $('#clear_fam_form').click(function (event) {
        event.preventDefault()
        $('#family_id').val('');
        $('#family_form select').each(function () {
            $(this).val($(this).find('option:first').val());
    
        });
        $('#family_form textarea').val('');
        $('#family_form input').val('');
       
    })
////////////////////////Family Modal end////////////////////////////////////////////////////////////
/////////////////////////////////////////////////customer creation//////////////////////////////////////
$('#submit_cus_creation').click(function (event) {
    event.preventDefault();

    // Validation
  
    let cus_id = $('#cus_id').val();
    let aadhar_number = $('#aadhar_number').val().replace(/\s/g, '');
    let cus_data = $('#cus_data').val();
    let cus_status = $('#cus_status').val();
    let first_name = $("#first_name").val();
    let last_name = $('#last_name').val();
    let dob = $('#dob').val();
    let age = $('#age').val();
    let area = $('#area').val();
    let mobile1 = $('#mobile1').val();
    let mobile2 = $('#mobile2').val();
    let whatsapp = $('#whatsapp').val();
    let occ_detail = $('#occ_detail').val();
    let occupation = $('#occupation').val();
    let address = $('#address').val();
    let native_address = $('#native_address').val();
    let multiple_loan = $('#multiple_loan').val();
    let pic = $('#pic')[0].files[0];
    let per_pic = $('#per_pic').val();
    let customer_profile_id = $('#customer_profile_id').val();

    let cusDetail = new FormData();
    cusDetail.append('cus_id', cus_id);
    cusDetail.append('aadhar_number', aadhar_number);
    cusDetail.append('cus_data', cus_data);
    cusDetail.append('cus_status', cus_status);
    cusDetail.append('first_name', first_name);
    cusDetail.append('last_name', last_name);
    cusDetail.append('dob', dob);
    cusDetail.append('age', age);
    cusDetail.append('area', area);
    cusDetail.append('mobile1', mobile1);
    cusDetail.append('mobile2', mobile2);
    cusDetail.append('whatsapp', whatsapp);
    cusDetail.append('occupation', occupation);
    cusDetail.append('occ_detail', occ_detail);
    cusDetail.append('address', address);
    cusDetail.append('native_address', native_address);
    cusDetail.append('multiple_loan', multiple_loan);
    cusDetail.append('pic', pic);
    cusDetail.append('per_pic', per_pic);
    cusDetail.append('customer_profile_id', customer_profile_id);

    var data = ['cus_id', 'first_name', 'aadhar_number','area', 'mobile1', 'multiple_loan']

    var isValid = true;
    data.forEach(function (entry) {
        var fieldIsValid = validateField($('#' + entry).val(), entry);
        if (!fieldIsValid) {
            isValid = false;
        }
    });
   
    if (isValid) {
        $.ajax({
            url: 'api/customer_creation_files/submit_customer.php',
            type: 'post',
            data: cusDetail,
            contentType: false,
            processData: false,
            cache: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response == '1') {
                    swalSuccess('Success', 'Customer Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Customer Info Updated Successfully!');
                }
                $('#customer_profile_id').val(response.last_id);
                $('#cus_data').val(response.cus_data);
                if (response.cus_data == 'Existing') {
                    $('.cus_status_div').show();
                }
                $('#cus_status').val(response.cus_status);
                $('#customer_creation').trigger('reset');
                getCustomerEntryTable();
                swapTableAndCreation()

            }
        });
    }
});
$(document).on('click', '.customerActionBtn', function () {
    let id = $(this).attr('value');
    $('#customer_profile_id').val(id);
    swapTableAndCreation();
    editCustomerCreation(id)

});
$('#aadhar_number').on('blur', function () {
    let aadhar_number = $('#aadhar_number').val().trim().replace(/\s/g, ''); 
        existingCustmerProfile(aadhar_number)
    
});
$(document).on('click', '.customerDeleteBtn', function () {
    var id = $(this).attr('value');
    swalConfirm('Delete', 'Do you want to Delete the Customer Details?', getCustomerDelete, id);
    return;
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////
});
$(function () {
    getCustomerEntryTable();
});
function getCustomerEntryTable() {
    serverSideTable('#customer_create', '', 'api/customer_creation_files/customer_creation_list.php');
    // setDropdownScripts();   
}
function swapTableAndCreation() {
    if ($('.customer_table_content').is(':visible')) {
        $('.customer_table_content').hide();
        $('#add_customer').hide();
        $('#customer_creation_content').show();
        $('#back_btn').show();
        // let customer_id = $('#customer_id').val();
        getAutoGenCusId('');
    } else {
        $('.customer_table_content').show();
        $('#add_customer').show();
        $('#customer_creation_content').hide();
        $('#back_btn').hide();
    }
}
function getAutoGenCusId(id) {
    $.post('api/customer_creation_files/get_autoGen_cus_id.php', { id }, function (response) {
        $('#cus_id').val(response);
    }, 'json');
}
function getAreaName() {
    $.post('api/customer_creation_files/get_area.php', function (response) {
        let appendAreaOption = '';
        appendAreaOption += "<option value=''>Select Area Name</option>";
        $.each(response, function (index, val) {
            let selected = '';
            let editArea = $('#area_edit').val();
            if (val.id == editArea) {
                selected = 'selected';
            }
            appendAreaOption += "<option value='" + val.id + "' " + selected + ">" + val.areaname + "</option>";
        });
        $('#area').empty().append(appendAreaOption);
    }, 'json');
}
function checkAdditionalRenewal(cus_id) {
    $.post('api/customer_creation_files/check_additional_renewal.php', { cus_id }, function (response) {
        $('#cus_status').val(response);
    }, 'json');
}
function editCustomerCreation(id) {
    $.post('api/customer_creation_files/customer_creation_data.php', { id: id }, function (response) {
        $('#customer_profile_id').val(id);
        $('#area_edit').val(response[0].area);
        $('#cus_id').val(response[0].cus_id);
        $('#first_name').val(response[0].first_name);
        $('#last_name').val(response[0].last_name);
        $('#dob').val(response[0].dob);
        $('#age').val(response[0].age);
        $('#mobile2').val(response[0].mobile2);
        $('#whatsapp').val(response[0].whatsapp);
        $('#aadhar_number').val(response[0].aadhar_number);
        $('#mobile1').val(response[0].mobile1);
        $('#cus_data').val(response[0].cus_data);
        $('#cus_status').val(response[0].cus_status);
        $('#address').val(response[0].address);
        $('#native_address').val(response[0].native_address);
        $('#occupation').val(response[0].occupation);
        $('#occ_detail').val(response[0].occ_detail);
        $('#multiple_loan').val(response[0].multiple_loan);
        if (response[0].whatsapp === response[0].mobile1) {
            $('#mobile1_radio').prop('checked', true);
            $('#selected_mobile_radio').val('mobile1');
        } else if (response[0].whatsapp === response[0].mobile2) {
            $('#mobile2_radio').prop('checked', true);
            $('#selected_mobile_radio').val('mobile2');
        }
        getAreaName()
        setTimeout(() => {
            getAutoGenCusId(id)  
            $('#area').trigger('change');
            getFamilyInfoTable()
        }, 1000);

        if (response[0].cus_data == 'Existing') {
            $('.cus_status_div').show();
            checkAdditionalRenewal(response[0].cus_id);
        } else {
            $('.cus_status_div').hide();
        }
        let path = "uploads/customer_creation/cus_pic/";
        $('#per_pic').val(response[0].pic);
        var img = $('#imgshow');
        img.attr('src', path + response[0].pic);
    }, 'json');

}
function existingCustmerProfile(aadhar_number) {
    $.post('api/customer_creation_files/customer_profile_existing.php', { aadhar_number }, function (response) {
        $('#customer_profile_id').val('');
        if (response == 'New') {
            $('#area_edit').val('');
            $('#first_name').val('');
            $('#last_name').val('');
            $('#dob').val('');
            $('#age').val('');
            $('#mobile2').val('');
            $('#whatsapp').val('');
            $('#mobile1').val('');
            $('#cus_data').val('New');
            $('#cus_status').val('');
            $('#address').val('');
            $('#native_address').val('');
            $('#occupation').val(''); 
            $('#occ_detail').val('');
            $('#multiple_loan').val('');
            $('.cus_status_div').hide();

            getFamilyInfoTable()
            getAutoGenCusId('')
            getAreaName()

            $('#per_pic').val('');
            var img = $('#imgshow');
            img.attr('src', 'img/avatar.png');
        } else {
            $('#area_edit').val(response[0].area);
            $('#cus_id').val(response[0].cus_id);
            $('#first_name').val(response[0].first_name);
            $('#last_name').val(response[0].last_name);
            $('#dob').val(response[0].dob);
            $('#age').val(response[0].age);
            $('#mobile2').val(response[0].mobile2);
            $('#whatsapp').val(response[0].whatsapp);
            $('#mobile1').val(response[0].mobile1);
            $('#cus_data').val('Existing');
            $('#cus_status').val(response[0].cus_status);
            $('#address').val(response[0].address);
            $('#native_address').val(response[0].native_address);
            $('#occupation').val(response[0].occupation);
            $('#occ_detail').val(response[0].occ_detail);
            $('#multiple_loan').val(response[0].multiple_loan);
            if (response[0].whatsapp === response[0].mobile1) {
                $('#mobile1_radio').prop('checked', true);
                $('#selected_mobile_radio').val('mobile1');
            } else if (response[0].whatsapp === response[0].mobile2) {
                $('#mobile2_radio').prop('checked', true);
                $('#selected_mobile_radio').val('mobile2');
            }
            getAreaName()
            setTimeout(() => {
                getFamilyInfoTable()
                $('#area').trigger('change');
               
            }, 1000);
            $('.cus_status_div').show();
         
            let path = "uploads/loan_entry/cus_pic/";
            $('#per_pic').val(response[0].pic);
            var img = $('#imgshow');
            img.attr('src', path + response[0].pic);
          

        }
    }, 'json');
}

$('button[type="reset"],#back_btn').click(function (event) {
    // event.preventDefault();
    $('input').each(function () {
        var id = $(this).attr('id');
        if (id !== 'cus_id') {
            $(this).val('');
        }
    });
    // Clear all textarea fields within the specific form
    $('#customer_creation').find('textarea').val('');

    //clear all upload inputs within the form.
    $('#customer_creation').find('input[type="file"]').val('');

    // Reset all select fields within the specific form
    $('#customer_creation').find('select').each(function () {
        $(this).val($(this).find('option:first').val());

    });
    $('#customer_creation').find('input[type="radio"]').prop('checked', false);
    //Reset all  images within the form
    $('#imgshow').attr('src', 'img/avatar.png');
    $('#customer_creation input').css('border', '1px solid #cecece');
    $('#customer_creation select').css('border', '1px solid #cecece');
    $('#customer_creation textarea').css('border', '1px solid #cecece');
    $('.cus_status_div').hide();
         

});
function getCustomerDelete(id) {
    $.post('api/customer_creation_files/delete_customer_creation.php', { id }, function (response) {
        if (response === 0) {
            swalError('Access Denied', 'Used in Loan Entry Screen');
        } else if (response === 1) {
            swalSuccess('Success', 'Customer Creation Deleted Successfully');
            getCustomerEntryTable();
        } else {
            swalError('Error', 'Failed to delete customer: ' + response);
        }
    }, 'json');
}
function getFamilyInfoTable() {
    let cus_id = $('#cus_id').val()
    $.post('api/customer_creation_files/family_creation_list.php', { cus_id }, function (response) {
        var columnMapping = [
            'sno',
            'fam_name',
            'fam_relationship',
            'fam_age',
            'fam_occupation',
            'fam_aadhar',
            'fam_mobile',
        ];
        appendDataToTable('#fam_info_table', response, columnMapping);
        setdtable('#fam_info_table');
    }, 'json')
}

function getFamilyTable() {
    let cus_id = $('#cus_id').val()
    $.post('api/customer_creation_files/family_creation_list.php', { cus_id: cus_id }, function (response) {
        var columnMapping = [
            'sno',
            'fam_name',
            'fam_relationship',
            'fam_age',
            'fam_occupation',
            'fam_aadhar',
            'fam_mobile',
            'action'
        ];
        appendDataToTable('#family_creation_table', response, columnMapping);
        setdtable('#family_creation_table');
        $('#family_form input').val('');
        $('#family_form input').css('border', '1px solid #cecece');
        $('#family_form select').css('border', '1px solid #cecece');
        $('#fam_relationship').val('');
        
    }, 'json')
}

function getFamilyDelete(id) {
    $.post('api/customer_creation_files/delete_family_creation.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'Family Info Deleted Successfully!');
            getFamilyTable();
        }  else {
            swalError('Warning', 'Error occur While Delete Family Info.');
        }
    }, 'json');
}
