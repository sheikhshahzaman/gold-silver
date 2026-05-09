<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Codes - Islamabad Bullion Exchange</title>
    <style>
        @page { margin: 8mm; }
        body { font-family: 'Helvetica', sans-serif; margin: 0; padding: 0; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: avoid; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td {
            width: {{ number_format(100 / $cols, 4) }}%;
            text-align: center;
            vertical-align: top;
            padding: {{ $cellPadMm }}mm {{ max(1, $cellPadMm / 2) }}mm;
        }
        .qr-img { width: {{ $qrMm }}mm; height: {{ $qrMm }}mm; }
        .serial {
            font-size: {{ $qrMm >= 20 ? '8pt' : '6.5pt' }};
            font-weight: bold;
            margin-top: 1.5mm;
            letter-spacing: 0.3px;
        }
        .product-name {
            font-size: {{ $qrMm >= 20 ? '6.5pt' : '5.5pt' }};
            color: #555;
            margin-top: 0.5mm;
            max-width: {{ $qrMm * 1.6 }}mm;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .brand {
            font-size: 5pt;
            color: #999;
            margin-top: 0.5mm;
        }
    </style>
</head>
<body>
    @foreach($pages as $pageItems)
    <div class="page">
        <table class="grid">
            @foreach($pageItems->chunk($cols) as $row)
            <tr>
                @foreach($row as $item)
                <td>
                    @if($item->qr_code_path && file_exists(public_path($item->qr_code_path)))
                        @if(str_ends_with($item->qr_code_path, '.svg'))
                            <div class="qr-img" style="display:inline-block;">{!! file_get_contents(public_path($item->qr_code_path)) !!}</div>
                        @else
                            <img src="{{ public_path($item->qr_code_path) }}" class="qr-img" alt="QR">
                        @endif
                    @else
                        <div class="qr-img" style="border:1px solid #ccc;display:inline-block;line-height:{{ $qrMm }}mm;font-size:6pt;color:#999;">No QR</div>
                    @endif
                    @if($showCaption)
                        <div class="serial">{{ $item->serial_number }}</div>
                        <div class="product-name">{{ $item->product->name ?? 'Unknown' }}</div>
                        <div class="brand">IBE — Scan to Verify</div>
                    @endif
                </td>
                @endforeach
                {{-- Fill empty cells in last row --}}
                @for($i = $row->count(); $i < $cols; $i++)
                <td></td>
                @endfor
            </tr>
            @endforeach
        </table>
    </div>
    @endforeach
</body>
</html>
