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

/****** Object:  StoredProcedure [dbo].[SP_CNoBrowseProduct]    Script Date: 8/10/2565 0:36:29 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

IF EXISTS
(SELECT * FROM dbo.sysobjects WHERE id = object_id(N'SP_CNoBrowseProduct'))
    DROP PROCEDURE [dbo].[SP_CNoBrowseProduct]
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


IF EXISTS
(SELECT * FROM dbo.sysobjects WHERE id = object_id(N'SP_RPTxReprintDocTmp'))
    DROP PROCEDURE [dbo].[SP_RPTxReprintDocTmp]
GO

-- =============================================
-- Author:		รายงาน - ข้อมูลการพิมพ์ซ้ำ
-- Create date: 06/10/2022 Wasin
-- =============================================
CREATE PROCEDURE [dbo].[SP_RPTxReprintDocTmp]
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


USE [FitAuto]
GO
/****** Object:  StoredProcedure [dbo].[SP_CNoBrowseProduct]    Script Date: 4/11/2565 21:17:10 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
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
	SET @tSql +=' LEFT JOIN TSVTJob2OrdHDCst JCst ON Job2Ord.FTXshRefDocNo = JCst.FTXshDocNo'
	SET @tSql +=' LEFT JOIN TSVMCar Car On JCst.FTCarCode = Car.FTCarCode'
	SET @tSql +=  @tSql1
	PRINT @tSql 
	EXECUTE(@tSql)

END TRY
BEGIN CATCH 
	SET @FNResult= -1
END CATCH	





