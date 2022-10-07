<?php
    $aCompanyInfo   = $aDataViewRpt['aCompanyInfo'];
    $aDataFilter    = $aDataViewRpt['aDataFilter'];
    $aDataTextRef   = $aDataViewRpt['aDataTextRef'];
    $aDataReport    = $aDataViewRpt['aDataReport'];
    $nDecimalShw    = get_cookie('tOptDecimalShow');
?>
<style type="text/css">
    .xCNFooterRpt{ border-bottom: 7px double #ddd;} .table thead th, .table>thead>tr>th, .table tbody tr, .table>tbody>tr>td{ border: 0px transparent !important;} .table>thead:first-child>tr:first-child>td, .table>thead:first-child>tr:first-child>th{ border-top: 1px solid black !important; border-bottom: 0px transparent !important;} .table>thead:first-child>tr:first-child>td:nth-child(1), .table>thead:first-child>tr:first-child>th:nth-child(1), .table>thead:first-child>tr:first-child>td:nth-child(2), .table>thead:first-child>tr:first-child>th:nth-child(2), .table>thead:first-child>tr:first-child>td:nth-child(3), .table>thead:first-child>tr:first-child>th:nth-child(3){ border-bottom: 1px dashed #ccc !important;} .xWRptMSORightline1{ border-right: 1px solid black !important;} .xWRptMSOleftline1{ border-left: 1px solid black !important;} .xWRptMSOUnderline{ border-bottom: 1px solid black !important;} .table>thead:first-child>tr:last-child>td, .table>thead:first-child>tr:last-child>th{ border-bottom: 1px solid black !important;} .table>tbody>tr.xCNTrSubFooter{ border-top: 1px solid black !important; border-bottom: 1px solid black !important;} .table>tbody>tr.xCNTrFooter{ border-top: 1px solid black !important; border-bottom: 6px double black !important;} .table>tbody>tr.xCNRptLastPdtList>td:nth-child(3), .table>tbody>tr.xCNRptLastPdtList>td:nth-child(4), .table>tbody>tr.xCNRptLastPdtList>td:nth-child(5), .table>tbody>tr.xCNRptLastPdtList>td:nth-child(6), .table>tbody>tr.xCNRptLastPdtList>td:nth-child(7){ border: 0px solid black !important; border-bottom: 1px dashed #ccc !important;} .table tbody tr.xCNRptLastGroupTr, .table>tbody>tr.xCNRptLastGroupTr>td{ border: 0px solid black !important; border-bottom: 1px dashed #ccc !important;} .table tbody tr.xCNRptSumFooterTrTop, .table>tbody>tr.xCNRptSumFooterTrTop>td{ border: 0px solid black !important; border-top: 1px solid black !important;} .table tbody tr.xCNRptSumFooterTrBottom, .table>tbody>tr.xCNRptSumFooterTrBottom>td{ border: 0px solid black !important; border-bottom: 1px solid black !important;} .table>tfoot>tr{ border-top: 1px solid black !important; border-bottom: 6px double black !important;} @media print{ @page{ size: landscape}}
</style>
<div id="odvRptReprintDocumentHtml">
    <div class="container-fluid xCNLayOutRptHtml">
        <div class="xCNHeaderReport">
            <div class="row">
                <?php include 'application\modules\report\datasources\Address\wRptAddress.php'; ?>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 report-filter">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="text-center">
                                <label class="xCNRptTitle"><?php echo $aDataTextRef['tTitleReport']; ?></label>
                            </div>
                        </div>
                    </div>
                    <!-- ============================ ฟิวเตอร์ข้อมูล สาขา ============================ -->
                    <?php if (isset($aDataFilter['tBchCodeSelect']) && !empty($aDataFilter['tBchCodeSelect'])) : ?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-center">
                                    <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchFrom']; ?> : </span> <?php echo ($aDataFilter['bBchStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tBchNameSelect']; ?></label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ((isset($aDataFilter['tDocDateFrom']) && !empty($aDataFilter['tDocDateFrom'])) && (isset($aDataFilter['tDocDateTo']) && !empty($aDataFilter['tDocDateTo']))) : ?>
                        <!-- ============================ ฟิวเตอร์ข้อมูล วันที่สร้างเอกสาร ============================ -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-center">
                                    <label class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptDateFrom'] ?> : </label> <label><?= date('d/m/Y', strtotime($aDataFilter['tDocDateFrom'])); ?></label>
                                    <label class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptDateTo'] ?> : </label> <label><?= date('d/m/Y', strtotime($aDataFilter['tDocDateTo'])); ?></label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ((isset($aDataFilter['tBchCodeFrom']) && !empty($aDataFilter['tBchCodeFrom'])) && (isset($aDataFilter['tBchCodeTo']) && !empty($aDataFilter['tBchCodeTo']))) { ?>
                        <!-- ============================ ฟิวเตอร์ข้อมูล สาขา ============================ -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-center">
                                    <label class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchFrom'] ?> : </label> <label><?= $aDataFilter['tBchNameFrom']; ?></label>
                                    <label class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchTo'] ?> : </label> <label><?= $aDataFilter['tBchNameTo']; ?></label>
                                </div>
                            </div>
                        </div>
                    <?php }; ?>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    &nbsp;
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="text-right">
                        <label class="xCNRptDataPrint"><?php echo $aDataTextRef['tDatePrint'] . ' ' . date('d/m/Y') . ' ' . $aDataTextRef['tTimePrint'] . ' ' . date('H:i:s'); ?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="xCNContentReport">
            <div id="odvRptReprintDocumentHtmlTbl" class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th nowrap="" class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptReprintDocBchCode']; ?></th>
                            <th nowrap="" class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptReprintDocBchName']; ?></th>
                            <th nowrap="" class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptReprintDocDocNo']; ?></th>
                            <th nowrap="" class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptReprintDocDocCreate']; ?></th>
                            <th nowrap="" class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptReprintDocNumPrint']; ?></th>
                            <th nowrap="" class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptReprintDocCashier']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($aDataReport['aRptData'])): ?>
                            <?php foreach ($aDataReport['aRptData'] as $aData): ?>
                                <tr class="">
                                    <td class="xCNRptDetail"><?php echo $aData['FTBchCode'];?></td>
                                    <td class="xCNRptDetail"><?php echo $aData['FTBchName'];?></td>
                                    <td class="xCNRptDetail"><?php echo $aData['FTXthDocNo'];?></td>
                                    <td class="xCNRptDetail"><?php echo $aData['FDXthHisDateTime'];?></td>
                                    <td class="text-right xCNRptDetail" style="width:5%;"><?php echo number_format($aData['FNXthReprintNum'], $nDecimalShw); ?></td>
                                    <td class="xCNRptDetail"><?php echo $aData['FTXthUsrName'];?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="17" class="text-center xCNRptColumnFooter"><?php echo $aDataTextRef['tRptNoData']; ?></td>
                            </tr>
                        <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="xCNFooterPageRpt">

        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){ var oFilterLabel=$('.report-filter .text-left label:first-child'); var nMaxWidth=0; oFilterLabel.each(function(index){ var nLabelWidth=$(this).outerWidth(); if (nLabelWidth >nMaxWidth){ nMaxWidth=nLabelWidth;}}); $('.report-filter .text-left label:first-child').width(nMaxWidth + 50);});
</script>
