/****** Object:  StoredProcedure [dbo].[SP_CNoBrowseProduct]    Script Date: 24/9/2565 1:51:54 ******/
-- DROP PROCEDURE [dbo].[SP_CNoBrowseProduct]
-- GO

/****** Object:  StoredProcedure [dbo].[SP_CNoBrowseProduct]    Script Date: 24/9/2565 1:51:54 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_CNoBrowseProduct]') AND type in (N'P', N'PC')) BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_CNoBrowseProduct] AS' 
END
GO

ALTER PROCEDURE [dbo].[SP_CNoBrowseProduct]
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
-- DROP PROCEDURE [dbo].[SP_RPTxStockAllCompareTextfile]
-- GO

/****** Object:  StoredProcedure [dbo].[SP_RPTxStockAllCompareTextfile]    Script Date: 24/9/2565 1:52:40 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxStockAllCompareTextfile]') AND type in (N'P', N'PC')) BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxStockAllCompareTextfile] AS' 
END
GO
-- =============================================
-- Author:		รายงานต้นทุนสินค้าตามการส่ง Textfile ต้นทุน
-- Create date: 21/04/2022 Wasin
-- Description:	
-- =============================================
ALTER PROCEDURE [dbo].[SP_RPTxStockAllCompareTextfile]
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
-- DROP PROCEDURE [dbo].[SP_RPTxStockBal1002001]
-- GO

/****** Object:  StoredProcedure [dbo].[SP_RPTxStockBal1002001]    Script Date: 24/9/2565 1:53:05 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxStockBal1002001]') AND type in (N'P', N'PC')) BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxStockBal1002001] AS' 
END
GO

ALTER PROCEDURE [dbo].[SP_RPTxStockBal1002001] 
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

/****** Object:  StoredProcedure [dbo].[SP_CNoBrowseProduct]    Script Date: 8/10/2565 0:36:29 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_CNoBrowseProduct]') AND type in (N'P', N'PC')) BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_CNoBrowseProduct] AS' 
END
GO

ALTER PROCEDURE [dbo].[SP_CNoBrowseProduct]
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
        SET @tSQL += '  LEFT JOIN VCN_ProductCost VPC WITH(NOLOCK) ON VPC.FTPdtCode = PDT.FTPdtCode AND VPC.FTAgnCode = '''+@tSesAgnCode+''' '
    END
		
	-- SELECT @tSQL
	-- PRINT(@tSQL)
     EXECUTE(@tSQL)
END
GO
/****** Object:  StoredProcedure [dbo].[SP_RPTxReprintDocTmp]    Script Date: 8/10/2565 0:36:29 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxReprintDocTmp]') AND type in (N'P', N'PC')) BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxReprintDocTmp] AS' 
END
GO

-- =============================================
-- Author:		รายงาน - ข้อมูลการพิมพ์ซ้ำ
-- Create date: 06/10/2022 Wasin
-- =============================================
ALTER PROCEDURE [dbo].[SP_RPTxReprintDocTmp]
	@pnLngID		INT , 
	@pnComName		VARCHAR(100),
	@ptRptCode		VARCHAR(100),
	@ptUsrSession	VARCHAR(255),
	@pnFilterType	INT, --1 BETWEEN 2 IN
	-- สาขา
	@ptBchL			VARCHAR(8000), --สาขา Condition IN
	@ptBchF			VARCHAR(5),
	@ptBchT			VARCHAR(5),
	-- เครื่องจุดขาย
	@ptPosL			VARCHAR(8000), --เครื่องขาย Condition IN
	@ptPosF			VARCHAR(10),
	@ptPosT			VARCHAR(10),
	-- Cashier
	@ptUsrL			VARCHAR(8000), --Cashier Condition IN
	@ptUsrF			VARCHAR(10),
	@ptUsrT			VARCHAR(10),
	-- Document Date
	@ptDocDateF		VARCHAR(10),
	@ptDocDateT		VARCHAR(10),

	@FNResult		INT OUTPUT
AS
BEGIN TRY
	DECLARE @nLngID			INT 
	DECLARE @nComName		VARCHAR(100)
	DECLARE @tRptCode		VARCHAR(100)
	DECLARE @tUsrSession	VARCHAR(255)
	DECLARE @tSql			VARCHAR(8000)
	DECLARE @tSqlIns		VARCHAR(8000)
	DECLARE @tSql1			VARCHAR(Max)
	DECLARE @tSql2			VARCHAR(8000)
	-- Branch Code
	DECLARE @tBchF			VARCHAR(5)
	DECLARE @tBchT			VARCHAR(5)
	-- Cashier
	DECLARE @tUsrF			VARCHAR(10)
	DECLARE @tUsrT			VARCHAR(10)
	-- Pos Code
	DECLARE @tPosF			VARCHAR(20)
	DECLARE @tPosT			VARCHAR(20)
	-- Document Date
	DECLARE @tDocDateF		VARCHAR(10)
	DECLARE @tDocDateT		VARCHAR(10)

	/** ================================================== SET PARAMETER ================================================== */
	SET @nLngID			= @pnLngID
	SET @nComName		= @pnComName
	SET @tUsrSession	= @ptUsrSession
	SET @tRptCode		= @ptRptCode
	-- Branch
	SET @tBchF			= @ptBchF
	SET @tBchT			= @ptBchT
	-- Pos
	SET @tPosF			= @ptPosF
	SET @tPosT			= @ptPosT
	-- Cashier
	SET @tUsrF			= @ptUsrF
	SET @tUsrT			= @ptUsrT
	-- Doc Date
	SET @tDocDateF		= @ptDocDateF
	SET @tDocDateT		= @ptDocDateT
	SET @FNResult		= 0
	-- Covert Doc Date
	SET @tDocDateF		= CONVERT(VARCHAR(10),@tDocDateF,121)
	SET @tDocDateT		= CONVERT(VARCHAR(10),@tDocDateT,121)

	/** ================================================== Check Condition Parameter ================================================== */
	/** เช็ค ภาษา */
	IF @nLngID	= NULL BEGIN	SET @nLngID	= 1 END

	/** เช็ค สาขา */
	IF @ptBchL	= NULL BEGIN	SET @ptBchL	= '' END
	IF @tBchF	= NULL BEGIN	SET @tBchF		= '' END
	IF @tBchT	= NULL OR @tBchT = '' BEGIN SET @tBchT	= @tBchF END

	/** เช็ค Pos เครื่องจุดขาย */
	IF @ptPosL	= NULL	BEGIN	SET @ptPosL = ''	END
	IF @tPosF	= NULL	BEGIN	SET @tPosF	= ''	END
	IF @tPosT	= NULL OR @tPosT = '' BEGIN	SET @tPosT = @tPosF	END

	/** เช็ค User */
	IF @ptUsrL	= NULL	BEGIN	SET @ptUsrL = ''	END
	IF @tUsrF	= NULL	BEGIN	SET @tUsrF	= ''	END
	IF @tUsrT	= NULL OR @tUsrT = ''	BEGIN	SET @tUsrT = @tUsrF	END

	/** เช็ค Doc Date */
	IF @tDocDateF = NULL BEGIN SET @tDocDateF = '' END
	IF @tDocDateT = NULL OR @tDocDateT ='' BEGIN SET @tDocDateT = @tDocDateF END

	SET @tSql1	=   ' WHERE  SV.FTEvnCode = ''008'''
		 
	IF @pnFilterType = '1'
	BEGIN
		/** Check Where Between Branch */
		IF (@tBchF <> '' AND @tBchT <> '')
		BEGIN
			SET @tSql1	+=' AND SV.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
		END
		/** Check Where Between Pos */
		IF (@tPosF <> '' AND @tPosT <> '')
		BEGIN
			SET @tSql1	+=' AND SV.FTPosCode BETWEEN ''' + @tPosF + ''' AND ''' + @tPosT + ''''
		END
		/** Check Where Between Usr Cashier */
		IF (@tUsrF <> '' AND @tUsrT <> '')
		BEGIN
			SET @tSql1	+=' AND SV.FTUsrCode BETWEEN ''' + @tUsrF + ''' AND ''' + @tUsrT + ''''
		END
	END

	IF @pnFilterType = '2'
	BEGIN
		/** Check Where In Branch */
		IF (@ptBchL <> '' )
		BEGIN
			SET @tSql1	+=' AND SV.FTBchCode IN (' + @ptBchL + ')'
		END
		/** Check Where In Pos */
		IF (@ptPosL <> '' )
		BEGIN
			SET @tSql1 +=' AND SV.FTPosCode IN (' + @ptPosL + ')'
		END
		/** Check Where In Usr Cashier */
		IF (@tUsrF <> '' AND @tUsrT <> '')
		BEGIN
			SET @tSql1 +=' AND SV.FTUsrCode BETWEEN ''' + @tUsrF + ''' AND ''' + @tUsrT + ''''
		END
	END

	/** WHERE Document Date Reprint */
	IF (@tDocDateF <> '' AND @tDocDateT <> '')
	BEGIN
    	SET @tSql1 +=' AND CONVERT(VARCHAR(10),SV.FDHisDateTime,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''''
	END


	DELETE FROM TRPTReprintDocTmp WITH (ROWLOCK) WHERE FTComName =  '' + @nComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''


	SET @tSql =  '	INSERT INTO TRPTReprintDocTmp (FTComName,FTRptCode,FTUsrSession,FTBchCode,FTBchName,FTXthDocNo,FDXthHisDateTime,FNXthReprintNum,FTXthUsrCode,FTXthUsrName)'
	SET @tSql += '	SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'
	SET @tSql += '		SV.FTBchCode AS FTXthBchCode,'
	SET @tSql += '		BL.FTBchName AS FTXthBchName,'
	SET @tSql += '		SV.FTSvnRemark AS FTXthDocNo,'
	SET @tSql += '		SV.FDHisDateTime AS FDXthHisDateTime,'
	SET @tSql += '		ROW_NUMBER() OVER(PARTITION BY SV.FTSvnRemark ORDER BY SV.FDHisDateTime) AS FNXthReprintNum,'
	SET @tSql += '		UL.FTUsrCode AS FTXthUsrCode,'
	SET @tSql += '		UL.FTUsrName AS FTXthUsrName'
	SET @tSql += '	FROM TPSTShiftEvent SV WITH(NOLOCK)'
	SET @tSql += '	LEFT JOIN TCNMBranch_L	BL WITH(NOLOCK) ON SV.FTBchCode = BL.FTBchCode AND BL.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''''
	SET @tSql += '	LEFT JOIN TCNMUser_L	UL WITH(NOLOCK) ON SV.FTSvnApvCode	= UL.FTUsrCode AND UL.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''''
	SET @tSql += @tSql1
	SET @tSql += '	ORDER BY  SV.FTBchCode,FDHisDateTime'


	-- PRINT @tSql;

	EXECUTE(@tSql)

	RETURN SELECT * FROM TRPTReprintDocTmp WHERE FTComName = ''+ @nComName + '' AND FTRptCode = ''+ @tRptCode +'' AND FTUsrSession = '' + @tUsrSession + ''

END TRY
BEGIN CATCH 
	SET @FNResult= -1
END CATCH	
GO

