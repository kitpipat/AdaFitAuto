<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) { ?>
                            <th nowrap class="xCNTextBold text-center" style="width:5%;">
                                <label class="fancy-checkbox">
                                    <input type="checkbox" class="ocmCENCheckDeleteAll" id="ocmCENCheckDeleteAll" >
                                    <span class="ospListItem">&nbsp;</span>
                                </label>
                            </th>
                        <?php } ?>
                        <th nowrap class="xCNTextBold text-center" style="width:20%;"><?php echo language('document/card/cardstatus', 'tCardShiftStatusTBBranchCode'); ?></th>
                        <th nowrap class="xCNTextBold text-center" style="width:20%;"><?php echo language('document/card/cardstatus', 'tCardShiftStatusTBDocNo'); ?></th>
                        <th nowrap class="xCNTextBold text-center" style="width:10%;"><?php echo language('document/card/cardstatus', 'tCardShiftStatusTBDocDate'); ?></th>
                        <th nowrap class="xCNTextBold text-center" style="width:10%;"><?php echo language('document/card/cardstatus', 'tCardShiftStatusTBCardNumber'); ?></th>
                        <th nowrap class="xCNTextBold text-center" style="width:10%;"><?php echo language('document/card/cardstatus', 'tCardShiftStatusTBDocStatus'); ?></th>
                        <!-- <th nowrap class="xCNTextBold text-left" style="width:10%;"><?php echo language('common/main/main', 'tStaPrcDocTitle'); ?></th> -->
                        <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) { ?>
                            <th class="xCNTextBold text-center" style="width:5%;"><?= language('common/main/main', 'tCMNActionDelete') ?></th>
                        <?php } ?>
                        <?php if ($aAlwEvent['tAutStaFull'] == 1 || ($aAlwEvent['tAutStaEdit'] == 1 || $aAlwEvent['tAutStaRead'] == 1)) : ?>
                            <th nowrap class="xCNTextBold text-center" style="width:5%;"><?php echo language('document/card/cardstatus', 'tCardShiftStatusTBEdit'); ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="odvRGPList">
                    <?php if ($aDataList['rtCode'] == 1) : ?>
                        <?php foreach ($aDataList['raItems'] as $key => $aValue) : ?>
                            <?php
                            $nCurrentPage = $aDataList['rnCurrentPage'];
                            $bIsApvOrCancel = ($aValue['rtCardShiftStatusCvhStaPrcDoc'] == 1 || $aValue['rtCardShiftStatusCvhStaPrcDoc'] == 2) || ($aValue['rtCardShiftStatusCvhStaDoc'] == 3);

                            if ($bIsApvOrCancel) {
                                $CheckboxDisabled = "disabled";
                                $ClassDisabled = 'xCNDocDisabled';
                                $Title = language('document/document/document', 'tDOCMsgCanNotDel');
                                $Onclick = '';
                            } else {
                                $CheckboxDisabled = "";
                                $ClassDisabled = '';
                                $Title = '';
                                $Onclick = "onclick=JSxCardShiftStatusDocDel('" . $nCurrentPage . "','" . $aValue['rtCardShiftStatusDocNo'] . "','" . $aValue['rtCardShiftStatusBchCode'] . "')";
                            }
                            ?>
                            <tr 
                            class="text-center xCNTextDetail2 otrCardShiftStatus"
                            data-doc-no="<?php echo $aValue['rtCardShiftStatusDocNo']; ?>"
                            data-bch-code="<?php echo $aValue['rtCardShiftStatusBchCode']; ?>">

                                <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) { ?>
                                    <td class="text-center">
                                        <label class="fancy-checkbox ">
                                            <input type="checkbox" class="ocbListItem" name="ocbListItem[]" <?= $CheckboxDisabled ?>>
                                            <span class="<?= $ClassDisabled ?>">&nbsp;</span>
                                        </label>
                                    </td>
                                <?php } ?>
                                <td nowrap class="text-left"><?php echo @$aValue['rtCardShiftStatusBchCode'] ?></td>
                                <td nowrap class="text-left"><?php echo @$aValue['rtCardShiftStatusDocNo']; ?></td>
                                <td nowrap class="text-center"><?php echo date('d/m/Y', strtotime(@$aValue['rtCardShiftStatusDocDate'])); ?></td>
                                <td nowrap class="text-left"><?php echo @number_format($aValue['rtCardShiftStatusCvhCardQty'], 0); ?></td>
                                <?php
                                $tCshStaDoc = "";
                                //StaDoc
                                if($aValue['rtCardShiftStatusCvhStaDoc'] == 3){
                                    $tClassStaDoc = 'text-danger';
                                    $tStaDoc = language('common/main/main', 'tStaDoc3');
                                }else if(!empty($aValue['rtCardShiftStatusCvhApvCode'])){
                                    $tClassStaDoc = 'text-success';
                                    $tStaDoc = language('common/main/main', 'tStaDoc1'); 
                                }else{
                                    $tClassStaDoc = 'text-warning';
                                    $tStaDoc = language('common/main/main', 'tStaDoc');
                                }
                                ?>
                                <td nowrap class="text-left"><label class="xCNTDTextStatus <?= $tClassStaDoc ?>"><?php echo $tStaDoc; ?></label></td>
                                <?php

                                // if ($aValue['rtCardShiftStatusCvhStaPrcDoc'] == 1) {
                                //     $tClassPrcStk = 'text-success';
                                //     $tStaPrcDoc = language('common/main/main', 'tStaPrcDoc1');
                                // } else if ($aValue['rtCardShiftStatusCvhStaPrcDoc'] == 2) {
                                //     $tClassPrcStk = 'text-warning';
                                //     $tStaPrcDoc = language('common/main/main', 'tStaPrcDoc3');
                                // } else if ($aValue['rtCardShiftStatusCvhStaPrcDoc'] == 0 || $aValue['rtCardShiftStatusCvhStaPrcDoc'] == '') {
                                //     $tClassPrcStk = 'text-warning';
                                //     $tStaPrcDoc = language('common/main/main', 'tStaPrcDoc3');
                                // }
                                ?>
                                <!-- <td nowrap class="text-left"><label class="xCNTDTextStatus <?= $tClassPrcStk ?>"><?php echo $tStaPrcDoc; ?></label></td> -->
                                <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) { ?>
                                    <td>
                                        <img class="xCNIconTable xCNIconDel <?= $ClassDisabled ?>" src="<?= base_url('application/modules/common/assets/images/icons/delete.png') ?>" title="<?= $Title ?>" <?= $Onclick ?>>
                                    </td>
                                <?php } ?>

                                <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaEdit'] == 1 || $aAlwEvent['tAutStaRead'] == 1) { ?>
                                    <td>
                                        <?php if($bIsApvOrCancel) { ?>
                                            <img class="xCNIconTable" style="width: 17px;" src="<?= base_url('application/modules/common/assets/images/icons/view2.png'); ?>" onClick="JSvCardShiftStatusCallPageCardShiftStatusEdit('<?= $aValue['rtCardShiftStatusDocNo'] ?>')">
                                        <?php }else{ ?>
                                            <img class="xCNIconTable" src="<?= base_url('application/modules/common/assets/images/icons/edit.png') ?>" onClick="JSvCardShiftStatusCallPageCardShiftStatusEdit('<?= $aValue['rtCardShiftStatusDocNo'] ?>')">
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td nowrap class='text-center xCNTextDetail2' colspan='100%'><?php echo language('common/main/main', 'tCMNNotFoundData'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <!-- ????????????????????? -->
    <div class="col-md-6">
        <p><?php echo language('common/main/main', 'tResultTotalRecord'); ?> <?= $aDataList['rnAllRow'] ?> <?php echo language('common/main/main', 'tRecord'); ?> <?php echo language('common/main/main', 'tCurrentPage'); ?> <?= $aDataList['rnCurrentPage'] ?> / <?= $aDataList['rnAllPage'] ?></p>
    </div>
    <!-- ????????????????????? -->
    <div class="col-md-6">
        <div class="xWPageCardShiftStatus btn-toolbar pull-right">
            <!-- ????????????????????????????????? Class ?????????????????????????????????????????????????????? -->
            <?php if ($nPage == 1) {
                $tDisabledLeft = 'disabled';
            } else {
                $tDisabledLeft = '-';
            } ?>
            <button onclick="JSvCardShiftStatusClickPage('previous')" class="btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
                <!-- ????????????????????????????????? Onclick ?????????????????????????????????????????????????????? -->
                <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
            </button>
            <?php for ($i = max($nPage - 2, 1); $i <= max(0, min($aDataList['rnAllPage'], $nPage + 2)); $i++) { ?>
                <!-- ????????????????????????????????? Parameter Loop ?????????????????????????????????????????????????????? -->
                <?php
                if ($nPage == $i) {
                    $tActive = 'active';
                    $tDisPageNumber = 'disabled';
                } else {
                    $tActive = '';
                    $tDisPageNumber = '';
                }
                ?>
                <!-- ????????????????????????????????? Onclick ?????????????????????????????????????????????????????? -->
                <button onclick="JSvCardShiftStatusClickPage('<?php echo $i ?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i ?></button>
            <?php } ?>
            <?php if ($nPage >= $aDataList['rnAllPage']) {
                $tDisabledRight = 'disabled';
            } else {
                $tDisabledRight = '-';
            } ?>
            <button onclick="JSvCardShiftStatusClickPage('next')" class="btn btn-white btn-sm" <?php echo $tDisabledRight ?>>
                <!-- ????????????????????????????????? Onclick ?????????????????????????????????????????????????????? -->
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
                <button id="osmConfirm" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button">
                    <?= language('common/main/main', 'tModalConfirm') ?>
                </button>
                <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button" data-dismiss="modal">
                    <?= language('common/main/main', 'tModalCancel') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(".ocbListItem").unbind().bind('click', function() {
        var bIsMore = $(".ocbListItem:checked").length > 0;
        if (bIsMore) {
            $("#oliBtnDeleteAll").removeClass('disabled');
        } else {
            $("#oliBtnDeleteAll").addClass('disabled');
        }
    });
</script>