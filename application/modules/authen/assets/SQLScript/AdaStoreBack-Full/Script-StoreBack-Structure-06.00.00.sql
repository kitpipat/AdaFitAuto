
/****** Object:  View [dbo].[VCN_ProductCost]    Script Date: 8/10/2565 0:36:29 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

IF EXISTS
(SELECT * FROM dbo.sysobjects WHERE id = object_id(N'VCN_ProductCost'))
    DROP VIEW [dbo].VCN_ProductCost
GO

CREATE VIEW [dbo].[VCN_ProductCost] AS SELECT 
CAVG.FTAgnCode,
PDT.FTPdtCode,
PDT.FCPdtCostStd AS FCPdtCostStd,
CAVG.FCPdtCostIn AS FCPdtCostAVGIN,
CAVG.FCPdtCostEx AS FCPdtCostAVGEX,
CLST.FCSplLastPrice AS FCPdtCostLast,
CFIFO.FCPdtCostIn AS FCPdtCostFIFOIN,
CFIFO.FCPdtCostEx AS FCPdtCostFIFOEX

FROM TCNMPdt PDT
LEFT JOIN TCNMPdtCostAvg CAVG ON PDT.FTPdtCode = CAVG.FTPdtCode
LEFT JOIN (
SELECT  C.* FROM (
SELECT  ROW_NUMBER() OVER(PARTITION BY FTPdtCode ORDER BY FTPdtCode  DESC , FDSplLastDate DESC) AS FNRowPart,
        FTPdtCode,
		FCSplLastPrice
FROM TCNMPdtSpl ) C
WHERE C.FNRowPart = 1
) CLST  ON PDT.FTPdtCode = CLST.FTPdtCode
LEFT JOIN TCNMPdtCostFIFO CFIFO ON PDT.FTPdtCode = CFIFO.FTPdtCode
GO
/****** Object:  Table [dbo].[TRPTReprintDocTmp]    Script Date: 8/10/2565 0:36:29 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO


IF EXISTS
(SELECT * FROM dbo.sysobjects WHERE id = object_id(N'TRPTReprintDocTmp'))
    DROP TABLE [dbo].TRPTReprintDocTmp
GO

CREATE TABLE [dbo].[TRPTReprintDocTmp](
	[FTRptRowSeq] [bigint] IDENTITY(1,1) NOT NULL,
	[FNRowPartID] [bigint] NULL,
	[FTUsrSession] [varchar](255) NULL,
	[FTBchCode] [varchar](5) NULL,
	[FTBchName] [varchar](255) NULL,
	[FTXthDocNo] [varchar](50) NULL,
	[FDXthHisDateTime] [datetime] NULL,
	[FNXthReprintNum] [bigint] NULL,
	[FTXthUsrCode] [varchar](255) NULL,
	[FTXthUsrName] [varchar](255) NULL,
	[FTComName] [varchar](50) NULL,
	[FTRptCode] [varchar](50) NULL,
 CONSTRAINT [PK_TRPTReprintDocTmp] PRIMARY KEY CLUSTERED 
(
	[FTRptRowSeq] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
-- /****** Object:  StoredProcedure [dbo].[SP_CNoBrowseProduct]    Script Date: 8/10/2565 0:36:29 ******/
-- SET ANSI_NULLS ON
-- GO
-- SET QUOTED_IDENTIFIER ON
-- GO

-- IF EXISTS
-- (SELECT * FROM dbo.sysobjects WHERE id = object_id(N'SP_CNoBrowseProduct'))
--     DROP PROCEDURE [dbo].[SP_CNoBrowseProduct]
-- GO

-- CREATE PROCEDURE [dbo].[SP_CNoBrowseProduct]
-- 	--ผู้ใช้และสิท
-- 	@ptUsrCode			VARCHAR(10),
-- 	@ptUsrLevel			VARCHAR(10),
-- 	@ptSesAgnCode		VARCHAR(10),
-- 	@ptSesAgnType		VARCHAR(10),
-- 	@ptSesBchCodeMulti	VARCHAR(100),
-- 	@ptSesShopCodeMulti VARCHAR(100),
-- 	@ptSesMerCode		VARCHAR(20),
-- 	@ptWahCode			VARCHAR(5),

-- 	--กำหนดการแสดงข้อมูล
-- 	@pnRow			INT,
-- 	@pnPage			INT,
-- 	@pnMaxTopPage	INT,

-- 	--ค้นหาตามประเภท
-- 	@ptFilterBy	VARCHAR(80),
-- 	@ptSearch	VARCHAR(1000),

-- 	--OPTION PDT
-- 	@ptWhere				VARCHAR(8000),
-- 	@ptNotInPdtType			VARCHAR(8000),
-- 	@ptPdtCodeIgnorParam	VARCHAR(30),
-- 	@ptPDTMoveon			VARCHAR(1),
-- 	@ptPlcCodeConParam		VARCHAR(10),
-- 	@ptDISTYPE				VARCHAR(1),
-- 	@ptPagename				VARCHAR(10),
-- 	@ptNotinItemString		VARCHAR(8000),
-- 	@ptSqlCode				VARCHAR(20),
	
-- 	--Price And Cost
-- 	@ptPriceType	VARCHAR(30),
-- 	@ptPplCode		VARCHAR(30),
-- 	@ptPdtSpcCtl	VARCHAR(100),
	
-- 	@pnLngID INT
-- AS
-- BEGIN

--     DECLARE @tSQL				VARCHAR(MAX)
--     DECLARE @tSQLMaster			VARCHAR(MAX)
--     DECLARE @tUsrCode			VARCHAR(10)
--     DECLARE @tUsrLevel			VARCHAR(10)
--     DECLARE @tSesAgnCode		VARCHAR(10)
-- 	DECLARE @tSesAgnType		VARCHAR(10)
--     DECLARE @tSesBchCodeMulti	VARCHAR(100)
--     DECLARE @tSesShopCodeMulti	VARCHAR(100)
--     DECLARE @tSesMerCode		VARCHAR(20)
--     DECLARE @tWahCode			VARCHAR(5)
--     DECLARE @nRow				INT
--     DECLARE @nPage				INT
--     DECLARE @nMaxTopPage		INT
--     DECLARE @tFilterBy			VARCHAR(80)
--     DECLARE @tSearch			VARCHAR(80)
--     DECLARE	@tWhere				VARCHAR(8000)
--     DECLARE	@tNotInPdtType		VARCHAR(8000)
--     DECLARE	@tPdtCodeIgnorParam VARCHAR(30)
--     DECLARE	@tPDTMoveon			VARCHAR(1)
--     DECLARE	@tPlcCodeConParam	VARCHAR(10)
--     DECLARE	@tDISTYPE			VARCHAR(1)
--     DECLARE	@tPagename			VARCHAR(10)
--     DECLARE	@tNotinItemString	VARCHAR(8000)
--     DECLARE	@tSqlCode			VARCHAR(10)
--     DECLARE	@tPriceType			VARCHAR(10)
--     DECLARE	@tPplCode			VARCHAR(10)
-- 	DECLARE	@tPdtSpcCtl			VARCHAR(100)
--     DECLARE @nLngID				INT


