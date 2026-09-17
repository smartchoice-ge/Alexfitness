-- Structured package durations for PackagesWebsite.
-- Idempotent: safe to run more than once.
--
-- Columns (snake_case, matching the rest of the table):
--   duration_unit   VARCHAR(10)  -- 'day' | 'week' | 'month' | 'year'
--   duration_value  INT          -- amount, e.g. 2 (weeks), 15 (days), 1 (year)
--   duration_days   INT          -- TOTAL days (day=N, week=N*7, month=N*30, year=N*365)
--   duration_month  INT NULL     -- equivalent months when applicable (month=N, year=N*12), else NULL
--
-- Effective duration = duration_value of duration_unit. The legacy
-- duration_days / duration_month columns are derived on save so any older
-- code paths that still read them keep working.
--
-- SQL Server column names are case-insensitive (duration_unit == DurationUnit).

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('dbo.PackagesWebsite') AND name = 'duration_days')
    ALTER TABLE dbo.PackagesWebsite ADD duration_days INT NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('dbo.PackagesWebsite') AND name = 'duration_value')
    ALTER TABLE dbo.PackagesWebsite ADD duration_value INT NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('dbo.PackagesWebsite') AND name = 'duration_unit')
    ALTER TABLE dbo.PackagesWebsite ADD duration_unit VARCHAR(10) NULL;
GO

-- Day/week packages have no month equivalent, so duration_month must be nullable.
IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('dbo.PackagesWebsite') AND name = 'duration_month')
    ALTER TABLE dbo.PackagesWebsite ALTER COLUMN duration_month INT NULL;
GO

-- Carry over data from an earlier duration_type column, if one exists.
IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('dbo.PackagesWebsite') AND name = 'duration_type')
    EXEC('UPDATE dbo.PackagesWebsite SET duration_unit = duration_type
          WHERE (duration_unit IS NULL OR duration_unit = '''') AND duration_type IS NOT NULL AND duration_type <> ''''');
GO

-- Infer unit/value from legacy columns for rows still lacking them.
-- (duration_month = 50 is a legacy "one-time" sentinel; leave it untyped.)
UPDATE dbo.PackagesWebsite SET duration_unit = 'day', duration_value = duration_days
WHERE (duration_unit IS NULL OR duration_unit = '') AND duration_days IS NOT NULL AND duration_days > 0;
GO

UPDATE dbo.PackagesWebsite SET duration_unit = 'month', duration_value = duration_month
WHERE (duration_unit IS NULL OR duration_unit = '')
  AND duration_month IS NOT NULL AND duration_month > 0 AND duration_month <> 50;
GO

-- Normalize legacy columns to totals for every structured row.
UPDATE dbo.PackagesWebsite SET duration_days = duration_value,       duration_month = NULL           WHERE duration_unit = 'day'   AND duration_value > 0;
UPDATE dbo.PackagesWebsite SET duration_days = duration_value * 7,   duration_month = NULL           WHERE duration_unit = 'week'  AND duration_value > 0;
UPDATE dbo.PackagesWebsite SET duration_days = duration_value * 30,  duration_month = duration_value WHERE duration_unit = 'month' AND duration_value > 0;
UPDATE dbo.PackagesWebsite SET duration_days = duration_value * 365, duration_month = duration_value * 12 WHERE duration_unit = 'year' AND duration_value > 0;
GO