/****** Object:  StoredProcedure [dbo].[SP_RPTxPurVat]    Script Date: 3/11/2565 10:01:46 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxPurVat]') AND type in (N'P', N'PC')) BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxPurVat] AS' 
END
GO

ALTER PROCEDURE [dbo].[SP_RPTxPurVat] 

	@pnLngID		INT , 
	@pnComName		VARCHAR(100),
	@ptRptCode		VARCHAR(100),
	@ptUsrSession	VARCHAR(255),
	@pnFilterType	INT, --1 BETWEEN 2 IN

	--Agency
	@ptAgnL			VARCHAR(MAX), --กรณี Condition IN
	--สาขา

	@ptBchL			VARCHAR(MAX), --กรณี Condition IN

	--Shop Code
	@ptShpL			VARCHAR(MAX), --กรณี Condition IN
	--เครื่องจุดขาย

	--Supplier
	@ptSplF			VARCHAR(MAX), 
	@ptSplT			VARCHAR(MAX),-- FTSplCode รหัสผู้จำหน่าย

	@ptDocDateF		VARCHAR(10),
	@ptDocDateT		VARCHAR(10),

	@FNResult		INT OUTPUT 
AS
--------------------------------------
-- Watcharakorn 
-- Create 10/07/2019
-- Temp name  TRPTSalRCTmp

--------------------------------------
BEGIN TRY

	DECLARE @nLngID			int 
	DECLARE @nComName		VARCHAR(100)
	DECLARE @tRptCode		VARCHAR(100)
	DECLARE @tUsrSession	VARCHAR(255)
	DECLARE @tSql			VARCHAR(MAX)
	DECLARE @tSqlIns		VARCHAR(MAX)
	DECLARE @tSql1			nVARCHAR(Max)
	DECLARE @tSql2			VARCHAR(MAX)


	DECLARE @tDocDateF		VARCHAR(10)
	DECLARE @tDocDateT		VARCHAR(10)
	--ลูกค้า
	
	SET @nLngID			= @pnLngID
	SET @nComName		= @pnComName
	SET @tUsrSession	= @ptUsrSession
	SET @tRptCode		= @ptRptCode

	SET @tDocDateF		= @ptDocDateF
	SET @tDocDateT		= @ptDocDateT

	SET @FNResult		= 0

	SET @tDocDateF		= CONVERT(VARCHAR(10),@tDocDateF,121)
	SET @tDocDateT		= CONVERT(VARCHAR(10),@tDocDateT,121)

	IF @nLngID = null
	BEGIN
		SET @nLngID = 1
	END	

	IF @ptBchL = null
	BEGIN
		SET @ptBchL = ''
	END

	IF @ptAgnL = null
	BEGIN
		SET @ptAgnL = ''
	END


	IF @ptShpL = null
	BEGIN
		SET @ptShpL	= ''
	END


	IF @tDocDateF = null
	BEGIN 
		SET @tDocDateF = ''
	END

	IF @tDocDateT = null OR @tDocDateT =''
	BEGIN 
		SET @tDocDateT = @tDocDateF
	END

	IF @ptSplF =NULL -- FTSplCode รหัสผู้จำหน่าย
	BEGIN
		SET @ptSplF = ''
	END
	IF @ptSplT =null OR @ptSplT = ''
	BEGIN
		SET @ptSplT = @ptSplF
	END 

	SET @tSql1 = ' '

	IF @pnFilterType = '2'
	BEGIN
		IF (@ptBchL <> '' )
		BEGIN
			SET @tSql1 +=' AND HD.FTBchCode IN (' + @ptBchL + ')'
		END

		IF (@ptAgnL <> '' )
		BEGIN
			SET @tSql1 +=' AND Bch.FTAgnCode IN (' + @ptAgnL + ')'
		END

		IF (@ptShpL <> '')
		BEGIN
			SET @tSql1 +=' AND HD.FTShpCode IN (' + @ptShpL + ')'
		END
	
	END
	--Supplier
	IF (@ptSplF<> '') -- FTSplCode รหัสผู้จำหน่าย
	BEGIN
		SET @tSql1 +=' AND HD.FTSplCode BETWEEN ''' + @ptSplF + ''' AND ''' + @ptSplT + '''' 
	END


	IF (@tDocDateF <> '' AND @tDocDateT <> '')
	BEGIN
		SET @tSql1 +=' AND CONVERT(VARCHAR(10),FDXphDocDate,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''''
	END

	DELETE FROM TRPTPurVatTmp WITH (ROWLOCK) WHERE FTComName =  '' + @nComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''--ลบข้อมูล Temp ของเครื่องที่จะบันทึกขอมูลลง Temp
 
 	SET @tSql  = ' INSERT INTO TRPTPurVatTmp'
	SET @tSql +=' ('
	SET @tSql +=' FTComName,FTRptCode,FTUsrSession,'
	SET @tSql +=' FTAgnCode,FTAgnName,FTBchCode,FTBchName,FDXphdocDate,FTXphDocNo,FTXphDocRef,FTSplCode,FTSplName,FTSplTaxNo,'
	SET @tSql +=' FCXphAmt,FCXphVat,FCXphAmtNV,FCXphGrandTotal,'
	--*NUI 2019-11-14
	SET @tSql +=' FNAppType,FTSplBchCode,FTSplBusiness,FTEstablishment'
	SET @tSql +=' ,FTXphRefExt,FDXphRefExtDate'
	-------------
	SET @tSql +=' )'
	SET @tSql +=' SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'	
	SET @tSql +=' FTAgnCode,FTAgnName ,SalVat.FTBchCode,FTBchName,CONVERT(VARCHAR(10),FDXphDocDate,121) AS FDXphdocDate,FTXphDocNo,'
	 --FTPosCode,
	SET @tSql +=' FTXphRefInt,FTSplCode,FTSplName,FTSplTaxNo,FCXphValue,FCXphVat,FCXphAmtNV,FCXphGrand,'
	--*NUI 2019-11-14
	SET @tSql +=' FNAppType,'
	 --FTPosRegNo,
	SET @tSql +=' FTSplBchCode,FTSplBusiness,FTEstablishment' --สถานประกอบการ
	SET @tSql +=' ,FTXphRefExt,FDXphRefExtDate'
	SET @tSql +=' FROM'	
		SET @tSql +=' (SELECT Bch.FTAgnCode,Agnl.FTAgnName ,HD.FTBchCode,CONVERT(VARCHAR(10),FDXphDocDate,121) AS FDXphdocDate,'
		--HD.FTPosCode,
		SET @tSql +=' HD.FTXphDocNo,ISNULL(HDDoc.FTXshRefDocNo,'''') FTXphRefInt,HD.FTSplCode,SplAddr.FTAddName AS FTSplName ,SplAddr.FTAddTaxNo AS FTSplTaxNo,'
		 --NUI10-04-2020
		SET @tSql +=' ISNULL(FCXphVatable,0)-ISNULL(FCXphAmtNV,0)  AS FCXphValue,'
		SET @tSql +=' ISNULL(FCXphVat,0) AS FCXphVat,'
		SET @tSql +=' ISNULL(FCXphAmtNV,0)AS FCXphAmtNV,'
		 --NUI10-04-2020
		SET @tSql +=' ISNULL(FCXphGrand,0)-ISNULL(FCXphRnd,0) AS FCXphGrand,'
		 --*NUI 2019-11-14
		SET @tSql +=' 1 AS FNAppType,'
			 --POS.FTPosRegNo,
		SET @tSql +=' ISNULL(FTSplBchCode,'''') AS FTSplBchCode,ISNULL(FTSplBusiness,''2'') AS FTSplBusiness,' -- ประเภทกิจการ 1:นิติบุคคล, 2:บุคคลธรรมดา
		SET @tSql +=' CASE WHEN ISNULL(FTSplBusiness,''2'') <> ''1'' THEN '''' ELSE CASE WHEN ISNULL(FTSplStaBchOrHQ,''1'') = ''1'' THEN ''Head Office'' ELSE ''Branch''  END + CASE WHEN FTSplBchCode <> '''' THEN '' / '' + FTSplBchCode ELSE '''' END   END AS FTEstablishment'
		SET @tSql +=' ,FTXphRefExt,FDXphRefExtDate'
		SET @tSql +=' FROM TAPTPiHD HD'
		SET @tSql +=' LEFT JOIN TAPTPiHDSpl HDSpl WITH (NOLOCK) ON HD.FTBchCode = HDSpl.FTBchCode AND HD.FTXphDocNo = HDSpl.FTXphDocNo'
		SET @tSql +=' LEFT JOIN TAPTPiHDDocRef HDDoc  WITH (NOLOCK) ON HD.FTBchCode = HDDoc.FTBchCode AND HD.FTXphDocNo = HDDoc.FTXshDocNo AND FTXshRefType = ''3'' AND FTXshRefKey = ''BillNote'' '
		SET @tSql +=' LEFT JOIN TCNMSplAddress_L SplAddr  WITH (NOLOCK) ON HD.FTSplCode = SplAddr.FTSplCode AND SplAddr.FNLngID =    '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' ' 
		SET @tSql +=' LEFT JOIN TCNMBranch Bch WITH (NOLOCK) ON  HD.FTBchCode= Bch.FTBchCode'
		 	SET @tSql +=' LEFT JOIN TCNMSpl Spl ON HD.FTSplCode = Spl.FTSplCode AND FTAddGrpType = ''1'''
			SET @tSql +=' LEFT JOIN TCNMAgency_L Agnl ON Bch.FTAgnCode = AgnL.FTAgnCode AND AgnL.FNLngID =   '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' ' 
			SET @tSql +=' LEFT JOIN TCNMSpl_L Spl_L ON HD.FTSplCode = Spl_L.FTSplCode AND Spl_L.FNLngID =    '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' '  
			SET @tSql +=' LEFT JOIN TCNMShop Shp WITH (NOLOCK) ON HD.FTBchCode = Shp.FTBchCode AND HD.FTShpCode = Shp.FTShpCode'
			SET @tSql +=' WHERE 1=1 AND FTXphStaDoc = ''1'''			  			
		 SET @tSql += @tSql1	
					
		SET @tSql +=' UNION ALL'
		SET @tSql +=' SELECT Bch.FTAgnCode,Agnl.FTAgnName ,HD.FTBchCode,CONVERT(VARCHAR(10),FDXphDocDate,121) AS FDXphdocDate,'
		--HD.FTPosCode,
		SET @tSql +=' HD.FTXphDocNo,ISNULL(HDDoc.FTXshRefDocNo,'''') FTXphRefInt,HD.FTSplCode,SplAddr.FTAddName AS FTSplName ,SplAddr.FTAddTaxNo AS FTSplTaxNo,'
		 --NUI10-04-2020
		SET @tSql +=' (ISNULL(FCXphVatable,0)-ISNULL(FCXphAmtNV,0))*-1  AS FCXphValue,'
		SET @tSql +=' ISNULL(FCXphVat,0)*-1 AS FCXphVat,'
		SET @tSql +=' ISNULL(FCXphAmtNV,0)*-1 AS FCXphAmtNV,'
		 --NUI10-04-2020
		SET @tSql +=' (ISNULL(FCXphGrand,0)-ISNULL(FCXphRnd,0))*-1 AS FCXphGrand,'
		 --*NUI 2019-11-14
		SET @tSql +=' 1 AS FNAppType,'
			 --POS.FTPosRegNo,
		SET @tSql +=' ISNULL(FTSplBchCode,'''') AS FTSplBchCode,ISNULL(FTSplBusiness,''2'') AS FTSplBusiness,' -- ประเภทกิจการ 1:นิติบุคคล, 2:บุคคลธรรมดา
		SET @tSql +=' CASE WHEN ISNULL(FTSplBusiness,''2'') <> ''1'' THEN '''' ELSE CASE WHEN ISNULL(FTSplStaBchOrHQ,''1'') = ''1'' THEN ''Head Office'' ELSE ''Branch''  END + CASE WHEN FTSplBchCode <> '''' THEN '' / '' + FTSplBchCode ELSE '''' END   END AS FTEstablishment'
		SET @tSql +=' ,FTXphRefExt,FDXphRefExtDate'
		SET @tSql +=' FROM TAPTPcHD HD' 
		SET @tSql +=' LEFT JOIN TAPTPiHDSpl HDSpl WITH (NOLOCK) ON HD.FTBchCode = HDSpl.FTBchCode AND HD.FTXphDocNo = HDSpl.FTXphDocNo'
		SET @tSql +=' LEFT JOIN TAPTPiHDDocRef HDDoc  WITH (NOLOCK) ON HD.FTBchCode = HDDoc.FTBchCode AND HD.FTXphDocNo = HDDoc.FTXshDocNo AND FTXshRefType = ''3'' AND FTXshRefKey = ''BillNote'' '
		SET @tSql +=' LEFT JOIN TCNMSplAddress_L SplAddr  WITH (NOLOCK) ON HD.FTSplCode = SplAddr.FTSplCode AND SplAddr.FNLngID =    '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' ' 
		SET @tSql +=' LEFT JOIN TCNMBranch Bch WITH (NOLOCK) ON  HD.FTBchCode= Bch.FTBchCode'
		 	SET @tSql +=' LEFT JOIN TCNMSpl Spl ON HD.FTSplCode = Spl.FTSplCode AND FTAddGrpType = ''1'''
			SET @tSql +=' LEFT JOIN TCNMAgency_L Agnl ON Bch.FTAgnCode = AgnL.FTAgnCode AND AgnL.FNLngID =   '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' ' 
			SET @tSql +=' LEFT JOIN TCNMSpl_L Spl_L ON HD.FTSplCode = Spl_L.FTSplCode AND Spl_L.FNLngID =    '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' '  
			SET @tSql +=' LEFT JOIN TCNMShop Shp WITH (NOLOCK) ON HD.FTBchCode = Shp.FTBchCode AND HD.FTShpCode = Shp.FTShpCode'
			SET @tSql +=' WHERE 1=1 AND FTXphStaDoc = ''1'''			  			
		 SET @tSql += @tSql1
	 SET @tSql +=' ) SalVat LEFT JOIN'     
	 SET @tSql +=' TCNMBranch_L Bch_L ON SalVat.FTBchCode = Bch_L.FTBchCode AND Bch_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' '     
	--SET @tSql +=' TCNMBranch_L Bch_L ON SalVat.FTBchCode = Bch_L.FTBchCode AND Bch_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' '	 

	 --PRINT @tSql

	EXECUTE(@tSql)

	--RETURN SELECT * FROM TRPTPSTaxHDFullTmp WHERE FTComName = ''+ @nComName + '' AND FTRptCode = ''+ @tRptCode +'' AND FTUsrSession = '' + @tUsrSession + ''
	
END TRY

BEGIN CATCH 
	SET @FNResult= -1
END CATCH	


/****** Object:  StoredProcedure [dbo].[SP_CNoBrowseProduct]    Script Date: 4/11/2565 21:17:10 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_CNoBrowseProduct]') AND type in (N'P', N'PC')) BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_CNoBrowseProduct] AS' 
END
GO

ALTER PROCEDURE [dbo].[SP_CNoBrowseProduct]
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

    --IF @tSqlCode != '' BEGIN------//----------------ผู้จำหน่าย-------------------
    --    SET @tSQLMaster += ' AND  ( PDTSPL.FTSplCode = '''+@tSqlCode+'''  OR ISNULL(PDTSPL.FTSplCode,'''') = '''' ) '
    --END
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
        SET @tSQL += '  LEFT JOIN VCN_ProductCost VPC WITH(NOLOCK) ON VPC.FTPdtCode = PDT.FTPdtCode AND VPC.FTAgnCode = '''+@tSesAgnCode+''' '
    END
		
	-- SELECT @tSQL
	-- PRINT(@tSQL)

	EXECUTE(@tSQL)
END


/****** Object:  StoredProcedure [dbo].[SP_RPTxPurHisPdtBySpl]    Script Date: 7/11/2565 10:02:09 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxPurHisPdtBySpl]') AND type in (N'P', N'PC')) BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxPurHisPdtBySpl] AS' 
END
GO

ALTER PROCEDURE [dbo].[SP_RPTxPurHisPdtBySpl]
--ALTER PROCEDURE [dbo].[SP_RPTxSalDailyByCashierTmp]
	@pnLngID int , 
	@pnComName Varchar(100),
	@ptRptCode Varchar(100),
	@ptUsrSession Varchar(255),
	@pnFilterType int, --1 BETWEEN 2 IN

--Agency
	@ptAgnL Varchar(8000), --Agency Condition IN 
--สาขา
	@ptBchL Varchar(8000), --สาขา Condition IN
--Shop
	@ptShpL Varchar(8000), 
--Purchase
	@ptStaApv Varchar(1),--FTXphStaApv สถานะ อนุมัติ เอกสาร ว่าง:ยังไม่ทำ, 1:อนุมัติแล้ว
	                     --การส่งค่า 0 : ไม่ Condition 1 : ยังไม่อนุมัติ 2 : อนุมัติแล้ว            
	@ptStaPaid Varchar(1),-- FTXphStaPaid สถานะ รับ/จ่ายเงิน 1:ยังไม่จ่าย 2:บางส่วน, 3:ครบ
--Supplier
	@ptSplF Varchar(20), @ptSplT Varchar(20),-- FTSplCode รหัสผู้จำหน่าย
	@ptSgpF Varchar(5),@ptSgpT Varchar(5),-- FTSgpCode กลุ่มผู้จำหน่าย
	@ptStyF Varchar(5),@ptStyT Varchar(5),--FTStyCode ประเภทผู้จำหน่าย	
--Pdt
--รหัสสินค้า --FTPdtCode --รหัสสินค้า
	@ptPdtF Varchar(20),@ptPdtT Varchar(20),
--กลุ่มสินค้า --FTPgpChain 
	@ptPgpF Varchar(30),@ptPgpT Varchar(30),
--FTPtyCode --ประเภทสินค้า
	@ptPtyF Varchar(5),@ptPtyT Varchar(5),	
--FTPbnCode --ยี่ห้อ
	@ptPbnF Varchar(5),	@ptPbnT Varchar(5),
--FTPmoCode --รุ่น
	@ptPmoF Varchar(5),	@ptPmoT Varchar(5),
	@ptSaleType	 Varchar(1),--FTPdtType  --ใช้ราคาขาย 1:บังคับ, 2:แก้ไข, 3:เครื่องชั่ง, 4:น้ำหนัก 6:สินค้ารายการซ่อม
	@ptPdtActive Varchar(1),--FTPdtStaActive --สถานะ เคลื่อนไหว 1:ใช่, 2:ไม่ใช่
	@PdtStaVat Varchar(1),--FTPdtStaVat --สถานะภาษีขาย 1:มี 2:ไม่มี

	@ptDocDateF Varchar(10), @ptDocDateT Varchar(10), 
	
	@FNResult INT OUTPUT 
AS
--------------------------------------
-- Watcharakorn 
-- Create 13/05/2021
--รายงานสินค้า
-- Temp name  SP_RPTxPdtEntry

--------------------------------------
BEGIN TRY	
	DECLARE @nLngID int 
	DECLARE @nComName Varchar(100)
	DECLARE @tRptCode Varchar(100)
	DECLARE @tUsrSession Varchar(255)
	DECLARE @tSql VARCHAR(8000)
	DECLARE @tSql1 VARCHAR(8000)
	DECLARE @tSql2 VARCHAR(8000)

	--Branch Code
	DECLARE @tBchF Varchar(5)
	DECLARE @tBchT Varchar(5)
	--Cashier
	DECLARE @tUsrF Varchar(10)
	DECLARE @tUsrT Varchar(10)
	--Pos Code
	DECLARE @tPosF Varchar(20)
	DECLARE @tPosT Varchar(20)

	DECLARE @tDocDateF Varchar(10)
	DECLARE @tDocDateT Varchar(10)

	SET @nLngID = @pnLngID
	SET @nComName = @pnComName
	SET @tUsrSession = @ptUsrSession
	SET @tRptCode = @ptRptCode

	--Branch
	
	SET @FNResult= 0

	SET @ptDocDateF = CONVERT(VARCHAR(10),@ptDocDateF,121)
	SET @ptDocDateT = CONVERT(VARCHAR(10),@ptDocDateT,121)

	IF @nLngID = null
	BEGIN
		SET @nLngID = 1
	END	

	IF @ptAgnL = null
	BEGIN
		SET @ptAgnL = ''
	END

	IF @ptBchL = null
	BEGIN
		SET @ptBchL = ''
	END

	IF @ptShpL = null
	BEGIN
		SET @ptShpL = ''
	END

	--Purchase
	IF @ptStaApv = NULL ----การส่งค่า 0 : ไม่ Condition 1 : ยังไม่อนุมัติ 2 : อนุมัติแล้ว   FTXphStaApv สถานะ อนุมัติ เอกสาร ว่าง:ยังไม่ทำ, 1:อนุมัติแล้ว
	BEGIN
		SET @ptStaApv = ''
	END

	IF @ptStaPaid = NULL --FTXphStaPaid สถานะ รับ/จ่ายเงิน 1:ยังไม่จ่าย 2:บางส่วน, 3:ครบ
	BEGIN
		SET @ptStaPaid = ''
	END

	IF @ptSplF =NULL -- FTSplCode รหัสผู้จำหน่าย
	BEGIN
		SET @ptSplF = ''
	END
	IF @ptSplT =null OR @ptSplT = ''
	BEGIN
		SET @ptSplT = @ptSplF
	END 

	IF @ptSgpF =NULL -- FTSgpCode กลุ่มผู้จำหน่าย
	BEGIN
		SET @ptSgpF = ''
	END
	IF @ptSgpT =null OR @ptSgpT = ''
	BEGIN
		SET @ptSgpT = @ptSgpF
	END 

	IF @ptStyF =NULL --FTStyCode ประเภทผู้จำหน่าย
	BEGIN
		SET @ptStyF = ''
	END
	IF @ptStyT =null OR @ptStyT = ''
	BEGIN
		SET @ptStyT = @ptStyF
	END 

	IF @ptPdtF =null
	BEGIN
		SET @ptPdtF = ''
	END
	IF @ptPdtT =null OR @ptPdtT = ''
	BEGIN
		SET @ptPdtT = @ptPdtF
	END 


	IF @ptPgpF =null
	BEGIN
		SET @ptPgpT = ''
	END
	IF @ptPgpT =null OR @ptPgpT = ''
	BEGIN
		SET @ptPgpT = @ptPgpF
	END

	IF @ptPtyF =null
	BEGIN
		SET @ptPtyT = ''
	END
	IF @ptPtyT =null OR @ptPtyT = ''
	BEGIN
		SET @ptPtyT = @ptPtyF
	END

	IF @ptPmoF =null
	BEGIN
		SET @ptPmoT = ''
	END
	IF @ptPmoT =null OR @ptPmoT = ''
	BEGIN
		SET @ptPmoT = @ptPmoF
	END

	IF @ptSaleType = NULL
	BEGIN
		SET @ptSaleType = ''
	END

	IF @ptPdtActive = NULL
	BEGIN
		SET @ptPdtActive = ''
	END

	IF @PdtStaVat = NULL
	BEGIN
		SET @PdtStaVat = ''
	END

	IF @ptDocDateF = null
	BEGIN 
		SET @ptDocDateF = ''
	END

	IF @ptDocDateT = null OR @ptDocDateT =''
	BEGIN 
		SET @ptDocDateT = @ptDocDateF
	END
	
		
	SET @tSql1 =   ' WHERE 1=1 '
	SET @tSql2 =   ' WHERE 1=1 '
	--Center
	IF (@ptAgnL <> '' )
	BEGIN
		SET @tSql1 +=' AND HD.FTAgnCode IN (' + @ptAgnL + ')' --Agency
	END


	IF (@ptBchL <> '' )
	BEGIN
		SET @tSql1 +=' AND HD.FTBchCode IN (' + @ptBchL + ')' --Branch
		SET @tSql2 +=' AND HD.FTBchCode IN (' + @ptBchL + ')' --Branch
	END


	IF (@ptShpL <> '' )
	BEGIN
		SET @tSql1 +=' AND HD.FTShpCode IN (' + @ptShpL + ')' --Shop
		SET @tSql2 +=' AND HD.FTShpCode IN (' + @ptShpL + ')' --Shop
	END
	
	--Purchase
	IF (@ptStaApv<> '') --การส่งค่า 0 : ไม่ Condition 1 : ยังไม่อนุมัติ 2 : อนุมัติแล้ว  FTXphStaApv สถานะ อนุมัติ เอกสาร ว่าง:ยังไม่ทำ, 1:อนุมัติแล้ว
	BEGIN
		--SET @tSql1 +=' AND CASE WHEN FTXphStaApv = ''1'' THEN ''2'' ELSE ''1'' END  = ''' + @ptStaApv + '''' 
		SET @tSql1 +=' AND CASE WHEN FTXphStaApv =  ''' + @ptStaApv + '''' 
		SET @tSql2 +=' AND CASE WHEN FTXphStaApv =  ''' + @ptStaApv + ''''
	END

	IF (@ptStaPaid<> '') -- FTXphStaPaid สถานะ รับ/จ่ายเงิน 1:ยังไม่จ่าย 2:บางส่วน, 3:ครบ
	BEGIN
		SET @tSql1 +=' AND HD.FTXphStaPaid = ''' + @ptStaPaid + '''' 
		SET @tSql2 +=' AND HD.FTXphStaPaid = ''' + @ptStaPaid + '''' 
	END

	--Supplier
	IF (@ptSplF<> '') -- FTSplCode รหัสผู้จำหน่าย
	BEGIN
		SET @tSql1 +=' AND HD.FTSplCode BETWEEN ''' + @ptSplF + ''' AND ''' + @ptSplT + '''' 
		SET @tSql2 +=' AND HD.FTSplCode BETWEEN ''' + @ptSplF + ''' AND ''' + @ptSplT + ''''
	END

	IF (@ptSgpF<> '') -- FTSgpCode กลุ่มผู้จำหน่าย
	BEGIN
		SET @tSql1 +=' AND Spl.FTSgpCode BETWEEN ''' + @ptSgpF + ''' AND ''' + @ptSgpT + '''' 
		SET @tSql2 +=' AND Spl.FTSgpCode BETWEEN ''' + @ptSgpF + ''' AND ''' + @ptSgpT + '''' 
	END

	IF (@ptSgpF<> '') -- FTStyCode ประเภทผู้จำหน่าย
	BEGIN
		SET @tSql1 +=' AND Spl.FTStyCode BETWEEN ''' + @ptSgpF + ''' AND ''' + @ptSgpT + '''' 
		SET @tSql2 +=' AND Spl.FTStyCode BETWEEN ''' + @ptSgpF + ''' AND ''' + @ptSgpT + '''' 
	END

	--Product
	IF (@ptPdtF<> '')
	BEGIN
		SET @tSql1 +=' AND PDT.FTPdtCode BETWEEN ''' + @ptPdtF + ''' AND ''' + @ptPdtT + '''' 
		SET @tSql2 +=' AND PDT.FTPdtCode BETWEEN ''' + @ptPdtF + ''' AND ''' + @ptPdtT + '''' 
	END

	IF (@ptPgpF<> '')
	BEGIN
		SET @tSql1 +=' AND PDT.FTPgpChain BETWEEN ''' + @ptPgpF + ''' AND ''' + @ptPgpT + ''''
		SET @tSql2 +=' AND PDT.FTPgpChain BETWEEN ''' + @ptPgpF + ''' AND ''' + @ptPgpT + ''''
	END

	IF (@ptPtyF<> '')
	BEGIN
		SET @tSql1 +=' AND PDT.FTPtyCode BETWEEN ''' + @ptPtyF + ''' AND ''' + @ptPtyT + ''''
		SET @tSql2 +=' AND PDT.FTPtyCode BETWEEN ''' + @ptPtyF + ''' AND ''' + @ptPtyT + ''''
	END

	IF (@ptPbnF<> '')
	BEGIN
		SET @tSql1 +=' AND PDT.FTPbnCode BETWEEN ''' + @ptPbnF + ''' AND ''' + @ptPbnT + ''''
		SET @tSql2 +=' AND PDT.FTPbnCode BETWEEN ''' + @ptPbnF + ''' AND ''' + @ptPbnT + ''''
	END

	IF (@ptPmoF<> '')
	BEGIN
		SET @tSql1 +=' AND PDT.FTPmoCode BETWEEN ''' + @ptPmoF + ''' AND ''' + @ptPmoT + ''''
		SET @tSql2 +=' AND PDT.FTPmoCode BETWEEN ''' + @ptPmoF + ''' AND ''' + @ptPmoT + ''''
	END

	IF (@ptSaleType<> '')
	BEGIN
		SET @tSql1 +=' AND PDT.FTPdtSaleType = ''' + @ptSaleType + ''''
		SET @tSql2 +=' AND PDT.FTPdtSaleType = ''' + @ptSaleType + ''''
	END

	IF (@ptPdtActive<> '')
	BEGIN
		SET @tSql1 +=' AND PDT.FTPdtStaActive = ''' + @ptPdtActive + ''''
		SET @tSql2 +=' AND PDT.FTPdtStaActive = ''' + @ptPdtActive + ''''
	END

	IF (@PdtStaVat<> '')
	BEGIN
		SET @tSql1 +=' AND PDT.FTPdtStaVat = ''' + @PdtStaVat + ''''
		SET @tSql2 +=' AND PDT.FTPdtStaVat = ''' + @PdtStaVat + ''''
	END
	IF (@ptDocDateF <> '' AND @ptDocDateT <> '')
	BEGIN
    	SET @tSql1 +=' AND CONVERT(VARCHAR(10),HD.FDXphDocDate,121) BETWEEN ''' + @ptDocDateF + ''' AND ''' + @ptDocDateT + ''''
		SET @tSql2 +=' AND CONVERT(VARCHAR(10),HD.FDXphDocDate,121) BETWEEN ''' + @ptDocDateF + ''' AND ''' + @ptDocDateT + ''''
	
	END
	--PRINT '99999'
	DELETE FROM TRPTPurHisPdtBySplTmp  WHERE FTComName =  '' + @nComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''--Åº¢éÍÁÙÅ Temp ¢Í§à¤Ã×èÍ§·Õè¨ÐºÑ¹·Ö¡¢ÍÁÙÅÅ§ Temp

	SET @tSql = 'INSERT INTO TRPTPurHisPdtBySplTmp'
	--PRINT @tSql
	SET @tSql +=' (FTComName,FTRptCode,FTUsrSession,'
	SET @tSql +=' FTSplCode,FTSplName,FTPdtCode,FTPdtName,FTPunName,FCXpdQty,FCXpdDis,FCXpdValue,FCXpdVat ,FCXpdNetAmt'
	SET @tSql +=' )'
	--PRINT @tSql
	SET @tSql +=' SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'
	SET @tSql +=' FTSplCode,FTSplName,FTPdtCode,FTPdtName,FTPunName,'
	SET @tSql +=' SUM(CASE WHEN FNXpdPurType = ''1'' THEN FCXpdQty ELSE FCXpdQty*-1 END) AS FCXpdQty,'
	SET @tSql +=' SUM(CASE WHEN FNXpdPurType = ''1'' THEN FCXpdDis ELSE FCXpdDis*-1 END) AS FCXpdDis,'
	SET @tSql +=' SUM(CASE WHEN FNXpdPurType = ''1'' THEN FCXpdValue ELSE FCXpdValue*-1 END) AS FCXpdValue,'
	SET @tSql +=' SUM(CASE WHEN FNXpdPurType = ''1'' THEN FCXpdVat ELSE FCXpdVat*-1 END) AS FCXpdVat ,'
	SET @tSql +=' SUM(CASE WHEN FNXpdPurType = ''1'' THEN FCXpdNetAmt ELSE FCXpdNetAmt*-1 END) AS FCXpdNetAmt'
	SET @tSql +=' FROM'
		--Purchase
		SET @tSql +=' (SELECT ''1'' AS FNXpdPurType, HD.FTSplCode,SplL.FTSplName,DT.FTPdtCode,ISNULL(PdtL.FTPdtName,'''') AS FTPdtName,DT.FTPunCode,ISNULL(PunL.FTPunName,'''') AS FTPunName,'
		 SET @tSql +=' SUM(DT.FCXpdQty) AS FCXpdQty,SUM(ISNULL(Dis.FCXpdDis,0)) AS FCXpdDis,'
		 SET @tSql +=' SUM(CASE' 
			 SET @tSql +=' WHEN FTXphVATInOrEx = ''1'' THEN (DT.FCXpdNetAfHD -ISNULL(Dis.FCXpdDis,0)) - ISNULL(DT.FCXpdVat,0)'
			 SET @tSql +=' WHEN FTXphVATInOrEx = ''2'' THEN (DT.FCXpdNetAfHD -ISNULL(Dis.FCXpdDis,0))'
			 SET @tSql +=' END'
			SET @tSql +=' ) AS FCXpdValue,'
 		 SET @tSql +=' SUM(ISNULL(DT.FCXpdVat,0)) AS FCXpdVat,'
		 SET @tSql +=' SUM(CASE' 
			 SET @tSql +=' WHEN FTXphVATInOrEx = ''1'' THEN (DT.FCXpdNetAfHD)' 
			 SET @tSql +=' WHEN FTXphVATInOrEx = ''2'' THEN (DT.FCXpdNetAfHD + ISNULL(DT.FCXpdVat,0))'
			 SET @tSql +=' END'
			SET @tSql +=' ) AS FCXpdNetAmt'
 		 SET @tSql +=' FROM TAPTPiDT DT WITH(NOLOCK)' 
		 SET @tSql +=' INNER JOIN TAPTPiHD HD WITH(NOLOCK) ON DT.FTBchCode = HD.FTBchCode	AND DT.FTXphDocNo = HD.FTXphDocNo'
		 SET @tSql +=' LEFT JOIN TCNMBranch Bch	WITH(NOLOCK) ON HD.FTBchCode = Bch.FTBchCode'	 	
		 SET @tSql +=' LEFT JOIN TCNMPdt Pdt WITH(NOLOCK) ON  DT.FTPdtCode = Pdt.FTPdtCode'	
		 SET @tSql +=' LEFT JOIN TCNMPdt_L PdtL WITH(NOLOCK) ON  DT.FTPdtCode = PdtL.FTPdtCode	AND PdtL.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
		 SET @tSql +=' LEFT JOIN TCNMPdtUnit_L PunL WITH(NOLOCK) ON  DT.FTPunCode = PunL.FTPunCode	AND PunL.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
		 SET @tSql +=' LEFT JOIN TCNMSpl Spl WITH(NOLOCK) ON HD.FTSplCode = Spl.FTSplCode'
		 SET @tSql +=' LEFT JOIN TCNMSpl_L SplL WITH(NOLOCK) ON HD.FTSplCode = SplL.FTSplCode	AND SplL.FNLngID ='''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
		 --LEFT JOIN TCNMPdtSpcBch SpcBch  WITH(NOLOCK)  ON DT.FTPdtCode =  SpcBch.FTPdtCode'
		 SET @tSql +=' LEFT JOIN' 
	 			SET @tSql +=' ('
					SET @tSql +=' SELECT FTBchCode,FTXphDocNo,FNXpdSeqNo,' 
					SET @tSql +=' SUM(CASE' 
						SET @tSql +=' WHEN FTXpdDisChgType IN (''1'',''2'') THEN FCXpdValue *-1' 
						SET @tSql +=' WHEN FTXpdDisChgType IN (''3'',''4'') THEN FCXpdValue'
					SET @tSql +=' END) AS FCXpdDis'
					SET @tSql +=' FROM TAPTPiDTDis'
					SET @tSql +=' GROUP BY FTBchCode,FTXphDocNo,FNXpdSeqNo' 
				SET @tSql +=' ) Dis ON DT.FTBchCode = Dis.FTBchCode	 AND DT.FTXphDocNo	= Dis.FTXphDocNo	AND DT.FNXpdSeqNo	= Dis.FNXpdSeqNo'	
				SET @tSql += @tSql1
				--WHERE  DT.FTPdtCode = '01875'
		 SET @tSql +=' GROUP BY HD.FTSplCode,SplL.FTSplName,DT.FTPdtCode,PdtL.FTPdtName,DT.FTPunCode,PunL.FTPunName'

		 SET @tSql +=' UNION ALL'
		--CreditNote
		 SET @tSql +=' SELECT ''2'' AS FNXpdPurType, HD.FTSplCode,SplL.FTSplName,DT.FTPdtCode,ISNULL(PdtL.FTPdtName,'''') AS FTPdtName,DT.FTPunCode,ISNULL(PunL.FTPunName,'''') AS FTPunName,'
		 SET @tSql +=' SUM(DT.FCXpdQty) AS FCXpdQty,SUM(ISNULL(Dis.FCXpdDis,0)) AS FCXpdDis,'
		 SET @tSql +=' SUM(CASE' 
			 SET @tSql +=' WHEN FTXphVATInOrEx = ''1'' THEN (DT.FCXpdNetAfHD -ISNULL(Dis.FCXpdDis,0)) - ISNULL(DT.FCXpdVat,0)'
			 SET @tSql +=' WHEN FTXphVATInOrEx = ''2'' THEN (DT.FCXpdNetAfHD -ISNULL(Dis.FCXpdDis,0))'
			 SET @tSql +=' END'
			SET @tSql +=' ) AS FCXpdValue,'
		 SET @tSql +=' SUM(ISNULL(DT.FCXpdVat,0)) AS FCXpdVat,'
		 SET @tSql +=' SUM(CASE'
			 SET @tSql +=' WHEN FTXphVATInOrEx = ''1'' THEN (DT.FCXpdNetAfHD)' 
			 SET @tSql +=' WHEN FTXphVATInOrEx = ''2'' THEN (DT.FCXpdNetAfHD + ISNULL(DT.FCXpdVat,0))'
			 SET @tSql +=' END'
			SET @tSql +=' ) AS FCXpdNetAmt'

		 SET @tSql +=' FROM TAPTPcDT DT WITH(NOLOCK)' 
		 SET @tSql +=' INNER JOIN TAPTPcHD HD WITH(NOLOCK) ON DT.FTBchCode = HD.FTBchCode	AND DT.FTXphDocNo = HD.FTXphDocNo'	
		 SET @tSql +=' LEFT JOIN TCNMBranch Bch	WITH(NOLOCK) ON HD.FTBchCode = Bch.FTBchCode'
		 SET @tSql +=' LEFT JOIN TCNMPdt Pdt WITH(NOLOCK) ON  DT.FTPdtCode = Pdt.FTPdtCode'	
		 SET @tSql +=' LEFT JOIN TCNMPdt_L PdtL WITH(NOLOCK) ON  DT.FTPdtCode = PdtL.FTPdtCode	AND PdtL.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
		 SET @tSql +=' LEFT JOIN TCNMPdtUnit_L PunL WITH(NOLOCK) ON  DT.FTPunCode = PunL.FTPunCode	AND PunL.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
		 SET @tSql +=' LEFT JOIN TCNMSpl Spl WITH(NOLOCK) ON HD.FTSplCode = Spl.FTSplCode'	
		 SET @tSql +=' LEFT JOIN TCNMSpl_L SplL WITH(NOLOCK) ON HD.FTSplCode = SplL.FTSplCode	AND SplL.FNLngID ='''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
		 --LEFT JOIN TCNMPdtSpcBch SpcBch  WITH(NOLOCK)  ON DT.FTPdtCode =  SpcBch.FTPdtCode
		 SET @tSql +=' LEFT JOIN' 
				SET @tSql +=' ('
					SET @tSql +=' SELECT FTBchCode,FTXphDocNo,FNXpdSeqNo,' 
					SET @tSql +=' SUM(CASE' 
						SET @tSql +=' WHEN FTXpdDisChgType IN (''1'',''2'') THEN FCXpdValue *-1' 
						SET @tSql +=' WHEN FTXpdDisChgType IN (''3'',''4'') THEN FCXpdValue'
					SET @tSql +=' END) AS FCXpdDis'
					SET @tSql +=' FROM TAPTPcDTDis'
					SET @tSql +=' GROUP BY FTBchCode,FTXphDocNo,FNXpdSeqNo' 
				SET @tSql +=' ) Dis ON DT.FTBchCode = Dis.FTBchCode	 AND DT.FTXphDocNo = Dis.FTXphDocNo	AND DT.FNXpdSeqNo = Dis.FNXpdSeqNo'	
				SET @tSql += @tSql2
				--WHERE  DT.FTPdtCode = '01875'
		 SET @tSql +=' GROUP BY HD.FTSplCode,SplL.FTSplName,DT.FTPdtCode,PdtL.FTPdtName,DT.FTPunCode,PunL.FTPunName'
		SET @tSql +=' ) Pur'
		--WHERE FTPdtCode = '00145'
	SET @tSql +=' GROUP BY FTSplCode,FTSplName,Pur.FTPdtCode,FTPdtName,FTPunName'	
	--PRINT @tSql
	EXECUTE(@tSql)
	--'''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
	--RETURN SELECT * FROM TRPTSalDailyByCashierTmp WHERE FTComName = ''+ @nComName + '' AND FTRptCode = ''+ @tRptCode +'' AND FTUsrSession = '' + @tUsrSession + ''
END TRY

BEGIN CATCH 
	SET @FNResult= -1
	--PRINT @tSql
END CATCH	


/****** Object:  StoredProcedure [dbo].[SP_RPTxAnalysPurchase]    Script Date: 9/11/2565 15:48:19 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxAnalysPurchase]') AND type in (N'P', N'PC')) BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxAnalysPurchase] AS' 
END
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
ALTER PROCEDURE [dbo].[SP_RPTxAnalysPurchase]

	@ptAgnCode VARCHAR(30), 
	@ptUsrSession  VARCHAR(255),
	@ptLangID  VARCHAR(1),
	@ptBchCode   VARCHAR(4000),
	@ptDocDateFrm  VARCHAR(10),
	@ptDocDateTo   VARCHAR(10),
	@ptPdtCodeFrm    VARCHAR(30),
	@ptPdtCodeTo    VARCHAR(10),
	@ptRptGroup     VARCHAR(50)

AS
BEGIN TRY

DECLARE @tSQL  VARCHAR(MAX)
DECLARE @tSQLFilter  VARCHAR(4000)

DECLARE @tRptGrpTableName VARCHAR(100)
DECLARE @tRptGrpColPK VARCHAR(100)
DECLARE @tRptGrpColName VARCHAR(100)
DECLARE @tRptGrpAxis VARCHAR(100)
DECLARE @tRptSubGrp VARCHAR(200)

IF(@ptRptGroup = 'SPdtType' )
   BEGIN
		SET @tRptGrpTableName = 'TCNMPdtType_L'
		SET @tRptGrpAxis = 'PTY'
		SET @tRptGrpColPK = 'FTPtyCode'
		SET @tRptGrpColName = 'FTPtyName'
		SET @tRptSubGrp = 'PDT.FTPtyCode'
   END
ELSE IF (@ptRptGroup = 'SPdtChain' )
	BEGIN
		SET @tRptGrpTableName = 'TCNMPdtGrp_L'
		SET @tRptGrpAxis = 'GRP'
		SET @tRptGrpColPK = 'FTPgpChain'
		SET @tRptGrpColName = 'FTPgpChainName'
		SET @tRptSubGrp = 'PDT.FTPgpChain'
	END
ELSE IF (@ptRptGroup = 'SPdtBrand' )
	BEGIN
		SET @tRptGrpTableName = 'TCNMPdtBrand_L'
		SET @tRptGrpAxis = 'BND'
		SET @tRptGrpColPK = 'FTPbnCode'
		SET @tRptGrpColName = 'FTPbnName'
		SET @tRptSubGrp = 'PDT.FTPbnCode'
	END
ELSE IF (@ptRptGroup = 'SPdtModel' )
	BEGIN
		SET @tRptGrpTableName = 'TCNMPdtModel_L'
		SET @tRptGrpAxis = 'MOD'
		SET @tRptGrpColPK = 'FTPmoCode'
		SET @tRptGrpColName = 'FTPmoName'
		SET @tRptSubGrp = 'PDT.FTPmoCode'
	END
ELSE IF (@ptRptGroup = 'SPdtSpl' )
	BEGIN
		SET @tRptGrpTableName = 'TCNMSpl_L'
		SET @tRptGrpAxis = 'SPL'
		SET @tRptGrpColPK = 'FTSplCode'
		SET @tRptGrpColName = 'FTSplName'
		SET @tRptSubGrp = 'HD.FTSplCode'
	END
ELSE 
BEGIN
		SET @tRptGrpTableName = ''
		SET @tRptGrpAxis = ''
		SET @tRptGrpColPK = ''
		SET @tRptGrpColName = ''
		SET @tRptSubGrp = ''
END


--Filter 
SET @tSQLFilter = ''
IF(@ptAgnCode <> '' OR @ptAgnCode <> NULL)
	BEGIN
	   SET @tSQLFilter += ' AND HD.FTAgnCode = '''+@ptAgnCode+''''
	END

IF(@ptBchCode <> '' OR @ptBchCode <> NULL)
	BEGIN
	   SET @tSQLFilter += ' AND HD.FTBchCode IN ('+@ptBchCode+')'
	END

IF((@ptDocDateFrm <> '' OR @ptDocDateFrm <> NULL) AND (@ptDocDateTo <> '' OR @ptDocDateTo <> NULL))
	BEGIN
	   SET @tSQLFilter += ' AND CONVERT(VARCHAR(10),HD.FDXphDocDate,121) BETWEEN '''+@ptDocDateFrm+''' AND '''+@ptDocDateTo+'''' 
	END

IF((@ptPdtCodeFrm <> '' OR @ptPdtCodeFrm <> NULL) AND (@ptPdtCodeTo <> '' OR @ptPdtCodeTo <> NULL))
	BEGIN
	   SET @tSQLFilter += ' AND DT.FTPdtCode BETWEEN '''+@ptPdtCodeFrm+''' AND '''+@ptPdtCodeTo+'''' 
	END


DELETE FROM TRPTxAnalysPurchaseTmp WHERE FTUsrSession = ''+@ptUsrSession+''

SET @tSQL  = ''
    SET @tSQL  += ' INSERT INTO TRPTxAnalysPurchaseTmp '
	SET @tSQL  += ' SELECT ROW_NUMBER() OVER(ORDER BY PID.FTPdtCode ) AS FNXsdSeqNo , '+@tRptGrpAxis+'.'+@tRptGrpColPK+', '
		   SET @tSQL  += @tRptGrpAxis+'.'+@tRptGrpColName+' , '
		   SET @tSQL  += ' PID.FTPdtCode, '
		   SET @tSQL  += ' PID.FTXpdPdtName, '
		   SET @tSQL  += ' ROUND(PID.FCXpdAmtB4DisChg / FCXpdQtyAll, 4) AS FCXpdSetPrice, '
		   SET @tSQL  += ' PID.FCXpdQtyAll, '
		   SET @tSQL  += ' ROUND((PID.FCXpdQtyAll * 100) / SUMF.FCXpdQtyAllSUM, 4) AS FCXsdQtyAvgPct, '
		   SET @tSQL  += ' PID.FCXpdAmtB4DisChg, '
		   SET @tSQL  += ' ROUND((PID.FCXpdAmtB4DisChg * 100) / SUMF.FCXpdAmtB4DisChgSUM, 4) AS FCXsdAmtAvgPct, '
		   SET @tSQL  += ' ISNULL(PID.FCXpdValue,0) AS FCXpdDisChgVal ,'
		   SET @tSQL  += ' PID.FCXpdNetAfHD, '
		   SET @tSQL  += ' ROUND((PID.FCXpdNetAfHD * 100) / SUMF.FCXpdNetAfHDSUM, 4) AS FCXsdNetAvgPct ,'
		   SET @tSQL  +=  ''''+ @ptUsrSession + ''' AS FTUsrSession '
	SET @tSQL  += ' FROM '
	  SET @tSQL  += ' (SELECT 1 AS FTXKey, '
			  SET @tSQL  += @tRptSubGrp +', '
			  SET @tSQL  += ' DT.FTPdtCode, '
			  SET @tSQL  += ' DT.FTXpdPdtName, '
			  SET @tSQL  += ' SUM(DT.FCXpdQtyAll) AS FCXpdQtyAll, '
			  SET @tSQL  += ' SUM(DT.FCXpdAmtB4DisChg) AS FCXpdAmtB4DisChg, '
			  SET @tSQL  += ' SUM(DIS.FCXpdValue) AS FCXpdValue, '
			  SET @tSQL  += ' SUM(DT.FCXpdNetAfHD) AS FCXpdNetAfHD '
	   SET @tSQL  += ' FROM TAPTPiDT DT '
	   SET @tSQL  += ' INNER JOIN TAPTPiHD HD ON DT.FTBchCode = HD.FTBchCode '
	   SET @tSQL  += ' AND DT.FTXphDocNo = HD.FTXphDocNo '

	   IF(@ptRptGroup <> '' AND @ptRptGroup <> 'SPdtSpl')
	      BEGIN
		     SET @tSQL  += ' INNER JOIN TCNMPdt PDT ON DT.FTPdtCode = PDT.FTPdtCode '
		  END
       
	   SET @tSQL  += ' LEFT JOIN (
	   SELECT  FTBchCode,FTXphDocNo ,  FNXpdSeqNo,SUM(FCXpdValue) AS FCXpdValue 
	   FROM TAPTPiDTDis 
	   GROUP BY FTBchCode,FTXphDocNo ,  FNXpdSeqNo ) DIS ON DT.FTBchCode = DIS.FTBchCode AND DT.FTXphDocNo = DIS.FTXphDocNo AND DT.FNXpdSeqNo = DIS.FNXpdSeqNo ' 

	   SET @tSQL  += ' WHERE HD.FTXphStaDoc = ''1'' '
		 SET @tSQL  += ' AND FTXphStaApv = ''1'' '

		 --Report Filter
		 SET @tSQL  += @tSQLFilter

	   SET @tSQL  += ' GROUP BY '+@tRptSubGrp+', '
				SET @tSQL  += ' DT.FTPdtCode, '
				SET @tSQL  += ' DT.FTXpdPdtName) PID '
	SET @tSQL  += ' INNER JOIN '
	  SET @tSQL  += ' (SELECT 1 AS FTXKey, '
			  SET @tSQL  += ' SUM(FCXpdQtyAll) AS FCXpdQtyAllSUM, '
			  SET @tSQL  += ' SUM(FCXpdAmtB4DisChg) AS FCXpdAmtB4DisChgSUM, '
			  SET @tSQL  += ' SUM(FCXpdNetAfHD) AS FCXpdNetAfHDSUM '
	   SET @tSQL  += ' FROM TAPTPiDT DT '
	   SET @tSQL  += ' INNER JOIN TAPTPiHD HD ON DT.FTBchCode = HD.FTBchCode '
	   SET @tSQL  += ' AND DT.FTXphDocNo = HD.FTXphDocNo '
	   SET @tSQL  += ' WHERE HD.FTXphStaDoc = ''1'' '
		 SET @tSQL  += ' AND FTXphStaApv = ''1'' '

		 --Report Filter
		 SET @tSQL  += @tSQLFilter

	   SET @tSQL  += ' ) SUMF ON PID.FTXKey = SUMF.FTXKey '
 
	 -- Join By Report Group
	 SET @tSQL  += ' LEFT JOIN '+ @tRptGrpTableName +' ' + @tRptGrpAxis + ' ON PID.'+@tRptGrpColPK+' = '+@tRptGrpAxis+'.'+@tRptGrpColPK+' AND '+@tRptGrpAxis+'.FNLngID = ' + @ptLangID

	 SET @tSQL  += ' ORDER BY PID.'+@tRptGrpColPK+'  , PID.FTPdtCode	'

	 --PRINT @tSql
	 EXECUTE(@tSql)

	 return 1

END  TRY

BEGIN CATCH 
    return -1
END CATCH


/****** Object:  StoredProcedure [dbo].[SP_RPTxPremRedem]    Script Date: 16/11/2565 11:43:17 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxPremRedem]') AND type in (N'P', N'PC')) BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxPremRedem] AS' 
END
GO

ALTER PROCEDURE [dbo].[SP_RPTxPremRedem] 
--ALTER PROCEDURE [dbo].[SP_RPTxDailySaleByPdt1001002] 

	@pnLngID int , 
	@pnComName Varchar(100),
	@ptRptCode Varchar(100),
	@ptUsrSession Varchar(255),
	@pnFilterType int, --1 BETWEEN 2 IN

	--สาขา
	@ptBchL Varchar(8000), --กรณี Condition IN

	--Agency
	@ptAgnL Varchar(8000), --กรณี Condition IN
	--@ptMerF Varchar(10),
	--@ptMerT Varchar(10),
	--Shop Code
	--@ptShpL Varchar(8000), --กรณี Condition IN
	--@ptShpF Varchar(10),
	--@ptShpT Varchar(10),
	----เครื่องจุดขาย
	--@ptPosL Varchar(8000), --กรณี Condition IN
	--@ptPosF Varchar(20),
	--@ptPosT Varchar(20),

	@ptPdtCodeF Varchar(20),
	@ptPdtCodeT Varchar(20),
	--@ptPdtChanF Varchar(30),
	--@ptPdtChanT Varchar(30),
	--@ptPdtTypeF Varchar(5),
	--@ptPdtTypeT Varchar(5),

	----NUI 22-09-05 RQ2208-020
	--@ptPbnF Varchar(5),
	--@ptPbnT Varchar(5),
	----ลูกค้า
	@ptCstF Varchar(20),
	@ptCstT Varchar(20),

	@ptDocDateF Varchar(10),
	@ptDocDateT Varchar(10),

	@FNResult INT OUTPUT 
AS
--------------------------------------
-- Watcharakorn 
-- Create 01/11/2022
-- Temp name  TRPTPremRedemTmp
-- @pnLngID ภาษา
-- @ptRptCdoe ชื่อรายงาน
-- @ptUsrSession UsrSession
-- @@ptBchL รหัสสาขา
-- @ptPdtCodeF จากสินค้า
-- @ptPdtCodeT ถึงสินค้า

-- @ptCstF จากลูกค้า
-- @ptCstT ถึงลูกค้า

-- @ptDocDateF จากวันที่
-- @ptDocDateT ถึงวันที่
-- @FNResult


--------------------------------------
BEGIN TRY

	DECLARE @nLngID int 
	DECLARE @nComName Varchar(100)
	DECLARE @tRptCode Varchar(100)
	DECLARE @tUsrSession Varchar(255)
	DECLARE @tSql VARCHAR(8000)
	DECLARE @tSqlIns VARCHAR(8000)
	DECLARE @tSql1 nVARCHAR(Max)
	DECLARE @tSql2 VARCHAR(8000)

	--Branch Code
	DECLARE @tBchF Varchar(5)
	DECLARE @tBchT Varchar(5)
	----Merchant
	--DECLARE @tMerF Varchar(10)
	--DECLARE @tMerT Varchar(10)
	----Shop Code
	--DECLARE @tShpF Varchar(10)
	--DECLARE @tShpT Varchar(10)
	----Pos Code
	--DECLARE @tPosF Varchar(20)
	--DECLARE @tPosT Varchar(20)

	DECLARE @tPdtCodeF Varchar(20)
	DECLARE @tPdtCodeT Varchar(20)
	--DECLARE @tPdtChanF Varchar(30)
	--DECLARE @tPdtChanT Varchar(30)
	--DECLARE @tPdtTypeF Varchar(5)
	--DECLARE @tPdtTypeT Varchar(5)

	--DECLARE @tPbnF Varchar(5)
	--DECLARE @tPbnT Varchar(5)

	DECLARE @tDocDateF Varchar(10)
	DECLARE @tDocDateT Varchar(10)
	--ลูกค้า
	DECLARE @tCstF Varchar(20)
	DECLARE @tCstT Varchar(20)


	
	SET @nLngID = @pnLngID
	SET @nComName = @pnComName
	SET @tUsrSession = @ptUsrSession
	SET @tRptCode = @ptRptCode

	----Branch
	--SET @tBchF  = @ptBchF
	--SET @tBchT  = @ptBchT
	----Merchant
	--SET @tMerF  = @ptMerF
	--SET @tMerT  = @ptMerT
	----Shop
	--SET @tShpF  = @ptShpF
	--SET @tShpT  = @ptShpT
	----Pos
	--SET @tPosF  = @ptPosF 
	--SET @tPosT  = @ptPosT

	SET @tPdtCodeF  = @ptPdtCodeF 
	SET @tPdtCodeT = @ptPdtCodeT
	--SET @tPdtChanF = @ptPdtChanF
	--SET @tPdtChanT = @ptPdtChanT 
	--SET @tPdtTypeF = @ptPdtTypeF
	--SET @tPdtTypeT = @ptPdtTypeT

	--SET @tPbnF = @ptPbnF
	--SET @tPbnT = @ptPbnT


	SET @tDocDateF = @ptDocDateF
	SET @tDocDateT = @ptDocDateT
	SET @FNResult= 0

	SET @tDocDateF = CONVERT(VARCHAR(10),@tDocDateF,121)
	SET @tDocDateT = CONVERT(VARCHAR(10),@tDocDateT,121)

	IF @nLngID = null
	BEGIN
		SET @nLngID = 1
	END	
	--Set ค่าให้ Paraleter กรณี T เป็นค่าว่างหรือ null


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

	--IF @ptMerL =null
	--BEGIN
	--	SET @ptMerL = ''
	--END

	--IF @tMerF =null
	--BEGIN
	--	SET @tMerF = ''
	--END
	--IF @tMerT =null OR @tMerT = ''
	--BEGIN
	--	SET @tMerT = @tMerF
	--END 

	--IF @ptShpL =null
	--BEGIN
	--	SET @ptShpL = ''
	--END

	--IF @tShpF =null
	--BEGIN
	--	SET @tShpF = ''
	--END
	--IF @tShpT =null OR @tShpT = ''
	--BEGIN
	--	SET @tShpT = @tShpF
	--END

	--IF @ptPosL =null
	--BEGIN
	--	SET @ptPosL = ''
	--END

	--IF @tPosF = null
	--BEGIN
	--	SET @tPosF = ''
	--END
	--IF @tPosT = null OR @tPosT = ''
	--BEGIN
	--	SET @tPosT = @tPosF
	--END

	IF @tPdtCodeF = null
	BEGIN
		SET @tPdtCodeF = ''
	END 
	IF @tPdtCodeT = null OR @tPdtCodeT =''
	BEGIN
		SET @tPdtCodeT = @tPdtCodeF
	END 

	--IF @tPdtChanF = null
	--BEGIN
	--	SET @tPdtChanF = ''
	--END 
	--IF @tPdtChanT = null OR @tPdtChanT =''
	--BEGIN
	--	SET @tPdtChanT = @tPdtChanF
	--END 

	--IF @tPdtTypeF = null
	--BEGIN
	--	SET @tPdtTypeF = ''
	--END 
	--IF @tPdtTypeT = null OR @tPdtTypeT =''
	--BEGIN
	--	SET @tPdtTypeT = @tPdtTypeF
	--END 

	--IF @tPbnF = null
	--BEGIN
	--	SET @tPbnF = ''
	--END 
	--IF @tPbnT = null OR @tPbnT =''
	--BEGIN
	--	SET @tPbnT = @tPbnF
	--END 

	IF @tDocDateF = null
	BEGIN 
		SET @tDocDateF = ''
	END

	IF @tDocDateT = null OR @tDocDateT =''
	BEGIN 
		SET @tDocDateT = @tDocDateF
	END

	SET @tSql1 =   ' WHERE HD.FTXshStaDoc =  ''1'' AND HD.FTXshStaApv =  ''1'' AND FNXshDocType = ''2'''

	--IF @pnFilterType = '1'
	--BEGIN
	--	IF (@tBchF <> '' AND @tBchT <> '')
	--	BEGIN
	--		SET @tSql1 +=' AND DT.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
	--	END

	--	IF (@tMerF <> '' AND @tMerT <> '')
	--	BEGIN
	--		SET @tSql1 +=' AND Shp.FTMerCode BETWEEN ''' + @tMerF + ''' AND ''' + @tMerT + ''''
	--	END

	--	IF (@tShpF <> '' AND @tShpT <> '')
	--	BEGIN
	--		SET @tSql1 +=' AND HD.FTShpCode BETWEEN ''' + @tShpF + ''' AND ''' + @tShpT + ''''
	--	END

	--	IF (@tPosF <> '' AND @tPosT <> '')
	--	BEGIN
	--		SET @tSql1 += ' AND HD.FTPosCode BETWEEN ''' + @tPosF + ''' AND ''' + @tPosT + ''''
	--	END		
	--END

	IF @pnFilterType = '2'
	BEGIN
		IF (@ptBchL <> '' )
		BEGIN
			SET @tSql1 +=' AND DT.FTBchCode IN (' + @ptBchL + ')'
		END

		IF (@ptAgnL <> '' )
		BEGIN
			SET @tSql1 +=' AND Bch.FTAgnCode IN (' + @ptAgnL + ')'
		END

	--	IF (@ptShpL <> '')
	--	BEGIN
	--		SET @tSql1 +=' AND HD.FTShpCode IN (' + @ptShpL + ')'
	--	END

	--	IF (@ptPosL <> '')
	--	BEGIN
	--		SET @tSql1 += ' AND HD.FTPosCode IN (' + @ptPosL + ')'
	--	END		
	END

	IF (@tPdtCodeF <> '' AND @tPdtCodeT <> '')
	BEGIN
		SET @tSql1 +=' AND DT.FTPdtCode BETWEEN ''' + @tPdtCodeF + ''' AND ''' + @tPdtCodeT + ''''
	END

	IF (@ptCstF <> '' AND @ptCstT <> '')
	BEGIN
		SET @tSql1 +=' AND HD.FTCstCode BETWEEN ''' + @ptCstF + ''' AND ''' + @ptCstT + ''''
	END

	--IF (@tPdtChanF <> '' AND @tPdtChanT <> '')
	--BEGIN
	--	SET @tSql1 +=' AND Pdt.FTPgpChain BETWEEN ''' + @tPdtChanF + ''' AND ''' + @tPdtChanT + ''''
	--END

	--IF (@tPdtTypeF <> '' AND @tPdtTypeT <> '')
	--BEGIN
	--	SET @tSql1 +=' AND Pdt.FTPtyCode BETWEEN ''' + @tPdtTypeF + ''' AND ''' + @tPdtTypeT + ''''
	--END

	--IF (@tPbnF <> '' AND @tPbnT <> '')
	--BEGIN
	--	SET @tSql1 +=' AND Pdt.FTPbnCode BETWEEN ''' + @tPbnF + ''' AND ''' + @tPbnT + ''''
	--END

	IF (@tDocDateF <> '' AND @tDocDateT <> '')
	BEGIN
		SET @tSql1 +=' AND CONVERT(VARCHAR(10),FDXshDocDate,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''''
	END

	DELETE FROM TRPTPremRedemTmp WITH (ROWLOCK) WHERE FTComName =  '' + @nComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''--ลบข้อมูล Temp ของเครื่องที่จะบันทึกขอมูลลง Temp
 --Sale
  	SET @tSql  = ' INSERT INTO TRPTPremRedemTmp '
	SET @tSql +=' (FTComName,FTRptCode,FTUsrSession,'
	SET @tSql +=' FTBchCode,FTBchName,FDXshDocDate,FTXshDocNo,FTCstCode,FTCstName,FTCarRegNo,FTPdtCode,FTPdtName,FCXsdQtyAll,FTXshRefExt'
	SET @tSql +=' )'
	SET @tSql +=' SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'	
	SET @tSql +=' HD.FTBchCode ,BL.FTBchName,HD.FDXshDocDate,HD.FTXshDocNo,HD.FTCstCode,CstL.FTCstName,Car.FTCarRegNo,DT.FTPdtCode,'
	SET @tSql +=' DT.FTXsdPdtName,DT.FCXsdQtyAll,RefExt.FTXshRefDocNo AS FTXshRefExt'
	SET @tSql +=' FROM TSVTSalTwoHD HD'
	SET @tSql +=' INNER JOIN TSVTSalTwoDT DT ON HD.FTBchCode = DT.FTBchCode AND HD.FTXshDocNo = DT.FTXshDocNo'
	SET @tSql +=' LEFT JOIN TCNMBranch Bch ON HD.FTBchCode = Bch.FTBchCode'
	SET @tSql +=' LEFT JOIN TCNMBranch_L BL ON HD.FTBchCode = BL.FTBchCode AND BL.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
	SET @tSql +=' LEFT JOIN TCNMCst_L CstL ON HD.FTCstCode = CstL.FTCstCode AND CstL.FNLngID ='''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
	SET @tSql +=' LEFT JOIN' 
	  SET @tSql +=' (SELECT FTBchCode, FTXshDocNo,FTXshRefDocNo FROM' 
	   SET @tSql +=' TSVTSalTwoHDDocRef  WHERE  FTXshRefKey = ''RefExt'') AS RefExt'
	   SET @tSql +=' ON  HD.FTXshDocNo = RefExt.FTXshDocNo AND HD.FTBchCode = RefExt.FTBchCode'
	SET @tSql +=' LEFT JOIN' 
	  SET @tSql +=' (SELECT  FTBchCode,FTXshDocNo,FTXshRefDocNo FROM' 
	   SET @tSql +=' TSVTSalTwoHDDocRef  WHERE  FTXshRefKey = ''Job2Ord'') AS Job2Ord'
	   SET @tSql +=' ON  HD.FTXshDocNo = RefExt.FTXshDocNo AND HD.FTBchCode = RefExt.FTBchCode'
	--SET @tSql +=' LEFT JOIN TSVTJob2OrdHDCst JCst ON Job2Ord.FTXshRefDocNo = JCst.FTXshDocNo'
	SET @tSql +=' LEFT JOIN TSVMCar Car On HD.FTCstCode = Car.FTCarOwner'
	SET @tSql +=  @tSql1
	PRINT @tSql 
	EXECUTE(@tSql)

END TRY
BEGIN CATCH 
	SET @FNResult= -1
END CATCH	


/******Script Stored 08.01.01******/

