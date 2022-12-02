<?php
    $aCompanyInfo    = $aDataViewRpt['aCompanyInfo'];
    $aDataFilter     = $aDataViewRpt['aDataFilter'];
    $aDataTextRef    = $aDataViewRpt['aDataTextRef'];
    $aDataReport     = $aDataViewRpt['aDataReport'];
    $nOptDecimalShow = $aDataViewRpt['nOptDecimalShow'];
?>


<style>
    .xCNFooterRpt {
        border-bottom : 7px double #ddd;
    }

    .table thead th, .table>thead>tr>th, .table tbody tr, .table>tbody>tr>td {
        border: 0px transparent !important;
    }

    .table>thead:first-child>tr:first-child>td, .table>thead:first-child>tr:first-child>th {
        border-top: 1px solid black !important;
        border-bottom : 1px solid black !important;
    }

    .table>tbody>tr.xCNTrSubFooter{
        border-top: 1px solid black !important;
        border-bottom : 1px solid black !important;
    }

    .table>tbody>tr.xCNTrFooter{
        border-top: 1px solid black !important;
        border-bottom
         : 1px solid black !important;
    }
    .table tbody tr.xCNHeaderGroup, .table>tbody>tr.xCNHeaderGroup>td {
        font-size: 18px !important;
        font-weight: 600;
    }
    .table>tbody>tr.xCNHeaderGroup>td:nth-child(4), .table>tbody>tr.xCNHeaderGroup>td:nth-child(5) {
        text-align: right;
    }

    /*แนวนอน*/
    @media print{@page {
        size: A4 portrait;
        margin: 5mm 5mm 5mm 5mm;
        }
    }
</style>

