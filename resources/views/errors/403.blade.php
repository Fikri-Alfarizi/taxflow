@extends('errors.minimal')

@section('title', 'Akses Ditolak')

@section('code')
403
@endsection

@section('icon')
<i class="ph ph-lock-key"></i>
@endsection

@section('message', 'Anda tidak memiliki hak otorisasi yang cukup untuk mengakses halaman atau mengeksekusi aksi ini di dalam sistem.')
