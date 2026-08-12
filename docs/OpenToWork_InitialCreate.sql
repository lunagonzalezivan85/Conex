-- ============================================================
-- OpenToWork - Initial Database Schema (MySQL 8.0+)
-- ============================================================
-- This script creates the complete database schema for Phase 1.
-- 
-- Usage:
--   1. Create the database:  CREATE DATABASE OpenToWorkDb CHARACTER SET utf8mb4;
--   2. Run this script against the database.
--   3. Update the connection string in src/OpenToWork.API/appsettings.json
--
-- Table Prefixes:
--   SC_ = Security (Users, UserRoles, RefreshTokens, UserDevices)
--   PT_ = Portal  (Candidates, Companies, TempVacancies, Skills)
--   SY_ = System  (WizardSteps, UserPreferences)
--
-- All tables include audit fields: CreatedAt, CreatedBy, UpdatedAt,
-- UpdatedBy, IsDeleted, DeletedAt, DeletedBy
-- ============================================================

CREATE TABLE IF NOT EXISTS `__EFMigrationsHistory` (
    `MigrationId` varchar(150) CHARACTER SET utf8mb4 NOT NULL,
    `ProductVersion` varchar(32) CHARACTER SET utf8mb4 NOT NULL,
    CONSTRAINT `PK___EFMigrationsHistory` PRIMARY KEY (`MigrationId`)
) CHARACTER SET=utf8mb4;

START TRANSACTION;

ALTER DATABASE CHARACTER SET utf8mb4;

CREATE TABLE `PT_Skills` (
    `Id` char(36) COLLATE ascii_general_ci NOT NULL,
    `Name` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
    `Category` varchar(100) CHARACTER SET utf8mb4 NULL,
    `CreatedAt` datetime(6) NOT NULL,
    `CreatedBy` char(36) COLLATE ascii_general_ci NULL,
    `UpdatedAt` datetime(6) NULL,
    `UpdatedBy` char(36) COLLATE ascii_general_ci NULL,
    `IsDeleted` tinyint(1) NOT NULL,
    `DeletedAt` datetime(6) NULL,
    `DeletedBy` char(36) COLLATE ascii_general_ci NULL,
    CONSTRAINT `PK_PT_Skills` PRIMARY KEY (`Id`)
) CHARACTER SET=utf8mb4;

CREATE TABLE `SC_Users` (
    `Id` char(36) COLLATE ascii_general_ci NOT NULL,
    `Email` varchar(256) CHARACTER SET utf8mb4 NOT NULL,
    `PasswordHash` longtext CHARACTER SET utf8mb4 NULL,
    `PrimaryRole` int NOT NULL DEFAULT 0,
    `Identification` varchar(50) CHARACTER SET utf8mb4 NULL,
    `Phone` varchar(20) CHARACTER SET utf8mb4 NULL,
    `EmailVerified` tinyint(1) NOT NULL DEFAULT FALSE,
    `GoogleId` varchar(256) CHARACTER SET utf8mb4 NULL,
    `IsActive` tinyint(1) NOT NULL DEFAULT TRUE,
    `LastLoginAt` datetime(6) NULL,
    `CreatedAt` datetime(6) NOT NULL,
    `CreatedBy` char(36) COLLATE ascii_general_ci NULL,
    `UpdatedAt` datetime(6) NULL,
    `UpdatedBy` char(36) COLLATE ascii_general_ci NULL,
    `IsDeleted` tinyint(1) NOT NULL,
    `DeletedAt` datetime(6) NULL,
    `DeletedBy` char(36) COLLATE ascii_general_ci NULL,
    CONSTRAINT `PK_SC_Users` PRIMARY KEY (`Id`)
) CHARACTER SET=utf8mb4;

CREATE TABLE `SY_WizardSteps` (
    `Id` char(36) COLLATE ascii_general_ci NOT NULL,
    `StepNumber` int NOT NULL,
    `StepName` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
    `StepTitle` varchar(200) CHARACTER SET utf8mb4 NOT NULL,
    `Description` varchar(500) CHARACTER SET utf8mb4 NULL,
    `IsRequired` tinyint(1) NOT NULL,
    `Order` int NOT NULL,
    `Phase` int NOT NULL,
    `CreatedAt` datetime(6) NOT NULL,
    `CreatedBy` char(36) COLLATE ascii_general_ci NULL,
    `UpdatedAt` datetime(6) NULL,
    `UpdatedBy` char(36) COLLATE ascii_general_ci NULL,
    `IsDeleted` tinyint(1) NOT NULL,
    `DeletedAt` datetime(6) NULL,
    `DeletedBy` char(36) COLLATE ascii_general_ci NULL,
    CONSTRAINT `PK_SY_WizardSteps` PRIMARY KEY (`Id`)
) CHARACTER SET=utf8mb4;

