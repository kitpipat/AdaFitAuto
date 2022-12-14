<!DOCTYPE html>
<html lang="th">
<head>
    <title><?php echo language('report/report/report','tRptPrintHtml');?></title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=0"/>

    <!-- ICONS -->
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url(); ?>application/modules/common/assets/images/AdaLogo.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo base_url(); ?>application/modules/common/assets/images/AdaLogo.png">
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/vendor/bootstrap/css/bootstrap.min.css"> 
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/vendor/bootstrap/css/bootstrap.custom.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/vendor/linearicons/style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/vendor/chartist/css/chartist-custom.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/css/cropper.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/css/bootstrap-colorpicker.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/css/globalcss/ContactFrom/main.css">
    <link rel="stylesheet" href="<?php echo base_url('application/modules/common/assets/vendor/loading-bar/loading-bar.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/css/demo.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/css/localcss/ada.layout.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/css/localcss/ada.menu.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/css/localcss/ada.fonts.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>application/modules/common/assets/css/localcss/ada.component.css">

    <!-- JS Script -->
    <script src="<?php echo base_url(); ?>application/modules/common/assets/vendor/jquery/jquery.js"></script>
    <script src="<?php echo base_url(); ?>application/modules/common/assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <!-- JS Custom AdaSoft -->
    <script src="<?php echo base_url(); ?>application/modules/common/assets/src/jCommon.js"></script>
    <script src="<?php echo base_url(); ?>application/modules/common/assets/src/jPageControll.js"></script>
    <script src="<?php echo base_url(); ?>application/modules/common/assets/src/jBrowseModal_New.js"></script>
    <script src="<?php echo base_url(); ?>application/modules/common/assets/src/jAjaxErrorHandle.js"></script>
    <script src="<?php echo base_url(); ?>application/modules/common/assets/src/jTempImage.js"></script>