<div id="odvRptProductPdtGrp">
    <div class="container-fluid xCNLayOutRptHtml">
        <div class="xCNHeaderReport">
            <div class="row">
                <?php include 'application\modules\report\datasources\Address\wRptAddress.php'; ?>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="text-center">
                                <label class="xCNRptTitle"><?php echo $aDataTextRef['tTitleReport']; ?></label>
                            </div>
                        </div>
                    </div>

                    <?php if ((isset($aDataFilter['tBchCodeFrom']) && !empty($aDataFilter['tBchCodeFrom'])) && (isset($aDataFilter['tBchCodeTo']) && !empty($aDataFilter['tBchCodeTo']))) { ?>
                        <!-- ============================ ฟิวเตอร์ข้อมูล สาขา ============================ -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-center">
                                    <label class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchFrom']?> : </label>   <label><?=$aDataFilter['tBchNameFrom'];?></label>&nbsp;
                                    <label class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchTo']?> : </label>     <label><?=$aDataFilter['tBchNameTo']; ?></label>
                                </div>
                            </div>
                        </div>
                    <?php }; ?>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 "></div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 table-responsive">
                    <div class="text-right">
                        <?php date_default_timezone_set('Asia/Bangkok'); ?>
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
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:left; width:10%;" rowspan="2"><?php echo $aDataTextRef['tRptPdtChain']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:left; width:20%;" rowspan="2"><?php echo $aDataTextRef['tRptAgnName']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:right; width:5%;" rowspan="2"><?php echo $aDataTextRef['tRptPdtCode']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:left; width:20%;" rowspan="2"><?php echo $aDataTextRef['tRptPdtName']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:left; width:20%;" rowspan="2"><?php echo $aDataTextRef['tRptBchName']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:left; width:20%;" rowspan="2"><?php echo $aDataTextRef['tRptWahName']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader" colspan="3" style='border-bottom: dashed 1px #333 !important;  width:20%;'><?php echo $aDataTextRef['tRptPdtInventory'];?></th>
                        </tr>
                        <tr style="border-bottom : 1px solid black !important;">
                            <th nowrap class="text-right xCNRptColumnHeader" style="width:7%;"><?php echo $aDataTextRef['tRptPdtGrpAmt']; ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="width:5%;"><?php echo $aDataTextRef['tRptCost']; ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="width:5%;"><?php echo $aDataTextRef['tRptTotalCap'];   ?></th>
                        </tr>

                        <tr>

                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($aDataReport['aRptData']) && !empty($aDataReport['aRptData'])) { ?>
                            <?php
                                $nVal               = 0;
                                $tPDTCodeOld        = '';
                                $tChainCodeName     = 'First';
                            ?>

                            <!--กลาง-->
                            <?php foreach ($aDataReport['aRptData'] as $nKey => $aValue) { ?>

                                <?php
                                    // Step 1 เตรียม Parameter สำหรับการ Groupping
                                    // $tWahCode       = $aValue["FTWahName"];
                                    // $tPDTCode       = $aValue["FTPdtCode"];
                                    // $tPDTName       = $aValue["FTPdtName"];
                                    // $nGroupMember   = $aValue["FNRptGroupMember"];
                                    $nRowPartID     = $aValue["FNRowPartChainID"];
                                    $nRowAgnID      = $aValue["FNRowPartAgnID"];
                                    $nRowPdtID     = $aValue["FNRowPartPdtID"];
                                    $nRowBchID     = $aValue["FNRowPartBchID"];

                                    if($nRowPartID != 1) {
                                        $tPgpChain = '';
                                        $tPgpChainName = '';
                                    }else{
                                        if($aValue["FTPgpChain"] == null || $aValue["FTPgpChain"] ==  ''){
                                            $tPgpChain = '';
                                        }else {
                                            $tPgpChain = '(' . $aValue["FTPgpChain"] . ')';
                                        }
    
                                        if($aValue["FTPgpChainName"] == null || $aValue["FTPgpChainName"] ==  ''){
                                            $tPgpChainName = 'อื่น ๆ';
                                        }else {
                                            $tPgpChainName = $aValue["FTPgpChainName"];
                                        }
                                    }


                                    if($nRowAgnID != 1) {
                                        $tAgnID = '';
                                        $tAgnName = '';
                                    }else{
                                        if($aValue["FTAgnCode"] == null || $aValue["FTAgnCode"] ==  ''){
                                            $tAgnID = '';
                                        }else {
                                            $tAgnID = '(' . $aValue["FTAgnCode"] . ')';
                                        }
    
                                        if($aValue["FTAgnName"] == null || $aValue["FTAgnName"] ==  ''){
                                            $tAgnName = '';
                                        }else {
                                            $tAgnName = $aValue["FTAgnName"];
                                        }
                                    }

                                    if($nRowPdtID != 1) {
                                        $tPdtID = '';
                                        $tPdtName = '';
                                    }else{
                                        // if($aValue["FTPdtCode"] == null || $aValue["FTPdtCode"] ==  ''){
                                        //     $tPdtID = '';
                                        // }else {
                                            $tPdtID = $aValue["FTPdtCode"];
                                        // }
    
                                        if($aValue["FTPdtName"] == null || $aValue["FTPdtName"] ==  ''){
                                            $tPdtName = '';
                                        }else {
                                            $tPdtName = $aValue["FTPdtName"];
                                        }
                                    }

                                    if($nRowBchID != 1) {
                                        $tBchID = '';
                                        $tBchName = '';
                                    }else{
                                        // if($aValue["FTBchCode"] == null || $aValue["FTBchCode"] ==  ''){
                                        //     $tBchID = '';
                                        // }else {
                                            $tBchID = '(' . $aValue["FTBchCode"] . ')';
                                        // }
    
                                        if($aValue["FTBchName"] == null || $aValue["FTBchName"] ==  ''){
                                            $tBchName = '';
                                        }else {
                                            $tBchName = $aValue["FTBchName"];
                                        }
                                    }

                
                                ?>

                                <?php
                                    if($nCostType == 0) { 
                                        $nPdtCost = $aValue["FCPdtCostStd"] ;
                                        $nSumPdtCost = $aValue["FCSumCostStd"] ;
                                        $nWahCost = $aValue["FCPdtCostStd"] ;
                                        $nSumWahCost = $aValue["FTPdtCostStdAmt"];
                                    } else {
                                        switch ($nCostType) {
                                            case 1 :
                                                $nPdtCost = $aValue["FCPdtCostAVGEX"] ;
                                                $nSumPdtCost = $aValue["FCSumCostAvg"] ;
                                                $ntWahCost = $aValue["FCPdtCostAVGEX"] ;
                                                $nSumWahCost = $aValue["FCPdtCostTotal"];
                                                break;
                                            case 3 :
                                                $nPdtCost = $aValue["FCPdtCostStd"] ;
                                                $nSumPdtCost = $aValue["FCSumCostStd"] ;
                                                $nWahCost = $aValue["FCPdtCostStd"] ;
                                                $nSumWahCost = $aValue["FTPdtCostStdAmt"];
                                                break;
                                            default : 
                                                $nPdtCost = $aValue["FCPdtCostStd"] ;
                                                $nSumPdtCost = $aValue["FCSumCostStd"] ;
                                                $nWahCost = $aValue["FCPdtCostStd"] ;
                                                $nSumWahCost = $aValue["FTPdtCostStdAmt"]; 
                                                break;
                                        }
                                    }                         
                                ?>

                                <?php 
                                    if ($nRowPdtID == 1) {
                                        if ($nRowAgnID == 1 && $nRowPartID == 1) {
                                            echo "<tr>";
                                            echo "    <td nowrap style='border-top: solid 1px #333 !important;' class='text-left xCNRptGrouPing' >". $tPgpChain . $tPgpChainName. "</td>";
                                            echo "    <td nowrap style='border-top: solid 1px #333 !important;' class='text-left xCNRptGrouPing' >". $tAgnID . ' ' .$tAgnName. "</td>";
                                            echo "    <td nowrap style='border-top: solid 1px #333 !important;' class='text-right xCNRptGrouPing' >". $tPdtID. "</td>";
                                            echo "    <td nowrap style='border-top: solid 1px #333 !important;' class='text-left xCNRptGrouPing' >". $tPdtName. "</td>";
                                            echo "    <td nowrap style='border-top: solid 1px #333 !important;' class='text-left xCNRptGrouPing' ></td>";
                                            echo "    <td nowrap style='border-top: solid 1px #333 !important;' class='text-left xCNRptGrouPing' ></td>";
                                            echo "    <td nowrap style='border-top: solid 1px #333 !important;' class='text-right xCNRptGrouPing'>".number_format($aValue['FCStkQty_SubTotal'], $nOptDecimalShow) ."</td>";
                                            echo "    <td nowrap style='border-top: solid 1px #333 !important;' class='text-right xCNRptGrouPing'>".number_format($nPdtCost, $nOptDecimalShow) ."</td>";
                                            echo "    <td nowrap style='border-top: solid 1px #333 !important;' class='text-right xCNRptGrouPing'>".number_format($nSumPdtCost, $nOptDecimalShow) ."</td>";
                                            echo "</tr>";
                                        } else if ($nRowAgnID == 1 && $nRowPartID != 1) {
                                            echo "<tr>";
                                            echo "    <td nowrap class='text-left xCNRptGrouPing' ></td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-left xCNRptGrouPing' >". $tAgnID . ' ' .$tAgnName. "</td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-right xCNRptGrouPing' >". $tPdtID. "</td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-left xCNRptGrouPing' >". $tPdtName. "</td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-left xCNRptGrouPing' ></td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-left xCNRptGrouPing' ></td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-right xCNRptGrouPing'>".number_format($aValue['FCStkQty_SubTotal'], $nOptDecimalShow) ."</td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-right xCNRptGrouPing'>".number_format($nPdtCost, $nOptDecimalShow) ."</td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-right xCNRptGrouPing'>".number_format($nSumPdtCost, $nOptDecimalShow) ."</td>";
                                            echo "</tr>";
                                        } else {
                                            echo "<tr>";
                                            echo "    <td nowrap class='text-left xCNRptGrouPing' ></td>";
                                            echo "    <td nowrap class='text-left xCNRptGrouPing' ></td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-right xCNRptGrouPing' >". $tPdtID. "</td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-left xCNRptGrouPing' >". $tPdtName. "</td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-left xCNRptGrouPing' ></td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-left xCNRptGrouPing' ></td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-right xCNRptGrouPing'>".number_format($aValue['FCStkQty_SubTotal'], $nOptDecimalShow) ."</td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-right xCNRptGrouPing'>".number_format($nPdtCost, $nOptDecimalShow) ."</td>";
                                            echo "    <td nowrap style='border-top: dashed 1px #333 !important;' class='text-right xCNRptGrouPing'>".number_format($nSumPdtCost, $nOptDecimalShow) ."</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        // echo "<tr>";
                                        // echo "    <td nowrap class='text-left xCNRptDetail' >". $tPgpChain . $tPgpChainName. "</td>";
                                        // echo "    <td nowrap class='text-left xCNRptDetail' >". $tAgnID . ' ' .$tAgnName. "</td>";
                                        // echo "    <td nowrap class='text-left xCNRptDetail' >". $tPdtID. "</td>";
                                        // echo "    <td nowrap class='text-left xCNRptDetail' >". $tPdtName. "</td>";
                                        // echo "    <td nowrap class='text-left xCNRptDetail' >(". $aValue["FTBchCode"] . ') ' .$aValue["FTBchName"] ."</td>";
                                        // echo "    <td nowrap class='text-left xCNRptDetail' >(". $aValue["FTWahCode"] . ') ' .$aValue["FTWahName"] ."</td>";
                                        // echo "    <td nowrap class='text-right xCNRptDetail'>".number_format($aValue['FCStkQty'], $nOptDecimalShow) ."</td>";
                                        // echo "    <td nowrap class='text-right xCNRptDetail'>".number_format(20.00, $nOptDecimalShow) ."</td>";
                                        // echo "    <td nowrap class='text-right xCNRptDetail'>".number_format(20.00, $nOptDecimalShow) ."</td>";
                                        // echo "</tr>";
                                    }
                                ?>
                                <tr>
                                    <!-- <td nowrap class="text-left xCNRptDetail" ><?php echo $tPgpChain . $tPgpChainName; ?></td> -->
                                    <!-- <td nowrap class="text-left xCNRptDetail" ><?php echo $tAgnID . ' ' .$tAgnName; ?></td> -->
                                    <!-- <td nowrap class="text-left xCNRptDetail" ><?php echo $tPdtID; ?></td> -->
                                    <!-- <td nowrap class="text-left xCNRptDetail" ><?php echo $tPdtName; ?></td> -->
                                    <td nowrap class="text-left xCNRptDetail" ></td>
                                    <td nowrap class="text-left xCNRptDetail" ></td>
                                    <td nowrap class="text-left xCNRptDetail" ></td>
                                    <td nowrap class="text-left xCNRptDetail" ></td>
                                    <td nowrap class="text-left xCNRptDetail" ><?php echo $aValue["FTBchCode"] . ' ' .$aValue["FTBchName"]; ?></td>
                                    <td nowrap class="text-left xCNRptDetail" >(<?php echo $aValue["FTWahCode"]; ?>) <?php echo $aValue["FTWahName"]; ?></td>
                                    <td nowrap class="text-right xCNRptDetail"><?php echo number_format($aValue["FCStkQty"], $nOptDecimalShow) ?></td>
                                    <td nowrap class="text-right xCNRptDetail"><?php echo number_format($nWahCost, $nOptDecimalShow) ?></td>
                                    <td nowrap class="text-right xCNRptDetail"><?php echo number_format($nSumWahCost, $nOptDecimalShow) ?></td>
                                </tr>
                                
                                <?php
                                    // Step 1 เตรียม Parameter สำหรับการ Groupping
                                    // $tWahCode       = $aValue["FTWahName"];
                                    // $tPDTCode       = $aValue["FTPdtCode"];
                                    // $tPDTName       = $aValue["FTPdtName"];
                                    // $nGroupMember   = $aValue["FNRptGroupMember"];
                                    // $nRowPartID     = $aValue["FNRowPartID"];
                                ?>

                        
                            <?php } ?>

                            <tr><td class='text-center xCNTrSubFooter' colspan='100%'></td></tr>
                        <?php } else { ?>
                            <tr><td class='text-center xCNTextDetail2' colspan='100%'><?php echo $aDataTextRef['tRptAdjStkNoData']; ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if ((isset($aDataFilter['tWahNameFrom']) && !empty($aDataFilter['tWahNameFrom'])) && (isset($aDataFilter['tWahNameTo']) && !empty($aDataFilter['tWahNameTo']))
                        || (isset($aDataFilter['tMerCodeFrom']) && !empty($aDataFilter['tMerCodeFrom'])) && (isset($aDataFilter['tMerCodeTo']) && !empty($aDataFilter['tMerCodeTo']))
                        || (isset($aDataFilter['tShpCodeFrom']) && !empty($aDataFilter['tShpCodeFrom'])) && (isset($aDataFilter['tShpCodeTo']) && !empty($aDataFilter['tShpCodeTo']))
                        || (isset($aDataFilter['tPosCodeFrom']) && !empty($aDataFilter['tPosCodeFrom'])) && (isset($aDataFilter['tPosCodeTo']) && !empty($aDataFilter['tPosCodeTo']))
                        || (isset($aDataFilter['tWahCodeFrom']) && !empty($aDataFilter['tWahCodeFrom'])) && (isset($aDataFilter['tWahCodeTo']) && !empty($aDataFilter['tWahCodeTo']))
                        || (isset($aDataFilter['tWahCodeFrom']) && !empty($aDataFilter['tWahCodeFrom'])) && (isset($aDataFilter['tWahCodeTo']) && !empty($aDataFilter['tWahCodeTo']))
                        || (isset($aDataFilter['tBchCodeSelect']))
                        || (isset($aDataFilter['tMerCodeSelect']))
                        || (isset($aDataFilter['tShpCodeSelect']))
                        || (isset($aDataFilter['tPosCodeSelect']))
                        || (isset($aDataFilter['tWahCodeSelect']))
                        ){ ?>
                    <div class="xCNRptFilterTitle">
                        <div class="text-left">
                            <label class="xCNTextConsOth"><?=$aDataTextRef['tRptConditionInReport'];?></label>
                        </div>
                    </div>
                <?php }; ?>

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
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptMerFrom'].' : </span>'.$aDataFilter['tMerNameFrom'];?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptMerTo'].' : </span>'.$aDataFilter['tMerNameTo'];?></label>
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
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptShopFrom'].' : </span>'.$aDataFilter['tShpNameFrom'];?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptShopTo'].' : </span>'.$aDataFilter['tShpNameTo'];?></label>
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

            <!-- ============================ ฟิวเตอร์ข้อมูล คลังสินค้า ============================ -->
            <?php if ((isset($aDataFilter['tWahCodeFrom']) && !empty($aDataFilter['tWahCodeFrom'])) && (isset($aDataFilter['tWahCodeTo']) && !empty($aDataFilter['tWahCodeTo']))) : ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjWahFrom'].' : </span>'.$aDataFilter['tWahNameFrom'];?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjWahTo'].' : </span>'.$aDataFilter['tWahNameTo'];?></label>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($aDataFilter['tWahCodeSelect']) && !empty($aDataFilter['tWahCodeSelect'])) : ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjWahFrom']; ?> : </span> <?php echo ($aDataFilter['bWahStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tWahNameSelect']; ?></label>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================ ฟิวเตอร์ข้อมูล สินค้ากลุ่ม ============================ -->
            <?php if ((isset($aDataFilter['tPdtGrpCodeFrom']) && !empty($aDataFilter['tPdtGrpCodeFrom'])) && (isset($aDataFilter['tPdtGrpCodeTo']) && !empty($aDataFilter['tPdtGrpCodeTo']))) : ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tPdtGrpFrom'].' : </span>'.$aDataFilter['tPdtGrpNameFrom'];?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tPdtGrpTo'].' : </span>'.$aDataFilter['tPdtGrpNameTo'];?></label>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="xCNFooterPageRpt">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="text-right">
                        <?php if ($aDataReport["aPagination"]["nTotalPage"] > 0) { ?>
                            <label class="xCNRptLabel"><?php echo $aDataReport["aPagination"]["nDisplayPage"] . ' / ' . $aDataReport["aPagination"]["nTotalPage"]; ?></label>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        var oFilterLabel = $('.report-filter .text-left label:first-child');
        var nMaxWidth = 0;
        oFilterLabel.each(function(index){
            var nLabelWidth = $(this).outerWidth();
            if(nLabelWidth > nMaxWidth){
                nMaxWidth = nLabelWidth;
            }
        });
        $('.report-filter .text-left label:first-child').width(nMaxWidth + 50);
    });
</script>
