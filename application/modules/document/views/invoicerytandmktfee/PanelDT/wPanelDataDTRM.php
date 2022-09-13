<?php 
    // Data Config Cst Royoty Fee And Marketting Fee
    if(isset($aConfigRytMktFee) && !empty($aConfigRytMktFee)){
        $tCshShipTo         = @$aConfigRytMktFee['FTCshShipTo'];
        $tCshSoldTo         = @$aConfigRytMktFee['FTCshSoldTo'];
        $tCshPaymentTerm    = @$aConfigRytMktFee['FTCshPaymentTerm'];
    }else{
        $tCshShipTo         = "";
        $tCshSoldTo         = "";
        $tCshPaymentTerm    = "";
    }

    if(isset($aDataDTFoot) && !empty($aDataDTFoot)){
        // Dat Footer DT RM
        $tVatRateRM     = ' ( '.number_format(@$aDataDTFoot['FCVatRateRM'],0).'% )';
        $cXphTotalRM    = @$aDataDTFoot['FCXphTotalRM'];
        $cXphVatRM      = @$aDataDTFoot['FCXphVatRM'];
        $cXphVatableRM  = @$aDataDTFoot['FCXphVatableRM'];
        $cXphGrandRM    = @$aDataDTFoot['FCXphGrandRM'];
    }else{
        $tVatRateRM     = 0;
        $cXphTotalRM    = 0;
        $cXphVatRM      = 0;
        $cXphVatableRM  = 0;
        $cXphGrandRM    = 0;
    }
?>
<input type="hidden"    id="ohdTRMCshShipTo"        name="ohdTRMCshShipTo"      value="<?=@$tCshShipTo;?>">
<input type="hidden"    id="ohdTRMCshSoldTo"        name="ohdTRMCshSoldTo"      value="<?=@$tCshSoldTo;?>">
<input type="hidden"    id="ohdTRMCshPaymentTerm"   name="ohdTRMCshPaymentTerm" value="<?=@$tCshPaymentTerm;?>">
<input type="hidden"    id="ohdTRMCshChkGrandRM"    name="ohdTRMCshChkGrandRM"  value="<?=floatval(@$cXphGrandRM);?>">
<div class="row">
    <div class="col-xs-12 col-md-12 col-lg-12">
        <label class="xCNLabelFrm"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMDTTitleLabel'); ?></label>
        <div class="table-responsive">
            <table class="table table-striped" style="width:100%">
                <thead>
                    <th nowrap class="xCNTextBold" style="width:50%;text-align:center;"><?php echo language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMDTList'); ?></th>
                    <th nowrap class="xCNTextBold" style="width:20%;text-align:center;"><?php echo language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMDTSaleValNV'); ?></th>
                    <th nowrap class="xCNTextBold" style="width:20%;text-align:center;"><?php echo language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMDTChgRate').' (%)'; ?></th>
                    <th nowrap class="xCNTextBold" style="width:10%;text-align:center;"><?php echo language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMDTAmount'); ?></th>
                </thead>
                <tbody>
                    <?php if(!empty($aDataDTTmp) && $aDataDTTmp['rtCode'] == '1'): ?>
                        <?php foreach($aDataDTTmp['rtResult'] AS $nKey => $aVal): ?>
                            <?php
                                if($aVal['FNXpdSeqNo'] == '1'){
                                    // Royolty Fee
                                    $tTextLabelDTRM = language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMRytFreeVal').' '.$aVal['FNXpdDesc'];
                                }else{
                                    // Marketting Fee
                                    $tTextLabelDTRM = language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMMktFreeVal').' '.$aVal['FNXpdDesc'];
                                }
                            ?>
                            <tr class="text-left xCNTextDetail2">   
                                <td class="text-left"><?=$tTextLabelDTRM;?></td>
                                <td class="text-right"><?=number_format($aVal['FCXpdTotalNV'],$nOptDecimalShow);?></td>
                                <td class="text-right"><?=number_format($aVal['FNXpdPercentRate'],$nOptDecimalShow).' %';?></td>
                                <td class="text-right"><?=number_format($aVal['FCXpdTotal'],$nOptDecimalShow);?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="text-left xCNTextDetail2">
                            <td class="text-left"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMMktFreeVal'); ?></td>
                            <td class="text-right"><?= number_format(0,$nOptDecimalShow)?></td>
                            <td class="text-right"><?= number_format(0,0)?></td>
                            <td class="text-right"><?= number_format(0,$nOptDecimalShow)?></td>
                        </tr>
                        <tr class="text-left xCNTextDetail2">
                            <td class="text-left"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMRytFreeVal'); ?></td>
                            <td class="text-right"><?= number_format(0,$nOptDecimalShow)?></td>
                            <td class="text-right"><?= number_format(0,0)?></td>
                            <td class="text-right"><?= number_format(0,$nOptDecimalShow)?></td>
                        </tr>
                    <?php endif; ?>

                    <tr class="text-left xCNTextDetail2" id="otbTRMTotalRM">
                        <td class="text-left" colspan="3"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTotalRM'); ?></td>
                        <td class="text-right"><b><?= number_format(@$cXphTotalRM,$nOptDecimalShow)?></b></td>
                    </tr>
                    <tr class="text-left xCNTextDetail2" id="otbTRMTotalVat">
                        <td class="text-left" colspan="3"><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMVatRM').''.@$tVatRateRM; ?></td>
                        <td class="text-right"><?= number_format(@$cXphVatRM,$nOptDecimalShow)?></td>
                    </tr>
                    <tr class="text-left xCNTextDetail2" id="otbTRMTotalVat">
                        <td class="text-left" colspan="3"><b><?= language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMGrandRM'); ?><b></td>
                        <td class="text-right"><b><?= number_format(@$cXphGrandRM,$nOptDecimalShow)?></b></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>