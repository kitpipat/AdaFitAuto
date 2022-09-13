<?php
    $aDataReport    = $aDataViewRpt['aDataReport'];
    $aDataTextRef   = $aDataViewRpt['aDataTextRef'];
    $aDataFilter    = $aDataViewRpt['aDataFilter'];
    $nOptDecimalShow = FCNxHGetOptionDecimalShow();
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

        /* background-color: #CFE2F3 !important; */
    }

    .table>tbody>tr.xCNTrSubFooter {
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
        /* background-color: #CFE2F3 !important; */
    }

    .table>tbody>tr.xCNTrFooter {
        /* background-color: #CFE2F3 !important; */
        /* border-bottom : 6px double black !important; */
        /* border-top: dashed 1px #333 !important; */
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
    }

    .table tbody tr.xCNHeaderGroup,
    .table>tbody>tr.xCNHeaderGroup>td {
        /* color: #232C3D !important; */
        font-size: 18px !important;
        font-weight: 600;
    }

    .table>tbody>tr.xCNHeaderGroup>td:nth-child(4),
    .table>tbody>tr.xCNHeaderGroup>td:nth-child(5) {
        text-align: right;
    }

    /*แนวนอน*/
    @media print {
        @page {
            size: A4 landscape;
        }

        body {
            margin: 0;
            color: #000;
            background-color: #fff;
        }

    }

    /*แนวตั้ง*/
    /*@media print{@page {size: portrait}}*/
