<?php

use \koolreport\widgets\koolphp\Table;

$nCurrentPage = $this->params['nCurrentPage'];
$nAllPage = $this->params['nAllPage'];
$aDataTextRef = $this->params['aDataTextRef'];
$aDataFilter = $this->params['aFilterReport'];
$aDataReport = $this->params['aDataReturn'];
$aCompanyInfo = $this->params['aCompanyInfo'];
$nOptDecimalShow = $this->params['nOptDecimalShow'];
$aSumDataReport = $this->params['aDataReturn']['aDataSumFooterReport'];

$bIsLastPage = ($nAllPage == $nCurrentPage);
?>

<style>
    
    .xCNFooterRpt {
        border-bottom: 7px double #ddd;
    }

    .table thead th,
    .table>thead>tr>th,
    .table tbody tr,
    .table>tbody>tr>td {
        border: 0px transparent !important;
    }

    .table>thead:first-child>tr:first-child>td,
    .table>thead:first-child>tr:first-child>th {
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
    }

    .table>tbody:last-child>tr:last-child>td.xCNRptSumFooter {
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
    }

    .table>tbody>tr.xCNTrSubFooter {
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
    }

    .table>tbody>tr.xCNTrFooter {
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
    }

    .table tbody tr.xCNHeaderGroup,
    .table>tbody>tr.xCNHeaderGroup>td {
        font-size: 18px !important;
        font-weight: 600;
    }

    .table>tbody>tr.xCNHeaderGroup>td:nth-child(4),
    .table>tbody>tr.xCNHeaderGroup>td:nth-child(5) {
        text-align: right;
    }

    @media print{@page {size: landscape}} 
</style>

