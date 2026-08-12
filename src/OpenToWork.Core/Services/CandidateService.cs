using Microsoft.EntityFrameworkCore;
using OpenToWork.Core.Interfaces;
using OpenToWork.Models.Context;
using OpenToWork.Models.Entities;
using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Services;

public class CandidateService : ICandidateService
{
    private readonly AppDbContext _context;

    public CandidateService(AppDbContext context)
    {
        _context = context;
    }

    public async Task<CandidateDto?> GetCandidateByUserIdAsync(Guid userId)
    {
        var candidate = await _context.PT_Candidates
            .FirstOrDefaultAsync(c => c.SCUserId == userId && !c.IsDeleted);

        return candidate == null ? null : MapToDto(candidate);
    }

    public async Task<CandidateDto?> GetCandidateByIdAsync(Guid id)
    {
        var candidate = await _context.PT_Candidates
            .FirstOrDefaultAsync(c => c.Id == id && !c.IsDeleted);

        return candidate == null ? null : MapToDto(candidate);
    }

    public async Task<CandidateDto> CreateCandidateAsync(Guid userId, string createdBy)
    {
        var existing = await _context.PT_Candidates
            .FirstOrDefaultAsync(c => c.SCUserId == userId && !c.IsDeleted);

        if (existing != null) return MapToDto(existing);

        var candidate = new PTCandidate
        {
            SCUserId = userId,
            WizardStep = 0,
            WizardCompleted = false,
            CreatedBy = Guid.Parse(createdBy)
        };

        _context.PT_Candidates.Add(candidate);
        await _context.SaveChangesAsync();
        return MapToDto(candidate);
    }

    public async Task<CandidateDto> UpdateWizardStepAsync(Guid userId, UpdateCandidateWizardDto dto)
    {
        var candidate = await _context.PT_Candidates
            .FirstOrDefaultAsync(c => c.SCUserId == userId && !c.IsDeleted);

        if (candidate == null)
        {
            candidate = new PTCandidate { SCUserId = userId, WizardStep = 0, WizardCompleted = false };
            _context.PT_Candidates.Add(candidate);
        }

        if (dto.FirstName != null) candidate.FirstName = dto.FirstName;
        if (dto.LastName != null) candidate.LastName = dto.LastName;
        if (dto.Identification != null) candidate.Identification = dto.Identification;
        if (dto.Phone != null) candidate.Phone = dto.Phone;
        if (dto.BirthDate.HasValue) candidate.BirthDate = dto.BirthDate;
        if (dto.Gender.HasValue) candidate.Gender = dto.Gender;
        if (dto.Title != null) candidate.Title = dto.Title;
        if (dto.Summary != null) candidate.Summary = dto.Summary;
        if (dto.Country != null) candidate.Country = dto.Country;
        if (dto.City != null) candidate.City = dto.City;
        if (dto.Address != null) candidate.Address = dto.Address;

        candidate.WizardStep = dto.WizardStep;
        candidate.WizardCompleted = dto.WizardCompleted;
        candidate.UpdatedAt = DateTime.UtcNow;

        await _context.SaveChangesAsync();
        return MapToDto(candidate);
    }

    public async Task<bool> IsWizardCompleteAsync(Guid userId)
    {
        return await _context.PT_Candidates
            .AnyAsync(c => c.SCUserId == userId && c.WizardCompleted && !c.IsDeleted);
    }

    private static CandidateDto MapToDto(PTCandidate c) => new()
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
        WizardCompleted = c.WizardCompleted,
        WizardStep = c.WizardStep
    };
}
