<?php
$aDataReport    = $aDataViewRpt['aDataReport'];
$aDataTextRef   = $aDataViewRpt['aDataTextRef'];
$aDataFilter    = $aDataViewRpt['aDataFilter'];

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
        border-bottom: dashed 1px #333 !important;
    }

    /** แนวตั้ง */
    @media print {
        @page {
            size: landscape
        }
    }
</style>

<div id="odvRptPdtSalePromotionHtml">
    <div class="container-fluid xCNLayOutRptHtml">
        <div class="xCNHeaderReport">
            <div class="row">
                <?php include 'application\modules\report\datasources\Address\wRptAddress.php'; ?>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="text-center">
                                <label class="xCNRptTitle"><?php echo @$aDataTextRef['tTitleReport']; ?></label>
                            </div>
                        </div>
                    </div>

                    <?php if ((isset($aDataFilter['tBchCodeFrom']) && !empty($aDataFilter['tBchCodeFrom'])) && (isset($aDataFilter['tBchCodeTo']) && !empty($aDataFilter['tBchCodeTo']))) { ?>
                        <!-- ============================ ฟิวเตอร์ข้อมูล สาขา ============================ -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-center">
                                    <label class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchFrom'] ?> : </label> <label><?= $aDataFilter['tBchNameFrom']; ?></label>&nbsp;
                                    <label class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchTo'] ?> : </label> <label><?= $aDataFilter['tBchNameTo']; ?></label>
                                </div>
                            </div>
                        </div>
                    <?php }; ?>

                    <?php if ((isset($aDataFilter['tDocDateFrom']) && !empty($aDataFilter['tDocDateFrom'])) && (isset($aDataFilter['tDocDateTo']) && !empty($aDataFilter['tDocDateTo']))) { ?>
                        <!-- ============================ ฟิวเตอร์ข้อมูล วันที่ ============================ -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-center">
                                    <label class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptDateFrom'] ?> : </label> <label><?= date('d/m/Y', strtotime($aDataFilter['tDocDateFrom'])); ?></label>&nbsp;
                                    <label class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptDateTo'] ?> : </label> <label><?= date('d/m/Y', strtotime($aDataFilter['tDocDateTo'])); ?></label>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 "></div>
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
            
                        <tr style="border-bottom: 1px solid black !important">
                            <th nowrap class="text-center xCNRptColumnHeader" style="width:5%;"><?php echo $aDataTextRef['tRptPremRedemBchCode']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="width:10%;"><?php echo $aDataTextRef['tRptPremRedemBchName']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="width:15%;"><?php echo $aDataTextRef['tRptPremRedemDocCreate']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="width:10%;"><?php echo $aDataTextRef['tRptPremRedemDocNo']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="width:10%;"><?php echo $aDataTextRef['tRptPremRedemRefDocExt']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="width:10%;"><?php echo $aDataTextRef['tRptCustCode']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="width:15%;"><?php echo $aDataTextRef['tRptCustName']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="width:5%;"><?php echo $aDataTextRef['tRPCCstForCastVehicleReg']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader"><?php echo $aDataTextRef['tRptPremRedemPdtCode']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="width:15%;"><?php echo $aDataTextRef['tRptPremRedemPdtName']; ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader"><?php echo $aDataTextRef['tRptCabinetnumber']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($aDataReport['aRptData']) && !empty($aDataReport['aRptData'])) : ?>
                            <?php
                            // Set ตัวแปร SumSubFooter และ ตัวแปร SumFooter
                            $nSumFooterFCXsdQty      = 0;
                            $nSumFooterFCXsdNet      = 0;
                            $nSumFooterFCXpdDis      = 0;
                            $nSumFooterFCXsdNetPmt   = 0;

                            $nSumSubFCXsdQty         = 0;
                            $nSumSubFCXsdNet         = 0;
                            $nSumSubFCXpdDis         = 0;
                            $nSumSubFCXsdNetPmt      = 0;

                            $tGetTypePmh  =  '';
                            ?>

                            <?php foreach ($aDataReport['aRptData'] as $nKey => $aValue) : ?>
                                <?php
                                $tBchSum        = $aValue['FTBchCode'] . "&nbsp;&nbsp;" . $aValue['FTBchName'];  // รหัสและชื่อสาขา
                                $nFCXsdQty_Sum  = number_format($aValue['FCXsdQtyAll_SUM'], $nOptDecimalShow); //รวมจำนวน

                                $nGroupMember   = $aValue["FNRptGroupMember"];
                                $nRowPartID     = $aValue["PartID"];

                               
                                ?>

                                <!--  Step 2 แสดงข้อมูลใน TD  -->
                                <tr>
                                    <td class="text-left xCNRptDetail"><?php echo $aValue["FTBchCode"]; ?></td>
                                    <td class="text-left xCNRptDetail"><?php echo $aValue["FTBchName"]; ?></td>
                                    <td class="text-left xCNRptDetail"><?php echo $aValue["FDXshDocDate"]; ?></td>
                                    <td class="text-left xCNRptDetail"><?php echo $aValue["FTXshDocNo"]; ?></td>
                                    <td class="text-left xCNRptDetail"><?php echo $aValue["FTXshRefExt"]; ?></td>
                                    <td class="text-left xCNRptDetail"><?php echo $aValue["FTCstCode"]; ?></td>
                                    <td class="text-left xCNRptDetail"><?php echo $aValue["FTCstName"]; ?></td>
                                    <td class="text-left xCNRptDetail"><?php echo $aValue["FTCarRegNo"]; ?></td>
                                    <td class="text-left xCNRptDetail"><?php echo $aValue["FTPdtCode"]; ?></td>
                                    <td class="text-left xCNRptDetail"><?php echo $aValue["FTPdtName"]; ?></td>
                                    <td class="text-right xCNRptDetail"><?php echo number_format($aValue["FCXsdQtyAll"], $nOptDecimalShow); ?></td>
                                </tr>

                                <?php
                                if ($nRowPartID == $nGroupMember) {
                                    echo "<tr class='xCNRptGrouPing' colspan='11' style='border-top: dashed 0.5px #333 !important; border-bottom: dashed 0.5px #333 !important;'>";

                                    echo "<td class='xCNRptGrouPing  text-left' style='padding-left: 20px !important' colspan='10'>". "รวมสาขา : " . $tBchSum . "</td>";
                                    echo "<td class='text-right xCNRptDetail'>".$nFCXsdQty_Sum."</td>";
                                    echo "</tr>";
                                }
                                ?>


                                <?php
                                // Step 5 เตรียม Parameter สำหรับ SumFooter
                                $nFCXsdQtyAll_Footer    = intval($aValue["FCXsdQtyAll_Footer"]);

                                $aFooterSumData      = array('รวม ', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', number_format($nFCXsdQtyAll_Footer, $nOptDecimalShow));

                                ?>

                            <?php endforeach; ?>

                            <?php
                            //Step 6 : สั่ง Summary Footer
                            $nPageNo    = $aDataReport["aPagination"]["nDisplayPage"];
                            $nTotalPage = $aDataReport["aPagination"]["nTotalPage"];

                            if ($nPageNo == $nTotalPage) {
                                echo "<tr class='xCNTrFooter'>";
                                for ($i = 0; $i < FCNnHSizeOf($aFooterSumData); $i++) {
                                    // if ($i == 0) {
                                    //     $tStyle = 'text-align:left;border-top:1px solid #333;border-bottom:1px solid #333;/*background-color: #CFE2F3;*/';
                                    // } else {
                                    //     $tStyle = 'text-align:right;border-top:1px solid #333;border-bottom:1px solid #333;/*background-color: #CFE2F3;*/';
                                    // }

                                    if (strval($aFooterSumData[$i]) != "N") {
                                        $tFooterVal = $aFooterSumData[$i];
                                    } else {
                                        $tFooterVal = '';
                                    }
                                    if ($i == 0) {
                                        echo "<td class='xCNRptSumFooter text-left' colspan='1'>" . $tFooterVal . "</td>";
                                    } else {
                                        echo "<td class='xCNRptSumFooter text-right'>" . $tFooterVal . "</td>";
                                    }
                                }
                                echo "<tr>";
                            }
                            ?>

                        <?php else : ?>
                            <tr>
                                <td class='text-center xCNTextDetail2' colspan='100%'><?php echo $aDataTextRef['tRptNoData']; ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ((isset($aDataFilter['tMerCodeFrom']) && !empty($aDataFilter['tMerCodeFrom'])) && (isset($aDataFilter['tMerCodeTo']) && !empty($aDataFilter['tMerCodeTo']))
                || (isset($aDataFilter['tShpCodeFrom']) && !empty($aDataFilter['tShpCodeFrom'])) && (isset($aDataFilter['tShpCodeTo']) && !empty($aDataFilter['tShpCodeTo']))
                || (isset($aDataFilter['tPosCodeFrom']) && !empty($aDataFilter['tPosCodeFrom'])) && (isset($aDataFilter['tPosCodeTo']) && !empty($aDataFilter['tPosCodeTo']))
                || (isset($aDataFilter['tBchCodeSelect']))
                || (isset($aDataFilter['tMerCodeSelect']))
                || (isset($aDataFilter['tShpCodeSelect']))
                || (isset($aDataFilter['tPosCodeSelect']))
            ) { ?>

                <div class="xCNRptFilterTitle">
                    <div class="text-left">
                        <label class="xCNTextConsOth"><?= $aDataTextRef['tRptConditionInReport']; ?></label>
                    </div>
                </div>
            <?php }; ?>

             <!-- ============================ ฟิวเตอร์ข้อมูล ตัวแทนขาย ============================ -->
             <?php if (isset($aDataFilter['tAgnCodeSelect']) && !empty($aDataFilter['tAgnCodeSelect'])) : ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptGrpAgency']; ?> : </span> <?php echo ($aDataFilter['tAgnName'])?></label>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================ ฟิวเตอร์ข้อมูล สาขา ============================ -->
            <?php if (isset($aDataFilter['tBchCodeSelect']) && !empty($aDataFilter['tBchCodeSelect'])) : ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchFrom']; ?> : </span> <?php echo ($aDataFilter['bBchStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tBchNameSelect']; ?></label>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================ ฟิวเตอร์ข้อมูล สินค้า ============================ -->
            <?php if( (isset($aDataFilter['tRptPdtCodeFrom']) && !empty($aDataFilter['tRptPdtCodeFrom'])) && (isset($aDataFilter['tRptPdtCodeTo']) && !empty($aDataFilter['tRptPdtCodeTo']))) { ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tPdtCodeFrom']; ?> : </span> <?php echo $aDataFilter['tRptPdtNameFrom']; ?></label>
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tPdtCodeTo']; ?> : </span> <?php echo $aDataFilter['tRptPdtNameTo']; ?></label>
                    </div>
                </div>
            <?php } ;?>  

            <!-- ============================ ฟิวเตอร์ข้อมูล ลูกค้า ============================ -->
            <?php if((isset($aDataFilter['tCstCodeFrom']) && !empty($aDataFilter['tCstCodeFrom'])) && (isset($aDataFilter['tCstCodeTo']) && !empty($aDataFilter['tCstCodeTo']))): ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptCstFrom']; ?> : </span> <?php echo $aDataFilter['tCstNameFrom']; ?></label>
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptCstTo']; ?> : </span> <?php echo $aDataFilter['tCstNameTo']; ?></label>
                    </div>
                </div>
            <?php endif; ?>

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