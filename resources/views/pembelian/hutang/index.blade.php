@extends('layouts.admin')

@section('content')
<h3> Piutang </h3>
<table class="table" id="tblCustomer">
@foreach($customers as $c)
<tr data-id="{{ $c->id }}">
<td>{{ $c->nama_customer }}</td>
<td>{{ number_format($c->faktur->sum(fn($f)=>$f->sisa)) }}</td>
</tr>
@endforeach
</table>


<table class="table" id="tblFaktur"></table>


<script>
document.querySelectorAll('#tblCustomer tr').forEach(row => {
row.addEventListener('click', function () {
fetch('/penjualan/piutang/' + this.dataset.id)
.then(res => res.json())
.then(data => {
let html = `<tr><th>No Faktur</th><th>Total</th><th>Terbayar</th><th>Sisa</th></tr>`;
data.forEach(d => {
html += `<tr><td>${d.no_faktur}</td><td>${d.total}</td><td>${d.terbayar}</td><td>${d.sisa}</td></tr>`;
});
document.getElementById('tblFaktur').innerHTML = html;
});
});
});
</script>
@endsection