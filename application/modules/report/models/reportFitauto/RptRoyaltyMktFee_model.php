<?php

defined('BASEPATH') or exit('No direct script access allowed');

class RptRoyaltyMktFee_model extends CI_Model {

    public function FSnMExecStoreReport($paDataFilter) {
        // สาขา
        // $tBchCodeSelect = ($paDataFilter['bBchStaSelectAll']) ? '' :  FCNtAddSingleQuote($paDataFilter['tBchCodeSelect']);

    //     @pnLngID int , 
    //     @pnComName Varchar(100),
    //     @ptRptCode Varchar(100),
    //     @ptUsrSession Varchar(255),
    //     @pnFilterType int, --1 BETWEEN 2 IN
    //     @ptBchLTo Varchar(8000), --กรณี Condition IN
    //     @ptAgnLTo Varchar(8000), --กรณี Condition IN
    //     @ptDocDateF Varchar(10),
    //     @ptDocDateT Varchar(10),
    //     @FNResult INT OUTPUT 

        $tCallStore = "{ CALL SP_RPTxRoyaltyMktFee(?,?,?,?,?,?,?,?,?,?) }";
        $aDataStore = array(
            'pnLngID'       => $paDataFilter['nLangID'],
            'pnComName'     => $paDataFilter['tCompName'],
            'ptRptCode'     => $paDataFilter['tRptCode'],
            'ptUsrSession'  => $paDataFilter['tSessionID'],
            'pnFilterType'  => $paDataFilter['tTypeSelect'],
            'ptBchLTo'      => $paDataFilter['tBchCodeSelect'],
            'ptAgnLTo'      => $paDataFilter['tAgnCodeSelect'],
            'ptDocDateF'    => $paDataFilter['tDocDateFrom'],
            'ptDocDateT'    => $paDataFilter['tDocDateTo'],
            'FNResult'      => 0
        );

        $oQuery = $this->db->query($tCallStore, $aDataStore);
        // print_r($oQuery);
        // exit();
        if ($oQuery !== FALSE) {
            unset($oQuery);
            return 1;
        } else {
            unset($oQuery);
            return 0;
        }
    }

    public function FMaMRPTPagination($paDataWhere) {

        $tUsrSession = $paDataWhere['tUsrSessionID'];

        $tSQL = "   SELECT
                        COUNT(TMP.FTUsrSession) AS rnCountPage
                    FROM TRPTRoyaltyMktFeeTmp TMP WITH(NOLOCK)
                    WHERE TMP.FTUsrSession = '$tUsrSession' ";

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

    public function FSaMGetDataReport($paDataWhere) {

        $nPage = $paDataWhere['nPage'];
        // Call Data Pagination
        $aPagination    = $this->FMaMRPTPagination($paDataWhere);

        $nRowIDStart    = $aPagination["nRowIDStart"];
        $nRowIDEnd      = $aPagination["nRowIDEnd"];
        $nTotalPage     = $aPagination["nTotalPage"];

        $tUsrSession    = $paDataWhere['tUsrSessionID'];

        // Check ว่าเป็นหน้าสุดท้ายหรือไม่ ถ้าเป็นหน้าสุดท้ายให้ไป Sum footer ข้อมูลมา
        if ($nPage == $nTotalPage) {
            $tJoinFoooter = "   SELECT
                                    FTUsrSession AS FTUsrSession_Footer
                                FROM TRPTRoyaltyMktFeeTmp WITH(NOLOCK)
                                WHERE FTUsrSession    = '$tUsrSession'
                                GROUP BY FTUsrSession ) T ON L.FTUsrSession = T.FTUsrSession_Footer ";
        } else {
            // ถ้าไม่ใช่ให้ Select 0 เพื่อให้ Join ได้แต่จะไม่มีการ Sum
            $tJoinFoooter = " SELECT
                                    '$tUsrSession'  AS FTUsrSession_Footer
                                ) T ON  L.FTUsrSession = T.FTUsrSession_Footer ";
        }

        // L = List ข้อมูลทั้งหมด
        // A = SaleDT
        // S = Misures Summary
        $tSQL   = " SELECT
                        L.*,
                        T.*
                    FROM (
                        SELECT
                            ROW_NUMBER() OVER(ORDER BY A.FTBchCodeTo ASC) AS RowID ,
                            A.*,
                            B.*
                        FROM TRPTRoyaltyMktFeeTmp A WITH(NOLOCK)
                        INNER JOIN (
                            SELECT 
                                FTUsrSession AS FTUsrSession_SUM,
                                SUM(FCXphTotal) AS FCXphTotal_SUM,
                                SUM(FCXphDicount) AS FCXphDicount_SUM,
                                SUM(FCXphGrand) AS FCXphGrand_SUM,
                                SUM(FTCphVatable) AS FTCphVatable_SUM,
                                SUM(FCXphVat) AS FCXphVat_SUM,
                                SUM(FCRoyaltyfeeBFVAT) AS FCRoyaltyfeeBFVAT_SUM,
                                SUM(FCMarketingfeeBFVAT) AS FCMarketingfeeBFVAT_SUM
                            FROM TRPTRoyaltyMktFeeTmp GROUP BY FTUsrSession
                        ) B ON B.FTUsrSession_SUM = A.FTUsrSession
                        WHERE A.FTUsrSession = '$tUsrSession'
                        /* End Calculate Misures */
                    ) AS L
                    LEFT JOIN (
                    " . $tJoinFoooter . "
        ";

        // WHERE เงื่อนไข Page
        $tSQL .= " WHERE L.RowID > $nRowIDStart AND L.RowID <= $nRowIDEnd ";

        //สั่ง Order by ตามข้อมูลหลัก
        $tSQL .= " ORDER BY L.FTBchCodeTo";
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
    public function FSnMCountDataReportAll($paDataWhere) {
        $tSessionID = $paDataWhere['tSessionID'];

        $tSQL = "
            SELECT
                COUNT(DTTMP.FTUsrSession) AS rnCountPage
            FROM TRPTRoyaltyMktFeeTmp AS DTTMP WITH(NOLOCK)
            WHERE FTUsrSession = '$tSessionID'
        ";
        // echo $tSQL;
        $oQuery = $this->db->query($tSQL);
        $nRptAllRecord = $oQuery->row_array()['rnCountPage'];
        unset($oQuery);
        return $nRptAllRecord;
    }

}
