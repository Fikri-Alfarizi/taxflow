@extends('errors.minimal')

@section('title', 'Kesalahan Server')

@section('code')
500
@endsection

@section('icon')
<i class="ph ph-warning"></i>
@endsection

@section('message', 'Terdapat kesalahan fatal pada sisi peladen saat memproses permintaan Anda. Kami telah mencatat galat ini untuk perbaikan.')
