<?php

use Kirby\Http\Remote;

/**
 * Invoice generation for Kielkraft orders.
 *
 *  kk_invoice_create($inv)  -> dispatcher:
 *     - if Lexware Office (lexoffice) is configured (INVOICE_PROVIDER=lexoffice
 *       + LEXOFFICE_API_KEY), the official invoice is issued via the API and
 *       its number + PDF are returned;
 *     - otherwise the in-house CI PDF is generated (dompdf) with a sequential
 *       fallback number.
 *  Returns ['number' => string, 'path' => ?string, 'provider' => string] or null.
 *
 * $inv = [
 *   'orderNumber','date','payment','lang',
 *   'customer' => ['name','email','phone','street','zip','city','country'],
 *   'items'    => [ ['title','variant','price'(brutto Einzel),'qty'], ... ],
 *   'shipping' => float (brutto), 'total' => float (brutto),
 * ]
 */

if (!function_exists('kk_invoice_dir')) {
    function kk_invoice_dir(): string
    {
        $dir = dirname(kirby()->root('index')) . '/storage/invoices';
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
        return $dir;
    }
}

if (!function_exists('kk_money')) {
    function kk_money(float $v): string
    {
        return number_format($v, 2, ',', '.') . ' €';
    }
}

if (!function_exists('kk_invoice_next_number')) {
    /** Sequential, atomic in-house number, e.g. KIE-2026-0001. */
    function kk_invoice_next_number(): string
    {
        $prefix = (string) option('kielkraft.invoiceNumberPrefix', 'KIE');
        $file   = dirname(kirby()->root('index')) . '/storage/invoice-counter.txt';
        $n = 1;
        $fp = @fopen($file, 'c+');
        if ($fp) {
            if (flock($fp, LOCK_EX)) {
                $cur = (int) stream_get_contents($fp);
                $n = $cur + 1;
                ftruncate($fp, 0);
                rewind($fp);
                fwrite($fp, (string) $n);
                fflush($fp);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }
        return $prefix . '-' . date('Y') . '-' . str_pad((string) $n, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('kk_invoice_html')) {
    function kk_invoice_html(array $inv, string $number): string
    {
        $c       = $inv['customer'];
        $vatRate = (float) option('kielkraft.invoiceVatRate', 19);
        $itemsGross = 0.0;
        foreach ($inv['items'] as $it) { $itemsGross += (float) $it['price'] * (int) $it['qty']; }
        $shipGross = (float) $inv['shipping'];
        $gross     = (float) $inv['total'];
        $net       = round($gross / (1 + $vatRate / 100), 2);
        $vat       = round($gross - $net, 2);
        $dateStr   = date('d.m.Y', strtotime($inv['date'] ?? 'now'));

        // Payment terms text
        $holder = (string) option('kielkraft.invoiceBankHolder', 'Boostboards GmbH & Co. KG');
        $iban   = (string) option('kielkraft.invoiceIban', '');
        $bic    = (string) option('kielkraft.invoiceBic', '');
        $bank   = (string) option('kielkraft.invoiceBank', '');
        $payLabels = [
            'vorkasse' => 'Vorkasse / Überweisung', 'paypal' => 'PayPal', 'klarna' => 'Klarna',
            'kreditkarte' => 'Kreditkarte', 'sepa' => 'SEPA-Lastschrift', 'rechnung' => 'Kauf auf Rechnung',
        ];
        $payLabel = $payLabels[$inv['payment']] ?? $inv['payment'];
        if ($inv['payment'] === 'vorkasse') {
            $pay = 'Zahlbar per Vorkasse. Bitte überweisen Sie den Rechnungsbetrag unter Angabe der Rechnungsnummer <strong>' . esc($number) . '</strong> auf folgendes Konto:'
                . '<br>Kontoinhaber: ' . esc($holder)
                . '<br>IBAN: ' . esc($iban !== '' ? $iban : '[IBAN bitte hinterlegen]')
                . ($bic !== '' ? '<br>BIC: ' . esc($bic) : '')
                . ($bank !== '' ? '<br>Bank: ' . esc($bank) : '');
        } elseif ($inv['payment'] === 'rechnung') {
            $pay = 'Zahlbar innerhalb von 14 Tagen ohne Abzug unter Angabe der Rechnungsnummer ' . esc($number) . '.';
        } else {
            $pay = 'Der Rechnungsbetrag wird per ' . esc($payLabel) . ' beglichen.';
        }

        // Line items
        $rows = '';
        $pos = 0;
        foreach ($inv['items'] as $it) {
            $pos++;
            $name = esc($it['title']) . ($it['variant'] ? ' (' . esc($it['variant']) . ')' : '');
            $rows .= '<tr>'
                . '<td class="c">' . $pos . '</td>'
                . '<td>' . $name . '</td>'
                . '<td class="c">' . (int) $it['qty'] . '</td>'
                . '<td class="r">' . kk_money((float) $it['price']) . '</td>'
                . '<td class="r">' . kk_money((float) $it['price'] * (int) $it['qty']) . '</td>'
                . '</tr>';
        }
        if ($shipGross > 0) {
            $pos++;
            $rows .= '<tr><td class="c">' . $pos . '</td><td>Versand / Fracht</td><td class="c">1</td>'
                . '<td class="r">' . kk_money($shipGross) . '</td><td class="r">' . kk_money($shipGross) . '</td></tr>';
        }

        $orderNo = esc($inv['orderNumber']);
        $custAddr = esc($c['name']) . '<br>' . esc($c['street']) . '<br>' . esc($c['zip']) . ' ' . esc($c['city']) . '<br>' . esc($c['country'] ?: 'Deutschland');

        return '<!doctype html><html><head><meta charset="utf-8"><style>
            @page { margin: 22mm 18mm 24mm 18mm; }
            * { font-family: "DejaVu Sans", sans-serif; }
            body { margin: 0; color: #16263a; font-size: 10.5px; line-height: 1.5; }
            .head { background: #0B2F55; color: #fff; padding: 16px 18px; margin: -4px 0 18px; }
            .brand { font-size: 20px; font-weight: bold; letter-spacing: -0.5px; }
            .sender { font-size: 8px; color: #6b7c8c; border-bottom: 1px solid #e1e8ee; padding-bottom: 2px; margin-bottom: 6px; }
            .addr { width: 58%; }
            .meta { width: 42%; }
            .meta table { width: 100%; font-size: 10px; }
            .meta td { padding: 1px 0; }
            .meta td.k { color: #6b7c8c; }
            h1 { font-size: 17px; color: #0B2F55; margin: 18px 0 4px; }
            table.items { width: 100%; border-collapse: collapse; margin-top: 6px; }
            table.items th { background: #f1f5f9; color: #16263a; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: .04em; padding: 6px 8px; border-bottom: 1px solid #cdd9e6; }
            table.items td { padding: 7px 8px; border-bottom: 1px solid #edf1f5; vertical-align: top; }
            td.r, th.r { text-align: right; } td.c, th.c { text-align: center; }
            .totals { width: 48%; margin-left: 52%; margin-top: 10px; font-size: 10.5px; }
            .totals td { padding: 3px 0; }
            .totals td.r { text-align: right; }
            .totals tr.sum td { border-top: 2px solid #0B2F55; font-weight: bold; font-size: 13px; color: #0B2F55; padding-top: 6px; }
            .vatnote { color: #6b7c8c; font-size: 9px; margin-top: 4px; }
            .pay { margin-top: 18px; background: #f7fafc; border: 1px solid #e1e8ee; border-radius: 6px; padding: 11px 13px; font-size: 10px; }
            .foot { position: fixed; bottom: -16mm; left: 0; right: 0; border-top: 1px solid #e1e8ee; padding-top: 6px; font-size: 8px; color: #6b7c8c; }
            .foot td { width: 33%; vertical-align: top; padding-right: 10px; }
        </style></head><body>
        <div class="head"><span class="brand">Kielkraft</span></div>

        <table style="width:100%"><tr>
            <td class="addr">
                <div class="sender">Boostboards GmbH &amp; Co. KG &middot; Groten Hoff 21 &middot; 22359 Hamburg</div>
                ' . $custAddr . '
            </td>
            <td class="meta">
                <table>
                    <tr><td class="k">Rechnungsnummer</td><td class="r"><strong>' . esc($number) . '</strong></td></tr>
                    <tr><td class="k">Rechnungsdatum</td><td class="r">' . $dateStr . '</td></tr>
                    <tr><td class="k">Leistungsdatum</td><td class="r">' . $dateStr . '</td></tr>
                    <tr><td class="k">Bestellnummer</td><td class="r">' . $orderNo . '</td></tr>
                    <tr><td class="k">Kunde</td><td class="r">' . esc($c['email']) . '</td></tr>
                </table>
            </td>
        </tr></table>

        <h1>Rechnung ' . esc($number) . '</h1>
        <p>Vielen Dank für Ihren Einkauf bei Kielkraft. Wir berechnen Ihnen die folgenden Positionen:</p>

        <table class="items">
            <thead><tr>
                <th class="c">Pos.</th><th>Bezeichnung</th><th class="c">Menge</th>
                <th class="r">Einzelpreis</th><th class="r">Gesamt</th>
            </tr></thead>
            <tbody>' . $rows . '</tbody>
        </table>

        <table class="totals">
            <tr><td>Zwischensumme</td><td class="r">' . kk_money($itemsGross) . '</td></tr>
            <tr><td>Versand / Fracht</td><td class="r">' . ($shipGross > 0 ? kk_money($shipGross) : 'frachtfrei') . '</td></tr>
            <tr class="sum"><td>Rechnungsbetrag</td><td class="r">' . kk_money($gross) . '</td></tr>
        </table>
        <div class="vatnote" style="text-align:right">Im Rechnungsbetrag enthalten: ' . (int) $vatRate . ' % MwSt. ' . kk_money($vat) . ' &middot; Nettobetrag ' . kk_money($net) . '</div>

        <div class="pay"><strong>Zahlung:</strong> ' . $pay . '</div>

        <table class="foot"><tr>
            <td><strong>Kielkraft</strong> – eine Marke der Boostboards GmbH &amp; Co. KG<br>Groten Hoff 21, 22359 Hamburg<br>info@boostboards.de &middot; +49 40 60 90 199 69</td>
            <td>Amtsgericht Hamburg HRA 131421<br>USt-IdNr. DE442130574<br>Vertreten durch: Sebastian Keye</td>
            <td>' . ($iban !== '' ? 'Bank: ' . esc($bank) . '<br>IBAN: ' . esc($iban) . '<br>BIC: ' . esc($bic) : 'Bankverbindung auf Anfrage') . '</td>
        </tr></table>
        </body></html>';
    }
}

if (!function_exists('kk_invoice_pdf_path')) {
    /** Render the in-house CI invoice to a PDF file and return its path (or null). */
    function kk_invoice_pdf_path(array $inv, string $number): ?string
    {
        if (!class_exists('\\Dompdf\\Dompdf')) { return null; }
        try {
            $html = kk_invoice_html($inv, $number);
            $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => false, 'defaultFont' => 'DejaVu Sans']);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $path = kk_invoice_dir() . '/' . $number . '.pdf';
            file_put_contents($path, $dompdf->output());
            return is_file($path) ? $path : null;
        } catch (Throwable $e) {
            return null;
        }
    }
}

if (!function_exists('kk_lexoffice_create_invoice')) {
    /**
     * Lexware Office (lexoffice) REST API. Prepared; activates once
     * INVOICE_PROVIDER=lexoffice and LEXOFFICE_API_KEY are set. Returns
     * ['number','path','provider'] or null (then the caller falls back).
     */
    function kk_lexoffice_create_invoice(array $inv): ?array
    {
        $key = trim((string) option('kielkraft.lexofficeApiKey', ''));
        if ($key === '' || option('kielkraft.invoiceProvider', 'fallback') !== 'lexoffice') {
            return null;
        }
        $vatRate = (float) option('kielkraft.invoiceVatRate', 19);
        $base    = 'https://api.lexoffice.io/v1';
        $headers = ['Authorization' => 'Bearer ' . $key, 'Accept' => 'application/json', 'Content-Type' => 'application/json'];
        try {
            $lineItems = [];
            foreach ($inv['items'] as $it) {
                $lineItems[] = [
                    'type'      => 'custom',
                    'name'      => $it['title'] . ($it['variant'] ? ' (' . $it['variant'] . ')' : ''),
                    'quantity'  => (float) $it['qty'],
                    'unitName'  => 'Stück',
                    'unitPrice' => ['currency' => 'EUR', 'grossAmount' => round((float) $it['price'], 2), 'taxRatePercentage' => $vatRate],
                ];
            }
            if ((float) $inv['shipping'] > 0) {
                $lineItems[] = ['type' => 'custom', 'name' => 'Versand / Fracht', 'quantity' => 1, 'unitName' => 'Pauschale',
                    'unitPrice' => ['currency' => 'EUR', 'grossAmount' => round((float) $inv['shipping'], 2), 'taxRatePercentage' => $vatRate]];
            }
            $payload = [
                'archived'           => false,
                'voucherDate'        => date('c'),
                'address'            => ['name' => $inv['customer']['name'], 'street' => $inv['customer']['street'],
                                         'zip' => $inv['customer']['zip'], 'city' => $inv['customer']['city'], 'countryCode' => 'DE'],
                'lineItems'          => $lineItems,
                'totalPrice'         => ['currency' => 'EUR'],
                'taxConditions'      => ['taxType' => 'gross'],
                'shippingConditions' => ['shippingDate' => date('c'), 'shippingType' => 'service'],
                'title'              => 'Rechnung',
                'remark'             => 'Bestellung ' . $inv['orderNumber'],
            ];
            $res = Remote::request("$base/invoices?finalize=true", ['method' => 'POST', 'headers' => $headers, 'data' => json_encode($payload)]);
            if ($res->code() < 200 || $res->code() >= 300) { return null; }
            $id = $res->json()['id'] ?? null;
            if (!$id) { return null; }

            $number = Remote::request("$base/invoices/$id", ['headers' => $headers])->json()['voucherNumber'] ?? ('LEX-' . $id);

            $path   = null;
            $fileId = Remote::request("$base/invoices/$id/document", ['headers' => $headers])->json()['documentFileId'] ?? null;
            if ($fileId) {
                $pdfRes = Remote::request("$base/files/$fileId", ['headers' => ['Authorization' => 'Bearer ' . $key, 'Accept' => 'application/pdf']]);
                if ($pdfRes->code() === 200) {
                    $path = kk_invoice_dir() . '/' . $number . '.pdf';
                    file_put_contents($path, $pdfRes->content());
                }
            }
            return ['number' => $number, 'path' => $path, 'provider' => 'lexoffice'];
        } catch (Throwable $e) {
            return null;
        }
    }
}

if (!function_exists('kk_invoice_create')) {
    function kk_invoice_create(array $inv): ?array
    {
        // 1) Lexware Office, if configured
        $lex = kk_lexoffice_create_invoice($inv);
        if ($lex !== null) { return $lex; }

        // 2) In-house CI PDF (fallback)
        $number = kk_invoice_next_number();
        $path   = kk_invoice_pdf_path($inv, $number);
        return ['number' => $number, 'path' => $path, 'provider' => 'fallback'];
    }
}
