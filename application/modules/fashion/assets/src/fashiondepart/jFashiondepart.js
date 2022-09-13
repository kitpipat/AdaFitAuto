var nStaChnBrowseType = $('#oetDepStaBrowse').val();
var tCallChnBackOption = $('#oetDepCallBackOption').val();

/*============================= Begin Auto Run ===============================*/

$('document').ready(function() {
    localStorage.removeItem('LocalItemData');
    JSxCheckPinMenuClose(); /*Check เปิดปิด Menu ตาม Pin*/
    JSxChanelNavDefult();
    if (nStaChnBrowseType != 1) {
        JSvCallPageFashionDepart(1);
    } else {
        JSvCallPageFashionDepartAdd();
    }
    console.log('jSlip run');
});

/*============================= End Auto Run =================================*/

/**
 * Functionality : Function Clear Defult Button Slip Message
 * Parameters : -
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxChanelNavDefult() {
    try {
        if (nStaChnBrowseType != 1 || nStaChnBrowseType == undefined) {
            $('.xCNChnVBrowse').hide();
            $('.xCNChnVMaster').show();
            $('#oliDepTitleAdd').hide();
            $('#oliDepTitleEdit').hide();
            $('#odvBtnAddEdit').hide();
            $('.obtChoose').hide();
            $('#odvBtnDepInfo').show();
        } else {
            $('#odvModalBody .xCNChnVMaster').hide();
            $('#odvModalBody .xCNChnVBrowse').show();
            $('#odvModalBody #odvDepMainMenu').removeClass('main-menu');
            $('#odvModalBody #oliChnNavBrowse').css('padding', '2px');
            $('#odvModalBody #odvChnBtnGroup').css('padding', '0');
            $('#odvModalBody .xCNChnBrowseLine').css('padding', '0px 0px');
            $('#odvModalBody .xCNChnBrowseLine').css('border-bottom', '1px solid #e3e3e3');
        }
    } catch (err) {
        console.log('JSxChanelNavDefult Error: ', err);
    }
}



/**
 * Functionality : Call Slip Message Page list
 * Parameters : {params}
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : view
 * Return Type : view
 */
