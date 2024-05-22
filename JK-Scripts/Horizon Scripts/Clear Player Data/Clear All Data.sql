DELETE
  FROM [GatewayDB].[dbo].[BounceBack_Awards]
GO

DELETE
  FROM [GatewayDB].[dbo].[BounceBack_DailyAwardTotals]
GO

DELETE
  FROM [GatewayDB].[dbo].[CommunityPrize_Ante]
GO

DELETE
  FROM [GatewayDB].[dbo].[CommunityPrize_GTGamePlayStatus]
GO

DELETE
  FROM [GatewayDB].[dbo].[CommunityPrize_Promotions]
GO

DELETE
  FROM [GatewayDB].[dbo].[MtAudit]
GO

DELETE
  FROM [GatewayDB].[dbo].[GtGameStatus]
GO

DELETE
  FROM [GatewayDB].[dbo].[GtDay]
GO

DELETE
  FROM [GatewayDB].[dbo].[GtStatus]
GO

DELETE
  FROM [GatewayDB].[dbo].[Gt]
GO

DELETE
  FROM [GatewayDB].[dbo].[EGMGamePlayHistory]
GO

DELETE
  FROM [GatewayDB].[dbo].[Session]
GO

DELETE
  FROM [GatewayDB].[dbo].[Event]
GO

DELETE
  FROM [GatewayDB].[dbo].[Device]
GO

DELETE
  FROM [GatewayDB].[dbo].[ExportErrors]
 GO
 
DELETE
  FROM [GatewayDB].[dbo].[GameStatus]
GO

DELETE
  FROM [GatewayDB].[dbo].[GtGameDay]
GO

DELETE
  FROM [GatewayDB].[dbo].[GtStatus]
GO

DELETE
  FROM [GatewayDB].[dbo].[New_PlaySession]
GO

DELETE
  FROM [GatewayDB].[dbo].[PlayerTracking_Audit]
GO

DELETE
  FROM [GatewayDB].[dbo].[PlayerTracking_Audit_Archive]
GO

DELETE
  FROM [GatewayDB].[dbo].[PlayerTracking_PlayerCard] WHERE iCardID > 4
GO

DELETE
  FROM [GatewayDB].[dbo].[PlayerTracking_PlayerCardEOD] WHERE iCardID > 4
GO

DELETE
  FROM [GatewayDB].[dbo].[PlayerTracking_Users] WHERE iCardID > 4
GO

DELETE
  FROM [GatewayDB].[dbo].[PlayerTracking_Points] WHERE iCardID > 4
GO

DELETE
  FROM [GatewayDB].[dbo].[PlayerTracking_GamePlay] WHERE iCardID > 4
GO

DELETE
  FROM [GatewayDB].[dbo].[PlayerTracking_GamePlay_Archive] WHERE iCardID > 4
GO

DELETE
  FROM [GatewayDB].[dbo].[PlayerTracking_Card] WHERE iCardID > 4
GO

DELETE
  FROM [GatewayDB].[dbo].[PlayerTracking_Event]
GO

DELETE
  FROM [GatewayDB].[dbo].[PlayerTracking_Event_Archive]
GO

DELETE
  FROM [GatewayDB].[dbo].[PlayerTracking_PlayerComp]
GO

DELETE
  FROM [GatewayDB].[dbo].[PlayerTrans]
GO

DELETE
  FROM [GatewayDB].[dbo].[PlaySession]
GO

DELETE
  FROM [GatewayDB].[dbo].[ProgDay]
GO

DELETE
  FROM [GatewayDB].[dbo].[ProgHistory]
GO

DELETE
  FROM [GatewayDB].[dbo].[SessionTrans]
GO

DELETE
  FROM [GatewayDB].[dbo].[SpinData]
GO

DELETE
  FROM [GatewayDB].[dbo].[SpinData_Archive]
GO

DELETE
  FROM [GatewayDB].[dbo].[CommunityPrize_ProgressivePrizeSlot]
GO

DELETE
  FROM [GatewayDB].[dbo].[SweepstakesAwardedPrizesData]
GO

DELETE
  FROM [GatewayDB].[dbo].[SweepstakesParSheetData]
GO

DELETE
  FROM [GatewayDB].[dbo].[Sweepstakes]
GO

DELETE
  FROM [GatewayDB].[dbo].[SweepstakesAwardedPrizes]
GO


DBCC CHECKIDENT ('[GatewayDB].[dbo].[Device]', RESEED, 0)
GO
DBCC CHECKIDENT ('[GatewayDB].[dbo].[MtAudit]', RESEED, 0)
GO
DBCC CHECKIDENT ('[GatewayDB].[dbo].[GT]', RESEED, 0)
GO
DBCC CHECKIDENT ('[GatewayDB].[dbo].[PlayerTracking_Card]', RESEED, 4)
GO

UPDATE [GatewayDB].[dbo].[PlayerTracking_Card]
SET
	strCardNumber = 437353
WHERE
	iCardID = 4;
GO