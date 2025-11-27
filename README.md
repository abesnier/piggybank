I know this readme is junk, I'll update it later...

# Family PiggyBank

Lightweight PHP actions and views to manage simple "piggy bank" records stored in SQLite. This repo is a minimal demo for inserting and reading monetary records per user (optionally separate tables per kid).

Project layout (root = `var/www/html or var/www/public`)
```
.
├── README.md
├── db
│   └── piggy.sqlite
├── img
│   ├── favicon.ico
│   └── plus-sign.svg
├── index.php
└── scripts
    ├── 404.php
    ├── create_kid.php
    ├── home.php
    ├── kid
    │   ├── add.php
    │   ├── home.php
    │   ├── kid.php
    │   ├── modify.php
    │   └── read.php
    └── password.php
```

Requirements
- PHP 7.4+ (PHP 8 recommended) with `PDO` and `pdo_sqlite` enabled.

Quick start

 - Clone this repo to a location that can be served by a http server with PHP support
 - Edit scripts/password.php and put your own password (to prevent your kids from adding money on their own piggy bank...)
 - Run the interactive script `scripts/create_kid.php` (e.g. `php ./scripts/create_kid.php`). You will be prompted for a kid's name, and necessary files will be copied, and index.php and home.php will be ammended accrodingly.

Routes / Endpoints
- `/` — home view that renders cards (the router maps `/`, `/alice`, `/victor` to the main home view).
- `/{kid}/home` — `GET` endpoint returning a basic view of your kid's piggybank status
- `/{kid}/read` — `GET` endpoint returning JSON with rows and a `total` value (only rows where `credit = 1`) for the specified kid (e.g. `/victor/read`).
- `/{kid}/modify` — `POST` handler to insert a new record for that kid (returns JSON on success), e.g. `/victor/modify`.
- `/{kid}/add` — form to add money to your kid's piggybank

Database schema
- Location: `db/piggy.sqlite` (relative to project root).
- Kid's table is checked and created if necessary automatically. Default table schema example (per-kid tables):

```sql
CREATE TABLE IF NOT EXISTS {kid} (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	montant REAL NOT NULL DEFAULT 0,
	credit INTEGER NOT NULL DEFAULT 0,
	raison TEXT,
	timestamp INTEGER -- Unix epoch seconds
);
```

JSON formats
- `GET /read` returns:

```json
{
	"success": true,
	"rows": [
		{ "montant": 12.5, "credit": 1, "raison": "allowance", "timestamp": 1700000000 }
	],
	"total": 12.5
}
```

- `POST /modify` expects form fields `montant`, `credit` (checkbox `1`) and `raison`. On success it returns:

```json
{ "success": true, "message": "Inserted successfully", "data": { "montant": 12.5, "credit": 1, "raison": "test", "timestamp": 1700000000 } }
```

Examples (PowerShell)
- Insert a record for `victor`:

```powershell
curl -Method POST -Body @{montant='12.5'; credit='1'; raison='test'} http://your.website/victor/modify
```

- Read records for `victor`:

```powershell
curl http://localhost:8000/victor/read
```

Security notes
- This project is a minimal demo and not production-ready. Important considerations:
	- Validate and sanitize all inputs.
	- Add CSRF protection for forms.
	- Do not expose internal error messages in production.

Next steps
- Move inline CSS into a shared stylesheet.
- Add authentication or per-kid access control if you need privacy.
