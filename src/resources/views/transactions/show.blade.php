@extends('layouts.app')

@section('title', 'Detalle de transacción')

@push('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('transactions.index') }}">Transacciones</a>
</li>
<li class="breadcrumb-item active">Detalle</li>
@endpush

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <span class="badge badge-{{ $transaction->type === 'income' ? 'success' : 'danger' }} mr-2">
                {{ $transaction->type === 'income' ? 'Ingreso' : 'Gasto' }}
            </span>
            {{ $transaction->name ?? $transaction->merchant ?? 'Transacción #' . $transaction->id }}
        </h3>
        <div class="card-tools">
            <a href="{{ route('transactions.edit', $transaction) }}"
                class="btn btn-sm btn-warning">
                <i class="fas fa-edit mr-1"></i> Editar
            </a>
        </div>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Importe</dt>
            <dd class="col-sm-9">
                {{--
                    Mostramos la moneda con esta jerarquía:
                    1. Moneda preferida del perfil del usuario  → user_currency()
                    2. Moneda con la que se guardó la transacción → $transaction->currency
                    3. EUR como último fallback

                    ¿Por qué esta jerarquía?
                    Si el usuario ha configurado USD en su perfil, es porque
                    trabaja en esa moneda y quiere verla así. Si por algún motivo
                    no tiene perfil (caso muy raro), usamos la moneda de la
                    transacción, que es la que se guardó realmente en BD.
                --}}
                <strong class="text-{{ $transaction->type === 'income' ? 'success' : 'danger' }} h5">
                    {{ $transaction->type === 'income' ? '+' : '-' }}
                    {{ number_format($transaction->amount, 2, ',', '.') }}
                    {{ user_currency() }}
                </strong>
            </dd>

            <dt class="col-sm-3">Fecha</dt>
            <dd class="col-sm-9">{{ $transaction->date->format('d/m/Y') }}</dd>

            <dt class="col-sm-3">Categoría</dt>
            <dd class="col-sm-9">
                {{ $transaction->category?->display_name
                       ?? $transaction->category?->name
                       ?? 'Sin categoría' }}
            </dd>

            @if($transaction->name)
            <dt class="col-sm-3">Concepto</dt>
            <dd class="col-sm-9">{{ $transaction->name }}</dd>
            @endif

            @if($transaction->merchant)
            <dt class="col-sm-3">Comercio / Pagador</dt>
            <dd class="col-sm-9">{{ $transaction->merchant }}</dd>
            @endif

            @if($transaction->description)
            <dt class="col-sm-3">Descripción</dt>
            <dd class="col-sm-9">{{ $transaction->description }}</dd>
            @endif

            <dt class="col-sm-3">Moneda original</dt>
            <dd class="col-sm-9">
                {{--
                    Si la moneda guardada en BD difiere de la moneda
                    del perfil, lo indicamos para que el usuario sepa
                    que esa transacción se registró en otra moneda.
                --}}
                <span class="badge badge-secondary">{{ $transaction->currency }}</span>
                @if($transaction->currency !== user_currency())
                    <small class="text-muted ml-1">
                        (tu moneda actual es {{ user_currency() }})
                    </small>
                @endif
            </dd>

            <dt class="col-sm-3">Registrada el</dt>
            <dd class="col-sm-9">{{ $transaction->created_at->format('d/m/Y H:i') }}</dd>

            @if($transaction->updated_at->ne($transaction->created_at))
            <dt class="col-sm-3">Última modificación</dt>
            <dd class="col-sm-9">{{ $transaction->updated_at->format('d/m/Y H:i') }}</dd>
            @endif
        </dl>
    </div>
    <div class="card-footer">
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Volver al listado
        </a>
        <form action="{{ route('transactions.destroy', $transaction) }}"
            method="POST" class="d-inline ml-2"
            onsubmit="return confirm('¿Eliminar esta transacción? Esta acción no se puede deshacer.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash mr-1"></i> Eliminar
            </button>
        </form>
    </div>
</div>

@endsection