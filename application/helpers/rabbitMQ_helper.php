<?php
require_once(APPPATH . 'libraries/rabbitmq/vendor/autoload.php');
require_once(APPPATH . 'config/rabbitmq.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 *
 * @param array $paParams
 *
 * $paParams = [
        "queueName" => "",
        "params" => [
            "ptBchCode" => "", "ptDocNo" => "", "ptUsrCode" => ""
        ]
    ];
 */
function FCNxCallRabbitMQ($paParams)
{
    $tQueueName = (isset($paParams['queueName']))?$paParams['queueName']:'';
    $aParams = (isset($paParams['params']))?$paParams['params']:[];
    $bStaUseConnStr = (isset($paParams['bStaUseConnStr']) && is_bool($paParams['bStaUseConnStr']))?$paParams['bStaUseConnStr']:true;
    $tVhostType = (isset($paParams['tVhostType'])) ?$paParams['tVhostType']:'D';

    if ($bStaUseConnStr) {
        $aParams['ptConnStr'] = DB_CONNECT;
    }
    $tExchange = EXCHANGE; // This use default exchange

    switch($tVhostType){
        case 'W': {
            $oConnection = new AMQPStreamConnection(MQ_CRD_HOST, MQ_CRD_PORT, MQ_CRD_USER, MQ_CRD_PASS, MQ_CRD_VHOST);
            // $aParams['ptData']['ptConnStr'] = DB_CONNECT;
            // $bDurable = true;
            break;
        }
        case 'A': {
            $oConnection = new AMQPStreamConnection(MQ_AUD_HOST, MQ_AUD_PORT, MQ_AUD_USER, MQ_AUD_PASS, MQ_AUD_VHOST);
            // $aParams['ptData']['ptConnStr'] = DB_CONNECT;
            // $bDurable = true;
            break;
        }
        case 'NOT': {
            $oConnection = new AMQPStreamConnection(MQ_NOT_HOST, MQ_NOT_PORT, MQ_NOT_USER, MQ_NOT_PASS, MQ_NOT_VHOST);
            // $aParams['ptData']['ptConnStr'] = DB_CONNECT;
            // $bDurable = true;
            break;
        }
        case 'S': {
            $oConnection = new AMQPStreamConnection(MQ_Sale_HOST, MQ_Sale_PORT, MQ_Sale_USER, MQ_Sale_PASS, MQ_Sale_VHOST);
            break;
        }
        case 'P': {
            $oConnection = new AMQPStreamConnection(MQ_PURGE_HOST, MQ_PURGE_PORT, MQ_PURGE_USER, MQ_PURGE_PASS, MQ_PURGE_VHOST);
            break;
        }
        default : {
            $oConnection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
            // $bDurable = false;
        }
    }

    $oChannel = $oConnection->channel();
    $oChannel->queue_declare($tQueueName, false, true, false, false);
    $oMessage = new AMQPMessage(json_encode($aParams,JSON_UNESCAPED_UNICODE));
    $oChannel->basic_publish($oMessage, "", $tQueueName);
    $oChannel->close();
    $oConnection->close();
    return 1;
    /** Success */

    /*$tQueueName = $paParams['queueName'];
    $aParams = $paParams['params'];
    $aParams['ptConnStr'] = DB_CONNECT;
    $tExchange = EXCHANGE;

    $oConnection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
    $oChannel = $oConnection->channel();
    $oChannel->queue_declare($tQueueName, false, false, false, false);
    $oChannel->exchange_declare($tExchange, 'direct', false, false, false);
    $oChannel->queue_bind($tQueueName, $tExchange);
    $oMessage = new AMQPMessage(json_encode($aParams));
    $oChannel->basic_publish($oMessage, $tExchange);

    echo "[x] Sent $tQueueName Success";

    $oChannel->close();
    $oConnection->close();*/
}

/**
 *
 * @param array $paParams
 *
 * $paParams = [
        "prefixQueueName" => "",
        "params" => [
            "ptBchCode" => "", "ptDocNo" => "", "ptUsrCode" => ""
        ]
    ];
 */
function FCNxRabbitMQDeleteQName(array $paParams = [])
{
    $tPrefixQueueName = $paParams['prefixQueueName'];
    $aParams = $paParams['params'];
    $tVhostType = (isset($paParams['tVhostType']) && in_array($paParams['tVhostType'],['W','D']))?$paParams['tVhostType']:'D';

    $tQueueName = $tPrefixQueueName . '_' . $aParams['ptDocNo'] . '_' . $aParams['ptUsrCode'];
    // $oConnection = new AMQPStreamConnection('172.16.30.28', '5672', 'admin', '1234', 'Pandora_PPT1');

    switch($tVhostType){
        case 'W': {
            $oConnection = new AMQPStreamConnection(MQ_CRD_HOST, MQ_CRD_PORT, MQ_CRD_USER, MQ_CRD_PASS, MQ_CRD_VHOST);
            break;
        }
        default : {
            $oConnection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
        }
    }

    $oChannel = $oConnection->channel();
    $oChannel->queue_delete($tQueueName);
    $oChannel->close();
    $oConnection->close();
    return 1;
    /** Success */
}

function FSaHRabbitMQUpdateStaDelQnameHD($paParams)
{
    try {
        $tDocTableName = $paParams['tDocTableName'];
        $tDocFieldDocNo = $paParams['tDocFieldDocNo'];
        $tDocFieldStaApv = $paParams['tDocFieldStaApv'];
        $tDocFieldStaDelMQ = $paParams['tDocFieldStaDelMQ'];
        $tDocStaDelMQ = $paParams['tDocStaDelMQ'];
        $tDocNo = $paParams['tDocNo'];

        $ci = &get_instance();
        $ci->load->database();

        // Update HD
        $ci->db->set($tDocFieldStaDelMQ, 1);
        $ci->db->where($tDocFieldDocNo, $tDocNo);
        $ci->db->update($tDocTableName);


        if ($ci->db->affected_rows() > 0) {
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'Update Master Success',
            );
        } else {
            $aStatus = array(
                'rtCode' => '905',
                'rtDesc' => 'Error Cannot Add/Edit Master.',
            );
        }
        return $aStatus;
    } catch (Exception $Error) {
        return $Error;
    }
}

