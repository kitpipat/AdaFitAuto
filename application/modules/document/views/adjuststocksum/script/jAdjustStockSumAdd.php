
<script type="text/javascript">
nLangEdits = <?php echo $this->session->userdata("tLangEdit"); ?>;
tUsrApv = <?php echo $this->session->userdata("tSesUsername"); ?>;

// Disabled Enter in Form
$(document).keypress(
    function(event){
        if (event.which == '13') {
            event.preventDefault();
        }
    }
);

$(document).ready(function(){

    var nStaApv = parseInt($('#ohdAdjStkSumAjhStaApv').val());
    var nStaDoc = parseInt($('#ohdAdjStkSumAjhStaDoc').val());
    if(nStaApv == 1 || nStaDoc == 3){
        $('.xCNBtnBrowseAddOn').attr('disabled',true);
        $('.xWASTDisabledOnApv').attr('disabled',true);
        $('.xWASTReadOnlyOnApv').attr('readonly',true);
    }

    var nCountBch = "<?php echo $this->session->userdata("nSesUsrBchCount"); ?>";
    if(nCountBch == 1){
        $('#obtAdjStkSumBrowseBch').attr('disabled',true);
    }

    if(JCNbAdjStkSumIsUpdatePage()){
        // Doc No
        $("#oetAdjStkSumAjhDocNo").attr("readonly", true);
        $("#odvAdjStkSumSubAutoGenDocNoForm input").attr("disabled", true);
        $('#obtSMLoadDTStkSubToTemp').attr("disabled", true);
        JSxCMNVisibleComponent('#odvAdjStkSumSubAutoGenDocNoForm', false);

        JSxCMNVisibleComponent('#obtCardShiftOutBtnApv', true);
        JSxCMNVisibleComponent('#obtCardShiftOutBtnCancelApv', true);
        JSxCMNVisibleComponent('#obtCardShiftOutBtnDocMa', true);
    }

    if(JCNbAdjStkSumIsCreatePage()){
        // Doc No
        $("#oetAdjStkSumAjhDocNo").attr("disabled", true);
        $('#ocbAdjStkSumSubAutoGenCode').change(function(){
            if($('#ocbAdjStkSumSubAutoGenCode').is(':checked')) {
                $("#oetAdjStkSumAjhDocNo").attr("disabled", true);
                $('#odvAdjStkSumSubDocNoForm').removeClass('has-error');
                $('#odvAdjStkSumSubDocNoForm em').remove();
            }else{
                $("#oetAdjStkSumAjhDocNo").attr("disabled", false);
            }
        });
        JSxCMNVisibleComponent('#odvAdjStkSumSubAutoGenDocNoForm', true);

        JSxCMNVisibleComponent('#obtCardShiftOutBtnApv', false);
        JSxCMNVisibleComponent('#obtCardShiftOutBtnCancelApv', false);
        JSxCMNVisibleComponent('#obtCardShiftOutBtnDocMa', false);
    }
    JSvAdjStkSumLoadPdtDataTableHtml();
    console.log('JStCMNUserLevel: ', JStCMNUserLevel());
    // Condition control onload
    if(JStCMNUserLevel() == 'HQ'){
        // Init
        $('#obtAdjStkSumBrowseMch').attr('disabled', false);
        $('#obtAdjStkSumBrowseShp').attr('disabled', true);
        $('#obtAdjStkSumBrowsePos').attr('disabled', true);
        $('#obtAdjStkSumBrowseWah').attr('disabled', false);
    }

    if(JStCMNUserLevel() == 'BCH'){
        // Init
        $('#obtAdjStkSumBrowseMch').attr('disabled', false);
        $('#obtAdjStkSumBrowseShp').attr('disabled', true);
        $('#obtAdjStkSumBrowsePos').attr('disabled', true);
        $('#obtAdjStkSumBrowseWah').attr('disabled', false);
    }

    if(JStCMNUserLevel() == 'SHP'){
        // Init
        $('#obtAdjStkSumBrowseMch').attr('disabled', true);
        $('#obtAdjStkSumBrowseShp').attr('disabled', true);
        $('#obtAdjStkSumBrowsePos').attr('disabled', false);
        $('#obtAdjStkSumBrowseWah').attr('disabled', true);
    }

    $('#oliAdjStkSumMngPdtScan').click(function(){
        // Hide
        $('#oetAdjStkSumSearchPdtHTML').hide();
        $('#oimAdjStkSumMngPdtIconSearch').hide();
        // Show
        $('#oetAdjStkSumScanPdtHTML').show();
        $('#oimAdjStkSumMngPdtIconScan').show();
    });

    $('#oliAdjStkSumMngPdtSearch').click(function(){
        // Hide
        $('#oetAdjStkSumScanPdtHTML').hide();
        $('#oimAdjStkSumMngPdtIconScan').hide();
        // Show
        $('#oetAdjStkSumSearchPdtHTML').show();
        $('#oimAdjStkSumMngPdtIconSearch').show();
    });

    $('.selectpicker').selectpicker();

    $('.xCNDatePicker').datepicker({
        format: 'yyyy-mm-dd',
	autoclose: true,
        todayHighlight: true
    });

    $('.xCNTimePicker').datetimepicker({
        format: 'HH:mm:ss'
    });

    $('.xWTooltipsBT').tooltip({'placement': 'bottom'});
    $('[data-toggle="tooltip"]').tooltip({'placement': 'top'});
    
    var tCountType = $("#oetASTCountType").val();
            /// ??????????????????????????????????????????????????????????????????????????????
            if(tCountType == '1'){
            $("#odvAdjStkSumCircle").hide();
        }
        
        var dDatefrm = $("#oetASTDateFrm").val();
            var dDateto = $("#oetASTDateTo").val();
            var ddiffInMs   = new Date(dDateto) - new Date(dDatefrm)
            var tdiffInDays = ddiffInMs / (1000 * 60 * 60 * 24);
            var tName = "????????????????????????";
            $( ".xWRoundType" ).each(function( index ) {
                if($(this).data('date') == tdiffInDays){
                    tName = $(this).text();
                    $(this).prop("selected", true);
                    return false;
                }else{
                    $('.xWElseSattment').prop("selected", true);
                }  
            });
            $( "[data-id]" ).each(function( index ) {
                if($(this).attr('data-id') == 'oetASTTypeRound'){
                    $(this).find( ".filter-option-inner-inner" ).text(tName);
                }
            });
    

            // ================================ Event Date Function  ===============================

            $('#obtASTDateFrm').unbind().click(function(){
                $('#oetASTDateFrm').datepicker('show');
            });

            $('#obtASTDateTo').unbind().click(function(){
                $('#oetASTDateTo').datepicker('show');
            });
        // =====================================================================================

        // ================================== Date Change on change Type =================================
        $('#oetASTTypeRound').on('change', function() {
            var dDatefrm = $("#oetASTDateFrm").val();
            var dDateto = $("#oetASTDateTo").val();
            var ddiffInMs   = new Date(dDateto) - new Date(dDatefrm)
            var tdiffInDays = ddiffInMs / (1000 * 60 * 60 * 24);
            var dCurrentDate    = $.datepicker.formatDate('yy-mm-dd', new Date());
            if($(this).val() == 1){
                // $('#oetASTDateFrm').datepicker("setDate",dCurrentDate);
                var dnewDate = $.datepicker.formatDate('yy-mm-dd', new Date(Date.now()-0*24*60*60*1000));
                $('#oetASTDateFrm').datepicker("setDate",dnewDate);
                $('#oetASTDateTo').datepicker("setDate",dCurrentDate);
            }else if($(this).val() == 2){
                var dnewDate = $.datepicker.formatDate('yy-mm-dd', new Date(Date.now()-7*24*60*60*1000));
                $('#oetASTDateFrm').datepicker("setDate",dnewDate);
                $('#oetASTDateTo').datepicker("setDate",dCurrentDate);
            }else if($(this).val() == 3){
                var dnewDate = $.datepicker.formatDate('yy-mm-dd', new Date(Date.now()-30*24*60*60*1000));
                $('#oetASTDateFrm').datepicker("setDate",dnewDate);
                $('#oetASTDateTo').datepicker("setDate",dCurrentDate);
            }
        });
        // =====================================================================================

        // ================================== Change CountType =================================
        $('#oetASTCountType').on('change', function() {
            if($(this).val() == '1'){
                $("#odvAdjStkSumCircle").hide();
            }else{
                $("#odvAdjStkSumCircle").show();
            }
        });
        // =====================================================================================

        // ==================================  change Type Date Change on =================================
        $('.xWDateControl').on('change', function() {
            var dDatefrm = $("#oetASTDateFrm").val();
            var dDateto = $("#oetASTDateTo").val();
            if(dDatefrm > dDateto && dDateto != ''){
                $('#oetASTDateFrm').datepicker("setDate",dDateto);
                dDatefrm = dDateto;
            }
            var ddiffInMs   = new Date(dDateto) - new Date(dDatefrm)
            var tdiffInDays = ddiffInMs / (1000 * 60 * 60 * 24);
            var tName = "????????????????????????";
            $( ".xWRoundType" ).each(function( index ) {
                if($(this).data('date') == tdiffInDays){
                    tName = $(this).text();
                    $(this).prop("selected", true);
                    return false;
                }else{
                    $('.xWElseSattment').prop("selected", true);
                }  
            });
            $( "[data-id]" ).each(function( index ) {
                if($(this).attr('data-id') == 'oetASTTypeRound'){
                    $(this).find( ".filter-option-inner-inner" ).text(tName);
                }
            });
        });

        // =====================================================================================
        
        // ================================== Set Date Default =================================
            var dCurrentDate    = new Date();
            var tAmOrPm         = (dCurrentDate.getHours() < 12) ? "AM" : "PM";
            var tCurrentTime    = dCurrentDate.getHours() + ":" + dCurrentDate.getMinutes() + " " + tAmOrPm;

            if($('#oetASTDateFrm').val() == ''){
                $('#oetASTDateFrm').datepicker("setDate",dCurrentDate); 
            }

            if($('#oetASTDateTo').val()==''){
                $('#oetASTDateTo').datepicker("setDate",dCurrentDate); 
            }
          
        // =====================================================================================

});