--     SET @tUsrCode			= @ptUsrCode
--     SET @tUsrLevel			= @ptUsrLevel
--     SET @tSesAgnCode		= @ptSesAgnCode
-- 	SET @tSesAgnType		= @ptSesAgnType
--     SET @tSesBchCodeMulti	= @ptSesBchCodeMulti
--     SET @tSesShopCodeMulti	= @ptSesShopCodeMulti
--     SET @tSesMerCode		= @ptSesMerCode
--     SET @tWahCode			= @ptWahCode

--     SET @nRow			= @pnRow
--     SET @nPage			= @pnPage
--     SET @nMaxTopPage	= @pnMaxTopPage

--     SET @tFilterBy		= @ptFilterBy
--     SET @tSearch		= @ptSearch

--     SET @tWhere				= @ptWhere
--     SET @tNotInPdtType		= @ptNotInPdtType
--     SET @tPdtCodeIgnorParam = @ptPdtCodeIgnorParam
--     SET @tPDTMoveon			= @ptPDTMoveon
--     SET @tPlcCodeConParam	= @ptPlcCodeConParam
--     SET @tDISTYPE			= @ptDISTYPE
--     SET @tPagename			= @ptPagename
--     SET @tNotinItemString	= @ptNotinItemString
--     SET @tSqlCode			= @ptSqlCode

--     SET @tPriceType		= @ptPriceType
--     SET @tPplCode		= @ptPplCode
-- 	SET @tPdtSpcCtl		= @ptPdtSpcCtl
--     SET @nLngID			= @pnLngID

--     SET @tSQLMaster = ' SELECT Base.*, '

--     IF @nPage = 1 BEGIN
--             SET @tSQLMaster += ' COUNT(*) OVER() AS rtCountData '
--     END ELSE BEGIN
--             SET @tSQLMaster += ' 0 AS rtCountData '
--     END

--     SET @tSQLMaster += ' FROM ( '
--     SET @tSQLMaster += ' SELECT DISTINCT'

--     IF @nMaxTopPage > 0 BEGIN
--         SET @tSQLMaster += ' TOP ' + CAST(@nMaxTopPage  AS VARCHAR(10)) + ' '
--     END

--         --SET @tSQLMaster += ' ROW_NUMBER () OVER (ORDER BY Products.FDCreateOn DESC) AS FNRowID,'
--     SET @tSQLMaster += ' Products.FTPdtForSystem, '
--     SET @tSQLMaster += ' Products.FTPdtCode,PDT_IMG.FTImgObj,'

	
--     IF @ptUsrLevel != 'HQ'  BEGIN
--             SET @tSQLMaster += ' PDLSPC.FTAgnCode,PDLSPC.FTBchCode,PDLSPC.FTShpCode,PDLSPC.FTMerCode, '
--     END ELSE BEGIN
--             SET @tSQLMaster += ' '''' AS FTAgnCode,'''' AS FTBchCode,'''' AS  FTShpCode,'''' AS FTMerCode, '
--     END 

--     SET @tSQLMaster += ' Products.FTPdtStaLot,'
--     SET @tSQLMaster += ' Products.FTPtyCode,'
--     SET @tSQLMaster += ' Products.FTPgpChain,'
--     SET @tSQLMaster += ' Products.FTPdtStaVatBuy,Products.FTPdtStaVat,Products.FTVatCode,Products.FTPdtStaActive, Products.FTPdtSetOrSN, Products.FTPdtStaAlwDis,Products.FTPdtType,'
    
-- 	/** 
-- 		เช็ค Agency เพื่อ Join เอาราคาต้นทุน Agency 
-- 		Update By Wasin 23/09/2022
-- 		============================================================================================================ 
-- 	*/
-- 	IF (@tSesAgnCode != '' AND @tSesAgnType != '' AND @tSesAgnType = 2) BEGIN
-- 		SET @tSQLMaster	+= ' COSTAVG.FCPdtCostStd,'
-- 	END ELSE BEGIN
-- 		SET @tSQLMaster	+= ' Products.FCPdtCostStd,'
-- 	END
-- 	/** ============================================================================================================ */

-- 	SET @tSQLMaster += ' PDTSPL.FTSplCode,PDTSPL.FTUsrCode AS FTBuyer,PBAR.FTBarCode,PPCZ.FTPunCode,PPCZ.FCPdtUnitFact,'
--     SET @tSQLMaster += ' Products.FTCreateBy,'
--     SET @tSQLMaster += ' Products.FDCreateOn'
--     SET @tSQLMaster += ' FROM'
--     SET @tSQLMaster += ' TCNMPdt Products WITH (NOLOCK)'

--     IF @tPagename = 'Promotion' BEGIN------//-----------------เงื่อนไขพิเศษ ตามหน้า-------------
-- 				SET @tSQLMaster += ''
--         --SET @tSQLMaster += ' LEFT JOIN TCNMPdtLot PDTLOT WITH (NOLOCK) ON Products.FTPdtCode = PDTLOT.FTPdtCode '
--     END
    
--     IF @ptUsrLevel != 'HQ'  BEGIN
--         SET @tSQLMaster += ' LEFT JOIN TCNMPdtSpcBch PDLSPC WITH (NOLOCK) ON Products.FTPdtCode = PDLSPC.FTPdtCode'
--     END

--     SET @tSQLMaster += ' INNER JOIN TCNMPdtPackSize PPCZ WITH (NOLOCK) ON Products.FTPdtCode = PPCZ.FTPdtCode LEFT JOIN TCNMPdtBar PBAR WITH (NOLOCK)  ON Products.FTPdtCode = PBAR.FTPdtCode  AND PPCZ.FTPunCode = PBAR.FTPunCode' --//หาบาร์โค้ด
--     SET @tSQLMaster += ' LEFT JOIN TCNMPdtSpl PDTSPL WITH (NOLOCK) ON PBAR.FTPdtCode = PDTSPL.FTPdtCode AND PBAR.FTBarCode = PDTSPL.FTBarCode '--//ผู้จำหน่าย
--     SET @tSQLMaster += ' LEFT JOIN TCNMImgPdt AS PDT_IMG WITH(NOLOCK) ON Products.FTPdtCode = PDT_IMG.FTImgRefID AND PDT_IMG.FTImgTable = ''TCNMPdt'' AND PDT_IMG.FNImgSeq = 1 '					
    
--     ---//--------การจอยตาราง------///
--     IF @tFilterBy = 'FTPdtCode' AND @tSearch <> '' BEGIN
--         SET @tSQLMaster += ' '--//รหัสสินค้า
--     END

