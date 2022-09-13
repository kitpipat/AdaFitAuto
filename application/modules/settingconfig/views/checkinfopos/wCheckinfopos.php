<input id="oetCIPStaBrowse"         type="hidden" value="<?=$nBrowseType;?>">
<input id="oetCIPCallBackOption"    type="hidden" value="<?=$tBrowseOption;?>">
<div id="odvCIPMainMenu" class="main-menu">
    <div class="xCNMrgNavMenu">
        <div class="row xCNavRow" style="width:inherit;">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <ol id="oliCIPMenuNav" class="breadcrumb">
                    <?php FCNxHADDfavorite('CheckInfoPos/0/0');?>
                    <li id="oliCIPTitle" class="active" style="cursor:pointer" onclick="JSvCallPageCIPList()"><?= language('settingconfig/checkinfopos/checkinfopos','tCIPTitle')?></li>
                </ol>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right">
                <div id="odvBtnAddEdit">
                    <div class="demo-button xCNBtngroup" style="width:100%;">
                        <button id="obtCIPSyncData" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button">
                            <?php echo language('settingconfig/checkinfopos/checkinfopos', 'tCIPSynceData'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="xCNMenuCump xCNRDHBrowseLine" id="odvMenuCump">&nbsp;</div>
<div class="main-content">
    <div id="odvContentPageCIP"></div>
</div>
<?php include('script/jCheckinfopos.php') ?>