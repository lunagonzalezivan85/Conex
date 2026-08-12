using Microsoft.EntityFrameworkCore;
using OpenToWork.Core.Interfaces;
using OpenToWork.Models.Context;
using OpenToWork.Models.Entities;
using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Services;

public class ProfileService : IProfileService
{
    private readonly AppDbContext _context;

    public ProfileService(AppDbContext context)
    {
        _context = context;
    }

    public async Task<CandidateProfileDto?> GetProfileAsync(Guid userId)
    {
        var candidate = await _context.PT_Candidates
            .Include(c => c.Experiences)
            .Include(c => c.Educations)
            .Include(c => c.Certifications)
            .FirstOrDefaultAsync(c => c.SCUserId == userId && !c.IsDeleted);

        if (candidate == null) return null;

        return MapToProfileDto(candidate);
    }

    public async Task<CandidateProfileDto?> UpdateProfileAsync(Guid userId, UpdateCandidateProfileDto dto)
    {
        var candidate = await _context.PT_Candidates
            .FirstOrDefaultAsync(c => c.SCUserId == userId && !c.IsDeleted);

        if (candidate == null) return null;

        if (dto.Title != null) candidate.Title = dto.Title;
        if (dto.Summary != null) candidate.Summary = dto.Summary;
        if (dto.YearsOfExperience.HasValue) candidate.YearsOfExperience = dto.YearsOfExperience;
        if (dto.LinkedInUrl != null) candidate.LinkedInUrl = dto.LinkedInUrl;
        if (dto.PortfolioUrl != null) candidate.PortfolioUrl = dto.PortfolioUrl;
        if (dto.Availability.HasValue) candidate.Availability = dto.Availability;
        if (dto.WorkAuthorization.HasValue) candidate.WorkAuthorization = dto.WorkAuthorization;
        if (dto.IsProfilePublic.HasValue) candidate.IsProfilePublic = dto.IsProfilePublic.Value;
        if (dto.CvUrl != null) candidate.CvUrl = dto.CvUrl;
        if (dto.ProfilePictureUrl != null) candidate.ProfilePictureUrl = dto.ProfilePictureUrl;
        candidate.UpdatedAt = DateTime.UtcNow;
        candidate.UpdatedBy = userId;

        await _context.SaveChangesAsync();

        var updated = await _context.PT_Candidates
            .Include(c => c.Experiences)
            .Include(c => c.Educations)
            .Include(c => c.Certifications)
            .FirstOrDefaultAsync(c => c.Id == candidate.Id);

        return updated != null ? MapToProfileDto(updated) : null;
    }

    public async Task<CandidateExperienceDto> AddExperienceAsync(Guid userId, CreateExperienceDto dto)
    {
        var candidate = await _context.PT_Candidates
            .FirstOrDefaultAsync(c => c.SCUserId == userId && !c.IsDeleted);

        if (candidate == null) throw new InvalidOperationException("Candidate not found");

        var experience = new PTCandidateExperience
        {
            PT_CandidateId = candidate.Id,
            CompanyName = dto.CompanyName,
            JobTitle = dto.JobTitle,
            Description = dto.Description,
            StartDate = dto.StartDate,
            EndDate = dto.EndDate,
            IsCurrentJob = dto.IsCurrentJob,
            Location = dto.Location,
            CreatedBy = userId
        };

        _context.PT_CandidateExperiences.Add(experience);
        await _context.SaveChangesAsync();
        return MapToExperienceDto(experience);
    }

    public async Task<CandidateExperienceDto?> UpdateExperienceAsync(Guid experienceId, UpdateExperienceDto dto, Guid userId)
    {
        var experience = await _context.PT_CandidateExperiences
            .FirstOrDefaultAsync(e => e.Id == experienceId && !e.IsDeleted);

        if (experience == null) return null;

        if (dto.CompanyName != null) experience.CompanyName = dto.CompanyName;
        if (dto.JobTitle != null) experience.JobTitle = dto.JobTitle;
        if (dto.Description != null) experience.Description = dto.Description;
        if (dto.StartDate.HasValue) experience.StartDate = dto.StartDate.Value;
        if (dto.EndDate.HasValue) experience.EndDate = dto.EndDate;
        if (dto.IsCurrentJob.HasValue) experience.IsCurrentJob = dto.IsCurrentJob.Value;
        if (dto.Location != null) experience.Location = dto.Location;
        experience.UpdatedAt = DateTime.UtcNow;
        experience.UpdatedBy = userId;

        await _context.SaveChangesAsync();
        return MapToExperienceDto(experience);
    }

