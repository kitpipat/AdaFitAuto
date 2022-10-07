<?php

defined('BASEPATH') or exit('No direct script access allowed');

class RptReprintDocument_model extends CI_Model {

    /**
     * Functionality: Delete Temp Report
     * Parameters:  Function Parameter
     * Creator: 06/10/2022 Wasin
     * Last Modified : 
     * Return : Call Store Proce
     * Return Type: Array
    */
    public function FSnMExecStoreReport($paDataFilter) {
        $nLangID        = $paDataFilter['nLangID'];
        $tComName       = $paDataFilter['tCompName'];
        $tRptCode       = $paDataFilter['tRptCode'];
        $tUserSession   = $paDataFilter['tUserSession'];
        // สาขา
        $tBchCodeSelect = ($paDataFilter['bBchStaSelectAll']) ?     '' : FCNtAddSingleQuote($paDataFilter['tBchCodeSelect']);
        // /แคชเชีย
        $tCstCodeSelect = ($paDataFilter['bCashierStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tCashierCodeSelect']);
        // ประเภทเครื่องจุดขาย
        $tPosCodeSelect = ($paDataFilter['bPosStaSelectAll']) ?     '' : FCNtAddSingleQuote($paDataFilter['tPosCodeSelect']);
        $tCallStore     = "{ CALL SP_RPTxReprintDocTmp(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) }";
        $aDataStore     = [
            'pnLngID'       => $nLangID,
            'ptComName'     => $tComName,
            'ptRptCode'     => $tRptCode,
            'ptUsrSession'  => $tUserSession,
            'pnFilterType'  => intval($paDataFilter['nFilterType']),
            // สาขา
            'ptBchL'        => $tBchCodeSelect,
            'ptBchF'        => $paDataFilter['tBchCodeFrom'],
            'ptBchT'        => $paDataFilter['tBchCodeTo'],
            // เครื่องจุดขาย
            'ptPosL'        => $tPosCodeSelect,
            'ptPosF'        => $paDataFilter['tCashierCodeFrom'],
            'ptPosT'        => $paDataFilter['tCashierCodeTo'],
            // แคชเชียร์
            'ptUsrL'        => $tCstCodeSelect,
            'ptUsrF'        => $paDataFilter['tCashierCodeFrom'],
            'ptUsrT'        => $paDataFilter['tCashierCodeTo'],
            // วันที่เอกสาร
            'ptDocDateF'    => $paDataFilter['tDocDateFrom'],
            'ptDocDateT'    => $paDataFilter['tDocDateTo'],
            'FNResult'      => 0
        ];
        $oQuery = $this->db->query($tCallStore, $aDataStore);
        if ($oQuery != FALSE) {
            $nStaReturn = 1;
        } else {
            $nStaReturn = 0;
        }
        unset($nLangID,$tComName,$tRptCode,$tUserSession,$tBchCodeSelect,$tCstCodeSelect,$tPosCodeSelect,$tCallStore,$aDataStore);
        unset($oQuery,$paDataFilter);
        return $nStaReturn;
    }

