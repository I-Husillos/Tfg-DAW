import $ from 'jquery';
import { dtLangEs } from './datatables-lang';

export function initCategoriesTable(apiUrl) {
    const tabla = $('#tabla-categorias');

    if (!tabla.length) return;

    if ($.fn.DataTable.isDataTable(tabla)) return;

    tabla.DataTable({
        processing: true,
        serverSide: true,
        responsive: {
            details: {
                type: 'inline',
                target: 'tr',
            },
        },
        autoWidth: false,
        language: dtLangEs,
        order: [[0, 'asc']],
        ajax: {
            url: apiUrl,
            type: 'GET',
            dataType: 'json',
            data: function (d) {
                d.type  = $('#filter-type').val();
                d.level = $('#filter-level').val();
            },
            error: function (xhr) {
                console.error('Error DataTables categories:', xhr.status, xhr.responseText);
            },
        },
        columns: [
            {
                data: 'name',
                className: 'align-middle',
                orderable: true,
            },
            {
                data: 'type',
                className: 'text-center align-middle',
                orderable: true,
                render: function (data, type, row) {
                    const color = row.type_raw === 'income' ? 'success' : 'danger';
                    return `<span class="badge badge-${color}">${data}</span>`;
                },
            },
            {
                data: 'subcategories',
                className: 'align-middle text-muted text-wrap',
                orderable: false,
            },
            {
                data: 'description',
                className: 'align-middle text-wrap',
                orderable: false,
            },
            {
                data: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
            },
        ],
        columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: 1 },
            { responsivePriority: 3, targets: 2 },
            { responsivePriority: 100, targets: 4 },
            { responsivePriority: 101, targets: 3 },
        ],
    });

    $('#filter-type').on('change', function () {
        tabla.DataTable().ajax.reload();
    });

    $('#filter-level').on('change', function () {
        tabla.DataTable().ajax.reload();
    });

    $('#clear-filters').on('click', function () {
        $('#filter-type').val('');
        $('#filter-level').val('');
        tabla.DataTable().search('').ajax.reload();
    });
}