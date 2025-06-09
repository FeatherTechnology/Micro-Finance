$(document).ready(function () {
  $("#submit_search").click(function (event) {
    event.preventDefault();

    // let aadhar_no = $("#aadhar_no").val();
    let aadhar_no = $('#aadhar_no').val().replace(/\D/g, "")
    let cus_name = $("#cust_name").val();
    let centre_no = $("#centre_no").val();
    let mobile = $("#mobile").val();
    let centre_name = $("#centre_name").val();
    let area = $("#area").val();

    if (validate()) {
      $.ajax({
        url: "api/search_files/search_customer.php",
        type: "POST",
        data: { aadhar_no, cus_name, centre_no, centre_name, mobile, area },
        success: function (data) {
          $("#custome_list_content").show();
          $("#customer_details").trigger("click");

          $("#search_content").show();
          $("#centre_list_content").hide();
          getSearchTable(data);
          getcentreList();
        },
      });
    } else {
      $("#search_content").hide();
    }
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
  
  $("input[name=search_type]").click(function () {
    let search_type = $(this).val();
    if (search_type == "customer_details") {
      $("#custome_list_content").show();
      $("#centre_list_content").hide();
    } else if (search_type == "centre_details") {
      $("#custome_list_content").hide();
      $("#centre_list_content").show();
    }
  });

  $("#back_to_search").click(function () {
    $("#customer_loan_content").hide();
    $("#search_content").show();
    $("#search").show();
    $("#customer_details").trigger("click");
  });

  $(document).on("click", ".view_customer", function () {
    $("#customer_loan_content").show();
    $("#search_content").hide();
    $("#search").hide();
    let cus_id = $(this).attr("value");
    getLoanDetails(cus_id);
  });

  $(document).on("click", ".view_centre_customer", function () {
    $("#customer_centre_content").show();
    $("#search").hide();
    $("#search_content").hide();
    $("#centre_list_content").hide();
    let centre_id = $(this).attr("centre_id");
    getCentreDetails(centre_id);
  });
  $(document).on("click", "#back_to_centre_list", function () {
    $("#customer_centre_content").hide();
    $("#search").show();
    $("#search_content").show();
    $("#centre_list_content").show();
    $("#centre_details").trigger("click");

    let centre_id = $(this).attr("centre_id");
    getCentreDetails(centre_id);
  });

  $(document).on("click", ".view_chart", function () {
    var cus_mapping_id = $(this).attr("value");
    var loan_id = $(this).attr("loan_id");
    $("#due_chart_model").modal("show");
    dueChartList(cus_mapping_id, loan_id); // To show Due Chart List.
    setTimeout(() => {
      $(".print_due_coll").click(function () {
        var id = $(this).attr("value");
        Swal.fire({
          title: "Print",
          text: "Do you want to print this collection?",
          imageUrl: "img/printer.png",
          imageWidth: 300,
          imageHeight: 210,
          imageAlt: "Custom image",
          showCancelButton: true,
          confirmButtonColor: "#009688",
          cancelButtonColor: "#d33",
          cancelButtonText: "No",
          confirmButtonText: "Yes",
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "api/collection_files/print_collection.php",
              data: { coll_id: id },
              type: "post",
              cache: false,
              success: function (html) {
                $("#printcollection").html(html);
                // Get the content of the div element
                var content = $("#printcollection").html();
              },
            });
          }
        });
      });
    }, 1000);
  });
});

