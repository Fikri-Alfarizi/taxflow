<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ekspor Data Pajak</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            margin: {{ $tipe_ekspor == 'pdf' ? '0' : '20px' }};
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #0f172a;
        }
        .header p {
            margin: 5px 0 0;
            color: #64748b;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
        }
        th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 1px;
            text-align: left;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .bg-green { background-color: #dcfce7; color: #166534; }
        .bg-yellow { background-color: #fef3c7; color: #92400e; }
        .bg-red { background-color: #fee2e2; color: #991b1b; }
        
        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #94a3b8;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Pemantauan Basis Pajak</h2>
        <p>TAXFLOW SYSTEM (Diekspor pada {{ date('d M Y H:i') }})</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="18%">Identitas Klien</th>
                <th width="10%">NPWP / NIB</th>
                <th width="14%">Jenis Pajak & Periode</th>
                <th width="10%">Tgl Input</th>
                <th width="10%">Jatuh Tempo</th>
                <th width="8%" class="text-center">Sisa Hari</th>
                <th width="10%" class="text-center">Status</th>
                <th width="10%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pajaks as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong style="color: #0f172a; font-size: 11px;">{{ $p->nama_perusahaan }}</strong><br>
                        <span style="color:#64748b; font-size:9px;">{{ $p->kategori_usaha ?? 'General' }}</span>
                    </td>
                    <td>
                        <span style="color:#475569; font-size:9px;">NPWP:</span> <br>{{ $p->npwp ?? '-' }}<br>
                        <span style="color:#475569; font-size:9px;">NIB:</span> <br>{{ $p->nib ?? '-' }}
                    </td>
                    <td>
                        <strong style="color: #0f172a; font-size: 10px;">{{ $p->jenis_pajak }}</strong><br>
                        <span style="color:#64748b; font-size:9px;">{{ $p->jenis_pajak_rincian }}</span><br>
                        Periode: {{ $p->periode }}
                    </td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($p->tanggal_input)->format('d-m-Y') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($p->tanggal_jatuh_tempo)->format('d-m-Y') }}</td>
                    
                    @php 
                        $days = $p->sisa_hari; 
                        $dayStr = $days;
                        $color = '#64748b'; // default slate-500
                        
                        if($p->status === 'selesai') {
                            $dayStr = 'STABIL';
                            $color = '#10b981'; // emerald-500
                        } elseif($days < 0) {
                            $dayStr = abs($days) . 'H LALU';
                            $color = '#e11d48'; // rose-600
                        } elseif($days <= 3) {
                            $dayStr = ($days == 0 ? 'NOW' : $days . 'H');
                            $color = '#f59e0b'; // amber-500
                        } else {
                            $dayStr = $days . 'H';
                        }
                    @endphp
                    <td class="text-center" style="color: {{ $color }}; font-weight: bold;">
                        {{ $dayStr }}
                    </td>
                    
                    <td class="text-center">
                        @if($p->status == 'selesai')
                            <span class="badge bg-green">SELESAI</span>
                        @elseif($p->status == 'diproses')
                            <span class="badge bg-yellow">PROSES</span>
                        @else
                            <span class="badge bg-red">TERLAMBAT</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $p->user->name ?? 'Sistem' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 30px 10px; font-style: italic; color: #94a3b8;">
                        Tidak ada data pajak yang ditemukan untuk parameter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated by TaxFlow &copy; {{ date('Y') }} PT Value Stream Internasional
    </div>

</body>
</html>
