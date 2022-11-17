<?php
/**
 * 
 * @param type $paParams
 * @return type
 */
function FCNaGetCompanyInfo($paParams = []){
    $ci = &get_instance();
    $ci->load->model('company/company/mCompany');

    $aCompParams = [
        'nLngID' => $paParams['nLngID'],
        'tBchCode' => $paParams['tBchCode']
    ];
    
    return $ci->mCompany->FSaMCMPGetCompanyInfo($aCompParams);
}

/**
 * 
 * @param type $paParams
 * @return type
 */
function FCNaGetBranchInfo($paParams = []){
    $ci = &get_instance();
    $ci->load->model('company/branch/mBranch');

     $aBchParams = [
        'nLngID' => $paParams['nLngID'],
        'tBchCode' => $paParams['tBchCode']
    ];
    
    return $ci->mBranch->FSaMCMPGetBchInfo($aBchParams);
}

/**
 * 
 * @param type $paParams
 * @return type
 */
function FCNtGetCompanyCode(){
    $ci = &get_instance();
    $ci->load->model('company/company/mCompany');
    $aCompany = $ci->mCompany->FSaMCMPGetCompanyCode();
    
    $tCompanyCode = "Company Code Not Found.";
    if($aCompany['rtCode'] == '1') {
        $tCompanyCode = $aCompany['raItems']['FTCmpCode'];
    }
    return $tCompanyCode;
}



/**
 * 
 * @param type $paParams
 * @return type
 */
function FCNaGetCompanyForDocument(){
    $ci = &get_instance();
    $ci->load->model('company/company/mCompany');
    $ci->load->model('payment/rate/mRate');
    $nLangEdit = $ci->session->userdata("tLangEdit");
    $aDataReturn = array();

    if(!empty($ci->session->userdata('tSesUsrAgnCode'))){
        $tSesUsrAgnCode = $ci->session->userdata('tSesUsrAgnCode');
        $tSQL ="SELECT
                    AGN.FTAgnCode,
                    AGN.FTBchCode,
                    AGNSPC.FTCmpVatInOrEx,
                    AGNSPC.FTRteCode,
                    AGNSPC.FTVatCode
                FROM
                    TCNMAgency AGN WITH(NOLOCK)
                LEFT OUTER JOIN TCNMAgencySpc AGNSPC WITH(NOLOCK) ON AGN.FTAgnCode = AGNSPC.FTAgnCode
                WHERE
                    AGN.FTAgnCode = '$tSesUsrAgnCode'
        ";
         $oQuery = $ci->db->query($tSQL);
         if ($oQuery->num_rows() > 0) {
            $oRes  = $oQuery->row_array();
            $aDataReturn['tBchCode'] = $oRes['FTBchCode'];
            $aDataReturn['tCmpRteCode'] = $oRes['FTRteCode'];
            $aDataReturn['tVatCode'] = $oRes['FTVatCode'];
            $aDataReturn['tCmpRetInOrEx'] = $oRes['FTCmpVatInOrEx'];
        } else {
            $oRes = NULL;
            $aDataReturn['tBchCode'] = FCNtGetBchInComp();
            $aDataReturn['tCmpRteCode'] = '';
            $aDataReturn['tVatCode'] = '';
            $aDataReturn['tCmpRetInOrEx'] = '1';
        }
    }else{
            $aDataWhere = array(
                'FNLngID' => $nLangEdit
            );
            $tAPOReq = "";
            $tMethodReq = "GET";
            $aCompData = $ci->mCompany->FSaMCMPList($tAPOReq, $tMethodReq, $aDataWhere);
            $aDataReturn['tBchCode'] = $aCompData['raItems']['rtCmpBchCode'];
            $aDataReturn['tCmpRteCode'] = $aCompData['raItems']['rtCmpRteCode'];
            $aDataReturn['tVatCode'] = $aCompData['raItems']['rtVatCodeUse'];
            $aDataReturn['tCmpRetInOrEx'] = $aCompData['raItems']['rtCmpRetInOrEx'];

    }

    $aVatRate = FCNoHCallVatlist($aDataReturn['tVatCode']);
    if (isset($aVatRate) && !empty($aVatRate)) {
        $aDataReturn['cVatRate'] = $aVatRate['FCVatRate'][0];
    } else {
        $aDataReturn['cVatRate'] = "";
    }
    $aDataRate = array(
        'FTRteCode' => $aDataReturn['tCmpRteCode'],
        'FNLngID' => $nLangEdit
    );
    $aResultRte = $ci->mRate->FSaMRTESearchByID($aDataRate);
    if (isset($aResultRte) && $aResultRte['rtCode']) {
        $aDataReturn['cXthRteFac'] = $aResultRte['raItems']['rcRteRate'];
    } else {
        $aDataReturn['cXthRteFac'] = "";
    }
 
    return $aDataReturn;
}


