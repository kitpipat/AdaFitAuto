<!-- Panel ลูกค้า -->
<div class="panel panel-default" style="margin-bottom: 25px;">
    <div class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
        <label class="xCNTextDetail1"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTitlePanelCst'); ?></label>
        <a class="xCNMenuplus" role="button" data-toggle="collapse" href="#odvPanelSPL" aria-expanded="true">
            <i class="fa fa-plus xCNPlus"></i>
        </a>
    </div>
    <div id="odvPanelSPL" class="panel-collapse collapse in" role="tabpanel">
        <div class="panel-body xCNPDModlue">

            <!-- ผู้จำหน่าย -->
            <div class="form-group">
                <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMSupplier'); ?></label>
                <input
                    type="text"
                    class="form-control xCNInputWithoutSpc"
                    id="oetPanel_AgnName"
                    name="oetPanel_AgnName"
                    placeholder="<?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMSupplier'); ?>"
                    value='<?=@$tTRMAgnNameTo?>'
                    readonly
                >
            </div>

            <!-- ที่อยู่ -->
            <div class="form-group">
                <label class="xCNLabelFrm"><?=language('document/invoicerytandmktfee/invoicerytandmktfee','tTRMTitleSplAddress');?></label>
                <textarea
                    class="form-control xControlRmk xWConditionSearchPdt"
                    id="oetPanel_AgnAddress"
                    name="oetPanel_AgnAddress"
                    rows="10"
                    maxlength="200"
                    style="resize: none;height:86px;"
                    readonly
                ><?= @$tTRMAgnAddress; ?></textarea>
            </div>
            
            <!-- เบอร์โทร -->
            <div class="form-group">
                <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMSupplierTel'); ?></label>
                <input
                    type="text"
                    class="form-control xCNInputWithoutSpc"
                    id="oetPanel_AgnTel"
                    name="oetPanel_AgnTel"
                    placeholder="<?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMSupplierTel'); ?>"
                    value='<?=@$tTRMAgnTel?>'
                    readonly
                >
            </div>

            <!-- อีเมล์ -->
            <div class="form-group">
                <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMSupplierMail'); ?></label>
                <input
                    type="text"
                    class="form-control xCNInputWithoutSpc"
                    id="oetPanel_AgnMail"
                    name="oetPanel_AgnMail"
                    placeholder="<?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMSupplierMail'); ?>"
                    value='<?=@$tTRMAgnEmail?>'
                    readonly
                >
            </div>
        </div>
    </div>
</div>