using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

#pragma warning disable CA1814 // Prefer jagged arrays over multidimensional

namespace OpenToWork.Models.Migrations
{
    /// <inheritdoc />
    public partial class Phase2Security : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
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

            migrationBuilder.AddColumn<DateTime>(
                name: "PasswordResetExpiresAt",
                table: "SC_Users",
                type: "datetime(6)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "PasswordResetToken",
                table: "SC_Users",
                type: "varchar(256)",
                maxLength: 256,
                nullable: true)
                .Annotation("MySql:CharSet", "utf8mb4");

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

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
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

            migrationBuilder.DropColumn(
                name: "PasswordResetExpiresAt",
                table: "SC_Users");

            migrationBuilder.DropColumn(
                name: "PasswordResetToken",
                table: "SC_Users");

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
        }
    }
}
