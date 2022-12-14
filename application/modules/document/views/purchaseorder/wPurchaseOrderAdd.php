<?php
$tSesUsrLevel   = $this->session->userdata('tSesUsrLevel');
if (isset($aDataDocHD) && $aDataDocHD['rtCode'] == '1') {
    // print_r($aDataDocHD['raItems']);
    $aDataDocHD             = @$aDataDocHD['raItems'];
    $aDataDocHDSpl          = @$aDataDocHDSpl['raItems'];

    $tPORoute               = "docPOEventEdit";
    $nPOAutStaEdit          = 1;
    $tPODocNo               = $aDataDocHD['FTXphDocNo'];
    $dPODocDate             = date("Y-m-d", strtotime($aDataDocHD['FDXphDocDate']));
    $dPODocTime             = date("H:i", strtotime($aDataDocHD['FDXphDocDate']));
    $tPOCreateBy            = $aDataDocHD['FTCreateBy'];
    $tPOUsrNameCreateBy     = $aDataDocHD['FTUsrName'];

    $tPOStaRefund           = $aDataDocHD['FTXphStaRefund'];
    $tPOStaDoc              = $aDataDocHD['FTXphStaDoc'];
    $tPOStaApv              = $aDataDocHD['FTXphStaApv'];
    $tPOStaPrcStk           = '';
    $tPOStaDelMQ            = '';
    $tPOStaPaid             = $aDataDocHD['FTXphStaPaid'];

    $tPOSesUsrBchCode       = $this->session->userdata("tSesUsrBchCodeDefault");
    $tPODptCode             = $aDataDocHD['FTDptCode'];
    $tPOUsrCode             = $this->session->userdata('tSesUsername');
    $tPOLangEdit            = $this->session->userdata("tLangEdit");

    $tPOApvCode             = $aDataDocHD['FTXphApvCode'];
    $tPOUsrNameApv          = $aDataDocHD['FTXphApvName'];
    $tPORefPoDoc            = "";
    $tPORefIntDoc           = $aDataDocHD['FTXphRefInt'];
    $dPORefIntDocDate       = $aDataDocHD['FDXphRefIntDate'];
    $tPORefExtDoc           = $aDataDocHD['FTXphRefExt'];
    $dPORefExtDocDate       = $aDataDocHD['FDXphRefExtDate'];
    $nPOStaRef              = $aDataDocHD['FNXphStaRef'];

    $tPOBchCode             = $aDataDocHD['FTBchCode'];
    $tPOBchName             = $aDataDocHD['FTBchName'];
    $tPOUserBchCode         = $tUserBchCode;
    $tPOUserBchName         = $tUserBchName;
    $tPOBchCompCode         = $tBchCompCode;
    $tPOBchCompName         = $tBchCompName;

    $tPOphBchCodeTo         = $aDataDocHD['FTXphBchTo'];
    $tPOphBchNameTo         = $aDataDocHD['FTBchNameTo'];

    $tPOMerCode             = $aDataDocHD['FTMerCode'];
    $tPOMerName             = $aDataDocHD['FTMerName'];
    $tPOShopType            = $aDataDocHD['FTShpType'];
    $tPOShopCode            = $aDataDocHD['FTShpCode'];
    $tPOShopName            = $aDataDocHD['FTShpName'];

    $tPOWahCode             = $aDataDocHD['FTWahCode'];
    $tPOWahName             = $aDataDocHD['FTWahName'];
    $nPOStaDocAct           = $aDataDocHD['FNXphStaDocAct'];
    $tPOFrmDocPrint         = $aDataDocHD['FNXphDocPrint'];
    $tPOFrmRmk              = $aDataDocHD['FTXphRmk'];
    $tPOSplCode             = $aDataDocHD['FTSplCode'];
    $tPOSplName             = $aDataDocHD['FTSplName'];
    $tPOSplEmail            = $aDataDocHD['FTSplEmail'];

    $tPOCmpRteCode          = $aDataDocHD['FTRteCode'];
    $cPORteFac              = $aDataDocHD['FCXphRteFac'];

    $tPOVatInOrEx           = $aDataDocHD['FTXphVATInOrEx'];
    $tPOSplPayMentType      = $aDataDocHD['FTXphCshOrCrd'];

    // ???????????????????????????????????????????????? Supplier
    $tPOSplDstPaid          = $aDataDocHDSpl['FTXphDstPaid'];
    $tPOSplCrTerm           = $aDataDocHDSpl['FNXphCrTerm'];
    $dPOSplDueDate          = $aDataDocHDSpl['FDXphDueDate'];
    $dPOSplBillDue          = $aDataDocHDSpl['FDXphBillDue'];
    $tPOSplCtrName          = $aDataDocHDSpl['FTXphCtrName'];
    $dPOSplTnfDate          = $aDataDocHDSpl['FDXphTnfDate'];
    $tPOSplRefTnfID         = $aDataDocHDSpl['FTXphRefTnfID'];
    $tPOSplRefVehID         = $aDataDocHDSpl['FTXphRefVehID'];
    $tPOSplRefInvNo         = $aDataDocHDSpl['FTXphRefInvNo'];
    $tPOSplQtyAndTypeUnit   = $aDataDocHDSpl['FTXphQtyAndTypeUnit'];

    // ??????????????????????????????????????????????????????????????????
    // $tPOSplShipAdd          = $aDataDocHDSpl['FNXphShipAdd'];
    // $tPOSplShipAdd          = (isset($aDataDocHDSpl['FTAddShipName']) && !empty($aDataDocHDSpl['FTAddShipName']))? $aDataDocHDSpl['FTAddShipName'] : "";
    // $tPOShipAddName         = (isset($aDataDocHDSpl['FTAddShipName']) && !empty($aDataDocHDSpl['FTAddShipName']))? $aDataDocHDSpl['FTAddShipName'] : "-";
    // $tPOShipAddAddV1No      = (isset($aDataDocHDSpl['FTXphShipAddNo']) && !empty($aDataDocHDSpl['FTXphShipAddNo']))? $aDataDocHDSpl['FTXphShipAddNo'] : "-";
    // $tPOShipAddV1Soi        = (isset($aDataDocHDSpl['FTXphShipAddPoi']) && !empty($aDataDocHDSpl['FTXphShipAddPoi']))? $aDataDocHDSpl['FTXphShipAddPoi'] : "-";
    // $tPOShipAddV1Village    = (isset($aDataDocHDSpl['FTXphShipAddVillage']) && !empty($aDataDocHDSpl['FTXphShipAddVillage']))? $aDataDocHDSpl['FTXphShipAddVillage'] : "-";
    // $tPOShipAddV1Road       = (isset($aDataDocHDSpl['FTXphShipAddRoad']) && !empty($aDataDocHDSpl['FTXphShipAddRoad']))? $aDataDocHDSpl['FTXphShipAddRoad'] : "-";
    // $tPOShipAddV1SubDist    = (isset($aDataDocHDSpl['FTXphShipSubDistrict']) && !empty($aDataDocHDSpl['FTXphShipSubDistrict']))? $aDataDocHDSpl['FTXphShipSubDistrict'] : "-";
    // $tPOShipAddV1DstCode    = (isset($aDataDocHDSpl['FTXphShipDistrict']) && !empty($aDataDocHDSpl['FTXphShipDistrict']))? $aDataDocHDSpl['FTXphShipDistrict'] : "-";
    // $tPOShipAddV1PvnCode    = (isset($aDataDocHDSpl['FTXphShipProvince']) && !empty($aDataDocHDSpl['FTXphShipProvince']))? $aDataDocHDSpl['FTXphShipProvince'] : "-";
    // $tPOShipAddV1PostCode   = (isset($aDataDocHDSpl['FTXphShipPosCode']) && !empty($aDataDocHDSpl['FTXphShipPosCode']))? $aDataDocHDSpl['FTXphShipPosCode'] : "-";
    // $tPOShipAddV1Tel        = (isset($aDataDocHDSpl['FTXphShipTel']) && !empty($aDataDocHDSpl['FTXphShipTel']))? $aDataDocHDSpl['FTXphShipTel'] : "-";
    // $tPOShipAddV1Fax        = (isset($aDataDocHDSpl['FTXphShipFax']) && !empty($aDataDocHDSpl['FTXphShipFax']))? $aDataDocHDSpl['FTXphShipFax'] : "-";
    // $tPOShipTax             = (isset($aDataDocHDSpl['FTXphShipTaxNo']) && !empty($aDataDocHDSpl['FTXphShipTaxNo']))? $aDataDocHDSpl['FTXphShipTaxNo'] : "-";

    // ??????????????????????????????????????????????????????????????????????????????????????????
    // $tPOSplTaxAdd           = $aDataDocHDSpl['FNXphTaxAdd'];
    // $tPOSplTaxAdd           = (isset($aDataDocHDSpl['FNXphTaxAdd']) && !empty($aDataDocHDSpl['FNXphTaxAdd']))? $aDataDocHDSpl['FNXphTaxAdd'] : "";
    // $tPOTexAddName          = (isset($aDataDocHDSpl['FTAddTaxName']) && !empty($aDataDocHDSpl['FTAddTaxName']))? $aDataDocHDSpl['FTAddTaxName'] : "-";
    // $tPOTexAddAddV1No       = (isset($aDataDocHDSpl['FTXphTaxAddNo']) && !empty($aDataDocHDSpl['FTXphTaxAddNo']))? $aDataDocHDSpl['FTXphTaxAddNo'] : "-";
    // $tPOTexAddV1Soi         = (isset($aDataDocHDSpl['FTXphTaxAddPoi']) && !empty($aDataDocHDSpl['FTXphTaxAddPoi']))? $aDataDocHDSpl['FTXphTaxAddPoi'] : "-";
    // $tPOTexAddV1Village     = (isset($aDataDocHDSpl['FTXphTaxAddVillage']) && !empty($aDataDocHDSpl['FTXphTaxAddVillage']))? $aDataDocHDSpl['FTXphTaxAddVillage'] : "-";
    // $tPOTexAddV1Road        = (isset($aDataDocHDSpl['FTXphTaxAddRoad']) && !empty($aDataDocHDSpl['FTXphTaxAddRoad']))? $aDataDocHDSpl['FTXphTaxAddRoad'] : "-";
    // $tPOTexAddV1SubDist     = (isset($aDataDocHDSpl['FTXphTaxSubDistrict']) && !empty($aDataDocHDSpl['FTXphTaxSubDistrict']))? $aDataDocHDSpl['FTXphTaxSubDistrict'] : "-";
    // $tPOTexAddV1DstCode     = (isset($aDataDocHDSpl['FTXphTaxDistrict']) && !empty($aDataDocHDSpl['FTXphTaxDistrict']))? $aDataDocHDSpl['FTXphTaxDistrict'] : "-";
    // $tPOTexAddV1PvnCode     = (isset($aDataDocHDSpl['FTXphTaxProvince']) && !empty($aDataDocHDSpl['FTXphTaxProvince']))? $aDataDocHDSpl['FTXphTaxProvince'] : "-";
    // $tPOTexAddV1PostCode    = (isset($aDataDocHDSpl['FTXphTaxPosCode']) && !empty($aDataDocHDSpl['FTXphTaxPosCode']))? $aDataDocHDSpl['FTXphTaxPosCode'] : "-";
    // $tPOTexAddV1Tel         = (isset($aDataDocHDSpl['FTXphTaxTel']) && !empty($aDataDocHDSpl['FTXphTaxTel']))? $aDataDocHDSpl['FTXphTaxTel'] : "-";
    // $tPOTexAddV1Fax         = (isset($aDataDocHDSpl['FTXphTaxFax']) && !empty($aDataDocHDSpl['FTXphTaxFax']))? $aDataDocHDSpl['FTXphTaxFax'] : "-";
    // $tPOTexAddV1Tax         = (isset($aDataDocHDSpl['FTXphTaxAddTAX']) && !empty($aDataDocHDSpl['FTXphTaxAddTAX']))? $aDataDocHDSpl['FTXphTaxAddTAX'] : "-";

    //???????????????????????????????????????
    $tSHIP_FNAddSeqNo        = @$aDataDocHDSpl['FNXphShipAdd'];
    $tSHIP_FTAddV1No         = @$aDataDocHDSpl['FTXphShipAddNo'];
    $tSHIP_FTAddV1Soi        = @$aDataDocHDSpl['FTXphShipAddSoi'];
    $tSHIP_FTAddV1Village    = @$aDataDocHDSpl['FTXphShipAddVillage'];
    $tSHIP_FTAddV1Road       = @$aDataDocHDSpl['FTXphShipAddRoad'];
    $tSHIP_FTSudName         = @$aDataDocHDSpl['FTXphShipSubDistrict'];
    $tSHIP_FTDstName         = @$aDataDocHDSpl['FTXphShipDistrict'];
    $tSHIP_FTPvnName         = @$aDataDocHDSpl['FTXphShipProvince'];
    $tSHIP_FTAddV1PostCode   = @$aDataDocHDSpl['FTXphShipPostCode'];
    $tSHIP_FTAddTel          = @$aDataDocHDSpl['FTXphShipTel'];
    $tSHIP_FTAddFax          = @$aDataDocHDSpl['FTXphShipFax'];
    $tSHIP_FTAddTaxNo        = @$aDataDocHDSpl['FTXphShipAddTaxNo'];
    $tSHIP_FTAddV2Desc1      = @$aDataDocHDSpl['FTXphShipAddV2Desc1'];
    $tSHIP_FTAddV2Desc2      = @$aDataDocHDSpl['FTXphShipAddV2Desc2'];
    $tSHIP_FTAddName         = @$aDataDocHDSpl['FTXphShipAddName'];

    //???????????????????????????????????????????????????????????????
    $tTAX_FNAddSeqNo        = @$aDataDocHDSpl['FNXphTaxAdd'];
    $tTAX_FTAddV1No         = @$aDataDocHDSpl['FTXphTaxAddNo'];
    $tTAX_FTAddV1Soi        = @$aDataDocHDSpl['FTXphTaxAddSoi'];
    $tTAX_FTAddV1Village    = @$aDataDocHDSpl['FTXphTaxAddVillage'];
    $tTAX_FTAddV1Road       = @$aDataDocHDSpl['FTXphTaxAddRoad'];
    $tTAX_FTSudName         = @$aDataDocHDSpl['FTXphTaxSubDistrict'];
    $tTAX_FTDstName         = @$aDataDocHDSpl['FTXphTaxDistrict'];
    $tTAX_FTPvnName         = @$aDataDocHDSpl['FTXphTaxProvince'];
    $tTAX_FTAddV1PostCode   = @$aDataDocHDSpl['FTXphTaxPostCode'];
    $tTAX_FTAddTel          = @$aDataDocHDSpl['FTXphTaxTel'];
    $tTAX_FTAddFax          = @$aDataDocHDSpl['FTXphTaxFax'];
    $tTAX_FTAddTaxNo        = @$aDataDocHDSpl['FTXphTaxAddTaxNo'];
    $tTAX_FTAddV2Desc1      = @$aDataDocHDSpl['FTXphTaxAddV2Desc1'];
    $tTAX_FTAddV2Desc2      = @$aDataDocHDSpl['FTXphTaxAddV2Desc2'];
    $tTAX_FTAddName         = @$aDataDocHDSpl['FTXphTaxAddName'];

    $tPOVatCodeBySPL        = $aDetailSPL['FTVatCode'];
    $tPOVatRateBySPL        = $aDetailSPL['FCXpdVatRate'];

    $tPOAgnCode        = $aDataDocHD['FTAgnCode'];
    $tPOAgnName        = $aDataDocHD['FTAgnName'];

    $nStaUploadFile        = 2;
} else {
    $tPORoute               = "docPOEventAdd";
    $nPOAutStaEdit          = 0;
    $tPODocNo               = "";
    $dPODocDate             = "";
    $dPODocTime             = date('H:i:s');
    $tPOCreateBy            = $this->session->userdata('tSesUsrUsername');
    $tPOUsrNameCreateBy     = $this->session->userdata('tSesUsrUsername');
    $nPOStaRef              = 0;
    $tPOStaRefund           = 1;
    $tPOStaDoc              = 1;
    $tPOStaApv              = NULL;
    $tPOStaPrcStk           = NULL;
    $tPOStaDelMQ            = NULL;
    $tPOStaPaid             = 1;

    $tPOSesUsrBchCode       = $this->session->userdata("tSesUsrBchCodeDefault");
    $tPODptCode             = $tDptCode;
    $tPOUsrCode             = $this->session->userdata('tSesUsername');
    $tPOLangEdit            = $this->session->userdata("tLangEdit");

    $tPOApvCode             = "";
    $tPOUsrNameApv          = "";
    $tPORefPoDoc            = "";
    $tPORefIntDoc           = "";
    $dPORefIntDocDate       = "";
    $tPORefExtDoc           = "";
    $dPORefExtDocDate       = "";


    $tPOBchCode             = $tBchCode;
    $tPOBchName             = $tBchName;
    $tPOUserBchCode         = $tBchCode;
    $tPOUserBchName         = $tBchName;
    $tPOBchCompCode         = $tBchCompCode;
    $tPOBchCompName         = $tBchCompName;
    $tPOMerCode             = $tMerCode;
    $tPOMerName             = $tMerName;
    $tPOShopType            = $tShopType;
    $tPOShopCode            = $tShopCode;
    $tPOShopName            = $tShopName;

    $tPOWahCode             = "";
    $tPOWahName             = "";
    $nPOStaDocAct           = "";
    $tPOFrmDocPrint         = 0;
    $tPOFrmRmk              = "";
    $tPOSplCode             = "";
    $tPOSplName             = "";
    $tPOSplEmail            = "";


    $tPOCmpRteCode          = $tCmpRteCode;
    $cPORteFac              = $cXthRteFac;

    $tPOVatInOrEx           = $tCmpRetInOrEx;
    $tPOSplPayMentType      = "1";

    // ???????????????????????????????????????????????? Supplier
    $tPOSplDstPaid          = "1";
    $tPOSplCrTerm           = "";
    $dPOSplDueDate          = "";
    $dPOSplBillDue          = "";
    $tPOSplCtrName          = "";
    $dPOSplTnfDate          = "";
    $tPOSplRefTnfID         = "";
    $tPOSplRefVehID         = "";
    $tPOSplRefInvNo         = "";
    $tPOSplQtyAndTypeUnit   = "";

    //???????????????????????????????????????
    $tSHIP_FNAddSeqNo        = '';
    $tSHIP_FTAddV1No         = '';
    $tSHIP_FTAddV1Soi        = '';
    $tSHIP_FTAddV1Village    = '';
    $tSHIP_FTAddV1Road       = '';
    $tSHIP_FTSudName         = '';
    $tSHIP_FTDstName         = '';
    $tSHIP_FTPvnName         = '';
    $tSHIP_FTAddV1PostCode   = '';
    $tSHIP_FTAddTel          = '';
    $tSHIP_FTAddFax          = '';
    $tSHIP_FTAddTaxNo        = '';
    $tSHIP_FTAddV2Desc1      = '';
    $tSHIP_FTAddV2Desc2      = '';
    $tSHIP_FTAddName         = '';

    //???????????????????????????????????????????????????????????????
    $tTAX_FNAddSeqNo        = '';
    $tTAX_FTAddV1No         = '';
    $tTAX_FTAddV1Soi        = '';
    $tTAX_FTAddV1Village    = '';
    $tTAX_FTAddV1Road       = '';
    $tTAX_FTSudName         = '';
    $tTAX_FTDstName         = '';
    $tTAX_FTPvnName         = '';
    $tTAX_FTAddV1PostCode   = '';
    $tTAX_FTAddTel          = '';
    $tTAX_FTAddFax          = '';
    $tTAX_FTAddTaxNo        = '';
    $tTAX_FTAddV2Desc1      = '';
    $tTAX_FTAddV2Desc2      = '';
    $tTAX_FTAddName         = '';
    $tPOAgnCode        = $this->session->userdata('tSesUsrAgnCode');
    $tPOAgnName        = $this->session->userdata('tSesUsrAgnName');

    // // ??????????????????????????????????????????????????????????????????
    // $tPOSplShipAdd          = "";
    // $tPOShipAddName          = "";
    // $tPOShipAddAddV1No      = "-";
    // $tPOShipAddV1Soi        = "-";
    // $tPOShipAddV1Village    = "-";
    // $tPOShipAddV1Road       = "-";
    // $tPOShipAddV1SubDist    = "-";
    // $tPOShipAddV1DstCode    = "-";
    // $tPOShipAddV1PvnCode    = "-";
    // $tPOShipAddV1PostCode   = "-";
    // $tPOShipAddV1Tel        = "-";
    // $tPOShipAddV1Fax        = "-";
    // $tPOShipTax        = "-";

    // // ??????????????????????????????????????????????????????????????????????????????????????????
    // $tPOSplTaxAdd           = "";
    // $tPOTexAddName          = "";
    // $tPOTexAddAddV1No       = "-";
    // $tPOTexAddV1Soi         = "-";
    // $tPOTexAddV1Village     = "-";
    // $tPOTexAddV1Road        = "-";
    // $tPOTexAddV1SubDist     = "-";
    // $tPOTexAddV1DstCode     = "-";
    // $tPOTexAddV1PvnCode     = "-";
    // $tPOTexAddV1PostCode    = "-";
    // $tPOTexAddV1Tel        = "-";
    // $tPOTexAddV1Fax        = "-";
    // $tPOTexAddV1Tax        = "-";

    // $tPOStaAlwPosCalSo   = "1";
    $tPOVatCodeBySPL        = "";
    $tPOVatRateBySPL        = "";


    $tPOphBchCodeTo         = $this->session->userdata('tSesUsrBchCodeDefault');
    $tPOphBchNameTo         = $this->session->userdata('tSesUsrBchNameDefault');
    $nStaUploadFile        = 1;

    $aSPLConfig              = $aSPLConfig;

    if ($this->session->userdata('bIsHaveAgn') == true && $this->session->userdata('tAgnType') == 2) { //????????????????????????
        $tPOSplCode         = $aSPLConfig['rtSPLCode'];
        $tPOSplName         = $aSPLConfig['rtSPLName'];
        $tPOSplDisabled     = "Disabled";
        $tPOVatInOrEx       = $aSPLConfig['FTSplStaVATInOrEx'];
        if($aSPLConfig['FCSplCrLimit'] > 0){
            $tPOSplPayMentType       = '2';
        }
        $tPOSplDstPaid      = $aSPLConfig['FTSplTspPaid'];
        $tPOSplCrTerm       = $aSPLConfig['FNSplCrTerm'];
    } else { //????????????????????????????????????
        $tPOSplCode         = "";
        $tPOSplName         = "";
        $tPOSplDisabled     = "";
    }
}
if (empty($tPOBchCode) && empty($tPOShopCode)) {
    $tASTUserType   = "HQ";
    $tASTUserTypeBro   = "HQ";
} else {
    $tASTUserTypeBro   = "BCH";
    if (!empty($tPOBchCode) && empty($tPOShopCode)) {
        $tASTUserType   = "BCH";
    } else if (!empty($tPOBchCode) && !empty($tPOShopCode)) {
        $tASTUserType   = "SHP";
    } else {
        $tASTUserType   = "";
    }
}
if ($tPOStaDoc == 1) {
    $tPOReadOnly   = "readonly";
}
?>
<style>
    .xQtyFontColorPanelTooter {
        margin-top: 6px;
        margin-bottom: 6px;
        color: #fff;
    }

    #odvRowDataEndOfBill .panel-heading {
        padding-top: 10px !important;
        padding-bottom: 10px !important;
    }

    #odvRowDataEndOfBill .panel-body {
        padding-top: 0px !important;
        padding-bottom: 0px !important;
    }

    #odvRowDataEndOfBill .list-group-item {
        padding-left: 0px !important;
        padding-right: 0px !important;
        border: 0px solid #ddd;
    }

    .mark-font,
    .panel-default>.panel-heading.mark-font {
        color: #232C3D !important;
        font-weight: 900;
    }
