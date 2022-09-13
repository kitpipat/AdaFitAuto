<script type="text/javascript">
	$(document).ready(function() {

		$('.selectpicker').selectpicker();

		$('#obtSearchDocDateFrom').click(function() {
			event.preventDefault();
			$('#oetSearchDocDateFrom').datepicker('show');
		});

		$('#obtSearchDocDateTo').click(function() {
			event.preventDefault();
			$('#oetSearchDocDateTo').datepicker('show');
		});

		$('.xCNDatePicker').datepicker({
			format			: 'yyyy-mm-dd',
			todayHighlight	: true,
			autoclose		: true
		});

		$(".selection-2").select2({
			dropdownParent: $('#dropDownSelect1')
		});

	});

	// Advance search display control
	$('#oahIVBAdvanceSearch').on('click', function() {
		if($('#odvIVBAdvanceSearchContainer').hasClass('hidden')) {
			$('#odvIVBAdvanceSearchContainer').removeClass('hidden').hide().slideDown(500);
		}else{
			$("#odvIVBAdvanceSearchContainer").slideUp(500,function() {
                $(this).addClass('hidden');
            });
		}
	});

	var tUsrLevel 	  	= "<?=$this->session->userdata("tSesUsrLevel"); ?>";
	var tBchCodeMulti 	= "<?=$this->session->userdata("tSesUsrBchCodeMulti"); ?>";
	var nCountBch 		= "<?=$this->session->userdata("nSesUsrBchCount"); ?>";
    var nLangEdits      = "<?=$this->session->userdata("tLangEdit")?>";
	var tWhere 			= "";

	if(nCountBch == 1){
		$('#obtIVBBrowseBchFrom').attr('disabled',true);
		$('#obtIVBBrowseBchTo').attr('disabled',true);
	}
	if(tUsrLevel != "HQ"){
		tWhere = " AND TCNMBranch.FTBchCode IN ("+tBchCodeMulti+") ";
	}else{
		tWhere = "";
	}

	// Option Branch From
	var oPmhBrowseBchFrom = {
		Title   : ['company/branch/branch', 'tBCHTitle'],
		Table   : {
			Master  : 'TCNMBranch',
			PK      : 'FTBchCode'
		},
		Join    : {
			Table   : ['TCNMBranch_L'],
			On      : ['TCNMBranch_L.FTBchCode = TCNMBranch.FTBchCode AND TCNMBranch_L.FNLngID = ' + nLangEdits, ]
		},
		Where   : {
			Condition : [tWhere]
		},
		GrideView : {
			ColumnPathLang   : 'company/branch/branch',
			ColumnKeyLang    : ['tBCHCode', 'tBCHName'],
			ColumnsSize      : ['15%', '75%'],
			WidthModal       : 50,
			DataColumns      : ['TCNMBranch.FTBchCode', 'TCNMBranch_L.FTBchName'],
			DataColumnsFormat: ['', ''],
			Perpage          : 10,
			OrderBy          : ['TCNMBranch.FTBchCode ASC'],
		},
		CallBack: {
			ReturnType      : 'S',
			Value           : ["oetBchCodeFrom", "TCNMBranch.FTBchCode"],
			Text            : ["oetBchNameFrom", "TCNMBranch_L.FTBchName"],
		},
	}

	// Option Branch To
	var oPmhBrowseBchTo = {
		Title   : ['company/branch/branch', 'tBCHTitle'],
		Table   : {
			Master  : 'TCNMBranch',
			PK      : 'FTBchCode'
		},
		Join    : {
			Table   : ['TCNMBranch_L'],
			On      : ['TCNMBranch_L.FTBchCode = TCNMBranch.FTBchCode AND TCNMBranch_L.FNLngID = ' + nLangEdits, ]
		},
		Where   :{
			Condition : [tWhere]
		},
		GrideView   : {
			ColumnPathLang      : 'company/branch/branch',
			ColumnKeyLang       : ['tBCHCode', 'tBCHName'],
			ColumnsSize         : ['15%', '75%'],
			WidthModal          : 50,
			DataColumns         : ['TCNMBranch.FTBchCode', 'TCNMBranch_L.FTBchName'],
			DataColumnsFormat   : ['', ''],
			Perpage             : 10,
			OrderBy             : ['TCNMBranch.FTBchCode DESC'],
		},
		CallBack: {
			ReturnType  : 'S',
			Value       : ["oetBchCodeTo", "TCNMBranch.FTBchCode"],
			Text        : ["oetBchNameTo", "TCNMBranch_L.FTBchName"],
		},
	}

	// Event Browse
	$('#obtIVBBrowseBchFrom').unbind().click(function() {
		var nStaSession = JCNxFuncChkSessionExpired();
		if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
			JSxCheckPinMenuClose(); // Hidden Pin Menu
			JCNxBrowseData('oPmhBrowseBchFrom');
		} else {
			JCNxShowMsgSessionExpired();
		}
	});
	$('#obtIVBBrowseBchTo').unbind().click(function() {
		var nStaSession = JCNxFuncChkSessionExpired();
		if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
			JSxCheckPinMenuClose(); // Hidden Pin Menu
			JCNxBrowseData('oPmhBrowseBchTo');
		} else {
			JCNxShowMsgSessionExpired();
		}
	});

	// Option Browse ตัวแทนขาย / แฟรนไชส์
	var oBrowseAgency	= function(poDataFnc) {
		var tReturnCode		= poDataFnc.tReturnCode;
        var tReturnName		= poDataFnc.tReturnName;
		var oOptionReturn	= {
			Title 	: ['ticket/agency/agency', 'tAggTitle'],
			Table	: {
                Master  : 'TCNMAgency',
                PK      : 'FTAgnCode'
            },
			Join	: {
                Table: ['TCNMAgency_L'],
                On: [
                    'TCNMAgency_L.FTAgnCode = TCNMAgency.FTAgnCode AND TCNMAgency_L.FNLngID = ' + nLangEdits
                ]
            },
			GrideView: {
                ColumnPathLang		: 'ticket/agency/agency',
                ColumnKeyLang		: ['tAggCode', 'tAggName'],
                ColumnsSize			: ['15%', '75%'],
                WidthModal			: 50,
                DataColumns			: ['TCNMAgency.FTAgnCode', 'TCNMAgency_L.FTAgnName'],
                DataColumnsFormat	: ['', ''],
                Perpage				: 5,
                OrderBy				: ['TCNMAgency.FDCreateOn'],
                SourceOrder			: "DESC"
            },
			CallBack	: {
                ReturnType	: 'S',
                Value       : [tReturnCode, "TCNMAgency.FTAgnCode"],
                Text        : [tReturnName, "TCNMAgency_L.FTAgnName"],
            },
		};
		return oOptionReturn;
	};

	// Event Browse ตัวแทนขาย / แฟรนไชส์
    $('#obtIVBBrowseAgn').unbind().click(function() {
        let nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            JSxCheckPinMenuClose(); // Hidden Pin Menu
			window.oBrowseAgencyOption  = undefined;
            oBrowseAgencyOption = oBrowseAgency({
				'tReturnCode'	: 'oetAgnCode',
				'tReturnName'	: 'oetAgnName'
			});
			JCNxBrowseData('oBrowseAgencyOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

	// Option Browse ผู้จำหน่าย
    var oBrowseSupplier	= function(poDataFnc) {
        var tInputReturnCode	= poDataFnc.tReturnInputCode;
        var tInputReturnName	= poDataFnc.tReturnInputName;
		var oOptionReturn		= {
			Title	: ['supplier/supplier/supplier', 'tSPLTitle'],
			Table	: {Master:'TCNMSpl', PK:'FTSplCode'},
			Join	: {
                Table   : ['TCNMSpl_L', 'TCNMSplCredit'],
                On      : [
                    'TCNMSpl_L.FTSplCode    = TCNMSpl.FTSplCode AND TCNMSpl_L.FNLngID = '+nLangEdits,
                    'TCNMSpl_L.FTSplCode    = TCNMSplCredit.FTSplCode'
                ]
            },
			Where	: {
                Condition   : ["AND TCNMSpl.FTSplStaActive = '1' "]
            },
			GrideView	: {
                ColumnPathLang		: 'supplier/supplier/supplier',
                ColumnKeyLang		: ['tSPLTBCode', 'tSPLTBName'],
                ColumnsSize			: ['15%', '75%'],
                WidthModal			: 50,
                DataColumns			: ['TCNMSpl.FTSplCode', 'TCNMSpl_L.FTSplName', 'TCNMSplCredit.FNSplCrTerm', 'TCNMSplCredit.FCSplCrLimit', 'TCNMSpl.FTSplStaVATInOrEx', 'TCNMSplCredit.FTSplTspPaid'],
                DataColumnsFormat	: ['',''],
                DisabledColumns		: [2, 3, 4, 5],
                Perpage				: 10,
                OrderBy				: ['TCNMSpl_L.FTSplName ASC']
            },
			CallBack    : {
                ReturnType: 'S',
                Value   : [tInputReturnCode,"TCNMSpl.FTSplCode"],
                Text    : [tInputReturnName,"TCNMSpl_L.FTSplName"]
            },
            RouteAddNew : 'supplier',
		};
		return oOptionReturn;
    }

	// Event Browse ผู้จำหน่าย
    $('#obtBrowseSpl').unbind().click(function() {
		var nStaSession	= JCNxFuncChkSessionExpired();
		if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
			JSxCheckPinMenuClose(); // Hidden Pin Menu
			window.oBrowseSupplierOption = undefined;
			oBrowseSupplierOption = oBrowseSupplier({
				'tReturnInputCode'	: 'oetSplCode',
                'tReturnInputName'	: 'oetSplName',
			});
			JCNxBrowseData('oBrowseSupplierOption');
		} else {
			JCNxShowMsgSessionExpired();
		}
	});

	//ล้างข้อมูล
	function JSxIVBClearSearchData() {
		var nCountBch = "<?=$this->session->userdata("nSesUsrBchCount"); ?>";
		if(nCountBch != 1){ //ถ้ามีมากกว่า 1 สาขาต้อง reset 
			$("#oetBchCodeTo").val("");
			$("#oetBchNameTo").val(""); 
			$("#oetBchCodeFrom").val("");
			$("#oetBchNameFrom").val("");
		}

		var tFilterAgnCode	= '';
		var tFilterAgnName	= '';
		let tChkAgnCodeAD   = '<?=$this->session->userdata("tSesUsrAgnCode")?>';
		if(tChkAgnCodeAD != ""){
			tFilterAgnCode	= $('#oetAgnCode').val();
			tFilterAgnName	= $('#oetAgnName').val();
		}

		// Set Agn Code Default
		$('#oetAgnCode').val(tFilterAgnCode);
		$('#oetAgnName').val(tFilterAgnName);

		// Reset Suplier
		$('#oetSplCode').val('');
		$('#oetSplName').val('');

		$("#oetSearchAll").val("");

		$("#obtIVBDocDateFrom").val("");
		$("#obtIVBDocDateTo").val("");
		$(".xCNDatePicker").datepicker("setDate", null);
		$(".selectpicker").val("0").selectpicker("refresh");

		JSvIVBCallPageDataTable();
    }

</script>