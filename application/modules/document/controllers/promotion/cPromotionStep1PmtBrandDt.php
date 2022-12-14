<?php
defined('BASEPATH') or exit('No direct script access allowed');

class cPromotionStep1PmtBrandDt extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('document/promotion/mPromotionStep1PmtBrandDt');
        $this->load->model('document/promotion/mPromotionStep1PmtDt');
        $this->load->model('document/promotion/mPromotion');

        // Clean XSS Filtering Security
		$this->load->helper("security");
		if ($this->security->xss_clean($this->input->post(), TRUE) === FALSE){
            echo "ERROR XSS Filter";
        }
    }

    /**
     * Functionality : Get PmtBrandDt in Temp
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : Array
     */
    public function FSxCPromotionGetPmtBrandDtInTmp()
    {
        $tPmtGroupTypeTmp = $this->input->post('tPmtGroupTypeTmp');
        $tPmtGroupListTypeTmp = $this->input->post('tPmtGroupListTypeTmp');
        $tPmtGroupNameTmp = $this->input->post('tPmtGroupNameTmp');
        $tPmtGroupNameTmpOld = $this->input->post('tPmtGroupNameTmpOld');
        $tSearchAll = $this->input->post('tSearchAll');
        $nPage = $this->input->post('nPageCurrent');
        $tPdtCond = $this->input->post('tPdtCond');
        $aAlwEvent = FCNaHCheckAlwFunc('promotion/0/0');
        $nOptDecimalShow = FCNxHGetOptionDecimalShow();
        $tUserSessionID = $this->session->userdata("tSesSessionID");
        $tUserLevel = $this->session->userdata('tSesUsrLevel');
        $tBchCodeLogin = $tUserLevel == 'HQ' ? FCNtGetBchInComp() : $this->session->userdata("tSesUsrBchCodeDefault");

        $aPdtCond = json_decode($tPdtCond, true);

        if ($nPage == '' || $nPage == null) {
            $nPage = 1;
        } else {
            $nPage = $this->input->post('nPageCurrent');
        }
        $nLangEdit = $this->session->userdata("tLangEdit");

        $aGetPmtBrandDtInTmpParams  = array(
            'tPmtGroupNameTmp' => $tPmtGroupNameTmp,
            'tPmtGroupTypeTmp' => $tPmtGroupTypeTmp,
            'tPmtGroupListTypeTmp' => $tPmtGroupListTypeTmp,
            'FNLngID' => $nLangEdit,
            'nPage' => $nPage,
            'nRow' => 500,
            'tSearchAll' => $tSearchAll,
            'tUserSessionID' => $tUserSessionID,
            'aPdtCond' => $aPdtCond
        );
        $aResList = $this->mPromotionStep1PmtBrandDt->FSaMGetPmtBrandDtInTmp($aGetPmtBrandDtInTmpParams);

        $aGetPmtBrandDtInAllTmpParams  = array(
            'FNLngID' => $nLangEdit,
            'tBchCodeLogin' => $tBchCodeLogin,
            'tUserSessionID' => $tUserSessionID,
            'tPmtGroupNameTmpOld' => $tPmtGroupNameTmpOld
        );
        $aGetPmtBrandDtInAllTmp = $this->mPromotionStep1PmtBrandDt->FSaMGetPmtBrandDtInAllTmp($aGetPmtBrandDtInAllTmpParams);

        $aNotIn = [];
        foreach ($aGetPmtBrandDtInAllTmp as $nIndex => $aGetPmtBrandDtInAllTmpItem) {
            $aNotIn[$nIndex][] = $aGetPmtBrandDtInAllTmpItem['FTPmdRefCode'];
            $aNotIn[$nIndex][] = $aGetPmtBrandDtInAllTmpItem['FTPmdBarCode'];
        }

        $aGenTable = array(
            'aAlwEvent' => $aAlwEvent,
            'aDataList' => $aResList,
            'nPage' => $nPage,
            'nOptDecimalShow' => $nOptDecimalShow,
            'aPdtCond' => $aPdtCond
        );
        $tHtml = $this->load->view('document/promotion/advance_table/wStep1PmtBrandDtTableTmp', $aGenTable, true);

        $aResponse = [
            'html' => $tHtml,
            'notIn' => $aNotIn
        ];

        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($aResponse));
    }

    /**
     * Functionality : Insert PmtBrandDt to Temp
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : Array
     */
    public function FSaCPromotionInsertPmtBrandDtToTmp()
    {
        $tPmtGroupNameTmp = $this->input->post('tPmtGroupNameTmp');
        $tPmtGroupNameTmpOld = $this->input->post('tPmtGroupNameTmpOld');
        $tPmtGroupTypeTmp = $this->input->post('tPmtGroupTypeTmp');
        $tPmtGroupListTypeTmp = $this->input->post('tPmtGroupListTypeTmp');
        $tBrandList = $this->input->post('tBrandList');
        $nLangEdit = $this->session->userdata("tLangEdit");
        $tUserSessionID = $this->session->userdata("tSesSessionID");
        $tUserSessionDate = $this->session->userdata("tSesSessionDate");
        $tUserLoginCode = $this->session->userdata("tSesUsername");
        $tUserLevel = $this->session->userdata('tSesUsrLevel');
        $tBchCodeLogin = $tUserLevel == 'HQ' ? FCNtGetBchInComp() : $this->session->userdata("tSesUsrBchCodeDefault");
        $tPmtTableName = $this->input->post('tTable');

        $this->db->trans_begin();

        $aClearPmtDtShopAllInTmpParams = [
            'tUserSessionID' => $tUserSessionID,
            'tPmtGroupNameTmpOld' => $tPmtGroupNameTmpOld
        ];
        $this->mPromotionStep1PmtDt->FSbClearPmtDtShopAllInTmp($aClearPmtDtShopAllInTmpParams);

        $aBrandList = json_decode($tBrandList, JSON_OBJECT_AS_ARRAY);
        if (isset($aBrandList) && is_array($aBrandList) && !empty($aBrandList)) {
            foreach ($aBrandList as $nKey => $aItem) {
                // $tBrandCode = json_decode($aItem)[0];
                // $tBrandName = json_decode($aItem)[1];
                $tBrandCode = $aItem[0];
                $tBrandName = $aItem[1];
                $aPmtBrandDtToTempParams = [
                    'tDocNo' => 'PMTDOCTEMP',
                    'tTable'        => $tPmtTableName,
                    'tPmtGroupNameTmp' => $tPmtGroupNameTmp,
                    'tPmtGroupNameTmpOld' => $tPmtGroupNameTmpOld,
                    'tPmtGroupTypeTmp' => $tPmtGroupTypeTmp,
                    'tPmtGroupListTypeTmp' => $tPmtGroupListTypeTmp,
                    'tBchCodeLogin' => $tBchCodeLogin,
                    'tUserSessionID' => $tUserSessionID,
                    'tUserSessionDate' => $tUserSessionDate,
                    'tUserLoginCode' => $tUserLoginCode,
                    'tBrandCode' => $tBrandCode,
                    'tBrandName' => $tBrandName,
                    'nLngID' => $nLangEdit
                ];
                $aResultToTmp = $this->mPromotionStep1PmtBrandDt->FSaMPmtBrandDtToTemp($aPmtBrandDtToTempParams);
                $aCheckStalotBrand[$nKey]['tPdtCode']   = $tBrandCode;
                $aCheckStalotBrand[$nKey]['nStaLot']    = $aResultToTmp['nStaLot'];
                $aCheckStalotBrand[$nKey]['nSeqno']     = $aResultToTmp['nSeqno'];
            }
        }
        if(!isset($aCheckStalotBrand)){
                $aCheckStalotBrand = '';
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $aReturn = array(
                'nStaEvent'    => '900',
                'tStaMessg'    => "Unsucess Add",
                'aStalot'    => $aCheckStalotBrand
            );
        } else {
            $this->db->trans_commit();
            $aReturn = array(
                'nStaEvent'    => '1',
                'tStaMessg' => 'Success Add',
                'aStalot'    => $aCheckStalotBrand
            );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($aReturn));
    }

        /**
     * Functionality : Insert PmtBrandDt to Temp
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : Array
     */
    public function FSaCPromotionInsertPmtPriDtToTmp()
    {
        $tPmtGroupNameTmp = $this->input->post('tPmtGroupNameTmp');
        $tPmtGroupNameTmpOld = $this->input->post('tPmtGroupNameTmpOld');
        $tPmtGroupTypeTmp = $this->input->post('tPmtGroupTypeTmp');
        $tPmtGroupListTypeTmp = $this->input->post('tPmtGroupListTypeTmp');
        $tBrandList = $this->input->post('tBrandList');
        $nLangEdit = $this->session->userdata("tLangEdit");
        $tUserSessionID = $this->session->userdata("tSesSessionID");
        $tUserSessionDate = $this->session->userdata("tSesSessionDate");
        $tUserLoginCode = $this->session->userdata("tSesUsername");
        $tUserLevel = $this->session->userdata('tSesUsrLevel');
        $tBchCodeLogin = $tUserLevel == 'HQ' ? FCNtGetBchInComp() : $this->session->userdata("tSesUsrBchCodeDefault");
        $tPmtTableName = $this->input->post('tTable');

        $this->db->trans_begin();

        $aClearPmtDtShopAllInTmpParams = [
            'tUserSessionID' => $tUserSessionID,
            'tPmtGroupNameTmpOld' => $tPmtGroupNameTmpOld
        ];
        $this->mPromotionStep1PmtDt->FSbClearPmtDtShopAllInTmp($aClearPmtDtShopAllInTmpParams);

        // $aBrandList = json_decode($tBrandList, JSON_OBJECT_AS_ARRAY);
        // print_r($aBrandList);


        // $tBrandCode = json_decode($aItem)[0];
        // $tBrandName = json_decode($aItem)[1];
        $tBrandCode = $tBrandList[0];
        $tBrandName = $tBrandList[1];
        $aPmtBrandDtToTempParams = [
            'tDocNo' => 'PMTDOCTEMP',
            'tTable'        => $tPmtTableName,
            'tPmtGroupNameTmp' => $tPmtGroupNameTmp,
            'tPmtGroupNameTmpOld' => $tPmtGroupNameTmpOld,
            'tPmtGroupTypeTmp' => $tPmtGroupTypeTmp,
            'tPmtGroupListTypeTmp' => $tPmtGroupListTypeTmp,
            'tBchCodeLogin' => $tBchCodeLogin,
            'tUserSessionID' => $tUserSessionID,
            'tUserSessionDate' => $tUserSessionDate,
            'tUserLoginCode' => $tUserLoginCode,
            'tBrandCode' => $tBrandCode,
            'tBrandName' => $tBrandName,
            'nLngID' => $nLangEdit
        ];
        $aResultToTmp = $this->mPromotionStep1PmtBrandDt->FSaMPmtBrandDtToTemp($aPmtBrandDtToTempParams);
        $aCheckStalotBrand['tPdtCode']   = $tBrandCode;
        $aCheckStalotBrand['nStaLot']    = $aResultToTmp['nStaLot'];
        $aCheckStalotBrand['nSeqno']     = $aResultToTmp['nSeqno'];
  

        if(!isset($aCheckStalotBrand)){
                $aCheckStalotBrand = '';
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $aReturn = array(
                'nStaEvent'    => '900',
                'tStaMessg'    => "Unsucess Add",
                'aStalot'    => $aCheckStalotBrand
            );
        } else {
            $this->db->trans_commit();
            $aReturn = array(
                'nStaEvent'    => '1',
                'tStaMessg' => 'Success Add',
                'aStalot'    => $aCheckStalotBrand
            );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($aReturn));
    }

    /**
     * Functionality : Update PmtBrandDt in Temp
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : -
     * Return Type : -
     */
    public function FSxCPromotionUpdatePmtBrandDtInTmp()
    {
        $tPmtGroupNameTmp = $this->input->post('tPmtGroupNameTmp');
        $tPmtGroupNameTmpOld = $this->input->post('tPmtGroupNameTmpOld');
        $tPmtGroupTypeTmp = $this->input->post('tPmtGroupTypeTmp');
        $tPmtGroupListTypeTmp = $this->input->post('tPmtGroupListTypeTmp');
        $tModelCode = $this->input->post('tModelCode');
        $tModelName = $this->input->post('tModelName');
        $nSeqNo = $this->input->post('nSeqNo');
        $tBchCode = $this->input->post('tBchCode');
        $tUserSessionID = $this->session->userdata("tSesSessionID");
        $tUserLoginCode = $this->session->userdata("tSesUsername");

        $this->db->trans_begin();

        $aUpdatePmtBrandDtInTmpBySeqParams = [
            'tPmtGroupNameTmp' => $tPmtGroupNameTmp,
            'tPmtGroupNameTmpOld' => $tPmtGroupNameTmpOld,
            'tPmtGroupTypeTmp' => $tPmtGroupTypeTmp,
            'tPmtGroupListTypeTmp' => $tPmtGroupListTypeTmp,
            'tModelCode' => $tModelCode,
            'tModelName' => $tModelName,
            'tUserLoginCode' => $tUserLoginCode,
            'tUserSessionID' => $tUserSessionID,
            'nSeqNo' => $nSeqNo,
        ];
        $this->mPromotionStep1PmtBrandDt->FSbUpdatePmtBrandDtInTmpBySeq($aUpdatePmtBrandDtInTmpBySeqParams);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $aReturn = array(
                'nStaEvent'    => '900',
                'tStaMessg'    => "Unsucess Update"
            );
        } else {
            $this->db->trans_commit();
            $aReturn = array(
                'nStaEvent'    => '1',
                'tStaMessg' => 'Success Update'
            );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($aReturn));
    }

    /**
     * Functionality : Show Modal Lot
     * Parameters : -
     * Creator : 03/11/2021 Off
     * Last Modified : -
     * Return : -
     * Return Type : -
     */
    public function FSxCPromotionGetLotBrandDetail(){
        $tPdtCode           = $this->input->post('tPdtCode');
        $nSeqno             = $this->input->post('nSeqno');
        $nRound             = $this->input->post('nRound');
        $nMaxRound          = $this->input->post('nMaxRound');
        $tTable             = $this->input->post('tTable');
        $tSubref            = $this->input->post('tSubref');
        
        $aWhereData     = [
            'FTXthDocKey'   => 'TCNMPdtLot',
            'FTSessionID'   => $this->session->userdata('tSesSessionID'),
            'tPdtCode'      => $tPdtCode[$nRound],
            'tTable'        => $tTable,
            'tSubref'       => $tSubref,
            'nSeqno'        => $nSeqno[$nRound]
        ];
        // Loop Insert DT Set temp
        // $this->Jobrequeststep1_model->FSaMJR1DeleteDTSetToTemp($aWhereData);
        // $this->Jobrequeststep1_model->FSaMJR1InsertDTSetToTemp($aWhereData);

        $aDataView  = [
            'aDataDTTmp'    => $this->mPromotionStep1PmtBrandDt->FSaMPmtBrandDtGetLotDetail($aWhereData),
            'aWhereData'    => $aWhereData,
            'nCountPDTLot'  => $nMaxRound,
            'nRound'        => $nRound,
            'tTable'        => $tTable,
            'tPdtCode'      => $tPdtCode,
            'nSeqno'        => $nSeqno,
        ];
        $this->load->view('document/promotion/advance_table/wStep1PmtPdtDtTableLotTmp',$aDataView);
        
    }
}
