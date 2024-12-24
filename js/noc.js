$(function () {
  getNocTable();
});
$(document).ready(function () {
  // click view button
  $(document).on("click", "#view", function () {
    let id = $(this).attr("value");
    let loan_id = $(this).attr("loan_id");
    $("#noc_content").show();
    $("#customer_list").show();
    $("#noc_date").show();
    $("#noc_list").hide();
    getCentreDetails(id);
    getDocCreationTable(loan_id);
    getCustomerName(loan_id);
    setTimeout(() => {
      setSubmittedDisabled();
    }, 500);
  });
  // click back button
  $(document).on("click", "#back_btn", function () {
    $("#noc_content").hide();
    $("#customer_list").hide();
    $("#noc_date").hide();
    $("#noc_list").show();
    getNocTable();
  });

  $("#noc_member").change(function () {
    setTimeout(() => {
      setValuesInTables();
    }, 500);
  });

  $(document).on("click", ".docCheckbox", function () {
    setValuesInTables();
    removeValuesInTables();
  });

  $(document).on("click", "#submit_noc", function () {
    submitCustomer();
  });

  setCurrentDate("#date_of_noc");
});

// Function start
function getNocTable() {
  serverSideTable("#noc_table", "", "api/noc_files/noc_list.php");
}

// get centre details
function getCentreDetails(id) {
  $.post(
    "api/closed_files/get_centre_details.php",
    { id },
    function (response) {
      if (response.length > 0) {
        $("#loan_id").val(response[0].loan_id);
        $("#centre_id").val(response[0].centre_id);
        $("#centre_no").val(response[0].centre_no);
        $("#centre_name").val(response[0].centre_name);
        $("#mobile").val(response[0].mobile1);
        $("#area").val(response[0].areaname);
        $("#branch").val(response[0].branch_name);
      }
    },
    "json"
  );
}

// get document list
function getDocCreationTable(loan_id) {
  $.post(
    "api/noc_files/doc_list.php",
    { loan_id },
    function (response) {
      let docInfoColumn = [
        "sno",
        "doc_id",
        "cus_id",
        "first_name",
        "doc_name",
        "doc_type",
        "count",
        "remark",
        "date_of_noc",
        "hand_over_person",
        "action",
      ];
      appendDataToTable("#document_list_table", response, docInfoColumn);
    },
    "json"
  );
}

// get customer name
function getCustomerName(loan_id) {
  $.post(
    "api/noc_files/getCustomerName.php",
    { loan_id },
    function (response) {
      //   let appendMember = "";
      $("#noc_member").empty();

      $("#noc_member").append('<option value="">Select Member Name</option>');
      $.each(response, function (index, val) {
        let selected = "";
        $("#noc_member").append(
          '<option value="' +
            val.cus_id +
            '" ' +
            " customer_name='" +
            val.first_name +
            "'>" +
            val.first_name +
            "</option>"
        );
      });
    },
    "json"
  );
}

function appendCuatomerList(cus_id, date_of_noc, customer_name) {
  $("#document_list_table tbody tr").each(function () {
    console.log("inside");
    let currentCusId = $(this).find("td").eq(2).text().trim();
    console.log("currentCusId" + currentCusId);
    console.log("customer id" + cus_id); 

    if (currentCusId === cus_id) {
      console.log("iside inside");
      $(this).find("td").eq(8).text(date_of_noc);
      $(this).find("td").eq(9).text(customer_name);
      $(".docCheckbox").prop("checked", true);
    }
  });
}

function submitCustomer() {
  var dataToSave = [];
  let loan_id = $("#loan_id").val();
  console.log("login id" + loan_id);
  $("#document_list_table tbody tr").each(function () {
    var doc_id = $(this).find("td").eq(1).text().trim();
    var date_of_noc = $(this).find("td").eq(8).text().trim();
    var customer_name = $(this).find("td").eq(9).text().trim();
    // var isChecked = $(this).find("td").eq(10).find("input[type='checkbox']").is(":checked");
    var isCheckedAndDisabled = $(this)
      .find("td")
      .eq(10)
      .find("input[type='checkbox']");
    var isChecked = isCheckedAndDisabled.is(":checked");
    var isDisabled = isCheckedAndDisabled.prop("disabled");
    // var isNotChecked=!isChecked;
    if (
      doc_id !== "" &&
      customer_name !== "" &&
      date_of_noc !== "" &&
      isChecked &&
      !isDisabled
    ) {
      dataToSave.push({
        doc_id: doc_id,
        loan_id: loan_id,
        date_of_noc: date_of_noc,
        customer_name: customer_name,
      });
    }
  });
  if (dataToSave.length > 0) {
    $.ajax({
      url: "api/noc_files/submit_customer_noc.php",
      type: "POST",
      dataType: "json",
      data: { customers: dataToSave },
      success: function (response) {
        if (response.success) {
          alert("NOC Completed");
          getDocCreationTable(loan_id);
          setTimeout(() => {
            setSubmittedDisabled();
          }, 500);
        } else {
          alert("Failed to save data: " + response.message);
        }
      },
      error: function () {
        alert("An error occurred while saving data.");
      },
    });
  } else {
    alert("Please Entre NOC Details");
  }
}


function setValuesInTables() {
  let member = $("#noc_member").val();
  let date = $("#date_of_noc").val();
  let formattedDate = member != "" ? formatDate(date) : "";
  let name = member != "" ? $("#noc_member").find(":selected").text() : "";
  let checked = false;
  $(".docCheckbox").each(function () {
    if ($(this).is(":checked")) {
      checked = true;
      $(this).closest("tr").find("td:nth-child(10)").text(name);
      $(this).closest("tr").find("td:nth-child(9)").text(formattedDate);
    }
  });
}

function removeValuesInTables() {
  $(".docCheckbox").each(function () {
    if (!$(this).is(":checked")) {
      $(this).closest("tr").find("td:nth-child(10)").text("");
      $(this).closest("tr").find("td:nth-child(9)").text("");
    }
  });
}

function formatDate(inputDate) {
  // Split the input date into year, month, and day components
  let parts = inputDate.split("-");
  // Rearrange them in dd-mm-yyyy format
  return parts[2] + "-" + parts[1] + "-" + parts[0];
}


function setSubmittedDisabled() {
  $(".docCheckbox").each(function () {
    if ($(this).attr("data-id") == "1") {
      $(this).closest("tr").addClass("disabled-row");
      $(this).attr("checked", true).attr("disabled", true);
    }
  });

  var cheque_checkDisabled =
    $(".docCheckbox:disabled").length === $(".docCheckbox").length;

  if (cheque_checkDisabled) {
    $("#submit_noc").hide();
  } else {
    $("#submit_noc").show();
  }
}
