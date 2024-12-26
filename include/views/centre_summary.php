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