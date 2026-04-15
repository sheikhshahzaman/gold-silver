<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Codes - Islamabad Bullion Exchange</title>
    <style>
        @page { margin: 10mm; }
        body { font-family: 'Helvetica', sans-serif; margin: 0; padding: 0; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: avoid; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 6mm 3mm;
            border: 1px dashed #ccc;
        }
        .qr-img { width: 30mm; height: 30mm; }
        .serial {
            font-size: 8pt;
            font-weight: bold;
            margin-top: 2mm;
            letter-spacing: 0.5px;
        }
        .product-name {
            font-size: 6.5pt;
            color: #555;
            margin-top: 1mm;
            max-width: 55mm;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .brand {
            font-size: 5.5pt;
            color: #999;
            margin-top: 1mm;
        }
    </style>
</head>
<body>
    @foreach($pages as $pageItems)
    <div class="page">
        <table class="grid">
            @foreach($pageItems->chunk(3) as $row)
            <tr>
                @foreach($row as $item)
                <td>
                    @if($item->qr_code_path && file_exists(public_path($item->qr_code_path)))
                        @if(str_ends_with($item->qr_code_path, '.svg'))
                            <div class="qr-img" style="width:30mm;height:30mm;display:inline-block;">{!! file_get_contents(public_path($item->qr_code_path)) !!}</div>
                        @else
                            <img src="{{ public_path($item->qr_code_path) }}" class="qr-img" alt="QR">
                        @endif
                    @else
                        <div style="width:30mm;height:30mm;border:1px solid #ccc;display:inline-block;line-height:30mm;font-size:7pt;color:#999;">No QR</div>
                    @endif
                    <div class="serial">{{ $item->serial_number }}</div>
                    <div class="product-name">{{ $item->product->name ?? 'Unknown' }}</div>
                    <div class="brand">IBE - Scan to Verify</div>
                </td>
                @endforeach
                {{-- Fill empty cells in last row --}}
                @for($i = $row->count(); $i < 3; $i++)
                <td></td>
                @endfor
            </tr>
            @endforeach
        </table>
    </div>
    @endforeach
</body>
</html>
