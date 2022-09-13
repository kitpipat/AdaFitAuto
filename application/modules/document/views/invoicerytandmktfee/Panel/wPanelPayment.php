<!-- Panel ประเภทการชำระเงิน -->
<div class="panel panel-default" style="margin-bottom: 25px;">
    <div class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
        <label class="xCNTextDetail1"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTitlePanelPayment'); ?></label>
        <a class="xCNMenuplus" role="button" data-toggle="collapse" href="#odvPanelPayment" aria-expanded="true">
            <i class="fa fa-plus xCNPlus"></i>
        </a>
    </div>
    <div id="odvPanelPayment" class="panel-collapse collapse in" role="tabpanel">
        <div class="panel-body xCNPDModlue">

            <!-- ประเภทภาษี -->
            <div class="form-group">
                <label class="xCNLabelFrm"><?= language('document/quotation/quotation','tTQVatInOrEx');?></label>
                <?php
                    switch($tTRMVatInOrEx){
                        case '1':
                            $tOptionVatIn   = "selected";
                            $tOptionVatEx   = "";
                        break;
                        case '2':
                            $tOptionVatIn   = "";
                            $tOptionVatEx   = "selected";
                        break;
                        default:
                            $tOptionVatIn   = "";
                            $tOptionVatEx   = "selected";
                    }
                ?>
                <select class="selectpicker form-control xCNSelectDisabledPicker" id="ocmTRMfoVatInOrEx" name="ocmTRMfoVatInOrEx" maxlength="1">
                    <option value="1" <?= @$tOptionVatIn;?>><?= language('document/quotation/quotation','tTQVatInclusive');?></option>
                    <option value="2" <?= @$tOptionVatEx;?>><?= language('document/quotation/quotation','tTQVatExclusive');?></option>
                </select>
            </div>

            <!-- ประเภทการชำระ -->
            <div class="form-group">
                <label class="xCNLabelFrm"><?= language('document/quotation/quotation','tTQPayment');?></label>
                <select class="selectpicker form-control xCNSelectDisabledPicker" id="ocmTRMPaymentType" name="ocmTRMPaymentType" maxlength="1">
                    <option value="1" <?php if(@$tTRMCshorCrd=='1'){ echo 'selected'; } ?>><?= language('document/quotation/quotation','tTQPaymentType1');?></option>
                    <option value="2" <?php if(@$tTRMCshorCrd=='2'){ echo 'selected'; } ?> ><?= language('document/quotation/quotation','tTQPaymentType2');?></option>
                </select>
            </div>

            <!-- วันที่ครบกำหนดชำระเงิน -->
            <div class="form-group">
                <label class="xCNLabelFrm"><span style="color:red">*</span><?=language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMPaymentDueDate'); ?></label>
                <div class="input-group">
                    <input
                        type="text"
                        class="form-control xCNInputReadOnly xCNDatePicker xCNInputMaskDate"
                        id="oetTRMDueDate"
                        name="oetTRMDueDate"
                        autocomplete="off" 
                        placeholder="YYYY-MM-DD" 
                        value="<?= @$dTRMDueDate?>"
                    >
                    <span class="input-group-btn">
                        <button id="obtTRMRefPanelDueDate" type="button" class="btn xCNBtnDateTime">
                            <img src="<?= base_url().'application/modules/common/assets/images/icons/icons8-Calendar-100.png'; ?>">
                        </button>
                    </span>
                </div>
            </div>

            <!-- ชำระโดย -->
            <div class="form-group">
                <label class="xCNLabelFrm"><span style="color:red">*</span><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMPaymentPaidby') ?></label>
                <div class="input-group">
                    <input 
                        type="text"
                        class="form-control xCNHide"
                        id="oetTRMBbkCode"
                        name="oetTRMBbkCode"
                        maxlength="5"
                        value="<?= @$tTRMBbkCode;?>"
                    >
                    <input 
                        type="text"
                        class="form-control xWPointerEventNone"
                        id="oetTRMBbkName"
                        name="oetTRMBbkName"
                        maxlength="255"
                        placeholder="<?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMPaymentPaidby') ?>"
                        value="<?= @$tTRMBbkName ?>" 
                        readonly
                    >
                    <span class="input-group-btn">
                        <button id="oimTRMBrowseBbk" type="button" class="btn xCNBtnBrowseAddOn">
                            <img src="<?= base_url() . '/application/modules/common/assets/images/icons/find-24.png' ?>">
                        </button>
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>