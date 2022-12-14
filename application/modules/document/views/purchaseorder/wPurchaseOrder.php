<input id="oetPOStaBrowse" type="hidden" value="<?php echo $nPOBrowseType ?>">
<input id="oetPOCallBackOption" type="hidden" value="<?php echo $tPOBrowseOption ?>">
<input id="oetPOJumpDocNo" type="hidden" value="<?php echo $aParams['tDocNo'] ?>">
<input id="oetPOJumpBchCode" type="hidden" value="<?php echo $aParams['tBchCode'] ?>">
<input id="oetPOJumpAgnCode" type="hidden" value="<?php echo $aParams['tAgnCode'] ?>">
<?php if (isset($nPOBrowseType) && ( $nPOBrowseType == 0 || $nPOBrowseType ==2)) : ?>
    <div id="odvPOMainMenu" class="main-menu">
        <div class="xCNMrgNavMenu">
            <div class="row xCNavRow" style="width:inherit;">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <ol id="oliPOMenuNav" class="breadcrumb">
                        <?php FCNxHADDfavorite('docPO/0/0');?>
                        <li id="oliPOTitle" style="cursor:pointer;"><?php echo language('document/purchaseorder/purchaseorder', 'tPOTitleMenu'); ?></li>
                        <li id="oliPOTitleAdd" class="active"><a><?php echo language('document/purchaseorder/purchaseorder', 'tPOTitleAdd'); ?></a></li>
                        <li id="oliPOTitleEdit" class="active"><a><?php echo language('document/purchaseorder/purchaseorder', 'tPOTitleEdit'); ?></a></li>
                        <li id="oliPOTitleDetail" class="active"><a><?php echo language('document/purchaseorder/purchaseorder', 'tPOTitleDetail'); ?></a></li>
                        <li id="oliPOTitleAprove" class="active"><a><?php echo language('document/purchaseorder/purchaseorder', 'tPOTitleAprove'); ?></a></li>
                        <li id="oliPOTitleConimg" class="active"><a><?php echo language('document/purchaseorder/purchaseorder', 'tPOTitleConimg'); ?></a></li>
                    </ol>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right p-r-0">
                    <div class="demo-button xCNBtngroup" style="width:100%;">
                        <div id="odvPOBtnGrpInfo">
                            <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaAdd'] == 1) : ?>
                                <button id="obtPOCallPageAdd" class="xCNBTNPrimeryPlus" type="button">+</button>
                            <?php endif; ?>
                        </div>
                        <!-- style="display:none;" -->
                        <div id="odvPOBtnGrpAddEdit">
                            <div class="demo-button xCNBtngroup" style="width:100%;">
                                <button id="obtPOCallBackPage"  class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button"> <?php echo language('common/main/main', 'tBack'); ?></button>
                                <?php if ($aAlwEvent['tAutStaFull'] == 1 || ($aAlwEvent['tAutStaAdd'] == 1 || $aAlwEvent['tAutStaEdit'] == 1)): ?>
                                    <button id="obtPOPrintDoc" onclick="JSxPOPrintDoc()" class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button"> <?php echo language('common/main/main', 'tCMNPrint'); ?></button>
                                    <button id="obtPOExportDoc" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button" onclick="JSxPOSendEmail()">???????????????????????????</button>    
                                    <button id="obtPOCancelDoc" class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button"> <?php echo language('common/main/main', 'tCancel'); ?></button>
                                    <button id="obtPOApproveDoc" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button"> <?php echo language('common/main/main', 'tCMNApprove'); ?></button>                                  
                                    <div  id="odvPOBtnGrpSave" class="btn-group">
                                        <button id="obtPOSubmitFromDoc" type="button" class="btn xWBtnGrpSaveLeft"> <?php echo language('common/main/main', 'tSave'); ?></button>
                                        <?php echo $vBtnSave ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="xCNMenuCump xCNPOBrowseLine" id="odvMenuCump">&nbsp;</div>
    <div class="main-content">
        <div id="odvPOContentPageDocument">
        </div>
    </div>
<?php else: ?>
    <div class="modal-header xCNModalHead">
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <a id="oahPOBrowseCallBack" class="xWBtnPrevious xCNIconBack" style="float:left;">
                    <i class="fa fa-arrow-left xCNIcon"></i>	
                </a>
                <ol id="oliPONavBrowse" class="breadcrumb xCNMenuModalBrowse">
                    <li id="oliPOBrowsePrevious" class="xWBtnPrevious"><a><?php echo language('common/main/main', 'tShowData'); ?> : <?php echo language('document/purchaseinvoice/purchaseinvoice', 'tPOTitleMenu'); ?></a></li>
                    <li class="active"><a><?php echo language('document/purchaseorder/purchaseorder', 'tPOTitleAdd'); ?></a></li>
                </ol>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                <div id="odvPOBtnGroup" class="demo-button xCNBtngroup" style="width:100%;">
                    <button id="obtPOBrowseSubmit" type="button" class="btn xCNBTNPrimery"><?php echo language('common/main/main', 'tSave'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <div id="odvModalBodyBrowse" class="modal-body xCNModalBodyAdd">
    </div>
<?php endif; ?>

<!--????????????????????????????????????????????????????????????-->
<div id="odvPOModalExportFile" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="xCNHeardModal modal-title" style="display:inline-block"><?=language('common/main/main', 'tMessageAlert')?></h5>
            </div>
            <div class="modal-body">
                <p>???????????????????????????????????????????????????????????????????????????????????????????????????</p>
                <div class="form-group">
                    <label class="xCNLabelFrm">????????????????????????????????????</label>
                    <select class="selectpicker form-control" id="ocmPOFilterExport" name="ocmPOFilterExport" maxlength="1">
                        <option value="" >???????????????????????????????????????????????????</option>
                        <option value="3" >?????????????????????????????????????????????????????????????????? Excel ????????????????????????????????????????????????</option>
                        <option value="4" >?????????????????????????????????????????????????????????????????? PDF ????????????????????????????????????????????????</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn xCNBTNPrimery xCNConfirmExport" data-dismiss="modal" >
                    ??????????????????
                </button> -->
                <button type="button" class="btn xCNBTNPrimery xCNConfirmExportAndDowload" data-dismiss="modal" >
                    ???????????????????????????
                </button>
                <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button" data-dismiss="modal">
                    <?=language('common/main/main', 'tModalCancel')?>
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>application/modules/document/assets/src/purchaseorder/jPurchaseorder.js"></script>








