<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Enterprise TaxFlow - {{ now()->format('d/m/Y') }}</title>
    <style>
        @page { margin: 2cm; }
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 10pt; 
            color: #334155; 
            line-height: 1.4;
        }
        .header { 
            display: table; 
            width: 100%; 
            border-bottom: 2px solid #2563eb; 
            padding-bottom: 20px; 
            margin-bottom: 30px; 
        }
        .header-cell { display: table-cell; vertical-align: middle; }
        .logo { 
            width: 50px; 
            height: 50px; 
            object-fit: contain;
        }
        .title { font-size: 18pt; font-weight: bold; color: #1e293b; margin: 0; }
        .subtitle { font-size: 9pt; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        
        .stats-grid { display: table; width: 100%; border-spacing: 15px 0; margin-bottom: 30px; margin-left: -15px; margin-right: -15px; }
        .stats-box { 
            display: table-cell; 
            width: 33.33%; 
            padding: 20px; 
            border: 1px solid #e2e8f0; 
            border-radius: 12px; 
            background-color: #f8fafc;
        }
        .stats-label { font-size: 7pt; font-weight: bold; color: #64748b; text-transform: uppercase; margin-bottom: 5px; }
        .stats-value { font-size: 16pt; font-weight: bold; color: #1e293b; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { 
            background-color: #f1f5f9; 
            color: #475569; 
            font-size: 8pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #e2e8f0;
        }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 9pt; vertical-align: middle; }
        .zebra tr:nth-child(even) { background-color: #f8fafc; }
        
        .progress-bar { width: 60px; height: 6px; background-color: #f1f5f9; border-radius: 3px; display: inline-block; position: relative; }
        .progress-fill { height: 6px; background-color: #2563eb; border-radius: 3px; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 7pt; font-weight: bold; text-transform: uppercase; }
        .badge-success { background-color: #f0fdf4; color: #166534; }
        .badge-danger { background-color: #fef2f2; color: #991b1b; }
        
        .footer { 
            position: fixed; 
            bottom: 0cm; 
            left: 0cm; 
            right: 0cm; 
            height: 1cm; 
            border-top: 1px solid #f1f5f9; 
            padding-top: 10px; 
            font-size: 8pt; 
            color: #94a3b8; 
            text-align: center;
        }
        
        .risk-row { display: table; width: 100%; margin-bottom: 10px; }
        .risk-logo { display: table-cell; width: 30px; vertical-align: middle; padding-right: 10px; }
        .risk-info { display: table-cell; vertical-align: middle; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-cell" style="width: 60px;">
            <img src="{{ public_path('logo/logo_original.png') }}" class="logo" alt="Logo">
        </div>
        <div class="header-cell">
            <h1 class="title">Laporan Analitik Resmi</h1>
            <p class="subtitle">Intelijen Manajemen TaxFlow Enterprise</p>
        </div>
        <div class="header-cell" style="text-align: right;">
            <p style="font-size: 8pt; color: #64748b;">ID Laporan: #TX-{{ now()->format('Ymd') }}-{{ rand(100,999) }}</p>
            <p style="font-size: 8pt; color: #64748b;">Tanggal: {{ now()->format('D, d M Y H:i') }}</p>
        </div>
    </div>

    <!-- Executive Summary -->
    <div class="stats-grid">
        <div class="stats-box">
            <div class="stats-label">Total Volume Audit</div>
            <div class="stats-value">{{ number_format($overallStats['total_records']) }}</div>
        </div>
        <div class="stats-box">
            <div class="stats-label">Hasil Penyelesaian</div>
            <div class="stats-value">{{ number_format($overallStats['avg_completion'], 1) }}%</div>
        </div>
        <div class="stats-box" style="border-left: 4px solid #ef4444;">
            <div class="stats-label">Defisit Sumber Daya Kritis</div>
            <div class="stats-value" style="color: #ef4444;">{{ number_format($overallStats['critical_records']) }}</div>
        </div>
    </div>

    <!-- Main Workflow Efficiency -->
    <h3 style="font-size: 11pt; color: #1e293b; text-transform: uppercase; margin-bottom: 15px;">Rincian Efisiensi Alur Kerja Pajak</h3>
    <table class="zebra">
        <thead>
            <tr>
                <th>Klasifikasi</th>
                <th style="text-align: center;">Volume</th>
                <th style="text-align: center;">Selesai</th>
                <th style="text-align: center;">Lambat</th>
                <th style="text-align: right;">Tingkat Hasil</th>
            </tr>
        </thead>
        <tbody>
            @foreach($taxTypeStats as $stat)
            <tr>
                <td style="font-weight: bold; color: #1e293b;">{{ $stat->jenis_pajak }}</td>
                <td style="text-align: center;">{{ number_format($stat->total) }}</td>
                <td style="text-align: center; color: #059669;">{{ number_format($stat->completed) }}</td>
                <td style="text-align: center; color: #dc2626;">{{ number_format($stat->overdue) }}</td>
                <td style="text-align: right;">
                    <span style="font-weight: bold; margin-right: 5px;">{{ number_format($stat->completed / max(1, $stat->total) * 100, 1) }}%</span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $stat->completed / max(1, $stat->total) * 100 }}%"></div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="page-break-inside: avoid;">
        <h3 style="font-size: 11pt; color: #1e293b; text-transform: uppercase; margin-bottom: 15px;">Profil Risiko Operasional</h3>
        <p style="font-size: 8pt; color: #64748b; margin-bottom: 15px;">Daftar entitas yang memerlukan intervensi manajemen segera karena status terlambat kritis.</p>
        
        <table style="width: 100%;">
            @foreach($topDefaulters as $risk)
            <tr>
                <td style="border-bottom: 1px solid #f1f5f9;">
                    <div class="risk-row">
                        <div class="risk-info">
                            <span style="font-weight: bold; color: #1e293b; display: block; font-size: 10pt;">{{ $risk->nama_perusahaan }}</span>
                            <span style="color: #ef4444; font-size: 8pt; font-weight: bold;">KRITIS: {{ $risk->overdue_count }} DOKUMEN TERLAMBAT</span>
                        </div>
                        <div style="display: table-cell; text-align: right; vertical-align: middle;">
                            <span class="badge badge-danger">Risiko Tinggi</span>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>

    <div class="footer">
        Intelijen Manajemen TaxFlow Enterprise | Dokumen Audit Rahasia | Halaman 1 dari 1
    </div>
</body>
</html>
