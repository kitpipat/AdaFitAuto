<?php
    // Check ข้อมูล TACTRMHD
    if (isset($aDataDocHD) && $aDataDocHD['rtCode'] == '1') {
        $tTRMRoute          = "docInvoiceRytAndMktFeeEventEdit";
        $nStaUploadFile     = 2; 
        $tTRMDocNo          = $aDataDocHD['raItems']['FTXphDocNo'];
        $tTRMStaApv         = $aDataDocHD['raItems']['FTXphStaApv'];
        $nTRMStaDocAct      = $aDataDocHD['raItems']['FNXphStaDocAct'];
        $nTRMStaRef         = $aDataDocHD['raItems']['FNXphStaRef'];
        $tTRMStaDoc         = $aDataDocHD['raItems']['FTXphStaDoc'];
        $tTRMFrmDocPrint    = number_format($aDataDocHD['raItems']['FNXphDocPrint'],0);
        $tTRMRmk            = $aDataDocHD['raItems']['FTXphRmk'];
        $tTRMApvCode        = $aDataDocHD['raItems']['FTXphApvCode'];
        $tTRMUsrNameApv     = $aDataDocHD['raItems']['FTXphApvName'];
        $tTRMFTDueDate      = date('Y-m-d',strtotime($aDataDocHD['raItems']['FDXphDocDate']));
        $dTRMDocDate        = date('Y-m-d',strtotime($aDataDocHD['raItems']['FDXphDocDate']));
        $dTRMDocTime        = date('H:i:s',strtotime($aDataDocHD['raItems']['FDXphDocDate']));
        $tTRMCreateByName   = $aDataDocHD['raItems']['FTCreateByName'];
        $tTRMAgnCode        = $aDataDocHD['raItems']['FTAgnCode'];
        $tTRMAgnName        = $aDataDocHD['raItems']['FTAgnName'];
        $tTRMBchCode        = $aDataDocHD['raItems']['FTBchCode'];
        $tTRMBchName        = $aDataDocHD['raItems']['FTBchName'];
        $tTRMAgnCodeTo      = $aDataDocHD['raItems']['FTAgnCodeTo'];
        $tTRMAgnNameTo      = $aDataDocHD['raItems']['FTAgnNameTo'];
        $tTRMBchCodeTo      = $aDataDocHD['raItems']['FTBchCodeTo'];
        $tTRMBchNameTo      = $aDataDocHD['raItems']['FTBchNameTo'];
        $dTRMDueDate        = date('Y-m-d',strtotime($aDataDocHD['raItems']['FDXphDueDate']));
        $tTRMBbkCode        = $aDataDocHD['raItems']['FTBbkCode'];
        $tTRMBbkName        = $aDataDocHD['raItems']['FTBbkName'];
        $tTRMVatInOrEx      = $aDataDocHD['raItems']['FTXphVATInOrEx'];

        // Bill Month
        $tTRMMonthRM        = $aDataDocHD['raItems']['FTXphMonthRM'];
        $tTRMYearRM         = $aDataDocHD['raItems']['FTXphYearRM'];
    } else {
        $tTRMRoute          = "docInvoiceRytAndMktFeeEventAdd";
        $nStaUploadFile     = 1; 
        $tTRMDocNo          = '';
        $tTRMStaApv         = '';
        $nTRMStaDocAct      = '';
        $nTRMStaRef         = '';
        $tTRMStaDoc         = '';
        $tTRMFrmDocPrint    = 0;
        $tTRMRmk            = '';
        $tTRMApvCode        = '';
        $tTRMUsrNameApv     = '';
        $tTRMFTDueDate      = '';
        $dTRMDocDate        = date('Y-m-d');
        $dTRMDocTime        = date('H:i:s');
        $tTRMCreateByName   = $this->session->userdata('tSesUsrUsername');
        $tTRMAgnCode        = '';
        $tTRMAgnName        = '';
        $tTRMBchCode        = '';
        $tTRMBchName        = '';
        $tTRMAgnCodeTo      = '';
        $tTRMAgnNameTo      = '';
        $tTRMBchCodeTo      = '';
        $tTRMBchNameTo      = '';
        $dTRMDueDate        = "";
        $tTRMBbkCode        = "";
        $tTRMBbkName        = "";
        $tTRMVatInOrEx      = '';

        $tTRMMonthRM        = date('m');
        $tTRMYearRM         = date('Y');
    }

    // Check ข้อมูล Address
    if (isset($aDataAgnBchAddr) && $aDataAgnBchAddr['rtCode'] == '1') {
        $aDataAddr  = $aDataAgnBchAddr['aQuery'];
        if($aDataAddr['FTAddVersion'] == '1'){
            $tTRMAgnAddress =  $aDataAddr['FTAddV1No'].' '.$aDataAddr['FTAddV1Soi'].' '.$aDataAddr['FTAddV1Road'].' '.$aDataAddr['FTAddV1Village'].' '.$aDataAddr['FTSudName'].' '.$aDataAddr['FTDstName'].' '.$aDataAddr['FTPvnName'].' '.$aDataAddr['FTAddV1PostCode'];
        } else {
            $tTRMAgnAddress = $aDataAddr['FTAddV2Desc1'].' '.$aDataAddr['FTAddV2Desc2'];
        }
        $tTRMAgnTel     = $aDataAddr['FTAddTel'];
        $tTRMAgnEmail   = $aDataAddr['FTAgnEmail'];
    } else {
        $tTRMAgnAddress = '';
        $tTRMAgnTel     = '';
        $tTRMAgnEmail   = '';
    }

    // Check ข้อมูล TACTRMHDCst
    if(isset($aDataDocHDCst) && $aDataDocHDCst['rtCode'] == '1'){
        $tTRMCshorCrd   = $aDataDocHDCst['raItems']['FTXphCshOrCrd'];
    } else {
        $tTRMCshorCrd   = "";
    }   
