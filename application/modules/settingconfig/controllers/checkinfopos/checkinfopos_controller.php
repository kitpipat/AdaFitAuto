<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
date_default_timezone_set("Asia/Bangkok");

class checkinfopos_controller extends MX_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('settingconfig/checkinfopos/checkinfopos_model');
    }

    public function index($nBrowseType, $tBrowseOption){
        $aAlwEvent  = FCNaHCheckAlwFunc('CheckInfoPos/0/0');
        $this->load->view('settingconfig/checkinfopos/wCheckinfopos',array(
            'nBrowseType'   => $nBrowseType,
            'tBrowseOption' => $tBrowseOption,
            'aAlwEvent'     => $aAlwEvent
        ));
    }

    public function FSvCCIPListPage(){
        $aData['aAlwEvent']     = FCNaHCheckAlwFunc('CheckInfoPos/0/0');
        $this->load->view('settingconfig/checkinfopos/wCheckinfoposList',$aData);    
    }

    //Functionality : Call Page Data List
    //Parameters : Ajax
    //Creator : 22/06/2022 wasin
    //Return : String View
    //Return Type : View
    public function FSvCCIPDataList(){
        $nPage      = $this->input->post('nPageCurrent');
        $tSearchAll = $this->input->post('tSearchAll');        
        if($nPage == '' || $nPage == null){$nPage = 1;}else{$nPage = $this->input->post('nPageCurrent');}
        if(!$tSearchAll){$tSearchAll='';}
        //Lang ภาษา
	    $nLangEdit  = $this->session->userdata("tLangEdit");
        $aData      = array(
            'nPage'         => $nPage,
            'nRow'          => 10,
            'FNLngID'       => $nLangEdit,
            'tSearchAll'    => $tSearchAll,
            'tSesAgnCode'   => $this->session->userdata("tSesUsrAgnCode"),
        );
        $aCIPList   = $this->checkinfopos_model->FSaMCIPDataList($aData);
        $aAlwEvent  = FCNaHCheckAlwFunc('CheckInfoPos/0/0');
        $aGenTable  = array(
            'aAlwEvent'         => $aAlwEvent,
            'aDataList'         => $aCIPList,
            'nPage'             => $nPage,
            'tSearchAll'        => $tSearchAll,
            'nOptDecimalShow'   => get_cookie('tOptDecimalShow')
        );
        $this->load->view('settingconfig/checkinfopos/wCheckinfoposDataTable',$aGenTable);
    }




}