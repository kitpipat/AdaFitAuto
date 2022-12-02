<?php
$aDataReport        = $aDataViewRpt['aDataReport'];
$aDataTextRef       = $aDataViewRpt['aDataTextRef'];
$aDataFilter        = $aDataViewRpt['aDataFilter'];
$nOptDecimalShow    = FCNxHGetOptionDecimalShow();
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

    .table>tbody>tr.xCNTrSubFooter {
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
    }

    .table>tbody>tr.xCNTrFooter {
        border-top: dashed 1px #333 !important;
        border-bottom: 1px solid black !important;
    }

    .table tbody tr.xCNRptSumFooterTrTop,
    .table>tbody>tr.xCNRptSumFooterTrTop>td {
        border: 0px solid black !important;
        border-top: 1px solid black !important;
    }

    .table tbody tr.xCNRptLastGroupTr,
    .table>tbody>tr.xCNRptLastGroupTr>td {
        border: 0px solid black !important;
        border-top: 1px dashed #ccc !important;
    }

    .table tbody tr.xCNRptSumFooterTrBottom,
    .table>tbody>tr.xCNRptSumFooterTrBottom>td {
        border: 0px solid black !important;
        border-bottom: 1px solid black !important;
    }


    /*แนวนอน*/
    @media print {
        @page {
            size: landscape;
            margin: 1.5mm 1.5mm 1.5mm 1.5mm;
        }
    }
