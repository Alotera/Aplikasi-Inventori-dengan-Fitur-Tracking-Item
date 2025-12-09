@extends('layouts.user')

@section('content')
<div class="card">
    <h1>Kirim Laporan WI: {{ $wi->wi_number }}</h1>

    <form method="post" action="{{ route('user.work-instructions.report.store', $wi) }}">
        @csrf
        <div>
            <label>Ringkasan</label>
            <textarea name="summary" rows="5" required></textarea>
        </div>
        <div>
            <label>Catatan Tambahan</label>
            <textarea name="notes" rows="3"></textarea>
        </div>
        <button type="submit" class="btn">Kirim</button>
        <a class="btn btn-secondary" href="{{ route('user.work-instructions.show', $wi) }}" style="margin-left:8px;">Batal</a>
    </form>
</div>
@endsection


