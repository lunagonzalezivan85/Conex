using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

#pragma warning disable CA1814 // Prefer jagged arrays over multidimensional

namespace OpenToWork.Models.Migrations
{
    /// <inheritdoc />
    public partial class Phase2 : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("12dccc0e-4f0d-452c-bbd3-3c10d041646a"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("6ed148be-adbb-4d53-a652-3f220a79ebc6"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("73b9d550-e578-484d-a666-7517c55d33a1"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("86419f0a-c00d-4404-85b1-59f0d68b4673"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("e1b54569-71d6-430d-86e9-8f151f377777"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("fb535ba9-4da7-44d3-b861-31b84cc7cbf2"));

            migrationBuilder.AddColumn<string>(
                name: "Category",
                table: "PT_TempVacancies",
                type: "varchar(100)",
                maxLength: 100,
                nullable: true)
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.AddColumn<int>(
                name: "EnglishLevel",
                table: "PT_TempVacancies",
                type: "int",
                nullable: true);

            migrationBuilder.AddColumn<int>(
                name: "ExperienceLevel",
                table: "PT_TempVacancies",
                type: "int",
                nullable: true);

            migrationBuilder.AddColumn<int>(
                name: "WorkMode",
                table: "PT_TempVacancies",
                type: "int",
                nullable: false,
                defaultValue: 0);

            migrationBuilder.AddColumn<int>(
                name: "CompanySize",
                table: "PT_Companies",
                type: "int",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "ContactEmail",
                table: "PT_Companies",
                type: "varchar(256)",
                maxLength: 256,
                nullable: true)
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.AddColumn<string>(
                name: "ContactPhone",
                table: "PT_Companies",
                type: "varchar(20)",
                maxLength: 20,
                nullable: true)
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.AddColumn<string>(
                name: "Industry",
                table: "PT_Companies",
                type: "varchar(100)",
                maxLength: 100,
                nullable: true)
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.AddColumn<bool>(
                name: "IsVerified",
                table: "PT_Companies",
                type: "tinyint(1)",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AddColumn<string>(
                name: "LinkedInUrl",
                table: "PT_Companies",
                type: "varchar(500)",
                maxLength: 500,
                nullable: true)
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.AddColumn<int>(
                name: "Availability",
                table: "PT_Candidates",
                type: "int",
                nullable: true);

            migrationBuilder.AddColumn<DateTime>(
                name: "CompletedAt",
                table: "PT_Candidates",
                type: "datetime(6)",
                nullable: true);

            migrationBuilder.AddColumn<bool>(
                name: "IsProfilePublic",
                table: "PT_Candidates",
                type: "tinyint(1)",
                nullable: false,
                defaultValue: true);

            migrationBuilder.AddColumn<string>(
                name: "LinkedInUrl",
                table: "PT_Candidates",
                type: "varchar(500)",
                maxLength: 500,
                nullable: true)
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.AddColumn<string>(
                name: "PortfolioUrl",
                table: "PT_Candidates",
                type: "varchar(500)",
                maxLength: 500,
                nullable: true)
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.AddColumn<int>(
                name: "WorkAuthorization",
                table: "PT_Candidates",
                type: "int",
                nullable: true);

            migrationBuilder.AddColumn<int>(
                name: "YearsOfExperience",
                table: "PT_Candidates",
                type: "int",
                nullable: true);

            migrationBuilder.CreateTable(
                name: "PT_CandidateCertifications",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    PT_CandidateId = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    Name = table.Column<string>(type: "varchar(200)", maxLength: 200, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    Issuer = table.Column<string>(type: "varchar(200)", maxLength: 200, nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    IssueDate = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    ExpiryDate = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    CredentialId = table.Column<string>(type: "varchar(200)", maxLength: 200, nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    CredentialUrl = table.Column<string>(type: "varchar(500)", maxLength: 500, nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    CreatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    CreatedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci"),
                    UpdatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    UpdatedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci"),
                    IsDeleted = table.Column<bool>(type: "tinyint(1)", nullable: false),
                    DeletedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    DeletedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci")
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PT_CandidateCertifications", x => x.Id);
                    table.ForeignKey(
                        name: "FK_PT_CandidateCertifications_PT_Candidates_PT_CandidateId",
                        column: x => x.PT_CandidateId,
                        principalTable: "PT_Candidates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateTable(
                name: "PT_CandidateEducations",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    PT_CandidateId = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    Institution = table.Column<string>(type: "varchar(200)", maxLength: 200, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    Degree = table.Column<string>(type: "varchar(200)", maxLength: 200, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    FieldOfStudy = table.Column<string>(type: "varchar(200)", maxLength: 200, nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    StartDate = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    EndDate = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    IsInProgress = table.Column<bool>(type: "tinyint(1)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    CreatedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci"),
                    UpdatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    UpdatedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci"),
                    IsDeleted = table.Column<bool>(type: "tinyint(1)", nullable: false),
                    DeletedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    DeletedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci")
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PT_CandidateEducations", x => x.Id);
                    table.ForeignKey(
                        name: "FK_PT_CandidateEducations_PT_Candidates_PT_CandidateId",
                        column: x => x.PT_CandidateId,
                        principalTable: "PT_Candidates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateTable(
                name: "PT_CandidateExperiences",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    PT_CandidateId = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    CompanyName = table.Column<string>(type: "varchar(200)", maxLength: 200, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    JobTitle = table.Column<string>(type: "varchar(200)", maxLength: 200, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    Description = table.Column<string>(type: "longtext", nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    StartDate = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    EndDate = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    IsCurrentJob = table.Column<bool>(type: "tinyint(1)", nullable: false),
                    Location = table.Column<string>(type: "varchar(200)", maxLength: 200, nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    CreatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    CreatedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci"),
                    UpdatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    UpdatedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci"),
                    IsDeleted = table.Column<bool>(type: "tinyint(1)", nullable: false),
                    DeletedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    DeletedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci")
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PT_CandidateExperiences", x => x.Id);
                    table.ForeignKey(
                        name: "FK_PT_CandidateExperiences_PT_Candidates_PT_CandidateId",
                        column: x => x.PT_CandidateId,
                        principalTable: "PT_Candidates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateTable(
                name: "PT_Vacancies",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    PT_CompanyId = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    Title = table.Column<string>(type: "varchar(200)", maxLength: 200, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    Description = table.Column<string>(type: "longtext", nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    Requirements = table.Column<string>(type: "longtext", nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    SalaryMin = table.Column<decimal>(type: "decimal(65,30)", nullable: true),
                    SalaryMax = table.Column<decimal>(type: "decimal(65,30)", nullable: true),
                    Location = table.Column<string>(type: "varchar(200)", maxLength: 200, nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    ContractType = table.Column<int>(type: "int", nullable: false),
                    WorkMode = table.Column<int>(type: "int", nullable: false, defaultValue: 0),
                    Category = table.Column<string>(type: "varchar(100)", maxLength: 100, nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    ExperienceLevel = table.Column<int>(type: "int", nullable: true),
                    EnglishLevel = table.Column<int>(type: "int", nullable: true),
                    Status = table.Column<int>(type: "int", nullable: false, defaultValue: 0),
                    PublishedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    ClosedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    ViewsCount = table.Column<int>(type: "int", nullable: false, defaultValue: 0),
                    CreatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    CreatedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci"),
                    UpdatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    UpdatedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci"),
                    IsDeleted = table.Column<bool>(type: "tinyint(1)", nullable: false),
                    DeletedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    DeletedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci")
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PT_Vacancies", x => x.Id);
                    table.ForeignKey(
                        name: "FK_PT_Vacancies_PT_Companies_PT_CompanyId",
                        column: x => x.PT_CompanyId,
                        principalTable: "PT_Companies",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateTable(
                name: "PT_Applications",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    PT_CandidateId = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    PT_VacancyId = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    Status = table.Column<int>(type: "int", nullable: false, defaultValue: 0),
                    CoverLetter = table.Column<string>(type: "longtext", nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    ExpectedSalary = table.Column<decimal>(type: "decimal(65,30)", nullable: true),
                    AvailableFromDate = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    ApplicationSource = table.Column<int>(type: "int", nullable: false, defaultValue: 0),
                    CreatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    CreatedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci"),
                    UpdatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    UpdatedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci"),
                    IsDeleted = table.Column<bool>(type: "tinyint(1)", nullable: false),
                    DeletedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    DeletedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci")
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PT_Applications", x => x.Id);
                    table.ForeignKey(
                        name: "FK_PT_Applications_PT_Candidates_PT_CandidateId",
                        column: x => x.PT_CandidateId,
                        principalTable: "PT_Candidates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_PT_Applications_PT_Vacancies_PT_VacancyId",
                        column: x => x.PT_VacancyId,
                        principalTable: "PT_Vacancies",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateTable(
                name: "PT_VacancySkills",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    PT_VacancyId = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    PT_SkillId = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    IsRequired = table.Column<bool>(type: "tinyint(1)", nullable: false, defaultValue: true),
                    MinProficiencyLevel = table.Column<int>(type: "int", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    CreatedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci"),
                    UpdatedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    UpdatedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci"),
                    IsDeleted = table.Column<bool>(type: "tinyint(1)", nullable: false),
                    DeletedAt = table.Column<DateTime>(type: "datetime(6)", nullable: true),
                    DeletedBy = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci")
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PT_VacancySkills", x => x.Id);
                    table.ForeignKey(
                        name: "FK_PT_VacancySkills_PT_Skills_PT_SkillId",
                        column: x => x.PT_SkillId,
                        principalTable: "PT_Skills",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_PT_VacancySkills_PT_Vacancies_PT_VacancyId",
                        column: x => x.PT_VacancyId,
                        principalTable: "PT_Vacancies",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.InsertData(
                table: "SY_WizardSteps",
                columns: new[] { "Id", "CreatedAt", "CreatedBy", "DeletedAt", "DeletedBy", "Description", "IsDeleted", "IsRequired", "Order", "Phase", "StepName", "StepNumber", "StepTitle", "UpdatedAt", "UpdatedBy" },
                values: new object[,]
                {
                    { new Guid("2247769a-97fd-4369-992c-b35396494e0f"), new DateTime(2026, 8, 12, 15, 34, 41, 395, DateTimeKind.Utc).AddTicks(9998), null, null, null, "Add your education", false, false, 8, 2, "Education", 8, "Education", null, null },
                    { new Guid("2607b354-db67-43b4-a5aa-72913736e808"), new DateTime(2026, 8, 12, 15, 34, 41, 395, DateTimeKind.Utc).AddTicks(9968), null, null, null, "Where are you located?", false, true, 2, 1, "Location", 2, "Location", null, null },
                    { new Guid("3e1fbc79-e416-408a-80dc-fd8bc347a489"), new DateTime(2026, 8, 12, 15, 34, 41, 396, DateTimeKind.Utc).AddTicks(8), null, null, null, "Upload your CV/resume", false, false, 10, 2, "UploadCV", 10, "Upload CV", null, null },
                    { new Guid("4475bf1b-a9aa-47ed-acdc-8cd588efcc83"), new DateTime(2026, 8, 12, 15, 34, 41, 395, DateTimeKind.Utc).AddTicks(9994), null, null, null, "Add your work experience", false, false, 7, 2, "WorkExperience", 7, "Work Experience", null, null },
                    { new Guid("9cde5469-8a1e-40a8-84fd-84e45e8b467c"), new DateTime(2026, 8, 12, 15, 34, 41, 395, DateTimeKind.Utc).AddTicks(9940), null, null, null, "Tell us about yourself", false, true, 1, 1, "PersonalData", 1, "Personal Data", null, null },
                    { new Guid("aa7a5195-fb45-4418-b6a6-a456f6f242a4"), new DateTime(2026, 8, 12, 15, 34, 41, 395, DateTimeKind.Utc).AddTicks(9978), null, null, null, "Select your skills", false, false, 4, 1, "Skills", 4, "Skills", null, null },
                    { new Guid("b1ef6e16-70bf-4ffa-ada1-c7817cf25708"), new DateTime(2026, 8, 12, 15, 34, 41, 395, DateTimeKind.Utc).AddTicks(9990), null, null, null, "Verify your data is correct", false, true, 6, 1, "Confirmation", 6, "Review and Confirm", null, null },
                    { new Guid("c46557b7-2b2f-44cb-b6c4-4e813bd0d84d"), new DateTime(2026, 8, 12, 15, 34, 41, 395, DateTimeKind.Utc).AddTicks(9982), null, null, null, "Choose your preference", false, true, 5, 1, "Preferences", 5, "What do you want to do?", null, null },
                    { new Guid("f13bec26-b48e-4822-9156-fe0ec553482d"), new DateTime(2026, 8, 12, 15, 34, 41, 395, DateTimeKind.Utc).AddTicks(9974), null, null, null, "Your professional information", false, true, 3, 1, "ProfessionalProfile", 3, "Professional Profile", null, null },
                    { new Guid("f34a9366-acd7-4d87-8bf2-5212c248f9b3"), new DateTime(2026, 8, 12, 15, 34, 41, 396, DateTimeKind.Utc).AddTicks(2), null, null, null, "Add your certifications", false, false, 9, 2, "Certifications", 9, "Certifications", null, null }
                });

            migrationBuilder.CreateIndex(
                name: "IX_PT_Applications_PT_CandidateId_PT_VacancyId_IsDeleted",
                table: "PT_Applications",
                columns: new[] { "PT_CandidateId", "PT_VacancyId", "IsDeleted" },
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_PT_Applications_PT_CandidateId_Status_IsDeleted",
                table: "PT_Applications",
                columns: new[] { "PT_CandidateId", "Status", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_PT_Applications_PT_VacancyId_Status_IsDeleted",
                table: "PT_Applications",
                columns: new[] { "PT_VacancyId", "Status", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_PT_CandidateCertifications_PT_CandidateId_IsDeleted",
                table: "PT_CandidateCertifications",
                columns: new[] { "PT_CandidateId", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_PT_CandidateEducations_PT_CandidateId_IsDeleted",
                table: "PT_CandidateEducations",
                columns: new[] { "PT_CandidateId", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_PT_CandidateExperiences_CompanyName_IsDeleted",
                table: "PT_CandidateExperiences",
                columns: new[] { "CompanyName", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_PT_CandidateExperiences_PT_CandidateId_IsDeleted",
                table: "PT_CandidateExperiences",
                columns: new[] { "PT_CandidateId", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_PT_Vacancies_Category_Status_IsDeleted",
                table: "PT_Vacancies",
                columns: new[] { "Category", "Status", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_PT_Vacancies_Location_Status_IsDeleted",
                table: "PT_Vacancies",
                columns: new[] { "Location", "Status", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_PT_Vacancies_PT_CompanyId_IsDeleted",
                table: "PT_Vacancies",
                columns: new[] { "PT_CompanyId", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_PT_Vacancies_Status_IsDeleted",
                table: "PT_Vacancies",
                columns: new[] { "Status", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_PT_Vacancies_WorkMode_Status_IsDeleted",
                table: "PT_Vacancies",
                columns: new[] { "WorkMode", "Status", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_PT_VacancySkills_PT_SkillId_IsDeleted",
                table: "PT_VacancySkills",
                columns: new[] { "PT_SkillId", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_PT_VacancySkills_PT_VacancyId_PT_SkillId_IsDeleted",
                table: "PT_VacancySkills",
                columns: new[] { "PT_VacancyId", "PT_SkillId", "IsDeleted" },
                unique: true);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "PT_Applications");

            migrationBuilder.DropTable(
                name: "PT_CandidateCertifications");

            migrationBuilder.DropTable(
                name: "PT_CandidateEducations");

            migrationBuilder.DropTable(
                name: "PT_CandidateExperiences");

            migrationBuilder.DropTable(
                name: "PT_VacancySkills");

            migrationBuilder.DropTable(
                name: "PT_Vacancies");

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("2247769a-97fd-4369-992c-b35396494e0f"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("2607b354-db67-43b4-a5aa-72913736e808"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("3e1fbc79-e416-408a-80dc-fd8bc347a489"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("4475bf1b-a9aa-47ed-acdc-8cd588efcc83"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("9cde5469-8a1e-40a8-84fd-84e45e8b467c"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("aa7a5195-fb45-4418-b6a6-a456f6f242a4"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("b1ef6e16-70bf-4ffa-ada1-c7817cf25708"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("c46557b7-2b2f-44cb-b6c4-4e813bd0d84d"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("f13bec26-b48e-4822-9156-fe0ec553482d"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("f34a9366-acd7-4d87-8bf2-5212c248f9b3"));

            migrationBuilder.DropColumn(
                name: "Category",
                table: "PT_TempVacancies");

            migrationBuilder.DropColumn(
                name: "EnglishLevel",
                table: "PT_TempVacancies");

            migrationBuilder.DropColumn(
                name: "ExperienceLevel",
                table: "PT_TempVacancies");

            migrationBuilder.DropColumn(
                name: "WorkMode",
                table: "PT_TempVacancies");

            migrationBuilder.DropColumn(
                name: "CompanySize",
                table: "PT_Companies");

            migrationBuilder.DropColumn(
                name: "ContactEmail",
                table: "PT_Companies");

            migrationBuilder.DropColumn(
                name: "ContactPhone",
                table: "PT_Companies");

            migrationBuilder.DropColumn(
                name: "Industry",
                table: "PT_Companies");

            migrationBuilder.DropColumn(
                name: "IsVerified",
                table: "PT_Companies");

            migrationBuilder.DropColumn(
                name: "LinkedInUrl",
                table: "PT_Companies");

            migrationBuilder.DropColumn(
                name: "Availability",
                table: "PT_Candidates");

            migrationBuilder.DropColumn(
                name: "CompletedAt",
                table: "PT_Candidates");

            migrationBuilder.DropColumn(
                name: "IsProfilePublic",
                table: "PT_Candidates");

            migrationBuilder.DropColumn(
                name: "LinkedInUrl",
                table: "PT_Candidates");

            migrationBuilder.DropColumn(
                name: "PortfolioUrl",
                table: "PT_Candidates");

            migrationBuilder.DropColumn(
                name: "WorkAuthorization",
                table: "PT_Candidates");

            migrationBuilder.DropColumn(
                name: "YearsOfExperience",
                table: "PT_Candidates");

            migrationBuilder.InsertData(
                table: "SY_WizardSteps",
                columns: new[] { "Id", "CreatedAt", "CreatedBy", "DeletedAt", "DeletedBy", "Description", "IsDeleted", "IsRequired", "Order", "Phase", "StepName", "StepNumber", "StepTitle", "UpdatedAt", "UpdatedBy" },
                values: new object[,]
                {
                    { new Guid("12dccc0e-4f0d-452c-bbd3-3c10d041646a"), new DateTime(2026, 8, 12, 3, 5, 30, 532, DateTimeKind.Utc).AddTicks(976), null, null, null, "Where are you located?", false, true, 2, 1, "Location", 2, "Location", null, null },
                    { new Guid("6ed148be-adbb-4d53-a652-3f220a79ebc6"), new DateTime(2026, 8, 12, 3, 5, 30, 532, DateTimeKind.Utc).AddTicks(990), null, null, null, "Your professional information", false, true, 3, 1, "ProfessionalProfile", 3, "Professional Profile", null, null },
                    { new Guid("73b9d550-e578-484d-a666-7517c55d33a1"), new DateTime(2026, 8, 12, 3, 5, 30, 532, DateTimeKind.Utc).AddTicks(967), null, null, null, "Tell us about yourself", false, true, 1, 1, "PersonalData", 1, "Personal Data", null, null },
                    { new Guid("86419f0a-c00d-4404-85b1-59f0d68b4673"), new DateTime(2026, 8, 12, 3, 5, 30, 532, DateTimeKind.Utc).AddTicks(994), null, null, null, "Choose your preference", false, true, 5, 1, "Preferences", 5, "What do you want to do?", null, null },
                    { new Guid("e1b54569-71d6-430d-86e9-8f151f377777"), new DateTime(2026, 8, 12, 3, 5, 30, 532, DateTimeKind.Utc).AddTicks(996), null, null, null, "Verify your data is correct", false, true, 6, 1, "Confirmation", 6, "Review and Confirm", null, null },
                    { new Guid("fb535ba9-4da7-44d3-b861-31b84cc7cbf2"), new DateTime(2026, 8, 12, 3, 5, 30, 532, DateTimeKind.Utc).AddTicks(992), null, null, null, "Select your skills", false, false, 4, 1, "Skills", 4, "Skills", null, null }
                });
        }
    }
}