CREATE TABLE `PT_Candidates` (
    `Id` char(36) COLLATE ascii_general_ci NOT NULL,
    `SCUserId` char(36) COLLATE ascii_general_ci NOT NULL,
    `FirstName` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
    `LastName` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
    `Identification` varchar(50) CHARACTER SET utf8mb4 NULL,
    `Phone` varchar(20) CHARACTER SET utf8mb4 NULL,
    `BirthDate` datetime(6) NULL,
    `Gender` int NULL,
    `Title` varchar(200) CHARACTER SET utf8mb4 NULL,
    `Summary` longtext CHARACTER SET utf8mb4 NULL,
    `CvUrl` varchar(500) CHARACTER SET utf8mb4 NULL,
    `ProfilePictureUrl` varchar(500) CHARACTER SET utf8mb4 NULL,
    `Country` varchar(100) CHARACTER SET utf8mb4 NULL,
    `City` varchar(100) CHARACTER SET utf8mb4 NULL,
    `Address` varchar(300) CHARACTER SET utf8mb4 NULL,
    `WizardCompleted` tinyint(1) NOT NULL DEFAULT FALSE,
    `WizardStep` int NOT NULL DEFAULT 0,
    `CreatedAt` datetime(6) NOT NULL,
    `CreatedBy` char(36) COLLATE ascii_general_ci NULL,
    `UpdatedAt` datetime(6) NULL,
    `UpdatedBy` char(36) COLLATE ascii_general_ci NULL,
    `IsDeleted` tinyint(1) NOT NULL,
    `DeletedAt` datetime(6) NULL,
    `DeletedBy` char(36) COLLATE ascii_general_ci NULL,
    CONSTRAINT `PK_PT_Candidates` PRIMARY KEY (`Id`),
    CONSTRAINT `FK_PT_Candidates_SC_Users_SCUserId` FOREIGN KEY (`SCUserId`) REFERENCES `SC_Users` (`Id`) ON DELETE CASCADE
) CHARACTER SET=utf8mb4;

CREATE TABLE `PT_Companies` (
    `Id` char(36) COLLATE ascii_general_ci NOT NULL,
    `SCUserId` char(36) COLLATE ascii_general_ci NOT NULL,
    `Name` varchar(200) CHARACTER SET utf8mb4 NOT NULL,
    `Description` longtext CHARACTER SET utf8mb4 NULL,
    `Website` varchar(500) CHARACTER SET utf8mb4 NULL,
    `LogoUrl` varchar(500) CHARACTER SET utf8mb4 NULL,
    `Country` varchar(100) CHARACTER SET utf8mb4 NULL,
    `City` varchar(100) CHARACTER SET utf8mb4 NULL,
    `Address` varchar(300) CHARACTER SET utf8mb4 NULL,
    `CreatedAt` datetime(6) NOT NULL,
    `CreatedBy` char(36) COLLATE ascii_general_ci NULL,
    `UpdatedAt` datetime(6) NULL,
    `UpdatedBy` char(36) COLLATE ascii_general_ci NULL,
    `IsDeleted` tinyint(1) NOT NULL,
    `DeletedAt` datetime(6) NULL,
    `DeletedBy` char(36) COLLATE ascii_general_ci NULL,
    CONSTRAINT `PK_PT_Companies` PRIMARY KEY (`Id`),
    CONSTRAINT `FK_PT_Companies_SC_Users_SCUserId` FOREIGN KEY (`SCUserId`) REFERENCES `SC_Users` (`Id`) ON DELETE CASCADE
) CHARACTER SET=utf8mb4;

CREATE TABLE `PT_TempVacancies` (
    `Id` char(36) COLLATE ascii_general_ci NOT NULL,
    `SCUserId` char(36) COLLATE ascii_general_ci NOT NULL,
    `Title` varchar(200) CHARACTER SET utf8mb4 NOT NULL,
    `Description` longtext CHARACTER SET utf8mb4 NULL,
    `Requirements` longtext CHARACTER SET utf8mb4 NULL,
    `SalaryMin` decimal(65,30) NULL,
    `SalaryMax` decimal(65,30) NULL,
    `Location` varchar(200) CHARACTER SET utf8mb4 NULL,
    `ContractType` int NOT NULL,
    `ExpiresAt` datetime(6) NOT NULL,
    `IsPublished` tinyint(1) NOT NULL,
    `CreatedAt` datetime(6) NOT NULL,
    `CreatedBy` char(36) COLLATE ascii_general_ci NULL,
    `UpdatedAt` datetime(6) NULL,
    `UpdatedBy` char(36) COLLATE ascii_general_ci NULL,
    `IsDeleted` tinyint(1) NOT NULL,
    `DeletedAt` datetime(6) NULL,
    `DeletedBy` char(36) COLLATE ascii_general_ci NULL,
    CONSTRAINT `PK_PT_TempVacancies` PRIMARY KEY (`Id`),
    CONSTRAINT `FK_PT_TempVacancies_SC_Users_SCUserId` FOREIGN KEY (`SCUserId`) REFERENCES `SC_Users` (`Id`) ON DELETE CASCADE
) CHARACTER SET=utf8mb4;

