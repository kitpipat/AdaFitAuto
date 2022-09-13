<?php
if ($aDataList['rtCode'] == '1') {
    $nCurrentPage = $aDataList['rnCurrentPage'];
} else {
    $nCurrentPage = '1';
}
?>
<style>
    .xWCptActive {
        color: #007b00 !important;
        font-weight: bold;
        font-size: 10px;
        cursor: default;
    }
    .xWCptInActive {
        color: #7b7f7b !important;
        font-weight: bold;
        cursor: default;
        font-size: 10px;
    }
    .xWTdDisable {
        cursor: not-allowed !important;
        opacity: 0.4 !important;
    }

    .xWImgDisable {
        pointer-events: none;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr class="xCNCenter">
                        <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) { ?>
                            <th nowrap class="xCNTextBold text-center" style="width:5%;">
                                <label class="fancy-checkbox">
                                    <input type="checkbox" class="ocmCENCheckDeleteAll ocbListItem" id="ocmCENCheckDeleteAll" >
                                    <span class="ospListItem">&nbsp;</span>
                                </label>
                            </th>
                        <?php } ?>
                        <th class="xCNTextBold"><?= language('document/transfer_branch_out/transfer_branch_out', 'tTBBchCreate') ?></th>
                        <th class="xCNTextBold"><?= language('document/transfer_branch_out/transfer_branch_out', 'tTBDocNo') ?></th>
                        <th class="xCNTextBold"><?= language('document/transfer_branch_out/transfer_branch_out', 'tTBDocDate') ?></th>
                        <th class="xCNTextBold"><?= language('document/transfer_branch_out/transfer_branch_out', 'tTBStaDoc') ?></th>
                        <!-- <th class="xCNTextBold"><?= language('document/transfer_branch_out/transfer_branch_out', 'tTBStaApv') ?></th> -->
                        <th class="xCNTextBold"><?= language('document/transfer_branch_out/transfer_branch_out', 'tTBStaPrc') ?></th>
                        <th class="xCNTextBold"><?= language('document/transfer_branch_out/transfer_branch_out', 'tTBCreateBy') ?></th>
                        <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) { ?>
                            <th class="xCNTextBold" style="width:5%;"><?= language('common/main/main', 'tCMNActionDelete') ?></th>
                        <?php } ?>
                        <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaRead'] == 1) { ?>
                            <th class="xCNTextBold" style="width:5%;"><?= language('common/main/main', 'tCMNActionEdit') ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody id="odvRGPList">
                    <?php if ($aDataList['rtCode'] == 1) { ?>
                        <?php foreach ($aDataList['raItems'] as $key => $aValue) { ?>
                            <?php
                            $tDocNo = $aValue['FTXthDocNo'];
                            if ($aValue['FTXthStaApv'] == 1 || $aValue['FTXthStaApv'] == 2 || $aValue['FTXthStaDoc'] == 3) {
                                $CheckboxDisabled = "disabled";
                                $ClassDisabled = 'xCNDocDisabled';
                                $Title = language('document/document/document', 'tDOCMsgCanNotDel');
                                $Onclick = '';
                            } else {
                                $CheckboxDisabled = "";
                                $ClassDisabled = '';
                                $Title = '';
                                $Onclick = "onclick=JSxTransferBchOutDocDel('" . $nCurrentPage . "','" . $tDocNo . "')";
                            }

                              if ($aValue['FTXthStaApv'] == 1 || $aValue['FTXthStaApv'] == 2 || $aValue['FTXthStaDoc'] == 3) {
                                $tDisableTD     = "xWTdDisable";
                                $tDisableImg    = "xWImgDisable";
                                $tDisabledItem  = "disabled ";
                                $tDisabledItem2  = "xCNDisabled ";
                                $tDisabledcheckrow  = "true";
                              }else{
                                $tDisableTD     = "";
                                $tDisableImg    = "";
                                $tDisabledItem  = "";
                                $tDisabledItem2  = " ";
                                $tDisabledcheckrow  = "false";
                              }
                            //FTXthStaDoc
                            if ($aValue['FTXthStaDoc'] == 3) {
                                $tClassStaDoc = 'text-danger';
                                $tStaDoc = language('common/main/main', 'tStaDoc3');
                            } else if ($aValue['FTXthStaApv'] == 1) {
                                $tClassStaDoc = 'text-success';
                                $tStaDoc = language('common/main/main', 'tStaDoc1');
                            } else {
                                $tClassStaDoc = 'text-warning';
                                $tStaDoc = language('common/main/main', 'tStaDoc');
                            }

                            if ($aValue['FTXthStaDoc'] == 3) {
                                $tClassPrcStk   = 'text-danger';
                                $tStaPrcDoc     = language('common/main/main', 'tStaDoc3');
                            }else{
                                if ($aValue['FTXthStaPrcStk'] == 1) {
                                    $tClassPrcStk   = 'text-success';
                                    $tStaPrcDoc     = language('common/main/main', 'tStaPrcDoc1');
                                } else if ($aValue['FTXthStaPrcStk'] == 2) {
                                    $tClassPrcStk   = 'text-warning';
                                    $tStaPrcDoc     = language('common/main/main', 'tStaPrcDoc2');
                                } else if ($aValue['FTXthStaPrcStk'] == 0 || $aValue['FTXthStaPrcStk'] == '') {
                                    $tClassPrcStk   = 'text-warning';
                                    $tStaPrcDoc     = language('common/main/main', 'tStaPrcDoc3');
                                }
                            }

                            ?>

                            <tr class="text-center xCNTextDetail2" id="otrTransferBchOutHD<?= $key ?>" data-code="<?= $aValue['FTXthDocNo'] ?>" data-name="<?= $aValue['FTXthDocNo'] ?>">
                                <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) { ?>
                                  <td class="text-center">
                                    <label class="fancy-checkbox">
                                      <input id="ocbListItem<?php echo $key; ?>" type="checkbox"
                                      <?php echo $tDisabledItem; ?>
                                      data-checkrow="<?php echo $tDisabledcheckrow; ?>"
                                      data-checkrowid="<?php echo $aValue['FTXthDocNo'].$aValue['FTBchCode']?>" checked="false"  class="ocbListItem" name="ocbListItem[]">
                                      <span class="<?php echo $tDisabledItem2; ?>">&nbsp;</span>
                                    </label>
                                  </td>
                                <?php } ?>

                                <td class="text-left"><?= $aValue['FTBchName'] != '' ? $aValue['FTBchName'] : '-' ?></td>
                                <td class="text-left"><?= $aValue['FTXthDocNo'] != '' ? $aValue['FTXthDocNo'] : '-' ?></td>
                                <td class="text-center"><?= $aValue['FDXthDocDate'] != '' ? date('d/m/Y', strtotime($aValue['FDXthDocDate'])) : '-' ?></td>
                                <td class="text-left"><label class="xCNTDTextStatus <?= $tClassStaDoc ?>"><?php echo $tStaDoc ?></label></td>
                                <!-- <td class="text-left"><label class="xCNTDTextStatus <?= $tClassStaApv ?>"><?= language('document/transfer_branch_out/transfer_branch_out', 'tStaApv' . $aValue['FTXthStaApv']) ?></label></td> -->
                                <td class="text-left"><label class="xCNTDTextStatus <?= $tClassPrcStk ?>"><?php echo $tStaPrcDoc ?></label></td>
                                <td class="text-left"><?= $aValue['FTCreateByName'] != '' ? $aValue['FTCreateByName'] : '-' ?></td>
                                <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) { ?>

                                    <td class="<?=$tDisableTD?>" id="otdDel<?php echo $aValue['FTXthDocNo'].$aValue['FTBchCode']?>">
                                        <img id="oimDel<?php echo $aValue['FTXthDocNo'].$aValue['FTBchCode']; ?>"
                                        class="xCNIconTable <?php echo $tDisableImg; ?>"
                                         src="<?php echo  base_url().'/application/modules/common/assets/images/icons/delete.png'?>"
                                         <?= $Onclick ?> title="<?= $Title ?>">
                                    </td>
                                <?php } ?>
                                <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaRead'] == 1) { ?>
                                    <td>
                                        <?php if($aValue['FTXthStaApv'] == 1 || $aValue['FTXthStaDoc'] == 3){ ?>
                                            <img class="xCNIconTable" style="width: 17px;" src="<?php echo  base_url().'/application/modules/common/assets/images/icons/view2.png'?>" onClick="JSvTransferBchOutCallPageEdit('<?php echo $aValue['FTXthDocNo']?>')">
                                        <?php }else{ ?>
                                            <img class="xCNIconTable" src="<?php echo  base_url().'/application/modules/common/assets/images/icons/edit.png'?>" onClick="JSvTransferBchOutCallPageEdit('<?php echo $aValue['FTXthDocNo']?>')">
                                        <?php } ?>
                                        <!-- <img class="xCNIconTable" src="<?= base_url('application/modules/common/assets/images/icons/edit.png') ?>" onClick="JSvTransferBchOutCallPageEdit('<?= $aValue['FTXthDocNo'] ?>')"> -->
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class='text-center xCNTextDetail2' colspan='100%'><?= language('common/main/main', 'tCMNNotFoundData') ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?php $nShowRecord = get_cookie('nShowRecordInPageList'); ?>
        <p>แสดงข้อมูลรายการล่าสุด <?=$nShowRecord?> รายการ</p>    </div>
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
                <button id="osmConfirm" onClick="JSxTransferBchOutDelChoose()" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button">
                    <?= language('common/main/main', 'tModalConfirm') ?>
                </button>
                <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button" data-dismiss="modal">
                    <?= language('common/main/main', 'tModalCancel') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$('ducument').ready(function(){
    localStorage.removeItem("LocalItemData");
    JSxShowButtonChoose();
    $('.ocbListItem').prop('checked',false);
  //  JSxPaseCodeDelInModal();// เอารายการที่เลือกไว้มาเก็บไว้ในmodal ให้เรียบร้อยเผื่อ user สั่งลบรายการ
})
    $('.ocbListItem').click(function() {
      //console.log('asdasdadadads');
        var nCode = $(this).parent().parent().parent().data('code'); // code
        var tName = $(this).parent().parent().parent().data('name'); // code
        $(this).prop('checked', true);
        var LocalTransferBchOutHDItemData = localStorage.getItem("LocalTransferBchOutHDItemData");
        var obj = [];
        if (LocalTransferBchOutHDItemData) {
            obj = JSON.parse(LocalTransferBchOutHDItemData);
        } else {}
        var aArrayConvert = [JSON.parse(localStorage.getItem("LocalTransferBchOutHDItemData"))];
        if (aArrayConvert == '' || aArrayConvert == null) {
            obj.push({
                "nCode": nCode,
                "tName": tName
            });
            localStorage.setItem("LocalTransferBchOutHDItemData", JSON.stringify(obj));
            JSxTextinModal();
        } else {
            var aReturnRepeat = findObjectByKey(aArrayConvert[0], 'nCode', nCode);
            if (aReturnRepeat == 'None') { // ยังไม่ถูกเลือก
                obj.push({
                    "nCode": nCode,
                    "tName": tName
                });
                localStorage.setItem("LocalTransferBchOutHDItemData", JSON.stringify(obj));
                JSxTextinModal();
            } else if (aReturnRepeat == 'Dupilcate') { // เคยเลือกไว้แล้ว
                localStorage.removeItem("LocalTransferBchOutHDItemData");
                $(this).prop('checked', false);
                var nLength = aArrayConvert[0].length;
                for ($i = 0; $i < nLength; $i++) {
                    if (aArrayConvert[0][$i].nCode == nCode) {
                        delete aArrayConvert[0][$i];
                    }
                }
                var aNewarraydata = [];
                for ($i = 0; $i < nLength; $i++) {
                    if (aArrayConvert[0][$i] != undefined) {
                        aNewarraydata.push(aArrayConvert[0][$i]);
                    }
                }
                localStorage.setItem("LocalTransferBchOutHDItemData", JSON.stringify(aNewarraydata));
                JSxTextinModal();
            }
        }
        JSxShowButtonChoose();
    })
</script>
