<?php defined('BASEPATH') or exit('No direct script access allowed');

class mRptMovePosVd extends CI_Model{

    //Functionality: Delete Temp Report
    //Parameters:  Function Parameter
    //Creator: 16/08/2019 Saharat(Golf)
    //Last Modified :
    //Return : Call Store Proce
    //Return Type: Array
    public function FSnMExecStoreReport($paDataFilter){
        // สาขา
        $tBchCodeSelect = ($paDataFilter['bBchStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tBchCodeSelect']);
        $tCallStore     = "{ CALL SP_RPTxStockMovent1002002(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) }";
        $aDataStore     = array(
            'pnLngID'           => $paDataFilter['nLangID'],
            'pnComName'         => $paDataFilter['tCompName'],
            'ptRptCode'         => $paDataFilter['tRptCode'],
            'ptUsrSession'      => $paDataFilter['tUserSession'],
            'pnFilterType'      => $paDataFilter['tTypeSelect'],
            'ptBchL'            => $tBchCodeSelect,
            'ptBchF'            => $paDataFilter['tBchCodeFrom'],
            'ptBchT'            => $paDataFilter['tBchCodeTo'],
            'ptPdtF'            => $paDataFilter['tPdtCodeFrom'],
            'ptPdtT'            => $paDataFilter['tPdtCodeTo'],
            'ptWahF'            => $paDataFilter['tWahCodeFrom'],
            'ptWahT'            => $paDataFilter['tWahCodeTo'],
            'ptMonth'           => ltrim($paDataFilter['tMonth'],0),
            'ptYear'            => $paDataFilter['tYear'],
            'ptPdtStaActive'    => $paDataFilter['tPdtStaActive'],
            'ptCate1From'       => FCNtAddSingleQuote($paDataFilter['tCate1From']),
            'ptCate2From'       => FCNtAddSingleQuote($paDataFilter['tCate2From']),
            'FNResult'          => 0,
        );
        $oQuery = $this->db->query($tCallStore, $aDataStore);
        if ($oQuery !== FALSE) {
            $nStaReturn = 1;
        } else {
            $nStaReturn = 0;
        }
        unset($tBchCodeSelect,$tCallStore,$aDataStore,$oQuery,$paDataFilter);
        return $nStaReturn;
    }