/*========================= Begin Browse Options =============================*/

// ????????????
$('#obtAdjStkSumBrowseBch').click(function(){

    if($('.xWPdtItem').length>0){
        if(confirm("<?=language('document/adjuststocksum/adjuststocksum','tAdjStkSumMassesChange')?>")==true){
                    JSxDocSMClearPdtInTmp();
        }
    }

    // $(".modal.fade:not(#odvAdjStkSumBrowseShipAdd, #odvModalDOCPDT, #odvModalWanning)").remove();
    tOldBchCkChange = $("#oetBchCode").val();
    // Lang Edit In Browse
    nLangEdits = <?php echo $this->session->userdata("tLangEdit")?>;

    var tUsrLevel     = "<?php echo $this->session->userdata("tSesUsrLevel"); ?>";
    var tBchCodeMulti = "<?php echo $this->session->userdata("tSesUsrBchCodeMulti"); ?>";
    var tWhere = "";

    if(tUsrLevel != "HQ"){
        tWhere = " AND TCNMBranch.FTBchCode IN ("+tBchCodeMulti+") ";
    }else{  //????????????????????????????????????
        if($('#ohdAdjStkSumADCode').val() == '' || $('#ohdAdjStkSumADCode').val() == null){
            tWhere += "";
        }else{
            tWhere += " AND (TCNMBranch.FTAgnCode = " + $('#ohdAdjStkSumADCode').val() +" OR ISNULL(TCNMBranch.FTAgnCode,'') = '' )";
        }
    }


    // Option Branch
    oPmhBrowseBch = {
        Title: ['company/branch/branch', 'tBCHTitle'],
        Table: {Master:'TCNMBranch', PK:'FTBchCode'},
        Join: {
            Table: ['TCNMBranch_L'],
            On: ['TCNMBranch_L.FTBchCode = TCNMBranch.FTBchCode AND TCNMBranch_L.FNLngID = '+nLangEdits]
        },
        Where : {
                        Condition : [tWhere]
                    },
        GrideView:{
            ColumnPathLang: 'company/branch/branch',
            ColumnKeyLang: ['tBCHCode', 'tBCHName'],
            ColumnsSize: ['15%', '75%'],
            WidthModal: 50,
            DataColumns: ['TCNMBranch.FTBchCode', 'TCNMBranch_L.FTBchName'],
            DataColumnsFormat: ['', ''],
            DisabledColumns: [],
            Perpage: 5,
            OrderBy: ['TCNMBranch_L.FTBchName'],
            SourceOrder: "ASC"
        },
        CallBack:{
            ReturnType: 'S',
            Value: ["oetAdjStkSumBchCode", "TCNMBranch.FTBchCode"],
            Text: ["oetAdjStkSumBchName", "TCNMBranch_L.FTBchName"]
        },
        NextFunc:{
            FuncName: 'JSxAdjStkSumCallbackAfterSelectBch',
            ArgReturn: ['FTBchCode', 'FTBchName']
        },
        RouteFrom: 'promotion',
        RouteAddNew: 'branch',
        BrowseLev: 2
    };
    // Option Branch
    JCNxBrowseData('oPmhBrowseBch');

});

