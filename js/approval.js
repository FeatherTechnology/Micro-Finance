$(document).ready(function () {
  $(document).on("click", "#add_loan,#back_btn", function () {
    swapTableAndCreation();
    clearLoanCalcForm();
    $("#profit_type_calc_scheme").hide();
    $("#centre_id_hidden").val("");
  });

  $("input[name=loan_entry_type]").click(function () {
    let loanEntryType = $(this).val();
    if (loanEntryType == "loan_details") {
      $("#loan_entry_loan_calculation").show();
      $("#loan_entry_calculation").hide();
    } else if (loanEntryType == "centre_limit") {
      $("#loan_entry_loan_calculation").hide();
      $("#loan_entry_calculation").show();
      let centre_id = $("#centre_id_hidden").val();
      getCentrelimit(centre_id);
      $("#approval_centre_limit").css(
        "border",
        "1px solid #CECECE"
      );
    }
  });

  $("#Centre_id").change(function () {
    if ($(this).val() != "") {
      getCentreDetails($(this).val());
      getCentreMapTable($(this).val())
      getRepresentInfoTable($(this).val());
      $("#Centre_id_edit").val($(this).val());
    } else {
      $("#centre_no").val("");
      $("#centre_name").val("");
      $("#centre_mobile").val("");
    }
  });

  /////////////////////////////////////////////////////////////// Loan Calculation START //////////////////////////////////////////////////////////////////////

  $("#loan_category_calc").change(function () {
    if ($(this).val() != "") {
      const selectedOption = $("#loan_category_calc option:selected");
      const limitValue = selectedOption.attr("limit");
      let loanCatId = $("#loan_category_calc").val();
      $("#loan_amount_calc").val("");
      $("#loan_amount_calc").attr(
        "onChange",
        `if( parseFloat($(this).val()) > '` +
        limitValue +
        `' ){ alert("Enter Lesser than '${limitValue}'"); $(this).val(""); }`
      ); //To check value between range
      $("#profit_type_calc").val("").trigger("change");
      $("#loan_category_calc2").val($(this).val());
    }
  });

  $('#submit_cus_map').click(function (event) {
    event.preventDefault(); // Prevent the default form submission
    let centre_id = $('#Centre_id').val();
    let add_customer = $('#add_customer').val().trim(); // Trim to remove any extra spaces
    let loan_id_calc = $('#loan_id_calc').val();
    let customer_mapping = $('#customer_mapping').val().trim(); // Get the customer_mapping value
    let total_cus = $('#total_cus').val().trim(); // Total members limit
    let designation = $('#designation').val().trim(); // Get the designation value
    let customer_amount = parseFloat($('#customer_amount').val()) || 0;
    let loan_amount_calc = parseFloat($('#loan_amount_calc').val()) || 0;
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
      let currentCustomerCount = $('#cus_mapping_table tbody tr').length;
      if (currentCustomerCount >= total_cus) {
        swalError('Warning', 'The Customer Mapping Limit is Exceed.');
        $('#add_customer').val('');
        $('#designation').val('');
        $('#customer_mapping').val('');
        $('#customer_amount').val('');
        return; // Stop the process if customer count exceeds total_cus
      }
      // Calculate the total allocated amount in the table
      let totalAllocatedAmount = 0;
      $('#cus_mapping_table tbody tr').each(function () {
        let existingAmount = parseFloat($(this).find('td:nth-child(9)').text()) || 0; // Get the amount from 8th column
        totalAllocatedAmount += existingAmount;
      });

      // Calculate the expected total after adding new amount
      let expectedTotal = totalAllocatedAmount + customer_amount;

      if (expectedTotal > loan_amount_calc) {
        swalError('Warning', 'Total Customer Amount exceeds the allowed limit.');
        return; // Stop the process
      }
      // Check if the customer already exists in the table
      let customerExists = false;
      $('#cus_mapping_table tbody tr').each(function () {
        if ($(this).find('td:nth-child(3)').text() == add_customer) { // Compare with data-id
          customerExists = true;
        }
      });
      if (customerExists) {
        swalError('Warning', 'Customer is already mapped to this loan.');
        $('#add_customer').val('');
        $('#designation').val('');
        $('#customer_mapping').val('');
        $('#customer_amount').val('');
        return; // Stop the process if customer is already mapped
      }
      // If customer doesn't exist and count is within the limit, submit the data
      $.post('api/loan_entry_files/submit_cus_mappings.php', {
        add_customer: add_customer,
        centre_id: centre_id,
        loan_id_calc: loan_id_calc,
        customer_mapping: customer_mapping,
        total_cus: total_cus,
        designation: designation
      }, function (response) {
        let result = response.result;
        if (result === 1) {
          // Success: Fetch the new customer details from the response and append to the table
          let customer = response.customer_data; // Assuming the response contains customer data in this format
          let customerMappingText = $("#customer_mapping option:selected").text(); // Get the selected text for Customer Mapping
          // Append the data to the table
          let newRow = `
                    <tr data-id="${customer.id}"> <!-- Add customer.id as data-id -->
                        <td>${$('#cus_mapping_table tbody tr').length + 1}</td> <!-- Auto-incremented serial number -->
                        <td>${customer.cus_id}</td>
                        <td style="display:none">${customer.id}</td>
                        <td>${customer.first_name}</td>
                        <td class="cus_mapping">${customerMappingText}</td>
                        <td>${customer.aadhar_number}</td>
                        <td>${customer.mobile1}</td>
                        <td>${customer.areaname}</td>
                        <td class="customer_amount">${customer_amount}</td>
                        <td class="designation">${designation}</td> <!-- Direct from form -->
                        <td>
                            <span class="icon-trash-2 cusMapDeleteBtn" profile_id="${customer.id}"></span>
                        </td>
                    </tr>
                `;
          $('#cus_mapping_table tbody').append(newRow);
          // Clear the form fields after successful submission
          $('#add_customer').val('');
          $('#designation').val('');
          $('#customer_mapping').val('');
          $('#customer_amount').val('');
          $('#customer_mapping, #add_customer').css('border', '1px solid #CECECE');
        } else if (result === 2) {
          swalError('Error', 'An error occurred while processing the request.');
        } else if (result === 3 || result === 4) {
          swalError('Warning', response.message);
        }
      }, 'json');
    }
  });

    // Event listener for delete button click
    $(document).on('click', '.cusMapDeleteBtn', function () {
        let btn = $(this); 
        let id = btn.attr('value');
        let loan_id = btn.attr('loan_id');

        let TableRowVal = {
            id: id,
            loan_id: loan_id,
            row: btn.closest('tr') 
        };

        swalConfirm('Delete', 'Do you want to remove this customer mapping?', removeCusMap, TableRowVal, '');
    });

  /////////////////////////////////////////////////////////Submit Customer Mapping End//////////////////////////////////////////////////
  $("#profit_type_calc").change(function () {
    let profitType = $(this).val();

    // Check if the loan category is selected
    let id = $("#loan_category_calc").val();
    if (id == "") {
      swalError("Alert", "Kindly select Loan Category");
      $(this).val("");
      return;
    }
    clearCalcSchemeFields(profitType);
    $("#profit_type_calc_scheme").show();
    $(".calc_scheme_title").text(profitType == "1" ? "Calculation" : "Scheme");

    if (profitType == "1") {
      // Loan Calculation
      $(".calc").show();
      $(".scheme").hide();
      $(".scheme_day").hide();
      $(".scheme_date").hide();
      getLoanCatDetails(id, profitType); // Fetch loan category details
      $("#int_rate_upd").val("");
      $("#due_period_upd").val("");
      $("#doc_charge_upd").val("");
      $("#proc_fees_upd").val("");
      $('#interest_rate_calc, #due_period_calc').removeAttr('readonly');
    } else if (profitType == "2") {
      // Scheme
      $('#interest_rate_calc, #due_period_calc').attr('readonly', true);
      dueMethodScheme(id, profitType); // Fetch due method scheme details
      $(".calc").show();
      $(".scheme").show();
      $(".scheme_day").hide();
      $(".scheme_date").hide();
      // Reset due method and profit method dropdowns
      $("#due_method_calc").val($("#due_method_calc option:first").val());
      $("#profit_method_calc").val($("#profit_method_calc option:first").val());
      $("#int_rate_upd").val("");
      $("#due_period_upd").val("");
      $("#doc_charge_upd").val("");
      $("#proc_fees_upd").val("");
    } else {
      $("#profit_type_calc_scheme").hide(); // Hide the scheme if profitType is invalid
    }

    // Reset calculation fields
    $("#due_startdate_calc").val("");
    $("#maturity_date_calc").val("");
    $(".int-diff").text("*");
    $(".due-diff").text("*");
    $(".doc-diff").text("*");
    $(".proc-diff").text("*");
    $(".refresh_loan_calc").val("");
  });

  //  Modify due_method_calc change handler
  $('#due_method_calc').change(function () {
        let schemeDueMethod = $(this).val();
        let profitType = $('#profit_type_calc').val(); // Get the current profit type value

        // Handle showing/hiding .scheme_day and .scheme_date based on profitType and schemeDueMethod
        if (profitType == '2' && schemeDueMethod == '2') {
            // Profit Type 2 and Scheme Due Method 2 (weekly)
            $('.scheme_day').show();
            $('.scheme_date').hide();
            $('.scheme_date_calc').val('');
        } else if (profitType == '1' && schemeDueMethod == '2') {
            // Profit Type 1 and Scheme Due Method 2 (weekly)
            $('.scheme_day').show();
            $('.scheme_date').hide();
            $('.scheme_date_calc').val('');
        } else if (profitType == '2' && schemeDueMethod == '1') {
            // Profit Type 2 and Scheme Due Method 1 (monthly)
            $('.scheme_day').hide();
            $('.scheme_date').show();
            getDateDropDown(); // Call getDateDropDown function
        } else if (profitType == '1' && schemeDueMethod == '1') {
            // Profit Type 1 and Scheme Due Method 1 (monthly)
            $('.scheme_day').hide();
            $('.scheme_date').show();
            getDateDropDown(); // Call getDateDropDown function
        } else {
            // Default case if none of the above conditions are met
            $('.scheme_day').hide();
            $('.scheme_date').hide();
            $('.scheme_date_calc').val('');
        }

        // Clear the start date and maturity date fields
        $('#due_startdate_calc').val('');
        $('#maturity_date_calc').val('');
    });

  $("#scheme_name_calc").change(function () {
    //Scheme Name change event
    let scheme_id = $(this).val();
    schemeCalAjax(scheme_id);
    CalculationMethod(scheme_id);
    $("#due_startdate_calc").val("");
    $("#maturity_date_calc").val("");
    $("#int_rate_upd").val("");
    $("#due_period_upd").val("");
    $("#doc_charge_upd").val("");
    $("#proc_fees_upd").val("");
  });
  $("#profit_type_calc").on("change", function () {
    resetValidation();
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

    let totalCus = parseInt($('#total_cus').val()) || 0;
    let cusMappingtable = $('#cus_mapping_table tbody tr').length;

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

    $('#cus_mapping_table tbody tr').each(function () {
      let loan_amt = parseFloat($(this).find('td:nth-child(9)').text()) || 0;
      if (loan_amt && int_rate && due_period) {
        let benefit_method = $('#profit_method_calc').val();
        let result;
        if (benefit_method == 1 || benefit_method === 'Pre Benefit') {
          result = getLoanPreInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee);
        } else if (benefit_method == 2 || benefit_method === 'After Benefit') {
          result = getLoanAfterInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee);
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

  {
    // Get today's date
    var today = new Date().toISOString().split("T")[0];
    //Due start date -- set min date = current date.
    $("#due_startdate_calc").attr("min", today);
  }

  $("#scheme_day_calc").change(function () {
    $("#due_start_from").val("");
    $("#maturity_month").val("");
  });
  $("#due_period_calc").on("input", function () {
    $("#due_startdate_calc").val("");
    $("#maturity_date_calc").val("");
  });

  $("#due_startdate_calc").change(function () {
    var due_start_from = $("#due_startdate_calc").val(); // get start date to calculate maturity date
    var due_period = parseInt($("#due_period_calc").val()); //get due period to calculate maturity date
    var profit_type = $("#profit_type_calc").val();
    if (profit_type == "1") {
      //Based on the profit method choose due method from input box
      var due_method = $("#due_method_calc").val();
    } else if (profit_type == "2") {
      var due_method = $("#due_method_calc").val();
    }

    if (due_period == "" || isNaN(due_period)) {
      swalError("Warning", "Kindly Fill the Due Period field.");
      $(this).val("");
    } else {
      if (due_method == "Monthly" || due_method == "1") {
        // if due method is monthly or 1(for scheme) then calculate maturity by month

        var maturityDate = moment(due_start_from, "YYYY-MM-DD")
          .add(due_period, "months")
          .subtract(1, "month")
          .format("YYYY-MM-DD"); //subract one month because by default its showing extra one month
        $("#maturity_date_calc").val(maturityDate);
      } else if (due_method == "Weekly" || due_method == "2") {
        //if Due method is weekly then calculate maturity by week

        var due_day = parseInt($("#scheme_day_calc").val());

        var momentStartDate = moment(due_start_from, "YYYY-MM-DD")
          .startOf("day")
          .isoWeekday(due_day); //Create a moment.js object from the start date and set the day of the week to the due day value

        var weeksToAdd = Math.floor(due_period - 1); //Set the weeks to be added by giving due period. subract 1 because by default it taking extra 1 week

        momentStartDate.add(weeksToAdd, "weeks"); //Add the calculated number of weeks to the start date.

        if (momentStartDate.isBefore(due_start_from)) {
          momentStartDate.add(1, "week"); //If the resulting maturity date is before the start date, add another week.
        }

        var maturityDate = momentStartDate.format("YYYY-MM-DD"); //Get the final maturity date as a formatted string.

        $("#maturity_date_calc").val(maturityDate);
      }
    }
  });

  $('#submit_loan_calculation').click(function (event) {
    event.preventDefault();
    $('#refresh_cal').trigger('click'); //For calculate once again if user missed to refresh calculation
    let int_rate = parseFloat($('#interest_rate_calc').val()) || 0;
    let due_period = parseInt($('#due_period_calc').val()) || 0;
    let doc_charge = parseFloat($('#doc_charge_calc').val()) || 0;
    let proc_fee = parseFloat($('#processing_fees_calc').val()) || 0;

    var totalCustomerAmount = 0;
    var loanAmount = $('#loan_amount_calc').val();
    var loanAmountCalc = parseFloat(loanAmount);

    $('#cus_mapping_table tbody tr').each(function () {
      var cus_mapping = parseFloat($(this).find('td:nth-child(9)').text());
      totalCustomerAmount += cus_mapping;
    });

    if (totalCustomerAmount != loanAmountCalc) {
      swalError('Warning', 'Total Customer Amount does not match Loan Amount Calculation!');
      return;
    }
    var customerMappingData = [];
    $('#cus_mapping_table tbody tr').each(function () {
      // var cus_id = $(this).text(); // Retrieve the customer.id from data-id attribute
      var cus_id = $(this).find('td:nth-child(3)').text();
      var cus_mapping = $(this).find('td:nth-child(5)').text();
      var designation = $(this).find('td:nth-child(10)').text();
      var customer_loan_amount = $(this).find('td:nth-child(9)').text();
      if (customer_loan_amount) {
        let benefit_method = $('#profit_method_calc').val();
        let result;

        if (benefit_method == 1 || benefit_method == 'Pre Benefit') {
          result = getLoanPreInterest(customer_loan_amount, int_rate, due_period, doc_charge, proc_fee);
        } else if (benefit_method == 2 || benefit_method == 'After Benefit') {
          result = getLoanAfterInterest(customer_loan_amount, int_rate, due_period, doc_charge, proc_fee);
        }

        // Accumulate
        customer_loan_amount = result.loan;
        principle = result.principal;
        intrest_amount = result.interest;
        due_amount = result.due;
        net_cash = result.net;
      }

      customerMappingData.push({ cus_id: cus_id, cus_mapping: cus_mapping, designation: designation, intrest_amount: intrest_amount, principle: principle, due_amount: due_amount, customer_loan_amount: customer_loan_amount, net_cash: net_cash });
    });


    let formData = {
      'loan_id_calc': $('#loan_id_calc').val(),
      'Centre_id': $('#Centre_id').val(),
      'loan_category_calc': $('#loan_category_calc').val(),
      'loan_amount_calc': $('#loan_amount_calc').val().replace(/,/g, ''),
      'total_cus': $('#total_cus').val(),
      'profit_type_calc': $('#profit_type_calc').val(),
      'due_method_calc': $('#due_method_calc').val(),
      'profit_method_calc': $('#profit_method_calc').val(),
      'interest_rate_calc': $('#interest_rate_calc').val(),
      'due_period_calc': $('#due_period_calc').val(),
      'doc_charge_calc': $('#doc_charge_calc').val(),
      'processing_fees_calc': $('#processing_fees_calc').val(),
      'scheme_name_calc': $('#scheme_name_calc').val(),
      'scheme_date_calc': $('#scheme_date_calc').val(),
      'loan_amnt_calc': $('#loan_amnt_calc').val().replace(/,/g, ''),
      'principal_amnt_calc': $('#principal_amnt_calc').val().replace(/,/g, ''),
      'interest_amnt_calc': $('#interest_amnt_calc').val().replace(/,/g, ''),
      'total_amnt_calc': $('#total_amnt_calc').val().replace(/,/g, ''),
      'due_amnt_calc': $('#due_amnt_calc').val().replace(/,/g, ''),
      'doc_charge_calculate': $('#doc_charge_calculate').val().replace(/,/g, ''),
      'processing_fees_calculate': $('#processing_fees_calculate').val().replace(/,/g, ''),
      'net_cash_calc': $('#net_cash_calc').val().replace(/,/g, ''),
      'scheme_day_calc': $('#scheme_day_calc').val(),
      'due_startdate_calc': $('#due_startdate_calc').val(),
      'maturity_date_calc': $('#maturity_date_calc').val(),
      'customer_mapping_data': customerMappingData,// Add customer mapping data
      'id': $('#loan_calculation_id').val()

    }
    if (isFormDataValid(formData)) {
      $('#submit_loan_calculation').prop('disabled', true);
      $.post("api/approval/submit_loan_edit_calculation.php", formData, function (response) {
        $('#submit_loan_calculation').prop('disabled', false);
        if (response.result == "1") {
          swalSuccess("Success", "Loan Entry Updated Successfully!");
          $("#loan_calculation_id").val(response.last_id);
          $("#loan_calculation_id").val("");
          // $("#loan_entry_loan_calculation").trigger("reset");
          $('html, body').animate({
            scrollTop: $('.page-content').offset().top
          }, 3000);

        } else if (response.result == "3") {
          swalError("Warning", "Remove the Customer Mapping Details");
        } else if (response.result == "4") {
          swalError("Warning", "Add the Customer Mapping Details");
        }
        else {
          swalError("Warning", "Loan Entry Not Submitted");
        }
      },
        "json"
      );
    }
  });

  $(document).on("click", ".approval-edit", async function () {
    $('#submit_loan_calculation').prop('disabled', false);
    $("#edit_content").show();

    let loanCalcId = $(this).attr("value");
    let centre_id = $(this).attr("centre_id");

    $("#loan_calculation_id").val(loanCalcId);
    $("#centre_id_hidden").val(centre_id);

    try {
      await loanCalculationEdit(loanCalcId); // Wait for loan edit to finish
      swapTableAndCreation();                // Then show table/content
    } catch (error) {
      console.error("Error during loan calculation edit:", error);
      swalError('Error', 'Failed to load loan calculation data.');
    }
  });


  $(document).on("click", ".approval-approve", function () {
    let loan_id = $(this).attr("value");
    let loan_amount = $(this).attr("loan_amount");
    let centre_limit = $(this).attr("centre_limit");
    let total_cus = $("#total_cus").val();

    // Check if cus_limit is empty
    if (centre_limit === '0' || !centre_limit) {
      // Prevent approval and show an alert or message
      swalError('Warning', 'Kindly Enter the centre Limit');
      return; // Stop further execution
    } else if (parseInt(loan_amount) > parseInt(centre_limit)) {
      swalError('Warning', 'Centre limit is less than the loan amount. Please update either the centre limit or the loan amount.');
      return;
    }

    validationForApproval(loan_id);

  });
  $(document).on('click', '.approval-cancel', function () {
    let loan_id = $(this).attr('value');
    let loan_sts = 5;
    $('#add_info_modal').modal('show');
    $('#exampleModalLongTitle').text('Cancel');
    $('#customer_profile_id').val(loan_id);
    $('#customer_status').val(loan_sts);
    $('#remark').val('');
  });
  $(document).on('click', '#submit_remark', function () {
    event.preventDefault();
    let action = $('#exampleModalLongTitle').text().toLowerCase(); // Get action (cancel or revoke)
    let loan_id = $('#customer_profile_id').val();
    let loan_sts = $('#customer_status').val();
    let remark = $('#remark').val();

    if (remark === '') {
      alert('Please enter a remark.');
      return;
    }

    submitForm(action, loan_id, loan_sts, remark);
  });


  $(document).on('click', '.approval-revoke', function () {
    let loan_id = $(this).attr('value');
    let loan_sts = 6;
    $('#add_info_modal').modal('show');
    $('#exampleModalLongTitle').text('Revoke');
    $('#customer_profile_id').val(loan_id);
    $('#customer_status').val(loan_sts);
    $('#remark').val('');
  });

  $(document).on("click", "#submit_centre_limit", function (event) {
    event.preventDefault();
    let centre_id = $("#centre_id_hidden").val();
    submitCentre_limit(centre_id);

  });
}); //Document END.

$(function () {
  getLoanEntryTable();
});

function getCentreDetails(id) {
  $.post(
    "api/loan_entry_files/get_centre_details.php",
    { id },
    function (response) {
      $("#centre_no").val(response[0].centre_no);
      $("#centre_name").val(response[0].centre_name);
      $("#centre_mobile").val(response[0].mobile1);
    },
    "json"
  );
}

function swapTableAndCreation() {
  if ($(".loan_table_content").is(":visible")) {
    $(".loan_table_content").hide();
    $("#add_loan").hide();
    $("#loan_entry_content").show();
    $("#back_btn").show();
    callLoanCaculationFunctions();
  } else {
    $(".loan_table_content").show();
    $("#add_loan").show();
    getLoanEntryTable();
    $("#loan_entry_content").hide();
    $("#back_btn").hide();
    $("#loan_details").trigger("click");
  }
}

function getLoanEntryTable() {
  serverSideTable("#loan_entry_table", "", "api/approval/approval_list.php");
}

function getCentreMapTable(id) {
  $.post('api/loan_entry_files/get_centre_map_details.php', { id }, function (response) {
    let cusMapColumn = [
      "sno",
      "cus_id",
      "map_id",
      "first_name",
      "customer_mapping",
      "aadhar_number",
      "mobile1",
      "areaname",
      'loan_amount',
      "designation",
      "action"
    ]
    appendDataToTable('#cus_mapping_table', response, cusMapColumn);
    $('#cus_mapping_table').find('th:eq(2), td:nth-child(3)').hide();
  }, 'json');
}

function validationForApproval(loan_id) {
  $.post('api/approval/validation_for_approval.php', { loan_id }, function (response) {
    if (response === "0") {
      let loan_sts = 4;
      moveToNext(loan_id, loan_sts);

    }
    else if (response === "1") {
      swalError("Warning", "Remove the Customer Mapping Details");
    }
    else if (response === "2") {
      swalError("Warning", "Add the Customer Mapping Details");
    }
    else {
      swalError("Warning", "Error");

    }


  }, 'json');

}

function submitCentre_limit(centre_id) {
  let lable = $("#lable").val();
  let feedback = $("#feedback").val();

  let remarks = $("#remarks").val();
  let centre_limit = $('#approval_centre_limit').val().replace(/,/g, '')
  
  if (centre_limit === '' || centre_limit === '0') {
    swalError("Warning", "Please fill Centre Limit.");
    $("#approval_centre_limit").css("border", "1px solid #ff2020");

  }
  else {
    $.post(
      "api/approval/submit_centre_limit.php",
      { centre_id, lable, feedback, remarks, centre_limit },
      function (response) {
        if (response === "0") {
          swalSuccess("Success", "Centre Limit  Updated Successfully!");
          $("#centre_limit").trigger("click");
          $('html, body').animate({
            scrollTop: $('.page-content').offset().top
          }, 3000);
        } else if (response === "1") {
          swalError("Warning", "Centre Limit Not Updated!");
        }
      },
      "json"
    );

  }

}

function moveToNext(loan_id, loan_sts) {
  $.post('api/common_files/move_to_next.php', { loan_id, loan_sts }, function (response) {
    if (response == '0') {
      let alertName;
      if (loan_sts == '4') {
        alertName = 'Approved Successfully';
      }
      else if (loan_sts == '5') {
        alertName = 'Cancelled Successfully';
      }
      else if (loan_sts == '6') {
        alertName = 'Revoked Successfully';
      }
      swalSuccess('Success', alertName);
      getLoanEntryTable()
    } else {
      swalError('Alert', 'Failed To Move');
    }
  }, 'json');
}

function closeRemarkModal() {
  $('#add_info_modal').modal('hide');
}

function submitForm(action, loan_id, loan_sts, remark) {
  $.post('include/common/update_status.php', { loan_id, remark, loan_sts }, function (response) {
    if (response == '0') {
      $('#add_info_modal').modal('hide');
      moveToNext(loan_id, loan_sts);
    } else {
      swalError('Alert', 'Failed to ' + action);
    }
  }, 'json');
}

async function callLoanCaculationFunctions() {
  try {
    getLoanCategoryName(); // This seems to be sync — leave as-is

    let loan_calc_id = $('#loan_calculation_id').val();
    await getAutoGenLoanId(loan_calc_id); // wait for loan ID to be set

    await getCusMapTable(); // wait for customer map table to be filled
    await getCentreId(); // wait for centre options to load

  } catch (error) {
    console.error("Error in callLoanCaculationFunctions:", error);
    swalError("Error", "Something went wrong while loading calculation data.");
  }
}

function getAutoGenLoanId(id) {
  $.post(
    "api/loan_entry_files/get_autoGen_loan_id.php",
    { id },
    function (response) {
      $("#loan_id_calc").val(response);
    },
    "json"
  );
}
$("#customer_mapping").change(function () {
  let customer_mapping = $(this).val();
  getCustomerList(customer_mapping);
});

function getLoanCategoryName() {
  $.post(
    "api/common_files/get_loan_category_creation.php",
    function (response) {
      let appendLoanCatOption = "";
      appendLoanCatOption += '<option value=" ">Select Loan Category</option>';
      $.each(response, function (index, val) {
        let selected = "";
        let loan_category_calc2 = $("#loan_category_calc2").val();
        if (val.id == loan_category_calc2) {
          selected = "selected";
        }
        appendLoanCatOption +=
          '<option value="' +
          val.id +
          '" limit="' +
          val.loan_limit +
          '" ' +
          selected +
          ">" +
          val.loan_category +
          "</option>";
      });
      $("#loan_category_calc").empty().append(appendLoanCatOption);
    },
    "json"
  );
}

function getLoanCatDetails(id, profitType) {
  // Proceed with the AJAX request only if the ID is not empty
  $.post(
    "api/loan_entry_files/getLoanCatDetails.php",
    { id, profitType },
    function (response) {
      if (response && response.length > 0) {
        $('#due_method_calc').val(response[0].due_method).trigger('change');
        $("#profit_method_calc").val(response[0].benefit_method);
        $("#due_method_calc").prop("disabled", true);
        $("#profit_method_calc").prop("disabled", true);
        $('#proc_fees_type').val(response[0].procrssing_fees_type);

        var int_rate_upd = $("#int_rate_upd").val()
          ? $("#int_rate_upd").val()
          : "";
        var due_period_upd = $("#due_period_upd").val()
          ? $("#due_period_upd").val()
          : "";
        var doc_charge_upd = $("#doc_charge_upd").val()
          ? $("#doc_charge_upd").val()
          : "";
        var proc_fee_upd = $("#proc_fees_upd").val()
          ? $("#proc_fees_upd").val()
          : "";

        // Set min and maximum ranges and validation for the respective fields
        $(".min-max-int").text(
          "* (" +
          response[0].interest_rate_min +
          "% - " +
          response[0].interest_rate_max +
          "%) "
        );
        $("#interest_rate_calc").attr(
          "onChange",
          `if( parseFloat($(this).val()) > '` +
          response[0].interest_rate_max +
          `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '` +
          response[0].interest_rate_min +
          `' && parseFloat($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `
        );
        $("#interest_rate_calc").val(int_rate_upd);

        $(".min-max-due").text(
          "* (" +
          response[0].due_period_min +
          " - " +
          response[0].due_period_max +
          ") "
        );
        $("#due_period_calc").attr(
          "onChange",
          `if( parseInt($(this).val()) > '` +
          response[0].due_period_max +
          `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseInt($(this).val()) < '` +
          response[0].due_period_min +
          `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `
        );
        $("#due_period_calc").val(due_period_upd);

        $(".min-max-doc").text(
          "* (" +
          response[0].doc_charge_min +
          "% - " +
          response[0].doc_charge_max +
          "%) "
        );
        $("#doc_charge_calc").attr(
          "onChange",
          `if( parseFloat($(this).val()) > '` +
          response[0].doc_charge_max +
          `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '` +
          response[0].doc_charge_min +
          `' && parseFloat($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `
        );
        $("#doc_charge_calc").val(doc_charge_upd);

        if (response[0].procrssing_fees_type === 'rupee') {
          $('.min-max-proc').text('* (' + response[0].processing_fee_min + '₹ - ' + response[0].processing_fee_max + '₹) ');
          $('#processing_fees_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + response[0].processing_fee_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '` + response[0].processing_fee_min + `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `);
          $('#processing_fees_calc').val(proc_fee_upd);
        }
        else {
          $('.min-max-proc').text('* (' + response[0].processing_fee_min + '% - ' + response[0].processing_fee_max + '%) ');
          $('#processing_fees_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + response[0].processing_fee_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '` + response[0].processing_fee_min + `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `);
          $('#processing_fees_calc').val(proc_fee_upd);
        }


        if (profitType == 2) {
          $("#interest_rate_calc").val("");
          $("#due_period_calc").val("");
          $("#doc_charge_calc").val("");
          $("#processing_fees_calc").val("");
        }
      } else {
        swalError("Warning", "No Calculation is found");
        // Hide the scheme element if the response is empty or null
        $("#profit_type_calc_scheme").hide();
      }
    },
    "json"
  );
}

function clearCalcSchemeFields(type) {
  $(".to_clear").val("");
  $(".min-max-int").text("*");
  $(".min-max-due").text("*");
  $(".min-max-doc").text("*");
  $(".min-max-proc").text("*");
}

function dueMethodScheme(id, profitType) {
  $.post(
    "api/common_files/get_due_method_scheme.php",
    { id, profitType },
    function (response) {
      if (response && response.length > 0) {
        clearCalcSchemeFields("1"); // Clear fields

        // Initialize options with default
        let appendSchemeNameOption =
          '<option value="">Select Scheme Name</option>';

        // Iterate through the response and append options
        $.each(response, function (index, val) {
          let selected = "";
          let scheme_edit_it = $("#scheme_name_edit").val();

          // If the scheme matches the edit value, mark it as selected
          if (val.id == scheme_edit_it) {
            selected = "selected";
          }

          // Build the option elements
          appendSchemeNameOption +=
            '<option value="' +
            val.id +
            '" ' +
            selected +
            ">" +
            val.scheme_name +
            "</option>";
        });

        // Set the options once after the loop
        $("#scheme_name_calc").empty().append(appendSchemeNameOption);
      } else {
        swalError("Warning", "No scheme is found");
        $("#profit_type_calc_scheme").hide(); // Hide scheme if no response
      }
    },
    "json"
  );

  // let profitType = $('#profit_type_calc').val(); // Get the current profit type value
  let schemeDueMethod = $("#due_method_calc").val();
  if (profitType == "2" && schemeDueMethod == "2") {
    $(".scheme_day").show();
  } else {
    $(".scheme_day").hide();
    $(".scheme_day_calc").val("");
  }
  if (profitType == "2" && schemeDueMethod == "1") {
    $(".scheme_date").show();
  } else {
    $(".scheme_date").hide();
    $(".scheme_date_calc").val("");
  }
}

function CalculationMethod(scheme_id) {
  $.post(
    "api/loan_entry_files/get_profit_method.php",
    { scheme_id },
    function (response) {
      // Set the values of the form fields based on the response
      $("#due_method_calc").val(response[0].due_method).trigger("change");
      $("#profit_method_calc").val(response[0].benefit_method);
      $("#profit_type_calc_scheme").show(); // Ensure the scheme is shown if data is valid
      $("#due_method_calc").prop("disabled", true);
      $("#profit_method_calc").prop("disabled", true);
    },
    "json"
  );
}

function getDateDropDown(editDId) {
  let dateOption = '<option value="">Select Date</option>';
  for (let i = 1; i <= 31; i++) {
    let selected = "";
    if (i == editDId) {
      selected = "selected";
    }
    dateOption +=
      '<option value="' + i + '" ' + selected + ">" + i + "</option>";
  }
  $("#scheme_date_calc").empty().append(dateOption);
}

function schemeCalAjax(id) {
  if (id != "") {
    var int_rate_upd = $("#int_rate_upd").val() ? $("#int_rate_upd").val() : "";
    var due_period_upd = $("#due_period_upd").val()
      ? $("#due_period_upd").val()
      : "";
    var doc_charge_upd = $("#doc_charge_upd").val()
      ? $("#doc_charge_upd").val()
      : "";
    var proc_fee_upd = $("#proc_fees_upd").val()
      ? $("#proc_fees_upd").val()
      : "";
    $.post(
      "api/loan_category_creation/get_scheme_data.php",
      { id },
      function (response) {

        $('#interest_rate_calc').val(response[0].interest_rate_percent_min)
        $('#due_period_calc').val(response[0].due_period_percent_min);

        $(".min-max-doc").text(
          "* (" +
          response[0].doc_charge_min +
          "% - " +
          response[0].doc_charge_max +
          "%) "
        );
        $("#doc_charge_calc").attr(
          "onChange",
          `if( parseFloat($(this).val()) > '` +
          response[0].doc_charge_max +
          `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '` +
          response[0].doc_charge_min +
          `' && parseFloat($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `
        ); //To check value between range
        $("#doc_charge_calc").val(doc_charge_upd);

        $(".min-max-proc").text(
          "* (" +
          response[0].processing_fee_min +
          "% - " +
          response[0].processing_fee_max +
          "%) "
        );
        $("#processing_fees_calc").attr(
          "onChange",
          `if( parseFloat($(this).val()) > '` +
          response[0].processing_fee_max +
          `' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '` +
          response[0].processing_fee_min +
          `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `
        ); //To check value between range
        $("#processing_fees_calc").val(proc_fee_upd);
      },
      "json"
    );
  } else {
    clearCalcSchemeFields("2");
  }
}


//To Get Loan Calculation for After Interest
function getLoanAfterInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee) {

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
  var doc_type = $('.min-max-doc').text();
  if (doc_type.includes('₹')) {
    var doc_charge = parseInt(doc_charge);
  } else if (doc_type.includes('%')) {
    var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100);
  }
  var roundeddoccharge = Math.ceil(doc_charge / 5) * 5;
  if (roundeddoccharge < doc_charge) {
    roundeddoccharge += 5;
  }
  let docDiff = parseInt(roundeddoccharge) - parseInt(doc_charge);

  // Calculate Processing fee
  var proc_type = $('.min-max-proc').text();
  if (proc_type.includes('₹')) {
    var proc_fee = parseInt(proc_fee);
  } else if (proc_type.includes('%')) {
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

function getLoanPreInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee) {

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
  var doc_type = $('.min-max-doc').text();
  if (doc_type.includes('₹')) {
    var doc_charge = parseInt(doc_charge);
  } else if (doc_type.includes('%')) {
    var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100);
  }
  var roundeddoccharge = Math.ceil(doc_charge / 5) * 5;
  if (roundeddoccharge < doc_charge) {
    roundeddoccharge += 5;
  }
  let docDiff = roundeddoccharge - doc_charge;

  // Calculate Processing fee
  var proc_type = $('.min-max-proc').text();
  if (proc_type.includes('₹')) {
    var proc_fee = parseInt(proc_fee);
  } else if (proc_type.includes('%')) {
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

function resetValidation() {
  const fieldsToReset = [
    "due_method_calc",
    "due_type_calc",
    "profit_method_calc",
    "interest_rate_calc",
    "due_period_calc",
    "doc_charge_calc",
    "processing_fees_calc",
    "scheme_due_method_calc",
    "scheme_name_calc",
    "scheme_day_calc",
    "scheme_date_calc",
  ];

  fieldsToReset.forEach((fieldId) => {
    $("#" + fieldId).css("border", "1px solid #cecece");
  });
}

function isFormDataValid(formData) {
  let isValid = true;
  const excludedFields = [
    'loan_amnt_calc',
    'principal_amnt_calc',
    'interest_amnt_calc',
    'total_amnt_calc',
    'processing_fees_calculate',
    'net_cash_calc',
    'due_amnt_calc',
    'doc_charge_calculate',
    'due_method_calc',
    'profit_method_calc',
    'scheme_date_calc',
    'scheme_day_calc',
    'scheme_name_calc',
    'due_period_calc',
    'interest_rate_calc',
    'processing_fees_calc',
    'doc_charge_calc',
    'id'
  ];
  // Validate all fields except the excluded ones
  for (let key in formData) {
    if (!excludedFields.includes(key)) {
      if (!validateField(formData[key], key)) {
        isValid = false;
      }
    }
  }
  // Additional validation based on specific conditions
  if (formData['profit_type_calc'] == '1') { // Calculation
    var validationResults = [
      validateField(formData['due_method_calc'], 'due_method_calc'),
      validateField(formData['profit_method_calc'], 'profit_method_calc'),
      validateField(formData['interest_rate_calc'], 'interest_rate_calc'),
      validateField(formData['due_period_calc'], 'due_period_calc'),
      validateField(formData['doc_charge_calc'], 'doc_charge_calc'),
      validateField(formData['processing_fees_calc'], 'processing_fees_calc')
    ];

  } else if (formData['profit_type_calc'] == '2') {
    var validationResults = [
      validateField(formData['due_method_calc'], 'due_method_calc'),
      validateField(formData['profit_method_calc'], 'profit_method_calc'),
      validateField(formData['scheme_name_calc'], 'scheme_name_calc'),
      validateField(formData['interest_rate_calc'], 'interest_rate_calc'),
      validateField(formData['due_period_calc'], 'due_period_calc'),
      validateField(formData['doc_charge_calc'], 'doc_charge_calc'),
      validateField(formData['processing_fees_calc'], 'processing_fees_calc')
    ];

  }

  if (formData['due_method_calc'] == '2') {
    validationResults.push(validateField(formData['scheme_day_calc'], 'scheme_day_calc'));
  }
  if (formData['due_method_calc'] == '1') {
    validationResults.push(validateField(formData['scheme_date_calc'], 'scheme_date_calc'));
  }

  if (!validationResults.every(result => result)) {
    isValid = false;
  }
  return isValid;
}

function clearLoanCalcForm() {
  // Clear input fields except those with IDs 'loan_id_calc' and 'loan_date_calc'
  $("#loan_entry_loan_calculation")
    .find("input")
    .each(function () {
      var id = $(this).attr("id");
      if (
        id !== "loan_id_calc" &&
        id !== "loan_date_calc" &&
        id != "profit_method_calc" &&
        id != "refresh_cal" &&
        id != "submit_cus_map"
      ) {
        $(this).val("");
      }
    });
  $("#loan_entry_loan_calculation input").css("border", "1px solid #cecece");
  $("#loan_entry_loan_calculation select").css("border", "1px solid #cecece");
  $("#loan_entry_loan_calculation textarea").css("border", "1px solid #cecece");
  $(".min-max-int").text("*");
  $(".min-max-due").text("*");
  $(".min-max-doc").text("*");
  $(".min-max-proc").text("*");
  $(".int-diff").text("*");
  $(".due-diff").text("*");
  $(".doc-diff").text("*");
  $(".proc-diff").text("*");

  // Clear all textarea fields within the specific form
  $("#loan_entry_loan_calculation").find("textarea").val("");

  // Reset all select fields within the specific form
  $("#loan_entry_loan_calculation")
    .find("select")
    .each(function () {
      $(this).val($(this).find("option:first").val());
    });
}
function getCustomerList(customer_mapping) {
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

function getCusMapTable() {
  return new Promise((resolve, reject) => {
    let loan_id_calc = $('#loan_id_calc').val();

    $.post('api/loan_entry_files/get_cus_map_details.php', { loan_id_calc }, function (response) {
      let cusMapColumn = [
        "sno",
        "cus_id",
        "map_id",
        "first_name",
        "customer_mapping",
        "aadhar_number",
        "mobile1",
        "areaname",
        "loan_amount",
        "designation",
        "action"
      ];
      appendDataToTable('#cus_mapping_table', response, cusMapColumn);
      $('#cus_mapping_table').find('th:eq(2), td:nth-child(3)').hide();
      resolve(); // resolve after complete
    }, 'json').fail(reject); // reject on error
  });
}

// Function to remove row
function removeCusMap(TableRowVal) {
    let { id, loan_id, row } = TableRowVal;

    row.remove(); // Remove the row directly
    swalSuccess('Success', 'Customer mapping removed successfully.');

    // Reindex remaining rows
    $('#cus_mapping_table tbody tr').each(function (index) {
        $(this).find('td:first').text(index + 1);
    });

    // API call
    $.post('api/loan_entry_files/delete_cus_mapping.php', { id, loan_id }, function (response) {
        if (response == 1) {
            // Success message already shown above
        } else {
            swalError('Warning', 'Customer mapping removed Faield.');
        }
    }, 'json');
}

function loanCalculationEdit(id) {
  return new Promise((resolve, reject) => {
    $.post('api/loan_entry_files/loan_calculation_data.php', { id }, function (response) {
      if (response.length > 0) {
        // Populate all fields
        $('#loan_id_calc').val(response[0].loan_id);
        $('#loan_category_calc').val(response[0].loan_category);
        $('#loan_category_calc2').val(response[0].loan_category);
        $('#Centre_id').val(response[0].centre_id);
        $('#Centre_id_edit').val(response[0].centre_id);
        $('#centre_no').val(response[0].centre_no);
        $('#centre_name').val(response[0].centre_name);
        $('#centre_mobile').val(response[0].mobile1);
        $('#total_cus').val(response[0].total_customer);
        $('#loan_amount_calc').val(response[0].loan_amount);
        $('#profit_type_calc').val(response[0].profit_type);
        $('#due_method_calc').val(response[0].due_method);
        $('#profit_method_calc').val(response[0].benefit_method);
        $('#scheme_name_edit').val(response[0].scheme_name);
        $('#int_rate_upd').val(response[0].interest_rate);
        $('#due_period_upd').val(response[0].due_period);
        $('#doc_charge_upd').val(response[0].doc_charge);
        $('#proc_fees_upd').val(response[0].processing_fees);
        $('#loan_amnt_calc').val(response[0].loan_amount_calc);
        $('#principal_amnt_calc').val(response[0].principal_amount_calc);
        $('#interest_amnt_calc').val(response[0].intrest_amount_calc);
        $('#total_amnt_calc').val(response[0].total_amount_calc);
        $('#due_amnt_calc').val(response[0].due_amount_calc);
        $('#doc_charge_calculate').val(response[0].document_charge_cal);
        $('#processing_fees_calculate').val(response[0].processing_fees_cal);
        $('#net_cash_calc').val(response[0].net_cash_calc);
        $('#profit_type_calc_scheme').show();

        let editDId = response[0].scheme_date;

        if (response[0].profit_type == '1') {
          $('.calc').show();
          $('.scheme').hide();
          $('.scheme_day').hide();
          getLoanCatDetails(response[0].loan_category, 1);
        } else if (response[0].profit_type == '2') {
          dueMethodScheme(response[0].loan_category, 2);
          $('.calc').show();
          $('.scheme').show();
          setTimeout(() => {
            $('#scheme_name_calc').trigger('change');
          }, 500);
        }

        setTimeout(() => {
          $('#scheme_day_calc').val(response[0].scheme_day_calc);
          getDateDropDown(editDId);
          $('#scheme_date_calc').trigger('change');
          $('#due_startdate_calc').val(response[0].due_start);
          $('#maturity_date_calc').val(response[0].due_end);
          $('#total_cus').trigger('blur');
          getCusMapTable().then(() => {
            let totalCus = parseInt($('#total_cus').val()) || 0;
            let cusMappingtable = $('#cus_mapping_table tbody tr').length;
            if (totalCus > 0 && cusMappingtable > 0 && totalCus === cusMappingtable) {
              $('#refresh_cal').trigger('click');
            }
            getRepresentInfoTable(response[0].centre_id);
            resolve(); // Resolve after data and table setup
          });
        }, 2000);
      } else {
        reject("No data returned");
      }
    }, 'json').fail(reject);
  });
}

function getCentrelimit(centre_id) {
  $.post(
    "api/approval/get_centre_limit.php",
    { centre_id },
    function (response) {
      let centreLimit = moneyFormatIndia(response[0].centre_limit)
      $('#approval_centre_limit').val(centreLimit === 0 ? "" : centreLimit);

      $("#lable").val(response[0].lable);
      $("#feedback").val(response[0].feedback);
      $("#remarks").val(response[0].remarks);
    },
    "json"
  );
}

async function getCentreId() {
  return new Promise((resolve, reject) => {
    $.post("api/loan_entry_files/get_centre_id.php", function (response) {
      try {
        let appendLoanCatOption = '<option value=" ">Select Centre ID</option>';
        let Centre_id_edit = $("#Centre_id_edit").val();
        let centreEditOptionAdded = false;

        $.each(response, function (index, val) {
          let selected = "";

          if (val.centre_id == Centre_id_edit) {
            selected = "selected";
            centreEditOptionAdded = true;
          }

          if (val.centre_id == Centre_id_edit) {
            appendLoanCatOption += `<option value="${val.centre_id}" ${selected}>${val.centre_id}</option>`;
          } else if (val.status === "can_show" && !Centre_id_edit) {
            appendLoanCatOption += `<option value="${val.centre_id}">${val.centre_id}</option>`;
          } else if (val.status === "can_show" && Centre_id_edit) {
            appendLoanCatOption += `<option value="${val.centre_id}">${val.centre_id}</option>`;
          }
        });

        if (!centreEditOptionAdded && Centre_id_edit) {
          appendLoanCatOption += `<option value="${Centre_id_edit}" selected>${Centre_id_edit}</option>`;
        }

        $("#Centre_id").empty().append(appendLoanCatOption);
        resolve(); //  All done
      } catch (err) {
        reject(err);
      }
    }, "json").fail((xhr, status, error) => {
      reject(error);
    });
  });
}

function getRepresentInfoTable(centre_id) {
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