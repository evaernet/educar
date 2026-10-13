<?php

namespace App\Services;

/**
 * ¿Qué hace?: Arma un PDF de texto simple (una o más páginas) sin librerías externas,
 * así no hace falta instalar nada con composer.
 * Usa la fuente Courier y codificación Windows-1252 (soporta tildes y ñ).
 */
class SimplePdf
{
    /** @param array<string> $lineas */
    public static function render(array $lineas): string
    {
        $porPagina = 55;
        $paginas = array_chunk($lineas ?: [''], $porPagina);

        $objetos = [];
        $objetos[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $hijos = [];
        $n = 4; // 1 catálogo, 2 páginas, 3 fuente, luego (página, contenido) por cada página
        $paginasObj = [];
        foreach ($paginas as $bloque) {
            $stream = "BT\n/F1 10 Tf\n13 TL\n50 800 Td\n";
            foreach ($bloque as $linea) {
                $stream .= '(' . self::escapar($linea) . ") Tj T*\n";
            }
            $stream .= "ET";
            $pageId = $n++;
            $contId = $n++;
            $hijos[] = "$pageId 0 R";
            $paginasObj[$pageId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R >> >> /Contents $contId 0 R >>";
            $paginasObj[$contId] = "<< /Length " . strlen($stream) . " >>\nstream\n$stream\nendstream";
        }
        $objetos[2] = '<< /Type /Pages /Kids [' . implode(' ', $hijos) . '] /Count ' . count($hijos) . ' >>';
        $objetos[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Courier /Encoding /WinAnsiEncoding >>';
        foreach ($paginasObj as $id => $contenido) {
            $objetos[$id] = $contenido;
        }
        ksort($objetos);

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objetos as $id => $contenido) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "$id 0 obj\n$contenido\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objetos) + 1) . "\n0000000000 65535 f \n";
        foreach ($offsets as $off) {
            $pdf .= sprintf("%010d 00000 n \n", $off);
        }
        $pdf .= "trailer\n<< /Size " . (count($objetos) + 1) . " /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";
        return $pdf;
    }

    private static function escapar(string $texto): string
    {
        $texto = mb_convert_encoding($texto, 'Windows-1252', 'UTF-8');
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $texto);
    }
}