function JSvCallPageFashionDepart(pnPage) {
    try {
        localStorage.tStaPageNow = 'JSvCallPageChanelList';

        $('#oetSearchAll').val('');
        $.ajax({
            type: "POST",
            url: "fashiondepartList",
            cache: false,
            timeout: 0,
            success: function(tResult) {
                $('#odvContentPageChanel').html(tResult);
                JSvFashionDepartDataTable(pnPage);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxChanelResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    } catch (err) {
        console.log('JSvCallPageFashionDepart Error: ', err);
    }
}

/**
 * Functionality : Call Recive Data List
 * Parameters : Ajax Success Event
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : view
 * Return Type : view
 */
function JSvFashionDepartDataTable(pnPage) {
    try {
        var tSearchAll = $('#oetSearchAll').val();
        var nPageCurrent = pnPage;
        if (nPageCurrent == undefined || nPageCurrent == '') {
            nPageCurrent = '1';
        }
        JCNxOpenLoading();
        $.ajax({
            type: "POST",
            url: "fashiondepartDataTable",
            data: {
                tSearchAll: tSearchAll,
                nPageCurrent: nPageCurrent
            },
            cache: false,
            Timeout: 5000,
            success: function(tResult) {
                if (tResult != "") {
                    $('#ostDataFashionDepart').html(tResult);
                }
                JSxChanelNavDefult();
                JCNxLayoutControll();
                // JStCMMGetPanalLangHTML('TCNMSlipMsgHD_L'); //โหลดภาษาใหม่
                JCNxCloseLoading();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    } catch (err) {
        console.log('JSvFashionDepartDataTable Error: ', err);
    }
}

/**
 * Functionality : Call Slip Message Page Add
 * Parameters : {params}
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : view
 * Return Type : view
 */
function JSvCallPageFashionDepartAdd() {
    try {
        JCNxOpenLoading();
        JStCMMGetPanalLangSystemHTML('', '');
        $.ajax({
            type: "POST",
            url: "fashiondepartPageAdd",
            cache: false,
            timeout: 0,
            success: function(tResult) {
                if (nStaChnBrowseType == 1) {
                    $('.xCNChnVMaster').hide();
                    $('.xCNChnVBrowse').show();
                } else {
                    $('.xCNChnVBrowse').hide();
                    $('.xCNChnVMaster').show();
                    $('#oliDepTitleEdit').hide();
                    $('#oliDepTitleAdd').show();
                    $('#odvBtnDepInfo').hide();
                    $('#odvBtnAddEdit').show();
                }
                $('#odvContentPageChanel').html(tResult);
                JCNxLayoutControll();
                JCNxCloseLoading();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    } catch (err) {
        console.log('JSvCallPageFashionDepartAdd Error: ', err);
    }
}

/**
 * Functionality : Call Slip Message Page Edit
 * Parameters : {params}
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : view
 * Return Type : view
 */
function JSvCallPageFashionDepartEdit(ptDepCode, ptChnBchCode) {
    try {

        JCNxOpenLoading();
        JStCMMGetPanalLangSystemHTML('JSvCallPageFashionDepartEdit', ptDepCode);

        $.ajax({
            type: "POST",
            url: "fashiondepartPageEdit",
            data: { tDepCode: ptDepCode, tChnBchCode: ptChnBchCode },
            cache: false,
            timeout: 0,
            success: function(tResult) {
                if (tResult != '') {
                    $('#oliDepTitleAdd').hide();
                    $('#oliDepTitleEdit').show();
                    $('#odvBtnDepInfo').hide();
                    $('#odvBtnAddEdit').show();
                    $('#odvContentPageChanel').html(tResult);
                    $('#oetDepCode').addClass('xCNDisable');
                    $('.xCNDisable').attr('readonly', true);
                }
                JCNxLayoutControll();
                JCNxCloseLoading();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    } catch (err) {
        console.log('JSvCallPageFashionDepartEdit Error: ', err);
    }
}



//Functionality : Event Add/Edit Chanel
//Parameters : From Submit
//Creator : 29/12/2020 Worakorn
//Return : Status Event Add/Edit  Chanel
//Return Type : object
function JSnAddEditFashionDepart(ptRoute) {

    $('#ofmAddFashionDepart').validate().destroy();
    $('#ofmAddFashionDepart').validate({
        rules: {
            oetDepCode: {
                "required": {
                    depends: function(oElement) {
                        if (ptRoute == "fashiondepartEventAdd") {
                            if ($('#ocbFashionDepartAutoGenCode').is(':checked')) {
                                return false;
                            } else {
                                return true;
                            }
                        } else {
                            return true;
                        }
                    }
                },
                // "dublicateCode": {}
            },
            oetDepName: { "required": {} },
            oetChnAppName: { "required": {} },
            oetWahBchNameCreated: {
                "required": {
                    depends: function(oElement) {
                        if ($('#oetDepUsrLoginLevel') != "HQ" && $('#oetDepUsrLoginLevel') != "AGN") {
                            return false;
                        } else {
                            return true;
                        }
                    }
                },
            }
        },
        messages: {
            oetDepCode: {
                "required": $('#oetDepCode').attr('data-validate-required'),
            },

            oetDepName: {
                "required": $('#oetDepName').attr('data-validate-required'),
            },
            oetChnAppName: {
                "required": $('#oetChnAppName').attr('data-validate-required'),
            },
            oetWahBchNameCreated: {
                "required": $('#oetWahBchNameCreated').attr('data-validate-required'),
            },
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
            $(element).closest('.form-group').addClass("has-error").removeClass("has-success");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).closest('.form-group').addClass("has-success").removeClass("has-error");
        },
        submitHandler: function(form) {
            $.ajax({
                type: "POST",
                url: ptRoute,
                data: $('#ofmAddFashionDepart').serialize(),
                cache: false,
                timeout: 0,
                success: function(tResult) {
                    var aReturn = JSON.parse(tResult);

                    if (aReturn['nStaEvent'] == 1) {
                        if (aReturn['nStaCallBack'] == '1' || aReturn['nStaCallBack'] == null) {
                            JSvCallPageFashionDepartEdit(aReturn['tCodeReturn'], aReturn['tCodeBchReturn']);
                        } else if (aReturn['nStaCallBack'] == '2') {
                            JSvCallPageFashionDepartAdd();
                        } else if (aReturn['nStaCallBack'] == '3') {
                            JSvCallPageFashionDepart();
                        }
                    } else {
                        alert(aReturn['tStaMessg']);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        },
    });
}




/**
 * Functionality : Set data on select multiple, use in table list main page
 * Parameters : -
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxChanelSetDataBeforeDelMulti() { // Action start after delete all button click.
    try {
        var oChecked = $('#odvRGPList td input:checked');
        var tValue = '';
        $(oChecked).each(function(pnIndex) {
            tValue += $(this).parents('tr.otrChanel').find('td.otdChnCode').text() + ', ';
        });
        $('#ospConfirmDelete').text(tValue.replace(/, $/, ""));
    } catch (err) {
        console.log('JSxChanelSetDataBeforeDelMulti Error: ', err);
    }
}

function JSaFashionDepartDelete(pnPage, tCode, ptName) {

    var aData = $('#ohdConfirmIDDelete').val();
    var aTexts = aData.substring(0, aData.length - 2);
    var aDataSplit = aTexts.split(" , ");
    var aDataSplitlength = aDataSplit.length;
    var aNewIdDelete = [];
    if (aDataSplitlength == '1') {
        $('#odvModalDelFashionDepart').modal('show');
        $('#ospConfirmDelete').html($('#oetTextComfirmDeleteSingle').val() + ' ' + tCode + ' ( ' + ptName + ' ) ');
        $('#osmConfirm').on('click', function(evt) {
            $.ajax({
                type: "POST",
                url: "fashiondepartDelete",
                data: { 'tDepCode': tCode },
                cache: false,
                timeout: 0,
                success: function(tResult) {
                    // tResult = tResult.trim();
                    var aReturn = JSON.parse(tResult);
                    if (aReturn['nStaEvent'] == 1) {
                        $('#odvModalDelFashionDepart').modal('hide');
                        $('#ospConfirmDelete').text($('#oetTextComfirmDeleteSingle').val());
                        $('#ohdConfirmIDDelete').val('');
                        localStorage.removeItem('LocalItemData');
                        $('.modal-backdrop').remove();
                        setTimeout(function() {
                            if (aReturn["nNumRowChnLoc"] != 0) {
                                if (aReturn["nNumRowChnLoc"] > 10) {
                                    nNumPage = Math.ceil(aReturn["nNumRowChnLoc"] / 10);
                                    if (pnPage <= nNumPage) {
                                        JSvCallPageFashionDepart(pnPage);
                                    } else {
                                        JSvCallPageFashionDepart(nNumPage);
                                    }
                                } else {
                                    JSvCallPageFashionDepart(1);
                                }
                            } else {
                                JSvCallPageFashionDepart(1);
                            }
                        }, 500);
                    } else {
                        JCNxOpenLoading();
                        alert(aReturn['tStaMessg']);
                    }
                    JSxChanelNavDefult();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        });
    }
}

//Functionality : (event) Delete All
//Parameters :
//Creator : 29/12/2020 Worakorn
//Return : 
//Return Type :
function JSnChanelDelChoose(pnPage) {

    var tNamepage = '';
    var aDataIdBranch = '';
    var nStaBrowse = '';
    var tStaInto = '';
    var aData = $('#ohdConfirmIDDelete').val();

    var aTexts = aData.substring(0, aData.length - 2);
    var aDataSplit = aTexts.split(" , ");
    var aDataSplitlength = aDataSplit.length;
    var aNewIdDelete = [];

    for ($i = 0; $i < aDataSplitlength; $i++) {
        aNewIdDelete.push(aDataSplit[$i]);
    }

    console.log(aNewIdDelete);

    if (aDataSplitlength > 1) {
        localStorage.StaDeleteArray = '1';
        $.ajax({
            type: "POST",
            url: "fashiondepartDeleteMulti",
            data: { 'tIDCode': aNewIdDelete },
            success: function(tResult) {
                var aReturn = JSON.parse(tResult);
                if (aReturn['nStaEvent'] == 1) {
                    setTimeout(function() {
                        $('#odvModalDelFashionDepart').modal('hide');
                        JSvFashionDepartDataTable(pnPage);
                        $('#ospConfirmDelete').text($('#oetTextComfirmDeleteSingle').val());
                        $('#ohdConfirmIDDelete').val('');
                        localStorage.removeItem('LocalItemData');
                        // $('.obtChoose').hide();
                        $('.modal-backdrop').remove();

                        if (aReturn["nNumRowChnLoc"] != 0) {
                            if (aReturn["nNumRowChnLoc"] > 10) {
                                nNumPage = Math.ceil(aReturn["nNumRowChnLoc"] / 10);
                                if (pnPage <= nNumPage) {
                                    JSvCallPageFashionDepart(pnPage);
                                } else {
                                    JSvCallPageFashionDepart(nNumPage);
                                }
                            } else {
                                JSvCallPageFashionDepart(1);
                            }
                        } else {
                            JSvCallPageFashionDepart(1);
                        }
                    }, 500);
                } else {
                    JCNxOpenLoading();
                    alert(aReturn['tStaMessg']);
                }
                JSxChanelNavDefult();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    } else {
        localStorage.StaDeleteArray = '0';

        return false;
    }
}

/**
 * Functionality : Pagenation changed
 * Parameters : -
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : view
 * Return Type : view
 */
function JSvFashionDepartClickPage(ptPage) {
    try {
        var nPageCurrent = '';
        var nPageNew;
        switch (ptPage) {
            case 'next': //กดปุ่ม Next
                $('.xWBtnNext').addClass('disabled');
                nPageOld = $('.xWPageGrp .active').text(); // Get เลขก่อนหน้า
                nPageNew = parseInt(nPageOld, 10) + 1; // +1 จำนวน
                nPageCurrent = nPageNew;
                break;
            case 'previous': //กดปุ่ม Previous
                nPageOld = $('.xWPageGrp .active').text(); // Get เลขก่อนหน้า
                nPageNew = parseInt(nPageOld, 10) - 1; // -1 จำนวน
                nPageCurrent = nPageNew;
                break;
            default:
                nPageCurrent = ptPage;
        }
        JSvFashionDepartDataTable(nPageCurrent);
    } catch (err) {
        console.log('JSvFashionDepartClickPage Error: ', err);
    }
}


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

//Functionality: Insert Text In Modal Delete
//Parameters: LocalStorage Data
//Creator: 29/12/2020 Worakorn
//Return: -
//Return Type: -
function JSxPaseCodeDelInModal() {

    var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
    if (aArrayConvert[0] == null || aArrayConvert[0] == '') {} else {
        var tTextCode = '';
        for ($i = 0; $i < aArrayConvert[0].length; $i++) {
            tTextCode += aArrayConvert[0][$i].nCode;
            tTextCode += ' , ';
        }
        $('#ospConfirmDelete').text($('#oetTextComfirmDeleteMulti').val());
        $('#ohdConfirmIDDelete').val(tTextCode);
    }
}


/**
 * Functionality : Delete head recive or end recive row
 * Parameters : poElement is Itself element, poEvent is Itself event
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxFashionDepartDeleteRowHead(poElement = null, poEvent = null) {
    try {
        if ((JCNnChanelCountRow('head') == 1)) { return; }
        if (confirm('Delete ?')) {
            $(poElement).parents('.xWChnItemSelect').remove();
        }
    } catch (err) {
        console.log('JSxChanelDeleteRow Error: ', err);
    }
}

/**
 * Functionality : Delete head recive or end recive row
 * Parameters : poElement is Itself element, poEvent is Itself event
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxFashionDepartDeleteRowEnd(poElement = null, poEvent = null) {
    try {
        if ((JCNnChanelCountRow('end') == 1)) { return; }
        if (confirm('Delete ?')) {
            $(poElement).parents('.xWChnItemSelect').remove();
        }
    } catch (err) {
        console.log('JSxChanelDeleteRow Error: ', err);
    }
}

/**
 * Functionality : Add head of receipt row
 * Parameters : -
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxChanelAddHeadReceiptRow() {
    try {
        if (JCNnChanelCountRow('head') >= 10) { return; }

        let nIndex = JCNnChanelGetMaxID('head');
        console.log('MaxID: ', JCNnChanelGetMaxID('head'));

        // Get template in wChanelAdd.php
        var template = $.validator.format($.trim($('#oscSlipHeadRowTemplate').html()));
        // Add template
        $(template(++nIndex)).appendTo("#odvChnSlipHeadContainer");
    } catch (err) {
        console.log('JSxChanelAddHeadReceiptRow Error: ', err);
    }
}

/**
 * Functionality : Add end of receipt row
 * Parameters : -
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxChanelAddEndReceiptRow() {
    try {
        if (JCNnChanelCountRow('end') >= 10) { return; }

        let nIndex = JCNnChanelGetMaxID('end');

        // Get template in wChanelAdd.php
        var template = $.validator.format($.trim($('#oscSlipEndRowTemplate').html()));
        // Add template
        $(template(++nIndex)).appendTo("#odvChnSlipEndContainer");
    } catch (err) {
        console.log('JSxChanelAddEndReceiptRow Error: ', err);
    }
}

/**
 * Functionality : {description}
 * Parameters : ptReceiptType is type for Head of receipt("head") End of receipt("end"),
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : Max id number
 * Return Type : number
 */
function JCNnChanelGetMaxID(ptReceiptType) {
    try {
        if (JCNnChanelCountRow(ptReceiptType) <= 0) { return 0; }

        if (ptReceiptType == 'head') {
            let nMaxID = 0;
            let oHeadItems = $('#odvChnSlipHeadContainer .xWChnItemSelect');
            oHeadItems.each((pnIndex, poElement) => {
                let tElementID = $(poElement).attr('id');
                if (nMaxID < tElementID) {
                    nMaxID = tElementID;
                }
            });
            return nMaxID;
        }
        if (ptReceiptType == 'end') {
            let nMaxID = 0;
            let oHeadItems = $('#odvChnSlipEndContainer .xWChnItemSelect');
            oHeadItems.each((pnIndex, poElement) => {
                let tElementID = $(poElement).attr('id');
                if (nMaxID < tElementID) {
                    nMaxID = tElementID;
                }
            });
            return nMaxID;
        }
    } catch (err) {
        console.log('JCNnChanelGetMaxID Error: ', err);
    }
}

/**
 * Functionality : Count row in head of receipt or end of receipt
 * Parameters : ptReceiptPosition is position for limit(head or end)
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : Row count
 * Return Type : number
 */
function JCNnChanelCountRow(ptReceiptPosition) {
    try {
        if (ptReceiptPosition == 'head') {
            let nHeadRow = $('#odvChnSlipHeadContainer .xWChnItemSelect').length;
            return nHeadRow;
        }
        if (ptReceiptPosition == 'end') {
            let nEndRow = $('#odvChnSlipEndContainer .xWChnItemSelect').length;
            return nEndRow;
        }
    } catch (err) {
        console.log('JCNnChanelCountRow Error: ', err);
    }
}

/**
 * Functionality : Display head of receipt and end of receipt row
 * Parameters : ptReceiptType is type for Head of receipt("head") End of receipt("end"), 
 * pnRows is number for row item
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxChanelRowDefualt(ptReceiptType, pnRows) {
    try {
        // Validate pnRows
        if (pnRows <= 0) { return; } // Invalid exit function

        // Setting Type
        let tReceiptType;
        if (ptReceiptType == "end") {
            tReceiptType = "End";
        }
        if (ptReceiptType == "head") {
            tReceiptType = "Head";
        }

        // Get template in wChanelAdd.php
        var template = $.validator.format($.trim($("#oscSlip" + tReceiptType + "RowTemplate").html()));

        // Add template by pnRows
        for (let loop = 1; loop <= pnRows; loop++) {
            $(template(loop)).appendTo("#odvChnSlip" + tReceiptType + "Container");
        }
    } catch (err) {
        console.log('JSxChanelRowDefualt Error: ', err);
    }
}

/**
 * Functionality : Set data sort from sortable plugin
 * Parameters : ptReceiptType is type for sort, paSortData is row sort
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxChanelSetRowSortData(ptReceiptType, paSortData) {
    try {
        if (ptReceiptType == 'head') {
            localStorage.removeItem('headReceiptSort');
            localStorage.setItem('headReceiptSort', JSON.stringify(paSortData));
        }
        if (ptReceiptType == 'end') {
            localStorage.removeItem('endReceiptSort');
            localStorage.setItem('endReceiptSort', JSON.stringify(paSortData));
        }
    } catch (err) {
        console.log('JSxChanelSetRowSortData Error: ', err);
    }
}

/**
 * Functionality : Remove data sort from sortable plugin
 * Parameters : ptReceiptType is type for remove sort data(head, end, all)
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : -
 * Return Type : -
 */
function JSxChanelRemoveSortData(ptReceiptType) {
    try {
        if (ptReceiptType == 'head') {
            localStorage.removeItem('headReceiptSort');
        }
        if (ptReceiptType == 'end') {
            localStorage.removeItem('endReceiptSort');
        }
        if (ptReceiptType == 'all') {
            localStorage.removeItem('headReceiptSort');
            localStorage.removeItem('endReceiptSort');
        }
    } catch (err) {
        console.log('JSxChanelRemoveSortData Error: ', err);
    }
}

/**
 * Functionality : Get data sort from sortable plugin
 * Parameters : ptReceiptType is type for get sort data(head, end)
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : Sort data
 * Return Type : array
 */
function JSaChanelGetSortData(ptReceiptType) {
    try {
        if (ptReceiptType == 'head') {
            if (!(localStorage.getItem('headReceiptSort') == null)) {
                return JSON.parse(localStorage.getItem('headReceiptSort'));
            }
        }
        if (ptReceiptType == 'end') {
            if (!(localStorage.getItem('endReceiptSort') == null)) {
                return JSON.parse(localStorage.getItem('endReceiptSort'));
            }
        }
    } catch (err) {
        console.log('JSaChanelGetSortData Error: ', err);
    }
}

/**
 * Functionality : Prepare sort number after move row
 * Parameters : ptReceiptType is type for sorting(head, end), 
 * pbUseStringFormat is use string format? (set true return string format, set false return object format)
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : Head of receipt or End of receipt value
 * Return Type : object
 */
function JSoChanelSortabled(ptReceiptType, pbUseStringFormat) {
    try {
        if (ptReceiptType == 'head') {
            let aSortData = JSaChanelGetSortData('head');
            let aSortabled = {};
            $.each(aSortData, (pnIndex, pnValue) => {
                let tValue = $('#odvChnSlipHeadContainer .xWChnItemSelect[id=' + pnValue + ']').find('.xWChnDyForm').val();
                aSortabled[pnIndex] = tValue;
            });
            // console.log('Sortabled: ', aSortabled);
            if (pbUseStringFormat) {
                return JSON.stringify(aSortabled);
            } else {
                return aSortabled;
            }
        }
        if (ptReceiptType == 'end') {
            let aSortData = JSaChanelGetSortData('end');
            let aSortabled = {};
            $.each(aSortData, (pnIndex, pnValue) => {
                let tValue = $('#odvChnSlipEndContainer .xWChnItemSelect[id=' + pnValue + ']').find('.xWChnDyForm').val();
                aSortabled[pnIndex] = tValue;
            });
            // console.log('Sortabled: ', aSortabled);
            if (pbUseStringFormat) {
                return JSON.stringify(aSortabled);
            } else {
                return aSortabled;
            }
        }
    } catch (err) {
        console.log('JSoChanelSortabled Error: ', err);
    }
}

/**
 * Functionality : Is create page.
 * Parameters : -
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : Status true is create page
 * Return Type : Boolean
 */
function JCNbChanelIsCreatePage() {
    try {
        const tChnCode = $('#oetDepCode').data('is-created');
        var bStatus = false;
        if (tChnCode == "") { // No have data
            bStatus = true;
        }
        return bStatus;
    } catch (err) {
        console.log('JCNbChanelIsCreatePage Error: ', err);
    }
}

/**
 * Functionality : Is update page.
 * Parameters : -
 * Creator : 29/12/2020 Worakorn
 * Last Modified : -
 * Return : Status true is update page
 * Return Type : Boolean
 */
function JCNbChanelIsUpdatePage() {
    try {
        const tVCode = $('#oetDepCode').data('is-created');
        let bStatus = false;
        if (tVCode != "") { // Have data
            bStatus = true;
        }
        return bStatus;
    } catch (err) {
        console.log('JCNbChanelIsUpdatePage Error: ', err);
    }
}

//Functionality: Function Chack Value LocalStorage
//Parameters: Event Select List Branch
//Creator: 29/12/2020 Worakorn
//Return: Duplicate/none
//Return Type: string
function findObjectByKey(array, key, value) {
    for (var i = 0; i < array.length; i++) {
        if (array[i][key] === value) {
            return 'Dupilcate';
        }
    }
    return 'None';
}

// Functionality: Function Check Is Create Page
// Parameters: Event Documet Redy
// Creator: 29/12/2020 Worakorn)
// Return: object Status Delete
// ReturnType: boolean
function JSbChnIsCreatePage() {
    try {
        const tChnCode = $('#oetDepCode').data('is-created');
        var bStatus = false;
        if (tChnCode == "") { // No have data
            bStatus = true;
        }
        return bStatus;
    } catch (err) {
        console.log('JSbChnIsCreatePage Error: ', err);
    }
}

// Functionality: Function Check Is Update Page
// Parameters: Event Documet Redy
// Creator: 29/12/2020 Worakorn
// Return: object Status Delete
// ReturnType: boolean
function JSbChnIsUpdatePage() {
    try {
        const tChnCode = $('#oetDepCode').data('is-created');
        var bStatus = false;
        if (!tChnCode == "") { // Have data
            bStatus = true;
        }
        return bStatus;
    } catch (err) {
        console.log('JSbChnIsUpdatePage Error: ', err);
    }
}

// Functionality : Show or Hide Component
// Parameters : ptComponent is element on document(id or class or...),pbVisible is visible
// Creator : 29/12/2020 Worakorn
// Return : -
// Return Type : -
function JSxChnVisibleComponent(ptComponent, pbVisible, ptEffect) {
    try {
        if (pbVisible == false) {
            $(ptComponent).addClass('hidden');
        }
        if (pbVisible == true) {
            // $(ptComponent).removeClass('hidden');
            $(ptComponent).removeClass('hidden fadeIn animated').addClass('fadeIn animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
                $(this).removeClass('hidden fadeIn animated');
            });
        }
    } catch (err) {
        console.log('JSxChnVisibleComponent Error: ', err);
    }
}

// Functionality: Function Check Is Create Page
// Parameters: Event Documet Redy
// Creator: 29/12/2020 Worakorn
// Return: object Status Delete
// ReturnType: boolean
function JSbChnIsCreatePage() {
    try {
        const tChnCode = $('#oetDepCode').data('is-created');
        var bStatus = false;
        if (tChnCode == "") { // No have data
            bStatus = true;
        }
        return bStatus;
    } catch (err) {
        console.log('JSbChnIsCreatePage Error: ', err);
    }
}

// Functionality: Function Check Is Update Page
// Parameters: Event Documet Redy
// Creator: 29/12/2020 Worakorn
// Return: object Status Delete
// ReturnType: boolean
function JSbChnIsUpdatePage() {
    try {
        const tChnCode = $('#oetDepCode').data('is-created');
        var bStatus = false;
        if (!tChnCode == "") { // Have data
            bStatus = true;
        }
        return bStatus;
    } catch (err) {
        console.log('JSbChnIsUpdatePage Error: ', err);
    }
}

// Functionality : Show or Hide Component
// Parameters : ptComponent is element on document(id or class or...),pbVisible is visible
// Creator : 29/12/2020 Worakorn
// Return : -
// Return Type : -
function JSxChnVisibleComponent(ptComponent, pbVisible, ptEffect) {
    try {
        if (pbVisible == false) {
            $(ptComponent).addClass('hidden');
        }
        if (pbVisible == true) {
            // $(ptComponent).removeClass('hidden');
            $(ptComponent).removeClass('hidden fadeIn animated').addClass('fadeIn animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
                $(this).removeClass('hidden fadeIn animated');
            });
        }
    } catch (err) {
        console.log('JSxChnVisibleComponent Error: ', err);
    }
}




function JSxChnDeleteMutirecord(pnPage) {
    // var tRevCodeWhere = $('#oetRcvCode').val();
    // var tRcvSpcStaAlwCfg = $('#ohdtRcvSpcStaAlwCfg').val();
    let nStaSession = JCNxFuncChkSessionExpired();
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
        // JCNxOpenLoading();
        let aDataDepCode = [];
        let ocbListItem = $(".ocbListItem");
        for (var nI = 0; nI < ocbListItem.length; nI++) {
            if ($($(".ocbListItem").eq(nI)).prop('checked')) {
                aDataDepCode.push($($(".ocbListItem").eq(nI)).parents('.xWChnItems').data('depcode'));

            }
        }
        let aDataWhere = {
            'paDepCode': aDataDepCode,
        };
        $.ajax({
            type: "POST",
            url: "fashiondepartDeleteMulti",
            data: {
                'paDataWhere': aDataWhere,
            },
            cache: false,
            timeout: 0,
            success: function(tResult) {
                tResult = tResult.trim();
                var aReturn = $.parseJSON(tResult);
                if (aReturn['nStaEvent'] == '1') {
                    $('#odvModalDeleteMutirecord').modal('hide');
                    $('#ospConfirmDelete').empty();
                    localStorage.removeItem('LocalItemData');
                    setTimeout(function() {
                        JSvCallPageFashionDepart(pnPage);
                    }, 500);
                } else {
                    alert(aReturn['tStaMessg']);
                }
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