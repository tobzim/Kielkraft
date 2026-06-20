# Kielkraft

Zweisprachiger (DE/EN) Premium-Webshop für Außenbordmotoren der Marken
**Tohatsu** (Benzin/4-Takt) und **ePropulsion** (Elektro).
Geschäftsmodell: **Dropshipping** – wir sind Verkäufer und Rechnungssteller,
die physische Auslieferung übernehmen Partner.

> **Markenname „Kielkraft"** ist eine Empfehlung/Platzhalter. Domains, Logo und
> finale Rechtstexte sind noch zu klären (siehe „Offene Punkte").

---

## Tech-Stack & Architekturentscheidungen

| Bereich        | Wahl | Begründung |
|----------------|------|-----------|
| CMS            | **Kirby K5** (Flat-File) | schnell, SEO/Performance-stark, kein DB-Server nötig |
| Layout         | **Public-Folder-Setup** | nur `public/` ist Web-Root → `content/`, `site/`, `kirby/`, `storage/` sind systembedingt nicht über HTTP erreichbar (sicherer als nginx-Deny-Regeln) |
| Shop-Modul     | **Kart (bnomei)** primär, **Merx** als Fallback | Panel-Bestellverwaltung + Rechnungs-/E-Mail-Hooks; gekapselt hinter einer Payment/Order-Abstraktion (Phase 5) |
| Zahlung        | **Stripe** Payment Element | Card, PayPal, Klarna, Apple/Google Pay, SEPA über eine Schicht |
| Rechnung       | **lexoffice/sevDesk API** (Fallback: in-house PDF) | rechtssichere fortlaufende Nummern, GoBD, DATEV |
| E-Mail         | dedizierter **ESP** (Postmark/Brevo/Mailgun) mit SPF/DKIM/DMARC; lokal **Mailpit** | nie PHP `mail()` |
| Frontend       | Vanilla CSS/JS, Progressive Enhancement | Core Web Vitals, kein schweres SPA |
| Container      | **Docker / Compose** (PHP 8.2-FPM + nginx) | identisch lokal/Prod |
| Hosting        | **Hetzner** + GitHub Actions (GHCR → SSH-Deploy) | |
| Repo           | privat **`tobzim/kielkraft-shop`** | persönlicher Account (separate Marke, nicht die Org) |

**Kostenpflichtige Lizenzen** (siehe „Lizenzen"): Kirby (Produktivbetrieb),
Kart **oder** Merx. Lokal/Entwicklung läuft Kirby im Trial-Modus ohne Lizenz.

---

## Schnellstart (lokal)

Voraussetzung: Docker Desktop (läuft) + Git.

```bash
cp .env.example .env          # Werte nach Bedarf anpassen
docker compose up -d --build  # oder: make up
```

| Dienst            | URL |
|-------------------|-----|
| Shop              | http://localhost:8080 |
| Panel             | http://localhost:8080/panel |
| Mailpit (E-Mails) | http://localhost:8025 |

Ersten Panel-Admin anlegen (nur in dev, `APP_ENV=development`):

```bash
docker compose exec app php kirby make:user      # oder: make seed-user
```

Weitere Befehle: `make help` (up/down/build/shell/logs/lint).

### Lokal ohne Docker (optional)
Es gibt eine portable PHP-8.3-Toolchain unter `C:\Users\tzimme\tools\php83`.
Kirby-Built-in-Server (Hinweis: Windows = single-threaded, nur für schnelle
Einzel-Checks geeignet, nicht für volle Browser-Sessions):

```bash
php83\php.exe -S 127.0.0.1:8095 -t public kirby/router.php
```

---

## Projektstruktur

```
kielkraft-shop/
├─ public/                # WEB ROOT (einziger über HTTP erreichbarer Ordner)
│  ├─ index.php           # Kirby-Bootstrap (custom roots)
│  ├─ assets/             # css/ js/ img/ fonts/ (versioniert)
│  └─ media/              # generierte Thumbnails  (NICHT im Repo, Volume)
├─ content/               # Flat-File-Inhalte (Katalog versioniert; orders/ NICHT)
├─ site/
│  ├─ blueprints/         # Panel-Felder (Produkt/Kategorie/Order/Kunde/...)
│  ├─ templates/          # PHP-Templates
│  ├─ snippets/           # Header/Footer/Produktkarte/SEO
│  ├─ languages/          # de.php (default) + en.php
│  └─ config/config.php   # env-getriebene Konfiguration
├─ storage/               # accounts/ sessions/ cache/ invoices/ logs/  (Volume)
├─ kirby/  vendor/        # via Composer (NICHT im Repo)
├─ docker/                # nginx- + php-Konfiguration
├─ Dockerfile             # multi-stage: target "runtime" (php-fpm) + "web" (nginx)
├─ docker-compose.yml     # DEV (bind-mount, mailpit)
├─ docker-compose.prod.yml# PROD (GHCR-Images, named volumes)
└─ .github/workflows/     # ci.yml + deploy.yml
```

---

## Persistenz, Volumes & Backups (WICHTIG – Flat-File!)

Kirby speichert **Inhalte, Bestellungen, Rechnungen und Kundenkonten auf der
Platte**. Diese Pfade dürfen NICHT ins Image „eingebacken" und bei Deploys
ersetzt werden. In Produktion liegen sie in **named volumes** (siehe
`docker-compose.prod.yml`) und werden bei `pull && up -d` NICHT angefasst:

| Volume    | Pfad im Container                | Inhalt |
|-----------|----------------------------------|--------|
| `content` | `/var/www/html/content`          | Produkte/Seiten **und** `orders/`, `customers/` (DSGVO!) |
| `media`   | `/var/www/html/public/media`     | generierte Thumbnails + Uploads |
| `storage` | `/var/www/html/storage`          | sessions, cache, accounts, **invoices**, logs |

**Erst-Seeding:** Beim ersten Start initialisiert Docker ein leeres named
volume automatisch aus dem Image-Inhalt (inkl. Eigentümer `www-data`) – der
Start-Katalog landet so im `content`-Volume. Spätere Deploys überschreiben den
Katalog NICHT (Pflege danach über das Panel).

**Backup-Job (Server, einzurichten):** täglich `content`, `media`, `storage`
sichern, z. B. `docker run --rm -v kielkraft_content:/c -v $PWD:/b alpine tar czf /b/content-$(date +%F).tgz -C /c .` (analog für media/storage), Rotation + Offsite-Kopie.

---

## Environment (.env)

Alle Keys in `.env.example`. Secrets gehören **nie** ins Repo oder Image –
lokal in `.env`, in Produktion als Datei auf dem Server bzw. GitHub-Actions-Secrets.

Wichtigste Gruppen: App (`APP_URL`, `KIRBY_DEBUG`, `KIRBY_CACHE`), Stripe,
Invoicing (`INVOICE_PROVIDER` = lexoffice|sevdesk|fallback), Mail/ESP,
Partner-Routing, `APP_SECRET`, Matomo.

---

## Deploy (GHCR → Hetzner)

`.github/workflows/deploy.yml` (Push auf `main`):

1. Baut **zwei** Images (`target: runtime` → `:app-latest`, `target: web` →
   `:web-latest`) und pusht nach **`ghcr.io/tobzim/kielkraft-shop`**.
2. SSH auf Hetzner → `docker compose -f docker-compose.prod.yml pull && up -d`.
   Daten-Volumes bleiben unberührt.

`ci.yml` (Push/PR): `composer validate`, `php -l`, Image-Build-Smoke-Test.

Benötigte **Actions-Secrets** (von Tobias zu setzen, Environment `production`):
`HETZNER_SSH_HOST`, `HETZNER_SSH_USER`, `HETZNER_SSH_KEY`, `SERVICE_PATH`
(Pfad zum Compose-Verzeichnis auf dem Server). `GITHUB_TOKEN` ist automatisch.

Apple/Google Pay benötigen **Domain-Verifizierung** im Stripe-Dashboard.

---

## Lizenzen

- **Kirby** ist für den Produktivbetrieb kostenpflichtig (pro Domain). Lokal
  läuft es lizenzfrei im Trial-Modus (Panel-Hinweis). Lizenz vor Go-Live kaufen.
- **Kart (bnomei)** bzw. **Merx (wagnerwagner)** sind kommerzielle Plugins.
  Erst nach Kauf installierbar (per Composer/Private-Repo). Bis dahin sind
  Warenkorb/Checkout als Abstraktion + Stub vorbereitet.
- Diese Lizenzen sind NICHT im Repo enthalten.

---

## Build-Status / Roadmap

Bearbeitung entlang der 12 Phasen (siehe Task-Board / Auftrag Kap. 22):

- [x] **Phase 1 – Infrastruktur & Scaffold:** Docker (php-fpm+nginx+mailpit),
  Kirby K5, DE/EN, Designsystem (maritim-premium), Header/Footer/Home,
  Blueprints-Start, CI/CD-Gerüst. Startseite rendert zweisprachig.
- [ ] Phase 2 – Datenmodell & Katalog (Blueprints, 8 Seed-Produkte, Filter)
- [ ] Phase 3 – Designsystem-Ausbau & Komponenten
- [ ] Phase 4 – PDP & Listing (Galerie, Varianten, JSON-LD)
- [ ] Phase 5 – Shop-Modul & Checkout (Kart/Merx, Stripe, Webhooks, Fracht)
- [ ] Phase 6 – Konten & Rechnungskauf (`invoice_eligible`, Kreditlimit, Mahnwesen)
- [ ] Phase 7 – Rechnung & Transaktions-Mails (lexoffice/sevDesk, ESP)
- [ ] Phase 8 – Dropshipping (Partner-Routing, Status, Tracking, Marge)
- [ ] Phase 9 – SEO & GEO (Sitemap, Schema, llms.txt, Kaufberater, Ratgeber)
- [ ] Phase 10 – Trust & Recht (Consent, Rechtsseiten, Compliance-Hinweise)
- [ ] Phase 11 – Performance/Security/Deploy (CSP/HSTS, 2FA, Backups, Pipeline scharf)
- [ ] Phase 12 – QA & Repo-Push

---

## Compliance-Hinweise (Pflicht, nicht optional)

- **Rechtstexte** (Impressum, AGB, Datenschutz, Widerruf + Muster) werden als
  **markierte Platzhalter** angelegt → durch **anwaltlich geprüfte Endfassung**
  ersetzen. Nichts erfinden.
- **ElektroG (WEEE):** Registrierung bei der **Stiftung EAR** für die
  ePropulsion-Elektromotoren; Rücknahme + Kennzeichnung.
- **Batterierecht (BattG / EU-Batterieverordnung):** Registrierung +
  Rücknahme-/Entsorgungshinweise für Li-Ion-Akkus.
- **Gefahrgut Li-Ion (UN3480/3481, ADR):** Produkt-Flag „Gefahrgut" steuert
  Versandlogik/Hinweise; Partner muss gefahrgutkonform versenden.
- **„Führerscheinfrei bis 15 PS"** nur mit korrekten Bedingungen formulieren.
- **BFSG / WCAG 2.2 AA:** Barrierefreiheitserklärung + zugängliches Frontend.
- **Gewährleistung/Widerruf/Retoure liegen bei uns** (Verkäufer), nicht beim
  Dropshipping-Partner → RMA-Prozess vorsehen.

---

## DSGVO – Auftragsverarbeiter (AV-Verträge nötig)

Stripe, ESP (Postmark/Brevo/Mailgun), lexoffice/sevDesk, Matomo (self-hosted →
kein AV nötig, sonst AV) sowie Hosting (Hetzner). AV-Verträge abschließen und
in der Datenschutzerklärung listen.

---

## Offene Punkte (manuell durch Tobias)

1. **Domains** `kielkraft.de/.com/.eu` prüfen/registrieren; E-Mail-Postfächer.
   Falls belegt: Alternativen „Kielkraft" / „Voltmare".
2. **GitHub:** `gh auth login` als `tobzim` (Scopes `repo`, `workflow`) ist
   vorhanden; Repo-Erstellung + Push erfolgt nach deinem OK (Phase 12).
3. **Actions-Secrets** setzen (siehe „Deploy").
4. **Lizenzen kaufen:** Kirby (Prod), Kart **oder** Merx.
5. **Konten/Keys:** Stripe (Test+Live), lexoffice/sevDesk-API, ESP-Zugang
   inkl. SPF/DKIM/DMARC im DNS.
6. **Hetzner:** Server mit Docker+Compose, DNS auf Server, Daten-Volumes +
   Backup-Job anlegen, hinter zentralem Reverse-Proxy einbinden.
7. **Recht:** alle Rechtstexte anwaltlich finalisieren; EAR-/BattG-Registrierung.
8. **Produktdaten:** finale Bilder + technische Datenblätter von
   Tohatsu/ePropulsion (Seed-Texte/Preise sind im Repo, Bilder folgen).
9. **Logo/Branding:** Platzhalter-Wortmarke durch finales Logo ersetzen.
10. **Frontend-Design-Skill:** im aktuellen Setup nicht installiert – Design
    folgt der maritim-premium-Richtung + hinterlegtem Stil-Profil.
