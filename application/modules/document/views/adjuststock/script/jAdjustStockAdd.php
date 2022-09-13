<script type="text/javascript">
    var nLangEdits      = '<?php echo $this->session->userdata("tLangEdit");?>';
    var tUsrApv         = '<?php echo $this->session->userdata("tSesUsername");?>';
    var tSesUsrLevel    = '<?php echo $this->session->userdata('tSesUsrLevel');?>';
    var tUserBchCode    = '<?php echo $this->session->userdata("tSesUsrBchCodeDefault");?>';
    var tUserBchName    = '<?php echo $this->session->userdata("tSesUsrBchNameDefault");?>';
    var tUserWahCode    = '<?php echo $this->session->userdata("tSesUsrWahCode");?>';
    var tUserWahName    = '<?php echo $this->session->userdata("tSesUsrWahName");?>';
    var tRoute          =  $('#ohdASTRoute').val();

    $(document).ready(function(){

        var tUsrLevel       = "<?php echo $this->session->userdata("tSesUsrLevel"); ?>";
        var tBchCodeMulti   = "<?php echo $this->session->userdata("tSesUsrBchCodeMulti"); ?>";
        var nCountBch       = parseInt(<?php echo $this->session->userdata("nSesUsrBchCount"); ?>);
        var tWhere          = "";
        var tCountType      = $("#oetASTCountType").val();

        /// เช็คประเภทและเปลี่ยนตามรอบ
        if(tCountType == '1'){
            $("#odvASTCircle").hide();
        }
        
        var dDatefrm    = $("#oetASTDateFrm").val();
        var dDateto     = $("#oetASTDateTo").val();
        var ddiffInMs   = new Date(dDateto) - new Date(dDatefrm)
        var tdiffInDays = ddiffInMs / (1000 * 60 * 60 * 24);
        var tName       = "กำหนดเอง";
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
    
        // ถ้า กลุ่มร้านค้า ว่างให้ปิดปุ่มร้านค้า
        if( $('#oetASTMerCode').val() == "" ){
            $('#obtASTBrowseShp').attr('disabled',true);
        }

        // ถ้าร้านค้า ว่างให้ปิดปุ่มเครื่องจุดขาย
        if( $('#oetASTShopCode').val() == "" ){
            $('#obtASTBrowsePos').attr('disabled',true);
        }

        if(nCountBch == 1){
            $('#obtBrowseASTBCH').attr('disabled',true);
            $('#obtBrowseASTBCH').attr("disabled","disabled");
        }

        if(tUserBchCode != '' && tRoute == 'dcmASTEventAdd'){
            $('#oetASTBchCode').val(tUserBchCode);
            $('#oetASTBchName').val(tUserBchName);
            // $('#obtBrowseASTBCH').attr("disabled","disabled");
        }
        if(tUserWahCode != '' && tRoute == 'dcmASTEventAdd'){
            $('#oetASTWahCode').val(tUserWahCode);
            $('#oetASTWahName').val(tUserWahName);
        }

        if( tUsrLevel == "SHP" ){
            $('#obtASTBrowseMer').attr('disabled', true);
            $('#obtASTBrowsePos').attr('disabled', false);
        }

        $('.selectpicker').selectpicker('refresh');
        
        $('.xCNDatePicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            enableOnReadonly: false,
            startDate :'1900-01-01',
            disableTouchKeyboard : true,
            autoclose: true
        });

        $('.xCNTimePicker').datetimepicker({format: 'HH:mm:ss'});

        $('.xWTooltipsBT').tooltip({'placement': 'bottom'});

        $('[data-toggle="tooltip"]').tooltip({'placement': 'top'});
    
        $(".xWConDisDocument .disabled").attr("disabled","disabled");

        // =============================== Event Search Function ===============================
        // สลับแทบค้นหาไปสแกนสินค้า
        $('#oliASTMngPdtScan').unbind().click(function(){
            // Hide
            $('#oetASTSearchPdtHTML').hide();
            $('#obtASTMngPdtIconSearch').hide();
            // Show
            $('#oetASTScanPdtHTML').show();
            $('#obtASTMngPdtIconScan').show();
        });

        // สลับแทบสแกนสินค้าไปค้นหาสินค้า
        $('#oliASTMngPdtSearch').unbind().click(function(){
            // Hide
            $('#oetASTScanPdtHTML').hide();
            $('#obtASTMngPdtIconScan').hide();
            // Show
            $('#oetASTSearchPdtHTML').show();
            $('#obtASTMngPdtIconSearch').show();
        });
        
        // ================================ Event Date Function  ===============================
        $('#obtASTDocDate').unbind().click(function(){
            $('#oetASTDocDate').datepicker('show');
        });

        $('#obtASTDocTime').unbind().click(function(){
            $('#oetASTDocTime').datetimepicker('show');
        });

        $('#obtASTDateFrm').unbind().click(function(){
            $('#oetASTDateFrm').datepicker('show');
        });

        $('#obtASTDateTo').unbind().click(function(){
            $('#oetASTDateTo').datepicker('show');
        });

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

        // ================================== Change CountType =================================
        $('#oetASTCountType').on('change', function() {
            if($(this).val() == '1'){
                $("#odvASTCircle").hide();
            }else{
                $("#odvASTCircle").show();
            }
        });

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
            var tName = "กำหนดเอง";
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

        
        // ================================== Set Date Default =================================
        var dCurrentDate    = new Date();
        var tAmOrPm         = (dCurrentDate.getHours() < 12) ? "AM" : "PM";
        var tCurrentTime    = dCurrentDate.getHours() + ":" + dCurrentDate.getMinutes() + " " + tAmOrPm;

        if($('#oetASTDocDate').val() == ''){
            $('#oetASTDocDate').datepicker("setDate",dCurrentDate); 
        }

        if($('#oetASTDateFrm').val() == ''){
            $('#oetASTDateFrm').datepicker("setDate",dCurrentDate); 
        }

        if($('#oetASTDateTo').val()==''){
            $('#oetASTDateTo').datepicker("setDate",dCurrentDate); 
        }

        if($('#oetASTDocTime').val()==''){
            $('#oetASTDocTime').val(tCurrentTime);
        }
          

        // =============================== Check Box Auto GenCode ==============================
        $('#ocbASTStaAutoGenCode').on('change', function (e) {
            if($('#ocbASTStaAutoGenCode').is(':checked')){
                $("#oetASTDocNo").val('');
                $("#oetASTDocNo").attr("readonly", true);
                $('#oetASTDocNo').closest(".form-group").css("cursor","not-allowed");
                $('#oetASTDocNo').css("pointer-events","none");
                $("#oetASTDocNo").attr("onfocus", "this.blur()");
                $('#ofmASTFormAdd').removeClass('has-error');
                $('#ofmASTFormAdd .form-group').closest('.form-group').removeClass("has-error");
                $('#ofmASTFormAdd em').remove();
            }else{
                $('#oetASTDocNo').closest(".form-group").css("cursor","");
                $('#oetASTDocNo').css("pointer-events","");
                $('#oetASTDocNo').attr('readonly',false);
                $("#oetASTDocNo").removeAttr("onfocus");
            }
        });
        
    }); 

    // ======================================= Option Browse Modal ========================================

    // Option Modal สาขา
    var oASTBrowseBranch        = function(poDataFnc){
        var tUsrLevel       = "<?php echo $this->session->userdata("tSesUsrLevel"); ?>";
        var tBchCodeMulti   = "<?php echo $this->session->userdata("tSesUsrBchCodeMulti"); ?>";
        var nCountBch       = "<?php echo $this->session->userdata("nSesUsrBchCount"); ?>";
        var tWhere          = "";
    
        if(nCountBch == 1){
            $('#obtASTBrowseBch').attr('disabled',true);
        }
        if(tUsrLevel != "HQ"){
            tWhere = " AND TCNMBranch.FTBchCode IN ("+tBchCodeMulti+") ";
        }else{
            tWhere = "";
        }

        var tInputReturnCode    = poDataFnc.tReturnInputCode;
        var tInputReturnName    = poDataFnc.tReturnInputName;
        var tNextFuncName       = poDataFnc.tNextFuncName;
        var aArgReturn          = poDataFnc.aArgReturn;
        var oOptionReturn       = {
            Title: ['company/branch/branch','tBCHTitle'],
            Table: {Master:'TCNMBranch',PK:'FTBchCode'},
            Join: {
                Table: ['TCNMBranch_L'],
                On: ['TCNMBranch_L.FTBchCode = TCNMBranch.FTBchCode AND TCNMBranch_L.FNLngID = '+nLangEdits,]
            },
            GrideView:{
                ColumnPathLang: 'company/branch/branch',
                ColumnKeyLang: ['tBCHCode', 'tBCHName'],
                ColumnsSize: ['15%', '75%'],
                WidthModal: 50,
                DataColumns: ['TCNMBranch.FTBchCode', 'TCNMBranch_L.FTBchName'],
                DataColumnsFormat: ['', ''],
                DisabledColumns: [],
                Perpage: 10,
                OrderBy: ['TCNMBranch.FTBchCode DESC'],
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode,"TCNMBranch.FTBchCode"],
                Text: [tInputReturnName,"TCNMBranch_L.FTBchName"],
            },
            NextFunc: {
                FuncName: tNextFuncName,
                ArgReturn: aArgReturn
            },
            RouteAddNew: 'branch',
            BrowseLev   : nStaASTBrowseType,
            // DebugSQL : true
        };
        return oOptionReturn;
    }
        
    // Option Modal กลุ่มธุรกิจ (กลุ่มร้านค้า)
    var oASTBrowseMerchant      = function(poDataFnc){
        var tASTBchCode         = poDataFnc.tASTBchCode;
        var tInputReturnCode    = poDataFnc.tReturnInputCode;
        var tInputReturnName    = poDataFnc.tReturnInputName;
        var tNextFuncName       = poDataFnc.tNextFuncName;
        var aArgReturn          = poDataFnc.aArgReturn;
        var tWhereModal         = "";

        // if(typeof(tASTBchCode) != undefined && tASTBchCode != ""){
            // tWhereModal = "AND (SELECT COUNT(FTShpCode) FROM TCNMShop WHERE TCNMShop.FTMerCode = TCNMMerchant.FTMerCode AND TCNMShop.FTBchCode = '"+tASTBchCode+"') != 0";
        // }

        var oOptionReturn       = {
            Title   : ['company/merchant/merchant','tMerchantTitle'],
            Table   : {Master:'TCNMMerchant',PK:'FTMerCode'},
            Join    : {
                Table : ['TCNMMerchant_L','TCNMShop','TCNMShop_L'],
                On : [
                    'TCNMMerchant.FTMerCode = TCNMMerchant_L.FTMerCode AND TCNMMerchant_L.FNLngID = ' + nLangEdits ,
                    "TCNMMerchant.FTMerCode = TCNMShop.FTMerCode AND TCNMShop.FTBchCode='"+tASTBchCode+"' AND TCNMShop.FTShpCode = ( SELECT TOP 1 FTShpCode FROM TCNMShop WHERE TCNMShop.FTMerCode = TCNMMerchant.FTMerCode AND TCNMShop.FTBchCode = '"+tASTBchCode+"' )",
                    "TCNMShop.FTBchCode = TCNMShop_L.FTBchCode AND TCNMShop.FTShpCode = TCNMShop_L.FTShpCode AND TCNMShop_L.FNLngID = " + nLangEdits
                ]
            },
            Where : {
                Condition : [
                    " AND TCNMShop.FTShpCode IS NOT NULL "
                ]
            },
            GrideView : {
                ColumnPathLang	: 'company/merchant/merchant',
                ColumnKeyLang	: ['tMerCode','tMerName'],
                ColumnsSize     : ['15%','75%'],
                WidthModal      : 50,
                DataColumns		: ['TCNMMerchant.FTMerCode','TCNMMerchant_L.FTMerName','TCNMShop.FTShpCode','TCNMShop_L.FTShpName'],
                DisabledColumns : [2,3],
                DataColumnsFormat : ['',''],
                Perpage			: 10,
                OrderBy			: ['TCNMMerchant.FDCreateOn DESC'],
            },
            CallBack : {
                ReturnType	: 'S',
                Value		: [tInputReturnCode,"TCNMMerchant.FTMerCode"],
                Text		: [tInputReturnName,"TCNMMerchant_L.FTMerName"],
            },
            NextFunc : {
                FuncName    : tNextFuncName,
                ArgReturn   : aArgReturn
            },
            RouteAddNew: 'merchant',
            BrowseLev: nStaASTBrowseType,
            // DebugSQL : true
        };
        return oOptionReturn;
    }

    // Option Modal ร้านค้า
    var oASTBrowseShop          = function(poDataFnc){
        var tASTBchCode         = poDataFnc.tASTBchCode;
        var tASTMerCode         = poDataFnc.tASTMerCode;
        var tInputReturnCode    = poDataFnc.tReturnInputCode;
        var tInputReturnName    = poDataFnc.tReturnInputName;
        var tNextFuncName       = poDataFnc.tNextFuncName;
        var aArgReturn          = poDataFnc.aArgReturn;
        var tWhereModal         = "";
        // Check Shop Branch
        if(typeof(tASTBchCode) != undefined && tASTBchCode != ""){
            tWhereModal += " AND (TCNMShop.FTBchCode = '"+tASTBchCode+"') AND TCNMShop.FTShpType  != 5"
        }
        // Cheack Shop Merchant
        if(typeof(tASTMerCode) != undefined && tASTMerCode != ""){
            tWhereModal += " AND (TCNMShop.FTMerCode = '"+tASTMerCode+"') AND TCNMShop.FTShpType  != 5";

        }

        tUsrLevel = "<?=$this->session->userdata('tSesUsrLevel')?>";
        tSHPMulti = "<?=$this->session->userdata("tSesUsrShpCodeMulti"); ?>";
        tSQLWhere = "";
        if(tUsrLevel == "SHP"){
            tWhereModal += " AND TCNMShop.FTShpCode IN ("+tSHPMulti+") ";
        }

        var oOptionReturn       = {
            Title: ["company/shop/shop","tSHPTitle"],
            Table: {Master:"TCNMShop",PK:"FTShpCode"},
            Join: {
                Table: ['TCNMShop_L','TCNMWaHouse_L'],
                On: [
                    'TCNMShop.FTShpCode = TCNMShop_L.FTShpCode AND TCNMShop.FTBchCode = TCNMShop_L.FTBchCode AND TCNMShop_L.FNLngID = '+nLangEdits,
                    'TCNMShop.FTWahCode = TCNMWaHouse_L.FTWahCode AND TCNMShop.FTBchCode = TCNMWaHouse_L.FTBchCode AND TCNMWaHouse_L.FNLngID= '+nLangEdits
                ]
            },
            Where: {
                Condition: [tWhereModal]
            },
            GrideView: {
                ColumnPathLang      : 'company/shop/shop',
                ColumnKeyLang       : ['tShopCode','tShopName'],
                ColumnsSize         : ['15%','75%'],
                WidthModal          : 50,
                DataColumns         : ['TCNMShop.FTShpCode','TCNMShop_L.FTShpName','TCNMShop.FTWahCode','TCNMWaHouse_L.FTWahName','TCNMShop.FTShpType','TCNMShop.FTBchCode'],
                DataColumnsFormat   : ['','','','','',''],
                DisabledColumns     : [2,3,4,5],
                Perpage             : 10,
                OrderBy			    : ['TCNMShop.FDCreateOn DESC'],
            },
            CallBack: {
                ReturnType	: 'S',
                Value		: [tInputReturnCode,"TCNMShop.FTShpCode"],
                Text		: [tInputReturnName,"TCNMShop_L.FTShpName"],
            },
            NextFunc:{
                FuncName    : tNextFuncName,
                ArgReturn   : aArgReturn
            },
            RouteAddNew: 'shop',
            BrowseLev : nStaASTBrowseType,
            // DebugSQL : true
        };
        return oOptionReturn;
    }

    // Option Modal เครื่องจุดขาย
    var oASTBrowsePos           = function(poDataFnc){
        var tASTBchCode         = poDataFnc.tASTBchCode;
        var tASTShpCode         = poDataFnc.tASTShpCode;
        var tInputReturnCode    = poDataFnc.tReturnInputCode;
        var tInputReturnName    = poDataFnc.tReturnInputName;
        var tNextFuncName       = poDataFnc.tNextFuncName;
        var aArgReturn          = poDataFnc.aArgReturn;
        var tWhereModal         = "";

        if(typeof(tASTBchCode) != undefined && tASTBchCode != ""){
            tWhereModal += " AND (TVDMPosShop.FTBchCode = '"+tASTBchCode+"') AND TVDMPosShop.FTPshStaUse = 1";
        }

        if(typeof(tASTShpCode) != undefined && tASTShpCode != ""){
            tWhereModal += " AND (TVDMPosShop.FTShpCode = '"+tASTShpCode+"') AND TVDMPosShop.FTPshStaUse = 1";
        }

        var oOptionReturn       = {
            Title: ["pos/posshop/posshop","tPshTitle"],
            Table: { Master:'TVDMPosShop', PK:'FTPosCode' },
            Join: {
                Table: ['TCNMPos', 'TCNMPosLastNo', 'TCNMWaHouse', 'TCNMWaHouse_L','TCNMPos_L'],
                On: [
                    "TVDMPosShop.FTPosCode = TCNMPos.FTPosCode AND TVDMPosShop.FTBchCode = TCNMPos.FTBchCode",
                    "TVDMPosShop.FTPosCode = TCNMPosLastNo.FTPosCode",
                    "TVDMPosShop.FTPosCode = TCNMWaHouse.FTWahRefCode AND TVDMPosShop.FTBchCode = TCNMWaHouse.FTBchCode AND TCNMWaHouse.FTWahStaType IN ('1','2')",
                    "TCNMWaHouse.FTWahCode = TCNMWaHouse_L.FTWahCode AND TVDMPosShop.FTBchCode = TCNMWaHouse_L.FTBchCode AND TCNMWaHouse_L.FNLngID = '"+nLangEdits+"'",
                    "TCNMPos_L.FTPosCode   = TVDMPosShop.FTPosCode AND TCNMPos_L.FTBchCode = TVDMPosShop.FTBchCode AND TCNMPos_L.FNLngID = '"+nLangEdits+"'"
                ]
            },
            Where: {
                Condition : [tWhereModal]
            },
            GrideView: {
                ColumnPathLang: 'pos/posshop/posshop',
                ColumnKeyLang: ['tPshBRWShopTBCode', 'tPshBRWPosTBName'],
                ColumnsSize: ['25%', '75%'],
                WidthModal: 50,
                DataColumns: ['TVDMPosShop.FTPosCode', 'TCNMPos_L.FTPosName', 'TVDMPosShop.FTShpCode', 'TVDMPosShop.FTBchCode', 'TCNMWaHouse.FTWahCode', 'TCNMWaHouse_L.FTWahName','TVDMPosShop.FTPshStaUse'],
                DataColumnsFormat : ['', '', '', '', '', ''],
                DisabledColumns: [2, 3, 4, 5, 6],
                Perpage: 10,
                OrderBy: ['TVDMPosShop.FDCreateOn DESC'],
            },
            CallBack: {
                ReturnType  : 'S',
                Value       : [tInputReturnCode,"TVDMPosShop.FTPosCode"],
                Text        : [tInputReturnName,"TCNMPos_L.FTPosName"]   // ก่อนหน้านั้นเป็น  TCNMPosLastNo.FTPosComName เปลี่ยน เป็น TVDMPosShop.FTPosCode
            },
            NextFunc: {
                FuncName    : tNextFuncName,
                ArgReturn   : aArgReturn
            },
            RouteAddNew: 'salemachine',
            BrowseLev : nStaASTBrowseType,
            // DebugSQL : true

        };
        return oOptionReturn;
    }

    // Option Modal คลังสินค้า
    var oASTBrowseWah           = function(poDataFnc){
        var tASTBchCode         = poDataFnc.tASTBchCode;
        var tASTShpCode         = poDataFnc.tASTShpCode;
        var tASTPosCode         = poDataFnc.tASTPosCode;
        var tInputReturnCode    = poDataFnc.tReturnInputCode;
        var tInputReturnName    = poDataFnc.tReturnInputName;
        var tNextFuncName       = poDataFnc.tNextFuncName;
        var aArgReturn          = poDataFnc.aArgReturn;
        var tWhereModal         = "";

        // Where คลังของ สาขา
        // if(tASTShpCode == "" && tASTPosCode == ""){
        //     tWhereModal += " AND (TCNMWaHouse.FTWahStaType IN (1,2,5))";
        // }

        // Where สาขา
        if(tASTBchCode  != ""){
            tWhereModal += " AND (TCNMWaHouse.FTBchCode = '"+tASTBchCode+"')";
        }

        // Where คลังของ ร้านค้า
        // if(tASTShpCode  != "" && tASTPosCode == ""){
        //     tWhereModal += " AND (TCNMWaHouse.FTWahStaType IN (4))";
        //     tWhereModal += " AND (TCNMWaHouse.FTWahRefCode = '"+tASTShpCode+"')";
        // }

        // Where คลังของ เครื่องจุดขาย
        // if(tASTShpCode  != "" && tASTPosCode != ""){
        //     tWhereModal += " AND (TCNMWaHouse.FTWahStaType IN (6))";
        //     tWhereModal += " AND (TCNMWaHouse.FTWahRefCode = '"+tASTPosCode+"')";
        // }

        var oOptionReturn       = {
            Title: ["company/warehouse/warehouse","tWAHTitle"],
            Table: { Master:"TCNMWaHouse", PK:"FTWahCode"},
            Join: {
                Table: ["TCNMWaHouse_L"],
                On: [
                    "TCNMWaHouse.FTWahCode = TCNMWaHouse_L.FTWahCode AND TCNMWaHouse.FTBchCode = TCNMWaHouse_L.FTBchCode AND TCNMWaHouse_L.FNLngID = '"+nLangEdits+"'"
                ]
            },
            Where: {
                Condition : [tWhereModal]
            },
            GrideView:{
                ColumnPathLang: 'company/warehouse/warehouse',
                ColumnKeyLang: ['tWahCode','tWahName'],
                DataColumns: ['TCNMWaHouse.FTWahCode','TCNMWaHouse_L.FTWahName'],
                DataColumnsFormat: ['',''],
                ColumnsSize: ['15%','75%'],
                Perpage: 10,
                WidthModal: 50,
                OrderBy: ['TCNMWaHouse.FDCreateOn DESC'],
            },
            CallBack: {
                ReturnType  : 'S',
                Value       : [tInputReturnCode,"TCNMWaHouse.FTWahCode"],
                Text        : [tInputReturnName,"TCNMWaHouse_L.FTWahName"]
            },
            NextFunc: {
                FuncName    : tNextFuncName,
                ArgReturn   : aArgReturn
            },
            RouteAddNew: 'warehouse',
            BrowseLev : nStaASTBrowseType,
            // DebugSQL : true
        };
        return oOptionReturn;
    }

    // Option Modal คลังสินค้า (ร้านค้า)
    var oASTBrowseShpWah        = function(poDataFnc){
        var tASTBchCode         = poDataFnc.tASTBchCode;
        var tASTShpCode         = poDataFnc.tASTShpCode;
        var tInputReturnCode    = poDataFnc.tReturnInputCode;
        var tInputReturnName    = poDataFnc.tReturnInputName;
        var tNextFuncName       = poDataFnc.tNextFuncName;
        var aArgReturn          = poDataFnc.aArgReturn;
        var tWhereModal         = "";

        var oOptionReturn       = {
            Title: ["company/warehouse/warehouse","tWAHTitle"],
            Table: {Master:"TCNMShpWah",PK:"FTWahCode"},
            Join: {
                Table: ['TCNMWaHouse_L'],
                On: [
                    'TCNMShpWah.FTWahCode = TCNMWaHouse_L.FTWahCode AND TCNMShpWah.FTBchCode = TCNMWaHouse_L.FTBchCode AND TCNMWaHouse_L.FNLngID = ' + nLangEdits
                ]
            },
            Where: {
                Condition: [
                    " AND TCNMShpWah.FTBchCode = '" + tASTBchCode + "' ",
                    " AND TCNMShpWah.FTShpCode = '" + tASTShpCode + "' "
                ]
            },
            GrideView: {
                ColumnPathLang      : 'company/warehouse/warehouse',
                ColumnKeyLang       : ['tWahCode','tWahName'],
                ColumnsSize         : ['15%','75%'],
                WidthModal          : 50,
                DataColumns         : ['TCNMShpWah.FTWahCode','TCNMWaHouse_L.FTWahName'],
                DataColumnsFormat   : ['',''],
                // DisabledColumns     : [2,3,4,5],
                Perpage             : 10,
                OrderBy			    : ['TCNMShpWah.FDCreateOn DESC'],
            },
            CallBack: {
                ReturnType	: 'S',
                Value		: [tInputReturnCode,"TCNMShpWah.FTWahCode"],
                Text		: [tInputReturnName,"TCNMWaHouse_L.FTWahName"],
            },
            NextFunc: {
                FuncName    : tNextFuncName,
                ArgReturn   : aArgReturn
            },
            RouteAddNew: 'warehouse',
            BrowseLev : nStaASTBrowseType,
            // DebugSQL : true
        };
        return oOptionReturn;
    }

    // Option Modal เหตุผล
    var oASTBrowseRsn           = function(poDataFnc){
        var tInputReturnCode    = poDataFnc.tReturnInputCode;
        var tInputReturnName    = poDataFnc.tReturnInputName;
        var oOptionReturn       = {
            Title: ["other/reason/reason","tRSNTitle"],
            Table: {Master:"TCNMRsn",PK:"FTRsnCode"},
            Join: {
                Table: ["TCNMRsn_L"],
                On: ["TCNMRsn.FTRsnCode = TCNMRsn_L.FTRsnCode AND TCNMRsn_L.FNLngID = '"+nLangEdits+"'"]
            },
            Where: {
                Condition : ["  AND TCNMRsn.FTRsgCode = '008' "]
            },
            GrideView: {
                ColumnPathLang: 'other/reason/reason',
                ColumnKeyLang: ['tRSNTBCode','tRSNTBName'],
                ColumnsSize: ['15%','75%'],
                WidthModal: 50,
                DataColumns: ['TCNMRsn.FTRsnCode','TCNMRsn_L.FTRsnName'],
                DataColumnsFormat: ['',''],
                Perpage: 10,
                OrderBy: ['TCNMRsn.FDCreateOn DESC'],
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode,"TCNMRsn.FTRsnCode"],
                Text: [tInputReturnName,"TCNMRsn_L.FTRsnName"],
            },
            RouteAddNew : 'reason',
            BrowseLev : nStaASTBrowseType,
        };
        return oOptionReturn;
    }

    // Option Modal เหตุผลการยกเลิกเอกสาร
    var oASTBrowseRsnCancel     = function(poDataFnc){
        var tInputReturnCode    = poDataFnc.tReturnInputCode;
        var tInputReturnName    = poDataFnc.tReturnInputName;
        var oOptionReturn       = {
            Title: ["other/reason/reason","tRSNTitle"],
            Table: {Master:"TCNMRsn",PK:"FTRsnCode"},
            Join: {
                Table: ["TCNMRsn_L"],
                On: ["TCNMRsn.FTRsnCode = TCNMRsn_L.FTRsnCode AND TCNMRsn_L.FNLngID = '"+nLangEdits+"'"]
            },
            Where: {
                Condition : ["  AND TCNMRsn.FTRsgCode = '019' "]
            },
            GrideView: {
                ColumnPathLang: 'other/reason/reason',
                ColumnKeyLang: ['tRSNTBCode','tRSNTBName'],
                ColumnsSize: ['15%','75%'],
                WidthModal: 50,
                DataColumns: ['TCNMRsn.FTRsnCode','TCNMRsn_L.FTRsnName'],
                DataColumnsFormat: ['',''],
                Perpage: 10,
                OrderBy: ['TCNMRsn.FDCreateOn DESC'],
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode,"TCNMRsn.FTRsnCode"],
                Text: [tInputReturnName,"TCNMRsn_L.FTRsnName"],
            },
            RouteAddNew : 'reason',
            BrowseLev : nStaASTBrowseType,
        };
        return oOptionReturn;
    }

    // Option Modal ตัวแทนขาย
    var oAgnOption              = function(poDataFnc){
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

    // ========================================= Event Browse Modal =======================================
    // Event Browse Modal สาขา
    $('#obtASTBrowseBch').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxCheckPinMenuClose(); // Hidden Pin Menu
            window.oASTBrowseBranchOption   = undefined;
            oASTBrowseBranchOption          = oASTBrowseBranch({
                'tReturnInputCode'  : 'oetASTBchCode',
                'tReturnInputName'  : 'oetASTBchName',
                'tNextFuncName'     : 'JSxASTSetConditionBranch',
                'aArgReturn'        : ['FTBchCode','FTBchName']
            });
            JCNxBrowseData('oASTBrowseBranchOption');
        }else{
            JCNxShowMsgSessionExpired();
        }
    });

    // Event Browse Modal กลุ่มร้านค้า
    $('#obtASTBrowseMer').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxCheckPinMenuClose(); // Hidden Pin Menu
            window.oASTBrowseMerchantOption = undefined;
            oASTBrowseMerchantOption        = oASTBrowseMerchant({
                'tASTBchCode'       : $('#oetASTBchCode').val(),
                'tReturnInputCode'  : 'oetASTMerCode',
                'tReturnInputName'  : 'oetASTMerName',
                'tNextFuncName'     : 'JSxASTSetConditionMerchant',
                'aArgReturn'        : ['FTMerCode','FTMerName','FTShpCode','FTShpName'],
            });
            JCNxBrowseData('oASTBrowseMerchantOption');
        }else{
            JCNxShowMsgSessionExpired();
        }
    });

    // Event Browse Modal ร้านค้า
    $('#obtASTBrowseShp').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxCheckPinMenuClose(); // Hidden Pin Menu
            window.oASTBrowseShopOption = undefined;
            oASTBrowseShopFrmOption     = oASTBrowseShop({
                'tASTBchCode'       : $('#oetASTBchCode').val(),
                'tASTMerCode'       : $('#oetASTMerCode').val(),
                'tReturnInputCode'  : 'oetASTShopCode',
                'tReturnInputName'  : 'oetASTShopName',
                'tNextFuncName'     : 'JSxASTSetConditionShop',
                'aArgReturn'        : ['FTBchCode','FTShpCode','FTShpType','FTShpName','FTWahCode','FTWahName']
            });
            JCNxBrowseData('oASTBrowseShopFrmOption');
        }else{
            JCNxShowMsgSessionExpired();
        }
    });

    // Event Browse Modal เครื่องจุดขาย
    $('#obtASTBrowsePos').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxCheckPinMenuClose(); // Hidden Pin Menu
            window.oASTBrowsePosOption  = undefined;
            oASTBrowsePosOption         = oASTBrowsePos({
                'tASTBchCode'       : $('#oetASTBchCode').val(),
                'tASTShpCode'       : $('#oetASTShopCode').val(),
                'tReturnInputCode'  : "oetASTPosCode",
                'tReturnInputName'  : "oetASTPosName",
                'tNextFuncName'     : "JSxASTSetConditionPos",
                'aArgReturn'        : ['FTBchCode','FTShpCode','FTPosCode','FTWahCode','FTWahName']
            });
            JCNxBrowseData('oASTBrowsePosOption');
        }else{
            JCNxShowMsgSessionExpired();
        }
    });

    // Event Browse Modal คลังสินค้า
    $('#obtASTBrowseWah').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxCheckPinMenuClose(); // Hidden Pin Menu

            var tShpCode = $('#oetASTShopCode').val();
            var tPosCode = $('#oetASTPosCode').val();
            if(typeof(tShpCode) != undefined && tShpCode != "" && tPosCode == ""){
                //คล้งร้านค้า ShopWah  Where ShpCode
                window.oASTBrowseShpWahOption = undefined;
                oASTBrowseShpWahOption     = oASTBrowseShpWah({
                    'tASTBchCode'        : $('#oetASTBchCode').val(),
                    'tASTShpCode'        : $('#oetASTShopCode').val(),
                    'tReturnInputCode'   : 'oetASTWahCode',
                    'tReturnInputName'   : 'oetASTWahName',
                    'tNextFuncName'      : "JSxASTSetConditionWah",
                    'aArgReturn'         : []
                });
                JCNxBrowseData('oASTBrowseShpWahOption');
            }else{
                //คลังสาขา Wahouse   Where RefCode
                window.oASTBrowseWahOption  = undefined;
                oASTBrowseWahOption         = oASTBrowseWah({
                    'tASTBchCode'       : $('#oetASTBchCode').val(),
                    'tASTShpCode'       : $('#oetASTShopCode').val(),
                    'tASTPosCode'       : $('#oetASTPosCode').val(),
                    'tReturnInputCode'  : "oetASTWahCode",
                    'tReturnInputName'  : "oetASTWahName",
                    'tNextFuncName'     : "JSxASTSetConditionWah",
                    'aArgReturn'        : []
                });
                JCNxBrowseData('oASTBrowseWahOption');
            }
            
        }else{
            JCNxShowMsgSessionExpired();
        }
    });

    // Event Browse Modal เหตุผล
    $('#obtASTBrowseRsn').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxCheckPinMenuClose(); // Hidden Pin Menu
            window.oASTBrowseRsnOption  = undefined;
            oASTBrowseRsnOption         = oASTBrowseRsn({
                'tReturnInputCode'  : "oetASTRsnCode",
                'tReturnInputName'  : "oetASTRsnName",
            });
            JCNxBrowseData('oASTBrowseRsnOption');
        }else{
            JCNxShowMsgSessionExpired();
        }
    });

    //เหตุผลในการยกเลิก
    $('#obtASTBrowseRsnCancel').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxCheckPinMenuClose(); // Hidden Pin Menu
            window.oASTBrowseRsnOption  = undefined;
            oASTBrowseRsnOption         = oASTBrowseRsnCancel({
                'tReturnInputCode'  : "oetASTRsnCodeCancel",
                'tReturnInputName'  : "oetASTRsnNameCancel",
            });
            JCNxBrowseData('oASTBrowseRsnOption');
        }else{
            JCNxShowMsgSessionExpired();
        }
    });

    // =================================== Function NextFunc Browse Modal =================================

    //Functionality : Function Behind NextFunc กลุ่มสาขา
    function JSxASTSetConditionBranch(poDataNextFunc){

        if(poDataNextFunc != "NULL"){
            aData = JSON.parse(poDataNextFunc);
            tAddBch = aData[0];
            tAddSeqNo = aData[1];
        }
        
        var tBchCode = $('#oetASTBchCode').val();
        var tMchName = $('#oetASTMerName').val();
        var tShpName = $('#oetASTShopName').val();
        var tPosName = $('#oetASTPosName').val();
        var tWahName = $('#oetASTWahName').val();


        $('#obtASTBrowseMer').attr('disabled', true);
        $('#obtASTBrowseShp').attr('disabled', true);
        $('#obtASTBrowsePos').attr('disabled', true);
        $('#obtASTBrowseWah').attr('disabled', true);

        JSxASTSetStatusClickSubmit(0);
        JSxValidateFormAddAST();
        $('#ofmASTFormAdd').submit();

    }

    //Functionality : Function Behind NextFunc กลุ่มร้านค้า
    function JSxASTSetConditionMerchant(poDataNextFunc){
        // console.log(poDataNextFunc);
        
        if(poDataNextFunc != "NULL"){
            var aData = JSON.parse(poDataNextFunc);
            $('#obtASTBrowseShp').attr('disabled', false);
            $('#obtASTBrowsePos').attr('disabled', false);
            $('#oetASTShopCode').val(aData[2]);
            $('#oetASTShopName').val(aData[3]);
        }else{
            $('#oetASTShopCode').val('');
            $('#oetASTShopName').val('');
            $('#obtASTBrowsePos').attr('disabled', true);
            $('#obtASTBrowseShp').attr('disabled', true);
        }

        $('#oetASTPosCode').val();
        $('#oetASTPosName').val();

        // ให้คลังสินค้าเป็นว่าง
        $('#oetASTWahCode').val('');
        $('#oetASTWahName').val('');

        JSxASTSetStatusClickSubmit(0);
        JSxValidateFormAddAST();
        $('#ofmASTFormAdd').submit();
    }

    //Functionality : Function Behind NextFunc ร้านค้า
    function JSxASTSetConditionShop(poDataNextFunc){
    
        $('#oetASTPosCode').val('');
        $('#oetASTPosName').val('');

        $('#oetASTWahCode, #oetASTWahName').val('');

        // ['FTBchCode','FTShpCode','FTShpType','FTShpName','FTWahCode','FTWahName']
        var aData, tResAddBch, tResAddSeqNo, tResWahCode, tResWahName;
        if(poDataNextFunc != "NULL"){
        
            aData   = JSON.parse(poDataNextFunc);
            tResAddBch      = aData[0];
            tResAddSeqNo    = aData[1];
            tResWahCode     = aData[3];
            tResWahName     = aData[4];

    

            tBchCode = aData[0];
            tShpCode = aData[1];
            tShpType = aData[2];
            tShpName = aData[3];
            tValueWahCode = aData[4];
            tValueWahName = aData[5];

            // $('#oetASTWahCode').val(tValueWahCode);
            // $('#oetASTWahName').val(tValueWahName);
            $('#oetASTShopCode').val(tShpCode);
            $('#oetASTShopName').val(tShpName);
            $('#obtASTBrowsePos').attr('disabled', false);
            
        }else{
            $('#obtASTBrowsePos').attr('disabled', true);
            $('#oetASTWahCode, #oetASTWahName').val('');
            $('#oetASTShopCode, #oetASTShopName').val('');
        }
        //  console.log(aData);
        // console.log('aData: ', aData);

        // var tBchCode = $('#oetASTBchCode').val();
        // var tMchName = $('#oetASTMerName').val();
        // var tShpName = $('#oetASTShopName').val();
        // var tPosName = $('#oetASTPosName').val();
        // var tWahName = $('#oetASTWahName').val();

        // $('#obtASTBrowsePos').attr('disabled', true);
        // $('#obtASTBrowseWah').attr('disabled', false);

        // if(JStCMNUserLevel() == 'HQ' || JStCMNUserLevel() == 'BCH'){
        //     if(tShpName != ''){
        //         tType = aData[2]
        //         if(tType == 4){
        //             $('#obtASTBrowsePos').attr('disabled', false);
        //             $('#obtASTBrowsePos').removeClass("disabled");
        //             $('#obtASTBrowseWah').attr('disabled', true);
        //             // $('#oetASTWahCode').val(tValueWahCode);  //ใส่ tValueWahCode
        //             // $('#oetASTWahName').val(tValueWahName);
        //         }else{
        //             // $('#oetASTWahCode').val(tValueWahCode);
        //             // $('#oetASTWahName').val(tValueWahName);
        //             $('#obtASTBrowsePos').attr('disabled', true);
        //             $('#obtASTBrowsePos').addClass("disabled");
        //             $('#obtASTBrowseWah').attr('disabled', true);          
        //         }
        //     }
        //     // else{
        //     //     $('#oetASTWahCode, #oetASTWahName').val('');
        //     // }
        //     $('#oetASTPosCode, #oetASTPosName').val('');
        //     $('#oetASTWahCode, #oetASTWahName').val('');
        // }

        JSxASTSetStatusClickSubmit(0);
        JSxValidateFormAddAST();
        $('#ofmASTFormAdd').submit();
    }

    //Functionality : Function Behind NextFunc เครื่องจุดขาย
    function JSxASTSetConditionPos(poDataNextFunc){
        
        var aData, tResAddBch, tResAddSeqNo, tResWahCode, tResWahName;
        if(poDataNextFunc != "NULL"){
            aData = JSON.parse(poDataNextFunc);
            // console.log(aData);
            tResAddBch   = aData[0];
            tResAddSeqNo = aData[1];
            tResWahCode  = aData[3];
            tResWahName  = aData[4];
            $('#oetASTWahCode').val(tResWahCode);
                $('#oetASTWahName').val(tResWahName);
                $('#obtASTBrowseWah').attr('disabled', true);
        }else{
            $('#oetASTPosCode, #oetASTPosName').val();
            $('#oetASTWahCode').val('');
            $('#oetASTWahName').val('');
            $('#obtASTBrowseWah').attr('disabled', false);
            return;
        }
        // console.log('aData POs: ', aData);

        // var tBchCode = $('#oetASTBchCode').val();
        // var tMchName = $('#oetASTMerName').val();
        // var tShpName = $('#oetASTShopName').val();
        // var tPosName = $('#oetASTPosName').val();
        // var tWahName = $('#oetASTWahName').val();

        // $('#obtASTBrowseWah').attr('disabled', false);

        // if(JStCMNUserLevel() == 'HQ' || JStCMNUserLevel() == 'BCH' || JStCMNUserLevel() == 'SHP'){
        //    if(tPosName != ''){
        //         $('#obtASTBrowseWah').attr('disabled', true);
        //    }
        // }

        JSxASTSetStatusClickSubmit(0);
        JSxValidateFormAddAST();
        $('#ofmASTFormAdd').submit();
    }

    //Functionality : Function Behind NextFunc คลังสินค้า
    function JSxASTSetConditionWah(poDataNextFunc){
        if (poDataNextFunc != "NULL") {
                aData = JSON.parse(poDataNextFunc);
                tAddBch = aData[0];
                tAddSeqNo = aData[1];
        }
        JSxASTSetStatusClickSubmit(0);
        JSxValidateFormAddAST();
        $.ajax({
            type : "POST",
            url : "docAdjStkUpdateAdj",
            data :{
                tBchCode        : $("#oetASTBchCode").val(),
                tDocCode        : $("#oetASTDocNo").val(),
                tWahCode        : $("#oetASTWahCode").val()
            },
            catch : false,
            timeout : 0,
            success : function (tResult){
                JSvASTLoadPdtDataTableHtml(1);
            },
            error: function (jqXHR,textStatus,errorThrown){
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });

        $('#ofmASTFormAdd').submit();
    }
        
    // =============================== Manage Product Advance Table Colums  ===============================
    $('#olbASTAdvTableLists').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxASTOpenColumnFormSet();
        }else{
            JCNxShowMsgSessionExpired();
        }
    });

    $('#odvASTOrderAdvTblColumns #obtASTSaveAdvTableColums').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxASTSaveColumnShow();
        }else{
            JCNxShowMsgSessionExpired();
        }
    });

    //Functionality : Call Modal Advnced Table List 
    //Parameters : Event After Click #olbASTAdvTableLists
    //Creator : 12/06/2019 Wasin(Yoshi)
    //Return : Open Modal Manage Colums Show
    //Return Type : -
    function JSxASTOpenColumnFormSet(){
        $.ajax({
            type: "POST",
            url: "dcmASTAdvanceTableShowColList",
            cache: false,
            Timeout: 0,
            success: function (oResult){
                var aDataReturn = JSON.parse(oResult);
                if(aDataReturn['nStaEvent'] == '1'){
                    var tViewTableShowCollist = aDataReturn['tViewTableShowCollist'];
                    $('#odvASTOrderAdvTblColumns .modal-body').html(tViewTableShowCollist);
                    $('#odvASTOrderAdvTblColumns').modal({backdrop: 'static', keyboard: false})  
                    $("#odvASTOrderAdvTblColumns").modal({ show: true });
                }else{
                    var tMessageError = aReturnData['tStaMessg'];
                    FSvCMNSetMsgErrorDialog(tMessageError);
                    JCNxCloseLoading();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    }

    //Functionality : Save Columns Show Advanced Table
    //Parameters : Event Next Func Modal
    //Creator : 12/06/2019 Wasin(Yoshi)
    //Return : Open Modal Manage Colums Show
    //Return Type : -
    function JSxASTSaveColumnShow(){
        // คอลัมน์ที่เลือกให้แสดง
        var aASTColShowSet = [];
        $("#odvASTOrderAdvTblColumns .xWASTInputColStaShow:checked").each(function(){
            aASTColShowSet.push($(this).data("id"));
        });

        // คอลัมน์ทั้งหมด
        var aASTColShowAllList = [];
        $("#odvASTOrderAdvTblColumns .xWASTInputColStaShow").each(function () {
            aASTColShowAllList.push($(this).data("id"));
        });

        // ชื่อคอลัมน์ทั้งหมดในกรณีมีการแก้ไขชื่อคอลัมน์ที่แสดง
        var aASTColumnLabelName = [];
        $("#odvASTOrderAdvTblColumns .xWASTLabelColumnName").each(function () {
            aASTColumnLabelName.push($(this).text());
        });

        // สถานะย้อนกลับค่าเริ่มต้น
        var nASTStaSetDef;
        if($("#odvASTOrderAdvTblColumns #ocbASTSetDefAdvTable").is(":checked")) {
            nASTStaSetDef   = 1;
        } else {
            nASTStaSetDef   = 0;
        }

        $.ajax({
            type: "POST",
            url: "dcmASTAdvanceTableShowColSave",
            data: {
                'nASTStaSetDef'         : nASTStaSetDef,
                'aASTColShowSet'        : aASTColShowSet,
                'aASTColShowAllList'    : aASTColShowAllList,
                'aASTColumnLabelName'   : aASTColumnLabelName
            },
            cache: false,
            Timeout: 0,
            success: function (oResult){
                $("#odvASTOrderAdvTblColumns").modal("hide");
                $(".modal-backdrop").remove();
                JSvASTLoadPdtDataTableHtml();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    }

    //สำนักงานใหญ่
    $('#obtASTHQApprove').click(function(){
        JSnASTHQApprove(false);
    });

    $('#obtASTHQConfirmApprDoc').click(function(){
        $("#ohdASTStaPrcStk").val(2);
        JSnASTHQApprove(true);
    });
    
    //สาขา
    $('#obtASTApprove').click(function(){
        JSnASTApprove(true);
    });

    $(document).ready(function(){

        //RabbitMQ
        /*var tLangCode   = nLangEdits;
        var tUsrBchCode = $("#ohdASTBchCode").val();
        var tUsrApv     = $("#ohdASTApvCodeUsrLogin").val();
        var tDocNo      = $("#oetASTDocNo").val();
        var tPrefix     = 'RESAJS';
        var tStaApv     = $("#ohdASTStaApv").val();
        var tStaDelMQ   = $("#ohdASTStaDelMQ").val();
        var tQName      = tPrefix + "_" + tDocNo + "_" + tUsrApv;
        var tStaPrcStk  = $("#ohdASTStaPrcStk").val();

        // MQ Message Config
        var poDocConfig = {
            tLangCode: tLangCode,
            tUsrBchCode: tUsrBchCode,
            tUsrApv: tUsrApv,
            tDocNo: tDocNo,
            tPrefix: tPrefix,
            tStaDelMQ: tStaDelMQ,
            tStaApv: tStaApv,
            tQName: tQName
        };

        // RabbitMQ STOMP Config
		var poMqConfig = {
            host        : "ws://" + oSTOMMQConfig.host + ":15674/ws",
            username    : oSTOMMQConfig.user,
            password    : oSTOMMQConfig.password,
            vHost       : oSTOMMQConfig.vhost
        };

        // Update Status For Delete Qname Parameter
        var poUpdateStaDelQnameParams = {
            ptDocTableName: "TCNTPdtAdjStkHD",
            ptDocFieldDocNo: "FTAjhDocNo",
            ptDocFieldStaApv: "FTAjhStaPrcStk",
            ptDocFieldStaDelMQ: "FTAjhStaDelMQ",
            ptDocStaDelMQ: "1",
            ptDocNo: tDocNo
        };

        // Callback Page Control(function)
        var poCallback = {
            tCallPageEdit: 'JSvASTCallPageEdit',
            tCallPageList: 'JSvASTCallPageList'
        };

	    //Check Show Progress %
        if (tDocNo != '' && (tStaApv == 2 || tStaPrcStk == 2)) { // 2 = Processing
            FSxCMNRabbitMQMessage(poDocConfig, poMqConfig, poUpdateStaDelQnameParams, poCallback);	
		}

        //Check Delete MQ SubScrib
        if (tStaApv == 1 && tStaPrcStk == 1 && tStaDelMQ == '') { // Qname removed ?
            // console.log('DelMQ:');
            // Delete Queue Name Parameter
            var poDelQnameParams = {
				ptPrefixQueueName: tPrefix,
				ptBchCode: tUsrBchCode,
				ptDocNo: tDocNo,
				ptUsrCode: tUsrApv
			};    
            FSxCMNRabbitMQDeleteQname(poDelQnameParams);
            FSxCMNRabbitMQUpdateStaDeleteQname(poUpdateStaDelQnameParams);
        }*/
    });
     
    var tUsrLevel       = "<?php echo $this->session->userdata("tSesUsrLevel"); ?>";
    var tBchCodeMulti   = "<?php echo $this->session->userdata("tSesUsrBchCodeMulti"); ?>";
    var nCountBch       = "<?php echo $this->session->userdata("nSesUsrBchCount"); ?>";
    var tWhere          = "";

    if(tUsrLevel != "HQ"){
        tWhere = " AND TCNMBranch.FTBchCode IN ("+tBchCodeMulti+") ";
    }else{  //สำนักงานใหญ่
        if($('#ohdASTADCode').val() == '' || $('#ohdASTADCode').val() == null){
            tWhere += "";
        }else{
            tWhere += " AND (TCNMBranch.FTAgnCode = " + $('#ohdASTADCode').val() +" OR ISNULL(TCNMBranch.FTAgnCode,'') = '' )";
        }
    }

    var oBrowse_BCH = {
        Title   : ['company/branch/branch','tBCHTitle'],
        Table   : {Master:'TCNMBranch',PK:'FTBchCode',PKName:'FTBchName'},
        Join    : {
            Table   : ['TCNMBranch_L','TCNMWaHouse_L'],
            On      : ['TCNMBranch_L.FTBchCode = TCNMBranch.FTBchCode AND TCNMBranch_L.FNLngID ='+nLangEdits,
                        'TCNMBranch.FTWahCode = TCNMWaHouse_L.FTWahCode AND TCNMBranch.FTBchCode = TCNMWaHouse_L.FTBchCode AND TCNMWaHouse_L.FNLngID ='+nLangEdits,
            ]
        },
        Where:{
            Condition : [tWhere]
        },
        GrideView:{
            ColumnPathLang : 'company/branch/branch',
            ColumnKeyLang : ['tBCHCode','tBCHName',''],
            ColumnsSize     : ['15%','75%',''],
            WidthModal      : 50,
            DataColumns  : ['TCNMBranch.FTBchCode','TCNMBranch_L.FTBchName','TCNMWaHouse_L.FTWahCode','TCNMWaHouse_L.FTWahName'],
            DataColumnsFormat : ['',''],
            DisabledColumns   : [2,3],
            Perpage   : 10,
            OrderBy   : ['TCNMBranch.FTBchCode DESC'],
        },
        CallBack:{
            ReturnType : 'S',
            Value  : ["oetASTBchCode","TCNMBranch.FTBchCode"],
            Text  : ["oetASTBchName","TCNMBranch_L.FTBchName"],
        },
        NextFunc    :   {
            FuncName    :   'JSxSetDefauleWahouse',
            ArgReturn   :   ['FTWahCode','FTWahName']
        }
    }

    // สาขา 
    $('#obtBrowseASTBCH').click(function(){
        var tUsrLevel = "<?php echo $this->session->userdata("tSesUsrLevel"); ?>";
        var tBchCodeMulti = "<?php echo $this->session->userdata("tSesUsrBchCodeMulti"); ?>";
        var nCountBch = "<?php echo $this->session->userdata("nSesUsrBchCount"); ?>";
        var tWhere = "";
        if(tUsrLevel != "HQ"){
            tWhere = " AND TCNMBranch.FTBchCode IN ("+tBchCodeMulti+") ";
        }else{  //สำนักงานใหญ่
            if($('#ohdASTADCode').val() == '' || $('#ohdASTADCode').val() == null){
                tWhere += "";
            }else{
                tWhere += " AND (TCNMBranch.FTAgnCode = " + $('#ohdASTADCode').val() +" OR ISNULL(TCNMBranch.FTAgnCode,'') = '' )";
            }
        }
        // Option Branch
        oBrowse_BCH = {
            Title   : ['company/branch/branch','tBCHTitle'],
            Table   : {Master:'TCNMBranch',PK:'FTBchCode',PKName:'FTBchName'},
            Join    : {
                Table   : ['TCNMBranch_L','TCNMWaHouse_L'],
                On      : ['TCNMBranch_L.FTBchCode = TCNMBranch.FTBchCode AND TCNMBranch_L.FNLngID ='+nLangEdits,
                            'TCNMBranch.FTWahCode = TCNMWaHouse_L.FTWahCode AND TCNMBranch.FTBchCode = TCNMWaHouse_L.FTBchCode AND TCNMWaHouse_L.FNLngID ='+nLangEdits,
                ]
            },
            Where:{
                Condition : [tWhere]
            },
            GrideView:{
                ColumnPathLang : 'company/branch/branch',
                ColumnKeyLang : ['tBCHCode','tBCHName',''],
                ColumnsSize     : ['15%','75%',''],
                WidthModal      : 50,
                DataColumns  : ['TCNMBranch.FTBchCode','TCNMBranch_L.FTBchName','TCNMWaHouse_L.FTWahCode','TCNMWaHouse_L.FTWahName'],
                DataColumnsFormat : ['',''],
                DisabledColumns   : [2,3],
                Perpage   : 10,
                OrderBy   : ['TCNMBranch.FTBchCode DESC'],
            },
            CallBack:{
                ReturnType : 'S',
                Value  : ["oetASTBchCode","TCNMBranch.FTBchCode"],
                Text  : ["oetASTBchName","TCNMBranch_L.FTBchName"],
            },
            NextFunc    :   {
                FuncName    :   'JSxSetDefauleWahouse',
                ArgReturn   :   ['FTWahCode','FTWahName']
            }
        }
        // Option Branch
        JCNxBrowseData('oBrowse_BCH');

    });

    function JSxSetDefauleWahouse(ptData){

        $('#obtASTBrowseMer').attr('disabled',true);
        $('#oetASTMerCode').val('');
        $('#oetASTMerName').val('');

        $('#obtASTBrowseShp').attr('disabled',true);
        $('#oetASTShopCode').val('');
        $('#oetASTShopName').val('');

        $('#obtASTBrowsePos').attr('disabled',true);
        $('#oetASTPosCode').val('');
        $('#oetASTPosName').val('');

        $.ajax({
            type : "POST",
            url : "docAdjStkUpdateAdj",
            data :{
                tBchCode        : $("#oetASTBchCode").val(),
                tDocCode        : $("#oetASTDocNo").val(),
                tWahCode        : $("#oetASTWahCode").val()
            },
            catch : false,
            timeout : 0,
            success : function (tResult){
                JSvASTLoadPdtDataTableHtml(1);
            },
            error: function (jqXHR,textStatus,errorThrown){
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
        
        if(ptData == '' || ptData == 'NULL'){
            $('#oetASTWahCode').val('');
            $('#oetASTWahName').val('');
        }else{
            var tResult = JSON.parse(ptData);
            $('#oetASTWahCode').val(tResult[0]);
            $('#oetASTWahName').val(tResult[1]);

            $('#obtASTBrowseMer').attr('disabled',false);
        }

        

    }

    /////////////// Browse Filter Product ///////////////

    $('#obtAdjStkFilterDataCondition').click(function(){
        if( $('#oetASTWahCode').val() != '' ){
            $('#odvAdjStkFilterDataCondition').modal('show');

            //Clear ค่า
            $('#oetASTFilterPdtNameFrom , #oetASTFilterPdtNameTo').val('');
            $('#oetASTFilterPdtCodeFrom , #oetASTFilterPdtCodeTo').val('');

            //Clear ค่า
            $('#oetASTFilterSplNameFrom , #oetASTFilterSplNameTo').val('');
            $('#oetASTFilterSplCodeFrom , #oetASTFilterSplCodeTo').val('');

            //Clear ค่า
            $('#oetASTFilterPgpName').val('');
            $('#oetASTFilterPgpCode').val('');

            //Clear ค่า
            $('#oetASTFilterPlcName').val('');
            $('#oetASTFilterPlcCode').val('');
        }else{
            alert('กรุณาเลือกคลัง');
        }
    });

    $('#obtAdjStkConfirmFilter').click(function(){
        JSxASTEventAddProducts();
    });

    $('#obtASTBrowseFilterProductFrom').click(function(){
        JSxAdjStkBrowsePdt('from');
    });

    $('#obtASTBrowseFilterProductTo').click(function(){
        JSxAdjStkBrowsePdt('to');
    });

    //Create By : Napat(Jame) 2020/07/29
    function JSxAdjStkBrowsePdt(ptType){
        $('#odvModalDOCPDT').modal({backdrop: 'static', keyboard: false});
        if(ptType == 'from'){
            tNextFunc = 'JSxAdjStkBrowsePdtFrom';
        }else{
            tNextFunc = 'JSxAdjStkBrowsePdtTo';
        }
        // if(localStorage.getItem("Ada.ProductListCenter") === null){
        //     localStorage.setItem("Ada.ProductListCenter",true);
            var dTime               = new Date();
            var dTimelocalStorage   = dTime.getTime();

            var nPdtStkCondition = $("input[name='orbASTPdtStkCondition']:checked").val();
            switch(nPdtStkCondition){
                case '2':
                    tPdtStkCondition = '1';
                    break;
                case '3':
                    tPdtStkCondition = '2';
                    break;
                default:
                    tPdtStkCondition = 'ALL';
            }

            $.ajax({
                type: "POST",
                url: "BrowseDataPDT",
                data: {
                    'Qualitysearch'   : ['SUP','NAMEPDT','CODEPDT','FromToBCH','FromToSHP','FromToPGP','FromToPTY'],
                    'PriceType'       : ['Pricesell'],
                    'SelectTier'      : ['PDT'],//PDT, Barcode
                    // 'Elementreturn'   : ['oetASTFilterPdtCodeFrom','oetASTFilterPdtNameFrom'],
                    'ShowCountRecord' : 10,
                    'NextFunc'        : tNextFunc,
                    'ReturnType'      : 'S', //S = Single M = Multi
                    'SPL'             : ['',''],
                    'BCH'             : [$('#oetASTBchCode').val(),$('#oetASTBchName').val()],//Code, Name
                    'MER'             : [$('#oetASTMerCode').val(),''],
                    'SHP'             : [$('#oetASTShopCode').val(),''],
                    'TimeLocalstorage': dTimelocalStorage,
                    'aAlwPdtType'     : ['T1','T3','T4','T5','T6','S2','S3','S4'],

                    'WAH'             : [$('#oetASTWahCode').val(), $('#oetASTWahName').val()], // คลังที่ต้องการตรวจสอบ
                    'tStaControlStk'  : tPdtStkCondition // ตรวจสอบสินค้าคงคลัง : All = แสดงทั้งหมด, 1 = เฉพาะสินค้าที่มีสต็อค ,2 = เฉพาะสินค้าที่ไม่มีสต็อค

                },
                cache: false,
                timeout: 0,
                success: function(tResult){ 
                    $('#odvModalDOCPDT').modal({ show: true });

                    //remove localstorage
                    localStorage.removeItem("LocalItemDataPDT");
                    $('#odvModalsectionBodyPDT').html(tResult);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        // }else{
        //     $('#odhEleNameNextFunc').val(tNextFunc);
        //     $('#odvModalDOCPDT').modal({ show: true });
        // }
    }

    //Create By : Napat(Jame) 2020/07/29
    function JSxAdjStkBrowsePdtFrom(poPdtData){
        var aDataPdt = JSON.parse(poPdtData);
        var tPdtCode = aDataPdt[0]['packData']['PDTCode'];
        var tPdtName = aDataPdt[0]['packData']['PDTName'];

        $('#oetASTFilterPdtCodeFrom').val(tPdtCode);
        $('#oetASTFilterPdtNameFrom').val(tPdtName);

        if($('#oetASTFilterPdtCodeTo').val() == ''){
            $('#oetASTFilterPdtCodeTo').val(tPdtCode);
            $('#oetASTFilterPdtNameTo').val(tPdtName);
        }
        
    }

    //Create By : Napat(Jame) 2020/07/29
    function JSxAdjStkBrowsePdtTo(poPdtData){
        var aDataPdt = JSON.parse(poPdtData);
        var tPdtCode = aDataPdt[0]['packData']['PDTCode'];
        var tPdtName = aDataPdt[0]['packData']['PDTName'];

        $('#oetASTFilterPdtCodeTo').val(tPdtCode);
        $('#oetASTFilterPdtNameTo').val(tPdtName);

        if($('#oetASTFilterPdtCodeFrom').val() == ''){
            $('#oetASTFilterPdtCodeFrom').val(tPdtCode);
            $('#oetASTFilterPdtNameFrom').val(tPdtName);
        }
    }

    // Browse Supplier
    $('#obtASTBrowseFilterSupplierFrom').click(function(){
        JSxASTBrowseFilterSupplier('from');
    });

    $('#obtASTBrowseFilterSupplierTo').click(function(){
        JSxASTBrowseFilterSupplier('to');
    });

    function JSxASTBrowseFilterSupplier(ptType){

        var tValue = "";
        var tText  = "";

        if(ptType == 'from'){
            tValue  = 'oetASTFilterSplCodeFrom';
            tText   = 'oetASTFilterSplNameFrom';
        }else{
            tValue  = 'oetASTFilterSplCodeTo';
            tText   = 'oetASTFilterSplNameTo';
        }

        let tAgnCode     = '<?=$this->session->userdata("tSesUsrAgnCode");?>';
        let tCondition   = '';
        if(tAgnCode != ''){
            tCondition += " AND TCNMSpl.FTAgnCode = '"+tAgnCode+"' ";
        }

        oASTFilterBrowseSpl = {
            Title: ['supplier/supplier/supplier', 'tSPLTitle'],
            Table: {Master:'TCNMSpl', PK:'FTSplCode'},
            Join: {
                Table: ['TCNMSpl_L'],
                On: ['TCNMSpl.FTSplCode = TCNMSpl_L.FTSplCode AND TCNMSpl_L.FNLngID = '+nLangEdits]
            },
            Where: {
                Condition: [tCondition]
            },
            GrideView:{
                ColumnPathLang: 'supplier/supplier/supplier',
                ColumnKeyLang: ['tSPLTBCode', 'tSPLTBName'],
                ColumnsSize: ['15%', '75%'],
                WidthModal: 50,
                DataColumns: ['TCNMSpl.FTSplCode', 'TCNMSpl_L.FTSplName'],
                DataColumnsFormat: ['', ''],
                DisabledColumns: [],
                Perpage: 10,
                OrderBy: ['TCNMSpl.FDCreateOn DESC']
            },
            CallBack:{
                ReturnType  : 'S',
                Value       : [tValue, "TCNMSpl.FTSplCode"],
                Text        : [tText, "TCNMSpl_L.FTSplName"]
            },
            NextFunc:{
                FuncName: 'JSxASTBrowseFilterSupplierNextFunc',
                ArgReturn: ['FTSplCode', 'FTSplName']
            },
            RouteAddNew: 'supplier',
            BrowseLev: 2
        };
        JCNxBrowseData('oASTFilterBrowseSpl');
    }

    function JSxASTBrowseFilterSupplierNextFunc(poArgReturn){
        if(poArgReturn != "NULL"){
            var aReturn = JSON.parse(poArgReturn);
            if($('#oetASTFilterSplCodeFrom').val() == ''){
                $('#oetASTFilterSplCodeFrom').val(aReturn[0]);
                $('#oetASTFilterSplNameFrom').val(aReturn[1]);
            }
            if($('#oetASTFilterSplCodeTo').val() == ''){
                $('#oetASTFilterSplCodeTo').val(aReturn[0]);
                $('#oetASTFilterSplNameTo').val(aReturn[1]);
            }
        }else{
            $('#oetASTFilterSplCodeFrom').val('');
            $('#oetASTFilterSplNameFrom').val('');

            $('#oetASTFilterSplCodeTo').val('');
            $('#oetASTFilterSplNameTo').val('');
        }
        $('#odvAdjStkFilterDataCondition').modal('show');
    }

    // Browse Product Group
    $('#obtASTBrowseFilterProductGroup').click(function(){
        JSxASTBrowseFilterPdtGrp();
    });

    function JSxASTBrowseFilterPdtGrp(ptType){

        // $('#odvAdjStkFilterDataCondition').modal('hide');

        let tValue = "oetASTFilterPgpCode";
        let tText  = "oetASTFilterPgpName";

        let tAgnCode     = '<?=$this->session->userdata("tSesUsrAgnCode");?>';
        let tCondition   = '';
        if(tAgnCode != ''){
            tCondition += " AND TCNMPdtGrp.FTAgnCode = '"+tAgnCode+"' ";
        }

        oASTFilterBrowsePdtGrp = {
            Title: ['product/pdtgroup/pdtgroup', 'tPGPTitle'],
            Table: {Master:'TCNMPdtGrp', PK:'FTPgpChain'},
            Join: {
                Table: ['TCNMPdtGrp_L'],
                On: ['TCNMPdtGrp.FTPgpChain = TCNMPdtGrp_L.FTPgpChain AND TCNMPdtGrp_L.FNLngID = '+nLangEdits]
            },
            Where: {
                Condition: [tCondition]
            },
            GrideView:{
                ColumnPathLang: 'product/pdtgroup/pdtgroup',
                ColumnKeyLang: ['tPGPTBCode', 'tPGPTBName'],
                ColumnsSize: ['15%', '75%'],
                WidthModal: 50,
                DataColumns: ['TCNMPdtGrp.FTPgpChain', 'TCNMPdtGrp_L.FTPgpName'],
                DataColumnsFormat: ['', ''],
                DisabledColumns: [],
                Perpage: 10,
                OrderBy: ['TCNMPdtGrp.FDCreateOn DESC']
            },
            CallBack:{
                ReturnType  : 'S',
                Value       : [tValue, "TCNMPdtGrp.FTPgpChain"],
                Text        : [tText, "TCNMPdtGrp_L.FTPgpName"]
            },
            NextFunc:{
                FuncName: 'JSxASTBrowseFilterPdtGrpNextFunc',
                ArgReturn: ['FTPgpChain', 'FTPgpName']
            },
            // RouteAddNew: 'pdtgroup',
            // BrowseLev: 2
        };
        JCNxBrowseData('oASTFilterBrowsePdtGrp');
    }

    function JSxASTBrowseFilterPdtGrpNextFunc(poArgReturn){
        $('#odvAdjStkFilterDataCondition').modal('show');
    }

    // Browse Product Location
    $('#obtASTBrowseFilterProductLocation').click(function(){
        JSxASTBrowseFilterPdtLoc();
    });

    function JSxASTBrowseFilterPdtLoc(ptType){

        // $('#odvAdjStkFilterDataCondition').modal('hide');

        let tValue = "oetASTFilterPlcCode";
        let tText  = "oetASTFilterPlcName";

        oASTFilterBrowsePdtGrp = {
            Title: ['product/pdtlocation/pdtlocation', 'tLOCTitle'],
            Table: {Master:'TCNMPdtLoc', PK:'FTPlcCode'},
            Join: {
                Table: ['TCNMPdtLoc_L'],
                On: ['TCNMPdtLoc.FTPlcCode = TCNMPdtLoc_L.FTPlcCode AND TCNMPdtLoc_L.FNLngID = '+nLangEdits]
            },
            GrideView:{
                ColumnPathLang: 'product/pdtlocation/pdtlocation',
                ColumnKeyLang: ['tLOCFrmLocCode', 'tLOCFrmLocName'],
                ColumnsSize: ['15%', '75%'],
                WidthModal: 50,
                DataColumns: ['TCNMPdtLoc.FTPlcCode', 'TCNMPdtLoc_L.FTPlcName'],
                DataColumnsFormat: ['', ''],
                DisabledColumns: [],
                Perpage: 5,
                OrderBy: ['TCNMPdtLoc.FDCreateOn DESC']
            },
            CallBack:{
                ReturnType  : 'S',
                Value       : [tValue, "TCNMPdtLoc.FTPlcCode"],
                Text        : [tText, "TCNMPdtLoc_L.FTPlcName"]
            },
            NextFunc:{
                FuncName: 'JSxASTBrowseFilterPdtPlcNextFunc',
                ArgReturn: ['FTPlcCode', 'FTPlcName']
            },
            // RouteAddNew: 'pdtgroup',
            // BrowseLev: 1
        };
        JCNxBrowseData('oASTFilterBrowsePdtGrp');
    }

    function JSxASTBrowseFilterPdtPlcNextFunc(poArgReturn){
        $('#odvAdjStkFilterDataCondition').modal('show');
    }

    $('#ocbASTUsePdtStkCard').change(function() {
        if(this.checked){
            $('.xWASTDisabledOnCheckUsePdtStkCard').attr('disabled',false);
        }else{
            $('.xWASTDisabledOnCheckUsePdtStkCard').attr('disabled',true);
        }
    });

    /////////////// Browse Filter Product ///////////////

    function JSnASTPrintDoc(){
        var aInfor = [
            { "Lang"        : '<?=FCNaHGetLangEdit(); ?>'},
            { "ComCode"     : '<?=FCNtGetCompanyCode(); ?>'},
            { "BranchCode"  : '<?=FCNtGetAddressBranch($tASTBchCode); ?>' },
            { "DocCode"     : $("#oetASTDocNo").val() }, // เลขที่เอกสาร
            { "DocBchCode"  : '<?=$tASTBchCode;?>'}
        ];
        window.open($("#ohdBaseUrl").val() + "formreport/ALLMPdtBillChkStk?infor=" + JCNtEnCodeUrlParameter(aInfor), '_blank');
    }

    //////////////////////////////////////////////////////////////// เลือกตัวแทนขาย ////////////////////////////////////////////////////////////
    $('#obtASTBrowseAgency').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxCheckPinMenuClose();
            window.oASTBrowseAgnOption   = undefined;
            oASTBrowseAgnOption          = oAgnOption({
                'tReturnInputCode'  : 'ohdASTADCode',
                'tReturnInputName'  : 'ohdASTADName',
                'tNextFuncName'     : 'JSxIVSetConditionAfterSelectAGN'
            });
            JCNxBrowseData('oASTBrowseAgnOption');
        }else{
            JCNxShowMsgSessionExpired();
        }
    });

    //หลังจากเลือก
    function JSxIVSetConditionAfterSelectAGN(poDataNextFunc){
        var aData;
        if (poDataNextFunc  != "NULL") {
            $('#oetASTBchName , #oetASTBchCode').val('');
        }
    }

    //กดเลือกบาร์โค๊ด
    function  JSxASTEventScan(e,elem){
        var tValue = $(elem).val();
        JSxCheckPinMenuClose();
        if(tValue.length === 0){
        }else{
            $(elem).attr('readonly',true);
            JSxASTEventSearchPdtOnScan(tValue);
            $(elem).val('');
        }
        e.preventDefault();
    }

    //ค้นหาบาร์โค๊ด
    function JSxASTEventSearchPdtOnScan(ptTextScan){
        // var tPXSplCode = $('#oetPXFrmSplCode').val();

        // var tWhereCondition = "";
        // if( tPXSplCode != "" ){
        //     tWhereCondition = " AND FTPdtSetOrSN IN('1','2') ";
        // }

        $.ajax({
            type : "POST",
            url : "BrowseDataPDTTableCallView",
            data :{
                Qualitysearch    : [],
                ReturnType       : "M",
                aPriceType       : ["Cost","tCN_Cost","Company","1"],
                NextFunc         : "",
                SelectTier       : ["Barcode"],
                SPL              : '',
                BCH              : $("#oetASTBchCode").val(),
                MCH              : '',
                SHP              : '',
                tInpSesSessionID : $('#ohdSesSessionID').val(),
                tInpSesUsrLevel  : $('#ohdSesUsrLevel').val(),
                tInpSesUsrBchCom : $('#ohdSesUsrBchCom').val(),
                tInpLangEdit     : $('#ohdLangEdit').val(),
                Where            : [''],
                tTextScan        : ptTextScan,
            },
            catch : false,
            timeout : 0,
            success : function (tResult){
                JCNxCloseLoading();
                var oText = JSON.parse(tResult);
                
                if(oText == '800'){
                    $('#oetAdjStkScan').attr('readonly',false);
                    $('#odvASTModalPDTNotFound').modal('show');
                    $('#oetAdjStkScan').val('');
                }else{
                    // พบสินค้ามีหลายบาร์โค้ด
                    if(oText.length > 1){
                        $('#odvASTModalPDTMoreOne').modal('show');
                        $('#odvASTModalPDTMoreOne .xCNTablePDTMoreOne tbody').html('');

                        for(i=0; i<oText.length; i++){
                            var aNewReturn      = JSON.stringify(oText[i]);
                            var tTest = "["+aNewReturn+"]";
                            var oEncodePackData = window.btoa(unescape(encodeURIComponent(tTest)));
                            var tHTML = "<tr class='xCNColumnPDTMoreOne"+i+" xCNColumnPDTMoreOne' data-information='"+oEncodePackData+"' style='cursor: pointer;'>";
                                tHTML += "<td>"+oText[i].pnPdtCode+"</td>";
                                tHTML += "<td>"+oText[i].packData.PDTName+"</td>";
                                tHTML += "<td>"+oText[i].packData.PUNName+"</td>";
                                tHTML += "<td>"+oText[i].ptBarCode+"</td>";
                                tHTML += "</tr>";
                            $('#odvASTModalPDTMoreOne .xCNTablePDTMoreOne tbody').append(tHTML);
                        }

                        //เลือกสินค้า
                        $('.xCNColumnPDTMoreOne').off();

                        //คลิกได้เลย
                        $('.xCNColumnPDTMoreOne').on('click',function(e){
                            //เลือกสินค้าแบบตัวเดียว
                            $('.xCNColumnPDTMoreOne').removeClass('xCNActivePDT');
                            $('.xCNColumnPDTMoreOne').children().attr('style', 'background-color:transparent !important; color:#232C3D !important;');
                            // $('.xCNColumnPDTMoreOne').children(':last-child').css('text-align','right');

                            $(this).addClass('xCNActivePDT');
                            $(this).children().attr('style', 'background-color:#1866ae !important; color:#FFF !important;');
                            // $(this).children().last().css('text-align', 'right');
                        });

                    }else{
                        //มีตัวเดียว
                        console.log(oText[0]['packData']);

                        var tPdtCode    = oText[0]['packData'].PDTCode;
                        var tBarCode    = oText[0]['packData'].Barcode;
                        JSxASTEventAddProductsByBarCode(tPdtCode,tBarCode);

                        // var tPdtCode = oText[0]['packData']['PDTCode'];
                        // $('#oetASTFilterPdtCodeFrom').val(tPdtCode);
                        // $('#oetASTFilterPdtCodeTo').val(tPdtCode);
                        // JSxASTEventAddProducts();
                    }
                }
            },
            error: function (jqXHR,textStatus,errorThrown){
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    }

    //เลือกสินค้า กรณีพบมากกว่าหนึ่งตัว
    function JCNxConfirmPDTMoreOne($ptType) {
        if ($ptType == 1) {

            if($("#odvASTModalPDTMoreOne .xCNTablePDTMoreOne tbody .xCNActivePDT").length == 0){
                $('#oetAdjStkScan').attr('readonly', false);
                $('#oetAdjStkScan').val('');
            }else{
                $("#odvASTModalPDTMoreOne .xCNTablePDTMoreOne tbody .xCNActivePDT").each(function(index) {
                    var tJSON       = decodeURIComponent(escape(window.atob($(this).attr('data-information'))));
                    var aPackData   = JSON.parse(tJSON);
                    var tPdtCode    = aPackData[0].pnPdtCode;
                    var tBarCode    = aPackData[0].ptBarCode;
                    JSxASTEventAddProductsByBarCode(tPdtCode,tBarCode);
                });
            }
        } else {
            $('#oetAdjStkScan').attr('readonly', false);
            $('#oetAdjStkScan').val('');
        }
    }

</script>