?>
<form id="ofmTRMFormAdd" class="validate-form" action="javascript:void(0)" method="post" enctype="multipart/form-data">
    <button style="display:none" type="submit" id="obtTRMSubmitDocument" onclick="JSxTRMAddEditDocument()"></button>
    <input type="hidden" id="ohdTRMDecimalShow"         name="ohdTRMDecimalShow"    value="<?=$nOptDecimalShow?>">
    <input type="hidden" id="ohdTRMRoute"               name="ohdTRMRoute"          value="<?=@$tTRMRoute?>">
    <input type="hidden" id="ohdTRMDocNo"               name="ohdTRMDocNo"          value="<?=@$tTRMDocNo?>">
    <input type="hidden" id="ohdTRMDocDate"             name="ohdTRMDocDate"        value="<?=@$dTRMDocDate?>">
    <input type="hidden" id="ohdTRMDocTime"             name="ohdTRMDocTime"        value="<?=@$dTRMDocTime?>">
    <input type="hidden" id="ohdTRMStaApv"              name="ohdTRMStaApv"         value="<?=@$tTRMStaApv?>">
    <input type="hidden" id="ohdTRMStaPrc"              name="ohdTRMStaPrc"         value="<?=@$tTRMStaPrc?>">
    <input type="hidden" id="ohdTRMStaDoc"              name="ohdTRMStaDoc"         value="<?=@$tTRMStaDoc?>">
    <input type="hidden" id="ohdConfirmTRMInsertPDT"    name="ohdConfirmTRMInsertPDT">
    <!-- เก็บข้อมูล % Royalty And Marketing Free -->
    <input type="hidden" id="ohdTRMRoyaltyFreeVal"      name="ohdTRMRoyaltyFreeVal">
    <input type="hidden" id="ohdTRMMarkettingFreeVal"   name="ohdTRMMarkettingFreeVal">
    <div class="row">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <!-- Panel รหัสเอกสารและสถานะเอกสาร -->
            <div class="panel panel-default" style="margin-bottom: 25px;">
                <div class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
                    <label class="xCNTextDetail1"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMDocument'); ?></label>
                    <a class="xCNMenuplus collapsed" role="button" data-toggle="collapse" href="#odvTRMDataStatusInfo" aria-expanded="true">
                        <i class="fa fa-plus xCNPlus"></i>
                    </a>
                </div>
                <div id="odvTRMDataStatusInfo" class="panel-collapse collapse" role="tabpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group xCNHide" style="text-align: right;">
                                    <label class="xCNTitleFrom "><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMApproved'); ?></label>
                                </div>
                                <label class="xCNLabelFrm"><span style="color:red">*</span><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMDocNo'); ?></label>
                                <?php if (empty($tTRMDocNo)) : ?>
                                    <div class="form-group">
                                        <label class="fancy-checkbox">
                                            <input type="checkbox" id="ocbTRMStaAutoGenCode" name="ocbTRMStaAutoGenCode" maxlength="1" checked="true" value="1">
                                            <span class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMAutoGenCode'); ?></span>
                                        </label>
                                    </div>
                                <?php endif; ?>
                                <!-- เลขรหัสเอกสาร -->
                                <div class="form-group" style="cursor:not-allowed">
                                    <input 
                                        type="text"
                                        class="form-control xCNInputReadOnly xWTooltipsBT xCNInputWithoutSpcNotThai xCNInputWithoutSingleQuote"
                                        id="oetTRMDocNo" 
                                        name="oetTRMDocNo" 
                                        maxlength="20" 
                                        value="<?= $tTRMDocNo; ?>" 
                                        data-validate-required="<?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMPlsEnterOrRunDocNo'); ?>" 
                                        data-validate-duplicate="<?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMPlsDocNoDuplicate'); ?>" 
                                        placeholder="<?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMDocNo'); ?>"
                                        style="pointer-events:none"
                                        readonly
                                    >
                                    <input type="hidden" id="ohdTRMCheckDuplicateCode" name="ohdTRMCheckDuplicateCode" value="2">
                                </div>
                                <!-- วันที่ในการออกเอกสาร -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMDocDate'); ?></label>
                                    <div class="input-group">
                                        <input 
                                            type="text"
                                            class="form-control xCNInputReadOnly xCNDatePicker xCNInputMaskDate"
                                            id="oetTRMDocDate"
                                            name="oetTRMDocDate"
                                            value="<?=@$dTRMDocDate; ?>"
                                            data-validate-required="<?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMPlsEnterDocDates'); ?>"
                                        >
                                        <span class="input-group-btn">
                                            <button id="obtTRMDocDate" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                                        </span>
                                    </div>
                                </div>
                                <!-- เวลาในการออกเอกสาร -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMDocTime'); ?></label>
                                    <div class="input-group">
                                        <input 
                                            type="text"
                                            class="form-control xCNInputReadOnly xCNTimePicker"
                                            id="oetTRMDocTime"
                                            name="oetTRMDocTime"
                                            value="<?=@$dTRMDocTime;?>"
                                            data-validate-required="<?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMPlsEnterDocTime');?>"
                                        >
                                        <span class="input-group-btn">
                                            <button id="obtTRMDocTime" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                                        </span>
                                    </div>
                                </div>
                                <!-- ผู้สร้างเอกสาร -->
                                <div class="form-group" style="margin:0">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                            <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTBStaDoc'); ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                                            <?php   
                                                if($tTRMRoute == "docInvoiceRytAndMktFeeEventEdit"){
                                                    $tTRMLabelStaDoc    = language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMStaDoc'.$tTRMStaDoc);
                                                }else{
                                                    $tTRMLabelStaDoc    = '-';
                                                }
                                            ?>
                                            <label><?=$tTRMLabelStaDoc;?></label>
                                        </div>
                                    </div>
                                </div>
                                <!-- สถานะอนุมัติเอกสาร -->
                                <div class="form-group" style="margin:0">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                            <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTBStaApv'); ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                                            <?php if ($tTRMStaDoc == 3) { ?>
                                                <label><?= language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMStaDoc'.$tTRMStaDoc);; ?></label>
                                            <?php }else{ ?>
                                                <label><?= language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMStaApv'.$tTRMStaApv); ?></label>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($tTRMDocNo) && !empty($tTRMDocNo)): ?>
                                    <!-- ผู้อนุมัติเอกสาร -->
                                    <div class="form-group" style="margin:0">
                                        <div class="row">
                                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                                <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMApvBy'); ?></label>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                                                <input type="hidden" id="ohdIVApvCode" name="ohdIVApvCode" maxlength="20" value="<?= $tTRMApvCode ?>">
                                                <label>
                                                    <?= (isset($tTRMUsrNameApv) && !empty($tTRMUsrNameApv)) ? $tTRMUsrNameApv : "-"; ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel เงื่อนไข อ้างอิงเอกสารภายใน และ ภายนอก-->
            <?php include('Panel/wPanelCondition.php');?>

            <!-- Panel ลูกค้า -->
            <?php include('Panel/wPanelCustomer.php');?>

            <!-- Panel เงื่อนไขการชำระเงิน -->
            <?php include('Panel/wPanelPayment.php');?>

            <!-- Panel อื่นๆ -->
            <?php include('Panel/wPanelOther.php');?>

            <!-- Panel ไฟลแนบ -->
            <?php include('Panel/wPanelFileImport.php');?>

        </div>
        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
            <div class="row">
                <!-- ตารางสินค้า -->
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="panel panel-default" style="margin-bottom:25px; position:relative; min-height:200px;">
                        <div class="panel-collapse collapse in" role="tabpanel">
                            <div class="panel-body">
                                <div class="row">
                                    <!-- ลูกค้า -->
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMSupplierChoose'); ?></label>
                                            <div class="input-group" style="width:100%;">
                                                <input type="text"  class="input100 xCNHide" id="ohdTRMAgnCode" name="ohdTRMAgnCode" value="<?= @$tTRMAgnCodeTo; ?>">
                                                <input 
                                                    type="text"
                                                    class="form-control xWPointerEventNone"
                                                    id="ohdTRMAgnName"
                                                    name="ohdTRMAgnName"
                                                    value="<?= @$tTRMAgnNameTo;?>"
                                                    placeholder="<?= language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMValidateSupplier'); ?>"
                                                    readonly 
                                                >
                                                <span class="input-group-btn">
                                                    <button id="obtTRMBrowseAgn" type="button" class="btn xCNBtnBrowseAddOn" >
                                                        <img src="<?=  base_url() . '/application/modules/common/assets/images/icons/find-24.png'; ?>">
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMSupplierBchChoose'); ?></label>
                                            <div class="input-group" style="width:100%;">
                                                <input type="text" class="input100 xCNHide" id="ohdTRMAgnBchCode" name="ohdTRMAgnBchCode" value="<?= @$tTRMBchCodeTo ?>">
                                                <input
                                                    type="text"
                                                    class="form-control xWPointerEventNone"
                                                    id="ohdTRMAgnBchName"
                                                    name="ohdTRMAgnBchName"
                                                    value="<?= @$tTRMBchNameTo ?>"
                                                    placeholder="<?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMSupplierBchChoose') ?>"
                                                    readonly
                                                >
                                                <span class="input-group-btn">
                                                    <button id="obtTRMBrowseAgnBch" type="button" class="btn xCNBtnBrowseAddOn">
                                                        <img src="<?= base_url() . '/application/modules/common/assets/images/icons/find-24.png' ?>">
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="row tab-content xCNClaimTabContent">
                                            <div id="odvInvoicerytandmktfreeStep1" class="tab-pane fade in active" style="padding-top: 0px;">
                                                <?php include('step_form/wStep1.php'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- =========================================== กรุณากรอกข้อมูลให้ครบถ้วน =========================================== -->
<div id="odvTRMModalPleseDataInFill" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="xCNHeardModal modal-title" style="display:inline-block"><?=language('common/main/main', 'tMessageAlert')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="ospTRMModalPleseDataInFill">กรุณาเลือกลูกค้า ก่อนทำรายการ</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn xCNBTNPrimery" data-dismiss="modal">
                    <?=language('common/main/main', 'tCMNOK')?>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================================================= -->

<!-- ================================================ อนุมัติเอกสาร  ================================================ -->
<div class="modal fade xCNModalApprove" id="odvTRMPopupApv">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="xCNHeardModal modal-title" style="display:inline-block"><?=language('common/main/main', 'tApproveTheDocument'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?=language('common/main/main', 'tMainApproveStatus'); ?></p>
                <ul>
                    <li><?=language('common/main/main', 'tMainApproveStatus1'); ?></li>
                    <li><?=language('common/main/main', 'tMainApproveStatus2'); ?></li>
                    <li><?=language('common/main/main', 'tMainApproveStatus3'); ?></li>
                    <li><?=language('common/main/main', 'tMainApproveStatus4'); ?></li>
                </ul>
                <p><?=language('common/main/main', 'tMainApproveStatus5'); ?></p>
                <p><strong><?=language('common/main/main', 'tMainApproveStatus6'); ?></strong></p>
            </div>
            <div class="modal-footer">
                <button onclick="JSxTRMDocumentApv(true)" type="button" class="btn xCNBTNPrimery">
                    <?=language('common/main/main', 'tModalConfirm'); ?>
                </button>
                <button type="button" class="btn xCNBTNDefult" data-dismiss="modal">
                    <?=language('common/main/main', 'tModalCancel'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================================================= -->

<!-- =============================================== ยกเลิกเอกสาร  ================================================= -->
<div class="modal fade" id="odvTRMPopupCancel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header xCNModalHead">
                <label class="xCNTextModalHeard"><?=language('document/document/document', 'tDocDocumentCancel') ?></label>
            </div>
            <div class="modal-body">
                <p>เอกสารที่ทำการยกเลิกแล้ว จะไม่สามารถแก้ไขได้</p>
                <p><strong><?=language('document/document/document', 'tDocCancelText2') ?></strong></p>
            </div>
            <div class="modal-footer">
                <button onclick="JSxTRMDocumentCancel(true)" type="button" class="btn xCNBTNPrimery">
                    <?=language('common/main/main', 'tModalConfirm'); ?>
                </button>
                <button type="button" class="btn xCNBTNDefult" data-dismiss="modal">
                    <?=language('common/main/main', 'tModalCancel'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================================================= -->

<!-- ================================================== เคลียค่า  ================================================== -->
<div class="modal fade" id="odvTRMPopupWarning">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header xCNModalHead">
                <label class="xCNTextModalHeard"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMWarning') ?></label>
            </div>
            <div class="modal-body">
                <p id="tWarningText"></p>
                <p><strong id="tWarningTextCfm"></strong></p>
            </div>
            <div class="modal-footer">
                <button onclick="JSxTRMPopupWarning(true)" type="button" class="btn xCNBTNPrimery">
                    <?=language('common/main/main', 'tModalConfirm'); ?>
                </button>
                <button type="button" class="btn xCNBTNDefult xCNBtnControllHide" data-dismiss="modal">
                    <?=language('common/main/main', 'tModalCancel'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================================================= -->

<?php include('script/jInvoicerytandmktfeePageAdd.php');?>