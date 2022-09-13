<div class="panel panel-headline">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-8 col-sm-4 col-md-4 col-lg-4">
                <div class="form-group">
                    <label class="xCNLabelFrm"><?php echo language('settingconfig/checkinfopos/checkinfopos','tCIPTBCSearch')?></label>
                    <div class="input-group">
                        <input 
                            type="text"
                            class="form-control xCNInputWithoutSingleQuote"
                            id="oetSearchAll"
                            name="oetSearchAll"
                            onkeypress="Javascript:if(event.keyCode==13) JSvCIPCallPageDataTable()"
                            autocomplete="off" 
                            placeholder="<?php echo language('common/main/main','tPlaceholder')?>"
                        >
                        <span class="input-group-btn">
                            <button id="oimSearchCIP" class="btn xCNBtnSearch" type="button">
                                <img onclick="JSvCIPCallPageDataTable()" class="xCNIconBrowse" src="<?= base_url().'/application/modules/common/assets/images/icons/search-24.png'?>">
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div id="odvContentCIPData"></div>
    </div>
</div>