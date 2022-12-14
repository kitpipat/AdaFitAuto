<script>

    $(document).ready(function(){

        // Put Sum HD In Footer
        // $('#othFCXthTotal').text($('#ohdFCXthTotalShow').val());
         
        //================================== in call function
        var oParameterSend = {
            "DocModules" : "",
            "FunctionName" : "JSxPNSaveInline",
            "DataAttribute" : ['data-field', 'data-seq'],
            "TableID" : "otbPNDOCPdtTable",
            "NotFoundDataRowClass" : "xWPNTextNotfoundDataPdtTable",
            "EditInLineButtonDeleteClass" : "xWPNDeleteBtnEditButtonPdt",
            "LabelShowDataClass" : "xWShowInLine",
            "DivHiddenDataEditClass" : "xWEditInLine"
        };
        JCNxSetNewEditInline(oParameterSend);
        $(".xWEditInlineElement").eq(nIndexInputEditInline).focus();
        $(".xWEditInlineElement").eq(nIndexInputEditInline).select();

        $(".xWEditInlineElement").removeAttr("disabled");


        let oElement = $(".xWEditInlineElement");
        for(let nI=0;nI<oElement.length;nI++){
            $(oElement.eq(nI)).val($(oElement.eq(nI)).val().trim());
        }
        //================================== end in call function
        
      JSxShowButtonChoose();

      var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
      var nlength = $('#odvRGPList').children('tr').length;
      for($i=0; $i < nlength; $i++){
            var tDataCode = $('#otrSpaTwoPdt'+$i).data('seq');
        if(aArrayConvert == null || aArrayConvert == ''){
        }else{
                var aReturnRepeat = findObjectByKey(aArrayConvert[0],'tSeq',tDataCode);
          if(aReturnRepeat == 'Dupilcate'){
            $('#ocbListPdtItem'+$i).prop('checked', true);
          }else{ }
        }
      }

      $('.ocbListPdtItem').click(function(){

          var tSeq = $(this).parent().parent().parent().data('seqno'); // Seq
          var tPdt = $(this).parent().parent().parent().data('pdtcode'); // Pdt
          var tDoc = $(this).parent().parent().parent().data('docno'); // Doc
          var tPun = $(this).parent().parent().parent().data('puncode'); // Pun

          $(this).prop('checked', true);
          var LocalItemData = localStorage.getItem("LocalItemData");
          var obj = [];
          if(LocalItemData){
              obj = JSON.parse(LocalItemData);
          }
          var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
          if(aArrayConvert == '' || aArrayConvert == null){
              obj.push({"tSeq": tSeq, 
                        "tPdt": tPdt, 
                        "tDoc": tDoc, 
                        "tPun": tPun 
                      });
              localStorage.setItem("LocalItemData",JSON.stringify(obj));
              JSxPNPdtTextinModal();
          }else{
              var aReturnRepeat = findObjectByKey(aArrayConvert[0],'tSeq',tSeq);
              if(aReturnRepeat == 'None' ){ // ??????????????????????????????????????????
                  obj.push({"tSeq": tSeq, 
                            "tPdt": tPdt, 
                            "tDoc": tDoc, 
                            "tPun": tPun 
                          });
                  localStorage.setItem("LocalItemData",JSON.stringify(obj));
                  JSxPNPdtTextinModal();
              }else if(aReturnRepeat == 'Dupilcate'){ // ?????????????????????????????????????????????
                  localStorage.removeItem("LocalItemData");
                  $(this).prop('checked', false);
                  var nLength = aArrayConvert[0].length;
                  for($i=0; $i<nLength; $i++){
                      if(aArrayConvert[0][$i].tSeq == tSeq){
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
                  JSxPNPdtTextinModal();
              }
          }
          JSxShowButtonChoose();
      });

    });
    
    function JSxPNSaveInline(paParams){
        // console.log('JSxPNSaveInline: ', paParams);
        var oThisEl = paParams['Element'];
        var tThisDisChgText = $(oThisEl).parents('tr.xWPdtItem').find('td label.xWPNDisChgDT').text();
        
        if(tThisDisChgText == ''){ // ?????????????????????/???????????????
            var nSeqNo = paParams.DataAttribute[1]['data-seq'];
            var tFieldName = paParams.DataAttribute[0]['data-field'];
            var tValue = accounting.unformat(paParams.VeluesInline);
            var bIsDelDTDis = false;
            FSvPNEditPdtIntoTableDT(nSeqNo, tFieldName, tValue, bIsDelDTDis); 
            
        }else{ // ????????????/???????????????
            $('#odvModalConfirmDeleteDTDis').modal({
                backdrop: 'static',
                show: true
            });
            
            $('#obtPNConfirmDeleteDTDis').one('click', function(){
                $('#odvModalConfirmDeleteDTDis').modal('hide');
                var nSeqNo = paParams.DataAttribute[1]['data-seq'];
                var tFieldName = paParams.DataAttribute[0]['data-field'];
                var tValue = accounting.unformat(paParams.VeluesInline);
                var bIsDelDTDis = true;
                FSvPNEditPdtIntoTableDT(nSeqNo, tFieldName, tValue, bIsDelDTDis);
            });
            
            $('#obtPNCancelDeleteDTDis').one('click', function(){
                if (JCNbPNIsDocType('havePdt')) {
                    JSvPNLoadPdtDataTableHtml(1, false);
                }
                if (JCNbPNIsDocType('nonePdt')) {
                    JSvPNLoadNonePdtDataTableHtml(1, false);
                }
            });
        }
        
    }
    
    /**
     * Functionality : Delete Record Before to Save.
     * Parameters : poElement is Itself element, poEvent is Itself event
     * Creator : 24/12/2018 piya
     * Last Modified : -
     * Return : -
     * Return Type : -
     */
    function JSnCrdditNoteRemoveDTRow(poEl) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof nStaSession !== "undefined" && nStaSession == 1) {
            var tVal = $(poEl).parents("tr.xWPdtItem").attr("data-pdtcode");
            var tSeqno = $(poEl).parents("tr.xWPdtItem").attr("data-seqno");
            $(poEl).parents("tr.xWPdtItem").remove();
            JSnPNRemoveDTTemp(tSeqno, tVal);
        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    /**
    * Functionality: Event Pdt Multi Delete
    * Parameters: Event Button Delete All
    * Creator: 22/05/2019 Piya
    * Return:  object Status Delete
    * Return Type: object
    */
    function JSoPNPdtDelChoose(pnPage) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof nStaSession !== "undefined" && nStaSession == 1) {
            
            var aSeq = $("#ohdConfirmSeqDelete").val();
            var tDocNo = $("#oetPNDocNo").val();

            // PdtCode
            var aTextSeq = aSeq.substring(0, aSeq.length - 2);
            var aSeqSplit = aTextSeq.split(" , ");
            var aSeqSplitlength = aSeqSplit.length;
            // Seq
            var aTextSeq = aSeq.substring(0, aSeq.length - 2);
            var aSeqSplit = aTextSeq.split(" , ");
            var aSeqData = [];

            for ($i = 0; $i < aSeqSplitlength; $i++) {
                aSeqData.push(aSeqSplit[$i]);
            }

            if (aSeqSplitlength > 1) {
                // JCNxOpenLoading();
                localStorage.StaDeleteArray = "1";
                $.ajax({
                    type: "POST",
                    url: "PNPdtMultiDeleteEvent",
                    data: {
                        tDocNo: tDocNo,
                        tSeqCode: aSeqData
                    },
                    success: function (tResult) {
                        // console.log(tResult);
                        setTimeout(function () {
                            $("#odvModalDelPdtPN").modal("hide");

                            if(JCNbPNIsDocType('havePdt')){
                                JSvPNLoadPdtDataTableHtml();
                            }
                            if(JCNbPNIsDocType('nonePdt')){
                                JSvPNLoadNonePdtDataTableHtml();
                            }

                            $("#ospConfirmDelete").text($("#oetTextComfirmDeleteSingle").val());
                            $("#ohdConfirmSeqDelete").val("");
                            $("#ohdConfirmPdtDelete").val("");
                            $("#ohdConfirmPunDelete").val("");
                            $("#ohdConfirmDocDelete").val("");
                            localStorage.removeItem("LocalItemData");
                            $(".obtChoose").hide();
                            $(".modal-backdrop").remove();
                        }, 1000);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        JCNxResponseError(jqXHR, textStatus, errorThrown);
                    }
                });
            } else {
                localStorage.StaDeleteArray = "0";
                return false;
            }
            
        }else {
            JCNxShowMsgSessionExpired();
        }
    }
    
    /**
    * Functionality: Event Edit Pdt Table
    * Parameters: Event Proporty
    * Creator: 22/05/2019 Piya
    * Return:  
    * Return 
    */
    function JCNvPNDisChagDT(poEl) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof nStaSession !== "undefined" && nStaSession == 1) {
            
            var tDocNo = $(poEl).parents('.xWPdtItem').data('docno');
            var tPdtCode = $(poEl).parents('.xWPdtItem').data('pdtcode');
            var tPdtName = $(poEl).parents('.xWPdtItem').data('pdtname');
            var tPunCode = $(poEl).parents('.xWPdtItem').data('puncode');
            var tNet = $(poEl).parents('.xWPdtItem').data('net');
            var tSetPrice = $(poEl).parents('.xWPdtItem').data('set-price');
            var tQty = $(poEl).parents('.xWPdtItem').data('qty');
            var tStaDis = $(poEl).parents('.xWPdtItem').data('stadis');
            var tSeqNo = $(poEl).parents('.xWPdtItem').data('seqno');
            var bHaveDisChgDT = $(poEl).parents('.xWPNDisChgDTForm').find('label.xWPNDisChgDT').text() == '' ? false : true;
            
            window.DisChgDataRowDT = {
                tDocNo: tDocNo,
                tPdtCode: tPdtCode,
                tPdtName: tPdtName,
                tPunCode: tPunCode,
                tNet: tNet,
                tSetPrice: tSetPrice,
                tQty: tQty,
                tStadis: tStaDis,
                tSeqNo: tSeqNo,
                bHaveDisChgDT: bHaveDisChgDT
            };
            
            var oDisChgParams = {
                DisChgType: 'disChgDT'
            };
            JSxPNOpenDisChgPanel(oDisChgParams);
        } else {
            JCNxShowMsgSessionExpired();
        }
    }

</script>
















































































