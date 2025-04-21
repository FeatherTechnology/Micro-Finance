<!-- Loan Issue List Start -->
<div class="card loanissue_table_content">
    <div class="card-body">
        <div class="col-12">
            <table id="loan_issue_table" class="table custom-table">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Loan ID</th>
                        <th>Date / Day</th>
                        <th>Centre ID</th>
                        <th>Centre No</th>
                        <th>Centre Name</th>
                        <th>Mobile</th>
                        <th>Area</th>
                        <th>Loan Category</th>
                        <th>Loan Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<!--Loan Issue List End-->
<div id="loan_issue_content" style="display:none;">
    <div class="text-right">
        <button type="button" class="btn btn-primary" id="back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
    </div>
    <br>
    <div class="radio-container">
        <div class="selector">
            <div class="selector-item">
                <input type="radio" id="documentation" name="loan_issue_type" class="selector-item_radio" value="loandoc" checked>
                <label for="documentation" class="selector-item_label">Documentation</label>
            </div>
            <div class="selector-item">
                <input type="radio" id="loan_issue" name="loan_issue_type" class="selector-item_radio" value="loanissue">
                <label for="loan_issue" class="selector-item_label">Loan Issue</label>
            </div>
        </div>
    </div>
    <br>
    <form id="documentation_form" name="documentation_form">
        <input type="hidden" id="loan_id">
        <div class="row gutters">
            <div class="col-12">
                <!--- -------------------------------------- Document Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Document Info
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_id"> Document ID</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="doc_id" name="doc_id" tabindex="1" value="D" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="customer_name"> Customer Name</label><span class="text-danger">*</span>
                                    <input type="hidden" id="customer_name_edit">
                                    <select class="form-control" id="customer_name" name="customer_name" tabindex="2">
                                        <option value="">Select Customer Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_name"> Document Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="doc_name" name="doc_name" tabindex="3" placeholder="Enter Document Name">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_type">Document Type</label><span class="text-danger">*</span>
                                    <select class="form-control" name="doc_type" id="doc_type" tabindex="4">
                                        <option value="">Select Document Type</option>
                                        <option value="1">Original</option>
                                        <option value="2">Xerox</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_count">Count</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" id="doc_count" name="doc_count" tabindex="5" placeholder="Enter Count">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="remark">Remark</label>
                                    <textarea class="form-control" name="remark" id="remark" placeholder="Enter Remark" tabindex="6"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_upload">Upload</label>
                                    <input type="file" class="form-control" name="doc_upload" id="doc_upload" tabindex="7">
                                    <input type="hidden" name="doc_upload_edit" id="doc_upload_edit">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button name="submit_doc_info" id="submit_doc_info" class="btn btn-primary" tabindex="8" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="document_info" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Document ID</th>
                                                <th>Customer ID</th>
                                                <th>Customer Name</th>
                                                <th>Document Name</th>
                                                <th>Document Type</th>
                                                <th>Count</th>
                                                <th>Remark</th>
                                                <th>Upload</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Document Info END ------------------------------- -->
            </div>
        </div>
    </form>

    <!-- -------------------------------------- Loan Issue START ------------------------------ -->
    <form id="loan_issue_form" name="loan_issue_form" style="display: none;">
        <input type="hidden" id="due_period_calc">
        <input type="hidden" id="profit_type_calc">
        <input type="hidden" id="due_method_calc">
        <input type="hidden" id="scheme_due_method_calc">
        <input type="hidden" id="scheme_day_calc">
        <div class="row gutters">
            <div class="col-12">

                <!--- -------------------------------------- Personal Info START ------------------------------- -->
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
                                    <label for="loan_category_calc"> Loan Category</label><span class="text-danger">*</span>
                                    <input class="form-control" id="loan_category_calc" name="loan_category_calc" tabindex="2" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="centre_no"> Centre Number</label><span class="text-danger">*</span>
                                    <input class="form-control" id="centre_no" name="centre_no" tabindex="3" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="centre_id"> Centre ID</label><span class="text-danger">*</span>
                                    <input class="form-control" id="centre_id" name="centre_id" tabindex="4" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="centre_name"> Centre Name</label><span class="text-danger">*</span>
                                    <input class="form-control" id="centre_name" name="centre_name" tabindex="5" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="centre_mobile"> Mobile Number</label><span class="text-danger">*</span>
                                    <input class="form-control" id="centre_mobile" name="centre_mobile" tabindex="6" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="area"> Area </label><span class="text-danger">*</span>
                                    <input type="text" class="form-control " id="area" name="area" tabindex="7" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="branch"> Branch </label><span class="text-danger">*</span>
                                    <input type="text" class="form-control " id="branch" name="branch" tabindex="8" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Personal Info END ------------------------------- -->

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
                                    <input type="text" class="form-control refresh_loan_calc" id="loan_amnt_calc" name="loan_amnt_calc" tabindex="7" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="principal_amnt_calc">Principal Amount</label><span class="text-danger princ-diff">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="principal_amnt_calc" name="principal_amnt_calc" tabindex="8" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="interest_amnt_calc">Interest Amount</label><span class="text-danger int-diff">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="interest_amnt_calc" name="interest_amnt_calc" tabindex="9" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="total_amnt_calc">Total Amount</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="total_amnt_calc" name="total_amnt_calc" tabindex="10" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="due_amnt_calc">Due Amount</label><span class="text-danger due-diff">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="due_amnt_calc" name="due_amnt_calc" tabindex="11" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_charge_calculate">Document Charges</label><span class="text-danger doc-diff">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="doc_charge_calculate" name="doc_charge_calculate" tabindex="12" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="processing_fees_calculate">Processing Fees</label><span class="text-danger proc-diff">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="processing_fees_calculate" name="processing_fees_calculate" tabindex="13" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="net_cash_calc">Net Cash</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="net_cash_calc" name="net_cash_calc" tabindex="14" readonly>
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
                                    <label for="loan_date_calc">Loan Date</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="loan_date_calc" name="loan_date_calc" tabindex="15" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="due_startdate_calc">Due Start Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="due_startdate_calc" name="due_startdate_calc" tabindex="16">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="maturity_date_calc">Maturity Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="maturity_date_calc" name="maturity_date_calc" tabindex="17" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Collection Info END ------------------------------- -->

                <!--- -------------------------------------- Issue Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Issue Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <table id="issue_info_table" class="table custom-table">
                                <thead>
                                    <tr>
                                        <th>S.NO</th>
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Net Cash</th>
                                        <th>Issued Amount</th>
                                        <th>Payment Mode</th>
                                        <th>Issue Type</th>
                                        <th>Issue Date</th>
                                        <th width="180">Issue Amount</th>
                                        <th>Action</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Issue Info END ------------------------------- -->

                <div class="col-12 mt-3 text-right">
                    <button name="submit_loan_issue" id="submit_loan_issue" class="btn btn-primary" tabindex="30"><span class="icon-check"></span>&nbsp;Submit</button>
                </div>
            </div>
        </div>
    </form>
    <!-- -------------------------------------- Loan Issue END ------------------------------ -->
</div> <!-- Loan Issue Content END - Customer profile & Loan Issue -->