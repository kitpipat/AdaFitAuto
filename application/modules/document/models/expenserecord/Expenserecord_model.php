<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Expenserecord_model extends CI_Model {

    // Functionality    : Get Data Purchase Invoice HD List
    // Parameters       : function parameters
    // Creator          : 19/06/2018 wasin (Yoshi AKA: Mr.JW)
    // Last Modified    : 24/02/2021 supawat 
    // Return           : Data Array
    // Return Type      : Array
    public function FSaMPXGetDataTableList($paDataCondition){
        $aRowLen                = FCNaHCallLenData($paDataCondition['nRow'],$paDataCondition['nPage']);
        $nLngID                 = $paDataCondition['FNLngID'];
        $aDatSessionUserLogIn   = $paDataCondition['aDatSessionUserLogIn'];
        $aAdvanceSearch         = $paDataCondition['aAdvanceSearch'];

        // Advance Search
        $tSearchList            = $aAdvanceSearch['tSearchAll'];
        $tSearchBchCodeFrom     = $aAdvanceSearch['tSearchBchCodeFrom'];
        $tSearchBchCodeTo       = $aAdvanceSearch['tSearchBchCodeTo'];
        $tSearchDocDateFrom     = $aAdvanceSearch['tSearchDocDateFrom'];
        $tSearchDocDateTo       = $aAdvanceSearch['tSearchDocDateTo'];
        $tSearchStaDoc          = $aAdvanceSearch['tSearchStaDoc'];
        // $tSearchStaPrcStk       = $aAdvanceSearch['tSearchStaPrcStk'];

        $tSQLFirst   = "SELECT c.* FROM ( SELECT ROW_NUMBER() OVER(ORDER BY FDCreateOn DESC) AS FNRowID, * FROM (";
        $tSQLContent = "    SELECT DISTINCT
                                PXHD.FTBchCode,
                                BCHL.FTBchName,
                                PXHD.FTXphDocNo,
                                CONVERT(CHAR(10),PXHD.FDXphDocDate,103) AS FDXphDocDate,
                                CONVERT(CHAR(5), PXHD.FDXphDocDate,108) AS FTXphDocTime,
                                PXHD.FTXphStaDoc,
                                PXHD.FTXphStaApv,
                                PXHD.FTCreateBy,
                                USRL.FTUsrName      AS FTCreateByName,
                                PXHD.FTXphApvCode,
                                USRLAPV.FTUsrName   AS FTXphApvName,
                                PXHD.FDCreateOn
                            FROM TAPTPxHD           PXHD    WITH (NOLOCK)
                            LEFT JOIN TCNMBranch_L  BCHL    WITH (NOLOCK) ON PXHD.FTBchCode     = BCHL.FTBchCode    AND BCHL.FNLngID    = $nLngID
                            LEFT JOIN TCNMUser_L    USRL    WITH (NOLOCK) ON PXHD.FTCreateBy    = USRL.FTUsrCode    AND USRL.FNLngID    = $nLngID
                            LEFT JOIN TCNMUser_L    USRLAPV WITH (NOLOCK) ON PXHD.FTXphApvCode  = USRLAPV.FTUsrCode AND USRLAPV.FNLngID = $nLngID
                            WHERE 1=1
        ";

        // Check User Login Branch
        if( $this->session->userdata("tSesUsrLevel") != "HQ" ){
            $tBchMulti = $this->session->userdata("tSesUsrBchCodeMulti");
            $tSQLContent .= " AND PXHD.FTBchCode IN (".$tBchMulti.") ";
        }

        // ?????????????????????????????????????????????,?????????????????????,????????????????????????????????????
        if(isset($tSearchList) && !empty($tSearchList)){
            $tSQLContent .= " AND ((PXHD.FTXphDocNo LIKE '%$tSearchList%') OR (BCHL.FTBchName LIKE '%$tSearchList%') OR (CONVERT(CHAR(10),PXHD.FDXphDocDate,103) LIKE '%$tSearchList%'))";
        }
        
        // ???????????????????????????????????? - ?????????????????????
        if(!empty($tSearchBchCodeFrom) && !empty($tSearchBchCodeTo)){
            $tSQLContent .= " AND ((PXHD.FTBchCode BETWEEN '$tSearchBchCodeFrom' AND '$tSearchBchCodeTo') OR (PXHD.FTBchCode BETWEEN '$tSearchBchCodeTo' AND '$tSearchBchCodeFrom'))";
        }

        // ?????????????????????????????????????????? - ???????????????????????????
        if(!empty($tSearchDocDateFrom) && !empty($tSearchDocDateTo)){
            $tSQLContent .= " AND ((PXHD.FDXphDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateFrom 00:00:00') AND CONVERT(datetime,'$tSearchDocDateTo 23:59:59')) OR (PXHD.FDXphDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateTo 23:00:00') AND CONVERT(datetime,'$tSearchDocDateFrom 00:00:00')))";
        }


        // ?????????????????????????????????
        if (!empty($tSearchStaDoc) && ($tSearchStaDoc != "0")) {
            if ($tSearchStaDoc == 3) {
                $tSQLContent .= " AND PXHD.FTXphStaDoc = '$tSearchStaDoc'";
            } elseif ($tSearchStaDoc == 2) {
                $tSQLContent .= " AND ISNULL(PXHD.FTXphStaApv,'') = '' AND PXHD.FTXphStaDoc != '3'";
            } elseif ($tSearchStaDoc == 1) {
                $tSQLContent .= " AND PXHD.FTXphStaApv = '$tSearchStaDoc'";
            }
        }        

        // ????????????????????????????????????????????????????????????
        $tSearchStaDocAct = $aAdvanceSearch['tSearchStaDocAct'];
        if (!empty($tSearchStaDocAct) && ($tSearchStaDocAct != "0")) {
            if ($tSearchStaDocAct == 1) {
                $tSQLContent .= " AND PXHD.FNXphStaDocAct = 1 ";
            } else {
                $tSQLContent .= " AND PXHD.FNXphStaDocAct <> 1 ";
            }
        }

        $tSQLEnd =  ") Base) AS c WHERE c.FNRowID > $aRowLen[0] AND c.FNRowID <= $aRowLen[1]";

        $tFullQuery = $tSQLFirst.$tSQLContent.$tSQLEnd;

        $oQuery = $this->db->query($tFullQuery);
        if( $oQuery->num_rows() > 0 ){
            // $oDataList          = $oQuery->result_array();
            // $aDataCountAllRow   = $this->FSnMPXCountPageDocListAll($paDataCondition);
            // $nFoundRow          = ($aDataCountAllRow['rtCode'] == '1')? $aDataCountAllRow['rtCountData'] : 0;

            $oQueryRow          = $this->db->query($tSQLContent);
            $nFoundRow          = ($oQueryRow->num_rows() > 0 ? $oQueryRow->num_rows() : 0);

            $nPageAll           = ceil($nFoundRow/$paDataCondition['nRow']);
            $aResult = array(
                'raItems'       => $oQuery->result_array(),
                'rnAllRow'      => $nFoundRow,
                'rnCurrentPage' => $paDataCondition['nPage'],
                'rnAllPage'     => $nPageAll,
                'rtCode'        => '1',
                'rtDesc'        => 'success',
            );
        }else{
            $aResult = array(
                'rnAllRow'      => 0,
                'rnCurrentPage' => $paDataCondition['nPage'],
                "rnAllPage"     => 0,
                'rtCode'        => '800',
                'rtDesc'        => 'data not found',
            );
        }
        unset($oQuery);
        unset($oDataList);
        unset($aDataCountAllRow);
        unset($nFoundRow);
        unset($nPageAll);
        return $aResult;
    }

    // Functionality    : Data Get Data Page All
    // Parameters       : function parameters
    // Creator          : 19/06/2018 wasin (Yoshi AKA: Mr.JW)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Data Array
    // Return Type      : Array
    public function FSnMPXCountPageDocListAll($paDataCondition){
        $nLngID                 = $paDataCondition['FNLngID'];
        $aDatSessionUserLogIn   = $paDataCondition['aDatSessionUserLogIn'];
        $aAdvanceSearch         = $paDataCondition['aAdvanceSearch'];
        // Advance Search
        $tSearchList        = $aAdvanceSearch['tSearchAll'];
        $tSearchBchCodeFrom = $aAdvanceSearch['tSearchBchCodeFrom'];
        $tSearchBchCodeTo   = $aAdvanceSearch['tSearchBchCodeTo'];
        $tSearchDocDateFrom = $aAdvanceSearch['tSearchDocDateFrom'];
        $tSearchDocDateTo   = $aAdvanceSearch['tSearchDocDateTo'];
        $tSearchStaDoc      = $aAdvanceSearch['tSearchStaDoc'];
        // $tSearchStaApprove  = $aAdvanceSearch['tSearchStaApprove'];
        // $tSearchStaPrcStk   = $aAdvanceSearch['tSearchStaPrcStk'];

        $tSQL   =   "   SELECT COUNT (PXHD.FTXphDocNo) AS counts
                        FROM TAPTPxHD PXHD WITH (NOLOCK)
                        LEFT JOIN TCNMBranch_L BCHL WITH (NOLOCK) ON PXHD.FTBchCode = BCHL.FTBchCode AND BCHL.FNLngID = $nLngID
                        WHERE 1=1 AND PXHD.FNXphDocType != 1 
                    ";

         // Check User Login Branch
         if( $this->session->userdata("tSesUsrLevel") != "HQ" ){
            $tBchMulti = $this->session->userdata("tSesUsrBchCodeMulti");
            $tSQL .= " AND PXHD.FTBchCode IN (".$tBchMulti.") ";
        }
        
        // Check User Login Shop
        if(isset($aDatSessionUserLogIn['FTShpCode']) && !empty($aDatSessionUserLogIn['FTShpCode'])){
            $tUserLoginShpCode  = $aDatSessionUserLogIn['FTShpCode'];
            $tSQL   .= " AND PXHD.FTShpCode = '$tUserLoginShpCode' ";
        }
        
        // ???????????????????????????????????????,?????????????????????,????????????????????????????????????
        if(isset($tSearchList) && !empty($tSearchList)){
            $tSQL .= " AND ((PXHD.FTXphDocNo LIKE '%$tSearchList%') OR (BCHL.FTBchName LIKE '%$tSearchList%') OR (CONVERT(CHAR(10),PXHD.FDXphDocDate,103) LIKE '%$tSearchList%'))";
        }
        
        // ???????????????????????????????????? - ?????????????????????
        if(!empty($tSearchBchCodeFrom) && !empty($tSearchBchCodeTo)){
            $tSQL .= " AND ((PXHD.FTBchCode BETWEEN '$tSearchBchCodeFrom' AND '$tSearchBchCodeTo') OR (PXHD.FTBchCode BETWEEN '$tSearchBchCodeTo' AND '$tSearchBchCodeFrom'))";
        }

        // ?????????????????????????????????????????? - ???????????????????????????
        if(!empty($tSearchDocDateFrom) && !empty($tSearchDocDateTo)){
            $tSQL .= " AND ((PXHD.FDXphDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateFrom 00:00:00') AND CONVERT(datetime,'$tSearchDocDateTo 23:59:59')) OR (PXHD.FDXphDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateTo 23:00:00') AND CONVERT(datetime,'$tSearchDocDateFrom 00:00:00')))";
        }

        // ?????????????????????????????????
        if (!empty($tSearchStaDoc) && ($tSearchStaDoc != "0")) {
            if ($tSearchStaDoc == 3) {
                $tSQL .= " AND PXHD.FTXphStaDoc = '$tSearchStaDoc'";
            } elseif ($tSearchStaDoc == 2) {
                $tSQL .= " AND ISNULL(PXHD.FTXphStaApv,'') = '' AND PXHD.FTXphStaDoc != '3'";
            } elseif ($tSearchStaDoc == 1) {
                $tSQL .= " AND PXHD.FTXphStaApv = '$tSearchStaDoc'";
            }
        }        

        // ??????????????????????????????????????????????????????
        // if (isset($tSearchStaPrcStk) && !empty($tSearchStaPrcStk)) {
        //     if ($tSearchStaPrcStk == 3) {
        //         $tSQL .= " AND (PXHD.FTXphStaPrcStk = '$tSearchStaPrcStk' OR ISNULL(PXHD.FTXphStaPrcStk,'') = '') ";
        //     } else {
        //         $tSQL .= " AND PXHD.FTXphStaPrcStk = '$tSearchStaPrcStk'";
        //     }
        // }
        // ????????????????????????????????????????????????????????????
        $tSearchStaDocAct = $aAdvanceSearch['tSearchStaDocAct'];
        if (!empty($tSearchStaDocAct) && ($tSearchStaDocAct != "0")) {
            if ($tSearchStaDocAct == 1) {
                $tSQL .= " AND PXHD.FNXphStaDocAct = 1";
            } else {
                $tSQL .= " AND PXHD.FNXphStaDocAct = 0";
            }
        }
        
        $oQuery = $this->db->query($tSQL);

        if($oQuery->num_rows() > 0) {
            $aDetail        = $oQuery->row_array();
            $aDataReturn    =  array(
                'rtCountData'   => $aDetail['counts'],
                'rtCode'        => '1',
                'rtDesc'        => 'success',
            );
        }else{
            $aDataReturn    =  array(
                'rtCode'        => '800',
                'rtDesc'        => 'Data Not Found',
            );
        }
        unset($oQuery);
        unset($aDetail);
        return $aDataReturn;
    }

    // Functionality    : Delete Purchase Invoice Document
    // Parameters       : function parameters
    // Creator          : 19/06/2018 wasin (Yoshi AKA: Mr.JW)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Status Delete
    // Return Type      : array
    public function FSnMPXDelDocument($paDataDoc){
        $tDataDocNo = $paDataDoc['tDataDocNo'];
        $this->db->trans_begin();

        // Document HD
        $this->db->where_in('FTXphDocNo',$tDataDocNo);
        $this->db->delete('TAPTPxHD');

        // Document HD Doc Ref
        $this->db->where_in('FTXphDocNo',$tDataDocNo);
        $this->db->delete('TAPTPxHDDocRef');
    
        // Document HD Spl
        $this->db->where_in('FTXphDocNo',$tDataDocNo);
        $this->db->delete('TAPTPxHDSpl');

        // Document HD Discount
        $this->db->where_in('FTXphDocNo',$tDataDocNo);
        $this->db->delete('TAPTPxHDDis');
        
        // Document DT
        $this->db->where_in('FTXphDocNo',$tDataDocNo);
        $this->db->delete('TAPTPxDT');

        // Document DT Discount
        $this->db->where_in('FTXphDocNo',$tDataDocNo);
        $this->db->delete('TAPTPxDTDis');

        // Document DT Discount
        $this->db->where_in('FTXshDocNo',$tDataDocNo);
        $this->db->delete('TAPTPxRC');

        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $aStaDelDoc     = array(
                'rtCode'    => '905',
                'rtDesc'    => 'Cannot Delete Item.',
            );
        }else{
            $this->db->trans_commit();
            $aStaDelDoc     = array(
                'rtCode'    => '1',
                'rtDesc'    => 'Delete Complete.',
            );
        }
        return $aStaDelDoc;
    }

    // Functionality    : Delete Purchase Invoice Document
    // Parameters       : function parameters
    // Creator          : 24/06/2018 wasin (Yoshi AKA: Mr.JW)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Status Delete
    // Return Type      : array
    public function FSxMPXClearDataInDocTemp($paWhereClearTemp){
        $tPXDocNo       = $paWhereClearTemp['FTXthDocNo'];
        $tPXDocKey      = $paWhereClearTemp['FTXthDocKey'];
        $tPXSessionID   = $paWhereClearTemp['FTSessionID'];

        // Query Delete DocTemp
        $tClearDocTemp  =   "   DELETE FROM TCNTDocDTTmp 
                                WHERE 1=1 
                                AND TCNTDocDTTmp.FTXthDocKey    = '$tPXDocKey'
                                AND TCNTDocDTTmp.FTSessionID    = '$tPXSessionID'
        ";
        $this->db->query($tClearDocTemp);

        // Query Delete Doc HD Discount Temp
        $tClearDocHDDisTemp =   "   DELETE FROM TCNTDocHDDisTmp
                                    WHERE 1=1
                                    AND TCNTDocHDDisTmp.FTSessionID = '$tPXSessionID'
        ";
        $this->db->query($tClearDocHDDisTemp);

        // Query Delete Doc DT Discount Temp
        $tClearDocDTDisTemp =   "   DELETE FROM TCNTDocDTDisTmp
                                    WHERE 1=1
                                    AND TCNTDocDTDisTmp.FTSessionID = '$tPXSessionID'
        ";
        $this->db->query($tClearDocDTDisTemp);

        // Query Delete DocHDRefTemp
        $tClearHDRefTemp  =   "     DELETE FROM TCNTDocHDRefTmp 
                                    WHERE TCNTDocHDRefTmp.FTXthDocKey    = '$tPXDocKey'
                                      AND TCNTDocHDRefTmp.FTSessionID    = '$tPXSessionID'
        ";
        $this->db->query($tClearHDRefTemp);
    
    }

    // Functionality    : Get ShopCode From User Login
    // Parameters       : function parameters
    // Creator          : 24/06/2018 wasin (Yoshi AKA: Mr.JW)
    // Last Modified    : 24/02/2021 supawat: -
    // Return           : Array Data Shop For User Login
    // ReturnType       : array
    public function FSaPXGetShpCodeForUsrLogin($paDataShp){
        $nLngID     = $paDataShp['FNLngID'];
        $tUsrLogin  = $paDataShp['tUsrLogin'];
        $tSQL       = " SELECT
                            UGP.FTBchCode,
                            BCHL.FTBchName,
                            MER.FTMerCode,
                            MERL.FTMerName,
                            UGP.FTShpCode,
                            SHPL.FTShpName,
                            SHP.FTShpType,
                            SHP.FTWahCode   AS FTWahCode,
                            WAHL.FTWahName  AS FTWahName
                        FROM TCNTUsrGroup           UGP     WITH (NOLOCK)
                        LEFT JOIN TCNMBranch        BCH     WITH (NOLOCK) ON UGP.FTBchCode = BCH.FTBchCode 
                        LEFT JOIN TCNMBranch_L      BCHL    WITH (NOLOCK) ON UGP.FTBchCode = BCHL.FTBchCode AND BCHL.FNLngID = $nLngID
                        LEFT JOIN TCNMShop          SHP     WITH (NOLOCK) ON UGP.FTShpCode = SHP.FTShpCode
                        LEFT JOIN TCNMShop_L        SHPL    WITH (NOLOCK) ON SHP.FTShpCode = SHPL.FTShpCode AND SHP.FTBchCode = SHPL.FTBchCode AND SHPL.FNLngID = $nLngID
                        LEFT JOIN TCNMMerchant		MER		WITH (NOLOCK)	ON SHP.FTMerCode	= MER.FTMerCode
                        LEFT JOIN TCNMMerchant_L    MERL    WITH (NOLOCK) ON SHP.FTMerCode = MERL.FTMerCode AND MERL.FNLngID = $nLngID
                        LEFT JOIN TCNMWaHouse_L     WAHL    WITH (NOLOCK) ON SHP.FTWahCode = WAHL.FTWahCode AND SHP.FTBchCode = WAHL.FTBchCode
                        WHERE UGP.FTUsrCode = '$tUsrLogin' ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aResult    = $oQuery->row_array();
        }else{
            $aResult    = "";
        }
        unset($oQuery);
        return $aResult;
    }

    // Functionality    : Get Data Config WareHouse TSysConfig
    // Parameters       : function parameters
    // Creator          : 25/07/2018 wasin (Yoshi AKA: Mr.JW)
    // Last Modified    : 24/02/2021 supawat: -
    // Return           : Array Data Default Config WareHouse
    // ReturnType       : array
    public function FSaMPXGetDefOptionConfigWah($paConfigSys){
        $tSysCode       = $paConfigSys['FTSysCode'];
        $nSysSeq        = $paConfigSys['FTSysSeq'];
        $nLngID         = $paConfigSys['FNLngID'];
        $aDataReturn    = array();

        $tSQLUsrVal = " SELECT
                            SYSCON.FTSysStaUsrValue AS FTSysWahCode,
                            WAHL.FTWahName          AS FTSysWahName
                        FROM TSysConfig SYSCON          WITH(NOLOCK)
                        LEFT JOIN TCNMWaHouse   WAH     WITH(NOLOCK)    ON SYSCON.FTSysStaUsrValue  = WAH.FTWahCode     AND WAH.FTWahStaType = 1
                        LEFT JOIN TCNMWaHouse_L WAHL    WITH(NOLOCK)    ON WAH.FTWahCode            = WAHL.FTWahCode    AND WAHL.FNLngID = $nLngID
                        WHERE 1=1
                        AND SYSCON.FTSysCode    = '$tSysCode'
                        AND SYSCON.FTSysSeq     = $nSysSeq
        ";
        $oQuery1    = $this->db->query($tSQLUsrVal);
        if($oQuery1->num_rows() > 0){
            $aDataReturn    = $oQuery1->row_array();
        }else{
            $tSQLUsrDef =   "   SELECT
                                    SYSCON.FTSysStaDefValue AS FTSysWahCode,
                                    WAHL.FTWahName          AS FTSysWahName
                        FROM TSysConfig SYSCON          WITH(NOLOCK)
                        LEFT JOIN TCNMWaHouse   WAH     WITH(NOLOCK)    ON SYSCON.FTSysStaDefValue  = WAH.FTWahCode     AND WAH.FTWahStaType = 1
                        LEFT JOIN TCNMWaHouse_L WAHL    WITH(NOLOCK)    ON WAH.FTWahCode            = WAHL.FTWahCode    AND WAHL.FNLngID = $nLngID
                        WHERE 1=1
                        AND SYSCON.FTSysCode    = '$tSysCode'
                        AND SYSCON.FTSysSeq     = $nSysSeq
            ";
            $oQuery2    = $this->db->query($tSQLUsrDef);
            if($oQuery2->num_rows() > 0){
                $aDataReturn    = $oQuery2->row_array();
            }
        }
        unset($oQuery1);
        unset($oQuery2);
        return $aDataReturn;
    }

    // Functionality    : Get Data In Doc DT Temp
    // Parameters       : function parameters
    // Creator          : 01/07/2019 wasin (Yoshi)
    // Last Modified    : 24/02/2021 supawat: -
    // Return           : Array Data Doc DT Temp
    // ReturnType       : array
    public function FSaMPXGetDocDTTempListPage($paDataWhere){
        $tPXDocNo           = $paDataWhere['FTXthDocNo'];
        $tPXDocKey          = $paDataWhere['FTXthDocKey'];
        $tSearchPdtAdvTable = $paDataWhere['tSearchPdtAdvTable'];
        $tPXSesSessionID    = $this->session->userdata('tSesSessionID');

        $aRowLen    = FCNaHCallLenData($paDataWhere['nRow'],$paDataWhere['nPage']);

        $tSQL       = " SELECT c.* FROM(
                            SELECT  ROW_NUMBER() OVER(ORDER BY FNXtdSeqNo ASC) AS rtRowID,* FROM (
                                SELECT
                                    DOCTMP.FTBchCode,
                                    DOCTMP.FTXthDocNo,
                                    DOCTMP.FNXtdSeqNo,
                                    DOCTMP.FTXthDocKey,
                                    DOCTMP.FTPdtCode,
                                    DOCTMP.FTXtdPdtName,
                                    DOCTMP.FTPunName,
                                    DOCTMP.FTXtdBarCode,
                                    DOCTMP.FTPunCode,
                                    DOCTMP.FCXtdFactor,
                                    DOCTMP.FCXtdQty,
                                    DOCTMP.FCXtdSetPrice,
                                    DOCTMP.FCXtdAmtB4DisChg,
                                    DOCTMP.FTXtdDisChgTxt,
                                    DOCTMP.FCXtdNet,
                                    DOCTMP.FCXtdNetAfHD,
                                    DOCTMP.FTXtdStaAlwDis,
                                    DOCTMP.FCXtdVatRate,
                                    DOCTMP.FTXtdVatType,
                                    DOCTMP.FTSrnCode,
                                    DOCTMP.FDLastUpdOn,
                                    DOCTMP.FDCreateOn,
                                    DOCTMP.FTLastUpdBy,
                                    DOCTMP.FTCreateBy
                                FROM TCNTDocDTTmp DOCTMP WITH (NOLOCK)
                                WHERE 1 = 1
                                AND ISNULL (DOCTMP.FTXthDocNo, '') =  '$tPXDocNo'
                                AND DOCTMP.FTXthDocKey = '$tPXDocKey'
                                AND DOCTMP.FTSessionID = '$tPXSesSessionID' ";
                                
        if(isset($tSearchPdtAdvTable) && !empty($tSearchPdtAdvTable)){
            $tSQL   .=  "   AND (
                                DOCTMP.FTPdtCode COLLATE THAI_BIN LIKE '%$tSearchPdtAdvTable%'
                                OR DOCTMP.FTXtdPdtName COLLATE THAI_BIN LIKE '%$tSearchPdtAdvTable%'
                                OR DOCTMP.FTXtdBarCode COLLATE THAI_BIN LIKE '%$tSearchPdtAdvTable%'
                                OR DOCTMP.FTPunName COLLATE THAI_BIN LIKE '%$tSearchPdtAdvTable%' )
                        ";
            
        }
        $tSQL   .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";
        $oQuery = $this->db->query($tSQL);

        if($oQuery->num_rows() > 0){
            $aDataList  = $oQuery->result_array();
            $aFoundRow  = $this->FSaMPXGetDocDTTempListPageAll($paDataWhere);
            $nFoundRow  = ($aFoundRow['rtCode'] == '1')? $aFoundRow['rtCountData'] : 0;
            $nPageAll   = ceil($nFoundRow/$paDataWhere['nRow']);
            $aDataReturn    = array(
                'raItems'       => $aDataList,
                'rnAllRow'      => $nFoundRow,
                'rnCurrentPage' => $paDataWhere['nPage'],
                'rnAllPage'     => $nPageAll,
                'rtCode'        => '1',
                'rtDesc'        => 'success',
            );
        }else{
            $aDataReturn    = array(
                'rnAllRow'      => 0,
                'rnCurrentPage' => $paDataWhere['nPage'],
                "rnAllPage"     => 0,
                'rtCode'        => '800',
                'rtDesc'        => 'data not found',
            );
        }
        unset($oQuery);
        unset($aDataList);
        unset($aFoundRow);
        unset($nFoundRow);
        unset($nPageAll);
        return $aDataReturn;
    }   

    // Functionality    : Count All Documeny DT Temp
    // Parameters       : function parameters
    // Creator          : 01/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : array Data Count All Data
    // Return Type      : array
    public function FSaMPXGetDocDTTempListPageAll($paDataWhere){
        $tPXDocNo           = $paDataWhere['FTXthDocNo'];
        $tPXDocKey          = $paDataWhere['FTXthDocKey'];
        $tSearchPdtAdvTable = $paDataWhere['tSearchPdtAdvTable'];
        $tPXSesSessionID    = $this->session->userdata('tSesSessionID');

        $tSQL   = " SELECT COUNT (DOCTMP.FTXthDocNo) AS counts
                    FROM TCNTDocDTTmp DOCTMP
                    WHERE 1 = 1 ";
        
        $tSQL   .= " AND DOCTMP.FTXthDocNo  = '$tPXDocNo' ";
        $tSQL   .= " AND DOCTMP.FTXthDocKey = '$tPXDocKey' ";
        $tSQL   .= " AND DOCTMP.FTSessionID = '$tPXSesSessionID' ";

        // if(isset($tSearchPdtAdvTable) && !empty($tSearchPdtAdvTable)){
        //     $tSQL   .= " AND ( DOCTMP.FTPdtCode LIKE '%$tSearchPdtAdvTable%' ";
        //     $tSQL   .= " OR DOCTMP.FTXtdPdtName LIKE '%$tSearchPdtAdvTable%' ";
        //     $tSQL   .= " OR DOCTMP.FTPunName    LIKE '%$tSearchPdtAdvTable%' ";
        //     $tSQL   .= " OR DOCTMP.FTXtdBarCode LIKE '%$tSearchPdtAdvTable%' ";
        // }
        
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aDetail        = $oQuery->row_array();
            $aDataReturn    =  array(
                'rtCountData'   => $aDetail['counts'],
                'rtCode'        => '1',
                'rtDesc'        => 'success',
            );
        }else{
            $aDataReturn    =  array(
                'rtCode'    => '800',
                'rtDesc'    => 'Data Not Found',
            );
        }
        unset($oQuery);
        unset($aDetail);
        return $aDataReturn;
    }

    // Functionality    : Function Sum Amount DT Temp
    // Parameters       : function parameters
    // Creator          : 01/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : array
    // Return Type      : array
    public function FSaMPXSumDocDTTemp($paDataWhere){
        $tPXDocNo           = $paDataWhere['FTXthDocNo'];
        $tPXDocKey          = $paDataWhere['FTXthDocKey'];
        $tPXSesSessionID    = $this->session->userdata('tSesSessionID');
        $tSQL               = " SELECT
                                    SUM(FCXtdNetAfHD)       AS FCXtdSumNetAfHD,
                                    SUM(FCXtdAmtB4DisChg)   AS FCXtdSumAmtB4DisChg
                                FROM TCNTDocDTTmp DOCTMP WITH (NOLOCK)
                                WHERE 1 = 1 ";
        $tSQL   .= " AND DOCTMP.FTXthDocNo  = '$tPXDocNo' ";
        $tSQL   .= " AND DOCTMP.FTXthDocKey = '$tPXDocKey' ";
        $tSQL   .= " AND DOCTMP.FTSessionID = '$tPXSesSessionID' ";
        
        $oQuery = $this->db->query($tSQL);
        if($oQuery->num_rows() > 0){
            $aResult    = $oQuery->row_array();
            $aDataReturn    =  array(
                'raDataSum' => $aResult,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        }else{
            $aDataReturn    =  array(
                'rtCode'    => '800',
                'rtDesc'    => 'Data Sum Empty',
            );
        }
        unset($oQuery);
        unset($aResult);
        return $aDataReturn;
    }

    // Functionality    : Function Get Max Seq From Doc DT Temp
    // Parameters       : function parameters
    // Creator          : 02/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : array
    // Return Type      : array
    public function FSaMPXGetMaxSeqDocDTTemp($paDataWhere){
        $tPXBchCode         = $paDataWhere['FTBchCode'];
        $tPXDocNo           = $paDataWhere['FTXthDocNo'];
        $tPXDocKey          = $paDataWhere['FTXthDocKey'];
        $tPXSesSessionID    = $this->session->userdata('tSesSessionID');
        $tSQL   =   "   SELECT 
                            MAX(DOCTMP.FNXtdSeqNo) AS rnMaxSeqNo
                        FROM TCNTDocDTTmp DOCTMP WITH (NOLOCK)
                        WHERE 1 = 1 ";
        $tSQL   .= " AND DOCTMP.FTBchCode   = '$tPXBchCode'";
        $tSQL   .= " AND DOCTMP.FTXthDocNo  = '$tPXDocNo'";
        $tSQL   .= " AND DOCTMP.FTXthDocKey = '$tPXDocKey'";
        $tSQL   .= " AND DOCTMP.FTSessionID = '$tPXSesSessionID'";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aDetail    = $oQuery->row_array();
            $nResult    = $aDetail['rnMaxSeqNo'];
        }else{
            $nResult    = 0;
        }
        return empty($nResult)? 0 : $nResult;
    }

    // Functionality    : Get Data Pdt
    // Parameters       : function parameters
    // Creator          : 02/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : array
    // Return Type      : array
    public function FSaMPXGetDataPdt($paDataPdtParams){

        $tPdtCode   = $paDataPdtParams['tPdtCode'];
        $FTPunCode  = $paDataPdtParams['tPunCode'];
        $FTBarCode  = $paDataPdtParams['tBarCode'];
        $nLngID     = $paDataPdtParams['nLngID'];
        $tSQL       = " SELECT
                            PDT.FTPdtCode,
                            PDT.FTPdtStkControl,
                            PDT.FTPdtGrpControl,
                            PDT.FTPdtForSystem,
                            PDT.FCPdtQtyOrdBuy,
                            PDT.FCPdtCostDef,
                            PDT.FCPdtCostOth,
                            PDT.FCPdtCostStd,
                            PDT.FCPdtMin,
                            PDT.FCPdtMax,
                            PDT.FTPdtPoint,
                            PDT.FCPdtPointTime,
                            PDT.FTPdtType,
                            PDT.FTPdtSaleType,
                            0 AS FTPdtSalePrice,
                            PDT.FTPdtSetOrSN,
                            PDT.FTPdtStaSetPri,
                            PDT.FTPdtStaSetShwDT,
                            PDT.FTPdtStaAlwDis,
                            PDT.FTPdtStaAlwReturn,
                            PDT.FTPdtStaVatBuy,
                            PDT.FTPdtStaVat,
                            PDT.FTPdtStaActive,
                            PDT.FTPdtStaAlwReCalOpt,
                            PDT.FTPdtStaCsm,
                            PDT.FTTcgCode,
                            PDT.FTPtyCode,
                            PDT.FTPbnCode,
                            PDT.FTPmoCode,
                            PDT.FTVatCode,
                            PDT.FDPdtSaleStart,
                            PDT.FDPdtSaleStop,
                            PDTL.FTPdtName,
                            PDTL.FTPdtNameOth,
                            PDTL.FTPdtNameABB,
                            PDTL.FTPdtRmk,
                            PKS.FTPunCode,
                            PKS.FCPdtUnitFact,
                            VAT.FCVatRate,
                            UNTL.FTPunName,
                            BAR.FTBarCode,
                            BAR.FTPlcCode,
                            PDTLOCL.FTPlcName,
                            PDTSRL.FTSrnCode,
                            PDT.FCPdtCostStd,
                            CAVG.FCPdtCostEx,
                            CAVG.FCPdtCostIn,
                            SPL.FCSplLastPrice
                        FROM TCNMPdt PDT WITH (NOLOCK)
                        LEFT JOIN TCNMPdt_L PDTL        WITH (NOLOCK)   ON PDT.FTPdtCode      = PDTL.FTPdtCode    AND PDTL.FNLngID    = $nLngID
                        LEFT JOIN TCNMPdtPackSize  PKS  WITH (NOLOCK)   ON PDT.FTPdtCode      = PKS.FTPdtCode     AND PKS.FTPunCode   = '$FTPunCode'
                        LEFT JOIN TCNMPdtUnit_L UNTL    WITH (NOLOCK)   ON UNTL.FTPunCode     = '$FTPunCode'      AND UNTL.FNLngID    = $nLngID
                        LEFT JOIN TCNMPdtBar BAR        WITH (NOLOCK)   ON PKS.FTPdtCode      = BAR.FTPdtCode     AND BAR.FTPunCode   = '$FTPunCode'
                        LEFT JOIN TCNMPdtLoc_L PDTLOCL  WITH (NOLOCK)   ON PDTLOCL.FTPlcCode  = BAR.FTPlcCode     AND PDTLOCL.FNLngID = $nLngID
                        LEFT JOIN (
                            SELECT DISTINCT
                                FTVatCode,
                                FCVatRate,
                                FDVatStart
                            FROM TCNMVatRate WITH (NOLOCK)
                            WHERE CONVERT(VARCHAR(19),GETDATE(),121) > FDVatStart ) VAT
                        ON PDT.FTVatCode = VAT.FTVatCode
                        LEFT JOIN TCNTPdtSerial PDTSRL  WITH (NOLOCK)   ON PDT.FTPdtCode    = PDTSRL.FTPdtCode
                        LEFT JOIN TCNMPdtSpl SPL        WITH (NOLOCK)   ON PDT.FTPdtCode    = SPL.FTPdtCode AND BAR.FTBarCode = SPL.FTBarCode
                        LEFT JOIN TCNMPdtCostAvg CAVG   WITH (NOLOCK)   ON PDT.FTPdtCode    = CAVG.FTPdtCode
                        LEFT JOIN (
                            SELECT DISTINCT
                                P4PDT.FTPdtCode,
                                P4PDT.FTPunCode,
                                P4PDT.FDPghDStart,
                                P4PDT.FTPghTStart,
                                P4PDT.FCPgdPriceRet
                            FROM TCNTPdtPrice4PDT P4PDT WITH (NOLOCK)
                            WHERE 1=1
                            AND (CONVERT(VARCHAR(10),GETDATE(),121) >= CONVERT(VARCHAR(10),P4PDT.FDPghDStart,121))
                            AND (CONVERT(VARCHAR(10),GETDATE(),121) <= CONVERT(VARCHAR(10),P4PDT.FDPghDStop,121))
                        ) AS PRI4PDT
                        ON PDT.FTPdtCode = PRI4PDT.FTPdtCode AND PRI4PDT.FTPunCode = PKS.FTPunCode
                        WHERE 1 = 1 ";
    
        if(isset($tPdtCode) && !empty($tPdtCode)){
            $tSQL   .= " AND PDT.FTPdtCode   = '$tPdtCode'";
        }

        if(isset($FTBarCode) && !empty($FTBarCode)){
            $tSQL   .= " AND BAR.FTBarCode = '$FTBarCode'";
        }

        $tSQL   .= " ORDER BY FDVatStart DESC";
        $oQuery = $this->db->query($tSQL);
 
        if ($oQuery->num_rows() > 0){
            $aDetail    = $oQuery->row_array();
            $aResult    = array(
                'raItem'    => $aDetail,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        }else{
            $aResult = array(
                'rtCode' => '800',
                'rtDesc' => 'data not found.',
            );
        }
        unset($oQuery);
        unset($aDetail);
        return $aResult;
    }

    // Functionality    : Insert Pdt To Doc DT Temp
    // Parameters       : function parameters
    // Creator          : 02/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : array
    // Return Type      : array
    public function FSaMPXInsertPDTToTemp($paDataPdtMaster,$paDataPdtParams){
        $paPXDataPdt    = $paDataPdtMaster['raItem'];
        if($paDataPdtParams['tPXOptionAddPdt'] == 1){
            // ??????????????????????????????????????????????????????????????????????????????
            $tSQL   =   "   SELECT
                                FNXtdSeqNo, 
                                FCXtdQty
                            FROM TCNTDocDTTmp
                            WHERE 1=1 
                            AND FTXthDocNo      = '".$paDataPdtParams['tDocNo']."'
                            AND FTBchCode       = '".$paDataPdtParams['tBchCode']."'
                            AND FTXthDocKey     = '".$paDataPdtParams['tDocKey']."'
                            AND FTSessionID     = '".$paDataPdtParams['tSessionID']."'
                            AND FTPdtCode       = '".$paPXDataPdt["FTPdtCode"]."'
                            AND FTXtdBarCode    = '".$paPXDataPdt["FTBarCode"]."'
                            ORDER BY FNXtdSeqNo
                        ";
            $oQuery = $this->db->query($tSQL);
            if($oQuery->num_rows() > 0){
                // ????????????????????????????????????????????????????????????????????????????????????????????????
                $aResult    = $oQuery->row_array();
                $tSQL       =   "   UPDATE TCNTDocDTTmp
                                    SET FCXtdQty = '".($aResult["FCXtdQty"] + 1 )."'
                                    WHERE 1=1
                                    AND FTBchCode       = '".$paDataPdtParams['tBchCode']."'
                                    AND FTXthDocNo      = '".$paDataPdtParams['tDocNo']."'
                                    AND FNXtdSeqNo      = '".$aResult["FNXtdSeqNo"]."'
                                    AND FTXthDocKey     = '".$paDataPdtParams['tDocKey']."'
                                    AND FTSessionID     = '".$paDataPdtParams['tSessionID']."'
                                    AND FTPdtCode       = '".$paPXDataPdt["FTPdtCode"]."'
                                    AND FTXtdBarCode    = '".$paPXDataPdt["FTBarCode"]."'
                                ";
                $this->db->query($tSQL);
                $aStatus = array(
                    'rtCode'    => '1',
                    'rtDesc'    => 'Add Success.',
                );
            }else{
                // ?????????????????????????????????????????????
                $aDataInsert    = array(
                    'FTBchCode'         => $paDataPdtParams['tBchCode'],
                    'FTXthDocNo'        => $paDataPdtParams['tDocNo'],
                    'FNXtdSeqNo'        => $paDataPdtParams['nMaxSeqNo'],
                    'FTXthDocKey'       => $paDataPdtParams['tDocKey'],
                    'FTPdtCode'         => $paPXDataPdt['FTPdtCode'],
                    'FTXtdPdtName'      => $paPXDataPdt['FTPdtName'],
                    'FCXtdFactor'       => $paPXDataPdt['FCPdtUnitFact'],
                    'FTPunCode'         => $paPXDataPdt['FTPunCode'],
                    'FTPunName'         => $paPXDataPdt['FTPunName'],
                    'FTXtdBarCode'      => $paDataPdtParams['tBarCode'],
                    'FTXtdVatType'      => $paPXDataPdt['FTPdtStaVatBuy'],
                    'FTVatCode'         => $paDataPdtParams['nVatCode'],
                    'FCXtdVatRate'      => $paDataPdtParams['nVatRate'],
                    'FTXtdStaAlwDis'    => $paPXDataPdt['FTPdtStaAlwDis'],
                    'FTXtdSaleType'     => $paPXDataPdt['FTPdtSaleType'],
                    'FCXtdSalePrice'    => $paPXDataPdt['FTPdtSalePrice'],
                    'FCXtdQty'          => 1,
                    'FCXtdQtyAll'       => 1*$paPXDataPdt['FCPdtUnitFact'],
                    'FCXtdSetPrice'     => $paDataPdtParams['cPrice'] * 1,
                    'FTSessionID'       => $paDataPdtParams['tSessionID'],
                    'FDLastUpdOn'       => date('Y-m-d h:i:s'),
                    'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                    'FDCreateOn'        => date('Y-m-d h:i:s'),
                    'FTCreateBy'        => $this->session->userdata('tSesUsername')
                );
                $this->db->insert('TCNTDocDTTmp',$aDataInsert);

                $this->db->last_query();  
                if($this->db->affected_rows() > 0){
                    $aStatus = array(
                        'rtCode'    => '1',
                        'rtDesc'    => 'Add Success.',
                    );
                }else{
                    $aStatus = array(
                        'rtCode'    => '905',
                        'rtDesc'    => 'Error Cannot Add.',
                    );
                }
            }   
        }else{
            // ????????????????????????????????????
            $aDataInsert    = array(
                'FTBchCode'         => $paDataPdtParams['tBchCode'],
                'FTXthDocNo'        => $paDataPdtParams['tDocNo'],
                'FNXtdSeqNo'        => $paDataPdtParams['nMaxSeqNo'],
                'FTXthDocKey'       => $paDataPdtParams['tDocKey'],
                'FTPdtCode'         => $paPXDataPdt['FTPdtCode'],
                'FTXtdPdtName'      => $paPXDataPdt['FTPdtName'],
                'FCXtdFactor'       => $paPXDataPdt['FCPdtUnitFact'],
                'FTPunCode'         => $paPXDataPdt['FTPunCode'],
                'FTPunName'         => $paPXDataPdt['FTPunName'],
                'FTXtdBarCode'      => $paDataPdtParams['tBarCode'],
                'FTXtdVatType'      => $paPXDataPdt['FTPdtStaVatBuy'],
                'FTVatCode'         => $paPXDataPdt['FTVatCode'],
                'FCXtdVatRate'      => $paPXDataPdt['FCVatRate'],
                'FTXtdStaAlwDis'    => $paPXDataPdt['FTPdtStaAlwDis'],
                'FTXtdSaleType'     => $paPXDataPdt['FTPdtSaleType'],
                'FCXtdSalePrice'    => $paPXDataPdt['FTPdtSalePrice'],
                'FCXtdQty'          => 1,
                'FCXtdQtyAll'       => 1*$paPXDataPdt['FCPdtUnitFact'],
                'FCXtdSetPrice'     => $paDataPdtParams['cPrice'] * 1,
                'FTSessionID'       => $paDataPdtParams['tSessionID'],
                'FDLastUpdOn'       => date('Y-m-d h:i:s'),
                'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                'FDCreateOn'        => date('Y-m-d h:i:s'),
                'FTCreateBy'        => $this->session->userdata('tSesUsername')
            );
            $this->db->insert('TCNTDocDTTmp',$aDataInsert);
            $this->db->last_query();  
            if($this->db->affected_rows() > 0){
                $aStatus = array(
                    'rtCode'    => '1',
                    'rtDesc'    => 'Add Success.',
                );
            }else{
                $aStatus = array(
                    'rtCode'    => '905',
                    'rtDesc'    => 'Error Cannot Add.',
                );
            }
        }
        return $aStatus;
    }

    // Functionality    : Update Document DT Temp by Seq
    // Parameters       : function parameters
    // Creator          : 02/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : array
    // Return Type      : array
    public function FSaMPXUpdateInlineDTTemp($paDataUpdateDT,$paDataWhere){

            $tPXSessionID   = $paDataWhere['tPXSessionID'];
            $tPXDocNo       = $paDataWhere['tPXDocNo'];
            $tPXBchCode     = $paDataWhere['tPXBchCode'];
            $nPXSeqNo       = $paDataWhere['nPXSeqNo'];

            $tSQL ="SELECT
                    PKS.FCPdtUnitFact
                    FROM
                   TCNTDocDTTmp DTTEMP
                LEFT OUTER JOIN TCNMPdtPackSize PKS WITH (NOLOCK) ON DTTEMP.FTPdtCode = PKS.FTPdtCode AND DTTEMP.FTPunCode = PKS.FTPunCode
                WHERE
                    FTSessionID = '$tPXSessionID'
                    AND FTBchCode = '$tPXBchCode'
                    AND FTXthDocNo = '$tPXDocNo'
                    AND FNXtdSeqNo = $nPXSeqNo
                    ";
            
            $cPdtUnitFact = $this->db->query($tSQL)->row_array()['FCPdtUnitFact'];

            if($cPdtUnitFact>0){ 
                $cPdtUnitFact = $cPdtUnitFact;
            }else{
                $cPdtUnitFact = 1;
            }


            $this->db->set('FCXtdQty', $paDataUpdateDT['FCXtdQty']);
            $this->db->set('FCXtdSetPrice', $paDataUpdateDT['FCXtdSetPrice']);
            $this->db->set('FCXtdNet', $paDataUpdateDT['FCXtdNet']);
            
            // $this->db->set('FCXtdAmtB4DisChg', $paDataUpdateDT['FCXtdNet']);
            $this->db->set('FCXtdQtyAll', $paDataUpdateDT['FCXtdQty']*$cPdtUnitFact);

            $this->db->where('FTSessionID',$paDataWhere['tPXSessionID']);
            $this->db->where('FTXthDocKey',$paDataWhere['tDocKey']);
            $this->db->where('FNXtdSeqNo',$paDataWhere['nPXSeqNo']);
            $this->db->where('FTXthDocNo',$paDataWhere['tPXDocNo']);
            $this->db->where('FTBchCode',$paDataWhere['tPXBchCode']);
            $this->db->update('TCNTDocDTTmp');
            if($this->db->affected_rows() > 0){
                $aStatus = array(
                    'rtCode'    => '1',
                    'rtDesc'    => 'Update Success',
                );
            }else{
                $aStatus = array(
                    'rtCode'    => '903',
                    'rtDesc'    => 'Update Fail',
                );
            }
            return $aStatus;
    }

    // Functionality    : Get Price Net DT
    // Parameters       : function parameters
    // Creator          : 01/09/2020 Nale
    // Last Modified    : 24/02/2021 supawat
    // Return           : array
    // Return Type      : array
    public function FSaMPXGetPdtNetInDTTemp($paDataWhere){
 
        $this->db->select('FCXtdNet');
        $this->db->where('FTSessionID',$paDataWhere['tPXSessionID']);
        $this->db->where('FTXthDocKey',$paDataWhere['tDocKey']);
        $this->db->where('FNXtdSeqNo',$paDataWhere['nPXSeqNo']);
        $this->db->where('FTXthDocNo',$paDataWhere['tPXDocNo']);
        $this->db->where('FTBchCode',$paDataWhere['tPXBchCode']);
        $cXtdNet= $this->db->get('TCNTDocDTTmp')->row_array()['FCXtdNet'];
        if($cXtdNet > 0){
            $aStatus = array(
                'rtCode'    => '1',
                'rtDesc'    => 'Update Success',
                'cXtdNet' => $cXtdNet,
            );
        }else{
            $aStatus = array(
                'rtCode'    => '903',
                'rtDesc'    => 'Update Fail',
                'cXtdNet' => 0.00,
            );
        }
        return $aStatus;
    }
    // Functionality    : Count Check Data Product In Doc DT Temp Before Save
    // Parameters       : function parameters
    // Creator          : 03/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Status Count
    // Return Type      : array
    public function FSnMPXChkPdtInDocDTTemp($paDataWhere){
        $tPXDocNo       = $paDataWhere['FTXthDocNo'];
        $tPXDocKey      = $paDataWhere['FTXthDocKey'];
        $tPXSessionID   = $paDataWhere['FTSessionID'];
        $tSQL           = " SELECT
                                COUNT(FNXtdSeqNo) AS nCountPdt
                            FROM TCNTDocDTTmp DocDT
                            WHERE 1=1
                            AND DocDT.FTXthDocNo    = '$tPXDocNo'
                            AND DocDT.FTXthDocKey   = '$tPXDocKey'
                            AND DocDT.FTSessionID   = '$tPXSessionID' ";
        $oQuery = $this->db->query($tSQL);
        if($oQuery->num_rows() > 0){
            $aDataQuery = $oQuery->row_array();
            return $aDataQuery['nCountPdt'];
        }else{
            return 0;
        }
    }

    // Functionality    : Delete Product Single Item In Doc DT Temp
    // Parameters       : function parameters
    // Creator          : 04/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Status Delete
    // Return Type      : array
    public function FSnMPXDelPdtInDTTmp($paDataWhere){
        // Delete Doc DT Temp
        $this->db->where_in('FTSessionID',$paDataWhere['tSessionID']);
        $this->db->where_in('FTPdtCode',$paDataWhere['tPdtCode']);
        $this->db->where_in('FNXtdSeqNo',$paDataWhere['nSeqNo']);
        $this->db->where_in('FTXthDocNo',$paDataWhere['tDocNo']);
        $this->db->where_in('FTBchCode',$paDataWhere['tBchCode']);
        $this->db->delete('TCNTDocDTTmp');

        // Delete Doc DT Temp
        $this->db->where_in('FNXtdStaDis',1);
        $this->db->where_in('FTSessionID',$paDataWhere['tSessionID']);
        $this->db->where_in('FNXtdSeqNo',$paDataWhere['nSeqNo']);
        $this->db->where_in('FTXthDocNo',$paDataWhere['tDocNo']);
        $this->db->where_in('FTBchCode',$paDataWhere['tBchCode']);
        $this->db->delete('TCNTDocDTDisTmp');
        return ;
    }

    // Functionality    : Delete Product Multiple Items In Doc DT Temp
    // Parameters       : function parameters
    // Creator          : 30/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Status Delete
    // Return Type      : array
    public function FSnMPXDelMultiPdtInDTTmp($paDataWhere){
        $tSessionID = $this->session->userdata('tSesSessionID');

        // Delete Doc DT Temp
        $this->db->where_in('FTSessionID',$tSessionID);
        $this->db->where_in('FNXtdSeqNo',$paDataWhere['aDataSeqNo']);
        // $this->db->where_in('FTPunCode',$paDataWhere['aDataPunCode']);
        $this->db->where_in('FTPdtCode',$paDataWhere['aDataPdtCode']);
        $this->db->where_in('FTXthDocNo',$paDataWhere['tDocNo']);
        $this->db->where_in('FTBchCode',$paDataWhere['tBchCode']);
        $this->db->delete('TCNTDocDTTmp');

        // Delete Doc DT Temp
        $this->db->where_in('FNXtdStaDis',1);
        $this->db->where_in('FTSessionID',$tSessionID);
        $this->db->where_in('FNXtdSeqNo',$paDataWhere['aDataSeqNo']);
        $this->db->where_in('FTXthDocNo',$paDataWhere['tDocNo']);
        $this->db->where_in('FTBchCode',$paDataWhere['tBchCode']);
        $this->db->delete('TCNTDocDTDisTmp');
        return ;
    }

    // Functionality    : Function Get Cal From DT Temp
    // Parameters       : function parameters
    // Creator          : 04/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : array
    // Return Type      : array
    public function FSaMPXCalInDTTemp($paParams){
        $tDocNo         = $paParams['tDocNo'];
        $tDocKey        = $paParams['tDocKey'];
        $tBchCode       = $paParams['tBchCode'];
        $tSessionID     = $paParams['tSessionID'];
        $tDataVatInOrEx = $paParams['tDataVatInOrEx'];
        
        $tSQL       = " SELECT
                            /* ?????????????????? ==============================================================*/
                            SUM(ISNULL(DTTMP.FCXtdNet, 0)) AS FCXphTotal,

                            /* ??????????????????????????????????????????????????????????????? ==============================================================*/
                            SUM(CASE WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNet, 0) ELSE 0 END) AS FCXphTotalNV,

                            /* ?????????????????????????????????????????????????????? ==============================================================*/
                            SUM(CASE WHEN DTTMP.FTXtdStaAlwDis = 2 THEN ISNULL(DTTMP.FCXtdNet, 0) ELSE 0 END) AS FCXphTotalNoDis,

                            /* ??????????????????????????????????????????????????? ??????????????????????????? ==============================================================*/
                            SUM(CASE WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdNet, 0) ELSE 0 END) AS FCXphTotalB4DisChgV,

                            /* ??????????????????????????????????????????????????? ???????????????????????????????????? */
                            SUM(CASE WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNet, 0) ELSE 0 END) AS FCXphTotalB4DisChgNV,

                            /* ???????????????????????????????????? ??????????????????????????? ==============================================================*/
                            SUM(CASE WHEN DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdNetAfHD, 0) ELSE 0 END) AS FCXphTotalAfDisChgV,

                            /* ???????????????????????????????????? ???????????????????????????????????? ==============================================================*/
                            SUM(CASE WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNetAfHD, 0) ELSE 0 END) AS FCXphTotalAfDisChgNV,

                            /* ????????????????????????????????????????????? ==============================================================*/
                            (
                                CASE 
                                    WHEN $tDataVatInOrEx = 1 THEN --???????????????
                                        (
                                            /* ?????????????????? */
                                            SUM(DTTMP.FCXtdNet)
                                            - 
                                            /* ??????????????????????????????????????????????????????????????? */
                                            SUM(
                                                CASE
                                                    WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNet, 0)
                                                    ELSE 0
                                                END
                                            )
                                        )
                                        -
                                        (
                                            /* ??????????????????????????????????????????????????? ??????????????????????????? */
                                            SUM(
                                                CASE
                                                    WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdNet, 0)
                                                    ELSE 0
                                                END
                                            )
                                            -
                                            /* ??????????????????????????????????????????????????? ??????????????????????????? FCXphTotalAfDisChgV */
                                            SUM(
                                                CASE
                                                    WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdNetAfHD, 0)
                                                    ELSE 0
                                                END
                                            )
                                        )
                                    WHEN $tDataVatInOrEx = 2 THEN --??????????????????
                                    
                                            (
                                                /* ?????????????????? */
                                                SUM(DTTMP.FCXtdNet)
                                                - 
                                                /* ??????????????????????????????????????????????????????????????? */
                                                SUM(
                                                    CASE
                                                        WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNet, 0)
                                                        ELSE 0
                                                    END
                                                )
                                            )
                                            -
                                            (
                                                /* ??????????????????????????????????????????????????? ??????????????????????????? */
                                                SUM(
                                                    CASE
                                                        WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdNet, 0)
                                                        ELSE 0
                                                    END
                                                )
                                                -
                                                /* ??????????????????????????????????????????????????? ??????????????????????????? FCXphTotalAfDisChgV */
                                                SUM(
                                                    CASE
                                                        WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 1 THEN 
                                                            ISNULL(DTTMP.FCXtdNetAfHD, 0)
                                                        ELSE 0
                                                    END
                                                )
                                            ) 
                                            + 
                                            SUM(ISNULL(DTTMP.FCXtdVat, 0))
                                ELSE 0 END
                            ) AS FCXphAmtV,

                            /* ???????????????????????????????????????????????????????????? ==============================================================*/
                            (
                                SUM(CASE WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNet, 0) ELSE 0 END)
                                -
                                (
                                    SUM(CASE WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdAmtB4DisChg, 0) ELSE 0 END)
                                    -
                                    SUM(CASE WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNetAfHD, 0) ELSE 0 END)
                                )
                            ) AS FCXphAmtNV,

                            /* ????????????????????? ==============================================================*/
                            SUM(ISNULL(DTTMP.FCXtdVat, 0)) AS FCXphVat,

                            /* ?????????????????????????????? ==============================================================*/
                            (
                                (
                                    CASE 
                                        WHEN $tDataVatInOrEx = 1 THEN --???????????????
                                            (
                                                /* ?????????????????? */
                                                SUM(DTTMP.FCXtdNet)
                                                - 
                                                /* ??????????????????????????????????????????????????????????????? */
                                                SUM(
                                                    CASE
                                                        WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNet, 0)
                                                        ELSE 0
                                                    END
                                                )
                                            )
                                            -
                                            (
                                                /* ??????????????????????????????????????????????????? ??????????????????????????? */
                                                SUM(
                                                    CASE
                                                        WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdNet, 0)
                                                        ELSE 0
                                                    END
                                                )
                                                -
                                                /* ??????????????????????????????????????????????????? ??????????????????????????? FCXphTotalAfDisChgV */
                                                SUM(
                                                    CASE
                                                        WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdNetAfHD, 0)
                                                        ELSE 0
                                                    END
                                                )
                                            )
                                        WHEN $tDataVatInOrEx = 2 THEN --??????????????????
                                        
                                                (
                                                    /* ?????????????????? */
                                                    SUM(DTTMP.FCXtdNet)
                                                    - 
                                                    /* ??????????????????????????????????????????????????????????????? */
                                                    SUM(
                                                        CASE
                                                            WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNet, 0)
                                                            ELSE 0
                                                        END
                                                    )
                                                )
                                                -
                                                (
                                                    /* ??????????????????????????????????????????????????? ??????????????????????????? */
                                                    SUM(
                                                        CASE
                                                            WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdNet, 0)
                                                            ELSE 0
                                                        END
                                                    )
                                                    -
                                                    /* ??????????????????????????????????????????????????? ??????????????????????????? FCXphTotalAfDisChgV */
                                                    SUM(
                                                        CASE
                                                            WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 1 THEN 
                                                                ISNULL(DTTMP.FCXtdNetAfHD, 0)
                                                            ELSE 0
                                                        END
                                                    )
                                                ) 
                                                + 
                                                SUM(ISNULL(DTTMP.FCXtdVat, 0))
                                    ELSE 0 END
                                    - 
                                    SUM(ISNULL(DTTMP.FCXtdVat, 0))
                                )
                                +
                                (
                                    SUM(CASE WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNet, 0) ELSE 0 END)
                                    -
                                    (
                                        SUM(CASE WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdAmtB4DisChg, 0) ELSE 0 END)
                                        -
                                        SUM(CASE WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNetAfHD, 0) ELSE 0 END)
                                    )
                                )
                            ) AS FCXphVatable,

                            /* ??????????????????????????????????????? ??? ????????????????????? ==============================================================*/
                            STUFF((
                                SELECT  ',' + DOCCONCAT.FTXtdWhtCode
                                FROM TCNTDocDTTmp DOCCONCAT
                                WHERE  1=1 
                                AND DOCCONCAT.FTBchCode = '$tBchCode'
                                AND DOCCONCAT.FTXthDocNo = '$tDocNo'
                                AND DOCCONCAT.FTSessionID = '$tSessionID'
                            FOR XML PATH('')), 1, 1, '') AS FTXphWpCode,

                            /* ????????????????????? ??? ????????????????????? ==============================================================*/
                            SUM(ISNULL(DTTMP.FCXtdWhtAmt, 0)) AS FCXphWpTax

                        FROM TCNTDocDTTmp DTTMP
                        WHERE DTTMP.FTXthDocNo  = '$tDocNo' 
                        AND DTTMP.FTXthDocKey   = '$tDocKey' 
                        AND DTTMP.FTSessionID   = '$tSessionID'
                        AND DTTMP.FTBchCode     = '$tBchCode'
                        GROUP BY DTTMP.FTSessionID ";

        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aResult    = $oQuery->result_array();
        }else{
            $aResult    = [];
        }
        return $aResult;
    }

    // Functionality    : Function Get Cal From HDDis Temp
    // Parameters       : function parameters
    // Creator          : 04/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : array
    // Return Type      : array
    public function FSaMPXCalInHDDisTemp($paParams){
        $tDocNo     = $paParams['tDocNo'];
        $tDocKey    = $paParams['tDocKey'];
        $tBchCode   = $paParams['tBchCode'];
        $tSessionID = $paParams['tSessionID']; 
        $tSQL       = " SELECT
                            /* ???????????????????????????????????????????????????????????? ==============================================================*/
                            STUFF((
                                SELECT  ',' + DOCCONCAT.FTXtdDisChgTxt
                                FROM TCNTDocHDDisTmp DOCCONCAT
                                WHERE  1=1 
                                AND DOCCONCAT.FTBchCode 		= '$tBchCode'
                                AND DOCCONCAT.FTXthDocNo		= '$tDocNo'
                                AND DOCCONCAT.FTSessionID		= '$tSessionID'
                            FOR XML PATH('')), 1, 1, '') AS FTXphDisChgTxt,
                            /* ????????????????????????????????????????????? ==============================================================*/
                            SUM( 
                                CASE 
                                    WHEN HDDISTMP.FTXtdDisChgType = 1 THEN ISNULL(HDDISTMP.FCXtdAmt, 0)
                                    WHEN HDDISTMP.FTXtdDisChgType = 2 THEN ISNULL(HDDISTMP.FCXtdAmt, 0)
                                    ELSE 0 
                                END
                            ) AS FCXphDis,
                            /* ?????????????????????????????????????????????????????? ==============================================================*/
                            SUM( 
                                CASE 
                                    WHEN HDDISTMP.FTXtdDisChgType = 3 THEN ISNULL(HDDISTMP.FCXtdAmt, 0)
                                    WHEN HDDISTMP.FTXtdDisChgType = 4 THEN ISNULL(HDDISTMP.FCXtdAmt, 0)
                                    ELSE 0 
                                END
                            ) AS FCXphChg
                        FROM TCNTDocHDDisTmp HDDISTMP
                        WHERE 1=1 
                        AND HDDISTMP.FTXthDocNo     = '$tDocNo' 
                        AND HDDISTMP.FTSessionID    = '$tSessionID'
                        AND HDDISTMP.FTBchCode      = '$tBchCode'
                        GROUP BY HDDISTMP.FTSessionID ";

        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aResult    = $oQuery->row_array();
        }else{
            $aResult    = [];
        }
        return $aResult;
    }
    
    // Functionality    : Add/Update Data HD
    // Parameters       : function parameters
    // Creator          : 03/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Status Add/Update Document HD
    // Return Type      : array
    public function FSxMPXAddUpdateHD($paDataMaster,$paDataWhere,$paTableAddUpdate){
        // Get Data PX HD
        // $aDataGetDataHD     =  $this->FSaMPXGetDataDocHD(array(
        //     'FTXthDocNo'    => $paDataWhere['FTXphDocNo'],
        //     'FNLngID'       => $this->session->userdata("tLangEdit")
        // ));

        // ?????????????????????????????????????????????????????????????????????????????????????????????
        $tSQL = " SELECT HD.FTXphDocNo 
                  FROM ".$paTableAddUpdate['tTableHD']." HD 
                  WHERE HD.FTXphDocNo = '".$paDataWhere['FTXphDocNo']."' 
                    AND HD.FTBchCode  = '".$paDataWhere['FTBchCode']."' 
                    AND HD.FTAgnCode  = '".$paDataWhere['FTAgnCode']."' 
                ";
        $oQuery = $this->db->query($tSQL);

        $aDataAddUpdateHD   = array();
        if ( $oQuery->num_rows() > 0 ){
            $aDataAddUpdateHD   = array_merge($paDataMaster,array(
                'FDLastUpdOn'   => $paDataWhere['FDLastUpdOn'],
                'FTLastUpdBy'   => $paDataWhere['FTLastUpdBy']
            ));
            // Update Px HD
            $this->db->where('FTAgnCode',$paDataWhere['FTAgnCode']);
            $this->db->where('FTBchCode',$paDataWhere['FTBchCode']);
            $this->db->where('FTXphDocNo',$paDataWhere['FTXphDocNo']);
            $this->db->update($paTableAddUpdate['tTableHD'],$aDataAddUpdateHD);
        }else{
            $aDataAddUpdateHD   = array_merge($paDataMaster,array(
                'FTAgnCode'     => $paDataWhere['FTAgnCode'],
                'FTBchCode'     => $paDataWhere['FTBchCode'],
                'FTXphDocNo'    => $paDataWhere['FTXphDocNo'],
                'FDLastUpdOn'   => $paDataWhere['FDLastUpdOn'],
                'FTLastUpdBy'   => $paDataWhere['FTLastUpdBy'],
                'FDCreateOn'    => $paDataWhere['FDCreateOn'],
                'FTCreateBy'    => $paDataWhere['FTCreateBy']
            ));
            // Insert PX HD
            $this->db->insert($paTableAddUpdate['tTableHD'],$aDataAddUpdateHD);
        }

        
        // if(isset($aDataGetDataHD['rtCode']) && $aDataGetDataHD['rtCode'] == 1){
            // $aDataHDOld         = $aDataGetDataHD['raItems'];
            
        // }else{
        //     $aDataAddUpdateHD   = array_merge($paDataMaster,array(
        //         'FTBchCode'     => $paDataWhere['FTBchCode'],
        //         'FTXphDocNo'    => $paDataWhere['FTXphDocNo'],
        //         'FDCreateOn'    => $paDataWhere['FDCreateOn'],
        //         'FTCreateBy'    => $paDataWhere['FTCreateBy'],
        //     ));
        // }
        // Delete PX HD
        // $this->db->where_in('FTBchCode',$aDataAddUpdateHD['FTBchCode']);
        // $this->db->where_in('FTXphDocNo',$aDataAddUpdateHD['FTXphDocNo']);
        // $this->db->delete($paTableAddUpdate['tTableHD']);

        
        return;
    }

    // Functionality    : Add/Update Data HD Supplier
    // Parameters       : Controller function parameters
    // Creator          : 03/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Status Add/Update Document Supplier
    // Return Type      : array
    public function FSxMPXAddUpdateHDSpl($paDataHDSpl,$paDataWhere,$paTableAddUpdate){
        // Get Data PX HD
        // $aDataGetDataSpl    =   $this->FSaMPXGetDataDocHDSpl(array(
        //     'FTXthDocNo'    => $paDataWhere['FTXphDocNo'],
        //     'FNLngID'       => $this->session->userdata("tLangEdit")
        // ));

        // ?????????????????????????????????????????????????????????????????????????????????????????????
        $tSQL = " SELECT HDSPL.FTXphDocNo 
                  FROM ".$paTableAddUpdate['tTableHDSpl']." HDSPL
                  WHERE HDSPL.FTXphDocNo = '".$paDataWhere['FTXphDocNo']."' 
                    AND HDSPL.FTBchCode  = '".$paDataWhere['FTBchCode']."' 
                    AND HDSPL.FTAgnCode  = '".$paDataWhere['FTAgnCode']."' 
                ";
        $oQuery = $this->db->query($tSQL);

        $aDataAddUpdateHDSpl = array();
        if ( $oQuery->num_rows() > 0 ){
            // Delete Px HD Spl
            $this->db->where('FTAgnCode',$paDataWhere['FTAgnCode']);
            $this->db->where('FTBchCode',$paDataWhere['FTBchCode']);
            $this->db->where('FTXphDocNo',$paDataWhere['FTXphDocNo']);
            $this->db->delete($paTableAddUpdate['tTableHDSpl']);
        }

        //Insert PX HD Dis
        $aDataAddUpdateHDSpl = array_merge($paDataHDSpl,array(
            'FTAgnCode'     => $paDataWhere['FTAgnCode'],
            'FTBchCode'     => $paDataWhere['FTBchCode'],
            'FTXphDocNo'    => $paDataWhere['FTXphDocNo'],
        ));
        $this->db->insert($paTableAddUpdate['tTableHDSpl'],$aDataAddUpdateHDSpl);

        // $aDataAddUpdateHDSpl    = array();
        // if(isset($aDataAddUpdateHDSpl['rtCode']) && $aDataAddUpdateHDSpl['rtCode'] == 1){
        //     $aDataHDSplOld  = $aDataGetDataSpl['raItems'];
        //     $aDataAddUpdateHDSpl    = array_merge($paDataHDSpl,array(
        //         'FTBchCode'     => $aDataGetDataSpl['FTBchCode'],
        //         'FTXphDocNo'    => $aDataGetDataSpl['FTXphDocNo'],
        //     ));
        // }else{
        //     $aDataAddUpdateHDSpl    = array_merge($paDataHDSpl,array(
        //         'FTBchCode'     => $paDataWhere['FTBchCode'],
        //         'FTXphDocNo'    => $paDataWhere['FTXphDocNo'],
        //     ));
        // }

        

        // // Insert PX HD Dis
        // $this->db->insert($paTableAddUpdate['tTableHDSpl'],$aDataAddUpdateHDSpl);
        return;
    }

    // Functionality    : Update DocNo In Doc Temp
    // Parameters       : function parameters
    // Creator          : 03/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Status Update DocNo In Doc Temp
    // Return Type      : array
    public function FSxMPXAddUpdateDocNoToTemp($paDataWhere,$paTableAddUpdate){
        // Update DocNo Into DTTemp
        $this->db->where('FTXthDocNo','');
        $this->db->where('FTSessionID',$paDataWhere['FTSessionID']);
        $this->db->where('FTXthDocKey',$paTableAddUpdate['tTableHD']);
        $this->db->update('TCNTDocDTTmp',array(
            'FTXthDocNo'    => $paDataWhere['FTXphDocNo'],
            'FTBchCode'     => $paDataWhere['FTBchCode']
        ));

        // Update DocNo Into HDDisTemp
        $this->db->where('FTXthDocNo','');
        $this->db->where('FTSessionID',$paDataWhere['FTSessionID']);
        $this->db->update('TCNTDocHDDisTmp',array(
            'FTXthDocNo'    => $paDataWhere['FTXphDocNo'],
            'FTBchCode'     => $paDataWhere['FTBchCode']
        ));

        // Update DocNo Into DTDisTemp
        $this->db->where('FTXthDocNo','');
        $this->db->where('FTSessionID',$paDataWhere['FTSessionID']);
        $this->db->update('TCNTDocDTDisTmp',array(
            'FTXthDocNo'    => $paDataWhere['FTXphDocNo'],
            'FTBchCode'     => $paDataWhere['FTBchCode']
        ));

        // Update DocNo Into HDRefTmp
        $this->db->where('FTXthDocNo','');
        $this->db->where('FTSessionID',$paDataWhere['FTSessionID']);
        $this->db->update('TCNTDocHDRefTmp',array(
            'FTXthDocNo'    => $paDataWhere['FTXphDocNo']
        ));
        return;
    }

    // Functionality    : Move Document HDDisTemp To Document HDDis
    // Parameters       : function parameters
    // Creator          : 04/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Status Insert Tempt To DT
    // Return Type      : array
    public function FSaMPXMoveHdDisTempToHdDis($paDataWhere,$paTableAddUpdate){
        $tPXDocNo       = $paDataWhere['FTXphDocNo'];
        $tPXBchCode     = $paDataWhere['FTBchCode'];
        $tPXAgnCode     = $paDataWhere['FTAgnCode'];
        $tPXSessionID   = $this->session->userdata('tSesSessionID');
        if(isset($tPXDocNo) && !empty($tPXDocNo)){
            $this->db->where_in('FTXphDocNo',$tPXDocNo);
            $this->db->where_in('FTBchCode',$tPXBchCode);
            $this->db->delete($paTableAddUpdate['tTableHDDis']);
        }
        $tSQL   =   "   INSERT INTO ".$paTableAddUpdate['tTableHDDis']." (
                            FTAgnCode,FTBchCode,FTXphDocNo,FDXphDateIns,FTXphDisChgTxt,FTXphDisChgType,
                            FCXphTotalAfDisChg,FCXphDisChg,FCXphAmt )
                    ";
        $tSQL   .=  "   SELECT
                            '$tPXAgnCode' AS FTAgnCode,
                            HDDISTEMP.FTBchCode,
                            HDDISTEMP.FTXthDocNo,
                            HDDISTEMP.FDXtdDateIns,
                            HDDISTEMP.FTXtdDisChgTxt,
                            HDDISTEMP.FTXtdDisChgType,
                            HDDISTEMP.FCXtdTotalAfDisChg,
                            HDDISTEMP.FCXtdDisChg,
                            HDDISTEMP.FCXtdAmt
                        FROM TCNTDocHDDisTmp AS HDDISTEMP WITH (NOLOCK)
                        WHERE 1 = 1
                        AND HDDISTEMP.FTBchCode     = '$tPXBchCode'
                        AND HDDISTEMP.FTXthDocNo    = '$tPXDocNo'
                        AND HDDISTEMP.FTSessionID   = '$tPXSessionID'
                    ";
        $oQuery = $this->db->query($tSQL);
        return;
    }

    // Functionality    : Move Document DTTemp To Document DT
    // Parameters       : function parameters
    // Creator          : 03/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Status Insert Tempt To DT
    // Return Type      : array
    public function FSaMPXMoveDtTmpToDt($paDataWhere,$paTableAddUpdate){
        $tPXAgnCode     = $paDataWhere['FTAgnCode'];
        $tPXBchCode     = $paDataWhere['FTBchCode'];
        $tPXDocNo       = $paDataWhere['FTXphDocNo'];
        $tPXDocKey      = $paTableAddUpdate['tTableHD'];
        $tPXSessionID   = $this->session->userdata('tSesSessionID');
        
        if(isset($tPXDocNo) && !empty($tPXDocNo)){
            $this->db->where_in('FTXphDocNo',$tPXDocNo);
            $this->db->delete($paTableAddUpdate['tTableDT']);
        }

        $tSQL   = " INSERT INTO ".$paTableAddUpdate['tTableDT']." (
                        FTAgnCode,FTBchCode,FTXphDocNo,FNXpdSeqNo,FTPdtCode,FTXpdPdtName,FTPunCode,FTPunName,FCXpdFactor,FTXpdBarCode,FTXpdVatType,FTVatCode,FCXpdVatRate,
                        FTXpdSaleType,FCXpdSalePrice,FCXpdQty,FCXpdQtyAll,FCXpdSetPrice,FCXpdAmtB4DisChg,FTXpdDisChgTxt,FCXpdDis,FCXpdChg,FCXpdNet,FCXpdNetAfHD,
                        FCXpdVat,FCXpdVatable,FCXpdWhtAmt,FTXpdWhtCode,FCXpdWhtRate,FCXpdCostIn,FCXpdCostEx,FCXpdQtyLef,FCXpdQtyRfn,FTXpdStaPrcStk,FTXpdStaAlwDis,
                        FNXpdPdtLevel,FTXpdPdtParent,FCXpdQtySet,FTPdtStaSet,FTXpdRmk,FDLastUpdOn,FTLastUpdBy,FDCreateOn,FTCreateBy ) ";
        $tSQL   .=  "   SELECT 
                            '$tPXAgnCode' AS FTAgnCode,
                            DOCTMP.FTBchCode,
                            DOCTMP.FTXthDocNo,
                            ROW_NUMBER() OVER(ORDER BY DOCTMP.FNXtdSeqNo ASC) AS FNXtdSeqNo,
                            DOCTMP.FTPdtCode,
                            DOCTMP.FTXtdPdtName,
                            DOCTMP.FTPunCode,
                            DOCTMP.FTPunName,
                            DOCTMP.FCXtdFactor,
                            DOCTMP.FTXtdBarCode,
                            DOCTMP.FTXtdVatType,
                            DOCTMP.FTVatCode,
                            DOCTMP.FCXtdVatRate,
                            DOCTMP.FTXtdSaleType,
                            DOCTMP.FCXtdSalePrice,
                            DOCTMP.FCXtdQty,
                            DOCTMP.FCXtdQtyAll,
                            DOCTMP.FCXtdSetPrice,
                            DOCTMP.FCXtdAmtB4DisChg,
                            DOCTMP.FTXtdDisChgTxt,
                            DOCTMP.FCXtdDis,
                            DOCTMP.FCXtdChg,
                            DOCTMP.FCXtdNet,
                            DOCTMP.FCXtdNetAfHD,
                            DOCTMP.FCXtdVat,
                            DOCTMP.FCXtdVatable,
                            DOCTMP.FCXtdWhtAmt,
                            DOCTMP.FTXtdWhtCode,
                            DOCTMP.FCXtdWhtRate,
                            DOCTMP.FCXtdCostIn,
                            DOCTMP.FCXtdCostEx,
                            DOCTMP.FCXtdQtyLef,
                            DOCTMP.FCXtdQtyRfn,
                            DOCTMP.FTXtdStaPrcStk,
                            DOCTMP.FTXtdStaAlwDis,
                            DOCTMP.FNXtdPdtLevel,
                            DOCTMP.FTXtdPdtParent,
                            DOCTMP.FCXtdQtySet,
                            DOCTMP.FTXtdPdtStaSet,
                            DOCTMP.FTXtdRmk,
                            DOCTMP.FDLastUpdOn,
                            DOCTMP.FTLastUpdBy,
                            DOCTMP.FDCreateOn,
                            DOCTMP.FTCreateBy
                        FROM TCNTDocDTTmp DOCTMP WITH (NOLOCK)
                        WHERE 1 = 1
                        AND DOCTMP.FTBchCode    = '$tPXBchCode'
                        AND DOCTMP.FTXthDocNo   = '$tPXDocNo'
                        AND DOCTMP.FTXthDocKey  = '$tPXDocKey'
                        AND DOCTMP.FTSessionID  = '$tPXSessionID'
                        ORDER BY DOCTMP.FNXtdSeqNo ASC
        ";
        $oQuery = $this->db->query($tSQL);
        return;
    }

    // Functionality    : Move Document DTDisTemp To Document DTDis
    // Parameters       : function parameters
    // Creator          : 03/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Status Insert Tempt To DT
    // Return Type      : array
    public function FSaMPXMoveDtDisTempToDtDis($paDataWhere,$paTableAddUpdate){
        $tPXBchCode     = $paDataWhere['FTBchCode'];
        $tPXAgnCode     = $paDataWhere['FTAgnCode'];
        $tPXDocNo       = $paDataWhere['FTXphDocNo'];
        $tPXSessionID   = $this->session->userdata('tSesSessionID');
        
        if(isset($tPXDocNo) && !empty($tPXDocNo)){
            $this->db->where_in('FTXphDocNo',$tPXDocNo);
            $this->db->where_in('FTBchCode',$tPXBchCode);
            $this->db->delete($paTableAddUpdate['tTableDTDis']);
        }

        $tSQL   =   "   INSERT INTO ".$paTableAddUpdate['tTableDTDis']." (FTAgnCode,FTBchCode,FTXphDocNo,FNXpdSeqNo,FDXpdDateIns,FNXpdStaDis,FTXpdDisChgTxt,FTXpdDisChgType,FCXpdNet,FCXpdValue) ";
        $tSQL   .=  "   SELECT
                            '$tPXAgnCode' AS FTAgnCode,
                            DOCDISTMP.FTBchCode,
                            DOCDISTMP.FTXthDocNo,
                            DOCDISTMP.FNXtdSeqNo,
                            DOCDISTMP.FDXtdDateIns,
                            DOCDISTMP.FNXtdStaDis,
                            DOCDISTMP.FTXtdDisChgTxt,
                            DOCDISTMP.FTXtdDisChgType,
                            DOCDISTMP.FCXtdNet,
                            DOCDISTMP.FCXtdValue
                        FROM TCNTDocDTDisTmp DOCDISTMP WITH (NOLOCK)
                        WHERE 1=1
                        AND DOCDISTMP.FTBchCode     = '$tPXBchCode'
                        AND DOCDISTMP.FTXthDocNo    = '$tPXDocNo'
                        AND DOCDISTMP.FTSessionID   = '$tPXSessionID' 
                        ORDER BY DOCDISTMP.FNXtdSeqNo ASC ";
        $oQuery = $this->db->query($tSQL);
        return;
    }

    // Functionality    : Get Data Document HD
    // Parameters       : function parameters
    // Creator          : 04/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Data Document HD
    // Return Type      : array
    public function FSaMPXGetDataDocHD($paDataWhere){
        $tPXDocNo   = $paDataWhere['FTXthDocNo'];
        $nLngID     = $paDataWhere['FNLngID'];

        $tSQL       = " SELECT
                            DOCHD.FTAgnCode,
                            AGNL.FTAgnName,
                            DOCHD.FTBchCode,
                            BCHL.FTBchName,
                            SHP.FTMerCode,
                            MERL.FTMerName,
                            SHP.FTShpType,
                            SHP.FTShpCode,
                            SHPL.FTShpName,
                            POS.FTWahRefCode,
                            POSL.FTPosComName,
                            DOCHD.FTWahCode,
                            WAHL.FTWahName,

                            DOCHD.FTXphDocNo,
                            DOCHD.FNXphDocType,
                            DOCHD.FDXphDocDate,
                            DOCHD.FTXphCshOrCrd,
                            DOCHD.FTXphVATInOrEx,
                            DOCHD.FTDptCode,
                            DPTL.FTDptName,
                            DOCHD.FTUsrCode,
                            USRL.FTUsrName,
                            DOCHD.FTXphApvCode,
                            USRAPV.FTUsrName	AS FTXphApvName,
                            DOCHD.FTSplCode,
                            SPLL.FTSplName,
                            DOCHD.FTXphRefExt,
                            DOCHD.FDXphRefExtDate,
                            DOCHD.FTXphRefInt,
                            DOCHD.FDXphRefIntDate,
                            DOCHD.FTXphRefAE,
                            DOCHD.FNXphDocPrint,
                            DOCHD.FTRteCode,
                            DOCHD.FCXphRteFac,
                            DOCHD.FTXphRmk,

                            DOCHD.FTXphStaRefund,
                            DOCHD.FTXphStaDoc,
                            DOCHD.FTXphStaApv,
                            DOCHD.FTXphStaDelMQ,
                            /*DOCHD.FTXphStaPrcStk,*/
                            DOCHD.FTXphStaPaid,
                            
                            DOCHD.FNXphStaDocAct,
                            DOCHD.FNXphStaRef,

                            DOCHD.FDLastUpdOn,
                            DOCHD.FTLastUpdBy,
                            DOCHD.FDCreateOn,
                            DOCHD.FTCreateBy
                            
                        FROM TAPTPxHD DOCHD WITH (NOLOCK)
                        LEFT JOIN TCNMAgency_L      AGNL    WITH (NOLOCK)   ON DOCHD.FTAgnCode      = AGNL.FTAgnCode    AND AGNL.FNLngID        = $nLngID
                        LEFT JOIN TCNMBranch_L      BCHL    WITH (NOLOCK)   ON DOCHD.FTBchCode      = BCHL.FTBchCode    AND BCHL.FNLngID	    = $nLngID
                        LEFT JOIN TCNMShop          SHP     WITH (NOLOCK)   ON DOCHD.FTShpCode      = SHP.FTShpCode     AND DOCHD.FTBchCode      = SHP.FTBchCode
                        LEFT JOIN TCNMShop_L        SHPL    WITH (NOLOCK)   ON DOCHD.FTShpCode      = SHPL.FTShpCode	AND DOCHD.FTBchCode      = SHPL.FTBchCode AND SHPL.FNLngID	    = $nLngID
                        LEFT JOIN TCNMMerchant_L    MERL    WITH (NOLOCK)   ON SHP.FTMerCode        = MERL.FTMerCode	AND MERL.FNLngID	    = $nLngID

                        LEFT JOIN TCNMWaHouse_L     WAHL    WITH (NOLOCK)   ON DOCHD.FTWahCode      = WAHL.FTWahCode	AND DOCHD.FTBchCode      = WAHL.FTBchCode  AND WAHL.FNLngID	    = $nLngID
                        LEFT JOIN TCNMWaHouse       POS     WITH (NOLOCK)   ON DOCHD.FTWahCode      = POS.FTWahCode	    AND DOCHD.FTBchCode      = POS.FTBchCode	AND POS.FTWahStaType    = '6'
                        LEFT JOIN TCNMPosLastNo		POSL    WITH (NOLOCK)   ON POS.FTWahRefCode     = POSL.FTPosCode
                        LEFT JOIN TCNMUsrDepart_L	DPTL    WITH (NOLOCK)   ON DOCHD.FTDptCode      = DPTL.FTDptCode	AND DPTL.FNLngID	= $nLngID
                        LEFT JOIN TCNMUser_L        USRL    WITH (NOLOCK)   ON DOCHD.FTUsrCode      = USRL.FTUsrCode	AND USRL.FNLngID	= $nLngID
                        LEFT JOIN TCNMUser_L        USRAPV	WITH (NOLOCK)   ON DOCHD.FTXphApvCode	= USRAPV.FTUsrCode	AND USRAPV.FNLngID	= $nLngID
                        LEFT JOIN TCNMSpl_L         SPLL    WITH (NOLOCK)   ON DOCHD.FTSplCode		= SPLL.FTSplCode	AND SPLL.FNLngID	= $nLngID
                        WHERE 1=1 AND DOCHD.FTXphDocNo = '$tPXDocNo' ";
                        
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aDetail = $oQuery->row_array();
            $aResult    = array(
                'raItems'   => $aDetail,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        }else{
            $aResult    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found.',
            );
        }
        return $aResult;
    }

    // Functionality    : Get Data Document HD Spl
    // Parameters       : function parameters
    // Creator          : 04/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Data Document HD Ref
    // Return Type      : array
    public function FSaMPXGetDataDocHDSpl($paDataWhere){
        $tPXDocNo   = $paDataWhere['FTXthDocNo'];
        $nLngID     = $paDataWhere['FNLngID'];
        $tSQL       = " SELECT
                            HDSPL.FTBchCode,
                            HDSPL.FTXphDocNo,
                            HDSPL.FTXphDstPaid,
                            HDSPL.FNXphCrTerm,
                            HDSPL.FDXphDueDate,
                            HDSPL.FDXphBillDue,
                            HDSPL.FTXphCtrName,
                            HDSPL.FDXphTnfDate,
                            HDSPL.FTXphRefTnfID,
                            HDSPL.FTXphRefVehID,
                            HDSPL.FTXphRefInvNo,
                            HDSPL.FTXphQtyAndTypeUnit,
                            HDSPL.FNXphShipAdd,
                            SHIP_Add.FTAddVersion           AS FTXphShipAddVersion,
                            SHIP_Add.FTAddV1No              AS FTXphShipAddNo,
                            SHIP_Add.FTAddV1Soi				AS FTXphShipAddSoi,
                            SHIP_Add.FTAddV1Village         AS FTXphShipAddVillage,
                            SHIP_Add.FTAddV1Road			AS FTXphShipAddRoad,
                            SHIP_SUDIS.FTSudName			AS FTXphShipSubDistrict,
                            SHIP_DIS.FTDstName				AS FTXphShipDistrict,
                            SHIP_PVN.FTPvnName				AS FTXphShipProvince,
                            SHIP_Add.FTAddV1PostCode	    AS FTXphShipPosCode,
                            SHIP_Add.FTAddTaxNo             AS FTXphShipTaxNo,
                            HDSPL.FNXphTaxAdd               AS FNXphTaxAdd,
                            TAX_Add.FTAddVersion            AS FTXphTaxAddVersion,
                            TAX_Add.FTAddV1No               AS FTXphTaxAddNo,
                            TAX_Add.FTAddV1Soi				AS FTXphTaxAddSoi,
                            TAX_Add.FTAddV1Village		    AS FTXphTaxAddVillage,
                            TAX_Add.FTAddV1Road				AS FTXphTaxAddRoad,
                            TAX_Add.FTAddTaxNo	            AS FTXphTaxAddTAX,
                            TAX_SUDIS.FTSudName				AS FTXphTaxSubDistrict,
                            TAX_DIS.FTDstName               AS FTXphTaxDistrict,
                            TAX_PVN.FTPvnName               AS FTXphTaxProvince,
                            TAX_Add.FTAddV1PostCode		    AS FTXphTaxPosCode
                        FROM TAPTPxHDSpl HDSPL  WITH (NOLOCK)
                        LEFT JOIN TCNMAddress_L			SHIP_Add    WITH (NOLOCK)   ON HDSPL.FNXphShipAdd       = SHIP_Add.FNAddSeqNo	AND SHIP_Add.FNLngID    = $nLngID
                        LEFT JOIN TCNMSubDistrict_L     SHIP_SUDIS 	WITH (NOLOCK)	ON SHIP_Add.FTAddV1SubDist	= SHIP_SUDIS.FTSudCode	AND SHIP_SUDIS.FNLngID  = $nLngID
                        LEFT JOIN TCNMDistrict_L        SHIP_DIS    WITH (NOLOCK)	ON SHIP_Add.FTAddV1DstCode	= SHIP_DIS.FTDstCode    AND SHIP_DIS.FNLngID    = $nLngID
                        LEFT JOIN TCNMProvince_L        SHIP_PVN    WITH (NOLOCK)	ON SHIP_Add.FTAddV1PvnCode	= SHIP_PVN.FTPvnCode    AND SHIP_PVN.FNLngID    = $nLngID
                        LEFT JOIN TCNMAddress_L			TAX_Add     WITH (NOLOCK)   ON HDSPL.FNXphTaxAdd        = TAX_Add.FNAddSeqNo	AND TAX_Add.FNLngID		= $nLngID
                        LEFT JOIN TCNMSubDistrict_L     TAX_SUDIS 	WITH (NOLOCK)	ON TAX_Add.FTAddV1SubDist   = TAX_SUDIS.FTSudCode	AND TAX_SUDIS.FNLngID	= $nLngID
                        LEFT JOIN TCNMDistrict_L        TAX_DIS     WITH (NOLOCK)	ON TAX_Add.FTAddV1DstCode   = TAX_DIS.FTDstCode     AND TAX_DIS.FNLngID     = $nLngID
                        LEFT JOIN TCNMProvince_L        TAX_PVN     WITH (NOLOCK)	ON TAX_Add.FTAddV1PvnCode   = TAX_PVN.FTPvnCode		AND TAX_PVN.FNLngID     = $nLngID
                        WHERE 1=1 AND HDSPL.FTXphDocNo = '$tPXDocNo'
        ";
        
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aDetail = $oQuery->row_array();
            $aResult    = array(
                'raItems'   => $aDetail,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        }else{
            $aResult    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found.',
            );
        }
        return $aResult;

    }

    // Functionality    : Move Data HD Dis To Temp
    // Parameters       : function parameters
    // Creator          : 04/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : -
    // Return Type      : None
    public function FSxMPXMoveHDDisToTemp($paDataWhere){
        $tPXDocNo       = $paDataWhere['FTXthDocNo'];
        $this->db->where('FTXthDocNo',$tPXDocNo);
        $this->db->delete('TCNTDocHDDisTmp');
        
        $tSQL       = " INSERT INTO TCNTDocHDDisTmp (
                            FTBchCode,
                            FTXthDocNo,
                            FDXtdDateIns,
                            FTXtdDisChgTxt,
                            FTXtdDisChgType,
                            FCXtdTotalAfDisChg,
                            FCXtdTotalB4DisChg,
                            FCXtdDisChg,
                            FCXtdAmt,
                            FTSessionID,
                            FDLastUpdOn,
                            FDCreateOn,
                            FTLastUpdBy,
                            FTCreateBy
                        )
                        SELECT 
                            PXHDDis.FTBchCode,
                            PXHDDis.FTXphDocNo,
                            PXHDDis.FDXphDateIns,
                            PXHDDis.FTXphDisChgTxt,
                            PXHDDis.FTXphDisChgType,
                            PXHDDis.FCXphTotalAfDisChg,
                            (ISNULL(NULL,0)) AS FCXtdTotalB4DisChg,
                            PXHDDis.FCXphDisChg,
                            PXHDDis.FCXphAmt,
                            CONVERT(VARCHAR,'".$this->session->userdata('tSesSessionID')."')    AS FTSessionID,
                            CONVERT(DATETIME,'".date('Y-m-d H:i:s')."') AS FDLastUpdOn,
                            CONVERT(DATETIME,'".date('Y-m-d H:i:s')."') AS FDCreateOn,
                            CONVERT(VARCHAR,'".$this->session->userdata('tSesUsername')."') AS FTLastUpdBy,
                            CONVERT(VARCHAR,'".$this->session->userdata('tSesUsername')."') AS FTCreateBy
                        FROM TAPTPxHDDis PXHDDis WITH (NOLOCK)
                        WHERE 1=1 AND PXHDDis.FTXphDocNo = '$tPXDocNo'
        ";
        $oQuery = $this->db->query($tSQL);
        return;
    }

    // Functionality    : Move Data DT To DTTemp
    // Parameters       : function parameters
    // Creator          : 04/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : -
    // Return Type      : None
    public function FSxMPXMoveDTToDTTemp($paDataWhere){
        $tPXDocNo       = $paDataWhere['FTXthDocNo'];
        $tPXDocKey      = $paDataWhere['FTXthDocKey'];

        // Delect Document DTTemp By Doc No
        $this->db->where('FTXthDocNo',$tPXDocNo);
        $this->db->delete('TCNTDocDTTmp');

        $tSQL   = " INSERT INTO TCNTDocDTTmp (
                        FTBchCode,FTXthDocNo,FNXtdSeqNo,FTXthDocKey,FTPdtCode,FTXtdPdtName,FTPunCode,FTPunName,FCXtdFactor,FTXtdBarCode,
                        FTXtdVatType,FTVatCode,FCXtdVatRate,FTXtdSaleType,FCXtdSalePrice,FCXtdQty,FCXtdQtyAll,FCXtdSetPrice,
                        FCXtdAmtB4DisChg,FTXtdDisChgTxt,FCXtdDis,FCXtdChg,FCXtdNet,FCXtdNetAfHD,FCXtdVat,FCXtdVatable,FCXtdWhtAmt,
                        FTXtdWhtCode,FCXtdWhtRate,FCXtdCostIn,FCXtdCostEx,FCXtdQtyLef,FCXtdQtyRfn,FTXtdStaPrcStk,FTXtdStaAlwDis,
                        FNXtdPdtLevel,FTXtdPdtParent,FCXtdQtySet,FTXtdPdtStaSet,FTXtdRmk,
                        FTSessionID,FDLastUpdOn,FDCreateOn,FTLastUpdBy,FTCreateBy )
                    SELECT
                        PXDT.FTBchCode,
                        PXDT.FTXphDocNo,
                        PXDT.FNXpdSeqNo,
                        CONVERT(VARCHAR,'".$tPXDocKey."') AS FTXthDocKey,
                        PXDT.FTPdtCode,
                        PXDT.FTXpdPdtName,
                        PXDT.FTPunCode,
                        PXDT.FTPunName,
                        PXDT.FCXpdFactor,
                        PXDT.FTXpdBarCode,
                        PXDT.FTXpdVatType,
                        PXDT.FTVatCode,
                        PXDT.FCXpdVatRate,
                        PXDT.FTXpdSaleType,
                        PXDT.FCXpdSalePrice,
                        PXDT.FCXpdQty,
                        PXDT.FCXpdQtyAll,
                        PXDT.FCXpdSetPrice,
                        PXDT.FCXpdAmtB4DisChg,
                        PXDT.FTXpdDisChgTxt,
                        PXDT.FCXpdDis,
                        PXDT.FCXpdChg,
                        PXDT.FCXpdNet,
                        PXDT.FCXpdNetAfHD,
                        PXDT.FCXpdVat,
                        PXDT.FCXpdVatable,
                        PXDT.FCXpdWhtAmt,
                        PXDT.FTXpdWhtCode,
                        PXDT.FCXpdWhtRate,
                        PXDT.FCXpdCostIn,
                        PXDT.FCXpdCostEx,
                        PXDT.FCXpdQtyLef,
                        PXDT.FCXpdQtyRfn,
                        PXDT.FTXpdStaPrcStk,
                        PXDT.FTXpdStaAlwDis,
                        PXDT.FNXpdPdtLevel,
                        PXDT.FTXpdPdtParent,
                        PXDT.FCXpdQtySet,
                        PXDT.FTPdtStaSet,
                        PXDT.FTXpdRmk,
                        CONVERT(VARCHAR,'".$this->session->userdata('tSesSessionID')."') AS FTSessionID,
                        CONVERT(DATETIME,'".date('Y-m-d H:i:s')."') AS FDLastUpdOn,
                        CONVERT(DATETIME,'".date('Y-m-d H:i:s')."') AS FDCreateOn,
                        CONVERT(VARCHAR,'".$this->session->userdata('tSesUsername')."') AS FTLastUpdBy,
                        CONVERT(VARCHAR,'".$this->session->userdata('tSesUsername')."') AS FTCreateBy
                    FROM TAPTPxDT AS PXDT WITH (NOLOCK)
                    WHERE 1=1 AND PXDT.FTXphDocNo = '$tPXDocNo'
                    ORDER BY PXDT.FNXpdSeqNo ASC ";
        $oQuery = $this->db->query($tSQL);
        return;
    }

    // Functionality    : Move Data DT Dis To DT Dis Temp
    // Parameters       : function parameters
    // Creator          : 04/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : -
    // Return Type      : None
    public function FSxMPXMoveDTDisToDTDisTemp($paDataWhere){
        $tPXDocNo       = $paDataWhere['FTXthDocNo'];
        
        // Delect Document DTDisTemp By Doc No
        $this->db->where('FTXthDocNo',$tPXDocNo);
        $this->db->delete('TCNTDocDTDisTmp');

        $tSQL   = " INSERT INTO TCNTDocDTDisTmp (
                        FTBchCode,
                        FTXthDocNo,
                        FNXtdSeqNo,
                        FTSessionID,
                        FDXtdDateIns,
                        FNXtdStaDis,
                        FTXtdDisChgType,
                        FCXtdNet,
                        FCXtdValue,
                        FDLastUpdOn,
                        FDCreateOn,
                        FTLastUpdBy,
                        FTCreateBy,
                        FTXtdDisChgTxt
                    )
                    SELECT
                        PXDTDis.FTBchCode,
                        PXDTDis.FTXphDocNo,
                        PXDTDis.FNXpdSeqNo,
                        CONVERT(VARCHAR,'".$this->session->userdata('tSesSessionID')."')    AS FTSessionID,
                        PXDTDis.FDXpdDateIns,
                        PXDTDis.FNXpdStaDis,
                        PXDTDis.FTXpdDisChgType,
                        PXDTDis.FCXpdNet,
                        PXDTDis.FCXpdValue,
                        CONVERT(DATETIME,'".date('Y-m-d H:i:s')."') AS FDLastUpdOn,
                        CONVERT(DATETIME,'".date('Y-m-d H:i:s')."') AS FDCreateOn,
                        CONVERT(VARCHAR,'".$this->session->userdata('tSesUsername')."') AS FTLastUpdBy,
                        CONVERT(VARCHAR,'".$this->session->userdata('tSesUsername')."') AS FTCreateBy,
                        PXDTDis.FTXpdDisChgTxt
                    FROM TAPTPxDTDis PXDTDis
                    WHERE 1=1 AND PXDTDis.FTXphDocNo = '$tPXDocNo'
                    ORDER BY PXDTDis.FNXpdSeqNo ASC
            ";
        $oQuery = $this->db->query($tSQL);
        return;
    }
    
    // Functionality    : Cancel Document Data
    // Parameters       : function parameters
    // Creator          : 09/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : -
    // Return Type      : None
    public function FSaMPXCancelDocument($paDataUpdate){
        $this->db->trans_begin();
        $this->db->set('FTXphStaDoc','3');
        $this->db->where('FTXphDocNo',$paDataUpdate['tDocNo']);
        $this->db->update('TAPTPxHD');
        if( $this->db->trans_status() === FALSE ){
            $this->db->trans_rollback();
            $aDatRetrun = array(
                'nStaEvent' => '900',
                'tStaMessg' => "Error Cannot Update Status Cancel Document."
            );
        }else{
            $this->db->trans_commit();
            $aDatRetrun = array(
                'nStaEvent' => '1',
                'tStaMessg' => "Update Status Document Cancel Success."
            );
        }
        return $aDatRetrun;
    }

    // Functionality    : Approve Document Data
    // Parameters       : function parameters
    // Creator          : 09/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : -
    // Return Type      : None
    public function FSaMPXApproveDocument($paDataUpdate){
        // TAPTPxHD
        $this->db->trans_begin();
        $dLastUpdOn = date('Y-m-d H:i:s');
        $tLastUpdBy = $this->session->userdata('tSesUsername');


        $this->db->set('FDLastUpdOn',$dLastUpdOn);
        $this->db->set('FTLastUpdBy',$tLastUpdBy);
        // $this->db->set('FTXphStaPrcStk',2);
        $this->db->set('FTXphStaApv','1');
        $this->db->set('FTXphApvCode',$paDataUpdate['tApvCode']);
        $this->db->where('FTXphDocNo',$paDataUpdate['tDocNo']);
        $this->db->update('TAPTPxHD');

        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $aDatRetrun = array(
                'nStaEvent' => '900',
                'tStaMessg' => "Error Cannot Update Status Approve Document."
            );
        }else{
            $this->db->trans_commit();
            $aDatRetrun = array(
                'nStaEvent' => '1',
                'tStaMessg' => "Update Status Document Approve Success."
            );
        }
        return $aDatRetrun;
    }

    // Functionality    : Count Product Bar
    // Parameters       : function parameters
    // Creator          : 30/07/2019 Wasin(Yoshi)
    // Last Modified    : 24/02/2021 supawat
    // Return           : Array Data Find Product
    // Return Type      : Array
    public function FSaCPXCountPdtBarInTablePdtBar($paDataChkINDB){
        $tPXDataSearchAndAdd    = $paDataChkINDB['tPXDataSearchAndAdd'];
        $nLangEdit              = $paDataChkINDB['nLangEdit'];

        $tSQL   = " SELECT 
                        PDTBAR.FTPdtCode,
                        PDT_L.FTPdtName,
                        PDTBAR.FTBarCode,
                        PDTBAR.FTPunCode,
                        PUN_L.FTPunName
                    FROM TCNMPdtBar         PDTBAR  WITH(NOLOCK)
                    LEFT JOIN TCNMPdt		PDT     WITH(NOLOCK)	ON PDTBAR.FTPdtCode = PDT.FTPdtCode
                    LEFT JOIN TCNMPdt_L	    PDT_L   WITH(NOLOCK)	ON PDT.FTPdtCode	= PDT_L.FTPdtCode   AND PDT_L.FNLngID   = $nLangEdit
                    LEFT JOIN TCNMPdtUnit   PUN     WITH(NOLOCK)	ON PDTBAR.FTPunCode	= PUN.FTPunCode
                    LEFT JOIN TCNMPdtUnit_L	PUN_L   WITH(NOLOCK)	ON PUN.FTPunCode    = PUN_L.FTPunCode   AND PUN_L.FNLngID   = $nLangEdit
                    WHERE 1=1
                    AND PDTBAR.FTBarStaUse 	= 1
                    AND (PDTBAR.FTPdtCode = '$tPXDataSearchAndAdd' OR PDTBAR.FTBarCode = '$tPXDataSearchAndAdd')
        ";
        $oQuery         = $this->db->query($tSQL);
        $aDataReturn    = $oQuery->result_array();
        unset($oQuery);
        return $aDataReturn;
    }

    //?????????????????????????????? ADD ?????????????????? ????????????????????????????????????????????? where session
    public function FSaMCENDeletePDTInTmp($paParams){
        $tSessionID = $this->session->userdata('tSesSessionID');
        $this->db->where_in('FTSessionID', $tSessionID);
        $this->db->delete('TCNTDocDTTmp');
        if($this->db->affected_rows() > 0){
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'success',
            );
        }else{
            $aStatus = array(
                'rtCode' => '905',
                'rtDesc' => 'cannot Delete Item.',
            );
        }
        return $aStatus;
    }

    public function FSaMPXCalVatLastDT($paData){
        $tDocNo         = $paData['tDocNo'];
        $tBchCode       = $paData['tBchCode'];
        $tSessionID     = $paData['tSessionID'];
        $tDataVatInOrEx = $paData['tDataVatInOrEx'];

        $cSumFCXtdVat = " SELECT
            SUM (ISNULL(DOCTMP.FCXtdVat, 0)) AS FCXtdVat
            FROM
                TCNTDocDTTmp DOCTMP WITH (NOLOCK)
            WHERE
                1 = 1
            AND DOCTMP.FTSessionID = '$tSessionID'
            AND DOCTMP.FTXthDocKey = 'TAPTPxHD'
            AND DOCTMP.FTXthDocNo = '$tDocNo'
            --AND DOCTMP.FTXtdVatType = 1
            AND DOCTMP.FCXtdVatRate > 0  ";

        $tSql ="UPDATE TCNTDocDTTmp
                SET FCXtdVat = (
                    ($cSumFCXtdVat) - (
                        SELECT
                            SUM (DTTMP.FCXtdVat) AS FCXtdVat
                        FROM
                            TCNTDocDTTmp DTTMP
                        WHERE
                            DTTMP.FTSessionID = '$tSessionID'
                        AND DTTMP.FTXthDocNo = '$tDocNo'
                        AND DTTMP.FTXtdVatType = 1
                        AND DTTMP.FNXtdSeqNo != (
                            SELECT
                                TOP 1 SUBDTTMP.FNXtdSeqNo
                            FROM
                                TCNTDocDTTmp SUBDTTMP
                            WHERE
                                SUBDTTMP.FTSessionID = '$tSessionID'
                            AND SUBDTTMP.FTXthDocNo = '$tDocNo'
                            AND SUBDTTMP.FTXtdVatType = 1
                            ORDER BY
                                SUBDTTMP.FNXtdSeqNo DESC
                        )
                    )
                ),
                FCXtdVatable = (
                    CASE
                        WHEN $tDataVatInOrEx  = 1 --??????????????? 
                        THEN FCXtdNet - (
                            ($cSumFCXtdVat) - (
                                SELECT
                                    SUM (DTTMP.FCXtdVat) AS FCXtdVat
                                FROM
                                    TCNTDocDTTmp DTTMP
                                WHERE
                                    DTTMP.FTSessionID = '$tSessionID'
                                AND DTTMP.FTXthDocNo = '$tDocNo'
                                AND DTTMP.FTXtdVatType = 1
                                AND DTTMP.FNXtdSeqNo != (
                                    SELECT
                                        TOP 1 SUBDTTMP.FNXtdSeqNo
                                    FROM
                                        TCNTDocDTTmp SUBDTTMP
                                    WHERE
                                        SUBDTTMP.FTSessionID = '$tSessionID'
                                    AND SUBDTTMP.FTXthDocNo = '$tDocNo'
                                    AND SUBDTTMP.FTXtdVatType = 1
                                    ORDER BY
                                        SUBDTTMP.FNXtdSeqNo DESC
                                )
                            )
                        )
                        WHEN $tDataVatInOrEx  = 2 --??????????????????
                        THEN FCXtdNetAfHD
                    ELSE 0 END 
                )
                WHERE
                    FTSessionID = '$tSessionID'
                AND FTXthDocNo = '$tDocNo'
                AND FNXtdSeqNo = (
                    SELECT
                        TOP 1 FNXtdSeqNo
                    FROM
                        TCNTDocDTTmp WHDTTMP
                    WHERE
                        WHDTTMP.FTSessionID = '$tSessionID'
                    AND WHDTTMP.FTXthDocNo = '$tDocNo'
                    AND WHDTTMP.FTXtdVatType = 1
                    ORDER BY
                        WHDTTMP.FNXtdSeqNo DESC
                )";

        $nRSCounDT =  $this->db->where('FTSessionID',$tSessionID)->where('FTXthDocNo',$tDocNo)->where('FTXtdVatType','1')->get('TCNTDocDTTmp')->num_rows();
        
        if($nRSCounDT>1){
            $this->db->query($tSql);
            if($this->db->affected_rows() > 0){
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'success',
                );
            }else{
                $aStatus = array(
                    'rtCode' => '905',
                    'rtDesc' => 'cannot Delete Item.',
                );
            }
        }else{
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'success',
            );
        }
        return $aStatus;
    }   

    //??????????????????????????????????????????????????????????????? Vat ????????????
    public function FSxMPXFindDetailSPL($paDataWhere){
        $tPXDocNo   = $paDataWhere['FTXthDocNo'];
        $tSQL   = " SELECT TOP 1 FTVatCode , FCXpdVatRate FROM TAPTPxDT WHERE FTXphDocNo = '$tPXDocNo' ";
        $oQuery = $this->db->query($tSQL);
        if($oQuery->num_rows() > 0){
            $aDataReturn = $oQuery->row_array();
        }else{
            $aDataReturn = array();
        }
        return $aDataReturn;
    }

    //?????????????????????????????????????????????????????? SPL ????????????????????? ????????????????????????????????????????????? VAT ????????????
    public function FSaMPXChangeSPLAffectNewVAT($paData){
        $this->db->set('FTVatCode', $paData['FTVatCode']);
        $this->db->set('FCXtdVatRate', $paData['FCXtdVatRate']);
        $this->db->where('FTSessionID',$paData['tSessionID']);
        $this->db->where('FTXthDocKey',$paData['tDocKey']);
        $this->db->where('FTXthDocNo',$paData['tPXDocNo']);
        $this->db->where('FTBchCode',$paData['tBCHCode']);
        $this->db->update('TCNTDocDTTmp');
    }

    // Create By : Napat(Jame) 05/04/2021
    // ???????????????????????????????????? Ref IN PO Move ????????????????????????????????????????????? PO ??????????????? Tmp PX
    public function FSaMPXMovePODTToDocTmp($paData){
        $tDate      = date('Y-m-d h:i:s');
        $tUsername  = $this->session->userdata('tSesUsername');

        $tSQL =  "  INSERT INTO TCNTDocDTTmp (FTBchCode, FTXthDocNo, FNXtdSeqNo,FTXthDocKey,FTPdtCode,FTXtdPdtName,FCXtdFactor,FTPunCode,FTPunName,
                    FTXtdBarCode,FTXtdVatType,FTVatCode,FCXtdVatRate,FTXtdStaAlwDis,FTXtdSaleType,FCXtdSalePrice,FCXtdQty,
                    FCXtdQtyAll,FCXtdSetPrice,FTSessionID,FDLastUpdOn,FTLastUpdBy,FDCreateOn,FTCreateBy)
                 ";
        $tSQL .= "  SELECT FTBchCode, '".$paData['tDocNo']."', FNXpdSeqNo,'".$paData['tDocKey']."',FTPdtCode,FTXpdPdtName,FCXpdFactor,FTPunCode,FTPunName,
                    FTXpdBarCode,FTXpdVatType,FTVatCode,FCXpdVatRate,FTXpdStaAlwDis,FTXpdSaleType,FCXpdSalePrice,FCXpdQty,
                    FCXpdQtyAll,FCXpdSetPrice,'".$paData['tSessionID']."','".$tDate."','".$tUsername."','".$tDate."','".$tUsername."'  
                    FROM TAPTPoDT WITH(NOLOCK)
                    WHERE FTXphDocNo = '".$paData['tPODocNo']."' 
                 ";

        $this->db->query($tSQL);
    }

    // Create By: Napat(Jame) 28/10/2021
    public function FSaMPXAddHDDocRef($paDataDocHDRef){
        $tPXDocNo       = $paDataDocHDRef['FTXphDocNo'];
        $tPXBchCode     = $paDataDocHDRef['FTBchCode'];
        $tPXAgnCode     = $paDataDocHDRef['FTAgnCode'];
        $tPXRefType     = $paDataDocHDRef['FTXphRefType'];

        $this->db->where('FTXphDocNo',$tPXDocNo);
        $this->db->where('FTBchCode',$tPXBchCode);
        $this->db->where('FTAgnCode',$tPXAgnCode);
        $this->db->where('FTXphRefType',$tPXRefType);
        $this->db->delete('TAPTPxHDDocRef');

        $this->db->insert('TAPTPxHDDocRef',$paDataDocHDRef);
    }

    // Create By: Napat(Jame) 28/10/2021
    public function FSaMPXGetDataDocHDRef($paDataWhere){
        $tPXDocNo   = $paDataWhere['FTXthDocNo'];
        $tSQL       = " SELECT
                            HDREF.FTXphRefDocNo,
                            HDREF.FDXphRefDocDate,
                            HDREF.FTXphRefType
                        FROM TAPTPxHDDocRef HDREF WITH (NOLOCK)
                        WHERE HDREF.FTXphDocNo = '$tPXDocNo'
        ";
        $oQuery = $this->db->query($tSQL);
        if ( $oQuery->num_rows() > 0 ){
            $aResult    = array(
                'aItems'   => $oQuery->result_array(),
                'tCode'    => '1',
                'tDesc'    => 'found data',
            );
        }else{
            $aResult    = array(
                'tCode'    => '800',
                'tDesc'    => 'data not found.',
            );
        }
        return $aResult;
    }

    // Create By : Napat(Jame) 02/11/2021
    public function FSxMPXMoveHDRefToHDRefTemp($paData){

        $FTXthDocNo     = $paData['FTXthDocNo'];
        $FTXthDocKey    = $paData['FTXthDocKey'];
        $FTSessionID    = $paData['FTSessionID']; 

        // ????????????????????????????????????????????? temp ????????????
        // $this->db->where('FTXthDocKey',$FTXthDocKey);
        // $this->db->where('FTSessionID',$FTSessionID);
        // $this->db->delete('TCNTDocHDRefTmp');

        $tSQL = " INSERT INTO TCNTDocHDRefTmp (FTXthDocNo, FTXthRefDocNo, FTXthRefType, FTXthRefKey, FDXthRefDocDate, FTXthDocKey, FTSessionID , FDCreateOn)";
        $tSQL .= "  SELECT 
                        FTXphDocNo,
                        FTXphRefDocNo,
                        FTXphRefType,
                        FTXphRefKey,
                        FDXphRefDocDate,
                        '$FTXthDocKey' AS FTXthDocKey,
                        '$FTSessionID' AS FTSessionID,
                        CONVERT(DATETIME,'".date('Y-m-d H:i:s')."') AS FDCreateOn
                    FROM TAPTPxHDDocRef
                    WHERE FTXphDocNo = '$FTXthDocNo'
                 ";
        $this->db->query($tSQL);

    }

    // Create By : Napat(Jame) 02/11/2021
    public function FSaMPXGetDataHDRefTmp($paData){

        $tTableTmpHDRef = $paData['tTableTmpHDRef'];
        $tTableHDDocRef = $paData['tTableHDDocRef'];
        $FTXthDocNo     = $paData['FTXthDocNo'];
        $FTXthDocKey    = $paData['FTXthDocKey'];
        $FTSessionID    = $paData['FTSessionID'];

        $tSQL = "   SELECT FTXthDocNo, FTXthRefDocNo, FTXthRefType, FTXthRefKey, FDXthRefDocDate
                    FROM $tTableTmpHDRef
                    WHERE FTXthDocNo  = '$FTXthDocNo'
                      AND FTXthDocKey = '$FTXthDocKey'
                      AND FTSessionID = '$FTSessionID'
                 ";
        $oQuery = $this->db->query($tSQL);
        if ( $oQuery->num_rows() > 0 ){
            $aResult    = array(
                'aItems'   => $oQuery->result_array(),
                'tCode'    => '1',
                'tDesc'    => 'found data',
            );
        }else{
            $aResult    = array(
                'tCode'    => '800',
                'tDesc'    => 'data not found.',
            );
        }
        return $aResult;

    }

    // Create By : Napat(Jame) 02/11/2021
    public function FSaMPXAddEditHDRefTmp($paDataWhere,$paDataAddEdit){

        $tRefDocNo = ( empty($paDataWhere['tPXRefDocNoOld']) ? $paDataAddEdit['FTXthRefDocNo'] : $paDataWhere['tPXRefDocNoOld'] );

        $tSQL = " SELECT FTXthRefDocNo FROM TCNTDocHDRefTmp 
                  WHERE FTXthDocNo    = '".$paDataWhere['FTXthDocNo']."' 
                    AND FTXthDocKey   = '".$paDataWhere['FTXthDocKey']."' 
                    AND FTSessionID   = '".$paDataWhere['FTSessionID']."' 
                    AND FTXthRefDocNo = '".$tRefDocNo."'
                ";
        $oQuery = $this->db->query($tSQL);
        $this->db->trans_begin();
        if ( $oQuery->num_rows() > 0 ){
            $this->db->where('FTXthRefDocNo',$tRefDocNo);
            $this->db->where('FTXthDocNo',$paDataWhere['FTXthDocNo']);
            $this->db->where('FTXthDocKey',$paDataWhere['FTXthDocKey']);
            $this->db->where('FTSessionID',$paDataWhere['FTSessionID']);
            $this->db->update('TCNTDocHDRefTmp',$paDataAddEdit);
        }else{
            $aDataAdd = array_merge($paDataAddEdit,array(
                'FTXthDocNo'  => $paDataWhere['FTXthDocNo'],
                'FTXthDocKey' => $paDataWhere['FTXthDocKey'],
                'FTSessionID' => $paDataWhere['FTSessionID']
            ));
            $this->db->insert('TCNTDocHDRefTmp',$aDataAdd);
        }

        if ( $this->db->trans_status() === FALSE ) {
            $this->db->trans_rollback();
            $aResult = array(
                'nStaEvent' => '800',
                'tStaMessg' => 'Add/Edit HDDocRef Error'
            );
        } else {
            $this->db->trans_commit();
            $aResult = array(
                'nStaEvent' => '1',
                'tStaMessg' => 'Add/Edit HDDocRef Success'
            );
        }
        return $aResult;
    }

    public function FSxMPXMoveHDRefTmpToHDRef($paDataWhere,$paTableAddUpdate){
        $tPXBchCode     = $paDataWhere['FTBchCode'];
        $tPXAgnCode     = $paDataWhere['FTAgnCode'];
        $tPXDocNo       = $paDataWhere['FTXphDocNo'];
        $tPXSessionID   = $this->session->userdata('tSesSessionID');
        
        if(isset($tPXDocNo) && !empty($tPXDocNo)){
            $this->db->where('FTAgnCode',$tPXAgnCode);
            $this->db->where('FTBchCode',$tPXBchCode);
            $this->db->where('FTXphDocNo',$tPXDocNo);
            $this->db->delete($paTableAddUpdate['tTableHDRef']);
        }

        $tSQL   =   "   INSERT INTO ".$paTableAddUpdate['tTableHDRef']." (FTAgnCode, FTBchCode, FTXphDocNo, FTXphRefDocNo, FTXphRefType, FTXphRefKey, FDXphRefDocDate) ";
        $tSQL   .=  "   SELECT
                            '$tPXAgnCode' AS FTAgnCode,
                            '$tPXBchCode' AS FTBchCode,
                            FTXthDocNo, 
                            FTXthRefDocNo, 
                            FTXthRefType, 
                            FTXthRefKey, 
                            FDXthRefDocDate
                        FROM TCNTDocHDRefTmp WITH (NOLOCK)
                        WHERE FTXthDocNo  = '$tPXDocNo'
                          AND FTXthDocKey = '".$paTableAddUpdate['tTableHD']."' 
                          AND FTSessionID = '$tPXSessionID' 
                    ";
        $this->db->query($tSQL);

    }

    
    public function FSaMPXDelHDDocRef($paData){
        $tPXDocNo       = $paData['FTXthDocNo'];
        $tPXRefDocNo    = $paData['FTXthRefDocNo'];
        $tPXDocKey      = $paData['FTXthDocKey'];
        $tPXSessionID   = $paData['FTSessionID'];
        
        $this->db->where('FTSessionID',$tPXSessionID);
        $this->db->where('FTXthDocKey',$tPXDocKey);
        $this->db->where('FTXthRefDocNo',$tPXRefDocNo);
        $this->db->where('FTXthDocNo',$tPXDocNo);
        $this->db->delete('TCNTDocHDRefTmp');

        if ( $this->db->trans_status() === FALSE ) {
            $this->db->trans_rollback();
            $aResult = array(
                'nStaEvent' => '800',
                'tStaMessg' => 'Delete HD Doc Ref Error'
            );
        } else {
            $this->db->trans_commit();
            $aResult = array(
                'nStaEvent' => '1',
                'tStaMessg' => 'Delete HD Doc Ref Success'
            );
        }
        return $aResult;
    }
    
    
}