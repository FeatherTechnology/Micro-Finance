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

<div class="row gutters">
    <div class="col-12">
        <!----------------------------- CARD START ACCOUNTS ------------------------------>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-sm-3 col-md-3 col-lg-3"></div>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <div class="form-group">
                                    <label for="opening_bal" class="lbl-style-cls">Opening Balance</label>
                                </div>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1"><label class="lbl-style-cls">:</label></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group">
                                    <label for="opening_val" class="lbl-style-cls opening_val"><?php echo moneyFormatIndia(0); ?></label>

                                </div>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <div class="form-group">
                                    <label for="closing_bal" class="lbl-style-cls">Closing Balance</label>
                                </div>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1"><label class="lbl-style-cls">:</label></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group">
                                    <label for="closing_val" class="lbl-style-cls closing_val"><?php echo moneyFormatIndia(0); ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3 col-md-3 col-lg-3"></div>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <div class="form-group">
                                    <label for="opening_hand_cash" class="lbl-style-cls">Hand Cash</label>
                                </div>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1"><label class="lbl-style-cls">:</label></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group">
                                    <label for="opening_hand_cash_val" class="lbl-style-cls op_hand_cash_val"><?php echo moneyFormatIndia(0); ?></label>
                                </div>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <div class="form-group">
                                    <label for="closing_hand_cash" class="lbl-style-cls">Hand Cash</label>
                                </div>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1"><label class="lbl-style-cls">:</label></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group">
                                    <label for="closing_hand_cash_val" class="lbl-style-cls clse_hand_cash_val"><?php echo moneyFormatIndia(0); ?></label>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="row">
                            <div class="col-sm-3 col-md-3 col-lg-3"></div>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <div class="form-group">
                                    <label for="opening_bank_cash" class="lbl-style-cls">Bank Cash</label>
                                </div>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1"><label class="lbl-style-cls">:</label></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group">
                                    <label for="opening_bank_cash_val" class="lbl-style-cls op_bank_cash_val"><?php echo moneyFormatIndia(0); ?></label>
                                </div>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <div class="form-group">
                                    <label for="closing_bank_cash" class="lbl-style-cls">Bank Cash</label>
                                </div>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1"><label class="lbl-style-cls">:</label></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group">
                                    <label for="closing_bank_cash_val" class="lbl-style-cls clse_bank_cash_val"><?php echo moneyFormatIndia(0); ?></label>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- CARD END ACCOUNTS ------------------------------>
        <div class="coll-radio-container">
            <div class="selector">
                <div class="selector-item" style="display: none;">
                    <input type="radio" id="collection" name="accounts_type" class="selector-item_radio" value="1">
                    <label for="collection" class="selector-item_label">Collection</label>
                </div>
                <div class="selector-item" style="display: none;">
                    <input type="radio" id="loan_issued" name="accounts_type" class="selector-item_radio" value="2">
                    <label for="loan_issued" class="selector-item_label">Loan Issued</label>
                </div>
                <div class="selector-item" style="display: none;">
                    <input type="radio" id="expenses" name="accounts_type" class="selector-item_radio" value="3">
                    <label for="expenses" class="selector-item_label">Expenses</label>
                </div>
                <div class="selector-item" style="display: none;">
                    <input type="radio" id="other_transaction" name="accounts_type" class="selector-item_radio" value="4">
                    <label for="other_transaction" class="selector-item_label">Other Transaction</label>
                </div>
                <div class="selector-item" style="display: none;">
                    <input type="radio" id="customer_savings" name="accounts_type" class="selector-item_radio" value="5">
                    <label for="customer_savings" class="selector-item_label">Customer Savings</label>
                </div>
            </div>
        </div>
        <br>
        <!----------------------------- COLLECTION CARD START ------------------------------>
        <div class="card" id="coll_card" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">Collection List</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3 col-md-3 col-lg-3"></div>
                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                        <div class="form-group">
                            <input type="radio" id="coll_hand_cash" name="coll_cash_type" value='1' />&emsp;<label class='radio-style'>Hand Cash</label>&emsp;
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12" style="display: none;">
                        <div class="form-group">
                            <input type="radio" id="coll_bank_cash" name="coll_cash_type" value='2' />&emsp;<label class='radio-style'>Bank Cash</label>&emsp;
                        </div>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2" style="display: none;">
                        <div class="form-group">
                            <select class="form-control" name="coll_bank_name" id="coll_bank_name" disabled>
                                <option value="">Select Bank</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row table-responsive">
                    <table id="accounts_collection_table" class="table custom-table">
                        <thead>
                            <tr>
                                <th width=25>S.NO</th>
                                <th>User</th>
                                <th>Branch</th>
                                <th>No of Bills</th>
                                <th>Collection Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
        <!----------------------------- COLLECTION CARD END ------------------------------>

        <!----------------------------- LOAN ISSUED CARD START ------------------------------>
        <div class="card" id="loan_issued_card" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">Loan Issued</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">

                        <div class="row">
                            <div class="col-sm-3 col-md-3 col-lg-3"></div>
                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                                <div class="form-group">
                                    <input type="radio" id="loan_issue_hand_cash" name="issue_cash_type" value='1' />&emsp;<label class='radio-style'>Hand Cash</label>&emsp;
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12" style="display:none;">
                                <div class="form-group">
                                    <input type="radio" id="loan_issue_bank_cash" name="issue_cash_type" value='2' />&emsp;<label class='radio-style'>Bank Cash</label>&emsp;
                                </div>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2" style="display:none;">
                                <div class="form-group">
                                    <select class="form-control" name="issue_bank_name" id="issue_bank_name">
                                        <option value="">Select Bank</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row table-responsive">
                            <table id="accounts_loanissue_table" class="table custom-table">
                                <thead>
                                    <tr>
                                        <th width=25>S.NO</th>
                                        <th>User</th>
                                        <th>Branch</th>
                                        <th>No of Members</th>
                                        <th>Total Net Cash</th>
                                        <!-- <th>Balance in Hand</th> -->
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- LOAN ISSUED CARD END ------------------------------>

        <!----------------------------- EXPENSES CARD START ------------------------------>
        <div class="card" id="expenses_card" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">Expenses</h3>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_expenses_modal" id="expenses_add" style="padding: 5px 35px; float: right;" tabindex='30'><span class="icon-add"></span></button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="accounts_expenses_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Cash Mode</th>
                                    <!-- <th>Bank Name</th> -->
                                    <th>Invoice ID</th>
                                    <th>Branch</th>
                                    <th>Expense Category</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <!-- <th>Transaction ID</th> -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- EXPENSES CARD END ------------------------------>

        <!----------------------------- OTHER TRANSACTION CARD START ------------------------------>
        <div class="card" id="other_transaction_card" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">Other Transaction</h3>
                <div class="text-right">
                    <button type="button" name="blnc_sheet_btn" id="blnc_sheet_btn" class="btn btn-primary" data-toggle='modal' data-target='.blncModal'>Balance Sheet</button>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_other_transaction_modal" id="other_trans_add" style="padding: 5px 35px;" tabindex='30'><span class="icon-add"></span></button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table id="accounts_other_trans_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width='20'>S.NO</th>
                                    <th>Coll Mode</th>
                                    <!-- <th>Bank Name</th> -->
                                    <th>Transaction Category</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Reference ID</th>
                                    <!-- <th>Transaction ID</th> -->
                                    <!-- <th>User Name</th> -->
                                    <th>Amount</th>
                                    <th>Remark</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- OTHER TRANSACTION CARD END ------------------------------>
        <!----------------------------- savings CARD START ------------------------------>
        <div class="card" id="customer_savings_card" style="display: none;">
            <input type="hidden" id="savings_chart_hidden_id">
            <div class="coll-radio-container mb-5">
                <div class="selector">
                    <div class="pr-5">
                        <input type="radio" id="customer_based" name="savings_type" class="selector-item_radio" value="1">
                        <label for="customer_based" class="selector-item_label">Customer Based</label>
                    </div>

                    <div>
                        <input type="radio" id="centre_based" name="savings_type" class="selector-item_radio" value="2">
                        <label for="centre_based" class="selector-item_label">Centre Based</label>
                    </div>
                </div>
            </div>

            <div class="Customer_based" style="display: none;">
                <div class="container-fluid mb-3">
                    <form id="savings_form">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="centre_id">Centre ID</label><span class="text-danger">*</span>
                                    <select class="form-control" name="centre_id" id="centre_id" tabindex="1">
                                        <option value="">Select Centre ID</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="aadhar_number">Aadhar Number</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="aadhar_number" id="aadhar_number" tabindex="2" maxlength="14" data-type="adhaar-number" placeholder="Enter Aadhar Number">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cus_id">Customer ID</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="cus_id" id="cus_id" tabindex="3" placeholder="Customer ID" readonly>
                                </div>
                            </div>
                           
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cus_name">Customer Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="cus_name" id="cus_name" tabindex="4" placeholder="Customer Name" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="savings_amnt">Amount</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="savings_amnt" id="savings_amnt" tabindex="5">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="catType">Type</label><span class="text-danger">*</span>
                                    <select class="form-control" name="catType" id="catType" tabindex="6">
                                        <option value="">Select Type</option>
                                        <option value="1">Credit</option>
                                        <option value="2">Debit</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group" style="float: right;">
                                    <button name="submit_savings_creation" id="submit_savings_creation" class="btn btn-primary" tabindex="7" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_savings_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="8">Clear</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table id="customer_savings_table" class="table custom-table">
                                <thead>
                                    <tr>
                                        <th>S.NO</th>
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Aadhar Number</th>
                                        <th>Savings Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
            <div class="centre_based" style="display: none;">
                <div class="card-body centre_based_div">
                    <div class="row">
                        <div class="col-12 table-responsive" id="centre_based_table">

                        </div>
                    </div>
                </div>

                <div id="customer_details_div" style="display: none;">
                    <div class="card p-4 m-4" >
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Center Info</h5>
                            <button class="btn btn-secondary centre_info_back">Back</button>

                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="centreId"> Centre ID</label>
                                                <input type="text" class="form-control" id="centreId" name="centreId" readonly>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="centre_no">Centre No</label>
                                                <input type="number" class="form-control " id="centre_no" name="centre_no" readonly>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="centre_name">Centre Name</label>
                                                <input type="text" class="form-control" id="centre_name" name="centre_name" readonly>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="mobile1"> Mobile Number 1</label>
                                                <input type="number" class="form-control" id="mobile1" name="mobile1" readonly>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="mobile2"> Mobile Number 2</label>
                                                <input type="number" class="form-control" id="mobile2" name="mobile2" readonly>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="area">Area</label>
                                                <input type="text" class="form-control" id="area" name="area" readonly>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="branch">Branch</label>
                                                <input type="text" class="form-control" id="branch" name="branch" readonly>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="latlong">Location</label>
                                                <input type="text" class="form-control" name="latlong" id="latlong" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header ">
                            <h5 class="card-title">Customer List</h5>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <table class=" table custom-table customer_list">
                                        <thead>
                                            <tr>
                                                <th width="50">S.No.</th>
                                                <th>Customer ID</th>
                                                <th>Customer Name</th>
                                                <th>Credited Amount</th>
                                                <th>Debited Amount</th>
                                                <th>Balance Amount</th>
                                                <th>Savings Date</th>
                                                <th>Credit / Debit</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Submit Button Start -->
                        <div class="col-md-12 ">
                            <div class="text-right">
                                <button type="submit" name="submit_savings" id="submit_savings" class="btn btn-primary" value="Submit" tabindex='8'><span class="icon-check"></span>&nbsp;Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Submit Button End -->
            </div>
        </div>
    </div>
