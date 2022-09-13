<!-- Panel อื่นๆ -->
<div class="panel panel-default" style="margin-bottom: 25px;">
    <div class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
        <label class="xCNTextDetail1"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMLabelFrmInfoOth'); ?></label>
        <a class="xCNMenuplus collapsed" role="button" data-toggle="collapse" href="#odvTRMConditionOrher" aria-expanded="true">
            <i class="fa fa-plus xCNPlus"></i>
        </a>
    </div>
    <div id="odvTRMConditionOrher" class="panel-collapse collapse" role="tabpanel">
        <div class="panel-body xCNPDModlue">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <!-- สถานะความเคลื่อนไหว -->
                    <div class="form-group">
                        <label class="fancy-checkbox">
                            <input 
                                type="checkbox"
                                id="ocbTRMFrmInfoOthStaDocAct"
                                name="ocbTRMFrmInfoOthStaDocAct"
                                maxlength="1"
                                value="1"
                                <?php echo ($nTRMStaDocAct == '1' || empty($nTRMStaDocAct)) ? 'checked' : ''; ?>
                            >
                            <span>&nbsp;</span>
                            <span class="xCNLabelFrm"><?php echo language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMLabelFrmInfoOthStaDocAct'); ?></span>
                        </label>
                    </div>
                    <!-- จำนวนครั้งที่พิมพ์ -->
                    <div class="form-group">
                        <label class="xCNLabelFrm"><?php echo language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMLabelFrmInfoOthDocPrint'); ?></label>
                        <input type="text" class="form-control text-right" id="ocmTRMFrmInfoOthDocPrint" name="ocmTRMFrmInfoOthDocPrint" value="<?php echo $tTRMFrmDocPrint; ?>" readonly>
                    </div>
                    <!-- หมายเหตุเพิ่มเติม -->
                    <textarea>
                        
                    </textarea>
                </div>
            </div>
        </div>
    </div>
</div>