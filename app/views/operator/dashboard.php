<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/controllers/OperatorController.php';

requireRole('operator');
$operator = currentUser();
$schedules = OperatorController::getMySchedules($operator['id']);
$defaultScheduleId = !empty($schedules) ? (int) $schedules[0]['id'] : 0;
$samplePassenger = [
    'ticket_id' => 2048,
    'ticket_code' => 'SAMPLE-2048',
    'name' => 'Amina Yusuf',
    'email' => 'amina.yusuf@example.com',
    'phone' => '+234 801 555 0144',
    'seats' => 2,
    'status' => 'approved',
    'source' => !empty($schedules) ? $schedules[0]['source'] : 'City Terminal',
    'destination' => !empty($schedules) ? $schedules[0]['destination'] : 'Central Park',
    'departure_time' => !empty($schedules) ? $schedules[0]['departure_time'] : date('Y-m-d H:i:s', strtotime('+2 hours')),
    'bus_name' => !empty($schedules) ? $schedules[0]['bus_name'] : 'Executive Coach 12',
    'booked_at' => date('Y-m-d H:i:s', strtotime('-35 minutes')),
    'boarded_at' => null,
    'schedule_id' => $defaultScheduleId
];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator Dashboard - Book Smarter, Travel Better</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: #1f2937;
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(90deg, #e8f4f8 0%, #d4e9f7 100%);
            color: #0f1c33;
            padding: 16px 40px;
            box-shadow: 0 10px 30px rgba(30, 60, 114, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
            flex-wrap: nowrap;
            gap: 20px;
            min-height: 70px;
        }
        
        nav h2 {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
            margin: 0;
            flex-shrink: 0;
        }
        
        nav div {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: nowrap;
        }
        
        nav a {
            color: #0f1c33;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.85rem;
            position: relative;
            padding: 10px 16px;
            border-radius: 6px;
            background: rgba(0, 114, 255, 0.1);
            border: 1px solid rgba(0, 114, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: auto;
            height: 38px;
            white-space: nowrap;
            line-height: 1;
            box-sizing: border-box;
            cursor: pointer;
        }
        
        nav a:hover {
            color: #ffffff;
            background: #0072ff;
            border-color: #0072ff;
            box-shadow: 0 4px 12px rgba(0, 114, 255, 0.5);
            transform: translateY(-2px);
        }
        
        .dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px 40px;
        }

        .page-header {
            background: white;
            color: #0f1c33;
            padding: 32px;
            border-radius: 14px;
            margin-bottom: 22px;
            box-shadow: 0 2px 10px rgba(15, 28, 51, 0.08);
            border: 1px solid #e7edf5;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 28px;
        }

        .page-header div h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.3px;
            color: #0f1c33;
        }

        .page-header div p {
            font-size: 1rem;
            margin: 0;
            font-weight: 500;
            color: #5f6b7a;
            max-width: 720px;
            line-height: 1.6;
        }

        .eyebrow {
            display: inline-block;
            margin-bottom: 12px;
            padding: 8px 14px;
            border-radius: 999px;
            background: linear-gradient(90deg, #e8f4f8 0%, #d4e9f7 100%);
            color: #0f1c33;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .header-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 14px;
            min-width: 240px;
        }

        .header-note {
            font-size: 0.9rem;
            color: #667085;
            text-align: right;
            line-height: 1.5;
        }

        .overview-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .overview-card {
            background: white;
            border: 1px solid #e7edf5;
            border-radius: 12px;
            padding: 20px 22px;
            box-shadow: 0 2px 8px rgba(15, 28, 51, 0.06);
        }

        .overview-label {
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            color: #7a8696;
            margin-bottom: 8px;
        }

        .overview-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0f1c33;
            margin-bottom: 6px;
        }

        .overview-subtext {
            font-size: 0.92rem;
            color: #627083;
            line-height: 1.5;
        }

        .scan-studio {
            background: linear-gradient(135deg, #f7fafc 0%, #eef5fb 100%);
            border: 1px solid #dce7f2;
            border-radius: 18px;
            padding: 28px;
            margin-bottom: 28px;
            box-shadow: 0 12px 30px rgba(15, 28, 51, 0.07);
        }

        .scan-studio-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 22px;
        }

        .scan-studio-title h3 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #10233c;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .scan-studio-title p {
            color: #5f6f82;
            line-height: 1.6;
            max-width: 760px;
        }

        .scan-studio-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .studio-link,
        .studio-btn {
            border-radius: 10px;
            padding: 11px 16px;
            font-weight: 700;
            font-size: 0.92rem;
            border: 1px solid #cdddff;
            background: white;
            color: #15396b;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .studio-btn.primary {
            background: #0f172a;
            color: white;
            border-color: #0f172a;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.18);
        }

        .studio-link:hover,
        .studio-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(15, 28, 51, 0.08);
        }

        .scan-studio-grid {
            display: grid;
            grid-template-columns: minmax(320px, 420px) minmax(0, 1fr);
            gap: 22px;
            align-items: stretch;
        }

        .scanner-panel,
        .passenger-panel {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(213, 225, 238, 0.9);
            border-radius: 16px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }

        .scan-controls {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto auto;
            gap: 10px;
            align-items: end;
            margin-bottom: 16px;
        }

        .field-stack {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field-stack label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #627083;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .schedule-select {
            width: 100%;
            border: 1px solid #cfdbeb;
            border-radius: 10px;
            padding: 13px 14px;
            font-size: 0.95rem;
            color: #10233c;
            background: #fff;
        }

        .schedule-select:focus {
            outline: none;
            border-color: #7aa2ff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        }

        .scanner-stage {
            position: relative;
            min-height: 336px;
            border-radius: 16px;
            overflow: hidden;
            background: radial-gradient(circle at top, #1e293b 0%, #0f172a 60%, #020617 100%);
            border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .scanner-stage video {
            width: 100%;
            height: 336px;
            object-fit: cover;
            display: none;
        }

        .scanner-stage video.active {
            display: block;
        }

        .scanner-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            color: rgba(255, 255, 255, 0.92);
            text-align: center;
            padding: 24px;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.22) 0%, rgba(15, 23, 42, 0.66) 100%);
        }

        .scanner-overlay.hidden {
            display: none;
        }

        .scanner-frame {
            width: 210px;
            height: 210px;
            border: 2px solid rgba(255, 255, 255, 0.92);
            border-radius: 22px;
            box-shadow: 0 0 0 999px rgba(2, 6, 23, 0.28);
        }

        .camera-pill {
            position: absolute;
            top: 16px;
            left: 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(15, 23, 42, 0.72);
            color: #e2e8f0;
            padding: 9px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .camera-pill.live::before,
        .camera-pill::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #f59e0b;
        }

        .camera-pill.live::before {
            background: #22c55e;
            box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.14);
        }

        .scan-message {
            margin-top: 14px;
            padding: 14px 16px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.92rem;
            line-height: 1.5;
            border: 1px solid transparent;
        }

        .scan-message.info {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .scan-message.success {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }

        .scan-message.error {
            background: #fff1f2;
            color: #be123c;
            border-color: #fecdd3;
        }

        .passenger-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
        }

        .passenger-panel-head h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #10233c;
            margin-bottom: 6px;
        }

        .passenger-panel-head p {
            color: #64748b;
            line-height: 1.5;
            font-size: 0.93rem;
        }

        .panel-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #6b7b90;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .passenger-placeholder {
            border: 1px dashed #d6e3f1;
            border-radius: 16px;
            padding: 26px;
            background: linear-gradient(180deg, rgba(248, 250, 252, 0.95) 0%, rgba(255, 255, 255, 1) 100%);
        }

        .passenger-shell {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .passenger-summary {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            padding: 18px 20px;
            border-radius: 16px;
            background: linear-gradient(135deg, #0f172a 0%, #16304f 100%);
            color: white;
        }

        .passenger-summary h4 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .passenger-summary p {
            color: rgba(226, 232, 240, 0.86);
            line-height: 1.6;
            max-width: 620px;
        }

        .ticket-token {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            font-weight: 700;
            color: #f8fafc;
            white-space: nowrap;
        }

        .sample-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(251, 191, 36, 0.18);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.3);
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.35px;
        }

        .passenger-topline {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .mini-stat {
            border-radius: 14px;
            border: 1px solid #dfebf5;
            background: #f9fbfe;
            padding: 16px 18px;
        }

        .mini-stat label,
        .detail-table-row label {
            display: block;
            color: #7b8796;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: 700;
            font-size: 0.75rem;
            margin-bottom: 8px;
        }

        .mini-stat strong {
            display: block;
            color: #10233c;
            font-size: 1.02rem;
            font-weight: 700;
            line-height: 1.45;
        }

        .detail-table {
            border: 1px solid #e2ebf4;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
        }

        .detail-table-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .detail-table-row {
            padding: 16px 18px;
            border-bottom: 1px solid #ecf1f6;
            min-height: 92px;
        }

        .detail-table-row:nth-child(odd) {
            border-right: 1px solid #ecf1f6;
        }

        .detail-table-row:nth-last-child(-n+2) {
            border-bottom: none;
        }

        .detail-table-row span {
            color: #10233c;
            font-weight: 600;
            line-height: 1.6;
            word-break: break-word;
        }

        .detail-table-row span.muted {
            color: #637489;
        }

        .status-inline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 999px;
            padding: 9px 12px;
            font-weight: 700;
            font-size: 0.82rem;
        }

        .status-inline.pending {
            background: #fff7ed;
            color: #c2410c;
        }

        .status-inline.approved {
            background: #ecfdf5;
            color: #047857;
        }

        .status-inline.boarded {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .status-inline.cancelled {
            background: #fff1f2;
            color: #be123c;
        }

        .boarding-note {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e2ebf4;
            color: #475569;
            line-height: 1.5;
        }

        .boarding-note strong {
            color: #10233c;
            display: block;
            margin-bottom: 2px;
        }

        .scan-btn-inline {
            background: #f8fafc;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            white-space: nowrap;
        }

        .scan-btn-inline.primary {
            background: #0f172a;
            color: #fff;
            border-color: #0f172a;
        }

        .scan-btn-inline:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(15, 28, 51, 0.08);
        }
        
        .scan-btn-hero {
            background: #0072ff;
            color: white;
            border: none;
            padding: 14px 26px;
            border-radius: 8px;
            font-size: 0.98rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 16px rgba(0, 114, 255, 0.2);
            white-space: nowrap;
            letter-spacing: 0.2px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .scan-btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 114, 255, 0.24);
            background: #005fd6;
        }
        
        .scan-btn-hero:active {
            transform: translateY(-1px);
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: white;
            color: #0f1c33;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid #d7e3f0;
            margin-bottom: 18px;
            font-size: 0.92rem;
            box-shadow: 0 2px 8px rgba(15, 28, 51, 0.06);
        }

        .back-btn:hover {
            background: #f8fbff;
            border-color: #b8d6ff;
            transform: translateY(-1px);
        }
        
        .workflow-guide {
            background: #ffffff;
            border: 1px solid #e7edf5;
            padding: 18px 22px;
            border-radius: 12px;
            margin-bottom: 24px;
            color: #425164;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(15, 28, 51, 0.05);
        }
        
        .workflow-guide::before {
            content: "i";
            font-size: 1rem;
            font-weight: 700;
            background: #0072ff;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .operator-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
            margin: 20px 0;
        }
        
        .schedule-card {
            background: white;
            border: 1px solid #e4ebf3;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(15, 28, 51, 0.07);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .schedule-card:hover {
            box-shadow: 0 8px 24px rgba(15, 28, 51, 0.1);
            transform: translateY(-2px);
            border-color: #cfe0f2;
        }
        
        .schedule-header {
            background: linear-gradient(90deg, #f9fbfd 0%, #f2f7fb 100%);
            color: #0f1c33;
            padding: 24px 28px;
            display: grid;
            grid-template-columns: 1fr 1.2fr auto;
            gap: 24px;
            align-items: center;
            border-bottom: 1px solid #e7edf5;
        }
        
        .schedule-route {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: -0.2px;
        }
        
        .route-separator {
            color: #7f90a5;
            font-weight: 700;
            font-size: 1.05rem;
        }
        
        .schedule-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            font-size: 0.95rem;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .info-label {
            opacity: 1;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #7a8696;
        }
        
        .info-value {
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: -0.2px;
            color: #0f1c33;
        }
        
        .status-badge {
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            border: none;
            letter-spacing: 0.4px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            justify-self: end;
        }
        
        .status-scheduled {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-boarding {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-departed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #7f1d1d;
        }
        
        .schedule-content {
            padding: 28px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }
        
        .stat-box {
            background: white;
            padding: 22px 18px;
            border-radius: 12px;
            text-align: left;
            border: 1px solid #edf2f7;
            transition: all 0.25s ease;
            position: relative;
        }
        
        .stat-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 28, 51, 0.08);
            border-color: #dfe9f4;
        }
        
        .stat-kicker {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.45px;
            text-transform: uppercase;
            color: #7b8796;
            margin-bottom: 12px;
        }
        
        .stat-number {
            font-size: 1.85rem;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }
        
        .stat-label {
            font-size: 0.82rem;
            color: #6b7280;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        
        .stat-box.total { border-left: 4px solid #3b82f6; }
        .stat-box.total .stat-number { color: #3b82f6; }
        
        .stat-box.pending { border-left: 4px solid #f59e0b; }
        .stat-box.pending .stat-number { color: #f59e0b; }
        
        .stat-box.ready { border-left: 4px solid #10b981; }
        .stat-box.ready .stat-number { color: #10b981; }
        
        .stat-box.cancelled { border-left: 4px solid #ef4444; }
        .stat-box.cancelled .stat-number { color: #ef4444; }
        
        .quick-actions {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
            padding-bottom: 24px;
            border-bottom: 1px solid #edf2f7;
        }
        
        .quick-btn {
            padding: 12px 22px;
            border: 1px solid #d8e3ef;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #0f1c33;
            box-shadow: 0 2px 6px rgba(15, 28, 51, 0.04);
            text-decoration: none;
        }
        
        .quick-btn:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .quick-btn.primary {
            background: #0072ff;
            color: white;
            border-color: #2563eb;
            box-shadow: 0 4px 14px rgba(0, 114, 255, 0.2);
        }
        
        .quick-btn.primary:hover {
            background: #005fd6;
            border-color: #005fd6;
            box-shadow: 0 8px 20px rgba(0, 114, 255, 0.24);
        }
        
        .section {
            margin-bottom: 24px;
            background: #fbfcfe;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 22px;
        }
        
        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f1c33;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #dbe6f2;
            letter-spacing: -0.2px;
        }
        
        .passenger-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }
        
        .passenger-table thead {
            background: #f4f7fb;
            border-bottom: 2px solid #e5edf5;
        }
        
        .passenger-table th {
            padding: 16px 14px;
            text-align: left;
            font-weight: 700;
            color: #374151;
            letter-spacing: 0.3px;
            font-size: 0.9rem;
            text-transform: capitalize;
        }
        
        .passenger-table td {
            padding: 16px 14px;
            border-bottom: 1px solid #edf2f7;
            color: #4b5563;
        }
        
        .passenger-table tr:hover {
            background: #f9fbfd;
        }
        
        .action-link {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(15, 28, 51, 0.06);
        }
        
        .btn-approve {
            background: #0f9f6e;
            color: white;
        }
        
        .btn-approve:hover {
            background: #0a845b;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 40px;
            color: #7a8696;
            background: white;
            border-radius: 12px;
            border: 1px dashed #dbe6f2;
        }
        
        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 16px;
            opacity: 0.7;
        }
        
        .empty-state h3 {
            color: #374151;
            margin-bottom: 10px;
            font-size: 1.05rem;
            font-weight: 700;
        }
        
        .empty-state p {
            color: #9ca3af;
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .schedule-header { grid-template-columns: 1fr; gap: 12px; }
            .page-header { flex-direction: column; align-items: flex-start; }
            .header-actions { width: 100%; align-items: flex-start; }
            .header-note { text-align: left; }
            .overview-strip { grid-template-columns: 1fr; }
            .scan-studio-grid { grid-template-columns: 1fr; }
            .scan-controls { grid-template-columns: 1fr; }
            .passenger-topline { grid-template-columns: 1fr; }
            .detail-table-grid { grid-template-columns: 1fr; }
            .detail-table-row:nth-child(odd) { border-right: none; }
            .detail-table-row:nth-last-child(-n+2) { border-bottom: 1px solid #ecf1f6; }
            .detail-table-row:last-child { border-bottom: none; }
            nav { padding: 14px 30px; }
        }
        
        @media (max-width: 768px) {
            nav { 
                padding: 12px 16px; 
                flex-direction: column;
                gap: 12px;
            }
            nav h2 { 
                font-size: 1.2rem; 
                width: 100%;
                text-align: center;
            }
            nav div {
                width: 100%;
                flex-wrap: wrap;
                justify-content: center;
            }
            nav a { 
                padding: 8px 12px; 
                font-size: 0.85rem; 
                flex: 1;
                min-width: 100px;
            }
            
            .dashboard { padding: 16px 12px; }
            .page-header {
                padding: 24px 20px;
                flex-direction: column;
                gap: 16px;
            }
            .page-header div h2 { 
                font-size: 1.65rem;
            }

            .scan-studio {
                padding: 22px 18px;
            }

            .scan-studio-header,
            .passenger-panel-head,
            .boarding-note,
            .passenger-summary {
                flex-direction: column;
                align-items: flex-start;
            }

            .scan-studio-actions {
                width: 100%;
            }

            .studio-link,
            .studio-btn,
            .scan-btn-inline {
                width: 100%;
            }

            .scanner-stage,
            .scanner-stage video {
                min-height: 280px;
                height: 280px;
            }
            
            .scan-btn-hero {
                padding: 14px 24px;
                font-size: 1rem;
                width: 100%;
                justify-content: center;
            }
            
            .stats-grid { 
                grid-template-columns: repeat(2, 1fr); 
                gap: 12px; 
            }
            .stat-box { 
                padding: 18px; 
            }
            .stat-number { 
                font-size: 1.6rem; 
            }
            .schedule-header { 
                padding: 16px; 
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .schedule-content {
                padding: 20px;
            }

            .section {
                padding: 18px;
            }
            
            .passenger-table { 
                font-size: 0.85rem; 
            }
            .passenger-table th, .passenger-table td { 
                padding: 12px 8px; 
            }
            
            .quick-actions { 
                flex-direction: column;
                gap: 10px;
            }
            .quick-btn { 
                width: 100%; 
            }
        }
    </style>
</head>
<body>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">??</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>operator/dashboard">Dashboard</a>
        <a href="<?php echo BASE_URL; ?>operator/add-schedule">Add Schedule</a>
        <a href="<?php echo BASE_URL; ?>operator/schedules">My Schedules</a>
        <a href="<?php echo BASE_URL; ?>">Home</a>
        <a href="<?php echo BASE_URL; ?>logout">Logout</a>
    </div>
</nav>

<div class="dashboard">
    <a href="javascript:history.back()" class="back-btn">? Back</a>

    <div class="workflow-guide">
        Daily workflow: review assigned trips, clear pending payments, then move passengers through boarding in sequence.
    </div>

    <div class="page-header">
        <div>
            <span class="eyebrow">Operator Operations</span>
            <h2>Operator Dashboard</h2>
            <p>Monitor assigned schedules, keep payment approvals moving, and prepare each departure with a clear view of passenger status.</p>
        </div>
        <div class="header-actions">
            <button class="scan-btn-hero" onclick="document.getElementById('scanStudio').scrollIntoView({ behavior: 'smooth', block: 'start' });">Launch Scan Workspace</button>
            <div class="header-note">Signed in as <?php echo htmlspecialchars($operator['name'] ?? 'Operator'); ?>. Use the scanner when passengers arrive at the gate.</div>
        </div>
    </div>

    <div class="overview-strip">
        <div class="overview-card">
            <div class="overview-label">Assigned Schedules</div>
            <div class="overview-value"><?php echo count($schedules); ?></div>
            <div class="overview-subtext">Trips currently linked to your operator account.</div>
        </div>
        <div class="overview-card">
            <div class="overview-label">Primary Focus</div>
            <div class="overview-value"><?php echo count($schedules) > 0 ? 'Boarding' : 'Awaiting Trips'; ?></div>
            <div class="overview-subtext"><?php echo count($schedules) > 0 ? 'Review payments first, then move approved passengers to boarding.' : 'No assigned departures are available right now.'; ?></div>
        </div>
        <div class="overview-card">
            <div class="overview-label">Navigation</div>
            <div class="overview-value">Quick Access</div>
            <div class="overview-subtext">Use schedule cards below for trip-level actions and passenger review.</div>
        </div>
    </div>

    <?php if (count($schedules) > 0): ?>
        <section class="scan-studio" id="scanStudio">
            <div class="scan-studio-header">
                <div class="scan-studio-title">
                    <h3>Passenger Scan Workspace</h3>
                    <p>Scan the QR code on a passenger ticket and review all booking details in a clean operator-ready layout. The details panel starts with a sample scanned passenger so the team can test the UI before using the live camera.</p>
                </div>
                <div class="scan-studio-actions">
                    <button type="button" class="studio-btn" id="loadSampleBtn">Show Sample Passenger</button>
                    <a href="<?php echo BASE_URL; ?>operator/boarding?schedule_id=<?php echo $defaultScheduleId; ?>" class="studio-link">Open Dedicated Boarding Page</a>
                </div>
            </div>

            <div class="scan-studio-grid">
                <div class="scanner-panel">
                    <div class="scan-controls">
                        <div class="field-stack">
                            <label for="scanScheduleId">Active Schedule</label>
                            <select id="scanScheduleId" class="schedule-select">
                                <?php foreach ($schedules as $schedule): ?>
                                    <option value="<?php echo (int) $schedule['id']; ?>" <?php echo (int) $schedule['id'] === $defaultScheduleId ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($schedule['source'] . ' ? ' . $schedule['destination'] . ' • ' . date('M d, H:i', strtotime($schedule['departure_time']))); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="button" class="scan-btn-inline primary" id="startScanBtn">Start Camera</button>
                        <button type="button" class="scan-btn-inline" id="stopScanBtn">Stop</button>
                    </div>

                    <div class="scanner-stage">
                        <div class="camera-pill" id="cameraStatus">Camera Offline</div>
                        <video id="scannerVideo" playsinline muted></video>
                        <canvas id="scannerCanvas" hidden></canvas>
                        <div class="scanner-overlay" id="scannerOverlay">
                            <div class="scanner-frame"></div>
                            <div>
                                <strong>Position the QR code inside the frame</strong>
                                <div style="margin-top: 6px; color: rgba(226, 232, 240, 0.82);">The system will stop automatically after a valid passenger ticket is detected.</div>
                            </div>
                        </div>
                    </div>

                    <div class="scan-message info" id="scanMessage">Camera is offline. Choose a schedule, then start scanning to load passenger details.</div>
                </div>

                <div class="passenger-panel">
                    <div class="passenger-panel-head">
                        <div>
                            <div class="panel-kicker">Scanned Passenger</div>
                            <h3>Passenger Details Board</h3>
                            <p>Live scan results appear here in cards and structured detail rows, with route, ticket, payment, and departure information visible at a glance.</p>
                        </div>
                    </div>

                    <div id="passengerDetails" class="passenger-placeholder"></div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (count($schedules) > 0): ?>
        <div class="operator-container">
            <?php foreach ($schedules as $schedule): 
                $summary = OperatorController::getTripSummary($schedule['id']);
                $pendingPayments = OperatorController::getPendingPayments($schedule['id']);
                $boardedPassengers = OperatorController::getBoardedPassengers($schedule['id']);
                $tripStatus = strtolower($schedule['status'] ?? 'scheduled');
            ?>
                <div class="schedule-card">
                    <div class="schedule-header">
                        <div class="schedule-route">
                            <span><?php echo htmlspecialchars($schedule['source']); ?></span>
                            <span class="route-separator">?</span>
                            <span><?php echo htmlspecialchars($schedule['destination']); ?></span>
                        </div>
                        <div class="schedule-info">
                            <div class="info-item">
                                <span class="info-label">Bus</span>
                                <span class="info-value"><?php echo htmlspecialchars($schedule['bus_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Departure</span>
                                <span class="info-value"><?php echo date('M d, H:i', strtotime($schedule['departure_time'])); ?></span>
                            </div>
                        </div>
                        <span class="status-badge status-<?php echo $tripStatus; ?>">
                            <?php 
                            $statusText = match($tripStatus) {
                                'boarding' => 'Boarding',
                                'departed' => 'Departed',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                default => 'Scheduled'
                            };
                            echo $statusText;
                            ?>
                        </span>
                    </div>

                    <div class="schedule-content">
                        <!-- STATS GRID -->
                        <div class="stats-grid">
                            <div class="stat-box total">
                                <div class="stat-kicker">Bookings</div>
                                <div class="stat-number"><?php echo ($summary['total_tickets'] ?? 0); ?></div>
                                <div class="stat-label">Total Bookings</div>
                            </div>
                            <div class="stat-box pending">
                                <div class="stat-kicker">Payments</div>
                                <div class="stat-number"><?php echo ($summary['pending_count'] ?? 0); ?></div>
                                <div class="stat-label">Pending Payments</div>
                            </div>
                            <div class="stat-box ready">
                                <div class="stat-kicker">Queue</div>
                                <div class="stat-number"><?php echo ($summary['approved_count'] ?? 0); ?></div>
                                <div class="stat-label">Ready to Board</div>
                            </div>
                            <div class="stat-box cancelled">
                                <div class="stat-kicker">Status</div>
                                <div class="stat-number"><?php echo ($summary['cancelled_count'] ?? 0); ?></div>
                                <div class="stat-label">Cancelled</div>
                            </div>
                        </div>

                        <!-- QUICK ACTIONS -->
                        <div class="quick-actions">
                            <a href="<?php echo BASE_URL; ?>operator/boarding?schedule_id=<?php echo $schedule['id']; ?>" class="quick-btn primary">Open Boarding and Payments</a>
                            <a href="<?php echo BASE_URL; ?>operator/schedules?id=<?php echo $schedule['id']; ?>" class="quick-btn">View Schedule Details</a>
                        </div>

                        <div class="section">
                            <div class="section-title">Pending Payments (<?php echo count($pendingPayments); ?>)</div>
                            <?php if (count($pendingPayments) > 0): ?>
                                <table class="passenger-table">
                                    <thead>
                                        <tr>
                                            <th>Passenger</th>
                                            <th>Seats</th>
                                            <th>Booked On</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingPayments as $payment): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($payment['name']); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($payment['phone']); ?></small>
                                                </td>
                                                <td><?php echo intval($payment['seats']); ?> Seat(s)</td>
                                                <td><?php echo date('M d, H:i', strtotime($payment['created_at'])); ?></td>
                                                <td>
                                                    <form method="POST" action="<?php echo BASE_URL; ?>operator/process-trip" style="display: inline;">
                                                        <input type="hidden" name="action" value="approve_payment">
                                                        <input type="hidden" name="ticket_id" value="<?php echo $payment['id']; ?>">
                                                        <input type="hidden" name="schedule_id" value="<?php echo $schedule['id']; ?>">
                                                        <button type="submit" class="action-link btn-approve">Approve Payment</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-state-icon">•</div>
                                    <h3>No Pending Payments</h3>
                                    <p>All current passengers are cleared, or new arrivals have not been scanned yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="section">
                            <div class="section-title">Ready to Board (<?php echo $summary['approved_count'] ?? 0; ?>)</div>
                            <?php 
                            $approved = $conn->query("
                                SELECT t.id, t.seats, u.name, u.phone FROM tickets t
                                LEFT JOIN users u ON t.user_id = u.id
                                WHERE t.schedule_id = {$schedule['id']} AND t.status = 'approved'
                                ORDER BY t.created_at ASC
                            ");
                            ?>
                            <?php if ($approved && $approved->num_rows > 0): ?>
                                <table class="passenger-table">
                                    <thead>
                                        <tr>
                                            <th>Passenger</th>
                                            <th>Seats</th>
                                            <th>Boarding</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($passenger = $approved->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($passenger['name']); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($passenger['phone']); ?></small>
                                                </td>
                                                <td><?php echo intval($passenger['seats']); ?> Seat(s)</td>
                                                <td>
                                                    <form method="POST" action="<?php echo BASE_URL; ?>operator/process-trip" style="display: inline;">
                                                        <input type="hidden" name="action" value="board_passenger">
                                                        <input type="hidden" name="ticket_id" value="<?php echo $passenger['id']; ?>">
                                                        <input type="hidden" name="schedule_id" value="<?php echo $schedule['id']; ?>">
                                                        <button type="submit" class="action-link btn-approve">Mark Boarded</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-state-icon">•</div>
                                    <h3>No Passengers Ready</h3>
                                    <p>Approve payments first to move passengers into the boarding queue.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="section">
                            <div class="section-title">Boarded Passengers (<?php echo count($boardedPassengers); ?>)</div>
                            <?php if (count($boardedPassengers) > 0): ?>
                                <table class="passenger-table">
                                    <thead>
                                        <tr>
                                            <th>Passenger</th>
                                            <th>Seats</th>
                                            <th>Recorded At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($boardedPassengers as $boarded): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($boarded['name']); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($boarded['phone']); ?></small>
                                                </td>
                                                <td><?php echo intval($boarded['seats']); ?> Seat(s)</td>
                                                <td><?php echo date('M d, H:i', strtotime($boarded['created_at'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-state-icon">•</div>
                                    <h3>No Boarded Passengers Yet</h3>
                                    <p>Passengers marked as boarded will appear here once processing begins.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="background: white; padding: 52px 40px; border-radius: 14px; text-align: center; box-shadow: 0 2px 10px rgba(15,28,51,0.08); border: 1px solid #e7edf5;">
            <h3 style="font-size: 1.35rem; color: #0f1c33; margin-bottom: 12px;">No Scheduled Trips</h3>
            <p style="color: #667085; margin-bottom: 22px; font-size: 1rem;">There are no active schedules assigned to your account yet. You can create a schedule or request assignment from an administrator.</p>
            <a href="<?php echo BASE_URL; ?>operator/add-schedule" class="quick-btn primary" style="padding: 12px 24px; display: inline-flex;">Create Schedule</a>
        </div>
    <?php endif; ?>
</div>

<?php if (count($schedules) > 0): ?>
<script>
    const dashboardSamplePassenger = <?php echo json_encode($samplePassenger, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
    let selectedScheduleId = <?php echo json_encode($defaultScheduleId); ?>;
    let scannerStream = null;
    let scannerActive = false;
    let scannerFrame = null;
    let lastScannedValue = null;
    let lastScannedAt = 0;

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDateTime(value) {
        if (!value) {
            return 'Not available';
        }

        const parsed = new Date(String(value).replace(' ', 'T'));
        if (Number.isNaN(parsed.getTime())) {
            return value;
        }

        return parsed.toLocaleString([], {
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function getStatusMeta(status) {
        const normalized = String(status || '').toLowerCase();
        if (normalized === 'pending') {
            return { label: 'Payment Pending', className: 'pending', note: 'Collect payment before boarding.' };
        }
        if (normalized === 'approved') {
            return { label: 'Ready to Board', className: 'approved', note: 'Passenger can proceed to boarding.' };
        }
        if (normalized === 'boarded') {
            return { label: 'Boarded', className: 'boarded', note: 'Passenger has already been processed.' };
        }
        if (normalized === 'cancelled') {
            return { label: 'Cancelled', className: 'cancelled', note: 'This booking should not be processed.' };
        }
        return { label: 'Unknown', className: 'pending', note: 'Verify this booking before continuing.' };
    }

    function setCameraStatus(label, isLive) {
        const cameraStatus = document.getElementById('cameraStatus');
        if (!cameraStatus) {
            return;
        }

        cameraStatus.textContent = label;
        cameraStatus.classList.toggle('live', Boolean(isLive));
    }

    function setScanMessage(message, tone) {
        const scanMessage = document.getElementById('scanMessage');
        if (!scanMessage) {
            return;
        }

        scanMessage.className = 'scan-message ' + (tone || 'info');
        scanMessage.textContent = message;
    }

    function extractTicketIdFromQRCode(rawValue) {
        if (!rawValue) {
            return null;
        }

        const trimmedValue = String(rawValue).trim();

        if (/^\d+$/.test(trimmedValue)) {
            return parseInt(trimmedValue, 10);
        }

        const ticketIdMatch = trimmedValue.match(/[?&]ticket_id=(\d+)/i);
        if (ticketIdMatch) {
            return parseInt(ticketIdMatch[1], 10);
        }

        const directIdMatch = trimmedValue.match(/ticket[_-]?id[=:/\s]+(\d+)/i);
        if (directIdMatch) {
            return parseInt(directIdMatch[1], 10);
        }

        return null;
    }

    function renderPassengerDetails(passenger, options = {}) {
        const container = document.getElementById('passengerDetails');
        if (!container) {
            return;
        }

        const sample = Boolean(options.sample);
        const statusMeta = getStatusMeta(passenger.status);
        const routeText = escapeHtml((passenger.source || 'N/A') + ' ? ' + (passenger.destination || 'N/A'));
        const ticketCode = escapeHtml(passenger.ticket_code || ('TICKET-' + passenger.ticket_id));
        const operatorNote = sample
            ? 'This is a sample scanned passenger card used to test the layout before live boarding begins.'
            : 'Live ticket match loaded from the scanned QR code. Confirm the passenger identity and proceed using the trip controls below.';

        container.className = 'passenger-shell';
        container.innerHTML = `
            <div class="passenger-summary">
                <div>
                    ${sample ? '<div class="sample-chip">Sample Test Scanned Passenger</div>' : ''}
                    <h4>${escapeHtml(passenger.name || 'Passenger')}</h4>
                    <p>${operatorNote}</p>
                </div>
                <div class="ticket-token">${ticketCode}</div>
            </div>

            <div class="passenger-topline">
                <div class="mini-stat">
                    <label>Route</label>
                    <strong>${routeText}</strong>
                </div>
                <div class="mini-stat">
                    <label>Assigned Bus</label>
                    <strong>${escapeHtml(passenger.bus_name || 'Not assigned')}</strong>
                </div>
                <div class="mini-stat">
                    <label>Departure</label>
                    <strong>${escapeHtml(formatDateTime(passenger.departure_time))}</strong>
                </div>
            </div>

            <div class="detail-table">
                <div class="detail-table-grid">
                    <div class="detail-table-row">
                        <label>Status</label>
                        <span><span class="status-inline ${statusMeta.className}">${statusMeta.label}</span></span>
                    </div>
                    <div class="detail-table-row">
                        <label>Seat Count</label>
                        <span>${escapeHtml(passenger.seats)} seat(s)</span>
                    </div>
                    <div class="detail-table-row">
                        <label>Phone Number</label>
                        <span>${escapeHtml(passenger.phone || 'N/A')}</span>
                    </div>
                    <div class="detail-table-row">
                        <label>Email Address</label>
                        <span class="muted">${escapeHtml(passenger.email || 'N/A')}</span>
                    </div>
                    <div class="detail-table-row">
                        <label>Booked At</label>
                        <span class="muted">${escapeHtml(formatDateTime(passenger.booked_at))}</span>
                    </div>
                    <div class="detail-table-row">
                        <label>Boarded At</label>
                        <span class="muted">${escapeHtml(formatDateTime(passenger.boarded_at))}</span>
                    </div>
                </div>
            </div>

            <div class="boarding-note">
                <div>
                    <strong>Operator Guidance</strong>
                    <span>${escapeHtml(statusMeta.note)}</span>
                </div>
                <a class="studio-link" href="boarding.php?schedule_id=${encodeURIComponent(passenger.schedule_id || selectedScheduleId)}">Open Full Boarding Controls</a>
            </div>
        `;
    }

    function updateScanButtons() {
        const startButton = document.getElementById('startScanBtn');
        const stopButton = document.getElementById('stopScanBtn');
        if (startButton) {
            startButton.disabled = scannerActive;
            startButton.style.opacity = scannerActive ? '0.7' : '1';
            startButton.style.cursor = scannerActive ? 'not-allowed' : 'pointer';
        }
        if (stopButton) {
            stopButton.disabled = !scannerActive;
            stopButton.style.opacity = !scannerActive ? '0.7' : '1';
            stopButton.style.cursor = !scannerActive ? 'not-allowed' : 'pointer';
        }
    }

    async function startScanner() {
        if (scannerActive) {
            return;
        }

        if (!selectedScheduleId) {
            setScanMessage('Select an active schedule before scanning a passenger ticket.', 'error');
            return;
        }

        const video = document.getElementById('scannerVideo');
        const overlay = document.getElementById('scannerOverlay');

        if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
            setScanMessage('Camera access is not available in this browser.', 'error');
            return;
        }

        try {
            scannerStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' },
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            });

            video.srcObject = scannerStream;
            await video.play();

            scannerActive = true;
            overlay.classList.add('hidden');
            video.classList.add('active');
            setCameraStatus('Camera Live', true);
            setScanMessage('Scanning in progress. Hold the passenger ticket steady inside the frame.', 'success');
            updateScanButtons();
            scanFrame();
        } catch (error) {
            console.error(error);
            setScanMessage('Unable to start the camera. Check browser permission and try again.', 'error');
            setCameraStatus('Camera Blocked', false);
        }
    }

    function stopScanner() {
        const video = document.getElementById('scannerVideo');
        const overlay = document.getElementById('scannerOverlay');

        if (scannerFrame) {
            cancelAnimationFrame(scannerFrame);
            scannerFrame = null;
        }

        if (scannerStream) {
            scannerStream.getTracks().forEach(track => track.stop());
            scannerStream = null;
        }

        if (video) {
            video.pause();
            video.srcObject = null;
            video.classList.remove('active');
        }

        if (overlay) {
            overlay.classList.remove('hidden');
        }

        scannerActive = false;
        setCameraStatus('Camera Offline', false);
        updateScanButtons();
    }

    function scanFrame() {
        if (!scannerActive) {
            return;
        }

        const video = document.getElementById('scannerVideo');
        const canvas = document.getElementById('scannerCanvas');

        if (!video || !canvas || video.readyState !== video.HAVE_ENOUGH_DATA) {
            scannerFrame = requestAnimationFrame(scanFrame);
            return;
        }

        const context = canvas.getContext('2d', { willReadFrequently: true });
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code && code.data) {
            const now = Date.now();
            if (code.data === lastScannedValue && now - lastScannedAt < 1800) {
                scannerFrame = requestAnimationFrame(scanFrame);
                return;
            }

            lastScannedValue = code.data;
            lastScannedAt = now;

            const ticketId = extractTicketIdFromQRCode(code.data);
            if (ticketId) {
                setScanMessage('Passenger ticket detected. Loading details for ticket #' + ticketId + '.', 'success');
                stopScanner();
                fetchPassengerDetails(ticketId);
                return;
            }

            setScanMessage('A QR code was detected, but it does not contain a valid passenger ticket ID.', 'error');
        }

        scannerFrame = requestAnimationFrame(scanFrame);
    }

    function fetchPassengerDetails(ticketId) {
        setScanMessage('Loading passenger details for ticket #' + ticketId + '...', 'info');

        fetch(BASE_URL + 'operator/get-passenger', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                ticket_id: String(ticketId),
                schedule_id: String(selectedScheduleId)
            }).toString()
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('The passenger details request could not be completed.');
            }
            return response.json();
        })
        .then(data => {
            if (!data.success || !data.passenger) {
                throw new Error(data.message || 'Passenger not found for the selected schedule.');
            }

            renderPassengerDetails(data.passenger, { sample: false });
            setScanMessage(data.message || 'Passenger details loaded successfully.', 'success');
        })
        .catch(error => {
            console.error(error);
            setScanMessage(error.message || 'Passenger details could not be loaded.', 'error');
        });
    }

    document.getElementById('scanScheduleId').addEventListener('change', function () {
        selectedScheduleId = parseInt(this.value, 10) || 0;
        setScanMessage('Schedule updated. Start scanning when the next passenger arrives.', 'info');
    });

    document.getElementById('startScanBtn').addEventListener('click', startScanner);
    document.getElementById('stopScanBtn').addEventListener('click', function () {
        stopScanner();
        setScanMessage('Camera stopped. Start scanning again when you are ready.', 'info');
    });
    document.getElementById('loadSampleBtn').addEventListener('click', function () {
        renderPassengerDetails(dashboardSamplePassenger, { sample: true });
        setScanMessage('Sample test passenger loaded. This preview will be replaced after a live scan.', 'info');
    });

    renderPassengerDetails(dashboardSamplePassenger, { sample: true });
    updateScanButtons();
    setCameraStatus('Camera Offline', false);
    window.addEventListener('beforeunload', stopScanner);
</script>
<?php endif; ?>

<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>
