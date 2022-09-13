<?php
defined('BASEPATH') or exit('No direct script access allowed');

class checkinfopos_model extends CI_Model{

    // Functionality : list Data Check Information Pos Server And Pos Client
    // Parameters    : function parameters
    // Creator       : 17/06/2022 Wasin
    // Return        : data
    // Return Type   : Array
    public function FSaMCIPDataList($paData){
        $nLngID         = $paData['FNLngID'];
        $aDataQuery     = [];
        $tSQL           = "
            SELECT 
                S.FTSynTable,
                ACT.FTScrActName,
                SL.FTSynName,
                HIS.FNHisRowID,
                HIS.FNSynSeqNo,
                HIS.FTAgnCode,
                AGNL.FTAgnName,
                HIS.FTBchCode,
                BCHL.FTBchName,
                HIS.FTPosCode,
                POSL.FTPosName,
                HIS.FNHisRowIns,
                HIS.FNHisRowUpd,
                HIS.FNHisRowAll,
                HIS.FDHisLastSync
            FROM (
                SELECT H.* FROM (
                    SELECT 
                        ROW_NUMBER() OVER (Partition BY FNSynSeqNo ORDER BY FDHisLastSync DESC) AS FNHisRowID ,FNSynSeqNo,FTPosCode,
                        FTAgnCode,FTBchCode,FNHisRowIns,FNHisRowUpd,FNHisRowAll,FDHisLastSync
                    FROM TCNTSyncHis WITH(NOLOCK)
                ) H WHERE H.FNHisRowID = 1 
            ) HIS
            INNER JOIN TSysSyncData S WITH(NOLOCK)		ON S.FNSynSeqNo		= HIS.FNSynSeqNo 
            INNER JOIN TSysSyncData_L SL WITH(NOLOCK)	ON SL.FNSynSeqNo	= HIS.FNSynSeqNo AND SL.FNLngID		= ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMAgency_L AGNL WITH(NOLOCK)	ON HIS.FTAgnCode	= AGNL.FTAgnCode AND AGNL.FNLngID	= ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMBranch_L BCHL WITH(NOLOCK)	ON HIS.FTBchCode	= BCHL.FTBchCode AND BCHL.FNLngID	= ".$this->db->escape($nLngID)."
            LEFT JOIN TCNMPos_L	POSL	WITH(NOLOCK)	ON HIS.FTBchCode	= POSL.FTBchCode AND HIS.FTPosCode	= POSL.FTPosCode AND POSL.FNLngID = ".$this->db->escape($nLngID)."
            LEFT JOIN (
                SELECT 
                    TABLE_NAME AS FTScrTableName,COLUMN_NAME AS FTScrActName
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE (COLUMN_NAME LIKE '%StaUse%')
            ) ACT ON S.FTSynTable = ACT.FTScrTableName
            WHERE HIS.FDHisLastSync <> ''
        ";
        $tSesAgnCode    = $paData['tSesAgnCode'];
        if(isset($tSesAgnCode) && !empty($tSesAgnCode)){
            $tSQL   .= " AND (HIS.FTAgnCode = ".$this->db->escape($tSesAgnCode).")";
        }
        $tSearchList = $paData['tSearchAll'];
        if(isset($tSearchList) && !empty($tSearchList)){
            $tSQL   .= " AND (SL.FTSynName COLLATE THAI_BIN LIKE '%".$this->db->escape_like_str($tSearchList)."%'";
            $tSQL   .= " OR AGNL.FTAgnName COLLATE THAI_BIN LIKE '%".$this->db->escape_like_str($tSearchList)."%'";
            $tSQL   .= " OR BCHL.FTBchName COLLATE THAI_BIN LIKE '%".$this->db->escape_like_str($tSearchList)."%'";
            $tSQL   .= " OR HIS.FTPosCode COLLATE THAI_BIN LIKE '%".$this->db->escape_like_str($tSearchList)."%')";
        }
        $oQuery  = $this->db->query($tSQL);
        if($oQuery->num_rows() > 0){
            $aList  = $oQuery->result_array();
            // Loop Data
            foreach($aList AS $nKey => $aValue){
                $aDataAFMerge       = [];
                $tWhereActive       = "";
                $tTableSelete       = $aValue['FTSynTable'];
                $tTableActiveName   = $aValue['FTScrActName'];
                switch($tTableSelete){
                    case 'TCNMPdt':
                        $tWhereActive   = "FTPdtStaActive = 1 ";
                    break;
                    case 'TCNTPdtPmtHD':
                        $tWhereActive   = "FTPmhStaApv = 1 AND FTPmhStaClosed = 0 AND (CONVERT(DATE,FDPmhDStop) >= CONVERT(DATE,GETDATE()))";
                    break;
                    case 'TFNTCouponHD':
                        $tWhereActive   = "FTCphStaApv = 1 AND FTCphStaClosed = 1 AND (CONVERT(DATE,FDCphDateStop) >= CONVERT(DATE,GETDATE()))";
                    break;
                    default:
                        if(isset($tTableActiveName) && !empty($tTableActiveName)){
                            $tWhereActive   =  "".$tTableSelete.".".$tTableActiveName." = 1 ";
                        }
                }

                $tSQLCount      = "SELECT COUNT(*) AS DATACOUNT FROM ".$tTableSelete." WITH(NOLOCK) WHERE ".$tWhereActive."";
                // echo '<pre>';
                // print_r($tSQLCount);
                // echo '</pre>';
                $oQueryCount    = $this->db->query($tSQLCount);
                $nCountTbl      = $oQueryCount->row_array()['DATACOUNT'];
                $aDataAFMerge   = array_merge($aValue,array('FNPSVRowActive'=>$nCountTbl));
                array_push($aDataQuery,$aDataAFMerge);
            }
            $aResult    = array(
                'raItems'   => $aDataQuery,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        }else{
            //No Data
            $aResult    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found',
            );
        }
        return $aResult;
    }
















}