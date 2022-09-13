<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="table-responsive">
        <input type="text" class="xCNHide" id="ohdBrowseDataPdtCode" value="">
        <input type="text" class="xCNHide" id="ohdBrowseDataPunCode" value="">
        <input type="text" class="xCNHide" id="ohdEditInlinePdtCode" value="<?=$tTWIPdtCode;?>">
        <input type="text" class="xCNHide" id="ohdEditInlinePunCode" value="<?=$tTWIPunCode;?>">
        
        <table id="otbTWIDocPdtAdvTableList" class="table xWPdtTableFont">
            <thead>
                <tr class="xCNCenter">
                    <?php if((@$tTWIStaApv == '') && @$tTWIStaDoc != 3) { ?>
                        <th class="text-center" id="othCheckboxHide">
                            <label class="fancy-checkbox" style="padding-left:7px">
                                <input id="ocbCheckAll" type="checkbox" class="ocbListItemAll" name="ocbCheckAll" onclick="FSxDOSelectAll(this)" >
                                <span class="">&nbsp;</span>
                            </label>
                        </th>
                    <?php } ?>
                    <th><?=language('document/purchaseinvoice/purchaseinvoice','tPITBNo')?></th>
                    <th ><?= language('document/purchaseinvoice/purchaseinvoice', 'รหัสสินค้า')?></th>
                    <th ><?= language('document/purchaseinvoice/purchaseinvoice', 'ชื่อสินค้า')?></th>
                    <th ><?= language('document/purchaseinvoice/purchaseinvoice', 'บาร์โค้ด')?></th>
                    <th ><?= language('document/purchaseinvoice/purchaseinvoice', 'ชื่อหน่วยสินค้า')?></th>
                    <th ><?= language('document/purchaseinvoice/purchaseinvoice', 'จำนวน')?></th>

                    <?php if((@$tTWIStaApv == '') && @$tTWIStaDoc != 3) { ?>
                        <th class="xCNTWIBeHideMQSS"><?=language('document/purchaseinvoice/purchaseinvoice', 'tPITBDelete');?></th>
                        <!-- <th class="xCNTWIBeHideMQSS xWTWIDeleteBtnEditButtonPdt"><?php echo language('document/saleorder/saleorder','tSOTBEdit');?></th> -->
                    <?php } ?>
                </tr>
            </thead>
            <tbody id="odvTBodyTWIPdtAdvTableList">
                <?php $nNumSeq  = 0;?>
                <?php if($aDataDocDTTemp['rtCode'] == 1):?>
                    <?php foreach($aDataDocDTTemp['raItems'] as $DataTableKey => $aDataTableVal): ?>
                        <tr
                            class="text-center xCNTextDetail2 nItem<?=$nNumSeq?> xWPdtItem"
                            data-index="<?=$aDataTableVal['rtRowID'];?>"
                            data-docno="<?=$aDataTableVal['FTXthDocNo'];?>"
                            data-seqno="<?=$aDataTableVal['FNXtdSeqNo']?>"
                            data-pdtcode="<?=$aDataTableVal['FTPdtCode'];?>" 
                            data-pdtname="<?=$aDataTableVal['FTXtdPdtName'];?>"
                            data-puncode="<?=$aDataTableVal['FTPunCode'];?>"
                            data-qty="<?=$aDataTableVal['FCXtdQty'];?>"
                            data-setprice="<?=$aDataTableVal['FCXtdSetPrice'];?>"
                            data-stadis="<?=$aDataTableVal['FTXtdStaAlwDis']?>"
                            data-netafhd="<?=$aDataTableVal['FCXtdNetAfHD'];?>"
                        >   
                            <?php if((@$tTWIStaApv == '') && @$tTWIStaDoc != 3) { ?>
                                <td class="text-center">
                                    <label class="fancy-checkbox">
                                        <input id="ocbListItem<?=$aDataTableVal['rtRowID']?>" type="checkbox" class="ocbListItem" name="ocbListItem[]">
                                        <span></span>
                                    </label>
                                </td>
                            <?php } ?>
                            <td><label><?=$aDataTableVal['rtRowID']?></label></td>
                            <!-- <?php foreach($aColumnShow as $DataKey => $DataVal): ?>
                            <?php
                                $tColumnName        = $DataVal->FTShwFedShw;
                                $nColWidth          = $DataVal->FNShwColWidth;
                                $tColumnDataType    = substr($tColumnName, 0, 2);
                                if($tColumnDataType == 'FC'){
                                    $tMaxlength     = '11';
                                    $tAlignFormat   = 'text-right';
                                    $tDataCol       =  $aDataTableVal[$tColumnName] != '' ? number_format($aDataTableVal[$tColumnName], $nOptDecimalShow, '.', ',') : number_format(0, $nOptDecimalShow,'.',',');
                                    $InputType      = 'text';
                                    $tValidateType  = 'xCNInputNumericWithDecimal';
                                }
                                if($tColumnDataType == 'FN'){
                                    $tMaxlength     = '';
                                    $tAlignFormat   = 'text-right';
                                    $tDataCol       = $aDataTableVal[$tColumnName] != '' ? number_format($aDataTableVal[$tColumnName], $nOptDecimalShow, '.', ',') : number_format(0, $nOptDecimalShow,'.',',');
                                    $InputType      = 'number';
                                    $tValidateType  = '';
                                }
                                if($tColumnDataType == 'FD'){
                                    $tMaxlength     = '';
                                    $tAlignFormat   = 'text-left';
                                    $tDataCol       = date('Y-m-d H:i:s');
                                    $InputType      = 'text';
                                    $tValidateType  = '';
                                }
                                if($tColumnDataType == 'FT'){
                                    $tMaxlength     = '';
                                    $tAlignFormat   = 'text-left';
                                    $tDataCol       = $aDataTableVal[$tColumnName];
                                    $InputType      = 'text';
                                    $tValidateType  = '';
                                }
                            ?>
                                <td nowrap class="<?=$tAlignFormat?>">
                                    <?php if($DataVal->FTShwStaAlwEdit == 1 && in_array($tColumnName, ['FCXtdSetPrice','FCXtdQty']) && (empty($tTWIStaApv) && $tTWIStaDoc != 3)):?>
                                            <label 
                                                data-field="<?=$tColumnName?>"
                                                data-seq="<?= $aDataTableVal['FNXtdSeqNo']?>"
                                                data-demo="TextDEmo"
                                                class="xCNPdtFont xWShowInLine<?=$aDataTableVal['rtRowID']?> xWShowValue<?=$tColumnName?><?=$aDataTableVal['rtRowID']?>"
                                            >
                                                <?=$tDataCol != '' ? "".$tDataCol : '1'; ?>
                                            </label>
                                            <div class="xCNHide xWEditInLine<?=$aDataTableVal['FNXtdSeqNo']?>">
                                                <input 
                                                    type="<?=$InputType?>" 
                                                    class="form-control xCNPdtEditInLine xWValueEditInLine<?=$aDataTableVal['rtRowID']?> <?=$tValidateType?> <?=$tAlignFormat;?>"
                                                    id="ohd<?=$tColumnName?><?=$aDataTableVal['rtRowID']?>" 
                                                    name="ohd<?=$tColumnName?><?=$aDataTableVal['rtRowID']?>" 
                                                    maxlength="<?=$tMaxlength?>" 
                                                    value="<?=$tDataCol;?>"
                                                    <?=$tColumnName == 'FTXtdDisChgTxt' ? 'readonly' : '' ?> <?=$tColumnName == 'FCXtdQty'; ?>>
                                            </div>       
                                    <?php else: ?>
                                        <label class="xCNPdtFont xWShowInLine xWShowValue<?=$tColumnName?><?=$aDataTableVal['rtRowID']?>"><?=$tDataCol?></label>
                                    <?php endif;?>                  
                                </td>
                            <?php endforeach; ?> -->
                            <td class="text-left" ><label class="text-left xCNPdtFont xWShowValueFTPdtCode<?php echo $aDataTableVal['FNXtdSeqNo']?>"><?php echo $aDataTableVal['FTPdtCode']?></label></td>
                            <td class="text-left" ><label class="text-left xCNPdtFont xWShowValueFTXtdPdtName<?php echo $aDataTableVal['FNXtdSeqNo']?>"><?php echo $aDataTableVal['FTXtdPdtName']?></label></td>
                            <td class="text-left"><label class="text-left xCNPdtFont xWShowValueFTXtdBarCode<?php echo $aDataTableVal['FNXtdSeqNo']?>"><?php echo $aDataTableVal['FTXtdBarCode']?></label></td>
                            <td class="text-left"><label class="text-left xCNPdtFont xWShowValueFTPunName<?php echo $aDataTableVal['FNXtdSeqNo']?>"><?php echo $aDataTableVal['FTPunName']?></label></td>
                            <?php if((@$tTWIStaApv == '') && @$tTWIStaDoc != 3) { ?>
                            <td class="otdQty">
                                <div class="xWEditInLine<?=$aDataTableVal['FNXtdSeqNo']?>">
                                    <input type="text" class="xCNQty form-control xWEditPdtInline xCNInputNumericWithDecimal xCNPdtEditInLine text-right xWValueEditInLine<?=$aDataTableVal['FNXtdSeqNo']?> xWShowInLine<?=$aDataTableVal['FNXtdSeqNo']?> " id="ohdQty<?=$aDataTableVal['FNXtdSeqNo']?>" name="ohdQty<?=$aDataTableVal['FNXtdSeqNo']?>" data-seq="<?=$aDataTableVal['FNXtdSeqNo']?>" maxlength="10" value="<?=str_replace(",","",number_format($aDataTableVal['FCXtdQty'],$nOptDecimalShow));?>" autocomplete="off">
                                </div>
                            </td>
                            <?php }else{ ?>
                                <td class="text-right"><label class="text-left xCNPdtFont xWShowValueFCXtdQty<?php echo $aDataTableVal['FNXtdSeqNo']?>"><?php echo number_format($aDataTableVal['FCXtdQty'], $nOptDecimalShow, '.', ',')?></label></td>
                            <?php } ?>
                            <?php if((@$tTWIStaApv == '') && @$tTWIStaDoc != 3){ ?>
                                <td nowrap class="text-center xCNTWIBeHideMQSS">
                                    <label class="xCNTextLink">
                                        <img class="xCNIconTable" id="omgDel" src="<?= base_url('application/modules/common/assets/images/icons/delete.png'); ?>" title="Remove" onclick="JSnTWIDelPdtInDTTempSingle(this)">
                                    </label>
                                </td>
                            <?php } ?> 
                        </tr>
                        <?php $nNumSeq++; ?>
                    <?php endforeach;?>
                <?php else:?>
                    <tr><td class="text-center xCNTextDetail2 xWTWITextNotfoundDataPdtTable" colspan="100%"><?=language('common/main/main','tCMNNotFoundData')?></td></tr>
                <?php endif;?>
            </tbody>
        </table>
    </div>