    // Functionality: Get Data Report
    // Parameters:  Function Parameter
    // Creator: 10/07/2019 Saharat(Golf)
    // Last Modified : 19/11/2019 wasin(Yoshi)
    // Return : Get Data Rpt Temp
    // Return Type: Array
    public function FSaMGetDataReport($paDataWhere){
        $nPage  = $paDataWhere['nPage'];
        if($paDataWhere['nPerPage'] != 0){
            // Call Data Pagination 
            $aPagination    = $this->FMaMRPTPagination($paDataWhere);
            $nRowIDStart    = $aPagination["nRowIDStart"];
            $nRowIDEnd      = $aPagination["nRowIDEnd"];
            $nTotalPage     = $aPagination["nTotalPage"];
        }else{
            $aPagination = 0;
            $nPage = 1;
            $nTotalPage = 1;
        }
        // Call Data Pagination 
        $tComName   = $paDataWhere['tCompName'];
        $tRptCode   = $paDataWhere['tRptCode'];
        $tSession   = $paDataWhere['tUsrSessionID'];
        //Set Priority
        $this->FMxMRPTSetPriorityGroup($tComName, $tRptCode, $tSession);
        $this->FMxMRPTAjdStkBal($tComName, $tRptCode, $tSession);
        // Check ว่าเป็นหน้าสุดท้ายหรือไม่ ถ้าเป็นหน้าสุดท้ายให้ไป Sum footer ข้อมูลมา
        if ($nPage == $nTotalPage) {
            $tJoinFoooter   =   "
                    SELECT
                        FTUsrSession        AS FTUsrSession_Footer,
                        SUM(FCStkQtyMonEnd) AS FCStkQtyMonEnd_Footer,
                        SUM(FCStkQtyIn)     AS FCStkQtyIn_Footer,
                        SUM(FCStkQtyOut)    AS FCStkQtyOut_Footer,
                        SUM(FCStkQtySaleDN - FCStkQtyCN) AS FCStkQtySale_Footer,
                        SUM(FCStkQtyAdj)    AS FCStkQtyAdj_Footer,
                        SUM(FCStkQtyBal)    AS FCStkQtyBal_Footer
                    FROM TRPTPdtStkCrdTmp WITH(NOLOCK)
                    WHERE FTUsrSession <> ''
                    AND FTComName       = ".$this->db->escape($tComName)."
                    AND FTRptCode       = ".$this->db->escape($tRptCode)."
                    AND FTUsrSession    = ".$this->db->escape($tSession)."
                    GROUP BY FTUsrSession
                ) T ON L.FTUsrSession = T.FTUsrSession_Footer
            ";
        } else {
            // ถ้าไม่ใช่ให้ Select 0 เพื่อให้ Join ได้แต่จะไม่มีการ Sum
            $tJoinFoooter   =   "
                    SELECT
                        '$tSession' AS FTUsrSession_Footer,
                        '0'           AS FCStkQtyMonEnd_Footer,
                        '0'           AS FCStkQtyIn_Footer,
                        '0'           AS FCStkQtyOut_Footer,
                        '0'           AS FCStkQtySale_Footer,
                        '0'           AS FCStkQtyAdj_Footer,
                        '0'           AS FCStkQtyBal_Footer
                ) T ON  L.FTUsrSession = T.FTUsrSession_Footer
            ";
        }
        $tSQL   = "
            SELECT L.*,T.*
            FROM (
                SELECT DISTINCT
                    ROW_NUMBER() OVER(ORDER BY DATASTKCRD.FTBchCode ASC,DATASTKCRD.FTWahCode ASC,DATASTKCRD.FTPdtCode ASC,DATASTKCRD.FDStkDate ASC, DATASTKCRD.FTRptRowSeq ASC) AS RowID,
                        DATASTKCRD.*,
                        DATASUBPDT.*,
                        DATASUBWAH.*,
                        DATASUBBCH.*
                        FROM TRPTPdtStkCrdTmp DATASTKCRD WITH(NOLOCK)
                        LEFT JOIN (
                            SELECT DISTINCT
                                FTBchCode   AS FTBchCode_SUBPDT,
                                FTWahCode   AS FTWahCode_SUBPDT,
                                FTPdtCode   AS FTPdtCode_SUBPDT,
                                COUNT(FTPdtCode)    AS FNRptGroupMember_SUBPDT,
                                SUM(FCStkQtyMonEnd) AS FCStkQtyMonEnd_SUBPDT,
                                SUM(FCStkQtyIn)     AS FCStkQtyIn_SUBPDT,
                                SUM(FCStkQtyOut)    AS FCStkQtyOut_SUBPDT,
                                SUM(FCStkQtySaleDN - FCStkQtyCN)    AS FCStkQtySale_SUBPDT,
                                SUM(FCStkQtyAdj)    AS FCStkQtyAdj_SUBPDT,
                                SUM(FCStkQtyBal)    AS FCStkQtyBal_SUBPDT
                            FROM TRPTPdtStkCrdTmp WITH(NOLOCK)
                            WHERE FTUsrSession <> ''
                            AND FTComName       = ".$this->db->escape($tComName)."
                            AND FTRptCode       = ".$this->db->escape($tRptCode)."
                            AND FTUsrSession    = ".$this->db->escape($tSession)."
                            GROUP BY FTBchCode,FTWahCode,FTPdtCode
                        ) DATASUBPDT ON DATASTKCRD.FTBchCode = DATASUBPDT.FTBchCode_SUBPDT AND DATASTKCRD.FTWahCode = DATASUBPDT.FTWahCode_SUBPDT AND DATASTKCRD.FTPdtCode = DATASUBPDT.FTPdtCode_SUBPDT
                        LEFT JOIN (
                            SELECT DISTINCT
                                FTBchCode   AS FTBchCode_SUBWAH,
                                FTWahCode   AS FTWahCode_SUBWAH,
                                COUNT(FTWahCode)    AS FNRptGroupMember_SUBWAH,
                                SUM(FCStkQtyMonEnd)	AS FCStkQtyMonEnd_SUBWAH,
                                SUM(FCStkQtyIn)     AS FCStkQtyIn_SUBWAH,
                                SUM(FCStkQtyOut)    AS FCStkQtyOut_SUBWAH,
                                SUM(FCStkQtySaleDN - FCStkQtyCN)    AS FCStkQtySale_SUBWAH,
                                SUM(FCStkQtyAdj)    AS FCStkQtyAdj_SUBWAH,
                                (SUM(FCStkQtyMonEnd) + SUM(FCStkQtyIn) - SUM(FCStkQtyOut) + SUM(FCStkQtyAdj) - SUM(FCStkQtySaleDN - FCStkQtyCN)) AS FCStkQtyBal_SUBWAH
                            FROM TRPTPdtStkCrdTmp WITH(NOLOCK)
                            WHERE FTUsrSession <> ''
                            AND FTComName       = ".$this->db->escape($tComName)."
                            AND FTRptCode       = ".$this->db->escape($tRptCode)."
                            AND FTUsrSession    = ".$this->db->escape($tSession)."
                            GROUP BY FTBchCode,FTWahCode
                        ) DATASUBWAH ON DATASTKCRD.FTBchCode = DATASUBWAH.FTBchCode_SUBWAH AND DATASTKCRD.FTWahCode = DATASUBWAH.FTWahCode_SUBWAH
                        LEFT JOIN (
                            SELECT DISTINCT
                                FTBchCode           AS FTBchCode_SUBBCH,
                                COUNT(FTBchCode)    AS FNRptGroupMember_SUBBCH,
                                SUM(FCStkQtyMonEnd)	AS FCStkQtyMonEnd_SUBBCH,
                                SUM(FCStkQtyIn)     AS FCStkQtyIn_SUBBCH,
                                SUM(FCStkQtyOut)    AS FCStkQtyOut_SUBBCH,
                                SUM(FCStkQtySaleDN - FCStkQtyCN)    AS FCStkQtySale_SUBBCH,
                                SUM(FCStkQtyAdj)    AS FCStkQtyAdj_SUBBCH,
                                (SUM(FCStkQtyMonEnd) + SUM(FCStkQtyIn) - SUM(FCStkQtyOut) + SUM(FCStkQtyAdj) - SUM(FCStkQtySaleDN - FCStkQtyCN))    AS FCStkQtyBal_SUBBCH
                            FROM TRPTPdtStkCrdTmp WITH(NOLOCK)
                            WHERE FTUsrSession <> ''
                            AND FTComName       = ".$this->db->escape($tComName)."
                            AND FTRptCode       = ".$this->db->escape($tRptCode)."
                            AND FTUsrSession    = ".$this->db->escape($tSession)."
                            GROUP BY FTBchCode
                        ) DATASUBBCH ON DATASTKCRD.FTBchCode = DATASUBBCH.FTBchCode_SUBBCH
                        WHERE FTUsrSession <> ''
                        AND FTComName       = ".$this->db->escape($tComName)."
                        AND FTRptCode       = ".$this->db->escape($tRptCode)."
                        AND FTUsrSession    = ".$this->db->escape($tSession)."
                    ) L
                    LEFT JOIN (
                        " . $tJoinFoooter . "
        ";
        if($paDataWhere['nPerPage'] != 0){
        // WHERE เงื่อนไข Page
            $tSQL   .=  "   WHERE L.RowID > $nRowIDStart AND L.RowID <= $nRowIDEnd ";
        }
        // สั่ง Order by ตามข้อมูลหลัก
        $tSQL   .=  "   ORDER BY L.FTBchCode,L.FTWahCode ,L.FTPdtCode,L.FDStkDate,L.FTRptRowSeq,L.FNRowPartID";
        $oQuery  = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aData  = $oQuery->result_array();
        } else {
            $aData  = NULL;
        }
        $aErrorList =   array(
            "nErrInvalidPage"   =>  ""
        );
        $aResualt   = array(
            "aPagination"   =>  $aPagination,
            "aRptData"      =>  $aData,
            "aError"        =>  $aErrorList
        );
        unset($nPage,$aPagination,$nRowIDStart,$nRowIDEnd,$nTotalPage,$tComName,$tRptCode,$tSession);
        unset($tJoinFoooter,$tSQL,$oQuery,$aData,$aErrorList);
        return  $aResualt;
    }

    public function FMaMRPTPagination($paDataWhere){
        $tComName       = $paDataWhere['tCompName'];
        $tRptCode       = $paDataWhere['tRptCode'];
        $tUsrSession    = $paDataWhere['tUsrSessionID'];
        $tSQL           =   "
            SELECT
                COUNT(STK.FTWahCode) AS rnCountPage
            FROM TRPTPdtStkCrdTmp STK WITH(NOLOCK)
            WHERE 1=1
            AND STK.FTComName    = '$tComName'
            AND STK.FTRptCode    = '$tRptCode'
            AND STK.FTUsrSession = '$tUsrSession'
        ";
        $oQuery         = $this->db->query($tSQL);
        $nRptAllRecord  = $oQuery->row_array()['rnCountPage'];
        $nPage          = $paDataWhere['nPage'];
        $nPerPage       = $paDataWhere['nPerPage'];
        $nPrevPage      = $nPage - 1;
        $nNextPage      = $nPage + 1;
        $nRowIDStart    = (($nPerPage * $nPage) - $nPerPage); //RowId Start
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
            "nTotalRecord"  =>  $nRptAllRecord,
            "nTotalPage"    =>  $nTotalPage,
            "nDisplayPage"  =>  $paDataWhere['nPage'],
            "nRowIDStart"   =>  $nRowIDStart,
            "nRowIDEnd"     =>  $nRowIDEnd,
            "nPrevPage"     =>  $nPrevPage,
            "nNextPage"     =>  $nNextPage
        );
        unset($tComName,$tRptCode,$tUsrSession,$tSQL,$oQuery,$nRptAllRecord,$nPage,$nPerPage);
        unset($nPrevPage,$nNextPage,$nRowIDStart,$nTotalPage,$nRowIDEnd);
        return $aRptMemberDet;
    }

    public function FMxMRPTSetPriorityGroup($ptComName, $ptRptCode, $ptUsrSession){
        $tSQLUPD    = "
            UPDATE DATAUPD
            SET
                DATAUPD.FNRowPartID     = DATASLT.PartIDPdt,
                DATAUPD.FNRowPartIDWah	= DATASLT.PartIDWah,
                DATAUPD.FNRowPartIDBch	= DATASLT.PartIDBch
            FROM TRPTPdtStkCrdTmp DATAUPD
            RIGHT JOIN (
                SELECT 
                    ROW_NUMBER() OVER(PARTITION BY FTBchCode,FTWahCode,FTPdtCode ORDER BY FTBchCode ASC,FTWahCode ASC,FTPdtCode ASC,FDStkDate ASC,FTRptRowSeq ASC) AS PartIDPdt,
                    ROW_NUMBER() OVER(PARTITION BY FTBchCode,FTWahCode ORDER BY FTBchCode ASC,FTWahCode ASC) AS PartIDWah,
                    ROW_NUMBER() OVER(PARTITION BY FTBchCode ORDER BY FTBchCode ASC) AS PartIDBch,
                    FTRptRowSeq,
                    FTBchCode,
                    FTWahCode,
                    FTPdtCode,
                    FTComName,
                    FTRptCode,
                    FTUsrSession
                FROM TRPTPdtStkCrdTmp WITH(NOLOCK)
                WHERE 1=1
                AND FTComName       = '$ptComName'
                AND FTRptCode       = '$ptRptCode'
                AND FTUsrSession    = '$ptUsrSession'
            ) DATASLT ON 1=1
            AND DATASLT.FTRptRowSeq     = DATAUPD.FTRptRowSeq
            AND DATASLT.FTBchCode       = DATAUPD.FTBchCode
            AND DATASLT.FTWahCode       = DATAUPD.FTWahCode
            AND DATASLT.FTPdtCode       = DATAUPD.FTPdtCode
            AND DATASLT.FTComName       = DATAUPD.FTComName
            AND DATASLT.FTRptCode       = DATAUPD.FTRptCode
            AND DATASLT.FTUsrSession	= DATAUPD.FTUsrSession
        ";
        $this->db->query($tSQLUPD);
        unset($tSQLUPD);
        unset($ptComName,$ptRptCode,$ptUsrSession);
    }

    //set AjdStkBal
    private function FMxMRPTAjdStkBal($ptComName, $ptRptCode, $ptUsrSession){
        // --Adjust stock balance in temp  
        $tSQL   =   "
            UPDATE STK
                SET STK.FCStkQtyBal =  STKAJB.FCStkBal
            FROM TRPTPdtStkCrdTmp STK 
            LEFT JOIN (
                SELECT STKB.* , 
                    SUM(STKB.FCStkSumTrans) OVER ( PARTITION  BY STKB.FTWahCode+STKB.FTPdtCode ORDER BY STKB.FTBchCode,STKB.FDStkDate  , STKB.FTRptRowSeq) AS FCStkBal
                FROM (
                    SELECT
                        FTBchCode,FTRptRowSeq, FTWahCode,FTPdtCode,FTStkDocNo,FDStkDate,
                        ROW_NUMBER() OVER(PARTITION by FTPdtCode ORDER BY FTWahCode,FTPdtCode,FTStkDocNo) AS FNStkRowGroupNo,
                        SUM(FCStkQtyMonEnd + FCStkQtyIn - FCStkQtyOut + FCStkQtyAdj - (FCStkQtySaleDN - FCStkQtyCN) ) AS FCStkSumTrans
                    FROM TRPTPdtStkCrdTmp
                    WHERE FTUsrSession <> ''
                    AND FTComName       = ".$this->db->escape($ptComName)."
                    AND FTRptCode       = ".$this->db->escape($ptRptCode)."
                    AND FTUsrSession    = ".$this->db->escape($ptUsrSession)."
                    GROUP BY FTBchCode,FTRptRowSeq,FTWahCode,FTPdtCode,FTStkDocNo ,FDStkDate
                ) STKB 
            ) STKAJB ON STK.FTBchCode = STKAJB.FTBchCode AND STK.FTWahCode = STKAJB.FTWahCode AND STK.FTPdtCode = STKAJB.FTPdtCode AND STK.FTStkDocNo = STKAJB.FTStkDocNo
        ";
        $this->db->query($tSQL);
        unset($tSQL);
        unset($ptComName,$ptRptCode,$ptUsrSession);
    }




}