function FCNxRabbitMQGetMassage(array $paParams = [])
{
    /*$tQname = $paParams['tQname'];
		$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
		$channel = $connection->channel();
        $channel->queue_declare($tQname, false, true, false, false);
        $message = $channel->basic_get($tQname);
        if(!empty($message)){
            $channel->basic_ack($message->delivery_info['delivery_tag']);
            $nProgress = intval($message->body);
        }else{
            $nProgress = 'false' ;
        }
        $channel->close();
        $connection->close();
        return $nProgress;*/

    try {
        /*===== Begin Set Config =======================================================*/
        $tQname = $paParams['tQname'];
        $tVhostType = (isset($paParams['tVhostType']) && in_array($paParams['tVhostType'],['W','D','A','NOT']))?$paParams['tVhostType']:'D';
        /*===== End Set Config =========================================================*/

        switch($tVhostType){
            case 'W': {
                $oConnection = new AMQPStreamConnection(MQ_CRD_HOST, MQ_CRD_PORT, MQ_CRD_USER, MQ_CRD_PASS, MQ_CRD_VHOST);
                $bAutoDelete = true;
                break;
            }
            case 'A': {
                $oConnection = new AMQPStreamConnection(MQ_AUD_HOST, MQ_AUD_PORT, MQ_AUD_USER, MQ_AUD_PASS, MQ_AUD_VHOST);
                $bAutoDelete = false;
                break;
            }
            case 'NOT': {
                $oConnection = new AMQPStreamConnection(MQ_NOT_HOST, MQ_NOT_PORT, MQ_NOT_USER, MQ_NOT_PASS, MQ_NOT_VHOST);
                $bAutoDelete = true;
                break;
            }
            default : {
                $oConnection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
                $bAutoDelete = false;
            }
        }
        $oChannel = $oConnection->channel();
        $oChannel->queue_declare($tQname, false, true, false, $bAutoDelete);
        $message = $oChannel->basic_get($tQname);

        if (!empty($message)) {
            if (!empty($message->body)) {
                $oChannel->basic_ack($message->delivery_info['delivery_tag']);
                $nProgress = $message->body;
            } else {
                $nProgress = 'end';
            }
        } else {
            $nProgress = 'false';
        }

        $oChannel->close();
        $oConnection->close();
        return $nProgress;
    } catch (Exception $Error) {
        return $Error;
    }
}

function FCNxRabbitMQCheckQueueMassage(array $paParams = []){
    try{
        /*===== Begin Set Config =======================================================*/
        $tQname = $paParams ['tQname'];
        $tVhostType = (isset($paParams ['tVhostType']) && in_array($paParams ['tVhostType'],['W','D']))?$paParams ['tVhostType']:'D';
        /*===== End Set Config =========================================================*/

        switch($tVhostType){
            case 'W': {
                $oConnection = new AMQPStreamConnection(MQ_CRD_HOST, MQ_CRD_PORT, MQ_CRD_USER, MQ_CRD_PASS, MQ_CRD_VHOST);
                $bAutoDelete = true;
                break;
            }
            default : {
                $oConnection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
                $bAutoDelete = false;
            }
        }

        $channel = $oConnection->channel();
        $channel->queue_declare($tQname, false, true, false, $bAutoDelete);
        $message = $channel->basic_get($tQname);

            if(!empty($message->body)){
                $nProgress = 'true' ;
            }else{
                $nProgress = 'false' ;
            }

        $channel->close();
        $oConnection->close();
        return $nProgress;
    }catch(Exception $Error){
        return $Error;
    }
}