<div id="odvRptSaleShopByDateHtml">
    <div class="container-fluid xCNLayOutRptHtml">
        <div class="xCNHeaderReport">

            <div class="row">
                <?php include 'application\modules\report\datasources\Address\wRptAddress.php'; ?>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <div class="text-center">
                        <label class="xCNRptTitle"><?php echo $aDataTextRef['tTitleReport']; ?></label>
                    </div>
                    <div class="report-filter">
                        <?php if ((isset($aDataFilter['tDocDateFrom']) && !empty($aDataFilter['tDocDateFrom'])) && (isset($aDataFilter['tDocDateTo']) && !empty($aDataFilter['tDocDateTo']))) {?>
                            <!-- ============================ ?????????????????????????????????????????? ??????????????????????????????????????????????????? ============================ -->
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="text-center xCNRptFilter">
                                        <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRPCDateFrom']; ?> : </span> <?php echo date('d/m/Y',strtotime($aDataFilter['tDocDateFrom'])); ?></label>
                                        <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRPCDateTo']; ?> : </span> <?php echo date('d/m/Y',strtotime($aDataFilter['tDocDateTo'])); ?></label>
                                    </div>
                                </div>
                            </div>
                        <?php }?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="text-right">
                        <label class="xCNRptDataPrint"><?php echo $aDataTextRef['tRptDatePrint'] . ' ' . date('d/m/Y') . ' ' . $aDataTextRef['tRptTimePrint'] . ' ' . date('H:i:s'); ?></label>
                    </div>
                </div>
            </div>
        </div>

        <div class="xCNContentReport">
            <div id="odvTableKoolReport" class="table-responsive">
                <?php if (isset($aDataReport['rtCode']) && !empty($aDataReport['rtCode']) && $aDataReport['rtCode'] == '1') {?>
                    <?php 
                        $bShowFooter = false;
                        if(($aDataReport['rnCurrentPage'] == $aDataReport['rnAllPage'])) {
                            $bShowFooter = true;
                        } 
                    ?>
                    <?php
                        Table::create(array(
                            "dataSource" => $this->dataStore("RptCheckStatusCard"),
                            "cssClass"  => array(
                                "table" => "",
                                "th"    => "xCNRptColumnHeader",
                                "td"    => "xCNRptDetail",
                                "tf"    => "xCNRptSumFooter"
                            ),
                            "showFooter" => $bShowFooter,
                            "columns" => array(
                                // 'rtRowID' => array(
                                //     "label"         => $aDataTextRef['tRPCRowNuber'],
                                //     "cssStyle"      => array("th" => "text-align:left", "td" => "text-align:left"),
                                // ),
                                'FTCrdCode' => array(
                                    "label"         => $aDataTextRef['tRPCCardCode'],
                                    "cssStyle"      => array("th" => "text-align:left", "td" => "text-align:left" , 'tf' =>  "text-align:left"),
                                    "footerText" => $bShowFooter ? $aDataTextRef['tRPCTBFooterSumAll'] : ''
                                ),
                                'FTCrdHolderID' => array(
                                    "label"         => $aDataTextRef['tRPCCardHolderID'],
                                    "cssStyle"      => array("th" => "text-align:left", "td" => "text-align:left"),
                                ),
                                'FTCtyName' => array(
                                    "label"         => $aDataTextRef['tRPCCardType'],
                                    "cssStyle"      => array("th" => "text-align:left", "td" => "text-align:left"),
                                ),
                                'FTCrdName' => array(
                                    "label"         => $aDataTextRef['tRPCCardName'],
                                    "cssStyle"      => array("th" => "text-align:left", "td" => "text-align:left"),
                                ),
                                'FDCrdStartDate' => array(
                                    "label"         => $aDataTextRef['tRPCCardStartDate'],
                                    "cssStyle"      => array("th" => "text-align:center", "td" => "text-align:center"),
                                    "formatValue"   => function($value, $row){
                                        return empty($value) ? '' : date("d/m/Y", strtotime($value));
                                    }
                                ),
                                'FDCrdExpireDate' => array(
                                    "label" => $aDataTextRef['tRPCCardExpireDate'],
                                    "cssStyle" => array(
                                        "th" => "text-align:center",
                                        "td" => "text-align:center",
                                        "tf" => "text-align:center"
                                    ),
                                    "formatValue" => function($value, $row){
                                        return empty($value) ? '' : date("d/m/Y", strtotime($value));
                                    },
                                ),
                                'FCCrdValue' => array(
                                    "label"     => $aDataTextRef['tRPCCardValue'],
                                    "cssStyle"  => array(
                                        "th"    => "text-align:right",
                                        "td"    => "text-align:right",
                                        "tf"    => "text-align:right"
                                    ),
                                    "type"          => "number",
                                    "decimals"      => 2,
                                    "footer"        => '',
                                    "footerText"    => $bShowFooter ? number_format($aSumDataReport[0]['FCCrdValueSum'], $nOptDecimalShow) : ''
                                ),
                                'FDTxnDocDate' => array(
                                    "label"         => $aDataTextRef['tRPCCardDocDate'],
                                    "cssStyle"      => array("th" => "text-align:center", "td" => "text-align:center"),
                                    "formatValue"   => function($value, $row){
                                        return empty($value) ? '' : date("d/m/Y H:i:s", strtotime($value));
                                    }
                                ),
                                'FTTxnPosCode' => array(
                                    "label"         => $aDataTextRef['tRPCCardPosCode'],
                                    "cssStyle"      => array("th" => "text-align:left", "td" => "text-align:left"),
                                )
                            ),
                        ));
                    ?>
                <?php } else { ?>
                    <table class="table">
                    <thead>
						<tr>
                            <th class="xCNRptColumnHeader"><?php echo $aDataTextRef['tRPCRowNuber']; ?></th>
                            <th class="xCNRptColumnHeader"><?php echo $aDataTextRef['tRPCCardCode']; ?></th>
                            <th class="xCNRptColumnHeader"><?php echo $aDataTextRef['tRPCCardHolderID']; ?></th>
                            <th class="xCNRptColumnHeader"><?php echo $aDataTextRef['tRPCCardType']; ?></th>
                            <th class="xCNRptColumnHeader"><?php echo $aDataTextRef['tRPCCardName']; ?></th>
                            <th class="xCNRptColumnHeader"><?php echo $aDataTextRef['tRPCCardStartDate']; ?></th>
                            <th class="xCNRptColumnHeader"><?php echo $aDataTextRef['tRPCCardExpireDate']; ?></th>
                            <th style="text-align:right" class="xCNRptColumnHeader"><?php echo $aDataTextRef['tRPCCardValue']; ?></th>
                            <th class="xCNRptColumnHeader"><?php echo $aDataTextRef['tRPCCardDocDate']; ?></th>
                            <th class="xCNRptColumnHeader"><?php echo $aDataTextRef['tRPCCardPosCode']; ?></th>			
                        </tr>
		            </thead>
                        <tbody>
                            <tr>
                                <td class='text-center xCNRptDetail' colspan='100%'><?php echo language('common/main/main', 'tCMNNotFoundData'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php }?>
            </div>

                <div class="xCNRptFilterTitle">
                    <label><u><?php echo $aDataTextRef['tRptConditionInReport']; ?></u></label>
                </div>

                    <!-- ============================ ?????????????????????????????????????????? ???????????? ============================ -->
                    <?php if (isset($aDataFilter['tBchCodeSelect']) && !empty($aDataFilter['tBchCodeSelect'])) : ?>
                        <div class="xCNRptFilterBox">
                            <div class="xCNRptFilter">
                                <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchFrom']; ?> : </span> <?php echo ($aDataFilter['bBchStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tBchNameSelect']; ?></label>
                                <br>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ============================ ?????????????????????????????????????????? ????????????????????????????????? ============================ -->
                    <?php if (isset($aDataFilter['tMerCodeSelect']) && !empty($aDataFilter['tMerCodeSelect'])) : ?>
                        <div class="xCNRptFilterBox">
                            <div class="xCNRptFilter">
                                <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptMerFrom']; ?> : </span> <?php echo ($aDataFilter['bMerStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tMerNameSelect']; ?></label>
                            </div>
                        </div>
                    <?php endif; ?> 

                <!-- ============================ ?????????????????????????????????????????? ????????????????????? ============================ -->  
                <?php if (isset($aDataFilter['tShpCodeSelect']) && !empty($aDataFilter['tShpCodeSelect'])) : ?>
                    <div class="xCNRptFilterBox">
                        <div class="xCNRptFilter">
                            <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptShopFrom']; ?> : </span> <?php echo ($aDataFilter['bShpStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tShpNameSelect']; ?></label>
                        </div>
                    </div>
                <?php endif; ?> 

                <!-- ============================ ?????????????????????????????????????????? ?????????????????? ============================ -->
                <?php if (isset($aDataFilter['tPosCodeSelect']) && !empty($aDataFilter['tPosCodeSelect'])) : ?>
                    <div class="xCNRptFilterBox">
                        <div class="xCNRptFilter">
                            <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptPosFrom']; ?> : </span> <?php echo ($aDataFilter['bPosStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tPosCodeSelect']; ?></label>
                        </div>
                    </div>
                <?php endif; ?> 

                <?php if ((isset($aDataFilter['tCardTypeCodeFrom']) && !empty($aDataFilter['tCardTypeCodeFrom'])) && (isset($aDataFilter['tCardTypeCodeTo']) && !empty($aDataFilter['tCardTypeCodeTo']))) {?>
                    <!-- ============================ ?????????????????????????????????????????? ?????????????????????????????? ============================ -->
                    <div class="xCNRptFilterBox">
                        <div class="xCNRptFilter">
                            <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRPCCrdTypeFrom']; ?> : </span> <?php echo $aDataFilter['tCardTypeNameFrom']; ?></label>
                            <br>
                            <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRPCCrdTypeTo']; ?> : </span> <?php echo $aDataFilter['tCardTypeNameTo']; ?></label>
                        </div>
                    </div>
                <?php }?>

                <?php if ((isset($aDataFilter['tCardCodeFrom']) && !empty($aDataFilter['tCardCodeFrom'])) && (isset($aDataFilter['tCardCodeTo']) && !empty($aDataFilter['tCardCodeTo']))) {?>
                    <!-- ============================ ?????????????????????????????????????????? ????????????????????????????????? ============================ -->
                    <div class="xCNRptFilterBox">
                        <div class="xCNRptFilter">
                            <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRPCCrdFrom']; ?> : </span> <?php echo $aDataFilter['tCardNameFrom']; ?></label>
                            <br>
                            <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRPCCrdTo']; ?> : </span> <?php echo $aDataFilter['tCardNameTo']; ?></label>
                        </div>
                    </div>
                <?php }?>

                <?php if ((isset($aDataFilter['tStaCardFrom']) && !empty($aDataFilter['tStaCardFrom'])) && (isset($aDataFilter['tStaCardTo']) && !empty($aDataFilter['tStaCardTo']))) {?>
                    <!-- ============================ ?????????????????????????????????????????? ??????????????????????????? ============================ -->
                    <div class="xCNRptFilterBox">
                        <div class="xCNRptFilter">
                            <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRPCStaCrdFrom']; ?> : </span> <?php echo $aDataFilter['tStaCardNameFrom']; ?></label>
                            <br>
                            <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRPCStaCrdTo']; ?> : </span> <?php echo $aDataFilter['tStaCardNameTo']; ?></label>
                        </div>
                    </div>
                <?php }?>

                <?php if ((isset($aDataFilter['tDateStartFrom']) && !empty($aDataFilter['tDateStartFrom'])) && (isset($aDataFilter['tDateStartTo']) && !empty($aDataFilter['tDateStartTo']))) {?>
                    <!-- ============================ ?????????????????????????????????????????? ??????????????????????????????????????????????????????????????? ============================ -->
                    <div class="xCNRptFilterBox">
                        <div class="xCNRptFilter">
                            <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRPCDateStartFrom']; ?> : </span> <?php echo $aDataFilter['tDateStartFrom']; ?></label>
                            <br>
                            <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRPCDateStartTo']; ?> : </span> <?php echo $aDataFilter['tDateStartTo']; ?></label>
                        </div>
                    </div>
                <?php }?>

                <?php if ((isset($aDataFilter['tDateExpireFrom']) && !empty($aDataFilter['tDateExpireFrom'])) && (isset($aDataFilter['tDateExpireTo']) && !empty($aDataFilter['tDateExpireTo']))) {?>
                    <!-- ============================ ?????????????????????????????????????????? ??????????????????????????????????????????????????? ============================ -->
                    <div class="xCNRptFilterBox">
                        <div class="xCNRptFilter">
                            <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRPCDateExpireFrom']; ?> : </span> <?php echo $aDataFilter['tDateExpireFrom']; ?></label>
                            <br>
                            <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRPCDateExpireTo']; ?> : </span> <?php echo $aDataFilter['tDateExpireTo']; ?></label>
                        </div>
                    </div>
                <?php }?>
        </div>

        <div class="xCNFooterPageRpt">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="text-right">
                        <label class="xCNRptLabel"><?php echo $nCurrentPage . ' / ' . $nAllPage; ?></label>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function(){
        var tFoot = $('tfoot').html();
        $('tfoot').remove();
        $('tbody').append(tFoot);
    });
</script>