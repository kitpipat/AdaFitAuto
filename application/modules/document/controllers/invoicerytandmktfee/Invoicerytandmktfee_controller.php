<?php 
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Invoicerytandmktfee_controller extends MX_Controller {

    public function __construct() {
        parent::__construct ();
        $this->load->model('document/invoicerytandmktfee/Invoicerytandmktfee_model');

        // Clean XSS Filtering Security
		$this->load->helper("security");
		if ($this->security->xss_clean($this->input->post(), TRUE) === FALSE){
            echo "ERROR XSS Filter";
        }
    }

    public function index($nTRMBrowseType,$tTRMBrowseOption){
        // รองรับการเข้ามาแบบ Noti
        $aParams        = array(
            'tDocNo'    => $this->input->post('tDocNo'),
            'tBchCode'  => $this->input->post('tBchCode'),
            'tAgnCode'  => $this->input->post('tAgnCode'),
        );
        $aDataConfigView        = array(
            'nTRMBrowseType'    => $nTRMBrowseType,
            'tTRMBrowseOption'  => $tTRMBrowseOption,
            'aAlwEvent'         => FCNaHCheckAlwFunc('docInvoiceRytAndMktFee/0/0'),
            'vBtnSave'          => FCNaHBtnSaveActiveHTML('docInvoiceRytAndMktFee/0/0'),
            'nOptDecimalShow'   => get_cookie('tOptDecimalShow'),
            'nOptDecimalSave'   => get_cookie('tOptDecimalSave'),
            'aParams'           => $aParams
        );
        $this->load->view('document/invoicerytandmktfee/wInvoicerytandmktfee',$aDataConfigView);
    }

    //  List
    public function FSvCTRMCPageList(){
        $this->load->view('document/invoicerytandmktfee/wInvoicerytandmktfeeSearchList');
    }

    // Data Table
    public function FSvCTRMDatatable(){
        $tAdvanceSearchData = $this->input->post('oAdvanceSearch');
        $nPage              = $this->input->post('nPageCurrent');
        $nOptDecimalShow    = get_cookie('tOptDecimalShow');
        if ($nPage == '' || $nPage == null) {
            $nPage  = 1;
        } else {
            $nPage  = $this->input->post('nPageCurrent');
        }
        $nLangEdit  = $this->session->userdata("tLangEdit");
        $aData      = array(
            'FNLngID'           => $nLangEdit,
            'nPage'             => $nPage,
            'nRow'              => get_cookie('nShowRecordInPageList'),
            'aAdvanceSearch'    => $tAdvanceSearchData
        );
        $aList      = $this->Invoicerytandmktfee_model->FSaMTRMList($aData);
        $aGenTable  = array(
            'aAlwEvent'         => FCNaHCheckAlwFunc('docInvoiceRytAndMktFee/0/0'),
            'aDataList'         => $aList,
            'nPage'             => $nPage,
            'nOptDecimalShow'   => $nOptDecimalShow
        );
        $this->load->view('document/invoicerytandmktfee/wInvoicerytandmktfeeDataTable',$aGenTable);
    }

    // หน้าจอเพิ่มข้อมูล
    public function FSvCTRMPageAdd(){
        try{
            //ล้างค่าใน Temp
            $this->Invoicerytandmktfee_model->FSaMTRMDeletePDTInTmp('');
            // Get Option Show Decimal
            $nOptDecimalShow    = get_cookie('tOptDecimalShow');
            $aDataConfigViewAdd = array(
                'nOptDecimalShow'   => $nOptDecimalShow,
                'aDataDocHD'        => array('rtCode'=>'99'),
                'aDataDocCST'       => array('rtCode'=>'99'),    
            );
            $tViewPageAdd   = $this->load->view('document/invoicerytandmktfee/wInvoicerytandmktfeePageAdd',$aDataConfigViewAdd,true);
            $aReturnData    = array(
                'tViewPageAdd'      => $tViewPageAdd,
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

    // หน้าจอแก้ไขข้อมูล
    public function FSvCTRMPageEdit(){
        try {
            $tTRMDocNo      = $this->input->post('ptTRMDocNo');
            $tTRMAgnCode    = $this->input->post('ptAgnCode');
            $tTRMBchCode    = $this->input->post('ptBchCode');
            // Clear Data In Doc DT Temp
            $this->Invoicerytandmktfee_model->FSaMTRMDeletePDTInTmp($tTRMDocNo);
            // Get Option Show Decimal
            $nOptDecimalShow    = get_cookie('tOptDecimalShow');
            // Array Data Where Get (HD,HDCst)
            $aDataWhere = array(
                'tAgnCode'  => $tTRMAgnCode,
                'tBCHCode'  => $tTRMBchCode,
                'tTRMDocNo' => $tTRMDocNo,
            );
            $this->db->trans_begin();

            // Get Data Document HD
            $aDataDocHD     = $this->Invoicerytandmktfee_model->FSaMTRMGetDataDocHD($aDataWhere);
            if($aDataDocHD['rtCode'] == '1'){
                $tBchCodeTo = $aDataDocHD['raItems']['FTBchCodeTo'];
            } else {
                $tBchCodeTo = "";
            }
            // Get Data Agency Branch Address
            $nLngID             = $this->session->userdata("tLangEdit");
            $aDataAgnBchAddr    = $this->Invoicerytandmktfee_model->FSaMTRMGetAgnAddress($tBchCodeTo,$nLngID);

            // Get Data Document HD Cst
            $aDataDocHDCst      = $this->Invoicerytandmktfee_model->FSaMTRMGetDataDocCSTHD($aDataWhere);

            // Move Data DT To DTTemp , DTSPL To DTTemp
            $this->Invoicerytandmktfee_model->FSxMTRMMoveDTToDTTemp($aDataWhere);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aReturnData    = array(
                    'nStaEvent' => '500',
                    'tStaMessg' => 'Error Query Call Edit Page.'
                );
            } else {
                $this->db->trans_commit();
                $aDataConfigViewAdd     = array(
                    'nOptDecimalShow'   => $nOptDecimalShow,
                    'aDataDocHD'        => $aDataDocHD,
                    'aDataAgnBchAddr'   => $aDataAgnBchAddr,
                    'aDataDocHDCst'     => $aDataDocHDCst,
                );
                $tViewPageAdd   = $this->load->view('document/invoicerytandmktfee/wInvoicerytandmktfeePageAdd',$aDataConfigViewAdd,true);
                $aReturnData    = array(
                    'tViewPageAdd'  => $tViewPageAdd,
                    'nStaEvent'     => '1',
                    'tStaMessg'     => 'Success'
                );
            }
        } catch (Exception $Error) {
            $aReturnData    = array(
                'nStaReturn'    => '500',
                'tStaMessg'     => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    // โหลดหน้าจอ Sale Page
    public function FSoCTRMLoadSalePage(){
        $aDataWhere = [
            'tStaEvn'       => $this->input->post('tStaEvn'),
            'tAgnCode'      => $this->input->post('tAgnCode'),
            'tBCHCode'      => $this->input->post('tBCHCode'),
            'tAgnCodeTo'    => $this->input->post('tAgnCodeTo'),
            'tBchCodeTo'    => $this->input->post('tBchCodeTo'),
            'tTRMDocNo'     => $this->input->post('tTRMDocNo'),
        ];
        if($aDataWhere['tStaEvn'] == 'Clear'){
            $tViewSumSalHD  = $this->load->view('document/invoicerytandmktfee/PanelDT/wPanelSaleHD',array(
                'nOptDecimalShow'   => get_cookie('tOptDecimalShow'),
                'aDataSumSalHD'     => [],
            ),true);
            $tViewSumVatSalHD   = $this->load->view('document/invoicerytandmktfee/PanelDT/wPanelVatSaleHD',array(
                'nOptDecimalShow'   => get_cookie('tOptDecimalShow'),
                'aDataSumVatSalHD'  => [],
            ),true);
            $tViewDataDTRM          = $this->load->view('document/invoicerytandmktfee/PanelDT/wPanelDataDTRM',array(
                'aConfigRytMktFee'  => [],
                'aDataDTTmp'        => [],
                'aDataDTFoot'       => [],
            ),true);
        } else {
            // ดึงข้อมูลยอดขายรวมในแต่ละเดือน
            $aDataSumSalHD  = $this->Invoicerytandmktfee_model->FSaMTRMGetDataSumSalHDByID($aDataWhere);
            $tViewSumSalHD  = $this->load->view('document/invoicerytandmktfee/PanelDT/wPanelSaleHD',array(
                'nOptDecimalShow'   => get_cookie('tOptDecimalShow'),
                'aDataSumSalHD'     => $aDataSumSalHD,
            ),true);

            // ดึงข้อมูลตารางรายละเอียด ภาษี
            $aDataSumVatSalHD   = $this->Invoicerytandmktfee_model->FSaMTRMGetDataSumVatSalHDByID($aDataWhere);
            $tViewSumVatSalHD   = $this->load->view('document/invoicerytandmktfee/PanelDT/wPanelVatSaleHD',array(
                'nOptDecimalShow'   => get_cookie('tOptDecimalShow'),
                'aDataSumVatSalHD'  => $aDataSumVatSalHD,
            ),true);

            // Get Data Config Royoty Free & Marketing Free
            $aDataWhereDTTmp    = [
                'tAgnCode'  => $aDataWhere['tAgnCode'],
                'tBchCode'  => $aDataWhere['tBCHCode'],
                'tDocNo'    => $aDataWhere['tTRMDocNo'],
            ];  
            $aConfigRytMktFee   = $this->Invoicerytandmktfee_model->FSaMTRMGetDataConfigRytMktFeeByID($aDataWhere);
            $aDataDTTmp         = $this->Invoicerytandmktfee_model->FSaMTRMGetDataDTTmp($aDataWhereDTTmp);
            $aDataDTFoot        = $this->Invoicerytandmktfee_model->FSaMTRMGetDataDTFootTmp($aDataWhereDTTmp);
            $tViewDataDTRM          = $this->load->view('document/invoicerytandmktfee/PanelDT/wPanelDataDTRM',array(
                'aConfigRytMktFee'  => $aConfigRytMktFee,
                'aDataDTTmp'        => $aDataDTTmp,
                'aDataDTFoot'       => $aDataDTFoot,
            ),true);
        }
        $aDataReturn    = [
            'tViewSumSalHD'     => $tViewSumSalHD,
            'tViewSumVatSalHD'  => $tViewSumVatSalHD,
            'tViewDataDTRM'     => $tViewDataDTRM
        ];

        echo json_encode($aDataReturn);
    }

    // ค้นหาที่ สาขา ของ Agency
    public function FSoCTRMFindingAgnBch(){
        $tAgnCode   = $this->input->post('tAgnCode');
        $aChkData   = $this->Invoicerytandmktfee_model->FSaMTRMChkAgnBch($tAgnCode);
        $aReturn    = array(
            'aChkData' => $aChkData
        );
        echo json_encode($aReturn);
    }

    // ค้นหาที่อยู่ Address สาขา ของ Agency
    public function FSoCTRMFindingAgnBchAddress(){
        $tAgnBchCode    = $this->input->post('tAgnBchCode');
        $nLngID         = $this->session->userdata("tLangID");
        $aDataAddress   = $this->Invoicerytandmktfee_model->FSaMTRMGetAgnAddress($tAgnBchCode,$nLngID);
        echo json_encode($aDataAddress);
    }

    // ค้นหาข้อมูลรายละเอียดเอกสารบิลขายประจำเดือน
    public function FSoCTRMFindingSale(){
        $tDocNo             = $this->input->post('tDocNo');
        $tAgnCode           = $this->input->post('tAgnCode');
        $tBchCode           = $this->input->post('tBchCode');
        $tAgnCodeTo         = $this->input->post('tAgnCodeTo');
        $tBchCodeTo         = $this->input->post('tBchCodeTo');
        $tVatInOrEx         = $this->input->post('tVatInOrEx');
        $tSearchBillMonth   = $this->input->post('tSearchBillMonth');
        $tSearchBillYear    = $this->input->post('tSearchBillYear');

        if(!empty($tSearchBillMonth) && !empty($tSearchBillYear)){
            $tFristDayOfMonth   = $tSearchBillYear.'-'.$tSearchBillMonth.'-'.'01';
            $tLastDayOfMonth    = date('Y-m-t',strtotime($tFristDayOfMonth));
        }else{
            $tFristDayOfMonth   = '';
            $tLastDayOfMonth   = '';
        }
        $aDataWhere         = [
            'tDocNo'            => $tDocNo,
            'tAgnCode'          => $tAgnCode,
            'tBchCode'          => $tBchCode,
            'tAgnCodeTo'        => $tAgnCodeTo,
            'tBchCodeTo'        => $tBchCodeTo,
            'tFristDayOfMonth'  => $tFristDayOfMonth,
            'tLastDayOfMonth'   => $tLastDayOfMonth,
        ];

        // Render Panel Sum Sale HD
        $aDataSumSalHD  = $this->Invoicerytandmktfee_model->FSaMTRMGetDataSumSalHD($aDataWhere);
        $tViewSumSalHD  = $this->load->view('document/invoicerytandmktfee/PanelDT/wPanelSaleHD',array(
            'nOptDecimalShow'   => get_cookie('tOptDecimalShow'),
            'aDataSumSalHD'     => $aDataSumSalHD,
        ),true);

        $aDataSumVatSalHD   = $this->Invoicerytandmktfee_model->FSaMTRMGetDataSumVatSalHD($aDataWhere);
        $tViewSumVatSalHD   = $this->load->view('document/invoicerytandmktfee/PanelDT/wPanelVatSaleHD',array(
            'nOptDecimalShow'   => get_cookie('tOptDecimalShow'),
            'aDataSumVatSalHD'  => $aDataSumVatSalHD,
        ),true);
        
        // Get Data Config Royoty Free & Marketing Free
        $aConfigRytMktFee   = $this->Invoicerytandmktfee_model->FSaMTRMGetDataConfigRytMktFee($aDataWhere);


        // Clear DT Doc Temp
        $this->Invoicerytandmktfee_model->FSaMTRMClearDTTmp();
        if(!empty($aConfigRytMktFee) && $aDataSumSalHD['rtCode']  == '1'){
            $aDataComp      = FCNaGetCompanyForDocument();
            $aDataSaleHD    = $aDataSumSalHD['rtResult'];
            if(!empty($aDataSaleHD['FCTRMAmtVTbl'])){
                $aDataPdtParams = [
                    'FTAgnCode'             => $aDataWhere['tAgnCode'],
                    'FTBchCode'             => $aDataWhere['tBchCode'],
                    'FTXphDocNo'            => $aDataWhere['tDocNo'],
                    'FTXphDocKey'           => 'TACTRMDT',
                    'FCXpdTotalNV'          => $aDataSaleHD['FCTRMAmtVTbl'],
                    'FCCshRoyaltyRate'      => $aConfigRytMktFee['FCCshRoyaltyRate'],
                    'FCCshMarketingRate'    => $aConfigRytMktFee['FCCshMarketingRate'],
                    'FTXphVATInOrEx'        => $tVatInOrEx,
                    'FCVatRate'             => $aDataComp['cVatRate'],
                    'FNLngID'               => $this->session->userdata("tLangID"),
                    'FTSessionID'           => $this->session->userdata('tSesSessionID'),
                    'FNXpdDesc'             => '('.date('m/Y',strtotime($tFristDayOfMonth)).')'
                ];
                // Insert DTTmp
                $this->db->trans_begin();
                $this->Invoicerytandmktfee_model->FSaMTRMInsertRytMktFeeToTemp($aDataPdtParams);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $aStaInsDTTmp   = array(
                        'nStaEvent' => '500',
                        'tStaMessg' => 'Error Insert Product Error Please Contact Admin.'
                    );
                } else {
                    $this->db->trans_commit();
                    // Calc Vat And Vat Table Grand
                    $bStaCalcVat    = $this->Invoicerytandmktfee_model->FSaMTRMCalcVatSalDTTmp($aDataPdtParams);
                    $aStaInsDTTmp   = array(
                        'nStaEvent' => '1',
                        'tStaMessg' => 'Success Add Product Into Document DT Temp.'
                    );
                }
            }
            
        }

        // Get Data DTTMP
        $tViewDataDTRM  = $this->load->view('document/invoicerytandmktfee/PanelDT/wPanelDataDTRM',array(
            'aConfigRytMktFee'  => $aConfigRytMktFee,
            'aDataDTTmp'        => $this->Invoicerytandmktfee_model->FSaMTRMGetDataDTTmp($aDataWhere),
            'aDataDTFoot'       => $this->Invoicerytandmktfee_model->FSaMTRMGetDataDTFootTmp($aDataWhere),
        ),true);


        $aDataReturn    = [
            'tViewSumSalHD'     => $tViewSumSalHD,
            'tViewSumVatSalHD'  => $tViewSumVatSalHD,
            'tViewDataDTRM'     => $tViewDataDTRM
        ];
        echo json_encode($aDataReturn);
    }

    // เคลียร์ข้อมูลในตาราง Temp
    public function FSoCTRMClearTmp(){
        $tDocNo         = $this->input->post('tDocNo');
        $tStaClearTmp   = $this->Invoicerytandmktfee_model->FSaMTRMDeletePDTInTmp($tDocNo);
        echo json_encode($tStaClearTmp);
    }

    // เช็คข้อมูลในตาราง Temp
    public function FSoCTRMChkDataInDTTmp(){
        $aDataWhere = [
            'tTRMDocNo'     => $this->input->post('tTRMDocNo'),
            'tTRMAgnCode'   => $this->input->post('tTRMAgnCode'),
            'tTRMBchCode'   => $this->input->post('tTRMBchCode'),
        ];
        $oDataChkDTTmp  = $this->Invoicerytandmktfee_model->FSaMTRMChkDataInDTTmp($aDataWhere);
        echo json_encode($oDataChkDTTmp);
    }

    // เพิ่มข้อมูล
    public function FSvCTRMEventAdd(){
        try {
            $aDataDocument      = $this->input->post();
            $tTRMAutoGenCode    = (isset($aDataDocument['ocbTRMStaAutoGenCode'])) ? 1 : 0;
            $tTRMDocNo          = (isset($aDataDocument['oetTRMDocNo'])) ? $aDataDocument['oetTRMDocNo'] : '';
            $tTRMDocDate        = $aDataDocument['oetTRMDocDate'] . " " . $aDataDocument['oetTRMDocTime'];
            // Array Data Table Document
            $aTableAddUpdate    = [
                'tTableHD'      => 'TACTRMHD',
                'tTableDT'      => 'TACTRMDT',
                'tTableDTTmp'   => 'TACTRMDTTmp',
                'tTableDTSpl'   => 'TACTRMHDCst',
                'tTableHDRef'   => 'TACTRMHDDocRef',
            ];

            // Array Data Where Insert
            $aDataWhere         = [
                'FTAgnCode'         => $aDataDocument['oetTRMAgnCode'],
                'FTBchCode'         => $aDataDocument['ohdTRMBchCode'],
                'FTXphDocNo'        => $tTRMDocNo,
                'FDLastUpdOn'       => date('Y-m-d H:i:s'),
                'FDCreateOn'        => date('Y-m-d H:i:s'),
                'FTCreateBy'        => $this->session->userdata('tSesUsername'),
                'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                'FTSessionID'       => $this->session->userdata('tSesSessionID'),
            ];

            //  Array Data HD Master Insert To TACTRMHD
            $aDataMaster        = [
                'FTXphDocType'          => '1',
                'FTAgnCodeTo'           => $aDataDocument['ohdTRMAgnCode'],
                'FTBchCodeTo'           => $aDataDocument['ohdTRMAgnBchCode'],
                'FDXphDocDate'          => $tTRMDocDate,
                'FTCstCode'             => null,
                'FTCshSoldTo'           => $aDataDocument['ohdTRMCshSoldTo'],
                'FTCshShipTo'           => $aDataDocument['ohdTRMCshShipTo'],
                'FTCshPaymentTerm'      => $aDataDocument['ohdTRMCshPaymentTerm'],
                'FTXphCond'             => null,
                'FTBbkCode'             => $aDataDocument['oetTRMBbkCode'],
                'FTXphVATInOrEx'        => $aDataDocument['ocmTRMfoVatInOrEx'],
                'FCXphVatRate'          => $aDataDocument['ohdXsdVatRate0'],
                'FCXphTotal'            => $aDataDocument['oetTRMTBDTotal'],
                'FCXphDis'              => $aDataDocument['oetTRMTBDDisChg'],
                'FCXphChg'              => floatval(0),
                'FCXphTotalAfDisChg'    => $aDataDocument['oetTRMTBAFDisChg'],
                'FCXphVat'              => $aDataDocument['oetTRMTBAmtV'],
                'FCXphVatable'          => $aDataDocument['oetTRMTBAmtVTbl'],
                'FCXphGrand'            => $aDataDocument['oetTRMTBGrand'],
                'FTXphMonthRM'          => $aDataDocument['ocmSearchBillMonth'],
                'FTXphYearRM'           => $aDataDocument['ocmSearchBillYear'],
                'FDXphDueDate'          => $aDataDocument['oetTRMDueDate'],
                'FTUsrCode'             => $this->session->userdata('tSesUsername'),
                'FTDptCode'             => null,
                'FTXphStaDoc'           => 1,
                'FNXphStaDocAct'        => 1,
            ];

            // Array Data SPL Insert To TACTRMHDCst
            $aDataSPL           = [
                'FTAgnCode'     => $aDataDocument['oetTRMAgnCode'],
                'FTBchCode'     => $aDataDocument['ohdTRMBchCode'],
                'FTXphCshOrCrd' => $aDataDocument['ocmTRMPaymentType'],
            ];

            $this->db->trans_begin();
            // Check Auto GenCode Document
            if ($tTRMAutoGenCode == '1') {
                $aStoreParam    = array(
                    "tTblName"  => $aTableAddUpdate['tTableHD'],
                    "tDocType"  => 0,
                    "tBchCode"  => $aDataDocument['ohdTRMBchCode'],
                    "tShpCode"  => "",
                    "tPosCode"  => "",
                    "dDocDate"  => date("Y-m-d")
                );
                $aAutogen       = FCNaHAUTGenDocNo($aStoreParam);
                $aDataWhere['FTXphDocNo']   = $aAutogen[0]["FTXxhDocNo"];
            } else {
                $aDataWhere['FTXphDocNo']   = $tTRMDocNo;
            }

            // [Add] Document HD
            $this->Invoicerytandmktfee_model->FSxMTRMAddUpdateHD($aDataMaster,$aDataWhere,$aTableAddUpdate);

            // [Add] Document Cst
            $this->Invoicerytandmktfee_model->FSxMTRMAddUpdateSPLHD($aDataSPL, $aDataWhere, $aTableAddUpdate);
            
            // [Update] DocNo -> Temp
            $this->Invoicerytandmktfee_model->FSxMTRMAddUpdateDocNoToTemp($aDataWhere);

            // [Add] Doc DTTemp -> DT
            $this->Invoicerytandmktfee_model->FSaMTRMMoveDTTmpToDT($aDataWhere,$aTableAddUpdate);

            // [Update] Sum Data DT -> Update HD RM
            $this->Invoicerytandmktfee_model->FSaMTRMSumRMDTTmpToHD($aDataWhere,$aTableAddUpdate);

            // Check Status Transection DB
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aReturnData    = array(
                    'nStaEvent' => '900',
                    'tStaMessg' => "Error Unsucess Add Document."
                );
            } else {
                $this->db->trans_commit();
                $aReturnData = array(
                    'nStaCallBack'  => $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn'   => $aDataWhere['FTXphDocNo'],
                    'tAgnCode'      => $aDataWhere['FTAgnCode'],
                    'tBchCode'      => $aDataWhere['FTBchCode'],
                    'nStaReturn'    => '1',
                    'tStaMessg'     => 'Success Add Document.'
                );
            }

        } catch (Exception $Error) {
            $aReturnData    = [
                'nStaReturn'    => '500',
                'tStaMessg'     => $Error->getMessage()
            ];
        }
        echo json_encode($aReturnData);
    }

    // แก้ไขข้อมูล
    public function FSvCTRMEventEdit(){
        try {
            $aDataDocument  = $this->input->post();
            $tTRMDocNo      = (isset($aDataDocument['oetTRMDocNo'])) ? $aDataDocument['oetTRMDocNo'] : '';
            $tTRMDocDate    = $aDataDocument['oetTRMDocDate'] . " " . $aDataDocument['oetTRMDocTime'];
            // Array Data Table Document
            $aTableAddUpdate    = [
                'tTableHD'      => 'TACTRMHD',
                'tTableDT'      => 'TACTRMDT',
                'tTableDTTmp'   => 'TACTRMDTTmp',
                'tTableDTSpl'   => 'TACTRMHDCst',
                'tTableHDRef'   => 'TACTRMHDDocRef',
            ];

            // Array Data Where Insert
            $aDataWhere     = [
                'FTAgnCode'         => $aDataDocument['oetTRMAgnCode'],
                'FTBchCode'         => $aDataDocument['ohdTRMBchCode'],
                'FTXphDocNo'        => $tTRMDocNo,
                'FDLastUpdOn'       => date('Y-m-d H:i:s'),
                'FDCreateOn'        => date('Y-m-d H:i:s'),
                'FTCreateBy'        => $this->session->userdata('tSesUsername'),
                'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                'FTSessionID'       => $this->session->userdata('tSesSessionID'),
            ];

            //  Array Data HD Master Insert To TACTRMHD
            $aDataMaster    = [
                'FTXphDocType'          => '1',
                'FTAgnCodeTo'           => $aDataDocument['ohdTRMAgnCode'],
                'FTBchCodeTo'           => $aDataDocument['ohdTRMAgnBchCode'],
                'FDXphDocDate'          => $tTRMDocDate,
                'FTCstCode'             => null,
                'FTCshSoldTo'           => $aDataDocument['ohdTRMCshSoldTo'],
                'FTCshShipTo'           => $aDataDocument['ohdTRMCshShipTo'],
                'FTCshPaymentTerm'      => $aDataDocument['ohdTRMCshPaymentTerm'],
                'FTXphCond'             => null,
                'FTBbkCode'             => $aDataDocument['oetTRMBbkCode'],
                'FTXphVATInOrEx'        => $aDataDocument['ocmTRMfoVatInOrEx'],
                'FCXphVatRate'          => $aDataDocument['ohdXsdVatRate0'],
                'FCXphTotal'            => floatval($aDataDocument['oetTRMTBDTotal']),
                'FCXphDis'              => floatval($aDataDocument['oetTRMTBDDisChg']),
                'FCXphChg'              => floatval(0),
                'FCXphTotalAfDisChg'    => floatval($aDataDocument['oetTRMTBAFDisChg']),
                'FCXphVat'              => floatval($aDataDocument['oetTRMTBAmtV']),
                'FCXphVatable'          => floatval($aDataDocument['oetTRMTBAmtVTbl']),
                'FCXphGrand'            => floatval($aDataDocument['oetTRMTBGrand']),
                'FTXphMonthRM'          => $aDataDocument['ocmSearchBillMonth'],
                'FTXphYearRM'           => $aDataDocument['ocmSearchBillYear'],
                'FDXphDueDate'          => $aDataDocument['oetTRMDueDate'],
                'FTUsrCode'             => $this->session->userdata('tSesUsername'),
                'FTDptCode'             => null,
                'FTXphStaDoc'           => 1,
                'FNXphStaDocAct'        => 1,
            ];

            // Array Data SPL Insert To TACTRMHDCst
            $aDataSPL   = [
                'FTAgnCode'     => $aDataDocument['oetTRMAgnCode'],
                'FTBchCode'     => $aDataDocument['ohdTRMBchCode'],
                'FTXphCshOrCrd' => $aDataDocument['ocmTRMPaymentType'],
            ];

            $this->db->trans_begin();

            // [Add] Document HD
            $this->Invoicerytandmktfee_model->FSxMTRMAddUpdateHD($aDataMaster,$aDataWhere,$aTableAddUpdate);
            // [Add] Document Cst
            $this->Invoicerytandmktfee_model->FSxMTRMAddUpdateSPLHD($aDataSPL, $aDataWhere, $aTableAddUpdate);
            // [Update] DocNo -> Temp
            $this->Invoicerytandmktfee_model->FSxMTRMAddUpdateDocNoToTemp($aDataWhere);
            // [Add] Doc DTTemp -> DT
            $this->Invoicerytandmktfee_model->FSaMTRMMoveDTTmpToDT($aDataWhere,$aTableAddUpdate);
            // [Update] Sum Data DT -> Update HD RM
            $this->Invoicerytandmktfee_model->FSaMTRMSumRMDTTmpToHD($aDataWhere,$aTableAddUpdate);

            // Check Status Transection DB
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aReturnData    = array(
                    'nStaEvent' => '900',
                    'tStaMessg' => "Error Unsucess Add Document."
                );
            } else {
                $this->db->trans_commit();
                $aReturnData = array(
                    'nStaCallBack'  => $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn'   => $aDataWhere['FTXphDocNo'],
                    'tAgnCode'      => $aDataWhere['FTAgnCode'],
                    'tBchCode'      => $aDataWhere['FTBchCode'],
                    'nStaReturn'    => '1',
                    'tStaMessg'     => 'Success Add Document.'
                );
            }
        } catch (Exception $Error) {
            $aReturnData    = [
                'nStaReturn'    => '500',
                'tStaMessg'     => $Error->getMessage()
            ];
        }
        echo json_encode($aReturnData);
    }

    // ลบข้อมูลเอกสาร
    public function FSoCTRMEventDelete(){
        try {
            $tDataDocNo     = $this->input->post('tDataDocNo');
            $aDataMaster    = array(
                'tDataDocNo'    => $tDataDocNo
            );
            $aResDelDoc = $this->Invoicerytandmktfee_model->FSnMTRMDelDocument($aDataMaster);
            if ($aResDelDoc['rtCode'] == '1') {
                $aDataStaReturn = array(
                    'nStaEvent' => '1',
                    'tStaMessg' => 'Success'
                );
            } else {
                $aDataStaReturn = array(
                    'nStaEvent' => $aResDelDoc['rtCode'],
                    'tStaMessg' => $aResDelDoc['rtDesc']
                );
            }
        } catch (Exception $Error) {
            $aDataStaReturn = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aDataStaReturn);
    }

    // ยกเลิกข้อมูลเอกสาร
    public function FSoCTRMEventCancel(){
        try {
            $tDataDocNo     = $this->input->post('tDataDocNo');
            $aDataMaster    = array(
                'tDataDocNo' => $tDataDocNo
            );
            $aResDelDoc     = $this->Invoicerytandmktfee_model->FSnMTRMEventCancel($aDataMaster);
            if ($aResDelDoc['rtCode'] == '1') {
                $aDataStaReturn = array(
                    'nStaEvent' => '1',
                    'tStaMessg' => 'Success'
                );
            } else {
                $aDataStaReturn = array(
                    'nStaEvent' => $aResDelDoc['rtCode'],
                    'tStaMessg' => $aResDelDoc['rtDesc']
                );
            }
        } catch (Exception $Error) {
            $aDataStaReturn = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aDataStaReturn);
    }

    // อนุมัติเอกสาร
    public function FSoCTRMEventAppove(){
        try {
            $tDataDocNo     = $this->input->post('tDataDocNo');
            $aDataMaster    = array(
                'FTXphDocNo'    => $tDataDocNo,
                'FTXphStaApv'   => 1,
                'FTXphApvCode'  => $this->session->userdata('tSesUsername')
            );
            $aResDelDoc = $this->Invoicerytandmktfee_model->FSnMTRMEventAppove($aDataMaster);
            if ($aResDelDoc['rtCode'] == '1') {
                $aDataStaReturn = array(
                    'nStaEvent' => '1',
                    'tStaMessg' => 'Success'
                );
            } else {
                $aDataStaReturn = array(
                    'nStaEvent' => $aResDelDoc['rtCode'],
                    'tStaMessg' => $aResDelDoc['rtDesc']
                );
            }
        } catch (Exception $Error) {
            $aDataStaReturn = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aDataStaReturn);
    }




    // เช็คเอกสาร
    public function FSoCTRMChkDocHaveInDB(){
        $aDataWhere = [
            'FTAgnCodeTo'   => $this->input->post('tAgnCodeTo'),
            'FTBchCodeTo'   => $this->input->post('tBchCodeTo'),
            'FTXphMonthRM'  => $this->input->post('tBillMonth'),
            'FTXphYearRM'   => $this->input->post('tBillYear'),
        ];
        $aDataReturn    = $this->Invoicerytandmktfee_model->FSaMTRMChkDocHaveInDB($aDataWhere);
        echo json_encode($aDataReturn);
    }





}