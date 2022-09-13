<script type="text/javascript">
    var nLangEdits  = <?php echo $this->session->userdata("tLangEdit"); ?>;

    $(document).ready(function(){
        $('.selectpicker').selectpicker();

        $('.xCNDatePicker').datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
        });

        // Doc Date From
        $('#obtPOAdvSearchDocDateForm').unbind().click(function(){
            $('#oetPOAdvSearcDocDateFrom').datepicker('show');
        });

        // Doc Date To
        $('#obtPOAdvSearchDocDateTo').unbind().click(function(){
            $('#oetPOAdvSearcDocDateTo').datepicker('show');
        });
        
    });

    // Advance search Display control
    $('#obtPOAdvanceSearch').unbind().click(function(){
        if($('#odvPOAdvanceSearchContainer').hasClass('hidden')){
            $('#odvPOAdvanceSearchContainer').removeClass('hidden').hide().slideDown(500);
        }else{
            $("#odvPOAdvanceSearchContainer").slideUp(500,function() {
                $(this).addClass('hidden');
            });
        }
    });

    var nCountBch 		= "<?php echo $this->session->userdata("nSesUsrBchCount"); ?>";
    if(nCountBch == 1){
        $('#obtPOAdvSearchBrowseBchFrom').attr('disabled',true);
        $('#obtPOAdvSearchBrowseBchTo').attr('disabled',true);
    }
    // Option Branch
    var oPOBrowseBranch = function(poReturnInput){
        var tInputReturnCode    = poReturnInput.tReturnInputCode;
        var tInputReturnName    = poReturnInput.tReturnInputName;
        var tWhereModal = "";
        var tUsrLevel   = "<?php echo $this->session->userdata("tSesUsrLevel");?>";
        var tBchMulti 	= "<?php echo $this->session->userdata("tSesUsrBchCodeMulti"); ?>";
        if(tUsrLevel != "HQ"){
            tWhereModal 	+= " AND TCNMBranch.FTBchCode IN ("+tBchMulti+") ";
        }
        var oOptionReturn       = {
            Title : ['company/branch/branch','tBCHTitle'],
            Table : {Master:'TCNMBranch',PK:'FTBchCode'},
            Join :{
                Table : ['TCNMBranch_L'],
                On : ['TCNMBranch_L.FTBchCode = TCNMBranch.FTBchCode AND TCNMBranch_L.FNLngID = '+nLangEdits,]
            },
            Where : {
                Condition : [tWhereModal]
            },
            GrideView:{
                ColumnPathLang      : 'company/branch/branch',
                ColumnKeyLang       : ['tBCHCode','tBCHName'],
                ColumnsSize         : ['15%','75%'],
                WidthModal          : 50,
                DataColumns         : ['TCNMBranch.FTBchCode','TCNMBranch_L.FTBchName'],
                DataColumnsFormat   : ['',''],
                Perpage             : 20,
                OrderBy             : ['TCNMBranch_L.FTBchName ASC'],
            },
            CallBack:{
                ReturnType	: 'S',
                Value		: [tInputReturnCode,"TCNMBranch.FTBchCode"],
                Text		: [tInputReturnName,"TCNMBranch_L.FTBchName"],
            },
        }
        return oOptionReturn;
    };

    // Event Browse Branch From
    $('#obtPOAdvSearchBrowseBchFrom').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxCheckPinMenuClose();
            window.oPOBrowseBranchFromOption  = oPOBrowseBranch({
                'tReturnInputCode'  : 'oetPOAdvSearchBchCodeFrom',
                'tReturnInputName'  : 'oetPOAdvSearchBchNameFrom'
            });
            JCNxBrowseData('oPOBrowseBranchFromOption');
        }else{
            JCNxShowMsgSessionExpired();
        }
    });

    // Event Browse Agency To
    $('#obtPOAdvSearchBrowseAgn').click(function() {
        JSxCheckPinMenuClose();
        window.oBrowseAgencyOption = undefined;
        oBrowseAgencyOption = oBrowseAgency({
            'tReturnCode' : 'oetPOAdvSearchAgnCode',
            'tReturnName' : 'oetPOAdvSearchAgnName'
        });
        JCNxBrowseData('oBrowseAgencyOption');
    });

    var oBrowseAgency = function(poDataFnc) {
        var tReturnCode = poDataFnc.tReturnCode;
        var tReturnName = poDataFnc.tReturnName;
        var oOptionReturn = {
            Title: ['ticket/agency/agency', 'tAggTitle'],
            Table: {
                Master  : 'TCNMAgency',
                PK      : 'FTAgnCode'
            },
            Join: {
                Table: ['TCNMAgency_L'],
                On: [
                    'TCNMAgency_L.FTAgnCode = TCNMAgency.FTAgnCode AND TCNMAgency_L.FNLngID = ' + nLangEdits
                ]
            },
            // Where: {
            //     Condition: [tWherePosType]
            // },
            GrideView: {
                ColumnPathLang: 'ticket/agency/agency',
                ColumnKeyLang: ['tAggCode', 'tAggName'],
                ColumnsSize: ['15%', '75%'],
                WidthModal: 50,
                DataColumns: ['TCNMAgency.FTAgnCode', 'TCNMAgency_L.FTAgnName'],
                DataColumnsFormat: ['', ''],
                Perpage: 5,
                OrderBy: ['TCNMAgency.FDCreateOn'],
                SourceOrder: "DESC"
            },
            CallBack: {
                ReturnType  : 'S',
                Value       : [tReturnCode, "TCNMAgency.FTAgnCode"],
                Text        : [tReturnName, "TCNMAgency_L.FTAgnName"],
            },
            // RouteAddNew: 'salemachine',
            // BrowseLev: nStaWahBrowseType
            // DebugSQL: true,
        }
        return oOptionReturn;
    }

    // Event Browse Branch To
    $('#obtPOAdvSearchBrowseBchTo').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxCheckPinMenuClose();
            window.oPOBrowseBranchToOption  = oPOBrowseBranch({
                'tReturnInputCode'  : 'oetPOAdvSearchBchCodeTo',
                'tReturnInputName'  : 'oetPOAdvSearchBchNameTo'
            });
            JCNxBrowseData('oPOBrowseBranchToOption');
        }else{
            JCNxShowMsgSessionExpired();
        }
    });
    
    $('#obtPOSearchReset').unbind().click(function(){
        var nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxPOClearAdvSearchData();
        }else{
            JCNxShowMsgSessionExpired();
        }
    });

    // Functionality: ฟังก์ชั่นล้างค่า Input Advance Search
    // Parameters: Button Event Click
    // Creator: 19/06/2019 Wasin(Yoshi)
    // Last Update: -
    // Return: Clear Value In Input Advance Search
    // ReturnType: -
    function JSxPOClearAdvSearchData(){
        let nStaSession = JCNxFuncChkSessionExpired();
        if(typeof nStaSession !== "undefined" && nStaSession == 1){
            var tFilterAgnCode  = '';
            var tFilterAgnName  = '';
            let tChkAgnCodeAD   = '<?=$this->session->userdata("tSesUsrAgnCode")?>';
            if(tChkAgnCodeAD != ""){
                tFilterAgnCode  = $('#oetPOAdvSearchAgnCode').val();
                tFilterAgnName  = $('#oetPOAdvSearchAgnName').val();
            }
            $('#ofmPOFromSerchAdv').find('input').val('');
            $('#ofmPOFromSerchAdv').find('select').val(0).selectpicker("refresh");
            
            // Set Agn Code Default
            $('#oetPOAdvSearchAgnCode').val(tFilterAgnCode);
            $('#oetPOAdvSearchAgnName').val(tFilterAgnName);

            JSvPOCallPageDataTable();
        }else{
            JCNxShowMsgSessionExpired();
        }
    }

    // ====================================================  From Search Data Page Purchase Invioce ====================================================
        $('#oetPOSearchAllDocument').keyup(function(event){
            var nCodeKey    = event.which;
            if(nCodeKey == 13){
                event.preventDefault();
                var nStaSession = JCNxFuncChkSessionExpired();
                if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
                    JSvPOCallPageDataTable();
                }else{
                    JCNxShowMsgSessionExpired();
                }
            }
        });
        
        $('#obtPOSerchAllDocument').unbind().click(function(){
            var nStaSession = JCNxFuncChkSessionExpired();
            if(typeof nStaSession !== "undefined" && nStaSession == 1){
                JSvPOCallPageDataTable();
            }else{
                JCNxShowMsgSessionExpired();
            }
        });

        $("#obtPOAdvSearchSubmitForm").unbind().click(function(){
            var nStaSession = JCNxFuncChkSessionExpired();
            if(typeof nStaSession !== "undefined" && nStaSession == 1){
                JSvPOCallPageDataTable();
            }else{
                JCNxShowMsgSessionExpired();
            }
        });

    // =================================================================================================================================================

    // Option Browse Supplier
    var oPOBrowsSpl = function(poDataFnc){
        var tInputReturnCode    = poDataFnc.tReturnInputCode;
        var tInputReturnName    = poDataFnc.tReturnInputName;
        var tParamsAgnCode      = poDataFnc.tParamsAgnCode;
        var nPODecimalShow      = $('#ohdPOODecimalShow').val();
        if( tParamsAgnCode != "" ){
            tWhereAgency    = " AND ( TCNMSpl.FTAgnCode = '"+tParamsAgnCode+"' OR ISNULL(TCNMSpl.FTAgnCode,'') = '' ) ";
        } else {
            tWhereAgency    = "";
        }
        var oOptionReturn   = {
            Title   : ['supplier/supplier/supplier', 'tSPLTitle'],
            Table   : {Master:'TCNMSpl', PK:'FTSplCode'},
            Join    : {
                Table   : ['TCNMSpl_L', 'TCNMSplCredit'],
                On      : [
                    'TCNMSpl_L.FTSplCode    = TCNMSpl.FTSplCode AND TCNMSpl_L.FNLngID = '+nLangEdits,
                    'TCNMSpl_L.FTSplCode    = TCNMSplCredit.FTSplCode'
                ]
            },
            Where   : {
                Condition   : ["AND TCNMSpl.FTSplStaActive = '1' " + tWhereAgency]
            },
            GrideView   : {
                ColumnPathLang: 'supplier/supplier/supplier',
                ColumnKeyLang: ['tSPLTBCode', 'tSPLTBName'],
                ColumnsSize: ['15%', '75%'],
                WidthModal: 50,
                DataColumns: ['TCNMSpl.FTSplCode', 'TCNMSpl_L.FTSplName', 'TCNMSplCredit.FNSplCrTerm', 'TCNMSplCredit.FCSplCrLimit', 'TCNMSpl.FTSplStaVATInOrEx', 'TCNMSplCredit.FTSplTspPaid'],
                DataColumnsFormat: ['',''],
                DisabledColumns: [2, 3, 4, 5],
                Perpage: 10,
                OrderBy: ['TCNMSpl_L.FTSplName ASC']
            },
            CallBack    : {
                ReturnType: 'S',
                Value   : [tInputReturnCode,"TCNMSpl.FTSplCode"],
                Text    : [tInputReturnName,"TCNMSpl_L.FTSplName"]
            },
            RouteAddNew : 'supplier',
            BrowseLev   : nPOStaBrowseType
        };
        return oOptionReturn;
    }

    // Event Browse Supplier Filter Advance
    $('#obtPOAdvSearchBrowseSpl').unbind().click(function(){
        let nStaSession = JCNxFuncChkSessionExpired();
        if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
            JSxCheckPinMenuClose();
            window.oPOBrowsSplOption    = undefined;
            oPOBrowsSplOption           = oPOBrowsSpl({
                'tParamsAgnCode'    : '<?=$this->session->userdata("tSesUsrAgnCode")?>',
                'tReturnInputCode'  : 'oetPOAdvSearchSplCode',
                'tReturnInputName'  : 'oetPOAdvSearchSplName',
            });
            JCNxBrowseData('oPOBrowsSplOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

</script>