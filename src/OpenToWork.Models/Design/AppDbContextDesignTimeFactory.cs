using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Design;
using OpenToWork.Models.Context;
using Pomelo.EntityFrameworkCore.MySql.Infrastructure;

namespace OpenToWork.Models.Design;

public class AppDbContextDesignTimeFactory : IDesignTimeDbContextFactory<AppDbContext>
{
    public AppDbContext CreateDbContext(string[] args)
    {
        var connectionString = "Server=localhost;Port=3306;Database=OpenToWorkDb;User=root;Password=;CharSet=utf8mb4;";

        var options = new DbContextOptionsBuilder<AppDbContext>()
            .UseMySql(connectionString, ServerVersion.Create(8, 0, 36, ServerType.MySql))
            .Options;

        return new AppDbContext(options);
    }
}
