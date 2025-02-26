$(function () {
  getcustomerlist();
});

$(document).ready(function () {
  $(document).on("click", "#back_btn", function () {
    $(".radio-container").hide();
    $(".customer_info_table_content").hide();
    $("#back_btn").hide();
    $(".customer_table_content").show();
    $(".centre_table_content").hide();
    $(".document_table_content").hide();
    getcustomerlist();
  });

  $("input[name=customer_data]").click(function () {
    let customerDataType = $(this).val();

    if (customerDataType == "customer_info") {
      let cus_id = $("#cus_id_upd").val();
      console.log("cus_map_id"+cus_id);
      $(".customer_info_table_content").show();
      $(".centre_table_content").hide();
      $(".document_table_content").hide();
      $("#documentation_form").hide();
      getCustomerDetails(cus_id);
    } else if (customerDataType == "centre_summary") {
      let cus_id = $("#cus_id").val();
      console.log("cus_id  in centre_cummary "+cus_id);
      $(".customer_info_table_content").hide();
      $(".centre_table_content").show();
      $(".document_table_content").hide();
      $("#documentation_form").hide();
      $("#centre_current").trigger("click");
      getCentreCurrentTable(cus_id);
    } else if (customerDataType == "documentation") {
      $(".customer_info_table_content").hide();
      $(".centre_table_content").hide();
      $(".document_table_content").show();
      $("#documentation_form").hide();
      addDocumentation();
    }
  });
  $(document).on("click", ".customerActionBtn", function () {
    var cus_id = $(this).attr("value");
    $("#cus_id_upd").val(cus_id);
    $(".radio-container").show();
    $(".customer_info_table_content").show();
    $("#back_btn").show();
    $(".customer_table_content").hide();
       $("#customer_info").trigger("click");
    getCustomerDetails(cus_id);
  });
  $("#submit_cus_creation").click(function (event) {
    event.preventDefault();

    // Validation

    let cus_id = $("#cus_id").val();
    let aadhar_number = $("#aadhar_number").val().replace(/\s/g, "");
    let cus_data = $("#cus_data").val();
    let cus_status = $("#cus_status").val();
    let first_name = $("#first_name").val();
    let last_name = $("#last_name").val();
    let dob = $("#dob").val();
    let age = $("#age").val();
    let area = $("#area").val();
    let mobile1 = $("#mobile1").val();
    let mobile2 = $("#mobile2").val();
    let whatsapp = $("#whatsapp").val();
    let occ_detail = $("#occ_detail").val();
    let occupation = $("#occupation").val();
    let address = $("#address").val();
    let native_address = $("#native_address").val();
    let multiple_loan = $("#multiple_loan").val();
    let pic = $("#pic")[0].files[0];
    let per_pic = $("#per_pic").val();
    let customer_profile_id = $("#customer_profile_id").val();

    let cusDetail = new FormData();
    cusDetail.append("cus_id", cus_id);
    cusDetail.append("aadhar_number", aadhar_number);
    cusDetail.append("cus_data", cus_data);
    cusDetail.append("cus_status", cus_status);
    cusDetail.append("first_name", first_name);
    cusDetail.append("last_name", last_name);
    cusDetail.append("dob", dob);
    cusDetail.append("age", age);
    cusDetail.append("area", area);
    cusDetail.append("mobile1", mobile1);
    cusDetail.append("mobile2", mobile2);
    cusDetail.append("whatsapp", whatsapp);
    cusDetail.append("occupation", occupation);
    cusDetail.append("occ_detail", occ_detail);
    cusDetail.append("address", address);
    cusDetail.append("native_address", native_address);
    cusDetail.append("multiple_loan", multiple_loan);
    cusDetail.append("pic", pic);
    cusDetail.append("per_pic", per_pic);
    cusDetail.append("customer_profile_id", customer_profile_id);

    var data = [
      "cus_id",
      "first_name",
      "aadhar_number",
      "area",
      "mobile1",
      "multiple_loan",
    ];

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });

    if (isValid) {
      $("#submit_cus_creation").prop("disabled", true);
      $.ajax({
        url: "api/customer_creation_files/submit_customer.php",
        type: "post",
        data: cusDetail,
        contentType: false,
        processData: false,
        cache: false,
        success: function (response) {
          $("#submit_cus_creation").prop("disabled", false);
          response = JSON.parse(response);
          if (response == "1") {
            swalSuccess("Success", "Customer Info Added Successfully!");
          } else {
            swalSuccess("Success", "Customer Info Updated Successfully!");
            $("#customer_info").trigger("click");

          }
          $("html, body").animate(
            {
              scrollTop: $(".page-content").offset().top,
            },
            3000
          );
        },
      });
    }
  });
  $("#submit_family").click(function (event) {
    event.preventDefault();
    // Validation
    let cus_id = $("#cus_id").val(); // Remove spaces from cus_id
    let fam_name = $("#fam_name").val();
    let fam_relationship = $("#fam_relationship").val();
    let fam_age = $("#fam_age").val();
    let fam_occupation = $("#fam_occupation").val();
    let fam_aadhar = $("#fam_aadhar").val().replace(/\s/g, "");
    let fam_mobile = $("#fam_mobile").val();
    let family_id = $("#family_id").val();

    var data = ["fam_name", "fam_relationship", "fam_mobile", "fam_aadhar"];

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });

    if (isValid) {
      $.post(
        "api/customer_creation_files/submit_family_info.php",
        {
          cus_id,
          fam_name,
          fam_relationship,
          fam_age,
          fam_occupation,
          fam_aadhar,
          fam_mobile,
          family_id,
        },
        function (response) {
          if (response == "1") {
            swalSuccess("Success", "Family Info Added Successfully!");
          } else {
            swalSuccess("Success", "Family Info Updated Successfully!");
          }
          // Refresh the family table
          getFamilyTable();
        }
      );
    }
  });
  $(document).on("click", ".familyActionBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    $.post(
      "api/customer_creation_files/family_creation_data.php",
      { id: id },
      function (response) {
        $("#family_id").val(id);
        $("#fam_name").val(response[0].fam_name);
        $("#fam_relationship").val(response[0].fam_relationship);
        $("#fam_age").val(response[0].fam_age);
        $("#fam_occupation").val(response[0].fam_occupation);
        $("#fam_aadhar").val(response[0].fam_aadhar);
        $("#fam_mobile").val(response[0].fam_mobile);
      },
      "json"
    );
  });

  $(document).on("click", ".familyDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Family Details?",
      getFamilyDelete,
      id
    );
    return;
  });
  $("#dob").on("change", function () {
    var dob = new Date($(this).val());
    var today = new Date();
    var age = today.getFullYear() - dob.getFullYear();
    var m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
      age--;
    }
    $("#age").val(age);
  });
  $('input[name="mobile_whatsapp"]').on("change", function () {
    let selectedValue = $(this).val();
    let mobileNumber;

    if (selectedValue === "mobile1") {
      mobileNumber = $("#mobile1").val();
    } else if (selectedValue === "mobile2") {
      mobileNumber = $("#mobile2").val();
    }

    $("#whatsapp").val(mobileNumber);
  });
  $("#pic").change(function () {
    let pic = $("#pic")[0];
    let img = $("#imgshow");
    img.attr("src", URL.createObjectURL(pic.files[0]));
    checkInputFileSize(this, 200, img);
  });
  $('input[data-type="adhaar-number"]').keyup(function () {
    var value = $(this).val();
    value = value
      .replace(/\D/g, "")
      .split(/(?:([\d]{4}))/g)
      .filter((s) => s.length > 0)
      .join(" ");
    $(this).val(value);
  });

  $('input[data-type="adhaar-number"]').change(function () {
    let len = $(this).val().length;
    if (len < 14) {
      $(this).val("");
      swalError("Warning", "Kindly Enter Valid Aadhaar Number");
    }
  });
  $("#mobile1, #mobile2, #whatsapp, #fam_mobile").change(function () {
    checkMobileNo($(this).val(), $(this).attr("id"));
  });
  $("#clear_fam_form").click(function (event) {
    event.preventDefault();
    $("#family_id").val("");
    $("#family_form select").each(function () {
      $(this).val($(this).find("option:first").val());
    });
    $("#family_form textarea").val("");
    $("#family_form input").val("");
  });

  $('input[name=customer_data_type]').click(function () {
    let centreType = $(this).val();
    if (centreType == 'cen_current') {
        $('.centre_loan_current').show();
        $('.centre_loan_closed').hide();
        let cus_id = $("#cus_id").val();
        getCentreCurrentTable(cus_id);
    } else if (centreType == 'cen_closed') {
        $('.centre_loan_closed').show();
        $('.centre_loan_current').hide();
        let cus_id = $("#cus_id").val();
        getCentreClosedTable(cus_id)


    }
})
$(document).on("click", ".addDocument", function () {
  console.log("kkkkk");
  event.preventDefault();
  var cus_id = $(this).attr("value");
  $('#documentation_form').show();
  $('.document_table_content').hide();
  getDocumentDetails(cus_id);
  getCustomerList(cus_id);
  getAutoGenDocId('')
});


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
              if (response == '1') {
                  swalSuccess('Success', 'Document Info Added Successfully')
              } else {
                  swalError('Alert', 'Failed')
              }
              $(".addDocument").trigger("click");

          }
      });
  }
});
$(document).on('click', '.docDeleteBtn', function () {
  let id = $(this).attr('value');
  swalConfirm('Delete', 'Are you sure you want to delete this document?', deleteDocInfo, id);
});
$(document).on('click','.due_chart', function(){
  var cus_mapping_id = $(this).attr('value');
  var loan_id = $(this).attr('loan_id');
  
  $('#due_chart_model').modal('show');
  dueChartList(cus_mapping_id,loan_id); // To show Due Chart List.
  setTimeout(()=>{
      $('.print_due_coll').click(function(){
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
                      url:'api/collection_files/print_collection.php',
                      data:{'coll_id':id},
                      type:'post',
                      cache:false,
                      success:function(html){
                          $('#printcollection').html(html)
                          // Get the content of the div element
                          var content = $("#printcollection").html();
                      }
                  })
              }
          })
      })
  },1000)
});
$(document).on('click','.closed_due_chart', function(){
  var cus_mapping_id = $(this).attr('value');
  var loan_id = $(this).attr('loan_id');
  
  $('#due_chart_model').modal('show');
  dueChartList(cus_mapping_id,loan_id); // To show Due Chart List.
  setTimeout(()=>{
      $('.print_due_coll').click(function(){
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
                      url:'api/collection_files/print_collection.php',
                      data:{'coll_id':id},
                      type:'post',
                      cache:false,
                      success:function(html){
                          $('#printcollection').html(html)
                          // Get the content of the div element
                          var content = $("#printcollection").html();
                      }
                  })
              }
          })
      })
  },1000)
});