// ????????????????????????????????????
$('#obtAdjStkSumBrowseMch').click(function(){
    // $(".modal.fade:not(#odvAdjStkSumBrowseShipAdd, #odvModalDOCPDT, #odvModalWanning)").remove();
    tOldMchCkChange = $("#oetMchCode").val();
    // Option merchant
    oAdjStkSumBrowseMch = {
        Title: ['company/warehouse/warehouse', 'tWAHBwsMchTitle'],
        Table: {Master:'TCNMMerchant', PK:'FTMerCode'},
        Join: {
            Table: ['TCNMMerchant_L'],
            On: ['TCNMMerchant.FTMerCode = TCNMMerchant_L.FTMerCode AND TCNMMerchant_L.FNLngID = '+nLangEdits]
        },
        Where: {
            Condition: ["AND (SELECT COUNT(FTShpCode) FROM TCNMShop WHERE TCNMShop.FTMerCode = TCNMMerchant.FTMerCode AND TCNMShop.FTBchCode = '"+$("#ohdAdjStkSumBchCode").val()+"') != 0"]
        },
        GrideView: {
            ColumnPathLang: 'company/warehouse/warehouse',
            ColumnKeyLang: ['tWAHBwsMchCode', 'tWAHBwsMchNme'],
            ColumnsSize: ['15%', '75%'],
            WidthModal: 50,
            DataColumns: ['TCNMMerchant.FTMerCode', 'TCNMMerchant_L.FTMerName'],
            DataColumnsFormat: ['',''],
            Perpage: 5,
            OrderBy: ['TCNMMerchant.FTMerCode'],
            SourceOrder: "ASC"
        },
        CallBack:{
            ReturnType: 'S',
            Value: ["oetAdjStkSumMchCode", "TCNMMerchant.FTMerCode"],
            Text: ["oetAdjStkSumMchName", "TCNMMerchant_L.FTMerName"]
        },
        NextFunc:{
            FuncName:'JSxAdjStkSumCallbackAfterSelectMer',
            ArgReturn:['FTMerCode', 'FTMerName']
        },
        BrowseLev: 1
    };
    // Option merchant
    JCNxBrowseData('oAdjStkSumBrowseMch');
});

// ?????????????????????
$('#obtAdjStkSumBrowseShp').click(function(){
    console.log('Mer: ', $("#oetAdjStkSumMchCode").val());
    // $(".modal.fade:not(#odvAdjStkSumBrowseShipAdd, #odvModalDOCPDT, #odvModalWanning)").remove();
    // Option Shop
    oAdjStkSumBrowseShp = {
        Title : ['company/shop/shop', 'tSHPTitle'],
        Table:{Master: 'TCNMShop', PK: 'FTShpCode'},
        Join :{
            Table: ['TCNMShop_L', 'TCNMWaHouse_L'],
            On: ['TCNMShop_L.FTShpCode = TCNMShop.FTShpCode AND TCNMShop.FTBchCode = TCNMShop_L.FTBchCode AND TCNMShop_L.FNLngID = '+nLangEdits,
                'TCNMShop.FTWahCode = TCNMWaHouse_L.FTWahCode AND TCNMWaHouse_L.FNLngID= '+nLangEdits
            ]
        },
        Where:{
            Condition : [
                function(){
                    var tSQL = "AND TCNMShop.FTBchCode = '"+$("#ohdAdjStkSumBchCode").val()+"' AND TCNMShop.FTMerCode = '"+$("#oetAdjStkSumMchCode").val()+"'";
                    return tSQL;
                }
            ]
        },
        GrideView: {
            ColumnPathLang: 'company/branch/branch',
            ColumnKeyLang: ['tBCHCode', 'tBCHName'],
            ColumnsSize: ['25%', '75%'],
            WidthModal: 50,
            DataColumns: ['TCNMShop.FTShpCode', 'TCNMShop_L.FTShpName', 'TCNMShop.FTWahCode', 'TCNMWaHouse_L.FTWahName', 'TCNMShop.FTShpType', 'TCNMShop.FTBchCode'],
            DataColumnsFormat: ['', '', '', '', '', ''],
            DisabledColumns:[2, 3, 4, 5],
            Perpage: 5,
            OrderBy: ['TCNMShop_L.FTShpName'],
            SourceOrder: "ASC"
        },
        CallBack: {
            ReturnType: 'S',
            Value: ["oetAdjStkSumShpCode", "TCNMShop.FTShpCode"],
            Text: ["oetAdjStkSumShpName", "TCNMShop_L.FTShpName"]
        },
        NextFunc: {
            FuncName: 'JSxAdjStkSumCallbackAfterSelectShp',
            ArgReturn: ['FTBchCode', 'FTShpCode', 'FTShpType', 'FTWahCode', 'FTWahName']
        },
        BrowseLev: 1
    };
    // Option Shop
    JCNxBrowseData('oAdjStkSumBrowseShp');
});

