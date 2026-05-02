<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
    <table border="1">
        <tr>
            <th colspan="5" style="text-align: center; font-size: 16pt; font-weight: bold; background-color: #2563eb; color: #ffffff; padding: 10px;">LAPORAN ANALITIK RESMI TAXFLOW ENTERPRISE</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; font-size: 10pt; color: #64748b;">Diekspor pada: {{ now()->format('D, d M Y H:i:s') }}</th>
        </tr>
        <tr><td colspan="5"></td></tr>

        <!-- Ringkasan Eksekutif -->
        <tr>
            <th colspan="5" style="font-size: 12pt; font-weight: bold; background-color: #f1f5f9; text-align: left; padding: 8px;">I. RINGKASAN EKSEKUTIF</th>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold; padding: 5px;">Total Volume Audit Seluruh Data</td>
            <td colspan="2" style="text-align: center; font-weight: bold; font-size: 14pt; color: #2563eb; padding: 5px;">{{ number_format($overallStats['total_records']) }}</td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold; padding: 5px;">Tingkat Efisiensi Penyelesaian Rata-rata</td>
            <td colspan="2" style="text-align: center; font-weight: bold; font-size: 14pt; color: #059669; padding: 5px;">{{ number_format($overallStats['avg_completion'], 1) }}%</td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold; padding: 5px;">Defisit Sumber Daya Kritis (Total Pajak Terlambat)</td>
            <td colspan="2" style="text-align: center; font-weight: bold; font-size: 14pt; color: #dc2626; padding: 5px;">{{ number_format($overallStats['critical_records']) }}</td>
        </tr>
        <tr><td colspan="5"></td></tr>

        <!-- Rincian Kinerja -->
        <tr>
            <th colspan="5" style="font-size: 12pt; font-weight: bold; background-color: #f1f5f9; text-align: left; padding: 8px;">II. RINCIAN EFISIENSI ALUR KERJA PAJAK</th>
        </tr>
        <tr>
            <th style="background-color: #1e293b; color: #ffffff; font-weight: bold; padding: 10px;">Klasifikasi / Jenis Pajak</th>
            <th style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; padding: 10px;">Total Volume</th>
            <th style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; padding: 10px;">Terselesaikan</th>
            <th style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; padding: 10px;">Dihitung Lambat</th>
            <th style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; padding: 10px;">Hasil Akhir (%)</th>
        </tr>
        @foreach($taxTypeStats as $stat)
        <tr>
            <td style="font-weight: bold; padding: 5px;">{{ $stat->jenis_pajak }}</td>
            <td style="text-align: center; padding: 5px;">{{ number_format($stat->total) }}</td>
            <td style="text-align: center; color: #059669; font-weight: bold; padding: 5px;">{{ number_format($stat->completed) }}</td>
            <td style="text-align: center; color: #dc2626; font-weight: bold; padding: 5px;">{{ number_format($stat->overdue) }}</td>
            <td style="text-align: center; font-weight: bold; padding: 5px;">{{ number_format($stat->completed / max(1, $stat->total) * 100, 1) }}%</td>
        </tr>
        @endforeach
        <tr><td colspan="5"></td></tr>

        <!-- Profil Risiko -->
        <tr>
            <th colspan="5" style="font-size: 12pt; font-weight: bold; background-color: #f1f5f9; text-align: left; padding: 8px;">III. PROFIL RISIKO OPERASIONAL (ENTITAS PERLU INTERVENSI)</th>
        </tr>
        <tr>
            <th colspan="3" style="background-color: #ef4444; color: #ffffff; font-weight: bold; padding: 10px;">Nama Perusahaan / Entitas Subjek Pajak</th>
            <th colspan="2" style="background-color: #ef4444; color: #ffffff; font-weight: bold; text-align: center; padding: 10px;">Identifikasi Terlambat Kritis</th>
        </tr>
        @foreach($topDefaulters as $risk)
        <tr>
            <td colspan="3" style="font-weight: bold; color: #1e293b; padding: 5px;">{{ $risk->nama_perusahaan }}</td>
            <td colspan="2" style="text-align: center; color: #dc2626; font-weight: bold; padding: 5px;">Kritis: {{ $risk->overdue_count }} Dokumen Terlambat</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