$(document).on("click", "#cancel_btn", function () {
  console.log("hlll");
  $("#documentation").trigger("click");

})


});

function getcustomerlist() {
  serverSideTable(
    "#customer_list_table",
    "",
    "api/customer_data_files/get_customer_list.php"
  );
}
function getCustomerDetails(id) {
  $.post(
    "api/customer_data_files/get_customer_deatils.php",
    { id: id },
    function (response) {
      $("#customer_profile_id").val(id);
      $("#area_edit").val(response[0].area);
      $("#cus_id").val(response[0].cus_id);
      $("#first_name").val(response[0].first_name);
      $("#last_name").val(response[0].last_name);
      $("#dob").val(response[0].dob);
      $("#age").val(response[0].age);
      $("#mobile2").val(response[0].mobile2);
      $("#whatsapp").val(response[0].whatsapp);
      $("#aadhar_number").val(response[0].aadhar_number);
      $("#mobile1").val(response[0].mobile1);
      $("#cus_data").val(response[0].cus_data);
      $("#cus_status").val(response[0].cus_status);
      $("#address").val(response[0].address);
      $("#native_address").val(response[0].native_address);
      $("#occupation").val(response[0].occupation);
      $("#occ_detail").val(response[0].occ_detail);
      $("#multiple_loan").val(response[0].multiple_loan);
      if (response[0].whatsapp === response[0].mobile1) {
        $("#mobile1_radio").prop("checked", true);
        $("#selected_mobile_radio").val("mobile1");
      } else if (response[0].whatsapp === response[0].mobile2) {
        $("#mobile2_radio").prop("checked", true);
        $("#selected_mobile_radio").val("mobile2");
      }
      getAreaName();
      setTimeout(() => {
        $("#area").trigger("change");
        getFamilyInfoTable();
      }, 1000);

      if (response[0].cus_data == "Existing") {
        $(".cus_status_div").show();
        checkAdditionalRenewal(response[0].cus_id);
      } else {
        $(".cus_status_div").hide();
      }
      let path = "uploads/customer_creation/cus_pic/";
      $("#per_pic").val(response[0].pic);
      var img = $("#imgshow");
      img.attr("src", path + response[0].pic);
    },
    "json"
  );
}
function getAreaName() {
  $.post(
    "api/customer_creation_files/get_area.php",
    function (response) {
      let appendAreaOption = "";
      appendAreaOption += "<option value=''>Select Area Name</option>";
      $.each(response, function (index, val) {
        let selected = "";
        let editArea = $("#area_edit").val();
        if (val.id == editArea) {
          selected = "selected";
        }
        appendAreaOption +=
          "<option value='" +
          val.id +
          "' " +
          selected +
          ">" +
          val.areaname +
          "</option>";
      });
      $("#area").empty().append(appendAreaOption);
    },
    "json"
  );
}
function getFamilyInfoTable() {
  let cus_id = $("#cus_id").val();
  $.post(
    "api/customer_creation_files/family_creation_list.php",
    { cus_id },
    function (response) {
      var columnMapping = [
        "sno",
        "fam_name",
        "fam_relationship",
        "fam_age",
        "fam_occupation",
        "fam_aadhar",
        "fam_mobile",
      ];
      appendDataToTable("#fam_info_table", response, columnMapping);
      // setdtable("#fam_info_table");
    },
    "json"
  );
}
function checkAdditionalRenewal(cus_id) {
  $.post(
    "api/customer_creation_files/check_additional_renewal.php",
    { cus_id },
    function (response) {
      $("#cus_status").val(response);
    },
    "json"
  );
}
function getFamilyTable() {
  let cus_id = $("#cus_id").val();
  $.post(
    "api/customer_creation_files/family_creation_list.php",
    { cus_id: cus_id },
    function (response) {
      var columnMapping = [
        "sno",
        "fam_name",
        "fam_relationship",
        "fam_age",
        "fam_occupation",
        "fam_aadhar",
        "fam_mobile",
        "action",
      ];
      appendDataToTable("#family_creation_table", response, columnMapping);
      setdtable("#family_creation_table");
      $("#family_form input").val("");
      $("#family_form input").css("border", "1px solid #cecece");
      $("#family_form select").css("border", "1px solid #cecece");
      $("#fam_relationship").val("");
    },
    "json"
  );
}


