<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('admin.wi.bulk_report_title') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #2c3e50;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 100%;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            text-align: center;
            padding: 25px 20px;
            margin-bottom: 0;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .header .subtitle {
            margin: 8px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .header .meta {
            margin: 5px 0 0 0;
            font-size: 12px;
            opacity: 0.8;
        }
        
        .content {
            padding: 25px;
        }
        
        .section {
            margin-bottom: 12px;
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 12px;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin: 0 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .filters-section {
            background-color: #fef3c7;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 12px;
            border: 1px solid #f59e0b;
        }
        
        .filters-title {
            font-weight: 600;
            color: #92400e;
            margin-bottom: 6px;
            font-size: 10px;
        }
        
        .filters-grid {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .filter-item {
            display: flex;
            align-items: center;
        }
        
        .filter-label {
            font-weight: 600;
            width: 80px;
            color: #856404;
            font-size: 10px;
        }
        
        .filter-value {
            flex: 1;
            font-size: 10px;
            color: #2c3e50;
        }
        
        .stats-section {
            background-color: #eff6ff;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 12px;
            border: 1px solid #3b82f6;
        }
        
        .stats-title {
            font-size: 12px;
            font-weight: 600;
            color: #1e40af;
            margin: 0 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #3b82f6;
        }
        
        .stats-grid {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
            background: white;
            padding: 6px 8px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
            flex: 1;
            min-width: 60px;
        }
        
        .stat-number {
            font-size: 12px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 2px;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 6px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            line-height: 1;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 8px;
            background-color: white;
            border: 1px solid #e5e7eb;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #e5e7eb;
            padding: 4px 6px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #f9fafb;
            color: #374151;
            font-weight: 600;
            font-size: 6px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .data-table tr:hover {
            background-color: #e3f2fd;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }
        
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-in_progress { background-color: #dbeafe; color: #1e40af; }
        .status-completed { background-color: #dcfce7; color: #166534; }
        .status-overdue { background-color: #fef2f2; color: #991b1b; }
        .status-not_started { background-color: #f3f4f6; color: #374151; }
        
        .type-checking { background-color: #dbeafe; color: #1e40af; }
        .type-ambil { background-color: #dcfce7; color: #166534; }
        
        .progress-bar {
            width: 100%;
            height: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 3px;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
            transition: width 0.3s ease;
        }
        
        /* Validation Section */
        .validation-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #2c3e50;
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
            color: #6b7280;
            margin-bottom: 5px;
        }
        
        .validation-name {
            font-size: 10px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .validation-signature-line {
            font-size: 9px;
            color: #2c3e50;
            margin-top: 50px;
            border-top: 1px solid #2c3e50;
            padding-top: 5px;
            display: inline-block;
            min-width: 120px;
        }
        
        .validation-team {
            font-size: 8px;
            color: #6b7280;
            margin-top: 5px;
        }
        
        .footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .summary-section {
            background-color: #d4edda;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }
        
        .summary-title {
            font-weight: 600;
            color: #155724;
            margin-bottom: 8px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>WORK INSTRUCTION BULK REPORT</h1>
            <p class="subtitle">Laporan Bulk Work Instruction</p>
            <p class="meta">Generated on {{ $generatedAt->format('d/m/Y H:i:s') }}</p>
        </div>

        <div class="content">
            <!-- Filters Applied -->
            <div class="filters-section">
                <div class="filters-title">Filters Applied:</div>
                <div class="filters-grid">
                    <div class="filter-item">
                        <div class="filter-label">Date:</div>
                        <div class="filter-value">
                            @if($filters['date_from'] || $filters['date_to'])
                                {{ $filters['date_from'] ? \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') : 'All' }} - 
                                {{ $filters['date_to'] ? \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') : 'All' }}
                            @else
                                All Dates
                            @endif
                        </div>
                    </div>
                    <div class="filter-item">
                        <div class="filter-label">Type:</div>
                        <div class="filter-value">{{ ucfirst($filters['type'] ?? 'all') }}</div>
                    </div>
                    <div class="filter-item">
                        <div class="filter-label">Status:</div>
                        <div class="filter-value">{{ ucfirst($filters['status'] ?? 'all') }}</div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-section">
                <div class="stats-title">Summary Statistics</div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['total'] }}</div>
                        <div class="stat-label">Total WI</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['checking'] }}</div>
                        <div class="stat-label">Checking</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['ambil'] }}</div>
                        <div class="stat-label">Ambil</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['average_progress'] }}%</div>
                        <div class="stat-label">Avg Progress</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['completed'] }}</div>
                        <div class="stat-label">Completed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['in_progress'] }}</div>
                        <div class="stat-label">In Progress</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['pending'] }}</div>
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['overdue'] }}</div>
                        <div class="stat-label">Overdue</div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="section">
                <h2 class="section-title">Work Instruction Data</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>WI Number</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Assigned User</th>
                            <th>Deadline</th>
                            <th>Progress</th>
                            <th>Items</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workInstructions as $index => $wi)
                        <tr>
                            <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                            <td style="font-family: monospace; font-size: 8px; font-weight: 600;">{{ $wi->wi_number }}</td>
                            <td style="max-width: 100px; word-wrap: break-word;"><strong>{{ Str::limit($wi->title, 35) }}</strong></td>
                            <td>
                                <span class="badge type-{{ $wi->type }}">
                                    {{ ucfirst($wi->type) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge status-{{ $wi->status }}">
                                    {{ str_replace('_', ' ', $wi->status) }}
                                </span>
                            </td>
                            <td style="font-size: 8px;"><strong>{{ Str::limit($wi->assignedUser->name, 20) }}</strong></td>
                            <td style="font-size: 8px;">{{ $wi->deadline->format('d/m/Y') }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <span style="font-size: 8px; font-weight: 600;">{{ $wi->calculateProgressPercentage() }}%</span>
                                    <div class="progress-bar" style="width: 40px;">
                                        <div class="progress-fill" style="width: {{ $wi->calculateProgressPercentage() }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center; font-weight: 600;">{{ $wi->items->count() }}</td>
                            <td style="font-size: 8px;">{{ $wi->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($workInstructions->count() == 0)
                <div class="summary-section">
                    <div class="summary-title">No Data Found</div>
                    <div>No work instructions match the selected filters.</div>
                </div>
                @endif
            </div>
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

        <div class="footer">
            <p><strong>Inventory Management System</strong> - Work Instruction Bulk Report</p>
            <p>Generated on {{ $generatedAt->format('d/m/Y H:i:s') }} | Total Records: {{ $workInstructions->count() }}</p>
        </div>
    </div>
</body>
</html>