CREATE TABLE `SC_RefreshTokens` (
    `Id` char(36) COLLATE ascii_general_ci NOT NULL,
    `SCUserId` char(36) COLLATE ascii_general_ci NOT NULL,
    `TokenHash` varchar(512) CHARACTER SET utf8mb4 NOT NULL,
    `ExpiresAt` datetime(6) NOT NULL,
    `IsRevoked` tinyint(1) NOT NULL,
    `ReplacedByTokenHash` varchar(512) CHARACTER SET utf8mb4 NULL,
    `CreatedAt` datetime(6) NOT NULL,
    `CreatedBy` char(36) COLLATE ascii_general_ci NULL,
    `UpdatedAt` datetime(6) NULL,
    `UpdatedBy` char(36) COLLATE ascii_general_ci NULL,
    `IsDeleted` tinyint(1) NOT NULL,
    `DeletedAt` datetime(6) NULL,
    `DeletedBy` char(36) COLLATE ascii_general_ci NULL,
    CONSTRAINT `PK_SC_RefreshTokens` PRIMARY KEY (`Id`),
    CONSTRAINT `FK_SC_RefreshTokens_SC_Users_SCUserId` FOREIGN KEY (`SCUserId`) REFERENCES `SC_Users` (`Id`) ON DELETE CASCADE
) CHARACTER SET=utf8mb4;

CREATE TABLE `SC_UserDevices` (
    `Id` char(36) COLLATE ascii_general_ci NOT NULL,
    `SCUserId` char(36) COLLATE ascii_general_ci NOT NULL,
    `DeviceHash` varchar(256) CHARACTER SET utf8mb4 NOT NULL,
    `DeviceName` varchar(200) CHARACTER SET utf8mb4 NULL,
    `FirstSeenAt` datetime(6) NOT NULL,
    `LastSeenAt` datetime(6) NOT NULL,
    `IsTrusted` tinyint(1) NOT NULL,
    `CreatedAt` datetime(6) NOT NULL,
    `CreatedBy` char(36) COLLATE ascii_general_ci NULL,
    `UpdatedAt` datetime(6) NULL,
    `UpdatedBy` char(36) COLLATE ascii_general_ci NULL,
    `IsDeleted` tinyint(1) NOT NULL,
    `DeletedAt` datetime(6) NULL,
    `DeletedBy` char(36) COLLATE ascii_general_ci NULL,
    CONSTRAINT `PK_SC_UserDevices` PRIMARY KEY (`Id`),
    CONSTRAINT `FK_SC_UserDevices_SC_Users_SCUserId` FOREIGN KEY (`SCUserId`) REFERENCES `SC_Users` (`Id`) ON DELETE CASCADE
) CHARACTER SET=utf8mb4;

CREATE TABLE `SC_UserRoles` (
    `Id` char(36) COLLATE ascii_general_ci NOT NULL,
    `SCUserId` char(36) COLLATE ascii_general_ci NOT NULL,
    `Role` int NOT NULL,
    `AssignedAt` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    `CreatedAt` datetime(6) NOT NULL,
    `CreatedBy` char(36) COLLATE ascii_general_ci NULL,
    `UpdatedAt` datetime(6) NULL,
    `UpdatedBy` char(36) COLLATE ascii_general_ci NULL,
    `IsDeleted` tinyint(1) NOT NULL,
    `DeletedAt` datetime(6) NULL,
    `DeletedBy` char(36) COLLATE ascii_general_ci NULL,
    CONSTRAINT `PK_SC_UserRoles` PRIMARY KEY (`Id`),
    CONSTRAINT `FK_SC_UserRoles_SC_Users_SCUserId` FOREIGN KEY (`SCUserId`) REFERENCES `SC_Users` (`Id`) ON DELETE CASCADE
) CHARACTER SET=utf8mb4;

