<?php

defined('BASEPATH') or exit('No direct script access allowed');

class RptSaleFCCompVD_model extends CI_Model {

    // Call Stored Procedure
    public function FSnMExecStoreReport($paDataFilter) {
        $nLangID        = $paDataFilter['nLangID'];
        $tComName       = $paDataFilter['tCompName'];
        $tRptCode       = $paDataFilter['tRptCode'];
        $tUserSession   = $paDataFilter['tUserSession'];
        // สาขา
        $tBchCodeSelect = ($paDataFilter['bBchStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tBchCodeSelect']); 
        // ร้านค้า
        $tShpCodeSelect = ($paDataFilter['bShpStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tShpCodeSelect']);

        // Get Config Data Supllier Default
        $aConfigParamsOnline    = [
            "tSysCode"  => "tCN_FCSupplier",
            "tSysApp"   => "CN",
            "tSysKey"   => "TCNMSpl",
            "tSysSeq"   => "1",
            "tGmnCode"  => "MSPL"
        ];
        $aConfigSplFC   = FCNaGetSysConfig($aConfigParamsOnline);
        $tSplCodeSysDef = (!empty($aConfigSplFC['raItems']['FTSysStaUsrValue'])) ? $aConfigSplFC['raItems']['FTSysStaUsrValue'] : $aConfigSplFC['raItems']['FTSysStaDefValue'];

        $tCallStore = "{CALL SP_RPTxSaleFCCompVD001001065(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)}";
        $aDataStore = [
            'pnLngID'       => $nLangID,
            'pnComName'     => $tComName,
            'ptRptCode'     => $tRptCode,
            'ptUsrSession'  => $tUserSession,
            'pnFilterType'  => $paDataFilter['tTypeSelect'],
            'ptBchL'        => $tBchCodeSelect,
            'ptBchF'        => $paDataFilter['tBchCodeFrom'],
            'ptBchT'        => $paDataFilter['tBchCodeTo'],
            'ptPdtCodeF'    => $paDataFilter['tPdtCodeFrom'],
            'ptPdtCodeT'    => $paDataFilter['tPdtCodeTo'],
            'ptPdtChanF'    => $paDataFilter['tPdtGrpCodeFrom'],
            'ptPdtChanT'    => $paDataFilter['tPdtGrpCodeTo'],
            'ptPdtTypeF'    => $paDataFilter['tPdtTypeCodeFrom'],
            'ptPdtTypeT'    => $paDataFilter['tPdtTypeCodeTo'],
            'ptDocDateF'    => $paDataFilter['tDocDateFrom'],
            'ptDocDateT'    => $paDataFilter['tDocDateTo'],
            'ptCate1From'   => FCNtAddSingleQuote($paDataFilter['tCate1From']),
            'ptCate2From'   => FCNtAddSingleQuote($paDataFilter['tCate2From']),
            'ptSysSplCode'  => $tSplCodeSysDef,
            'FNResult'      => 0
        ];
        $oQuery = $this->db->query($tCallStore, $aDataStore);
        if ($oQuery !== FALSE) {
            unset($oQuery);
            return 1;
        } else {
            unset($oQuery);
            return 0;
        }
    }

