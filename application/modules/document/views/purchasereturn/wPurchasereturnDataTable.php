<?php 
    if($aDataList['rtCode'] == '1'){
        $nCurrentPage = $aDataList['rnCurrentPage'];
    }else{
        $nCurrentPage = '1';
    }
?>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr class="xCNCenter">
                        <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) : ?>
                            <th nowrap class="xCNTextBold text-center" style="width:5%;">
                                <label class="fancy-checkbox">
                                    <input type="checkbox" class="ocmCENCheckDeleteAll" id="ocmCENCheckDeleteAll" >
                                    <span class="ospListItem">&nbsp;</span>
                                </label>
                            </th>
                        <?php endif; ?>
                        <th class="xCNTextBold"><?= language('document/purchasereturn/purchasereturn', 'tPNTBBchCreate') ?></th>
                        <th class="xCNTextBold"><?= language('document/purchasereturn/purchasereturn', 'tPNTBDocNo') ?></th>
                        <th class="xCNTextBold"><?= language('document/purchasereturn/purchasereturn', 'tPNTBDocDate') ?></th>
                        <th class="xCNTextBold"><?= language('document/purchasereturn/purchasereturn', 'tPNRefInt') ?></th>
                        <th class="xCNTextBold"><?= language('document/purchasereturn/purchasereturn', 'tPNRefIntDate') ?></th>
                        <th class="xCNTextBold"><?= language('document/purchasereturn/purchasereturn', 'tPNTBStaDoc') ?></th>
                        <th class="xCNTextBold"><?= language('document/purchasereturn/purchasereturn', 'tPNTBCreateBy') ?></th>
                        <th class="xCNTextBold"><?= language('document/purchasereturn/purchasereturn', 'tPNTBApvBy') ?></th>
                        <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) : ?>
                            <th class="xCNTextBold" style="width:5%;"><?= language('common/main/main', 'tCMNActionDelete') ?></th>
                        <?php endif; ?>
                        <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaRead'] == 1) : ?>
                            <th class="xCNTextBold" style="width:5%;"><?= language('common/main/main', 'tCMNActionEdit') ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="odvRGPList">
                <?php if($aDataList['rtCode'] == 1 ) : ?>
                    <?php if(!empty($aDataList['raItems'])) { ?>
                        <?php foreach($aDataList['raItems'] AS $key=>$aValue) { ?>
                            <?php 
                                $tXthDocNo = $aValue['FTXphDocNo'];
                                if(!empty($aValue['FTXphStaApv']) || $aValue['FTXphStaDoc'] == 3){
                                    $CheckboxDisabled = "disabled";
                                    $ClassDisabled = 'xCNDocDisabled';
                                    $Title = language('document/document/document','tDOCMsgCanNotDel');
                                    $Onclick = '';
                                }else{
                                    $CheckboxDisabled = "";
                                    $ClassDisabled = '';
                                    $Title = '';
                                    $Onclick = "onclick=JSnPNDel('".$nCurrentPage."','".$tXthDocNo."')";
                                }

                                //FTXphStaDoc
                                if ($aValue['FTXphStaDoc'] == 3) {
                                    $tClassStaDoc = 'text-danger';
                                    $tStaDoc = language('common/main/main', 'tStaDoc3');
                                } else if ($aValue['FTXphStaApv'] == 1) {
                                    $tClassStaDoc = 'text-success';
                                    $tStaDoc = language('common/main/main', 'tStaDoc1');
                                } else {
                                    $tClassStaDoc = 'text-warning';
                                    $tStaDoc = language('common/main/main', 'tStaDoc');
                                }

                                //FTXphStaPrcStk
                                if ($aValue['FTXphStaPrcStk'] == 1) {
                                    $tClassPrcStk = 'text-success';
                                    $tStaPrcDoc = language('common/main/main', 'tStaPrcDoc1');
                                } else if ($aValue['FTXphStaPrcStk'] == 2) {
                                    $tClassPrcStk = 'text-warning';
                                    $tStaPrcDoc = language('common/main/main', 'tStaPrcDoc3');
                                } else if ($aValue['FTXphStaPrcStk'] == 0 || $aValue['FTXphStaPrcStk'] == '') {
                                    $tClassPrcStk = 'text-warning';
                                    $tStaPrcDoc = language('common/main/main', 'tStaPrcDoc3');
                                }
                            ?>
                            <tr class="text-center xCNTextDetail2 otrPurchaseoeder" id="otrPurchaseoeder<?=$key?>" data-code="<?=$aValue['FTXphDocNo']?>" data-name="<?=$aValue['FTXphDocNo']?>">
                                <?php if($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) : ?>
                                    <td class="text-center">
                                        <label class="fancy-checkbox ">
                                            <input id="ocbListItem<?=$key?>" type="checkbox" class="ocbListItem" name="ocbListItem[]" <?=$CheckboxDisabled?>>
                                            <span class="<?=$ClassDisabled?>">&nbsp;</span>
                                        </label>
                                    </td>
                                <?php endif; ?>
                                <td class="text-left"><?php echo $aValue['FTBchName'] != '' ? $aValue['FTBchName'] : '-' ?></td>
                                <td class="text-left"><?php echo $aValue['FTXphDocNo'] != '' ? $aValue['FTXphDocNo'] : '-' ?></td>
                                <td class="text-center"><?php echo $aValue['FDXphDocDate'] != '' ? $aValue['FDXphDocDate'] : '-' ?></td>
                                <td class="text-left"><?php echo $aValue['FTXphRefInt'] != '' ? $aValue['FTXphRefInt'] : '-' ?></td>
                                <td class="text-center"><?php echo $aValue['FDXphRefIntDate'] != '' ? $aValue['FDXphRefIntDate'] : '-' ?></td>
                                <td class="text-left"><label class="xCNTDTextStatus <?=$tClassStaDoc?>"><?php echo $tStaDoc ?></label></td>
                                <td class="text-left"><?php echo $aValue['FTCreateByName'] != '' ? $aValue['FTCreateByName'] : '-' ?></td>
                                <td class="text-left"><?php echo $aValue['FTXphApvName'] != '' ? $aValue['FTXphApvName'] : '-' ?></td> 
                                <?php if($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) { ?>
                                    <td>
                                        <img class="xCNIconTable xCNIconDel <?=$ClassDisabled?>" src="<?php echo  base_url().'/application/modules/common/assets/images/icons/delete.png'?>" <?=$Onclick?> title="<?=$Title?>">
                                    </td>
                                <?php } ?>
                                
                                <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaRead'] == 1) : ?>
                                <td>
                                    <?php if ($aValue['FTXphStaPrcStk'] == 1 || $aValue['FTXphStaDoc'] == 3) { ?>
                                        <img class="xCNIconTable" style="width: 17px;" src="<?= base_url() . '/application/modules/common/assets/images/icons/view2.png' ?>" onClick="JSvCallPagePNEdit('<?= $aValue['FTXphDocNo'] ?>')">
                                    <?php } else { ?>
                                        <img class="xCNIconTable" src="<?= base_url() . '/application/modules/common/assets/images/icons/edit.png' ?>" onClick="JSvCallPagePNEdit('<?= $aValue['FTXphDocNo'] ?>')">
                                    <?php } ?>
                                </td>
                            <?php endif; ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                <?php else:?>
                    <tr><td class='text-center xCNTextDetail2' colspan='100%'><?= language('common/main/main','tCMNNotFoundData')?></td></tr>
                <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <!-- ????????????????????? -->
    <div class="col-md-6">
        <p><?= language('common/main/main','tResultTotalRecord')?> <?=$aDataList['rnAllRow']?> <?= language('common/main/main','tRecord')?> <?= language('common/main/main','tCurrentPage')?> <?=$aDataList['rnCurrentPage']?> / <?=$aDataList['rnAllPage']?></p>
    </div>
    <!-- ????????????????????? -->
    <div class="col-md-6">
        <div class="xWPage btn-toolbar pull-right"> <!-- ????????????????????????????????? Class ?????????????????????????????????????????????????????? --> 
            <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
            <button onclick="JSvPNClickPage('previous')" class="btn btn-white btn-sm" <?php echo $tDisabledLeft ?>> <!-- ????????????????????????????????? Onclick ?????????????????????????????????????????????????????? --> 
                <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
            </button>
            <?php for($i=max($nPage-2, 1); $i<=max(0, min($aDataList['rnAllPage'],$nPage+2)); $i++){?> <!-- ????????????????????????????????? Parameter Loop ?????????????????????????????????????????????????????? --> 
                <?php 
                    if($nPage == $i){ 
                        $tActive = 'active'; 
                        $tDisPageNumber = 'disabled';
                    }else{ 
                        $tActive = '';
                        $tDisPageNumber = '';
                    }
                ?>
                <!-- ????????????????????????????????? Onclick ?????????????????????????????????????????????????????? --> 
                <button onclick="JSvPNClickPage('<?php echo $i?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
            <?php } ?>
            <?php if($nPage >= $aDataList['rnAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
            <button onclick="JSvPNClickPage('next')" class="btn btn-white btn-sm" <?php echo $tDisabledRight ?>> <!-- ????????????????????????????????? Onclick ?????????????????????????????????????????????????????? --> 
                <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="odvModalDel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header xCNModalHead">
                <label class="xCNTextModalHeard"><?= language('common/main/main', 'tModalDelete') ?></label>
            </div>
            <div class="modal-body">
                <span id="ospConfirmDelete"> - </span>
                <input type='hidden' id="ohdConfirmIDDelete">
            </div>
            <div class="modal-footer">
                <!-- ????????? -->
                <button id="osmConfirm" onClick="JSnPNDelChoose()" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button">
                    <?php echo language('common/main/main', 'tModalConfirm') ?>
                </button>
                <!-- ????????? -->
                <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button" data-dismiss="modal">
                    <?php echo language('common/main/main', 'tModalCancel') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.ocbListItem').click(function(){
        var nCode = $(this).parent().parent().parent().data('code');  //code
        var tName = $(this).parent().parent().parent().data('name');  //code
        $(this).prop('checked', true);
        var LocalItemData = localStorage.getItem("LocalItemData");
        var obj = [];
        if(LocalItemData){
            obj = JSON.parse(LocalItemData);
        }else{ }
        var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
        if(aArrayConvert == '' || aArrayConvert == null){
            obj.push({"nCode": nCode, "tName": tName });
            localStorage.setItem("LocalItemData",JSON.stringify(obj));
            JSxTextinModal();
        }else{
            var aReturnRepeat = findObjectByKey(aArrayConvert[0],'nCode',nCode);
            if(aReturnRepeat == 'None' ){           //??????????????????????????????????????????
                obj.push({"nCode": nCode, "tName": tName });
                localStorage.setItem("LocalItemData",JSON.stringify(obj));
                JSxTextinModal();
            }else if(aReturnRepeat == 'Dupilcate'){	//?????????????????????????????????????????????
                localStorage.removeItem("LocalItemData");
                $(this).prop('checked', false);
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
                JSxTextinModal();
            }
        }
        JSxShowButtonChoose();
    });
</script>






















