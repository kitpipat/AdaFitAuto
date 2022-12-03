<?php

defined('BASEPATH') or exit('No direct script access allowed');

class mRptInventoryPdtGrp extends CI_Model {
          //Functionality: Delete Temp Report
    //Parameters:  Function Parameter
    //Creator: 04/04/2019 Wasin(Yoshi)
    //Last Modified :
    //Return : Call Store Proce
    //Return Type: Array
    public function FSnMExecStoreCReport($paDataFilter) {

        // สาขา
        $tBchCodeSelect = ($paDataFilter['bBchStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tBchCodeSelect']); 
        // ร้านค้า
        $tShpCodeSelect = ($paDataFilter['bShpStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tShpCodeSelect']);
        // กลุ่มธุรกิจ
        $tMerCodeSelect = ($paDataFilter['bMerStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tMerCodeSelect']);
        $tAgnCode   = $paDataFilter['tAgnCodeSelect'];
        
        $tCallStore = "{ CALL SP_RPTxPdtBalByPdtGrp(?,?,?,?,?,?,?,?,?,?) }";

        $aDataStore = array(
            'pnLngID'       => $paDataFilter['nLangID'],
            'ptComName'     => $paDataFilter['tCompName'],
            'ptRptCode'     => $paDataFilter['tRptCode'],
            'ptUsrSession'  => $paDataFilter['tUserSession'],

            'pnFilterType'  => $paDataFilter['tTypeSelect'],
            'ptAgnCode'     => $tAgnCode,
            'ptBchL'        => $tBchCodeSelect,
            // 'ptBchF'        => $paDataFilter['tBchCodeFrom'],
            // 'ptBchT'        => $paDataFilter['tBchCodeTo'],

            // 'ptMerL'        => $tMerCodeSelect,
            // 'ptMerF'        => $paDataFilter['tMerCodeFrom'],
            // 'ptMerT'        => $paDataFilter['tMerCodeTo'],

            // 'ptShpL'        => $tShpCodeSelect,
            // 'ptShpF'        => $paDataFilter['tShpCodeFrom'],
            // 'ptShpT'        => $paDataFilter['tShpCodeTo'],

            'ptPdtGrpF'     => $paDataFilter['tPdtGrpCodeFrom'],
            'ptPdtGrpT'     => $paDataFilter['tPdtGrpCodeTo'],
        
            'FTResult'      => 0,
        );

        $oQuery = $this->db->query($tCallStore, $aDataStore);
        // print_r($this->db->last_query());

        if ($oQuery != FALSE) {
            unset($oQuery);
            return 1;
        } else {
            unset($oQuery);
            return 0;
        }
    }


