<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('admin.wi.show_title') }} — {{ __('user.wi_type.ambil') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #2c3e50;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #34495e;
        }
        
        .info-value {
            flex: 1;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending { background-color: #f39c12; color: white; }
        .status-in_progress { background-color: #3498db; color: white; }
        .status-completed { background-color: #27ae60; color: white; }
        .status-overdue { background-color: #e74c3c; color: white; }
        
        .destination-section {
            background-color: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #27ae60;
        }
        
        .destination-title {
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 5px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .summary-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #ecf0f1;
            border-radius: 5px;
        }
        
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #bdc3c7;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #3498db;
            transition: width 0.3s ease;
        }
        
        .notes-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff3cd;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
        
        .notes-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 5px;
        }
        
        /* Validation Section */
        .validation-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #333;
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
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        
        .validation-name {
            font-size: 10px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .validation-signature-line {
            font-size: 9px;
            color: #333;
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 5px;
            display: inline-block;
            min-width: 120px;
        }
        
        .validation-team {
            font-size: 8px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>WORK INSTRUCTION REPORT</h1>
        <h2>Type: AMBIL (PICKUP)</h2>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">WI Number:</div>
            <div class="info-value">{{ $workInstruction->wi_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Title:</div>
            <div class="info-value">{{ $workInstruction->title }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Assigned User:</div>
            <div class="info-value">{{ $workInstruction->assignedUser->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Deadline:</div>
            <div class="info-value">{{ $workInstruction->deadline->format('d/m/Y H:i') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status-badge status-{{ $workInstruction->status }}">
                    {{ str_replace('_', ' ', $workInstruction->status) }}
                </span>
            </div>
        </div>
        @if($workInstruction->description)
        <div class="info-row">
            <div class="info-label">Description:</div>
            <div class="info-value">{{ $workInstruction->description }}</div>
        </div>
        @endif
    </div>

    @if($workInstruction->destination_line)
    <div class="destination-section">
        <div class="destination-title">Destination Line Production:</div>
        <div>{{ $workInstruction->destination_line }}</div>
    </div>
    @endif

    <h3>Items to Pick Up</h3>
    <table class="items-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Item Name</th>
                <th>Required Qty</th>
                <th>Actual Qty</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workInstruction->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->pivot->required_quantity }}</td>
                <td>{{ $item->pivot->actual_quantity ?? '-' }}</td>
                <td>
                    @if($item->pivot->status)
                        <span class="status-badge status-{{ $item->pivot->status }}">
                            {{ str_replace('_', ' ', $item->pivot->status) }}
                        </span>
                    @else
                        <span class="status-badge status-pending">Pending</span>
                    @endif
                </td>
                <td>{{ $item->pivot->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($workInstruction->dropoff_notes)
    <div class="notes-section">
        <div class="notes-title">Drop-off Notes:</div>
        <div>{{ $workInstruction->dropoff_notes }}</div>
    </div>
    @endif

    <div class="summary-section">
        <div class="summary-title">Summary</div>
        <div class="info-row">
            <div class="info-label">Total Items:</div>
            <div class="info-value">{{ $workInstruction->items->count() }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Completed Items:</div>
            <div class="info-value">{{ $workInstruction->items->where('pivot.status', 'completed')->count() }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Progress:</div>
            <div class="info-value">{{ $workInstruction->calculateProgressPercentage() }}%</div>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: {{ $workInstruction->calculateProgressPercentage() }}%"></div>
        </div>
        @if($workInstruction->statusProgress)
        <div class="info-row">
            <div class="info-label">Last Updated:</div>
            <div class="info-value">{{ $workInstruction->statusProgress->status_updated_at->format('d/m/Y H:i') }}</div>
        </div>
        @endif
    </div>

    <!-- Validation Section -->
    <div class="validation-section">
        <table class="validation-table">
            <tr>
                <td class="validation-cell" style="width: 100%; text-align: right;">
                    <div>
                        <div class="validation-label">Disetujui oleh,</div>
                        <div class="validation-name">Hafizh Hadiawan</div>
                        <div class="validation-signature-line"></div>
                        <div class="validation-team">
                            (PV Electric Team)
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Report generated on {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Inventory Management System</p>
    </div>
</body>
</html>
