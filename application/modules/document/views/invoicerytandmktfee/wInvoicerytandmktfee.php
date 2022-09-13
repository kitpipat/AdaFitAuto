<!-- รองรับการเข้ามาแบบ Noti -->
<input id="oetTRMJumpDocNo" 	type="hidden" value="<?=$aParams['tDocNo'] ?>">
<input id="oetTRMJumpBchCode" 	type="hidden" value="<?=$aParams['tBchCode'] ?>">
<input id="oetTRMJumpAgnCode" 	type="hidden" value="<?=$aParams['tAgnCode'] ?>">
<input id="oetTRMJumpBrwType" 	type="hidden" value="<?=$nTRMBrowseType;?>">
<input id="oetTRMJumpBrwOption" type="hidden" value="<?=$tTRMBrowseOption;?>">
<div id="odvTRMMainMenu" class="main-menu">
    <div class="xCNMrgNavMenu">
        <div class="xCNTRMMaster row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <ol id="oliMenuNav" class="breadcrumb">
                    <?php FCNxHADDfavorite('docInvoiceRytAndMktFee/0/0');?>
                    <li id="oliTRMTitle"     class="xCNLinkClick" onclick="JSvTRMCallPageList('')"><?= language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMTitle')?></li>
                    <li id="oliTRMTitleAdd"  class="active"><a href="javascrip:;"><?= language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMtAddData')?></a></li>
                    <li id="oliTRMTitleInspect" class="active"><a href="javascrip:;"><?= language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMInspect')?></a></li>
                </ol>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right p-r-0">
                <div class="demo-button xCNBtngroup" style="width:100%;">
                    <?php if($aAlwEvent["tAutStaFull"] == "1" || $aAlwEvent["tAutStaAdd"] == "1") : ?>
                        <div id="odvBtnTRMPageAddorEdit">
							<button class="xCNBTNPrimeryPlus" type="button" onclick="JSvTRMCallPageAdd('pageadd')">+</button>
						</div>
                    <?php endif; ?>
                    <div id="odvBtnAddEdit">
                        <div class="demo-button xCNBtngroup" style="width:100%;">
                            <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button" onclick="JSvTRMCallPageList('')"><?=language('common/main/main', 'tBack'); ?></button>
                            <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button" id="obtTRMCancelDoc" style="display:none;" onclick="JSxTRMDocumentCancel(false)"> <?=language('common/main/main', 'tCancel'); ?></button>
                            <button id="obtTRMPrintDoc"     class="btn xCNBTNDefult xCNBTNDefult2Btn" 	type="button" onclick="JSxTRMPrintDoc()"><?=language('common/main/main', 'tCMNPrint'); ?></button>
                            <button id="obtTRMApproveDoc" 	class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button" onclick="JSxTRMDocumentApv(false)"> <?=language('common/main/main', 'tCMNApprove'); ?></button>                                  
                            <?php if ($aAlwEvent['tAutStaFull'] == 1 || ($aAlwEvent['tAutStaAdd'] == 1 || $aAlwEvent['tAutStaEdit'] == 1)): ?>
                                <div class="btn-group xCNBTNSaveDoc">
                                    <button id="obtTRMSubmitFromDoc" type="button" class="btn xWBtnGrpSaveLeft"><?=language('common/main/main', 'tSave'); ?></button>
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
<div class="xCNMenuCump" id="odvMenuCump">&nbsp;</div>
<div class="main-content" id="odvMainContent" style="background-color: #F0F4F7;"><div id="odvContentTRM"></div></div>
<?php include('script/jInvoicerytandmktfee.php') ?>
