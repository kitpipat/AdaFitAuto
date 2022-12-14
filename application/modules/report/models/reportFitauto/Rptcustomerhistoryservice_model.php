<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rptcustomerhistoryservice_model extends CI_Model
{

    // Functionality: Call Stored Procedure
    // Parameters:  Function Parameter
    // Creator: 01/02/2021 Worakorn
    // Last Modified : -
    // Return : Status Return Call Stored Procedure
    // Return Type: Array
    public function FSnMExecStoreReport($paDataFilter)
    {
        // สาขา
        $tBchCodeSelect = ($paDataFilter['bBchStaSelectAll']) ? '' :  FCNtAddSingleQuote($paDataFilter['tBchCodeSelect']);
        // ร้านค้า
        $tShpCodeSelect = ($paDataFilter['bShpStaSelectAll']) ? '' :  FCNtAddSingleQuote($paDataFilter['tShpCodeSelect']);
        // กลุ่มธุรกิจ
        $tMerCodeSelect = ($paDataFilter['bMerStaSelectAll']) ? '' :  FCNtAddSingleQuote($paDataFilter['tMerCodeSelect']);
        // ประเภทเครื่องจุดขาย
        $tPosCodeSelect = ($paDataFilter['bPosStaSelectAll']) ? '' :  FCNtAddSingleQuote($paDataFilter['tPosCodeSelect']);

        $tCallStore = "{ CALL SP_RPTxServiceHis(?,?,?,?,?,?,?,?,?,?) }";
        $aDataStore = array(
            'ptSessionID'           => $paDataFilter['tSessionID'],
            'ptAgnCode'             => $paDataFilter['tAgnCodeSelect'],
            'ptBchCode'             => $tBchCodeSelect,
            'ptCstCodeFrm'          => $paDataFilter['tCstCodeFrom'],
            'ptCstCodeTo'           => $paDataFilter['tCstCodeTo'],
            'ptCarRegCodeFrm'       => $paDataFilter['tRegCodeFrom'],
            'ptCarRegCodeTo'        => $paDataFilter['tRegCodeTo'],
            'pdServiceDateFrm'      => $paDataFilter['tDocDateFrom'],
            'pdServiceDateTo'       => $paDataFilter['tDocDateTo'],
            'pnResult'              => 0,


        );
        $oQuery = $this->db->query($tCallStore, $aDataStore);
        // print_r($this->db->last_query());exit;
        if ($oQuery !== FALSE) {
            unset($oQuery);
            return 1;
        } else {
            unset($oQuery);
            return 0;
        }
    }

    // Functionality: Get Data Page Co
    // Parameters:  Function Parameter
    // Creator: 01/02/2021 Worakorn
    // Last Modified : -
    // Return : Array Data Page Nation
    // Return Type: Array
    public function FMaMRPTPagination($paDataWhere)
    {

        $tComName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];
        $tUsrSession = $paDataWhere['tUsrSessionID'];

        // $tSQL = "   
        //     SELECT
        //         COUNT(ADJSTK_TMP.FTPdtCode) AS rnCountPage
        //     FROM TRPTSVServiceHisTmp ADJSTK_TMP WITH(NOLOCK)
        //     WHERE 1=1
        //     AND ADJSTK_TMP.FTComName    = '$tComName'
        //     AND ADJSTK_TMP.FTRptCode    = '$tRptCode'
        //     AND ADJSTK_TMP.FTUsrSession = '$tUsrSession'
        // ";
        $tSQL = "   
        SELECT
            COUNT(ADJSTK_TMP.FTXshDocNo) AS rnCountPage
        FROM TRPTSVServiceHisTmp ADJSTK_TMP WITH(NOLOCK)
        WHERE 1=1
        AND ADJSTK_TMP.FTUsrSession = '$tUsrSession'
    ";

        $oQuery = $this->db->query($tSQL);
        $nRptAllRecord = $oQuery->row_array()['rnCountPage'];
        $nPage = $paDataWhere['nPage'];
        $nPerPage = $paDataWhere['nPerPage'];
        $nPrevPage = $nPage - 1;
        $nNextPage = $nPage + 1;
        $nRowIDStart = (($nPerPage * $nPage) - $nPerPage); //RowId Start
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

    // Functionality: Get Data Page Co
    // Parameters:  Function Parameter
    // Creator: 01/02/2021 Worakorn
    // Last Modified : -
    // Return : Array Data Page Nation
    // Return Type: Array
    public function FMxMRPTSetPriorityGroup($paDataWhere)
    {

        $tComName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];
        $tUsrSession = $paDataWhere['tUsrSessionID'];

        // $tSQL   = " UPDATE TRPTSVServiceHisTmp
        //                 SET FNRowPartID = B.PartID
        //             FROM(
        //                 SELECT
        //                     ROW_NUMBER() OVER(PARTITION BY FTPdtCode ORDER BY FTPdtCode DESC) AS PartID,
        //                     FTRptRowSeq
        //                 FROM TRPTSVServiceHisTmp TMP WITH(NOLOCK)
        //                 AND TMP.FTUsrSession    = '$tUsrSession'
        //             ) AS B
        //             WHERE TRPTSVServiceHisTmp.FTRptRowSeq = B.FTRptRowSeq
        //             AND TRPTSVServiceHisTmp.FTUsrSession   = '$tUsrSession'
        // ";

        $tSQL   = " UPDATE TRPTSVServiceHisTmp
        SET FNRowPartID = B.PartID
    FROM(
        SELECT
            ROW_NUMBER() OVER(PARTITION BY FTXshDocNo ORDER BY FTXshDocNo DESC) AS PartID,
            FTRptRowSeq
        FROM TRPTSVServiceHisTmp TMP WITH(NOLOCK)
        WHERE TMP.FTUsrSession    = '$tUsrSession'
    ) AS B
    WHERE TRPTSVServiceHisTmp.FTRptRowSeq = B.FTRptRowSeq
    AND TRPTSVServiceHisTmp.FTUsrSession   = '$tUsrSession'
";


        $this->db->query($tSQL);
    }

    // Functionality: Call Stored Procedure
    // Parameters:  Function Parameter
    // Creator: 01/02/2021 Worakorn
    // Last Modified : -
    // Return : Status Return Call Stored Procedure
    // Return Type: Array
    public function FSaMGetDataReport($paDataWhere)
    {

        $nPage = $paDataWhere['nPage'];
        // Call Data Pagination
        $aPagination    = $this->FMaMRPTPagination($paDataWhere);

        $nRowIDStart    = $aPagination["nRowIDStart"];
        $nRowIDEnd      = $aPagination["nRowIDEnd"];
        $nTotalPage     = $aPagination["nTotalPage"];

        $tComName       = $paDataWhere['tCompName'];
        $tRptCode       = $paDataWhere['tRptCode'];
        $tUsrSession    = $paDataWhere['tUsrSessionID'];

        // Set Priority
        $this->FMxMRPTSetPriorityGroup($paDataWhere);

        // Check ว่าเป็นหน้าสุดท้ายหรือไม่ ถ้าเป็นหน้าสุดท้ายให้ไป Sum footer ข้อมูลมา
        if ($nPage == $nTotalPage) {
            $tJoinFoooter = "   
                SELECT
                FTUsrSession            AS FTUsrSession_Footer,
                SUM(FCXrcNet)           AS FCXidQty_Footer
                FROM TRPTSVServiceHisTmp WITH(NOLOCK)
                WHERE 1=1
                AND FTUsrSession    = '$tUsrSession'
                GROUP BY FTUsrSession ) T ON L.FTUsrSession = T.FTUsrSession_Footer
            ";
        } else {
            // ถ้าไม่ใช่ให้ Select 0 เพื่อให้ Join ได้แต่จะไม่มีการ Sum
            $tJoinFoooter = "   
                SELECT
                    '$tUsrSession'  AS FTUsrSession_Footer,
                    '0'             AS FCXidQty_Footer
                ) T ON  L.FTUsrSession = T.FTUsrSession_Footer
            ";
        }


        // L = List ข้อมูลทั้งหมด
        // A = SaleDT
        // S = Misures Summary

        $tSQL   = " SELECT
        L.*,
        T.FCXidQty_Footer 
    FROM (
        SELECT
            ROW_NUMBER() OVER(ORDER BY FTXshDocNo ASC) AS RowID ,
            A.*,
            S.FNRptGroupMember,
            S.FCXidQty_SubFooter
        FROM TRPTSVServiceHisTmp A WITH(NOLOCK)
        /* Calculate Misures */
        LEFT JOIN (
            SELECT
            FTXshDocNo              AS FTAjhDocNo_SUM,
                COUNT(FTXshDocNo)       AS FNRptGroupMember,
                SUM(FCXrcNet)      AS FCXidQty_SubFooter
            FROM TRPTSVServiceHisTmp WITH(NOLOCK)
            WHERE 1=1
            AND FTUsrSession    = '$tUsrSession'
            GROUP BY FTXshDocNo
        ) AS S ON A.FTXshDocNo = S.FTAjhDocNo_SUM
        WHERE 1=1 AND A.FTUsrSession = '$tUsrSession'
        /* End Calculate Misures */
    ) AS L
    LEFT JOIN (
    " . $tJoinFoooter . "
";



        // WHERE เงื่อนไข Page
        $tSQL .= " WHERE L.RowID > $nRowIDStart AND L.RowID <= $nRowIDEnd ";

        //สั่ง Order by ตามข้อมูลหลัก
        $tSQL .= " ORDER BY L.FTXshDocNo ASC";

        // print_r($tSQL);exit;
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
            "aPagination" => $aPagination,
            "aRptData" => $aData,
            "aError" => $aErrorList
        );
        unset($oQuery);
        unset($aData);
        return $aResualt;
    }

    // Functionality: Count Data Report All
    // Parameters: Function Parameter
    // Creator: 01/02/2021 Worakorn
    // Last Modified: -
    // Return: Data Report All
    // ReturnType: Array
    public function FSnMCountDataReportAll($paDataWhere)
    {
        $tSessionID = $paDataWhere['tSessionID'];
        $tCompName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];

        $tSQL = "   
            SELECT 
                COUNT(DTTMP.FTRptCode) AS rnCountPage
            FROM TRPTSVServiceHisTmp AS DTTMP WITH(NOLOCK)
            WHERE 1 = 1
            AND FTUsrSession    = '$tSessionID'
        ";



        $oQuery = $this->db->query($tSQL);
        $nRptAllRecord = $oQuery->row_array()['rnCountPage'];
        unset($oQuery);
        return $nRptAllRecord;
    }
}
