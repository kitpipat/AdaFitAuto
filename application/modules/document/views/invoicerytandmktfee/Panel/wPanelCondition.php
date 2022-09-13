<!-- Panel เงื่อนไข--> 
<div class="panel panel-default" style="margin-bottom: 25px;">
    <div class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
        <label class="xCNTextDetail1"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMCondition'); ?></label>
        <a class="xCNMenuplus" role="button" data-toggle="collapse" href="#odvTRMCondition" aria-expanded="true">
            <i class="fa fa-plus xCNPlus"></i>
        </a>
    </div>
    <div id="odvTRMCondition" class="panel-collapse collapse in" role="tabpanel">
        <div class="panel-body xCNPDModlue">
            <!-- ตัวแทนขาย -->
            <?php
                $tTRMDataInputADCode    = "";
                $tTRMDataInputADName    = "";
                if($tTRMRoute  == "docInvoiceRytAndMktFeeEventAdd"){
                    $tTRMDataInputADCode    = $this->session->userdata('tSesUsrAgnCode');
                    $tTRMDataInputADName    = $this->session->userdata('tSesUsrAgnName');
                    $tBrowseADDisabled      = '';
                    if($this->session->userdata('tSesUsrLevel') != "HQ"){
                        $tBrowseADDisabled  = 'disabled';
                    }
                } else {
                    $tTRMDataInputADCode    = @$tTRMAgnCode;
                    $tTRMDataInputADName    = @$tTRMAgnName;
                    $tBrowseADDisabled      = 'disabled';
                }
            ?>
            <script type="text/javascript">
                var tUsrLevel   = '<?=$this->session->userdata('tSesUsrLevel')?>';
                if( tUsrLevel != "HQ" ){
                }
                $('.xCNBrowseAD').hide();
            </script>
            <div class="form-group xCNBrowseAD">
                <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMAgency'); ?></label>
                <div class="input-group" style="width:100%;">
                    <input type="text" class="input100 xCNHide" id="oetTRMAgnCode" name="oetTRMAgnCode" value="<?=$tTRMDataInputADCode?>">
                    <input 
                        class="form-control xWPointerEventNone"
                        type="text"
                        id="oetTRMAgnName"
                        name="oetTRMAgnName"
                        readonly
                        value="<?=$tTRMDataInputADName?>"
                        placeholder="<?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMAgency'); ?>"
                    >
                    <span class="input-group-btn">
                        <button id="obtTRMBrowseAgency" type="button" class="btn xCNBtnBrowseAddOn" <?= $tBrowseADDisabled; ?>>
                            <img src="<?=  base_url() . '/application/modules/common/assets/images/icons/find-24.png'; ?>">
                        </button>
                    </span>
                </div>
            </div>



            <!-- สาขา -->
            <?php
                $tTRMDataInputBchCode   = "";
                $tTRMDataInputBchName   = "";
                if($tTRMRoute  == "docInvoiceRytAndMktFeeEventAdd"){
                    $tTRMDataInputBchCode   = $this->session->userdata('tSesUsrBchCodeDefault');
                    $tTRMDataInputBchName   = $this->session->userdata('tSesUsrBchNameDefault');
                    $tBrowseBchDisabled     = '';
                }else{
                    $tTRMDataInputBchCode   = @$tTRMBchCode;
                    $tTRMDataInputBchName   = @$tTRMBchName;
                    $tBrowseBchDisabled     = 'disabled';
                }
            ?>
            <script type="text/javascript">
                var tUsrLevel   = '<?=$this->session->userdata('tSesUsrLevel')?>';
                if( tUsrLevel != "HQ" ){
                    //BCH - SHP
                    var tBchCount   = '<?=$this->session->userdata("nSesUsrBchCount");?>';
                    if(tBchCount < 2){
                        $('#obtTRMBrowseBranch').attr('disabled',true);
                    }
                }
            </script>
            <div class="form-group">
                <label class="xCNLabelFrm"><span style="color:red">*</span><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMBranch'); ?></label>
                <div class="input-group" style="width:100%;">
                    <input type="text" class="input100 xCNHide" id="ohdTRMBchCode" name="ohdTRMBchCode" value="<?= @$tTRMDataInputBchCode; ?>">
                    <input 
                        type="text"
                        class="form-control xWPointerEventNone"
                        id="oetTRMBchName"
                        name="oetTRMBchName"
                        value="<?= @$tTRMDataInputBchName; ?>"
                        placeholder="<?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMBranch');?>"
                        readonly 
                    >
                    <span class="input-group-btn">
                        <button id="obtTRMBrowseBranch" type="button" class="btn xCNBtnBrowseAddOn" <?= $tBrowseBchDisabled; ?>>
                            <img src="<?=  base_url() . '/application/modules/common/assets/images/icons/find-24.png'; ?>">
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>