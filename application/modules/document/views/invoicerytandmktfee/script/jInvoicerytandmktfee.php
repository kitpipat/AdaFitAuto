<script type="text/javascript">

    $(document).on("keypress", 'form', function (e) {
        let code    = e.keyCode || e.which;
        if (code == 13) {
            e.preventDefault();
            return false;
        }
    });

    $("document").ready(function () {
        localStorage.removeItem("LocalItemData");
        JSxCheckPinMenuClose(); 
        JSxTRMNavDefult('showpage_list');
        // รองรับการเข้ามาแบบ Noti
        let nStaTRMBrowseType   = $('#oetTRMJumpBrwType').val();
        switch(nStaTRMBrowseType){
            case '2':
                let tAgnCode    = $('#oetTRMJumpAgnCode').val();
                let tBchCode    = $('#oetTRMJumpBchCode').val();
                let tDocNo      = $('#oetTRMJumpDocNo').val();
                JSvTRMCallPageEdit(tDocNo);
            break;
            default:
                JSvTRMCallPageList();
        }
    });

    // Control เมนู
    function JSxTRMNavDefult(ptType){
        switch(ptType){
            case 'showpage_list':
                $("#oliTRMTitleAdd").hide();
                $("#oliTRMTitleInspect").hide();
                $("#odvBtnAddEdit").hide();
                $('#odvBtnTRMPageAddorEdit').show();
                $('#obtTRMCancelDoc').hide();
                $('#obtTRMApproveDoc').hide();
            break;
            case 'showpage_add':
                $("#oliTRMTitleAdd").show();
                $("#oliTRMTitleInspect").hide();
                $("#odvBtnAddEdit").show();
                $('#obtTRMPrintDoc').hide();
                $('#odvBtnTRMPageAddorEdit').hide();
                $('.xCNBTNSaveDoc').show();
                $('#obtTRMCancelDoc').hide();
                $('#obtTRMApproveDoc').hide();
            break;
            case 'showpage_edit':
                $("#oliTRMTitleAdd").hide();
                $("#oliTRMTitleInspect").show();
                $("#odvBtnAddEdit").show();
                $('#odvBtnTRMPageAddorEdit').hide();
                $('#obtTRMPrintDoc').show();
                $('#obtTRMCancelDoc').show();
                $('#obtTRMApproveDoc').show();
            break;
        }
        // ล้างค่า
        localStorage.removeItem('TRM_LocalItemDataDelDtTemp');
        localStorage.removeItem('LocalItemData');
    }

    // Control ปุ่ม และอินพุตต่างๆ [ เอกสารอยู่ระหว่างดำเนินการ StaPrc ]  
    function JSxTRMControlFormWhenDocProcess(){
        var tStatusPrcDoc   = $('#ohdTRMStaPrc').val();
        var tStatusDoc      = $('#ohdTRMStaDoc').val();
        var tStatusApv      = $('#ohdTRMStaApv').val();
        if(tStatusDoc == 1 && tStatusApv == 1){
            // อนุมัติเอกสารเรียบร้อย
            //ปุ่มเลือก
            $('.xCNBtnBrowseAddOn').addClass('disabled');
            $('.xCNBtnBrowseAddOn').attr('disabled',true);
            //ปุ่มเวลา
            $('.xCNBtnDateTime').addClass('disabled');
            $('.xCNBtnDateTime').attr('disabled',true);
            //อินพุต
            $('.xCNInputReadOnly').attr('readonly', true);
            //อินพุต
            $('.form-control').attr('readonly', true);
            //อินพุต
            $('.xControlRmk').attr('disabled', true);

            //พวก selectpicker
            $('.selectpicker').prop("disabled",true);

            //ปุ่มอนุมัติ
            $('#obtTRMApproveDoc').hide();

            // ปุ่มล้างฟอร์มค้นหา และ ปุ่มกรองข้อมูล
            $('#oahTRMAdvanceSearchReset').hide();
            $('#oahTRMAdvanceSearchSubmit').hide();
        } else if(tStatusDoc == 3){
            // เอกสารที่ทำการยกเลิก

        }
    }

    // โหลด หน้าจอ List
    function JSvTRMCallPageList(){
        JSxCheckPinMenuClose();
        $.ajax({
            type    : "GET",
            url     : "docInvoiceRytAndMktFeePageList",
            cache   : false,
            timeout : 5000,
            success : function(tResult){
                $("#odvContentTRM").html(tResult);
                JSxTRMNavDefult('showpage_list');
                JSvTRMCallPageDataTable();
            },
            error   : function(jqXHR, textStatus, errorThrown){
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    }

    // โหลดข้อมูลตาราง
    function JSvTRMCallPageDataTable(pnPage){
        JCNxOpenLoading();
        let oAdvanceSearch  = JSoTRMGetAdvanceSearchData();
        let nPageCurrent    = pnPage;
        if (nPageCurrent == undefined || nPageCurrent == "") {
            nPageCurrent = "1";
        }
        $.ajax({
            type    : "POST",
            url     : "docInvoiceRytAndMktFeeDataTable",
            data    : {
                oAdvanceSearch  : oAdvanceSearch,
                nPageCurrent    : nPageCurrent
            },
            cache   : false,
            timeout : 5000,
            success : function (oResult) {
                $('#ostContentTRM').html(oResult);
                JCNxCloseLoading();
            },
            error   : function (jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    }

    // ดึงข้อมูลค้นหาขั้นสูง 
    function JSoTRMGetAdvanceSearchData() {
        let oAdvanceSearchData = {
            tSearchAll          : $("#oetSearchAll").val(),
            tSearchBchCodeFrom  : $("#oetBchCodeFrom").val(),
            tSearchBchCodeTo    : $("#oetBchCodeTo").val(),
            tSearchSPLCodeFrom  : $('#oetSplCodeFrom').val(),
            tSearchSPLCodeTo    : $('#oetSplCodeTo').val(),
            tSearchDocDateFrom  : $("#oetSearchDocDateFrom").val(),
            tSearchDocDateTo    : $("#oetSearchDocDateTo").val(),
            tSearchStaDoc       : $("#ocmSearchStaDoc").val(),
        };
        return oAdvanceSearchData;
    }

    // เข้าหน้าแบบ เพิ่ม
    function JSvTRMCallPageAdd(ptType){
        try{
            let nStaSession = JCNxFuncChkSessionExpired();
            if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
                JCNxOpenLoading();
                $.ajax({
                    type    : "POST",
                    url     : "docInvoiceRytAndMktFeePageAdd",
                    cache   : false,
                    timeout : 0,
                    success: function(oResult){
                        let aReturnData = JSON.parse(oResult);
                        if (aReturnData['nStaEvent'] == '1') {
                            JSxTRMNavDefult('showpage_add');
                            $('#odvContentTRM').html(aReturnData['tViewPageAdd']);
                            JCNxCloseLoading();
                        } else {
                            let tMessageError   = aReturnData['tStaMessg'];
                            FSvCMNSetMsgErrorDialog(tMessageError);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        JCNxResponseError(jqXHR, textStatus, errorThrown);
                    }
                });
            } else {
                JCNxShowMsgSessionExpired();
            }
        }catch(err){
            console.log('JSvTRMCallPageAdd Error: ', err);
        }
    }

    // เข้าหน้าแบบ แก้ไข
    function JSvTRMCallPageEdit(ptDocumentNumber,ptAgnCode,ptBchCode){
        let nStaSession = JCNxFuncChkSessionExpired();
        if(typeof nStaSession !== "undefined" && nStaSession == 1) {
            JCNxOpenLoading();
            $.ajax({
                type    : "POST",
                url     : "docInvoiceRytAndMktFeePageEdit",
                data    : {
                    'ptTRMDocNo' : ptDocumentNumber,
                    'ptAgnCode'  : ptAgnCode,
                    'ptBchCode'  : ptBchCode,
                },
                cache   : false,
                timeout : 0,
                success: function(tResult){
                    let aReturnData = JSON.parse(tResult);
                    if(aReturnData['nStaEvent'] == '1'){
                        JSxTRMNavDefult('showpage_edit');
                        $('#odvContentTRM').html(aReturnData['tViewPageAdd']);
                        JCNxCloseLoading();
                        //เช็คว่าเอกสารยกเลิก อยู่สถานะไหนเเล้ว
                        JSxTRMControlFormWhenDocProcess();
                    } else {
                        var tMessageError   = aReturnData['tStaMessg'];
                        FSvCMNSetMsgErrorDialog(tMessageError);
                        JCNxCloseLoading();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown){
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    // เช็คก่อนกดปุ่ม บันทึก
    $('#obtTRMSubmitFromDoc').unbind().click(function(){
        let tTRMDocNo       = $('#oetTRMDocNo').val();
        let tTRMAgnCode     = $('#oetTRMAgnCode').val();
        let tTRMBchCode     = $('#ohdTRMBchCode').val();
        // Data Royalty And Markting
        let tTRMAgnCodeTo   = $('#ohdTRMAgnCode').val();
        let tTRMBchCpdeTo   = $('#ohdTRMAgnBchCode').val();
        let tTRMDueDate     = $('#oetTRMDueDate').val();
        let tTRMBbkCode     = $('#oetTRMBbkCode').val();

        // Check Grand RM
        let tTRMChkGrandRM  = $('#ohdTRMCshChkGrandRM').val();
        if(tTRMChkGrandRM > 0){
            if(tTRMAgnCodeTo == '' || tTRMBchCpdeTo == '' || tTRMDueDate == '' || tTRMBbkCode == ''){
                //กรุณากรอกข้อมูลให้ครบถ้วน
                if(tTRMAgnCodeTo == ""){
                    var tTextWarning    = '<?= language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMNotFoundAgnCodeTo');?>';
                }else if(tTRMBchCpdeTo == ""){
                    var tTextWarning    = '<?= language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMNotFoundBchCodeTo');?>';
                }else if(tTRMDueDate == ""){
                    var tTextWarning    = '<?= language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMNotFoundPaidDate');?>';
                }else if(tTRMBbkCode == ""){
                    var tTextWarning    = '<?= language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMNotFoundBookBank');?>';
                }
                $('#odvTRMModalPleseDataInFill #ospTRMModalPleseDataInFill').text(tTextWarning);
                $('#odvTRMModalPleseDataInFill').modal('show');
            }else{
                // Check Data In DT Tmp
                $.ajax({
                    type    : "POST",
                    url     : "docInvoiceRytAndMktFeeChkDataInDTTmp",
                    data    : {
                        'tTRMDocNo'     : tTRMDocNo,
                        'tTRMAgnCode'   : tTRMAgnCode,
                        'tTRMBchCode'   : tTRMBchCode,
                    },
                    cache   : false,
                    timeout : 0,
                    success: function(oResult){
                        let aReturnData = JSON.parse(oResult);
                        if(aReturnData['FNCountChkDTTmp'] == '0'){
                            // ไม่พบข้อมูลในตาราง DTTmp
                            var tTextNotFoundDTTmp  = '<?= language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMNotFoundDTTmp');?>';
                            $('#odvTRMModalPleseDataInFill #ospTRMModalPleseDataInFill').text(tTextNotFoundDTTmp);
                            $('#odvTRMModalPleseDataInFill').modal('show');
                        } else {
                            // JCNxOpenLoading();
                            $('#obtTRMSubmitDocument').click();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        JCNxResponseError(jqXHR, textStatus, errorThrown);
                    }
                });
            }
        }else{
            var tTextWarning    = "ไม่พบข้อมูลยอดเรียกเก็บค่ารอยัลตี้และค่าการตลาดรายเดือน กรุณาตรวจสอบข้อมูล";
            $('#odvTRMModalPleseDataInFill #ospTRMModalPleseDataInFill').text(tTextWarning);
            $('#odvTRMModalPleseDataInFill').modal('show');
        }
    });

    // เพิ่มข้อมูล
    function JSxTRMAddEditDocument(){
        $('#ofmTRMFormAdd').validate({
            focusInvalid    : false,
            onclick         : false,
            onfocusout      : false,
            onkeyup         : false,
            rules           : {
                oetTRMDocNo : {
                    "required" : {
                        depends: function (oElement) {
                            if($("#ohdTRMRoute").val()  ==  "docInvoiceRytAndMktFeeEventAdd"){
                                if($('#ocbTRMStaAutoGenCode').is(':checked')){
                                    return false;
                                }else{
                                    return true;
                                }
                            }else{
                                return false;
                            }
                        }
                    }
                },
                oetTRMDocDate   : {"required" : true},
                oetTRMDocTime   : {"required" : true},
            },
            messages        : {
                oetTRMDocNo     : {"required" : $('#oetTRMDocNo').attr('data-validate-required')},
                oetTRMDocDate   : {"required" : $('#oetTRMDocDate').attr('data-validate-required')},
                oetTRMDocTime   : {"required" : $('#oetTRMDocTime').attr('data-validate-required')}
            },
            errorElement: "em",
            errorPlacement: function (error, element) {
                error.addClass("help-block");
                if(element.prop("type") === "checkbox") {
                    error.appendTo(element.parent("label"));
                }else{
                    var tCheck  = $(element.closest('.form-group')).find('.help-block').length;
                    if(tCheck == 0) {
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
            submitHandler: function (form) {
                if(!$('#ocbTRMStaAutoGenCode').is(':checked')){
                    if($("#ohdTRMRoute").val() ==  "docInvoiceRytAndMktFeeEventAdd"){
                        JSxTRMValidateDocCodeDublicate();
                    }else{
                        JSxTRMSubmitEventByButton();
                    }
                }else{
                    JSxTRMSubmitEventByButton();
                }
            }
        });
    }

    // ถ้าปล่อยให้คีย์เองต้อง ตรวจสอบรหัสเอกสารว่าซ้ำไหม
    function JSxTRMValidateDocCodeDublicate(){
        $.ajax({
            type    : "POST",
            url     : "CheckInputGenCode",
            data    : {
                'tTableName'    : 'TACTRMHD',
                'tFieldName'    : 'FTXphDocNo',
                'tCode'         : $('#oetTRMDocNo').val()
            },
            success: function (oResult) {
                var aResultData = JSON.parse(oResult);
                $("#ohdTRMCheckDuplicateCode").val(aResultData["rtCode"]);
                if($("#ohdTRMCheckDuplicateCode").val() != 1) {
                    $('#ofmTRMFormAdd').validate().destroy();
                }
                $.validator.addMethod('dublicateCode', function(value,element){
                    if($("#ohdTRMRoute").val() == "docInvoiceBillEventAdd"){
                        if($('#ocbTRMStaAutoGenCode').is(':checked')) {
                            return true;
                        }else{
                            if($("#ohdTRMCheckDuplicateCode").val() == 1) {
                                return false;
                            }else{
                                return true;
                            }
                        }
                    }else{
                        return true;
                    }
                });
                // Set Form Validate From Add Document
                $('#ofmTRMFormAdd').validate({
                    focusInvalid    : false,
                    onclick         : false,
                    onfocusout      : false,
                    onkeyup         : false,
                    rules           : {
                        oetTRMDocNo : {"dublicateCode": {}}
                    },
                    messages: {
                        oetTRMDocNo : {"dublicateCode"  : $('#oetTRMDocNo').attr('data-validate-duplicate')}
                    },
                    errorElement: "em",
                    errorPlacement: function (error, element) {
                        error.addClass("help-block");
                        if(element.prop("type") === "checkbox") {
                            error.appendTo(element.parent("label"));
                        }else{
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
                    submitHandler: function (form) {
                        JSxTRMSubmitEventByButton();
                    }
                })

                $("#ofmTRMFormAdd").submit();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }

        });
    }

    // บันทึก
    function JSxTRMSubmitEventByButton(){
        JCNxOpenLoading();
        $('.selectpicker').removeAttr("disabled")
        $.ajax({
            type    : "POST",
            url     : $("#ohdTRMRoute").val(),
            data    : $("#ofmTRMFormAdd").serialize(),
            cache   : false,
            timeout : 0,
            success : function(oResult){
                let aDataReturnEvent    = JSON.parse(oResult);
                if(aDataReturnEvent['nStaReturn'] == '1'){
                    let nStaCallBack    = aDataReturnEvent['nStaCallBack'];
                    let nDocNoCallBack  = aDataReturnEvent['tCodeReturn'];
                    let tAgnCode        = aDataReturnEvent['tAgnCode'];
                    let tBchCode        = aDataReturnEvent['tBchCode'];
                    // ---------------------- Insert File ---------------------- //
                        let oTRMXCallDataTableFile = {
                            ptElementID : 'odvShowDataTable',
                            ptBchCode   : $('#ohdTRMBchCode').val(),
                            ptDocNo     : nDocNoCallBack,
                            ptDocKey    :'TACTRMHD',
                        }
                        JCNxUPFInsertDataFile(oTRMXCallDataTableFile);
                    // --------------------------------------------------------- //
                    switch(nStaCallBack){
                        case 1 :
                            JCNxOpenLoading();
                            JSvTRMCallPageEdit(nDocNoCallBack,tAgnCode,tBchCode);
                        break;
                        case 2 :
                            JSvTRMCallPageAdd();
                        break;
                        case 3 :
                            JSvTRMCallPageList();
                        break;
                        default :
                    }

                    JCNxCloseLoading();
                }else{
                    var tMessageError   = aDataReturnEvent['tStaMessg'];
                    FSvCMNSetMsgErrorDialog(tMessageError);
                }
            },
            error   : function (jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    }

    // ลบเอกสาร
    function JSoTRMDelDocSingle(ptCurrentPage,ptDocNo,ptBchCode){
        let nStaSession = JCNxFuncChkSessionExpired();
        if(typeof nStaSession !== "undefined" && nStaSession == 1) {
            if(typeof(ptDocNo) != undefined && ptDocNo != ""){
                var tTextConfrimDelSingle   = $('#oetTextComfirmDeleteSingle').val()+"&nbsp"+ptDocNo+"&nbsp"+$('#oetTextComfirmDeleteYesOrNot').val();
                $('#odvTRMModalDelDocSingle #ospTextConfirmDelSingle').html(tTextConfrimDelSingle);
                $('#odvTRMModalDelDocSingle').modal('show');
                $('#odvTRMModalDelDocSingle #osmTRMConfirmPdtDTTemp').unbind().click(function(){
                    JCNxOpenLoading();
                    $.ajax({
                        type    : "POST",
                        url     : "docInvoiceRytAndMktFeeEventDelete",
                        data    : {
                            'tDataDocNo'    : ptDocNo
                        },
                        cache   : false,
                        timeout : 0,
                        success : function(oResult){
                            var aReturnData = JSON.parse(oResult);
                            if(aReturnData['nStaEvent'] == '1') {
                                $('#odvTRMModalDelDocSingle').modal('hide');
                                $('#odvTRMModalDelDocSingle #ospTextConfirmDelSingle').html($('#oetTextComfirmDeleteSingle').val());
                                $('.modal-backdrop').remove();
                                setTimeout(function () {
                                    JSvTRMCallPageDataTable(ptCurrentPage);
                                }, 500);
                            }else{
                                JCNxCloseLoading();
                                FSvCMNSetMsgErrorDialog(aReturnData['tStaMessg']);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            JCNxResponseError(jqXHR, textStatus, errorThrown);
                        }
                    });
                });
            }else{
                FSvCMNSetMsgErrorDialog('Error Not Found Document Number !!');
            }
        }else{
            JCNxShowMsgSessionExpired();
        }
    }

    // // ลบเอกสาร หลายตัว
    function JSoTRMDelDocMultiple(){
        let aDataDelMultiple    = $('#odvTRMModalDelDocMultiple #ohdConfirmIDDelMultiple').val();
        let aTextsDelMultiple   = aDataDelMultiple.substring(0, aDataDelMultiple.length - 2);
        let aDataSplit          = aTextsDelMultiple.split(" , ");
        let nDataSplitlength    = aDataSplit.length;
        let aNewIdDelete        = [];
        for ($i = 0; $i < nDataSplitlength; $i++) {
            aNewIdDelete.push(aDataSplit[$i]);
        }
        if (nDataSplitlength > 1) {
            JCNxOpenLoading();
            $.ajax({
                type    : "POST",
                url     : "docInvoiceRytAndMktFeeEventDelete",
                data    : {'tDataDocNo' : aNewIdDelete},
                cache   : false,
                timeout : 0,
                success : function (oResult) {
                    var aReturnData = JSON.parse(oResult);
                    if (aReturnData['nStaEvent'] == '1') {
                        setTimeout(function () {
                            $('#odvTRMModalDelDocMultiple').modal('hide');
                            $('#odvTRMModalDelDocMultiple #ospTextConfirmDelMultiple').empty();
                            $('#odvTRMModalDelDocMultiple #ohdConfirmIDDelMultiple').val('');
                            $('.modal-backdrop').remove();
                            localStorage.removeItem('LocalItemData');
                                JSvTRMCallPageDataTable();
                            }, 1000);
                    } else {
                        JCNxCloseLoading();
                        FSvCMNSetMsgErrorDialog(aReturnData['tStaMessg']);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        }

    }

    //เซตข้อความ กรณีลบหลายรายการ
    function JSxTextinModal() {
        var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
        if (aArrayConvert[0] == null || aArrayConvert[0] == "") {} else {
            var tTextCode = "";
            for ($i = 0; $i < aArrayConvert[0].length; $i++) {
                tTextCode += aArrayConvert[0][$i].nCode;
                tTextCode += " , ";
            }
            //Disabled ปุ่ม Delete
            if (aArrayConvert[0].length > 1) {
                $(".xCNIconDel").addClass("xCNDisabled");
            } else {
                $(".xCNIconDel").removeClass("xCNDisabled");
            }

            $("#ospTextConfirmDelMultiple").text($('#oetTextComfirmDeleteMulti').val());
            $("#ohdConfirmIDDelMultiple").val(tTextCode);
        }
    }

    //เปิดปุ่มให้ลบได้ กรณีลบหลายรายการ
    function JSxShowButtonChoose() {
        var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
        if (aArrayConvert[0] == null || aArrayConvert[0] == '') {
            $('#odvMngTableList #oliBtnDeleteAll').addClass('disabled');
        } else {
            nNumOfArr = aArrayConvert[0].length;
            if (nNumOfArr > 1) {
                $('#odvMngTableList #oliBtnDeleteAll').removeClass('disabled');
            } else {
                $('#odvMngTableList #oliBtnDeleteAll').addClass('disabled');
            }
        }
    }

    //ลบคอลัมน์ในฐานข้อมูล เช็คค่าใน array [หลายรายการ]
    function JStTRMFindObjectByKey(array,key,value){
        for (var i = 0; i < array.length; i++) {
            if (array[i][key] === value) {
                return "Dupilcate";
            }
        }
        return "None";
    }

</script>