     /**
     * Functionality: Call Stored Procedure
     * Parameters:  Function Parameter
     * Creator: 18/07/2019 Wasin(Yoshi)
     * Last Modified : 24/09/2019 Piya
     * Return : Status Return Call Stored Procedure
     * Return Type: Array
     */
    public function FSaMGetDataReport($paDataWhere){

        $nPage = $paDataWhere['nPage'];

        // Call Data Pagination 
        $aPagination  = $this->FMaMRPTPagination($paDataWhere);

        $nRowIDStart  = $aPagination["nRowIDStart"];
        $nRowIDEnd    = $aPagination["nRowIDEnd"];
        $nTotalPage   = $aPagination["nTotalPage"];

        $tComName     = $paDataWhere['tCompName'];
        $tRptCode     = $paDataWhere['tRptCode'];
        $tUsrSession  = $paDataWhere['tUsrSessionID']; 

        // Set Priority
        $this->FMxMRPTSetPriorityGroup($tComName, $tRptCode, $tUsrSession);

        $tSQL = "SELECT L.*,
                    ISNULL(L.FCStkQty_SubTotal,0) * ISNULL(L.FCPdtCostStd,0) AS FCSumCostStd,
                    ISNULL(L.FCStkQty_SubTotal,0) * ISNULL(L.FCPdtCostAVGEX,0) AS FCSumCostAvg
                    FROM
                    
                    (SELECT ROW_NUMBER() OVER(ORDER BY A.FTPgpChain ASC , ISNULL(A.FTAgnCode,'') ASC, A.FTPdtCode ASC , A.FTBchCode ASC, A.FTWahCode ASC) AS RowID,
                        ROW_NUMBER() OVER(PARTITION BY ISNULL(A.FTPgpChain,'') ORDER BY A.FTPgpChain ASC) AS FNRowPartChainID, 
                        ROW_NUMBER() OVER(PARTITION BY ISNULL(A.FTPgpChain,''), ISNULL(A.FTAgnCode,'') ORDER BY A.FTAgnCode ASC) AS FNRowPartAgnID, 
                        ROW_NUMBER() OVER(PARTITION BY ISNULL(A.FTPgpChain,''), ISNULL(A.FTAgnCode,'') , A.FTPdtCode ORDER BY A.FTPdtCode ASC) AS FNRowPartPdtID, 
                        ROW_NUMBER() OVER(PARTITION BY ISNULL(A.FTPgpChain,''), ISNULL(A.FTAgnCode,'') , A.FTPdtCode, A.FTBchCode ORDER BY A.FTBchCode ASC) AS FNRowPartBchID, 
                        A.*,
                        S.FNRptGroupMember,
                        S.FCStkQty_SubTotal
                    FROM TRPTPdtBalByPdtGrpTmp A WITH(NOLOCK)
                    LEFT JOIN
                    (SELECT  ISNULL(FTPgpChain,'') AS ChainGrp, 
                            ISNULL(FTAgnCode, '') AS AgnGrp,
                            FTPdtCode AS FTPdtCode,
                            COUNT(FTWahCode) AS FNRptGroupMember,
                            SUM(ISNULL(FCStkQty,0)) AS FCStkQty_SubTotal
                    FROM TRPTPdtBalByPdtGrpTmp WITH(NOLOCK)
                    WHERE 1=1
                        AND FTComName       = '$tComName'
                        AND FTRptCode       = '$tRptCode'
                        AND FTUsrSession    = '$tUsrSession'
                    GROUP BY ISNULL(FTPgpChain,''), ISNULL(FTAgnCode, ''), FTPdtCode) AS S ON ISNULL(A.FTPgpChain, '') = S.ChainGrp AND ISNULL(A.FTAgnCode, '') = S.AgnGrp AND A.FTPdtCode = S.FTPdtCode
                    WHERE 1=1
                        AND FTComName       = '$tComName'
                        AND FTRptCode       = '$tRptCode'
                        AND FTUsrSession    = '$tUsrSession'
                ) AS L

                WHERE 1=1
                        AND FTComName       = '$tComName'
                        AND FTRptCode       = '$tRptCode'
                        AND FTUsrSession    = '$tUsrSession'
                        AND L.RowID > $nRowIDStart AND L.RowID <= $nRowIDEnd
                ORDER BY L.RowID ASC, L.FTPgpChainName ASC,
                    L.FTPdtCode ASC,
                    len(L.FTWahCode) ASC 
                ";
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


    public function FMxMRPTSetPriorityGroup($ptComName, $ptRptCode, $ptUsrSession){

        $tSQL = "
            UPDATE TRPTPdtBalByPdtGrpTmp 
                SET FNRowPartID = B.PartID
            FROM( 
                SELECT 
                    ROW_NUMBER() OVER(PARTITION BY FTPgpChainName , FTPdtCode ORDER BY FTPgpChainName ASC , FTPdtCode ASC , FTWahCode ASC) AS PartID, 
                    FTRptRowSeq  
                FROM TRPTPdtBalByPdtGrpTmp TMP WITH(NOLOCK)
                WHERE TMP.FTComName     = '$ptComName' 
                AND TMP.FTRptCode       = '$ptRptCode'
                AND TMP.FTUsrSession    = '$ptUsrSession'
            ) B
            WHERE TRPTPdtBalByPdtGrpTmp.FTRptRowSeq = B.FTRptRowSeq 
            AND TRPTPdtBalByPdtGrpTmp.FTComName     = '$ptComName' 
            AND TRPTPdtBalByPdtGrpTmp.FTRptCode     = '$ptRptCode'
            AND TRPTPdtBalByPdtGrpTmp.FTUsrSession  = '$ptUsrSession' 
        ";

        $this->db->query($tSQL);
    }


    /**
     * Functionality: Call Stored Procedure
     * Parameters:  Function Parameter
     * Creator: 18/07/2019 Wasin(Yoshi)
     * Last Modified : 24/09/2019 Piya
     * Return : Status Return Call Stored Procedure
     * Return Type: Array
     */
    public function FMaMRPTPagination($paDataWhere){

        $tComName    = $paDataWhere['tCompName'];
        $tRptCode    = $paDataWhere['tRptCode'];
        $tUsrSession = $paDataWhere['tUsrSessionID'];

        $tSQL = "   
            SELECT
                TT_TMP.FTWahCode
            FROM TRPTPdtBalByPdtGrpTmp TT_TMP WITH(NOLOCK)
            WHERE TT_TMP.FTComName      = '$tComName'
            AND TT_TMP.FTRptCode        = '$tRptCode'
            AND TT_TMP.FTUsrSession     = '$tUsrSession'
        ";

        // echo '<pre>'.$tSQL; exit();s
        $oQuery = $this->db->query($tSQL);
        $nRptAllRecord = $oQuery->num_rows();
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
            "nTotalRecord" => $nRptAllRecord,
            "nTotalPage" => $nTotalPage,
            "nDisplayPage" => $paDataWhere['nPage'],
            "nRowIDStart" => $nRowIDStart,
            "nRowIDEnd" => $nRowIDEnd,
            "nPrevPage" => $nPrevPage,
            "nNextPage" => $nNextPage
        );
        unset($oQuery);
        return $aRptMemberDet;
    }