--     IF @tFilterBy = 'TCNTPdtStkBal' BEGIN
--         SET @tSQLMaster += ' LEFT JOIN TCNTPdtStkBal STK WITH(NOLOCK) ON Products.FTPdtCode = STK.FTPdtCode AND STK.FTBchCode IN ('+@tSesBchCodeMulti+') AND STK.FTWahCode = '''+@tWahCode+''' '
--     END		

--     --IF @tFilterBy = 'FTPdtName' AND @tSearch <> '' BEGIN
--         SET @tSQLMaster += ' LEFT JOIN TCNMPdt_L PDTL WITH (NOLOCK)       ON Products.FTPdtCode = PDTL.FTPdtCode  AND PDTL.FNLngID   = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '--//หาชื่อสินค้า
--     --END

--     /*IF @tFilterBy = 'PDTANDBarcode' OR @tFilterBy = 'FTPlcCode' OR @tSqlCode != '' BEGIN
--         SET @tSQLMaster += ' LEFT JOIN TCNMPdtPackSize PPCZ WITH (NOLOCK) ON PDT.FTPdtCode = PPCZ.FTPdtCode LEFT JOIN TCNMPdtBar PBAR WITH (NOLOCK)      ON PDT.FTPdtCode = PBAR.FTPdtCode  AND PPCZ.FTPunCode = PBAR.FTPunCode' --//หาบาร์โค้ด
--     END

--     IF @tFilterBy = 'FTBarCode' BEGIN
--         SET @tSQLMaster += ' LEFT JOIN TCNMPdtPackSize PPCZ WITH (NOLOCK) ON PDT.FTPdtCode = PPCZ.FTPdtCode LEFT JOIN TCNMPdtBar PBAR WITH (NOLOCK)      ON PDT.FTPdtCode = PBAR.FTPdtCode  AND PPCZ.FTPunCode = PBAR.FTPunCode' --//หาบาร์โค้ด
--     END*/

--     IF @tFilterBy = 'FTPunCode' AND @tSearch <> '' BEGIN
--         SET @tSQLMaster += ' LEFT JOIN TCNMPdtUnit_L PUNL WITH (NOLOCK)   ON PPCZ.FTPunCode = PUNL.FTPunCode AND PUNL.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' ' --//หาหน่วย
--     END								

--     IF @tFilterBy = 'FTPgpChain' AND @tSearch <> '' BEGIN
--         SET @tSQLMaster += ' LEFT JOIN TCNMPdtGrp_L PGL WITH (NOLOCK)     ON PGL.FTPgpChain = Products.FTPgpChain AND PGL.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '--//หากลุ่มสินค้า
--     END							

--     IF @tFilterBy = 'FTPtyCode' AND @tSearch <> '' BEGIN
--         SET @tSQLMaster += ' LEFT JOIN TCNMPdtType_L PTL WITH (NOLOCK)    ON Products.FTPtyCode = PTL.FTPtyCode   AND PTL.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '--//หาประเภทสินค้า
--     END	

--     IF @tFilterBy = 'FTBuyer' AND @tSearch <> '' BEGIN
--         SET @tSQLMaster += ' '--//ผู้จัดซื้อ
--     END

--     /* IF @tSqlCode != '' BEGIN------//----------------ผู้จำหน่าย-------------------
--         SET @tSQLMaster += ' LEFT JOIN TCNMPdtSpl PDTSPL WITH (NOLOCK) ON PBAR.FTPdtCode = PDTSPL.FTPdtCode AND PBAR.FTBarCode = PDTSPL.FTBarCode '--//ผู้จำหน่าย
--     END*/

--     ---//--------การจอยตาราง------///

--     SET @tSQLMaster += ' LEFT JOIN TCNMPdtCategory CATINFO WITH (NOLOCK) ON Products.FTPdtCode = CATINFO.FTPdtCode '


-- 	IF @tPdtSpcCtl <> '' BEGIN
-- 		SET @tSQLMaster += ' LEFT JOIN TCNSDocCtl_L DCT WITH(NOLOCK) ON DCT.FTDctTable = '''+ @tPdtSpcCtl +''' AND	DCT.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
-- 		SET @tSQLMaster += ' LEFT JOIN TCNMPdtSpcCtl PSC WITH(NOLOCK) ON Products.FTPdtCode = PSC.FTPdtCode AND DCT.FTDctCode = PSC.FTDctCode '
-- 	END

-- 	/** 
-- 		เช็ค Agency เพื่อ Join เอาราคาต้นทุน Agency 
-- 		Update By Wasin 23/09/2022
-- 		==============================================================================================================================================================================
-- 	*/
-- 	IF (@tSesAgnCode != '' AND @tSesAgnType != '' AND @tSesAgnType = 2) BEGIN
-- 		SET @tSQLMaster += ' LEFT JOIN TCNMPdtCostAvg	COSTAVG		WITH(NOLOCK)	ON Products.FTPdtCode	= COSTAVG.FTPdtCode		AND COSTAVG.FTAgnCode = '''+@tSesAgnCode+''' '
-- 	END 
-- 	/** ============================================================================================================================================================================== */

--     SET @tSQLMaster += ' WHERE ISNULL(Products.FTPdtCode,'''') != '''' '

-- 	IF @tPdtSpcCtl <> '' BEGIN
-- 		IF @tUsrLevel = 'HQ' BEGIN
-- 			SET @tSQLMaster += ' AND (PSC.FTPscAlwCmp = ''1'' OR PSC.FTPdtCode IS NULL OR (PSC.FTPscAlwOwner = ''1'' AND Products.FTCreateBy = '''+@tUsrCode+''')) '
-- 		END
-- 		IF @tUsrLevel = 'AD' BEGIN
-- 			SET @tSQLMaster += ' AND (PSC.FTPscAlwAD = ''1'' OR PSC.FTPdtCode IS NULL OR (PSC.FTPscAlwOwner = ''1'' AND Products.FTCreateBy = '''+@tUsrCode+''')) '
-- 		END
-- 		IF @tUsrLevel = 'BCH' BEGIN
-- 			SET @tSQLMaster += ' AND (PSC.FTPscAlwBch = ''1'' OR PSC.FTPdtCode IS NULL OR (PSC.FTPscAlwOwner = ''1'' AND Products.FTCreateBy = '''+@tUsrCode+''')) '
-- 		END
-- 		IF @tUsrLevel = 'MER' BEGIN
-- 			SET @tSQLMaster += ' AND (PSC.FTPscAlwMer = ''1'' OR PSC.FTPdtCode IS NULL OR (PSC.FTPscAlwOwner = ''1'' AND Products.FTCreateBy = '''+@tUsrCode+''')) '
-- 		END
-- 		IF @tUsrLevel = 'SHP' BEGIN
-- 			SET @tSQLMaster += ' AND (PSC.FTPscAlwShp = ''1'' OR PSC.FTPdtCode IS NULL OR (PSC.FTPscAlwOwner = ''1'' AND Products.FTCreateBy = '''+@tUsrCode+''')) '
-- 		END
-- 	END

--     ---//--------การค้นหา------///
--     IF @tFilterBy = 'FTPdtCode' AND @tSearch <> '' BEGIN
--         SET @tSQLMaster += ' AND ( Products.FTPdtCode  COLLATE THAI_BIN    LIKE ''%' + @tSearch + '%'' )'--//รหัสสินค้า
--     END

--     IF @tFilterBy = 'FTPdtName' AND @tSearch <> '' BEGIN
--         SET @tSQLMaster += ' AND ( UPPER(PDTL.FTPdtName)  COLLATE THAI_BIN    LIKE UPPER(''%' + @tSearch + '%'') ) '--//หาชื่อสินค้า
--     END

--     IF @tFilterBy = 'FTBarCode' AND @tSearch <> '' BEGIN
--         SET @tSQLMaster += ' AND ( PBAR.FTBarCode  COLLATE THAI_BIN    LIKE ''%' + @tSearch + '%'' )' --//หาบาร์โค้ด
--     END

--     IF @tFilterBy = 'PDTANDBarcode' AND @tSearch <> '' BEGIN
-- 				SET @tSQLMaster += ''
--         --SET @tSQLMaster += ' AND ( PBAR.FTPdtCode =''' + @tSearch + '''  OR  PBAR.FTBarCode =''' + @tSearch + ''' )' --//หาบาร์โค้ด
--     END

--     IF @tFilterBy = 'FTPunCode' AND @tSearch <> '' BEGIN
--         SET @tSQLMaster += ' AND ( PUNL.FTPunName  COLLATE THAI_BIN    LIKE ''%' + @tSearch + '%'' OR PUNL.FTPunCode COLLATE THAI_BIN LIKE ''%' + @tSearch + '%'' )' --//หาหน่วย
--     END								

--     IF @tFilterBy = 'FTPgpChain' AND @tSearch <> '' BEGIN
--         SET @tSQLMaster += ' AND ( PGL.FTPgpName   COLLATE THAI_BIN    LIKE ''%' + @tSearch + '%'' OR PGL.FTPgpChainName COLLATE THAI_BIN LIKE ''%' + @tSearch + '%'' ) '--//หากลุ่มสินค้า
--     END							

--     IF @tFilterBy = 'FTPtyCode' AND @tSearch <> '' BEGIN
--         SET @tSQLMaster += ' AND ( PTL.FTPtyName   COLLATE THAI_BIN    LIKE ''%' + @tSearch + '%'' ) '--//หาประเภทสินค้า
--     END	

--     IF @tFilterBy = 'FTBuyer' AND @tSearch <> '' BEGIN
--         SET @tSQLMaster += ' '--//ผู้จัดซื้อ
--     END

--     IF @tPagename = 'Promotion' BEGIN------//-----------------เงื่อนไขพิเศษ ตามหน้า-------------
-- 				SET @tSQLMaster += ''
--         --SET @tSQLMaster += ' AND (Products.FTPdtStaLot = ''2'' OR Products.FTPdtStaLot = ''1'' AND Products.FTPdtStaLot = ''1'' AND ISNULL(PDTLOT.FTLotNo,'''') <> '''' ) '
--     END
--     ---//--------การค้นหา------///

--     ---//--------การมองเห็นสินค้าตามผู้ใช้------///
--     IF @tUsrLevel != 'HQ' BEGIN
--         --//---------------------- การมองเห็นเฉพาะสินค้าตามระดับผู้ใช้--------------------------//
--         SET @tSQLMaster += ' AND ( ('
--         SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''+@tSesAgnCode+''' '

