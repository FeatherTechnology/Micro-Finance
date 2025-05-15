<div class="row gutters">
    <div class="col-12">
        <div class="toggle-container col-12">
            <input type="date" id='search_date' name='search_date' class="toggle-button" value='' max="<?= date('Y-m-d'); ?>">
            <select class="form-control col-xl-2 col-md-4" name="due_type" id="due_type" >
                <option value="">Select Type</option>
                <option value="1">Monthly</option>
                <option value="2">Weekly</option>
            </select>
            <input type="button" id='nipe_report_btn' name='nipe_report_btn' class="toggle-button" style="background-color: #381f42;color:white" value='Search'>
        </div> <br />
        <!-- Closed report Start -->
        <div class="card">
            <div class="card-body">
                <div class="col-12" style="width: 100%; overflow-x: auto;">
                    <table id="nipe_list_report_table" class="table custom-table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Date / Day </th>
                                <th>Loan ID</th>
                                <th>Loan Date</th>
                                <th>Maturity Date</th>
                                <th>Centre Id</th>
                                <th>Centre Number</th>
                                <th>Centre Name</th>
                                <th>Customer ID</th>
                                <th>Customer Name</th>
                                <th>Mobile</th>
                                <th>Area</th>
                                <th>Loan Amount</th>
                                <th>Due Amount</th>
                                <th>Number Of Dues</th>
                                <th>Pending Amount</th>
                                <th>Number Of Pending Due</th>
                                <th>Payable Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--Closed report End-->
    </div>
</div>