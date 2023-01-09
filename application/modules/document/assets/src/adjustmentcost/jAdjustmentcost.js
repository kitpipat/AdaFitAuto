var nStaADCBrowseType = $("#oetADCStaBrowse").val();
var tCallADCBackOption = $("#oetADCCallBackOption").val();

$("document").ready(function () {
    sessionStorage.removeItem("EditInLine");
    localStorage.removeItem("LocalItemData");
    localStorage.removeItem("Ada.ProductListCenter");
    JSxCheckPinMenuClose(); /*Check เปิดปิด Menu ตาม Pin*/
    if (typeof (nStaADCBrowseType) != 'undefined' && nStaADCBrowseType == 0) { // เข้ามาจาก Menulist Tab
        $('#oliADCTitle').unbind().click(function () {
            JSvADCCallPageList();
        });
        $('#obtADCCallPageAdd').unbind().click(function () {
            JSvADCCallPageAdd();
        });
        $('#obtADCCallBackPage').unbind().click(function () {
            JSvADCCallPageList();
        });
        $('#obtADCCancel').unbind().click(function () {
            var nStaSession = JCNxFuncChkSessionExpired();
            if (typeof nStaSession !== "undefined" && nStaSession == 1) {
                JSnADCCancelDoc(false);
            } else {
                JCNxShowMsgSessionExpired();
            }
        });
        $('#obtADCApprove').unbind().click(function () {
            var nStaSession = JCNxFuncChkSessionExpired();
            if (typeof (nStaSession) !== "undefined" && nStaSession == 1) {
                JSxADCSetStatusClickSubmit(2);
                JSxADCApproveDocument(false);
            } else {
                JCNxShowMsgSessionExpired();
            }
        });
        $('#obtADCSubmitFrom').click(function () {
            JSxADCSetStatusClickSubmit(1);
            JSxValidateFormAddADC();
            //$('#obtSubmitADC').click();
            $('#ofmADCFormAdd').submit();
        });


        JSxADCNavDefult();
        JSvADCCallPageList();
    } else if (typeof (nStaADCBrowseType) != 'undefined' && nStaADCBrowseType == 1) { // เข้ามาจาก Modal Browse
        $('#oahADCBrowseCallBack').unbind().click(function () { JCNxBrowseData(tCallADCBackOption); });
        $('#oliADCBrowsePrevious').unbind().click(function () { JCNxBrowseData(tCallADCBackOption); });
        $('#obtADCBrowseSubmit').unbind().click(function () {
            JSxADCSetStatusClickSubmit(1);
            $('#obtSubmitADC').click();
        });
        JSxADCNavDefult();
        JSvADCCallPageAdd();
    } else { }
});


// Function: Set Defult Nav Menu
// Parameters: Document Ready And Button Event Click
// Creator: 23/02/2021 Sooksanti(Nont)
// LastUpdate:
// Return: -
// ReturnType: -
function JSxADCNavDefult() {
    if (typeof (nStaADCBrowseType) != 'undefined' && nStaADCBrowseType == 0) { // เข้ามาจาก Menulist Tab
        $('.xCNChoose').hide();
        $('#oliADCTitleAdd').hide();
        $('#oliADCTitleEdit').hide();
        $('#oliADCTitleDetail').hide();
        $('#odvADCBtnAddEdit').hide();

        $('#odvADCBtnInfo').show();
    } else if (typeof (nStaADCBrowseType) != 'undefined' && nStaADCBrowseType == 1) { // เข้ามาจาก Modal Browse
        $('#odvModalBody #odvADCMainMenu').removeClass('main-menu');
        $('#odvModalBody #oliADCNavBrowse').css('padding', '2px');
        $('#odvModalBody #odvADCBtnGroup').css('padding', '0');
        $('#odvModalBody .xCNADCBrowseLine').css('padding', '0px 0px');
        $('#odvModalBody .xCNADCBrowseLine').css('border-bottom', '1px solid #e3e3e3');
    } else { }
}

