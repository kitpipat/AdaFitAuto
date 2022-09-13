<input id="oetTFWStaBrowse" type="hidden" value="<?= $nBrowseType ?>">
<input id="oetTFWCallBackOption" type="hidden" value="<?= $tBrowseOption ?>">
<input id="oetTFWJumpDocNo" type="hidden" value="<?php echo $aParams['tDocNo'] ?>">
<input id="oetTFWJumpBchCode" type="hidden" value="<?php echo $aParams['tBchCode'] ?>">
<input id="oetTFWJumpAgnCode" type="hidden" value="<?php echo $aParams['tAgnCode'] ?>">

<div id="odvTFWMainMenu" class="main-menu">
    <div class="xCNMrgNavMenu">
        <div class="row xCNavRow" style="width:inherit;">
            <div class="xCNTFWVMaster">
                <div class="col-xs-12 col-md-6">
                    <ol id="oliMenuNav" class="breadcrumb">
                        <?php FCNxHADDfavorite('TFW/0/0');?> 
                        <li id="oliTFWTitle" class="xCNLinkClick" onclick="JSvCallPageTFWList()"><?= language('document/producttransferwahouse/producttransferwahouse', 'tTFWTitle') ?></li>
                        <li id="oliTFWTitleAdd" class="active"><a><?= language('document/producttransferwahouse/producttransferwahouse', 'tTFWTitleAdd') ?></a></li>
                        <li id="oliTFWTitleEdit" class="active"><a><?= language('document/producttransferwahouse/producttransferwahouse', 'tTFWTitleEdit') ?></a></li>
                        <li id="oliPITitleDetail" class="active"><a><?php echo language('document/purchaseinvoice/purchaseinvoice', 'tPITitleDetail'); ?></a></li>
                    </ol>
                </div>
                <div class="col-xs-12 col-md-6 text-right p-r-0">
                    <div class="demo-button xCNBtngroup" style="width:100%;">
                        <div id="odvBtnTFWInfo">
                            <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaAdd'] == 1) : ?>
                                <button class="xCNBTNPrimeryPlus" type="button" onclick="JSvCallPageTFWAdd()">+</button>
                            <?php endif; ?>
                        </div>
                        <div id="odvBtnAddEdit">
                            <div class="demo-button xCNBtngroup" style="width:100%;">
                                <button onclick="JSvCallPageTFWList()" class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button"> <?php echo language('common/main/main', 'tBack') ?></button>
                                <?php if ($aAlwEvent['tAutStaFull'] == 1 || ($aAlwEvent['tAutStaAdd'] == 1 || $aAlwEvent['tAutStaEdit'] == 1)) : ?>
                                    <button id="obtTFWPrint" onclick="FSxTranferProductPrintDoc()" class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button"> <?php echo language('common/main/main', 'tCMNPrint') ?></button>
                                    <button id="obtTFWCancel" onclick="JSnTFWCancel(false)" class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button"> <?php echo language('common/main/main', 'tCancel') ?></button>
                                    <button id="obtTFWApprove" onclick="JSxSetStatusClickTFWSubmit(2);JSxValidateViaOrderBeforeApv(false)" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button"> <?php echo language('common/main/main', 'tCMNApprove') ?></button>                                   
                                    <div class="btn-group">
                                        <button id="obtTWXSubmit" type="button" class="btn xWBtnGrpSaveLeft" onclick="JSxSetStatusClickTFWSubmit(1);$('#obtSubmitTFW').click()"> <?php echo language('common/main/main', 'tSave') ?></button>
                                        <?php echo $vBtnSave ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="xCNTFWVBrowse">
                <div class="col-xs-12 col-md-6">
                    <a class="xWBtnPrevious xCNIconBack" style="float:left;font-size:19px;">
                        <i class="fa fa-arrow-left xCNIcon"></i>	
                    </a>
                    <ol id="oliTFWNavBrowse" class="breadcrumb xCNBCMenu" style="margin-left:25px">
                        <li class="xWBtnPrevious"><a><?= language('common/main/main', 'tShowData') ?> : <?= language('promotion/promotion/promotion', 'tPMTTitle') ?></a></li>
                        <li class="active"><a><?= language('common/main/main', 'tAddData') ?></a></li>
                    </ol>
                </div>
                <div class="col-xs-12 col-md-6 text-right">
                    <div id="odvTFWBtnGroup" class="demo-button xCNBtngroup" style="width:100%;">
                        <button type="button" class="btn xCNBTNPrimery" onclick="$('#obtSubmitTFW').click()"><?= language('common/main/main', 'tSave') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="xCNMenuCump xCNTFWBrowseLine" id="odvMenuCump">
    &nbsp;
</div>
<div class="main-content">
    <div id="odvContentPageTFW">
    </div>
</div>
<script>
    var tBaseURL        = '<?php echo base_url(); ?>';
    //tSys Decimal Show
    var nOptDecimalShow = '<?php echo $nOptDecimalShow; ?>';
    var nOptDecimalSave = '<?php echo $nOptDecimalSave; ?>';
    // Set Lang Edit 
    var nLangEdits      = '<?php echo $this->session->userdata("tLangEdit");?>';
    var tUsrApv         = '<?php echo $this->session->userdata("tSesUsername");?>';
</script>

<script type="text/javascript" src="<?php echo base_url() ?>application/modules/common/assets/js/jquery.tablednd.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>application/modules/document/assets/src/producttransferwahouse/jProducttransferwahouse.js"></script>






