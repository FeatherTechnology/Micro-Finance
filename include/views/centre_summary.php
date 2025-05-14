<?php
//Format number in Indian Format
function moneyFormatIndia($num1)
{
    if ($num1 < 0) {
        $num = str_replace("-", "", $num1);
    } else {
        $num = $num1;
    }
    $explrestunits = "";
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3, strlen($num));
        $restunits = substr($num, 0, strlen($num) - 3);
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
        $expunit = str_split($restunits, 2);
        for ($i = 0; $i < sizeof($expunit); $i++) {
            if ($i == 0) {
                $explrestunits .= (int)$expunit[$i] . ",";
            } else {
                $explrestunits .= $expunit[$i] . ",";
            }
        }
        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $num;
    }

    if ($num1 < 0 && $num1 != '') {
        $thecash = "-" . $thecash;
    }

    return $thecash;
}
?>

<div class="radio-container" id="curr_closed">
    <div class="selector">
        <div class="selector-item">
            <input type="radio" id="centre_current" name="customer_data_type" class="selector-item_radio" value="cen_current" checked>
            <label for="centre_current" class="selector-item_label">Current</label>
        </div>
        <div class="selector-item">
            <input type="radio" id="centre_closed" name="customer_data_type" class="selector-item_radio" value="cen_closed">
            <label for="centre_closed" class="selector-item_label">Closed</label>
        </div>
    </div>
</div>
<br>
<div class="text-right">
    <button type="button" class="btn btn-primary" id="back_btn" style="display:none;"><span class="icon-arrow-left"></span>&nbsp; Back </button>
</div>
<div class="text-right">
    <button type="button" class="btn btn-primary" id="close_back_btn" style="display:none;"><span class="icon-arrow-left"></span>&nbsp; Back </button>
</div>
<br>
<!----------------Centre Table---------------------------->
<div class="col-12">
    <div class="card centre_loan_current">
        <div class="card-header">
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <table id="centre_current_table" class="table custom-table">
                        <thead>
                            <tr>
                                <th width="50">S.No.</th>
                                <th>Centre ID</th>
                                <th>Centre Number</th>
                                <th>Centre Name</th>
                                <th>Loan ID</th>
                                <th>Loan Amount</th>
                                <th>Branch</th>
                                <th>Centre Status</th>
                                <th>Collection Status</th>
                                <th>Action</th>
                                <th>Charts</th>
                            </tr>
                        </thead>
                        <tbody> </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--------------Centre table end-------------------------->
<div class="col-12">
    <div class="card centre_loan_closed" style="display:none">
        <div class="card-header">
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <table id="centre_close_table" class=" table custom-table">
                        <thead>
                            <th width="50">S.No.</th>
                            <th>Centre ID</th>
                            <th>Centre Number</th>
                            <th>Centre Name</th>
                            <th>Loan ID</th>
                            <th>Loan Amount</th>
                            <th>Branch</th>
                            <th>Centre Status</th>
                            <th>Centre Sub Status</th>
                            <th>Collection Status</th>
                            <th>Action</th>
                            <th>Charts</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!----------------------------- CARD Start- Ledger View Chart ------------------------------>
<div class="card ledger_view_chart_model" style="display: none;">
    <div class="card-header">
        <div class="card-title">Ledger View</div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12" id="ledger_view_table_div" style="overflow: auto;">

            </div>
        </div>
    </div>
</div>
<!-----------------------------CARD END - Ledger View Chart --------------------------------->
<div id="printcollection" style="display: none"></div>
<!-- /////////////////////////////////////////////////////////////////// Due Chart Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade bd-example-modal-lg" id="due_chart_model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document" style="max-width: 70% !important">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="dueChartTitle">Due Chart</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="closeChartsModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid" id="due_chart_table_div">
                    <table class="table custom-table">
                        <thead>
                            <th>Due No.</th>
                            <th>Month</th>
                            <th>Date</th>
                            <th>Due Amount</th>
                            <th>Pending</th>
                            <th>Payable</th>
                            <th>Collection Date</th>
                            <th>Collection Amount</th>
                            <th>Balance Amount</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Due Chart Modal END ////////////////////////////////////////////////////////////////////// -->

<!-- /////////////////////////////////////////////////////////////////// Loan Info START ////////////////////////////////////////////////////////////////////////// -->

