-- Insert TCNSDocCtl_L
INSERT INTO [dbo].[TCNSDocCtl_L]([FTDctCode],[FNLngID],[FTDctTable],[FTDctName],[FTDctStaUse]) VALUES('00001',1,'TCNTPdtAdjPriHD','ใบปรับราคาขาย','1')
INSERT INTO [dbo].[TCNSDocCtl_L]([FTDctCode],[FNLngID],[FTDctTable],[FTDctName],[FTDctStaUse]) VALUES('00002',1,'TCNTPdtPmtHD','โปรโมชั่น','1')

-- ============================================= Insert รายงาน - เปรียบเทียบการสั่งซื้อกับยอดขายแฟรนไซส์ =============================================
DELETE FROM [dbo].[TSysReport] 		WHERE FTRptCode = '001001065';
DELETE FROM [dbo].[TSysReport_L] 	WHERE FTRptCode = '001001065';

INSERT INTO [dbo].[TSysReport](
	[FTRptCode],[FTGrpRptModCode],[FTGrpRptCode],[FTRptRoute],[FTRptStaUseFrm],[FTRptTblView],[FTRptFilterCol],[FTRptFileName],[FTRptStaShwBch],[FTRptStaShwYear],[FTRptSeqNo],[FTRptStaUse],[FTLicPdtCode]
)
VALUES (
	'001001065','001','001001','rptSaleFCCompVD',NULL,NULL,'1,2,4,13,8,9,84,85',NULL,'1','1','43','1','SB-RPT001003065'
);

INSERT INTO [dbo].[TSysReport_L]([FTRptCode],[FNLngID],[FTRptName],[FTRptDes]) 	VALUES('001001065',1,'รายงาน - เปรียบเทียบการสั่งซื้อกับยอดขายแฟรนไซส์',NULL);
INSERT INTO [dbo].[TSysReport_L]([FTRptCode],[FNLngID],[FTRptName],[FTRptDes])	VALUES('001001065',2,'Report comparing orders and sales of franchises',NULL);


DELETE [dbo].[TCNTUsrFuncRpt] WHERE FTRolCode = '00002' AND FTUfrRef = '001001065'
INSERT INTO [dbo].[TCNTUsrFuncRpt](
	[FTRolCode],[FTUfrType],[FTUfrGrpRef],[FTUfrRef],[FTGhdApp],[FTUfrStaAlw],[FTUfrStaFavorite],[FDLastUpdOn],[FTLastUpdBy],[FDCreateOn],[FTCreateBy]
)
VALUES(
	'00002','2','001001','001001065',NULL,'1','0',NULL,NULL,'2022-08-24 14:20:43.000','00002'
)

-- =======================================================================================================================================

-- ==================================================== Insert รายงาน - ข้อมูลจ่ายโอนรับโอน ====================================================
DELETE FROM [dbo].[TSysReport]		WHERE FTRptCode	= '009001018';
DELETE FROM [dbo].[TSysReport_L]	WHERE FTRptCode	= '009001018';

INSERT INTO [dbo].[TSysReport](
[FTRptCode]
,[FTGrpRptModCode]
,[FTGrpRptCode]
,[FTRptRoute]
,[FTRptStaUseFrm]
,[FTRptTblView]
,[FTRptFilterCol]
,[FTRptFileName]
,[FTRptStaShwBch]
,[FTRptStaShwYear]
,[FTRptSeqNo]
,[FTRptStaUse]
,[FTLicPdtCode]
)
VALUES (
	'009001018','009','009001','rptTrfpmtinf','NULL','NULL','1,2,4,13,8,9,84,85',NULL,'1','1','18','1','SB-RPT009001018'
);

INSERT INTO [dbo].[TSysReport_L](
[FTRptCode]
,[FNLngID]
,[FTRptName]
,[FTRptDes])
VALUES(
	'009001018','1','รายงาน - ข้อมูลจ่ายโอนรับโอน',NULL
);
INSERT INTO [dbo].[TSysReport_L](
[FTRptCode]
,[FNLngID]
,[FTRptName]
,[FTRptDes])
VALUES(
	'009001018','2','Report Transfer Payment Information',NULL
);

DELETE [dbo].[TCNTUsrFuncRpt] WHERE FTRolCode = '00002' AND FTUfrRef = '009001018'
INSERT INTO [dbo].[TCNTUsrFuncRpt](
[FTRolCode]
,[FTUfrType]
,[FTUfrGrpRef]
,[FTUfrRef]
,[FTGhdApp]
,[FTUfrStaAlw]
,[FTUfrStaFavorite]
,[FDLastUpdOn]
,[FTLastUpdBy]
,[FDCreateOn]
,[FTCreateBy]
)
VALUES(
	'00002','2','009001','009001018',NULL,'1','1','2022-08-30 15:14:14.627','00002','2022-08-30 15:14:14.627','00002'
);

-- =======================================================================================================================================