</style>
<div id="odvRptTaxSalePosHtml">
    <div class="container-fluid xCNLayOutRptHtml">
        <div class="xCNHeaderReport">
            <!-- <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="text-center">
                        <label class="xCNRptTitle"><?php echo $aDataTextRef['tTitleReport']; ?></label>
                    </div>
                </div>
            </div> -->
            <div class="row">
                <?php include 'application\modules\report\datasources\Address\wRptAddress.php'; ?>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <div class="text-center">
                        <label class="xCNRptTitle"><?php echo $aDataTextRef['tTitleReport']; ?></label>
                    </div>
                    <?php if ((isset($aDataFilter['tDocDateFrom']) && !empty($aDataFilter['tDocDateFrom'])) && (isset($aDataFilter['tDocDateTo']) && !empty($aDataFilter['tDocDateTo']))) : ?>
                        <!-- ============================ ฟิวเตอร์ข้อมูล วันที่สร้างเอกสาร ============================ -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-center xCNRptFilter">
                                    <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptSaleByCashierAndPosFilterDocDateFrom']; ?> </span> <?php echo date("d/m/Y", strtotime($aDataFilter['tDocDateFrom'])); ?></label>
                                    <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptSaleByCashierAndPosFilterDocDateTo']; ?> </span> <?php echo date("d/m/Y", strtotime($aDataFilter['tDocDateTo'])); ?></label>
                                </div>
                                <?php
                                //   //จัดฟอร์แมต DateFrom
                                //   $tDocDateFrom = explode("-",$aDataFilter['tDocDateFrom']);
                                //   //เซตปี คศ +543
                                //   $tYearFrom    = $tDocDateFrom[0]+543;
                                //   $tMonthFrom   = $tDocDateFrom[1];
                                //   $tDayFrom     = $tDocDateFrom[2];
                                //   //ตรวจสอบ เดือนที่เลือก
                                //   $tMonth  = language('report/report/report', 'tRptMonth'.$tMonthFrom);
                                //   //จัดฟอแมต วัน/เดือน/ปี
                                //   $tFormatDateFrom =($aDataTextRef['tRptTaxSalePosDocDate'].' '.$tDayFrom.' '.$aDataTextRef['tRptTaxSalePosTaxMonth'].' '.$tMonth.' '.$aDataTextRef['tRptTaxSalePosYear'].' '.$tYearFrom);
                                ?>
                                <?php
                                //   ////จัดฟอร์แมต DateTo
                                //   $tDocDateFrom = explode("-",$aDataFilter['tDocDateTo']);
                                //   //เซตปี คศ +543
                                //   $tYearFromTo    = $tDocDateFrom[0]+543;
                                //   $tMonthFromTo   = $tDocDateFrom[1];
                                //   $tDayFromTo     = $tDocDateFrom[2];
                                //   //ตรวจสอบ เดือนที่เลือก
                                //   $tMonthFormTo  = language('report/report/report', 'tRptMonth'.$tMonthFromTo);
                                //   //จัดฟอแมต วัน/เดือน/ปี
                                //   $tFormatDateFromTo =($aDataTextRef['tRptTaxSalePosDocDate'].' '.$tDayFromTo.' '.$aDataTextRef['tRptTaxSalePosTaxMonth'].' '.$tMonthFormTo.' '.$aDataTextRef['tRptTaxSalePosYear'].' '.$tYearFromTo);
                                ?>
                                <!-- <div class="text-center xCNRptFilter">
                                    <label><?php echo $tFormatDateFrom; ?></label>&nbsp;&nbsp;
                                    <label><?php echo $aDataTextRef['tRptTaxSaleDateTo'] . ' ' . $tFormatDateFromTo; ?></label>
                                </div> -->
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ((isset($aDataFilter['tBchCodeFrom']) && !empty($aDataFilter['tBchCodeFrom'])) && (isset($aDataFilter['tBchCodeTo']) && !empty($aDataFilter['tBchCodeTo']))) { ?>
                        <!-- ============================ ฟิวเตอร์ข้อมูล สาขา ============================ -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-center xCNRptFilter">
                                    <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptTaxSalePosFilterBchFrom'] . ' </span>' . $aDataFilter['tBchNameFrom']; ?></label>&nbsp;&nbsp;
                                    <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptTaxSalePosFilterBchTo'] . ' </span>' . $aDataFilter['tBchNameTo']; ?></label>
                                </div>
                            </div>
                        </div>
                    <?php }; ?>

                    <!-- <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="text-center xCNRptFilter">
                                <label class="xCNRptLabel"><?php echo $aDataTextRef['tRptTaxSalePosType'] . ' : ' . language('report/report/report', 'tRptPosType' . $aDataFilter['tPosType']); ?></label>
                            </div>
                        </div>
                    </div> -->

                </div>

                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 ">

                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 table-responsive">
                    <div class="text-right">
                        <label class="xCNRptDataPrint"><?php echo $aDataTextRef['tDatePrint'] . ' ' . date('d/m/Y') . ' ' . $aDataTextRef['tTimePrint'] . ' ' . date('H:i:s'); ?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="xCNContentReport">
            <div id="odvRptTableAdvance" class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;" ><?php echo language('report/report/report', 'รหัสสาขา'); ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle; " ><?php echo language('report/report/report', 'ชื่อสาขา'); ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="vertical-align : middle; " ><?php echo language('report/report/report', 'ยอดขาย'); ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="vertical-align : middle;" ><?php echo language('report/report/report', 'ส่วนลด'); ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="vertical-align : middle;"><?php echo language('report/report/report', 'ยอดขายรวม'); ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="vertical-align : middle;"><?php echo language('report/report/report', 'มูลค่าแยกภาษี'); ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="vertical-align : middle;"><?php echo language('report/report/report', 'ภาษีมูลค่าเพิ่ม'); ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="vertical-align : middle;"><?php echo language('report/report/report', 'Royalty fee (%)'); ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="vertical-align : middle;"><?php echo language('report/report/report', 'Royalty fee before VAT'); ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="vertical-align : middle;"><?php echo language('report/report/report', 'Marketing fee'); ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="vertical-align : middle;"><?php echo language('report/report/report', 'Marketing fee before VAT'); ?></th>
                        </tr>
                    </thead>
                    <tbody>

                      <?php
                        $nSumNetAfHD = 0;
                        $nSumPShare = 0;
                        $aDataArray = array();
                       ?>
                        <?php 
                        if(isset($aDataReport['aRptData']) && !empty($aDataReport['aRptData'])) { 
                            foreach ($aDataReport['aRptData'] as $nKey => $aValue) {
                        ?>  
                           <tr>
                                <td class="text-left xCNRptDetail" ><?=$aValue['FTBchCodeTo']?></td>
                                <td class="text-left xCNRptDetail"><?=$aValue['FTBchNameTo']?></td>
                                <td class="text-right xCNRptDetail"><?=number_format($aValue['FCXphTotal'],$nOptDecimalShow)?></td>
                                <td class="text-right xCNRptDetail"><?=number_format($aValue['FCXphDicount'],$nOptDecimalShow)?></td>
                                <td class="text-right xCNRptDetail"><?=number_format($aValue['FCXphGrand'],$nOptDecimalShow)?></td>
                                <td class="text-right xCNRptDetail"><?=number_format($aValue['FTCphVatable'],$nOptDecimalShow)?></td>
                                <td class="text-right xCNRptDetail"><?=number_format($aValue['FCXphVat'],$nOptDecimalShow)?></td>
                                <td class="text-right xCNRptDetail"><?=number_format($aValue['FCRoyaltyfeePCRate'],$nOptDecimalShow)?></td>
                                <td class="text-right xCNRptDetail"><?=number_format($aValue['FCRoyaltyfeeBFVAT'],$nOptDecimalShow)?></td>
                                <td class="text-right xCNRptDetail"><?=number_format($aValue['FCMarketingfeePCRate'],$nOptDecimalShow)?></td>
                                <td class="text-right xCNRptDetail"><?=number_format($aValue['FCMarketingfeeBFVAT'],$nOptDecimalShow)?></td>
                            </tr>

                        <?php 
                            }
                        ?>
                            <tr class="xCNTrFooter">
                                <td class="text-left xCNRptSumFooter"><?php echo language('report/report/report', 'รวมทั้งสิ้น'); ?></td>
                                <td class="text-left xCNRptSumFooter"></td>
                                <td class="text-right xCNRptSumFooter"><?=number_format($aDataReport['aRptData'][0]['FCXphTotal_SUM'],$nOptDecimalShow)?></td>
                                <td class="text-right xCNRptSumFooter"><?=number_format($aDataReport['aRptData'][0]['FCXphDicount_SUM'],$nOptDecimalShow)?></td>
                                <td class="text-right xCNRptSumFooter"><?=number_format($aDataReport['aRptData'][0]['FCXphGrand_SUM'],$nOptDecimalShow)?></td>
                                <td class="text-right xCNRptSumFooter"><?=number_format($aDataReport['aRptData'][0]['FTCphVatable_SUM'],$nOptDecimalShow)?></td>
                                <td class="text-right xCNRptSumFooter"><?=number_format($aDataReport['aRptData'][0]['FCXphVat_SUM'],$nOptDecimalShow)?></td>
                                <td class="text-left xCNRptSumFooter"></td>
                                <td class="text-right xCNRptSumFooter"><?=number_format($aDataReport['aRptData'][0]['FCRoyaltyfeeBFVAT_SUM'],$nOptDecimalShow)?></td>
                                <td class="text-left xCNRptSumFooter"></td>
                                <td class="text-right xCNRptSumFooter"><?=number_format($aDataReport['aRptData'][0]['FCMarketingfeeBFVAT_SUM'],$nOptDecimalShow)?></td>
                            </td>

                        <?php
                        }
                        ?>
                       
                   </tbody>
                </table>
            </div>

            <!--เเสดงหน้า-->
            <div class="xCNRptFilterTitle">
                <div class="text-right">
                    <label><?= language('report/report/report', 'tRptPage') ?> <?= $aDataReport["aPagination"]["nDisplayPage"] ?> <?= language('report/report/report', 'tRptTo') ?> <?= $aDataReport["aPagination"]["nTotalPage"] ?> </label>
                </div>
            </div>

            <div class="xCNRptFilterTitle">
                <div class="text-left">
                    <label class="xCNTextConsOth"><?= $aDataTextRef['tRptConditionInReport']; ?></label>
                </div>
            </div>

            <!-- ============================ ฟิวเตอร์ข้อมูล สาขา ============================ -->
            <?php if (isset($aDataFilter['tBchCodeSelect']) && !empty($aDataFilter['tBchCodeSelect'])) : ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchFrom']; ?> : </span> <?php echo ($aDataFilter['bBchStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tBchNameSelect']; ?></label>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================ ฟิวเตอร์ข้อมูล กลุ่มธุรกิจ ============================ -->
            <?php if ((isset($aDataFilter['tMerCodeFrom']) && !empty($aDataFilter['tMerCodeFrom'])) && (isset($aDataFilter['tMerCodeTo']) && !empty($aDataFilter['tMerCodeTo']))) : ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjMerChantFrom'] . ' : </span>' . $aDataFilter['tMerNameFrom']; ?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjMerChantTo'] . ' : </span>' . $aDataFilter['tMerNameTo']; ?></label>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($aDataFilter['tMerCodeSelect']) && !empty($aDataFilter['tMerCodeSelect'])) : ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptMerFrom']; ?> : </span> <?php echo ($aDataFilter['bMerStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tMerNameSelect']; ?></label>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================ ฟิวเตอร์ข้อมูล ร้านค้า ============================ -->
            <?php if ((isset($aDataFilter['tShpCodeFrom']) && !empty($aDataFilter['tShpCodeFrom'])) && (isset($aDataFilter['tShpCodeTo']) && !empty($aDataFilter['tShpCodeTo']))) : ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjShopFrom'] . ' : </span>' . $aDataFilter['tShpNameFrom']; ?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjShopTo'] . ' : </span>' . $aDataFilter['tShpNameTo']; ?></label>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($aDataFilter['tShpCodeSelect']) && !empty($aDataFilter['tShpCodeSelect'])) : ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptShopFrom']; ?> : </span> <?php echo ($aDataFilter['bShpStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tShpNameSelect']; ?></label>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================ ฟิวเตอร์ข้อมูล จุดขาย ============================ -->
            <!-- <?php if ((isset($aDataFilter['tPosCodeFrom']) && !empty($aDataFilter['tPosCodeFrom'])) && (isset($aDataFilter['tPosCodeTo']) && !empty($aDataFilter['tPosCodeTo']))) : ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjPosFrom'] . ' : </span>' . $aDataFilter['tPosCodeFrom']; ?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjPosTo'] . ' : </span>' . $aDataFilter['tPosCodeTo']; ?></label>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($aDataFilter['tPosCodeSelect']) && !empty($aDataFilter['tPosCodeSelect'])) : ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptPosFrom']; ?> : </span> <?php echo ($aDataFilter['bPosStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tPosCodeSelect']; ?></label>
                    </div>
                </div>
            <?php endif; ?> -->

            <!-- ============================ ฟิวเตอร์ข้อมูล ประเภทจุดขาย ============================ -->
            <!-- <?php if (isset($aDataFilter['tPosType'])) {
                    ?>

            <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptPosTypeName'] . ' : </span>' . $aDataTextRef['tRptPosType' . $aDataFilter['tPosType']]; ?></label>
                    </div>
                </div>

            <?php }
            ?> -->

        </div>
        <div class="xCNFooterPageRpt">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="text-right">
                        <?php if ($aDataReport["aPagination"]["nTotalPage"] > 0) : ?>
                            <label class="xCNRptLabel"><?php echo $aDataReport["aPagination"]["nDisplayPage"] . ' / ' . $aDataReport["aPagination"]["nTotalPage"]; ?></label>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var oFilterLabel = $('.report-filter .text-left label:first-child');
        var nMaxWidth = 0;
        oFilterLabel.each(function(index) {
            var nLabelWidth = $(this).outerWidth();
            if (nLabelWidth > nMaxWidth) {
                nMaxWidth = nLabelWidth;
            }
        });
        $('.report-filter .text-left label:first-child').width(nMaxWidth + 50);
    });
</script>
