<?php
require_once "stimulsoft/helper.php";
require_once "../decodeURLCenter.php";
require_once('../../config_deploy.php');
?>
<!DOCTYPE html>

<html>
<head>
	<?php
		if(isset($_GET["infor"])){
			$aParamiterMap	= array(
				"Lang","ComCode","BranchCode","DocCode","DocBchCode"
			);
			$aDataMQ	= FSaHDeCodeUrlParameter($_GET["infor"],$aParamiterMap);
		}else{
			$aDataMQ	= false;
		}
		if($aDataMQ){
	?>
	
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Frm_SQL_SMSBillStateMent_Fit.mrt - Viewer</title>
	<link rel="stylesheet" type="text/css" href="<?=BASE_URL?>/formreport/AdaCoreFrmReport/css/stimulsoft.viewer.office2013.whiteblue.css">
	<script type="text/javascript" src="<?=BASE_URL?>/formreport/AdaCoreFrmReport/scripts_year_2022/stimulsoft.reports.engine.js"></script>
	<script type="text/javascript" src="<?=BASE_URL?>/formreport/AdaCoreFrmReport/scripts_year_2022/stimulsoft.reports.export.js"></script>
	<script type="text/javascript" src="<?=BASE_URL?>/formreport/AdaCoreFrmReport/scripts_year_2022/stimulsoft.viewer.js"></script>

	<?php
		StiHelper::init("handler.php", 30);
	?>
	<script type="text/javascript">
		function Start() {
			Stimulsoft.Base.StiLicense.key =
				"6vJhGtLLLz2GNviWmUTrhSqnOItdDwjBylQzQcAOiHmblKNPE3oHWCJIxhSkwga4xSTZvYynA4rRkZbo" +
				"Z1DoLJFeWf2NrwwwuYedMxsfMi4ZvTsFHSz/kYZN0AE6wlSuk1nPoBlevRV9GOE0/wnNfEBvBpZjLgh/" +
				"5wFu7p8fTpHspbjxp0pFdyIM9Ht498koyhskZxemQ9KsfTu+bqIFHN79zTuyu5OZ6xgmXuMTvtFFZHNZ" +
				"Iwl/NsuaVzI7UxOKFMK5xYRkYn5wP1ge8xvnwX/TUCMBAllYc2tCi31todUzyQXMYgh/5jp3sv5OqIZr" +
				"Sb82YzIR6Dh5j3zq0Ix/IzoIVsYCfC0Cbu9kHUBZdGLpClG7QE2pz/iIP0QmUOOMZKr3e3lfNm/7p55h" +
				"k3eyRZjWRjQDqU3HztZpug541GqrOcKDOz/NQ5cZNTQ0XlCDDVFlQXR4L7x7Audn8L1VyfvmC0wp0mOc" +
				"WiHfogrdRFCljWtXKDgwgfELI+ITWrtkdprYQZ+OGxfoO8t1Z0apOq48n/B829iOEdFRT9W+3kINk3bm" +
				"oT0yl2dyjmqOGOx+HKPegw8QesiN5Xc1CcGCoNhXdpfGzbcH9MZKDoM1+6Jkp1ZzQhtj5iagaTH92kHr" +
				"quIXhajOsx8nLtqnbjwu8lnBg8gGzO6gOaBuEKyKvRHiW0LY0/zvorp5KkD4e29x5NiMoxkco3i8CwZz" +
				"LS9fE+Pqh/L5zRKDZI4XqoCqA/nGYC6vyutQQpAqVIuIkZtoDeHe5X1y6k2VYnaiKXS8KQ+21UPFuk7G" +
				"7RJqfvWn83gVBtSwWJI6brvOcRZZpBoFqJdZ1AyQ4hzbr/o6e6ZJ2Wn4ZrqfPxGOPlxcijyVeEpB5NO8" +
				"b/FvgsHYecG0+q4lVtqFDsuAG4pKCdprckNv8ndqTFVJaFpSiIPox3mqwY7JwWqpYVFuxIT4n673Xo0y" +
				"XTTTsvnXJHMvUKxNBCAnbRH9x8m0Oz/2y7aMVDcpAdaH2GBDPSQ++ltg87dYzOBvjgjxh2N8Tjg0ssVC" +
				"WKxwyD1ofNwSCpEwvYANgnevoDk=";

			Stimulsoft.Base.Localization.StiLocalization.setLocalizationFile("<?=BASE_URL?>/formreport/AdaCoreFrmReport/localization/en.xml", true);

			var report = Stimulsoft.Report.StiReport.createNewReport();
			report.loadFile("reports/Frm_SQL_SMSBillStateMent_Fit.mrt");

			report.dictionary.variables.getByName("SP_nLang").valueObject 		= "<?=$aDataMQ["Lang"];?>";
			report.dictionary.variables.getByName("nLanguage").valueObject 		= "<?=$aDataMQ["Lang"];?>";
			report.dictionary.variables.getByName("SP_tCompCode").valueObject	= "<?=$aDataMQ["ComCode"];?>";
			report.dictionary.variables.getByName("SP_tCmpBch").valueObject 	= "<?=$aDataMQ["BranchCode"];?>";
			report.dictionary.variables.getByName("SP_tDocNo").valueObject 		= "<?=$aDataMQ["DocCode"];?>";
			report.dictionary.variables.getByName("SP_nAddSeq").valueObject 	= 10149;
			report.dictionary.variables.getByName("SP_tDocBch").valueObject 	= "<?=$aDataMQ["DocBchCode"];?>";
			report.dictionary.variables.getByName("SP_tGrdStr").valueObject		= "";

			var options = new Stimulsoft.Viewer.StiViewerOptions();
			options.appearance.fullScreenMode = true;
			options.toolbar.displayMode = Stimulsoft.Viewer.StiToolbarDisplayMode.Separated;
			
			var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);

			viewer.onPrepareVariables = function (args, callback) {
				Stimulsoft.Helper.process(args, callback);
			}

			viewer.onBeginProcessData = function (args, callback) {
				Stimulsoft.Helper.process(args, callback);
			}

			viewer.report = report;
			viewer.renderHtml("viewerContent");
		}
	</script>
	<?php
		}
	?>
</head>
<body onload="Start()">
	<?php
		if($aDataMQ){
	?>
	<div id="viewerContent"></div>
	<?php
		}else{
			echo "ไม่สามารถเข้าถึงข้อมูลนี้ได้";
		}
	?>
</body>
</html>