// centre Summarry function
function getCentreCurrentTable(cus_id) {
    let params = { 'cus_id': cus_id };
    serverSideTable('#centre_current_table', params, 'api/customer_data_files/centre_current_list.php');
}
function getCentreClosedTable(cus_id) {
    let params = { 'cus_id': cus_id };
    serverSideTable('#centre_close_table', params, 'api/customer_data_files/centre_closed_list.php');
}

// documentation function 
function addDocumentation(){
    let cus_id = $("#cus_id").val();
    console.log("customer id "+cus_id);
    $.post(
      "api/customer_data_files/get_loan_details.php",
      { cus_id: cus_id },
      function (response) {
        var columnMapping = [
          "sno",
          "cus_id",
          "first_name",
          "loan_id",
          "centre_id",
          "centre_name",
          "centre_status",
          "action",
        ];
        appendDataToTable("#document_list_table", response, columnMapping);
        setdtable("#document_list_table");
    
    } ,"json")

}
function getDocumentDetails(cus_id){
  $.post('api/customer_data_files/get_doc_info.php', { cus_id }, function (response) {
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
    // setdtable('#document_info')
    console.log("helooooooooo");
    console.log("response"+response)
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
function getAutoGenDocId(id) {
  $.post('api/loan_issue_files/get_doc_id.php', { id }, function (response) {
      $('#doc_id').val(response);
  }, 'json');
}
function getCustomerList(cus_id) {
 
  $.post('api/customer_data_files/get_cus_list.php', { cus_id }, function (response) {
      let cusOptn = '';
      cusOptn = '<option value="">Select Customer Name</option>';
      response.forEach(val => {
          cusOptn += '<option value="' + val.id + '">' + val.first_name + ' - ' + val.mobile1 + ' - ' + val.areaname + '</option>';
      });
      $('#customer_name').empty().append(cusOptn);
  }, 'json');
}
function deleteDocInfo(id) {
  $.post('api/loan_issue_files/delete_doc_info.php', { id }, function (response) {
      if (response == '1') {
          swalSuccess('success', 'Doc Info Deleted Successfully');
          $(".addDocument").trigger("click");
      } else {
          swalError('Alert', 'Delete Failed')
      }
  }, 'json');
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
function closeChartsModal() {
  $('#due_chart_model').modal('hide');
  $('#penalty_model').modal('hide');
  $('#fine_model').modal('hide');
}
function dueChartList(cus_mapping_id,loan_id){
  $.ajax({
      url: 'api/collection_files/get_due_chart_list.php',
      data: {'cus_mapping_id':cus_mapping_id},
      type:'post',
      cache: false,
      success: function(response){
          $('#due_chart_table_div').empty();
          $('#due_chart_table_div').html(response);
      }
  }).then(function(){
    
      $.post('api/collection_files/get_due_method_name.php',{loan_id},function(response){
          $('#dueChartTitle').text('Due Chart ( '+ response['due_month'] + ' - '+ response['loan_type'] +' )');
      },'json');
  })

}
