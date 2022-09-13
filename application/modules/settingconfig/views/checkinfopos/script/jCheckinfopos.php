<script type="text/javascript">
    var nBrowseType     = $('#oetCIPStaBrowse').val();
    var tCallBackOption = $('#oetCIPCallBackOption').val();
    $('document').ready(function() {
        localStorage.removeItem('LocalItemData');
        JSxCheckPinMenuClose(); /*Check เปิดปิด Menu ตาม Pin*/
        JSxCIPNavDefult();
        if (nBrowseType != '1') {
            JSvCallPageCIPList();
        }
        $('.xCNHideBtnStaAlw').hide();
    });

    //function : Nav Default
    //Parameters : Document Ready
    //Creator : 22/06/2022 Wasin
    //Return : Show Tab Menu
    //Return Type : -
    function JSxCIPNavDefult(){
        if (nBrowseType != 1 || nBrowseType == undefined) {
            $('#odvBtnAddEdit').hide();
            $('#oliAdvTitleEdit').hide();
            $('#odvBtnSelectCIP').hide();
            $('.panel-heading').show();
        }else{
            $('#odvModalBody #odvCIPMainMenu').removeClass('main-menu');
            $('#odvModalBody #oliCIPNavBrowse').css('padding', '2px');
            $('#odvModalBody #odvCIPBtnGroup').css('padding', '0');
            $('#odvModalBody .xCNCIPBrowseLine').css('padding', '0px 0px');
            $('#odvModalBody .xCNCIPBrowseLine').css('border-bottom', '1px solid #e3e3e3');
        }
    }
    // Function : Function Show Event Error
    // Parameters : Error Ajax Function 
    // Creator : 17/06/2022 Wasin
    // Return : Modal Status Error
    // Return Type  : view
    function JCNxResponseError(jqXHR, textStatus, errorThrown) {
        JCNxCloseLoading();
        let tHtmlError   = $(jqXHR.responseText);
        let tMsgError    = "<h3 style='font-size:20px;color:red'>";
        tMsgError       += "<i class='fa fa-exclamation-triangle'></i>";
        tMsgError       += " Error<hr></h3>";
        switch (jqXHR.status) {
            case 404:
                tMsgError   += tHtmlError.find('p:nth-child(2)').text();
                break;
            case 500:
                tMsgError   += tHtmlError.find('p:nth-child(3)').text();
                break;
            default:
                tMsgError   += 'something had error. please contact admin';
                break;
        }
        $("body").append(tModal);
        $('#modal-customs').attr("style", 'width: 450px; margin: 1.75rem auto;top:20%;');
        $('#myModal').modal({ show: true });
        $('#odvModalBody').html(tMsgError);
    }

    //function : Function Call Page List Check Information Pos
    //Parameters : Document Redy And Event Button
    //Creator : 22/06/2022 Wasin
    //Return : View
    //Return Type : View
    function JSvCallPageCIPList(){
        $.ajax({
            type    : "GET",
            url     : "CheckInfoPosList",
            cache   : false,
            timeout : 0,
            success: function(tResult) {
                $('#odvContentPageCIP').html(tResult);
                JSxCIPNavDefult();
                JSvCIPCallPageDataTable();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    }

    // function : Call Page Check Information Pos Data List
    // Parameters : Ajax Success Event 
    // Creator:	22/06/2022 Wasin
    // Return : View Data List
    // Return Type : View
    function JSvCIPCallPageDataTable(pnPage){
        let tSearchAll      = $('#oetSearchAll').val();
        let nPageCurrent    = pnPage;
        if (nPageCurrent == undefined || nPageCurrent == '') {
            nPageCurrent    = '1';
        }
        JCNxOpenLoading();
        $.ajax({
            type : "POST",
            url  : "CheckInfoPosDataTable",
            data : {
                tSearchAll   : tSearchAll,
                nPageCurrent : nPageCurrent,
            },
            cache   : false,
            Timeout : 0,
            success: function(tResult) {
                if (tResult != "") {
                    $('#odvContentCIPData').html(tResult);
                }
                JSxCIPNavDefult();
                JCNxLayoutControll();
                JCNxCloseLoading();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    }


</script>