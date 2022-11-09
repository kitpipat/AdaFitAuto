<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rptanalyspurchase_model extends CI_Model {

    public function FSnMExecStoreReport($paDataFilter) {
        // สาขา
        $tBchCodeSelect = ($paDataFilter['bBchStaSelectAll']) ? '' :  FCNtAddSingleQuote($paDataFilter['tBchCodeSelect']);

        $tCallStore = "{ CALL SP_RPTxAnalysPurchase(?,?,?,?,?,?,?,?,?) }";
        $aDataStore = array(
            'ptAgnCode'     => $paDataFilter['tAgnCodeSelect'],
            'ptUsrSession'  => $paDataFilter['tSessionID'],
            'ptLangID'      => $paDataFilter['nLangID'],
            'ptBchCode'     => $tBchCodeSelect,
            'ptDocDateFrm'  => $paDataFilter['tDocDateFrom'],
            'ptDocDateTo'   => $paDataFilter['tDocDateTo'],
            'ptPdtCodeFrm'  => $paDataFilter['tPdtCodeFrom'],
            'ptPdtCodeTo'   => $paDataFilter['tPdtCodeTo'],
            'ptRptGroup'    => $paDataFilter['tRptCondition']
        );

        $oQuery = $this->db->query($tCallStore, $aDataStore);
        // print_r($this->db->last_query());
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

        $tSQL = "
            SELECT
                COUNT(AnlPur_TMP.FTPdtCode) AS rnCountPage
            FROM TRPTxAnalysPurchaseTmp AnlPur_TMP WITH(NOLOCK)
            WHERE 1=1
            AND AnlPur_TMP.FTUsrSession = '$tUsrSession'
        ";

        $oQuery             = $this->db->query($tSQL);
        $nRptAllRecord      = $oQuery->row_array()['rnCountPage'];
        $nPage              = $paDataWhere['nPage'];
        $nPerPage           = $paDataWhere['nPerPage'];
        $nPrevPage          = $nPage - 1;
        $nNextPage          = $nPage + 1;
        $nRowIDStart        = (($nPerPage * $nPage) - $nPerPage); //RowId Start
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
            // $tJoinFoooter = " SELECT
            //         FTUsrSession            AS FTUsrSession_Footer

            //     FROM TRPTSaleGrpByCondTmp WITH(NOLOCK)
            //     WHERE 1=1
            //     AND FTUsrSession    = '$tUsrSession'
            //     GROUP BY FTUsrSession ) T ON L.FTUsrSession = T.FTUsrSession_Footer
            // ";
            $tJoinFoooter = "SELECT 
                                FTUsrSession			AS FTUsrSession_Footer,
                                SUM(FCXsdQtyAll)		AS FCXsdQtyAllTotal_Footer,
                                SUM(FCXsdQtyAvgPct)		AS FCXsdQtyAvgPctTotal_Footer,
                                SUM(FCXsdAmtB4DisChg)	AS FCXsdAmtB4DisChgTotal_Footer,
                                SUM(FCXsdAmtAvgPct)		AS FCXsdAmtAvgPctTotal_Footer,
                                SUM(FCXsdDisChg)		AS FCXsdDisChgTotal_Footer,
                                SUM(FCXsdNetAfHD)		AS FCXsdNetAfHDTotal_Footer,
                                SUM(FCXsdNetAvgPct)		AS FCXsdNetAvgPctTotal_Footer
                            FROM TRPTxAnalysPurchaseTmp WITH(NOLOCK)
                            WHERE 1=1
                            AND FTUsrSession    = '$tUsrSession'
                            GROUP BY FTUsrSession ) T ON L.FTUsrSession = T.FTUsrSession_Footer";
        } else {
            // ถ้าไม่ใช่ให้ Select 0 เพื่อให้ Join ได้แต่จะไม่มีการ Sum
            $tJoinFoooter = "
                SELECT
                    '$tUsrSession'  AS FTUsrSession_Footer,
                    0		AS FCXsdQtyAllTotal_Footer,
                    0		AS FCXsdQtyAvgPctTotal_Footer,
                    0	    AS FCXsdAmtB4DisChgTotal_Footer,
                    0		AS FCXsdAmtAvgPctTotal_Footer,
                    0		AS FCXsdDisChgTotal_Footer,
                    0		AS FCXsdNetAfHDTotal_Footer,
                    0		AS FCXsdNetAvgPctTotal_Footer
                ) T ON  L.FTUsrSession = T.FTUsrSession_Footer
            ";
        }

        // L = List ข้อมูลทั้งหมด
        // A = SaleDT
        // S = Misures Summary
        $tSQL   = " SELECT 
                        L.*, T.*
                    FROM 
                        (
                            SELECT 
                                ROW_NUMBER() OVER(ORDER BY A.FTXsdGrpCode ASC, A.FTPdtCode ASC) AS RowID ,
                                ROW_NUMBER () OVER (PARTITION BY A.FTXsdGrpCode ORDER BY A.FTXsdGrpCode ASC) AS PARTITION_Grp, 
                                SUM(1) OVER (PARTITION BY A.FTXsdGrpCode ORDER BY A.FTXsdGrpCode ASC) AS MAX_Grp, 
                                A.* , S.*
                            FROM TRPTxAnalysPurchaseTmp A WITH(NOLOCK) 
                            LEFT JOIN 
                                ( 
                                    SELECT 
                                        FTXsdGrpCode            AS FTXsdGrpCode_SUM, 
                                        COUNT(FTXsdGrpCode)     AS FNRptGroupMember,
                                        SUM(ISNULL(FCXsdQtyAll, 0))			AS FCXsdQtyAll_SUM,
                                        SUM(ISNULL(FCXsdQtyAvgPct, 0))		AS FCXsdQtyAvgPct_SUM,
                                        SUM(ISNULL(FCXsdAmtB4DisChg, 0))	AS FCXsdAmtB4DisChg_SUM, 
                                        SUM(ISNULL(FCXsdAmtAvgPct, 0))		AS FCXsdAmtAvgPct_SUM,
                                        SUM(ISNULL(FCXsdDisChg, 0))			AS FCXsdDisChg_SUM,
                                        SUM(ISNULL(FCXsdNetAfHD, 0))		AS FCXsdNetAfHD_SUM,
                                        SUM(ISNULL(FCXsdNetAvgPct, 0))		AS FCXsdNetAvgPct_SUM
                                    FROM TRPTxAnalysPurchaseTmp WITH(NOLOCK) 
                                    WHERE 1=1 AND FTUsrSession = '$tUsrSession' 
                                    GROUP BY FTXsdGrpCode 
                                ) AS S ON ISNULL(A.FTXsdGrpCode, '') = ISNULL(S.FTXsdGrpCode_SUM , '')
                            WHERE 1=1 AND FTUsrSession = '$tUsrSession' 
                            ) AS L 
                    LEFT JOIN (
                    " . $tJoinFoooter . "
        ";

        // WHERE เงื่อนไข Page
        $tSQL .= " WHERE L.RowID > $nRowIDStart AND L.RowID <= $nRowIDEnd ";

        //สั่ง Order by ตามข้อมูลหลัก
        $tSQL .= " ORDER BY L.FTXsdGrpCode ASC , L.FTPdtCode ASC";
        // print_r($tSQL); exit;
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
                COUNT(DTTMP.FTRptCode) AS rnCountPage
            FROM TRPTxAnalysPurchaseTmp AS DTTMP WITH(NOLOCK)
            WHERE 1 = 1
            AND FTUsrSession    = '$tSessionID'
        ";
        // echo $tSQL;
        $oQuery = $this->db->query($tSQL);
        $nRptAllRecord = $oQuery->row_array()['rnCountPage'];
        unset($oQuery);
        return $nRptAllRecord;
    }

}
