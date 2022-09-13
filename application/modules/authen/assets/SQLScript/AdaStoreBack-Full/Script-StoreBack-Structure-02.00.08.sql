/* รายงานการใช้จากระบบสินเชื่อ */
IF EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTSalInstallmentTmp') BEGIN
    DROP TABLE [dbo].[TRPTSalInstallmentTmp]
END
GO
CREATE TABLE [dbo].[TRPTSalInstallmentTmp](
	[FTCstCode] [varchar](20) NULL,
	[FTCstCompName] [varchar](150) NULL,
	[FTCstName] [varchar](150) NULL,
	[FTCstCreditLimit] [int] NULL,
	[FTXshDocNo] [varchar](20) NULL,
	[FTXshDocVatFull] [varchar](20) NULL,
	[FDXshDocDate] [varchar](10) NULL,
	[FTRcvCode] [varchar](5) NULL,
	[FTRcvName] [varchar](100) NULL,
	[FCXrcNet] [numeric](18, 4) NULL,
	[FCXshTotal] [numeric](18, 4) NULL,
	[FCXshDis] [numeric](18, 4) NULL,
	[FCXshGrand] [numeric](18, 4) NULL,
	[FCCstCreditBal] [numeric](18, 4) NULL,
	[FTBchCode] [varchar](5) NULL,
	[FTBchName] [varchar](100) NULL,
	[FTUsrSessID] [varchar](150) NULL,
	[FDCreateOn] [datetime] NULL,
	[FTPdtCode] [varchar](255) NULL,
	[FTPdtName] [varchar](255) NULL,
	[FTPunName] [varchar](50) NULL,
	[FCXsdAmt] [float] NULL,
	[FCXsdDis] [float] NULL,
	[FCXsdNet] [float] NULL,
	[FCXsdQty] [float] NULL,
	[FCXsdSetPrice] [float] NULL,
	[FNRptType] [varchar](10) NULL,
	[FTBchCodeCst] [varchar](5) NULL,
	[FTBchNameCst] [varchar](100) NULL,
	[FCXshCost]	[float] NULL,
	[FCXshCostIncludeVat] [float] NULL,
	[FCXshCostTotal] [float] NULL,		
	[FCXshProfit] [float] NULL,
	[FCXshProfitPercent] [float] NULL
) ON [PRIMARY]
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TCNTImpMasTmp' AND COLUMN_NAME = 'FTPdtStaVat') BEGIN
	ALTER TABLE TCNTImpMasTmp ADD FTPdtStaVat VARCHAR(1)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TCNTImpMasTmp' AND COLUMN_NAME = 'FTPtyCode') BEGIN
	ALTER TABLE TCNTImpMasTmp ADD FTPtyCode VARCHAR(5)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TCNTImpMasTmp' AND COLUMN_NAME = 'FTPtyName') BEGIN
	ALTER TABLE TCNTImpMasTmp ADD FTPtyName VARCHAR(100)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TCNTImpMasTmp' AND COLUMN_NAME = 'FTPmoCode') BEGIN
	ALTER TABLE TCNTImpMasTmp ADD FTPmoCode VARCHAR(5)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TCNTImpMasTmp' AND COLUMN_NAME = 'FTPmoName') BEGIN
	ALTER TABLE TCNTImpMasTmp ADD FTPmoName VARCHAR(50)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TCNTImpMasTmp' AND COLUMN_NAME = 'FTPgpChain') BEGIN
	ALTER TABLE TCNTImpMasTmp ADD FTPgpChain VARCHAR(30)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TCNTImpMasTmp' AND COLUMN_NAME = 'FTPgpName') BEGIN
	ALTER TABLE TCNTImpMasTmp ADD FTPgpName VARCHAR(100)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TFNTCouponHD' AND COLUMN_NAME = 'FTCphRefInt') BEGIN
	ALTER TABLE TFNTCouponHD ADD FTCphRefInt VARCHAR(20)
END
GO

