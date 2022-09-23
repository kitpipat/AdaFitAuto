/****** Object:  StoredProcedure [dbo].[SP_CNoBrowseProduct]    Script Date: 24/9/2565 1:51:54 ******/
DROP PROCEDURE [dbo].[SP_CNoBrowseProduct]
GO

/****** Object:  StoredProcedure [dbo].[SP_CNoBrowseProduct]    Script Date: 24/9/2565 1:51:54 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[SP_CNoBrowseProduct]
	--ผู้ใช้และสิท
	@ptUsrCode			VARCHAR(10),
	@ptUsrLevel			VARCHAR(10),
	@ptSesAgnCode		VARCHAR(10),
	@ptSesAgnType		VARCHAR(10),
	@ptSesBchCodeMulti	VARCHAR(100),
	@ptSesShopCodeMulti VARCHAR(100),
	@ptSesMerCode		VARCHAR(20),
	@ptWahCode			VARCHAR(5),

	--กำหนดการแสดงข้อมูล
	@pnRow			INT,
	@pnPage			INT,
	@pnMaxTopPage	INT,

	--ค้นหาตามประเภท
	@ptFilterBy	VARCHAR(80),
	@ptSearch	VARCHAR(1000),

	--OPTION PDT
	@ptWhere				VARCHAR(8000),
	@ptNotInPdtType			VARCHAR(8000),
	@ptPdtCodeIgnorParam	VARCHAR(30),
	@ptPDTMoveon			VARCHAR(1),
	@ptPlcCodeConParam		VARCHAR(10),
	@ptDISTYPE				VARCHAR(1),
	@ptPagename				VARCHAR(10),
	@ptNotinItemString		VARCHAR(8000),
	@ptSqlCode				VARCHAR(20),
	
	--Price And Cost
	@ptPriceType	VARCHAR(30),
	@ptPplCode		VARCHAR(30),
	@ptPdtSpcCtl	VARCHAR(100),
	
	@pnLngID INT
AS
BEGIN

    DECLARE @tSQL				VARCHAR(MAX)
    DECLARE @tSQLMaster			VARCHAR(MAX)
    DECLARE @tUsrCode			VARCHAR(10)
    DECLARE @tUsrLevel			VARCHAR(10)
    DECLARE @tSesAgnCode		VARCHAR(10)
	DECLARE @tSesAgnType		VARCHAR(10)
    DECLARE @tSesBchCodeMulti	VARCHAR(100)
    DECLARE @tSesShopCodeMulti	VARCHAR(100)
    DECLARE @tSesMerCode		VARCHAR(20)
    DECLARE @tWahCode			VARCHAR(5)
    DECLARE @nRow				INT
    DECLARE @nPage				INT
    DECLARE @nMaxTopPage		INT
    DECLARE @tFilterBy			VARCHAR(80)
    DECLARE @tSearch			VARCHAR(80)
    DECLARE	@tWhere				VARCHAR(8000)
    DECLARE	@tNotInPdtType		VARCHAR(8000)
    DECLARE	@tPdtCodeIgnorParam VARCHAR(30)
    DECLARE	@tPDTMoveon			VARCHAR(1)
    DECLARE	@tPlcCodeConParam	VARCHAR(10)
    DECLARE	@tDISTYPE			VARCHAR(1)
    DECLARE	@tPagename			VARCHAR(10)
    DECLARE	@tNotinItemString	VARCHAR(8000)
    DECLARE	@tSqlCode			VARCHAR(10)
    DECLARE	@tPriceType			VARCHAR(10)
    DECLARE	@tPplCode			VARCHAR(10)
	DECLARE	@tPdtSpcCtl			VARCHAR(100)
    DECLARE @nLngID				INT


    SET @tUsrCode			= @ptUsrCode
    SET @tUsrLevel			= @ptUsrLevel
    SET @tSesAgnCode		= @ptSesAgnCode
	SET @tSesAgnType		= @ptSesAgnType
    SET @tSesBchCodeMulti	= @ptSesBchCodeMulti
    SET @tSesShopCodeMulti	= @ptSesShopCodeMulti
    SET @tSesMerCode		= @ptSesMerCode
    SET @tWahCode			= @ptWahCode

    SET @nRow			= @pnRow
    SET @nPage			= @pnPage
    SET @nMaxTopPage	= @pnMaxTopPage

    SET @tFilterBy		= @ptFilterBy
    SET @tSearch		= @ptSearch

    SET @tWhere				= @ptWhere
    SET @tNotInPdtType		= @ptNotInPdtType
    SET @tPdtCodeIgnorParam = @ptPdtCodeIgnorParam
    SET @tPDTMoveon			= @ptPDTMoveon
    SET @tPlcCodeConParam	= @ptPlcCodeConParam
    SET @tDISTYPE			= @ptDISTYPE
    SET @tPagename			= @ptPagename
    SET @tNotinItemString	= @ptNotinItemString
    SET @tSqlCode			= @ptSqlCode

    SET @tPriceType		= @ptPriceType
    SET @tPplCode		= @ptPplCode
	SET @tPdtSpcCtl		= @ptPdtSpcCtl
    SET @nLngID			= @pnLngID

    SET @tSQLMaster = ' SELECT Base.*, '

    IF @nPage = 1 BEGIN
            SET @tSQLMaster += ' COUNT(*) OVER() AS rtCountData '
    END ELSE BEGIN
            SET @tSQLMaster += ' 0 AS rtCountData '
    END

    SET @tSQLMaster += ' FROM ( '
    SET @tSQLMaster += ' SELECT DISTINCT'

    IF @nMaxTopPage > 0 BEGIN
        SET @tSQLMaster += ' TOP ' + CAST(@nMaxTopPage  AS VARCHAR(10)) + ' '
    END

        --SET @tSQLMaster += ' ROW_NUMBER () OVER (ORDER BY Products.FDCreateOn DESC) AS FNRowID,'
    SET @tSQLMaster += ' Products.FTPdtForSystem, '
    SET @tSQLMaster += ' Products.FTPdtCode,PDT_IMG.FTImgObj,'

	
    IF @ptUsrLevel != 'HQ'  BEGIN
            SET @tSQLMaster += ' PDLSPC.FTAgnCode,PDLSPC.FTBchCode,PDLSPC.FTShpCode,PDLSPC.FTMerCode, '
    END ELSE BEGIN
            SET @tSQLMaster += ' '''' AS FTAgnCode,'''' AS FTBchCode,'''' AS  FTShpCode,'''' AS FTMerCode, '
    END 

    SET @tSQLMaster += ' Products.FTPdtStaLot,'
    SET @tSQLMaster += ' Products.FTPtyCode,'
    SET @tSQLMaster += ' Products.FTPgpChain,'
    SET @tSQLMaster += ' Products.FTPdtStaVatBuy,Products.FTPdtStaVat,Products.FTVatCode,Products.FTPdtStaActive, Products.FTPdtSetOrSN, Products.FTPdtStaAlwDis,Products.FTPdtType,'
    
	/** 
		เช็ค Agency เพื่อ Join เอาราคาต้นทุน Agency 
		Update By Wasin 23/09/2022
		============================================================================================================ 
	*/
	IF (@tSesAgnCode != '' AND @tSesAgnType != '' AND @tSesAgnType = 2) BEGIN
		SET @tSQLMaster	+= ' COSTAVG.FCPdtCostStd,'
	END ELSE BEGIN
		SET @tSQLMaster	+= ' Products.FCPdtCostStd,'
	END
	/** ============================================================================================================ */

	SET @tSQLMaster += ' PDTSPL.FTSplCode,PDTSPL.FTUsrCode AS FTBuyer,PBAR.FTBarCode,PPCZ.FTPunCode,PPCZ.FCPdtUnitFact,'
    SET @tSQLMaster += ' Products.FTCreateBy,'
    SET @tSQLMaster += ' Products.FDCreateOn'
    SET @tSQLMaster += ' FROM'
    SET @tSQLMaster += ' TCNMPdt Products WITH (NOLOCK)'

    IF @tPagename = 'Promotion' BEGIN------//-----------------เงื่อนไขพิเศษ ตามหน้า-------------
				SET @tSQLMaster += ''
        --SET @tSQLMaster += ' LEFT JOIN TCNMPdtLot PDTLOT WITH (NOLOCK) ON Products.FTPdtCode = PDTLOT.FTPdtCode '
    END
    
    IF @ptUsrLevel != 'HQ'  BEGIN
        SET @tSQLMaster += ' LEFT JOIN TCNMPdtSpcBch PDLSPC WITH (NOLOCK) ON Products.FTPdtCode = PDLSPC.FTPdtCode'
    END

    SET @tSQLMaster += ' INNER JOIN TCNMPdtPackSize PPCZ WITH (NOLOCK) ON Products.FTPdtCode = PPCZ.FTPdtCode LEFT JOIN TCNMPdtBar PBAR WITH (NOLOCK)  ON Products.FTPdtCode = PBAR.FTPdtCode  AND PPCZ.FTPunCode = PBAR.FTPunCode' --//หาบาร์โค้ด
    SET @tSQLMaster += ' LEFT JOIN TCNMPdtSpl PDTSPL WITH (NOLOCK) ON PBAR.FTPdtCode = PDTSPL.FTPdtCode AND PBAR.FTBarCode = PDTSPL.FTBarCode '--//ผู้จำหน่าย
    SET @tSQLMaster += ' LEFT JOIN TCNMImgPdt AS PDT_IMG WITH(NOLOCK) ON Products.FTPdtCode = PDT_IMG.FTImgRefID AND PDT_IMG.FTImgTable = ''TCNMPdt'' AND PDT_IMG.FNImgSeq = 1 '					
    
    ---//--------การจอยตาราง------///
    IF @tFilterBy = 'FTPdtCode' AND @tSearch <> '' BEGIN
        SET @tSQLMaster += ' '--//รหัสสินค้า
    END

    IF @tFilterBy = 'TCNTPdtStkBal' BEGIN
        SET @tSQLMaster += ' LEFT JOIN TCNTPdtStkBal STK WITH(NOLOCK) ON Products.FTPdtCode = STK.FTPdtCode AND STK.FTBchCode IN ('+@tSesBchCodeMulti+') AND STK.FTWahCode = '''+@tWahCode+''' '
    END		

    --IF @tFilterBy = 'FTPdtName' AND @tSearch <> '' BEGIN
        SET @tSQLMaster += ' LEFT JOIN TCNMPdt_L PDTL WITH (NOLOCK)       ON Products.FTPdtCode = PDTL.FTPdtCode  AND PDTL.FNLngID   = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '--//หาชื่อสินค้า
    --END

    /*IF @tFilterBy = 'PDTANDBarcode' OR @tFilterBy = 'FTPlcCode' OR @tSqlCode != '' BEGIN
        SET @tSQLMaster += ' LEFT JOIN TCNMPdtPackSize PPCZ WITH (NOLOCK) ON PDT.FTPdtCode = PPCZ.FTPdtCode LEFT JOIN TCNMPdtBar PBAR WITH (NOLOCK)      ON PDT.FTPdtCode = PBAR.FTPdtCode  AND PPCZ.FTPunCode = PBAR.FTPunCode' --//หาบาร์โค้ด
    END

    IF @tFilterBy = 'FTBarCode' BEGIN
        SET @tSQLMaster += ' LEFT JOIN TCNMPdtPackSize PPCZ WITH (NOLOCK) ON PDT.FTPdtCode = PPCZ.FTPdtCode LEFT JOIN TCNMPdtBar PBAR WITH (NOLOCK)      ON PDT.FTPdtCode = PBAR.FTPdtCode  AND PPCZ.FTPunCode = PBAR.FTPunCode' --//หาบาร์โค้ด
    END*/

    IF @tFilterBy = 'FTPunCode' AND @tSearch <> '' BEGIN
        SET @tSQLMaster += ' LEFT JOIN TCNMPdtUnit_L PUNL WITH (NOLOCK)   ON PPCZ.FTPunCode = PUNL.FTPunCode AND PUNL.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' ' --//หาหน่วย
    END								

    IF @tFilterBy = 'FTPgpChain' AND @tSearch <> '' BEGIN
        SET @tSQLMaster += ' LEFT JOIN TCNMPdtGrp_L PGL WITH (NOLOCK)     ON PGL.FTPgpChain = Products.FTPgpChain AND PGL.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '--//หากลุ่มสินค้า
    END							

    IF @tFilterBy = 'FTPtyCode' AND @tSearch <> '' BEGIN
        SET @tSQLMaster += ' LEFT JOIN TCNMPdtType_L PTL WITH (NOLOCK)    ON Products.FTPtyCode = PTL.FTPtyCode   AND PTL.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '--//หาประเภทสินค้า
    END	

    IF @tFilterBy = 'FTBuyer' AND @tSearch <> '' BEGIN
        SET @tSQLMaster += ' '--//ผู้จัดซื้อ
    END

    /* IF @tSqlCode != '' BEGIN------//----------------ผู้จำหน่าย-------------------
        SET @tSQLMaster += ' LEFT JOIN TCNMPdtSpl PDTSPL WITH (NOLOCK) ON PBAR.FTPdtCode = PDTSPL.FTPdtCode AND PBAR.FTBarCode = PDTSPL.FTBarCode '--//ผู้จำหน่าย
    END*/

    ---//--------การจอยตาราง------///

    SET @tSQLMaster += ' LEFT JOIN TCNMPdtCategory CATINFO WITH (NOLOCK) ON Products.FTPdtCode = CATINFO.FTPdtCode '


	IF @tPdtSpcCtl <> '' BEGIN
		SET @tSQLMaster += ' LEFT JOIN TCNSDocCtl_L DCT WITH(NOLOCK) ON DCT.FTDctTable = '''+ @tPdtSpcCtl +''' AND	DCT.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
		SET @tSQLMaster += ' LEFT JOIN TCNMPdtSpcCtl PSC WITH(NOLOCK) ON Products.FTPdtCode = PSC.FTPdtCode AND DCT.FTDctCode = PSC.FTDctCode '
	END

	/** 
		เช็ค Agency เพื่อ Join เอาราคาต้นทุน Agency 
		Update By Wasin 23/09/2022
		==============================================================================================================================================================================
	*/
	IF (@tSesAgnCode != '' AND @tSesAgnType != '' AND @tSesAgnType = 2) BEGIN
		SET @tSQLMaster += ' LEFT JOIN TCNMPdtCostAvg	COSTAVG		WITH(NOLOCK)	ON Products.FTPdtCode	= COSTAVG.FTPdtCode		AND COSTAVG.FTAgnCode = '''+@tSesAgnCode+''' '
	END 
	/** ============================================================================================================================================================================== */

    SET @tSQLMaster += ' WHERE ISNULL(Products.FTPdtCode,'''') != '''' '

	IF @tPdtSpcCtl <> '' BEGIN
		IF @tUsrLevel = 'HQ' BEGIN
			SET @tSQLMaster += ' AND (PSC.FTPscAlwCmp = ''1'' OR PSC.FTPdtCode IS NULL OR (PSC.FTPscAlwOwner = ''1'' AND Products.FTCreateBy = '''+@tUsrCode+''')) '
		END
		IF @tUsrLevel = 'AD' BEGIN
			SET @tSQLMaster += ' AND (PSC.FTPscAlwAD = ''1'' OR PSC.FTPdtCode IS NULL OR (PSC.FTPscAlwOwner = ''1'' AND Products.FTCreateBy = '''+@tUsrCode+''')) '
		END
		IF @tUsrLevel = 'BCH' BEGIN
			SET @tSQLMaster += ' AND (PSC.FTPscAlwBch = ''1'' OR PSC.FTPdtCode IS NULL OR (PSC.FTPscAlwOwner = ''1'' AND Products.FTCreateBy = '''+@tUsrCode+''')) '
		END
		IF @tUsrLevel = 'MER' BEGIN
			SET @tSQLMaster += ' AND (PSC.FTPscAlwMer = ''1'' OR PSC.FTPdtCode IS NULL OR (PSC.FTPscAlwOwner = ''1'' AND Products.FTCreateBy = '''+@tUsrCode+''')) '
		END
		IF @tUsrLevel = 'SHP' BEGIN
			SET @tSQLMaster += ' AND (PSC.FTPscAlwShp = ''1'' OR PSC.FTPdtCode IS NULL OR (PSC.FTPscAlwOwner = ''1'' AND Products.FTCreateBy = '''+@tUsrCode+''')) '
		END
	END

    ---//--------การค้นหา------///
    IF @tFilterBy = 'FTPdtCode' AND @tSearch <> '' BEGIN
        SET @tSQLMaster += ' AND ( Products.FTPdtCode  COLLATE THAI_BIN    LIKE ''%' + @tSearch + '%'' )'--//รหัสสินค้า
    END

    IF @tFilterBy = 'FTPdtName' AND @tSearch <> '' BEGIN
        SET @tSQLMaster += ' AND ( UPPER(PDTL.FTPdtName)  COLLATE THAI_BIN    LIKE UPPER(''%' + @tSearch + '%'') ) '--//หาชื่อสินค้า
    END

    IF @tFilterBy = 'FTBarCode' AND @tSearch <> '' BEGIN
        SET @tSQLMaster += ' AND ( PBAR.FTBarCode  COLLATE THAI_BIN    LIKE ''%' + @tSearch + '%'' )' --//หาบาร์โค้ด
    END

    IF @tFilterBy = 'PDTANDBarcode' AND @tSearch <> '' BEGIN
				SET @tSQLMaster += ''
        --SET @tSQLMaster += ' AND ( PBAR.FTPdtCode =''' + @tSearch + '''  OR  PBAR.FTBarCode =''' + @tSearch + ''' )' --//หาบาร์โค้ด
    END

    IF @tFilterBy = 'FTPunCode' AND @tSearch <> '' BEGIN
        SET @tSQLMaster += ' AND ( PUNL.FTPunName  COLLATE THAI_BIN    LIKE ''%' + @tSearch + '%'' OR PUNL.FTPunCode COLLATE THAI_BIN LIKE ''%' + @tSearch + '%'' )' --//หาหน่วย
    END								

    IF @tFilterBy = 'FTPgpChain' AND @tSearch <> '' BEGIN
        SET @tSQLMaster += ' AND ( PGL.FTPgpName   COLLATE THAI_BIN    LIKE ''%' + @tSearch + '%'' OR PGL.FTPgpChainName COLLATE THAI_BIN LIKE ''%' + @tSearch + '%'' ) '--//หากลุ่มสินค้า
    END							

    IF @tFilterBy = 'FTPtyCode' AND @tSearch <> '' BEGIN
        SET @tSQLMaster += ' AND ( PTL.FTPtyName   COLLATE THAI_BIN    LIKE ''%' + @tSearch + '%'' ) '--//หาประเภทสินค้า
    END	

    IF @tFilterBy = 'FTBuyer' AND @tSearch <> '' BEGIN
        SET @tSQLMaster += ' '--//ผู้จัดซื้อ
    END

    IF @tPagename = 'Promotion' BEGIN------//-----------------เงื่อนไขพิเศษ ตามหน้า-------------
				SET @tSQLMaster += ''
        --SET @tSQLMaster += ' AND (Products.FTPdtStaLot = ''2'' OR Products.FTPdtStaLot = ''1'' AND Products.FTPdtStaLot = ''1'' AND ISNULL(PDTLOT.FTLotNo,'''') <> '''' ) '
    END
    ---//--------การค้นหา------///

    ---//--------การมองเห็นสินค้าตามผู้ใช้------///
    IF @tUsrLevel != 'HQ' BEGIN
        --//---------------------- การมองเห็นเฉพาะสินค้าตามระดับผู้ใช้--------------------------//
        SET @tSQLMaster += ' AND ( ('
        SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''+@tSesAgnCode+''' '

                    IF @tSesMerCode != '' AND @tSesMerCode != '' BEGIN 
                            SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''+@tSesMerCode+''' '
                    END

                    IF (SELECT ISNULL(FTBchCode,'') FROM TCNTUsrGroup WHERE FTUsrCode = @tUsrCode )<>'' BEGIN
                            IF (@tSesBchCodeMulti <> '') BEGIN
                                SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') IN ('+@tSesBchCodeMulti+') '
                            END ELSE BEGIN
                                SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') = '''' '
                            END
                    END
                                
                    IF @tSesShopCodeMulti != '' BEGIN 
                            SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') IN ('+@tSesShopCodeMulti+') '
                    END

        SET @tSQLMaster += ' )'
        -- |-------------------------------------------------------------------------------------------| 

        --//---------------------- การมองเห็นสินค้าระดับสาขา (สำหรับผู้ใช้ระดับร้านค้า)--------------------------//
    IF @tSesShopCodeMulti != '' BEGIN 
        SET @tSQLMaster += ' OR ('--//กรณีผู้ใช้ผูก Shp จะต้องเห็นสินค้าที่อยู่ใน Bch แต่ไม่ผูก Shp
        SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''+@tSesAgnCode+''' '
        SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''+@tSesMerCode+''' '
        SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') IN ('+@tSesBchCodeMulti+') '
        SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') = '''' '
        SET @tSQLMaster += ' )'

        SET @tSQLMaster += ' OR (' --//กรณีผู้ใช้ผูก Shp จะต้องเห็นสินค้าที่อยู่ใน Bch แต่ไม่ผูก Shp และไม่ผูก Mer
        SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''+@tSesAgnCode+''' '
        SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''' '
        SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') IN ('+@tSesBchCodeMulti+') '
        SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') = '''' '
        SET @tSQLMaster += ' )'

        SET @tSQLMaster += ' OR (' --//กรณีผู้ใช้ผูก Shp จะต้องเห็นสินค้าที่ไม่ผูก Bch และ ไม่ผูก Shp
        SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''+@tSesAgnCode+''' '
        SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''+@tSesMerCode+''' '
        SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') = '''' '
        SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') = '''' '
        SET @tSQLMaster += ' )'

        SET @tSQLMaster += ' OR (' --//กรณีผู้ใช้ผูก Shp จะต้องเห็นสินค้าที่ไม่ผูก Mer และสินค้าผูก Bch / Shp
        SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''+@tSesAgnCode+''' '
        SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''' '
        SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') IN ('+@tSesBchCodeMulti+') '
        SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') IN ('+@tSesShopCodeMulti+') '
        SET @tSQLMaster += ' )'
    END
    -- |-------------------------------------------------------------------------------------------| 

    -- //---------------------- การมองเห็นสินค้าระดับส่วนกลางหรือสินค้าที่ไม่ได้ผูกกับอะไรเลย--------------------------//
    SET @tSQLMaster += ' OR ('

    SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''+@tSesAgnCode+''' '

    IF @tSesMerCode != '' AND @tSesMerCode != '' BEGIN --//กรณีผู้ใช้ผูก Mer จะต้องเห็นสินค้าที่ไม่ได้ผูก Mer ด้วย
            SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''' '
    END

    IF (SELECT ISNULL(FTBchCode,'') FROM TCNTUsrGroup WHERE FTUsrCode= @tUsrCode)<>'' BEGIN --//กรณีผู้ใช้ผูก Bch จะต้องเห็นสินค้าที่ไม่ได้ผูก Bch ด้วย
            SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''')  = '''' '
    END

    IF @tSesShopCodeMulti != '' BEGIN 
            SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') = '''' '
    END

    SET @tSQLMaster += ' )'
    -- |-------------------------------------------------------------------------------------------| 

    -- //---------------------- การมองเห็นสินค้าระดับส่วนกลางหรือสินค้าที่ไม่ได้ผูกกับอะไรเลย--------------------------//
    SET @tSQLMaster += ' OR ('
    SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''' '
    SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''' '
    SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') = '''' '
    SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') = '''' '
    SET @tSQLMaster += ' ))'
    -- |-------------------------------------------------------------------------------------------| 

    END
    ---//--------การมองเห็นสินค้าตามผู้ใช้------///


    -----//----Option-----//------

    IF @tWhere != '' BEGIN
        SET @tSQLMaster += @tWhere
    END
    
    IF @tNotInPdtType != '' BEGIN-----//------------- ไม่แสดงสินค้าตาม ประเภทสินค้า -------------------
        SET @tSQLMaster += ' AND ISNULL(Products.FTPDtCode,'''') NOT IN ('+@tNotInPdtType+') '
    END

    IF @tPdtCodeIgnorParam != '' BEGIN----//-------------สินค้าที่ไม่ใช่ตัวข้อมูลหลักในการจัดสินค้าชุด-------------------
        SET @tSQLMaster += ' AND ISNULL(Products.FTPDtCode,'''') != '''+@tPdtCodeIgnorParam+''' '
    END

    IF @tPDTMoveon != '' BEGIN------//---------สินค้าเคลื่อนไหว---------
        SET @tSQLMaster += ' AND  Products.FTPdtStaActive = '''+@tPDTMoveon+''' '
    END

    IF @tPlcCodeConParam != '' AND @tFilterBy = 'FTPlcCode' BEGIN---/ที่เก็บ-  //กรณีที่เข้าไปหา plc code เเล้วไม่เจอ PDT เลย ต้องให้มันค้นหา โดย KEYWORD : EMPTY
            IF  @tPlcCodeConParam != 'EMPTY' BEGIN
                    SET @tSQLMaster += ' AND  PBAR.FTBarCode = '''+@tPlcCodeConParam+''' '
            END
            ELSE BEGIN
                    SET @tSQLMaster += ' AND  PPCZ.FTPdtCode = ''EMPTY'' AND PPCZ.FTPunCode = ''EMPTY'' '
            END
    END

    IF @ptDISTYPE != '' BEGIN------//----------------อนุญาตลด----------------
        SET @tSQLMaster += ' AND  Products.FTPdtStaAlwDis = '''+@ptDISTYPE+''' '
    END

    IF @tPagename = 'PI' BEGIN------//-----------------เงื่อนไขพิเศษ ตามหน้า-------------
        SET @tSQLMaster += ' AND  Products.FTPdtSetOrSN != ''4'' '
    END

    IF @tNotinItemString  != '' BEGIN-------//-----------------ไม่เอาสินค้าอะไรบ้าง NOT IN-----------
        SET @tSQLMaster += @tNotinItemString
    END

    IF @tSqlCode != '' BEGIN------//----------------ผู้จำหน่าย-------------------
        SET @tSQLMaster += ' AND  ( PDTSPL.FTSplCode = '''+@tSqlCode+'''  OR ISNULL(PDTSPL.FTSplCode,'''') = '''' ) '
    END
    -----//----Option-----//------
        
    SET @tSQLMaster += ' ) Base '

    IF @nRow != ''  BEGIN------------เงื่อนไขพิเศษ แบ่งหน้า----
        SET @tSQLMaster += ' ORDER BY Base.FDCreateOn DESC '
        SET @tSQLMaster += ' OFFSET '+CAST(((@nPage-1)*@nRow) AS VARCHAR(10))+' ROWS FETCH NEXT '+CAST(@nRow AS VARCHAR(10))+' ROWS ONLY'
    END
    ----//----------------------Data Master And Filter-------------//			

    ----//----------------------Query Builder-------------//

    SET @tSQL = '  SELECT PDT.rtCountData ,PDT.FTAgnCode,PDT.FTBchCode AS FTPdtSpcBch,PDT.FTShpCode,PDT.FTMerCode,PDT.FTImgObj,';
    SET @tSQL += ' PDT.FTPdtCode,PDT_L.FTPdtName,PDT.FTPdtForSystem,PDT.FTPdtStaVatBuy,PDT.FTPdtStaVat,PDT.FTVatCode,ISNULL(VAT.FCVatRate, 0) AS FCVatRate, '
    SET @tSQL += ' PDT.FTPdtStaActive,PDT.FTPdtSetOrSN,PDT.FTPgpChain,PDT.FTPtyCode,ISNULL(PDT_AGE.FCPdtCookTime,0) AS FCPdtCookTime,ISNULL(PDT_AGE.FCPdtCookHeat,0) AS FCPdtCookHeat, '
    SET @tSQL += ' PDT.FTPunCode,PDT_UNL.FTPunName,PDT.FCPdtUnitFact, PDT.FTSplCode,PDT.FTBuyer,PDT.FTBarCode,PDT.FTPdtStaAlwDis,PDT.FTPdtType,'
	SET @tSQL += ' ISNULL(PDT.FCPdtCostStd,0) AS FCPdtCostStd,'
	SET @tSQL += ' PDT.FTPdtStaLot'

    IF @tPriceType = 'Pricesell' OR @tPriceType = '' BEGIN------///ถ้าเป็นราคาขาย---
        SET @tSQL += '  ,0 AS FCPgdPriceNet,VPA.FCPgdPriceRet AS FCPgdPriceRet,0 AS FCPgdPriceWhs'
    END

    IF @tPriceType = 'Price4Cst' BEGIN------// //ถ้าเป็นราคาทุน-----
        SET @tSQL += '  ,0 AS FCPgdPriceNet,0 AS FCPgdPriceWhs,'
        SET @tSQL += '  CASE'
        SET @tSQL += '  WHEN ISNULL(PCUS.FCPgdPriceRet,0) <> 0 THEN PCUS.FCPgdPriceRet'
        SET @tSQL += '  WHEN ISNULL(PBCH.FCPgdPriceRet,0) <> 0 THEN PBCH.FCPgdPriceRet'
        --SET @tSQL += '  WHEN ISNULL(PEMPTY.FCPgdPriceRet,0) <> 0 THEN PEMPTY.FCPgdPriceRet'
        SET @tSQL += '  ELSE 0'
        SET @tSQL += '  END AS FCPgdPriceRet'
    END

    IF @tPriceType = 'Cost' BEGIN------//-----
        SET @tSQL += '  ,ISNULL(FCPdtCostAVGIN,0)		AS FCPdtCostAVGIN,'
        SET @tSQL += '  ISNULL(VPC.FCPdtCostAVGEx,0)	AS FCPdtCostAVGEx  , ISNULL(FCPdtCostLast,0)      AS FCPdtCostLast,'
        SET @tSQL += '  ISNULL(VPC.FCPdtCostFIFOIN,0)	AS FCPdtCostFIFOIN , ISNULL(FCPdtCostFIFOEx,0)    AS FCPdtCostFIFOEx'
    END

    SET @tSQL += ' FROM ('
    SET @tSQL +=  @tSQLMaster
    SET @tSQL += ' ) PDT ';
    SET @tSQL += ' LEFT JOIN TCNMPdt_L AS PDT_L WITH(NOLOCK) ON PDT.FTPdtCode = PDT_L.FTPdtCode AND PDT_L.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
    SET @tSQL += ' LEFT JOIN TCNMPdtUnit_L AS PDT_UNL WITH(NOLOCK) ON PDT.FTPunCode = PDT_UNL.FTPunCode  AND PDT_UNL.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''''
    --SET @tSQL += ' LEFT OUTER JOIN TCNMImgPdt AS PDT_IMG WITH(NOLOCK) ON PDT.FTPdtCode = PDT_IMG.FTImgRefID AND PDT_IMG.FTImgTable = ''TCNMPdt'' AND PDT_IMG.FNImgSeq = 1 '
    SET @tSQL += ' LEFT OUTER JOIN TCNMPdtAge AS PDT_AGE WITH(NOLOCK) ON PDT.FTPdtCode = PDT_AGE.FTPdtCode '
    SET @tSQL += ' LEFT OUTER JOIN VCN_VatActive AS VAT WITH(NOLOCK) ON PDT.FTVatCode = VAT.FTVatCode '

    IF @tPriceType = 'Pricesell' OR @tPriceType = ''  BEGIN------//-----
        --SET @tSQL += '  '
        SET @tSQL += '  LEFT JOIN VCN_Price4PdtActive VPA WITH(NOLOCK) ON VPA.FTPdtCode = PDT.FTPdtCode AND VPA.FTPunCode = PDT_UNL.FTPunCode'
    END

    IF @tPriceType = 'Price4Cst' BEGIN

			--//----ราคาของ customer
      SET @tSQL += 'LEFT JOIN ( '
			SET @tSQL += 'SELECT '
			SET @tSQL += '	BP.FNRowPart,BP.FTPdtCode,BP.FTPunCode,BP.FDPghDStart,BP.FCPgdPriceNet,BP.FCPgdPriceWhs, '
			SET @tSQL += '	CASE '
			SET @tSQL += '		WHEN ADJ.FTPghStaAdj = ''2'' AND ADJ.FTPdtCode IS NOT NULL THEN ';
			SET @tSQL += ' 			CONVERT (NUMERIC (18, 4),(BP.FCPgdPriceRet - (BP.FCPgdPriceRet * (ADJ.FCPgdPriceRet * 0.01)))) '
			SET @tSQL += '		WHEN ADJ.FTPghStaAdj = ''3'' AND ADJ.FTPdtCode IS NOT NULL THEN '
			SET @tSQL += ' 			CONVERT(NUMERIC(18,4), BP.FCPgdPriceRet - ADJ.FCPgdPriceRet) '
			SET @tSQL += '		WHEN ADJ.FTPghStaAdj = ''4'' AND ADJ.FTPdtCode IS NOT NULL THEN '
			SET @tSQL += ' 			CONVERT(NUMERIC(18,4), ((BP.FCPgdPriceRet * (ADJ.FCPgdPriceRet*0.01)) + BP.FCPgdPriceRet)) '
			SET @tSQL += '		WHEN ADJ.FTPghStaAdj = ''5'' AND ADJ.FTPdtCode IS NOT NULL THEN '
			SET @tSQL += ' 			CONVERT(NUMERIC(18,4), BP.FCPgdPriceRet + ADJ.FCPgdPriceRet) '
			SET @tSQL += '	ELSE BP.FCPgdPriceRet '
			SET @tSQL += '	END AS FCPgdPriceRet '
			SET @tSQL += 'FROM ( '
			SET @tSQL += '	SELECT '
			SET @tSQL += '		ROW_NUMBER() OVER (PARTITION BY FTPdtCode,FTPunCode ORDER BY FTPplCode DESC, FTPghDocType DESC , FDPghDStart DESC) AS FNRowPart, '
			SET @tSQL += '		CONVERT(VARCHAR(16), FDPghDStart, 121) AS FDPghDStart, '
			SET @tSQL += '		FTPdtCode,FTPunCode,0 AS FCPgdPriceNet,FCPgdPriceRet,0 AS FCPgdPriceWhs,FTPplCode '
			SET @tSQL += '   FROM TCNTPdtPrice4PDT WITH(NOLOCK) '
			SET @tSQL += '   WHERE FDPghDStart <= CONVERT(VARCHAR(10), GETDATE(), 121) AND FTPghStaAdj = ''1'' '
				IF @tPplCode = '' 
					BEGIN SET @tSQL += '   AND ISNULL(FTPplCode,'''') = '''' ' END 
				ELSE
					BEGIN SET @tSQL += '   AND (FTPplCode = '''+@tPplCode+''' OR ISNULL(FTPplCode,'''') = '''')  ' END
			SET @tSQL += ') BP '
			SET @tSQL += 'LEFT JOIN ( '
			SET @tSQL += '	SELECT '
			SET @tSQL += '		ROW_NUMBER() OVER (PARTITION BY FTPdtCode,FTPunCode ORDER BY FTPplCode DESC, FTPghDocType DESC , FDPghDStart DESC) AS FNRowPart, '
			SET @tSQL += '		CONVERT(VARCHAR(16), FDPghDStart, 121) AS FDPghDStart, '
			SET @tSQL += '		FTPdtCode,FTPunCode,0 AS FCPgdPriceNet,FCPgdPriceRet,0 AS FCPgdPriceWhs,FTPghStaAdj,FTPplCode '
			SET @tSQL += '   FROM TCNTPdtPrice4PDT WITH(NOLOCK) '
			SET @tSQL += '   WHERE FDPghDStart <= CONVERT(VARCHAR(10), GETDATE(), 121) AND FTPghStaAdj <> ''1'' '
				IF @tPplCode = '' 
					BEGIN SET @tSQL += ' AND ISNULL(FTPplCode,'''') = '''' ' END 
				ELSE 
					BEGIN SET @tSQL += ' AND (FTPplCode = '''+@tPplCode+''' OR ISNULL(FTPplCode,'''') = '''') ' END
			SET @tSQL += ' ) ADJ ON BP.FTPdtCode = ADJ.FTPdtCode AND BP.FTPunCode = ADJ.FTPunCode '
			SET @tSQL += ' WHERE BP.FNRowPart = 1 '
			SET @tSQL += ' AND (ADJ.FTPdtCode IS NULL OR ADJ.FNRowPart = 1) '
			SET @tSQL += ' ) PCUS ON PDT.FTPdtCode = PCUS.FTPdtCode AND PDT.FTPunCode = PCUS.FTPunCode ' 
		
			--// --ราคาของสาขา
			SET @tSQL += ' LEFT JOIN ('
			SET @tSQL += ' SELECT * FROM ('
			SET @tSQL += ' SELECT '
			SET @tSQL += ' ROW_NUMBER () OVER ( PARTITION BY FTPdtCode,FTPunCode ORDER BY FTPghDocType DESC , FDPghDStart DESC ) AS FNRowPart,'
			SET @tSQL += ' FTPdtCode , '
			SET @tSQL += ' FTPunCode , '
			SET @tSQL += ' FCPgdPriceRet '
			SET @tSQL += ' FROM TCNTPdtPrice4PDT WHERE  '
			SET @tSQL += ' FDPghDStart <= CONVERT (VARCHAR(10), GETDATE(), 121)'
			SET @tSQL += ' AND FDPghDStop >= CONVERT (VARCHAR(10), GETDATE(), 121)'
			SET @tSQL += ' AND FTPghTStart <= CONVERT(time,GETDATE())'
			SET @tSQL += ' AND FTPghTStop >= CONVERT(time,GETDATE())'
			SET @tSQL += ' AND (FTPghDocType <> 3 AND FTPghDocType <> 4) '
			SET @tSQL += ' AND ISNULL(FTPplCode,'''') = '''' OR FTPplCode = (SELECT FTPplCode FROM TCNMBranch WHERE FTPplCode != '''' AND FTBchCode = (SELECT TOP 1 FTBchCode FROM TCNMBranch WHERE FTAgnCode = '''+@tSesAgnCode+''' ))'
			SET @tSQL += ') AS PCUS '
			SET @tSQL += ' WHERE PCUS.FNRowPart = 1 '
			SET @tSQL += ' ) PBCH ON PDT.FTPdtCode = PBCH.FTPdtCode AND PDT.FTPunCode = PBCH.FTPunCode '
    END

    IF @tPriceType = 'Cost' BEGIN
        SET @tSQL += '  LEFT JOIN VCN_ProductCost VPC WITH(NOLOCK) ON VPC.FTPdtCode = PDT.FTPdtCode'
    END
		
	-- SELECT @tSQL
	-- PRINT(@tSQL)
    EXECUTE(@tSQL)
END
GO


/****** Object:  StoredProcedure [dbo].[SP_RPTxStockAllCompareTextfile]    Script Date: 24/9/2565 1:52:40 ******/
DROP PROCEDURE [dbo].[SP_RPTxStockAllCompareTextfile]
GO

/****** Object:  StoredProcedure [dbo].[SP_RPTxStockAllCompareTextfile]    Script Date: 24/9/2565 1:52:40 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

-- =============================================
-- Author:		รายงานต้นทุนสินค้าตามการส่ง Textfile ต้นทุน
-- Create date: 21/04/2022 Wasin
-- Description:	
-- =============================================
CREATE PROCEDURE [dbo].[SP_RPTxStockAllCompareTextfile]
	@ptRptCode			Varchar(100),
	@ptUsrSessionID		VARCHAR(100),
	@ptAgnCode			VARCHAR(20),
	@ptAgnType			VARCHAR(10),
	@ptBchCode			VARCHAR(255),
	@ptPdtCodeFrom		VARCHAR(255),
	@ptPdtCodeTo		VARCHAR(255),
	@ptPdtUnitCodeFrom	VARCHAR(255),
	@ptPdtUnitCodeTo	VARCHAR(255),
	@ptCate1CodeFrom	VARCHAR(255),
	@ptCate2CodeFrom	VARCHAR(255),
	@pnLangID			INT , 
	@pnResult			INT OUTPUT
AS
BEGIN TRY
	DECLARE @tSQL VARCHAR(MAX)
	SET @tSQL		= ''
	
	DECLARE @tSQLFilter VARCHAR(MAX)
	SET @tSQLFilter = ''
	 
	-- Filter Branch Code
	IF (@ptBchCode <> '' OR @ptBchCode <> NULL)
	BEGIN
		SET @tSQLFilter	+= ' AND CRD.FTBchCode IN ( ' +  @ptBchCode + ' ) '
	END

	-- Filter Product
	IF ((@ptPdtCodeFrom <> '' OR @ptPdtCodeFrom <> NULL) AND  (@ptPdtCodeTo <> '' OR @ptPdtCodeTo <> NULL))
	BEGIN
		SET @tSQLFilter	+= ' AND CRD.FTPdtCode BETWEEN ''' + @ptPdtCodeFrom + ''' AND ''' + @ptPdtCodeTo + ''' '
	END

	-- Filter Product Unit
	IF ((@ptPdtUnitCodeFrom <> '' OR @ptPdtUnitCodeFrom <> NULL) AND  (@ptPdtUnitCodeTo <> '' OR @ptPdtUnitCodeTo <> NULL))
	BEGIN
		SET @tSQLFilter	+= ' AND PUN.FTPunCode BETWEEN ''' + @ptPdtUnitCodeFrom + ''' AND ''' + @ptPdtUnitCodeTo + ''' '
	END

	-- Filter Product Cat 1
	IF (@ptCate1CodeFrom <> '' OR @ptCate1CodeFrom <> NULL)
	BEGIN
		SET @tSQLFilter	+= ' AND  CAT.FTPdtCat1 = ''' + @ptCate1CodeFrom + ''''
	END

	-- Filter Product Cat 2
	IF (@ptCate2CodeFrom <> '' OR @ptCate2CodeFrom <> NULL)
	BEGIN
		SET @tSQLFilter	+= ' AND CAT.FTPdtCat2 = ''' + @ptCate2CodeFrom + ''''
	END

	-- Delete Table Temp
	DELETE FROM TRPTStockAllCompareTextfileTmp WHERE FTUsrSession = ''+@ptUsrSessionID+''

	SET @tSQL	+= ' INSERT INTO TRPTStockAllCompareTextfileTmp ( '
	SET @tSQL	+= '	FTRptCode,FTUsrSession,FTBchCode,FTBchRefID,FTBchName,FTPdtCode,FTPdtName,FTPunCode,FTPunName,FTPdtCat1,FTPdtCat2,FTCatName,FTMapUsrValue,FCXtdQty,FCXtdCost,FCXtdAmount'
	SET @tSQL	+= ' )'
	SET @tSQL	+= ' SELECT   '
	SET @tSQL	+=		''''+@ptRptCode +''' AS FTRptCode, '''+ @ptUsrSessionID +''' AS FTUsrSession,'
	SET @tSQL	+= '	CRD.FTBchCode,'
	SET @tSQL	+= '	BC.FTBchRefID,'
	SET @tSQL	+= '	BCL.FTBchName,'
	SET @tSQL	+= '	CRD.FTPdtCode,'
	SET @tSQL	+= '	PDTL.FTPdtName,'
	SET @tSQL	+= '	PUN.FTPunCode,'
	SET @tSQL	+= '	PUN.FTPunName,'
	SET @tSQL	+= '	CAT.FTPdtCat1,'
	SET @tSQL	+= '	CAT.FTPdtCat2,'
	SET @tSQL	+= '	INL.FTCatName,'
	SET @tSQL	+= '	MAP90.FTMapUsrValue,'
	SET @tSQL	+= '	SUM(CRD.FCStkQty) AS FCXtdQty,'

	/** 
		เช็ค Agency เพื่อ Join เอาราคาต้นทุน Agency 
		Update By Wasin 23/09/2022
		==============================================================================================================================================================================
	*/
	IF (@ptAgnCode != '' AND @ptAgnType != 0 AND @ptAgnType = 2) BEGIN
		SET @tSQL	+= '	SUM(COSTAVG.FCPdtCostStd) AS FCXtdCost,'
		SET @tSQL	+= '	SUM((CRD.FCStkQty * COSTAVG.FCPdtCostStd)) AS FCXtdAmount'
	END ELSE BEGIN
		SET @tSQL	+= '	SUM(PDT.FCPdtCostStd) AS FCXtdCost,'
		SET @tSQL	+= '	SUM((CRD.FCStkQty * PDT.FCPdtCostStd)) AS FCXtdAmount'
	END
	/** ============================================================================================================================================================================== */

	SET @tSQL	+= ' FROM TCNTPdtStkCrdME CRD WITH(NOLOCK)'
	SET @tSQL	+= ' INNER JOIN	TLKMMapping MAP90 WITH(NOLOCK) ON LEFT(CRD.FTPdtCode,7) = MAP90.FTMapDefValue AND MAP90.FTMapCode= ''PDTCOSTDIV90'''
	SET @tSQL	+= ' LEFT JOIN	TCNMPDT PDT WITH(NOLOCK) ON CRD.FTPdtCode = PDT.FTPdtCode'
	SET @tSQL	+= ' LEFT JOIN	TCNMBranch BC WITH(NOLOCK) ON CRD.FTBchCode = BC.FTBchCode'
	SET @tSQL	+= ' LEFT JOIN	TCNMBranch_L BCL WITH(NOLOCK) ON   CRD.FTBchCode = BCL.FTBchCode'
	SET @tSQL	+= ' LEFT JOIN	TCNMPDT_L PDTL WITH(NOLOCK) ON CRD.FTPdtCode = PDTL.FTPdtCode'
	SET @tSQL	+= ' LEFT JOIN	TCNMPdtGrp_L GL WITH(NOLOCK) ON PDT.FTPgpChain = GL.FTPgpChain'
	SET @tSQL	+= ' LEFT JOIN	TCNMPdtCategory Cat WITH(NOLOCK) ON PDT.FTPdtCode = CAT.FTPdtCode'
	SET @tSQL	+= ' LEFT JOIN	TCNMPdtCatInfo_L INL WITH(NOLOCK) ON CAT.FTPdtCat2 = INL.FTCatCode'
	SET @tSQL	+= ' LEFT JOIN	TCNMPdtPackSize PKS WITH(NOLOCK) ON CRD.FTPdtCode = PKS.FTPdtCode'
	SET @tSQL	+= ' LEFT JOIN	TCNMPdtUnit_L PUN WITH(NOLOCK) ON PKS.FTPunCode = PUN.FTPunCode'

	/** 
		เช็ค Agency เพื่อ Join เอาราคาต้นทุน Agency 
		Update By Wasin 23/09/2022
		==============================================================================================================================================================================
	*/
	IF (@ptAgnCode != '' AND @ptAgnType != 0 AND @ptAgnType = 2) BEGIN
		SET @tSQL += ' LEFT JOIN TCNMPdtCostAvg	COSTAVG WITH(NOLOCK) ON CRD.FTPdtCode = COSTAVG.FTPdtCode AND COSTAVG.FTAgnCode = '''+@ptAgnCode+''' '
	END 
	/** ============================================================================================================================================================================== */

	SET @tSQL	+= ' WHERE CRD.FCStkQty > 0  AND ISNULL( PKS.FCPdtUnitFact, 0 ) = 1 '
	SET @tSQL	+= @tSQLFilter
	SET @tSQL	+= ' GROUP BY CRD.FTBchCode,CRD.FTPdtCode,PDTL.FTPdtName,FTPunName,BCL.FTBchName,PUN.FTPunCode,PDTL.FTPdtName,MAP90.FTMapUsrValue,CAT.FTPdtCat1,CAT.FTPdtCat2,inl.FTCatName,inl.FTCatName,GL.FTPgpName,BC.FTBchRefID '
	SET @tSQL	+= ' ORDER BY CRD.FTBchCode,BCL.FTBchName,CRD.FTPdtCode'


	-- print @tSQL
 	EXECUTE(@tSQL)

	 return 0
END TRY	
BEGIN CATCH

	SET @pnResult = -1
	RETURN @pnResult
	
END CATCH
GO


/****** Object:  StoredProcedure [dbo].[SP_RPTxStockBal1002001]    Script Date: 24/9/2565 1:53:05 ******/
DROP PROCEDURE [dbo].[SP_RPTxStockBal1002001]
GO

/****** Object:  StoredProcedure [dbo].[SP_RPTxStockBal1002001]    Script Date: 24/9/2565 1:53:05 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[SP_RPTxStockBal1002001] 
	@pnLngID		INT , 
	@ptComName		VARCHAR(100),
	@ptRptCode		VARCHAR(100),
	@ptUsrSession	VARCHAR(255),
	@pnFilterType	INT, --1 BETWEEN 2 IN
	-- Agency
	@ptAgnCode		VARCHAR(10),
	@ptAgnType		VARCHAR(1),
	--สาขา
	@ptBchL			VARCHAR(8000),
	@ptBchF			VARCHAR(5),
	@ptBchT			VARCHAR(5),
	@ptWahCodeF		VARCHAR(100),
	@ptWahCodeT		VARCHAR(100),
	@ptCate1F		VARCHAR(100),
	@ptCate2F		VARCHAR(100),
	@FTResult		VARCHAR(8000) OUTPUT
AS
BEGIN TRY
	DECLARE @tSQL			VARCHAR(8000)
	DECLARE @tSQL_Filter	VARCHAR(8000)
	DECLARE @nLngID			INT
	DECLARE @tComName		VARCHAR(100)
	DECLARE @tRptCode		VARCHAR(100)
	DECLARE @tUsrSession	VARCHAR(255)
	-- Agency
	DECLARE @tAgnCode		VARCHAR(10)
	DECLARE @tAgnType		VARCHAR(1)
	--Branch Code
	DECLARE @tBchF			VARCHAR(5)
	DECLARE @tBchT			VARCHAR(5)
	DECLARE @tWahCodeF		VARCHAR(100)
	DECLARE @tWahCodeT		VARCHAR(100)			
	DECLARE @tCate1F		VARCHAR(100)
	DECLARE @tCate2F		VARCHAR(100)

	SET @tComName		= @ptComName
	SET @tRptCode		= @ptRptCode
	SET @tUsrSession	= @ptUsrSession
	SET @nLngID			= @pnLngID
	-- Agency
	SET	@tAgnCode		= @ptAgnCode
	SET @tAgnType		= @ptAgnType
	--Branch
	SET @tBchF			= @ptBchF
	SET @tBchT			= @ptBchT
	SET @tWahCodeF		= @ptWahCodeF
	SET @tWahCodeT		= @ptWahCodeT
	SET @tCate1F		= @ptCate1F
	SET @tCate2F		= @ptCate2F
	SET @tSQL_Filter	= ' WHERE 1 = 1 AND PDT1.FTPdtStaActive = 1  '

	IF(@nLngID = '' OR @nLngID = NULL) 
	BEGIN
		SET @nLngID = 1
	END ELSE IF(@nLngID <> '') 
	BEGIN
		SET @nLngID = @pnLngID
	END

	IF @ptBchL = null 
	BEGIN
		SET @ptBchL = ''
	END

	IF @tBchF = null 
	BEGIN
		SET @tBchF = ''
	END
	IF @tBchT = null OR @tBchT = ''
	BEGIN
		SET @tBchT = @tBchF
	END

	IF @pnFilterType = '1'
	BEGIN
		IF (@tBchF <> '' AND @tBchT <> '')
			BEGIN
                SET @tSQL_Filter +=' AND STK.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
            END	
	END

	IF @pnFilterType = '2'
	BEGIN
		IF (@ptBchL <> '' )
		BEGIN
			SET @tSQL_Filter +=' AND STK.FTBchCode IN (' + @ptBchL + ')'
		END	
	END

	--คลังสินค้า
	IF(@tWahCodeF = '' OR @tWahCodeF = NULL)
	BEGIN
		SET @tWahCodeF = ''
	END
	ELSE IF(@tWahCodeF <> '')
	BEGIN
		SET @tWahCodeF = @tWahCodeF
	END

	--ถึงคลัง
	IF(@tWahCodeT = '' OR @tWahCodeT = NULL)
	BEGIN
		SET @tWahCodeT = ''
	END
	ELSE IF(@tWahCodeT <> '')
	BEGIN
		SET @tWahCodeT = @tWahCodeT
	END

	IF(@tWahCodeF <> '' AND @tWahCodeT <> '')
	BEGIN 
		SET @tSQL_Filter += ' AND STK.FTWahCode BETWEEN '''+@tWahCodeF+''' AND '''+@tWahCodeT+''' ' 
	END
	ELSE IF(@tWahCodeF = '' AND @tWahCodeT = '')
	BEGIN 
		SET @tSQL_Filter += ''
	END							
				
	IF(@tCate1F <> '')
	BEGIN 
		SET @tSQL_Filter += ' AND PDTCAT.FTPdtCat1 IN ('+@tCate1F+') ' 
	END
								
	IF(@tCate2F <> '')
	BEGIN 
		SET @tSQL_Filter += ' AND PDTCAT.FTPdtCat2 IN ('+@tCate2F+') ' 
	END
        
	DELETE FROM TRPTPdtStkBalTmp  WHERE FTComName =  '' + @tComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''

	SET @tSQL	 = ' INSERT INTO TRPTPdtStkBalTmp ('
	SET @tSQL	+= '	FTComName,FTRptCode,FTUsrSession,FTWahCode,FTWahName,FTPdtCode,FTPdtName,FCStkQty,'
	SET @tSQL	+= '	FTPgpChainName,FCPdtCostAVGEX,FCPdtCostTotal,FTBchCode,FTBchName,FCPdtCostStd,FCPdtCostStdTotal,FTPdtCatName1,FTPdtCatName2'
	SET @tSQL	+= ' )'

	SET @tSQL	+= ' SELECT'
	SET @tSQL	+= '	'''+ @tComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'
	SET @tSQL	+= '	WAH.FTWAHCODE,WAH.FTWahName,'
	SET @tSQL	+= '	PDT.FTPdtCode,PDT.FTPdtName,'
	SET @tSQL	+= '	STK.FCStkQty,Grp_L.FTPgpChainName,'
	SET @tSQL	+= '	ISNULL(AvgCost.FCPdtCostAVGEX,0) AS FCPdtCostAVGEX,'
	SET @tSQL	+= '	ISNULL(AvgCost.FCPdtCostAVGEX,0)* ISNULL(STK.FCStkQty,0) AS FCPdtCostTotal,'
	SET @tSQL	+= '	BCHL.FTBchCode,BCHL.FTBchName,'
	/** 
		เช็ค Agency เพื่อ Join เอาราคาต้นทุน Agency 
		Update By Wasin 23/09/2022
		======================================================================================================================================================================
	*/
	IF (@ptAgnCode != '' AND @ptAgnType != 0 AND @ptAgnType = 2) 
	BEGIN
	SET @tSQL	+= '	ISNULL(COSTFC.FCPdtCostStd,0) AS FCPdtCostStd,'
	SET @tSQL	+= '	ISNULL(COSTFC.FCPdtCostStd,0)* ISNULL(STK.FCStkQty,0) AS FCPdtCostStdTotal,'
	END ELSE BEGIN
	SET @tSQL	+= '	ISNULL(AvgCost.FCPdtCostStd,0) AS FCPdtCostStd,'
	SET @tSQL	+= '	ISNULL(AvgCost.FCPdtCostStd,0)* ISNULL(STK.FCStkQty,0) AS FCPdtCostStdTotal,'
	END
	/** ===================================================================================================================================================================== */

	SET @tSQL	+= '	PDTCATL1.FTCatName,PDTCATL2.FTCatName'
	SET @tSQL	+= ' FROM TCNTPDTSTKBAL STK WITH (NOLOCK)'
	SET @tSQL	+= ' LEFT JOIN VCN_ProductCost AvgCost	WITH (NOLOCK) ON STK.FTPdtCode = AvgCost.FTPdtCode'

	/** 
		เช็ค Agency เพื่อ Join เอาราคาต้นทุน Agency 
		Update By Wasin 23/09/2022
		=====================================================================================================================================================================
	*/
	SET @tSQL	+= ' LEFT JOIN TCNMPdtCostAvg  COSTFC	WITH (NOLOCK) ON STK.FTPdtCode = COSTFC.FTPdtCode AND COSTFC.FTAgnCode = '''+@ptAgnCode+''' '
	/** ===================================================================================================================================================================== */
	SET @tSQL	+= ' LEFT JOIN TCNMPdt PDT1 WITH (NOLOCK) ON  STK.FTPdtCode = PDT1.FTPdtCode'
	SET @tSQL	+= ' LEFT JOIN TCNMPDT_L PDT WITH (NOLOCK) ON  STK.FTPDTCODE = PDT.FTPDTCODE AND PDT.FNLNGID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
	SET @tSQL	+= ' LEFT JOIN TCNMWAHOUSE_L  WAH WITH (NOLOCK) ON STK.FTWAHCODE = WAH.FTWAHCODE AND STK.FTBchCode = WAH.FTBchCode AND WAH.FNLNGID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
	SET @tSQL	+= ' LEFT JOIN TCNMBranch_L BCHL WITH (NOLOCK) ON STK.FTBchCode = BCHL.FTBchCode AND BCHL.FNLNGID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
	SET @tSQL	+= ' LEFT JOIN TCNMPdtGrp_L Grp_L WITH (NOLOCK) ON Pdt1.FTPgpChain  =  Grp_L.FTPgpChain AND WAH.FNLNGID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
	SET @tSQL	+= ' LEFT JOIN TCNMPdtCategory PDTCAT WITH (NOLOCK) ON  STK.FTPdtCode = PDTCAT.FTPdtCode'
	SET @tSQL	+= ' LEFT JOIN TCNMPdtCatInfo PDTCATINFO1 WITH ( NOLOCK ) ON PDTCAT.FTPdtCat1 = PDTCATINFO1.FTCatCode'
	SET @tSQL	+= ' LEFT JOIN TCNMPdtCatInfo PDTCATINFO2 WITH ( NOLOCK ) ON PDTCAT.FTPdtCat2 = PDTCATINFO2.FTCatCode'
	SET	@tSQL	+= ' LEFT JOIN TCNMPdtCatInfo_L PDTCATL1 WITH ( NOLOCK ) ON PDTCATL1.FTCatCode = PDTCATINFO1.FTCatCode AND PDTCATL1.FNCatLevel = PDTCATINFO1.FNCatLevel AND PDTCATL1.FNLNGID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
	SET @tSQL	+= ' LEFT JOIN TCNMPdtCatInfo_L PDTCATL2 WITH ( NOLOCK ) ON PDTCATL2.FTCatCode = PDTCATINFO2.FTCatCode AND PDTCATL2.FNCatLevel = PDTCATINFO2.FNCatLevel AND PDTCATL2.FNLNGID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
	SET @tSQL	+= @tSQL_Filter

	SET @tSQL	+=' UNION'

	SET @tSQL	+= ' SELECT'
	SET @tSQL	+= '	'''+ @tComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'
	SET @tSQL	+= '	WAH.FTWAHCODE,WAH.FTWahName,'
	SET @tSQL	+= '	PDT.FTPdtCode,PDT.FTPdtName,'
	SET @tSQL	+= '	STK.FCStkQty,Grp_L.FTPgpChainName,'
	SET @tSQL	+= '	ISNULL(AvgCost.FCPdtCostAVGEX,0) AS FCPdtCostAVGEX,'
	SET @tSQL	+= '	ISNULL(AvgCost.FCPdtCostAVGEX,0)* ISNULL(STK.FCStkQty,0) AS FCPdtCostTotal,'
	SET @tSQL	+= '	BCHL.FTBchCode,BCHL.FTBchName,'
	/** 
		เช็ค Agency เพื่อ Join เอาราคาต้นทุน Agency 
		Update By Wasin 23/09/2022
		======================================================================================================================================================================
	*/
	IF (@ptAgnCode != '' AND @ptAgnType != 0 AND @ptAgnType = 2) 
	BEGIN
	SET @tSQL	+= '	ISNULL(COSTFC.FCPdtCostStd,0) AS FCPdtCostStd,'
	SET @tSQL	+= '	ISNULL(COSTFC.FCPdtCostStd,0)* ISNULL(STK.FCStkQty,0) AS FCPdtCostStdTotal,'
	END ELSE BEGIN
	SET @tSQL	+= '	ISNULL(AvgCost.FCPdtCostStd,0) AS FCPdtCostStd,'
	SET @tSQL	+= '	ISNULL(AvgCost.FCPdtCostStd,0)* ISNULL(STK.FCStkQty,0) AS FCPdtCostStdTotal,'
	END
	/** ===================================================================================================================================================================== */
	SET @tSQL	+= '	PDTCATL1.FTCatName,PDTCATL2.FTCatName'
	SET @tSQL	+= ' FROM TCNTPDTSTKBALBch STK WITH (NOLOCK)'
	SET @tSQL	+= ' LEFT JOIN VCN_ProductCost AvgCost	WITH (NOLOCK) ON STK.FTPdtCode = AvgCost.FTPdtCode'

	/** 
		เช็ค Agency เพื่อ Join เอาราคาต้นทุน Agency 
		Update By Wasin 23/09/2022
		=====================================================================================================================================================================
	*/
	SET @tSQL	+= ' LEFT JOIN TCNMPdtCostAvg  COSTFC	WITH (NOLOCK) ON STK.FTPdtCode = COSTFC.FTPdtCode AND COSTFC.FTAgnCode = '''+@ptAgnCode+''' '
	/** ===================================================================================================================================================================== */
	
	SET @tSQL	+= ' LEFT JOIN TCNMPdt PDT1 WITH (NOLOCK) ON  STK.FTPdtCode = PDT1.FTPdtCode'
	SET @tSQL	+= ' LEFT JOIN TCNMPDT_L PDT WITH (NOLOCK) ON  STK.FTPDTCODE = PDT.FTPDTCODE AND PDT.FNLNGID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
	SET @tSQL	+= ' LEFT JOIN TCNMWAHOUSE_L  WAH WITH (NOLOCK) ON STK.FTWAHCODE = WAH.FTWAHCODE AND STK.FTBchCode = WAH.FTBchCode AND WAH.FNLNGID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
	SET @tSQL	+= ' LEFT JOIN TCNMBranch_L BCHL WITH (NOLOCK) ON STK.FTBchCode = BCHL.FTBchCode AND BCHL.FNLNGID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
	SET @tSQL	+= ' LEFT JOIN TCNMPdtGrp_L Grp_L WITH (NOLOCK) ON Pdt1.FTPgpChain  =  Grp_L.FTPgpChain AND WAH.FNLNGID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
	SET @tSQL	+= ' LEFT JOIN TCNMPdtCategory PDTCAT WITH (NOLOCK) ON  STK.FTPdtCode = PDTCAT.FTPdtCode'
	SET @tSQL	+= ' LEFT JOIN TCNMPdtCatInfo PDTCATINFO1 WITH ( NOLOCK ) ON PDTCAT.FTPdtCat1 = PDTCATINFO1.FTCatCode'
	SET @tSQL	+= ' LEFT JOIN TCNMPdtCatInfo PDTCATINFO2 WITH ( NOLOCK ) ON PDTCAT.FTPdtCat2 = PDTCATINFO2.FTCatCode'
	SET	@tSQL	+= ' LEFT JOIN TCNMPdtCatInfo_L PDTCATL1 WITH ( NOLOCK ) ON PDTCATL1.FTCatCode = PDTCATINFO1.FTCatCode AND PDTCATL1.FNCatLevel = PDTCATINFO1.FNCatLevel AND PDTCATL1.FNLNGID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
	SET @tSQL	+= ' LEFT JOIN TCNMPdtCatInfo_L PDTCATL2 WITH ( NOLOCK ) ON PDTCATL2.FTCatCode = PDTCATINFO2.FTCatCode AND PDTCATL2.FNCatLevel = PDTCATINFO2.FNCatLevel AND PDTCATL2.FNLNGID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
	SET @tSQL	+= @tSQL_Filter

	-- PRINT @tSQL
	EXECUTE(@tSQL)

END TRY
BEGIN CATCH
	return -1
END CATCH
GO



