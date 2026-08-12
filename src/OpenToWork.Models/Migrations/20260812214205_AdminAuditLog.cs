using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

#pragma warning disable CA1814 // Prefer jagged arrays over multidimensional

namespace OpenToWork.Models.Migrations
{
    /// <inheritdoc />
    public partial class AdminAuditLog : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("01729bca-2f02-4456-9255-33096fc4ce7c"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("2a45a283-794a-4ee7-8d5d-529d9a2e1431"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("38db2517-920a-4874-afe3-84f1467355e0"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("68d9758f-2843-4c18-be66-d643feb5f7e7"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("6f24c58a-7c99-4b02-84d6-6e7ba1cc7e11"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("95666fd1-99a9-4dfe-9f7a-b04ca50da151"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("abae0ed8-916e-424d-9106-99c81a13a280"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("d1889f95-9982-4b55-8856-6d866ebeeb78"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("e1afabde-4b4d-40ec-8459-5d2537f37319"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("fa2827be-bb1e-453b-8295-d5daf2fd7616"));

            migrationBuilder.CreateTable(
                name: "AD_AuditLogs",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    SCUserId = table.Column<Guid>(type: "char(36)", nullable: false, collation: "ascii_general_ci"),
                    Action = table.Column<string>(type: "varchar(100)", maxLength: 100, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    EntityType = table.Column<string>(type: "varchar(100)", maxLength: 100, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    EntityId = table.Column<Guid>(type: "char(36)", nullable: true, collation: "ascii_general_ci"),
                    ChangesJson = table.Column<string>(type: "longtext", nullable: true)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    IpAddress = table.Column<string>(type: "varchar(45)", maxLength: 45, nullable: true)
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
                    table.PrimaryKey("PK_AD_AuditLogs", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AD_AuditLogs_SC_Users_SCUserId",
                        column: x => x.SCUserId,
                        principalTable: "SC_Users",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.InsertData(
                table: "SY_WizardSteps",
                columns: new[] { "Id", "CreatedAt", "CreatedBy", "DeletedAt", "DeletedBy", "Description", "IsDeleted", "IsRequired", "Order", "Phase", "StepName", "StepNumber", "StepTitle", "UpdatedAt", "UpdatedBy" },
                values: new object[,]
                {
                    { new Guid("1ecde287-d0b3-48d0-8083-dca24bf5b68f"), new DateTime(2026, 8, 12, 21, 42, 5, 383, DateTimeKind.Utc).AddTicks(5932), null, null, null, "Add your education", false, false, 8, 2, "Education", 8, "Education", null, null },
                    { new Guid("20df1f1a-381e-4e7a-92ec-49ffa4f343c9"), new DateTime(2026, 8, 12, 21, 42, 5, 383, DateTimeKind.Utc).AddTicks(5934), null, null, null, "Add your certifications", false, false, 9, 2, "Certifications", 9, "Certifications", null, null },
                    { new Guid("8042dfc4-c69a-41e6-ab7f-bb3096f578c9"), new DateTime(2026, 8, 12, 21, 42, 5, 383, DateTimeKind.Utc).AddTicks(5917), null, null, null, "Your professional information", false, true, 3, 1, "ProfessionalProfile", 3, "Professional Profile", null, null },
                    { new Guid("909facd3-124c-4f85-8def-5db0d8d7e4a6"), new DateTime(2026, 8, 12, 21, 42, 5, 383, DateTimeKind.Utc).AddTicks(5920), null, null, null, "Select your skills", false, false, 4, 1, "Skills", 4, "Skills", null, null },
                    { new Guid("a46cba54-3d26-49b9-889b-f41e967175a2"), new DateTime(2026, 8, 12, 21, 42, 5, 383, DateTimeKind.Utc).AddTicks(5922), null, null, null, "Choose your preference", false, true, 5, 1, "Preferences", 5, "What do you want to do?", null, null },
                    { new Guid("abfb92bb-6e9e-4c31-be9d-45b464121bbe"), new DateTime(2026, 8, 12, 21, 42, 5, 383, DateTimeKind.Utc).AddTicks(5924), null, null, null, "Verify your data is correct", false, true, 6, 1, "Confirmation", 6, "Review and Confirm", null, null },
                    { new Guid("c64c4bf4-675f-4092-9ea0-a1a7f90aa9f5"), new DateTime(2026, 8, 12, 21, 42, 5, 383, DateTimeKind.Utc).AddTicks(5929), null, null, null, "Add your work experience", false, false, 7, 2, "WorkExperience", 7, "Work Experience", null, null },
                    { new Guid("def9e009-3812-4deb-89e4-9de11393eab1"), new DateTime(2026, 8, 12, 21, 42, 5, 383, DateTimeKind.Utc).AddTicks(5907), null, null, null, "Where are you located?", false, true, 2, 1, "Location", 2, "Location", null, null },
                    { new Guid("ea939395-9f9c-416a-a063-7664b1d34684"), new DateTime(2026, 8, 12, 21, 42, 5, 383, DateTimeKind.Utc).AddTicks(5936), null, null, null, "Upload your CV/resume", false, false, 10, 2, "UploadCV", 10, "Upload CV", null, null },
                    { new Guid("f5585533-9b9b-473a-be10-321bde41306f"), new DateTime(2026, 8, 12, 21, 42, 5, 383, DateTimeKind.Utc).AddTicks(5900), null, null, null, "Tell us about yourself", false, true, 1, 1, "PersonalData", 1, "Personal Data", null, null }
                });

            migrationBuilder.CreateIndex(
                name: "IX_AD_AuditLogs_CreatedAt_IsDeleted",
                table: "AD_AuditLogs",
                columns: new[] { "CreatedAt", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_AD_AuditLogs_EntityType_EntityId_IsDeleted",
                table: "AD_AuditLogs",
                columns: new[] { "EntityType", "EntityId", "IsDeleted" });

            migrationBuilder.CreateIndex(
                name: "IX_AD_AuditLogs_SCUserId_IsDeleted",
                table: "AD_AuditLogs",
                columns: new[] { "SCUserId", "IsDeleted" });
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "AD_AuditLogs");

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("1ecde287-d0b3-48d0-8083-dca24bf5b68f"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("20df1f1a-381e-4e7a-92ec-49ffa4f343c9"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("8042dfc4-c69a-41e6-ab7f-bb3096f578c9"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("909facd3-124c-4f85-8def-5db0d8d7e4a6"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("a46cba54-3d26-49b9-889b-f41e967175a2"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("abfb92bb-6e9e-4c31-be9d-45b464121bbe"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("c64c4bf4-675f-4092-9ea0-a1a7f90aa9f5"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("def9e009-3812-4deb-89e4-9de11393eab1"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("ea939395-9f9c-416a-a063-7664b1d34684"));

            migrationBuilder.DeleteData(
                table: "SY_WizardSteps",
                keyColumn: "Id",
                keyValue: new Guid("f5585533-9b9b-473a-be10-321bde41306f"));

            migrationBuilder.InsertData(
                table: "SY_WizardSteps",
                columns: new[] { "Id", "CreatedAt", "CreatedBy", "DeletedAt", "DeletedBy", "Description", "IsDeleted", "IsRequired", "Order", "Phase", "StepName", "StepNumber", "StepTitle", "UpdatedAt", "UpdatedBy" },
                values: new object[,]
                {
                    { new Guid("01729bca-2f02-4456-9255-33096fc4ce7c"), new DateTime(2026, 8, 12, 15, 48, 0, 827, DateTimeKind.Utc).AddTicks(9595), null, null, null, "Add your education", false, false, 8, 2, "Education", 8, "Education", null, null },
                    { new Guid("2a45a283-794a-4ee7-8d5d-529d9a2e1431"), new DateTime(2026, 8, 12, 15, 48, 0, 827, DateTimeKind.Utc).AddTicks(9600), null, null, null, "Upload your CV/resume", false, false, 10, 2, "UploadCV", 10, "Upload CV", null, null },
                    { new Guid("38db2517-920a-4874-afe3-84f1467355e0"), new DateTime(2026, 8, 12, 15, 48, 0, 827, DateTimeKind.Utc).AddTicks(9566), null, null, null, "Where are you located?", false, true, 2, 1, "Location", 2, "Location", null, null },
                    { new Guid("68d9758f-2843-4c18-be66-d643feb5f7e7"), new DateTime(2026, 8, 12, 15, 48, 0, 827, DateTimeKind.Utc).AddTicks(9559), null, null, null, "Tell us about yourself", false, true, 1, 1, "PersonalData", 1, "Personal Data", null, null },
                    { new Guid("6f24c58a-7c99-4b02-84d6-6e7ba1cc7e11"), new DateTime(2026, 8, 12, 15, 48, 0, 827, DateTimeKind.Utc).AddTicks(9583), null, null, null, "Select your skills", false, false, 4, 1, "Skills", 4, "Skills", null, null },
                    { new Guid("95666fd1-99a9-4dfe-9f7a-b04ca50da151"), new DateTime(2026, 8, 12, 15, 48, 0, 827, DateTimeKind.Utc).AddTicks(9588), null, null, null, "Verify your data is correct", false, true, 6, 1, "Confirmation", 6, "Review and Confirm", null, null },
                    { new Guid("abae0ed8-916e-424d-9106-99c81a13a280"), new DateTime(2026, 8, 12, 15, 48, 0, 827, DateTimeKind.Utc).AddTicks(9593), null, null, null, "Add your work experience", false, false, 7, 2, "WorkExperience", 7, "Work Experience", null, null },
                    { new Guid("d1889f95-9982-4b55-8856-6d866ebeeb78"), new DateTime(2026, 8, 12, 15, 48, 0, 827, DateTimeKind.Utc).AddTicks(9597), null, null, null, "Add your certifications", false, false, 9, 2, "Certifications", 9, "Certifications", null, null },
                    { new Guid("e1afabde-4b4d-40ec-8459-5d2537f37319"), new DateTime(2026, 8, 12, 15, 48, 0, 827, DateTimeKind.Utc).AddTicks(9586), null, null, null, "Choose your preference", false, true, 5, 1, "Preferences", 5, "What do you want to do?", null, null },
                    { new Guid("fa2827be-bb1e-453b-8295-d5daf2fd7616"), new DateTime(2026, 8, 12, 15, 48, 0, 827, DateTimeKind.Utc).AddTicks(9580), null, null, null, "Your professional information", false, true, 3, 1, "ProfessionalProfile", 3, "Professional Profile", null, null }
                });
        }
    }
}