</style>
<form id="ofmPOFormAdd" class="validate-form" action="javascript:void(0)" method="post" enctype="multipart/form-data">
    <input type="hidden" id="ohdPORefIntDocOld" name="ohdPORefIntDocOld" value="<?php echo $tPORefIntDoc ?>">
    <input type="hidden" id="ohdPOPage" name="ohdPOPage" value="1">
    <input type="hidden" id="ohdPOStaImport" name="ohdPOStaImport" value="0">
    <input type="hidden" id="ohdPORoute" name="ohdPORoute" value="<?php echo $tPORoute; ?>">
    <input type="hidden" id="ohdPOCheckClearValidate" name="ohdPOCheckClearValidate" value="0">
    <input type="hidden" id="ohdPOCheckSubmitByButton" name="ohdPOCheckSubmitByButton" value="0">
    <input type="hidden" id="ohdPOAutStaEdit" name="ohdPOAutStaEdit" value="<?php echo $nPOAutStaEdit; ?>">
    <input type="hidden" id="ohdPOODecimalShow" name="ohdPOODecimalShow" value="<?= $nOptDecimalShow ?>">
    <input type="hidden" id="ohdPOStaRefund" name="ohdPOStaRefund" value="<?php echo $tPOStaRefund; ?>">
    <input type="hidden" id="ohdPOStaDoc" name="ohdPOStaDoc" value="<?php echo $tPOStaDoc; ?>">
    <input type="hidden" id="ohdPOStaApv" name="ohdPOStaApv" value="<?php echo $tPOStaApv; ?>">
    <input type="hidden" id="ohdPOStaDelMQ" name="ohdPOStaDelMQ" value="<?php echo $tPOStaDelMQ; ?>">
    <input type="hidden" id="ohdPOStaPrcStk" name="ohdPOStaPrcStk" value="<?php echo $tPOStaPrcStk; ?>">
    <input type="hidden" id="ohdPOStaRef" name="ohdPOStaRef" value="<?php echo $nPOStaRef; ?>">

    <input type="hidden" id="ohdPOStaPaid" name="ohdPOStaPaid" value="<?php echo $tPOStaPaid; ?>">
    <input type="hidden" id="ohdPOSesUsrBchCode" name="ohdPOSesUsrBchCode" value="<?php echo $tPOSesUsrBchCode; ?>">
    <input type="hidden" id="ohdPOBchCode" name="ohdPOBchCode" value="<?php echo $tPOBchCode; ?>">
    <input type="hidden" id="ohdPODptCode" name="ohdPODptCode" value="<?php echo $tPODptCode; ?>">
    <input type="hidden" id="ohdPOUsrCode" name="ohdPOUsrCode" value="<?php echo $tPOUsrCode ?>">
    <input type="hidden" id="ohdPOCmpRteCode" name="ohdPOCmpRteCode" value="<?php echo $tPOCmpRteCode; ?>">
    <input type="hidden" id="ohdPORteFac" name="ohdPORteFac" value="<?php echo $cPORteFac; ?>">
    <input type="hidden" id="ohdPOApvCodeUsrLogin" name="ohdPOApvCodeUsrLogin" value="<?php echo $tPOUsrCode; ?>">
    <input type="hidden" id="ohdPOLangEdit" name="ohdPOLangEdit" value="<?php echo $tPOLangEdit; ?>">
    <input type="hidden" id="ohdPOOptAlwSaveQty" name="ohdPOOptAlwSaveQty" value="<?php echo $nOptDocSave ?>">
    <input type="hidden" id="ohdPOOptScanSku" name="ohdPOOptScanSku" value="<?php echo $nOptScanSku ?>">
    <input type="hidden" id="ohdPOVatRate" name="ohdPOVatRate" value="<?= $cVatRate ?>">
    <input type="hidden" id="ohdPOCmpRetInOrEx" name="ohdPOCmpRetInOrEx" value="<?= $tCmpRetInOrEx ?>">
    <input type="hidden" id="ohdSesSessionID" name="ohdSesSessionID" value="<?= $this->session->userdata('tSesSessionID') ?>">
    <input type="hidden" id="ohdSesUsrLevel" name="ohdSesUsrLevel" value="<?= $this->session->userdata('tSesUsrLevel') ?>">
    <input type="hidden" id="ohdSesUsrBchCom" name="ohdSesUsrBchCom" value="<?= $this->session->userdata('tSesUsrBchCom') ?>">
    <input type="hidden" id="ohdPOValidatePdt" name="ohdPOValidatePdt" value="<?= language('document/purchaseorder/purchaseorder', 'tPOPleaseSeletedPDTIntoTable') ?>">
    <input type="hidden" id="ohdPOSubmitWithImp" name="ohdPOSubmitWithImp" value="0">
    <input type="hidden" id="ohdPOValidatePdtImp" name="ohdPOValidatePdtImp" value="<?= language('document/purchaseorder/purchaseorder', 'tPONotFoundPdtCodeAndBarcodeImpList') ?>">
    <input type="hidden" id="ohdPOApvOrSave" name="ohdPOApvOrSave" value="">
    <input type="hidden" id="ohdPOSplEmail" name="ohdPOSplEmail" value="<?php echo $tPOSplEmail; ?>">
    <button style="display:none" type="submit" id="obtPOSubmitDocument" onclick="JSxPOAddEditDocument()"></button>

    <div class="row">
        <!--???????????????????????????????????????????????????????????????-->
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">

            <!-- Panel ???????????????????????????????????????????????????????????????????????? -->
            <div class="panel panel-default" style="margin-bottom: 25px;">
                <div id="odvPOHeadStatusInfo" class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
                    <label class="xCNTextDetail1"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmStatus'); ?></label>
                    <a class="xCNMenuplus" role="button" data-toggle="collapse" href="#odvPODataStatusInfo" aria-expanded="true">
                        <i class="fa fa-plus xCNPlus"></i>
                    </a>
                </div>
                <div id="odvPODataStatusInfo" class="xCNMenuPanelData panel-collapse collapse in" role="tabpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group xCNHide" style="text-align: right;">
                                    <label class="text-success xCNTitleFrom"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmAppove'); ?></label>
                                </div>
                                <label class="xCNLabelFrm"><span style="color:red">*</span><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelAutoGenCode'); ?></label>
                                <?php if (isset($tPODocNo) && empty($tPODocNo)) : ?>
                                    <div class="form-group">
                                        <label class="fancy-checkbox">
                                            <input type="checkbox" id="ocbPOStaAutoGenCode" name="ocbPOStaAutoGenCode" maxlength="1" checked="checked">
                                            <span>&nbsp;</span>
                                            <span class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmAutoGenCode'); ?></span>
                                        </label>
                                    </div>
                                <?php endif; ?>
                                <!-- ??????????????????????????????????????? -->
                                <div class="form-group" style="cursor:not-allowed">
                                    <input type="text" class="form-control xCNGenarateCodeTextInputValidate xCNInputWithoutSpcNotThai" id="oetPODocNo" name="oetPODocNo" maxlength="20" value="<?php echo $tPODocNo; ?>" data-validate-required="<?php echo language('document/purchaseorder/purchaseorder', 'tPOPlsEnterOrRunDocNo'); ?>" data-validate-duplicate="<?php echo language('document/purchaseorder/purchaseorder', 'tPOPlsDocNoDuplicate'); ?>" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmDocNo'); ?>" style="pointer-events:none" readonly>
                                    <input type="hidden" id="ohdPOCheckDuplicateCode" name="ohdPOCheckDuplicateCode" value="2">
                                </div>
                                <!-- ???????????????????????????????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmDocDate'); ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control xCNDatePicker xCNInputMaskDate" id="oetPODocDate" name="oetPODocDate" value="<?php echo $dPODocDate; ?>" data-validate-required="<?php echo language('document/purchaseorder/purchaseorder', 'tPOPlsEnterDocDate'); ?>">
                                        <span class="input-group-btn">
                                            <button id="obtPODocDate" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                                        </span>
                                    </div>
                                </div>
                                <!-- ?????????????????????????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmDocTime'); ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control xCNTimePicker xCNInputMaskTime" id="oetPODocTime" name="oetPODocTime" value="<?php echo $dPODocTime; ?>" data-validate-required="<?php echo language('document/purchaseorder/purchaseorder', 'tPOPlsEnterDocTime'); ?>">
                                        <span class="input-group-btn">
                                            <button id="obtPODocTime" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                                        </span>
                                    </div>
                                </div>
                                <!-- ?????????????????????????????????????????? -->
                                <div class="form-group" style="margin:0">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                            <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmCreateBy'); ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                                            <input type="hidden" id="ohdPOCreateBy" name="ohdPOCreateBy" value="<?php echo $tPOCreateBy ?>">
                                            <label><?php echo $tPOUsrNameCreateBy ?></label>
                                        </div>
                                    </div>
                                </div>
                                <!-- ????????????????????????????????? -->
                                <div class="form-group" style="margin:0">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                            <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmStaDoc'); ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                                            <?php
                                            if ($tPORoute == "docPOEventAdd") {
                                                $tPOLabelStaDoc  = language('document/purchaseorder/purchaseorder', 'tPOLabelFrmValStaDoc');
                                            } else {
                                                $tPOLabelStaDoc  = language('document/purchaseorder/purchaseorder', 'tPOLabelFrmValStaDoc' . $tPOStaDoc);
                                            }
                                            ?>
                                            <label><?php echo $tPOLabelStaDoc; ?></label>
                                        </div>
                                    </div>
                                </div>

                                <!-- ?????????????????????????????????????????????????????? -->
                                <div class="form-group" style="margin:0">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                            <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmStaApv'); ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                                            <label><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmValStaApv' . $tPOStaApv); ?></label>
                                        </div>
                                    </div>
                                </div>
                                <!-- ????????????????????????????????????????????????????????? -->
                                <!-- <div class="form-group" style="margin:0">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                            <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmStaPrcStk'); ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                                            <label><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmValStaPrcStk' . $tPOStaPrcStk); ?></label>
                                        </div>
                                    </div>
                                </div> -->
                                <!-- ?????????????????????????????????????????????????????? -->
                                <div class="form-group" style="margin:0">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                            <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmStaRef'); ?></label>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">

                                            <label><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmStaRef' . $nPOStaRef); ?></label>

                                        </div>
                                    </div>
                                </div>

                                <?php if (isset($tPODocNo) && !empty($tPODocNo)) : ?>
                                    <!-- ???????????????????????????????????????????????? -->
                                    <div class="form-group" style="margin:0">
                                        <div class="row">
                                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                                <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmApvBy'); ?></label>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                                                <input type="hidden" id="ohdPOApvCode" name="ohdPOApvCode" maxlength="20" value="<?php echo $tPOApvCode ?>">
                                                <label>
                                                    <?php echo (isset($tPOUsrNameApv) && !empty($tPOUsrNameApv)) ? $tPOUsrNameApv : "-" ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel ?????????????????????????????? -->
            <div class="panel panel-default" style="margin-bottom: 25px;">
                <div id="odvPOReferenceDoc" class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
                    <label class="xCNTextDetail1"><?php echo language('document/purchaseorder/purchaseorder', 'tPOPanelBranchFrom'); ?></label>
                    <a class="xCNMenuplus collapsed" role="button" data-toggle="collapse" href="#odvPODataReferenceDoc" aria-expanded="true">
                        <i class="fa fa-plus xCNPlus"></i>
                    </a>
                </div>
                <?php
                if ($tPORoute  == "docPOEventAdd") {
                    $tDisabledAgn = '';
                } else {
                    $tDisabledAgn = 'disabled';
                }
                ?>
                <div id="odvPODataReferenceDoc" class="xCNMenuPanelData panel-collapse collapse" role="tabpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-t-10">
                                <!-- Condition ??????????????????????????? -->
                                <div class="form-group ">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOPanelAgency'); ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control xCNHide" id="oetPOAgnCodeFrm" name="oetPOAgnCodeFrm" maxlength="5" value="<?php echo $tPOAgnCode; ?>">
                                        <input type="text" class="form-control xWPointerEventNone" id="oetPOAgnNameFrm" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOPanelAgency'); ?>" name="oetPOAgnNameFrm" lavudate-label="<?php echo language('document/purchaseorder/purchaseorder', 'tPOPanelAgency'); ?>" value="<?php echo $tPOAgnName; ?>" readonly>
                                        <span class="xWConditionSearchPdt input-group-btn">
                                            <button id="obtPOBrowseAgencyFrm" type="button" class=" btn xCNBtnBrowseAddOn" <?php echo $tDisabledAgn; ?>>
                                                <img class="xCNIconFind">
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <!-- Condition ???????????? -->
                                <div class="form-group m-b-0">
                                    <?php
                                    $tPODataInputBchCode   = "";
                                    $tPODataInputBchName   = "";
                                    $tPODataInputBchCode2   = "";
                                    $tPODataInputBchName2   = "";
                                    if ($tPORoute  == "docPOEventAdd") {
                                        $tPODataInputBchCode    = $this->session->userdata('tSesUsrBchCodeDefault');
                                        $tPODataInputBchName    = $this->session->userdata('tSesUsrBchNameDefault');
                                        $tPODataInputBchCode2    = $this->session->userdata('tSesUsrBchCodeDefault');
                                        $tPODataInputBchName2    = $this->session->userdata('tSesUsrBchNameDefault');
                                        $tDisabledBch = '';
                                    } else {
                                        $tPODataInputBchCode    = $tPOBchCode;
                                        $tPODataInputBchName    = $tPOBchName;
                                        $tPODataInputBchCode2    = $tPOphBchCodeTo;
                                        $tPODataInputBchName2    = $tPOphBchNameTo;
                                        $tDisabledBch = 'disabled';
                                    }

                                    ?>
                                    <!--????????????-->
                                    <script>
                                        var tUsrLevel = '<?= $this->session->userdata('tSesUsrLevel') ?>';
                                        if (tUsrLevel != "HQ") {
                                            //BCH - SHP
                                            var tBchCount = '<?= $this->session->userdata("nSesUsrBchCount"); ?>';
                                            if (tBchCount < 2) {
                                                $('#obtPOBrowseBCHFrm').attr('disabled', true);
                                            }
                                        }
                                    </script>
                                    <div class="form-group">
                                        <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmBranch') ?></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control xCNHide xCNInputWithoutSpcNotThai xCNInputWithoutSingleQuote" id="oetPOFrmBchCode" name="oetPOFrmBchCode" maxlength="5" value="<?php echo @$tPODataInputBchCode ?>" data-bchcodeold="<?php echo @$tPODataInputBchCode ?>">
                                            <input type="text" class="form-control xWPointerEventNone" id="oetPOFrmBchName" name="oetPOFrmBchName" maxlength="100" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmBranch') ?>" value="<?php echo @$tPODataInputBchName ?>" readonly>
                                            <span class="input-group-btn xWConditionSearchPdt">
                                                <button id="obtPOBrowseBCHFrm" type="button" class="btn xCNBtnBrowseAddOn " <?= $tDisabledBch ?>>
                                                    <img src="<?php echo  base_url() . '/application/modules/common/assets/images/icons/find-24.png' ?>">
                                                </button>
                                            </span>
                                        </div>
                                    </div>

                                </div>

                                <!-- ???????????????????????????????????????????????????????????????????????? -->
                                <!-- <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmRefIntDoc') ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="oetPORefIntDoc" name="oetPORefIntDoc" maxlength="20" value="<?php echo $tPORefIntDoc; ?>" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmRefIntDoc') ?>" readonly>
                                        <span class="input-group-btn xWConditionSearchPdt">
                                            <button id="obtPOBrowseRefIntDoc" type="button" class="btn xCNBtnBrowseAddOn xWConditionSearchPdt">
                                                <img src="<?php echo  base_url() . '/application/modules/common/assets/images/icons/find-24.png' ?>">
                                            </button>
                                        </span>
                                    </div>
                                </div> -->

                                <!-- ?????????????????????????????????????????????????????????????????????????????????????????? -->
                                <!-- <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmRefIntDocDate'); ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control xCNDatePicker xCNInputMaskDate xWConditionSearchPdt" id="oetPORefIntDocDate" name="oetPORefIntDocDate" placeholder="YYYY-MM-DD" value="<?php echo $dPORefIntDocDate; ?>">
                                        <span class="input-group-btn">
                                            <button id="obtPOBrowseRefIntDocDate" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                                        </span>
                                    </div>
                                </div> -->
                                <!-- ??????????????????????????????????????????????????????????????????????????? -->
                                <!-- <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmRefExtDoc'); ?></label>
                                    <input type="text" class="form-control xWConditionSearchPdt" id="oetPORefExtDoc" name="oetPORefExtDoc" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmRefExtDoc'); ?>" value="<?php echo $tPORefExtDoc; ?>">
                                </div> -->
                                <!-- ?????????????????????????????????????????????????????? -->
                                <!-- <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmRefExtDocDate'); ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control xCNDatePicker xCNInputMaskDate xWConditionSearchPdt" id="oetPORefExtDocDate" name="oetPORefExtDocDate" placeholder="YYYY-MM-DD" value="<?php echo $dPORefExtDocDate; ?>">
                                        <span class="input-group-btn">
                                            <button id="obtPOBrowseRefExtDocDate" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                                        </span>
                                    </div>
                                </div> -->

                                <div id="" class="row">
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"></div>
                                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                        <input type="hidden" id="ohdPOFrmTaxAdd" name="ohdPOFrmTaxAdd" value="<?= @$tPOSplTaxAdd ?>">

                                        <input type="hidden" id="ohdPOTaxAddSeqNo" name="ohdPOTaxAddSeqNo" value="<?= @$tTAX_FNAddSeqNo ?>">
                                        <input type="hidden" id="ohdPOTaxAddTaxNo" name="ohdPOTaxAddTaxNo" value="<?= @$tTAX_FTAddTaxNo ?>">
                                        <input type="hidden" id="ohdPOTaxAddName" name="ohdPOTaxAddName" value="<?= @$tTAX_FTAddName ?>">
                                        <input type="hidden" id="ohdPOTaxTel" name="ohdPOTaxTel" value="<?= @$tTAX_FTAddTel ?>">
                                        <input type="hidden" id="ohdPOTaxFax" name="ohdPOTaxFax" value="<?= @$tTAX_FTAddFax ?>">

                                        <!-- Addr Version 1 -->
                                        <input type="hidden" id="ohdPOTaxAddV1No" name="ohdPOTaxAddV1No" value="<?= @$tTAX_FTAddV1No ?>">
                                        <input type="hidden" id="ohdPOTaxV1Soi" name="ohdPOTaxV1Soi" value="<?= @$tTAX_FTAddV1Soi ?>">
                                        <input type="hidden" id="ohdPOTaxV1Village" name="ohdPOTaxV1Village" value="<?= @$tTAX_FTAddV1Village ?>">
                                        <input type="hidden" id="ohdPOTaxV1Road" name="ohdPOTaxV1Road" value="<?= @$tTAX_FTAddV1Road ?>">
                                        <input type="hidden" id="ohdPOTaxV1SubDistrict" name="ohdPOTaxV1SubDistrict" value="<?= @$tTAX_FTSudName ?>">
                                        <input type="hidden" id="ohdPOTaxV1District" name="ohdPOTaxV1District" value="<?= @$tTAX_FTDstName ?>">
                                        <input type="hidden" id="ohdPOTaxV1Province" name="ohdPOTaxV1Province" value="<?= @$tTAX_FTPvnName ?>">
                                        <input type="hidden" id="ohdPOTaxV1PostCode" name="ohdPOTaxV1PostCode" value="<?= @$tTAX_FTAddV1PostCode ?>">

                                        <!-- Addr Version 2 -->
                                        <input type="hidden" id="ohdPOTaxAddV2Desc1" name="ohdPOTaxAddV2Desc1" value="<?= @$tTAX_FTAddV2Desc1 ?>">
                                        <input type="hidden" id="ohdPOTaxAddV2Desc2" name="ohdPOTaxAddV2Desc2" value="<?= @$tTAX_FTAddV2Desc2 ?>">

                                        <button type="button" id="obtPOFrmBrowseTaxAdd" class="btn xCNBTNSubSave" style="width:100%;" data-codebrowse="2">
                                            <?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoTaxAddress'); ?>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel ?????????????????????????????? -->
            <div class="panel panel-default" style="margin-bottom: 25px;">
                <div id="odvPISupplierInfo" class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
                    <label class="xCNTextDetail1"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoDoc'); ?></label>
                    <a class="xCNMenuplus collapsed" role="button" data-toggle="collapse" href="#odvPIDataSupplierInfo" aria-expanded="true">
                        <i class="fa fa-plus xCNPlus"></i>
                    </a>
                </div>
                <div id="odvPIDataSupplierInfo" class="xCNMenuPanelData panel-collapse collapse" role="tabpanel">
                    <div class="panel-body">
                        <div id="odvRowPanelSplInfo" class="row" style="max-height:350px;overflow-x:auto">
                            <div class="col-xs-12 col-sm-12 col-col-md-12 col-lg-12">
                                <!--?????????????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOTBSpl'); ?></label>

                                    <input type="text" class="form-control" id="oetPOFrmSplNameShow" name="oetPOFrmSplNameShow" value="<?php echo $tPOSplName; ?>" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOTBSpl'); ?>" readonly>
                                </div>
                                <!-- ?????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoVatInOrEx'); ?></label>
                                    <?php
                                    switch ($tPOVatInOrEx) {
                                        case '1':
                                            $tOptionVatIn   = "selected";
                                            $tOptionVatEx   = "";
                                            break;
                                        case '2':
                                            $tOptionVatIn   = "";
                                            $tOptionVatEx   = "selected";
                                            break;
                                        default:
                                            $tOptionVatIn   = "selected";
                                            $tOptionVatEx   = "";
                                    }
                                    ?>
                                    <select class="selectpicker form-control xWPIDisabledOnApv xWConditionSearchPdt" id="ocmPOFrmSplInfoVatInOrEx" name="ocmPOFrmSplInfoVatInOrEx" maxlength="1">
                                        <option value="1" <?php echo @$tOptionVatIn; ?>><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoVatInclusive'); ?></option>
                                        <option value="2" <?php echo @$tOptionVatEx; ?>><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoVatExclusive'); ?></option>
                                    </select>
                                </div>
                                <input type="hidden" id="ohdPOFrmSplVatRate" name="ohdPOFrmSplVatRate" value="<?= $tPOVatRateBySPL ?>">
                                <input type="hidden" id="ohdPOFrmSplVatCode" name="ohdPOFrmSplVatCode" value="<?= $tPOVatCodeBySPL ?>">

                                <!-- ??????????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoPaymentType'); ?></label>
                                    <select class="selectpicker form-control xWPIDisabledOnApv xWConditionSearchPdt" id="ocmPOFrmSplInfoPaymentType" name="ocmPOFrmSplInfoPaymentType" maxlength="1" value="<?php echo $tPOSplPayMentType; ?>">
                                        <option value="1" <?php if ($tPOSplPayMentType == '1') {
                                                                echo 'selected';
                                                            } ?>><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoPaymentType1'); ?></option>
                                        <option value="2" <?php if ($tPOSplPayMentType == '2') {
                                                                echo 'selected';
                                                            } ?>><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoPaymentType2'); ?></option>
                                    </select>
                                </div>
                                <!-- ????????????????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoDstPaid'); ?></label>
                                    <select class="selectpicker form-control xWPIDisabledOnApv xWConditionSearchPdt" id="ocmPOFrmSplInfoDstPaid" name="ocmPOFrmSplInfoDstPaid" maxlength="1" value="<?php echo $tPOSplDstPaid; ?>">
                                        <option value="1" <?php if ($tPOSplDstPaid == '1') {
                                                                echo 'selected';
                                                            } ?>><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoDstPaid1'); ?></option>
                                        <option value="2" <?php if ($tPOSplDstPaid == '2') {
                                                                echo 'selected';
                                                            } ?>><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoDstPaid2'); ?></option>
                                    </select>
                                </div>
                                <!-- ?????????????????????????????? -->
                                <div class="form-group xCNPanel_CreditTerm">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoCrTerm'); ?></label>
                                    <input type="text" class="form-control text-right xCNInputNumericWithoutDecimal xWPIDisabledOnApv xWConditionSearchPdt" id="oetPOFrmSplInfoCrTerm" name="oetPOFrmSplInfoCrTerm" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoCrTerm'); ?>" value="<?php echo $tPOSplCrTerm; ?>">
                                </div>
                                <!-- ????????????????????????????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoDueDate'); ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control xCNDatePicker xCNInputMaskDate xWConditionSearchPdt" id="oetPOFrmSplInfoDueDate" name="oetPOFrmSplInfoDueDate" placeholder="YYYY-MM-DD" value="<?php echo $dPOSplDueDate; ?>">
                                        <span class="input-group-btn">
                                            <button id="obtPOFrmSplInfoDueDate" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                                        </span>
                                    </div>
                                </div>
                                <!-- ??????????????????????????? -->
                                <!-- <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoBillDue'); ?></label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control xCNDatePicker xCNInputMaskDate xWConditionSearchPdt"
                                            id="oetPOFrmSplInfoBillDue"
                                            name="oetPOFrmSplInfoBillDue"
                                            placeholder="YYYY-MM-DD"
                                            value="<?php echo $dPOSplBillDue; ?>"
                                        >
                                        <span class="input-group-btn">
                                            <button id="obtPOFrmSplInfoBillDue" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                                        </span>
                                    </div>
                                </div> -->

                            </div>
                        </div>




                    </div>
                </div>
            </div>

            <!-- Panel ????????????????????????????????? -->
            <div class="panel panel-default" style="margin-bottom: 25px;">
                <div id="odvPIBranchToInfo" class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
                    <label class="xCNTextDetail1"><?php echo language('document/purchaseorder/purchaseorder', 'tPOPanelBranchTo'); ?></label>
                    <a class="xCNMenuplus collapsed" role="button" data-toggle="collapse" href="#odvPIDataBranchToInfo" aria-expanded="true">
                        <i class="fa fa-plus xCNPlus"></i>
                    </a>
                </div>
                <div id="odvPIDataBranchToInfo" class="xCNMenuPanelData panel-collapse collapse" role="tabpanel">
                    <div class="panel-body">
                        <div id="" class="row" style="max-height:350px;overflow-x:auto">
                            <div class="col-xs-12 col-sm-12 col-col-md-12 col-lg-12">

                                <!-- Condition ??????????????????????????? -->
                                <div class="form-group ">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOPanelAgency'); ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control xCNHide" id="oetPOAgnCodeTo" name="oetPOAgnCodeTo" maxlength="5" value="<?=$tPOAgnCode?>">
                                        <input type="text" class="form-control xWPointerEventNone" id="oetPOAgnNameTo" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOPanelAgency'); ?>" name="oetPOAgnNameTo" lavudate-label="<?php echo language('document/purchaseorder/purchaseorder', 'tPOPanelAgency'); ?>" value="<?=$tPOAgnName?>" readonly>
                                        <span class="xWConditionSearchPdt input-group-btn">
                                            <button id="obtPOBrowseAgencyTo" type="button" class="xWConditionSearchPdt btn xCNBtnBrowseAddOn">
                                                <img class="xCNIconFind">
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <!-- Condition ???????????? -->
                                <div class="form-group m-b-0">
                                    <div class="form-group">

                                        <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmBranch') ?></label>
                                        <?php if ($tASTUserTypeBro == "HQ") { ?>
                                            <div class="input-group">
                                                <input type="text" class="form-control xCNHide xCNInputWithoutSpcNotThai xCNInputWithoutSingleQuote" id="oetPOToBchCode" name="oetPOToBchCode" maxlength="5" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmBranch') ?>" value="<?= $tPODataInputBchCode2 ?>" data-bchcodeold="">
                                                <input type="text" class="form-control xWPointerEventNone" id="oetPOToBchName" name="oetPOToBchName" maxlength="100" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmBranch') ?>" value="<?= $tPODataInputBchName2 ?>" readonly>
                                                <span class="input-group-btn xWConditionSearchPdt">
                                                    <button id="obtPOBrowseBCHTo" type="button" class="btn xCNBtnBrowseAddOn xWConditionSearchPdt  ">
                                                        <img src="<?php echo  base_url() . '/application/modules/common/assets/images/icons/find-24.png' ?>">
                                                    </button>
                                                </span>
                                            </div>
                                        <?php } else { ?>
                                            <input type="text" class="form-control xCNHide xCNInputWithoutSpcNotThai xCNInputWithoutSingleQuote" id="oetPOToBchCode" name="oetPOToBchCode" maxlength="5" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmBranch') ?>" value="<?= $tPODataInputBchCode2 ?>" data-bchcodeold="">
                                            <input type="text" class="form-control xWPointerEventNone" id="oetPOToBchName" name="oetPOToBchName" maxlength="100" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmBranch') ?>" value="<?= $tPODataInputBchName2 ?>" readonly>
                                        <?php } ?>
                                    </div>

                                </div>




                            </div>
                        </div>


                        <div class="row">
                            <div class="col-xs-12 col-sm-3 col-md-6 col-lg-6"></div>
                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <input type="hidden" id="ohdPOFrmShipAdd" name="ohdPOFrmShipAdd" value="<?= @$tSHIP_FNAddSeqNo ?>">

                                <input type="hidden" id="ohdPOShipAddSeqNo" name="ohdPOShipAddSeqNo" value="<?= @$tSHIP_FNAddSeqNo ?>">
                                <input type="hidden" id="ohdPOShipAddTaxNo" name="ohdPOShipAddTaxNo" value="<?= @$tSHIP_FTAddTaxNo ?>">
                                <input type="hidden" id="ohdPOShipAddName" name="ohdPOShipAddName" value="<?= @$tSHIP_FTAddName ?>">
                                <input type="hidden" id="ohdPOShipTel" name="ohdPOShipTel" value="<?= @$tSHIP_FTAddTel ?>">
                                <input type="hidden" id="ohdPOShipFax" name="ohdPOShipFax" value="<?= @$tSHIP_FTAddFax ?>">

                                <!-- Addr Version 1 -->
                                <input type="hidden" id="ohdPOShipAddV1No" name="ohdPOShipAddV1No" value="<?= @$tSHIP_FTAddV1No ?>">
                                <input type="hidden" id="ohdPOShipV1Soi" name="ohdPOShipV1Soi" value="<?= @$tSHIP_FTAddV1Soi ?>">
                                <input type="hidden" id="ohdPOShipV1Village" name="ohdPOShipV1Village" value="<?= @$tSHIP_FTAddV1Village ?>">
                                <input type="hidden" id="ohdPOShipV1Road" name="ohdPOShipV1Road" value="<?= @$tSHIP_FTAddV1Road ?>">
                                <input type="hidden" id="ohdPOShipV1SubDistrict" name="ohdPOShipV1SubDistrict" value="<?= @$tSHIP_FTSudName ?>">
                                <input type="hidden" id="ohdPOShipV1District" name="ohdPOShipV1District" value="<?= @$tSHIP_FTDstName ?>">
                                <input type="hidden" id="ohdPOShipV1Province" name="ohdPOShipV1Province" value="<?= @$tSHIP_FTPvnName ?>">
                                <input type="hidden" id="ohdPOShipV1PostCode" name="ohdPOShipV1PostCode" value="<?= @$tSHIP_FTAddV1PostCode ?>">

                                <!-- Addr Version 2 -->
                                <input type="hidden" id="ohdPOShipAddV2Desc1" name="ohdPOShipAddV2Desc1" value="<?= @$tSHIP_FTAddV2Desc1 ?>">
                                <input type="hidden" id="ohdPOShipAddV2Desc2" name="ohdPOShipAddV2Desc2" value="<?= @$tSHIP_FTAddV2Desc2 ?>">

                                <button type="button" id="obtPOFrmBrowseAddrAdd" class="btn xCNBTNSubSave" style="width:100%;" data-codebrowse="1">
                                    <?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoShipAddress'); ?>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Panel ??????????????? -->
            <div class="panel panel-default" style="margin-bottom: 25px;">
                <div id="odvPIDeliveryInfo" class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
                    <label class="xCNTextDetail1"><?php echo language('document/purchaseorder/purchaseorder', 'tPOPanelDelevery'); ?></label>
                    <a class="xCNMenuplus collapsed" role="button" data-toggle="collapse" href="#odvPIDataDeliveryInfo" aria-expanded="true">
                        <i class="fa fa-plus xCNPlus"></i>
                    </a>
                </div>
                <div id="odvPIDataDeliveryInfo" class="xCNMenuPanelData panel-collapse collapse" role="tabpanel">
                    <div class="panel-body">
                        <div id="" class="row" style="max-height:350px;overflow-x:auto">
                            <div class="col-xs-12 col-sm-12 col-col-md-12 col-lg-12">
                                <!-- ??????????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoCtrName'); ?></label>
                                    <input type="text" class="form-control xWPIDisabledOnApv xWConditionSearchPdt" id="oetPOFrmSplInfoCtrName" name="oetPOFrmSplInfoCtrName" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoCtrName'); ?>" value="<?php echo $tPOSplCtrName; ?>">
                                </div>
                                <!-- ????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoRefTnfID'); ?></label>
                                    <input type="text" class="form-control xWPIDisabledOnApv xWConditionSearchPdt" id="oetPOFrmSplInfoRefTnfID" name="oetPOFrmSplInfoRefTnfID" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoRefTnfID'); ?>" value="<?php echo $tPOSplRefTnfID; ?>">
                                </div>
                                <!-- ????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoTnfDate'); ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control xCNDatePicker xCNInputMaskDate xWConditionSearchPdt" id="oetPOFrmSplInfoTnfDate" name="oetPOFrmSplInfoTnfDate" placeholder="YYYY-MM-DD" value="<?php echo $dPOSplTnfDate; ?>">
                                        <span class="input-group-btn">
                                            <button id="obtPOFrmSplInfoTnfDate" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                                        </span>
                                    </div>
                                </div>
                                <!-- ?????????????????????????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoRefVehID'); ?></label>
                                    <input type="text" class="form-control xWPIDisabledOnApv xWConditionSearchPdt" id="oetPOFrmSplInfoRefVehID" name="oetPOFrmSplInfoRefVehID" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmSplInfoRefVehID'); ?>" value="<?php echo $tPOSplRefVehID; ?>">
                                </div>

                            </div>
                        </div>




                    </div>
                </div>
            </div>

            <!-- Panel ???????????? -->
            <div class="panel panel-default" style="margin-bottom: 25px;">
                <div id="odvPOInfoOther" class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
                    <label class="xCNTextDetail1"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmInfoOth'); ?></label>
                    <a class="xCNMenuplus collapsed" role="button" data-toggle="collapse" href="#odvPODataInfoOther" aria-expanded="true">
                        <i class="fa fa-plus xCNPlus"></i>
                    </a>
                </div>
                <div id="odvPODataInfoOther" class="xCNMenuPanelData panel-collapse collapse" role="tabpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-xs-12">
                                <!-- ????????????????????????????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="fancy-checkbox">
                                        <input type="checkbox" value="1" id="ocbPOFrmInfoOthStaDocAct" name="ocbPOFrmInfoOthStaDocAct" maxlength="1" <?php echo ($nPOStaDocAct == '1' || empty($nPOStaDocAct)) ? 'checked' : ''; ?>>
                                        <span>&nbsp;</span>
                                        <span class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmInfoOthStaDocAct'); ?></span>
                                    </label>
                                </div>
                                <?php
                                switch ($nPOStaRef) {
                                    case '1':
                                        $tOptionNoRef       = "";
                                        $tOptionSomeRef     = "selected";
                                        $tOptionAllRef      = "";
                                        break;
                                    case '2':
                                        $tOptionNoRef       = "";
                                        $tOptionSomeRef     = "";
                                        $tOptionAllRef      = "selected";
                                        break;
                                    default:
                                        $tOptionNoRef       = "selected";
                                        $tOptionSomeRef     = "";
                                        $tOptionAllRef      = "";
                                }
                                ?>
                                <!-- ???????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmInfoOthRef'); ?></label>
                                    <select class="selectpicker form-control xWPIDisabledOnApv xWConditionSearchPdt" id="ocmPOFrmInfoOthRef" name="ocmPOFrmInfoOthRef" maxlength="1">
                                        <option value="0" <?php echo $tOptionNoRef ?>><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmInfoOthRef0'); ?></option>
                                        <option value="1" <?php echo $tOptionSomeRef ?>><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmInfoOthRef1'); ?></option>
                                        <option value="2" <?php echo $tOptionAllRef ?>><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmInfoOthRef2'); ?></option>
                                    </select>
                                </div>
                                <!-- ?????????????????????????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmInfoOthDocPrint'); ?></label>
                                    <input type="text" class="form-control text-right" id="ocmPOFrmInfoOthDocPrint" name="ocmPOFrmInfoOthDocPrint" value="<?php echo $tPOFrmDocPrint; ?>" readonly>
                                </div>
                                <!-- ??????????????????????????????????????????????????????????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmInfoOthReAddPdt'); ?></label>
                                    <select class="form-control selectpicker xWPIDisabledOnApv xWConditionSearchPdt" id="ocmPOFrmInfoOthReAddPdt" name="ocmPOFrmInfoOthReAddPdt">
                                        <option value="1"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmInfoOthReAddPdt1'); ?></option>
                                        <option value="2" selected><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmInfoOthReAddPdt2'); ?></option>
                                    </select>
                                </div>
                                <!-- ???????????????????????? -->
                                <div class="form-group">
                                    <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmInfoOthRemark'); ?></label>
                                    <textarea class="" id="otaPOFrmInfoOthRmk" name="otaPOFrmInfoOthRmk" rows="10" maxlength="200" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOLabelFrmInfoOthRemark'); ?>" style="resize: none;height:86px;"><?php echo $tPOFrmRmk ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel ?????????????????? -->
            <div class="panel panel-default" style="margin-bottom: 25px;">
                <div id="odvSOReferenceDoc" class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
                    <label class="xCNTextDetail1"><?php echo language('document/saleorder/saleorder', '?????????????????????'); ?></label>
                    <a class="xCNMenuplus collapsed" role="button" data-toggle="collapse" href="#odvSODataFile" aria-expanded="true">
                        <i class="fa fa-plus xCNPlus"></i>
                    </a>
                </div>
                <div id="odvSODataFile" class="xCNMenuPanelData panel-collapse collapse" role="tabpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="odvPOShowDataTable">


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                var oSOCallDataTableFile = {
                    ptElementID: 'odvPOShowDataTable',
                    ptBchCode: $('#oetPOFrmBchCode').val(),
                    ptDocNo: $('#oetPODocNo').val(),
                    ptDocKey: 'TAPTPoHD',
                    ptSessionID: '<?= $this->session->userdata("tSesSessionID") ?>',
                    pnEvent: '<?= $nStaUploadFile ?>',
                    ptCallBackFunct: '',
                    ptStaApv: $('#ohdPOStaApv').val(),
                    ptStaDoc: $('#ohdPOStaDoc').val()
                }
                JCNxUPFCallDataTable(oSOCallDataTableFile);
            </script>
        </div>

        <!--??????????????????????????????????????????????????????-->
        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
            <div class="row">
                <!-- ??????????????????????????????????????????????????? -->
                <div id="odvPODataPanelDetailPDT" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="panel panel-default" style="margin-bottom:25px;position:relative;min-height:300px;">
                        <div class="panel-collapse collapse in" role="tabpanel" data-grpname="Condition">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                            <ul class="nav" role="tablist">

                                                <!-- ?????????????????? -->
                                                <li class="xWMenu active xCNStaHideShow" style="cursor:pointer;">
                                                    <a role="tab" data-toggle="tab" data-target="#odvPOContentProduct" aria-expanded="true"><?= language('document/expenserecord/expenserecord', '????????????????????????????????????') ?></a>
                                                </li>

                                                <!-- ????????????????????? -->
                                                <li class="xWMenu xCNStaHideShow" style="cursor:pointer;">
                                                    <a role="tab" data-toggle="tab" data-target="#odvPOContentHDRef" aria-expanded="false"><?= language('document/expenserecord/expenserecord', '???????????????????????????????????????') ?></a>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-content">
                                    <div id="odvPOContentProduct" class="tab-pane fade active in" style="padding: 0px !important;">

                                        <div class="row p-t-10">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <label class="xCNLabelFrm"><?php echo language('document/purchaseorder/purchaseorder', 'tPOTBSpl'); ?></label>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control xCNHide" id="oetPOFrmSplCode" name="oetPOFrmSplCode" value="<?php echo $tPOSplCode; ?>">
                                                        <input type="text" class="form-control" id="oetPOFrmSplName" name="oetPOFrmSplName" value="<?php echo $tPOSplName; ?>" placeholder="<?php echo language('document/purchaseorder/purchaseorder', 'tPOMsgValidSplCode') ?>" readonly>
                                                        <span class="input-group-btn">
                                                            <button id="obtPOBrowseSupplier" type="button" class="btn xCNBtnBrowseAddOn">
                                                                <img class="xCNIconFind">
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row p-t-10">

                                            <!--??????????????????????????????????????? Temp-->
                                            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control xCNInputWithoutSingleQuote" id="oetPOFrmFilterPdtHTML" name="oetPOFrmFilterPdtHTML" onkeyup="JSvPODOCFilterPdtInTableTemp()" placeholder="<?= language('common/main/main', 'tPlaceholder'); ?>">
                                                        <span class="input-group-btn">
                                                            <button id="obtPOMngPdtIconSearch" class="btn xCNBtnSearch" type="button" onclick="JSvPODOCFilterPdtInTableTemp()">
                                                                <img class="xCNIconBrowse" src="<?php echo base_url() . '/application/modules/common/assets/images/icons/search-24.png' ?>">
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 text-right">

                                                <div class="row" id="odvPOImExport">

                                                    <!--??????????????????-->
                                                    <div id="odvPOMngAdvTableList" class="btn-group xCNDropDrownGroup">
                                                        <button type="button" class="btn xCNBTNMngTable xCNImportBtn xWConditionSearchPdt" style="margin-right:10px;" onclick="JSxOpenImportForm()">
                                                            <?= language('common/main/main', 'tImport') ?>
                                                        </button>
                                                    </div>
                                                    <!--??????????????????-->
                                                    <div id="" class="btn-group xCNDropDrownGroup">
                                                        <button type="button" class="btn xCNBTNMngTable xCNImportBtn xWConditionSearchPdt" style="margin-right:10px;" id="obtPOExportDT">
                                                            <?= language('common/main/main', 'tExport') ?>
                                                        </button>
                                                    </div>
                                                    <!--????????????????????????-->
                                                    <div id="odvPOMngDelPdtInTableDT" class="btn-group xCNDropDrownGroup">
                                                        <button type="button" class="btn xCNBTNMngTable xWConditionSearchPdt" data-toggle="dropdown">
                                                            <?php echo language('common/main/main', 'tCMNOption') ?>
                                                            <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu" role="menu">
                                                            <li id="oliPOBtnDeleteMulti" class="disabled">
                                                                <a data-toggle="modal" data-target="#odvPOModalDelPdtInDTTempMultiple"><?php echo language('common/main/main', 'tDelAll') ?></a>
                                                            </li>
                                                        </ul>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" id="odvPOAddPd">
                                                <!--????????????????????????????????????????????????-->
                                                <div class="form-group" style="width: 85%;">
                                                    <input type="text" class="form-control" id="oetPOInsertBarcode" autocomplete="off" name="oetPOInsertBarcode" maxlength="50" value="" onkeypress="Javascript:if(event.keyCode==13) JSxSearchFromBarcode(event,this);" placeholder="????????????????????????????????????????????????????????????????????? ???????????? ??????????????????????????????">
                                                </div>

                                                <!--??????????????????????????????????????????????????????-->
                                                <div class="form-group">
                                                    <div style="position: absolute;right: 15px;top:-5px;">
                                                        <button type="button" id="obtPODocBrowsePdt" class="xCNBTNPrimeryPlus xCNDocBrowsePdt">+</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row p-t-10" id="odvPODataPdtTableDTTemp"></div>
                                        <div class="odvRowDataEndOfBill" id="odvRowDataEndOfBill">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <label class="pull-left mark-font"><?= language('document/purchaseorder/purchaseorder', 'tPOPanelQtyFooter'); ?></label>
                                                    <label class="pull-right mark-font">&nbsp;<?= language('document/purchaseorder/purchaseorder', 'tPOItems'); ?> </label>
                                                    <label class="pull-right mark-font xShowQtyFooter">0 </label>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--?????????????????????????????????????????????-->
                                        <div class="row xCNHide" id="odvRowDataEndOfBill">
                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading mark-font" id="odvDataTextBath"></div>
                                                </div>
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <div class="pull-left mark-font"><?= language('document/purchaseorder/purchaseorder', 'tPOTBVatRate'); ?></div>
                                                        <div class="pull-right mark-font"><?= language('document/purchaseorder/purchaseorder', 'tPOTBAmountVat'); ?></div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    <div class="panel-body">
                                                        <ul class="list-group" id="oulDataListVat">
                                                        </ul>
                                                    </div>
                                                    <div class="panel-heading">
                                                        <label class="pull-left mark-font"><?= language('document/purchaseorder/purchaseorder', 'tPOTBTotalValVat'); ?></label>
                                                        <label class="pull-right mark-font" id="olbVatSum">0.00</label>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Of Bill -->
                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <ul class="list-group">
                                                            <li class="list-group-item">
                                                                <label class="pull-left mark-font"><?= language('document/purchaseorder/purchaseorder', 'tPOTBSumFCXtdNet'); ?></label>
                                                                <input type="text" id="olbSumFCXtdNetAlwDis" style="display:none;"></label>
                                                                <label class="pull-right mark-font" id="olbSumFCXtdNet">0.00</label>
                                                                <div class="clearfix"></div>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <label class="pull-left"><?= language('document/purchaseorder/purchaseorder', 'tPOTBDisChg'); ?>
                                                                    <button type="button" class="xCNBTNPrimeryDisChgPlus" onclick="JCNvPOMngDocDisChagHD(this)" style="float: right; margin-top: 3px; margin-left: 5px;">+</button>
                                                                </label>
                                                                <label class="pull-left" style="margin-left: 5px;" id="olbDisChgHD"></label>
                                                                <label class="pull-right" id="olbSumFCXtdAmt">0.00</label>
                                                                <div class="clearfix"></div>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <label class="pull-left"><?= language('document/purchaseorder/purchaseorder', 'tPOTBSumFCXtdNetAfHD'); ?></label>
                                                                <label class="pull-right" id="olbSumFCXtdNetAfHD">0.00</label>
                                                                <div class="clearfix"></div>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <label class="pull-left"><?= language('document/purchaseorder/purchaseorder', 'tPOTBSumFCXtdVat'); ?></label>
                                                                <label class="pull-right" id="olbSumFCXtdVat">0.00</label>
                                                                <input type="hidden" name="ohdSumFCXtdVat" id="ohdSumFCXtdVat" value="0.00">
                                                                <div class="clearfix"></div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="panel-heading">
                                                        <label class="pull-left mark-font"><?= language('document/purchaseorder/purchaseorder', 'tPOTBFCXphGrand'); ?></label>
                                                        <label class="pull-right mark-font" id="olbCalFCXphGrand">0.00</label>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ????????????????????? -->
                                    <div id="odvPOContentHDRef" class="tab-pane fade" style="padding: 0px !important;">
                                        <div class="row p-t-15">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right">
                                                <div style="margin-top:-2px;">
                                                    <button type="button" id="obtPOAddDocRef" class="xCNBTNPrimeryPlus xCNDocBrowsePdt xCNHideWhenCancelOrApprove">+</button>
                                                </div>
                                            </div>
                                            <div id="odvPOTableHDRef"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- ======================================================================== View Modal Appove Document  ======================================================================== -->