    public async Task<bool> DeleteExperienceAsync(Guid experienceId, Guid userId)
    {
        var experience = await _context.PT_CandidateExperiences
            .FirstOrDefaultAsync(e => e.Id == experienceId && !e.IsDeleted);

        if (experience == null) return false;

        experience.IsDeleted = true;
        experience.DeletedAt = DateTime.UtcNow;
        experience.DeletedBy = userId;
        await _context.SaveChangesAsync();
        return true;
    }

    public async Task<CandidateEducationDto> AddEducationAsync(Guid userId, CreateEducationDto dto)
    {
        var candidate = await _context.PT_Candidates
            .FirstOrDefaultAsync(c => c.SCUserId == userId && !c.IsDeleted);

        if (candidate == null) throw new InvalidOperationException("Candidate not found");

        var education = new PTCandidateEducation
        {
            PT_CandidateId = candidate.Id,
            Institution = dto.Institution,
            Degree = dto.Degree,
            FieldOfStudy = dto.FieldOfStudy,
            StartDate = dto.StartDate,
            EndDate = dto.EndDate,
            IsInProgress = dto.IsInProgress,
            CreatedBy = userId
        };

        _context.PT_CandidateEducations.Add(education);
        await _context.SaveChangesAsync();
        return MapToEducationDto(education);
    }

    public async Task<CandidateEducationDto?> UpdateEducationAsync(Guid educationId, UpdateEducationDto dto, Guid userId)
    {
        var education = await _context.PT_CandidateEducations
            .FirstOrDefaultAsync(e => e.Id == educationId && !e.IsDeleted);

        if (education == null) return null;

        if (dto.Institution != null) education.Institution = dto.Institution;
        if (dto.Degree != null) education.Degree = dto.Degree;
        if (dto.FieldOfStudy != null) education.FieldOfStudy = dto.FieldOfStudy;
        if (dto.StartDate.HasValue) education.StartDate = dto.StartDate;
        if (dto.EndDate.HasValue) education.EndDate = dto.EndDate;
        if (dto.IsInProgress.HasValue) education.IsInProgress = dto.IsInProgress.Value;
        education.UpdatedAt = DateTime.UtcNow;
        education.UpdatedBy = userId;

        await _context.SaveChangesAsync();
        return MapToEducationDto(education);
    }

    public async Task<bool> DeleteEducationAsync(Guid educationId, Guid userId)
    {
        var education = await _context.PT_CandidateEducations
            .FirstOrDefaultAsync(e => e.Id == educationId && !e.IsDeleted);

        if (education == null) return false;

        education.IsDeleted = true;
        education.DeletedAt = DateTime.UtcNow;
        education.DeletedBy = userId;
        await _context.SaveChangesAsync();
        return true;
    }

    public async Task<CandidateCertificationDto> AddCertificationAsync(Guid userId, CreateCertificationDto dto)
    {
        var candidate = await _context.PT_Candidates
            .FirstOrDefaultAsync(c => c.SCUserId == userId && !c.IsDeleted);

        if (candidate == null) throw new InvalidOperationException("Candidate not found");

        var certification = new PTCandidateCertification
        {
            PT_CandidateId = candidate.Id,
            Name = dto.Name,
            Issuer = dto.Issuer,
            IssueDate = dto.IssueDate,
            ExpiryDate = dto.ExpiryDate,
            CredentialId = dto.CredentialId,
            CredentialUrl = dto.CredentialUrl,
            CreatedBy = userId
        };

        _context.PT_CandidateCertifications.Add(certification);
        await _context.SaveChangesAsync();
        return MapToCertificationDto(certification);
    }