// ???????????????????????????????????????
$('#obtAdjStkSumBrowsePos').click(function(){
    // $(".modal.fade:not(#odvAdjStkSumBrowseShipAdd, #odvModalDOCPDT, #odvModalWanning)").remove();
    // Option Shop
    oAdjStkSumBrowsePos = {
        Title: ['pos/posshop/posshop', 'tPshTBPosCode'],
        Table: { Master:'TVDMPosShop', PK:'FTPosCode' },
        Join: {
            Table: ['TCNMPos', 'TCNMPosLastNo', 'TCNMWaHouse', 'TCNMWaHouse_L'],
            On:['TVDMPosShop.FTPosCode = TCNMPos.FTPosCode',
                'TVDMPosShop.FTPosCode = TCNMPosLastNo.FTPosCode',
                'TVDMPosShop.FTPosCode = TCNMWaHouse.FTWahRefCode AND TCNMWaHouse.FTWahStaType = 6',
                'TCNMWaHouse.FTWahCode = TCNMWaHouse_L.FTWahCode AND TCNMWaHouse_L.FNLngID= '+nLangEdits
            ]
        },
        Where: {
            Condition: [
                function(){
                    var tSQL = "AND TVDMPosShop.FTBchCode = '"+$("#ohdAdjStkSumBchCode").val()+"' AND TVDMPosShop.FTShpCode = '"+$("#oetAdjStkSumShpCode").val()+"'";
                    /*if($("#oetShpCodeEnd").val()!=""){
                        if($("#oetShpCodeStart").val()==$("#oetShpCodeEnd").val()){
                            if($("#oetPosCodeEnd").val()!=""){
                                tSQL += " AND TVDMPosShop.FTPosCode != '"+$("#oetPosCodeEnd").val()+"'";
                            }
                        }
                    }*/
                    return tSQL;
                }
            ]
        },
        GrideView: {
            ColumnPathLang: 'pos/posshop/posshop',
            ColumnKeyLang: ['tPshBRWShopTBCode', 'tPshBRWPosTBName'],
            ColumnsSize: ['25%', '75%'],
            WidthModal: 50,
            DataColumns: ['TVDMPosShop.FTPosCode', 'TCNMPosLastNo.FTPosComName', 'TVDMPosShop.FTShpCode', 'TVDMPosShop.FTBchCode', 'TCNMWaHouse.FTWahCode', 'TCNMWaHouse_L.FTWahName'],
            DataColumnsFormat : ['', '', '', '', '', ''],
            DisabledColumns: [2, 3, 4, 5],
            Perpage: 5,
            OrderBy: ['TVDMPosShop.FTPosCode'],
            SourceOrder: "ASC"
        },
        CallBack: {
            ReturnType: 'S',
            Value: ["oetAdjStkSumPosCode", "TVDMPosShop.FTPosCode"],
            Text: ["oetAdjStkSumPosName", "TCNMPosLastNo.FTPosComName"]
        },
        NextFunc: {
            FuncName: 'JSxAdjStkSumCallbackAfterSelectPos',
            ArgReturn: ['FTBchCode', 'FTShpCode', 'FTPosCode', 'FTWahCode', 'FTWahName']
        },
        BrowseLev: 1

    };
    // Option Shop
    JCNxBrowseData('oAdjStkSumBrowsePos');
});

// ??????????????????????????????
$('#obtAdjStkSumBrowseWah').click(function(){


    if($('.xWPdtItem').length>0){
        if(confirm('<?=language('document/adjuststocksum/adjuststocksum','tAdjStkSumMassesChange')?>')==true){
                    JSxDocSMClearPdtInTmp();
        }
    }


    // $(".modal.fade:not(#odvAdjStkSumBrowseShipAdd, #odvModalDOCPDT, #odvModalWanning)").remove();
    // Option WareHouse
    oAdjStkSumBrowseWah = {
        Title: ['company/warehouse/warehouse', 'tWAHTitle'],
        Table: { Master:'TCNMWaHouse', PK:'FTWahCode'},
        Join: {
            Table: ['TCNMWaHouse_L'],
            On:['TCNMWaHouse_L.FTWahCode = TCNMWaHouse.FTWahCode AND TCNMWaHouse_L.FTBchCode = TCNMWaHouse.FTBchCode AND TCNMWaHouse_L.FNLngID = '+nLangEdits]
        },
        Where: {
            Condition: [
                function(){
                    var tSQL = "";
                    // if( /*($("#oetAdjStkSumMchCode").val() == '') &&*/ ($("#oetAdjStkSumShpCode").val() == '') && ($("#oetAdjStkSumPosCode").val() == '') ){ // Branch Wah
                        // tSQL += " AND TCNMWaHouse.FTWahStaType IN (1,2,5)";
                        // tSQL += " AND TCNMWaHouse.FTWahRefCode = '"+$("#ohdAdjStkSumBchCode").val()+"'";
                    // }
                    // if($('#oetAdjStkSumBchCode').val()!=''){
                    //     tSQL += " AND TCNMWaHouse.FTBchCode='"+$('#oetAdjStkSumBchCode').val()+"'  AND TCNMWaHouse.FTWahRefCode = '"+$('#oetAdjStkSumBchCode').val()+"'";
                    // }
                    // if( ($("#oetAdjStkSumShpCode").val() != '') && ($("#oetAdjStkSumPosCode").val() == '') ){ // Shop Wah
                    //     tSQL += " AND TCNMWaHouse.FTWahStaType IN (4)";
                    //     tSQL += " AND TCNMWaHouse.FTWahRefCode = '"+$('#oetAdjStkSumShpCode').val()+"'";
                    // }

                    // if( ($("#oetAdjStkSumShpCode").val() != '') && ($("#oetAdjStkSumPosCode").val() != '') ){ // Pos(vending) Wah
                    //     tSQL += " AND TCNMWaHouse.FTWahStaType IN (6)";
                    //     tSQL += " AND TCNMWaHouse.FTWahRefCode = '"+$('#oetAdjStkSumPosCode').val()+"'";
                    // }
                    // console.log(tSQL);
                    tSQL += " AND TCNMWaHouse.FTBchCode = '"+$('#oetAdjStkSumBchCode').val()+"' ";
                    return tSQL;
                }
            ]
        },
        GrideView:{
            ColumnPathLang: 'company/warehouse/warehouse',
            ColumnKeyLang: ['tWahCode','tWahName'],
            DataColumns: ['TCNMWaHouse.FTWahCode','TCNMWaHouse_L.FTWahName'],
            DataColumnsFormat: ['',''],
            ColumnsSize: ['15%','75%'],
            Perpage: 5,
            WidthModal: 50,
            OrderBy: ['TCNMWaHouse_L.FTWahName'],
            SourceOrder: "ASC"
        },
        CallBack:{
            ReturnType: 'S',
            Value: ["oetAdjStkSumWahCode","TCNMWaHouse.FTWahCode"],
            Text: ["oetAdjStkSumWahName","TCNMWaHouse_L.FTWahName"]
        },

        RouteAddNew: 'warehouse',
        BrowseLev: nStaAdjStkSumBrowseType
    };
    // Option WareHouse
    JCNxBrowseData('oAdjStkSumBrowseWah');
});