/* ขยายฟิวส์จากเดิม */
IF EXISTS(SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TACTSBHD' AND COLUMN_NAME = 'FTPrdCode' AND CHARACTER_MAXIMUM_LENGTH = 5) BEGIN
	ALTER TABLE TACTSBHD ALTER COLUMN FTPrdCode VARCHAR(20) NULL
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TAPTDoHD' AND COLUMN_NAME = 'FTAgnCode') BEGIN
	ALTER TABLE TAPTDoHD ADD FTAgnCode VARCHAR(5)
END
GO

IF EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TSVTDODocDTTmp') BEGIN
	DROP TABLE [dbo].[TSVTDODocDTTmp]
END
CREATE TABLE [dbo].[TSVTDODocDTTmp](
	[FTBchCode] [varchar](5) NOT NULL,
	[FTXthDocNo] [varchar](50) NULL,
	[FNXtdSeqNo] [bigint] NULL,
	[FTXthDocKey] [varchar](20) NULL,
	[FTPdtCode] [varchar](20) NULL,
	[FTXtdPdtName] [varchar](255) NULL,
	[FTPunCode] [varchar](5) NULL,
	[FTPunName] [varchar](50) NULL,
	[FCXtdFactor] [numeric](18, 4) NULL,
	[FTXtdBarCode] [varchar](25) NULL,
	[FTXtdVatType] [varchar](1) NULL,
	[FTVatCode] [varchar](5) NULL,
	[FCXtdVatRate] [numeric](18, 4) NULL,
	[FCXtdQty] [numeric](18, 4) NULL,
	[FCXtdQtyAll] [numeric](18, 4) NULL,
	[FCXtdSetPrice] [numeric](18, 4) NULL,
	[FCXtdAmt] [numeric](18, 4) NULL,
	[FCXtdVat] [numeric](18, 4) NULL,
	[FCXtdVatable] [numeric](18, 4) NULL,
	[FCXtdNet] [numeric](18, 4) NULL,
	[FCXtdCostIn] [numeric](18, 4) NULL,
	[FCXtdCostEx] [numeric](18, 4) NULL,
	[FTXtdStaPrcStk] [varchar](1) NULL,
	[FNXtdPdtLevel] [bigint] NULL,
	[FTXtdPdtParent] [varchar](20) NULL,
	[FCXtdQtySet] [numeric](18, 4) NULL,
	[FTXtdPdtStaSet] [varchar](1) NULL,
	[FTXtdRmk] [varchar](200) NULL,
	[FTXtdBchRef] [varchar](5) NULL,
	[FTXtdDocNoRef] [varchar](20) NULL,
	[FCXtdPriceRet] [numeric](18, 4) NULL,
	[FCXtdPriceWhs] [numeric](18, 4) NULL,
	[FCXtdPriceNet] [numeric](18, 4) NULL,
	[FTXtdShpTo] [varchar](5) NULL,
	[FTXtdBchTo] [varchar](5) NULL,
	[FTSrnCode] [varchar](50) NULL,
	[FTXtdSaleType] [varchar](1) NULL,
	[FCXtdSalePrice] [numeric](18, 4) NULL,
	[FCXtdAmtB4DisChg] [numeric](18, 4) NULL,
	[FTXtdDisChgTxt] [varchar](20) NULL,
	[FCXtdDis] [numeric](18, 4) NULL,
	[FCXtdChg] [numeric](18, 4) NULL,
	[FCXtdNetAfHD] [numeric](18, 4) NULL,
	[FCXtdWhtAmt] [numeric](18, 4) NULL,
	[FTXtdWhtCode] [varchar](5) NULL,
	[FCXtdWhtRate] [numeric](18, 4) NULL,
	[FCXtdQtyLef] [numeric](18, 4) NULL,
	[FCXtdQtyRfn] [numeric](18, 4) NULL,
	[FTXtdStaAlwDis] [varchar](1) NULL,
	[FTPdtName] [varchar](50) NULL,
	[FCPdtUnitFact] [numeric](18, 4) NULL,
	[FTPgpChain] [varchar](50) NULL,
	[FNAjdLayRow] [numeric](18, 2) NULL,
	[FNAjdLayCol] [numeric](18, 2) NULL,
	[FCAjdWahB4Adj] [numeric](18, 4) NULL,
	[FCAjdSaleB4AdjC1] [numeric](18, 4) NULL,
	[FDAjdDateTimeC1] [datetime] NULL,
	[FCAjdUnitQtyC1] [numeric](18, 4) NULL,
	[FCAjdQtyAllC1] [numeric](18, 4) NULL,
	[FCAjdSaleB4AdjC2] [numeric](18, 4) NULL,
	[FDAjdDateTimeC2] [datetime] NULL,
	[FCAjdUnitQtyC2] [numeric](18, 4) NULL,
	[FCAjdQtyAllC2] [numeric](18, 4) NULL,
	[FCAjdUnitQty] [numeric](18, 4) NULL,
	[FDAjdDateTime] [datetime] NULL,
	[FCAjdQtyAll] [numeric](18, 4) NULL,
	[FCAjdQtyAllDiff] [numeric](18, 4) NULL,
	[FTAjdPlcCode] [varchar](5) NULL,
	[FTSessionID] [varchar](255) NULL,
	[FDLastUpdOn] [datetime] NULL,
	[FDCreateOn] [datetime] NULL,
	[FTLastUpdBy] [varchar](20) NULL,
	[FTCreateBy] [varchar](20) NULL,
	[FNLayRowForTWXVD] [bigint] NULL,
	[FNLayColForTWXVD] [bigint] NULL,
	[FCLayColQtyMaxForTWXVD] [numeric](18, 4) NULL,
	[FCStkQty] [numeric](18, 4) NULL,
	[FCMaxTransferForTWXVD] [numeric](18, 4) NULL,
	[FCUserInPutTransferForTWXVD] [numeric](18, 4) NULL,
	[FTMerCodeForADJPL] [varchar](5) NULL,
	[FTShpCodeForADJPL] [varchar](5) NULL,
	[FTPzeCodeForADJPL] [varchar](5) NULL,
	[FTRthCodeForADJPL] [varchar](5) NULL,
	[FTSizNameForADJPL] [varchar](40) NULL,
	[FTBchCodeForADJPL] [varchar](5) NULL,
	[FNLayRowForADJSTKVD] [bigint] NULL,
	[FNLayColForADJSTKVD] [bigint] NULL,
	[FCLayColQtyMaxForADJSTKVD] [numeric](18, 4) NULL,
	[FCUserInPutForADJSTKVD] [numeric](18, 4) NULL,
	[FCDateTimeInputForADJSTKVD] [datetime] NULL,
	[FNCabSeqForTWXVD] [int] NULL,
	[FTCabNameForTWXVD] [varchar](255) NULL,
	[FTXthWhFrmForTWXVD] [varchar](5) NULL,
	[FTXthWhToForTWXVD] [varchar](5) NULL,
	[FTBddTypeForDeposit] [varchar](255) NULL,
	[FTBddRefNoForDeposit] [varchar](20) NULL,
	[FDBddRefDateForDeposit] [datetime] NULL,
	[FCBddRefAmtForDeposit] [numeric](18, 4) NULL,
	[FTBddRefBnkNameForDeposit] [varchar](255) NULL,
	[FTTmpStatus] [varchar](1) NULL,
	[FTTmpRemark] [varchar](max) NULL,
	[FTBuyLicenseTextFeatues] [varchar](max) NULL,
	[FTBuyLicenseTextFeatuesDetail] [varchar](max) NULL,
	[FTBuyLicenseTextFeatuesQty] [varchar](max) NULL,
	[FTBuyLicenseTextFeatuesPrice] [float] NULL,
	[FTBuyLicenseTextPos] [varchar](max) NULL,
	[FTBuyLicenseTextPosQty] [varchar](max) NULL,
	[FTBuyLicenseTextPosPrice] [float] NULL,
	[FTBuyLicenseTextPackage] [varchar](max) NULL,
	[FTBuyLicenseTextPackageDetail] [varchar](max) NULL,
	[FTBuyLicenseTextPackageMonth] [varchar](max) NULL,
	[FTBuyLicenseTextPackagePrice] [float] NULL,
	[FTWahCode] [varchar](10) NULL,
	[FTPdtSetOrSN] [varchar](1) NULL,
	[FTAgnCode] [varchar](20) NULL,
	[FTXtdPdtSetOrSN] [varchar](1) NULL,
	[FCXtdQtyOrd] [numeric](18, 4) NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

IF EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TSVTJRQDocDTTmp') BEGIN
	DROP TABLE [dbo].[TSVTJRQDocDTTmp]
END
CREATE TABLE [dbo].[TSVTJRQDocDTTmp](
	[FTBchCode] [varchar](5) NOT NULL,
	[FTXthDocNo] [varchar](50) NULL,
	[FNXtdSeqNo] [bigint] NULL,
	[FTXthDocKey] [varchar](20) NULL,
	[FTPdtCode] [varchar](20) NULL,
	[FTXtdPdtName] [varchar](255) NULL,
	[FTPunCode] [varchar](5) NULL,
	[FTPunName] [varchar](50) NULL,
	[FCXtdFactor] [numeric](18, 4) NULL,
	[FTXtdBarCode] [varchar](25) NULL,
	[FTXtdVatType] [varchar](1) NULL,
	[FTVatCode] [varchar](5) NULL,
	[FCXtdVatRate] [numeric](18, 4) NULL,
	[FCXtdQty] [numeric](18, 4) NULL,
	[FCXtdQtyAll] [numeric](18, 4) NULL,
	[FCXtdSetPrice] [numeric](18, 4) NULL,
	[FCXtdAmt] [numeric](18, 4) NULL,
	[FCXtdVat] [numeric](18, 4) NULL,
	[FCXtdVatable] [numeric](18, 4) NULL,
	[FCXtdNet] [numeric](18, 4) NULL,
	[FCXtdCostIn] [numeric](18, 4) NULL,
	[FCXtdCostEx] [numeric](18, 4) NULL,
	[FTXtdStaPrcStk] [varchar](1) NULL,
	[FNXtdPdtLevel] [bigint] NULL,
	[FTXtdPdtParent] [varchar](20) NULL,
	[FCXtdQtySet] [numeric](18, 4) NULL,
	[FTXtdPdtStaSet] [varchar](1) NULL,
	[FTXtdRmk] [varchar](200) NULL,
	[FTXtdBchRef] [varchar](5) NULL,
	[FTXtdDocNoRef] [varchar](20) NULL,
	[FCXtdPriceRet] [numeric](18, 4) NULL,
	[FCXtdPriceWhs] [numeric](18, 4) NULL,
	[FCXtdPriceNet] [numeric](18, 4) NULL,
	[FTXtdShpTo] [varchar](5) NULL,
	[FTXtdBchTo] [varchar](5) NULL,
	[FTSrnCode] [varchar](50) NULL,
	[FTXtdSaleType] [varchar](1) NULL,
	[FCXtdSalePrice] [numeric](18, 4) NULL,
	[FCXtdAmtB4DisChg] [numeric](18, 4) NULL,
	[FTXtdDisChgTxt] [varchar](20) NULL,
	[FCXtdDis] [numeric](18, 4) NULL,
	[FCXtdChg] [numeric](18, 4) NULL,
	[FCXtdNetAfHD] [numeric](18, 4) NULL,
	[FCXtdWhtAmt] [numeric](18, 4) NULL,
	[FTXtdWhtCode] [varchar](5) NULL,
	[FCXtdWhtRate] [numeric](18, 4) NULL,
	[FCXtdQtyLef] [numeric](18, 4) NULL,
	[FCXtdQtyRfn] [numeric](18, 4) NULL,
	[FTXtdStaAlwDis] [varchar](1) NULL,
	[FTPdtName] [varchar](50) NULL,
	[FCPdtUnitFact] [numeric](18, 4) NULL,
	[FTPgpChain] [varchar](50) NULL,
	[FNAjdLayRow] [numeric](18, 2) NULL,
	[FNAjdLayCol] [numeric](18, 2) NULL,
	[FCAjdWahB4Adj] [numeric](18, 4) NULL,
	[FCAjdSaleB4AdjC1] [numeric](18, 4) NULL,
	[FDAjdDateTimeC1] [datetime] NULL,
	[FCAjdUnitQtyC1] [numeric](18, 4) NULL,
	[FCAjdQtyAllC1] [numeric](18, 4) NULL,
	[FCAjdSaleB4AdjC2] [numeric](18, 4) NULL,
	[FDAjdDateTimeC2] [datetime] NULL,
	[FCAjdUnitQtyC2] [numeric](18, 4) NULL,
	[FCAjdQtyAllC2] [numeric](18, 4) NULL,
	[FCAjdUnitQty] [numeric](18, 4) NULL,
	[FDAjdDateTime] [datetime] NULL,
	[FCAjdQtyAll] [numeric](18, 4) NULL,
	[FCAjdQtyAllDiff] [numeric](18, 4) NULL,
	[FTAjdPlcCode] [varchar](5) NULL,
	[FTSessionID] [varchar](255) NULL,
	[FDLastUpdOn] [datetime] NULL,
	[FDCreateOn] [datetime] NULL,
	[FTLastUpdBy] [varchar](20) NULL,
	[FTCreateBy] [varchar](20) NULL,
	[FNLayRowForTWXVD] [bigint] NULL,
	[FNLayColForTWXVD] [bigint] NULL,
	[FCLayColQtyMaxForTWXVD] [numeric](18, 4) NULL,
	[FCStkQty] [numeric](18, 4) NULL,
	[FCMaxTransferForTWXVD] [numeric](18, 4) NULL,
	[FCUserInPutTransferForTWXVD] [numeric](18, 4) NULL,
	[FTMerCodeForADJPL] [varchar](5) NULL,
	[FTShpCodeForADJPL] [varchar](5) NULL,
	[FTPzeCodeForADJPL] [varchar](5) NULL,
	[FTRthCodeForADJPL] [varchar](5) NULL,
	[FTSizNameForADJPL] [varchar](40) NULL,
	[FTBchCodeForADJPL] [varchar](5) NULL,
	[FNLayRowForADJSTKVD] [bigint] NULL,
	[FNLayColForADJSTKVD] [bigint] NULL,
	[FCLayColQtyMaxForADJSTKVD] [numeric](18, 4) NULL,
	[FCUserInPutForADJSTKVD] [numeric](18, 4) NULL,
	[FCDateTimeInputForADJSTKVD] [datetime] NULL,
	[FNCabSeqForTWXVD] [int] NULL,
	[FTCabNameForTWXVD] [varchar](255) NULL,
	[FTXthWhFrmForTWXVD] [varchar](5) NULL,
	[FTXthWhToForTWXVD] [varchar](5) NULL,
	[FTBddTypeForDeposit] [varchar](255) NULL,
	[FTBddRefNoForDeposit] [varchar](20) NULL,
	[FDBddRefDateForDeposit] [datetime] NULL,
	[FCBddRefAmtForDeposit] [numeric](18, 4) NULL,
	[FTBddRefBnkNameForDeposit] [varchar](255) NULL,
	[FTTmpStatus] [varchar](1) NULL,
	[FTTmpRemark] [varchar](max) NULL,
	[FTBuyLicenseTextFeatues] [varchar](max) NULL,
	[FTBuyLicenseTextFeatuesDetail] [varchar](max) NULL,
	[FTBuyLicenseTextFeatuesQty] [varchar](max) NULL,
	[FTBuyLicenseTextFeatuesPrice] [float] NULL,
	[FTBuyLicenseTextPos] [varchar](max) NULL,
	[FTBuyLicenseTextPosQty] [varchar](max) NULL,
	[FTBuyLicenseTextPosPrice] [float] NULL,
	[FTBuyLicenseTextPackage] [varchar](max) NULL,
	[FTBuyLicenseTextPackageDetail] [varchar](max) NULL,
	[FTBuyLicenseTextPackageMonth] [varchar](max) NULL,
	[FTBuyLicenseTextPackagePrice] [float] NULL,
	[FTWahCode] [varchar](10) NULL,
	[FTPdtSetOrSN] [varchar](1) NULL,
	[FTAgnCode] [varchar](20) NULL,
	[FTXtdPdtSetOrSN] [varchar](1) NULL,
	[FCXtdQtyOrd] [numeric](18, 4) NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

IF EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TSVTJRQDTDisTmp') BEGIN
	DROP TABLE [dbo].[TSVTJRQDTDisTmp]
END
CREATE TABLE [dbo].[TSVTJRQDTDisTmp](
	[FTBchCode] [varchar](5) NULL,
	[FTXthDocNo] [varchar](20) NULL,
	[FNXtdSeqNo] [bigint] NULL,
	[FTSessionID] [varchar](255) NULL,
	[FDXtdDateIns] [datetime] NULL,
	[FNXtdStaDis] [bigint] NULL,
	[FTXtdDisChgType] [varchar](10) NULL,
	[FCXtdNet] [numeric](18, 4) NULL,
	[FCXtdValue] [numeric](18, 4) NULL,
	[FTDisCode] [varchar](20) NULL,
	[FTRsnCode] [varchar](5) NULL,
	[FDLastUpdOn] [datetime] NULL,
	[FDCreateOn] [datetime] NULL,
	[FTLastUpdBy] [varchar](20) NULL,
	[FTCreateBy] [varchar](20) NULL,
	[FTXtdDisChgTxt] [varchar](20) NULL
) ON [PRIMARY]
GO

IF EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TSVTJRQDTSetTmp') BEGIN
	DROP TABLE [dbo].[TSVTJRQDTSetTmp]
END
CREATE TABLE [dbo].[TSVTJRQDTSetTmp](
	[FTBchCode] [varchar](5) NOT NULL,
	[FTXthDocNo] [varchar](20) NULL,
	[FNXtdSeqNo] [int] NULL,
	[FNPstSeqNo] [int] NULL,
	[FTPdtCode] [varchar](20) NULL,
	[FTPsvType] [varchar](255) NULL,
	[FTXtdPdtName] [varchar](100) NULL,
	[FTPunCode] [varchar](5) NULL,
	[FCXtdQtySet] [numeric](18, 4) NULL,
	[FCXtdSalePrice] [numeric](18, 4) NULL,
	[FDLastUpdOn] [datetime] NULL,
	[FDCreateOn] [datetime] NULL,
	[FTLastUpdBy] [varchar](20) NULL,
	[FTCreateBy] [varchar](20) NULL,
	[FTSessionID] [varchar](255) NULL,
	[FTPdtCodeOrg] [varchar](20) NULL,
	[FTSrnCode] [varchar](255) NULL,
	[FTPsvStaSuggest] [varchar](10) NULL,
	[FTXthDocKey] [varchar](50) NULL,
	[FTAgnCode] [varchar](50) NULL
) ON [PRIMARY]
GO

IF EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TSVTJRQHDDisTmp') BEGIN
	DROP TABLE [dbo].[TSVTJRQHDDisTmp]
END
CREATE TABLE [dbo].[TSVTJRQHDDisTmp](
	[FTBchCode] [varchar](5) NOT NULL,
	[FTXthDocNo] [varchar](20) NOT NULL,
	[FDXtdDateIns] [datetime] NOT NULL,
	[FTXtdDisChgTxt] [varchar](20) NULL,
	[FTXtdDisChgType] [varchar](10) NULL,
	[FCXtdTotalAfDisChg] [numeric](18, 4) NULL,
	[FCXtdTotalB4DisChg] [numeric](18, 4) NULL,
	[FCXtdDisChg] [numeric](18, 4) NULL,
	[FCXtdAmt] [numeric](18, 4) NULL,
	[FTDisCode] [varchar](20) NULL,
	[FTRsnCode] [varchar](5) NULL,
	[FTSessionID] [varchar](255) NOT NULL,
	[FDLastUpdOn] [datetime] NULL,
	[FDCreateOn] [datetime] NULL,
	[FTLastUpdBy] [varchar](255) NULL,
	[FTCreateBy] [varchar](255) NULL,
	[FTAgnCode] [varchar](255) NULL
) ON [PRIMARY]
GO

IF EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TSVTTBODocDTTmp') BEGIN
	DROP TABLE [dbo].[TSVTTBODocDTTmp]
END
CREATE TABLE [dbo].[TSVTTBODocDTTmp](
	[FTBchCode] [varchar](5) NOT NULL,
	[FTXthDocNo] [varchar](50) NULL,
	[FNXtdSeqNo] [bigint] NULL,
	[FTXthDocKey] [varchar](20) NULL,
	[FTPdtCode] [varchar](20) NULL,
	[FTXtdPdtName] [varchar](255) NULL,
	[FTPunCode] [varchar](5) NULL,
	[FTPunName] [varchar](50) NULL,
	[FCXtdFactor] [numeric](18, 4) NULL,
	[FTXtdBarCode] [varchar](25) NULL,
	[FTXtdVatType] [varchar](1) NULL,
	[FTVatCode] [varchar](5) NULL,
	[FCXtdVatRate] [numeric](18, 4) NULL,
	[FCXtdQty] [numeric](18, 4) NULL,
	[FCXtdQtyAll] [numeric](18, 4) NULL,
	[FCXtdSetPrice] [numeric](18, 4) NULL,
	[FCXtdAmt] [numeric](18, 4) NULL,
	[FCXtdVat] [numeric](18, 4) NULL,
	[FCXtdVatable] [numeric](18, 4) NULL,
	[FCXtdNet] [numeric](18, 4) NULL,
	[FCXtdCostIn] [numeric](18, 4) NULL,
	[FCXtdCostEx] [numeric](18, 4) NULL,
	[FTXtdStaPrcStk] [varchar](1) NULL,
	[FNXtdPdtLevel] [bigint] NULL,
	[FTXtdPdtParent] [varchar](20) NULL,
	[FCXtdQtySet] [numeric](18, 4) NULL,
	[FTXtdPdtStaSet] [varchar](1) NULL,
	[FTXtdRmk] [varchar](200) NULL,
	[FTXtdBchRef] [varchar](5) NULL,
	[FTXtdDocNoRef] [varchar](20) NULL,
	[FCXtdPriceRet] [numeric](18, 4) NULL,
	[FCXtdPriceWhs] [numeric](18, 4) NULL,
	[FCXtdPriceNet] [numeric](18, 4) NULL,
	[FTXtdShpTo] [varchar](5) NULL,
	[FTXtdBchTo] [varchar](5) NULL,
	[FTSrnCode] [varchar](50) NULL,
	[FTXtdSaleType] [varchar](1) NULL,
	[FCXtdSalePrice] [numeric](18, 4) NULL,
	[FCXtdAmtB4DisChg] [numeric](18, 4) NULL,
	[FTXtdDisChgTxt] [varchar](20) NULL,
	[FCXtdDis] [numeric](18, 4) NULL,
	[FCXtdChg] [numeric](18, 4) NULL,
	[FCXtdNetAfHD] [numeric](18, 4) NULL,
	[FCXtdWhtAmt] [numeric](18, 4) NULL,
	[FTXtdWhtCode] [varchar](5) NULL,
	[FCXtdWhtRate] [numeric](18, 4) NULL,
	[FCXtdQtyLef] [numeric](18, 4) NULL,
	[FCXtdQtyRfn] [numeric](18, 4) NULL,
	[FTXtdStaAlwDis] [varchar](1) NULL,
	[FTPdtName] [varchar](50) NULL,
	[FCPdtUnitFact] [numeric](18, 4) NULL,
	[FTPgpChain] [varchar](50) NULL,
	[FNAjdLayRow] [numeric](18, 2) NULL,
	[FNAjdLayCol] [numeric](18, 2) NULL,
	[FCAjdWahB4Adj] [numeric](18, 4) NULL,
	[FCAjdSaleB4AdjC1] [numeric](18, 4) NULL,
	[FDAjdDateTimeC1] [datetime] NULL,
	[FCAjdUnitQtyC1] [numeric](18, 4) NULL,
	[FCAjdQtyAllC1] [numeric](18, 4) NULL,
	[FCAjdSaleB4AdjC2] [numeric](18, 4) NULL,
	[FDAjdDateTimeC2] [datetime] NULL,
	[FCAjdUnitQtyC2] [numeric](18, 4) NULL,
	[FCAjdQtyAllC2] [numeric](18, 4) NULL,
	[FCAjdUnitQty] [numeric](18, 4) NULL,
	[FDAjdDateTime] [datetime] NULL,
	[FCAjdQtyAll] [numeric](18, 4) NULL,
	[FCAjdQtyAllDiff] [numeric](18, 4) NULL,
	[FTAjdPlcCode] [varchar](5) NULL,
	[FTSessionID] [varchar](255) NULL,
	[FDLastUpdOn] [datetime] NULL,
	[FDCreateOn] [datetime] NULL,
	[FTLastUpdBy] [varchar](20) NULL,
	[FTCreateBy] [varchar](20) NULL,
	[FNLayRowForTWXVD] [bigint] NULL,
	[FNLayColForTWXVD] [bigint] NULL,
	[FCLayColQtyMaxForTWXVD] [numeric](18, 4) NULL,
	[FCStkQty] [numeric](18, 4) NULL,
	[FCMaxTransferForTWXVD] [numeric](18, 4) NULL,
	[FCUserInPutTransferForTWXVD] [numeric](18, 4) NULL,
	[FTMerCodeForADJPL] [varchar](5) NULL,
	[FTShpCodeForADJPL] [varchar](5) NULL,
	[FTPzeCodeForADJPL] [varchar](5) NULL,
	[FTRthCodeForADJPL] [varchar](5) NULL,
	[FTSizNameForADJPL] [varchar](40) NULL,
	[FTBchCodeForADJPL] [varchar](5) NULL,
	[FNLayRowForADJSTKVD] [bigint] NULL,
	[FNLayColForADJSTKVD] [bigint] NULL,
	[FCLayColQtyMaxForADJSTKVD] [numeric](18, 4) NULL,
	[FCUserInPutForADJSTKVD] [numeric](18, 4) NULL,
	[FCDateTimeInputForADJSTKVD] [datetime] NULL,
	[FNCabSeqForTWXVD] [int] NULL,
	[FTCabNameForTWXVD] [varchar](255) NULL,
	[FTXthWhFrmForTWXVD] [varchar](5) NULL,
	[FTXthWhToForTWXVD] [varchar](5) NULL,
	[FTBddTypeForDeposit] [varchar](255) NULL,
	[FTBddRefNoForDeposit] [varchar](20) NULL,
	[FDBddRefDateForDeposit] [datetime] NULL,
	[FCBddRefAmtForDeposit] [numeric](18, 4) NULL,
	[FTBddRefBnkNameForDeposit] [varchar](255) NULL,
	[FTTmpStatus] [varchar](1) NULL,
	[FTTmpRemark] [varchar](max) NULL,
	[FTBuyLicenseTextFeatues] [varchar](max) NULL,
	[FTBuyLicenseTextFeatuesDetail] [varchar](max) NULL,
	[FTBuyLicenseTextFeatuesQty] [varchar](max) NULL,
	[FTBuyLicenseTextFeatuesPrice] [float] NULL,
	[FTBuyLicenseTextPos] [varchar](max) NULL,
	[FTBuyLicenseTextPosQty] [varchar](max) NULL,
	[FTBuyLicenseTextPosPrice] [float] NULL,
	[FTBuyLicenseTextPackage] [varchar](max) NULL,
	[FTBuyLicenseTextPackageDetail] [varchar](max) NULL,
	[FTBuyLicenseTextPackageMonth] [varchar](max) NULL,
	[FTBuyLicenseTextPackagePrice] [float] NULL,
	[FTWahCode] [varchar](10) NULL,
	[FTPdtSetOrSN] [varchar](1) NULL,
	[FTAgnCode] [varchar](20) NULL,
	[FTXtdPdtSetOrSN] [varchar](1) NULL,
	[FCXtdQtyOrd] [numeric](18, 4) NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

IF NOT EXISTS(SELECT * FROM sys.indexes WHERE name = 'IX_TRPTSalPdtBillTmp__Filter1' AND object_id = OBJECT_ID('TRPTSalPdtBillTmp'))
BEGIN
	CREATE NONCLUSTERED INDEX IX_TRPTSalPdtBillTmp__Filter1
	ON [dbo].[TRPTSalPdtBillTmp] ([FTUsrSession],[FTComName],[FTRptCode],[FTXshDocNo])
END

IF NOT EXISTS(SELECT * FROM sys.indexes WHERE name = 'IX_TRPTSalPdtBillTmp__Filter2' AND object_id = OBJECT_ID('TRPTSalPdtBillTmp'))
BEGIN
	CREATE NONCLUSTERED INDEX IX_TRPTSalPdtBillTmp__Filter2
	ON [dbo].[TRPTSalPdtBillTmp] ([FTUsrSession],[FTComName],[FTRptCode])
	INCLUDE ([FTRptRowSeq],[FNRowPartID],[FNAppType],[FNXshDocType],[FNType],[FTXshDocNo],[FTShpCode],[FDXshDocDate],[FTCstCode],[FTCstName],[FTXshRefInt],[FCXshTotal],[FCXshDis],[FCXshVat],[FCXshVatable],[FCXshGrand],[FCXshRnd],[FCXshTotalAfDis],[FNXrcSeqNo],[FTRcvCode],[FTRcvName],[FTXrcRefNo1],[FDXrcRefDate],[FTBnkCode],[FTBnkName],[FCXrcNet],[FCXrcChg],[FNXsdSeqNo],[FTPdtCode],[FTPdtName],[FTPunCode],[FTPunName],[FTXsdBarCode],[FCXsdQty],[FCXsdSetPrice],[FCXsdAmt],[FCXsdDis],[FCXsdNet],[FDLastUpdOn],[FTLastUpdBy],[FDCreateOn],[FTCreateBy],[FDTmpTxnDate],[FTBchCode],[FTBchName])
END

IF NOT EXISTS(SELECT * FROM sys.indexes WHERE name = 'IX_TRPTPdtStkBalTmp_GroupFilter' AND object_id = OBJECT_ID('TRPTPdtStkBalTmp'))
BEGIN
	CREATE NONCLUSTERED INDEX IX_TRPTPdtStkBalTmp_GroupFilter
	ON [dbo].[TRPTPdtStkBalTmp] ([FTRptCode],[FTUsrSession]) INCLUDE ([FTWahCode],[FCStkQty])
END

IF NOT EXISTS(SELECT * FROM sys.indexes WHERE name = 'IX_TRPTPdtStkBalTmp_GroupFilter2' AND object_id = OBJECT_ID('TRPTPdtStkBalTmp'))
BEGIN
	CREATE NONCLUSTERED INDEX IX_TRPTPdtStkBalTmp_GroupFilter2
	ON [dbo].[TRPTPdtStkBalTmp] ([FTRptCode],[FTUsrSession])
	INCLUDE ([FTRptRowSeq],[FNRowPartID],[FTBchCode],[FTWahCode],[FTWahName],[FTPdtCode],[FTPdtName],[FTPgpChainName],[FCPdtCostAVGEX],[FCPdtCostTotal],[FCStkQty],[FDLastUpdOn],[FTLastUpdBy],[FDCreateOn],[FTCreateBy],[FTComName],[FDTmpTxnDate],[FTBchName],[FCPdtCostStd],[FCPdtCostStdTotal])
END

IF NOT EXISTS(SELECT * FROM sys.indexes WHERE name = 'IX_TRPTPdtStkBalTmp_GroupFilter3' AND object_id = OBJECT_ID('TRPTPdtStkBalTmp'))
BEGIN
	CREATE NONCLUSTERED INDEX IX_TRPTPdtStkBalTmp_GroupFilter3
	ON [dbo].[TRPTPdtStkBalTmp] ([FTWahCode],[FTRptCode],[FTUsrSession])
	INCLUDE ([FCStkQty])
END

IF NOT EXISTS(SELECT * FROM sys.indexes WHERE name = 'IX_TRPTPdtStkBalTmp_GroupFilter4' AND object_id = OBJECT_ID('TRPTPdtStkBalTmp'))
BEGIN
	CREATE NONCLUSTERED INDEX IX_TRPTPdtStkBalTmp_GroupFilter4
	ON [dbo].[TRPTPdtStkBalTmp] ([FTWahCode],[FTComName],[FTRptCode],[FTUsrSession])
	INCLUDE ([FCStkQty])
END


IF EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TSVTDODocHDRefTmp') BEGIN
	DROP TABLE [dbo].[TSVTDODocHDRefTmp]
END
CREATE TABLE [dbo].[TSVTDODocHDRefTmp](
 [FTXthDocNo] [varchar](20) NOT NULL,
 [FTXthRefDocNo] [varchar](20) NOT NULL,
 [FTXthRefType] [varchar](1) NULL,
 [FTXthRefKey] [varchar](10) NULL,
 [FDXthRefDocDate] [datetime] NULL,
 [FTXthDocKey] [varchar](20) NOT NULL,
 [FTSessionID] [varchar](255) NOT NULL,
 [FDCreateOn] [datetime] NULL
) ON [PRIMARY]


IF EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTSalesByCategoryTemplate') BEGIN
	DROP TABLE [dbo].[TRPTSalesByCategoryTemplate]
END
CREATE TABLE [dbo].[TRPTSalesByCategoryTemplate](
	[FTRptRowSeq] [bigint] IDENTITY(1,1) NOT NULL,
	[FTBchCode] [varchar](5) NULL,
	[FTBchName] [varchar](100) NULL,
	[FDCreateOn] [datetime] NULL,
	[FTCreateBy] [varchar](20) NULL,
	[FTUsrSession] [varchar](255) NULL,
	[FNPdtGroupLube] [numeric](18, 4) NULL,
	[FNPdtGroupTire] [numeric](18, 4) NULL,
	[FNPdtGroupService] [numeric](18, 4) NULL,
	[FNPdtGroupOther] [numeric](18, 4) NULL,
PRIMARY KEY CLUSTERED 
(
	[FTRptRowSeq] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) 
) 

IF EXISTS(SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TPSTSalHD' AND COLUMN_NAME = 'FTXshDisChgTxt') BEGIN
    ALTER TABLE TPSTSalHD ALTER COLUMN FTXshDisChgTxt VARCHAR(250)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTPdtStkBalTmp' AND COLUMN_NAME = 'FTPdtCatName1') BEGIN
    ALTER TABLE TRPTPdtStkBalTmp ADD  FTPdtCatName1 VARCHAR(225)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTPdtStkBalTmp' AND COLUMN_NAME = 'FTPdtCatName2') BEGIN
    ALTER TABLE TRPTPdtStkBalTmp ADD  FTPdtCatName2 VARCHAR(225)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTPSBillByPdtPmtTmp' AND COLUMN_NAME = 'FTPdtCatName1') BEGIN
    ALTER TABLE TRPTPSBillByPdtPmtTmp ADD  FTPdtCatName1 VARCHAR(225)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTPSBillByPdtPmtTmp' AND COLUMN_NAME = 'FTPdtCatName2') BEGIN
    ALTER TABLE TRPTPSBillByPdtPmtTmp ADD  FTPdtCatName2 VARCHAR(225)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTPSBillByPdtPmtTmp' AND COLUMN_NAME = 'FTBchName') BEGIN
    ALTER TABLE TRPTPSBillByPdtPmtTmp ADD  FTBchName VARCHAR(225)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTSalDTTmp' AND COLUMN_NAME = 'FTPdtCatName1') BEGIN
    ALTER TABLE TRPTSalDTTmp ADD  FTPdtCatName1 VARCHAR(225)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTSalDTTmp' AND COLUMN_NAME = 'FTPdtCatName2') BEGIN
    ALTER TABLE TRPTSalDTTmp ADD  FTPdtCatName2 VARCHAR(225)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTSalMthQtyByPdtTmp' AND COLUMN_NAME = 'FTPdtCatName1') BEGIN
        ALTER TABLE TRPTSalMthQtyByPdtTmp ADD FTPdtCatName1 VARCHAR(255)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTSalMthQtyByPdtTmp' AND COLUMN_NAME = 'FTPdtCatName2') BEGIN
        ALTER TABLE TRPTSalMthQtyByPdtTmp ADD FTPdtCatName2 VARCHAR(255)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTPdtEntryTmp' AND COLUMN_NAME = 'FTPdtCatName1') BEGIN
    ALTER TABLE TRPTPdtEntryTmp ADD  FTPdtCatName1 VARCHAR(225)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTPdtEntryTmp' AND COLUMN_NAME = 'FTPdtCatName2') BEGIN
    ALTER TABLE TRPTPdtEntryTmp ADD  FTPdtCatName2 VARCHAR(225)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTPdtEntryTmp' AND COLUMN_NAME = 'FTPplName') BEGIN
    ALTER TABLE TRPTPdtEntryTmp ADD  FTPplName VARCHAR(225)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTPdtEntryTmp' AND COLUMN_NAME = 'FCPdtCostStd') BEGIN
    ALTER TABLE TRPTPdtEntryTmp ADD  FCPdtCostStd numeric(18, 4)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTPdtStkCrdTmp' AND COLUMN_NAME = 'FTPdtCatName1') BEGIN
    ALTER TABLE TRPTPdtStkCrdTmp ADD  FTPdtCatName1 VARCHAR(225)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTPdtStkCrdTmp' AND COLUMN_NAME = 'FTPdtCatName2') BEGIN
    ALTER TABLE TRPTPdtStkCrdTmp ADD  FTPdtCatName2 VARCHAR(225)
END
GO

IF NOT EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTPSBillByPdtPmtTmp' AND COLUMN_NAME = 'FTRcvName') BEGIN
    ALTER TABLE TRPTPSBillByPdtPmtTmp ADD  FTRcvName VARCHAR(225)
END
GO

/* ขยายฟิวส์จากเดิม */
IF EXISTS(SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TRPTPdtStkCrdTmp' AND COLUMN_NAME = 'FTStkDocNo' AND CHARACTER_MAXIMUM_LENGTH = 20) BEGIN
	ALTER TABLE TRPTPdtStkCrdTmp ALTER COLUMN FTStkDocNo VARCHAR(50) NULL
END
GO

IF EXISTS(SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TSVTTBIDocDTTmp') BEGIN
	DROP TABLE [dbo].[TSVTTBIDocDTTmp]
END
CREATE TABLE [dbo].[TSVTTBIDocDTTmp](
	[FTBchCode] [varchar](5) NOT NULL,
	[FTXthDocNo] [varchar](50) NULL,
	[FNXtdSeqNo] [bigint] NULL,
	[FTXthDocKey] [varchar](20) NULL,
	[FTPdtCode] [varchar](20) NULL,
	[FTXtdPdtName] [varchar](255) NULL,
	[FTPunCode] [varchar](5) NULL,
	[FTPunName] [varchar](50) NULL,
	[FCXtdFactor] [numeric](18, 4) NULL,
	[FTXtdBarCode] [varchar](25) NULL,
	[FTXtdVatType] [varchar](1) NULL,
	[FTVatCode] [varchar](5) NULL,
	[FCXtdVatRate] [numeric](18, 4) NULL,
	[FCXtdQty] [numeric](18, 4) NULL,
	[FCXtdQtyAll] [numeric](18, 4) NULL,
	[FCXtdSetPrice] [numeric](18, 4) NULL,
	[FCXtdAmt] [numeric](18, 4) NULL,
	[FCXtdVat] [numeric](18, 4) NULL,
	[FCXtdVatable] [numeric](18, 4) NULL,
	[FCXtdNet] [numeric](18, 4) NULL,
	[FCXtdCostIn] [numeric](18, 4) NULL,
	[FCXtdCostEx] [numeric](18, 4) NULL,
	[FTXtdStaPrcStk] [varchar](1) NULL,
	[FNXtdPdtLevel] [bigint] NULL,
	[FTXtdPdtParent] [varchar](20) NULL,
	[FCXtdQtySet] [numeric](18, 4) NULL,
	[FTXtdPdtStaSet] [varchar](1) NULL,
	[FTXtdRmk] [varchar](200) NULL,
	[FTXtdBchRef] [varchar](5) NULL,
	[FTXtdDocNoRef] [varchar](20) NULL,
	[FCXtdPriceRet] [numeric](18, 4) NULL,
	[FCXtdPriceWhs] [numeric](18, 4) NULL,
	[FCXtdPriceNet] [numeric](18, 4) NULL,
	[FTXtdShpTo] [varchar](5) NULL,
	[FTXtdBchTo] [varchar](5) NULL,
	[FTSrnCode] [varchar](50) NULL,
	[FTXtdSaleType] [varchar](1) NULL,
	[FCXtdSalePrice] [numeric](18, 4) NULL,
	[FCXtdAmtB4DisChg] [numeric](18, 4) NULL,
	[FTXtdDisChgTxt] [varchar](20) NULL,
	[FCXtdDis] [numeric](18, 4) NULL,
	[FCXtdChg] [numeric](18, 4) NULL,
	[FCXtdNetAfHD] [numeric](18, 4) NULL,
	[FCXtdWhtAmt] [numeric](18, 4) NULL,
	[FTXtdWhtCode] [varchar](5) NULL,
	[FCXtdWhtRate] [numeric](18, 4) NULL,
	[FCXtdQtyLef] [numeric](18, 4) NULL,
	[FCXtdQtyRfn] [numeric](18, 4) NULL,
	[FTXtdStaAlwDis] [varchar](1) NULL,
	[FTPdtName] [varchar](50) NULL,
	[FCPdtUnitFact] [numeric](18, 4) NULL,
	[FTPgpChain] [varchar](50) NULL,
	[FNAjdLayRow] [numeric](18, 2) NULL,
	[FNAjdLayCol] [numeric](18, 2) NULL,
	[FCAjdWahB4Adj] [numeric](18, 4) NULL,
	[FCAjdSaleB4AdjC1] [numeric](18, 4) NULL,
	[FDAjdDateTimeC1] [datetime] NULL,
	[FCAjdUnitQtyC1] [numeric](18, 4) NULL,
	[FCAjdQtyAllC1] [numeric](18, 4) NULL,
	[FCAjdSaleB4AdjC2] [numeric](18, 4) NULL,
	[FDAjdDateTimeC2] [datetime] NULL,
	[FCAjdUnitQtyC2] [numeric](18, 4) NULL,
	[FCAjdQtyAllC2] [numeric](18, 4) NULL,
	[FCAjdUnitQty] [numeric](18, 4) NULL,
	[FDAjdDateTime] [datetime] NULL,
	[FCAjdQtyAll] [numeric](18, 4) NULL,
	[FCAjdQtyAllDiff] [numeric](18, 4) NULL,
	[FTAjdPlcCode] [varchar](5) NULL,
	[FTSessionID] [varchar](255) NULL,
	[FDLastUpdOn] [datetime] NULL,
	[FDCreateOn] [datetime] NULL,
	[FTLastUpdBy] [varchar](20) NULL,
	[FTCreateBy] [varchar](20) NULL,
	[FNLayRowForTWXVD] [bigint] NULL,
	[FNLayColForTWXVD] [bigint] NULL,
	[FCLayColQtyMaxForTWXVD] [numeric](18, 4) NULL,
	[FCStkQty] [numeric](18, 4) NULL,
	[FCMaxTransferForTWXVD] [numeric](18, 4) NULL,
	[FCUserInPutTransferForTWXVD] [numeric](18, 4) NULL,
	[FTMerCodeForADJPL] [varchar](5) NULL,
	[FTShpCodeForADJPL] [varchar](5) NULL,
	[FTPzeCodeForADJPL] [varchar](5) NULL,
	[FTRthCodeForADJPL] [varchar](5) NULL,
	[FTSizNameForADJPL] [varchar](40) NULL,
	[FTBchCodeForADJPL] [varchar](5) NULL,
	[FNLayRowForADJSTKVD] [bigint] NULL,
	[FNLayColForADJSTKVD] [bigint] NULL,
	[FCLayColQtyMaxForADJSTKVD] [numeric](18, 4) NULL,
	[FCUserInPutForADJSTKVD] [numeric](18, 4) NULL,
	[FCDateTimeInputForADJSTKVD] [datetime] NULL,
	[FNCabSeqForTWXVD] [int] NULL,
	[FTCabNameForTWXVD] [varchar](255) NULL,
	[FTXthWhFrmForTWXVD] [varchar](5) NULL,
	[FTXthWhToForTWXVD] [varchar](5) NULL,
	[FTBddTypeForDeposit] [varchar](255) NULL,
	[FTBddRefNoForDeposit] [varchar](20) NULL,
	[FDBddRefDateForDeposit] [datetime] NULL,
	[FCBddRefAmtForDeposit] [numeric](18, 4) NULL,
	[FTBddRefBnkNameForDeposit] [varchar](255) NULL,
	[FTTmpStatus] [varchar](1) NULL,
	[FTTmpRemark] [varchar](max) NULL,
	[FTBuyLicenseTextFeatues] [varchar](max) NULL,
	[FTBuyLicenseTextFeatuesDetail] [varchar](max) NULL,
	[FTBuyLicenseTextFeatuesQty] [varchar](max) NULL,
	[FTBuyLicenseTextFeatuesPrice] [float] NULL,
	[FTBuyLicenseTextPos] [varchar](max) NULL,
	[FTBuyLicenseTextPosQty] [varchar](max) NULL,
	[FTBuyLicenseTextPosPrice] [float] NULL,
	[FTBuyLicenseTextPackage] [varchar](max) NULL,
	[FTBuyLicenseTextPackageDetail] [varchar](max) NULL,
	[FTBuyLicenseTextPackageMonth] [varchar](max) NULL,
	[FTBuyLicenseTextPackagePrice] [float] NULL,
	[FTWahCode] [varchar](10) NULL,
	[FTPdtSetOrSN] [varchar](1) NULL,
	[FTAgnCode] [varchar](20) NULL,
	[FTXtdPdtSetOrSN] [varchar](1) NULL,
	[FCXtdQtyOrd] [numeric](18, 4) NULL,
	[FTCstBchCode] [varchar](50) NULL,
	[FTBchName] [varchar](255) NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO