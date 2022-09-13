<div class="panel panel-headline">
    <div class="panel-heading">
        <div class="row">
            <!-- ค้นหาธรรมดา -->
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-4">
                <div class="form-group">
                    <div class="input-group">
                        <input
                            class="form-control xCNInputWithoutSingleQuote"
                            type="text"
                            id="oetSearchAll"
                            name="oetSearchAll"
                            placeholder="<?=language('common/main/main','tPlaceholder')?>"
                            onkeyup="Javascript:if(event.keyCode==13) JSvTRMCallPageDataTable()"
                            autocomplete="off"
                        >
                        <span class="input-group-btn">
                            <button type="button" class="btn xCNBtnDateTime" onclick="JSvTRMCallPageDataTable()">
                                <img class="xCNIconSearch">
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <!-- ค้นหาขั้นสูง -->
            <a id="oahTRMAdvanceSearch" class="btn xCNBTNDefult xCNBTNDefult1Btn" href="javascript:;"><?=language('common/main/main', 'tAdvanceSearch'); ?></a>
            <!-- ล้างข้อมูลค้นหา -->
            <a class="btn xCNBTNDefult xCNBTNDefult1Btn" href="javascript:;" onclick="JSxTRMClearSearchData()"><?=language('common/main/main', 'tClearSearch'); ?></a>
        </div>
        <!-- แท๊บข้อมูลหน้าจอค้นหาขั้นสูง -->
        <div class="row hidden" id="odvTRMAdvanceSearchContainer" style="margin-bottom:20px;">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-2">
                        <label class="xCNLabelFrm"><?=language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMAdvSearchBranch'); ?></label>
                        <div class="form-group">
                            <?php
                                $tBCHCode   = '';
                                $tBCHName   = '';
                                if($this->session->userdata("tSesUsrLevel") != "HQ"){
                                    if( $this->session->userdata("nSesUsrBchCount") <= 1 ){ 
                                        //ค้นหาขั้นสูง
                                        $tBCHCode   = $this->session->userdata("tSesUsrBchCodeDefault");
                                        $tBCHName   = $this->session->userdata("tSesUsrBchNameDefault");
                                    }
                                }
                            ?>
                            <div class="input-group">
                                <input class="form-control xCNHide" id="oetBchCodeFrom" name="oetBchCodeFrom" maxlength="5" value="<?=$tBCHCode;?>">
                                <input 
                                    type="text"
                                    class="form-control xWPointerEventNone" 
                                    id="oetBchNameFrom"
                                    name="oetBchNameFrom"
                                    placeholder="<?=language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMAdvSearchFrom');?>"
                                    value="<?= $tBCHName;?>"
                                    readonly
                                >
                                <span class="input-group-btn">
                                    <button id="obtTRMBrowseBchFrom" type="button" class="btn xCNBtnBrowseAddOn">
                                        <img src="<?=base_url().'application/modules/common/assets/images/icons/find-24.png';?>">
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-2">
                        <label class="xCNLabelFrm"><?=language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMAdvSearchTo');?></label>
                        <div class="form-group">
                            <div class="input-group">
                                <input class="form-control xCNHide" id="oetBchCodeTo" name="oetBchCodeTo" maxlength="5" value="<?= $tBCHCode; ?>">
                                <input
                                    type="text"
                                    class="form-control xWPointerEventNone"
                                    id="oetBchNameTo"
                                    name="oetBchNameTo"
                                    placeholder="<?=language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMAdvSearchTo');?>"
                                    readonly
                                    value="<?= $tBCHName; ?>"
                                >
                                <span class="input-group-btn">
                                    <button id="obtTRMBrowseBchTo" type="button" class="btn xCNBtnBrowseAddOn">
                                        <img src="<?=base_url() . 'application/modules/common/assets/images/icons/find-24.png' ?>">
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-2">
                        <label class="xCNLabelFrm"><?=language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMAdvSearchSplFrom'); ?></label>
                        <div class="form-group">
                            <div class="input-group">
                                <input class="form-control xCNHide" id="oetSplCodeFrom" name="oetSplCodeFrom" maxlength="5" value="">
                                <input 
                                    type="text"
                                    class="form-control xWPointerEventNone" 
                                    id="oetSplNameFrom" 
                                    name="oetSplNameFrom" 
                                    value=""
                                    placeholder="<?=language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMAdvSearchSplFrom'); ?>" 
                                    readonly
                                >
                                <span class="input-group-btn">
                                    <button id="obtTRMBrowseSplFrom" type="button" class="btn xCNBtnBrowseAddOn">
                                        <img src="<?=base_url() . 'application/modules/common/assets/images/icons/find-24.png' ?>">
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-2">
                        <label class="xCNLabelFrm"><?=language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMAdvSearchSplTo'); ?></label>
                        <div class="form-group">
                            <div class="input-group">
                                <input class="form-control xCNHide" id="oetSplCodeTo" name="oetSplCodeTo" maxlength="5" value="">
                                <input 
                                    type="text"
                                    class="form-control xWPointerEventNone" 
                                    id="oetSplNameTo" 
                                    name="oetSplNameTo" 
                                    value=""
                                    placeholder="<?=language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMAdvSearchSplTo'); ?>" 
                                    readonly
                                >
                                <span class="input-group-btn">
                                    <button id="obtTRMBrowseSplTo" type="button" class="btn xCNBtnBrowseAddOn">
                                        <img src="<?=base_url() . 'application/modules/common/assets/images/icons/find-24.png' ?>">
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-2">
                        <label class="xCNLabelFrm"><?=language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMAdvSearchDateFrom'); ?></label>
                        <div class="form-group">
                            <div class="input-group">
                                <input 
                                    type="text"
                                    class="form-control input100 xCNDatePicker" 
                                    id="oetSearchDocDateFrom" 
                                    name="oetSearchDocDateFrom" 
                                    placeholder="<?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMAdvSearchDateFrom');?>"
                                >
                                <span class="input-group-btn">
                                    <button id="obtSearchDocDateFrom" type="button" class="btn xCNBtnDateTime">
                                        <img src="<?=base_url(); ?>application/modules/common/assets/images/icons/icons8-Calendar-100.png">
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-2">
                        <label class="xCNLabelFrm"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMAdvSearchDateTo'); ?></label>
                        <div class="form-group">
                            <div class="input-group">
                                <input 
                                    type="text"
                                    class="form-control input100 xCNDatePicker" 
                                    id="oetSearchDocDateTo" 
                                    name="oetSearchDocDateTo" 
                                    placeholder="<?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMAdvSearchDateTo'); ?>"
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
                    <!-- สถานะเอกสาร -->
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-2">
                        <div class="col-lg-12 col-md-12 col-xs-12 no-padding">
                            <label class="xCNLabelFrm"><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMAdvSearchStaDoc'); ?></label>
                        </div>
                        <div class="form-group">
                            <select class="selectpicker form-control" id="ocmSearchStaDoc" name="ocmSearchStaDoc">
                                <option value='0'><?=language('common/main/main', 'tStaDocAll'); ?></option>
                                <option value='1'><?php echo language('common/main/main', 'tStaDocApv'); ?></option>
                                <option value='2'><?php echo language('common/main/main', 'tStaDocPendingApv'); ?></option>
                                <option value='3'><?php echo language('common/main/main', 'tStaDocCancel'); ?></option>
                            </select>
                        </div>
                    </div>
                    <!-- ปุ่มค้นหา -->
                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-3">
                        <div class="form-group" style="width: 60%;">
                            <label class="xCNLabelFrm">&nbsp;</label>
                            <button id="oahTRMAdvanceSearchSubmit" class="btn xCNBTNPrimery" style="width:100%" onclick="JSvTRMCallPageDataTable()"><?=language('common/main/main', 'tSearch'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4"></div>
            <!-- ตัวเลือกลบหลายตัว -->
            <div class="col-xs-4 col-sm-4 col-md-8 col-lg-8 text-right" style="margin-top:-35px;">
                <div id="odvMngTableList" class="btn-group xCNDropDrownGroup">
                    <button type="button" class="btn xCNBTNMngTable" data-toggle="dropdown">
                        <?=language('common/main/main','tCMNOption')?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li id="oliBtnDeleteAll" class="disabled">
                            <a data-toggle="modal" data-target="#odvTRMModalDelDocMultiple"><?= language('common/main/main','tDelAll')?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <section id="ostContentTRM"></section>
    </div>
</div>
<script src="<?=base_url('application/modules/common/assets/js/jquery.mask.js')?>"></script>
<script src="<?=base_url('application/modules/common/assets/src/jFormValidate.js')?>"></script>
<?php include('script/jAdvancedSearch.php')?>