</div>
</div>

<!--Expenses Modal Start-->
<div class="modal fade" id="add_expenses_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Expenses</h5>
                <button type="button" class="close exp-clse" data-dismiss="modal" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="expenses_form">

                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <input type="radio" id="expenses_hand_cash" name="expenses_cash_type" tabindex="2" value='1' />&emsp;<label class='radio-style'>Hand Cash</label>&emsp;
                                </div>
                            </div>
                        </div></br>

                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="invoice_id">Invoice ID</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="invoice_id" id="invoice_id" tabindex="5" placeholder="Invoice ID" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="branch_name">Branch Name</label><span class="text-danger">*</span>
                                    <select class="form-control" name="branch_name" id="branch_name" tabindex="6">
                                        <option value="">Select Branch Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="expenses_category">Expenses Category</label><span class="text-danger">*</span>
                                    <select class="form-control" name="expenses_category" id="expenses_category" tabindex="7">
                                        <option value="">Select Expenses Category</option>
                                        <option value="1">Pooja</option>
                                        <option value="2">Vehicle</option>
                                        <option value="3">Fuel</option>
                                        <option value="4">Stationary</option>
                                        <option value="5">Press</option>
                                        <option value="6">Food</option>
                                        <option value="7">Rent</option>
                                        <option value="8">EB</option>
                                        <option value="9">Mobile bill</option>
                                        <option value="10">Office Maintenance</option>
                                        <option value="12">Tax & Auditor</option>
                                        <option value="13">Int Less</option>
                                        <option value="14">Agent Incentive</option>
                                        <option value="15">Common</option>
                                        <option value="16">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="description">Description</label><span class="text-danger">*</span>
                                    <textarea class="form-control" name="description" id="description" tabindex="11"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="expenses_amnt">Amount</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="expenses_amnt" id="expenses_amnt" tabindex="12">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button name="submit_expenses_creation" id="submit_expenses_creation" class="btn btn-primary" tabindex="14" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_expenses_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="15">Clear</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="expenses_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Cash Mode</th>
                                    <!-- <th>Bank Name</th> -->
                                    <th>Invoice ID</th>
                                    <th>Branch</th>
                                    <th>Expense Category</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <!-- <th>Transaction ID</th> -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary exp-clse" data-dismiss="modal" tabindex="15">Close</button>
            </div>
        </div>
    </div>
