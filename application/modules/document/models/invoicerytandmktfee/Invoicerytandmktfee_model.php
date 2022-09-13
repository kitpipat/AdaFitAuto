<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoicerytandmktfee_model extends CI_Model{

    // Datatable
    public function FSaMTRMList($paData){
        $aRowLen    = FCNaHCallLenData($paData['nRow'], $paData['nPage']);
        $nLngID     = $paData['FNLngID'];
        $tSQL       = "
            SELECT c.* FROM(
                SELECT  ROW_NUMBER() OVER(ORDER BY FDCreateOn DESC, FTXphDocNo DESC) AS FNRowID,* FROM( 
        ";
        $tSQLMain   = "
            SELECT DISTINCT
                HD.FTAgnCode,
                AGN.FTAgnName,
                BCHL.FTBchName,
                HD.FTBchCode,
                HD.FTXphDocNo,
                CONVERT(CHAR(10),HD.FDXphDocDate,103) AS FDXphDocDate,
                CONVERT(CHAR(10),HD.FDXphDueDate,103) AS FDXphDueDate,
                HD.FTAgnCodeTo,
				AGNLTO.FTAgnName	AS FTAgnNameTo,
				HD.FTBchCodeTo,
				BCHLTO.FTBchName	AS FTBchNameTo,
                HD.FTXphMonthRM,
				HD.FTXphYearRM,
                HD.FTXphStaDoc,
                HD.FTXphStaApv,
                HD.FTXphRmk,
                USRL.FTUsrName	AS FTCreateByName,
                HD.FDCreateOn
            FROM TACTRMHD HD WITH(NOLOCK)
            LEFT JOIN TACTRMHDCst   HDSPL	WITH (NOLOCK) ON HD.FTBchCode   = HDSPL.FTBchCode   AND HDSPL.FTXphDocNo    = HD.FTXphDocNo
            LEFT JOIN TCNMBranch_L	BCHL	WITH (NOLOCK) ON HD.FTBchCode   = BCHL.FTBchCode    AND BCHL.FNLngID        = ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMUser_L    USRL	WITH (NOLOCK) ON HD.FTCreateBy  = USRL.FTUsrCode    AND USRL.FNLngID        = ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMAgency_L	AGN		WITH (NOLOCK) ON HD.FTAgnCode	= AGN.FTAgnCode		AND AGN.FNLngID		    = ".$this->db->escape($nLngID)."
			LEFT JOIN TCNMAgency_L	AGNLTO	WITH (NOLOCK) ON HD.FTAgnCodeTo = AGNLTO.FTAgnCode	AND AGNLTO.FNLngID	    = ".$this->db->escape($nLngID)."
			LEFT JOIN TCNMBranch_L	BCHLTO	WITH (NOLOCK) ON HD.FTBchCodeTo = BCHLTO.FTBchCode	AND BCHLTO.FNLngID	    = ".$this->db->escape($nLngID)."
            WHERE HD.FDCreateOn <> ''
        ";
        $aAdvanceSearch = $paData['aAdvanceSearch'];
        @$tSearchList   = $aAdvanceSearch['tSearchAll'];
        if (@$tSearchList != '') {
            $tSQLMain   .= " 
                AND (
                    (HD.FTXphDocNo LIKE '%".$this->db->escape_like_str($tSearchList)."%') 
                    OR (BCHL.FTBchName LIKE '%".$this->db->escape_like_str($tSearchList)."%') 
                    OR (CONVERT(CHAR(10),HD.FDXphDocDate,103) LIKE '%".$this->db->escape_like_str($tSearchList)."%')
                    OR (AGNLTO.FTAgnName LIKE '%".$this->db->escape_like_str($tSearchList)."%')
                    OR (BCHLTO.FTBchName LIKE '%".$this->db->escape_like_str($tSearchList)."%')
                )
            ";
        }

        // Check Branch Level Branch And Shop
        if ($this->session->userdata("tSesUsrLevel") == 'BCH' || $this->session->userdata("tSesUsrLevel") == 'SHP') {
            $tBCH        = $this->session->userdata("tSesUsrBchCodeMulti");
            $tSQLMain   .= " AND  HD.FTBchCode IN ($tBCH) ";
        }
        // จากสาขา - ถึงสาขา
        $tSearchBchCodeFrom = $aAdvanceSearch['tSearchBchCodeFrom'];
        $tSearchBchCodeTo   = $aAdvanceSearch['tSearchBchCodeTo'];
        if (!empty($tSearchBchCodeFrom) && !empty($tSearchBchCodeFrom)) {
            $tSQLMain   .= "
                AND (
                    (HD.FTBchCode BETWEEN ".$this->db->escape($tSearchBchCodeFrom)." AND ".$this->db->escape($tSearchBchCodeTo).") 
                    OR 
                    (HD.FTBchCode BETWEEN ".$this->db->escape($tSearchBchCodeTo)." AND ".$this->db->escape($tSearchBchCodeFrom).")
                    OR
                    (HD.FTBchCodeTo BETWEEN ".$this->db->escape($tSearchBchCodeTo)." AND ".$this->db->escape($tSearchBchCodeFrom).")
                )
            ";
        }
        // จากลูกค้า - ถึงลูกค้า
        $tSearchSPLCodeFrom = $aAdvanceSearch['tSearchSPLCodeFrom'];
        $tSearchSPLCodeTo   = $aAdvanceSearch['tSearchSPLCodeTo'];
        if (!empty($tSearchSPLCodeFrom) && !empty($tSearchSPLCodeTo)) {
            $tSQLMain   .= "
                AND (
                    (HD.FTAgnCodeTo BETWEEN ".$this->db->escape($tSearchSPLCodeFrom)." AND ".$this->db->escape($tSearchSPLCodeTo).") 
                    OR 
                    (HD.FTAgnCodeTo BETWEEN ".$this->db->escape($tSearchSPLCodeFrom)." AND ".$this->db->escape($tSearchSPLCodeTo).")
                )
            ";
        }
        // จากวันที่ - ถึงวันที่
        $tSearchDocDateFrom = $aAdvanceSearch['tSearchDocDateFrom'];
        $tSearchDocDateTo   = $aAdvanceSearch['tSearchDocDateTo'];
        if (!empty($tSearchDocDateFrom) && !empty($tSearchDocDateTo)) {
            $tSQLMain   .= " 
                AND (
                    (HD.FDXphDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateFrom 00:00:00') AND CONVERT(datetime,'$tSearchDocDateTo 23:59:59'))
                    OR 
                    (HD.FDXphDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateTo 23:00:00') AND CONVERT(datetime,'$tSearchDocDateFrom 00:00:00'))
                )
            ";
        }
        // สถานะเอกสาร
        $tSearchStaDoc  = $aAdvanceSearch['tSearchStaDoc'];
        if(!empty($tSearchStaDoc) && ($tSearchStaDoc != "0")) {
            if($tSearchStaDoc == '1'){
                $tSQLMain   .= " AND HD.FTXphStaApv = ".$this->db->escape($tSearchStaDoc)." ";
            }elseif($tSearchStaDoc == '2'){
                $tSQLMain   .= " AND HD.FTXphStaDoc = '1' AND ISNULL(HD.FTXphStaApv,'') = '' ";
            }else{
                $tSQLMain   .= " AND HD.FTXphStaDoc = ".$this->db->escape($tSearchStaDoc)." ";
            }
        }
        $tSQL   .= $tSQLMain;
        $tSQL   .= ") Base) AS c WHERE c.FNRowID > ".$this->db->escape($aRowLen[0])." AND c.FNRowID <= ".$this->db->escape($aRowLen[1])."";


        // echo "<pre>";
        // print_r($tSQL);
        // echo "</pre>";

        $oQuery  = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $oList              = $oQuery->result_array();
            $oQueryMain         = $this->db->query($tSQLMain);
            $aDataCountAllRow   = $oQueryMain->num_rows();
            $nFoundRow          = $aDataCountAllRow;
            $nPageAll           = ceil($nFoundRow / $paData['nRow']);
            $aResult            = array(
                'raItems'       => $oList,
                'rnAllRow'      => $nFoundRow,
                'rnCurrentPage' => $paData['nPage'],
                'rnAllPage'     => $nPageAll,
                'rtCode'        => '1',
                'rtDesc'        => 'success'
            );
        } else {
            $aResult    = array(
                'rnAllRow'      => 0,
                'rnCurrentPage' => $paData['nPage'],
                "rnAllPage"     => 0,
                'rtCode'        => '800',
                'rtDesc'        => 'data not found'
            );
        }
        unset($aRowLen,$nLngID,$tSQL,$tSQLMain,$aAdvanceSearch,$tSearchList);
        unset($tSearchBchCodeFrom,$tSearchBchCodeTo,$tSearchSPLCodeFrom,$tSearchSPLCodeTo,$tSearchDocDateFrom,$tSearchDocDateTo,$tSearchStaDoc);
        unset($oQuery,$oList,$oQueryMain,$aDataCountAllRow,$nFoundRow,$nPageAll);
        return $aResult;
    }

    // ล้างข้อมูลใน temp
    public function FSaMTRMDeletePDTInTmp($tDocno = ''){
        $tSessionID = $this->session->userdata('tSesSessionID');
        $this->db->where_in('FTSessionID', $tSessionID);
        $this->db->where_in('FTXphDocKey', 'TACTRMDT');
        $this->db->where_in('FTXphDocNo', $tDocno);
        $this->db->delete('TACTRMDTTmp');
    }

    // เช็ค Agency Branch
    public function FSaMTRMChkAgnBch($ptAgnCode){
        $nLngID = $this->session->userdata("tLangEdit");
        $tSQL   = "
            SELECT
                AGN.FTBchcode,
                BCHL.FTBchName
            FROM TCNMAgency AGN WITH(NOLOCK)
            LEFT JOIN TCNMBranch_L BCHL WITH(NOlOCK) ON AGN.FTBchcode = BCHL.FTBchcode AND BCHL.FNLngID = ".$this->db->escape($nLngID)."
            WHERE AGN.FTAgnCode = ".$this->db->escape($ptAgnCode)."
        ";
        $oQuery = $this->db->query($tSQL);
        $aFind  = $oQuery->result_array();
        if ($oQuery->num_rows() > 0) {
            $aResult    = array(
                'rtCode'        => '1',
                'rtResult'      => $oQuery->result_array(),
                'row'           => count($aFind),
                'rtDesc'        => 'Find',
            );
        }else{
            $aResult    = array(
                'rtCode'        => '800',
                'row'           => 0,
                'rtDesc'        => 'data not found.',
            );
        }
        return $aResult;
    }

    // Get Branch Address
    public function FSaMTRMGetAgnAddress($tAgnBchCode,$nLngID){
        $nAddressVersion    = FCNaHAddressFormat('TCNMBranch');
        if(isset($tAgnBchCode) && !empty($tAgnBchCode)){
            $tSQL   = "
                SELECT TOP 1
                    AGN.FTAgnCode,
                    AGNL.FTAgnName,
                    BCHL.FTBchCode,
                    BCHL.FTBchName,
                    ADDL.FTAddRefNo,
                    ADDL.FTAddVersion,
                    ISNULL(ADDL.FTAddV1No,'')       AS FTAddV1No,
                    ISNULL(ADDL.FTAddV1Soi,'')      AS  FTAddV1Soi,
                    ISNULL(ADDL.FTAddV1Road,'')     AS FTAddV1Road,
                    ISNULL(ADDL.FTAddV1Village,'')  AS FTAddV1Village,
                    ISNULL(SDT.FTSudName,'')        AS FTSudName,
                    ISNULL(DTS.FTDstName,'')        AS FTDstName,
                    ISNULL(PVN.FTPvnName,'')        AS FTPvnName,
                    ISNULL(ADDL.FTAddV1PostCode,'') AS FTAddV1PostCode,
                    ISNULL(ADDL.FTAddV2Desc1,'')    AS FTAddV2Desc1,
                    ISNULL(ADDL.FTAddV2Desc2,'')    AS FTAddV2Desc2,
                    AGN.FTAgnEmail,
                    ADDL.FTAddTel
                FROM TCNMAddress_L              ADDL 	WITH(NOLOCK)
                LEFT JOIN TCNMAgency            AGN		WITH(NOLOCK) ON ADDL.FTAddRefCode 	= AGN.FTBchcode
                LEFT JOIN TCNMAgency_L			AGNL	WITH(NOLOCK) ON AGN.FTAgnCode       = AGNL.FTAgnCode 	AND AGNL.FNLngID    = ".$this->db->escape($nLngID)."
                LEFT JOIN TCNMBranch_L 			BCHL 	WITH(NOLOCK) ON ADDL.FTAddRefCode 	= BCHL.FTBchCode 	AND BCHL.FNLngID    = ".$this->db->escape($nLngID)."
                LEFT JOIN TCNMSubDistrict_L     SDT     WITH(NOLOCK) ON ADDL.FTAddV1SubDist = SDT.FTSudCode     AND SDT.FNLngID		= ".$this->db->escape($nLngID)."
                LEFT JOIN TCNMDistrict_L 		DTS 	WITH(NOLOCK) ON ADDL.FTAddV1SubDist = DTS.FTDstCode 	AND DTS.FNLngID		= ".$this->db->escape($nLngID)."
                LEFT JOIN TCNMProvince_L 		PVN 	WITH(NOLOCK) ON ADDL.FTAddV1PvnCode = PVN.FTPvnCode 	AND PVN.FNLngID		= ".$this->db->escape($nLngID)."
                WHERE ADDL.FTAddRefCode = ".$this->db->escape($tAgnBchCode)."
                ORDER BY ADDL.FNAddSeqNo DESC
            ";
            $oQuery = $this->db->query($tSQL);
            if (empty($oQuery->result_array())) {
                $aDataReturn    = array(
                    'rtCode'    => '999',
                    'rtDesc'    => 'Not Found',
                );
            } else {
                $aDataReturn    = array(
                    'rtCode'    => '1',
                    'rtDesc'    => 'Found',
                    'aQuery'    => $oQuery->row_array()
                );
            }
        }else{
            $aDataReturn    = array(
                'rtCode'    => '999',
                'rtDesc'    => 'Not Found',
            );
        }
        return $aDataReturn;
    }

    // --------------------------------------- เข้าหน้าแก้ไข -------------------------------------------- //

    // ข้อมูล HD
    public function FSaMTRMGetDataDocHD($paDataWhere){
        $tAgnCode   = $paDataWhere['tAgnCode'];
        $tBCHCode   = $paDataWhere['tBCHCode'];
        $tTRMDocNo  = $paDataWhere['tTRMDocNo'];
        $nLngID     = $this->session->userdata("tLangEdit");
        $tSQL       = "
            SELECT
                HD.FTAgnCode,AGNL.FTAgnName,
                HD.FTBchCode,BCHL.FTBchName,
                HD.FTXphDocNo,
                HD.FTXphDocType,
                HD.FTAgnCodeTo,AGNLTO.FTAgnName AS FTAgnNameTo,
                HD.FTBchCodeTo,BCHLTO.FTBchName AS FTBchNameTo,
                HD.FDXphDocDate,
                HD.FTCstCode,
                CSTL.FTCstName,
                HD.FTCshSoldTo,
                HD.FTCshShipTo,
                HD.FTCshPaymentTerm,
                HD.FTXphCond,
                HD.FTBbkCode,BBKL.FTBbkName,
                HD.FTXphVATInOrEx,
                HD.FCXphTotal,HD.FCXphDis,HD.FCXphChg,HD.FCXphVat,HD.FCXphVatable,HD.FCXphGrand,
                HD.FTXphMonthRM,
                HD.FTXphYearRM,
                HD.FCXphTotalRM,HD.FCXphVatRateRM,HD.FCXphVatRM,HD.FCXphVatableRM,HD.FCXphGrandRM,
                HD.FDXphDueDate,
                HD.FTXphRmk,
                HD.FTUsrCode,USRL.FTUsrName,
                HD.FTDptCode,
                HD.FTXphStaApv,
                HD.FTXphApvCode,UAPV.FTUsrName	AS FTXphApvName,
                HD.FTXphStaDoc,
                HD.FTXphStaPaid,
                HD.FNXphStaDocAct,
                HD.FNXphStaRef,
                HD.FNXphDocPrint,
                HD.FDLastUpdOn,
                HD.FTLastUpdBy,
                USRLAST.FTUsrName   AS FTLastUpdByName,
                HD.FDCreateOn,
                HD.FTCreateBy,
                USRCRET.FTUsrName   AS FTCreateByName
            FROM TACTRMHD HD WITH(NOLOCK)
            LEFT JOIN TCNMAgency_L		AGNL	WITH(NOLOCK) ON HD.FTAgnCode	= AGNL.FTAgnCode	    AND AGNL.FNLngID	= ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMBranch_L		BCHL	WITH(NOLOCK) ON HD.FTBchCode	= BCHL.FTBchCode	    AND BCHL.FNLngID	= ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMAgency_L		AGNLTO	WITH(NOLOCK) ON HD.FTAgnCodeTo	= AGNLTO.FTAgnCode	    AND AGNLTO.FNLngID	= ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMBranch_L		BCHLTO	WITH(NOLOCK) ON HD.FTBchCodeTo	= BCHLTO.FTBchCode	    AND	BCHLTO.FNLngID	= ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMCst_L			CSTL	WITH(NOLOCK) ON HD.FTCstCode	= CSTL.FTCstCode	    AND CSTL.FNLngID	= ".$this->db->escape($nLngID)."
            LEFT JOIN TFNMBookBank_L	BBKL	WITH(NOLOCK) ON HD.FTBchCode	= BBKL.FTBchCode	    AND HD.FTBbkCode	= BBKL.FTBbkCode	AND BBKL.FNLngID = ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMUser_L		USRL	WITH(NOLOCK) ON HD.FTUsrCode	= USRL.FTUsrCode	    AND USRL.FNLngID	= ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMUser_L		UAPV	WITH(NOLOCK) ON HD.FTXphApvCode	= UAPV.FTUsrCode	    AND UAPV.FNLngID	= ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMUser_L        USRLAST WITH(NOLOCK) ON HD.FTLastUpdBy  = USRLAST.FTUsrCode     AND USRLAST.FNLngID = ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMUser_L        USRCRET WITH(NOLOCK) ON HD.FTCreateBy   = USRCRET.FTUsrCode     AND USRCRET.FNLngID = ".$this->db->escape($nLngID)."
            WHERE HD.FDCreateOn <> ''
            AND HD.FTAgnCode    = ".$this->db->escape($tAgnCode)."
            AND HD.FTBchCode    = ".$this->db->escape($tBCHCode)."
            AND HD.FTXphDocNo	= ".$this->db->escape($tTRMDocNo)."
        ";
        $oQuery = $this->db->query($tSQL);
        if($oQuery->num_rows() > 0) {
            $aDataList      = $oQuery->row_array();
            $aDataReturn    = array(
                'raItems'   => $aDataList,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        }else{
            $aDataReturn    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found',
            );
        }
        unset($tAgnCode,$tBCHCode,$tTRMDocNo,$nLngID,$tSQL,$oQuery,$aDataList);
        return $aDataReturn;
    }

    // ข้อมูล CST HD
    public function FSaMTRMGetDataDocCSTHD($paDataWhere){
        $tAgnCode   = $paDataWhere['tAgnCode'];
        $tBCHCode   = $paDataWhere['tBCHCode'];
        $tTRMDocNo  = $paDataWhere['tTRMDocNo'];
        $nLngID     = $this->session->userdata("tLangEdit");
        $tSQL       = "
            SELECT 
                HDCST.FTAgnCode,
                HDCST.FTBchCode,
                HDCST.FTXphDocNo,
                HDCST.FTXphDstPaid,
                HDCST.FTXphCshOrCrd
            FROM TACTRMHDCst HDCST
            WHERE HDCST.FTAgnCode   = ".$this->db->escape($tAgnCode)."
            AND HDCST.FTBchCode		= ".$this->db->escape($tBCHCode)."
            AND HDCST.FTXphDocNo	= ".$this->db->escape($tTRMDocNo)."
        ";
        $oQuery = $this->db->query($tSQL);
        if($oQuery->num_rows() > 0) {
            $aDataList      = $oQuery->row_array();
            $aDataReturn    = array(
                'raItems'   => $aDataList,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        }else{
            $aDataReturn    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found',
            );
        }
        unset($tAgnCode,$tBCHCode,$tTRMDocNo,$nLngID,$tSQL,$oQuery,$aDataList);
        return $aDataReturn;
    }

    // --------------------------------------- ย้ายข้อมูลจากจริงไป Temp ---------------------------------- //

    // ย้ายจาก DT To Temp
    public function FSxMTRMMoveDTToDTTemp($paDataWhere){
        $tAgnCode       = $paDataWhere['tAgnCode'];
        $tBCHCode       = $paDataWhere['tBCHCode'];
        $tDocNo         = $paDataWhere['tTRMDocNo'];
        $tSesSessionID  = $this->session->userdata('tSesSessionID');
        $tDocKey        = 'TACTRMDT';
        // Delect Document DTTemp By Doc No
        $this->db->where('FTXphDocNo', $tDocNo);
        $this->db->delete('TACTRMDTTmp');
        // TACTRMDT Step 1
        $tSQL   = "
            INSERT INTO TACTRMDTTmp (
                FTAgnCode,FTBchCode,FTXphDocNo,FNXpdSeqNo,FTXphDocKey,FNXpdDesc,FCXpdTotalNV,FNXpdPercentRate,FCXpdTotal,FCVatRate,FCXpdVat,FCXpdVatable,
                FCXpdGrand,FCXpdInvLeft,FCXpdInvPaid,FTXpdRmk,FDLastUpdOn,FTLastUpdBy,FDCreateOn,FTCreateBy,FTSessionID
            )
            SELECT
                DT.FTAgnCode,
                DT.FTBchCode,
                DT.FTXphDocNo,
                DT.FNXpdSeqNo,
                ".$this->db->escape($tDocKey)." AS FTXphDocKey,
                DT.FNXpdDesc,
                DT.FCXpdTotalNV,
                DT.FNXpdPercentRate,
                DT.FCXpdTotal,
                DT.FCVatRate,
                DT.FCXpdVat,
                DT.FCXpdVatable,
                DT.FCXpdGrand,
                DT.FCXpdInvLeft,
                DT.FCXpdInvPaid,
                DT.FTXpdRmk,
                DT.FDLastUpdOn,
                DT.FTLastUpdBy,
                DT.FDCreateOn,
                DT.FTCreateBy,
                ".$this->db->escape($tSesSessionID)." AS FTSessionID
            FROM TACTRMDT DT WITH(NOLOCK) 
            WHERE DT.FDCreateOn	<> ''
            AND DT.FTAgnCode    = ".$this->db->escape($tAgnCode)."
            AND DT.FTBchCode    = ".$this->db->escape($tBCHCode)."
            AND DT.FTXphDocNo   = ".$this->db->escape($tDocNo)."
        ";
        $this->db->query($tSQL);
        return;
    }

    // ดึงข้อมูลยอดขายรวมในแต่ละเดือน
    public function FSaMTRMGetDataSumSalHDByID($paDataWhere){
        $tAgnCode   = $paDataWhere['tAgnCode'];
        $tBCHCode   = $paDataWhere['tBCHCode'];
        $tDocNo     = $paDataWhere['tTRMDocNo'];
        $tSQL       = "
            SELECT
                HD.FCXphTotal			AS FCTRMTotal,
                HD.FCXphDis				AS FCTRMDisChg,
                HD.FCXphTotalAfDisChg	AS FCTRMAFDisChg,
                HD.FCXphVat				AS FCTRMAmtV,
                HD.FCXphVatable         AS FCTRMAmtVTbl,
                HD.FCXphGrand			AS FCTRMGrand
            FROM TACTRMHD HD WITH(NOLOCK)
            WHERE HD.FDCreateOn <> ''
            AND HD.FTAgnCode	= ".$this->db->escape($tAgnCode)."
            AND HD.FTBchCode	= ".$this->db->escape($tBCHCode)."
            AND HD.FTXphDocNo	= ".$this->db->escape($tDocNo)."
        ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aResult    = array(
                'rtCode'    => '1',
                'rtResult'  => $oQuery->row_array(),
                'rtDesc'    => 'Success Query Data',
            );
        }else{
            $aResult    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found.',
            );
        }
        unset($tAgnCode,$tBCHCode,$tDocNo,$tSQL,$oQuery);
        return $aResult;
    }

    // ดึงข้อมูลตารางรายละเอียด ภาษี
    public function FSaMTRMGetDataSumVatSalHDByID($paDataWhere){
        $tAgnCode   = $paDataWhere['tAgnCode'];
        $tBCHCode   = $paDataWhere['tBCHCode'];
        $tDocNo     = $paDataWhere['tTRMDocNo'];
        $tSQL       = "
            SELECT
                HD.FCXphVatRate AS FCXsdVatRate,
                HD.FCXphGrand   AS FCXshGrand
            FROM TACTRMHD HD WITH(NOLOCK)
            WHERE HD.FDCreateOn <> ''
            AND HD.FTAgnCode	= ".$this->db->escape($tAgnCode)."
            AND HD.FTBchCode	= ".$this->db->escape($tBCHCode)."
            AND HD.FTXphDocNo	= ".$this->db->escape($tDocNo)."
        ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aResult    = array(
                'rtCode'    => '1',
                'rtResult'  => $oQuery->result_array(),
                'rtDesc'    => 'Success Query Data',
            );
        } else {
            $aResult    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found.',
            );
        }
        unset($tAgnCode,$tBCHCode,$tDocNo,$tSQL,$oQuery);
        return $aResult;
    }

    // ดึงข้อมูล Seting 
    public function FSaMTRMGetDataConfigRytMktFeeByID($paDataWhere){
        $tAgnCode   = $paDataWhere['tAgnCode'];
        $tBCHCode   = $paDataWhere['tBCHCode'];
        $tDocNo     = $paDataWhere['tTRMDocNo'];
        $tSQL       = "
            SELECT
                HD.FTCshSoldTo          AS FTCshSoldTo,
                HD.FTCshShipTo          AS FTCshShipTo,
                HD.FTCshPaymentTerm     AS FTCshPaymentTerm
            FROM TACTRMHD HD WITH(NOLOCK)
            WHERE HD.FDCreateOn <> ''
            AND HD.FTAgnCode    = ".$this->db->escape($tAgnCode)."
            AND HD.FTBchCode	= ".$this->db->escape($tBCHCode)."
            AND HD.FTXphDocNo	= ".$this->db->escape($tDocNo)."
        ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aResult    = array(
                'rtCode'    => '1',
                'rtResult'  => $oQuery->row_array(),
                'rtDesc'    => 'Success Query Data',
            );
        } else {
            $aResult    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found.',
            );
        }
        unset($tAgnCode,$tBCHCode,$tDocNo,$tSQL,$oQuery);
        return $aResult;
    }




    // Get Data Sale HD Sum All
    public function FSaMTRMGetDataSumSalHD($paDataWhere){
        $nLngID     = $this->session->userdata("tLangEdit");
        $tWhereSQL  = "";
        // Where Brance Search
        if(isset($paDataWhere['tBchCodeTo']) && !empty($paDataWhere['tBchCodeTo'])){
            $tWhereSQL  .= " AND DT.FTBchCode = ".$this->db->escape($paDataWhere['tBchCodeTo'])."";
        }
        // Where Date Start And Date Stop
        if(isset($paDataWhere['tFristDayOfMonth']) && !empty($paDataWhere['tLastDayOfMonth'])){
            $tWhereSQL  .= " AND CONVERT(VARCHAR(10),HD.FDXshDocDate,121) BETWEEN '".$paDataWhere['tFristDayOfMonth']."' AND '".$paDataWhere['tLastDayOfMonth']."' ";
        }

        $tSQL   = "
            SELECT 
                FTBchCode,
                FTBchName,
                MAX(FCXsdVatRate)              AS FCXsdVatRate,
                SUM(ROUND(FCXsdAmtB4DisChg,2))      AS FCTRMTotal,
                SUM(ROUND(FCXsdDis,2))              AS FCTRMDisChg,
                SUM(ROUND(FCXsdNetAfHD,2))          AS FCTRMAFDisChg,
                SUM(ROUND(FCXsdVat,2))              AS FCTRMAmtV,
                SUM(ROUND(FCXsdVatable,2))          AS FCTRMAmtVTbl,
                SUM(ROUND(FCXsdVat,2)) + SUM(ROUND(FCXsdVatable,2)) AS FCTRMGrand   
            FROM (
                SELECT HD.FTBchCode, Bch_L.FTBchName, HD.FTXshDocNo, DT.FCXsdVatRate
                , SUM((CASE WHEN HD.FNXshDocType = 1 THEN  1 ELSE -1 END) * ISNULL(DT.FCXsdAmtB4DisChg,0)) AS FCXsdAmtB4DisChg
                , SUM((CASE WHEN HD.FNXshDocType = 1 THEN  1 ELSE -1 END) * (CASE WHEN ISNULL(DTDis.FTXddDisChgType,'') IN ('3','4') THEN -1 ELSE 1 END) * ISNULL(DTDis.FCXddValue,0)) AS FCXsdDis
                , SUM((CASE WHEN HD.FNXshDocType = 1 THEN  1 ELSE -1 END) * ISNULL(DT.FCXsdNetAfHD,0)) AS FCXsdNetAfHD
                , HD.FCXshVat AS FCXsdVat, HD.FCXshVatable AS FCXsdVatable
                FROM TPSTSalHD HD WITH(NOLOCK)
                INNER JOIN TPSTSalDT DT WITH(NOLOCK) ON
                    HD.FTBchCode = DT.FTBchCode AND HD.FTXshDocNo = DT.FTXshDocNo 
                LEFT JOIN TPSTSalDTDis DTDis WITH(NOLOCK) ON
                    HD.FTBchCode = DTDis.FTBchCode AND HD.FTXshDocNo = DTDis.FTXshDocNo 
                    AND DT.FNXsdSeqNo = DTDis.FNXsdSeqNo
                LEFT JOIN TCNMBranch_L Bch_L WITH (NOLOCK) ON  HD.FTBchCode = Bch_L.FTBchCode AND Bch_L.FNLngID = 1
                WHERE HD.FDCreateOn <> ''
                    AND HD.FTXshStaDoc  = '1' 
                    AND DT.FTXsdStaPdt <> '4' 
                    ".$tWhereSQL."
                GROUP BY HD.FTBchCode, Bch_L.FTBchName, HD.FTXshDocNo, DT.FCXsdVatRate, HD.FCXshVat, HD.FCXshVatable
            ) SalePdt 
            GROUP BY FTBchCode,FTBchName
        
        ";

        // $tSQL   = "
        //     SELECT 
        //         FTBchCode,
        //         FTBchName,
        //         MAX(FCXsdVatRate)		            AS FCXsdVatRate,
        //         SUM(ROUND(FCXsdAmtB4DisChg,2))      AS FCTRMTotal,
        //         SUM(ROUND(FCXsdDis,2))              AS FCTRMDisChg,
        //         SUM(ROUND(FCXsdNetAfHD,2))          AS FCTRMAFDisChg,
        //         SUM(ROUND(FCXsdVat,2))              AS FCTRMAmtV,
        //         SUM(ROUND(FCXsdVatable,2))          AS FCTRMAmtVTbl,
        //         SUM(ROUND(FCXsdVat,2)) + SUM(ROUND(FCXsdVatable,2)) AS FCTRMGrand			
        //     FROM (
        //         SELECT	
        //             HD.FTBchCode,
        //             Bch_L.FTBchName,
        //             DT.FCXsdVatRate,
        //             CASE WHEN HD.FNXshDocType = 1 THEN  ISNULL(DT. FCXsdAmtB4DisChg,0)	ELSE (ISNULL(DT. FCXsdAmtB4DisChg,0))*-1	END AS FCXsdAmtB4DisChg,
        //             CASE WHEN HD.FNXshDocType = 1 THEN  ISNULL(DTDis.FCXddValue, 0)		ELSE (ISNULL(DTDis.FCXddValue, 0))*-1		END AS FCXsdDis,
        //             CASE WHEN HD.FNXshDocType = 1 THEN  ISNULL(DT.FCXsdNetAfHD,0)		ELSE ISNULL(DT.FCXsdNetAfHD,0)*-1			END AS FCXsdNetAfHD,
        //             CASE WHEN HD.FNXshDocType = 1 THEN  ISNULL(DT.FCXsdVat,0)			ELSE ISNULL(DT.FCXsdVat,0)*-1				END AS FCXsdVat,
        //             CASE WHEN HD.FNXshDocType = 1 THEN  ISNULL(DT.FCXsdVatable,0)		ELSE ISNULL(DT.FCXsdVatable,0)*-1			END AS FCXsdVatable
        //         FROM TPSTSalDT DT 
        //         INNER JOIN TPSTSalHD HD ON DT.FTBchCode = HD.FTBchCode AND DT.FTXshDocNo = HD.FTXshDocNo AND DT.FCXsdQty > 0
        //         LEFT JOIN ( 
        //             SELECT 
        //                 FTBchCode,FTXshDocNo,FNXsdSeqNo,
        //                 SUM (CASE WHEN FTXddDisChgType = 3 OR FTXddDisChgType = 4 THEN ISNULL(FCXddValue, 0) ELSE ISNULL(FCXddValue, 0)*-1 END) AS FCXddValue
        //             FROM TPSTSalDTDis 
        //             GROUP BY FTBchCode,FTXshDocNo,FNXsdSeqNo
        //         ) AS DTDis ON DT.FTBchCode = DTDis.FTBchCode AND DT.FTXshDocNo = DTDis.FTXshDocNo AND DT.FNXsdSeqNo = DTDis.FNXsdSeqNo 
        //         LEFT JOIN TCNMBranch_L Bch_L WITH (NOLOCK) ON  HD.FTBchCode = Bch_L.FTBchCode AND Bch_L.FNLngID     = ".$this->db->escape($nLngID)."
        //         WHERE HD.FDCreateOn <> ''
        //         AND HD.FTXshStaDoc  = '1' 
        //         AND DT.FTXsdStaPdt <> '4' 
        //         ".$tWhereSQL."
        //     ) SalePdt 
        //     GROUP BY FTBchCode,FTBchName
        // ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aResult    = array(
                'rtCode'    => '1',
                'rtResult'  => $oQuery->row_array(),
                'rtDesc'    => 'Success Query Data',
            );
        }else{
            $aResult    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found.',
            );
        }
        return $aResult;
    }

    // Get Data Sale HD Sum Vat All
    public function FSaMTRMGetDataSumVatSalHD($paDataWhere){
        $tWhereSQL  = "";
        // Where Brance Search
        if(isset($paDataWhere['tBchCodeTo']) && !empty($paDataWhere['tBchCodeTo'])){
            $tWhereSQL  .= " AND HD.FTBchCode = ".$this->db->escape($paDataWhere['tBchCodeTo'])."";
        }
        // Where Date Start And Date Stop
        if(isset($paDataWhere['tFristDayOfMonth']) && !empty($paDataWhere['tLastDayOfMonth'])){
            $tWhereSQL  .= " AND CONVERT(VARCHAR(10),HD.FDXshDocDate,121) BETWEEN '".$paDataWhere['tFristDayOfMonth']."' AND '".$paDataWhere['tLastDayOfMonth']."' ";
        }
        $tSQL       = "
            SELECT 
                FCXsdVatRate    AS FCXsdVatRate,
                SUM(ROUND(FCXsdVat,2))  AS FCXshGrand
            FROM (
                SELECT HD.FTBchCode, Bch_L.FTBchName, HD.FTXshDocNo, DT.FCXsdVatRate
                , SUM((CASE WHEN HD.FNXshDocType = 1 THEN  1 ELSE -1 END) * ISNULL(DT.FCXsdAmtB4DisChg,0)) AS FCXsdAmtB4DisChg
                , SUM((CASE WHEN HD.FNXshDocType = 1 THEN  1 ELSE -1 END) * (CASE WHEN ISNULL(DTDis.FTXddDisChgType,'') IN ('3','4') THEN -1 ELSE 1 END) * ISNULL(DTDis.FCXddValue,0)) AS FCXsdDis
                , SUM((CASE WHEN HD.FNXshDocType = 1 THEN  1 ELSE -1 END) * ISNULL(DT.FCXsdNetAfHD,0)) AS FCXsdNetAfHD
                , HD.FCXshVat AS FCXsdVat, HD.FCXshVatable AS FCXsdVatable
                FROM TPSTSalHD HD WITH(NOLOCK)
                INNER JOIN TPSTSalDT DT WITH(NOLOCK) ON
                    HD.FTBchCode = DT.FTBchCode AND HD.FTXshDocNo = DT.FTXshDocNo 
                LEFT JOIN TPSTSalDTDis DTDis WITH(NOLOCK) ON
                    HD.FTBchCode = DTDis.FTBchCode AND HD.FTXshDocNo = DTDis.FTXshDocNo 
                    AND DT.FNXsdSeqNo = DTDis.FNXsdSeqNo
                LEFT JOIN TCNMBranch_L Bch_L WITH (NOLOCK) ON  HD.FTBchCode = Bch_L.FTBchCode AND Bch_L.FNLngID = 1
                WHERE HD.FDCreateOn <> ''
                    AND HD.FTXshStaDoc  = '1' 
                    AND DT.FTXsdStaPdt <> '4' 
                    ".$tWhereSQL."
                GROUP BY HD.FTBchCode, Bch_L.FTBchName, HD.FTXshDocNo, DT.FCXsdVatRate, HD.FCXshVat, HD.FCXshVatable
            ) SalePdt 
            GROUP BY FCXsdVatRate
        ";
        // $tSQL   = "
        //     SELECT 
        //         FCXsdVatRate,
        //         SUM(ROUND(FCXsdVat,2))  AS FCXshGrand
        //     FROM (
        //         SELECT
        //             DT.FCXsdVatRate,
        //             CASE WHEN HD.FNXshDocType = 1 THEN  ISNULL(DT.FCXsdVat,0) ELSE ISNULL(DT.FCXsdVat,0)*-1 END AS FCXsdVat
        //         FROM TPSTSalDT DT 
        //         INNER JOIN TPSTSalHD HD ON DT.FTBchCode = HD.FTBchCode AND DT.FTXshDocNo = HD.FTXshDocNo AND DT.FCXsdQty > 0
        //         LEFT JOIN ( 
        //             SELECT 
        //                 FTBchCode,FTXshDocNo,FNXsdSeqNo,
        //                 SUM (CASE WHEN FTXddDisChgType = 3 OR FTXddDisChgType = 4 THEN ISNULL(FCXddValue, 0) ELSE ISNULL(FCXddValue, 0)*-1 END) AS FCXddValue
        //             FROM TPSTSalDTDis 
        //             GROUP BY FTBchCode,FTXshDocNo,FNXsdSeqNo
        //         ) AS DTDis ON DT.FTBchCode = DTDis.FTBchCode AND DT.FTXshDocNo = DTDis.FTXshDocNo AND DT.FNXsdSeqNo = DTDis.FNXsdSeqNo 
        //         LEFT JOIN TCNMBranch_L Bch_L WITH (NOLOCK) ON  HD.FTBchCode = Bch_L.FTBchCode AND Bch_L.FNLngID     = 1
        //         WHERE HD.FDCreateOn <> ''
        //         AND HD.FTXshStaDoc  = '1' 
        //         AND DT.FTXsdStaPdt <> '4' 
        //         ".$tWhereSQL."
        //     ) SalePdt 
        //     GROUP BY FCXsdVatRate
        // ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aResult    = array(
                'rtCode'        => '1',
                'rtResult'      => $oQuery->result_array(),
                'rtDesc'        => 'Success Query Data',
            );
        }else{
            $aResult    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found.',
            );
        }
        return $aResult;
    }

    // Get Data Config Marketing Fee And Royolty Fee
    public function FSaMTRMGetDataConfigRytMktFee($paDataWhere){
        $tBchCodeTo =   $paDataWhere['tBchCodeTo'];
        $tSQL       = "
            SELECT 
                CSTS.FTBchCode,
                BRNL.FTBchName,
                BRN.FTBchRefID,
                CSTS.FTCshWhTaxCode,
                CSTS.FTCshCostCenter,
                CSTS.FTCshShipTo,
                CSTS.FTCshSoldTo,
                CSTS.FCCshRoyaltyRate,
                CSTS.FCCshMarketingRate,
                CSTS.FTCshPaymentTerm
            FROM  TLKMCstShp CSTS WITH(NOLOCK)
            LEFT JOIN  TCNMBranch BRN ON  BRN.FTBchCode = CSTS.FTBchCode
            LEFT JOIN  TCNMBranch_L BRNL ON  BRN.FTBchCode = BRNL.FTBchCode AND BRNL.FNLngID = '1'
            WHERE CSTS.FTBchCode <> ''
            AND CSTS.FTBchCode  = ".$this->db->escape($tBchCodeTo)."
        ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aDataReturn    = $oQuery->row_array();
        }else{
            $aDataReturn    = "";
        }
        return $aDataReturn;
    }

    // Clear Data DT Temp
    public function FSaMTRMClearDTTmp(){
        $tSessionID = $this->session->userdata('tSesSessionID');
        $this->db->where('FTSessionID', $tSessionID);
        $this->db->delete('TACTRMDTTmp');
        return;
    }

    // Insert Data DT Temp
    public function FSaMTRMInsertRytMktFeeToTemp($paData){
        // ############################################# Insert Royolty Fee In To DT Tmp #############################################
        $tCalurateTotalRytFee   = floatval((floatval($paData['FCXpdTotalNV']) * floatval($paData['FCCshRoyaltyRate'])) / 100);
        $aDataInsertRytFee      = array(
            'FTAgnCode'         => $paData['FTAgnCode'],
            'FTBchCode'         => $paData['FTBchCode'],
            'FTXphDocNo'        => $paData['FTXphDocNo'],
            'FNXpdSeqNo'        => 1,
            'FTXphDocKey'       => $paData['FTXphDocKey'],
            'FNXpdDesc'         => $paData['FNXpdDesc'],
            'FCXpdTotalNV'      => $paData['FCXpdTotalNV'],
            'FNXpdPercentRate'  => $paData['FCCshRoyaltyRate'],
            'FCXpdTotal'        => $tCalurateTotalRytFee,
            'FCVatRate'         => $paData['FCVatRate'],
            'FDLastUpdOn'       => date('Y-m-d H:i:s'),
            'FDCreateOn'        => date('Y-m-d H:i:s'),
            'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
            'FTCreateBy'        => $this->session->userdata('tSesUsername'),
            'FTSessionID'       => $paData['FTSessionID']
        );
        $this->db->insert('TACTRMDTTmp',$aDataInsertRytFee);
        // ###########################################################################################################################
        // ############################################# Insert Marketing Fee In To DT Tmp ###########################################
        $tCalurateTotalMktFee   = floatval((floatval($paData['FCXpdTotalNV']) * floatval($paData['FCCshMarketingRate'])) / 100);
        $aDataInsertMktFee      = array(
            'FTAgnCode'         => $paData['FTAgnCode'],
            'FTBchCode'         => $paData['FTBchCode'],
            'FTXphDocNo'        => $paData['FTXphDocNo'],
            'FNXpdSeqNo'        => 2,
            'FTXphDocKey'       => $paData['FTXphDocKey'],
            'FNXpdDesc'         => $paData['FNXpdDesc'],
            'FCXpdTotalNV'      => $paData['FCXpdTotalNV'],
            'FNXpdPercentRate'  => $paData['FCCshMarketingRate'],
            'FCXpdTotal'        => $tCalurateTotalMktFee,
            'FCVatRate'         => $paData['FCVatRate'],
            'FDLastUpdOn'       => date('Y-m-d H:i:s'),
            'FDCreateOn'        => date('Y-m-d H:i:s'),
            'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
            'FTCreateBy'        => $this->session->userdata('tSesUsername'),
            'FTSessionID'       => $paData['FTSessionID']
        );
        $this->db->insert('TACTRMDTTmp',$aDataInsertMktFee);
        // ###########################################################################################################################
    }

    // Caculate DT Temp
    public function FSaMTRMCalcVatSalDTTmp($paData){
        $tVATInOrEx = $paData['FTXphVATInOrEx'];
        $tAgnCode   = $paData['FTAgnCode'];
        $tBchCode   = $paData['FTBchCode'];
        $tDocno     = $paData['FTXphDocNo'];
        $tSessionID = $paData['FTSessionID'];
        $tSQL       = "
            UPDATE MIAIN
            SET 
                MIAIN.FCXpdVat		= UPD.FCXpdVat,
                MIAIN.FCXpdVatable	= UPD.FCXpdVatable,
                MIAIN.FCXpdGrand	= UPD.FCXpdGrand,
                MIAIN.FDLastUpdOn	= GETDATE(),
                MIAIN.FTLastUpdBy	= '".$this->session->userdata('tSesUsername')."'
            FROM TACTRMDTTmp MIAIN WITH(NOLOCK)
            INNER JOIN (
                SELECT 
                    DATADT.*,
                    CASE WHEN DATADT.FTXphVATInOrEx = '1' THEN DATADT.FCXpdTotal - DATADT.FCXpdVat ELSE DATADT.FCXpdTotal END AS FCXpdVatable,
                    (DATADT.FCXpdVat + CASE WHEN DATADT.FTXphVATInOrEx = '1' THEN DATADT.FCXpdTotal - DATADT.FCXpdVat ELSE DATADT.FCXpdTotal END ) AS FCXpdGrand                    
                FROM(
                    SELECT 
                        DTTMP.FTAgnCode,
                        DTTMP.FTBchCode,
                        DTTMP.FTXphDocNo,
                        DTTMP.FNXpdSeqNo,
                        DTTMP.FTXphDocKey,
                        DTTMP.FTSessionID,
                        DTTMP.FCXpdTotal,
                        DTTMP.FCVatRate,
                        ".$this->db->escape($tVATInOrEx)." AS FTXphVATInOrEx,
                        CAST(((ISNULL(DTTMP.FCXpdTotal,0) * ISNULL(DTTMP.FCVatRate,0)) / 100) AS decimal(18,4)) AS FCXpdVat
                    FROM TACTRMDTTmp DTTMP WITH(NOLOCK)
                    WHERE DTTMP.FTSessionID <> ''
                    AND DTTMP.FTAgnCode     = ".$this->db->escape($tAgnCode)."
                    AND DTTMP.FTBchCode		= ".$this->db->escape($tBchCode)."
                    AND DTTMP.FTXphDocNo	= ".$this->db->escape($tDocno)."
                    AND DTTMP.FTSessionID	= ".$this->db->escape($tSessionID)."
                ) DATADT
            ) UPD ON MIAIN.FTAgnCode = UPD.FTAgnCode
            AND MIAIN.FTBchCode		= UPD.FTBchCode 
            AND MIAIN.FTXphDocNo	= UPD.FTXphDocNo
            AND MIAIN.FNXpdSeqNo	= UPD.FNXpdSeqNo
            AND MIAIN.FTXphDocKey	= UPD.FTXphDocKey
            AND MIAIN.FTSessionID	= UPD.FTSessionID
        ";
        $oQuery = $this->db->query($tSQL);
        if($oQuery == 1){
            return true;
        }else{
            return false;
        }
    }

    // Get Data DT Temp
    public function FSaMTRMGetDataDTTmp($paDataWhere){
        $tAgnCode   = $paDataWhere['tAgnCode'];
        $tBchCode   = $paDataWhere['tBchCode'];
        $tDocno     = $paDataWhere['tDocNo'];
        $tSessionID = $this->session->userdata('tSesSessionID');
        $tSQL       = "
            SELECT DTTMP.*
            FROM TACTRMDTTmp DTTMP WITH(NOLOCK)
            WHERE DTTMP.FTSessionID <> ''
            AND DTTMP.FTAgnCode     = ".$this->db->escape($tAgnCode)."
            AND DTTMP.FTBchCode		= ".$this->db->escape($tBchCode)."
            AND DTTMP.FTXphDocNo	= ".$this->db->escape($tDocno)."
            AND DTTMP.FTSessionID	= ".$this->db->escape($tSessionID)."
        ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aResult    = array(
                'rtCode'        => '1',
                'rtResult'      => $oQuery->result_array(),
                'rtDesc'        => 'Success Query Data',
            );
        }else{
            $aResult    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found.',
            );
        }
        return $aResult;
    }

    // Get Data DT Sum Footer
    public function FSaMTRMGetDataDTFootTmp($paDataWhere){
        $tAgnCode   = $paDataWhere['tAgnCode'];
        $tBchCode   = $paDataWhere['tBchCode'];
        $tDocno     = $paDataWhere['tDocNo'];
        $tSessionID = $this->session->userdata('tSesSessionID');
        $tSQL       = "
            SELECT
                DTTMP.FCVatRate                     AS FCVatRateRM,
                SUM(ROUND(DTTMP.FCXpdTotal,2))	    AS FCXphTotalRM,
                SUM(ROUND(DTTMP.FCXpdVat,2))        AS FCXphVatRM,
                SUM(ROUND(DTTMP.FCXpdVatable,2))    AS FCXphVatableRM,
                SUM(ROUND(DTTMP.FCXpdGrand,2))      AS FCXphGrandRM
            FROM TACTRMDTTmp DTTMP WITH(NOLOCK)
            WHERE DTTMP.FTSessionID <> ''
            AND DTTMP.FTAgnCode     = ".$this->db->escape($tAgnCode)."
            AND DTTMP.FTBchCode		= ".$this->db->escape($tBchCode)."
            AND DTTMP.FTXphDocNo	= ".$this->db->escape($tDocno)."
            AND DTTMP.FTSessionID	= ".$this->db->escape($tSessionID)."
            GROUP BY DTTMP.FCVatRate
        ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aResult    = $oQuery->row_array();
        }else{
            $aResult    = '';
        }
        return $aResult;
    }

    // Check Data In DT Tmp
    public function FSaMTRMChkDataInDTTmp($paDataWhere){
        $tTRMAgnCode    = $paDataWhere['tTRMAgnCode'];
        $tTRMBchCode    = $paDataWhere['tTRMBchCode'];
        $tTRMDocNo      = $paDataWhere['tTRMDocNo'];
        $tSessionID     = $this->session->userdata('tSesSessionID');
        $tSQL           = "
            SELECT COUNT(TMP.FTSessionID) AS FNCountChkDTTmp
            FROM TACTRMDTTmp TMP WITH(NOLOCK)
            WHERE TMP.FTSessionID <> ''
            AND TMP.FTAgnCode	= ".$this->db->escape($tTRMAgnCode)."
            AND TMP.FTBchCode	= ".$this->db->escape($tTRMBchCode)."
            AND TMP.FTXphDocNo	= ".$this->db->escape($tTRMDocNo)."
            AND TMP.FTSessionID = ".$this->db->escape($tSessionID)."
        ";
        $oQuery = $this->db->query($tSQL);
        if($oQuery->num_rows() > 0) {
            $aResult  = $oQuery->row_array();
        } else {
            $aResult  = '';
        }
        unset($tTRMAgnCode,$tTRMBchCode,$tTRMDocNo,$tSessionID,$oQuery);
        return $aResult;
    }



    // --------------------------------------- บันทึกข้อมูล -------------------------------------------- //

    // ข้อมูล HD ลบ และ เพิ่มใหม่
    public function FSxMTRMAddUpdateHD($paDataMaster,$paDataWhere,$paTableAddUpdate){
        $aDataGetDataHD = $this->FSaMTRMGetDataDocHD(array(
            'tAgnCode'  => $paDataWhere['FTAgnCode'],
            'tBCHCode'  => $paDataWhere['FTBchCode'],
            'tTRMDocNo' => $paDataWhere['FTXphDocNo'],
        ));
        $aDataAddUpdateHD   = array();
        if (isset($aDataGetDataHD['rtCode']) && $aDataGetDataHD['rtCode'] == 1) {
            $aDataHDOld         = $aDataGetDataHD['raItems'];
            $aDataAddUpdateHD   = array_merge($paDataMaster, array(
                'FTAgnCode'     => $paDataWhere['FTAgnCode'],
                'FTBchCode'     => $paDataWhere['FTBchCode'],
                'FTXphDocNo'    => $paDataWhere['FTXphDocNo'],
                'FDLastUpdOn'   => $paDataWhere['FDLastUpdOn'],
                'FTLastUpdBy'   => $paDataWhere['FTLastUpdBy'],
                'FDCreateOn'    => $aDataHDOld['FDCreateOn'],
                'FTCreateBy'    => $aDataHDOld['FTCreateBy'],
                'FTXphStaApv'   => $aDataHDOld['FTXphStaApv'],
                'FTXphApvCode'  => $aDataHDOld['FTXphApvCode'],
                'FTXphStaDoc'   => $aDataHDOld['FTXphStaDoc']
            ));
        }else{
            $aDataAddUpdateHD   = array_merge($paDataMaster, array(
                'FTAgnCode'     => $paDataWhere['FTAgnCode'],
                'FTBchCode'     => $paDataWhere['FTBchCode'],
                'FTXphDocNo'    => $paDataWhere['FTXphDocNo'],
                'FDLastUpdOn'   => $paDataWhere['FDLastUpdOn'],
                'FTLastUpdBy'   => $paDataWhere['FTLastUpdBy'],
                'FDCreateOn'    => $paDataWhere['FDCreateOn'],
                'FTCreateBy'    => $paDataWhere['FTCreateBy'],
            ));
        }
        // Delete Table HD
        $this->db->where_in('FTAgnCode',$aDataAddUpdateHD['FTAgnCode']);
        $this->db->where_in('FTBchCode',$aDataAddUpdateHD['FTBchCode']);
        $this->db->where_in('FTXphDocNo',$aDataAddUpdateHD['FTXphDocNo']);
        $this->db->delete($paTableAddUpdate['tTableHD']);
        // Insert HD 
        $this->db->insert($paTableAddUpdate['tTableHD'],$aDataAddUpdateHD);
        return;
    }

    // ข้อมูล CST ลบและ เพิ่มใหม่
    public function FSxMTRMAddUpdateSPLHD($paDataCSTHD, $paDataWhere, $paTableAddUpdate){
        $aDataGetDataSPLHD      = $this->FSaMTRMGetDataDocCSTHD(array(
            'tAgnCode'  => $paDataWhere['FTAgnCode'],
            'tBCHCode'  => $paDataWhere['FTBchCode'],
            'tTRMDocNo' => $paDataWhere['FTXphDocNo'],
        ));
        $aDataAddUpdateCSTHD    = array();
        if (isset($aDataGetDataSPLHD['rtCode']) && $aDataGetDataSPLHD['rtCode'] == 1) {
            $aDataAddUpdateCSTHD    = array_merge($paDataCSTHD, array(
                'FTAgnCode'     => $paDataWhere['FTAgnCode'],
                'FTBchCode'     => $paDataWhere['FTBchCode'],
                'FTXphDocNo'    => $paDataWhere['FTXphDocNo'],
            ));
        } else {
            $aDataAddUpdateCSTHD    = array_merge($paDataCSTHD, array(
                'FTAgnCode'     => $paDataWhere['FTAgnCode'],
                'FTBchCode'     => $paDataWhere['FTBchCode'],
                'FTXphDocNo'    => $paDataWhere['FTXphDocNo']
            ));
        }
        // Delete SPL
        $this->db->where_in('FTAgnCode', $aDataAddUpdateCSTHD['FTAgnCode']);
        $this->db->where_in('FTBchCode', $aDataAddUpdateCSTHD['FTBchCode']);
        $this->db->where_in('FTXphDocNo', $aDataAddUpdateCSTHD['FTXphDocNo']);
        $this->db->delete($paTableAddUpdate['tTableDTSpl']);
        // Insert SPL
        $this->db->insert($paTableAddUpdate['tTableDTSpl'], $aDataAddUpdateCSTHD);
        return;
    }

    // อัพเดทเลขที่เอกสาร  TACTRMDTTmp
    public function FSxMTRMAddUpdateDocNoToTemp($paDataWhere){
        // Update DocNo Into DTTemp
        $this->db->where('FTXphDocNo', '');
        $this->db->where('FTSessionID', $paDataWhere['FTSessionID']);
        $this->db->where('FTXphDocKey', 'TACTRMDT');
        $this->db->update('TACTRMDTTmp', array(
            'FTXphDocNo'    => $paDataWhere['FTXphDocNo']
        ));
        return;
    }

    // ข้อมูล Move DTTemp To DT
    public function FSaMTRMMoveDTTmpToDT($paDataWhere,$paTableAddUpdate){
        $tAgnCode   = $paDataWhere['FTAgnCode'];
        $tBchCode   = $paDataWhere['FTBchCode'];
        $tDocNo     = $paDataWhere['FTXphDocNo'];
        $tSessionID = $this->session->userdata('tSesSessionID');
        // Delete DT Old
        if(isset($tDocNo) && !empty($tDocNo)) {
            $this->db->where_in('FTXphDocNo', $tDocNo);
            $this->db->delete($paTableAddUpdate['tTableDT']);
        }

        // Move DTTmp To TACTRMDT 
        $tSQL   = "
            INSERT INTO ".$paTableAddUpdate['tTableDT']."(
                FTAgnCode,FTBchCode,FTXphDocNo,FNXpdSeqNo,FNXpdDesc,FCXpdTotalNV,FNXpdPercentRate,
                FCXpdTotal,FCVatRate,FCXpdVat,FCXpdVatable,FCXpdGrand,FCXpdInvLeft,FCXpdInvPaid,
                FTXpdRmk,FDLastUpdOn,FTLastUpdBy,FDCreateOn,FTCreateBy
            )
            SELECT
                DTTMP.FTAgnCode,
                DTTMP.FTBchCode,
                DTTMP.FTXphDocNo,
                DTTMP.FNXpdSeqNo,
                DTTMP.FNXpdDesc,
                DTTMP.FCXpdTotalNV,
                DTTMP.FNXpdPercentRate,
                DTTMP.FCXpdTotal,
                DTTMP.FCVatRate,
                DTTMP.FCXpdVat,
                DTTMP.FCXpdVatable,
                DTTMP.FCXpdGrand,
                DTTMP.FCXpdInvLeft,
                DTTMP.FCXpdInvPaid,
                DTTMP.FTXpdRmk,
                DTTMP.FDLastUpdOn,
                DTTMP.FTLastUpdBy,
                DTTMP.FDCreateOn,
                DTTMP.FTCreateBy
            FROM TACTRMDTTmp DTTMP WITH(NOLOCK)
            WHERE DTTMP.FTSessionID <> ''
            AND DTTMP.FTAgnCode		= ".$this->db->escape($tAgnCode)."
            AND DTTMP.FTBchCode		= ".$this->db->escape($tBchCode)."
            AND DTTMP.FTXphDocNo	= ".$this->db->escape($tDocNo)."
            AND DTTMP.FTSessionID   = ".$this->db->escape($tSessionID)."
            ORDER BY DTTMP.FNXpdSeqNo ASC
        ";
        $this->db->query($tSQL);
        return;
    }

    // อัพเดตข้อมูล RM ในตาราง HD
    public function FSaMTRMSumRMDTTmpToHD($paDataWhere,$paTableAddUpdate){
        $tSessionID = $paDataWhere['FTSessionID'];
        $tSQL       = "
            UPDATE HD
            SET 
                HD.FCXphVatRateRM	= DT.FCXphVatRateRM,
                HD.FCXphTotalRM		= DT.FCXphTotalRM,
                HD.FCXphVatRM		= DT.FCXphVatRM,
                HD.FCXphVatableRM	= DT.FCXphVatableRM,
                HD.FCXphGrandRM		= DT.FCXphGrandRM
            FROM ".$paTableAddUpdate['tTableHD']." HD WITH(NOLOCK)
            INNER JOIN (
                SELECT 
                    TMP.FTAgnCode,
                    TMP.FTBchCode,
                    TMP.FTXphDocNo,
                    TMP.FCVatRate			        AS FCXphVatRateRM,
                    SUM(ROUND(TMP.FCXpdTotal,2))    AS FCXphTotalRM,
                    SUM(ROUND(TMP.FCXpdVat,2))      AS FCXphVatRM,
                    SUM(ROUND(TMP.FCXpdVatable,2))  AS FCXphVatableRM,
                    SUM(ROUND(TMP.FCXpdGrand,2))    AS FCXphGrandRM
                FROM ".$paTableAddUpdate['tTableDTTmp']." TMP WITH(NOLOCK)
                WHERE TMP.FTSessionID <> ''
                AND TMP.FTSessionID = ".$this->db->escape($tSessionID)."
                GROUP BY TMP.FTAgnCode,TMP.FTBchCode,TMP.FTXphDocNo,TMP.FCVatRate
            ) DT ON HD.FTAgnCode    = DT.FTAgnCode 
            AND HD.FTBchCode	    = DT.FTBchCode
            AND HD.FTXphDocNo	    = DT.FTXphDocNo
        ";
        $this->db->query($tSQL);
        return;
    }

    // ------------------------------------------------------------------------------------------------- //
    

    // ลบข้อมูลเอกสาร
    public function FSnMTRMDelDocument($paDataDoc){
        $tDataDocNo = $paDataDoc['tDataDocNo'];
        $this->db->trans_begin();

        $this->db->where_in('FTXphDocNo', $tDataDocNo);
        $this->db->delete('TACTRMHD');

        $this->db->where_in('FTXphDocNo', $tDataDocNo);
        $this->db->delete('TACTRMHDCst');

        $this->db->where_in('FTXphDocNo', $tDataDocNo);
        $this->db->delete('TACTRMDT');

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $aStaDelDoc     = array(
                'rtCode'    => '905',
                'rtDesc'    => 'Cannot Delete Item.',
            );
        } else {
            $this->db->trans_commit();
            $aStaDelDoc     = array(
                'rtCode'    => '1',
                'rtDesc'    => 'Delete Complete.',
            );
        }
        return $aStaDelDoc;
    }

    // ยกเลิกข้อมูลเอกสาร
    public function FSnMTRMEventCancel($paDataDoc){
        $tDataDocNo = $paDataDoc['tDataDocNo'];
        $this->db->trans_begin();
        $this->db->set('FTXphStaDoc', '3');
        $this->db->set('FTXphStaApv', null);
        $this->db->set('FTXphApvCode', null);
        $this->db->where('FTXphDocNo', $tDataDocNo);
        $this->db->update('TACTRMHD');

        //ยกเลิกแล้วให้อ้างอิงใหม่ได้
        $this->db->set('FTXpdRmk', null);
        $this->db->where('FTXphDocNo', $tDataDocNo);
        $this->db->update('TACTRMDT');

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $aStaDelDoc     = array(
                'rtCode'    => '905',
                'rtDesc'    => 'Cannot Delete Item.',
            );
        } else {
            $this->db->trans_commit();
            $aStaDelDoc     = array(
                'rtCode'    => '1',
                'rtDesc'    => 'Cancel Complete.',
            );
        }
        return $aStaDelDoc;
    }

    // อนุมัติเอกสาร
    public function FSnMTRMEventAppove($paDataDoc){
        $dLastUpdOn = date('Y-m-d H:i:s');
        $tLastUpdBy = $this->session->userdata('tSesUsername');
        $tDataDocNo = $paDataDoc['tDataDocNo'];

        $this->db->trans_begin();

        // Change Status Appove [ TACTRMHD ]
        $this->db->set('FDLastUpdOn',$dLastUpdOn);
        $this->db->set('FTLastUpdBy',$tLastUpdBy);
        $this->db->set('FTXphStaApv',$paDataDoc['FTXphStaApv']);
        $this->db->set('FTXphApvCode',$paDataDoc['FTXphApvCode']);
        $this->db->where('FTXphDocNo',$paDataDoc['FTXphDocNo']);
        $this->db->update('TACTRMHD');

        // Update Remark Appove  [ TACTRMDT ]
        $this->db->set('FTXpdRmk','1');
        $this->db->where('FTXphDocNo',$paDataDoc['FTXphDocNo']);
        $this->db->update('TACTRMDT');

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $aStatus        = array(
                'rtCode'    => '903',
                'rtDesc'    => 'Not Update Status Document.',
            );
        } else {
            $this->db->trans_commit();
            $aStatus        = array(
                'rtCode'    => '1',
                'rtDesc'    => 'Updated Status Document Appove Success.',
            );
        }
        return $aStatus;
    }


    // เช็คข้อมูลว่า Duplicate ในตาราง HD
    public function FSaMTRMChkDocHaveInDB($paDataWhere){
        $tSQL   = "
            SELECT ISNULL(HD.FTXphStaApv,0)	AS FTXphStaApv,COUNT(HD.FTXphDocNo)		AS FNCountDoc
            FROM TACTRMHD HD WITH(NOLOCK)
            WHERE HD.FDCreateOn <> ''
            AND HD.FTXphStaDoc	<> '3'
            AND HD.FTAgnCodeTo  = ".$this->db->escape($paDataWhere['FTAgnCodeTo'])."
            AND HD.FTBchCodeTo  = ".$this->db->escape($paDataWhere['FTBchCodeTo'])."
            AND HD.FTXphMonthRM = ".$this->db->escape($paDataWhere['FTXphMonthRM'])."
            AND HD.FTXphYearRM  = ".$this->db->escape($paDataWhere['FTXphYearRM'])."
            GROUP BY HD.FTXphStaApv
        ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aResult    = array(
                'raItems'   => $oQuery->row_array(),
                'rtCode'    => '1',
                'rtDesc'    => 'success'
            );
        } else {
            $aResult    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found'
            );
        }
        unset($tSQL,$oQuery);
        return $aResult;
    }


}