/****** Object:  StoredProcedure [dbo].[SP_RPTxDebtorReceive]    Script Date: 29/11/2565 18:01:16 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,>
-- Midify By : NUI
-- Modify date: <29/11/2022 13:31,>
-- Description:	<Description,รายงาน-การรับชำระลูกหนี้,>
-- Version: 01.00.00
-- Temp Name: TRPTDebtorReceiveTmp
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxDebtorReceive]') AND type in (N'P', N'PC'))
BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxDebtorReceive] AS' 
END
GO
ALTER PROCEDURE [dbo].[SP_RPTxDebtorReceive]
--ALTER PROCEDURE [dbo].[SP_RPTxDebtorReceive]

	@pnLngID int , 
	@pnComName Varchar(100),
	@ptRptCode Varchar(100),
	@ptUsrSession Varchar(255),
	@pnFilterType int, --1 BETWEEN 2 IN

--Agency
	@ptAgnL Varchar(8000), --Agency Condition IN 
--สาขา
	@ptBchL Varchar(8000), --สาขา Condition IN
--Shop
	@ptShpL Varchar(8000), 
--Purchase
--	@ptStaApv Varchar(1),--FTXphStaApv สถานะ อนุมัติ เอกสาร ว่าง:ยังไม่ทำ, 1:อนุมัติแล้ว
--	                     --การส่งค่า 0 : ไม่ Condition 1 : ยังไม่อนุมัติ 2 : อนุมัติแล้ว            
	@ptStaPaid Varchar(1),-- FTXphStaPaid สถานะ รับ/จ่ายเงิน 1:ยังไม่จ่าย 2:บางส่วน, 3:ครบ
--Supplier
	@ptCstCodeFrm Varchar(20), @ptCstCodeTo Varchar(20),-- FTSplCode รหัสผู้จำหน่าย
	@ptUsrF Varchar(5),@ptUsrT Varchar(5),-- FTSgpCode กลุ่มผู้จำหน่าย
	@ptStyF Varchar(5),@ptStyT Varchar(5),--FTStyCode ประเภทผู้จำหน่าย	

	@ptDocDateF Varchar(10), @ptDocDateT Varchar(10), 
	
	@FNResult INT OUTPUT 
AS
--------------------------------------
--  
-- Create 19/10/2021
--รายงานงชำระ
-- Temp name  

--------------------------------------
BEGIN TRY	
	DECLARE @nLngID int 
	DECLARE @nComName Varchar(100)
	DECLARE @tRptCode Varchar(100)
	DECLARE @tUsrSession Varchar(255)
	DECLARE @tSql VARCHAR(8000)
	DECLARE @tSql1 VARCHAR(8000)

	--Branch Code
	DECLARE @tBchF Varchar(5)
	DECLARE @tBchT Varchar(5)
	--Cashier
	DECLARE @tUsrF Varchar(10)
	DECLARE @tUsrT Varchar(10)
	--Pos Code
	DECLARE @tPosF Varchar(20)
	DECLARE @tPosT Varchar(20)

	DECLARE @tDocDateF Varchar(10)
	DECLARE @tDocDateT Varchar(10)

	SET @nLngID = @pnLngID
	SET @nComName = @pnComName
	SET @tUsrSession = @ptUsrSession
	SET @tRptCode = @ptRptCode

	--Branch
	
	SET @FNResult= 0

	SET @ptDocDateF = CONVERT(VARCHAR(10),@ptDocDateF,121)
	SET @ptDocDateT = CONVERT(VARCHAR(10),@ptDocDateT,121)

	IF @nLngID = null
	BEGIN
		SET @nLngID = 1
	END	

	IF @ptAgnL = null
	BEGIN
		SET @ptAgnL = ''
	END

	IF @ptBchL = null
	BEGIN
		SET @ptBchL = ''
	END

	IF @ptShpL = null
	BEGIN
		SET @ptShpL = ''
	END

	--Purchase
	--IF @ptStaApv = NULL ----การส่งค่า 0 : ไม่ Condition 1 : ยังไม่อนุมัติ 2 : อนุมัติแล้ว   FTXphStaApv สถานะ อนุมัติ เอกสาร ว่าง:ยังไม่ทำ, 1:อนุมัติแล้ว
	--BEGIN
	--	SET @ptStaApv = ''
	--END

	IF @ptStaPaid = NULL --FTXphStaPaid สถานะ รับ/จ่ายเงิน 1:ยังไม่จ่าย 2:บางส่วน, 3:ครบ
	BEGIN
		SET @ptStaPaid = ''
	END

	IF @ptCstCodeFrm =NULL -- FTSplCode รหัสผู้จำหน่าย
	BEGIN
		SET @ptCstCodeFrm = ''
	END
	IF @ptCstCodeTo =null OR @ptCstCodeTo = ''
	BEGIN
		SET @ptCstCodeTo = @ptCstCodeFrm
	END 

	IF @ptUsrF =NULL -- FTSgpCode กลุ่มผู้จำหน่าย
	BEGIN
		SET @ptUsrF = ''
	END
	IF @ptUsrT =null OR @ptUsrT = ''
	BEGIN
		SET @ptUsrT = @ptUsrF
	END 

	IF @ptStyF =NULL --FTStyCode ประเภทผู้จำหน่าย
	BEGIN
		SET @ptStyF = ''
	END
	
	IF @ptDocDateF = null
	BEGIN 
		SET @ptDocDateF = ''
	END

	IF @ptDocDateT = null OR @ptDocDateT =''
	BEGIN 
		SET @ptDocDateT = @ptDocDateF
	END
	
		
	--SET @tSql1 =   ' WHERE ISNULL(HD.FTXphStaPaid,'''') <> ''3'' AND HD.FTXphStaDoc = ''1'' '

	SET @tSql1 =   ' WHERE ISNULL(FTXshStaDoc, '''') = ''1'' '
	--Center
	IF (@ptAgnL <> '' )
	BEGIN
		SET @tSql1 +=' AND Bch.FTAgnCode IN (' + @ptAgnL + ')' --Agency
	END


	IF (@ptBchL <> '' )
	BEGIN
		SET @tSql1 +=' AND HD.FTBchCode IN (' + @ptBchL + ')' --Branch
	END


	IF (@ptShpL <> '' )
	BEGIN
		SET @tSql1 +=' AND HD.FTShpCode IN (' + @ptShpL + ')' --Shop
	END
	
	--Purchase
	--IF (@ptStaApv<> '0') --การส่งค่า 0 : ไม่ Condition 1 : ยังไม่อนุมัติ 2 : อนุมัติแล้ว  FTXphStaApv สถานะ อนุมัติ เอกสาร ว่าง:ยังไม่ทำ, 1:อนุมัติแล้ว
	--BEGIN
	--	SET @tSql1 +=' AND CASE WHEN FTXphStaApv = ''1'' THEN ''2'' ELSE ''1'' END  = ''' + @ptStaApv + '''' 
	--END

	--IF (@ptStaPaid<> '') -- FTXphStaPaid สถานะ รับ/จ่ายเงิน 1:ยังไม่จ่าย 2:บางส่วน, 3:ครบ
	--BEGIN
	--	SET @tSql1 +=' AND ISNULL(HD.FTXphStaPaid,''1'') = ''' + @ptStaPaid + '''' 
	--END

	--Supplier
	IF (@ptCstCodeFrm<> '') -- FTSplCode รหัสผู้จำหน่าย
	BEGIN
		SET @tSql1 +=' AND HD.FTCstCode BETWEEN ''' + @ptCstCodeFrm + ''' AND ''' + @ptCstCodeTo + '''' 
	END

	IF (@ptUsrF<> '') -- FTSgpCode กลุ่มผู้จำหน่าย
	BEGIN
		SET @tSql1 +=' AND HD.FTUsrCode BETWEEN ''' + @ptUsrF + ''' AND ''' + @ptUsrT + '''' 
	END

	
	IF (@ptDocDateF <> '' AND @ptDocDateT <> '')
	BEGIN
    	SET @tSql1 +=' AND CONVERT(VARCHAR(10),HD.FDXshDocDate,121) BETWEEN ''' + @ptDocDateF + ''' AND ''' + @ptDocDateT + ''''	
	END
	--PRINT @tSql1
	--PRINT '99999'
	DELETE FROM TRPTDebtorReceiveTmp  WHERE FTUsrSession = '' + @tUsrSession + ''--Åº¢éÍÁÙÅ Temp ¢Í§à¤Ã×èÍ§·Õè¨ÐºÑ¹·Ö¡¢ÍÁÙÅÅ§ Temp
	--Purchase
	SET @tSql = 'INSERT INTO TRPTDebtorReceiveTmp'
	--PRINT @tSql
	SET @tSql +=' (FTUsrSession,'
	SET @tSql +=' FTBchCode,FTBchName,FTCstCode,FTCstName,FTXphDocNo,FDXshDocDate,FTXsdInvNo,FTXsdDocType,'
	SET @tSql +=' FCXsdInvGrand,FCXsdInvPaid,FCXsdInvPay,FCXsdInvRem,FTUsrCode,FTUsrName,FTXshRefDocDate'
	SET @tSql +=' )'
	SET @tSql +=' SELECT '''+ @tUsrSession +''' AS FTUsrSession,'
	SET @tSql +=' HD.FTBchCode,
       BCH.FTBchName,
       HD.FTCstCode,
       CSTL.FTCstName, 
       HD.FTXshDocNo, 
       HD.FDXshDocDate, 
       DT.FTXsdInvNo,
       CASE
           WHEN FNXsdInvType = 1
           THEN ''ใบขาย''
           WHEN FNXsdInvType = 2
           THEN ''ใบมัดจำ''
           WHEN FNXsdInvType = 3
           THEN ''ใบลดหนี้''
           WHEN FNXsdInvType = 4
           THEN ''ใบเพิ่มหนี้''
           ELSE ''ไม่ระบุ''
       END AS FTXsdDocType, 
       DT.FCXsdInvGrand,
       DT.FCXsdInvPaid,
       DT.FCXsdInvPay,
       ISNULL(DT.FCXsdInvRem,0)-ISNULL(DT.FCXsdInvPay,0)  AS FCXsdInvRem ,
       HD.FTUsrCode,
			 USRL.FTUsrName,
			 DT.FDXsdInvDate '

SET @tSql +=' FROM TARTSpDT DT WITH(NOLOCK) '
    SET @tSql +=' INNER JOIN TARTSpHD HD WITH(NOLOCK) ON DT.FTBchCode = HD.FTBchCode AND DT.FTXshDocNo = HD.FTXshDocNo '
		SET @tSql +=' LEFT JOIN TCNMCst_L CSTL ON HD.FTCstCode = CSTL.FTCstCode AND CSTL.FNLngID = 1 '
		SET @tSql +=' LEFT JOIN TCNMBranch_L BCH ON HD.FTBchCode = BCH.FTBchCode AND  BCH.FNLngID = 1 '
		SET @tSql +=' LEFT JOIN TCNMUser_L USRL WITH(NOLOCK) ON HD.FTUsrCode = USRL.FTUsrCode '

SET @tSql += @tSql1
SET @tSql += ' ORDER BY HD.FTCstCode '
--SET @tSql +=' AND HD.FTXphStaDoc = 1 AND HD.FTXphStaApv = 1 '

	--PRINT @tSql
	EXECUTE(@tSql)

END TRY

BEGIN CATCH 
	SET @FNResult= -1
	--PRINT @tSql
END CATCH
GO


/****** Object:  StoredProcedure [dbo].[SP_RPTxAccruedReceivable]    Script Date: 29/11/2565 18:08:14 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Midify By : Nui
-- Midify Date : 29/11/2022
-- Varsion : 02.00.00
-- Temp Name : TRPTAccruedReceiTmp
-- Description:	<รายงาน-ติดตามยอดหนี้คงค้าง,,>
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxAccruedReceivable]') AND type in (N'P', N'PC'))
BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxAccruedReceivable] AS' 
END
GO
ALTER PROCEDURE [dbo].[SP_RPTxAccruedReceivable]
	@ptAgnCode VARCHAR(20),
	  @ptSessionID VARCHAR(100),
	  @ptBchCode VARCHAR(500),
	  @ptCstCodeFrm VARCHAR(20),
	  @ptCstCodeTo VARCHAR(20),
	  @pdDocDateFrm VARCHAR(10),
	  @pdDocDateTo VARCHAR(10),
	  @pnLangID INT
AS
BEGIN TRY


        DECLARE @tSQL VARCHAR(MAX)
		SET @tSQL = ''

		DECLARE @tSQLFilter VARCHAR(255)
		SET @tSQLFilter = ''

		--IF (@ptAgnCode <> '' OR @ptAgnCode <> NULL)
		--	BEGIN
		--		 SET @tSQLFilter += ' AND ISNULL(JOB.FTAgnCode,'''') = ''' + @ptAgnCode + ''' '
		--	END

		IF (@ptBchCode <> '' OR @ptBchCode <> NULL)
			BEGIN
				 SET @tSQLFilter += ' AND ISNULL(HD.FTBchCode,'''') IN ('+@ptBchCode+')'
			END


		IF ((@ptCstCodeFrm <> '' OR @ptCstCodeFrm <> NULL) AND  (@ptCstCodeTo <> '' OR @ptCstCodeTo <> NULL))
			BEGIN
					SET @tSQLFilter += ' AND ISNULL(HD.FTCstCode,'''') BETWEEN ''' + @ptCstCodeFrm + ''' AND ''' + @ptCstCodeTo + ''' '
			END

		IF ((@pdDocDateFrm <> '' OR @pdDocDateFrm <> NULL) AND  (@pdDocDateTo <> '' OR @pdDocDateTo <> NULL))
			BEGIN
					SET @tSQLFilter += ' AND CONVERT(VARCHAR(10),ISNULL(HD.FDXshDocDate,''''),121) BETWEEN  ''' + @pdDocDateFrm + ''' AND ''' + @pdDocDateTo + ''' '
			END


		DELETE FROM TRPTAccruedReceiTmp WITH (ROWLOCK) WHERE FTUsrSession =  '' + @ptSessionID + ''

		SET @tSQL += ' INSERT INTO TRPTAccruedReceiTmp '
		SET @tSQL += ' SELECT HD.FTCstCode, 
			   CSTL.FTCstName, 
			   HD.FDXshDocDate, 
			   HD.FTXshDocNo, 
			   HD.FTUsrCode, 
			   USRL.FTUsrName, 
			   CASE WHEN FNXshDocType = ''1'' THEN ISNULL(HD.FCXshGrand,0) ELSE ISNULL(FCXshGrand,0)*-1 END AS FCXshGrand, 
			   CASE WHEN FNXshDocType = ''1'' THEN ISNULL(SPDT.FCXsdInvPay,ISNULL(HD.FCXshPaid,0)) ELSE (ISNULL(SPDT.FCXsdInvPay,ISNULL(HD.FCXshPaid,0)))*-1 END AS FCXshPaid, 			   
			   CASE WHEN FNXshDocType = ''1'' THEN ISNULL(SPDT.FCXsdInvRem,ISNULL(HD.FCXshLeft,0))  ELSE (ISNULL(SPDT.FCXsdInvRem,ISNULL(HD.FCXshLeft,0)) )*-1 END AS FCXshLeft,'

		SET @tSQL += ' '''+@ptSessionID+''' AS FTUsrSession '

		SET @tSQL += ' FROM TPSTSalHD HD WITH (NOLOCK) ' 
		SET @tSQL += ' LEFT JOIN' 
			SET @tSQL += ' (SELECT FTXsdInvNo , FCXsdInvGrand,'
			SET @tSQL += ' SUM(ISNULL(FCXsdInvPaid,0)) AS FCXsdInvPaid,'
			SET @tSQL += ' SUM(ISNULL(FCXsdInvPay,0)) AS FCXsdInvPay,'
			SET @tSQL += ' ISNULL(FCXsdInvGrand,0)-SUM(ISNULL(FCXsdInvPay,0))  AS FCXsdInvRem ' 
			 SET @tSQL += ' FROM TARTSpDT WITH (NOLOCK)' 
			 SET @tSQL += ' WHERE FNXsdInvType IN (1,3)'
			 SET @tSQL += ' GROUP BY FTXsdInvNo ,   FCXsdInvGrand'
			SET @tSQL += ' ) SPDT ON HD.FTXshDocNo = SPDT.FTXsdInvNo'
		SET @tSQL += ' LEFT JOIN TCNMCst_L CSTL WITH (NOLOCK) ON HD.FTCstCode = CSTL.FTCstCode AND CSTL.FNLngID = ' + CAST(@pnLangID AS varchar(1)) 
		SET @tSQL += ' LEFT JOIN TCNMUser_L USRL WITH (NOLOCK) ON HD.FTUsrCode = USRL.FTUsrCode AND USRL.FNLngID = ' + CAST(@pnLangID AS varchar(1))  
		SET @tSQL += ' WHERE HD.FTXshStaDoc = 1 AND ISNULL(SPDT.FCXsdInvRem,ISNULL(HD.FCXshLeft,0)) > 0 '

		SET @tSQL += @tSQLFilter

		--PRINT(@tSQL)
		EXEC(@tSQL)
		

	return 0

END TRY
BEGIN CATCH
    return -1
END CATCH
GO


/****** Object:  StoredProcedure [dbo].[SP_RPTxAd]    Script Date: 30/11/2565 14:46:13 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Nui>
-- Create date: <Create Date,28/11/2022,>
-- Description:	<Description,รายงาน-ลูกหนี้ค้างชำระ,>
-- Version: 01.00.00
-- Temp Name: TRPTAdTmp
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxAd]') AND type in (N'P', N'PC'))
BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxAd] AS' 
END
GO
ALTER PROCEDURE [dbo].[SP_RPTxAd]
	@pnLngID INT , 
	@ptComName VARCHAR(100),
	@ptRptCode VARCHAR(100),
	@ptUsrSession VARCHAR(255),

	--@pnFilterType int, --1 BETWEEN 2 IN
	@ptAgnCode VARCHAR(20),
	--@ptSessionID VARCHAR(100),
	@ptBchCode VARCHAR(500),
	@ptCstCodeFrm VARCHAR(20),
	@ptCstCodeTo VARCHAR(20),
	@pdDocDateFrm VARCHAR(10),
	@pdDocDateTo VARCHAR(10),
	@FNResult INT OUTPUT	
AS
BEGIN TRY
    DECLARE @tSQL VARCHAR(8000)
	DECLARE @tSQLCon VARCHAR(255)
	DECLARE @nLngID INT
	DECLARE @tComName VARCHAR(100)
	DECLARE @tRptCode VARCHAR(100)
	DECLARE @tUsrSession VARCHAR(255)

	SET @tSQL = ''
	SET @tSQLCon = ''
	SET @tComName = ''
	SET @tRptCode = ''
	SET @tUsrSession = ''

	SET @nLngID = @pnLngID
	SET @tComName = @ptComName
	SET @tRptCode = @ptRptCode
	SET @tUsrSession = @ptUsrSession

	--IF (@ptAgnCode <> '' OR @ptAgnCode <> NULL)
	--	BEGIN
	--		 SET @tSQLCon += ' AND ISNULL(JOB.FTAgnCode,'''') = ''' + @ptAgnCode + ''' '
	--	END

	IF (@ptBchCode <> '' OR @ptBchCode <> NULL)
		BEGIN
				SET @tSQLCon += ' AND ISNULL(HD.FTBchCode,'''') IN ('+@ptBchCode+')'
		END


	IF ((@ptCstCodeFrm <> '' OR @ptCstCodeFrm <> NULL) AND  (@ptCstCodeTo <> '' OR @ptCstCodeTo <> NULL))
		BEGIN
				SET @tSQLCon += ' AND ISNULL(HD.FTCstCode,'''') BETWEEN ''' + @ptCstCodeFrm + ''' AND ''' + @ptCstCodeTo + ''' '
		END

	IF ((@pdDocDateFrm <> '' OR @pdDocDateFrm <> NULL) AND  (@pdDocDateTo <> '' OR @pdDocDateTo <> NULL))
		BEGIN
				SET @tSQLCon += ' AND CONVERT(VARCHAR(10),HD.FDXshDocDate,121) BETWEEN  ''' + @pdDocDateFrm + ''' AND ''' + @pdDocDateTo + ''' '
		END


	--DELETE FROM TRPTAdTmp WITH (ROWLOCK) WHERE FTUsrSession =  '' + @ptSessionID + ''
	DELETE FROM TRPTAdTmp WITH (ROWLOCK) WHERE FTComName = '' + @tComName + '' AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''

	SET @tSQL = ' INSERT INTO TRPTAdTmp '
	SET @tSQL += ' (FTComName,FTRptCode,FTUsrSession,'
	SET @tSQL += ' FTBchCode,FTBchName,FTCstCode,FTCstName,FCXshGrand,FCXshPaid,FCXshLeft'
	SET @tSQL += ' )'
	SET @tSQL += ' SELECT '''+@tComName+''' AS FTComName,'''+@tRptCode+''' AS FTRptCode,'''+@tUsrSession+''' AS FTUsrSession,'
	SET @tSQL += ' HD.FTBchCode,BchL.FTBchName,'
	SET @tSQL += ' ISNULL(HD.FTCstCode,'''') AS FTCstCode,'
	SET @tSQL += ' ISNULL(CSTL.FTCstName,'''') AS FTCstName,' 
	SET @tSQL += ' SUM(CASE WHEN FNXshDocType = ''1'' THEN ISNULL(HD.FCXshGrand,0) ELSE ISNULL(FCXshGrand,0)*-1 END) AS FCXshGrand,' 
	SET @tSQL += ' SUM(CASE WHEN FNXshDocType = ''1'' THEN ISNULL(SPDT.FCXsdInvPay,ISNULL(HD.FCXshPaid,0)) ELSE (ISNULL(SPDT.FCXsdInvPay,ISNULL(HD.FCXshPaid,0)))*-1 END) AS FCXshPaid,' 
	SET @tSQL += ' SUM(CASE WHEN FNXshDocType = ''1'' THEN ISNULL(SPDT.FCXsdInvRem,ISNULL(HD.FCXshLeft,0))  ELSE (ISNULL(SPDT.FCXsdInvRem,ISNULL(HD.FCXshLeft,0)) )*-1 END) AS FCXshLeft' 

	SET @tSQL += ' FROM TPSTSalHD HD WITH (NOLOCK)' 
	SET @tSQL += ' LEFT JOIN TCNMCst_L CSTL WITH (NOLOCK) ON HD.FTCstCode = CSTL.FTCstCode AND CSTL.FNLngID = ' + CAST(@pnLngID AS varchar(1)) 
	SET @tSQL += ' LEFT JOIN TCNMUser_L USRL WITH (NOLOCK) ON HD.FTUsrCode = USRL.FTUsrCode AND USRL.FNLngID = ' + CAST(@pnLngID AS varchar(1))  
	SET @tSQL += ' LEFT JOIN TCNMBranch Bch WITH (NOLOCK) ON HD.FTBchCode = Bch.FTBchCode' 
	SET @tSQL += ' LEFT JOIN TCNMBranch_L BchL WITH (NOLOCK) ON Bch.FTBchCode = BchL.FTBchCode  AND BchL.FNLngID = ' + CAST(@pnLngID AS varchar(1))  
		SET @tSQL += ' LEFT JOIN' 
			SET @tSQL += ' (SELECT FTXsdInvNo , FCXsdInvGrand,'
			SET @tSQL += ' SUM(ISNULL(FCXsdInvPaid,0)) AS FCXsdInvPaid,'
			SET @tSQL += ' SUM(ISNULL(FCXsdInvPay,0)) AS FCXsdInvPay,'
			SET @tSQL += ' ISNULL(FCXsdInvGrand,0)-SUM(ISNULL(FCXsdInvPay,0))  AS FCXsdInvRem ' 
			 SET @tSQL += ' FROM TARTSpDT WITH (NOLOCK)' 
			 SET @tSQL += ' WHERE FNXsdInvType IN (1,3)'
			 SET @tSQL += ' GROUP BY FTXsdInvNo ,   FCXsdInvGrand'
			SET @tSQL += ' ) SPDT ON HD.FTXshDocNo = SPDT.FTXsdInvNo'
	SET @tSQL += ' WHERE HD.FTXshStaDoc = ''1'' AND ISNULL(HD.FCXshLeft, 0) > 0 '
    SET @tSQL += @tSQLCon

	SET @tSQL += ' GROUP BY HD.FTBchCode,BchL.FTBchName,HD.FTCstCode,CSTL.FTCstName'			   
	SET @tSQL += ' HAVING SUM(CASE WHEN FNXshDocType = ''1'' THEN ISNULL(HD.FCXshLeft,0) ELSE ISNULL(HD.FCXshLeft,0)*-1 END) >0'

	PRINT(@tSQL)
	EXEC(@tSQL)
		
	return 0

END TRY
BEGIN CATCH
    return -1
END CATCH
GO


-- Start Script Upgrade Store 30-11-2022 --

/****** Object:  StoredProcedure [dbo].[SP_RPTxPSSVat1001006]    Script Date: 11/30/2022 5:50:23 PM ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[SP_RPTxPSSVat1001006]') AND type in (N'P', N'PC'))
	DROP PROCEDURE [dbo].[SP_RPTxPSSVat1001006]
GO 

/****** Object:  StoredProcedure [dbo].[SP_RPTxPSSVat1001006]    Script Date: 11/30/2022 5:50:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[SP_RPTxPSSVat1001006] 

--ALTER PROCEDURE [dbo].[SP_RPTxPSSVat1001006] 
	@pnLngID int , 
	@pnComName Varchar(100),
	@ptRptCode Varchar(100),
	@ptUsrSession Varchar(255),
	@pnFilterType int, --1 BETWEEN 2 IN
	--สาขา
	@ptBchL Varchar(8000), --กรณี Condition IN
	@ptBchF Varchar(5),
	@ptBchT Varchar(5),
	--Merchant
	@ptMerL Varchar(8000), --กรณี Condition IN
	@ptMerF Varchar(10),
	@ptMerT Varchar(10),
	--Shop Code
	@ptShpL Varchar(8000), --กรณี Condition IN
	@ptShpF Varchar(10),
	@ptShpT Varchar(10),
	--เครื่องจุดขาย
	@ptPosL Varchar(8000), --กรณี Condition IN
	@ptPosF Varchar(20),
	@ptPosT Varchar(20),
	--@ptBchF Varchar(5),
	--@ptBchT Varchar(5),
	----เครื่องจุดขาย
	--@ptPosCodeF Varchar(20),
	--@ptPosCodeT Varchar(20),

	@ptDocDateF Varchar(10),
	@ptDocDateT Varchar(10),
	----ลูกค้า
	--@ptCstF Varchar(20),
	--@ptCstT Varchar(20),
	@FNResult INT OUTPUT 
AS
--------------------------------------
-- Watcharakorn 
-- Create 10/07/2019
-- Temp name  TRPTSalRCTmp
-- @pnLngID ภาษา
-- @ptRptCdoe ชื่อรายงาน
-- @ptUsrSession UsrSession
-- @ptBchF จากรหัสสาขา
-- @ptBchT ถึงรหัสสาขา
	--DECLARE @tPosCodeF Varchar(30)
	--DECLARE @tPosCodeT Varchar(30)
-- @ptDocDateF จากวันที่
-- @ptDocDateT ถึงวันที่
-- @FNResult


--------------------------------------
BEGIN TRY

	DECLARE @nLngID int 
	DECLARE @nComName Varchar(100)
	DECLARE @tRptCode Varchar(100)
	DECLARE @tUsrSession Varchar(255)
	DECLARE @tSql VARCHAR(8000)
	DECLARE @tSqlIns VARCHAR(8000)
	DECLARE @tSql1 nVARCHAR(Max)
	DECLARE @tSql2 VARCHAR(8000)
	DECLARE @tSql3 VARCHAR(8000)

	--DECLARE @tBchF Varchar(5)
	--DECLARE @tBchT Varchar(5)

	--DECLARE @tPosCodeF Varchar(20)
	--DECLARE @tPosCodeT Varchar(20)

	--Branch Code
	DECLARE @tBchF Varchar(5)
	DECLARE @tBchT Varchar(5)
	--Merchant
	DECLARE @tMerF Varchar(10)
	DECLARE @tMerT Varchar(10)
	--Shop Code
	DECLARE @tShpF Varchar(10)
	DECLARE @tShpT Varchar(10)
	--Pos Code
	DECLARE @tPosF Varchar(20)
	DECLARE @tPosT Varchar(20)

	DECLARE @tDocDateF Varchar(10)
	DECLARE @tDocDateT Varchar(10)
	--ลูกค้า
	--DECLARE @tCstF Varchar(20)
	--DECLARE @tCstT Varchar(20)


	--SET @nLngID = 1
	--SET @nComName = 'Ada062'
	--SET @tRptName = 'PSSVat1001006'
	--SET @ptUsrSession = '001'
	--SET @tBchF = '001'
	--SET @tBchT = '001'

	--SET @tDocDateF = '2019-07-01'
	--SET @tDocDateT = '2019-07-10'


	--SET @nLngID = 1
	--SET @nComName = 'Ada062'
	--SET @tRptName = 'DailySaleByInv1001001'
	--SET @ptUsrSession = '001'
	--SET @tBchF = ''
	--SET @tBchT = ''

	--SET @tDocDateF = ''
	--SET @tDocDateT = ''

	SET @nLngID = @pnLngID
	SET @nComName = @pnComName
	SET @tUsrSession = @ptUsrSession
	SET @tRptCode = @ptRptCode

	--SET @tBchF = @ptBchF
	--SET @tBchT = @ptBchT

	--SET @tPosCodeF  = @ptPosCodeF 
	--SET @tPosCodeT = @ptPosCodeT 

	--Branch
	SET @tBchF  = @ptBchF
	SET @tBchT  = @ptBchT
	--Merchant
	SET @tMerF  = @ptMerF
	SET @tMerT  = @ptMerT
	--Shop
	SET @tShpF  = @ptShpF
	SET @tShpT  = @ptShpT
	--Pos
	SET @tPosF  = @ptPosF 
	SET @tPosT  = @ptPosT

	SET @tDocDateF = @ptDocDateF
	SET @tDocDateT = @ptDocDateT

	SET @FNResult= 0

	SET @tDocDateF = CONVERT(VARCHAR(10),@tDocDateF,121)
	SET @tDocDateT = CONVERT(VARCHAR(10),@tDocDateT,121)

	IF @nLngID = null
	BEGIN
		SET @nLngID = 1
	END	
	--Set ค่าให้ Paraleter กรณี T เป็นค่าว่างหรือ null


	--IF @tBchF = null
	--BEGIN
	--	SET @tBchF = ''
	--END
	--IF @tBchT = null OR @tBchT = ''
	--BEGIN
	--	SET @tBchT = @tBchF
	--END

	--IF @tPosCodeF = null
	--BEGIN
	--	SET @tPosCodeF = ''
	--END

	--IF @tPosCodeT = null OR @tPosCodeT = ''
	--BEGIN
	--	SET @tPosCodeT = @tPosCodeF
	--END

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

	IF @ptMerL =null
	BEGIN
		SET @ptMerL = ''
	END

	IF @tMerF =null
	BEGIN
		SET @tMerF = ''
	END
	IF @tMerT =null OR @tMerT = ''
	BEGIN
		SET @tMerT = @tMerF
	END 

	IF @ptShpL =null
	BEGIN
		SET @ptShpL = ''
	END

	IF @tShpF =null
	BEGIN
		SET @tShpF = ''
	END
	IF @tShpT =null OR @tShpT = ''
	BEGIN
		SET @tShpT = @tShpF
	END

	IF @ptPosL =null
	BEGIN
		SET @ptPosL = ''
	END

	IF @tPosF = null
	BEGIN
		SET @tPosF = ''
	END
	IF @tPosT = null OR @tPosT = ''
	BEGIN
		SET @tPosT = @tPosF
	END

	IF @tDocDateF = null
	BEGIN 
		SET @tDocDateF = ''
	END

	IF @tDocDateT = null OR @tDocDateT =''
	BEGIN 
		SET @tDocDateT = @tDocDateF
	END

	--SET @tSqlSal =  ' WHERE 1=1 AND FTXshStaDoc = ''1'''
	--SET @tSqlVD =   ' WHERE 1=1 AND FTXshStaDoc = ''1'' AND Rcv.FTFmtCode <> ''004'''


	--IF (@tBchF <> '' AND @tBchT <> '')
	--BEGIN
	--	SET @tSql1 +=' AND FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
	--END

	--IF (@tPosCodeF <> '' AND @tPosCodeT <> '')
	--	BEGIN
	--		SET @tSql1 += ' AND HD.FTPosCode BETWEEN '''+@tPosCodeF+''' AND '''+@tPosCodeT+''''
	--	END		

	SET @tSql1 = ' '
	SET @tSql3 = ' '
	IF @pnFilterType = '1'
	BEGIN
		IF (@tBchF <> '' AND @tBchT <> '')
		BEGIN
			SET @tSql1 +=' AND HD.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
		END

		IF (@tMerF <> '' AND @tMerT <> '')
		BEGIN
			SET @tSql1 +=' AND Shp.FTMerCode BETWEEN ''' + @tMerF + ''' AND ''' + @tMerT + ''''
		END

		IF (@tShpF <> '' AND @tShpT <> '')
		BEGIN
			SET @tSql1 +=' AND HD.FTShpCode BETWEEN ''' + @tShpF + ''' AND ''' + @tShpT + ''''
		END

		IF (@tPosF <> '' AND @tPosT <> '')
		BEGIN
			SET @tSql1 += ' AND HD.FTPosCode BETWEEN ''' + @tPosF + ''' AND ''' + @tPosT + ''''
		END		
	END

	IF @pnFilterType = '2'
	BEGIN
		IF (@ptBchL <> '' )
		BEGIN
			SET @tSql1 +=' AND HD.FTBchCode IN (' + @ptBchL + ')'
		END

		IF (@ptMerL <> '' )
		BEGIN
			SET @tSql1 +=' AND Shp.FTMerCode IN (' + @ptMerL + ')'
		END

		IF (@ptShpL <> '')
		BEGIN
			SET @tSql1 +=' AND HD.FTShpCode IN (' + @ptShpL + ')'
		END

		IF (@ptPosL <> '')
		BEGIN
			SET @tSql1 += ' AND HD.FTPosCode IN (' + @ptPosL + ')'
		END		
	END

	IF (@tDocDateF <> '' AND @tDocDateT <> '')
	BEGIN
		SET @tSql1 +=' AND CONVERT(VARCHAR(10),FDXshDocDate,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''''
	END

	DELETE FROM TRPTPSTaxHDTmp WITH (ROWLOCK) WHERE FTComName =  '' + @nComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''--ลบข้อมูล Temp ของเครื่องที่จะบันทึกขอมูลลง Temp
 
 	SET @tSql  = ' INSERT INTO TRPTPSTaxHDTmp '
	SET @tSql +=' ('
	SET @tSql +=' FTComName,FTRptCode,FTUsrSession,'
	SET @tSql +=' FTBchCode,FTBchName,FDXshdocDate,FTXshDocNo,FTPosCode,FTXshDocRef,FTCstCode,FTCstName,FTCstTaxNo,FCXshAmt,FCXshVat,FCXshAmtNV,FCXshGrandTotal,'
	--*NUI 2019-11-14
	SET @tSql +=' FNAppType,FTPosRegNo,FTCstBchCode,FTCstBusiness,FTEstablishment,FTXshTaxID'
	-------------
	SET @tSql +=' )'
	SET @tSql +=' SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'	
	SET @tSql +=' SalVat.FTBchCode,FTBchName,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,FTXshDocNo,FTPosCode,FTXshRefInt,FTCstCode,FTCstName,FTCstTaxNo,FCXshValue,FCXshVat,FCXshAmtNV,FCXshGrand,'
	--*NUI 2019-11-14
	SET @tSql +=' FNAppType,FTPosRegNo,FTCstBchCode,FTCstBusiness,FTEstablishment,FTCstTaxNo AS FTXshTaxID'
	SET @tSql +=' FROM'	
			SET @tSql +=' (SELECT HD.FTBchCode,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,HD.FTPosCode,FTXshDocNo,ISNULL(FTXshRefInt,'''') FTXshRefInt,HD.FTCstCode,Cst_L.FTCstName,Cst.FTCstTaxNo,'
			--NUI10-04-2020
			SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshVatable,0)-ISNULL(FCXshAmtNV,0) ELSE (ISNULL(FCXshVatable,0)-ISNULL(FCXshAmtNV,0))*-1 END AS FCXshValue,'
			SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshVat,0) ELSE ISNULL(FCXshVat,0)*-1 END AS FCXshVat,'
			SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshAmtNV,0) ELSE ISNULL(FCXshAmtNV,0)*-1 END AS FCXshAmtNV,'
			--NUI10-04-2020
			SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0) ELSE (ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0))*-1 END AS FCXshGrand,'
			--*NUI 2019-11-14
			SET @tSql +=' 1 AS FNAppType,POS.FTPosRegNo,Cst.FTCstBchCode,Cst.FTCstBusiness,'
			SET @tSql +=' CASE WHEN ISNULL(Cst.FTCstBusiness,'''') <> ''1'' THEN '''' ELSE CASE WHEN FTCstBchHQ = ''2'' THEN ''2''  ELSE ''1'' END  END AS FTEstablishment'
			SET @tSql +=' FROM TPSTSalHD HD LEFT JOIN'
		 			  SET @tSql +=' TCNMCst Cst ON HD.FTCstCode = Cst.FTCstCode LEFT JOIN'
					  SET @tSql +=' TCNMPOS POS ON HD.FTPosCode = Pos.FTPosCode AND HD.FTBchCode = Pos.FTBchCode LEFT JOIN'
					  SET @tSql +=' TCNMCst_L Cst_L ON HD.FTCstCode = Cst_L.FTCstCode AND Cst_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' '
					  SET @tSql +=' LEFT JOIN TCNMShop Shp WITH (NOLOCK) ON HD.FTBchCode = Shp.FTBchCode AND HD.FTShpCode = Shp.FTShpCode '
					  SET @tSql +=' WHERE 1=1 AND FTXshStaDoc = ''1'''			  			
			SET @tSql += @tSql1			
			SET @tSql +=' ) SalVat LEFT JOIN '    
	SET @tSql +=' TCNMBranch_L Bch_L ON SalVat.FTBchCode = Bch_L.FTBchCode AND Bch_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' '
		
	SET @tsql3 += ' UNION'

	SET @tSql3 +=' SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'	
	SET @tSql3 +=' SalVat.FTBchCode,FTBchName,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,FTXshDocNo,FTPosCode,FTXshRefInt,FTCstCode,FTCstName,FTCstTaxNo,FCXshValue,FCXshVat,FCXshAmtNV,FCXshGrand,'
	--*NUI 2019-11-14
	SET @tSql3 +=' FNAppType,FTPosRegNo,FTCstBchCode,FTCstBusiness,FTEstablishment,FTCstTaxNo AS FTXshTaxID'
	SET @tSql3 +=' FROM'	
			SET @tSql3 +=' (SELECT HD.FTBchCode,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,HD.FTPosCode,HD.FTXshDocNo,ISNULL(FTXshRefInt,'''') FTXshRefInt,HD.FTCstCode,Cst_L.FTCstName,Cst.FTCstTaxNo,'
			--NUI10-04-2020
			--SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshVatable,0) ELSE ISNULL(FCXshVatable,0)*-1 END AS FCXshValue,'
			SET @tSql3 +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshVatable,0)-ISNULL(FCXshAmtNV,0) ELSE (ISNULL(FCXshVatable,0)-ISNULL(FCXshAmtNV,0))*-1 END AS FCXshValue,'
			SET @tSql3 +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshVat,0) ELSE ISNULL(FCXshVat,0)*-1 END AS FCXshVat,'
			SET @tSql3 +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshAmtNV,0) ELSE ISNULL(FCXshAmtNV,0)*-1 END AS FCXshAmtNV,'
			--NUI10-04-2020
			--SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshGrand,0) ELSE ISNULL(FCXshGrand,0)*-1 END AS FCXshGrand,'
			--SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0) ELSE (ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0))*-1 END AS FCXshGrand,'
			SET @tSql3 +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0) ELSE (ISNULL(FCXshGrand,0))*-1 END AS FCXshGrand,'
			--*NUI 2019-11-14
			SET @tSql3 +=' 2 AS FNAppType,POS.FTPosRegNo,Cst.FTCstBchCode,Cst.FTCstBusiness,'
			SET @tSql3 +=' CASE WHEN ISNULL(Cst.FTCstBusiness,'''') <> ''1'' THEN '''' ELSE CASE WHEN FTCstBchHQ = ''2'' THEN ''2''  ELSE ''1'' END  END AS FTEstablishment'
			SET @tSql3 +=' FROM TVDTSalHD HD' 
			--NUI 2020-01-06
			SET @tSql3 +=' INNER JOIN TVDTSalRC RC WITH(NOLOCK) ON HD.FTBchCode = RC.FTBchCode AND HD.FTXshDocNo = RC.FTXshDocNo'
			SET @tSql3 +=' LEFT JOIN TFNMRcv Rcv WITH(NOLOCK) ON  RC.FTRcvCode = Rcv.FTRcvCode'			
			------------
		 			  SET @tSql3 +=' LEFT JOIN TCNMCst Cst ON HD.FTCstCode = Cst.FTCstCode LEFT JOIN'
					  SET @tSql3 +=' TCNMPOS POS ON HD.FTPosCode = Pos.FTPosCode AND HD.FTBchCode = Pos.FTBchCode LEFT JOIN'
					  SET @tSql3 +=' TCNMCst_L Cst_L ON HD.FTCstCode = Cst_L.FTCstCode AND Cst_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' '
					  SET @tSql3 +=' LEFT JOIN TCNMShop Shp WITH (NOLOCK) ON HD.FTBchCode = Shp.FTBchCode AND HD.FTShpCode = Shp.FTShpCode '
					  SET @tSql3 +=' WHERE 1=1 AND FTXshStaDoc = ''1'' AND Rcv.FTFmtCode <> ''004'''
			SET @tSql3 +=  @tSql1
			SET @tSql3 +=' ) SalVat LEFT JOIN '    
	SET @tSql3 +=' TCNMBranch_L Bch_L ON SalVat.FTBchCode = Bch_L.FTBchCode AND Bch_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''					 

	--PRINT @tSql
	EXECUTE(@tSql + @tSql3)

	RETURN SELECT * FROM TRPTPSTaxHDTmp WHERE FTComName = ''+ @nComName + '' AND FTRptCode = ''+ @tRptCode +'' AND FTUsrSession = '' + @tUsrSession + ''
	
END TRY

BEGIN CATCH 
	SET @FNResult= -1
END CATCH	

--SELECT * FROM TRPTPSTaxHDTmp

GO


-- Stop Script Upgrade Store 30-11-2022 --


--- Script Stored 08.01.02 [01/12/2022] ---


/****** Object:  StoredProcedure [dbo].[SP_RPTxPremRedem]    Script Date: 1/12/2565 17:41:25 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxPremRedem]') AND type in (N'P', N'PC'))
BEGIN
EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxPremRedem] AS' 
END
GO

ALTER PROCEDURE [dbo].[SP_RPTxPremRedem] 
--ALTER PROCEDURE [dbo].[SP_RPTxDailySaleByPdt1001002] 

	@pnLngID int , 
	@pnComName Varchar(100),
	@ptRptCode Varchar(100),
	@ptUsrSession Varchar(255),
	@pnFilterType int, --1 BETWEEN 2 IN

	--สาขา
	@ptBchL Varchar(8000), --กรณี Condition IN

	--Agency
	@ptAgnL Varchar(8000), --กรณี Condition IN
	--@ptMerF Varchar(10),
	--@ptMerT Varchar(10),
	--Shop Code
	--@ptShpL Varchar(8000), --กรณี Condition IN
	--@ptShpF Varchar(10),
	--@ptShpT Varchar(10),
	----เครื่องจุดขาย
	--@ptPosL Varchar(8000), --กรณี Condition IN
	--@ptPosF Varchar(20),
	--@ptPosT Varchar(20),

	@ptPdtCodeF Varchar(20),
	@ptPdtCodeT Varchar(20),
	--@ptPdtChanF Varchar(30),
	--@ptPdtChanT Varchar(30),
	--@ptPdtTypeF Varchar(5),
	--@ptPdtTypeT Varchar(5),

	----NUI 22-09-05 RQ2208-020
	--@ptPbnF Varchar(5),
	--@ptPbnT Varchar(5),
	----ลูกค้า
	@ptCstF Varchar(20),
	@ptCstT Varchar(20),

	@ptDocDateF Varchar(10),
	@ptDocDateT Varchar(10),

	@FNResult INT OUTPUT 
AS
--------------------------------------
-- Watcharakorn 
-- Create 01/11/2022
-- Temp name  TRPTPremRedemTmp
-- @pnLngID ภาษา
-- @ptRptCdoe ชื่อรายงาน
-- @ptUsrSession UsrSession
-- @@ptBchL รหัสสาขา
-- @ptPdtCodeF จากสินค้า
-- @ptPdtCodeT ถึงสินค้า

-- @ptCstF จากลูกค้า
-- @ptCstT ถึงลูกค้า

-- @ptDocDateF จากวันที่
-- @ptDocDateT ถึงวันที่
-- @FNResult


--------------------------------------
BEGIN TRY

	DECLARE @nLngID int 
	DECLARE @nComName Varchar(100)
	DECLARE @tRptCode Varchar(100)
	DECLARE @tUsrSession Varchar(255)
	DECLARE @tSql VARCHAR(8000)
	DECLARE @tSqlIns VARCHAR(8000)
	DECLARE @tSql1 nVARCHAR(Max)
	DECLARE @tSql2 VARCHAR(8000)

	--Branch Code
	DECLARE @tBchF Varchar(5)
	DECLARE @tBchT Varchar(5)
	----Merchant
	--DECLARE @tMerF Varchar(10)
	--DECLARE @tMerT Varchar(10)
	----Shop Code
	--DECLARE @tShpF Varchar(10)
	--DECLARE @tShpT Varchar(10)
	----Pos Code
	--DECLARE @tPosF Varchar(20)
	--DECLARE @tPosT Varchar(20)

	DECLARE @tPdtCodeF Varchar(20)
	DECLARE @tPdtCodeT Varchar(20)
	--DECLARE @tPdtChanF Varchar(30)
	--DECLARE @tPdtChanT Varchar(30)
	--DECLARE @tPdtTypeF Varchar(5)
	--DECLARE @tPdtTypeT Varchar(5)

	--DECLARE @tPbnF Varchar(5)
	--DECLARE @tPbnT Varchar(5)

	DECLARE @tDocDateF Varchar(10)
	DECLARE @tDocDateT Varchar(10)
	--ลูกค้า
	DECLARE @tCstF Varchar(20)
	DECLARE @tCstT Varchar(20)


	
	SET @nLngID = @pnLngID
	SET @nComName = @pnComName
	SET @tUsrSession = @ptUsrSession
	SET @tRptCode = @ptRptCode

	----Branch
	--SET @tBchF  = @ptBchF
	--SET @tBchT  = @ptBchT
	----Merchant
	--SET @tMerF  = @ptMerF
	--SET @tMerT  = @ptMerT
	----Shop
	--SET @tShpF  = @ptShpF
	--SET @tShpT  = @ptShpT
	----Pos
	--SET @tPosF  = @ptPosF 
	--SET @tPosT  = @ptPosT

	SET @tPdtCodeF  = @ptPdtCodeF 
	SET @tPdtCodeT = @ptPdtCodeT
	--SET @tPdtChanF = @ptPdtChanF
	--SET @tPdtChanT = @ptPdtChanT 
	--SET @tPdtTypeF = @ptPdtTypeF
	--SET @tPdtTypeT = @ptPdtTypeT

	--SET @tPbnF = @ptPbnF
	--SET @tPbnT = @ptPbnT


	SET @tDocDateF = @ptDocDateF
	SET @tDocDateT = @ptDocDateT
	SET @FNResult= 0

	SET @tDocDateF = CONVERT(VARCHAR(10),@tDocDateF,121)
	SET @tDocDateT = CONVERT(VARCHAR(10),@tDocDateT,121)

	IF @nLngID = null
	BEGIN
		SET @nLngID = 1
	END	
	--Set ค่าให้ Paraleter กรณี T เป็นค่าว่างหรือ null


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

	--IF @ptMerL =null
	--BEGIN
	--	SET @ptMerL = ''
	--END

	--IF @tMerF =null
	--BEGIN
	--	SET @tMerF = ''
	--END
	--IF @tMerT =null OR @tMerT = ''
	--BEGIN
	--	SET @tMerT = @tMerF
	--END 

	--IF @ptShpL =null
	--BEGIN
	--	SET @ptShpL = ''
	--END

	--IF @tShpF =null
	--BEGIN
	--	SET @tShpF = ''
	--END
	--IF @tShpT =null OR @tShpT = ''
	--BEGIN
	--	SET @tShpT = @tShpF
	--END

	--IF @ptPosL =null
	--BEGIN
	--	SET @ptPosL = ''
	--END

	--IF @tPosF = null
	--BEGIN
	--	SET @tPosF = ''
	--END
	--IF @tPosT = null OR @tPosT = ''
	--BEGIN
	--	SET @tPosT = @tPosF
	--END

	IF @tPdtCodeF = null
	BEGIN
		SET @tPdtCodeF = ''
	END 
	IF @tPdtCodeT = null OR @tPdtCodeT =''
	BEGIN
		SET @tPdtCodeT = @tPdtCodeF
	END 

	--IF @tPdtChanF = null
	--BEGIN
	--	SET @tPdtChanF = ''
	--END 
	--IF @tPdtChanT = null OR @tPdtChanT =''
	--BEGIN
	--	SET @tPdtChanT = @tPdtChanF
	--END 

	--IF @tPdtTypeF = null
	--BEGIN
	--	SET @tPdtTypeF = ''
	--END 
	--IF @tPdtTypeT = null OR @tPdtTypeT =''
	--BEGIN
	--	SET @tPdtTypeT = @tPdtTypeF
	--END 

	--IF @tPbnF = null
	--BEGIN
	--	SET @tPbnF = ''
	--END 
	--IF @tPbnT = null OR @tPbnT =''
	--BEGIN
	--	SET @tPbnT = @tPbnF
	--END 

	IF @tDocDateF = null
	BEGIN 
		SET @tDocDateF = ''
	END

	IF @tDocDateT = null OR @tDocDateT =''
	BEGIN 
		SET @tDocDateT = @tDocDateF
	END

	SET @tSql1 =   ' WHERE HD.FTXshStaDoc =  ''1'' AND HD.FTXshStaApv =  ''1'' AND FNXshDocType = ''2'''

	--IF @pnFilterType = '1'
	--BEGIN
	--	IF (@tBchF <> '' AND @tBchT <> '')
	--	BEGIN
	--		SET @tSql1 +=' AND DT.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
	--	END

	--	IF (@tMerF <> '' AND @tMerT <> '')
	--	BEGIN
	--		SET @tSql1 +=' AND Shp.FTMerCode BETWEEN ''' + @tMerF + ''' AND ''' + @tMerT + ''''
	--	END

	--	IF (@tShpF <> '' AND @tShpT <> '')
	--	BEGIN
	--		SET @tSql1 +=' AND HD.FTShpCode BETWEEN ''' + @tShpF + ''' AND ''' + @tShpT + ''''
	--	END

	--	IF (@tPosF <> '' AND @tPosT <> '')
	--	BEGIN
	--		SET @tSql1 += ' AND HD.FTPosCode BETWEEN ''' + @tPosF + ''' AND ''' + @tPosT + ''''
	--	END		
	--END

	IF @pnFilterType = '2'
	BEGIN
		IF (@ptBchL <> '' )
		BEGIN
			SET @tSql1 +=' AND DT.FTBchCode IN (' + @ptBchL + ')'
		END

		IF (@ptAgnL <> '' )
		BEGIN
			SET @tSql1 +=' AND Bch.FTAgnCode IN (' + @ptAgnL + ')'
		END

	--	IF (@ptShpL <> '')
	--	BEGIN
	--		SET @tSql1 +=' AND HD.FTShpCode IN (' + @ptShpL + ')'
	--	END

	--	IF (@ptPosL <> '')
	--	BEGIN
	--		SET @tSql1 += ' AND HD.FTPosCode IN (' + @ptPosL + ')'
	--	END		
	END

	IF (@tPdtCodeF <> '' AND @tPdtCodeT <> '')
	BEGIN
		SET @tSql1 +=' AND DT.FTPdtCode BETWEEN ''' + @tPdtCodeF + ''' AND ''' + @tPdtCodeT + ''''
	END

	IF (@ptCstF <> '' AND @ptCstT <> '')
	BEGIN
		SET @tSql1 +=' AND HD.FTCstCode BETWEEN ''' + @ptCstF + ''' AND ''' + @ptCstT + ''''
	END

	--IF (@tPdtChanF <> '' AND @tPdtChanT <> '')
	--BEGIN
	--	SET @tSql1 +=' AND Pdt.FTPgpChain BETWEEN ''' + @tPdtChanF + ''' AND ''' + @tPdtChanT + ''''
	--END

	--IF (@tPdtTypeF <> '' AND @tPdtTypeT <> '')
	--BEGIN
	--	SET @tSql1 +=' AND Pdt.FTPtyCode BETWEEN ''' + @tPdtTypeF + ''' AND ''' + @tPdtTypeT + ''''
	--END

	--IF (@tPbnF <> '' AND @tPbnT <> '')
	--BEGIN
	--	SET @tSql1 +=' AND Pdt.FTPbnCode BETWEEN ''' + @tPbnF + ''' AND ''' + @tPbnT + ''''
	--END

	IF (@tDocDateF <> '' AND @tDocDateT <> '')
	BEGIN
		SET @tSql1 +=' AND CONVERT(VARCHAR(10),FDXshDocDate,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''''
	END

	DELETE FROM TRPTPremRedemTmp WITH (ROWLOCK) WHERE FTComName =  '' + @nComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''--ลบข้อมูล Temp ของเครื่องที่จะบันทึกขอมูลลง Temp
 --Sale
  	SET @tSql  = ' INSERT INTO TRPTPremRedemTmp '
	SET @tSql +=' (FTComName,FTRptCode,FTUsrSession,'
	SET @tSql +=' FTBchCode,FTBchName,FDXshDocDate,FTXshDocNo,FTCstCode,FTCstName,FTCarRegNo,FTPdtCode,FTPdtName,FCXsdQtyAll,FTXshRefExt'
	SET @tSql +=' )'
	SET @tSql +=' SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'	
	SET @tSql +=' HD.FTBchCode ,BL.FTBchName,HD.FDXshDocDate,HD.FTXshDocNo,HD.FTCstCode,CstL.FTCstName,Car.FTCarRegNo,DT.FTPdtCode,'
	SET @tSql +=' DT.FTXsdPdtName,DT.FCXsdQtyAll,RefExt.FTXshRefDocNo AS FTXshRefExt'
	SET @tSql +=' FROM TSVTSalTwoHD HD'
	SET @tSql +=' INNER JOIN TSVTSalTwoDT DT ON HD.FTBchCode = DT.FTBchCode AND HD.FTXshDocNo = DT.FTXshDocNo'
	SET @tSql +=' LEFT JOIN TCNMBranch Bch ON HD.FTBchCode = Bch.FTBchCode'
	SET @tSql +=' LEFT JOIN TCNMBranch_L BL ON HD.FTBchCode = BL.FTBchCode AND BL.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
	SET @tSql +=' LEFT JOIN TCNMCst_L CstL ON HD.FTCstCode = CstL.FTCstCode AND CstL.FNLngID ='''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
	SET @tSql +=' LEFT JOIN' 
	  SET @tSql +=' (SELECT FTBchCode, FTXshDocNo,FTXshRefDocNo FROM' 
	   SET @tSql +=' TSVTSalTwoHDDocRef  WHERE  FTXshRefKey = ''RefExt'') AS RefExt'
	   SET @tSql +=' ON  HD.FTXshDocNo = RefExt.FTXshDocNo AND HD.FTBchCode = RefExt.FTBchCode'
	SET @tSql +=' LEFT JOIN' 
	  SET @tSql +=' (SELECT  FTBchCode,FTXshDocNo,FTXshRefDocNo FROM' 
	   SET @tSql +=' TSVTSalTwoHDDocRef  WHERE  FTXshRefKey = ''Job2Ord'') AS Job2Ord'
	   SET @tSql +=' ON  HD.FTXshDocNo = Job2Ord.FTXshDocNo AND HD.FTBchCode = Job2Ord.FTBchCode'
	--SET @tSql +=' LEFT JOIN TSVTJob2OrdHDCst JCst ON Job2Ord.FTXshRefDocNo = JCst.FTXshDocNo'
	SET @tSql +=' LEFT JOIN TSVMCar Car On HD.FTCstCode = Car.FTCarOwner'
	SET @tSql +=  @tSql1
	PRINT @tSql 
	EXECUTE(@tSql)

END TRY
BEGIN CATCH 
	SET @FNResult= -1
END CATCH	


/****** Object:  StoredProcedure [dbo].[SP_RPTxPSCouponByBillPmt]    Script Date: 1/12/2565 17:34:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxPSCouponByBillPmt]') AND type in (N'P', N'PC'))
BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxPSCouponByBillPmt] AS' 
END
GO
ALTER PROCEDURE [dbo].[SP_RPTxPSCouponByBillPmt] 
	@pnLngID int , 
	@pnComName Varchar(100),
	@ptRptCode Varchar(100),
	@ptUsrSession Varchar(255),
	@pnFilterType int, 
	--สาขา
	@ptBchL Varchar(8000), --กรณี Condition IN
	@ptBchF Varchar(5),
	@ptBchT Varchar(5),
	--เครื่องจุดขาย
	@ptPosCodeL Varchar(8000), --กรณี Condition IN
	@ptPosCodeF Varchar(20),
	@ptPosCodeT Varchar(20),
	--วันที่
	@ptDocDateF Varchar(10),
	@ptDocDateT Varchar(10),
	--คูปอง
	@ptCouponCodeF Varchar(8000), --กรณี Condition IN
	@FNResult INT OUTPUT 
AS
--------------------------------------
-- Watcharakorn 
-- Create 12/01/2021
-- Temp name  TRPTPSByPeriodTmp
-- @pnLngID ภาษา
-- @ptRptCdoe ชื่อรายงาน
-- @ptUsrSession UsrSession
-- @ptBchF จากรหัสสาขา
-- @ptBchT ถึงรหัสสาขา
--DECLARE @tPosCodeF Varchar(30)
--DECLARE @tPosCodeT Varchar(30)
-- @ptDocDateF จากวันที่
-- @ptDocDateT ถึงวันที่
-- @FNResult


--------------------------------------
BEGIN TRY

	DECLARE @nLngID int 
	DECLARE @nComName Varchar(100)
	DECLARE @tRptCode Varchar(100)
	DECLARE @tUsrSession Varchar(255)
	DECLARE @tSql VARCHAR(8000)
	DECLARE @tSql1 VARCHAR(8000)
    DECLARE @tSqlPos1 Varchar(255)
	DECLARE @tSqlPos2 Varchar(255)

	DECLARE @tBchF Varchar(5)
	DECLARE @tBchT Varchar(5)


	DECLARE @tPosCodeF Varchar(20)
	DECLARE @tPosCodeT Varchar(20)

	DECLARE @tDocDateF Varchar(10)
	DECLARE @tDocDateT Varchar(10)

	SET @nLngID = @pnLngID
	SET @nComName = @pnComName
	SET @tUsrSession = @ptUsrSession
	SET @tRptCode = @ptRptCode

	SET @tBchF = @ptBchF
	SET @tBchT = @ptBchT



	SET @tPosCodeF  = @ptPosCodeF 
	SET @tPosCodeT = @ptPosCodeT 

	SET @tDocDateF = @ptDocDateF
	SET @tDocDateT = @ptDocDateT

	
	--NUI 08-01-2020
	SET @tDocDateF = CONVERT(VARCHAR(10),@tDocDateF,121)
	SET @tDocDateT = CONVERT(VARCHAR(10),@tDocDateT,121)
	SET @FNResult= 0

	IF @nLngID = null
	BEGIN
		SET @nLngID = 1
	END	

	--Branch
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

	

	--Pos
	IF @ptPosCodeL = null
	BEGIN
		SET @ptPosCodeL = ''
	END
	IF @tPosCodeF = null
	BEGIN
		SET @tPosCodeF = ''
	END

	IF @tPosCodeT = null OR @tPosCodeT = ''
	BEGIN
		SET @tPosCodeT = @tPosCodeF
	END

	

	IF @tDocDateF = null
	BEGIN 
		SET @tDocDateF = ''
	END	
	IF @tDocDateT = null OR @tDocDateT = ''
	BEGIN
		SET @tDocDateT = @tDocDateF
	END

	SET @tSql1 =   ' '
	SET @tSqlPos1 =   ' '
	SET @tSqlPos2 =   ' '

	IF @pnFilterType = '2'
	BEGIN
		IF (@ptBchL <> '')
		BEGIN
			SET @tSql1 +=' AND CPH.FTBchCode IN (' + @ptBchL + ')'
		END

		IF (@ptPosCodeL <>'')
		BEGIN
			SET @tSqlPos1 += ' AND SHD.FTPosCode IN ('+@ptPosCodeL+ ')'
			SET @tSqlPos2 += ' AND SHD.FTXshToPos IN ('+@ptPosCodeL+ ')'
		END		
		IF (@ptCouponCodeF <>'')
		BEGIN
			SET @tSql1 += ' AND CPH.FTCphDocNo IN ('+@ptCouponCodeF+ ')'
		END	
	END

	

	IF (@tDocDateF <> '' AND @tDocDateT <> '')
	BEGIN
		SET @tSql1 +=' AND CONVERT(VARCHAR(10),FDXshDocDate,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''''
	END

	DELETE FROM TRPTPSCouponByBillTmp WITH (ROWLOCK) WHERE FTUsrSession = '' + @tUsrSession + ''--ลบข้อมูล Temp ของเครื่องที่จะบันทึกขอมูลลง Temp

	SET @tSql  =' INSERT INTO TRPTPSCouponByBillTmp'
	SET @tSql +=' ('
	SET @tSql +=' FTUsrSession,'
	SET @tSql +=' FTXshDocNo,FDXshDocDate,FTPmhDocNo,FTPmhName,FTXpdGetType,FTPdtCode,FTXsdBarCode,FTPdtName,FTSplCode,FTSplName,FTPunCode,FTPunName,'
	SET @tSql +=' FCXpdPoint,FCXsdQty,FCXsdNet,FCXpdDis,FCXsdNetPmt,FTBchName,FTPdtCatName1,FTPdtCatName2,FTXddRefCode,FTRcvName'
	SET @tSql +=' )'


	SET @tSql +=' SELECT  FTUsrSession, 
              FTCpbFrmSalRef, 
              FDXshDocDate, 
              FTCphDocNo , 
              FTCpnName, 
              FTXpdGetType, 
              FTPdtCode, 
              FTXsdBarCode, 
              FTXsdPdtName, 
              FTSplCode, 
              FTSplName, 
              FTPunCode, 
              FTPunName, 
              0 AS FCXpdPoint, 
              FCXsdQtyAll AS FCXsdQty,   
              FCXsdNet AS FCXsdNet,       
              FCXddValue AS FCXdtDisPmt,  
              FCXsdNetAfHD AS FCXsdNetPmt, 
              FTBchName, 
              FTCatName1 AS FTPdtCatName1, 
              FTCatName2 AS FTPdtCatName2,
			  FTXddRefCode,
			  rtRcvName FROM ( '
    SET @tSql +=' SELECT DISTINCT ''' + @tUsrSession + ''' AS FTUsrSession, 
                  CPH.FTCpbFrmSalRef, 
                  SHD.FDXshDocDate, 
                  CPH.FTCphDocNo, 
                  CPL.FTCpnName, 
                  '''' AS FTXpdGetType, 
                  SDT.FTPdtCode, 
                  '''' AS FTXsdBarCode, 
                  SDT.FTXsdPdtName, 
                  '''' AS FTSplCode, 
                  '''' AS FTSplName, 
                  '''' AS FTPunCode, 
                  SDT.FTPunName, 
                  0 AS FCXpdPoint, 
                  SDT.FCXsdQtyAll, 
                  SDT.FCXsdNet,
									DTD.SUMDIS AS FCXddValue,
									SDT.FCXsdNet - DTD.SUMDIS AS FCXsdNetAfHD, 
                  BCH.FTBchName, 
                  CL1.FTCatName AS FTCatName1, 
                  CL2.FTCatName AS FTCatName2, 
				  DTD.FTXddRefCode,
				  RCV.rtRcvName '
    SET @tSql +=' FROM TFNTCouponDTHis CPH '
    SET @tSql +=' INNER JOIN TPSTSalHD SHD ON CPH.FTCpbFrmSalRef = SHD.FTXshDocNo '
    SET @tSql +=' INNER JOIN TPSTSalDT SDT ON SHD.FTXshDocNo = SDT.FTXshDocNo AND SHD.FTBchCode = SDT.FTBchCode '
    SET @tSql +='  LEFT JOIN ( 
  SELECT FTBchCode , FTXshDocNo , FNXsdSeqNo , SUM(FCXddValue) AS SUMDIS,FTXddRefCode FROM TPSTSalDTDis 
  WHERE FTXddDisChgType = 5 OR FTXddDisChgType = 6
  GROUP BY FTBchCode , FTXshDocNo , FNXsdSeqNo,FTXddRefCode
 ) DTD ON SDT.FTBchCode = DTD.FTBchCode
 AND SDT.FTXshDocNo = DTD.FTXshDocNo 
 AND SDT.FNXsdSeqNo = DTD.FNXsdSeqNo 
 AND CPH.FTCpdBarCpn = DTD.FTXddRefCode '
    SET @tSql +=' LEFT JOIN TCNMPdtCategory PCT ON SDT.FTPdtCode = PCT.FTPdtCode '
    SET @tSql +=' LEFT JOIN TCNMPdtCatInfo_L CL1 ON PCT.FTPdtCat1 = CL1.FTCatCode AND CL1.FNCatLevel = 1 AND CL1.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
    SET @tSql +=' LEFT JOIN TCNMPdtCatInfo_L CL2 ON PCT.FTPdtCat2 = CL2.FTCatCode AND CL2.FNCatLevel = 2 AND CL2.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
    SET @tSql +=' LEFT JOIN TCNMBranch_L BCH ON SHD.FTBchCode = BCH.FTBchCode AND BCH.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
    SET @tSql +=' LEFT JOIN TFNTCouponHD_L CPL ON CPH.FTCphDocNo = CPL.FTCphDocNo AND CPL.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
	SET @tSql +=' LEFT JOIN (  SELECT FTXshDocNo, 
																		 rtRcvName = STUFF((
																	SELECT distinct '','' + FTRcvName
																	FROM TPSTSalRC t1
																	WHERE t1.FTXshDocNo = t2.FTXshDocNo FOR XML PATH('''')
																	), 1, 1, '''')
															FROM TPSTSalRC t2
															GROUP BY FTXshDocNo ) AS RCV ON SHD.FTXshDocNo = RCV.FTXshDocNo '
    SET @tSql +=' WHERE LEFT(CPH.FTCpbFrmSalRef, 1) = '''  + 'S' + ''''
    SET @tSql +=' AND CPH.FTCpbStaBook <> 3 '
	SET @tSql += @tSql1
	SET @tSql += @tSqlPos1

    SET @tSql +=' UNION ALL '
    SET @tSql +=' SELECT DISTINCT ''' + @tUsrSession + ''' AS FTUsrSession, 
                  CPH.FTCpbFrmSalRef, 
                  SHD.FDXshDocDate, 
                  CPH.FTCphDocNo, 
                  CPL.FTCpnName, 
                  '''' AS FTXpdGetType, 
                  SDT.FTPdtCode, 
                  '''' AS FTXsdBarCode, 
                  SDT.FTXsdPdtName, 
                  '''' AS FTSplCode, 
                  '''' AS FTSplName, 
                  '''' AS FTPunCode, 
                  SDT.FTPunName, 
                  0 AS FCXpdPoint, 
                  SDT.FCXsdQtyAll, 
                  SDT.FCXsdNet,
									DTD.SUMDIS AS FCXddValue,
									SDT.FCXsdNet - DTD.SUMDIS AS FCXsdNetAfHD, 
                  BCH.FTBchName, 
                  CL1.FTCatName AS FTCatName1, 
                  CL2.FTCatName AS FTCatName2,
				  DTD.FTXddRefCode,
				  RCV.rtRcvName '
    SET @tSql +=' FROM TFNTCouponDTHis CPH '
    SET @tSql +=' INNER JOIN TSVTSalTwoHD SHD ON CPH.FTCpbFrmSalRef = SHD.FTXshDocNo AND SHD.FNXshDocType = 2 '
    SET @tSql +=' INNER JOIN TSVTSalTwoDT SDT ON SHD.FTXshDocNo = SDT.FTXshDocNo AND SHD.FTBchCode = SDT.FTBchCode '
    SET @tSql +=' 		 LEFT JOIN ( 
  SELECT FTBchCode , FTXshDocNo , FNXsdSeqNo , SUM(FCXddValue) AS SUMDIS,FTXddRefCode FROM TSVTSalTwoDis 
  WHERE FTXddDisChgType = 5 OR FTXddDisChgType = 6
  GROUP BY FTBchCode , FTXshDocNo , FNXsdSeqNo,FTXddRefCode
 ) DTD ON SDT.FTBchCode = DTD.FTBchCode
 AND SDT.FTXshDocNo = DTD.FTXshDocNo 
 AND SDT.FNXsdSeqNo = DTD.FNXsdSeqNo
 AND CPH.FTCpdBarCpn = DTD.FTXddRefCode	'

    SET @tSql +=' LEFT JOIN TCNMPdtCategory PCT ON SDT.FTPdtCode = PCT.FTPdtCode '
    SET @tSql +=' LEFT JOIN TCNMPdtCatInfo_L CL1 ON PCT.FTPdtCat1 = CL1.FTCatCode AND CL1.FNCatLevel = 1 AND CL1.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
    SET @tSql +=' LEFT JOIN TCNMPdtCatInfo_L CL2 ON PCT.FTPdtCat2 = CL2.FTCatCode AND CL2.FNCatLevel = 2 AND CL2.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
    SET @tSql +=' LEFT JOIN TCNMBranch_L BCH ON SHD.FTBchCode = BCH.FTBchCode AND BCH.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
    SET @tSql +=' LEFT JOIN TFNTCouponHD_L CPL ON CPH.FTCphDocNo = CPL.FTCphDocNo AND CPL.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
	SET @tSql +=' LEFT JOIN (  SELECT FTXshDocNo, 
																		 rtRcvName = STUFF((
																	SELECT distinct '','' + FTRcvName
																	FROM TPSTSalRC t1
																	WHERE t1.FTXshDocNo = t2.FTXshDocNo FOR XML PATH('''')
																	), 1, 1, '''')
															FROM TPSTSalRC t2
															GROUP BY FTXshDocNo ) AS RCV ON SHD.FTXshDocNo = RCV.FTXshDocNo '
    SET @tSql +=' WHERE LEFT(CPH.FTCpbFrmSalRef, 3) = '''  + 'PWO' + ''''  
	SET @tSql +=' AND CPH.FTCpbStaBook <> 3 '
	SET @tSql += @tSql1
	SET @tSql += @tSqlPos2
    SET @tSql +=' ) C '
	SET @tSql +=' ORDER BY FTCphDocNo,FTBchName,FTCpbFrmSalRef,FDXshDocDate; '

	EXECUTE(@tSql)
	--SELECT(@tSql)
END TRY

BEGIN CATCH 
	SET @FNResult= -1
END CATCH
GO


------------ Script 08.01.03 ------------
/****** Object:  StoredProcedure [dbo].[SP_RPTxPdtBalByPdtGrp]    Script Date: 2/12/2565 17:51:01 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxPdtBalByPdtGrp]') AND type in (N'P', N'PC'))
BEGIN
EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxPdtBalByPdtGrp] AS' 
END
GO
ALTER PROCEDURE [dbo].[SP_RPTxPdtBalByPdtGrp] 
	@pnLngID int , 
	@pnComName Varchar(100),
	@ptRptCode Varchar(100),
	@ptUsrSession Varchar(255),
	@pnFilterType int, --1 BETWEEN 2 IN
	@ptAgnCode  Varchar(255),--FTAgnCode
	--2 สาขา
	@ptBchL Varchar(8000), --กรณี Condition IN
	
	--3. Merchant
	--@ptMerL Varchar(8000), --กรณี Condition IN

	--4. Shop Code
	--@ptShpL Varchar(8000), --กรณี Condition IN

	--7. กลุ่มสินค้า
	@ptPgpF Varchar(20),
	@ptPgpT Varchar(20),
	
	--8.
	@FNResult INT OUTPUT 
AS
--------------------------------------
-- Watcharakorn 
-- Create 26/01/2021
-- Temp name  TRPTPdtBalByBchTmp

--------------------------------------
BEGIN TRY

	DECLARE @nLngID int 
	DECLARE @nComName Varchar(100)
	DECLARE @tRptCode Varchar(100)
	DECLARE @tUsrSession Varchar(255)
	DECLARE @tSql VARCHAR(8000)
	DECLARE @tSql1 VARCHAR(8000)
	DECLARE @tSqlPdt VARCHAR(8000)


	SET @nLngID = @pnLngID
	SET @nComName = @pnComName
	SET @tUsrSession = @ptUsrSession
	SET @tRptCode = @ptRptCode

		SET @FNResult= 0

	IF @nLngID = null
	BEGIN
		SET @nLngID = 1
	END	
	--Set ค่าให้ Paraleter กรณี T เป็นค่าว่างหรือ null
	--Branch
	IF @ptBchL = null
	BEGIN
		SET @ptBchL = ''
	END

/*
	---Merchant
	IF @ptMerL =null
	BEGIN
		SET @ptMerL = ''
	END
*/
	----------------
	/*
	--SHOP
	IF @ptShpL =null
	BEGIN
		SET @ptShpL = ''
	END
*/
	------------------

	IF @ptPgpF =null
	BEGIN
		SET @ptPgpF = ''
	END
	IF @ptPgpT =null OR @ptPgpT = ''
	BEGIN
		SET @ptPgpT = @ptPgpF
	END 

	SET @tSql1 = ' WHERE 1=1 '
	IF @pnFilterType = 2
	BEGIN

		--SET @tSql1 += '111111'
		--PRINT @tSql1		

	IF (@ptAgnCode <> '')
		BEGIN
			SET @tSql1 +=' AND AGN.FTAgnCode =''' + @ptAgnCode + ''' '
		END

		IF (@ptBchL <> '')
		BEGIN
			SET @tSql1 +=' AND Stk.FTBchCode IN (' + @ptBchL + ')'
		END
/*
		IF (@ptMerL <> '' )
		BEGIN
			SET @tSql1 +=' AND Shp.FTMerCode IN (' + @ptMerL + ')'
		END
*/
/*
		IF (@ptShpL <> '')
		BEGIN
			SET @tSql1 +=' AND Stk.FTShpCode IN (' + @ptShpL + ')'
		END
*/
	END

	IF (@ptPgpF <> '' AND @ptPgpT <> '')
	BEGIN
		SET @tSql1 +=' AND GRP.FTPgpChain BETWEEN ''' + @ptPgpF + ''' AND ''' + @ptPgpT + ''''
	END
	--PRINT @tSql1
	--PRINT @tSqlPdt
	
	DELETE FROM TRPTPdtBalByPdtGrpTmp  WHERE FTComName =  '' + @nComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''--ลบข้อมูล Temp ของเครื่องที่จะบันทึกขอมูลลง Temp
	--SET @tSql   = '1111111'
	--PRINT  (@tSql)
	SET @tSql  =' INSERT INTO TRPTPdtBalByPdtGrpTmp '
	SET @tSql +=' ('
	SET @tSql +=' FTComName,FTRptCode,FTUsrSession,'
	
	--SET @tSql +=' FTBchCode,FTBchName,
	SET @tSql +=' FTBchCode,FTBchName,FTAgnCode,FTAgnName,FTPdtCode,FTPdtName,FTPgpChain,FTPgpChainName,FTWahCode,FTWahName,FCStkQty,FCPdtCostStd,FCPdtCostAVGEX,FCPdtCostTotal,FTPdtCostStdAmt'
	SET @tSql +=' ) '
		SET @tSql +=' SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'	


		SET @tSql +=' STK.FTBchCode,BCHL.FTBchName, BCH.FTAgnCode, AGNL.FTAgnName, STK.FTPdtCode,'
		SET @tSql +=' PDTL.FTPdtName, GRP.FTPgpCode, GRPL.FTPgpChainName, STK.FTWahCode, WHA.FTWahName, STK.FCStkQty,'
		SET @tSql +=' CASE WHEN AGN.FTAgnType = ''2'' THEN COST.FCPdtCostStd ELSE PDT.FCPdtCostStd END  AS FCPdtCostStd,'
		SET @tSql +=' COST.FCPdtCostEx,'
		SET @tSql +=' ISNULL(STK.FCStkQty,0) * ISNULL(COST.FCPdtCostEx,0) AS FCPdtCostTotal,'
		SET @tSql +=' CASE WHEN AGN.FTAgnType = ''2'' THEN ISNULL(COST.FCPdtCostStd,0) ELSE ISNULL(PDT.FCPdtCostStd,0) END * ISNULL(STK.FCStkQty,0) AS FTPdtCostStdAmt'
		SET @tSql +=' FROM TCNTPdtStkBal STK WITH(NOLOCK)'
		SET @tSql +=' LEFT JOIN TCNMBranch BCH WITH(NOLOCK) ON STK.FTBchCode = BCH.FTBchCode'
		SET @tSql +=' LEFT JOIN TCNMBranch_L BCHL WITH(NOLOCK) ON STK.FTBchCode = BCHL.FTBchCode AND BCHL.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + '''' 
		SET @tSql +=' LEFT JOIN TCNMAgency AGN WITH(NOLOCK) ON BCH.FTAgnCode = AGN.FTAgnCode'
		SET @tSql +=' LEFT JOIN TCNMAgency_L AGNL WITH(NOLOCK) ON AGN.FTAgnCode = AGNL.FTAgnCode AND AGNL.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + '''' 
		SET @tSql +=' LEFT JOIN TCNMPdtCostAvg COST WITH(NOLOCK) ON STK.FTPdtCode = COST.FTPdtCode AND BCH.FTAgnCode = COST.FTAgnCode'
		SET @tSql +=' LEFT JOIN TCNMPdt PDT WITH(NOLOCK) ON STK.FTPdtCode = PDT.FTPdtCode  '
		SET @tSql +=' LEFT JOIN TCNMPdt_L PDTL WITH(NOLOCK) ON PDT.FTPdtCode = PDTL.FTPdtCode AND PDTL.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + '''' 
		SET @tSql +=' LEFT JOIN TCNMPdtGrp GRP WITH(NOLOCK) ON  PDT.FTPgpChain = GRP.FTPgpChain'
		SET @tSql +=' LEFT JOIN TCNMPdtGrp_L GRPL WITH(NOLOCK) ON  GRP.FTPgpChain = GRPL.FTPgpChain AND GRPL.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + '''' 
		SET @tSql +=' LEFT JOIN TCNMWaHouse_L WHA WITH(NOLOCK) ON  STK.FTBchCode =  WHA.FTBchCode AND STK.FTWahCode = WHA.FTWahCode AND WHA.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + '''' 
	  SET @tSql += @tSql1
			--Filter
			--WHERE AGN.FTAgnCode   = ''
			--AND STK.FTBchCode IN('')
			--AND GRP.FTPgpChain BETWEEN ''  AND  ''

		SET @tSql +=' ORDER BY AGN.FTAgnCode,BCH.FTBchCode'



	PRINT (@tSql)
	EXECUTE (@tSql)