--                     IF @tSesMerCode != '' AND @tSesMerCode != '' BEGIN 
--                             SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''+@tSesMerCode+''' '
--                     END

--                     IF (SELECT ISNULL(FTBchCode,'') FROM TCNTUsrGroup WHERE FTUsrCode = @tUsrCode )<>'' BEGIN
--                             IF (@tSesBchCodeMulti <> '') BEGIN
--                                 SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') IN ('+@tSesBchCodeMulti+') '
--                             END ELSE BEGIN
--                                 SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') = '''' '
--                             END
--                     END
                                
--                     IF @tSesShopCodeMulti != '' BEGIN 
--                             SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') IN ('+@tSesShopCodeMulti+') '
--                     END

--         SET @tSQLMaster += ' )'
--         -- |-------------------------------------------------------------------------------------------| 

--         --//---------------------- การมองเห็นสินค้าระดับสาขา (สำหรับผู้ใช้ระดับร้านค้า)--------------------------//
--     IF @tSesShopCodeMulti != '' BEGIN 
--         SET @tSQLMaster += ' OR ('--//กรณีผู้ใช้ผูก Shp จะต้องเห็นสินค้าที่อยู่ใน Bch แต่ไม่ผูก Shp
--         SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''+@tSesAgnCode+''' '
--         SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''+@tSesMerCode+''' '
--         SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') IN ('+@tSesBchCodeMulti+') '
--         SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') = '''' '
--         SET @tSQLMaster += ' )'

--         SET @tSQLMaster += ' OR (' --//กรณีผู้ใช้ผูก Shp จะต้องเห็นสินค้าที่อยู่ใน Bch แต่ไม่ผูก Shp และไม่ผูก Mer
--         SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''+@tSesAgnCode+''' '
--         SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''' '
--         SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') IN ('+@tSesBchCodeMulti+') '
--         SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') = '''' '
--         SET @tSQLMaster += ' )'

--         SET @tSQLMaster += ' OR (' --//กรณีผู้ใช้ผูก Shp จะต้องเห็นสินค้าที่ไม่ผูก Bch และ ไม่ผูก Shp
--         SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''+@tSesAgnCode+''' '
--         SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''+@tSesMerCode+''' '
--         SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') = '''' '
--         SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') = '''' '
--         SET @tSQLMaster += ' )'

--         SET @tSQLMaster += ' OR (' --//กรณีผู้ใช้ผูก Shp จะต้องเห็นสินค้าที่ไม่ผูก Mer และสินค้าผูก Bch / Shp
--         SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''+@tSesAgnCode+''' '
--         SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''' '
--         SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') IN ('+@tSesBchCodeMulti+') '
--         SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') IN ('+@tSesShopCodeMulti+') '
--         SET @tSQLMaster += ' )'
--     END
--     -- |-------------------------------------------------------------------------------------------| 

--     -- //---------------------- การมองเห็นสินค้าระดับส่วนกลางหรือสินค้าที่ไม่ได้ผูกกับอะไรเลย--------------------------//
--     SET @tSQLMaster += ' OR ('

--     SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''+@tSesAgnCode+''' '

--     IF @tSesMerCode != '' AND @tSesMerCode != '' BEGIN --//กรณีผู้ใช้ผูก Mer จะต้องเห็นสินค้าที่ไม่ได้ผูก Mer ด้วย
--             SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''' '
--     END

--     IF (SELECT ISNULL(FTBchCode,'') FROM TCNTUsrGroup WHERE FTUsrCode= @tUsrCode)<>'' BEGIN --//กรณีผู้ใช้ผูก Bch จะต้องเห็นสินค้าที่ไม่ได้ผูก Bch ด้วย
--             SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''')  = '''' '
--     END

--     IF @tSesShopCodeMulti != '' BEGIN 
--             SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') = '''' '
--     END

--     SET @tSQLMaster += ' )'
--     -- |-------------------------------------------------------------------------------------------| 

--     -- //---------------------- การมองเห็นสินค้าระดับส่วนกลางหรือสินค้าที่ไม่ได้ผูกกับอะไรเลย--------------------------//
--     SET @tSQLMaster += ' OR ('
--     SET @tSQLMaster += ' ISNULL(PDLSPC.FTAgnCode,'''') = '''' '
--     SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTMerCode,'''') = '''' '
--     SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTBchCode,'''') = '''' '
--     SET @tSQLMaster += ' AND ISNULL(PDLSPC.FTShpCode,'''') = '''' '
--     SET @tSQLMaster += ' ))'
--     -- |-------------------------------------------------------------------------------------------| 

--     END
--     ---//--------การมองเห็นสินค้าตามผู้ใช้------///


--     -----//----Option-----//------

--     IF @tWhere != '' BEGIN
--         SET @tSQLMaster += @tWhere
--     END
    
--     IF @tNotInPdtType != '' BEGIN-----//------------- ไม่แสดงสินค้าตาม ประเภทสินค้า -------------------
--         SET @tSQLMaster += ' AND ISNULL(Products.FTPDtCode,'''') NOT IN ('+@tNotInPdtType+') '
--     END

