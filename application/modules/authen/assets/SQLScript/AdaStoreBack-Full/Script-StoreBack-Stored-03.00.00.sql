/****** Object:  StoredProcedure [dbo].[SP_CNoBrowseProduct]    Script Date: 5/9/2565 18:26:13 ******/
DROP PROCEDURE [dbo].[SP_CNoBrowseProduct]
GO

/****** Object:  StoredProcedure [dbo].[SP_CNoBrowseProduct]    Script Date: 5/9/2565 18:26:13 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[SP_CNoBrowseProduct]
	--ผู้ใช้และสิท
	@ptUsrCode VARCHAR(10),
	@ptUsrLevel VARCHAR(10),
	@ptSesAgnCode VARCHAR(10),
	@ptSesBchCodeMulti VARCHAR(100),
	@ptSesShopCodeMulti VARCHAR(100),
	@ptSesMerCode VARCHAR(20),
	@ptWahCode VARCHAR(5),

	--กำหนดการแสดงข้อมูล
	@pnRow INT,
	@pnPage INT,
	@pnMaxTopPage INT,
	--ค้นหาตามประเภท
	@ptFilterBy VARCHAR(80),
	@ptSearch VARCHAR(1000),

	--OPTION PDT
	@ptWhere VARCHAR(8000),
	@ptNotInPdtType VARCHAR(8000),
	@ptPdtCodeIgnorParam VARCHAR(30),
	@ptPDTMoveon VARCHAR(1),
	@ptPlcCodeConParam VARCHAR(10),
	@ptDISTYPE VARCHAR(1),
	@ptPagename VARCHAR(10),
	@ptNotinItemString VARCHAR(8000),
	@ptSqlCode VARCHAR(20),
	
	--Price And Cost
	@ptPriceType VARCHAR(30),
	@ptPplCode VARCHAR(30),
	@ptPdtSpcCtl VARCHAR(100),
	
	@pnLngID INT
AS
BEGIN

    DECLARE @tSQL VARCHAR(MAX)
    DECLARE @tSQLMaster VARCHAR(MAX)
    DECLARE @tUsrCode VARCHAR(10)
    DECLARE @tUsrLevel VARCHAR(10)
    DECLARE @tSesAgnCode VARCHAR(10)
    DECLARE @tSesBchCodeMulti VARCHAR(100)
    DECLARE @tSesShopCodeMulti VARCHAR(100)
    DECLARE @tSesMerCode VARCHAR(20)
    DECLARE @tWahCode VARCHAR(5)
    DECLARE @nRow INT
    DECLARE @nPage INT
    DECLARE @nMaxTopPage INT
    DECLARE @tFilterBy VARCHAR(80)
    DECLARE @tSearch VARCHAR(80)
    DECLARE	@tWhere VARCHAR(8000)
    DECLARE	@tNotInPdtType VARCHAR(8000)
    DECLARE	@tPdtCodeIgnorParam VARCHAR(30)
    DECLARE	@tPDTMoveon VARCHAR(1)
    DECLARE	@tPlcCodeConParam VARCHAR(10)
    DECLARE	@tDISTYPE VARCHAR(1)
    DECLARE	@tPagename VARCHAR(10)
    DECLARE	@tNotinItemString VARCHAR(8000)
    DECLARE	@tSqlCode VARCHAR(10)
    DECLARE	@tPriceType VARCHAR(10)
    DECLARE	@tPplCode VARCHAR(10)
	DECLARE	@tPdtSpcCtl VARCHAR(100)

    DECLARE @nLngID INT
    SET @tUsrCode = @ptUsrCode
    SET @tUsrLevel = @ptUsrLevel
    SET @tSesAgnCode = @ptSesAgnCode
    SET @tSesBchCodeMulti = @ptSesBchCodeMulti
    SET @tSesShopCodeMulti = @ptSesShopCodeMulti
    SET @tSesMerCode = @ptSesMerCode
    SET @tWahCode = @ptWahCode

    SET @nRow = @pnRow
    SET @nPage = @pnPage
    SET @nMaxTopPage = @pnMaxTopPage

    SET @tFilterBy = @ptFilterBy
    SET @tSearch = @ptSearch

    SET @tWhere = @ptWhere
    SET @tNotInPdtType = @ptNotInPdtType
    SET @tPdtCodeIgnorParam = @ptPdtCodeIgnorParam
    SET @tPDTMoveon = @ptPDTMoveon
    SET @tPlcCodeConParam = @ptPlcCodeConParam
    SET @tDISTYPE = @ptDISTYPE
    SET @tPagename = @ptPagename
    SET @tNotinItemString = @ptNotinItemString
    SET @tSqlCode = @ptSqlCode

    SET @tPriceType = @ptPriceType
    SET @tPplCode = @ptPplCode
	SET @tPdtSpcCtl = @ptPdtSpcCtl
    SET @nLngID = @pnLngID

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
    SET @tSQLMaster += ' Products.FTPdtStaVatBuy,Products.FTPdtStaVat,Products.FTVatCode,Products.FTPdtStaActive, Products.FTPdtSetOrSN, Products.FTPdtStaAlwDis,Products.FTPdtType,Products.FCPdtCostStd,'
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
    SET @tSQL += ' PDT.FTPunCode,PDT_UNL.FTPunName,PDT.FCPdtUnitFact, PDT.FTSplCode,PDT.FTBuyer,PDT.FTBarCode,PDT.FTPdtStaAlwDis,PDT.FTPdtType,PDT.FCPdtCostStd,PDT.FTPdtStaLot'

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
        SET @tSQL += '  ,ISNULL(VPC.FCPdtCostStd,0)       AS FCPdtCostStd    , ISNULL(FCPdtCostAVGIN,0)     AS FCPdtCostAVGIN,'
        SET @tSQL += '  ISNULL(VPC.FCPdtCostAVGEx,0)     AS FCPdtCostAVGEx  , ISNULL(FCPdtCostLast,0)      AS FCPdtCostLast,'
        SET @tSQL += '  ISNULL(VPC.FCPdtCostFIFOIN,0)    AS FCPdtCostFIFOIN , ISNULL(FCPdtCostFIFOEx,0)    AS FCPdtCostFIFOEx'
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
		
		--SELECT @tSQL
	--PRINT(@tSQL)
    EXECUTE(@tSQL)
END
GO





/****** Object:  StoredProcedure [dbo].[SP_RPTxSaleFCCompVD001001065]    Script Date: 5/9/2565 16:38:28 ******/
DROP PROCEDURE [dbo].[SP_RPTxSaleFCCompVD001001065]
GO

