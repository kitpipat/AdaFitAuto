
<label class="xCNLabelFrm"><?php echo language('document/invoicerytandmktfee/invoicerytandmktfee', 'tTRMTitleDetailVat'); ?></label>
<div class="table-responsive">
    <table class="table table-striped" style="width:100%">
        <thead>
            <th nowrap class="xCNTextBold" style="width:80%;text-align:center;"><?php echo language('document/invoicerytandmktfee/invoicerytandmktfee', 'ภาษีมูลค่าเพิ่ม'); ?></th>
            <th nowrap class="xCNTextBold" style="width:20%;text-align:center;"><?php echo language('document/invoicerytandmktfee/invoicerytandmktfee', 'ยอดภาษี'); ?></th>
        </thead>
        <tbody>
            <?php if(!empty($aDataSumVatSalHD) && $aDataSumVatSalHD['rtCode'] == '1'): ?>
                <?php foreach($aDataSumVatSalHD['rtResult'] AS $nKey => $aValue): ?>
                    <tr>
                        <input type="hidden" id="ohdXsdVatRate<?=$nKey;?>" name="ohdXsdVatRate<?=$nKey;?>" value="<?=@$aValue['FCXsdVatRate'];?>">
                        <td class="text-left"><?=number_format(@$aValue['FCXsdVatRate'],0).' % ';?></td>
                        <td class="text-right"><?=number_format(@$aValue['FCXshGrand'],$nOptDecimalShow);?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td class="text-center xCNTextDetail2 xCNTextNotfoundDataPdtTable" colspan="100%"><?= language('common/main/main', 'tCMNNotFoundData') ?></td></tr>
            <?php endif;?>
        </tbody>
    </table>
</div>