--     IF @tPdtCodeIgnorParam != '' BEGIN----//-------------สินค้าที่ไม่ใช่ตัวข้อมูลหลักในการจัดสินค้าชุด-------------------
--         SET @tSQLMaster += ' AND ISNULL(Products.FTPDtCode,'''') != '''+@tPdtCodeIgnorParam+''' '
--     END

--     IF @tPDTMoveon != '' BEGIN------//---------สินค้าเคลื่อนไหว---------
--         SET @tSQLMaster += ' AND  Products.FTPdtStaActive = '''+@tPDTMoveon+''' '
--     END

--     IF @tPlcCodeConParam != '' AND @tFilterBy = 'FTPlcCode' BEGIN---/ที่เก็บ-  //กรณีที่เข้าไปหา plc code เเล้วไม่เจอ PDT เลย ต้องให้มันค้นหา โดย KEYWORD : EMPTY
--             IF  @tPlcCodeConParam != 'EMPTY' BEGIN
--                     SET @tSQLMaster += ' AND  PBAR.FTBarCode = '''+@tPlcCodeConParam+''' '
--             END
--             ELSE BEGIN
--                     SET @tSQLMaster += ' AND  PPCZ.FTPdtCode = ''EMPTY'' AND PPCZ.FTPunCode = ''EMPTY'' '
--             END
--     END

--     IF @ptDISTYPE != '' BEGIN------//----------------อนุญาตลด----------------
--         SET @tSQLMaster += ' AND  Products.FTPdtStaAlwDis = '''+@ptDISTYPE+''' '
--     END

--     IF @tPagename = 'PI' BEGIN------//-----------------เงื่อนไขพิเศษ ตามหน้า-------------
--         SET @tSQLMaster += ' AND  Products.FTPdtSetOrSN != ''4'' '
--     END

--     IF @tNotinItemString  != '' BEGIN-------//-----------------ไม่เอาสินค้าอะไรบ้าง NOT IN-----------
--         SET @tSQLMaster += @tNotinItemString
--     END

--     IF @tSqlCode != '' BEGIN------//----------------ผู้จำหน่าย-------------------
--         SET @tSQLMaster += ' AND  ( PDTSPL.FTSplCode = '''+@tSqlCode+'''  OR ISNULL(PDTSPL.FTSplCode,'''') = '''' ) '
--     END
--     -----//----Option-----//------
        
--     SET @tSQLMaster += ' ) Base '

--     IF @nRow != ''  BEGIN------------เงื่อนไขพิเศษ แบ่งหน้า----
--         SET @tSQLMaster += ' ORDER BY Base.FDCreateOn DESC '
--         SET @tSQLMaster += ' OFFSET '+CAST(((@nPage-1)*@nRow) AS VARCHAR(10))+' ROWS FETCH NEXT '+CAST(@nRow AS VARCHAR(10))+' ROWS ONLY'
--     END
--     ----//----------------------Data Master And Filter-------------//			

--     ----//----------------------Query Builder-------------//

--     SET @tSQL = '  SELECT PDT.rtCountData ,PDT.FTAgnCode,PDT.FTBchCode AS FTPdtSpcBch,PDT.FTShpCode,PDT.FTMerCode,PDT.FTImgObj,';
--     SET @tSQL += ' PDT.FTPdtCode,PDT_L.FTPdtName,PDT.FTPdtForSystem,PDT.FTPdtStaVatBuy,PDT.FTPdtStaVat,PDT.FTVatCode,ISNULL(VAT.FCVatRate, 0) AS FCVatRate, '
--     SET @tSQL += ' PDT.FTPdtStaActive,PDT.FTPdtSetOrSN,PDT.FTPgpChain,PDT.FTPtyCode,ISNULL(PDT_AGE.FCPdtCookTime,0) AS FCPdtCookTime,ISNULL(PDT_AGE.FCPdtCookHeat,0) AS FCPdtCookHeat, '
--     SET @tSQL += ' PDT.FTPunCode,PDT_UNL.FTPunName,PDT.FCPdtUnitFact, PDT.FTSplCode,PDT.FTBuyer,PDT.FTBarCode,PDT.FTPdtStaAlwDis,PDT.FTPdtType,'
-- 	SET @tSQL += ' ISNULL(PDT.FCPdtCostStd,0) AS FCPdtCostStd,'
-- 	SET @tSQL += ' PDT.FTPdtStaLot'

--     IF @tPriceType = 'Pricesell' OR @tPriceType = '' BEGIN------///ถ้าเป็นราคาขาย---
--         SET @tSQL += '  ,0 AS FCPgdPriceNet,VPA.FCPgdPriceRet AS FCPgdPriceRet,0 AS FCPgdPriceWhs'
--     END

--     IF @tPriceType = 'Price4Cst' BEGIN------// //ถ้าเป็นราคาทุน-----
--         SET @tSQL += '  ,0 AS FCPgdPriceNet,0 AS FCPgdPriceWhs,'
--         SET @tSQL += '  CASE'
--         SET @tSQL += '  WHEN ISNULL(PCUS.FCPgdPriceRet,0) <> 0 THEN PCUS.FCPgdPriceRet'
--         SET @tSQL += '  WHEN ISNULL(PBCH.FCPgdPriceRet,0) <> 0 THEN PBCH.FCPgdPriceRet'
--         --SET @tSQL += '  WHEN ISNULL(PEMPTY.FCPgdPriceRet,0) <> 0 THEN PEMPTY.FCPgdPriceRet'
--         SET @tSQL += '  ELSE 0'
--         SET @tSQL += '  END AS FCPgdPriceRet'
--     END

--     IF @tPriceType = 'Cost' BEGIN------//-----
--         SET @tSQL += '  ,ISNULL(FCPdtCostAVGIN,0)		AS FCPdtCostAVGIN,'
--         SET @tSQL += '  ISNULL(VPC.FCPdtCostAVGEx,0)	AS FCPdtCostAVGEx  , ISNULL(FCPdtCostLast,0)      AS FCPdtCostLast,'
--         SET @tSQL += '  ISNULL(VPC.FCPdtCostFIFOIN,0)	AS FCPdtCostFIFOIN , ISNULL(FCPdtCostFIFOEx,0)    AS FCPdtCostFIFOEx'
--     END

--     SET @tSQL += ' FROM ('
--     SET @tSQL +=  @tSQLMaster
--     SET @tSQL += ' ) PDT ';
--     SET @tSQL += ' LEFT JOIN TCNMPdt_L AS PDT_L WITH(NOLOCK) ON PDT.FTPdtCode = PDT_L.FTPdtCode AND PDT_L.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' '
--     SET @tSQL += ' LEFT JOIN TCNMPdtUnit_L AS PDT_UNL WITH(NOLOCK) ON PDT.FTPunCode = PDT_UNL.FTPunCode  AND PDT_UNL.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''''
--     --SET @tSQL += ' LEFT OUTER JOIN TCNMImgPdt AS PDT_IMG WITH(NOLOCK) ON PDT.FTPdtCode = PDT_IMG.FTImgRefID AND PDT_IMG.FTImgTable = ''TCNMPdt'' AND PDT_IMG.FNImgSeq = 1 '
--     SET @tSQL += ' LEFT OUTER JOIN TCNMPdtAge AS PDT_AGE WITH(NOLOCK) ON PDT.FTPdtCode = PDT_AGE.FTPdtCode '
--     SET @tSQL += ' LEFT OUTER JOIN VCN_VatActive AS VAT WITH(NOLOCK) ON PDT.FTVatCode = VAT.FTVatCode '

