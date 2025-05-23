<!-- Loan Entry List Start -->
<div class="text-right">
    <button type="button" class="btn btn-primary " id="add_loan"><span class="fa fa-plus"></span>&nbsp; Add Loan Entry</button>
    <button type="button" class="btn btn-primary" id="back_btn" style="display: none;"><span class="icon-arrow-left"></span>&nbsp; Back </button>
</div>
<br>
<div class="card loan_table_content">
    <div class="card-body">
        <div class="col-12">
            <table id="loan_entry_table" class="table custom-table">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Loan ID</th>
                        <th>Date / Day</th>
                        <th>Loan Category</th>
                        <th>Centre ID</th>
                        <th>Centre No</th>
                        <th>Centre Name</th>
                        <th>Mobile</th>
                        <th>Area</th>
                        <th>Branch</th>
                        <th>Centre Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<!--Loan Entry List End-->
<div id="loan_entry_content" style="display:none;">
    <!-- -------------------------------------- Loan Calculation START ------------------------------ -->
    <form id="loan_entry_loan_calculation" name="loan_entry_loan_calculation">
        <input type="hidden" id="loan_calculation_id">
        <input type="hidden" id="int_rate_upd">
        <input type="hidden" id="due_period_upd">
        <input type="hidden" id="doc_charge_upd">
        <input type="hidden" id="proc_fees_upd">
        <input type="hidden" id="proc_type">

        <div class="row gutters">
            <div class="col-12">
                <!--- -------------------------------------- Loan Info ------------------------------- -->

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Loan Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_id_calc"> Loan ID</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="loan_id_calc" name="loan_id_calc" tabindex="1" value="" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="Centre_id"> Centre ID</label><span class="text-danger">*</span>
                                    <input type="hidden" id="Centre_id_edit">
                                    <select class="form-control" id="Centre_id" name="Centre_id" tabindex="2">
                                        <option value=" ">Select Centre ID</option>
                                    </select>
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
                                    <input type="hidden" id="loan_category_calc2">
                                    <select class="form-control" id="loan_category_calc" name="loan_category_calc" tabindex="6">
                                        <option value="">Select Loan Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_amount_calc"> Loan Amount</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" id="loan_amount_calc" name="loan_amount_calc" tabindex="7">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="total_cus"> Total No Of Customer</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" id="total_cus" name="total_cus" tabindex="8">
                                </div>
                            </div>
                            <!-- <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_amount_per_cus"> Loan Amount Per Customer</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" id="loan_amount_per_cus" name="loan_amount_per_cus" tabindex="9" readonly>
                                </div>
                            </div> -->
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="profit_type_calc">Profit Type</label><span class="text-danger">*</span>
                                    <input type="hidden" id="profit_name_edit">
                                    <select class="form-control" id="profit_type_calc" name="profit_type_calc" tabindex="10">
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
                <div class="card" id="profit_type_calc_scheme" style="display: none;">
                    <div class="card-header">
                        <div class="card-title calc_scheme_title">Calculation</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 scheme" style="display:none">
                                <div class="form-group">
                                    <label for="scheme_name_calc">Scheme Name</label><span class="text-danger">*</span>
                                    <input type="hidden" id="scheme_name_edit">
                                    <select class="form-control to_clear" id="scheme_name_calc" name="scheme_name_calc" tabindex="14">
                                        <option value="">Select Scheme Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 calc" style="display:none">
                                <div class="form-group">
                                    <label for="due_method_calc">Due Method</label><span class="text-danger">*</span>
                                    <select class="form-control" id="due_method_calc" name="due_method_calc" tabindex="11">
                                        <option value="">Select Due Method</option>
                                        <option value="1">Monthly</option>
                                        <option value="2">Weekly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 calc" style="display:none">
                                <div class="form-group">
                                    <label for="profit_method_calc">Benefit Method</label><span class="text-danger">*</span>
                                    <select class="form-control " id="profit_method_calc" name="profit_method_calc" tabindex="12">
                                        <option value="">Select Benefit Method</option>
                                        <option value="1">Pre Benefit</option>
                                        <option value="2">After Benefit</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 scheme_day" style="display:none">
                                <div class="form-group">
                                    <label for="scheme_day_calc">Day</label><span class="text-danger">*</span>
                                    <select class="form-control to_clear" id="scheme_day_calc" name="scheme_day_calc" tabindex="13">
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
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 scheme_date" style="display:none">
                                <div class="form-group">
                                    <label for="scheme_date_calc">Date</label><span class="text-danger">*</span>
                                    <input type="hidden" id="date_name_edit">
                                    <select class="form-control to_clear" id="scheme_date_calc" name="scheme_date_calc" tabindex="15">
                                        <option value="">Select Date</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="interest_rate_calc">Interest Rate</label><span class="text-danger min-max-int">*</span><!-- Min and max intrest rate-->
                                    <input type="number" class="form-control to_clear" id="interest_rate_calc" name="interest_rate_calc" tabindex="16" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="due_period_calc">Due Period</label><span class="text-danger min-max-due">*</span><!-- Min and max Profit Method-->
                                    <input type="number" class="form-control to_clear" id="due_period_calc" name="due_period_calc" tabindex="17" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_charge_calc">Document Charges</label><span class="text-danger min-max-doc">*</span><!-- Min and max Document charges-->
                                    <input type="number" class="form-control to_clear" id="doc_charge_calc" name="doc_charge_calc" tabindex="18">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="processing_fees_calc">Processing Fees</label><span class="text-danger min-max-proc">*</span><!-- Min and max Processing fee-->
                                    <input type="number" class="form-control to_clear" id="processing_fees_calc" name="processing_fees_calc" tabindex="19">
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
                        <input type="button" class="btn btn-outline-primary card-head-btn" id="refresh_cal" tabindex="20" value="Calculate">
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
                <!--- -------------------------------------- Loan Calculate END ------------------------------- -->

                <!--- -------------------------------------- Collection Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Collection Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="due_startdate_calc">Due Start Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="due_startdate_calc" name="due_startdate_calc" tabindex="29">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="maturity_date_calc">Maturity Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="maturity_date_calc" name="maturity_date_calc" tabindex="30" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Collection Info END ------------------------------- -->

                <!--- -------------------------------------- Reference Info START ------------------------------- -->

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Reference Info
                            <!-- <button type="button" class="btn btn-primary" id="add_rep" name="add_rep" data-toggle="modal" data-target="#add_rep_info_modal" onclick="getRepresentTable()" style="padding: 5px 35px; float: right;" tabindex='9'><span class="icon-add"></span></button> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="rep_info_table" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Name</th>
                                                <th>Aadhar No</th>
                                                <th>Occupation</th>
                                                <th>Mobile No</th>
                                                <th>Designation</th>
                                                <th>Remark</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Other Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Add Customer</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <label for="customer_mapping">Customer Mapping</label><span class="text-danger">*</span>
                                    <select class="form-control" id="customer_mapping" name="customer_mapping" tabindex="31">
                                        <option value="">Select Customer Mapping</option>
                                        <option value="1">New</option>
                                        <option value="2">Renewal</option>
                                        <option value="3">Additional</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <label for="add_customer">Customer Name</label><span class="text-danger">*</span>
                                    <select class="form-control" id="add_customer" name="add_customer" tabindex="32">
                                        <option value="">Select Customer Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <label for="customer_amount">Amount</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="customer_amount" name="customer_amount" tabindex="34">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <label for="designation">Designation</label>
                                    <input type="text" class="form-control" id="designation" name="designation" tabindex="33">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group d-flex justify-content-end">
                                <input type="button" class="btn btn-primary modalBtnCss" id="submit_cus_map" name="submit_cus_map" value="Add" tabindex="35">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <table id="cus_mapping_table" class="table custom-table">
                                    <thead>
                                        <tr>
                                            <th width="20">S.No.</th>
                                            <th>Customer ID</th>
                                            <th style="display: none;">Customer Map Id</th>
                                            <th>Customer Name</th>
                                            <th>Customer Data</th>
                                            <th>Aadhar No</th>
                                            <th>Mobile No</th>
                                            <th style="width: 80px;">Area</th>
                                            <th style="width: 150px;">Customer Loan Amount</th>
                                            <th style="width: 110px;">Designation</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Other Info END ------------------------------- -->


                <div class="col-12 mt-3 text-right">
                    <input type="hidden" id="cus_map_id">
                    <button name="submit_loan_calculation" id="submit_loan_calculation" class="btn btn-primary" tabindex="36"><span class="icon-check"></span>&nbsp;Submit</button>
                    <button type="reset" id="clear_loan_calc_form" class="btn btn-outline-secondary" tabindex="37">Clear</button>
                </div>
            </div>
        </div>
    </form>
    <!-- -------------------------------------- Loan Calculation END ------------------------------ -->
</div> <!-- Loan entry Content END - Loan Calculation -->