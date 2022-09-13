SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE [dbo].[SP_RPTxSaleAllBchByPdt]

AS
BEGIN TRY

DECLARE @tSQL VARCHAR(MAX)
SET @tSQL = ' '

DECLARE @tDateGen VARCHAR(6)
SET @tDateGen = CONVERT(VARCHAR(6), GETDATE(), 112)
--SET @tDateGen = '202204'

DECLARE @tTempName VARCHAR(100)
SET @tTempName = 'TRPTSaleAllBchByPdt'+@tDateGen

DECLARE @tSQLDropTemp NVARCHAR(MAX);
SET @tSQLDropTemp = '';
IF OBJECT_ID(N'dbo.' + @tTempName, N'U') IS NOT NULL
    BEGIN
        SET @tSQLDropTemp+=' DROP TABLE ' + @tTempName;
        EXECUTE (@tSQLDropTemp);
END
	

SET @tSQL += ' SELECT D.FTBchCode, '
       SET @tSQL += ' CAT.FTPdtCat1, ' 
       SET @tSQL += ' CATL.FTCatName AS FTCatName1, '
       SET @tSQL += ' CAT.FTPdtCat2, '
       SET @tSQL += ' CATL2.FTCatName AS FTCatName2, '
       SET @tSQL += ' SUM(D.FCXsdQty) AS FCXsdQty, '
       SET @tSQL += ' SUM(D.FCXsdNetAfHD) AS FCXsdNetAfHD '
	   SET @tSQL += ' INTO ' + @tTempName
