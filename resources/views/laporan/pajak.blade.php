<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pajak - {{ $pajak->nama_perusahaan }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            background-color: #f0f0f0;
            padding: 5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .info-table .label {
            font-weight: bold;
            background-color: #f9f9f9;
            width: 30%;
        }
        .status-section {
            margin-bottom: 20px;
        }
        .status-table {
            width: 100%;
            border-collapse: collapse;
        }
        .status-table th, .status-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .status-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            width: 200px;
            display: inline-block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($pajak->logo_url)
            <img src="{{ public_path('storage/' . $pajak->logo_url) }}" class="logo" alt="Logo">
        @endif
        <div class="title">LAPORAN PAJAK</div>
        <div class="subtitle">Sistem Informasi Monitoring Proses Pajak</div>
        <div class="subtitle">TaxFlow Professional</div>
    </div>

    <div class="info-section">
        <div class="info-title">INFORMASI PERUSAHAAN</div>
        <table class="info-table">
            <tr>
                <td class="label">Nama Perusahaan</td>
                <td>{{ $pajak->nama_perusahaan }}</td>
            </tr>
            <tr>
                <td class="label">NPWP</td>
                <td>{{ $pajak->npwp }}</td>
            </tr>
            <tr>
                <td class="label">Alamat Lengkap</td>
                <td>{{ $pajak->alamat_lengkap }}</td>
            </tr>
            <tr>
                <td class="label">Nama PIC</td>
                <td>{{ $pajak->nama_pic }}</td>
            </tr>
            <tr>
                <td class="label">Kontak PIC</td>
                <td>{{ $pajak->kontak_pic }}</td>
            </tr>
            <tr>
                <td class="label">Email PIC</td>
                <td>{{ $pajak->email_pic }}</td>
            </tr>
            <tr>
                <td class="label">KPP Pratama</td>
                <td>{{ $pajak->kpp_pratama }}</td>
            </tr>
            <tr>
                <td class="label">Kategori Usaha</td>
                <td>{{ $pajak->kategori_usaha }}</td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <div class="info-title">INFORMASI PAJAK</div>
        <table class="info-table">
            <tr>
                <td class="label">Jenis Pajak</td>
                <td>{{ $pajak->jenis_pajak }}</td>
            </tr>
            <tr>
                <td class="label">Periode</td>
                <td>{{ $pajak->periode }}</td>
            </tr>
            <tr>
                <td class="label">ID Transaksi Source</td>
                <td>{{ $pajak->id_transaksi_source }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Input</td>
                <td>{{ $pajak->tanggal_input ? $pajak->tanggal_input->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Jatuh Tempo</td>
                <td>{{ $pajak->tanggal_jatuh_tempo ? $pajak->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td>{{ ucfirst($pajak->status) }}</td>
            </tr>
            <tr>
                <td class="label">Keterangan</td>
                <td>{{ $pajak->keterangan ?: '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="status-section">
        <div class="info-title">STATUS APPROVAL WORKFLOW</div>
        <table class="status-table">
            <thead>
                <tr>
                    <th>Status Verifikasi</th>
                    <th>Tanggal Verifikasi</th>
                    <th>Status Validasi</th>
                    <th>Tanggal Validasi</th>
                    <th>Status Approval</th>
                    <th>Tanggal Approval</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $pajak->status_verifikasi)) }}</td>
                    <td>{{ $pajak->tanggal_verifikasi ? $pajak->tanggal_verifikasi->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ ucfirst($pajak->status_validasi) }}</td>
                    <td>{{ $pajak->tanggal_validasi ? $pajak->tanggal_validasi->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ ucfirst($pajak->status_approval) }}</td>
                    <td>{{ $pajak->tanggal_approval ? $pajak->tanggal_approval->format('d/m/Y H:i') : '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($pajak->catatanPerbaikans->count() > 0)
    <div class="info-section">
        <div class="info-title">CATATAN PERBAIKAN</div>
        @foreach($pajak->catatanPerbaikans as $catatan)
        <div style="margin-bottom: 15px; padding: 10px; border: 1px solid #ddd;">
            <strong>Tanggal: {{ $catatan->tanggal_catatan->format('d/m/Y H:i') }}</strong><br>
            <strong>Status: {{ ucfirst(str_replace('_', ' ', $catatan->status)) }}</strong><br>
            <strong>Catatan:</strong> {{ $catatan->catatan_perbaikan }}<br>
            @if($catatan->tanggal_perbaikan)
            <strong>Tanggal Perbaikan: {{ $catatan->tanggal_perbaikan->format('d/m/Y H:i') }}</strong>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    @if($pajak->dokumens->count() > 0)
    <div class="info-section">
        <div class="info-title">DOKUMEN TERKAIT</div>
        <table class="status-table">
            <thead>
                <tr>
                    <th>Nama Dokumen</th>
                    <th>Ukuran File</th>
                    <th>Tanggal Upload</th>
                    <th>Status Validasi</th>
                    <th>Tanggal Validasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pajak->dokumens as $dokumen)
                <tr>
                    <td>{{ $dokumen->nama_dokumen }}</td>
                    <td>{{ $dokumen->human_size }}</td>
                    <td>{{ $dokumen->tanggal_upload ? $dokumen->tanggal_upload->format('d/m/Y') : '-' }}</td>
                    <td>{{ ucfirst($dokumen->status_validasi) }}</td>
                    <td>{{ $dokumen->tanggal_validasi ? $dokumen->tanggal_validasi->format('d/m/Y H:i') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($pajak->monitorings->count() > 0)
    <div class="info-section">
        <div class="info-title">RIWAYAT MONITORING</div>
        <table class="status-table">
            <thead>
                <tr>
                    <th>Tanggal Update</th>
                    <th>Status Proses</th>
                    <th>Catatan</th>
                    <th>Staff</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pajak->monitorings as $monitoring)
                <tr>
                    <td>{{ $monitoring->tanggal_update->format('d/m/Y H:i') }}</td>
                    <td>{{ ucfirst($monitoring->status_proses) }}</td>
                    <td>{{ $monitoring->catatan }}</td>
                    <td>{{ $monitoring->user->name ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="signature">
        <div>Ditetapkan pada: {{ now()->format('d/m/Y') }}</div>
        <br><br>
        <div class="signature-line"></div>
        <div>{{ Auth::user()->name }}</div>
        <div>Admin TaxFlow</div>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem TaxFlow pada {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>&copy; 2026 TaxFlow Professional - Sistem Informasi Monitoring Proses Pajak</p>
    </div>
</body>
</html>