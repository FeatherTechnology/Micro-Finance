<div class="row gutters">
    <div class="col-12">
        <div class="toggle-container col-12">
            <input type="date" id='from_date' name='from_date' class="toggle-button" value=''>
            <input type="date" id='to_date' name='to_date' class="toggle-button" value=''>
            <input type="button" id='loan_issue_report_btn' name='loan_issue_report_btn' class="btn btn-primary" style="background-color: #381f42;color:white" value='Search'>
        </div> <br/>
        <!-- Loan Issue report Start -->
        <div class="card">
            <div class="card-body overflow-x-cls">
                <div class="col-12">
                    <table id="loan_issue_report_table" class="table custom-table">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Loan ID</th>
                                <th>Centre ID</th>
                                <th>Centre Number</th>
                                <th>Centre Name</th>
                                <th>Day</th>
                                <th>Branch</th>
                                <th>Mobile</th>
                                <th>Loan category</th>
                                <th>Loan Date</th>
                                <th>Loan Amount</th>
                                <th>Pricipal Amount</th>
                                <th>Interest Amount</th>
                                <th>Document Charge</th>
                                <th>Processing Fee</th>
                                <th>Total Amount</th>
                                <th>Net Cash</th>
                                <th>Received By</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--Loan Issue report End-->
    </div>
</div>