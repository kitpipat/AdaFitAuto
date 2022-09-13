<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <?php if($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaAppv'] == 1) : ?>
                            <!-- <th rowspan="2" nowrap class="xCNTextBold text-center" style="width:5%;vertical-align:middle;">
                                <label class="fancy-checkbox">
                                    <input type="checkbox" class="ocmCIPCheckDeleteAll" id="ocmCIPCheckDeleteAll" >
                                    <span class="ospListItem">&nbsp;</span>
                                </label>
                            </th> -->
                        <?php endif; ?>
                        <th rowspan="2" class="xCNTextBold text-center" style="width:10%;vertical-align:middle;"><?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBAgency')?></th>
                        <th rowspan="2" class="xCNTextBold text-center" style="width:10%;vertical-align:middle;"><?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBBranch')?></th>
                        <th rowspan="2" class="xCNTextBold text-center" style="width:10%;vertical-align:middle;"><?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBPos')?></th>
                        <th rowspan="2" class="xCNTextBold text-center" style="width:15%;vertical-align:middle;"><?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBListName')?></th>
                        <th colspan="1" class="xCNTextBold text-center" style="width:5%;vertical-align:middle;">
                            <?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBDataQty')?><br><?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBDataPosServ')?>
                        </th>
                        <th colspan="3" class="xCNTextBold text-center" style="width:5%;vertical-align:middle;">
                            <?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBDataQty')?><br><?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBDataPosClient')?>
                        </th>
                        <th rowspan="2" class="xCNTextBold text-center" style="width:10%;vertical-align:middle;"><?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBDataLastSynce')?></th>
                        <th rowspan="2" class="xCNTextBold text-center" style="width:5%;vertical-align:middle;"><?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBDataDiff')?></th>
                    </tr>
                    <tr>
                        <th class="xCNTextBold text-center" style="width:5%;vertical-align:middle;"><?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBAllActived')?></th>
                        <th class="xCNTextBold text-center" style="width:5%;vertical-align:middle;"><?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBDataLastSave')?></th>
                        <th class="xCNTextBold text-center" style="width:5%;vertical-align:middle;"><?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBDataLastUpd')?></th>
                        <th class="xCNTextBold text-center" style="width:5%;vertical-align:middle;"><?= language('settingconfig/checkinfopos/checkinfopos','tCIPTBAllActived')?></th>
                    </tr>
                </thead>
                <tbody id="odvCIPList">
                    <?php if($aDataList['rtCode'] == 1 ):?>
                        <?php foreach($aDataList['raItems'] AS $key => $aValue): ?>
                            <tr class="xCNTextDetail2 otrCIP" id="otrCIP<?=$key?>" 
                                data-syntble="<?=$aValue['FTSynTable'];?>"
                                data-agncode="<?=$aValue['FTAgnCode'];?>"
                                data-bchcode="<?=$aValue['FTBchCode'];?>"
                                data-poscode="<?=$aValue['FTPosCode'];?>"
                            >
                                <?php 
                                    $tCIPTBDataDiff = 0;
                                    if(!empty($aValue['FNPSVRowActive']) && !empty($aValue['FNHisRowAll'])){
                                        $tCIPTBDataDiff = floatval($aValue['FNPSVRowActive']) - floatval($aValue['FNHisRowAll']);
                                    }
                                ?>
                                <td class="text-left"><?=(!empty($aValue['FTAgnName']))? $aValue['FTAgnName'] : '-';?></td>
                                <td class="text-left"><?=(!empty($aValue['FTBchName']))? $aValue['FTBchCode'].' - '.$aValue['FTBchName'] : '-';?></td>
                                <td class="text-left"><?=(!empty($aValue['FTPosCode']))? $aValue['FTPosCode'] : '-';?></td>
                                <td class="text-left"><?=(!empty($aValue['FTSynName']))? $aValue['FTSynName'] : '-';?></td>
                                <td class="text-right"><?=number_format($aValue['FNPSVRowActive'],$nOptDecimalShow);?></td>
                                <td class="text-right"><?=number_format($aValue['FNHisRowIns'],$nOptDecimalShow);?></td>
                                <td class="text-right"><?=number_format($aValue['FNHisRowUpd'],$nOptDecimalShow);?></td>
                                <td class="text-right"><?=number_format($aValue['FNHisRowAll'],$nOptDecimalShow);?></td>
                                <td class="text-center"><?=date('d/m/Y h:i:s',strtotime($aValue['FDHisLastSync']))?></td>
                                <td class="text-right"><?=number_format($tCIPTBDataDiff,$nOptDecimalShow);?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else:?>
                        <tr><td class='text-center xCNTextDetail2' colspan='99'><?= language('common/main/main','tCMNNotFoundData')?></td></tr>
                    <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
</div>