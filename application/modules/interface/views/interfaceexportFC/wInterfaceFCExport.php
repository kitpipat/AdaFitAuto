<?php 
    if($this->session->userdata("tSesUsrLevel") == "HQ"){
        $tDisabled  = "";
    } else {
        $nCountBch  = $this->session->userdata("nSesUsrBchCount");
        if($nCountBch == 1){
			$tDisabled  = "disabled";
		}else{
			$tDisabled  = "";
		}
    }
    $tUserBchCode   = $this->session->userdata("tSesUsrBchCodeDefault");
	$tUserBchName   = $this->session->userdata("tSesUsrBchNameDefault");
	$dStartFrm		= date('Y-m-d');
	$dStartTo		= date('Y-m-d');
?>
<input type="hidden"    id="oetInterfaceFCExportStaBrowse"      value="<?=$nBrowseType?>">
<input type="hidden"    id="oetInterfaceFCExportCallBackOption" value="<?=$tBrowseOption?>">

<div id="odvIFCMainMenu" class="main-menu clearfix">
    <div class="xCNMrgNavMenu">
        <div class="row xCNavRow" style="width:inherit;">
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
				<ol id="oliMenuNav" class="breadcrumb">
					<?php FCNxHADDfavorite('interfaceexportFC/0/0');?> 
					<li id="oliInterfaceExportFCTitle" class="xCNLinkClick" style="cursor:pointer" onclick="JSvInterfaceExportFCFCCallPage('')">
						<?= language('interface/interfaceexportfc/interfaceexportfc','tITFCTitle') ?>
					</li>
				</ol>
			</div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 text-right p-r-0">
                <div id="odvBtnCmpEditInfo">
                    <button id="obtInterfaceExportFCConfirm" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button">
                        <?=language('interface/interfaceexportfc/interfaceexportfc','tITFCTConfirm')  ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<form class="contact100-form validate-form" action="javascript:void(0)" method="post" enctype="multipart/form-data" id="ofmInterfaceExportFC">
    <div class="main-content">
        <div class="panel panel-headline">
            <input type="hidden" name="tUserCode" id="tUserCode" value="<?=$this->session->userdata('tSesUserCode');?>">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="table-responsive" style="padding:20px">
                        <table id="otbInterfaceExportFCFCDataList" class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="4%" nowrap class="text-center xCNTextBold"><?=language('interface/interfaceexportfc/interfaceexportfc','tITFCID'); ?></th>
                                    <th width="4%" nowrap class="text-center xCNTextBold">
                                        <input type="checkbox" id="ocmINMExportAll" value="1">
                                    </th>
                                    <th width="20%" nowrap class="text-center xCNTextBold"><?=language('interface/interfaceexportfc/interfaceexportfc','tITFCList'); ?></th>
                                    <th nowrap class="text-center xCNTextBold"><?=language('interface/interfaceexportfc/interfaceexportfc','tITFCCondition'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ?????????????????????????????????????????????????????? ???????????????????????????????????????????????????????????? -->
                                <input type='text' class='form-control xCNHide' id="ohdExCreditCode" name="ohdExCreditCode">
                                <?php if(!empty($aDataMasterExport)): ?>
                                    <?php foreach($aDataMasterExport AS $nK => $aData): ?>
                                        <tr data-apicode="<?=$aData['FTApiCode']?>">
                                            <td align="center"><?=($nK+1)?></td>
                                            <td align="center">
                                                <input type="checkbox" class="progress-bar-chekbox xCNCheckBoxExport xCNCheckbox<?=$aData['FTApiCode']?>" name="ocmIFCExport[]" value="<?=$aData['FTApiCode']?>" idpgb="xWIFCExpBonTextDisplay" data-type="ExpBon">
                                            </td>
                                            <td align="left"><?=$aData['FTApiName']?></td>
                                            <?php
                                                //$aFilterSearch = '1' : ???????????? ????????? ????????????
                                                //$aFilterSearch = '2' : ?????????????????? ????????? ??????????????????
                                                //$aFilterSearch = '3' : ??????????????????????????? ????????? ???????????????????????????
                                                //$aFilterSearch = '4' : ???????????????????????? ????????? ????????????????????????
                                                //$aFilterSearch = '5' : ???????????????
                                                //$aFilterSearch = '6' : ??????
                                                //$aFilterSearch = '7' : ????????????????????????????????? ????????? ?????????????????????????????????
                                                //$aFilterSearch = '8' : ?????????????????????????????? ????????? ??????????????????????????????
                                                //$aFilterSearch = '9' : ?????????????????? ????????? ??????????????????
                                                //$aFilterSearch = '10' : ???????????????????????? ????????? ????????????????????????
                                                //$aFilterSearch = '11' : ??????????????????????????????????????????????????? ????????? ???????????????????????????????????????????????????
                                                //$aFilterSearch = '12' : ????????????????????????????????? ????????? ?????????????????????????????????
                                                //$aFilterSearch = '13' : ??????????????????????????? ????????? ???????????????????????????
                                                //$aFilterSearch = '14' : ??????????????????????????????????????? ????????? ???????????????????????????????????????
                                                //$aFilterSearch = '15' : ???????????????????????????????????????????????????
                                                //$aFilterSearch = '16' : ????????????????????????????????????
                                                switch ($aData['FTApiCode']) {
                                                    case "00035": // ???????????????????????????????????? Royalty Fee & Marketing Fee(SAP)
                                                        $HideClass      = 'xCNHide';
                                                        $aFilterSearch  =[1,5,6];
                                                    break;
                                                }
                                            ?>
                                            <td>
                                                <?php 
                                                    foreach($aFilterSearch AS $n => $nInforFilter){
                                                        switch ($nInforFilter) {
                                                            case "1": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--?????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <?=language('document\adjustmentcost\adjustmentcost', 'tADCAdvSearchBranch'); ?>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">	
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExBchCodeFrm<?=$aData['FTApiCode']?>" name="oetExBchCodeFrm<?=$aData['FTApiCode']?>" maxlength="5" value="<?=$tUserBchCode; ?>">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExBchNameFrm<?=$aData['FTApiCode']?>" value="<?=$tUserBchName; ?>" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseBchFrm" <?=$tDisabled; ?> data-typebrowse="Frm" data-apicode="<?=$aData['FTApiCode']?>" >
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--?????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <label><?=language('document\adjustmentcost\adjustmentcost', 'tADCAdvSearchBranchTo'); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExBchCodeTo<?=$aData['FTApiCode']?>" name="oetExBchCodeTo<?=$aData['FTApiCode']?>" maxlength="5" value="<?=$tUserBchCode; ?>">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExBchNameTo<?=$aData['FTApiCode']?>" value="<?=$tUserBchName; ?>" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseBchTo" <?=$tDisabled; ?> data-typebrowse="To" data-apicode="<?=$aData['FTApiCode']?>" >
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php break;
                                                            case "2": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--???????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <label><?=language('interface/interfaceexport/interfaceexport', 'tITFXFilterDate'); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input 
                                                                                        type="text" 
                                                                                        class="form-control xWITFXDatePickerSale xCNDatePicker xCNDatePickerStart xCNInputMaskDate xWRptAllInput" 
                                                                                        autocomplete="off" 
                                                                                        id="oetExDateFrm<?=$aData['FTApiCode']?>" 
                                                                                        name="oetExDateFrm<?=$aData['FTApiCode']?>" 
                                                                                        maxlength="10"
                                                                                        value=<?=$dStartFrm;?>>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnDateTime xCNExDateFrm"><img class="xCNIconCalendar"></button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--???????????????????????????-->
                                                                        <div class="col-md-1 <?php echo $HideClass ?>">
                                                                            <label><?=language('interface/interfaceexport/interfaceexport', 'tITFXFilterDateTo'); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5 <?php echo $HideClass ?>">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input 
                                                                                        type="text" 
                                                                                        class="form-control xWITFXDatePickerSale xCNDatePicker xCNDatePickerStart xCNInputMaskDate xWRptAllInput" 
                                                                                        autocomplete="off" 
                                                                                        id="oetExDateTo<?=$aData['FTApiCode']?>"  
                                                                                        name="oetExDateTo<?=$aData['FTApiCode']?>" 
                                                                                        maxlength="10"
                                                                                        value=<?=$dStartTo;?>>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnDateTime xCNExDateTo"><img class="xCNIconCalendar"></button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php break;
                                                            case "3": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--?????????????????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <?=language('interface/interfaceexport/interfaceexport','tITFXFilterDocSal'); ?>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">	
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExDocSaleCodeFrm<?=$aData['FTApiCode']?>" name="oetExDocSaleCodeFrm<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExDocSaleNameFrm<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseDocSaleFrm" data-typebrowse="Frm" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--?????????????????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <label><?=language('interface/interfaceexport/interfaceexport', 'tITFXFilterTo'); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExDocSaleCodeTo<?=$aData['FTApiCode']?>" name="oetExDocSaleCodeTo<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExDocSaleNameTo<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseDocSaleTo" data-typebrowse="To" data-apicode="<?=$aData['FTApiCode']?>" >
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php break;
                                                            case "4": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <?=language('interface/interfaceexport/interfaceexport','tExFilterCreditorFrm'); ?>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">	
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExCreditCodeFrm<?=$aData['FTApiCode']?>" name="oetExCreditCodeFrm<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExCreditNameFrm<?=$aData['FTApiCode']?>"value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseCreditFrm" data-typebrowse="Frm" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--?????????????????????????????????-->
                                                                        <!-- <div class="col-md-1">
                                                                            <label><?=language('interface/interfaceexport/interfaceexport', 'tExFilterCreditorTo'); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExCreditCodeTo<?=$aData['FTApiCode']?>" name="oetExCreditCodeTo<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExCreditNameTo<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseCreditTo" data-typebrowse="To" data-apicode="<?=$aData['FTApiCode']?>" >
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div> -->
                                                                    </div>
                                                                </div>
                                                                <?php break;
                                                            case "5": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--???????????????-->
                                                                        <div class="col-md-1">
                                                                            <?=language('interface/interfaceexport/interfaceexport','tExFilterMonth'); ?>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <select name="ocmExMonth<?=$aData['FTApiCode']?>" id="ocmExMonth<?=$aData['FTApiCode']?>" class="form-control"  style="width:100%"> 
                                                                                    <option value="01" <?php if(date('m') == '01'){ echo 'selected'; } ?> ><?=language('movement/movement/movement', 'tMMTJan')?></option>
                                                                                    <option value="02" <?php if(date('m') == '02'){ echo 'selected'; } ?>><?=language('movement/movement/movement', 'tMMTFeb')?></option>
                                                                                    <option value="03" <?php if(date('m') == '03'){ echo 'selected'; } ?>><?=language('movement/movement/movement', 'tMMTMar')?></option>
                                                                                    <option value="04" <?php if(date('m') == '04'){ echo 'selected'; } ?>><?=language('movement/movement/movement', 'tMMTApr')?></option>
                                                                                    <option value="05" <?php if(date('m') == '05'){ echo 'selected'; } ?>><?=language('movement/movement/movement', 'tMMTMay')?></option>
                                                                                    <option value="06" <?php if(date('m') == '06'){ echo 'selected'; } ?>><?=language('movement/movement/movement', 'tMMTJune')?></option>
                                                                                    <option value="07" <?php if(date('m') == '07'){ echo 'selected'; } ?>><?=language('movement/movement/movement', 'tMMTJuly')?></option>
                                                                                    <option value="08" <?php if(date('m') == '08'){ echo 'selected'; } ?>><?=language('movement/movement/movement', 'tMMTAug')?></option>
                                                                                    <option value="09" <?php if(date('m') == '09'){ echo 'selected'; } ?>><?=language('movement/movement/movement', 'tMMTSept')?></option>
                                                                                    <option value="10" <?php if(date('m') == '10'){ echo 'selected'; } ?>><?=language('movement/movement/movement', 'tMMTOct')?></option>
                                                                                    <option value="11" <?php if(date('m') == '11'){ echo 'selected'; } ?>><?=language('movement/movement/movement', 'tMMTNov')?></option>
                                                                                    <option value="12" <?php if(date('m') == '12'){ echo 'selected'; } ?>><?=language('movement/movement/movement', 'tMMTDec')?></option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php break;
                                                            case "6": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--??????-->
                                                                        <div class="col-md-1">
                                                                            <?=language('interface/interfaceexport/interfaceexport','tExFilterYears'); ?>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <select name="ocmExYear<?=$aData['FTApiCode']?>" id="ocmExYear<?=$aData['FTApiCode']?>" class="form-control"  style="width:100%"> 
                                                                                    <option value="2017" <?php if(date('Y') == '2017'){ echo 'selected'; } ?>>2017</option>
                                                                                    <option value="2018" <?php if(date('Y') == '2018'){ echo 'selected'; } ?>>2018</option>
                                                                                    <option value="2019" <?php if(date('Y') == '2019'){ echo 'selected'; } ?>>2019</option>
                                                                                    <option value="2020" <?php if(date('Y') == '2020'){ echo 'selected'; } ?>>2020</option>
                                                                                    <option value="2021" <?php if(date('Y') == '2021'){ echo 'selected'; } ?>>2021</option>
                                                                                    <option value="2022" <?php if(date('Y') == '2022'){ echo 'selected'; } ?>>2022</option>
                                                                                    <option value="2023" <?php if(date('Y') == '2023'){ echo 'selected'; } ?>>2023</option>
                                                                                    <option value="2024" <?php if(date('Y') == '2024'){ echo 'selected'; } ?>>2024</option>
                                                                                    <option value="2025" <?php if(date('Y') == '2025'){ echo 'selected'; } ?>>2025</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                                break;
                                                            case "7": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--?????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <?=language('interface/interfaceexport/interfaceexport','tExFilterGrpPdtFrm'); ?>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">	
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExGrpPDTCodeFrm<?=$aData['FTApiCode']?>" name="oetExGrpPDTCodeFrm<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExGrpPDTNameFrm<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseGrpPDTFrm" data-typebrowse="To" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--??????????????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <label><?=language('interface/interfaceexport/interfaceexport', 'tExFilterGrpPdtTo'); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExGrpPDTCodeTo<?=$aData['FTApiCode']?>" name="oetExGrpPDTCodeTo<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExGrpPDTNameTo<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseGrpPDTTo" data-typebrowse="Frm" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php break;
                                                            case "8": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--??????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <?=language('interface/interfaceexport/interfaceexport','tExFilterCatePdtFrm'); ?>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">	
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExCatePDTCodeFrm<?=$aData['FTApiCode']?>" name="oetExCatePDTCodeFrm<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExCatePDTNameFrm<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseCatePDTFrm" data-typebrowse="Frm" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--???????????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <label><?=language('interface/interfaceexport/interfaceexport', 'tExFilterCatePdtTo'); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExCatePDTCodeTo<?=$aData['FTApiCode']?>" name="oetExCatePDTCodeTo<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExCatePDTNameTo<?=$aData['FTApiCode']?>"  value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseCatePDTTo" data-typebrowse="To" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php break;
                                                            case "9": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--??????????????????-->
                                                                        <div class="col-md-1">
                                                                            <?=language('interface/interfaceexport/interfaceexport','tExFilterPdtFrm'); ?>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">	
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExPDTCodeFrm<?=$aData['FTApiCode']?>" name="oetExPDTCodeFrm<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExPDTNameFrm<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowsePDTFrm"  data-typebrowse="Frm" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--???????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <label><?=language('interface/interfaceexport/interfaceexport', 'tExFilterPdtTo'); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExPDTCodeTo<?=$aData['FTApiCode']?>" name="oetExPDTCodeTo<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExPDTNameTo<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowsePDTTo"  data-typebrowse="To" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php break;
                                                            case "10": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--?????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <?=language('interface/interfaceexport/interfaceexport','tExFilterUnitPdtFrm'); ?>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">	
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExUnitPDTCodeFrm<?=$aData['FTApiCode']?>" name="oetExUnitPDTCodeFrm<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExUnitPDTNameFrm<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseUnitPDTFrm" data-typebrowse="Frm" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--?????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <label><?=language('interface/interfaceexport/interfaceexport', 'tExFilterUnitPdtTo'); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExUnitPDTCodeTo<?=$aData['FTApiCode']?>" name="oetExUnitPDTCodeTo<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExUnitPDTNameTo<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseUnitPDTTo" data-typebrowse="To" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php break;
                                                            case "11": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--??????????????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <?=language('interface/interfaceexport/interfaceexport','tExFilterTypePayFrm'); ?>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">	
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExTypePayCodeFrm<?=$aData['FTApiCode']?>" name="oetExTypePayCodeFrm<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExTypePayNameFrm<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseTypePayFrm" data-typebrowse="Frm" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--???????????????????????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <label><?=language('interface/interfaceexport/interfaceexport', 'tExFilterTypePayTo'); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExTypePayCodeTo<?=$aData['FTApiCode']?>" name="oetExTypePayCodeTo<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExTypePayNameTo<?=$aData['FTApiCode']?>"  value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseTypePayTo" data-typebrowse="To" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php break;
                                                            case "12": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--?????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <?=language('interface/interfaceexport/interfaceexport','tExFilterDocCouponFrm'); ?>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">	
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExDocCouponCodeFrm<?=$aData['FTApiCode']?>" name="oetExDocCouponCodeFrm<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExDocCouponNameFrm<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseDocCouponFrm" data-typebrowse="Frm" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--??????????????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <label><?=language('interface/interfaceexport/interfaceexport', 'tExFilterDocCouponTo'); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExDocCouponCodeTo<?=$aData['FTApiCode']?>" name="oetExDocCouponCodeTo<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExDocCouponNameTo<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseDocCouponTo" data-typebrowse="To" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php break;
                                                            case "13": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--?????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <?=language('interface/interfaceexport/interfaceexport','tExFilterCouponCodeFrm'); ?>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">	
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExCouponCodeFrm<?=$aData['FTApiCode']?>" name="oetExCouponCodeFrm<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExCouponNameFrm<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseCouponFrm" data-typebrowse="Frm" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--??????????????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <label><?=language('interface/interfaceexport/interfaceexport', 'tExFilterCouponCodeTo'); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExCouponCodeTo<?=$aData['FTApiCode']?>" name="oetExCouponCodeTo<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExCouponNameTo<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseCouponTo" data-typebrowse="To" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php break;
                                                            case "14": ?>
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <!--???????????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <?=language('interface/interfaceexport/interfaceexport','tExFilterPromotionFrm'); ?>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">	
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExPromotionCodeFrm<?=$aData['FTApiCode']?>" name="oetExPromotionCodeFrm<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExPromotionNameFrm<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowsePromotionFrm" data-typebrowse="Frm" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--????????????????????????????????????????????????-->
                                                                        <div class="col-md-1">
                                                                            <label><?=language('interface/interfaceexport/interfaceexport', 'tExFilterPromotionTo'); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input class="form-control xCNHide" id="oetExPromotionCodeTo<?=$aData['FTApiCode']?>" name="oetExPromotionCodeTo<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                    <input class="form-control xWPointerEventNone" type="text" id="oetExPromotionNameTo<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowsePromotionTo" data-typebrowse="To" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                            <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php break;
                                                            case "15": ?>
                                                                    <div class="col-lg-12">
                                                                        <div class="row">
                                                                            <!--????????????????????????????????????????????? ????????????????????????-->
                                                                            <div class="col-md-1">
                                                                                <?=language('interface/interfaceexport/interfaceexport','tExFilterBillFrm'); ?>
                                                                            </div>
                                                                            <div class="col-md-5">
                                                                                <div class="form-group">	
                                                                                    <div class="input-group">
                                                                                        <input class="form-control xCNHide" id="oetExDocBillCodeFrm<?=$aData['FTApiCode']?>" name="oetExDocBillCodeFrm<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                        <input class="form-control xWPointerEventNone" type="text" id="oetExDocBillNameFrm<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                        <span class="input-group-btn">
                                                                                            <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseDocBillFrm" data-typebrowse="Frm" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                                <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                            </button>
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!--????????????????????????????????????????????? ????????????????????????-->
                                                                            <!-- <div class="col-md-1">
                                                                                <label><?=language('interface/interfaceexport/interfaceexport', 'tExFilterBillTo'); ?></label>
                                                                            </div>
                                                                            <div class="col-md-5">
                                                                                <div class="form-group">
                                                                                    <div class="input-group">
                                                                                        <input class="form-control xCNHide" id="oetExDocBillCodeTo<?=$aData['FTApiCode']?>" name="oetExDocBillCodeTo<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                        <input class="form-control xWPointerEventNone" type="text" id="oetExDocBillNameTo<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                        <span class="input-group-btn">
                                                                                            <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseDocBillTo" data-typebrowse="To" data-apicode="<?=$aData['FTApiCode']?>" >
                                                                                                <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                            </button>
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div> -->
                                                                        </div>
                                                                    </div>
                                                                <?php break;
                                                            case "16": ?>
                                                                    <div class="col-lg-12">
                                                                        <div class="row">
                                                                            <!--??????????????????????????????????????????????????????-->
                                                                            <div class="col-md-1">
                                                                                <?=language('interface/interfaceexport/interfaceexport','tExFilterIVFrm'); ?>
                                                                            </div>
                                                                            <div class="col-md-5">
                                                                                <div class="form-group">	
                                                                                    <div class="input-group">
                                                                                        <input class="form-control xCNHide" id="oetExDocIVCode<?=$aData['FTApiCode']?>" name="oetExDocIVCode<?=$aData['FTApiCode']?>" maxlength="5" value="">
                                                                                        <input class="form-control xWPointerEventNone" type="text" id="oetExDocIVName<?=$aData['FTApiCode']?>" value="" readonly>
                                                                                        <span class="input-group-btn">
                                                                                            <button type="button" class="btn xCNBtnBrowseAddOn xCNExBrowseDocIV" data-apicode="<?=$aData['FTApiCode']?>">
                                                                                                <img src="<?=base_url('application/modules/common/assets/images/icons/find-24.png'); ?>">
                                                                                            </button>
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php break;
                                                            default:
                                                        }
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!--Modal Success-->
<div class="modal fade" id="odvInterfaceExportFCSuccess">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="xCNHeardModal modal-title" style="display:inline-block"><?=language('interface/interfaceexport/interfaceexport','tStatusProcess'); ?></h5>
            </div>
            <div class="modal-body">
                <p><?=language('interface/interfaceexport/interfaceexport','tContentProcess'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" id="obtIFCModalMsgConfirm" type="button" class="btn xCNBTNPrimery">
                    <?=language('common/main/main', 'tModalConfirm'); ?>
                </button>
				<button type="button" id="obtIFCModalMsgCancel" class="btn xCNBTNDefult" data-dismiss="modal">
					<?php echo language('common/main/main', 'tModalCancel'); ?>
				</button>
            </div>
        </div>
    </div>
</div>

<!--Modal ????????????????????????????????????????????????-->
<div class="modal fade" id="odvInterfaceExportFCIsNull" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="xCNHeardModal modal-title" style="display:inline-block"><?=language('common/main/main', 'tModalWarning')?></h5>
            </div>
            <div class="modal-body">
                <p>?????????????????????????????????????????????????????????????????????????????????</p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" type="button" class="btn xCNBTNPrimery">
                    <?=language('common/main/main', 'tModalConfirm'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!--Modal ??????????????????????????????????????????????????????-->
<div class="modal fade" id="odvInterfaceExportFCTextFile" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
						<label class="xCNTextModalHeard" style="font-weight: bold; font-size: 20px;"><?=language('common/main/main', 'tModalWarning')?></label>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right">
						<button class="btn xCNBTNDefult xCNBTNDefult2Btn" data-dismiss="modal" >?????????</button>
					</div>
				</div>
            </div>
            <div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-sm-12">
						<p><b>?????????????????????????????????????????????????????? ????????????????????????????????????????????????????????????????????????????????????????????????????????????<b></p>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<!--Modal ??????????????????????????????????????????????????????-->
<div class="modal fade" id="odvInterfaceExportFCCostIsNull" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
						<label class="xCNTextModalHeard" style="font-weight: bold; font-size: 20px;"><?=language('common/main/main', 'tModalWarning')?></label>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right">
						<button class="btn xCNBTNDefult xCNBTNDefult2Btn" data-dismiss="modal" >?????????</button>
					</div>
				</div>
            </div>
            <div class="modal-body">
				<div class="row">
					<div class="col-lg-7 col-sm-7">
						<p><b>?????????????????????????????????????????????????????? ??????????????????????????? ????????????????????????????????????????????????????????? ???????????????????????????????????????????????????????????????????????????????????????????????????????????????<b></p>
					</div>
					<div class="col-lg-5 col-sm-5">
						<button type="button" class="btn xCNBTNImportFile" style="float: right; margin-bottom: 10px;" onclick="JSxIFCExportDocumentNotApv()" >
							?????????????????? Excel
						</button>
					</div>
				</div>

				<div class="row" id="odvHistoryDocumentNotApv" style="display:none;">
					<div class="col-lg-12">
						<div class="table-responsive" style="overflow-x: auto; height: 300px;">
							<table class="table" id="otbInterfaceExportFCDocNotApv" style="width:100%">
								<thead>
									<tr>
										<th>???????????????</th>
										<th>????????????????????????</th>
										<th>??????????????????</th>
										<th>????????????????????????????????????</th>
										<th>????????????????????????????????????</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>

				<br>
            </div>
        </div>
    </div>
</div>

<!--Modal ??????????????????????????????????????????????????? ???????????????????????????????????????????????????-->
<div class="modal fade" id="odvInterfaceExportFCCostIsNullButCanMoveOn" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="xCNHeardModal modal-title" style="display:inline-block"><?=language('common/main/main', 'tModalWarning')?></h5>
            </div>
            <div class="modal-body">
                <p id="ospExportCostIsNullButCanMoveOn">
					?????????????????????????????????????????????????????????????????????????????????????????????????????? ??????????????????????????????????????????????????????????????????????????????????????????????????????<br> 
					????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? ?????????????????????????????? ?
				</p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" type="button" class="btn xCNBTNPrimery" onclick="JSxIFCCallRabbitMQToExport('remove')">
                    <?=language('common/main/main', 'tModalConfirm'); ?>
                </button>
				<button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button"  data-dismiss="modal">
					<?= language('common/main/main', 'tModalCancel')?>
				</button>
            </div>
        </div>
    </div>
</div>

<!--Modal ????????????????????????????????????????????????-->
<div class="modal fade" id="odvInterfaceExportFCDateIsNull" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"> 
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="xCNHeardModal modal-title" style="display:inline-block">???????????????????????????</h5>
            </div>
            <div class="modal-body">
                <p>?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????</p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal"  id="obtIFCModalMsgConfirmIsNull" type="button" class="btn xCNBTNPrimery">
                    <?=language('common/main/main', 'tModalConfirm'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('application/modules/interface/assets/src/interfaceexportfc/jInterfaceExportFC.js')?>"></script>
<script type="text/javascript">
    $( document ).ready(function(){
        $('.xCNDatePicker').datepicker({
			format			: 'yyyy-mm-dd',
			autoclose		: true,
			todayHighlight	: true,
		});
    });

    //????????????????????????????????????????????? browse ???????????????
	var tTypeBrowse , tApiCode , tKeyInputCode , tKeyInputName;
	var nLangEdits  = '<?=$this->session->userdata("tLangEdit");?>';
    
    ///////////////////////////////////////////////  Browse ???????????????????????????????????? ///////////////////////////////////////////
        $('.xCNExBrowseCreditFrm , .xCNExBrowseCreditTo').off('click').on('click',function(){
            tTypeBrowse 	= $(this).attr('data-typebrowse');
            tApiCode 		= $(this).attr('data-apicode');
            tKeyInputCode	= "oetExCreditCode";
            tKeyInputName	= "oetExCreditName";
            var tCode 		= tKeyInputCode+tTypeBrowse+tApiCode;
            var tName 		= tKeyInputName+tTypeBrowse+tApiCode;

            window.oBrowseSPL = undefined;
            oBrowseSPL        = oBrowseSPLOption({
                'tReturnInputCode'  : tCode,
                'tReturnInputName'  : tName,
                'tNextFuncName'     : 'JSxEXAfterBrowseSPL',
                'aArgReturn'        : ['FTSplCode','FTSplName']
            });
            JCNxBrowseMultiSelect('oBrowseSPL');
        });

        var oBrowseSPLOption      = function(poDataFnc){
            var tInputReturnCode    = poDataFnc.tReturnInputCode;
            var tInputReturnName    = poDataFnc.tReturnInputName;
            var tNextFuncName       = poDataFnc.tNextFuncName;
            var aArgReturn          = poDataFnc.aArgReturn;
            var oOptionReturn       = {
                Title	: ['supplier/supplier/supplier', 'tSPLTitle'],
                Table	: {Master:'TCNMSpl', PK:'FTSplCode'},
                Join	: {
                    Table	: ['TCNMSpl_L', 'TCNMSplCredit'],
                    On		: [
                        'TCNMSpl_L.FTSplCode = TCNMSpl.FTSplCode AND TCNMSpl_L.FNLngID = '+nLangEdits,
                        'TCNMSpl_L.FTSplCode = TCNMSplCredit.FTSplCode'
                    ]
                },
                Where:{
                    Condition : ["AND TCNMSpl.FTSplStaActive = '1' "]
                },
                GrideView:{
                    ColumnPathLang	: 'supplier/supplier/supplier',
                    ColumnKeyLang	: ['tSPLTBCode', 'tSPLTBName'],
                    ColumnsSize		: ['15%', '75%'],
                    WidthModal		: 50,
                    DataColumns		: ['TCNMSpl.FTSplCode', 'TCNMSpl_L.FTSplName', 'TCNMSplCredit.FNSplCrTerm', 'TCNMSplCredit.FCSplCrLimit', 'TCNMSpl.FTSplStaVATInOrEx', 'TCNMSplCredit.FTSplTspPaid'],
                    DataColumnsFormat: ['',''],
                    DisabledColumns	: [2, 3, 4, 5],
                    Perpage			: 10,
                    OrderBy			: ['TCNMSpl_L.FTSplName ASC']
                },
                CallBack:{
                    StausAll    	: ['ohdExCreditCode'],
                    Value   		: [tInputReturnCode,"TCNMSpl.FTSplCode"],
                    Text    		: [tInputReturnName,"TCNMSpl_L.FTSplName"]
                },
            };
            return oOptionReturn;
        }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////


    //////////////////////////////////////////////  Browse ???????????? /////////////////////////////////////////////// 
        $('.xCNExBrowseBchFrm , .xCNExBrowseBchTo').off('click').on('click',function(){
            tTypeBrowse 	= $(this).attr('data-typebrowse');
            tApiCode 		= $(this).attr('data-apicode');
            tKeyInputCode	= "oetExBchCode";
            tKeyInputName	= "oetExBchName";
            var tCode 		= tKeyInputCode+tTypeBrowse+tApiCode;
            var tName 		= tKeyInputName+tTypeBrowse+tApiCode;

            window.oBrowseBCH = undefined;
            oBrowseBCH        = oBrowseBCHOption({
                'tReturnInputCode'  : tCode,
                'tReturnInputName'  : tName,
                'tNextFuncName'     : 'JSxEXAfterBrowseBCH',
                'aArgReturn'        : ['FTBchCode','FTBchName']
            });
            JCNxBrowseData('oBrowseBCH');
        });

        var oBrowseBCHOption = function(poReturnInput){
            var tNextFuncName    = poReturnInput.tNextFuncName;
            var aArgReturn       = poReturnInput.aArgReturn;
            var tInputReturnCode = poReturnInput.tReturnInputCode;
            var tInputReturnName = poReturnInput.tReturnInputName;

            var tUsrLevel     = "<?= $this->session->userdata("tSesUsrLevel"); ?>";
            var tBchCodeMulti = "<?= $this->session->userdata("tSesUsrBchCodeMulti"); ?>";
            var nCountBch     = "<?= $this->session->userdata("nSesUsrBchCount"); ?>";
            var tWhere		  = "";

            if(tUsrLevel != "HQ"){
                tWhere  = " AND TCNMBranch.FTBchCode IN ("+tBchCodeMulti+") ";
            }else{
                tWhere  = "";
            }

            var oOptionReturn    = {
                Title	: ['company/branch/branch','tBCHTitle'],
                Table	: {Master:'TCNMBranch',PK:'FTBchCode'},
                Join	: {
                    Table	: ['TCNMBranch_L'],
                    On		 :['TCNMBranch.FTBchCode = TCNMBranch_L.FTBchCode AND TCNMBranch_L.FNLngID = '+nLangEdits]
                },
                Where: {
                    Condition: ["AND TCNMBranch.FTBchStaActive	= '1' AND TCNMBranch.FTBchType = '4'"+tWhere]
                },
                GrideView:{
                    ColumnPathLang	: 'company/branch/branch',
                    ColumnKeyLang	: ['tBCHCode','tBCHName'],
                    ColumnsSize     : ['30%','70%'],
                    WidthModal      : 50,
                    DataColumns		: ['TCNMBranch.FTBchCode','TCNMBranch_L.FTBchName'],
                    DataColumnsFormat : ['',''],
                    Perpage			: 10,
                    OrderBy			: ['TCNMBranch.FTBchCode DESC'],
                },
                CallBack:{
                    ReturnType	: 'S',
                    Value		: [tInputReturnCode,"TCNMBranch.FTBchCode"],
                    Text		: [tInputReturnName,"TCNMBranch_L.FTBchName"]
                },
                NextFunc:{
                    FuncName    	: tNextFuncName,
                    ArgReturn   	: aArgReturn
                }
            };
            return oOptionReturn;
        };

        //????????????????????????????????????????????????
        function JSxEXAfterBrowseBCH(ptDataNextFunc){
            if(typeof(ptDataNextFunc) != undefined && ptDataNextFunc != "NULL"){
                aDataNextFunc   = JSON.parse(ptDataNextFunc);
                tParamFirst     = aDataNextFunc[0];
                tParamSecond	= aDataNextFunc[1];
                if(tTypeBrowse == 'Frm'){
                    var tNewTypeBrowse = 'To';
                    $('#'+tKeyInputCode+tNewTypeBrowse+tApiCode).val(tParamFirst);	
                    $('#'+tKeyInputName+tNewTypeBrowse+tApiCode).val(tParamSecond);
                }
            }else{
                var tTypeBrowseFrm 	= 'Frm';
                var tTypeBrowseTo 	= 'To';
                $('#'+tKeyInputCode+tTypeBrowseFrm+tApiCode).val('');	
                $('#'+tKeyInputName+tTypeBrowseFrm+tApiCode).val('');
                $('#'+tKeyInputCode+tTypeBrowseTo+tApiCode).val('');	
                $('#'+tKeyInputName+tTypeBrowseTo+tApiCode).val('');
            }
        }
    //////////////////////////////////////////////////////////////////////////////////////////////////////////


    ////////////////////////////////////////////  Browse ???????????????????????????????????? //////////////////////////////////////////
        $('.xCNExBrowseDocSaleFrm , .xCNExBrowseDocSaleTo').off('click').on('click',function(){
            tTypeBrowse 	= $(this).attr('data-typebrowse');
            tApiCode 		= $(this).attr('data-apicode');

            //????????????????????????????????????????????? Temp
            JSxCreateDataDocSaleToTemp();
        });

        function JSxCreateDataDocSaleToTemp(){
            $.ajax({
                type    : "POST",
                url     : "interfaceexportFilterBill",
                data    : {
                    "dDateFrm"		:	$('#oetExDateFrm'+tApiCode).val(),
                    "dDateTo"		:	$('#oetExDateTo'+tApiCode).val(),
                    "tBCHCodeFrm"	:	$('#oetExBchCodeFrm'+tApiCode).val(),
                    "tBCHCodeTo"	:   $('#oetExBchCodeTo'+tApiCode).val(),
                    "tSPLCodeFrm"	: 	$('#oetExCreditCodeFrm'+tApiCode).val(),
                    "tSPLCodeTo"	:	$('#oetExCreditCodeTo'+tApiCode).val(),
                    "tType"			:   "DocSale"
                },
                cache   : false,
                Timeout : 0,
                success: function(tResult){
                    tKeyInputCode	= "oetExDocSaleCode";
                    tKeyInputName	= "oetExDocSaleName";
                    var tCode 		= tKeyInputCode+tTypeBrowse+tApiCode;
                    var tName 		= tKeyInputName+tTypeBrowse+tApiCode;

                    window.oBrowseSALE = undefined;
                    oBrowseSALE        = oBrowseSALEOption({
                        'tReturnInputCode'  : tCode,
                        'tReturnInputName'  : tName,
                        'tNextFuncName'     : 'JSxEXAfterBrowseSALE',
                        'aArgReturn'        : ['FTXshDocNo','FTXshDocNo']
                    });
                    JCNxBrowseData('oBrowseSALE');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        }

        var oBrowseSALEOption = function(poReturnInput){
            var tNextFuncName    = poReturnInput.tNextFuncName;
            var aArgReturn       = poReturnInput.aArgReturn;
            var tInputReturnCode = poReturnInput.tReturnInputCode;
            var tInputReturnName = poReturnInput.tReturnInputName;
            var tWhere		     = "";
            var tSessionUserCode  = '<?=$this->session->userdata('tSesUserCode')?>'
            
            tWhere += " AND TCNTBrsBillTmp.FTUsrCode = '"+tSessionUserCode+"' ";
        
            var oOptionReturn    = {
                Title	: ['interface/interfaceexport/interfaceexport','tITFXDataSal'],
                Table	: {Master:'TCNTBrsBillTmp',PK:'FTXshDocNo'},
                Where	: {
                        Condition: [tWhere]
                },
                GrideView:{
                    ColumnPathLang	: 'interface/interfaceexport/interfaceexport',
                    ColumnKeyLang	: ['tITFXSalDocNo','tITFXSalDate'],
                    ColumnsSize     : ['30%','50%','20%'],
                    WidthModal      : 50,
                    DataColumns		: ['TCNTBrsBillTmp.FTXshDocNo','TCNTBrsBillTmp.FDXshDocDate'],
                    DataColumnsFormat : ['','',''],
                    Perpage			: 10,
                    OrderBy			: ['FTXshDocNo ASC'],
                },
                CallBack:{
                    ReturnType	: 'S',
                    Value		: [tInputReturnCode,"TCNTBrsBillTmp.FTXshDocNo"],
                    Text		: [tInputReturnName,"TCNTBrsBillTmp.FTXshDocNo"]
                },
                NextFunc : {
                    FuncName    : tNextFuncName,
                    ArgReturn   : aArgReturn
                }
            };
            return oOptionReturn;
        };

        //????????????????????????????????????????????????????????????????????????
        function JSxEXAfterBrowseSALE(ptDataNextFunc){
            if(typeof(ptDataNextFunc) != undefined && ptDataNextFunc != "NULL"){
                aDataNextFunc   = JSON.parse(ptDataNextFunc);
                tParamFirst     = aDataNextFunc[0];
                tParamSecond	= aDataNextFunc[1];
                if(tTypeBrowse == 'Frm'){
                    var tNewTypeBrowse = 'To';
                    $('#'+tKeyInputCode+tNewTypeBrowse+tApiCode).val(tParamFirst);	
                    $('#'+tKeyInputName+tNewTypeBrowse+tApiCode).val(tParamSecond);
                }
            }else{
                var tTypeBrowseFrm 	= 'Frm';
                var tTypeBrowseTo 	= 'To';
                $('#'+tKeyInputCode+tTypeBrowseFrm+tApiCode).val('');	
                $('#'+tKeyInputName+tTypeBrowseFrm+tApiCode).val('');
                $('#'+tKeyInputCode+tTypeBrowseTo+tApiCode).val('');	
                $('#'+tKeyInputName+tTypeBrowseTo+tApiCode).val('');
            }
        }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////


    //////////////////////////////////////////  Browse ???????????????????????????????????????????????????????????? /////////////////////////////////////
        $('.xCNExBrowseDocBillFrm').off('click').on('click',function(){
            tTypeBrowse 	= $(this).attr('data-typebrowse');
            tApiCode 		= $(this).attr('data-apicode');

            //????????????????????????????????????????????? Temp
            JSxCreateDataBillToTemp();
        });

        function JSxCreateDataBillToTemp(){
            $.ajax({
                type    : "POST",
                url     : "interfaceexportFilterBill",
                data    : {
                    "dDateFrm"		:	$('#oetExDateFrm'+tApiCode).val(),
                    "dDateTo"		:	$('#oetExDateTo'+tApiCode).val(),
                    "tBCHCodeFrm"	:	$('#oetExBchCodeFrm'+tApiCode).val(),
                    "tBCHCodeTo"	:   $('#oetExBchCodeTo'+tApiCode).val(),
                    "tSPLCodeFrm"	: 	$('#oetExCreditCodeFrm'+tApiCode).val(),
                    "tSPLCodeTo"	:	$('#oetExCreditCodeTo'+tApiCode).val(),
                    "tType"			:   "DocBill"
                },
                cache   : false,
                Timeout : 0,
                success: function(tResult){
                    tKeyInputCode	= "oetExDocBillCode";
                    tKeyInputName	= "oetExDocBillName";
                    var tCode 		= tKeyInputCode+tTypeBrowse+tApiCode;
                    var tName 		= tKeyInputName+tTypeBrowse+tApiCode;

                    window.oBrowseBill = undefined;
                    oBrowseBill        = oBrowseBillOption({
                        'tReturnInputCode'  : tCode,
                        'tReturnInputName'  : tName,
                        'tNextFuncName'     : 'JSxEXAfterBrowseBill',
                        'aArgReturn'        : ['FTXshDocNo','FTXshDocNo']
                    });
                    JCNxBrowseMultiSelect('oBrowseBill');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        }

        var oBrowseBillOption = function(poReturnInput){
            var tNextFuncName    = poReturnInput.tNextFuncName;
            var aArgReturn       = poReturnInput.aArgReturn;
            var tInputReturnCode = poReturnInput.tReturnInputCode;
            var tInputReturnName = poReturnInput.tReturnInputName;
            var tWhere		     = "";
            var tSessionUserCode  = '<?=$this->session->userdata('tSesUserCode')?>'
            
            tWhere += " AND TCNTBrsBillTmp.FTUsrCode = '"+tSessionUserCode+"' ";
        
            var oOptionReturn    = {
                Title	: ['interface/interfaceexport/interfaceexport','tITFXDataSal'],
                Table	: {Master:'TCNTBrsBillTmp',PK:'FTXshDocNo'},
                Where	: {
                        Condition: [tWhere]
                },
                GrideView:{
                    ColumnPathLang	: 'interface/interfaceexport/interfaceexport',
                    ColumnKeyLang	: ['tITFXSalDocNo','tITFXSalDate'],
                    ColumnsSize     : ['30%','50%','20%'],
                    WidthModal      : 50,
                    DataColumns		: ['TCNTBrsBillTmp.FTXshDocNo','TCNTBrsBillTmp.FDXshDocDate'],
                    DataColumnsFormat : ['','',''],
                    Perpage			: 10,
                    OrderBy			: ['FTXshDocNo ASC'],
                },
                CallBack:{
                    ReturnType	: 'S',
                    Value		: [tInputReturnCode,"TCNTBrsBillTmp.FTXshDocNo"],
                    Text		: [tInputReturnName,"TCNTBrsBillTmp.FTXshDocNo"]
                },
                /*NextFunc : {
                    FuncName    : tNextFuncName,
                    ArgReturn   : aArgReturn
                }*/
            };
            return oOptionReturn;
        };

        //????????????????????????????????????????????????????????????????????????????????????????????????
        function JSxEXAfterBrowseBill(ptDataNextFunc){
            if(typeof(ptDataNextFunc) != undefined && ptDataNextFunc != "NULL"){
                aDataNextFunc   = JSON.parse(ptDataNextFunc);
                tParamFirst     = aDataNextFunc[0];
                tParamSecond	= aDataNextFunc[1];
                if(tTypeBrowse == 'Frm'){
                    var tNewTypeBrowse = 'To';
                    $('#'+tKeyInputCode+tNewTypeBrowse+tApiCode).val(tParamFirst);	
                    $('#'+tKeyInputName+tNewTypeBrowse+tApiCode).val(tParamSecond);
                }
            }else{
                var tTypeBrowseFrm 	= 'Frm';
                var tTypeBrowseTo 	= 'To';
                $('#'+tKeyInputCode+tTypeBrowseFrm+tApiCode).val('');	
                $('#'+tKeyInputName+tTypeBrowseFrm+tApiCode).val('');
                $('#'+tKeyInputCode+tTypeBrowseTo+tApiCode).val('');	
                $('#'+tKeyInputName+tTypeBrowseTo+tApiCode).val('');
            }
        }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////


    //////////////////////////////////////////  Browse ?????????????????????????????????????????????????????? //////////////////////////////////////
        $('.xCNExBrowseDocIV').off('click').on('click',function(){
            tApiCode 		= $(this).attr('data-apicode');

            //????????????????????????????????????????????? Temp
            JSxCreateDataIVToTemp();
        });

        function JSxCreateDataIVToTemp(){
            $.ajax({
                type    : "POST",
                url     : "interfaceexportFilterBill",
                data    : {
                    "dDateFrm"		:	$('#oetExDateFrm'+tApiCode).val(),
                    "dDateTo"		:	$('#oetExDateTo'+tApiCode).val(),
                    "tBCHCodeFrm"	:	$('#oetExBchCodeFrm'+tApiCode).val(),
                    "tBCHCodeTo"	:   $('#oetExBchCodeTo'+tApiCode).val(),
                    "tSPLCodeFrm"	: 	$('#oetExCreditCodeFrm'+tApiCode).val(),
                    "tType"			:   "DocIV"
                },
                cache   : false,
                Timeout : 0,
                success: function(tResult){
                    tKeyInputCode	= "oetExDocIVCode";
                    tKeyInputName	= "oetExDocIVName";
                    var tCode 		= tKeyInputCode+tApiCode;
                    var tName 		= tKeyInputName+tApiCode;

                    window.oBrowseIV = undefined;
                    oBrowseIV        = oBrowseIVOption({
                        'tReturnInputCode'  : tCode,
                        'tReturnInputName'  : tName,
                        'tNextFuncName'     : 'JSxEXAfterBrowseIV',
                        'aArgReturn'        : ['FTXshDocNo','FTXshDocNo']
                    });
                    JCNxBrowseMultiSelect('oBrowseIV');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        }

        var oBrowseIVOption = function(poReturnInput){
            var tNextFuncName    	= poReturnInput.tNextFuncName;
            var aArgReturn       	= poReturnInput.aArgReturn;
            var tInputReturnCode 	= poReturnInput.tReturnInputCode;
            var tInputReturnName 	= poReturnInput.tReturnInputName;
            var tWhere		     	= "";
            var tSessionUserCode  	= '<?=$this->session->userdata('tSesUserCode')?>'
            
            tWhere += " AND TCNTBrsBillTmp.FTUsrCode = '"+tSessionUserCode+"' ";
        
            var oOptionReturn    = {
                Title	: ['interface/interfaceexport/interfaceexport','tITFXDataSal'],
                Table	: {Master:'TCNTBrsBillTmp',PK:'FTXshDocNo'},
                Where	: {
                        Condition: [tWhere]
                },
                GrideView:{
                    ColumnPathLang	: 'interface/interfaceexport/interfaceexport',
                    ColumnKeyLang	: ['tITFXSalDocNo','tITFXSalDate'],
                    ColumnsSize     : ['30%','50%','20%'],
                    WidthModal      : 50,
                    DataColumns		: ['TCNTBrsBillTmp.FTXshDocNo','TCNTBrsBillTmp.FDXshDocDate'],
                    DataColumnsFormat : ['','',''],
                    Perpage			: 10,
                    OrderBy			: ['FTXshDocNo ASC'],
                },
                CallBack:{
                    Value		: [tInputReturnCode,"TCNTBrsBillTmp.FTXshDocNo"],
                    Text		: [tInputReturnName,"TCNTBrsBillTmp.FTXshDocNo"]
                },
                /*NextFunc : {
                    FuncName    : tNextFuncName,
                    ArgReturn   : aArgReturn
                }*/
            };
            return oOptionReturn;
        };
    ///////////////////////////////////////////////////////////////////////////////////////////////////////


    //////////////////////////////////////////  Browse ??????????????????????????? ////////////////////////////////////////// 
        $('.xCNExBrowsePromotionFrm , .xCNExBrowsePromotionTo').off('click').on('click',function(){
            tTypeBrowse 	= $(this).attr('data-typebrowse');
            tApiCode 		= $(this).attr('data-apicode');
            tKeyInputCode	= "oetExPromotionCode";
            tKeyInputName	= "oetExPromotionName";
            var tCode 		= tKeyInputCode+tTypeBrowse+tApiCode;
            var tName 		= tKeyInputName+tTypeBrowse+tApiCode;

            window.oBrowsePromotion = undefined;
            oBrowsePromotion        = oBrowsePromotionOption({
                'tReturnInputCode'  : tCode,
                'tReturnInputName'  : tName,
                'tNextFuncName'     : 'JSxEXAfterBrowsePromotion',
                'aArgReturn'        : ['FTPmhDocNo','FTPmhName']
            });
            JCNxBrowseData('oBrowsePromotion');
        });

        var oBrowsePromotionOption = function(poReturnInput){
            var tNextFuncName    = poReturnInput.tNextFuncName;
            var aArgReturn       = poReturnInput.aArgReturn;
            var tInputReturnCode = poReturnInput.tReturnInputCode;
            var tInputReturnName = poReturnInput.tReturnInputName;

            var tUsrLevel     = "<?= $this->session->userdata("tSesUsrLevel"); ?>";
            var tBchCodeMulti = "<?= $this->session->userdata("tSesUsrBchCodeMulti"); ?>";
            var nCountBch     = "<?= $this->session->userdata("nSesUsrBchCount"); ?>";
            var tWhere		  = "";

            if(tUsrLevel != "HQ"){
                tWhere = " AND TCNTPdtPmtHD.FTBchCode IN ("+tBchCodeMulti+") ";
            }else{
                tWhere = "";
            }

            var oOptionReturn    = {
                Title	: ['document/promotion/promotion','tTitle'],
                Table	: {Master:'TCNTPdtPmtHD',PK:'FTPmhDocNo'},
                Join	: {
                    Table	: ['TCNTPdtPmtHD_L'],
                    On		 :['TCNTPdtPmtHD.FTPmhDocNo = TCNTPdtPmtHD_L.FTPmhDocNo AND TCNTPdtPmtHD.FTBchCode = TCNTPdtPmtHD_L.FTBchCode AND TCNTPdtPmtHD_L.FNLngID = '+nLangEdits]
                },
                Where: {
                    Condition: [tWhere]
                },
                GrideView:{
                    ColumnPathLang	: 'document/promotion/promotion',
                    ColumnKeyLang	: ['tBCH','tTBDocNo','tPromotionName'],
                    ColumnsSize     : ['20%','30%','70%'],
                    WidthModal      : 50,
                    DataColumns		: ['TCNTPdtPmtHD.FTBchCode','TCNTPdtPmtHD.FTPmhDocNo','TCNTPdtPmtHD_L.FTPmhName'],
                    DataColumnsFormat : ['','',''],
                    Perpage			: 10,
                    OrderBy			: ['TCNTPdtPmtHD.FDCreateOn DESC'],
                },
                CallBack:{
                    ReturnType	: 'S',
                    Value		: [tInputReturnCode,"TCNTPdtPmtHD.FTPmhDocNo"],
                    Text		: [tInputReturnName,"TCNTPdtPmtHD_L.FTPmhName"]
                },
                NextFunc:{
                    FuncName    	: tNextFuncName,
                    ArgReturn   	: aArgReturn
                }
            };
            return oOptionReturn;
        };

        //???????????????????????????????????????????????????????????????
        function JSxEXAfterBrowsePromotion(ptDataNextFunc){
            if(typeof(ptDataNextFunc) != undefined && ptDataNextFunc != "NULL"){
                aDataNextFunc   = JSON.parse(ptDataNextFunc);
                tParamFirst     = aDataNextFunc[0];
                tParamSecond	= aDataNextFunc[1];
                if(tTypeBrowse == 'Frm'){
                    var tNewTypeBrowse = 'To';
                    $('#'+tKeyInputCode+tNewTypeBrowse+tApiCode).val(tParamFirst);	
                    $('#'+tKeyInputName+tNewTypeBrowse+tApiCode).val(tParamSecond);
                }
            }else{
                var tTypeBrowseFrm 	= 'Frm';
                var tTypeBrowseTo 	= 'To';
                $('#'+tKeyInputCode+tTypeBrowseFrm+tApiCode).val('');	
                $('#'+tKeyInputName+tTypeBrowseFrm+tApiCode).val('');
                $('#'+tKeyInputCode+tTypeBrowseTo+tApiCode).val('');	
                $('#'+tKeyInputName+tTypeBrowseTo+tApiCode).val('');
            }
        }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////

    //////////////////////////////////////////  Browse ????????????????????????????????? ////////////////////////////////////////// 
        $('.xCNExBrowseGrpPDTFrm , .xCNExBrowseGrpPDTTo').off('click').on('click',function(){
            tTypeBrowse 	= $(this).attr('data-typebrowse');
            tApiCode 		= $(this).attr('data-apicode');
            tKeyInputCode	= "oetExGrpPDTCode";
            tKeyInputName	= "oetExGrpPDTName";
            var tCode 		= tKeyInputCode+tTypeBrowse+tApiCode;
            var tName 		= tKeyInputName+tTypeBrowse+tApiCode;

            window.oBrowseGroupPDT = undefined;
            oBrowseGroupPDT        = oBrowseGroupPDTOption({
                'tReturnInputCode'  : tCode,
                'tReturnInputName'  : tName,
                'tNextFuncName'     : 'JSxEXAfterBrowseGroupPDT',
                'aArgReturn'        : ['FTPgpChain','FTPgpName']
            });
            JCNxBrowseData('oBrowseGroupPDT');
        });

        var oBrowseGroupPDTOption = function(poReturnInput){
            // var tNextFuncName    = poReturnInput.tNextFuncName;
            // var aArgReturn       = poReturnInput.aArgReturn;
            // var tInputReturnCode = poReturnInput.tReturnInputCode;
            // var tInputReturnName = poReturnInput.tReturnInputName;

            // var tUsrLevel     = "<?= $this->session->userdata("tSesUsrLevel"); ?>";
            // var tBchCodeMulti = "<?= $this->session->userdata("tSesUsrBchCodeMulti"); ?>";
            // var nCountBch     = "<?= $this->session->userdata("nSesUsrBchCount"); ?>";
            // var tWhere		  = "";

            // if(tUsrLevel != "HQ"){
            // 	tWhere = " AND TCNTPdtPmtHD.FTAgnCode IN ("+tBchCodeMulti+") ";
            // }else{
            // 	tWhere = "";
            // }

            // var oOptionReturn    = {
            //     Title	: ['document/promotion/promotion','tTitle'],
            //     Table	: {Master:'TCNMPdtGrp',PK:'FTPmhDocNo'},
            // 	Join	: {
            //         Table	: ['TCNMPdtGrp_L'],
            //         On		 :['TCNMPdtGrp.FTPgpChain = TCNMPdtGrp_L.FTPgpChain AND TCNMPdtGrp_L.FNLngID = '+nLangEdits]
            // 	},
            //     GrideView:{
            //         ColumnPathLang	: 'document/promotion/promotion',
            //         ColumnKeyLang	: ['tBCH','tTBDocNo'],
            //         ColumnsSize     : ['20%','30%'],
            //         WidthModal      : 50,
            //         DataColumns		: ['TCNMPdtGrp.FTPgpChain','TCNMPdtGrp_L.FTPgpName'],
            //         DataColumnsFormat : ['','',''],
            //         Perpage			: 10,
            //         OrderBy			: ['TCNMPdtGrp.FDCreateOn DESC'],
            //     },
            //     CallBack:{
            //         ReturnType	: 'S',
            //         Value		: [tInputReturnCode,"TCNMPdtGrp.FTPmhDocNo"],
            //         Text		: [tInputReturnName,"TCNMPdtGrp_L.FTPmhName"]
            // 	},
            // 	NextFunc:{
            // 		FuncName    	: tNextFuncName,
            // 		ArgReturn   	: aArgReturn
            // 	}
            // };
            // return oOptionReturn;
        };

        //?????????????????????????????????????????????????????????????????????
        function JSxEXAfterBrowseGroupPDT(ptDataNextFunc){
            if(typeof(ptDataNextFunc) != undefined && ptDataNextFunc != "NULL"){
                aDataNextFunc   = JSON.parse(ptDataNextFunc);
                tParamFirst     = aDataNextFunc[0];
                tParamSecond	= aDataNextFunc[1];
                if(tTypeBrowse == 'Frm'){
                    var tNewTypeBrowse = 'To';
                    $('#'+tKeyInputCode+tNewTypeBrowse+tApiCode).val(tParamFirst);	
                    $('#'+tKeyInputName+tNewTypeBrowse+tApiCode).val(tParamSecond);
                }
            }else{
                var tTypeBrowseFrm 	= 'Frm';
                var tTypeBrowseTo 	= 'To';
                $('#'+tKeyInputCode+tTypeBrowseFrm+tApiCode).val('');	
                $('#'+tKeyInputName+tTypeBrowseFrm+tApiCode).val('');
                $('#'+tKeyInputCode+tTypeBrowseTo+tApiCode).val('');	
                $('#'+tKeyInputName+tTypeBrowseTo+tApiCode).val('');
            }
        }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////  ???????????????????????????????????? ///////////////////////////////////////////////
        $('#obtInterfaceExportFCConfirm').off('click').on('click',function(){
            var nImpportFile    = $('.progress-bar-chekbox:checked').length;
            if(nImpportFile > 0){
                var nStatusCallRabbitMQ = 0;
                $('.progress-bar-chekbox:checked').each(function(i, obj) {
                    var nDatePicker = $(this).parent().parent().find('.xCNDatePicker').length;
                    if(nDatePicker > 0){
                        for(i=0; i<nDatePicker; i++){
                            var nValue  = $(this).parent().parent().find('.xCNDatePicker:eq('+i+')').val();
                            if(nValue == '' || nValue == null){
                                $('#odvInterfaceExportFCDateIsNull').modal('show');
                                var oElement    = $(this).parent().parent().find('.xCNDatePicker:eq('+i+')');
                                $('#obtIFCModalMsgConfirmIsNull').off('click').on('click',function(){
                                    $(oElement).focus();
                                });
                                nStatusCallRabbitMQ = 1;
                                return;
                            }
                        }
                    }
                });
                if(nStatusCallRabbitMQ == 0){
                    // JCNxOpenLoading(); 
                    JSxIFCCallRabbitMQ();
                }
            } else {
                $('#odvInterfaceExportFCIsNull').modal('show');
            }
        });

        // ??????????????????????????????????????????????????????????????????????????????????????????????????? ???????????????????????????????????????-??????????????????
        $('#obtIFCModalMsgConfirm').off('click');
        $('#obtIFCModalMsgConfirm').on('click',function(){
            setTimeout(function(){
                $.ajax({
                    type    : "POST",
                    url     : "interfacehistory/0/0",
                    data    : {},
                    cache   : false,
                    Timeout : 0,
                    success: function(tResult){
                        $('.odvMainContent').html(tResult);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        JCNxResponseError(jqXHR, textStatus, errorThrown);
                    }
                });
            },100);
        });

        // ????????????????????????????????? modal
        $('#obtIFCModalMsgCancel').off('click');
        $('#obtIFCModalMsgCancel').on('click',function(){
            $('#obtInterfaceExportFCConfirm').attr('disabled', false);
        });

        // ?????????????????????????????????????????????
        $('#ocmINMExportAll').change(function(){
            var bStatus = $(this).is(":checked") ? true : false;
            if(bStatus == false){
                $('.xCNCheckBoxExport').prop("checked",false);
            }else{
                $('.xCNCheckBoxExport').prop("checked",true)
            }
        });

        // ???????????? ????????????????????????????????????????????????
        function JSvInterfaceExportFCCallPage(){
            $.ajax({
                type    : "GET",
                url     : "interfaceexportFC/0/0",
                data    : {},
                cache   : false,
                timeout : 5000,
                success : function (tResult) {
                    $('.odvMainContent').html(tResult);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////

</script>
