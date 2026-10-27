# Importing real facility data

Real data is loaded with one command. It checks every row first and writes nothing if any row is invalid.

## Before you import

1. **Check the licence** of the source and record it in [data-sources.md](data-sources.md): the source name, the licence or terms, who approved it, the date, and the status. See [decision record 0002](decisions/0002-data-model-and-data-policy.md).
2. Make sure the data contains **no personal or patient information**.
3. Prepare a CSV file using the columns below. [facilities-template.csv](facilities-template.csv) has the header row.

## CSV columns

| Column | Required | Notes |
|--------|----------|-------|
| `name` | yes | Up to 255 characters |
| `type` | yes | `hospital`, `health_centre`, `dispensary`, or `clinic` |
| `county` | yes | One of the 47 counties, spelled as in `config/afyalink.php` |
| `sub_county` | no | |
| `address` | no | |
| `phone` | no | |
| `latitude`, `longitude` | no | Both or neither. Must be inside Kenya (latitude -5 to 5.5, longitude 33.5 to 42) |
| `services` | no | Names separated by semicolons, for example `Pharmacy;Dental care`. Each must already exist in the services list |
| `verified_at` | no | A date such as `2026-10-01`. Leave empty if the facility has not been verified, and the site will say so |

A facility is identified by its **name and county**. Importing a row that already exists updates it instead of creating a duplicate.

## Steps

```bash
# 1. Check the file without changing anything
php artisan facilities:import data.csv --source="Name of the source" --dry-run

# 2. Import for real, once the licence is recorded
php artisan facilities:import data.csv --source="Name of the source" --confirm-licence

# 3. Optional: replace the demo data in the same step
php artisan facilities:import data.csv --source="Name of the source" --confirm-licence --remove-demo-data
```

- The source name `synthetic` is reserved for demo data and is refused.
- Each row's source is stored with the facility. Keep the name consistent with `data-sources.md`.
- With `--remove-demo-data`, the demo facilities (and their reports) are deleted only after the import succeeds.
- The "Demo data" notice disappears on its own once no demo facilities are left.
