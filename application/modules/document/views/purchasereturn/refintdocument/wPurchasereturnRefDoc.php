<!-- Filter -->
<section>
                <div class="col-md-3 col-xs-3 col-sm-3">
                    <div class="form-group">
                                <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmBranch')?></label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control xCNHide xCNInputWithoutSpcNotThai xCNInputWithoutSingleQuote"
                                            id="oetPNRefIntBchCode"
                                            name="oetPNRefIntBchCode"
                                            maxlength="5"
                                            value="<?=$tBCHCode?>"
                                            data-bchcodeold = ""
                                        >
                                        <input
                                            type="text"
                                            class="form-control xWPointerEventNone"
                                            id="oetPNRefIntBchName"
                                            name="oetPNRefIntBchName"
                                            maxlength="100"
                                            value="<?=$tBCHName?>"
                                            readonly
                                        >
                                        <span class="input-group-btn xWConditionSearchPdt">
                                            <button id="obtPNBrowseBchRefIntDoc" type="button" class="btn xCNBtnBrowseAddOn "    >
                                                <img src="<?php echo  base_url().'/application/modules/common/assets/images/icons/find-24.png'?>">
                                            </button>
                                        </span>
                                    </div>
                                </div>

                    </div>
                </div>
                <!-- ???????????????????????????????????? -->
                <div class="col-md-2 col-xs-2 col-sm-2">
                    <div class="form-group">
                                <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOTBDocNo')?></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="oetPNRefIntDocNo"
                                            name="oetPNRefIntDocNo"
                                            maxlength="100"
                                            value=""
                                            placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOTBDocNo')?>"
                                        >
                                </div>
                    </div>
                 </div>
                <!-- ??????????????????????????????????????????????????? -->
                 <div class="col-md-2 col-xs-2 col-sm-2">
                    <div class="form-group">
                                <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPORefIntDocDateFrm')?></label>
                                        <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control xCNDatePicker xCNInputMaskDate"
                                            id="oetPNRefIntDocDateFrm"
                                            name="oetPNRefIntDocDateFrm"
                                            placeholder="YYYY-MM-DD"
                                            value=""
                                        >
                                        <span class="input-group-btn">
                                            <button id="obtPNBrowseRefExtDocDateFrm" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                                        </span>
                                    </div>
                                </div>
                    </div>
                 </div>
                <!-- ????????????????????????????????????????????????????????? -->
                <div class="col-md-2 col-xs-2 col-sm-2">
                    <div class="form-group">
                                <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPORefIntDocDateTo')?></label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control xCNDatePicker xCNInputMaskDate"
                                            id="oetPNRefIntDocDateTo"
                                            name="oetPNRefIntDocDateTo"
                                            placeholder="YYYY-MM-DD"
                                            value=""
                                        >
                                        <span class="input-group-btn">
                                            <button id="obtPNBrowseRefExtDocDateTo" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                                        </span>
                                    </div>
                            </div>
                    </div>
                 </div>
                <!-- ????????????????????????????????? -->
                <div class="col-md-2 col-xs-2 col-sm-2">
                    <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder','tPORefIntDocStatus');?></label>
                                    <select class="selectpicker form-control  " id="oetPNRefIntStaDoc" name="oetPNRefIntStaDoc" maxlength="1" value="<?php echo $tPOSplPayMentType;?>">
                                        <option value="1" ><?php echo language('document/purchaseorder/purchaseorder','tPOLabelFrmValStaApv1');?></option>
                                        <option value="2" ><?php echo language('document/purchaseorder/purchaseorder','tPOLabelFrmValStaApv');?></option>
                                        <option value="3" ><?php echo language('document/purchaseorder/purchaseorder','tPOLabelFrmValStaDoc3');?></option>
                                    </select>
                    </div>
                 </div>
                          
                         
                <!-- ??????????????????????????? -->
                <div class="col-md-1 col-xs-1 col-sm-1" style="padding-top: 24px;">
                   <button id="obtRefIntDocFilter" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button" ><?= language('document/purchaseorder/purchaseorder', 'tPORefIntDocFilter')?></button>
                </div>
</section>
<!-- Document -->
<section>
    <div id="odvRefIntDocHDDataTable"></div>
</section>
<!-- Items -->
<section>
    <div id="odvRefIntDocDTDataTable"></div>
</section>

<?php include('script/jPurchasereturnRefDoc.php');?>