using Microsoft.EntityFrameworkCore;
using OpenToWork.Core.Interfaces;
using OpenToWork.Models.Context;
using OpenToWork.Models.Entities;
using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Services;

public class VacancyService : IVacancyService
{
    private readonly AppDbContext _context;

    public VacancyService(AppDbContext context)
    {
        _context = context;
    }

    public async Task<TempVacancyDto> CreateTempVacancyAsync(Guid userId, CreateTempVacancyDto dto)
    {
        var vacancy = new PTTempVacancy
        {
            SCUserId = userId,
            Title = dto.Title,
            Description = dto.Description,
            Requirements = dto.Requirements,
            SalaryMin = dto.SalaryMin,
            SalaryMax = dto.SalaryMax,
            Location = dto.Location,
            ContractType = dto.ContractType,
            ExpiresAt = DateTime.UtcNow.AddDays(dto.ExpirationDays),
            IsPublished = dto.IsPublished,
            CreatedBy = userId
        };

        _context.PT_TempVacancies.Add(vacancy);
        await _context.SaveChangesAsync();
        return MapToDto(vacancy);
    }

    public async Task<IEnumerable<TempVacancyDto>> GetTempVacanciesByUserAsync(Guid userId)
    {
        var vacancies = await _context.PT_TempVacancies
            .Where(v => v.SCUserId == userId && !v.IsDeleted && v.ExpiresAt > DateTime.UtcNow)
            .OrderByDescending(v => v.CreatedAt)
            .ToListAsync();

        return vacancies.Select(MapToDto);
    }

    public async Task<(IEnumerable<TempVacancyDto> Items, int Total)> SearchVacanciesAsync(SearchVacancyDto search)
    {
        var query = _context.PT_TempVacancies
            .Where(v => !v.IsDeleted && v.IsPublished && v.ExpiresAt > DateTime.UtcNow);

        if (!string.IsNullOrEmpty(search.Query))
        {
            var q = search.Query.ToLower();
            query = query.Where(v => v.Title.ToLower().Contains(q) ||
                                     (v.Description != null && v.Description.ToLower().Contains(q)) ||
                                     (v.Requirements != null && v.Requirements.ToLower().Contains(q)));
        }

        if (!string.IsNullOrEmpty(search.Location))
            query = query.Where(v => v.Location != null && v.Location.Contains(search.Location));

        if (search.ContractType.HasValue)
            query = query.Where(v => v.ContractType == search.ContractType.Value);

        if (search.SalaryMin.HasValue)
            query = query.Where(v => v.SalaryMin >= search.SalaryMin.Value);

        var total = await query.CountAsync();

        var items = await query
            .OrderByDescending(v => v.CreatedAt)
            .Skip((search.Page - 1) * search.PageSize)
            .Take(search.PageSize)
            .ToListAsync();

        return (items.Select(MapToDto), total);
    }

    public async Task<bool> DeleteTempVacancyAsync(Guid vacancyId, Guid userId)
    {
        var vacancy = await _context.PT_TempVacancies
            .FirstOrDefaultAsync(v => v.Id == vacancyId && v.SCUserId == userId && !v.IsDeleted);

        if (vacancy == null) return false;

        vacancy.IsDeleted = true;
        vacancy.DeletedAt = DateTime.UtcNow;
        vacancy.DeletedBy = userId;
        await _context.SaveChangesAsync();
        return true;
    }

    private static TempVacancyDto MapToDto(PTTempVacancy v) => new()
    {
        Id = v.Id,
        UserId = v.SCUserId,
        Title = v.Title,
        Description = v.Description,
        Requirements = v.Requirements,
        SalaryMin = v.SalaryMin,
        SalaryMax = v.SalaryMax,
        Location = v.Location,
        ContractType = v.ContractType,
        ExpiresAt = v.ExpiresAt,
        IsPublished = v.IsPublished
    };
}