END TRY

BEGIN CATCH 
	SET @FNResult= -1
END CATCH	
GO


/****** Object:  StoredProcedure [dbo].[SP_RPTxPSSVat1001006]    Script Date: 2/12/2565 17:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxPSSVat1001006]') AND type in (N'P', N'PC'))
BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxPSSVat1001006] AS' 
END
GO
ALTER PROCEDURE [dbo].[SP_RPTxPSSVat1001006] 

--ALTER PROCEDURE [dbo].[SP_RPTxPSSVat1001006] 
	@pnLngID int , 
	@pnComName Varchar(100),
	@ptRptCode Varchar(100),
	@ptUsrSession Varchar(255),
	@pnFilterType int, --1 BETWEEN 2 IN
	--สาขา
	@ptBchL Varchar(8000), --กรณี Condition IN
	@ptBchF Varchar(5),
	@ptBchT Varchar(5),
	--Merchant
	@ptMerL Varchar(8000), --กรณี Condition IN
	@ptMerF Varchar(10),
	@ptMerT Varchar(10),
	--Shop Code
	@ptShpL Varchar(8000), --กรณี Condition IN
	@ptShpF Varchar(10),
	@ptShpT Varchar(10),
	--เครื่องจุดขาย
	@ptPosL Varchar(8000), --กรณี Condition IN
	@ptPosF Varchar(20),
	@ptPosT Varchar(20),
	--@ptBchF Varchar(5),
	--@ptBchT Varchar(5),
	----เครื่องจุดขาย
	--@ptPosCodeF Varchar(20),
	--@ptPosCodeT Varchar(20),

	@ptDocDateF Varchar(10),
	@ptDocDateT Varchar(10),
	----ลูกค้า
	--@ptCstF Varchar(20),
	--@ptCstT Varchar(20),
	@FNResult INT OUTPUT 
AS
--------------------------------------
-- Watcharakorn 
-- Create 10/07/2019
-- Temp name  TRPTSalRCTmp
-- @pnLngID ภาษา
-- @ptRptCdoe ชื่อรายงาน
-- @ptUsrSession UsrSession
-- @ptBchF จากรหัสสาขา
-- @ptBchT ถึงรหัสสาขา
	--DECLARE @tPosCodeF Varchar(30)
	--DECLARE @tPosCodeT Varchar(30)
-- @ptDocDateF จากวันที่
-- @ptDocDateT ถึงวันที่
-- @FNResult


--------------------------------------
BEGIN TRY

	DECLARE @nLngID int 
	DECLARE @nComName Varchar(100)
	DECLARE @tRptCode Varchar(100)
	DECLARE @tUsrSession Varchar(255)
	DECLARE @tSql VARCHAR(8000)
	DECLARE @tSqlIns VARCHAR(8000)
	DECLARE @tSql1 nVARCHAR(Max)
	DECLARE @tSql2 VARCHAR(8000)
	DECLARE @tSql3 VARCHAR(8000)

	--DECLARE @tBchF Varchar(5)
	--DECLARE @tBchT Varchar(5)

	--DECLARE @tPosCodeF Varchar(20)
	--DECLARE @tPosCodeT Varchar(20)

	--Branch Code
	DECLARE @tBchF Varchar(5)
	DECLARE @tBchT Varchar(5)
	--Merchant
	DECLARE @tMerF Varchar(10)
	DECLARE @tMerT Varchar(10)
	--Shop Code
	DECLARE @tShpF Varchar(10)
	DECLARE @tShpT Varchar(10)
	--Pos Code
	DECLARE @tPosF Varchar(20)
	DECLARE @tPosT Varchar(20)

	DECLARE @tDocDateF Varchar(10)
	DECLARE @tDocDateT Varchar(10)
	--ลูกค้า
	--DECLARE @tCstF Varchar(20)
	--DECLARE @tCstT Varchar(20)


	--SET @nLngID = 1
	--SET @nComName = 'Ada062'
	--SET @tRptName = 'PSSVat1001006'
	--SET @ptUsrSession = '001'
	--SET @tBchF = '001'
	--SET @tBchT = '001'

	--SET @tDocDateF = '2019-07-01'
	--SET @tDocDateT = '2019-07-10'


	--SET @nLngID = 1
	--SET @nComName = 'Ada062'
	--SET @tRptName = 'DailySaleByInv1001001'
	--SET @ptUsrSession = '001'
	--SET @tBchF = ''
	--SET @tBchT = ''

	--SET @tDocDateF = ''
	--SET @tDocDateT = ''

	SET @nLngID = @pnLngID
	SET @nComName = @pnComName
	SET @tUsrSession = @ptUsrSession
	SET @tRptCode = @ptRptCode

	--SET @tBchF = @ptBchF
	--SET @tBchT = @ptBchT

	--SET @tPosCodeF  = @ptPosCodeF 
	--SET @tPosCodeT = @ptPosCodeT 

	--Branch
	SET @tBchF  = @ptBchF
	SET @tBchT  = @ptBchT
	--Merchant
	SET @tMerF  = @ptMerF
	SET @tMerT  = @ptMerT
	--Shop
	SET @tShpF  = @ptShpF
	SET @tShpT  = @ptShpT
	--Pos
	SET @tPosF  = @ptPosF 
	SET @tPosT  = @ptPosT

	SET @tDocDateF = @ptDocDateF
	SET @tDocDateT = @ptDocDateT

	SET @FNResult= 0

	SET @tDocDateF = CONVERT(VARCHAR(10),@tDocDateF,121)
	SET @tDocDateT = CONVERT(VARCHAR(10),@tDocDateT,121)

	IF @nLngID = null
	BEGIN
		SET @nLngID = 1
	END	
	--Set ค่าให้ Paraleter กรณี T เป็นค่าว่างหรือ null


	--IF @tBchF = null
	--BEGIN
	--	SET @tBchF = ''
	--END
	--IF @tBchT = null OR @tBchT = ''
	--BEGIN
	--	SET @tBchT = @tBchF
	--END

	--IF @tPosCodeF = null
	--BEGIN
	--	SET @tPosCodeF = ''
	--END

	--IF @tPosCodeT = null OR @tPosCodeT = ''
	--BEGIN
	--	SET @tPosCodeT = @tPosCodeF
	--END

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

	IF @ptMerL =null
	BEGIN
		SET @ptMerL = ''
	END

	IF @tMerF =null
	BEGIN
		SET @tMerF = ''
	END
	IF @tMerT =null OR @tMerT = ''
	BEGIN
		SET @tMerT = @tMerF
	END 

	IF @ptShpL =null
	BEGIN
		SET @ptShpL = ''
	END

	IF @tShpF =null
	BEGIN
		SET @tShpF = ''
	END
	IF @tShpT =null OR @tShpT = ''
	BEGIN
		SET @tShpT = @tShpF
	END

	IF @ptPosL =null
	BEGIN
		SET @ptPosL = ''
	END

	IF @tPosF = null
	BEGIN
		SET @tPosF = ''
	END
	IF @tPosT = null OR @tPosT = ''
	BEGIN
		SET @tPosT = @tPosF
	END

	IF @tDocDateF = null
	BEGIN 
		SET @tDocDateF = ''
	END

	IF @tDocDateT = null OR @tDocDateT =''
	BEGIN 
		SET @tDocDateT = @tDocDateF
	END

	--SET @tSqlSal =  ' WHERE 1=1 AND FTXshStaDoc = ''1'''
	--SET @tSqlVD =   ' WHERE 1=1 AND FTXshStaDoc = ''1'' AND Rcv.FTFmtCode <> ''004'''


	--IF (@tBchF <> '' AND @tBchT <> '')
	--BEGIN
	--	SET @tSql1 +=' AND FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
	--END

	--IF (@tPosCodeF <> '' AND @tPosCodeT <> '')
	--	BEGIN
	--		SET @tSql1 += ' AND HD.FTPosCode BETWEEN '''+@tPosCodeF+''' AND '''+@tPosCodeT+''''
	--	END		

	SET @tSql1 = ' '
	SET @tSql3 = ' '
	IF @pnFilterType = '1'
	BEGIN
		IF (@tBchF <> '' AND @tBchT <> '')
		BEGIN
			SET @tSql1 +=' AND HD.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
		END

		IF (@tMerF <> '' AND @tMerT <> '')
		BEGIN
			SET @tSql1 +=' AND Shp.FTMerCode BETWEEN ''' + @tMerF + ''' AND ''' + @tMerT + ''''
		END

		IF (@tShpF <> '' AND @tShpT <> '')
		BEGIN
			SET @tSql1 +=' AND HD.FTShpCode BETWEEN ''' + @tShpF + ''' AND ''' + @tShpT + ''''
		END

		IF (@tPosF <> '' AND @tPosT <> '')
		BEGIN
			SET @tSql1 += ' AND HD.FTPosCode BETWEEN ''' + @tPosF + ''' AND ''' + @tPosT + ''''
		END		
	END

	IF @pnFilterType = '2'
	BEGIN
		IF (@ptBchL <> '' )
		BEGIN
			SET @tSql1 +=' AND HD.FTBchCode IN (' + @ptBchL + ')'
		END

		IF (@ptMerL <> '' )
		BEGIN
			SET @tSql1 +=' AND Shp.FTMerCode IN (' + @ptMerL + ')'
		END

		IF (@ptShpL <> '')
		BEGIN
			SET @tSql1 +=' AND HD.FTShpCode IN (' + @ptShpL + ')'
		END

		IF (@ptPosL <> '')
		BEGIN
			SET @tSql1 += ' AND HD.FTPosCode IN (' + @ptPosL + ')'
		END		
	END

	IF (@tDocDateF <> '' AND @tDocDateT <> '')
	BEGIN
		SET @tSql1 +=' AND CONVERT(VARCHAR(10),FDXshDocDate,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''''
	END

	DELETE FROM TRPTPSTaxHDTmp WITH (ROWLOCK) WHERE FTComName =  '' + @nComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''--ลบข้อมูล Temp ของเครื่องที่จะบันทึกขอมูลลง Temp
 
 	SET @tSql  = ' INSERT INTO TRPTPSTaxHDTmp '
	SET @tSql +=' ('
	SET @tSql +=' FTComName,FTRptCode,FTUsrSession,'
	SET @tSql +=' FTBchCode,FTBchName,FDXshdocDate,FTXshDocNo,FTPosCode,FTXshDocRef,FTCstCode,FTCstName,FTCstTaxNo,FCXshAmt,FCXshVat,FCXshAmtNV,FCXshGrandTotal,'
	--*NUI 2019-11-14
	SET @tSql +=' FNAppType,FTPosRegNo,FTCstBchCode,FTCstBusiness,FTEstablishment,FTXshTaxID'
	-------------
	SET @tSql +=' )'
	SET @tSql +=' SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'	
	SET @tSql +=' SalVat.FTBchCode,FTBchName,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,FTXshDocNo,FTPosCode,FTXshRefInt,FTCstCode,FTCstName,FTCstTaxNo,FCXshValue,FCXshVat,FCXshAmtNV,FCXshGrand,'
	--*NUI 2019-11-14
	SET @tSql +=' FNAppType,FTPosRegNo,FTCstBchCode,FTCstBusiness,FTEstablishment,FTCstTaxNo AS FTXshTaxID'
	SET @tSql +=' FROM'	
			SET @tSql +=' (SELECT HD.FTBchCode,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,HD.FTPosCode,FTXshDocNo,ISNULL(FTXshRefInt,'''') FTXshRefInt,HD.FTCstCode,Cst_L.FTCstName,Cst.FTCstTaxNo,'
			--NUI10-04-2020
			SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshVatable,0)-ISNULL(FCXshAmtNV,0) ELSE (ISNULL(FCXshVatable,0)-ISNULL(FCXshAmtNV,0))*-1 END AS FCXshValue,'
			SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshVat,0) ELSE ISNULL(FCXshVat,0)*-1 END AS FCXshVat,'
			SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshAmtNV,0) ELSE ISNULL(FCXshAmtNV,0)*-1 END AS FCXshAmtNV,'
			--NUI10-04-2020
			SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0) ELSE (ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0))*-1 END AS FCXshGrand,'
			--*NUI 2019-11-14
			SET @tSql +=' 1 AS FNAppType,POS.FTPosRegNo,Cst.FTCstBchCode,Cst.FTCstBusiness,'
			SET @tSql +=' CASE WHEN ISNULL(Cst.FTCstBusiness,'''') <> ''1'' THEN '''' ELSE CASE WHEN FTCstBchHQ = ''2'' THEN ''2''  ELSE ''1'' END  END AS FTEstablishment'
			SET @tSql +=' FROM TPSTSalHD HD LEFT JOIN'
		 			  SET @tSql +=' TCNMCst Cst ON HD.FTCstCode = Cst.FTCstCode LEFT JOIN'
					  SET @tSql +=' TCNMPOS POS ON HD.FTPosCode = Pos.FTPosCode AND HD.FTBchCode = Pos.FTBchCode LEFT JOIN'
					  SET @tSql +=' TCNMCst_L Cst_L ON HD.FTCstCode = Cst_L.FTCstCode AND Cst_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' '
					  SET @tSql +=' LEFT JOIN TCNMShop Shp WITH (NOLOCK) ON HD.FTBchCode = Shp.FTBchCode AND HD.FTShpCode = Shp.FTShpCode '
					  SET @tSql +=' WHERE 1=1 AND FTXshStaDoc = ''1'''			  			
			SET @tSql += @tSql1			
			SET @tSql +=' ) SalVat LEFT JOIN '    
	SET @tSql +=' TCNMBranch_L Bch_L ON SalVat.FTBchCode = Bch_L.FTBchCode AND Bch_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' '
		
	SET @tsql3 += ' UNION'

	SET @tSql3 +=' SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'	
	SET @tSql3 +=' SalVat.FTBchCode,FTBchName,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,FTXshDocNo,FTPosCode,FTXshRefInt,FTCstCode,FTCstName,FTCstTaxNo,FCXshValue,FCXshVat,FCXshAmtNV,FCXshGrand,'
	--*NUI 2019-11-14
	SET @tSql3 +=' FNAppType,FTPosRegNo,FTCstBchCode,FTCstBusiness,FTEstablishment,FTCstTaxNo AS FTXshTaxID'
	SET @tSql3 +=' FROM'	
			SET @tSql3 +=' (SELECT HD.FTBchCode,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,HD.FTPosCode,HD.FTXshDocNo,ISNULL(FTXshRefInt,'''') FTXshRefInt,HD.FTCstCode,Cst_L.FTCstName,Cst.FTCstTaxNo,'
			--NUI10-04-2020
			--SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshVatable,0) ELSE ISNULL(FCXshVatable,0)*-1 END AS FCXshValue,'
			SET @tSql3 +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshVatable,0)-ISNULL(FCXshAmtNV,0) ELSE (ISNULL(FCXshVatable,0)-ISNULL(FCXshAmtNV,0))*-1 END AS FCXshValue,'
			SET @tSql3 +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshVat,0) ELSE ISNULL(FCXshVat,0)*-1 END AS FCXshVat,'
			SET @tSql3 +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshAmtNV,0) ELSE ISNULL(FCXshAmtNV,0)*-1 END AS FCXshAmtNV,'
			--NUI10-04-2020
			--SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshGrand,0) ELSE ISNULL(FCXshGrand,0)*-1 END AS FCXshGrand,'
			--SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0) ELSE (ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0))*-1 END AS FCXshGrand,'
			SET @tSql3 +=' CASE WHEN HD.FNXshDocType = 1 THEN ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0) ELSE (ISNULL(FCXshGrand,0))*-1 END AS FCXshGrand,'
			--*NUI 2019-11-14
			SET @tSql3 +=' 2 AS FNAppType,POS.FTPosRegNo,Cst.FTCstBchCode,Cst.FTCstBusiness,'
			SET @tSql3 +=' CASE WHEN ISNULL(Cst.FTCstBusiness,'''') <> ''1'' THEN '''' ELSE CASE WHEN FTCstBchHQ = ''2'' THEN ''2''  ELSE ''1'' END  END AS FTEstablishment'
			SET @tSql3 +=' FROM TVDTSalHD HD' 
			--NUI 2020-01-06
			SET @tSql3 +=' INNER JOIN TVDTSalRC RC WITH(NOLOCK) ON HD.FTBchCode = RC.FTBchCode AND HD.FTXshDocNo = RC.FTXshDocNo'
			SET @tSql3 +=' LEFT JOIN TFNMRcv Rcv WITH(NOLOCK) ON  RC.FTRcvCode = Rcv.FTRcvCode'			
			------------
		 			  SET @tSql3 +=' LEFT JOIN TCNMCst Cst ON HD.FTCstCode = Cst.FTCstCode LEFT JOIN'
					  SET @tSql3 +=' TCNMPOS POS ON HD.FTPosCode = Pos.FTPosCode AND HD.FTBchCode = Pos.FTBchCode LEFT JOIN'
					  SET @tSql3 +=' TCNMCst_L Cst_L ON HD.FTCstCode = Cst_L.FTCstCode AND Cst_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' '
					  SET @tSql3 +=' LEFT JOIN TCNMShop Shp WITH (NOLOCK) ON HD.FTBchCode = Shp.FTBchCode AND HD.FTShpCode = Shp.FTShpCode '
					  SET @tSql3 +=' WHERE 1=1 AND FTXshStaDoc = ''1'' AND Rcv.FTFmtCode <> ''004'''
			SET @tSql3 +=  @tSql1
			SET @tSql3 +=' ) SalVat LEFT JOIN '    
	SET @tSql3 +=' TCNMBranch_L Bch_L ON SalVat.FTBchCode = Bch_L.FTBchCode AND Bch_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''					 

	--PRINT @tSql
	EXECUTE(@tSql + @tSql3)

	RETURN SELECT * FROM TRPTPSTaxHDTmp WHERE FTComName = ''+ @nComName + '' AND FTRptCode = ''+ @tRptCode +'' AND FTUsrSession = '' + @tUsrSession + ''
	
END TRY

BEGIN CATCH 
	SET @FNResult= -1
END CATCH	

--SELECT * FROM TRPTPSTaxHDTmp

GO


/****** Object:  StoredProcedure [dbo].[SP_RPTxPSSVatByDate1001007]    Script Date: 2/12/2565 17:57:13 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxPSSVatByDate1001007]') AND type in (N'P', N'PC'))
BEGIN
	EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxPSSVatByDate1001007] AS' 
END
GO
ALTER PROCEDURE [dbo].[SP_RPTxPSSVatByDate1001007] 
    --ALTER PROCEDURE [dbo].[SP_RPTxPSSVatByDate1001007] 
        @pnLngID int , 
        @pnComName Varchar(100),
        @ptRptCode Varchar(100),
        @ptUsrSession Varchar(255),
        @pnFilterType int, --1 BETWEEN 2 IN
        --สาขา
        @ptBchL Varchar(8000), --กรณี Condition IN
        @ptBchF Varchar(5),
        @ptBchT Varchar(5),
        --Merchant
        @ptMerL Varchar(8000), --กรณี Condition IN
        @ptMerF Varchar(10),
        @ptMerT Varchar(10),
        --Shop Code
        @ptShpL Varchar(8000), --กรณี Condition IN
        @ptShpF Varchar(10),
        @ptShpT Varchar(10),
        --เครื่องจุดขาย
        @ptPosL Varchar(8000), --กรณี Condition IN
        @ptPosF Varchar(20),
        @ptPosT Varchar(20),
        @ptDocDateF Varchar(10),
        @ptDocDateT Varchar(10),
        @FNResult INT OUTPUT 
    AS
    --------------------------------------
    -- Watcharakorn 
    -- Create 10/07/2019
    -- Temp name  TRPTSalRCTmp
    -- @pnLngID ภาษา
    -- @ptRptCdoe ชื่อรายงาน
    -- @ptUsrSession UsrSession
    -- @ptBchF จากรหัสสาขา
    -- @ptBchT ถึงรหัสสาขา
        --DECLARE @tPosCodeF Varchar(30)
        --DECLARE @tPosCodeT Varchar(30)
    -- @ptDocDateF จากวันที่
    -- @ptDocDateT ถึงวันที่
    -- @FNResult


    --------------------------------------
    BEGIN TRY

        DECLARE @nLngID int 
        DECLARE @nComName Varchar(100)
        DECLARE @tRptCode Varchar(100)
        DECLARE @tUsrSession Varchar(255)
        DECLARE @tSql VARCHAR(8000)
        DECLARE @tSqlDrop VARCHAR(8000)
        DECLARE @tSql1 nVARCHAR(Max)
        DECLARE @tSqlSale VARCHAR(8000)
        DECLARE @tTblName Varchar(255)
        DECLARE @tSqlS Varchar(MAX)
        DECLARE @tSqlR Varchar(MAX)
        DECLARE @tSql2 VARCHAR(MAX)

        --Branch Code
        DECLARE @tBchF Varchar(5)
        DECLARE @tBchT Varchar(5)
        --Merchant
        DECLARE @tMerF Varchar(10)
        DECLARE @tMerT Varchar(10)
        --Shop Code
        DECLARE @tShpF Varchar(10)
        DECLARE @tShpT Varchar(10)
        --Pos Code
        DECLARE @tPosF Varchar(20)
        DECLARE @tPosT Varchar(20)

        DECLARE @tDocDateF Varchar(10)
        DECLARE @tDocDateT Varchar(10)

        SET @nLngID = @pnLngID
        SET @nComName = @pnComName
        SET @tUsrSession = @ptUsrSession
        SET @tRptCode = @ptRptCode

        --Branch
        SET @tBchF  = @ptBchF
        SET @tBchT  = @ptBchT
        --Merchant
        SET @tMerF  = @ptMerF
        SET @tMerT  = @ptMerT
        --Shop
        SET @tShpF  = @ptShpF
        SET @tShpT  = @ptShpT
        --Pos
        SET @tPosF  = @ptPosF 
        SET @tPosT  = @ptPosT

        SET @tDocDateF = @ptDocDateF
        SET @tDocDateT = @ptDocDateT

        SET @FNResult= 0

        SET @tDocDateF = CONVERT(VARCHAR(10),@tDocDateF,121)
        SET @tDocDateT = CONVERT(VARCHAR(10),@tDocDateT,121)

        IF @nLngID = null
        BEGIN
            SET @nLngID = 1
        END	
        --Set ค่าให้ Paraleter กรณี T เป็นค่าว่างหรือ null

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

        IF @ptMerL =null
        BEGIN
            SET @ptMerL = ''
        END

        IF @tMerF =null
        BEGIN
            SET @tMerF = ''
        END
        IF @tMerT =null OR @tMerT = ''
        BEGIN
            SET @tMerT = @tMerF
        END 

        IF @ptShpL =null
        BEGIN
            SET @ptShpL = ''
        END

        IF @tShpF =null
        BEGIN
            SET @tShpF = ''
        END
        IF @tShpT =null OR @tShpT = ''
        BEGIN
            SET @tShpT = @tShpF
        END

        IF @tPosF = null
        BEGIN
            SET @tPosF = ''
        END
        IF @tPosT = null OR @tPosT = ''
        BEGIN
            SET @tPosT = @tPosF
        END


        IF @tDocDateF = null
        BEGIN 
            SET @tDocDateF = ''
        END

        IF @tDocDateT = null OR @tDocDateT =''
        BEGIN 
            SET @tDocDateT = @tDocDateF
        END

        SET @tSql2 =   ' WHERE 1=1 AND FTXshStaDoc = ''1'''
        SET @tSqlS =   ' WHERE 1=1 AND FTXshStaDoc = ''1'' AND FNXshDocType = ''1'''
        SET @tSqlR =   ' WHERE 1=1 AND FTXshStaDoc = ''1'' AND FNXshDocType = ''9'''

        IF @pnFilterType = '1'
        BEGIN
            IF (@tBchF <> '' AND @tBchT <> '')
            BEGIN
                SET @tSqlS +=' AND HD.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
                SET @tSqlR +=' AND HD.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
                SET @tSql2 +=' AND HD.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
            END

            IF (@tMerF <> '' AND @tMerT <> '')
            BEGIN
                SET @tSqlS +=' AND Shp.FTMerCode BETWEEN ''' + @tMerF + ''' AND ''' + @tMerT + ''''
                SET @tSqlR +=' AND Shp.FTMerCode BETWEEN ''' + @tMerF + ''' AND ''' + @tMerT + ''''
                SET @tSql2 +=' AND Shp.FTMerCode BETWEEN ''' + @tMerF + ''' AND ''' + @tMerT + ''''
            END

            IF (@tShpF <> '' AND @tShpT <> '')
            BEGIN
                SET @tSqlS +=' AND HD.FTShpCode BETWEEN ''' + @tShpF + ''' AND ''' + @tShpT + ''''
                SET @tSqlR +=' AND HD.FTShpCode BETWEEN ''' + @tShpF + ''' AND ''' + @tShpT + ''''
                SET @tSql2 +=' AND HD.FTShpCode BETWEEN ''' + @tShpF + ''' AND ''' + @tShpT + ''''
            END

            IF (@tPosF <> '' AND @tPosT <> '')
            BEGIN
                SET @tSqlS += ' AND HD.FTPosCode BETWEEN ''' + @tPosF + ''' AND ''' + @tPosT + ''''
                SET @tSqlR += ' AND HD.FTPosCode BETWEEN ''' + @tPosF + ''' AND ''' + @tPosT + ''''
                SET @tSql2 += ' AND HD.FTPosCode BETWEEN ''' + @tPosF + ''' AND ''' + @tPosT + ''''
            END		
        END

        IF @pnFilterType = '2'
        BEGIN
            IF (@ptBchL <> '' )
            BEGIN
                SET @tSqlS +=' AND HD.FTBchCode IN (' + @ptBchL + ')'
                SET @tSqlR +=' AND HD.FTBchCode IN (' + @ptBchL + ')'
                SET @tSql2 +=' AND HD.FTBchCode IN (' + @ptBchL + ')'
            END

            IF (@ptMerL <> '' )
            BEGIN
                SET @tSqlS +=' AND Shp.FTMerCode IN (' + @ptMerL + ')'
                SET @tSqlR +=' AND Shp.FTMerCode IN (' + @ptMerL + ')'
                SET @tSql2 +=' AND Shp.FTMerCode IN (' + @ptMerL + ')'
            END

            IF (@ptShpL <> '')
            BEGIN
                SET @tSqlS +=' AND HD.FTShpCode IN (' + @ptShpL + ')'
                SET @tSqlR +=' AND HD.FTShpCode IN (' + @ptShpL + ')'
                SET @tSql2 +=' AND HD.FTShpCode IN (' + @ptShpL + ')'
            END

            IF (@ptPosL <> '')
            BEGIN
                SET @tSqlS += ' AND HD.FTPosCode IN (' + @ptPosL + ')'
                SET @tSqlR += ' AND HD.FTPosCode IN (' + @ptPosL + ')'
                SET @tSql2 += ' AND HD.FTPosCode IN (' + @ptPosL + ')'
            END		
        END


        IF (@tDocDateF <> '' AND @tDocDateT <> '')
        BEGIN
            SET @tSql2 +=' AND CONVERT(VARCHAR(10),FDXshDocDate,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''''
            SET @tSqlS +=' AND CONVERT(VARCHAR(10),FDXshDocDate,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''''
            SET @tSqlR +=' AND CONVERT(VARCHAR(10),FDXshDocDate,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''''
        END

        DELETE FROM TRPTPSTaxHDDateTmp WITH (ROWLOCK) WHERE FTComName =  '' + @nComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''--ลบข้อมูล Temp ของเครื่องที่จะบันทึกขอมูลลง Temp


        SET @tSqlSale  = ' INSERT INTO TRPTPSTaxHDDateTmp '
        SET @tSqlSale +=' ('
        SET @tSqlSale +=' FTComName,FTRptCode,FTUsrSession,'
        SET @tSqlSale +=' FTBchCode,FTBchName,FDXshdocDate,FTPosCode,FTXshDocNoSale,FTXshDocNoRefun,FCXshAmt,FCXshVat,FCXshAmtNV,FCXshGrandTotal,'
        --*NUI 2019-11-14
        SET @tSqlSale +=' FNAppType,FTPosRegNo'
        -----------
        SET @tSqlSale +=' )'
        SET @tSqlSale +=' SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'	
        SET @tSqlSale +=' vAT.FTBchCode,Vat.FTBchName,Vat.FDXshdocDate,Vat.FTPosCode,ISNULL(FTXshDocNoSale,'''') AS FTXshDocNoSale,ISNULL(FTXshDocNoRefun,'''') AS FTXshDocNoRefun,FCXshValue,FCXshVat,FCXshAmtNV,FCXshGrand, FNAppType,FTPosRegNo'
        SET @tSqlSale +=' FROM('
        SET @tSqlSale +=' SELECT HD.FTBchCode,FTBchName,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,HD.FTPosCode,'
        SET @tSqlSale +=' SUM(CASE WHEN HD.FNXshDocType = ''1'' THEN ROUND(ISNULL(FCXshVatable,0)-ISNULL(FCXshAmtNV,0),2) ELSE ROUND((ISNULL(FCXshVatable,0)-ISNULL(FCXshAmtNV,0))*-1,2) END) AS FCXshValue, '
        SET @tSqlSale +=' SUM(CASE WHEN HD.FNXshDocType = ''1'' THEN ROUND(ISNULL(FCXshVat,0),2)	ELSE ROUND(ISNULL(FCXshVat,0)*-1,2) END) AS FCXshVat,'
        SET @tSqlSale +=' SUM(CASE WHEN HD.FNXshDocType = ''1'' THEN ROUND(ISNULL(FCXshAmtNV,0),2) ELSE ROUND(ISNULL(FCXshAmtNV,0)*-1,2) END) AS FCXshAmtNV,' 
        SET @tSqlSale +=' SUM(CASE WHEN HD.FNXshDocType = ''1'' THEN ROUND(ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0),2) ELSE ROUND((ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0))*-1,2) END) AS FCXshGrand,' 
        SET @tSqlSale +=' 1 AS FNAppType,POS.FTPosRegNo'
        SET @tSqlSale +=' FROM TPSTSalHD HD LEFT JOIN TCNMCst Cst ON HD.FTCstCode = Cst.FTCstCode' 
        SET @tSqlSale +=' LEFT JOIN TCNMPOS POS ON HD.FTPosCode = Pos.FTPosCode AND HD.FTBchCode = Pos.FTBchCode  LEFT JOIN' 
        SET @tSqlSale +=' TCNMCst_L Cst_L ON HD.FTCstCode = Cst_L.FTCstCode AND Cst_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''	 
        SET @tSqlSale +=' LEFT JOIN TCNMBranch_L Bch_L ON HD.FTBchCode = Bch_L.FTBchCode AND Bch_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
        SET @tSqlSale +=' LEFT JOIN TCNMShop Shp WITH (NOLOCK) ON HD.FTBchCode = Shp.FTBchCode AND HD.FTShpCode = Shp.FTShpCode '
        SET @tSqlSale += @tSql2	
        SET @tSqlSale +=' GROUP BY HD.FTBchCode,FTBchName,CONVERT(VARCHAR(10),FDXshDocDate,121),HD.FTPosCode,POS.FTPosRegNo'
        SET @tSqlSale +=' ) Vat LEFT JOIN'
            SET @tSqlSale +=' (SELECT HD.FTBchCode,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,HD.FTPosCode,'
            SET @tSqlSale +=' MIN(FTXshDocNo) + ''-'' + MAX(FTXshDocNo) AS FTXshDocNoSale'
            SET @tSqlSale +=' FROM TPSTSalHD HD LEFT JOIN TCNMCst Cst ON HD.FTCstCode = Cst.FTCstCode' 
            SET @tSqlSale +=' LEFT JOIN TCNMPOS POS ON HD.FTPosCode = Pos.FTPosCode AND HD.FTBchCode = Pos.FTBchCode  LEFT JOIN' 
            SET @tSqlSale +=' TCNMCst_L Cst_L ON HD.FTCstCode = Cst_L.FTCstCode AND Cst_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
            SET @tSqlSale +=' LEFT JOIN TCNMShop Shp WITH (NOLOCK) ON HD.FTBchCode = Shp.FTBchCode AND HD.FTShpCode = Shp.FTShpCode '
            SET @tSqlSale += @tSqlS
            SET @tSqlSale +=' GROUP BY HD.FTBchCode,CONVERT(VARCHAR(10),FDXshDocDate,121),HD.FTPosCode) Sale ON Vat.FTBchCode = Sale.FTBchCode AND Vat.FDXshdocDate = Sale.FDXshdocDate AND Vat.FTPosCode = Sale.FTPosCode '
            SET @tSqlSale +=' LEFT JOIN'
            SET @tSqlSale +=' (SELECT HD.FTBchCode,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,HD.FTPosCode,'
            SET @tSqlSale +=' MIN(FTXshDocNo) + ''-'' + MAX(FTXshDocNo) AS FTXshDocNoRefun'
            SET @tSqlSale +=' FROM TPSTSalHD HD LEFT JOIN TCNMCst Cst ON HD.FTCstCode = Cst.FTCstCode' 
            SET @tSqlSale +=' LEFT JOIN TCNMPOS POS ON HD.FTPosCode = Pos.FTPosCode AND HD.FTBchCode = Pos.FTBchCode  LEFT JOIN' 
            SET @tSqlSale +=' TCNMCst_L Cst_L ON HD.FTCstCode = Cst_L.FTCstCode AND Cst_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
            SET @tSqlSale +=' LEFT JOIN TCNMShop Shp WITH (NOLOCK) ON HD.FTBchCode = Shp.FTBchCode AND HD.FTShpCode = Shp.FTShpCode '
            SET @tSqlSale += @tSqlR
            SET @tSqlSale +=' GROUP BY HD.FTBchCode,CONVERT(VARCHAR(10),FDXshDocDate,121),HD.FTPosCode) Ret ON Vat.FTBchCode = Ret.FTBchCode AND Vat.FDXshdocDate = Ret.FDXshdocDate AND Vat.FTPosCode = Ret.FTPosCode'	
        -- PRINT @tSqlSale
        EXECUTE(@tSqlSale)

        ----Vending
        --SET @tSqlSale  = ' INSERT INTO TRPTPSTaxHDDateTmp '
        --SET @tSqlSale +=' ('
        --SET @tSqlSale +=' FTComName,FTRptCode,FTUsrSession,'
        --SET @tSqlSale +=' FTBchCode,FTBchName,FDXshdocDate,FTPosCode,FTXshDocNoSale,FTXshDocNoRefun,FCXshAmt,FCXshVat,FCXshAmtNV,FCXshGrandTotal,'
        ----*NUI 2019-11-14
        --SET @tSqlSale +=' FNAppType,FTPosRegNo'
        -------------
        --SET @tSqlSale +=' )'
        --SET @tSqlSale +=' SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'	
        --SET @tSqlSale +=' vAT.FTBchCode,Vat.FTBchName,Vat.FDXshdocDate,Vat.FTPosCode,ISNULL(FTXshDocNoSale,'''') AS FTXshDocNoSale,ISNULL(FTXshDocNoRefun,'''') AS FTXshDocNoRefun,FCXshValue,FCXshVat,FCXshAmtNV,FCXshGrand, FNAppType,FTPosRegNo'
        ----SET @tSqlSale +=' INTO  '+ @tTblName + ''
        --SET @tSqlSale +=' FROM('
        --SET @tSqlSale +=' SELECT HD.FTBchCode,FTBchName,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,HD.FTPosCode,'
        ----SET @tSqlSale +=' SUM(CASE WHEN HD.FNXshDocType = ''1'' THEN ISNULL(FCXshGrand,0)-ISNULL(FCXshAmtNV,0)-ISNULL(FCXshVat,0) ELSE (ISNULL(FCXshGrand,0)-ISNULL(FCXshAmtNV,0)-ISNULL(FCXshVat,0))*-1 END) AS FCXshValue, '
        --SET @tSqlSale +=' SUM(CASE WHEN HD.FNXshDocType = ''1'' THEN ROUND(ISNULL(FCXshVatable,0)-ISNULL(FCXshAmtNV,0),2) ELSE ROUND((ISNULL(FCXshVatable,0)-ISNULL(FCXshAmtNV,0))*-1,2) END) AS FCXshValue, '
        --SET @tSqlSale +=' SUM(CASE WHEN HD.FNXshDocType = ''1'' THEN ROUND(ISNULL(FCXshVat,0),2)	ELSE ROUND(ISNULL(FCXshVat,0)*-1,2) END) AS FCXshVat,'
        --SET @tSqlSale +=' SUM(CASE WHEN HD.FNXshDocType = ''1'' THEN ROUND(ISNULL(FCXshAmtNV,0),2) ELSE ROUND(ISNULL(FCXshAmtNV,0)*-1,2) END) AS FCXshAmtNV,' 
        --SET @tSqlSale +=' SUM(CASE WHEN HD.FNXshDocType = ''1'' THEN ROUND(ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0),2) ELSE ROUND((ISNULL(FCXshGrand,0)-ISNULL(FCXshRnd,0))*-1,2) END) AS FCXshGrand,' 
        --SET @tSqlSale +=' 2 AS FNAppType,POS.FTPosRegNo'--,Cst.FTCstBchCode,Cst.FTCstBusiness 
        --SET @tSqlSale +=' FROM TVDTSalHD HD LEFT JOIN TCNMCst Cst ON HD.FTCstCode = Cst.FTCstCode' 
        --SET @tSqlSale +=' LEFT JOIN TCNMPOS POS ON HD.FTPosCode = Pos.FTPosCode AND HD.FTBchCode = Pos.FTBchCode ' 
        ----NUI 2020-01-07
        --SET @tSqlSale +=' INNER JOIN TVDTSalRC RC WITH(NOLOCK) ON HD.FTBchCode = RC.FTBchCode AND HD.FTXshDocNo = RC.FTXshDocNo'
        --SET @tSqlSale +=' LEFT JOIN TFNMRcv Rcv WITH(NOLOCK) ON  RC.FTRcvCode = Rcv.FTRcvCode'			
        --------------
        --SET @tSqlSale +=' LEFT JOIN TCNMCst_L Cst_L ON HD.FTCstCode = Cst_L.FTCstCode AND Cst_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''	 
        --SET @tSqlSale +=' LEFT JOIN TCNMBranch_L Bch_L ON HD.FTBchCode = Bch_L.FTBchCode AND Bch_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
        --SET @tSqlSale +=' LEFT JOIN TCNMShop Shp WITH (NOLOCK) ON HD.FTBchCode = Shp.FTBchCode AND HD.FTShpCode = Shp.FTShpCode '
        --SET @tSqlSale += @tSql2	
        --SET @tSqlSale += ' AND Rcv.FTFmtCode <> ''004'''
        --SET @tSqlSale +=' GROUP BY HD.FTBchCode,FTBchName,CONVERT(VARCHAR(10),FDXshDocDate,121),HD.FTPosCode,POS.FTPosRegNo'
        --SET @tSqlSale +=' ) Vat LEFT JOIN'
        --    SET @tSqlSale +=' (SELECT HD.FTBchCode,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,HD.FTPosCode,'
        --    SET @tSqlSale +=' MIN(HD.FTXshDocNo) + ''-'' + MAX(HD.FTXshDocNo) AS FTXshDocNoSale'
        --    SET @tSqlSale +=' FROM TVDTSalHD HD LEFT JOIN TCNMCst Cst ON HD.FTCstCode = Cst.FTCstCode' 
        --    SET @tSqlSale +=' LEFT JOIN TCNMPOS POS ON HD.FTPosCode = Pos.FTPosCode AND HD.FTBchCode = Pos.FTBchCode ' 
        --    --NUI 2020-01-07
        --    SET @tSqlSale +=' INNER JOIN TVDTSalRC RC WITH(NOLOCK) ON HD.FTBchCode = RC.FTBchCode AND HD.FTXshDocNo = RC.FTXshDocNo'
        --    SET @tSqlSale +=' LEFT JOIN TFNMRcv Rcv WITH(NOLOCK) ON  RC.FTRcvCode = Rcv.FTRcvCode'			
        --    ------------
        --    SET @tSqlSale +=' LEFT JOIN TCNMCst_L Cst_L ON HD.FTCstCode = Cst_L.FTCstCode AND Cst_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
        --    SET @tSqlSale +=' LEFT JOIN TCNMShop Shp WITH (NOLOCK) ON HD.FTBchCode = Shp.FTBchCode AND HD.FTShpCode = Shp.FTShpCode '
        --    SET @tSqlSale += @tSqlS
        --    SET @tSqlSale += ' AND Rcv.FTFmtCode <> ''004'''
        --    SET @tSqlSale +=' GROUP BY HD.FTBchCode,CONVERT(VARCHAR(10),FDXshDocDate,121),HD.FTPosCode) Sale ON Vat.FTBchCode = Sale.FTBchCode AND Vat.FDXshdocDate = Sale.FDXshdocDate AND Vat.FTPosCode = Sale.FTPosCode '
        --    SET @tSqlSale +=' LEFT JOIN'
        --    SET @tSqlSale +=' (SELECT HD.FTBchCode,CONVERT(VARCHAR(10),FDXshDocDate,121) AS FDXshdocDate,HD.FTPosCode,'
        --    SET @tSqlSale +=' MIN(HD.FTXshDocNo) + ''-'' + MAX(HD.FTXshDocNo) AS FTXshDocNoRefun'
        --    SET @tSqlSale +=' FROM TVDTSalHD HD LEFT JOIN TCNMCst Cst ON HD.FTCstCode = Cst.FTCstCode' 
        --    SET @tSqlSale +=' LEFT JOIN TCNMPOS POS ON HD.FTPosCode = Pos.FTPosCode AND HD.FTBchCode = Pos.FTBchCode ' 
        --    --NUI 2020-01-07
        --    SET @tSqlSale +=' INNER JOIN TVDTSalRC RC WITH(NOLOCK) ON HD.FTBchCode = RC.FTBchCode AND HD.FTXshDocNo = RC.FTXshDocNo'
        --    SET @tSqlSale +=' LEFT JOIN TFNMRcv Rcv WITH(NOLOCK) ON  RC.FTRcvCode = Rcv.FTRcvCode'			
        --    ------------
        --    SET @tSqlSale +=' LEFT JOIN TCNMCst_L Cst_L ON HD.FTCstCode = Cst_L.FTCstCode AND Cst_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
        --    SET @tSqlSale +=' LEFT JOIN TCNMShop Shp WITH (NOLOCK) ON HD.FTBchCode = Shp.FTBchCode AND HD.FTShpCode = Shp.FTShpCode '
        --    SET @tSqlSale += @tSqlR
        --    SET @tSqlSale += ' AND Rcv.FTFmtCode <> ''004'''
        --    SET @tSqlSale +=' GROUP BY HD.FTBchCode,CONVERT(VARCHAR(10),FDXshDocDate,121),HD.FTPosCode) Ret ON Vat.FTBchCode = Ret.FTBchCode AND Vat.FDXshdocDate = Ret.FDXshdocDate AND Vat.FTPosCode = Ret.FTPosCode'	
        ----PRINT @tSqlSale
        --EXECUTE(@tSqlSale)

        RETURN SELECT * FROM TRPTPSTaxHDDateTmp WHERE FTComName = ''+ @nComName + '' AND FTRptCode = ''+ @tRptCode +'' AND FTUsrSession = '' + @tUsrSession 

        --SET @tSqlDrop += 'DROP TABLE '+ @tTblName + ''
        ----PRINT @tSqlDrop
        --EXECUTE(@tSqlDrop)

    END TRY

    BEGIN CATCH 
        SET @FNResult= -1
    END CATCH
GO

------------ Stroed 08.01.04 -------------
/****** Object:  StoredProcedure [dbo].[SP_RPTxServiceHis]    Script Date: 12/12/2565 11:24:26 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxServiceHis]') AND type in (N'P', N'PC'))
BEGIN
EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxServiceHis] AS' 
END
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
ALTER PROCEDURE [dbo].[SP_RPTxServiceHis]
	@ptSessionID VARCHAR(100),
	@ptAgnCode VARCHAR(5),
	@ptBchCode VARCHAR(255),
	@ptCstCodeFrm VARCHAR(30),
	@ptCstCodeTo VARCHAR(30),
	@ptCarRegCodeFrm  VARCHAR(20),
	@ptCarRegCodeTo  VARCHAR(20),
	@pdServiceDateFrm VARCHAR(20),
	@pdServiceDateTo VARCHAR(20),
	@pnResult INT 
AS
BEGIN TRY 

    DECLARE @nResult INT
	SET @nResult = @pnResult
	DECLARE @tSQL VARCHAR(MAX)
	SET @tSQL = ''

	DECLARE @tSQLFilter VARCHAR(1000)
	SET @tSQLFilter = ''

	--IF (@ptAgnCode <> '' OR @ptAgnCode <> NULL)
	--BEGIN
	--	 SET @tSQL += ' AND ISNULL(FTAgnCode,'''') = ''' + @ptAgnCode + ''' '
	--END

	IF (@ptBchCode <> '' OR @ptBchCode <> NULL)
	BEGIN
			SET @tSQLFilter += ' AND SHD.FTBchCode IN( ' +  @ptBchCode + ' ) '
	END

	IF ((@ptCstCodeFrm <> '' OR @ptCstCodeFrm <> NULL) AND  (@ptCstCodeTo <> '' OR @ptCstCodeTo <> NULL))
	BEGIN
			SET @tSQLFilter += ' AND (SHD.FTCstCode  BETWEEN ''' + @ptCstCodeFrm + ''' AND ''' + @ptCstCodeTo + ''' OR SHD.FTCstCode  BETWEEN ''' + @ptCstCodeTo + ''' AND ''' + @ptCstCodeFrm + ''') '
	END

	IF ((@ptCarRegCodeFrm <> '' OR @ptCarRegCodeFrm <> NULL) AND  (@ptCarRegCodeTo <> '' OR @ptCarRegCodeTo <> NULL))
	BEGIN
			--SET @tSQLFilter += ' AND CAR.FTCarRegNo BETWEEN ''' + @ptCarRegCodeFrm + ''' AND ''' + @ptCarRegCodeTo + ''' '
			SET @tSQLFilter += ' AND (CAR.FTCarCode BETWEEN ''' + @ptCarRegCodeFrm + ''' AND ''' + @ptCarRegCodeTo + ''' OR CAR.FTCarCode BETWEEN ''' + @ptCarRegCodeTo + ''' AND ''' + @ptCarRegCodeFrm + ''') '
	END
		
	IF ((@pdServiceDateFrm <> '' OR @pdServiceDateFrm <> NULL) AND  (@pdServiceDateTo <> '' OR @pdServiceDateTo <> NULL))
	BEGIN
			SET @tSQLFilter += ' AND CONVERT(VARCHAR(10),SHD.FDXshDocDate,121) BETWEEN ''' + @pdServiceDateFrm + ''' AND ''' + @pdServiceDateTo + ''' '
	END

    DELETE FROM TRPTSVServiceHisTmp WHERE FTUsrSession = ''+@ptSessionID+''

    SET @tSQL += ' INSERT INTO TRPTSVServiceHisTmp '
    SET @tSQL += ' SELECT LIST.* , BCH.FTBchName, '''+@ptSessionID+''' AS FTUsrSession, NULL AS FNRowPartID '
	SET @tSQL += ' FROM '
	SET @tSQL += ' ( '
		SET @tSQL += ' SELECT HD.* '
		SET @tSQL += ' FROM '
		SET @tSQL += ' ( '
			SET @tSQL += ' SELECT  '
				  SET @tSQL +=' ''1'' AS FNTxnType, ' 
				  SET @tSQL += ' SHD.FTBchCode, '
				  SET @tSQL += ' SHD.FTCstCode, '
				  SET @tSQL += ' HCS.FTXshCstName, '
				  SET @tSQL += ' HCS.FTXshCstTel, '
				  SET @tSQL += ' ADDR.FTAddV1No, '
				  SET @tSQL += ' ISNULL(ADDR.FTAddV2Desc1,'''') + '''' + ISNULL(ADDR.FTAddV2Desc2,'''') + '''' + ISNULL(FTAddV1PostCode,'''') AS FTCstAddress, '
				  SET @tSQL += ' HCS.FTXshCstEmail, '
				  SET @tSQL += ' LMS.FTTxnCrdCode AS FTXshCardNo, '
				  SET @tSQL += ' CAR.FTCarRegNo, '
				  SET @tSQL += ' BRAND_L.FTCaiName AS FTCarBrand, '
				  SET @tSQL += ' Model_L.FTCaiName AS FTCarModel, '
				  SET @tSQL += ' SHD.FTXshDocNo, '
				  SET @tSQL += ' SHD.FDXshDocDate, '
				  SET @tSQL += ' '''' AS FTPdtName, '
				  SET @tSQL += ' SRC.FTRcvCode, '
				  SET @tSQL += ' SRC.FTRcvName,  '
				  SET @tSQL += ' CAST(SRC.FCXrcNet AS NUMERIC(18, 2)) AS FCXrcNet, '
				  SET @tSQL += ' NULL AS FDFlwDateForcast, '
				  SET @tSQL += ' NULL AS FDFlwLastDate, '
				  SET @tSQL += ' SHD.FTXshRefInt , '
			      SET @tSQL += ' SHD.FTPosCode, '
				  SET @tSQL += ' '''' AS FTPdtCode '
			SET @tSQL += ' FROM TPSTSalHD SHD WITH(NOLOCK) '
				 SET @tSQL += ' LEFT JOIN TPSTSalRC SRC WITH(NOLOCK) ON SHD.FTBchCode = SRC.FTBchCode '
														 SET @tSQL += ' AND SHD.FTXshDocNo = SRC.FTXshDocNo '


				SET @tSQL += ' LEFT JOIN ( SELECT DISTINCT FTTxnCrdCode , FTXshDocNo '
								SET @tSQL += ' FROM   TLKTLmsTxnHD ' 
								SET @tSQL += ' WHERE  LEFT(FTXshDocNo,1)=''S'' '
								SET @tSQL += ' AND    FTTxnType IN (1,2) ) LMS ON SHD.FTXshDocNo = LMS.FTXshDocNo '

				 SET @tSQL += ' LEFT JOIN TPSTSalHDCst HCS WITH(NOLOCK) ON SHD.FTBchCode = HCS.FTBchCode '
															SET @tSQL += ' AND SHD.FTXshDocNo = HCS.FTXshDocNo '

				 SET @tSQL += ' LEFT JOIN TCNMCstAddress_L ADDR WITH(NOLOCK) ON HCS.FNXshAddrShip = ADDR.FNAddSeqNo '

				 SET @tSQL += ' LEFT JOIN TSVMCar CAR WITH(NOLOCK) ON HCS.FTCarCode = CAR.FTCarCode '
				 SET @tSQL += ' LEFT JOIN TSVMCarInfo BRAND WITH(NOLOCK) ON CAR.FTCarBrand = BRAND.FTCaiCode '
															 SET @tSQL += ' AND BRAND.FTCaiType = 2 '
				 SET @tSQL += ' LEFT JOIN TSVMCarInfo_L BRAND_L WITH(NOLOCK) ON BRAND.FTCaiCode = BRAND_L.FTCaiCode '
																 SET @tSQL += ' AND BRAND.FTCaiType = 2 '
				 SET @tSQL += ' LEFT JOIN TSVMCarInfo Model WITH(NOLOCK) ON CAR.FTCarModel = Model.FTCaiCode '
															 SET @tSQL += ' AND Model.FTCaiType = 3 '
				 SET @tSQL += ' LEFT JOIN TSVMCarInfo_L Model_L WITH(NOLOCK) ON Model.FTCaiCode = Model_L.FTCaiCode '
																 SET @tSQL += ' AND Model.FTCaiType = 3 '
			SET @tSQL += ' WHERE SHD.FTXshStaDoc = 1 '
			SET @tSQL += ' AND ISNULL(SHD.FTXshRefInt, '''') = '''' '

			SET @tSQL += @tSQLFilter

		SET @tSQL += ' ) HD '
		SET @tSQL += ' UNION ALL '
		SET @tSQL += ' SELECT DT.* '
		SET @tSQL += ' FROM '
		SET @tSQL += ' ( '
			SET @tSQL += ' SELECT  '
				  SET @tSQL += ' ''2'' AS FNTxnType,  '
				   SET @tSQL += ' SHD.FTBchCode, '
				   SET @tSQL += ' SHD.FTCstCode, '
				   SET @tSQL += ' '''' AS FTXshCstName, '
				   SET @tSQL += ' '''' AS FTXshCstTel,  '
				   SET @tSQL += ' '''' AS FTAddV1No,  '
				   SET @tSQL += ' '''' AS FTCstAddress, ' 
				   SET @tSQL += ' '''' AS FTXshCstEmail,  '
				   SET @tSQL += ' '''' AS FTXshCardNo, '
				   SET @tSQL += ' '''' AS FTCarRegNo, '
				   SET @tSQL += ' '''' AS FTCarBrand,  '
				   SET @tSQL += ' '''' AS FTCarModel, '
				   SET @tSQL += ' SHD.FTXshDocNo, '
				   SET @tSQL += ' SHD.FDXshDocDate,  '
				   SET @tSQL += ' SDT.FTXsdPdtName,  '
				   SET @tSQL += ' '''' AS FTRcvCode,  '
				   SET @tSQL += ' '''' AS FTRcvName, '
				   SET @tSQL += ' 0 AS FCXrcNet, '
				   SET @tSQL += ' FLW.FDFlwDateForcast, '
				   SET @tSQL += ' FLW.FDFlwLastDate, '
				   SET @tSQL += ' SHD.FTXshRefInt , '
				   SET @tSQL += ' '''' AS  FTPosCode, '
				   SET @tSQL += ' SDT.FTPdtCode '
			  
			SET @tSQL += ' FROM TPSTSalDT SDT WITH(NOLOCK) '
				 SET @tSQL += ' LEFT JOIN TPSTSalHD SHD WITH(NOLOCK) ON SHD.FTBchCode = SDT.FTBchCode '
														 SET @tSQL += ' AND SHD.FTXshDocNo = SDT.FTXshDocNo '
				 SET @tSQL += ' LEFT JOIN TPSTSalHDCst HDC WITH(NOLOCK) ON SHD.FTBchCode = HDC.FTBchCode '
															SET @tSQL += ' AND SHD.FTXshDocNo = HDC.FTXshDocNo '
				 SET @tSQL += ' LEFT JOIN TSVTCstFollow FLW ON HDC.FTCarCode = FLW.FTCarCode '
												SET @tSQL += ' AND SDT.FTPdtCode = FLW.FTPdtCode '
				 SET @tSQL += ' LEFT JOIN TSVMCar CAR WITH(NOLOCK) ON HDC.FTCarCode = CAR.FTCarCode '

			SET @tSQL += ' WHERE SHD.FTXshStaDoc = 1 '
			SET @tSQL += ' AND ISNULL(SHD.FTXshRefInt, '''') = '''' '

			SET @tSQL += @tSQLFilter

		SET @tSQL += ' ) DT '
	SET @tSQL += ' ) LIST '
	SET @tSQL +=' LEFT JOIN TCNMBranch_L BCH WITH(NOLOCK) ON LIST.FTBchCode = BCH.FTBchCode AND BCH.FNLngID = 1 '
	SET @tSQL += ' ORDER BY LIST.FTCstCode, '
			 SET @tSQL += ' LIST.FTXshDocNo, '
			 SET @tSQL += ' LIST.FNTxnType,  '
			 SET @tSQL +=' LIST.FTXshCstName DESC '
	--PRINT(@tSQL)
	EXEC (@tSQL)

	RETURN 0
 
END TRY	
BEGIN CATCH
	SET @nResult = -1
	RETURN @nResult
END CATCH
GO


------------ Stroed 08.01.05 -------------
/****** Object:  StoredProcedure [dbo].[SP_RPTxDailySaleByPdt1001002]    Script Date: 13/12/2565 15:54:42 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxDailySaleByPdt1001002]') AND type in (N'P', N'PC'))
BEGIN
EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxDailySaleByPdt1001002] AS' 
END
GO
ALTER PROCEDURE [dbo].[SP_RPTxDailySaleByPdt1001002] 
--ALTER PROCEDURE [dbo].[SP_RPTxDailySaleByPdt1001002] 
	@pnLngID int , 
	@pnComName Varchar(100),
	@ptRptCode Varchar(100),
	@ptUsrSession Varchar(255),
	@pnFilterType int, --1 BETWEEN 2 IN

	--สาขา
	@ptBchL Varchar(8000), --กรณี Condition IN
	@ptBchF Varchar(5),
	@ptBchT Varchar(5),
	--Merchant
	@ptMerL Varchar(8000), --กรณี Condition IN
	@ptMerF Varchar(10),
	@ptMerT Varchar(10),
	--Shop Code
	@ptShpL Varchar(8000), --กรณี Condition IN
	@ptShpF Varchar(10),
	@ptShpT Varchar(10),
	--เครื่องจุดขาย
	@ptPosL Varchar(8000), --กรณี Condition IN
	@ptPosF Varchar(20),
	@ptPosT Varchar(20),

	@ptPdtCodeF Varchar(20),
	@ptPdtCodeT Varchar(20),
	@ptPdtChanF Varchar(30),
	@ptPdtChanT Varchar(30),
	@ptPdtTypeF Varchar(5),
	@ptPdtTypeT Varchar(5),

	@ptDocDateF Varchar(10),
	@ptDocDateT Varchar(10),

	@ptCate1F VARCHAR(100),
	@ptCate2F VARCHAR(100),

	@FNResult INT OUTPUT 
AS

--------------------------------------
BEGIN TRY

	DECLARE @nLngID int 
	DECLARE @nComName Varchar(100)
	DECLARE @tRptCode Varchar(100)
	DECLARE @tUsrSession Varchar(255)
	DECLARE @tSql VARCHAR(8000)
	DECLARE @tSqlIns VARCHAR(8000)
	DECLARE @tSql1 nVARCHAR(Max)
	DECLARE @tSql2 VARCHAR(8000)

	--Branch Code
	DECLARE @tBchF Varchar(5)
	DECLARE @tBchT Varchar(5)
	--Merchant
	DECLARE @tMerF Varchar(10)
	DECLARE @tMerT Varchar(10)
	--Shop Code
	DECLARE @tShpF Varchar(10)
	DECLARE @tShpT Varchar(10)
	--Pos Code
	DECLARE @tPosF Varchar(20)
	DECLARE @tPosT Varchar(20)

	DECLARE @tPdtCodeF Varchar(20)
	DECLARE @tPdtCodeT Varchar(20)
	DECLARE @tPdtChanF Varchar(30)
	DECLARE @tPdtChanT Varchar(30)
	DECLARE @tPdtTypeF Varchar(5)
	DECLARE @tPdtTypeT Varchar(5)

	DECLARE @tDocDateF Varchar(10)
	DECLARE @tDocDateT Varchar(10)

	DECLARE @tCate1F VARCHAR(100)
	DECLARE @tCate2F VARCHAR(100)

	SET @nLngID = @pnLngID
	SET @nComName = @pnComName
	SET @tUsrSession = @ptUsrSession
	SET @tRptCode = @ptRptCode

	--Branch
	SET @tBchF  = @ptBchF
	SET @tBchT  = @ptBchT
	--Merchant
	SET @tMerF  = @ptMerF
	SET @tMerT  = @ptMerT
	--Shop
	SET @tShpF  = @ptShpF
	SET @tShpT  = @ptShpT
	--Pos
	SET @tPosF  = @ptPosF 
	SET @tPosT  = @ptPosT

	SET @tPdtCodeF  = @ptPdtCodeF 
	SET @tPdtCodeT = @ptPdtCodeT
	SET @tPdtChanF = @ptPdtChanF
	SET @tPdtChanT = @ptPdtChanT 
	SET @tPdtTypeF = @ptPdtTypeF
	SET @tPdtTypeT = @ptPdtTypeT

	SET @tDocDateF = @ptDocDateF
	SET @tDocDateT = @ptDocDateT

	SET @tCate1F  = @ptCate1F
	SET @tCate2F = @ptCate2F

	SET @FNResult= 0

	SET @tDocDateF = CONVERT(VARCHAR(10),@tDocDateF,121)
	SET @tDocDateT = CONVERT(VARCHAR(10),@tDocDateT,121)

	IF @nLngID = null
	BEGIN
		SET @nLngID = 1
	END	
	--Set ค่าให้ Paraleter กรณี T เป็นค่าว่างหรือ null


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

	IF @ptMerL =null
	BEGIN
		SET @ptMerL = ''
	END

	IF @tMerF =null
	BEGIN
		SET @tMerF = ''
	END
	IF @tMerT =null OR @tMerT = ''
	BEGIN
		SET @tMerT = @tMerF
	END 

	IF @ptShpL =null
	BEGIN
		SET @ptShpL = ''
	END

	IF @tShpF =null
	BEGIN
		SET @tShpF = ''
	END
	IF @tShpT =null OR @tShpT = ''
	BEGIN
		SET @tShpT = @tShpF
	END

	IF @ptPosL =null
	BEGIN
		SET @ptPosL = ''
	END

	IF @tPosF = null
	BEGIN
		SET @tPosF = ''
	END
	IF @tPosT = null OR @tPosT = ''
	BEGIN
		SET @tPosT = @tPosF
	END

	IF @tPdtCodeF = null
	BEGIN
		SET @tPdtCodeF = ''
	END 
	IF @tPdtCodeT = null OR @tPdtCodeT =''
	BEGIN
		SET @tPdtCodeT = @tPdtCodeF
	END 

	IF @tPdtChanF = null
	BEGIN
		SET @tPdtChanF = ''
	END 
	IF @tPdtChanT = null OR @tPdtChanT =''
	BEGIN
		SET @tPdtChanT = @tPdtChanF
	END 

	IF @tPdtTypeF = null
	BEGIN
		SET @tPdtTypeF = ''
	END 
	IF @tPdtTypeT = null OR @tPdtTypeT =''
	BEGIN
		SET @tPdtTypeT = @tPdtTypeF
	END 

	IF @tDocDateF = null
	BEGIN 
		SET @tDocDateF = ''
	END

	IF @tDocDateT = null OR @tDocDateT =''
	BEGIN 
		SET @tDocDateT = @tDocDateF
	END

	SET @tSql1 =   ' WHERE 1=1 AND FTXshStaDoc = ''1'' AND DT.FTXsdStaPdt <> ''4'''

	IF @pnFilterType = '1'
	BEGIN
		IF (@tBchF <> '' AND @tBchT <> '')
		BEGIN
			SET @tSql1 +=' AND DT.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
		END

		IF (@tMerF <> '' AND @tMerT <> '')
		BEGIN
			SET @tSql1 +=' AND Shp.FTMerCode BETWEEN ''' + @tMerF + ''' AND ''' + @tMerT + ''''
		END

		IF (@tShpF <> '' AND @tShpT <> '')
		BEGIN
			SET @tSql1 +=' AND HD.FTShpCode BETWEEN ''' + @tShpF + ''' AND ''' + @tShpT + ''''
		END

		IF (@tPosF <> '' AND @tPosT <> '')
		BEGIN
			SET @tSql1 += ' AND HD.FTPosCode BETWEEN ''' + @tPosF + ''' AND ''' + @tPosT + ''''
		END		
	END

	IF @pnFilterType = '2'
	BEGIN
		IF (@ptBchL <> '' )
		BEGIN
			SET @tSql1 +=' AND DT.FTBchCode IN (' + @ptBchL + ')'
		END

		IF (@ptMerL <> '' )
		BEGIN
			SET @tSql1 +=' AND Shp.FTMerCode IN (' + @ptMerL + ')'
		END

		IF (@ptShpL <> '')
		BEGIN
			SET @tSql1 +=' AND HD.FTShpCode IN (' + @ptShpL + ')'
		END

		IF (@ptPosL <> '')
		BEGIN
			SET @tSql1 += ' AND HD.FTPosCode IN (' + @ptPosL + ')'
		END		
	END

	IF (@tPdtCodeF <> '' AND @tPdtCodeT <> '')
	BEGIN
		SET @tSql1 +=' AND Pdt.FTPdtCode BETWEEN ''' + @tPdtCodeF + ''' AND ''' + @tPdtCodeT + ''''
	END

	IF (@tPdtChanF <> '' AND @tPdtChanT <> '')
	BEGIN
		SET @tSql1 +=' AND Pdt.FTPgpChain BETWEEN ''' + @tPdtChanF + ''' AND ''' + @tPdtChanT + ''''
	END

	IF (@tPdtTypeF <> '' AND @tPdtTypeT <> '')
	BEGIN
		SET @tSql1 +=' AND Pdt.FTPtyCode BETWEEN ''' + @tPdtTypeF + ''' AND ''' + @tPdtTypeT + ''''
	END

	IF (@tDocDateF <> '' AND @tDocDateT <> '')
	BEGIN
		SET @tSql1 +=' AND CONVERT(VARCHAR(10),FDXshDocDate,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''''
	END

	IF(@tCate1F <> '')
	BEGIN 
			SET @tSql1 += ' AND PDTCAT.FTPdtCat1 IN ('+@tCate1F+') ' 
	END
			
	IF(@tCate2F <> '')
	BEGIN 
			SET @tSql1 += ' AND PDTCAT.FTPdtCat2 IN ('+@tCate2F+') ' 
	END

	DELETE FROM TRPTSalDTTmp WITH (ROWLOCK) WHERE FTComName =  '' + @nComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''--ลบข้อมูล Temp ของเครื่องที่จะบันทึกขอมูลลง Temp

  SET @tSql  = ' INSERT INTO TRPTSalDTTmp '
	SET @tSql +=' (FTComName,FTRptCode,FTUsrSession,'
	SET @tSql +=' FNAppType,FTBchCode,FTBchName,FTPdtCode,FTXsdPdtName,FTPgpChainName,FTPunName,FCXsdQty,FCXsdSetPrice,FCXsdAmtB4DisChg,FCXsdDis,FCXsdVat,FCXsdNet,FCXsdNetAfHD,FTPdtCatName1,FTPdtCatName2,FTPdtCatCode1,FTPdtCatCode2'
	SET @tSql +=' )'
	SET @tSql +=' SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'
	SET @tSql +=' 1 AS FNAppType,FTBchCode,FTBchName,FTPdtCode,FTPdtName,FTPgpChainName,FTPunName,'
	SET @tSql +=' SUM(FCXsdQty) AS FCXsdQty,'
  SET @tSql +=' CASE WHEN SUM(FCXsdQty) = 0 THEN SUM(FCXsdNetAfHD) ELSE SUM(FCXsdNetAfHD)/SUM(FCXsdQty) END FCXsdSetPrice,'
	SET @tSql +=' SUM(FCXsdAmtB4DisChg) AS FCXsdAmtB4DisChg,'
	SET @tSql +=' SUM(FCXsdDis) AS FCXsdDis ,'
	SET @tSql +=' SUM(FCXsdVat) AS FCXsdVat,'
	SET @tSql +=' SUM(FCXsdNet) AS FCXsdNet,'
	SET @tSql +=' SUM(FCXsdNetAfHD) AS FCXsdNetAfHD,'
	SET @tSql +=' FTCatName1,FTCatName2,FTCatCode1,FTCatCode2'
	SET @tSql +=' FROM'
		SET @tSql +=' (SELECT 
										PDTCATL1.FTCatName AS FTCatName1,
										PDTCATL2.FTCatName AS FTCatName2, 
										PDTCAT.FTPdtCat1	 AS FTCatCode1,
										PDTCAT.FTPdtCat2 	 AS FTCatCode2,
									  DT.FTXshDocNo,HD.FDXshDocDate,HD.FTBchCode,Bch_L.FTBchName,DT.FTPdtCode,Pdt_L.FTPdtName,Chan_L.FTPgpChainName,ISNULL(Pun_L.FTPunName,'''') AS FTPunName,'
		SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN  ISNULL(DT.FCXsdQty,0) ELSE ISNULL(DT.FCXsdQty,0)*-1 END FCXsdQty,'
		SET @tSql +=' ISNULL(DT.FCXsdSetPrice,0) AS FCXsdSetPrice,'
		SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN  ISNULL(DT. FCXsdAmtB4DisChg,0) ELSE (ISNULL(DT. FCXsdAmtB4DisChg,0))*-1 END AS FCXsdAmtB4DisChg,'
		SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN  ISNULL(DTDis.FCXddValue, 0) ELSE (ISNULL(DTDis.FCXddValue, 0))*-1 END FCXsdDis,'
		SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN  ISNULL(DT.FCXsdVat,0) ELSE ISNULL(DT.FCXsdVat,0)*-1 END FCXsdVat,'
		SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN  ISNULL(DT.FCXsdNet,0) ELSE ISNULL(DT.FCXsdNet,0)*-1 END FCXsdNet,'
		SET @tSql +=' CASE WHEN HD.FNXshDocType = 1 THEN  ISNULL(DT.FCXsdNetAfHD,0) ELSE ISNULL(DT.FCXsdNetAfHD,0)*-1 END FCXsdNetAfHD,'
		SET @tSql +=' HD.FNXshDocType'
		SET @tSql +=' FROM TPSTSalDT DT INNER JOIN TPSTSalHD HD ON DT.FTBchCode = HD.FTBchCode AND DT.FTXshDocNo = HD.FTXshDocNo AND DT.FCXsdQty > 0 LEFT JOIN'
		SET @tSql +=' ('
			SET @tSql +=' SELECT FTBchCode,FTXshDocNo,FNXsdSeqNo,'
			SET @tSql +=' SUM (CASE WHEN FTXddDisChgType = 3 OR FTXddDisChgType = 4 THEN ISNULL(FCXddValue, 0) ELSE ISNULL(FCXddValue, 0)*-1 END) AS FCXddValue'
			SET @tSql +=' FROM TPSTSalDTDis GROUP BY FTBchCode,FTXshDocNo,FNXsdSeqNo'
		SET @tSql +=' ) AS DTDis ON DT.FTBchCode = DTDis.FTBchCode AND DT.FTXshDocNo = DTDis.FTXshDocNo AND DT.FNXsdSeqNo = DTDis.FNXsdSeqNo LEFT JOIN'
		SET @tSql +=' TCNMPdt Pdt WITH (NOLOCK) ON DT.FTPdtCode = Pdt.FTPdtCode LEFT JOIN'
		SET @tSql +=' TCNMPdt_L Pdt_L WITH (NOLOCK) ON DT.FTPdtCode = Pdt_L.FTPdtCode AND Pdt_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' LEFT JOIN'
		SET @tSql +=' TCNMPdtUnit_L Pun_L WITH (NOLOCK) ON DT.FTPunCode = Pun_L.FTPunCode AND  Pun_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' LEFT JOIN'
		SET @tSql +=' TCNMPdtGrp_L Chan_L WITH (NOLOCK) ON Pdt.FTPgpChain = Chan_L.FTPgpChain AND Chan_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
		SET @tSql +=' LEFT JOIN TCNMBranch_L Bch_L WITH (NOLOCK) ON  HD.FTBchCode = Bch_L.FTBchCode AND Bch_L.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''''
		SET @tSql +='	LEFT JOIN TCNMPdtCategory  PDTCAT 	WITH (NOLOCK) ON  Pdt.FTPdtCode = PDTCAT.FTPdtCode
									LEFT JOIN TCNMPdtCatInfo   CATINFO1 WITH (NOLOCK) ON  PDTCAT.FTPdtCat1 = CATINFO1.FTCatCode
									LEFT JOIN TCNMPdtCatInfo_L PDTCATL1 WITH (NOLOCK) ON  CATINFO1.FTCatCode = PDTCATL1.FTCatCode AND CATINFO1.FNCatLevel = PDTCATL1.FNCatLevel AND PDTCATL1.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + '''
									LEFT JOIN TCNMPdtCatInfo   CATINFO2 WITH (NOLOCK) ON  PDTCAT.FTPdtCat2 = CATINFO2.FTCatCode
									LEFT JOIN TCNMPdtCatInfo_L PDTCATL2 WITH (NOLOCK) ON  CATINFO2.FTCatCode = PDTCATL2.FTCatCode AND CATINFO2.FNCatLevel = PDTCATL2.FNCatLevel AND PDTCATL2.FNLngID = '''  + CAST(@nLngID  AS VARCHAR(10)) + ''' '
		SET @tSql +=' LEFT JOIN TCNMShop Shp WITH (NOLOCK) ON HD.FTBchCode = Shp.FTBchCode AND HD.FTShpCode = Shp.FTShpCode ' 
		SET @tSql += @tSql1			
		SET @tSql +=' ) SalePdt'

	SET @tSql +=' GROUP BY FTBchCode,FTBchName,FTCatCode1,FTCatCode2,FTPdtCode,FTPdtName,FTPgpChainName,FTPunName,FTCatName1,FTCatName2,FNXshDocType'

	 --PRINT @tSql

	-- SELECT @tSql

	EXECUTE(@tSql)
	 --RETURN SELECT * FROM TRPTSalDTTmp WHERE FTComName = ''+ @nComName + '' AND FTRptCode = ''+ @tRptCode +'' AND FTUsrSession = '' + @tUsrSession + ''
	
END TRY

BEGIN CATCH 
	SET @FNResult= -1
END CATCH	
GO



/****** Object:  StoredProcedure [dbo].[SP_RPTxTrfpmtinf]    Script Date: 13/12/2565 15:56:58 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SP_RPTxTrfpmtinf]') AND type in (N'P', N'PC'))
BEGIN
EXEC dbo.sp_executesql @statement = N'CREATE PROCEDURE [dbo].[SP_RPTxTrfpmtinf] AS' 
END
GO
-- =============================================
-- Author		: รายงาน - ข้อมูลจ่ายโอนรับโอน
-- Create date	: 31/08/2022 Wasin
-- =============================================
ALTER PROCEDURE [dbo].[SP_RPTxTrfpmtinf]
	@pnLngID		int , 
	@pnComName		Varchar(100),
	@ptRptCode		Varchar(100),
	@ptUsrSession	Varchar(255),
	@pnFilterType	int, --1 BETWEEN 2 IN
	-- สาขา
	@ptBchL	Varchar(8000),
	-- สินค้า
	@ptPdtF Varchar(20),
	@ptPdtT Varchar(20),

	@ptPdtChanF Varchar(30),
	@ptPdtChanT Varchar(30),
	-- ประเภทสินค้า
	@ptPdtTypeF Varchar(5),
	@ptPdtTypeT Varchar(5),
	-- หมวดหมู่
	@ptCate1F VARCHAR(100),
	@ptCate2F VARCHAR(100),
	-- วันที่โออนออก
	@ptDocDateF Varchar(10), 
	@ptDocDateT Varchar(10), 
	-- Return 
	@FNResult INT OUTPUT 
AS
BEGIN TRY
	DECLARE @nLngID			int 
	DECLARE @nComName		Varchar(100)
	DECLARE @tRptCode		Varchar(100)
	DECLARE @tUsrSession	Varchar(255)
	DECLARE @tSql			VARCHAR(8000)
	DECLARE @tSql1			VARCHAR(8000)
	-- สาขา
	DECLARE @tBchF			Varchar(5)
	DECLARE @tBchT			Varchar(5)
	-- สินค้า
	DECLARE @tPdtF			Varchar(20)
	DECLARE @tPdtT			Varchar(20)	

	DECLARE @tPdtChanF		Varchar(30)
	DECLARE @tPdtChanT		Varchar(30)
	-- ประเภทสินค้า
	DECLARE @tPdtTypeF		Varchar(5)
	DECLARE @tPdtTypeT		Varchar(5)
	-- หมวดหมู่
	DECLARE @tCate1F		VARCHAR(100)
	DECLARE @tCate2F		VARCHAR(100)

	SET @nLngID			= @pnLngID
	SET @nComName		= @pnComName
	SET @tUsrSession	= @ptUsrSession
	SET @tRptCode		= @ptRptCode
	SET @tPdtF			= @ptPdtF
	SET @tPdtT			= @ptPdtT
	SET @tPdtChanF		= @ptPdtChanF
	SET @tPdtChanT		= @ptPdtChanT 
	SET @tPdtTypeF		= @ptPdtTypeF
	SET @tPdtTypeT		= @ptPdtTypeT
	SET @tCate1F  		= @ptCate1F
	SET @tCate2F 	    = @ptCate2F
	SET @ptDocDateF		= CONVERT(VARCHAR(10),@ptDocDateF,121)
	SET @ptDocDateT		= CONVERT(VARCHAR(10),@ptDocDateT,121)
	SET @FNResult		= 0

	-- Check Lang 
	IF @nLngID = null
	BEGIN
		SET @nLngID = 1
	END	

	-- Check Branch
	IF @ptBchL	= null
	BEGIN
		SET @ptBchL = ''
	END

	-- Check Product
	IF @tPdtF = null
	BEGIN
		SET @tPdtF = ''
	END 
	IF @tPdtT = null OR @tPdtT =''
	BEGIN
		SET @tPdtT = @tPdtF
	END

	IF @tPdtChanF = null
	BEGIN
		SET @tPdtChanF = ''
	END 
	IF @tPdtChanT = null OR @tPdtChanT =''
	BEGIN
		SET @tPdtChanT = @tPdtChanF
	END 

	IF @tPdtTypeF = null
	BEGIN
		SET @tPdtTypeF = ''
	END 
	IF @tPdtTypeT = null OR @tPdtTypeT =''
	BEGIN
		SET @tPdtTypeT = @tPdtTypeF
	END 

	SET @tSql1	= ''
	SET @tSql1 += ' WHERE TBO.FTXthStaApv = ''1'' AND TBI.FTXthStaApv = ''1'' AND BchFrm_L.FNLngID = ''1'' AND BchTo_L.FNLngID = ''1'' '
	IF @pnFilterType = '2'
	BEGIN
		IF (@ptBchL <> '' )
		BEGIN
			SET @tSql1	+=' AND (BchFrm_L.FTBchCode IN (' + @ptBchL + ') OR BchTo_L.FTBchCode IN (' + @ptBchL + '))'
		END	
	END

	-- Check Where DocDate
	IF (@ptDocDateF <> '' AND @ptDocDateT <> '')
	BEGIN
		SET @tSql1 +=' AND ((CONVERT(VARCHAR(10),TBO.FDXthDocDate,121) BETWEEN ''' + @ptDocDateF + ''' AND ''' + @ptDocDateT + ''') OR  (CONVERT(VARCHAR(10),TBI.FDXthDocDate,121) BETWEEN ''' + @ptDocDateF + ''' AND ''' + @ptDocDateT + '''))'
	END

	-- Check Where Product
	IF (@tPdtF <> '' AND @tPdtT <> '')
	BEGIN
		SET @tSql1 +=' AND INB.FTPdtCode BETWEEN ''' + @tPdtF + ''' AND ''' + @tPdtT + ''''
	END

	IF (@tPdtChanF <> '' AND @tPdtChanT <> '')
	BEGIN
		SET @tSql1 +=' AND Pdt.FTPgpChain BETWEEN ''' + @tPdtChanF + ''' AND ''' + @tPdtChanT + ''''
	END

	IF (@tPdtTypeF <> '' AND @tPdtTypeT <> '')
	BEGIN
		SET @tSql1 +=' AND PDT.FTPtyCode BETWEEN ''' + @tPdtTypeF + ''' AND ''' + @tPdtTypeT + ''''
	END

	IF(@tCate1F <> '')
	BEGIN 
			SET @tSql1 += ' AND PCAT1.FTCatCode IN ('+@tCate1F+') ' 
	END
				
	IF(@tCate2F <> '')
	BEGIN 
			SET @tSql1 += ' AND PCAT2.FTCatCode IN ('+@tCate2F+') ' 
	END


	DELETE FROM TRPTTrfpmtinfTmp WITH (ROWLOCK) WHERE FTComName =  '' + @nComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''

	SET @tSql	= '	INSERT INTO TRPTTrfpmtinfTmp ('
	SET @tSql  += '		FTComName,FTRptCode,FTUsrSession,'
	SET @tSql  += '		FTBchRefIDFrm,FTBchCodeFrm,FTBchNameFrm,FTXthDocNoFrm,FDXthDocDateFrm,'
	SET @tSql  += '		FTBchRefIDTo,FTBchCodeTo,FTBchNameTo,FTXthDocNoTo,FDXthDocDateTo,'
	SET @tSql  += '		FTPdtCode,FTXtdPdtName,'
	SET @tSql  += '		FTPunCode,FTPunName,'
	SET @tSql  += '		FTPgpChain,FTPgpChainName,'
	SET @tSql  += '		FTPtyCode,FTPtyName,'
	SET @tSql  += '		FTPdtCatCode1,FTPdtCatName1,'
	SET @tSql  += '		FTPdtCatCode2,FTPdtCatName2,'
	SET @tSql  += '		FCXtdQty'
	SET @tSql  += '	)'
	SET @tSql  += '	SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'  
	SET @tSql  += '		BchFrm.FTBchRefID,BchFrm_L.FTBchCode,BchFrm_L.FTBchName,TBO.FTXthDocNo,TBO.FDXthDocDate,'
	SET @tSql  += '		BchTo.FTBchRefID,BchTo_L.FTBchCode,BchTo_L.FTBchName,TBI.FTXthDocNo,TBI.FDXthDocDate,'
	SET @tSql  += '		INB.FTPdtCode, INB.FTXtdPdtName,'
	SET @tSql  += '		INB.FTPunCode,INB.FTPunName,'
	SET @tSql  += '		PDT.FTPgpChain,PDTG.FTPgpChainName,'
	SET @tSql  += '		PDT.FTPtyCode,PDTT.FTPtyName,'
	SET @tSql  += '		PDTC.FTPdtCat1,PCAT1.FTCatName AS FTCatName1,'
	SET @tSql  += '		PDTC.FTPdtCat2,PCAT2.FTCatName AS FTCatName2,'
	SET @tSql  += '		INB.FCXtdQty'
	SET @tSql  += '	FROM TCNTPdtIntDTBch		INB			WITH(NOLOCK)'
	SET @tSql  += '	INNER JOIN TCNTPdtTboHD		TBO			WITH(NOLOCK) ON INB.FTXthDocNo	= TBO.FTXthDocNo'
	SET @tSql  += '	INNER JOIN TCNMBranch		BchFrm		WITH(NOLOCK) ON TBO.FTBchCode	= BchFrm.FTBchCode'
	SET @tSql  += '	INNER JOIN TCNMBranch_L		BchFrm_L	WITH(NOLOCK) ON TBO.FTBchCode	= BchFrm_L.FTBchCode'
	SET @tSql  += '	LEFT JOIN TCNTPdtTbiHD		TBI			WITH(NOLOCK) ON INB.FTXtdRvtRef = TBI.FTXthDocNo'
	SET @tSql  += '	LEFT JOIN TCNMBranch		BchTo		WITH(NOLOCK) ON TBI.FTBchCode	= BchTo.FTBchCode'
	SET @tSql  += '	LEFT JOIN TCNMBranch_L		BchTo_L		WITH(NOLOCK) ON TBI.FTBchCode	= BchTo_L.FTBchCode'
	SET @tSql  += '	LEFT JOIN TCNMPdt			PDT			WITH(NOLOCK) ON INB.FTPdtCode	= PDT.FTPdtCode'
	SET @tSql  += '	LEFT JOIN TCNMPdtGrp_L		PDTG		WITH(NOLOCK) ON PDT.FTPgpChain	= PDTG.FTPgpChain	AND PDTG.FNLngID	= '''+CAST(@nLngID AS VARCHAR(10))+''' '
	SET @tSql  += '	LEFT JOIN TCNMPdtType_L		PDTT		WITH(NOLOCK) ON PDT.FTPtyCode	= PDTT.FTPtyCode	AND PDTT.FNLngID	= '''+CAST(@nLngID AS VARCHAR(10))+''' '
	SET @tSql  += '	LEFT JOIN TCNMPdtCategory	PDTC		WITH(NOLOCK) ON INB.FTPdtCode	= PDTC.FTPdtCode'
	SET @tSql  += '	LEFT JOIN TCNMPdtCatInfo_L	PCAT1		WITH(NOLOCK) ON PDTC.FTPdtCat1	= PCAT1.FTCatCode	AND PCAT1.FNLngID	= '''+CAST(@nLngID AS VARCHAR(10))+''' '
	SET @tSql  += '	LEFT JOIN TCNMPdtCatInfo_L	PCAT2		WITH(NOLOCK) ON PDTC.FTPdtCat2	= PCAT2.FTCatCode	AND PCAT2.FNLngID	= '''+CAST(@nLngID AS VARCHAR(10))+''' '
	SET @tSql  += @tSql1
	SET @tSql  += 'ORDER BY BchFrm.FTBchRefID,BchFrm_L.FTBchCode,BchTo.FTBchRefID,BchTo_L.FTBchCode'

	 --PRINT @tSql
	EXECUTE(@tSql)

END TRY
BEGIN CATCH 
	SET @FNResult= -1
END CATCH	
GO