CREATE TABLE `SY_UserPreferences` (
    `Id` char(36) COLLATE ascii_general_ci NOT NULL,
    `SCUserId` char(36) COLLATE ascii_general_ci NOT NULL,
    `Theme` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT 'navy',
    `Language` varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT 'es',
    `PreferredRole` int NULL,
    `CreatedAt` datetime(6) NOT NULL,
    `CreatedBy` char(36) COLLATE ascii_general_ci NULL,
    `UpdatedAt` datetime(6) NULL,
    `UpdatedBy` char(36) COLLATE ascii_general_ci NULL,
    `IsDeleted` tinyint(1) NOT NULL,
    `DeletedAt` datetime(6) NULL,
    `DeletedBy` char(36) COLLATE ascii_general_ci NULL,
    CONSTRAINT `PK_SY_UserPreferences` PRIMARY KEY (`Id`),
    CONSTRAINT `FK_SY_UserPreferences_SC_Users_SCUserId` FOREIGN KEY (`SCUserId`) REFERENCES `SC_Users` (`Id`) ON DELETE CASCADE
) CHARACTER SET=utf8mb4;

CREATE TABLE `PT_CandidateSkills` (
    `Id` char(36) COLLATE ascii_general_ci NOT NULL,
    `PT_CandidateId` char(36) COLLATE ascii_general_ci NOT NULL,
    `PT_SkillId` char(36) COLLATE ascii_general_ci NOT NULL,
    `ProficiencyLevel` int NULL,
    `CreatedAt` datetime(6) NOT NULL,
    `CreatedBy` char(36) COLLATE ascii_general_ci NULL,
    `UpdatedAt` datetime(6) NULL,
    `UpdatedBy` char(36) COLLATE ascii_general_ci NULL,
    `IsDeleted` tinyint(1) NOT NULL,
    `DeletedAt` datetime(6) NULL,
    `DeletedBy` char(36) COLLATE ascii_general_ci NULL,
    CONSTRAINT `PK_PT_CandidateSkills` PRIMARY KEY (`Id`),
    CONSTRAINT `FK_PT_CandidateSkills_PT_Candidates_PT_CandidateId` FOREIGN KEY (`PT_CandidateId`) REFERENCES `PT_Candidates` (`Id`) ON DELETE CASCADE,
    CONSTRAINT `FK_PT_CandidateSkills_PT_Skills_PT_SkillId` FOREIGN KEY (`PT_SkillId`) REFERENCES `PT_Skills` (`Id`) ON DELETE CASCADE
) CHARACTER SET=utf8mb4;

INSERT INTO `SY_WizardSteps` (`Id`, `CreatedAt`, `CreatedBy`, `DeletedAt`, `DeletedBy`, `Description`, `IsDeleted`, `IsRequired`, `Order`, `Phase`, `StepName`, `StepNumber`, `StepTitle`, `UpdatedAt`, `UpdatedBy`)
VALUES ('12dccc0e-4f0d-452c-bbd3-3c10d041646a', TIMESTAMP '2026-08-12 03:05:30', NULL, NULL, NULL, 'Where are you located?', FALSE, TRUE, 2, 1, 'Location', 2, 'Location', NULL, NULL),
('6ed148be-adbb-4d53-a652-3f220a79ebc6', TIMESTAMP '2026-08-12 03:05:30', NULL, NULL, NULL, 'Your professional information', FALSE, TRUE, 3, 1, 'ProfessionalProfile', 3, 'Professional Profile', NULL, NULL),
('73b9d550-e578-484d-a666-7517c55d33a1', TIMESTAMP '2026-08-12 03:05:30', NULL, NULL, NULL, 'Tell us about yourself', FALSE, TRUE, 1, 1, 'PersonalData', 1, 'Personal Data', NULL, NULL),
('86419f0a-c00d-4404-85b1-59f0d68b4673', TIMESTAMP '2026-08-12 03:05:30', NULL, NULL, NULL, 'Choose your preference', FALSE, TRUE, 5, 1, 'Preferences', 5, 'What do you want to do?', NULL, NULL),
('e1b54569-71d6-430d-86e9-8f151f377777', TIMESTAMP '2026-08-12 03:05:30', NULL, NULL, NULL, 'Verify your data is correct', FALSE, TRUE, 6, 1, 'Confirmation', 6, 'Review and Confirm', NULL, NULL),
('fb535ba9-4da7-44d3-b861-31b84cc7cbf2', TIMESTAMP '2026-08-12 03:05:30', NULL, NULL, NULL, 'Select your skills', FALSE, FALSE, 4, 1, 'Skills', 4, 'Skills', NULL, NULL);

