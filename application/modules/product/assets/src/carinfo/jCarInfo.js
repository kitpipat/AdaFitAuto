var nStaCAIBrowseType = $('#oetCAIStaBrowse').val();
var tCallCAIBackOption = $('#oetCAICallBackOption').val();
var nCarType            = $('#oetCAICarType').val();
$('ducument').ready(function() {
    localStorage.removeItem('LocalItemData');
    JSxCheckPinMenuClose(); /*Check เปิดปิด Menu ตาม Pin*/
    JSxQNavDefult();
    if (nStaCAIBrowseType != 1) {
        JSvCallPageCarInfoList();
    } else {
        JSvCallPageCarInfoAdd();
    }
});


///function : Function Clear Defult Button QasSubGroup
//Parameters : -
//Creator : 25/05/2021 Off
//Update:   
//Return : -
//Return Type : -
function JSxQNavDefult() {
    // Menu Bar เข้ามาจาก หน้า Master หรือ Browse
    if (nStaCAIBrowseType != 1) { // เข้ามาจาก  Master
        $('.obtChoose').hide();
        $('#oliCAITitleAdd').hide();
        $('#oliCAITitleEdit').hide();
        $('#odvBtnCAIAddEdit').hide();
        $('#odvBtnCAIInfo').show();
    } else { // เข้ามาจาก Browse Modal
        $('#odvModalBody #odvCAIMainMenu').removeClass('main-menu');
        $('#odvModalBody .xCNCAIBrowseLine').css('padding', '0px 0px');
        $('#odvModalBody .xCNCAIBrowseLine').css('border-bottom', '1px solid #e3e3e3');
    }
}

///function : Call CarInfo Page list  
//Parameters : - 
//Creator : 02/06/2021 Off
//Update:   
//Return : View
//Return Type : View
function JSvCallPageCarInfoList(pnPage) {
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
        localStorage.tStaPageNow = 'JSvCallPageCarInfoList';
        $('#oetSearchAll').val('');
        $.ajax({
            type: "POST",
            url: "carinfoList",
            data : { 'nCarType' : nCarType },
            cache: false,
            timeout: 0,
            success: function(tResult) {
                $('#odvContentPageCarInfo').html(tResult);
                JSvCAICarInfoDataTable(pnPage);
            },
            error: function(data) {
                console.log(data);
            }
        });
    } else {
        JCNxShowMsgSessionExpired();
    }
}

///function : Call CarInfo Data List
//Parameters : Ajax Success Event 
//Creator : 02/06/2021 Off
//Update:   
//Return : View
//Return Type : View
function JSvCAICarInfoDataTable(pnPage) {
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
        var tSearchAll   = $('#oetSearchAll').val();
        var nPageCurrent = pnPage;
        if (nPageCurrent == undefined || nPageCurrent == '') {
            nPageCurrent = '1';
        }
        JCNxOpenLoading();
        $.ajax({
            type: "POST",
            url: "carinfoDataTable",
            data: {
                tSearchAll: tSearchAll,
                nPageCurrent: nPageCurrent,
                nCarType: nCarType
            },
            cache: false,
            Timeout: 0,
            success: function(tResult) {
                if (tResult != "") {
                    $('#ostDataCarinfo').html(tResult);
                    $(".xCNDocDisabled").removeAttr("onclick");
                }
                JSxQNavDefult();
                JCNxLayoutControll();
                JCNxCloseLoading();
            },
            error: function(data) {
                console.log(data);
            }
        });
    } else {
        JCNxShowMsgSessionExpired();
    }
}

//Functionality : Call CarInfo Page Add  
//Parameters : -
//Creator : 02/06/2021 Off
//Update: 
//Return : View
//Return Type : View
function JSvCallPageCarInfoAdd() {
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
        JCNxOpenLoading();
        JStCMMGetPanalLangSystemHTML('', '');
        $.ajax({
            type: "POST",
            url: "carinfoPageAdd",
            data: {
                nCarType: nCarType
            },
            cache: false,
            timeout: 0,
            success: function(tResult) {
                if (nStaCAIBrowseType == 1) {
                    $('#odvModalBodyBrowse').html(tResult);
                    $('#odvModalBodyBrowse .panel-body').css('padding-top', '0');
                } else {
                    $('#oliCAITitleEdit').hide();
                    $('#oliCAITitleAdd').show();
                    $('#odvBtnCAIInfo').hide();
                    $('#odvBtnCAIAddEdit').show();
                }
                $('#odvContentPageCarInfo').html(tResult);
                JCNxLayoutControll();
                JCNxCloseLoading();
            },
            error: function(data) {
                console.log(data);
            }
        });
    } else {
        JCNxShowMsgSessionExpired();
    }
}