--     IF @tPriceType = 'Pricesell' OR @tPriceType = ''  BEGIN------//-----
--         --SET @tSQL += '  '
--         SET @tSQL += '  LEFT JOIN VCN_Price4PdtActive VPA WITH(NOLOCK) ON VPA.FTPdtCode = PDT.FTPdtCode AND VPA.FTPunCode = PDT_UNL.FTPunCode'
--     END

--     IF @tPriceType = 'Price4Cst' BEGIN

-- 			--//----ราคาของ customer
--       SET @tSQL += 'LEFT JOIN ( '
-- 			SET @tSQL += 'SELECT '
-- 			SET @tSQL += '	BP.FNRowPart,BP.FTPdtCode,BP.FTPunCode,BP.FDPghDStart,BP.FCPgdPriceNet,BP.FCPgdPriceWhs, '
-- 			SET @tSQL += '	CASE '
-- 			SET @tSQL += '		WHEN ADJ.FTPghStaAdj = ''2'' AND ADJ.FTPdtCode IS NOT NULL THEN ';
-- 			SET @tSQL += ' 			CONVERT (NUMERIC (18, 4),(BP.FCPgdPriceRet - (BP.FCPgdPriceRet * (ADJ.FCPgdPriceRet * 0.01)))) '
-- 			SET @tSQL += '		WHEN ADJ.FTPghStaAdj = ''3'' AND ADJ.FTPdtCode IS NOT NULL THEN '
-- 			SET @tSQL += ' 			CONVERT(NUMERIC(18,4), BP.FCPgdPriceRet - ADJ.FCPgdPriceRet) '
-- 			SET @tSQL += '		WHEN ADJ.FTPghStaAdj = ''4'' AND ADJ.FTPdtCode IS NOT NULL THEN '
-- 			SET @tSQL += ' 			CONVERT(NUMERIC(18,4), ((BP.FCPgdPriceRet * (ADJ.FCPgdPriceRet*0.01)) + BP.FCPgdPriceRet)) '
-- 			SET @tSQL += '		WHEN ADJ.FTPghStaAdj = ''5'' AND ADJ.FTPdtCode IS NOT NULL THEN '
-- 			SET @tSQL += ' 			CONVERT(NUMERIC(18,4), BP.FCPgdPriceRet + ADJ.FCPgdPriceRet) '
-- 			SET @tSQL += '	ELSE BP.FCPgdPriceRet '
-- 			SET @tSQL += '	END AS FCPgdPriceRet '
-- 			SET @tSQL += 'FROM ( '
-- 			SET @tSQL += '	SELECT '
-- 			SET @tSQL += '		ROW_NUMBER() OVER (PARTITION BY FTPdtCode,FTPunCode ORDER BY FTPplCode DESC, FTPghDocType DESC , FDPghDStart DESC) AS FNRowPart, '
-- 			SET @tSQL += '		CONVERT(VARCHAR(16), FDPghDStart, 121) AS FDPghDStart, '
-- 			SET @tSQL += '		FTPdtCode,FTPunCode,0 AS FCPgdPriceNet,FCPgdPriceRet,0 AS FCPgdPriceWhs,FTPplCode '
-- 			SET @tSQL += '   FROM TCNTPdtPrice4PDT WITH(NOLOCK) '
-- 			SET @tSQL += '   WHERE FDPghDStart <= CONVERT(VARCHAR(10), GETDATE(), 121) AND FTPghStaAdj = ''1'' '
-- 				IF @tPplCode = '' 
-- 					BEGIN SET @tSQL += '   AND ISNULL(FTPplCode,'''') = '''' ' END 
-- 				ELSE
-- 					BEGIN SET @tSQL += '   AND (FTPplCode = '''+@tPplCode+''' OR ISNULL(FTPplCode,'''') = '''')  ' END
-- 			SET @tSQL += ') BP '
-- 			SET @tSQL += 'LEFT JOIN ( '
-- 			SET @tSQL += '	SELECT '
-- 			SET @tSQL += '		ROW_NUMBER() OVER (PARTITION BY FTPdtCode,FTPunCode ORDER BY FTPplCode DESC, FTPghDocType DESC , FDPghDStart DESC) AS FNRowPart, '
-- 			SET @tSQL += '		CONVERT(VARCHAR(16), FDPghDStart, 121) AS FDPghDStart, '
-- 			SET @tSQL += '		FTPdtCode,FTPunCode,0 AS FCPgdPriceNet,FCPgdPriceRet,0 AS FCPgdPriceWhs,FTPghStaAdj,FTPplCode '
-- 			SET @tSQL += '   FROM TCNTPdtPrice4PDT WITH(NOLOCK) '
-- 			SET @tSQL += '   WHERE FDPghDStart <= CONVERT(VARCHAR(10), GETDATE(), 121) AND FTPghStaAdj <> ''1'' '
-- 				IF @tPplCode = '' 
-- 					BEGIN SET @tSQL += ' AND ISNULL(FTPplCode,'''') = '''' ' END 
-- 				ELSE 
-- 					BEGIN SET @tSQL += ' AND (FTPplCode = '''+@tPplCode+''' OR ISNULL(FTPplCode,'''') = '''') ' END
-- 			SET @tSQL += ' ) ADJ ON BP.FTPdtCode = ADJ.FTPdtCode AND BP.FTPunCode = ADJ.FTPunCode '
-- 			SET @tSQL += ' WHERE BP.FNRowPart = 1 '
-- 			SET @tSQL += ' AND (ADJ.FTPdtCode IS NULL OR ADJ.FNRowPart = 1) '
-- 			SET @tSQL += ' ) PCUS ON PDT.FTPdtCode = PCUS.FTPdtCode AND PDT.FTPunCode = PCUS.FTPunCode ' 
		
-- 			--// --ราคาของสาขา
-- 			SET @tSQL += ' LEFT JOIN ('
-- 			SET @tSQL += ' SELECT * FROM ('
-- 			SET @tSQL += ' SELECT '
-- 			SET @tSQL += ' ROW_NUMBER () OVER ( PARTITION BY FTPdtCode,FTPunCode ORDER BY FTPghDocType DESC , FDPghDStart DESC ) AS FNRowPart,'
-- 			SET @tSQL += ' FTPdtCode , '
-- 			SET @tSQL += ' FTPunCode , '
-- 			SET @tSQL += ' FCPgdPriceRet '
-- 			SET @tSQL += ' FROM TCNTPdtPrice4PDT WHERE  '
-- 			SET @tSQL += ' FDPghDStart <= CONVERT (VARCHAR(10), GETDATE(), 121)'
-- 			SET @tSQL += ' AND FDPghDStop >= CONVERT (VARCHAR(10), GETDATE(), 121)'
-- 			SET @tSQL += ' AND FTPghTStart <= CONVERT(time,GETDATE())'
-- 			SET @tSQL += ' AND FTPghTStop >= CONVERT(time,GETDATE())'
-- 			SET @tSQL += ' AND (FTPghDocType <> 3 AND FTPghDocType <> 4) '
-- 			SET @tSQL += ' AND ISNULL(FTPplCode,'''') = '''' OR FTPplCode = (SELECT FTPplCode FROM TCNMBranch WHERE FTPplCode != '''' AND FTBchCode = (SELECT TOP 1 FTBchCode FROM TCNMBranch WHERE FTAgnCode = '''+@tSesAgnCode+''' ))'
-- 			SET @tSQL += ') AS PCUS '
-- 			SET @tSQL += ' WHERE PCUS.FNRowPart = 1 '
-- 			SET @tSQL += ' ) PBCH ON PDT.FTPdtCode = PBCH.FTPdtCode AND PDT.FTPunCode = PBCH.FTPunCode '
--     END

--     IF @tPriceType = 'Cost' BEGIN
--         SET @tSQL += '  LEFT JOIN VCN_ProductCost VPC WITH(NOLOCK) ON VPC.FTPdtCode = PDT.FTPdtCode AND VPC.FTAgnCode = '''+@tSesAgnCode+''' '
--     END
		
-- 	-- SELECT @tSQL
-- 	-- PRINT(@tSQL)
--      EXECUTE(@tSQL)
-- END
-- GO
-- /****** Object:  StoredProcedure [dbo].[SP_RPTxReprintDocTmp]    Script Date: 8/10/2565 0:36:29 ******/
-- SET ANSI_NULLS ON
-- GO
-- SET QUOTED_IDENTIFIER ON
-- GO


-- IF EXISTS
-- (SELECT * FROM dbo.sysobjects WHERE id = object_id(N'SP_RPTxReprintDocTmp'))
--     DROP PROCEDURE [dbo].[SP_RPTxReprintDocTmp]
-- GO

-- -- =============================================
-- -- Author:		รายงาน - ข้อมูลการพิมพ์ซ้ำ
-- -- Create date: 06/10/2022 Wasin
-- -- =============================================
-- CREATE PROCEDURE [dbo].[SP_RPTxReprintDocTmp]
-- 	@pnLngID		INT , 
-- 	@pnComName		VARCHAR(100),
-- 	@ptRptCode		VARCHAR(100),
-- 	@ptUsrSession	VARCHAR(255),
-- 	@pnFilterType	INT, --1 BETWEEN 2 IN
-- 	-- สาขา
-- 	@ptBchL			VARCHAR(8000), --สาขา Condition IN
-- 	@ptBchF			VARCHAR(5),
-- 	@ptBchT			VARCHAR(5),
-- 	-- เครื่องจุดขาย
-- 	@ptPosL			VARCHAR(8000), --เครื่องขาย Condition IN
-- 	@ptPosF			VARCHAR(10),
-- 	@ptPosT			VARCHAR(10),
-- 	-- Cashier
-- 	@ptUsrL			VARCHAR(8000), --Cashier Condition IN
-- 	@ptUsrF			VARCHAR(10),
-- 	@ptUsrT			VARCHAR(10),
-- 	-- Document Date
-- 	@ptDocDateF		VARCHAR(10),
-- 	@ptDocDateT		VARCHAR(10),

-- 	@FNResult		INT OUTPUT
-- AS
-- BEGIN TRY
-- 	DECLARE @nLngID			INT 
-- 	DECLARE @nComName		VARCHAR(100)
-- 	DECLARE @tRptCode		VARCHAR(100)
-- 	DECLARE @tUsrSession	VARCHAR(255)
-- 	DECLARE @tSql			VARCHAR(8000)
-- 	DECLARE @tSqlIns		VARCHAR(8000)
-- 	DECLARE @tSql1			VARCHAR(Max)
-- 	DECLARE @tSql2			VARCHAR(8000)
-- 	-- Branch Code
-- 	DECLARE @tBchF			VARCHAR(5)
-- 	DECLARE @tBchT			VARCHAR(5)
-- 	-- Cashier
-- 	DECLARE @tUsrF			VARCHAR(10)
-- 	DECLARE @tUsrT			VARCHAR(10)
-- 	-- Pos Code
-- 	DECLARE @tPosF			VARCHAR(20)
-- 	DECLARE @tPosT			VARCHAR(20)
-- 	-- Document Date
-- 	DECLARE @tDocDateF		VARCHAR(10)
-- 	DECLARE @tDocDateT		VARCHAR(10)

-- 	/** ================================================== SET PARAMETER ================================================== */
-- 	SET @nLngID			= @pnLngID
-- 	SET @nComName		= @pnComName
-- 	SET @tUsrSession	= @ptUsrSession
-- 	SET @tRptCode		= @ptRptCode
-- 	-- Branch
-- 	SET @tBchF			= @ptBchF
-- 	SET @tBchT			= @ptBchT
-- 	-- Pos
-- 	SET @tPosF			= @ptPosF
-- 	SET @tPosT			= @ptPosT
-- 	-- Cashier
-- 	SET @tUsrF			= @ptUsrF
-- 	SET @tUsrT			= @ptUsrT
-- 	-- Doc Date
-- 	SET @tDocDateF		= @ptDocDateF
-- 	SET @tDocDateT		= @ptDocDateT
-- 	SET @FNResult		= 0
-- 	-- Covert Doc Date
-- 	SET @tDocDateF		= CONVERT(VARCHAR(10),@tDocDateF,121)
-- 	SET @tDocDateT		= CONVERT(VARCHAR(10),@tDocDateT,121)

-- 	/** ================================================== Check Condition Parameter ================================================== */
-- 	/** เช็ค ภาษา */
-- 	IF @nLngID	= NULL BEGIN	SET @nLngID	= 1 END

-- 	/** เช็ค สาขา */
-- 	IF @ptBchL	= NULL BEGIN	SET @ptBchL	= '' END
-- 	IF @tBchF	= NULL BEGIN	SET @tBchF		= '' END
-- 	IF @tBchT	= NULL OR @tBchT = '' BEGIN SET @tBchT	= @tBchF END

-- 	/** เช็ค Pos เครื่องจุดขาย */
-- 	IF @ptPosL	= NULL	BEGIN	SET @ptPosL = ''	END
-- 	IF @tPosF	= NULL	BEGIN	SET @tPosF	= ''	END
-- 	IF @tPosT	= NULL OR @tPosT = '' BEGIN	SET @tPosT = @tPosF	END

-- 	/** เช็ค User */
-- 	IF @ptUsrL	= NULL	BEGIN	SET @ptUsrL = ''	END
-- 	IF @tUsrF	= NULL	BEGIN	SET @tUsrF	= ''	END
-- 	IF @tUsrT	= NULL OR @tUsrT = ''	BEGIN	SET @tUsrT = @tUsrF	END

-- 	/** เช็ค Doc Date */
-- 	IF @tDocDateF = NULL BEGIN SET @tDocDateF = '' END
-- 	IF @tDocDateT = NULL OR @tDocDateT ='' BEGIN SET @tDocDateT = @tDocDateF END

-- 	SET @tSql1	=   ' WHERE  SV.FTEvnCode = ''008'''
		 
