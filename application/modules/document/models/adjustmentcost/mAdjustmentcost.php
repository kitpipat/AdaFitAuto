<?php
defined('BASEPATH') or exit('No direct script access allowed');

class mAdjustmentcost extends CI_Model{

    // Functionality: Data List
    // Parameters: function parameters
    // Creator:  03/03/2021 Sooksanti(Nont)
    // Last Modified: -
    // Return: Data Array
    // Return Type: Array
    public function FSaMADCGetDataTable($paDataCondition){
        $aDataUserInfo      = $this->session->userdata("tSesUsrInfo");
        $aRowLen            = FCNaHCallLenData($paDataCondition['nRow'], $paDataCondition['nPage']);
        $nLngID             = $paDataCondition['FNLngID'];
        $aAdvanceSearch     = $paDataCondition['aAdvanceSearch'];
        @$tSearchList       = $aAdvanceSearch['tSearchAll'];
        // Advance Search
        $tSearchBchCodeFrom = $aAdvanceSearch['tSearchBchCodeFrom'];
        $tSearchBchCodeTo   = $aAdvanceSearch['tSearchBchCodeTo'];
        $tSearchDocDateFrom = $aAdvanceSearch['tSearchDocDateFrom'];
        $tSearchDocDateTo   = $aAdvanceSearch['tSearchDocDateTo'];
        $tSearchStaDoc      = $aAdvanceSearch['tSearchStaDoc'];
        $tUsrBchCode        = $this->session->userdata("tSesUsrBchCodeMulti");

        /** ค้นหารหัสเอกสาร,ชือสาขา,วันที่เอกสาร */
        $tWhereSearchAll    = "";
        if (@$tSearchList != '') {
            $tWhereSearchAll    = "
                AND ((ADC.FTXchDocNo LIKE '%".$this->db->escape_like_str($tSearchList)."%')
                OR (BCHL.FTBchName LIKE '%".$this->db->escape_like_str($tSearchList)."%')
                OR (CONVERT(CHAR(10),ADC.FDXchDocDate,103) LIKE '%".$this->db->escape_like_str($tSearchList)."%'))
            ";
        }

        // Check User Level Branch HQ OR Bch Or Shop
        $tUserLevel = $this->session->userdata("tSesUsrLevel");
        $tWhereBch  = "";
        $tWhereShp  = "";
        if (isset($tUserLevel) && !empty($tUserLevel) && $tUserLevel == "BCH") {
            // Check User Level BCH
            $tWhereBch  = " AND ADC.FTBchCode IN (" . $tUsrBchCode . ") ";
        }

        $tWhereBchFrmTo     = "";
        if ($this->session->userdata("tSesUsrLevel") == "HQ" || $this->session->userdata("nSesUsrBchCount") >1) {
            /* ค้นหาจากสาขา - ถึงสาขา */
            $tSearchBchCodeFrom = $aAdvanceSearch['tSearchBchCodeFrom'];
            $tSearchBchCodeTo   = $aAdvanceSearch['tSearchBchCodeTo'];
            if (!empty($tSearchBchCodeFrom) && !empty($tSearchBchCodeTo)) {
                $tWhereBchFrmTo = " AND ((ADC.FTBchCode BETWEEN ".$this->db->escape($tSearchBchCodeFrom)." AND ".$this->db->escape($tSearchBchCodeTo).") OR (ADC.FTBchCode BETWEEN ".$this->db->escape($tSearchBchCodeTo)." AND ".$this->db->escape($tSearchBchCodeFrom)."))";
            }
        } else {
            $tWhereBchFrmTo .= " AND ADC.FTBchCode IN (" . $tUsrBchCode . ")";
        }

        /** ค้นหาจากวันที่ - ถึงวันที่ */
        $tSearchDocDateFrom = $aAdvanceSearch['tSearchDocDateFrom'];
        $tSearchDocDateTo   = $aAdvanceSearch['tSearchDocDateTo'];
        $tWhereDateFrmTo    = "";
        if (!empty($tSearchDocDateFrom) && !empty($tSearchDocDateTo)) {
            $tWhereDateFrmTo    = " AND ((ADC.FDXchDocDate BETWEEN CONVERT(datetime,".$this->db->escape($tSearchDocDateFrom." 00:00:00").") AND CONVERT(datetime,".$this->db->escape($tSearchDocDateTo." 23:59:59").")) OR (ADC.FDXchDocDate BETWEEN CONVERT(datetime,".$this->db->escape($tSearchDocDateTo." 23:00:00").") AND CONVERT(datetime,".$this->db->escape($tSearchDocDateFrom." 00:00:00").")))";
        }

        /** ค้นหาสถานะเอกสาร */
        $tSearchStaDoc = $aAdvanceSearch['tSearchStaDoc'];
        $tWhereStaDoc   = "";
        if (!empty($tSearchStaDoc) && ($tSearchStaDoc != "0")) {
            if ($tSearchStaDoc == 3) {
                $tWhereStaDoc   .= " AND ADC.FTXchStaDoc = ".$this->db->escape($tSearchStaDoc)." ";
            } else if ($tSearchStaDoc == 2) {
                $tWhereStaDoc   .= " AND ISNULL(ADC.FTXchStaApv,'') = '' AND ADC.FTXchStaDoc != '3'";
            } else if ($tSearchStaDoc == 1) {
                $tWhereStaDoc   .= " AND ADC.FTXchStaApv = ".$this->db->escape($tSearchStaDoc)." ";
            }
        }

        $tSQL   =   "
            SELECT c.*
                FROM( SELECT ROW_NUMBER() OVER(ORDER BY FDCreateOn DESC, FTXchDocNo DESC) AS FNRowID,*
                    FROM (
                        SELECT HD.* FROM (
                            SELECT  DISTINCT
                                ADC.FTBchCode,
                                BCHL.FTBchName,
                                ADC.FTXchDocNo,
                                CONVERT(CHAR(10),ADC.FDXchDocDate,103)   AS FDXchDocDate,
                                CONVERT(CHAR(5), ADC.FTXchDocTime, 108)  AS FTXchDocTime,
                                ADC.FTXchStaDoc,
                                ADC.FTXchStaApv,
                                ADC.FTXchStaPrcDoc,
                                ADC.FTCreateBy,
                                ADC.FDCreateOn,
                                USRL.FTUsrName  AS FTCreateByName,
                                ADC.FTXchApvCode,
                                USRLAPV.FTUsrName   AS FTXchApvName,
                                ADC.FNXchDocType
                            FROM [TCNTPdtAdjCostHD] ADC WITH (NOLOCK)
                            LEFT JOIN TCNMBranch_L  BCHL    WITH (NOLOCK) ON ADC.FTBchCode      = BCHL.FTBchCode    AND BCHL.FNLngID    = ".$this->db->escape($nLngID)."
                            LEFT JOIN TCNMUser_L    USRL    WITH (NOLOCK) ON ADC.FTCreateBy     = USRL.FTUsrCode    AND USRL.FNLngID    = ".$this->db->escape($nLngID)."
                            LEFT JOIN TCNMUser_L    USRLAPV WITH (NOLOCK) ON ADC.FTXchApvCode   = USRLAPV.FTUsrCode AND USRLAPV.FNLngID = ".$this->db->escape($nLngID)."
                            WHERE ADC.FDCreateOn <> ''
                            " . $tWhereSearchAll . "
                            " . $tWhereBch . "
                            " . $tWhereBchFrmTo . "
                            " . $tWhereDateFrmTo . "
                            " . $tWhereStaDoc . "
                        ) HD
                        INNER JOIN (SELECT DISTINCT FTXchDocNo  FROM TCNTPdtAdjCostDT)DT
                        ON HD.FTXchDocNo = DT.FTXchDocNo
                ) Base) AS c WHERE c.FNRowID > ".$this->db->escape($aRowLen[0])." AND c.FNRowID <= ".$this->db->escape($aRowLen[1])."
        ";
        $oQuery = $this->db->query($tSQL);
        //echo $this->db->last_query();
        if ($oQuery->num_rows() > 0) {
            $aDataList  = $oQuery->result_array();
            $aFoundRow  = $this->FSnMADCGetPageAll($paDataCondition);
            $nFoundRow  = ($aFoundRow['rtCode'] == '1') ? $aFoundRow['rtCountData'] : 0;
            $nPageAll   = ceil($nFoundRow / $paDataCondition['nRow']);
            $aResult    = array(
                'raItems'       => $aDataList,
                'rnAllRow'      => $nFoundRow,
                'rnCurrentPage' => $paDataCondition['nPage'],
                'rnAllPage'     => $nPageAll,
                'rtCode'        => '1',
                'rtDesc'        => 'success',
            );
        } else {
            $aResult    = array(
                'rnAllRow'      => 0,
                'rnCurrentPage' => $paDataCondition['nPage'],
                "rnAllPage"     => 0,
                'rtCode'        => '800',
                'rtDesc'        => 'data not found',
            );
        }
        unset($oQuery);
        unset($oDataList);
        unset($aFoundRow);
        unset($nFoundRow);
        unset($nPageAll);
        return $aResult;
    }

