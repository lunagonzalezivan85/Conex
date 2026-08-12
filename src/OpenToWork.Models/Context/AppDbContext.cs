using Microsoft.EntityFrameworkCore;
using OpenToWork.Models.Entities;

namespace OpenToWork.Models.Context;

public class AppDbContext : DbContext
{
    public AppDbContext(DbContextOptions<AppDbContext> options) : base(options) { }

    public DbSet<SCUser> SC_Users => Set<SCUser>();
    public DbSet<SCUserRole> SC_UserRoles => Set<SCUserRole>();
    public DbSet<SCRefreshToken> SC_RefreshTokens => Set<SCRefreshToken>();
    public DbSet<SCUserDevice> SC_UserDevices => Set<SCUserDevice>();
    public DbSet<PTCandidate> PT_Candidates => Set<PTCandidate>();
    public DbSet<PTCompany> PT_Companies => Set<PTCompany>();
    public DbSet<PTTempVacancy> PT_TempVacancies => Set<PTTempVacancy>();
    public DbSet<PTSkill> PT_Skills => Set<PTSkill>();
    public DbSet<PTCandidateSkill> PT_CandidateSkills => Set<PTCandidateSkill>();
    public DbSet<SYWizardStep> SY_WizardSteps => Set<SYWizardStep>();
    public DbSet<SYUserPreference> SY_UserPreferences => Set<SYUserPreference>();

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);

        modelBuilder.Entity<SCUser>(e =>
        {
            e.ToTable("SC_Users");
            e.HasIndex(u => u.Email).IsUnique().HasFilter("IsDeleted = 0");
            e.HasIndex(u => u.GoogleId).IsUnique().HasFilter("GoogleId IS NOT NULL AND IsDeleted = 0");
            e.HasIndex(u => new { u.IsActive, u.IsDeleted });
            e.Property(u => u.Email).IsRequired().HasMaxLength(256);
            e.Property(u => u.PrimaryRole).HasDefaultValue(0);
            e.Property(u => u.EmailVerified).HasDefaultValue(false);
            e.Property(u => u.IsActive).HasDefaultValue(true);
        });

        modelBuilder.Entity<SCUserRole>(e =>
        {
            e.ToTable("SC_UserRoles");
            e.HasIndex(r => new { r.SCUserId, r.Role, r.IsDeleted }).IsUnique();
            e.HasIndex(r => r.Role);
            e.Property(r => r.AssignedAt).HasDefaultValueSql("CURRENT_TIMESTAMP(6)");
        });

        modelBuilder.Entity<SCRefreshToken>(e =>
        {
            e.ToTable("SC_RefreshTokens");
            e.HasIndex(t => t.TokenHash).IsUnique();
            e.HasIndex(t => new { t.SCUserId, t.IsRevoked, t.IsDeleted });
        });

        modelBuilder.Entity<SCUserDevice>(e =>
        {
            e.ToTable("SC_UserDevices");
            e.HasIndex(d => new { d.SCUserId, d.DeviceHash, d.IsDeleted }).IsUnique();
            e.HasIndex(d => new { d.SCUserId, d.IsTrusted });
        });

        modelBuilder.Entity<PTCandidate>(e =>
        {
            e.ToTable("PT_Candidates");
            e.HasIndex(c => new { c.SCUserId, c.IsDeleted }).IsUnique();
            e.HasIndex(c => c.Identification);
            e.HasIndex(c => new { c.WizardCompleted, c.IsDeleted });
            e.Property(c => c.WizardCompleted).HasDefaultValue(false);
            e.Property(c => c.WizardStep).HasDefaultValue(0);
        });

        modelBuilder.Entity<PTCompany>(e =>
        {
            e.ToTable("PT_Companies");
            e.HasIndex(c => new { c.SCUserId, c.IsDeleted }).IsUnique();
            e.HasIndex(c => new { c.Name, c.IsDeleted });
        });

        modelBuilder.Entity<PTTempVacancy>(e =>
        {
            e.ToTable("PT_TempVacancies");
            e.HasIndex(v => new { v.SCUserId, v.IsDeleted });
            e.HasIndex(v => new { v.ExpiresAt, v.IsDeleted });
            e.HasIndex(v => new { v.IsPublished, v.IsDeleted });
        });

        modelBuilder.Entity<PTSkill>(e =>
        {
            e.ToTable("PT_Skills");
            e.HasIndex(s => new { s.Name, s.IsDeleted }).IsUnique();
            e.HasIndex(s => new { s.Category, s.IsDeleted });
        });

        modelBuilder.Entity<PTCandidateSkill>(e =>
        {
            e.ToTable("PT_CandidateSkills");
            e.HasIndex(cs => new { cs.PT_CandidateId, cs.PT_SkillId, cs.IsDeleted }).IsUnique();
            e.HasIndex(cs => new { cs.PT_SkillId, cs.IsDeleted });
        });

        modelBuilder.Entity<SYWizardStep>(e =>
        {
            e.ToTable("SY_WizardSteps");
            e.HasIndex(w => new { w.StepNumber, w.IsDeleted }).IsUnique();
            e.HasIndex(w => new { w.Order, w.Phase, w.IsDeleted });
        });

        modelBuilder.Entity<SYUserPreference>(e =>
        {
            e.ToTable("SY_UserPreferences");
            e.HasIndex(p => new { p.SCUserId, p.IsDeleted }).IsUnique();
            e.Property(p => p.Theme).HasDefaultValue("navy");
            e.Property(p => p.Language).HasDefaultValue("es");
        });

        SeedWizardSteps(modelBuilder);
    }

    private static void SeedWizardSteps(ModelBuilder modelBuilder)
    {
        var steps = new[]
        {
            new SYWizardStep { Id = Guid.NewGuid(), StepNumber = 1, StepName = "PersonalData", StepTitle = "Personal Data", Description = "Tell us about yourself", IsRequired = true, Order = 1, Phase = 1 },
            new SYWizardStep { Id = Guid.NewGuid(), StepNumber = 2, StepName = "Location", StepTitle = "Location", Description = "Where are you located?", IsRequired = true, Order = 2, Phase = 1 },
            new SYWizardStep { Id = Guid.NewGuid(), StepNumber = 3, StepName = "ProfessionalProfile", StepTitle = "Professional Profile", Description = "Your professional information", IsRequired = true, Order = 3, Phase = 1 },
            new SYWizardStep { Id = Guid.NewGuid(), StepNumber = 4, StepName = "Skills", StepTitle = "Skills", Description = "Select your skills", IsRequired = false, Order = 4, Phase = 1 },
            new SYWizardStep { Id = Guid.NewGuid(), StepNumber = 5, StepName = "Preferences", StepTitle = "What do you want to do?", Description = "Choose your preference", IsRequired = true, Order = 5, Phase = 1 },
            new SYWizardStep { Id = Guid.NewGuid(), StepNumber = 6, StepName = "Confirmation", StepTitle = "Review and Confirm", Description = "Verify your data is correct", IsRequired = true, Order = 6, Phase = 1 }
        };

        modelBuilder.Entity<SYWizardStep>().HasData(steps);
    }
}