-- 	IF @pnFilterType = '1'
-- 	BEGIN
-- 		/** Check Where Between Branch */
-- 		IF (@tBchF <> '' AND @tBchT <> '')
-- 		BEGIN
-- 			SET @tSql1	+=' AND SV.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
-- 		END
-- 		/** Check Where Between Pos */
-- 		IF (@tPosF <> '' AND @tPosT <> '')
-- 		BEGIN
-- 			SET @tSql1	+=' AND SV.FTPosCode BETWEEN ''' + @tPosF + ''' AND ''' + @tPosT + ''''
-- 		END
-- 		/** Check Where Between Usr Cashier */
-- 		IF (@tUsrF <> '' AND @tUsrT <> '')
-- 		BEGIN
-- 			SET @tSql1	+=' AND SV.FTUsrCode BETWEEN ''' + @tUsrF + ''' AND ''' + @tUsrT + ''''
-- 		END
-- 	END

-- 	IF @pnFilterType = '2'
-- 	BEGIN
-- 		/** Check Where In Branch */
-- 		IF (@ptBchL <> '' )
-- 		BEGIN
-- 			SET @tSql1	+=' AND SV.FTBchCode IN (' + @ptBchL + ')'
-- 		END
-- 		/** Check Where In Pos */
-- 		IF (@ptPosL <> '' )
-- 		BEGIN
-- 			SET @tSql1 +=' AND SV.FTPosCode IN (' + @ptPosL + ')'
-- 		END
-- 		/** Check Where In Usr Cashier */
-- 		IF (@tUsrF <> '' AND @tUsrT <> '')
-- 		BEGIN
-- 			SET @tSql1 +=' AND SV.FTUsrCode BETWEEN ''' + @tUsrF + ''' AND ''' + @tUsrT + ''''
-- 		END
-- 	END

-- 	/** WHERE Document Date Reprint */
-- 	IF (@tDocDateF <> '' AND @tDocDateT <> '')
-- 	BEGIN
--     	SET @tSql1 +=' AND CONVERT(VARCHAR(10),SV.FDHisDateTime,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''''
-- 	END