/****** Object:  StoredProcedure [dbo].[SP_RPTxSaleFCCompVD001001065]    Script Date: 5/9/2565 16:38:28 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE [dbo].[SP_RPTxSaleFCCompVD001001065]
	@pnLngID		int , 
	@pnComName		Varchar(100),
	@ptRptCode		Varchar(100),
	@ptUsrSession	Varchar(255),
	@pnFilterType	int,

	--สาขา
	@ptBchL			Varchar(8000), --กรณี Condition IN
	@ptBchF			Varchar(5),
	@ptBchT			Varchar(5),

	-- Product
	@ptPdtCodeF		Varchar(20),
	@ptPdtCodeT		Varchar(20),
	@ptPdtChanF		Varchar(30),
	@ptPdtChanT		Varchar(30),
	@ptPdtTypeF		Varchar(5),
	@ptPdtTypeT		Varchar(5),

	@ptDocDateF		Varchar(10),
	@ptDocDateT		Varchar(10),

	@ptCate1F		VARCHAR(100),
	@ptCate2F		VARCHAR(100),

	@ptSysSplCode	VARCHAR(100),

	@FNResult		INT OUTPUT 

AS
BEGIN TRY
	DECLARE @nLngID			int 
	DECLARE @nComName		Varchar(100)
	DECLARE @tRptCode		Varchar(100)
	DECLARE @tUsrSession	Varchar(255)
	DECLARE @tSql			VARCHAR(8000)

	DECLARE @tSqlIns		VARCHAR(8000)
	DECLARE @tSql1			nVARCHAR(Max)
	DECLARE @tSql2			nVARCHAR(Max)

	-- Filter Branch
	DECLARE @tBchF Varchar(5)
	DECLARE @tBchT Varchar(5)

	-- Filter Product
	DECLARE @tPdtCodeF Varchar(20)
	DECLARE @tPdtCodeT Varchar(20)
	DECLARE @tPdtChanF Varchar(30)
	DECLARE @tPdtChanT Varchar(30)
	DECLARE @tPdtTypeF Varchar(5)
	DECLARE @tPdtTypeT Varchar(5)

	-- Filter Date
	DECLARE @tDocDateF Varchar(10)
	DECLARE @tDocDateT Varchar(10)

	-- Filter Catagory
	DECLARE @tCate1F VARCHAR(100)
	DECLARE @tCate2F VARCHAR(100)

	-- System Supplier Code
	DECLARE @tSysSplCode	VARCHAR(100)

	---------------------------------------------------- Set Parameter ----------------------------------------------------
	-- Parameter Center
	SET @nLngID			= @pnLngID
	SET @nComName		= @pnComName
	SET @tUsrSession	= @ptUsrSession
	SET @tRptCode		= @ptRptCode

	-- Set Branch Filter
	SET @tBchF			= @ptBchF
	SET @tBchT			= @ptBchT

	-- Set Product Filter
	SET @tPdtCodeF		= @ptPdtCodeF 
	SET @tPdtCodeT		= @ptPdtCodeT
	SET @tPdtChanF		= @ptPdtChanF
	SET @tPdtChanT		= @ptPdtChanT 
	SET @tPdtTypeF		= @ptPdtTypeF
	SET @tPdtTypeT		= @ptPdtTypeT

	-- Set DocDate Filter
	SET @tDocDateF		= @ptDocDateF
	SET @tDocDateT		= @ptDocDateT

	-- Set Catagory Filter
	SET @tCate1F		= @ptCate1F
	SET @tCate2F		= @ptCate2F

	SET @FNResult		= 0

	-- Convert Doc Date
	SET @tDocDateF		= CONVERT(VARCHAR(10),@tDocDateF,121)
	SET @tDocDateT		= CONVERT(VARCHAR(10),@tDocDateT,121)

	-- Set System Spl Code
	SET @tSysSplCode	= @ptSysSplCode

	-- Check Null Lang Result
	IF @nLngID	= null
	BEGIN
		SET @nLngID	= 1
	END	

	-- Check Null Branch
	IF @ptBchL	= null
	BEGIN
		SET @ptBchL	= ''
	END

	IF @tBchF	= null
	BEGIN
		SET @tBchF	= ''
	END
	IF @tBchT	= null OR @tBchT = ''
	BEGIN
		SET @tBchT	= @tBchF
	END

	-- Check Null Product From - To
	IF @tPdtCodeF	= null
	BEGIN
		SET @tPdtCodeF	= ''
	END 
	IF @tPdtCodeT	= null OR @tPdtCodeT =''
	BEGIN
		SET @tPdtCodeT	= @tPdtCodeF
	END

	-- Check Null Product Chan From - To
	IF @tPdtChanF	= null
	BEGIN
		SET @tPdtChanF	= ''
	END 
	IF @tPdtChanT	= null OR @tPdtChanT =''
	BEGIN
		SET @tPdtChanT	= @tPdtChanF
	END 

	-- Check Null Product Type From - To
	IF @tPdtTypeF = null
	BEGIN
		SET @tPdtTypeF = ''
	END 
	IF @tPdtTypeT = null OR @tPdtTypeT =''
	BEGIN
		SET @tPdtTypeT = @tPdtTypeF
	END


	-- Check Null Doc Date From - To
	IF @tDocDateF = null
	BEGIN 
		SET @tDocDateF = ''
	END
	IF @tDocDateT = null OR @tDocDateT =''
	BEGIN 
		SET @tDocDateT = @tDocDateF
	END

	------------------------------------------------ WHERE STATUS AND WHERE BRANCH ------------------------------------------------
		SET @tSql1	=   ' WHERE DT.FCXpdNetAfHD <> 0 AND HD.FTXphStaDoc	= ''1'' AND HD.FTXphStaApv = ''1'' '

		IF @pnFilterType = '1'
		BEGIN
			IF (@tBchF <> '' AND @tBchT <> '')
			BEGIN
				SET @tSql1 +=' AND HD.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
			END
		END

		IF @pnFilterType = '2'
		BEGIN
			IF (@ptBchL <> '' )
			BEGIN
				SET @tSql1 +=' AND HD.FTBchCode IN (' + @ptBchL + ')'
			END
		END

		IF (@tDocDateF <> '' AND @tDocDateT <> '')
		BEGIN
			SET @tSql1	+=' AND CONVERT(VARCHAR(10),HD.FDXphDocDate,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''''
		END

	-------------------------------------------------------------------------------------------------------------------------------

	-------------------------------------------------------- WHERE PRODUCT --------------------------------------------------------
		SET @tSql2	= 'WHERE 1=1 '

		IF (@tPdtCodeF <> '' AND @tPdtCodeT <> '')
		BEGIN
			SET @tSql2 +=' AND PDT.FTPdtCode BETWEEN ''' + @tPdtCodeF + ''' AND ''' + @tPdtCodeT + ''''
		END

		IF (@tPdtChanF <> '' AND @tPdtChanT <> '')
		BEGIN
			SET @tSql2 +=' AND PDT.FTPgpChain BETWEEN ''' + @tPdtChanF + ''' AND ''' + @tPdtChanT + ''''
		END

		IF (@tPdtTypeF <> '' AND @tPdtTypeT <> '')
		BEGIN
			SET @tSql2 +=' AND PDT.FTPtyCode BETWEEN ''' + @tPdtTypeF + ''' AND ''' + @tPdtTypeT + ''''
		END

		IF(@tCate1F <> '')
		BEGIN 
			SET @tSql2 += ' AND PDTC.FTPdtCat1 IN ('+@tCate1F+') ' 
		END
			
		IF(@tCate2F <> '')
		BEGIN 
			SET @tSql2 += ' AND PDTC.FTPdtCat2 IN ('+@tCate2F+') ' 
		END
	-------------------------------------------------------------------------------------------------------------------------------

	-- ลบข้อมูล Temp ของเครื่องที่จะบันทึกขอมูลลง Temp
	DELETE FROM TRPTSaleFCCompVDTmp WITH (ROWLOCK) WHERE FTComName =  '' + @nComName + ''  AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''


	SET @tSql	 = ' INSERT INTO TRPTSaleFCCompVDTmp ('
	SET @tSql	+= '	FTComName,FTRptCode,FTUsrSession,'
	SET @tSql	+= '	FNAppType,FTAgnCode,FTAgnName,FTBchCode,FTBchName,FTPdtCode,FTPdtName,FTPgpChain,FTPgpChainName,FTPtyCode,'
	SET @tSql	+= '	FTPtyName,FTPdtCat1,FTCatName1,FTPdtCat2,FTCatName2,FCXpdNetAfHDHQ,FCXpdPerPoByHQ,FCXpdNetAfHDVD,FCXpdPerPoByVD'
	SET @tSql	+= ')'
	SET @tSql	+= ' SELECT '''+ @nComName + ''' AS FTComName,'''+ @tRptCode +''' AS FTRptCode, '''+ @tUsrSession +''' AS FTUsrSession,1 AS FNAppType,'
	SET @tSql	+= '	HD.FTAgnCode,AGNL.FTAgnName,HD.FTBchCode,BCHL.FTBchName,HD.FTPdtCode,PDTL.FTPdtName,PDT.FTPgpChain,PDTG.FTPgpChainName,PDT.FTPtyCode,PDTT.FTPtyName,'
	SET @tSql	+= '	PDTC.FTPdtCat1,PCAT1.FTCatName AS FTCatName1,PDTC.FTPdtCat2,PCAT2.FTCatName AS FTCatName2,'
	SET @tSql	+= '	HD.FCXpdNetAfHDHQ,'
	SET @tSql	+= '	CASE WHEN HD.FCXpdNetAfHDTotal = 0 THEN 100 ELSE ROUND((HD.FCXpdNetAfHDHQ * 100) / HD.FCXpdNetAfHDTotal,2) END AS FCXpdPerPoByHQ,'
	SET @tSql	+= '	HD.FCXpdNetAfHDVD,'
	SET @tSql	+= '	CASE WHEN HD.FCXpdNetAfHDTotal = 0 THEN 100 ELSE ROUND((HD.FCXpdNetAfHDVD * 100) / HD.FCXpdNetAfHDTotal,2) END AS FCXpdPerPoByVD'
	SET @tSql	+= ' FROM ('
	SET @tSql	+= '	SELECT '
	SET @tSql	+= '		HD.FTAgnCode,HD.FTBchCode,DT.FTPdtCode,'
	SET @tSql	+= '		SUM(DT.FCXpdNetAfHD) AS FCXpdNetAfHDTotal,'
	SET @tSql	+= '		SUM(CASE WHEN HD.FTSplCode = '''+@tSysSplCode+'''	THEN ROUND(DT.FCXpdNetAfHD,2) ELSE 0 END) AS FCXpdNetAfHDHQ,'
	SET @tSql	+= '		SUM(CASE WHEN HD.FTSplCode <> '''+@tSysSplCode+'''	THEN ROUND(DT.FCXpdNetAfHD,2) ELSE 0 END) AS FCXpdNetAfHDVD'
	SET @tSql	+= '	FROM TAPTPiHD HD WITH(NOLOCK)'
	SET @tSql	+= '	LEFT JOIN TAPTPiDT DT WITH(NOLOCK) ON HD.FTBchCode = DT.FTBchCode AND HD.FTXphDocNo = DT.FTXphDocNo'
	SET @tSql	+= @tSql1
	SET @tSql	+= '	GROUP BY HD.FTAgnCode,HD.FTBchCode,DT.FTPdtCode'
	SET @tSql	+= ' ) HD'
	SET @tSql	+= ' LEFT JOIN TCNMAgency_L AGNL WITH(NOLOCK) ON HD.FTAgnCode = AGNL.FTAgnCode AND AGNL.FNLngID	= '''+CAST(@nLngID AS VARCHAR(10))+''' '
	SET @tSql	+= ' LEFT JOIN TCNMBranch_L	BCHL WITH(NOLOCK) ON HD.FTBchCode = BCHL.FTBchCode AND BCHL.FNLngID	= '''+CAST(@nLngID AS VARCHAR(10))+''' '
	SET @tSql	+= ' LEFT JOIN TCNMPdt PDT WITH(NOLOCK) ON HD.FTPdtCode	= PDT.FTPdtCode'
	SET @tSql	+= ' LEFT JOIN TCNMPdt_L PDTL WITH(NOLOCK) ON HD.FTPdtCode = PDTL.FTPdtCode	AND PDTL.FNLngID = '''+CAST(@nLngID AS VARCHAR(10))+''' '
	SET @tSql	+= ' LEFT JOIN TCNMPdtCategory PDTC	WITH(NOLOCK) ON HD.FTPdtCode = PDTC.FTPdtCode'
	SET @tSql	+= ' LEFT JOIN TCNMPdtGrp_L PDTG WITH(NOLOCK) ON PDT.FTPgpChain	= PDTG.FTPgpChain AND PDTG.FNLngID = '''+CAST(@nLngID AS VARCHAR(10))+''' '
	SET @tSql	+= ' LEFT JOIN TCNMPdtType_L PDTT WITH(NOLOCK) ON PDT.FTPtyCode	= PDTT.FTPtyCode AND PDTT.FNLngID  = '''+CAST(@nLngID AS VARCHAR(10))+''' '
	SET @tSql	+= ' LEFT JOIN TCNMPdtCatInfo_L	PCAT1 WITH(NOLOCK) ON PDTC.FTPdtCat1 = PCAT1.FTCatCode	AND PCAT1.FNLngID = '''+CAST(@nLngID AS VARCHAR(10))+''' '
	SET @tSql	+= ' LEFT JOIN TCNMPdtCatInfo_L	PCAT2 WITH(NOLOCK) ON PDTC.FTPdtCat2 = PCAT2.FTCatCode	AND PCAT2.FNLngID = '''+CAST(@nLngID AS VARCHAR(10))+''' '
	SET @tSql	+= @tSql2
	SET @tSql	+= 'ORDER BY HD.FTAgnCode,HD.FTBchCode,HD.FTPdtCode'


	-- PRINT @tSql
	EXECUTE(@tSql)

END TRY
BEGIN CATCH 
	SET @FNResult= -1
END CATCH	
GO

/****** Object:  StoredProcedure [dbo].[SP_RPTxStockAllCompareTextfile]    Script Date: 5/9/2565 16:36:59 ******/
DROP PROCEDURE [dbo].[SP_RPTxStockAllCompareTextfile]
GO

