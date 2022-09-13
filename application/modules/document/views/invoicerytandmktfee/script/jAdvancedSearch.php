<script type="text/javascript">

    $(document).ready(function(){
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
	$('#oahTRMAdvanceSearch').on('click', function() {
		if($('#odvTRMAdvanceSearchContainer').hasClass('hidden')) {
			$('#odvTRMAdvanceSearchContainer').removeClass('hidden').hide().slideDown(500);
		}else{
			$("#odvTRMAdvanceSearchContainer").slideUp(500,function() {
                $(this).addClass('hidden');
            });
		}
	});

	var tUsrLevel		= "<?=$this->session->userdata("tSesUsrLevel"); ?>";
	var tBchCodeMulti	= "<?=$this->session->userdata("tSesUsrBchCodeMulti"); ?>";
	var nCountBch		= "<?=$this->session->userdata("nSesUsrBchCount"); ?>";
    var nLangEdits      = "<?=$this->session->userdata("tLangEdit")?>";
	var tWhere 			= "";
	if(nCountBch == 1){
		$('#obtTRMBrowseBchFrom').attr('disabled',true);
		$('#obtTRMBrowseBchTo').attr('disabled',true);
	}
	if(tUsrLevel != "HQ"){
		tWhere	= " AND TCNMBranch.FTBchCode IN ("+tBchCodeMulti+") ";
	}else{
		tWhere	= "";
	}
	
	// ===================================================== Event Browse Branch =====================================================
		// Option Branch From
		var oTRMBrowseBchFrom	= {
			Title	: ['company/branch/branch','tBCHTitle'],
			Table   : {
				Master	: 'TCNMBranch',
				PK      : 'FTBchCode'
			},
			Join    : {
				Table	: ['TCNMBranch_L'],
				On		: ['TCNMBranch_L.FTBchCode = TCNMBranch.FTBchCode AND TCNMBranch_L.FNLngID = ' + nLangEdits, ]
			},
			Where	: {Condition : [tWhere]},
			GrideView	: {
				ColumnPathLang   	: 'company/branch/branch',
				ColumnKeyLang    	: ['tBCHCode', 'tBCHName'],
				ColumnsSize      	: ['15%', '75%'],
				WidthModal       	: 50,
				DataColumns      	: ['TCNMBranch.FTBchCode', 'TCNMBranch_L.FTBchName'],
				DataColumnsFormat	: ['', ''],
				Perpage          	: 10,
				OrderBy          	: ['TCNMBranch.FTBchCode ASC'],
			},
			CallBack: {
				ReturnType	: 'S',
				Value		: ["oetBchCodeFrom", "TCNMBranch.FTBchCode"],
				Text		: ["oetBchNameFrom", "TCNMBranch_L.FTBchName"],
			},
		};
		// Option Branch To
		var oTRMBrowseBchTo		= {
			Title		: ['company/branch/branch', 'tBCHTitle'],
			Table   	: {
				Master	: 'TCNMBranch',
				PK      : 'FTBchCode'
			},
			Join		: {
				Table	: ['TCNMBranch_L'],
				On		: ['TCNMBranch_L.FTBchCode = TCNMBranch.FTBchCode AND TCNMBranch_L.FNLngID = ' + nLangEdits, ]
			},
			Where		: {Condition : [tWhere]},
			GrideView	: {
				ColumnPathLang		: 'company/branch/branch',
				ColumnKeyLang       : ['tBCHCode', 'tBCHName'],
				ColumnsSize         : ['15%', '75%'],
				WidthModal          : 50,
				DataColumns         : ['TCNMBranch.FTBchCode', 'TCNMBranch_L.FTBchName'],
				DataColumnsFormat   : ['', ''],
				Perpage             : 10,
				OrderBy             : ['TCNMBranch.FTBchCode DESC'],
			},
			CallBack	: {
				ReturnType	: 'S',
				Value       : ["oetBchCodeTo", "TCNMBranch.FTBchCode"],
				Text        : ["oetBchNameTo", "TCNMBranch_L.FTBchName"],
			},
		}
		$('#obtTRMBrowseBchFrom').unbind().click(function() {
			let nStaSession	= JCNxFuncChkSessionExpired();
			if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
				JSxCheckPinMenuClose(); // Hidden Pin Menu
				JCNxBrowseData('oTRMBrowseBchFrom');
			} else {
				JCNxShowMsgSessionExpired();
			}
		});
		$('#obtTRMBrowseBchTo').unbind().click(function() {
			let nStaSession	= JCNxFuncChkSessionExpired();
			if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
				JSxCheckPinMenuClose(); // Hidden Pin Menu
				JCNxBrowseData('oTRMBrowseBchTo');
			} else {
				JCNxShowMsgSessionExpired();
			}
		});
	// ===============================================================================================================================


	// =================================================== Event Browse Supplier =====================================================
		// Set Option SPL From
		var oTRMBrowseSPLFrom	= {
			Title   : ['ticket/agency/agency', 'tAggTitle'],
			Table   : {Master: 'TCNMAgency',PK: 'FTAgnCode'},
			Join: {
				Table: ['TCNMAgency_L'],
				On: ['TCNMAgency_L.FTAgnCode = TCNMAgency.FTAgnCode AND TCNMAgency_L.FNLngID = ' + nLangEdits]
			},
			Where	: {
				Condition : [" AND TCNMAgency.FTAgnStaActive = '1' "]
			},
			GrideView	: {
				ColumnPathLang: 'ticket/agency/agency',
				ColumnKeyLang: ['tAggCode', 'tAggName'],
				ColumnsSize: ['15%', '85%'],
				WidthModal: 50,
				DataColumns: ['TCNMAgency.FTAgnCode', 'TCNMAgency_L.FTAgnName'],
				DataColumnsFormat: ['', ''],
				Perpage: 10,
				OrderBy: ['TCNMAgency.FTAgnCode DESC'],
			},
			CallBack	: {
				ReturnType	: 'S',
				Value		: ['oetSplCodeFrom',"TCNMAgency.FTAgnCode"],
				Text		: ['oetSplNameFrom',"TCNMAgency_L.FTAgnName"]
			},
			// DebugSQL: true,
		};
		// Set Option SPL To
		var oTRMBrowseSPLTo		= {
			Title   : ['ticket/agency/agency', 'tAggTitle'],
			Table   : {Master: 'TCNMAgency',PK: 'FTAgnCode'},
			Join: {
				Table: ['TCNMAgency_L'],
				On: ['TCNMAgency_L.FTAgnCode = TCNMAgency.FTAgnCode AND TCNMAgency_L.FNLngID = ' + nLangEdits]
			},
			Where	: {
				Condition : [" AND TCNMAgency.FTAgnStaActive = '1' "]
			},
			GrideView	: {
				ColumnPathLang: 'ticket/agency/agency',
				ColumnKeyLang: ['tAggCode', 'tAggName'],
				ColumnsSize: ['15%', '85%'],
				WidthModal: 50,
				DataColumns: ['TCNMAgency.FTAgnCode', 'TCNMAgency_L.FTAgnName'],
				DataColumnsFormat: ['', ''],
				Perpage: 10,
				OrderBy: ['TCNMAgency.FTAgnCode DESC'],
			},
			CallBack	: {
				ReturnType	: 'S',
				Value		: ['oetSplCodeTo',"TCNMAgency.FTAgnCode"],
				Text		: ['oetSplNameTo',"TCNMAgency_L.FTAgnName"]
			},
			// DebugSQL: true,
		}

		$('#obtTRMBrowseSplFrom').unbind().click(function() {
			let nStaSession	= JCNxFuncChkSessionExpired();
			if(typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
				JSxCheckPinMenuClose();	// Hidden Pin Menu
				JCNxBrowseData('oTRMBrowseSPLFrom');
			} else {
				JCNxShowMsgSessionExpired();
			}
		});
		$('#obtTRMBrowseSplTo').unbind().click(function() {
			let nStaSession	= JCNxFuncChkSessionExpired();
			if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
				JSxCheckPinMenuClose(); // Hidden Pin Menu
				JCNxBrowseData('oTRMBrowseSPLTo');
			} else {
				JCNxShowMsgSessionExpired();
			}
		});
	// ===============================================================================================================================

	// ล้างข้อมูล
	function JSxTRMClearSearchData(){
		let nCountBch	= "<?=$this->session->userdata("nSesUsrBchCount"); ?>";
		if(nCountBch != 1){
			//ถ้ามีมากกว่า 1 สาขาต้อง reset 
			$("#oetBchCodeTo").val("");
			$("#oetBchNameTo").val(""); 
			$("#oetBchCodeFrom").val("");
			$("#oetBchNameFrom").val("");
		}
		$("#oetSearchAll").val("");
		$("#obtTRMDocDateFrom").val("");
		$("#obtTRMDocDateTo").val("");
		$("#oetSplNameFrom , #oetSplCodeFrom").val("");
		$("#oetSplNameTo , #oetSplCodeTo").val("");
		$("#oetCstCode , #oetCstName").val("");
		$("#oetCarCode , #oetCarName").val("");
		$(".xCNDatePicker").datepicker("setDate", null);
		$(".selectpicker").val("0").selectpicker("refresh");
	}

</script>