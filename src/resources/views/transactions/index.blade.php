@extends('layouts.app')

@section('title', 'Transacciones')

@push('breadcrumb')
<li class="breadcrumb-item active">Transacciones</li>
@endpush

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-exchange-alt mr-1"></i> Transacciones
        </h3>
        <div class="card-tools">
            <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-1"></i> Nueva transacción
            </a>
        </div>
    </div>
    <div class="card-body">

        {{--
            Filtros de la tabla.

            Distribución en md (≥768px):
              Tipo       col-md-2  → 2
              Categoría  col-md-3  → 3
              Desde      col-md-2  → 2
              Hasta      col-md-2  → 2
              Moneda     col-md-2  → 2
              Limpiar    col-md-1  → 1
                                  ─────
                                   12 ✓

            En sm/xs cada filtro ocupa col-sm-6 o col-12,
            por lo que se apila en dos columnas en móvil.
        --}}
        <div class="row mb-3">
            <div class="col-md-2 col-sm-6 mb-2">
                <select id="filter-type" class="form-control">
                    <option value="">Tipo: Todos</option>
                    <option value="income">Ingresos</option>
                    <option value="expense">Gastos</option>
                </select>
            </div>
            <div class="col-md-3 col-sm-6 mb-2">
                <select id="filter-category" class="form-control">
                    <option value="">Categoría: Todas</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">
                        {{ $category->display_name ?? $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-sm-6 mb-2">
                <input type="date" id="filter-date-from"
                    class="form-control" placeholder="Desde">
            </div>
            <div class="col-md-2 col-sm-6 mb-2">
                <input type="date" id="filter-date-to"
                    class="form-control" placeholder="Hasta">
            </div>
            <div class="col-md-2 col-sm-6 mb-2">
                <select id="filter-currency" class="form-control">
                    <option value="">Moneda: Todas</option>
                    <option value="EUR">EUR — Euro</option>
                    <option value="USD">USD — Dólar</option>
                    <option value="GBP">GBP — Libra</option>
                </select>
            </div>
            {{--
                col-md-1: suficiente para el icono en escritorio.
                col-sm-6: en móvil ocupa media fila para alinearse
                          con el filtro de moneda de arriba.
            --}}
            <div class="col-md-1 col-sm-6 mb-2">
                <button id="clear-filters" class="btn btn-secondary btn-block">
                    <i class="fas fa-times"></i>
                    <span class="d-none d-lg-inline ml-1">Limpiar</span>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tabla-transacciones"
                class="table table-hover table-striped table-bordered mb-0 text-center dt-responsive"
                data-api-url="{{ route('api.transactions.index') }}">
                <thead class="text-center bg-white font-weight-bold">
                    <tr>
                        <th>Fecha</th>
                        <th>Concepto</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th>Importe</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>

@endsection