SET @tSQL += ' FROM '
SET @tSQL += ' ( '
    SET @tSQL += ' SELECT HD.FTBchCode, '
           SET @tSQL += ' DT.FTPdtCode, '
           SET @tSQL += ' COUNT(CASE '
                     SET @tSQL += ' WHEN HD.FNXshDocType = 1 '
                     SET @tSQL += ' THEN DT.FCXsdQty * 1 '
                     SET @tSQL += ' ELSE DT.FCXsdQty * -1 '
                 SET @tSQL += ' END) AS FCXsdQty, '
           SET @tSQL += ' SUM(CASE '
                   SET @tSQL += ' WHEN HD.FNXshDocType = 1 '
                   SET @tSQL += ' THEN DT.FCXsdNetAfHD * 1 '
                   SET @tSQL += ' ELSE DT.FCXsdNetAfHD * -1 '
               SET @tSQL += ' END) AS FCXsdNetAfHD '
    SET @tSQL += ' FROM TPSTSalDT DT WITH(NOLOCK) '
         SET @tSQL += ' LEFT JOIN TPSTSalHD HD WITH(NOLOCK) ON DT.FTBchCode = HD.FTBchCode '
                                                SET @tSQL += ' AND DT.FTXshDocNo = HD.FTXshDocNo '
    SET @tSQL += ' WHERE HD.FTXshStaDoc = ''1'' '
          SET @tSQL += ' AND CONVERT(VARCHAR(6), HD.FDXshDocDate, 112) = '''+@tDateGen+''''
    SET @tSQL += ' GROUP BY HD.FTBchCode, '
             SET @tSQL += ' DT.FTPdtCode '
SET @tSQL += ' ) D '
SET @tSQL += ' INNER JOIN TCNMPdtCategory CAT WITH(NOLOCK) ON D.FTPdtCode = CAT.FTPdtCode '
SET @tSQL += ' LEFT JOIN TCNMPdtCatInfo_L CATL WITH(NOLOCK) ON CAT.FTPdtCat1 = CATL.FTCatCode '
                                                SET @tSQL += ' AND CATL.FNLngID = 1 '
SET @tSQL += ' LEFT JOIN TCNMPdtCatInfo_L CATL2 WITH(NOLOCK) ON CAT.FTPdtCat2 = CATL2.FTCatCode '
                                                 SET @tSQL += ' AND CATL2.FNLngID = 1 '

SET @tSQL += ' GROUP BY 	
								D.FTBchCode,
								CAT.FTPdtCat1,
								CATL.FTCatName,
								CAT.FTPdtCat2,
								CATL2.FTCatName '

EXEC(@tSQL)
--PRINT(@tSQL)

END TRY
BEGIN CATCH
 return -1
END CATCH
GO
/****** Object:  StoredProcedure [dbo].[SP_RPTxSalesByCountBills]    Script Date: 19/05/2022 12:19:41 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[SP_RPTxSalesByCountBills]
	@pnLngID int , 
	@ptUsrSession Varchar(255),
	@ptAgnL Varchar(8000), 
	@ptBchL Varchar(8000), 
	@ptMonth Varchar(2), 
	@ptYear Varchar(4),
	@ptDocDateF Varchar(10),
    @ptDocDateT Varchar(10),
	@FNResult INT OUTPUT 
AS
BEGIN TRY

		DECLARE @nLngID int 
		DECLARE @tUsrSession Varchar(255)
		DECLARE @tSql VARCHAR(8000)
		DECLARE @tSql1 VARCHAR(8000)
		DECLARE @tMonth VARCHAR(100)
		DECLARE @tYear VARCHAR(100)

		---------------------------------------

		SET @nLngID 			= @pnLngID
		SET @tUsrSession 	= @ptUsrSession
		SET @tMonth 			= @ptMonth
		SET @tYear 			  = @ptYear
		SET @FNResult 		= 0

		---------------------------------------
			
		SET @tSql1 = ''

		IF @nLngID = null
		BEGIN
			SET @nLngID = 1
		END	

		IF (@ptBchL <> '' )
		BEGIN
			SET @tSql1 +=' AND HD.FTBchCode IN (' + @ptBchL + ')'
		END

		  IF((@ptDocDateF <> '' OR @ptDocDateF <> NULL) AND (@ptDocDateT <> '' OR @ptDocDateT <> NULL) )
            BEGIN
                SET @tSql1 += ' AND  CONVERT(VARCHAR(10),HD.FDXshDocDate,121) BETWEEN ''' + @ptDocDateF + ''''+ ' AND ''' + @ptDocDateT + ''' '
            END
            ELSE
            BEGIN
                SET @tSql1 += ' ' 
            END
		
		IF(@ptMonth <> '' OR @ptMonth <> NULL)
		BEGIN
				SET @tSql1 += ' AND MONTH(HD.FDXshDocDate) =  ''' + @ptMonth + ''' '
		END
		ELSE
		BEGIN
				SET @tSql1 += '' 
		END
		IF(@ptYear <> '' OR @ptYear <> NULL)
		BEGIN
				SET @tSql1 += ' AND YEAR(HD.FDXshDocDate) =  ''' + @ptYear + ''' '
		END
		ELSE
		BEGIN
				SET @tSql1 += '' 
		END 

		-- Delete
		DELETE FROM TRPTSalesByCountBillsTmp WITH (ROWLOCK) WHERE FTUsrSession = '' + @tUsrSession + ''
			
		-- Insert 
		SET @tSql = '  INSERT INTO TRPTSalesByCountBillsTmp'
		SET @tSql += ' (FTUsrSession,'
		SET @tSql += ' 	FTBchCode,
										FTBchName,
										FNBillCount,
										FNBillPerDay,
										FCXsdQty,
										FCXshTotal,
										FCXshDis,
										FCXshGrand,
										FCXshVatable,
										FCXshTotalVat '
		SET @tSql += ' )'
		SET @tSql += ' SELECT ''' + @tUsrSession + ''' AS FTUsrSession,'
		SET @tSql += ' HD.FTBchCode,
										BCHL.FTBchName,
										COUNT ( HD.FTXshDocNo ) AS FNBillCount,
										COUNT ( HD.FTXshDocNo )/31.0 AS FNBillPerDay,
										DT.QTY AS FCXsdQty,
										SUM(HD.FCXshGrand)-(SUM(HD.FCXshDis)+SUM(HD.FCXshChg)) AS FCXshTotal,
										SUM(HD.FCXshDis)+SUM(HD.FCXshChg) AS FCXshDis,
										SUM(HD.FCXshGrand) AS FCXshGrand,
										SUM(HD.FCXshVatable) AS FCXshVatable,
										SUM(HD.FCXshVatable)*1/107 AS FCXshTotalVat'
		SET @tSql += ' FROM'
		SET @tSql += ' TPSTSalHD HD
									LEFT JOIN ( SELECT FTBchCode, SUM ( FCXsdQty ) AS QTY FROM TPSTSalDT DT GROUP BY FTBchCode ) DT ON DT.FTBchCode = HD.FTBchCode
									LEFT JOIN TCNMBranch_L BCHL ON BCHL.FTBchCode = HD.FTBchCode 
									AND BCHL.FNLngID = ''' + CAST(@nLngID  AS VARCHAR(10)) + ''' WHERE 1=1 '
		SET @tSql +=  @tSql1
		SET @tSql += ' GROUP BY
									HD.FTBchCode,
									BCHL.FTBchName,
									DT.QTY '

		PRINT @tSQL
		EXECUTE(@tSql)	
		RETURN @FNResult
END TRY
BEGIN CATCH 
		SET @FNResult = -1
		RETURN @FNResult
END CATCH
GO
/****** Object:  StoredProcedure [dbo].[SP_RPTxWithholdingtaxTmp]    Script Date: 19/05/2022 12:19:41 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- =============================================
-- Author:		รายงาน ข้อมูล WHT
-- Create date: 21/04/2022 Wasin
-- Description:	
-- =============================================
CREATE PROCEDURE [dbo].[SP_RPTxWithholdingtaxTmp]
	@ptRptCode		Varchar(100),
	@ptUsrSessionID	VARCHAR(100),
	@ptAgnCode		VARCHAR(20),
	@ptBchCode		VARCHAR(255),
	@pdDocDateFrm 	VARCHAR(10),
	@pdDocDateTo	VARCHAR(10),
	@ptRcvCodeFrm 	VARCHAR(20),
	@ptRcvCodeTo	VARCHAR(20),
	@ptCstCodeFrm 	VARCHAR(20),
	@ptCstCodeTo	VARCHAR(20),
	@pnLangID		INT , 
	@pnResult		INT OUTPUT
AS
BEGIN TRY

	DECLARE @tSQL VARCHAR(MAX)
	SET @tSQL		= ''
	
	DECLARE @tSQLFilter VARCHAR(MAX)
	SET @tSQLFilter = ''

	DECLARE @tSQLFilterRC VARCHAR(MAX)
	SET @tSQLFilterRC = ''

	
	-- Filter Branch Code
	IF (@ptBchCode <> '' OR @ptBchCode <> NULL)
	BEGIN
		SET @tSQLFilter	+= ' AND BL.FTBchCode IN ( ' +  @ptBchCode + ' ) '
	END
	
	-- Filter Document Date
	IF ((@pdDocDateFrm <> '' OR @pdDocDateFrm <> NULL) AND  (@pdDocDateTo <> '' OR @pdDocDateTo <> NULL))
	BEGIN
		SET @tSQLFilter += 'AND CONVERT(datetime,HD.FDXshDocDate) BETWEEN CONVERT(datetime,''' + @pdDocDateFrm + ''') AND CONVERT(datetime,''' + @pdDocDateTo + ''') '
	END

	-- Filter Customer
	IF ((@ptCstCodeFrm <> '' OR @ptCstCodeFrm <> NULL) AND  (@ptCstCodeTo <> '' OR @ptCstCodeTo <> NULL))
	BEGIN
		SET @tSQLFilterRC += ' AND HD.FTCstCode BETWEEN ''' + @ptCstCodeFrm + ''' AND ''' + @ptCstCodeTo + ''' '
	END
	
	-- Filter Recive 
	IF ((@ptRcvCodeFrm <> '' OR @ptRcvCodeFrm <> NULL) AND  (@ptRcvCodeTo <> '' OR @ptRcvCodeTo <> NULL))
	BEGIN
		SET @tSQLFilterRC += ' AND RC.FTRcvCode BETWEEN ''' + @ptRcvCodeFrm + ''' AND ''' + @ptRcvCodeTo + ''' '
	END
	
	
	-- Delete Table Temp
	DELETE FROM TRPTTaxWithholdingtaxTmp WHERE FTUsrSession = ''+@ptUsrSessionID+''
	
	SET @tSQL	+= ' INSERT INTO TRPTTaxWithholdingtaxTmp ( '
	SET @tSQL	+= '	FTRptCode,FTUsrSession,FTBchCode,FTBchName,FTCstCode,FTCstName,FDXshDocDate,FTXshDocNo,FCXshVat,FCXshVatable,FCXshGrand,FTXrcRefNo1,FCXrcNet,FTRcvName,FTXshDocNoRef'
	SET @tSQL	+= ' )'
	SET @tSQL	+= ' SELECT'
	SET @tSQL	+=		''''+@ptRptCode +''' AS FTRptCode, '''+ @ptUsrSessionID +''' AS FTUsrSession,'
	SET @tSQL	+= '	BL.FTBchCode		AS FTBchCode,'
	SET @tSQL	+= '	BL.FTBchName		AS FTBchName,'
	SET @tSQL	+= '	HD.FTCstCode		AS FTCstCode,'
	SET @tSQL	+= '	HDcst.FTXshCstName	AS FTCstName,'
	SET @tSQL	+= '	HD.FDXshDocDate		AS FDXshDocDate,'
	SET @tSQL	+= '	HD.FTXshDocNo		AS FTXshDocNo,'
	SET @tSQL	+= '	HD.FCXshVat			AS FCXshVat,'
	SET @tSQL	+= '	HD.FCXshVatable		AS FCXshVatable,'
	SET @tSQL	+= '	HD.FCXshGrand		AS FCXshGrand,'
	SET @tSQL	+= '	RC1.FTXrcRefNo1		AS FTXrcRefNo1,'
	SET @tSQL	+= '	RC1.FCXrcNet		AS FCXrcNet,'
	SET @tSQL	+= '	RCData.FTRcvName	AS FTRcvName,'
	SET @tSQL	+= '	TAX.FTXshRefDocNo	AS FTXshDocNoRef'
	SET @tSQL	+= ' FROM TPSTSalHD HD WITH(NOLOCK)'
	SET @tSQL	+= ' LEFT JOIN TPSTSalHDcst HDcst	WITH(NOLOCK) ON HD.FTXshDocNo = HDcst.FTXshDocNo'
	SET @tSQL	+= ' LEFT JOIN TCNMBranch_L BL		WITH(NOLOCK) ON HD.FTBchCode = BL.FTBchCode'
	SET @tSQL	+= ' LEFT JOIN TPSTWhTaxHDDocRef TAX WITH(NOLOCK) ON HD.FTXshDocNo = TAX.FTXshDocNo AND HD.FTBchCode = TAX.FTBchCode AND TAX.FTXshRefType = 3 AND TAX.FTXshRefKey = ''RefExt''' 
	SET @tSQL	+= ' LEFT JOIN ('
	SET @tSQL	+= '	SELECT  FTXshDocNo,  SUBSTRING(d.FTRcvName,1, LEN(d.FTRcvName) - 1) FTRcvName'
	SET @tSQL	+= '	FROM ('
	SET @tSQL	+= '		SELECT DISTINCT FTXshDocNo'
	SET @tSQL	+= '		FROM TPSTSalRC WITH(NOLOCK)'
	SET @tSQL	+= '	) A'
	SET @tSQL	+= '	CROSS APPLY ('
	SET @tSQL	+= '		SELECT RC.FTRcvName + '','''
	SET @tSQL	+= '	FROM TPSTSalRC AS RC WITH(NOLOCK)'
	SET @tSQL	+= '	WHERE A.FTXshDocNo = RC.FTXshDocNo'
	SET @tSQL	+= '	FOR XML PATH('''')'
	SET @tSQL	+= '	) D (FTRcvName)'
	SET @tSQL	+= ' ) RCData ON HD.FTXshDocNo = RCData.FTXshDocNo'
	SET @tSQL	+= ' LEFT JOIN TPSTSalRC RC1  ON HD.FTXshDocNo = RC1.FTXshDocNo AND RC1.FTRcvCode = ''014'''
	SET @tSQL	+= ' WHERE  HD.FTXshDocNo IN ('
	SET @tSQL	+= '	SELECT  FTXshDocNo'
	SET @tSQL	+= '	FROM TPSTSalRC RC WITH(NOLOCK)'
	SET @tSQL	+= '	WHERE RC.FDCreateOn <> '''''
	SET @tSQL	+= '	AND FTRcvCode = ''014'' '
	SET @tSQL	+= @tSQLFilter
	SET @tSQL	+= ' )'
	 

	 --PRINT @tSQL
	EXECUTE(@tSQL)
	return 0
END TRY	
BEGIN CATCH
	SET @pnResult = -1
	RETURN @pnResult
END CATCH
GO

-- =============================================
-- Author:	รายงาน - การคืนสินค้าตามวันที่
-- Create date: 20/07/2022 Wasin
-- Description:	
-- =============================================

/****** Object:  StoredProcedure [dbo].[SP_RPTxSalPdtRetTmp]    Script Date: 20/7/2565 10:46:03 ******/
DROP PROCEDURE [dbo].[SP_RPTxSalPdtRetTmp]
GO

/****** Object:  StoredProcedure [dbo].[SP_RPTxSalPdtRetTmp]    Script Date: 20/7/2565 10:46:03 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[SP_RPTxSalPdtRetTmp]
	@pnLngID INT , 
	@ptComName VARCHAR(100),
	@ptRptCode VARCHAR(100),
	@ptUsrSession VARCHAR(255),
	@pnFilterType INT, --1 BETWEEN 2 IN
	--สาขา
	@ptBchL VARCHAR(8000), --กรณี Condition IN
	@ptBchF VARCHAR(5),
	@ptBchT VARCHAR(5),
	--Shop Code
	@ptShpL VARCHAR(8000), --กรณี Condition IN
	@ptShpF VARCHAR(10),
	@ptShpT VARCHAR(10),
	--เครื่องจุดขาย
	@ptPosL VARCHAR(8000), --กรณี Condition IN
	@ptPosF VARCHAR(20),
	@ptPosT VARCHAR(20),
	--แคชเชียร์
	--@ptUsrL VARCHAR(8000), --กรณี Condition IN
	@ptUsrF VARCHAR(20),
	@ptUsrT VARCHAR(20),
	--วันที่เอกสาร
	@ptDocDateF VARCHAR(50),
	@ptDocDateT VARCHAR(50),

	@FNResult INT OUTPUT	
AS
BEGIN TRY
	DECLARE @tSQL VARCHAR(MAX)
	DECLARE @tSQL_Filter VARCHAR(MAX)
	DECLARE @nLngID INT
	DECLARE @tComName VARCHAR(100)
	DECLARE @tRptCode VARCHAR(100)
	DECLARE @tUsrSession VARCHAR(255)
	--Branch Code
	DECLARE @tBchF VARCHAR(5)
	DECLARE @tBchT VARCHAR(5)
	--Shop Code
	DECLARE @tShpF VARCHAR(10)
	DECLARE @tShpT VARCHAR(10)
	--Pos Code
	DECLARE @tPosF VARCHAR(20)
	DECLARE @tPosT VARCHAR(20)
	--Cashier
	DECLARE @tUsrF VARCHAR(20)
	DECLARE @tUsrT VARCHAR(20)
	--วันที่
	DECLARE @tDocDateF VARCHAR(50)
	DECLARE @tDocDateT VARCHAR(50)
	 
	SET @nLngID = @pnLngID
	SET @tComName = @ptComName
	SET @tRptCode = @ptRptCode
	SET @tUsrSession = @ptUsrSession
	--Branch
	SET @tBchF  = @ptBchF
	SET @tBchT  = @ptBchT
	--Shop
	SET @tShpF  = @ptShpF
	SET @tShpT  = @ptShpT
	--Pos
	SET @tPosF  = @ptPosF 
	SET @tPosT  = @ptPosT
	--Cashier
	SET @tUsrF  = @ptUsrF 
	SET @tUsrT  = @ptUsrT
	--วันที่
	SET @tDocDateF = @ptDocDateF
	SET @tDocDateT = @ptDocDateT

	SET @tSQL_Filter = ''

	/*===== ตรวจสอบค่า Filter ========================================*/

	IF @ptBchL = null OR @ptBchL = ''
	BEGIN
		SET @ptBchL = ''
	END

	IF @tBchF = null OR @tBchF = ''
	BEGIN
		SET @tBchF = ''
	END
	IF @tBchT = null OR @tBchT = ''
	BEGIN
		SET @tBchT = @tBchF
	END

	IF @ptShpL = null OR @ptShpL = ''
	BEGIN
		SET @ptShpL = ''
	END

	IF @tShpF = null OR @tShpF = ''
	BEGIN
		SET @tShpF = ''
	END
	IF @tShpT = null OR @tShpT = ''
	BEGIN
		SET @tShpT = @tShpF
	END

	IF @tPosF = null OR @tPosF = ''
	BEGIN
		SET @tPosF = ''
	END
	IF @tPosT = null OR @tPosT = ''
	BEGIN
		SET @tPosT = @tPosF
	END

	IF @tUsrF = null OR @tUsrF = ''
	BEGIN
		SET @tUsrF = ''
	END
	IF @tUsrT = null OR @tUsrT = ''
	BEGIN
		SET @tUsrT = @tUsrF
	END

	IF @pnFilterType = '1'
	BEGIN
		IF (@tBchF <> '' AND @tBchT <> '')
		BEGIN
			SET @tSQL_Filter +=' AND HD.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
		END

		IF (@tShpF <> '' AND @tShpT <> '')
		BEGIN
			SET @tSQL_Filter +=' AND HD.FTShpCode BETWEEN ''' + @tShpF + ''' AND ''' + @tShpT + ''''
		END

		IF (@tPosF <> '' AND @tPosT <> '')
		BEGIN
			SET @tSQL_Filter += ' AND HD.FTPosCode BETWEEN ''' + @tPosF + ''' AND ''' + @tPosT + ''''
		END		
	END

	IF @pnFilterType = '2'
	BEGIN
		IF (@ptBchL <> '' )
		BEGIN
			SET @tSQL_Filter +=' AND HD.FTBchCode IN (' + @ptBchL + ')'
		END

		IF (@ptShpL <> '')
		BEGIN
			SET @tSQL_Filter +=' AND HD.FTShpCode IN (' + @ptShpL + ')'
		END

		IF (@ptPosL <> '')
		BEGIN
			SET @tSQL_Filter += ' AND HD.FTPosCode IN (' + @ptPosL + ')'
		END		
	END

	IF (@tUsrF <> '' AND @tUsrT <> '')
	BEGIN
		SET @tSQL_Filter += ' AND HD.FTUsrCode BETWEEN ''' + @tUsrF + ''' AND ''' + @tUsrT + ''''
	END
	
	/*===== Filter วันที่เอกสาร =============================================*/
	--เช็ค Parameter จากวันที่
	IF(@tDocDateF = '' OR @tDocDateF = NULL)
		BEGIN 
			SET @tDocDateF = ''
	    END
	ELSE IF(@tDocDateF <> '')
	   BEGIN 
		   SET @tDocDateF = @tDocDateF
	   END

     --เช็ค Parameter ถึงวันที่
	IF(@tDocDateT = '' OR @tDocDateT = NULL)
	   BEGIN 
		   SET @tDocDateT = ''
	   END
	ELSE IF(@tDocDateT <> '')
	   BEGIN 
		   SET @tDocDateT = @tDocDateT
	   END
	IF (@tDocDateF <> '' AND @tDocDateT <> '')
		BEGIN
			SET @tSQL_Filter += ' AND ( (CONVERT(VARCHAR(10),HD.FDXshDocDate,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''') OR (CONVERT(VARCHAR(10),HD.FDXshDocDate,121) BETWEEN ''' + @tDocDateT + ''' AND ''' + @tDocDateF + ''') ) '
		END

	DELETE FROM TRPTSalPdtRetTmp WITH (ROWLOCK) WHERE FTComName = '' + @tComName + '' AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''

	SET @tSQL = '
		INSERT INTO TRPTSalPdtRetTmp 
		(FTComName,FTRptCode,FTUsrSession,FTXthTnsType,FNRowPartID,FTXihDocNo,FTXihRefIn,FDXihDocDate,FCXshRnd,FCXtdAmount,FTRsnName,FTUsrName,FTXshApvName,FTBchCode,
		FTBchName)'

	SET @tSQL += '
		SELECT 
			'''+@tComName+''' AS FTComName,
			'''+@tRptCode+''' AS FTRptCode,
			'''+@tUsrSession+''' AS FTUsrSession,
			HD.FTXshTnsType AS FTXthTnsType,
			HD.FNRowPartID,
			HD.FTXshDocNo AS FTXihDocNo,
			HD.FTXshRefInt AS FTXihRefIn,
			HD.FDXshDocDate AS FDXihDocDate,
			HD.FCXshRnd,
			HD.FCXshGrand AS FCXtdAmount,
			HD.FTRsnName,
			HD.FTUsrName,
			HD.FTUsrApvBy AS FTXshApvName,
			HD.FTBchCode,
			HD.FTBchName
		FROM (
			SELECT
				1 AS FTXshTnsType,
				0 AS FNRowPartID,
				HD.FTXshDocNo,
				ISNULL(HD.FTXshRefInt, ''N/A'') AS FTXshRefInt,
				HD.FDXshDocDate,
				HD.FCXshRnd,
				HD.FCXshGrand,
				ISNULL(RSN.FTRsnName, ''N/A'') AS FTRsnName,
				USR.FTUsrName,
				USR.FTUsrName AS FTUsrApvBy,
				HD.FTBchCode,
				BCHL.FTBchName
			FROM TPSTSalHD HD WITH (NOLOCK) 
			LEFT JOIN TCNMBranch_L BCHL WITH (NOLOCK) ON HD.FTBchCode = BCHL.FTBchCode
			AND BCHL.FNLngID = '''+ CAST(@nLngID AS VARCHAR(10)) +'''
			LEFT JOIN TCNMRsn_L RSN WITH (NOLOCK) ON HD.FTRsnCode = RSN.FTRsnCode
			AND RSN.FNLngID = '''+ CAST(@nLngID AS VARCHAR(10)) +'''
			LEFT JOIN TCNMUser_L USR WITH (NOLOCK) ON HD.FTUsrCode = USR.FTUsrCode
			AND USR.FNLngID = '''+ CAST(@nLngID AS VARCHAR(10)) +'''

			WHERE HD.FNXshDocType = 9
			AND HD.FTXshStaDoc = 1'
			--Where parameter ต่างๆได้ที่นี่
			SET @tSQL += @tSQL_Filter
		SET @tSQL += ') HD'



		SET @tSQL += '
		INSERT INTO TRPTSalPdtRetTmp 
		(FTComName,FTRptCode,FTUsrSession,FTXthTnsType,FNRowPartID,FTXihDocNo,FDXihDocDate,FTPdtCode,FTPdtName,FCXidQty,FTPunName,FCXtdAmount)'

		SET @tSQL += '
		SELECT 
			'''+@tComName+''' AS FTComName,
			'''+@tRptCode+''' AS FTRptCode,
			'''+@tUsrSession+''' AS FTUsrSession,
			DT.FTXshTnsType AS FTXthTnsType,
			DT.FNRowPartID,
			DT.FTXshDocNo AS FTXihDocNo,
			DT.FDXshDocDate AS FDXihDocDate,
			DT.FTPdtCode,
			DT.FTPdtName,
			DT.FCXsdQty AS FCXidQty,
			DT.FTPunName,
			DT.FCXsdNetAfHD AS FCXtdAmount
		FROM (
			SELECT
				2 AS FTXshTnsType,
				ROW_NUMBER() OVER (PARTITION BY DT.FTXshDocNo ORDER BY DT.FTPdtCode DESC) AS FNRowPartID,
				DT.FTXshDocNo,
				HD.FDXshDocDate,
				DT.FTPdtCode,
				PDT.FTPdtName,
				DT.FCXsdQty,
				DT.FTPunName,
				DT.FCXsdNetAfHD
			FROM TPSTSalDT DT WITH (NOLOCK)

			INNER JOIN TPSTSalHD HD WITH (NOLOCK) ON DT.FTXshDocNo = HD.FTXshDocNo
			AND DT.FTBchCode = HD.FTBchCode
			INNER JOIN TCNMPdt_L PDT WITH (NOLOCK) ON DT.FTPdtCode = PDT.FTPdtCode
			AND PDT.FNLngID = '''+ CAST(@nLngID AS VARCHAR(10)) +'''

			WHERE HD.FNXshDocType = 9
			AND HD.FTXshStaDoc = 1
			AND DT.FTXsdStaPdt <> 4'
			--Where parameter ต่างๆได้ที่นี่
			SET @tSQL += @tSQL_Filter
		SET @tSQL += ') DT'

	--PRINT @tSQL
	EXECUTE(@tSQL)

	SET @FNResult = 0
	RETURN @FNResult
END TRY	
BEGIN CATCH

    SET @FNResult = -1
	RETURN @FNResult

END CATCH
GO

-- =============================================
-- Author:	รายงาน - การคืนสินค้าข้ามวัน
-- Create date: 20/07/2022 Wasin
-- Description:	
-- =============================================

/****** Object:  StoredProcedure [dbo].[SP_RPTxSalPdtRetNextDate]    Script Date: 20/7/2565 10:50:11 ******/
DROP PROCEDURE [dbo].[SP_RPTxSalPdtRetNextDate]
GO

/****** Object:  StoredProcedure [dbo].[SP_RPTxSalPdtRetNextDate]    Script Date: 20/7/2565 10:50:11 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[SP_RPTxSalPdtRetNextDate]
	@pnLngID INT , 
	@ptComName VARCHAR(100),
	@ptRptCode VARCHAR(100),
	@ptUsrSession VARCHAR(255),
	@pnFilterType INT, --1 BETWEEN 2 IN
	--สาขา
	@ptBchL VARCHAR(8000), --กรณี Condition IN
	@ptBchF VARCHAR(5),
	@ptBchT VARCHAR(5),
	--Shop Code
	@ptShpL VARCHAR(8000), --กรณี Condition IN
	@ptShpF VARCHAR(10),
	@ptShpT VARCHAR(10),
	--เครื่องจุดขาย
	@ptPosL VARCHAR(8000), --กรณี Condition IN
	@ptPosF VARCHAR(20),
	@ptPosT VARCHAR(20),
	--แคชเชียร์
	--@ptUsrL VARCHAR(8000), --กรณี Condition IN
	@ptUsrF VARCHAR(20),
	@ptUsrT VARCHAR(20),
	--วันที่เอกสาร
	@ptDocDateF VARCHAR(50),
	@ptDocDateT VARCHAR(50),

	@FNResult INT OUTPUT	
AS
BEGIN TRY
	DECLARE @tSQL VARCHAR(MAX)
	DECLARE @tSQL_Filter VARCHAR(MAX)
	DECLARE @nLngID INT
	DECLARE @tComName VARCHAR(100)
	DECLARE @tRptCode VARCHAR(100)
	DECLARE @tUsrSession VARCHAR(255)
	--Branch Code
	DECLARE @tBchF VARCHAR(5)
	DECLARE @tBchT VARCHAR(5)
	--Shop Code
	DECLARE @tShpF VARCHAR(10)
	DECLARE @tShpT VARCHAR(10)
	--Pos Code
	DECLARE @tPosF VARCHAR(20)
	DECLARE @tPosT VARCHAR(20)
	--Cashier
	DECLARE @tUsrF VARCHAR(20)
	DECLARE @tUsrT VARCHAR(20)
	--วันที่
	DECLARE @tDocDateF VARCHAR(50)
	DECLARE @tDocDateT VARCHAR(50)
	 
	SET @nLngID = @pnLngID
	SET @tComName = @ptComName
	SET @tRptCode = @ptRptCode
	SET @tUsrSession = @ptUsrSession
	--Branch
	SET @tBchF  = @ptBchF
	SET @tBchT  = @ptBchT
	--Shop
	SET @tShpF  = @ptShpF
	SET @tShpT  = @ptShpT
	--Pos
	SET @tPosF  = @ptPosF 
	SET @tPosT  = @ptPosT
	--Cashier
	SET @tUsrF  = @ptUsrF 
	SET @tUsrT  = @ptUsrT
	--วันที่
	SET @tDocDateF = @ptDocDateF
	SET @tDocDateT = @ptDocDateT

	SET @tSQL_Filter = ''

	/*===== ตรวจสอบค่า Filter ========================================*/

	IF @ptBchL = null OR @ptBchL = ''
	BEGIN
		SET @ptBchL = ''
	END

	IF @tBchF = null OR @tBchF = ''
	BEGIN
		SET @tBchF = ''
	END
	IF @tBchT = null OR @tBchT = ''
	BEGIN
		SET @tBchT = @tBchF
	END

	IF @ptShpL = null OR @ptShpL = ''
	BEGIN
		SET @ptShpL = ''
	END

	IF @tShpF = null OR @tShpF = ''
	BEGIN
		SET @tShpF = ''
	END
	IF @tShpT = null OR @tShpT = ''
	BEGIN
		SET @tShpT = @tShpF
	END

	IF @tPosF = null OR @tPosF = ''
	BEGIN
		SET @tPosF = ''
	END
	IF @tPosT = null OR @tPosT = ''
	BEGIN
		SET @tPosT = @tPosF
	END

	IF @tUsrF = null OR @tUsrF = ''
	BEGIN
		SET @tUsrF = ''
	END
	IF @tUsrT = null OR @tUsrT = ''
	BEGIN
		SET @tUsrT = @tUsrF
	END

	IF @pnFilterType = '1'
	BEGIN
		IF (@tBchF <> '' AND @tBchT <> '')
		BEGIN
			SET @tSQL_Filter +=' AND HD.FTBchCode BETWEEN ''' + @tBchF + ''' AND ''' + @tBchT + ''''
		END

		IF (@tShpF <> '' AND @tShpT <> '')
		BEGIN
			SET @tSQL_Filter +=' AND HD.FTShpCode BETWEEN ''' + @tShpF + ''' AND ''' + @tShpT + ''''
		END

		IF (@tPosF <> '' AND @tPosT <> '')
		BEGIN
			SET @tSQL_Filter += ' AND HD.FTPosCode BETWEEN ''' + @tPosF + ''' AND ''' + @tPosT + ''''
		END		
	END

	IF @pnFilterType = '2'
	BEGIN
		IF (@ptBchL <> '' )
		BEGIN
			SET @tSQL_Filter +=' AND HD.FTBchCode IN (' + @ptBchL + ')'
		END

		IF (@ptShpL <> '')
		BEGIN
			SET @tSQL_Filter +=' AND HD.FTShpCode IN (' + @ptShpL + ')'
		END

		IF (@ptPosL <> '')
		BEGIN
			SET @tSQL_Filter += ' AND HD.FTPosCode IN (' + @ptPosL + ')'
		END		
	END

	IF (@tUsrF <> '' AND @tUsrT <> '')
	BEGIN
		SET @tSQL_Filter += ' AND HD.FTUsrCode BETWEEN ''' + @tUsrF + ''' AND ''' + @tUsrT + ''''
	END
	
	/*===== Filter วันที่เอกสาร =============================================*/
	--เช็ค Parameter จากวันที่
	IF(@tDocDateF = '' OR @tDocDateF = NULL)
		BEGIN 
			SET @tDocDateF = ''
	    END
	ELSE IF(@tDocDateF <> '')
	   BEGIN 
		   SET @tDocDateF = @tDocDateF
	   END

     --เช็ค Parameter ถึงวันที่
	IF(@tDocDateT = '' OR @tDocDateT = NULL)
	   BEGIN 
		   SET @tDocDateT = ''
	   END
	ELSE IF(@tDocDateT <> '')
	   BEGIN 
		   SET @tDocDateT = @tDocDateT
	   END
	IF (@tDocDateF <> '' AND @tDocDateT <> '')
		BEGIN
			SET @tSQL_Filter += ' AND ( (CONVERT(VARCHAR(10),HD.FDXshDocDate,121) BETWEEN ''' + @tDocDateF + ''' AND ''' + @tDocDateT + ''') OR (CONVERT(VARCHAR(10),HD.FDXshDocDate,121) BETWEEN ''' + @tDocDateT + ''' AND ''' + @tDocDateF + ''') ) '
		END

	DELETE FROM TRPTxSalPdtRetNextDateTmp WITH (ROWLOCK) WHERE FTComName = '' + @tComName + '' AND FTRptCode = '' + @tRptCode + '' AND FTUsrSession = '' + @tUsrSession + ''

	SET @tSQL = '
		INSERT INTO TRPTxSalPdtRetNextDateTmp 
		(FTComName,FTRptCode,FTUsrSession,FTXthTnsType,FNRowPartID,FTXihDocNo,FTXihRefIn,FDXihDocDate,FDXihSalDate,FCXshRnd,FCXtdAmount,FTRsnName,FTUsrName,FTXshApvName,FTBchCode,FTBchName)'

	SET @tSQL += '
		SELECT 
			'''+@tComName+''' AS FTComName,
			'''+@tRptCode+''' AS FTRptCode,
			'''+@tUsrSession+''' AS FTUsrSession,
			HD.FTXshTnsType AS FTXthTnsType,
			HD.FNRowPartID,
			HD.FTXshDocNo AS FTXihDocNo,
			HD.FTXshRefInt AS FTXihRefIn,
			HD.FDXshDocDate AS FDXihDocDate,
			S.FDXshDocDate AS FDXihSaleDate,
			HD.FCXshRnd,
			HD.FCXshGrand AS FCXtdAmount,
			HD.FTRsnName,
			HD.FTUsrName,
			HD.FTUsrApvBy AS FTXshApvName,
			HD.FTBchCode,
			BCHL.FTBchName
		FROM (
			SELECT
				1 AS FTXshTnsType,
				0 AS FNRowPartID,
				HD.FTBchCode,
				BCHL.FTBchName,
				HD.FTXshDocNo,
				ISNULL(HD.FTXshRefInt, ''N/A'') AS FTXshRefInt,
				HD.FDXshDocDate,
				HD.FCXshRnd,
				HD.FCXshGrand,
				ISNULL(RSN.FTRsnName, ''N/A'') AS FTRsnName,
				USR.FTUsrName,
				USR.FTUsrName AS FTUsrApvBy
			FROM TPSTSalHD HD WITH (NOLOCK)

			LEFT JOIN TCNMRsn_L RSN WITH (NOLOCK) ON HD.FTRsnCode = RSN.FTRsnCode
			AND RSN.FNLngID = '''+ CAST(@nLngID AS VARCHAR(10)) +'''
			LEFT JOIN TCNMBranch_L BCHL WITH ( NOLOCK ) ON HD.FTBchCode = BCHL.FTBchCode 
			AND BCHL.FNLngID = '''+ CAST(@nLngID AS VARCHAR(10)) +'''
			LEFT JOIN TCNMUser_L USR WITH (NOLOCK) ON HD.FTUsrCode = USR.FTUsrCode
			AND USR.FNLngID = '''+ CAST(@nLngID AS VARCHAR(10)) +'''

			WHERE HD.FNXshDocType = 9
			AND HD.FTXshStaDoc = 1'
			--Where parameter ต่างๆได้ที่นี่
		SET @tSQL += @tSQL_Filter
		SET @tSQL += ') HD INNER JOIN TPSTSalHD S ON HD.FTBchCode = S.FTBchCode AND HD.FTXshRefInt =  S.FTXshDocNo  AND S.FTXshStaDoc = 1'
		SET @tSQL += ' LEFT JOIN TCNMBranch_L BCHL WITH ( NOLOCK ) ON S.FTBchCode = BCHL.FTBchCode AND BCHL.FNLngID = '''+ CAST(@nLngID AS VARCHAR(10)) +''''
		SET @tSQL += ' WHERE CONVERT(VARCHAR(10),HD.FDXshDocDate,121) > CONVERT(VARCHAR(10),S.FDXshDocDate,121) '


		SET @tSQL += '
		INSERT INTO TRPTxSalPdtRetNextDateTmp 
		(FTComName,FTRptCode,FTUsrSession,FTXthTnsType,FNRowPartID,FTXihDocNo,FDXihDocDate,FTPdtCode,FTPdtName,FCXidQty,FTPunName,FCXtdAmount,FDXihSalDate)'

		SET @tSQL += '
		SELECT 
			'''+@tComName+''' AS FTComName,
			'''+@tRptCode+''' AS FTRptCode,
			'''+@tUsrSession+''' AS FTUsrSession,
			DT.FTXshTnsType AS FTXthTnsType,
			DT.FNRowPartID,
			DT.FTXshDocNo AS FTXihDocNo,
			DT.FDXshDocDate AS FDXihDocDate,
			DT.FTPdtCode,
			DT.FTPdtName,
			DT.FCXsdQty AS FCXidQty,
			DT.FTPunName,
			DT.FCXsdNetAfHD AS FCXtdAmount,
			S.FDXshDocDate AS FDXihSaleDate
		FROM (
			SELECT
				2 AS FTXshTnsType,
				ROW_NUMBER() OVER (PARTITION BY DT.FTXshDocNo ORDER BY DT.FTPdtCode DESC) AS FNRowPartID,
				DT.FTBchCode,
				DT.FTXshDocNo,
				ISNULL(HD.FTXshRefInt, ''N/A'') AS FTXshRefInt,
				HD.FDXshDocDate,
				DT.FTPdtCode,
				PDT.FTPdtName,
				DT.FCXsdQty,
				DT.FTPunName,
				DT.FCXsdNetAfHD
			FROM TPSTSalDT DT WITH (NOLOCK)

			INNER JOIN TPSTSalHD HD WITH (NOLOCK) ON DT.FTXshDocNo = HD.FTXshDocNo
			AND DT.FTBchCode = HD.FTBchCode
			INNER JOIN TCNMPdt_L PDT WITH (NOLOCK) ON DT.FTPdtCode = PDT.FTPdtCode
			AND PDT.FNLngID = '''+ CAST(@nLngID AS VARCHAR(10)) +'''

			WHERE HD.FNXshDocType = 9
			AND HD.FTXshStaDoc = 1
			AND DT.FTXsdStaPdt <> 4'
			--Where parameter ต่างๆได้ที่นี่
			SET @tSQL += @tSQL_Filter
		SET @tSQL += ') DT INNER JOIN TPSTSalHD S ON DT.FTBchCode = S.FTBchCode AND DT.FTXshRefInt =  S.FTXshDocNo  AND S.FTXshStaDoc = 1'
		SET @tSQL += ' WHERE CONVERT(VARCHAR(10),DT.FDXshDocDate,121) > CONVERT(VARCHAR(10),S.FDXshDocDate,121)'

	--PRINT @tSQL
	EXECUTE(@tSQL)

	SET @FNResult = 0
	RETURN @FNResult
END TRY	
BEGIN CATCH

    SET @FNResult = -1
	RETURN @FNResult

END CATCH
GO