// Function: Call Page List Document
// Parameters: Document Redy Function
// Creator: 23/02/2021 Sooksanti(Nont)
// LastUpdate:
// Return: Call View Adjust Cost List
// ReturnType: View
function JSvADCCallPageList() {
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof nStaSession !== "undefined" && nStaSession == 1) {
        $.ajax({
            type: "GET",
            url: "docADCFormSearchList",
            cache: false,
            timeout: 0,
            success: function (tResult) {
                $("#odvADCContentPage").html(tResult);
                JSxADCNavDefult();
                JSvADCCallPageDataTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    } else {
        JCNxShowMsgSessionExpired();
    }
}


// Function: Get Data Advanced Search
// Parameters: Function Call Page
// Creator: 23/02/2021 Sooksanti(Nont)
// LastUpdate: -
// Return: object Data Advanced Search
// ReturnType: object
function JSoADSGetAdvanceSearchData() {
    var oAdvanceSearchData = {
        tSearchAll: $("#oetSearchAll").val(),
        tSearchBchCodeFrom: $("#oetADSBchCodeFrom").val(),
        tSearchBchCodeTo: $("#oetADSBchCodeTo").val(),
        tSearchDocDateFrom: $("#oetADSDocDateFrom").val(),
        tSearchDocDateTo: $("#oetADSDocDateTo").val(),
        tSearchStaDoc: $("#ocmADSStaDoc").val(),
        // tSearchStaDocAct: $("#ocmStaDocAct").val(),
        tSearchStaApprove: $("#ocmADSStaApprove").val(),
        tSearchStaPrcStk: $("#ocmADSStaPrcStk").val()
    };
    return oAdvanceSearchData;
}

// Functionality : Call Page Adjust Stock Add Page
// Parameters : Event Click Buttom
// Creator : 24/02/2021 Sooksanti(Nont)
// Return : View
// Return Type : View
function JSvADCCallPageAdd() {
    JCNxOpenLoading();
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof nStaSession !== "undefined" && nStaSession == 1) {
        $.ajax({
            type: "GET",
            url: "docADCPageAdd",
            cache: false,
            timeout: 0,
            success: function (tResult) {
                var aReturnData = JSON.parse(tResult);
                if (aReturnData['nStaEvent'] == '1') {
                    if (nStaADCBrowseType == '1') {
                        $('#odvModalBodyBrowse .panel-body').css('padding-top', '0');
                        $('#odvModalBodyBrowse').html(aReturnData['tViewPageAdd']);
                    } else {
                        $('#oliADCTitleEdit').hide();
                        $("#obtADCApprove").hide();
                        $("#obtADCCancel").hide();
                        $('#odvADCBtnInfo').hide();
                        $('#obtADCPrint').hide();
                        $('#oliADCTitleAdd').show();
                        $('#odvADCBtnAddEdit').show();

                        $(".xWBtnGrpSaveLeft").show();
                        $(".xWBtnGrpSaveRight").show();

                        $('#odvModalBodyBrowse .panel-body').css('padding-top', '0');
                        $('#odvADCContentPage').html(aReturnData['tViewPageAdd']);
                    }
                    JSxADCNumberRows($("#odvADCTable"));
                    JCNxADCControlObjAndBtn()
                    JSvAdPdtPriDataTable()
                    JCNxCloseLoading();
                } else {
                    var tMessageError = aReturnData['tStaMessg'];
                    FSvCMNSetMsgErrorDialog(tMessageError);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    } else {
        JCNxShowMsgSessionExpired();
    }
}


// Functionality : call page edit
// Parameter : function parameters
// Create : 02/03/2021 Sooksanti(Nont)
// Return : -
// Return Type : -
function JSvADCCallPageEdit(ptXchDocNo, pnXchDocType, pnXchStaPrcDoc) {
    $.ajax({
        type: "POST",
        url: "docADCPageEdit",
        data: {
                ptXchDocNo: ptXchDocNo,
                pnTypeAdc: pnXchDocType
            },
        cache: false,
        timeout: 0,
        success: function (tResult) {
            var aReturnData = JSON.parse(tResult);
            if (tResult != "") {
                $("#oliADCTitleAdd").hide();
                $("#oliADCTitleEdit").show();
                $("#odvADCBtnInfo").hide();
                $("#odvADCBtnAddEdit").show();
                // $("#odvADCContentPage").html(tResult);
                $("#oetADCDocNo").addClass("xCNDisable");
                $(".xCNDisable").attr("readonly", true);
                $(".xCNiConGen").hide();
                $("#obtADCApprove").show();
                $("#obtADCPrint").show();
                $("#obtADCCancel").show();

                $(".xWBtnGrpSaveLeft").show();
                $(".xWBtnGrpSaveRight").show();
                $("#odvGrpOptionFrom").show();

            }
            //Control Event Button
            if ($("#ohdASTAutStaEdit").val() == 0) {
                $(".xCNUplodeImage").hide();
                $(".xCNIconBrowse").show();
                $(".xCNEditRowBtn").show();
                $("select").prop("disabled", false);
                $("input").attr("disabled", false);
            } else {
                $(".xCNUplodeImage").show();
                $(".xCNIconBrowse").show();
                $(".xCNEditRowBtn").show();
                $("select").prop("disabled", false);
                $("input").attr("disabled", false);
            }

            $('#odvADCContentPage').html(aReturnData['tViewPageAdd']);
            JSxADCGetPdtFromDT()
            JSvAdPdtPriDataTable()
            JCNxADCControlObjAndBtn(pnXchStaPrcDoc, pnXchDocType)
        },
        error: function (jqXHR, textStatus, errorThrown) {
            JCNxResponseError(jqXHR, textStatus, errorThrown);
        }
    });
}




// Function: Call Page Data Table Document
// Parameters: Function Call Page
// Creator: 01/03/2021 Sooksanti
// LastUpdate: -
// Return: Call View Adjust Cost Data Table
// ReturnType: View
function JSvADCCallPageDataTable(pnPage) {
    JCNxOpenLoading();
    var oAdvanceSearch = JSoASTGetAdvanceSearchData();
    var nPageCurrent = pnPage;
    if (nPageCurrent == undefined || nPageCurrent == "") {
        nPageCurrent = "1";
    }
    $.ajax({
        type: "POST",
        url: "docADCDataTable",
        data: {
            oAdvanceSearch: oAdvanceSearch,
            nPageCurrent: nPageCurrent,
        },
        cache: false,
        timeout: 0,
        success: function (oResult) {
            var aReturnData = JSON.parse(oResult);
            if (aReturnData['nStaEvent'] == '1') {
                JSxADCNavDefult();
                $('#ostContentAdjustmentcost').html(aReturnData['tViewDataTable']);
            } else {
                var tMessageError = aReturnData['tStaMessg'];
                FSvCMNSetMsgErrorDialog(tMessageError);
            }
            JCNxLayoutControll();
            JCNxCloseLoading();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            JCNxResponseError(jqXHR, textStatus, errorThrown);
        }
    });
}

// Function: Get Data Advanced Search
// Parameters: Function Call Page
// Creator: 25/02/2021 Sooksanti(Nont)
// LastUpdate: -
// Return: object Data Advanced Search
// ReturnType: object
function JSoASTGetAdvanceSearchData() {
    var oAdvanceSearchData = {
        tSearchAll: $("#oetADCSearchAll").val(),
        tSearchBchCodeFrom: $("#oetADCBchCodeFrom").val(),
        tSearchBchCodeTo: $("#oetADCBchCodeTo").val(),
        tSearchDocDateFrom: $("#oetADCDocDateFrom").val(),
        tSearchDocDateTo: $("#oetADCDocDateTo").val(),
        tSearchStaDoc: $("#ocmADCStaDoc").val(),
    };
    return oAdvanceSearchData;
}

//Functionality : เซ็ตค่าเพื่อให้รู้ว่าตอนนี้กดปุ่มบันทึกหลักจริงๆ (เพราะมีการซัมมิทฟอร์มแต่ไม่บันทึกเพื่อให้เกิด validate ใน on blur)
//Parameters : -
//Creator : 01/03/2021 Sooksanti(Nont)
//Update : -
//Return : -
//Return Type : -
function JSxADCSetStatusClickSubmit(pnStatus) {
    $("#ohdCheckADCSubmitByButton").val(pnStatus);
}


//Functionality : main validate form (validate ขั้นที่ 1 ตรวจสอบทั่วไป)
//Parameters : -
//Creator : 01/03/2021 Sooksanti(Non)
//Update : -
//Return : -
//Return Type : -
function JSxValidateFormAddADC() {

    if ($("#ohdCheckADCClearValidate").val() != 0) {
        $('#ofmADCFormAdd').validate().destroy();
    }
    $('#ofmADCFormAdd').validate({
        focusInvalid: false,
        onclick: false,
        onfocusout: false,
        onkeyup: false,
        rules: {
            oetADCDocNo: {
                "required": {
                    depends: function (oElement) {
                        if ($("#ohdADCRoute").val() == "docADCEventAdd") {
                            if ($('#ocbADCStaAutoGenCode').is(':checked')) {
                                return false;
                            } else {
                                return true;
                            }
                        } else {
                            return false;
                        }
                    }
                }
            },
            ohdADCBchName: {
                "required": true
            }
        },
        messages: {
            oetADCDocNo: {
                "required": $('#oetADCDocNo').attr('data-validate-required')
            },
            oetADCDocDate: {
                "required": $('#oetADCDocDate').attr('data-validate-required')
            },
            ohdADCBchName: {
                "required": $('#ohdADCBchName').attr('data-validate-required')
            }
        },
        submitHandler: function (form) {
            if ($("#ohdADCRoute").val() == "docADCEventAdd") {
                if (!$('#ocbADCStaAutoGenCode').is(':checked')) {
                    JSxValidateADCCodeDublicate();
                } else {
                    if ($('#ohdCheckADCSubmitByButton').val() == 1) {
                        JSxADCSubmitEventByButton();
                    }
                }
            } else {
                if ($('#ohdCheckADCSubmitByButton').val() == 1) {
                    JSxADCSubmitEventByButton();
                }
            }

        }
    });
    if ($("#ohdCheckADCClearValidate").val() != 0) {
        $('#ofmADCFormAdd').submit();
        $("#ohdCheckADCClearValidate").val(0);
    }
}

//Functionality : function submit by submit button only (ส่งข้อมูลที่ผ่านการ validate ไปบันทึกฐานข้อมูล)
//Parameters : route
//Creator : 01/03/2021 Sooksanti
//Update : -
//Return : -
//Return Type : -
function JSxADCSubmitEventByButton() {
    JCNxOpenLoading();
    JSxADCInsertDT();
}

function JSxADCInsertDT(){
    // var nRowlength = $('#ofmADCFormAdd tr.ostAdDataPdtPri').length;
    // $oForm = $('#ofmADCFormAdd').serialize() + '&nStaAction=' + '1';
    $.ajax({
        type: "POST",
        url: $("#ohdADCRoute").val(),
        data: {
            ohdADCDocNo: $('#ohdADCDocNo').val(),
            ohdADCBchCode: $('#ohdADCBchCode').val(),
            oetADCDocDate: $('#oetADCDocDate').val(),
            oetADCDocTime: $('#oetADCDocTime').val(),
            oetADCEffectiveDate: $('#oetADCEffectiveDate').val(),
            oetADCRefInt: $('#oetADCRefInt').val(),
            oetADCRefIntDate: $('#oetADCRefIntDate').val(),
            otaADCRmk: $('#otaADCRmk').val(),
            ocmADCDocType : $("#ocmADCDocType").val(),
        },
        cache: false,
        timeout: 0,
        success: function (tResult) {
            var aReturn = JSON.parse(tResult);
            if (nStaADCBrowseType != 1) {
                if (aReturn['nStaEvent'] == 1) {
                  var oSOCallDataTableFile = {
                             ptElementID  : 'odvShowDataTable',                     //div ที่ต้องการแสดงผลรายการไฟล์แนบ
                             ptBchCode    : $("#oetADCBchCodeFile").val(),                                  //รหัสสาขา
                             ptDocNo      : aReturn['tCodeReturn'],                       //เลขที่เอกสาร
                             ptDocKey     : 'TCNTPdtAdjCostHD',                                 //ชื่อตารางของเอกสาร
                  }
                 JCNxUPFInsertDataFile(oSOCallDataTableFile);
                    switch (aReturn['nStaCallBack']) {
                        case '1':
                            JSvADCCallPageEdit(aReturn['tCodeReturn'],aReturn['nDocType']);
                            break;
                        case '2':
                            JSvADCCallPageAdd();
                            break;
                        case '3':
                            JSvADCCallPageList();
                            break;
                        default:
                            JSvADCCallPageEdit(aReturn['tCodeReturn'],aReturn['nDocType']);
                    }
                } else {
                    FSvCMNSetMsgErrorDialog(aReturn['tStaMessg']);
                    JCNxCloseLoading();
                }
            } else {
                JCNxCloseLoading();
                JCNxBrowseData(tCallSpaBackOption);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            JCNxResponseError(jqXHR, textStatus, errorThrown);
        }
    });
}
// Functionality : Action for approve
// Parameters : pbIsConfirm
// Creator : 03/03/2021
// Last Modified : -
// Return : -
// Return Type : -
function JSnADCCancelDoc(pbIsConfirm) {

    var tXchDocNo = $("#ohdADCDocNo").val();
    var nDocType = $("#ocmADCDocType").val();

    if (pbIsConfirm) {
        $.ajax({
            type: "POST",
            url: "docADCCancel",
            data: {
                tXchDocNo: tXchDocNo
            },
            cache: false,
            timeout: 5000,
            success: function (tResult) {
                $("#odvADCPopupCancel").modal("hide");
                aResult = $.parseJSON(tResult);
                if (aResult.nSta == 1) {
                    JSvADCCallPageEdit(tXchDocNo,nDocType);

                } else {
                    JCNxCloseLoading();
                    tMsgBody = aResult.tMsg;
                    FSvCMNSetMsgWarningDialog(tMsgBody);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    } else {
        //Check Status Approve for Control Msg In Modal
        nStaApv = $("#ohdADCStaApv").val();
        if (nStaApv == 1) {
            $("#obpMsgApv").show();
        } else {
            $("#obpMsgApv").hide();
        }
        $("#odvADCPopupCancel").modal("show");

    }
}

//Function : Control Object And Button ปิด เปิด
// Parameter : function parameters
// Create : 02/03/2021 Sooksanti(Nont)
// Return : -
// Return Type : -
function JCNxADCControlObjAndBtn(pnXchStaPrcDoc, pnXchDocType) {
    //Check สถานะอนุมัติ
    var nXthStaApv = $("#ohdADCStaApv").val();
    var nADCStaDoc = $("#ohdADCStaDoc").val();
    var nADCStaPrc = pnXchStaPrcDoc;
    var nDocType   = pnXchDocType;

    //Set Default
    //Btn Cancel
    $("#obtADCCancel").attr("disabled", false);
    //Btn Apv
    $("#obtADCApprove").attr("disabled", false);
    $("#obtADCPrint").attr("disabled", false);
    $(".xControlForm").attr("disabled", false);
    $(".ocbListItem").attr("disabled", false);
    // $(".xCNBtnBrowseAddOn").attr("disabled", false);
    $(".xCNBtnDateTimeControl").attr("disabled", false);
    $(".xCNDocBrowsePdt")
        .attr("disabled", false)
        .removeClass("xCNBrowsePdtdisabled");
    $(".xCNDocDrpDwn").show();
    $("#oetADCSearchPdtHTML").attr("disabled", false);
    $(".xWBtnGrpSaveLeft").attr("disabled", false);
    $(".xWBtnGrpSaveRight").attr("disabled", false);
    $("#oliBtnEditShipAdd").show();
    $("#oliBtnEditTaxAdd").show();

    if (nXthStaApv == '1') {
        if (nDocType == 12) {
            if (nADCStaPrc == '') {
                $("#obtADCCancel").show();
            } else {
                $("#obtADCCancel").hide();
            }
        } else {
            $("#obtADCCancel").hide();
        }
        $('#oliADCTitleDetail').show();
        $('#oliADCTitleEdit').hide();
        //Btn Apv
        $("#obtADCApprove").hide();
        $("#obtADCPrint").attr("disabled", false);
        $("#odvGrpOptionFrom").hide();

        $('.xCNHideWhenApvOrCancel').hide();

        // $("#obtADCCancel").hide();
        $(".xWBtnGrpSaveLeft").show();
        $(".xWBtnGrpSaveRight").show();

        //Control input ปิด
        $(".xControlForm").attr("disabled", true);
        $(".ocbListItem").attr("disabled", true);
        $(".xCNBtnBrowseAddOn").attr("disabled", true);
        $(".xCNBtnDateTimeControl").attr("disabled", true);
        $(".xCNDocBrowsePdt")
            .attr("disabled", true)
            .addClass("xCNBrowsePdtdisabled");
        $(".xCNDocDrpDwn").hide();
        $("#oetADCSearchPdtHTML").attr("disabled", false);
        $("#oliBtnEditShipAdd").hide();
        $("#oliBtnEditTaxAdd").hide();
        $("#obtImportPDTInCN").attr("disabled", true);
        $("#ocbADCPurchase").attr("disabled", true);
        $("#ocbADCAddDoc").attr("disabled", true);
        $(".xCNIconTable").addClass("xCNDocDisabled");
        $(".xCNIconTable").attr("onclick", "").unbind("click");
        $(".xWBtnDelPdt").attr("disabled", true);
        $(".xCNImportBtn").attr("disabled", true);
    }

    //Check สถานะเอกสาร
    if (nADCStaDoc == '3') {
        $('#oliADCTitleDetail').show();
        $('#oliADCTitleEdit').hide();
        $("#obtADCCancel").hide();
        $("#obtADCApprove").hide();
        $("#obtADCPrint").hide();
        $(".xControlForm").attr("disabled", true);
        $(".ocbListItem").attr("disabled", true);
        $(".xCNBtnBrowseAddOn").attr("disabled", true);
        $(".xCNBtnDateTimeControl").attr("disabled", true);
        $(".xCNDocBrowsePdt")
            .attr("disabled", true)
            .addClass("xCNBrowsePdtdisabled");
        $(".xCNDocDrpDwn").hide();
        $("#oetADCSearchPdtHTML").attr("disabled", false);
        $("#odvGrpOptionFrom").hide();
        $(".xWBtnGrpSaveLeft").hide();
        $(".xWBtnGrpSaveRight").hide();
        $("#oliBtnEditShipAdd").hide();
        $("#oliBtnEditTaxAdd").hide();
        $("#obtImportPDTInCN").attr("disabled", true);
        $("#ocbADCPurchase").attr("disabled", true);
        $(".xCNIconTable").addClass("xCNDocDisabled");
        $(".xCNIconTable").attr("onclick", "").unbind("click");
        $("#ocbADCAddDoc").attr("disabled", true);
        $(".xWBtnDelPdt").attr("disabled", true);
        $(".xCNImportBtn").attr("disabled", true);
        $('.xCNHideWhenApvOrCancel').hide();
    }
}


// Functionality : Applove Document
// Parameters : Event Click Buttom
// Creator : 03/03/2021 Sooksanti
// LastUpdate: -
// Return : Status Applove Document
// Return Type : -
function JSxADCApproveDocument(pbIsConfirm) {
    if (pbIsConfirm) {
        $("#odvADCModalAppoveDoc").modal("hide");
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();

        var tADCDocNo = $("#ohdADCDocNo").val();
        var tADCBchCode = $('#ohdADCBchCode').val();
        var tADCStaApv = $("#ohdADCStaApv").val();

        $.ajax({
            type: "POST",
            url: "docADCApproveDocument",
            data: {
                'ptADCDocNo': tADCDocNo,
                'ptADCBchCode': tADCBchCode,
                'ptADCStaApv': tADCStaApv,
            },
            cache: false,
            timeout: 0,
            success: function (tResult) {
                try {
                    let oResult = JSON.parse(tResult);
                    if (oResult.nStaEvent == "900") {
                        FSvCMNSetMsgErrorDialog(oResult.tStaMessg);
                    }
                } catch (err) { }
                JSoADCCallSubscribeMQ();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    } else {
        $('#odvADCModalAppoveDoc').modal({ backdrop: 'static', keyboard: false });
        $("#odvADCModalAppoveDoc").modal("show");
    }
}

// Functionality : Call Data Subscript Document
// Parameters : Event Click Buttom
// Creator : 03/03/2021 Sooksanti(Non)
// LastUpdate: -
// Return : Status Applove Document
// Return Type : -
function JSoADCCallSubscribeMQ() {
    // RabbitMQ
    /*===========================================================================*/
    // Document variable
    var tLangCode = $("#ohdADCLang").val();
    var tUsrBchCode = $("#ohdADCBchCode").val();
    var tUsrApv = $("#ohdADCUsrApvMQ").val();
    var tDocNo = $("#ohdADCDocNo").val();
    var tPrefix = "RESADJCOST";
    var tStaApv = $("#ohdADCStaApv").val();
    var tQName = tPrefix + "_" + tDocNo + "_" + tUsrApv;

    // MQ Message Config
    var poDocConfig = {
        tLangCode: tLangCode,
        tUsrBchCode: tUsrBchCode,
        tUsrApv: tUsrApv,
        tDocNo: tDocNo,
        tPrefix: tPrefix,
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
        ptDocTableName: "TCNTPdtAdjCostHD",
        ptDocFieldDocNo: "FTXchDocNo",
        ptDocFieldStaApv: "FTXchStaApv",
        ptDocFieldStaDelMQ:"",
        ptDocNo: tDocNo
    };

    // Callback Page Control(function)
    var poCallback = {
        tCallPageEdit: "JSvADCCallPageEdit",
        tCallPageList: "JSvADCCallPageList"
    };

    // Check Show Progress %
    FSxCMNRabbitMQMessage(poDocConfig, poMqConfig, poUpdateStaDelQnameParams, poCallback);
}


//Functionality: Insert Text In Modal Delete
//Parameters: LocalStorage Data
//Creator: 03/03/2021
//Return: -
//Return Type: -
function JSxADCTextInModal() {
    var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
    if (aArrayConvert[0] == null || aArrayConvert[0] == "") { } else {
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
        $("#ospTextConfirmDelMultiple").text("ท่านต้องการลบข้อมูลทั้งหมดหรือไม่ ?");
        $("#ohdConfirmIDDelete").val(tTextCode);
        $("#ohdConfirmIDDelMultiple").val(tTextCode);
    }
}

//Functionality: Function Chack Value LocalStorage
//Parameters: Event Select List Branch
//Creator: 03/03/2021 Sooksanti
//Return: Duplicate/none
//Return Type: string
function JStADCFindObjectByKey(array, key, value) {
    for (var i = 0; i < array.length; i++) {
        if (array[i][key] === value) {
            return "Dupilcate";
        }
    }
    return "None";
}

// Function: Event Mutiple Delete Doc Mutiple
// Parameters: Function Call Page
// Creator: 03/03/2021 Sooksanti
// LastUpdate: -
// Return: object Data Sta Delete
// ReturnType: object
function JSoADCDelDocMultiple() {
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof (nStaSession) !== 'undefined' && nStaSession == 1) {
        var aDataDelMultiple = $('#odvADCModalDelDocMultiple #ohdConfirmIDDelMultiple').val();
        var aTextsDelMultiple = aDataDelMultiple.substring(0, aDataDelMultiple.length - 2);
        var aDataSplit = aTextsDelMultiple.split(" , ");
        var nDataSplitlength = aDataSplit.length;
        var aNewIdDelete = [];

        for ($i = 0; $i < nDataSplitlength; $i++) {
            aNewIdDelete.push(aDataSplit[$i]);
        }
        if (nDataSplitlength > 1) {
            JCNxOpenLoading();
            localStorage.StaDeleteArray = '1';
            $.ajax({
                type: "POST",
                url: "docADCEventDelete",
                data: { 'tADCDocNo': aNewIdDelete },
                cache: false,
                timeout: 0,
                success: function (oResult) {
                    var aReturnData = JSON.parse(oResult);
                    if (aReturnData['nStaEvent'] == '1') {
                        setTimeout(function () {
                            $('#odvADCModalDelDocMultiple').modal('hide');
                            $('#odvADCModalDelDocMultiple #ospTextConfirmDelMultiple').empty();
                            $('#odvADCModalDelDocMultiple #ohdConfirmIDDelMultiple').val('');
                            $('.modal-backdrop').remove();
                            localStorage.removeItem('LocalItemData');
                            JSvADCCallPageList();
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
    } else {
        JCNxShowMsgSessionExpired();
    }
}

//Functionality: Function Chack And Show Button Delete All
//Parameters: LocalStorage Data
//Creator: 03/03/2021 Sooksanti
//Return: Show Button Delete All
//Return Type: -
function JSxADCShowButtonChoose() {
    var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
    if (aArrayConvert[0] == null || aArrayConvert[0] == "") {
        $("#oliBtnDeleteAll").addClass("disabled");
    } else {
        nNumOfArr = aArrayConvert[0].length;
        if (nNumOfArr > 1) {
            $("#oliBtnDeleteAll").removeClass("disabled");
        } else {
            $("#oliBtnDeleteAll").addClass("disabled");
        }
    }
}

// Function: Event Single Delete Document Single
// Parameters: Event Click Button Delete Document Single
// Creator: 03/03/2021
// LastUpdate: -
// Return: object Data Status Delete
// ReturnType: object
function JSoADCDelDocSingle(ptCurrentPage, ptADCDocNo) {
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof nStaSession !== "undefined" && nStaSession == 1) {
        $('#odvADCModalDelDocSingle #ospTextConfirmDelSingle').html($('#oetTextComfirmDeleteSingle').val() + ptADCDocNo);
        $('#odvADCModalDelDocSingle').modal('show');
        $('#odvADCModalDelDocSingle #osmADCConfirmPdtDTTemp ').unbind().click(function () {
            JCNxOpenLoading();
            $.ajax({
                type: "POST",
                url: "docADCEventDelete",
                data: { 'tADCDocNo': ptADCDocNo },
                cache: false,
                timeout: 0,
                success: function (oResult) {
                    var aReturnData = JSON.parse(oResult);
                    if (aReturnData['nStaEvent'] == '1') {
                        $('#odvADCModalDelDocSingle').modal('hide');
                        $('#odvADCModalDelDocSingle #ospTextConfirmDelSingle').html($('#oetTextComfirmDeleteSingle').val());
                        $('.modal-backdrop').remove();
                        setTimeout(function () {
                            JSvADCCallPageDataTable(ptCurrentPage);
                        }, 500);
                    } else {

                        JCNxCloseLoading();
                        FSvCMNSetMsgErrorDialog(aReturnData['tStaMessg']);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        });
    } else {
        JCNxShowMsgSessionExpired();
    }
}

// Function: GetData Insert
function JSxADCGetDataFromTableInsert() {
    var aData = [];

    $('#odvADCPdtTablePanal').find('tr').each(function (i, el) {
        // no thead
        if (i != 0) {
            var $tds = $(this).find('td');
            var aRow = [];

            $tds.each(function (i, el) {
                if (i == 1) {
                    aRow.push($(this).text());
                }
                if (i == 2) {
                    aRow.push($(this).text());
                }
                if (i == 3) {
                    aRow.push($(this).text());
                }
                if (i == 4) {
                    aRow.push($(this).text());
                }

                if (i == 6) {
                    aRow.push($(this).text());
                }
                if (i == 7) {
                    var tCostDiff = $(this).text()
                    if (tCostDiff == '') {
                        tCostDiff = 0
                    }
                    aRow.push(tCostDiff);
                }
                if (i == 8) {
                    aRow.push($(this).parents("tr").find(".xCNPdtEditInLine").val());
                }

                if (i == 9) {
                    if($(this).text() == ''){
                        aRow.push(1);
                    }else{
                        aRow.push(0);
                    }
                }

                if (i == 11) {
                    aRow.push($(this).text());
                }
                if (i == 12) {
                    aRow.push($(this).text());
                }

            });

            if(aRow.length != 0){
                aData.push(aRow);
            }

        }

    });
    return aData;
}

//function: Call Product Price Data List
//Parameters: Ajax Success Event 
//Creator:	18/02/2019 Napat(Jame)
//Return: View
//Return Type: View
// ptFocusType = 1 focus input , 2 focus scaner
function JSvAdPdtPriDataTable(pnPage, ptFocusType) {
    var nStaSession = 1;
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
        var tSearchAll  = $('#oetSearchSpaPdtPri').val();
        var FTXphDocNo  = $('#oetXphDocNo').val();
        var tFocusType  = (ptFocusType === undefined || ptFocusType == '') ? '1' : ptFocusType;
        if ($('#ofmADCFormAdd tr.ostAdDataPdtPri').length == 0) {
            if (pnPage != undefined) {
                pnPage = pnPage - 1;
            }
        }
        nPageCurrent    = (pnPage === undefined || pnPage == '' || pnPage <= 0) ? '1' : pnPage;
        $.ajax({
            type    : "POST",
            url     : "docADCPdtPriDataTable",
            data    : {
                tSearchAll      : tSearchAll,
                nPageCurrent    : nPageCurrent,
                FTXphDocNo      : FTXphDocNo,
            },
            cache: false,
            Timeout: 0,
            success: function(tResult) {
                $('#ostAdDataPdtPri').html(tResult);
                let oParameterSend = {
                    "FunctionName": "JSxSpaSaveInLine",
                    "DataAttribute": ["dataSEQ", "dataPRICE", "dataPAGE"],
                    "TableID": "otbAdDataList",
                    "NotFoundDataRowClass": "xWTextNotfoundDataSalePriceAdj",
                    "EditInLineButtonDeleteClass": "xWDeleteBtnEditButton",
                    "LabelShowDataClass": "xWShowInLine",
                    "DivHiddenDataEditClass": "xWEditInLine"
                };
                // JCNxSetNewEditInline(oParameterSend);
                if (tFocusType == '1') {
                    $(".xWEditInlineElement").eq(nIndexInputEditInline).focus(function() {
                        this.select();
                    });
                    setTimeout(function() {
                        $(".xWEditInlineElement").eq(nIndexInputEditInline).focus();
                    }, 300);
                }
                $(".xWEditInlineElement").removeAttr("disabled");
                let oElement = $(".xWEditInlineElement");
                for (let nI = 0; nI < oElement.length; nI++) {
                    $(oElement.eq(nI)).val($(oElement.eq(nI)).val().trim());
                }

                $(".xWEditInlineElement").css({
                    "padding": "0px",
                    "text-align": "right"
                });

                // var oParameterEditInLine    = {
                //     "DocModules"                    : "",
                //     "FunctionName"                  : "JSxPISaveEditInline",
                //     "DataAttribute"                 : ['data-field', 'data-seq'],
                //     "TableID"                       : "otbPIDocPdtAdvTableList",
                //     "NotFoundDataRowClass"          : "xWPITextNotfoundDataPdtTable",
                //     "EditInLineButtonDeleteClass"   : "xWPIDeleteBtnEditButtonPdt",
                //     "LabelShowDataClass"            : "xWShowInLine",
                //     "DivHiddenDataEditClass"        : "xWEditInLine"
                // }
                // JCNxSetNewEditInline(oParameterSend);
                // $(".xWEditInlineElement").eq(nIndexInputEditInline).focus();
                // $(".xWEditInlineElement").eq(nIndexInputEditInline).select();
                // $(".xWEditInlineElement").removeAttr("disabled");

                // let oElement = $(".xWEditInlineElement");
                // for(let nI=0;nI<oElement.length;nI++){
                //     $(oElement.eq(nI)).val($(oElement.eq(nI)).val().trim());
                // }

                var tSPAFitstPdtCode = $('#oetSPAFitstPdtCode').val();
                if (tSPAFitstPdtCode != '') {
                    var tAttrIdPdtCodeFirst = $('#ohdSPAFrtPdtCode' + tSPAFitstPdtCode).val();
                    if ($('#' + tAttrIdPdtCodeFirst).val() != '' && $('#' + tAttrIdPdtCodeFirst).val() != undefined) {

                        var tValueNext = parseFloat($('#' + tAttrIdPdtCodeFirst).val().replace(/,/g, ''));
                        $('#' + tAttrIdPdtCodeFirst).val(tValueNext);
                        if (tFocusType == '1') {
                            $('#' + tAttrIdPdtCodeFirst).focus();
                            $('#' + tAttrIdPdtCodeFirst).select();
                        }
                    }
                }

                // JSxSpaNavDefult();
                JCNxLayoutControll();
                // JStCMMGetPanalLangHTML('TCNMPdtSize_L'); //โหลดภาษาใหม่
                //JSxDisableInput();
                JCNxCloseLoading();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });

    } else {

        JCNxShowMsgSessionExpired();

    }

}

//พวกตัวเลขใส่ comma ให้มัน
function numberWithCommas(x) {
    return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
}

//Function Save product price list inline
function JSxSpaSaveInLine(oEvent, oElm) {
    // var nStaSession = 1;
    var nStaSession = 1;
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {

        // var nSeq        = oElm.DataAttribute[0]['dataSEQ'];
        // var tPrice      = oElm.DataAttribute[1]['dataPRICE'];
        // var nPage       = oElm.DataAttribute[2]['dataPAGE'];
        // var tValue      = oElm.VeluesInline;
        var nDecimalShow = $('#nDecimalShow').val();
        var nSeq = $(oElm).attr('seq');
        var tPrice = $(oElm).attr('columname');
        var tColValidate = $(oElm).attr('col-validate');
        var nPage = $(oElm).attr('page');
        var b4value = parseFloat($(oElm).attr('b4value'));
        var tValue = ($(oElm).val() == "") ? 0 : parseFloat($(oElm).val().replace(/,/g, ''));
        // alert(tValue);
        // console.log(b4value);
        // console.log(tValue);
        //console.log(oElm);
        // if(tValue == ""){
        //     alert('Value is null');
        //     JSvSpaPdtPriDataTable();
        // }else{
        // var tRet = parseFloat($('#ohdFCXtdPriceRet'+pnSeq).val());
        // var tWhs = parseFloat($('#ohdFCXtdPriceWhs'+pnSeq).val());
        // var tNet = parseFloat($('#ohdFCXtdPriceNet'+pnSeq).val());
        var tDiff = $('#Diff' + nSeq).val()
        var tDocNo = $('#ostAdDataPdtPri' + nSeq).data('doc');
        var tPdtCode = $('#ostAdDataPdtPri' + nSeq).data('code');
        var tPunCode = $('#ostAdDataPdtPri' + nSeq).data('pun');
        var tSeq = $('#ostAdDataPdtPri' + nSeq).data('seq');
        var oetSearchSpaPdtPri = $('#oetSearchSpaPdtPri').val();
        // $('.xWShowValueFCXtdPriceRet'+pnSeq).text(tRet.toFixed(nDecimalShow));
        // $('.xWShowValueFCXtdPriceWhs'+pnSeq).text(tWhs.toFixed(nDecimalShow));
        // $('.xWShowValueFCXtdPriceNet'+pnSeq).text(tNet.toFixed(nDecimalShow));

        // $('.xWEditInLine'+pnSeq).addClass('xCNHide');
        // $('.xWShowInLine'+pnSeq).removeClass('xCNHide');
        // $('.xWShowIconSaveInLine'+pnSeq).addClass('xCNHide');
        // $('.xWShowIconEditInLine'+pnSeq).removeClass('xCNHide');
        // $('.xWShowIconCancelInLine'+pnSeq).addClass('xCNHide');

        // JCNxOpenLoading();

        // console.log(nDecimalShow,'tDocNo:',tDocNo,'tPdtCode:',tPdtCode,'tPunCode:',tPunCode,'tPrice:',tPrice,'tValue:',tValue, 'tDiff:',tDiff)
            // $(oElm).addClass('xCNHide');
            $.ajax({
                type: "POST",
                url: "docADCPdtPriEventUpdPriTmp",
                data: {
                    'FTXthDocNo': tDocNo,
                    'FTPdtCode': tPdtCode,
                    'FTPunCode': tPunCode,
                    'ptPrice': tPrice,
                    'ptValue': tValue,
                    'tSearchSpaPdtPri': oetSearchSpaPdtPri,
                    'tSeq': tSeq,
                    'tDiff': tDiff,
                    'tColValidate': tColValidate
                },
                cache: false,
                success: function(pResutl) {
                    var objResult = JSON.parse(pResutl);
                    // $(oElm).removeClass('xCNHide');
                    $(oElm).val(numberWithCommas(tValue.toFixed(nDecimalShow)));
                    $(oElm).attr('b4value', tValue);
                    // $('#otdSPATotalPrice').text(objResult['cSpaTotalPrice']);

                    var tStatus = $(oElm).parents(".ostAdDataPdtPri").data("status");
                    if (tStatus == "3") {
                        $(oElm).parents(".ostAdDataPdtPri").find(".xCNAdjPriceStaRmk").text("").removeClass("text-danger");
                    }
                    // JSvSpaPdtPriDataTable(nPage);
                    // JCNxCloseLoading();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });

        //}
        if (oEvent.keyCode == 13) {
            var tNextElement = $(oElm).closest('form').find('input[type=text]');
            var tNextElementID = tNextElement.eq(tNextElement.index(oElm) + 1).attr('id');
            // console.log(tNextElementID);
            var tValueNext = parseFloat($('#' + tNextElementID).val().replace(/,/g, ''));
            $('#' + tNextElementID).val(tValueNext);
            $('#' + tNextElementID).focus();
            $('#' + tNextElementID).select();

        }
    } else {
        JCNxShowMsgSessionExpired();
    }
}

//Functionality : Event Single Delete
//Parameters : Event Icon Delete
//Creator : 25/02/2019 Napat(Jame)
//Return : object Status Delete
//Return Type : object
function JSoAdPdtPriDel(pnPage, ptDocNo, ptPdtCode, ptPunCode, pnSeq, ptSta, ptName) {

    var nStaSession = 1;
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {

        $('#odvModalDelAdPdtPri').modal('show');
        $('#ospConfirmDelete').html($('#oetTextComfirmDeleteSingle').val() + ptPdtCode + '(' + ptName + ')');
        $('#osmConfirm').off('click');
        $('#osmConfirm').on('click', function() {
            $.ajax({
                type: "POST",
                url: "docADCdtPriEventDelete",
                data: {
                    'tDocNo': ptDocNo,
                    'tPdtCode': ptPdtCode,
                    'tPunCode': ptPunCode,
                    'tSeq': pnSeq,
                    'tSta': ptSta
                },
                cache: false,
                success: function(tResult) {
                    $('#odvModalDelAdPdtPri').modal('hide');
                    $('#ospConfirmDelete').text($('#oetTextComfirmDeleteSingle').val());
                    $('#ohdConfirmPdtDelete').val('');
                    $('#ohdConfirmPunDelete').val('');
                    $('#ohdConfirmDocDelete').val('');
                    localStorage.removeItem('LocalItemData');
                    $('.modal-backdrop').remove();
                    JCNxOpenLoading();
                    JSvAdPdtPriDataTable(pnPage);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                    //ส่งขอมูลไปรวบรวมที่ Center ก่อนส่ง MQ เพื่อเก็บ LOG (TYPE:ERROR)
                    if (jqXHR.status != 404){
                        var tLogFunction = 'ERROR';
                        var tDisplayEvent = 'ลบใบปรับราคาขาย ';
                        var tErrorStatus = jqXHR.status;
                        var tHtmlError = $(jqXHR.responseText);
                        var tMsgErrorBody = tHtmlError.find('p:nth-child(3)').text();
                        var tLogDocNo   = ptDocNo;
                        JCNxPackDataToMQLog(tMsgErrorBody,tErrorStatus,tDisplayEvent,tLogFunction,tLogDocNo);
                    }else{
                        //JCNxSendMQPageNotFound(jqXHR,ptPODocNo);
                    }
                }
            });
        });

    } else {

        JCNxShowMsgSessionExpired();

    }
}

//Functionality: Event Multi Delete
//Parameters: Event Button Delete All
//Creator: 25/02/2019 Napat(Jame)
//Return:  object Status Delete
//Return Type: object
function JSoAdPdtPriDelChoose(pnPage) {
    var nStaSession = 1;
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {

        JCNxOpenLoading();
        var aDocData = $('#oetXphDocNo').val();

        var oPdtDataItem = JSON.parse(localStorage.getItem('LocalItemData'));
        var nPdtDataItemLength = oPdtDataItem.length;

        if (nPdtDataItemLength > 1) {
            localStorage.StaDeleteArray = '1';
            $.ajax({
                type: "POST",
                format: "JSON",
                url: "docADCdtPriEventDelete",
                data: {
                    'tDocNo': aDocData,
                    'tDelType': "M",
                    'tPdtDataItem': JSON.stringify(oPdtDataItem)
                },
                success: function(tResult) {
                    setTimeout(function() {
                        $('#odvModalDelAdPdtPri').modal('hide');
                        JCNxCloseLoading();
                        JSvAdPdtPriDataTable(pnPage);
                        $('#ospConfirmDelete').text($('#oetTextComfirmDeleteSingle').val());
                        $('#ohdConfirmSeqDelete').val('');
                        $('#ohdConfirmPdtDelete').val('');
                        $('#ohdConfirmPunDelete').val('');
                        $('#ohdConfirmDocDelete').val('');
                        localStorage.removeItem('LocalItemData');
                        $('.obtChoose').hide();
                        $('.modal-backdrop').remove();
                    }, 1000);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        } else {
            localStorage.StaDeleteArray = '0';
            return false;
        }
    } else {
        JCNxShowMsgSessionExpired();
    }

}

// Functionality: เปลี่ยนหน้า Pagenation หน้า Table List Document
// Parameters: Event Click Pagenation
// Creator: 03/03/2021
// Return: View
// ReturnType : View
function JSvADCClickPage(ptPage) {
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof nStaSession !== "undefined" && nStaSession == 1) {
        var nPageCurrent = "";
        switch (ptPage) {
            case "next": //กดปุ่ม Next
                nPageOld = $(".xWPageADCPdt .active").text(); // Get เลขก่อนหน้า
                nPageNew = parseInt(nPageOld, 10) + 1; // +1 จำนวน
                nPageCurrent = nPageNew;
                break;
            case "previous": //กดปุ่ม Previous
                nPageOld = $(".xWPageADCPdt .active").text(); // Get เลขก่อนหน้า
                nPageNew = parseInt(nPageOld, 10) - 1; // -1 จำนวน
                nPageCurrent = nPageNew;
                break;
            default:
                nPageCurrent = ptPage;
        }
        JSvADCCallPageDataTable(nPageCurrent);
    } else {
        JCNxShowMsgSessionExpired();
    }
}

//Functionality : เปลี่ยนหน้า pagenation product price temp
//Parameters : Event Click Pagenation
//Creator : 03/05/2019 Napat(Jame)
//Return : View
//Return Type : View
function JSvAdPdtPriClickPage(ptPage) {

    var nStaSession = 1;
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {

        var nPageCurrent = '';
        switch (ptPage) {
            case 'next': //กดปุ่ม Next
                $('.xWBtnNext').addClass('disabled');
                nPageOld = $('.xWPagePdtPri .active').text(); // Get เลขก่อนหน้า
                nPageNew = parseInt(nPageOld, 10) + 1; // +1 จำนวน
                nPageCurrent = nPageNew
                break;
            case 'previous': //กดปุ่ม Previous
                nPageOld = $('.xWPagePdtPri .active').text(); // Get เลขก่อนหน้า
                nPageNew = parseInt(nPageOld, 10) - 1; // -1 จำนวน
                nPageCurrent = nPageNew
                break;
            default:
                nPageCurrent = ptPage
        }
        JCNxOpenLoading();
        JSvAdPdtPriDataTable(nPageCurrent);

    } else {

        JCNxShowMsgSessionExpired();

    }

}

//Function Disabled Input On User Approve
function JSxDisableInput() {

    var tUsrApv = $('#oetStaApv').val();
    var tStaDoc = $('#oetStaDoc').val();
    var tStaPrcDoc = $('#oetStaPrcDoc').val();
    if (tUsrApv != "") {
        if (tStaPrcDoc == '') {
            $('#obtSubmit').show();
            $('.xWBtnGrpSaveRight').show();
            $('#obtBtnSpaCancel').show();
        } else {
            $('#obtSubmit').show();
            $('.xWBtnGrpSaveRight').show();
            $('#obtBtnSpaCancel').hide();
        }

        //============= Create by Witsarut 27/08/2019 =============
        $('#obtBtnSpaApv').hide();
        $('#obtBtnPrint').attr('disabled', false);
        $('.otdListItem').hide();
        $(".xCNPIBeHideMQSS").hide();

        //============= Create by Witsarut 27/08/2019 =============

        $('.xWEditInlineElement').attr('disabled', true);
        $('#oetXphDocDate').attr('disabled', true);
        $('#oetXphDocTime').attr('disabled', true);
        $('#obtXphDocDate').attr('disabled', true);
        $('#obtXphDocTime').attr('disabled', true);
        $('#ocmXphDocType').attr('disabled', true);
        $('#ocmXphStaAdj').attr('disabled', true);
        $('#oetValue').attr('disabled', true);
        $('#ocmChangePrice').attr('disabled', true);
        $('#obtAdjAll').attr('disabled', true);
        $('#btnBrowseZone').attr('disabled', true);
        $('#btnBrowseBranch').attr('disabled', true);
        $('#btnBrowseMerChrant').attr('disabled', true);
        $('#btnBrowsePdtPriList').attr('disabled', true);
        $('#btnBrowseMerchant').attr('disabled', true);
        $('#oetXphDStart').attr('disabled', true);
        $('#obtXphDStart').attr('disabled', true);
        $('#oetXphDStop').attr('disabled', true);
        $('#obtXphDStop').attr('disabled', true);

        $('#oetXphTStart').attr('disabled', true);
        $('#obtXphTStart').attr('disabled', true);

        $('#oetXphTStop').attr('disabled', true);
        $('#obtXphTStop').attr('disabled', true);

        $('#oetXphName').attr('disabled', true);
        $('#oetXphRefInt').attr('disabled', true);
        $('#oetXphRefIntDate').attr('disabled', true);
        $('#obtXphRefIntDate').attr('disabled', true);

        $('#btnBrowseAgency').attr('disabled', true);
        $('#ocmXphPriType').attr('disabled', true);
        $('#ocbXphStaDocAct').attr('disabled', true);
        $('#otaXphRmk').removeAttr('disabled', true);

        $('#obtAddPdt').attr('disabled', true);
        $('#obtAddPdt').addClass('xCNBrowsePdtdisabled');
        $('#obtAddPdt').hide();
        $('.ocbListItem').attr('disabled', true);
        $('.ospListItem').addClass('xCNDocDisabled');
        $('.xCNDeleteInLineClick').attr('disabled', true);
        $('.xCNDeleteInLineClick').addClass('xWImgDisable');
        $('.xCNEditInLineClick').attr('disabled', true);
        $('.xCNEditInLineClick').addClass('xWImgDisable');
        $('.xWLabelInLine').addClass('xWImgDisable');
        $('.xWInLine').addClass('xWTdDisable');

        $('#oetSPAInsertScan').attr('disabled', true);
        $('#oetSPAInsertScan').hide();
        $('.xCNImportBtn').attr('disabled', true);
        $('.xCNBTNMngTable').attr('disabled', true);
        $('.xCNImportBtn').hide();
        $('.xCNBTNMngTable').hide();
    }

    if (tStaDoc == '3') {
        //============= Create by Witsarut 27/08/2019 =============
        $('#obtBtnSpaApv').hide();
        $('#obtBtnPrint').attr('disabled', false);
        $('#obtBtnSpaCancel').hide();
        $('#obtSubmit').hide();
        $('.xWBtnGrpSaveRight').hide();
        $('.otdListItem').hide();
        $(".xCNPIBeHideMQSS").hide();

        //============= Create by Witsarut 27/08/2019 =============

        $('.xWEditInlineElement').attr('disabled', true);
        $('#oetXphDocDate').attr('disabled', true);
        $('#oetXphDocTime').attr('disabled', true);
        $('#obtXphDocDate').attr('disabled', true);
        $('#obtXphDocTime').attr('disabled', true);
        $('#ocmXphDocType').attr('disabled', true);
        $('#ocmXphStaAdj').attr('disabled', true);
        $('#oetValue').attr('disabled', true);
        $('#ocmChangePrice').attr('disabled', true);
        $('#obtAdjAll').attr('disabled', true);
        $('#btnBrowseZone').attr('disabled', true);
        $('#btnBrowseBranch').attr('disabled', true);
        $('#btnBrowseMerChrant').attr('disabled', true);
        $('#btnBrowsePdtPriList').attr('disabled', true);
        $('#btnBrowseMerchant').attr('disabled', true);
        $('#oetXphDStart').attr('disabled', true);
        $('#obtXphDStart').attr('disabled', true);
        $('#oetXphDStop').attr('disabled', true);
        $('#obtXphDStop').attr('disabled', true);

        $('#oetXphTStart').attr('disabled', true);
        $('#obtXphTStart').attr('disabled', true);

        $('#oetXphTStop').attr('disabled', true);
        $('#obtXphTStop').attr('disabled', true);

        $('#oetXphName').attr('disabled', true);
        $('#oetXphRefInt').attr('disabled', true);
        $('#oetXphRefIntDate').attr('disabled', true);
        $('#obtXphRefIntDate').attr('disabled', true);

        $('#btnBrowseAgency').attr('disabled', true);
        $('#ocmXphPriType').attr('disabled', true);
        $('#ocbXphStaDocAct').attr('disabled', true);
        $('#otaXphRmk').attr('disabled', true);

        $('#obtAddPdt').attr('disabled', true);
        $('#obtAddPdt').addClass('xCNBrowsePdtdisabled');
        $('#obtAddPdt').hide();
        $('.ocbListItem').attr('disabled', true);
        $('.ospListItem').addClass('xCNDocDisabled');
        $('.xCNDeleteInLineClick').attr('disabled', true);
        $('.xCNDeleteInLineClick').addClass('xWImgDisable');
        $('.xCNEditInLineClick').attr('disabled', true);
        $('.xCNEditInLineClick').addClass('xWImgDisable');
        $('.xWLabelInLine').addClass('xWImgDisable');
        $('.xWInLine').addClass('xWTdDisable');

        $('#oetSPAInsertScan').attr('disabled', true);
        $('#oetSPAInsertScan').hide();
        $('.xCNImportBtn').attr('disabled', true);
        $('.xCNBTNMngTable').attr('disabled', true);
        $('.xCNImportBtn').hide();
        $('.xCNBTNMngTable').hide();
    }
}

//Functionality: Function Chack And Show Button Delete All
//Parameters: LocalStorage Data
//Creator: 18/02/2019 Napat(Jame)
//Return: - 
//Return Type: -
function JSxShowButtonChoose() {

    var nStaSession = 1;
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {

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

    } else {

        JCNxShowMsgSessionExpired();

    }
}

//Functionality : validate Code (validate ขั้นที่ 2 ตรวจสอบรหัสเอกสาร)
//Parameters : -
//Creator : 03/03/2021 Non
//Update : -
//Return : -
//Return Type : -
function JSxValidateADCCodeDublicate() {
    $.ajax({
        type: "POST",
        url: "CheckInputGenCode",
        data: {
            tTableName: "TCNTPdtAdjCostHD",
            tFieldName: "FTXchDocNo",
            tCode: $("#oetADCDocNo").val()
        },
        cache: false,
        timeout: 0,
        success: function(tResult) {
            var aResult = JSON.parse(tResult);
            $("#ohdADCCheckDuplicateCode").val(aResult["rtCode"]);
            if ($("#ohdCheckADCClearValidate").val() != 1) {
                $('#ofmADCFormAdd').validate().destroy();
            }
            $.validator.addMethod('dublicateCode', function(value, element) {
                if ($("#ohdADCRoute").val() == "docADCEventAdd") {
                    if ($('#ocbADCStaAutoGenCode').is(':checked')) {
                        return true;
                    } else {
                        if ($("#ohdADCCheckDuplicateCode").val() == 1) {
                            return false;
                        } else {
                            return true;
                        }
                    }
                } else {
                    return true;
                }
            });
            $('#ofmADCFormAdd').validate({
                focusInvalid: false,
                onclick: false,
                onfocusout: false,
                onkeyup: false,
                rules: {
                    oetADCDocNo: {
                        "dublicateCode": {}
                    }
                },
                messages: {
                    oetADCDocNo: {
                        "dublicateCode": "ไม่สามารถใช้รหัสเอกสารนี้ได้"
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
                invalidHandler: function(event, validator) {
                    if ($("#ohdCheckADCSubmitByButton").val() == 1) {
                        FSvCMNSetMsgWarningDialog("<p>โปรดระบุข้อมูลให้สมบูรณ์</p>");
                    }
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).closest('.form-group').addClass("has-error");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).closest('.form-group').removeClass("has-error");
                },
                submitHandler: function(form) {
                    if ($('#ohdCheckADCSubmitByButton').val() == 1) {
                        JSxADCSubmitEventByButton();
                    }
                }
            });
            if ($("#ohdCheckADCClearValidate").val() != 1) {
                $("#ofmADCFormAdd").submit();
                $("ohdCheckADCClearValidate").val(1);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            JCNxResponseError(jqXHR, textStatus, errorThrown);
        }
    });
}