</head>
<body class="xCNBody">
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
            background-color: #CFE2F3 !important;
        }

        .table>tbody>tr.xCNTrSubFooter{
            border-top: 1px solid black !important;
            border-bottom : 1px solid black !important;
            background-color: #CFE2F3 !important;
        }

        .table>tbody>tr.xCNTrFooter{
            border-top: 1px solid black !important;
            background-color: #CFE2F3 !important;
            border-bottom : 6px double black !important;
        }
        /* .table>tbody>tr.xCNHeaderGroup{
            border-bottom : 1px solid black !important;
            border-top : 1px solid black !important;
        } */
    </style>
    <nav class="navbar navbar-default navbar-fixed-top" style="height:70px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="xWRptNavGroup">
                        <button type="button" id="obtPrintViewHtml" class="btn btn-primary xWBTNRptPrintPreview">
                            <?php echo language('report/report/report','tRptPrintHtml');?>
                        </button>
                        <script type="text/javascript">
                            $('#obtPrintViewHtml').click(function(){
                                $(this).hide();
                                window.print();
                                $(this).show();
                            });
                        </script>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <!-- <div class="xWPageReport btn-toolbar pull-right" style="padding:20px 29px;">
                        <?php if($aDataReport['rnCurrentPage'] == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
                        <button onclick="JCNvReportClickPage('previous')" class="btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
                            <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
                        </button>
                        
                        <?php for($i = max($aDataReport['rnCurrentPage']-2, 1); $i<=max(0, min($aDataReport['rnAllPage'],$aDataReport['rnCurrentPage']+2)); $i++):?>
                            <?php 
                                if($aDataReport['rnCurrentPage'] == $i){ 
                                    $tActive = 'active'; 
                                    $tDisPageNumber = 'disabled';
                                }else{ 
                                    $tActive = '';
                                    $tDisPageNumber = '';
                                }
                            ?>
                            <button onclick="JCNvReportClickPage('<?php echo $i?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
                        <?php endfor; ?>

                        <?php if($aDataReport['rnCurrentPage'] >= $aDataReport['rnAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
                        <button onclick="JCNvReportClickPage('next')" class="btn btn-white btn-sm" <?php echo $tDisabledRight ?>>
                            <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
                        </button>
                    </div> -->
                </div>
            </div>
        </div>
    </nav>    
    <div class="odvMainContent main">
        <div id="odvRvwMainMenu" class="main-menu clearfix">
            <div class="xCNMrgNavMenu">
                <div class="row xCNavRow">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <ol id="oliMenuNav" class="breadcrumb xCNBCMenu">
                            <!-- <li id="oliRptTitle"><?php echo language('report/report/report','tRptViewer') ?></li> -->
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="xCNMenuCump xCNRvwBrowseLine" id="odvMenuCump">
        </div>
        <div class="main-content">
            <div id="odvContentPageRptViewer" class="panel panel-headline"> 
                <div class="panel-body">
              <table width="100%" border="0">
                <tr valign="top">
                        <td width="100%" align="center" colspan=2><b style="font-size:25px;">
                           <br> <li id="oliRptTitle"><?php echo language('report/report/report','tRptTitleSaleRecive') ?></b><br>
                           <?php if($aDataFilter['tDocDateFrom'] !== '' && $aDataFilter['tDocDateFrom'] != '') : ?>
                           <?php echo language('report/report/report','tRptDateFrom') ?> <?php echo $aDataFilter['tDocDateFrom'] ?> <?php echo language('report/report/report','tRptDateTo') ?> <?php echo $aDataFilter['tDocDateFrom'] ?> 
                            <?php endif;?> 
                        </td>
                </tr>
                <tr valign="top">
                    <td width="50%" align="left">
                        ??????????????????: <?php echo $aCompData['raItems']['rtCmpName']; ?><br>
                        <?php echo $aAddress['FTAddV1No']." ".$aAddress['FTAddV1Soi']." ".$aAddress['FTSudName'] ;?><br>
                        <?php echo $aAddress['FTDstName']." ".$aAddress['FTPvnName']." ".$aAddress['FTAddV1PostCode']?><br>
                        <?php echo language('report/report/report','tRptTel') ?>. : <?php echo $aCompData['raItems']['rtCmpTel']; ?><br>
                        <?php echo language('report/report/report','tRptFaxNo') ?>. : <?php echo $aCompData['raItems']['rtCmpFax'];  ?><br>
                        <?php echo language('report/report/report','tRptBarchName') ?>. : <?php echo $aCompData['raItems']['rtCmpBchName'];; ?>
                    </td>
        
                 <tr>
                    <td width="100%" colspan=2 align="right"><?php echo language('report/report/report','tRptDatePrint') ?> <?php echo date("d/m/Y"); ?> <?php echo language('report/report/report','tRptTimePrint') ?> <?php echo date("H:i:s"); ?></td>
                </tr> 
            </table>             

            <div id="odvContentPageRptViewer" class="panel panel-headline"> 
        <table class="table">
        <thead>
        <tr>
            <th nowrap class="text-center xCNTextBold" style="width:5%; padding: 15px;"><?php echo  language('report/report/report','tRptPayby');?></th>
            <th nowrap class="text-center xCNTextBold" style="width:15%; padding: 10px;"><?php echo  language('report/report/report','tRptRcvDocumentCode');?></th>
            <th nowrap class="text-center xCNTextBold" style="width:15%; padding: 10px;"><?php echo  language('report/report/report','tRptDate');?></th>
            <th nowrap class="text-center xCNTextBold" style="width:5%; padding: 10px;"><?php  echo  language('report/report/report','tRptRcvTotal');?></th>
        </tr>
        </thead>

        <?php if(!empty($DataSource['aRptData'])){?>
        <?php

        $nSumQty = 0;
        $nSumDis = 0;
        $nSumNet = 0;

        $nSumFooterQty = 0;
        $nSumFooterDis = 0;
        $nSumFooterNet = 0;


        foreach ($DataSource['aRptData'] as $key=>$value) { ?>

                <?php
         
                    // Step 1 ?????????????????? Parameter ??????????????????????????? Groupping
                    $tRcvName   = $value["FTRcvName"];  
                    // $tDocDate = $value["FTShdDocDate"]; 

                    $nGroupMember = $value["FNRptGroupMember"]; 
                    $nRowPartID   = $value["FNRowPartID"]; 
                ?>

                <?php
                    //Step 2 Groupping data
                    // $aGrouppingData = array($tDocNo,$tDocDate);
                    $aGrouppingData = array($tRcvName);
                    // Parameter
                    //$nRowPartID = ???????????????????????????????????????
                    //$aGrouppingData = ???????????????????????????????????? Groupping
                    FCNtHRPTHeadGroupping($nRowPartID,$aGrouppingData);
                ?>


            <!-- Step 2 ???????????????????????????????????? TD -->
                <tr>
                    <td></td>
                    <td nowrap class="text-left" style="padding:7px;"><?php echo $value["FTXshDocNo"];?></td>
                    <td nowrap class="text-center"><?php echo $value["FDXrcRefDate"];?></td>
                    <td nowrap class="number text-right" style="padding:7px;"><?php echo number_format($value["FCXrcNet"],2)?></td>
                </tr>

                    <?php
                //Step 3 : ?????????????????? Parameter ?????????????????? Summary SubFooter

                $nSumNet = $value["FCSdtSubQty"];
                $aSumFooter = array('?????????'.$value["FTRcvName"],'N','N',number_format($nSumNet,2));
                

                //Step 4 : ???????????? Summary SubFooter

                //Parameter 
                //$nGroupMember = ???????????????????????????????????????????????????????????????????????????
                //$nRowPartID = ??????????????????????????????????????????????????????
                //$aSumFooter =  ?????????????????? Summary SubFooter

                $nStaNewGroup = FCNtHRPTSumSubFooter($nGroupMember,$nRowPartID,$aSumFooter);


                //Step 5 ?????????????????? Parameter ?????????????????? SumFooter
                //$nSumFooterQty = number_format($value["FCSdtQtyFooter"]);
                //$nSumFooterDis = number_format($value["FCSdtDisFooter"],2);
                $nSumFooterNet = number_format($value["FCXrcNetFooter"],2);
                $paFooterSumData = array('?????????????????????????????????','N','N',$nSumFooterNet);
                        ?>
                    <?php } // End for ?>
                    <?php 
                $nPageNo = $DataSource["aPagination"]["nDisplayPage"];
                $nTotalPage = $DataSource["aPagination"]["nTotalPage"];
                    
                //Step 6 : ???????????? Summary Footer
                FCNtHRPTSumFooter($nPageNo,$nTotalPage,$paFooterSumData);

                ?>
                </table>
                </div>
                <!-- ?????????????????????????????? Footer -->
                    <div class="row">
                        <div class="col-6">
                            ????????????????????????    <?php echo $DataSource["aPagination"]["nTotalRecord"];?> ?????????????????? 
                            ????????????????????????    <?php echo $DataSource["aPagination"]["nDisplayPage"];?>  /  
                                        <?php echo $DataSource["aPagination"]["nTotalPage"];?>
                        </div>
                        <div class="col-6">
                            <?php 
                                $nCurrentPage = $DataSource["aPagination"]["nDisplayPage"]; 
                                for($p = 1;$p <= $DataSource["aPagination"]["nTotalPage"];$p++){
                            ?>
                            <a href="<?=base_url("index.php/GroupingData/$p")?>"> [<?=$p?>] </a> 
                            <?php } ?>
                        </div>
                    </div>
                <!-- ???????????????????????????????????? Footer -->
                <?php } else {?>
                    <tr>
                    <td  class="text-center" colspan="4">?????????????????????????????????</td>
                    </tr>
                <?php } ?>    
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay Data Viewer -->
    <div class="xCNOverlayLodingData" style="z-index: 7000;">
        <img src="<?php echo base_url();?>application/modules/common/assets/images/ada.loading.gif" class="xWImgLoading">
        <div id="odvOverLayContentForLongTimeLoading" style="display: none;"><?php echo language('common/main/main', 'tLodingDataReport'); ?></div>
    </div>
    

    <script type="text/javascript">
        // Function Click Page
        function JCNvReportClickPage(ptPage){
            var nPageCurrent = '';
            switch (ptPage) {
                case 'next': //?????????????????? Next
                    // $('.xWBtnNext').addClass('disabled');
                    nPageOld = $('.xWPageReport .active').text(); // Get ?????????????????????????????????
                    nPageNew = parseInt(nPageOld, 10) + 1; // +1 ???????????????
                    nPageCurrent = nPageNew
                    break;
                case 'previous': //?????????????????? Previous
                    nPageOld = $('.xWPageReport .active').text(); // Get ?????????????????????????????????
                    nPageNew = parseInt(nPageOld, 10) - 1; // -1 ???????????????
                    nPageCurrent = nPageNew
                    break;
                default:
                    nPageCurrent = ptPage
            }
            JCNvCallDataReportPageClick(nPageCurrent);
        }

        // Function Call Data Rpt
        function JCNvCallDataReportPageClick(pnPageCurrent){
            JCNxOpenLoadingData();
            var tRptRote = $('#ohdRptRoute').val();
            $('#ohdRptCurrentPage').val(pnPageCurrent);
            $('#ofmRptSubmitClickPage').attr('action',tRptRote+'ClickPage');
            $('#ofmRptSubmitClickPage').submit();
            $('#ofmRptSubmitClickPage').attr('action','javascript:void(0)');
            JCNxCloseLoadingData();
        }
    </script>
</body>





