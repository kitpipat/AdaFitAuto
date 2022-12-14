<?php 
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class APDebitnoteDisChgModal_controller extends MX_Controller {

    public function __construct() {
        parent::__construct ();
        $this->load->model('company/company/mCompany');
        $this->load->model('company/branch/mBranch');
        $this->load->model('company/shop/mShop');
        $this->load->model('payment/rate/mRate');
        $this->load->model('company/vatrate/mVatRate');
        $this->load->model('document/apdebitnote/APDebitnoteDisChgModal_model');
    }

    // Function : Function Call Data From Dis HD
    // Creator  : 09/03/2022 Wasin
    public function FSoCAPDDisChgHDList(){
        try{
            $tUserLevel         = $this->session->userdata('tSesUsrLevel');
            $tDocNo             = $this->input->post('oetAPDDocNo');
            $nSeqNo             = $this->input->post('tSeqNo');
            $tBchCode           = $this->input->post('tBchCode'); 
            $aAdvanceSearch     = $this->input->post('oAdvanceSearch');
            $nPage              = $this->input->post('nPageCurrent');
            $aAlwEvent          = FCNaHCheckAlwFunc('docAPDebitnote/0/0');
            // Get Option Show Decimal
            $nOptDecimalShow    = FCNxHGetOptionDecimalShow();
            // Page Current 
            if($nPage == '' || $nPage == null){$nPage = 1;}else{$nPage = $this->input->post('nPageCurrent');}
            // Lang ภาษา
            $nLangEdit          = $this->session->userdata("tLangEdit");
            // Data Conditon Get Data Document
            $aDataCondition     = [
                'FNLngID'           => $nLangEdit,
                'nPage'             => $nPage,
                'nRow'              => 10,
                'aAdvanceSearch'    => $aAdvanceSearch,
                'tDocNo'            => $tDocNo,  
                'nSeqNo'            => $nSeqNo,
                'tBchCode'          => $tBchCode,
                'tSessionID'        => $this->session->userdata('tSesSessionID')
            ];
            $aDataList      = $this->APDebitnoteDisChgModal_model->FSaMAPDGetDisChgHDList($aDataCondition);
            
            $aConfigView    = array(
                'nPage'             => $nPage,
                'nOptDecimalShow'   => $nOptDecimalShow,
                'aAlwEvent'         => $aAlwEvent,
                'aDataList'         => $aDataList,
            );
            $tPIViewDataTableList   = $this->load->view('document/apdebitnote/dis_chg/wAPDebitnoteDisChgHDList', $aConfigView, true);
            $aReturnData    = array(
                'tPIViewDataTableList'  => $tPIViewDataTableList,
                'nStaEvent'             => '1',
                'tStaMessg'             => 'Success'
            );
        }catch(Exception $Error){
            $aReturnData    = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    // Function : Function Call Data From Dis DT
    // Creator  : 09/03/2022 Wasin
    public function FSoCAPDDisChgDTList(){
        try{
            $tUserLevel         = $this->session->userdata('tSesUsrLevel');
            $tDocNo             = $this->input->post('oetAPDDocNo');
            $nSeqNo             = $this->input->post('tSeqNo');
            $tBchCode           = $this->input->post('tBchCode'); 
            $aAdvanceSearch     = $this->input->post('oAdvanceSearch');
            $nPage              = $this->input->post('nPageCurrent');
            $aAlwEvent          = FCNaHCheckAlwFunc('docAPDebitnote/0/0');
            // Get Option Show Decimal
            $nOptDecimalShow    = FCNxHGetOptionDecimalShow();
            // Page Current 
            if($nPage == '' || $nPage == null){$nPage = 1;}else{$nPage = $this->input->post('nPageCurrent');}
            // Lang ภาษา
            $nLangEdit          = $this->session->userdata("tLangEdit");
            
            // Data Conditon Get Data Document
            $aDataCondition  = array(
                'tDocNo'            => $tDocNo,  
                'nSeqNo'            => $nSeqNo,
                'tBchCode'          => $tBchCode,
                'FNLngID'           => $nLangEdit,
                'nPage'             => $nPage,
                'nRow'              => 10,
                'aAdvanceSearch'    => $aAdvanceSearch,
                'tSessionID'        => $this->session->userdata('tSesSessionID')
            );
            $aDataList      = $this->APDebitnoteDisChgModal_model->FSaMAPDGetDisChgDTList($aDataCondition);
            $aConfigView    = array(
                'nPage'             => $nPage,
                'nOptDecimalShow'   => $nOptDecimalShow,
                'aAlwEvent'         => $aAlwEvent,
                'aDataList'         => $aDataList,
            );
            $tPIViewDataTableList   = $this->load->view('document/apdebitnote/dis_chg/wAPDebitnoteDisChgDTList', $aConfigView, true);
            $aReturnData    = array(
                'tPIViewDataTableList'  => $tPIViewDataTableList,
                'nStaEvent'             => '1',
                'tStaMessg'             => 'Success'
            );
        }catch(Exception $Error){
            $aReturnData    = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    // Function : วาด Modal DTDis HTML ส่วนลดรายการ
    // Creator  : 09/03/2022 Wasin
    public function FSvCAPDGetDTDisTableData(){
        $tUserLevel = $this->session->userdata('tSesUsrLevel');
        $tDocNo     = $this->input->post('tDocNo');
        $tBchCode   = $tUserLevel == 'HQ' ? FCNtGetBchInComp() : $this->session->userdata("tSesUsrBchCode");
        $nKey       = $this->input->post('nKey');
        $nPdtCode   = $this->input->post('nPdtCode');
        $nPunCode   = $this->input->post('nPunCode');
        $nSeqNo     = $this->input->post('nSeqNo');
        // คำนวนใน File ใหม่
        $this->FCNoAPDProcessCalculaterInFile($tDocNo);
        // Get Data From File
        $aDataFile  = $this->FMaAPDGetDataFormFile($tDocNo);
        // Get Option Show Decimal
        $nOptDecimalShow = FCNxHGetOptionDecimalShow(); 
        $aData['nOptDecimalShow']   = $nOptDecimalShow;
        $aData['nKey']              = $nKey;
        $aData['aDataFile']         =  $aDataFile['DTData'];
        $aData['nXpdSeqNo']         = $aDataFile['DTData'][$nKey]['FNXpdSeqNo'];
        $aData['cXpdSetPrice']      = $aDataFile['DTData'][$nKey]['FCXpdSetPrice'];
        $aData['cXpdDisChgAvi']     = $aDataFile['DTData'][$nKey]['FCXpdDisChgAvi'];
        $aData['aDTDiscount']       = $aDataFile['DTData'][$nKey]['DTDiscount'];
        $aData['nPdtCode']          = $nPdtCode;
        $aData['nPunCode']          = $nPunCode;
        $aData['nSeqNo']            = $nSeqNo;
        $this->load->view('document/apdebitnote/advancetable/wAPDebitnoteDTDisTableData', $aData);
    }


    // Function : วาด Modal HDDis HTML ส่วนลดท้ายบิล
    // Creator  : 09/03/2022 Wasin
    public function FSvCAPDGetHDDisTableData(){
        $tXthDocNo      = $this->input->post('tXthDocNo');
        $nXthVATInOrEx  = $this->input->post('nXthVATInOrEx');
        $nXthRefAEAmt   = $this->input->post('nXthRefAEAmt');
        $nXthVATRate    = $this->input->post('nXthVATRate');
        $nXthWpTax      = $this->input->post('nXthWpTax');
        // คำนวนใน File ใหม่ ก่อนดึงไฟล์
        $this->FCNoAPDProcessCalculaterInFile($tXthDocNo); 
        // Get Data From File
        $aDataFile = $this->FMaAPDGetDataFormFile($tXthDocNo);
        // Get Option Show Decimal
        $nOptDecimalShow = FCNxHGetOptionDecimalShow(); 
        $cXthTotal = 0;
        // ยอดรวมก่อนลด SUM(DT.FCXpdNet)
        foreach($aDataFile['DTData'] AS $DTKey => $DTValue){
            $cXthTotal = $cXthTotal+$DTValue['FCXpdNet'];
        }
        $aData['nOptDecimalShow']   = $nOptDecimalShow;
        $aData['aDataFile']         = $aDataFile;
        $aData['cXthTotal']         = $cXthTotal;
        $aData['nXthVATInOrEx']     = $nXthVATInOrEx;
        $aData['cXthRefAEAmt']      = $nXthRefAEAmt;
        $aData['nXthVATRate']       = $nXthVATRate;
        $aData['nXthWpTax']         = $nXthWpTax;
        $this->load->view('document/apdebitnote/advancetable/wAPDebitnoteHDDisTableData',$aData);
    }
    
    // Function : แก้ไข ส่วนลด ท้ายบิล
    // Creator  : 09/03/2022 Wasin
    public function FSvCAPDAddEditHDDis(){
        
        $tDocNo         = $this->input->post('tDocNo');
        $tSplVatType    = $this->input->post('tSplVatType');
        $tUserLevel     = $this->session->userdata('tSesUsrLevel');
        $tBchCode       = $this->input->post('tBchCode');
        $tDisChgItems   = $this->input->post('tDisChgItems');
        $tDisChgSummary = $this->input->post('tDisChgSummary');
        $aDisChgItems   = json_decode($tDisChgItems, true);
        $aDisChgSummary = json_decode($tDisChgSummary, true);
        
        $aParams = array(
            'tDocNo'        => $tDocNo,  
            'tBchCode'      => $tBchCode,
            'nLngID'        => $this->session->userdata("tLangID"), // รหัสภาษาที่ login
            'tSessionID'    => $this->session->userdata('tSesSessionID'),
            'aDisChgSummary' => $aDisChgSummary
        );
        /*==================== Begin DB Process ==============================*/
        
        $this->APDebitnoteDisChgModal_model->FSaMAPDDeleteHDDisTemp($aParams);
        
        $this->db->trans_begin();
        if(!empty($aDisChgItems)){
            foreach ($aDisChgItems as $key => $item) {
                $this->APDebitnoteDisChgModal_model->FSaMAPDAddEditHDDisTemp($aParams, $item);
            }

            // Prorat Call
            $aResProrat = FCNaHCalculateProrate('TAPTPcHD', $tDocNo);
            
            $aCalcDTParams = [
                'tDataDocEvnCall' => '',
                'tDataVatInOrEx' => $tSplVatType,
                'tDataDocNo' => $tDocNo,
                'tDataDocKey' => 'TAPTPcHD',
                'tDataSeqNo' => ''
            ];
            FCNbHCallCalcDocDTTemp($aCalcDTParams);
        }
        
        /*==================== End DB Process ================================*/
        
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $aReturn = array(
                'nStaEvent'    => '900',
                'tStaMessg'    => "Unsucess process"
            );
        }else{
            $this->db->trans_commit();
            $aReturn = array(
                'nStaEvent' => '1',
                'tStaMessg' => 'Success process'
            );
        }
        // $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($aReturn));
    }
    
    // Function : แก้ไข ส่วนลด รายการ
    public function FSvCAPDAddEditDTDis(){
        $tUserLevel     = $this->session->userdata('tSesUsrLevel');
        $tDocNo         = $this->input->post('tDocNo');
        $tSplVatType    = $this->input->post('tSplVatType');
        $nSeqNo         = $this->input->post('tSeqNo');
        $tSessionID     = $this->session->userdata('tSesSessionID');
        $tBchCode       = $this->input->post('tBchCode');
        $tDisChgItems   = $this->input->post('tDisChgItems');
        $tDisChgSummary = $this->input->post('tDisChgSummary');
        $aDisChgItems   = json_decode($tDisChgItems, true);
        $aDisChgSummary = json_decode($tDisChgSummary, true);
        
        $this->db->trans_begin();
        
        $aParams = array(
            'nStaDis'           => 1,
            'tDocNo'            => $tDocNo,  
            'nSeqNo'            => $nSeqNo,
            'tBchCode'          => $tBchCode,
            'nLngID'            => $this->session->userdata("tLangID"),
            'tSessionID'        => $tSessionID,
            'tSplVatType'       => $tSplVatType,
            'aDisChgSummary'    => $aDisChgSummary
        );
        
        $this->APDebitnoteDisChgModal_model->FSaMAPDClearDisChgTxtDTTemp($aParams);
        $this->APDebitnoteDisChgModal_model->FSaMAPDDeleteDTDisTemp($aParams);
        
        if(!empty($aDisChgItems)){
            $aInsertDTDisTmp    =   array();
            foreach ($aDisChgItems as $key => $item) {
                array_push($aInsertDTDisTmp,array(
                    'FTBchCode'         => $tBchCode,
                    'FTXthDocNo'        => $tDocNo,
                    'FNXtdSeqNo'        => $item['nSeqNo'],
                    'FDXtdDateIns'      => date('Y-m-d H:i:s',strtotime($item['tCreatedAt'])),
                    'FTXtdDisChgTxt'    => $item['tDisChgTxt'],
                    'FNXtdStaDis'       => $item['tStaDis'],
                    'FTXtdDisChgType'   => $item['nDisChgType'],
                    'FCXtdNet'          => $item['cAfterDisChg'],
                    'FCXtdValue'        => $item['cDisChgValue'],
                    'FTSessionID'       => $tSessionID,
                    'FDLastUpdOn'       => date('Y-m-d h:i:s'),
                    'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                    'FDCreateOn'        => date('Y-m-d h:i:s'),
                    'FTCreateBy'        => $this->session->userdata('tSesUsername')
                ));

                $this->APDebitnoteDisChgModal_model->FSaMAPDAddEditDTDisTemp($aParams, $item);

                //Update TCNTDocDTTmp - supawat 27/02/2021
                $this->APDebitnoteDisChgModal_model->FSaMPCUpdateDTDisInTemp($aInsertDTDisTmp);
            }
        }
        
        // Prorat Call
        FCNaHCalculateProrate('TAPTPcHD', $tDocNo);

        $aCalcDTParams = [
            'tDataDocEvnCall'   => '',
            'tDataVatInOrEx'    => $tSplVatType,
            'tDataDocNo'        => $tDocNo,
            'tDataDocKey'       => 'TAPTPcHD',
            'tDataSeqNo'        => ''
        ];
        FCNbHCallCalcDocDTTemp($aCalcDTParams);
        
        /*==================== End DB Process ================================*/
        
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $aReturn = array(
                'nStaEvent'    => '900',
                'tStaMessg'    => "Unsucess process"
            );
        }else{
            $this->db->trans_commit();
            $aReturn = array(
                'nStaEvent' => '1',
                'tStaMessg' => 'Success process'
            );
        }
        //$this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($aReturn));
    }
    
    //หาส่วนลด
    public function FSaCCENGetPriceAlwDiscount(){
        $tDocno         = $this->input->post('tDocno');
        $tBCHCode       = ($this->input->post('tBCHCode') == '' ) ? FCNtGetBchInComp() : $this->input->post('tBCHCode');
        $tSesstion      = $this->session->userdata('tSesSessionID');

        $aWhere = array(
            'tDocno'        => $tDocno,
            'tBCHCode'      => $tBCHCode,
            'tSessionID'    => $tSesstion
        );
        
        $nTotal = $this->APDebitnoteDisChgModal_model->FSaMCENGetPriceAlwDiscount($aWhere);
        $aTotal = array(
            'nTotal' => $nTotal['Total']
        );
        echo json_encode($aTotal);
    }
}