</div>
<!--Expenses Modal End-->

<!--Other Transaction Modal Start-->
<div class="modal fade" id="add_other_transaction_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Other Transaction</h5>
                <button type="button" class="close clse-trans" data-dismiss="modal" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="other_transaction_form">

                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <input type="radio" id="othertransaction_hand_cash" name="othertransaction_cash_type" tabindex="2" value='1' />&emsp;<label class='radio-style'>Hand Cash</label>&emsp;
                                </div>
                            </div>
                        </div></br>

                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="trans_category">Transaction Category</label><span class="text-danger">*</span>
                                    <select class="form-control" name="trans_category" id="trans_category" tabindex="5">
                                        <option value="">Select Transaction Category</option>
                                        <option value="1">Deposit</option>
                                        <option value="2">Investment</option>
                                        <option value="3">EL</option>
                                        <option value="4">Exchange</option>
                                        <option value="5">Bank Deposit</option>
                                        <option value="6">Bank Withdrawal</option>
                                        <option value="8">Other Income</option>
                                        <option value="9">Bank Unbilled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <label for="other_trans_name">Name</label><span class="text-danger">*</span>
                                    <select class="form-control" id="other_trans_name" name="other_trans_name" tabindex="6">
                                        <option value="">Select Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-1 col-lg-1 col-md-1 col-sm-1 col-12" style="margin-top: 18px; padding-left: 0px !important">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary modalBtnCss" id="name_modal_btn" tabindex="7">+</button>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cat_type">Type</label><span class="text-danger">*</span>
                                    <select class="form-control" name="cat_type" id="cat_type" tabindex="8">
                                        <option value="">Select Type</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="other_ref_id">Reference ID</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="other_ref_id" id="other_ref_id" tabindex="9" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 other_trans_div" style="display: none;">
                                <div class="form-group">
                                    <label for="other_trans_id">Transaction ID</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="other_trans_id" id="other_trans_id" tabindex="10">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="other_amnt">Amount</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="other_amnt" id="other_amnt" tabindex="12">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="other_remark">Remark</label><span class="text-danger">*</span>
                                    <textarea class="form-control" name="other_remark" id="other_remark" tabindex="13"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button name="submit_other_transaction" id="submit_other_transaction" class="btn btn-primary" tabindex="14" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_othertransaction_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="15">Clear</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="other_transaction_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Coll Mode</th>
                                    <!-- <th>Bank Name</th> -->
                                    <th>Transaction Category</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Reference ID</th>
                                    <!-- <th>Transaction ID</th> -->
                                    <!-- <th>User Name</th> -->
                                    <th>Amount</th>
                                    <th>Remark</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary clse-trans" data-dismiss="modal" tabindex="1">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ----------------------------------------------------- Other Transaction Modal End ----------------------------------------------------------------- -->