//Functionality : Call QasSubGroup Page Edit  
//Parameters : -
//Creator : 25/05/2021 Off
//Update: 
//Return : View
//Return Type : View
function JSvCallPageCarInfoEdit(ptCaiCode) {
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
        JCNxOpenLoading();
        JStCMMGetPanalLangSystemHTML('JSvCallPageCarInfoEdit', ptCaiCode);
        $.ajax({
            type: "POST",
            url: "carinfoPageEdit",
            data: { tCAICode: ptCaiCode,
                nCarType: nCarType },
            cache: false,
            timeout: 0,
            success: function(tResult) {
                if (tResult != "") {
                    $('#oliCAITitleAdd').hide();
                    $('#oliCAITitleEdit').show();
                    $('#odvBtnCAIInfo').hide();
                    $('#odvBtnCAIAddEdit').show();
                    $('#odvContentPageCarInfo').html(tResult);
                    $('#oetCAICode').addClass('xCNDisable');
                    $('.xCNDisable').attr('readonly', true);
                    $('.xCNiConGen').attr('disabled', true);
                }
                JCNxLayoutControll();
                JCNxCloseLoading();
            },
            error: function(data) {
                console.log(data);
            }
        });
    } else {
        JCNxShowMsgSessionExpired();
    }
}

//Functionality : (event) Add/Edit QasSubGroup
//Parameters : form
//Creator : 25/05/2021 Off
//Return : object Status Event And Event Call Back
//Return Type : object
function JSnAddEditCarInfo(ptRoute) {
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
        $('#ofmAddCarInfo').validate().destroy();
        
        $.validator.addMethod('dublicateCode', function(value, element) {
            if (ptRoute == "qassubgroupEventAdd") {
                if ($("#ohdCheckDuplicateObjCode").val() == 1) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }, '');


        $('#ofmAddCarInfo').validate({
            rules: {
                oetObjCode: {
                    "required": {
                        depends: function(oElement) {
                            if (ptRoute == "qasgroupEventAdd") {
                                if ($('#ocbQasGroupAutoGenCode').is(':checked')) {
                                    return false;
                                } else {
                                    return true;
                                }
                            } else {
                                return true;
                            }
                        }
                    },
                    "dublicateCode": {}
                },
                oetCAIName: { "required": {} },
                oetCaiBrandName: { "required": {} },
                ocmObjGroup: { "required": {} },
            },
            messages: {
                oetObjCode: {
                    "required": $('#oetObjCode').attr('data-validate-required'),
                    "dublicateCode": $('#oetObjCode').attr('data-validate-dublicateCode')
                },
                oetObjName: {
                    "required": $('#oetObjName').attr('data-validate-required'),
                },
                oetCAIName: {
                    "required": $('#oetCAIName').attr('data-validate-required'),
                },
                oetCaiBrandName: {
                    "required": $('#oetCaiBrandName').attr('data-validate-required'),
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
                $(element).closest('.form-group').addClass("has-error").removeClass("has-success");
            },
            unhighlight: function(element, errorClass, validClass) {
                var nStaCheckValid = $(element).parents('.form-group').find('.help-block').length
                if (nStaCheckValid != 0) {
                    $(element).closest('.form-group').addClass("has-success").removeClass("has-error");
                }
            },
            submitHandler: function(form) {
                JCNxOpenLoading();
                $.ajax({
                    type: "POST",
                    url: ptRoute,
                    data: $('#ofmAddCarInfo').serialize(),
                    async: false,
                    cache: false,
                    timeout: 0,
                    success: function(tResult) {
                        if (nStaCAIBrowseType != 1) {
                            var aReturn = JSON.parse(tResult);
                            if (aReturn['nStaEvent'] == 1) {
                                if (aReturn['nStaCallBack'] == '1' || aReturn['nStaCallBack'] == null) {
                                    JSvCallPageCarInfoEdit(aReturn['tCodeReturn']);
                                } else if (aReturn['nStaCallBack'] == '2') {
                                    JSvCallPageCarInfoAdd();
                                } else if (aReturn['nStaCallBack'] == '3') {
                                    JSvCallPageCarInfoList();
                                }
                            } else {
                                alert(aReturn['tStaMessg']);
                            }
                        } else {
                            JCNxCloseLoading();
                            JCNxBrowseData(tCallCAIBackOption);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        JCNxResponseError(jqXHR, textStatus, errorThrown);
                    }
                });
            }
        });
    } else {
        JCNxShowMsgSessionExpired();
    }
}

//Functionality: เปลี่ยนหน้า pagenation
//Parameters: -
//Creator : 25/05/2021 Off
//Update: 
//Return: View
//Return Type: View
function JSvClickPage(ptPage) {
    var nPageCurrent = '';
    var nPageNew;
    switch (ptPage) {
        case 'next': //กดปุ่ม Next
            $('.xWBtnNext').addClass('disabled');
            nPageOld = $('.xWPageCarInfo .active').text(); // Get เลขก่อนหน้า
            nPageNew = parseInt(nPageOld, 10) + 1; // +1 จำนวน
            nPageCurrent = nPageNew;
            break;
        case 'previous': //กดปุ่ม Previous
            nPageOld = $('.xWPageCarInfo .active').text(); // Get เลขก่อนหน้า
            nPageNew = parseInt(nPageOld, 10) - 1; // -1 จำนวน
            nPageCurrent = nPageNew;
            break;
        default:
            nPageCurrent = ptPage;
    }
    JSvCAICarInfoDataTable(nPageCurrent);
}

//Functionality: (event) Delete
//Parameters: Button Event [tIDCode รหัสเหตุผล]
//Creator : 25/05/2021 Off
//Update: 
//Return: Event Delete QasSubGroup List
//Return Type: -

function JSnCarInfoDel(pnCurrentPage, ptDelName, ptIDCode, ptYesOnNo) {
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
        var aData = $('#ohdConfirmIDDelete').val();
        var aTexts = aData.substring(0, aData.length - 2);
        var aDataSplit = aTexts.split(" , ");
        var aDataSplitlength = aDataSplit.length;
        var aNewIdDelete = [];
        if (aDataSplitlength == '1') {
            $('#odvModalDelCarInfo').modal('show');
            // $('#ospConfirmDelete').html($('#oetTextComfirmDeleteSingle').val() + tIDCode);
            $('#ospConfirmDelete').html($('#oetTextComfirmDeleteSingle').val() + ptIDCode + ' ( ' + ptDelName + ' ) ' + ptYesOnNo);
            $('#osmConfirm').off('click').on('click', function(evt) {
                if (localStorage.StaDeleteArray != '1') {
                    $.ajax({
                        type: "POST",
                        url: "carinfoEventDelete",
                        data: { 'tIDCode': ptIDCode,
                        'nCarType': nCarType },
                        cache: false,
                        success: function(tResult) {
                            var aReturn = JSON.parse(tResult);
                            if (aReturn['nStaEvent'] == 1) {
                                $('#odvModalDelCarInfo').modal('hide');
                                $('#ospConfirmDelete').text($('#oetTextComfirmDeleteSingle').val());
                                $('#ohdConfirmIDDelete').val('');
                                localStorage.removeItem('LocalItemData');
                                $('.modal-backdrop').remove();
                                setTimeout(function() {
                                    if (aReturn["nNumRowCldLoc"] != 0) {
                                        if (aReturn["nNumRowCldLoc"] > 10) {
                                            nNumPage = Math.ceil(aReturn["nNumRowCldLoc"] / 10);
                                            if (pnCurrentPage <= nNumPage) {
                                                JSvCallPageCarInfoList(pnCurrentPage);
                                            } else {
                                                JSvCallPageCarInfoList(nNumPage);
                                            }
                                        } else {
                                            JSvCallPageCarInfoList(1);
                                        }
                                    } else {
                                        JSvCallPageCarInfoList(1);
                                    }
                                    // JSvBntDataTable(pnPage);
                                    // JSvCAICarInfoDataTable(tCurrentPage);
                                }, 500);
                            } else {
                                FSvCMNSetMsgWarningDialog(aReturn['tStaMessg']);
                                //alert(aReturn['tStaMessg']);
                            }
                            JSxQNavDefult();
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            });
        }
    } else {
        JCNxShowMsgSessionExpired();
    }
}

//Functionality : (event) Delete All
//Parameters : Button Event 
//Creator : 25/05/2021 Off
//Update: 
//Return : Event Delete All Select List
//Return Type : -
function JSnCarInfoDelChoose(ptCurrentPage) {
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
        JCNxOpenLoading();
        var aData = $('#ohdConfirmIDDelete').val();
        var aTexts = aData.substring(0, aData.length - 2);
        var aDataSplit = aTexts.split(" , ");
        var aDataSplitlength = aDataSplit.length;
        var aNewIdDelete = [];

        for ($i = 0; $i < aDataSplitlength; $i++) {
            aNewIdDelete.push(aDataSplit[$i]);
        }
        if (aDataSplitlength > 1) {
            localStorage.StaDeleteArray = '1';
            $.ajax({
                type: "POST",
                url: "carinfoEventDelete",
                data: { 'tIDCode': aNewIdDelete },
                success: function(tResult) {
                    var aReturn = JSON.parse(tResult);
                    if (aReturn['nStaEvent'] == '1') {
                        setTimeout(function() {
                            $('#odvModalDelCarInfo').modal('hide');
                            JSvCAICarInfoDataTable(ptCurrentPage);
                            $('#ospConfirmDelete').empty();
                            $('#ohdConfirmIDDelete').val();
                            localStorage.removeItem('LocalItemData');
                            $('.modal-backdrop').remove();
                            // JSvBntDataTable(pnPage);
                            // JSvCAICarInfoDataTable(tCurrentPage);
                        }, 1000);
                    } else {
                        alert(aReturn['tStaMessg']);
                    }
                    JSxQNavDefult();
                },
                error: function(data) {
                    console.log(data);
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

//Functionality: Function Show Button Delete All
//Parameters:   Event Parameter
//Creator : 25/05/2021 Off
//Return: Event Button Delete All
//Return Type: -
function JSxShowButtonChoose() {
    var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
    if (aArrayConvert[0] == null || aArrayConvert[0] == '') {
        $('.obtChoose').hide();
    } else {
        nNumOfArr = aArrayConvert[0].length;
        if (nNumOfArr > 1) {
            $('.obtChoose').fadeIn(300);
        } else {
            $('.obtChoose').fadeOut(300);
        }
    }
}

//Functionality: Function Insert Text Delete
//Parameters: Event Parameter
//Creator : 25/05/2021 Off
//Return: Event Insert Text
//Return Type: -
function JSxTextinModal() {
    var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];

    if (aArrayConvert[0] == null || aArrayConvert[0] == '') {} else {
        var tText = '';
        var tTextCode = '';
        for ($i = 0; $i < aArrayConvert[0].length; $i++) {
            tText += aArrayConvert[0][$i].tName + '(' + aArrayConvert[0][$i].nCode + ') ';
            tText += ' , ';

            tTextCode += aArrayConvert[0][$i].nCode;
            tTextCode += ' , ';
        }
        var tTexts = tText.substring(0, tText.length - 2);
        $('#ospConfirmDelete').text($('#oetTextComfirmDeleteSingle').val() + tTexts);
        $('#ohdConfirmIDDelete').val(tTextCode);
    }
}

//Functionality: Function Chack Dupilcate Data
//Parameters: Event Select List CarInfo
//Creator : 25/05/2021 Off
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

//Choose Checkbox
function JSxCarInfoVisibledDelAllBtn(poElement, poEvent) { // Action start after change check box value.
    try {
        var nCheckedCount = $('#odvCLDList td input:checked').length;
        if (nCheckedCount > 1) {
            $('#oliBtnDeleteAll').removeClass("disabled");
        } else {
            $('#oliBtnDeleteAll').addClass("disabled");
        }
        if (nCheckedCount > 1) {
            $('.xCNIconDel').addClass('xCNDisabled');
        } else {
            $('.xCNIconDel').removeClass('xCNDisabled');
        }
    } catch (err) {
        //console.log('JSxDepartmentVisibledDelAllBtn Error: ', err);
    }
}


//Functionality: Insert Text In Modal Delete
//Parameters: LocalStorage Data
//Creator : 25/05/2021 Off
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


// Functionality: Function Check Is Create Page
// Parameters: Event Documet Redy
//Creator : 25/05/2021 Off
// Return: object Status Delete
// ReturnType: boolean
function JSbCarInfoIsCreatePage() {
    try {
        const tCAICode = $('#oetCAICode').data('is-created');
        var bStatus = false;
        if (tCAICode == "") { // No have data
            bStatus = true;
        }
        
        return bStatus;
    } catch (err) {
        console.log('JSbCarInfoIsCreatePage Error: ', err);
    }
}

// Functionality: Function Check Is Update Page
// Parameters: Event Documet Redy
//Creator : 25/05/2021 Off
// Return: object Status Delete
// ReturnType: boolean
function JSbCarInfoIsUpdatePage() {
    try {
        const tCAICode = $('#oetCAICode').data('is-created');
        var bStatus = false;
        if (!tCAICode == "") { // Have data
            bStatus = true;
        }
        return bStatus;
    } catch (err) {
        console.log('JSbCarInfoIsUpdatePage Error: ', err);
    }
}

// Functionality : Show or Hide Component
// Parameters : ptComponent is element on document(id or class or...),pbVisible is visible
//Creator : 25/05/2021 Off
// Return : -
// Return Type : -
function JSxCarInfoVisibleComponent(ptComponent, pbVisible, ptEffect) {
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
        console.log('JSxCarInfoVisibleComponent Error: ', err);
    }
}