<style>
    #odvTAXRowDataEndOfBill .panel-heading{
        padding-top: 10px !important;
        padding-bottom: 10px !important;
    }
    #odvTAXRowDataEndOfBill .panel-body{
        padding-top: 0px !important;
        padding-bottom: 0px !important;
    }
    #odvTAXRowDataEndOfBill .list-group-item {
        padding-left: 0px !important;
        padding-right: 0px !important;
        border: 0px Solid #ddd;
    }
    .mark-font, .panel-default > .panel-heading.mark-font{
        color: #232C3D !important;
        font-weight: 900;
    }
</style>

<div class="row p-t-10" id="odvTAXRowDataEndOfBill" >
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <ul class="list-group">
                    <?php 
                        if($aGetHD['rtCode'] == 1){

                            if($aGetHD['raItems'][0]['FCXshTotal'] == '' || $aGetHD['raItems'][0]['FCXshTotal'] == null){
                                $nFCXshTotal = 0;
                            }else{
                                $nFCXshTotal = $aGetHD['raItems'][0]['FCXshTotal'];
                            }

                            if($aGetHD['raItems'][0]['FCXshDis'] == '' || $aGetHD['raItems'][0]['FCXshDis'] == null){
                                $nFCXshDis = 0;
                            }else{
                                $nFCXshDis = $aGetHD['raItems'][0]['FCXshDis'];
                            }

                            if($aGetHD['raItems'][0]['FCXshChg'] == '' || $aGetHD['raItems'][0]['FCXshChg'] == null){
                                $nFCXshChg = 0;
                            }else{
                                $nFCXshChg = $aGetHD['raItems'][0]['FCXshChg'];
                            }

                            if($aGetHD['raItems'][0]['FCXshVat'] == '' || $aGetHD['raItems'][0]['FCXshVat'] == null){
                                $nFCXshVat = 0;
                            }else{
                                $nFCXshVat = $aGetHD['raItems'][0]['FCXshVat'];
                            }

                            if($aGetHD['raItems'][0]['FCXshGrand'] == '' || $aGetHD['raItems'][0]['FCXshGrand'] == null){
                                $nFCXshGrand = 0;
                            }else{
                                $nFCXshGrand = $aGetHD['raItems'][0]['FCXshGrand'];
                            }

                            if($aGetHD['raItems'][0]['FCXshRnd'] == '' || $aGetHD['raItems'][0]['FCXshRnd'] == null){
                                $nFCXshRnd = 0;
                            }else{
                                $nFCXshRnd = $aGetHD['raItems'][0]['FCXshRnd'];
                            }

                            if($aGetHD['raItems'][0]['FCXshVatable'] == '' || $aGetHD['raItems'][0]['FCXshVatable'] == null){
                                $nFCXshVatable = 0;
                            }else{
                                $nFCXshVatable = $aGetHD['raItems'][0]['FCXshVatable'];
                            }

                            if($aGetHD['raItems'][0]['FCXshAmtNV'] == '' || $aGetHD['raItems'][0]['FCXshAmtNV'] == null){
                                $nFCXshAmtNV = 0;
                            }else{
                                $nFCXshAmtNV = $aGetHD['raItems'][0]['FCXshAmtNV'];
                            }

                            // if($aGetHD['raItems'][0]['FTXshGndText'] == '' || $aGetHD['raItems'][0]['FTXshGndText'] == null){
                            //     $tGndText       = 'บาท';
                            // }else{
                                // $tGndText       = FCNtNumberToTextBaht($nFCXshGrand);
                            // }

                            //มีข้อมูล
                            $FCXshTotal     = number_format($nFCXshTotal,2);
                            $FCXshDis       = number_format($nFCXshDis + $nFCXshChg,2);
                            $nB4            = number_format($nFCXshTotal - ($nFCXshDis - $nFCXshChg),2);
                            // $FCXshVat       = number_format($nFCXshVat,2);
                            $FCXshVat = ($nFCXshGrand - $nFCXshRnd) - ROUND($nFCXshVatable,2) - $nFCXshAmtNV;
                            $FCXshGrand     = number_format($nFCXshGrand,2);
                            $tGndText       = FCNtNumberToTextBaht($FCXshGrand);
                        }else{
                            //ไม่มีข้อมูล
                            $FCXshTotal     = '0.00';
                            $FCXshDis       = '0.00';
                            $nB4            = '0.00';
                            $FCXshVat       = '0.00';
                            $FCXshGrand     = '0.00';
                            $tGndText       = 'บาท';
                        }
                    ?>
                    <li class="list-group-item">
                        <label class="pull-left mark-font"><?=language('document/taxinvoice/taxinvoice','tTAXTBSumFCXtdNet');?></label>
                        <label class="pull-right mark-font" id="olbTAXSumFCXtdNet"><?=$FCXshTotal?></label>
                        <div class="clearfix"></div>
                    </li>
                    <li class="list-group-item">
                        <label class="pull-left"><?=language('document/taxinvoice/taxinvoice','tTAXTBDisChg');?></label>
                        <label class="pull-left" style="margin-left: 5px;" id="olbTAXDisChgHD"></label>
                        <label class="pull-right" id="olbTAXSumFCXtdAmt"><?=$FCXshDis?></label>
                        <div class="clearfix"></div>
                    </li>
                    <li class="list-group-item">
                        <label class="pull-left"><?=language('document/taxinvoice/taxinvoice','tTAXTBSumFCXtdNetAfHD');?></label>
                        <label class="pull-right" id="olbTAXSumFCXtdNetAfHD"><?=$nB4?></label>
                        <div class="clearfix"></div>
                    </li>
                    <li class="list-group-item">
                        <label class="pull-left"><?=language('document/taxinvoice/taxinvoice','tTAXTBSumFCXtdVat');?></label>
                        <label class="pull-right" id="olbTAXSumFCXtdVat"><?=$FCXshVat?></label>
                        <div class="clearfix"></div>
                    </li>
                </ul>
            </div>
            <div class="panel-heading">
                <label class="pull-left mark-font"><?=language('document/taxinvoice/taxinvoice','tTAXTBFCXphGrand');?></label>
                <label class="pull-right mark-font" id="olbTAXCalFCXphGrand"><?=$FCXshGrand?></label>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<script>
    var tGndText = '<?=$tGndText?>';
    $('#olbGrandText').text(tGndText);
</script>