</div>
<?php if($aDataDocDTTemp['rnAllPage'] > 1) : ?>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <p><?php echo language('common/main/main','tResultTotalRecord')?> <?php echo $aDataDocDTTemp['rnAllRow']?> <?php echo language('common/main/main','tRecord')?> <?php echo language('common/main/main','tCurrentPage')?> <?php echo $aDataDocDTTemp['rnCurrentPage']?> / <?php echo $aDataDocDTTemp['rnAllPage']?></p>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="xWPageTWIPdt btn-toolbar pull-right">
            <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
            <button onclick="JSvTWIPDTDocDTTempClickPage('previous')" class="btn btn-white btn-sm" <?php echo $tDisabledLeft ?>> 
                <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
            </button>
            <?php for($i=max($nPage-2, 1); $i<=max(0, min($aDataDocDTTemp['rnAllPage'],$nPage+2)); $i++){?> 
                <?php 
                    if($nPage == $i){ 
                        $tActive = 'active'; 
                        $tDisPageNumber = 'disabled';
                    }else{ 
                        $tActive = '';
                        $tDisPageNumber = '';
                    }
                ?>
                <button onclick="JSvTWIPDTDocDTTempClickPage('<?php echo $i?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
            <?php } ?>
            <?php if($nPage >= $aDataDocDTTemp['rnAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
            <button onclick="JSvTWIPDTDocDTTempClickPage('next')" class="btn btn-white btn-sm" <?php echo $tDisabledRight ?>> 
                <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
            </button>
        </div>
    </div>
<?php endif;?>

<!-- ============================================ Modal Confirm Delete Documet Detail Dis ============================================ -->
    <div id="odvPIModalConfirmDeleteDTDis" class="modal fade" style="z-index: 7000;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header xCNModalHead">
                    <label class="xCNTextModalHeard"><?= language('common/main/main', 'tPIMsgNotificationChangeData') ?></label>
                </div>
                <div class="modal-body">
                    <label><?php echo language('document/purchaseinvoice/purchaseinvoice','tPIMsgTextNotificationChangeData');?></label>
                </div>
                <div class="modal-footer">
                    <button id="obtPIConfirmDeleteDTDis" type="button"  class="btn xCNBTNPrimery"><?php echo language('common/main/main', 'tModalConfirm');?></button>
                    <button id="obtPICancelDeleteDTDis" type="button" class="btn xCNBTNDefult"><?php echo language('common/main/main', 'ยกเลิก');?></button>
                </div>
            </div>
        </div>
    </div>
<!-- ================================================================================================================================= -->

<script src="<?php echo  base_url('application/modules/common/assets/js/jquery.mask.js')?>"></script>
<script src="<?php echo  base_url('application/modules/common/assets/src/jFormValidate.js')?>"></script>

<script>
    //ลบสินค้าใน Tmp - หลายตัว
    $('#otbTWIDocPdtAdvTableList #odvTBodyTWIPdtAdvTableList .ocbListItem').unbind().click(function(){
        var tTWIDocNo    = $('#oetTWIDocNo').val();
        var tTWISeqNo    = $(this).parents('.xWPdtItem').data('seqno');
        var tTWIPdtCode  = $(this).parents('.xWPdtItem').data('pdtcode');
        var tTWIPunCode  = $(this).parents('.xWPdtItem').data('puncode');
        $(this).prop('checked', true);
        let oLocalItemDTTemp    = localStorage.getItem("TWI_LocalItemDataDelDtTemp");
        let oDataObj            = [];
        if(oLocalItemDTTemp){
            oDataObj    = JSON.parse(oLocalItemDTTemp);
        }
        let aArrayConvert   = [JSON.parse(localStorage.getItem("TWI_LocalItemDataDelDtTemp"))];
        if(aArrayConvert == '' || aArrayConvert == null){
            oDataObj.push({
                'tDocNo'    : tTWIDocNo,
                'tSeqNo'    : tTWISeqNo,
                'tPdtCode'  : tTWIPdtCode,
                'tPunCode'  : tTWIPunCode,
            });
            localStorage.setItem("TWI_LocalItemDataDelDtTemp",JSON.stringify(oDataObj));
            JSxTWITextInModalDelPdtDtTemp();
        }else{
            var aReturnRepeat   = JStTWIFindObjectByKey(aArrayConvert[0],'tSeqNo',tTWISeqNo);
            if(aReturnRepeat == 'None' ){
                //ยังไม่ถูกเลือก
                oDataObj.push({
                    'tDocNo'    : tTWIDocNo,
                    'tSeqNo'    : tTWISeqNo,
                    'tPdtCode'  : tTWIPdtCode,
                    'tPunCode'  : tTWIPunCode,
                });
                localStorage.setItem("TWI_LocalItemDataDelDtTemp",JSON.stringify(oDataObj));
                JSxTWITextInModalDelPdtDtTemp();
            }else if(aReturnRepeat == 'Dupilcate'){
                localStorage.removeItem("TWI_LocalItemDataDelDtTemp");
                $(this).prop('checked', false);
                var nLength = aArrayConvert[0].length;
                for($i=0; $i<nLength; $i++){
                    if(aArrayConvert[0][$i].tSeqNo == tTWISeqNo){
                        delete aArrayConvert[0][$i];
                    }
                }
                var aNewarraydata   = [];
                for($i=0; $i<nLength; $i++){
                    if(aArrayConvert[0][$i] != undefined){
                        aNewarraydata.push(aArrayConvert[0][$i]);
                    }
                }
                localStorage.setItem("TWI_LocalItemDataDelDtTemp",JSON.stringify(aNewarraydata));
                JSxTWITextInModalDelPdtDtTemp();
            }
        }
        JSxTWIShowButtonDelMutiDtTemp();
    });
    
    $(document).ready(function(){
        JSxEditQtyAndPrice(); 
        if((tTWIStaDoc == 3) || (tTWIStaApvDoc == 1 || tTWIStaPrcStkDoc == 1)){
                $('#otbTWIDocPdtAdvTableList .xCNPIBeHideMQSS').hide();
        }else{
            // var oParameterEditInLine    = {
            //     "DocModules"                    : "",
            //     "FunctionName"                  : "JSxTWISaveEditInline",
            //     "DataAttribute"                 : ['data-field', 'data-seq'],
            //     "TableID"                       : "otbTWIDocPdtAdvTableList",
            //     "NotFoundDataRowClass"          : "xWTWITextNotfoundDataPdtTable",
            //     "EditInLineButtonDeleteClass"   : "xWTWIDeleteBtnEditButtonPdt",
            //     "LabelShowDataClass"            : "xWShowInLine",
            //     "DivHiddenDataEditClass"        : "xWEditInLine"
            // }
            // JCNxSetNewEditInline(oParameterEditInLine);

            // $(".xWEditInlineElement").eq(nIndexInputEditInline).focus();
            // $(".xWEditInlineElement").eq(nIndexInputEditInline).select();
            $(".xWEditInlineElement").removeAttr("disabled");

            let oElement = $(".xWEditInlineElement");
            for(let nI=0;nI<oElement.length;nI++){
                $(oElement.eq(nI)).val($(oElement.eq(nI)).val().trim());
            }
        }
    });

    //เเก้ไขจำนวน
    function JSxEditQtyAndPrice() {
    $('.xCNPdtEditInLine').click(function() {
        $(this).focus().select();
    });
    // $('.xCNQty').change(function(e){
    $('.xCNQty').off().on('change keyup', function(e) {
        if(e.type === 'change' || e.keyCode === 13){
            var nSeq    = $(this).attr('data-seq');
            var nQty        = $('#ohdQty'+nSeq).val();
            nNextTab = parseInt(nSeq)+1;
            $('.xWValueEditInLine'+nNextTab).focus().select();
            JSxGetDisChgList(nSeq);
        }
    });
    }

    //เเก้ไขจำนวน และ ราคา
    function JSxGetDisChgList(pnSeq){

    var nQty        = $('#ohdQty'+pnSeq).val();
    var tTRBDocNo        = $("#oetTransferBchOutDocNo").val();
    var tTRBBchCode      = $("#oetTransferBchOutBchCode").val();
    FSvTWIEditPdtIntoTableDT(pnSeq, "FCXtdQty", nQty);
    }

    // Check All
    $('#ocbCheckAll').click(function(){
        if($(this).is(':checked')==true){
            $('.ocbListItem').prop('checked',true);
            $("#odvTWIMngDelPdtInTableDT #oliTWIBtnDeleteMulti").removeClass("disabled");
            $("#omgDel").attr("disabled", true);
        }else{
            $('.ocbListItem').prop('checked',false);
            $("#odvTWIMngDelPdtInTableDT #oliTWIBtnDeleteMulti").addClass("disabled");
            $("#omgDel").removeAttr("disabled");
        }
    });

    function FSxDOSelectAll(){
        if($('.ocbListItemAll').is(":checked")){
            $('.ocbListItem').each(function (e) { 
                if(!$(this).is(":checked")){
                    $(this).on( "click", FSxDOSelectMulDel(this) );
                }
            });
        }else{
            $('.ocbListItem').each(function (e) { 
                if($(this).is(":checked")){
                    $(this).on( "click", FSxDOSelectMulDel(this) );
                }
            });
        }
    }

    //ลบสินค้าใน Tmp - หลายตัว
    function FSxDOSelectMulDel(ptElm){
        var tTWIDocNo    = $('#oetTWIDocNo').val();
        var tTWISeqNo    = $(ptElm).parents('.xWPdtItem').data('seqno');
        var tTWIPdtCode  = $(ptElm).parents('.xWPdtItem').data('pdtcode');
        var tTWIPunCode  = $(ptElm).parents('.xWPdtItem').data('puncode');
        $(ptElm).prop('checked', true);
        let oLocalItemDTTemp    = localStorage.getItem("TWI_LocalItemDataDelDtTemp");
        let oDataObj            = [];
        if(oLocalItemDTTemp){
            oDataObj    = JSON.parse(oLocalItemDTTemp);
        }
        let aArrayConvert   = [JSON.parse(localStorage.getItem("TWI_LocalItemDataDelDtTemp"))];
        if(aArrayConvert == '' || aArrayConvert == null){
            oDataObj.push({
                'tDocNo'    : tTWIDocNo,
                'tSeqNo'    : tTWISeqNo,
                'tPdtCode'  : tTWIPdtCode,
                'tPunCode'  : tTWIPunCode,
            });
            localStorage.setItem("TWI_LocalItemDataDelDtTemp",JSON.stringify(oDataObj));
            JSxTWITextInModalDelPdtDtTemp();
        }else{
            var aReturnRepeat   = JStTWIFindObjectByKey(aArrayConvert[0],'tSeqNo',tTWISeqNo);
            if(aReturnRepeat == 'None' ){
                //ยังไม่ถูกเลือก
                oDataObj.push({
                    'tDocNo'    : tTWIDocNo,
                    'tSeqNo'    : tTWISeqNo,
                    'tPdtCode'  : tTWIPdtCode,
                    'tPunCode'  : tTWIPunCode,
                });
                localStorage.setItem("TWI_LocalItemDataDelDtTemp",JSON.stringify(oDataObj));
                JSxTWITextInModalDelPdtDtTemp();
            }else if(aReturnRepeat == 'Dupilcate'){
                localStorage.removeItem("TWI_LocalItemDataDelDtTemp");
                $(ptElm).prop('checked', false);
                var nLength = aArrayConvert[0].length;
                for($i=0; $i<nLength; $i++){
                    if(aArrayConvert[0][$i].tSeqNo == tTWISeqNo){
                        delete aArrayConvert[0][$i];
                    }
                }
                var aNewarraydata   = [];
                for($i=0; $i<nLength; $i++){
                    if(aArrayConvert[0][$i] != undefined){
                        aNewarraydata.push(aArrayConvert[0][$i]);
                    }
                }
                localStorage.setItem("TWI_LocalItemDataDelDtTemp",JSON.stringify(aNewarraydata));
                JSxTWITextInModalDelPdtDtTemp();
            }
        }
        JSxTWIShowButtonDelMutiDtTemp();
    
    }
</script>