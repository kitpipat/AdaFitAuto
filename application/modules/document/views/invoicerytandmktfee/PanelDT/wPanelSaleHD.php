<?php 
    if(isset($aDataSumSalHD) && !empty($aDataSumSalHD) && $aDataSumSalHD['rtCode'] == '1'){
        $cTRMTotal      = (!empty($aDataSumSalHD['rtResult']['FCTRMTotal']))?       $aDataSumSalHD['rtResult']['FCTRMTotal']    : 0;
        $cTRMDisChg     = (!empty($aDataSumSalHD['rtResult']['FCTRMDisChg']))?      $aDataSumSalHD['rtResult']['FCTRMDisChg']   : 0;
        $cTRMAFDisChg   = (!empty($aDataSumSalHD['rtResult']['FCTRMAFDisChg']))?    $aDataSumSalHD['rtResult']['FCTRMAFDisChg'] : 0;
        $cTRMAmtV       = (!empty($aDataSumSalHD['rtResult']['FCTRMAmtV']))?        $aDataSumSalHD['rtResult']['FCTRMAmtV']     : 0;
        $cTRMAmtVtbl    = (!empty($aDataSumSalHD['rtResult']['FCTRMAmtVTbl']))?     $aDataSumSalHD['rtResult']['FCTRMAmtVTbl']  : 0;
        $cTRMGrand      = (!empty($aDataSumSalHD['rtResult']['FCTRMGrand']))?       $aDataSumSalHD['rtResult']['FCTRMGrand']    : 0;
    }else{
        $cTRMTotal      = 0;
        $cTRMDisChg     = 0;
        $cTRMAFDisChg   = 0;
        $cTRMAmtV       = 0;
        $cTRMAmtVtbl    = 0;
        $cTRMGrand      = 0;
    }
?>
<label class="xCNLabelFrm"><?php echo language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTitleDetailSale'); ?></label>
<div class="table-responsive">
    <table class="table table-striped" style="width:100%">
        <thead>
            <th nowrap class="xCNTextBold" style="width:80%;text-align:center;"><?php echo language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMLabelSaleHDList'); ?></th>
            <th nowrap class="xCNTextBold" style="width:20%;text-align:center;"><?php echo language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMLabelSaleHDAmount'); ?></th>
        </thead>
        <tbody>
            <tr class="text-left xCNTextDetail2" id="otbTRMTotal">
                <td class="text-left  xWTRMDataSaleLab"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTBDTotal'); ?></td>
                <td class="text-right xWTRMDataSaleVal"><?= number_format(@$cTRMTotal,$nOptDecimalShow)?></td>
                <input type="hidden" id="oetTRMTBDTotal"    name="oetTRMTBDTotal" value="<?=@$cTRMTotal?>">
            </tr>
            <tr class="text-left xCNTextDetail2" id="otbTRMDisChg">
                <td class="text-left  xWTRMDataSaleLab"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTBDDisChg'); ?></td>
                <td class="text-right xWTRMDataSaleVal"><?= number_format(@$cTRMDisChg,$nOptDecimalShow)?></td>
                <input type="hidden" id="oetTRMTBDDisChg"   name="oetTRMTBDDisChg" value="<?=@$cTRMDisChg?>">
            </tr>
            <tr class="text-left xCNTextDetail2" id="otbTRMAFDisChg">
                <td class="text-left  xWTRMDataSaleLab"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTBB4DisChg'); ?></td>
                <td class="text-right xWTRMDataSaleVal"><?= number_format(@$cTRMAFDisChg,$nOptDecimalShow)?></td>
                <input type="hidden" id="oetTRMTBAFDisChg"  name="oetTRMTBAFDisChg" value="<?=@$cTRMAFDisChg?>">
            </tr>
            <tr class="text-left xCNTextDetail2" id="otbTRMAmtV">
                <td class="text-left  xWTRMDataSaleLab"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTBAmtV'); ?></td>
                <td class="text-right xWTRMDataSaleVal"><?= number_format(@$cTRMAmtV,$nOptDecimalShow)?></td>
                <input type="hidden" id="oetTRMTBAmtV"      name="oetTRMTBAmtV" value="<?=@$cTRMAmtV?>">
            </tr>
            <tr class="text-left xCNTextDetail2" id="otbTRMGrand">
                <td class="text-left  xWTRMDataSaleLab"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTBGrand'); ?></td>
                <td class="text-right xWTRMDataSaleVal"><?= number_format(@$cTRMAmtVtbl,$nOptDecimalShow)?></td>
                <input type="hidden" id="oetTRMTBAmtVTbl"   name="oetTRMTBAmtVTbl"  value="<?=@$cTRMAmtVtbl?>">
                <input type="hidden" id="oetTRMTBGrand"     name="oetTRMTBGrand"    value="<?=@$cTRMGrand?>">
            </tr>
        </tbody>
    </table>
</div>