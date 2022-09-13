<script type="text/javascript">
    localStorage.removeItem("LocalItemData");
    $(document).ready(function(){
        $('.ocbListItem').unbind().click(function(){
            var nCode   = $(this).parent().parent().parent().data('code');  //code
            var tName   = $(this).parent().parent().parent().data('name');  //code
            $(this).prop('checked', true); 
            $('#odvBtnAddEdit').show();
            $('#odvBtnSelectLOG').show();
            var LocalItemData   = localStorage.getItem("LocalItemData");
            var obj = [];
            if(LocalItemData){
                obj = JSON.parse(LocalItemData);
            }else{}
            var aArrayConvert   = [JSON.parse(localStorage.getItem("LocalItemData"))];
            if(aArrayConvert == '' || aArrayConvert == null){
                obj.push({"nCode": nCode, "tName": tName });
                localStorage.setItem("LocalItemData",JSON.stringify(obj));
                JSxLOGTextinModal();
            }else{
                var aReturnRepeat = JStLOGFindObjectByKey(aArrayConvert[0],'nCode',nCode);
                if(aReturnRepeat == 'None' ){           //ยังไม่ถูกเลือก
                    obj.push({"nCode": nCode, "tName": tName });
                    localStorage.setItem("LocalItemData",JSON.stringify(obj));
                    JSxLOGTextinModal();
                }else if(aReturnRepeat == 'Dupilcate'){	//เคยเลือกไว้แล้ว
                    localStorage.removeItem("LocalItemData");
                    $(this).prop('checked', false);
                    $('#odvBtnAddEdit').hide();
                    $('#odvBtnSelectLOG').hide();
                    var nLength = aArrayConvert[0].length;
                    for($i=0; $i<nLength; $i++){
                        if(aArrayConvert[0][$i].nCode == nCode){
                            delete aArrayConvert[0][$i];
                        }
                    }
                    var aNewarraydata = [];
                    for($i=0; $i<nLength; $i++){
                        if(aArrayConvert[0][$i] != undefined){
                            aNewarraydata.push(aArrayConvert[0][$i]);
                        }
                    }
                    localStorage.setItem("LocalItemData",JSON.stringify(aNewarraydata));
                    JSxLOGTextinModal();
                }
            }
            JSxShowButtonChoose();
        });
        // Event Click Confrim Delete Multiple
        $('#odvLOGModalDelDocMultiple #osmConfirmDelMultiple').unbind().click(function(){
            var nStaSession = JCNxFuncChkSessionExpired();
            if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
                JSoLOGDelDocMultiple();
            }else{
                JCNxShowMsgSessionExpired();
            }
        });
    });


    // Insert Text In Modal Delete
    function JSxLOGTextinModal(){
        var aArrayConvert   = [JSON.parse(localStorage.getItem("LocalItemData"))];
        if (aArrayConvert[0] == null || aArrayConvert[0] == "") { } else {
            var tTextCode   = "";
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
            $("#odvLOGModalDelDocMultiple #ospTextConfirmDelMultiple").text($('#oetTextComfirmDeleteMulti').val());
            $("#odvLOGModalDelDocMultiple #ohdConfirmIDDelMultiple").val(tTextCode);
        }
    }

    // เช็คค่าใน array [หลายรายการ]
    function JStLOGFindObjectByKey(array, key, value) {
        for (var i = 0; i < array.length; i++) {
            if (array[i][key] === value) {
                return "Dupilcate";
            }
        }
        return "None";
    }

    // เปิดปุ่มให้ลบได้ กรณีลบหลายรายการ
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

    // Click Next Page Controll Pagination
    function JSvLOGClickPageList(ptPage){
        var nPageCurrent    = '';
        switch (ptPage) {
            case 'next': //กดปุ่ม Next
                $('.xWBtnNext').addClass('disabled');
                nPageOld    = $('.xWLOGPageDataTable .active').text(); // Get เลขก่อนหน้า
                nPageNew    = parseInt(nPageOld, 10) + 1; // +1 จำนวน
                nPageCurrent = nPageNew
                break;
            case 'previous': //กดปุ่ม Previous
                nPageOld    = $('.xWLOGPageDataTable .active').text(); // Get เลขก่อนหน้า
                nPageNew    = parseInt(nPageOld, 10) - 1; // -1 จำนวน
                nPageCurrent = nPageNew
                break;
            default:
                nPageCurrent = ptPage
        }
        JSvLOGCallPageDataTable(nPageCurrent);
    }
    
    // Delete Document Multiple
    function JSoLOGDelDocMultiple(){
        var aDataDelMultiple    = $('#odvLOGModalDelDocMultiple #ohdConfirmIDDelMultiple').val();
        var aTextsDelMultiple   = aDataDelMultiple.substring(0, aDataDelMultiple.length - 2);
        var aDataSplit          = aTextsDelMultiple.split(" , ");
        var nDataSplitlength    = aDataSplit.length;
        var aNewIdDelete        = [];
        for ($i = 0; $i < nDataSplitlength; $i++) {
            aNewIdDelete.push(aDataSplit[$i].trim());
        }
        if (nDataSplitlength > 1) {
            JCNxOpenLoading();

            $.ajax({
                type    : "POST",
                url     : "docLOGEventDelete",
                data    : {'tDataDocNo' : aNewIdDelete},
                cache   : false,
                timeout : 0,
                success : function (oResult) {
                    setTimeout(function () {
                        $('#odvLOGModalDelDocMultiple').modal('hide');
                        $('#odvLOGModalDelDocMultiple #ospTextConfirmDelMultiple').empty();
                        $('#odvLOGModalDelDocMultiple #ohdConfirmIDDelMultiple').val('');
                        $('.modal-backdrop').remove();
                        localStorage.removeItem('LocalItemData');
                        JSvLOGCallPageDataTable();
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        }
    }

    // Delete Document Single
    function JSoLOGDelDocSingle(ptCurrentPage, ptLOGDocNo, ptLOGAgnCode, ptLOGBchCode, ptLOGDocRefCode) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof nStaSession !== "undefined" && nStaSession == 1) {
            if (typeof(ptLOGDocNo) != undefined && ptLOGDocNo != "") {
                var tTextConfrimDelSingle = $('#oetTextComfirmDeleteSingle').val() + "&nbsp" + ptLOGDocNo + "&nbsp" + $('#oetTextComfirmDeleteYesOrNot').val();
                $('#odvLOGModalDelDocSingle #ospTextConfirmDelSingle').html(tTextConfrimDelSingle);
                $('#odvLOGModalDelDocSingle').modal('show');
                $('#odvLOGModalDelDocSingle #osmConfirmDelSingle').unbind().click(function() {
                    JCNxOpenLoading();
                    $.ajax({
                        type: "POST",
                        url: "docLOGEventDelete",
                        data: {
                            'tDataDocNo': ptLOGDocNo,
                            'tLOGAgnCode' : ptLOGAgnCode,
                            'tLOGBchCode' : ptLOGBchCode,
                            'tLOGDocRefCode' : ptLOGDocRefCode
                        },
                        cache: false,
                        timeout: 0,
                        success: function(oResult) {
                            var aReturnData = JSON.parse(oResult);
                            if (aReturnData['nStaEvent'] == '1') {
                                $('#odvLOGModalDelDocSingle').modal('hide');
                                $('#odvLOGModalDelDocSingle #ospTextConfirmDelSingle').html($('#oetTextComfirmDeleteSingle').val());
                                $('.modal-backdrop').remove();
                                setTimeout(function() {
                                JSvLOGCallPageDataTable(ptCurrentPage);
                                }, 500);
                            } else {
                                JCNxCloseLoading();
                                FSvCMNSetMsgErrorDialog(aReturnData['tStaMessg']);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            JCNxResponseError(jqXHR, textStatus, errorThrown);
                        }
                    });
                });
            } else {
                FSvCMNSetMsgErrorDialog('Error Not Found Document Number !!');
            }
        } else {
            JCNxShowMsgSessionExpired();
        }
    }

</script>