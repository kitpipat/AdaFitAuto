<?php
    $aDataFilterReport  = $aDataViewRpt['aDataFilter'];
    $aDataTextRef       = $aDataViewRpt['aDataTextRef'];
    $aDataReport        = $aDataViewRpt['aDataReport'];
    $nOptDecimalShow    = $aDataViewRpt['nOptDecimalShow'];
?>
<style type="text/css">
    .xCNFooterRpt {border-bottom : 7px double #ddd;}
    .table thead th, .table>thead>tr>th, .table tbody tr, .table>tbody>tr>td {border: 0px transparent !important;}
    .table>thead:first-child>tr:first-child>td, .table>thead:first-child>tr:first-child>th {border-top: 1px solid black !important;border-bottom : 1px solid black !important;}
    .table>tbody>tr.xCNTrSubFooter{border-top: 1px solid black !important;border-bottom : 1px solid black !important;}
    .table>tbody>tr.xCNTrFooter{border-top: 1px solid black !important;}
    .table tbody tr.xCNHeaderGroup, .table>tbody>tr.xCNHeaderGroup>td {font-size: 18px !important;font-weight: 600;}
    .table>tbody>tr.xCNHeaderGroup>td:nth-child(4), .table>tbody>tr.xCNHeaderGroup>td:nth-child(5) {text-align: right;}
    /*แนวนอน*/
    @media print{
        @page {
            size: A4 landscape;
            margin: 5mm 5mm 5mm 5mm;
        }
    }
</style>
<div id="odvRptSaleFCCompVDHtml">
    <div class="container-fluid xCNLayOutRptHtml">
        <div class="xCNHeaderReport">
            <div class="row">
                <?php include 'application\modules\report\datasources\Address\wRptAddress.php'; ?>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <div class="text-center">
                        <label class="xCNRptTitle"><?php echo $aDataTextRef['tTitleReport']; ?></label>
                    </div>
                    <?php if((isset($aDataFilter['tDocDateFrom']) && !empty($aDataFilter['tDocDateFrom'])) && (isset($aDataFilter['tDocDateTo']) && !empty($aDataFilter['tDocDateTo']))): ?>
                        <!-- ============================ ฟิวเตอร์ข้อมูล วันที่สร้างเอกสาร ============================ -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-center xCNRptFilter">
                                    <label class="xCNRptLabel"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptDateFrom'].' : </span>'.date('d/m/Y',strtotime($aDataFilter['tDocDateFrom']));?></label>
                                    <label class="xCNRptLabel"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptDateTo'].' : </span>'.date('d/m/Y',strtotime($aDataFilter['tDocDateTo']));?></label>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>
                    <?php if( (isset($aDataFilter['tBchCodeFrom']) && !empty($aDataFilter['tBchCodeFrom'])) && (isset($aDataFilter['tBchCodeTo']) && !empty($aDataFilter['tBchCodeTo']))) { ?>
                        <!-- ============================ ฟิวเตอร์ข้อมูล สาขา ============================ -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-center xCNRptFilter">
                                    <label class="xCNRptLabel"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchFrom'].' : </span>'.$aDataFilter['tBchNameFrom'];?></label>
                                    <label class="xCNRptLabel"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchTo'].' : </span>'.$aDataFilter['tBchNameTo'];?></label>
                                </div>
                            </div>
                        </div>
                    <?php } ;?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="text-right">
                        <label class="xCNRptDataPrint"><?php echo $aDataTextRef['tDatePrint'].' '.date('d/m/Y').' '.$aDataTextRef['tTimePrint'].' '.date('H:i:s');?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="xCNContentReport">
            <div id="odvRptTableAdvance" class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th nowrap class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSaleFCCompVDAgency']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSaleFCCompVDBranch']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSaleFCCompVDPdtCode']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSaleFCCompVDPdtName']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSaleFCCompVDPdtGroup']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSaleFCCompVDPdtType']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSaleFCCompVDPdtCat1']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSaleFCCompVDPdtCat2']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader" colspan="2"><?php echo $aDataTextRef['tRptSaleFCCompVDBuyForHQ']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader" colspan="2"><?php echo $aDataTextRef['tRptSaleFCCompVDBuyForVD']; ?></th>
                        </tr>
                        <tr>
                            <th nowrap class="text-center xCNRptColumnHeader"></th>
                            <th nowrap class="text-center xCNRptColumnHeader"></th>
                            <th nowrap class="text-center xCNRptColumnHeader"></th>
                            <th nowrap class="text-center xCNRptColumnHeader"></th>
                            <th nowrap class="text-center xCNRptColumnHeader"></th>
                            <th nowrap class="text-center xCNRptColumnHeader"></th>
                            <th nowrap class="text-center xCNRptColumnHeader"></th>
                            <th nowrap class="text-center xCNRptColumnHeader"></th>
                            <th nowrap class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSaleFCCompVDPurchaseAmt']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSaleFCCompVDPercentAmt']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSaleFCCompVDPurchaseAmt']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSaleFCCompVDPercentAmt']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($aDataReport['aRptData']) && !empty($aDataReport['aRptData'])):?>
                            <?php
                                // Set ตัวแปร Sum - SubFooter
                                $cSumSubXpdNetAfHDHQ    = 0;
                                $cSumSubXpdPerPoByHQ    = 0;
                                $cSumSubXpdNetAfHDVD    = 0;
                                $cSumSubXpdPerPoByVD    = 0;
                                
                                // Set ตัวแปร SumFooter
                                $nSumFootXpdNetAfHDHQ   = 0;
                                $cSumFootXpdPerPoByHQ   = 0;
                                $cSumFootXpdNetAfHDVD   = 0;
                                $cSumFootXpdPerPoByVD   = 0;
                            ?>
                            <?php foreach($aDataReport['aRptData'] as $nKey=>$aValue):?>
                                <?php
                                    // Step 1 เตรียม Parameter สำหรับการ Groupping
                                    $tBchCodeGroup  = $aDataTextRef['tRptBranch'].' '.$aValue["FTBchName"];
                                    // $tRptDocDate    = date('Y-m-d H:i:s', strtotime($aValue["FDCreateOn"]));
                                    $nGroupMember   = $aValue["FNRptGroupMember"];
                                    $nRowPartID     = $aValue["FNRowPartID"];
                                ?>
                                <?php
                                    //Step 2 Groupping data
                                    $aGrouppingData = array($tBchCodeGroup);
                                    // Parameter
                                    // $nRowPartID      = ลำดับตามกลุ่ม
                                    // $aGrouppingData  = ข้อมูลสำหรับ Groupping
                                    FCNtHRPTHeadGrouppingRptTSPBch($nRowPartID,$aGrouppingData);
                                ?>
                                <!--  Step 2 แสดงข้อมูลใน TD  -->
                                <tr>
                                    <td class="text-left xCNRptDetail"><?php echo '&nbsp;&nbsp;'.$aValue["FTAgnName"];?></td>
                                    <td class="text-left xCNRptDetail"><?php echo ($aValue["FTBchName"] != "" ? $aValue["FTBchName"] : "-");?></td>
                                    <td class="text-left xCNRptDetail"><?php echo ($aValue["FTPdtCode"] != "" ? $aValue["FTPdtCode"] : "-");?></td>
                                    <td class="text-left xCNRptDetail"><?php echo ($aValue["FTPdtName"] != "" ? $aValue["FTPdtName"] : "-");?></td>
                                    <td class="text-left xCNRptDetail"><?php echo ($aValue["FTPgpChainName"] != "" ? $aValue["FTPgpChainName"] : "-");?></td>
                                    <td class="text-left xCNRptDetail"><?php echo ($aValue["FTPtyName"] != "" ? $aValue["FTPtyName"] : "-");?></td>
                                    <td class="text-left xCNRptDetail"><?php echo ($aValue["FTCatName1"] != "" ? $aValue["FTCatName1"] : "-");?></td>
                                    <td class="text-left xCNRptDetail"><?php echo ($aValue["FTCatName2"] != "" ? $aValue["FTCatName2"] : "-");?></td>
                                    <td class="text-right xCNRptDetail"><?php echo number_format($aValue["FCXpdNetAfHDHQ"], $nOptDecimalShow);?></td>
                                    <td class="text-right xCNRptDetail"><?php echo number_format($aValue["FCXpdPerPoByHQ"], $nOptDecimalShow);?></td>
                                    <td class="text-right xCNRptDetail"><?php echo number_format($aValue["FCXpdNetAfHDVD"], $nOptDecimalShow);?></td>
                                    <td class="text-right xCNRptDetail"><?php echo number_format($aValue["FCXpdPerPoByVD"], $nOptDecimalShow);?></td>
                                </tr>
                                <?php
                                    // Step 3 : เตรียม Parameter สำหรับ Summary Sub Footer
                                    $cSumSubXpdNetAfHDHQ    = number_format($aValue["FCXpdNetAfHDHQ_SUM"], $nOptDecimalShow);
                                    $cSumSubXpdPerPoByHQ    = number_format($aValue["FCXpdPerPoByHQ_SUM"], $nOptDecimalShow);
                                    $cSumSubXpdNetAfHDVD    = number_format($aValue["FCXpdNetAfHDVD_SUM"], $nOptDecimalShow);
                                    $cSumSubXpdPerPoByVD    = number_format($aValue["FCXpdPerPoByVD_SUM"], $nOptDecimalShow);
                                    $tSumBranch             = $aDataTextRef['tRptTotal'].' '.$aValue["FTBchName"];
                                    $aSumFooter             = array($tSumBranch,'N','N','N','N','N','N',$cSumSubXpdNetAfHDHQ,$cSumSubXpdPerPoByHQ,$cSumSubXpdNetAfHDVD,$cSumSubXpdPerPoByVD);
                                    // Step 4 : สั่ง Summary SubFooter
                                    // Parameter
                                    // $nGroupMember    = จำนวนข้อมูลทั้งหมดในกลุ่ม
                                    // $nRowPartID      = ลำดับข้อมูลในกลุ่ม
                                    // $aSumFooter      =  ข้อมูล Summary SubFooter
                                    FCNtHRPTSumSubFooter3($nGroupMember,$nRowPartID,$aSumFooter,2);

                                    //Step 5 เตรียม Parameter สำหรับ SumFooter
                                    $nSumFootXpdNetAfHDHQ   = number_format($aValue["FCXpdNetAfHDHQ_Footer"], $nOptDecimalShow);
                                    $cSumFootXpdPerPoByHQ   = number_format($aValue["FCXpdPerPoByHQ_Footer"], $nOptDecimalShow);
                                    $cSumFootXpdNetAfHDVD   = number_format($aValue["FCXpdNetAfHDVD_Footer"], $nOptDecimalShow);
                                    $cSumFootXpdPerPoByVD   = number_format($aValue["FCXpdPerPoByVD_Footer"], $nOptDecimalShow);
                                    $paFooterSumData        = array($aDataTextRef['tRptTotalFooter'],'N','N','N','N','N','N','N',$nSumFootXpdNetAfHDHQ,$cSumFootXpdPerPoByHQ,$cSumFootXpdNetAfHDVD,$cSumFootXpdPerPoByVD);
                                ?>
                            <?php endforeach;?>
                            <?php
                                //Step 6 : สั่ง Summary Footer
                                $nPageNo    = $aDataReport["aPagination"]["nDisplayPage"];
                                $nTotalPage = $aDataReport["aPagination"]["nTotalPage"];
                                FCNtHRPTSumFooter($nPageNo,$nTotalPage,$paFooterSumData);
                            ?>
                        <?php else:?>
                            <tr><td class='text-center xCNTextDetail2' colspan='100%'><?php echo @$aDataTextRef['tRptNoData'];?></td></tr>
                        <?php endif;?>
                    </tbody>
                </table>
            </div>

            <!-- ============================ ฟิวเตอร์ข้อมูล ============================ -->
            <div class="xCNRptFilterTitle">
                <div class="text-left">
                    <label class="xCNTextConsOth"><?=$aDataTextRef['tRptConditionInReport'];?></label>
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

            <!-- ============================ ฟิวเตอร์ข้อมูล สินค้า ============================ -->
            <?php if((isset($aDataFilter['tPdtCodeFrom']) && !empty($aDataFilter['tPdtCodeFrom'])) && (isset($aDataFilter['tPdtCodeTo']) && !empty($aDataFilter['tPdtCodeTo']))): ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tPdtCodeFrom'].' : </span>'.$aDataFilter['tPdtNameFrom'];?></label>
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tPdtCodeTo'].' : </span>'.$aDataFilter['tPdtNameTo'];?></label>
                    </div>
                </div>
            <?php endif;?>

            <!-- ============================ ฟิวเตอร์ข้อมูล กลุ่มสินค้า ============================ -->
            <?php if((isset($aDataFilter['tPdtGrpNameFrom']) && !empty($aDataFilter['tPdtGrpNameFrom'])) && (isset($aDataFilter['tPdtGrpNameTo']) && !empty($aDataFilter['tPdtGrpNameTo']))): ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptDisplayBlock" ><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tPdtGrpFrom'].' : </span>'.$aDataFilter['tPdtGrpNameFrom'];?></label>
                        <label class="xCNRptDisplayBlock" ><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tPdtGrpTo'].' : </span>'.$aDataFilter['tPdtGrpNameTo'];?></label>
                    </div>
                </div>
            <?php endif;?>

            <!-- ============================ ฟิวเตอร์ข้อมูล ประเภทสินค้า ============================ -->
            <?php if((isset($aDataFilter['tPdtTypeNameFrom']) && !empty($aDataFilter['tPdtTypeNameFrom'])) && (isset($aDataFilter['tPdtTypeNameTo']) && !empty($aDataFilter['tPdtTypeNameTo']))): ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptDisplayBlock" ><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tPdtTypeFrom'].' : </span>'.$aDataFilter['tPdtTypeNameFrom'];?></label>
                        <label class="xCNRptDisplayBlock" ><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tPdtTypeTo'].' : </span>'.$aDataFilter['tPdtTypeNameTo'];?></label>
                    </div>
                </div>
            <?php endif;?>

            <!-- ============================ ฟิวเตอร์ข้อมูล หมวดหมู่สินค้าหลัก ============================ -->
            <?php if(isset($aDataFilter['tCate1From'])  && !empty($aDataFilter['tCate1From'])){ ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptDisplayBlock" ><span class="xCNRptFilterHead">หมวดหมู่สินค้าหลัก : </span><?=$aDataFilter['tCate1FromName'];?></label>
                    </div>
                </div>
            <?php } ?>

            <!-- ============================ ฟิวเตอร์ข้อมูล หมวดหมู่สินค้าย่อย ============================ -->
            <?php if(isset($aDataFilter['tCate2From'])  && !empty($aDataFilter['tCate2From'])){ ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptDisplayBlock" ><span class="xCNRptFilterHead">หมวดหมู่สินค้าย่อย : </span><?=$aDataFilter['tCate2FromName'];?></label>
                    </div>
                </div>
            <?php } ?>

        </div>
        <div class="xCNFooterPageRpt">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="text-right">
                        <?php if($aDataReport["aPagination"]["nTotalPage"] > 0):?>
                            <label class="xCNRptLabel"><?php echo $aDataReport["aPagination"]["nDisplayPage"].' / '.$aDataReport["aPagination"]["nTotalPage"]; ?></label>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var oFilterLabel    = $('.report-filter .text-left label:first-child');
        var nMaxWidth       = 0;
        oFilterLabel.each(function(index){
            var nLabelWidth = $(this).outerWidth();
            if(nLabelWidth > nMaxWidth){
                nMaxWidth = nLabelWidth;
            }
        });
        $('.report-filter .text-left label:first-child').width(nMaxWidth + 50);
    });
</script>