/**
 * 
 * @param type $paParams
 * @return type
 */
Function FCNtGetCompanyGroupMember(){
    $ci = &get_instance();
    $ci->load->model('customer/customer/mCustomer');

    ///กรณีลูกค้าใช้ระบบ Member จะมี กลุ่มบัตรใน TsysConfig ใช้ส่งใน MQ
    $tCgpCode   = $ci->db->where('FTSysCode','AMQMember')->where('FTSysSeq',1)->get('TSysConfig')->row_array()['FTSysStaUsrValue'];
    if(!empty($ci->session->userdata('tSesUsrAgnCode'))){
    $aDataCgp = $ci->mCustomer->FScMCSTGetInfoMemberConFigSpc($ci->session->userdata('tSesUsrAgnCode'),1);
        if($aDataCgp['rtCode']=='1'){
                $tCgpCode = $aDataCgp['raItem']['FTCfgStaUsrValue'];
        }
    }

    return $tCgpCode;
}



/**
 * Functionality : แจ้งเตือนการยกเลิกการจองสินค้า
 * Parameters : 
 * Creator : 14/11/2022 Nale
 * Last Modified : -
 * Return : status
*/
function FCNaNotiCancelBookingStock(){
    $ci = &get_instance();

    $tSQL="SELECT LCB.FDLcbDate
            FROM TCNTLastCancelBookingTmp LCB WITH (NOLOCK) 
            WHERE LCB.FNLcbType = 1 
            AND CONVERT(VARCHAR(10),LCB.FDLcbDate,120) = CONVERT(VARCHAR(10),GETDATE(),120)
            ";
    $oQuery = $ci->db->query($tSQL);
    if ($oQuery->num_rows() == 0) {//ถ้าตรวจสอบวันที่การซิงค์ของวันปัจจุบันยังไม่ถูกทำให้เข้าเงื่อนไข
        $ci->db->set('FDLcbDate', date('Y-m-d'));
        $ci->db->update('TCNTLastCancelBookingTmp');
        if($ci->db->affected_rows() > 0){
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'Update Success',
            );
        }else{
            $ci->db->insert('TCNTLastCancelBookingTmp',array('FDLcbDate'=>date('Y-m-d') , 'FNLcbType'=> 1));
            if($ci->db->affected_rows() > 0 ){
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Add Success',
                );
            }else{
                $aStatus = array(
                    'rtCode' => '905',
                    'rtDesc' => 'Error Cannot Add.',
                );
            }
        }

        $tSQL1="SELECT
                HD.FTBchCode,
                BCH_L.FTBchName,
                HD.FTXshDocNo,
                HD.FDXshDocDate,
                HD.FDXshBookDate,
                HD.FDXshTimeStart,
                HD.FDXshTimeStop
            FROM
                TSVTBookHD HD WITH (NOLOCK)
            LEFT JOIN TCNMBranch_L BCH_L WITH (NOLOCK) ON HD.FTBchCode = BCH_L.FTBchCode AND BCH_L.FNLngID = 1
            LEFT JOIN TSVTBookHDDocRef REF WITH (NOLOCK) ON HD.FTBchCode = REF.FTBchCode AND HD.FTXshDocNo = REF.FTXshDocNo
            WHERE HD.FDXshTimeStop<CONVERT(VARCHAR(10),GETDATE(),120)
                AND HD.FTXshStaApv = '1' 
                AND HD.FTXshStaDoc = '1'
                AND ISNULL(REF.FTXshDocNo,'') = '' ";
            $oQuery1 = $ci->db->query($tSQL1);
            //คิวรี่หาการจองที่ลูกค้าไม่มาตามนัดคือเลยวันนัดมาแล้ว
            if ($oQuery1->num_rows() > 0) {//พบข้อมูลที่มีการจองแต่เลยนัด
                $oQuery1 = $oQuery1->result_array();
                $tNotiID = '';
                foreach($oQuery1 as $aData){
                    // ส่ง Massage Noti
                    $aTCNTNotiSpc[] = array(
                        "FNNotID"       => $tNotiID,
                        "FTNotType"    => '1',
                        "FTNotStaType" => '1',
                        "FTAgnCode"    => '',
                        "FTAgnName"    => '',
                        "FTBchCode"    => $aData['FTBchCode'],
                        "FTBchName"    => $aData['FTBchName'],
                    );
      
                    $aMQParamsNoti = [
                        "queueName" => "CN_SendToNoti",
                        "tVhostType" => "NOT",
                        "params"    => [
                                        "oaTCNTNoti" => array(
                                                        "FNNotID"       => $tNotiID,
                                                        "FTNotCode"     => '00019',
                                                        "FTNotKey"      => 'TSVTBookHD',
                                                        "FTNotBchRef"    => $aData['FTBchCode'],
                                                        "FTNotDocRef"   => $aData['FTXshDocNo'],
                                        ),
                                        "oaTCNTNoti_L" => array(
                                                            0 => array(
                                                                "FNNotID"       => $tNotiID,
                                                                "FNLngID"       => 1,
                                                                "FTNotDesc1"    => 'เอกสารการจองเลยกำหนด #'.$aData['FTXshDocNo'],
                                                                "FTNotDesc2"    => 'รหัสสาขา '.$aData['FTBchCode'].' รอยกเลิกการจอง ',
                                                            ),
                                                            1 => array(
                                                                "FNNotID"       => $tNotiID,
                                                                "FNLngID"       => 2,
                                                                "FTNotDesc1"    => 'Booking document late #'.$aData['FTXshDocNo'],
                                                                "FTNotDesc2"    => 'Branch code '.$aData['FTBchCode'].' Wait Cancel Booking ',
                                                            )
                                        ),
                                        "oaTCNTNotiAct" => array(
                                                            0 => array( 
                                                                    "FNNotID"       => $tNotiID,
                                                                    "FDNoaDateInsert" => date('Y-m-d H:i:s'),
                                                                    "FTNoaDesc"          => 'รหัสสาขา '.$aData['FTBchCode'].' รอยกเลิกการจอง ',
                                                                    "FTNoaDocRef"    => $aData['FTXshDocNo'],
                                                                    "FNNoaUrlType"   =>  1,
                                                                    "FTNoaUrlRef"    => 'docBookingCalendar/0/0',
                                                                    ),
                                            ), 
                                        "oaTCNTNotiSpc" => $aTCNTNotiSpc,
                            "ptUser"  => $ci->session->userdata('tSesUsername'),
                        ]
                    ];
                    FCNxCallRabbitMQ($aMQParamsNoti);
                }
                $aDataResult = array(
                    'rtCode' => '200' ,
                    'rtDesc' => 'Success CanCeled Bookking of '.date('Y-m-d')
                );
            }else{
                $aDataResult = array(
                    'rtCode' => '200' ,
                    'rtDesc' => 'Not Found Data '
                );
            }
    }else{
            $aDataResult = array(
                'rtCode' => '200' ,
                'rtDesc' => 'CanCeled Bookking of '.date('Y-m-d')
            );
          
    }
    return $aDataResult;
}