/* เพิ่มฟิวส์ */
IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TFNMRcv' AND COLUMN_NAME = 'FCRcvPayMin') BEGIN
	ALTER TABLE TFNMRcv ADD FCRcvPayMin NUMERIC(18,4)
END
GO

/* เพิ่มฟิวส์ */
IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TFNMRcv' AND COLUMN_NAME = 'FCRcvPayMax') BEGIN
	ALTER TABLE TFNMRcv ADD FCRcvPayMax NUMERIC(18,4)
END
GO

/* ขยายฟิวส์จากเดิม */
IF EXISTS(SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TACTPbHD' AND COLUMN_NAME = 'FTPrdCode' AND CHARACTER_MAXIMUM_LENGTH = 5) BEGIN
	ALTER TABLE TACTPbHD ALTER COLUMN FTPrdCode VARCHAR(20) NULL
END
GO


/****** Object:  StoredProcedure [dbo].[SP_RPTxWithholdingtaxTmp]    Script Date: 16/5/2565 11:13:30 ******/
DROP PROCEDURE [dbo].[SP_RPTxWithholdingtaxTmp]
GO

/****** Object:  StoredProcedure [dbo].[SP_RPTxWithholdingtaxTmp]    Script Date: 16/5/2565 11:13:30 ******/
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
	SET @tSQL	+= '	FTRptCode,FTUsrSession,FTBchCode,FTBchName,FTCstCode,FTCstName,FDXshDocDate,FTXshDocNo,FCXshVat,FCXshVatable,FCXshGrand,FTXrcRefNo1,FCXrcNet,FTRcvName'
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
	SET @tSQL	+= '	RCData.FTRcvName	AS FTRcvName'
	SET @tSQL	+= ' FROM TPSTSalHD HD WITH(NOLOCK)'
	SET @tSQL	+= ' LEFT JOIN TPSTSalHDcst HDcst	WITH(NOLOCK) ON HD.FTXshDocNo = HDcst.FTXshDocNo'
	SET @tSQL	+= ' LEFT JOIN TCNMBranch_L BL		WITH(NOLOCK) ON HD.FTBchCode = BL.FTBchCode'
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
	 

	PRINT @tSQL
	-- EXECUTE(@tSQL)
	return 0
END TRY	
BEGIN CATCH
	SET @pnResult = -1
	RETURN @pnResult
END CATCH
GO



/* เพิ่มฟิวส์ FTCstStaFC */
IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TCNMCst' AND COLUMN_NAME = 'FTCstStaFC') BEGIN
	ALTER TABLE TCNMCst ADD FTCstStaFC NUMERIC(18,4)
END
GO

-- =============================================
-- Author:	รายงาน - การคืนสินค้าตามวันที่
-- Create date: 20/07/2022 Wasin
-- Description:	
-- =============================================
DROP PROCEDURE [dbo].[TRPTSalPdtRetTmp]
GO

CREATE TABLE [dbo].[TRPTSalPdtRetTmp](
	[FTRptRowSeq] [bigint] IDENTITY(1,1) NOT NULL,
	[FNRowPartID] [bigint] NULL,
	[FTUsrSession] [varchar](255) NULL,
	[FTXihDocNo] [varchar](20) NULL,
	[FTXihRefIn] [varchar](20) NULL,
	[FDXihDocDate] [datetime] NULL,
	[FTPdtCode] [varchar](20) NULL,
	[FTPdtName] [varchar](100) NULL,
	[FCXidQty] [numeric](18, 4) NULL,
	[FCXshRnd] [numeric](18, 4) NULL,
	[FCXtdAmount] [numeric](18, 4) NULL,
	[FTPunCode] [varchar](5) NULL,
	[FTPunName] [varchar](255) NULL,
	[FTRsnCode] [varchar](5) NULL,
	[FTRsnName] [varchar](100) NULL,
	[FTUsrCode] [varchar](5) NULL,
	[FTUsrName] [varchar](50) NULL,
	[FTXshApvCode] [varchar](5) NULL,
	[FTXshApvName] [varchar](255) NULL,
	[FTXthTnsType] [varchar](1) NULL,
	[FTAppCode] [varchar](50) NULL,
	[FTAppName] [varchar](50) NULL,
	[FTComName] [varchar](50) NULL,
	[FTRptCode] [varchar](50) NULL,
	[FDTmpTxnDate] [datetime] NOT NULL,
	[FTBchCode] [varchar](20) NULL,
	[FTBchName] [varchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[FTRptRowSeq] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 70) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[TRPTSalPdtRetTmp] ADD  DEFAULT (getdate()) FOR [FDTmpTxnDate]
GO


-- =============================================
-- Author:	รายงาน - การคืนสินค้าข้ามวัน
-- Create date: 20/07/2022 Wasin
-- Description:	
-- =============================================
DROP PROCEDURE [dbo].[TRPTxSalPdtRetNextDateTmp]
GO

CREATE TABLE [dbo].[TRPTxSalPdtRetNextDateTmp](
	[FTRptRowSeq] [bigint] IDENTITY(1,1) NOT NULL,
	[FNRowPartID] [bigint] NULL,
	[FTUsrSession] [varchar](255) NULL,
	[FTXihDocNo] [varchar](20) NULL,
	[FTXihRefIn] [varchar](20) NULL,
	[FDXihDocDate] [datetime] NULL,
	[FTPdtCode] [varchar](20) NULL,
	[FTPdtName] [varchar](100) NULL,
	[FCXidQty] [numeric](18, 4) NULL,
	[FCXshRnd] [numeric](18, 4) NULL,
	[FCXtdAmount] [numeric](18, 4) NULL,
	[FTPunCode] [varchar](5) NULL,
	[FTPunName] [varchar](255) NULL,
	[FTRsnCode] [varchar](5) NULL,
	[FTRsnName] [varchar](100) NULL,
	[FTUsrCode] [varchar](5) NULL,
	[FTUsrName] [varchar](50) NULL,
	[FTXshApvCode] [varchar](5) NULL,
	[FTXshApvName] [varchar](255) NULL,
	[FTXthTnsType] [varchar](1) NULL,
	[FTAppCode] [varchar](50) NULL,
	[FTAppName] [varchar](50) NULL,
	[FTComName] [varchar](50) NULL,
	[FTRptCode] [varchar](50) NULL,
	[FDTmpTxnDate] [datetime] NOT NULL,
	[FDXihSalDate] [datetime] NULL,
	[FTBchCode] [varchar](20) NULL,
	[FTBchName] [varchar](255) NULL,
 CONSTRAINT [PK__TRPTSalP__F671FB6B1BAD70CE_copy1] PRIMARY KEY CLUSTERED 
(
	[FTRptRowSeq] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 70) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[TRPTxSalPdtRetNextDateTmp] ADD  DEFAULT (getdate()) FOR [FDTmpTxnDate]
GO

/****** Object:  Table [dbo].[TCNSDocCtl_L]    Script Date: 5/9/2565 18:21:46 ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[TCNSDocCtl_L]') AND type in (N'U'))
DROP TABLE [dbo].[TCNSDocCtl_L]
GO

/****** Object:  Table [dbo].[TCNSDocCtl_L]    Script Date: 5/9/2565 18:21:46 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[TCNSDocCtl_L](
	[FTDctCode] [varchar](5) NOT NULL,
	[FNLngID] [bigint] NOT NULL,
	[FTDctTable] [varchar](100) NOT NULL,
	[FTDctName] [varchar](150) NULL,
	[FTDctStaUse] [varchar](1) NULL,
 CONSTRAINT [PK_TCNSDocCtl_1] PRIMARY KEY CLUSTERED 
(
	[FTDctCode] ASC,
	[FNLngID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[TCNMPdtSpcCtl]    Script Date: 5/9/2565 18:25:18 ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[TCNMPdtSpcCtl]') AND type in (N'U'))
DROP TABLE [dbo].[TCNMPdtSpcCtl]
GO

/****** Object:  Table [dbo].[TCNMPdtSpcCtl]    Script Date: 5/9/2565 18:25:18 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[TCNMPdtSpcCtl](
	[FTPdtCode] [varchar](20) NOT NULL,
	[FTDctCode] [varchar](5) NOT NULL,
	[FTPscAlwCmp] [varchar](1) NULL,
	[FTPscAlwAD] [varchar](1) NULL,
	[FTPscAlwBch] [varchar](1) NULL,
	[FTPscAlwMer] [varchar](1) NULL,
	[FTPscAlwShp] [varchar](1) NULL,
	[FTPscAlwOwner] [varchar](1) NULL,
 CONSTRAINT [PK_TCNMPdtSpcCtl] PRIMARY KEY CLUSTERED 
(
	[FTPdtCode] ASC,
	[FTDctCode] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO


/****** Object:  Table [dbo].[TRPTTrfpmtinfTmp]    Script Date: 16/9/2565 16:59:45 ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[TRPTTrfpmtinfTmp]') AND type in (N'U'))
DROP TABLE [dbo].[TRPTTrfpmtinfTmp]
GO

/****** Object:  Table [dbo].[TRPTTrfpmtinfTmp]    Script Date: 16/9/2565 16:59:45 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[TRPTTrfpmtinfTmp](
	[FTRptRowSeq] [bigint] IDENTITY(1,1) NOT NULL,
	[FNRowPartID] [bigint] NULL,
	[FTBchRefIDFrm] [varchar](20) NULL,
	[FTBchCodeFrm] [varchar](20) NULL,
	[FTBchNameFrm] [varchar](255) NULL,
	[FTXthDocNoFrm] [varchar](20) NULL,
	[FDXthDocDateFrm] [datetime] NULL,
	[FTBchRefIDTo] [varchar](20) NULL,
	[FTBchCodeTo] [varchar](20) NULL,
	[FTBchNameTo] [varchar](255) NULL,
	[FTXthDocNoTo] [varchar](20) NULL,
	[FDXthDocDateTo] [datetime] NULL,
	[FTPdtCode] [varchar](20) NULL,
	[FTXtdPdtName] [varchar](255) NULL,
	[FTPunCode] [varchar](20) NULL,
	[FTPunName] [varchar](255) NULL,
	[FTPgpChain] [varchar](20) NULL,
	[FTPgpChainName] [varchar](255) NULL,
	[FTPtyCode] [varchar](20) NULL,
	[FTPtyName] [varchar](255) NULL,
	[FTPdtCatCode1] [varchar](50) NULL,
	[FTPdtCatName1] [varchar](255) NULL,
	[FTPdtCatCode2] [varchar](50) NULL,
	[FTPdtCatName2] [varchar](255) NULL,
	[FCXtdQty] [numeric](18, 4) NULL,
	[FTXtdUsrKey] [varchar](50) NULL,
	[FTComName] [varchar](50) NULL,
	[FTRptCode] [varchar](50) NULL,
	[FTUsrSession] [varchar](255) NULL,
	[FDTmpTxnDate] [datetime] NULL
) ON [PRIMARY]
GO


/****** Object:  Table [dbo].[TRPTStockAllCompareTextfileTmp]    Script Date: 16/9/2565 17:03:37 ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[TRPTStockAllCompareTextfileTmp]') AND type in (N'U'))
DROP TABLE [dbo].[TRPTStockAllCompareTextfileTmp]
GO

/****** Object:  Table [dbo].[TRPTStockAllCompareTextfileTmp]    Script Date: 16/9/2565 17:03:37 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[TRPTStockAllCompareTextfileTmp](
	[FTRptCode] [varchar](50) NULL,
	[FTUsrSession] [varchar](255) NULL,
	[FTBchCode] [varchar](50) NULL,
	[FTBchName] [varchar](255) NULL,
	[FTPdtCode] [varchar](50) NULL,
	[FTPdtName] [varchar](255) NULL,
	[FTPunCode] [varchar](50) NULL,
	[FTPunName] [varchar](255) NULL,
	[FTPdtCat1] [varchar](255) NULL,
	[FTPdtCat2] [varchar](255) NULL,
	[FTCatName] [varchar](255) NULL,
	[FTMapUsrValue] [varchar](255) NULL,
	[FCXtdQty] [numeric](18, 4) NULL,
	[FCXtdCost] [numeric](18, 4) NULL,
	[FCXtdAmount] [numeric](18, 4) NULL,
	[FTBchRefID] [varchar](255) NULL
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[TRPTSaleFCCompVDTmp]    Script Date: 16/9/2565 17:04:17 ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[TRPTSaleFCCompVDTmp]') AND type in (N'U'))
DROP TABLE [dbo].[TRPTSaleFCCompVDTmp]
GO

/****** Object:  Table [dbo].[TRPTSaleFCCompVDTmp]    Script Date: 16/9/2565 17:04:17 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[TRPTSaleFCCompVDTmp](
	[FTRptRowSeq] [bigint] IDENTITY(1,1) NOT NULL,
	[FNRowPartID] [bigint] NULL,
	[FNAppType] [int] NULL,
	[FTAgnCode] [varchar](100) NULL,
	[FTAgnName] [varchar](255) NULL,
	[FTBchCode] [varchar](100) NULL,
	[FTBchName] [varchar](255) NULL,
	[FTPdtCode] [varchar](100) NULL,
	[FTPdtName] [varchar](255) NULL,
	[FTPgpChain] [varchar](100) NULL,
	[FTPgpChainName] [varchar](255) NULL,
	[FTPtyCode] [varchar](100) NULL,
	[FTPtyName] [varchar](255) NULL,
	[FTPdtCat1] [varchar](100) NULL,
	[FTCatName1] [varchar](255) NULL,
	[FTPdtCat2] [varchar](100) NULL,
	[FTCatName2] [varchar](255) NULL,
	[FCXpdNetAfHDHQ] [numeric](18, 4) NULL,
	[FCXpdPerPoByHQ] [numeric](18, 4) NULL,
	[FCXpdNetAfHDVD] [numeric](18, 4) NULL,
	[FCXpdPerPoByVD] [numeric](18, 4) NULL,
	[FDLastUpdOn] [datetime] NULL,
	[FTLastUpdBy] [varchar](20) NULL,
	[FDCreateOn] [datetime] NULL,
	[FTCreateBy] [varchar](20) NULL,
	[FTComName] [varchar](50) NULL,
	[FTRptCode] [varchar](50) NULL,
	[FTUsrSession] [varchar](255) NULL,
 CONSTRAINT [PK_TRPTSaleFCCompVDTmp] PRIMARY KEY CLUSTERED 
(
	[FTRptRowSeq] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
