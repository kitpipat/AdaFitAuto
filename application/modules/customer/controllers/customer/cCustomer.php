<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class cCustomer extends MX_Controller {

    public function __construct(){
        parent::__construct ();
        $this->load->model('customer/customer/mCustomer');
        date_default_timezone_set("Asia/Bangkok");
    }

    public function index($nCstBrowseType, $tCstBrowseOption){
        $vBtnSave   = FCNaHBtnSaveActiveHTML('customer/0/0'); 
        $this->load->view ( 'customer/customer/wCustomer', array (
            'vBtnSave'          => $vBtnSave,
            'nCstBrowseType'    =>$nCstBrowseType,
            'tCstBrowseOption'  =>$tCstBrowseOption
        ));
    }

    // Page List
    public function FSvCSTListPage(){
        $this->load->view('customer/customer/wCustomerList');
    }

    // DataTables Customer
    public function FSvCSTDataList(){
        $nPage      = $this->input->post('nPageCurrent');
        $tSearchAll = $this->input->post('tSearchAll');
        if($nPage == '' || $nPage == null){$nPage = 1;}else{$nPage = $this->input->post('nPageCurrent');}
        if(!$tSearchAll){$tSearchAll='';}

	    $nLangEdit      = $this->session->userdata("tLangEdit");
        $aData  = array(
            'nPage'         => $nPage,
            'nRow'          => 10,
            'FNLngID'       => $nLangEdit,
            'tSearchAll'    => $tSearchAll
        );

        $tAPIReq        = "";
        $tMethodReq     = "GET";
        $aResList       = $this->mCustomer->FSaMCSTList($tAPIReq, $tMethodReq, $aData);
        $aGenTable      = array(
            'aDataList'     => $aResList,
            'nPage'         => $nPage,
            'tSearchAll'    => $tSearchAll
        );
        $this->load->view('customer/customer/wCustomerDataTable', $aGenTable);
    }

    // เข้าหน้าเพิ่มข้อมูล
    public function FSvCSTAddPage(){
        //หาว่า user นี้ มีสิทธิ์มองเห็นลูกค้าเครดิตไหม
        $nConfigAllowCusCredit = $this->mCustomer->FSxMCSTFindCstCredit();
        if( $nConfigAllowCusCredit == 1){ //อนุญาตเห็นทั้งหมด
            $nAllowCusCreditCode    = 0;
        }else{
            //หาว่า user นี้ มีสิทธิ์มองเห็นลูกค้าเครดิตไหม
            $aCodeAllowCusCredit    = $this->mCustomer->FSxMCSTFindCstCreditCode();
            if($aCodeAllowCusCredit['rtCode'] == 800){
                $nAllowCusCreditCode    = 0;
            }else{
                $nAllowCusCreditCode    = $aCodeAllowCusCredit['raItem']['FTSysStaUsrValue'];
            }
        }
        $aDataAdd = array(
            'aResult'                   => array('rtCode'=>'99'),
            'nConfigAllowCusCredit'     => $nConfigAllowCusCredit,
            'nAllowCusCreditCode'       => $nAllowCusCreditCode
        );
        $this->load->view('customer/customer/wCustomerAdd',$aDataAdd);
    }

    // เข้าหน้าแก้ไข
    public function FSvCSTEditPage(){
        $tCstCode   = $this->input->post('tCstCode');
        $nLangEdit  = $this->session->userdata("tLangEdit");
        $aData      = [
            'FTCstCode' => $tCstCode,
            'FNLngID'   => $nLangEdit
        ];
        $tAPIReq        = "";
        $tMethodReq     = "GET";
        $aCstData       = $this->mCustomer->FSaMCSTSearchByID($tAPIReq, $tMethodReq, $aData);

        $nMemAmtActive  = $this->mCustomer->FScMCSTGetAmtActive($tCstCode); //ยอดซื้อสะสม
        $nMemPntActive  = $this->mCustomer->FScMCSTGetPntActive($tCstCode); //แต้มสะสม
        $nMemPntExp     = $this->mCustomer->FScMCSTGetPntExp($tCstCode); //แต้มสะสมที่จะหมดอายุ

        //หาว่า user นี้ มีสิทธิ์มองเห็นลูกค้าเครดิตไหม
        $nConfigAllowCusCredit = $this->mCustomer->FSxMCSTFindCstCredit();
        if( $nConfigAllowCusCredit == 1){ //อนุญาตเห็นทั้งหมด
            $nAllowCusCreditCode    = 0;
        }else{
            //หาว่า user นี้ มีสิทธิ์มองเห็นลูกค้าเครดิตไหม
            $aCodeAllowCusCredit    = $this->mCustomer->FSxMCSTFindCstCreditCode();
            if($aCodeAllowCusCredit['rtCode'] == 800){
                $nAllowCusCreditCode    = 0;
            }else{
                $nAllowCusCreditCode    = $aCodeAllowCusCredit['raItem']['FTSysStaUsrValue'];
            }
        }

        // Check Data Image Customer
        $aDataEdit  = [
            'tImgObjAll'                => $aCstData['raItems']['rtImgObj'],
            'aResult'                   => $aCstData,
            'nMemAmtActive'             => $nMemAmtActive,
            'nMemPntActive'             => $nMemPntActive,
            'nMemPntExp'                => $nMemPntExp,
            'nConfigAllowCusCredit'     => $nConfigAllowCusCredit,
            'nAllowCusCreditCode'       => $nAllowCusCreditCode
        ];
        $this->load->view('customer/customer/wCustomerAdd',$aDataEdit);
    }

    //เพิ่มข้อมูล
    public function FSaCSTAddEvent(){

        try{
            $tImgInputCustomer      = $this->input->post('oetImgInputCustomer');
            $tImgInputCustomerOld   = $this->input->post('oetImgInputCustomerOld');

            $aDataMaster = array(
                'tIsAutoGenCode'        => $this->input->post('ocbCustomerAutoGenCode'),
                'FTImgObj'              => $this->input->post('oetImgInputCustomer'),
                'FTCstCode'             => $this->input->post('oetCstCode'),
                'FTCstName'             => $this->input->post('oetCstName'),
                'FTCstRmk'              => $this->input->post('otaCstRemark'),
                'FTCstTel'              => $this->input->post('oetCstTel'),
                'FTCstEmail'            => $this->input->post('oetCstEmail'),
                'FTCstCardID'           => $this->input->post('oetCstIdenNum'),
                'FDCstDob'              => $this->input->post('oetCstBirthday'),
                'FTCstSex'              => $this->input->post('orbCstSex'),
                'FTCstBusiness'         => $this->input->post('orbCstBusiness'),
                'FTCstTaxNo'            => $this->input->post('oetCstTaxIdenNum'),
                'FTCstStaActive'        => empty($this->input->post('ocbCstStaActive')) ? 2 : $this->input->post('ocbCstStaActive'),
                'FTCstStaAlwPosCalSo'   => empty($this->input->post('ocbCstStaAlwPosCalSo')) ? 2 : $this->input->post('ocbCstStaAlwPosCalSo'),
                'FTCgpCode'             => $this->input->post('oetCstCgpCode'),
                'FTCtyCode'             => $this->input->post('oetCstCtyCode'),
                'FTClvCode'             => $this->input->post('oetCstClvCode'),
                'FTOcpCode'             => $this->input->post('oetCstCstOcpCode'),
                // 'FTPmgCode'             => $this->input->post('oetCstPmgCode'), //ถอดออกจาก Master Customer 23/06/2021
                'FTCstDiscRet'          => $this->input->post('oetCstDiscRet'),
                'FTCstDiscWhs'          => $this->input->post('oetCstDiscWhs'),
                'FTCstBchHQ'            => $this->input->post('ocbCstStaBchOrHQ'),
                'FTCstBchCode'          => $this->input->post('oetCstBchCodes'),
                'FDCstStart'            => $this->input->post('oetUsrDateStart'),
                'FTLastUpdBy'           => $this->session->userdata('tSesUsername'),
                'FDLastUpdOn'           => date('Y-m-d H:i:s'),
                'FTCreateBy'            => $this->session->userdata('tSesUsername'),
                'FDCreateOn'            => date('Y-m-d H:i:s'),
                'FNLngID'               => $this->session->userdata('tLangEdit'),
                'FTAgnCode'             => $this->input->post('oetCstAgnCode'),
            );

            if(!empty($aDataMaster['FTAgnCode'])){
                $aDataCgp = $this->mCustomer->FScMCSTGetInfoMemberConFigSpc($aDataMaster['FTAgnCode'],1);
                if($aDataCgp['rtCode']=='1'){
                    $aDataMaster['FTCgpCode'] = $aDataCgp['raItem']['FTCfgStaUsrValue'];
                }
            }

            // Check Auto Gen Customer Code?
            if($aDataMaster['tIsAutoGenCode'] == '1'){
                $aStoreParam = array(
                    "tTblName"    => 'TCNMCst',
                    "tDocType"    => 0,
                    "tBchCode"    => $this->session->userdata("tSesUsrBchCodeDefault"),
                    "tShpCode"    => "",
                    "tPosCode"    => "",
                    "dDocDate"    => date("Y-m-d")
                );
                $aAutogen                   = FCNaHAUTGenDocNo($aStoreParam);
                $aDataMaster['FTCstCode']   = $aAutogen[0]["FTXxhDocNo"];
            }

            $oCountDup  = $this->mCustomer->FSoMCSTCheckDuplicate($aDataMaster['FTCstCode']);
            $nStaDup    = $oCountDup[0]->counts;
            if($nStaDup == 0){
                $this->db->trans_begin();

                $this->mCustomer->FSaMCSTAddUpdateMaster($aDataMaster);
                $this->mCustomer->FSaMCSTAddUpdateLang($aDataMaster);

                if($this->db->trans_status() === false){
                    $this->db->trans_rollback();
                    $aReturn = array(
                        'nStaEvent'    => '900',
                        'tStaMessg'    => "Unsucess Add Event"
                    );
                }else{
                    $this->db->trans_commit();

                    // Check Data Image New Compare Image Old
                    if($tImgInputCustomer != $tImgInputCustomerOld){
                        $aImageData = [
                            'tModuleName'       => 'customer',
                            'tImgFolder'        => 'customer',
                            'tImgRefID'         => $aDataMaster['FTCstCode'],
                            'tImgObj'           => $tImgInputCustomer,
                            'tImgTable'         => 'TCNMCst',
                            'tTableInsert'      => 'TCNMImgPerson',
                            'tImgKey'           => '',
                            'dDateTimeOn'       => date('Y-m-d H:i:s'),
                            'tWhoBy'            => $this->session->userdata('tSesUsername'),
                            'nStaDelBeforeEdit' => 1
                        ];
                        $aImgReturn = FCNnHAddImgObj($aImageData);
                    }

                    ///---------------QMember-----------------------//
                    $aQMemberParam = $this->FSaCCstFormatDataMemberV5($aDataMaster['FTCstCode']);
                    $aMQParams = [
                        "queueName" => "QMember",
                        "exchangname" => "",
                        "params" => $aQMemberParam
                    ];
                    $this->FSxCCSTSendDataMemberV5($aMQParams);

                    // Set return
                    $aReturn = array(
                        'aImgReturn'    => ( isset($aImgReturn) && !empty($aImgReturn) ? $aImgReturn : array("nStaEvent" => '1') ),
                        'nStaCallBack'	=> $this->session->userdata('tBtnSaveStaActive'),
                        'tCodeReturn'	=> $aDataMaster['FTCstCode'],
                        'nStaEvent'	    => '1',
                        'tStaMessg'		=> 'Success Add Event'
                    );
                }
            }else{
                $aReturn = array(
                    'nStaEvent'    => '801',
                    'tStaMessg'    => "Data Code Duplicate"
                );
            }
            echo json_encode($aReturn);
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //แก้ไขข้อมูล
    public function FSaCSTEditEvent(){
        try{
            $tImgInputCustomer      = $this->input->post('oetImgInputCustomer');
            $tImgInputCustomerOld   = $this->input->post('oetImgInputCustomerOld');
      
            $aDataMaster = array(
                // Master
                'FTImgObj'          => $this->input->post('oetImgInputCustomer'),
                'FTCstCode'         => $this->input->post('oetCstCode'),
                'FTCstName'         => $this->input->post('oetCstName'),
                'FTCstRmk'          => $this->input->post('otaCstRemark'),
                'FTCstTel'          => $this->input->post('oetCstTel'),
                'FTCstEmail'        => $this->input->post('oetCstEmail'),
                'FTCstCardID'       => $this->input->post('oetCstIdenNum'),
                'FDCstDob'          => $this->input->post('oetCstBirthday'),
                'FTCstSex'          => $this->input->post('orbCstSex'),
                'FTCstBusiness'     => $this->input->post('orbCstBusiness'),
                'FTCstTaxNo'        => $this->input->post('oetCstTaxIdenNum'),
                'FTCstStaActive'    => empty($this->input->post('ocbCstStaActive')) ? 2 : $this->input->post('ocbCstStaActive'),
                'FTCstStaAlwPosCalSo' => empty($this->input->post('ocbCstStaAlwPosCalSo')) ? 2 : $this->input->post('ocbCstStaAlwPosCalSo'),
                'FTCgpCode'         => $this->input->post('oetCstCgpCode'),
                'FTCtyCode'         => $this->input->post('oetCstCtyCode'),
                'FTClvCode'         => $this->input->post('oetCstClvCode'),
                'FTOcpCode'         => $this->input->post('oetCstCstOcpCode'),
                // 'FTPmgCode'         => $this->input->post('oetCstPmgCode'), //ถอดออกจาก Master Customer 23/06/2021
                'FTCstDiscRet'      => $this->input->post('oetCstDiscRet'),
                'FTCstDiscWhs'      => $this->input->post('oetCstDiscWhs'),
                'FTCstBchHQ'        => $this->input->post('ocbCstStaBchOrHQ'),
                'FTCstBchCode'      => $this->input->post('oetCstBchCodes'),
                'FDCstStart'        => $this->input->post('oetUsrDateStart'),
                'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                'FDLastUpdOn'       => date('Y-m-d H:i:s'),
                'FTCreateBy'        => $this->session->userdata('tSesUsername'),
                'FDCreateOn'        => date('Y-m-d H:i:s'),
                'FNLngID'           => $this->session->userdata('tLangEdit'),
                'FTAgnCode'         => $this->input->post('oetCstAgnCode'),
            );

            if($aDataMaster['FTCstBchHQ'] == 1){
                $aDataMaster['FTCstBchCode'] = $this->input->post('oetCstBchNames');
            }else{
                $aDataMaster['FTCstBchCode'] = $this->input->post('oetCstBchCodes');
            }
            

            if(!empty($aDataMaster['FTAgnCode'])){
                $aDataCgp = $this->mCustomer->FScMCSTGetInfoMemberConFigSpc($aDataMaster['FTAgnCode'],1);
                    if($aDataCgp['rtCode']=='1'){
                            $aDataMaster['FTCgpCode'] = $aDataCgp['raItem']['FTCfgStaUsrValue'];
                    }
            }

            $this->db->trans_begin();
            $this->mCustomer->FSaMCSTAddUpdateMaster($aDataMaster);
            $this->mCustomer->FSaMCSTAddUpdateLang($aDataMaster);

            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $aReturn = array(
                    'nStaEvent'    => '900',
                    'tStaMessg'    => "Unsucess Update Event"
                );
            }else{
                $this->db->trans_commit();

                // Check Data Image New Compare Image Old
                if($tImgInputCustomer != $tImgInputCustomerOld){
                    $aImageData = [
                        'tModuleName'       => 'customer',
                        'tImgFolder'        => 'customer',
                        'tImgRefID'         => $aDataMaster['FTCstCode'],
                        'tImgObj'           => $tImgInputCustomer,
                        'tImgTable'         => 'TCNMCst',
                        'tTableInsert'      => 'TCNMImgPerson',
                        'tImgKey'           => '',
                        'dDateTimeOn'       => date('Y-m-d H:i:s'),
                        'tWhoBy'            => $this->session->userdata('tSesUsername'),
                        'nStaDelBeforeEdit' => 1
                    ];
                    $aImgReturn = FCNnHAddImgObj($aImageData);
                }
                    ///---------------QMember-----------------------//
                $aQMemberParam = $this->FSaCCstFormatDataMemberV5($aDataMaster['FTCstCode']);
                $aMQParams = [
                    "queueName" => "QMember",
                    "exchangname" => "",
                    "params" => $aQMemberParam
                ];
                $this->FSxCCSTSendDataMemberV5($aMQParams);

                $aReturn = array(
                    'aImgReturn'    => ( isset($aImgReturn) && !empty($aImgReturn) ? $aImgReturn : array("nStaEvent" => '1') ),
                    'nStaCallBack'	=> $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn'	=> $aDataMaster['FTCstCode'],
                    'nStaEvent'	    => '1',
                    'tStaMessg'		=> 'Success Update Event'
                );
            }
            echo json_encode($aReturn);
        }catch(Exception $Error){
            echo $Error;
        }

    }

    //เพิ่มที่อยู่ลูกค้า
    public function FSaCSTAddUpdateAddressEvent(){
        try{
            $aDataMaster = array(
                // Address
                'FTCstCode' => $this->input->post('ohdCstCode'), // Customer reference
                'AddressMode' => $this->input->post('ohdCstAddressMode'), // Address mode 1 or 2
                'FTAddGrpType' => "1", // 1: Customer

                'FNAddSeqNo' => $this->input->post('ohdCstAddSeqNo'),
                'FTAddRefNo' => $this->input->post('ohdCstAddRefNo'),
                'FTAddV1No' => $this->input->post('oetCstAddNo'),
                'FTAddV1Soi' => $this->input->post('oetCstAddSoi'),
                'FTAddV1Village' => $this->input->post('oetCstAddVillage'),
                'FTAddV1Road' => $this->input->post('oetCstAddRoad'),
                'FTAddCountry' => $this->input->post('oetCstAddCountry'),
                'FTZneCode' => $this->input->post('oetCstAddZoneCode'),
                'FTAreCode' => $this->input->post('ohdCstAddAreaCode'),
                'FTAddV1PvnCode' => $this->input->post('oetCstAddPvnCode'),
                'FTAddV1DstCode' => $this->input->post('oetCstAddDstCode'),
                'FTAddV1SubDist' => $this->input->post('oetCstAddSubDistCode'),
                'FTAddV1PostCode' => $this->input->post('oetCstAddPostCode'),
                'FTAddWebsite' => $this->input->post('oetCstAddWebsite'),
                'FTAddRmk' => $this->input->post('otaCstAddRemark'),
                'FTAddV2Desc1' => $this->input->post('otaCstAddDist1'),
                'FTAddV2Desc2' => $this->input->post('otaCstAddDist2'),
                'FTAddLongitude' => $this->input->post('ohdCstAddLongitude'),
                'FTAddLatitude' => $this->input->post('ohdCstAddLatitude'),

                'FTLastUpdBy'   => $this->session->userdata('tSesUsername'),
                'FDLastUpdOn'   => date('Y-m-d H:i:s'),
                'FTCreateBy'   => $this->session->userdata('tSesUsername'),
                'FDCreateOn'   => date('Y-m-d H:i:s'),
                'FNLngID'       => $this->session->userdata('tLangEdit')

            );

            /*echo '<pre>';
            var_dump($aDataMaster);
            echo '</pre>';
            return;*/

            $this->db->trans_begin();
            $aStaCstMaster  = $this->mCustomer->FSaMCSTAddUpdateAddress($aDataMaster);

            if($this->db->trans_status() === false){
                $this->db->trans_rollback();
                $aReturn = array(
                    'nStaEvent'    => '900',
                    'tStaMessg'    => "Unsucess Add Event"
                );
            }else{
                $this->db->trans_commit();

                // Set return
                $aReturn = array(
                    'nStaCallBack'	=> $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn'	=> $aDataMaster['FTCstCode'],
                    'nStaEvent'	    => '1',
                    'tStaMessg'		=> 'Success Add Event'
                );

                ///---------------QMember-----------------------//
                $aQMemberParam = $this->FSaCCstFormatDataMemberV5($aDataMaster['FTCstCode']);
                $aMQParams = [
                    "queueName" => "QMember",
                    "exchangname" => "",
                    "params" => $aQMemberParam
                ];
                $this->FSxCCSTSendDataMemberV5($aMQParams);
            }
            echo json_encode($aReturn);
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //เพิ่มข้อมูลติดต่อลูกค้า
    public function FSaCSTAddUpdateContactEvent(){
        try{
            $aDataMaster = array(
                // Contact
                'FTCstCode' => $this->input->post('ohdCstCode'), // Customer reference
                'FNCtrSeq' => $this->input->post('ohdCstContactSeq'),
                'FTCtrName' => $this->input->post('oetCstContactName'),
                'FTCtrEmail' => $this->input->post('oetCstContactEmail'),
                'FTCtrTel' => $this->input->post('oetCstContactTel'),
                'FTCtrFax' => $this->input->post('oetCstContactFax'),
                'FTCtrRmk' => $this->input->post('otaCstContactRmk'),
                'FTCreateBy'  => $this->session->userdata('tSesUsername'),
                'FDCreateOn'  => date('Y-m-d H:i:s'),
                'FTLastUpdBy' => $this->session->userdata('tSesUsername'),
                'FDLastUpdOn' => date('Y-m-d H:i:s'),
                'FNLngID' => $this->session->userdata('tLangEdit')

            );
            $this->db->trans_begin();
            $aStaCstContactMaster  = $this->mCustomer->FSaMCSTAddUpdateContact($aDataMaster);
            if($aStaCstContactMaster['rtRefId'] != 0){ // New Insert
                $aDataMaster['FTAddRefNo'] = $aStaCstContactMaster['rtRefId'];
            }

            if($this->db->trans_status() === false){
                $this->db->trans_rollback();
                $aReturn = array(
                    'nStaEvent'    => '900',
                    'tStaMessg'    => "Unsucess Add Event"
                );
            }else{
                $this->db->trans_commit();

                // Set return
                $aReturn = array(
                    'nStaCallBack'	=> $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn'	=> $aDataMaster['FTCstCode'],
                    'nStaEvent'	    => '1',
                    'tStaMessg'		=> 'Success Add Event'
                );
            }
            echo json_encode($aReturn);
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //ข้อมูลผู้ติดต่อลูกค้า
    public function FSvCSTContactDataList(){
        $nPage      = $this->input->post('nPageCurrent');
        $tSearchAll = $this->input->post('tSearchAll');
        if($nPage == '' || $nPage == null){$nPage = 1;}else{$nPage = $this->input->post('nPageCurrent');}
        if(!$tSearchAll){$tSearchAll='';}
        // Lang ภาษา
        $nLangResort    = $this->session->userdata("tLangID");
	    $nLangEdit      = $this->session->userdata("tLangEdit");
        $aData  = array(
            'FTCstCode'     => $this->input->post('tCstCode'),
            'nPage'         => $nPage,
            'nRow'          => 5,
            'FNLngID'       => $nLangEdit,
            'tSearchAll'    => $tSearchAll
        );

        $tAPIReq = "";
        $tMethodReq = "GET";
        $aResList = $this->mCustomer->FSaMCSTContactList($tAPIReq, $tMethodReq, $aData);

        $aGenTable = array(
            'aDataList' => $aResList,
            'nPage' => $nPage,
            'tSearchAll' => $tSearchAll
        );
        $this->load->view('customer/customer/wCustomerContactDataTable', $aGenTable);
    }

    //ลบข้อมูล
    public function FSaCSTDeleteContactEvent(){

       $tCtrName    = $this->input->post('tCtrName');
       $tCstCode    = $this->input->post('tCstCode');
       $tCtrSeq     = $this->input->post('tCtrSeq');
       $tCtrRefNo   = $this->input->post('tCtrRefNo');

       $aCst    = array(
            'FTCstCode'     => $tCstCode,
            'FNCtrSeq'      => $tCtrSeq,
            'FTAddRefNo'    => $tCtrRefNo
       );

        $this->mCustomer->FSnMCSTContactDel($aCst);
        echo json_encode($tCstCode);
    }

    //เพิ่มข้อมูลบัตร
    public function FSaCSTAddUpdateCardInfoEvent(){
        try{
            $aDataMaster = array(
                // Address
                'FTCstCode'         => $this->input->post('oetCstCode'), // Customer reference
                'FTCstCrdNo'        => $this->input->post('oetCstCardNo'),
                'FDCstApply'        => $this->input->post('oetCSTApply'),
                'FDCstCrdIssue'     => $this->input->post('oetCSTCardIssue'),
                'FDCstCrdExpire'    => $this->input->post('oetCSTCardExpire'),
                'FTBchCode'         => $this->input->post('oetCstCardBchCode'),
                'FTCstStaAge'       => empty($this->input->post('ocbCstCardStaAge')) ? 2 : $this->input->post('ocbCstCardStaAge'),
                'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                'FDLastUpdOn'       => date('Y-m-d H:i:s'),
                'FNLngID'           => $this->session->userdata('tLangEdit')
            );

            $aCstMaster =  $this->db->where('FTCstCode',$this->input->post('oetCstCode'))->get('TCNMCst')->row_array();
            ///กรณีลูกค้าใช้ระบบ Member จะมี กลุ่มบัตรใน TsysConfig ใช้ส่งใน MQ
            if(!empty($aCstMaster['FTAgnCode'])){
            $aDataCgp = $this->mCustomer->FScMCSTGetInfoMemberConFigSpc($aCstMaster['FTAgnCode'],1);
                if($aDataCgp['rtCode']=='1'){
                        $aDataMaster['FTCgpCode'] = $aDataCgp['raItem']['FTCfgStaUsrValue'];
                }
            }

            $this->db->trans_begin();
            $aStaCstMaster  = $this->mCustomer->FSaMCSTAddUpdateCardInfo($aDataMaster);

            if($this->db->trans_status() === false){
                $this->db->trans_rollback();
                $aReturn = array(
                    'nStaEvent'    => '900',
                    'tStaMessg'    => "Unsucess Add Event"
                );
            }else{
                $this->db->trans_commit();

                // Set return
                $aReturn = array(
                    'nStaCallBack'	=> $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn'	=> $aDataMaster['FTCstCode'],
                    'nStaEvent'	    => '1',
                    'tStaMessg'		=> 'Success Add Event'
                );
            ///---------------QMember-----------------------//
            $aQMemberParam = $this->FSaCCstFormatDataMemberV5($aDataMaster['FTCstCode']);


            $aMQParams = [
                "queueName" => "QMember",
                "exchangname" => "",
                "params" => $aQMemberParam
            ];
            $this->FSxCCSTSendDataMemberV5($aMQParams);
            }
            echo json_encode($aReturn);
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //เพิ่มข้อมูลเครดิต
    public function FSaCSTAddUpdateCreditEvent(){
        try{
            $aDataMaster = array(
                // Address
                'FTCstCode'         => $this->input->post('oetCstCode'), // Customer reference
                'FNCstCrTerm'       => $this->input->post('oetCstCreditTerm'),
                'FTCstStaAlwOrdMon' => empty($this->input->post('ocbCstStaAlwOrdMon')) ? 2 : $this->input->post('ocbCstStaAlwOrdMon'),
                'FTCstStaAlwOrdTue' => empty($this->input->post('ocbCstStaAlwOrdTue')) ? 2 : $this->input->post('ocbCstStaAlwOrdTue'),
                'FTCstStaAlwOrdWed' => empty($this->input->post('ocbCstStaAlwOrdWed')) ? 2 : $this->input->post('ocbCstStaAlwOrdWed'),
                'FTCstStaAlwOrdThu' => empty($this->input->post('ocbCstStaAlwOrdThu')) ? 2 : $this->input->post('ocbCstStaAlwOrdThu'),
                'FTCstStaAlwOrdFri' => empty($this->input->post('ocbCstStaAlwOrdFri')) ? 2 : $this->input->post('ocbCstStaAlwOrdFri'),
                'FTCstStaAlwOrdSat' => empty($this->input->post('ocbCstStaAlwOrdSat')) ? 2 : $this->input->post('ocbCstStaAlwOrdSat'),
                'FTCstStaAlwOrdSun' => empty($this->input->post('ocbCstStaAlwOrdSun')) ? 2 : $this->input->post('ocbCstStaAlwOrdSun'),
                'FTCstPayRmk'       => $this->input->post('otaCstPayRmk'),
                'FTCstBillRmk'      => $this->input->post('otaCstBillRmk'),
                'FTCstViaRmk'       => $this->input->post('otaCstViaRmk'),
                'FNCstViaTime'      => $this->input->post('oetCstViaTime'),
                'FTViaCode'         => $this->input->post('oetCstShipViaCode'),
                'FTCstTspPaid'      => $this->input->post('orbCstTspPaid'),
                'FTCstStaApv'       => empty($this->input->post('orbCstCreStaApv')) ? 2 : $this->input->post('orbCstCreStaApv'),
                'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                'FDLastUpdOn'       => date('Y-m-d H:i:s'),
                'FNLngID'            => $this->session->userdata('tLangEdit')
            );

            $this->db->trans_begin();
            $aStaCstMaster  = $this->mCustomer->FSaMCSTAddUpdateCredit($aDataMaster);

            if($this->db->trans_status() === false){
                $this->db->trans_rollback();
                $aReturn = array(
                    'nStaEvent'    => '900',
                    'tStaMessg'    => "Unsucess Add Event"
                );
            }else{
                $this->db->trans_commit();

                // Set return
                $aReturn = array(
                    'nStaCallBack'	=> $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn'	=> $aDataMaster['FTCstCode'],
                    'nStaEvent'	    => '1',
                    'tStaMessg'		=> 'Success Add Event'
                );
            }
            echo json_encode($aReturn);
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //เพิ่มข้อมูล RFID
    public function FSaCSTAddRfidEvent(){
        try{
            $aDataMaster = array(
                // Address
                'FTCstID'       => $this->input->post('ptCstID'),
                'FTCstCode'     => $this->input->post('ptCstCode'), // Customer reference
                'FTCrfName'     => $this->input->post('ptCrfName'),
                'FTLastUpdBy'   => $this->session->userdata('tSesUsername'),
                'FDLastUpdOn'   => date('Y-m-d H:i:s'),
                'FNLngID'       => $this->session->userdata('tLangEdit')
            );

            // $this->db->trans_begin();

            $this->mCustomer->FSaMCSTAddUpdateRfid($aDataMaster);
            $aCstDataTable  = $this->mCustomer->FSaMCSTRfidDataTable($aDataMaster);
            $aDataEdit         = array(
                'aResult'       => $aCstDataTable,
                'tCstCode'      => $aDataMaster['FTCstCode']
            );
            $this->load->view('customer/customer/tab/wCstTabIdRfid', $aDataEdit);
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //อัพเดทข้อมูล RFID
    public function FSaCSTUpdateRfidEvent(){
        try{
            $aDataMaster = array(
                'FTCstID'       => $this->input->post('ptCstID'),
                'FTCstCode'     => $this->input->post('ptCstCode'),
                'tEditCstID'    => $this->input->post('ptEditCstID'),
                'tEditCrfName'  => $this->input->post('ptEditCrfName'),
                'FNLngID'       => $this->session->userdata('tLangEdit')
            );

            $this->mCustomer->FSaMCSTUpdateRfid($aDataMaster);
            $aCstDataTable  = $this->mCustomer->FSaMCSTRfidDataTable($aDataMaster);
            $aDataEdit         = array(
                'aResult'       => $aCstDataTable,
                'tCstCode'      => $aDataMaster['FTCstCode']
            );
            $this->load->view('customer/customer/tab/wCstTabIdRfid', $aDataEdit);

        }catch(Exception $Error){
            echo $Error;
        }
    }

    //ลบข้อมูล RFID
    public function FSaCSTDeleteRfidEvent(){

        $tCstCode   = $this->input->post('ptCstCode');
        $tCstID     = $this->input->post('ptCstID');
        $aDataMaster = array(

            'FTCstID'       => $this->input->post('ptCstID'),
            'FTCstCode'     => $this->input->post('ptCstCode'),
            'FNLngID'       => $this->session->userdata('tLangEdit')
        );
        $aResDel            = $this->mCustomer->FSnMCSTDeleteRfid($aDataMaster);
        $aCstDataTable      = $this->mCustomer->FSaMCSTRfidDataTable($aDataMaster);
        $aDataEdit          = array(
            'aResult'       => $aCstDataTable,
            'tCstCode'      => $aDataMaster['FTCstCode']
        );
        $this->load->view('customer/customer/tab/wCstTabIdRfid', $aDataEdit);
    }

    //Validate
    public function FStCSTUniqueValidate($tSelect = ''){

        if($this->input->is_ajax_request()){ // Request check
            if($tSelect == 'cstcode'){

                $tCstCode = $this->input->post('tCstCode');
                $oCustomer = $this->mCustomer->FSoMCSTCheckDuplicate($tCstCode);

                $tStatus = 'false';
                if($oCustomer[0]->counts > 0){ // If have record
                    $tStatus = 'true';
                }
                echo $tStatus;

                return;
            }
            echo 'Param not match.';
        }else{
            echo 'Method Not Allowed';
        }

    }

    //ลบหลายตัว
    public function FSoCSTDeleteMulti(){
        $tIDCode = $this->input->post('tIDCode');

        if(!empty($tIDCode)){
            foreach($tIDCode as $tCstCode){
                ///---------------QMember-----------------------//
                $aQMemberParam = $this->FSaCCstFormatDataDeleteMemberV5($tCstCode);
                $aMQParams = [
                    "queueName" => "QMember",
                    "exchangname" => "",
                    "params" => $aQMemberParam
                ];
                $this->FSxCCSTSendDataMemberV5($aMQParams);
            }
        }
		$aDataMaster = array(
			'FTCstCode' => $tIDCode
        );
        $aResDel   = $this->mCustomer->FSnMCSTDel($aDataMaster);

        //Delete Face
        for($i=0; $i<FCNnHSizeOf($tIDCode); $i++){
            $tID = trim($tIDCode[$i]);
            require_once APPPATH.'modules\customer\controllers\customerRegisFace\cCustomerRegisFace.php';
            $oRegisterFace = new cCustomerRegisFace();
            $oRegisterFace->FSaCstRGFDeleteImageByID($tID);
        }

        echo json_encode($aResDel);
    }

    //ลบข้อมูลลูกค้า
    public function FSoCSTDelete(){
        $tCstCode = $this->input->post('tCstCode');

        ///---------------QMember-----------------------//
        $aQMemberParam = $this->FSaCCstFormatDataDeleteMemberV5($tCstCode);
        $aMQParams = [
            "queueName" => "QMember",
            "exchangname" => "",
            "params" => $aQMemberParam
        ];
        $this->FSxCCSTSendDataMemberV5($aMQParams);

        $aCst = ['FTCstCode' => $tCstCode];
        $this->mCustomer->FSnMCSTDel($aCst);
        echo json_encode($tCstCode);
    }

    //Member
    public function FSaCCstFormatDataMemberV5($ptCstCode){


            $aCstMaster =  $this->db->where('FTCstCode',$ptCstCode)->get('TCNMCst')->row_array();
            $aCstCard_L = $this->db->where('FTCstCode',$ptCstCode)->get('TCNMCstCard')->row_array();
             ///กรณีลูกค้าใช้ระบบ Member จะมี กลุ่มบัตรใน TsysConfig ใช้ส่งใน MQ
            if(!empty($aCstMaster['FTAgnCode'])){
                $aDataCgp = $this->mCustomer->FScMCSTGetInfoMemberConFigSpc($aCstMaster['FTAgnCode'],1);
                    if($aDataCgp['rtCode']=='1'){
                            $tCgpCode = $aDataCgp['raItem']['FTCfgStaUsrValue'];
                    }
                $aDataBch = $this->mCustomer->FScMCSTGetInfoMemberConFigSpc($aCstMaster['FTAgnCode'],2);
                if($aDataBch['rtCode']=='1'){
                             $tBchCenter = $aDataBch['raItem']['FTCfgStaUsrValue'];
                 }
            }

            if(empty($tCgpCode)){
                $tCgpCode   = $this->db->where('FTSysCode','AMQMember')->where('FTSysSeq',1)->get('TSysConfig')->row_array()['FTSysStaUsrValue'];
            }
            if(empty($tBchCenter)){
                $tBchCenter = $this->db->where('FTSysCode','AMQMember')->where('FTSysSeq',2)->get('TSysConfig')->row_array()['FTSysStaUsrValue'];
            }

            // echo 'tCgpCode=>'.$tCgpCode;
            // echo 'tBchCenter=>'.$tBchCenter;
            // die();
           $aoTCNMMember = array(
               'FTCgpCode' => $tCgpCode,
               'FTMemCode' => $aCstMaster['FTCstCode'],
               'FTMemCardID' => $aCstMaster['FTCstCardID'],
               'FTMemTaxNo' => $aCstMaster['FTCstTaxNo'],
               'FTMemTel' => $aCstMaster['FTCstTel'],
               'FTMemFax' => $aCstMaster['FTCstFax'],
               'FTMemEmail' => $aCstMaster['FTCstEmail'],
               'FTMemSex' => $aCstMaster['FTCstSex'],
               'FDMemDob' => $aCstMaster['FDCstDob'],
               'FTOcpCode' => $aCstMaster['FTOcpCode'],
               'FTMemBusiness' => $aCstMaster['FTCstBusiness'],
               'FTMemBchHQ' => $aCstMaster['FTCstBchHQ'],
               'FTMemBchCode' => $aCstMaster['FTCstBchCode'],
               'FTMemStaActive' => $aCstMaster['FTCstStaActive'],
               'FDLastUpdOn' => $aCstMaster['FDLastUpdOn'],
               'FTLastUpdBy' => $aCstMaster['FTLastUpdBy'],
               'FDCreateOn' => $aCstMaster['FDCreateOn'],
               'FTCreateBy' => $aCstMaster['FTCreateBy'],
           );

           $aoTCNMMember_L = $this->mCustomer->FSaMCSTGetMasterLang4MQ($tCgpCode,$ptCstCode);
           $aoTCNMMemberAddress_L = $this->mCustomer->FSaMCSTGetAddress4MQ($tCgpCode,$ptCstCode);

           $aoTCNMMemCard = array(
               'FTCgpCode'  => $tCgpCode,
               'FTMemCode'  => $aCstCard_L['FTCstCode'],
               'FTMemCrdNo'  => $aCstCard_L['FTCstCrdNo'],
               'FDMemApply'  => $aCstCard_L['FDCstApply'],
               'FDMemCrdIssue'  => $aCstCard_L['FDCstCrdIssue'],
               'FDMemCrdExpire'  => $aCstCard_L['FDCstCrdExpire'],
           );

           $ptUpdData = array(
            'aoTCNMMember' => ($aoTCNMMember) ? array($aoTCNMMember) : NULL,
            'aoTCNMMember_L' => ($aoTCNMMember_L) ? $aoTCNMMember_L : NULL ,
            'aoTCNMMemCard' => ($aoTCNMMemCard) ? array($aoTCNMMemCard) : NULL,
            'aoTCNMMemAddress_L' => ($aoTCNMMemberAddress_L) ? $aoTCNMMemberAddress_L : NULL,
           );
           $aMemberParam = array(
               'ptFunction' => 'UPDATE_MEMBER',
               'ptSource' => $tBchCenter,
               'ptDest' => 'CENTER',
               'ptDelObj' => '',
               'ptUpdData' => json_encode($ptUpdData)
           );

        //    print_r($aMemberParam);
        //    die();
           return $aMemberParam;
    }

    //Member
    public function FSaCCstFormatDataDeleteMemberV5($ptCstCode){
        $tBchCenter = $this->db->where('FTSysCode','AMQMember')->where('FTSysSeq',2)->get('TSysConfig')->row_array()['FTSysStaUsrValue'];
        $aCstMaster =  $this->db->where('FTCstCode',$ptCstCode)->get('TCNMCst')->row_array();
        if(!empty($aCstMaster['FTAgnCode'])){
            $aDataBch = $this->mCustomer->FScMCSTGetInfoMemberConFigSpc($aCstMaster['FTAgnCode'],2);
            if($aDataBch['rtCode']=='1'){
             $tBchCenter = $aDataBch['raItem']['FTCfgStaUsrValue'];
             }
        }

        $dDelDate = date('Y-m-d H:i:s');
        $aMemberParam = array(
            'ptFunction' => 'UPDATE_MEMBER',
            'ptSource' => $tBchCenter,
            'ptDest' => 'CENTER',
            'ptDelObj' => "{\"FTDelTable\": \"TCNMMember\",\"FDDelDate\": \"$dDelDate\",\"FTDelRefValue\": \"$ptCstCode\"}",
            'ptUpdData' => ''
        );

       return $aMemberParam;
    }

    //Member
    public function FSxCCSTSendDataMemberV5($paParams){
        $tQueueName             = $paParams['queueName'];
        $aParams                = $paParams['params'];
        $tExchange              = EXCHANGE; // This use default exchange
        return 1;
    }



}
