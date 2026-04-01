import './bootstrap';
import 'bootstrap';
import 'admin-lte/dist/css/adminlte.min.css';
import 'admin-lte/dist/js/adminlte.min.js';
import '@fortawesome/fontawesome-free/css/all.min.css';

import $ from 'jquery';
window.$ = $;
window.jQuery = $;

import DataTable from 'datatables.net-bs4';
import 'datatables.net-responsive-bs4';
import 'datatables.net-bs4/css/dataTables.bootstrap4.min.css';
import 'datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css';

import Chart from 'chart.js/auto';
window.Chart = Chart;
window.DataTable = DataTable;

// CSRF token en todas las peticiones AJAX.
// Equivalente al Bearer token del proyecto de tickets
// pero usando la sesión web que ya está activa.
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
});

import { initTransactionsTable } from './tables/transactions-table';
import { initCategoriesTable }   from './tables/categories-table';
import { initBudgetsTable }      from './tables/budgets-table';
import { initReportsCharts }     from './charts/reports-charts';
import { initCategorySelect } from './selects/category-select';
import { initAiChat } from './chat/ai-chat';


document.addEventListener('DOMContentLoaded', function () {

    // El mismo patrón que tickets:
    // buscamos el elemento por id, leemos el data-api-url
    // y lo pasamos a la función de inicialización.
    const transTable = document.getElementById('tabla-transacciones');
    if (transTable) {
        initTransactionsTable(transTable.dataset.apiUrl);
    }

    const catTable = document.getElementById('tabla-categorias');
    if (catTable) {
        initCategoriesTable(catTable.dataset.apiUrl);
    }

    const budTable = document.getElementById('tabla-presupuestos');
    if (budTable) {
        initBudgetsTable(budTable.dataset.apiUrl);
    }

    if (document.getElementById('chartDaily')) {
        initReportsCharts();
    }

    initCategorySelect();
    initAiChat();
});