// ??????????????????
$('#obtAdjStkSumBrowseReason').click(function(){
    // $(".modal.fade:not(#odvAdjStkSumBrowseShipAdd, #odvModalDOCPDT, #odvModalWanning)").remove();
    // Option WareHouse
    oAdjStkSumBrowseReason = {
            Title: ['other/reason/reason', 'tRSNTitle'],
            Table: { Master:'TCNMRsn', PK:'FTRsnCode' },
            Join: {
                Table: ['TCNMRsn_L'],
                On: ['TCNMRsn_L.FTRsnCode = TCNMRsn.FTRsnCode AND TCNMRsn_L.FNLngID = '+nLangEdits]
            },
            Where: {
                Condition : ["AND TCNMRsn.FTRsgCode = '008' "]
            },
            GrideView:{
                ColumnPathLang: 'other/reason/reason',
                ColumnKeyLang: ['tRSNTBCode', 'tRSNTBName'],
                // ColumnsSize: ['15%', '85%'],
                WidthModal: 50,
                DataColumns: ['TCNMRsn.FTRsnCode', 'TCNMRsn_L.FTRsnName'],
                DisabledColumns: [],
                DataColumnsFormat: ['', ''],
                Perpage: 5,
                OrderBy: ['TCNMRsn_L.FTRsnName'],
                SourceOrder: "ASC"
            },
            CallBack:{
                ReturnType: 'S',
                Value: ["oetAdjStkSumReasonCode", "TCNMRsn.FTRsnCode"],
                Text: ["oetAdjStkSumReasonName", "TCNMRsn_L.FTRsnName"]
            },
            /*NextFunc:{
                FuncName:'JSxCSTAddSetAreaCode',
                ArgReturn:['FTRsnCode']
            },*/
            // RouteFrom : 'cardShiftChange',
            RouteAddNew : 'reason',
            BrowseLev : nStaAdjStkSumBrowseType
    };
    // Option WareHouse
    JCNxBrowseData('oAdjStkSumBrowseReason');
});

/*=========================== End Browse Options =============================*/

