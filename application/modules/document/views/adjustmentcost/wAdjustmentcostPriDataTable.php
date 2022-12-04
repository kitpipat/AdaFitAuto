<?php 
    // echo "<pre>";
    // print_r($aPdtPriDataList);
    // echo "</pre>";
    if(isset($aPdtPriDataList['rtCode']) && $aPdtPriDataList['rtCode'] == '1'){
        $nCurrentPage   = $aPdtPriDataList['rnCurrentPage'];
    }else{
        $nCurrentPage   = '1';
    }
?>
<style type="text/css">
    .table>tbody>tr>td.text-danger{color: #F9354C !important;}
</style>
<?php 
    $tImportStatus  = "1";
    if( $aPdtPriDataList['rtCode'] == 1 ){
        foreach($aPdtPriDataList['raItems'] as $DataTableKey => $DataTableVal){
            if( $DataTableVal['FTTmpStatus'] != '1' ){
                $tImportStatus  = $DataTableVal['FTTmpStatus'];
            }
        }
    }
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="table-responsive">
            <table id="otbAdDataList" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center otdListItem">
                            <label class="fancy-checkbox">
                                <input id="ocbCheckAll" type="checkbox" class="ocbListItemAll" name="ocbCheckAll" onclick="FSxSPASelectAll(this)">
                                <span class="">&nbsp;</span>
                            </label>
                        </th>
                        <th ><?= language('document/purchaseorder/purchaseorder','ลำดับ')?></th>
                        <th ><?= language('document/purchaseorder/purchaseorder', 'รหัสสินค้า')?></th>
                        <th ><?= language('document/purchaseorder/purchaseorder', 'ชื่อสินค้า')?></th>
                        <th ><?= language('document/purchaseorder/purchaseorder', 'หน่วยสินค้า')?></th>
                        <th ><?= language('document/purchaseorder/purchaseorder', 'ต้นทุนเดิม')?></th>
                        <th><?= language('document/salepriceadj/salepriceadj','ผลต่าง')?></th>
                        <th ><?= language('document/purchaseorder/purchaseorder', 'ต้นทุนใหม่')?></th>
                        <th ><?= language('document/purchaseorder/purchaseorder', 'หมายเหตุ')?></th>
                        <?php if(@$tXphStaApv != 1 && @$tXphStaDoc != 3): ?>
                            <th class="xWDeleteBtnEditButton text-center xCNPIBeHideMQSS"><?= language('document/salepriceadj/salepriceadj','tPdtPriTBDelete')?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        //print_r($aPdtPriDataList['raItems']);
                    ?>
                    <?php if(isset($aPdtPriDataList['rtCode']) && $aPdtPriDataList['rtCode'] == 1 ):?>
                        <?php 
                            $nDummy = '0';
                            $nIndex = 1; 
                            $tItemData = '';
                            $tFCPdtCost = '';
                            $tFSPdtCost = '' ;
                            $tFTTmpRemark = '';
                        ?>
                        <?php foreach($aPdtPriDataList['raItems'] as $DataTableKey => $DataTableVal): ?>
                            <?php 
                                //print_r($DataTableVal);
                                $aAllpunCode    = (explode(",",$DataTableVal['FTAllPunCode']));
                                $aAllpunName    = (explode(",",$DataTableVal['FTAllPunName']));  
                                
                               
                                if ($DataTableVal){
                                    if(@$DataTableVal['FCPdtCostStd']){
                                        if($DataTableVal['FCPdtCostStd'] == '' || $DataTableVal['FCPdtCostStd'] == NULL){
                                            $tFCPdtCost = number_format($nDummy,2);
                                            $tFSPdtCost = '0';
                                        }else{
                                            $tFCPdtCost = number_format($DataTableVal['FCPdtCostStd'],2);
                                            $tFSPdtCost = $DataTableVal['FCPdtCostStd'];
                                        }
                                    } 
                                    
                                    if(@$DataTableVal['FCPdtCostEx']){
                                        if($DataTableVal['FCPdtCostEx'] == '' || $DataTableVal['FCPdtCostEx'] == NULL){
                                            $tFCPdtCost = number_format($nDummy,2);
                                            $tFSPdtCost = '0';
                                        }else{
                                            $tFCPdtCost = number_format($DataTableVal['FCPdtCostEx'],2);
                                            $tFSPdtCost = $DataTableVal['FCPdtCostEx'];
                                        }
                                        
                                    }                                 
                                }
                               
                            
                               
                
                                if($DataTableVal['FCXcdDiff'] == '' || $DataTableVal['FCXcdDiff'] == null){
                                    $DataTableVal['FCXcdDiff'] = number_format($nDummy,2);
                                }else{
                                    $DataTableVal['FCXcdDiff'] = number_format($DataTableVal['FCXcdDiff'],2);
                                }
                
                                if($DataTableVal['FCXcdCostNew'] == ''|| $DataTableVal['FCXcdCostNew'] == null){
                                    $DataTableVal['FCXcdCostNew'] = '';
                                }else{
                                    $DataTableVal['FCXcdCostNew'] = number_format($DataTableVal['FCXcdCostNew'],2);
                                }
                
                                if($DataTableVal['FTPdtName'] == ''|| $DataTableVal['FTPdtName'] == null){
                                    $DataTableVal['FTPdtName'] = '';
                                }
                
                                if($DataTableVal['FTPunName'] == ''|| $DataTableVal['FTPunName'] == null){
                                    $DataTableVal['FTPunName'] = '';
                                }
                
                                if($DataTableVal['FTPunCode'] == ''|| $DataTableVal['FTPunCode'] == null){
                                    $DataTableVal['FTPunCode'] = '';
                                }
                
                                if($DataTableVal['FCXcdFactor'] == ''|| $DataTableVal['FCXcdFactor'] == null){
                                    $DataTableVal['FCXcdFactor'] = 0;
                                }
                            ?>
                            <tr class="text-center xCNTextDetail2 otrSpaPdtPri xWPdtItem" 
                                id="otrSpaPdtPri<?=$DataTableVal['FNXtdSeqNo']?>" 
                                name="otrSpaPdtPri" 
                                data-pdt="<?=$DataTableVal['FTPdtCode']?>"
                                data-doc="<?=$DataTableVal['FTXthDocNo']?>" 
                                data-code="<?=$DataTableVal['FTPdtCode']?>" 
                                data-pun="<?=$DataTableVal['FTPunCode']?>" 
                                data-name="<?=$DataTableVal['FTPdtName']?>"
                                data-seq="<?=$DataTableVal['FNXtdSeqNo']?>"
                                data-status="<?=$DataTableVal['FTTmpStatus']?>"
                                data-rmk="<?=$DataTableVal['FTTmpRemark']?>" 
                                data-page="<?=$nCurrentPage?>"
                            >
                                <td nowrap class="text-center otdListItem">
                                    <label class="fancy-checkbox">
                                        <input id="ocbListItem<?=$DataTableVal['FNXtdSeqNo']?>" type="checkbox" class="ocbListItem" name="ocbListItem[]">
                                        <span class="ospListItem">&nbsp;</span>
                                    </label>
                                    <input type="hidden" id="ohdFTPunCode<?=$DataTableVal['FNXtdSeqNo']?>" name="ohdFTPunCode<?=$DataTableVal['FNXtdSeqNo']?>" value="<?=$DataTableVal['FTPunCode']?>">
                                    <input type="hidden" id="ohdFTXpdShpTo<?=$DataTableVal['FNXtdSeqNo']?>" name="ohdFTXpdShpTo<?=$DataTableVal['FNXtdSeqNo']?>" value="<?=$DataTableVal['FTXtdShpTo']?>">
                                    <input type="hidden" id="ohdFTXpdBchTo<?=$DataTableVal['FNXtdSeqNo']?>" name="ohdFTXpdBchTo<?=$DataTableVal['FNXtdSeqNo']?>" value="<?=$DataTableVal['FTXtdBchTo']?>">
                                </td>
                                <td><?=($DataTableKey+1)?></td>
                                <td class="text-left" ><label class="text-left xCNPdtFont xWShowValueFTPdtCode<?php echo $DataTableVal['FNXtdSeqNo']?>"><?php echo $DataTableVal['FTPdtCode']?></label></td>
                                <td class="text-left" ><label class="text-left xCNPdtFont xWShowValueFTPdtName <?php echo $DataTableVal['FNXtdSeqNo']?>"><?php echo $DataTableVal['FTPdtName']?></label></td>
                                <td class="text-left" >
                                        <label class="text-right xCNPdtFont xWShowValuePuncode <?php echo $DataTableVal['FNXtdSeqNo']?>"><?php echo $DataTableVal['FTPunName']?>
                                </td>
                                <td class="text-right">
                                    <?php echo $tFCPdtCost;?>
                                </td>
                                <td nowrap class="text-right xCNPdtInLine" >
                                    
                                </td>
                                <td class="xCNPdtEditInLine">
                                    <div class="xWEditInLine">
                                    <input type="hidden" id="Diff<?=$DataTableVal['FNXtdSeqNo']?>" value="">
                                    <input style="    
                                                background: rgb(249, 249, 249);
                                                box-shadow: 0px 0px 0px inset;
                                                border-top: 0px !important;
                                                border-left: 0px !important;
                                                border-right: 0px !important;
                                                padding: 0px;
                                                text-align: right;
                                            " 
                                            type="text" 
                                            class="form-control xStaDocEdit xWValueEditInLine1 xCNInputNumericWithDecimal text-right" 
                                            id="ohdFCXtdPriceRet<?=$DataTableVal['FNXtdSeqNo']?>" 
                                            name="ohdFCXtdPriceRet<?=$DataTableVal['FNXtdSeqNo']?>" 
                                            maxlength="11" 
                                            value="<?=$DataTableVal['FCXcdCostNew']?>" 
                                            autocomplete="off" 
                                            seq="<?=$DataTableVal['FNXtdSeqNo']?>" 
                                            columname="FCXtdVatRate" 
                                            col-validate=""                          
                                            page="<?=$nPage?>"
                                            b4value="<?= $tFSPdtCost ?>" 
                                            onkeypress=" if(event.keyCode==13 ){     event.preventDefault(); return JSxSpaSaveInLine(event,this); } " 
                                            onfocusout="JSxSpaSaveInLine(event,this)"
                                            onblur = "JSxADCCostDiff(this,<?= $tFSPdtCost ?>,<?=$DataTableVal['FNXtdSeqNo']?>)">
                                    </div>
                                </td>
                                <td class="text-left xWRemark1" style="color: red !important;"> <?php echo $DataTableVal['FTTmpRemark']; ?> </td>
                                <?php if(@$tXphStaApv != 1 && @$tXphStaDoc != 3): ?>
                                    <td nowrap class="text-center xWInLine xCNPIBeHideMQSS">
                                        <label class="xCNTextLink xWLabelInLine">
                                            <img class="xCNIconTable xCNDeleteInLineClick" data-seq="<?=$DataTableVal['FNXtdSeqNo']?>" src="<?=base_url().'/application/modules/common/assets/images/icons/delete.png'?>" title="Remove">
                                        </label>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php $nIndex++ ?>
                        <?php endforeach; ?>
                    <?php else:?>
                        <tr><td class='text-center xCNTextDetail2 xWTextNotfoundDataSalePriceAdj' colspan='100%'><?= language('common/main/main','tCMNNotFoundData')?></td></tr>
                    <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <p><?= language('common/main/main','tResultTotalRecord')?> <?=$aPdtPriDataList['rnAllRow']?> <?= language('common/main/main','tRecord')?> <?= language('common/main/main','tCurrentPage')?> <?=$aPdtPriDataList['rnCurrentPage']?> / <?=$aPdtPriDataList['rnAllPage']?></p>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="xWPageSpaPdt btn-toolbar pull-right">
            <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
            <button onclick="JSvSpaPdtClickPage('previous')" class="btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
                <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
            </button>
            <?php for($i=max($nPage-2, 1); $i<=max(0, min($aPdtPriDataList['rnAllPage'],$nPage+2)); $i++){?> <!-- เปลี่ยนชื่อ Parameter Loop เป็นของเรื่องนั้นๆ --> 
                <?php 
                    if($nPage == $i){ 
                        $tActive = 'active'; 
                        $tDisPageNumber = 'disabled';
                    }else{ 
                        $tActive = '';
                        $tDisPageNumber = '';
                    }
                ?>
                <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                <button onclick="JSvSpaPdtClickPage('<?php echo $i?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
            <?php } ?>
            <?php if($nPage >= $aPdtPriDataList['rnAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
            <button onclick="JSvSpaPdtClickPage('next')" class="btn btn-white btn-sm" <?php echo $tDisabledRight ?>> <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
            </button>
        </div>
    </div>
</div>
<!-- Modal Delete Items -->
<div class="modal fade" id="odvModalDelAdPdtPri">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?=language('common/main/main', 'tModalDelete')?></label>
			</div>
			<div class="modal-body">
				<span id="ospConfirmDelete"> - </span>
                <input type='hidden' id="ohdConfirmSeqDelete">
				<input type='hidden' id="ohdConfirmPdtDelete">
                <input type='hidden' id="ohdConfirmPunDelete">
                <input type='hidden' id="ohdConfirmDocDelete">
			</div>
			<div class="modal-footer">
				<button id="osmConfirm" type="button" class="btn xCNBTNPrimery" onClick="JSoAdPdtPriDelChoose('<?=$nCurrentPage?>')"><?=language('common/main/main', 'tModalConfirm')?></button>
        		<button type="button" class="btn xCNBTNDefult" data-dismiss="modal"><?=language('common/main/main', 'tModalCancel')?></button>
			</div>
		</div>
	</div>
</div>
<!-- Modal Show Original Price -->
<div class="modal fade" id="odvModalOriginalPrice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="xCNTextModalHeard" id="exampleModalLabel"><?= language('document/salepriceadj/salepriceadj','tPdtPriTiTleOrnPri')?></label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="odvDetailOriginalPrice">
                ...
            </div>
        </div>
    </div>
</div>
<!-- Modal Show Column -->
<div class="modal fade" id="odvShowOrderColumn" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="xCNTextModalHeard" id="exampleModalLabel"><?= language('common/main/main','tModalAdvTable')?></label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="odvOderDetailShowColumn">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= language('common/main/main','tModalAdvClose')?></button>
                <button type="button" class="btn btn-primary" onclick="JSxSaveColumnShow()"><?= language('common/main/main','tModalAdvSave')?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    //เปลี่ยนข้อความใน Table
    var nXphStaAdj  = $('#ocmXphStaAdj').val();
    if(nXphStaAdj == 1){
        var tTextTHInTable = "ราคาขาย";
    }else if(nXphStaAdj == 2){
        var tTextTHInTable = "ปรับลด %";
    }else if(nXphStaAdj == 3){
        var tTextTHInTable = "ปรับลด มูลค่า";
    }else if(nXphStaAdj == 4){
        var tTextTHInTable = "ปรับเพิ่ม %";
    }else if(nXphStaAdj == 5){
        var tTextTHInTable = "ปรับเพิ่ม มูลค่า";
    }else{
        var tTextTHInTable = "ราคาขาย";
    }
    $('.xCNPriceRetInAdjPrice').text(tTextTHInTable);

    /**  Check Box All Table */
    $('#ocbCheckAll').click(function(){
        if($(this).is(':checked')==true){
            $('.ocbListItem').prop('checked',true);
            $("#odvMngTableList #oliBtnDeleteAll").removeClass("disabled");
        }else{
            $('.ocbListItem').prop('checked',false);
            $("#odvMngTableList #oliBtnDeleteAll").addClass("disabled");
        }
    });

    // Functionality : หาผลต่างต้นทุน
    // Parameter : function parameters
    // Create : 25/02/2021 Sooksanti(Nont)
    // Return : -
    // Return Type : -
    function JSxADCCostDiff(tThis,tCostOld,Seq) {
        var tCostNew = $(tThis).val() - tCostOld
        if(isNaN(tCostNew)){
            tCostNew = 0;    
        }
        $('#Diff'+Seq).attr('value',tCostNew)
        $(tThis).parents("tr").find(".xCNPdtInLine").html(tCostNew.toFixed(2));
        // if ($(tThis).parents("tr").find(".xCNPdtEditInLine").val() != '') {
        //     var tCostOld = parseFloat($(tThis).parents("tr").find("td").text());
        //     var tCostNew = parseFloat($(tThis).parents("tr").find(".xCNPdtEditInLine").val());
        
        // }else{
        //     parseFloat($(tThis).parents("tr").find(".xCNPdtEditInLine").val(''));
        //     $(tThis).parents("tr").find("td:eq(7)").html((0).toFixed(2));
        // }
    }

    function FSxSPASelectAll(){
        if($('.ocbListItemAll').is(":checked")){
            $('.ocbListItem').each(function (e) { 
                if(!$(this).is(":checked")){
                    $(this).on( "click", FSxSPASelectMulDel(this) );
                }
            });
        }else{
            $('.ocbListItem').each(function (e) { 
                if($(this).is(":checked")){
                    $(this).on( "click", FSxSPASelectMulDel(this) );
                }
            });
        }
    }

    $('.xCNDeleteInLineClick').off('click');
    $('.xCNDeleteInLineClick').on('click',function(){
        var nSeq  = $(this).data('seq');
        var nPage = $('#otrSpaPdtPri'+nSeq).data('page');
        var tDoc  = $('#otrSpaPdtPri'+nSeq).data('doc');
        var tPdt  = $('#otrSpaPdtPri'+nSeq).data('code');
        var tPun  = $('#otrSpaPdtPri'+nSeq).data('pun');
        var tSta  = $('#otrSpaPdtPri'+nSeq).data('status');
        var tName = $('#otrSpaPdtPri'+nSeq).data('name');
        JSoAdPdtPriDel(nPage,tDoc,tPdt,tPun,nSeq,tSta,tName);
    });

    if($('#ohdXphStaApv').val()==1){
        if($("#ocmXphDocType").val() != '4'){
            $('.xWSelectDis').prop('disabled',true);
            $('.xStaDocEdit').prop('disabled',true);
        }
    }

    if($('#oetStaDoc').val()==3){
        $('.xStaDocEdit').prop('disabled',true);
        $('.xWSelectDis').prop('disabled',true);
    }

    function JSxSPASetValueCommaOut(e){
        var tValueNext     = parseFloat($(e).val().replace(/,/g, ''));
        $(e).val(tValueNext);
        $(e).focus();
        $(e).select();
    }

    $('.xWOriginalPriceClick').click(function(){
        var elem = $(this).data('seq');
        JSxSPAShowOriginalPrice(elem);
    });

    $('ducument').ready(function(){
        JSxShowButtonChoose();
        var aArrayConvert   = [JSON.parse(localStorage.getItem("LocalItemData"))];
        var nlength         = $('#odvRGPList').children('tr').length;
        for($i=0; $i < nlength; $i++){
            var tDataCode   = $('#otrSpaPdtPri'+$i).data('seq');
            if(aArrayConvert == null || aArrayConvert == ''){
            }else{
                var aReturnRepeat = findObjectByKey(aArrayConvert[0],'tSeq',tDataCode);
                if(aReturnRepeat == 'Dupilcate'){
                    $('#ocbListItem'+$i).prop('checked', true);
                }else{ }
            }
        }

        $('.ocbListItem').click(function(){
            var tSeq = $(this).parent().parent().parent().data('seq'); // Pdt
            var tPdt = $(this).parent().parent().parent().data('code'); // Pdt
            var tDoc = $(this).parent().parent().parent().data('doc'); // Doc
            var tPun = $(this).parent().parent().parent().data('pun'); // Pun
            var tSta = $(this).parent().parent().parent().data('status'); // Pun

            $(this).prop('checked', true);
            var LocalItemData = localStorage.getItem("LocalItemData");
            var obj = [];
            if(LocalItemData){
                obj = JSON.parse(LocalItemData);
            }else{ }
            var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
            if(aArrayConvert == '' || aArrayConvert == null){
                obj.push({"tSeq": tSeq, "tPdt": tPdt, "tDoc": tDoc, "tPun": tPun, "tSta" : tSta });
                localStorage.setItem("LocalItemData",JSON.stringify(obj));
                JSxSpaPdtPriTextinModal();
            }else{
                var aReturnRepeat = findObjectByKey(aArrayConvert[0],'tSeq',tSeq);
                if(aReturnRepeat == 'None' ){ // ยังไม่ถูกเลือก
                    obj.push({"tSeq": tSeq, "tPdt": tPdt, "tDoc": tDoc, "tPun": tPun, "tSta" : tSta });
                    localStorage.setItem("LocalItemData",JSON.stringify(obj));
                    JSxSpaPdtPriTextinModal();
                }else if(aReturnRepeat == 'Dupilcate'){	// เคยเลือกไว้แล้ว
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
                    JSxSpaPdtPriTextinModal();
                }
            }
            JSxShowButtonChoose();
        });

    });

    // ลบรายการสินค้า
    function FSxSPASelectMulDel(ptElm){
        // $('#otbDODocPdtAdvTableList #odvTBodyDOPdtAdvTableList .ocbListItem').click(function(){
        var tSeq = $(ptElm).parents('.xWPdtItem').data('seq'); // Pdt
        var tPdt = $(ptElm).parents('.xWPdtItem').data('code'); // Pdt
        var tDoc = $(ptElm).parents('.xWPdtItem').data('doc'); // Doc
        var tPun = $(ptElm).parents('.xWPdtItem').data('pun'); // Pun
        var tSta = $(ptElm).parents('.xWPdtItem').data('status'); // Pun

        $(ptElm).prop('checked', true);
        var LocalItemData = localStorage.getItem("LocalItemData");
        var obj = [];
        if(LocalItemData){
            obj = JSON.parse(LocalItemData);
        }else{ }
        var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
        if(aArrayConvert == '' || aArrayConvert == null){
            obj.push({"tSeq": tSeq, "tPdt": tPdt, "tDoc": tDoc, "tPun": tPun, "tSta" : tSta });
            localStorage.setItem("LocalItemData",JSON.stringify(obj));
            JSxSpaPdtPriTextinModal();
        }else{
            var aReturnRepeat = findObjectByKey(aArrayConvert[0],'tSeq',tSeq);
            if(aReturnRepeat == 'None' ){ // ยังไม่ถูกเลือก
                obj.push({"tSeq": tSeq, "tPdt": tPdt, "tDoc": tDoc, "tPun": tPun, "tSta" : tSta });
                localStorage.setItem("LocalItemData",JSON.stringify(obj));
                JSxSpaPdtPriTextinModal();
            }else if(aReturnRepeat == 'Dupilcate'){	// เคยเลือกไว้แล้ว
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
                JSxSpaPdtPriTextinModal();
            }
        }
        JSxShowButtonChoose();
    }

    // Click รายการเปลี่ยน Page สินค้า
    function JSvSpaPdtClickPage(ptPage) {
        var nStaSession = 1;
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            var nPageCurrent = '';
            switch (ptPage) {
                case 'next': //กดปุ่ม Next
                    $('.xWBtnNext').addClass('disabled');
                    nPageOld        = $('.xWPageSpaPdt .active').text(); // Get เลขก่อนหน้า
                    nPageNew        = parseInt(nPageOld, 10) + 1; // +1 จำนวน
                    nPageCurrent    = nPageNew
                    break;
                case 'previous': //กดปุ่ม Previous
                    nPageOld = $('.xWPageSpaPdt .active').text(); // Get เลขก่อนหน้า
                    nPageNew = parseInt(nPageOld, 10) - 1; // -1 จำนวน
                    nPageCurrent    = nPageNew
                    break;
                default:
                    nPageCurrent    = ptPage
            }
            JCNxOpenLoading();
            JSvAdPdtPriDataTable(nPageCurrent);
        } else {
            JCNxShowMsgSessionExpired();
        }
    }




</script>
