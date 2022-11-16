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
GO
-- ==================================================== Update route, filter รายงาน - วิเคราะห์การซื้อตามเงื่อนไข  ====================================================
UPDATE TSysReport SET FTRptRoute = 'rptAnalysPurchase' , FTRptFilterCol = '1,2,4,13,90' WHERE FTRptCode = '007001006'

INSERT INTO TSysReportFilter (FTRptFltCode, FTRptFltStaFrm, FTRptFltStaTo, FTRptGrpFlt)
VALUES ('90', '1', '0', 'G10')

INSERT INTO TSysReportFilter_L (FTRptFltCode, FNLngID, FTRptFltName)
VALUES ('90', 1, 'เงื่อนไขรายงาน')

INSERT INTO TSysReportFilter_L (FTRptFltCode, FNLngID, FTRptFltName)
VALUES ('90', 2, 'Condition Report')


-- ==================================================== สร้าง รายงาน - ข้อมูลใบแลกของพรีเมี่ยม  ====================================================
INSERT [TCNTUsrFuncRpt] ([FTRolCode], [FTUfrType], [FTUfrGrpRef], [FTUfrRef], [FTGhdApp], [FTUfrStaAlw], [FTUfrStaFavorite], [FDLastUpdOn], [FTLastUpdBy], [FDCreateOn], [FTCreateBy]) 
VALUES ('00002', '2', '001001', '001001067', NULL, '1', '0', GETDATE(), '00002', GETDATE(), '00002')

INSERT [TSysReport] ([FTRptCode], [FTGrpRptModCode], [FTGrpRptCode], [FTRptRoute], [FTRptStaUseFrm], [FTRptTblView], [FTRptFilterCol], [FTRptFileName], [FTRptStaShwBch], [FTRptStaShwYear], [FTRptSeqNo], [FTRptStaUse], [FTLicPdtCode]) 
VALUES ('001001067', '001', '001001', 'rptPremRedem', NULL, NULL, '1,4,27,13', NULL, '1', '1', 45, '1', 'SB-RPT001001067')

INSERT [TSysReport_L] ([FTRptCode], [FNLngID], [FTRptName], [FTRptDes]) VALUES ('001001067', 1, 'รายงาน - ข้อมูลใบแลกของพรีเมี่ยม', NULL)

INSERT [TSysReport_L] ([FTRptCode], [FNLngID], [FTRptName], [FTRptDes]) VALUES ('001001067', 2, 'Report - Premium Redem', NULL)