/****** Object:  StoredProcedure [dbo].[SP_RPTxStockAllCompareTextfile]    Script Date: 5/9/2565 16:36:59 ******/
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
	SET @tSQL	+= '	SUM(pdt.FCPdtCostStd) AS FCXtdCost,'
	SET @tSQL	+= '	SUM((CRD.FCStkQty * pdt.FCPdtCostStd)) AS FCXtdAmount'
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
	SET @tSQL	+= ' WHERE CRD.FCStkQty > 0  AND ISNULL( PKS.FCPdtUnitFact, 0 ) = 1 '
	SET @tSQL	+= @tSQLFilter
	SET @tSQL	+= ' GROUP BY CRD.FTBchCode,CRD.FTPdtCode,PDTL.FTPdtName,FTPunName,BCL.FTBchName,PUN.FTPunCode,PDTL.FTPdtName,MAP90.FTMapUsrValue,CAT.FTPdtCat1,CAT.FTPdtCat2,inl.FTCatName,inl.FTCatName,GL.FTPgpName,BC.FTBchRefID '
	SET @tSQL	+= ' ORDER BY CRD.FTBchCode,BCL.FTBchName,CRD.FTPdtCode'


-- 	print @tSQL
 	EXECUTE(@tSQL)

	 return 0
END TRY	
BEGIN CATCH

	SET @pnResult = -1
	RETURN @pnResult
	
END CATCH
GO



/****** Object:  StoredProcedure [dbo].[SP_RPTxTrfpmtinf]    Script Date: 5/9/2565 16:39:25 ******/
DROP PROCEDURE [dbo].[SP_RPTxTrfpmtinf]
GO

/****** Object:  StoredProcedure [dbo].[SP_RPTxTrfpmtinf]    Script Date: 5/9/2565 16:39:25 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

-- =============================================
-- Author		: รายงาน - ข้อมูลจ่ายโอนรับโอน
-- Create date	: 31/08/2022 Wasin
-- =============================================
CREATE PROCEDURE [dbo].[SP_RPTxTrfpmtinf]
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

	SET @nLngID			= @pnLngID
	SET @nComName		= @pnComName
	SET @tUsrSession	= @ptUsrSession
	SET @tRptCode		= @ptRptCode
	SET @tPdtF			= @ptPdtF
	SET @tPdtT			= @ptPdtT
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

	-- PRINT @tSql
	EXECUTE(@tSql)

END TRY
BEGIN CATCH 
	SET @FNResult= -1
END CATCH	
GO