</style>
<div id="odvRptPobyBchbyPdteHtml">
    <div class="container-fluid xCNLayOutRptHtml">
        <div class="xCNHeaderReport">
            <div class="row">
                <?php include 'application\modules\report\datasources\Address\wRptAddress.php'; ?>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 report-filter">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="text-center">
                                <label class="xCNRptTitle"><?= $aDataTextRef['tTitleReport']; ?></label>
                            </div>
                            <?php if ((isset($aDataFilter['tDocDateFrom']) && !empty($aDataFilter['tDocDateFrom'])) && (isset($aDataFilter['tDocDateTo']) && !empty($aDataFilter['tDocDateTo']))) : ?>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="text-center xCNRptFilter">
                                            <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptDateFrom']; ?> </span> <?php echo date("d/m/Y", strtotime($aDataFilter['tDocDateFrom'])); ?></label>
                                            <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptDateTo']; ?> </span> <?php echo date("d/m/Y", strtotime($aDataFilter['tDocDateTo'])); ?></label>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="text-right">
                        <label class="xCNRptDataPrint"><?= $aDataTextRef['tDatePrint'] . ' ' . date('d/m/Y') . ' ' . $aDataTextRef['tTimePrint'] . ' ' . date('H:i:s'); ?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="xCNContentReport">
            <div id="odvRptTableAdvance" class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th nowrap class="text-left   xCNRptColumnHeader" style="width:10%;">ลูกค้า / ลูกหนี้</th>
                            <th nowrap class="text-left   xCNRptColumnHeader" style="width:10%;">เลขที่เอกสาร</th>
                            <th nowrap class="text-left   xCNRptColumnHeader" style="width:10%;"></th>
                            <th nowrap class="text-left   xCNRptColumnHeader" style="width:10%;">วันครบกำหนด</th>
                            <th nowrap class="text-right   xCNRptColumnHeader" style="width:5%;"></th>
                            <th nowrap class="text-right   xCNRptColumnHeader" style="width:10%;"></th>
                            <th nowrap class="text-right   xCNRptColumnHeader" style="width:10%;"></th>
                            <th nowrap class="text-right   xCNRptColumnHeader" style="width:10%;"></th>
                            <th nowrap class="text-center   xCNRptColumnHeader" style="width:10%;"></th>
                            <th nowrap class="text-center   xCNRptColumnHeader" style="width:10%;"></th>
                        </tr>
                        <tr>
                            <th nowrap class="text-left   xCNRptColumnHeader" style="width:10%;"></th>
                            <th nowrap class="text-left   xCNRptColumnHeader" style="width:10%;text-indent: 15px;">เอกสารอ้างอิง</th>
                            <th nowrap class="text-left   xCNRptColumnHeader" style="width:10%;">ประเภทเอกสาร</th>
                            <th nowrap class="text-left   xCNRptColumnHeader" style="width:10%;text-indent: 15px;">วันที่อ้างอิงเอกสาร</th>
                            <th nowrap class="text-left   xCNRptColumnHeader" style="width:5%;">พนักงานรับชำระ</th>
                            <th nowrap class="text-right   xCNRptColumnHeader" style="width:10%;">ยอดหนี้รวม</th>
                            <th nowrap class="text-right   xCNRptColumnHeader" style="width:10%;">ยอดชำระก่อนหน้ารวม</th>
                            <th nowrap class="text-right   xCNRptColumnHeader" style="width:10%;">ยอดชำระในบิลนี้</th>
                            <th nowrap class="text-right   xCNRptColumnHeader" style="width:10%;">ยอดชำระครั้งถัดไป</th>
                            <th nowrap class="text-left   xCNRptColumnHeader" style="width:5%;">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($aDataReport['aRptData']) && !empty($aDataReport['aRptData'])) : ?>
                            <?php
                            // Set ตัวแปร SumSub Footer
                            $nQty_SubTotal  = 0;
                            // Set ตัวแปร Sum Footer
                            $nQty_Footer    = 0;
                            // Check Branch 
                            $tChkBranch     = "";
                            $tChkProduct    = "";

                            ?>
                            <?php foreach ($aDataReport['aRptData'] as $nKey => $aValue) : ?>
                                <!--  Step 2 แสดงข้อมูลใน TD  -->
                                <?php
                                    if ($aValue["FTCstCode"]=="") {
                                        $aValue["FTCstCode"] = '999';
                                        $aValue["FTCstName"] = 'ลูกค้าทั่วไป';
                                        $aValue['FTCstCode_CST_Footer'] = '999';
                                    }else {

                                    }
                                ?>

                                <?php if($aValue['PARTTITIONBYDOC_COUNT'] == '1'){ ?> 
                                <tr class = 'xCNRptLastGroupTr'>
                                    <td nowrap class="text-left   xCNRptDetail xCNRptSumFooter"><?php echo $aValue["FTCstName"]; ?></td>
                                    <td nowrap class="text-left   xCNRptDetail xCNRptSumFooter"><?php echo $aValue["FTXphDocNo"]; ?></td>
                                    <td nowrap class="text-left   xCNRptDetail"></td>
                                    <td nowrap class="text-left   xCNRptDetail xCNRptSumFooter"><?php echo  date("d/m/Y", strtotime($aValue["FDXshDocDate"])); ?></td>
                                    <td nowrap class="text-left   xCNRptDetail xCNRptSumFooter"><?php echo $aValue["FTUsrName"]; ?></td>
                                    <td nowrap class="text-right   xCNRptDetail xCNRptSumFooter"><?=number_format($aValue['FCXsdInvGrand_SUM'], $nOptDecimalShow); ?></td>
                                    <td nowrap class="text-right   xCNRptDetail xCNRptSumFooter"><?=number_format($aValue['FCXsdInvPaid_SUM'], $nOptDecimalShow); ?></td>
                                    <td nowrap class="text-right   xCNRptDetail xCNRptSumFooter"><?=number_format($aValue['FCXsdInvPay_SUM'], $nOptDecimalShow); ?></td>
                                    <td nowrap class="text-right   xCNRptDetail xCNRptSumFooter"><?=number_format($aValue['FCXsdInvRem_SUM'], $nOptDecimalShow); ?></td>
                                    <td nowrap class="text-left   xCNRptDetail"></td>
                                </tr>
                                <?php 
                                }?>

                                <?php 
                                    if($aValue['FCXsdInvRem'] == 0) {
                                        $status = language('report/report/report', 'tRptdebtorreceiveClose');
                                    }else {
                                        $status = language('report/report/report', 'tRptdebtorreceivePartialPayment');
                                    }
                                ?>
                                <tr>
                                    <td nowrap class="text-left   xCNRptDetail"></td>
                                    <td nowrap class="text-left   xCNRptDetail" style="text-indent: 15px;"><?php echo $aValue["FTXsdInvNo"]; ?></td>
                                    <td nowrap class="text-left   xCNRptDetail"><?php echo $aValue["FTXsdDocType"]; ?></td>
                                    <td nowrap class="text-left   xCNRptDetail" style="text-indent: 15px;"><?php echo  date("d/m/Y", strtotime($aValue["FTXshRefDocDate"])); ?></td>
                                    <td nowrap class="text-left   xCNRptDetail"></td>
                                    <td nowrap class="text-right   xCNRptDetail"><?=number_format($aValue['FCXsdInvGrand'], $nOptDecimalShow)?></td>
                                    <td nowrap class="text-right   xCNRptDetail"><?=number_format($aValue['FCXsdInvPaid'], $nOptDecimalShow)?></td>
                                    <td nowrap class="text-right   xCNRptDetail"><?=number_format($aValue['FCXsdInvPay'], $nOptDecimalShow)?></td>
                                    <td nowrap class="text-right   xCNRptDetail"><?=number_format($aValue['FCXsdInvRem'], $nOptDecimalShow)?></td>
                                    <td nowrap class="text-left   xCNRptDetail"><?=$status?></td>
                                </tr>
                            
                                <!--รวมยอดตามสาขา-->
                                <?php if($aValue['PARTTITIONBYCST'] == $aValue['PARTTITIONBYCST_COUNT']){ ?>
                                    <tr class="xCNRptSumFooterTrTop">
                                        <td class="xCNRptSumFooter" colspan="5">ยอดรวม : <?=$aValue["FTCstName"]; ?></td>
                                        <td class="text-right xCNRptSumFooter"><?=number_format($aValue['FCXsdInvGrand_SubTotal'], $nOptDecimalShow); ?></td>
                                        <td class="text-right xCNRptSumFooter"><?=number_format($aValue['FCXsdInvPaid_SubTotal'], $nOptDecimalShow); ?></td>
                                        <td class="text-right xCNRptSumFooter"><?=number_format($aValue['FCXsdInvPay_SubTotal'], $nOptDecimalShow); ?></td>
                                        <td class="text-right xCNRptSumFooter"><?=number_format($aValue['FCXsdInvRem_SubTotal'], $nOptDecimalShow); ?></td>
                                        <td nowrap class="text-left   xCNRptDetail"></td>
                                    </tr>
                                    <tr class="xCNRptSumFooterTrBottom"></tr>
                                <?php } ?>
                                    
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td class='text-center xCNTextDetail2' colspan='100%'><?php echo @$aDataTextRef['tRptAdjStkNoData']; ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="xCNRptFilterTitle">
                <div class="text-left">
                    <label class="xCNTextConsOth"><?= $aDataTextRef['tRptConditionInReport']; ?></label>
                </div>
            </div>
            <?php if (isset($aDataFilter['tBchCodeSelect']) && !empty($aDataFilter['tBchCodeSelect'])) : ?>
                <!-- ============================ ฟิวเตอร์ข้อมูล สาขา ============================ -->
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchFrom']; ?> : </span> <?php echo ($aDataFilter['bBchStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tBchNameSelect']; ?></label>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ((isset($aDataFilter['tPdtSupplierCodeFrom']) && !empty($aDataFilter['tPdtSupplierCodeFrom'])) && (isset($aDataFilter['tPdtSupplierCodeTo']) && !empty($aDataFilter['tPdtSupplierCodeTo']))) { ?>
                <!-- ===== ฟิวเตอร์ข้อมูล ผู้จำหน่าย =================================== -->
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptSplFrom'] . ' : </span>' . $aDataFilter['tPdtSupplierNameFrom']; ?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptSplTo'] . ' : </span>' . $aDataFilter['tPdtSupplierNameTo']; ?></label>
                    </div>
                </div>
            <?php }; ?>

            <?php if ((isset($aDataFilter['tPdtSgpCodeFrom']) && !empty($aDataFilter['tPdtSgpCodeFrom'])) || (isset($aDataFilter['tPdtSgpCodeTo']) && !empty($aDataFilter['tPdtSgpCodeTo']))) { ?>
                <!-- ===== ฟิวเตอร์ข้อมูล กลุ่มผู้จำหน่าย =================================== -->
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptSplGrpForm'] . ' : </span>' . $aDataFilter['tPdtSgpNameFrom']; ?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptSplGrpTo'] . ' : </span>' . $aDataFilter['tPdtSgpNameTo']; ?></label>
                    </div>
                </div>
            <?php }; ?>

            <?php if ((isset($aDataFilter['tPdtStyCodeFrom']) && !empty($aDataFilter['tPdtStyCodeFrom'])) || (isset($aDataFilter['tPdtStyCodeTo']) && !empty($aDataFilter['tPdtStyCodeTo']))) { ?>
                <!-- ===== ฟิวเตอร์ข้อมูล ประเภทผู้จำหน่าย =================================== -->
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptSplTypeForm'] . ' : </span>' . $aDataFilter['tPdtStyNameFrom']; ?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptSplTypeTo'] . ' : </span>' . $aDataFilter['tPdtStyNameTo']; ?></label>
                    </div>
                </div>
            <?php }; ?>

            <?php if ((isset($aDataFilter['tCstCodeFrom']) && !empty($aDataFilter['tCstCodeFrom'])) || (isset($aDataFilter['tCstCodeTo']) && !empty($aDataFilter['tCstCodeTo']))) { ?>
                <!-- ===== ฟิวเตอร์ข้อมูล ลูกค้า =================================== -->
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptCstFrom'] . ' : </span>' . $aDataFilter['tCstNameFrom']; ?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptCstTo'] . ' : </span>' . $aDataFilter['tCstNameTo']; ?></label>
                    </div>
                </div>
            <?php }; ?>

            <!-- ============================ ฟิวเตอร์ข้อมูล แคชเชียร์ ============================ -->
            <?php if ((isset($aDataFilter['tCashierCodeFrom']) && !empty($aDataFilter['tCashierCodeFrom'])) && (isset($aDataFilter['tCashierCodeTo']) && !empty($aDataFilter['tCashierCodeTo']))) : ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptCashierFrom'] . ' : </span>' . $aDataFilter['tCashierNameFrom']; ?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptCashierTo'] . ' : </span>' . $aDataFilter['tCashierNameTo']; ?></label>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($aDataFilter['tCashierCodeSelect']) && !empty($aDataFilter['tCashierCodeSelect'])) : ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptCashierFrom']; ?> : </span> <?php echo ($aDataFilter['bCashierStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tCashierCodeSelect']; ?></label>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>