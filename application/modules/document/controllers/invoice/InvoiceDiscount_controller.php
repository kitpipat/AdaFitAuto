<?php
use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;

defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class InvoiceDiscount_controller extends MX_Controller {

    public $tRouteMenu = 'docInvoice/0/0';

    public function __construct() {
        parent::__construct ();
        $this->load->model('document/invoice/InvoiceDiscount_model');
    }

    // Functionality    : Function Call Data From IV HD
    // Parameters       : Ajax and Function Parameter
    // Creator          : 02/07/2021 Supawat
    public function FSoCIVDisChgHDList(){
        try{
            $tDocNo             = $this->input->post('tDocNo');
            $nSeqNo             = $this->input->post('tSeqNo');
            $tBchCode           = $this->input->post('tBCHCode');
            $aAdvanceSearch     = $this->input->post('oAdvanceSearch');
            $nPage              = $this->input->post('nPageCurrent');
            $aAlwEvent          = FCNaHCheckAlwFunc($this->tRouteMenu);

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
                'aAdvanceSearch'    => $aAdvanceSearch,
                'tDocNo'            => $tDocNo,  
                'nSeqNo'            => $nSeqNo,
                'tBchCode'          => $tBchCode,
                'tSessionID'        => $this->session->userdata('tSesSessionID')
            );
            $aDataList  = $this->InvoiceDiscount_model->FSaMIVGetDisChgHDList($aDataCondition);

            $aConfigView    = array(
                'nPage'             => $nPage,
                'nOptDecimalShow'   => $nOptDecimalShow,
                'aAlwEvent'         => $aAlwEvent,
                'aDataList'         => $aDataList,
            );
            $tIVViewDataTableList   = $this->load->view('document/invoice/dis_chg/wInvoiceDisChgHDList', $aConfigView, true);
            $aReturnData = array(
                'tIVViewDataTableList'  => $tIVViewDataTableList,
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

    // Functionality    : Function Call Data From IV DT
    // Parameters       : Ajax and Function Parameter
    // Creator          : 02/07/2021 Supawat
    public function FSoCIVDisChgDTList(){
        try{
            $tDocNo             = $this->input->post('tDocNo');
            $nSeqNo             = $this->input->post('tSeqNo');
            $tBchCode           = $this->input->post('tBCHCode');
            $aAdvanceSearch     = $this->input->post('oAdvanceSearch');
            $nPage              = $this->input->post('nPageCurrent');
            $aAlwEvent          = FCNaHCheckAlwFunc($this->tRouteMenu);

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

            $aDataList      = $this->InvoiceDiscount_model->FSaMIVGetDisChgDTList($aDataCondition);

            $aConfigView    = array(
                'nPage'             => $nPage,
                'nOptDecimalShow'   => $nOptDecimalShow,
                'aAlwEvent'         => $aAlwEvent,
                'aDataList'         => $aDataList,
            );
            $tIVViewDataTableList   = $this->load->view('document/invoice/dis_chg/wInvoiceDisChgDTList', $aConfigView, true);
            $aReturnData = array(
                'tIVViewDataTableList'  => $tIVViewDataTableList,
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

    // Function         : เพิ่มและแก้ไข ส่วนลดรายการ
    // Parameters       : Ajax and Function Parameter
    // Creator          : 02/07/2021 Supawat
    public function FSoCIVAddEditDTDis(){
        try{
            $tBchCode       = $this->input->post('tBchCode');
            $tDocNo         = $this->input->post('tDocNo');
            $nSeqNo         = $this->input->post('tSeqNo');
            $tVatInOrEx     = $this->input->post('tVatInOrEx');
            $tSessionID     = $this->session->userdata('tSesSessionID');
            $tDisChgItems   = $this->input->post('tDisChgItems');
            $tDisChgSummary = $this->input->post('tDisChgSummary');
            $aDisChgItems   = json_decode($tDisChgItems, true);
            $aDisChgSummary = json_decode($tDisChgSummary, true);

            $this->db->trans_begin();
            // ================================ Begin DB Process ================================
                $aParams    = array(
                    'nStaDis'         => 1,
                    'tDocNo'          => $tDocNo,
                    'nSeqNo'          => $nSeqNo,
                    'tBchCode'        => $tBchCode,
                    'nLngID'          => $this->session->userdata("tLangID"),
                    'tSessionID'      => $tSessionID,
                    'tVatInOrEx'      => $tVatInOrEx,
                    'aDisChgSummary'  => $aDisChgSummary
                );

                $this->InvoiceDiscount_model->FSaMIVClearDisChgTxtDTTemp($aParams);
                $this->InvoiceDiscount_model->FSaMIVDeleteDTDisTemp($aParams);

                if(isset($aDisChgItems) && !empty($aDisChgItems)){
                    // print_r($aDisChgItems);
                    $aInsertDTDisTmp    =   array();
                    foreach ($aDisChgItems as $key => $item){
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
                            'FDLastUpdOn'       => date('Y-m-d H:i:s'),
                            'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                            'FDCreateOn'        => date('Y-m-d H:i:s'),
                            'FTCreateBy'        => $this->session->userdata('tSesUsername')
                        ));
                    }
                    
                    //Insert TCNTDocDTDisTmp 
                    $this->InvoiceDiscount_model->FSaMIVAddEditDTDisTemp($aInsertDTDisTmp);

                    //Update TCNTDocDTTmp 
                    $this->InvoiceDiscount_model->FSaMIVUpdateDTDisInTemp($aInsertDTDisTmp);
                }
            // ==================================================================================
                
            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $aReturnData    = array(
                    'nStaEvent' => '500',
                    'tStaMessg' => 'Error Not Insert Document DT Dis Temp.'
                );
            }else{
                $this->db->trans_commit();

                $aCalcDTParams = [
                    'tDataDocEvnCall'   => '',
                    'tDataVatInOrEx'    => $tVatInOrEx,
                    'tDataDocNo'        => $tDocNo,
                    'tDataDocKey'       => 'TAPTPiDT',
                    'tDataSeqNo'        => $nSeqNo
                ];
                // $aStaCalcDTTemp = FCNbHCallCalcDocDTTemp($aCalcDTParams);
                // if($aStaCalcDTTemp === TRUE){
                    // Prorate HD
                    FCNaHCalculateProrate('TAPTPiDT',$tDocNo);
                    FCNbHCallCalcDocDTTemp($aCalcDTParams);
                    $aCalEndOfBillHDDisParams = [
                        'tDocNo'        => $tDocNo,
                        'tBchCode'      => $tBchCode,
                        'tSessionID'    => $tSessionID,
                        'tSplVatType'   => $tVatInOrEx,
                        'nLngID'        => '',
                        'tDocKey'       => 'TAPTPiDT',
                        'nSeqNo'        => $nSeqNo
                    ];
                    FSvCCreditNoteCalEndOfBillHDDis($aCalEndOfBillHDDisParams);
                    $aReturnData    = array(
                        'nStaEvent' => '1',
                        'tStaMessg' => 'Success Insert Document Dis Temp.'
                    );
                // }else{
                //     $aReturnData    = array(
                //         'nStaEvent' => '500',
                //         'tStaMessg' => 'Error Not Calcurate DT Temp.'
                //     );
                // }
            }
        }catch(Exception $Error){
            $aReturnData    = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    // Function         : เพิ่มและแก้ไข ส่วนลดท้ายบิล
    // Parameters       : Ajax and Function Parameter
    // Creator          : 02/07/2021 Supawat
    public function FSoCIVAddEditHDDis(){
        try{
            $tBchCode       = $this->input->post('tBchCode');
            $tDocNo         = $this->input->post('tDocNo');
            $nSeqNo         = $this->input->post('tSeqNo');
            $tVatInOrEx     = $this->input->post('tVatInOrEx');
            $tSessionID     = $this->session->userdata('tSesSessionID');
            $tDisChgItems   = $this->input->post('tDisChgItems');
            $tDisChgSummary = $this->input->post('tDisChgSummary');
            $aDisChgItems   = json_decode($tDisChgItems, true);
            $aDisChgSummary = json_decode($tDisChgSummary, true);

            // ================================ Begin DB Process ================================
            $aParams = array(
                'tDocNo'          => $tDocNo,  
                'tBchCode'        => $tBchCode,
                'nLngID'          => $this->session->userdata("tLangID"),
                'tSessionID'      => $this->session->userdata('tSesSessionID'),
                'aDisChgSummary'  => $aDisChgSummary
            );
            
            // Delete Dis/Chg Tabel HD DIS Temp
            $this->InvoiceDiscount_model->FSaMIVDeleteHDDisTemp($aParams);

            $this->db->trans_begin();
                if(isset($aDisChgItems) && !empty($aDisChgItems)){
                    $aInsertHDDisTmp    =   array();
                    foreach ($aDisChgItems as $nKey =>  $aItem) {
                        array_push($aInsertHDDisTmp,array(
                            'FTBchCode'             => $tBchCode,
                            'FTXthDocNo'            => $tDocNo,
                            'FDXtdDateIns'          => date('Y-m-d H:i:s',strtotime($aItem['tCreatedAt'])),
                            'FTXtdDisChgTxt'        => $aItem['tDisChgTxt'],
                            'FTXtdDisChgType'       => $aItem['nDisChgType'],
                            'FCXtdTotalAfDisChg'    => $aItem['cAfterDisChg'],
                            'FCXtdTotalB4DisChg'    => $aItem['cBeforeDisChg'],
                            'FCXtdDisChg'           => $aItem['cDisChgNum'],
                            'FCXtdAmt'              => $aItem['cDisChgValue'],
                            'FTSessionID'           => $tSessionID,
                            'FDLastUpdOn'           => date('Y-m-d H:i:s'),
                            'FTLastUpdBy'           => $this->session->userdata('tSesUsername'),
                            'FDCreateOn'            => date('Y-m-d H:i:s'),
                            'FTCreateBy'            => $this->session->userdata('tSesUsername')
                        ));
                    }
                    $this->InvoiceDiscount_model->FSaMIVAddEditHDDisTemp($aInsertHDDisTmp);
                }
            // ==================================================================================
            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $aReturnData    = array(
                    'nStaEvent' => '500',
                    'tStaMessg' => 'Error Not Insert Document HD Dis Temp.'
                );
            }else{
                $this->db->trans_commit();
                // Prorate HD
                FCNaHCalculateProrate('TAPTPiDT',$tDocNo);
                $aCalcDTParams = [
                    'tDataDocEvnCall'   => '',
                    'tDataVatInOrEx'    => $tVatInOrEx,
                    'tDataDocNo'        => $tDocNo,
                    'tDataDocKey'       => 'TAPTPiDT',
                    'tDataSeqNo'        => ''
                ];
                $aStaCalcDTTemp = FCNbHCallCalcDocDTTemp($aCalcDTParams);
                if($aStaCalcDTTemp === TRUE){
                    $aReturnData    = array(
                        'nStaEvent' => '1',
                        'tStaMessg' => 'Success process'
                    );
                }else{
                    $aReturnData    = array(
                        'nStaEvent' => '500',
                        'tStaMessg' => 'Error Calcurate DT Document Temp.'
                    );
                }
            }
        }catch(Exception $Error){
            $aReturnData    = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

}
