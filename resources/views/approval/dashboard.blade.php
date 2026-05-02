@extends('layouts.app')

@section('title', 'Approval Dashboard')
@section('page-title', 'Pusat Kendali Approval')

@section('actions')
    <div class="flex items-center gap-2">
        <div class="px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-lg">
            <span class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest flex items-center gap-2">
                <i class="ph ph-shield-check text-base"></i> MODE_ADMIN_AKTIF
            </span>
        </div>
        <form action="{{ route('approval.simulasi.randomize') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all" style="cursor: pointer;">
                <i class="ph ph-shuffle text-amber-600 dark:text-amber-400 text-sm"></i>
                <span class="text-[8px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest">SIMULASI</span>
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
                <i class="ph ph-fingerprint text-8xl"></i>
            </div>
            <p class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Pending Verifikasi</p>
            <div class="flex items-end justify-between">
                <h2 class="text-3xl font-black text-slate-800 dark:text-white tabular-nums leading-none">{{ $pendingVerifikasi }}</h2>
                <span class="text-[9px] font-black px-2 py-0.5 bg-amber-50 dark:bg-amber-900/30 text-amber-600 rounded-full">ENTRI_BARU</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
                <i class="ph ph-files text-8xl"></i>
            </div>
            <p class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Pending Validasi</p>
            <div class="flex items-end justify-between">
                <h2 class="text-3xl font-black text-slate-800 dark:text-white tabular-nums leading-none">{{ $pendingValidasi }}</h2>
                <span class="text-[9px] font-black px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 rounded-full">CEK_BERKAS</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
                <i class="ph ph-seal-check text-8xl"></i>
            </div>
            <p class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Pending Approval</p>
            <div class="flex items-end justify-between">
                <h2 class="text-3xl font-black text-slate-800 dark:text-white tabular-nums leading-none">{{ $pendingApproval }}</h2>
                <span class="text-[9px] font-black px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 rounded-full">FINAL_STAGE</span>
            </div>
        </div>

        <div class="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-xl overflow-hidden relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-transparent"></div>
            <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest mb-1 relative z-10">Total Database</p>
            <div class="flex items-end justify-between relative z-10">
                <h2 class="text-3xl font-black text-white tabular-nums leading-none">{{ $totalPajak }}</h2>
                <i class="ph ph-database text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Complex Filter Bar -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm p-5 transition-all">
        <form action="{{ route('approval.dashboard') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Universal Search -->
                <div class="md:col-span-4">
                    <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2 px-1">Universal Search Filter</label>
                    <div class="relative group">
                        <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama klien, jenis pajak, atau periode..." 
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 focus:bg-white transition-all">
                    </div>
                </div>

                <!-- Workflow Phase Filters -->
                <div class="md:col-span-2">
                    <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2 px-1">Internal_Verif</label>
                    <select name="status_verifikasi" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-[10px] font-black text-slate-600 dark:text-slate-400 outline-none appearance-none">
                        <option value="">SEMUA STATUS</option>
                        <option value="pending" {{ request('status_verifikasi') == 'pending' ? 'selected' : '' }}>PENDING</option>
                        <option value="verified" {{ request('status_verifikasi') == 'verified' ? 'selected' : '' }}>VERIFIED</option>
                        <option value="needs_revision" {{ request('status_verifikasi') == 'needs_revision' ? 'selected' : '' }}>REVISION</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2 px-1">Doc_Validation</label>
                    <select name="status_validasi" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-[10px] font-black text-slate-600 dark:text-slate-400 outline-none appearance-none">
                        <option value="">SEMUA STATUS</option>
                        <option value="pending" {{ request('status_validasi') == 'pending' ? 'selected' : '' }}>PENDING</option>
                        <option value="valid" {{ request('status_validasi') == 'valid' ? 'selected' : '' }}>VALID</option>
                        <option value="invalid" {{ request('status_validasi') == 'invalid' ? 'selected' : '' }}>INVALID</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2 px-1">Final_Approval</label>
                    <select name="status_approval" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-[10px] font-black text-slate-600 dark:text-slate-400 outline-none appearance-none">
                        <option value="">SEMUA STATUS</option>
                        <option value="pending" {{ request('status_approval') == 'pending' ? 'selected' : '' }}>PENDING</option>
                        <option value="approved" {{ request('status_approval') == 'approved' ? 'selected' : '' }}>APPROVED</option>
                        <option value="rejected" {{ request('status_approval') == 'rejected' ? 'selected' : '' }}>REJECTED</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit" class="flex-1 py-3 bg-slate-900 dark:bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-black dark:hover:bg-blue-700 shadow-lg transition-all">Apply</button>
                    <a href="{{ route('approval.dashboard') }}" class="p-3 bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-400 rounded-2xl hover:bg-slate-200 transition-all">
                        <i class="ph ph-arrows-clockwise font-black"></i>
                    </a>
                </div>
            </div>

            <!-- Advanced Filters Drawer (Always Visible for now as user requested complex) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-4 border-t border-slate-50 dark:border-slate-700/50">
                <div class="md:col-span-2">
                    <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2 px-1">Klasifikasi Objek Pajak</label>
                    <select name="jenis_pajak" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-xl text-[10px] font-black text-slate-600 dark:text-slate-400 outline-none appearance-none">
                        <option value="">Pilih Jenis Pajak...</option>
                        @foreach($jenisPajakList as $jp)
                            <option value="{{ $jp }}" {{ request('jenis_pajak') == $jp ? 'selected' : '' }}>{{ strtoupper($jp) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2 px-1">Petugas Penginput (Staff)</label>
                    <select name="staff_id" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-xl text-[10px] font-black text-slate-600 dark:text-slate-400 outline-none appearance-none">
                        <option value="">Semua Staff Pengelola...</option>
                        @foreach($staffList as $staff)
                            <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>{{ strtoupper($staff->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Pajak Table Modern -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
        <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-[0.2em] flex items-center gap-2">
                <i class="ph ph-list-checks text-blue-600 text-xl"></i> Antrian Kerja Perpajakan
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-[11px]">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] border-b border-slate-100 dark:border-slate-700">
                        <th class="px-6 py-4 text-left">Organisasi / Klien</th>
                        <th class="px-6 py-4 text-left">Internal Verification</th>
                        <th class="px-6 py-4 text-left">Document Validation</th>
                        <th class="px-6 py-4 text-left">Final Approval</th>
                        <th class="px-6 py-4 text-left">Staff</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($pajaks as $pajak)
                    <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-900/10 transition-all group">
                        <td class="px-6 py-4">
                            <div class="font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">{{ $pajak->nama_perusahaan }}</div>
                            <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ $pajak->jenis_pajak }} · {{ $pajak->periode }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($pajak->status_verifikasi === 'verified')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[9px] font-black rounded-full uppercase tracking-widest border border-emerald-100 dark:border-emerald-800">
                                    <i class="ph ph-check-circle"></i> VERIFIED
                                </span>
                            @elseif($pajak->status_verifikasi === 'needs_revision')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-[9px] font-black rounded-full uppercase tracking-widest border border-rose-100 dark:border-rose-800">
                                    <i class="ph ph-warning-circle"></i> REVISION
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 dark:bg-slate-900 text-slate-400 text-[9px] font-black rounded-full uppercase tracking-widest border border-slate-200 dark:border-slate-700">
                                    <i class="ph ph-clock"></i> PENDING
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($pajak->status_validasi === 'valid')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[9px] font-black rounded-full uppercase tracking-widest border border-emerald-100 dark:border-emerald-800">
                                    <i class="ph ph-file-check"></i> VALID
                                </span>
                            @elseif($pajak->status_validasi === 'invalid')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-[9px] font-black rounded-full uppercase tracking-widest border border-rose-100 dark:border-rose-800">
                                    <i class="ph ph-file-x"></i> INVALID
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 dark:bg-slate-900 text-slate-400 text-[9px] font-black rounded-full uppercase tracking-widest border border-slate-200 dark:border-slate-700">
                                    <i class="ph ph-clock"></i> PENDING
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($pajak->status_approval === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[9px] font-black rounded-full uppercase tracking-widest border border-blue-100 dark:border-blue-800">
                                    <i class="ph ph-seal-check"></i> APPROVED
                                </span>
                            @elseif($pajak->status_approval === 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-[9px] font-black rounded-full uppercase tracking-widest border border-rose-100 dark:border-rose-800">
                                    <i class="ph ph-x-circle"></i> REJECTED
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 dark:bg-slate-900 text-slate-400 text-[9px] font-black rounded-full uppercase tracking-widest border border-slate-200 dark:border-slate-700">
                                    <i class="ph ph-clock"></i> PENDING
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-bold uppercase">{{ $pajak->user->name ?? 'SYSTEM' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if(trim($pajak->status_verifikasi) == 'pending')
                                    <button onclick="showVerifikasiModal({{ $pajak->id }})" class="p-2 bg-amber-50 dark:bg-amber-900/20 text-amber-600 hover:bg-amber-600 hover:text-white rounded-xl transition-all shadow-sm" title="Verifikasi">
                                        <i class="ph ph-fingerprint text-lg"></i>
                                    </button>
                                @endif

                                @if(trim($pajak->status_verifikasi) == 'verified' && trim($pajak->status_validasi) == 'pending')
                                    <button onclick="showValidasiModal({{ $pajak->id }})" class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-sm" title="Validasi">
                                        <i class="ph ph-files text-lg"></i>
                                    </button>
                                @endif

                                @if(trim($pajak->status_validasi) == 'valid' && trim($pajak->status_approval) == 'pending')
                                    <button onclick="showApprovalModal({{ $pajak->id }})" class="p-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-xl transition-all shadow-sm" title="Approval">
                                        <i class="ph ph-seal-check text-lg"></i>
                                    </button>
                                @endif

                                @if(trim($pajak->status_approval) == 'approved')
                                    <button onclick="showLaporanModal({{ $pajak->id }})" class="p-2 bg-slate-900 dark:bg-slate-700 text-white hover:bg-blue-600 rounded-xl transition-all shadow-md" title="Laporan">
                                        <i class="ph ph-file-pdf text-lg"></i>
                                    </button>
                                @endif
                                
                                <a href="{{ route('pajak.show', $pajak->id) }}" class="p-2 text-slate-400 hover:text-slate-800 dark:hover:text-white transition-all">
                                    <i class="ph ph-arrow-right text-lg"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center text-slate-300 dark:text-slate-600 font-black italic uppercase tracking-[0.3em]">Antrian Kosong</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700">
            {{ $pajaks->links() }}
        </div>
    </div>
</div>

<!-- Modern Modals Layout -->

<!-- Verifikasi Modal -->
<div id="verifikasiModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-all duration-300 opacity-0 px-4">
    <div class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-[32px] shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden transform transition-all scale-95 opacity-0" id="verifikasiPanel">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="ph ph-fingerprint text-blue-600 text-2xl"></i> Verifikasi Data Pajak
                </h3>
                <button type="button" onclick="closeModernModal('verifikasiModal', 'verifikasiPanel')" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-50 dark:bg-slate-700 text-slate-400 hover:text-rose-500 transition-colors">
                    <i class="ph ph-x font-bold"></i>
                </button>
            </div>
            
            <form id="verifikasiForm" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Status Verifikasi Internal</label>
                    <select name="status_verifikasi" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-2xl text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                        <option value="verified">Verified (Siap Validasi Dokumen)</option>
                        <option value="needs_revision">Needs Revision (Kembalikan ke Staff)</option>
                    </select>
                </div>
                
                <div id="catatanGroup" class="hidden">
                    <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Instruksi Perbaikan</label>
                    <textarea name="catatan_perbaikan" rows="3" class="w-full px-4 py-3 bg-rose-50/10 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/50 rounded-2xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none" placeholder="Tuliskan alasan pengembalian..."></textarea>
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="w-full py-4 bg-slate-900 dark:bg-blue-600 text-white font-black rounded-2xl text-[10px] tracking-widest uppercase hover:bg-blue-600 dark:hover:bg-blue-700 shadow-xl transition-all">
                        Simpan Keputusan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Validasi Modal Modern -->
<div id="validasiModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-all duration-300 opacity-0 px-4">
    <div class="relative w-full max-w-2xl bg-slate-50 dark:bg-slate-900 rounded-[32px] shadow-2xl border border-slate-100 dark:border-slate-800 overflow-hidden transform transition-all scale-95 opacity-0" id="validasiPanel">
        <div class="p-6 max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="ph ph-files text-blue-600 text-2xl"></i> Validasi Berkas Pendukung
                </h3>
                <button type="button" onclick="closeModernModal('validasiModal', 'validasiPanel')" class="w-8 h-8 flex items-center justify-center rounded-full bg-white dark:bg-slate-700 text-slate-400 hover:text-rose-500 transition-colors border border-slate-100 dark:border-slate-600">
                    <i class="ph ph-x font-bold"></i>
                </button>
            </div>
            
            <div id="dokumenList" class="space-y-4 overflow-y-auto pr-2 custom-scrollbar">
                <!-- Data loaded via JS -->
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal Modern -->
<div id="approvalModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-all duration-300 opacity-0 px-4">
    <div class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-[32px] shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden transform transition-all scale-95 opacity-0" id="approvalPanel">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="ph ph-seal-check text-blue-600 text-2xl"></i> Final Approval Decision
                </h3>
                <button type="button" onclick="closeModernModal('approvalModal', 'approvalPanel')" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-50 dark:bg-slate-700 text-slate-400 hover:text-rose-500 transition-colors">
                    <i class="ph ph-x font-bold"></i>
                </button>
            </div>
            
            <form id="approvalForm" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Keputusan Akhir</label>
                    <select name="status_approval" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-2xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                        <option value="approved">Approved & Selesai</option>
                        <option value="rejected">Rejected (Tolak Proses)</option>
                    </select>
                </div>
                
                <div id="keteranganGroup" class="hidden">
                    <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Keterangan / Alasan Penolakan</label>
                    <textarea name="keterangan" rows="3" class="w-full px-4 py-3 bg-rose-50/10 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/50 rounded-2xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none" placeholder="Jelaskan alasan pemutusan..."></textarea>
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="w-full py-4 bg-blue-600 text-white font-black rounded-2xl text-[10px] tracking-widest uppercase hover:bg-blue-700 shadow-xl transition-all">
                        EKSEKUSI_KEPUTUSAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Laporan Modal Modern -->
<div id="laporanModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-all duration-300 opacity-0 px-4">
    <div class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-[32px] shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden transform transition-all scale-95 opacity-0" id="laporanPanel">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/30 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-4">
                <i class="ph ph-file-pdf text-3xl"></i>
            </div>
            <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest mb-2">Generate Laporan Output</h3>
            <p class="text-[10px] font-medium text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6">Konfigurasi Parameter Laporan Pajak</p>
            
            <form id="laporanForm" method="POST" class="space-y-4 text-left">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Format Output</label>
                        <select name="jenis_laporan" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-2xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none">
                            <option value="PDF">DOC_PDF</option>
                            <option value="Excel">SPREADSHEET</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Masa Laporan</label>
                        <input type="date" name="periode_laporan" value="{{ date('Y-m-d') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-2xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none">
                    </div>
                </div>
                
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeModernModal('laporanModal', 'laporanPanel')" class="flex-1 py-3 bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-300 font-black rounded-2xl text-[9px] tracking-widest uppercase hover:bg-slate-200 transition-all">BATAL</button>
                    <button type="submit" class="flex-[2] py-3 bg-slate-900 dark:bg-blue-600 text-white font-black rounded-2xl text-[9px] tracking-widest uppercase hover:bg-blue-600 shadow-xl transition-all">GENERATE_NOW</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModernModal(modalId, panelId) {
    const modal = document.getElementById(modalId);
    const panel = document.getElementById(panelId);
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        panel.classList.remove('scale-95', 'opacity-0');
        panel.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeModernModal(modalId, panelId) {
    const modal = document.getElementById(modalId);
    const panel = document.getElementById(panelId);
    
    modal.classList.add('opacity-0');
    panel.classList.remove('scale-100', 'opacity-100');
    panel.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 300);
}

function showVerifikasiModal(pajakId) {
    document.getElementById('verifikasiForm').action = `/approval/verifikasi/${pajakId}`;
    openModernModal('verifikasiModal', 'verifikasiPanel');
}

function showValidasiModal(pajakId) {
    // Load dokumen list via AJAX
    $.get(`/pajak/${pajakId}/dokumen`, function(data) {
        let html = '';
        if (data.length === 0) {
            html = '<div class="p-10 text-center"><div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3"><i class="ph ph-warning text-slate-300 text-2xl"></i></div><p class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest">Tidak ada berkas tersedia</p></div>';
        } else {
            data.forEach(dokumen => {
                const statusInfo = dokumen.status_validasi === 'valid' 
                    ? { bg: 'bg-emerald-50 dark:bg-emerald-900/30', text: 'text-emerald-600 dark:text-emerald-400', label: 'VALID', icon: 'ph-check-circle' } 
                    : dokumen.status_validasi === 'invalid'
                    ? { bg: 'bg-rose-50 dark:bg-rose-900/30', text: 'text-rose-600 dark:text-rose-400', label: 'INVALID', icon: 'ph-warning-circle' }
                    : { bg: 'bg-amber-50 dark:bg-amber-900/30', text: 'text-amber-600 dark:text-amber-400', label: 'PENDING', icon: 'ph-clock' };
                
                html += `
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm transition-all hover:shadow-md">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-blue-600">
                                    <i class="ph ph-file-pdf text-2xl"></i>
                                </div>
                                <div>
                                    <h6 class="text-[11px] font-black text-slate-800 dark:text-white uppercase tracking-tight line-clamp-1">${dokumen.nama_dokumen}</h6>
                                    <p class="text-[8px] font-medium text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5">
                                        ${dokumen.ukuran_file ? (dokumen.ukuran_file / 1024).toFixed(1) + ' KB' : 'UKURAN_NULL'} · ${dokumen.tanggal_upload ? new Date(dokumen.tanggal_upload).toLocaleDateString('id-ID') : 'TGL_NULL'}
                                    </p>
                                </div>
                            </div>
                            <div class="px-2.5 py-1 ${statusInfo.bg} ${statusInfo.text} rounded-full text-[8px] font-black uppercase tracking-widest border border-slate-100 dark:border-slate-700 flex items-center gap-1.5">
                                <i class="${statusInfo.icon}"></i> ${statusInfo.label}
                            </div>
                        </div>
                        
                        ${dokumen.status_validasi === 'pending' ? `
                            <form method="POST" action="/approval/validasi-dokumen/${dokumen.id}" class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-700 flex flex-wrap gap-2">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="flex-1 min-w-[120px]">
                                    <select name="status_validasi" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-xl text-[10px] font-black text-slate-800 dark:text-slate-200 outline-none" required>
                                        <option value="valid">SET_VALID</option>
                                        <option value="invalid">SET_INVALID</option>
                                    </select>
                                </div>
                                <div class="flex-[3] min-w-[180px]">
                                    <input type="text" name="keterangan_validasi" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-xl text-[10px] font-bold text-slate-600 dark:text-slate-400 outline-none" placeholder="Catatan opsional...">
                                </div>
                                <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-blue-600 text-white text-[9px] font-black rounded-xl uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg">SUBMIT_CEK</button>
                            </form>
                        ` : `
                            <div class="mt-2 p-3 bg-slate-50/50 dark:bg-slate-900/50 rounded-2xl">
                                <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase flex items-center gap-2">
                                    <i class="ph ph-calendar"></i> VERIFIED: ${dokumen.tanggal_validasi ? new Date(dokumen.tanggal_validasi).toLocaleDateString('id-ID') : 'SYSTEM_CHECKED'}
                                </p>
                                ${dokumen.keterangan_validasi ? `<p class="text-[9px] font-medium text-slate-600 dark:text-slate-300 italic mt-1 bg-white dark:bg-slate-800 p-2 rounded-lg border border-slate-100 dark:border-slate-700">" ${dokumen.keterangan_validasi} "</p>` : ''}
                            </div>
                        `}
                    </div>
                `;
            });
        }
        $('#dokumenList').html(html);
    }).fail(function() {
        $('#dokumenList').html('<div class="p-10 text-center"><p class="text-[10px] font-black text-rose-500 uppercase">SERVER_CONNECT_RESTRICTED</p></div>');
    });
    openModernModal('validasiModal', 'validasiPanel');
}

function showApprovalModal(pajakId) {
    document.getElementById('approvalForm').action = `/approval/approve-reject/${pajakId}`;
    openModernModal('approvalModal', 'approvalPanel');
}

function showLaporanModal(pajakId) {
    document.getElementById('laporanForm').action = `/approval/generate-laporan/${pajakId}`;
    openModernModal('laporanModal', 'laporanPanel');
}

// Toggle fields using jQuery as existing
$(document).ready(function() {
    $('select[name="status_verifikasi"]').change(function() {
        if ($(this).val() === 'needs_revision') {
            $('#catatanGroup').removeClass('hidden');
        } else {
            $('#catatanGroup').addClass('hidden');
        }
    });

    $('select[name="status_approval"]').change(function() {
        if ($(this).val() === 'rejected') {
            $('#keteranganGroup').removeClass('hidden');
        } else {
            $('#keteranganGroup').addClass('hidden');
        }
    });
});
</script>

<style>
/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
</style>
@endsection