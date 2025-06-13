$(document).ready(function () {
    //Move Loan Entry 
    $(document).on('click', '#add_centre, #back_btn', function () {
        $('#submit_centre_creation').prop('disabled', false);
        $('#centre_id').val('');
        swapTableAndCreation();
       
        setTimeout(() => {
        getAreaName()
        getBranchList()
        getRepresentInfoTable()
        }, 1000);
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
 
 
    $('#mobile1, #mobile2,#rep_mobile').change(function () {
        checkMobileNo($(this).val(), $(this).attr('id'));
    });
 
    $('#getlatlong').click(function () {
        event.preventDefault();
        
        $.getJSON('http://ip-api.com/json', function (data) {
            $('#latlong').val(data.lat + ',' + data.lon);
        });
    });
    
    ///////////////////////////////////////////Representative Info////////////////////////////////

    $('#submit_rep').click(function (event) {
        event.preventDefault();
        // Validation
        let centre_id = $('#centre_id').val();// Remove spaces from cus_id
        let rep_name = $('#rep_name').val();
        let rep_occupation = $('#rep_occupation').val();
        let rep_aadhar = $('#rep_aadhar').val().replace(/\s/g, '');
        let rep_mobile = $('#rep_mobile').val();
        let designation = $('#designation').val();
        let remark = $('#remark').val();
        let represent_id = $('#represent_id').val();

        var data = ['rep_name', 'rep_aadhar','rep_mobile']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });

        if (isValid) {
            $.post('api/centre_creation_files/submit_representative_info.php', { centre_id, rep_name, rep_occupation, rep_aadhar, rep_mobile, designation, remark, represent_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Representative Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Representative Info Updated Successfully!');
                }
                // Refresh the family table
                getRepresentTable();
            });
        }
    });
    $(document).on('click', '.representActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/centre_creation_files/represent_creation_data.php', { id: id }, function (response) {
            $('#represent_id').val(id);
            $('#rep_name').val(response[0].rep_name);
            $('#rep_aadhar').val(response[0].rep_aadhar);
            $('#rep_occupation').val(response[0].rep_occupation);
            $('#rep_mobile').val(response[0].rep_mobile);
            $('#designation').val(response[0].designation);
            $('#remark').val(response[0].remark);
        }, 'json');
    });

    $(document).on('click', '.representDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Representative Details?', getRepresentDelete, id);
        return;
    });
  




    /////////////////////////////////////////////////////////////Representative End//////////////////////////////
    ///////////////////////////////////////Centre Info///////////////////////
    $('#submit_centre_creation').click(function (event) {
        event.preventDefault();

        // Validation
        let famInfoRowCount = $('#rep_info_table').DataTable().rows().count();
        let centre_id = $('#centre_id').val();
        let centre_no = $("#centre_no").val();
        let centre_name = $('#centre_name').val();
        let mobile1 = $('#mobile1').val();
        let mobile2 = $('#mobile2').val();
        let area = $('#area').val();
        let branch = $('#branch').val();
        let latlong = $('#latlong').val();
        let pic = $('#pic')[0].files[0];
        let per_pic = $('#per_pic').val();
        let centre_create_id = $('#centre_create_id').val();

        let centreDetail = new FormData();
        centreDetail.append('centre_id', centre_id);
        centreDetail.append('centre_no', centre_no);
        centreDetail.append('centre_name', centre_name);
        centreDetail.append('mobile1', mobile1);
        centreDetail.append('mobile2', mobile2);
        centreDetail.append('area', area);
        centreDetail.append('branch', branch);
        centreDetail.append('latlong', latlong);
        centreDetail.append('pic', pic);
        centreDetail.append('per_pic', per_pic);
        centreDetail.append('centre_create_id', centre_create_id);

        var data = ['centre_id', 'centre_no', 'centre_name', 'area', 'mobile1','branch']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
       
        if (isValid) {
            if (famInfoRowCount === 0 ) {
                swalError('Warning', 'Please Fill out Representative Info!');
                return false;
            }
            $('#submit_centre_creation').prop('disabled', true);
            $.ajax({
                url: 'api/centre_creation_files/submit_centre.php',
                type: 'post',
                data: centreDetail,
                contentType: false,
                processData: false,
                cache: false,
                success: function (response) {
                    $('#submit_cus_map').prop('disabled', false);
                    response = JSON.parse(response);
                    if (response == '1') {
                        swalSuccess('Success', 'Centre Info Added Successfully!');
                    } else {
                        swalSuccess('Success', 'Centre Info Updated Successfully!');
                    }
                    $('#centre_create_id').val('');
                    getCentreEntryTable();
                    swapTableAndCreation()

                }
            });
        }
    });

    $(document).on('click', '.centreActionBtn', function () {
        $('#submit_centre_creation').prop('disabled', false);
        let id = $(this).attr('value');
        $('#centre_create_id').val(id);

        swapTableAndCreation();
        editCentreCreation(id)

    });

    /////////////////////////////////////////Centre Info/////////////////////////
    /////////////////////////////////Document End/////////////////////////////////////////////////
});
$(function () {
    getCentreEntryTable();
});
function getCentreEntryTable() {
    serverSideTable('#centre_create', '', 'api/centre_creation_files/centre_create_list.php');
    // setDropdownScripts();   
}
function swapTableAndCreation() {
    if ($('.centre_table_content').is(':visible')) {
        $('.centre_table_content').hide();
        $('#add_centre').hide();
        $('#centre_creation_content').show();
        $('#back_btn').show();
        // let customer_id = $('#customer_id').val();
        getAutoGenCentreId('');
    } else {
        $('.centre_table_content').show();
        $('#add_centre').show();
        $('#centre_creation_content').hide();
        $('#back_btn').hide();
    }
}
function getAutoGenCentreId(id) {
    $.post('api/centre_creation_files/get_autoGen_centre_id.php', { id }, function (response) {
        $('#centre_id').val(response);
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
function getBranchList() {
    $.post('api/area_creation_files/get_branch_list.php', function (response) {
        let appendBranchOption = '';
        appendBranchOption += '<option value="">Select Branch</option>';
        $.each(response, function (index, val) {
            let selected = '';
            let editBranch = $('#branch_edit').val();
            if (val.id == editBranch) {
                selected = 'selected';
            }
            appendBranchOption += "<option value='" + val.id + "' " + selected + ">" + val.branch_name + "</option>";
        });
        $('#branch').empty().append(appendBranchOption);

    }, 'json');
}
function getRepresentInfoTable() {
    let centre_id = $('#centre_id').val()
    $.post('api/centre_creation_files/represent_creation_list.php', { centre_id }, function (response) {
        var columnMapping = [
            'sno',
            'rep_name',
            'rep_aadhar',
            'rep_occupation',
            'rep_mobile',
            'designation',
            'remark'
        ];
        appendDataToTable('#rep_info_table', response, columnMapping);
        setdtable('#rep_info_table');
    }, 'json')
}

function getRepresentTable() {
    let centre_id = $('#centre_id').val()
    $.post('api/centre_creation_files/represent_creation_list.php', { centre_id: centre_id }, function (response) {
        var columnMapping = [
            'sno',
            'rep_name',
            'rep_aadhar',
            'rep_occupation',
            'rep_mobile',
            'designation',
            'remark',
            'action'
        ];
        appendDataToTable('#represent_creation_table', response, columnMapping);
        setdtable('#represent_creation_table');
        $('#represent_form input').val('');
        $('#represent_form input').css('border', '1px solid #cecece');
        $('#represent_form select').css('border', '1px solid #cecece');
        $('#represent_form select').each(function () {
            $(this).val($(this).find('option:first').val());
    
        });
        $('#represent_form textarea').val('');
        
    }, 'json')
}

function getRepresentDelete(id) {
      let centre_id = $('#centre_id').val()
      let centre_create_id = $('#centre_create_id').val()
    $.post('api/centre_creation_files/delete_represent_creation.php', { id, centre_id,centre_create_id }, function (response) {
        if (response == '0') {
            swalError('Warning', 'Have to maintain atleast one Representative Info');
        } else if (response == '1') {
            swalSuccess('Success', 'Representative Info Deleted Successfully!');
            getRepresentTable();
        } else if (response == '2') {
            swalError('Access Denied', 'Representative Member Already Used');
        } else {
            swalError('Warning', 'Error occur While Delete Representative Info.');
        }
    }, 'json');
}
function editCentreCreation(id) {
    $.post('api/centre_creation_files/centre_creation_data.php', { id: id }, function (response) {
        $('#centre_create_id').val(id);
        $('#centre_id').val(response[0].centre_id);
        $('#centre_no').val(response[0].centre_no);
        $('#centre_name').val(response[0].centre_name);
        $('#area_edit').val(response[0].area);
        $('#branch_edit').val(response[0].branch);
        $('#latlong').val(response[0].latlong);
        $('#mobile1').val(response[0].mobile1);
        $('#mobile2').val(response[0].mobile2);
        getAreaName()
        getBranchList();
        setTimeout(() => {
            getAutoGenCentreId(id)
        }, 1000);
        getRepresentInfoTable()
       
        let path = "uploads/customer_creation/cus_pic/";
        $('#per_pic').val(response[0].pic);
        var img = $('#imgshow');
        img.attr('src', path + response[0].pic);
    }, 'json');

}
$('button[type="reset"],#back_btn,#add_centre').click(function (event) {
    // event.preventDefault();
    $('input').each(function () {
        var id = $(this).attr('id');
        if (id !== 'centre_id') {
            $(this).val('');
        }
    });
    // Clear all textarea fields within the specific form
    $('#centre_creation').find('textarea').val('');

    //clear all upload inputs within the form.
    $('#centre_creation').find('input[type="file"]').val('');

    // Reset all select fields within the specific form
    $('#centre_creation').find('select').each(function () {
        $(this).val($(this).find('option:first').val());

    });
    //Reset all  images within the form
    $('#imgshow').attr('src', 'img/avatar.png');
    $('#centre_creation input').css('border', '1px solid #cecece');
    $('#centre_creation select').css('border', '1px solid #cecece');
    $('#centre_creation textarea').css('border', '1px solid #cecece');

});