    // Get Data Page
    public function FMaMRPTPagination($paDataWhere) {
        $tComName       = $paDataWhere['tCompName'];
        $tRptCode       = $paDataWhere['tRptCode'];
        $tUsrSession    = $paDataWhere['tUsrSessionID'];
        $tSQL           = "
            SELECT
                COUNT(DTTMP.FTRptCode) AS rnCountPage
            FROM TRPTSaleFCCompVDTmp AS DTTMP WITH(NOLOCK)
            WHERE DTTMP.FTUsrSession <> ''
            AND DTTMP.FTComName    = '$tComName'
            AND DTTMP.FTRptCode    = '$tRptCode'
            AND DTTMP.FTUsrSession = '$tUsrSession'
        ";
        $oQuery = $this->db->query($tSQL);
        $nRptAllRecord = $oQuery->row_array()['rnCountPage'];
        $nPage = $paDataWhere['nPage'];
        $nPerPage = $paDataWhere['nPerPage'];
        $nPrevPage = $nPage - 1;
        $nNextPage = $nPage + 1;
        $nRowIDStart = (($nPerPage * $nPage) - $nPerPage);
        if ($nRptAllRecord <= $nPerPage) {
            $nTotalPage = 1;
        } else if (($nRptAllRecord % $nPerPage) == 0) {
            $nTotalPage = ($nRptAllRecord / $nPerPage);
        } else {
            $nTotalPage = ($nRptAllRecord / $nPerPage) + 1;
            $nTotalPage = (int) $nTotalPage;
        }

        // get rowid end
        $nRowIDEnd  = $nPerPage * $nPage;
        if ($nRowIDEnd > $nRptAllRecord) {
            $nRowIDEnd = $nRptAllRecord;
        }

        $aRptMemberDet = array(
            "nTotalRecord"  => $nRptAllRecord,
            "nTotalPage"    => $nTotalPage,
            "nDisplayPage"  => $paDataWhere['nPage'],
            "nRowIDStart"   => $nRowIDStart,
            "nRowIDEnd"     => $nRowIDEnd,
            "nPrevPage"     => $nPrevPage,
            "nNextPage"     => $nNextPage
        );
        unset($tComName,$tRptCode,$tUsrSession,$tSQL,$oQuery,$nRptAllRecord,$nPage,$nPerPage,$nPrevPage,$nNextPage,$nRowIDStart);
        unset($nTotalPage,$nRowIDEnd);
        return $aRptMemberDet;
    }

    // Priority Group
    public function FMxMRPTSetPriorityGroup($paDataWhere) {
        $tComName       = $paDataWhere['tCompName'];
        $tRptCode       = $paDataWhere['tRptCode'];
        $tUsrSession    = $paDataWhere['tUsrSessionID'];
        $tSQL           = "
            UPDATE DATAUPD SET 
                DATAUPD.FNRowPartID = B.PartID
            FROM TRPTSaleFCCompVDTmp AS DATAUPD WITH(NOLOCK)
            INNER JOIN(
                SELECT
                    ROW_NUMBER() OVER(PARTITION BY FTBchCode ORDER BY FTBchCode DESC) AS PartID,
                    FTRptRowSeq
                FROM TRPTSaleFCCompVDTmp TMP WITH(NOLOCK)
                WHERE TMP.FTComName     = '$tComName'
                AND TMP.FTRptCode       = '$tRptCode'
                AND TMP.FTUsrSession    = '$tUsrSession'
        ";
        $tSQL .= "
            ) AS B
            ON DATAUPD.FTRptRowSeq = B.FTRptRowSeq
            AND DATAUPD.FTComName       = '$tComName'
            AND DATAUPD.FTRptCode       = '$tRptCode'
            AND DATAUPD.FTUsrSession    = '$tUsrSession'
        ";
        $this->db->query($tSQL);
    }