    public async Task<CandidateCertificationDto?> UpdateCertificationAsync(Guid certificationId, UpdateCertificationDto dto, Guid userId)
    {
        var certification = await _context.PT_CandidateCertifications
            .FirstOrDefaultAsync(c => c.Id == certificationId && !c.IsDeleted);

        if (certification == null) return null;

        if (dto.Name != null) certification.Name = dto.Name;
        if (dto.Issuer != null) certification.Issuer = dto.Issuer;
        if (dto.IssueDate.HasValue) certification.IssueDate = dto.IssueDate;
        if (dto.ExpiryDate.HasValue) certification.ExpiryDate = dto.ExpiryDate;
        if (dto.CredentialId != null) certification.CredentialId = dto.CredentialId;
        if (dto.CredentialUrl != null) certification.CredentialUrl = dto.CredentialUrl;
        certification.UpdatedAt = DateTime.UtcNow;
        certification.UpdatedBy = userId;

        await _context.SaveChangesAsync();
        return MapToCertificationDto(certification);
    }

    public async Task<bool> DeleteCertificationAsync(Guid certificationId, Guid userId)
    {
        var certification = await _context.PT_CandidateCertifications
            .FirstOrDefaultAsync(c => c.Id == certificationId && !c.IsDeleted);

        if (certification == null) return false;

        certification.IsDeleted = true;
        certification.DeletedAt = DateTime.UtcNow;
        certification.DeletedBy = userId;
        await _context.SaveChangesAsync();
        return true;
    }

    private static CandidateProfileDto MapToProfileDto(PTCandidate c) => new()
    {
        Id = c.Id,
        UserId = c.SCUserId,
        FirstName = c.FirstName,
        LastName = c.LastName,
        Identification = c.Identification,
        Phone = c.Phone,
        BirthDate = c.BirthDate,
        Gender = c.Gender,
        Title = c.Title,
        Summary = c.Summary,
        CvUrl = c.CvUrl,
        ProfilePictureUrl = c.ProfilePictureUrl,
        Country = c.Country,
        City = c.City,
        Address = c.Address,
        YearsOfExperience = c.YearsOfExperience,
        LinkedInUrl = c.LinkedInUrl,
        PortfolioUrl = c.PortfolioUrl,
        Availability = c.Availability,
        WorkAuthorization = c.WorkAuthorization,
        IsProfilePublic = c.IsProfilePublic,
        Experiences = c.Experiences.Where(e => !e.IsDeleted).Select(MapToExperienceDto).ToList(),
        Educations = c.Educations.Where(e => !e.IsDeleted).Select(MapToEducationDto).ToList(),
        Certifications = c.Certifications.Where(c => !c.IsDeleted).Select(MapToCertificationDto).ToList()
    };

    private static CandidateExperienceDto MapToExperienceDto(PTCandidateExperience e) => new()
    {
        Id = e.Id,
        CandidateId = e.PT_CandidateId,
        CompanyName = e.CompanyName,
        JobTitle = e.JobTitle,
        Description = e.Description,
        StartDate = e.StartDate,
        EndDate = e.EndDate,
        IsCurrentJob = e.IsCurrentJob,
        Location = e.Location
    };

    private static CandidateEducationDto MapToEducationDto(PTCandidateEducation e) => new()
    {
        Id = e.Id,
        CandidateId = e.PT_CandidateId,
        Institution = e.Institution,
        Degree = e.Degree,
        FieldOfStudy = e.FieldOfStudy,
        StartDate = e.StartDate,
        EndDate = e.EndDate,
        IsInProgress = e.IsInProgress
    };

    private static CandidateCertificationDto MapToCertificationDto(PTCandidateCertification c) => new()
    {
        Id = c.Id,
        CandidateId = c.PT_CandidateId,
        Name = c.Name,
        Issuer = c.Issuer,
        IssueDate = c.IssueDate,
        ExpiryDate = c.ExpiryDate,
        CredentialId = c.CredentialId,
        CredentialUrl = c.CredentialUrl
    };
}