<div class="loan_info_table_content" style="display:none">
    <form id="loan_info_creation" name="loan_info_creation">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Loan Info</div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="loan_id_calc"> Loan ID</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" id="loan_id_calc" name="loan_id_calc" tabindex="1" value="L" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="Centre_id"> Centre ID</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" id="Centre_id" name="Centre_id" tabindex="2" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="centre_no"> Centre Number</label>
                            <input class="form-control" id="centre_no" name="centre_no" tabindex="3" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="centre_name"> Centre Name</label>
                            <input class="form-control" id="centre_name" name="centre_name" tabindex="4" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="centre_mobile"> Mobile Number</label>
                            <input class="form-control" id="centre_mobile" name="centre_mobile" tabindex="5" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="loan_category_calc"> Loan Category</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" id="loan_category_calc" name="loan_category_calc" tabindex="6" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="loan_amount_calc"> Loan Amount</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" id="loan_amount_calc" name="loan_amount_calc" tabindex="7" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="total_cus"> Total No Of Customer</label><span class="text-danger">*</span>
                            <input type="number" class="form-control" id="total_cus" name="total_cus" tabindex="8" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="profit_type_calc">Profit Type</label><span class="text-danger">*</span>
                            <input type="hidden" id="profit_name_edit">
                            <select class="form-control" id="profit_type_calc" name="profit_type_calc" tabindex="10" disabled>
                                <option value="">Select Profit Type</option>
                                <option value="1">Calculation</option>
                                <option value="2">Scheme</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--- -------------------------------------- Loan Info END ------------------------------- -->
        <!--- -------------------------------------- Calculation - Scheme START ------------------------------- -->
        <div class="card" id="profit_type_calc_scheme">
            <div class="card-header">
                <div class="card-title calc_scheme_title">Calculation</div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 scheme" style="display:none">
                        <div class="form-group">
                            <label for="scheme_name_calc">Scheme Name</label><span class="text-danger">*</span>
                            <input type="text" class="form-control to_clear" id="scheme_name_calc" name="scheme_name_calc" tabindex="14" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="due_method_calc">Due Method</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" id="due_method_calc" name="due_method_calc" tabindex="11" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="profit_method_calc">Benefit Method</label><span class="text-danger">*</span>
                            <select class="form-control " id="profit_method_calc" name="profit_method_calc" tabindex="12" disabled>
                                <option value="">Select Benefit Method</option>
                                <option value="1">Pre Benefit</option>
                                <option value="2">After Benefit</option>
                            </select>
                        </div>
                    </div>


                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 scheme_day">
                        <div class="form-group">
                            <label for="scheme_day_calc">Day</label><span class="text-danger">*</span>
                            <select class="form-control to_clear" id="scheme_day_calc" name="scheme_day_calc" tabindex="13" disabled>
                                <option value="">Select Day</option>
                                <option value="1">Monday</option>
                                <option value="2">Tuesday</option>
                                <option value="3">Wednesday</option>
                                <option value="4">Thursday</option>
                                <option value="5">Friday</option>
                                <option value="6">Saturday</option>
                                <option value="7">Sunday</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 calc" id="date">
                        <div class="form-group">
                            <label for="scheme_date_calc">Date</label><span class="text-danger">*</span>
                            <input type="text" class="form-control to_clear" id="scheme_date_calc" name="scheme_date_calc" tabindex="15" disabled>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="interest_rate_calc">Interest Rate</label><span class="text-danger min-max-int">*</span><!-- Min and max intrest rate-->
                            <input type="text" class="form-control to_clear" id="interest_rate_calc" name="interest_rate_calc" tabindex="16" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="due_period_calc">Due Period</label><span class="text-danger min-max-due">*</span><!-- Min and max Profit Method-->
                            <input type="text" class="form-control to_clear" id="due_period_calc" name="due_period_calc" tabindex="17" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="doc_charge_calc">Document Charges</label><span class="text-danger min-max-doc">*</span><!-- Min and max Document charges-->
                            <input type="text" class="form-control to_clear" id="doc_charge_calc" name="doc_charge_calc" tabindex="18" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="processing_fees_calc">Processing Fees</label><span class="text-danger min-max-proc">*</span><!-- Min and max Processing fee-->
                            <input type="text" class="form-control to_clear" id="processing_fees_calc" name="processing_fees_calc" tabindex="19" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--- -------------------------------------- Calculation - Scheme END ------------------------------- -->

        <!--- -------------------------------------- Loan Calculate START ------------------------------- -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Loan Calculation</div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="loan_amnt_calc">Loan Amount</label><span class="text-danger">*</span>
                            <input type="text" class="form-control refresh_loan_calc" id="loan_amnt_calc" name="loan_amnt_calc" tabindex="21" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="principal_amnt_calc">Principal Amount</label><span class="text-danger princ-diff">*</span>
                            <input type="text" class="form-control refresh_loan_calc" id="principal_amnt_calc" name="principal_amnt_calc" tabindex="22" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="interest_amnt_calc">Interest Amount</label><span class="text-danger int-diff">*</span>
                            <input type="text" class="form-control refresh_loan_calc" id="interest_amnt_calc" name="interest_amnt_calc" tabindex="23" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="total_amnt_calc">Total Amount</label><span class="text-danger">*</span>
                            <input type="text" class="form-control refresh_loan_calc" id="total_amnt_calc" name="total_amnt_calc" tabindex="24" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="due_amnt_calc">Due Amount</label><span class="text-danger due-diff">*</span>
                            <input type="text" class="form-control refresh_loan_calc" id="due_amnt_calc" name="due_amnt_calc" tabindex="25" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="doc_charge_calculate">Document Charges</label><span class="text-danger doc-diff">*</span>
                            <input type="text" class="form-control refresh_loan_calc" id="doc_charge_calculate" name="doc_charge_calculate" tabindex="26" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="processing_fees_calculate">Processing Fees</label><span class="text-danger proc-diff">*</span>
                            <input type="text" class="form-control refresh_loan_calc" id="processing_fees_calculate" name="processing_fees_calculate" tabindex="27" readonly>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="net_cash_calc">Net Cash</label><span class="text-danger">*</span>
                            <input type="text" class="form-control refresh_loan_calc" id="net_cash_calc" name="net_cash_calc" tabindex="28" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- /////////////////////////////////////////////////////////////////// Loan Info END //////////////////////////////////////////////////////////////////////////// -->