<x-filament-panels::page>
    <div wire:poll.30s="refreshRates">
        <x-filament::section>
            <x-slot name="heading">Live Metal Prices</x-slot>
            <x-slot name="description">International spot and currency rows used by the website and app.</x-slot>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(148, 163, 184, 0.22);">
                            <th style="padding: 14px 18px; font-weight: 600;">Item</th>
                            <th style="padding: 14px 18px; font-weight: 600;">Type</th>
                            <th style="padding: 14px 18px; font-weight: 600;">Unit</th>
                            <th style="padding: 14px 18px; font-weight: 600; text-align: right;">Bid / Buy</th>
                            <th style="padding: 14px 18px; font-weight: 600; text-align: right;">Ask / Sell</th>
                            <th style="padding: 14px 18px; font-weight: 600;">Currency</th>
                            <th style="padding: 14px 18px; font-weight: 600;">Source</th>
                            <th style="padding: 14px 18px; font-weight: 600;">Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->rows() as $row)
                            <tr style="border-bottom: 1px solid rgba(148, 163, 184, 0.12);">
                                <td style="padding: 14px 18px; white-space: nowrap;">{{ $row['item'] }}</td>
                                <td style="padding: 14px 18px; white-space: nowrap;">{{ $row['type'] }}</td>
                                <td style="padding: 14px 18px; white-space: nowrap;">{{ $row['unit'] }}</td>
                                <td style="padding: 14px 18px; text-align: right; white-space: nowrap;">
                                    {{ is_numeric($row['buy']) ? number_format((float) $row['buy'], 4) : '-' }}
                                </td>
                                <td style="padding: 14px 18px; text-align: right; white-space: nowrap;">
                                    {{ is_numeric($row['sell']) ? number_format((float) $row['sell'], 4) : '-' }}
                                </td>
                                <td style="padding: 14px 18px; white-space: nowrap;">{{ $row['currency'] }}</td>
                                <td style="padding: 14px 18px; white-space: nowrap;">{{ $row['source'] }}</td>
                                <td style="padding: 14px 18px; white-space: nowrap;">{{ $row['updated_at'] ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
