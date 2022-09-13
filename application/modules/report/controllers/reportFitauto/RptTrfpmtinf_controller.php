<?php
defined('BASEPATH') or exit('No direct script access allowed');

date_default_timezone_set("Asia/Bangkok");

include APPPATH . 'third_party/PHPExcel/Classes/PHPExcel.php';
include APPPATH . 'third_party/PHPExcel/Classes/PHPExcel/IOFactory.php';
include APPPATH . 'third_party/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';
include APPPATH . 'libraries/spout-3.1.0/src/Spout/Autoloader/autoload.php';

use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class RptTrfpmtinf_controller extends MX_Controller{

    /**
     * ภาษา
     * @var array
    */
    public $aText       = [];

    /**
     * จำนวนต่อหน้าในรายงาน
     * @var int
    */
    public $nPerPage    = 100;

    /**
     * Page number
     * @var int
    */
    public $nPage       = 1;

    /**
     * จำนวนทศนิยม
     * @var int
    */
    public $nOptDecimalShow = 2;

    /**
     * จำนวนข้อมูลใน Temp
     * @var int
    */
    public $nRows   = 0;

    /**
     * Computer Name
     * @var string
    */
    public $tCompName;

    /**
     * User Login on Bch
     * @var string
    */
    public $tBchCodeLogin;

    /**
     * Report Code
     * @var string
    */
    public $tRptCode;

    /**
     * Report Group
     * @var string
    */
    public $tRptGroup;

    /**
     * System Language
     * @var int
    */
    public $nLngID;

    /**
     * User Session ID
     * @var string
    */
    public $tUserSessionID;

    /**
     * Report route
     * @var string
    */
    public $tRptRoute;

    /**
     * Report Export Type
     * @var string
    */
    public $tRptExportType;

    /**
     * Filter for Report
     * @var array
    */
    public $aRptFilter      = [];

    /**
     * Company Info
     * @var array
    */
    public $aCompanyInfo    = [];

    /**
     * User Login Session
     * @var string
    */
    public $tUserLoginCode;

    /**
     * Sys Bch Code
     * @var string
    */
    public $tSysBchCode;


    public function __construct(){
        parent::__construct();
        $this->load->helper('report');
        $this->load->model('company/company/mCompany');
        $this->load->model('report/report/mReport');
        $this->load->model('report/reportFitauto/RptTrfpmtinf_model');

        // Init Report
        $this->init();
        parent::__construct();
    }

    private function init(){
        $this->aText    = [
            'tTitleReport'              => language('report/report/report', 'tRptTrfPmtInfTitle'),
            'tDatePrint'                => language('report/report/report', 'tRptTrfPmtInfDatePrint'),
            'tTimePrint'                => language('report/report/report', 'tRptTrfPmtInfTimePrint'),
            'tRptAddrRoad'              => language('report/report/report', 'tRptAddrRoad'),
            'tRptAddrSoi'               => language('report/report/report', 'tRptAddrSoi'),
            'tRptAddrSubDistrict'       => language('report/report/report', 'tRptAddrSubDistrict'),
            'tRptAddrDistrict'          => language('report/report/report', 'tRptAddrDistrict'),
            'tRptAddrProvince'          => language('report/report/report', 'tRptAddrProvince'),
            'tRptAddrTel'               => language('report/report/report', 'tRptAddrTel'),
            'tRptAddrFax'               => language('report/report/report', 'tRptAddrFax'),
            'tRptAddrBranch'            => language('report/report/report', 'tRptAddrBranch'),
            'tRptAddV2Desc1'            => language('report/report/report', 'tRptAddV2Desc1'),
            'tRptAddV2Desc2'            => language('report/report/report', 'tRptAddV2Desc2'),
            'tRPCTaxNo'                 => language('report/report/report', 'tRPCTaxNo'),
            'tRptFaxNo'                 => language('report/report/report', 'tRptFaxNo'),
            'tRptTel'                   => language('report/report/report', 'tRptTel'),
            'tRptConditionInReport'     => language('report/report/report', 'tRptConditionInReport'),
            'tRptNoData'                => language('report/report/report', 'tRptNoData'),
            'tRptAdjDateFrom'           => language('report/report/report', 'tRptAdjDateFrom'),
            'tRptAdjDateTo'             => language('report/report/report', 'tRptAdjDateTo'),
            // Title Header Excel
            'tRptTrfPmtInfBchCodeForm'  => language('report/report/report', 'tRptTrfPmtInfBchCodeForm'),
            'tRptTrfPmtInfBchNameForm'  => language('report/report/report', 'tRptTrfPmtInfBchNameForm'),
            'tRptTrfPmtInfDocNoForm'    => language('report/report/report', 'tRptTrfPmtInfDocNoForm'),
            'tRptTrfPmtInfDocDateForm'  => language('report/report/report', 'tRptTrfPmtInfDocDateForm'),
            'tRptTrfPmtInfBchCodeTo'    => language('report/report/report', 'tRptTrfPmtInfBchCodeTo'),
            'tRptTrfPmtInfBchNameTo'    => language('report/report/report', 'tRptTrfPmtInfBchNameTo'),
            'tRptTrfPmtInfDocNoTo'      => language('report/report/report', 'tRptTrfPmtInfDocNoTo'),
            'tRptTrfPmtInfDocDateTo'    => language('report/report/report', 'tRptTrfPmtInfDocDateTo'),
            'tRptTrfPmtInfPdtCode'      => language('report/report/report', 'tRptTrfPmtInfPdtCode'),
            'tRptTrfPmtInfPdtName'      => language('report/report/report', 'tRptTrfPmtInfPdtName'),
            'tRptTrfPmtInfPdtUnit'      => language('report/report/report', 'tRptTrfPmtInfPdtUnit'),
            'tRptTrfPmtInfPdtGroup'     => language('report/report/report', 'tRptTrfPmtInfPdtGroup'),
            'tRptTrfPmtInfPdtType'      => language('report/report/report', 'tRptTrfPmtInfPdtType'),
            'tRptTrfPmtInfPdtCat1'      => language('report/report/report', 'tRptTrfPmtInfPdtCat1'),
            'tRptTrfPmtInfPdtCat2'      => language('report/report/report', 'tRptTrfPmtInfPdtCat2'),
            'tRptTrfPmtInfXtdQty'       => language('report/report/report', 'tRptTrfPmtInfXtdQty'),
        ];
        $this->tSysBchCode      = SYS_BCH_CODE;
        $this->tBchCodeLogin    = (!empty($this->session->userdata('tSesUsrBchCom')) ? $this->session->userdata('tSesUsrBchCom') : $this->session->userdata('tSesUsrBchCom'));
        $this->nPerPage         = 100;
        $this->nOptDecimalShow  = get_cookie('tOptDecimalShow');
        $tIP                    = $this->input->ip_address();
        $tFullHost              = gethostbyaddr($tIP);
        $this->tCompName        = $tFullHost;
        $this->nLngID           = FCNaHGetLangEdit();
        $this->tRptCode         = $this->input->post('ohdRptCode');
        $this->tRptGroup        = $this->input->post('ohdRptGrpCode');
        $this->tUserSessionID   = $this->session->userdata('tSesSessionID');
        $this->tRptRoute        = $this->input->post('ohdRptRoute');
        $this->tRptExportType   = $this->input->post('ohdRptTypeExport');
        $this->nPage            = empty($this->input->post('ohdRptCurrentPage')) ? 1 : $this->input->post('ohdRptCurrentPage');
        $this->tUserLoginCode   = $this->session->userdata('tSesUsername');
        // Report Fillter
        $this->aRptFilter   = [
            'tSessionID'        => $this->tUserSessionID,
            'tCompName'         => $this->tCompName,
            'tRptCode'          => $this->tRptCode,
            'nLangID'           => $this->nLngID,
            'tTypeSelect'       => !empty($this->input->post('ohdTypeDataCondition'))   ? $this->input->post('ohdTypeDataCondition')    : "",
            // Fillter Agency (ตัวแทนขาย)
            'tAgnCodeSelect'    => !empty($this->input->post('oetSpcAgncyCode'))        ? $this->input->post('oetSpcAgncyCode')         : "",
            // Filter BCH (สาขา)
            'tBchCodeFrom'      => !empty($this->input->post('oetRptBchCodeFrom'))      ? $this->input->post('oetRptBchCodeFrom')       : "",
            'tBchNameFrom'      => !empty($this->input->post('oetRptBchNameFrom'))      ? $this->input->post('oetRptBchNameFrom')       : "",
            'tBchCodeTo'        => !empty($this->input->post('oetRptBchCodeTo'))        ? $this->input->post('oetRptBchCodeTo')         : "",
            'tBchNameTo'        => !empty($this->input->post('oetRptBchNameTo'))        ? $this->input->post('oetRptBchNameTo')         : "",
            'tBchCodeSelect'    => !empty($this->input->post('oetRptBchCodeSelect'))    ? $this->input->post('oetRptBchCodeSelect')     : "",
            'tBchNameSelect'    => !empty($this->input->post('oetRptBchNameSelect'))    ? $this->input->post('oetRptBchNameSelect')     : "",
            'bBchStaSelectAll'  => !empty($this->input->post('oetRptBchStaSelectAll')) && ($this->input->post('oetRptBchStaSelectAll') == 1) ? true : false,
            // Filter Document Date (วันที่สร้างเอกสาร)
            'tDocDateFrom'      => (empty($this->input->post('oetRptDocDateFrom')))     ? ''    : $this->input->post('oetRptDocDateFrom'),
            'tDocDateTo'        => (empty($this->input->post('oetRptDocDateTo')))       ? ''    : $this->input->post('oetRptDocDateTo'),
            // สินค้า
            'tPdtCodeFrom'      => !empty($this->input->post('oetRptPdtCodeFrom'))      ? $this->input->post('oetRptPdtCodeFrom')   : "",
            'tPdtNameFrom'      => !empty($this->input->post('oetRptPdtNameFrom'))      ? $this->input->post('oetRptPdtNameFrom')   : "",
            'tPdtCodeTo'        => !empty($this->input->post('oetRptPdtCodeTo'))        ? $this->input->post('oetRptPdtCodeTo')     : "",
            'tPdtNameTo'        => !empty($this->input->post('oetRptPdtNameTo'))        ? $this->input->post('oetRptPdtNameTo')     : "",
        ];
        // ดึงข้อมูลบริษัทฯ
        $aCompInfoParams    = [
            'nLngID'        => $this->nLngID,
            'tBchCode'      => $this->tBchCodeLogin
        ];
        $this->aCompanyInfo = FCNaGetCompanyInfo($aCompInfoParams)['raItems'];
    }

    public function index(){
        if (!empty($this->tRptExportType) && !empty($this->tRptCode)) {
            $this->RptTrfpmtinf_model->FSxMExecStoreReport($this->aRptFilter);
            switch ($this->tRptExportType) {
                case 'html':
                    //ไม่เอา preview
                    break;
                case 'excel':
                    $this->FSvCCallRptExportFile();
                    break;
            }
        }
    }

    // Excel : ส่วนกลาง
    public function FSvCCallRptExportFile(){
        $tFileName  = $this->aText['tTitleReport'] . '_' . date('YmdHis') . '.xlsx';
        $oWriter    = WriterEntityFactory::createXLSXWriter();
        // stream data directly to the browser
        $oWriter->openToBrowser($tFileName);

        //เรียกฟังชั่นสร้างส่วนหัวรายงาน
        $aMulltiRow = $this->FSoCCallRptRenderHedaerExcel();
        $oWriter->addRows($aMulltiRow);

        $oBorder = (new BorderBuilder())
            ->setBorderTop(Color::BLACK, Border::WIDTH_THIN)
            ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN)
            ->build();

        $oStyleColums = (new StyleBuilder())
            ->setBorder($oBorder)
            ->build();
            
        $aCells = [
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfBchCodeForm')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfBchNameForm')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfDocNoForm')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfDocDateForm')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfBchCodeTo')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfBchNameTo')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfDocNoTo')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfDocDateTo')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfPdtCode')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfPdtName')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfPdtUnit')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfPdtGroup')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfPdtType')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfPdtCat1')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfPdtCat2')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptTrfPmtInfXtdQty')),
        ];

        /** add a row at a time */
        $singleRow = WriterEntityFactory::createRow($aCells, $oStyleColums);
        $oWriter->addRow($singleRow);
 
        $aDataReportParams = [
            'nPerPage'      => 999999999999,
            'nPage'         => $this->nPage,
            'tCompName'     => $this->tCompName,
            'tRptCode'      => $this->tRptCode,
            'tUsrSessionID' => $this->tUserSessionID,
            'aDataFilter'   => $this->aRptFilter
        ];

        $aDataReport    = $this->RptTrfpmtinf_model->FSaMGetDataReport($aDataReportParams);

        /** Create a style with the StyleBuilder */
        $oStyle = (new StyleBuilder())
            ->setCellAlignment(CellAlignment::RIGHT)
            ->build();
    
        if(isset($aDataReport['aRptData']) && !empty($aDataReport['aRptData'])) {
            foreach ($aDataReport['aRptData'] as $nKey => $aValue) {
                $values = [
                    WriterEntityFactory::createCell($aValue['FTBchRefIDFrm']),
                    WriterEntityFactory::createCell($aValue['FTBchNameFrm']),
                    WriterEntityFactory::createCell($aValue['FTXthDocNoFrm']),
                    WriterEntityFactory::createCell($aValue['FDXthDocDateFrm']),
                    WriterEntityFactory::createCell($aValue['FTBchRefIDTo']),
                    WriterEntityFactory::createCell($aValue['FTBchNameTo']),
                    WriterEntityFactory::createCell($aValue['FTXthDocNoTo']),
                    WriterEntityFactory::createCell($aValue['FDXthDocDateTo']),
                    WriterEntityFactory::createCell($aValue['FTPdtCode']),
                    WriterEntityFactory::createCell($aValue['FTXtdPdtName']),
                    WriterEntityFactory::createCell($aValue['FTPunName']),
                    WriterEntityFactory::createCell($aValue['FTPgpChainName']),
                    WriterEntityFactory::createCell($aValue['FTPtyName']),
                    WriterEntityFactory::createCell($aValue['FTPdtCatName1']),
                    WriterEntityFactory::createCell($aValue['FTPdtCatName2']),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXtdQty'])),
                ];
                $aRow   = WriterEntityFactory::createRow($values);
                $oWriter->addRow($aRow);
            }
        }
        $aMulltiRow = $this->FSoCCallRptRenderFooterExcel(); //เรียกฟังชั่นสร้างส่วนท้ายรายงาน
        $oWriter->addRows($aMulltiRow);
        $oWriter->close();
    }

    public function FSoCCallRptRenderHedaerExcel(){
        if (isset($this->aCompanyInfo) && count($this->aCompanyInfo)>0) {
            $tFTAddV1Village    = $this->aCompanyInfo['FTAddV1Village']; 
            $tFTCmpName         = $this->aCompanyInfo['FTCmpName'];
            $tFTAddV1No         = $this->aCompanyInfo['FTAddV1No'];
            $tFTAddV1Road       = $this->aCompanyInfo['FTAddV1Road'];
            $tFTAddV1Soi        = $this->aCompanyInfo['FTAddV1Soi'];
            $tFTSudName         = $this->aCompanyInfo['FTSudName'];
            $tFTDstName         = $this->aCompanyInfo['FTDstName'];
            $tFTPvnName         = $this->aCompanyInfo['FTPvnName'];
            $tFTAddV1PostCode   = $this->aCompanyInfo['FTAddV1PostCode'];
            $tFTAddV2Desc1      = $this->aCompanyInfo['FTAddV2Desc1'];
            $tFTAddV2Desc2      = $this->aCompanyInfo['FTAddV2Desc2'];
            $tFTAddVersion      = $this->aCompanyInfo['FTAddVersion'];
            $tFTBchName         = $this->aCompanyInfo['FTBchName'];
            $tFTAddTaxNo        = $this->aCompanyInfo['FTAddTaxNo'];
            $tFTCmpTel          = $this->aCompanyInfo['FTAddTel'];
            $tRptFaxNo          = $this->aCompanyInfo['FTAddFax'];
        } else {
            $tFTCmpTel          = "";
            $tFTCmpName         = "";
            $tFTAddV1No         = "";
            $tFTAddV1Road       = "";
            $tFTAddV1Soi        = "";
            $tFTSudName         = "";
            $tFTDstName         = "";
            $tFTPvnName         = "";
            $tFTAddV1PostCode   = "";
            $tFTAddV2Desc1      = "1";
            $tFTAddV1Village    = "";
            $tFTAddV2Desc2      = "2";
            $tFTAddVersion      = "";
            $tFTBchName         = "";
            $tFTAddTaxNo        = "";
            $tRptFaxNo          = "";
        }


        $oStyle = (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(12)
            ->build();

        $aCells = [
            WriterEntityFactory::createCell($tFTCmpName),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell($this->aText['tTitleReport']),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells, $oStyle);

        $tAddress = '';
        if ($tFTAddVersion == '1') {
            $tAddress = $tFTAddV1No . ' ' .$tFTAddV1Village. ' '.$tFTAddV1Road.' ' . $tFTAddV1Soi . ' ' . $tFTSudName . ' ' . $tFTDstName . ' ' . $tFTPvnName . ' ' . $tFTAddV1PostCode;
        }
        if ($tFTAddVersion == '2') {
            $tAddress = $tFTAddV2Desc1 . ' ' . $tFTAddV2Desc2;
        }

        $aCells = [
            WriterEntityFactory::createCell($tAddress),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);

        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptTel'] . ' ' . $tFTCmpTel . ' '.$this->aText['tRptFaxNo'] . ' ' . $tRptFaxNo),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
        ];

        $aMulltiRow[]  = WriterEntityFactory::createRow($aCells);

        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptAddrBranch'] . ' ' . $tFTBchName),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);


        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRPCTaxNo'] . ' : ' . $tFTAddTaxNo),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);

        $aCells = [
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);


        if ((isset($this->aRptFilter['tDocDateFrom']) && !empty($this->aRptFilter['tDocDateFrom'])) && (isset($this->aRptFilter['tDocDateTo']) && !empty($this->aRptFilter['tDocDateTo']))) {
            $aCells = [
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell($this->aText['tRptAdjDateFrom'] . ' ' . date('d/m/Y', strtotime($this->aRptFilter['tDocDateFrom'])) . ' ' . $this->aText['tRptAdjDateTo'] . ' ' . date('d/m/Y', strtotime($this->aRptFilter['tDocDateTo']))),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        $aCells = [
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell($this->aText['tDatePrint'] . ' ' . date('d/m/Y') . ' ' . $this->aText['tTimePrint'] . ' ' . date('H:i:s')),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        return $aMulltiRow;
    }
      
    public function FSoCCallRptRenderFooterExcel(){
        $oStyleFilter = (new StyleBuilder())
            ->setFontBold()
            ->build();

        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptConditionInReport']),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells, $oStyleFilter);

        if (isset($this->aRptFilter['tBchCodeSelect']) && !empty($this->aRptFilter['tBchCodeSelect'])) {
            $tBchSelect =  ($this->aRptFilter['bBchStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tBchNameSelect'];
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptBchFrom'] . ' : ' . $tBchSelect),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        // เครื่องจุดขาย (Pos) แบบเลือก
        if (!empty($this->aRptFilter['tPosCodeSelect'])) {
            $tPosSelectText = ($this->aRptFilter['bPosStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tPosNameSelect'];
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptPosFrom'] . ' : ' . $tPosSelectText),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        //กลุ่มธุรกิจ
        if (isset($this->aRptFilter['tMerCodeSelect']) && !empty($this->aRptFilter['tMerCodeSelect'])) {
            $tMerSelect =  ($this->aRptFilter['bMerStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tMerNameSelect'];
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptMerFrom'] . ' : ' . $tMerSelect),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        //ร้านค้า (Shop)
        if (isset($this->aRptFilter['tShpCodeSelect']) && !empty($this->aRptFilter['tShpCodeSelect'])) {
            $tShpSelect =  ($this->aRptFilter['bShpStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tShpNameSelect'];
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptShopFrom'] . ' : ' . $tShpSelect),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        if ((isset($this->aRptFilter['tPdtCodeFrom']) && !empty($this->aRptFilter['tPdtCodeFrom'])) && (isset($this->aRptFilter['tPdtCodeTo']) && !empty($this->aRptFilter['tPdtCodeTo']))) {
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tPdtCodeFrom'] . ' : ' . $this->aRptFilter['tPdtNameFrom'] . ' ' . $this->aText['tPdtCodeTo'] . ' : ' . $this->aRptFilter['tPdtNameTo']),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
            ];

            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        return $aMulltiRow;
    }









}