<!-- --------------------------------------------------------- Other Transaction Name Modal Start --------------------------------------------------------------- -->
<div class="modal fade" id="add_name_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Name</h5>
                <button type="button" class="close name_close" data-dismiss="modal" tabindex="1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="name_form">
                        <div class="row">
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="trans_cat">Transaction Category</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="trans_cat" id="trans_cat" tabindex="2" readonly>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="other_name">Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="other_name" id="other_name" tabindex="3">
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <button name="submit_name" id="submit_name" class="btn btn-primary" tabindex="4" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table id="other_trans_name_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="50">S.No.</th>
                                    <th>Transaction Category</th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary name_close" data-dismiss="modal" tabindex="1">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- --------------------------------------------------------- Other Transaction Name Modal END ------------------------------------------------------------ -->


<!-- /////////////////////////////////////////////////////////////////// Balance Sheet Modal START ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade blncModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel"> Balance Sheet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="resetBlncSheet()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <label for='IDE_type'>Balance Sheet type</label>
                                    <select class="form-control" id='IDE_type' name='IDE_type'>
                                        <option value=''>Select Sheet type</option>
                                        <option value='1'>Deposit</option>
                                        <option value='3'>EL</option>
                                        <option value='4'>Exchange</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <label for='IDE_view_type'>View</label>
                                    <select class="form-control" id='IDE_view_type' name='IDE_view_type'>
                                        <option value=''>Select Sheet type</option>
                                        <option value='1'>Overall</option>
                                        <option value='2'>Individual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12 IDE_nameDiv" style="display:none">
                                <div class="form-group">
                                    <label for='IDE_name_list'>Name</label>
                                    <select class="form-control" id='IDE_name_list' name='IDE_name_list'>
                                        <option value=''>Select Name</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div id="blncSheetDiv">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id='' data-dismiss="modal" onclick="resetBlncSheet()">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Balance Sheet Modal END ////////////////////////////////////////////////////////////////////// -->

