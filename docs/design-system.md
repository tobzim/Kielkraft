# Marvento - Designsprache

Abgeleitet aus der Welt des Außenborders: Instrumenten-/Gauge-Cluster,
Throttle/Pinne, Transom-Montage, Wasseroberfläche & Kielwelle, gebürstetes
Aluminium, Spezifikationen in PS/kW/kg/mm – und die Dualität **Verbrenner ↔
Elektro** (mechanisch/warm/Metall vs. sauber/kühl/Strom).

## 1) Token-System

### Farbe (6 benannte Werte + Ableitungen)
| Token | Hex | Rolle |
|-------|-----|-------|
| Tiefsee | `#0E2A33` | tiefes, entsättigtes Petrol-Ink – dunkle Flächen, Überschriften (NICHT Schwarz) |
| Stahl | `#5B6B70` | Sekundärtext, Linien, Struktur |
| Gischt | `#EEF1F2` | kühles Foam-Weiß – helle Basis (bewusst kühl, NICHT cremig) |
| Aluminium | `#C9D2D4` | Konsolen-/Panel-Flächen |
| Brass | `#C2772E` | **warmer Pol** = Tohatsu/Verbrenner + sparsame Highlights |
| Strom | `#0E8C86` | **kühler Pol** = ePropulsion **und** primäre Aktionsfarbe/CTA |

Zwei **bedeutungstragende** Farben (warm/kühl = Antriebsart) statt eines
dekorativen Akzents.

### Typografie (drei Faces, kein Inter/Roboto/Arial/Space Grotesk)
- **Display:** *Bricolage Grotesque* (600/700/800) – charaktervoller,
  leicht industrieller Grotesk mit Persönlichkeit.
- **Body/UI:** *IBM Plex Sans* (400–700) – Engineering-DNA, passt zu Motoren.
- **Daten/Specs/Preise:** *IBM Plex Mono* (400–700), **tabular-nums** – PS · kW ·
  kg · Schaft mm · € als Instrumenten-Readout. Alle selbst-gehostet (woff2,
  latin-subset, kein Google-Fonts-Tracking).

### Layout-Konzept
Editorial/asymmetrisch statt Karten-Raster. Echte Spec-/Vergleichstabellen, die
wie Instrumentendaten aussehen. Großzügiger Weißraum. Motion nur subjekt-dienlich
(eine orchestrierte Hero-Reveal, eine Mini-Konsole beim Scrollen).

```
PDP-Wireframe (Desktop)
+--------------------------------------------------------------+
| HERO: reales Produkt links  |  Modellname/Wert-Zeile rechts   |
+-----------------------------+--------------------------------+
| Galerie (real)              |  ## DIE KONSOLE (sticky)        |
|   thumbs                    |  Preis (mono, groß)            |
|                             |  zzgl. Versand X €             |
| Beschreibung (editorial)    |  [Schaft] [Farbe] [Steuerung]  |
|                             |  Verfügbar · Versand in X Tg.  |
| GAUGE-READOUT               |  ( In den Warenkorb )          |
|  PS | kW | kg | Schaft mm   |  WhatsApp beraten              |
|                             |  Wallet-Reihe · Micro-Trust    |
| Vergleich Verbrenner|Elektro|  Rechnungskauf-Zeile           |
| Cross-Sell (Propeller…)     |                                |
+-----------------------------+--------------------------------+
```

### Signature-Element – „Die Konsole"
Die Kaufeinheit auf der PDP ist als **Präzisions-Instrumentenkonsole**
gestaltet (Aluminium-Panel, feine Hairlines NUR hier, Mono-Readouts).
Varianten konfigurieren = ein Instrument einstellen. Eine reduzierte
**Mini-Konsole** begleitet beim Scrollen. Hier steckt der gesamte „Mut";
alles drumherum bleibt ruhig und diszipliniert.

## 2) Kritik gegen die drei KI-Klischees

**Klischee #1 – Creme + Serif + Terrakotta:** vermieden. Keine cremige Basis
(Gischt `#EEF1F2` ist bewusst **kühl**), kein Serif (Bricolage + Plex),
Brass ist ein **bedeutungstragender** warmer Pol (Verbrenner), kein
dekoratives Terrakotta.

**Klischee #2 – Fast-Schwarz + 1 greller Acid-Akzent:** vermieden. Tiefsee
`#0E2A33` ist entsättigtes Petrol-Ink, **nicht** Fast-Schwarz. Es gibt **zwei**
bedeutungstragende Farben (Brass warm / Strom kühl) plus Aluminium-Panels –
kein „ein Neon auf Schwarz". Strom `#0E8C86` ist ein tiefes Marine-Teal,
**kein** Acid-Neon (geprüft: hoher Kontrast, aber entsättigt).

**Klischee #3 – Broadsheet-Haarlinien, Radius 0, dichte Spalten:** vermieden.
Großzügiger Weißraum statt dichter Spalten, weiche Radien an Konsole/Panels
(nicht 0), editorial-asymmetrisch. **Eine** disziplinierte Anleihe: feine
Instrumenten-Hairlines **ausschließlich innerhalb der Konsole** (dort
subjekt-gerechtfertigt = Gauge-Cluster), nicht seitenweit.

## 3) Conversion-Prinzipien (Kurzfassung)
- Hero = These (reales Bild + versetzter Block, EIN Primär-CTA), kein
  zentrierter Hero mit zwei gleichwertigen Buttons.
- Sticky-Konsole als Conversion-Motor; Social Proof & Micro-Trust in CTA-Nähe.
- Ehrliche Dringlichkeit (reale Lager/Versand-Cutoffs), keine Fake-Countdowns.
- Copy aus Nutzersicht, aktiv, Satz-Case; Button-Wort = Bestätigungs-Wort.

## 4) Qualitäts-Boden
Responsiv bis Mobile, sichtbarer Tastatur-Fokus, `prefers-reduced-motion`
respektiert, Kontrast WCAG AA, Core Web Vitals grün.