/*=================== Begin Callback Browse ==================================*/
/**
 * ????????????
 * Functionality : Process after shoose branch
 * Parameters : -
 * Creator : 22/05/2019 piya(tiger)
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxAdjStkSumCallbackAfterSelectBch(poJsonData) {

    if (poJsonData != "NULL") {
        aData = JSON.parse(poJsonData);
        tAddBch = aData[0];
        tAddSeqNo = aData[1];
    }

    $('#oetAdjStkSumWahCode').val('');
    $('#oetAdjStkSumWahName').val('');


    // var tBchCode = $('#ohdAdjStkSumBchCode').val();
    // var tMchName = $('#oetAdjStkSumMchName').val();
    // var tShpName = $('#oetAdjStkSumShpName').val();
    // var tPosName = $('#oetAdjStkSumPosName').val();
    // var tWahName = $('#oetAdjStkSumWahName').val();

    // $('#obtAdjStkSumBrowseMch').attr('disabled', true);
    // $('#obtAdjStkSumBrowseShp').attr('disabled', true);
    // $('#obtAdjStkSumBrowsePos').attr('disabled', true);
    // $('#obtAdjStkSumBrowseWah').attr('disabled', true);
}

/**
 * ????????????????????????????????????
 * Functionality : Process after shoose merchant
 * Parameters : -
 * Creator : 22/05/2019 piya(tiger)
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxAdjStkSumCallbackAfterSelectMer(poJsonData) {

    if (poJsonData != "NULL") {
        aData = JSON.parse(poJsonData);
        tAddBch = aData[0];
        tAddSeqNo = aData[1];
    }

    var tBchCode = $('#ohdAdjStkSumBchCode').val();
    var tMchName = $('#oetAdjStkSumMchName').val();
    var tShpName = $('#oetAdjStkSumShpName').val();
    var tPosName = $('#oetAdjStkSumPosName').val();
    var tWahName = $('#oetAdjStkSumWahName').val();

    $('#obtAdjStkSumBrowseShp').attr('disabled', true);
    $('#obtAdjStkSumBrowsePos').attr('disabled', true);
    $('#obtAdjStkSumBrowseWah').attr('disabled', true);

    if(JStCMNUserLevel() == 'HQ' || JStCMNUserLevel() == 'BCH'){
        if(tMchName != ''){
            $('#obtAdjStkSumBrowseShp').attr('disabled', false);
            $('#obtAdjStkSumBrowseWah').attr('disabled', true);
        }else{
            $('#obtAdjStkSumBrowseWah').attr('disabled', false);
        }
        $('#oetAdjStkSumShpCode, #oetAdjStkSumShpName').val('');
        $('#oetAdjStkSumPosCode, #oetAdjStkSumPosName').val('');
        $('#oetAdjStkSumWahCode, #oetAdjStkSumWahName').val('');
    }
}

/**
 * ?????????????????????
 * Functionality : Process after shoose shop
 * Parameters : -
 * Creator : 22/05/2019 piya(tiger)
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxAdjStkSumCallbackAfterSelectShp(poJsonData) {

    var aData, tResAddBch, tResAddSeqNo, tResWahCode, tResWahName;
    if (poJsonData != "NULL") {
        aData = JSON.parse(poJsonData);
        tResAddBch = aData[0];
        tResAddSeqNo = aData[1];
        tResWahCode = aData[3];
        tResWahName = aData[4];
    }else{
        $('#oetAdjStkSumWahCode, #oetAdjStkSumWahName').val('');
    }
    console.log('aData: ', aData);
    $('#ohdAdjStkSumWahCodeInShp').val(tResWahCode);
    $('#ohdAdjStkSumWahNameInShp').val(tResWahName);
    var tBchCode = $('#ohdAdjStkSumBchCode').val();
    var tMchName = $('#oetAdjStkSumMchName').val();
    var tShpName = $('#oetAdjStkSumShpName').val();
    var tPosName = $('#oetAdjStkSumPosName').val();
    var tWahName = $('#oetAdjStkSumWahName').val();

    $('#obtAdjStkSumBrowsePos').attr('disabled', true);
    $('#obtAdjStkSumBrowseWah').attr('disabled', false);

    if(JStCMNUserLevel() == 'HQ' || JStCMNUserLevel() == 'BCH'){
        if(tShpName != ''){
            $('#obtAdjStkSumBrowsePos').attr('disabled', false);
            $('#obtAdjStkSumBrowseWah').attr('disabled', true);
            $('#oetAdjStkSumWahCode').val(tResWahCode);
            $('#oetAdjStkSumWahName').val(tResWahName);
        }else{
            $('#oetAdjStkSumWahCode, #oetAdjStkSumWahName').val('');
        }
        $('#oetAdjStkSumPosCode, #oetAdjStkSumPosName').val('');
    }
}

/**
 * ???????????????????????????????????????
 * Functionality : Process after shoose pos
 * Parameters : -
 * Creator : 22/05/2019 piya(tiger)
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxAdjStkSumCallbackAfterSelectPos(poJsonData) {

    var aData, tResAddBch, tResAddSeqNo, tResWahCode, tResWahName;
    if (poJsonData != "NULL") {
        aData = JSON.parse(poJsonData);
        tResAddBch = aData[0];
        tResAddSeqNo = aData[1];
        tResWahCode = aData[3];
        tResWahName = aData[4];
    }else{
        $('#oetAdjStkSumPosCode, #oetAdjStkSumPosName').val('');
        $('#oetAdjStkSumWahCode').val($('#ohdAdjStkSumWahCodeInShp').val());
        $('#oetAdjStkSumWahName').val($('#ohdAdjStkSumWahNameInShp').val());
        return;
    }
    console.log('aData Pos: ', aData);

    var tBchCode = $('#ohdAdjStkSumBchCode').val();
    var tMchName = $('#oetAdjStkSumMchName').val();
    var tShpName = $('#oetAdjStkSumShpName').val();
    var tPosName = $('#oetAdjStkSumPosName').val();
    var tWahName = $('#oetAdjStkSumWahName').val();

    $('#obtAdjStkSumBrowseWah').attr('disabled', false);

    if(JStCMNUserLevel() == 'HQ' || JStCMNUserLevel() == 'BCH' || JStCMNUserLevel() == 'SHP'){
        if(tPosName != ''){
            $('#obtAdjStkSumBrowseWah').attr('disabled', true);
            $('#oetAdjStkSumWahCode').val(tResWahCode);
            $('#oetAdjStkSumWahName').val(tResWahName);
        }
    }
}

/**
 * ??????????????????????????????
 * Functionality : Process after shoose warehouse
 * Parameters : -
 * Creator : 22/05/2019 piya(tiger)
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxAdjStkSumCallbackAfterSelectWah(poJsonData) {

    if (poJsonData != "NULL") {
        aData = JSON.parse(poJsonData);
        tAddBch = aData[0];
        tAddSeqNo = aData[1];
    }

}


function JSxAdjStkSumAddPdtInRow(poJsonData){
    for (var n = 0; n < poJsonData.length; n++) {

        var tdVal = $('.nItem'+n).data('otrval')

        if((tdVal != '') && (typeof tdVal == 'undefined')){

            nTRID = JCNnRandomInteger(100, 1000000);

            var aColDatas = JSON.parse(poJsonData[n]);
            var tPdtCode = aColDatas[0];
            var tPunCode = aColDatas[1];
            FSvAdjStkSumAddPdtIntoTableDT(tPdtCode, tPunCode);

        }
    }
}




/**
* Functionality : Load StockSub DT
* Parameters : -
* Creator : 16/07/2020 nale
* Last Modified : -
* Return : Approve status
* Return Type : boolean
*/
function JSxDocSMLoadDTStkSubToTemp(){




   var ptXthDocNo = $("#oetAdjStkSumAjhDocNo").val();
   var ptBchCode = $("#oetAdjStkSumBchCode").val();
   var ptWahCode = $("#oetAdjStkSumWahCode").val();
   var ptCountType = $("#oetASTCountType").val();

    if($('#oetAdjStkSumBchName').val()==''){
       var tMsgBody = '???????????????????????????????????????';
        FSvCMNSetMsgWarningDialog(tMsgBody);
        return false;
    }

    if(ptWahCode==''){
       var tMsgBody = '????????????????????????????????????';
        FSvCMNSetMsgWarningDialog(tMsgBody);
        return false;
    }

    JCNxOpenLoading();

    $.ajax({
        type: "POST",
        url: "docSMEventCallPdtStkSum",
        data: {
            ptXthDocNo: ptXthDocNo,
            ptBchCode: ptBchCode,
            ptWahCode: ptWahCode,
            ptCountType: ptCountType
        },
        cache: false,
        timeout: 5000,
        success: function (tResult) {
            oResult = JSON.parse(tResult);
            var tMarkUp = '';
            for(var nI = 0 ; nI < oResult.length ; nI++){
                tMarkUp +="<input  type='hidden' name='ohdSMXtdDocNoRef[]' value='"+oResult[nI].FTAjhDocNo+"'>";
            }
            $('#ohddocSMDocRef').html(tMarkUp);
            JSvAdjStkSumLoadPdtDataTableHtml();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            JCNxResponseError(jqXHR, textStatus, errorThrown);
        }
    });


}

