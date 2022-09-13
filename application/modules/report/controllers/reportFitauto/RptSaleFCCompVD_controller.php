<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Bangkok");
include APPPATH . 'libraries/spout-3.1.0/src/Spout/Autoloader/autoload.php';
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
date_default_timezone_set("Asia/Bangkok");

class RptSaleFCCompVD_controller extends MX_Controller {
    
    /**
     * ภาษา
     * @var array
    */
    public $aText   = [];

    /**
     * จำนวนต่อหน้าในรายงาน
     * @var int
    */
    public $nPerPage    = 100;

    /**
     * Page number
     * @var int
    */
    public $nPage   = 1;

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
        $this->load->model('company/company/mCompany');
        $this->load->model('report/report/mReport');
        $this->load->model('report/reportFitauto/RptSaleFCCompVD_model');
        // Init Report
        $this->init();
        parent::__construct();
    }

    private function init(){
        $this->aText    = [
            // TitleReport
            'tTitleReport'          => language('report/report/report', 'tRptSaleFCCompVDTitle'),
            'tDatePrint'            => language('report/report/report', 'tRptSaleFCCompVDDatePrint'),
            'tTimePrint'            => language('report/report/report', 'tRptSaleFCCompVDTimePrint'),

            // Filter Heard Report
            'tRptBchFrom'           => language('report/report/report', 'tRptBchFrom'),
            'tRptBchTo'             => language('report/report/report', 'tRptBchTo'),
            'tRptShopFrom'          => language('report/report/report', 'tRptShopFrom'),
            'tRptShopTo'            => language('report/report/report', 'tRptShopTo'),
            'tPdtCodeFrom'          => language('report/report/report', 'tPdtCodeFrom'),
            'tPdtCodeTo'            => language('report/report/report', 'tPdtCodeTo'),
            'tPdtGrpFrom'           => language('report/report/report', 'tPdtGrpFrom'),
            'tPdtGrpTo'             => language('report/report/report', 'tPdtGrpTo'),
            'tPdtTypeFrom'          => language('report/report/report', 'tPdtTypeFrom'),
            'tPdtTypeTo'            => language('report/report/report', 'tPdtTypeTo'),
            'tRptDateFrom'          => language('report/report/report', 'tRptDateFrom'),
            'tRptDateTo'            => language('report/report/report', 'tRptDateTo'),

            // Address Language
            'tRptAddrBuilding'      => language('report/report/report', 'tRptAddrBuilding'),
            'tRptAddrRoad'          => language('report/report/report', 'tRptAddrRoad'),
            'tRptAddrSoi'           => language('report/report/report', 'tRptAddrSoi'),
            'tRptAddrSubDistrict'   => language('report/report/report', 'tRptAddrSubDistrict'),
            'tRptAddrDistrict'      => language('report/report/report', 'tRptAddrDistrict'),
            'tRptAddrProvince'      => language('report/report/report', 'tRptAddrProvince'),
            'tRptAddrTel'           => language('report/report/report', 'tRptAddrTel'),
            'tRptAddrFax'           => language('report/report/report', 'tRptAddrFax'),
            'tRptAddrBranch'        => language('report/report/report', 'tRptAddrBranch'),
            'tRptAddV2Desc1'        => language('report/report/report', 'tRptAddV2Desc1'),
            'tRptAddV2Desc2'        => language('report/report/report', 'tRptAddV2Desc2'),
            'tRptFaxNo'             => language('report/report/report', 'tRptAddrFax'),
            'tRptTel'               => language('report/report/report', 'tRptAddrTel'),

            //  Table Label
            'tRptSaleFCCompVDAgency'        => language('report/report/report', 'tRptSaleFCCompVDAgency'),
            'tRptSaleFCCompVDBranch'        => language('report/report/report', 'tRptSaleFCCompVDBranch'),
            'tRptSaleFCCompVDPdtCode'       => language('report/report/report', 'tRptSaleFCCompVDPdtCode'),
            'tRptSaleFCCompVDPdtName'       => language('report/report/report', 'tRptSaleFCCompVDPdtName'),
            'tRptSaleFCCompVDPdtGroup'      => language('report/report/report', 'tRptSaleFCCompVDPdtGroup'),
            'tRptSaleFCCompVDPdtType'       => language('report/report/report', 'tRptSaleFCCompVDPdtType'),
            'tRptSaleFCCompVDPdtCat1'       => language('report/report/report', 'tRptSaleFCCompVDPdtCat1'),
            'tRptSaleFCCompVDPdtCat2'       => language('report/report/report', 'tRptSaleFCCompVDPdtCat2'),
            'tRptSaleFCCompVDBuyForHQ'      => language('report/report/report', 'tRptSaleFCCompVDBuyForHQ'),
            'tRptSaleFCCompVDBuyForVD'      => language('report/report/report', 'tRptSaleFCCompVDBuyForVD'),
            'tRptSaleFCCompVDPurchaseAmt'   => language('report/report/report', 'tRptSaleFCCompVDPurchaseAmt'),
            'tRptSaleFCCompVDPercentAmt'    => '%'.' '.language('report/report/report', 'tRptSaleFCCompVDPercentAmt'),
            
            // No Data Report
            'tRptNoData'            => language('common/main/main', 'tCMNNotFoundData'),

            //อัพเดทใหม่ 18/11/2019 Napat
            'tRptPosTypeName'       => language('report/report/report', 'tRptPosTypeName'),
            'tRptPosType'           => language('report/report/report', 'tRptPosType'),
            'tRptPosType1'          => language('report/report/report', 'tRptPosType1'),
            'tRptPosType2'          => language('report/report/report', 'tRptPosType2'),

            'tRptPdtCode'           => language('report/report/report', 'tRptPdtCode'),
            'tRptPdtName'           => language('report/report/report', 'tRptPdtName'),
            'tRptPdtGrp'            => language('report/report/report', 'tRptPdtGrp'),
            'tRptQty'               => language('report/report/report', 'tRptQty'),
            'tRptUnit'              => language('report/report/report', 'tRptUnit'),
            'tRptAveragePrice'      => language('report/report/report', 'tRptAveragePrice'),

            'tRptAdjMerChantFrom'   => language('report/report/report', 'tRptAdjMerChantFrom'),
            'tRptAdjMerChantTo'     => language('report/report/report', 'tRptAdjMerChantTo'),
            'tRptAdjShopFrom'       => language('report/report/report', 'tRptAdjShopFrom'),
            'tRptAdjShopTo'         => language('report/report/report', 'tRptAdjShopTo'),
            'tRptAdjPosFrom'        => language('report/report/report', 'tRptAdjPosFrom'),
            'tRptAdjPosTo'          => language('report/report/report', 'tRptAdjPosTo'),
            'tRptBranch'            => language('report/report/report', 'tRptBranch'),
            'tRptTotal'             => language('report/report/report', 'tRptTotal'),
            'tRPCTaxNo'             => language('report/report/report', 'tRPCTaxNo'),
            'tRptConditionInReport' => language('report/report/report', 'tRptConditionInReport'),
            'tRptMerFrom'           => language('report/report/report', 'tRptMerFrom'),
            'tRptMerTo'             => language('report/report/report', 'tRptMerTo'),
            'tRptShopFrom'          => language('report/report/report', 'tRptShopFrom'),
            'tRptShopTo'            => language('report/report/report', 'tRptShopTo'),
            'tRptPosFrom'           => language('report/report/report', 'tRptPosFrom'),
            'tRptPosTo'             => language('report/report/report', 'tRptPosTo'),
            'tPdtCodeTo'            => language('report/report/report', 'tPdtCodeTo'),
            'tPdtCodeFrom'          => language('report/report/report', 'tPdtCodeFrom'),
            'tPdtGrpFrom'           => language('report/report/report', 'tPdtGrpFrom'),
            'tPdtGrpTo'             => language('report/report/report', 'tPdtGrpTo'),
            'tPdtTypeFrom'          => language('report/report/report', 'tPdtTypeFrom'),
            'tPdtTypeTo'            => language('report/report/report', 'tPdtTypeTo'),
            'tRptAdjWahFrom'        => language('report/report/report', 'tRptAdjWahFrom'),
            'tRptAdjWahTo'          => language('report/report/report', 'tRptAdjWahTo'),
            'tRptAll'               => language('report/report/report', 'tRptAll'),

            'tRptTaxSalePosTaxId'   => language('report/report/report', 'tRptTaxSalePosTaxId'),
            'tRptDatePrint'         => language('report/report/report', 'tRptDatePrint'),
            'tRptTimePrint'         => language('report/report/report', 'tRptTimePrint'),
            'tRptTotalAllSale'      => language('report/report/report', 'tRptTotalAllSale'),
            'tRptTaxPointByCstDocDateFrom'  => language('report/report/report', 'tRptTaxPointByCstDocDateFrom'),
            'tRptTaxPointByCstDocDateTo'    => language('report/report/report', 'tRptTaxPointByCstDocDateTo'),

            'tRptTotalSub'      => language('report/report/report', 'tRptTotalSub'),
            'tRptTotalFooter'   => language('report/report/report', 'tRptTotalFooter'),
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
        // Report Filter
        $this->aRptFilter       = [
            'tUserSession'  => $this->tUserSessionID,
            'tCompName'     => $tFullHost,
            'tRptCode'      => $this->tRptCode,
            'nLangID'       => $this->nLngID,
            'tTypeSelect'   => !empty($this->input->post('ohdTypeDataCondition')) ? $this->input->post('ohdTypeDataCondition') : "",

            // สาขา(Branch)
            'tBchCodeFrom'      => !empty($this->input->post('oetRptBchCodeFrom')) ? $this->input->post('oetRptBchCodeFrom') : "",
            'tBchNameFrom'      => !empty($this->input->post('oetRptBchNameFrom')) ? $this->input->post('oetRptBchNameFrom') : "",
            'tBchCodeTo'        => !empty($this->input->post('oetRptBchCodeTo')) ? $this->input->post('oetRptBchCodeTo') : "",
            'tBchNameTo'        => !empty($this->input->post('oetRptBchNameTo')) ? $this->input->post('oetRptBchNameTo') : "",
            'tBchCodeSelect'    => !empty($this->input->post('oetRptBchCodeSelect')) ? $this->input->post('oetRptBchCodeSelect') : "",
            'tBchNameSelect'    => !empty($this->input->post('oetRptBchNameSelect')) ? $this->input->post('oetRptBchNameSelect') : "",
            'bBchStaSelectAll'  => !empty($this->input->post('oetRptBchStaSelectAll')) && ($this->input->post('oetRptBchStaSelectAll') == 1) ? true : false,

            // ร้านค้า(Shop)
            'tShpCodeFrom'      => !empty($this->input->post('oetRptShpCodeFrom')) ? $this->input->post('oetRptShpCodeFrom') : "",
            'tShpNameFrom'      => !empty($this->input->post('oetRptShpNameFrom')) ? $this->input->post('oetRptShpNameFrom') : "",
            'tShpCodeTo'        => !empty($this->input->post('oetRptShpCodeTo')) ? $this->input->post('oetRptShpCodeTo') : "",
            'tShpNameTo'        => !empty($this->input->post('oetRptShpNameTo')) ? $this->input->post('oetRptShpNameTo') : "",
            'tShpCodeSelect'    => !empty($this->input->post('oetRptShpCodeSelect')) ? $this->input->post('oetRptShpCodeSelect') : "",
            'tShpNameSelect'    => !empty($this->input->post('oetRptShpNameSelect')) ? $this->input->post('oetRptShpNameSelect') : "",
            'bShpStaSelectAll'  => !empty($this->input->post('oetRptShpStaSelectAll')) && ($this->input->post('oetRptShpStaSelectAll') == 1) ? true : false,

            // วันที่เอกสาร(DocNo)
            'tDocDateFrom'      => !empty($this->input->post('oetRptDocDateFrom')) ? $this->input->post('oetRptDocDateFrom') : "",
            'tDocDateTo'        => !empty($this->input->post('oetRptDocDateTo')) ? $this->input->post('oetRptDocDateTo') : "",

            // สินค้า
            'tPdtCodeFrom'      => !empty($this->input->post('oetRptPdtCodeFrom')) ? $this->input->post('oetRptPdtCodeFrom') : "",
            'tPdtNameFrom'      => !empty($this->input->post('oetRptPdtNameFrom')) ? $this->input->post('oetRptPdtNameFrom') : "",
            'tPdtCodeTo'        => !empty($this->input->post('oetRptPdtCodeTo')) ? $this->input->post('oetRptPdtCodeTo') : "",
            'tPdtNameTo'        => !empty($this->input->post('oetRptPdtNameTo')) ? $this->input->post('oetRptPdtNameTo') : "",

            // กลุ่มสินค้า
            'tPdtGrpCodeFrom'   => !empty($this->input->post('oetRptPdtGrpCodeFrom')) ? $this->input->post('oetRptPdtGrpCodeFrom') : "",
            'tPdtGrpNameFrom'   => !empty($this->input->post('oetRptPdtGrpNameFrom')) ? $this->input->post('oetRptPdtGrpNameFrom') : "",
            'tPdtGrpCodeTo'     => !empty($this->input->post('oetRptPdtGrpCodeTo')) ? $this->input->post('oetRptPdtGrpCodeTo') : "",
            'tPdtGrpNameTo'     => !empty($this->input->post('oetRptPdtGrpNameTo')) ? $this->input->post('oetRptPdtGrpNameTo') : "",

            // ประเภทสินค้า
            'tPdtTypeCodeFrom'  => !empty($this->input->post('oetRptPdtTypeCodeFrom')) ? $this->input->post('oetRptPdtTypeCodeFrom') : "",
            'tPdtTypeNameFrom'  => !empty($this->input->post('oetRptPdtTypeNameFrom')) ? $this->input->post('oetRptPdtTypeNameFrom') : "",
            'tPdtTypeCodeTo'    => !empty($this->input->post('oetRptPdtTypeCodeTo')) ? $this->input->post('oetRptPdtTypeCodeTo') : "",
            'tPdtTypeNameTo'    => !empty($this->input->post('oetRptPdtTypeNameTo')) ? $this->input->post('oetRptPdtTypeNameTo') : "",

            // Filter หมวดหมู่
            'tCate1From'        => !empty($this->input->post('oetRptCate1CodeFrom')) ? $this->input->post('oetRptCate1CodeFrom') : "",
            'tCate1FromName'    => !empty($this->input->post('oetRptCate1NameFrom')) ? $this->input->post('oetRptCate1NameFrom') : "",
            'tCate2From'        => !empty($this->input->post('oetRptCate2CodeFrom')) ? $this->input->post('oetRptCate2CodeFrom') : "",
            'tCate2FromName'    => !empty($this->input->post('oetRptCate2NameFrom')) ? $this->input->post('oetRptCate2NameFrom') : "",
        ];

        // ดึงข้อมูลบริษัทฯ
        $aCompInfoParams    = [
            'nLngID'        => $this->nLngID,
            'tBchCode'      => $this->tBchCodeLogin,
        ];
        $this->aCompanyInfo = FCNaGetCompanyInfo($aCompInfoParams)['raItems'];
    }

    public function index(){
        if (!empty($this->tRptExportType) && !empty($this->tRptCode)) {
            // Execute Stored Procedure
            $this->RptSaleFCCompVD_model->FSnMExecStoreReport($this->aRptFilter);
            switch ($this->tRptExportType) {
                case 'html':
                    $this->FSvCCallRptViewBeforePrint();
                    break;
                case 'excel':
                    $this->FSvCCallRptRenderExcel();
                    break;
            }
        }
    }

    // ฟังก์ชั่นดูตัวอย่างก่อนพิมพ์ (Report Viewer)
    public function FSvCCallRptViewBeforePrint(){
        // ข้อมูลสำหรับดึงข้อมูลจากฐานข้อมูล
        $aDataWhereRpt  = array(
            'nPerPage'      => $this->nPerPage,
            'nPage'         => '1', // เริ่มทำงานหน้าแรก
            'tCompName'     => $this->tCompName,
            'tRptCode'      => $this->tRptCode,
            'tUsrSessionID' => $this->tUserSessionID,
            'aDataFilter'   => $this->aRptFilter,
        );
        $aDataReport    = $this->RptSaleFCCompVD_model->FSaMGetDataReport($aDataWhereRpt);
        // ข้อมูล Render Report
        $aDataViewRpt   = array(
            'aDataReport'       => $aDataReport,
            'aDataTextRef'      => $this->aText,
            'aCompanyInfo'      => $this->aCompanyInfo,
            'nOptDecimalShow'   => $this->nOptDecimalShow,
            'aDataFilter'       => $this->aRptFilter,
        );
        // Load View Advance Table
        $tRptView   = JCNoHLoadViewAdvanceTable('report/datasources/rptsalefccompvD', 'wRptSaleFCCompVDHtml', $aDataViewRpt);
        // Data Viewer Center Report
        $aDataViewer = array(
            'tTitleReport'      => $this->aText['tTitleReport'],
            'tRptTypeExport'    => $this->tRptExportType,
            'tRptCode'          => $this->tRptCode,
            'tRptRoute'         => $this->tRptRoute,
            'tViewRenderKool'   => $tRptView,
            'aDataFilter'       => $this->aRptFilter,
            'aDataReport'       => array(
                'raItems'       => $aDataReport['aRptData'],
                'rnAllRow'      => $aDataReport['aPagination']['nTotalRecord'],
                'rnCurrentPage' => $aDataReport['aPagination']['nDisplayPage'],
                'rnAllPage'     => $aDataReport['aPagination']['nTotalPage'],
                'rtCode'        => '1',
                'rtDesc'        => 'success',
            ),
        );
        $this->load->view('report/report/wReportViewer', $aDataViewer);
    }

    // Click Page ดูตัวอย่างก่อนพิมพ์ (Report Viewer)
    public function FSvCCallRptViewBeforePrintClickPage(){
        $aDataFilter    = json_decode($this->input->post('ohdRptDataFilter'), true);
        // ข้อมูลสำหรับดึงข้อมูลจากฐานข้อมูล
        $aDataWhereRpt  = array(
            'nPerPage'      => $this->nPerPage,
            'nPage'         => $this->nPage,
            'tCompName'     => $this->tCompName,
            'tRptCode'      => $this->tRptCode,
            'tUsrSessionID' => $this->tUserSessionID,
            'aDataFilter'   => $this->aRptFilter,
        );
        $aDataReport    = $this->RptSaleFCCompVD_model->FSaMGetDataReport($aDataWhereRpt);
        // ข้อมูล Render Report
        $aDataViewRpt   = array(
            'aDataReport'       => $aDataReport,
            'aDataTextRef'      => $this->aText,
            'aCompanyInfo'      => $this->aCompanyInfo,
            'nOptDecimalShow'   => $this->nOptDecimalShow,
            'aDataFilter'       => $this->aRptFilter,
        );
        // Load View Advance Table
        $tRptView   = JCNoHLoadViewAdvanceTable('report/datasources/rptsalefccompvD', 'wRptSaleFCCompVDHtml', $aDataViewRpt);
        // Data Viewer Center Report
        $aDataViewer = array(
            'tTitleReport'      => $this->aText['tTitleReport'],
            'tRptTypeExport'    => $this->tRptExportType,
            'tRptCode'          => $this->tRptCode,
            'tRptRoute'         => $this->tRptRoute,
            'tViewRenderKool'   => $tRptView,
            'aDataFilter'       => $this->aRptFilter,
            'aDataReport'       => array(
                'raItems'       => $aDataReport['aRptData'],
                'rnAllRow'      => $aDataReport['aPagination']['nTotalRecord'],
                'rnCurrentPage' => $aDataReport['aPagination']['nDisplayPage'],
                'rnAllPage'     => $aDataReport['aPagination']['nTotalPage'],
                'rtCode'        => '1',
                'rtDesc'        => 'success',
            ),
        );
        $this->load->view('report/report/wReportViewer', $aDataViewer);
    }


    // excel ส่วนกลาง
    public function FSvCCallRptRenderExcel(){
        $tFileName  = $this->aText['tTitleReport'] . '_' . date('YmdHis') . '.xlsx';
        $oWriter    = WriterEntityFactory::createXLSXWriter();
        $oWriter->openToBrowser($tFileName); // stream data directly to the browser
        $aMulltiRow = $this->FSoCCallRptRenderHedaerExcel(); //เรียกฟังชั่นสร้างส่วนหัวรายงาน
        $oWriter->addRows($aMulltiRow);

        $oBorder    = (new BorderBuilder())
            ->setBorderTop(Color::BLACK, Border::WIDTH_THIN)
            ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN)
            ->build();

        $oStyleColums   = (new StyleBuilder())
            ->setBorder($oBorder)
            ->setFontBold()
            ->build();

        $aCells1 = [
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDAgency')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDBranch')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDPdtCode')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDPdtName')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDPdtGroup')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDPdtType')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDPdtCat1')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDPdtCat2')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDBuyForHQ')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDBuyForVD')),
        ];

        $singleRow1 = WriterEntityFactory::createRow($aCells1, $oStyleColums);
        $oWriter->addRow($singleRow1);

        $aCells2 = [
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDPurchaseAmt')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDPercentAmt')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDPurchaseAmt')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleFCCompVDPercentAmt')),
        ];
        $singleRow2 = WriterEntityFactory::createRow($aCells2, $oStyleColums);
        $oWriter->addRow($singleRow2);


        $aDataWhereRpt  = array(
            'nPerPage'      => 0,
            'nPage'         => '1',
            'tCompName'     => $this->tCompName,
            'tRptCode'      => $this->tRptCode,
            'tUsrSessionID' => $this->tUserSessionID,
            'aDataFilter'   => $this->aRptFilter,
        );
        $aDataReport    = $this->RptSaleFCCompVD_model->FSaMGetDataReport($aDataWhereRpt);
        if (isset($aDataReport['aRptData']) && !empty($aDataReport['aRptData'])){
            foreach ($aDataReport['aRptData'] as $nKey => $aValue) {
                $values = [
                    WriterEntityFactory::createCell($aValue['FTAgnName']),
                    WriterEntityFactory::createCell(($aValue["FTBchName"] != ""         ? $aValue["FTBchName"] : "-")),
                    WriterEntityFactory::createCell(($aValue["FTPdtCode"] != ""         ? $aValue["FTPdtCode"] : "-")),
                    WriterEntityFactory::createCell(($aValue["FTPdtName"] != ""         ? $aValue["FTPdtName"] : "-")),
                    WriterEntityFactory::createCell(($aValue["FTPgpChainName"] != ""    ? $aValue["FTPgpChainName"] : "-")),
                    WriterEntityFactory::createCell(($aValue["FTPtyName"] != ""         ? $aValue["FTPtyName"] : "-")),
                    WriterEntityFactory::createCell(($aValue["FTCatName1"] != ""        ? $aValue["FTCatName1"] : "-")),
                    WriterEntityFactory::createCell(($aValue["FTCatName2"] != ""        ? $aValue["FTCatName2"] : "-")),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXpdNetAfHDHQ'])),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXpdPerPoByHQ'])),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXpdNetAfHDVD'])),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXpdPerPoByVD'])),
                ];
                $aRow = WriterEntityFactory::createRow($values);
                $oWriter->addRow($aRow);

                if (($nKey + 1) == FCNnHSizeOf($aDataReport['aRptData'])) { 
                    //SumFooter
                    $values = [
                        WriterEntityFactory::createCell($this->aText['tRptTotalAllSale']),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($aValue["FCXpdNetAfHDHQ_Footer"])),
                        WriterEntityFactory::createCell(FCNnGetNumeric($aValue["FCXpdPerPoByHQ_Footer"])),
                        WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXpdNetAfHDVD_Footer'])),
                        WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXpdPerPoByVD_Footer'])),
                    ];
                    $aRow   = WriterEntityFactory::createRow($values, $oStyleColums);
                    $oWriter->addRow($aRow);
                }
            }
        }

        $aMulltiRow = $this->FSoCCallRptRenderFooterExcel();
        $oWriter->addRows($aMulltiRow);
        $oWriter->close();
    }

    //excel ส่วนหัว
    public function FSoCCallRptRenderHedaerExcel(){
        if (isset($this->aCompanyInfo) && count($this->aCompanyInfo)>0) {
            $tFTAddV1Village        = $this->aCompanyInfo['FTAddV1Village']; 
            $tFTCmpName             = $this->aCompanyInfo['FTCmpName'];
            $tFTAddV1No             = $this->aCompanyInfo['FTAddV1No'];
            $tFTAddV1Road           = $this->aCompanyInfo['FTAddV1Road'];
            $tFTAddV1Soi            = $this->aCompanyInfo['FTAddV1Soi'];
            $tFTSudName             = $this->aCompanyInfo['FTSudName'];
            $tFTDstName             = $this->aCompanyInfo['FTDstName'];
            $tFTPvnName             = $this->aCompanyInfo['FTPvnName'];
            $tFTAddV1PostCode       = $this->aCompanyInfo['FTAddV1PostCode'];
            $tFTAddV2Desc1          = $this->aCompanyInfo['FTAddV2Desc1'];
            $tFTAddV2Desc2          = $this->aCompanyInfo['FTAddV2Desc2'];
            $tFTAddVersion          = $this->aCompanyInfo['FTAddVersion'];
            $tFTBchName             = $this->aCompanyInfo['FTBchName'];
            $tFTAddTaxNo            = $this->aCompanyInfo['FTAddTaxNo'];
            $tFTCmpTel              = $this->aCompanyInfo['FTAddTel'];
            $tRptFaxNo              = $this->aCompanyInfo['FTAddFax'];
        }else {
            $tFTCmpTel              = "";
            $tFTCmpName             = "";
            $tFTAddV1No             = "";
            $tFTAddV1Road           = "";
            $tFTAddV1Soi            = "";
            $tFTSudName             = "";
            $tFTDstName             = "";
            $tFTPvnName             = "";
            $tFTAddV1PostCode       = "";
            $tFTAddV2Desc1          = "1"; 
            $tFTAddV1Village        = "";
            $tFTAddV2Desc2          = "2";
            $tFTAddVersion          = "";
            $tFTBchName             = "";
            $tFTAddTaxNo            = "";
            $tRptFaxNo              = "";
        }
        $oStyle = (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(12)
            ->build();

        $aCells = [
            WriterEntityFactory::createCell($tFTCmpName),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell($this->aText['tTitleReport'])
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

        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptTel'] . ' ' . $tFTCmpTel . ' '.$this->aText['tRptFaxNo'] . ' ' . $tRptFaxNo),
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

        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptBranch'] . ' ' . $tFTBchName),
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

        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptTaxSalePosTaxId'] . ' : ' . $tFTAddTaxNo),
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

        $aCells = [
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
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

        if ((isset($this->aRptFilter['tDocDateFrom']) && !empty($this->aRptFilter['tDocDateFrom'])) && (isset($this->aRptFilter['tDocDateTo']) && !empty($this->aRptFilter['tDocDateTo']))) {
            $aCells = [
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell($this->aText['tRptTaxPointByCstDocDateFrom'] . ' ' . date('d/m/Y', strtotime($this->aRptFilter['tDocDateFrom'])) . ' ' . $this->aText['tRptTaxPointByCstDocDateTo'] . ' ' . date('d/m/Y', strtotime($this->aRptFilter['tDocDateTo']))),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        $aCells = [
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell($this->aText['tRptDatePrint'] . ' ' . date('d/m/Y') . ' ' . $this->aText['tRptTimePrint'] . ' ' . date('H:i:s')),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        return $aMulltiRow;
    }

    //excel ส่วนท้าย
    public function FSoCCallRptRenderFooterExcel(){
        $oStyleFilter = (new StyleBuilder())
            ->setFontBold()
            ->build();

        $aCells = [
            WriterEntityFactory::createCell(null),
        ];
        $aMulltiRow[] = WriterEntityFactory::createRow($aCells, $oStyleFilter);

        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptConditionInReport']),
        ];
        $aMulltiRow[] = WriterEntityFactory::createRow($aCells, $oStyleFilter);

        // สาขา แบบเลือก
        if (!empty($this->aRptFilter['tBchCodeSelect'])) {
            $tBchSelectText = ($this->aRptFilter['bBchStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tBchNameSelect'];
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptBchFrom'] . ' : ' . $tBchSelectText),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        // Fillter Prodict (สินค้า) แบบช่วง
        if (!empty($this->aRptFilter['tPdtCodeFrom']) && !empty($this->aRptFilter['tPdtCodeTo'])) {
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tPdtCodeFrom'] . ' : ' . $this->aRptFilter['tPdtNameFrom'] . '     ' . $this->aText['tPdtCodeTo'] . ' : ' . $this->aRptFilter['tPdtNameTo']),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        // Fillter Product Group (กลุ่มสินค้า)  แบบช่วง
        if (!empty($this->aRptFilter['tPdtGrpCodeFrom']) && !empty($this->aRptFilter['tPdtGrpCodeTo'])) {
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tPdtGrpFrom'] . ' : ' . $this->aRptFilter['tPdtGrpNameFrom'] . '     ' . $this->aText['tPdtGrpTo'] . ' : ' . $this->aRptFilter['tPdtGrpNameTo']),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        // Fillter Product Type (ประเภทสินค้า)  แบบช่วง
        if (!empty($this->aRptFilter['tPdtTypeCodeFrom']) && !empty($this->aRptFilter['tPdtTypeCodeTo'])) {
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tPdtTypeFrom'].' : '.$this->aRptFilter['tPdtTypeNameFrom'] . '     ' . $this->aText['tPdtTypeTo'].' : '.$this->aRptFilter['tPdtTypeNameTo']),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        // Filter หมวดหมู่สินค้าหลัก
        if(isset($this->aRptFilter['tCate1From']) && !empty($this->aRptFilter['tCate1From'])){
            $aCells = [
                WriterEntityFactory::createCell('หมวดหมู่สินค้าหลัก '.' : '.$this->aRptFilter['tCate1FromName']),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        // Filter หมวดหมู่สินค้าย่อย
        if(isset($this->aRptFilter['tCate2From']) && !empty($this->aRptFilter['tCate2From'])){
            $aCells = [
                WriterEntityFactory::createCell('หมวดหมู่สินค้าย่อย '.' : '.$this->aRptFilter['tCate2FromName']),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }
        return $aMulltiRow;
    }

}