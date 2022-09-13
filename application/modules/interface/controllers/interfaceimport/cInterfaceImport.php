<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'libraries/rabbitmq/vendor/autoload.php');
require_once(APPPATH . 'config/rabbitmq.php');
require_once('././config_deploy.php');
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Message\AMQPMessage;

class cInterfaceimport extends MX_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model('interface/interfaceimport/mInterfaceImport');
    }

    public function index($nBrowseType, $tBrowseOption)
    {

        $aData['nBrowseType']                   = $nBrowseType;
        $aData['tBrowseOption']                 = $tBrowseOption;
        $aData['aAlwEventInterfaceImport']      = FCNaHCheckAlwFunc('interfaceimport/0/0'); //Controle Event
        $aData['vBtnSave']                      = FCNaHBtnSaveActiveHTML('interfaceimport/0/0'); //Load Html ของปุ่ม Save ที่เก็บ Session ปัจจุบัน
        $tLangEdit                              = $this->session->userdata("tLangEdit");
        $aData['aDataMasterImport']             = $this->mInterfaceImport->FSaMINMGetHD($tLangEdit);
        $tUserCode                              = $this->session->userdata('tSesUserCode');

        // $aParams = [
        //     'prefixQueueName'   => 'LK_RPTransferResponseSAP',
        //     'ptUsrCode'         => $tUserCode
        // ];
        // $this->FSxCINMRabbitMQDeleteQName($aParams);
        // $this->FSxCINMRabbitMQDeclareQName($aParams);
        $this->load->view('interface/interfaceimport/wInterfaceImport', $aData);
    }


    public function FSxCINMCallRabitMQ(){
        $tLangEdit  = $this->session->userdata("tLangEdit");
        $tTypeEvent = $this->input->post('ptTypeEvent');
        if ($tTypeEvent == 'getpassword') {
            $aResult = $this->mInterfaceImport->FSaMINMGetDataConfig($tLangEdit);
            $aConnect = array(
                'tHost'      => $aResult[0]['FTCfgStaUsrValue'],
                'tPort'      => $aResult[1]['FTCfgStaUsrValue'],
                'tPassword'  => $aResult[2]['FTCfgStaUsrValue'],
                'tUser'      => $aResult[4]['FTCfgStaUsrValue'],
                'tVHost'     => $aResult[5]['FTCfgStaUsrValue']
            );
			
            echo json_encode($aConnect);
        } else {
            $tPassword      = $this->input->post('tPassword');
            $aINMImport     = $this->input->post('ocmINMImport');

            if(!empty($aINMImport)){
                foreach($aINMImport as $tApiCode => $tApiGrpPrc){
                    // echo print_r($tApiCode);

                    $aMQParams = [
                        "queueName"     => "IM_SAPMProduct",
                        "exchangname"   => "",
                        "params"        => [
                            "ptFunction"    => $tApiGrpPrc,
                            "ptSource"      => "HQ.AdaStoreBack",
                            "ptDest"        => "BQ Process",
                            "ptFilter"      => $this->session->userdata('tSesUsrAgnCode'),
                            "ptData"        => [
                                "ptDateFrm"     => "",
                                "ptDateTo"      => ""
                            ]
                        ]
                    ];
                    $this->FCNxCallRabbitMQMaster($aMQParams, false, $tPassword);
                }
            }
            exit;
        }
    }

    function FSxCINMRabbitMQDeclareQName($paParams){
        $tPrefixQueueName = $paParams['prefixQueueName'];
        $tQueueName = $tPrefixQueueName;
        $oConnection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
        $oChannel = $oConnection->channel();
        $oChannel->queue_declare($tQueueName, false, true, false, false);
        $oChannel->close();
        $oConnection->close();
        return 1;
    }

    function FSxCINMRabbitMQDeleteQName($paParams){
        $tPrefixQueueName = $paParams['prefixQueueName'];
        $tQueueName = $tPrefixQueueName;
        $oConnection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
        $oChannel = $oConnection->channel();
        $oChannel->queue_delete($tQueueName);
        $oChannel->close();
        $oConnection->close();
        return 1;
    }

    function FCNxCallRabbitMQMaster($paParams, $pbStaUse = true, $ptPasswordMQ){

        $tLangEdit  = $this->session->userdata("tLangEdit");
        $aVal       = $this->mInterfaceImport->FSaMINMGetDataConfig($tLangEdit);
        $tHost      = $aVal[0]['FTCfgStaUsrValue'];
        $tPort      = $aVal[1]['FTCfgStaUsrValue'];
        $tPassword  = $aVal[2]['FTCfgStaUsrValue'];
        $tQueueName = $aVal[3]['FTCfgStaUsrValue'];
        $tUser      = $aVal[4]['FTCfgStaUsrValue'];
        $tVHost     = $aVal[5]['FTCfgStaUsrValue'];

        if($tQueueName==''){
            $tQueueName = $paParams['queueName'];
        }
        $aParams    = $paParams['params'];
        if ($pbStaUse == true) {
            $aParams['ptConnStr']   = DB_CONNECT;
        }

        //ถ้ามีการเซตแบบ SSL ต้องวิ่งอีกแบบ AMQPSSLConnection
        $tRabbitHelper = '';
        if(defined('RABBITSSL')){
            if(RABBITSSL == true || RABBITSSL == 1){ //กรณีต้องการ connect MQ แบบ SSL
                $tRabbitHelper = 'rabbitMQSSL';
            }else{
                $tRabbitHelper = 'rabbitMQ';
            }
        }else{
            $tRabbitHelper = 'rabbitMQ';
        }   

        if($tRabbitHelper == 'rabbitMQSSL'){
            $aSsl_options = array(
                'allow_self_signed' => false,
                'verify_peer' 		=> false,
                'verify_peer_name' 	=> false
            );
            $oConnection    = new AMQPSSLConnection($tHost, $tPort,  $tUser, $ptPasswordMQ, $tVHost , $aSsl_options);
        }else{
            $oConnection    = new AMQPStreamConnection($tHost, $tPort,  $tUser, $ptPasswordMQ, $tVHost);
        }

        $oChannel       = $oConnection->channel();
        $oChannel->queue_declare($tQueueName, false, true, false, false);
        $oMessage       = new AMQPMessage(json_encode($aParams));
        $oChannel->basic_publish($oMessage, "", $tQueueName);
        $oChannel->close();
        $oConnection->close();
        return 1;
    }

}
