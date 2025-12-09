<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Movement Report - {{ now()->format('d/m/Y') }}</title>
    <style>
        @page {
            margin: 20mm;
            size: A4;
        }
        
        body { 
            font-family: 'Arial', 'Helvetica', sans-serif; 
            font-size: 10px; 
            line-height: 1.4;
            color: #000000;
            margin: 0;
            padding: 0;
        }
        
        /* Header */
        .header { 
            text-align: center; 
            padding: 20px 0;
            border-bottom: 2px solid #000000;
            margin-bottom: 20px;
        }
        
        .header h1 { 
            margin: 0 0 10px 0; 
            font-size: 18px; 
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header .subtitle {
            margin: 0;
            font-size: 12px;
            font-weight: normal;
        }
        
        .header .meta {
            margin: 8px 0 0 0;
            font-size: 9px;
            color: #666666;
        }
        
        /* Summary Section */
        .summary-section {
            margin-bottom: 25px;
        }
        
        .summary-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
            border-bottom: 1px solid #cccccc;
            padding-bottom: 5px;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
        }
        
        .summary-table th {
            background-color: #f5f5f5;
            border: 1px solid #000000;
            padding: 8px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        
        .summary-table td {
            border: 1px solid #000000;
            padding: 8px;
            font-size: 10px;
            text-align: center;
            font-weight: bold;
        }
        
        /* Movement Table */
        .table-section {
            margin-top: 20px;
        }
        
        .table-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
            border-bottom: 1px solid #cccccc;
            padding-bottom: 5px;
        }
        
        .data-table { 
            width: 100%; 
            border-collapse: collapse;
            border: 1px solid #000000;
        }
        
        .data-table th { 
            background-color: #f5f5f5;
            border: 1px solid #000000;
            padding: 8px 6px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left;
        }
        
        .data-table td { 
            border: 1px solid #000000;
            padding: 6px;
            font-size: 9px;
            vertical-align: top;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        /* Movement Type Badges */
        .movement-badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1px solid #000000;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-in { 
            background-color: #d4edda;
            color: #155724;
            border-color: #28a745;
        }
        
        .badge-out { 
            background-color: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }
        
        .badge-adjustment { 
            background-color: #ffffff;
            color: #000000;
            border-color: #6c757d;
        }
        
        .badge-checking { 
            background-color: #ffffff;
            color: #000000;
            border-color: #6c757d;
        }
        
        .badge-wi { 
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffeaa7;
        }
        
        /* Quantity Colors */
        .quantity-positive { 
            color: #28a745;
            font-weight: bold;
        }
        
        .quantity-negative { 
            color: #dc3545;
            font-weight: bold;
        }
        
        .quantity-neutral { 
            color: #000000;
            font-weight: bold;
        }
        
        /* Text Utilities */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        
        .item-code {
            color: #666666;
            font-size: 8px;
            font-style: italic;
        }
        
        .staff-email {
            color: #666666;
            font-size: 8px;
        }
        
        /* Validation Section */
        .validation-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #000000;
        }
        
        .validation-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        
        .validation-cell {
            width: 50%;
            text-align: center;
            padding: 20px;
            vertical-align: bottom;
        }
        
        .validation-label {
            font-size: 10px;
            color: #666666;
            margin-bottom: 5px;
        }
        
        .validation-name {
            font-size: 10px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 5px;
        }
        
        .validation-signature-line {
            font-size: 9px;
            color: #000000;
            margin-top: 50px;
            border-top: 1px solid #000000;
            padding-top: 5px;
            display: inline-block;
            min-width: 80px;
        }
        
        .validation-team {
            font-size: 8px;
            color: #666666;
            margin-top: 5px;
        }
        
        /* Footer */
        .footer {
            border-top: 2px solid #000000;
            text-align: center;
            padding: 15px 0;
            margin-top: 30px;
            font-size: 9px;
            color: #666666;
        }
        
        .footer strong {
            font-weight: bold;
        }
        
        /* Page Break */
        .page-break { 
            page-break-before: always; 
        }
        
        /* Column Widths */
        .col-item { width: 20%; }
        .col-type { width: 10%; }
        .col-quantity { width: 10%; }
        .col-before { width: 8%; }
        .col-after { width: 8%; }
        .col-staff { width: 18%; }
        .col-date { width: 12%; }
        .col-notes { width: 14%; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>STOCK MOVEMENT REPORT</h1>
        <p class="subtitle">Inventory Management System</p>
        <p class="meta">
            Generated: {{ now()->format('d/m/Y H:i') }} | 
            Total Records: {{ $movements->count() }} | 
            Report ID: {{ strtoupper(substr(md5(time()), 0, 8)) }}
        </p>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-section">
        <div class="summary-title">Summary Statistics</div>
        <table class="summary-table">
            <tr>
                <th>Total Movements</th>
                <th>Stock In</th>
                <th>Stock Out</th>
                <th>Checking</th>
                <th>Stock Adjustment</th>
                <th>Ambil</th>
                <th>Total In Units</th>
                <th>Total Out Units</th>
            </tr>
            <tr>
                <td>{{ number_format($analytics['total_movements']) }}</td>
                <td>{{ number_format($analytics['stock_in_count']) }}</td>
                <td>{{ number_format($analytics['stock_out_count']) }}</td>
                <td>{{ number_format($analytics['checking_count']) }}</td>
                <td>{{ number_format($analytics['adjustment_count']) }}</td>
                <td>{{ number_format($analytics['wi_consumption_count']) }}</td>
                <td>{{ number_format($analytics['total_stock_in']) }}</td>
                <td>{{ number_format($analytics['total_stock_out']) }}</td>
            </tr>
        </table>
    </div>

    <!-- Movement Details Table -->
    <div class="table-section">
        <div class="table-title">Stock Movement Details</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-item">Item</th>
                    <th class="col-type">Type</th>
                    <th class="col-quantity text-right">Quantity</th>
                    <th class="col-before text-right">Before</th>
                    <th class="col-after text-right">After</th>
                    <th class="col-staff">Staff</th>
                    <th class="col-date">Date & Time</th>
                    <th class="col-notes">Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movements as $movement)
                <tr>
                    <td class="col-item">
                        <div class="text-bold">{{ $movement->item->name }}</div>
                        <div class="item-code">{{ $movement->item->item_code }}</div>
                    </td>
                    <td class="col-type">
                        @php
                            $badgeClass = match($movement->movement_type) {
                                'IN' => 'badge-in',
                                'OUT' => 'badge-out',
                                'ADJUSTMENT' => 'badge-adjustment',
                                'CHECKING_RESULT' => 'badge-checking',
                                'WI_CONSUMPTION' => 'badge-wi',
                                default => 'badge-adjustment'
                            };
                        @endphp
                        <span class="movement-badge {{ $badgeClass }}">{{ $movement->movement_type }}</span>
                    </td>
                    <td class="col-quantity text-right">
                        @php
                            $quantityClass = match($movement->movement_type) {
                                'IN' => 'quantity-positive',
                                'OUT' => 'quantity-negative',
                                'WI_CONSUMPTION' => 'quantity-negative',
                                default => 'quantity-neutral'
                            };
                        @endphp
                        <span class="{{ $quantityClass }}">
                            {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity) }}
                        </span>
                        <div style="font-size: 8px; color: #666666;">{{ $movement->item->unit }}</div>
                    </td>
                    <td class="col-before text-right">
                        <span class="text-bold">{{ number_format($movement->before_quantity) }}</span>
                    </td>
                    <td class="col-after text-right">
                        <span class="text-bold">{{ number_format($movement->after_quantity) }}</span>
                    </td>
                    <td class="col-staff">
                        <div class="text-bold">{{ $movement->user->name }}</div>
                        <div class="staff-email">{{ $movement->user->email }}</div>
                    </td>
                    <td class="col-date">
                        <div class="text-bold">{{ $movement->created_at->format('d/m/Y') }}</div>
                        <div style="font-size: 8px; color: #666666;">{{ $movement->created_at->format('H:i') }}</div>
                    </td>
                    <td class="col-notes">
                        {{ $movement->notes ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Validation Section -->
    <div class="validation-section">
        <table class="validation-table">
            <tr>
                <td class="validation-cell" style="width: 100%; text-align: right;">
                <div>
                            <div class="validation-label">Disetujui oleh,</div>
                            <div class="validation-name">Team Leader</div>
                            <div class="validation-signature-line"></div>
                            <div class="validation-name">Hafizh Hadiawan</div>
                            <div class="validation-team">
                                (PV Electric Team)
                            </div>
                        </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Inventory Management System</strong> - Stock Movement Report</p>
        <p>
            Generated: {{ now()->format('d/m/Y H:i:s') }} | 
            Total Records: {{ $movements->count() }} | 
            © {{ now()->format('Y') }} All Rights Reserved
        </p>
    </div>
</body>
</html>