<div class="panel panel-headline">
    <div class="panel-heading">
        <div class="row">
            <!--ค้นหาธรรมดา-->
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <div class="form-group">
                    <div class="input-group">
                        <input
                            class="form-control xCNInputWithoutSingleQuote"
                            type="text"
                            id="oetSearchAll"
                            name="oetSearchAll"
                            placeholder="<?=language('common/main/main','tPlaceholder')?>"
                            onkeyup="Javascript:if(event.keyCode==13) JSvIVCallPageDataTable()"
                            autocomplete="off"
                        >
                        <span class="input-group-btn">
                            <button type="button" class="btn xCNBtnDateTime" onclick="JSvIVCallPageDataTable()">
                                <img class="xCNIconSearch">
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <!--ค้นหาขั้นสูง-->
            <a id="oahIVAdvanceSearch" class="btn xCNBTNDefult xCNBTNDefult1Btn" href="javascript:;"><?=language('common/main/main', 'tAdvanceSearch'); ?></a>
            <!--ล้างข้อมูลค้นหา-->
            <a class="btn xCNBTNDefult xCNBTNDefult1Btn" href="javascript:;" onclick="JSxIVClearSearchData()"><?=language('common/main/main', 'tClearSearch'); ?></a>
        </div>
        <!--ค้นหาขั้นสูง-->
        <div id="odvIVAdvanceSearchContainer" class="hidden" style="margin-bottom:20px;">
            <form id="ofmIVFromSerchAdv" class="validate-form" action="javascript:void(0)" method="post">
                <div class="row">
                    <!-- From Search Advanced Branch -->
                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                        <label class="xCNLabelFrm"><?=language('document/deliveryorder/deliveryorder','tDOAdvSearchBranch'); ?></label>
                        <div class="input-group">
                            <?php
                                if ( $this->session->userdata("tSesUsrLevel") != "HQ" ){
                                    if( $this->session->userdata("nSesUsrBchCount") <= 1 ){ //ค้นหาขั้นสูง
                                        $tBCHCode 	= $this->session->userdata("tSesUsrBchCodeDefault");
                                        $tBCHName 	= $this->session->userdata("tSesUsrBchNameDefault");
                                    }else{
                                        $tBCHCode   = '';
                                        $tBCHName 	= '';
                                    }
                                }else{
                                    $tBCHCode 		= '';
                                    $tBCHName 		= '';
                                }
                            ?>
                            <input class="form-control xCNHide" id="oetBchCodeFrom" name="oetBchCodeFrom" maxlength="5" value="<?= $tBCHCode; ?>">
                            <input 
                                class="form-control xWPointerEventNone" 
                                type="text" id="oetBchNameFrom" 
                                name="oetBchNameFrom" 
                                placeholder="<?=language('document/deliveryorder/deliveryorder','tDOAdvSearchFrom'); ?>" 
                                readonly
                                value="<?= $tBCHName; ?>"
                            >
                            <span class="input-group-btn">
                                <button id="obtIVBrowseBchFrom" type="button" class="btn xCNBtnBrowseAddOn">
                                    <img src="<?=base_url() . 'application/modules/common/assets/images/icons/find-24.png' ?>">
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                        <label class="xCNLabelFrm"><?=language('document/deliveryorder/deliveryorder','tDOAdvSearchTo'); ?></label>
                        <div class="input-group">
                            <input class="form-control xCNHide" id="oetBchCodeTo" name="oetBchCodeTo" maxlength="5" value="<?= $tBCHCode; ?>"> 
                            <input 
                                class="form-control xWPointerEventNone" 
                                type="text" 
                                id="oetBchNameTo" 
                                name="oetBchNameTo" 
                                placeholder="<?=language('document/deliveryorder/deliveryorder','tDOAdvSearchTo'); ?>" 
                                readonly
                                value="<?= $tBCHName; ?>"
                            >
                            <span class="input-group-btn">
                                <button id="obtIVBrowseBchTo" type="button" class="btn xCNBtnBrowseAddOn">
                                    <img src="<?=base_url() . 'application/modules/common/assets/images/icons/find-24.png' ?>">
                                </button>
                            </span>
                        </div>
                    </div>
                    <!-- From Search Advanced Agency -->
                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                        <div class="form-group">
                            <label class="xCNLabelFrm"><?=language('document/purchaseorder/purchaseorder','tPOPanelAgency'); ?></label>
                            <div class="input-group">
                                <?php
                                    if ( $this->session->userdata("tSesUsrLevel") != "HQ" ){
                                        $tSatAgnCode    = $this->session->userdata('tSesUsrAgnCode');
                                        $tSatAgnName    = $this->session->userdata('tSesUsrAgnName');
                                        $tDisabled      = 'disabled';
                                    } else {
                                        $tSatAgnCode    ='';
                                        $tSatAgnName    ='';
                                        $tDisabled      = '';
                                    }
                                ?>
                                <input class="form-control xCNHide" type="text" id="oetAdvSearchAgnCode" name="oetAdvSearchAgnCode" maxlength="5" value="<?php echo $tSatAgnCode?>">
                                <input
                                    class="form-control xWPointerEventNone"
                                    type="text"
                                    id="oetAdvSearchAgnName"
                                    name="oetAdvSearchAgnName"
                                    placeholder="<?php echo language('document/deliveryorder/deliveryorder','tPOPanelAgency'); ?>"
                                    readonly
                                    value="<?php echo $tSatAgnName?>"
                                >
                                <span class="input-group-btn">
                                    <button id="obtIVAdvSearchBrowseAgn" type="button" class="btn xCNBtnBrowseAddOn" <?php echo $tDisabled?>><img class="xCNIconFind"></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- From Search Advanced  Supplier -->
                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                        <div class="form-group">
                            <label class="xCNLabelFrm"><?=language('document/purchaseorder/purchaseorder','tPOPanelSupplier'); ?></label>
                            <div class="input-group">
                                <input class="form-control xCNHide" type="text" id="oetAdvSearchSplCode" name="oetAdvSearchSplCode" maxlength="5">
                                <input
                                    class="form-control xWPointerEventNone"
                                    type="text"
                                    id="oetAdvSearchSplName"
                                    name="oetAdvSearchSplName"
                                    placeholder="<?php echo language('document/deliveryorder/deliveryorder','tPOPanelSupplier'); ?>"
                                    readonly
                                >
                                <span class="input-group-btn">
                                    <button id="obtIVAdvSearchBrowseSpl" type="button" class="btn xCNBtnBrowseAddOn"><img class="xCNIconFind"></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- From Search Advanced  DocDate -->
                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                        <div class="form-group">
                            <label class="xCNLabelFrm"><?=language('document/deliveryorder/deliveryorder','tDOAdvSearchDocDate'); ?></label>
                            <div class="input-group">
                                <input 
                                    class="form-control input100 xCNDatePicker" 
                                    type="text" id="oetSearchDocDateFrom" 
                                    name="oetSearchDocDateFrom" 
                                    placeholder="<?=language('document/deliveryorder/deliveryorder', 'tDOAdvSearchDateFrom'); ?>"
                                >
                                <span class="input-group-btn">
                                    <button id="obtSearchDocDateFrom" type="button" class="btn xCNBtnDateTime">
                                        <img src="<?=base_url(); ?>application/modules/common/assets/images/icons/icons8-Calendar-100.png">
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                        <div class="form-group">
                            <label class="xCNLabelFrm"><?=language('document/deliveryorder/deliveryorder', 'tDOAdvSearchDateTo'); ?></label>
                            <div class="input-group">
                                <input 
                                    class="form-control input100 xCNDatePicker" 
                                    type="text" id="oetSearchDocDateTo" 
                                    name="oetSearchDocDateTo" 
                                    placeholder="<?=language('document/deliveryorder/deliveryorder', 'tDOAdvSearchDateTo'); ?>"
                                >
                                <span class="input-group-btn">
                                    <button id="obtSearchDocDateTo" type="button" class="btn xCNBtnDateTime">
                                        <img src="<?=base_url(); ?>application/modules/common/assets/images/icons/icons8-Calendar-100.png">
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- From Search Advanced Status Doc -->
                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                        <div class="form-group">
                            <label class="xCNLabelFrm"><?=language('document/topupVending/topupVending', 'tTBStaDoc'); ?></label>
                            <select class="selectpicker form-control" id="ocmStaDoc" name="ocmStaDoc">
                                <option value='0'><?=language('common/main/main', 'tStaDocAll'); ?></option>
                                <option value='1'><?=language('common/main/main', 'tStaDocApv'); ?></option>
                                <option value='2'><?=language('common/main/main', 'tStaDocPendingApv'); ?></option>
                                <option value='3'><?=language('common/main/main', 'tStaDocCancel'); ?></option>
                            </select>
                        </div>
                    </div>
                    <!-- From Search Advanced Status Approve -->
                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                        <div class="form-group">
                            <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder','tPOAdvSearchStaApprove'); ?></label>
                            <select class="selectpicker form-control" id="ocmStaApprove" name="ocmStaApprove">
                                <option value='0'><?php echo language('common/main/main', 'tAll'); ?></option>
                                <option value='1'><?php echo language('common/main/main', 'tStaDocProcessor'); ?></option>
                                <option value='2'><?php echo language('common/main/main', 'tStaDocProcessing'); ?></option>
                                <option value='3'><?php echo language('common/main/main', 'tStaDocPendingProcessing'); ?></option>
                            </select>
                        </div>    
                    </div>
                    <!-- From Search Advanced Status Act -->
                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                        <div class="form-group">
                            <label class="xCNLabelFrm">สถานะ<?=language('common/main/main', 'tStaDocAct'); ?></label>
                            <select class="selectpicker form-control" id="ocmStaDocAct" name="ocmStaDocAct">
                                <option value='0' selected><?=language('common/main/main', 'tAll'); ?></option>
                                <option value='1'><?=language('common/main/main', 'tStaDocActMove'); ?></option>
                                <option value='2'><?=language('common/main/main', 'tStaDocActNotMoving'); ?></option>
                            </select>
                        </div>
                    </div>
                    <!-- Button Form Search Advanced -->
                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                        <div class="form-group" style="width:60%;">
                            <label class="xCNLabelFrm">&nbsp;</label>
                            <button id="oahIVAdvanceSearchSubmit" class="btn xCNBTNPrimery" style="width:100%" onclick="JSvIVCallPageDataTable()"><?php echo language('common/main/main', 'tSearch'); ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-8 col-sm-4 col-md-4 col-lg-4">
            </div>
            <!--ตัวเลือกลบหลายตัว-->
            <div class="col-xs-4 col-md-8 col-lg-8 text-right" style="margin-top:-35px;">
                <div id="odvMngTableList" class="btn-group xCNDropDrownGroup">
                    <button type="button" class="btn xCNBTNMngTable" data-toggle="dropdown">
                        <?=language('common/main/main','tCMNOption')?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li id="oliBtnDeleteAll" class="disabled">
                            <a data-toggle="modal" data-target="#odvIVModalDelDocMultiple"><?= language('common/main/main','tDelAll')?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <!--Content-->
		<section id="ostContentIV"></section>
	</div>
</div>

<script src="<?=base_url('application/modules/common/assets/js/jquery.mask.js')?>"></script>
<script src="<?=base_url('application/modules/common/assets/src/jFormValidate.js')?>"></script>
<?php include('script/jAdvancedSearch.php')?>