function validate() {
  let response = true;

  // let aadhar_no = $("#aadhar_no").val().trim();
  let aadhar_no = $('#aadhar_no').val().replace(/\D/g, "")
  let cus_name = $("#cust_name").val().trim();
  let centre_no = $("#centre_no").val().trim();
  let mobile = $("#mobile").val().trim();
  let centre_name = $("#centre_name").val().trim();
  let area = $("#area").val().trim();

  // Reset all field borders initially
  $("#aadhar_no, #cust_name, #centre_no, #mobile,#centre_name,#area").css(
    "border",
    "1px solid #cecece"
  );

  // Check if any one field is filled
  if (aadhar_no || cus_name || centre_no || mobile || centre_name || area) {
    // If any field is filled, reset the other fields' borders
    if (aadhar_no) {
      $("#cust_name, #centre_no, #mobile,#centre_name,#area").css(
        "border",
        "1px solid #cecece"
      );
    } else if (cus_name) {
      $("#aadhar_no, #centre_no, #mobile,#centre_name,#area").css(
        "border",
        "1px solid #cecece"
      );
    } else if (centre_no) {
      $("#aadhar_no, #cust_name,  #mobile,#centre_name,#area").css(
        "border",
        "1px solid #cecece"
      );
    } else if (mobile) {
      $("#aadhar_no, #cust_name,#centre_no, #centre_name,#area").css(
        "border",
        "1px solid #cecece"
      );
    } else if (area) {
      $("#aadhar_no, #cust_name,#centre_no, #centre_name,#mobile").css(
        "border",
        "1px solid #cecece"
      );
    }
  } else {
    // If no fields are filled, show validation errors
    if (!validateField(aadhar_no, "aadhar_no")) {
      response = false;
    }
    if (!validateField(cus_name, "cust_name")) {
      response = false;
    }
    if (!validateField(centre_no, "centre_no")) {
      response = false;
    }
    if (!validateField(mobile, "mobile")) {
      response = false;
    }
    if (!validateField(centre_name, "centre_name")) {
      response = false;
    }
    if (!validateField(area, "area")) {
      response = false;
    }
  }

  return response;
}
function getSearchTable(data) {
  // Assuming response is in JSON format and contains customer data
  let response = JSON.parse(data);
  // if (response && response.length > 0) {
  var columnMapping = [
    "sno",
    "cus_id",
    "first_name",
    "centre_name",
    "area",
    "branch_name",
    "mobile1",
    "action",
  ];
  appendDataToTable("#customer_list", response, columnMapping);
  setdtable("#customer_list");

  // }
}
function getcentreList() {
  event.preventDefault();

  // let aadhar_no = $("#aadhar_no").val();
  let aadhar_no = $('#aadhar_no').val().replace(/\D/g, "")
  let cus_name = $("#cust_name").val();
  let centre_no = $("#centre_no").val();
  let mobile = $("#mobile").val();
  let centre_name = $("#centre_name").val();
  let area = $("#area").val();

  if (validate()) {
    $.ajax({
      url: "api/search_files/search_centre.php",
      type: "POST",
      data: { aadhar_no, cus_name, centre_no, centre_name, mobile, area },
      success: function (data) {
        $("#search_content").show();
        $("#custome_list").hide();
        getcentreTable(data);
      },
    });
  } else {
    $("#search_content").hide();
  }
}

function getcentreTable(data) {
  let response = JSON.parse(data);
  var columnMapping = [
    "sno",
    "centre_id",
    "centre_no",
    "centre_name",
    "loan_id",
    "area",
    "branch_name",
    "mobile1",
    "designation",
    "action",
  ];
  appendDataToTable("#centre_list", response, columnMapping);
  setdtable("#centre_list");
}

function getLoanDetails(cus_id) {
  $.post(
    "api/search_files/get_loan_details.php",
    { cus_id },
    function (response) {
      let cusMapColumn = [
        "sno",
        "loan_date",
        "loan_id",
        "loan_category",
        "loan_amount",
        "status",
        "action",
      ];
      appendDataToTable("#customer_loan_list", response, cusMapColumn);
    },
    "json"
  );
}

function getCentreDetails(centre_id) {
  $.post(
    "api/search_files/get_centre_details.php",
    { centre_id },
    function (response) {
      let cusMapColumn = [
        "sno",
        "cus_id",
        "first_name",
        "aadhar_number",
        "mobile1",
        "areaname",
      ];
      appendDataToTable("#customer_centre_list", response, cusMapColumn);
    },
    "json"
  );
}
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

function closeChartsModal() {
  $("#due_chart_model").modal("hide");
  $("#penalty_model").modal("hide");
  $("#fine_model").modal("hide");
}