/**
* Functionality : Check Approve
* Parameters : -
* Creator : 24/05/2019 piya(tiger)
* Last Modified : -
* Return : Approve status
* Return Type : boolean
*/
function JSbAdjStkSumIsApv(){
    var bStatus = false;
    if(($("#ohdAdjStkSumAjhStaApv").val() == "1") || ($("#ohdAdjStkSumAjhStaApv").val() == "2")){
        bStatus = true;
    }
    return bStatus;
}

/**
* Functionality : Check Approve
* Parameters : -
* Creator : 24/05/2019 piya(tiger)
* Last Modified : -
* Return : Approve status
* Return Type : boolean
*/
function JSbAdjStkSumIsStaPrcStk(){
    var bStatus = false;
    if($("#ohdAdjStkSumAjhStaPrcStk").val() == "1"){
        bStatus = true;
    }
    return bStatus;
}

/**
* Functionality : Check document status
* Parameters : ptStaType is ("complete", "incomplete", "cancel")
* Creator : 24/05/2019 piya(tiger)
* Last Modified : -
* Return : Document status
* Return Type : boolean
*/
function JSbAdjStkSumIsStaDoc(ptStaType){
    var bStatus = false;
    if(ptStaType == "complete"){
        if($("#ohdAdjStkSumAjhStaDoc").val() == "1"){
            bStatus = true;
        }
        return bStatus;
    }
    if(ptStaType == "incomplete"){
        if($("#ohdAdjStkSumAjhStaDoc").val() == "2"){
            bStatus = true;
        }
        return bStatus;
    }
    if(ptStaType == "cancel"){
        if($("#ohdAdjStkSumAjhStaDoc").val() == "3"){
            bStatus = true;
        }
        return bStatus;
    }
    return bStatus;
}

/*============================= Begin Custom Form Validate ===================*/

var bUniqueAdjStkSumCode;
$.validator.addMethod(
    "uniqueAdjStkSumCode",
    function(tValue, oElement, aParams) {
        var tAdjStkSumCode = tValue;
        $.ajax({
            type: "POST",
            url: "adjStkSumUniqueValidate/docAdjStkSumCode",
            data: "tAdjStkSumCode=" + tAdjStkSumCode,
            dataType:"html",
            success: function(ptMsg)
            {
                // If vatrate and vat start exists, set response to true
                bUniqueAdjStkSumCode = (ptMsg == 'true') ? false : true;
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Custom validate uniqueAdjStkSumCode: ', jqXHR, textStatus, errorThrown);
            },
            async: false
        });
        return bUniqueAdjStkSumCode;
    },
    "Adjust Stock Doc Code is Already Taken"
);

// Override Error Message
jQuery.extend(jQuery.validator.messages, {
    required: "This field is required.",
    remote: "Please fix this field.",
    email: "Please enter a valid email address.",
    url: "Please enter a valid URL.",
    date: "Please enter a valid date.",
    dateISO: "Please enter a valid date (ISO).",
    number: "Please enter a valid number.",
    digits: "Please enter only digits.",
    creditcard: "Please enter a valid credit card number.",
    equalTo: "Please enter the same value again.",
    accept: "Please enter a value with a valid extension.",
    maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
    minlength: jQuery.validator.format("Please enter at least {0} characters."),
    rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
    range: jQuery.validator.format("Please enter a value between {0} and {1}."),
    max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
    min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
});

/*============================= End Custom Form Validate =====================*/

/**
* Functionality : Form validate
* Parameters : -
* Creator : 24/05/2019 piya(tiger)
* Last Modified : -
* Return : -
* Return Type : -
*/
function JSxValidateFormAddAdjStkSum() {
    $('#ofmAddAdjStkSum').validate({
        focusInvalid: true,
        onclick: false,
        onfocusout: false,
        onkeyup: false,
        rules: {
            oetAdjStkSumAjhDocNo: {
                required: true,
                maxlength: 20,
                uniqueAdjStkSumCode: JCNbAdjStkSumIsCreatePage()
            },
            oetAdjStkSumAjhDocDate: {
                required: true
            },
            oetAdjStkSumAjhDocTime: {
                required: true
            },
            oetAdjStkSumWahCode: {
                required: true
            },
            oetAdjStkSumWahName: {
                required: true
            },
            oetAdjStkSumReasonCode: {
                required: true
            },
            oetAdjStkSumReasonName: {
                required: true
            }
        },
        messages: {
            oetAdjStkSumAjhDocNo: {
                "required": $('#oetAdjStkSumAjhDocNo').attr('data-validate-required')
            }
        },
        errorElement: "em",
        errorPlacement: function (error, element) {
            error.addClass("help-block");
            if (element.prop("type") === "checkbox") {
                error.appendTo(element.parent("label"));
            } else {
                var tCheck = $(element.closest('.form-group')).find('.help-block').length;
                if (tCheck == 0) {
                    error.appendTo(element.closest('.form-group')).trigger('change');
                }
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).closest('.form-group').addClass("has-error");
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).closest('.form-group').removeClass("has-error");
        },
        invalidHandler: function(event, validator) {
            if ($("#oetAdjStkSumReasonName").val() == '' || $("#oetAdjStkSumWahName").val() == '') {
                FSvCMNSetMsgWarningDialog("<p>????????????????????????????????????????????????????????????????????????</p>");
            }
        },
        submitHandler: function (form) {
            JSxAdjStkSumAddUpdateAction();
        }
    });
}