    // Functionality: Count Data All HD
    // Parameters: function parameters
    // Creator:  03/03/2021 Sooksanti
    // Last Modified: -
    // Return: Data Array
    // Return Type: Array
    public function FSnMADCGetPageAll($paDataCondition){
        $aDataUserInfo      = $this->session->userdata("tSesUsrInfo");
        $aRowLen            = FCNaHCallLenData($paDataCondition['nRow'], $paDataCondition['nPage']);
        $nLngID             = $paDataCondition['FNLngID'];
        $aAdvanceSearch     = $paDataCondition['aAdvanceSearch'];
        @$tSearchList       = $aAdvanceSearch['tSearchAll'];
        // Advance Search
        $tSearchBchCodeFrom = $aAdvanceSearch['tSearchBchCodeFrom'];
        $tSearchBchCodeTo   = $aAdvanceSearch['tSearchBchCodeTo'];
        $tSearchDocDateFrom = $aAdvanceSearch['tSearchDocDateFrom'];
        $tSearchDocDateTo   = $aAdvanceSearch['tSearchDocDateTo'];
        $tSearchStaDoc      = $aAdvanceSearch['tSearchStaDoc'];
        $tUsrBchCode        = $this->session->userdata("tSesUsrBchCodeMulti");
        /** ค้นหารหัสเอกสาร,ชือสาขา,วันที่เอกสาร */
        $tWhereSearchAll    = "";
        if (@$tSearchList != '') {
            $tWhereSearchAll    =  "
                AND ((ADC.FTXchDocNo    LIKE '%".$this->db->escape_like_str($tSearchList)."%')
                OR (BCHL.FTBchName      LIKE '%".$this->db->escape_like_str($tSearchList)."%')
                OR (CONVERT(CHAR(10),ADC.FDXchDocDate,103) LIKE '%".$this->db->escape_like_str($tSearchList)."%'))";
        }

        // Check User Level Branch HQ OR Bch Or Shop
        $tUserLevel = $this->session->userdata("tSesUsrLevel");
        $tWhereBch  = "";
        $tWhereShp  = "";
        if (isset($tUserLevel) && !empty($tUserLevel) && $tUserLevel == "BCH") {
            // Check User Level BCH
            $tWhereBch  = " AND ADC.FTBchCode IN (" . $tUsrBchCode . ") ";
        }

        $tWhereBchFrmTo     = "";
        if ($this->session->userdata("tSesUsrLevel") == "HQ" || $this->session->userdata("nSesUsrBchCount") >1) {
            /* ค้นหาจากสาขา - ถึงสาขา */
            $tSearchBchCodeFrom = $aAdvanceSearch['tSearchBchCodeFrom'];
            $tSearchBchCodeTo   = $aAdvanceSearch['tSearchBchCodeTo'];
            if (!empty($tSearchBchCodeFrom) && !empty($tSearchBchCodeTo)) {
                $tWhereBchFrmTo = " AND ((ADC.FTBchCode BETWEEN ".$this->db->escape($tSearchBchCodeFrom)." AND ".$this->db->escape($tSearchBchCodeTo).") OR (ADC.FTBchCode BETWEEN ".$this->db->escape($tSearchBchCodeTo)." AND ".$this->db->escape($tSearchBchCodeFrom)."))";            }
        } else {
            $tWhereBchFrmTo .= " AND ADC.FTBchCode IN (" . $tUsrBchCode . ")";
        }

        /** ค้นหาจากวันที่ - ถึงวันที่ */
        $tSearchDocDateFrom = $aAdvanceSearch['tSearchDocDateFrom'];
        $tSearchDocDateTo   = $aAdvanceSearch['tSearchDocDateTo'];
        $tWhereDateFrmTo    = "";
        if (!empty($tSearchDocDateFrom) && !empty($tSearchDocDateTo)) {
            $tWhereDateFrmTo = " AND ((ADC.FDXchDocDate BETWEEN CONVERT(datetime,".$this->db->escape($tSearchDocDateFrom." 00:00:00").") AND CONVERT(datetime,".$this->db->escape($tSearchDocDateTo." 23:59:59").")) OR (ADC.FDXchDocDate BETWEEN CONVERT(datetime,".$this->db->escape($tSearchDocDateTo." 23:00:00").") AND CONVERT(datetime,".$this->db->escape($tSearchDocDateFrom." 00:00:00").")))";
        }

        /** ค้นหาสถานะเอกสาร */
        $tSearchStaDoc  = $aAdvanceSearch['tSearchStaDoc'];
        $tWhereStaDoc   = "";
        if (!empty($tSearchStaDoc) && ($tSearchStaDoc != "0")) {
            if ($tSearchStaDoc == 3) {
                $tWhereStaDoc .= " AND ADC.FTXchStaDoc  = ".$this->db->escape($tSearchStaDoc)." ";
            } else if ($tSearchStaDoc == 2) {
                $tWhereStaDoc .= " AND ISNULL(ADC.FTXchStaApv,'') = '' AND ADC.FTXchStaDoc != '3'";
            } else if ($tSearchStaDoc == 1) {
                $tWhereStaDoc .= " AND ADC.FTXchStaApv  = ".$this->db->escape($tSearchStaDoc)." ";
            }
        }

        $tSQL   =   "
            SELECT COUNT (ADC.FTXchDocNo) AS counts
            FROM TCNTPdtAdjCostHD    ADC WITH (NOLOCK)
            LEFT JOIN TCNMBranch_L  BCHL WITH (NOLOCK) ON ADC.FTBchCode = BCHL.FTBchCode AND BCHL.FNLngID   = ".$this->db->escape($nLngID)."
            INNER JOIN(
                SELECT DISTINCT
                FTXchDocNo
                FROM TCNTPdtAdjCostDT
            ) DT ON ADC.FTXchDocNo = DT.FTXchDocNo
            WHERE 1=1
            " . $tWhereSearchAll . "
            " . $tWhereBch . "
            " . $tWhereShp . "
            " . $tWhereBchFrmTo . "
            " . $tWhereDateFrmTo . "
            " . $tWhereStaDoc . "
        ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aDetail        = $oQuery->row_array();
            $aDataReturn    =  array(
                'rtCountData'   => $aDetail['counts'],
                'rtCode'        => '1',
                'rtDesc'        => 'success',
            );
        } else {
            $aDataReturn    =  array(
                'rtCode'        => '800',
                'rtDesc'        => 'Data Not Found',
            );
        }
        unset($oQuery);
        unset($aDetail);
        return $aDataReturn;
    }

    // Functionality : Function Get Pdt From Doc
    // Parameters : Function Parameter
    // Creator : 25/02/2021
    // Last Modified :
    // Return : array
    // Return Type : array
    public function FSaMADCGetPdtFromDoc($paData){
        $nLngID         = $paData['FNLngID'];
        $tTable         = $paData['tTable'];
        $tDocNo         = $paData['tDocNo'];
        $tPdtCodeDup    = $paData['tPdtCodeDup'];
        $aPdtCodeDup    = (explode(",",$tPdtCodeDup));
        if($tTable == 'TCNTPdtTwiDT'){
            $tPdtField      = 'TCNTPdtTwiDT.FTXtdPdtName';
            $tDocNoField    = 'TCNTPdtTwiDT.FTXthDocNo';
            $tFactor        = 'TCNTPdtTwiDT.FCXtdFactor';
            $tBarCode       = 'TCNTPdtTwiDT.FTXtdBarCode';
        }else{
            $tPdtField      = 'TAPTPiDT.FTXpdPdtName';
            $tDocNoField    = 'TAPTPiDT.FTXphDocNo';
            $tFactor        = 'TAPTPiDT.FCXpdFactor';
            $tBarCode       = 'TAPTPiDT.FTXpdBarCode';
        }

        $this->db->select('"'.$tTable.'".FTPdtCode,"'.$tPdtField.'" AS FTPdtName,"'.$tTable.'".FTPunName,TCNMPdtCostAvg.FCPdtCostEx,"'.$tTable.'".FTPunCode,"'.$tFactor.'" AS FCXcdFactor,"'.$tBarCode.'" AS FTXcdBarScan,0 AS FCXcdDiff,0 AS FCXcdCostNew');
        $this->db->from($tTable);
        $this->db->join('TCNMPdtCostAvg', 'TCNMPdtCostAvg.FTPdtCode = "'.$tTable.'".FTPdtCode','left');
        if($tPdtCodeDup != ''){
            $this->db->where_not_in('"'.$tTable.'".FTPdtCode',$aPdtCodeDup);
        }
        $this->db->where('"'.$tDocNoField.'"', $tDocNo);
        $oQuery = $this->db->get();
        if ($oQuery->num_rows() > 0) {
            $oList      = $oQuery->result_array();
            $aResult    = array(
                'raItems'   => $oList,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        } else {
            $aResult    = array(
                'rtCode'    => '99',
                'rtDesc'    => 'data not found',
            );
        }
        unset($nLngID);
        unset($tSQL);
        unset($oQuery);
        unset($oList);
        return $aResult;
    }

        // Functionality : Function Get Pdt From Doc
    // Parameters : Function Parameter
    // Creator : 25/02/2021
    // Last Modified :
    // Return : array
    // Return Type : array
    public function FSaMADCGetPdtFromPdtCode($paData){
        $tUserLevel     = $this->session->userdata("tSesUsrLevel");
        $tAgnCode       = $this->session->userdata("tSesUsrAgnCode");
        $tAgnType       = $this->session->userdata('tAgnType');
        $nLngID         = $paData['FNLngID'];
        $tPdtCode       = $paData['FTPdtCode'];
        $FTBarCode      = $paData['FTBarCode'];
        $tBchCode       = $paData['tBchCode'];

        if(isset($tAgnCode) && !empty($tAgnCode) && isset($tAgnType) && $tAgnType == 2){
            $tSQL   = "
                SELECT * FROM(
                    SELECT
                        ROW_NUMBER() OVER(PARTITION BY PDT.FTPdtCode ORDER BY PDT.FTPdtCode DESC) FNRowID,
                        RANK() OVER(PARTITION BY PDT.FTPdtCode ORDER BY PPCZ.FCPdtUnitFact ASC) FNUnitFactRank,
                        PDT.FTPdtCode,
                        PDTL.FTPdtName,
                        PUNL.FTPunName,
                        COST.FCPdtCostStd,
                        COST.FCPdtCostEx,
                        PPCZ.FTPunCode,
                        PPCZ.FCPdtUnitFact AS FCXcdFactor,
                        PBAR.FTBarCode AS FTXcdBarScan,
                        '' AS FCXcdDiff,
                        '' AS FCXcdCostNew,
                        ISNULL(PBAR.FTBarCode, PDT.FTPdtCode) AS FTBarCode
                    FROM TCNMPdt PDT WITH(NOLOCK)
                    LEFT JOIN TCNMPdt_L         PDTL    WITH(NOLOCK)    ON PDT.FTPdtCode    = PDTL.FTPdtCode    AND PDTL.FNLngID    = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMPdtCostAvg    COST    WITH(NOLOCK)    ON PDT.FTPdtCode    = COST.FTPdtCode    AND COST.FTAgnCOde  = ".$this->db->escape($tAgnCode)."
                    LEFT JOIN TCNMPdtPackSize   PPCZ    WITH(NOLOCK)    ON PDT.FTPdtCode    = PPCZ.FTPdtCode
                    LEFT JOIN TCNMPdtUnit_L     PUNL    WITH(NOLOCK)    ON PPCZ.FTPunCode   = PUNL.FTPunCode    AND PUNL.FNLngID    = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMPdtBar        PBAR    WITH (NOLOCK)   ON PDT.FTPdtCode    = PBAR.FTPdtCode    AND PPCZ.FTPunCode  = PBAR.FTPunCode
                    LEFT JOIN TCNMPdtSpcBch     SPC     WITH (NOLOCK)   ON PDT.FTPdtCode    = SPC.FTPdtCode
                    WHERE PDT.FDCreateOn <> '' AND PDT.FTPdtCode = ".$this->db->escape($tPdtCode)." AND PBAR.FTBarCode = ".$this->db->escape($FTBarCode)."
            ";
        } else {
            $tSQL   = "
                SELECT * FROM(
                    SELECT
                        ROW_NUMBER() OVER(PARTITION BY PDT.FTPdtCode ORDER BY PDT.FTPdtCode DESC) FNRowID,
                        RANK() OVER(PARTITION BY PDT.FTPdtCode ORDER BY PPCZ.FCPdtUnitFact ASC) FNUnitFactRank,
                        PDT.FTPdtCode,
                        PDTL.FTPdtName,
                        PUNL.FTPunName,
                        PDT.FCPdtCostStd,
                        COST.FCPdtCostEx,
                        PPCZ.FTPunCode,
                        PPCZ.FCPdtUnitFact AS FCXcdFactor,
                        PBAR.FTBarCode AS FTXcdBarScan,
                        '' AS FCXcdDiff,
                        '' AS FCXcdCostNew,
                        ISNULL(PBAR.FTBarCode, PDT.FTPdtCode) AS FTBarCode
                    FROM TCNMPdt PDT WITH(NOLOCK)
                    LEFT JOIN TCNMPdt_L         PDTL    WITH(NOLOCK)    ON PDT.FTPdtCode    = PDTL.FTPdtCode AND PDTL.FNLngID   = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMPdtCostAvg    COST    WITH(NOLOCK)    ON PDT.FTPdtCode    = COST.FTPdtCode AND COST.FTAgnCOde = ''
                    LEFT JOIN TCNMPdtPackSize   PPCZ    WITH(NOLOCK)    ON PDT.FTPdtCode    = PPCZ.FTPdtCode
                    LEFT JOIN TCNMPdtUnit_L     PUNL    WITH(NOLOCK)    ON PPCZ.FTPunCode   = PUNL.FTPunCode AND PUNL.FNLngID   = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMPdtBar        PBAR    WITH (NOLOCK)   ON PDT.FTPdtCode    = PBAR.FTPdtCode AND PPCZ.FTPunCode = PBAR.FTPunCode
                    LEFT JOIN TCNMPdtSpcBch     SPC     WITH (NOLOCK)   ON PDT.FTPdtCode    = SPC.FTPdtCode
                    WHERE PDT.FDCreateOn <> '' AND PDT.FTPdtCode  = ".$this->db->escape($tPdtCode)." AND PBAR.FTBarCode = ".$this->db->escape($FTBarCode)."
            ";
        }

        if($tUserLevel != 'HQ'){
            $tSQL   .="
                AND ((SPC.FTAgnCode = ".$this->db->escape($tAgnCode).")	OR SPC.FTBchCode = ".$this->db->escape($tBchCode)."
                OR (ISNULL(SPC.FTBchCode,'') = ''   AND SPC.FTAgnCode = ".$this->db->escape($tAgnCode).")
                OR ISNULL(SPC.FTAgnCode,'') = '' )
            ";
        }
        $tSQL .= ") A";


        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $oList      = $oQuery->result_array();
            $aResult    = array(
                'raItems'   => $oList,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        } else {
            $aResult    = array(
                'rtCode'    => '99',
                'rtDesc'    => 'data not found',
            );
        }
        unset($nLngID);
        unset($tSQL);
        unset($oQuery);
        unset($oList);
        return $aResult;
    }



    //Functionality : Get HD
    //Parameters : function parameters
    //Creator : 02/03/2021 Sooksanti
    //Last Modified : -
    //Return : data
    //Return Type : Array
    public function FSaMADCGetHD($paData){
        $tXchDocNo      = $paData['FTXchDocNo'];
        $tXchDocType    = $paData['FNXchDocType'];
        $nLngID         = $paData['FNLngID'];
        $tSQL           = "
            SELECT ADC.FTBchCode,
                BCHLDOC.FTBchName,
                ADC.FTXchDocNo,
                ADC.FNXchDocType,
                CONVERT(CHAR(10), ADC.FDXchDocDate, 121) AS FDXchDocDate,
                CONVERT(CHAR(5), ADC.FTXchDocTime, 108) AS FTXchDocTime,
                CONVERT(CHAR(10), ADC.FDXchAffect, 121) AS FDXchAffect,
                ADC.FTDptCode,
                DPTL.FTDptName,
                ADC.FTXchRefInt,
                CONVERT(CHAR(10), ADC.FDXchRefIntDate, 121) AS FDXchRefIntDate,
                ADC.FTXchRmk,
                ADC.FTXchStaDoc,
                ADC.FTXchStaApv,
                ADC.FTCreateBy,
                USRLCREATE.FTUsrName AS FTUsrNameCreate,
                ADC.FTXchApvCode,
                USRAPV.FTUsrName AS FTUsrNameApv,
                ADC.FTXchStaPrcDoc
            FROM TCNTPdtAdjCostHD ADC
            LEFT JOIN TCNMBranch_L BCHLDOC WITH(NOLOCK) ON ADC.FTBchCode = BCHLDOC.FTBchCode AND BCHLDOC.FNLngID = ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMBranch_L BCHLTO WITH(NOLOCK) ON ADC.FTXchDocNo = BCHLTO.FTBchCode AND BCHLTO.FNLngID = ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMUser_L USRLKEY WITH(NOLOCK) ON ADC.FTUsrCode = USRLKEY.FTUsrCode AND USRLKEY.FNLngID = ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMUser_L USRLCREATE WITH(NOLOCK) ON ADC.FTCreateBy = USRLCREATE.FTUsrCode AND USRLCREATE.FNLngID = ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMUser_L USRAPV WITH(NOLOCK) ON ADC.FTXchApvCode = USRAPV.FTUsrCode AND USRAPV.FNLngID = ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMUsrDepart_L DPTL WITH(NOLOCK) ON ADC.FTDptCode = DPTL.FTDptCode AND DPTL.FNLngID = ".$this->db->escape($nLngID)."
            WHERE ADC.FDCreateOn <> ''
        ";
        if ($tXchDocType != "") {
            $tSQL   .= "AND ADC.FNXchDocType = ".$this->db->escape($tXchDocType)." ";
        }
        if ($tXchDocNo != "") {
            $tSQL   .= "AND ADC.FTXchDocNo = ".$this->db->escape($tXchDocNo)." ";
        }
        $oQuery     = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $oDetail = $oQuery->result();
            $aResult = array(
                'raItems'   => $oDetail[0],
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        } else {
            // Not Found
            $aResult = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found.',
            );
        }
        $jResult = json_encode($aResult);
        $aResult = json_decode($jResult, true);
        return $aResult;
    }

    // Functionality : Function Get Pdt From Filter
    // Parameters : Function Parameter
    // Creator : 25/02/2021
    // Last Modified :
    // Return : array
    // Return Type : array
    public function FSaMADCGetPdtFromFilter($paData){
        $tUserLevel     = $this->session->userdata("tSesUsrLevel");
        $tAgnCode       = $this->session->userdata("tSesUsrAgnCode");
        $tAgnType       = $this->session->userdata('tAgnType');
        $nLngID         = $paData['FNLngID'];
        $tPdtCodeFrom   = $paData['tPdtCodeFrom'];
        $tPdtCodeTo     = $paData['tPdtCodeTo'];
        $tBarCodeFrom   = $paData['tBarCodeFrom'];
        $tBarCodeCodeTo = $paData['tBarCodeCodeTo'];
        $tPdtCodeDup    = FCNtAddSingleQuote($paData['tPdtCodeDup']);
        $tBchCode       = $paData['tBchCode'];

        if(isset($tAgnCode) && !empty($tAgnCode) && isset($tAgnType) && $tAgnType == 2){
            $tSQL   = "
                SELECT * FROM(
                    SELECT
                        ROW_NUMBER() OVER(PARTITION BY PDT.FTPdtCode ORDER BY PDT.FTPdtCode DESC) FNRowID,
                        RANK() OVER(PARTITION BY PDT.FTPdtCode ORDER BY PPCZ.FCPdtUnitFact ASC) FNUnitFactRank,
                        PDT.FTPdtCode,
                        PDTL.FTPdtName,
                        PUNL.FTPunName,
                        COST.FCPdtCostStd,
                        COST.FCPdtCostEx,
                        PPCZ.FTPunCode,
                        PPCZ.FCPdtUnitFact AS FCXcdFactor,
                        PBAR.FTBarCode AS FTXcdBarScan,
                        '' AS FCXcdDiff,
                        '' AS FCXcdCostNew,
                        ISNULL(PBAR.FTBarCode, PDT.FTPdtCode) AS FTBarCode
                    FROM TCNMPdt PDT WITH(NOLOCK)
                    LEFT JOIN TCNMPdt_L PDTL WITH(NOLOCK) ON PDT.FTPdtCode = PDTL.FTPdtCode AND PDTL.FNLngID = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMPdtCostAvg COST WITH(NOLOCK) ON PDT.FTPdtCode = COST.FTPdtCode
                    LEFT JOIN TCNMPdtPackSize PPCZ WITH(NOLOCK) ON PDT.FTPdtCode = PPCZ.FTPdtCode
                    LEFT JOIN TCNMPdtUnit_L PUNL WITH(NOLOCK) ON PPCZ.FTPunCode = PUNL.FTPunCode AND PUNL.FNLngID = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMPdtBar PBAR WITH (NOLOCK)  ON PDT.FTPdtCode = PBAR.FTPdtCode AND PPCZ.FTPunCode = PBAR.FTPunCode
                    LEFT JOIN TCNMPdtSpcBch SPC WITH (NOLOCK) ON PDT.FTPdtCode = SPC.FTPdtCode
                    WHERE PDT.FDCreateOn <> ''
            ";
        } else {
            $tSQL   = "
                SELECT * FROM(
                    SELECT
                        ROW_NUMBER() OVER(PARTITION BY PDT.FTPdtCode ORDER BY PDT.FTPdtCode DESC) FNRowID,
                        RANK() OVER(PARTITION BY PDT.FTPdtCode ORDER BY PPCZ.FCPdtUnitFact ASC) FNUnitFactRank,
                        PDT.FTPdtCode,
                        PDTL.FTPdtName,
                        PUNL.FTPunName,
                        PDT.FCPdtCostStd,
                        COST.FCPdtCostEx,
                        PPCZ.FTPunCode,
                        PPCZ.FCPdtUnitFact AS FCXcdFactor,
                        PBAR.FTBarCode AS FTXcdBarScan,
                        '' AS FCXcdDiff,
                        '' AS FCXcdCostNew,
                        ISNULL(PBAR.FTBarCode, PDT.FTPdtCode) AS FTBarCode
                    FROM TCNMPdt PDT WITH(NOLOCK)
                    LEFT JOIN TCNMPdt_L PDTL WITH(NOLOCK) ON PDT.FTPdtCode = PDTL.FTPdtCode AND PDTL.FNLngID = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMPdtCostAvg COST WITH(NOLOCK) ON PDT.FTPdtCode = COST.FTPdtCode
                    LEFT JOIN TCNMPdtPackSize PPCZ WITH(NOLOCK) ON PDT.FTPdtCode = PPCZ.FTPdtCode
                    LEFT JOIN TCNMPdtUnit_L PUNL WITH(NOLOCK) ON PPCZ.FTPunCode = PUNL.FTPunCode AND PUNL.FNLngID = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMPdtBar PBAR WITH (NOLOCK)  ON PDT.FTPdtCode = PBAR.FTPdtCode AND PPCZ.FTPunCode = PBAR.FTPunCode
                    LEFT JOIN TCNMPdtSpcBch SPC WITH (NOLOCK) ON PDT.FTPdtCode = SPC.FTPdtCode
                    WHERE PDT.FDCreateOn <> ''
            ";
        }

        if($tPdtCodeDup != ''){
            $tSQL   .= "AND PDT.FTPdtCode NOT IN($tPdtCodeDup)";
        }
        $tSQL       .= "
            AND ((PDT.FTPdtCode BETWEEN ".$this->db->escape($tPdtCodeFrom)."    AND ".$this->db->escape($tPdtCodeTo)."
            OR PDT.FTPdtCode    BETWEEN ".$this->db->escape($tPdtCodeTo)."      AND ".$this->db->escape($tPdtCodeFrom).")
            OR (PBAR.FTBarCode  BETWEEN ".$this->db->escape($tBarCodeFrom)."    AND ".$this->db->escape($tBarCodeCodeTo)."
            OR PBAR.FTBarCode   BETWEEN ".$this->db->escape($tBarCodeCodeTo)."  AND ".$this->db->escape($tBarCodeFrom)."))
        ";
        if($tUserLevel != 'HQ'){
            $tSQL   .="
                AND ((SPC.FTAgnCode = '$tAgnCode')	OR SPC.FTBchCode = '$tBchCode'
                OR (ISNULL(SPC.FTBchCode,'') = '' AND SPC.FTAgnCode = '$tAgnCode')
                OR ISNULL(SPC.FTAgnCode,'') = '' )
            ";
        }
        $tSQL  .= ") A WHERE A.FNRowID = 1 AND FNUnitFactRank = 1 ORDER BY A.FTPdtCode DESC";
        $oQuery = $this->db->query($tSQL);
        //echo $this->db->last_query();
        if ($oQuery->num_rows() > 0) {
            $oList      = $oQuery->result_array();
            $aResult    = array(
                'raItems'   => $oList,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        } else {
            $aResult    = array(
                'rtCode'    => '99',
                'rtDesc'    => 'data not found',
            );
        }
        unset($nLngID);
        unset($tSQL);
        unset($oQuery);
        unset($oList);
        return $aResult;
    }


    // Functionality : Function Get Pdt From Filter
    // Parameters : Function Parameter
    // Creator : 25/02/2021
    // Last Modified :
    // Return : array
    // Return Type : array
    public function FSaMADCGetPdtFromImportExcel($paData){
        $tAgnCode       = $this->session->userdata("tSesUsrAgnCode");
        $tAgnType       = $this->session->userdata('tAgnType');
        $nLngID         = $paData['FNLngID'];
        $tPdtCodeDup    = FCNtAddSingleQuote($paData['tPdtCodeDup']);
        $tFTSessionID   = $paData['FTSessionID'];
        if(isset($tAgnCode) && !empty($tAgnCode) && isset($tAgnType) && $tAgnType == 2){
            $tSQL   = "
                SELECT * FROM (
                    SELECT
                        ROW_NUMBER() OVER(PARTITION BY PDTL.FTPdtCode ORDER BY PDTL.FTPdtCode DESC) FNRowID,
                        RANK() OVER(PARTITION BY PDTL.FTPdtCode ORDER BY PPCZ.FCPdtUnitFact ASC) FNUnitFactRank,
                        DOCTMP.FTPdtCode,
                        PDTL.FTPdtName,
                        PUNL.FTPunName,
                        COST.FCPdtCostEx,
                        PPCZ.FTPunCode,
                        COST.FCPdtCostStd,
                        PPCZ.FCPdtUnitFact AS FCXcdFactor,
                        PBAR.FTBarCode AS FTXcdBarScan,
                        (DOCTMP.FCXtdCostEx - COST.FCPdtCostStd) AS FCXcdDiff,
                        DOCTMP.FCXtdCostEx AS FCXcdCostNew,
                        DOCTMP.FTTmpStatus,
                        DOCTMP.FTTmpRemark
                    FROM TCNTDocDTTmp DOCTMP
                    LEFT JOIN TCNMPdt_L PDTL WITH(NOLOCK) ON DOCTMP.FTPdtCode = PDTL.FTPdtCode AND PDTL.FNLngID     = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMPdt PDT WITH ( NOLOCK ) ON DOCTMP.FTPdtCode = PDT.FTPdtCode 
                    LEFT JOIN TCNMPdtCostAvg COST WITH(NOLOCK) ON DOCTMP.FTPdtCode = COST.FTPdtCode
                    LEFT JOIN TCNMPdtPackSize PPCZ WITH(NOLOCK) ON DOCTMP.FTPdtCode = PPCZ.FTPdtCode
                    LEFT JOIN TCNMPdtUnit_L PUNL WITH(NOLOCK) ON PPCZ.FTPunCode = PUNL.FTPunCode AND PUNL.FNLngID   = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMPdtBar PBAR WITH (NOLOCK)  ON DOCTMP.FTPdtCode = PBAR.FTPdtCode
                    LEFT JOIN TCNMPdtSpcBch SPC WITH (NOLOCK) ON DOCTMP.FTPdtCode = SPC.FTPdtCode
                    WHERE DOCTMP.FDCreateOn <> ''
                    AND DOCTMP.FTXthDocKey  = 'TCNTPdtAdjCostHD'
                    AND DOCTMP.FTSessionID  = ".$this->db->escape($tFTSessionID)."
                ) A WHERE A.FTTmpStatus = 5 OR (A.FNRowID = 1 AND FNUnitFactRank = 1) ORDER BY A.FTPdtCode DESC
            ";
        } else {
            $tSQL   = "
                SELECT * FROM (
                    SELECT
                        ROW_NUMBER() OVER(PARTITION BY PDTL.FTPdtCode ORDER BY PDTL.FTPdtCode DESC) FNRowID,
                        RANK() OVER(PARTITION BY PDTL.FTPdtCode ORDER BY PPCZ.FCPdtUnitFact ASC) FNUnitFactRank,
                        DOCTMP.FTPdtCode,
                        PDTL.FTPdtName,
                        PUNL.FTPunName,
                        COST.FCPdtCostEx,
                        PPCZ.FTPunCode,
                        PDT.FCPdtCostStd,
                        PPCZ.FCPdtUnitFact AS FCXcdFactor,
                        PBAR.FTBarCode AS FTXcdBarScan,
                        (DOCTMP.FCXtdCostEx - PDT.FCPdtCostStd) AS FCXcdDiff,
                        DOCTMP.FCXtdCostEx AS FCXcdCostNew,
                        DOCTMP.FTTmpStatus,
                        DOCTMP.FTTmpRemark
                    FROM TCNTDocDTTmp DOCTMP
                    LEFT JOIN TCNMPdt_L PDTL WITH(NOLOCK) ON DOCTMP.FTPdtCode = PDTL.FTPdtCode AND PDTL.FNLngID     = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMPdt PDT WITH ( NOLOCK ) ON DOCTMP.FTPdtCode = PDT.FTPdtCode 
                    LEFT JOIN TCNMPdtCostAvg COST WITH(NOLOCK) ON DOCTMP.FTPdtCode = COST.FTPdtCode
                    LEFT JOIN TCNMPdtPackSize PPCZ WITH(NOLOCK) ON DOCTMP.FTPdtCode = PPCZ.FTPdtCode
                    LEFT JOIN TCNMPdtUnit_L PUNL WITH(NOLOCK) ON PPCZ.FTPunCode = PUNL.FTPunCode AND PUNL.FNLngID   = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMPdtBar PBAR WITH (NOLOCK)  ON DOCTMP.FTPdtCode = PBAR.FTPdtCode
                    LEFT JOIN TCNMPdtSpcBch SPC WITH (NOLOCK) ON DOCTMP.FTPdtCode = SPC.FTPdtCode
                    WHERE DOCTMP.FDCreateOn <> ''
                    AND DOCTMP.FTXthDocKey  = 'TCNTPdtAdjCostHD'
                    AND DOCTMP.FTSessionID  = ".$this->db->escape($tFTSessionID)."
                ) A WHERE A.FTTmpStatus = 5 OR (A.FNRowID = 1 AND FNUnitFactRank = 1) ORDER BY A.FTPdtCode DESC
            ";
        }

        $oQuery = $this->db->query($tSQL);

        if ($oQuery->num_rows() > 0) {
            $oList      = $oQuery->result_array();
            $aResult    = array(
                'raItems'   => $oList,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        } else {
            $aResult    = array(
                'rtCode'    => '99',
                'rtDesc'    => 'data not found',
            );
        }
        unset($nLngID);
        unset($tSQL);
        unset($oQuery);
        unset($oList);
        return $aResult;
    }

    //Functionality : Checkduplicate Sale Price Adj
    //Parameters : function parameters
    //Creator : 21/02/2019 Napat(Jame)
    //Return : data
    //Return Type : Array
    public function FSnMADCheckDuplicate($ptXphDocNo){
        $tSQL = "
            SELECT 
                COUNT(PPH.FTXchDocNo) AS counts
            FROM TCNTPdtAdjCostHD PPH 
            WHERE PPH.FTXchDocNo = ".$this->db->escape($ptXphDocNo)."
        ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            return $oQuery->row_array();
        } else {
            return FALSE;
        }
    }

    public function FSaMADAddUpdateDocNoInDocTemp($aDataWhere){
        try {
            $this->db->set('FTXthDocNo', $aDataWhere['FTXchDocNo']);
            $this->db->where('FTSessionID', $aDataWhere['FTSessionID']);
            $this->db->where('FTXthDocKey', $aDataWhere['FTXthDocKey']);
            $this->db->update('TCNTDocDTTmp');

            if ($this->db->affected_rows() > 0) {
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'OK',
                );
            } else {
                $aStatus = array(
                    'rtCode' => '903',
                    'rtDesc' => 'Not Update',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }

    // Functionality : INSERT DT
    // Parameters : Function Parameter
    // Creator : 25/02/2021 Sooksanti(Nont)
    // Last Modified :
    // Return : array
    // Return Type : array
    public function FSaMADCEventAddDT($paData){
        $this->db->insert('TCNTPdtAdjCostDT', $paData);
        if ($this->db->affected_rows() > 0) {
            $aResult    = array(
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        } else {
            $aResult    = array(
                'rtCode'    => '99',
                'rtDesc'    => 'data not found',
            );
        }
    }


    // Functionality : Clear DT
    // Parameters : Function Parameter
    // Creator : 25/02/2021 Sooksanti(Nont)
    // Last Modified :
    // Return : array
    // Return Type : array
    public function FSaMADCClearDT($paData){
        $this->db->where_in('FTXchDocNo', $paData['FTXchDocNo']);
        $this->db->delete('TCNTPdtAdjCostDT');
    }

    //Function Delete all data where docno from TCNTPdtAdjPriDT
    public function FSaMADDelAllProductDT($paData)
    {
        try {
            $this->db->trans_begin();
            $this->db->where('FTXchDocNo', $paData['FTXchDocNo']);
            $this->db->delete('TCNTPdtAdjCostDT');

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aStatus = array(
                    'rtCode' => '905',
                    'rtDesc' => 'Delete Unsuccess.'
                );
            } else {
                $this->db->trans_commit();
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Delete Success.'
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            echo $Error;
        }
    }

    //Function Select DocTmp and Insert into PdtPriDT
    public function FSoMADTmptoDT($paData){
        $FTXchDocNo     = $paData['FTXchDocNo'];
        $FTSessionID    = $paData['FTSessionID'];
        $FTXthDocKey    = $paData['FTXthDocKey'];
        $FDLastUpdOn    = $paData['FDLastUpdOn'];
        $FDCreateOn     = $paData['FDCreateOn'];
        $FTLastUpdBy    = $paData['FTLastUpdBy'];
        $FTCreateBy     = $paData['FTCreateBy'];
        $FTBchCode      = $paData['FTBchCode'];
        $tSQL           = "
            INSERT INTO TCNTPdtAdjCostDT (
                FTBchCode,FTXchDocNo,FNXcdSeqNo,FTPdtCode,FTPdtName,FCXcdCostOld,FCXcdDiff,FCXcdCostNew,
                FTPunCode,FCXcdFactor,FTXcdBarScan,FDLastUpdOn,FTLastUpdBy,FDCreateOn,FTCreateBy
            )
            SELECT 
                '$FTBchCode' AS FTBchCode,
                '$FTXchDocNo' AS FTXchDocNo,
                ROW_NUMBER() OVER(ORDER BY FNXtdSeqNo ASC) AS FNXcdSeqNo,
                FTPdtCode,
                FTPdtName,
                FCXtdVatRate AS FCXcdCostOld,
                FCXtdQtyOrd AS FCXcdDiff,
                FCXtdAmt AS FCXcdCostNew,
                FTPunCode,
                FCXtdFactor AS FCXcdFactor,
                FTXtdBarCode AS FTXcdBarScan,
                '$FDLastUpdOn' AS FDLastUpdOn,
                '$FTLastUpdBy' AS FTLastUpdBy,
                '$FDCreateOn' AS FDCreateOn,
                '$FTCreateBy' AS FTCreateBy
            FROM TCNTDocDTTmp WITH(NOLOCK)
            WHERE FTTmpStatus = '1' AND FTSessionID = '$FTSessionID' AND FTXthDocKey = '$FTXthDocKey'
        ";
        $oQuery = $this->db->query($tSQL);

        if ($oQuery > 0) {
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'Insert Doc Temp to DT Success'
            );
        } else {
            $aStatus = array(
                'rtCode' => '905',
                'rtDesc' => 'Error Cannot Insert Product to DT'
            );
        }
        return $aStatus;
    }


    // Functionality : INSERT HD
    // Parameters : Function Parameter
    // Creator : 25/02/2021
    // Last Modified :
    // Return : array
    // Return Type : array
    public function FSaMADCEventAddHD($paData){
        if($paData['FDXchRefIntDate'] == ''){
            $FDXchRefIntDate = NULL;
        }else{
            $FDXchRefIntDate = $paData['FDXchRefIntDate'];
        }
        $this->db->insert('TCNTPdtAdjCostHD', array(
            'FTBchCode'         => $paData['FTBchCode'],
            'FTXchDocNo'        => $paData['FTXchDocNo'],
            'FNXchDocType'      => $paData['FNXchDocType'],
            'FDXchDocDate'      => $paData['FDXchDocDate'],
            'FTXchDocTime'      => $paData['FTXchDocTime'],
            'FDXchAffect'       => $paData['FDXchAffect'],
            'FTXchRefInt'       => $paData['FTXchRefInt'],
            'FDXchRefIntDate'   => $FDXchRefIntDate,
            'FTUsrCode'         => $paData['FTUsrCode'],
            'FTXchStaDoc'       => $paData['FTXchStaDoc'],
            'FTXchRmk'          => $paData['FTXchRmk'],
            'FDLastUpdOn'       => date('Y-m-d H:i:s'),
            'FTLastUpdBy'       => $this->session->userdata('tSesUserCode'),
            'FDCreateOn'        => date('Y-m-d H:i:s'),
            'FTCreateBy'        => $this->session->userdata('tSesUserCode')
        ));
        //echo $this->db->last_query();
        if ($this->db->affected_rows() > 0) {
            $aResult = array(
                'rtCode' => '1',
                'rtDesc' => 'success',
            );
        } else {
            $aResult = array(
                'rtCode' => '99',
                'rtDesc' => 'data not found',
            );
        }
        return $aResult;
    }

    // Functionality : Edit HD
    // Parameters : Function Parameter
    // Creator : 25/02/2021 Sooksanti(Non)
    // Last Modified :
    // Return : array
    // Return Type : array
    public function FSaMADCEventEditHD($paData){
        if($paData['FDXchRefIntDate'] == ''){
            $FDXchRefIntDate = NULL;
        }else{
            $FDXchRefIntDate = $paData['FDXchRefIntDate'];
        }
        $this->db->set('FTBchCode', $paData['FTBchCode']);
        $this->db->set('FDXchDocDate', $paData['FDXchDocDate']);
        $this->db->set('FNXchDocType', $paData['FNXchDocType']);
        $this->db->set('FTXchDocTime', $paData['FTXchDocTime']);
        $this->db->set('FDXchAffect', $paData['FDXchAffect']);
        $this->db->set('FTXchRefInt', $paData['FTXchRefInt']);
        $this->db->set('FDXchRefIntDate', $FDXchRefIntDate);
        $this->db->set('FTUsrCode', $paData['FTUsrCode']);
        $this->db->set('FTXchRmk', $paData['FTXchRmk']);
        $this->db->set('FDLastUpdOn', date('Y-m-d h:i:sa'));
        $this->db->set('FTLastUpdBy', $this->session->userdata('tSesUserCode'));
        $this->db->where('FTXchDocNo', $paData['FTXchDocNo']);
        $this->db->update('TCNTPdtAdjCostHD');
        if ($this->db->affected_rows() > 0) {
            $aResult    = array(
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        } else {
            $aResult    = array(
                'rtCode'    => '99',
                'rtDesc'    => 'data not found',
            );
        }
        return $aResult;
    }


    // Functionality : Function Get Pdt From DT
    // Parameters : Function Parameter
    // Creator : 25/02/2021 Sooksanti (Nont)
    // Last Modified :
    // Return : array
    // Return Type : array
    public function FSaMADCGetPdtFromDT($paData){
        $tDocNo = $paData['tDocNo'];
        $nLngID = $this->session->userdata("tLangEdit");
        $tSQL   = "
            SELECT
                ADCDT.FTPdtCode,
                ADCDT.FTPdtName,
                PUNL.FTPunName,
                ADCDT.FCXcdCostOld AS FCPdtCostStd,
                ADCDT.FCXcdCostOld AS FCPdtCostEx,
                ADCDT.FCXcdDiff,
                ADCDT.FCXcdCostNew,
                ADCDT.FTPunCode,
                ADCDT.FCXcdFactor,
                ADCDT.FTXcdBarScan
            FROM TCNTPdtAdjCostDT ADCDT
            LEFT JOIN TCNMPdtUnit_L PUNL WITH(NOLOCK) ON ADCDT.FTPunCode = PUNL.FTPunCode
            WHERE 1=1
        ";
        if($tDocNo != ''){
            $tSQL   .= "AND ADCDT.FTXchDocNo    = ".$this->db->escape($tDocNo)." ";
        }
        // echo $tSQL;
        // exit();
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $oList      = $oQuery->result_array();
            $aResult    = array(
                'raItems'   => $oList,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        } else {
            $aResult    = array(
                'rtCode'    => '99',
                'rtDesc'    => 'data not found',
            );
        }
        unset($nLngID);
        unset($tSQL);
        unset($oQuery);
        unset($oList);
        return $aResult;
    }


    //Functionality : Function Cancel Doc
    //Parameters : function parameters
    //Creator : 03/03/2021
    //Last Modified : -
    //Return : Status Cancel
    //Return Type : array
    public function FSaMADCCancel($paDataUpdate){
        try {
            $this->db->set('FTXchStaApv', null);
            $this->db->set('FTXchStaDoc', 3);
            $this->db->where('FTXchDocNo', $paDataUpdate['FTXchDocNo']);
            $this->db->update('TCNTPdtAdjCostHD');
            if ($this->db->affected_rows() > 0) {
                $aStatus    = array(
                    'rtCode'    => '1',
                    'rtDesc'    => 'OK',
                );
            } else {
                $aStatus    = array(
                    'rtCode'    => '903',
                    'rtDesc'    => 'Not Approve',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }

    // Functionality : Delete HD/DT Document Adjust Cost
    // Parameters : function parameters
    // Creator : 03/03/2021 Sooksanti
    // Last Modified : -
    // Return : Array Status Delete
    // Return Type : array
    public function FSnMADCDelDocument($paDataDoc){
        $tADCDocNo  = $paDataDoc['tADCDocNo'];
        $this->db->trans_begin();
        // Document HD
        $this->db->where_in('FTXchDocNo', $tADCDocNo);
        $this->db->delete('TCNTPdtAdjCostHD');
        // Document DT
        $this->db->where_in('FTXchDocNo', $tADCDocNo);
        $this->db->delete('TCNTPdtAdjCostDT');
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $aStaDeleteDoc  = array(
                'rtCode'    => '905',
                'rtDesc'    => 'Cannot Delete Item.',
            );
        } else {
            $this->db->trans_commit();
            $aStaDeleteDoc  = array(
                'rtCode'    => '1',
                'rtDesc'    => 'Delete Complete.',
            );
        }
        return $aStaDeleteDoc;
    }


    //Functionality : Function Approve Doc
    //Parameters : function parameters
    //Creator : Sooksanti
    //Last Modified :
    //Return : Status Approve
    //Return Type : array
    public function FSvMADCApprove($paDataUpdate)
    {
        try {
            $dLastUpdOn = date('Y-m-d H:i:s');
            $tLastUpdBy = $this->session->userdata('tSesUsername');
            $this->db->set('FDLastUpdOn', $dLastUpdOn);
            $this->db->set('FTLastUpdBy', $tLastUpdBy);
            $this->db->set('FTXchStaApv', 1);
            $this->db->set('FTXchApvCode', $paDataUpdate['FTXchApvCode']);
            $this->db->where('FTXchDocNo', $paDataUpdate['FTXchDocNo']);
            $this->db->update('TCNTPdtAdjCostHD');
            if ($this->db->affected_rows() > 0) {
                $aStatus = array(
                    'rtCode'  => '1',
                    'rtDesc'  => 'OK',
                );
            } else {
                $aStatus = array(
                    'rtCode'  => '903',
                    'rtDesc'  => 'Not Approve',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }

    //Function Check Duplicate Data from Tmemp
    public function FSaMSPACheckDataSeq($paData)
    {

        try {
            $FTSessionID  = $paData['FTSessionID'];
            $FTXthDocKey  = $paData['FTXthDocKey'];

            $tSQL = "SELECT MAX(FNXtdSeqNo) AS nSeq
                     FROM TCNTDocDTTmp
                     WHERE FTSessionID = '$FTSessionID' AND FTXthDocKey = '$FTXthDocKey'";
            $oQuery = $this->db->query($tSQL);

            if ($oQuery->num_rows() > 0) {

                return $oQuery->result_array();
            } else {

                return FALSE;
            }
        } catch (Exception $Error) {
            echo $Error;
        }
    }
    
    //Function Delete data from TCNTDocDTTmp
    public function FSaMAdDelPdtTmp($paData)
    {
        try {
            $this->db->trans_begin();
            $this->db->where('FTXthDocKey', $paData['FTXthDocKey']);
            $this->db->where('FTSessionID', $paData['FTSessionID']);
            $this->db->delete('TCNTDocDTTmp');

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aStatus = array(
                    'rtCode' => '905',
                    'rtDesc' => 'Delete Unsuccess.',
                );
            } else {
                $this->db->trans_commit();
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Delete Success.',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            echo $Error;
        }
    }

    //Functionality : Add Product to Doc Temp
    //Parameters : function parameters
    //Creator : 27/02/2019 Napat(Jame)
    //Return : Status Add Event
    //Return Type : array
    public function FSaMSPAAddPdtDocTmp($paData)
    {
        try {
            if($paData['FCXtdQtyOrd'] == ''){
                $paData['FCXtdQtyOrd'] = 0;
            }

            if($paData['FCXtdAmt'] == ''){
                $paData['FCXtdAmt'] = 0;
            }
            // Add TCNTDocDTTmp
            $this->db->insert('TCNTDocDTTmp', array(
                'FNXtdSeqNo'      => $paData['FNXtdSeqNo'],
                'FTBchCode'       => $paData['FTBchCode'],
                'FTXthDocNo'      => $paData['FTXthDocNo'],
                'FTXthDocKey'     => $paData['FTXthDocKey'],
                'FTPdtCode'      => $paData['FTPdtCode'],
                'FTPdtName'      => $paData['FTPdtName'],
                'FTPunName'       => $paData['FTPunName'],
                'FCXtdVatRate'       => $paData['FCXtdVatRate'],               
                'FCXtdQty'   => $paData['FCXtdQty'],
                'FTPunCode'   => $paData['FTPunCode'],
                'FCXtdFactor'       => $paData['FCXtdFactor'],
                'FTXtdBarCode'       => $paData['FTXtdBarCode'],               
                'FCXtdAmt'       => $paData['FCXtdAmt'],
                'FCXtdQtyOrd'       => $paData['FCXtdQtyOrd'],               
                'FTSessionID'     => $paData['FTSessionID'],
                'FDLastUpdOn'     => $paData['FDLastUpdOn'],
                'FDCreateOn'      => $paData['FDCreateOn'],
                'FTLastUpdBy'     => $paData['FTLastUpdBy'],
                'FTCreateBy'      => $paData['FTCreateBy'],
                'FTTmpStatus'     => '1'
            ));

            if ($this->db->affected_rows() > 0) {
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Add Product to tmp Success',
                );
            } else {
                $aStatus = array(
                    'rtCode' => '905',
                    'rtDesc' => 'Error Cannot Add/Edit Product to tmp',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            echo $Error;
        }
    }

     //Function Check Duplicate Data from Tmemp
     public function FSaMSPACheckDataTempDuplicate($paData){
        try {
            $FTXthDocNo   = $paData['FTXthDocNo'];
            $FTPdtCode    = $paData['FTPdtCode'];
            $FTPunCode    = $paData['FTPunCode'];
            $FTSessionID  = $paData['FTSessionID'];
            $FTXthDocKey  = $paData['FTXthDocKey'];
            $tSQL   = "
                SELECT *
                FROM TCNTDocDTTmp WITH(NOLOCK)
                WHERE FTXthDocNo    = ".$this->db->escape($FTXthDocNo)."
                AND FTPdtCode       = ".$this->db->escape($FTPdtCode)."
                AND FTPunCode       = ".$this->db->escape($FTPunCode)."
                AND FTSessionID     = ".$this->db->escape($FTSessionID)."
                AND FTXthDocKey     = ".$this->db->escape($FTXthDocKey)."
            ";
            $oQuery = $this->db->query($tSQL);
            if ($oQuery->num_rows() > 0) {
                return $oQuery->row_array();
            } else {
                return FALSE;
            }
        } catch (Exception $Error) {
            echo $Error;
        }
    }

    //Functionality : All Page Of Product Size
    //Parameters : function parameters
    //Creator :  25/02/2019 Napat(Jame)
    //Return : object Count All Product Model
    //Return Type : Object
    public function FSoMSPAGetPdtPriPageAll($ptSearchList, $ptXphDocNo, $FTSessionID, $nLngID, $FTXthDocKey){
        try {
            $tSQL   = "
                SELECT 
                    COUNT (DTP.FTSessionID) AS counts
                FROM TCNTDocDTTmp DTP WITH(NOLOCK)
                LEFT JOIN TCNMPdt_L PDT_L      ON DTP.FTPdtCode = PDT_L.FTPdtCode AND PDT_L.FNLngID  = ".$this->db->escape($nLngID)."
                LEFT JOIN TCNMPdtUnit_L PUN_L  ON DTP.FTPunCode = PUN_L.FTPunCode AND PUN_L.FNLngID  = ".$this->db->escape($nLngID)."
                LEFT JOIN TCNMShop_L SHP_L     ON DTP.FTXtdShpTo = SHP_L.FTShpCode AND DTP.FTBchCode = SHP_L.FTBchCode AND SHP_L.FNLngID = ".$this->db->escape($nLngID)."
                LEFT JOIN TCNMBranch_L BCH_L   ON DTP.FTXtdBchTo = BCH_L.FTBchCode AND BCH_L.FNLngID = ".$this->db->escape($nLngID)."
                WHERE DTP.FTSessionID = ".$this->db->escape($FTSessionID)." AND DTP.FTXthDocKey = ".$this->db->escape($FTXthDocKey)."
                AND (DTP.FTTmpStatus = 1 OR ISNULL(DTP.FTTmpStatus,'') = '')
            ";
            if (isset($ptSearchList) && !empty($ptSearchList)) {
                $tSQL   .= " AND (DTP.FTPdtCode  LIKE '%".$this->db->escape_like_str($ptSearchList)."%'";
                $tSQL   .= " OR PDT_L.FTPdtName  LIKE '%".$this->db->escape_like_str($ptSearchList)."%'";
                $tSQL   .= " OR PUN_L.FTPunName  LIKE '%".$this->db->escape_like_str($ptSearchList)."%'";
                $tSQL   .= " OR SHP_L.FTShpName  LIKE '%".$this->db->escape_like_str($ptSearchList)."%'";
                $tSQL   .= " OR BCH_L.FTBchName  LIKE '%".$this->db->escape_like_str($ptSearchList)."%')";
            }
            $oQuery = $this->db->query($tSQL);
            if ($oQuery->num_rows() > 0) {
                return $oQuery->result();
            } else {
                return false;
            }
        } catch (Exception $Error) {
            echo $Error;
        }
    }

    //Functionality : list Product Price Data Table
    //Parameters : function parameters
    //Creator :  18/02/2019 Napat(Jame)
    //Return : data
    //Return Type : Array
    public function FSaMSPAPdtAdPriList($paData){
        try {
            $aRowLen        = FCNaHCallLenData($paData['nRow'],$paData['nPage']);
            $nLngID         = $paData['FNLngID'];
            $tSearchList    = $paData['tSearchAll'];
            $FTXthDocKey    = $paData['FTXthDocKey'];
            $FTXphDocNo     = $paData['FTXphDocNo'];
            $FTSessionID    = $paData['FTSessionID'];
            $tCostType      = $paData['tCostType'];
            $tCostCondition = '';
            if($tCostType == 12){
                $tCostCondition = 'DTP.FCXtdVatRate AS FCPdtCostStd,';
            }else{
                $tCostCondition = 'DTP.FCXtdQty AS FCPdtCostEx,';
            }
            ////เช็คตัวที่ไม่มี puncode
            // Last Update : Napat(Jame) 29/08/2022 เปลี่ยนเป็นค้นหาตาม error text ให้หาหน่วยเล็กสุด เฉพาะเคสที่ กรอกหน่วยผิด
            $tSQL2          = "
                SELECT
                    TMP.FTPunCode AS FTPunCodeTemp,
                    TMP.FTPdtCode AS FTPdtCodeTemp,
                    ISNULL( MAS.FTPunCode, MAS.FTPunCode ) AS FTPunCode,
                    ISNULL( MAS.FTPdtCode, MAS.FTPdtCode ) AS FTPdtCode
                FROM TCNTDocDTTmp TMP WITH(NOLOCK)
                LEFT JOIN TCNMPdtPackSize MAS WITH ( NOLOCK ) ON MAS.FTPdtCode = TMP.FTPdtCode AND MAS.FCPdtUnitFact = 1
                WHERE TMP.FTSessionID = ".$this->db->escape($FTSessionID)."
                AND FTTmpRemark = 'ไม่พบหน่วยสินค้าในระบบ'
                AND MAS.FTPunCode != ''
            ";
            
            $oQuery2  = $this->db->query($tSQL2);
            $aList2   = $oQuery2->result_array();
            foreach($aList2 as $key => $aval){
                $this->db->where('FTSessionID', $paData['FTSessionID']);
                $this->db->where('FTPunCode', $aval['FTPunCodeTemp']);
                $this->db->where('FTPdtCode', $aval['FTPdtCodeTemp']);
                $this->db->update('TCNTDocDTTmp', array(
                    'FTPunCode'         => $aval['FTPunCode'],
                    'FTTmpStatus'       => 1,
                    'FTTmpRemark'       => '',
                ));
            }

            ////จบเช็คตัวที่ไม่มี puncode
            $tSQL   = "
                SELECT c.* FROM(
                SELECT  ROW_NUMBER() OVER(ORDER BY FNXtdSeqNo ASC) AS rtRowID,* 
                FROM (
                    SELECT 
                        DTP.FTXthDocNo AS FTXthDocNo,
                        DTP.FNXtdSeqNo AS FNXtdSeqNo,
                        DTP.FTPdtCode AS FTPdtCode,
                        DTP.FTPunCode AS FTPunCode,
                        ".$tCostCondition."
                        DTP.FCXtdQtyOrd AS FCXcdDiff,
                        DTP.FCXtdAmt AS FCXcdCostNew,
                        DTP.FCXtdFactor AS FCXcdFactor,
                        PDT_L.FTPdtName AS FTPdtName,
                        PUN_L.FTPunName AS FTPunName,
                        BCH_L.FTBchName AS FTBchName,
                        SHP_L.FTShpName AS FTShpName,
                        DTP.FCXtdPriceRet AS FCXtdPriceRet,
                        DTP.FCXtdPriceWhs AS FCXtdPriceWhs,
                        DTP.FCXtdPriceNet AS FCXtdPriceNet,
                        DTP.FTXthDocNo AS FTDefalutPrice,
                        DTP.FTXtdShpTo AS FTXtdShpTo,
                        DTP.FTXtdBchTo AS FTXtdBchTo,
                        DTP.FTTmpRemark AS FTTmpRemark,
                        DTP.FTTmpStatus AS FTTmpStatus,
                        convert(varchar, DTP.FDCreateOn,103) AS FDDateIns,
                        convert(varchar, DTP.FDLastUpdOn,103) AS FDDateUpd,
                        STUFF( (    SELECT DISTINCT ',' + cast(PAC.FTPunCode as varchar(max))
                                FROM TCNTDocDTTmp DTP
                                                LEFT JOIN TCNMPdtPackSize PAC ON PAC.FTPdtCode = DTP.FTPdtCode
                                                WHERE DTP.FTSessionID = ".$this->db->escape($FTSessionID)." 
                                                AND DTP.FTXthDocKey = ".$this->db->escape($FTXthDocKey)."
                                for xml path ('')
                                ), 1, 1, '' ) AS FTAllPunCode,

                        STUFF( (    SELECT DISTINCT ',' + cast(UNITL.FTPunName as varchar(max))
                                    FROM TCNTDocDTTmp DTP
                                                    LEFT JOIN TCNMPdtPackSize PAC ON DTP.FTPdtCode = PAC.FTPdtCode  
                                                    LEFT JOIN TCNMPdtUnit_L UNITL ON PAC.FTPunCode = UNITL.FTPunCode AND UNITL.FNLngID = ".$this->db->escape($nLngID)."
                                                    WHERE DTP.FTSessionID = ".$this->db->escape($FTSessionID)." 
                                                    AND DTP.FTXthDocKey = ".$this->db->escape($FTXthDocKey)."
                                    for xml path ('')
                                ), 1, 1, '' ) AS FTAllPunName
                        /*STRING_AGG(PAC.FTPunCode,',') AS FTAllPunCode,
                        STRING_AGG ( UNITL.FTPunName, ',' ) AS FTAllPunName*/
                    FROM TCNTDocDTTmp DTP WITH(NOLOCK)
                    LEFT JOIN TCNMPdt_L PDT_L WITH(NOLOCK) ON DTP.FTPdtCode = PDT_L.FTPdtCode AND PDT_L.FNLngID      = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMPdtUnit_L PUN_L WITH(NOLOCK) ON DTP.FTPunCode = PUN_L.FTPunCode AND PUN_L.FNLngID  = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMShop_L SHP_L WITH(NOLOCK) ON DTP.FTXtdShpTo = SHP_L.FTShpCode AND DTP.FTBchCode = SHP_L.FTBchCode  AND SHP_L.FNLngID   = ".$this->db->escape($nLngID)."
                    LEFT JOIN TCNMBranch_L BCH_L WITH(NOLOCK) ON DTP.FTXtdBchTo = BCH_L.FTBchCode AND BCH_L.FNLngID = ".$this->db->escape($nLngID)."
                    INNER JOIN TCNMPdtPackSize PAC WITH(NOLOCK) ON PAC.FTPdtCode = DTP.FTPdtCode  AND PAC.FTPunCode = DTP.FTPunCode
                    LEFT JOIN TCNMPdtUnit_L UNITL WITH(NOLOCK) ON PAC.FTPunCode = UNITL.FTPunCode AND UNITL.FNLngID = ".$this->db->escape($nLngID)."
                    WHERE DTP.FDCreateOn <> '' 
                    AND DTP.FTSessionID     = ".$this->db->escape($FTSessionID)." 
                    AND DTP.FTXthDocKey     = ".$this->db->escape($FTXthDocKey)."
            ";
           
            if (isset($tSearchList) && !empty($tSearchList)) {
                $tSQL   .= " AND (DTP.FTPdtCode LIKE '%".$this->db->escape_like_str($tSearchList)."%'";
                $tSQL   .= " OR PDT_L.FTPdtName LIKE '%".$this->db->escape_like_str($tSearchList)."%'";
                $tSQL   .= " OR PUN_L.FTPunName LIKE '%".$this->db->escape_like_str($tSearchList)."%'";
                $tSQL   .= " OR SHP_L.FTShpName LIKE '%".$this->db->escape_like_str($tSearchList)."%'";
                $tSQL   .= " OR BCH_L.FTBchName LIKE '%".$this->db->escape_like_str($tSearchList)."%')";
            }

            $tSQL   .= "
                GROUP BY 
                    DTP.FTPdtCode,
                    DTP.FTXthDocNo,
                    DTP.FNXtdSeqNo,
                    DTP.FTPunCode,
                    PAC.FTPunCode,
                    UNITL.FTPunName,
                    PDT_L.FTPdtName,
                    PUN_L.FTPunName,
                    BCH_L.FTBchName,
                    SHP_L.FTShpName,
                    DTP.FCXtdPriceRet,
                    DTP.FCXtdPriceWhs,
                    DTP.FCXtdPriceNet,
                    DTP.FTXthDocNo,
                    DTP.FTXtdShpTo,
                    DTP.FTXtdBchTo,
                    DTP.FTTmpRemark,
                    DTP.FTTmpStatus,
                    DTP.FDCreateOn,
                    DTP.FDLastUpdOn,
                    DTP.FCXtdQty,
                    DTP.FCXtdQtyOrd,
                    DTP.FCXtdAmt,
                    DTP.FCXtdFactor,
                    DTP.FCXtdVatRate
            ";
            $tSQL   .= ") Base) AS c WHERE c.rtRowID > ".$this->db->escape($aRowLen[0])." AND c.rtRowID <= ".$this->db->escape($aRowLen[1])."";

            $oQuery  = $this->db->query($tSQL);
            if ($oQuery->num_rows() > 0) {
                $aList      = $oQuery->result_array();
                $oFoundRow  = $this->FSoMSPAGetPdtPriPageAll($tSearchList, $FTXphDocNo, $FTSessionID, $nLngID, $FTXthDocKey);
                $nFoundRow  = $oFoundRow[0]->counts;
                $nPageAll   = ceil($nFoundRow / $paData['nRow']); //หา Page All จำนวน Rec หาร จำนวนต่อหน้า
                $aResult    = array(
                    'raItems'       => $aList,
                    'rnAllRow'      => $nFoundRow,
                    'rnCurrentPage' => $paData['nPage'],
                    'rnAllPage'     => $nPageAll,
                    'rtCode'        => '1',
                    'rtDesc'        => 'success',
                );
            } else {
                //No Data
                $aResult    = array(
                    'rnAllRow'      => 0,
                    'rnCurrentPage' => $paData['nPage'],
                    "rnAllPage"     => 0,
                    'rtCode'        => '800',
                    'rtDesc'        => 'data not found',
                );
            }
            return $aResult;
        } catch (Exception $Error) {
            echo $Error;
        }
    }

    //Function Updated Product Price for Table Doc Temp
    public function FSaMAdUpdatePriceTemp($paData)
    {
        try {
        
            $this->db->set('FCXtdAmt', $paData['tValue']);
            $this->db->set('FCXtdQtyOrd', $paData['tDiff']);
            
            if ($paData['tSeq'] != 'N') {
                $this->db->where('FNXtdSeqNo', $paData['tSeq']);
            }

            $this->db->where('FTSessionID', $paData['FTSessionID']);

            $this->db->update('TCNTDocDTTmp');

            if ($this->db->affected_rows() > 0) {
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Updated Price Temp Success',
                );
            } else {
                $aStatus = array(
                    'rtCode' => '905',
                    'rtDesc' => 'Error Cannot Updated Price Temp',
                );
            }

            return $aStatus;
        } catch (Exception $Error) {
            echo $Error;
        }
    }

     //Functionality : Delete Product Temp
    //Parameters : function parameters
    //Creator : 27/02/2019 Napat(Jame)
    //Return : Status Delete
    //Return Type : array
    public function FSaMADCPdtTmpDelAll($paData)
    {
        try {
            $this->db->trans_begin();
            // $this->db->where('FTXthDocNo', $paData['FTXphDocNo']);
            // $this->db->where('FTPdtCode', $paData['FTPdtCode']);
            // $this->db->where('FTPunCode', $paData['FTPunCode']);
            $this->db->where('FTXthDocKey', $paData['FTXthDocKey']);
            $this->db->where('FTSessionID', $paData['FTSessionID']);
            $this->db->where('FNXtdSeqNo', $paData['FNXtdSeqNo']);
            $this->db->delete('TCNTDocDTTmp');

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aStatus = array(
                    'rtCode' => '905',
                    'rtDesc' => 'Delete Unsuccess.',
                );
            } else {
                $this->db->trans_commit();
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Delete Success.',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            echo $Error;
        }
    }
}
