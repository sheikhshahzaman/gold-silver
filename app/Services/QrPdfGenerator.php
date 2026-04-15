<?php

namespace App\Services;

use App\Models\InventoryItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class QrPdfGenerator
{
    public function generate(Collection $items): \Barryvdh\DomPDF\PDF
    {
        $items->load('product');

        // 3 columns x 5 rows = 15 per page
        $pages = $items->chunk(15);

        return Pdf::loadView('pdf.qr-batch', [
            'pages' => $pages,
        ])->setPaper('a4', 'portrait');
    }
}
