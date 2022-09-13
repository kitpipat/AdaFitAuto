<div class="row p-t-10">
    <div class="col-xs-12 col-md-12 col-lg-12" id="odvStep1Search">
        <div class="col-xs-12 col-md-12 col-lg-12" style="border: 1px solid #ccc!important;">
            <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMDocDateList'); ?></label>
            <div class="row">
                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                    <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMFilterMonthly'); ?></label>
                    <div class="form-group">
                        <select class="form-control selectpicker input-sm" id="ocmSearchBillMonth" name="ocmSearchBillMonth" style="width:100%">
                            <option value="01"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMJan')?></option>
                            <option value="02"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMFeb')?></option>
                            <option value="03"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMMar')?></option>
                            <option value="04"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMApr')?></option>
                            <option value="05"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMMay')?></option>
                            <option value="06"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMJune')?></option>
                            <option value="07"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMJuly')?></option>
                            <option value="08"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMAug')?></option>
                            <option value="09"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMSept')?></option>
                            <option value="10"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMOct')?></option>
                            <option value="11"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMNov')?></option>
                            <option value="12"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMDec')?></option>
                        </select>
                        <script type="text/javascript">
                            var tTRMMonthRM = '<?=@$tTRMMonthRM;?>';
                            if(tTRMMonthRM != ""){
                                $('#ocmSearchBillMonth').val(tTRMMonthRM);
                            }
                        </script>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                    <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMFilterYear'); ?></label>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control xCNInputReadOnly xCNYearPicker" id="ocmSearchBillYear" name="ocmSearchBillYear" value="<?=@$tTRMYearRM;?>">
                            <span class="input-group-btn">
                                <button id="obtTRMDocYear" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                            </span>
                        </div>
                    </div>
                </div>
                <!-- ปุ่มค้นหา -->
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class='row'>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="xCNLabelFrm">&nbsp;</label>
                                <button id="oahTRMAdvanceSearchReset" class="btn xCNBTNDefult xCNBTNDefult1Btn" style="width:100%" onclick="JSxTRMResetFilter()">
                                    <?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMFilterReset');?>
                                </button>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="xCNLabelFrm">&nbsp;</label>
                                <button id="oahTRMAdvanceSearchSubmit" class="btn xCNBTNPrimery" style="width:100%" onclick="JSxTRMShowBillOnEditEventSearch()">
                                    <?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMFilter'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Data Show  รายละเอียดยอดขายประจำเดือน -->
            <div class="row" id="odvTRMSaleSumMontly">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" id="odvStep1SumVatSaleHD">
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" id="odvStep1SumSaleHD">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row p-t-10">
    <div class="col-xs-12 col-md-12 col-lg-12" id="odvStep1DataDTRM">
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        JSxTRMStep1LoadSalePage();
    });

    // โหลด Page Data Sale Calurate
    function JSxTRMStep1LoadSalePage(tStaEvn = ''){
        if($("#ohdTRMRoute").val()  == "docInvoiceRytAndMktFeeEventAdd") {
            var tTRMDocNo   = "";
        } else {
            var tTRMDocNo   = $("#oetTRMDocNo").val();
        }
        $.ajax({
            type    : "POST",
            url     : "docInvoiceRytAndMktFeeLoadSalePage",
            data    : {
                'tStaEvn'       : tStaEvn,
                'tAgnCode'      : $('#oetTRMAgnCode').val(),
                'tBCHCode'      : $('#ohdTRMBchCode').val(),
                'tTRMDocNo'     : tTRMDocNo,
                'tAgnCodeTo'    : $('#ohdTRMAgnCode').val(),
                'tBchCodeTo'    : $('#ohdTRMAgnBchCode').val(),
            },
            async   : false,
            cache   : false,
            Timeout : 0,
            success: function(oResult) {
                let aDataRetun  = JSON.parse(oResult);
                $('#odvStep1SumSaleHD').html(aDataRetun['tViewSumSalHD']);
                $('#odvStep1SumVatSaleHD').html(aDataRetun['tViewSumVatSalHD']);
                $('#odvStep1DataDTRM').html(aDataRetun['tViewDataDTRM']);
                JCNxCloseLoading();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    }






    
</script>