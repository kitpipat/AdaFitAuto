<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class cAdjustmentcost extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('company/company/mCompany');
        $this->load->model('document/adjustmentcost/mAdjustmentcost');
        // Test XSS Load Helper Security
        $this->load->helper("security");
        if ($this->security->xss_clean($this->input->post(), TRUE) === FALSE){
            echo "ERROR XSS Filter";
        }
    }

    public function index($nBrowseType,$tBrowseOption){
        $aDataConfigView    = array(
            'nBrowseType'       => $nBrowseType,    // nBrowseType สถานะการเข้าเมนู 0 :เข้ามาจากการกด Menu / 1 : เข้ามาจากการเพิ่มข้อมูลจาก Modal Browse ข้อมูล
            'tBrowseOption'     => $tBrowseOption,  //
            'aAlwEvent'         => FCNaHCheckAlwFunc('docADCCost/0/0'), // Controle Event
            'vBtnSave'          => FCNaHBtnSaveActiveHTML('docADCCost/0/0'), // Load Html ของปุ่ม Save ที่เก็บ Session ปัจจุบัน
            'nOptDecimalShow'   => FCNxHGetOptionDecimalShow(), // Setting Config การโชว์จำนวนเลขทศนิยม
            'nOptDecimalSave'   => FCNxHGetOptionDecimalSave() // Setting Config การ Saveจ ำนวนเลขทศนิยม
        );
        $this->load->view('document/adjustmentcost/wAdjustmentcost',$aDataConfigView);
    }


    // Functionality : Function Call Page From Search List
    // Parameters : Ajax and Function Parameter
    // Creator : 23/02/2021 Sooksanti(Nont)
    // Return : String View
    // ReturnType : View
    public function FSvCADCFormSearchList(){
        $this->load->view('document/adjustmentcost/wAdjustmentcostFormSearchList');
    }


    // Functionality : Function Call Page Data Table
    // Parameters : Ajax and Function Parameter
    // Creator : 06/06/2019 wasin (Yoshi)
    // Return : Object View Data Table
    // ReturnType : object
    public function FSoCASTDataTable(){
        try{
            $aAdvanceSearch     = $this->input->post('oAdvanceSearch');
            $nPage              = $this->input->post('nPageCurrent');

            // Controle Event
            $aAlwEvent          = FCNaHCheckAlwFunc('docADCCost/0/0');

            // Get Option Show Decimal
            $nOptDecimalShow    = FCNxHGetOptionDecimalShow();

            // Page Current
            if($nPage == '' || $nPage == null){$nPage = 1;}else{$nPage = $this->input->post('nPageCurrent');}

            // Lang ภาษา
            $nLangEdit          = $this->session->userdata("tLangEdit");

            // Data Conditon Get Data Document
            $aDataCondition  = array(
                'FNLngID'           => $nLangEdit,
                'nPage'             => $nPage,
                'nRow'              => 10,
                'aAdvanceSearch'    => $aAdvanceSearch
            );

            $aDataList  = $this->mAdjustmentcost->FSaMADCGetDataTable($aDataCondition);

            $aConfigView    = array(
                'nPage'             => $nPage,
                'nOptDecimalShow'   => $nOptDecimalShow,
                'aAlwEvent'         => $aAlwEvent,
                'aDataList'         => $aDataList,
            );

            $tASTViewDataTable = $this->load->view('document/adjustmentcost/wAdjustmentcostDataTable',$aConfigView,true);
            $aReturnData = array(
                'tViewDataTable'    => $tASTViewDataTable,
                'nStaEvent'         => '1',
                'tStaMessg'         => 'Success'
            );
        }catch(Exception $Error){
            $aReturnData    = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }


    // Functionality : Function Call Page Add Adjust Cost
    // Parameters : Ajax and Function Parameter
    // Creator : 24/02/2021 Sooksanti(Nont)
    // Return : String View
    // ReturnType : View
    public function FSvCADCAddPage(){
        try{

        // Get Option Show Decimal
        $nOptDecimalShow    = FCNxHGetOptionDecimalShow();

        //Lang ภาษา
        $nLangEdit          = $this->session->userdata("tLangEdit");
        $aDataWhere = array(
            'FNLngID'   => $nLangEdit
        );
        $tAPIReq    = "";
        $tMethodReq = "GET";
        $aResList   = $this->mCompany->FSaMCMPList($tAPIReq, $tMethodReq, $aDataWhere);

        $aDataConfigViewAdd = array(
            'nOptDecimalShow'   => $nOptDecimalShow,
            'aDataDocHD'        => array('rtCode'=>'99')
        );

        $aData = array(
            'nStaAddOrEdit'   => 99,
            'FTXthDocKey'     => 'TCNTPdtAdjCostHD',
            'FTSessionID'     => $this->session->userdata('tSesSessionID'),
            'aResList'        => $aResList,
            'tBchCompCode'    => $this->session->userdata("tSesUsrBchCodeDefault"),
            'tBchCompName'    => $this->session->userdata("tSesUsrBchNameDefault")
        );
        $this->mAdjustmentcost->FSaMAdDelPdtTmp($aData);


        $tViewPageAdd = $this->load->view('document/adjustmentcost/wAdjustmentcostAdd',$aDataConfigViewAdd,true);

        $aReturnData        = array(
            'tViewPageAdd'      => $tViewPageAdd,
            'nStaEvent'         => '1',
            'tStaMessg'         => 'Success'
        );
        }catch(Exception $Error){
            $aReturnData = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }


    // Functionality : Function Call Page Add Adjust Stock
    // Parameters : Ajax and Function Parameter
    // Creator : 24/02/2021 Sooksanti(Nont)
    // Return : String View
    // ReturnType : View
    public function FSvCADCEditPage(){
        try{
            $tXchDocNo          = $this->input->post('ptXchDocNo');
            $pnTypeAdc          = $this->input->post('pnTypeAdc');
        // Get Option Show Decimal
        $nOptDecimalShow    = FCNxHGetOptionDecimalShow();

        //Lang ภาษา
        $nLangEdit          = $this->session->userdata("tLangEdit");

        $aDataDoc   = array(
            'FTXphDocNo'    => $tXchDocNo,
            'FTXthDocKey'   => 'TCNTPdtAdjCostHD',
            'FTSessionID'   => $this->session->userdata('tSesSessionID'),
            'FDLastUpdOn'   => date('Y-m-d'),
            'FDCreateOn'    => date('Y-m-d'),
            'FTLastUpdBy'   => $this->session->userdata('tSesUsername'),
            'FTCreateBy'    => $this->session->userdata('tSesUsername')
        );
        $oDelTmp    = $this->mAdjustmentcost->FSaMAdDelPdtTmp($aDataDoc);
        $oDTtoTmp   = $this->mAdjustmentcost->FSoMADCDTtoTmp($aDataDoc);
        //Data Master
        $aDataWhere  = array(
            'FTXchDocNo'    => $tXchDocNo,
            'FNLngID'       => $nLangEdit,
            'nRow'          => 10000,
            'nPage'         => 1,
            'FNXchDocType'  => $pnTypeAdc,
            'FTXchDocKey'   => 'TCNTPdtAdjCostHD',
        );

        $aResult    = $this->mAdjustmentcost->FSaMADCGetHD($aDataWhere);  //TCNTPdtAdjCostHD

        $aDataConfigViewAdd = array(
            'nOptDecimalShow'   => $nOptDecimalShow,
            'aDataDocHD'        => $aResult,
            'oDelTmp'           => $oDelTmp,
            'oDTtoTmp'          => $oDTtoTmp,
        );

        $tViewPageAdd = $this->load->view('document/adjustmentcost/wAdjustmentcostAdd',$aDataConfigViewAdd,true);

        $aReturnData        = array(
            'tViewPageAdd'      => $tViewPageAdd,
            'nStaEvent'         => '1',
            'tStaMessg'         => 'Success'
        );
        }catch(Exception $Error){
            $aReturnData = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }
    // Functionality : Get PDT From Doc
    // Parameters : Ajax and Function Parameter
    // Creator : 25/02/2021 Sooksanti(Nont)
    // Return :
    // Return Type : object
    public function FSoCADCGetPdtFromDoc(){
        try{
            $tTable         = $this->input->post('tTable');
            $tDocNo         = $this->input->post('tDocNo');
            $tPdtCodeDup    = $this->input->post('tPdtCodeDup');
            $tBchCode       = $this->input->post('tBchCode');
            $nLangEdit      = $this->session->userdata("tLangEdit");
            $tSession       = $this->session->userdata('tSesSessionID');

            $aParams = array(
                'tTable' => $tTable,
                'tDocNo' => $tDocNo,
                'tPdtCodeDup' => $tPdtCodeDup,
                'FNLngID' => $nLangEdit,
            );

            $aData = $this->mAdjustmentcost->FSaMADCGetPdtFromDoc($aParams);
            // print_r($aData);

            if($aData['rtCode'] == '1') {
                for ($i = 0; $i < FCNnHSizeOf($aData['raItems']); $i++) {
                    $nSeq = $i + 1;
                    $aDataEdit = array(
                        'FNXtdSeqNo'            => $nSeq,
                        'FTBchCode'             => $tBchCode,
                        'FTXthDocNo'            => $tDocNo,
                        'FTXthDocKey'           => 'TCNTPdtAdjCostHD',
                        'FCXtdQtyOrd'           => empty($aData['raItems'][$i]['FCXcdDiff']) ? 0 : $aData['raItems'][$i]['FCXcdDiff'],
                        'FCXtdAmt'              => empty($aData['raItems'][$i]['FCXcdCostNew']) ? 0 : $aData['raItems'][$i]['FCXcdCostNew'],
                        'FTPdtCode'             => $aData['raItems'][$i]['FTPdtCode'],
                        'FTPdtName'             => $aData['raItems'][$i]['FTPdtName'],
                        'FTPunName'             => empty($aData['raItems'][$i]['FTPunName']) ? '' : $aData['raItems'][$i]['FTPunName'],
                        'FCXtdVatRate'          => empty($aData['raItems'][$i]['FCPdtCostStd']) ? 0 : $aData['raItems'][$i]['FCPdtCostStd'],
                        'FCXtdQty'              => empty($aData['raItems'][$i]['FCPdtCostEx']) ? 0 : $aData['raItems'][$i]['FCPdtCostEx'],
                        'FTPunCode'             => empty($aData['raItems'][$i]['FTPunCode']) ? '' : $aData['raItems'][$i]['FTPunCode'],
                        'FCXtdFactor'           => empty($aData['raItems'][$i]['FCXcdFactor']) ? 0 : $aData['raItems'][$i]['FCXcdFactor'],
                        'FTXtdBarCode'          => empty($aData['raItems'][$i]['FTXcdBarScan']) ? '' : $aData['raItems'][$i]['FTXcdBarScan'],
                        'FTSessionID'           => $tSession,
                        'FDLastUpdOn'           => date('Y-m-d'),
                        'FDCreateOn'            => date('Y-m-d'),
                        'FTLastUpdBy'           => $this->session->userdata('tSesUsername'),
                        'FTCreateBy'            => $this->session->userdata('tSesUsername')
                    );
                    $aCheckTmpDup = $this->mAdjustmentcost->FSaMSPACheckDataTempDuplicate($aDataEdit); //check data duplicate
                    // insert data to table doctmp if not have items
                    if ($aCheckTmpDup == FALSE) {
                        $this->mAdjustmentcost->FSaMSPAAddPdtDocTmp($aDataEdit);
                    }
                }
            }

            if ($aData['rtCode'] == '1') {
                $aReturnData = array(
                    'aData' => $aData['raItems'],
                    'nStaEvent' => $aData['rtCode'],
                    'tStaMessg' => $aData['rtDesc'],
                );
            } else {
                $aReturnData = array(
                    'nStaEvent' => $aData['rtCode'],
                    'tStaMessg' => $aData['rtDesc'],
                );
            }
        }catch(Exception $Error){
            $aReturnData    = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    // Functionality : Get PDT From PDT Code
    // Parameters : Ajax and Function Parameter
    // Creator : 19/11/2021 Off
    // Return :
    // Return Type : object
    public function FSoCADCGetPdtFromPdtCode(){
        try{
            $aPdtDetail     = $this->input->post();
            $FTPdtCode      = $aPdtDetail['aProduct'][0]['pnPdtCode'];
            $FTBarCode      = $aPdtDetail['aProduct'][0]['ptBarCode'];
            $tDocNo         = $this->input->post('tDocNo');
            $tBchCode       = $this->input->post('tBchCode');
            $nLangEdit      = $this->session->userdata("tLangEdit");
            $tSession       = $this->session->userdata('tSesSessionID');

            $aDataGetSeq = array(
                'FTXthDocNo'    => $tDocNo,
                'FTXthDocKey'   => 'TCNTPdtAdjCostHD',
                'FTSessionID'   => $tSession
            );
            $aGetSeq = $this->mAdjustmentcost->FSaMSPACheckDataSeq($aDataGetSeq);
            $nSeq    = $aGetSeq[0]['nSeq'];

            $aParams = array(
                'tBchCode'  => $tBchCode,
                'FTPdtCode' => $FTPdtCode,
                'FTBarCode' => $FTBarCode,
                'FNLngID'   => $nLangEdit,
            );

            // print_r($aParams);
            $aData = $this->mAdjustmentcost->FSaMADCGetPdtFromPdtCode($aParams);
            // print_r($aData);exit;
            if ($aData['rtCode'] == '1') {
                $aDataEdit = array(
                    'FNXtdSeqNo'            => $nSeq+1,
                    'FTBchCode'             => $tBchCode,
                    'FTXthDocNo'            => $tDocNo,
                    'FTXthDocKey'           => 'TCNTPdtAdjCostHD',
                    'FCXtdQtyOrd'           => $aData['raItems'][0]['FCXcdDiff'],
                    'FCXtdAmt'              => $aData['raItems'][0]['FCXcdCostNew'],
                    'FTPdtCode'             => $aData['raItems'][0]['FTPdtCode'],
                    'FTPdtName'             => $aData['raItems'][0]['FTPdtName'],
                    'FTPunName'             => $aData['raItems'][0]['FTPunName'],
                    'FCXtdVatRate'          => $aData['raItems'][0]['FCPdtCostStd'],
                    'FCXtdQty'              => $aData['raItems'][0]['FCPdtCostEx'],
                    'FTPunCode'             => $aData['raItems'][0]['FTPunCode'],
                    'FCXtdFactor'           => $aData['raItems'][0]['FCXcdFactor'],
                    'FTXtdBarCode'          => $aData['raItems'][0]['FTXcdBarScan'],
                    'FTXtdPdtParent'        => $aData['raItems'][0]['FTBarCode'],
                    'FTSessionID'           => $tSession,
                    'FDLastUpdOn'           => date('Y-m-d'),
                    'FDCreateOn'            => date('Y-m-d'),
                    'FTLastUpdBy'           => $this->session->userdata('tSesUsername'),
                    'FTCreateBy'            => $this->session->userdata('tSesUsername')
                );

                $aCheckTmpDup = $this->mAdjustmentcost->FSaMSPACheckDataTempDuplicate($aDataEdit);

                if ($aCheckTmpDup == FALSE) {
                    $this->mAdjustmentcost->FSaMSPAAddPdtDocTmp($aDataEdit);
                }
            
                $aReturnData = array(
                    'aData' => $aData['raItems'],
                    'nStaEvent' => $aData['rtCode'],
                    'tStaMessg' => $aData['rtDesc'],
                );
            } else {
                $aReturnData = array(
                    'nStaEvent' => $aData['rtCode'],
                    'tStaMessg' => $aData['rtDesc'],
                );
            }
        }catch(Exception $Error){
            $aReturnData    = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    // Functionality : Get PDT From Filter
    // Parameters : Ajax and Function Parameter
    // Creator : 25/02/2021 Sooksanti(Nont)
    // Return :
    // Return Type : object
    public function FSoCADCGetPdtFromFilter(){
        try{
            $tBchCode           = $this->input->post('tFTBchCode');
            $tDocNo             = $this->input->post('tFTXthDocNo');
            $tPdtCodeFrom       = $this->input->post('tPdtCodeFrom');
            $tPdtCodeTo         = $this->input->post('tPdtCodeTo');
            $tBarCodeFrom       = $this->input->post('tBarCodeFrom');
            $tBarCodeCodeTo     = $this->input->post('tBarCodeCodeTo');
            $tPdtCodeDup        = $this->input->post('tPdtCodeDup');
            $tBchCode           = $this->input->post('tBchCode');
            $nLangEdit          = $this->session->userdata("tLangEdit");
            $tSession           = $this->session->userdata('tSesSessionID');

            $aDataGetSeq = array(
                'FTXthDocNo'    => $tDocNo,
                'FTXthDocKey'   => 'TCNTPdtAdjCostHD',
                'FTSessionID'   => $tSession
            );
            $aParams = array(
                'tPdtCodeFrom'      => $tPdtCodeFrom,
                'tPdtCodeTo'        => $tPdtCodeTo,
                'tBarCodeFrom'      => $tBarCodeFrom,
                'tBarCodeCodeTo'    => $tBarCodeCodeTo,
                'tPdtCodeDup'       => $tPdtCodeDup,
                'tBchCode'          => $tBchCode,
                'FNLngID'           => $nLangEdit,
            );
            $aGetSeq = $this->mAdjustmentcost->FSaMSPACheckDataSeq($aDataGetSeq);


            $aData = $this->mAdjustmentcost->FSaMADCGetPdtFromFilter($aParams);
            $nNumData    = FCNnHSizeOf($aData['raItems']);
            
            $nSeq    = $aGetSeq[0]['nSeq'];
            if($aData['rtCode'] == '1') {
                for ($i = 0; $i < $nNumData; $i++) {
                    $nSeq = $nSeq + 1;
                    $aDataEdit = array(
                        'FNXtdSeqNo'            => $nSeq,
                        'FTBchCode'             => $tBchCode,
                        'FTXthDocNo'            => $tDocNo,
                        'FTXthDocKey'           => 'TCNTPdtAdjCostHD',
                        'FCXtdQtyOrd'           => $aData['raItems'][$i]['FCXcdDiff'],
                        'FCXtdAmt'              => $aData['raItems'][$i]['FCXcdCostNew'],
                        'FTPdtCode'             => $aData['raItems'][$i]['FTPdtCode'],
                        'FTPdtName'             => $aData['raItems'][$i]['FTPdtName'],
                        'FTPunName'             => $aData['raItems'][$i]['FTPunName'],
                        'FCXtdVatRate'          => $aData['raItems'][$i]['FCPdtCostStd'],
                        'FCXtdQty'              => $aData['raItems'][$i]['FCPdtCostEx'],
                        'FTPunCode'             => $aData['raItems'][$i]['FTPunCode'],
                        'FCXtdFactor'           => $aData['raItems'][$i]['FCXcdFactor'],
                        'FTXtdBarCode'          => $aData['raItems'][$i]['FTXcdBarScan'],
                        'FTXtdPdtParent'        => $aData['raItems'][$i]['FTBarCode'],
                        'FTSessionID'           => $tSession,
                        'FDLastUpdOn'           => date('Y-m-d H:i:s'),
                        'FDCreateOn'            => date('Y-m-d H:i:s'),
                        'FTLastUpdBy'           => $this->session->userdata('tSesUsername'),
                        'FTCreateBy'            => $this->session->userdata('tSesUsername')
                    );
                    $aCheckTmpDup = $this->mAdjustmentcost->FSaMSPACheckDataTempDuplicate($aDataEdit); //check data duplicate
                    // insert data to table doctmp if not have items
                    if ($aCheckTmpDup == FALSE) {
                        $this->mAdjustmentcost->FSaMSPAAddPdtDocTmp($aDataEdit);
                    }
                }
            }
            if ($aData['rtCode'] == '1') {
                $aReturnData = array(
                    'aData' => $aData['raItems'],
                    'nStaEvent' => $aData['rtCode'],
                    'tStaMessg' => $aData['rtDesc'],
                );
            } else {
                $aReturnData = array(
                    'nStaEvent' => $aData['rtCode'],
                    'tStaMessg' => $aData['rtDesc'],
                );
            }
        }catch(Exception $Error){
            $aReturnData    = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }


    // Functionality : Get PDT From Filter
    // Parameters : Ajax and Function Parameter
    // Creator : 25/02/2021 Sooksanti(Nont)
    // Return :
    // Return Type : object
    public function FSoCADCGetPdtFromImportExcel(){
        try{
            $tPdtCodeDup    = $this->input->post('tPdtCodeDup');
            $nLangEdit      = $this->session->userdata("tLangEdit");
            $tBchCode       = $this->input->post('tBchCode');
            $tDocNo         = $this->input->post('tDocNo');
            $tSession       = $this->session->userdata('tSesSessionID');

            $aDataGetSeq = array(
                'FTXthDocNo'    => $tDocNo,
                'FTXthDocKey'   => 'TCNTPdtAdjCostHD',
                'FTSessionID'   => $tSession
            );
            $aParams = array(
                'tPdtCodeDup' => $tPdtCodeDup,
                'FTSessionID'   => $this->session->userdata('tSesSessionID'),
                'FNLngID' => $nLangEdit,
            );

            $aGetSeq    = $this->mAdjustmentcost->FSaMSPACheckDataSeq($aDataGetSeq);
            $aData      = $this->mAdjustmentcost->FSaMADCGetPdtFromImportExcel($aParams);

            // print_r($aData);
            $nNumData    = FCNnHSizeOf($aData['raItems']);

            $nSeq    = $aGetSeq[0]['nSeq'];
            if($aData['rtCode'] == '1') {
               //print_r($aData['raItems']);
                for ($i = 0; $i < $nNumData; $i++) {
                    $nSeq = $nSeq + 1;
                    $aDataEdit = array(
                        'FNXtdSeqNo'            => $nSeq,
                        'FTBchCode'             => $tBchCode,
                        'FTXthDocNo'            => $tDocNo,
                        'FTXthDocKey'           => 'TCNTPdtAdjCostHD',
                        'FCXtdQtyOrd'           => (empty($aData['raItems'][$i]['FCXcdDiff'])) ? 0 : (int)$aData['raItems'][$i]['FCXcdDiff'],
                        'FCXtdAmt'              => (empty($aData['raItems'][$i]['FCXcdCostNew'])) ? 0 : (int)$aData['raItems'][$i]['FCXcdCostNew'],
                        'FTPdtCode'             => $aData['raItems'][$i]['FTPdtCode'],
                        'FTPdtName'             => $aData['raItems'][$i]['FTPdtName'],
                        'FTPunName'             => $aData['raItems'][$i]['FTPunName'],
                        'FCXtdVatRate'          => (empty($aData['raItems'][$i]['FCPdtCostStd'])) ? 0 : (int)$aData['raItems'][$i]['FCPdtCostStd'],
                        'FCXtdQty'              => (empty($aData['raItems'][$i]['FCPdtCostEx'])) ? 0 : (int)$aData['raItems'][$i]['FCPdtCostEx'],
                        'FTPunCode'             => $aData['raItems'][$i]['FTPunCode'],
                        'FCXtdFactor'           => (empty($aData['raItems'][$i]['FCXcdFactor'])) ? 0 : (int)$aData['raItems'][$i]['FCXcdFactor'],
                        'FTXtdBarCode'          => (empty($aData['raItems'][$i]['FTXcdBarScan'])) ? '' : $aData['raItems'][$i]['FTXcdBarScan'],
                        'FTSessionID'           => $tSession,
                        'FDLastUpdOn'           => date('Y-m-d H:i:s'),
                        'FDCreateOn'            => date('Y-m-d H:i:s'),
                        'FTLastUpdBy'           => $this->session->userdata('tSesUsername'),
                        'FTCreateBy'            => $this->session->userdata('tSesUsername')

                    );
                    $aCheckTmpDup = $this->mAdjustmentcost->FSaMSPACheckDataTempDuplicate($aDataEdit); //check data duplicate
                    // insert data to table doctmp if not have items
                    if ($aCheckTmpDup == FALSE) {
                        // $this->mAdjustmentcost->FSaMSPAAddPdtDocTmp($aDataEdit);
                        $this->mAdjustmentcost->FSaMSPAUpdatePdtDocTmp($aDataEdit);
                    }
                    //print_r($aDataEdit);
                }
            }

            if ($aData['rtCode'] == '1') {
                $aReturnData = array(
                    'aData' => $aData['raItems'],
                    'nStaEvent' => $aData['rtCode'],
                    'tStaMessg' => $aData['rtDesc'],
                );
            } else {
                $aReturnData = array(
                    'nStaEvent' => $aData['rtCode'],
                    'tStaMessg' => $aData['rtDesc'],
                );
            }
        }catch(Exception $Error){
            $aReturnData    = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    // Functionality : Event Add
    // Parameters : Ajax and Function Parameter
    // Creator : 03/03/2021 Sooksanti(Nont)
    // Return :
    // Return Type : object
    public function FSoCADCEventAdd(){
        try{
            $tBchCode               = $this->input->post('ohdADCBchCode');
            $tDocDate               = $this->input->post('oetADCDocDate');
            $tDocTime               = $this->input->post('oetADCDocTime');
            $tEffectiveDate         = $this->input->post('oetADCEffectiveDate');
            $tRefInt                = $this->input->post('oetADCRefInt');
            $tRefIntDate            = $this->input->post('oetADCRefIntDate');
            $tRmk                   = $this->input->post('otaADCRmk');
            $aDataInsert            = $this->input->post('aDataInsert');
            $tDocType               = $this->input->post('ocmADCDocType');

            $aStoreParam = array(
                "tTblName"    => 'TCNTPdtAdjCostHD',
                "tDocType"    => '10',
                "tBchCode"    => $tBchCode,
                "tShpCode"    => "",
                "tPosCode"    => "",
                "dDocDate"    => date("Y-m-d")
            );

            $aAutogen                   = FCNaHAUTGenDocNo($aStoreParam);
            $aDataMaster['FTXchDocNo']  = $aAutogen[0]["FTXxhDocNo"];

            $oCountDup = $this->mAdjustmentcost->FSnMADCheckDuplicate($aDataMaster['FTXchDocNo']);

            $aDataIns = array(
                'FTBchCode'         => $tBchCode,
                'FTXchDocNo'        => $aDataMaster['FTXchDocNo'],
                'FDLastUpdOn'       => date('Y-m-d H:i:s'),
                'FTLastUpdBy'       => '',
                'FDCreateOn'        => date('Y-m-d H:i:s'),
                'FTCreateBy'        => $this->session->userdata('tSesUserCode'),
                // for DocTmpDT
                'FTSessionID'       => $this->session->userdata('tSesSessionID'),
                'FTXthDocKey'       => 'TCNTPdtAdjCostHD',
            );

            $aParams = array(
                'FTBchCode'         => $tBchCode,
                'FTXchDocNo'        => $aDataMaster['FTXchDocNo'],
                'FNXchDocType'      => $tDocType,
                'FDXchDocDate'      => $tDocDate,
                'FTXchDocTime'      => $tDocTime,
                'FDXchAffect'       => $tEffectiveDate,
                'FTXchRefInt'       => $tRefInt,
                'FDXchRefIntDate'   => $tRefIntDate,
                'FTUsrCode'         => $this->session->userdata("tSesUserCode"),
                'FTXchStaDoc'       => '1',
                'FTXchRmk'          => $tRmk
            );
           

            if ($oCountDup !== FALSE && $oCountDup['counts'] == 0) {

                $this->db->trans_begin();

                $this->mAdjustmentcost->FSaMADAddUpdateDocNoInDocTemp($aDataIns); // Update Docno in DocTemp
                $this->mAdjustmentcost->FSaMADCEventAddHD($aParams); // Update to HD
                $this->mAdjustmentcost->FSaMADDelAllProductDT($aParams); // Delete All Product by id from table DT
                $this->mAdjustmentcost->FSoMADTmptoDT($aDataIns); // Move Doc temp to DT
                

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    $aReturn = array(
                        'nStaEvent' => '900',
                        'tStaMessg' => "Unsucess Add Sale Price Adj"
                    );
                } else {
                    $this->db->trans_commit();
                    $aReturn = array(
                        'nStaCallBack' => $this->session->userdata('tBtnSaveStaActive'),
                        'tCodeReturn' => $aParams['FTXchDocNo'],
                        'tBchCode' => $aParams['FTBchCode'],
                        'nStaEvent'    => '1',
                        'tStaMessg' => 'Success Add Sale Price Adj',
                        //เพิ่มใหม่
                        'tLogType' => 'INFO',
                        'tDocNo' => $aParams['FTXchDocNo'],
                        'tEventName' => 'บันทึกใบปรับราคาขาย',
                        'nLogCode' => '001',
                        'nLogLevel' => '',
                        'FTXphUsrApv'   => ''
                    );
                }

            } else {
                $aReturn = array(
                    'nStaEvent' => '801',
                    'tStaMessg' => "เลขที่เอกสารมีอยู่แล้วในระบบ",
                    //เพิ่มใหม่
                    'tLogType' => 'ERROR',
                    'tDocNo' => $aParams['FTXchDocNo'],
                    'tEventName' => 'Check Data Duplicate',
                    'nLogCode' => '900',
                    'nLogLevel' => 'Critical',
                    'FTXphUsrApv'   => ''
                );
            }


            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $aReturnData = array(
                    'nStaEvent' => '99',
                    'tStaMessg' => 'not success',
                );
            } else {
                $this->db->trans_commit();
                $aReturnData = array(
                    'nStaEvent' => '1',
                    'tStaMessg' => 'success',
                    'nStaCallBack' => $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn' => $aDataMaster['FTXchDocNo'],
                    'nDocType'  => $tDocType
                );
            }
        }catch(Exception $Error){
            $aReturnData    = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    // Functionality : Event Edit
    // Parameters : Ajax and Function Parameter
    // Creator : 25/02/2021 Sooksanti(Nont)
    // Return :
    // Return Type : object
    public function FSoCADCEventEdit(){
        try{
            $tDocNo             = $this->input->post('ohdADCDocNo');
            $tBchCode           = $this->input->post('ohdADCBchCode');
            $tDocDate           = $this->input->post('oetADCDocDate');
            $tDocTime           = $this->input->post('oetADCDocTime');
            $tEffectiveDate     = $this->input->post('oetADCEffectiveDate');
            $tRefInt            = $this->input->post('oetADCRefInt');
            $tRefIntDate        = $this->input->post('oetADCRefIntDate');
            $tRmk               = $this->input->post('otaADCRmk');
            $aDataInsert        = $this->input->post('aDataInsert');
            $tDocType           = $this->input->post('ocmADCDocType');
            $aParams = array(
                'FTBchCode'    => $tBchCode,
                'FTXchDocNo'   => $tDocNo,
                'FNXchDocType' => $tDocType,
                'FDXchDocDate' => $tDocDate,
                'FTXchDocTime' => $tDocTime,
                'FDXchAffect'  => $tEffectiveDate,
                'FTXchRefInt'  => $tRefInt,
                'FDXchRefIntDate' => $tRefIntDate,
                'FTUsrCode'    => $this->session->userdata("tSesUserCode"),
                'FTXchStaDoc'  => '1',
                'FTXchRmk'     => $tRmk
            );

            $aDataIns = array(
                'FTBchCode'         => $tBchCode,
                'FTXchDocNo'        => $tDocNo,
                'FDLastUpdOn'       => date('Y-m-d H:i:s'),
                'FTLastUpdBy'       => '',
                'FDCreateOn'        => date('Y-m-d H:i:s'),
                'FTCreateBy'        => $this->session->userdata('tSesUserCode'),
                // for DocTmpDT
                'FTSessionID'       => $this->session->userdata('tSesSessionID'),
                'FTXthDocKey'       => 'TCNTPdtAdjCostHD',
            );
            $this->mAdjustmentcost->FSaMADCEventEditHD($aParams);
            $this->mAdjustmentcost->FSaMADDelAllProductDT($aParams); // Delete All Product by id from table DT
            $this->mAdjustmentcost->FSoMADTmptoDT($aDataIns); // Move Doc temp to DT
            // $this->db->trans_begin();
            // $this->mAdjustmentcost->FSaMADCClearDT($aParams);
            // print_r($aDataInsert); exit;
            // foreach ($aDataInsert as $key => $aValue) {
            //     if(empty($aValue[3])){
            //         $tBarScan = '';
            //     }else{
            //         $tBarScan = $aValue[3];
            //     }
            //     if(empty($aValue[6])){
            //         $tFCXcdCostNew = NULL;
            //     }else{
            //         $tFCXcdCostNew = $aValue[6];
            //     }
            //     if(empty($aValue[8])){
            //         $tPunCode = '';
            //     }else{
            //         $tPunCode = $aValue[8];
            //     }
            //     if(empty($aValue[9])){
            //         $nXcdFactor = 0;
            //     }else{
            //         $nXcdFactor = $aValue[9];
            //     }

            //     if($aValue[7] != 0){
            //         $aDataIns = array(
            //             'FTBchCode'         => $tBchCode,
            //             'FTXchDocNo'        => $tDocNo,
            //             'FNXcdSeqNo'        => $aValue[0],
            //             'FTPdtCode'         => $aValue[1],
            //             'FTPdtName'         => $aValue[2],
            //             'FCXcdCostOld'      => $aValue[4],
            //             'FCXcdDiff'         => $aValue[5],
            //             'FCXcdCostNew'      => $tFCXcdCostNew,
            //             'FTPunCode'         => $tPunCode,
            //             'FCXcdFactor'       => $nXcdFactor,
            //             'FTXcdBarScan'      => $tBarScan,
            //             'FDLastUpdOn'       => date('Y-m-d H:i:s'),
            //             'FTLastUpdBy'       => $this->session->userdata('tSesUserCode'),
            //         );
            //         $this->mAdjustmentcost->FSaMADCEventAddDT($aDataIns);
            //     }
            // }
            // $this->mAdjustmentcost->FSaMADCEventEditHD($aParams);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $aReturnData = array(
                    'nStaEvent' => '99',
                    'tStaMessg' => 'not success',
                );
            } else {
                $this->db->trans_commit();
                $aReturnData = array(
                    'nStaEvent' => '1',
                    'tStaMessg' => 'success',
                    'tCodeReturn' => $tDocNo,
                    'nDocType'  => $tDocType
                );
            }
        }catch(Exception $Error){
            $aReturnData    = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    // Functionality : Get PDT From DT
    // Parameters : Ajax and Function Parameter
    // Creator : 03/03/2021 Sooksanti(Nont)
    // Return :
    // Return Type : object
    public function FSoCADCGetPdtFromDT(){
        try{
            $tDocNo = $this->input->post('tDocNo');
            $aParams = array(
                'tDocNo' => $tDocNo,
            );

            $aData = $this->mAdjustmentcost->FSaMADCGetPdtFromDT($aParams);

            if ($aData['rtCode'] == '1') {
                $aReturnData = array(
                    'aData' => $aData['raItems'],
                    'nStaEvent' => $aData['rtCode'],
                    'tStaMessg' => $aData['rtDesc'],
                );
            } else {
                $aReturnData = array(
                    'nStaEvent' => $aData['rtCode'],
                    'tStaMessg' => $aData['rtDesc'],
                );
            }
        }catch(Exception $Error){
            $aReturnData    = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }

        echo json_encode($aReturnData);
    }


    //Function : Cancel ApproveDoc
    //Parameters : Cancel ApproveDoc
    //Creator : 03/03/2021 Sooksanti(Nont)
    //Return : Array
    //Return Type : Array
    public function FSoCADCCancel(){

        $tXchDocNo =  $this->input->post('tXchDocNo');

        $aDataUpdate    =  array(
            'FTXchDocNo'  => $tXchDocNo,
        );

            $aStaApv = $this->mAdjustmentcost->FSaMADCCancel($aDataUpdate);

            if($aStaApv['rtCode'] == 1 ){
                $aApv = array(
                    'nSta'  => 1,
                    'tMsg'  => "Cancel done",
                );
            }else{
                $aApv = array(
                    'nSta' => 2,
                    'tMsg' => "Not Cancel",
                );
            }
            echo json_encode($aApv);
    }

    // Function: Approve Document
    // Parameters: Ajex Event Add Document
    // Creator: 03/03/2021 Sooksanti(Nont)
    // LastUpdate: -
    // Return: Object Status Cancel Document
    // ReturnType: Object
    public function FSvCADCApproveDocument() {
        try {
            $tADCDocNo = $this->input->post('ptADCDocNo');
            $tADCBchCode = $this->input->post('ptADCBchCode');

            $aDataUpdate = array(
                'FTXchDocNo' => $tADCDocNo,
                'FTXchApvCode' => $this->session->userdata('tSesUsername')
            );

            $aStaApv = $this->mAdjustmentcost->FSvMADCApprove($aDataUpdate);

            if($aStaApv['rtCode'] == '1'){
                $aMQParams = [
                    "queueName" => "CN_QDocApprove",
                    "params"    => [
                        'ptFunction'    => "AdjustCost",
                        'ptSource'      => 'AdaStoreBack',
                        'ptDest'        =>'MQReceivePrc',
                        'ptFilter'      => $tADCBchCode,
                        'ptData'        =>json_encode([
                            "ptBchCode" => $tADCBchCode,
                            "ptDocNo"   => $tADCDocNo,
                            "ptUser"    => $this->session->userdata("tSesUsername"),
                        ])
                    ]
                ];
                FCNxCallRabbitMQ($aMQParams);

                $aReturn = array(
                    'nStaEvent'    => '1',
                    'tStaMessg'    => 'ok'
                );
            }
            else{
                $aReturn = array(
                    'nStaEvent'    => '99',
                    'tStaMessg'    => 'Not Approve'
                );
            }

        } catch (ErrorException $err) {
            $aReturn = array(
                'nStaEvent' => '900',
                'tStaMessg' => language('common/main/main', 'tApproveFail')
            );
            echo json_encode($aReturn);
            return;
        }
    }

    // Functionality: Function Delete Document Adjust Cost
    // Parameters: Ajax and Function Parameter
    // Creator: 03/03/2021 Sooksanti(Non)
    // Return: Object View Data Table
    // ReturnType: object
    public function FSoCADCDeleteEventDoc(){
        try{
            $tADCDocNo  = $this->input->post('tADCDocNo');
            $aDataMaster = array(
                'tADCDocNo'     => $tADCDocNo
            );

            // for ($i=0; $i < count($tADCDocNo); $i++) {
            //     $aDataDeleteFile[$i]['tDocNo']        = $tADCDocNo[$i];
            //     $aDataDeleteFile[$i]['tBchCode']      = $this->session->userdata("tSesUsrBchCodeDefault");
            //     $aDataDeleteFile[$i]['tDocKey']       = 'TCNTPdtAdjCostHD';
            // }

            // FCNaUPFDelDocFileEvent($aDataDeleteFile);

            $aResDelDoc = $this->mAdjustmentcost->FSnMADCDelDocument($aDataMaster);
            if($aResDelDoc['rtCode'] == '1'){
                $aDataStaReturn  = array(
                    'nStaEvent' => '1',
                    'tStaMessg' => 'Success'
                );
            }else{
                $aDataStaReturn  = array(
                    'nStaEvent' => $aResDelDoc['rtCode'],
                    'tStaMessg' => $aResDelDoc['rtDesc']
                );
            }
        }catch(Exception $Error){
            $aDataStaReturn = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aDataStaReturn);
    }

    //Functionality : Event Delete Product Price list
    //Parameters : Ajax jReason()
    //Creator : 25/02/2019 Napat(Jame)
    //Return : Status Delete Event
    //Return Type : String
    public function FSoCADPdtPriDeleteEvent()
    {
        $aPdtDataItem = json_decode($this->input->post('tPdtDataItem'), JSON_FORCE_OBJECT);
        $tDelType = $this->input->post('tDelType');
        $FTXphDocNo = $this->input->post('tDocNo');
        $FTPdtCode = $this->input->post('tPdtCode');
        $FTPunCode = $this->input->post('tPunCode');
        $tSta = $this->input->post('tSta');
        $tSeq = $this->input->post('tSeq');
        $tSession = $this->session->userdata('tSesSessionID');

        if ($tDelType == "M") { // Delete Multiple
            foreach ($aPdtDataItem as $aPdtData) {
                $aDataMaster = array(
                    'FNXtdSeqNo' => $aPdtData['tSeq'],
                    'FTPdtCode' => $aPdtData['tPdt'],
                    'FTPunCode' => $aPdtData['tPun'],
                    'FTSessionID' => $tSession,
                    'FTXthDocKey' => 'TCNTPdtAdjCostHD'
                );
                $aResDel = $this->mAdjustmentcost->FSaMADCPdtTmpDelAll($aDataMaster);

                // ตรวจสอบกรอกข้อมูลซ้ำ Temp
                if ($aPdtData['tSta'] == "5") {
                    $aParams = [
                        'tUserSessionID' => $tSession,
                        'aFieldName' => [['FTPdtCode', $aPdtData['tPdt']], ['FTPunCode', $aPdtData['tPun']]]
                    ];
                    FCNnDocTmpChkInlineCodeMultiDupInTemp($aParams);
                }
            }
        } else { // Delete Single

            $aDataMaster = array(
                'FTXphDocNo' => $FTXphDocNo,
                'FTPdtCode' => $FTPdtCode,
                'FTPunCode' => $FTPunCode,
                'FTSessionID' => $tSession,
                'FNXtdSeqNo' => $tSeq,
                'FTXthDocKey' => 'TCNTPdtAdjCostHD',
            );
            $aResDel = $this->mAdjustmentcost->FSaMADCPdtTmpDelAll($aDataMaster);

            // ตรวจสอบกรอกข้อมูลซ้ำ Temp
            if ($tSta == "5") {
                $aParams = [
                    'tUserSessionID' => $tSession,
                    'aFieldName' => [['FTPdtCode', $FTPdtCode], ['FTPunCode', $FTPunCode]]
                ];
                FCNnDocTmpChkInlineCodeMultiDupInTemp($aParams);
            }
        }
        if ($aResDel['rtCode'] == 1) {
            $aReturn    = array(
                'nStaEvent' => $aResDel['rtCode'],
                'tStaMessg' => $aResDel['rtDesc'],
                'tLogType' => 'INFO',
                'tDocNo' => $FTXphDocNo,
                'tEventName' => 'ลบใบปรับราคาทุน ',
                'nLogCode' => '001',
                'nLogLevel' => '',
                'FTXphUsrApv'   => ''
            );
        }else{
            $aReturn    = array(
                'nStaEvent' => $aResDel['rtCode'],
                'tStaMessg' => $aResDel['rtDesc'],
                'tLogType' => 'ERROR',
                'tDocNo' => $FTXphDocNo,
                'tEventName' => 'ลบใบปรับราคาทุน ',
                'nLogLevel' => '500',
                'nLogLevel' => 'Critical',
                'FTXphUsrApv'   => ''
            );
        }
        
        //ถ้าทำงานเสร็จสิ้นแล้วจะรวบรวม Data เพื่อส่ง MQ_LOG
        FSoCCallLogMQ($aReturn);
        echo json_encode($aReturn);
    }

    public function FSoCSPAUpdatePriceTemp(){
        $aDataMaster    = array(
            'FTXthDocNo'        => $this->input->post('FTXthDocNo'),
            'FTPdtCode'         => $this->input->post('FTPdtCode'),
            'FTPunCode'         => $this->input->post('FTPunCode'),
            'tPrice'            => $this->input->post('ptPrice'),
            'tSeq'              => $this->input->post('tSeq'),
            'tDiff'             => empty($this->input->post('tDiff')) ? 0 : (float)$this->input->post('tDiff'),
            'tColValidate'      => $this->input->post('tColValidate'),
            'tValue'            => empty($this->input->post('ptValue')) ? 0 : (float)$this->input->post('ptValue'),
            'FTSessionID'       => $this->session->userdata('tSesSessionID'),
            'tSearchSpaPdtPri'  => $this->session->userdata('tSearchSpaPdtPri')
        );

        $aResDel    = $this->mAdjustmentcost->FSaMAdUpdatePriceTemp($aDataMaster);
        $aReturn    = array(
            'nStaEvent' => $aResDel['rtCode'],
            'tStaMessg' => $aResDel['rtDesc'],
        );
        echo json_encode($aReturn);
    }

    //Functionality : Function Call DataTables Product Price
    //Parameters : Ajax Call View DataTable
    //Creator : 18/02/2019 Napat(Jame)
    //Return : String View
    //Return Type : View
    public function FSvCSPAPdtAdPriDataList(){
        try {
            $tSearchAll = $this->input->post('tSearchAll');
            $FTXphDocNo = $this->input->post('FTXphDocNo');
            $tCostType  = $this->input->post('tCostType');
            $nPage      = ($this->input->post('nPageCurrent') == '' || null) ? 1 : $this->input->post('nPageCurrent');   // Check Number Page
            $nLangEdit  = $this->session->userdata("tLangEdit");
            $aData      = array(
                'nStaAddOrEdit' => 99,
                'nPage'         => $nPage,
                'nRow'          => get_cookie('nShowRecordInPageList'),
                'FNLngID'       => $nLangEdit,
                'tSearchAll'    => $tSearchAll,
                'FTXthDocKey'   => 'TCNTPdtAdjCostHD',
                'FTXphDocNo'    => $FTXphDocNo,
                'tCostType'    => $tCostType,
                'FTSessionID'   => $this->session->userdata('tSesSessionID'),
            );
            // Get Option Show Decimal
            $nOptDecimalShow        = get_cookie('tOptDecimalShow');
            $aPdtPriDataList        = $this->mAdjustmentcost->FSaMSPAPdtAdPriList($aData);
            $aAlwEventSalePriceAdj  = FCNaHCheckAlwFunc('docADCCost/0/0');
            $aGenTable              = array(
                'aPdtPriDataList'           => $aPdtPriDataList,
                'nPage'                     => $nPage,
                'tSearchAll'                => $tSearchAll,
                'aAlwEventSalePriceAdj'     => $aAlwEventSalePriceAdj,
                'nOptDecimalShow'           => $nOptDecimalShow,
                'nRow'                      => get_cookie('nShowRecordInPageList'),
            );
            // echo "<pre>";
            // print_r($aGenTable);
            // echo "</pre>";
            $this->load->view('document/adjustmentcost/wAdjustmentcostPriDataTable', $aGenTable);
        } catch (Exception $Error) {
            echo $Error;
        }
        
    }
}