<div class="modal fade" id="cntr_bsd_cus_savings" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Centre List</h5>
                <button type="button" class="close cus-clse" data-dismiss="modal" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 overflow-x-cls" id="customer_savings_centre_list">
                        <!-- <table id="customer_savings_table_list" class="table custom-table"> -->
                        <table id="" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width='20'> S.No </th>
                                    <th> Centre ID </th>
                                    <th>Centre Name</th>
                                    <th>Centre Number</th>
                                    <th> Total Savings </th>
                                    <th> Action </th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary cus-savings" data-dismiss="modal" tabindex="8">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// savings Sheet Modal END ////////////////////////////////////////////////////////////////////// -->
<!-- ////////////////////////////////////////////////////////////////// Savings Chart Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="Savings_chart_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Savings Chart</h5>
                <button type="button" class="close savings_chart_back" data-dismiss="modal" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body overflow-x-cls" id="savings_chart_table_div">
                <table class="table custom-table">
                    <thead>
                        <th>S No.</th>
                        <th>Date</th>
                        <th>Credit / Debit</th>
                        <th>Savings</th>
                        <th>Total Savings</th>
                    </thead>
                </table>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary savings_chart_back" data-dismiss="modal" tabindex="2">Back</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Savings Chart Modal END ////////////////////////////////////////////////////////////////////// -->