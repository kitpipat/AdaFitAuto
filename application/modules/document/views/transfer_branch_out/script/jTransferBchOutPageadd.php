<script>
    $(document).ready(function() {

        localStorage.removeItem("LocalTransferBchOutPdtItemData");

        JSxTransferBchOutGetPdtInTmp();

        if (tStaApv == "2" && !bIsCancel) {
            JSoTransferBchOutSubscribeMQ();
        }

        var tStaDoc = '<?php echo $tStaDoc; ?>';
        var tStaApv = '<?php echo $tStaApv; ?>';
        var tRoute = '<?php echo $tRoute; ?>';
        var tStaPrcStk = '<?php echo $tStaPrcStk; ?>';

        $('.selectpicker').selectpicker();

        $('.xCNDatePicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });

        $("#obtTFBO").removeAttr("disabled");

        $('#obtXthDocDate').click(function() {
            event.preventDefault();
            $('#oetXthDocDate').datepicker('show');
        });

        $('#obtXthDocTime').click(function() {
            event.preventDefault();
            $('#oetXthDocTime').datetimepicker('show');
        });

        $('#obtXthRefExtDate').click(function() {
            event.preventDefault();
            $('#oetXthRefExtDate').datepicker('show');
        });

        $('#obtXthRefIntDate').click(function() {
            event.preventDefault();
            $('#oetXthRefIntDate').datepicker('show');
        });

        $('#obtXthTnfDate').click(function() {
            event.preventDefault();
            $('#oetXthTnfDate').datepicker('show');
        });

        $('.xCNTimePicker').datetimepicker({
            format: 'HH:mm:ss'
        });

        $('.xWTooltipsBT').tooltip({
            'placement': 'bottom'
        });

        $('[data-toggle="tooltip"]').tooltip({
            'placement': 'top'
        });

        $('#obtTransferBchOutXthRefIntDate').click(function() {
            $('#oetTransferBchOutXthRefIntDate').datepicker('show')
        });

        $('#obtTransferBchOutXthRefExtDate').click(function() {
            $('#oetTransferBchOutXthRefExtDate').datepicker('show')
        });

        $('#obtXthDocDate').click(function() {
            $('#oetTransferBchOutDocDate').datepicker('show')
        });

        $('#ocbTransferBchOutAutoGenCode').unbind().bind('change', function() {
            var bIsChecked = $('#ocbTransferBchOutAutoGenCode').is(':checked');
            var oInputDocNo = $('#oetTransferBchOutDocNo');
            if (bIsChecked) {
                $(oInputDocNo).attr('readonly', true);
                $(oInputDocNo).attr('disabled', true);
                $(oInputDocNo).val("");
                $(oInputDocNo).parents('.form-group').removeClass('has-error').find('em').hide();
            } else {
                $(oInputDocNo).removeAttr('readonly');
                $(oInputDocNo).removeAttr('disabled');
            }
        });

        if (!bIsApvOrCancel) {
            if (tUserLoginLevel == 'HQ') {
                // ???????????????????????????????????????????????????????????????????????????????????????????????????????????? ?????????????????????????????????????????????????????????
                if ($('#oetTransferBchOutXthBchFrmCode').val() == '') { // ???????????????????????????????????????????????????????????????
                    $('#obtTransferBchOutBrowseMerFrom').attr('disabled', true);
                } else { // ?????????????????????????????????????????????????????????
                    $('#obtTransferBchOutBrowseMerFrom').attr('disabled', false);
                }

                $('#obtTransferBchOutBrowseShpFrom').attr('disabled', true);
            }

            if (tUserLoginLevel == 'BCH' && !bIsMultiBch) {
                $('#obtTransferBchOutBrowseBchFrom').attr('disabled', true);
                $('#obtTransferBchOutBrowseShpFrom').attr('disabled', true);
            }

            if (tUserLoginLevel == 'SHP') {
                $('#obtTransferBchOutBrowseBchFrom').attr('disabled', true);
                $('#obtTransferBchOutBrowseMerFrom').attr('disabled', true);
                $('#obtTransferBchOutBrowseShpFrom').attr('disabled', true);
                $('#obtTransferBchOutBrowseWahFrom').attr('disabled', true);
            }

            if ($('#oetTransferBchOutXthMerchantFrmCode').val() != '') { // ?????????????????????????????????????????????????????????????????????????????????
                $('#obtTransferBchOutBrowseShpFrom').attr('disabled', false);
            }
            if ($('#oetTransferBchOutXthShopFrmCode').val() != '') { // ??????????????????????????????????????????????????????????????????
                $('#obtTransferBchOutBrowseWahFrom').attr('disabled', false);
            }

            // ?????????????????????????????????????????????????????????????????????????????? ???????????????????????????????????????????????????
            if ($('#oetTransferBchOutXthWhFrmCode').val() == '') { // ???????????????????????????????????????????????????????????????
                $('#obtTransferBchOutBrowseBchTo').attr('disabled', true);
                $('#obtTransferBchOutBrowseWahTo').attr('disabled', true);
            } else { // ?????????????????????????????????????????????????????????
                if (tUserLoginLevel == "HQ" || bIsMultiBch) {
                    $('#obtTransferBchOutBrowseBchTo').attr('disabled', false);
                } else {

                }
                $('#obtTransferBchOutBrowseWahTo').attr('disabled', true);

            }

            // ????????????????????????????????????????????????????????????????????????????????? ??????????????????????????????????????????????????????????????????
            if ($('#oetTransferBchOutXthBchToCode').val() == '') { // ??????????????????????????????????????????????????????????????????
                $('#obtTransferBchOutBrowseWahTo').attr('disabled', true);
            } else { // ????????????????????????????????????????????????????????????
                if (tUserLoginLevel == "HQ" || bIsMultiBch) {
                    $('#obtTransferBchOutBrowseWahTo').attr('disabled', false);
                } else {
                    $('#obtTransferBchOutBrowseWahTo').attr('disabled', true);
                }
            }
        }

        if (bIsApvOrCancel && !bIsAddPage) {
            $('form .xCNApvOrCanCelDisabled').attr('disabled', true);
        } else {
            $('#odvBtnAddEdit .btn-group').show();
        }

        /*===== Begin Control ???????????????????????????????????? ================================================*/
        if (tUserLoginLevel != "HQ" && !bIsMultiBch) { //|| (!bIsAddPage) || (!bIsMultiBch)
            $("#obtTransferBchOutBrowseBch").attr('disabled', true);
        }
        /*===== End Control ???????????????????????????????????? ==================================================*/

        $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                return false;
            }
        });

    });

    /*===== Begin Event Browse =========================================================*/
    // ????????????????????????????????????
    $("#obtTransferBchOutBrowseBch").click(function() {

        let tWhereCon = "";
        if (tUserLoginLevel != "HQ") {
            tWhereCon = " AND TCNMBranch.FTBchCode IN(<?php echo $this->session->userdata('tSesUsrBchCodeMulti'); ?>) ";
        }

        // option
        window.oTransferBchOutBrowseBch = {
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
                Condition: [tWhereCon]
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
                Value: ["oetTransferBchOutBchCode", "TCNMBranch.FTBchCode"],
                Text: ["oetTransferBchOutBchName", "TCNMBranch_L.FTBchName"]
            },
            RouteAddNew: 'branch',
            BrowseLev: 1
        };
        JCNxBrowseData('oTransferBchOutBrowseBch');
    });

    // ?????????????????????
    $("#obtTransferBchOutBrowseBchFrom").click(function() {

        let tWhereCon = "";
        if (tUserLoginLevel != "HQ") {
            tWhereCon = " AND TCNMBranch.FTBchCode IN(<?php echo $this->session->userdata('tSesUsrBchCodeMulti'); ?>) ";
        }

        // option
        window.oTransferBchOutBrowseBchFrom = {
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
                Condition: [
                    " AND TCNMBranch.FTBchCode NOT IN ('" + $('#oetTransferBchOutXthBchToCode').val() + "') ",
                    tWhereCon
                ]
            },
            GrideView: {
                ColumnPathLang: 'authen/user/user',
                ColumnKeyLang: ['tBrowseBCHCode', 'tBrowseBCHName'],
                ColumnsSize: ['10%', '75%'],
                DataColumns: ['TCNMBranch.FTBchCode', 'TCNMBranch_L.FTBchName'],
                DataColumnsFormat: ['', ''],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMBranch.FTBchCode DESC'],
                // SourceOrder: "ASC"
            },
            CallBack: {
                ReturnType: 'S',
                Value: ["oetTransferBchOutXthBchFrmCode", "TCNMBranch.FTBchCode"],
                Text: ["oetTransferBchOutXthBchFrmName", "TCNMBranch_L.FTBchName"]
            },
            NextFunc: {
                FuncName: 'JSxTransferBchOutCallbackBchFrom',
                ArgReturn: ['FTBchCode']
            },
            RouteAddNew: 'branch',
            BrowseLev: 1
        };
        JCNxBrowseData('oTransferBchOutBrowseBchFrom');
    });

    // ??????????????????????????????????????????
    $("#obtTransferBchOutBrowseMerFrom").click(function() {
        // option
        window.oTransferBchOutBrowseMerFrom = {
            Title: ['company/warehouse/warehouse', 'tWAHBwsMchTitle'],
            Table: {
                Master: 'TCNMMerchant',
                PK: 'FTMerCode'
            },
            Join: {
                Table: ['TCNMMerchant_L'],
                On: ['TCNMMerchant.FTMerCode = TCNMMerchant_L.FTMerCode AND TCNMMerchant_L.FNLngID = ' + nLangEdits]
            },
            Where: {
                Condition: [""]
            },
            GrideView: {
                ColumnPathLang: 'company/warehouse/warehouse',
                ColumnKeyLang: ['tWAHBwsMchCode', 'tWAHBwsMchNme'],
                ColumnsSize: ['15%', '75%'],
                WidthModal: 50,
                DataColumns: ['TCNMMerchant.FTMerCode', 'TCNMMerchant_L.FTMerName'],
                DataColumnsFormat: ['', ''],
                Perpage: 10,
                OrderBy: ['TCNMMerchant.FDCreateOn DESC'],
                // SourceOrder: "ASC"
            },
            CallBack: {
                ReturnType: 'S',
                Value: ["oetTransferBchOutXthMerchantFrmCode", "TCNMMerchant.FTMerCode"],
                Text: ["oetTransferBchOutXthMerchantFrmName", "TCNMMerchant_L.FTMerName"],
            },
            NextFunc: {
                FuncName: 'JSxTransferBchOutCallbackMerFrom',
                ArgReturn: ['FTMerCode', 'FTMerName']
            },
            BrowseLev: 1,
            //DebugSQL : true
        };
        JCNxBrowseData('oTransferBchOutBrowseMerFrom');
    });

    // ??????????????????????????????
    $("#obtTransferBchOutBrowseShpFrom").click(function() {
        // Option Shop
        window.oTransferBchOutBrowseShpFrom = {
            Title: ['company/shop/shop', 'tSHPTitle'],
            Table: {
                Master: 'TCNMShop',
                PK: 'FTShpCode'
            },
            Join: {
                Table: ['TCNMShop_L', 'TCNMWaHouse_L'],
                On: [
                    'TCNMShop_L.FTShpCode = TCNMShop.FTShpCode AND TCNMShop.FTBchCode = TCNMShop_L.FTBchCode AND TCNMShop_L.FNLngID = ' + nLangEdits,
                    'TCNMWaHouse_L.FTWahCode = TCNMShop.FTWahCode AND TCNMWaHouse_L.FTBchCode = TCNMShop.FTBchCode AND TCNMWaHouse_L.FNLngID = ' + nLangEdits,
                ]
            },
            Where: {
                Condition: [
                    function() {
                        var tSQL = " AND TCNMShop.FTShpType = '1' AND TCNMShop.FTMerCode = '" + $('#oetTransferBchOutXthMerchantFrmCode').val() + "' AND TCNMShop.FTBchCode = '" + $('#oetTransferBchOutXthBchFrmCode').val() + "'";
                        return tSQL;
                    }
                ]
            },
            GrideView: {
                ColumnPathLang: 'company/branch/branch',
                ColumnKeyLang: ['tBCHCode', 'tBCHName'],
                ColumnsSize: ['25%', '75%'],
                WidthModal: 50,
                DataColumns: ['TCNMShop.FTShpCode', 'TCNMShop_L.FTShpName', 'TCNMShop.FTShpType', 'TCNMShop.FTBchCode', 'TCNMShop.FTWahCode', 'TCNMWaHouse_L.FTWahName'],
                DataColumnsFormat: ['', '', '', '', '', ''],
                DisabledColumns: [2, 3, 4, 5],
                Perpage: 5,
                OrderBy: ['TCNMShop_L.FTShpName'],
                SourceOrder: "ASC"
            },
            CallBack: {
                ReturnType: 'S',
                Value: ["oetTransferBchOutXthShopFrmCode", "TCNMShop.FTShpCode"],
                Text: ["oetTransferBchOutXthShopFrmName", "TCNMShop_L.FTShpName"],
            },
            NextFunc: {
                FuncName: 'JSxTransferBchOutCallbackShpFrom',
                ArgReturn: ['FTBchCode', 'FTShpCode', 'FTShpType', 'FTWahCode', 'FTWahName']
            },
            BrowseLev: 1,
            // DebugSQL : true
        }
        JCNxBrowseData('oTransferBchOutBrowseShpFrom');
    });

    // ???????????????????????????????????????
    $("#obtTransferBchOutBrowseWahFrom").click(function() {

        //User ?????????????????????????????????????????????????????????????????????????????????
        if($('#ohdTWOnStaWasteWAH').val() == ""){
            var tFindWahouse = " AND TCNMWaHouse.FTWahStaType IN('1','2') ";
        }else{
            var tFindWahouse = " AND TCNMWaHouse.FTWahStaType IN('1','2','10') ";
        }

        var tShpFromCode = $('#oetTransferBchOutXthShopFrmCode').val();
        var bIsShpFromEmpty = (tShpFromCode === '') || (tShpFromCode == undefined);
        if (bIsShpFromEmpty) {
            window.oTransferBchOutBrowseWahFrom = {
                Title: ["company/warehouse/warehouse", "tWAHTitle"],
                Table: {
                    Master: 'TCNMWaHouse',
                    PK: 'FTWahCode'
                },
                Join: {
                    Table: ['TCNMWaHouse_L'],
                    On: ['TCNMWaHouse_L.FTWahCode = TCNMWaHouse.FTWahCode AND TCNMWaHouse.FTBchCode = TCNMWaHouse_L.FTBchCode AND TCNMWaHouse_L.FNLngID = ' + nLangEdits]
                },
                Where: {
                    Condition: [
                        function() {
                            var tSQL = " "+tFindWahouse+" AND TCNMWaHouse.FTBchCode = '" + $('#oetTransferBchOutXthBchFrmCode').val() + "'";
                            return tSQL;
                        }
                    ]
                },
                GrideView: {
                    ColumnPathLang: 'company/warehouse/warehouse',
                    ColumnKeyLang: ['tWahCode', 'tWahName'],
                    ColumnsSize: ['25%', '75%'],
                    WidthModal: 50,
                    DataColumns: ['TCNMWaHouse.FTWahCode', 'TCNMWaHouse_L.FTWahName', 'TCNMWaHouse.FTBchCode'],
                    DataColumnsFormat: ['', '', '', ''],
                    DisabledColumns: [2, 3],
                    Perpage: 10,
                    OrderBy: ['TCNMWaHouse.FDCreateOn DESC'],
                    // SourceOrder: "ASC"
                },
                CallBack: {
                    ReturnType: 'S',
                    Value: ["oetTransferBchOutXthWhFrmCode", "TCNMWaHouse.FTWahCode"],
                    Text: ["oetTransferBchOutXthWhFrmName", "TCNMWaHouse_L.FTWahName"],
                },
                NextFunc: {
                    FuncName: 'JSxTransferBchOutCallbackWahFrom',
                    ArgReturn: ['FTBchCode', 'FTWahCode', 'FTWahName']
                },
                BrowseLev: 1,
                // DebugSQL : true
            }
        } else {
            window.oTransferBchOutBrowseWahFrom = {
                Title: ['company/shop/shop', 'tSHPWah'],
                Table: {
                    Master: 'TCNMShpWah',
                    PK: 'FTWahCode'
                },
                Join: {
                    Table: ['TCNMWaHouse_L'],
                    On: ['TCNMWaHouse_L.FTWahCode = TCNMShpWah.FTWahCode AND TCNMWaHouse_L.FTBchCode = TCNMShpWah.FTBchCode AND TCNMWaHouse_L.FNLngID = ' + nLangEdits, ]
                },
                Where: {
                    Condition: [
                        function() {
                            var tSQL = " AND TCNMShpWah.FTBchCode = '" + $('#oetTransferBchOutXthBchFrmCode').val() + "' AND TCNMShpWah.FTShpCode = '" + $('#oetTransferBchOutXthShopFrmCode').val() + "'";
                            return tSQL;
                        }
                    ]
                },
                GrideView: {
                    ColumnPathLang: 'company/shop/shop',
                    ColumnKeyLang: ['tWahCode', 'tWahName'],
                    ColumnsSize: ['15%', '75%'],
                    WidthModal: 50,
                    DataColumns: ['TCNMShpWah.FTWahCode', 'TCNMWaHouse_L.FTWahName', 'TCNMShpWah.FTBchCode'],
                    DataColumnsFormat: ['', ''],
                    Perpage: 10,
                    OrderBy: ['TCNMShpWah.FDCreateOn DESC'],
                    // SourceOrder  : "ASC"
                },
                CallBack: {
                    ReturnType: 'S',
                    Value: ["oetTransferBchOutXthWhFrmCode", "TCNMShpWah.FTWahCode"],
                    Text: ["oetTransferBchOutXthWhFrmName", "TCNMWaHouse_L.FTWahName"],
                },
                NextFunc: {
                    FuncName: 'JSxTransferBchOutCallbackWahFrom',
                    ArgReturn: ['FTBchCode', 'FTWahCode', 'FTWahName']
                },
                BrowseLev: 1,
                // DebugSQL : true
            }
        }
        JCNxBrowseData('oTransferBchOutBrowseWahFrom');
    });

    // ?????????????????????
    $("#obtTransferBchOutBrowseBchTo").click(function() {

        var tSesUsrAgnCode = "<?php echo $this->session->userdata("tSesUsrAgnCode"); ?>";
        let tWhereCon = "";
        // if(tUserLoginLevel != "HQ"){
        //     tWhereCon = " AND TCNMBranch.FTBchCode IN(<?php echo $this->session->userdata('tSesUsrBchCodeMulti'); ?>) ";
        // }
        if (tSesUsrAgnCode != "") {
            tWhereCon += " AND TCNMBranch.FTAgnCode ='" + tSesUsrAgnCode + "' ";
        }
        // option
        window.oTransferBchOutBrowseBchTo = {
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
                Condition: [
                    " AND TCNMBranch.FTBchCode NOT IN ('" + $('#oetTransferBchOutXthBchFrmCode').val() + "')",
                    tWhereCon
                ]
            },
            GrideView: {
                ColumnPathLang: 'authen/user/user',
                ColumnKeyLang: ['tBrowseBCHCode', 'tBrowseBCHName'],
                ColumnsSize: ['10%', '75%'],
                DataColumns: ['TCNMBranch.FTBchCode', 'TCNMBranch_L.FTBchName'],
                DataColumnsFormat: ['', ''],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMBranch.FTBchCode DESC'],
                // SourceOrder: "ASC"
            },
            CallBack: {
                ReturnType: 'S',
                Value   : ["oetTransferBchOutXthBchToCode", "TCNMBranch.FTBchCode"],
                Text    : ["oetTransferBchOutXthBchToName", "TCNMBranch_L.FTBchName"]
            },
            NextFunc: {
                FuncName: 'JSxTransferBchOutCallbackBchTo',
                ArgReturn: ['FTBchCode']
            }
        };
        JCNxBrowseData('oTransferBchOutBrowseBchTo');
    });

    // ???????????????????????????????????????
    $("#obtTransferBchOutBrowseWahTo").click(function() {

        //User ?????????????????????????????????????????????????????????????????????????????????
        if($('#ohdTWOnStaWasteWAH').val() == ""){
            var tFindWahouse = " AND TCNMWaHouse.FTWahStaType IN('1','2') ";
        }else{
            var tFindWahouse = " AND TCNMWaHouse.FTWahStaType IN('1','2','10') ";
        }

        window.oTransferBchOutBrowseWahTo = {
            Title: ["company/warehouse/warehouse", "tWAHTitle"],
            Table: {
                Master: 'TCNMWaHouse',
                PK: 'FTWahCode'
            },
            Join: {
                Table: ['TCNMWaHouse_L'],
                On: ['TCNMWaHouse_L.FTWahCode = TCNMWaHouse.FTWahCode AND TCNMWaHouse.FTBchCode = TCNMWaHouse_L.FTBchCode AND TCNMWaHouse_L.FNLngID = ' + nLangEdits]
            },
            Where: {
                Condition: [
                    function() {
                        var tSQL = " "+tFindWahouse+" AND TCNMWaHouse.FTBchCode = '" + $('#oetTransferBchOutXthBchToCode').val() + "'";
                        return tSQL;
                    }
                ]
            },
            GrideView: {
                ColumnPathLang: 'company/warehouse/warehouse',
                ColumnKeyLang: ['tWahCode', 'tWahName'],
                ColumnsSize: ['25%', '75%'],
                WidthModal: 50,
                DataColumns: ['TCNMWaHouse.FTWahCode', 'TCNMWaHouse_L.FTWahName', 'TCNMWaHouse.FTBchCode'],
                DataColumnsFormat: ['', '', '', ''],
                DisabledColumns: [2, 3],
                Perpage: 10,
                OrderBy: ['TCNMWaHouse.FDCreateOn DESC'],
                // SourceOrder: "ASC"
            },
            CallBack: {
                ReturnType: 'S',
                Value: ["oetTransferBchOutXthWhToCode", "TCNMWaHouse.FTWahCode"],
                Text: ["oetTransferBchOutXthWhToName", "TCNMWaHouse_L.FTWahName"],
            },
            NextFunc: {
                FuncName: 'JSxTransferBchOutCallbackWahTo',
                ArgReturn: ['FTBchCode', 'FTWahCode', 'FTWahName']
            },
            BrowseLev: 1
        }
        JCNxBrowseData('oTransferBchOutBrowseWahTo');
    });

    // ??????????????????
    $("#obtTransferBchOutBrowseReason").click(function() {
        // Option
        window.oTransferBchOutBrowseReason = {
            Title: ['other/reason/reason', 'tRSNTitle'],
            Table: {
                Master: 'TCNMRsn',
                PK: 'FTRsnCode'
            },
            Join: {
                Table: ['TCNMRsn_L'],
                On: [
                    'TCNMRsn.FTRsnCode = TCNMRsn_L.FTRsnCode AND TCNMRsn_L.FNLngID = ' + nLangEdits
                ]
            },
            Where: {
                Condition : ["AND TCNMRsn.FTRsgCode = '016' "] // Type ?????????
            },
            GrideView: {
                ColumnPathLang: 'other/reason/reason',
                ColumnKeyLang: ['tRSNTBCode', 'tRSNTBName'],
                ColumnsSize: ['10%', '30%'],
                WidthModal: 50,
                DataColumns: ['TCNMRsn.FTRsnCode', 'TCNMRsn_L.FTRsnName'],
                DataColumnsFormat: ['', ''],
                Perpage: 10,
                OrderBy: ['TCNMRsn.FDCreateOn DESC'],
            },
            CallBack: {
                ReturnType: 'S',
                Value: ['oetTransferBchOutRsnCode', "TCNMRsn.FTRsnCode"],
                Text: ['oetTransferBchOutRsnName', "TCNMRsn_L.FTRsnName"]
            },
            RouteAddNew : 'reason',
            BrowseLev : 0
        }
        JCNxBrowseData('oTransferBchOutBrowseReason');
    });

    // ???????????????
    $("#obtTransferBchOutBrowseShipVia").click(function() {
        // Option
        window.oTransferBchOutBrowseShipVia = {
            Title: ['document/producttransferwahouse/producttransferwahouse', 'tTFWShipViaModalTitle'],
            Table: {
                Master: 'TCNMShipVia',
                PK: 'FTViaCode'
            },
            Join: {
                Table: ['TCNMShipVia_L'],
                On: [
                    "TCNMShipVia.FTViaCode = TCNMShipVia_L.FTViaCode AND TCNMShipVia_L.FNLngID = " + nLangEdits
                ]
            },
            GrideView: {
                ColumnPathLang: 'document/producttransferwahouse/producttransferwahouse',
                ColumnKeyLang: ['tTFWShipViaCode', 'tTFWShipViaName'],
                DataColumns: ['TCNMShipVia.FTViaCode', 'TCNMShipVia_L.FTViaName'],
                DataColumnsFormat: ['', ''],
                ColumnsSize: [''],
                Perpage: 10,
                WidthModal: 50,
                OrderBy: ['TCNMShipVia.FDCreateOn DESC'],
                // SourceOrder: "ASC"
            },
            CallBack: {
                ReturnType: 'S',
                Value: ["oetTWOUpVendingViaCode", "TCNMShipVia.FTViaCode"],
                Text: ["oetTWOUpVendingViaName", "TCNMShipVia_L.FTViaName"],
            },
            BrowseLev: 1
        }
        JCNxBrowseData('oTransferBchOutBrowseShipVia');
    });
    /*===== End Event Browse ===========================================================*/

    /*===== Begin Callback Browse ======================================================*/
    // Browse Bch From Callback
    function JSxTransferBchOutCallbackBchFrom(params) {
        var tBchCodeFrom = $('#oetTransferBchOutXthBchFrmCode').val();

        // ??????????????????
        $('#oetTransferBchOutXthMerchantFrmCode').val("");
        $('#oetTransferBchOutXthMerchantFrmName').val("");
        $('#oetTransferBchOutXthShopFrmCode').val("");
        $('#oetTransferBchOutXthShopFrmName').val("");
        $('#oetTransferBchOutXthWhFrmCode').val("");
        $('#oetTransferBchOutXthWhFrmName').val("");
        // ?????????????????????
        $('#oetTransferBchOutXthBchToCode').val("");
        $('#oetTransferBchOutXthBchToName').val("");
        $('#oetTransferBchOutXthWhToCode').val("");
        $('#oetTransferBchOutXthWhToName').val("");

        // ??????????????????
        $('#obtTransferBchOutBrowseMerFrom').attr('disabled', true);
        $('#obtTransferBchOutBrowseShpFrom').attr('disabled', true);
        $('#obtTransferBchOutBrowseWahFrom').attr('disabled', true);
        // ?????????????????????
        $('#obtTransferBchOutBrowseBchTo').attr('disabled', true);
        $('#obtTransferBchOutBrowseWahTo').attr('disabled', true);

        if (tBchCodeFrom != "") {
            $('#obtTransferBchOutBrowseMerFrom').attr('disabled', false);
        }

        if (tUserLoginLevel == "HQ" || tUserLoginLevel == "BCH") {
            $('#obtTransferBchOutBrowseWahFrom').attr('disabled', false);
            if (tBchCodeFrom == "") {
                $('#obtTransferBchOutBrowseWahFrom').attr('disabled', true);
            }
        }

    }

    // Browse Mer From Callback
    function JSxTransferBchOutCallbackMerFrom(params) {
        var tMerCodeFrom = $('#oetTransferBchOutXthMerchantFrmCode').val();

        // ??????????????????
        $('#oetTransferBchOutXthShopFrmCode').val("");
        $('#oetTransferBchOutXthShopFrmName').val("");
        $('#oetTransferBchOutXthWhFrmCode').val("");
        $('#oetTransferBchOutXthWhFrmName').val("");
        // ?????????????????????
        $('#oetTransferBchOutXthBchToCode').val("");
        $('#oetTransferBchOutXthBchToName').val("");
        $('#oetTransferBchOutXthWhToCode').val("");
        $('#oetTransferBchOutXthWhToName').val("");

        // ??????????????????
        $('#obtTransferBchOutBrowseShpFrom').attr('disabled', true);
        $('#obtTransferBchOutBrowseWahFrom').attr('disabled', true);
        // ?????????????????????
        $('#obtTransferBchOutBrowseBchTo').attr('disabled', true);
        $('#obtTransferBchOutBrowseWahTo').attr('disabled', true);

        if (tMerCodeFrom != "") {
            $('#obtTransferBchOutBrowseShpFrom').attr('disabled', false);
        }

        if (tUserLoginLevel == "HQ" || tUserLoginLevel == "BCH") {
            $('#obtTransferBchOutBrowseWahFrom').attr('disabled', false);
        }
    }

    // Browse Shop From Callback
    function JSxTransferBchOutCallbackShpFrom(params) {
        var aParam = JSON.parse(params);
        $('#oetTransferBchOutWahInShopCode').val(aParam[3]);

        var tShpCodeFrom = $('#oetTransferBchOutXthShopFrmCode').val();

        // ??????????????????
        $('#oetTransferBchOutXthWhFrmCode').val("");
        $('#oetTransferBchOutXthWhFrmName').val("");
        // ?????????????????????
        $('#oetTransferBchOutXthBchToCode').val("");
        $('#oetTransferBchOutXthBchToName').val("");
        $('#oetTransferBchOutXthWhToCode').val("");
        $('#oetTransferBchOutXthWhToName').val("");

        // ??????????????????
        $('#obtTransferBchOutBrowseWahFrom').attr('disabled', true);
        // ?????????????????????
        $('#obtTransferBchOutBrowseBchTo').attr('disabled', true);
        $('#obtTransferBchOutBrowseWahTo').attr('disabled', true);

        if (tShpCodeFrom != "") {
            $('#obtTransferBchOutBrowseWahFrom').attr('disabled', false);
        }

        if (tUserLoginLevel == "HQ" || tUserLoginLevel == "BCH") {
            $('#obtTransferBchOutBrowseWahFrom').attr('disabled', false);
        }
    }

    // Browse Warehouse From Callback
    function JSxTransferBchOutCallbackWahFrom(params) {
        var tWahCodeFrom = $('#oetTransferBchOutXthWhFrmCode').val();
        var tBchCodeTo = $('#oetTransferBchOutXthBchToCode').val();
        var tWahCodeTo = $('#oetTransferBchOutXthWhToCode').val();

        // ?????????????????????
        $('#obtTransferBchOutBrowseBchTo').attr('disabled', true);
        $('#obtTransferBchOutBrowseWahTo').attr('disabled', true);

        if (tWahCodeFrom != "") {
            $('#obtTransferBchOutBrowseBchTo').attr('disabled', false);
            if (tBchCodeTo != "") {
                $('#obtTransferBchOutBrowseWahTo').attr('disabled', false);
            }
        } else {
            $('#oetTransferBchOutXthBchToCode').val("");
            $('#oetTransferBchOutXthBchToName').val("");
            $('#oetTransferBchOutXthWhToCode').val("");
            $('#oetTransferBchOutXthWhToName').val("");
        }
    }

    // Browse Bch To Callback
    function JSxTransferBchOutCallbackBchTo(params) {
        var tBchCodeTo = $('#oetTransferBchOutXthBchToCode').val();

        // ?????????????????????
        $('#oetTransferBchOutXthWhToCode').val("");
        $('#oetTransferBchOutXthWhToName').val("");

        // ?????????????????????
        $('#obtTransferBchOutBrowseWahTo').attr('disabled', true);

        if (tBchCodeTo != "") {
            $('#obtTransferBchOutBrowseWahTo').attr('disabled', false);

            //??????????????? ????????????????????????????????? default ?????????????????????????????????
            $.ajax({
                type    : "POST",
                url     : "docTransferBchOutCheckWahouseInBCH",
                data    : {
                    tBCHCode    : tBchCodeTo,
                },
                cache   : false,
                timeout : 5000,
                success : function(oResult) {
                    var aResult = JSON.parse(oResult)
                    $('#oetTransferBchOutXthWhToCode').val(aResult.aItems[0].FTWahCode);
                    $('#oetTransferBchOutXthWhToName').val(aResult.aItems[0].FTWahName);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxCloseLoading();
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        }
    }

    // Browse Warehouse To Callback
    function JSxTransferBchOutCallbackWahTo(params) {}

    var bUniqueTransferBchOutCode;
    $.validator.addMethod(
        "uniqueTransferBchOutCode",
        function(tValue, oElement, aParams) {
            var nStaSession = JCNxFuncChkSessionExpired();
            if (typeof nStaSession !== "undefined" && nStaSession == 1) {

                var tTransferBchOutCode = tValue;
                $.ajax({
                    type: "POST",
                    url: "docTransferBchOutUniqueValidate",
                    data: "tTransferBchOutCode=" + tTransferBchOutCode,
                    dataType: "JSON",
                    success: function(poResponse) {
                        bUniqueTransferBchOutCode = (poResponse.bStatus) ? false : true;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Custom validate uniqueTransferBchOutCode: ', jqXHR, textStatus, errorThrown);
                    },
                    async: false
                });
                return bUniqueTransferBchOutCode;

            } else {
                JCNxShowMsgSessionExpired();
            }

        },
        "Doc No. is Already Taken"
    );

    // Validate Form
    function JSxTransferBchOutValidateForm() {

        if($('#oetTransferBchOutRsnCode').val() == '' || $('#oetTransferBchOutRsnCode').val() == null){
            FSvCMNSetMsgWarningDialog('<?php echo language('document/purchasebranch/purchasebranch','tPRBPlsSelectReason'); ?>');
            return;
        }

        var oTopUpVendingForm = $('#ofmTransferBchOutForm').validate({
            focusInvalid: false,
            onclick: false,
            onfocusout: false,
            onkeyup: false,
            rules: {
                oetTransferBchOutDocNo: {
                    required: true,
                    maxlength: 20,
                    uniqueTransferBchOutCode: bIsAddPage
                },
                oetTransferBchOutDocDate: {
                    required: true
                },
                oetTransferBchOutDocTime: {
                    required: true
                },
            },
            messages: {
                oetTransferBchOutDocNo: {
                    "required": $('#oetTransferBchOutDocNo').attr('data-validate-required')
                }
            },
            errorElement: "em",
            errorPlacement: function(error, element) {
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
            highlight: function(element, errorClass, validClass) {
                $(element).closest('.form-group').addClass("has-error");
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).closest('.form-group').removeClass("has-error");
            },
            submitHandler: function(form) {
                JSxTransferBchOutSave();
            }
        });
    }

    // Save Doc
    function JSxTransferBchOutSave(ptType = '') {
        var bIsWahFromEmpty = $('#oetTransferBchOutXthWhFrmCode').val() == "";
        var bIsWahToEmpty = $('#oetTransferBchOutXthWhToCode').val() == "";
        if (bIsWahFromEmpty || bIsWahToEmpty) {
            var tWarningMessage = '?????????????????????????????????????????????????????? ???????????????????????? ??????????????????????????????';
            FSvCMNSetMsgWarningDialog(tWarningMessage);
            return;
        }

        var bIsPdtEmpty = $('#otbTransferBchOutPdtTable').find('tr.xWTransferBchOutPdtItem').length < 1;
        if (bIsPdtEmpty) {
            var tWarningMessage = '?????????????????????????????????????????????????????????????????? ??????????????????????????????';
            FSvCMNSetMsgWarningDialog(tWarningMessage);
            return;
        }

        // var nStaSession = JCNxFuncChkSessionExpired();
        // if (typeof nStaSession !== "undefined" && nStaSession == 1) {

            var tBchCode = $('#oetTransferBchOutBchCode').val();
            var tMerCode = $('#oetTransferBchOutMchCode').val();
            var tShpCode = $('#oetTransferBchOutShpCode').val();
            var tPosCode = $('#oetTransferBchOutPosCode').val();
            var tWahCode = $('#oetTransferBchOutWahCode').val();

            // JCNxOpenLoading();

            $.ajax({
                type: "POST",
                url: "<?php echo $tRoute; ?>",
                data: $("#ofmTransferBchOutForm").serialize(),
                cache: false,
                timeout: 5000,
                dataType: "JSON",
                success: function(oResult) {
                    if(ptType == 'approve'){

                    }else{
                        switch (oResult.nStaCallBack) {
                            case "1": {
                                JSvTransferBchOutCallPageEdit(oResult.tCodeReturn);
                                break;
                            }
                            case "2": {
                                JSvTransferBchOutCallPageAdd();
                                break;
                            }
                            case "3": {
                                JSvTransferBchOutCallPageList();
                                break;
                            }
                            default: {
                                JSvTransferBchOutCallPageEdit(oResult.tCodeReturn);
                            }
                        }
                    }
                    $("#obtTFBO").removeAttr("disabled");
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // JCNxCloseLoading();
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                    $("#obtTFBO").removeAttr("disabled");
                    var tDocNo = $('#oetTransferBchOutDocNo').val();
                    if (jqXHR.status != 404){
                        var tLogFunction = 'ERROR';
                        var tDisplayEvent = '??????????????????/??????????????? ??????????????????????????? - ????????????';
                        var tErrorStatus = jqXHR.status;
                        var tHtmlError = $(jqXHR.responseText);
                        var tMsgErrorBody = tHtmlError.find('p:nth-child(3)').text();
                        var tLogDocNo   = tDocNo;
                        JCNxPackDataToMQLog(tMsgErrorBody,tErrorStatus,tDisplayEvent,tLogFunction,tLogDocNo);
                    }else{
                        //JCNxSendMQPageNotFound(jqXHR,tPODocNo);
                    }
                }
            });

        // } else {
        //     JCNxShowMsgSessionExpired();
        // }
    }

    // Approve Doc
    function JSvTransferBchOutApprove(pbIsConfirm) {
        var bIsWahFromEmpty = $('#oetTransferBchOutXthWhFrmCode').val() == "";
        var bIsWahToEmpty = $('#oetTransferBchOutXthWhToCode').val() == "";


        if (bIsWahFromEmpty || bIsWahToEmpty) {
            var tWarningMessage = '?????????????????????????????????????????????????????? ???????????????????????? ?????????????????????????????????';
            FSvCMNSetMsgWarningDialog(tWarningMessage);
            return;
        }

        var bIsPdtEmpty = $('#otbTransferBchOutPdtTable').find('tr.xWTransferBchOutPdtItem').length < 1;
        if (bIsPdtEmpty) {
            var tWarningMessage = '?????????????????????????????????????????????????????????????????? ?????????????????????????????????';
            FSvCMNSetMsgWarningDialog(tWarningMessage);
            return;
        }

        // var nStaSession = JCNxFuncChkSessionExpired();
        // if (typeof nStaSession !== "undefined" && nStaSession == 1) {
            try {
                if (pbIsConfirm) {
                    JCNxOpenLoading();
                    $("#ohdTransferBchOutStaApv").val(2);
                    $("#odvTransferBchOutPopupApv").modal("hide");
                    JSxTransferBchOutSave('approve');

                    var tDocNo      = $("#oetTransferBchOutDocNo").val();
                    var tStaApv     = $("#ohdTransferBchOutStaApv").val();
                    var tBchCode    = $('#ohdTransferBchOutBchLogin').val();

                    $.ajax({
                        type    : "POST",
                        url     : "docTransferBchOutCheckProductWahouse",
                        data    : {
                            'tDocNo'        : tDocNo,
                            'tBchCode'      : $('#oetTransferBchOutXthBchFrmCode').val(),
                            'tWahCode'      : $('#oetTransferBchOutXthWhFrmCode').val()
                        },
                        cache   : false,
                        timeout : 0,
                        success : function(tResult) {
                            var aReturnData = JSON.parse(tResult);

                            if( aReturnData['nStaEvent'] == 1 || aReturnData['nStaEvent'] == 400 || aReturnData['tChkTsysConfig'] == '2' ){
                                $.ajax({
                                    type    : "POST",
                                    url     : "docTransferBchOutDocApprove",
                                    data    : {
                                        tDocNo      : tDocNo,
                                        tStaApv     : tStaApv,
                                        tBchCode    : tBchCode
                                    },
                                    cache: false,
                                    timeout: 0,
                                    success: function(tResult) {
                                        var oResult = JSON.parse(tResult);
                                        if (oResult.nStaEvent == "900") {
                                            FSvCMNSetMsgErrorDialog(oResult.tStaMessg);
                                            return;
                                        }else if (oResult.nStaEvent == "905") {
                                            FSvCMNSetMsgErrorDialog(oResult.tStaMessg);
                                            var tLogFunction        = 'WARNING';
                                            var tDisplayEvent       = '???????????????????????????????????????????????? - ????????????';
                                            var tErrorStatus        = '';
                                            var tMsgErrorBody       = oResult.tStaMessg;
                                            var tLogDocNo           = tDocNo;
                                            JCNxPackDataToMQLog(tMsgErrorBody,tErrorStatus,tDisplayEvent,tLogFunction,tLogDocNo);
                                            return;
                                        }else{
                                            JSoTransferBchOutSubscribeMQ();
                                        }
                                        JCNxCloseLoading();
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        JCNxResponseError(jqXHR, textStatus, errorThrown);
                                        JCNxCloseLoading();
                                        //????????????????????????????????????????????????????????? Center ????????????????????? MQ ??????????????????????????? LOG (TYPE:ERROR)
                                        if (jqXHR.status != 404){
                                            var tLogFunction = 'ERROR';
                                            var tDisplayEvent = '???????????????????????????????????????????????? - ????????????';
                                            var tErrorStatus = jqXHR.status;
                                            var tHtmlError = $(jqXHR.responseText);
                                            var tMsgErrorBody = tHtmlError.find('p:nth-child(3)').text();
                                            var tLogDocNo = tDocNo;
                                            JCNxPackDataToMQLog(tMsgErrorBody,tErrorStatus,tDisplayEvent,tLogFunction,tLogDocNo);
                                        }
                                    }
                                });
                            }else{
                                JSvTransferBchOutCallPageEdit(tDocNo);
                                var tMessageError   = aReturnData['tStaMessg'];
                                var aItemFail       = aReturnData['aItemFail'];
                                //??????????????????????????????????????????????????????????????? ??????????????????
                                var tTextStockFail      = '';
                                var tTextStockFailShow  = '';
                                for(var i=0; i<aItemFail.length; i++){
                                    tTextStockFail += '('+aItemFail[i][0]+')' + ' - ' + aItemFail[i][1] + ' [?????????????????? : ' + aItemFail[i][2] + ' ???????????? , ???????????????????????? : ' + aItemFail[i][3] + ' ????????????] <br>';
                                }
                                tTextStockFailShow = '<p style="font-weight: bold;">'+tTextStockFail+'</p>';

                                FSvCMNSetMsgErrorDialog(tMessageError + '<br>' + tTextStockFailShow);
                                JCNxCloseLoading();
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            JCNxResponseError(jqXHR, textStatus, errorThrown);
                        }
                    });
                } else {
                    // console.log("StaApvDoc Call Modal");
                    $("#odvTransferBchOutPopupApv").modal("show");
                }
            } catch (err) {
                console.log("JSvTransferBchOutApprove Error: ", err);
            }

        // } else {
        //     JCNxShowMsgSessionExpired();
        // }
    }

    // Cancel Doc
    function JSvTransferBchOutCancel(pbIsConfirm) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof nStaSession !== "undefined" && nStaSession == 1) {
            var tDocNo = $("#oetTransferBchOutDocNo").val();
            var tBchCode    = $('#ohdTransferBchOutBchLogin').val();
            var tStaApv    = $('#ohdTransferBchOutStaApv').val();
            if (pbIsConfirm) {
                $.ajax({
                    type: "POST",
                    url: "docTransferBchOutDocCancel",
                    data: {
                        tDocNo : tDocNo,
                        tBchCode : tBchCode,
                        tStaApv : tStaApv
                    },
                    cache: false,
                    timeout: 5000,
                    success: function(tResult) {
                        $("#odvTransferBchOutPopupCancel").modal("hide");
                        var aResult = $.parseJSON(tResult);
                        if (aResult.nSta == 1) {
                            JSvTransferBchOutCallPageEdit(tDocNo);
                        } else {
                            JCNxCloseLoading();
                            var tMsgBody = aResult.tStaMessg;
                            FSvCMNSetMsgWarningDialog(tMsgBody);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        JCNxResponseError(jqXHR, textStatus, errorThrown);
                        //????????????????????????????????????????????????????????? Center ????????????????????? MQ ??????????????????????????? LOG (TYPE:ERROR)
                        if (jqXHR.status != 404){
                            var tLogFunction = 'ERROR';
                            var tDisplayEvent = '????????????????????????????????????????????? - ????????????';
                            var tErrorStatus = jqXHR.status;
                            var tHtmlError = $(jqXHR.responseText);
                            var tMsgErrorBody = tHtmlError.find('p:nth-child(3)').text();
                            var tLogDocNo = tDocNo;
                            JCNxPackDataToMQLog(tMsgErrorBody,tErrorStatus,tDisplayEvent,tLogFunction,tLogDocNo);
                        }else{
                            //JCNxSendMQPageNotFound(jqXHR,tPODocNo);
                        }
                    }
                });
            } else {
                $("#odvTransferBchOutPopupCancel").modal("show");
            }

        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    // SubscribeMQ
    function JSoTransferBchOutSubscribeMQ() {
        // Document variable
        var tLangCode       = $("#ohdLangEdit").val();
        var tUsrBchCode     = $("#ohdTransferBchOutBchLogin").val();
        var tUsrApv         = $("#oetTransferBchOutApvCodeUsrLogin").val();
        var tDocNo          = $("#oetTransferBchOutDocNo").val();
        var tPrefix         = "RESTBO";
        var tStaApv         = $("#ohdTransferBchOutStaApv").val();
        var tStaDelMQ       = $("#ohdTransferBchOutStaDelMQ").val();
        var tQName          = tPrefix + "_" + tDocNo + "_" + tUsrApv;

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
            host: "ws://" + oSTOMMQConfig.host + ":15674/ws",
            username: oSTOMMQConfig.user,
            password: oSTOMMQConfig.password,
            vHost: oSTOMMQConfig.vhost
        };

        // Update Status For Delete Qname Parameter
        var poUpdateStaDelQnameParams = {
            ptDocTableName: "TCNTPdtTboHD",
            ptDocFieldDocNo: "FTXthDocNo",
            ptDocFieldStaApv: "FTXthStaPrcStk",
            ptDocFieldStaDelMQ: "FTXthStaDelMQ",
            ptDocStaDelMQ: tStaDelMQ,
            ptDocNo: tDocNo
        };

        // Callback Page Control(function)
        var poCallback = {
            tCallPageEdit: "JSvTransferBchOutCallPageEdit",
            tCallPageList: "JSvTransferBchOutCallPageList"
        };

        // Check Show Progress %
        FSxCMNRabbitMQMessage(
            poDocConfig,
            poMqConfig,
            poUpdateStaDelQnameParams,
            poCallback
        );
    }

    // Get Pdt in Temp
    function JSxTransferBchOutGetPdtInTmp(pnPage, pbUseLoading) {
        // var nStaSession = JCNxFuncChkSessionExpired();
        // if (typeof nStaSession !== "undefined" && nStaSession == 1) {

            var tBchCode = $('#oetTransferBchOutBchCode').val();
            var tMerCode = $('#oetTransferBchOutMchCode').val();
            var tShpCode = $('#oetTransferBchOutShpCode').val();
            var tPosCode = $('#oetTransferBchOutPosCode').val();
            var tWahCode = $('#oetTransferBchOutWahCode').val();

            var tSearchAll = $('#oetTransferBchOutPdtSearchAll').val();

            if (pbUseLoading) {
                JCNxOpenLoading();
            }

            (pnPage == '' || (typeof pnPage) == 'undefined') ? pnPage = 1: pnPage = pnPage;

            $.ajax({
                type: "POST",
                url: "docTransferBchOutGetPdtInTmp",
                data: {
                    tBchCode: tBchCode,
                    tMerCode: tMerCode,
                    tShpCode: tShpCode,
                    tPosCode: tPosCode,
                    tWahCode: tWahCode,
                    nPageCurrent: pnPage,
                    tIsApvOrCancel: (bIsApvOrCancel) ? "1" : "0",
                    tSearchAll: tSearchAll
                },
                cache: false,
                timeout: 5000,
                success: function(oResult) {
                    // JSxTransferBchOutSetEndOfBill(oResult.aEndOfBill);
                    $('#odvTransferBchOutPdtDataTable').html(oResult.html);
                    JCNxCloseLoading();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxCloseLoading();
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });

        // } else {
        //     JCNxShowMsgSessionExpired();
        // }
    }

    // Insert Pdt to Temp
    function JSvTransferBchOutInsertPdtToTemp(ptPdtData) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof nStaSession !== "undefined" && nStaSession == 1) {

            var tTransferBchOutOptionAddPdt = $('#ocmTransferBchOutOptionAddPdt').val();

            JCNxOpenLoading();

            $.ajax({
                type: "POST",
                url: "docTransferBchOutInsertPdtToTmp",
                data: {
                    tPdtData: ptPdtData,
                    ptBchCode: $('#oetTransferBchOutBchCode').val(),
                    tTransferBchOutOptionAddPdt: tTransferBchOutOptionAddPdt
                },
                cache: false,
                timeout: 5000,
                success: function(tResult) {
                    JSxTransferBchOutGetPdtInTmp(1, true);
                    $('#odvTransferBchOutPopupPdtAdd').modal('hide');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxCloseLoading();
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });

        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    //  Clear Pdt in Temp
    function JSvTransferBchOutClearPdtInTemp() {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof nStaSession !== "undefined" && nStaSession == 1) {

            JCNxOpenLoading();

            $.ajax({
                type: "POST",
                url: "docTransferBchOutClearPdtInTmp",
                cache: false,
                timeout: 5000,
                success: function(tResult) {
                    JSxTransferBchOutGetPdtInTmp(1, true);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxCloseLoading();
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });

        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    // Pdt Column Control
    function JSxTransferBchOutPdtColumnControl() {
        $("#odvTransferBchOutPdtColumnControlPanel").modal('show');
        $.ajax({
            type: "POST",
            url: "docTransferBchOutGetPdtColumnList",
            data: {},
            cache: false,
            Timeout: 0,
            success: function(tResult) {
                $("#odvTransferBchOutPdtColummControlDetail").html(tResult);
                // JSCNAdjustTable();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    }

    // Update Pdt Column
    function JSxTransferBchOutUpdatePdtColumn() {
        var aColShowSet = [];
        $(".ocbTransferBchOutPdtColStaShow:checked").each(function() {
            aColShowSet.push($(this).data("id"));
        });

        var aColShowAllList = [];
        $(".ocbTransferBchOutPdtColStaShow").each(function() {
            aColShowAllList.push($(this).data("id"));
        });

        var aColumnLabelName = [];
        $(".olbTransferBchOutColumnLabelName").each(function() {
            aColumnLabelName.push($(this).text());
        });

        var nStaSetDef;
        if ($("#ocbSetToDef").is(":checked")) {
            nStaSetDef = 1;
        } else {
            nStaSetDef = 0;
        }

        $.ajax({
            type: "POST",
            url: "docTransferBchOutUpdatePdtColumn",
            data: {
                aColShowSet: aColShowSet,
                nStaSetDef: nStaSetDef,
                aColShowAllList: aColShowAllList,
                aColumnLabelName: aColumnLabelName
            },
            cache: false,
            Timeout: 0,
            success: function(tResult) {
                $("#odvTransferBchOutPdtColumnControlPanel").modal("hide");
                JSxTransferBchOutGetPdtInTmp(1, true);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    }

    //Browse Pdt
    function JCNvTransferBchOutBrowsePdt() {

        var bIsWahFromEmpty = $('#oetTransferBchOutXthWhFrmCode').val() == "";
        var bIsWahToEmpty = $('#oetTransferBchOutXthWhToCode').val() == "";
        if (bIsWahFromEmpty || bIsWahToEmpty) {
            var tWarningMessage = '?????????????????????????????????????????????????????? ???????????????????????? ???????????????????????????????????????????????????????????????';
            FSvCMNSetMsgWarningDialog(tWarningMessage);
            return;
        }

        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof nStaSession !== "undefined" && nStaSession == 1) {

            var oBrowsePdtSettings = {
                Qualitysearch: [
                    "NAMEPDT",
                    "CODEPDT"
                ],
                PriceType: ["Cost", "tCN_Cost", "Company", "1"],
                //'PriceType'       : ['Pricesell'],
                //'SelectTier'      : ['PDT'],
                SelectTier: ["Barcode"],
                //'Elementreturn'   : ['oetInputTestValue','oetInputTestName'],
                ShowCountRecord: 10,
                NextFunc: "JSvTransferBchOutInsertPdtToTemp",
                ReturnType: "M",
                SPL: ["", ""],
                BCH: [$("#oetTransferBchOutXthBchFrmCode").val(), $("#oetTransferBchOutXthBchFrmCode").val()],
                MER: [$('#oetTransferBchOutXthMerchantFrmCode').val(), $('#oetTransferBchOutXthMerchantFrmCode').val()],
                SHP: [$('#oetTransferBchOutXthShopFrmCode').val(), $('#oetTransferBchOutXthShopFrmCode').val()],
                'aAlwPdtType' : ['T1','T3','T4','T5','T6','S2','S3','S4'],
                'Where' : [" AND Products.FTPdtStkControl = 1"]
            }

            var tMerFromCode = $('#oetTransferBchOutXthMerchantFrmCode').val();
            var bIsMerFromEmpty = (tMerFromCode === '') || (tMerFromCode == undefined);
            if (bIsMerFromEmpty) {
                delete oBrowsePdtSettings.MER;
            }
            var tShpFromCode = $('#oetTransferBchOutXthShopFrmCode').val();
            var bIsShpFromEmpty = (tShpFromCode === '') || (tShpFromCode == undefined);
            if (bIsShpFromEmpty) {
                delete oBrowsePdtSettings.SHP;
            }

            $.ajax({
                type: "POST",
                url: "BrowseDataPDT",
                data: oBrowsePdtSettings,
                cache: false,
                timeout: 5000,
                success: function(tResult) {
                    // $(".modal.fade:not(#odvTBBrowseShipAdd,#odvModalDOCPDT,#odvModalWanning,#odvModalInfoMessage,#odvShowOrderColumn,#odvTBPopupApv,#odvModalDelPdtTB)").remove();
                    $("#odvModalDOCPDT").modal({
                        backdrop: "static",
                        keyboard: false
                    });
                    $("#odvModalDOCPDT").modal({
                        show: true
                    });

                    //remove localstorage
                    localStorage.removeItem("LocalItemDataPDT");
                    $("#odvModalsectionBodyPDT").html(tResult);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    // Display End Of Bill Calc
    function JSxTransferBchOutSetEndOfBill(poParams) {}

    //????????????????????????????????? ??????????????????????????? - ????????????
    function JSxTransferBchOutPrintDoc() {
        var aInfor = [{
                "Lang": '<?= FCNaHGetLangEdit(); ?>'
            },
            {
                "ComCode": '<?= FCNtGetCompanyCode(); ?>'
            },
            {
                "BranchCode": '<?= FCNtGetAddressBranch($tBchCode); ?>'
            },
            {
                "DocCode": $('#oetTransferBchOutDocNo').val()
            },
            {
                "DocBchCode": '<?= $tBchCode ?>'
            }
        ];
        window.open("<?= base_url(); ?>formreport/Frm_SQL_ALLMPdtBillTnfOutBch?infor=" + JCNtEnCodeUrlParameter(aInfor), '_blank');
    }
</script>
