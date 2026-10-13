<p>Hola {{ $nombrePadre }},</p>
<p>Te enviamos la factura del mes. El total a pagar es <strong>${{ number_format($total, 2, ',', '.') }}</strong>.</p>
@foreach($facturas as $f)
    <p><strong>{{ $f->alumno->apellido }}, {{ $f->alumno->nombre }}</strong> — periodo {{ $f->periodo }} (vence el {{ $f->fecha_vencimiento->format('d/m/Y') }})</p>
    <ul>
        @foreach($f->items->whereNull('pago_id') as $item)
            <li>{{ $item->descripcion }}: ${{ number_format((float) $item->importe, 2, ',', '.') }}</li>
        @endforeach
    </ul>
@endforeach
<p>El pago es solo por transferencia bancaria. Después subí el comprobante desde la aplicación.</p>
<p>Las facturas van adjuntas en PDF.</p>
<p>Educar Para Transformar</p>
