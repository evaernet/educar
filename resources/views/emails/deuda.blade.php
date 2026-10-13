<p>Hola {{ $nombrePadre }},</p>
<p>Hoy es el día 20 y figuran pagos pendientes por un total de <strong>${{ number_format($total, 2, ',', '.') }}</strong>.</p>
@foreach($facturas as $f)
    <p><strong>{{ $f->alumno->apellido }}, {{ $f->alumno->nombre }}</strong> — periodo {{ $f->periodo }}</p>
    <ul>
        @foreach($f->items->whereNull('pago_id') as $item)
            <li>{{ $item->descripcion }}: ${{ number_format((float) $item->importe, 2, ',', '.') }}</li>
        @endforeach
    </ul>
@endforeach
<p>Si ya pagaste, ignorá este mensaje. Podés regularizar desde la aplicación.</p>
<p>Educar Para Transformar</p>
