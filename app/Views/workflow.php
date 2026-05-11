<?php

/**
 * Safe JSON for embedding in <script>. Prevents invalid output when json_encode() fails
 * (INF/NAN) or non‑UTF8 bytes break encoding — which would leave `Tests: ,` or truncate scripts and cause SyntaxError / empty tables.
 */
if (! function_exists('workflow_view_json')) {
    function workflow_view_json($data): string
    {
        $flags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
        if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
            $flags |= JSON_INVALID_UTF8_SUBSTITUTE;
        }
        if (defined('JSON_PARTIAL_OUTPUT_ON_ERROR')) {
            $flags |= JSON_PARTIAL_OUTPUT_ON_ERROR;
        }
        $json = json_encode($data, $flags);

        return $json === false ? '[]' : $json;
    }
}

// Calculate all packs for use in tables and JS
$allPacks = [];
if (!empty($Tests)) {
    foreach ($Tests as $a) {
        if (!empty($a['test_packs'])) {
            foreach ($a['test_packs'] as $tp) {
                $tp['assessment_name'] = $a['name'];
                $allPacks[] = $tp;
            }
        }
    }
}
?>

<script>window.__APP_BASE__ = <?= workflow_view_json(rtrim(base_url(), '/')) ?>;</script>

<!-- Flash Message Handler -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({ title: 'Success', text: <?= workflow_view_json(session()->getFlashdata('success')) ?>, icon: 'success', timer: 3000 });
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({ title: 'Upload Failed', text: <?= workflow_view_json(session()->getFlashdata('error')) ?>, icon: 'error' });
        <?php endif; ?>
    });
</script>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Question Paper Workflow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .swal2-popup {
            border-radius: 20px !important;
            font-family: 'Poppins', sans-serif !important;
        }

        .swal2-styled.swal2-confirm {
            background-color: var(--brand) !important;
            border-radius: 10px !important;
            padding: 0.6rem 1.5rem !important;
            font-weight: 600 !important;
        }

        .swal2-styled.swal2-cancel {
            border-radius: 10px !important;
            padding: 0.6rem 1.5rem !important;
            font-weight: 600 !important;
        }

        .modal-blur {
            filter: blur(5px);
            transition: filter 0.3s ease;
        }

        /* DataTables Compact Design */
        .dataTables_wrapper .dataTables_filter input {
            width: 220px !important;
            font-size: 11px !important;
            height: 32px !important;
        }

        /* ============================================
           MODERN RED PAGINATION (Bootstrap 5 DataTables)
           Targets: .dataTables_paginate ul.pagination > li.page-item > a.page-link
           ============================================ */
        .dataTables_wrapper .dataTables_info {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            letter-spacing: 0.02em;
            padding-top: 14px !important;
        }

        .dataTables_wrapper .dataTables_info::first-letter {
            color: #dc2230;
        }

        /* Pagination outer wrapper */
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 10px !important;
        }

        .dataTables_wrapper .dataTables_paginate ul.pagination {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item {
            margin: 0 !important;
        }

        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item .page-link {
            min-width: 36px !important;
            height: 36px !important;
            padding: 0 12px !important;
            font-size: 11.5px !important;
            font-weight: 800 !important;
            border-radius: 10px !important;
            border: 1px solid transparent !important;
            background: transparent !important;
            color: #64748b !important;
            letter-spacing: 0.03em !important;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
            outline: none !important;
            box-shadow: none !important;
            text-decoration: none !important;
        }

        /* Hover: light red wash + lift */
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item:not(.active):not(.disabled) .page-link:hover,
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item:not(.active):not(.disabled) .page-link:focus {
            background: #fef2f2 !important;
            border-color: #fecaca !important;
            color: #dc2230 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 10px -4px rgba(220, 34, 48, 0.22) !important;
        }

        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item .page-link:active {
            transform: translateY(0) scale(0.97) !important;
        }

        /* ACTIVE page: bold red gradient */
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.active .page-link,
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.active .page-link:hover,
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.active .page-link:focus {
            background: linear-gradient(135deg, #dc2230 0%, #b91c1c 100%) !important;
            color: #ffffff !important;
            border-color: transparent !important;
            box-shadow:
                0 6px 14px -4px rgba(220, 34, 48, 0.5),
                0 2px 4px -2px rgba(220, 34, 48, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.18) !important;
            transform: translateY(-1px) !important;
            z-index: 1 !important;
        }

        /* Disabled */
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.disabled .page-link,
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.disabled .page-link:hover {
            opacity: 0.45 !important;
            cursor: not-allowed !important;
            background: #f8fafc !important;
            border-color: #f1f5f9 !important;
            color: #cbd5e1 !important;
            transform: none !important;
            box-shadow: none !important;
        }

        /* Previous / Next pills with chevron icons */
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.previous .page-link,
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.next .page-link,
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.first .page-link,
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.last .page-link {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            font-weight: 800 !important;
            padding: 0 14px !important;
            gap: 4px !important;
            text-transform: capitalize !important;
        }

        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.previous:not(.disabled) .page-link:hover,
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.next:not(.disabled) .page-link:hover,
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.first:not(.disabled) .page-link:hover,
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.last:not(.disabled) .page-link:hover {
            background: linear-gradient(135deg, #dc2230 0%, #b91c1c 100%) !important;
            border-color: transparent !important;
            color: #ffffff !important;
            box-shadow: 0 6px 14px -4px rgba(220, 34, 48, 0.42) !important;
        }

        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.previous .page-link::before {
            content: "\F284";
            font-family: "bootstrap-icons";
            font-size: 11px;
            margin-right: 4px;
        }

        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.next .page-link::after {
            content: "\F285";
            font-family: "bootstrap-icons";
            font-size: 11px;
            margin-left: 4px;
        }

        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.first .page-link::before {
            content: "\F282";
            font-family: "bootstrap-icons";
            font-size: 11px;
            margin-right: 4px;
        }

        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.last .page-link::after {
            content: "\F286";
            font-family: "bootstrap-icons";
            font-size: 11px;
            margin-left: 4px;
        }

        /* Ellipsis */
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item .page-link.ellipsis,
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.disabled .page-link[data-dt-idx*="ellipsis"] {
            color: #cbd5e1 !important;
            font-weight: 900 !important;
            padding: 0 6px !important;
            letter-spacing: 1px !important;
            background: transparent !important;
            border-color: transparent !important;
        }

        /* Kill Bootstrap focus blue ring entirely */
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item .page-link:focus {
            box-shadow: 0 0 0 3px rgba(220, 34, 48, 0.15) !important;
        }
        .dataTables_wrapper .dataTables_paginate ul.pagination li.page-item.active .page-link:focus {
            box-shadow:
                0 6px 14px -4px rgba(220, 34, 48, 0.5),
                0 2px 4px -2px rgba(220, 34, 48, 0.3),
                0 0 0 3px rgba(220, 34, 48, 0.2) !important;
        }

        /* Length / dropdown selector */
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 4px 28px 4px 12px !important;
            font-size: 11.5px !important;
            font-weight: 800 !important;
            color: #475569 !important;
            background-color: #fff !important;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%23dc2230' d='M3.204 5L8 10.5 12.796 5z'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 8px center !important;
            background-size: 12px !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            cursor: pointer !important;
            transition: all 0.2s ease;
        }

        .dataTables_wrapper .dataTables_length select:focus {
            border-color: var(--brand) !important;
            box-shadow: 0 0 0 3px rgba(220, 34, 48, 0.12) !important;
            outline: none !important;
        }

        .dataTables_wrapper .dataTables_length label {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            letter-spacing: 0.02em;
        }

        #TestPacksTable_wrapper {
            padding: 0 !important;
        }

        #TestPacksTable {
            border: none !important;
            margin: 0 !important;
        }

        #TestPacksTable thead th {
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 10px 20px !important;
            font-size: 10px !important;
        }

        #TestPacksTable tbody td {
            padding: 8px 20px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        /* Nested DataTable Accordion Styles */
        #TestsDataTable td.dt-control {
            cursor: pointer;
            text-align: center;
            color: #94a3b8;
            transition: all 0.2s ease;
        }

        #TestsDataTable tr.dt-hasChild td.dt-control {
            color: #ef4444;
        }

        #TestsDataTable tr.dt-hasChild td.dt-control i {
            transform: rotate(180deg);
        }

        #TestsDataTable tr td.dt-control i {
            display: inline-block;
            transition: transform 0.2s ease;
        }

        /* Inline Editable Table Styles */
        .inline-editable-input {
            width: 100%;
            background-color: transparent;
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 0.5rem 0.75rem;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            pointer-events: none;
        }

        /* Editing State */
        tr.is-editing .inline-editable-input {
            background-color: rgba(248, 250, 252, 0.8);
            border-color: #e2e8f0;
            pointer-events: auto;
            height: 38px;
        }

        tr.is-editing .inline-editable-input:focus {
            background-color: #ffffff;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08);
            color: #1e293b;
        }

        .hidden {
            display: none !important;
        }

        /* Ensure cells have consistent height and alignment */
        #TestsDataTable td,
        .child-table-container td {
            vertical-align: middle !important;
            height: 56px;
        }

        /* Test Headers row hover state: shaded bg + outlined row */
        #TestsDataTable tbody tr td {
            transition: background-color 0.18s ease, border-color 0.18s ease;
        }

        #TestsDataTable tbody tr:hover td {
            background-color: #f8fafc !important;
            border-top: 1px solid #dbe3ee !important;
            border-bottom: 1px solid #dbe3ee !important;
        }

        #TestsDataTable tbody tr:hover td:first-child {
            border-left: 1px solid #dbe3ee !important;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        #TestsDataTable tbody tr:hover td:last-child {
            border-right: 1px solid #dbe3ee !important;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .inline-select-container {
            position: relative;
        }

        .inline-select-container::after {
            content: "\F282";
            font-family: "bootstrap-icons";
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            color: #94a3b8;
            pointer-events: none;
            display: none;
            /* Hidden in readonly */
        }

        tr.is-editing .inline-select-container::after {
            display: block;
            /* Shown when editing */
        }

        /* Toggle Action Buttons & Views */
        [data-action="save"],
        .edit-view,
        .readonly-view {
            display: none !important;
        }

        tr.is-editing [data-action="save"] {
            display: flex !important;
        }

        tr.is-editing .edit-view {
            display: flex !important;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            z-index: 10;
        }

        tr.is-editing [data-action="edit"] {
            display: none !important;
        }

        tr.is-editing .readonly-view {
            display: none !important;
        }

        tr:not(.is-editing) .readonly-view {
            display: flex !important;
            align-items: center;
            gap: 0.5rem;
        }

        tr.is-editing .inline-editable-input {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            pointer-events: auto !important;
            cursor: text !important;
            height: 38px;
        }

        .batch-action-btn {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        /* Published Row State */
        tr.is-published {
            background-color: rgba(240, 253, 244, 0.5);
        }

        tr.is-published .inline-editable-input {
            color: #059669;
            font-weight: 700;
        }

        .published-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            background-color: #ecfdf5;
            color: #059669;
            border-radius: 6px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: 1px solid #d1fae5;
        }

        .batch-action-btn i {
            line-height: 0;
        }

        .batch-candidate-inline {
            position: relative;
            min-width: 0;
            width: 100%;
            z-index: 120;
        }

        .batch-candidate-trigger {
            width: 100%;
            height: 38px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            color: #334155;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 0 10px;
            transition: all 0.2s ease;
        }

        .batch-candidate-trigger:focus,
        .batch-candidate-trigger:hover {
            border-color: #cbd5e1;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.08);
        }

        .batch-candidate-dropdown {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 6px);
            z-index: 1500;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
            padding: 10px;
        }

        .child-table-container,
        .child-table-container .table-responsive,
        .child-table-container table,
        .child-table-container tbody,
        .child-table-container tr,
        .child-table-container td,
        .candidate-selector-wrapper {
            overflow: visible !important;
        }

        .batch-candidate-list {
            max-height: 190px;
            overflow-y: auto;
            overflow-x: hidden;
            margin-top: 6px;
            padding-right: 2px;
        }

        .batch-candidate-item {
            display: flex;
            align-items: center;
            gap: 8px;
            border-radius: 8px;
            padding: 5px 6px;
            cursor: pointer;
        }

        .batch-candidate-item:hover {
            background: #f8fafc;
        }

        .batch-candidate-item .name {
            font-size: 11px;
            font-weight: 700;
            color: #334155;
            line-height: 1.2;
        }

        .batch-candidate-item .meta {
            font-size: 10px;
            color: #94a3b8;
            line-height: 1.2;
        }

        #TestsDataTable tr.dt-hasChild {
            background-color: #f8fafc;
        }

        .child-table-container {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .child-table-container {
            animation: fadeIn 0.3s ease-out;
            border-left: 4px solid var(--brand);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .template-table th,
        .template-table td {
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
        }

        .template-table thead {
            background: #f8fafc;
        }

        .template-table thead th {
            font-size: 10px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .template-info-cell {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .template-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #fff1f2;
            color: var(--brand);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .template-name {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .template-sub {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
        }

        .section-count-badge {
            background: #f1f5f9;
            color: #475569;
            font-size: 11px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .action-icon-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #94a3b8;
        }

        .action-icon-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            color: #1e293b;
            background: #fff;
        }

        .action-icon-btn.view:hover {
            color: #2563eb;
            border-color: #93c5fd;
            background-color: #eff6ff;
        }

        .action-icon-btn.edit:hover {
            color: #10b981;
            border-color: #6ee7b7;
            background-color: #ecfdf5;
        }

        .action-icon-btn.delete:hover {
            color: #dc2230;
            border-color: #fca5a5;
            background-color: #fef2f2;
        }

        /* Quick Template Modal Styles - TRANSFORMED TO FULL SCREEN TEMPLATE */
        #createPackModal.quick-mode {
            overflow: hidden !important;
        }

        #createPackModal.quick-mode .modal-dialog {
            max-width: 100% !important;
            width: 100% !important;
            height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
            display: flex !important;
        }

        #createPackModal.quick-mode .modal-content {
            border-radius: 0 !important;
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
            box-shadow: none !important;
            border: none !important;
            display: flex !important;
            flex-direction: column !important;
            position: relative !important;
        }

        #createPackModal.quick-mode .modal-body {
            flex: 1 1 auto !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
            min-height: 0 !important;
            padding: 0 !important;
        }

        #createPackModal.quick-mode #quick-mode-footer {
            display: flex;
            flex-shrink: 0 !important;
            height: 64px !important;
            background: #f8fafc !important;
            z-index: 1050 !important;
            position: sticky !important;
            bottom: 0 !important;
            border-top: 1px solid #e2e8f0 !important;
        }

        #createPackModal.quick-mode .modal-header,
        #createPackModal.quick-mode .w-\[500px\] {
            display: none !important;
        }

        #createPackModal.quick-mode .w-\[360px\] {
            display: flex !important;
        }

        #createPackModal.quick-mode #wizardMainColumn {
            padding: 0 !important;
            overflow-y: auto !important;
            min-height: 0 !important;
            height: 100% !important;
            max-height: 100% !important;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }

        /* In template edit/create view, rely on one scroll parent (modal-body). */
        #createPackModal.quick-mode.template-scroll-mode .modal-body {
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        #createPackModal.quick-mode.template-scroll-mode .modal-body>.flex.flex-1 {
            overflow: visible !important;
            min-height: auto !important;
            height: auto !important;
        }

        #createPackModal.quick-mode.template-scroll-mode #wizardMainColumn {
            overflow: visible !important;
            height: auto !important;
            max-height: none !important;
        }

        #createPackModal.quick-mode #wizard_template_section {
            grid-column: span 10 / span 10 !important;
        }

        #createPackModal.quick-mode #wizard_audience_section,
        #createPackModal.quick-mode #wizard_question_content_section,
        #createPackModal.quick-mode #active_template_delete_btn,
        #createPackModal.quick-mode #wizard_edit_structure_btn,
        #createPackModal.quick-mode #active_template_tags .group-hover\:opacity-100 {
            display: none !important;
        }

        #quick-qb-selector-section,
        #quick-generated-paper-section {
            display: none;
        }

        #createPackModal.quick-mode #quick-qb-selector-section,
        #createPackModal.quick-mode #quick-generated-paper-section {
            display: block;
        }

        /* Ensure full generated paper is scrollable via the main container */
        #createPackModal.quick-mode #quick-generated-paper-section {
            max-height: none !important;
            overflow: visible !important;
            padding-bottom: 80px;
        }

        #createPackModal.quick-mode #quick_generated_questions_container {
            overflow: visible;
        }

        /* Ensure builder-mode generated questions are scrollable and usable */
        #createPackModal.quick-mode #builder_questions_section_inline {
            max-height: none;
            overflow: visible;
        }

        #createPackModal.quick-mode #builder_questions_container_inline {
            height: auto;
            max-height: none;
            min-height: 0;
            overflow: visible !important;
            padding-right: 0;
            padding-bottom: 24px;
            scroll-behavior: smooth;
            overscroll-behavior: auto;
        }

        #quick-mode-header {
            display: none;
        }

        #createPackModal.quick-mode #quick-mode-header {
            display: flex !important;
        }

        #quick-mode-footer {
            display: none;
        }

        /* Handled above */

        #quick-template-selector {
            display: none !important;
        }

        /* Question Bank Modal Styles - TRANSFORMED TO FULL SCREEN TEMPLATE */
        #QuestionBankModal.qb-template-mode {
            background: #fff !important;
            backdrop-filter: none !important;
            padding: 0 !important;
            align-items: stretch !important;
        }

        #QuestionBankModal.qb-template-mode>div {
            width: 100% !important;
            max-width: 100% !important;
            height: 100vh !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }
    </style>
    <style>
        :root {
            --brand: #dc2230;
            --brand-dark: #b41a26;
            --brand-soft: #fff1f2;
            --ink: #0b1220;
            --muted: #6b7280;
            --bg: #f4f6f9;
            --line: #e6e8ec;
            --bg-card: #ffffff;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --primary-red: #dc2230;
            --text-primary: #1e293b;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: 'Poppins', sans-serif;
        }

        .label {
            font-size: 10px;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: #9aa0a6;
            font-weight: 700;
        }

        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .03);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .7rem 1.4rem;
            border-radius: 999px;
            font-weight: 800;
            font-size: 12px;
            letter-spacing: .14em;
            text-transform: uppercase;
            transition: all .15s;
        }

        .pill-active {
            background: var(--brand);
            color: #fff;
            box-shadow: 0 8px 20px -6px rgba(220, 34, 48, .5);
        }

        .pill-idle {
            background: #fff;
            color: #b5b9c2;
            border: 1px solid var(--line);
        }

        .btn-red {
            background: var(--brand);
            color: #fff;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            font-size: 12px;
            padding: .85rem 1.4rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            box-shadow: 0 10px 22px -8px rgba(220, 34, 48, .55);
        }

        .btn-red:hover {
            background: var(--brand-dark);
        }

        .btn-ghost {
            background: #fff;
            border: 1px solid var(--line);
            color: #374151;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .6rem .9rem;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }

        .btn-ghost:hover {
            border-color: #cbd5e1;
        }

        .input,
        .select {
            width: 100%;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: .6rem .8rem;
            font-size: 14px;
            color: #111827;
        }

        .stat-card {
            background: linear-gradient(180deg, #fff 0%, #fafbfc 100%);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px 22px;
            min-width: 260px;
            position: relative;
            overflow: hidden;
        }

        .stat-card.is-active {
            border: 1.5px solid var(--brand);
            box-shadow: 0 0 0 4px rgba(220, 34, 48, .06);
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .3rem .6rem;
            border-radius: 999px;
        }

        .chip-mcq {
            background: #eef4ff;
            color: #2563eb;
        }

        .chip-2m {
            background: #fef3c7;
            color: #b45309;
        }

        .chip-mark {
            background: #ecfdf5;
            color: #047857;
        }

        .accordion {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
        }

        .accordion+.accordion {
            margin-top: 12px;
        }

        .custom-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .45);
            display: none;
            align-items: flex-start;
            justify-content: center;
            z-index: 2000;
            padding: 60px 16px;
            overflow: auto;
        }

        .custom-modal-backdrop.open {
            display: flex;
        }

        /* Create/Edit Test: inline slide-down (not a fullscreen backdrop) */
        #TestModal.test-form-inline-panel {
            position: relative;
            inset: auto;
            width: 100%;
            background: transparent;
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.42s cubic-bezier(0.4, 0, 0.2, 1), margin 0.42s ease;
            z-index: 1;
            padding: 0;
            overflow: visible;
            margin: 0;
        }

        #TestModal.test-form-inline-panel.open {
            grid-template-rows: 1fr;
            margin-bottom: 2rem;
        }

        #TestModal.test-form-inline-panel:not(.open) .test-form-slide-inner {
            min-height: 0;
            overflow: hidden;
        }

        #TestModal.test-form-inline-panel.open .test-form-slide-inner {
            overflow: visible;
        }

        #TestModal.test-form-inline-panel .test-create-sheet {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 1.5rem 1.75rem;
            border-radius: 16px;
            background: #fff;
            transform: translateY(-10px);
            opacity: 0;
            transition: transform 0.38s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.32s ease;
            border: 1px solid #e2e8f0;
            box-shadow: 0 12px 32px -18px rgba(15, 23, 42, 0.12);
        }

        @media (min-width: 640px) {
            #TestModal.test-form-inline-panel .test-create-sheet {
                padding: 2rem 2.25rem;
            }
        }

        #TestModal.test-form-inline-panel.open .test-create-sheet {
            transform: translateY(0);
            opacity: 1;
        }

        /* Exam configuration toggle cards inside test form */
        .test-exam-config-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            border-radius: 14px;
            border: 1px solid #f1f5f9;
            background: rgba(248, 250, 252, 0.85);
            min-height: 5.125rem;
        }

        .test-exam-config-card .form-switch .form-check-input {
            cursor: pointer;
            width: 2.5rem;
            height: 1.35rem;
        }

        .custom-modal {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 720px;
            padding: 28px;
            box-shadow: 0 25px 60px -20px rgba(15, 23, 42, .35);
        }

        .tab {
            padding: .55rem 1rem;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            border-radius: 10px;
            cursor: pointer;
        }

        .tab-active {
            background: var(--brand);
            color: #fff;
        }

        .tab-idle {
            color: #6b7280;
            background: #f3f4f6;
        }

        .navbar {
            background: #fff;
            border-bottom: 2px solid #f0f1f3;
            padding: 0.5rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-logo text {
            font-weight: 700;
            color: var(--brand);
            font-size: 1.1rem;
        }

        .nav-center {
            display: flex;
            align-items: center;
            gap: 1.75rem;
        }

        .nav-right-divider {
            width: 1px;
            height: 32px;
            background: #e2e8f0;
            margin: 0 0.25rem;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.05em;
            color: var(--muted);
            cursor: pointer;
            transition: color 0.2s;
        }

        .nav-item:hover {
            color: var(--ink);
        }

        .nav-item.active {
            color: var(--brand);
            border-bottom: 2px solid var(--brand);
            padding-bottom: 4px;
            margin-bottom: -6px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #f3f4f6;
            padding: 0.4rem 0.8rem;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .module-header {
            background: #fff;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #f0f1f3;
        }

        .module-tabs {
            background: #fff;
            border-bottom: 1px solid #f0f1f3;
            padding: 0 2rem;
            display: flex;
            gap: 2rem;
        }

        .module-tab {
            padding: 1rem 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }

        .module-tab:hover {
            color: var(--ink);
        }

        .module-tab.active {
            color: var(--brand);
            border-bottom-color: var(--brand);
        }

        .module-icon {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #fef2f2;
            color: var(--brand);
        }

        .stepper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1000px;
            margin: 3rem auto;
            position: relative;
        }

        .stepper::before {
            content: "";
            position: absolute;
            top: 1.25rem;
            left: 5%;
            right: 5%;
            height: 1px;
            background: #e2e8f0;
            z-index: 0;
        }

        .step {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            text-align: center;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1.5px solid #cbd5e1;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 14px;
            transition: all 0.3s;
        }

        .step.active .step-circle {
            border-color: var(--brand);
            color: var(--brand);
            background: #fef2f2;
            box-shadow: 0 0 0 4px rgba(220, 34, 48, 0.1);
        }

        .step-label {
            font-size: 11px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .step.active .step-label {
            color: var(--brand);
        }

        .card-main {
            background: #fff;
            border: 1px solid #f0f1f3;
            border-radius: 20px;
            padding: 2.5rem;
            max-width: 1100px;
            margin: 0 auto;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
            transition: all 0.3s;
        }

        .details-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .details-item label {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .details-item span {
            display: block;
            font-weight: 700;
            color: var(--ink);
        }

        .btn-outline {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            transition: all 0.2s;
            background: #fff;
        }

        .btn-outline:hover:not(:disabled) {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .btn-red-rounded {
            background: var(--brand);
            color: #fff;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-step-circle {
            width: 22px;
            height: 22px;
            min-width: 22px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-variant-numeric: tabular-nums;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
            transition: all 0.2s ease;
        }

        .btn-red-rounded:hover .btn-step-circle {
            background: #ffffff;
            color: var(--brand);
            border-color: #ffffff;
        }

        .btn-red-rounded.is-active {
            background: #ffffff;
            color: var(--brand);
            border: 1px solid var(--brand);
            box-shadow: inset 0 0 0 2px rgba(220, 34, 48, 0.08), 0 4px 12px -4px rgba(220, 34, 48, 0.25);
        }

        .btn-red-rounded.is-active .btn-step-circle {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
        }

        .btn-red-rounded.is-active:hover {
            background: #fef2f2;
            color: var(--brand);
        }

        .btn-red-rounded:hover {
            background: var(--brand-dark);
            transform: translateY(-1px);
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #e2e8f0;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #2563eb;
        }

        input:checked+.slider:before {
            transform: translateX(20px);
        }

        .settings-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
        }

        .settings-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #f8fafc;
            gap: 2rem;
        }

        .settings-row:last-child {
            border-bottom: none;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
        }

        .summary-total {
            border-top: 1px solid #e2e8f0;
            margin-top: 10px;
            padding-top: 10px;
            font-weight: 700;
            color: var(--brand);
        }

        /* Hide number input arrows */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        /* Accordion Styles */
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, padding 0.3s ease;
        }

        .accordion-content.open {
            max-height: 2000px;
            padding-bottom: 1.5rem;
        }

        .chevron {
            transition: transform 0.3s ease;
        }

        .chevron.rotated {
            transform: rotate(180deg);
        }

        .accordion-header:hover {
            background-color: #f1f5f9;
        }

        /* Premium Multiselect */
        .custom-multiselect {
            position: relative;
            width: 100%;
        }

        .multiselect-btn {
            width: 100%;
            height: 2.75rem;
            min-height: 2.75rem;
            padding: 0 0.75rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.875rem;
            line-height: 1.25;
            color: #475569;
            cursor: pointer;
            text-align: left;
            transition: all 0.2s;
        }

        .multiselect-btn:hover {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .multiselect-options {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-top: 4px;
            z-index: 1000;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            max-height: 200px;
            overflow-y: auto;
            padding: 4px;
        }

        .multiselect-options.show {
            display: block;
        }

        .ms-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 8px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8125rem;
            line-height: 1.2;
            color: #475569;
            margin-bottom: 0;
            transition: all 0.2s;
        }

        .ms-option:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .ms-option input {
            width: 14px;
            height: 14px;
            cursor: pointer;
            accent-color: #dc2230;
            flex-shrink: 0;
        }

        /* Validation Styles */
        .input.is-invalid,
        .select.is-invalid,
        .multiselect-btn.is-invalid {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }

        .error-msg {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: #ef4444;
            margin-top: 4px;
            transition: all 0.2s;
        }

        .error-msg.hidden {
            display: none;
        }

        /* Force SweetAlert to be on top */
        .swal2-container {
            z-index: 10000 !important;
        }

        /* Semantic Badge Colors */
        .badge-fresher {
            background-color: #fef9c3;
            color: #854d0e;
        }

        /* Yellow */
        .badge-enova {
            background-color: #dcfce7;
            color: #166534;
        }

        /* Green */
        .badge-tech {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        /* Indigo */
        .badge-comp {
            background-color: #ffedd5;
            color: #9a3412;
        }

        /* Orange */

        .role-dev {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .role-design {
            background-color: #fce7f3;
            color: #9d174d;
        }

        .role-test {
            background-color: #f3e8ff;
            color: #6b21a8;
        }

        .role-hr {
            background-color: #e0f2fe;
            color: #075985;
        }

        /* Action Reveal Styles */
        .action-reveal {
            opacity: 0;
            transform: translateX(10px);
            transition: all 0.2s ease;
            pointer-events: none;
        }

        .accordion-header:hover .action-reveal,
        .pack-card:hover .action-reveal {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            background: #fff;
            border: 1px solid #e2e8f0;
        }

        .action-btn:hover {
            background-color: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-delete:hover {
            color: #dc2230;
            border-color: #fca5a5;
            background-color: #fef2f2;
        }

        .btn-edit:hover {
            color: #2563eb;
            border-color: #93c5fd;
            background-color: #eff6ff;
        }

        /* Premium Execution View */
        .execution-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #f8fafc;
            z-index: 3000;
            display: flex;
            flex-direction: column;
            animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }

            to {
                transform: translateY(0);
            }
        }

        .execution-header {
            background: #fff;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .execution-body {
            flex: 1;
            overflow-y: auto;
            padding: 3rem 2rem;
        }

        .execution-footer {
            background: #fff;
            padding: 1.5rem 2rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #fff;
            transition: all 0.2s ease;
        }

        .card-custom:hover {
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .active-selection {
            border: 2px solid #4f46e5 !important;
            background-color: #eef2ff !important;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.1) !important;
        }

        .active-selection i {
            color: #4f46e5 !important;
        }

        .active-selection h4 {
            color: #4f46e5 !important;
        }

        .question-nav {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(36px, 1fr));
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .nav-dot {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .nav-dot.active {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
        }

        .nav-dot.answered {
            background: #ecfdf5;
            color: #047857;
            border-color: #10b981;
        }

        .nav-dot.flagged {
            background: #fef3c7;
            color: #b45309;
            border-color: #f59e0b;
        }

        .timer-badge {
            background: #fef2f2;
            color: var(--brand);
            padding: 0.5rem 1rem;
            border-radius: 999px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid #fee2e2;
        }

        .evaluation-item {
            transition: all 0.2s;
        }

        .evaluation-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Sidebar Execution View */
        .exec-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 2rem;
            height: calc(100vh - 160px);
            overflow: hidden;
        }

        .exec-sidebar {
            background: #fff;
            border-left: 1px solid #e2e8f0;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            overflow-y: auto;
        }

        .sb-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem;
        }

        .sb-title {
            font-size: 11px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1rem;
        }

        .proctor-badge {
            background: #ecfdf5;
            color: #047857;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.05em;
            border: 1px solid #10b981;
        }

        .timer-box {
            background: #0f172a;
            color: #fff;
            padding: 1rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .timer-box.warning {
            background: var(--brand);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }

            100% {
                opacity: 1;
            }
        }

        /* Results Enhancements */
        .score-card {
            background: linear-gradient(135deg, #fef2f2 0%, #fff 100%);
            border: 1.5px solid var(--brand-soft);
            border-radius: 20px;
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 3rem;
        }

        .score-display {
            text-align: center;
            padding-right: 3rem;
            border-right: 1px solid #fee2e2;
        }

        .score-val {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--brand);
            line-height: 1;
        }

        .score-label {
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            margin-top: 0.5rem;
        }

        /* Wizard Styles */
        .modal-backdrop.show {
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            background: rgba(15, 23, 42, 0.5);
        }

        /* Strong blur on background content when result modal opens */
        body.result-modal-open > nav,
        body.result-modal-open > #main-content-area,
        body.result-modal-open > header,
        body.result-modal-open > main,
        body.result-modal-open > footer {
            filter: blur(8px) saturate(105%) !important;
            -webkit-filter: blur(8px) saturate(105%) !important;
            transition: filter 0.25s ease !important;
            pointer-events: none !important;
            user-select: none !important;
        }

        body.result-modal-open .modal-backdrop.show {
            -webkit-backdrop-filter: blur(10px) saturate(140%);
            backdrop-filter: blur(10px) saturate(140%);
            background: rgba(15, 23, 42, 0.55) !important;
        }

        #studentResultSummaryModal .modal-content {
            box-shadow: 0 30px 80px -20px rgba(15, 23, 42, 0.45),
                        0 12px 30px -10px rgba(220, 34, 48, 0.18) !important;
        }

        .modal-content {
            border-radius: 24px;
            border: none;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .wizard-step-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.02em;
        }

        .wizard-step-subtitle {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .stepper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }

        .stepper::before {
            content: '';
            position: absolute;
            top: 18px;
            left: 50px;
            right: 50px;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }

        .stepper .step {
            position: relative;
            z-index: 2;
            background: #fff;
            padding: 0 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .stepper .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #94a3b8;
            background: #fff;
            transition: all 0.3s;
        }

        .stepper .step-label {
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            transition: all 0.3s;
        }

        .stepper .step.active .step-circle {
            border-color: #dc2230;
            background: #dc2230;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(220, 34, 48, 0.1);
        }

        .stepper .step.active .step-label {
            color: #dc2230;
            font-weight: 700;
        }

        .stepper .step.completed .step-circle {
            border-color: #dc2230;
            background: #dc2230;
            color: #fff;
        }

        .stepper .step.completed .step-label {
            color: #dc2230;
        }

        .card-custom {
            background: var(--bg-card);
            border-radius: 10px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            padding: 20px;
            margin-bottom: 24px;
        }

        .btn {
            font-size: 13px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-custom {
            background-color: var(--primary-red);
            color: white !important;
            border: none;
            border-radius: 6px;
        }

        .btn-primary-custom:hover {
            background-color: #c62828;
            box-shadow: 0 4px 8px rgba(229, 57, 53, 0.2);
        }

        .btn-secondary-custom {
            background-color: white;
            color: var(--text-primary) !important;
            border: 1px solid var(--border-color);
            border-radius: 6px;
        }

        .btn-secondary-custom:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }

        .form-select,
        .form-control {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 10px 12px;
            font-size: 14px;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #dc2230;
            box-shadow: 0 0 0 3px rgba(220, 34, 48, 0.1);
        }

        /* ============================================
           MODERN GLOBAL FOCUS STYLE — Red Brand Ring
           ============================================ */
        .input,
        .select,
        textarea,
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="search"],
        input[type="tel"],
        input[type="url"],
        input[type="date"],
        input[type="time"],
        input[type="datetime-local"],
        input[type="month"],
        input[type="week"],
        input[type="file"],
        select {
            outline: none !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease !important;
        }

        .input:focus,
        .input:focus-visible,
        .select:focus,
        .select:focus-visible,
        textarea:focus,
        textarea:focus-visible,
        input[type="text"]:focus,
        input[type="text"]:focus-visible,
        input[type="email"]:focus,
        input[type="email"]:focus-visible,
        input[type="password"]:focus,
        input[type="password"]:focus-visible,
        input[type="number"]:focus,
        input[type="number"]:focus-visible,
        input[type="search"]:focus,
        input[type="search"]:focus-visible,
        input[type="tel"]:focus,
        input[type="tel"]:focus-visible,
        input[type="url"]:focus,
        input[type="url"]:focus-visible,
        input[type="date"]:focus,
        input[type="date"]:focus-visible,
        input[type="time"]:focus,
        input[type="time"]:focus-visible,
        input[type="datetime-local"]:focus,
        input[type="datetime-local"]:focus-visible,
        input[type="month"]:focus,
        input[type="month"]:focus-visible,
        input[type="week"]:focus,
        input[type="week"]:focus-visible,
        input[type="file"]:focus,
        input[type="file"]:focus-visible,
        select:focus,
        select:focus-visible {
            outline: none !important;
            border-color: #dc2230 !important;
            background-color: #ffffff !important;
            box-shadow:
                0 0 0 4px rgba(220, 34, 48, 0.12),
                0 1px 3px rgba(220, 34, 48, 0.08) !important;
        }

        /* Bootstrap form-control / form-select polish */
        .form-control,
        .form-select {
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .form-control:focus,
        .form-control:focus-visible,
        .form-select:focus,
        .form-select:focus-visible {
            outline: none !important;
            border-color: #dc2230 !important;
            box-shadow:
                0 0 0 4px rgba(220, 34, 48, 0.12),
                0 1px 3px rgba(220, 34, 48, 0.08) !important;
        }

        /* Kill the default blue browser autofill focus */
        input:-webkit-autofill:focus,
        textarea:-webkit-autofill:focus,
        select:-webkit-autofill:focus {
            box-shadow:
                0 0 0 30px #ffffff inset,
                0 0 0 4px rgba(220, 34, 48, 0.12) !important;
            -webkit-text-fill-color: #0f172a !important;
        }

        /* Invalid focus state stays red but darker */
        .input.is-invalid:focus,
        .form-control.is-invalid:focus,
        input.is-invalid:focus,
        textarea.is-invalid:focus,
        select.is-invalid:focus {
            border-color: #b91c1c !important;
            box-shadow:
                0 0 0 4px rgba(185, 28, 28, 0.15),
                0 1px 3px rgba(185, 28, 28, 0.1) !important;
        }

        /* Opt-out: inputs inside their own styled wrappers shouldn't get the red ring */
        .no-focus-ring,
        .no-focus-ring:focus,
        .no-focus-ring:focus-visible {
            outline: none !important;
            border-color: transparent !important;
            box-shadow: none !important;
            background-color: transparent !important;
        }

        /* Premium Execution View Styles */
        .badge-custom {
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
            border: 1px solid transparent;
        }

        .badge-green {
            background: #f0fdf4;
            color: #16a34a;
            border-color: #bbf7d0;
        }

        .badge-blue {
            background: #eff6ff;
            color: #2563eb;
            border-color: #93c5fd;
        }

        .badge-yellow {
            background: #fffbeb;
            color: #d97706;
            border-color: #fde68a;
        }

        .badge-red {
            background: #fef2f2;
            color: #dc2230;
            border-color: #fecdd3;
        }

        .badge-purple {
            background: #faf5ff;
            color: #7e22ce;
            border-color: #e9d5ff;
        }

        .exec-header {
            background: #fff;
            padding: 0.75rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .exec-header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .exec-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .exec-logo {
            width: 32px;
            height: 32px;
            background: var(--brand);
            color: #fff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .exec-brand-text {
            font-weight: 800;
            font-size: 1.1rem;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .proctor-badge {
            background: #ecfdf5;
            color: #059669;
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #10b981;
        }

        .exec-divider {
            width: 1px;
            height: 32px;
            background: #e2e8f0;
        }

        .exec-test-name {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .exec-test-step {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
        }

        .exec-header-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .btn-simulate-custom {
            background: #fff1f2;
            color: #e11d48;
            border: 1px solid #fecdd3;
            font-weight: 700;
            font-size: 12px;
            border-radius: 8px;
        }

        .btn-simulate-custom:hover {
            background: #ffe4e6;
        }

        .exec-timer-box-custom {
            background: #0f172a;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 120px;
        }

        .exec-timer-box-custom.warning {
            background: var(--brand);
            animation: pulse-timer 1s infinite;
        }

        @keyframes pulse-timer {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        .timer-icon-custom {
            color: #94a3b8;
            font-size: 1.1rem;
        }

        .timer-values-custom {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .btn-submit-test-custom {
            background: var(--brand);
            color: #fff;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            padding: 0.6rem 1.25rem;
            border: none;
        }

        .exec-container-custom {
            display: flex;
            height: calc(100vh - 60px);
            overflow: hidden;
            background: #f8fafc;
        }

        .exec-content-custom {
            flex: 1;
            padding: 2.5rem;
            overflow-y: auto;
        }

        .exec-content-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .exec-sidebar-custom {
            width: 360px;
            background: #fff;
            border-left: 1px solid #e2e8f0;
            padding: 1.5rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .question-card-custom-v2 {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .q-card-header-custom {
            padding: 1.5rem 2rem;
            background: #fafbfc;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .q-meta-group-custom {
            display: flex;
            gap: 0.5rem;
        }

        .q-id-pill-custom {
            background: #f1f5f9;
            color: #475569;
            font-weight: 800;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .q-type-pill-custom {
            background: #eff6ff;
            color: #2563eb;
            font-weight: 800;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .q-marks-pill-custom {
            background: #f0fdf4;
            color: #16a34a;
            font-weight: 800;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .q-category-pill-custom {
            background: #fff7ed;
            color: #ea580c;
            font-weight: 800;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .btn-flag-custom {
            border: none;
            background: transparent;
            color: #94a3b8;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-flag-custom.btn-primary-custom {
            color: #f59e0b;
        }

        .q-body-custom {
            padding: 2.5rem 2rem;
        }

        .q-text-custom {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.5;
            margin-bottom: 0.5rem;
        }

        .q-hint-custom {
            font-size: 0.875rem;
        }

        .options-grid {
            display: grid;
            gap: 1rem;
        }

        .option-item {
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .option-item:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .option-item.selected {
            border-color: var(--brand);
            background: #fef2f2;
        }

        .option-circle {
            width: 20px;
            height: 20px;
            border: 2px solid #cbd5e1;
            border-radius: 50%;
            position: relative;
        }

        .option-square {
            width: 20px;
            height: 20px;
            border: 2px solid #cbd5e1;
            border-radius: 4px;
            position: relative;
        }

        .option-item.selected .option-circle::after {
            content: '';
            position: absolute;
            inset: 3px;
            background: var(--brand);
            border-radius: 50%;
        }

        .option-item.selected .option-square::after {
            content: '\F26B';
            font-family: 'bootstrap-icons';
            position: absolute;
            inset: -2px;
            color: var(--brand);
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .option-text {
            font-weight: 600;
            font-size: 0.95rem;
            color: #334155;
        }

        .exec-footer-custom {
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-nav-prev-custom {
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            padding: 0.75rem 1.5rem;
            color: #475569;
        }

        .btn-nav-next-custom {
            background: var(--brand);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            padding: 0.75rem 2rem;
            color: #fff;
        }

        .save-status-custom {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 600;
        }

        .save-dot-custom {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
        }

        .exec-sidebar-custom {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .sidebar-card-custom {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .sb-title-custom {
            font-size: 11px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1.25rem;
        }

        .progress-info-custom {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 0.75rem;
        }

        .progress-percent-custom {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
        }

        .progress-label-custom {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .exec-progress-gradient-custom {
            background: linear-gradient(90deg, #dc2230 0%, #ef4444 100%);
            border-radius: 10px;
        }

        .sb-stats-custom {
            display: flex;
            gap: 1.5rem;
        }

        .sb-stat-item-custom {
            display: flex;
            flex-direction: column;
        }

        .sb-stat-val-custom {
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
        }

        .sb-stat-label-custom {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
        }

        .navigator-grid-custom-v2 {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.5rem;
        }

        .nav-item {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            border: 1.5px solid transparent;
        }

        .nav-item.unanswered {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #64748b;
        }

        .nav-item.current {
            background: #fff;
            border-color: var(--brand);
            color: var(--brand);
            box-shadow: 0 0 0 3px rgba(220, 34, 48, 0.1);
        }

        .nav-item.answered {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #16a34a;
        }

        .nav-item.flagged {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #ea580c;
        }

        .nav-legend-custom {
            margin-top: 1.25rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .legend-item-custom {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-transform: uppercase;
        }

        .leg-dot-custom {
            width: 10px;
            height: 10px;
            border-radius: 3px;
        }

        .leg-dot-custom.current {
            background: #fff;
            border: 1.5px solid var(--brand);
        }

        .leg-dot-custom.answered {
            background: #f0fdf4;
            border: 1.5px solid #bbf7d0;
        }

        .leg-dot-custom.flagged {
            background: #fff7ed;
            border: 1.5px solid #fed7aa;
        }

        .leg-dot-custom.unanswered {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
        }

        .instructions-card-custom {
            background: #fefce8;
            border-color: #fef08a;
        }

        .instructions-card-custom .sb-title-custom {
            color: #a16207;
        }

        .sb-list-custom {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .sb-list-custom li {
            font-size: 12px;
            color: #854d0e;
            font-weight: 500;
            display: flex;
            gap: 0.5rem;
        }

        .sb-list-custom li::before {
            content: '•';
            color: #eab308;
            font-weight: 900;
        }

        .submission-success-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            z-index: 4000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-card {
            background: #fff;
            border-radius: 24px;
            padding: 3rem;
            max-width: 480px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .success-icon-wrapper {
            width: 80px;
            height: 80px;
            background: #f0fdf4;
            color: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1.5rem;
        }

        .success-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.75rem;
        }

        .success-text {
            color: #64748b;
            margin-bottom: 2rem;
        }

        .success-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .btn-view-results {
            background: var(--brand);
            color: #fff;
            border-radius: 12px;
            font-weight: 700;
            padding: 1rem;
            border: none;
            text-align: center;
        }

        .custom-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }

        .custom-modal {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 2rem;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }

        .submit-confirm-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 4000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .confirm-card {
            background: #fff;
            border-radius: 20px;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .confirm-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1rem;
        }

        .confirm-text {
            font-size: 0.95rem;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .confirm-warning {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-bottom: 2rem;
        }

        .confirm-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn-confirm-cancel {
            background: #f1f5f9;
            color: #475569;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            padding: 0.75rem;
        }

        .btn-confirm-yes {
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            padding: 0.75rem;
        }

        .violation-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.9);
            z-index: 5000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .violation-overlay.active {
            display: flex;
        }

        .violation-card {
            background: #fff;
            border-radius: 24px;
            padding: 2.5rem;
            max-width: 440px;
            width: 90%;
            text-align: center;
        }

        .violation-icon {
            font-size: 3.5rem;
            color: #ef4444;
            margin-bottom: 1rem;
        }

        /* Print Optimization */
        @media print {

            /* Hide everything except the preview modal content */
            body>*:not(#paperPreviewModal) {
                display: none !important;
            }

            .modal-backdrop,
            .modal-header,
            .modal-footer,
            button,
            .btn {
                display: none !important;
            }

            #paperPreviewModal {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                display: block !important;
                visibility: visible !important;
            }

            #paperPreviewModal .modal-dialog {
                max-width: 100% !important;
                margin: 0 !important;
                width: 100% !important;
            }

            #paperPreviewModal .modal-content {
                border: none !important;
                box-shadow: none !important;
                background: white !important;
            }

            #previewPaperContent {
                display: block !important;
                visibility: visible !important;
                padding: 0 !important;
                overflow: visible !important;
                max-height: none !important;
            }

            #previewPaperContent>div {
                box-shadow: none !important;
                padding: 20px !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: none !important;
                background: white !important;
            }

            /* Ensure colors and backgrounds print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            /* Prevent section breaks in the middle of a question */
            .mb-5 {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            /* Clean up the paper appearance for print */
            .bg-[#f8fafc],
            .bg-[#fef2f2] {
                background-color: transparent !important;
            }

            .border-dashed {
                border-style: solid !important;
                border-width: 1px !important;
                border-color: #e2e8f0 !important;
            }
        }
    </style>
    <style>
        /* Template Specific Styles */
        .template-table th {
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1.5rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .template-table td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .template-info-cell {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .template-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #fff1f2;
            color: #dc2230;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .template-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.95rem;
            margin-bottom: 2px;
        }

        .template-sub {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .section-count-badge {
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.75rem;
            color: #475569;
            min-width: 40px;
            text-align: center;
        }

        .duration-text {
            font-weight: 800;
            color: #1e293b;
            font-size: 0.9rem;
        }

        .duration-text span {
            color: #94a3b8;
            font-weight: 500;
            font-size: 0.75rem;
            margin-left: 4px;
        }

        .marks-pill {
            background: #fff1f2;
            color: #dc2230;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.75rem;
            border: 1px solid #fee2e2;
        }

        .date-text {
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .action-icon-btn {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            transition: all 0.2s;
        }

        .action-icon-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .action-icon-btn.view:hover {
            color: #2563eb;
            border-color: #93c5fd;
            background: #eff6ff;
        }

        .action-icon-btn.edit:hover {
            color: #059669;
            border-color: #6ee7b7;
            background: #ecfdf5;
        }

        .action-icon-btn.delete:hover {
            color: #dc2230;
            border-color: #fca5a5;
            background: #fef2f2;
        }

        /* Template Builder Sidebar Redesign */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
        }

        .sidebar-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .sidebar-panel {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            width: 95%;
            max-width: 1250px;
            height: 90vh;
            background: #fff;
            z-index: 10001;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            border-radius: 24px;
            opacity: 0;
            visibility: hidden;
            overflow: hidden;
        }

        .sidebar-panel.open {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, -50%) scale(1);
        }

        .builder-header {
            padding: 1.25rem 2rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            flex-shrink: 0;
        }

        .builder-main {
            display: grid;
            grid-template-columns: 390px 1fr;
            flex: 1;
            overflow: hidden;
        }

        .builder-sidebar {
            background: #f8fafc;
            border-right: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .builder-content {
            background: white;
            padding: 2.5rem 3rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
        }

        .sidebar-header {
            padding: 1.5rem 2rem;
            background: white;
            border-bottom: 1px solid #f1f5f9;
        }

        .sidebar-list {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            gap: 4px;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
        }

        .filter-tab {
            flex: 1;
            text-align: center;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-tab:hover {
            color: #1e293b;
        }

        .filter-tab.active {
            background: white;
            color: var(--brand);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Template Item Card */
        .template-item-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 14px;
            padding: 1.05rem 1.1rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
        }

        .template-item-card:hover {
            border-color: var(--brand);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(220, 34, 48, 0.05);
        }

        .template-item-card.active {
            border-color: var(--brand);
            background: #fff1f2;
        }

        .template-item-card.active::after {
            content: '';
            position: absolute;
            left: -1px;
            top: 20%;
            bottom: 20%;
            width: 3px;
            background: var(--brand);
            border-radius: 0 4px 4px 0;
        }

        /* Template Action Icons */
        .template-card-actions {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            gap: 6px;
            opacity: 0;
            transition: all 0.2s;
            background: white;
            padding: 4px;
            border-radius: 10px;
            box-shadow: -4px 0 12px rgba(255, 255, 255, 0.9);
            z-index: 20;
        }

        .template-item-card:hover .template-card-actions,
        .template-card:hover .template-card-actions {
            opacity: 1;
        }

        .action-icon-btn {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #f1f5f9;
            background: #fff;
            color: #64748b;
            transition: all 0.2s;
            cursor: pointer;
            padding: 0;
        }

        .action-icon-btn i {
            font-size: 13px;
        }

        .action-icon-btn:hover {
            color: #fff;
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-clone:hover {
            background: #3b82f6;
        }

        .btn-edit:hover {
            background: #10b981;
        }

        .btn-delete:hover {
            background: #ef4444;
        }

        .builder-field-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .builder-field-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        /* Section Cards */
        .section-builder-card {
            background: #fff;
            border: 1.5px solid #f1f5f9;
            border-radius: 16px;
            padding: 1.25rem;
            transition: all 0.2s;
        }

        .section-builder-card:hover {
            border-color: #e2e8f0;
        }

        .section-drag-handle {
            cursor: grab;
            color: #cbd5e1;
        }

        .template-item {
            padding: 1rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s;
        }

        .template-item:hover {
            border-color: #cbd5e1;
            background: #f1f5f9;
        }

        .template-item.active {
            background: #4b2a63;
            /* Purple from image */
            border-color: #4b2a63;
        }

        .template-item.active .template-item-name {
            color: white;
        }

        .template-item.active .action-icon {
            color: white;
        }

        .template-item-name {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            flex: 1;
            margin-right: 1rem;
        }

        .action-icon {
            font-size: 14px;
            color: #94a3b8;
            padding: 4px;
            transition: color 0.2s;
        }

        .action-icon:hover {
            color: #dc2230;
        }

        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .filter-tab {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid #e2e8f0;
            background: white;
            color: #64748b;
            cursor: pointer;
        }

        .filter-tab.active {
            background: #4b2a63;
            color: white;
            border-color: #4b2a63;
        }

        .builder-field-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .builder-field-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.5rem;
        }

        .section-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
        }

        .section-item-drag {
            cursor: grab;
            color: #cbd5e1;
        }

        .section-item-content {
            display: grid;
            grid-template-columns: 100px 2fr 1fr 1fr 1fr;
            gap: 1rem;
            flex: 1;
            align-items: center;
        }

        .section-delete {
            color: #dc2230;
            cursor: pointer;
            padding: 4px;
        }

        .builder-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            background: white;
        }

        .btn-purple {
            background: #4b2a63;
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
        }

        .btn-outline-gray {
            border: 1px solid #e2e8f0;
            color: #64748b;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
        }

        .search-container {
            position: relative;
            max-width: 300px;
        }

        .search-input {
            width: 100%;
            background: #f3f4f6;
            border: none;
            border-radius: 999px;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            font-size: 13px;
            font-weight: 500;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1.5rem;
        }

        .pagination-info {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }

        .pagination-btns {
            display: flex;
            gap: 4px;
        }

        .page-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #475569;
            transition: all 0.2s;
        }

        .page-btn:hover:not(:disabled) {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .page-btn.active {
            background: #dc2230;
            color: #fff;
            border-color: #dc2230;
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Accordion & Pack Styles */
        .accordion-header {
            cursor: pointer;
            transition: background 0.2s;
            padding: 0.75rem 1.5rem !important;
        }

        .accordion-header:hover {
            background: #f1f5f9;
        }

        .accordion-content {
            display: none;
            padding: 1rem 1.5rem;
            background: #fff;
            border-top: 1px solid #f1f5f9;
        }

        .accordion-content.open {
            display: block;
        }

        .pack-item-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem;
            background: #f8fafc;
            transition: all 0.2s;
            display: grid;
            grid-template-columns: 1fr 1.5fr 1fr 1fr auto;
            gap: 1.5rem;
            align-items: center;
        }

        .pack-item-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            background: #fff;
        }

        .pack-name-badge {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #fff1f2;
            color: #dc2230;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .badge-status {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-active {
            background: #ecfdf5;
            color: #059669;
        }

        .status-draft {
            background: #f1f5f9;
            color: #64748b;
        }

        .pack-assigned-avatars {
            display: flex;
            align-items: center;
        }

        .pack-assigned-avatars img {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid #fff;
            margin-left: -8px;
        }

        .pack-assigned-avatars img:first-child {
            margin-left: 0;
        }

        .pack-assigned-count {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            margin-left: 8px;
        }

        /* New Paper Preview Styles */
        .paper-preview-container {
            background: #fff;
            border-radius: 0;
            padding: 40px;
            box-shadow: none;
        }

        .paper-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            border-left: 5px solid #dc2230;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            border: 1px solid #f1f5f9;
            border-left: 5px solid #dc2230;
        }

        .q-badge {
            background: #fff1f2;
            color: #dc2230;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }

        .marks-text {
            color: #475569;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .option-box {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            transition: all 0.2s;
        }

        .option-letter {
            width: 26px;
            height: 26px;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            color: #94a3b8;
            background: #f8fafc;
        }

        .instruction-box {
            background: #fff1f2;
            border-left: 5px solid #dc2230;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .preview-pill {
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .section-divider {
            border-top: 2px solid #dc2230;
            margin: 30px 0;
            width: 100%;
        }

        .section-title {
            color: #dc2230;
            font-weight: 800;
            letter-spacing: 0.05em;
        }

        .more-questions-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 600;
            margin-top: 20px;
        }

        @media print {
            .paper-preview-container {
                padding: 0 !important;
            }

            .paper-card {
                break-inside: avoid;
            }
        }

        /* Batch Tab List */
        .pack-table th {
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .pack-table td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Question Bank Modal Styles */
        .qb-sidebar {
            width: 342px;
            background: #f8fafc;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .qb-sidebar-header {
            padding: 20px 22px 16px;
            border-bottom: 1px solid #e2e8f0;
            background: #fff;
        }

        .qb-sidebar-title-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .qb-sidebar-title-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff1f2;
            color: var(--brand);
            border: 1px solid #ffe4e6;
            font-size: 14px;
            flex-shrink: 0;
        }

        .qb-sidebar-title-row h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            line-height: 1.2;
            color: #0b1220;
        }

        .qb-sidebar-title-row p {
            margin: 2px 0 0;
            font-size: 10px;
            line-height: 1.2;
            letter-spacing: .12em;
            text-transform: uppercase;
            font-weight: 800;
            color: #475569;
        }

        .qb-sidebar-search {
            position: relative;
        }

        .qb-sidebar-search i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 11px;
        }

        .qb-sidebar-search input {
            width: 100%;
            height: 38px;
            padding: 0 12px 0 34px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            transition: all .18s ease;
            outline: none;
        }

        .qb-sidebar-search input:focus {
            background: #fff;
            border-color: #fecdd3;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, .08);
        }

        .qb-sidebar-list {
            flex: 1;
            overflow-y: auto;
            padding: 12px 14px 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .qb-bank-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            min-height: 54px;
        }

        .qb-bank-card:hover {
            border-color: var(--brand);
            transform: translateX(2px);
            background: #fff;
            box-shadow: 0 4px 10px -4px rgba(15, 23, 42, .18);
        }

        .qb-bank-card.active {
            border-color: var(--brand);
            background: #fef2f2;
            box-shadow: 0 3px 10px -2px rgba(220, 34, 48, 0.08);
        }

        .qb-bank-card.active .bank-icon {
            background: var(--brand);
            color: white;
            border-color: var(--brand);
        }

        .bank-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #ffffff;
            color: var(--brand);
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.2s;
            flex-shrink: 0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .qb-bank-card:hover .bank-icon {
            background: #fef2f2;
            border-color: #fecaca;
            color: var(--brand);
        }

        .qb-bank-main {
            min-width: 0;
            flex: 1;
        }

        .qb-bank-title {
            margin: 0;
            font-size: 13px;
            font-weight: 800;
            color: #0b1220;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .qb-bank-meta {
            margin-top: 3px;
            font-size: 10px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .1em;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .qb-bank-delete {
            width: 24px;
            height: 24px;
            border-radius: 7px;
            border: 0;
            background: #fef2f2;
            color: #ef4444;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.18s ease;
            margin-left: 6px;
        }

        .qb-bank-delete:hover {
            background: #ef4444;
            color: #fff;
        }

        .qb-section-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .qb-section-header {
            padding: 16px 24px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .qb-question-item {
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s;
        }

        .qb-question-item:last-child {
            border-bottom: none;
        }

        .qb-question-item:hover {
            background: #fbfcfe;
        }

        .upload-zone {
            border: 2px dashed #e2e8f0;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            background: #fcfdfe;
            transition: all 0.2s;
            cursor: pointer;
        }

        .upload-zone:hover {
            border-color: var(--brand);
            background: #fef2f2;
        }

        .qb-blur-overlay {
            backdrop-filter: blur(8px);
            background: rgba(15, 23, 42, 0.6) !important;
        }

        .correct-opt-selector.active {
            border-color: #dc2230 !important;
            background: #fef2f2;
        }

        .correct-opt-selector.active .selector-dot {
            opacity: 1 !important;
            transform: scale(1.1);
        }

        .animate-shake {
            animation: shake 0.5s cubic-bezier(.36, .07, .19, .97) both;
        }

        @keyframes shake {

            10%,
            90% {
                transform: translate3d(-1px, 0, 0);
            }

            20%,
            80% {
                transform: translate3d(2px, 0, 0);
            }

            30%,
            50%,
            70% {
                transform: translate3d(-4px, 0, 0);
            }

            40%,
            60% {
                transform: translate3d(4px, 0, 0);
            }
        }

        .dropdown-menu {
            animation: dropdownFade 0.3s ease-out;
            transform-origin: top right;
            border-radius: 24px !important;
            border: 1px solid rgba(226, 232, 240, 0.5) !important;
            padding: 10px !important;
            min-width: 240px !important;
        }

        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .dropdown-item {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 12px 16px !important;
            transition: all 0.2s ease !important;
        }

        .dropdown-item i {
            font-size: 1.1rem;
        }

        .dropdown-item:hover {
            background-color: #f8fafc !important;
            color: #dc2230 !important;
        }
    </style>



</head>

<body class="min-h-screen">

    <!-- eNova Navigation -->
    <nav class="navbar">
        <div class="nav-left">
            <button class="icon-btn" style="border:none;background:transparent;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div class="nav-logo">
                <svg width="32" height="32" viewBox="0 0 40 40" fill="none">
                    <circle cx="20" cy="20" r="18" fill="var(--brand)" />
                    <path d="M12 20C12 15.5817 15.5817 12 20 12" stroke="white" stroke-width="4"
                        stroke-linecap="round" />
                    <path d="M28 20C28 24.4183 24.4183 28 20 28" stroke="white" stroke-width="4"
                        stroke-linecap="round" />
                </svg>
                <text>eNova Administration Portal</text>
            </div>
        </div>

        <div class="nav-right">
            <div class="nav-center">
                <div class="nav-item active">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    HOME
                </div>
                <div class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                        <polyline points="10 9 9 9 8 9" />
                    </svg>
                    TICKETS
                </div>
                <div class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    TIMESHIFT
                </div>
                <div class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="1" />
                        <circle cx="19" cy="12" r="1" />
                        <circle cx="5" cy="12" r="1" />
                    </svg>
                    MORE
                </div>
            </div>

            <div class="nav-right-divider"></div>

            <div class="dropdown">
                <div class="user-profile dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <img src="https://i.pravatar.cc/150?u=logesh" class="user-avatar" alt="Avatar">
                    <span>Logeshwaran S</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow-2xl border-0 rounded-3xl p-2 mt-2"
                    aria-labelledby="profileDropdown">
                    <li class="px-3 py-2 border-b border-slate-50 mb-1">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Signed in as</div>
                        <div class="text-[12px] font-bold text-slate-800">Admin User</div>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2xl py-2.5 px-4 text-[13px] font-bold text-slate-700 hover:bg-red-50 hover:text-red-600 transition-all flex items-center gap-3"
                            href="javascript:void(0)" onclick="window.switchMainTab('management')">
                            <i class="bi bi-speedometer2 text-lg"></i>
                            Admin Dashboard
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2xl py-2.5 px-4 text-[13px] font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-all flex items-center gap-3"
                            href="javascript:void(0)" onclick="window.switchMainTab('execution')">
                            <i class="bi bi-mortarboard text-lg"></i>
                            Student Dashboard
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2xl py-2.5 px-4 text-[13px] font-bold text-slate-700 hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center gap-3"
                            href="javascript:void(0)" onclick="window.switchMainTab('results'); switchResultView('student');">
                            <i class="bi bi-bar-chart-line text-lg"></i>
                            Test Score
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2xl py-2.5 px-4 text-[13px] font-bold text-slate-700 hover:bg-violet-50 hover:text-violet-600 transition-all flex items-center gap-3"
                            href="javascript:void(0)" onclick="window.switchMainTab('results'); switchResultView('evaluator');">
                            <i class="bi bi-clipboard-check text-lg"></i>
                            Evaluator View
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider border-slate-50 mx-2">
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2xl py-2.5 px-4 text-[13px] font-bold text-slate-400 hover:bg-slate-50 hover:text-slate-600 transition-all flex items-center gap-3"
                            href="javascript:void(0)">
                            <i class="bi bi-box-arrow-right text-lg"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>



    <!-- MAIN CONTENT CONTAINER -->
    <div id="main-content-area">

        <!-- 1. Test MANAGEMENT HUB -->
        <main id="tab-content-management" class="px-8 py-6">


            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-2xl font-bold">Test Inventory</h3>
                    <p class="text-sm text-gray-500">Manage your Test lifecycle from headers to Batches.</p>
                </div>
                <div class="flex gap-3">

                    <button id="btn_new_test_toggle" class="btn-red-rounded px-6 inline-flex items-center gap-2" onclick="toggleCreateTest()">
                        <span class="btn-step-circle">1</span>
                        <span id="btn_new_test_label">New Test Name</span>
                    </button>
                    <button class="btn-red-rounded px-6 inline-flex items-center gap-2" onclick="window.openQuestionBankModal()">
                        <span class="btn-step-circle">2</span>
                        Question Bank
                    </button>
                    <button class="btn-red-rounded px-6 inline-flex items-center gap-2" onclick="openQuickTemplateModal()">
                        <span class="btn-step-circle">3</span>
                        Create Template
                    </button>
                </div>
            </div>

            <!-- Create / Edit Test: full-width slide-down sheet (same field IDs / JS hooks) -->
            <div id="TestModal" class="test-form-inline-panel" role="region" aria-label="Create or edit test">
                <div class="test-form-slide-inner">
                    <div class="test-create-sheet">
                        <div class="flex justify-between items-start gap-4 mb-8">
                            <div>
                                <h3 class="text-xl font-extrabold text-slate-800 tracking-tight">Create New Test</h3>
                                <p id="test_form_subtitle" class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1.5 mb-0">Configure your assessment basic details</p>
                            </div>
                            <button type="button" class="text-slate-400 hover:text-red-500 transition-colors shrink-0 p-1" onclick="closeModal('TestModal')" aria-label="Close">
                                <i class="bi bi-x-lg text-xl"></i>
                            </button>
                        </div>

                        <input type="hidden" id="ass_code" value="" />

                        <!-- Row 1: compact balanced horizontal layout -->
                        <div id="test_form_row1" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-3 mb-6 items-end">
                            <div class="form-group min-w-0">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Test
                                    Name <span class="text-red-500">*</span></label>
                                <input id="ass_name" class="input h-11 w-full bg-slate-50 border-slate-100 rounded-xl px-3 text-sm"
                                    placeholder="e.g., Technical Proficiency Test" />
                                <span class="error-msg hidden" id="err_ass_name">Test name is required</span>
                            </div>
                            <div class="form-group min-w-0">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Test
                                    Category <span class="text-red-500">*</span></label>
                                <select id="ass_category" class="select h-11 w-full bg-slate-50 border-slate-100 rounded-xl px-3 text-sm"
                                    onchange="toggleEnovaFields(this.value)">
                                    <option value="Enova">Enova</option>
                                    <option value="HR Recruitment-Fresher">HR Recruitment-Fresher</option>
                                </select>
                                <span class="error-msg hidden" id="err_ass_category">Please select a category</span>
                            </div>
                            <div id="enova_extra_fields" class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:col-span-2 xl:col-span-2 xl:grid-cols-2 min-w-0">
                                <div class="form-group min-w-0">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Test
                                        Type <span class="text-red-500">*</span></label>
                                    <select id="ass_type" class="select h-11 w-full bg-slate-50 border-slate-100 rounded-xl px-3 text-sm">
                                        <option value="">Select type...</option>
                                        <option>Technical</option>
                                        <option>Compliance</option>
                                        <option>Behavioral</option>
                                    </select>
                                    <span class="error-msg hidden" id="err_ass_type">Test type is required</span>
                                </div>
                                <div class="form-group min-w-0">
                                    <label
                                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Assign
                                        To <span class="text-red-500">*</span></label>
                                    <div class="custom-multiselect" id="ass_assigned_container">
                                        <button type="button"
                                            class="multiselect-btn h-11 min-h-[2.75rem] max-h-[2.75rem] bg-slate-50 border-slate-100 rounded-xl px-3 text-sm w-full leading-tight"
                                            id="multiselect_btn" onclick="toggleMultiselect()">
                                            <span id="multiselect_label">-- Select Roles --</span>
                                            <i class="bi bi-chevron-down text-xs opacity-60"></i>
                                        </button>
                                        <div class="multiselect-options" id="multiselect_options">
                                            <label
                                                class="ms-option font-bold text-slate-800 border-b border-slate-100 rounded-none mb-0 pb-1.5">
                                                <input type="checkbox" id="select_all_roles" onchange="selectAllRoles(this)"> Select
                                                All
                                            </label>
                                            <label class="ms-option"><input type="checkbox" value="Developers"
                                                    onchange="updateMultiselectLabel()"> Developers</label>
                                            <label class="ms-option"><input type="checkbox" value="Designers"
                                                    onchange="updateMultiselectLabel()"> Designers</label>
                                            <label class="ms-option"><input type="checkbox" value="Testers"
                                                    onchange="updateMultiselectLabel()"> Testers</label>
                                            <label class="ms-option"><input type="checkbox" value="HR"
                                                    onchange="updateMultiselectLabel()"> HR</label>
                                            <label class="ms-option"><input type="checkbox" value="Client Advocate"
                                                    onchange="updateMultiselectLabel()"> Client Advocate</label>
                                        </div>
                                    </div>
                                    <select id="ass_assigned" class="hidden" multiple>
                                        <option value="Developers">Developers</option>
                                        <option value="Designers">Designers</option>
                                        <option value="Testers">Testers</option>
                                        <option value="HR">HR</option>
                                        <option value="Client Advocate">Client Advocate</option>
                                    </select>
                                    <span class="error-msg hidden" id="err_ass_assigned">Please assign to at least one role</span>
                                </div>
                            </div>
                            <div class="form-group min-w-0">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Add
                                    Video <span class="text-red-500">*</span></label>
                                <select id="ass_add_video" class="select h-11 w-full bg-slate-50 border-slate-100 rounded-xl px-3 text-sm"
                                    onchange="syncIntroVideoUploadColumn()">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                                <span class="error-msg hidden" id="err_ass_add_video">Please choose an option</span>
                            </div>
                            <div id="ass_intro_upload_col" class="hidden form-group min-w-0">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Upload
                                    Videos <span class="text-red-500">*</span></label>
                                <div class="h-11 w-full flex items-center gap-2 px-2 rounded-xl border border-slate-100 bg-slate-50">
                                    <input type="file" id="ass_intro_video_input" accept="video/*" multiple class="hidden"
                                        onchange="onAssIntroVideoFilesChange(this)" />
                                    <button type="button"
                                        id="ass_intro_video_browse_btn"
                                        class="px-2 py-1 rounded-lg border border-slate-200 bg-white text-[9px] font-black uppercase tracking-wider text-slate-600 hover:border-red-200 hover:text-red-600 shrink-0 leading-none"
                                        onclick="document.getElementById('ass_intro_video_input').click()">
                                        Browse
                                    </button>
                                    <span id="ass_intro_video_count" class="text-[10px] font-bold text-slate-500 whitespace-nowrap">0 / 5</span>
                                </div>
                                <span class="error-msg hidden" id="err_ass_intro_videos">Add at least one video or set Add Video to No</span>
                            </div>
                            <input id="ass_pass_mark" type="hidden" value="60" />
                        </div>

                        <!-- Row 2: Instruction (left) + Exam configurations 2×2 (right), equal column height -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 mb-8 items-stretch">
                            <div class="flex flex-col gap-1.5 min-h-0 h-full">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0">Instruction
                                    <span class="text-red-500">*</span></label>
                                <textarea id="ass_desc" class="input bg-slate-50 border-slate-100 rounded-xl p-4 text-sm w-full flex-1 min-h-[12rem] lg:min-h-0 resize-none"
                                    rows="7" placeholder="Briefly provide instructions..."></textarea>
                                <span class="error-msg hidden" id="err_ass_desc">Instruction is required</span>
                            </div>
                            <div class="flex flex-col gap-1.5 min-h-0 h-full">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0">Exam
                                    configurations</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1 auto-rows-fr min-h-[12rem] lg:min-h-0">
                                    <div class="test-exam-config-card">
                                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-red-500 shrink-0 shadow-sm">
                                            <i class="bi bi-shield-check text-lg"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-[10px] font-black text-slate-800 uppercase tracking-wide">Proctored Exam</div>
                                            <p class="text-[10px] text-slate-500 font-medium mb-0 leading-snug">AI &amp; camera monitoring</p>
                                        </div>
                                        <div class="form-check form-switch mb-0 ps-0">
                                            <input class="form-check-input ms-0" type="checkbox" id="test_form_proctored" checked role="switch">
                                        </div>
                                    </div>
                                    <div class="test-exam-config-card">
                                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-red-500 shrink-0 shadow-sm">
                                            <i class="bi bi-lock-fill text-lg"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-[10px] font-black text-slate-800 uppercase tracking-wide">Browser Lockdown</div>
                                            <p class="text-[10px] text-slate-500 font-medium mb-0 leading-snug">Restrict tab switches</p>
                                        </div>
                                        <div class="form-check form-switch mb-0 ps-0">
                                            <input class="form-check-input ms-0" type="checkbox" id="test_form_lockdown" role="switch">
                                        </div>
                                    </div>
                                    <div class="test-exam-config-card">
                                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-red-500 shrink-0 shadow-sm">
                                            <i class="bi bi-eye-fill text-lg"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-[10px] font-black text-slate-800 uppercase tracking-wide">Show Results</div>
                                            <p class="text-[10px] text-slate-500 font-medium mb-0 leading-snug">Instant score display</p>
                                        </div>
                                        <div class="form-check form-switch mb-0 ps-0">
                                            <input class="form-check-input ms-0" type="checkbox" id="test_form_show_results" role="switch">
                                        </div>
                                    </div>
                                    <div class="test-exam-config-card">
                                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-red-500 shrink-0 shadow-sm">
                                            <i class="bi bi-skip-backward-fill text-lg"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-[10px] font-black text-slate-800 uppercase tracking-wide">Allow Backtrack</div>
                                            <p class="text-[10px] text-slate-500 font-medium mb-0 leading-snug">Navigate between questions</p>
                                        </div>
                                        <div class="form-check form-switch mb-0 ps-0">
                                            <input class="form-check-input ms-0" type="checkbox" id="test_form_backtrack" role="switch">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-slate-100">
                            <button type="button"
                                class="px-8 py-3 bg-slate-50 text-slate-600 font-bold rounded-xl hover:bg-slate-100 transition-all text-sm w-full sm:w-auto"
                                onclick="closeModal('TestModal')">Cancel</button>
                            <button type="button"
                                class="px-8 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all text-sm flex items-center justify-center gap-2 shadow-lg shadow-red-100 w-full sm:w-auto"
                                onclick="createTest()">
                                Create Test <i class="bi bi-rocket-takeoff"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tests DataTable -->
            <div class="card overflow-hidden border-slate-200 mb-12">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-0">Test Headers</h4>
                    <div class="flex items-center gap-4">
                        <div
                            class="flex items-center gap-3 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
                            <i class="bi bi-filter text-slate-400 text-[10px]"></i>
                            <select id="typeFilter"
                                class="text-[10px] font-black text-slate-500 border-0 focus:ring-0 cursor-pointer bg-transparent uppercase tracking-wider p-0"
                                onchange="filterByType(this.value)">
                                <option value="">All Types</option>
                                <option value="Technical">Technical</option>
                                <option value="Compliance">Compliance</option>
                                <option value="Behavioral">Behavioral</option>
                            </select>
                        </div>
                        <div
                            class="flex items-center gap-3 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm w-[250px]">
                            <i class="bi bi-search text-slate-400 text-[10px]"></i>
                            <input type="text"
                                class="no-focus-ring text-[10px] font-black text-slate-500 border-0 focus:ring-0 p-0 w-full placeholder:text-slate-300 uppercase tracking-wider"
                                placeholder="Search tests..." oninput="searchTests(this.value)">
                        </div>
                    </div>
                </div>
                <div class="table-responsive p-0">
                    <table id="TestsDataTable" class="w-full text-left">
                        <thead class="bg-white border-b border-slate-100">
                            <tr>
                                <th class="hidden">ID</th>
                                <th class="w-12"></th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                    Test Name</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                    Category</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                    Type</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                    Assigned Roles</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                    Batches</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>


        </main>




        <main id="tab-content-results" class="hidden px-8 py-6">
            <div class="flex items-start justify-between gap-4 mb-8">
                <div>
                    <button
                        class="mb-4 px-4 py-2 bg-slate-100 text-slate-600 rounded-lg font-bold text-xs flex items-center gap-2 hover:bg-slate-200 transition-all border border-slate-200 shadow-sm"
                        onclick="window.backFromResultsPage()">
                        <i class="bi bi-arrow-left"></i> Back to Inventory
                    </button>
                    <h3 class="text-2xl font-bold">Results & Evaluation</h3>
                    <p class="text-sm text-gray-500">Review candidate performance and grade subjective answers.</p>
                </div>
                <div id="resultsOverviewCards" class="w-full xl:w-[36%]">
                    <h5 class="text-[10px] font-black text-[#1e293b] mb-2">Evaluation Overview</h5>
                    <div class="grid grid-cols-4 gap-2">
                        <div class="bg-[#f8fbff] border border-[#dbeafe] rounded-lg p-2 border-t-[3px] border-t-[#3b82f6] min-h-[62px] flex flex-col justify-between">
                            <p class="text-[8px] font-black text-[#64748b] uppercase tracking-[0.12em] text-center mb-0">Total Score</p>
                            <p id="resSummaryTotalScore" class="text-[16px] font-black text-[#0f172a] leading-none text-center mb-0">0</p>
                        </div>
                        <div class="bg-[#f0fdf4] border border-[#dcfce7] rounded-lg p-2 border-t-[3px] border-t-[#16a34a] min-h-[62px] flex flex-col justify-between">
                            <p class="text-[8px] font-black text-[#64748b] uppercase tracking-[0.12em] text-center mb-0">Overall Pass %</p>
                            <p id="resSummaryPassPct" class="text-[16px] font-black text-[#16a34a] leading-none text-center mb-0">0%</p>
                        </div>
                        <div class="bg-[#fef2f2] border border-[#fee2e2] rounded-lg p-2 border-t-[3px] border-t-[#dc2626] min-h-[62px] flex flex-col justify-between">
                            <p class="text-[8px] font-black text-[#64748b] uppercase tracking-[0.12em] text-center mb-0">Fail Count</p>
                            <p id="resSummaryFailCount" class="text-[16px] font-black text-[#dc2626] leading-none text-center mb-0">0</p>
                        </div>
                        <div class="bg-[#faf5ff] border border-[#f3e8ff] rounded-lg p-2 border-t-[3px] border-t-[#7c3aed] min-h-[62px] flex flex-col justify-between">
                            <p class="text-[8px] font-black text-[#64748b] uppercase tracking-[0.12em] text-center mb-0">Pending Count</p>
                            <p id="resSummaryPendingCount" class="text-[16px] font-black text-[#475569] leading-none text-center mb-0">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student View Container -->
            <div id="result-student-view" class="space-y-4">
                <div class="card p-4 border border-[#e2e8f0] shadow-sm rounded-[12px] bg-white hidden">
                    <div class="flex flex-wrap items-center justify-between gap-6">
                        <div class="flex-1 min-w-[320px]">
                            <label
                                class="text-[9px] font-bold text-[#94a3b8] uppercase tracking-widest mb-1 block">Candidate
                                Performance Dashboard</label>
                            <div class="text-[15px] font-bold text-[#1e293b]" id="results_dashboard_title"></div>
                            <div class="text-[11px] text-[#94a3b8] font-medium mt-0.5" id="results_dashboard_subtitle">No completed submissions yet</div>
                        </div>

                        <div class="flex items-center gap-8 border-l border-[#f1f5f9] pl-8">
                            <div class="text-center">
                                <div class="text-[24px] font-bold text-[#dc2230] leading-none" id="resTotalScore">82
                                </div>
                                <div class="text-[9px] font-bold text-[#94a3b8] uppercase mt-1.5 tracking-wider">Total
                                    Score</div>
                            </div>
                            <div class="text-center">
                                <div class="text-[18px] font-bold text-[#10b981]" id="resPercentage">82%</div>
                                <div class="text-[9px] font-bold text-[#94a3b8] uppercase mt-1.5 tracking-wider">
                                    Accuracy</div>
                            </div>
                            <div class="text-center">
                                <div class="text-[18px] font-bold text-[#475569]" id="resTimeTaken">78m</div>
                                <div class="text-[9px] font-bold text-[#94a3b8] uppercase mt-1.5 tracking-wider">
                                    Duration</div>
                            </div>
                            <div class="pl-4">
                                <span id="resStatusBadge"
                                    class="bg-[#f0fdf4] text-[#16a34a] border border-[#bbf7d0] px-3 py-1 rounded-[6px] font-bold text-[10px] uppercase">✓
                                    PASS</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border border-[#e2e8f0] shadow-sm rounded-[12px] overflow-hidden bg-white">
                    <div class="px-5 py-3 border-b border-[#f1f5f9] bg-[#f8fafc]/50 flex items-center justify-between gap-3 flex-wrap">
                        <h4 class="text-[12px] font-bold text-[#1e293b] mb-0 uppercase tracking-wide">Candidate Ranking
                            & Leaderboard</h4>
                        <span
                            class="text-[10px] font-bold text-[#94a3b8] bg-white border border-[#e2e8f0] px-2 py-0.5 rounded"
                            id="breakdown-cat-count">0 Candidates</span>
                    </div>
                    <div class="px-5 py-3 border-b border-[#f1f5f9] bg-white">
                        <div class="grid grid-cols-1 xl:grid-cols-[1fr_auto] gap-3 items-center">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2">
                                <select id="resultsTypeFilter" onchange="App.loadCandidateResult()"
                                    class="h-9 text-[11px] font-bold text-[#475569] bg-white border border-[#e2e8f0] px-3 rounded-lg">
                                    <option value="">All Test Types</option>
                                </select>
                                <select id="resultsTestFilter" onchange="App.loadCandidateResult()"
                                    class="h-9 text-[11px] font-bold text-[#475569] bg-white border border-[#e2e8f0] px-3 rounded-lg">
                                    <option value="">All Test Names</option>
                                </select>
                                <select id="resultsGroupFilter" onchange="App.loadCandidateResult()"
                                    class="h-9 text-[11px] font-bold text-[#475569] bg-white border border-[#e2e8f0] px-3 rounded-lg">
                                    <option value="">All Groups</option>
                                </select>
                                <select id="resultsDateFilter" onchange="App.loadCandidateResult()"
                                    class="h-9 text-[11px] font-bold text-[#475569] bg-white border border-[#e2e8f0] px-3 rounded-lg">
                                    <option value="">All Dates</option>
                                </select>
                                <select id="resultsSortFilter" onchange="App.loadCandidateResult()"
                                    class="h-9 text-[11px] font-bold text-[#475569] bg-white border border-[#e2e8f0] px-3 rounded-lg">
                                    <option value="high">Sort By</option>
                                    <option value="low">Lowest to Highest</option>
                                </select>
                            </div>
                            <div class="relative min-w-[220px]">
                                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-[11px]"></i>
                                <input id="resultsCandidateSearch" type="text" placeholder="Search candidate..."
                                    oninput="App.loadCandidateResult()"
                                    class="w-full h-9 text-[11px] font-semibold text-[#334155] bg-white border border-[#e2e8f0] rounded-lg pl-9 pr-3 outline-none focus:border-[#dc2230] transition-all">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-[#f8fafc] border-b border-[#f1f5f9]">
                                <tr>
                                    <th
                                        class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest">
                                        Candidate Name</th>
                                    <th
                                        class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-center">
                                        Test Type</th>
                                    <th
                                        class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-center">
                                        Role</th>
                                    <th
                                        class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-center">
                                        Status</th>
                                    <th
                                        class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-right">
                                        Marks</th>
                                    <th
                                        class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-right">
                                        Overall %</th>
                                    <th
                                        class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-center">
                                        Pass / Fail</th>
                                    <th
                                        class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-center">
                                        Evaluate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#f1f5f9]" id="topicBreakdownTable">
                                <!-- Dynamic content -->
                            </tbody>
                        </table>
                    </div>
                    <div id="resultsLeaderboardPagination" class="px-5 py-3 border-t border-[#f1f5f9] bg-white flex items-center justify-end gap-2"></div>
                </div>
            </div>

            <!-- Evaluator View Container -->
            <div id="result-evaluator-view" class="hidden space-y-4">
                <div class="card border border-[#e2e8f0] shadow-sm rounded-[12px] overflow-hidden bg-white">
                    <div
                        class="px-5 py-2.5 border-b border-[#f1f5f9] bg-[#f8fafc]/50 flex justify-between items-center">
                        <h4 class="text-[12px] font-bold text-[#1e293b] mb-0 tracking-wide uppercase">Subjective
                            Evaluation Required</h4>
                    </div>
                    <div id="pendingEvaluationList" class="p-5 space-y-4">
                        <!-- List of questions requiring manual marking -->
                    </div>
                </div>
            </div>
        </main>

        <!-- REPORTS TAB -->
        <main id="tab-content-reports" class="hidden px-8 py-10">
            <div class="card p-8 text-center">
                <h3 class="text-lg font-bold">Analytics Reports</h3>
                <p class="text-gray-500">Detailed insights and trends.</p>
            </div>
        </main>

        <!-- 5. EXECUTION VIEW TAB (Live Tests) -->
        <main id="tab-content-execution" class="hidden px-8 py-6">
            <div class="card-custom">
                <div class="px-4 py-3 bg-header border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <button
                            class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 hover:text-red-500 transition-all shadow-sm"
                            onclick="window.switchMainTab('management')" title="Back to Inventory">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h2 class="section-title mb-0">Scheduled & Live Tests</h2>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Test Name</th>
                                <th>Status</th>
                                <th>Schedule</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="execution_dashboard_body">
                            <!-- Dynamic content will be injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

    </div>

    <div id="templateBuilderOverlay" class="sidebar-overlay" onclick="closeTemplateBuilder()"></div>
    <div id="templateBuilderSidebar" class="sidebar-panel">
        <!-- Builder Header -->
        <div class="builder-header">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-600">
                    <i class="bi bi-layers-fill text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 mb-0">Template Builder</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Create & Manage Test
                        Structures</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="btn-outline py-2 px-4 rounded-xl text-xs flex items-center gap-2"
                    onclick="closeTemplateBuilder()">
                    <i class="bi bi-x-lg"></i> Cancel
                </button>
                <button class="btn-red-rounded py-2 px-6 rounded-xl text-xs shadow-lg shadow-red-100"
                    onclick="saveTemplateBuilder()">
                    <i class="bi bi-check-lg"></i> Save Changes
                </button>
            </div>
        </div>

        <!-- Builder Main Body -->
        <div class="builder-main">
            <!-- Sidebar: Discovery -->
            <div class="builder-sidebar">
                <div class="sidebar-header">
                    <h5 class="text-[12px] font-black text-slate-800 uppercase tracking-widest mb-4">Discovery</h5>
                    <div class="relative">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                        <input type="text" id="sidebar_search"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-11 pr-4 text-[12px] font-semibold text-slate-700 focus:ring-2 focus:ring-red-100 focus:border-red-200 transition-all"
                            placeholder="Search templates..." oninput="searchSidebar(this.value)" />
                    </div>
                </div>
                <div id="sidebar_list" class="sidebar-list">
                    <!-- Templates loaded via JS -->
                </div>
                <div class="p-4 border-top border-slate-100 bg-white">
                    <button
                        class="w-full py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-[12px] font-bold uppercase tracking-widest transition-all border border-dashed border-slate-300"
                        onclick="resetBuilder()">
                        <i class="bi bi-plus-lg mr-2"></i> Create New
                    </button>
                </div>
            </div>

            <!-- Editor Content -->
            <div class="builder-content">
                <div class="space-y-8">
                    <!-- Meta Info -->
                    <section>
                        <div class="flex items-center gap-2 mb-6">
                            <div class="w-1 h-5 bg-red-500 rounded-full"></div>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-0">General
                                Configuration</h4>
                        </div>

                        <div class="builder-field-grid">
                            <div class="form-group">
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Template
                                    Category</label>
                                <select id="builder_category"
                                    class="form-select w-full bg-slate-50 border-slate-100 rounded-xl text-xs h-11">
                                    <option>Performance</option>
                                    <option>Compliance</option>
                                    <option>General</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Template
                                    Identity</label>
                                <input id="builder_storage_name"
                                    class="form-control w-full bg-slate-50 border-slate-100 rounded-xl text-xs h-11"
                                    placeholder="e.g. Research Ethics Test 2024" />
                            </div>
                        </div>

                        <div class="builder-field-grid-3 mt-4">
                            <div class="form-group">
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Default
                                    Duration</label>
                                <div class="relative">
                                    <input type="number" id="builder_duration"
                                        class="form-control w-full bg-slate-50 border-slate-100 rounded-xl text-xs h-11 pr-12"
                                        placeholder="60" />
                                    <span
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400">MINS</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Start
                                    Constraint</label>
                                <input type="date" id="builder_start_date"
                                    class="form-control w-full bg-slate-50 border-slate-100 rounded-xl text-xs h-11" />
                            </div>
                            <div class="form-group">
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">End
                                    Constraint</label>
                                <input type="date" id="builder_end_date"
                                    class="form-control w-full bg-slate-50 border-slate-100 rounded-xl text-xs h-11" />
                            </div>
                        </div>
                    </section>

                    <!-- Structure Area -->
                    <section>
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-5 bg-blue-500 rounded-full"></div>
                                <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-0">Question
                                    Paper Structure</h4>
                            </div>
                            <div
                                class="flex items-center gap-4 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Total Marks</span>
                                    <span class="text-xs font-black text-slate-800" id="builder_total_marks">0
                                        Marks</span>
                                </div>
                                <div class="w-px h-6 bg-slate-200"></div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Sections</span>
                                    <span class="text-xs font-black text-slate-800" id="builder_section_count">0
                                        Sections</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-6 mb-6">
                            <label class="block text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-3">Add
                                New Section Component</label>
                            <div class="relative">
                                <select id="task_selector"
                                    class="form-select w-full h-12 bg-white border-blue-200 rounded-xl text-xs pl-5 pr-10 appearance-none cursor-pointer shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all"
                                    onchange="addSelectedSection(this.value)">
                                    <option value="">Browse section blueprints..</option>
                                    <option value="MCQ">Multiple Choice Questions (MCQ)</option>
                                    <option value="descriptive">Descriptive question</option>
                                    <option value="Coding">Coding / Practical Section</option>
                                </select>
                                <div
                                    class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-blue-400">
                                    <i class="bi bi-plus-circle-fill text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div id="builder_sections_container" class="space-y-4">
                            <!-- Dynamic Sections -->
                            <div class="empty-state py-12 text-center bg-slate-50 border border-dashed border-slate-200 rounded-3xl"
                                id="builder_empty_state">
                                <div
                                    class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 text-slate-300">
                                    <i class="bi bi-stack text-3xl"></i>
                                </div>
                                <h5 class="text-sm font-bold text-slate-600 mb-1">No Sections Added</h5>
                                <p class="text-xs text-slate-400">Select a section blueprint above to start building
                                    your paper structure</p>
                            </div>
                        </div>
                    </section>

                    <!-- Footer Summary/Actions -->
                    <section class="mt-8 pt-8 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="builder_is_active" checked>
                                    <label class="form-check-label text-xs font-bold text-slate-600"
                                        for="builder_is_active">Set as Default Template</label>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Last Sync
                                </p>
                                <p class="text-xs font-bold text-slate-600" id="builder_last_sync">Not saved yet</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>



    <!-- Candidate Picker Modal -->
    <div id="candidatePickerModal" class="custom-modal-backdrop" style="z-index: 10006;"
        onclick="if(event.target===this)closeModal('candidatePickerModal')">
        <div class="custom-modal max-w-lg">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-800 mb-1">Assign Candidates</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest" id="cp_role_label">
                        Developers</p>
                </div>
                <button
                    class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors"
                    onclick="closeModal('candidatePickerModal')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500" id="cp_count_label">0 selected</span>
                    <button class="text-[10px] font-black text-red-500 uppercase tracking-widest hover:underline"
                        onclick="selectAllCandidates(true)">Select All</button>
                </div>
                <div class="max-h-[300px] overflow-y-auto border border-slate-100 rounded-xl p-2 bg-slate-50/30"
                    id="cp_list_container">
                    <!-- Populated via JS -->
                </div>
            </div>

            <div class="flex gap-3">
                <button class="btn-outline flex-1" onclick="closeModal('candidatePickerModal')">Cancel</button>
                <button class="btn-red-rounded flex-1 justify-center" onclick="confirmCandidateSelection()">Confirm
                    Assignment</button>
            </div>
        </div>
    </div>

    <!-- Question Bank Modal (Now Full Screen Template) -->
    <div id="QuestionBankModal" class="custom-modal-backdrop qb-template-mode"
        onclick="if(event.target===this)closeQuestionBankModal()">
        <div class="bg-white w-full h-screen flex flex-col overflow-hidden">
            <!-- Global Top Header (Matches Create Template Style) -->
            <div class="px-8 py-3 bg-white border-b sticky top-0 z-50 flex items-center justify-between shadow-sm flex-shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-9 h-9 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-100">
                        <i class="bi bi-journal-bookmark-fill text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-800 leading-tight">Question Bank Manager</h3>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Discovery & Repository Control</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button id="qbCreateBankBtn" class="px-7 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg shadow-red-100"
                        onclick="promptCreateQB()">
                        <i class="bi bi-plus-lg text-xs"></i> Create Question Bank
                    </button>
                    <button class="px-8 py-2.5 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg shadow-red-100 flex items-center gap-2"
                        onclick="closeQuestionBankModal()"><i class="bi bi-arrow-left text-[14px] font-black leading-none"></i>Back</button>
                </div>
            </div>

            <div class="flex flex-1 overflow-hidden" style="min-height: 0;">
                <!-- Sidebar -->
                <div class="qb-sidebar">
                    <div class="qb-sidebar-header">
                        <div class="qb-sidebar-title-row">
                            <span class="qb-sidebar-title-icon">
                                <i class="bi bi-stack"></i>
                            </span>
                            <div>
                                <h4>Question Bank</h4>
                                <p>Question Bank</p>
                            </div>
                        </div>
                    <div class="qb-sidebar-search">
                        <i class="bi bi-search"></i>
                        <input type="text" placeholder="Search banks..."
                            oninput="filterBanks(this.value)">
                    </div>
                </div>
                <div class="qb-sidebar-list custom-scrollbar" id="qbList">
                    <!-- Bank items will be rendered here -->
                </div>
                <!-- Create button moved to top right -->
            </div>

            <!-- Workspace -->
                <!-- Workspace -->
                <div class="flex-1 flex flex-col bg-slate-50/50 overflow-hidden relative" id="qbWorkspace">
                    <div class="flex-1 overflow-y-auto relative custom-scrollbar pb-12">
                        
                        <!-- Sticky Navigation Arrows (Relative to screen width) -->
                        <div id="qbNavigationArrows" class="hidden sticky top-[45vh] h-0 flex justify-between items-center pointer-events-none z-50 px-[4%]">
                            <button class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transition-all shadow-xl active:scale-90 pointer-events-auto border-4 border-white" onclick="navigateQBCategory(-1)">
                                <i class="bi bi-chevron-left text-lg"></i>
                            </button>
                            <button class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transition-all shadow-xl active:scale-90 pointer-events-auto border-4 border-white" onclick="navigateQBCategory(1)">
                                <i class="bi bi-chevron-right text-lg"></i>
                            </button>
                        </div>

                        <div class="px-[5%] mt-6 space-y-6">
                            <!-- Main Content Card (Matches Create Template Style) -->
                            <div class="card bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden p-8 min-h-[420px] space-y-8">
                                
                                <!-- Header Bar: Active Info & Stats -->
                                <div id="qbUnifiedHeader" class="flex items-center justify-between">
                                    <div class="flex items-center gap-6">
                                        <!-- Bank Title Block -->
                                        <div class="flex items-center gap-4 bg-transparent px-6 py-3.5 rounded-3xl min-w-[280px]">
                                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-red-600 shadow-sm border border-slate-50">
                                                <i class="bi bi-journal-text text-xl"></i>
                                            </div>
                                            <div>
                                                <h3 id="activeQBName" class="text-lg font-black text-slate-800 mb-0 leading-tight">Select a Bank</h3>
                                                <p id="activeQBSubtitle" class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mb-0"></p>
                                            </div>
                                        </div>

                                        <!-- Dashboard Stats -->
                                        <div id="qbHeaderStats" class="hidden bg-slate-50/50 px-8 py-3 rounded-3xl border border-slate-100 flex items-center gap-10 animate-fadeIn">
                                            <div class="text-center">
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Sections</p>
                                                <p id="activeQBSectionsCount" class="text-xl font-black text-slate-800 leading-none">0</p>
                                            </div>
                                            <div class="w-px h-8 bg-slate-200"></div>
                                            <div class="text-center">
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Questions</p>
                                                <p id="activeQBQuestionsCount" class="text-xl font-black text-red-600 leading-none">0</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Button Row -->
                                    <div class="flex items-center gap-4"></div>
                                </div>

                                <!-- Controls Row: Tabs & Bulk Actions -->
                                <div id="qbSecondaryControls" class="hidden flex items-center justify-between bg-transparent px-8 py-1 rounded-2xl animate-fadeIn">
                                    <div id="qbCategoryTabs" class="flex gap-10"></div>
                                    <div id="qbHeaderActions" class="flex items-center gap-3"></div>
                                </div>

                                <!-- State Containers -->
                                <div id="qbEmptyState" class="py-20 flex flex-col items-center justify-center text-center animate-fadeIn">
                                    <div class="w-24 h-24 bg-white rounded-[40px] shadow-xl flex items-center justify-center mb-8 border border-slate-100">
                                        <i class="bi bi-journal-bookmark text-5xl text-slate-100"></i>
                                    </div>
                                    <h4 class="text-2xl font-black text-slate-700 mb-4">No Bank Selected</h4>
                                    <p class="text-base text-slate-400 max-w-sm mx-auto font-medium leading-relaxed">Please select a question bank from the left panel to begin managing your repository.</p>
                                </div>

                                <div id="qbContentArea" class="hidden pb-10 animate-fadeIn"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action Bar -->
                    <div id="qbFooter" class="hidden px-[5%] py-3 border-t border-slate-100 bg-white flex justify-end items-center shadow-[0_-15px_60px_-20px_rgba(0,0,0,0.08)] z-10 shrink-0">
                        <button class="px-10 py-3 bg-red-600 text-white rounded-xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-red-200 hover:bg-red-700 hover:scale-[1.02] active:scale-95 transition-all"
                            onclick="saveAllQBDetails()">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: NEW Batch -->
    <div id="testPackModal" class="custom-modal-backdrop" onclick="if(event.target===this)closeModal('testPackModal')">
        <div class="custom-modal">
            <h3 class="text-xl font-extrabold mb-4">Create Batch</h3>
            <input type="hidden" id="tp_ass_id" />
            <div class="grid gap-4 mb-4">
                <input id="tp_name" class="input" placeholder="Pack Name" />
                <select id="tp_role" class="select">
                    <option>Designer</option>
                    <option>Developer</option>
                    <option>HR</option>
                    <option>Digital Marketing</option>
                </select>
                <select id="tp_template" class="select">
                    <?php foreach ($templates as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= esc($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button class="btn-ghost" onclick="closeModal('testPackModal')">Cancel</button>
                <button class="btn-red" onclick="createTestPack()">Save Pack</button>
            </div>
        </div>
    </div>

    <!-- MODAL: ASSIGN QUESTIONS (Refactored to Standard Bootstrap for better interaction) -->
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 px-6 pt-6 pb-0">
                    <div class="w-full">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-xl font-extrabold text-slate-800" id="assign_modal_title">Manage Batch</h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <p id="assign_subtitle" class="text-sm text-gray-500 font-medium"></p>

                        <!-- Template Selection Section -->
                        <div id="assign_template_section"
                            class="mt-4 p-4 bg-[#f8fafc] rounded-xl border border-slate-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <label
                                        class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Selected
                                        Template</label>
                                    <button type="button"
                                        class="bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center border-0 p-0 hover:bg-red-600 transition-all shadow-sm"
                                        onclick="openTemplateBuilder()" title="Create New Template">
                                        <i class="bi bi-plus text-xs"></i>
                                    </button>
                                </div>
                                <span id="template_change_status"
                                    class="text-[10px] font-bold text-green-600 hidden italic">Changes saved!</span>
                            </div>
                            <div class="flex gap-3">
                                <select id="edit_pack_template_id"
                                    class="form-select h-12 text-sm flex-1 bg-white border-slate-200 rounded-lg shadow-sm">
                                    <option value="">-- No Template (Custom Questions Only) --</option>
                                    <?php if (!empty($templates)):
                                        foreach ($templates as $t): ?>
                                            <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                                        <?php endforeach; endif; ?>
                                </select>
                                <button class="btn btn-primary-custom px-6 h-12 text-xs font-bold rounded-lg shadow-sm"
                                    onclick="updatePackTemplate()">
                                    Update Template
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body px-6 pt-4">
                    <!-- Sub-tabs per question type -->
                    <div class="flex gap-2 mb-6 bg-slate-100 p-1.5 rounded-xl inline-flex">
                        <button id="btn-assign-mcq" class="tab tab-active px-8 py-2.5 rounded-lg"
                            onclick="App.switchAssignTab('assign-mcq')">MCQ</button>
                        <button id="btn-assign-2m" class="tab tab-idle px-8 py-2.5 rounded-lg"
                            onclick="App.switchAssignTab('assign-2m')">DESCRIPTIVE QUESTION</button>
                    </div>

                    <!-- MCQ panel -->
                    <div id="assign-mcq" class="assign-panel">
                        <!-- Bulk Upload Section -->
                        <div class="card p-6 mb-8 flex items-center justify-between flex-wrap gap-4"
                            style="background:#fff; border: 2px dashed #e2e8f0; border-radius: 16px;">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shadow-sm">
                                    <i class="bi bi-cloud-arrow-up-fill text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-base font-extrabold text-slate-800">MCQ Bulk Upload</div>
                                    <div class="text-slate-400 text-[10px] mt-0.5 uppercase font-black tracking-widest">
                                        CSV format required</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="Test/downloadTemplate/mcq"
                                    class="btn btn-secondary-custom h-12 text-xs px-6 font-bold border-slate-200 rounded-lg">DOWNLOAD
                                    TEMPLATE</a>
                                <form action="Test/uploadQuestions" method="POST" enctype="multipart/form-data"
                                    class="flex gap-2 m-0">
                                    <input type="hidden" name="test_pack_id" class="assign_tp_id_input" />
                                    <input type="hidden" name="type" value="MCQ" />
                                    <input type="file" name="file" class="hidden" id="file_mcq"
                                        onchange="this.form.submit()" />
                                    <label for="file_mcq"
                                        class="btn btn-primary-custom h-12 text-xs px-8 cursor-pointer font-bold rounded-lg shadow-md hover:shadow-lg transition-all">UPLOAD
                                        CSV</label>
                                </form>
                            </div>
                        </div>

                        <!-- Manual Entry Section -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-1 h-6 bg-red-600 rounded-full"></div>
                                <h5 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-0">MANUAL MCQ
                                    ENTRY</h5>
                            </div>
                            <div class="grid gap-5">
                                <div>
                                    <label class="form-label text-[11px] font-bold text-[#64748b] uppercase tracking-wider mb-2">Pedagogy</label>
                                    <div class="pedagogy-combo relative w-full" data-pedagogy-base="assign_mcq_pedagogy">
                                        <input type="hidden" class="pedagogy-combo-hidden" value="">
                                        <input type="text" class="pedagogy-combo-search form-control h-12 text-sm border-slate-200 rounded-lg shadow-sm w-full" autocomplete="off" spellcheck="false" placeholder="Search or type pedagogy...">
                                        <div class="pedagogy-combo-panel mt-0.5 max-h-52 overflow-y-auto rounded-lg border border-slate-200 bg-white py-1 shadow-xl hidden"></div>
                                    </div>
                                </div>
                                <textarea id="mcq_content"
                                    class="form-control text-sm focus:ring-2 focus:ring-red-100 p-4 bg-slate-50 border-slate-200"
                                    placeholder="Type your question content here..." rows="4"
                                    style="border-radius: 12px;"></textarea>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-[11px] font-black text-slate-400">A</span>
                                        <input id="mcq_opt_a"
                                            class="form-control h-12 text-sm ps-10 border-slate-200 rounded-xl"
                                            placeholder="Option A" />
                                    </div>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-[11px] font-black text-slate-400">B</span>
                                        <input id="mcq_opt_b"
                                            class="form-control h-12 text-sm ps-10 border-slate-200 rounded-xl"
                                            placeholder="Option B" />
                                    </div>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-[11px] font-black text-slate-400">C</span>
                                        <input id="mcq_opt_c"
                                            class="form-control h-12 text-sm ps-10 border-slate-200 rounded-xl"
                                            placeholder="Option C" />
                                    </div>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-[11px] font-black text-slate-400">D</span>
                                        <input id="mcq_opt_d"
                                            class="form-control h-12 text-sm ps-10 border-slate-200 rounded-xl"
                                            placeholder="Option D" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                    <select id="mcq_correct"
                                        class="form-select h-12 text-sm font-bold border-slate-200 rounded-lg shadow-sm">
                                        <option value="">-- Choose Correct Answer --</option>
                                        <option value="A">Option A</option>
                                        <option value="B">Option B</option>
                                        <option value="C">Option C</option>
                                        <option value="D">Option D</option>
                                    </select>
                                    <button
                                        class="btn btn-primary-custom h-12 text-[12px] font-extrabold justify-center rounded-lg shadow-md hover:shadow-lg transition-all"
                                        onclick="App.addManualAssignQuestion('MCQ')">
                                        <i class="bi bi-plus-circle-fill me-1"></i> Add Question to Pack
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descriptive question panel -->
                    <div id="assign-2m" class="assign-panel hidden">
                        <!-- Bulk Upload Section -->
                        <div class="card p-6 mb-8 flex items-center justify-between flex-wrap gap-4"
                            style="background:#fff; border: 2px dashed #e2e8f0; border-radius: 16px;">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shadow-sm">
                                    <i class="bi bi-file-earmark-spreadsheet-fill text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-base font-extrabold text-slate-800">Descriptive question — bulk upload</div>
                                    <div class="text-slate-400 text-[10px] mt-0.5 uppercase font-black tracking-widest">
                                        CSV format required</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="Test/downloadTemplate/2m"
                                    class="btn btn-secondary-custom h-12 text-xs px-6 font-bold border-slate-200 rounded-lg">DOWNLOAD
                                    TEMPLATE</a>
                                <form action="Test/uploadQuestions" method="POST" enctype="multipart/form-data"
                                    class="flex gap-2 m-0">
                                    <input type="hidden" name="test_pack_id" class="assign_tp_id_input" />
                                    <input type="hidden" name="type" value="descriptive" />
                                    <input type="file" name="file" class="hidden" id="file_2m"
                                        onchange="this.form.submit()" />
                                    <label for="file_2m"
                                        class="btn btn-primary-custom h-12 text-xs px-8 cursor-pointer font-bold rounded-lg shadow-md hover:shadow-lg transition-all">UPLOAD
                                        CSV</label>
                                </form>
                            </div>
                        </div>

                        <!-- Manual Entry Section -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-1 h-6 bg-red-600 rounded-full"></div>
                                <h5 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-0">MANUAL
                                    DESCRIPTIVE QUESTION ENTRY</h5>
                            </div>
                            <div class="grid gap-5">
                                <div>
                                    <label class="form-label text-[11px] font-bold text-[#64748b] uppercase tracking-wider mb-2">Pedagogy</label>
                                    <div class="pedagogy-combo relative w-full" data-pedagogy-base="assign_m2_pedagogy">
                                        <input type="hidden" class="pedagogy-combo-hidden" value="">
                                        <input type="text" class="pedagogy-combo-search form-control h-12 text-sm border-slate-200 rounded-lg shadow-sm w-full" autocomplete="off" spellcheck="false" placeholder="Search or type pedagogy...">
                                        <div class="pedagogy-combo-panel mt-0.5 max-h-52 overflow-y-auto rounded-lg border border-slate-200 bg-white py-1 shadow-xl hidden"></div>
                                    </div>
                                </div>
                                <textarea id="m2_content" class="form-control text-sm p-4 bg-slate-50 border-slate-200"
                                    placeholder="Type the descriptive question..." rows="3"
                                    style="border-radius: 12px;"></textarea>
                                <textarea id="m2_correct" class="form-control text-sm p-4 bg-slate-50 border-slate-200"
                                    placeholder="Expected answer for evaluation..." rows="3"
                                    style="border-radius: 12px;"></textarea>
                                <div class="flex justify-end pt-2">
                                    <button
                                        class="btn btn-primary-custom h-12 px-10 text-[12px] font-extrabold justify-center rounded-lg shadow-md"
                                        onclick="App.addManualAssignQuestion('descriptive')">
                                        <i class="bi bi-plus-circle-fill me-1"></i> Add Question to Pack
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-6 pb-6">
                    <button type="button" class="btn btn-light px-8 py-2.5 font-bold text-slate-500"
                        data-bs-dismiss="modal">Close Panel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Execution View (Full Screen) -->
    <div id="executionView" class="execution-wrapper d-none">
        <!-- Top Sticky Header -->
        <header class="exec-header">
            <div class="exec-header-left">
                <div class="exec-brand">
                    <div class="exec-logo"><i class="bi bi-shield-check"></i></div>
                    <div class="exec-brand-text">AssessHub <span class="proctor-badge">PROCTORING ON</span></div>
                </div>
                <div class="exec-divider"></div>
                <div class="exec-test-info flex items-center gap-3">
                    <div id="execHeaderLogo"
                        class="w-10 h-10 bg-white rounded-lg border flex items-center justify-center text-brand font-bold shadow-sm overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&h=100&fit=crop"
                            class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h1 id="execTestTitle" class="exec-test-name">JavaScript Developer Test</h1>
                        <div id="execQuestionProgress" class="exec-test-step">Question 10 of 15</div>
                    </div>
                </div>
            </div>

            <!-- Center Stats: Total Marks, Pass Mark & Duration -->
            <div class="flex items-center gap-6 px-6 py-1 bg-[#f8fafc] border border-[#e2e8f0] rounded-xl shadow-sm">
                <div class="text-center px-4 border-e border-[#e2e8f0]">
                    <div id="execTotalMarks" class="text-sm font-extrabold text-[#1e293b] leading-tight">--</div>
                    <div class="text-[9px] font-bold text-[#94a3b8] uppercase tracking-wider">Total Marks</div>
                </div>
                <div class="text-center px-4 border-e border-[#e2e8f0]">
                    <div id="execPassMark" class="text-sm font-extrabold text-[#10b981] leading-tight">--</div>
                    <div class="text-[9px] font-bold text-[#94a3b8] uppercase tracking-wider">Pass Mark</div>
                </div>
                <div class="text-center px-4">
                    <div id="execTotalDuration" class="text-sm font-extrabold text-[#1e293b] leading-tight">--</div>
                    <div class="text-[9px] font-bold text-[#94a3b8] uppercase tracking-wider">Duration</div>
                </div>
            </div>

            <div class="exec-header-right">
                <div id="execTimer" class="exec-timer-box-custom">
                    <div class="timer-icon-custom"><i class="bi bi-clock"></i></div>
                    <div class="timer-values-custom">
                        <span id="timerText">--:--</span>
                    </div>
                </div>
                <button class="btn btn-submit-test-custom" onclick="App.confirmSubmit()">
                    Submit Test <i class="bi bi-send-fill ms-2"></i>
                </button>
            </div>
        </header>

        <!-- Main Content Layout -->
        <div class="exec-container-custom">
            <!-- Main Question Area -->
            <main class="exec-content-custom">
                <div class="exec-content-container">
                    <div class="question-card-custom-v2" id="questionCard">
                        <div class="q-card-header-custom">
                            <div class="q-meta-group-custom">
                                <span class="q-id-pill-custom" id="qIdxBadge">Q10</span>
                                <span class="q-category-pill-custom" id="qPedagogyBadge" title="Pedagogy">—</span>
                            </div>
                            <button class="btn btn-flag-custom" id="flagBtn" onclick="App.toggleFlagCurrent()">
                                <i class="bi bi-flag"></i> Flag Question
                            </button>
                        </div>

                        <div class="q-body-custom">
                            <h2 class="q-text-custom" id="qTextContent">
                                Which testing types are included in the software testing pyramid?
                            </h2>
                            <p class="q-hint-custom text-secondary mb-4">Select all that apply.</p>

                            <div id="answerArea" class="options-container-custom">
                                <!-- Options or Textarea injected here -->
                            </div>
                        </div>
                    </div>


                    <div id="execQuestionFooter" class="exec-footer-custom">
                        <button class="btn btn-nav-prev-custom" onclick="App.prevQuestion()">
                            <i class="bi bi-chevron-left me-2"></i> Previous Question
                        </button>
                        <button class="btn btn-nav-next-custom" id="nextQBtn" onclick="App.nextQuestion()">
                            Next Question <i class="bi bi-chevron-right ms-2"></i>
                        </button>
                    </div>

                    <div id="finalSubmissionPage" class="d-none mt-4 bg-white border border-slate-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-[14px] font-black text-slate-800 mb-0">Final Submission</h4>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Optional Upload</span>
                        </div>
                        <p class="text-[12px] text-slate-500 mb-3">You can upload one or multiple files before submitting. This is optional.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                            <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/50">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Single File</label>
                                <input type="file" id="execFinalSingleFileInput" class="form-control text-[12px]" onchange="App.onExecutionAttachmentsChange(this)">
                            </div>
                            <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/50">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Bulk Upload</label>
                                <input type="file" id="execFinalBulkFileInput" multiple class="form-control text-[12px]" onchange="App.onExecutionAttachmentsChange(this)">
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mb-2">Maximum file size: 2 MB each. Any format allowed.</p>
                        <div id="execFinalAttachmentList" class="space-y-1 mb-4"></div>

                        <div class="flex items-center justify-end gap-2">
                            <button class="btn btn-light px-4 py-2 text-[11px] font-bold" onclick="App.backToQuestionsFromFinal()">Back</button>
                            <button class="btn btn-danger px-5 py-2 text-[11px] font-black uppercase tracking-widest" onclick="App.submitTest()">Submit Test</button>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Sidebar -->
            <aside class="exec-sidebar-custom">
                <!-- Instructions Card -->
                <div class="sidebar-card-custom instructions-card-custom">
                    <h3 class="sb-title-custom"><i class="bi bi-info-circle me-2"></i> INSTRUCTIONS</h3>
                    <ul class="sb-list-custom">
                        <li>Questions can be revisited at any time.</li>
                        <li>Partial marks are available for multi-select.</li>
                        <li>Avoid refreshing the page during the exam.</li>
                    </ul>
                </div>

                <!-- Progress Section -->
                <div class="sidebar-card-custom">
                    <h3 class="sb-title-custom">OVERALL PROGRESS</h3>
                    <div class="sb-progress-container-custom">
                        <div class="progress-info-custom">
                            <span class="progress-percent-custom"><span id="answeredCount">8</span> of 15</span>
                            <span class="progress-label-custom">Answered</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px;">
                            <div id="execProgressBar" class="progress-bar exec-progress-gradient-custom"
                                style="width: 53%;"></div>
                        </div>
                    </div>
                    <div class="sb-stats-custom mt-3">
                        <div class="sb-stat-item-custom">
                            <span class="sb-stat-val-custom" id="flaggedCount">0</span>
                            <span class="sb-stat-label-custom">Flagged</span>
                        </div>
                    </div>
                </div>

                <!-- Navigator Section -->
                <div class="sidebar-card-custom">
                    <h3 class="sb-title-custom">QUESTION NAVIGATOR</h3>
                    <div id="navGrid" class="navigator-grid-custom-v2">
                        <!-- Navigation items injected here -->
                    </div>
                    <div class="nav-legend-custom">
                        <div class="legend-item-custom"><span class="leg-dot-custom current"></span> Current</div>
                        <div class="legend-item-custom"><span class="leg-dot-custom answered"></span> Answered</div>
                        <div class="legend-item-custom"><span class="leg-dot-custom flagged"></span> Flagged</div>
                        <div class="legend-item-custom"><span class="leg-dot-custom unanswered"></span> Unanswered</div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Orientation videos (outside execution view so intro shows before test UI) -->
    <div id="execIntroOverlay" class="d-none"
        style="position:fixed;inset:0;z-index:10050;background:rgba(15,23,42,.93);overflow-y:auto;">
        <div class="container py-4 px-3" style="max-width:920px;">
            <h2 class="text-white text-center fw-bold mb-2" style="font-size:1.35rem;">Orientation videos</h2>
            <p class="text-center text-slate-300 small mb-4 mb-md-5">Orientation screen is shown before the test starts. Click the button below when you're ready to begin.</p>
            <div id="execIntroVideosMount" class="d-flex flex-column gap-4 mb-4"></div>
            <div class="text-center pb-5">
                <button type="button" id="execIntroCompleteBtn" class="btn btn-lg btn-danger fw-bold px-5 rounded-pill shadow"
                    style="min-width:260px;" onclick="App.completeIntroGate()">
                    I've completed watching — Begin test
                </button>
            </div>
        </div>
    </div>

    <!-- Submission Success Overlay -->
    <div id="submissionSuccessOverlay" class="submission-success-overlay d-none">
        <div class="success-card">
            <div class="success-icon-wrapper">
                <i class="bi bi-check-lg"></i>
            </div>
            <h2 class="success-title">Test Submitted Successfully!</h2>
            <p class="success-text">Your answers have been recorded. Results will be available shortly.</p>
            <div class="success-actions">
                <button class="btn btn-view-results" onclick="App.backToDashboard()">Back to Dashboard</button>
            </div>
        </div>
    </div>

    <!-- Student: own result summary (Scheduled & Live Tests — View Results) -->
    <div class="modal fade" id="studentResultSummaryModal" tabindex="-1" aria-labelledby="studentResultSummaryTitleLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div>
                        <h5 class="modal-title fw-bold text-slate-800 mb-0" id="studentResultSummaryTitleLabel">Your results</h5>
                        <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mb-0 mt-1" id="studentResultSummarySubtitle"></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pt-2 pb-4" id="studentResultSummaryBody">
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    <div id="submitConfirmModal" class="submit-confirm-overlay d-none">
        <div class="confirm-card">
            <h3 class="confirm-title">Submit Test?</h3>
            <p class="confirm-text">
                You have answered <span id="confirmAnsweredCount" class="fw-bold">15</span> of
                <span id="confirmTotalCount" class="fw-bold">15</span> questions.
            </p>
            <div class="mb-3 text-start">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Attachment (Optional)</label>
                <input type="file" id="execSubmissionAttachmentInput" class="form-control text-[12px]" onchange="App.onExecutionAttachmentChange(this)">
                <p class="text-[10px] text-slate-400 mt-1 mb-0">Maximum file size: 2 MB. Any file format is allowed.</p>
                <p id="execSubmissionAttachmentMeta" class="text-[10px] text-slate-500 mt-1 mb-0"></p>
            </div>
            <p class="confirm-warning">
                Once submitted, you cannot make changes. Are you sure you want to submit?
            </p>
            <div class="confirm-actions">
                <button class="btn btn-confirm-cancel" onclick="App.hideSubmitConfirmation()">Cancel</button>
                <button class="btn btn-confirm-yes" onclick="App.submitTest()">Yes, Submit</button>
            </div>
        </div>
    </div>

    <div id="violationOverlay" class="violation-overlay">
        <div class="violation-card">
            <div class="violation-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <h3 class="fw-bold mb-2" id="violationTitle">Tab Switch Detected</h3>
            <p class="text-secondary-custom mb-4" id="violationMsg">
                You have switched away from the test window. This is a violation of the proctoring rules.
            </p>
            <div class="bg-light p-3 rounded mb-4">
                <div class="label-text mb-2">Violation Count</div>
                <div class="d-flex gap-2" id="violationDots">
                    <div class="flex-1 bg-danger rounded-pill" style="height: 6px;"></div>
                    <div class="flex-1 bg-light border rounded-pill" style="height: 6px;"></div>
                    <div class="flex-1 bg-light border rounded-pill" style="height: 6px;"></div>
                </div>
            </div>
            <button class="btn btn-primary-custom w-100 py-3" onclick="App.dismissViolation()">
                Dismiss & Continue
            </button>
        </div>
    </div>
    <script>
        // Global Data
        const App = {
            Tests: <?= workflow_view_json($Tests ?? []) ?>,
            templates: <?= workflow_view_json($templates ?? []) ?>,
            employees: <?= workflow_view_json($employees ?? []) ?>,
            QuestionBanks: <?= workflow_view_json($questionBank ?? []) ?>,
            selectedCandidates: {}, // Stores { TestId: [empId1, empId2] }
            manualQuestions: [],
            quickModePaperSource: null,
            resultsContextTestName: '',
            activeEvaluatorSubmissionKey: '',

            evaluationState: { submissions: {} },

            loadEvaluationState: function () {
                try {
                    const raw = localStorage.getItem('evaluationSubmissions');
                    if (!raw) return;
                    const parsed = JSON.parse(raw);
                    if (parsed && typeof parsed === 'object') {
                        App.evaluationState.submissions = parsed;
                    }
                } catch (e) {
                    console.warn('Failed to load evaluation state', e);
                }
            },

            saveEvaluationState: function () {
                try {
                    localStorage.setItem('evaluationSubmissions', JSON.stringify(App.evaluationState.submissions || {}));
                } catch (e) {
                    console.warn('Failed to persist evaluation state', e);
                }
            },

            loadCandidateResult: function () {},

            // Helper for deterministic/random shuffle
            shuffle: function (array) {
                let currentIndex = array.length, randomIndex;
                while (currentIndex != 0) {
                    randomIndex = Math.floor(Math.random() * currentIndex);
                    currentIndex--;
                    [array[currentIndex], array[randomIndex]] = [array[randomIndex], array[currentIndex]];
                }
                return array;
            },

            normalizeType: function (t) {
                if (!t) return '';
                t = t.toString().toLowerCase().trim();
                if (t.includes('mcq') || t.includes('multiple choice') || t.includes('objective') || t === '1') return 'mcq';
                if (
                    t === 'short'
                    || t.includes('short answer')
                    || t.includes('descriptive')
                    || t.includes('2-mark')
                    || t.includes('2mark')
                    || (t.includes('2') && t.includes('mark'))
                    || /^\u0032\s*marks?$/.test(t.trim())
                    || /^\u0032\s*$/.test(t.trim())
                ) {
                    return 'descriptive';
                }
                return t;
            },

            shuffleOptions: function (q) {
                const options = ['a', 'b', 'c', 'd'].map(key => ({
                    key: key.toUpperCase(),
                    value: q['option_' + key]
                })).filter(o => o.value && o.value.trim() !== '');

                // Fisher-Yates shuffle
                for (let i = options.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [options[i], options[j]] = [options[j], options[i]];
                }
                return options;
            },

            shuffleArray: function (array) {
                const newArray = [...array];
                for (let i = newArray.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
                }
                return newArray;
            },

            // New logic for randomized question selection during batch initialization
            generatePaperFromBank: function (qbId, templateOrBuilder) {
                const bank = QuestionBanks.find(b => b.id == qbId);
                if (!bank) return null;

                let sections = [];
                if (templateOrBuilder && typeof templateOrBuilder === 'object' && Array.isArray(templateOrBuilder.sections)) {
                    sections = templateOrBuilder.sections;
                } else {
                    const template = App.templates.find(t => t.id == templateOrBuilder);
                    if (!template) return null;
                    sections = getTemplateSections(template);
                }

                let allPickedQuestions = [];
                let availablePool = [...(bank.questions || [])];

                // Pre-shuffle the entire pool
                this.shuffle(availablePool);

                const warnings = [];
                const groupedBySection = [];

                sections.forEach((s, sIdx) => {
                    const count = parseInt(s.num_questions || s.count || 0);
                    const type = s.marks_type || s.type || 'MCQ';
                    const targetType = this.normalizeType(type);

                    let eligible = availablePool.filter(q => this.normalizeType(q.type) === targetType);

                    if (eligible.length < count) {
                        warnings.push(`Section "${s.section_name || s.name || type}" requires ${count} questions, but only ${eligible.length} available in bank.`);
                    }

                    // Pick questions and remove from pool
                    const rawPicked = eligible.slice(0, count);
                    const picked = rawPicked.map(q => normalizeQuestionRecord({
                        ...q,
                        sectionIdx: sIdx,
                        type: s.marks_type || s.type || q.type || 'MCQ',
                        marks: parseInt(s.marks_per_question || s.marks || q.marks || 0, 10) || 0
                    }, sIdx));
                    allPickedQuestions.push(...picked);

                    const pickedIds = rawPicked.map(pq => pq.id);
                    availablePool = availablePool.filter(aq => !pickedIds.includes(aq.id));

                    groupedBySection.push({
                        section: s,
                        questions: picked
                    });
                });

                return { questions: allPickedQuestions, warnings: warnings, grouped: groupedBySection };
            },

            // Groups any list of questions according to the template's structure
            getGroupedPaper: function (questions, templateId) {
                const template = App.templates.find(t => t.id == templateId);
                if (!template) {
                    console.error("Template not found for grouping:", templateId);
                    return null;
                }

                const sections = getTemplateSections(template);

                const groupedBySection = [];
                const warnings = [];
                let pool = normalizeQuestionList(questions);

                sections.forEach((s, sectionIdx) => {
                    const type = s.marks_type || s.type || 'MCQ';
                    const targetType = this.normalizeType(type);
                    const count = parseInt(s.num_questions || s.count || 0);

                    let sectionQuestions = pool.filter(q => String(q.sectionIdx ?? q.section_idx ?? '') === String(sectionIdx));
                    if (sectionQuestions.length === 0) {
                        sectionQuestions = pool.filter(q => this.normalizeType(q.type || q.marks_type) === targetType);
                    }

                    if (sectionQuestions.length < count) {
                        warnings.push(`Section "${s.section_name || s.name || type}" requires ${count} questions, but only ${sectionQuestions.length} matching types found.`);
                    }

                    const picked = sectionQuestions.slice(0, count);

                    // Remove picked questions from pool to avoid double-counting
                    const pickedIds = picked.map(p => p.id);
                    pool = pool.filter(q => !pickedIds.includes(q.id));

                    groupedBySection.push({
                        section: s,
                        questions: picked
                    });
                });

                return { questions: questions, warnings: warnings, grouped: groupedBySection };
            }
        };
    </script>

    <script>

        // --- Test Execution Engine ---
        // Modal Helpers
        function openModal(id) {
            const modalEl = document.getElementById(id);
            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        }
        function closeModal(id) {
            const modalEl = document.getElementById(id);
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
        }

        let QuestionBanks = App.QuestionBanks || [];
        let activeQB = null;
        let activeQBCategory = 'MCQ';

        // --- Question Bank Management Logic ---
        window.openQuestionBankModal = function () {
            const modal = document.getElementById('QuestionBankModal');
            if (modal) {
                modal.classList.add('open');
                document.body.style.overflow = 'hidden';
                renderQuestionBanks();
                // Always start in "New Bank" mode on initial open.
                promptCreateQB();
            } else {
                console.error("QuestionBankModal element not found!");
            }
        };

        window.closeQuestionBankModal = function () {
            const modal = document.getElementById('QuestionBankModal');
            if (modal) {
                modal.classList.remove('open');
                document.body.style.overflow = '';
            }
        };

        function renderQuestionBanks() {
            const list = document.getElementById('qbList');
            if (!list) return;
            list.innerHTML = QuestionBanks.map(bank => {
                const isActive = activeQB && activeQB.id == bank.id;
                const qCount = bank.questions ? bank.questions.length : 0;
                const sections = bank.questions ? new Set(bank.questions.map(q => q.type || q.marks_type)).size : 0;
                return `
                    <div class="qb-bank-card group ${isActive ? 'active shadow-lg shadow-red-50' : ''}" onclick="selectQuestionBank(${bank.id})">
                        <div class="bank-icon">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <div class="qb-bank-main">
                            <h4 class="qb-bank-title">${bank.name}</h4>
                            <p class="qb-bank-meta">${qCount} Questions • ${sections} Sections</p>
                        </div>

                        <!-- Delete Action for Bank -->
                        <button class="qb-bank-delete"
                                onclick="event.stopPropagation(); deleteQuestionBank(${bank.id})">
                            <i class="bi bi-trash3 text-[10px]"></i>
                        </button>
                    </div>
                `;
            }).join('');
            syncQBDropdowns();
        }

        function syncQBDropdowns() {
            const qbSelect = document.getElementById('quick_qb_select');
            if (qbSelect) {
                const currentValue = qbSelect.value;
                qbSelect.innerHTML = '<option value="" disabled selected>-- Select a Question Bank --</option>' +
                    QuestionBanks.map(b => `<option value="${b.id}">${b.name}</option>`).join('');
                if (currentValue) qbSelect.value = currentValue;
            }
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        const PedagogyRegistry = {
            STORAGE_KEY: 'workflow_pedagogy_custom_options_v1',
            DEFAULT_OPTIONS: [
                'Logical Thinking',
                'Problem Solving',
                'Debugging Skills',
                'Code Efficiency',
                'Analytical Thinking',
                'Programming Fundamentals',
                'Database Knowledge',
                'UI/UX Understanding',
                'Communication Skills',
                'Aptitude',
                'Algorithm Design'
            ],
            normalize(s) {
                return String(s ?? '').trim().replace(/\s+/g, ' ');
            },
            getCustomList() {
                try {
                    const raw = localStorage.getItem(this.STORAGE_KEY);
                    const arr = raw ? JSON.parse(raw) : [];
                    return Array.isArray(arr) ? arr.map(x => this.normalize(x)).filter(Boolean) : [];
                } catch (e) {
                    return [];
                }
            },
            setCustomList(arr) {
                const seen = new Set();
                const out = [];
                arr.forEach(item => {
                    const n = this.normalize(item);
                    if (!n || seen.has(n.toLowerCase())) return;
                    seen.add(n.toLowerCase());
                    out.push(n);
                });
                localStorage.setItem(this.STORAGE_KEY, JSON.stringify(out));
            },
            getAllOptions() {
                const merged = new Map();
                [...this.DEFAULT_OPTIONS, ...this.getCustomList()].forEach(raw => {
                    const n = this.normalize(raw);
                    if (!n) return;
                    merged.set(n.toLowerCase(), n);
                });
                return Array.from(merged.values()).sort((a, b) => a.localeCompare(b));
            },
            matchesExisting(name) {
                const n = this.normalize(name).toLowerCase();
                if (!n) return false;
                return this.getAllOptions().some(o => o.toLowerCase() === n);
            },
            addCustom(raw) {
                const n = this.normalize(raw);
                if (!n) return { ok: false, reason: 'empty' };
                if (n.length > 128) return { ok: false, reason: 'long' };
                if (this.matchesExisting(n)) {
                    const all = this.getAllOptions();
                    const found = all.find(o => o.toLowerCase() === n.toLowerCase());
                    return { ok: false, reason: 'duplicate', value: found || n };
                }
                const next = this.getCustomList();
                next.push(n);
                this.setCustomList(next);
                return { ok: true, value: n };
            }
        };

        function pedagogyResetPanelStyle(panel) {
            if (!panel) return;
            panel.style.position = '';
            panel.style.left = '';
            panel.style.top = '';
            panel.style.right = '';
            panel.style.width = '';
            panel.style.maxHeight = '';
            panel.style.zIndex = '';
            delete panel.dataset.pedagogyFixed;
        }

        function pedagogyHidePanel(panel) {
            if (!panel) return;
            panel.classList.add('hidden');
            pedagogyResetPanelStyle(panel);
        }

        function pedagogyHideAllPedagogyPanels() {
            document.querySelectorAll('.pedagogy-combo-panel').forEach(pedagogyHidePanel);
        }

        function pedagogyPositionPanelFixed(root) {
            const panel = root?.querySelector('.pedagogy-combo-panel');
            const input = root?.querySelector('.pedagogy-combo-search');
            if (!panel || !input || panel.classList.contains('hidden')) return;
            const r = input.getBoundingClientRect();
            if (r.width < 1 || r.height < 1) return;

            const vw = window.innerWidth;
            const vh = window.innerHeight;
            const margin = 8;
            const gap = 4;
            let left = r.left;
            let width = r.width;
            if (left + width > vw - margin) {
                width = Math.max(120, vw - margin - Math.max(margin, left));
            }
            if (left < margin) {
                const shift = margin - left;
                left = margin;
                width = Math.max(120, width - shift);
                if (left + width > vw - margin) {
                    width = Math.max(120, vw - margin - left);
                }
            }

            const desiredMax = 208;
            const spaceBelow = vh - r.bottom - margin - gap;
            const spaceAbove = r.top - margin - gap;
            let top = r.bottom + gap;
            let maxH = Math.min(desiredMax, Math.max(72, spaceBelow));

            if (maxH < 120 && spaceAbove > spaceBelow + 40) {
                maxH = Math.min(desiredMax, Math.max(72, spaceAbove));
                top = Math.max(margin, r.top - maxH - gap);
            }

            panel.style.position = 'fixed';
            panel.style.left = left + 'px';
            panel.style.top = top + 'px';
            panel.style.width = width + 'px';
            panel.style.maxHeight = maxH + 'px';
            panel.style.right = 'auto';
            panel.style.zIndex = '20000';
            panel.dataset.pedagogyFixed = '1';
        }

        function pedagogyRepositionOpenPanels() {
            document.querySelectorAll('.pedagogy-combo-panel:not(.hidden)').forEach(panel => {
                const root = panel.closest('.pedagogy-combo');
                if (root) pedagogyPositionPanelFixed(root);
            });
        }

        function pedagogyComboHtml(baseId, selectedValue, opts = {}) {
            const sel = PedagogyRegistry.normalize(selectedValue);
            const manualAttr = opts.manualQuestionId != null && opts.manualQuestionId !== ''
                ? ` data-pedagogy-manual-id="${escapeHtml(String(opts.manualQuestionId))}"`
                : '';
            const searchClass = opts.searchClass
                ? ` ${opts.searchClass}`
                : ' w-full bg-slate-50 border border-slate-200 rounded-lg px-1.5 py-1 text-[11px] font-bold text-slate-700';
            const fillCell = opts.fillCell ? ' h-full min-h-0 flex flex-col' : '';
            const searchShrink = opts.fillCell ? ' shrink-0' : '';
            return `
                <div class="pedagogy-combo relative w-full${fillCell}" data-pedagogy-base="${escapeHtml(baseId)}"${manualAttr}>
                    <input type="hidden" class="pedagogy-combo-hidden" value="${escapeHtml(sel)}">
                    <input type="text" class="pedagogy-combo-search${searchClass}${searchShrink}" autocomplete="off" spellcheck="false" placeholder="Search or type pedagogy..." value="${escapeHtml(sel)}">
                    <div class="pedagogy-combo-panel mt-0.5 max-h-52 overflow-y-auto rounded-lg border border-slate-200 bg-white py-1 shadow-xl hidden"></div>
                </div>`;
        }

        function pedagogyFindRootByBase(baseId) {
            return Array.from(document.querySelectorAll('[data-pedagogy-base]')).find(el => el.getAttribute('data-pedagogy-base') === baseId) || null;
        }

        function getPedagogyComboValue(baseId) {
            const root = pedagogyFindRootByBase(baseId);
            if (!root) return '';
            const hi = root.querySelector('.pedagogy-combo-hidden');
            const se = root.querySelector('.pedagogy-combo-search');
            const fromHidden = PedagogyRegistry.normalize(hi ? hi.value : '');
            const fromSearch = PedagogyRegistry.normalize(se ? se.value : '');
            return fromHidden || fromSearch;
        }

        function setPedagogyComboValue(baseId, value) {
            const root = pedagogyFindRootByBase(baseId);
            if (!root) return;
            const n = PedagogyRegistry.normalize(value);
            const hi = root.querySelector('.pedagogy-combo-hidden');
            const se = root.querySelector('.pedagogy-combo-search');
            if (hi) hi.value = n;
            if (se) se.value = n;
        }

        function resetPedagogyCombo(baseId) {
            setPedagogyComboValue(baseId, '');
            const root = pedagogyFindRootByBase(baseId);
            pedagogyHidePanel(root?.querySelector('.pedagogy-combo-panel'));
        }

        function pedagogyRenderPanel(root) {
            if (!root) return;
            const panel = root.querySelector('.pedagogy-combo-panel');
            const input = root.querySelector('.pedagogy-combo-search');
            if (!panel || !input) return;

            const qRaw = PedagogyRegistry.normalize(input.value).toLowerCase();
            const all = PedagogyRegistry.getAllOptions();
            const filtered = !qRaw ? all.slice(0, 80) : all.filter(o => o.toLowerCase().includes(qRaw)).slice(0, 80);

            let html = '';
            filtered.forEach(opt => {
                html += `<button type="button" class="pedagogy-opt block w-full px-3 py-2 text-left text-[13px] text-slate-700 hover:bg-slate-50 border-0 bg-transparent" data-value="${escapeHtml(opt)}">${escapeHtml(opt)}</button>`;
            });

            const typed = PedagogyRegistry.normalize(input.value);
            if (typed && !PedagogyRegistry.matchesExisting(typed)) {
                html += `<button type="button" class="pedagogy-add block w-full px-3 py-2 text-left text-[13px] font-semibold text-red-600 hover:bg-red-50 border-t border-slate-100 bg-transparent" data-add="${escapeHtml(typed)}">Add '${escapeHtml(typed)}'</button>`;
            }

            if (!html) {
                html = `<div class="px-3 py-2 text-[12px] text-slate-400">No matches — type to add new</div>`;
            }

            panel.innerHTML = html;
            panel.classList.remove('hidden');
            requestAnimationFrame(() => pedagogyPositionPanelFixed(root));
        }

        function pedagogyPick(root, value) {
            if (!root) return;
            const n = PedagogyRegistry.normalize(value);
            const hi = root.querySelector('.pedagogy-combo-hidden');
            const se = root.querySelector('.pedagogy-combo-search');
            if (hi) hi.value = n;
            if (se) se.value = n;
            pedagogyHidePanel(root.querySelector('.pedagogy-combo-panel'));

            const mid = root.getAttribute('data-pedagogy-manual-id');
            if (mid && typeof App !== 'undefined' && typeof App.updateManualQuestion === 'function') {
                App.updateManualQuestion(mid, 'pedagogy', n);
            }
        }

        function pedagogyCommitBlur(root) {
            if (!root) return;
            const se = root.querySelector('.pedagogy-combo-search');
            const hi = root.querySelector('.pedagogy-combo-hidden');
            if (!se || !hi) return;
            const n = PedagogyRegistry.normalize(se.value);
            hi.value = n;
            const mid = root.getAttribute('data-pedagogy-manual-id');
            if (mid && typeof App !== 'undefined' && typeof App.updateManualQuestion === 'function') {
                App.updateManualQuestion(mid, 'pedagogy', n);
            }
            pedagogyHidePanel(root.querySelector('.pedagogy-combo-panel'));
        }

        (function setupPedagogyComboDelegation() {
            document.addEventListener('focusin', (e) => {
                const s = e.target.closest('.pedagogy-combo-search');
                if (!s) return;
                const root = s.closest('.pedagogy-combo');
                if (root) pedagogyRenderPanel(root);
            });

            document.addEventListener('input', (e) => {
                const s = e.target.closest('.pedagogy-combo-search');
                if (!s) return;
                pedagogyRenderPanel(s.closest('.pedagogy-combo'));
            });

            document.addEventListener('mousedown', (e) => {
                const opt = e.target.closest('.pedagogy-opt');
                if (opt) {
                    e.preventDefault();
                    const root = opt.closest('.pedagogy-combo');
                    pedagogyPick(root, opt.getAttribute('data-value') || '');
                    return;
                }
                const addBtn = e.target.closest('.pedagogy-add');
                if (addBtn) {
                    e.preventDefault();
                    const root = addBtn.closest('.pedagogy-combo');
                    const raw = addBtn.getAttribute('data-add') || '';
                    const res = PedagogyRegistry.addCustom(raw);
                    if (res.ok) {
                        pedagogyPick(root, res.value);
                    } else if (res.reason === 'duplicate' && res.value != null) {
                        pedagogyPick(root, res.value);
                    }
                    if (root) pedagogyRenderPanel(root);
                    return;
                }
            });

            document.addEventListener('blur', (e) => {
                const s = e.target.closest('.pedagogy-combo-search');
                if (!s) return;
                const root = s.closest('.pedagogy-combo');
                setTimeout(() => {
                    if (!root || root.contains(document.activeElement)) return;
                    pedagogyCommitBlur(root);
                }, 120);
            }, true);

            document.addEventListener('click', (e) => {
                if (e.target.closest('.pedagogy-combo')) return;
                pedagogyHideAllPedagogyPanels();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key !== 'Escape') return;
                pedagogyHideAllPedagogyPanels();
            });

            if (!window._pedagogyRepositionBound) {
                window._pedagogyRepositionBound = true;
                window.addEventListener('scroll', pedagogyRepositionOpenPanels, true);
                window.addEventListener('resize', pedagogyRepositionOpenPanels);
            }
        })();

        function normalizeQuestionRecord(question = {}, fallbackSectionIdx = null) {
            return {
                ...question,
                sectionIdx: question.sectionIdx ?? question.section_idx ?? fallbackSectionIdx,
                marks: parseInt(question.marks ?? 0, 10) || 0,
                type: question.type || question.marks_type || '',
                question: question.question || '',
                pedagogy: String(question.pedagogy ?? question.knowledge_type ?? '').trim(),
                option_a: question.option_a || '',
                option_b: question.option_b || '',
                option_c: question.option_c || '',
                option_d: question.option_d || '',
                correct_answer: question.correct_answer || ''
            };
        }

        function normalizeQuestionList(questions = []) {
            return Array.isArray(questions) ? questions.map(q => normalizeQuestionRecord(q)) : [];
        }

        function getTemplateSections(template) {
            let sections = template?.sections || [];
            if (typeof sections === 'string') {
                try {
                    sections = JSON.parse(sections);
                } catch (e) {
                    sections = [];
                }
            }
            return Array.isArray(sections) ? sections : [];
        }

        function hydrateTemplateQuestions(templateId, questions = []) {
            const normalized = normalizeQuestionList(questions);
            if (normalized.length === 0) return [];

            const hasSectionIndexes = normalized.some(q => q.sectionIdx !== null && q.sectionIdx !== undefined && q.sectionIdx !== '');
            if (hasSectionIndexes) {
                return normalized;
            }

            const groupedPaper = App.getGroupedPaper ? App.getGroupedPaper(normalized, templateId) : null;
            if (!groupedPaper || !Array.isArray(groupedPaper.grouped)) {
                return normalized;
            }

            return groupedPaper.grouped.flatMap((group, sectionIdx) =>
                normalizeQuestionList(group.questions || []).map(question => ({
                    ...question,
                    sectionIdx
                }))
            );
        }

        function renderActiveTemplateQuestions(template, sections, questions) {
            const container = document.getElementById('active_template_questions');
            if (!container) return;

            if (!template || !Array.isArray(sections) || sections.length === 0) {
                container.innerHTML = '';
                return;
            }

            const normalizedQuestions = hydrateTemplateQuestions(template.id, questions);
            if (normalizedQuestions.length === 0) {
                container.innerHTML = `
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/40 px-5 py-4 text-center">
                        <p class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-400 mb-1">Question Preview</p>
                        <p class="text-[12px] font-medium text-slate-500 mb-0">No saved questions on this template yet. Choose a Question Bank and save the batch to attach questions.</p>
                    </div>
                `;
                return;
            }

            const grouped = sections.map((section, idx) => ({
                section: section,
                questions: normalizedQuestions.filter(q => String(q.sectionIdx ?? q.section_idx ?? '') === String(idx))
            }));

            const missingIndexedSections = grouped.every(group => group.questions.length === 0);
            const resolvedGroups = missingIndexedSections
                ? ((App.getGroupedPaper(normalizedQuestions, template.id) || {}).grouped || [])
                : grouped;

            container.innerHTML = `
                <div class="pt-2">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-1 h-4 bg-indigo-500 rounded-full"></div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Saved Questions</label>
                    </div>
                    <div class="space-y-3">
                        ${resolvedGroups.map((group, sectionIdx) => {
                const section = group.section || sections[sectionIdx] || {};
                const pickedQuestions = normalizeQuestionList(group.questions || []);
                const sectionName = escapeHtml(section.section_name || section.name || section.marks_type || section.type || `Section ${sectionIdx + 1}`);
                const marksEach = parseInt(section.marks_per_question || section.marks || 0, 10) || 0;

                return `
                                <div class="rounded-2xl border border-slate-100 bg-slate-50/40 overflow-hidden">
                                    <div class="px-4 py-3 bg-white border-b border-slate-100 flex items-center justify-between gap-3">
                                        <div>
                                            <h5 class="text-[12px] font-black text-slate-800 mb-1">${sectionName}</h5>
                                            <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-0">${pickedQuestions.length} Saved Questions • ${marksEach} Marks Each</p>
                                        </div>
                                    </div>
                                    <div class="p-3 space-y-2">
                                        ${pickedQuestions.length > 0 ? pickedQuestions.map((question, questionIdx) => {
                    const isMcq = App.normalizeType(question.type) === 'mcq';
                    const options = ['a', 'b', 'c', 'd']
                        .map(opt => question[`option_${opt}`] ? `<span class="inline-flex items-center gap-1 rounded-lg bg-white border border-slate-100 px-2 py-1 text-[10px] font-medium text-slate-600">${opt.toUpperCase()}. ${escapeHtml(question[`option_${opt}`])}</span>` : '')
                        .join('');

                    return `
                                                <div class="rounded-xl border border-slate-100 bg-white px-3 py-3">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div class="min-w-0">
                                                            <p class="text-[11px] font-semibold text-slate-700 mb-0">${questionIdx + 1}. ${escapeHtml(question.question || 'Untitled question')}</p>
                                                            ${question.pedagogy ? `<p class="text-[9px] font-bold uppercase tracking-widest text-indigo-500 mb-0 mt-1">${escapeHtml(question.pedagogy)}</p>` : ''}
                                                        </div>
                                                        <span class="shrink-0 rounded-lg bg-slate-50 px-2 py-1 text-[9px] font-black uppercase tracking-widest text-slate-500">${escapeHtml(question.marks || marksEach)}M</span>
                                                    </div>
                                                    ${isMcq && options ? `<div class="mt-2 flex flex-wrap gap-2">${options}</div>` : ''}
                                                    ${!isMcq && question.correct_answer ? `<p class="mt-2 text-[10px] font-medium text-slate-500 mb-0"><span class="font-black uppercase tracking-widest text-slate-400">Expected Answer:</span> ${escapeHtml(question.correct_answer)}</p>` : ''}
                                                </div>
                                            `;
                }).join('') : `
                                            <div class="rounded-xl border border-dashed border-slate-200 bg-white/70 px-3 py-4 text-center text-[11px] font-medium text-slate-400">
                                                No questions saved for this section yet.
                                            </div>
                                        `}
                                    </div>
                                </div>
                            `;
            }).join('')}
                    </div>
                </div>
            `;
        }

        async function generateQuestionsForQuickMode(templateId, qbId) {
            if (
                App.quickModePaperSource &&
                String(App.quickModePaperSource.templateId) === String(templateId) &&
                String(App.quickModePaperSource.qbId) === String(qbId) &&
                Array.isArray(App.manualQuestions) &&
                App.manualQuestions.length > 0
            ) {
                return App.getGroupedPaper(App.manualQuestions, templateId);
            }

            const paper = App.generatePaperFromBank(qbId, templateId);
            if (!paper) {
                Swal.fire('Error', 'Failed to generate questions from the selected template and bank.', 'error');
                return null;
            }

            if ((paper.questions || []).length === 0) {
                Swal.fire('No Questions Found', 'The selected Question Bank does not contain questions for this template structure.', 'warning');
                return null;
            }

            if (paper.warnings && paper.warnings.length > 0) {
                const confirm = await Swal.fire({
                    title: 'Insufficient Questions',
                    html: paper.warnings.join('<br>'),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Proceed Anyway',
                    cancelButtonText: 'Wait, let me check'
                });
                if (!confirm.isConfirmed) {
                    return null;
                }
            }

            App.manualQuestions = normalizeQuestionList(paper.questions || []);
            App.quickModePaperSource = { templateId, qbId };
            return paper;
        }

        function handleQuickQuestionBankChange(select) {
            if (!select) return;
            select.classList.remove('border-red-500', 'ring-2', 'ring-red-100');
            App.quickModePaperSource = null;
        }

        async function deleteQuestionBank(id) {
            const confirmed = await Swal.fire({
                title: 'Delete Question Bank?',
                text: "This will permanently delete the bank and ALL its questions. This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2230',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete everything'
            });

            if (!confirmed.isConfirmed) return;

            try {
                const response = await fetch(`/Test/deleteQuestionBank/${id}`, { method: 'POST' });
                const result = await response.json();
                if (result.status === 'success') {
                    QuestionBanks = QuestionBanks.filter(b => b.id != id);

                    if (activeQB && activeQB.id == id) {
                        activeQB = null;
                        resetToQBEmptyState();
                    }

                    renderQuestionBanks();
                    Swal.fire({ icon: 'success', title: 'Bank Deleted', timer: 1500, showConfirmButton: false });
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                Swal.fire('Error', error.message || 'Failed to delete bank', 'error');
            }
        }


        function resetToQBEmptyState() {
            const nameEl = document.getElementById('activeQBName');
            const subtitleEl = document.getElementById('activeQBSubtitle');
            if (nameEl) nameEl.textContent = 'Select a Bank';
            if (subtitleEl) subtitleEl.textContent = '';
            
            const show = ['qbEmptyState'];
            const hide = ['qbContentArea', 'qbHeaderStats', 'qbSecondaryControls', 'qbFooter', 'qbNavigationArrows'];
            
            show.forEach(id => document.getElementById(id)?.classList.remove('hidden'));
            hide.forEach(id => document.getElementById(id)?.classList.add('hidden'));
            updateQBCreateButtonVisibility();
        }

        function updateQBCreateButtonVisibility() {
            const createBtn = document.getElementById('qbCreateBankBtn');
            if (!createBtn) return;
            const isCreateMode = !!(activeQB && activeQB.isDraft);
            createBtn.classList.toggle('hidden', isCreateMode);
        }

        function setQBFooterVisibility(visible) {
            const footer = document.getElementById('qbFooter');
            if (!footer) return;
            footer.classList.toggle('hidden', !visible);
        }

        function collapseFreshQBAfterSave(bank = null) {
            const qCount = Array.isArray(bank?.questions) ? bank.questions.length : 0;
            if (qCount > 0) return;

            document.getElementById('qbSecondaryControls')?.classList.add('hidden');
            document.getElementById('qbContentArea')?.classList.add('hidden');
            document.getElementById('qbNavigationArrows')?.classList.add('hidden');
            setQBFooterVisibility(false);
        }

        function focusQBNameInput() {
            const focusNow = () => {
                const nameInput = document.getElementById('inlineQBName');
                if (!nameInput) return;
                nameInput.focus();
                const len = nameInput.value.length;
                nameInput.setSelectionRange(len, len);
            };

            requestAnimationFrame(focusNow);
            setTimeout(focusNow, 40);
        }

        function promptCreateQB() {
            activeQB = {
                id: null,
                name: '',
                questions: [],
                isDraft: true
            };
            updateQBCreateButtonVisibility();
            renderQuestionBanks();

            const hide = ['qbEmptyState'];
            const show = ['qbContentArea', 'qbHeaderStats', 'qbSecondaryControls', 'qbFooter'];
            
            hide.forEach(id => document.getElementById(id)?.classList.add('hidden'));
            show.forEach(id => document.getElementById(id)?.classList.remove('hidden'));
            document.getElementById('qbNavigationArrows')?.classList.add('hidden');

            const nameEl = document.getElementById('activeQBName');
            const subtitleEl = document.getElementById('activeQBSubtitle');
            if (nameEl) {
                nameEl.innerHTML = `
                    <div class="flex flex-col gap-2">
                        <div>
                            <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-widest mb-0">Question Bank Name</h4>
                            <p class="text-[9px] text-slate-400 font-bold uppercase mb-0">Define question bank name</p>
                        </div>
                        <input type="text" id="inlineQBName" class="w-full bg-slate-50 border border-slate-100 rounded-xl text-[13px] font-bold h-11 px-4 focus:ring-2 focus:ring-red-100 focus:border-red-400 transition-all text-slate-700 shadow-inner placeholder:text-slate-300" placeholder="Enter Question Bank Name..." autofocus oninput="if(activeQB){activeQB.name=this.value.trim();}">
                    </div>
                `;
            }
            if (subtitleEl) subtitleEl.textContent = '';
            focusQBNameInput();
            const tabsContainer = document.getElementById('qbCategoryTabs');
            if (tabsContainer) {
                tabsContainer.innerHTML = `
                    <div class="flex gap-10">
                        <div class="relative py-3">
                            <button class="text-[10px] font-black uppercase tracking-[0.2em] transition-all ${activeQBCategory === 'MCQ' ? 'text-red-600' : 'text-slate-400 hover:text-slate-500'}" onclick="selectQBCategory('MCQ')">MCQ</button>
                            ${activeQBCategory === 'MCQ' ? '<div class="absolute bottom-0 left-0 w-full h-[3px] bg-red-600 rounded-t-full shadow-[0_-2px_15px_rgba(220,34,48,0.25)] animate-fadeIn"></div>' : ''}
                        </div>
                        <div class="relative py-3">
                            <button class="text-[10px] font-black uppercase tracking-[0.2em] transition-all ${activeQBCategory === 'descriptive' ? 'text-red-600' : 'text-slate-400 hover:text-slate-500'}" onclick="selectQBCategory('descriptive')">Descriptive question</button>
                            ${activeQBCategory === 'descriptive' ? '<div class="absolute bottom-0 left-0 w-full h-[3px] bg-red-600 rounded-t-full shadow-[0_-2px_15px_rgba(220,34,48,0.25)] animate-fadeIn"></div>' : ''}
                        </div>
                    </div>
                `;
            }
            updateQBCounters();
            renderQBQuestions();
        }

        async function saveInlineQB() {
            const input = document.getElementById('inlineQBName');
            const name = input.value.trim();

            if (!name) {
                input.focus();
                input.classList.add('animate-shake');
                setTimeout(() => input.classList.remove('animate-shake'), 500);
                return;
            }

            Swal.fire({ title: 'Saving Bank...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

            try {
                const response = await fetch('Test/saveQuestionBank', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: name })
                });

                const result = await response.json();
                if (result.status === 'success') {
                    const newBank = {
                        id: result.id,
                        name: result.name,
                        questions: [],
                        sections: 0
                    };
                    QuestionBanks.push(newBank);
                    renderQuestionBanks();
                    syncQBDropdowns();
                    selectQuestionBank(newBank.id);
                    collapseFreshQBAfterSave(newBank);
                    Swal.fire({ icon: 'success', title: 'Bank Created!', timer: 1500, showConfirmButton: false });
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                Swal.fire('Error', error.message || 'Failed to save bank', 'error');
            }
        }

        function cancelInlineCreate() {
            if (QuestionBanks.length > 0) {
                selectQuestionBank(QuestionBanks[0].id);
            } else {
                resetToQBEmptyState();
                restoreQBHeader();
            }
        }

        function selectQuestionBank(id) {
            activeQB = QuestionBanks.find(b => b.id == id);
            if (!activeQB) return;
            activeQB.isDraft = false;
            updateQBCreateButtonVisibility();

            renderQuestionBanks();

            // Show workspace elements with null checks
            const emptyState = document.getElementById('qbEmptyState');
            const contentArea = document.getElementById('qbContentArea');
            const statsArea = document.getElementById('qbHeaderStats');
            const secondaryControls = document.getElementById('qbSecondaryControls');
            const navigationArrows = document.getElementById('qbNavigationArrows');
            const footerArea = document.getElementById('qbFooter');

            if (emptyState) emptyState.classList.add('hidden');
            if (contentArea) contentArea.classList.remove('hidden');
            if (statsArea) statsArea.classList.remove('hidden');
            if (secondaryControls) secondaryControls.classList.remove('hidden');
            if (navigationArrows) navigationArrows.classList.add('hidden');
            if (footerArea) footerArea.classList.add('hidden');

            restoreQBHeader();
            selectQBCategory(activeQBCategory);
            updateQBCounters();
            renderQBQuestions();
        }

        function updateQBCounters() {
            if (!activeQB) return;
            
            const questionsCountEl = document.getElementById('activeQBQuestionsCount');
            const sectionsCountEl = document.getElementById('activeQBSectionsCount');
            
            if (questionsCountEl) {
                const qCount = activeQB.questions ? activeQB.questions.length : 0;
                questionsCountEl.textContent = qCount;
            }

            if (sectionsCountEl) {
                const sections = activeQB.questions ? new Set(activeQB.questions.map(q => q.type || q.marks_type)).size : 0;
                sectionsCountEl.textContent = sections;
            }
        }

        function qbQuestionMatchesCategory(q, activeCat) {
            if (!q) return false;
            if (activeCat === 'MCQ') return (q.type || '') === 'MCQ';
            if (activeCat === 'descriptive') {
                return typeof App !== 'undefined' && App.normalizeType
                    ? App.normalizeType(q.type) === 'descriptive'
                    : false;
            }
            return false;
        }

        function selectQBCategory(cat) {
            activeQBCategory = cat;
            renderQuestionBanks(); // Sync sidebar tabs
            restoreQBHeader();     // Sync header tabs
            renderQBQuestions();
        }

        function navigateQBCategory(dir) {
            const categories = ['MCQ', 'descriptive'];
            let idx = categories.indexOf(activeQBCategory);
            if (idx === -1) idx = 0;
            idx += dir;
            if (idx < 0) idx = categories.length - 1;
            if (idx >= categories.length) idx = 0;
            selectQBCategory(categories[idx]);
        }

        function restoreQBHeader() {
            if (!activeQB) return;
            const nameEl = document.getElementById('activeQBName');
            const subtitleEl = document.getElementById('activeQBSubtitle');
            if (nameEl) nameEl.textContent = activeQB.name;
            if (subtitleEl) subtitleEl.textContent = 'Repository Context';

            const tabsContainer = document.getElementById('qbCategoryTabs');
            if (tabsContainer) {
                tabsContainer.innerHTML = `
                    <div class="flex gap-10">
                        <div class="relative py-3">
                            <button class="text-[10px] font-black uppercase tracking-[0.2em] transition-all ${activeQBCategory === 'MCQ' ? 'text-red-600' : 'text-slate-400 hover:text-slate-500'}" onclick="selectQBCategory('MCQ')">MCQ</button>
                            ${activeQBCategory === 'MCQ' ? '<div class="absolute bottom-0 left-0 w-full h-[3px] bg-red-600 rounded-t-full shadow-[0_-2px_15px_rgba(220,34,48,0.25)] animate-fadeIn"></div>' : ''}
                        </div>
                        <div class="relative py-3">
                            <button class="text-[10px] font-black uppercase tracking-[0.2em] transition-all ${activeQBCategory === 'descriptive' ? 'text-red-600' : 'text-slate-400 hover:text-slate-500'}" onclick="selectQBCategory('descriptive')">Descriptive question</button>
                            ${activeQBCategory === 'descriptive' ? '<div class="absolute bottom-0 left-0 w-full h-[3px] bg-red-600 rounded-t-full shadow-[0_-2px_15px_rgba(220,34,48,0.25)] animate-fadeIn"></div>' : ''}
                        </div>
                    </div>
                `;
            }
            updateQBCounters();
        }

        function renderQBQuestions() {
            const content = document.getElementById('qbContentArea');
            if (!activeQB || !content) return;

            const filteredQuestions = activeQB.questions ? activeQB.questions.filter(q => qbQuestionMatchesCategory(q, activeQBCategory)) : [];

            // Show secondary controls area with null check
            const secondaryControls = document.getElementById('qbSecondaryControls');
            if (secondaryControls) secondaryControls.classList.remove('hidden');

            const headerActions = document.getElementById('qbHeaderActions');
            if (headerActions) {
                headerActions.innerHTML = `
                    <button class="px-7 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm" onclick="downloadQBTemplate()">
                        <i class="bi bi-file-earmark-arrow-down text-sm"></i> Template
                    </button>
                    <input type="file" id="qbDirectFileInput" class="hidden" onchange="handleQBFileUpload(this)">
                    <button class="px-7 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm" onclick="document.getElementById('qbDirectFileInput')?.click()">
                        <i class="bi bi-cloud-arrow-up text-sm"></i> Bulk
                    </button>
                    <button class="px-7 py-2.5 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-all flex items-center gap-2 shadow-lg shadow-red-100" onclick="addQuestionInline()">
                        <i class="bi bi-plus-lg text-sm"></i> Add Question
                    </button>
                `;
            }

            let html = `
                <div class="bg-white rounded-3xl border border-slate-200 overflow-visible mb-6 shadow-sm">
                    <div class="relative bg-slate-50/80 border-b border-slate-100 py-3 px-10">
                        <button class="absolute left-3 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center shadow-sm z-10"
                            onclick="navigateQBCategory(-1)" title="Previous Section" data-bs-toggle="tooltip" data-bs-title="Previous Section">
                            <i class="bi bi-chevron-left text-[12px]"></i>
                        </button>
                        <button class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center shadow-sm z-10"
                            onclick="navigateQBCategory(1)" title="Next Section" data-bs-toggle="tooltip" data-bs-title="Next Section">
                            <i class="bi bi-chevron-right text-[12px]"></i>
                        </button>
                        <div class="grid ${activeQBCategory === 'MCQ' ? 'grid-cols-[48px_minmax(96px,0.9fr)_minmax(0,1.1fr)_minmax(0,1fr)]' : 'grid-cols-[48px_minmax(96px,0.9fr)_1fr]'} gap-2">
                        <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.15em] text-center">#</div>
                        <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.15em]">Pedagogy</div>
                        <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.15em]">Question Content</div>
                        ${activeQBCategory === 'MCQ' ? '<div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.15em] px-2">Option Details</div>' : ''}
                        </div>
                    </div>
                    <div id="questionsList" class="divide-y divide-slate-100">
                        ${filteredQuestions.map((q, idx) => renderQuestionCard(q, idx)).join('')}
                    </div>
                </div>
            `;

            content.innerHTML = html;
        }

        function saveAllQBDetails() {
            if (!activeQB) return;
            const draftNameInput = document.getElementById('inlineQBName');
            const bankName = (draftNameInput?.value || activeQB.name || '').trim();

            if (!bankName) {
                Swal.fire('Bank Name Required', 'Please enter a bank name before saving.', 'warning');
                draftNameInput?.focus();
                return;
            }

            activeQB.name = bankName;

            if (activeQB.id) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: 'Repository changes are already synced.',
                    timer: 1400,
                    showConfirmButton: false
                });
                renderQuestionBanks();
                renderQBQuestions();
                syncQBDropdowns();
                setQBFooterVisibility(false);
                return;
            }

            Swal.fire({ title: 'Saving Repository...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            fetch('/Test/saveQuestionBank', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: bankName })
            })
            .then(response => response.json())
            .then(async result => {
                if (result.status !== 'success') throw new Error(result.message || 'Failed to save bank');
                activeQB.id = result.id;
                activeQB.isDraft = false;

                if (Array.isArray(activeQB.questions) && activeQB.questions.length > 0) {
                    const questionsPayload = activeQB.questions.map(q => ({
                        question: q.question || '',
                        type: q.type || 'MCQ',
                        option_a: q.option_a || '',
                        option_b: q.option_b || '',
                        option_c: q.option_c || '',
                        option_d: q.option_d || '',
                        correct_answer: q.correct_answer || '',
                        marks: q.marks || (q.type === 'Short Answer' ? 2 : 1),
                        section_name: q.category || bankName,
                        pedagogy: q.pedagogy || ''
                    }));

                    const qResponse = await fetch('/Test/bulkSaveQBQuestions', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            repository_id: activeQB.id,
                            questions: questionsPayload
                        })
                    });
                    const qResult = await qResponse.json();
                    if (qResult.status !== 'success') throw new Error(qResult.message || 'Failed to save questions');
                }

                const existing = QuestionBanks.findIndex(b => String(b.id) === String(activeQB.id));
                const savedBank = {
                    id: activeQB.id,
                    name: activeQB.name,
                    questions: Array.isArray(activeQB.questions) ? activeQB.questions : []
                };
                if (existing === -1) {
                    QuestionBanks.push(savedBank);
                } else {
                    QuestionBanks[existing] = savedBank;
                }

                renderQuestionBanks();
                syncQBDropdowns();
                selectQuestionBank(activeQB.id);
                collapseFreshQBAfterSave(activeQB);
                Swal.fire({ icon: 'success', title: 'Repository Saved!', timer: 1500, showConfirmButton: false });
            })
            .catch(error => {
                Swal.fire('Error', error.message || 'Failed to save repository', 'error');
            });
        }

        function renderQuestionCard(q, idx) {
            if (!q) return '';
            const gridClass = activeQBCategory === 'MCQ' ? "grid grid-cols-[48px_minmax(96px,0.9fr)_minmax(0,1.1fr)_minmax(0,1fr)] gap-2 items-center" : "grid grid-cols-[48px_minmax(96px,0.9fr)_1fr] gap-2 items-center";
            const ped = (q.pedagogy || q.knowledge_type || '').trim();
            if (activeQBCategory === 'MCQ') {
                return `
                    <div class="py-1.5 px-10 hover:bg-slate-50/30 transition-all group relative" id="question-card-${q.id || idx}">
                        <div class="${gridClass}">
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-6 h-6 rounded-full bg-slate-800 text-white flex items-center justify-center text-[10px] font-black shadow-sm border border-slate-700">${idx + 1}</div>
                                <span class="px-1.5 py-0.5 rounded-md bg-red-50 text-red-600 text-[7px] font-black uppercase tracking-widest border border-red-100">MCQ</span>
                            </div>
                            <div class="min-w-0 px-1">
                                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Pedagogy</p>
                                <p class="text-[10px] font-bold text-slate-600 truncate mb-0">${ped ? escapeHtml(ped) : '—'}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[13px] font-bold text-slate-800 leading-tight truncate">${q.question || ''}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-1 px-1">
                                ${['A', 'B', 'C', 'D'].map(opt => `
                                    <div class="flex items-center gap-2 p-0.5 px-1.5 rounded border ${q.correct_answer === opt ? 'border-green-200 bg-green-50/50' : 'border-slate-50 bg-slate-50/30'}">
                                        <div class="w-4 h-4 rounded-sm ${q.correct_answer === opt ? 'bg-green-600 text-white' : 'bg-slate-200 text-slate-500'} flex items-center justify-center text-[9px] font-black shrink-0">${opt}</div>
                                        <span class="text-[9px] font-bold ${q.correct_answer === opt ? 'text-green-800' : 'text-slate-500'} truncate">${q[`option_${opt.toLowerCase()}`] || ''}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        <!-- Floating Actions on hover -->
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5 bg-white/90 border border-slate-100 rounded-lg px-1.5 py-1 shadow-sm z-10">
                            <button class="w-7 h-7 rounded bg-white border border-slate-200 text-slate-500 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 shadow-sm transition-all" onclick="editQuestionInline('${q.id || idx}')">
                                <i class="bi bi-pencil-square text-[11px]"></i>
                            </button>
                            <button class="w-7 h-7 rounded bg-white border border-slate-200 text-slate-500 flex items-center justify-center hover:bg-red-50 hover:text-red-600 shadow-sm transition-all" onclick="deleteQuestion('${q.id || idx}')">
                                <i class="bi bi-trash3 text-[11px]"></i>
                            </button>
                        </div>
                    </div>
                `;
            } else {
                return `
                    <div class="py-1.5 px-10 hover:bg-slate-50/30 transition-all group relative" id="question-card-${q.id || idx}">
                        <div class="${gridClass}">
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-6 h-6 rounded-full bg-slate-800 text-white flex items-center justify-center text-[10px] font-black shadow-sm border border-slate-700">${idx + 1}</div>
                                <span class="px-1.5 py-0.5 rounded-md bg-blue-50 text-blue-600 text-[7px] font-black uppercase tracking-widest border border-blue-100">Descriptive</span>
                            </div>
                            <div class="min-w-0 px-1">
                                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Pedagogy</p>
                                <p class="text-[10px] font-bold text-slate-600 truncate mb-0">${ped ? escapeHtml(ped) : '—'}</p>
                            </div>
                            <div class="min-w-0 pr-20">
                                <p class="text-[13px] font-bold text-slate-800 leading-tight truncate">${q.question || ''}</p>
                            </div>
                        </div>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5 bg-white/90 border border-slate-100 rounded-lg px-1.5 py-1 shadow-sm z-10">
                            <button class="w-7 h-7 rounded bg-white border border-slate-200 text-slate-500 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 shadow-sm transition-all" onclick="editQuestionInline('${q.id || idx}')">
                                <i class="bi bi-pencil-square text-[11px]"></i>
                            </button>
                            <button class="w-7 h-7 rounded bg-white border border-slate-200 text-slate-500 flex items-center justify-center hover:bg-red-50 hover:text-red-600 shadow-sm transition-all" onclick="deleteQuestion('${q.id || idx}')">
                                <i class="bi bi-trash3 text-[11px]"></i>
                            </button>
                        </div>
                    </div>
                `;
            }
        }

        function addQuestionInline() {
            const list = document.getElementById('questionsList');
            if (!list) return;

            const existingPlaceholder = document.getElementById('inlineFormPlaceholder');
            if (existingPlaceholder) {
                const focusTarget = document.getElementById('inlineQContent');
                if (focusTarget) focusTarget.focus();
                return;
            }

            const placeholder = document.createElement('div');
            placeholder.id = 'inlineFormPlaceholder';
            list.prepend(placeholder);

            let formHtml = '';
            const nextIdx = activeQB.questions.filter(q => qbQuestionMatchesCategory(q, activeQBCategory)).length + 1;
            const gridClass = activeQBCategory === 'MCQ' ? "grid grid-cols-[48px_minmax(96px,0.9fr)_minmax(0,1.1fr)_minmax(0,1fr)] gap-2 items-stretch" : "grid grid-cols-[48px_minmax(96px,0.9fr)_1fr] gap-2 items-center";

            if (activeQBCategory === 'MCQ') {
                formHtml = `
                    <div class="py-1 px-10 bg-white border-b border-red-100 transition-all shadow-inner">
                        <div class="${gridClass}">
                            <div class="flex flex-col items-center gap-1 justify-start pt-0.5 min-h-0">
                                <div class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center font-black text-[10px] shadow-sm">${nextIdx}</div>
                                <div class="px-1 py-0.5 rounded-md bg-blue-50 text-blue-600 text-[7px] font-black uppercase tracking-widest border border-blue-100">MCQ</div>
                            </div>
                            <div class="p-1 min-w-0 min-h-0 flex flex-col justify-start h-full">
                                <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5 shrink-0">Pedagogy</label>
                                ${pedagogyComboHtml('inlinePedagogy', '', { fillCell: true, searchClass: 'w-full bg-slate-50 border border-slate-200 rounded-lg px-1.5 py-1 text-[11px] font-bold text-slate-700' })}
                            </div>
                            <div class="p-1 min-w-0 min-h-0 flex flex-col h-full">
                                <textarea id="inlineQContent" class="w-full min-h-0 flex-1 bg-slate-50 border border-slate-200 rounded-lg p-1.5 text-[13px] font-bold text-slate-700 focus:bg-white focus:border-red-400 outline-none transition-all resize-none" rows="1" placeholder="Question..." autofocus></textarea>
                            </div>
                            <div class="min-h-0 h-full grid grid-cols-2 gap-1.5 px-1 content-start self-stretch">
                                ${['A', 'B', 'C', 'D'].map(opt => `
                                    <div class="relative group">
                                        <div class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] font-black text-blue-400 uppercase tracking-widest">${opt}</div>
                                        <input type="text" id="inlineOpt${opt}" class="w-full pl-6 pr-6 py-1.5 bg-white border border-slate-100 rounded text-[11px] font-bold text-slate-600 focus:border-red-400 outline-none transition-all" placeholder="...">
                                        <button class="absolute right-1 top-1/2 -translate-y-1/2 w-3.5 h-3.5 rounded-full border border-slate-200 flex items-center justify-center hover:border-red-400 transition-all correct-opt-selector" onclick="setInlineCorrect('${opt}')" id="selector${opt}">
                                            <div class="w-1 h-1 rounded-full bg-red-500 opacity-0 transition-all selector-dot"></div>
                                        </button>
                                    </div>
                                `).join('')}
                                <input type="hidden" id="inlineCorrect" value="">
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-2 pb-1 pr-1">
                            <button class="px-4 py-1.5 rounded-lg bg-slate-100 text-slate-600 font-black text-[9px] uppercase tracking-widest hover:bg-slate-200 transition-all border border-slate-200" onclick="cancelInlineForm()">Cancel</button>
                            <button class="px-4 py-1.5 rounded-lg bg-red-600 text-white font-black text-[9px] uppercase tracking-widest hover:bg-red-700 transition-all shadow-sm shadow-red-100" onclick="saveQuestionInline()">Save</button>
                        </div>
                    </div>
                `;
            } else {
                formHtml = `
                    <div class="py-1 px-10 bg-white border-b border-blue-100 transition-all shadow-inner">
                        <div class="${gridClass}">
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center font-black text-[10px] shadow-sm">${nextIdx}</div>
                                <div class="px-1 py-0.5 rounded-md bg-blue-50 text-blue-600 text-[7px] font-black uppercase tracking-widest border border-blue-100">2M</div>
                            </div>
                            <div class="p-1 min-w-0 flex flex-col justify-start">
                                <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Pedagogy</label>
                                ${pedagogyComboHtml('inlinePedagogy', '', { searchClass: 'w-full bg-slate-50 border border-slate-200 rounded-lg px-1.5 py-1 text-[11px] font-bold text-slate-700' })}
                            </div>
                            <div class="p-1 min-w-0">
                                <textarea id="inlineQContent" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-1.5 text-[13px] font-bold text-slate-700 focus:bg-white focus:border-blue-400 outline-none transition-all resize-none" rows="1" placeholder="Question..." autofocus></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-2 pb-1 pr-1">
                            <button class="px-4 py-1.5 rounded-lg bg-slate-100 text-slate-600 font-black text-[9px] uppercase tracking-widest hover:bg-slate-200 transition-all border border-slate-200" onclick="cancelInlineForm()">Cancel</button>
                            <button class="px-4 py-1.5 rounded-lg bg-red-600 text-white font-black text-[9px] uppercase tracking-widest hover:bg-red-700 transition-all shadow-sm shadow-red-100" onclick="saveQuestionInline()">Save</button>
                        </div>
                    </div>
                `;
            }

            placeholder.innerHTML = formHtml;
            placeholder.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function setInlineCorrect(opt) {
            document.querySelectorAll('.correct-opt-selector').forEach(btn => {
                if (btn.id === 'selector' + opt) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            document.getElementById('inlineCorrect').value = opt;
        }

        function cancelInlineForm() {
            const el = document.getElementById('inlineFormPlaceholder');
            if (el) el.remove();
        }

        async function saveQuestionInline() {
            const content = document.getElementById('inlineQContent').value.trim();
            if (!content) return;

            const qData = {
                repository_id: activeQB.id,
                question: content,
                type: activeQBCategory === 'descriptive' ? 'Short Answer' : 'MCQ',
                marks: activeQBCategory === 'descriptive' ? 2 : 1,
                category: activeQB.name,
                pedagogy: getPedagogyComboValue('inlinePedagogy')
            };

            if (activeQBCategory === 'MCQ') {
                qData.option_a = document.getElementById('inlineOptA').value;
                qData.option_b = document.getElementById('inlineOptB').value;
                qData.option_c = document.getElementById('inlineOptC').value;
                qData.option_d = document.getElementById('inlineOptD').value;
                qData.correct_answer = document.getElementById('inlineCorrect').value;
            } else {
                const expEl = document.getElementById('inlineExpected');
                qData.correct_answer = expEl ? expEl.value : '';
            }

            if (!activeQB.id) {
                qData.id = `draft_${Date.now()}_${Math.random().toString(36).slice(2, 7)}`;
                if (!activeQB.questions) activeQB.questions = [];
                activeQB.questions.push(qData);
                updateQBCounters();
                renderQBQuestions();
                renderQuestionBanks();
                syncQBDropdowns();
                Swal.fire({ icon: 'success', title: 'Question Added', text: 'Saved in draft. Click Save to persist.', timer: 1300, showConfirmButton: false });
                return;
            }

            try {
                const response = await fetch('Test/saveQBQuestion', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(qData)
                });
                const result = await response.json();
                if (result.status === 'success') {
                    qData.id = result.id;
                    if (!activeQB.questions) activeQB.questions = [];
                    activeQB.questions.push(qData);
                    updateQBCounters();
                    renderQBQuestions();
                    renderQuestionBanks();
                    syncQBDropdowns();
                    Swal.fire({ icon: 'success', title: 'Question Saved', timer: 1000, showConfirmButton: false });
                }
            } catch (e) { console.error(e); }
        }

        /** Question bank rows use numeric DB ids; draft banks use string ids like draft_* or draft_import_*. */
        function isQBQuestionPersisted(id) {
            return /^\d+$/.test(String(id ?? '').trim());
        }

        async function deleteQuestion(id) {
            const confirmed = await Swal.fire({
                title: 'Delete Question?',
                text: "This will permanently remove the question from the bank.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2230',
                confirmButtonText: 'Yes, delete it'
            });

            if (!confirmed.isConfirmed) return;

            if (!isQBQuestionPersisted(id)) {
                activeQB.questions = activeQB.questions.filter(q => (q.id || '').toString() !== id.toString());
                updateQBCounters();
                renderQBQuestions();
                renderQuestionBanks();
                Swal.fire({ icon: 'success', title: 'Removed', timer: 1000, showConfirmButton: false });
                return;
            }

            try {
                const response = await fetch(`/Test/deleteQBQuestion/${id}`, { method: 'POST' });
                const result = await response.json();
                if (result.status === 'success') {
                    activeQB.questions = activeQB.questions.filter(q => (q.id || '').toString() !== id.toString());
                    updateQBCounters();
                    renderQBQuestions();
                    renderQuestionBanks();
                    Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1000, showConfirmButton: false });
                }
            } catch (e) { console.error(e); }
        }

        function editQuestionInline(id) {
            const idx = activeQB.questions.findIndex(q => (q.id || '').toString() === id.toString());
            if (idx === -1) return;

            const q = activeQB.questions[idx];
            const card = document.getElementById(`question-card-${id}`);
            const questionIdx = activeQB.questions.filter(qu => qu.type === q.type).indexOf(q) + 1;
            const gridClass = activeQBCategory === 'MCQ' ? "grid grid-cols-[48px_minmax(96px,0.9fr)_minmax(0,1.1fr)_minmax(0,1fr)] gap-2 items-stretch" : "grid grid-cols-[48px_minmax(96px,0.9fr)_1fr] gap-2 items-center";
            const pedVal = (q.pedagogy || q.knowledge_type || '').trim();

            let editHtml = '';
            if (q.type === 'MCQ') {
                editHtml = `
                    <div class="py-1 px-10 bg-blue-50/30 border-y border-blue-100 transition-all shadow-inner">
                        <div class="${gridClass}">
                            <div class="flex flex-col items-center gap-1 justify-start pt-0.5 min-h-0">
                                <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-black shadow-sm border border-blue-500">${questionIdx}</div>
                                <span class="px-1 py-0 rounded-md bg-blue-100 text-blue-600 text-[7px] font-black uppercase tracking-widest">EDIT</span>
                            </div>
                            <div class="p-1 min-w-0 min-h-0 flex flex-col justify-start h-full">
                                <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5 shrink-0">Pedagogy</label>
                                ${pedagogyComboHtml('editPedagogy', pedVal, { fillCell: true, searchClass: 'w-full bg-white border border-blue-200 rounded-lg px-1.5 py-1 text-[11px] font-bold text-slate-700' })}
                            </div>
                            <div class="p-1 min-w-0 min-h-0 flex flex-col h-full">
                                <textarea id="editQContent" class="w-full min-h-0 flex-1 bg-white border border-blue-200 rounded p-1.5 text-[13px] font-bold text-slate-700 outline-none transition-all resize-none" rows="1">${q.question}</textarea>
                            </div>
                            <div class="min-h-0 h-full grid grid-cols-2 gap-1 px-1 content-start self-stretch">
                                ${['A', 'B', 'C', 'D'].map(opt => `
                                    <div class="relative group">
                                        <div class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] font-black text-blue-400 uppercase tracking-widest">${opt}</div>
                                        <input type="text" id="editOpt${opt}" class="w-full pl-6 pr-6 py-1.5 bg-white border border-blue-200 rounded text-[11px] font-bold text-slate-600 outline-none transition-all" value="${q[`option_${opt.toLowerCase()}`]}">
                                        <button class="absolute right-1 top-1/2 -translate-y-1/2 w-3.5 h-3.5 rounded-full border ${q.correct_answer === opt ? 'border-green-500 bg-green-50 active' : 'border-slate-200'} flex items-center justify-center correct-opt-selector edit-correct-btn" onclick="setEditCorrect('${opt}')" id="editSelector${opt}">
                                            <div class="w-1 h-1 rounded-full bg-green-500 ${q.correct_answer === opt ? 'opacity-100' : 'opacity-0'} transition-all selector-dot"></div>
                                        </button>
                                    </div>
                                `).join('')}
                                <input type="hidden" id="editCorrect" value="${q.correct_answer}">
                            </div>
                        </div>
                        <div class="flex justify-end gap-1 mt-1 pb-1 pr-1">
                            <button class="px-2 py-1 rounded bg-white border border-slate-200 text-slate-400 font-black text-[7px] uppercase tracking-widest hover:bg-slate-50 transition-all" onclick="renderQBQuestions()">Cancel</button>
                            <button class="px-4 py-1 rounded bg-blue-600 text-white font-black text-[7px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-sm shadow-blue-100" onclick="updateQuestionInline('${id}')">Update</button>
                        </div>
                    </div>
                `;
            } else {
                editHtml = `
                    <div class="py-2 px-10 bg-blue-50/30 border-y border-blue-100 transition-all shadow-inner">
                        <div class="${gridClass}">
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-[11px] font-black shadow-sm border border-blue-500">${questionIdx}</div>
                                <span class="px-1.5 py-0.5 rounded-md bg-blue-100 text-blue-600 text-[8px] font-black uppercase tracking-widest">EDIT</span>
                            </div>
                            <div class="p-1 min-w-0 flex flex-col justify-start">
                                <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Pedagogy</label>
                                ${pedagogyComboHtml('editPedagogy', pedVal, { searchClass: 'w-full bg-white border border-blue-200 rounded-lg px-1.5 py-1 text-[11px] font-bold text-slate-700' })}
                            </div>
                            <div class="p-1 min-w-0">
                                <textarea id="editQContent" class="w-full bg-white border border-blue-200 rounded p-2 text-sm font-bold text-slate-700 outline-none transition-all resize-none" rows="1">${q.question}</textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-1 mt-1 pb-1 pr-1">
                            <button class="px-2 py-1 rounded bg-white border border-slate-200 text-slate-400 font-black text-[7px] uppercase tracking-widest hover:bg-slate-50 transition-all" onclick="renderQBQuestions()">Cancel</button>
                            <button class="px-4 py-1 rounded bg-blue-600 text-white font-black text-[7px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-sm shadow-blue-100" onclick="updateQuestionInline('${id}')">Update</button>
                        </div>
                    </div>
                `;
            }

            card.innerHTML = editHtml;
            setQBFooterVisibility(true);
        }

        function setEditCorrect(opt) {
            document.querySelectorAll('.edit-correct-btn').forEach(btn => {
                const dot = btn.querySelector('.selector-dot');
                if (btn.id === 'editSelector' + opt) {
                    btn.className = 'absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full border-2 border-green-500 bg-green-50 active flex items-center justify-center correct-opt-selector edit-correct-btn';
                    if (dot) dot.style.opacity = '1';
                } else {
                    btn.className = 'absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full border-2 border-slate-200 flex items-center justify-center correct-opt-selector edit-correct-btn';
                    if (dot) dot.style.opacity = '0';
                }
            });
            document.getElementById('editCorrect').value = opt;
        }

        async function updateQuestionInline(id) {
            const idx = activeQB.questions.findIndex(q => (q.id || '').toString() === id.toString());
            if (idx === -1) return;

            const q = activeQB.questions[idx];
            const updatedData = {
                question: document.getElementById('editQContent').value.trim(),
                pedagogy: getPedagogyComboValue('editPedagogy')
            };

            if (q.type === 'MCQ') {
                updatedData.option_a = document.getElementById('editOptA').value;
                updatedData.option_b = document.getElementById('editOptB').value;
                updatedData.option_c = document.getElementById('editOptC').value;
                updatedData.option_d = document.getElementById('editOptD').value;
                updatedData.correct_answer = document.getElementById('editCorrect').value;
            } else {
                const expEl = document.getElementById('editExpected');
                updatedData.correct_answer = expEl ? expEl.value : (q.expected_answer || '');
            }

            if (!isQBQuestionPersisted(id)) {
                Object.assign(q, updatedData);
                renderQBQuestions();
                Swal.fire({ icon: 'success', title: 'Updated', text: 'Saved in draft. Use Save on the bank to persist.', timer: 1400, showConfirmButton: false });
                return;
            }

            try {
                const response = await fetch(`/Test/updateQBQuestion/${id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(updatedData)
                });

                const result = await response.json();
                if (result.status === 'success') {
                    Object.assign(q, updatedData);
                    renderQBQuestions();
                    Swal.fire({ icon: 'success', title: 'Updated!', timer: 1000, showConfirmButton: false });
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                Swal.fire('Error', error.message || 'Failed to update question', 'error');
            }
        }

        function cancelInlineCreate() {
            if (QuestionBanks.length > 0) {
                selectQuestionBank(QuestionBanks[0].id);
            } else {
                resetToQBEmptyState();
                restoreQBHeader();
            }
        }



        function downloadQBTemplate() {
            const headers = "section_name,question,type,option_a,option_b,option_c,option_d,correct_answer,marks,expected_answer,pedagogy\n";
            const sample1 = "MCQ,What is 2+2?,MCQ,3,4,5,6,B,1,,Factual\n";
            const sample2 = "Aptitude,Explain gravity.,Descriptive question,,,,,,2,Force that pulls objects towards each other.,Conceptual\n";

            const blob = new Blob([headers + sample1 + sample2], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'QuestionBank_Template.csv';
            a.click();
        }

        async function handleQBFileUpload(input) {
            const file = input.files[0];
            if (!file || !activeQB) return;

            const reader = new FileReader();
            reader.onload = async function (e) {
                const text = e.target.result;
                const rows = text.split('\n');
                let startIndex = 0;
                // Skip headers and comments
                while (startIndex < rows.length && (rows[startIndex].trim() === '' || rows[startIndex].startsWith('#') || rows[startIndex].toLowerCase().includes('section_name'))) {
                    startIndex++;
                }

                const isDescriptiveCsvType = (cell) => {
                    const t = String(cell || '').trim().toLowerCase();
                    return t === 'short answer' || t === 'descriptive question' || t.includes('descriptive');
                };
                const questions = rows.slice(startIndex).filter(r => r.trim()).map(r => {
                    const cols = r.split(',').map(c => c.replace(/^"|"$/g, '').trim());
                    const desc = isDescriptiveCsvType(cols[2]);
                    return {
                        section_name: cols[0] || 'General',
                        question: cols[1],
                        type: desc ? 'Short Answer' : 'MCQ',
                        option_a: cols[3] || '',
                        option_b: cols[4] || '',
                        option_c: cols[5] || '',
                        option_d: cols[6] || '',
                        correct_answer: cols[7] || '',
                        marks: parseInt(cols[8], 10) || (desc ? 2 : 1),
                        expected_answer: cols[9] || '',
                        pedagogy: (cols[10] || '').trim()
                    };
                });

                if (questions.length === 0) {
                    Swal.fire('Empty File', 'No questions found in the CSV.', 'warning');
                    return;
                }

                Swal.fire({ title: 'Importing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                if (!activeQB.id) {
                    const localQuestions = questions.map((q, idx) => ({
                        id: `draft_import_${Date.now()}_${idx}`,
                        repository_id: null,
                        question: q.question || '',
                        type: q.type === 'Short Answer' ? 'Short Answer' : 'MCQ',
                        option_a: q.option_a || '',
                        option_b: q.option_b || '',
                        option_c: q.option_c || '',
                        option_d: q.option_d || '',
                        correct_answer: q.correct_answer || q.expected_answer || '',
                        marks: q.marks || (q.type === 'Short Answer' ? 2 : 1),
                        category: activeQB.name || 'Draft',
                        pedagogy: q.pedagogy || ''
                    }));
                    activeQB.questions = [...(activeQB.questions || []), ...localQuestions];
                    updateQBCounters();
                    renderQBQuestions();
                    renderQuestionBanks();
                    Swal.fire({
                        title: 'Imported to Draft',
                        text: `${localQuestions.length} questions added. Click Save to persist.`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    return;
                }

                try {
                    const response = await fetch('Test/bulkSaveQBQuestions', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            repository_id: activeQB.id,
                            questions: questions
                        })
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        // Refresh activeQB questions by refetching or just adding locally (if backend doesn't return them)
                        // For simplicity, we'll just reload the page or refetch the list
                        Swal.fire({
                            title: 'Success!',
                            text: `${questions.length} questions imported and saved permanently.`,
                            icon: 'success',
                            confirmButtonColor: '#dc2230'
                        }).then(() => {
                            location.reload(); // Hard refresh to ensure everything is in sync with DB
                        });
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    Swal.fire('Error', 'Failed to save questions: ' + error.message, 'error');
                }
            };
            reader.readAsText(file);
        }

        function filterBanks(query) {
            const filtered = QuestionBanks.filter(b => b.name.toLowerCase().includes(query.toLowerCase()));
            const list = document.getElementById('qbList');
            if (!list) return;
            list.innerHTML = filtered.map(bank => {
                const qCount = Array.isArray(bank.questions) ? bank.questions.length : 0;
                const sections = Array.isArray(bank.questions) ? new Set(bank.questions.map(q => q.type || q.marks_type)).size : 0;
                return `
                <div class="qb-bank-card ${activeQB && activeQB.id === bank.id ? 'active shadow-lg shadow-red-50' : ''}" onclick="selectQuestionBank(${bank.id})">
                    <div class="bank-icon">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <div class="qb-bank-main">
                        <h4 class="qb-bank-title">${bank.name}</h4>
                        <p class="qb-bank-meta">${qCount} Questions • ${sections} Sections</p>
                    </div>
                    <!-- Delete Action for Bank -->
                    <button class="qb-bank-delete"
                            onclick="event.stopPropagation(); deleteQuestionBank(${bank.id})">
                        <i class="bi bi-trash3 text-[10px]"></i>
                    </button>
                </div>
            `;
            }).join('');
        }

        // --- Tab Management ---
        function switchMainTab(tabId) {
            // Save current tab to localStorage
            localStorage.setItem('activeTestTab', tabId);

            // Hide all main containers in the content area
            document.querySelectorAll('#main-content-area > main').forEach(tab => {
                tab.classList.add('hidden');
            });

            // Update module tabs if they exist
            document.querySelectorAll('.module-tab').forEach(tab => {
                tab.classList.remove('active');
                const onclick = tab.getAttribute('onclick');
                if (onclick && onclick.includes("'" + tabId + "'")) {
                    tab.classList.add('active');
                }
            });

            // Show target tab
            const targetTab = document.getElementById('tab-content-' + tabId);
            if (targetTab) {
                targetTab.classList.remove('hidden');

                // Specific initialization for each tab
                if (tabId === 'management') {
                    if (typeof initTestsDataTable === 'function') initTestsDataTable();
                }

                if (tabId === 'results') {
                    if (typeof switchResultView === 'function') {
                        switchResultView('student');
                    } else if (App.loadCandidateResult) {
                        App.loadCandidateResult();
                    }
                }

                if (tabId === 'execution') {
                    if (App.initExecutionDashboard) {
                        App.initExecutionDashboard();
                    } else {
                        console.error("App.initExecutionDashboard not defined");
                    }
                }
            }
        }

        // --- Execution View Dashboard Logic ---
        App.initExecutionDashboard = () => {
            const body = document.getElementById('execution_dashboard_body');
            if (!body) return;

            async function refresh() {
                try {
                    const response = await fetch('Test/getTests');
                    const result = await response.json();
                    if (result.status === 'success') {
                        App.Tests = result.tests;
                    }
                } catch (e) {
                    console.error("Failed to fetch latest tests:", e);
                }

                let html = '';
                const now = new Date();

                if (!App.Tests || !Array.isArray(App.Tests)) {
                    body.innerHTML = '<tr><td colspan="6" class="py-20 text-center text-slate-400">Loading assessments...</td></tr>';
                    return;
                }

                App.Tests.forEach(test => {
                    const packs = test.test_packs || [];
                    packs.forEach(pack => {
                        // Only show published tests on student dashboard
                        if (pack.status !== 'published') return;

                        // Correctly combine date and time for parsing
                        const datePart = pack.scheduled_date || new Date().toISOString().split('T')[0];
                        const startTime = new Date(`${datePart}T${pack.start_time}`);
                        const endTime = new Date(`${datePart}T${pack.end_time}`);

                        if (isNaN(startTime.getTime())) return; // Skip invalid dates

                        const diffMins = (startTime - now) / 60000;
                        const completedSubmission = App.getAllSubmissions().find(
                            s => String(s.pack_id) === String(pack.id) && String(s.candidate_name || '').toLowerCase() === String(App.getCandidateName() || '').toLowerCase()
                        );

                        let status = '';
                        let primaryAction = '';
                        let badgeClass = '';

                        if (completedSubmission) {
                            status = 'COMPLETED';
                            badgeClass = 'badge-green';
                            const resultsPublished = Number(pack.results_published) === 1 || pack.results_published === true || pack.results_published === '1';
                            if (resultsPublished) {
                                primaryAction = `<button type="button" class="px-4 py-1.5 bg-violet-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-violet-700 transition-all" onclick="App.openStudentResultSummaryModal(${pack.id})" title="View your results">
                                        <i class="bi bi-bar-chart-line me-1"></i> View Results
                                     </button>`;
                            } else {
                                primaryAction = `<span class="text-emerald-600 text-[10px] font-black uppercase tracking-widest">Completed</span>`;
                            }
                        } else
                        // 5-minute pre-test logic
                        if (diffMins > 5) {
                            status = 'SCHEDULED';
                            badgeClass = 'badge-blue';
                            primaryAction = `<span class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Locked</span>`;
                        } else if (diffMins <= 5 && now < startTime) {
                            status = 'READY';
                            badgeClass = 'badge-yellow';
                            primaryAction = `<button class="px-4 py-1.5 bg-slate-100 text-slate-400 rounded-lg text-[10px] font-black uppercase tracking-widest cursor-not-allowed border border-slate-200 shadow-sm" disabled>
                                    Waiting...
                                 </button>`;
                        } else if (now >= startTime && now < endTime) {
                            status = 'LIVE';
                            badgeClass = 'badge-green';
                            primaryAction = `<button class="px-4 py-1.5 bg-red-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-100 hover:bg-red-700 transition-all" onclick="App.startExecution('${test.id}', '${pack.id}')">
                                    <i class="bi bi-play-fill me-1"></i> Take Test
                                 </button>`;
                        } else {
                            status = 'EXPIRED';
                            badgeClass = 'badge-red';
                            primaryAction = `<span class="text-slate-300 text-[10px] font-black uppercase tracking-widest">Closed</span>`;
                        }
                        const action = `<div class="flex items-center justify-center gap-2">${primaryAction}</div>`;

                        html += `
                        <tr class="hover:bg-slate-50/50 transition-colors border-b border-slate-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 group-hover:text-red-600 transition-colors border border-slate-100">
                                        <i class="bi bi-file-earmark-text text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-[14px] font-black text-slate-800 leading-tight">${test.name}</div>
                                        <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mt-1">${pack.pack_name || 'Standard Batch'}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge-custom ${badgeClass} px-3 py-1.5 rounded-lg text-[10px] font-black tracking-widest">${status}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="text-[12px] font-black text-slate-700 uppercase tracking-wide">
                                        <i class="bi bi-calendar3 me-2 text-slate-400"></i>${startTime.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-bold flex items-center gap-2">
                                        <span class="px-2 py-0.5 bg-slate-100 rounded text-slate-600">${startTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                                        <span class="text-slate-300">—</span>
                                        <span class="px-2 py-0.5 bg-slate-50 rounded text-slate-400">${endTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">${action}</td>
                        </tr>
                    `;
                    });
                });

                if (!html) {
                    html = '<tr><td colspan="6" class="py-24 text-center"><div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-200"><i class="bi bi-calendar-x text-3xl"></i></div><p class="text-slate-400 font-black text-[11px] uppercase tracking-widest">No scheduled tests found</p></td></tr>';
                }
                body.innerHTML = html;
            }

            refresh();
            if (App.executionInterval) clearInterval(App.executionInterval);
            App.executionInterval = setInterval(refresh, 30000); // Check every 30s
        };
        let TestsDataTable = null;
        function initTestsDataTable() {
            if (typeof jQuery === 'undefined' || typeof $.fn.dataTable !== 'function') {
                return;
            }
            if ($.fn.dataTable.isDataTable('#TestsDataTable')) {
                return;
            }

            TestsDataTable = $('#TestsDataTable').DataTable({
                data: App.Tests,
                order: [[0, 'desc']],
                columns: [
                    { data: 'id', visible: false },
                    {
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: '',
                        width: '30px'
                    },
                    {
                        data: 'name',
                        render: (data, type, row) => `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center font-bold text-xs border border-red-100">
                                ${data.charAt(0)}
                            </div>
                            <div>
                                <div class="font-bold text-slate-700">${data}</div>
                                <div class="text-[10px] text-slate-400 font-medium">Created on ${new Date(row.created_at).toLocaleDateString()}</div>
                            </div>
                        </div>
                    `
                    },
                    {
                        data: 'category',
                        className: 'text-center',
                        render: (data) => `<span class="chip bg-blue-50 text-blue-600 border-blue-100">${data || 'General'}</span>`
                    },
                    {
                        data: 'assessment_type',
                        className: 'text-center',
                        render: (data) => `<span class="text-xs font-bold text-slate-600">${data || 'Standard'}</span>`
                    },
                    {
                        data: 'assigned_to',
                        className: 'text-center',
                        render: (data) => {
                            if (!data) return '<span class="text-slate-400">-</span>';
                            const roles = data.split(',');
                            return roles.map(r => `<span class="inline-block px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[9px] font-bold mr-1 mb-1 uppercase">${r}</span>`).join('');
                        }
                    },
                    {
                        data: 'test_packs',
                        className: 'text-center',
                        render: (data) => {
                            const packs = data || [];
                            const completed = packs.reduce((sum, p) => sum + App.getPackSubmissionStats(p.id).completed, 0);
                            return `
                                <div class="flex flex-col items-center">
                                    <span class="font-bold text-slate-700">${packs.length} <span class="text-slate-400 text-[10px] uppercase">Batches</span></span>
                                    <span class="text-[9px] font-black uppercase tracking-widest ${completed > 0 ? 'text-emerald-600' : 'text-slate-300'}">${completed} Completed</span>
                                </div>
                            `;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center px-6',
                        render: (data, type, row) => `
                        <div class="flex items-center justify-center gap-2">
                            <button class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 transition-all" onclick="event.stopPropagation(); App.openResultsForTest('${(row.name || '').replace(/'/g, "\\'")}')" title="Results & Evaluation">
                                <i class="bi bi-file-earmark-text"></i>
                            </button>
                            <button class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 transition-all" onclick="event.stopPropagation(); editTestById(${row.id})" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-all" onclick="event.stopPropagation(); deleteTest(${row.id})" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `
                    }
                ],
                pageLength: 7,
                lengthChange: false,
                dom: 'rt<"px-6 py-4 flex justify-between items-center"ip>',
                language: {
                    emptyTable: "No tests found matching your criteria"
                },
                drawCallback: function () {
                    $('.dataTables_filter input').addClass('form-control form-control-sm border-slate-200 rounded-lg text-xs w-[250px] shadow-none focus:border-red-500');
                }
            });

            // Add event listener for opening and closing details
            $('#TestsDataTable tbody').on('click', 'td.dt-control', function () {
                var tr = $(this).closest('tr');
                var row = TestsDataTable.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('dt-hasChild');
                } else {
                    row.child(formatTestPacks(row.data())).show();
                    initBatchDataTable(row.data().id, row.data().test_packs);
                    tr.addClass('dt-hasChild');
                }
            });
        }

        function toggleViewTest(btn) {
            $(btn).closest('tr').find('td.dt-control').click();
        }

        function filterByType(val) {
            if (TestsDataTable) {
                TestsDataTable.column(4).search(val).draw();
            }
        }

        function searchTests(val) {
            if (TestsDataTable) {
                TestsDataTable.search(val).draw();
            }
        }

        function formatTestPacks(d) {
            let packs = d.test_packs || [];

            let html = `
            <div class="bg-slate-50/50 py-6 px-4 border-y border-slate-100 child-table-container">
                <div class="flex items-center justify-between mb-6 px-2">
                    <div>
                        <h5 class="text-[11px] font-black text-slate-800 uppercase tracking-[0.2em] mb-1">Assigned Batches</h5>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Manage and schedule test deployments for "${d.name}"</p>
                    </div>
                    <button type="button" class="px-5 py-2.5 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-100 hover:bg-red-700 transition-all flex items-center gap-2" onclick="addNewBatchRow('${d.id}')">
                        <i class="bi bi-plus-lg"></i> Add New Group
                    </button>
                </div>

                <div class="bg-white rounded-[24px] border border-slate-200 overflow-visible shadow-sm">
                    <table id="BatchTable_${d.id}" class="w-full text-left border-collapse">
                        <thead class="bg-slate-50/50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Batch Name</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Select Template</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Assign To</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Start Time</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">End Time</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Duration</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50"></tbody>
                    </table>
                </div>
            </div>`;
            return html;
        }

        function initBatchDataTable(testId, packs) {
            $(`#BatchTable_${testId}`).DataTable({
                data: packs,
                rowCallback: function (row, data) {
                    if (data.status === 'published') {
                        $(row).addClass('is-published');
                    } else {
                        $(row).removeClass('is-published');
                    }

                    if (data._isEditing) {
                        $(row).addClass('is-editing');
                    } else {
                        $(row).removeClass('is-editing');
                    }
                },
                columns: [
                    {
                        data: 'pack_name',
                        width: '18%',
                        className: 'px-4 py-3',
                        render: (data, type, row) => {
                            const stats = App.getPackSubmissionStats(row.id);
                            return `
                            <div class="flex flex-col gap-1">
                                <input type="text" class="inline-editable-input" data-field="pack_name" value="${data}" placeholder="e.g. Morning Batch" ${row.status === 'published' ? 'readonly' : ''}>
                                ${row.status === 'published' ? '<div class="published-badge"><i class="bi bi-shield-check"></i> Published</div>' : ''}
                                ${stats.total > 0 ? `<div class="text-[9px] font-black uppercase tracking-widest ${stats.subjectivePending > 0 ? 'text-amber-600' : 'text-emerald-600'}">${stats.completed}/${stats.total} Completed • ${stats.subjectivePending} Pending Eval</div>` : ''}
                            </div>
                        `
                        }
                    },
                    {
                        data: 'template_id',
                        width: '15%',
                        className: 'px-4 py-3',
                        render: (data, type, row) => {
                            let options = App.templates.map(t => `<option value="${t.id}" ${t.id == data ? 'selected' : ''}>${t.name}</option>`).join('');
                            return `
                                <div class="inline-select-container">
                                    <select class="inline-editable-input appearance-none pr-8" data-field="template_id" ${row.status === 'published' ? 'disabled' : ''}>
                                        <option value="">Select Template</option>
                                        ${options}
                                    </select>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'candidates',
                        width: '18%',
                        className: 'px-4 py-3',
                        render: (data, type, row) => {
                            const type_val = row.candidates_type || (data && data !== 'all' ? 'specific' : 'all');
                            const isAll = type_val === 'all';
                            const val = data || 'all';
                            const totalCount = App.employees ? App.employees.length : 0;
                            const specificCount = (val && val !== 'all') ? val.split(',').length : 0;
                            const isPublished = row.status === 'published';
                            const selectedIds = (val && val !== 'all')
                                ? val.split(',').filter(Boolean)
                                : (App.employees || []).map(emp => String(emp.id));
                            const selectedNames = (App.employees || [])
                                .filter(emp => selectedIds.includes(String(emp.id)))
                                .map(emp => emp.name)
                                .slice(0, 2);
                            const display = selectedIds.length === totalCount
                                ? `All Candidates (${totalCount})`
                                : (selectedNames.length
                                    ? `${selectedNames.join(', ')}${selectedIds.length > 2 ? ` +${selectedIds.length - 2}` : ''}`
                                    : `Selected (${specificCount})`);
                            const selectAllChecked = selectedIds.length > 0 && selectedIds.length === totalCount ? 'checked' : '';
                            const candidateRows = (App.employees || []).map(emp => {
                                const empId = String(emp.id);
                                const checked = selectedIds.includes(empId) ? 'checked' : '';
                                return `
                                    <label class="batch-candidate-item" data-name="${escapeHtml(emp.name || '').toLowerCase()}" data-email="${escapeHtml(emp.email || '').toLowerCase()}">
                                        <input type="checkbox" class="form-check-input batch-candidate-check" value="${empId}" ${checked}
                                            onchange="toggleBatchCandidateCheckbox(this)" ${isPublished ? 'disabled' : ''}>
                                        <div class="min-w-0">
                                            <div class="name truncate">${escapeHtml(emp.name || 'Candidate')}</div>
                                            <div class="meta truncate">${escapeHtml(emp.email || '')}</div>
                                        </div>
                                    </label>
                                `;
                            }).join('');

                            return `
                                <div class="candidate-selector-wrapper">
                                    <div class="edit-view">
                                        <div class="batch-candidate-inline">
                                            <button type="button" class="batch-candidate-trigger" onclick="toggleBatchCandidateDropdown(this)" ${isPublished ? 'disabled' : ''}>
                                                <span class="truncate batch-candidate-trigger-label">${display}</span>
                                                <i class="bi bi-chevron-down text-[10px] text-slate-400"></i>
                                            </button>
                                            <div class="batch-candidate-dropdown hidden">
                                                <input type="text" class="inline-editable-input !h-[34px] !text-[11px] !font-semibold !px-2.5 !py-1.5 !rounded-lg !border-slate-200" placeholder="Search candidates..." oninput="filterBatchCandidateOptions(this)">
                                                <label class="flex items-center justify-between gap-2 mt-2 text-[10px] font-black text-slate-500 uppercase tracking-wider">
                                                    <span class="flex items-center gap-2">
                                                        <input type="checkbox" class="form-check-input batch-candidate-select-all" onchange="toggleBatchCandidateSelectAll(this)" ${selectAllChecked} ${isPublished ? 'disabled' : ''}>
                                                        Select All
                                                    </span>
                                                    <span class="batch-candidate-count">${isAll ? totalCount : specificCount} selected</span>
                                                </label>
                                                <div class="batch-candidate-list">
                                                    ${candidateRows || '<div class="text-[11px] text-slate-400 italic py-2">No candidates available.</div>'}
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" data-field="candidates_type" value="${type_val}">
                                        <input type="hidden" data-field="candidates" value="${val}">
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'start_time',
                        width: '12%',
                        className: 'px-4 py-3',
                        render: (data, type, row) => {
                            let dateVal = row.scheduled_date || (data ? data.split(' ')[0] : '');
                            return `
                                <input type="date" class="inline-editable-input" data-field="scheduled_date" value="${dateVal}" ${row.status === 'published' ? 'readonly' : ''}>
                            `;
                        }
                    },
                    {
                        data: 'start_time',
                        width: '10%',
                        className: 'px-4 py-3',
                        render: (data, type, row) => {
                            let timeVal = data ? (data.includes(' ') ? data.split(' ')[1].substring(0, 5) : data.substring(0, 5)) : '';
                            return `
                                <input type="time" class="inline-editable-input px-2" data-field="start_time" value="${timeVal}" oninput="updateDuration(this)" ${row.status === 'published' ? 'readonly' : ''}>
                            `;
                        }
                    },
                    {
                        data: 'end_time',
                        width: '10%',
                        className: 'px-4 py-3',
                        render: (data, type, row) => {
                            let timeVal = data ? (data.includes(' ') ? data.split(' ')[1].substring(0, 5) : data.substring(0, 5)) : '';
                            return `
                                <input type="time" class="inline-editable-input px-2" data-field="end_time" value="${timeVal}" oninput="updateDuration(this)" ${row.status === 'published' ? 'readonly' : ''}>
                            `;
                        }
                    },
                    {
                        data: 'duration',
                        width: '8%',
                        className: 'text-center px-4 py-3',
                        render: (data, type, row) => `
                            <div class="flex items-center justify-center gap-1">
                                <input type="number" class="inline-editable-input text-center px-1" style="width: 50px" data-field="duration" value="${data || 60}" ${row.status === 'published' ? 'readonly' : ''}>
                                <span class="text-[9px] font-black text-slate-300 uppercase">m</span>
                            </div>
                        `
                    },
                    {
                        data: null,
                        className: 'text-right px-4 py-3 whitespace-nowrap',
                        width: '15%',
                        render: (data, type, row) => {
                            const isPublished = row.status === 'published';
                            const stats = App.getPackSubmissionStats(row.id);
                            const resultsPublished = Number(row.results_published) === 1 || row.results_published === true || row.results_published === '1';
                            const canPublishResults = isPublished && stats.total > 0 && stats.completed === stats.total && stats.subjectivePending === 0 && !resultsPublished;
                            return `
                            <div class="flex flex-nowrap items-center justify-end gap-1" data-batch-id="${row.id}">
                                ${!isPublished ? `
                                    <button class="batch-action-btn bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white" onclick="saveBatchInline(this, '${row.id}', '${testId}')" title="Save Changes" data-action="save">
                                        <i class="bi bi-floppy-fill"></i>
                                    </button>
                                    <button class="batch-action-btn bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white" onclick="toggleEditBatch(this)" title="Edit Row" data-action="edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="batch-action-btn bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white" onclick="publishBatch(this, '${row.id}', '${testId}')" title="Publish Batch">
                                        <i class="bi bi-send-check"></i>
                                    </button>
                                    <button class="batch-action-btn bg-red-50 text-red-600 hover:bg-red-600 hover:text-white" onclick="deletePack('${row.id}')" title="Delete Batch">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                ` : `
                                    <div class="w-8 h-8 flex items-center justify-center text-emerald-500 bg-emerald-50 rounded-lg" title="Batch Published & Locked">
                                        <i class="bi bi-lock-fill"></i>
                                    </div>
                                    ${canPublishResults ? `
                                        <button type="button" class="batch-action-btn bg-violet-50 text-violet-600 hover:bg-violet-600 hover:text-white" onclick="publishEvaluatedPackResults(this, '${row.id}', '${testId}')" title="Publish evaluated results to candidates">
                                            <i class="bi bi-megaphone-fill"></i>
                                        </button>
                                    ` : ''}
                                    ${resultsPublished ? `
                                        <div class="w-8 h-8 flex items-center justify-center text-violet-600 bg-violet-50 rounded-lg" title="Results published to candidates">
                                            <i class="bi bi-check2-circle"></i>
                                        </div>
                                    ` : ''}
                                `}
                                <button class="batch-action-btn bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white" onclick="App.previewTestPack('${row.id}')" title="View Question Paper">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>`

                        }
                    }
                ],
                dom: 't',
                ordering: false,
                language: {
                    emptyTable: "No Batches found for this Test."
                }
            });
        }

        function updateDuration(input) {
            const tr = $(input).closest('tr');
            const startTime = tr.find('[data-field="start_time"]').val();
            const endTime = tr.find('[data-field="end_time"]').val();

            if (startTime && endTime) {
                const start = new Date(`2000-01-01T${startTime}`);
                let end = new Date(`2000-01-01T${endTime}`);

                if (end < start) {
                    end = new Date(`2000-01-02T${endTime}`);
                }

                const diff = (end - start) / (1000 * 60);
                tr.find('[data-field="duration"]').val(Math.max(0, Math.floor(diff)));
            }
        }

        async function publishBatch(btn, batchId, testId) {
            if (!batchId) {
                Swal.fire('Wait', 'Please save the batch before publishing.', 'info');
                return;
            }

            const result = await Swal.fire({
                title: 'Publish Batch?',
                text: "Once published, this batch cannot be edited.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#f1f5f9',
                confirmButtonText: 'Yes, Publish it!',
                customClass: {
                    cancelButton: '!text-slate-600'
                }
            });

            if (result.isConfirmed) {
                btn.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
                btn.disabled = true;

                try {
                    const response = await fetch('Test/publishTestPack', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `id=${batchId}`
                    });

                    const data = await response.json();
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Published!',
                            text: 'Batch has been locked and published.',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        const table = $(`#BatchTable_${testId}`).DataTable();
                        const tr = $(btn).closest('tr');
                        const row = table.row(tr);
                        const rowData = row.data();
                        rowData.status = 'published';
                        row.data(rowData).draw(false);

                        // Real-time sync: Refresh execution dashboard if batch is published
                        if (App.initExecutionDashboard) App.initExecutionDashboard();
                    } else {
                        Swal.fire('Error', data.message || 'Failed to publish', 'error');
                        btn.innerHTML = '<i class="bi bi-send-check"></i>';
                        btn.disabled = false;
                    }
                } catch (error) {
                    Swal.fire('Error', 'An unexpected error occurred.', 'error');
                    btn.innerHTML = '<i class="bi bi-send-check"></i>';
                    btn.disabled = false;
                }
            }
        }

        async function publishEvaluatedPackResults(btn, batchId, testId) {
            if (!batchId) return;
            const warn = await Swal.fire({
                title: 'Publish results to candidates?',
                text: 'Candidates who completed this batch can view their results from Scheduled & Live Tests.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#f1f5f9',
                confirmButtonText: 'Yes, publish',
                customClass: { cancelButton: '!text-slate-600' }
            });
            if (!warn.isConfirmed) return;

            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
            btn.disabled = true;

            try {
                const response = await fetch('Test/publishPackResults', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${encodeURIComponent(batchId)}`
                });
                const data = await response.json();
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Results published',
                        text: 'Candidates can now open their results.',
                        timer: 1800,
                        showConfirmButton: false
                    });

                    const table = $(`#BatchTable_${testId}`).DataTable();
                    const tr = $(btn).closest('tr');
                    const row = table.row(tr);
                    const rowData = row.data();
                    rowData.results_published = 1;
                    row.data(rowData).draw(false);

                    const testRow = App.Tests.find(t => String(t.id) === String(testId));
                    if (testRow && testRow.test_packs) {
                        const pack = testRow.test_packs.find(p => String(p.id) === String(batchId));
                        if (pack) pack.results_published = 1;
                    }
                    if (App.initExecutionDashboard) App.initExecutionDashboard();
                } else {
                    Swal.fire('Error', data.message || 'Could not publish results', 'error');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            } catch (e) {
                console.error(e);
                Swal.fire('Error', 'An unexpected error occurred.', 'error');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        }

        function addNewBatchRow(testId) {
            const table = $(`#BatchTable_${testId}`).DataTable();
            const newRowData = {
                id: '',
                pack_name: '',
                template_id: '',
                candidates: 'all',
                status: 'draft',
                results_published: 0,
                _isEditing: true,
                scheduled_date: new Date().toISOString().split('T')[0],
                start_time: '10:00',
                end_time: '11:00',
                duration: 60
            };

            table.row.add(newRowData).draw(false);

            setTimeout(() => {
                const trNode = $(`#BatchTable_${testId} tbody tr`).last();
                trNode[0]?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                trNode.find('input').first().focus();
            }, 100);
        }



        function handleCandidateTypeChange() { }

        function syncBatchCandidateSummary(wrapperEl) {
            if (!wrapperEl) return;
            const hiddenInput = wrapperEl.querySelector('input[data-field="candidates"]');
            const typeInput = wrapperEl.querySelector('[data-field="candidates_type"]');
            const displaySpan = wrapperEl.querySelector('.readonly-view span');
            const triggerLabel = wrapperEl.querySelector('.batch-candidate-trigger-label');
            const countLabel = wrapperEl.querySelector('.batch-candidate-count');
            const selectAll = wrapperEl.querySelector('.batch-candidate-select-all');
            const checks = Array.from(wrapperEl.querySelectorAll('.batch-candidate-check'));
            const selected = checks.filter(cb => cb.checked).map(cb => cb.value);
            const totalCount = checks.length;
            const selectedCount = selected.length;
            const selectedNames = checks
                .filter(cb => cb.checked)
                .map(cb => cb.closest('.batch-candidate-item')?.querySelector('.name')?.textContent?.trim())
                .filter(Boolean);
            const previewNames = selectedNames.slice(0, 2).join(', ');
            const isAllSelected = selectedCount === totalCount || selectedCount === 0;

            if (hiddenInput) hiddenInput.value = isAllSelected ? 'all' : selected.join(',');
            if (typeInput) typeInput.value = isAllSelected ? 'all' : 'specific';

            const summary = isAllSelected
                ? `All Candidates (${totalCount})`
                : `${previewNames}${selectedCount > 2 ? ` +${selectedCount - 2}` : ''}`;

            if (displaySpan) displaySpan.textContent = summary;
            if (triggerLabel) triggerLabel.textContent = summary;
            if (countLabel) {
                countLabel.textContent = isAllSelected ? `${totalCount} selected` : `${selectedCount} selected`;
            }

            if (selectAll) {
                selectAll.checked = totalCount > 0 && selectedCount === totalCount;
                selectAll.indeterminate = selectedCount > 0 && selectedCount < totalCount;
            }
        }

        function closeAllBatchCandidateDropdowns(exceptWrapper = null) {
            document.querySelectorAll('.candidate-selector-wrapper .batch-candidate-dropdown').forEach(dd => {
                const wrapper = dd.closest('.candidate-selector-wrapper');
                if (exceptWrapper && wrapper === exceptWrapper) return;
                dd.classList.add('hidden');
            });
        }

        function toggleBatchCandidateDropdown(btn) {
            const wrapper = btn.closest('.candidate-selector-wrapper');
            const dropdown = wrapper ? wrapper.querySelector('.batch-candidate-dropdown') : null;
            if (!wrapper || !dropdown) return;

            const willOpen = dropdown.classList.contains('hidden');
            closeAllBatchCandidateDropdowns(wrapper);
            dropdown.classList.toggle('hidden', !willOpen);
            if (willOpen) {
                syncBatchCandidateSummary(wrapper);
                const searchInput = dropdown.querySelector('input[type="text"]');
                if (searchInput) searchInput.focus();
            }
        }

        function filterBatchCandidateOptions(input) {
            const wrapper = input.closest('.candidate-selector-wrapper');
            if (!wrapper) return;
            const keyword = String(input.value || '').trim().toLowerCase();
            wrapper.querySelectorAll('.batch-candidate-item').forEach(item => {
                const name = item.getAttribute('data-name') || '';
                const email = item.getAttribute('data-email') || '';
                const match = !keyword || name.includes(keyword) || email.includes(keyword);
                item.classList.toggle('hidden', !match);
            });
        }

        function toggleBatchCandidateSelectAll(check) {
            const wrapper = check.closest('.candidate-selector-wrapper');
            if (!wrapper) return;
            const items = Array.from(wrapper.querySelectorAll('.batch-candidate-item'));
            items.forEach(item => {
                if (item.classList.contains('hidden')) return;
                const cb = item.querySelector('.batch-candidate-check');
                if (cb && !cb.disabled) cb.checked = check.checked;
            });
            syncBatchCandidateSummary(wrapper);
        }

        function toggleBatchCandidateCheckbox(input) {
            const wrapper = input.closest('.candidate-selector-wrapper');
            syncBatchCandidateSummary(wrapper);
        }

        function toggleEditBatch(btn) {
            const tr = $(btn).closest('tr');
            const table = tr.closest('table').DataTable();
            const row = table.row(tr);
            const data = row.data();

            // Close other editing rows if any
            table.rows().every(function () {
                const d = this.data();
                if (d._isEditing && d.id !== data.id) {
                    d._isEditing = false;
                    this.data(d);
                }
            });

            data._isEditing = !data._isEditing;
            row.data(data).draw(false);

            if (data._isEditing) {
                setTimeout(() => {
                    $(row.node()).find('input').first().focus();
                }, 50);
            }
        }

        async function saveBatchInline(btn, batchId, testId) {
            const tr = $(btn).closest('tr');
            const data = {
                id: batchId,
                assessment_id: testId,
                pack_name: tr.find('[data-field="pack_name"]').val(),
                template_id: tr.find('[data-field="template_id"]').val(),
                candidates: tr.find('[data-field="candidates"]').val(),
                scheduled_date: tr.find('[data-field="scheduled_date"]').val(),
                start_time: tr.find('[data-field="start_time"]').val(),
                end_time: tr.find('[data-field="end_time"]').val(),
                duration: tr.find('[data-field="duration"]').val(),
                candidates_type: tr.find('[data-field="candidates_type"]').val()
            };

            if (!data.pack_name || !data.template_id) {
                Swal.fire('Required', 'Batch Name and Template are mandatory.', 'warning');
                return;
            }

            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
            btn.disabled = true;

            try {
                const formData = new URLSearchParams();
                for (const key in data) {
                    formData.append(key, data[key]);
                }

                const response = await fetch('Test/createTestPack', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                });

                const result = await response.json();
                if (result.status === 'success') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.id ? 'Batch updated successfully' : 'Batch created successfully',
                        showConfirmButton: false,
                        timer: 2000
                    });

                    // Update the row data with the new ID and exit edit mode
                    const table = $(`#BatchTable_${testId}`).DataTable();
                    const row = table.row(tr);
                    const originalData = row.data();
                    const updatedData = { ...originalData, ...data, id: result.id, status: 'draft', _isEditing: false };
                    row.data(updatedData).draw(false);
                } else {
                    Swal.fire('Error', result.message || 'Failed to save batch', 'error');
                }
            } catch (error) {
                console.error('Save error:', error);
                Swal.fire('Error', 'An unexpected error occurred.', 'error');
            } finally {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        }

        var currentTestIdForPack = null;
        function toggleInlinePackForm(id) {
            currentTestIdForPack = id;

            // Reset form
            document.getElementById('pack_wizard_name').value = '';
            document.getElementById('baseTemplateSelect').selectedIndex = 0;
            document.getElementById('pack_duration').value = '60';
            document.getElementById('pack_user_role').value = 'General Access';
            document.getElementById('pack_start_time').value = '';
            document.getElementById('pack_end_time').value = '';

            // Reset candidates
            App.selectedCandidates[id] = [];
            updateWizardCandidateLabel();

            clearValidationErrors();

            // Open modal
            const modalEl = document.getElementById('createPackModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }

        let cpActiveTestId = null;
        function openCandidatePicker(TestId, rolesStr) {
            cpActiveTestId = TestId;
            const roles = (rolesStr && rolesStr !== 'undefined' && rolesStr !== 'null') ? rolesStr.split(',') : [];
            const container = document.getElementById('cp_list_container');
            const roleLabel = document.getElementById('cp_role_label');

            // Filter employees by role
            const filtered = App.employees.filter(emp => {
                if (roles.length === 0) return true;
                // Map common roles to employee data if necessary
                // For now, assuming internal employees match general roles
                return emp.type === 'internal';
            });

            roleLabel.textContent = `${filtered.length} ${roles.length > 0 ? roles.join('/') : 'Candidates'} Available`;

            const selected = App.selectedCandidates[TestId] || [];

            container.innerHTML = filtered.map(emp => `
            <label class="flex items-center gap-3 p-3 hover:bg-white rounded-xl cursor-pointer transition-colors border border-transparent hover:border-slate-100 mb-1">
                <input type="checkbox" class="form-check-input cp-check" value="${emp.id}" ${selected.includes(emp.id.toString()) ? 'checked' : ''} onchange="updateCPCount()">
                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500 font-bold text-xs">
                    ${emp.name.charAt(0)}
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-700">${emp.name}</div>
                    <div class="text-[10px] text-slate-400">${emp.email}</div>
                </div>
            </label>
        `).join('');

            if (filtered.length === 0) {
                container.innerHTML = '<div class="text-center py-8 text-slate-400 text-xs italic">No candidates found for these roles.</div>';
            }

            updateCPCount();
            openModal('candidatePickerModal');
        }

        function updateCPCount() {
            const count = document.querySelectorAll('.cp-check:checked').length;
            document.getElementById('cp_count_label').textContent = `${count} selected`;
        }

        function selectAllCandidates(check) {
            document.querySelectorAll('.cp-check').forEach(cb => cb.checked = check);
            updateCPCount();
        }

        function confirmCandidateSelection() {
            const selected = Array.from(document.querySelectorAll('.cp-check:checked')).map(cb => cb.value);
            App.selectedCandidates[cpActiveTestId] = selected;

            const label = document.getElementById(`candidateCountLabel_${cpActiveTestId}`);
            if (label) label.textContent = `${selected.length} Selected`;

            // Also update wizard label if active
            updateWizardCandidateLabel();

            // Trigger hook for inline batch editing if active
            if (typeof App.saveSelectedCandidates === 'function') {
                App.saveSelectedCandidates();
            }

            closeModal('candidatePickerModal');
        }

        function openCandidatePickerForWizard() {
            if (!currentTestIdForPack) return;
            const test = App.Tests.find(t => t.id == currentTestIdForPack);
            openCandidatePicker(currentTestIdForPack, test ? test.assigned_to : '');
        }

        function updateWizardCandidateLabel() {
            if (!currentTestIdForPack) return;
            const selectedIds = App.selectedCandidates[currentTestIdForPack] || [];
            const count = selectedIds.length;

            // 1. Update Legacy Label (if any)
            const legacyLabel = document.getElementById('wizardCandidateCountLabel');
            if (legacyLabel) legacyLabel.textContent = count > 0 ? `${count} Candidates Selected` : 'Select Candidates';

            // 2. Update New High-Density Wizard Summary
            const countDisplay = document.getElementById('wizard_selected_count');
            const roleDisplay = document.getElementById('wizard_selected_role');
            const avatarContainer = document.getElementById('wizard_candidate_avatars');

            if (countDisplay) countDisplay.textContent = count > 0 ? `${count} Selected` : '0 Selected';

            if (roleDisplay) {
                const roleInput = document.getElementById('pack_user_role');
                roleDisplay.textContent = roleInput ? roleInput.value.toUpperCase() : 'NO ROLE ASSIGNED';
            }

            if (avatarContainer) {
                if (count === 0) {
                    avatarContainer.innerHTML = '';
                } else {
                    // Get selected employee objects
                    const selectedEmps = App.employees.filter(emp => selectedIds.includes(emp.id.toString()));
                    const displayEmps = selectedEmps.slice(0, 5);

                    let html = displayEmps.map(emp => `
                    <div class="w-8 h-8 rounded-full border-2 border-white bg-white flex items-center justify-center text-[10px] font-black text-slate-400 shadow-sm" title="${emp.name}">
                        ${emp.name.charAt(0)}
                    </div>
                `).join('');

                    if (count > 5) {
                        html += `
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-800 text-white flex items-center justify-center text-[10px] font-black shadow-sm">
                            +${count - 5}
                        </div>
                    `;
                    }
                    avatarContainer.innerHTML = html;
                }
            }
        }

        function selectTemplate(templateId, clearQuestions = false, keepBuilderView = false) {
            document.getElementById('baseTemplateSelect').value = templateId;
            isInlineTemplateEditMode = false;

            // Update UI Highlights
            document.querySelectorAll('.template-card').forEach(card => {
                card.classList.remove('border-red-600', 'bg-red-50');
                const badge = card.querySelector('.check-badge');
                if (badge) badge.classList.add('opacity-0');
            });

            const selectedCard = document.getElementById('temp_card_' + templateId);
            if (selectedCard) {
                selectedCard.classList.add('border-red-600', 'bg-red-50');
                const badge = selectedCard.querySelector('.check-badge');
                if (badge) badge.classList.remove('opacity-0');
            }

            // Update Center Column Details
            updateTemplateDetails(templateId, clearQuestions);
            updateQuickModeFooterVisibility();

            // If in template mode, switch back to batch view to show the details
            const builderView = document.getElementById('templateBuilderInlineView');
            if (!keepBuilderView && builderView && !builderView.classList.contains('hidden')) {
                toggleWizardView('batch');
            }

            // Find template data and update duration
            const t = App.templates.find(item => item.id == templateId);
            if (t) {
                // Auto-fill batch name if empty
                const qBatchName = document.getElementById('quick_batch_name');
                const todayStr = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
                if (qBatchName && !qBatchName.value) {
                    qBatchName.value = t.name + ' - ' + todayStr;
                    document.getElementById('pack_wizard_name').value = qBatchName.value;
                }

                const duration = t.duration || 60;
                const durationInput = document.getElementById('pack_duration');
                if (durationInput) durationInput.value = duration;

                // Also sync to quick mode duration if visible
                const qBatchDuration = document.getElementById('quick_batch_duration');
                if (qBatchDuration) qBatchDuration.value = duration;

                updateSummary();
            }
        }

        function syncSidebarToMain(mainId, value) {
            const mainEl = document.getElementById(mainId);
            if (mainEl) {
                if (mainEl.type === 'checkbox') {
                    mainEl.checked = value;
                } else {
                    mainEl.value = value;
                }
                updateSummary();
            }
        }

        function toggleSidebarOption(mainId) {
            const mainEl = document.getElementById(mainId);
            if (mainEl) {
                mainEl.checked = !mainEl.checked;
                updateSummary();
            }
        }

        function updateSummary() {
            const start = document.getElementById('pack_start_time').value;
            const end = document.getElementById('pack_end_time').value;
            const date = document.getElementById('pack_scheduled_date').value;
            const duration = document.getElementById('pack_duration').value;
            const instructions = document.getElementById('pack_instructions').value;
            const passMark = document.getElementById('pack_pass_mark').value;
            const attempts = document.getElementById('pack_attempts').value;
            const name = document.getElementById('pack_wizard_name').value;
            const category = document.getElementById('pack_user_role').value;

            // Ensure candidate summary is updated (especially the role display)
            // updateWizardCandidateLabel();

            const shuffle = document.getElementById('pack_shuffle').checked;
            const shuffle_options = document.getElementById('pack_shuffle_options').checked;
            const proctored = document.getElementById('pack_proctored').checked;
            const lockdown = document.getElementById('pack_lockdown').checked;
            const show_results = document.getElementById('pack_show_results').checked;
            const allow_backtracking = document.getElementById('pack_allow_backtracking').checked;

            // Sync Sidebar Inputs (Two-way)
            if (document.getElementById('summary_name')) document.getElementById('summary_name').value = name;
            if (document.getElementById('summary_category')) document.getElementById('summary_category').value = category;
            if (document.getElementById('summary_date_input')) document.getElementById('summary_date_input').value = date;
            if (document.getElementById('summary_start_input')) document.getElementById('summary_start_input').value = start;
            if (document.getElementById('summary_end_input')) document.getElementById('summary_end_input').value = end;
            if (document.getElementById('summary_duration_input')) document.getElementById('summary_duration_input').value = duration;
            if (document.getElementById('summary_instructions_input')) document.getElementById('summary_instructions_input').value = instructions;
            if (document.getElementById('summary_pass_mark_input')) document.getElementById('summary_pass_mark_input').value = passMark;
            if (document.getElementById('summary_attempts_input')) document.getElementById('summary_attempts_input').value = attempts;

            // Security Toggles (Visual Sync)
            const updateToggle = (id, state) => {
                const el = document.getElementById(id);
                if (el) {
                    const track = el.querySelector('.toggle-track');
                    const thumb = el.querySelector('.toggle-thumb');
                    if (state) {
                        track.classList.replace('bg-slate-200', 'bg-red-600');
                        thumb.classList.add('translate-x-4');
                    } else {
                        track.classList.replace('bg-red-600', 'bg-slate-200');
                        thumb.classList.remove('translate-x-4');
                    }
                }
            };

            updateToggle('summary_shuffle_wrap', shuffle);
            updateToggle('summary_shuffle_options_wrap', shuffle_options);
            updateToggle('summary_proctored_wrap', proctored);
            updateToggle('summary_lockdown_wrap', lockdown);
            updateToggle('summary_show_results_wrap', show_results);
            updateToggle('summary_allow_backtracking_wrap', allow_backtracking);

            // Instructions Count
            if (document.getElementById('summary_instructions_count')) {
                document.getElementById('summary_instructions_count').textContent = `${instructions.length} / 2000 characters`;
            }
        }


        async function savePackFromWizard() {
            // Ensure Quick Mode inputs are synced to master
            const qName = document.getElementById('quick_batch_name');
            const qDuration = document.getElementById('quick_batch_duration');
            if (qName) document.getElementById('pack_wizard_name').value = qName.value;
            if (qDuration) document.getElementById('pack_duration').value = qDuration.value;

            console.log("Save Triggered. Syncing values...", {
                batchName: document.getElementById('pack_wizard_name').value,
                duration: document.getElementById('pack_duration').value
            });

            const modalEl = document.getElementById('createPackModal');
            const isQuickMode = modalEl && modalEl.classList.contains('quick-mode');
            const qbSelect = document.getElementById('quick_qb_select');
            const qbId = qbSelect ? qbSelect.value : '';

            if (!validatePackWizard()) {
                const name = document.getElementById('pack_wizard_name').value.trim();
                const template = document.getElementById('baseTemplateSelect').value;
                const duration = document.getElementById('pack_duration').value;
                let missing = [];
                if (!name) missing.push("Batch Name");
                if (!template) missing.push("Template Selection");
                if (!duration || parseInt(duration) <= 0) missing.push("Valid Duration");
                if (isQuickMode && !qbId) missing.push("Question Bank");

                Swal.fire({
                    title: 'Incomplete Form',
                    html: `Please check the following required fields:<br><br><ul class="text-left text-xs text-red-600 font-bold list-disc pl-5">` +
                        missing.map(m => `<li>${m}</li>`).join('') + `</ul>`,
                    icon: 'warning'
                });
                return;
            }

            const testId = currentTestIdForPack;
            const packName = document.getElementById('pack_wizard_name').value;
            const templateSelect = document.getElementById('baseTemplateSelect');
            const templateId = templateSelect ? templateSelect.value : '';
            const duration = document.getElementById('pack_duration').value;
            const userRole = document.getElementById('pack_user_role').value;
            const startTime = document.getElementById('pack_scheduled_date').value + ' ' + document.getElementById('pack_start_time').value;
            const endTime = document.getElementById('pack_scheduled_date').value + ' ' + document.getElementById('pack_end_time').value;
            const candidates = App.selectedCandidates[testId] || [];

            if (isQuickMode) {
                const paper = await generateQuestionsForQuickMode(templateId, qbId);
                if (!paper) return;
            }

            Swal.fire({
                title: 'Saving Test Data...',
                text: 'Persisting batch settings and question assignments.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const formData = new FormData();
                if (currentEditPackId) {
                    formData.append('id', currentEditPackId);
                }
                formData.append('assessment_id', testId);
                formData.append('template_id', templateId);
                formData.append('pack_name', packName);
                formData.append('user_role', userRole);
                formData.append('duration', duration);
                formData.append('start_time', startTime);
                formData.append('end_time', endTime);
                formData.append('instructions', document.getElementById('pack_instructions').value);
                formData.append('pass_mark', document.getElementById('pack_pass_mark').value);
                formData.append('max_attempts', document.getElementById('pack_attempts').value);
                formData.append('shuffle_questions', document.getElementById('pack_shuffle').checked ? 1 : 0);
                formData.append('shuffle_options', document.getElementById('pack_shuffle_options').checked ? 1 : 0);
                formData.append('proctored_exam', document.getElementById('pack_proctored').checked ? 1 : 0);
                formData.append('browser_lockdown', document.getElementById('pack_lockdown').checked ? 1 : 0);
                formData.append('show_results', document.getElementById('pack_show_results').checked ? 1 : 0);
                formData.append('allow_backtracking', document.getElementById('pack_allow_backtracking').checked ? 1 : 0);
                formData.append('candidates', candidates.join(','));
                formData.append('manual_questions', JSON.stringify(App.manualQuestions));
                formData.append('selected_qb_id', qbId || '');
                formData.append('sync_template_questions', isQuickMode ? '1' : '0');

                // Log the exact payload for debugging as requested
                console.group("🚀 Final Save Payload");
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}:`, value);
                }
                console.groupEnd();

                const response = await fetch('Test/createTestPack', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.status === 'success') {
                    if (templateId) {
                        const templateIndex = (App.templates || []).findIndex(item => item.id == templateId);
                        if (templateIndex !== -1) {
                            const updatedTemplate = {
                                ...App.templates[templateIndex],
                                ...(result.template || {}),
                                questions: normalizeQuestionList((result.template && result.template.questions) || App.manualQuestions)
                            };
                            if (result.template && result.template.sections) {
                                updatedTemplate.sections = result.template.sections;
                            }
                            App.templates[templateIndex] = updatedTemplate;
                        }
                    }

                    Swal.fire({
                        title: 'Success!',
                        text: 'Test batch and assignments saved successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        if (typeof renderTable === 'function') renderTable();
                        closeModal('createPackModal');
                    });
                } else {
                    throw new Error(result.message || 'Failed to save batch');
                }
            } catch (e) {
                console.error(e);
                Swal.fire('Error', e.message || 'Failed to save Batch.', 'error');
            }
        }

        var currentEditingTemplateIdInline = null;
        var isInlineTemplateEditMode = false;
        var isInlineTemplateCloneMode = false;
        var hasBuilderGeneratedPreview = false;

        function getUniqueTemplateCloneName(baseName) {
            const templates = Array.isArray(App.templates) ? App.templates : [];
            const existingNames = new Set(
                templates
                    .map(t => (t && t.name ? String(t.name).trim().toLowerCase() : ''))
                    .filter(Boolean)
            );

            const safeBase = (baseName || 'Untitled Template').trim();
            let candidate = `${safeBase} Copy`;
            let idx = 2;
            while (existingNames.has(candidate.trim().toLowerCase())) {
                candidate = `${safeBase} Copy ${idx++}`;
            }
            return candidate;
        }

        function isTemplateNameTaken(name, editingId = null) {
            const normalized = String(name || '').trim().toLowerCase();
            if (!normalized) return false;

            const templates = Array.isArray(App.templates) ? App.templates : [];
            return templates.some(t => {
                const sameName = String(t?.name || '').trim().toLowerCase() === normalized;
                if (!sameName) return false;
                if (editingId && String(t?.id) === String(editingId)) return false;
                return true;
            });
        }

        function focusTemplateNameInput() {
            const focusNow = () => {
                const input = document.getElementById('builder_storage_name_inline');
                if (!input) return;
                input.focus();
                const len = input.value.length;
                input.setSelectionRange(len, len);
            };

            requestAnimationFrame(focusNow);
            setTimeout(focusNow, 40);
        }

        function toggleWizardView(view) {
            const configView = document.getElementById('batchWizardConfigView');
            const builderView = document.getElementById('templateBuilderInlineView');
            const paperView = document.getElementById('quick-generated-paper-section');
            const footer = document.getElementById('quick-mode-footer');
            const header = document.getElementById('quick-mode-header');
            const sidebar = document.getElementById('wizardDiscoverySidebar');
            const mainColumn = document.getElementById('wizardMainColumn');
            const modalEl = document.getElementById('createPackModal');

            const forceDisplay = (element, value = null) => {
                if (!element) return;
                if (value === null) {
                    element.style.removeProperty('display');
                } else {
                    element.style.setProperty('display', value, 'important');
                }
            };

            const backBtn = document.getElementById('wizard_global_back_btn');
            const createBtn = document.getElementById('wizard_global_create_btn');
            const divider = document.getElementById('wizard_header_divider');

            // Hide everything by default
            if (configView) configView.classList.add('hidden');
            if (builderView) builderView.classList.add('hidden');
            if (paperView) paperView.classList.add('hidden');
            if (footer) footer.classList.add('hidden');
            forceDisplay(footer, 'none');
            forceDisplay(header, 'none');

            if (view === 'template') {
                if (modalEl) modalEl.classList.add('template-scroll-mode');
                if (builderView) builderView.classList.remove('hidden');
                if (backBtn) backBtn.classList.remove('hidden');
                if (createBtn) createBtn.classList.add('hidden');
                if (divider) divider.classList.add('hidden');

                if (mainColumn) {
                    mainColumn.classList.remove('hidden');
                    mainColumn.scrollTop = 0;
                }

                if (sidebar) sidebar.classList.remove('opacity-50', 'pointer-events-none');
                forceDisplay(header, 'flex');
            } else if (view === 'batch') {
                if (modalEl) modalEl.classList.remove('template-scroll-mode');
                if (configView) configView.classList.remove('hidden');
                if (footer) footer.classList.remove('hidden');
                if (backBtn) backBtn.classList.add('hidden');
                if (createBtn) createBtn.classList.remove('hidden');
                if (divider) divider.classList.remove('hidden');

                forceDisplay(header, 'flex');
                forceDisplay(footer, 'flex');
                if (sidebar) sidebar.classList.remove('opacity-50', 'pointer-events-none');
            } else if (view === 'paper') {
                if (modalEl) modalEl.classList.remove('template-scroll-mode');
                if (paperView) paperView.classList.remove('hidden');
                if (backBtn) backBtn.classList.remove('hidden');
                if (createBtn) createBtn.classList.add('hidden');
                if (divider) divider.classList.add('hidden');

                forceDisplay(header, 'flex');
                if (sidebar) sidebar.classList.remove('opacity-50', 'pointer-events-none');
            }
            updateQuickModeFooterVisibility();
        }

        function updateQuickModeFooterVisibility() {
            const footer = document.getElementById('quick-mode-footer');
            if (!footer) return;

            const builderView = document.getElementById('templateBuilderInlineView');
            const isBuilderVisible = builderView && !builderView.classList.contains('hidden');
            const selectedTemplateId = document.getElementById('baseTemplateSelect')?.value || '';
            const shouldHide = isBuilderVisible || !!selectedTemplateId;

            if (shouldHide) {
                footer.classList.add('hidden');
                footer.style.setProperty('display', 'none', 'important');
            } else {
                footer.classList.remove('hidden');
                footer.style.setProperty('display', 'flex', 'important');
            }
        }

        function ensureBuilderQuestionScrollWorks() {
            // This ensures the main column scrolls while the questions container remains auto-height
            const mainColumn = document.getElementById('wizardMainColumn');
            if (mainColumn) {
                mainColumn.style.overflowY = 'auto';
                mainColumn.style.height = '100%';
            }

            const container = document.getElementById('builder_questions_container_inline');
            if (container) {
                container.style.height = 'auto';
                container.style.maxHeight = 'none';
                container.style.overflow = 'visible';
                container.tabIndex = 0;
            }

            const qSection = document.getElementById('builder_questions_section_inline');
            if (qSection) {
                qSection.style.maxHeight = 'none';
                qSection.style.overflow = 'visible';
            }
        }

        function handleWizardBack() {
            const builderView = document.getElementById('templateBuilderInlineView');
            const paperView = document.getElementById('quick-generated-paper-section');
            
            if (builderView && !builderView.classList.contains('hidden')) {
                toggleWizardView('batch');
            } else if (paperView && !paperView.classList.contains('hidden')) {
                closeQuickPreview();
            }
        }

        function closeQuickSetup() {
            const modalEl = document.getElementById('createPackModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                } else {
                    // Fallback for jQuery/Bootstrap 4 if needed
                    $(modalEl).modal('hide');
                }
            }
        }

        function openTemplateBuilderInline() {
            console.log("Opening Template Builder Inline...");
            currentEditingTemplateIdInline = null; // New Template
            isInlineTemplateEditMode = false;
            isInlineTemplateCloneMode = false;
            hasBuilderGeneratedPreview = false;
            updateBuilderTemplateFooterVisibility();
            ensureBuilderQuestionScrollWorks();
            
            // 1. Reset the UI components
            const container = document.getElementById('builder_sections_container_inline');
            if (container) {
                container.innerHTML = `
                    <div class="empty-state py-24 text-center flex flex-col items-center justify-center h-full" id="builder_empty_state_inline">
                        <div class="w-24 h-24 bg-slate-50 rounded-[32px] shadow-inner flex items-center justify-center mb-6 text-slate-200">
                            <i class="bi bi-stack text-5xl"></i>
                        </div>
                        <h5 class="text-[16px] font-black text-slate-700 mb-2">Structure is Empty</h5>
                        <p class="text-[12px] text-slate-400 font-medium max-w-[220px] mx-auto">Select a section blueprint above to define your paper structure</p>
                    </div>
                `;
            }

            const header = document.getElementById('inline_builder_header');
            if (header) header.style.display = 'none';

            // 2. Reset totals
            // (Removed as per user request to hide stats cards)

            // 3. Reset inputs
            const nameInput = document.getElementById('builder_storage_name_inline');
            if (nameInput) nameInput.value = '';
            const passMarkInline = document.getElementById('builder_pass_mark_inline');
            const passMarkVisible = document.getElementById('builder_pass_mark_visible');
            if (passMarkInline) passMarkInline.value = 60;
            if (passMarkVisible) passMarkVisible.value = 60;

            // 4. Clear state
            if (typeof App !== 'undefined') {
                App.manualQuestions = [];
                App.quickModePaperSource = null;
            }

            // 5. Hide Questions Section
            const qSection = document.getElementById('builder_questions_section_inline');
            if (qSection) qSection.classList.add('hidden');

            // 6. Hide Question Bank Selector
            const qbSection = document.getElementById('quick-qb-selector-section');
            if (qbSection) qbSection.classList.add('hidden');

            // 7. Toggle View
            toggleWizardView('template');
            // Start with one editable section by default.
            addNewSectionRowInline();
            updateBuilderTemplateFooterVisibility();
            focusTemplateNameInput();
        }

        function openTemplateCloneBuilderInline(templateId) {
            const template = (App.templates || []).find(t => String(t.id) === String(templateId));
            if (!template) return;

            // Start from clean "create template" state first.
            openTemplateBuilderInline();
            isInlineTemplateCloneMode = true;
            hasBuilderGeneratedPreview = false;

            currentEditingTemplateIdInline = null;
            isInlineTemplateEditMode = false;
            updateBuilderTemplateFooterVisibility();

            // Unique default clone name.
            const cloneName = getUniqueTemplateCloneName(template.name || 'Untitled Template');
            const nameInput = document.getElementById('builder_storage_name_inline');
            if (nameInput) nameInput.value = cloneName;
            const clonePassMark = parseInt(template.pass_mark || 60, 10) || 60;
            const passMarkInline = document.getElementById('builder_pass_mark_inline');
            const passMarkVisible = document.getElementById('builder_pass_mark_visible');
            if (passMarkInline) passMarkInline.value = clonePassMark;
            if (passMarkVisible) passMarkVisible.value = clonePassMark;

            // Clone structure as editable rows.
            const container = document.getElementById('builder_sections_container_inline');
            if (container) container.innerHTML = '';
            const header = document.getElementById('inline_builder_header');
            if (header) header.style.display = 'grid';

            let structure = [];
            if (Array.isArray(template.sections)) {
                structure = template.sections;
            } else if (typeof template.sections === 'string') {
                try {
                    structure = JSON.parse(template.sections || '[]') || [];
                } catch (e) {
                    structure = [];
                }
            }

            structure.forEach(s => {
                addSelectedSectionInline(
                    s.marks_type || s.type || 'MCQ',
                    s.section_name || s.name || 'Section',
                    s.num_questions || s.count || 0,
                    s.marks_per_question || s.marks || 1
                );
            });

            // Clone should behave like Create Template: fresh QB mapping and fresh generation.
            App.manualQuestions = [];
            App.quickModePaperSource = null;
            const qbSelectInline = document.getElementById('builder_qb_select_inline');
            if (qbSelectInline) qbSelectInline.value = '';
            const quickQbSelect = document.getElementById('quick_qb_select');
            if (quickQbSelect) quickQbSelect.value = '';

            updateBuilderStatsInline();
            toggleWizardView('template');
        }

        function loadTemplateToBuilderInline(id, editable = false) {
            currentEditingTemplateIdInline = id;
            isInlineTemplateEditMode = !!editable;
            isInlineTemplateCloneMode = false;
            updateBuilderTemplateFooterVisibility();
            ensureBuilderQuestionScrollWorks();
            const template = App.templates.find(t => t.id == id);
            if (!template) return;

            // 1. Set Name
            document.getElementById('builder_storage_name_inline').value = template.name;
            const templatePassMark = parseInt(template.pass_mark || 60, 10) || 60;
            const passMarkInline = document.getElementById('builder_pass_mark_inline');
            const passMarkVisible = document.getElementById('builder_pass_mark_visible');
            if (passMarkInline) passMarkInline.value = templatePassMark;
            if (passMarkVisible) passMarkVisible.value = templatePassMark;
            
            // 2. Clear Container
            const container = document.getElementById('builder_sections_container_inline');
            container.innerHTML = '';
            document.getElementById('inline_builder_header').style.display = 'grid';

            // 3. Load Sections
            const structure = typeof template.sections === 'string' ? JSON.parse(template.sections) : template.sections;
            App.manualQuestions = hydrateTemplateQuestions(template.id, template.questions || []);
            hasBuilderGeneratedPreview = Array.isArray(App.manualQuestions) && App.manualQuestions.length > 0;
            updateBuilderTemplateFooterVisibility();

            if (structure && structure.length > 0) {
                structure.forEach(s => {
                    addSelectedSectionInline(s.marks_type || s.type, s.section_name || s.name, s.num_questions || s.count, s.marks_per_question || s.marks);
                });
            }

            // 4. Toggle view
            toggleWizardView('template');
            
            // 5. Update stats
            updateBuilderStatsInline();
            const qbSection = document.getElementById('quick-qb-selector-section');
            if (qbSection) qbSection.classList.add('hidden');
            focusTemplateNameInput();
        }

        function updateBuilderTemplateFooterVisibility() {
            const footer = document.getElementById('builder_template_footer');
            if (!footer) return;
            const hasGenerated = Array.isArray(App.manualQuestions) && App.manualQuestions.length > 0 && !!hasBuilderGeneratedPreview;
            const shouldShow = hasGenerated;
            footer.classList.toggle('hidden', !shouldShow);
        }

        function isInlineMarksEditable(type) {
            return normalizeInlineSectionType(type) === 'descriptive';
        }

        function addSelectedSectionInline(type, name = null, count = 10, marks = null) {
            if (!type) return;
            const normalizedType = normalizeInlineSectionType(type);
            const used = getUsedInlineSectionTypes();
            if (used.length >= 2 && !used.includes(normalizedType)) {
                Swal.fire('Section limit reached', 'Only two sections are allowed: one MCQ and one descriptive question.', 'info');
                return;
            }

            const container = document.getElementById('builder_sections_container_inline');
            const emptyState = document.getElementById('builder_empty_state_inline');
            const header = document.getElementById('inline_builder_header');

            if (emptyState) emptyState.style.display = 'none';
            if (header) header.style.display = 'grid';

            const row = document.createElement('div');
            row.className = 'grid grid-cols-12 gap-0 bg-white hover:bg-slate-50/50 transition-colors animate-fadeIn items-center min-h-[56px] section-builder-row-inline';
            row.dataset.type = normalizedType;
            const typeLabel = inlineSectionTypeDisplayName(normalizedType);
            const displayName = name || `${typeLabel} Section`;
            const displayMarks = getFixedMarksByType(normalizedType);
            const marksEditable = isInlineMarksEditable(normalizedType);

            row.innerHTML = `
            <div class="col-span-4 py-2.5 pl-14 pr-3 flex items-center gap-3 min-h-[56px]">
                <div class="w-6 h-6 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                    <i class="bi bi-grip-vertical"></i>
                </div>
                <div class="leading-tight">
                    <input type="hidden" class="sec-name-hidden-inline" value="${displayName}">
                    <input type="text" class="bg-transparent border-0 font-bold text-slate-800 p-0 focus:ring-0 text-[11px] w-full" value="${displayName}">
                    <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest leading-none">${typeLabel} Component</p>
                </div>
            </div>
            <div class="col-span-2 py-2 px-2 min-h-[56px] flex items-center justify-center">
                <input type="number" class="w-16 h-8 mx-auto bg-slate-50 border border-slate-200 rounded-lg px-2 font-bold text-slate-800 text-[11px] text-center focus:ring-2 focus:ring-red-100 sec-count-inline" value="${count}" oninput="updateBuilderStatsInline()">
            </div>
            <div class="col-span-2 py-2 px-2 min-h-[56px] flex items-center justify-center">
                <input type="number" class="w-16 h-8 mx-auto bg-slate-50 border border-slate-200 rounded-lg px-2 font-bold text-slate-800 text-[11px] text-center focus:ring-2 focus:ring-red-100 sec-marks-inline" value="${displayMarks}" min="1" ${marksEditable ? '' : 'readonly'}>
            </div>
            <div class="col-span-4 py-2 px-3 min-h-[56px] flex items-center justify-end">
                <button class="w-7 h-7 rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center" 
                        onclick="this.closest('.grid').remove(); updateBuilderStatsInline(); if(document.querySelectorAll('.sec-count-inline').length === 0) { document.getElementById('builder_empty_state_inline').style.display='block'; document.getElementById('inline_builder_header').style.display='none'; }">
                    <i class="bi bi-trash-fill text-[9px]"></i>
                </button>
            </div>
        `;
            container.appendChild(row);
            updateBuilderStatsInline();
        }

        function updateBuilderStatsInline() {
            const sections = document.querySelectorAll('#builder_sections_container_inline > div:not(.empty-state)');
            let totalMarks = 0;
            let totalQuestions = 0;
            let totalSections = sections.length;

            sections.forEach(s => {
                const countInput = s.querySelector('.sec-count-inline');
                const marksInput = s.querySelector('.sec-marks-inline');
                
                if (countInput && marksInput) {
                    const count = parseInt(countInput.value) || parseInt(countInput.textContent) || 0;
                    const marks = parseInt(marksInput.value) || parseInt(marksInput.textContent) || 0;
                    totalQuestions += count;
                    totalMarks += (count * marks);
                }
            });

            // Update Header Stats (Older UI)
            const oldMarks = document.getElementById('builder_total_marks_inline');
            const oldSections = document.getElementById('builder_section_count_inline');
            if (oldMarks) oldMarks.textContent = totalMarks + ' Marks';
            if (oldSections) oldSections.textContent = totalSections + ' Sections';

            // Update New High-Density Dashboard
            const qDisplay = document.getElementById('total_questions_display');
            const mDisplay = document.getElementById('total_marks_display');
            if (qDisplay) qDisplay.textContent = totalQuestions;
            if (mDisplay) mDisplay.textContent = totalMarks;

            // Question Content Management
            const qSection = document.getElementById('builder_questions_section_inline');
            if (qSection) {
                if (isInlineTemplateCloneMode || !hasBuilderGeneratedPreview) {
                    qSection.classList.add('hidden');
                } else if (totalSections > 0) {
                    qSection.classList.remove('hidden');
                    const dataSections = Array.from(sections).map(s => ({
                        name: (
                            s.querySelector('.sec-name-hidden-inline')?.value ||
                            s.querySelector('input[type="text"]')?.value ||
                            s.querySelector('.sec-display-name')?.textContent ||
                            'Section'
                        ),
                        count: parseInt(
                            s.querySelector('.sec-count-inline')?.value ||
                            s.querySelector('.sec-display-count')?.textContent ||
                            '0'
                        ) || 0,
                        marks: parseInt(
                            s.querySelector('.sec-marks-inline')?.value ||
                            s.querySelector('.sec-display-marks')?.textContent ||
                            '0'
                        ) || 0,
                        type: s.dataset.type
                    }));
                    App.renderBuilderManualSections(dataSections);
                } else {
                    qSection.classList.add('hidden');
                }
            }

            // Question Bank Selection (Quick Mode)
            const qbSection = document.getElementById('quick-qb-selector-section');
            if (qbSection) {
                const isCreateTemplateMode = !currentEditingTemplateIdInline;
                if (totalSections > 0 && isCreateTemplateMode) {
                    qbSection.classList.remove('hidden');
                    // Ensure QB select is populated
                    const qbSelect = document.getElementById('quick_qb_select');
                    if (qbSelect && qbSelect.children.length <= 1) {
                        qbSelect.innerHTML = '<option value="" disabled selected>-- Select a Question Bank --</option>' +
                            QuestionBanks.map(b => `<option value="${b.id}">${b.name}</option>`).join('');
                    }
                } else {
                    qbSection.classList.add('hidden');
                }
            }
        }

        App.renderBuilderManualSections = (sections) => {
            const container =
                document.getElementById('builder_manual_sections_container_inline') ||
                document.getElementById('builder_questions_container_inline');
            if (!container) return;

            container.innerHTML = sections.map((s, idx) => {
                const name = s.name || 'Section';
                const count = s.count || 0;
                const marks = s.marks || 0;

                return `
                <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm mb-2 animate-fadeIn">
                    <div class="p-3 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center text-[9px] font-black shadow-lg shadow-red-100">
                                ${name.substring(0, 3).toUpperCase()}
                            </div>
                            <div>
                                <h5 class="text-[13px] font-black text-slate-800 mb-0 uppercase tracking-wide leading-none">${name}</h5>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">${count} Questions</span>
                                    <div class="w-1.5 h-1.5 bg-slate-200 rounded-full"></div>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">${marks} Marks Each</span>
                                </div>
                            </div>
                        </div>
                        <button class="px-4 py-2 bg-white border border-slate-200 text-red-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all shadow-sm" 
                                onclick="App.addNewManualQuestion(${idx}, '${name}', ${marks}, ${count})">
                            <i class="bi bi-plus-lg me-1"></i> Add Question
                        </button>
                    </div>
                    <div class="p-4">
                        <div id="builder_section_questions_${idx}">
                            <div class="py-12 text-center border border-dashed border-slate-100 rounded-2xl bg-slate-50/20">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center mx-auto mb-2 text-slate-200 shadow-sm">
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Questions defined in Batch configuration</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }).join('');

            // Populate each section with already saved/generated questions.
            sections.forEach((_, idx) => App.refreshSectionQuestions(idx));
        };

        App.handleBuilderBulkUpload = (input) => {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            const reader = new FileReader();

            Swal.fire({ title: 'Processing Upload...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

            reader.onload = (e) => {
                try {
                    const text = e.target.result;
                    const lines = text.split('\n');
                    const questions = [];

                    lines.forEach((line, idx) => {
                        // Skip empty, comments, and header
                        if (!line.trim() || line.startsWith('#') || idx === 0) return;

                        // Simple CSV parse (handling quotes)
                        const parts = line.match(/(".*?"|[^",\s]+)(?=\s*,|\s*$)/g);
                        if (parts && parts.length >= 8) {
                            const clean = (s) => s ? s.replace(/^"|"$/g, '').trim() : '';
                            questions.push({
                                section_name: clean(parts[0]),
                                question: clean(parts[1]),
                                type: clean(parts[2]),
                                option_a: clean(parts[3]),
                                option_b: clean(parts[4]),
                                option_c: clean(parts[5]),
                                option_d: clean(parts[6]),
                                correct_answer: clean(parts[7]),
                                marks: parseInt(clean(parts[8])) || 1
                            });
                        }
                    });

                    if (questions.length === 0) {
                        Swal.fire('Empty File', 'No valid questions found in the CSV. Please check the template format.', 'warning');
                        return;
                    }

                    App.manualQuestions = questions;
                    Swal.fire({
                        title: 'Questions Uploaded',
                        text: `${questions.length} questions integrated into the template.`,
                        icon: 'success',
                        timer: 2000
                    });

                    if (typeof updateManualQCount === 'function') updateManualQCount();
                } catch (err) {
                    console.error("CSV Parse Error:", err);
                    Swal.fire('Upload Failed', 'Error parsing CSV file.', 'error');
                }
            };
            reader.onerror = () => Swal.fire('Error', 'Failed to read file', 'error');
            reader.readAsText(file);
        };

        async function saveTemplateFromWizard() {
            const name = document.getElementById('builder_storage_name_inline').value.trim();
            if (!name) { Swal.fire('Required', 'Please enter a Template Name', 'warning'); return; }
            const isEditMode = !!currentEditingTemplateIdInline;
            if (isTemplateNameTaken(name, currentEditingTemplateIdInline)) {
                Swal.fire('Duplicate Name', 'Template name already exists. Please use a unique name.', 'warning');
                return;
            }

            const sections = [];
            document.querySelectorAll('.section-builder-row-inline:not(.is-editing)').forEach(row => {
                const sectionType = normalizeInlineSectionType(row.dataset.type);
                sections.push({
                    name: (
                        row.querySelector('.sec-name-hidden-inline')?.value ||
                        row.querySelector('.sec-display-name')?.textContent ||
                        'Section'
                    ),
                    type: sectionType,
                    count: parseInt(
                        row.querySelector('.sec-count-inline')?.value ||
                        row.querySelector('.sec-display-count')?.textContent ||
                        '0'
                    ) || 0,
                    marks: parseInt(
                        row.querySelector('.sec-marks-inline')?.value ||
                        row.querySelector('.sec-display-marks')?.textContent ||
                        String(getFixedMarksByType(sectionType))
                    ) || getFixedMarksByType(sectionType)
                });
            });

            if (sections.length > 2) {
                Swal.fire('Section limit reached', 'Only two sections are allowed: one MCQ and one descriptive question.', 'warning');
                return;
            }

            if (sections.length === 0) { 
                Swal.fire('Required', 'Please add and SAVE at least one section to your template structure', 'warning'); 
                return; 
            }

            const quickQbId = document.getElementById('quick_qb_select')?.value || '';
            const inlineQbId = document.getElementById('builder_qb_select_inline')?.value || '';
            const sourceQbId = App.quickModePaperSource?.qbId ? String(App.quickModePaperSource.qbId) : '';
            const qbId = inlineQbId || quickQbId || sourceQbId;
            const hasGeneratedQuestions = Array.isArray(App.manualQuestions) && App.manualQuestions.length > 0;
            if (!currentEditingTemplateIdInline && !qbId && !hasGeneratedQuestions) {
                Swal.fire('Required', 'Please select a Question Bank for this template', 'warning');
                return;
            }

            Swal.fire({ title: 'Saving Template...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

            try {
                const data = {
                    id: currentEditingTemplateIdInline || null,
                    name: name,
                    category: document.getElementById('builder_category_inline').value,
                    duration: document.getElementById('builder_duration_inline').value || 60,
                    pass_mark: document.getElementById('builder_pass_mark_inline').value || 60,
                    max_attempts: document.getElementById('builder_attempts_inline').value || 2,
                    sections: sections,
                    questions: App.manualQuestions
                };

                const response = await fetch('Test/saveTemplate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.status === 'success') {
                    // Add/update template in app state and discovery list
                    const newTemplate = result.template;

                    // Add to global state
                    if (!App.templates) App.templates = [];
                    // Ensure sections and questions are attached for immediate UI update
                    newTemplate.sections = data.sections;
                    newTemplate.questions = normalizeQuestionList(data.questions || []);

                    const existingIndex = App.templates.findIndex(t => String(t.id) === String(newTemplate.id));
                    if (existingIndex !== -1) {
                        App.templates[existingIndex] = { ...App.templates[existingIndex], ...newTemplate };
                    } else {
                        App.templates.push(newTemplate);
                    }

                    // Calculate marks for sidebar label
                    let tm = 0;
                    data.sections.forEach(s => tm += (parseInt(s.count || 0) * parseInt(s.marks || 0)));
                    const cardId = `temp_card_${newTemplate.id}`;
                    const existingCard = document.getElementById(cardId);
                    if (existingCard) {
                        const titleEl = existingCard.querySelector('h5');
                        const metaEl = existingCard.querySelector('span');
                        if (titleEl) titleEl.textContent = newTemplate.name;
                        if (metaEl) metaEl.textContent = `${newTemplate.category || 'General'} • ${tm} Marks • ${data.sections.length} Sec`;
                    } else {
                        const discoveryList = document.getElementById('templateDiscoveryList');
                        if (discoveryList) {
                            const newCard = document.createElement('div');
                            newCard.className = 'p-2.5 rounded-xl border border-slate-50 bg-white hover:border-red-200 cursor-pointer transition-all group relative template-card';
                            newCard.id = cardId;
                            newCard.onclick = () => selectTemplate(newTemplate.id);
                            newCard.innerHTML = `
                            <div class="flex items-start gap-2.5">
                                <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:text-red-600 transition-colors flex-shrink-0">
                                    <i class="bi bi-file-earmark-text text-base"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <h5 class="text-[12px] font-bold text-slate-800 mb-0 leading-tight truncate">${newTemplate.name}</h5>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">${newTemplate.category || 'General'} • ${tm} Marks • ${data.sections.length} Sec</span>
                                </div>
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity check-badge">
                                     <div class="w-4 h-4 bg-red-600 rounded-full flex items-center justify-center text-white text-[8px]">
                                        <i class="bi bi-check-lg"></i>
                                     </div>
                                </div>
                            </div>
                        `;
                            discoveryList.prepend(newCard);
                        }
                    }

                    // Add to hidden select
                    const select = document.getElementById('baseTemplateSelect');
                    const option = document.createElement('option');
                    option.value = newTemplate.id;
                    option.textContent = newTemplate.name;
                    select.appendChild(option);

                    // Select it
                    selectTemplate(newTemplate.id, false, true);
                    
                    // Refresh Sidebar UI completely to ensure consistency
                    if (typeof filterSidebar === 'function') {
                        filterSidebar('all', document.querySelector('.discovery-filter-btn') || null); // Refresh the list
                    } else if (typeof loadSidebarTemplates === 'function') {
                        loadSidebarTemplates();
                    }
                    
                    Swal.fire({ 
                        icon: 'success', 
                        title: isEditMode ? 'Template Updated!' : 'Template Saved!', 
                        text: isEditMode ? 'Template changes have been saved.' : 'Template has been created and is ready to use.',
                        timer: 2000, 
                        showConfirmButton: false 
                    });

                    // Optional: Scroll to the new card
                    const newEl = document.getElementById(`temp_card_${newTemplate.id}`);
                    if (newEl) newEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    hasBuilderGeneratedPreview = false;
                    updateBuilderTemplateFooterVisibility();

                    const quickFooter = document.getElementById('quick-mode-footer');
                    if (quickFooter) quickFooter.classList.add('hidden');
                } else {
                    throw new Error(result.message);
                }
            } catch (e) {
                Swal.fire('Error', e.message || 'Failed to save template', 'error');
            }
        }

        // Handle Question Bank Selection in Builder
        App.handleQBSelectionInline = (qbId) => {
            const btnEl = document.getElementById('qb_action_buttons');
            const statusEl = document.getElementById('qb_mapping_status');
            
            if (qbId) {
                if (btnEl) btnEl.classList.remove('hidden');
                // Hide mapping status until QP Generation is clicked
                if (statusEl) statusEl.classList.add('hidden');
            } else {
                if (btnEl) btnEl.classList.add('hidden');
                if (statusEl) statusEl.classList.add('hidden');
            }
            hasBuilderGeneratedPreview = false;
            updateBuilderTemplateFooterVisibility();
        };

        function handleQuickQuestionBankChange(qbId) {
            const btnEl = document.getElementById('quick_qb_action_buttons');
            if (qbId) {
                if (btnEl) btnEl.classList.remove('hidden');
            } else {
                if (btnEl) btnEl.classList.add('hidden');
            }
            App.quickModePaperSource = null;
        }

        // --- INLINE SECTION BUILDER LOGIC ---
        function normalizeInlineSectionType(value) {
            const raw = String(value || '').trim();
            if (!raw) return 'MCQ';

            const nt = typeof App !== 'undefined' && App.normalizeType ? App.normalizeType(raw) : '';
            if (nt === 'descriptive') return 'descriptive';
            if (nt === 'mcq') return 'MCQ';

            const v = raw.toLowerCase();
            if (v.includes('coding') || v.includes('practical')) return raw;

            return 'MCQ';
        }

        /** Sidebar/template builder keys: MCQ vs descriptive (displayed as descriptive question). */
        function inlineSectionTypeDisplayName(internalType) {
            const t = normalizeInlineSectionType(internalType);
            if (t === 'descriptive') return 'Descriptive question';
            if (t === 'MCQ') return 'MCQ';
            return String(internalType || '').trim() || 'MCQ';
        }

        function getUsedInlineSectionTypes(excludeRowId = null) {
            const rows = Array.from(document.querySelectorAll('#builder_sections_container_inline .section-builder-row-inline'));
            const types = [];

            rows.forEach(row => {
                if (excludeRowId && row.id === excludeRowId) return;
                const select = row.querySelector('.sec-type-select-inline');
                const type = normalizeInlineSectionType(select?.value || row.dataset.type);
                if (type) types.push(type);
            });

            return [...new Set(types)];
        }

        function getAllowedTypesForNewInlineRow() {
            const used = getUsedInlineSectionTypes();
            if (used.length >= 2) return [];
            if (used.includes('MCQ')) return ['descriptive'];
            if (used.includes('descriptive')) return ['MCQ'];
            return ['MCQ', 'descriptive'];
        }

        function buildInlineTypeOptions(allowedTypes, selectedType) {
            return allowedTypes.map(type => {
                const label = `${inlineSectionTypeDisplayName(type)} Section`;
                return `<option value="${type}" ${type === selectedType ? 'selected' : ''}>${label}</option>`;
            }).join('');
        }

        function getFixedMarksByType(type) {
            return normalizeInlineSectionType(type) === 'descriptive' ? 2 : 1;
        }

        function handleInlineSectionTypeChange(selectEl) {
            const row = selectEl.closest('.section-builder-row-inline');
            if (!row) return;

            const nextType = normalizeInlineSectionType(selectEl.value);
            const usedOtherTypes = getUsedInlineSectionTypes(row.id);
            if (usedOtherTypes.includes(nextType)) {
                Swal.fire('Type already used', 'You can only have one MCQ section and one descriptive question section.', 'info');
                const fallback = usedOtherTypes.includes('MCQ') ? 'descriptive' : 'MCQ';
                selectEl.value = fallback;
            }

            const finalType = normalizeInlineSectionType(selectEl.value);
            row.dataset.type = finalType;

            const nameHidden = row.querySelector('.sec-name-hidden-inline');
            if (nameHidden) nameHidden.value = `${inlineSectionTypeDisplayName(finalType)} Section`;

            const marksInput = row.querySelector('.sec-marks-inline');
            if (marksInput) {
                if (!isInlineMarksEditable(finalType)) {
                    marksInput.value = getFixedMarksByType(finalType);
                } else if (!marksInput.value || parseInt(marksInput.value, 10) <= 0) {
                    marksInput.value = getFixedMarksByType(finalType);
                }
                marksInput.readOnly = !isInlineMarksEditable(finalType);
            }
            hasBuilderGeneratedPreview = false;
            updateBuilderTemplateFooterVisibility();
        }

        function addNewSectionRowInline() {
            const container = document.getElementById('builder_sections_container_inline');
            const emptyState = document.getElementById('builder_empty_state_inline');
            const allowedTypes = getAllowedTypesForNewInlineRow();
            if (allowedTypes.length === 0) {
                Swal.fire('Section limit reached', 'Only two sections are allowed: one MCQ and one descriptive question.', 'info');
                return;
            }

            const defaultType = allowedTypes[0];
            const defaultMarks = getFixedMarksByType(defaultType);
            const marksEditable = isInlineMarksEditable(defaultType);

            if (emptyState) emptyState.classList.add('hidden');
            document.getElementById('inline_builder_header').style.display = 'grid';

            const rowId = 'row_' + Date.now();
            const row = document.createElement('div');
            row.className = 'grid grid-cols-12 gap-0 items-center py-2.5 group section-builder-row-inline is-editing';
            row.id = rowId;
            row.dataset.type = defaultType;
            
            row.innerHTML = `
                <div class="col-span-4 pl-4 sm:pl-14 pr-4">
                    <div class="edit-mode">
                        <select class="w-full bg-slate-50 border border-slate-100 rounded-lg h-9 px-3 text-[12px] font-bold text-slate-700 focus:ring-2 focus:ring-red-100 focus:border-red-400 sec-type-select-inline" onchange="handleInlineSectionTypeChange(this)">
                            ${buildInlineTypeOptions(allowedTypes, defaultType)}
                        </select>
                        <input type="hidden" class="sec-name-hidden-inline" value="${inlineSectionTypeDisplayName(defaultType)} Section">
                    </div>
                    <div class="view-mode hidden">
                        <h5 class="text-[12px] font-black text-slate-800 mb-0 sec-display-name">${inlineSectionTypeDisplayName(defaultType)} Section</h5>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5 sec-display-type">${inlineSectionTypeDisplayName(defaultType)} Component</p>
                    </div>
                </div>
                <div class="col-span-2 px-2 flex justify-center">
                    <div class="edit-mode w-16">
                        <input type="number" class="w-full bg-slate-50 border border-slate-100 rounded-lg h-9 px-2 text-[12px] font-bold text-slate-700 text-center sec-count-inline" value="10" min="1">
                    </div>
                    <div class="view-mode hidden text-[12px] font-black text-slate-700 sec-display-count">10</div>
                </div>
                <div class="col-span-2 px-2 flex justify-center">
                    <div class="edit-mode w-16">
                        <input type="number" class="w-full bg-slate-50 border border-slate-100 rounded-lg h-9 px-2 text-[12px] font-bold text-slate-700 text-center sec-marks-inline" value="${defaultMarks}" min="1" ${marksEditable ? '' : 'readonly'}>
                    </div>
                    <div class="view-mode hidden text-[12px] font-black text-slate-700 sec-display-marks">${defaultMarks}</div>
                </div>
                <div class="col-span-4 px-4 flex items-center justify-end gap-3">
                    <div class="edit-mode flex gap-2">
                        <button onclick="saveInlineSectionRow('${rowId}')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-emerald-700 transition-all">Save</button>
                        <button onclick="cancelInlineSectionRow('${rowId}')" class="px-3 py-1.5 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
                    </div>
                    <div class="view-mode hidden flex gap-3">
                        <button onclick="editInlineSectionRow('${rowId}')" class="text-blue-600 hover:text-blue-700 transition-all"><i class="bi bi-pencil-square text-base"></i></button>
                        <button onclick="deleteInlineSectionRow('${rowId}')" class="text-red-600 hover:text-red-700 transition-all"><i class="bi bi-trash text-base"></i></button>
                    </div>
                </div>
            `;
            container.appendChild(row);
            hasBuilderGeneratedPreview = false;
            updateBuilderTemplateFooterVisibility();
        }

        function saveInlineSectionRow(rowId) {
            const row = document.getElementById(rowId);
            const typeSelect = row.querySelector('.sec-type-select-inline');
            const countInput = row.querySelector('.sec-count-inline');
            const marksInput = row.querySelector('.sec-marks-inline');
            const nameHidden = row.querySelector('.sec-name-hidden-inline');

            const type = normalizeInlineSectionType(typeSelect.value);
            const usedOtherTypes = getUsedInlineSectionTypes(rowId);
            if (usedOtherTypes.includes(type)) {
                Swal.fire('Type already used', 'Only one MCQ section and one descriptive question section are allowed.', 'warning');
                return;
            }

            const count = countInput.value;
            const marks = Math.max(1, parseInt(marksInput.value || String(getFixedMarksByType(type)), 10) || getFixedMarksByType(type));
            const name = inlineSectionTypeDisplayName(type) + " Section";

            // Update hidden name
            nameHidden.value = name;
            row.dataset.type = type;
            marksInput.value = marks;
            marksInput.readOnly = !isInlineMarksEditable(type);

            // Update display labels
            row.querySelector('.sec-display-name').textContent = name;
            row.querySelector('.sec-display-type').textContent = inlineSectionTypeDisplayName(type) + " Component";
            row.querySelector('.sec-display-count').textContent = count;
            row.querySelector('.sec-display-marks').textContent = marks;

            // Toggle modes
            row.classList.remove('is-editing');
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('hidden'));
            row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('hidden'));

            // Update stats
            updateBuilderStatsInline();
            hasBuilderGeneratedPreview = false;
            updateBuilderTemplateFooterVisibility();
        }

        function cancelInlineSectionRow(rowId) {
            const row = document.getElementById(rowId);
            // If it's a new row (no name yet), remove it
            const __dn = row.querySelector('.sec-display-name').textContent.trim();
            if (!row.classList.contains('was-saved') && (__dn === 'MCQ Section' || __dn === 'Descriptive question Section' || __dn === '')) {
                row.remove();
                // Check if empty
                if (document.querySelectorAll('.section-builder-row-inline').length === 0) {
                    document.getElementById('builder_empty_state_inline').classList.remove('hidden');
                    document.getElementById('inline_builder_header').style.display = 'none';
                }
            } else {
                // Revert to view mode
                row.classList.remove('is-editing');
                row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('hidden'));
                row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('hidden'));
            }
        }

        function editInlineSectionRow(rowId) {
            const row = document.getElementById(rowId);
            let currentType = 'MCQ';
            const typeSelect = row.querySelector('.sec-type-select-inline');
            if (typeSelect) {
                currentType = normalizeInlineSectionType(typeSelect.value || row.dataset.type);
                const usedOtherTypes = getUsedInlineSectionTypes(rowId);
                const allowed = usedOtherTypes.length > 0 ? [usedOtherTypes.includes('MCQ') ? 'descriptive' : 'MCQ'] : ['MCQ', 'descriptive'];
                const safeAllowed = allowed.includes(currentType) ? allowed : [...new Set([currentType, ...allowed])];
                typeSelect.innerHTML = buildInlineTypeOptions(safeAllowed, currentType);
                typeSelect.value = currentType;
            }

            row.classList.add('is-editing', 'was-saved');
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
            row.querySelectorAll('.view-mode').forEach(el => el.classList.add('hidden'));

            const marksInput = row.querySelector('.sec-marks-inline');
            if (marksInput) {
                marksInput.readOnly = !isInlineMarksEditable(currentType);
            }
        }

        function deleteInlineSectionRow(rowId) {
            document.getElementById(rowId).remove();
            if (document.querySelectorAll('.section-builder-row-inline').length === 0) {
                document.getElementById('builder_empty_state_inline').classList.remove('hidden');
                document.getElementById('inline_builder_header').style.display = 'none';
            }
            updateBuilderStatsInline();
            hasBuilderGeneratedPreview = false;
            updateBuilderTemplateFooterVisibility();
        }

        App.downloadBuilderTemplate = () => {
            let csv = "Section,Question,Option A,Option B,Option C,Option D,Correct Answer (A/B/C/D),Marks\n";
            const sections = document.querySelectorAll('#builder_sections_container_inline > div:not(.empty-state)');
            sections.forEach(s => {
                const name = s.querySelector('input[type="text"]').value;
                const marks = s.querySelector('.sec-marks-inline').value;
                csv += `"${name.replace(/"/g, '""')}",Example Question,Opt 1,Opt 2,Opt 3,Opt 4,A,${marks}\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.setAttribute('hidden', '');
            a.setAttribute('href', url);
            a.setAttribute('download', 'template_structure.csv');
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        };

        function switchQuestionMode(mode, btn) {
            // Toggle Buttons
            const parent = btn.parentElement;
            parent.querySelectorAll('button').forEach(b => {
                b.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                b.classList.add('text-slate-400');
            });
            btn.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
            btn.classList.remove('text-slate-400');

            // Toggle Views
            document.getElementById('question_mode_bulk').classList.add('hidden');
            document.getElementById('question_mode_manual').classList.add('hidden');
            document.getElementById(`question_mode_${mode}`).classList.remove('hidden');
        }

        function formatTemplateDate(raw) {
            if (!raw) return '--';
            const d = new Date(String(raw).replace(' ', 'T'));
            if (isNaN(d.getTime())) return String(raw);
            return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        function renderActiveTemplateMeta(template) {
            const createdEl = document.getElementById('active_template_created_date');
            const summaryEl = document.getElementById('active_template_usage_summary');
            const listEl = document.getElementById('active_template_usage_list');
            const btn = document.getElementById('active_template_usage_btn');
            const dropdown = document.getElementById('active_template_usage_dropdown');

            if (createdEl) createdEl.textContent = formatTemplateDate(template.created_at);

            const usage = Array.isArray(template.usage) ? template.usage : [];
            if (summaryEl) {
                if (usage.length === 0) {
                    summaryEl.textContent = 'Not used yet';
                } else if (usage.length === 1) {
                    summaryEl.textContent = usage[0].pack_name || usage[0].assessment_name || '1 batch';
                } else {
                    summaryEl.textContent = usage.length + ' batches';
                }
            }

            if (listEl) {
                if (usage.length === 0) {
                    listEl.innerHTML = '<div class="text-[10px] text-slate-400 font-medium px-2 py-3 text-center">This template has not been used in any batch yet.</div>';
                } else {
                    listEl.innerHTML = usage.map(u => `
                        <div class="flex items-start gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-50 transition-colors">
                            <div class="w-5 h-5 bg-red-50 text-red-600 rounded-md flex items-center justify-center text-[9px] flex-shrink-0">
                                <i class="bi bi-collection"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-[11px] font-bold text-slate-700 truncate">${escapeHtml(u.pack_name || 'Unnamed Batch')}</div>
                                <div class="text-[9px] text-slate-400 font-medium truncate">${escapeHtml(u.assessment_name || '')}</div>
                            </div>
                        </div>
                    `).join('');
                }
            }

            if (btn && dropdown && !btn.dataset.bound) {
                btn.dataset.bound = '1';
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });
                document.addEventListener('click', (e) => {
                    if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }
            if (dropdown) dropdown.classList.add('hidden');
        }

        function updateTemplateDetails(templateId, clearQuestions = false) {
            const template = App.templates.find(t => t.id == templateId);
            const placeholder = document.getElementById('template_details_placeholder');
            const activeView = document.getElementById('template_details_active');
            const deleteBtn = document.getElementById('active_template_delete_btn');
            const questionPreview = document.getElementById('active_template_questions');
            const metaBox = document.getElementById('active_template_meta');

            if (!template) {
                placeholder.classList.remove('hidden');
                activeView.classList.add('hidden');
                if (deleteBtn) deleteBtn.classList.add('hidden');
                if (questionPreview) questionPreview.innerHTML = '';
                if (metaBox) {
                    metaBox.classList.add('hidden');
                    metaBox.classList.remove('flex');
                }
                updateQuickModeFooterVisibility();
                return;
            }

            placeholder.classList.add('hidden');
            activeView.classList.remove('hidden');
            if (deleteBtn) deleteBtn.classList.remove('hidden');
            if (metaBox) {
                metaBox.classList.remove('hidden');
                metaBox.classList.add('flex');
            }
            renderActiveTemplateMeta(template);
            updateQuickModeFooterVisibility();

            document.getElementById('active_template_name').value = template.name;

            const sections = getTemplateSections(template);

            let totalMarks = 0;
            sections.forEach(s => {
                const count = parseInt(s.num_questions || s.count || 0);
                const marks = parseInt(s.marks_per_question || s.marks || 0);
                totalMarks += (count * marks);
            });

            const marksInput = document.getElementById('active_template_marks_input');
            const sectionsInput = document.getElementById('active_template_sections_input');

            if (marksInput) marksInput.value = totalMarks + ' Marks';
            if (sectionsInput) sectionsInput.value = sections.length + ' Sections';

            // Reset or Load manual questions
            if (clearQuestions) {
                App.manualQuestions = [];
                App.quickModePaperSource = null;
                console.log("Template cloned: Questions bank cleared.");
            } else {
                App.manualQuestions = hydrateTemplateQuestions(template.id, template.questions || []);
                App.quickModePaperSource = null;
            }

            // Update Question Entry Area
            if (App.renderManualSections) {
                App.renderManualSections(sections);
                // Refresh each section list to show questions
                if (sections.length > 0) {
                    sections.forEach((_, idx) => {
                        if (typeof App.refreshSectionQuestions === 'function') {
                            App.refreshSectionQuestions(idx);
                        }
                    });
                }
            }

            const tagContainer = document.getElementById('active_template_tags');
            if (tagContainer) {
                tagContainer.innerHTML = '';
                sections.forEach((s, idx) => {
                    const sectionRow = document.createElement('div');
                    sectionRow.className = 'flex items-center justify-between p-3 bg-white border border-slate-100 rounded-xl mb-2 hover:border-red-100 transition-all group';
                    sectionRow.innerHTML = `
                    <div class="flex-1 flex items-center gap-4">
                        <div class="w-8 h-8 bg-slate-50 text-slate-800 rounded-lg flex items-center justify-center font-black text-[11px] border border-slate-100 shadow-sm">
                            ${idx + 1}
                        </div>
                        <div class="grid grid-cols-12 gap-4 flex-1 items-center">
                            <div class="col-span-5">
                                <span class="block text-[12px] font-black text-slate-800 leading-none mb-1">${s.section_name || s.name || inlineSectionTypeDisplayName(s.marks_type || s.type || 'MCQ')}</span>
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest leading-none">${inlineSectionTypeDisplayName(s.marks_type || s.type || 'MCQ')} Component</span>
                            </div>
                            <div class="col-span-3 flex items-center gap-2">
                                <input type="number" value="${s.num_questions || s.count || 0}" 
                                       class="w-12 h-7 bg-slate-50 border-0 rounded-lg text-center text-[11px] font-black focus:ring-2 focus:ring-red-100 transition-all pointer-events-none opacity-80" 
                                       readonly>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Qs</span>
                            </div>
                            <div class="col-span-3 flex items-center gap-2">
                                <input type="number" value="${s.marks_per_question || s.marks || 0}" 
                                       class="w-12 h-7 bg-slate-50 border-0 rounded-lg text-center text-[11px] font-black focus:ring-2 focus:ring-red-100 transition-all pointer-events-none opacity-80" 
                                       readonly>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Marks</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all">
                        <button class="w-8 h-8 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" onclick="editTemplate(${template.id})">
                            <i class="bi bi-pencil-fill text-[10px]"></i>
                        </button>
                        <button class="w-8 h-8 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm" onclick="deleteTemplate(event, ${template.id})">
                            <i class="bi bi-trash-fill text-[10px]"></i>
                        </button>
                    </div>
                `;
                    tagContainer.appendChild(sectionRow);
                });
            }

            // Update Manual Entry View
            App.renderManualSections(sections);

            // Quick Mode: Show QB selector
            const modalEl = document.getElementById('createPackModal');
            if (modalEl && modalEl.classList.contains('quick-mode')) {
                const qbSection = document.getElementById('quick-qb-selector-section');
                if (qbSection) {
                    qbSection.classList.add('hidden');
                }
            }

            renderActiveTemplateQuestions(template, sections, App.manualQuestions);
        }

        function openManualQuestionModal() {
            $('#manualQuestionModal').modal('show');
        }

        function addManualQuestion() {
            const type = document.getElementById('manual_q_type').value;
            const text = document.getElementById('manual_q_text').value.trim();
            const marks = document.getElementById('manual_q_marks').value;

            if (!text) { Swal.fire('Required', 'Please enter question text', 'warning'); return; }

            const list = document.getElementById('manual_questions_list');
            const empty = document.getElementById('manual_q_empty');
            if (empty) empty.style.display = 'none';

            const qId = Date.now();
            const card = document.createElement('div');
            card.className = 'p-4 bg-slate-50 border border-slate-100 rounded-xl flex items-start justify-between animate-fadeIn group';
            card.innerHTML = `
            <div class="flex gap-3">
                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-indigo-600 font-bold text-xs shadow-sm">${list.children.length + 1}</div>
                <div>
                    <p class="text-[12px] font-bold text-slate-700 mb-1">${text}</p>
                    <div class="flex gap-2">
                        <span class="text-[8px] font-black uppercase tracking-widest bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded">${type}</span>
                        <span class="text-[8px] font-black uppercase tracking-widest bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">${marks} Marks</span>
                    </div>
                </div>
            </div>
            <button class="w-7 h-7 rounded-lg bg-white text-slate-300 hover:text-red-500 transition-all shadow-sm opacity-0 group-hover:opacity-100" onclick="this.closest('.p-4').remove(); updateManualQCount();">
                <i class="bi bi-trash"></i>
            </button>
        `;
            list.appendChild(card);
            updateManualQCount();
            $('#manualQuestionModal').modal('hide');

            // Reset form
            document.getElementById('manual_q_text').value = '';
        }

        function updateManualQCount() {
            const count = document.querySelectorAll('#manual_questions_list > div:not(#manual_q_empty)').length;
            document.getElementById('manual_q_count').textContent = count + ' Questions';
            if (count === 0 && document.getElementById('manual_q_empty')) {
                document.getElementById('manual_q_empty').style.display = 'block';
            }
        }

        function validatePackWizard() {
            let isValid = true;
            const name = document.getElementById('pack_wizard_name');
            const template = document.getElementById('baseTemplateSelect');
            const duration = document.getElementById('pack_duration');
            const modalEl = document.getElementById('createPackModal');
            const isQuickMode = modalEl && modalEl.classList.contains('quick-mode');
            const qbSelect = document.getElementById('quick_qb_select');

            // Check candidates for the CURRENT active test
            const testId = currentTestIdForPack;
            const candidates = App.selectedCandidates[testId] || [];

            // Visible fields for Quick Mode feedback
            const qName = document.getElementById('quick_batch_name');
            const qDuration = document.getElementById('quick_batch_duration');

            console.group("Batch Validation Report");
            console.log("Target Test ID:", testId);
            console.log("Name Field:", name ? `'${name.value}'` : "MISSING");
            console.log("Template Field:", template ? `'${template.value}'` : "MISSING");
            console.log("Duration Field:", duration ? `'${duration.value}'` : "MISSING");
            console.log("Candidates Selected:", candidates.length, candidates);
            console.groupEnd();

            // 1. Name Validation
            if (!name || !name.value.trim()) {
                showError('pack_wizard_name', true);
                if (qName) qName.classList.add('is-invalid', 'border-red-500');
                isValid = false;
                console.warn("❌ Validation Fail: Batch Name is empty");
            } else {
                showError('pack_wizard_name', false);
                if (qName) qName.classList.remove('is-invalid', 'border-red-500');
            }

            // 2. Template Validation
            if (!template || !template.value || template.value === "") {
                showError('baseTemplateSelect', true);
                isValid = false;
                console.warn("❌ Validation Fail: No template selected");
            } else {
                showError('baseTemplateSelect', false);
            }

            // 3. Duration Validation
            if (!duration || !duration.value || parseInt(duration.value) <= 0) {
                showError('pack_duration', true);
                if (qDuration) qDuration.classList.add('is-invalid', 'border-red-500');
                isValid = false;
                console.warn("❌ Validation Fail: Invalid duration");
            } else {
                showError('pack_duration', false);
                if (qDuration) qDuration.classList.remove('is-invalid', 'border-red-500');
            }

            // 4. Question Bank Validation - Required in Quick Mode
            if (isQuickMode && (!qbSelect || !qbSelect.value)) {
                if (qbSelect) qbSelect.classList.add('border-red-500', 'ring-2', 'ring-red-100');
                isValid = false;
                console.warn("Validation Fail: No Question Bank selected in Quick Mode");
            } else if (qbSelect) {
                qbSelect.classList.remove('border-red-500', 'ring-2', 'ring-red-100');
            }

            // 5. Candidate Validation - REMOVED (Optional)
            /*
            if (candidates.length === 0) {
                showError('wizardCandidateCountLabel', true);
                const candSummary = document.getElementById('wizard_candidate_summary');
                if(candSummary) candSummary.classList.add('border-red-500', 'bg-red-50');
                isValid = false;
                console.warn("❌ Validation Fail: No candidates selected for test ID:", testId);
            } else {
                showError('wizardCandidateCountLabel', false);
                const candSummary = document.getElementById('wizard_candidate_summary');
                if(candSummary) candSummary.classList.remove('border-red-500', 'bg-red-50');
            }
            */

            return isValid;
        }

        async function saveInlinePack(TestId) {
            const packName = document.getElementById(`inline_pack_name_${TestId}`).value;
            const templateId = document.getElementById(`inline_template_id_${TestId}`).value;
            const candidates = App.selectedCandidates[TestId] || [];

            if (!packName || !templateId) {
                Swal.fire('Incomplete Data', 'Please provide a pack name and choose a template.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Creating Batch...',
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const formData = new FormData();
                formData.append('assessment_id', TestId);
                formData.append('pack_name', packName);
                formData.append('template_id', templateId);
                formData.append('user_role', 'Assigned Roles'); // or pass actual roles
                // We might need to save candidate assignments separately or pass them here

                const response = await fetch('Test/createTestPack', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Batch has been created.',
                        icon: 'success',
                        timer: 1500
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', result.message || 'Failed to create Batch', 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'An unexpected error occurred.', 'error');
            }
        }

        Object.assign(App, {
            mockQuestions: [
                { id: 1, text: 'What is the correct way to check if a variable is an array in JS?', type: 'MCQ', options: ['typeof arr === "array"', 'arr instanceof Array', 'Array.isArray(arr)', 'Object.is(arr, Array)'], category: 'JavaScript', marks: 1 },
                { id: 2, text: 'Which keyword is used to create a constant variable?', type: 'MCQ', options: ['var', 'let', 'const', 'final'], category: 'JavaScript', marks: 1 },
                { id: 3, text: 'What is the output of 2 + "2"?', type: 'MCQ', options: ['4', '"22"', 'NaN', 'Error'], category: 'JavaScript', marks: 1 },
                { id: 4, text: 'Select all features introduced in ES6.', type: 'Multi-select', options: ['Arrow functions', 'Classes', 'Promises', 'XMLHttpRequest'], category: 'Modern JS', marks: 3 },
                { id: 5, text: 'JavaScript is a statically typed language.', type: 'True/False', options: ['True', 'False'], category: 'Fundamentals', marks: 1 },
                { id: 6, text: 'What is the time complexity of binary search?', type: 'MCQ', options: ['O(n)', 'O(log n)', 'O(n^2)', 'O(1)'], category: 'Data Structures', marks: 2 },
                { id: 7, text: 'Which testing types are included in the software testing pyramid?', type: 'Multi-select', options: ['Unit Tests', 'Integration Tests', 'Performance Tests', 'End-to-End Tests'], category: 'Testing', marks: 3 },
                { id: 8, text: 'Redux is primarily used for component styling.', type: 'True/False', options: ['True', 'False'], category: 'State Management', marks: 1 },
                { id: 9, text: 'Agile methodology uses fixed, long-term plans that don\'t change.', type: 'True/False', options: ['True', 'False'], category: 'Project Management', marks: 1 },
                { id: 10, text: 'Describe the SOLID principles in object-oriented design.', type: 'Short Answer', options: [], category: 'Software Design', marks: 2 },
                { id: 11, text: 'Explain the concept of closures in JavaScript.', type: 'Short Answer', options: [], category: 'JavaScript', marks: 2 },
                { id: 12, text: 'What is the purpose of the "use strict" directive?', type: 'MCQ', options: ['Enable new features', 'Enforce stricter parsing', 'Optimize performance', 'Prevent hoisting'], category: 'JavaScript', marks: 1 },
                { id: 13, text: 'What does "this" refer to in a global context?', type: 'MCQ', options: ['The function', 'The object', 'The window/global', 'undefined'], category: 'JavaScript', marks: 1 },
                { id: 14, text: 'Which method is used to remove the last element from an array?', type: 'MCQ', options: ['shift()', 'pop()', 'splice()', 'remove()'], category: 'JavaScript', marks: 1 },
                { id: 15, text: 'React uses a Virtual DOM to improve performance.', type: 'True/False', options: ['True', 'False'], category: 'React', marks: 1 }
            ],

            executionState: {
                active: false,
                questions: [],
                currentIndex: 0,
                answers: {},
                flagged: new Set(),
                timeLeft: 3600, // 60 minutes
                timerInterval: null,
                violations: 0,
                packId: null,
                passMark: 0,
                durationMins: 60,
                startedAt: null,
                testId: null,
                attachment: null,
                attachments: []
            },

            evaluationState: {
                submissions: {}
            },

            saveEvaluationState: () => {
                try {
                    localStorage.setItem('evaluationSubmissions', JSON.stringify(App.evaluationState.submissions || {}));
                } catch (e) {
                    console.warn('Failed to persist evaluation state', e);
                }
            },

            loadEvaluationState: () => {
                try {
                    const raw = localStorage.getItem('evaluationSubmissions');
                    if (!raw) return;
                    const parsed = JSON.parse(raw);
                    if (parsed && typeof parsed === 'object') {
                        App.evaluationState.submissions = parsed;
                    }
                } catch (e) {
                    console.warn('Failed to load evaluation state', e);
                }
            },

            _parseIntroUrls: (row) => {
                if (!row || row.intro_videos == null) return [];
                let raw = row.intro_videos;
                if (Array.isArray(raw)) return raw.filter(Boolean);
                if (typeof raw === 'string') {
                    try {
                        const p = JSON.parse(raw);
                        return Array.isArray(p) ? p.filter(Boolean) : [];
                    } catch (e) {
                        // Backward compatibility: older rows may store one plain URL
                        // or a comma/newline separated list rather than JSON.
                        return raw
                            .split(/[\n,]/)
                            .map(x => String(x || '').trim())
                            .filter(Boolean);
                    }
                }
                return [];
            },

            _resolveIntroVideoSrc: (url) => {
                if (!url) return '';
                const s = String(url).trim();
                const appBaseRaw = (typeof window.__APP_BASE__ === 'string' ? window.__APP_BASE__ : '').trim();
                const appBase = appBaseRaw.replace(/\/index\.php\/?$/i, '');

                let path = s;
                if (/^https?:\/\//i.test(s)) {
                    try {
                        path = new URL(s).pathname || s;
                    } catch (_) {
                        path = s;
                    }
                }

                // Remove accidental front-controller segment.
                path = path.replace(/\/index\.php(?=\/)/i, '');

                // Keep only the uploads/assessment_intro tail if present.
                const good = path.match(/\/uploads\/assessment_intro\/[^?#]+/i);
                if (good) {
                    const clean = good[0];
                    return appBase ? appBase + clean : `${window.location.origin}${clean}`;
                }

                // Legacy rows may store /assessment_intro/... without uploads segment.
                const legacy = path.match(/\/assessment_intro\/[^?#]+/i);
                if (legacy) {
                    const clean = '/uploads' + legacy[0];
                    return appBase ? appBase + clean : `${window.location.origin}${clean}`;
                }

                // Relative fallbacks.
                if (s.startsWith('uploads/assessment_intro/')) {
                    return (appBase || window.location.origin) + '/' + s;
                }
                if (s.startsWith('assessment_intro/')) {
                    return (appBase || window.location.origin) + '/uploads/' + s;
                }

                if (/^https?:\/\//i.test(s)) return s;
                if (!appBase) return s;
                return appBase + (s.startsWith('/') ? s : '/' + s);
            },
            _resolveIntroVideoCandidates: (url) => {
                const s = String(url || '').trim();
                if (!s) return [];
                const appBaseRaw = (typeof window.__APP_BASE__ === 'string' ? window.__APP_BASE__ : '').trim();
                const appBase = appBaseRaw.replace(/\/index\.php\/?$/i, '');
                const origin = window.location.origin;
                const out = [];
                const seen = new Set();
                const add = (u) => {
                    const n = String(u || '').trim();
                    if (!n || seen.has(n)) return;
                    seen.add(n);
                    out.push(n);
                };

                // Primary normalized URL.
                add(App._resolveIntroVideoSrc(s));

                // Extract a robust upload path candidate and fan out absolute variants.
                let path = s.replace(/\/index\.php(?=\/)/i, '');
                const uploadPath = path.match(/\/uploads\/assessment_intro\/[^?#]+/i)
                    || path.match(/\/assessment_intro\/[^?#]+/i);
                if (uploadPath) {
                    let p = uploadPath[0];
                    if (!/^\/uploads\//i.test(p)) p = '/uploads' + p;
                    add(p);
                    add(origin + p);
                    if (appBase) add(appBase + p);
                }

                // Relative fallbacks.
                if (s.startsWith('uploads/assessment_intro/')) {
                    add('/' + s);
                    add(origin + '/' + s);
                    if (appBase) add(appBase + '/' + s);
                } else if (s.startsWith('assessment_intro/')) {
                    const p = '/uploads/' + s;
                    add(p);
                    add(origin + p);
                    if (appBase) add(appBase + p);
                }

                return out;
            },

            resolveIntroConfig: async (testId, testMeta) => {
                const parseAddVideo = (val) => (val === true || val === 1 || val === '1' || String(val).toLowerCase() === 'yes');

                let meta = testMeta || null;
                let urls = App._parseIntroUrls(meta);
                let addVideoOn = parseAddVideo(meta?.add_video);

                // Fallback to in-memory test map if payload is stale/incomplete.
                if ((!addVideoOn || urls.length === 0) && Array.isArray(App.Tests)) {
                    const local = App.Tests.find(t => String(t.id) === String(testId));
                    if (local) {
                        meta = local;
                        urls = App._parseIntroUrls(local);
                        addVideoOn = parseAddVideo(local.add_video);
                    }
                }

                // Final fallback: fetch latest tests snapshot from server.
                if ((!addVideoOn || urls.length === 0) && testId) {
                    try {
                        const r = await fetch('Test/getTests');
                        const j = await r.json();
                        const tests = Array.isArray(j?.tests) ? j.tests : [];
                        const srv = tests.find(t => String(t.id) === String(testId));
                        if (srv) {
                            meta = srv;
                            urls = App._parseIntroUrls(srv);
                            addVideoOn = parseAddVideo(srv.add_video);
                        }
                    } catch (e) {
                        console.warn('Failed to refresh intro config from server', e);
                    }
                }

                return {
                    addVideoOn,
                    introUrls: Array.isArray(urls) ? urls : []
                };
            },

            setupIntroGate: (urls) => {
                const overlay = document.getElementById('execIntroOverlay');
                const mount = document.getElementById('execIntroVideosMount');
                const btn = document.getElementById('execIntroCompleteBtn');
                if (!overlay || !mount || !btn) {
                    document.getElementById('executionView').classList.remove('d-none');
                    document.body.style.overflow = 'hidden';
                    App.startTimer();
                    App.renderExecutionQuestion();
                    App.renderNavigator();
                    App.updateProgress();
                    return;
                }
                mount.innerHTML = '';
                App.introGateTotal = urls.length;
                App.introGateEnded = new Set();
                btn.disabled = true;
                btn.title = 'Watch all orientation videos to continue';
                urls.forEach((url, idx) => {
                    const box = document.createElement('div');
                    box.className = 'bg-white rounded-3 p-3 shadow-sm';
                    const v = document.createElement('video');
                    v.className = 'w-100 rounded-3';
                    v.controls = true;
                    v.setAttribute('playsinline', '');
                    v.preload = 'metadata';
                    const candidates = App._resolveIntroVideoCandidates(url);
                    let candidateIdx = 0;
                    const tryLoadNext = () => {
                        if (candidateIdx >= candidates.length) {
                            console.warn('Intro video failed to load for all URL candidates:', url, candidates);
                            return;
                        }
                        const src = candidates[candidateIdx++];
                        v.src = src;
                        v.load();
                    };
                    // Show video only (no poster/image fallback).
                    v.removeAttribute('poster');
                    v.addEventListener('ended', () => App.markIntroVideoEnded(idx));
                    v.addEventListener('error', () => {
                        tryLoadNext();
                    });
                    tryLoadNext();
                    box.appendChild(v);
                    mount.appendChild(box);
                });
                overlay.classList.remove('d-none');
            },

            markIntroVideoEnded: (idx) => {
                if (!App.introGateEnded) App.introGateEnded = new Set();
                App.introGateEnded.add(idx);
                const btn = document.getElementById('execIntroCompleteBtn');
                if (btn) {
                    const done = App.introGateEnded.size >= (App.introGateTotal || 0);
                    btn.disabled = !done;
                    btn.title = done ? '' : 'Watch all orientation videos to continue';
                }
            },

            completeIntroGate: () => {
                const total = App.introGateTotal || 0;
                const done = App.introGateEnded ? App.introGateEnded.size : 0;
                if (total > 0 && done < total) {
                    Swal.fire('Watch required videos', 'Please watch all orientation videos before beginning the test.', 'warning');
                    return;
                }
                const overlay = document.getElementById('execIntroOverlay');
                if (overlay) overlay.classList.add('d-none');
                const ev = document.getElementById('executionView');
                if (ev) ev.classList.remove('d-none');
                document.body.style.overflow = 'hidden';
                App.startTimer();
                App.renderExecutionQuestion();
                App.renderNavigator();
                App.updateProgress();
            },

            startExecution: async (testId, packId) => {
                const test = App.Tests.find(t => t.id == testId);

                Swal.fire({
                    title: 'Preparing Test...',
                    text: 'Fetching questions and establishing secure connection.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                try {
                    const response = await fetch(`Test/getPackQuestions/${packId}`);
                    const data = await response.json();

                    if (data.status !== 'success') throw new Error(data.message || 'Failed to fetch questions');

                    // Use real questions: fallback from packQuestions to templateQuestions
                    let questions = data.packQuestions && data.packQuestions.length > 0
                        ? data.packQuestions
                        : data.templateQuestions;

                    if (!questions || questions.length === 0) {
                        throw new Error('No questions found for this test. Please contact admin.');
                    }

                    // Map to expected format
                    App.executionState.questions = questions.map(q => ({
                        id: q.id,
                        text: q.question,
                        type: q.type,
                        options: [q.option_a, q.option_b, q.option_c, q.option_d].filter(o => o && o.trim() !== ''),
                        option_a: q.option_a || '',
                        option_b: q.option_b || '',
                        option_c: q.option_c || '',
                        option_d: q.option_d || '',
                        correct_answer: q.correct_answer || '',
                        category: q.category || 'General',
                        marks: q.marks || 1,
                        pedagogy: String(q.pedagogy ?? q.knowledge_type ?? '').trim()
                    }));

                    const pack = data.pack;
                    const duration = pack ? parseInt(pack.duration || 60) : 60;

                    const testMeta = data.test || test;
                    if (testMeta && testMeta.name) {
                        document.getElementById('execTestTitle').textContent = testMeta.name;
                    } else if (test) {
                        document.getElementById('execTestTitle').textContent = test.name;
                    }

                    App.executionState.active = true;
                    App.executionState.currentIndex = 0;
                    App.executionState.answers = {};
                    App.executionState.flagged = new Set();
                    App.executionState.timeLeft = duration * 60;
                    App.executionState.violations = 0;
                    App.executionState.packId = packId;
                    App.executionState.testId = testId;
                    App.executionState.passMark = parseInt(pack ? (pack.pass_mark || 70) : 70, 10) || 70;
                    App.executionState.durationMins = duration;
                    App.executionState.startedAt = Date.now();
                    App.executionState.testName = testMeta?.name || test?.name || 'Test';
                    App.executionState.batchName = pack?.pack_name || 'Standard Batch';
                    App.executionState.testType = testMeta?.assessment_type || test?.assessment_type || 'Standard';
                    App.executionState.assignedRoles = testMeta?.assigned_to || test?.assigned_to || pack?.user_role || 'General Access';
                    App.executionState.attachment = null;
                    App.executionState.attachments = [];
                    const attachInput = document.getElementById('execSubmissionAttachmentInput');
                    const attachMeta = document.getElementById('execSubmissionAttachmentMeta');
                    const singleInput = document.getElementById('execFinalSingleFileInput');
                    const bulkInput = document.getElementById('execFinalBulkFileInput');
                    if (attachInput) attachInput.value = '';
                    if (attachMeta) attachMeta.textContent = '';
                    if (singleInput) singleInput.value = '';
                    if (bulkInput) bulkInput.value = '';
                    App.renderExecutionAttachmentList();
                    App.backToQuestionsFromFinal();

                    // Calculate Total Marks
                    const totalMarks = App.executionState.questions.reduce((acc, q) => acc + (parseInt(q.marks) || 0), 0);
                    const totalDuration = `${duration} Mins`;

                    // Update UI
                    const totalEl = document.getElementById('execTotalMarks');
                    const passEl = document.getElementById('execPassMark');
                    const durEl = document.getElementById('execTotalDuration');

                    if (totalEl) totalEl.textContent = `${totalMarks} Marks`;
                    if (passEl) passEl.textContent = `${pack ? (pack.pass_mark || 70) : 70}%`;
                    if (durEl) durEl.textContent = totalDuration;

                    const headerLogo = document.getElementById('execHeaderLogo');
                    if (headerLogo) {
                        headerLogo.innerHTML = `<img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&h=100&fit=crop" class="w-full h-full object-cover">`;
                    }

                    Swal.close();

                    const introConfig = await App.resolveIntroConfig(testId, testMeta);
                    if (introConfig.addVideoOn && introConfig.introUrls.length === 0) {
                        throw new Error('Intro video is enabled for this test, but no playable orientation video was found. Please contact admin.');
                    }
                    const wantIntro = introConfig.addVideoOn && introConfig.introUrls.length > 0;

                    if (wantIntro) {
                        document.getElementById('executionView').classList.add('d-none');
                        App.setupIntroGate(introConfig.introUrls);
                    } else {
                        document.getElementById('executionView').classList.remove('d-none');
                        document.body.style.overflow = 'hidden';
                        App.startTimer();
                        App.renderExecutionQuestion();
                        App.renderNavigator();
                        App.updateProgress();
                    }

                } catch (e) {
                    Swal.fire('Error', e.message, 'error');
                }
            },

            startTimer: () => {
                if (App.executionState.timerInterval) clearInterval(App.executionState.timerInterval);

                App.executionState.timerInterval = setInterval(() => {
                    App.executionState.timeLeft--;
                    if (App.executionState.timeLeft <= 0) {
                        clearInterval(App.executionState.timerInterval);
                        App.submitTest(true);
                    }
                    App.updateTimerUI();
                }, 1000);
            },

            updateTimerUI: () => {
                const mins = Math.floor(App.executionState.timeLeft / 60);
                const secs = App.executionState.timeLeft % 60;
                const timeStr = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                const timerText = document.getElementById('timerText');
                if (timerText) {
                    timerText.textContent = timeStr;
                    const timerPill = document.getElementById('execTimer');
                    if (App.executionState.timeLeft < 300) {
                        timerPill.classList.add('warning');
                    }
                }
            },

            renderExecutionQuestion: () => {
                const qIdx = App.executionState.currentIndex;
                const q = App.executionState.questions[qIdx];

                document.getElementById('execQuestionProgress').textContent = `Question ${qIdx + 1} of ${App.executionState.questions.length}`;
                document.getElementById('qIdxBadge').textContent = `Q${qIdx + 1}`;
                const pedEl = document.getElementById('qPedagogyBadge');
                if (pedEl) {
                    const ped = (q.pedagogy || '').trim();
                    pedEl.textContent = ped || '—';
                }
                document.getElementById('qTextContent').textContent = q.text;

                const flagBtn = document.getElementById('flagBtn');
                if (App.executionState.flagged.has(qIdx)) {
                    flagBtn.innerHTML = '<i class="bi bi-flag-fill"></i> Flagged';
                    flagBtn.classList.add('btn-primary-custom');
                } else {
                    flagBtn.innerHTML = '<i class="bi bi-flag"></i> Flag Question';
                    flagBtn.classList.remove('btn-primary-custom');
                }

                const area = document.getElementById('answerArea');
                area.innerHTML = '';

                if (App.normalizeType(q.type) === 'descriptive') {
                    const textarea = document.createElement('textarea');
                    textarea.className = 'form-control mt-4 p-3';
                    textarea.rows = 6;
                    textarea.placeholder = 'Write your answer here...';
                    textarea.style.borderRadius = '12px';
                    textarea.style.borderColor = '#e2e8f0';
                    textarea.value = App.executionState.answers[qIdx] || '';
                    textarea.oninput = (e) => App.saveAnswer(qIdx, e.target.value);
                    area.appendChild(textarea);
                } else {
                    const grid = document.createElement('div');
                    grid.className = 'options-grid mt-4';
                    q.options.forEach((opt) => {
                        const item = document.createElement('div');
                        const isSelected = q.type === 'Multi-select'
                            ? (App.executionState.answers[qIdx] || []).includes(opt)
                            : App.executionState.answers[qIdx] === opt;

                        item.className = `option-item ${isSelected ? 'selected' : ''}`;
                        item.innerHTML = `
                        <div class="${q.type === 'Multi-select' ? 'option-square' : 'option-circle'}"></div>
                        <div class="option-text">${opt}</div>
                    `;
                        item.onclick = () => App.handleOptionClick(qIdx, opt, q.type);
                        grid.appendChild(item);
                    });
                    area.appendChild(grid);
                }

                document.getElementById('nextQBtn').innerHTML = qIdx === App.executionState.questions.length - 1
                    ? 'Next <i class="bi bi-chevron-right ms-2"></i>'
                    : 'Next Question <i class="bi bi-chevron-right ms-2"></i>';
            },

            handleOptionClick: (qIdx, opt, type) => {
                if (type === 'Multi-select') {
                    let selected = App.executionState.answers[qIdx] || [];
                    if (selected.includes(opt)) {
                        selected = selected.filter(s => s !== opt);
                    } else {
                        selected.push(opt);
                    }
                    App.saveAnswer(qIdx, selected);
                } else {
                    App.saveAnswer(qIdx, opt);
                }
                App.renderExecutionQuestion();
                App.renderNavigator();
            },

            saveAnswer: (qIdx, val) => {
                App.executionState.answers[qIdx] = val;
                App.updateProgress();
            },

            updateProgress: () => {
                const total = App.executionState.questions.length;
                const answered = Object.keys(App.executionState.answers).filter(k => {
                    const val = App.executionState.answers[k];
                    return Array.isArray(val) ? val.length > 0 : String(val).trim() !== '' && val !== undefined;
                }).length;

                const percent = (answered / total) * 100;
                const progressBar = document.getElementById('execProgressBar');
                if (progressBar) progressBar.style.width = `${percent}%`;

                const answeredCount = document.getElementById('answeredCount');
                if (answeredCount) answeredCount.textContent = answered;

                const headerSubmitBtn = document.querySelector('.btn-submit-test-custom');

                if (answered === total) {
                    if (headerSubmitBtn) {
                        headerSubmitBtn.disabled = false;
                        headerSubmitBtn.style.opacity = "1";
                    }
                } else {
                    if (headerSubmitBtn) {
                        headerSubmitBtn.disabled = true;
                        headerSubmitBtn.style.opacity = "0.5";
                    }
                }
            },

            toggleFlagCurrent: () => {
                const qIdx = App.executionState.currentIndex;
                if (App.executionState.flagged.has(qIdx)) {
                    App.executionState.flagged.delete(qIdx);
                } else {
                    App.executionState.flagged.add(qIdx);
                }
                const flaggedCount = document.getElementById('flaggedCount');
                if (flaggedCount) flaggedCount.textContent = App.executionState.flagged.size;
                App.renderExecutionQuestion();
                App.renderNavigator();
            },

            nextQuestion: () => {
                if (App.executionState.currentIndex < App.executionState.questions.length - 1) {
                    App.executionState.currentIndex++;
                    App.renderExecutionQuestion();
                    App.renderNavigator();
                } else {
                    App.openFinalSubmissionPage();
                }
            },

            prevQuestion: () => {
                if (App.executionState.currentIndex > 0) {
                    App.executionState.currentIndex--;
                    App.renderExecutionQuestion();
                    App.renderNavigator();
                }
            },

            renderNavigator: () => {
                const grid = document.getElementById('navGrid');
                if (!grid) return;
                grid.innerHTML = '';

                App.executionState.questions.forEach((_, i) => {
                    const item = document.createElement('div');
                    let statusClass = 'unanswered';

                    const ans = App.executionState.answers[i];
                    const isAnswered = ans !== undefined && (
                        Array.isArray(ans) ? ans.length > 0 : String(ans).trim() !== ''
                    );

                    if (i === App.executionState.currentIndex) statusClass = 'current';
                    else if (App.executionState.flagged.has(i)) statusClass = 'flagged';
                    else if (isAnswered) statusClass = 'answered';

                    item.className = `nav-item ${statusClass}`;
                    item.textContent = i + 1;
                    item.onclick = () => {
                        App.executionState.currentIndex = i;
                        App.renderExecutionQuestion();
                        App.renderNavigator();
                    };
                    grid.appendChild(item);
                });
            },

            // --- Interaction UI Helpers ---
            simulateViolation: () => {
                App.executionState.violations++;
                document.getElementById('violationOverlay').classList.add('active');
                const dots = document.getElementById('violationDots').children;
                for (let i = 0; i < dots.length; i++) {
                    if (i < App.executionState.violations) dots[i].className = 'flex-1 bg-danger rounded-pill';
                    else dots[i].className = 'flex-1 bg-light border rounded-pill';
                }
                if (App.executionState.violations >= 3) {
                    document.getElementById('violationTitle').textContent = "Test Terminated";
                    document.getElementById('violationMsg').textContent = "Multiple violations detected. Your Test has been automatically submitted.";
                    const btn = document.querySelector('#violationOverlay .btn');
                    btn.textContent = "Return to Dashboard";
                    btn.onclick = () => App.backToDashboard();

                    // Automatically submit the test
                    App.submitTest(true);
                }
            },

            dismissViolation: () => {
                if (App.executionState.violations < 3) {
                    document.getElementById('violationOverlay').classList.remove('active');
                }
            },

            showSubmitConfirmation: () => {
                App.openFinalSubmissionPage();
            },

            hideSubmitConfirmation: () => {
                document.getElementById('submitConfirmModal').classList.add('d-none');
            },

            readExecutionFileAsDataUrl: (file) => new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(String(reader.result || ''));
                reader.onerror = reject;
                reader.readAsDataURL(file);
            }),

            renderExecutionAttachmentList: () => {
                const wrap = document.getElementById('execFinalAttachmentList');
                if (!wrap) return;
                const files = Array.isArray(App.executionState.attachments) ? App.executionState.attachments : [];
                if (!files.length) {
                    wrap.innerHTML = '<div class="text-[11px] text-slate-400 italic">No files selected.</div>';
                    return;
                }
                wrap.innerHTML = files.map((f, idx) => `
                    <div class="flex items-center justify-between gap-2 px-3 py-2 rounded-lg border border-slate-200 bg-white">
                        <div class="min-w-0">
                            <div class="text-[11px] font-bold text-slate-700 truncate">${f.name}</div>
                            <div class="text-[10px] text-slate-400">${Math.max(1, Math.round((f.size || 0) / 1024))} KB</div>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-600 text-[11px] font-black uppercase tracking-wider" onclick="App.removeExecutionAttachment(${idx})">Remove</button>
                    </div>
                `).join('');
            },

            removeExecutionAttachment: (idx) => {
                if (!Array.isArray(App.executionState.attachments)) App.executionState.attachments = [];
                App.executionState.attachments.splice(idx, 1);
                App.executionState.attachment = App.executionState.attachments[0] || null;
                App.renderExecutionAttachmentList();
            },

            onExecutionAttachmentsChange: async (inputEl) => {
                const files = Array.from(inputEl?.files || []);
                if (!files.length) return;
                const maxBytes = 2 * 1024 * 1024;
                const validFiles = files.filter(file => (file.size || 0) <= maxBytes);
                if (validFiles.length !== files.length) {
                    Swal.fire('File too large', 'Some files were skipped because they exceed 2 MB.', 'warning');
                }
                const current = Array.isArray(App.executionState.attachments) ? App.executionState.attachments : [];
                const room = Math.max(0, 10 - current.length);
                const picked = validFiles.slice(0, room);
                if (!picked.length) {
                    if (inputEl) inputEl.value = '';
                    App.renderExecutionAttachmentList();
                    return;
                }
                try {
                    const parsed = await Promise.all(picked.map(async file => ({
                        name: file.name,
                        type: file.type || 'application/octet-stream',
                        size: file.size || 0,
                        data_url: await App.readExecutionFileAsDataUrl(file)
                    })));
                    App.executionState.attachments = [...current, ...parsed];
                    App.executionState.attachment = App.executionState.attachments[0] || null;
                    App.renderExecutionAttachmentList();
                } catch (e) {
                    Swal.fire('Attachment error', 'Failed to read selected file.', 'error');
                } finally {
                    if (inputEl) inputEl.value = '';
                }
            },

            onExecutionAttachmentChange: (inputEl) => App.onExecutionAttachmentsChange(inputEl),

            openFinalSubmissionPage: () => {
                document.getElementById('questionCard')?.classList.add('d-none');
                document.getElementById('execQuestionFooter')?.classList.add('d-none');
                document.getElementById('finalSubmissionPage')?.classList.remove('d-none');
                App.renderExecutionAttachmentList();
            },

            backToQuestionsFromFinal: () => {
                document.getElementById('questionCard')?.classList.remove('d-none');
                document.getElementById('execQuestionFooter')?.classList.remove('d-none');
                document.getElementById('finalSubmissionPage')?.classList.add('d-none');
            },

            confirmSubmit: () => App.openFinalSubmissionPage(),

            isMcqCorrect: (question, answer) => {
                if (answer === undefined || answer === null) return false;

                const normalize = (v) => String(v || '').trim().toLowerCase();
                const correctRaw = String(question.correct_answer || '').trim();
                if (!correctRaw) return false;

                const optionMap = {
                    a: question.option_a || '',
                    b: question.option_b || '',
                    c: question.option_c || '',
                    d: question.option_d || ''
                };

                const expectedValues = correctRaw
                    .split(/[,/|]/)
                    .map(s => s.trim())
                    .filter(Boolean)
                    .map(token => {
                        const key = token.toLowerCase();
                        if (optionMap[key]) return optionMap[key];
                        return token;
                    });

                if (Array.isArray(answer)) {
                    const selected = answer.map(normalize).filter(Boolean).sort();
                    const expected = expectedValues.map(normalize).filter(Boolean).sort();
                    return selected.length > 0 &&
                        selected.length === expected.length &&
                        selected.every((v, idx) => v === expected[idx]);
                }

                const ans = normalize(answer);
                const expectedNormalized = expectedValues.map(normalize);
                return expectedNormalized.includes(ans) || expectedNormalized.includes(normalize(optionMap[ans]));
            },

            getCandidateName: () => {
                const el = document.getElementById('execCandidateName');
                return (el?.textContent || 'Candidate').trim();
            },

            getSubmissionKey: () => {
                const name = App.getCandidateName();
                const packId = App.executionState.packId || 'pack';
                return `${packId}::${name}`;
            },

            getAttachmentActionsHtml: (submission) => {
                const file = submission?.attachment || (Array.isArray(submission?.attachments) ? submission.attachments[0] : null);
                if (!file || !file.data_url) {
                    return '<span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">-</span>';
                }
                const safeName = String(file.name || 'attachment').replace(/"/g, '');
                return `
                    <div class="flex items-center justify-center gap-2">
                        <a href="${file.data_url}" target="_blank" onclick="event.stopPropagation()"
                            class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all inline-flex items-center justify-center"
                            title="View Attachment">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="${file.data_url}" download="${safeName}" onclick="event.stopPropagation()"
                            class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all inline-flex items-center justify-center"
                            title="Download Attachment">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                `;
            },

            getAllSubmissions: () => Object.values(App.evaluationState.submissions || {}),

            getPackSubmissionStats: (packId) => {
                const rows = App.getAllSubmissions().filter(s => String(s.pack_id) === String(packId));
                const completed = rows.filter(s => s.completed).length;
                const subjectivePending = rows.filter(s =>
                    (s.subjective_items || []).some(q => (q.candidate_answer || '').trim() && !q.graded)
                ).length;
                return { total: rows.length, completed, subjectivePending };
            },

            submitTest: (auto = false) => {
                clearInterval(App.executionState.timerInterval);
                document.getElementById('submitConfirmModal').classList.add('d-none');
                document.getElementById('executionView').classList.add('d-none');
                document.getElementById('submissionSuccessOverlay').classList.remove('d-none');

                const questions = App.executionState.questions || [];
                const answers = App.executionState.answers || {};

                let mcqScore = 0;
                let mcqTotal = 0;
                const subjectiveItems = [];

                questions.forEach((q, idx) => {
                    const type = App.normalizeType(q.type);
                    const marks = parseInt(q.marks || 0, 10) || 0;
                    const answer = answers[idx];

                    if (type === 'mcq') {
                        mcqTotal += marks;
                        if (App.isMcqCorrect(q, answer)) mcqScore += marks;
                    } else if (type === 'descriptive') {
                        const ansText = String(answer ?? '').trim();
                        if (!ansText) return;
                        subjectiveItems.push({
                            id: `${q.id || idx}`,
                            index: idx,
                            text: q.text || '',
                            marks,
                            candidate_answer: ansText,
                            awarded_marks: null,
                            graded: false
                        });
                    }
                });

                const totalMarks = questions.reduce((sum, q) => sum + (parseInt(q.marks || 0, 10) || 0), 0);
                const elapsedSeconds = Math.max(
                    0,
                    (parseInt(App.executionState.durationMins || 60, 10) * 60) - (parseInt(App.executionState.timeLeft || 0, 10))
                );
                const manualScore = subjectiveItems.reduce((sum, item) => sum + (item.awarded_marks || 0), 0);
                const finalScore = mcqScore + manualScore;
                const key = App.getSubmissionKey();

                App.evaluationState.submissions[key] = {
                    key,
                    candidate_name: App.getCandidateName(),
                    test_id: App.executionState.testId,
                    pack_id: App.executionState.packId,
                    test_name: App.executionState.testName || 'Test',
                    batch_name: App.executionState.batchName || 'Batch',
                    test_type: App.executionState.testType || 'Standard',
                    assigned_roles: App.executionState.assignedRoles || 'General Access',
                    submitted_date: new Date().toISOString(),
                    submitted_at: Date.now(),
                    completed: true,
                    auto_submitted: !!auto,
                    mcq_score: mcqScore,
                    mcq_total: mcqTotal,
                    subjective_total: subjectiveItems.reduce((sum, item) => sum + item.marks, 0),
                    subjective_items: subjectiveItems,
                    evaluation_saved: subjectiveItems.length === 0,
                    final_score: finalScore,
                    total_marks: totalMarks,
                    pass_mark: parseInt(App.executionState.passMark || 70, 10) || 70,
                    duration_seconds: elapsedSeconds,
                    answers_snapshot: { ...answers },
                    attachment: App.executionState.attachment ? { ...App.executionState.attachment } : null,
                    attachments: Array.isArray(App.executionState.attachments)
                        ? App.executionState.attachments.map(f => ({ ...f }))
                        : []
                };

                App.saveEvaluationState();
                App.loadCandidateResult();
                App.renderEvaluatorView();
                if (typeof initTestsDataTable === 'function') initTestsDataTable();
                if (typeof App.initExecutionDashboard === 'function') App.initExecutionDashboard();
            },



            backToDashboard: () => {
                location.reload();
            },

            // --- Results & Evaluation ---
            loadCandidateResult: () => {
                const testTypeFilterEl = document.getElementById('resultsTypeFilter');
                const testFilterEl = document.getElementById('resultsTestFilter');
                const groupFilterEl = document.getElementById('resultsGroupFilter');
                const dateFilterEl = document.getElementById('resultsDateFilter');
                const sortFilterEl = document.getElementById('resultsSortFilter');
                const candidateSearchEl = document.getElementById('resultsCandidateSearch');

                const selectedType = testTypeFilterEl?.value || '';
                const selectedTest = testFilterEl?.value || '';
                const selectedGroup = groupFilterEl?.value || '';
                const selectedDate = dateFilterEl?.value || '';
                const sortMode = sortFilterEl?.value || 'high';
                const candidateSearch = (candidateSearchEl?.value || '').trim().toLowerCase();

                const norm = (v) => String(v || '').trim();
                const normLower = (v) => norm(v).toLowerCase();
                const ymd = (v) => {
                    if (!v) return '';
                    const d = new Date(v);
                    if (Number.isNaN(d.getTime())) return '';
                    return d.toISOString().slice(0, 10);
                };

                const submissionsByPackCandidate = {};
                const submissions = App.getAllSubmissions().map(item => {
                    const subjectiveAwarded = (item.subjective_items || []).reduce((sum, q) => sum + (q.awarded_marks || 0), 0);
                    const finalScore = (item.mcq_score || 0) + ((item.evaluation_saved || (item.subjective_items || []).length === 0) ? subjectiveAwarded : 0);
                    const totalMarks = parseInt(item.total_marks || 0, 10) || 0;
                    const accuracy = totalMarks > 0 ? Math.round((finalScore / totalMarks) * 100) : 0;
                    const passMark = parseInt(item.pass_mark || 70, 10) || 70;
                    const passCutoff = Math.round((totalMarks * passMark) / 100);
                    const key = `${item.pack_id || ''}::${normLower(item.candidate_name)}`;
                    const enriched = {
                        ...item,
                        final_score: finalScore,
                        total_marks: totalMarks,
                        accuracy,
                        status: item.completed ? 'Completed' : 'Pending',
                        pass_fail: finalScore >= passCutoff ? 'Pass' : 'Fail',
                        submitted_ymd: ymd(item.submitted_date || item.submitted_at)
                    };
                    submissionsByPackCandidate[key] = enriched;
                    return enriched;
                });

                const roleMap = {};
                (App.Tests || []).forEach(test => {
                    const testName = norm(test?.name);
                    const testType = norm(test?.type);
                    const role = norm(test?.assigned_to || test?.user_role || 'General Access');
                    (test?.test_packs || []).forEach(pack => {
                        const packId = String(pack?.id || '');
                        if (!packId) return;
                        roleMap[packId] = roleMap[packId] || {
                            test_name: testName || '-',
                            test_type: testType || '-',
                            role: role || '-',
                            group_name: norm(pack?.pack_name || 'Batch'),
                            date_ymd: ymd(pack?.scheduled_date || pack?.start_time)
                        };
                    });
                });

                const rows = [];
                (App.Tests || []).forEach(test => {
                    const testName = norm(test?.name || '-');
                    const testType = norm(test?.type || '-');
                    const role = norm(test?.assigned_to || test?.user_role || 'General Access');
                    const packs = Array.isArray(test?.test_packs) ? test.test_packs : [];

                    packs.forEach(pack => {
                        const packId = String(pack?.id || '');
                        if (!packId) return;
                        const groupName = norm(pack?.pack_name || 'Batch');
                        const dateYmd = ymd(pack?.scheduled_date || pack?.start_time);
                        const passMark = parseInt(pack?.pass_mark || test?.pass_mark || 70, 10) || 70;

                        const selectedIds = (() => {
                            const raw = norm(pack?.candidates);
                            if (!raw || raw.toLowerCase() === 'all') {
                                return (App.employees || []).map(e => String(e.id));
                            }
                            return raw.split(',').map(v => v.trim()).filter(Boolean);
                        })();

                        selectedIds.forEach(empId => {
                            const emp = (App.employees || []).find(e => String(e.id) === String(empId));
                            if (!emp) return;
                            const candidateName = norm(emp?.name || '');
                            if (!candidateName) return;
                            const subKey = `${packId}::${normLower(candidateName)}`;
                            const submission = submissionsByPackCandidate[subKey];

                            if (submission) {
                                const totalMarks = parseInt(submission.total_marks || 0, 10) || 0;
                                const finalScore = parseInt(submission.final_score || 0, 10) || 0;
                                const accuracy = totalMarks > 0 ? Math.round((finalScore / totalMarks) * 100) : 0;
                                const passCutoff = Math.round((totalMarks * passMark) / 100);
                                rows.push({
                                    key: submission.key || subKey,
                                    candidate_name: candidateName,
                                    test_type: norm(submission.test_type || testType),
                                    test_name: norm(submission.test_name || testName),
                                    role: norm(submission.assigned_roles || role),
                                    group_name: groupName,
                                    date_ymd: submission.submitted_ymd || dateYmd,
                                    status: 'Completed',
                                    marks_text: `${finalScore} / ${totalMarks}`,
                                    final_score: finalScore,
                                    overall_pct: accuracy,
                                    pass_fail: finalScore >= passCutoff ? 'Pass' : 'Fail',
                                    subjective_items: submission.subjective_items || []
                                });
                            } else {
                                rows.push({
                                    key: `pending::${packId}::${empId}`,
                                    candidate_name: candidateName,
                                    test_type: testType,
                                    test_name: testName,
                                    role: role,
                                    group_name: groupName,
                                    date_ymd: dateYmd,
                                    status: 'Pending',
                                    marks_text: '0 / 0',
                                    final_score: 0,
                                    overall_pct: 0,
                                    pass_fail: '-',
                                    subjective_items: []
                                });
                            }
                        });
                    });
                });

                // Include completed submissions even if no candidate mapping exists anymore
                submissions.forEach(sub => {
                    const already = rows.some(r => String(r.key) === String(sub.key));
                    if (already) return;
                    const meta = roleMap[String(sub.pack_id)] || {};
                    rows.push({
                        key: sub.key,
                        candidate_name: norm(sub.candidate_name || 'Candidate'),
                        test_type: norm(sub.test_type || meta.test_type || '-'),
                        test_name: norm(sub.test_name || meta.test_name || '-'),
                        role: norm(sub.assigned_roles || meta.role || '-'),
                        group_name: norm(sub.batch_name || meta.group_name || 'Batch'),
                        date_ymd: sub.submitted_ymd || meta.date_ymd || '',
                        status: 'Completed',
                        marks_text: `${sub.final_score || 0} / ${sub.total_marks || 0}`,
                        final_score: parseInt(sub.final_score || 0, 10) || 0,
                        overall_pct: parseInt(sub.accuracy || 0, 10) || 0,
                        pass_fail: sub.pass_fail || '-',
                        subjective_items: sub.subjective_items || []
                    });
                });

                const bindOptions = (el, options, allText) => {
                    if (!el) return;
                    const current = el.value || '';
                    const safeOptions = [...new Set(options.filter(Boolean))].sort((a, b) => a.localeCompare(b));
                    el.innerHTML = `<option value="">${allText}</option>${safeOptions.map(v => `<option value="${v}">${v}</option>`).join('')}`;
                    if (safeOptions.includes(current)) el.value = current;
                };

                bindOptions(testTypeFilterEl, rows.map(r => r.test_type), 'All Test Types');
                bindOptions(testFilterEl, rows.map(r => r.test_name), 'All Test Names');

                let activeSelectedTest = selectedTest;
                const contextTestName = String(App.resultsContextTestName || '').trim();
                if (contextTestName && testFilterEl) {
                    const matchingOption = Array.from(testFilterEl.options || []).find(opt => opt.value === contextTestName);
                    if (matchingOption) {
                        testFilterEl.value = contextTestName;
                        activeSelectedTest = contextTestName;
                        if (groupFilterEl) groupFilterEl.value = '';
                        if (dateFilterEl) dateFilterEl.value = '';
                    }
                    App.resultsContextTestName = '';
                }

                const groupScopeRows = activeSelectedTest
                    ? rows.filter(r => r.test_name === activeSelectedTest)
                    : rows;
                bindOptions(groupFilterEl, groupScopeRows.map(r => r.group_name), 'All Groups');
                bindOptions(dateFilterEl, groupScopeRows.map(r => r.date_ymd), 'All Dates');

                let filtered = rows.filter(r => {
                    if (selectedType && r.test_type !== selectedType) return false;
                    if (activeSelectedTest && r.test_name !== activeSelectedTest) return false;
                    if (selectedGroup && r.group_name !== selectedGroup) return false;
                    if (selectedDate && r.date_ymd !== selectedDate) return false;
                    if (candidateSearch && !normLower(r.candidate_name).includes(candidateSearch)) return false;
                    return true;
                });

                filtered.sort((a, b) => {
                    const scoreDiff = sortMode === 'low' ? (a.final_score - b.final_score) : (b.final_score - a.final_score);
                    if (scoreDiff !== 0) return scoreDiff;
                    return norm(a.candidate_name).localeCompare(norm(b.candidate_name));
                });

                const paginationState = App.resultsPagination || { page: 1, perPage: 10 };
                App.resultsPagination = paginationState;
                const totalRows = filtered.length;
                const totalPages = Math.max(1, Math.ceil(totalRows / paginationState.perPage));
                if (paginationState.page > totalPages) paginationState.page = totalPages;
                if (paginationState.page < 1) paginationState.page = 1;
                const startIdx = (paginationState.page - 1) * paginationState.perPage;
                const endIdx = startIdx + paginationState.perPage;
                const pagedRows = filtered.slice(startIdx, endIdx);

                const tbody = document.getElementById('topicBreakdownTable');
                if (tbody) {
                    if (!pagedRows.length) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-[12px] font-bold text-slate-400 uppercase tracking-widest">
                                    No candidate data found
                                </td>
                            </tr>
                        `;
                    } else {
                        tbody.innerHTML = pagedRows.map((item) => `
                            ${(() => {
                                const hasAnsweredSubjective = (item.subjective_items || []).some(q => (q.candidate_answer || '').trim() !== '');
                                const canEvaluate = item.status === 'Completed' && !!item.key && !String(item.key).startsWith('pending::') && hasAnsweredSubjective;
                                const evaluateCell = canEvaluate
                                    ? `<button class="w-7 h-7 rounded-md border border-indigo-200 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all inline-flex items-center justify-center"
                                            onclick="event.stopPropagation(); App.openEvaluatorForSubmission('${item.key}')"
                                            title="Evaluate Candidate">
                                            <i class="bi bi-clipboard-check text-[11px]"></i>
                                       </button>`
                                    : `<span class="text-slate-300 text-[11px] font-black">-</span>`;
                                return `
                            <tr class="hover:bg-[#f8fafc] transition-colors">
                                <td class="px-6 py-2 text-[12px] font-bold text-[#334155]">${item.candidate_name || '-'}</td>
                                <td class="px-6 py-2 text-[12px] text-[#64748b] text-center font-bold">${item.test_type || '-'}</td>
                                <td class="px-6 py-2 text-[12px] text-[#64748b] text-center font-bold uppercase">${item.role || '-'}</td>
                                <td class="px-6 py-2 text-center">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase ${item.status === 'Completed' ? 'bg-[#ecfeff] text-[#0891b2] border border-[#a5f3fc]' : 'bg-[#f8fafc] text-[#64748b] border border-[#e2e8f0]'}">${item.status}</span>
                                </td>
                                <td class="px-6 py-2 text-[12px] text-right font-black text-[#dc2230]">${item.marks_text}</td>
                                <td class="px-6 py-2 text-[12px] text-right font-black text-[#1e293b]">${item.overall_pct}%</td>
                                <td class="px-6 py-2 text-center">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase ${item.pass_fail === 'Pass' ? 'bg-[#f0fdf4] text-[#16a34a] border border-[#bbf7d0]' : item.pass_fail === 'Fail' ? 'bg-[#fef2f2] text-[#dc2626] border border-[#fecaca]' : 'bg-[#f8fafc] text-[#64748b] border border-[#e2e8f0]'}">${item.pass_fail}</span>
                                </td>
                                <td class="px-6 py-2 text-center">${evaluateCell}</td>
                            </tr>
                            `;
                            })()}
                        `).join('');
                    }
                }

                const paginationEl = document.getElementById('resultsLeaderboardPagination');
                if (paginationEl) {
                    if (totalRows <= paginationState.perPage) {
                        paginationEl.innerHTML = '';
                    } else {
                        const maxVisible = 5;
                        let startPage = Math.max(1, paginationState.page - Math.floor(maxVisible / 2));
                        let endPage = Math.min(totalPages, startPage + maxVisible - 1);
                        if ((endPage - startPage + 1) < maxVisible) {
                            startPage = Math.max(1, endPage - maxVisible + 1);
                        }

                        const pageBtns = [];
                        for (let p = startPage; p <= endPage; p++) {
                            pageBtns.push(`
                                <button type="button"
                                    class="h-8 min-w-[32px] px-2 rounded-md text-[10px] font-black border transition-all ${p === paginationState.page ? 'bg-red-600 text-white border-red-600' : 'bg-white text-slate-500 border-slate-200 hover:border-red-200 hover:text-red-600'}"
                                    onclick="App.setResultsPage(${p})">${p}</button>
                            `);
                        }

                        paginationEl.innerHTML = `
                            <button type="button"
                                class="h-8 px-3 rounded-md text-[10px] font-black border ${paginationState.page === 1 ? 'bg-slate-50 text-slate-300 border-slate-100 cursor-not-allowed' : 'bg-white text-slate-500 border-slate-200 hover:border-red-200 hover:text-red-600'}"
                                ${paginationState.page === 1 ? 'disabled' : ''}
                                onclick="App.setResultsPage(${paginationState.page - 1})">Previous</button>
                            ${pageBtns.join('')}
                            <button type="button"
                                class="h-8 px-3 rounded-md text-[10px] font-black border ${paginationState.page === totalPages ? 'bg-slate-50 text-slate-300 border-slate-100 cursor-not-allowed' : 'bg-white text-slate-500 border-slate-200 hover:border-red-200 hover:text-red-600'}"
                                ${paginationState.page === totalPages ? 'disabled' : ''}
                                onclick="App.setResultsPage(${paginationState.page + 1})">Next</button>
                        `;
                    }
                }

                const hasAnyFilter = !!(selectedType || activeSelectedTest || selectedGroup || selectedDate || candidateSearch);
                const totalScore = hasAnyFilter ? filtered.reduce((sum, r) => sum + (r.final_score || 0), 0) : 0;
                const completedRows = hasAnyFilter ? filtered.filter(r => r.status === 'Completed') : [];
                const passRows = completedRows.filter(r => r.pass_fail === 'Pass');
                const failRows = completedRows.filter(r => r.pass_fail === 'Fail');
                const pendingRows = hasAnyFilter ? filtered.filter(r => r.status === 'Pending') : [];
                const passPct = hasAnyFilter && completedRows.length ? Math.round((passRows.length / completedRows.length) * 100) : 0;

                const totalScoreEl = document.getElementById('resSummaryTotalScore');
                const passPctEl = document.getElementById('resSummaryPassPct');
                const failCountEl = document.getElementById('resSummaryFailCount');
                const pendingCountEl = document.getElementById('resSummaryPendingCount');
                if (totalScoreEl) totalScoreEl.textContent = `${totalScore}`;
                if (passPctEl) passPctEl.textContent = `${passPct}%`;
                if (failCountEl) failCountEl.textContent = `${failRows.length}`;
                if (pendingCountEl) pendingCountEl.textContent = `${pendingRows.length}`;

                const countEl = document.getElementById('breakdown-cat-count');
                if (countEl) countEl.textContent = `${filtered.length} Candidate${filtered.length === 1 ? '' : 's'}`;

            },

            loadDetailedResult: (name) => {
                Swal.fire({
                    title: 'Student Details',
                    text: `Loading full performance breakdown for ${name}...`,
                    icon: 'info',
                    timer: 1000,
                    showConfirmButton: false
                });
            },

            deleteLeaderboardEntry: async (submissionKey) => {
                const submission = App.evaluationState.submissions?.[submissionKey];
                if (!submission) return;

                const result = await Swal.fire({
                    title: 'Delete Candidate Score?',
                    text: `This will remove ${submission.candidate_name || 'this candidate'} from leaderboard and evaluator list.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#cbd5e1'
                });
                if (!result.isConfirmed) return;

                delete App.evaluationState.submissions[submissionKey];
                App.saveEvaluationState();
                App.loadCandidateResult();
                App.renderEvaluatorView();
                if (typeof initTestsDataTable === 'function') initTestsDataTable();
                if (typeof App.initExecutionDashboard === 'function') App.initExecutionDashboard();

                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: 'Candidate score entry removed.',
                    timer: 1000,
                    showConfirmButton: false
                });
            },

            setResultsPage: (pageNo) => {
                if (!App.resultsPagination) App.resultsPagination = { page: 1, perPage: 10 };
                App.resultsPagination.page = Math.max(1, parseInt(pageNo || 1, 10) || 1);
                App.loadCandidateResult();
            },

            openResultsForTest: (testName) => {
                App.resultsContextTestName = String(testName || '').trim();
                if (typeof window.switchMainTab === 'function') window.switchMainTab('results');
                if (typeof window.switchResultView === 'function') window.switchResultView('student');
            },

            /**
             * Build leaderboard rows for one batch (same scoring rules as admin leaderboard).
             */
            computePackScoreboard: (packId) => {
                const pid = String(packId ?? '');
                if (!pid) return null;
                let pack = null;
                let test = null;
                for (const t of App.Tests || []) {
                    const p = (t.test_packs || []).find(pp => String(pp.id) === pid);
                    if (p) {
                        pack = p;
                        test = t;
                        break;
                    }
                }
                if (!pack || !test) return null;

                const norm = (v) => String(v || '').trim();
                const normLower = (v) => norm(v).toLowerCase();

                const submissionsByKey = {};
                App.getAllSubmissions().forEach((item) => {
                    if (String(item.pack_id) !== pid) return;
                    const subjectiveAwarded = (item.subjective_items || []).reduce((sum, q) => sum + (q.awarded_marks || 0), 0);
                    const gradedPart = (item.evaluation_saved || (item.subjective_items || []).length === 0) ? subjectiveAwarded : 0;
                    const finalScore = (item.mcq_score || 0) + gradedPart;
                    const totalMarks = parseInt(item.total_marks || 0, 10) || 0;
                    const k = `${pid}::${normLower(item.candidate_name)}`;
                    submissionsByKey[k] = { item, final_score: finalScore, total_marks: totalMarks };
                });

                const selectedIds = (() => {
                    const raw = norm(pack.candidates);
                    if (!raw || raw.toLowerCase() === 'all') {
                        return (App.employees || []).map(e => String(e.id));
                    }
                    return raw.split(',').map((v) => v.trim()).filter(Boolean);
                })();

                const passMarkPct = parseInt(pack.pass_mark ?? test.pass_mark ?? 70, 10) || 70;
                const board = [];
                selectedIds.forEach((empId) => {
                    const emp = (App.employees || []).find((e) => String(e.id) === String(empId));
                    if (!emp) return;
                    const candidateName = norm(emp.name);
                    if (!candidateName) return;
                    const sk = `${pid}::${normLower(candidateName)}`;
                    const hit = submissionsByKey[sk];
                    let finalScore = 0;
                    let totalMarks = 0;
                    let submission = null;
                    if (hit) {
                        finalScore = hit.final_score;
                        totalMarks = hit.total_marks;
                        submission = hit.item;
                    }
                    const overallPct = totalMarks > 0 ? Math.round((finalScore / totalMarks) * 100) : 0;
                    const passCutoff = Math.round((totalMarks * passMarkPct) / 100);
                    let passFail = '-';
                    if (totalMarks > 0) {
                        passFail = finalScore >= passCutoff ? 'Pass' : 'Fail';
                    }
                    board.push({
                        candidate_name: candidateName,
                        final_score: finalScore,
                        total_marks: totalMarks,
                        overall_pct: overallPct,
                        pass_fail: passFail,
                        submission
                    });
                });

                // Submissions keyed by display name at test time may not match roster names (e.g. "Candidate" vs HR name).
                const namesOnBoard = new Set(board.map((r) => normLower(r.candidate_name)));
                App.getAllSubmissions().forEach((item) => {
                    if (String(item.pack_id) !== pid || !item.completed) return;
                    const cn = norm(item.candidate_name);
                    if (!cn) return;
                    if (namesOnBoard.has(normLower(cn))) return;
                    const subjectiveAwarded = (item.subjective_items || []).reduce((sum, q) => sum + (q.awarded_marks || 0), 0);
                    const gradedPart = (item.evaluation_saved || (item.subjective_items || []).length === 0) ? subjectiveAwarded : 0;
                    const fs = (item.mcq_score || 0) + gradedPart;
                    const tm = parseInt(item.total_marks || 0, 10) || 0;
                    const overallPct = tm > 0 ? Math.round((fs / tm) * 100) : 0;
                    const passCutoff = Math.round((tm * passMarkPct) / 100);
                    let passFail = '-';
                    if (tm > 0) passFail = fs >= passCutoff ? 'Pass' : 'Fail';
                    board.push({
                        candidate_name: cn,
                        final_score: fs,
                        total_marks: tm,
                        overall_pct: overallPct,
                        pass_fail: passFail,
                        submission: item
                    });
                    namesOnBoard.add(normLower(cn));
                });

                board.sort((a, b) => {
                    if (b.final_score !== a.final_score) return b.final_score - a.final_score;
                    if (b.overall_pct !== a.overall_pct) return b.overall_pct - a.overall_pct;
                    return a.candidate_name.localeCompare(b.candidate_name);
                });

                let lastRank = 0;
                let lastKey = '';
                board.forEach((row, idx) => {
                    const tieKey = `${row.final_score}|${row.overall_pct}`;
                    if (idx === 0 || tieKey !== lastKey) {
                        lastRank = idx + 1;
                        lastKey = tieKey;
                    }
                    row.rank = lastRank;
                });

                return { test, pack, board, passMarkPct };
            },

            openStudentResultSummaryModal: (packId) => {
                const normL = (v) => String(v || '').trim().toLowerCase();
                const me = normL(App.getCandidateName());
                const modalEl = document.getElementById('studentResultSummaryModal');
                const titleEl = document.getElementById('studentResultSummaryTitleLabel');
                const subEl = document.getElementById('studentResultSummarySubtitle');
                const bodyEl = document.getElementById('studentResultSummaryBody');
                if (!modalEl || !titleEl || !subEl || !bodyEl) return;

                const directSub = App.getAllSubmissions().find((s) =>
                    String(s.pack_id) === String(packId) && s.completed && normL(s.candidate_name) === me
                );
                if (!directSub) {
                    Swal.fire('No results', 'No completed submission was found for you in this batch.', 'info');
                    return;
                }

                let data = App.computePackScoreboard(packId);
                if (!data) {
                    Swal.fire('Unavailable', 'Could not load this batch.', 'info');
                    return;
                }

                const nameKey = normL(directSub.candidate_name);
                let myRow = data.board.find((r) => normL(r.candidate_name) === nameKey && r.submission);
                if (!myRow) {
                    const subjectiveAwarded = (directSub.subjective_items || []).reduce((sum, q) => sum + (q.awarded_marks || 0), 0);
                    const gradedPart = (directSub.evaluation_saved || (directSub.subjective_items || []).length === 0) ? subjectiveAwarded : 0;
                    const finalScore = (directSub.mcq_score || 0) + gradedPart;
                    const totalMarks = parseInt(directSub.total_marks || 0, 10) || 0;
                    const passMarkPct = data.passMarkPct;
                    const overallPct = totalMarks > 0 ? Math.round((finalScore / totalMarks) * 100) : 0;
                    const passCutoff = Math.round((totalMarks * passMarkPct) / 100);
                    let passFail = '-';
                    if (totalMarks > 0) passFail = finalScore >= passCutoff ? 'Pass' : 'Fail';
                    const merged = data.board.filter((r) => normL(r.candidate_name) !== nameKey).concat([{
                        candidate_name: String(directSub.candidate_name || '').trim() || App.getCandidateName(),
                        final_score: finalScore,
                        total_marks: totalMarks,
                        overall_pct: overallPct,
                        pass_fail: passFail,
                        submission: directSub
                    }]);
                    merged.sort((a, b) => {
                        if (b.final_score !== a.final_score) return b.final_score - a.final_score;
                        if (b.overall_pct !== a.overall_pct) return b.overall_pct - a.overall_pct;
                        return a.candidate_name.localeCompare(b.candidate_name);
                    });
                    let lastRank = 0;
                    let lastKey = '';
                    merged.forEach((row, idx) => {
                        const tieKey = `${row.final_score}|${row.overall_pct}`;
                        if (idx === 0 || tieKey !== lastKey) {
                            lastRank = idx + 1;
                            lastKey = tieKey;
                        }
                        row.rank = lastRank;
                    });
                    myRow = merged.find((r) => normL(r.candidate_name) === nameKey);
                    data = { ...data, board: merged };
                }

                if (!myRow) {
                    Swal.fire('No results', 'Could not match your submission to this batch.', 'info');
                    return;
                }

                let evalNote = '';
                const sub = myRow.submission || directSub;
                const hasSubjective = Array.isArray(sub.subjective_items) && sub.subjective_items.length > 0;
                if (hasSubjective && !sub.evaluation_saved) {
                    evalNote = `
                        <p class="text-[11px] text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 mb-0 mt-3">
                            <i class="bi bi-info-circle me-1"></i> Descriptive scores may still update until evaluation is finalized.
                        </p>`;
                }

                const passBadge = myRow.pass_fail === 'Pass'
                    ? '<span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-100">Pass</span>'
                    : (myRow.pass_fail === 'Fail'
                        ? '<span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-red-50 text-red-700 border border-red-100">Fail</span>'
                        : '<span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-slate-100 text-slate-600 border border-slate-200">—</span>');

                titleEl.textContent = `${data.test.name || 'Test'} — your results`;
                subEl.textContent = data.pack.pack_name || 'Batch';

                bodyEl.innerHTML = `
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Marks obtained</p>
                            <p class="text-[22px] font-black text-slate-900 mb-0">${myRow.final_score}<span class="text-slate-400 text-[14px] font-bold"> / ${myRow.total_marks}</span></p>
                        </div>
                        <div class="rounded-xl border border-violet-100 bg-violet-50/60 p-4">
                            <p class="text-[9px] font-black text-violet-700 uppercase tracking-widest mb-1">Overall rank in batch</p>
                            <p class="text-[22px] font-black text-violet-900 mb-0">#${myRow.rank}<span class="text-violet-600/80 text-[12px] font-bold"> of ${data.board.length}</span></p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap items-center gap-3 rounded-xl border border-slate-100 bg-white p-4">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Overall %</p>
                            <p class="text-[16px] font-black text-slate-800 mb-0">${myRow.overall_pct}%</p>
                        </div>
                        <div class="h-8 w-px bg-slate-100 hidden sm:block"></div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Outcome</p>
                            <div>${passBadge}</div>
                        </div>
                        <div class="h-8 w-px bg-slate-100 hidden sm:block"></div>
                        <div class="flex-1 min-w-[140px]">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Pass mark (${data.passMarkPct}%)</p>
                            <p class="text-[12px] font-bold text-slate-600 mb-0">You need roughly ${Math.round((myRow.total_marks || 0) * (data.passMarkPct || 70) / 100)} marks to pass this test.</p>
                        </div>
                    </div>
                    ${evalNote}
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-sm btn-light border text-slate-700 px-4" data-bs-dismiss="modal">Close</button>
                    </div>`;

                const inst = bootstrap.Modal.getOrCreateInstance(modalEl);
                if (!modalEl.dataset.blurBound) {
                    modalEl.dataset.blurBound = '1';
                    modalEl.addEventListener('hidden.bs.modal', () => {
                        document.body.classList.remove('result-modal-open');
                    });
                }
                document.body.classList.add('result-modal-open');
                inst.show();
            },

            openEvaluatorForSubmission: (submissionKey) => {
                if (!submissionKey) return;
                App.activeEvaluatorSubmissionKey = submissionKey;
                if (typeof window.switchMainTab === 'function') window.switchMainTab('results');
                if (typeof window.switchResultView === 'function') window.switchResultView('evaluator');
                App.renderEvaluatorView(submissionKey);
            },

            downloadBulkEvaluationTemplate: () => {
                const headers = ["candidate_id", "candidate_name", "assessment_id", "question_count", "marks_obtained"];
                const candidates = [
                    ["C001", "Arjun Sharma", "T882", "40", ""],
                    ["C002", "Priya Patel", "T882", "40", ""],
                    ["C003", "Vikram Singh", "T882", "40", ""],
                    ["C004", "Ananya Iyer", "T882", "40", ""]
                ];

                const instructions = "# EVALUATION UPLOAD INSTRUCTIONS:\n"
                    + "# 1. Keep candidate_id and candidate_name as provided.\n"
                    + "# 2. Fill the 'marks_obtained' column for each candidate.\n"
                    + "# 3. Ensure assessment_id matches the current Test.\n";

                let csvContent = "data:text/csv;charset=utf-8,"
                    + instructions
                    + headers.join(",") + "\n"
                    + candidates.map(e => e.join(",")).join("\n");

                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "bulk_evaluation_template.csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },

            handleBulkEvaluationUpload: (input) => {
                if (!input.files || !input.files[0]) return;

                Swal.fire({
                    title: 'Processing File',
                    text: 'Reading candidate data...',
                    timer: 1000,
                    didOpen: () => { Swal.showLoading(); }
                }).then(() => {
                    App.previewBulkEvaluation();
                });
            },

            previewBulkEvaluation: () => {
                const previewContainer = document.getElementById('bulkEvaluationPreview');
                const tbody = document.getElementById('bulkEvaluationTableBody');

                const mockData = [
                    { id: 'C001', name: 'Arjun Sharma', prev: 82, new: 85, status: 'Increase' },
                    { id: 'C002', name: 'Priya Patel', prev: 80, new: 88, status: 'Increase' },
                    { id: 'C003', name: 'Vikram Singh', prev: 80, new: 80, status: 'No Change' },
                    { id: 'C004', name: 'Ananya Iyer', prev: 78, new: 82, status: 'Increase' }
                ];

                tbody.innerHTML = mockData.map(d => `
                <tr class="hover:bg-[#f8fafc]">
                    <td class="px-6 py-3 text-[12px] font-medium text-[#64748b]">${d.id}</td>
                    <td class="px-6 py-3 text-[13px] font-bold text-[#1e293b]">${d.name}</td>
                    <td class="px-6 py-3 text-[13px] text-center text-[#94a3b8]">${d.prev}</td>
                    <td class="px-6 py-3 text-[14px] text-center font-bold text-[#dc2230]">${d.new}</td>
                    <td class="px-6 py-3 text-right">
                        <span class="text-[10px] font-bold uppercase ${d.new > d.prev ? 'text-green-500' : 'text-gray-400'}">
                            ${d.new > d.prev ? `+${d.new - d.prev} Marks Added` : 'No Manual Marks'}
                        </span>
                    </td>
                </tr>
            `).join('');

                previewContainer.classList.remove('hidden');
                previewContainer.scrollIntoView({ behavior: 'smooth' });
            },

            submitBulkEvaluation: () => {
                Swal.fire({
                    title: 'Apply Bulk Marks?',
                    text: "This will update the final scores for all candidates in the list.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Update All',
                    confirmButtonColor: '#dc2230'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire('Success', 'Marks updated successfully for 4 candidates.', 'success');
                        document.getElementById('bulkEvaluationPreview').classList.add('hidden');
                        App.loadCandidateResult(); // Refresh leaderboard
                    }
                });
            },

            renderEvaluatorView: (submissionKey = null) => {
                const list = document.getElementById('pendingEvaluationList');
                if (!list) return;

                const submissions = App.getAllSubmissions().filter(s =>
                    (s.subjective_items || []).some(q => (q.candidate_answer || '').trim() !== '')
                );
                if (!submissions.length) {
                    list.innerHTML = `
                        <div class="py-12 text-center bg-gray-50/30 rounded-[10px] border border-dashed border-[#e2e8f0]">
                            <h5 class="text-sm font-bold text-[#1e293b]">No subjective evaluation required</h5>
                            <p class="text-[11px] text-[#94a3b8] uppercase tracking-wider font-bold">Descriptive answers will appear here automatically after submission</p>
                        </div>
                    `;
                    return;
                }

                const key = submissionKey || App.activeEvaluatorSubmissionKey || submissions[0].key;
                App.activeEvaluatorSubmissionKey = key;

                const submission = App.evaluationState.submissions[key];
                if (!submission) return;

                const subjectiveItemsAll = submission.subjective_items || [];
                const subjectiveItems = subjectiveItemsAll.filter(q => (q.candidate_answer || '').trim() !== '');
                const pending = subjectiveItems.filter(q => !q.graded);
                const awarded = subjectiveItems.reduce((sum, q) => sum + (q.awarded_marks || 0), 0);
                const subjectiveMaxPoints = subjectiveItems.reduce((s, q) => s + (parseInt(q.marks, 10) || 0), 0);

                const evalAttachment = submission?.attachment || (Array.isArray(submission?.attachments) ? submission.attachments[0] : null);
                const attachmentBlock = evalAttachment?.data_url
                    ? `
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest mb-1">Attachment</p>
                                <p class="text-[12px] font-black mb-0"><a href="${evalAttachment.data_url}" target="_blank" class="text-indigo-600 hover:text-indigo-700" onclick="event.stopPropagation()">View</a> · <a href="${evalAttachment.data_url}" download="${evalAttachment.name || 'attachment'}" class="text-emerald-600 hover:text-emerald-700" onclick="event.stopPropagation()">Download</a></p>
                            </div>
                    `
                    : '';

                const headerCard = `
                    <div class="px-1 py-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 ${evalAttachment?.data_url ? 'xl:grid-cols-4' : 'xl:grid-cols-3'} gap-4 items-start">
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest mb-1">Candidate</p>
                                <p class="text-[14px] font-bold text-[#1e293b] mb-0">${submission.candidate_name}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest mb-1">MCQ Auto Score</p>
                                <p class="text-[14px] font-black text-[#2563eb] mb-0">${submission.mcq_score} / ${submission.mcq_total}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest mb-1">Descriptive question graded</p>
                                <p class="text-[14px] font-black text-[#dc2230] mb-0">${awarded} / ${subjectiveMaxPoints}</p>
                            </div>
                            ${attachmentBlock}
                        </div>
                    </div>
                `;

                if (!pending.length) {
                    const saveEvalBtnBlock = submission.evaluation_saved ? '' : `
                        <div class="flex justify-end">
                            <button class="bg-[#dc2230] hover:bg-[#c61e2b] text-white px-6 py-2 rounded-[6px] font-bold text-[11px] uppercase tracking-widest transition-all shadow-sm" onclick="App.saveFinalEvaluation('${submission.key}')">
                                Save Evaluation
                            </button>
                        </div>
                    `;
                    list.innerHTML = `
                        ${headerCard}
                        ${saveEvalBtnBlock}
                        <div class="py-12 text-center bg-gray-50/30 rounded-[10px] border border-dashed border-[#e2e8f0]">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-[#f0fdf4] text-[#16a34a] rounded-full mb-3">
                                <i class="bi bi-patch-check-fill text-2xl"></i>
                            </div>
                            <h5 class="text-sm font-bold text-[#1e293b]">Evaluation Complete</h5>
                            <p class="text-[11px] text-[#94a3b8] uppercase tracking-wider font-bold">All subjective answers are graded</p>
                        </div>
                    `;
                    return;
                }

                list.innerHTML = headerCard + `
                    <div class="bg-white border border-[#e2e8f0] rounded-[10px] overflow-hidden shadow-sm">
                        <div class="grid grid-cols-12 gap-2 px-4 py-2.5 bg-[#f8fafc] border-b border-[#f1f5f9]">
                            <div class="col-span-4 text-[9px] font-black text-[#94a3b8] uppercase tracking-widest">Question</div>
                            <div class="col-span-6 text-[9px] font-black text-[#94a3b8] uppercase tracking-widest">Candidate Answer</div>
                            <div class="col-span-2 text-[9px] font-black text-[#94a3b8] uppercase tracking-widest text-right">Marks</div>
                        </div>
                        ${subjectiveItems.map(q => `
                            <div class="grid grid-cols-12 gap-2 px-4 py-2.5 border-b border-[#f8fafc] last:border-0 items-start">
                                <div class="col-span-4 pr-2">
                                    <div class="flex items-start gap-2">
                                        <span class="bg-[#eff6ff] text-[#2563eb] px-1.5 py-0.5 rounded-[3px] text-[10px] font-bold shrink-0">Q${q.index + 1}</span>
                                        <div>
                                            <p class="text-[12px] font-bold text-[#1e293b] mb-1 leading-snug">${q.text}</p>
                                            <span class="${q.graded ? 'bg-[#f0fdf4] text-[#16a34a] border-[#bbf7d0]' : 'bg-[#fefce8] text-[#a16207] border-[#fef08a]'} inline-flex px-2 py-0.5 rounded-[4px] text-[9px] font-bold border uppercase tracking-wider">${q.graded ? 'Evaluated' : 'Pending'}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-6">
                                    <div class="h-[72px] overflow-y-auto bg-[#f8fafc] border border-[#e2e8f0] rounded-[6px] px-3 py-2">
                                        <p class="text-[12px] text-[#475569] italic mb-0 whitespace-pre-wrap leading-relaxed">${q.candidate_answer || 'No answer submitted.'}</p>
                                    </div>
                                </div>
                                <div class="col-span-2 flex items-start justify-end">
                                    <div class="flex items-center gap-1.5">
                                        <input type="number" id="manual_mark_${submission.key}_${q.id}" class="w-[54px] h-[30px] border border-[#cbd5e1] rounded-[4px] text-center font-bold text-[12px] outline-none focus:border-[#dc2230]" value="${q.awarded_marks ?? 0}" max="${q.marks}" min="0" ${q.graded ? 'disabled' : ''}>
                                        <span class="text-[#2563eb] font-bold text-[11px]">/ ${q.marks}</span>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="flex justify-end mt-3">
                        <button class="bg-[#dc2230] hover:bg-[#c61e2b] text-white px-6 py-2 rounded-[6px] font-bold text-[11px] uppercase tracking-widest transition-all shadow-sm"
                            onclick="App.submitAllManualGrades('${submission.key}')">
                            Submit Grade
                        </button>
                    </div>
                `;
            },

            submitAllManualGrades: (submissionKey) => {
                const submission = App.evaluationState.submissions[submissionKey];
                if (!submission) return;

                const subjectiveItems = (submission.subjective_items || []).filter(q => (q.candidate_answer || '').trim() !== '');
                if (!subjectiveItems.length) {
                    Swal.fire('Nothing to grade', 'No answered descriptive questions for grading.', 'info');
                    return;
                }

                for (const item of subjectiveItems) {
                    const input = document.getElementById(`manual_mark_${submissionKey}_${item.id}`);
                    if (!input) continue;
                    const maxMarks = parseInt(item.marks || 0, 10) || 0;
                    const parsed = parseInt(input.value || '', 10);
                    if (Number.isNaN(parsed)) {
                        Swal.fire('Invalid marks', `Please enter marks for Q${(item.index || 0) + 1}.`, 'warning');
                        return;
                    }
                    const safeVal = Math.max(0, Math.min(parsed, maxMarks));
                    item.awarded_marks = safeVal;
                    item.graded = true;
                }

                submission.evaluation_saved = false;
                submission.final_score = (submission.mcq_score || 0) + subjectiveItems.reduce((sum, q) => sum + (q.awarded_marks || 0), 0);
                App.saveEvaluationState();
                App.saveFinalEvaluation(submissionKey);
            },

            enableManualGradeEdit: (submissionKey, questionId) => {
                const submission = App.evaluationState.submissions[submissionKey];
                if (!submission) return;
                const idx = (submission.subjective_items || []).findIndex(q => String(q.id) === String(questionId));
                if (idx === -1) return;

                submission.subjective_items[idx].graded = false;
                submission.evaluation_saved = false;
                App.saveEvaluationState();
                App.renderEvaluatorView(submissionKey);
            },

            submitManualGrade: (submissionKey, questionId, maxMarks) => {
                const input = document.getElementById(`manual_mark_${submissionKey}_${questionId}`);
                if (!input) return;
                const val = Math.max(0, Math.min(parseInt(input.value || '0', 10) || 0, parseInt(maxMarks || 0, 10) || 0));

                const submission = App.evaluationState.submissions[submissionKey];
                if (!submission) return;

                const idx = (submission.subjective_items || []).findIndex(q => String(q.id) === String(questionId));
                if (idx === -1) return;

                submission.subjective_items[idx].awarded_marks = val;
                submission.subjective_items[idx].graded = true;
                submission.evaluation_saved = false;
                submission.final_score = (submission.mcq_score || 0) + (submission.subjective_items || [])
                    .filter(q => (q.candidate_answer || '').trim())
                    .reduce((sum, q) => sum + (q.awarded_marks || 0), 0);

                App.saveEvaluationState();
                App.loadCandidateResult();
                App.renderEvaluatorView(submissionKey);

                Swal.fire({ icon: 'success', title: 'Grade saved', timer: 1000, showConfirmButton: false });
            },

            saveFinalEvaluation: (submissionKey) => {
                const submission = App.evaluationState.submissions[submissionKey];
                if (!submission) return;

                const answeredSub = (submission.subjective_items || []).filter(q => (q.candidate_answer || '').trim());
                const pending = answeredSub.filter(q => !q.graded);
                if (pending.length > 0) {
                    Swal.fire('Pending grading', 'Please submit grades for all answered descriptive questions before saving evaluation.', 'warning');
                    return;
                }

                submission.evaluation_saved = true;
                submission.final_score = (submission.mcq_score || 0) + answeredSub.reduce((sum, q) => sum + (q.awarded_marks || 0), 0);
                App.saveEvaluationState();
                App.loadCandidateResult();
                App.activeEvaluatorSubmissionKey = submissionKey;
                if (typeof window.switchResultView === 'function') {
                    window.switchResultView('student');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Evaluation Saved',
                    text: 'Final score is updated in student score table.',
                    timer: 1200,
                    showConfirmButton: false
                });
            },

            // --- Assign Questions Logic ---
            switchAssignTab: (tabId) => {
                document.querySelectorAll('.assign-panel').forEach(p => p.classList.add('hidden'));
                document.getElementById(tabId).classList.remove('hidden');

                document.querySelectorAll('#btn-assign-mcq, #btn-assign-2m').forEach(b => {
                    b.classList.remove('tab-active');
                    b.classList.add('tab-idle');
                });

                const activeBtnId = tabId === 'assign-mcq' ? 'btn-assign-mcq' : 'btn-assign-2m';
                const activeBtn = document.getElementById(activeBtnId);
                activeBtn.classList.remove('tab-idle');
                activeBtn.classList.add('tab-active');
            },

            addManualAssignQuestion: async (type) => {
                let data = {
                    test_pack_id: document.querySelector('.assign_tp_id_input').value,
                    type: type === 'MCQ' ? 'MCQ' : 'descriptive'
                };

                if (type === 'MCQ') {
                    data.content = document.getElementById('mcq_content').value;
                    data.option_a = document.getElementById('mcq_opt_a').value;
                    data.option_b = document.getElementById('mcq_opt_b').value;
                    data.option_c = document.getElementById('mcq_opt_c').value;
                    data.option_d = document.getElementById('mcq_opt_d').value;
                    data.correct_answer = document.getElementById('mcq_correct').value;
                    data.marks = 1;
                    data.pedagogy = getPedagogyComboValue('assign_mcq_pedagogy');

                    if (!data.content || !data.option_a || !data.option_b || !data.correct_answer) {
                        Swal.fire('Incomplete Data', 'Please fill in the question, at least 2 options, and the correct answer.', 'warning');
                        return;
                    }
                } else {
                    data.content = document.getElementById('m2_content').value;
                    data.correct_answer = document.getElementById('m2_correct').value;
                    data.marks = 2;
                    data.pedagogy = getPedagogyComboValue('assign_m2_pedagogy');

                    if (!data.content || !data.correct_answer) {
                        Swal.fire('Incomplete Data', 'Please fill in both the question and the expected answer.', 'warning');
                        return;
                    }
                }

                try {
                    const response = await fetch('Test/saveQuestion', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        Swal.fire('Added!', 'Question has been added to the Batch.', 'success');
                        if (type === 'MCQ') {
                            document.getElementById('mcq_content').value = '';
                            document.getElementById('mcq_opt_a').value = '';
                            document.getElementById('mcq_opt_b').value = '';
                            document.getElementById('mcq_opt_c').value = '';
                            document.getElementById('mcq_opt_d').value = '';
                            document.getElementById('mcq_correct').value = '';
                            resetPedagogyCombo('assign_mcq_pedagogy');
                        } else {
                            document.getElementById('m2_content').value = '';
                            document.getElementById('m2_correct').value = '';
                            resetPedagogyCombo('assign_m2_pedagogy');
                        }
                    } else {
                        Swal.fire('Error', result.message || 'Failed to add question', 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'An unexpected error occurred.', 'error');
                }
            },

            previewTestPack: async (packId) => {
                try {
                    const response = await fetch(`Test/getPackQuestions/${packId}`);
                    const result = await response.json();

                    if (result.status === 'success') {
                        const { pack, test, template, sections, packQuestions, templateQuestions } = result;
                        const container = document.getElementById('previewPaperContent');

                        // Fallback: Use pack-specific questions if they exist, otherwise use template-based questions
                        let questions = packQuestions && packQuestions.length > 0 ? packQuestions : templateQuestions;

                        // Apply Question Shuffling if enabled
                        const shouldShuffleQuestions = (pack.shuffle_questions == 1 || test.shuffle_questions == 1);
                        const shouldShuffleOptions = (pack.shuffle_options == 1 || test.shuffle_options == 1);

                        if (shouldShuffleQuestions) {
                            questions = App.shuffleArray(questions);
                        }

                        let sectionsHtml = '';

                        // Group questions by section structure
                        sections.forEach((s, sIdx) => {
                            const targetType = App.normalizeType(s.marks_type || s.type || 'MCQ');

                            // Filter questions for this section
                            const sectionQuestions = questions.filter(q => App.normalizeType(q.type) === targetType);

                            if (sectionQuestions.length > 0) {
                                sectionsHtml += `
                                <div class="mb-10 px-8">
                                    <div class="flex items-center justify-between mb-6 pb-2 border-b-2 border-slate-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-red-600 text-white rounded-lg flex items-center justify-center font-black text-sm shadow-sm">${sIdx + 1}</div>
                                            <h3 class="text-[14px] font-black text-slate-800 uppercase tracking-widest mb-0">${s.section_name || s.name || (targetType === 'mcq' ? 'Multiple Choice' : 'Descriptive question')}</h3>
                                        </div>
                                        <div class="px-3 py-1 bg-slate-50 border border-slate-100 rounded-full text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                            ${sectionQuestions.length} Questions • ${s.marks_per_question || s.marks || 1} Marks Each
                                        </div>
                                    </div>
                                    <div class="space-y-6">
                                `;

                                sectionQuestions.forEach((q, qIdx) => {
                                    const isMCQ = App.normalizeType(q.type) === 'mcq';

                                    // Handle Option Shuffling
                                    let optionsToRender = [];
                                    if (isMCQ) {
                                        if (shouldShuffleOptions) {
                                            optionsToRender = App.shuffleOptions(q);
                                        } else {
                                            optionsToRender = ['a', 'b', 'c', 'd'].map(opt => ({
                                                key: opt.toUpperCase(),
                                                value: q['option_' + opt]
                                            })).filter(o => o.value && o.value.trim() !== '');
                                        }
                                    }

                                    sectionsHtml += `
                                    <div class="relative pl-10">
                                        <div class="absolute left-0 top-0 w-8 h-8 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center text-[11px] font-black border border-slate-100">${qIdx + 1}</div>
                                        <div class="flex justify-between items-start mb-4 gap-2">
                                            <p class="text-[14px] font-bold text-slate-800 leading-relaxed mb-0 pt-1">${q.question || q.content || 'No question text'}</p>
                                            <div class="flex flex-col items-end gap-1 shrink-0 ml-4">
                                            ${(q.pedagogy || q.knowledge_type) ? `<span class="px-2 py-0.5 bg-indigo-50 border border-indigo-100 rounded text-[8px] font-black text-indigo-600 uppercase tracking-tighter">${escapeHtml(String(q.pedagogy || q.knowledge_type))}</span>` : ''}
                                            <span class="px-2 py-1 bg-white border border-slate-100 rounded text-[9px] font-black text-slate-400 uppercase tracking-tighter">${q.marks || 1} MARK</span>
                                            </div>
                                        </div>
                                        
                                        ${isMCQ ? `
                                            <div class="grid grid-cols-2 gap-3 mb-4">
                                                ${optionsToRender.map(opt => `
                                                    <div class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-50 bg-slate-50/30">
                                                        <div class="w-6 h-6 rounded bg-slate-200 text-slate-500 flex items-center justify-center text-[10px] font-black uppercase">${opt.key}</div>
                                                        <span class="text-[12px] text-slate-600 font-medium">${opt.value}</span>
                                                    </div>
                                                `).join('')}
                                            </div>
                                        ` : `
                                            <div class="p-4 bg-blue-50/30 border border-blue-100 rounded-2xl mb-4">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Expected Answer / Reference</span>
                                                </div>
                                                <p class="text-[12px] text-slate-600 font-medium italic mb-0 leading-relaxed">${q.correct_answer || q.expected_answer || 'No reference answer provided.'}</p>
                                            </div>
                                        `}
                                    </div>
                                    `;
                                });

                                sectionsHtml += `</div></div>`;
                            }
                        });

                        container.innerHTML = `
                        <div class="bg-white mx-auto shadow-2xl overflow-hidden" style="max-width: 900px; border-radius: 24px; margin-top: 20px; margin-bottom: 40px; border: 1px solid #f1f5f9;">
                            <!-- Exam Header -->
                            <div class="p-10 border-b border-slate-100 bg-slate-50/50">
                                <div class="flex items-center justify-between mb-8">
                                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center border border-slate-100">
                                        <img src="https://via.placeholder.com/60" class="rounded-lg grayscale opacity-50">
                                    </div>
                                    <div class="text-right">
                                        <div class="px-3 py-1 bg-red-600 text-white rounded-full text-[10px] font-black uppercase tracking-widest inline-block mb-2 shadow-lg shadow-red-100">Official Question Paper</div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Code: ${template.code || 'BCH-' + pack.id}</div>
                                    </div>
                                </div>
                                
                                <div class="text-center mb-8">
                                    <h1 class="text-3xl font-black text-slate-800 mb-2 tracking-tight">${template.paper_title || template.name}</h1>
                                    <p class="text-slate-400 font-bold uppercase tracking-widest text-[11px]">${pack.pack_name}</p>
                                </div>

                                <div class="grid grid-cols-3 gap-6">
                                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                        <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Duration</div>
                                        <div class="text-[15px] font-black text-slate-800">${pack.duration} Minutes</div>
                                    </div>
                                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm text-center">
                                        <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Marks</div>
                                        <div class="text-[15px] font-black text-slate-800">${template.total_marks || 0} Marks</div>
                                    </div>
                                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm text-right">
                                        <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Questions</div>
                                        <div class="text-[15px] font-black text-slate-800">${questions.length} Total</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Student Info Section -->
                            <div class="p-8 bg-white border-b border-slate-50">
                                <div class="grid grid-cols-2 gap-8 border-2 border-dashed border-slate-100 rounded-3xl p-6">
                                    <div class="space-y-4">
                                        <div class="flex items-end gap-3">
                                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap mb-1">Name:</span>
                                            <div class="flex-1 border-b border-slate-200 h-6"></div>
                                        </div>
                                        <div class="flex items-end gap-3">
                                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap mb-1">Roll No:</span>
                                            <div class="flex-1 border-b border-slate-200 h-6"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="flex items-end gap-3">
                                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap mb-1">Date:</span>
                                            <div class="flex-1 border-b border-slate-200 h-6"></div>
                                        </div>
                                        <div class="flex items-end gap-3">
                                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap mb-1">Signature:</span>
                                            <div class="flex-1 border-b border-slate-200 h-6"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Instructions -->
                            <div class="p-8">
                                <div class="bg-slate-50 rounded-2xl p-6 mb-8">
                                    <div class="flex items-center gap-2 mb-4">
                                        <i class="bi bi-info-circle-fill text-slate-800 text-sm"></i>
                                        <h4 class="text-[12px] font-black text-slate-800 uppercase tracking-widest mb-0">General Instructions</h4>
                                    </div>
                                    <div class="text-[12px] text-slate-500 font-medium leading-relaxed space-y-2">
                                        ${pack.instructions ? pack.instructions.split('\n').map(line => `<p class="mb-0">• ${line}</p>`).join('') : '<p class="mb-0">• Ensure a stable internet connection if applicable.</p><p class="mb-0">• Read all questions carefully before answering.</p>'}
                                    </div>
                                </div>

                                <!-- Questions Content -->
                                <div class="space-y-2">${sectionsHtml || '<div class="text-center py-20 text-slate-400 italic">No questions have been added to this Batch yet.</div>'}</div>
                            </div>

                            <!-- Footer -->
                            <div class="p-10 bg-slate-50/50 border-t border-slate-100 text-center">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">End of Question Paper</p>
                                <p class="text-[9px] text-slate-300 font-bold tracking-widest mb-0">© 2026 eNova Technology Solutions • System Generated Preview</p>
                            </div>
                        </div>
                        `;

                        const modal = new bootstrap.Modal(document.getElementById('paperPreviewModal'));
                        modal.show();
                    } else {
                        Swal.fire('Error', 'Could not load questions for preview.', 'error');
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire('Error', 'An error occurred while generating the preview.', 'error');
                }
            }
        });

        function switchResultView(view) {
            const btnStudent = document.getElementById('btn-view-student');
            const btnEvaluator = document.getElementById('btn-view-evaluator');
            const viewStudent = document.getElementById('result-student-view');
            const viewEvaluator = document.getElementById('result-evaluator-view');
            const overviewCards = document.getElementById('resultsOverviewCards');

            if (view === 'student') {
                if (btnStudent) btnStudent.className = 'tab tab-active';
                if (btnEvaluator) btnEvaluator.className = 'tab tab-idle';
                if (viewStudent) viewStudent.classList.remove('hidden');
                if (viewEvaluator) viewEvaluator.classList.add('hidden');
                if (overviewCards) overviewCards.classList.remove('hidden');
                App.loadCandidateResult();
            } else {
                if (btnStudent) btnStudent.className = 'tab tab-idle';
                if (btnEvaluator) btnEvaluator.className = 'tab tab-active';
                if (viewStudent) viewStudent.classList.add('hidden');
                if (viewEvaluator) viewEvaluator.classList.remove('hidden');
                if (overviewCards) overviewCards.classList.add('hidden');
                App.loadCandidateResult();
                App.renderEvaluatorView();
            }
        }

        // Proctoring: Tab/Window Switch Detection
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden' && App.executionState.active) {
                App.simulateViolation();
            }
        });

        window.addEventListener('blur', () => {
            if (App.executionState.active) {
                App.simulateViolation();
            }
        });

        window.backFromResultsPage = () => {
            const viewEvaluator = document.getElementById('result-evaluator-view');
            const inEvaluator = viewEvaluator && !viewEvaluator.classList.contains('hidden');
            if (inEvaluator && typeof switchResultView === 'function') {
                switchResultView('student');
                return;
            }
            if (typeof window.switchMainTab === 'function') {
                window.switchMainTab('management');
            }
        };

        window.switchMainTab = switchMainTab;
        window.initTestsDataTable = initTestsDataTable;
        window.inlineSectionTypeDisplayName = inlineSectionTypeDisplayName;
    </script>

    <!-- MODAL: QUICK BATCH CREATION (Full Screen Template) -->
    <div class="modal fade quick-mode" id="createPackModal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
                <div class="modal-content border-0 h-full flex flex-col overflow-hidden relative">
                    <!-- Quick Mode Header -->
                    <div id="quick-mode-header"
                        class="px-8 py-3 bg-white border-b sticky top-0 z-50 flex items-center justify-between shadow-sm flex-shrink-0">
                        <div class="flex items-center gap-4">
                            <div class="w-9 h-9 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-100">
                                <i class="bi bi-lightning-charge-fill text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-800 leading-tight">Quick Test Setup</h3>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5">
                                    Initialize evaluation in seconds</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Navigation Controls -->
                            
                            <button id="wizard_global_create_btn" class="px-5 py-2.5 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg shadow-red-100 flex items-center gap-2" onclick="openTemplateBuilderInline()">
                                <i class="bi bi-plus-lg"></i> Create New Template
                            </button>

                            <div class="w-px h-6 bg-slate-100 mx-1" id="wizard_header_divider"></div>

                            <button class="px-6 py-2.5 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg shadow-red-100 flex items-center gap-2"
                                onclick="closeQuickSetup()"><i class="bi bi-arrow-left text-[14px] font-black leading-none"></i>Back</button>
                        </div>
                    </div>

                    <div class="modal-body p-0 bg-[#f8fafc] overflow-hidden flex flex-col flex-1 min-h-0">
                        <div class="flex flex-1 overflow-hidden min-h-0 h-full">
                            <!-- 1. LEFT SIDEBAR: Discovery -->
                            <div class="w-[360px] bg-white border-e flex flex-col overflow-hidden"
                                id="wizardDiscoverySidebar">
                                <div class="flex-1 overflow-y-auto">
                                    <div class="p-4 pb-0">
                                    <div class="flex items-center gap-2.5 mb-5">
                                        <i class="bi bi-stack text-red-600 text-lg"></i>
                                        <div>
                                            <h4 class="text-[13px] font-black text-slate-800 mb-0">Template Builder</h4>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">
                                                Manage test
                                                structures</p>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h5 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                            Discovery</h5>
                                         <div class="relative">
                                            <input type="text"
                                                class="w-full bg-slate-50 border-0 rounded-xl px-4 py-2.5 text-[11px] font-medium focus:ring-2 focus:ring-red-100 transition-all"
                                                placeholder="Search templates...">
                                            <i
                                                class="bi bi-search absolute right-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                        </div>
                                    </div>

                                    <div class="flex-1 space-y-2 mb-6" id="templateDiscoveryList">
                                        <?php foreach ($templates as $t): ?>
                                        <div class="p-2.5 rounded-xl border border-slate-50 bg-white hover:border-red-200 cursor-pointer transition-all group relative template-card"
                                            onclick="selectTemplate('<?= $t['id'] ?>')" id="temp_card_<?= $t['id'] ?>">
                                            <div class="flex items-start gap-2.5">
                                                <div
                                                    class="w-8 h-8 bg-white border border-slate-100 rounded-lg flex items-center justify-center text-red-500 group-hover:text-red-600 group-hover:bg-red-50 group-hover:border-red-100 shadow-sm transition-all flex-shrink-0">
                                                    <i class="bi bi-file-earmark-text text-base"></i>
                                                </div>
                                                <div class="overflow-hidden">
                                                    <h5
                                                        class="text-[12px] font-bold text-slate-800 mb-0 leading-tight truncate">
                                                        <?= esc($t['name']) ?>
                                                    </h5>
                                                    <?php
                                                    $secs = is_array($t['sections']) ? $t['sections'] : (json_decode($t['sections'], true) ?: []);
                                                    $tm = 0;
                                                    foreach ($secs as $s) {
                                                        $tm += (($s['num_questions'] ?? $s['count'] ?? 0) * ($s['marks_per_question'] ?? $s['marks'] ?? 0));
                                                    }
                                                    ?>
                                                    <span
                                                        class="text-[9px] text-slate-400 font-bold uppercase tracking-wider"><?= esc($t['category'] ?? 'General') ?>
                                                        • <?= $tm ?> Marks • <?= count($secs) ?> Sec</span>
                                                </div>

                                                <!-- Actions overlay -->
                                                <div class="template-card-actions">
                                                    <button class="action-icon-btn btn-clone"
                                                        onclick="cloneTemplate(<?= $t['id'] ?>, event)"
                                                        title="Clone Template">
                                                        <i class="bi bi-copy"></i>
                                                    </button>
                                                    <button class="action-icon-btn btn-edit"
                                                        onclick="editTemplate(<?= $t['id'] ?>, event)"
                                                        title="Edit Template">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="action-icon-btn btn-delete"
                                                        onclick="deleteTemplate(<?= $t['id'] ?>, event)"
                                                        title="Delete Template">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>

                                                <div
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity check-badge">
                                                    <div
                                                        class="w-4 h-4 bg-red-600 rounded-full flex items-center justify-center text-white text-[8px]">
                                                        <i class="bi bi-check-lg"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>

                                </div>
                            </div>

                            <!-- Sidebar Footer Removed -->
                        </div>

                        <!-- 2. MAIN CONTENT -->
                        <div class="flex-1 overflow-y-auto px-[5%] py-8 min-h-0 h-full" id="wizardMainColumn">
                            <!-- Quick Mode Template Selector (Removed from here) -->

                            <!-- BATCH CONFIG VIEW -->
                            <div class="w-full space-y-6" id="batchWizardConfigView">
                                <!-- TOP ROW: Template (70%) & Target Audience (30%) -->
                                <div class="grid grid-cols-10 gap-4 items-start">
                                    <!-- 1. TEMPLATE DETAILS SECTION (70%) -->
                                    <div class="col-span-7 card border border-slate-200 shadow-sm rounded-2xl overflow-hidden bg-white"
                                        id="wizard_template_section">
                                        <div class="p-5">
                                            <div class="flex items-center justify-between mb-4 gap-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center shadow-sm">
                                                        <i class="bi bi-layout-text-window-reverse text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h4
                                                            class="text-[11px] font-black text-slate-800 uppercase tracking-widest mb-0">
                                                            Template & Structure</h4>
                                                        <p class="text-[9px] text-slate-400 font-medium mb-0">Current
                                                            question paper framework</p>
                                                    </div>
                                                </div>

                                                <!-- Template Meta Info (Created Date & Usage) -->
                                                <div id="active_template_meta" class="hidden flex-1 flex items-center justify-end gap-2 min-w-0">
                                                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-100 rounded-xl px-3 py-1.5 shadow-sm">
                                                        <i class="bi bi-calendar2-event text-red-500 text-[11px]"></i>
                                                        <div class="leading-tight">
                                                            <span class="block text-[7px] font-black text-slate-400 uppercase tracking-widest leading-none">Created</span>
                                                            <span id="active_template_created_date" class="block text-[10px] font-black text-slate-700 leading-tight">--</span>
                                                        </div>
                                                    </div>
                                                    <div class="relative group">
                                                        <button type="button" id="active_template_usage_btn" class="flex items-center gap-2 bg-slate-50 border border-slate-100 rounded-xl px-3 py-1.5 shadow-sm hover:border-red-200 transition-all max-w-[260px]">
                                                            <i class="bi bi-link-45deg text-red-500 text-[12px]"></i>
                                                            <div class="leading-tight text-left min-w-0">
                                                                <span class="block text-[7px] font-black text-slate-400 uppercase tracking-widest leading-none">Used In</span>
                                                                <span id="active_template_usage_summary" class="block text-[10px] font-black text-slate-700 leading-tight truncate">Not used yet</span>
                                                            </div>
                                                            <i class="bi bi-chevron-down text-slate-400 text-[9px]"></i>
                                                        </button>
                                                        <div id="active_template_usage_dropdown" class="hidden absolute right-0 top-full mt-1 w-72 max-h-64 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-2">
                                                            <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest px-2 py-1 border-b border-slate-100 mb-1">Template Usage</div>
                                                            <div id="active_template_usage_list" class="space-y-1"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-2 shrink-0">
                                                    <button id="active_template_delete_btn"
                                                        class="hidden px-3 py-2 bg-red-50 text-red-600 border border-red-100 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all shadow-sm"
                                                        onclick="deleteActiveTemplate()" title="Delete Template">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    <button id="wizard_edit_structure_btn"
                                                        class="px-4 py-2 bg-slate-50 text-slate-600 border border-slate-100 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-red-600 hover:text-white hover:border-red-600 transition-all shadow-sm"
                                                        onclick="openTemplateBuilder()">
                                                        <i class="bi bi-pencil-square me-2"></i> Edit Structure
                                                    </button>
                                                </div>
                                            </div>

                                            <div id="template_details_placeholder"
                                                class="py-6 text-center border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/40">
                                                <div
                                                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center mx-auto mb-2 text-slate-300 shadow-sm">
                                                    <i class="bi bi-info-circle text-lg"></i>
                                                </div>
                                                <h5 class="text-[12px] font-bold text-slate-500 mb-1">No Template
                                                    Selected</h5>
                                                <p class="text-[10px] text-slate-400">Choose a template from the
                                                    discovery sidebar to begin</p>
                                            </div>

                                            <div id="template_details_active" class="hidden animate-fadeIn">
                                                <div class="flex flex-col gap-4">
                                                    <!-- Row 1: Template Name (Readonly) & Marks/Sections -->
                                                    <div
                                                        class="flex flex-col lg:flex-row items-center justify-between gap-4">
                                                        <div class="flex-1 w-full">
                                                            <div class="flex items-center gap-2 mb-2">
                                                                <div class="w-1 h-4 bg-slate-300 rounded-full"></div>
                                                                <label
                                                                    class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Active
                                                                    Template</label>
                                                            </div>
                                                            <input id="active_template_name"
                                                                class="w-full bg-slate-50 border border-slate-200 rounded-xl text-[11px] font-black h-10 px-4 text-slate-700 focus:ring-0 transition-all opacity-95"
                                                                readonly value="--" />
                                                        </div>

                                                        <div
                                                            class="flex items-center gap-4 bg-slate-50 p-2 rounded-xl border border-slate-200 h-10 self-end mb-0.5">
                                                            <div class="px-3">
                                                                <span
                                                                    class="block text-[8px] font-bold text-slate-400 uppercase leading-none mb-1">Total
                                                                    Marks</span>
                                                                <input id="active_template_marks_input"
                                                                    class="bg-transparent border-0 p-0 text-[12px] font-black text-slate-800 leading-none w-16 focus:ring-0"
                                                                    readonly value="0 Marks">
                                                            </div>
                                                            <div class="w-px h-6 bg-slate-200"></div>
                                                            <div class="px-3">
                                                                <span
                                                                    class="block text-[8px] font-bold text-slate-400 uppercase leading-none mb-1">Structure</span>
                                                                <input id="active_template_sections_input"
                                                                    class="bg-transparent border-0 p-0 text-[12px] font-black text-slate-800 leading-none w-20 focus:ring-0"
                                                                    readonly value="0 Sections">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="active_template_tags" class="mt-4 space-y-2"></div>
                                            <div id="active_template_questions" class="mt-4 space-y-3"></div>

                                            <!-- Quick Mode: Question Bank Selector -->
                                            <div id="quick-qb-selector-section"
                                                class="mt-4 pt-4 border-t border-slate-100 hidden">
                                                <div class="flex items-center gap-4 mb-3">
                                                    <div
                                                        class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                                                        <i class="bi bi-database-fill text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h4
                                                            class="text-[11px] font-black text-slate-800 uppercase tracking-widest mb-0">
                                                            Select Question Bank</h4>
                                                        <p class="text-[8px] text-slate-400 font-bold uppercase mb-0">
                                                            Choose repository</p>
                                                    </div>
                                                </div>
                                                <select id="quick_qb_select"
                                                    onchange="handleQuickQuestionBankChange(this.value)"
                                                    class="w-full bg-slate-50 border border-slate-100 rounded-xl h-10 px-4 text-[11px] font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all">
                                                    <option value="" disabled selected>-- Select a Question Bank --</option>
                                                </select>

                                                <div id="quick_qb_action_buttons" class="mt-4 flex items-center gap-3 hidden">
                                                    <button onclick="generateQuickQuestionPaper(true)" class="px-5 py-2 bg-indigo-600 text-white font-black rounded-xl text-[9px] uppercase tracking-widest shadow-lg shadow-indigo-100 transition-all hover:bg-indigo-700 flex items-center gap-2">
                                                        <i class="bi bi-gear-wide-connected"></i> QP Generation
                                                    </button>
                                                    <button onclick="savePackFromWizard()" class="px-5 py-2 bg-red-600 text-white font-black rounded-xl text-[9px] uppercase tracking-widest shadow-lg shadow-red-100 transition-all hover:bg-red-700 flex items-center gap-2">
                                                        <i class="bi bi-check-lg"></i> Save Template
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. CANDIDATE ASSIGNMENT & SETTINGS (30%) -->
                                    <div id="wizard_audience_section"
                                        class="col-span-3 card border-0 shadow-sm rounded-2xl overflow-hidden bg-white h-full">
                                        <div class="p-4 h-full flex flex-col gap-6">
                                            <!-- Batch Settings (Name & Duration) -->
                                            <div id="quick_mode_batch_settings" class="space-y-4">
                                                <div>
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <div class="w-1 h-4 bg-red-500 rounded-full"></div>
                                                        <label
                                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Batch
                                                            Name <span class="text-red-500">*</span></label>
                                                    </div>
                                                    <input id="quick_batch_name"
                                                        oninput="document.getElementById('pack_wizard_name').value = this.value; updateSummary();"
                                                        class="w-full bg-slate-50 border border-slate-100 rounded-xl text-[11px] font-bold h-10 px-4 text-slate-700 focus:ring-4 focus:ring-red-50 transition-all shadow-sm"
                                                        placeholder="Enter batch name..." />
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <div class="w-1 h-4 bg-red-500 rounded-full"></div>
                                                        <label
                                                            class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Duration
                                                            (Mins) <span class="text-red-500">*</span></label>
                                                    </div>
                                                    <input type="number" id="quick_batch_duration" value="60"
                                                        oninput="document.getElementById('pack_duration').value = this.value; updateSummary();"
                                                        class="w-full bg-slate-50 border border-slate-100 rounded-xl text-[11px] font-bold h-10 px-4 text-slate-700 focus:ring-4 focus:ring-red-50 transition-all shadow-sm" />
                                                </div>
                                            </div>
                                            <div class="border-t border-slate-100 pt-6 hidden">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shadow-sm">
                                                            <i class="bi bi-people-fill text-base"></i>
                                                        </div>
                                                        <div>
                                                            <h4
                                                                class="text-[12px] font-black text-slate-800 uppercase tracking-widest mb-0">
                                                                Audience</h4>
                                                        </div>
                                                    </div>
                                                    <button
                                                        class="w-8 h-8 bg-white border border-slate-200 text-slate-600 rounded-lg flex items-center justify-center hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm"
                                                        onclick="openCandidatePickerForWizard()"
                                                        title="Select Participants">
                                                        <i class="bi bi-person-plus-fill"></i>
                                                    </button>
                                                </div>

                                                <div id="wizard_candidate_summary"
                                                    class="bg-slate-50 rounded-xl p-3 border border-slate-100 flex-1 hidden">
                                                    <div
                                                        class="flex flex-col items-center justify-center h-full text-center py-2">
                                                        <div class="flex items-center justify-center gap-3 mb-2">
                                                            <div
                                                                class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-slate-300 shadow-sm">
                                                                <i class="bi bi-people text-xl"></i>
                                                            </div>
                                                            <div class="text-left">
                                                                <h5 class="text-[12px] font-black text-slate-800 mb-0"
                                                                    id="wizard_selected_count">0 Selected</h5>
                                                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest leading-none mt-1"
                                                                    id="wizard_selected_role">NO ROLE</p>
                                                            </div>
                                                        </div>
                                                        <div id="wizard_candidate_avatars"
                                                            class="flex -space-x-2 justify-center">
                                                            <!-- Avatars here -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- End batchWizardConfigView -->

                                <!-- Quick Mode: Generated Question Paper (Grouped) -->
                                <div id="quick-generated-paper-section" class="space-y-6 hidden">
                                    <!-- Header moved to main header -->
                                        <div class="flex items-center gap-3">
                                            <button
                                                class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2"
                                                onclick="generateQuickQuestionPaper(true)">
                                                <i class="bi bi-shuffle"></i> Re-shuffle
                                            </button>
                                            <button
                                                class="px-8 py-2.5 bg-red-600 text-white font-black rounded-xl text-[10px] uppercase tracking-[0.15em] shadow-[0_8px_15px_-3px_rgba(220,34,48,0.25)] transition-all hover:bg-red-700 hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2"
                                                onclick="savePackFromWizard()">
                                                <i class="bi bi-check-lg"></i> Save Template
                                            </button>
                                        </div>
                                    </div>
                                    <div id="quick_generated_questions_container"
                                        class="space-y-8 bg-slate-50/30 rounded-3xl p-6">
                                        <!-- Populated via JS -->
                                    </div>
                                </div>


                                <!-- HIDDEN MASTER INPUTS -->
                                <div class="hidden">
                                    <input type="text" id="pack_wizard_name" value="">
                                    <select id="pack_user_role">
                                        <option value="General Access">General Access</option>
                                        <option value="Technical">Technical</option>
                                        <option value="Management">Management</option>
                                        <option value="Internal">Internal</option>
                                    </select>
                                    <input type="number" id="pack_duration" value="60">
                                    <input type="date" id="pack_scheduled_date">
                                    <input type="time" id="pack_start_time">
                                    <input type="time" id="pack_end_time">
                                    <textarea
                                        id="pack_instructions">Read all questions carefully before answering. Ensure a stable internet connection.</textarea>
                                    <input type="number" id="pack_pass_mark" value="60">
                                    <input type="number" id="pack_attempts" value="2">
                                    <input type="checkbox" id="pack_shuffle" checked>
                                    <input type="checkbox" id="pack_shuffle_options" checked>
                                    <input type="checkbox" id="pack_proctored" checked>
                                    <input type="checkbox" id="pack_lockdown" checked>
                                    <input type="checkbox" id="pack_show_results" checked>
                                    <input type="checkbox" id="pack_allow_backtracking" checked>
                                </div>

                                <select id="baseTemplateSelect" class="hidden">
                                    <option value="" selected disabled>-- Select Evaluation Template --</option>
                                    <?php foreach ($templates as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= esc($t['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- TEMPLATE BUILDER VIEW (Inline) -->
                            <div class="w-full space-y-4 hidden animate-fadeIn px-[5%] mt-[3%]" id="templateBuilderInlineView">
                                <!-- Header moved to main header -->

                                <div class="space-y-6">
                                <!-- Unified Builder Container -->
                                <div class="card border-0 shadow-sm rounded-3xl bg-white overflow-visible border border-slate-100">
                                                         <div class="p-8 space-y-8">
                                        <!-- Top Configuration Row: shared label height + controls aligned on one baseline -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-5 xl:gap-4 xl:items-end">
                                                <!-- 1. Template Name -->
                                                <div class="xl:col-span-4 flex flex-col min-w-0">
                                                    <div class="flex items-start gap-3 mb-3 min-h-[3.25rem]">
                                                        <div class="w-8 h-8 shrink-0 bg-red-50 text-red-600 rounded-lg flex items-center justify-center shadow-sm border border-red-100 mt-0.5">
                                                            <i class="bi bi-card-text text-sm"></i>
                                                        </div>
                                                        <div class="min-w-0 pt-0.5">
                                                            <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-widest mb-0 leading-tight">Template Name</h4>
                                                            <p class="text-[9px] text-slate-400 font-bold uppercase mb-0 mt-0.5 leading-snug">Define template name</p>
                                                        </div>
                                                    </div>
                                                    <input id="builder_storage_name_inline"
                                                        class="w-full bg-slate-50 border border-slate-100 rounded-xl text-[13px] font-bold h-11 px-4 focus:ring-2 focus:ring-red-100 focus:border-red-400 transition-all text-slate-700 shadow-inner placeholder:text-slate-300"
                                                        placeholder="e.g. Technical Skills 2024" />
                                                </div>

                                            <!-- 2. Select Question Bank -->
                                            <div id="builder_qb_selector_inline" class="xl:col-span-4 flex flex-col min-w-0">
                                                <div class="flex items-start gap-3 mb-3 min-h-[3.25rem]">
                                                    <div class="w-8 h-8 shrink-0 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center shadow-sm border border-indigo-100 mt-0.5">
                                                        <i class="bi bi-database-fill text-sm"></i>
                                                    </div>
                                                    <div class="min-w-0 pt-0.5">
                                                        <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-widest mb-0 leading-tight">Select Question Bank</h4>
                                                        <p class="text-[9px] text-slate-400 font-bold uppercase mb-0 mt-0.5 leading-snug">Choose repository</p>
                                                    </div>
                                                </div>
                                                
                                                <div class="relative group">
                                                    <select id="builder_qb_select_inline" onchange="App.handleQBSelectionInline(this.value)"
                                                        class="w-full bg-slate-50 border border-slate-100 rounded-xl text-[13px] font-bold h-11 px-4 focus:ring-2 focus:ring-indigo-100 focus:border-indigo-200 transition-all text-slate-700 shadow-inner appearance-none cursor-pointer">
                                                        <option value="" selected disabled>-- Select a Question Bank --</option>
                                                        <?php if(!empty($questionBank)): ?>
                                                            <?php foreach ($questionBank as $bank): ?>
                                                                <option value="<?= $bank['id'] ?>"><?= esc($bank['name']) ?></option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 group-hover:text-indigo-600 transition-colors">
                                                        <i class="bi bi-chevron-down text-xs"></i>
                                                    </div>
                                                </div>

                                                <div id="qb_mapping_status" class="mt-2 hidden">
                                                    <div class="flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg border border-emerald-100">
                                                        <i class="bi bi-check-circle-fill text-[9px]"></i>
                                                        <span class="text-[9px] font-black uppercase tracking-widest" id="qb_mapping_text">Bank mapped</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 3. Template Pass Mark -->
                                            <div class="xl:col-span-2 flex flex-col min-w-0">
                                                <div class="flex items-start gap-3 mb-3 min-h-[3.25rem]">
                                                    <div class="w-8 h-8 shrink-0 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center shadow-sm border border-emerald-100 mt-0.5">
                                                        <i class="bi bi-percent text-sm"></i>
                                                    </div>
                                                    <div class="min-w-0 pt-0.5">
                                                        <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-widest mb-0 leading-tight">Pass Mark (%)</h4>
                                                        <p class="text-[9px] text-slate-400 font-bold uppercase mb-0 mt-0.5 leading-snug">Minimum to pass</p>
                                                    </div>
                                                </div>
                                                <input id="builder_pass_mark_visible" type="number" min="1" max="100" value="60"
                                                    oninput="document.getElementById('builder_pass_mark_inline').value = this.value || 60"
                                                    class="w-full bg-slate-50 border border-slate-100 rounded-xl text-[13px] font-bold h-11 px-4 focus:ring-2 focus:ring-emerald-100 focus:border-emerald-300 transition-all text-slate-700 shadow-inner"
                                                    placeholder="e.g. 60" />
                                            </div>

                                            <!-- 4. Total Questions / Total Marks (same control height as inputs) -->
                                            <div class="xl:col-span-2 flex flex-col min-w-0 xl:min-w-[11rem]">
                                                <div class="flex items-start gap-3 mb-3 min-h-[3.25rem]">
                                                    <div class="w-8 h-8 shrink-0 bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center shadow-sm border border-slate-200 mt-0.5">
                                                        <i class="bi bi-calculator text-sm"></i>
                                                    </div>
                                                    <div class="min-w-0 pt-0.5">
                                                        <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-widest mb-0 leading-tight">Totals</h4>
                                                        <p class="text-[9px] text-slate-400 font-bold uppercase mb-0 mt-0.5 leading-snug">From structure</p>
                                                    </div>
                                                </div>
                                                <div class="h-11 w-full flex items-stretch rounded-xl border border-slate-100 bg-slate-50/80 overflow-hidden shadow-inner">
                                                    <div class="flex-1 flex flex-col items-center justify-center px-2 min-w-0">
                                                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none mb-0.5">Questions</p>
                                                        <p class="text-base font-black text-slate-800 leading-none truncate" id="total_questions_display">0</p>
                                                    </div>
                                                    <div class="w-px self-stretch bg-slate-200 my-1.5 shrink-0"></div>
                                                    <div class="flex-1 flex flex-col items-center justify-center px-2 min-w-0">
                                                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none mb-0.5">Marks</p>
                                                        <p class="text-base font-black text-red-600 leading-none truncate" id="total_marks_display">0</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="builder_category_inline" value="General">
                                            <input type="hidden" id="builder_duration_inline" value="60">
                                            <input type="hidden" id="builder_pass_mark_inline" value="60">
                                            <input type="hidden" id="builder_attempts_inline" value="2">
                                        </div>
                                        <hr class="border-slate-100">

                                        <!-- 2. Blueprint Section -->
                                        <section class="w-full">
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-8 h-8 shrink-0 bg-red-50 text-red-600 rounded-lg flex items-center justify-center shadow-sm border border-red-100">
                                                        <i class="bi bi-grid-1x2-fill text-sm"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-widest mb-0 leading-tight">Template Structure</h4>
                                                        <p class="text-[9px] text-slate-400 font-bold uppercase mb-0 mt-0.5 leading-snug">Define sections for this paper</p>
                                                    </div>
                                                </div>
                                                <button type="button" onclick="addNewSectionRowInline()" class="btn-red px-5 py-2.5 shadow-lg shadow-red-100 inline-flex items-center justify-center gap-2 h-11 rounded-xl whitespace-nowrap shrink-0 self-start sm:self-auto">
                                                    <i class="bi bi-plus-circle-fill text-base"></i>
                                                    <span class="text-[11px] font-black uppercase tracking-widest">Add Section</span>
                                                </button>
                                            </div>

                                            <div class="bg-white border border-slate-100 rounded-xl overflow-hidden shadow-sm">
                                                <div class="grid grid-cols-12 gap-0 bg-slate-50/50 border-b border-slate-100 items-center"
                                                    id="inline_builder_header" style="display: none;">
                                                    <div class="col-span-4 py-3 pl-4 sm:pl-14 pr-2 text-[8px] font-black text-slate-400 uppercase tracking-widest">Section / Type</div>
                                                    <div class="col-span-2 py-3 px-2 text-[8px] font-black text-slate-400 uppercase tracking-widest text-center">Questions</div>
                                                    <div class="col-span-2 py-3 px-2 text-[8px] font-black text-slate-400 uppercase tracking-widest text-center">Marks Each</div>
                                                    <div class="col-span-4 py-3 px-3 text-right text-[8px] font-black text-slate-400 uppercase tracking-widest">Actions</div>
                                                </div>
                                                <div id="builder_sections_container_inline" class="divide-y divide-slate-100 flex-1">
                                                    <div class="empty-state py-16 text-center flex flex-col items-center justify-center h-full" id="builder_empty_state_inline">
                                                        <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-4 text-slate-200 border border-slate-100">
                                                            <i class="bi bi-stack text-3xl"></i>
                                                        </div>
                                                        <h5 class="text-[13px] font-black text-slate-700 mb-1">Structure is Empty</h5>
                                                        <p class="text-[10px] text-slate-400 font-medium max-w-[200px] mx-auto">Select a section blueprint above to define your paper structure</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- QP Generation Actions -->
                                            <div id="qb_action_buttons" class="mt-8 flex items-center justify-end gap-4 hidden animate-fadeIn">
                                                <button onclick="generateQuickQuestionPaper(true)" class="px-8 py-3.5 bg-indigo-600 text-white font-black rounded-2xl text-[11px] uppercase tracking-widest shadow-xl shadow-indigo-100 transition-all hover:bg-indigo-700 hover:scale-[1.02] active:scale-95 flex items-center gap-3">
                                                    <i class="bi bi-gear-wide-connected text-lg"></i> 
                                                    <span>QP Generation</span>
                                                </button>
                                                <button onclick="generateQuickQuestionPaper(true)" class="px-8 py-3.5 bg-[#dc2230] text-white font-black rounded-2xl text-[11px] uppercase tracking-widest shadow-xl shadow-red-100 transition-all hover:bg-red-700 hover:scale-[1.02] active:scale-95 flex items-center gap-3">
                                                    <i class="bi bi-shuffle text-lg"></i>
                                                    <span>Re-shuffle</span>
                                                </button>
                                            </div>
                                        </section>

                                        <!-- 3. Questions Preview Section (Builder Mode Only) -->
                                        <section id="builder_questions_section_inline" class="w-full hidden border-t border-slate-50 pt-8 mt-4 animate-fadeIn">
                                            <div class="flex items-center gap-3 mb-6">
                                                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center shadow-sm border border-indigo-100">
                                                    <i class="bi bi-file-earmark-text-fill text-sm"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-widest mb-0">Generated Paper Content</h4>
                                                    <p class="text-[9px] text-slate-400 font-bold uppercase mb-0">Review auto-selected questions from bank</p>
                                                </div>
                                            </div>

                                            <div id="builder_questions_container_inline" class="space-y-6">
                                                <!-- Questions will be dynamically injected here -->
                                            </div>
                                        </section>
                                    </div>

                                    <!-- Action Footer -->
                                    <div id="builder_template_footer" class="px-5 py-2.5 bg-white/95 backdrop-blur border-t border-slate-100 flex justify-end sticky bottom-0 z-40 shadow-[0_-4px_14px_rgba(15,23,42,0.04)]">
                                        <button
                                            class="h-10 px-6 bg-[#dc2230] text-white font-black rounded-lg text-[10px] uppercase tracking-[0.12em] shadow-lg shadow-red-100 transition-all hover:bg-red-700 hover:scale-[1.01] active:scale-95 inline-flex items-center gap-2"
                                            onclick="saveTemplateFromWizard()">
                                            <i class="bi bi-check-lg text-sm"></i> Save Template
                                        </button>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. RIGHT SIDEBAR: Batch Config -->
                        <div class="w-[500px] bg-white border-s flex flex-col overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-white shrink-0">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-100">
                                        <i class="bi bi-speedometer2 text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-[16px] font-black text-slate-800 leading-none">Test Summary</h4>
                                        <p class="text-[12px] text-slate-400 font-medium mt-1">Schedule & Exam
                                            configuration</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto p-3 flex flex-col gap-3 bg-slate-50/20">
                                <div
                                    class="bg-white p-3 rounded-2xl border border-slate-100 shadow-sm space-y-2 shrink-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i class="bi bi-info-circle text-red-600 text-[12px]"></i>
                                        <span class="text-[13px] font-black text-slate-800 uppercase tracking-wider">0.
                                            Basic Information</span>
                                    </div>
                                    <div class="form-group">
                                        <label class="block text-[12px] font-bold text-slate-500 mb-1">Batch
                                            Name</label>
                                        <input type="text" id="summary_name" placeholder="Enter batch name..."
                                            class="w-full bg-slate-50 border border-slate-100 rounded-xl h-10 px-4 text-[14px] font-bold text-slate-700 focus:ring-2 focus:ring-red-100 transition-all"
                                            oninput="syncSidebarToMain('pack_wizard_name', this.value)">
                                    </div>
                                </div>

                                <div
                                    class="bg-white p-3 rounded-2xl border border-slate-100 shadow-sm space-y-2 shrink-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i class="bi bi-calendar2-check text-red-600 text-[12px]"></i>
                                        <span class="text-[13px] font-black text-slate-800 uppercase tracking-wider">1.
                                            Schedule & Duration</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="form-group">
                                            <label class="block text-[12px] font-bold text-slate-500 mb-1">Scheduled
                                                Date</label>
                                            <input type="date" id="summary_date_input"
                                                class="w-full bg-slate-50 border border-slate-100 rounded-xl h-10 px-4 text-[14px] font-bold text-slate-700 focus:ring-2 focus:ring-red-100 transition-all"
                                                onchange="syncSidebarToMain('pack_scheduled_date', this.value)">
                                        </div>
                                        <div class="form-group">
                                            <label class="block text-[12px] font-bold text-slate-500 mb-1">Duration
                                                (mins)</label>
                                            <input type="number" id="summary_duration_input" placeholder="e.g. 90"
                                                class="w-full bg-slate-50 border border-slate-100 rounded-xl h-10 px-4 text-[14px] font-bold text-slate-700 focus:ring-2 focus:ring-red-100 transition-all"
                                                oninput="syncSidebarToMain('pack_duration', this.value)">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="form-group">
                                            <label class="block text-[12px] font-bold text-slate-500 mb-1">Start
                                                Time</label>
                                            <input type="time" id="summary_start_input"
                                                class="w-full bg-slate-50 border border-slate-100 rounded-xl h-10 px-4 text-[14px] font-bold text-slate-700 focus:ring-2 focus:ring-red-100 transition-all"
                                                onchange="syncSidebarToMain('pack_start_time', this.value)">
                                        </div>
                                        <div class="form-group">
                                            <label class="block text-[12px] font-bold text-slate-500 mb-1">End
                                                Time</label>
                                            <input type="time" id="summary_end_input"
                                                class="w-full bg-slate-50 border border-slate-100 rounded-xl h-10 px-4 text-[14px] font-bold text-slate-700 focus:ring-2 focus:ring-red-100 transition-all"
                                                onchange="syncSidebarToMain('pack_end_time', this.value)">
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="bg-white p-3 rounded-2xl border border-slate-100 shadow-sm flex flex-col min-h-[140px] shrink-0">
                                    <div class="flex items-center gap-2 mb-2 shrink-0">
                                        <i class="bi bi-list-task text-red-600 text-[12px]"></i>
                                        <span class="text-[13px] font-black text-slate-800 uppercase tracking-wider">2.
                                            Test Instructions</span>
                                    </div>
                                    <div class="relative flex-1">
                                        <textarea id="summary_instructions_input"
                                            class="w-full h-full bg-slate-50 border border-slate-100 rounded-xl p-3 pb-8 text-[13px] font-medium text-slate-600 focus:ring-2 focus:ring-red-100 transition-all resize-none min-h-[100px]"
                                            placeholder="Enter instructions..."
                                            oninput="syncSidebarToMain('pack_instructions', this.value)"></textarea>
                                        <div class="absolute bottom-2 right-4 text-[10px] font-bold text-slate-300"
                                            id="summary_instructions_count">0 / 2000</div>
                                    </div>
                                </div>

                                <div
                                    class="bg-white p-3 rounded-2xl border border-slate-100 shadow-sm space-y-2 shrink-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i class="bi bi-target text-red-600 text-[12px]"></i>
                                        <span class="text-[13px] font-black text-slate-800 uppercase tracking-wider">3.
                                            Passing Criteria & Attempts</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="form-group">
                                            <label class="block text-[12px] font-bold text-slate-500 mb-1">Pass Mark
                                                (%)</label>
                                            <input type="number" id="summary_pass_mark_input" placeholder="e.g. 50"
                                                class="w-full bg-slate-50 border border-slate-100 rounded-xl h-10 px-4 text-[14px] font-bold text-slate-700 focus:ring-2 focus:ring-red-100 transition-all"
                                                oninput="syncSidebarToMain('pack_pass_mark', this.value)">
                                        </div>
                                        <div class="form-group">
                                            <label class="block text-[12px] font-bold text-slate-500 mb-1">No. of
                                                Attempts</label>
                                            <input type="number" id="summary_attempts_input" placeholder="e.g. 1"
                                                class="w-full bg-slate-50 border border-slate-100 rounded-xl h-10 px-4 text-[14px] font-bold text-slate-700 focus:ring-2 focus:ring-red-100 transition-all"
                                                oninput="syncSidebarToMain('pack_attempts', this.value)">
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="bg-white p-2 rounded-xl border border-slate-100 shadow-sm space-y-1.5 shrink-0">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <i class="bi bi-gear-fill text-red-600 text-[10px]"></i>
                                        <span class="text-[12px] font-bold text-slate-800 uppercase tracking-wider">4.
                                            Exam Configuration</span>
                                    </div>
                                    <div class="divide-y divide-slate-50">
                                        <div class="flex items-center justify-between py-1.5 cursor-pointer group"
                                            onclick="toggleSidebarOption('pack_shuffle')">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-5 h-5 bg-slate-50 text-slate-400 rounded-md flex items-center justify-center group-hover:bg-red-50 group-hover:text-red-600 transition-all text-[10px]">
                                                    <i class="bi bi-shuffle"></i>
                                                </div>
                                                <span class="block text-[12px] font-bold text-slate-700">Shuffle
                                                    Questions</span>
                                            </div>
                                            <div id="summary_shuffle_wrap"
                                                class="relative inline-flex items-center h-3 w-5">
                                                <div
                                                    class="toggle-track w-5 h-3 bg-slate-200 rounded-full transition-colors duration-200 ease-in-out">
                                                </div>
                                                <div
                                                    class="toggle-thumb absolute left-0.5 top-0.5 w-2 h-2 bg-white rounded-full shadow-sm transform transition-transform duration-200 ease-in-out">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between py-1.5 cursor-pointer group"
                                            onclick="toggleSidebarOption('pack_shuffle_options')">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-5 h-5 bg-slate-50 text-slate-400 rounded-md flex items-center justify-center group-hover:bg-red-50 group-hover:text-red-600 transition-all text-[10px]">
                                                    <i class="bi bi-shuffle"></i>
                                                </div>
                                                <span class="block text-[12px] font-bold text-slate-700">Shuffle
                                                    Options</span>
                                            </div>
                                            <div id="summary_shuffle_options_wrap"
                                                class="relative inline-flex items-center h-3 w-5">
                                                <div
                                                    class="toggle-track w-5 h-3 bg-slate-200 rounded-full transition-colors duration-200 ease-in-out">
                                                </div>
                                                <div
                                                    class="toggle-thumb absolute left-0.5 top-0.5 w-2 h-2 bg-white rounded-full shadow-sm transform transition-transform duration-200 ease-in-out">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between py-1.5 cursor-pointer group"
                                            onclick="toggleSidebarOption('pack_show_results')">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-5 h-5 bg-slate-50 text-slate-400 rounded-md flex items-center justify-center group-hover:bg-red-50 group-hover:text-red-600 transition-all text-[10px]">
                                                    <i class="bi bi-eye"></i>
                                                </div>
                                                <span class="block text-[12px] font-bold text-slate-700">Show
                                                    Results</span>
                                            </div>
                                            <div id="summary_show_results_wrap"
                                                class="relative inline-flex items-center h-3 w-5">
                                                <div
                                                    class="toggle-track w-5 h-3 bg-slate-200 rounded-full transition-colors duration-200 ease-in-out">
                                                </div>
                                                <div
                                                    class="toggle-thumb absolute left-0.5 top-0.5 w-2 h-2 bg-white rounded-full shadow-sm transform transition-transform duration-200 ease-in-out">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between py-1.5 cursor-pointer group"
                                            onclick="toggleSidebarOption('pack_allow_backtracking')">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-5 h-5 bg-slate-50 text-slate-400 rounded-md flex items-center justify-center group-hover:bg-red-50 group-hover:text-red-600 transition-all text-[10px]">
                                                    <i class="bi bi-sign-turn-left"></i>
                                                </div>
                                                <span class="block text-[12px] font-bold text-slate-700">Allow
                                                    Backtracking</span>
                                            </div>
                                            <div id="summary_allow_backtracking_wrap"
                                                class="relative inline-flex items-center h-3 w-5">
                                                <div
                                                    class="toggle-track w-5 h-3 bg-slate-200 rounded-full transition-colors duration-200 ease-in-out">
                                                </div>
                                                <div
                                                    class="toggle-thumb absolute left-0.5 top-0.5 w-2 h-2 bg-white rounded-full shadow-sm transform transition-transform duration-200 ease-in-out">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-4 py-3 bg-white border-t">
                                <div class="grid grid-cols-2 gap-3">
                                    <button
                                        class="py-2 bg-slate-50 text-slate-500 font-bold rounded-xl hover:bg-slate-100 transition-all text-[10px] uppercase tracking-widest"
                                        data-bs-dismiss="modal">Discard</button>
                                    <button
                                        class="py-2 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all text-[10px] uppercase tracking-widest shadow-lg shadow-red-100"
                                        onclick="savePackFromWizard()">
                                        Create Batch
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                        </div> <!-- End main content container (line 8900) -->
                    </div> <!-- End modal-body -->

                    <!-- Quick Mode Footer (Professional Control Bar) -->
                    <div id="quick-mode-footer"
                        class="w-full h-16 px-8 bg-slate-50/90 backdrop-blur-md border-t border-slate-200/60 flex items-center justify-between z-[100] flex-shrink-0">
                        <button
                            class="text-slate-400 hover:text-red-500 font-bold text-[11px] uppercase tracking-[0.1em] transition-all flex items-center gap-2 px-2"
                            data-bs-dismiss="modal">
                            <i class="bi bi-x-circle-fill"></i> Cancel
                        </button>

                        <div class="flex items-center gap-3">


                            <button
                                class="h-10 px-10 bg-red-600 text-white font-extrabold rounded-xl text-[11px] uppercase tracking-[0.05em] shadow-[0_4px_12px_-2px_rgba(220,34,48,0.25)] transition-all hover:bg-red-700 hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2"
                                onclick="savePackFromWizard()">
                                <i class="bi bi-check-lg text-sm"></i> Save Template
                            </button>
                        </div>
                    </div>

                </div> <!-- End modal-content -->
            </div>
        </div>
    </div>

    <!-- Add Question Manually Modal -->
    <div class="modal fade" id="addManualQuestionModal" tabindex="-1" style="z-index: 10050;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header px-4 py-3 border-bottom border-gray-100">
                    <h5 class="modal-title fw-bold text-[#1e293b]">Create New Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label
                                class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-2">Question
                                Type</label>
                            <select class="form-select h-[42px] rounded-[8px] border-[#e2e8f0] text-[13px] font-medium"
                                id="manualQuestionType" onchange="App.onManualQuestionTypeChange(this.value)">
                                <option value="MCQ">MCQ</option>
                                <option value="descriptive">Descriptive question</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label
                                class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-2">Marks</label>
                            <input type="number"
                                class="form-control h-[42px] rounded-[8px] border-[#e2e8f0] text-[14px] font-bold"
                                id="manualQuestionMarks" value="2">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-2">Pedagogy</label>
                        <div class="pedagogy-combo relative w-full" data-pedagogy-base="manualQuestionPedagogy">
                            <input type="hidden" class="pedagogy-combo-hidden" value="">
                            <input type="text" class="pedagogy-combo-search form-control h-[42px] rounded-[8px] border-[#e2e8f0] text-[13px] font-medium w-full" autocomplete="off" spellcheck="false" placeholder="Search or type pedagogy...">
                            <div class="pedagogy-combo-panel mt-0.5 max-h-52 overflow-y-auto rounded-lg border border-slate-200 bg-white py-1 shadow-xl hidden"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label
                            class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-2">Question
                            Text <span class="text-danger">*</span></label>
                        <textarea class="form-control rounded-[8px] border-[#e2e8f0] text-[13px] p-3"
                            id="manualQuestionText" rows="3" placeholder="Enter the question..."></textarea>
                    </div>

                    <!-- MCQ / Multi-select Options Section -->
                    <div id="manualOptionsSection">
                        <label
                            class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-3"
                            id="manualOptionLabel">Answer Options (check one correct)</label>
                        <div class="d-flex flex-column gap-3">
                            <div
                                class="d-flex align-items-center gap-3 p-3 bg-white border border-[#e2e8f0] rounded-[8px]">
                                <span class="fw-bold text-[#64748b]" style="width: 20px;">A</span>
                                <input type="text" class="form-control border-0 p-0 text-[13px] shadow-none"
                                    placeholder="Option A">
                                <div class="d-flex align-items-center gap-2 ps-3 border-start border-[#f1f5f9]">
                                    <input type="radio" class="form-check-input manual-correct-check"
                                        name="manualCorrect" value="A" checked style="width: 18px; height: 18px;">
                                    <span class="text-[11px] font-bold text-[#94a3b8] text-uppercase">Correct</span>
                                </div>
                            </div>
                            <div
                                class="d-flex align-items-center gap-3 p-3 bg-white border border-[#e2e8f0] rounded-[8px]">
                                <span class="fw-bold text-[#64748b]" style="width: 20px;">B</span>
                                <input type="text" class="form-control border-0 p-0 text-[13px] shadow-none"
                                    placeholder="Option B">
                                <div class="d-flex align-items-center gap-2 ps-3 border-start border-[#f1f5f9]">
                                    <input type="radio" class="form-check-input manual-correct-check"
                                        name="manualCorrect" value="B" style="width: 18px; height: 18px;">
                                    <span class="text-[11px] font-bold text-[#94a3b8] text-uppercase">Correct</span>
                                </div>
                            </div>
                            <div
                                class="d-flex align-items-center gap-3 p-3 bg-white border border-[#e2e8f0] rounded-[8px]">
                                <span class="fw-bold text-[#64748b]" style="width: 20px;">C</span>
                                <input type="text" class="form-control border-0 p-0 text-[13px] shadow-none"
                                    placeholder="Option C">
                                <div class="d-flex align-items-center gap-2 ps-3 border-start border-[#f1f5f9]">
                                    <input type="radio" class="form-check-input manual-correct-check"
                                        name="manualCorrect" value="C" style="width: 18px; height: 18px;">
                                    <span class="text-[11px] font-bold text-[#94a3b8] text-uppercase">Correct</span>
                                </div>
                            </div>
                            <div
                                class="d-flex align-items-center gap-3 p-3 bg-white border border-[#e2e8f0] rounded-[8px]">
                                <span class="fw-bold text-[#64748b]" style="width: 20px;">D</span>
                                <input type="text" class="form-control border-0 p-0 text-[13px] shadow-none"
                                    placeholder="Option D">
                                <div class="d-flex align-items-center gap-2 ps-3 border-start border-[#f1f5f9]">
                                    <input type="radio" class="form-check-input manual-correct-check"
                                        name="manualCorrect" value="D" style="width: 18px; height: 18px;">
                                    <span class="text-[11px] font-bold text-[#94a3b8] text-uppercase">Correct</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- True/False Section -->
                    <div id="manualTFSection" class="d-none">
                        <label
                            class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-3">Correct
                            Answer</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="manualTF" id="tfTrue" value="True"
                                    checked>
                                <label class="form-check-label text-[13px] font-bold text-[#334155]"
                                    for="tfTrue">True</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="manualTF" id="tfFalse" value="False">
                                <label class="form-check-label text-[13px] font-bold text-[#334155]"
                                    for="tfFalse">False</label>
                            </div>
                        </div>
                    </div>

                    <!-- Descriptive answer section -->
                    <div id="manualShortAnswerSection" class="d-none">
                        <label
                            class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-2">Correct
                            answer (descriptive question)</label>
                        <input type="text" class="form-control rounded-[8px] border-[#e2e8f0] text-[13px] h-[42px]"
                            placeholder="Enter the correct answer..."
                            id="manualShortCorrectAnswer">
                    </div>

                </div>
                <div class="modal-footer border-0 px-4 pb-4 gap-2">
                    <button type="button" class="btn btn-light px-4 rounded-[8px] font-bold text-[13px]"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary-custom px-4 rounded-[8px] font-bold text-[13px]"
                        onclick="App.addQuestionManually()">Create Question</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Paper Preview Modal -->
    <div class="modal fade" id="paperPreviewModal" tabindex="-1" style="z-index: 10010;">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: #f8fafc;">
                <div class="modal-header px-4 py-3 border-bottom bg-white sticky-top"
                    style="border-top-left-radius: 20px; border-top-right-radius: 20px; z-index: 10;">
                    <h5 class="modal-title fw-bold text-[#1e293b]">Question Paper Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" id="previewPaperContent" style="max-height: 80vh; overflow-y: auto;">
                    <!-- Content injected via JS -->
                </div>
                <div class="modal-footer bg-white border-0 px-4 pb-4"
                    style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                    <div id="defaultPreviewFooter" class="d-flex gap-2">
                        <button type="button" class="btn btn-light px-4 rounded-[8px] font-bold"
                            data-bs-dismiss="modal">Close Preview</button>
                    </div>
                    <div id="selectionPreviewFooter" class="d-none d-flex gap-2">
                        <button type="button" class="btn btn-light px-4 rounded-[8px] font-bold"
                            onclick="cancelTemplateSelection()">Cancel</button>
                        <button type="button" class="btn btn-red px-5 rounded-[8px] font-bold"
                            onclick="confirmTemplateSelection()">OK, Select This Template</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onerror = function (msg, url, lineNo, columnNo, error) {
            alert("JS Error: " + msg + "\nLine: " + lineNo);
            return false;
        };

        // --- Global Helpers & Navigation ---
        // Restore tab on load
        window.addEventListener('DOMContentLoaded', () => {
            const fn = window.switchMainTab;
            if (typeof fn !== 'function') return;
            const savedTab = localStorage.getItem('activeTestTab');
            if (savedTab && document.getElementById('tab-content-' + savedTab)) {
                fn(savedTab);
            } else {
                fn('management');
            }
        });

        function openModal(id) {
            if (id === 'createPackModal') { openPackWizard(); return; }
            const el = document.getElementById(id);
            if (el) el.classList.add('open');
            if (id === 'TestModal' && typeof updateNewTestButtonState === 'function') {
                updateNewTestButtonState(true);
            }
        }
        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('open');
            if (id === 'TestModal' && typeof updateNewTestButtonState === 'function') {
                updateNewTestButtonState(false);
            }
        }

        /* Template Builder */
        function openTemplateBuilder() {
            if (document.getElementById('createPackModal').classList.contains('show')) {
                toggleWizardView('template');
            } else {
                document.getElementById('templateBuilderOverlay').classList.add('open');
                document.getElementById('templateBuilderSidebar').classList.add('open');
                loadSidebarTemplates();
            }
        }

        function closeTemplateBuilder() {
            document.getElementById('templateBuilderOverlay').classList.remove('open');
            document.getElementById('templateBuilderSidebar').classList.remove('open');
        }

        function resetBuilder() {
            document.getElementById('builder_storage_name').value = '';
            document.getElementById('builder_duration').value = '60';
            document.getElementById('builder_sections_container').innerHTML = `
            <div class="empty-state py-12 text-center bg-slate-50 border border-dashed border-slate-200 rounded-3xl" id="builder_empty_state">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <i class="bi bi-stack text-3xl"></i>
                </div>
                <h5 class="text-sm font-bold text-slate-600 mb-1">No Sections Added</h5>
                <p class="text-xs text-slate-400">Select a section blueprint above to start building your paper structure</p>
            </div>
        `;
            document.getElementById('builder_total_marks').textContent = '0 Marks';
            document.getElementById('builder_section_count').textContent = '0 Sections';
            document.getElementById('builder_last_sync').textContent = 'New Template';

            // Remove active states from sidebar
            document.querySelectorAll('.template-item-card').forEach(el => el.classList.remove('active'));
        }

        function loadSidebarTemplates() {
            const list = document.getElementById('sidebar_list');
            const templates = App.templates || [];

            if (templates.length === 0) {
                list.innerHTML = `<div class="p-8 text-center text-slate-400 text-xs italic">No templates found.</div>`;
                return;
            }

            list.innerHTML = templates.map(t => `
            <div class="template-item-card group relative" onclick="loadTemplateToBuilder(${t.id}, this)" data-category="${t.category}" id="builder_temp_${t.id}">
                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-500 group-hover:text-red-500 transition-colors">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h6 class="text-[13px] font-bold text-slate-800 mb-0 truncate">${t.name}</h6>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[10px] font-black text-red-500 uppercase tracking-widest">${t.category || 'General'}</span>
                        <span class="text-[10px] font-bold text-slate-500">• ${t.total_marks || 0} Marks</span>
                    </div>
                </div>
                
                <!-- Actions overlay -->
                <div class="template-card-actions">
                    <button class="action-icon-btn btn-clone" onclick="cloneTemplate(${t.id}, event)" title="Clone Structure">
                        <i class="bi bi-copy"></i>
                    </button>
                    <button class="action-icon-btn btn-edit" onclick="editTemplate(${t.id}, event)" title="Edit Template">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="action-icon-btn btn-delete" onclick="deleteTemplate(${t.id}, event)" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
        }

        function filterSidebar(category, btn) {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            if (btn) btn.classList.add('active');

            const cards = document.querySelectorAll('.template-item-card');
            cards.forEach(card => {
                if (category === 'All' || card.dataset.category === category) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function searchSidebar(query) {
            const cards = document.querySelectorAll('.template-item-card');
            const q = query.toLowerCase();
            cards.forEach(card => {
                const name = card.querySelector('h6').textContent.toLowerCase();
                if (name.includes(q)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function addSelectedSection(type, name = null, count = 10, marks = null) {
            if (!type) return;

            const container = document.getElementById('builder_sections_container');
            const emptyState = document.getElementById('builder_empty_state');
            if (emptyState) emptyState.style.display = 'none';

            const sectionId = Date.now() + Math.random();
            const sectionCard = document.createElement('div');
            sectionCard.className = 'section-builder-card animate-fadeIn';
            sectionCard.dataset.type = type;

            const nt = typeof App !== 'undefined' && App.normalizeType ? App.normalizeType(type) : '';
            const typeLabel = typeof window.inlineSectionTypeDisplayName === 'function'
                ? window.inlineSectionTypeDisplayName(type)
                : (nt === 'descriptive' ? 'Descriptive question' : type);
            const displayName = name || `${typeLabel} Section`;
            const displayMarks = marks !== null ? marks : (nt === 'descriptive' ? 2 : 1);

            sectionCard.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="section-drag-handle">
                        <i class="bi bi-grip-vertical text-lg"></i>
                    </div>
                    <div>
                        <input type="text" class="bg-transparent border-0 font-bold text-slate-800 p-0 focus:ring-0 text-sm" value="${displayName}">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">${typeLabel} Component</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 hover:text-red-500 transition-all" onclick="this.closest('.section-builder-card').remove(); updateBuilderStats();">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Questions</label>
                    <input type="number" class="w-full bg-transparent border-0 p-0 font-bold text-slate-800 text-xs focus:ring-0 sec-count" value="${count}" oninput="updateBuilderStats()">
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Marks Each</label>
                    <input type="number" class="w-full bg-transparent border-0 p-0 font-bold text-slate-800 text-xs focus:ring-0 sec-marks" value="${displayMarks}" oninput="updateBuilderStats()">
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Pass %</label>
                    <input type="number" class="w-full bg-transparent border-0 p-0 font-bold text-slate-800 text-xs focus:ring-0" value="40">
                </div>
            </div>
        `;

            container.appendChild(sectionCard);
            document.getElementById('task_selector').value = '';
            updateBuilderStats();
        }

        function updateBuilderStats() {
            const sections = document.querySelectorAll('.section-builder-card');
            let totalMarks = 0;
            let totalSections = sections.length;

            sections.forEach(s => {
                const count = parseInt(s.querySelector('.sec-count').value) || 0;
                const marks = parseInt(s.querySelector('.sec-marks').value) || 0;
                totalMarks += (count * marks);
            });

            document.getElementById('builder_total_marks').textContent = totalMarks + ' Marks';
            document.getElementById('builder_section_count').textContent = totalSections + ' Sections';
        }

        let currentEditingTemplateId = null;

        function loadTemplateToBuilder(id, btn, isClone = false) {
            currentEditingTemplateId = isClone ? null : id;
            document.querySelectorAll('.template-item-card').forEach(el => el.classList.remove('active'));
            if (btn && !isClone) btn.classList.add('active');

            const template = App.templates.find(t => t.id == id);
            if (!template) return;

            document.getElementById('builder_storage_name').value = isClone ? template.name + ' (Copy)' : template.name;
            document.getElementById('builder_category').value = template.category || 'General';
            document.getElementById('builder_duration').value = template.duration || 60;
            document.getElementById('builder_start_date').value = template.start_date || '';
            document.getElementById('builder_end_date').value = template.end_date || '';

            const container = document.getElementById('builder_sections_container');
            container.innerHTML = '';

            try {
                const structure = typeof template.sections === 'string' ? JSON.parse(template.sections) : template.sections;

                // Load Questions
                App.manualQuestions = isClone ? [] : hydrateTemplateQuestions(template.id, template.questions || []);

                if (structure && structure.length > 0) {
                    structure.forEach((s, idx) => {
                        addSelectedSection(s.marks_type || s.type, s.section_name || s.name, s.num_questions || s.count, s.marks_per_question || s.marks);
                    });

                    // If App.renderManualSections is available, sync the question bank UI
                    if (typeof App.renderManualSections === 'function' && !isClone) {
                        App.renderManualSections(structure);
                        structure.forEach((_, idx) => App.refreshSectionQuestions(idx));
                    }
                } else {
                    // Fallback for older data or empty templates
                    const emptyState = `<div class="empty-state py-12 text-center bg-slate-50 border border-dashed border-slate-200 rounded-3xl" id="builder_empty_state">
                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <i class="bi bi-stack text-3xl"></i>
                    </div>
                    <h5 class="text-sm font-bold text-slate-600 mb-1">No Sections Added</h5>
                    <p class="text-xs text-slate-400">Select a section blueprint above to start building your paper structure</p>
                </div>`;
                    container.innerHTML = emptyState;
                }
            } catch (e) {
                console.error("Failed to parse template structure", e);
            }

            document.getElementById('builder_last_sync').textContent = isClone ? 'New Cloned Template' : 'Loaded: ' + new Date().toLocaleTimeString();

            Swal.fire({
                toast: true, position: 'top-end', icon: 'success', title: isClone ? 'Template structure cloned' : 'Template loaded with ' + App.manualQuestions.length + ' questions', showConfirmButton: false, timer: 1500
            });
        }

        /* Action Handlers */

        function resetBuilder() {
            currentEditingTemplateId = null;
            document.querySelectorAll('.template-item-card').forEach(el => el.classList.remove('active'));
            document.getElementById('builder_storage_name').value = '';
            document.getElementById('builder_duration').value = 60;
            document.getElementById('builder_start_date').value = '';
            document.getElementById('builder_end_date').value = '';
            document.getElementById('builder_sections_container').innerHTML = `<div class="empty-state py-12 text-center bg-slate-50 border border-dashed border-slate-200 rounded-3xl" id="builder_empty_state">
            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 text-slate-300">
                <i class="bi bi-stack text-3xl"></i>
            </div>
            <h5 class="text-sm font-bold text-slate-600 mb-1">No Sections Added</h5>
            <p class="text-xs text-slate-400">Select a section blueprint above to start building your paper structure</p>
        </div>`;
            updateBuilderStats();
            document.getElementById('builder_last_sync').textContent = 'New Template';
        }

        async function saveTemplateBuilder() {
            const name = document.getElementById('builder_storage_name').value.trim();
            if (!name) {
                Swal.fire('Required', 'Please enter a Template Name', 'warning');
                return;
            }

            const category = document.getElementById('builder_category').value;
            const duration = document.getElementById('builder_duration').value || 60;
            const startDate = document.getElementById('builder_start_date').value;
            const endDate = document.getElementById('builder_end_date').value;

            const sections = [];
            document.querySelectorAll('.section-builder-card').forEach(card => {
                sections.push({
                    name: card.querySelector('input[type="text"]').value,
                    type: card.dataset.type,
                    count: parseInt(card.querySelector('.sec-count').value) || 0,
                    marks: parseInt(card.querySelector('.sec-marks').value) || 0
                });
            });

            if (sections.length === 0) {
                Swal.fire('Required', 'Please add at least one section to the template', 'warning');
                return;
            }

            Swal.fire({
                title: 'Saving Template...',
                text: 'Uploading structure to server',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const data = {
                    id: currentEditingTemplateId,
                    name: name,
                    category: category,
                    duration: duration,
                    start_date: startDate,
                    end_date: endDate,
                    sections: sections,
                    questions: App.manualQuestions
                };

                const response = await fetch('Test/saveTemplate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.status === 'success') {
                    // Update local App.templates state
                    if (currentEditingTemplateId) {
                        const idx = App.templates.findIndex(t => t.id == currentEditingTemplateId);
                        if (idx !== -1) App.templates[idx] = result.template;
                    } else {
                        App.templates.push(result.template);
                        currentEditingTemplateId = result.template.id;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Template Saved',
                        text: 'The Test structure has been persisted.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    document.getElementById('builder_last_sync').textContent = 'Saved at ' + new Date().toLocaleTimeString();
                    loadSidebarTemplates(); // Refresh sidebar
                } else {
                    Swal.fire('Error', result.message || 'Failed to save template', 'error');
                }
            } catch (error) {
                console.error('Save Error:', error);
                Swal.fire('Error', 'An unexpected error occurred.', 'error');
            }
        }


        // --- Batch Wizard Logic ---
        let currentPackStep = 1;
        const totalPackSteps = 3;

        function openPackWizard(testId) {
            if (testId) currentTestIdForPack = testId;
            currentEditPackId = null; // Clear edit mode if any

            // 1. Reset Master Hidden Inputs
            document.getElementById('pack_wizard_name').value = '';
            document.getElementById('baseTemplateSelect').value = '';
            document.getElementById('pack_duration').value = 60;
            document.getElementById('pack_start_time').value = '';
            document.getElementById('pack_end_time').value = '';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('pack_scheduled_date').value = today;
            document.getElementById('pack_instructions').value = 'Read all questions carefully before answering. Ensure a stable internet connection.';
            document.getElementById('pack_shuffle').checked = true;
            document.getElementById('pack_proctored').checked = true;
            document.getElementById('pack_lockdown').checked = true;
            App.manualQuestions = [];
            App.quickModePaperSource = null;
            hasBuilderGeneratedPreview = false;

            // Initialize candidate storage for this test if it doesn't exist
            if (!App.selectedCandidates[currentTestIdForPack]) {
                App.selectedCandidates[currentTestIdForPack] = [];
            }

            // 2. Reset Sidebar Interactive Inputs
            document.getElementById('summary_name').value = 'New Batch';
            document.getElementById('summary_duration_input').value = 60;
            document.getElementById('summary_start_input').value = '';
            document.getElementById('summary_end_input').value = '';
            document.getElementById('summary_instructions_input').value = 'Read all questions carefully before answering. Ensure a stable internet connection.';

            // Reset Toggle Visuals in Sidebar
            updateSummary(); // This will sync master -> sidebar visuals

            // 3. Reset UI Highlights & Details
            document.querySelectorAll('.template-card').forEach(card => {
                card.classList.remove('border-red-600', 'bg-red-50');
                card.querySelector('.check-badge').classList.add('opacity-0');
            });
            updateTemplateDetails(null);

            App.selectedWizardCandidates = [];
            updateWizardCandidateLabel();

            // 4. Open Modal
            const modalEl = document.getElementById('createPackModal');
            if (modalEl) {
                try {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                } catch (e) {
                    $(modalEl).modal('show');
                }
            }
        }

        function openQuickTemplateModal() {
            const modalEl = document.getElementById('createPackModal');
            if (!modalEl) return;

            // Ensure we start in config view, not preview
            closeQuickPreview();

            // Set Quick Mode
            modalEl.classList.add('quick-mode');

            // Hide generated sections initially (redundant with closeQuickPreview but safe)
            const qbSection = document.getElementById('quick-qb-selector-section');
            const paperSection = document.getElementById('quick-generated-paper-section');
            if (qbSection) qbSection.classList.add('hidden');
            if (paperSection) paperSection.classList.add('hidden');

            // Sync Question Banks dropdown
            if (typeof syncQBDropdowns === 'function') {
                syncQBDropdowns();
            }

            // Reset Quick Mode specific inputs
            const qBatchName = document.getElementById('quick_batch_name');
            if (qBatchName) qBatchName.value = '';
            const qBatchDuration = document.getElementById('quick_batch_duration');
            if (qBatchDuration) qBatchDuration.value = '60';
            const baseTemplateSelect = document.getElementById('baseTemplateSelect');
            if (baseTemplateSelect) baseTemplateSelect.value = '';
            updateQuickModeFooterVisibility();

            // If no test is currently active for pack, default to the first one available
            if (!currentTestIdForPack) {
                if (App.Tests && App.Tests.length > 0) {
                    currentTestIdForPack = App.Tests[0].id;
                } else {
                    // Fallback to a dummy ID if no tests exist (should ideally not happen if UI is gated)
                    currentTestIdForPack = 'temp_' + Date.now();
                }
            }

            // Open standard wizard logic but with specific reset
            openPackWizard(currentTestIdForPack);

            // AUTO-OPEN CREATE TEMPLATE SECTION
            openTemplateBuilderInline();

            // Additional Quick Mode resets
            const qSelector = document.querySelector('#quick-template-selector select');
            if (qSelector) qSelector.selectedIndex = 0;

            // Listen for modal close to remove quick-mode class
            const resetModal = () => {
                modalEl.classList.remove('quick-mode');
                modalEl.classList.remove('template-scroll-mode');
                modalEl.removeEventListener('hidden.bs.modal', resetModal);
            };
            modalEl.addEventListener('hidden.bs.modal', resetModal);
        }

        function closeQuickPreview() {
            const configView = document.getElementById('batchWizardConfigView');
            const paperSection = document.getElementById('quick-generated-paper-section');
            const footer = document.getElementById('quick-mode-footer');

            if (configView) configView.classList.remove('hidden');
            if (paperSection) paperSection.classList.add('hidden');
            if (footer) footer.classList.remove('hidden');
        }

        function initGeneratedSectionPager(container) {
            if (!container) return;
            const cards = Array.from(container.querySelectorAll(':scope > .card'));
            if (cards.length <= 1) return;

            let current = 0;
            cards.forEach((card, idx) => {
                card.classList.toggle('hidden', idx !== current);
            });

            const nav = document.createElement('div');
            nav.className = 'flex items-center justify-between mb-4 p-3 bg-white border border-slate-100 rounded-xl shadow-sm';
            nav.innerHTML = `
                <button type="button" class="w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-800 hover:border-slate-300 transition-all flex items-center justify-center" data-dir="-1" data-nav-btn="true">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div class="text-center px-3">
                    <div class="text-[12px] font-black text-slate-800 uppercase tracking-widest" id="generated_section_nav_title"></div>
                    <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider" id="generated_section_nav_meta"></div>
                </div>
                <button type="button" class="w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-800 hover:border-slate-300 transition-all flex items-center justify-center" data-dir="1" data-nav-btn="true">
                    <i class="bi bi-chevron-right"></i>
                </button>
            `;

            const titleEl = nav.querySelector('#generated_section_nav_title');
            const metaEl = nav.querySelector('#generated_section_nav_meta');

            const update = () => {
                cards.forEach((card, idx) => card.classList.toggle('hidden', idx !== current));
                const active = cards[current];
                if (titleEl) titleEl.textContent = active?.dataset.sectionTitle || `Section ${current + 1}`;
                if (metaEl) metaEl.textContent = active?.dataset.sectionMeta || `${current + 1} / ${cards.length}`;

                const isMcqSection = (active?.dataset.sectionType || '').toLowerCase() === 'mcq';
                nav.querySelectorAll('[data-nav-btn="true"]').forEach((btn) => {
                    if (isMcqSection) {
                        btn.className = 'w-9 h-9 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all flex items-center justify-center';
                    } else {
                        btn.className = 'w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-800 hover:border-slate-300 transition-all flex items-center justify-center';
                    }
                });
            };

            nav.querySelectorAll('button[data-dir]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const dir = parseInt(btn.getAttribute('data-dir') || '0', 10);
                    if (!dir) return;
                    current += dir;
                    if (current < 0) current = cards.length - 1;
                    if (current >= cards.length) current = 0;
                    update();
                });
            });

            container.prepend(nav);
            update();
        }


        function generateQuickQuestionPaper(forceGenerate = false) {
            const qbSelectInline = document.getElementById('builder_qb_select_inline');
            const qbSelectQuick = document.getElementById('quick_qb_select');
            
            const qbId = (qbSelectInline && qbSelectInline.value) || (qbSelectQuick && qbSelectQuick.value);
            
            // Determine if we are in builder mode
            const builderView = document.getElementById('templateBuilderInlineView');
            const isBuilderMode = builderView && !builderView.classList.contains('hidden');
            
            const templateId = isBuilderMode ? (currentEditingTemplateIdInline || 'new_temp') : (document.getElementById('baseTemplateSelect').value || 'new_temp');
            
            if (!qbId) { Swal.fire('Wait!', 'Please select a Question Bank first.', 'info'); return; }

            // Always force reshuffle for "QP Generate" button
            if (forceGenerate) {
                App.quickModePaperSource = null;
            }

            const configView = document.getElementById('batchWizardConfigView');
            const paperSection = document.getElementById('quick-generated-paper-section');
            const footer = document.getElementById('quick-mode-footer');

            // Toggle visibility
            if (!isBuilderMode) {
                if (configView) configView.classList.add('hidden');
                if (paperSection) paperSection.classList.remove('hidden');
            } else {
                hasBuilderGeneratedPreview = false;
                updateBuilderTemplateFooterVisibility();
                const builderPreview = document.getElementById('builder_questions_section_inline');
                if (builderPreview) builderPreview.classList.remove('hidden');
            }

            const container = isBuilderMode ? 
                document.getElementById('builder_questions_container_inline') : 
                document.getElementById('quick_generated_questions_container');
            container.innerHTML = '<div class="text-center py-24"><div class="spinner-border text-red-600 mb-4" style="width: 3rem; height: 3rem;"></div><p class="text-[12px] font-black uppercase tracking-widest text-slate-400">Assembling Question Paper...</p></div>';

            setTimeout(async () => {
                try {
                    let paper = null;
                    
                    if (isBuilderMode) {
                        // In builder mode, we pull sections from the DOM
                        const sections = [];
                        document.querySelectorAll('#builder_sections_container_inline > div:not(.empty-state):not(.is-editing)').forEach(row => {
                            sections.push({
                                name: (
                                    row.querySelector('.sec-name-hidden-inline')?.value ||
                                    row.querySelector('input[type="text"]')?.value ||
                                    row.querySelector('.sec-display-name')?.textContent ||
                                    'Section'
                                ),
                                type: row.dataset.type || 'MCQ',
                                count: parseInt(
                                    row.querySelector('.sec-count-inline')?.value ||
                                    row.querySelector('.sec-display-count')?.textContent ||
                                    '0'
                                ) || 0,
                                marks: parseInt(
                                    row.querySelector('.sec-marks-inline')?.value ||
                                    row.querySelector('.sec-display-marks')?.textContent ||
                                    '0'
                                ) || 0
                            });
                        });
                        
                        if (sections.length === 0) {
                            throw new Error("Please add and SAVE at least one section to your blueprint first.");
                        }
                        
                        paper = await App.generatePaperFromBank(qbId, { sections });
                        if (paper && paper.questions) {
                            App.manualQuestions = paper.questions;
                            hasBuilderGeneratedPreview = App.manualQuestions.length > 0;
                            updateBuilderTemplateFooterVisibility();
                            // Update mapping status in builder
                            const statusEl = document.getElementById('qb_mapping_status');
                            const textEl = document.getElementById('qb_mapping_text');
                            if (statusEl) statusEl.classList.remove('hidden');
                            if (textEl) textEl.textContent = `${App.manualQuestions.length} Questions Mapped from Bank`;
                        }
                        if (!paper) {
                            hasBuilderGeneratedPreview = false;
                            updateBuilderTemplateFooterVisibility();
                            closeQuickPreview();
                            return;
                        }
                        console.log("Updated App.manualQuestions with", App.manualQuestions.length, "questions from bank");
                    } else if (qbId) {
                        // Standard Quick Mode from base template
                        paper = await generateQuestionsForQuickMode(templateId, qbId);
                        if (!paper) {
                            closeQuickPreview();
                            return;
                        }
                        console.log("Updated App.manualQuestions with", App.manualQuestions.length, "questions from bank");
                    } else if (!forceGenerate && App.manualQuestions && App.manualQuestions.length > 0) {
                        console.log("Rendering paper from saved template/manual questions...");
                        paper = App.getGroupedPaper(App.manualQuestions, templateId);
                    } else {
                        // No manual questions and no bank selected
                        console.warn("No questions available and no bank selected");
                        container.innerHTML = `
                            <div class="text-center py-20 bg-white border border-slate-100 rounded-3xl shadow-sm">
                                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                                    <i class="bi bi-database-exclamation text-3xl"></i>
                                </div>
                                <h5 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-1">No Questions Found</h5>
                                <p class="text-[11px] text-slate-400 font-medium mb-6">Please upload questions to the template or select a Question Bank to generate them.</p>
                                <button class="px-6 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-indigo-100" onclick="document.getElementById('batchWizardConfigView').classList.remove('hidden'); document.getElementById('quick-generated-paper-section').classList.add('hidden');">
                                    Back to Config
                                </button>
                            </div>`;
                        return;
                    }

                    if (!paper) {
                        console.error("Paper generation returned null");
                        container.innerHTML = '<div class="text-center py-12 text-red-500 font-bold">Failed to process paper structure. Template data is missing.</div>';
                        return;
                    }

                    if (paper.warnings && paper.warnings.length > 0) {
                        console.warn("Paper Warnings:", paper.warnings);
                        Swal.fire({
                            title: 'Paper Composition Notice',
                            html: `<div class="text-left text-sm font-medium text-slate-600">${paper.warnings.map(w => `<div class="mb-2 flex items-start gap-2"><i class="bi bi-exclamation-triangle-fill text-amber-500"></i><span>${w}</span></div>`).join('')}</div>`,
                            icon: 'info',
                            confirmButtonColor: '#4f46e5'
                        });
                    }

                    let groupedHtml = '';
                    if (paper.grouped && paper.grouped.length > 0) {
                        console.log("Rendering", paper.grouped.length, "sections");
                        paper.grouped.forEach((group, sIdx) => {
                            const s = group.section;
                            const picked = group.questions;
                            const count = parseInt(s.num_questions || s.count || 0);
                            const type = s.marks_type || s.type || 'MCQ';
                            const targetType = App.normalizeType(type);
                            const marks = s.marks_per_question || s.marks || 0;

                            const sectionTitle = s.section_name || s.name || (targetType === 'mcq' ? 'Multiple Choice' : 'Descriptive question');
                            const sectionMeta = `${picked.length} of ${count} Questions • ${marks} Marks Each`;

                            groupedHtml += `
                                <div class="card border-0 shadow-sm rounded-2xl overflow-hidden bg-white mb-6 animate-fadeIn" style="animation-delay: ${sIdx * 0.1}s" data-section-title="${sectionTitle}" data-section-meta="${sectionMeta}" data-section-type="${targetType}">
                                    <div class="p-4 bg-slate-50/50 border-b border-slate-50 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-slate-800 font-black text-xs shadow-sm border border-slate-100">${sIdx + 1}</div>
                                            <div>
                                                <h5 class="text-[12px] font-black text-slate-800 uppercase tracking-widest mb-0">${sectionTitle}</h5>
                                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mb-0">${sectionMeta}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-6 space-y-6">
                                        ${picked && picked.length > 0 ? picked.map((q, qIdx) => `
                                            <div class="relative pl-10 border-b border-slate-50 pb-6 last:border-0 last:pb-0">
                                                <div class="absolute left-0 top-0 w-8 h-8 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center text-[11px] font-black border border-slate-100">${qIdx + 1}</div>
                                                <div class="flex items-start justify-between mb-4">
                                                    <p class="text-[14px] font-bold text-slate-800 leading-relaxed pt-1 whitespace-pre-wrap mb-0">${q.question || q.content || 'No question text'}</p>
                                                    <div class="flex flex-col items-end gap-1 shrink-0 ml-4">
                                                        <span class="px-2 py-0.5 rounded bg-slate-50 text-slate-400 text-[8px] font-black uppercase tracking-widest border border-slate-100">${q.marks || marks} Marks</span>
                                                        ${q.difficulty ? `<span class="px-2 py-0.5 rounded ${q.difficulty === 'Hard' ? 'bg-red-50 text-red-600 border-red-100' : q.difficulty === 'Medium' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100'} text-[8px] font-black uppercase tracking-widest border">${q.difficulty}</span>` : ''}
                                                    </div>
                                                </div>
                                                ${App.normalizeType(q.type || type) === 'mcq' ? `
                                                    <div class="grid grid-cols-2 gap-3">
                                                        ${['a', 'b', 'c', 'd'].map(opt => `
                                                            <div class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-50 bg-slate-50/20">
                                                                <div class="w-6 h-6 rounded bg-slate-200 text-slate-500 flex items-center justify-center text-[10px] font-black uppercase">${opt}</div>
                                                                <span class="text-[12px] text-slate-600 font-medium whitespace-pre-wrap">${q['option_' + opt] || '---'}</span>
                                                            </div>
                                                        `).join('')}
                                                    </div>
                                                ` : `
                                                    <div class="p-4 bg-blue-50/30 border border-blue-100 rounded-2xl">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                                            <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Evaluation Reference</span>
                                                        </div>
                                                        <p class="text-[12px] text-slate-600 font-medium italic mb-0 whitespace-pre-wrap leading-relaxed">${q.correct_answer || q.expected_answer || 'No reference answer provided'}</p>
                                                    </div>
                                                `}
                                            </div>
                                        `).join('') : `
                                            <div class="text-center py-12 border-2 border-dashed border-slate-100 rounded-3xl bg-slate-50/20">
                                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mx-auto mb-3 text-slate-200">
                                                    <i class="bi bi-exclamation-triangle text-2xl"></i>
                                                </div>
                                                <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">No matching questions found in this section</p>
                                            </div>
                                        `}
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        console.warn("No grouped sections found in paper");
                        groupedHtml = '<div class="text-center py-20 bg-white border border-slate-100 rounded-3xl shadow-sm"><div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300"><i class="bi bi-stack text-3xl"></i></div><h5 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-1">Incomplete Template</h5><p class="text-[11px] text-slate-400 font-medium">The selected template has no sections defined.</p></div>';
                    }

                    container.innerHTML = groupedHtml;
                    if (isBuilderMode && paper.grouped && paper.grouped.length > 1) {
                        initGeneratedSectionPager(container);
                    }

                    // Scroll to paper section
                    setTimeout(() => {
                        const targetScroll = isBuilderMode ? document.getElementById('builder_questions_section_inline') : paperSection;
                        if (targetScroll) targetScroll.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                } catch (error) {
                    if (isBuilderMode) {
                        hasBuilderGeneratedPreview = false;
                        updateBuilderTemplateFooterVisibility();
                    }
                    console.error("Preview Generation Error:", error);
                    container.innerHTML = '<div class="text-center py-12 text-red-500 font-bold">An error occurred while generating the paper preview. Check console for details.</div>';
                }
            }, 600);
        }

        function handleTestNameChange(value) {
            // Trigger the visibility logic
            handleTestTypeChange('internal');
        }

        function handleTestTypeChange(type) {
            // Toggle Assignment Views in Step 1
            const internalView = document.getElementById('step1InternalAssign');
            const recruitmentView = document.getElementById('step1RecruitmentAssign');
            const hasName = document.getElementById('packTestName').value;

            if (internalView) {
                internalView.classList.toggle('d-none', type !== 'internal' || !hasName);
            }
            if (recruitmentView) {
                recruitmentView.classList.toggle('d-none', type !== 'recruitment' || !hasName);
            }

            // Update summary label in Step 1
            const summTypeBadge = document.getElementById('summ_temp_type');
            if (summTypeBadge) {
                summTypeBadge.textContent = type.charAt(0).toUpperCase() + type.slice(1);
                summTypeBadge.className = type === 'recruitment' ? 'badge-custom badge-purple' : 'badge-custom badge-blue';
            }
        }

        // Handle Select All for Candidate Assignment & Questions
        document.addEventListener('change', (e) => {
            const isSelectAllAssign = e.target.classList.contains('select-all-assign');
            const isSelectAllQuestions = e.target.classList.contains('select-all-questions');
            const isInternal = e.target.classList.contains('internal-check');
            const isRecruit = e.target.classList.contains('recruitment-check');
            const isQuestion = e.target.classList.contains('question-check');

            if (isSelectAllAssign || isSelectAllQuestions) {
                const targetClass = e.target.dataset.target;
                const checkboxes = document.querySelectorAll(`.${targetClass}`);
                checkboxes.forEach(cb => cb.checked = e.target.checked);

                // If selecting all questions, trigger the count update
                if (isSelectAllQuestions) {
                    App.updateManualCount();
                }
            }

            if (isSelectAllAssign || isInternal || isRecruit) {
                const type = (isInternal || (isSelectAllAssign && e.target.dataset.target === 'internal-check')) ? 'internal' : 'recruitment';
                const checks = document.querySelectorAll(`.${type}-check:checked`);
                const count = checks.length;

                // Update Review Summary
                const revAssigned = document.getElementById('rev_assigned');
                if (revAssigned) {
                    revAssigned.textContent = `${count} ${count === 1 ? 'Person' : 'People'}`;
                }

                // Update section header
                const container = document.getElementById(type === 'internal' ? 'step1InternalAssign' : 'step1RecruitmentAssign');
                if (container) {
                    const label = container.querySelector('.label-text');
                    if (label) {
                        label.textContent = `${type === 'internal' ? 'Target Employees' : 'Target Freshers / Candidates'} (${count} selected)`;
                    }
                }
            }
        });

        let currentEditingPackId = null;

        function editTestPack(id, templateId, packName) {
            currentEditingPackId = id;

            // 1. Populate Master Hidden Inputs with existing data
            if (document.getElementById('pack_wizard_name')) document.getElementById('pack_wizard_name').value = packName || '';
            if (document.getElementById('baseTemplateSelect')) document.getElementById('baseTemplateSelect').value = templateId || '';

            // Update Sidebar/Interactive inputs
            if (document.getElementById('summary_name')) document.getElementById('summary_name').value = packName || '';

            // Pre-select template in discovery
            if (templateId) {
                selectTemplate(templateId);
            }

            // Sync visuals
            updateSummary();

            // Open the correct modal
            const modalEl = document.getElementById('createPackModal');
            if (modalEl) {
                try {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                } catch (e) {
                    $(modalEl).modal('show');
                }
            }
        }

        async function updatePackTemplate() {
            if (!currentEditingPackId) return;

            const templateId = document.getElementById('edit_pack_template_id').value;

            try {
                const resp = await fetch('Test/updateTestPackTemplate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        pack_id: currentEditingPackId,
                        template_id: templateId
                    })
                });
                const res = await resp.json();
                if (res.status === 'success') {
                    const status = document.getElementById('template_change_status');
                    status.classList.remove('hidden');
                    setTimeout(() => status.classList.add('hidden'), 3000);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Failed to update template.', 'error');
            }
        }

        function manageQuestions(id) {
            // Compatibility wrapper for any old calls
            editTestPack(id, '', 'Question Management');
        }

        App.downloadMCQTemplate = () => {
            const headers = ["question_text", "option_a", "option_b", "option_c", "option_d", "correct_answer"];
            const rows = []; // Empty template

            let csvContent = "data:text/csv;charset=utf-8,"
                + headers.join(",") + "\n"
                + rows.map(e => e.map(val => `"${val}"`).join(",")).join("\n");

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "mcq_template.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        App.downloadShortTemplate = () => {
            const headers = ["question_text", "correct_answer"];
            const rows = []; // Empty template

            let csvContent = "data:text/csv;charset=utf-8,"
                + headers.join(",") + "\n"
                + rows.map(e => e.map(val => `"${val}"`).join(",")).join("\n");

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "descriptive_question_template.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        function selectPopulateMethod(method) {
            // Fix for ID mapping
            const cardIds = { 'Manual': 'cardManualOverride', 'Bulk': 'cardBulkUpload' };
            Object.values(cardIds).forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.remove('active-selection');
            });
            document.getElementById(cardIds[method]).classList.add('active-selection');

            document.getElementById('manualOverrideView').classList.toggle('d-none', method !== 'Manual');
            document.getElementById('bulkUploadView').classList.toggle('d-none', method !== 'Bulk');

            App.updateManualCount();

            document.getElementById('rev_method').textContent = method + '-select';
        }

        App.addNewManualQuestion = (sectionIdx, type, marks, maxCount) => {
            // We'll allow adding questions beyond the limit during the build process
            // to provide a better user experience. We can show a toast instead of blocking.
            const currentCount = App.manualQuestions.filter(q => q.sectionIdx == sectionIdx).length;
            if (currentCount >= maxCount) {
                Swal.fire({
                    title: 'Note: Limit Exceeded',
                    text: `You are adding more than the configured ${maxCount} questions for this section. The structure will be updated to accommodate this.`,
                    icon: 'info',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            }

            const qId = 'm' + Date.now();
            App.manualQuestions.push({
                id: qId,
                sectionIdx: sectionIdx,
                type: type,
                marks: marks,
                question: '',
                pedagogy: '',
                option_a: '',
                option_b: '',
                option_c: '',
                option_d: '',
                correct_answer: '',
                category: 'Manual'
            });
            App.refreshSectionQuestions(sectionIdx);
        };

        App.updateManualQuestion = (id, field, value) => {
            const q = App.manualQuestions.find(q => q.id === id);
            if (q) {
                q[field] = value;
            }
        };

        App.saveManualQuestionRow = (id) => {
            const q = App.manualQuestions.find(item => item.id === id);
            if (!q) return;

            if (!String(q.question || '').trim()) {
                Swal.fire('Required', 'Please enter question text before saving.', 'warning');
                return;
            }

            Swal.fire({
                icon: 'success',
                title: 'Question Saved',
                text: 'Question changes stored in template draft.',
                timer: 900,
                showConfirmButton: false
            });
        };

        App.currentTargetSectionIdx = null;

        App.onManualQuestionTypeChange = (type) => {
            const optionsSec = document.getElementById('manualOptionsSection');
            const tfSec = document.getElementById('manualTFSection');
            const shortSec = document.getElementById('manualShortAnswerSection');
            const label = document.getElementById('manualOptionLabel');

            if (optionsSec) optionsSec.classList.add('d-none');
            if (tfSec) tfSec.classList.add('d-none');
            if (shortSec) shortSec.classList.add('d-none');

            const cleanType = (type || '').toLowerCase();

            if (cleanType.includes('mcq') || cleanType.includes('multiple choice')) {
                if (optionsSec) optionsSec.classList.remove('d-none');
                if (label) label.textContent = 'Answer Options (check one correct)';
                const checks = document.querySelectorAll('.manual-correct-check');
                checks.forEach(c => {
                    c.type = 'radio';
                    c.name = 'manualCorrect';
                });
            } else if (cleanType.includes('true/false')) {
                if (tfSec) tfSec.classList.remove('d-none');
            } else if (cleanType.includes('short answer') || cleanType.includes('descriptive')) {
                if (shortSec) shortSec.classList.remove('d-none');
            }
        };

        App.renderManualSections = (sections) => {
            const container = document.getElementById('manual_sections_entry_container');
            if (!container) return;

            if (!sections || sections.length === 0) {
                container.innerHTML = `
                <div class="py-20 text-center border-2 border-dashed border-slate-100 rounded-3xl bg-slate-50/30">
                    <i class="bi bi-layout-text-sidebar text-4xl text-slate-200 mb-4 block"></i>
                    <p class="text-slate-400 font-bold text-[11px] uppercase tracking-widest">This template has no sections defined</p>
                </div>
            `;
                return;
            }

            container.innerHTML = sections.map((s, idx) => {
                const name = s.marks_type || s.name || 'Section';
                const count = s.num_questions || s.count || 0;
                const marks = s.marks_per_question || s.marks || 0;

                return `
                <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm mb-3">
                    <div class="p-3 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-600 text-white rounded-xl flex items-center justify-center text-[9px] font-black shadow-lg shadow-blue-100">
                                ${name.substring(0, 3).toUpperCase()}
                            </div>
                            <div>
                                <h5 class="text-[12px] font-black text-slate-800 mb-0 uppercase tracking-wide leading-none">${name}</h5>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[8px] text-slate-400 font-bold uppercase tracking-widest">${count} Questions</span>
                                    <div class="w-1 h-1 bg-slate-200 rounded-full"></div>
                                    <span class="text-[8px] text-slate-400 font-bold uppercase tracking-widest">${marks} Marks Each</span>
                                </div>
                            </div>
                        </div>
                        <button class="px-3 py-1.5 bg-white border border-slate-100 text-blue-600 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-sm" 
                                onclick="App.addNewManualQuestion(${idx}, '${name.replace(/'/g, "\\'")}', ${marks}, ${count})">
                            <i class="bi bi-plus-lg me-1"></i> Add Question
                        </button>
                    </div>
                    <div class="p-3">
                        <div id="section_questions_${idx}">
                            <!-- Table list injected here -->
                            <div class="py-10 text-center border border-dashed border-slate-100 rounded-2xl bg-slate-50/20">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center mx-auto mb-2 text-slate-200 shadow-sm">
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">No questions added yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }).join('');
        };

        App.downloadCurrentTemplate = () => {
            const inlineBuilder = document.getElementById('templateBuilderInlineView');
            const isBuilderActive = inlineBuilder && !inlineBuilder.classList.contains('hidden');

            if (isBuilderActive) {
                const sections = document.querySelectorAll('#builder_sections_container_inline > div:not(.empty-state)');
                if (sections.length === 0) {
                    Swal.fire({
                        title: 'Empty Structure',
                        text: 'Please add at least one section (e.g., MCQ or descriptive question) to your template before downloading.',
                        icon: 'warning',
                        confirmButtonColor: '#ef4444'
                    });
                    return;
                }

                // Generate Smart CSV based on current structure
                let csv = "Section Name,Question,Option A,Option B,Option C,Option D,Correct Answer,Marks\n";
                sections.forEach(s => {
                    const name = s.querySelector('input[type="text"]').value.replace(/"/g, '""');
                    const marks = s.querySelector('.sec-marks-inline').value;
                    // Add a placeholder row for each section
                    csv += `"${name}","[Type Question Here]","[Opt A]","[Opt B]","[Opt C]","[Opt D]","A","${marks}"\n`;
                });

                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement("a");
                link.setAttribute("href", url);
                link.setAttribute("download", "question_bulk_upload_template.csv");
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                Swal.fire({
                    title: 'Template Generated',
                    text: 'A custom CSV has been generated based on your current sections.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            const templateId = document.getElementById('baseTemplateSelect').value;
            if (!templateId) {
                Swal.fire('Error', 'Please select a template first', 'error');
                return;
            }
            window.location.href = `/Test/downloadTemplateByTemplateId/${templateId}`;
        };



        App.addQuestionManually = () => {
            const type = document.getElementById('manualQuestionType').value;
            const text = document.getElementById('manualQuestionText').value;
            const marks = document.getElementById('manualQuestionMarks').value;
            if (!text) { Swal.fire('Required', 'Please enter question text', 'error'); return; }

            let options = [];
            let correct_answer = '';

            if (type === 'MCQ') {
                const optInputs = document.querySelectorAll('#manualOptionsSection input[type="text"]');
                const radios = document.querySelectorAll('.manual-correct-check');
                optInputs.forEach((input, i) => {
                    if (input.value) {
                        options.push({ text: input.value, label: String.fromCharCode(65 + i) });
                        if (radios[i] && radios[i].checked) correct_answer = String.fromCharCode(65 + i);
                    }
                });
            } else if (type === 'True/False') {
                options = [{ text: 'True' }, { text: 'False' }];
                const tfRadios = document.getElementsByName('manualTF');
                tfRadios.forEach(r => { if (r.checked) correct_answer = r.value; });
            } else if (typeof App !== 'undefined' && App.normalizeType && App.normalizeType(type) === 'descriptive') {
                const saEl = document.getElementById('manualShortCorrectAnswer');
                correct_answer = saEl ? String(saEl.value || '').trim() : '';
            }

            const storedType = type === 'MCQ' ? 'MCQ'
                : (typeof App !== 'undefined' && App.normalizeType && App.normalizeType(type) === 'descriptive'
                    ? 'Short Answer'
                    : type);

            const qData = {
                question: text,
                type: storedType,
                marks: marks,
                option_a: options[0] ? options[0].text : '',
                option_b: options[1] ? options[1].text : '',
                option_c: options[2] ? options[2].text : '',
                option_d: options[3] ? options[3].text : '',
                correct_answer: correct_answer,
                category: 'Manual',
                id: 'm' + Date.now(),
                sectionIdx: App.currentTargetSectionIdx,
                pedagogy: getPedagogyComboValue('manualQuestionPedagogy')
            };

            App.manualQuestions.push(qData);

            if (App.currentTargetSectionIdx !== null) {
                App.refreshSectionQuestions(App.currentTargetSectionIdx);
            }

            bootstrap.Modal.getInstance(document.getElementById('addManualQuestionModal')).hide();

            // Reset form
            document.getElementById('manualQuestionText').value = '';
            document.querySelectorAll('#manualOptionsSection input[type="text"]').forEach(i => i.value = '');
            resetPedagogyCombo('manualQuestionPedagogy');

            Swal.fire({ title: 'Added!', text: 'Question added to section', icon: 'success', timer: 1000, showConfirmButton: false });
        };

        App.refreshSectionQuestions = (idx) => {
            const list = document.getElementById(`section_questions_${idx}`) || document.getElementById(`builder_section_questions_${idx}`);
            if (!list) return;

            const questions = App.manualQuestions.filter(q => q.sectionIdx == idx);
            if (questions.length === 0) {
                list.innerHTML = `
                <div class="py-12 text-center border border-dashed border-slate-100 rounded-2xl bg-slate-50/20">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mx-auto mb-3 text-slate-200 shadow-sm">
                        <i class="bi bi-pencil-square text-xl"></i>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No questions added yet</p>
                </div>
            `;
                return;
            }

            list.innerHTML = `
            <div class="bg-slate-50/50 rounded-xl overflow-hidden border border-slate-100">
                <div class="grid grid-cols-12 gap-0 bg-slate-100/80 border-b border-slate-200">
                    <div class="col-span-1 py-2 px-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">#</div>
                    <div class="col-span-2 py-2 px-2 text-[9px] font-black text-slate-400 uppercase tracking-widest">Pedagogy</div>
                    <div class="col-span-4 py-2 px-2 text-[9px] font-black text-slate-400 uppercase tracking-widest">Question</div>
                    <div class="col-span-4 py-2 px-2 text-[9px] font-black text-slate-400 uppercase tracking-widest">Options / expected</div>
                    <div class="col-span-1 py-2 px-3 text-right"></div>
                </div>
                <div class="divide-y divide-slate-100">
                    ${questions.map((q, qIdx) => {
                const isMCQ = (q.type || '').toLowerCase().includes('mcq') || (q.type || '').toLowerCase().includes('multiple choice');
                const pedRowBase = 'tmpl_ped_' + String(q.id).replace(/[^a-zA-Z0-9_-]/g, '_');

                return `
                            <div class="grid grid-cols-12 gap-0 bg-white hover:bg-slate-50/50 transition-colors animate-fadeIn border-b border-slate-50 last:border-0">
                                <div class="col-span-1 py-3 px-3 flex flex-col items-center justify-start gap-1">
                                    <span class="w-5 h-5 bg-slate-800 text-white rounded-lg flex items-center justify-center text-[8px] font-black">${qIdx + 1}</span>
                                    <span class="px-1 py-0.5 bg-blue-50 text-blue-600 rounded text-[7px] font-black uppercase">${q.marks}M</span>
                                </div>
                                <div class="col-span-2 py-3 px-2 flex items-start">
                                    ${pedagogyComboHtml(pedRowBase, q.pedagogy || '', { manualQuestionId: q.id, searchClass: 'w-full bg-slate-50 border border-slate-100 rounded-lg px-1.5 py-1.5 text-[10px] font-bold text-slate-700' })}
                                </div>
                                <div class="col-span-4 py-3 px-2">
                                    <textarea oninput="App.updateManualQuestion('${q.id}', 'question', this.value)" 
                                              class="w-full bg-slate-50 border-0 rounded-lg p-2 text-[11px] font-medium text-slate-700 focus:ring-2 focus:ring-red-100 transition-all" 
                                              rows="2" placeholder="Type your question here...">${q.question || ''}</textarea>
                                 </div>
                                 ${isMCQ ? `
                                 <div class="col-span-4 py-3 px-2">
                                     <div class="grid grid-cols-2 gap-2">
                                         ${['a', 'b', 'c', 'd'].map(opt => {
                    const isCorrect = q.correct_answer === opt.toUpperCase();
                    return `
                                             <div class="flex items-center gap-1.5 p-1 border ${isCorrect ? 'border-emerald-200 bg-emerald-50' : 'border-slate-100 bg-white'} rounded-lg transition-all overflow-hidden">
                                                 <span class="text-[8px] font-black ${isCorrect ? 'text-emerald-600' : 'text-slate-400'} uppercase w-2 flex-shrink-0">${opt}</span>
                                                 <input type="text" value="${q['option_' + opt] || ''}" 
                                                        oninput="App.updateManualQuestion('${q.id}', 'option_${opt}', this.value)" 
                                                        class="flex-1 bg-transparent border-0 focus:ring-0 text-[10px] font-bold p-0 text-slate-700 min-w-0" 
                                                        placeholder="...">
                                                 <input type="radio" name="correct_${q.id}" ${isCorrect ? 'checked' : ''} 
                                                        onchange="App.updateManualQuestion('${q.id}', 'correct_answer', '${opt.toUpperCase()}'); App.refreshSectionQuestions(${idx})" 
                                                        class="w-3 h-3 text-emerald-600 border-slate-200 flex-shrink-0">
                                             </div>
                                             `;
                }).join('')}
                                     </div>
                                 </div>
                                 ` : `
                                 <div class="col-span-4 py-3 px-2">
                                     <textarea oninput="App.updateManualQuestion('${q.id}', 'correct_answer', this.value)"
                                               class="w-full bg-slate-50 border border-slate-100 rounded-lg p-2 text-[10px] font-medium text-slate-700 focus:ring-2 focus:ring-blue-100 transition-all"
                                               rows="2" placeholder="Expected answer / reference...">${q.correct_answer || ''}</textarea>
                                 </div>
                                 `}
                                <div class="col-span-1 py-3 px-3 flex items-start justify-center">
                                    <div class="flex flex-col gap-1">
                                        <button class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all flex items-center justify-center" 
                                                onclick="App.saveManualQuestionRow('${q.id}')"
                                                title="Save Question">
                                            <i class="bi bi-floppy-fill text-[10px]"></i>
                                        </button>
                                        <button class="w-7 h-7 rounded-lg bg-slate-50 text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center" 
                                                onclick="App.removeManualQuestion('${q.id}', ${idx})"
                                                title="Delete Question">
                                            <i class="bi bi-trash-fill text-[10px]"></i>
                                        </button>
                                    </div>
                                 </div>
                            </div>
                        `;
            }).join('')}
                </div>
            </div>
        `;
        };

        App.removeManualQuestion = (id, sectionIdx) => {
            App.manualQuestions = App.manualQuestions.filter(q => q.id !== id);
            App.refreshSectionQuestions(sectionIdx);
        };

        App.handleRealBulkUpload = (input) => {
            if (!input.files || !input.files[0]) return;

            Swal.fire({
                title: 'Processing Upload...',
                text: 'Mapping questions to template structure.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const reader = new FileReader();
            reader.onload = function (e) {
                const text = e.target.result;
                const lines = text.split('\n');
                if (lines.length < 2) { Swal.fire('Error', 'Invalid or empty CSV file', 'error'); return; }

                const headers = lines.find(l => l && !l.startsWith('#')).split(',').map(h => h.trim().replace(/^"|"$/g, ''));
                const rows = lines.slice(lines.indexOf(lines.find(l => l && !l.startsWith('#'))) + 1);

                // Get current sections for mapping
                const currentTemplateId = document.getElementById('baseTemplateSelect').value;
                const template = App.templates.find(t => t.id == currentTemplateId);
                let sections = [];
                if (template) {
                    sections = template.sections || [];
                    if (typeof sections === 'string') {
                        try { sections = JSON.parse(sections); } catch (e) { sections = []; }
                    }
                }

                let addedCount = 0;
                rows.forEach(row => {
                    if (!row.trim() || row.startsWith('#')) return;
                    const cols = row.split(',').map(c => c.trim().replace(/^"|"$/g, ''));
                    const data = {};
                    headers.forEach((h, i) => data[h] = cols[i]);

                    if (data.question) {
                        // Try to find section index by name
                        let sectionIdx = 0;
                        if (data.section_name) {
                            const foundIdx = sections.findIndex(s => (s.marks_type || s.name) === data.section_name);
                            if (foundIdx !== -1) sectionIdx = foundIdx;
                        }

                        const qData = {
                            id: 'b' + Date.now() + Math.random(),
                            sectionIdx: sectionIdx,
                            question: data.question,
                            type: data.type || 'MCQ',
                            marks: data.marks || 1,
                            option_a: data.option_a || '',
                            option_b: data.option_b || '',
                            option_c: data.option_c || '',
                            option_d: data.option_d || '',
                            correct_answer: data.correct_answer || '',
                            category: 'Bulk',
                            pedagogy: (data.pedagogy || '').trim()
                        };
                        App.manualQuestions.push(qData);
                        addedCount++;
                    }
                });

                // Refresh all sections
                sections.forEach((_, idx) => App.refreshSectionQuestions(idx));

                input.value = '';
                Swal.fire('Success', `${addedCount} questions imported and mapped successfully.`, 'success');
            };
            reader.readAsText(input.files[0]);
        };

        App.previewQuestionPaper = () => {
            // Gather selected questions from the table
            const checkedInputs = document.querySelectorAll('#manualQuestionTableBody input[type="checkbox"]:checked');
            let questions = [];

            checkedInputs.forEach(input => {
                if (input.dataset.q) {
                    questions.push(JSON.parse(input.dataset.q));
                }
            });

            // Add manually added questions if any (though they are also added to the table)
            // To avoid duplicates, we only rely on the table state

            if (questions.length === 0) {
                Swal.fire('No Questions', 'Select at least one question to preview the paper.', 'info');
                return;
            }

            const totalMarks = questions.reduce((acc, q) => acc + parseInt(q.marks), 0);
            const container = document.getElementById('previewPaperContent');

            // Group by category
            const sections = {};
            questions.forEach(q => {
                if (!sections[q.category]) sections[q.category] = [];
                sections[q.category].push(q);
            });

            let sectionsHtml = '';
            let secIndex = 0;
            const secNames = ['A', 'B', 'C', 'D', 'E'];

            for (const [cat, qs] of Object.entries(sections)) {
                const secName = secNames[secIndex++] || 'X';
                sectionsHtml += `
                <div class="mb-10 px-8">
                    <div class="section-divider"></div>
                    <div class="d-flex justify-content-between align-items-center mb-6">
                        <h3 class="h5 section-title mb-0 text-uppercase tracking-wider">SECTION ${secName}</h3>
                        <div class="preview-pill">
                             <i class="bi bi-list-task text-danger"></i> ${qs.length} Questions | ${qs[0].marks} Marks each
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-4 text-slate-500 text-[12px] font-semibold">
                        <i class="bi bi-info-circle text-primary"></i> Question Type: ${qs[0].type === 'MCQ' ? 'Multiple Choice' : (App.normalizeType(qs[0].type) === 'descriptive' ? 'Descriptive question' : qs[0].type)}
                    </div>
                    
                    <div class="space-y-6">
                        ${qs.map((q, i) => `
                            <div class="paper-card">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div class="q-badge">Question ${i + 1}</div>
                                    <div class="marks-text"><i class="bi bi-star"></i> ${q.marks} Marks</div>
                                </div>
                                <div class="fw-bold text-slate-800 text-[15px] mb-4">${q.text}</div>
                                
                                <div class="ps-2">
                                    ${App.normalizeType(q.type) === 'descriptive' ? `
                                        <div class="border border-1 border-slate-200 rounded-xl p-6 text-slate-300 text-[12px] bg-slate-50/30">
                                            Student response area...
                                        </div>
                                    ` : `
                                        <div class="row g-3">
                                            ${(q.options || []).map((opt, oi) => `
                                                <div class="col-md-6">
                                                    <div class="option-box">
                                                        <div class="option-letter">${String.fromCharCode(65 + oi)}</div>
                                                        <div class="option-text">${opt.text || opt}</div>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    `}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            }

            container.innerHTML = `
            <div class="paper-preview-container mx-auto" style="max-width: 850px; min-height: 1000px;">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start mb-10 px-8">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #dc2230 0%, #a11a24 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative;">
                            <div style="width: 26px; height: 26px; border: 4px solid white; border-radius: 50%; border-right-color: transparent; transform: rotate(45deg);"></div>
                            <div style="position: absolute; width: 12px; height: 12px; background: white; border-radius: 50%; top: 12px; right: 12px;"></div>
                        </div>
                        <div class="text-start">
                            <div style="font-size: 28px; font-weight: 800; color: #dc2230; line-height: 1; letter-spacing: -1px;">ENOVA</div>
                            <div style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Software and Hardware Solutions Pvt Ltd</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 1px; height: 50px; background: #e2e8f0; margin: 0 10px;"></div>
                        <div class="text-end">
                            <h2 class="fw-bold text-slate-800 mb-2 text-xl tracking-tight text-uppercase">Manual Test Paper</h2>
                            <div class="d-flex justify-content-end gap-2">
                                <div class="preview-pill">
                                    <i class="bi bi-clock text-primary"></i> Duration: 60 Minutes
                                </div>
                                <div class="preview-pill">
                                    <i class="bi bi-star text-primary"></i> Total Marks: ${totalMarks}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-10">${sectionsHtml}</div>

                <div class="instruction-box mx-8">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="text-danger fs-4"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <h4 class="mb-0 fw-bold text-danger text-[16px] uppercase tracking-wider">Important Instructions</h4>
                    </div>
                    <ul class="text-[13px] text-slate-600 mb-0 ps-3 space-y-2 font-medium">
                        <li>Verify the question paper title and code before starting.</li>
                        <li>All questions are mandatory unless specified otherwise.</li>
                        <li>This paper contains <strong>${Object.keys(sections).length}</strong> sections.</li>
                        <li>Duration: <strong>60 minutes</strong>.</li>
                    </ul>
                </div>

                <div class="text-center mt-10 pt-8 border-top mx-8">
                    <div class="font-black text-slate-800 text-[12px] mb-1">© 2026 ENOVA TECHNOLOGY SOLUTIONS</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Official Test Document</div>
                </div>
            </div>
        `;

            const modalEl = document.getElementById('paperPreviewModal');
            const parentModalContent = document.querySelector('#createPackModal .modal-content');

            if (parentModalContent) parentModalContent.classList.add('modal-blur');

            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            modalEl.addEventListener('hidden.bs.modal', () => {
                if (parentModalContent) parentModalContent.classList.remove('modal-blur');
            }, { once: true });
        };

        async function previewTemplate(id, isSelection = false) {
            let t;
            const select = document.getElementById('baseTemplateSelect') || document.getElementById('ass_pack_template');
            if (select) {
                const opt = Array.from(select.options).find(o => o.value == id);
                if (opt && opt.dataset.json) {
                    try {
                        t = JSON.parse(opt.dataset.json);
                    } catch (e) { console.error("Error parsing template json", e); }
                }
            }

            if (!t) {
                const templates = <?= workflow_view_json($templates ?? []) ?>;
                t = templates.find(item => item.id == id);
            }

            if (!t) return;

            // Toggle footer buttons
            const defaultFooter = document.getElementById('defaultPreviewFooter');
            const selectionFooter = document.getElementById('selectionPreviewFooter');
            if (isSelection) {
                defaultFooter.classList.add('d-none');
                selectionFooter.classList.remove('d-none');
            } else {
                defaultFooter.classList.remove('d-none');
                selectionFooter.classList.add('d-none');
            }

            const container = document.getElementById('previewPaperContent');
            let sectionsHtml = '';

            t.sections.forEach((s, idx) => {
                sectionsHtml += `
                <div class="mb-10 px-8">
                    <div class="section-divider"></div>
                    <div class="d-flex justify-content-between align-items-center mb-6">
                        <h3 class="h5 section-title mb-0 text-uppercase tracking-wider">${s.section_name || 'Section ' + String.fromCharCode(65 + idx)}</h3>
                        <div class="preview-pill">
                             <i class="bi bi-list-task text-danger"></i> ${s.num_questions} Questions | ${s.marks_per_question} Marks each
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-4 text-slate-500 text-[12px] font-semibold">
                        <i class="bi bi-info-circle text-primary"></i> Question Type: ${(typeof App !== 'undefined' && App.normalizeType && App.normalizeType(s.marks_type || '') === 'descriptive') ? 'Descriptive question' : (String(s.marks_type || '').toUpperCase() === 'MCQ' || (typeof App !== 'undefined' && App.normalizeType && App.normalizeType(s.marks_type || '') === 'mcq') ? 'Multiple Choice' : s.marks_type)}
                    </div>
                    
                    <div class="space-y-6">
                        ${Array.from({ length: 2 }).map((_, qIdx) => `
                            <div class="paper-card">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div class="q-badge">Question ${qIdx + 1}</div>
                                    <div class="marks-text"><i class="bi bi-star"></i> ${s.marks_per_question} Marks</div>
                                </div>
                                <div class="fw-bold text-slate-800 text-[15px] mb-4">[Sample Question Text for ${s.section_name}]</div>
                                
                                <div class="ps-2">
                                    ${(typeof App !== 'undefined' && App.normalizeType && App.normalizeType(s.marks_type || '') === 'descriptive') ? `
                                        <div class="border border-1 border-slate-200 rounded-xl p-6 text-slate-300 text-[12px] bg-slate-50/30">
                                            Candidate response area...
                                        </div>
                                    ` : `
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="option-box">
                                                    <div class="option-letter">A</div>
                                                    <div class="option-text">Option 1</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="option-box">
                                                    <div class="option-letter">B</div>
                                                    <div class="option-text">Option 2</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="option-box">
                                                    <div class="option-letter">C</div>
                                                    <div class="option-text">Option 3</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="option-box">
                                                    <div class="option-letter">D</div>
                                                    <div class="option-text">Option 4</div>
                                                </div>
                                            </div>
                                        </div>
                                    `}
                                </div>
                            </div>
                        `).join('')}
                        <div class="more-questions-box">
                            + ${parseInt(s.num_questions) - 2} more questions in this section
                        </div>
                    </div>
                </div>
            `;
            });

            container.innerHTML = `
            <div class="paper-preview-container mx-auto" style="max-width: 850px; min-height: 1000px;">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start mb-10 px-8">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #dc2230 0%, #a11a24 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative;">
                            <div style="width: 26px; height: 26px; border: 4px solid white; border-radius: 50%; border-right-color: transparent; transform: rotate(45deg);"></div>
                            <div style="position: absolute; width: 12px; height: 12px; background: white; border-radius: 50%; top: 12px; right: 12px;"></div>
                        </div>
                        <div class="text-start">
                            <div style="font-size: 28px; font-weight: 800; color: #dc2230; line-height: 1; letter-spacing: -1px;">ENOVA</div>
                            <div style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Software and Hardware Solutions Pvt Ltd</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 1px; height: 50px; background: #e2e8f0; margin: 0 10px;"></div>
                        <div class="text-end">
                            <h2 class="fw-bold text-slate-800 mb-2 text-xl tracking-tight text-uppercase">${t.paper_title || t.name}</h2>
                            <div class="d-flex justify-content-end gap-2">
                                <div class="preview-pill">
                                    <i class="bi bi-clock text-primary"></i> Duration: ${t.duration || '60'} Minutes
                                </div>
                                <div class="preview-pill">
                                    <i class="bi bi-star text-primary"></i> Total Marks: ${t.total_marks || 0}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-10">${sectionsHtml}</div>

                <div class="instruction-box mx-8">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="text-danger fs-4"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <h4 class="mb-0 fw-bold text-danger text-[16px] uppercase tracking-wider">Important Instructions</h4>
                    </div>
                    <ul class="text-[13px] text-slate-600 mb-0 ps-3 space-y-2 font-medium">
                        <li>Verify the question paper title and code before starting.</li>
                        <li>All questions are mandatory unless specified otherwise.</li>
                        <li>This paper contains <strong>${t.sections.length}</strong> sections.</li>
                        <li>Duration: <strong>${t.duration || '60'} minutes</strong>.</li>
                    </ul>
                </div>

                <div class="text-center mt-10 pt-8 border-top mx-8">
                    <div class="font-black text-slate-800 text-[12px] mb-1">© 2026 ENOVA TECHNOLOGY SOLUTIONS</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Official Test Document</div>
                </div>
            </div>
        `;

            const modal = new bootstrap.Modal(document.getElementById('paperPreviewModal'));
            modal.show();
        }

        // --- Unified Template Actions ---
        window.cloneTemplate = function (id, event) {
            if (event) event.stopPropagation();

            const quickModal = document.getElementById('createPackModal');
            const isQuickSetupContext = !!(quickModal && quickModal.classList.contains('show'));

            if (isQuickSetupContext) {
                // In Quick Setup discovery: open clone in editable create-mode builder.
                openTemplateCloneBuilderInline(id);
                Swal.fire({
                    title: 'Template Cloned!',
                    text: 'Structure copied. Select a Question Bank and save as a new template.',
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                // In standalone sidebar builder
                loadTemplateToBuilder(id, null, true); // true = isClone
            }
        };

        window.editTemplate = function (id, event) {
            if (event) event.stopPropagation();
            const templates = App.templates || [];
            const t = templates.find(item => item.id == id);
            if (!t) return;

            // Open builder and load data
            if (document.getElementById('createPackModal').classList.contains('show')) {
                // Inline Wizard Edit
                loadTemplateToBuilderInline(id, true);
            } else {
                // Sidebar Builder Edit
                openTemplateBuilder();
                loadTemplateToBuilder(id, null, false);
            }
        };

        window.deleteTemplate = async function (id, event) {
            if (event) event.stopPropagation();

            const result = await Swal.fire({
                title: 'Delete Template?',
                text: "This action cannot be undone. Any batches using this template might be affected.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2230',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/Test/deleteTemplate/${id}`, { method: 'POST' });
                    const res = await response.json();

                    if (res.status === 'success') {
                        // Update Local State
                        App.templates = App.templates.filter(t => t.id != id);

                        // Remove from UI
                        const card = document.getElementById('temp_card_' + id);
                        if (card) card.remove();

                        const builderCard = document.getElementById('builder_temp_' + id);
                        if (builderCard) builderCard.remove();

                        Swal.fire('Deleted!', 'Template has been removed.', 'success');
                    } else {
                        throw new Error(res.message);
                    }
                } catch (e) {
                    Swal.fire('Error', 'Failed to delete template: ' + e.message, 'error');
                }
            }
        };

        App.calculateDuration = () => {
            const start = document.getElementById('final_start_time').value;
            const end = document.getElementById('final_end_time').value;

            if (start && end) {
                const startDate = new Date(start);
                const endDate = new Date(end);
                const diffInMs = endDate - startDate;

                if (diffInMs > 0) {
                    const diffInMins = Math.floor(diffInMs / 60000);
                    document.getElementById('final_duration').value = diffInMins;
                    const rev = document.getElementById('rev_duration');
                    if (rev) rev.textContent = diffInMins + ' mins';
                } else {
                    document.getElementById('final_duration').value = '';
                    const rev = document.getElementById('rev_duration');
                    if (rev) rev.textContent = '-- mins';
                }
            }
        };

        App.updateManualCount = () => {
            const count = document.querySelectorAll('#manualQuestionTableBody input[type="checkbox"]:checked').length;
            const countEl = document.getElementById('selectedQuestionsCount');
            const required = App.requiredQuestionsTotal || 0;

            if (countEl) {
                countEl.textContent = `${count} / ${required} required`;
                countEl.classList.toggle('text-danger', count < required);
                countEl.classList.toggle('text-success', count >= required);
            }
        };

        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof window.initTestsDataTable === 'function') window.initTestsDataTable();
            if (typeof App.loadEvaluationState === 'function') App.loadEvaluationState();
            if (typeof App.loadCandidateResult === 'function') App.loadCandidateResult();

            const previewModal = document.getElementById('paperPreviewModal');
            if (previewModal) {
                previewModal.addEventListener('hidden.bs.modal', function () {
                    // If the modal was closed (X, ESC, click outside) while a template was pending
                    // we should reset the dropdown.
                    if (pendingTemplateId) {
                        const select = document.getElementById('baseTemplateSelect');
                        if (select) select.value = '';

                        const previewBtn = document.getElementById('previewTemplateBtn');
                        if (previewBtn) previewBtn.classList.add('hidden');

                        pendingTemplateId = null;
                    }
                });
            }
        });

        let pendingTemplateId = null;
        function handleWizardTemplateChange(val) {
            if (!val) return;
            pendingTemplateId = val;
            previewTemplate(val, true);
        }

        function confirmTemplateSelection() {
            const wizardSelect = document.getElementById('ass_pack_template');
            const baseSelect = document.getElementById('baseTemplateSelect');

            // Hide modal
            const modalEl = document.getElementById('paperPreviewModal');
            const inst = bootstrap.Modal.getInstance(modalEl);
            if (inst) inst.hide();

            if (wizardSelect && wizardSelect.value) {
                // Wizard context - updateTemplatePreview already set the summary
                console.log('Confirmed wizard template:', wizardSelect.value);
            } else if (pendingTemplateId && baseSelect) {
                // Base template context
                const val = pendingTemplateId;
                pendingTemplateId = null;
                baseSelect.value = val;
                App.onTemplateSelect(val);
                const previewBtn = document.getElementById('previewTemplateBtn');
                if (previewBtn) previewBtn.classList.remove('hidden');
            }
        }

        function cancelTemplateSelection() {
            pendingTemplateId = null;

            const wizardSelect = document.getElementById('ass_pack_template');
            const baseSelect = document.getElementById('baseTemplateSelect');

            if (wizardSelect && wizardSelect.offsetParent !== null) {
                wizardSelect.value = '';
                const card = document.getElementById('template_info_card');
                if (card) card.classList.add('hidden');
            } else if (baseSelect) {
                baseSelect.value = '';
                const previewBtn = document.getElementById('previewTemplateBtn');
                if (previewBtn) previewBtn.classList.add('hidden');
            }

            const modalEl = document.getElementById('paperPreviewModal');
            const inst = bootstrap.Modal.getInstance(modalEl);
            if (inst) inst.hide();
        }

        function previewSelectedTemplate() {
            const val = document.getElementById('baseTemplateSelect').value;
            if (val) {
                previewTemplate(val, false);
            }
        }

        function setTestAndOpenPack(id) {
            const select = document.getElementById('packTestName');
            if (select) select.value = id;
            openPackWizard();
        }

        App.onTemplateSelect = (val) => {
            const select = document.getElementById('baseTemplateSelect');
            const option = select.options[select.selectedIndex];
            if (!option || !option.getAttribute('data-json')) return;

            const data = JSON.parse(option.getAttribute('data-json'));
            document.getElementById('rev_template').textContent = data.name;

            // Calculate total questions required
            let total = 0;
            if (data.sections) {
                data.sections.forEach(s => total += parseInt(s.num_questions || 0));
            }
            App.requiredQuestionsTotal = total;
            App.updateManualCount();
        };

        App.updateManualCount = () => {
            const checked = document.querySelectorAll('#manualQuestionTableBody input[type="checkbox"]:checked');
            const countEl = document.getElementById('selectedQuestionsCount');
            if (countEl) {
                const total = App.requiredQuestionsTotal || 0;
                countEl.textContent = `${checked.length} / ${total} required`;

                if (checked.length >= total) {
                    countEl.classList.replace('text-danger', 'text-success');
                } else {
                    countEl.classList.replace('text-success', 'text-danger');
                }
            }
        };

        async function publishFinalTest() {
            const assessment_id = document.getElementById('packTestName').value;
            const template_id = document.getElementById('baseTemplateSelect').value;
            const pack_name = document.getElementById('pack_wizard_name').value;
            const duration = document.getElementById('final_duration').value;
            const startTime = document.getElementById('final_start_time').value;
            const endTime = document.getElementById('final_end_time').value;
            const user_role = document.getElementById('packCategorySelect') ? document.getElementById('packCategorySelect').value : 'Internal';

            if (!assessment_id || !template_id || !pack_name || !duration || !startTime || !endTime) {
                Swal.fire('Error', 'Please complete all required fields (Start Time, End Time, Duration) before publishing.', 'error');
                return;
            }

            if (new Date(startTime) >= new Date(endTime)) {
                Swal.fire('Error', 'End Date & Time must be after the Start Date & Time.', 'error');
                return;
            }

            const confirm = await Swal.fire({
                title: 'Publish Test?',
                text: "This will create the Batch and link it to the selected Test and template.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Publish Now',
                confirmButtonColor: '#dc2230'
            });

            if (confirm.isConfirmed) {
                // Hide modal immediately for instant feedback
                const modalEl = document.getElementById('createPackModal');
                if (modalEl) {
                    const inst = bootstrap.Modal.getInstance(modalEl);
                    if (inst) inst.hide();
                }

                Swal.fire({
                    title: 'Publishing...',
                    text: 'Creating your Batch.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                const response = await fetch('Test/createTestPack', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `assessment_id=${assessment_id}&template_id=${template_id}&pack_name=${encodeURIComponent(pack_name)}&user_role=${encodeURIComponent(user_role)}&duration=${duration}`
                });
                const result = await response.json();
                if (result.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Batch has been published.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', result.message || 'Failed to publish.', 'error');
                }
            }
        }

        // --- Legacy Actions Support ---
        window.startExecutionMode = () => window.switchMainTab('execution');
        window.changeQuestion = (dir) => {
            if (dir > 0) App.nextQuestion();
            else App.prevQuestion();
        };
        window.toggleFlag = (idx) => App.toggleFlag(idx);
        window.confirmSubmitTest = () => App.confirmSubmit();

        let currentEditTestId = null;
        let testIntroVideoFrozen = false;

        let assIntroVideoUrls = [];
        let assIntroVideoFiles = [];

        function parseTestIntroVideosRow(data) {
            if (!data || data.intro_videos == null) return [];
            if (Array.isArray(data.intro_videos)) return data.intro_videos;
            if (typeof data.intro_videos === 'string') {
                try {
                    const p = JSON.parse(data.intro_videos);
                    return Array.isArray(p) ? p : [];
                } catch (e) {
                    return data.intro_videos
                        .split(/[\n,]/)
                        .map(x => String(x || '').trim())
                        .filter(Boolean);
                }
            }
            return [];
        }

        function syncIntroVideoUploadColumn() {
            const sel = document.getElementById('ass_add_video');
            const col = document.getElementById('ass_intro_upload_col');
            if (!sel || !col) return;
            const yes = sel.value === 'Yes';
            col.classList.toggle('hidden', !yes);
            if (!yes && !testIntroVideoFrozen) {
                assIntroVideoFiles = [];
                const fi = document.getElementById('ass_intro_video_input');
                if (fi) fi.value = '';
            }
            renderAssIntroVideoChips();
        }

        function setIntroVideoUploaderFrozen(frozen) {
            testIntroVideoFrozen = !!frozen;
            const sel = document.getElementById('ass_add_video');
            const fi = document.getElementById('ass_intro_video_input');
            const browse = document.getElementById('ass_intro_video_browse_btn');
            const grp = document.getElementById('ass_intro_upload_col');
            if (sel) {
                sel.disabled = testIntroVideoFrozen;
                sel.classList.toggle('opacity-60', testIntroVideoFrozen);
                sel.classList.toggle('cursor-not-allowed', testIntroVideoFrozen);
                sel.setAttribute('aria-disabled', testIntroVideoFrozen ? 'true' : 'false');
            }
            if (fi) fi.disabled = testIntroVideoFrozen;
            if (browse) {
                browse.disabled = testIntroVideoFrozen;
                browse.classList.toggle('opacity-50', testIntroVideoFrozen);
                browse.classList.toggle('pointer-events-none', testIntroVideoFrozen);
            }
            if (grp) grp.classList.toggle('opacity-80', testIntroVideoFrozen && sel && sel.value === 'Yes');
            renderAssIntroVideoChips();
        }

        function renderAssIntroVideoChips() {
            const wrap = document.getElementById('ass_intro_video_chips');
            const cnt = document.getElementById('ass_intro_video_count');
            const n = assIntroVideoUrls.length + assIntroVideoFiles.length;
            if (cnt) cnt.textContent = n + ' / 5';
            if (!wrap) return;
            let html = '';
            assIntroVideoUrls.forEach((url, i) => {
                const rm = testIntroVideoFrozen ? '' :
                    '<button type="button" class="border-0 bg-transparent text-red-500 p-0 ms-1" onclick="removeAssIntroUrl(' + i + ')">&times;</button>';
                html += '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-[9px] font-bold text-slate-700 max-w-[140px] truncate" title="Saved video">' +
                    'Saved ' + (i + 1) + rm + '</span>';
            });
            assIntroVideoFiles.forEach((f, i) => {
                const nm = String(f.name || 'file').replace(/</g, '&lt;');
                const rm = testIntroVideoFrozen ? '' :
                    '<button type="button" class="border-0 bg-transparent text-red-500 p-0 ms-1" onclick="removeAssIntroFile(' + i + ')">&times;</button>';
                html += '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-50 text-[9px] font-bold text-red-700 max-w-[140px] truncate">' + nm + rm + '</span>';
            });
            wrap.innerHTML = html || '<span class="text-[9px] text-slate-400">No files yet</span>';
        }

        function removeAssIntroUrl(idx) {
            if (testIntroVideoFrozen) return;
            assIntroVideoUrls.splice(idx, 1);
            renderAssIntroVideoChips();
        }

        function removeAssIntroFile(idx) {
            if (testIntroVideoFrozen) return;
            assIntroVideoFiles.splice(idx, 1);
            renderAssIntroVideoChips();
        }

        function onAssIntroVideoFilesChange(input) {
            if (testIntroVideoFrozen) return;
            const picked = Array.from(input.files || []);
            if (input) input.value = '';
            const maxTotal = 5;
            const room = maxTotal - assIntroVideoUrls.length - assIntroVideoFiles.length;
            if (room <= 0) return;
            assIntroVideoFiles.push(...picked.slice(0, room));
            renderAssIntroVideoChips();
        }

        function toggleEnovaFields(category) {
            const enovaFields = document.getElementById('enova_extra_fields');
            if (enovaFields) {
                if (category === 'Enova') {
                    enovaFields.classList.remove('hidden');
                } else {
                    enovaFields.classList.add('hidden');
                }
            }
        }

        let wizardStep = 1;
        let wizardQuestions = [];
        let selectedWizardQuestions = new Set();
        let currentWizardTemplate = null;

        function navigateWizard(dir) {
            if (dir === 1 && !validateWizardStep(wizardStep)) return;

            wizardStep += dir;
            updateWizardUI();
        }

        function updateWizardUI() {
            // Update Steps UI
            document.querySelectorAll('.step-item').forEach(item => {
                const step = parseInt(item.dataset.step);
                const circle = item.querySelector('div');

                if (step < wizardStep) {
                    circle.className = 'w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center font-bold z-10';
                    circle.innerHTML = '<i class="bi bi-check-lg"></i>';
                } else if (step === wizardStep) {
                    circle.className = 'w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center font-bold shadow-lg shadow-red-100 z-10';
                    circle.textContent = step;
                } else {
                    circle.className = 'w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold z-10';
                    circle.textContent = step;
                }
            });

            // Show/Hide Panes
            document.querySelectorAll('.wizard-pane').forEach((p, idx) => {
                p.classList.toggle('hidden', idx + 1 !== wizardStep);
            });

            // Navigation Buttons
            document.getElementById('btnWizardPrev').classList.toggle('hidden', wizardStep === 1);
            document.getElementById('btnWizardNext').classList.toggle('hidden', wizardStep === 4);
            document.getElementById('btnWizardSubmit').classList.toggle('hidden', wizardStep !== 4);

            if (wizardStep === 3) loadWizardQuestions();
            if (wizardStep === 4) populateWizardSummary();
        }

        function validateWizardStep(step) {
            if (step === 1) {
                if (!document.getElementById('ass_name').value.trim()) { Swal.fire('Error', 'Test Name is required', 'error'); return false; }
                if (!document.getElementById('ass_code').value.trim()) { Swal.fire('Error', 'Test Code is required', 'error'); return false; }
                return true;
            }
            if (step === 2) {
                if (!document.getElementById('ass_pack_name').value.trim()) { Swal.fire('Error', 'Pack Name is required', 'error'); return false; }
                if (!document.getElementById('ass_pack_template').value) { Swal.fire('Error', 'Please select a template', 'error'); return false; }
                return true;
            }
            return true;
        }

        function updateTemplatePreview(val) {
            if (!val) return;

            const select = document.getElementById('ass_pack_template');
            const opt = select.options[select.selectedIndex];
            if (!opt || !opt.dataset.json) return;

            currentWizardTemplate = JSON.parse(opt.dataset.json);

            // Update summary card UI
            const card = document.getElementById('template_info_card');
            const text = document.getElementById('template_summary_text');
            if (card && text) {
                card.classList.remove('hidden');
                text.innerHTML = `
                <strong>${currentWizardTemplate.name}</strong><br>
                Total Questions: ${currentWizardTemplate.total_questions || 'N/A'} | 
                Total Marks: ${currentWizardTemplate.total_marks || 'N/A'} | 
                Duration: ${currentWizardTemplate.duration || '60'} mins
            `;
            }

            if (document.getElementById('ass_required_q_count')) {
                document.getElementById('ass_required_q_count').textContent = currentWizardTemplate.total_questions || '--';
            }

            // Show the full preview with OK/Cancel
            previewTemplate(val, true);
        }

        function toggleWizardQuestion(id, checked) {
            if (checked) selectedWizardQuestions.add(id.toString());
            else selectedWizardQuestions.delete(id.toString());
            document.getElementById('ass_selected_q_count').textContent = selectedWizardQuestions.size;
        }

        function selectAllWizardQuestions(checked) {
            document.querySelectorAll('.wizard-q-check').forEach(cb => {
                cb.checked = checked;
                toggleWizardQuestion(cb.value, checked);
            });
        }

        function switchQuestionMethod(method) {
            document.getElementById('q_manual_view').classList.toggle('hidden', method !== 'manual');
            document.getElementById('q_bulk_view').classList.toggle('hidden', method !== 'bulk');
            document.getElementById('btn-q-manual').classList.toggle('active', method === 'manual');
            document.getElementById('btn-q-bulk').classList.toggle('active', method === 'bulk');
        }

        function calculateWizardDuration() {
            const start = document.getElementById('ass_start_time').value;
            const end = document.getElementById('ass_end_time').value;
            if (!start || !end) return;

            const sTime = new Date(`2000-01-01 ${start}`);
            const eTime = new Date(`2000-01-01 ${end}`);
            const diff = (eTime - sTime) / (1000 * 60);

            document.getElementById('wizard_duration_label').textContent = diff > 0 ? `${diff} Minutes` : 'Invalid Range';
        }

        function switchCandidateTab(type) {
            document.getElementById('wizard_internal_view').classList.toggle('hidden', type !== 'internal');
            document.getElementById('wizard_recruitment_view').classList.toggle('hidden', type !== 'recruitment');
            document.getElementById('btn-c-internal').classList.toggle('active', type === 'internal');
            document.getElementById('btn-c-recruitment').classList.toggle('active', type === 'recruitment');
        }

        function selectAllWizardCandidates(type, checked) {
            document.querySelectorAll(`.wizard-c-check.${type}`).forEach(cb => {
                cb.checked = checked;
            });
            updateSelectedCandidateCount();
        }

        function updateSelectedCandidateCount() {
            const selected = document.querySelectorAll('.wizard-c-check:checked').length;
            document.getElementById('wizard_selected_c_count').textContent = selected;
            document.getElementById('summary_c_count').textContent = selected;
        }

        function filterWizardQuestions(query) {
            const rows = document.querySelectorAll('.wizard-q-row');
            rows.forEach(row => {
                const text = row.querySelector('.font-bold').textContent.toLowerCase();
                row.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
            });
        }

        function loadWizardQuestions() {
            // No longer need mock load since it's PHP rendered now
            document.getElementById('ass_selected_q_count').textContent = selectedWizardQuestions.size;
        }

        function populateWizardSummary() {
            // Function retained to prevent runtime errors if called, 
            // but UI elements (Final Summary) have been removed.
            updateSelectedCandidateCount();
        }

        async function finalizeWizard() {
            const candidates = []; // Candidates assignment removed from wizard

            Swal.fire({ title: 'Publishing...', didOpen: () => Swal.showLoading() });

            const data = {
                name: document.getElementById('ass_name').value,
                code: document.getElementById('ass_code').value,
                category: document.getElementById('ass_category').value,
                assessment_type: document.getElementById('ass_type').value,
                assigned_to: Array.from(document.getElementById('ass_assigned').selectedOptions).map(o => o.value).join(','),
                description: document.getElementById('ass_desc').value,
                instructions: document.getElementById('ass_instructions').value,

                // Configuration Toggles
                shuffle_questions: document.getElementById('set_shuffle_q').checked,
                shuffle_options: document.getElementById('set_shuffle_o').checked,
                proctored_exam: document.getElementById('set_proctored').checked,
                browser_lockdown: document.getElementById('set_lockdown').checked,
                show_results: document.getElementById('set_show_results').checked,
                allow_backtracking: document.getElementById('set_backtracking').checked,

                // Passing & Attempts
                pass_mark: document.getElementById('ass_pass_mark').value,
                attempts: document.getElementById('ass_attempts').value,

                pack_name: document.getElementById('ass_pack_name').value,
                template_id: document.getElementById('ass_pack_template').value,
                questions: Array.from(selectedWizardQuestions),
                candidates: candidates,
                publish_date: document.getElementById('ass_publish_date').value,
                duration: document.getElementById('ass_wizard_duration').value,
                start_time: document.getElementById('ass_start_time').value,
                end_time: document.getElementById('ass_end_time').value,
                notify: false
            };

            try {
                const resp = await fetch('Test/createTest', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const res = await resp.json();
                if (res.status === 'success') Swal.fire('Success!', 'Test & Batch Published!', 'success').then(() => location.reload());
                else Swal.fire('Error', res.message, 'error');
            } catch (e) { Swal.fire('Error', 'Failed to save Test.', 'error'); }
        }

        function resetCreateTestExamConfigDefaults() {
            const map = [['test_form_proctored', true], ['test_form_lockdown', false], ['test_form_show_results', false], ['test_form_backtrack', false]];
            map.forEach(([id, on]) => {
                const el = document.getElementById(id);
                if (el) el.checked = on;
            });
            const vid = document.getElementById('ass_add_video');
            if (vid) vid.value = 'No';
        }

        function toggleCreateTest() {
            const panel = document.getElementById('TestModal');
            if (panel && panel.classList.contains('open')) {
                closeModal('TestModal');
                updateNewTestButtonState(false);
                return;
            }
            openCreateTest();
            updateNewTestButtonState(true);
        }

        function updateNewTestButtonState(isOpen) {
            const btn = document.getElementById('btn_new_test_toggle');
            const label = document.getElementById('btn_new_test_label');
            if (!btn || !label) return;
            if (isOpen) {
                btn.classList.add('is-active');
                label.textContent = 'Close New Test';
            } else {
                btn.classList.remove('is-active');
                label.textContent = 'New Test Name';
            }
        }

        function openCreateTest() {
            currentEditTestId = null;
            setIntroVideoUploaderFrozen(false);

            document.querySelectorAll('#TestModal input, #TestModal select, #TestModal textarea').forEach(el => {
                if (el.type === 'checkbox') el.checked = false;
                else if (el.tagName === 'SELECT') {
                    el.selectedIndex = 0;
                } else el.value = '';
            });

            // Reset custom multiselect
            document.querySelectorAll('#multiselect_options input[type="checkbox"]').forEach(cb => { cb.checked = false; });
            updateMultiselectLabel();

            // Exam toggles / Add Video (after generic reset clears checkboxes / first select option)
            resetCreateTestExamConfigDefaults();
            const passMarkInput = document.getElementById('ass_pass_mark');
            if (passMarkInput) passMarkInput.value = '60';
            assIntroVideoUrls = [];
            assIntroVideoFiles = [];
            renderAssIntroVideoChips();
            syncIntroVideoUploadColumn();

            // Reset titles for Create mode
            document.querySelector('#TestModal h3').textContent = 'Create New Test';
            const sub = document.getElementById('test_form_subtitle');
            if (sub) sub.textContent = 'Configure your assessment basic details';
            const submitBtn = document.querySelector('#TestModal button[onclick^="createTest"], #TestModal button[onclick^="updateTest"]');
            if (submitBtn) {
                submitBtn.innerHTML = 'Create Test <i class="bi bi-rocket-takeoff"></i>';
                submitBtn.setAttribute('onclick', 'createTest()');
            }

            toggleEnovaFields(document.getElementById('ass_category').value);

            clearValidationErrors();
            openModal('TestModal');
        }

        function editTestById(id) {
            const data = App.Tests.find(t => String(t.id) === String(id));
            if (data) editTest(data);
        }
        window.editTestById = editTestById;

        function editTest(data) {
            currentEditTestId = data.id;
            document.getElementById('ass_name').value = data.name;
            document.getElementById('ass_code').value = data.code || '';
            document.getElementById('ass_category').value = data.category || '';

            toggleEnovaFields(data.category || '');

            if (document.getElementById('ass_type')) document.getElementById('ass_type').value = data.assessment_type || '';
            if (document.getElementById('ass_assigned') && data.assigned_to) {
                const values = data.assigned_to.split(',');
                // Update custom multiselect checkboxes
                document.querySelectorAll('#multiselect_options input[type="checkbox"]').forEach(cb => {
                    cb.checked = values.includes(cb.value);
                });
                updateMultiselectLabel();
            }
            const rawInstr = data.instructions != null && String(data.instructions).length ? data.instructions : '';
            document.getElementById('ass_desc').value = rawInstr || data.description || '';

            const yn = (v) => v === true || v === 1 || v === '1' || v === 'true';
            const p = document.getElementById('test_form_proctored');
            const l = document.getElementById('test_form_lockdown');
            const sr = document.getElementById('test_form_show_results');
            const bt = document.getElementById('test_form_backtrack');
            if (p) p.checked = yn(data.proctored_exam);
            if (l) l.checked = yn(data.browser_lockdown);
            if (sr) sr.checked = yn(data.show_results);
            if (bt) bt.checked = yn(data.allow_backtracking);
            const av = document.getElementById('ass_add_video');
            if (av) av.value = yn(data.add_video) ? 'Yes' : 'No';
            const pm = document.getElementById('ass_pass_mark');
            if (pm) pm.value = (data.pass_mark ?? 60);

            assIntroVideoUrls = parseTestIntroVideosRow(data);
            assIntroVideoFiles = [];
            renderAssIntroVideoChips();
            syncIntroVideoUploadColumn();
            setIntroVideoUploaderFrozen(true);

            document.querySelector('#TestModal h3').textContent = 'Edit Test';
            const sub = document.getElementById('test_form_subtitle');
            if (sub) sub.textContent = 'Update your assessment details';
            const submitBtn = document.querySelector('#TestModal button[onclick^="createTest"], #TestModal button[onclick^="updateTest"]');
            if (submitBtn) {
                submitBtn.innerHTML = 'Update Test <i class="bi bi-pencil-square"></i>';
                submitBtn.setAttribute('onclick', 'updateTest()');
            }

            clearValidationErrors();
            openModal('TestModal');
        }

        async function updateTest() {
            if (!validateTestForm()) return;

            closeModal('TestModal');

            const id = currentEditTestId;
            const name = document.getElementById('ass_name').value;
            const code = document.getElementById('ass_code').value;
            const category = document.getElementById('ass_category').value;
            const type = document.getElementById('ass_type') ? document.getElementById('ass_type').value : null;
            const assignedSelect = document.getElementById('ass_assigned');
            const assigned = assignedSelect ? Array.from(assignedSelect.selectedOptions).map(o => o.value).join(',') : null;
            const desc = document.getElementById('ass_desc').value.trim();
            const addVideoEl = document.getElementById('ass_add_video');
            const addVideo = addVideoEl && addVideoEl.value === 'Yes';
            const g = (id) => { const e = document.getElementById(id); return e ? e.checked : false; };

            Swal.fire({
                title: 'Updating...',
                text: 'Saving your changes.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                if (!testIntroVideoFrozen && assIntroVideoFiles.length > 0) {
                    const fd = new FormData();
                    assIntroVideoFiles.forEach(f => fd.append('videos[]', f));
                    const up = await fetch(`Test/uploadIntroVideos/${id}`, { method: 'POST', body: fd });
                    const uj = await up.json().catch(() => ({}));
                    if (uj.status === 'success' && Array.isArray(uj.intro_videos)) {
                        assIntroVideoUrls = uj.intro_videos;
                        assIntroVideoFiles = [];
                    } else {
                        throw new Error(uj.message || 'Intro video upload failed');
                    }
                }

                const response = await fetch(`/Test/updateTest/${id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name, code, category, assessment_type: type, assigned_to: assigned,
                        description: desc.substring(0, 500),
                        instructions: desc,
                        proctored_exam: g('test_form_proctored'),
                        browser_lockdown: g('test_form_lockdown'),
                        show_results: g('test_form_show_results'),
                        allow_backtracking: g('test_form_backtrack'),
                        add_video: addVideo,
                        intro_videos: addVideo ? assIntroVideoUrls : []
                    })
                });
                if (response.ok) {
                    Swal.fire({ title: 'Updated!', text: 'Test has been updated', icon: 'success', timer: 2000, showConfirmButton: false }).then(() => location.reload());
                } else {
                    throw new Error();
                }
            } catch (e) {
                Swal.fire('Error', 'Failed to update Test.', 'error');
            }
        }

        async function deleteTest(id) {
            if (!(await Swal.fire({ title: 'Delete Test?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true }).then(r => r.isConfirmed))) return;

            Swal.fire({
                title: 'Deleting...',
                text: 'Removing the Test.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            await fetch(`/Test/deleteTest/${id}`, { method: 'POST' });
            location.reload();
        }

        async function createTest() {
            if (!validateTestForm()) return;

            closeModal('TestModal');

            const name = document.getElementById('ass_name').value;
            const code = document.getElementById('ass_code').value;
            const category = document.getElementById('ass_category').value;
            const type = category === 'Enova' ? document.getElementById('ass_type').value : null;
            const assignedSelect = document.getElementById('ass_assigned');
            const assigned = category === 'Enova' ? Array.from(assignedSelect.selectedOptions).map(o => o.value).join(',') : null;
            const desc = document.getElementById('ass_desc').value.trim();
            const addVideoEl = document.getElementById('ass_add_video');
            const addVideo = addVideoEl && addVideoEl.value === 'Yes';
            const g = (id) => { const e = document.getElementById(id); return e ? e.checked : false; };

            Swal.fire({
                title: 'Creating...',
                text: 'Setting up new Test.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const response = await fetch('Test/createTest', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name, code, category,
                        assessment_type: type,
                        assigned_to: assigned,
                        description: desc.substring(0, 500),
                        instructions: desc,
                        proctored_exam: g('test_form_proctored'),
                        browser_lockdown: g('test_form_lockdown'),
                        show_results: g('test_form_show_results'),
                        allow_backtracking: g('test_form_backtrack'),
                        add_video: addVideo
                    })
                });
                const result = await response.json().catch(() => ({}));
                if (!response.ok || result.status !== 'success') {
                    throw new Error(result.message || 'Server rejected create request');
                }

                const newId = result.id;
                if (newId && assIntroVideoFiles.length > 0) {
                    const fd = new FormData();
                    assIntroVideoFiles.forEach(f => fd.append('videos[]', f));
                    const up = await fetch(`Test/uploadIntroVideos/${newId}`, { method: 'POST', body: fd });
                    const uj = await up.json().catch(() => ({}));
                    if (uj.status !== 'success') {
                        Swal.fire({ title: 'Partial save', text: 'Test was created but intro video upload failed. You can edit the test to retry.', icon: 'warning', timer: 3500, showConfirmButton: false }).then(() => location.reload());
                        return;
                    }
                }

                Swal.fire({ title: 'Success!', text: 'Test created successfully', icon: 'success', timer: 2000, showConfirmButton: false }).then(() => location.reload());
            } catch (e) {
                Swal.fire('Error', e.message || 'Failed to create Test.', 'error');
            }
        }

        let tempSections = [];
        function addSectionRow() {
            const type = document.getElementById('sec_type').value;
            const count = document.getElementById('sec_count').value;
            const knowledge = document.getElementById('sec_knowledge').value;
            if (!count) return;
            tempSections.push({ type, count, knowledge });
            renderSections();
        }
        function renderSections() {
            const div = document.getElementById('section_preview');
            div.innerHTML = tempSections.map(s => `
            <div class="border p-2 rounded flex justify-between bg-gray-50 text-sm">
                <span><b>${s.type}</b>: ${s.count} Qs (${s.knowledge})</span>
                <button onclick="tempSections.splice(${tempSections.indexOf(s)},1);renderSections()">✕</button>
            </div>
        `).join('');
        }
        async function saveTemplate() {
            const name = document.getElementById('temp_name').value;
            const description = document.getElementById('temp_desc').value;
            await fetch('Test/saveTemplate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    name,
                    description,
                    sections: tempSections.map(s => ({
                        type: s.type,
                        count: s.count,
                        knowledge: s.knowledge
                    }))
                })
            });
            Swal.fire({ title: 'Template Saved', text: 'Your template has been created successfully', icon: 'success', timer: 1500, showConfirmButton: false }).then(() => location.reload());
        }

        function setTestAndRedirect(id) {
            const sel = document.getElementById('main_Test_select');
            if (sel) sel.value = id;
            if (typeof window.switchMainTab === 'function') window.switchMainTab('test-creation');
            openPackWizard();
        }

        let currentEditPackId = null;
        function editPack(data) {
            currentEditPackId = data.id;
            currentTestIdForPack = data.assessment_id;

            document.getElementById('pack_wizard_name').value = data.pack_name;
            document.getElementById('baseTemplateSelect').value = data.template_id || '';
            document.getElementById('pack_duration').value = data.duration || 60;
            document.getElementById('pack_user_role').value = data.user_role || 'General Access';
            document.getElementById('pack_start_time').value = data.start_time || '';
            document.getElementById('pack_end_time').value = data.end_time || '';

            // Highlight template in sidebar
            if (data.template_id) {
                selectTemplate(data.template_id);
            } else {
                updateSummary();
            }

            // Load candidates
            App.selectedCandidates[data.assessment_id] = (data.candidates || '').split(',').filter(id => id.trim());
            updateWizardCandidateLabel();

            document.querySelector('#createPackModal h3').textContent = 'Edit Batch';

            const modalEl = document.getElementById('createPackModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }


        function deletePack(id) {
            Swal.fire({ title: 'Delete this pack?', text: "This action cannot be undone.", icon: 'warning', showCancelButton: true }).then(async (result) => {
                if (result.isConfirmed) {
                    await fetch(`/Test/deletePack/${id}`, { method: 'POST' });
                    location.reload();
                }
            });
        }

        function saveDraftTest() {
            Swal.fire({
                title: 'Save as Draft?',
                text: "You can return later to complete and publish this Test.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes, Save Draft',
                confirmButtonColor: '#475569'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Saved!', 'Test has been saved as a draft.', 'success').then(() => {
                        addPackToTable({
                            id: Date.now(),
                            pack_name: 'Draft Test ' + (new Date().getHours() + ":" + new Date().getMinutes()),
                            created_at: new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
                            template_name: 'In Progress...',
                            status: 'Draft',
                            user_role: 'User Draft'
                        });
                        bootstrap.Modal.getInstance(document.getElementById('createPackModal')).hide();
                    });
                }
            });
        }

        function reusePack(id) {
            Swal.fire({
                title: 'Reuse Test?',
                text: "This will copy the template and questions, jumping directly to candidate assignment.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Reuse',
                confirmButtonColor: '#2563eb'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Configuration Copied!',
                        text: 'Redirecting to Step 3: Assign Candidates...',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Create a duplicated row in background for demo
                        addPackToTable({
                            id: Date.now(),
                            pack_name: 'Copy of Previous Test',
                            created_at: new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
                            template_name: 'Duplicated Template',
                            status: 'Draft',
                            user_role: 'Reused'
                        });

                        // Jump to Step 3
                        currentPackStep = 3;
                        updatePackWizardUI();

                        // Show Modal
                        const modalEl = document.getElementById('createPackModal');
                        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.show();
                    });
                }
            });
        }

        let packsDataTable = null;
        function initPacksDataTable() {
            if ($.fn.dataTable.isDataTable('#TestPacksTable')) {
                packsDataTable = $('#TestPacksTable').DataTable();
                return;
            }

            const initialPacks = <?= workflow_view_json($allPacks ?? []) ?>;

            packsDataTable = $('#TestPacksTable').DataTable({
                data: initialPacks,
                columns: [
                    {
                        data: 'pack_name',
                        render: (data, type, row) => `
                        <div class="font-bold text-[#1e293b]">${data}</div>
                        <div class="text-[10px] text-[#94a3b8]">Created on ${row.created_at}</div>
                    `
                    },
                    {
                        data: 'template_name',
                        render: (data) => `<span class="text-[13px] text-[#475569] font-medium">${data}</span>`
                    },
                    {
                        data: 'status',
                        render: (data) => `
                        <span class="chip ${data.toLowerCase() === 'draft' ? 'bg-gray-100 text-gray-500 border-gray-200' : 'chip-mark'}">
                            ${data}
                        </span>
                    `
                    },
                    {
                        data: 'user_role',
                        render: (data) => `<span class="text-[13px] text-[#64748b]">${data}</span>`
                    },
                    {
                        data: null,
                        className: 'text-right',
                        render: (data, type, row) => `
                        <button class="action-btn text-slate-400 hover:text-blue-600 me-2" onclick="App.previewTestPack(${row.id})" title="Preview Paper"><i class="bi bi-eye"></i></button>
                        <button class="action-btn text-slate-400 hover:text-slate-600 me-2" onclick="manageQuestions(${row.id})" title="Manage Questions"><i class="bi bi-gear"></i></button>
                        <button class="action-btn text-red-400 hover:text-red-600" onclick="deletePack(${row.id})" title="Delete Batch"><i class="bi bi-trash"></i></button>
                    `
                    }
                ],
                pageLength: 5,
                lengthChange: false,
                ordering: true,
                info: false,
                autoWidth: false,
                dom: '<"flex justify-between items-center mb-3"f>rtp',
                language: {
                    search: "",
                    searchPlaceholder: "Search Tests...",
                    paginate: {
                        previous: '<i class="bi bi-chevron-left"></i>',
                        next: '<i class="bi bi-chevron-right"></i>'
                    }
                },
                drawCallback: function () {
                    // Style search input
                    $('.dataTables_filter input').addClass('form-control form-control-sm border-[#e2e8f0] rounded-[8px] px-3 py-1.5 w-[220px] shadow-none focus:border-[#dc2230]');
                    $('.dataTables_paginate').addClass('mt-3');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (typeof syncIntroVideoUploadColumn === 'function') syncIntroVideoUploadColumn();
            const resultsTab = document.getElementById('tab-content-results');
            if (resultsTab && !resultsTab.classList.contains('hidden')) {
                if (typeof App !== 'undefined' && App.loadCandidateResult) App.loadCandidateResult();
            }
        });
        function toggleMultiselect() {
            document.getElementById('multiselect_options').classList.toggle('show');
        }

        function selectAllRoles(checkbox) {
            const checkboxes = document.querySelectorAll('#multiselect_options input[type="checkbox"]:not(#select_all_roles)');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateMultiselectLabel();
        }

        function updateMultiselectLabel() {
            const allCheckboxes = document.querySelectorAll('#multiselect_options input[type="checkbox"]:not(#select_all_roles)');
            const selectAllCb = document.getElementById('select_all_roles');
            const selected = Array.from(allCheckboxes).filter(cb => cb.checked).map(cb => cb.value);

            // Update Select All checkbox state
            if (selectAllCb) {
                selectAllCb.checked = (selected.length === allCheckboxes.length && allCheckboxes.length > 0);
            }

            const label = document.getElementById('multiselect_label');
            const realSelect = document.getElementById('ass_assigned');

            if (selected.length === 0) {
                label.textContent = '-- Select Roles --';
            } else {
                label.textContent = selected.length > 2 ? selected.length + ' roles selected' : selected.join(', ');
            }

            // Sync with real select
            if (realSelect) {
                Array.from(realSelect.options).forEach(opt => {
                    opt.selected = selected.includes(opt.value);
                });
            }
        }

        document.addEventListener('click', (e) => {
            const container = document.getElementById('ass_assigned_container');
            const options = document.getElementById('multiselect_options');
            if (container && options && !container.contains(e.target)) {
                options.classList.remove('show');
            }

            if (!e.target.closest('.candidate-selector-wrapper')) {
                closeAllBatchCandidateDropdowns();
            }
        });

        function clearValidationErrors() {
            document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        }

        function validateTestForm() {
            let isValid = true;
            const name = document.getElementById('ass_name');
            const code = document.getElementById('ass_code');
            const category = document.getElementById('ass_category');
            const desc = document.getElementById('ass_desc');

            if (!name || !name.value.trim()) { showError('ass_name', true); isValid = false; } else { showError('ass_name', false); }
            // Test Code is now hidden and optional in UI
            // if(!code || !code.value.trim()) { showError('ass_code', true); isValid = false; } else { showError('ass_code', false); }
            if (!category || !category.value) { showError('err_ass_category', true, 'ass_category'); isValid = false; } else { showError('err_ass_category', false, 'ass_category'); }

            if (category && category.value === 'Enova') {
                const type = document.getElementById('ass_type');
                if (!type || !type.value) { showError('ass_type', true); isValid = false; } else { showError('ass_type', false); }

                const assigned = document.getElementById('ass_assigned');
                if (!assigned || assigned.selectedOptions.length === 0) {
                    showError('ass_assigned', true, 'multiselect_btn');
                    isValid = false;
                } else {
                    showError('ass_assigned', false, 'multiselect_btn');
                }
            } else {
                // Hide errors for non-enova fields if they were visible
                showError('ass_type', false);
                showError('ass_assigned', false, 'multiselect_btn');
            }

            if (!desc || !desc.value.trim()) { showError('ass_desc', true); isValid = false; } else { showError('ass_desc', false); }

            const addV = document.getElementById('ass_add_video');
            if (addV && addV.value === 'Yes') {
                const n = (typeof assIntroVideoUrls !== 'undefined' ? assIntroVideoUrls.length : 0)
                    + (typeof assIntroVideoFiles !== 'undefined' ? assIntroVideoFiles.length : 0);
                if (n < 1) {
                    showError('err_ass_intro_videos', true, 'ass_intro_upload_col');
                    isValid = false;
                } else {
                    showError('err_ass_intro_videos', false, 'ass_intro_upload_col');
                }
            } else {
                showError('err_ass_intro_videos', false, 'ass_intro_upload_col');
            }

            return isValid;
        }

        function showError(fieldId, isError, targetId = null) {
            const input = document.getElementById(targetId || fieldId);
            const errorSpan = document.getElementById(fieldId.startsWith('err_') ? fieldId : 'err_' + fieldId);
            if (isError) {
                if (input) input.classList.add('is-invalid');
                if (errorSpan) errorSpan.classList.remove('hidden');
            } else {
                if (input) input.classList.remove('is-invalid');
                if (errorSpan) errorSpan.classList.add('hidden');
            }
        }

        function inferIconTooltip(iconEl) {
            if (!iconEl) return 'Action';
            const className = iconEl.className || '';
            const rules = [
                { key: 'bi-trash', label: 'Delete' },
                { key: 'bi-pencil', label: 'Edit' },
                { key: 'bi-eye', label: 'View' },
                { key: 'bi-download', label: 'Download' },
                { key: 'bi-floppy', label: 'Save' },
                { key: 'bi-plus', label: 'Add' },
                { key: 'bi-copy', label: 'Clone' },
                { key: 'bi-send-check', label: 'Publish' },
                { key: 'bi-send', label: 'Submit' },
                { key: 'bi-search', label: 'Search' },
                { key: 'bi-arrow-left', label: 'Back' },
                { key: 'bi-chevron-left', label: 'Previous' },
                { key: 'bi-chevron-right', label: 'Next' },
                { key: 'bi-lock', label: 'Locked' },
                { key: 'bi-journal', label: 'Open' },
                { key: 'bi-file-earmark', label: 'Open Details' }
            ];
            const match = rules.find(rule => className.includes(rule.key));
            return match ? match.label : 'Action';
        }

        function initGlobalIconTooltips(root = document) {
            const targets = root.querySelectorAll('button, a, [role="button"], .batch-action-btn');
            targets.forEach((el) => {
                if (el.hasAttribute('title') || el.hasAttribute('data-bs-title') || el.getAttribute('aria-label')) return;
                const icon = el.querySelector('i.bi');
                if (!icon) return;

                const textClone = el.cloneNode(true);
                textClone.querySelectorAll('i.bi').forEach((node) => node.remove());
                const visibleText = (textClone.textContent || '').trim();
                if (visibleText) return;

                const tooltipText = inferIconTooltip(icon);
                el.setAttribute('title', tooltipText);
                el.setAttribute('data-bs-title', tooltipText);
                el.setAttribute('data-bs-toggle', 'tooltip');

                if (window.bootstrap?.Tooltip) {
                    const existing = window.bootstrap.Tooltip.getInstance(el);
                    if (existing) existing.dispose();
                    new window.bootstrap.Tooltip(el, { trigger: 'hover focus', container: 'body' });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initGlobalIconTooltips(document);

            if (window.MutationObserver) {
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === 1) initGlobalIconTooltips(node);
                        });
                    });
                });
                observer.observe(document.body, { childList: true, subtree: true });
            }
        });

        // Live Validation Listeners
        document.addEventListener('DOMContentLoaded', () => {
            ['ass_name', 'ass_code', 'ass_category', 'ass_type'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('blur', () => {
                        if (id === 'ass_type' && document.getElementById('ass_category').value !== 'Enova Test') return;
                        showError(id, !el.value.trim());
                    });
                    el.addEventListener('input', () => {
                        if (el.value.trim()) showError(id, false);
                    });
                }
            });
        });

        function validateTemplateForm() {
            let isValid = true;
            const fields = ['builder_storage_name', 'builder_duration'];
            fields.forEach(id => {
                const el = document.getElementById(id);
                if (!el || !el.value.trim()) { showError(id, true); isValid = false; } else { showError(id, false); }
            });

            const sections = document.querySelectorAll('.section-builder-card');
            if (sections.length === 0) {
                Swal.fire('Required', 'Please add at least one section to your template', 'warning');
                isValid = false;
            }
            return isValid;
        }

        // Live Listeners for Template Builder
        document.addEventListener('DOMContentLoaded', () => {
            ['builder_storage_name'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('blur', () => { if (!el.value.trim()) showError(id, true); });
                    el.addEventListener('input', () => { if (el.value.trim()) showError(id, false); });
                }
            });
        });
    </script>
    <!-- MANUAL QUESTION MODAL -->
    <div class="modal fade" id="manualQuestionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 bg-indigo-600 text-white rounded-xl flex items-center justify-center shadow-md">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <h5 class="text-[14px] font-black text-slate-800 uppercase tracking-widest mb-0">Add Manual
                            Question</h5>
                    </div>
                    <button type="button" class="btn-close text-[10px]" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="p-8 space-y-6">
                    <div class="form-group">
                        <label
                            class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Question
                            Type</label>
                        <select id="manual_q_type"
                            class="w-full bg-slate-50 border-0 rounded-xl h-12 px-4 text-[13px] font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all">
                            <option value="MCQ">Multiple Choice (MCQ)</option>
                            <option value="Short Answer">Descriptive question</option>
                            <option value="Practical">Practical / Coding</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label
                            class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Question
                            Content</label>
                        <textarea id="manual_q_text" rows="4"
                            class="w-full bg-slate-50 border-0 rounded-xl p-4 text-[13px] font-medium text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all"
                            placeholder="Enter your question here..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Marks
                            Weightage</label>
                        <input type="number" id="manual_q_marks" value="1"
                            class="w-full bg-slate-50 border-0 rounded-xl h-12 px-4 text-[13px] font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all">
                    </div>
                </div>
                <div class="p-6 bg-slate-50/50 border-t border-slate-50 flex gap-4">
                    <button type="button"
                        class="flex-1 py-3 bg-white border border-slate-200 text-slate-500 font-bold rounded-xl text-[11px] uppercase tracking-widest hover:bg-slate-50 transition-all"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button"
                        class="flex-1 py-3 bg-indigo-600 text-white font-bold rounded-xl text-[11px] uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all"
                        onclick="addManualQuestion()">Add Question</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>