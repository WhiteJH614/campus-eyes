<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Maintenance Report</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .badge-high { background: #fee2e2; color: #dc2626; }
        .badge-medium { background: #fef3c7; color: #d97706; }
        .badge-low { background: #dcfce7; color: #16a34a; }
        .info-box { background: #f8fafc; border-left: 4px solid #667eea; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0; }
        .info-row { margin: 10px 0; }
        .info-label { font-weight: bold; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { color: #1e293b; margin-top: 4px; }
        .description { background: #f1f5f9; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .button { display: inline-block; background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; color: #64748b; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 New Maintenance Report</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Campus Eye Reporting System</p>
        </div>
        
        <div class="content">
            <p>A new maintenance report has been submitted and requires attention.</p>
            
            <div style="text-align: center; margin: 20px 0;">
                <span class="badge badge-{{ strtolower($report->urgency) }}">
                    {{ $report->urgency }} Urgency
                </span>
            </div>
            
            <div class="info-box">
                <div class="info-row">
                    <div class="info-label">Report ID</div>
                    <div class="info-value">#{{ $report->id }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Submitted By</div>
                    <div class="info-value">{{ $report->reporter->name ?? 'Unknown' }} ({{ $report->reporter->email ?? '' }})</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Location</div>
                    <div class="info-value">{{ $report->room->block->block_name ?? '' }} - {{ $report->room->room_name ?? '' }} (Floor {{ $report->room->floor_number ?? '' }})</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Category</div>
                    <div class="info-value">{{ $report->category->name ?? 'Uncategorized' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Submitted At</div>
                    <div class="info-value">{{ $report->created_at->format('F d, Y \a\t h:i A') }}</div>
                </div>
            </div>
            
            <div class="info-label">Description</div>
            <div class="description">
                {{ $report->description }}
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <p style="color: #64748b; font-size: 14px;">Please log in to the Admin Dashboard to assign a technician.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from Campus Eye Maintenance Reporting System.</p>
            <p>© {{ date('Y') }} TARUMT Penang Campus</p>
        </div>
    </div>
</body>
</html>