<div id="odvPOModalAppoveDoc" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="xCNHeardModal modal-title" style="display:inline-block"><?php echo language('common/main/main', 'tApproveTheDocument'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?php echo language('common/main/main', 'tMainApproveStatus'); ?></p>
                <ul>
                    <li><?php echo language('common/main/main', 'tMainApproveStatus1'); ?></li>
                    <li><?php echo language('common/main/main', 'tMainApproveStatus2'); ?></li>
                    <li><?php echo language('common/main/main', 'tMainApproveStatus3'); ?></li>
                    <li><?php echo language('common/main/main', 'tMainApproveStatus4'); ?></li>
                </ul>
                <p><?php echo language('common/main/main', 'tMainApproveStatus5'); ?></p>
                <p><strong><?php echo language('common/main/main', 'tMainApproveStatus6'); ?></strong></p>
            </div>
            <div class="modal-footer">
                <button onclick="JSxPOApproveDocument(true)" type="button" class="btn xCNBTNPrimery">
                    <?php echo language('common/main/main', 'tModalConfirm'); ?>
                </button>
                <button type="button" class="btn xCNBTNDefult" data-dismiss="modal">
                    <?php echo language('common/main/main', 'tModalCancel'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================================================================================================================= -->

<!-- ======================================================================== View Modal Cancel Document  ======================================================================== -->
<div class="modal fade" id="odvPurchaseInviocePopupCancel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header xCNModalHead">
                <label class="xCNTextModalHeard">????????????????????????????????????</label>
            </div>
            <div class="modal-body">
                <p id="obpMsgApv">???????????????????????????????????????????????????????????????????????? ?????????????????????????????????????????? ???????????????????????????????????????????????????</p>
                <p><strong>????????????????????????????????????????????????????????????????????????????????????????????????????????????????</strong></p>
            </div>
            <div class="modal-footer">
                <button onclick="JSnPOCancelDocument(true)" type="button" class="btn xCNBTNPrimery">
                    <?php echo language('common/main/main', 'tModalConfirm'); ?>
                </button>
                <button type="button" class="btn xCNBTNDefult" data-dismiss="modal">
                    <?php echo language('common/main/main', 'tModalCancel'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================================================================================================================= -->

<!-- =====================================================================  Modal Advance Table Product DT Temp ==================================================================-->
<div class="modal fade" id="odvPOOrderAdvTblColumns" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        <label class="xCNTextModalHeard" style="font-weight: bold; font-size: 20px;"><?php echo language('common/main/main', 'tModalAdvTable'); ?></label>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-body" id="odvPOModalBodyAdvTable">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo language('common/main/main', 'tModalAdvClose'); ?></button>
                <button id="obtPOSaveAdvTableColums" type="button" class="btn btn-primary"><?php echo language('common/main/main', 'tModalAdvSave'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================================================================================================================= -->

<!-- ============================================================== View Modal Delete Product In DT DocTemp Multiple  ============================================================ -->
<div id="odvPOModalDelPdtInDTTempMultiple" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header xCNModalHead">
                <label class="xCNTextModalHeard"><?php echo language('common/main/main', 'tModalDelete') ?></label>
            </div>
            <div class="modal-body">
                <span id="ospTextConfirmDelMultiple" class="xCNTextModal" style="display: inline-block; word-break:break-all"></span>
                <input type="hidden" id="ohdConfirmPODocNoDelete" name="ohdConfirmPODocNoDelete">
                <input type="hidden" id="ohdConfirmPOSeqNoDelete" name="ohdConfirmPOSeqNoDelete">
                <input type="hidden" id="ohdConfirmPOPdtCodeDelete" name="ohdConfirmPOPdtCodeDelete">
                <input type="hidden" id="ohdConfirmPOPunCodeDelete" name="ohdConfirmPOPunCodeDelete">

            </div>
            <div class="modal-footer">
                <button id="osmConfirmDelMultiple" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button"><?php echo language('common/main/main', 'tModalConfirm') ?></button>
                <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button" data-dismiss="modal"><?php echo language('common/main/main', 'tModalCancel') ?></button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================================================================================================================= -->

<!-- ======================================================================== Modal ?????????????????????????????????   ======================================================================== -->
<div id="odvPOModalPleseselectSPL" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="xCNHeardModal modal-title" style="display:inline-block"><?= language('common/main/main', 'tMessageAlert') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>???????????????????????????????????????????????????????????? ?????????????????????????????????????????????</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn xCNBTNPrimery" data-dismiss="modal" onclick="JSxFocusInputCustomer();">
                    <?= language('common/main/main', 'tCMNOK') ?>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================================================================================================================= -->

<!-- ======================================================================== Modal ????????????????????????????????????????????? ======================================================================== -->
<div id="odvPOModalPDTNotFound" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="xCNHeardModal modal-title" style="display:inline-block"><?= language('common/main/main', 'tMessageAlert') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>??????????????????????????????????????????????????? ????????????????????????????????????????????????????????????</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn xCNBTNPrimery" data-dismiss="modal" onclick="JSxNotFoundClose();">
                    <?= language('common/main/main', 'tCMNOK') ?>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================================================================================================================= -->

<!-- ======================================================================== ????????????????????????????????????????????????????????????????????? ======================================================================== -->
<div id="odvPOModalPDTMoreOne" class="modal fade">
    <div class="modal-dialog" role="document" style="width: 85%; margin: 1.75rem auto;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        <label class="xCNTextModalHeard" style="font-weight: bold; font-size: 20px;">????????????????????????????????????????????????</label>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right">
                        <button class="btn xCNBTNPrimery xCNBTNPrimery2Btn" onclick="JCNxConfirmPDTMoreOne(1)" data-dismiss="modal">???????????????</button>
                        <button class="btn xCNBTNDefult xCNBTNDefult2Btn" onclick="JCNxConfirmPDTMoreOne(2)" data-dismiss="modal">?????????</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <table class="table table-striped xCNTablePDTMoreOne">
                    <thead>
                        <tr>
                            <th class="xCNTextBold" style="text-align:center; width:120px;"><?= language('common/main/main', 'tModalcodePDT') ?></th>
                            <th class="xCNTextBold" style="text-align:center; width:160px;"><?= language('common/main/main', 'tModalnamePDT') ?></th>
                            <th class="xCNTextBold" style="text-align:center; width:120px;"><?= language('common/main/main', 'tModalPriceUnit') ?></th>
                            <th class="xCNTextBold" style="text-align:center; width:160px;"><?= language('common/main/main', 'tModalbarcodePDT') ?></th>
                            <th class="xCNTextBold" style="text-align:center; width:120px;">?????????????????????</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================================================================================================================= -->

<!-- ======================================================================== Modal ????????????????????????????????????????????? ======================================================================== -->
<div id="odvPOModalChangeBCH" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="xCNHeardModal modal-title" style="display:inline-block"><?= language('common/main/main', 'tMessageAlert') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>???????????????????????????????????????????????????????????? ???????????????????????????????????????????????????????????? ???????????????????????????????????? ???????????????????????????????????????????????????????????????????????????????????? ? </p>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" id="obtChangeBCH" class="btn xCNBTNPrimery"><?php echo language('common/main/main', 'tModalConfirm'); ?></button>
                <button type="button" data-dismiss="modal" class="btn xCNBTNDefult"><?php echo language('common/main/main', '??????????????????'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================================================================================================================= -->

<!-- ======================================================================== Modal ????????????????????????????????????????????? ======================================================================== -->
<div id="odvPOModalImpackImportExcel" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="xCNHeardModal modal-title" style="display:inline-block"><?= language('common/main/main', 'tMessageAlert') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>???????????????????????????????????????????????????????????????????????????</p>
                <p>&nbsp;&nbsp;&nbsp;???????????????????????? ?????????????????????????????????????????????????????????????????? ?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? </p>
                <p>&nbsp;&nbsp;&nbsp;???????????????????????? ??????????????????????????????????????????????????????</p>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" id="obtPOImportConfirm" class="btn xCNBTNPrimery"><?php echo language('common/main/main', 'tModalConfirm'); ?></button>
                <button type="button" data-dismiss="modal" class="btn xCNBTNDefult"><?php echo language('common/main/main', '??????????????????'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================================================================================================================= -->

<!-- =========================================== ???????????????????????????????????? ============================================= -->
<div id="odvPOModalAddress" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header xCNModalHead">
                <label class="xCNTextModalHeard">????????????????????????????????????</label>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-lg-12">
                        <!--?????????????????????-->
                        <div class="form-group">
                            <label class="xCNLabelFrm"><?= language('company/company/company', 'tCMPAddrName'); ?></label>
                            <div class="input-group" style="width:100%;">
                                <input type="text" class="input100 xCNHide" id="ohdPOAddrCode" name="ohdPOAddrCode" value="">
                                <input class="form-control xWPointerEventNone" type="text" id="ohdPOAddrName" name="ohdPOAddrName" value="" readonly placeholder="<?= language('company/company/company', 'tCMPAddrName'); ?>">
                                <span class="input-group-btn">
                                    <button id="obtPOBrowseAddr" type="button" class="btn xCNBtnBrowseAddOn">
                                        <img src="<?= base_url() . '/application/modules/common/assets/images/icons/find-24.png'; ?>">
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ?????????????????????????????? -->
                    <div class="xWPOAddress1">
                        <div class="col-lg-12">
                            <!--????????????????????????????????????????????????????????????????????????????????????????????????????????????-->
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?= language('company/company/company', 'tCMPTaxNo'); ?></label>
                                <input class="form-control xWPointerEventNone" type="text" id="ohdPOAddrTaxNo" name="ohdPOAddrTaxNo" value="" readonly placeholder="<?= language('company/company/company', 'tCMPTaxNo'); ?>">
                            </div>

                            <!--??????????????????????????????-->
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?= language('company/company/company', 'tCMPAddressNo'); ?></label>
                                <input class="form-control xWPointerEventNone" type="text" id="ohdPOAddrNoHouse" name="ohdPOAddrNoHouse" value="" readonly placeholder="<?= language('company/company/company', 'tCMPAddressNo'); ?>">
                            </div>

                            <!--???????????????????????? / ???????????????-->
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?= language('company/company/company', 'tCMPVillage'); ?></label>
                                <input class="form-control xWPointerEventNone" type="text" id="ohdPOAddrVillage" name="ohdPOAddrVillage" value="" readonly placeholder="<?= language('company/company/company', 'tCMPVillage'); ?>">
                            </div>

                            <!--?????????-->
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?= language('company/company/company', 'tCMPRoad'); ?></label>
                                <input class="form-control xWPointerEventNone" type="text" id="ohdPOAddrRoad" name="ohdPOAddrRoad" value="" readonly placeholder="<?= language('company/company/company', 'tCMPRoad'); ?>">
                            </div>

                        </div>

                        <!--???????????? / ????????????-->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?= language('company/company/company', 'tCMPSubDistrict'); ?></label>
                                <input class="form-control xWPointerEventNone" type="text" id="ohdPOAddrSubDistrict" name="ohdPOAddrSubDistrict" value="" readonly placeholder="<?= language('company/company/company', 'tCMPSubDistrict'); ?>">
                            </div>
                        </div>

                        <!--????????? / ???????????????-->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?= language('company/company/company', 'tCMPDistict'); ?></label>
                                <input class="form-control xWPointerEventNone" type="text" id="ohdPOAddrDistict" name="ohdPOAddrDistict" value="" readonly placeholder="<?= language('company/company/company', 'tCMPDistict'); ?>">
                            </div>
                        </div>

                        <!--?????????????????????-->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?= language('company/company/company', 'tCMPProvince'); ?></label>
                                <input class="form-control xWPointerEventNone" type="text" id="ohdPOAddrProvince" name="ohdPOAddrProvince" value="" readonly placeholder="<?= language('company/company/company', 'tCMPProvince'); ?>">
                            </div>
                        </div>

                        <!--?????????????????????????????????-->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?= language('company/company/company', 'tCMPZipCode'); ?></label>
                                <input class="form-control xWPointerEventNone" type="text" id="ohdPOZipCode" name="ohdPOZipCode" value="" readonly placeholder="<?= language('company/company/company', 'tCMPZipCode'); ?>">
                            </div>
                        </div>


                    </div>

                    <!-- ?????????????????????????????? -->
                    <div class="xWPOAddress2">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?= language('company/company/company', '????????????????????? 1'); ?></label>
                                <textarea class="form-control" id="ohdPOAddV2Desc1" name="ohdPOAddV2Desc1" maxlength="200" readonly></textarea>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?= language('company/company/company', '????????????????????? 2'); ?></label>
                                <textarea class="form-control" id="ohdPOAddV2Desc2" name="ohdPOAddV2Desc2" maxlength="200" readonly></textarea>
                            </div>
                        </div>
                    </div>

                    <!--????????????????????????-->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="xCNLabelFrm"><?= language('company/company/company', 'tCMPTel'); ?></label>
                            <input class="form-control xWPointerEventNone" type="text" id="ohdPOAddrTel" name="ohdPOAddrTel" value="" readonly placeholder="<?= language('company/company/company', 'tCMPTel'); ?>">
                        </div>
                    </div>

                    <!--????????????????????????-->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="xCNLabelFrm"><?= language('company/company/company', 'tCMPFax'); ?></label>
                            <input class="form-control xWPointerEventNone" type="text" id="ohdPOAddrFax" name="ohdPOAddrFax" value="" readonly placeholder="<?= language('company/company/company', 'tCMPFax'); ?>">
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button id="osmConfirmAddress" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button" onclick="JSxConfirmAddress();" data-dismiss="modal"><?= language('common/main/main', 'tModalConfirm') ?></button>
                <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button" data-dismiss="modal"><?= language('common/main/main', 'tModalCancel') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================== ?????????????????????????????????????????????????????? ============================================= -->
<div id="odvPOModalRefIntDoc" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width: 1200px;">
        <div class="modal-content">
            <div class="modal-header xCNModalHead">
                <label class="xCNTextModalHeard"><?php echo language('document/purchaseorder/purchaseorder', 'tPORefIntDocPrsTital') ?></label>
            </div>
            <div class="modal-body">
                <div class="row" id="odvPOFromRefIntDoc"></div>
            </div>
            <div class="modal-footer">
                <button id="obtConfirmRefDocInt" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button" data-dismiss="modal"><?= language('common/main/main', 'tModalConfirm') ?></button>
                <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button" data-dismiss="modal"><?= language('common/main/main', 'tModalCancel') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================== Modal Delete Document Single ===================================================== -->
<div id="odvPOModalAddressRemove" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header xCNModalHead">
                <label class="xCNTextModalHeard"><?= language('common/main/main', 'tModalWarning') ?></label>
            </div>
            <div class="modal-body">
                <span id="ospTextConfirmDelSingle" class="xCNTextModal" style="display: inline-block; word-break:break-all"><?= language('common/main/main', 'tModalAddressClear') ?></span>
            </div>
            <div class="modal-footer">
                <button id="osmConfirmRemoveAddress" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button"><?php echo language('common/main/main', 'tModalConfirm') ?></button>
                <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button" data-dismiss="modal"><?php echo language('common/main/main', 'tModalCancel') ?></button>
            </div>
        </div>
    </div>
</div>
<!-- ======================================================================================================================================== -->

<!-- ===========================================  ?????????????????????????????????????????????????????? (??????????????? ???????????? ??????????????????) =========================================== -->
<div id="odvPOModalAddDocRef" class="modal fade" tabindex="-1" role="dialog" style='z-index:1045'>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="ofmPOFormAddDocRef" class="validate-form" action="javascript:void(0)" method="post" enctype="multipart/form-data">
                <div class="modal-header xCNModalHead">
                    <label class="xCNTextModalHeard"><?=language('common/main/main', '???????????????????????????????????????') ?></label>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control xCNHide" id="oetPORefDocNoOld" name="oetPORefDocNoOld">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-xs-12">
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?=language('common/main/main', '??????????????????????????????????????????????????????????????????'); ?></label>
                                <select class="selectpicker form-control" id="ocbPORefType" name="ocbPORefType">
                                    <option value="1" selected><?=language('common/main/main', '????????????????????????????????????'); ?></option>
                                    <option value="3"><?=language('common/main/main', '???????????????????????????????????????'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-xs-12 xWShowRefInt">
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?=language('common/main/main', '??????????????????'); ?></label>
                                <select class="selectpicker form-control" id="ocbPORefDoc" name="ocbPORefDoc">
                                    <option value="1" selected><?=language('common/main/main', '??????????????????????????????????????????'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-xs-12 xWShowRefInt">
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?=language('common/main/main', '?????????????????????????????????????????????????????????') ?></label>
                                <div class="input-group">
                                    <input type="text" class="form-control xCNHide xCNInputWithoutSpcNotThai xCNInputWithoutSingleQuote" id="oetPODocRefInt" name="oetPODocRefInt" maxlength="20" value="">
                                    <input type="text" class="form-control xWPointerEventNone" id="oetPODocRefIntName" name="oetPODocRefIntName" maxlength="20" placeholder="<?=language('common/main/main', '?????????????????????????????????????????????????????????') ?>" value="" readonly>
                                    <span class="input-group-btn">
                                        <button id="obtPOBrowseRefDoc" type="button" class="btn xCNBtnBrowseAddOn">
                                            <img src="<?= base_url() . '/application/modules/common/assets/images/icons/find-24.png' ?>">
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-xs-12 xWShowRefExt">
                            <div class="form-group">
                                <label class="xCNLabelFrm"><span class="text-danger">*</span><?=language('common/main/main', '?????????????????????????????????????????????????????????'); ?></label>
                                <input type="text" class="form-control" id="oetPORefDocNo" name="oetPORefDocNo" placeholder="<?=language('common/main/main', '?????????????????????????????????????????????????????????'); ?>" maxlength="20" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-xs-12">
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?=language('document/expenserecord/expenserecord', '?????????????????????????????????????????????????????????'); ?></label>
                                <div class="input-group">
                                    <input type="text" class="form-control xCNDatePicker xCNInputMaskDate" id="oetPORefDocDate" name="oetPORefDocDate" placeholder="YYYY-MM-DD" autocomplete="off">
                                    <span class="input-group-btn">
                                        <button id="obtPORefDocDate" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-xs-12 xWShowRefExt">
                            <div class="form-group">
                                <label class="xCNLabelFrm"><?=language('common/main/main', '??????????????????????????????'); ?></label>
                                <input type="text" class="form-control" id="oetPORefKey" name="oetPORefKey" placeholder="<?=language('common/main/main', '??????????????????????????????'); ?>" maxlength="10" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="obtPOConfirmAddDocRef" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="submit"><?=language('common/main/main', 'tModalConfirm') ?></button>
                    <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button" data-dismiss="modal"><?=language('common/main/main', 'tModalCancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="<?php echo base_url('application/modules/common/assets/src/jThaiBath.js') ?>"></script>
<script src="<?php echo base_url('application/modules/common/assets/js/jquery.mask.js') ?>"></script>
<script src="<?php echo base_url('application/modules/common/assets/src/jFormValidate.js') ?>"></script>
<?php include('script/jPurchaseOrderAdd.php'); ?>
<?php include('dis_chg/wPurchaseOrderDisChgModal.php'); ?>


<script>
    //????????????????????????????????????????????????????????????
    if ('<?= @$tPOSplPayMentType ?>' == '2') {
        $('.xCNPanel_CreditTerm').show();
    }

    $('#ocmPOFrmSplInfoPaymentType').on('change', function() {
        if (this.value == 1) {
            $('.xCNPanel_CreditTerm').hide();
        } else {
            $('.xCNPanel_CreditTerm').show();
        }
    });

    //????????????????????????????????????????????????????????????
    function JSxFocusInputCustomer() {
        $('#oetPOFrmCstName').focus();
    }

    function JSxNotFoundClose() {
        $('#oetPOInsertBarcode').focus();
    }

    //?????????????????????????????????????????????
    function JSxSearchFromBarcode(e, elem) {
        var tValue = $(elem).val();
        if ($('#oetPOFrmSplName').val() != "") {
            JSxCheckPinMenuClose();
            if (tValue.length === 0) {

            } else {
                // JCNxOpenLoading();
                $('#oetPOInsertBarcode').attr('readonly', true);
                JCNSearchBarcodePdt(tValue);
                $('#oetPOInsertBarcode').val('');
            }
        } else {
            $('#odvPOModalPleseselectSPL').modal('show');
            $('#oetPOInsertBarcode').val('');
        }
        e.preventDefault();
    }

    //???????????????????????????????????????
    function JCNSearchBarcodePdt(ptTextScan) {

        var tPOSplCode = $('#oetPOFrmSplCode').val();
        if (typeof(tPOSplCode) === undefined || tPOSplCode === '') {
            $('#oetPOInsertBarcode').val('');
            $('#oetPOInsertBarcode').attr('readonly', false);

            var tWarningMessage = '?????????????????????????????????????????????????????????????????????????????????????????????';
            FSvCMNSetMsgWarningDialog(tWarningMessage);
            return;
        }

        // var tWhereCondition = "";
        // if( tPISplCode != "" ){
        //     tWhereCondition = " AND FTPdtSetOrSN IN('1','2') ";
        // }

        var aMulti = [];
        $.ajax({
            type: "POST",
            url: "BrowseDataPDTTableCallView",
            data: {
                // aPriceType      : ['Price4Cst',tPOPplCode],
                aPriceType: ["Cost", "tCN_Cost", "Company", "1"],
                NextFunc: "",
                SPL: $("#oetPOFrmSplCode").val(),
                BCH: $("#oetPOFrmBchCode").val(),
                tInpSesSessionID: $('#ohdSesSessionID').val(),
                tInpUsrCode: $('#ohdPOUsrCode').val(),
                tInpLangEdit: $('#ohdPOLangEdit').val(),
                tInpSesUsrLevel: $('#ohdSesUsrLevel').val(),
                tInpSesUsrBchCom: $('#ohdSesUsrBchCom').val(),
                // Where            : [tWhereCondition],
                tTextScan: ptTextScan,
                'tWhere': [" AND PPCZ.FTPdtStaAlwPoSPL = 1 "],
                'aPackDataForSearch': {
                    'tSearchPDTType': "T1,T3,T4,T5,T6,S2,S3,S4"
                }
            },
            cache: false,
            timeout: 0,
            success: function(tResult) {
                // $('#oetPOInsertBarcode').attr('readonly',false);
                JCNxCloseLoading();
                var oText = JSON.parse(tResult);
                if (oText == '800') {
                    $('#oetPOInsertBarcode').attr('readonly', false);
                    $('#odvPOModalPDTNotFound').modal('show');
                    $('#oetPOInsertBarcode').val('');
                } else {
                    if (oText.length > 1) {

                        // ??????????????????????????????????????????????????????????????????
                        $('#odvPOModalPDTMoreOne').modal('show');
                        $('#odvPOModalPDTMoreOne .xCNTablePDTMoreOne tbody').html('');
                        for (i = 0; i < oText.length; i++) {
                            var aNewReturn = JSON.stringify(oText[i]);
                            var tTest = "[" + aNewReturn + "]";
                            var oEncodePackData = window.btoa(unescape(encodeURIComponent(tTest)));
                            var tHTML = "<tr class='xCNColumnPDTMoreOne" + i + " xCNColumnPDTMoreOne' data-information='" + oEncodePackData + "' style='cursor: pointer;'>";
                            tHTML += "<td>" + oText[i].pnPdtCode + "</td>";
                            tHTML += "<td>" + oText[i].packData.PDTName + "</td>";
                            tHTML += "<td>" + oText[i].packData.PUNName + "</td>";
                            tHTML += "<td>" + oText[i].ptBarCode + "</td>";
                            tHTML += "<td class='xCNTextRight' style='text-align: right;'>" + oText[i].packData.PriceRet + "</td>";
                            tHTML += "</tr>";
                            $('#odvPOModalPDTMoreOne .xCNTablePDTMoreOne tbody').append(tHTML);
                        }

                        //?????????????????????????????????
                        $('.xCNColumnPDTMoreOne').off();

                        //????????????????????????????????????
                        $('.xCNColumnPDTMoreOne').on('dblclick', function(e) {
                            $('#odvPOModalPDTMoreOne').modal('hide');
                            var tJSON = decodeURIComponent(escape(window.atob($(this).attr('data-information'))));
                            FSvPOAddPdtIntoDocDTTemp(tJSON); //Client
                            FSvPOAddBarcodeIntoDocDTTemp(tJSON);
                        });

                        //??????????????????????????????
                        $('.xCNColumnPDTMoreOne').on('click', function(e) {
                            //???????????????????????????????????????????????????????????????
                            // var tCheck = $(this).hasClass('xCNActivePDT');
                            // if($(this).hasClass('xCNActivePDT')){
                            //     //??????????????????
                            //     $(this).removeClass('xCNActivePDT');
                            //     $(this).children().attr('style', 'background-color:transparent !important; color:#232C3D !important');
                            // }else{
                            //     //???????????????
                            //     $(this).addClass('xCNActivePDT');
                            //     $(this).children().attr('style', 'background-color:#1866ae !important; color:#FFF !important');
                            // }

                            //??????????????????????????????????????????????????????????????????
                            $('.xCNColumnPDTMoreOne').removeClass('xCNActivePDT');
                            $('.xCNColumnPDTMoreOne').children().attr('style', 'background-color:transparent !important; color:#232C3D !important;');
                            $('.xCNColumnPDTMoreOne').children(':last-child').css('text-align', 'right');

                            $(this).addClass('xCNActivePDT');
                            $(this).children().attr('style', 'background-color:#1866ae !important; color:#FFF !important;');
                            $(this).children().last().css('text-align', 'right');
                        });
                    } else {
                        //??????????????????????????????
                        var aNewReturn = JSON.stringify(oText);
                        console.log('aNewReturn: ' + aNewReturn);
                        // var aNewReturn  = '[{"pnPdtCode":"00009","ptBarCode":"ca2020010003","ptPunCode":"00001","packData":{"SHP":null,"BCH":null,"PDTCode":"00009","PDTName":"?????????_03","PUNCode":"00001","Barcode":"ca2020010003","PUNName":"?????????","PriceRet":"17.00","PriceWhs":"0.00","PriceNet":"0.00","IMAGE":"D:/xampp/htdocs/Moshi-Moshi/application/modules/product/assets/systemimg/product/00009/Img200128172902CEHHRSS.jpg","LOCSEQ":"","Remark":"?????????_03","CookTime":0,"CookHeat":0}}]';
                        FSvPOAddPdtIntoDocDTTemp(aNewReturn); //Client
                        // JCNxCloseLoading();
                        // $('#oetPOInsertBarcode').attr('readonly',false);
                        // $('#oetPOInsertBarcode').val('');
                        FSvPOAddBarcodeIntoDocDTTemp(aNewReturn); //Server
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // JCNxResponseError(jqXHR,textStatus,errorThrown);
                JCNSearchBarcodePdt(ptTextScan);
            }
        });
    }

    //????????????????????????????????? ???????????????????????????????????????????????????????????????
    function JCNxConfirmPDTMoreOne($ptType) {
        if ($ptType == 1) {
            $("#odvPOModalPDTMoreOne .xCNTablePDTMoreOne tbody .xCNActivePDT").each(function(index) {
                var tJSON = decodeURIComponent(escape(window.atob($(this).attr('data-information'))));
                FSvPOAddPdtIntoDocDTTemp(tJSON);
                FSvPOAddBarcodeIntoDocDTTemp(tJSON);
            });
        } else {
            $('#oetPOInsertBarcode').attr('readonly', false);
            $('#oetPOInsertBarcode').val('');
        }
    }

    //???????????????????????????????????????????????????????????????
    function FSvPOAddBarcodeIntoDocDTTemp(ptPdtData) {
        var nStaSession = 1;
        if (typeof nStaSession !== "undefined" && nStaSession == 1) {
            // JCNxOpenLoading();
            var ptXthDocNoSend = "";
            if ($("#ohdPORoute").val() == "docPOEventEdit") {
                ptXthDocNoSend = $("#oetPODocNo").val();
            }
            var tPOVATInOrEx = $('#ocmPOFrmSplInfoVatInOrEx').val();
            var tPOOptionAddPdt = $('#ocmPOFrmInfoOthReAddPdt').val();
            let tPOPplCodeBch = $('#ohdPOPplCodeBch').val();
            let tPOPplCodeCst = $('#ohdPOPplCodeCst').val();
            var nKey = parseInt($('#otbPODocPdtAdvTableList tr:last').attr('data-seqno'));

            $('#oetPOInsertBarcode').attr('readonly', false);
            $('#oetPOInsertBarcode').val('');

            $.ajax({
                type: "POST",
                url: "docPOAddPdtIntoDTDocTemp",
                data: {
                    'tSelectBCH': $('#oetPOFrmBchCode').val(),
                    'tPODocNo': ptXthDocNoSend,
                    'tPOVATInOrEx': tPOVATInOrEx,
                    'tPOOptionAddPdt': tPOOptionAddPdt,
                    'tPOPdtData': ptPdtData,
                    'tPOPplCodeBch': tPOPplCodeBch,
                    'tPOPplCodeCst': tPOPplCodeCst,
                    'ohdSesSessionID': $('#ohdSesSessionID').val(),
                    'ohdPOUsrCode': $('#ohdPOUsrCode').val(),
                    'ohdPOLangEdit': $('#ohdPOLangEdit').val(),
                    'ohdSesUsrLevel': $('#ohdSesUsrLevel').val(),
                    'ohdPOSesUsrBchCode': $('#ohdPOSesUsrBchCode').val(),
                    'tSeqNo': nKey
                    //     'nVatRate'            : $('#ohdPOFrmSplVatRate').val(),
                    //     'nVatCode'            : $('#ohdPOFrmSplVatCode').val()
                },
                cache: false,
                timeout: 0,
                success: function(oResult) {
                    // JSvPOLoadPdtDataTableHtml();
                    JSxPOCountPdtItems();
                    var aResult = JSON.parse(oResult);

                    if (aResult['nStaEvent'] == 1) {
                        JCNxCloseLoading();
                        // $('#oetPOInsertBarcode').attr('readonly',false);
                        // $('#oetPOInsertBarcode').val('');
                        // if(tPOOptionAddPdt=='1'){
                        //     JSvPOCallEndOfBill();
                        // }else{
                        //     JCNxCloseLoading();
                        // }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // JCNxResponseError(jqXHR, textStatus, errorThrown);
                    FSvPOAddBarcodeIntoDocDTTemp(ptPdtData);
                }
            });
        } else {
            JCNxphowMsgSessionExpired();
        }
    }

    var nStaShwAddress = <?= $nStaShwAddress ?>;
    if (nStaShwAddress == 1) {
        $('.xWPOAddress1').show();
        $('.xWPOAddress2').hide();
    } else {
        $('.xWPOAddress1').hide();
        $('.xWPOAddress2').show();
    }

</script>