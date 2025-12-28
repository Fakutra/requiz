<?php

namespace App\Services;

use setasign\Fpdi\Tcpdf\Fpdi;

class PdfWatermarkService
{
    public static function watermark(
        string $inputPath,
        string $outputPath,
        string $watermarkText
    ): void {
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($inputPath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tpl  = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($tpl);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tpl);

            /**
             * ============================
             * WATERMARK — TRUE CENTER
             * ============================
             */
            $fontSize = 120;

            $pdf->SetFont('helvetica', 'B', $fontSize);
            $pdf->SetTextColor(150, 150, 150);
            $pdf->SetAlpha(0.18);

            // pusat halaman
            $pageCenterX = $size['width'] / 2;
            $pageCenterY = $size['height'] / 2;

            // hitung ukuran teks AKTUAL
            $textWidth  = $pdf->GetStringWidth($watermarkText);
            $textHeight = $fontSize; // tinggi font ≈ fontSize

            $pdf->StartTransform();

            // ROTASI DARI TENGAH HALAMAN
            $pdf->Rotate(45, $pageCenterX, $pageCenterY);

            // POSISI BENAR-BENAR TENGAH
            $pdf->SetXY(
                $pageCenterX - ($textWidth / 2),
                $pageCenterY - ($textHeight / 2)
            );

            $pdf->Text(
                $pageCenterX - ($textWidth / 2),
                $pageCenterY - ($textHeight / 2),
                strtoupper($watermarkText)
            );

            $pdf->StopTransform();
            $pdf->SetAlpha(1);
        }

        $pdf->Output($outputPath, 'F');
    }
}