/**
 * Functionality : Add or Update
 * Parameters : route
 * Creator : 23/05/2019 Piya(Tiger)
 * Update : -
 * Return : -
 * Return Type : -
 */
function JSxAdjStkSumAddUpdateAction() {

    $( ".xWASTDisabledOnApv" ).prop( "disabled", false );
    if($('.xCNPdtEditInLine').length>0){

        $.ajax({
            type: "POST",
            url: '<?php echo $tRoute; ?>',
            data: $("#ofmAddAdjStkSum").serialize(),
            cache: false,
            timeout: 0,
            success: function (tResult) {

                if (nStaAdjStkSumBrowseType != 1) {
                    var aReturn = JSON.parse(tResult);
                    if (aReturn["nStaEvent"] == 1) {
                        if (
                                aReturn["nStaCallBack"] == "1" ||
                                aReturn["nStaCallBack"] == null
                                ) {
                            JSvCallPageAdjStkSumEdit(aReturn["tCodeReturn"]);
                        } else if (aReturn["nStaCallBack"] == "2") {
                            JSvCallPageAdjStkSumAdd();
                        } else if (aReturn["nStaCallBack"] == "3") {
                            JSvCallPageAdjStkSumList();
                        }
                    } else {
                        tMsgBody = aReturn["tStaMessg"];
                        FSvCMNSetMsgWarningDialog(tMsgBody);
                    }
                } else {
                    JCNxBrowseData(tCallAdjStkSumBackOption);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });

    }else{
        FSvCMNSetMsgWarningDialog('?????????????????????????????????????????????????????????????????????????????????');
    }
}

//Print
function JSxAdjStkSumPrintDoc(){
    var aInfor = [
        { "Lang"        : '<?=FCNaHGetLangEdit(); ?>'},
        { "ComCode"     : '<?=FCNtGetCompanyCode(); ?>'},
        { "BranchCode"  : '<?=FCNtGetAddressBranch($tUserBchCode); ?>' },
        { "DocCode"     : $("#oetAdjStkSumAjhDocNo").val() }, // ????????????????????????????????????
        { "DocBchCode"  : '<?=$tUserBchCode;?>' }
    ];
    window.open('<?=base_url()?>' + "formreport/Frm_SQL_ALLMPdtBillSumChkStk?infor=" + JCNtEnCodeUrlParameter(aInfor), '_blank');
}

    //////////////////////////////////////////////////////////////// ?????????????????????????????????????????? ////////////////////////////////////////////////////////////
    $('#obtAdjStkSumBrowseAgency').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxCheckPinMenuClose();
            window.oASTBrowseAgnOption   = undefined;
            oASTBrowseAgnOption          = oAgnOption({
                'tReturnInputCode'  : 'ohdAdjStkSumADCode',
                'tReturnInputName'  : 'ohdAdjStkSumADName',
                'tNextFuncName'     : 'JSxIVSetConditionAfterSelectAGN'
            });
            JCNxBrowseData('oASTBrowseAgnOption');
        }else{
            JCNxShowMsgSessionExpired();
        }
    });

    var oAgnOption      = function(poDataFnc){
        var tInputReturnCode    = poDataFnc.tReturnInputCode;
        var tInputReturnName    = poDataFnc.tReturnInputName;
        var tNextFuncName       = poDataFnc.tNextFuncName;
        var tUsrLevSession      = '<?=$this->session->userdata("tSesUsrLevel"); ?>';
        var tWhereAgn           = '';

        var oOptionReturn  = {
            Title   : ['company/branch/branch', 'tBchAgnTitle'],
            Table   : {Master:'TCNMAgency', PK:'FTAgnCode'},
            Join    : {
                Table   : ['TCNMAgency_L'],
                On      : [' TCNMAgency.FTAgnCode = TCNMAgency_L.FTAgnCode AND TCNMAgency_L.FNLngID = '+nLangEdits]
            },
            Where   : {
                Condition : [tWhereAgn]
            },
            GrideView:{
                ColumnPathLang	: 'company/branch/branch',
                ColumnKeyLang	: ['tBchAgnCode', 'tBchAgnName'],
                ColumnsSize     : ['15%', '85%'],
                WidthModal      : 50,
                DataColumns		: ['TCNMAgency.FTAgnCode', 'TCNMAgency_L.FTAgnName'],
                DataColumnsFormat : ['', ''],
                Perpage			: 10,
                OrderBy			: ['TCNMAgency.FDCreateOn DESC'],
            },
            CallBack:{
                ReturnType      : 'S',
                Value           : [tInputReturnCode, "TCNMAgency.FTAgnCode"],
                Text            : [tInputReturnName, "TCNMAgency_L.FTAgnName"]
            },
            NextFunc:{
                FuncName            : tNextFuncName,
                ArgReturn           : ['FTAgnCode']
            }
        };
        return oOptionReturn;
    }

    //????????????????????????????????????
    function JSxIVSetConditionAfterSelectAGN(poDataNextFunc){
        var aData;
        if (poDataNextFunc  != "NULL") {
            $('#ohdAdjStkSumBchCode , #oetAdjStkSumBchCode').val('');
        }
    }
</script>
