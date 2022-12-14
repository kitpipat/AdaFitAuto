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
                            onkeyup="Javascript:if(event.keyCode==13) JSvQTCallPageDataTable()"
                            autocomplete="off"
                        >
                        <span class="input-group-btn">
                            <button type="button" class="btn xCNBtnDateTime" onclick="JSvQTCallPageDataTable()">
                                <img class="xCNIconSearch">
                            </button>
                        </span>
                    </div>
                </div>
            </div>

            <!--ค้นหาขั้นสูง-->
            <a id="oahTQAdvanceSearch" class="btn xCNBTNDefult xCNBTNDefult1Btn" href="javascript:;"><?=language('common/main/main', 'tAdvanceSearch'); ?></a>

            <!--ล้างข้อมูลค้นหา-->
            <a class="btn xCNBTNDefult xCNBTNDefult1Btn" href="javascript:;" onclick="JSxTQClearSearchData()"><?=language('common/main/main', 'tClearSearch'); ?></a>
        </div>

        <!--ค้นหาขั้นสูง-->
        <div class="row hidden" id="odvTQAdvanceSearchContainer" style="margin-bottom:20px;">
            <div class="col-xs-12 col-md-6 col-lg-6">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-xs-6">
                        <label class="xCNLabelFrm"><?php echo language('document/adjuststock/adjuststock', 'tASTAdvSearchBranch'); ?></label>
                        <div class="form-group">
                            <?php
                                if ( $this->session->userdata("tSesUsrLevel") != "HQ" ){
                                    if( $this->session->userdata("nSesUsrBchCount") <= 1 ){ //ค้นหาขั้นสูง
                                        $tBCHCode 	= $this->session->userdata("tSesUsrBchCodeDefault");
                                        $tBCHName 	= $this->session->userdata("tSesUsrBchNameDefault");
                                    }else{
                                        $tBCHCode 	= '';
                                        $tBCHName 	= '';
                                    }
                                }else{
                                    $tBCHCode 		= '';
                                    $tBCHName 		= '';
                                }
                            ?>
                            <div class="input-group">
                                <input class="form-control xCNHide" id="oetBchCodeFrom" name="oetBchCodeFrom" maxlength="5" value="<?= $tBCHCode; ?>">
                                <input 
                                class="form-control xWPointerEventNone" 
                                type="text" id="oetBchNameFrom" 
                                name="oetBchNameFrom" 
                                placeholder="<?php echo language('document/topupVending/topupVending', 'tFrom'); ?>" 
                                readonly
                                value="<?= $tBCHName; ?>">
                                <span class="input-group-btn">
                                    <button id="obtTQBrowseBchFrom" type="button" class="btn xCNBtnBrowseAddOn">
                                        <img src="<?php echo  base_url() . 'application/modules/common/assets/images/icons/find-24.png' ?>">
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-xs-6">
                        <label class="xCNLabelFrm"><?php echo language('document/adjuststock/adjuststock', 'tASTAdvSearchBranchTo'); ?></label>
                        <div class="form-group">
                            <div class="input-group">
                                <input class="form-control xCNHide" id="oetBchCodeTo" name="oetBchCodeTo" maxlength="5" value="<?= $tBCHCode; ?>"> 
                                <input 
                                class="form-control xWPointerEventNone" 
                                type="text" 
                                id="oetBchNameTo" 
                                name="oetBchNameTo" 
                                placeholder="<?php echo language('document/topupVending/topupVending', 'tTo'); ?>" 
                                readonly
                                value="<?= $tBCHName; ?>">
                                <span class="input-group-btn">
                                    <button id="obtTQBrowseBchTo" type="button" class="btn xCNBtnBrowseAddOn">
                                        <img src="<?php echo  base_url() . 'application/modules/common/assets/images/icons/find-24.png' ?>">
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6 col-lg-6">
                <div class="row">

                    <div class="col-lg-6 col-md-6 col-xs-6">
                        <label class="xCNLabelFrm"><?php echo language('document/topupVending/topupVending', 'tTBDocDate'); ?></label>
                        <div class="form-group">
                            <div class="input-group">
                                <input 
                                class="form-control input100 xCNDatePicker" 
                                type="text" id="oetSearchDocDateFrom" 
                                name="oetSearchDocDateFrom" 
                                placeholder="<?php echo language('document/topupVending/topupVending', 'tFrom'); ?>">
                                <span class="input-group-btn">
                                    <button id="obtSearchDocDateFrom" type="button" class="btn xCNBtnDateTime">
                                        <img src="<?php echo base_url(); ?>application/modules/common/assets/images/icons/icons8-Calendar-100.png">
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-xs-6">
                        <label class="xCNLabelFrm"><?php echo language('document/topupVending/topupVending', 'tTo'); ?><?php echo language('document/topupVending/topupVending', 'tTBDocDate'); ?></label>
                        <div class="form-group">
                            <div class="input-group">
                                <input 
                                class="form-control input100 xCNDatePicker" 
                                type="text" id="oetSearchDocDateTo" 
                                name="oetSearchDocDateTo" 
                                placeholder="<?php echo language('document/topupVending/topupVending', 'tTo'); ?>">
                                <span class="input-group-btn">
                                    <button id="obtSearchDocDateTo" type="button" class="btn xCNBtnDateTime">
                                        <img src="<?php echo base_url(); ?>application/modules/common/assets/images/icons/icons8-Calendar-100.png">
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                <div class="col-lg-12 col-md-12 col-xs-12 no-padding">
                    <label class="xCNLabelFrm"><?php echo language('document/topupVending/topupVending', 'tTBStaDoc'); ?></label>
                </div>
                <div class="form-group">
                    <select class="selectpicker form-control" id="ocmStaDoc" name="ocmStaDoc">
                    <option value='0'><?php echo language('common/main/main', 'tStaDocAll'); ?></option>
                        <option value='1'><?php echo language('common/main/main', 'tStaDocApv'); ?></option>
                        <option value='2'><?php echo language('common/main/main', 'tStaDocPendingApv'); ?></option>
                        <option value='3'><?php echo language('common/main/main', 'tStaDocCancel'); ?></option>
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="xCNLabelFrm"><?php echo language('common/main/main', 'tStaDocAct'); ?></label>
                    <select class="selectpicker form-control" id="ocmStaDocAct" name="ocmStaDocAct">
                        <option value='0' selected><?php echo language('common/main/main', 'tAll'); ?></option>
                        <option value='1'><?php echo language('common/main/main', 'tStaDocActMove'); ?></option>
                        <option value='2'><?php echo language('common/main/main', 'tStaDocActNotMoving'); ?></option>
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <div class="form-group" style="width: 60%;">
                    <label class="xCNLabelFrm">&nbsp;</label>
                    <button id="oahTQAdvanceSearchSubmit" class="btn xCNBTNPrimery" style="width:100%" onclick="JSvQTCallPageDataTable()"><?php echo language('common/main/main', 'tSearch'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-8 col-sm-4 col-md-4 col-lg-4">
            </div>
            <!--ตัวเลือกลบหลายตัว-->
            <div class="col-xs-4 col-sm-8 col-md-8 col-lg-8 text-right" style="margin-top:-35px;">
                <div id="odvMngTableList" class="btn-group xCNDropDrownGroup">
                    <button type="button" class="btn xCNBTNMngTable" data-toggle="dropdown">
                        <?=language('common/main/main','tCMNOption')?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li id="oliBtnDeleteAll" class="disabled">
                            <a data-toggle="modal" data-target="#odvQTModalDelDocMultiple"><?= language('common/main/main','tDelAll')?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <!--Content-->
		<section id="ostContentTQ"></section>
	</div>
</div>

<script src="<?=base_url('application/modules/common/assets/js/jquery.mask.js')?>"></script>
<script src="<?=base_url('application/modules/common/assets/src/jFormValidate.js')?>"></script>
<?php include('script/jAdvancedSearch.php')?>