//Functionality : Controller ในการ Get Last Massage
//Parameters    : Ajax input type post
//Creator       : 20/11/2020 (Nale)
//Return        : text
//Return Type   : srting
function FCNxRabbitMQGetLastQueueMassage($paData){
    try{
        $massgeBack = '';
        $nProgress='';
        do{
            $tQname     = $paData['tQname'];
            $tVhostType = (isset($paData['tVhostType']) && in_array($paData['tVhostType'],['W','D','A','NOT']))?$paData['tVhostType']:'D';
            /*===== End Set Config =========================================================*/
    
            switch($tVhostType){
                case 'W': {
                    $oConnection = new AMQPStreamConnection(MQ_CRD_HOST, MQ_CRD_PORT, MQ_CRD_USER, MQ_CRD_PASS, MQ_CRD_VHOST);
                    $bAutoDelete = true;
                    break;
                }
                case 'A': {
                    $oConnection = new AMQPStreamConnection(MQ_AUD_HOST, MQ_AUD_PORT, MQ_AUD_USER, MQ_AUD_PASS, MQ_AUD_VHOST);
                    $bAutoDelete = false;
                    break;
                }
                case 'NOT': {
                    $oConnection = new AMQPStreamConnection(MQ_NOT_HOST, MQ_NOT_PORT, MQ_NOT_USER, MQ_NOT_PASS, MQ_NOT_VHOST);
                    $bAutoDelete = true;
                    break;
                }
                default : {
                    $oConnection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
                    $bAutoDelete = false;
                }
            }
            $oChannel    = $oConnection->channel();
            $oChannel->queue_declare($tQname, false, true, false, $bAutoDelete);
            $message    = $oChannel->basic_get($tQname);
            if(!empty($message)){
                if(!empty($message->body)){
                    $oChannel->basic_ack($message->delivery_info['delivery_tag']);
                    $nProgress  = $message->body;
                    $massgeBack = $message->body;
                }else{
                    $nProgress = 'end';
                }
            }else{
                $nProgress = 'false';
            }
            $oChannel->close();
            $oConnection->close();
        }while($nProgress!='false');
        return $massgeBack;
    }catch(Exception $Error){
        return $Error;
    }
}

// Create By : Napat(Jame) 04/08/2021
function FCNaRabbitMQInterface($paParams){
    $ci = &get_instance();
    $ci->load->database();

    $tSQL = "   SELECT *
                FROM TLKMConfig WITH(NOLOCK)
                WHERE TLKMConfig.FTCfgKey = 'Noti'
                AND TLKMConfig.FTCfgSeq = '4' ";
    $oQuery = $ci->db->query($tSQL);
    if ( $oQuery->num_rows() > 0 ){
        $aConfigMQ      = $oQuery->result_array();
        $tHost          = $aConfigMQ[1]['FTCfgStaUsrValue'];
        $tPort          = $aConfigMQ[2]['FTCfgStaUsrValue'];
        $tPassword      = FCNtHAES128Decrypt($aConfigMQ[3]['FTCfgStaUsrValue']);
        $tQueueName     = $paParams['queueName']/*$aConfigMQ][4]['FTCfgStaUsrValue']*/;
        $tUser          = $aConfigMQ[5]['FTCfgStaUsrValue'];
        $tVHost         = $aConfigMQ[6]['FTCfgStaUsrValue'];
        $aParams        = $paParams['params'];

        $oConnection = new AMQPStreamConnection($tHost, $tPort, $tUser, $tPassword, $tVHost);
        $oChannel = $oConnection->channel();
        $oChannel->queue_declare($tQueueName, false, true, false, false);
        $oMessage = new AMQPMessage(json_encode($aParams));
        $oChannel->basic_publish($oMessage, "", $tQueueName);

        $oChannel->close();
        $oConnection->close();

        // echo "<pre>";
        // print_r($aConfigMQ);
        // print_r($oConnection);
        // echo $tPassword;

        $aReturnData  = array(
            'nStaEvent'     => '1',
            'tStaMessg'     => 'Success',
        );

    }else{
        $aReturnData  = array(
            'nStaEvent'     => '800',
            'tStaMessg'     => 'ไม่พบการตั้งค่า MQ กรุณาติดต่อผู้ดูแลระบบ',
        );
    }
    return $aReturnData;
}
