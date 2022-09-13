<script type="text/javascript">
    var tStatusDoc      = $('#ohdTRMStaDoc').val();
    var tChkrount       = $('#ohdTRMRoute').val();
    var nAddressVersion = '<?=FCNaHAddressFormat('TCNMCst')?>';
    var tStatusDoc      = $('#ohdTRMStaDoc').val();
    var tStatusApv      = $('#ohdTRMStaApv').val();
    var nLangEdits      = '<?php echo $this->session->userdata("tLangEdit") ?>';
    var nOptDecimalShow = '<?php echo $nOptDecimalShow?>';



    $('.selectpicker').selectpicker('refresh');
    
    $('.xCNDatePicker').datepicker({
        format : "yyyy-mm-dd",
        todayHighlight : true,
        enableOnReadonly : false,
        disableTouchKeyboard : true,
        autoclose : true
    });

    $('.xCNTimePicker').datetimepicker({
        format : 'HH:mm:ss'
    });

    $('.xCNYearPicker').datepicker({
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years"
    });

    $('#obtTRMDocDate').unbind().click(function(){
        $('#oetTRMDocDate').datepicker('show');
    });
    $('#obtTRMDocTime').unbind().click(function(){
        $('#oetTRMDocTime').datetimepicker('show');
    });
    $('#obtTRMDocYear').unbind().click(function(){
        $('#ocmSearchBillYear').datepicker('show');
    });


    /** ================== Check Box Auto GenCode ===================== */
        $('#ocbTRMStaAutoGenCode').on('change', function (e) {
            if($('#ocbTRMStaAutoGenCode').is(':checked')){
                $("#oetTRMDocNo").val('');
                $("#oetTRMDocNo").attr("readonly", true);
                $('#oetTRMDocNo').closest(".form-group").css("cursor","not-allowed");
                $('#oetTRMDocNo').css("pointer-events","none");
                $("#oetTRMDocNo").attr("onfocus", "this.blur()");
                $('#ofmTRMFormAdd').removeClass('has-error');
                $('#ofmTRMFormAdd .form-group').closest('.form-group').removeClass("has-error");
                $('#ofmTRMFormAdd em').remove();
            } else {
                $('#oetTRMDocNo').closest(".form-group").css("cursor","");
                $('#oetTRMDocNo').css("pointer-events","");
                $('#oetTRMDocNo').attr('readonly',false);
                $("#oetTRMDocNo").removeAttr("onfocus");
            }
        });
    /** =============================================================== */

    //  ======================================== เลือกสาขาที่สร้างเอกสาร ========================================
        var oBranchOption   = function(poDataFnc) {
            var tInputReturnCode    = poDataFnc.tReturnInputCode;
            var tInputReturnName    = poDataFnc.tReturnInputName;
            var tNextFuncName       = poDataFnc.tNextFuncName;
            var tSQLWhere           = "";
            var tUsrLevel           = "<?= $this->session->userdata('tSesUsrLevel') ?>";
            var tBchMulti           = "<?= $this->session->userdata("tSesUsrBchCodeMulti"); ?>";
            if (tUsrLevel != "HQ"){
                //แบบสาขา
                tSQLWhere   = " AND TCNMBranch.FTBchCode IN (" + tBchMulti + ") ";
            }
            // ตัวแปร ออฟชั่นในการ Return
            var oOptionReturn = {
                Title: ['authen/user/user', 'tBrowseBCHTitle'],
                Table: {
                    Master: 'TCNMBranch',
                    PK: 'FTBchCode'
                },
                Join: {
                    Table: ['TCNMBranch_L'],
                    On: ['TCNMBranch_L.FTBchCode = TCNMBranch.FTBchCode AND TCNMBranch_L.FNLngID = ' + nLangEdits]
                },
                Where: {
                    Condition: [tSQLWhere]
                },
                GrideView: {
                    ColumnPathLang: 'authen/user/user',
                    ColumnKeyLang: ['tBrowseBCHCode', 'tBrowseBCHName'],
                    ColumnsSize: ['10%', '75%'],
                    DataColumns: ['TCNMBranch.FTBchCode', 'TCNMBranch_L.FTBchName'],
                    DataColumnsFormat: ['', ''],
                    WidthModal: 50,
                    Perpage: 10,
                    OrderBy: ['TCNMBranch.FTBchCode DESC']
                },
                CallBack: {
                    ReturnType: 'S',
                    Value: [tInputReturnCode, "TCNMBranch.FTBchCode"],
                    Text: [tInputReturnName, "TCNMBranch_L.FTBchName"]
                },
                NextFunc: {
                    FuncName: tNextFuncName,
                    ArgReturn: ['FTBchCode']
                }
            };
            return oOptionReturn;
        }

        $('#obtTRMBrowseBranch').unbind().click(function() {
            let nStaSession = JCNxFuncChkSessionExpired();
            if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
                JSxCheckPinMenuClose();
                window.oTRMBranchOption = undefined;
                oTRMBranchOption        = oBranchOption({
                    'tReturnInputCode'  : 'ohdTRMBchCode',
                    'tReturnInputName'  : 'oetTRMBchName',
                    'tNextFuncName'     : 'JSxTRMWhenSeletedBranch'
                });
                JCNxBrowseData('oTRMBranchOption');
            } else {
                JCNxShowMsgSessionExpired();
            }
        });

        function JSxTRMWhenSeletedBranch() {
        }

    // =====================================================================================================

    //  ======================================= เลือก ตัวแทนขาย/แฟร์นไซส์ =====================================
        var oTRMAgnOption   = function(poDataFnc) {
            var tInputReturnCode    = poDataFnc.tReturnInputCode;
            var tInputReturnName    = poDataFnc.tReturnInputName;
            var aArgReturn          = poDataFnc.aArgReturn;
            var tNextFuncName       = poDataFnc.tNextFuncName;
            var tParamsAgnCode      = poDataFnc.tParamsAgnCode;
            var tWhereAgency        = '';
            if (tParamsAgnCode != "") {
                tWhereAgency    = " AND ( TCNMAgency.FTAgnCode = '" + tParamsAgnCode + "' OR ISNULL(TCNMAgency.FTAgnCode,'') = '' ) ";
            }
            var oOptionReturn       = {
                Title   : ['ticket/agency/agency', 'tAggTitle'],
                Table   : {Master: 'TCNMAgency',PK: 'FTAgnCode'},
                Join: {
                    Table: ['TCNMAgency_L'],
                    On: ['TCNMAgency_L.FTAgnCode = TCNMAgency.FTAgnCode AND TCNMAgency_L.FNLngID = ' + nLangEdits]
                },
                Where : {
                    Condition : [" AND TCNMAgency.FTAgnStaActive = '1' " + tWhereAgency]
                },
                GrideView: {
                    ColumnPathLang: 'ticket/agency/agency',
                    ColumnKeyLang: ['tAggCode', 'tAggName'],
                    ColumnsSize: ['15%', '85%'],
                    WidthModal: 50,
                    DataColumns: ['TCNMAgency.FTAgnCode', 'TCNMAgency_L.FTAgnName'],
                    DataColumnsFormat: ['', ''],
                    Perpage: 10,
                    OrderBy: ['TCNMAgency.FTAgnCode DESC'],
                },
                CallBack: {
                    ReturnType: 'S',
                    Value: [tInputReturnCode, "TCNMAgency.FTAgnCode"],
                    Text: [tInputReturnName, "TCNMAgency_L.FTAgnName"],
                },
                RouteAddNew: 'agency',
                BrowseLev: 1,
                NextFunc: {
                    FuncName  : tNextFuncName,
                    ArgReturn : aArgReturn
                },
                // DebugSQL: true,
            }
            return oOptionReturn;
        }

        // Browse Customer Supplier 
        $('#obtTRMBrowseAgn').unbind().click(function(){
            let nStaSession = JCNxFuncChkSessionExpired();
            let tCstName    = $('#oetPanel_AgnName').val();
            if (tCstName != '') {
                $('#tWarningText').text('<?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMWarningText') ?>');
                $('#tWarningTextCfm').text('<?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMWarningTextCfm')?>');
                $('.xCNBtnControllHide').show();
                $('#odvTRMPopupWarning').modal('show');
            } else {
                if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
                    JSxCheckPinMenuClose();
                    window.oTRMBrowseAgnOption  = undefined;
                    oTRMBrowseAgnOption         = oTRMAgnOption({
                        'tParamsAgnCode'    : '<?= $this->session->userdata("tSesUsrAgnCode") ?>',
                        'tReturnInputCode'  : 'ohdTRMAgnCode',
                        'tReturnInputName'  : 'ohdTRMAgnName',
                        'tNextFuncName'     : 'JSxTRMSetConditionAfterSelectAgn',
                        'aArgReturn'        : ['FTAgnCode', 'FTAgnName']
                    });
                    JCNxBrowseData('oTRMBrowseAgnOption');
                } else {
                    JCNxShowMsgSessionExpired();
                }
            }
        });

        function JSxTRMSetConditionAfterSelectAgn(poDataNextFunc) {
            let aDataNextFunc   = JSON.parse(poDataNextFunc);
            let tAgnCode        = aDataNextFunc[0];
            let tAgnName        = aDataNextFunc[1];
            let tDataNextFunc   = "NextFunc";
            $.ajax({
                type    : "POST",
                url     : "docInvoiceRytAndMktFeeFindingAgnBch",
                data    : {'tAgnCode' : tAgnCode,'tAgnName' : tAgnName},
                cache   : false,
                Timeout : 0,
                success: function(oResult) {
                    aData   = JSON.parse(oResult);
                    if (aData['aChkData']['row'] == 1) {
                        $.ajax({
                            type    : "POST",
                            url     : "docInvoiceRytAndMktFeeFindingAgnBchAddress",
                            data: {
                                'tAgnCode'    : tAgnCode,
                                'tAgnBchCode' : aData['aChkData']['rtResult'][0]['FTBchcode']
                            },
                            cache: false,
                            Timeout: 0,
                            success: function(oResult) {
                                var aDataAddress    = JSON.parse(oResult);
                                if(aDataAddress['rtCode'] != 1) {
                                    $('#odvTRMPopupWarning #tWarningText').text('<?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMAgnAddrNotFound') ?>');
                                    $('#odvTRMPopupWarning #tWarningTextCfm').text('');
                                    $('.xCNBtnControllHide').hide();
                                    $('#odvTRMPopupWarning').modal('show');
                                }else{
                                    // Set ข้อมูล สาขา / ตัวแทนขาย/แฟร์นไซส์
                                    $('#ohdTRMAgnBchCode').val(aDataAddress['aQuery']['FTBchCode']);
                                    $('#ohdTRMAgnBchName').val(aDataAddress['aQuery']['FTBchName']);
                                    // Set ข้อมูล ตัวแทนขาย / แฟร์นไซส์ - Panel
                                    $('#oetPanel_AgnName').val(aDataAddress['aQuery']['FTAgnName']);
                                    // FTAddVersion
                                    var AddressfromCst  = aDataAddress['aQuery']['FTAddVersion']
                                    var tAddfull ='';
                                    if(AddressfromCst == '1'){
                                        tAddfull = aDataAddress['aQuery']['FTAddV1No']+' '+aDataAddress['aQuery']['FTAddV1Soi']+' '+aDataAddress['aQuery']['FTAddV1Village']+' '+aDataAddress['aQuery']['FTAddV1Road']+' '+aDataAddress['aQuery']['FTSudName']+' '+aDataAddress['aQuery']['FTDstName']+' '+aDataAddress['aQuery']['FTPvnName']+' '+aDataAddress['aQuery']['FTAddV1PostCode'];
                                    }else if(AddressfromCst == '2'){
                                        tAddfull = aDataAddress['aQuery']['FTAddV2Desc1']+' '+aDataAddress['aQuery']['FTAddV2Desc2'];
                                    }else{
                                        tAddfull    = "-";
                                    }
                                    $('#oetPanel_AgnAddress').val(tAddfull);
                                    $('#oetPanel_AgnTel').val(aDataAddress['aQuery']['FTAddTel']);
                                    $('#oetPanel_AgnMail').val(aDataAddress['aQuery']['FTAgnEmail']);

                                    // วิ่งโหลดข้อมูลรายละเอียดฟอร์ทสรุปยอดขาย
                                    // JSxTRMShowBillOnEditEventSearch();
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                JCNxResponseError(jqXHR, textStatus, errorThrown);
                            }
                        });
                    }else{
                        
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        }
    // =====================================================================================================

    //  ========================================== เลือก สาขาตัวแทนขาย =======================================
        var oBrowseAgnBranch    = function(poReturnInput) {
            var tUsrLevel           = "<?php echo $this->session->userdata("tSesUsrLevel"); ?>";
            var tBchCodeMulti       = "<?php echo $this->session->userdata("tSesUsrBchCodeMulti"); ?>";
            var tInputReturnCode    = poReturnInput.tReturnInputCode;
            var tInputReturnName    = poReturnInput.tReturnInputName;
            var tNextFuncName       = poReturnInput.tNextFuncName;
            var aArgReturn          = poReturnInput.aArgReturn;
            var tAgnCodeWhere       = poReturnInput.tAgnCodeWhere;
            var tWhere      = "";
            if (tUsrLevel != "HQ") {
                tWhere      = " AND TCNMBranch.FTBchCode IN (" + tBchCodeMulti + ") ";
            }
            var tWhereAgn   = '';
            if(tAgnCodeWhere != '' || tAgnCodeWhere != null) {
                tWhereAgn   = " AND TCNMBranch.FTAgnCode = '" + tAgnCodeWhere + "'";
            }
            var oOptionReturn   = {
                Title   : ['company/branch/branch', 'tBCHTitle'],
                Table   : {Master : 'TCNMBranch',PK : 'FTBchCode'},
                Join    : {
                    Table   : ['TCNMBranch_L', 'TCNMAgency_L'],
                    On      : [
                        'TCNMBranch_L.FTBchCode = TCNMBranch.FTBchCode AND TCNMBranch_L.FNLngID = ' + nLangEdits,
                        'TCNMAgency_L.FTAgnCode = TCNMBranch.FTAgnCode AND TCNMAgency_L.FNLngID = ' + nLangEdits,
                    ],
                },
                Where       : {Condition : [tWhere + tWhereAgn]},
                GrideView   : {
                    ColumnPathLang      : 'company/branch/branch',
                    ColumnKeyLang       : ['tBCHCode', 'tBCHName'],
                    ColumnsSize         : ['15%', '75%'],
                    WidthModal          : 50,
                    DataColumns         : ['TCNMBranch.FTBchCode', 'TCNMBranch_L.FTBchName', 'TCNMAgency_L.FTAgnName', 'TCNMBranch.FTAgnCode'],
                    DataColumnsFormat   : ['', '', '', ''],
                    DisabledColumns     : [2, 3],
                    WidthModal          : 50,
                    Perpage             : 10,
                    OrderBy             : ['TCNMBranch.FTBchCode DESC'],
                },
                CallBack : {
                    ReturnType: 'S',
                    Value   : [tInputReturnCode,"TCNMBranch.FTBchCode"],
                    Text    : [tInputReturnName,"TCNMBranch_L.FTBchName"],
                },
                NextFunc : {
                    FuncName  : tNextFuncName,
                    ArgReturn : aArgReturn
                },
                // DebugSQL: true,
            }
            return oOptionReturn;
        };

        // Browse Customer Branch
        $('#obtTRMBrowseAgnBch').unbind().click(function(){
            let nStaSession     = JCNxFuncChkSessionExpired();
            if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
                let tAgnCode    = $('#ohdTRMAgnCode').val();
                JSxCheckPinMenuClose();
                window.oBrowseAgnBranchOption   = oBrowseAgnBranch({
                    'tReturnInputCode'  : 'ohdTRMAgnBchCode',
                    'tReturnInputName'  : 'ohdTRMAgnBchName',
                    'tNextFuncName'     : 'JSxTRMSetConditionAfterSelectAgnBch',
                    'aArgReturn'        : ['FTBchCode', 'FTBchName'],
                    'tAgnCodeWhere'     : tAgnCode
                });
                JCNxBrowseData('oBrowseAgnBranchOption');
            } else {
                JCNxShowMsgSessionExpired();
            }
        });

        function JSxTRMSetConditionAfterSelectAgnBch(poDataNextFunc) {
            JSxTRMResetFilter();
        }

    // =====================================================================================================

    //  ============================================ เลือก ชำระโดย ==========================================
        var oTRMBbkOption   = function(poDataFnc) {
            var tInputReturnCode    = poDataFnc.tReturnInputCode;
            var tInputReturnName    = poDataFnc.tReturnInputName;
            var oOptionReturn       = {
                Title : ['bank/bank/bank', 'tBNKTitle'],
                Table : {Master: 'TFNMBookBank',PK: 'FTBbkCode'},
                Join  : {
                    Table   : ['TFNMBookBank_L', 'TFNMBank_L'],
                    On      : [
                        'TFNMBookBank.FTBchCode = TFNMBookBank_L.FTBchCode AND TFNMBookBank.FTBbkCode = TFNMBookBank_L.FTBbkCode AND TFNMBookBank_L.FNLngID = ' + nLangEdits,
                        'TFNMBookBank.FTBnkCode = TFNMBank_L.FTBnkCode AND TFNMBank_L.FNLngID = ' + nLangEdits
                    ]
                },
                Where : {
                    Condition: [' AND TFNMBookBank.FTBchCode = \'' + $('#ohdTRMBchCode').val() + '\' '],
                },
                GrideView : {
                    ColumnPathLang      : 'bank/bank/bank',
                    ColumnKeyLang       : ['tBNKBbkCode', 'tBNKBbkName', 'tBNKTBName'],
                    ColumnsSize         : ['15%', '25%', '50%'],
                    WidthModal          : 50,
                    DataColumns         : ['TFNMBookBank.FTBbkCode', 'TFNMBookBank_L.FTBbkName', 'TFNMBank_L.FTBnkName'],
                    DataColumnsFormat   : ['', '', ''],
                    Perpage             : 10,
                    OrderBy             : ['TFNMBookBank.FDCreateOn DESC'],
                },
                CallBack : {
                    ReturnType  : 'S',
                    Value       : [tInputReturnCode,"TFNMBookBank.FTBbkCode"],
                    Text        : [tInputReturnName,"TFNMBookBank_L.FTBbkName"],
                },
                // DebugSQL: true,
            };
            return oOptionReturn;
        }

        // Browse Customer Bookbank
        $('#oimTRMBrowseBbk').unbind().click(function(){
            let nStaSession = JCNxFuncChkSessionExpired();
            if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
                JSxCheckPinMenuClose();
                window.oTRMBbkOptionOption  = oTRMBbkOption({
                    'tReturnInputCode'  : 'oetTRMBbkCode',
                    'tReturnInputName'  : 'oetTRMBbkName',
                });
                JCNxBrowseData('oTRMBbkOptionOption');
            } else {
                JCNxShowMsgSessionExpired();
            }
        });

    // =====================================================================================================


    // Function Reset Filter
    function JSxTRMResetFilter(){
        let dMonthNow   = '<?=date('m');?>'
        let dYearNow    = '<?=date('Y');?>'
        // Reset Filter Default Month
        $('#ocmSearchBillMonth').val(dMonthNow);
        $('#ocmSearchBillMonth').selectpicker('refresh');
        // Reset Filter Default Year
        $('#ocmSearchBillYear').val(dYearNow);
        $('#ocmSearchBillYear').datepicker('setDate',dYearNow);
        // Load Refresh Default Detail Sale
        JCNxOpenLoading();
        let  tDocNo = $('#oetTRMDocNo').val();
        $.ajax({
            type    : "POST",
            url     : "docInvoiceRytAndMktFeeClearTemp",
            data    : {tDocNo : tDocNo},
            cache   : false,
            Timeout : 0,
            success: function(oResult) {
                JSxTRMStep1LoadSalePage('Clear');
            }
        });
    }

    // กรองข้อมูล
    function JSxTRMShowBillOnEditEventSearch(){
        let tAgnCodeTo  = $("#ohdTRMAgnCode").val();
        let tBchCodeTo  = $("#ohdTRMAgnBchCode").val();
        let tBillMonth  = $("#ocmSearchBillMonth").val();
        let tBillYear   = $("#ocmSearchBillYear").val();
        if(tAgnCodeTo != "" && tBchCodeTo != ""){
            $.ajax({
                type    : "POST",
                url     : "docInvoiceRytAndMktFeeChkDocHaveInDB",
                data    : {
                    'tAgnCodeTo' : tAgnCodeTo,
                    'tBchCodeTo' : tBchCodeTo,
                    'tBillMonth' : tBillMonth,
                    'tBillYear'  : tBillYear,
                },
                cache   : false,
                Timeout : 0,
                success : function(oResult){
                    let aDataRetun  = JSON.parse(oResult);
                    if(aDataRetun['rtCode'] == 1){
                        let aDataChkDocHave = aDataRetun['raItems'];
                        // Check Text Alert
                        switch(aDataChkDocHave['FTXphStaApv']){
                            case '0' :
                                // เอกสารที่มีในระบบถูกสร้างไว้แต่ยังไม่ได้อนุมัติ
                                var tTextWanning    = '<?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTextWanningHaveData0');?>';
                            break;
                            case '1' :
                                // เอกสารที่มีในระบบถูกสร้างไว้อนุมัติไว้แล้ว
                                var tTextWanning    = '<?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTextWanningHaveData1');?>';
                            break;
                        }
                        FSvCMNSetMsgWarningDialog(tTextWanning);
                        JSxTRMStep1LoadSalePage('Clear');
                    }else{
                        // ไม่พบข้อมูลซ้ำในระบบ
                        JCNxOpenLoading();
                        var aDataSend   = {
                            'tDocNo'            : $('#oetTRMDocNo').val(),
                            'tAgnCode'          : $('#oetTRMAgnCode').val(),
                            'tBchCode'          : $("#ohdTRMBchCode").val(),
                            'tAgnCodeTo'        : tAgnCodeTo,
                            'tBchCodeTo'        : tBchCodeTo,
                            'tSearchBillMonth'  : $("#ocmSearchBillMonth").val(),
                            'tSearchBillYear'   : $("#ocmSearchBillYear").val(),
                            'tVatInOrEx'        : $("#ocmTRMfoVatInOrEx").val()
                        };          
                        $.ajax({
                            type    : "POST",
                            url     : "docInvoiceRytAndMktFeeFindingSale",
                            data    : aDataSend,
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
                },
                error   : function(jqXHR, textStatus, errorThrown){
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        } else {
            let tTRMMsgNotFoundAgn  = '<?=language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMMsgNotFoundAgn');?>';
            FSvCMNSetMsgWarningDialog(tTRMMsgNotFoundAgn);
        }
    }
    








    // เคลียค่า
    function JSxTRMPopupWarning(pbIsConfirm){
        JCNxOpenLoading();
        let  tDocNo = $('#oetTRMDocNo').val();
        $.ajax({
            type    : "POST",
            url     : "docInvoiceRytAndMktFeeClearTemp",
            data    : {tDocNo : tDocNo},
            cache   : false,
            Timeout : 0,
            success: function(oResult) {
                var tDocNo      = $('#ohdTRMDocNo').val();
                var tDocDate    = $('#ohdTRMDocDate').val();
                var tDocTime    = $('#ohdTRMDocTime').val();
                $('#oetTRMDocNo').val(tDocNo);
                $('#oetTRMDocDate').val(tDocDate);
                $('#oetTRMDocTime').val(tDocTime);
                // Clear Input Agency
                $('#ohdTRMAgnCode').val('');
                $('#ohdTRMAgnName').val('');
                $('#ohdTRMAgnBchCode').val('');
                $('#ohdTRMAgnBchName').val('');
                $('#oetPanel_AgnName').val('');
                $('#oetPanel_AgnAddress').val('');
                $('#oetPanel_AgnTel').val('');
                $('#oetPanel_AgnMail').val('');
                // Modal Close
                $('#odvTRMPopupWarning').modal('hide');
                JSxTRMStep1LoadSalePage('Clear');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    }

    // พิมพ์เอกสาร
    function JSxTRMPrintDoc(){
        let aInfor  = [
            {"Lang"         : '<?=FCNaHGetLangEdit(); ?>'},
            {"ComCode"      : '<?=FCNtGetCompanyCode(); ?>'},
            {"BranchCode"   : '<?=FCNtGetAddressBranch(@$tTRMBchCode); ?>'},
            {"DocCode"      : '<?=@$tTRMDocNo; ?>'}, // เลขที่เอกสาร
            {"DocBchCode"   : '<?=@$tTRMBchCodeTo;?>'}
        ];
        window.open("<?=base_url(); ?>formreport/Frm_SQL_SMMonthlyInvCal_Fit?infor=" + JCNtEnCodeUrlParameter(aInfor) , '_blank');
    }

    // ยกเลิกเอกสาร
    function JSxTRMDocumentCancel(pbIsConfirm){
        var tDataDocNo  = $('#oetTRMDocNo').val();
        if(pbIsConfirm){
            $.ajax({
                type    : "POST",
                url     : "docInvoiceRytAndMktFeeEventCancel",
                data    : {'tDataDocNo' : tDataDocNo},
                cache   : false,
                timeout : 0,
                success : function (tResult) {
                    $("#odvTRMPopupCancel").modal("hide");
                    $('.modal-backdrop').remove();
                    JSvTRMCallPageList();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        } else {
            $('#odvTRMPopupCancel').modal({backdrop:'static',keyboard:false});
            $("#odvTRMPopupCancel").modal("show");
        }
    }

    // อนุมัติ เอกสาร
    function JSxTRMDocumentApv(pbIsConfirm){
        let tDataDocNo  = $('#oetTRMDocNo').val();
        if(pbIsConfirm){
            $.ajax({
                type    : "POST",
                url     : "docInvoiceRytAndMktFeeEventAppove",
                data    : {'tDataDocNo' : tDataDocNo},
                cache   : false,
                timeout : 0,
                success : function (tResult) {
                    $("#odvTRMPopupApv").modal("hide");
                    $('.modal-backdrop').remove();
                    JSvTRMCallPageList();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        } else {
            $("#odvTRMPopupApv").modal('show');
        }
    }

    
</script>