CREATE INDEX `IX_PT_Candidates_Identification` ON `PT_Candidates` (`Identification`);

CREATE UNIQUE INDEX `IX_PT_Candidates_SCUserId` ON `PT_Candidates` (`SCUserId`);

CREATE UNIQUE INDEX `IX_PT_Candidates_SCUserId_IsDeleted` ON `PT_Candidates` (`SCUserId`, `IsDeleted`);

CREATE INDEX `IX_PT_Candidates_WizardCompleted_IsDeleted` ON `PT_Candidates` (`WizardCompleted`, `IsDeleted`);

CREATE UNIQUE INDEX `IX_PT_CandidateSkills_PT_CandidateId_PT_SkillId_IsDeleted` ON `PT_CandidateSkills` (`PT_CandidateId`, `PT_SkillId`, `IsDeleted`);

CREATE INDEX `IX_PT_CandidateSkills_PT_SkillId_IsDeleted` ON `PT_CandidateSkills` (`PT_SkillId`, `IsDeleted`);

CREATE INDEX `IX_PT_Companies_Name_IsDeleted` ON `PT_Companies` (`Name`, `IsDeleted`);

CREATE UNIQUE INDEX `IX_PT_Companies_SCUserId` ON `PT_Companies` (`SCUserId`);

CREATE UNIQUE INDEX `IX_PT_Companies_SCUserId_IsDeleted` ON `PT_Companies` (`SCUserId`, `IsDeleted`);

CREATE INDEX `IX_PT_Skills_Category_IsDeleted` ON `PT_Skills` (`Category`, `IsDeleted`);

CREATE UNIQUE INDEX `IX_PT_Skills_Name_IsDeleted` ON `PT_Skills` (`Name`, `IsDeleted`);

CREATE INDEX `IX_PT_TempVacancies_ExpiresAt_IsDeleted` ON `PT_TempVacancies` (`ExpiresAt`, `IsDeleted`);

CREATE INDEX `IX_PT_TempVacancies_IsPublished_IsDeleted` ON `PT_TempVacancies` (`IsPublished`, `IsDeleted`);

CREATE INDEX `IX_PT_TempVacancies_SCUserId_IsDeleted` ON `PT_TempVacancies` (`SCUserId`, `IsDeleted`);

CREATE INDEX `IX_SC_RefreshTokens_SCUserId_IsRevoked_IsDeleted` ON `SC_RefreshTokens` (`SCUserId`, `IsRevoked`, `IsDeleted`);

CREATE UNIQUE INDEX `IX_SC_RefreshTokens_TokenHash` ON `SC_RefreshTokens` (`TokenHash`);

CREATE UNIQUE INDEX `IX_SC_UserDevices_SCUserId_DeviceHash_IsDeleted` ON `SC_UserDevices` (`SCUserId`, `DeviceHash`, `IsDeleted`);

CREATE INDEX `IX_SC_UserDevices_SCUserId_IsTrusted` ON `SC_UserDevices` (`SCUserId`, `IsTrusted`);

CREATE INDEX `IX_SC_UserRoles_Role` ON `SC_UserRoles` (`Role`);

CREATE UNIQUE INDEX `IX_SC_UserRoles_SCUserId_Role_IsDeleted` ON `SC_UserRoles` (`SCUserId`, `Role`, `IsDeleted`);

CREATE UNIQUE INDEX `IX_SC_Users_Email` ON `SC_Users` (`Email`);

CREATE UNIQUE INDEX `IX_SC_Users_GoogleId` ON `SC_Users` (`GoogleId`);

CREATE INDEX `IX_SC_Users_IsActive_IsDeleted` ON `SC_Users` (`IsActive`, `IsDeleted`);

CREATE UNIQUE INDEX `IX_SY_UserPreferences_SCUserId` ON `SY_UserPreferences` (`SCUserId`);

CREATE UNIQUE INDEX `IX_SY_UserPreferences_SCUserId_IsDeleted` ON `SY_UserPreferences` (`SCUserId`, `IsDeleted`);

CREATE INDEX `IX_SY_WizardSteps_Order_Phase_IsDeleted` ON `SY_WizardSteps` (`Order`, `Phase`, `IsDeleted`);

CREATE UNIQUE INDEX `IX_SY_WizardSteps_StepNumber_IsDeleted` ON `SY_WizardSteps` (`StepNumber`, `IsDeleted`);

INSERT INTO `__EFMigrationsHistory` (`MigrationId`, `ProductVersion`)
VALUES ('20260812030531_InitialCreate', '8.0.2');

COMMIT;