    /**
     * Functionality: Get Data Report
     * Parameters:  Function Parameter
     * Creator: 06/10/2022 Wasin
     * Last Modified : 
     * Return : Get Data Rpt Temp
     * Return Type: Array
    */
    public function FSaMGetDataReport($paDataWhere) {
        $nPage          = $paDataWhere['nPage'];
        // Call Data Pagination
        $aPagination    = $this->FMaMRPTPagination($paDataWhere);
        $nRowIDStart    = $aPagination["nRowIDStart"];
        $nRowIDEnd      = $aPagination["nRowIDEnd"];
        $nTotalPage     = $aPagination["nTotalPage"];
        $tComName       = $paDataWhere['tCompName'];
        $tRptCode       = $paDataWhere['tRptCode'];
        $tSession       = $paDataWhere['tUsrSessionID'];
        $aData          = $this->FMxMRPTSetPriorityGroup($tComName, $tRptCode, $tSession);

        // Check ว่าเป็นหน้าสุดท้ายหรือไม่ ถ้าเป็นหน้าสุดท้ายให้ไป Sum footer ข้อมูลมา
        if ($nPage == $nTotalPage) {
            $tJoinFoooter = "
                SELECT
                    FTUsrSession AS FTUsrSession_Footer,
                    SUM(
                        ISNULL(FNXthReprintNum, 0)
                    ) AS FNXthReprintNum_Footer
                FROM TRPTReprintDocTmp WITH(NOLOCK)
                WHERE FTComName     = ".$this->db->escape($tComName)."
                AND FTRptCode       = ".$this->db->escape($tRptCode)."
                AND FTUsrSession    = ".$this->db->escape($tSession)."
                GROUP BY FTUsrSession ) T
                ON L.FTUsrSession = T.FTUsrSession_Footer
            ";
        } else {
            // ถ้าไม่ใช่ให้ Select 0 เพื่อให้ Join ได้แต่จะไม่มีการ Sum
            $tJoinFoooter = "
                SELECT
                    ".$this->db->escape($tSession)." AS FTUsrSession_Footer,
                    '0' AS FNXthReprintNum_Footer
                ) T ON  L.FTUsrSession = T.FTUsrSession_Footer
            ";
        }

        // L = List ข้อมูลทั้งหมด
        // A = SaleDT
        // S = Misures Summary
        $tSQL = "
            SELECT
                L.*,
                T.*
            FROM (
                SELECT
                    ROW_NUMBER() OVER(ORDER BY FTBchCode ASC ,FDXthHisDateTime ASC) AS RowID,
                    A.*,
                    S.FNRptGroupMember
                FROM TRPTReprintDocTmp A WITH(NOLOCK)
                /* Calculate Misures */

                LEFT JOIN (
                    SELECT
                        FTBchCode           AS FTBchCode_SUM,
                        COUNT(FTBchCode)    AS FNRptGroupMember
                    FROM TRPTReprintDocTmp WITH(NOLOCK)
                    WHERE FTComName     = ".$this->db->escape($tComName)."
                    AND FTRptCode       = ".$this->db->escape($tRptCode)."
                    AND FTUsrSession    = ".$this->db->escape($tSession)."
                    GROUP BY FTBchCode
                ) AS S ON A.FTBchCode = S.FTBchCode_SUM
                WHERE A.FTComName       = ".$this->db->escape($tComName)."
                AND   A.FTRptCode       = ".$this->db->escape($tRptCode)."
                AND   A.FTUsrSession    = ".$this->db->escape($tSession)."

                /* End Calculate Misures */
            ) AS L
            LEFT JOIN (
                " . $tJoinFoooter . "
        ";
        // WHERE เงื่อนไข Page
        $tSQL   .= " WHERE L.RowID > $nRowIDStart AND L.RowID <= $nRowIDEnd ";
        // สั่ง Order by ตามข้อมูลหลัก
        $tSQL   .= " ORDER BY FTBchCode ASC , FDXthHisDateTime ASC";

        $oQuery  = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aData  = $oQuery->result_array();
        } else {
            $aData  = NULL;
        }
        $aErrorList = array("nErrInvalidPage"   =>  "");
        $aResualt   = array(
            "aPagination"   => $aPagination,
            "aRptData"      => $aData,
            "aError"        => $aErrorList
        );

        unset($oQuery);
        unset($aData);
        return $aResualt;
    }

    /**
     * Functionality: Calcurate Pagination
     * Parameters:  Function Parameter
     * Creator: 06/10/2022 Wasin
     * Last Modified : 
     * Return : Get Data Rpt Temp
     * Return Type: Array
    */
    public function FMaMRPTPagination($paDataWhere) {
        $tComName       = $paDataWhere['tCompName'];
        $tRptCode       = $paDataWhere['tRptCode'];
        $tUsrSession    = $paDataWhere['tUsrSessionID'];
        $tSQL           = "
            SELECT TMP.FTUsrSession
            FROM TRPTReprintDocTmp TMP WITH(NOLOCK)
            WHERE TMP.FTComName     = '$tComName'
            AND TMP.FTRptCode       = '$tRptCode'
            AND TMP.FTUsrSession    = '$tUsrSession'
        ";
        $oQuery         = $this->db->query($tSQL);
        $nRptAllRecord  = $oQuery->num_rows();
        $nPage          = $paDataWhere['nPage'];
        $nPerPage       = $paDataWhere['nPerPage'];
        $nPrevPage      = $nPage - 1;
        $nNextPage      = $nPage + 1;
        $nRowIDStart    = (($nPerPage * $nPage) - $nPerPage); // RowId Start
        if ($nRptAllRecord <= $nPerPage) {
            $nTotalPage = 1;
        } else if (($nRptAllRecord % $nPerPage) == 0) {
            $nTotalPage = ($nRptAllRecord / $nPerPage);
        } else {
            $nTotalPage = ($nRptAllRecord / $nPerPage) + 1;
            $nTotalPage = (int) $nTotalPage;
        }
        // get rowid end
        $nRowIDEnd = $nPerPage * $nPage;
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
        unset($oQuery);
        return $aRptMemberDet;
    }

    /**
     * Functionality: Set Priority Group
     * Parameters:  Function Parameter
     * Creator: 06/10/2022 Wasin
     * Last Modified :
     * Return : Get Data Rpt Temp
     * Return Type: Array
    */
    public function FMxMRPTSetPriorityGroup($ptComName, $ptRptCode, $ptUsrSession) {
        $tSQL = "
            UPDATE TRPTReprintDocTmp SET
                FNRowPartID = B.PartID
            FROM(
                SELECT
                    ROW_NUMBER() OVER(PARTITION BY FTBchCode ORDER BY FTBchCode ASC , FDXthHisDateTime ASC) AS PartID,
                    FTRptRowSeq
                FROM TRPTReprintDocTmp TMP WITH(NOLOCK)
                WHERE TMP.FTComName     = '$ptComName'
                AND TMP.FTRptCode       = '$ptRptCode'
                AND TMP.FTUsrSession    = '$ptUsrSession'
            ) AS B
            WHERE TRPTReprintDocTmp.FTRptRowSeq = B.FTRptRowSeq
            AND TRPTReprintDocTmp.FTComName     = '$ptComName'
            AND TRPTReprintDocTmp.FTRptCode     = '$ptRptCode'
            AND TRPTReprintDocTmp.FTUsrSession  = '$ptUsrSession'
        ";
        $this->db->query($tSQL);
        return;
    }






}