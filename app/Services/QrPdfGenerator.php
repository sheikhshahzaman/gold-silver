<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class QrPdfGenerator
{
    /**
     * QR sticker size presets. Keys are values bound to the admin form.
     * `qrMm` is the physical print size; `cols` × `rows` is the page grid.
     */
    public const SIZES = [
        'tiny' => [
            'qrMm' => 10,
            'cols' => 8,
            'rows' => 12,
            'cellPadMm' => 1.5,
            'showCaption' => false,
            'label' => 'Tiny (10mm) — 1g bars / very small coins',
        ],
        'small' => [
            'qrMm' => 14,
            'cols' => 6,
            'rows' => 9,
            'cellPadMm' => 2,
            'showCaption' => true,
            'label' => 'Small (14mm) — 5g bars / 1 tola coins',
        ],
        'medium' => [
            'qrMm' => 20,
            'cols' => 4,
            'rows' => 6,
            'cellPadMm' => 4,
            'showCaption' => true,
            'label' => 'Medium (20mm) — 10g bars',
        ],
        'large' => [
            'qrMm' => 30,
            'cols' => 3,
            'rows' => 5,
            'cellPadMm' => 6,
            'showCaption' => true,
            'label' => 'Large (30mm) — bigger bars / display sheets',
        ],
    ];

    /**
     * Build options for a Filament Select::make()->options(...).
     */
    public static function sizeOptions(): array
    {
        return collect(self::SIZES)->mapWithKeys(
            fn (array $cfg, string $key) => [$key => $cfg['label']],
        )->all();
    }

    public function generate(Collection $items, string $size = 'medium'): \Barryvdh\DomPDF\PDF
    {
        $cfg = self::SIZES[$size] ?? self::SIZES['medium'];

        $items->load('product');

        $perPage = $cfg['cols'] * $cfg['rows'];
        $pages = $items->chunk($perPage);

        return Pdf::loadView('pdf.qr-batch', [
            'pages' => $pages,
            'qrMm' => $cfg['qrMm'],
            'cols' => $cfg['cols'],
            'cellPadMm' => $cfg['cellPadMm'],
            'showCaption' => $cfg['showCaption'],
        ])->setPaper('a4', 'portrait');
    }
}