    public function FSnMGetCostType(){
        $tSesUsrAgnCode = $this->session->userdata('tSesUsrAgnCode');
        $tSesUsrAgnType = $this->session->userdata('tAgnType');

        if(isset($tSesUsrAgnCode) && !empty($tSesUsrAgnCode) && isset($tSesUsrAgnType) && $tSesUsrAgnType == 2){
            $tSQL = "
                SELECT 
                    FTCfgStaUsrValue AS FTSysStaDefValue,
                    LEFT(FTCfgStaUsrValue, 1) AS FTSysStaUsrValue
                FROM  TCNTConfigSpc
                WHERE FTSysCode = 'tCN_Cost' 
                AND FTSysKey    = 'Company'
                AND FTSysSeq    = '2'
                AND FTSysApp    = 'AP'
                AND FTAgnCode   = '$tSesUsrAgnCode'
            ";
        } else {
            $tSQL = "
                SELECT FTSysStaDefValue, LEFT(FTSysStaUsrValue, 1) AS FTSysStaUsrValue
                FROM  TSysConfig WITH(NOLOCK)
                WHERE 
                FTSysCode = 'tCN_Cost' 
                AND FTSysKey = 'Company' 
                AND FTSysSeq = '2'
                AND FTSysApp = 'AP'
            ";
        }

        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $oList = $oQuery->result();
            if ($oList[0]->FTSysStaUsrValue != '') {
                $aResult = array(
                    'raItems' => $oList[0]->FTSysStaUsrValue,
                    'rtCode' => '1',
                    'rtDesc' => 'success',
                );
            }else {
                $aResult = array(
                    'raItems' => $oList[0]->FTSysStaDefValue,
                    'rtCode' => '1',
                    'rtDesc' => 'success',
                );
            }
        } else {
            //No Data
            $aResult = array(
                'raItems' => 0,
                'rtCode' => '800',
                'rtDesc' => 'data not found'
            );
        }
        $jResult = json_encode($aResult);
        $aResult = json_decode($jResult, true);

        return $aResult;

    }

  
}














