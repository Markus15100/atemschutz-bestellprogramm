<?php
ob_start();

require __DIR__ . '/../config/auth.php';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../tcpdf/tcpdf.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die('Ungültige Bestellung');
}

/* Bestellung laden */
$stmt = $pdo->prepare("SELECT * FROM bestellungen WHERE id = ?");
$stmt->execute([$id]);
$bestellung = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$bestellung) {
    die('Bestellung nicht gefunden');
}

/* Positionen laden */
$stmt = $pdo->prepare("
    SELECT 
        a.artikelnummer,
        a.name,
        a.ve_groesse,
        p.menge,
        p.einheit,
        p.einzelpreis
    FROM bestellpositionen p
    JOIN artikel a ON a.id = p.artikel_id
    WHERE p.bestellung_id = ?
");
$stmt->execute([$id]);
$positionen = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ======================
   PDF INITIALISIEREN
   ====================== */
$pdf = new TCPDF('P', 'mm', 'A4');
$pdf->SetCreator('Atemschutz FF Bodelshausen');
$pdf->SetAuthor('FF Bodelshausen');
$pdf->SetTitle('Bestellung #' . $bestellung['id']);
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

/* ===== KOPFBEREICH ===== */

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 6, 'Freiwillige Feuerwehr Bodelshausen', 0, 1);

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 5, 'Abteilung Atemschutz', 0, 1);
$pdf->Cell(0, 5, 'Eberhardtstraße 25', 0, 1);
$pdf->Cell(0, 5, '72411 Bodelshausen', 0, 1);

$pdf->Ln(3);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(8);

/* ===== TITEL ===== */

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Bestellung #' . $bestellung['id'], 0, 1);

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'Feuerwehr: ' . $bestellung['feuerwehr'], 0, 1);
$pdf->Cell(0, 6, 'Datum: ' . date('d.m.Y H:i', strtotime($bestellung['datum'])), 0, 1);
$pdf->Ln(4);

/* Tabelle */
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(25, 8, 'Art.-Nr.', 1);
$pdf->Cell(70, 8, 'Artikel', 1);
$pdf->Cell(15, 8, 'Menge', 1, 0, 'R');
$pdf->Cell(15, 8, 'Einheit', 1);
$pdf->Cell(20, 8, 'VE-Größe', 1, 0, 'R');
$pdf->Cell(20, 8, 'Preis €', 1, 1, 'R');

$pdf->SetFont('helvetica', '', 9);

$gesamt = 0;

foreach ($positionen as $p) {
    $startY = $pdf->GetY();

    $pdf->MultiCell(25, 8, $p['artikelnummer'], 1, 'L', false, 0);
    $pdf->MultiCell(70, 8, $p['name'], 1, 'L', false, 0);
    $pdf->MultiCell(15, 8, $p['menge'], 1, 'R', false, 0);
    $pdf->MultiCell(15, 8, $p['einheit'], 1, 'L', false, 0);
    $pdf->MultiCell(20, 8, (int)$p['ve_groesse'] . ' Stk', 1, 'R', false, 0);

    $preis = $p['menge'] * $p['einzelpreis'];
    $gesamt += $preis;

    $pdf->MultiCell(20, 8, number_format($preis, 2, ',', '.'), 1, 'R', false, 1);
}

/* Gesamt */
$pdf->Ln(4);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(145, 8, 'Gesamt', 1);
$pdf->Cell(20, 8, number_format($gesamt, 2, ',', '.') . ' €', 1, 1, 'R');

/* Ausgabe */
ob_end_clean();
$pdf->Output('Bestellung_' . $bestellung['id'] . '.pdf', 'I');
exit;
