<?php
/**
 * Export Assigned Assets to Excel (.xlsx)
 *
 * Generates a proper Office Open XML spreadsheet of every active
 * asset assignment, including full asset details.
 */

session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();

$stmt = $pdo->prepare("
    SELECT
        a.asset_tag,
        a.asset_name,
        a.model_number,
        a.serial_number,
        a.barcode,
        ac.category_name,
        asc2.subcategory_name,
        v.vendor_name,
        a.purchase_date,
        a.purchase_cost,
        a.warranty_expiry,
        a.asset_condition,
        a.status        AS asset_status,
        f.floor_name,
        d.department_name,
        l.location_name,
        l.room_number,
        aa.assigned_date,
        aa.assigned_by,
        aa.status        AS assignment_status
    FROM asset_assignments aa
    LEFT JOIN assets             a    ON aa.asset_id       = a.id
    LEFT JOIN asset_categories   ac   ON a.category_id     = ac.id
    LEFT JOIN asset_subcategories asc2 ON a.subcategory_id = asc2.id
    LEFT JOIN vendors            v    ON a.vendor_id       = v.id
    LEFT JOIN floors             f    ON aa.floor_id       = f.id
    LEFT JOIN departments        d    ON aa.department_id  = d.id
    LEFT JOIN locations          l    ON aa.location_id    = l.id
    WHERE aa.is_deleted = 0
    ORDER BY aa.id DESC
");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ------------------------------------------------------------------ */
/*  Column headers (display labels)                                   */
/* ------------------------------------------------------------------ */
$headers = [
    'Asset Tag',
    'Asset Name',
    'Model Number',
    'Serial Number',
    'Barcode',
    'Category',
    'Subcategory',
    'Vendor',
    'Purchase Date',
    'Purchase Cost',
    'Warranty Expiry',
    'Condition',
    'Asset Status',
    'Floor',
    'Department',
    'Location',
    'Room Number',
    'Assigned Date',
    'Assigned By',
    'Assignment Status',
];

/* ------------------------------------------------------------------ */
/*  Helper: escape XML special characters                             */
/* ------------------------------------------------------------------ */
function xmlEscape(string $value): string
{
    return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

/* ------------------------------------------------------------------ */
/*  Helper: convert a 0-based column index to an Excel column letter  */
/* ------------------------------------------------------------------ */
function colLetter(int $index): string
{
    $letter = '';
    $index++;                       // 1-based
    while ($index > 0) {
        $index--;
        $letter = chr(65 + ($index % 26)) . $letter;
        $index  = intdiv($index, 26);
    }
    return $letter;
}

/* ------------------------------------------------------------------ */
/*  Build sheet XML                                                   */
/* ------------------------------------------------------------------ */
$sheetXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
$sheetXml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"';
$sheetXml .= ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';

// Column widths
$sheetXml .= '<cols>';
foreach ($headers as $i => $h) {
    $width = max(strlen($h) + 4, 14);
    $col   = $i + 1;
    $sheetXml .= '<col min="' . $col . '" max="' . $col . '" width="' . $width . '" bestFit="1" customWidth="1"/>';
}
$sheetXml .= '</cols>';

$sheetXml .= '<sheetData>';

// Header row (row 1) — style index 1 = bold
$sheetXml .= '<row r="1">';
foreach ($headers as $i => $header) {
    $cell = colLetter($i) . '1';
    $sheetXml .= '<c r="' . $cell . '" t="inlineStr" s="1"><is><t>' . xmlEscape($header) . '</t></is></c>';
}
$sheetXml .= '</row>';

// Data rows
$rowNum = 2;
foreach ($rows as $row) {
    $values = array_values($row);
    $sheetXml .= '<row r="' . $rowNum . '">';
    foreach ($values as $i => $value) {
        $cell = colLetter($i) . $rowNum;
        $val  = $value ?? '';
        $sheetXml .= '<c r="' . $cell . '" t="inlineStr"><is><t>' . xmlEscape((string) $val) . '</t></is></c>';
    }
    $sheetXml .= '</row>';
    $rowNum++;
}

$sheetXml .= '</sheetData>';
$sheetXml .= '</worksheet>';

/* ------------------------------------------------------------------ */
/*  Styles (minimal – just bold for the header)                       */
/* ------------------------------------------------------------------ */
$stylesXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
$stylesXml .= '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
$stylesXml .= '<fonts count="2">';
$stylesXml .= '<font><sz val="11"/><name val="Calibri"/></font>';
$stylesXml .= '<font><b/><sz val="11"/><name val="Calibri"/></font>';
$stylesXml .= '</fonts>';
$stylesXml .= '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>';
$stylesXml .= '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>';
$stylesXml .= '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>';
$stylesXml .= '<cellXfs count="2">';
$stylesXml .= '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>';
$stylesXml .= '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>';   // bold
$stylesXml .= '</cellXfs>';
$stylesXml .= '</styleSheet>';

/* ------------------------------------------------------------------ */
/*  Shared strings (not used – we use inline strings)                 */
/* ------------------------------------------------------------------ */
$sharedStringsXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
$sharedStringsXml .= '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="0" uniqueCount="0"/>';

/* ------------------------------------------------------------------ */
/*  Workbook                                                          */
/* ------------------------------------------------------------------ */
$workbookXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
$workbookXml .= '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"';
$workbookXml .= ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';
$workbookXml .= '<sheets><sheet name="Assigned Assets" sheetId="1" r:id="rId1"/></sheets>';
$workbookXml .= '</workbook>';

/* ------------------------------------------------------------------ */
/*  Relationships                                                     */
/* ------------------------------------------------------------------ */
$relsXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
$relsXml .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
$relsXml .= '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>';
$relsXml .= '</Relationships>';

$workbookRelsXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
$workbookRelsXml .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
$workbookRelsXml .= '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/spreadsheetml/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>';
$workbookRelsXml .= '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/spreadsheetml/2006/relationships/styles" Target="styles.xml"/>';
$workbookRelsXml .= '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/spreadsheetml/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>';
$workbookRelsXml .= '</Relationships>';

/* ------------------------------------------------------------------ */
/*  Content Types                                                     */
/* ------------------------------------------------------------------ */
$contentTypesXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
$contentTypesXml .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">';
$contentTypesXml .= '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';
$contentTypesXml .= '<Default Extension="xml" ContentType="application/xml"/>';
$contentTypesXml .= '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>';
$contentTypesXml .= '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
$contentTypesXml .= '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
$contentTypesXml .= '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>';
$contentTypesXml .= '</Types>';

/* ------------------------------------------------------------------ */
/*  Assemble the .xlsx ZIP archive                                    */
/* ------------------------------------------------------------------ */
$tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_');

$zip = new ZipArchive();
if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    die('Unable to create Excel file.');
}

$zip->addFromString('[Content_Types].xml',           $contentTypesXml);
$zip->addFromString('_rels/.rels',                   $relsXml);
$zip->addFromString('xl/workbook.xml',               $workbookXml);
$zip->addFromString('xl/_rels/workbook.xml.rels',    $workbookRelsXml);
$zip->addFromString('xl/worksheets/sheet1.xml',      $sheetXml);
$zip->addFromString('xl/styles.xml',                 $stylesXml);
$zip->addFromString('xl/sharedStrings.xml',          $sharedStringsXml);
$zip->close();

/* ------------------------------------------------------------------ */
/*  Send the file to the browser                                      */
/* ------------------------------------------------------------------ */
$filename = 'Assigned_Assets_' . date('Y-m-d') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($tmpFile));
header('Cache-Control: max-age=0');

readfile($tmpFile);
unlink($tmpFile);
exit;