    // Get Data Report In Table Temp
    public function FSaMGetDataReport($paDataWhere) {
        $nPage  = $paDataWhere['nPage'];
        if( $paDataWhere['nPerPage'] != 0 ){
            $aPagination    = $this->FMaMRPTPagination($paDataWhere);
            $nRowIDStart    = $aPagination["nRowIDStart"];
            $nRowIDEnd      = $aPagination["nRowIDEnd"];
            $nTotalPage     = $aPagination["nTotalPage"];
        }else{
            $aPagination    = '';
            $nTotalPage     = 1;
        }
        $tComName       = $paDataWhere['tCompName'];
        $tRptCode       = $paDataWhere['tRptCode'];
        $tUsrSession    = $paDataWhere['tUsrSessionID'];

        // Set Priority
        $this->FMxMRPTSetPriorityGroup($paDataWhere);

        // Check ว่าเป็นหน้าสุดท้ายหรือไม่ ถ้าเป็นหน้าสุดท้ายให้ไป Sum footer ข้อมูลมา
        if ($nPage == $nTotalPage) {
            $tJoinFoooter = "
                SELECT
                    FTUsrSession		AS FTUsrSession_Footer,
                    SUM(FCXpdNetAfHDHQ)	AS FCXpdNetAfHDHQ_Footer,
                    (SUM(FCXpdNetAfHDHQ) * 100) / (SUM(FCXpdNetAfHDHQ)+SUM(FCXpdNetAfHDVD)) AS FCXpdPerPoByHQ_Footer,
                    SUM(FCXpdNetAfHDVD)	AS FCXpdNetAfHDVD_Footer,
                    (SUM(FCXpdNetAfHDVD) * 100) /(SUM(FCXpdNetAfHDVD)+SUM(FCXpdNetAfHDHQ))	AS FCXpdPerPoByVD_Footer
                FROM TRPTSaleFCCompVDTmp WITH(NOLOCK)
                WHERE 1=1
                AND FTComName       = '$tComName'
                AND FTRptCode       = '$tRptCode'
                AND FTUsrSession    = '$tUsrSession'
                GROUP BY FTUsrSession
                ) T ON L.FTUsrSession = T.FTUsrSession_Footer
            ";
        } else {
            // ถ้าไม่ใช่ให้ Select 0 เพื่อให้ Join ได้แต่จะไม่มีการ Sum
            $tJoinFoooter   = " 
                SELECT
                    '$tUsrSession'  AS FTUsrSession_Footer,
                    0   AS FCXpdNetAfHDHQ_Footer,
                    0   AS FCXpdPerPoByHQ_Footer,
                    0   AS FCXpdNetAfHDVD_Footer,
                    0   AS FCXpdPerPoByVD_Footer
                ) T ON  L.FTUsrSession = T.FTUsrSession_Footer
            ";
        }

        $tSQL = "
            SELECT L.*,T.*
            FROM (
                SELECT
                    ROW_NUMBER() OVER(ORDER BY FTBchCode ASC) AS RowID,
                    A.*,
                    S.FNRptGroupMember,
                    S.FCXpdNetAfHDHQ_SUM,
                    S.FCXpdPerPoByHQ_SUM,
                    S.FCXpdNetAfHDVD_SUM,
                    S.FCXpdPerPoByVD_SUM
                FROM TRPTSaleFCCompVDTmp A WITH(NOLOCK)
                LEFT JOIN (
                    SELECT
                        FTBchCode			AS FTBchCode_SUM,
                        COUNT(FTBchCode)	AS FNRptGroupMember,
                        SUM(FCXpdNetAfHDHQ)	AS FCXpdNetAfHDHQ_SUM,
                        (SUM(FCXpdNetAfHDHQ) * 100) / (SUM(FCXpdNetAfHDHQ)+SUM(FCXpdNetAfHDVD)) AS FCXpdPerPoByHQ_SUM,
                        SUM(FCXpdNetAfHDVD)	AS FCXpdNetAfHDVD_SUM,
                        (SUM(FCXpdNetAfHDVD) * 100) /(SUM(FCXpdNetAfHDVD)+SUM(FCXpdNetAfHDHQ))	AS FCXpdPerPoByVD_SUM
                    FROM TRPTSaleFCCompVDTmp WITH(NOLOCK)
                    WHERE FTUsrSession <> ''
                    AND FTComName       = '$tComName'
                    AND FTRptCode       = '$tRptCode'
                    AND FTUsrSession    = '$tUsrSession'
                    GROUP BY FTBchCode
                ) AS S ON A.FTBchCode = S.FTBchCode_SUM
                WHERE 1=1
                AND A.FTComName     = '$tComName'
                AND A.FTRptCode     = '$tRptCode'
                AND A.FTUsrSession  = '$tUsrSession' 
            ) AS L
            LEFT JOIN (
                ".$tJoinFoooter."
        ";
        
        if( $paDataWhere['nPerPage'] != 0 ){
            $tSQL .= "   WHERE L.RowID > $nRowIDStart AND L.RowID <= $nRowIDEnd ";
        }
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aData = $oQuery->result_array();
        } else {
            $aData = NULL;
        }
        $aErrorList = array(
            "nErrInvalidPage" => ""
        );

        $aResualt = array(
            "aPagination"   => $aPagination,
            "aRptData"      => $aData,
            "aError"        => $aErrorList
        );
        unset($oQuery);
        unset($aData);
        return $aResualt;
    }






}