-- 	DELETE FROM TRPTReprintDocTmp WITH (ROWLOCK) WHERE FTComName =  '' + @nComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''


-- 	SET @tSql =  '	INSERT INTO TRPTReprintDocTmp (FTComName,FTRptCode,FTUsrSession,FTBchCode,FTBchName,FTXthDocNo,FDXthHisDateTime,FNXthReprintNum,FTXthUsrCode,FTXthUsrName)'
-- 	SET @tSql += '	SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,'
-- 	SET @tSql += '		SV.FTBchCode AS FTXthBchCode,'
-- 	SET @tSql += '		BL.FTBchName AS FTXthBchName,'
-- 	SET @tSql += '		SV.FTSvnRemark AS FTXthDocNo,'
-- 	SET @tSql += '		SV.FDHisDateTime AS FDXthHisDateTime,'
-- 	SET @tSql += '		ROW_NUMBER() OVER(PARTITION BY SV.FTSvnRemark ORDER BY SV.FDHisDateTime) AS FNXthReprintNum,'
-- 	SET @tSql += '		UL.FTUsrCode AS FTXthUsrCode,'
-- 	SET @tSql += '		UL.FTUsrName AS FTXthUsrName'
-- 	SET @tSql += '	FROM TPSTShiftEvent SV WITH(NOLOCK)'
-- 	SET @tSql += '	LEFT JOIN TCNMBranch_L	BL WITH(NOLOCK) ON SV.FTBchCode = BL.FTBchCode AND BL.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''''
-- 	SET @tSql += '	LEFT JOIN TCNMUser_L	UL WITH(NOLOCK) ON SV.FTSvnApvCode	= UL.FTUsrCode AND UL.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''''
-- 	SET @tSql += @tSql1
-- 	SET @tSql += '	ORDER BY  SV.FTBchCode,FDHisDateTime'


-- 	-- PRINT @tSql;

-- 	EXECUTE(@tSql)

-- 	RETURN SELECT * FROM TRPTReprintDocTmp WHERE FTComName = ''+ @nComName + '' AND FTRptCode = ''+ @tRptCode +'' AND FTUsrSession = '' + @tUsrSession + ''

-- END TRY
-- BEGIN CATCH 
-- 	SET @FNResult= -1
-- END CATCH	
-- GO

DELETE [dbo].[TCNTUsrFuncRpt] WHERE FTRolCode = '001001066'
GO
DELETE [dbo].[TSysReport] WHERE FTRptCode = '001001066'
GO
DELETE [dbo].[TSysReport_L] WHERE FTRptCode = '001001066'
GO
INSERT [dbo].[TCNTUsrFuncRpt] ([FTRolCode], [FTUfrType], [FTUfrGrpRef], [FTUfrRef], [FTGhdApp], [FTUfrStaAlw], [FTUfrStaFavorite], [FDLastUpdOn], [FTLastUpdBy], [FDCreateOn], [FTCreateBy]) VALUES (N'00002', N'2', N'001001', N'001001066', NULL, N'1', N'0', CAST(N'2022-10-05T19:50:59.733' AS DateTime), N'00002', CAST(N'2022-10-05T19:50:59.733' AS DateTime), N'00002')
GO
INSERT [dbo].[TSysReport] ([FTRptCode], [FTGrpRptModCode], [FTGrpRptCode], [FTRptRoute], [FTRptStaUseFrm], [FTRptTblView], [FTRptFilterCol], [FTRptFileName], [FTRptStaShwBch], [FTRptStaShwYear], [FTRptSeqNo], [FTRptStaUse], [FTLicPdtCode]) VALUES (N'001001066', N'001', N'001001', N'rptReprintDocument', NULL, NULL, N'1,6,2,3,4,45', NULL, N'1', N'1', 44, N'1', N'SB-RPT001001066')
GO
INSERT [dbo].[TSysReport_L] ([FTRptCode], [FNLngID], [FTRptName], [FTRptDes]) VALUES (N'001001066', 1, N'รายงาน - ข้อมูลการพิมพ์ซ้ำ', NULL)
GO
INSERT [dbo].[TSysReport_L] ([FTRptCode], [FNLngID], [FTRptName], [FTRptDes]) VALUES (N'001001066', 2, N'Report - Reprint Data', NULL)



/****** Object:  Table [dbo].[TRPTxAnalysPurchaseTmp]    Script Date: 9/11/2565 15:49:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[TRPTxAnalysPurchaseTmp]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[TRPTxAnalysPurchaseTmp](
	[FNXsdSeqNo] [bigint] NULL,
	[FTXsdGrpCode] [varchar](50) NULL,
	[FTXsdGrpName] [varchar](255) NULL,
	[FTPdtCode] [varchar](50) NULL,
	[FTPdrName] [varchar](255) NULL,
	[FCXsdSetPrice] [numeric](18, 4) NULL,
	[FCXsdQtyAll] [numeric](18, 4) NULL,
	[FCXsdQtyAvgPct] [numeric](18, 4) NULL,
	[FCXsdAmtB4DisChg] [numeric](18, 4) NULL,
	[FCXsdAmtAvgPct] [numeric](18, 4) NULL,
	[FCXsdDisChg] [numeric](18, 4) NULL,
	[FCXsdNetAfHD] [numeric](18, 4) NULL,
	[FCXsdNetAvgPct] [numeric](18, 4) NULL,
	[FTUsrSession] [varchar](400) NULL
) ON [PRIMARY]
END
GO
