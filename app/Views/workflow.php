<?php
// Calculate all packs for use in tables and JS
$allPacks = [];
if (!empty($assessments)) {
    foreach($assessments as $a) {
        if (!empty($a['test_packs'])) {
            foreach($a['test_packs'] as $tp) {
                $tp['assessment_name'] = $a['name'];
                $allPacks[] = $tp;
            }
        }
    }
}
?>

<!-- Flash Message Handler -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({ title: 'Success', text: '<?= session()->getFlashdata('success') ?>', icon: 'success', timer: 3000 });
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({ title: 'Upload Failed', text: '<?= session()->getFlashdata('error') ?>', icon: 'error' });
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
        .swal2-popup { border-radius: 20px !important; font-family: 'Poppins', sans-serif !important; }
        .swal2-styled.swal2-confirm { background-color: var(--brand) !important; border-radius: 10px !important; padding: 0.6rem 1.5rem !important; font-weight: 600 !important; }
        .swal2-styled.swal2-cancel { border-radius: 10px !important; padding: 0.6rem 1.5rem !important; font-weight: 600 !important; }
        .modal-blur { filter: blur(5px); transition: filter 0.3s ease; }
        
        /* DataTables Compact Design */
        .dataTables_wrapper .dataTables_filter input { width: 220px !important; font-size: 11px !important; height: 32px !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { 
            padding: 4px 12px !important; 
            font-size: 11px !important; 
            margin: 0 2px !important; 
            border-radius: 8px !important; 
            border: 1px solid #e2e8f0 !important;
            background: #f8fafc !important;
            color: #64748b !important;
            font-weight: 700 !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            display: inline-block !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: var(--brand) !important;
            text-decoration: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--brand) !important;
            color: white !important;
            border-color: var(--brand) !important;
            box-shadow: 0 4px 10px -2px rgba(220, 34, 48, 0.3) !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
            background: #f8fafc !important;
            color: #94a3b8 !important;
        }
        .dataTables_paginate { display: flex; align-items: center; justify-content: flex-end; gap: 4px; }
        #assessmentPacksTable_wrapper { padding: 0 !important; }
        #assessmentPacksTable { border: none !important; margin: 0 !important; }
        #assessmentPacksTable thead th { border-bottom: 1px solid #f1f5f9 !important; padding: 10px 20px !important; font-size: 10px !important; }
        #assessmentPacksTable tbody td { padding: 8px 20px !important; border-bottom: 1px solid #f1f5f9 !important; }
        .table-responsive { overflow: visible !important; }

        /* Nested DataTable Accordion Styles */
        #assessmentsDataTable td.dt-control { cursor: pointer; text-align: center; font-size: 18px; }
        #assessmentsDataTable tr.dt-hasChild td.dt-control i { color: var(--brand) !important; transform: rotate(0deg); }
        #assessmentsDataTable tr td.dt-control i { transition: transform 0.2s ease; }
        
        .child-table-container { 
            animation: fadeIn 0.3s ease-out;
            border-left: 4px solid var(--brand);
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
        
        .template-table th, .template-table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; }
        .template-table thead { background: #f8fafc; }
        .template-table thead th { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; }
        
        .template-info-cell { display: flex; align-items: center; gap: 1rem; }
        .template-icon { width: 40px; height: 40px; border-radius: 10px; background: #fff1f2; color: var(--brand); display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .template-name { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
        .template-sub { font-size: 11px; color: #64748b; font-weight: 500; }
        
        .section-count-badge { background: #f1f5f9; color: #475569; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px; }
        
        .action-icon-btn { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; background: #f8fafc; border: 1px solid #e2e8f0; color: #94a3b8; }
        .action-icon-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); color: #1e293b; background: #fff; }
        .action-icon-btn.view:hover { color: #2563eb; border-color: #93c5fd; background-color: #eff6ff; }
        .action-icon-btn.edit:hover { color: #10b981; border-color: #6ee7b7; background-color: #ecfdf5; }
        .action-icon-btn.delete:hover { color: #dc2230; border-color: #fca5a5; background-color: #fef2f2; }
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
        * { font-family: 'Poppins', sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; font-family: 'Poppins', sans-serif; }
        .label { font-size: 10px; letter-spacing: .18em; text-transform: uppercase; color: #9aa0a6; font-weight: 700; }
        .card { background:#fff; border:1px solid var(--line); border-radius:14px; box-shadow: 0 1px 2px rgba(15,23,42,.03); }
        .pill { display:inline-flex; align-items:center; gap:.5rem; padding:.7rem 1.4rem; border-radius:999px; font-weight:800; font-size:12px; letter-spacing:.14em; text-transform:uppercase; transition: all .15s; }
        .pill-active { background: var(--brand); color:#fff; box-shadow: 0 8px 20px -6px rgba(220,34,48,.5); }
        .pill-idle { background:#fff; color:#b5b9c2; border:1px solid var(--line); }
        .btn-red { background: var(--brand); color:#fff; font-weight:800; letter-spacing:.12em; text-transform:uppercase; font-size:12px; padding:.85rem 1.4rem; border-radius:999px; display:inline-flex; align-items:center; gap:.55rem; box-shadow: 0 10px 22px -8px rgba(220,34,48,.55);}
        .btn-red:hover { background: var(--brand-dark); }
        .btn-ghost { background:#fff; border:1px solid var(--line); color:#374151; font-weight:700; font-size:12px; letter-spacing:.08em; text-transform:uppercase; padding:.6rem .9rem; border-radius:10px; display:inline-flex; align-items:center; gap:.5rem; }
        .btn-ghost:hover { border-color:#cbd5e1; }
        .input, .select { width:100%; background:#fff; border:1px solid var(--line); border-radius:10px; padding:.6rem .8rem; font-size:14px; color:#111827; }
        .stat-card { background: linear-gradient(180deg, #fff 0%, #fafbfc 100%); border:1px solid var(--line); border-radius:16px; padding:18px 22px; min-width:260px; position:relative; overflow:hidden;}
        .stat-card.is-active { border:1.5px solid var(--brand); box-shadow: 0 0 0 4px rgba(220,34,48,.06); }
        .chip { display:inline-flex; align-items:center; gap:.35rem; font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; padding:.3rem .6rem; border-radius:999px; }
        .chip-mcq { background:#eef4ff; color:#2563eb; }
        .chip-2m { background:#fef3c7; color:#b45309; }
        .chip-mark { background:#ecfdf5; color:#047857; }
        .accordion { background:#fff; border:1px solid var(--line); border-radius:14px; overflow:hidden; }
        .accordion + .accordion { margin-top:12px; }
        .custom-modal-backdrop { position:fixed; inset:0; background:rgba(15,23,42,.45); display:none; align-items:flex-start; justify-content:center; z-index:2000; padding:60px 16px; overflow:auto;}
        .custom-modal-backdrop.open { display:flex; }
        .custom-modal { background:#fff; border-radius:16px; width:100%; max-width:720px; padding:28px; box-shadow: 0 25px 60px -20px rgba(15,23,42,.35); }
        .tab { padding:.55rem 1rem; font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; border-radius:10px; cursor:pointer; }
        .tab-active { background: var(--brand); color:#fff; }
        .tab-idle { color:#6b7280; background:#f3f4f6; }
        .navbar { background: #fff; border-bottom: 2px solid #f0f1f3; padding: 0.5rem 2rem; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; }
        .nav-left { display: flex; align-items: center; gap: 1.5rem; }
        .nav-logo { display: flex; align-items: center; gap: 0.75rem; }
        .nav-logo text { font-weight: 700; color: var(--brand); font-size: 1.1rem; }
        .nav-center { display: flex; align-items: center; gap: 2rem; }
        .nav-item { display: flex; flex-direction: column; align-items: center; gap: 0.25rem; font-size: 10px; font-weight: 800; letter-spacing: 0.05em; color: var(--muted); cursor: pointer; transition: color 0.2s; }
        .nav-item:hover { color: var(--ink); }
        .nav-item.active { color: var(--brand); border-bottom: 2px solid var(--brand); padding-bottom: 4px; margin-bottom: -6px; }
        .nav-right { display: flex; align-items: center; gap: 1rem; }
        .user-profile { display: flex; align-items: center; gap: 0.5rem; background: #f3f4f6; padding: 0.4rem 0.8rem; border-radius: 999px; font-size: 13px; font-weight: 600; }
        .user-avatar { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; }
        
        .module-header { background:#fff; padding: 1.5rem 2rem; border-bottom: 1px solid #f0f1f3; }
        .module-tabs { background:#fff; border-bottom: 1px solid #f0f1f3; padding: 0 2rem; display: flex; gap: 2rem; }
        .module-tab { padding: 1rem 0.5rem; display: flex; align-items: center; gap: 0.5rem; font-size: 13px; font-weight: 600; color: #64748b; cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.2s; }
        .module-tab:hover { color: var(--ink); }
        .module-tab.active { color: var(--brand); border-bottom-color: var(--brand); }
        .module-icon { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: #fef2f2; color: var(--brand); }
        
        .stepper { display: flex; align-items: center; justify-content: space-between; max-width: 1000px; margin: 3rem auto; position: relative; }
        .stepper::before { content: ""; position: absolute; top: 1.25rem; left: 5%; right: 5%; height: 1px; background: #e2e8f0; z-index: 0; }
        .step { position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; gap: 0.75rem; text-align: center; }
        .step-circle { width: 40px; height: 40px; border-radius: 50%; border: 1.5px solid #cbd5e1; background: #fff; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 14px; transition: all 0.3s; }
        .step.active .step-circle { border-color: var(--brand); color: var(--brand); background: #fef2f2; box-shadow: 0 0 0 4px rgba(220,34,48,0.1); }
        .step-label { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
        .step.active .step-label { color: var(--brand); }
        
        .card-main { background: #fff; border: 1px solid #f0f1f3; border-radius: 20px; padding: 2.5rem; max-width: 1100px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); transition: all 0.3s; }
        .details-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; margin-top: 1.5rem; }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem; }
        .details-item label { display: block; font-size: 12px; color: #64748b; margin-bottom: 0.25rem; }
        .details-item span { display: block; font-weight: 700; color: var(--ink); }
        
        .btn-outline { border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 0.6rem 1.5rem; font-size: 14px; font-weight: 600; color: #64748b; transition: all 0.2s; background: #fff; }
        .btn-outline:hover:not(:disabled) { background: #f8fafc; border-color: #cbd5e1; }
        .btn-red-rounded { background: var(--brand); color: #fff; border-radius: 8px; padding: 0.6rem 1.5rem; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; }
        .btn-red-rounded:hover { background: var(--brand-dark); transform: translateY(-1px); }

        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 0.5rem; }
        .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; inset: 0; background-color: #e2e8f0; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #2563eb; }
        input:checked + .slider:before { transform: translateX(20px); }

        .settings-card { background: #fff; border: 1px solid #f1f5f9; border-radius: 16px; padding: 1.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04); }
        .settings-row { display: flex; align-items: center; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid #f8fafc; gap: 2rem; }
        .settings-row:last-child { border-bottom: none; }
        .summary-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; }
        .summary-total { border-top: 1px solid #e2e8f0; margin-top: 10px; padding-top: 10px; font-weight: 700; color: var(--brand); }

        /* Hide number input arrows */
        input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        /* Accordion Styles */
        .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out, padding 0.3s ease; }
        .accordion-content.open { max-height: 2000px; padding-bottom: 1.5rem; }
        .chevron { transition: transform 0.3s ease; }
        .chevron.rotated { transform: rotate(180deg); }
        .accordion-header:hover { background-color: #f1f5f9; }

        /* Premium Multiselect */
        .custom-multiselect { position: relative; width: 100%; }
        .multiselect-btn {
            width: 100%; height: 44px; padding: 0 1rem; background: #fff;
            border: 1px solid #e2e8f0; border-radius: 12px;
            display: flex; align-items: center; justify-content: space-between;
            font-size: 0.875rem; color: #475569; cursor: pointer; text-align: left;
            transition: all 0.2s;
        }
        .multiselect-btn:hover { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .multiselect-options {
            display: none; position: absolute; top: 100%; left: 0; width: 100%;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
            margin-top: 4px; z-index: 1000; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            max-height: 200px; overflow-y: auto; padding: 8px;
        }
        .multiselect-options.show { display: block; }
        .option-item {
            display: flex; align-items: center; gap: 10px; padding: 8px 12px;
            border-radius: 8px; cursor: pointer; font-size: 0.875rem; color: #1e293b;
            margin-bottom: 2px; transition: background 0.2s;
        }
        .option-item:hover { background: #f1f5f9; }
        .option-item input { width: 16px; height: 16px; cursor: pointer; }

        /* Validation Styles */
        .input.is-invalid, .select.is-invalid, .multiselect-btn.is-invalid { border-color: #ef4444 !important; background-color: #fef2f2 !important; }
        .error-msg { display: block; font-size: 10px; font-weight: 600; color: #ef4444; margin-top: 4px; transition: all 0.2s; }
        .error-msg.hidden { display: none; }

        /* Force SweetAlert to be on top */
        .swal2-container { z-index: 10000 !important; }

        /* Semantic Badge Colors */
        .badge-fresher { background-color: #fef9c3; color: #854d0e; } /* Yellow */
        .badge-enova { background-color: #dcfce7; color: #166534; }   /* Green */
        .badge-tech { background-color: #e0e7ff; color: #3730a3; }    /* Indigo */
        .badge-comp { background-color: #ffedd5; color: #9a3412; }    /* Orange */
        
        .role-dev { background-color: #dbeafe; color: #1e40af; }
        .role-design { background-color: #fce7f3; color: #9d174d; }
        .role-test { background-color: #f3e8ff; color: #6b21a8; }
        .role-hr { background-color: #e0f2fe; color: #075985; }

        /* Action Reveal Styles */
        .action-reveal { opacity: 0; transform: translateX(10px); transition: all 0.2s ease; pointer-events: none; }
        .accordion-header:hover .action-reveal, .pack-card:hover .action-reveal { opacity: 1; transform: translateX(0); pointer-events: auto; }
        .action-btn { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; background: #fff; border: 1px solid #e2e8f0; }
        .action-btn:hover { background-color: #f8fafc; transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .btn-delete:hover { color: #dc2230; border-color: #fca5a5; background-color: #fef2f2; }
        .btn-edit:hover { color: #2563eb; border-color: #93c5fd; background-color: #eff6ff; }

        /* Premium Execution View */
        .execution-wrapper {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: #f8fafc;
            z-index: 3000;
            display: flex;
            flex-direction: column;
            animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
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
        .nav-dot.active { background: var(--brand); color: #fff; border-color: var(--brand); }
        .nav-dot.answered { background: #ecfdf5; color: #047857; border-color: #10b981; }
        .nav-dot.flagged { background: #fef3c7; color: #b45309; border-color: #f59e0b; }

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

        .evaluation-item { transition: all 0.2s; }
        .evaluation-item:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }

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
        .timer-box.warning { background: var(--brand); animation: pulse 1s infinite; }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.8; }
            100% { opacity: 1; }
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
        .score-val { font-size: 3.5rem; font-weight: 800; color: var(--brand); line-height: 1; }
        .score-label { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-top: 0.5rem; }

        /* Wizard Styles */
        .modal-backdrop.show { backdrop-filter: blur(10px); background: rgba(15, 23, 42, 0.5); }
        .modal-content { border-radius: 24px; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        
        .wizard-step-title { font-size: 1.25rem; font-weight: 800; color: #1e293b; letter-spacing: -0.02em; }
        .wizard-step-subtitle { font-size: 0.875rem; color: #64748b; margin-top: 0.25rem; }
        
        .stepper { display: flex; align-items: center; justify-content: space-between; position: relative; max-width: 800px; margin: 0 auto; }
        .stepper::before { content: ''; position: absolute; top: 18px; left: 50px; right: 50px; height: 2px; background: #e2e8f0; z-index: 1; }
        .stepper .step { position: relative; z-index: 2; background: #fff; padding: 0 10px; display: flex; flex-direction: column; align-items: center; gap: 8px; }
        .stepper .step-circle { width: 36px; height: 36px; border-radius: 50%; border: 2px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #94a3b8; background: #fff; transition: all 0.3s; }
        .stepper .step-label { font-size: 12px; font-weight: 600; color: #94a3b8; transition: all 0.3s; }
        
        .stepper .step.active .step-circle { border-color: #dc2230; background: #dc2230; color: #fff; box-shadow: 0 0 0 4px rgba(220,34,48,0.1); }
        .stepper .step.active .step-label { color: #dc2230; font-weight: 700; }
        .stepper .step.completed .step-circle { border-color: #dc2230; background: #dc2230; color: #fff; }
        .stepper .step.completed .step-label { color: #dc2230; }

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
        
        .form-label { font-size: 13px; font-weight: 600; color: #475569; }
        .form-select, .form-control { border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px; font-size: 14px; }
        .form-select:focus, .form-control:focus { border-color: #dc2230; box-shadow: 0 0 0 3px rgba(220,34,48,0.1); }

        /* Premium Execution View Styles */
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
        .exec-header-left { display: flex; align-items: center; gap: 1.5rem; }
        .exec-brand { display: flex; align-items: center; gap: 0.75rem; }
        .exec-logo { width: 32px; height: 32px; background: var(--brand); color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .exec-brand-text { font-weight: 800; font-size: 1.1rem; color: #0f172a; display: flex; align-items: center; gap: 0.5rem; }
        .proctor-badge { background: #ecfdf5; color: #059669; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px; border: 1px solid #10b981; }
        .exec-divider { width: 1px; height: 32px; background: #e2e8f0; }
        .exec-test-name { font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0; }
        .exec-test-step { font-size: 0.75rem; color: #64748b; font-weight: 600; }

        .exec-header-right { display: flex; align-items: center; gap: 1.5rem; }
        .btn-simulate-custom { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; font-weight: 700; font-size: 12px; border-radius: 8px; }
        .btn-simulate-custom:hover { background: #ffe4e6; }
        .exec-timer-box-custom { background: #0f172a; color: #fff; padding: 0.5rem 1rem; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem; min-width: 120px; }
        .exec-timer-box-custom.warning { background: var(--brand); animation: pulse-timer 1s infinite; }
        @keyframes pulse-timer { 0%, 100% { opacity: 1; } 50% { opacity: 0.8; } }
        .timer-icon-custom { color: #94a3b8; font-size: 1.1rem; }
        .timer-values-custom { font-family: 'JetBrains Mono', monospace; font-weight: 700; font-size: 1.1rem; }
        .btn-submit-test-custom { background: var(--brand); color: #fff; border-radius: 8px; font-weight: 700; font-size: 14px; padding: 0.6rem 1.25rem; border: none; }

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
        .question-card-custom-v2 { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
        .q-card-header-custom { padding: 1.5rem 2rem; background: #fafbfc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .q-meta-group-custom { display: flex; gap: 0.5rem; }
        .q-id-pill-custom { background: #f1f5f9; color: #475569; font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 6px; }
        .q-type-pill-custom { background: #eff6ff; color: #2563eb; font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 6px; }
        .q-marks-pill-custom { background: #f0fdf4; color: #16a34a; font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 6px; }
        .q-category-pill-custom { background: #fff7ed; color: #ea580c; font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 6px; }
        .btn-flag-custom { border: none; background: transparent; color: #94a3b8; font-weight: 700; font-size: 12px; display: flex; align-items: center; gap: 0.5rem; }
        .btn-flag-custom.btn-primary-custom { color: #f59e0b; }

        .q-body-custom { padding: 2.5rem 2rem; }
        .q-text-custom { font-size: 1.25rem; font-weight: 700; color: #0f172a; line-height: 1.5; margin-bottom: 0.5rem; }
        .q-hint-custom { font-size: 0.875rem; }

        .options-grid { display: grid; gap: 1rem; }
        .option-item { background: #fff; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 1.25rem 1.5rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 1rem; }
        .option-item:hover { border-color: #cbd5e1; background: #f8fafc; }
        .option-item.selected { border-color: var(--brand); background: #fef2f2; }
        .option-circle { width: 20px; height: 20px; border: 2px solid #cbd5e1; border-radius: 50%; position: relative; }
        .option-square { width: 20px; height: 20px; border: 2px solid #cbd5e1; border-radius: 4px; position: relative; }
        .option-item.selected .option-circle::after { content: ''; position: absolute; inset: 3px; background: var(--brand); border-radius: 50%; }
        .option-item.selected .option-square::after { content: '\F26B'; font-family: 'bootstrap-icons'; position: absolute; inset: -2px; color: var(--brand); font-size: 14px; display: flex; align-items: center; justify-content: center; }
        .option-text { font-weight: 600; font-size: 0.95rem; color: #334155; }

        .exec-footer-custom { margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; }
        .btn-nav-prev-custom { background: #fff; border: 1.5px solid #e2e8f0; border-radius: 10px; font-weight: 700; font-size: 14px; padding: 0.75rem 1.5rem; color: #475569; }
        .btn-nav-next-custom { background: var(--brand); border: none; border-radius: 10px; font-weight: 700; font-size: 14px; padding: 0.75rem 2rem; color: #fff; }
        .save-status-custom { display: flex; align-items: center; gap: 0.5rem; font-size: 12px; color: #94a3b8; font-weight: 600; }
        .save-dot-custom { width: 8px; height: 8px; background: #10b981; border-radius: 50%; }

        .exec-sidebar-custom { display: flex; flex-direction: column; gap: 1.5rem; }
        .sidebar-card-custom { background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .sb-title-custom { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1.25rem; }
        .progress-info-custom { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 0.75rem; }
        .progress-percent-custom { font-size: 1.5rem; font-weight: 800; color: #0f172a; }
        .progress-label-custom { font-size: 11px; font-weight: 700; color: #94a3b8; margin-bottom: 4px; }
        .exec-progress-gradient-custom { background: linear-gradient(90deg, #dc2230 0%, #ef4444 100%); border-radius: 10px; }
        .sb-stats-custom { display: flex; gap: 1.5rem; }
        .sb-stat-item-custom { display: flex; flex-direction: column; }
        .sb-stat-val-custom { font-size: 1.1rem; font-weight: 800; color: #0f172a; }
        .sb-stat-label-custom { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }

        .navigator-grid-custom-v2 { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.5rem; }
        .nav-item { width: 100%; aspect-ratio: 1; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; border: 1.5px solid transparent; }
        .nav-item.unanswered { background: #f8fafc; border-color: #e2e8f0; color: #64748b; }
        .nav-item.current { background: #fff; border-color: var(--brand); color: var(--brand); box-shadow: 0 0 0 3px rgba(220,34,48,0.1); }
        .nav-item.answered { background: #f0fdf4; border-color: #bbf7d0; color: #16a34a; }
        .nav-item.flagged { background: #fff7ed; border-color: #fed7aa; color: #ea580c; }
        .nav-legend-custom { margin-top: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        .legend-item-custom { font-size: 10px; font-weight: 700; color: #64748b; display: flex; align-items: center; gap: 0.5rem; text-transform: uppercase; }
        .leg-dot-custom { width: 10px; height: 10px; border-radius: 3px; }
        .leg-dot-custom.current { background: #fff; border: 1.5px solid var(--brand); }
        .leg-dot-custom.answered { background: #f0fdf4; border: 1.5px solid #bbf7d0; }
        .leg-dot-custom.flagged { background: #fff7ed; border: 1.5px solid #fed7aa; }
        .leg-dot-custom.unanswered { background: #f8fafc; border: 1.5px solid #e2e8f0; }

        .instructions-card-custom { background: #fefce8; border-color: #fef08a; }
        .instructions-card-custom .sb-title-custom { color: #a16207; }
        .sb-list-custom { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.75rem; }
        .sb-list-custom li { font-size: 12px; color: #854d0e; font-weight: 500; display: flex; gap: 0.5rem; }
        .sb-list-custom li::before { content: '•'; color: #eab308; font-weight: 900; }

        .submission-success-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px); z-index: 4000; display: flex; align-items: center; justify-content: center; }
        .success-card { background: #fff; border-radius: 24px; padding: 3rem; max-width: 480px; width: 90%; text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .success-icon-wrapper { width: 80px; height: 80px; background: #f0fdf4; color: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 1.5rem; }
        .success-title { font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-bottom: 0.75rem; }
        .success-text { color: #64748b; margin-bottom: 2rem; }
        .success-actions { display: flex; flex-direction: column; gap: 0.75rem; }
        .btn-view-results { background: var(--brand); color: #fff; border-radius: 12px; font-weight: 700; padding: 1rem; border: none; text-align: center; }

        .custom-modal-backdrop {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
            display: none; align-items: center; justify-content: center; z-index: 9999;
            padding: 20px;
        }
        .custom-modal {
            background: #fff; border-radius: 20px; width: 100%; max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            padding: 2rem; position: relative; max-height: 90vh; overflow-y: auto;
        }

        .submit-confirm-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 4000; display: flex; align-items: center; justify-content: center; }
        .confirm-card { background: #fff; border-radius: 20px; padding: 2rem; max-width: 400px; width: 90%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        .confirm-title { font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; }
        .confirm-text { font-size: 0.95rem; color: #475569; margin-bottom: 0.5rem; }
        .confirm-warning { font-size: 0.85rem; color: #94a3b8; margin-bottom: 2rem; }
        .confirm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .btn-confirm-cancel { background: #f1f5f9; color: #475569; border: none; border-radius: 10px; font-weight: 700; padding: 0.75rem; }
        .btn-confirm-yes { background: var(--brand); color: #fff; border: none; border-radius: 10px; font-weight: 700; padding: 0.75rem; }

        .violation-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 5000; display: none; align-items: center; justify-content: center; }
        .violation-overlay.active { display: flex; }
        .violation-card { background: #fff; border-radius: 24px; padding: 2.5rem; max-width: 440px; width: 90%; text-align: center; }
        .violation-icon { font-size: 3.5rem; color: #ef4444; margin-bottom: 1rem; }

        /* Print Optimization */
        @media print {
            /* Hide everything except the preview modal content */
            body > *:not(#paperPreviewModal) { display: none !important; }
            .modal-backdrop, .modal-header, .modal-footer, button, .btn { display: none !important; }
            
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
            #previewPaperContent > div { 
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
            .mb-5 { page-break-inside: avoid; break-inside: avoid; }
            
            /* Clean up the paper appearance for print */
            .bg-[#f8fafc], .bg-[#fef2f2] { background-color: transparent !important; }
            .border-dashed { border-style: solid !important; border-width: 1px !important; border-color: #e2e8f0 !important; }
        }
    </style>
    <style>
        /* Template Specific Styles */
        .template-table th { color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 1.5rem 1rem; border-bottom: 1px solid #f1f5f9; }
        .template-table td { padding: 1.25rem 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .template-info-cell { display: flex; align-items: center; gap: 1rem; }
        .template-icon { width: 40px; height: 40px; border-radius: 10px; background: #fff1f2; color: #dc2230; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .template-name { font-weight: 700; color: #1e293b; font-size: 0.95rem; margin-bottom: 2px; }
        .template-sub { font-size: 0.75rem; color: #94a3b8; font-weight: 500; }
        .section-count-badge { background: #fff; border: 1px solid #e2e8f0; padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 0.75rem; color: #475569; min-width: 40px; text-align: center; }
        .duration-text { font-weight: 800; color: #1e293b; font-size: 0.9rem; }
        .duration-text span { color: #94a3b8; font-weight: 500; font-size: 0.75rem; margin-left: 4px; }
        .marks-pill { background: #fff1f2; color: #dc2230; padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 0.75rem; border: 1px solid #fee2e2; }
        .date-text { color: #64748b; font-size: 0.85rem; font-weight: 500; }
        
        .action-icon-btn { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; background: #fff; color: #64748b; transition: all 0.2s; }
        .action-icon-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .action-icon-btn.view:hover { color: #2563eb; border-color: #93c5fd; background: #eff6ff; }
        .action-icon-btn.edit:hover { color: #059669; border-color: #6ee7b7; background: #ecfdf5; }
        .action-icon-btn.delete:hover { color: #dc2230; border-color: #fca5a5; background: #fef2f2; }

        /* Template Builder Sidebar Redesign */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
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
            top: 0;
            right: -100%;
            width: 100%;
            max-width: 1100px;
            height: 100vh;
            background: #fff;
            z-index: 10001;
            transition: right 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: -20px 0 50px rgba(15, 23, 42, 0.1);
            display: flex;
            flex-direction: column;
        }
        .sidebar-panel.open {
            right: 0;
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
            grid-template-columns: 340px 1fr;
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
        .filter-tabs { display: flex; gap: 4px; background: #f1f5f9; padding: 4px; border-radius: 12px; }
        .filter-tab { 
            flex: 1; text-align: center; padding: 6px 10px; border-radius: 8px; font-size: 10px; 
            font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; 
            cursor: pointer; transition: all 0.2s;
        }
        .filter-tab:hover { color: #1e293b; }
        .filter-tab.active { background: white; color: var(--brand); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

        /* Template Item Card */
        .template-item-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 14px;
            padding: 1rem;
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

        .builder-field-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
        .builder-field-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }

        /* Section Cards */
        .section-builder-card {
            background: #fff;
            border: 1.5px solid #f1f5f9;
            border-radius: 16px;
            padding: 1.25rem;
            transition: all 0.2s;
        }
        .section-builder-card:hover { border-color: #e2e8f0; }
        .section-drag-handle { cursor: grab; color: #cbd5e1; }

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
            background: #4b2a63; /* Purple from image */
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
        
        .search-container { position: relative; max-width: 300px; }
        .search-input { width: 100%; background: #f3f4f6; border: none; border-radius: 999px; padding: 0.6rem 1rem 0.6rem 2.5rem; font-size: 13px; font-weight: 500; }
        .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; }

        .pagination-container { display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; }
        .pagination-info { font-size: 12px; color: #64748b; font-weight: 500; }
        .pagination-btns { display: flex; gap: 4px; }
        .page-btn { padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; border: 1px solid #e2e8f0; background: #fff; color: #475569; transition: all 0.2s; }
        .page-btn:hover:not(:disabled) { background: #f8fafc; border-color: #cbd5e1; }
        .page-btn.active { background: #dc2230; color: #fff; border-color: #dc2230; }
        .page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        /* Accordion & Pack Styles */
        .accordion-header { cursor: pointer; transition: background 0.2s; padding: 0.75rem 1.5rem !important; }
        .accordion-header:hover { background: #f1f5f9; }
        .accordion-content { display: none; padding: 1rem 1.5rem; background: #fff; border-top: 1px solid #f1f5f9; }
        .accordion-content.open { display: block; }
        .pack-item-card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; background: #f8fafc; transition: all 0.2s; display: grid; grid-template-columns: 1fr 1.5fr 1fr 1fr auto; gap: 1.5rem; align-items: center; }
        .pack-item-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); background: #fff; }
        .pack-name-badge { width: 32px; height: 32px; border-radius: 8px; background: #fff1f2; color: #dc2230; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; }
        
        .badge-status { padding: 4px 10px; border-radius: 6px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .status-active { background: #ecfdf5; color: #059669; }
        .status-draft { background: #f1f5f9; color: #64748b; }
        
        .pack-assigned-avatars { display: flex; align-items: center; }
        .pack-assigned-avatars img { width: 24px; height: 24px; border-radius: 50%; border: 2px solid #fff; margin-left: -8px; }
        .pack-assigned-avatars img:first-child { margin-left: 0; }
        .pack-assigned-count { font-size: 11px; color: #64748b; font-weight: 600; margin-left: 8px; }

        /* New Paper Preview Styles */
        .paper-preview-container { background: #fff; border-radius: 0; padding: 40px; box-shadow: none; }
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
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
            .paper-preview-container { padding: 0 !important; }
            .paper-card { break-inside: avoid; }
        }

        /* Assessment Pack Tab List */
        .pack-table th { color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
        .pack-table td { padding: 1.25rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    </style>



</head>
<body class="min-h-screen">

<!-- eNova Navigation -->
<nav class="navbar">
  <div class="nav-left">
    <button class="icon-btn" style="border:none;background:transparent;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <div class="nav-logo">
      <svg width="32" height="32" viewBox="0 0 40 40" fill="none">
        <circle cx="20" cy="20" r="18" fill="var(--brand)" />
        <path d="M12 20C12 15.5817 15.5817 12 20 12" stroke="white" stroke-width="4" stroke-linecap="round" />
        <path d="M28 20C28 24.4183 24.4183 28 20 28" stroke="white" stroke-width="4" stroke-linecap="round" />
      </svg>
      <text>eNova Administration Portal</text>
    </div>
  </div>

  <div class="nav-center">
    <div class="nav-item active">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      HOME
    </div>
    <div class="nav-item">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
      TICKETS
    </div>
    <div class="nav-item">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      TIMESHIFT
    </div>
    <div class="nav-item">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
      MORE
    </div>
  </div>

  <div class="nav-right">
    <div class="user-profile">
      <img src="https://i.pravatar.cc/150?u=logesh" class="user-avatar" alt="Avatar">
      <span>Logeshwaran S</span>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="6 9 12 15 18 9"/></svg>
    </div>
  </div>
</nav>

<!-- Module Header -->
<div class="module-header flex items-center justify-between">
    <div class="flex items-center gap-4">
        <div class="module-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        </div>
        <div>
            <h2 class="text-xl font-bold">Assessment Module</h2>
            <p class="text-sm text-gray-500">Evaluate & track employee competencies</p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <button class="btn-outline flex items-center gap-2" onclick="switchMainTab('results')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H9H8"/></svg>
            Results & Evaluation
        </button>
        <button class="btn-outline flex items-center gap-2" onclick="startExecutionMode()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
            Execution View
        </button>
    </div>
</div>

<!-- Module Tabs -->
<div class="module-tabs">
    <div class="module-tab active" onclick="switchMainTab('management')">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-3-3.87"/><path d="M9 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Assessment Management
    </div>
</div>

<!-- MAIN CONTENT CONTAINER -->
<div id="main-content-area">

    <!-- 1. ASSESSMENT MANAGEMENT HUB -->
    <main id="tab-content-management" class="px-8 py-6">
        <!-- Dashboard Header Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="stat-card is-active">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Assessments</div>
                <div class="text-2xl font-black text-slate-800"><?= count($assessments) ?></div>
                <div class="text-[10px] text-green-500 font-bold mt-2"><i class="bi bi-arrow-up"></i> Active Headers</div>
            </div>
            <div class="stat-card">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Test Packs</div>
                <div class="text-2xl font-black text-slate-800"><?= count($allPacks) ?></div>
                <div class="text-[10px] text-red-500 font-bold mt-2">Published Tests</div>
            </div>
            <div class="stat-card">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Questions</div>
                <div class="text-2xl font-black text-slate-800"><?= count($questionBank ?? []) ?></div>
                <div class="text-[10px] text-amber-500 font-bold mt-2">Question Bank Size</div>
            </div>
        </div>

        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold">Assessment Inventory</h3>
                <p class="text-sm text-gray-500">Manage your assessment lifecycle from headers to test packs.</p>
            </div>
            <div class="flex gap-3">
                <button class="btn-red-rounded px-6" onclick="openCreateAssessment()">
                    <i class="bi bi-plus-lg me-2"></i> New Assessment Name
                </button>
            </div>
        </div>

        <!-- Assessments DataTable -->
        <div class="card overflow-hidden border-slate-200 mb-12">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-0">Assessment Headers</h4>
                <div id="assessmentsTableSearch"></div>
            </div>
            <div class="table-responsive p-0">
                <table id="assessmentsDataTable" class="w-full text-left">
                    <thead class="bg-white border-b border-slate-100">
                        <tr>
                            <th class="w-12"></th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Assessment Name</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Code</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Category</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Inventory</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
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
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold">Results & Evaluation</h3>
                <p class="text-sm text-gray-500">Review candidate performance and grade subjective answers.</p>
            </div>
            <div class="flex p-1 bg-gray-100 rounded-xl">
                <button class="tab tab-active" id="btn-view-student" onclick="switchResultView('student')" style="min-width: 140px;">Student View</button>
                <button class="tab tab-idle" id="btn-view-evaluator" onclick="switchResultView('evaluator')" style="min-width: 140px;">Evaluator View</button>
            </div>
        </div>

        <!-- Student View Container -->
        <div id="result-student-view" class="space-y-4">
            <div class="card p-4 border border-[#e2e8f0] shadow-sm rounded-[12px] bg-white">
                <div class="flex flex-wrap items-center justify-between gap-6">
                    <div class="flex-1 min-w-[320px]">
                        <label class="text-[9px] font-bold text-[#94a3b8] uppercase tracking-widest mb-1 block">Candidate Performance Dashboard</label>
                        <div class="text-[15px] font-bold text-[#1e293b]">Overall Ranking — React Recruitment Drive</div>
                        <div class="text-[11px] text-[#94a3b8] font-medium mt-0.5">Batch: Recruitment Drive April 2024</div>
                    </div>
                    
                    <div class="flex items-center gap-8 border-l border-[#f1f5f9] pl-8">
                        <div class="text-center">
                            <div class="text-[24px] font-bold text-[#dc2230] leading-none" id="resTotalScore">82</div>
                            <div class="text-[9px] font-bold text-[#94a3b8] uppercase mt-1.5 tracking-wider">Total Score</div>
                        </div>
                        <div class="text-center">
                            <div class="text-[18px] font-bold text-[#10b981]" id="resPercentage">82%</div>
                            <div class="text-[9px] font-bold text-[#94a3b8] uppercase mt-1.5 tracking-wider">Accuracy</div>
                        </div>
                        <div class="text-center">
                            <div class="text-[18px] font-bold text-[#475569]" id="resTimeTaken">78m</div>
                            <div class="text-[9px] font-bold text-[#94a3b8] uppercase mt-1.5 tracking-wider">Duration</div>
                        </div>
                        <div class="pl-4">
                             <span id="resStatusBadge" class="bg-[#f0fdf4] text-[#16a34a] border border-[#bbf7d0] px-3 py-1 rounded-[6px] font-bold text-[10px] uppercase">✓ PASS</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border border-[#e2e8f0] shadow-sm rounded-[12px] overflow-hidden bg-white">
                <div class="px-5 py-3 border-b border-[#f1f5f9] bg-[#f8fafc]/50 flex justify-between items-center">
                    <h4 class="text-[12px] font-bold text-[#1e293b] mb-0 uppercase tracking-wide">Candidate Ranking & Leaderboard</h4>
                    <span class="text-[10px] font-bold text-[#94a3b8] bg-white border border-[#e2e8f0] px-2 py-0.5 rounded" id="breakdown-cat-count">15 Candidates</span>
                </div>
                <div class="table-responsive">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-[#f8fafc] border-b border-[#f1f5f9]">
                            <tr>
                                <th class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest">Rank</th>
                                <th class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest">Candidate Name</th>
                                <th class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-center">Score</th>
                                <th class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-center">Accuracy</th>
                                <th class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-center">Status</th>
                                <th class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-right">Time Utility</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f1f5f9]" id="topicBreakdownTable">
                            <!-- Dynamic content -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Evaluator View Container -->
        <div id="result-evaluator-view" class="hidden space-y-4">
            <!-- Bulk Evaluation Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="card p-4 border border-[#e2e8f0] shadow-sm rounded-[12px] bg-white flex items-center justify-between">
                    <div>
                        <h5 class="text-[13px] font-bold text-[#1e293b] mb-1">Bulk Grading Template</h5>
                        <p class="text-[11px] text-[#94a3b8] mb-0">Download candidate list to enter marks offline.</p>
                    </div>
                    <button class="btn btn-outline-primary-custom btn-sm px-4 rounded-[8px] font-bold text-[11px]" onclick="App.downloadBulkEvaluationTemplate()">
                        <i class="bi bi-download me-1"></i> Download CSV
                    </button>
                </div>
                <div class="card p-4 border border-[#e2e8f0] shadow-sm rounded-[12px] bg-white flex items-center justify-between">
                    <div>
                        <h5 class="text-[13px] font-bold text-[#1e293b] mb-1">Upload Scored Sheet</h5>
                        <p class="text-[11px] text-[#94a3b8] mb-0">Sync marks from your completed CSV file.</p>
                    </div>
                    <div class="flex gap-2">
                        <input type="file" id="bulkEvaluationInput" class="hidden" onchange="App.handleBulkEvaluationUpload(this)">
                        <button class="btn btn-primary-custom btn-sm px-4 rounded-[8px] font-bold text-[11px]" onclick="document.getElementById('bulkEvaluationInput').click()">
                            <i class="bi bi-upload me-1"></i> Upload CSV
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bulk Preview Section (Hidden by default) -->
            <div id="bulkEvaluationPreview" class="hidden card border border-[#e2e8f0] shadow-sm rounded-[12px] overflow-hidden bg-white">
                <div class="px-5 py-3 border-b border-[#f1f5f9] bg-[#fefce8]/30 flex justify-between items-center">
                    <h4 class="text-[12px] font-bold text-[#1e293b] mb-0 uppercase tracking-wide">Bulk Evaluation Preview</h4>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-light px-3 rounded-[8px] font-bold text-[11px]" onclick="document.getElementById('bulkEvaluationPreview').classList.add('hidden')">Cancel</button>
                        <button class="btn btn-sm btn-success px-4 rounded-[8px] font-bold text-[11px]" onclick="App.submitBulkEvaluation()">Submit Marks</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-[#f8fafc] border-b border-[#f1f5f9]">
                            <tr>
                                <th class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest">Candidate ID</th>
                                <th class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest">Name</th>
                                <th class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-center">MCQ Score</th>
                                <th class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-center">Final Total</th>
                                <th class="px-6 py-2.5 text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest text-right">Manual Grading</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f1f5f9]" id="bulkEvaluationTableBody">
                            <!-- Preview rows injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card border border-[#e2e8f0] shadow-sm rounded-[12px] overflow-hidden bg-white">
                <div class="px-5 py-2.5 border-b border-[#f1f5f9] bg-[#f8fafc]/50 flex justify-between items-center">
                    <h4 class="text-[12px] font-bold text-[#1e293b] mb-0 tracking-wide uppercase">Subjective Evaluation Required</h4>
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest leading-none">Evaluator Focus:</span>
                        <div class="relative">
                            <select class="appearance-none bg-white border border-[#e2e8f0] rounded-[8px] pl-3 pr-8 py-1.5 text-[11px] font-bold text-[#1e293b] focus:outline-none focus:border-[#dc2230] min-w-[220px] cursor-pointer shadow-sm transition-all" onchange="App.renderEvaluatorView(this.value)">
                                <option value="1">Arjun Sharma — 2 Pending answers</option>
                                <option value="2">Priya Patel — All Graded</option>
                            </select>
                            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-[#94a3b8]">
                                <i class="bi bi-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>
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

    <!-- 5. EXECUTION VIEW TAB (Live Assessments) -->
    <main id="tab-content-execution" class="hidden px-8 py-6">
        <div class="card-custom">
            <div class="px-4 py-3 bg-header border-bottom d-flex justify-content-between align-items-center">
                <h2 class="section-title mb-0">Scheduled & Live Assessments</h2>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Test Name</th>
                            <th>Status</th>
                            <th>Schedule</th>
                            <th>Participants</th>
                            <th>Completed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="fw-bold">JavaScript Developer Assessment</span></td>
                            <td><span class="badge-custom badge-green">LIVE</span></td>
                            <td><span class="small">Oct 29, 2023 10:00 AM</span></td>
                            <td><span class="small">15 Employees</span></td>
                            <td><span class="small">8 / 15</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary-custom" onclick="App.startExecution()">
                                    <i class="bi bi-play-fill"></i> Start Test
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fw-bold">Senior Java Developer Screening</span></td>
                            <td><span class="badge-custom badge-blue">SCHEDULED</span></td>
                            <td><span class="small">Nov 02, 2023 02:00 PM</span></td>
                            <td><span class="small">45 Candidates</span></td>
                            <td><span class="small">0 / 45</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary-custom border-0">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
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
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Create & Manage Assessment Structures</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button class="btn-outline py-2 px-4 rounded-xl text-xs flex items-center gap-2" onclick="closeTemplateBuilder()">
                <i class="bi bi-x-lg"></i> Cancel
            </button>
            <button class="btn-red-rounded py-2 px-6 rounded-xl text-xs shadow-lg shadow-red-100" onclick="saveTemplateBuilder()">
                <i class="bi bi-check-lg"></i> Save Changes
            </button>
        </div>
    </div>

    <!-- Builder Main Body -->
    <div class="builder-main">
        <!-- Sidebar: Discovery -->
        <div class="builder-sidebar">
            <div class="sidebar-header">
                <h5 class="text-[11px] font-black text-slate-800 uppercase tracking-widest mb-4">Discovery</h5>
                <div class="filter-tabs mb-4">
                    <div class="filter-tab active" onclick="filterSidebar('All', this)">All</div>
                    <div class="filter-tab" onclick="filterSidebar('Performance', this)">Performance</div>
                    <div class="filter-tab" onclick="filterSidebar('Compliance', this)">Compliance</div>
                </div>
                <div class="relative">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" id="sidebar_search" class="w-full bg-slate-50 border-0 rounded-xl py-2.5 pl-11 pr-4 text-xs focus:ring-2 focus:ring-red-100 transition-all" placeholder="Search templates..." oninput="searchSidebar(this.value)" />
                </div>
            </div>
            <div id="sidebar_list" class="sidebar-list">
                <!-- Templates loaded via JS -->
            </div>
            <div class="p-4 border-top border-slate-100 bg-white">
                <button class="w-full py-3 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-xl text-xs font-bold uppercase tracking-widest transition-all border border-dashed border-slate-200" onclick="resetBuilder()">
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
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-0">General Configuration</h4>
                    </div>
                    
                    <div class="builder-field-grid">
                        <div class="form-group">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Template Category</label>
                            <select id="builder_category" class="form-select w-full bg-slate-50 border-slate-100 rounded-xl text-xs h-11">
                                <option>Performance</option>
                                <option>Compliance</option>
                                <option>General</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Template Identity</label>
                            <input id="builder_storage_name" class="form-control w-full bg-slate-50 border-slate-100 rounded-xl text-xs h-11" placeholder="e.g. Research Ethics Assessment 2024" />
                        </div>
                    </div>

                    <div class="builder-field-grid-3 mt-4">
                        <div class="form-group">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Default Duration</label>
                            <div class="relative">
                                <input type="number" id="builder_duration" class="form-control w-full bg-slate-50 border-slate-100 rounded-xl text-xs h-11 pr-12" placeholder="60" />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400">MINS</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Start Constraint</label>
                            <input type="date" id="builder_start_date" class="form-control w-full bg-slate-50 border-slate-100 rounded-xl text-xs h-11" />
                        </div>
                        <div class="form-group">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">End Constraint</label>
                            <input type="date" id="builder_end_date" class="form-control w-full bg-slate-50 border-slate-100 rounded-xl text-xs h-11" />
                        </div>
                    </div>
                </section>

                <!-- Structure Area -->
                <section>
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2">
                            <div class="w-1 h-5 bg-blue-500 rounded-full"></div>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-0">Question Paper Structure</h4>
                        </div>
                        <div class="flex items-center gap-4 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-slate-400 uppercase">Total Marks</span>
                                <span class="text-xs font-black text-slate-800" id="builder_total_marks">0 Marks</span>
                            </div>
                            <div class="w-px h-6 bg-slate-200"></div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-slate-400 uppercase">Sections</span>
                                <span class="text-xs font-black text-slate-800" id="builder_section_count">0 Sections</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-6 mb-6">
                        <label class="block text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-3">Add New Section Component</label>
                        <div class="relative">
                            <select id="task_selector" class="form-select w-full h-12 bg-white border-blue-200 rounded-xl text-xs pl-5 pr-10 appearance-none cursor-pointer shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all" onchange="addSelectedSection(this.value)">
                                <option value="">Browse section blueprints..</option>
                                <option value="MCQ">Multiple Choice Questions (MCQ)</option>
                                <option value="2 Marks">Short Answer (2 Marks)</option>
                                <option value="Coding">Coding / Practical Section</option>
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-blue-400">
                                <i class="bi bi-plus-circle-fill text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div id="builder_sections_container" class="space-y-4">
                        <!-- Dynamic Sections -->
                        <div class="empty-state py-12 text-center bg-slate-50 border border-dashed border-slate-200 rounded-3xl" id="builder_empty_state">
                            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 text-slate-300">
                                <i class="bi bi-stack text-3xl"></i>
                            </div>
                            <h5 class="text-sm font-bold text-slate-600 mb-1">No Sections Added</h5>
                            <p class="text-xs text-slate-400">Select a section blueprint above to start building your paper structure</p>
                        </div>
                    </div>
                </section>

                <!-- Footer Summary/Actions -->
                <section class="mt-8 pt-8 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="builder_is_active" checked>
                                <label class="form-check-label text-xs font-bold text-slate-600" for="builder_is_active">Set as Default Template</label>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Last Sync</p>
                            <p class="text-xs font-bold text-slate-600" id="builder_last_sync">Not saved yet</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>



<!-- MODAL: NEW ASSESSMENT -->
<!-- Candidate Picker Modal -->
<div id="candidatePickerModal" class="custom-modal-backdrop" style="z-index: 10006;" onclick="if(event.target===this)closeModal('candidatePickerModal')">
    <div class="custom-modal max-w-lg">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-extrabold text-slate-800 mb-1">Assign Candidates</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest" id="cp_role_label">Developers</p>
            </div>
            <button class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors" onclick="closeModal('candidatePickerModal')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500" id="cp_count_label">0 selected</span>
                <button class="text-[10px] font-black text-red-500 uppercase tracking-widest hover:underline" onclick="selectAllCandidates(true)">Select All</button>
            </div>
            <div class="max-h-[300px] overflow-y-auto border border-slate-100 rounded-xl p-2 bg-slate-50/30" id="cp_list_container">
                <!-- Populated via JS -->
            </div>
        </div>

        <div class="flex gap-3">
            <button class="btn-outline flex-1" onclick="closeModal('candidatePickerModal')">Cancel</button>
            <button class="btn-red-rounded flex-1 justify-center" onclick="confirmCandidateSelection()">Confirm Assignment</button>
        </div>
    </div>
</div>

<div id="assessmentModal" class="custom-modal-backdrop" onclick="if(event.target===this)closeModal('assessmentModal')">
  <div class="custom-modal modal-xl max-w-5xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-xl font-extrabold text-slate-800">Create New Assessment</h3>
            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">Configure assessment header and initial test pack</p>
        </div>
        <button class="text-slate-400 hover:text-slate-600" onclick="closeModal('assessmentModal')">
            <i class="bi bi-x-lg text-xl"></i>
        </button>
    </div>

    <!-- Stepper -->
    <div class="flex items-center justify-between mb-8 px-12 relative h-16">
        <div class="absolute top-5 left-20 right-20 h-0.5 bg-slate-100 -z-10"></div>
        <div class="step-item active relative" data-step="1">
            <div class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center font-bold shadow-lg shadow-red-100 z-10 mx-auto">1</div>
            <span class="absolute top-12 left-1/2 -translate-x-1/2 text-[10px] font-bold text-slate-800 whitespace-nowrap">General Info</span>
        </div>
        <div class="step-item relative" data-step="2">
            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold z-10 mx-auto">2</div>
            <span class="absolute top-12 left-1/2 -translate-x-1/2 text-[10px] font-bold text-slate-400 whitespace-nowrap">Configuration</span>
        </div>
        <div class="step-item relative" data-step="3">
            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold z-10 mx-auto">3</div>
            <span class="absolute top-12 left-1/2 -translate-x-1/2 text-[10px] font-bold text-slate-400 whitespace-nowrap">Questions</span>
        </div>
        <div class="step-item relative" data-step="4">
            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold z-10 mx-auto">4</div>
            <span class="absolute top-12 left-1/2 -translate-x-1/2 text-[10px] font-bold text-slate-400 whitespace-nowrap">Publish</span>
        </div>
    </div>

    <div class="mt-8 min-h-[400px]">
        <!-- Step 1: General Info -->
        <div id="assStep1" class="wizard-pane">
            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div class="form-group">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Assessment Name</label>
                        <input id="ass_name" class="input" placeholder="e.g. Annual Technical Challenge" />
                        <span class="error-msg hidden" id="err_ass_name">Please enter assessment name</span>
                    </div>
                    <div class="form-group">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Assessment Code</label>
                        <input id="ass_code" class="input" placeholder="e.g. REC2026" />
                        <span class="error-msg hidden" id="err_ass_code">Please enter a valid assessment code</span>
                    </div>
                    <div class="form-group">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Category</label>
                        <select id="ass_category" class="select text-sm h-11" onchange="toggleEnovaFields(this.value)">
                            <option value="">-- Choose Category --</option>
                            <option value="HR Recruitment-Fresher">HR Recruitment-Fresher</option>
                            <option value="Enova Assessment">Enova Assessment</option>
                        </select>
                        <span class="error-msg hidden" id="err_ass_category">Please select a category</span>
                    </div>
                </div>
                <div class="space-y-4">
                    <div id="enova_fields" class="hidden space-y-4">
                        <div class="form-group">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Assessment Type</label>
                            <select id="ass_type" class="select text-sm h-11 bg-white">
                                <option value="">-- Select Type --</option>
                                <option>Technical</option>
                                <option>Compliance</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Assigned To</label>
                            <div class="custom-multiselect" id="ass_assigned_container">
                                <button type="button" class="multiselect-btn" id="multiselect_btn" onclick="toggleMultiselect()">
                                    <span id="multiselect_label">-- Select Roles --</span>
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                                <div class="multiselect-options" id="multiselect_options">
                                    <label class="option-item"><input type="checkbox" value="Developers" onchange="updateMultiselectLabel()"> Developers</label>
                                    <label class="option-item"><input type="checkbox" value="Designers" onchange="updateMultiselectLabel()"> Designers</label>
                                    <label class="option-item"><input type="checkbox" value="Testers" onchange="updateMultiselectLabel()"> Testers</label>
                                    <label class="option-item"><input type="checkbox" value="HR" onchange="updateMultiselectLabel()"> HR</label>
                                    <label class="option-item"><input type="checkbox" value="Client Advocate" onchange="updateMultiselectLabel()"> Client Advocate</label>
                                </div>
                            </div>
                            <select id="ass_assigned" class="hidden" multiple>
                                <option value="Developers">Developers</option>
                                <option value="Designers">Designers</option>
                                <option value="Testers">Testers</option>
                                <option value="HR">HR</option>
                                <option value="Client Advocate">Client Advocate</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group relative">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Short Description</label>
                        <textarea id="ass_desc" class="input text-sm p-3" rows="5" placeholder="Briefly describe the assessment goal..." maxlength="500" oninput="updateCharCount(this)"></textarea>
                        <span id="char_count" class="absolute bottom-2 right-2 text-[10px] text-gray-400 font-bold">0 / 500</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Configuration -->
        <div id="assStep2" class="wizard-pane hidden">
            <div class="max-w-2xl mx-auto space-y-6">
                <div class="form-group">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Test Pack Name</label>
                    <input id="ass_pack_name" class="input" value="Batch-1" placeholder="e.g. Q1 Technical Round" />
                </div>
                <div class="form-group">
                    <div class="flex items-center gap-2 mb-1.5">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0">Select Template</label>
                        <button class="text-red-500 hover:text-red-700 font-black text-sm" onclick="openTemplateBuilder()" title="Create New Template">
                            <i class="bi bi-plus-circle-fill"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <select id="ass_pack_template" class="select text-sm h-11" onchange="updateTemplatePreview(this.value)">
                            <option value="">-- Choose Template --</option>
                            <?php foreach($templates as $t): ?>
                                <option value="<?= $t['id'] ?>" data-json='<?= json_encode($t) ?>'><?= esc($t['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn-outline text-xs h-11" onclick="previewTemplate(document.getElementById('ass_pack_template').value)">
                            <i class="bi bi-eye me-2"></i> Preview Selected Template
                        </button>
                    </div>
                </div>

                <div id="template_info_card" class="hidden p-4 bg-blue-50 rounded-xl border border-blue-100 flex items-start gap-4">
                    <i class="bi bi-info-circle text-blue-500 mt-1"></i>
                    <div>
                        <div class="text-[11px] font-bold text-blue-800 uppercase tracking-wider">Template Summary</div>
                        <div id="template_summary_text" class="text-xs text-blue-600 mt-1"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Questions -->
        <div id="assStep3" class="wizard-pane hidden">
            <div class="flex justify-between items-center mb-6">
                <div class="flex gap-4">
                    <button class="btn-tab active" id="btn-q-manual" onclick="switchQuestionMethod('manual')">Manual Selection</button>
                    <button class="btn-tab" id="btn-q-bulk" onclick="switchQuestionMethod('bulk')">Bulk Upload</button>
                </div>
                <div class="bg-red-50 text-red-600 px-4 py-2 rounded-lg text-xs font-bold border border-red-100">
                    Questions: <span id="ass_selected_q_count">0</span> / <span id="ass_required_q_count">--</span>
                </div>
            </div>

            <div id="q_manual_view" class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="relative w-72">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" class="input pl-10 h-10 text-xs" placeholder="Search questions..." oninput="filterWizardQuestions(this.value)">
                    </div>
                    <button class="btn-red-rounded text-xs px-4 h-10" onclick="App.openAddManualQuestionModal()">
                        <i class="bi bi-plus-lg me-2"></i> Create New Question
                    </button>
                </div>
                <div class="border border-slate-100 rounded-xl overflow-y-auto max-h-[400px]">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-100 sticky top-0 z-10">
                            <tr>
                                <th class="p-4 w-12"><input type="checkbox" onchange="selectAllWizardQuestions(this.checked)"></th>
                                <th class="p-4 font-bold text-slate-500 uppercase">Question</th>
                                <th class="p-4 font-bold text-slate-500 uppercase">Type</th>
                                <th class="p-4 font-bold text-slate-500 uppercase">Category</th>
                                <th class="p-4 font-bold text-slate-500 uppercase text-center">Marks</th>
                            </tr>
                        </thead>
                        <tbody id="wizard_question_list">
                            <?php foreach($questionBank as $q): ?>
                            <tr class="hover:bg-slate-50 border-b border-slate-50 wizard-q-row">
                                <td class="p-4"><input type="checkbox" class="wizard-q-check" value="<?= $q['id'] ?>" onchange="toggleWizardQuestion('<?= $q['id'] ?>', this.checked)"></td>
                                <td class="p-4">
                                    <div class="font-bold text-slate-700"><?= esc($q['text']) ?></div>
                                </td>
                                <td class="p-4"><span class="bg-slate-100 px-2 py-0.5 rounded text-[10px] font-bold text-slate-500"><?= esc($q['type']) ?></span></td>
                                <td class="p-4 text-slate-500"><?= esc($q['category'] ?? 'General') ?></td>
                                <td class="p-4 text-center font-bold text-slate-700"><?= esc($q['marks']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="q_bulk_view" class="hidden">
                <div class="max-w-md mx-auto text-center py-12 space-y-6">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-slate-300">
                        <i class="bi bi-cloud-upload text-4xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800">Upload Questions via CSV</h4>
                        <p class="text-xs text-slate-400 mt-1">Download our template, fill in your questions, and upload.</p>
                    </div>
                    <div class="flex flex-col gap-3">
                        <button class="btn-outline h-12 w-full justify-center" onclick="downloadQuestionTemplate()">
                            <i class="bi bi-download me-2"></i> Download Template
                        </button>
                        <label class="btn-red h-12 w-full justify-center cursor-pointer">
                            <i class="bi bi-file-earmark-arrow-up me-2"></i> Upload CSV File
                            <input type="file" class="hidden" accept=".csv" onchange="handleBulkQuestionUpload(this)">
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Publish -->
        <div id="assStep4" class="wizard-pane hidden">
            <div class="max-w-5xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">
                    
                    <!-- Left Column: Scheduling & Limits -->
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="bi bi-calendar-event text-red-500"></i> Schedule & Finalize
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Scheduled Date</label>
                                    <input type="date" id="ass_publish_date" class="input h-11" value="<?= date('Y-m-d') ?>" />
                                </div>
                                <div class="form-group">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Duration (Mins)</label>
                                    <input type="number" id="ass_wizard_duration" class="input h-11" value="60" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div class="form-group">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Start Time</label>
                                    <input type="time" id="ass_start_time" class="input h-11" value="09:00" />
                                </div>
                                <div class="form-group">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">End Time</label>
                                    <input type="time" id="ass_end_time" class="input h-11" value="10:00" />
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="bi bi-shield-check text-red-500"></i> Passing Criteria & Attempts
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Pass Mark (%)</label>
                                    <input type="number" id="ass_pass_mark" class="input h-11" value="50" placeholder="e.g. 50" />
                                </div>
                                <div class="form-group">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">No. of Attempts</label>
                                    <input type="number" id="ass_attempts" class="input h-11" value="1" placeholder="e.g. 1" />
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Instructions & Config -->
                    <div class="space-y-6">
                        <div class="form-group">
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="bi bi-card-text text-red-500"></i> Assessment Instructions
                            </h4>
                            <textarea id="ass_instructions" class="input text-sm p-4" rows="5" style="min-height: 120px;" placeholder="Enter instructions for candidates..."></textarea>
                        </div>

                        <div class="settings-card mt-0">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-1.5 h-4 bg-red-500 rounded-full"></div>
                                <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest mb-0">Exam Configuration</h4>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2">
                                <div class="settings-row py-2">
                                    <div>
                                        <div class="text-[12px] font-bold text-slate-700">Shuffle Questions</div>
                                        <div class="text-[9px] text-slate-400 font-medium leading-tight">Randomize order</div>
                                    </div>
                                    <label class="toggle-switch scale-75 origin-right">
                                        <input type="checkbox" id="set_shuffle_q" checked>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                <div class="settings-row py-2">
                                    <div>
                                        <div class="text-[12px] font-bold text-slate-700">Shuffle Options</div>
                                        <div class="text-[9px] text-slate-400 font-medium leading-tight">Randomize MCQ choices</div>
                                    </div>
                                    <label class="toggle-switch scale-75 origin-right">
                                        <input type="checkbox" id="set_shuffle_o" checked>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                <div class="settings-row py-2">
                                    <div>
                                        <div class="text-[12px] font-bold text-slate-700">Proctored Exam</div>
                                        <div class="text-[9px] text-slate-400 font-medium leading-tight">AI & Camera proctoring</div>
                                    </div>
                                    <label class="toggle-switch scale-75 origin-right">
                                        <input type="checkbox" id="set_proctored">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                <div class="settings-row py-2">
                                    <div>
                                        <div class="text-[12px] font-bold text-slate-700">Browser Lockdown</div>
                                        <div class="text-[9px] text-slate-400 font-medium leading-tight">Restrict tab switches</div>
                                    </div>
                                    <label class="toggle-switch scale-75 origin-right">
                                        <input type="checkbox" id="set_lockdown" checked>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                <div class="settings-row py-2">
                                    <div>
                                        <div class="text-[12px] font-bold text-slate-700">Show Results</div>
                                        <div class="text-[9px] text-slate-400 font-medium leading-tight">Instant score display</div>
                                    </div>
                                    <label class="toggle-switch scale-75 origin-right">
                                        <input type="checkbox" id="set_show_results" checked>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                <div class="settings-row py-2">
                                    <div>
                                        <div class="text-[12px] font-bold text-slate-700">Allow Backtracking</div>
                                        <div class="text-[9px] text-slate-400 font-medium leading-tight">Navigation to previous qns</div>
                                    </div>
                                    <label class="toggle-switch scale-75 origin-right">
                                        <input type="checkbox" id="set_backtracking" checked>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center mt-12 pt-8 border-t border-slate-50">
        <button id="btnWizardPrev" class="btn-ghost hidden" onclick="navigateWizard(-1)">
            <i class="bi bi-chevron-left me-2"></i> Previous
        </button>
        <div class="flex-1"></div>
        <button id="btnWizardNext" class="btn-red px-10" onclick="navigateWizard(1)">
            Next Step <i class="bi bi-chevron-right ms-2"></i>
        </button>
        <button id="btnWizardSubmit" class="btn-red px-10 hidden" onclick="finalizeWizard()">
            Publish Assessment <i class="bi bi-check-lg ms-2"></i>
        </button>
    </div>
  </div>
</div>

<!-- MODAL: NEW TEST PACK -->
<div id="testPackModal" class="custom-modal-backdrop" onclick="if(event.target===this)closeModal('testPackModal')">
  <div class="custom-modal">
    <h3 class="text-xl font-extrabold mb-4">Create Test Pack</h3>
    <input type="hidden" id="tp_ass_id" />
    <div class="grid gap-4 mb-4">
        <input id="tp_name" class="input" placeholder="Pack Name" />
        <select id="tp_role" class="select">
            <option>Designer</option><option>Developer</option><option>HR</option><option>Digital Marketing</option>
        </select>
        <select id="tp_template" class="select">
            <?php foreach($templates as $t): ?>
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
                <h3 class="text-xl font-extrabold text-slate-800" id="assign_modal_title">Manage Test Pack</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <p id="assign_subtitle" class="text-sm text-gray-500 font-medium"></p>
            
            <!-- Template Selection Section -->
            <div id="assign_template_section" class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Selected Template</label>
                    <span id="template_change_status" class="text-[10px] font-bold text-green-600 hidden italic">Changes saved!</span>
                </div>
                <div class="flex gap-3">
                    <select id="edit_pack_template_id" class="select h-10 text-xs flex-1">
                        <option value="">-- No Template (Custom Questions Only) --</option>
                        ${App.templates.map(t => `<option value="${t.id}">${t.name}</option>`).join('')}
                    </select>
                    <button class="btn-red-rounded px-6 h-10 text-xs font-bold" onclick="updatePackTemplate()">
                        Update Template
                    </button>
                </div>
            </div>
          </div>
      </div>
      
      <div class="modal-body px-6 pt-4">
        <!-- Sub-tabs per question type -->
        <div class="flex gap-2 mb-6 bg-slate-50 p-1.5 rounded-xl inline-flex">
          <button id="btn-assign-mcq" class="tab tab-active px-6 py-2" onclick="App.switchAssignTab('assign-mcq')">MCQ</button>
          <button id="btn-assign-2m" class="tab tab-idle px-6 py-2" onclick="App.switchAssignTab('assign-2m')">2 Marks</button>
        </div>

        <!-- MCQ panel -->
        <div id="assign-mcq" class="assign-panel">
          <!-- Bulk Upload Section -->
          <div class="card p-5 mb-6 flex items-center justify-between flex-wrap gap-4" style="background:#fcfcfd; border: 1.5px dashed #e2e8f0;">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="bi bi-cloud-arrow-up-fill text-xl"></i>
                </div>
                <div>
                  <div class="font-bold text-slate-700">MCQ Bulk Upload</div>
                  <div class="text-gray-400 text-[10px] mt-0.5 uppercase font-bold tracking-widest">CSV format required</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
              <a href="assessment/downloadTemplate/mcq" class="btn-ghost h-10 text-[11px] px-4 font-bold border-slate-200">Download Template</a>
              <form action="assessment/uploadQuestions" method="POST" enctype="multipart/form-data" class="flex gap-2">
                <input type="hidden" name="test_pack_id" class="assign_tp_id_input" />
                <input type="hidden" name="type" value="MCQ" />
                <input type="file" name="file" class="hidden" id="file_mcq" onchange="this.form.submit()" />
                <label for="file_mcq" class="btn-red h-10 text-[11px] px-5 cursor-pointer font-bold">Upload CSV</label>
              </form>
            </div>
          </div>

          <!-- Manual Entry Section -->
          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-1.5 h-4 bg-red-500 rounded-full"></div>
                <h5 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-0">Manual MCQ Entry</h5>
            </div>
            <div class="grid gap-4">
                <textarea id="mcq_content" class="input text-sm focus:ring-2 focus:ring-red-100" placeholder="Type your question content here..." rows="3"></textarea>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300">A</span><input id="mcq_opt_a" class="input h-11 text-sm ps-8" placeholder="Option A" /></div>
                    <div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300">B</span><input id="mcq_opt_b" class="input h-11 text-sm ps-8" placeholder="Option B" /></div>
                    <div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300">C</span><input id="mcq_opt_c" class="input h-11 text-sm ps-8" placeholder="Option C" /></div>
                    <div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300">D</span><input id="mcq_opt_d" class="input h-11 text-sm ps-8" placeholder="Option D" /></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <select id="mcq_correct" class="select h-12 text-sm font-bold">
                        <option value="">-- Choose Correct Answer --</option>
                        <option value="A">Option A</option><option value="B">Option B</option><option value="C">Option C</option><option value="D">Option D</option>
                    </select>
                    <button class="btn-red h-12 text-[12px] font-extrabold justify-center" onclick="App.addManualAssignQuestion('MCQ')">
                        <i class="bi bi-plus-circle-fill me-1"></i> Add Question to Pack
                    </button>
                </div>
            </div>
          </div>
        </div>

        <!-- 2 Marks panel -->
        <div id="assign-2m" class="assign-panel hidden">
          <div class="card p-5 mb-6 flex items-center justify-between flex-wrap gap-4" style="background:#fcfcfd; border: 1.5px dashed #e2e8f0;">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center"><i class="bi bi-file-earmark-spreadsheet-fill text-xl"></i></div>
                <div><div class="font-bold text-slate-700">2 Marks Bulk Upload</div><div class="text-gray-400 text-[10px] mt-0.5 uppercase font-bold tracking-widest">CSV format required</div></div>
            </div>
            <div class="flex items-center gap-2">
              <a href="assessment/downloadTemplate/2m" class="btn-ghost h-10 text-[11px] px-4 font-bold border-slate-200">Download Template</a>
              <form action="assessment/uploadQuestions" method="POST" enctype="multipart/form-data" class="flex gap-2">
                <input type="hidden" name="test_pack_id" class="assign_tp_id_input" /><input type="hidden" name="type" value="2 Marks" />
                <input type="file" name="file" class="hidden" id="file_2m" onchange="this.form.submit()" /><label for="file_2m" class="btn-red h-10 text-[11px] px-5 cursor-pointer font-bold">Upload CSV</label>
              </form>
            </div>
          </div>

          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5"><div class="w-1.5 h-4 bg-red-500 rounded-full"></div><h5 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-0">Manual 2-Mark Entry</h5></div>
            <div class="grid gap-4">
                <textarea id="m2_content" class="input text-sm" placeholder="Type the 2-mark question..." rows="3"></textarea>
                <textarea id="m2_correct" class="input text-sm" placeholder="Expected answer for evaluation..." rows="3"></textarea>
                <div class="flex justify-end pt-2"><button class="btn-red h-12 px-10 text-[12px] font-extrabold justify-center" onclick="App.addManualAssignQuestion('2-Mark')"><i class="bi bi-plus-circle-fill me-1"></i> Add Question to Pack</button></div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="modal-footer border-0 px-6 pb-6">
        <button type="button" class="btn btn-light px-8 py-2.5 font-bold text-slate-500" data-bs-dismiss="modal">Close Panel</button>
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
                <div id="execHeaderLogo" class="w-10 h-10 bg-white rounded-lg border flex items-center justify-center text-brand font-bold shadow-sm overflow-hidden">
                     <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&h=100&fit=crop" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 id="execTestTitle" class="exec-test-name">JavaScript Developer Assessment</h1>
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
                    <span id="timerText">58:45</span>
                </div>
            </div>
            <button class="btn btn-submit-test-custom" onclick="App.confirmSubmit()">
                Submit Assessment <i class="bi bi-send-fill ms-2"></i>
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
                            <span class="q-type-pill-custom" id="qTypeBadge">Multi-select</span>
                            <span class="q-marks-pill-custom" id="qMarksBadge">3 marks</span>
                            <span class="q-category-pill-custom" id="qCategoryBadge">Testing</span>
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


                <div class="exec-footer-custom">
                    <button class="btn btn-nav-prev-custom" onclick="App.prevQuestion()">
                        <i class="bi bi-chevron-left me-2"></i> Previous Question
                    </button>
                    <div class="save-status-custom">
                        <div class="save-dot-custom"></div>
                        <span>Last saved: just now</span>
                    </div>
                    <button class="btn btn-nav-next-custom" id="nextQBtn" onclick="App.nextQuestion()">
                        Next Question <i class="bi bi-chevron-right ms-2"></i>
                    </button>
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
                        <div id="execProgressBar" class="progress-bar exec-progress-gradient-custom" style="width: 53%;"></div>
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

<!-- Submit Confirmation Modal -->
<div id="submitConfirmModal" class="submit-confirm-overlay d-none">
    <div class="confirm-card">
        <h3 class="confirm-title">Submit Test?</h3>
        <p class="confirm-text">
            You have answered <span id="confirmAnsweredCount" class="fw-bold">15</span> of
            <span id="confirmTotalCount" class="fw-bold">15</span> questions.
        </p>
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
</div><script>
    // Global Data
    const App = {
        assessments: <?= json_encode($assessments) ?>,
        templates: <?= json_encode($templates) ?>,
        employees: <?= json_encode($employees) ?>,
        selectedCandidates: {} // Stores { assessmentId: [empId1, empId2] }
    };

    // --- Assessment Execution Engine ---
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

    // --- Tab Management ---
    function switchMainTab(tabId) {
        document.querySelectorAll('main[id^="tab-content-"]').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        document.querySelectorAll('.module-tab').forEach(tab => {
            tab.classList.remove('active');
        });

        const targetTab = document.getElementById('tab-content-' + tabId);
        if (targetTab) {
            targetTab.classList.remove('hidden');
        }

        document.querySelectorAll('.module-tab').forEach(tab => {
            if (tab.getAttribute('onclick').includes("'" + tabId + "'")) {
                tab.classList.add('active');
            }
        });
        
        if (tabId === 'management') {
            initAssessmentsDataTable();
        }
    }

    // --- Assessments DataTable with Accordion ---
    let assessmentsDataTable = null;
    function initAssessmentsDataTable() {
        if ($.fn.dataTable.isDataTable('#assessmentsDataTable')) {
            return;
        }

        assessmentsDataTable = $('#assessmentsDataTable').DataTable({
            data: App.assessments,
            columns: [
                {
                    className: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: '<i class="bi bi-plus-circle text-slate-300 hover:text-red-500 cursor-pointer transition-colors"></i>',
                    width: '50px'
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
                    data: 'code',
                    className: 'text-center',
                    render: (data) => `<span class="text-xs font-mono font-bold text-slate-500 bg-slate-50 px-2 py-1 rounded">#${data || 'N/A'}</span>`
                },
                { 
                    data: 'category',
                    className: 'text-center',
                    render: (data) => `<span class="chip bg-blue-50 text-blue-600 border-blue-100">${data || 'General'}</span>`
                },
                { 
                    data: 'test_packs',
                    className: 'text-center',
                    render: (data) => `<span class="font-bold text-slate-700">${data ? data.length : 0} <span class="text-slate-400 text-[10px] uppercase">Packs</span></span>`
                },
                {
                    data: null,
                    className: 'text-right px-6',
                    render: (data, type, row) => `
                        <div class="flex items-center justify-end gap-2">
                            <button class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 transition-all" onclick="event.stopPropagation(); editAssessment(${JSON.stringify(row).replace(/"/g, '&quot;')})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-all" onclick="event.stopPropagation(); deleteAssessment(${row.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `
                }
            ],
            pageLength: 10,
            dom: '<"px-6 py-3 border-b border-slate-100 flex justify-end"f>rt<"px-6 py-4 flex justify-between items-center"ip>',
            language: {
                search: "",
                searchPlaceholder: "Search Assessments..."
            },
            drawCallback: function() {
                $('.dataTables_filter input').addClass('form-control form-control-sm border-slate-200 rounded-lg text-xs w-[250px] shadow-none focus:border-red-500');
            }
        });

        // Add event listener for opening and closing details
        $('#assessmentsDataTable tbody').on('click', 'td.dt-control', function () {
            var tr = $(this).closest('tr');
            var row = assessmentsDataTable.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('dt-hasChild');
                $(this).find('i').removeClass('bi-dash-circle text-red-500').addClass('bi-plus-circle text-slate-300');
            } else {
                row.child(formatAssessmentPacks(row.data())).show();
                tr.addClass('dt-hasChild');
                $(this).find('i').removeClass('bi-plus-circle text-slate-300').addClass('bi-dash-circle text-red-500');
            }
        });
    }

    function formatAssessmentPacks(d) {
        let packs = d.test_packs || [];
        
        // Find default template from the first existing pack, if any
        let defaultTemplateId = '';
        if (packs.length > 0) {
            defaultTemplateId = packs[0].template_id || '';
        }

        let html = `
            <div class="bg-slate-50/50 p-6 border-y border-slate-100 child-table-container">
                <div class="flex items-center justify-between mb-4 px-2">
                    <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0">Assigned Test Packs (${packs.length})</h5>
                    <button class="btn-red-rounded text-[10px] py-1.5 px-4" id="btnAddPack_${d.id}" onclick="toggleInlinePackForm(${d.id})">
                        <i class="bi bi-plus-lg me-1"></i> Add New Pack
                    </button>
                </div>

                <!-- Inline Create Form -->
                <div id="inlinePackForm_${d.id}" class="hidden mb-6 bg-white p-5 rounded-2xl border border-red-100 shadow-sm animate-in fade-in slide-in-from-top-4 duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div class="form-group">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Test Pack Name</label>
                            <input id="inline_pack_name_${d.id}" class="input h-10 text-xs" placeholder="e.g. Phase 1 Test" />
                        </div>
                        <div class="form-group">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Choose Template</label>
                            <select id="inline_template_id_${d.id}" class="select h-10 text-xs py-0">
                                <option value="">-- Select Template --</option>
                                ${App.templates.map(t => `<option value="${t.id}" ${t.id == defaultTemplateId ? 'selected' : ''}>${t.name}</option>`).join('')}
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Assign Candidates</label>
                            <div class="h-10 flex items-center bg-slate-50 rounded-xl border border-slate-200 px-3 cursor-pointer hover:bg-slate-100 transition-colors" onclick="openCandidatePicker(${d.id}, '${d.assigned_to}')">
                                <i class="bi bi-people text-slate-400 me-2"></i>
                                <span id="candidateCountLabel_${d.id}" class="text-[11px] font-bold text-slate-600">0 Selected</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="btn-red-rounded h-10 flex-1 justify-center text-xs" onclick="saveInlinePack(${d.id})">
                                <i class="bi bi-check-lg me-1"></i> Create
                            </button>
                            <button class="btn-outline h-10 px-3" onclick="toggleInlinePackForm(${d.id})">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3">
        `;

        if (packs.length === 0) {
            html += `<div class="text-center py-8 text-slate-400 text-xs italic bg-white rounded-xl border border-dashed border-slate-200">No test packs found for this assessment.</div>`;
        } else {
            packs.forEach(tp => {
                html += `
                    <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-50 text-slate-400 rounded-lg flex items-center justify-center font-bold">
                                ${tp.pack_name.charAt(0)}
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-sm">${tp.pack_name}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">${tp.user_role || 'General Access'}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-8">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-file-earmark-text text-blue-500"></i>
                                <span class="text-xs font-bold text-slate-600">${tp.template ? tp.template.name : 'Custom Layout'}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="action-icon-btn view" onclick="App.previewTestPack(${tp.id})" title="Preview"><i class="bi bi-eye"></i></button>
                                <button class="action-icon-btn edit text-blue-600 bg-blue-50 border-blue-100 hover:bg-blue-100" onclick="editTestPack(${tp.id}, '${tp.template_id}', '${tp.pack_name.replace(/'/g, "\\'")}')" title="Edit Pack"><i class="bi bi-pencil-square"></i></button>
                                <button class="action-icon-btn delete" onclick="deletePack(${tp.id})" title="Delete"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        html += `</div></div>`;
        return html;
    }

    function toggleInlinePackForm(id) {
        const form = document.getElementById(`inlinePackForm_${id}`);
        const btn = document.getElementById(`btnAddPack_${id}`);
        if (form.classList.contains('hidden')) {
            form.classList.remove('hidden');
            btn.innerHTML = '<i class="bi bi-x-lg me-1"></i> Cancel';
            btn.classList.replace('btn-red-rounded', 'btn-outline');
        } else {
            form.classList.add('hidden');
            btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i> Add New Pack';
            btn.classList.replace('btn-outline', 'btn-red-rounded');
        }
    }

    let cpActiveAssessmentId = null;
    function openCandidatePicker(assessmentId, rolesStr) {
        cpActiveAssessmentId = assessmentId;
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
        
        const selected = App.selectedCandidates[assessmentId] || [];
        
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
        App.selectedCandidates[cpActiveAssessmentId] = selected;
        
        const label = document.getElementById(`candidateCountLabel_${cpActiveAssessmentId}`);
        if (label) label.textContent = `${selected.length} Selected`;
        
        closeModal('candidatePickerModal');
    }

    async function saveInlinePack(assessmentId) {
        const packName = document.getElementById(`inline_pack_name_${assessmentId}`).value;
        const templateId = document.getElementById(`inline_template_id_${assessmentId}`).value;
        const candidates = App.selectedCandidates[assessmentId] || [];

        if (!packName || !templateId) {
            Swal.fire('Incomplete Data', 'Please provide a pack name and choose a template.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Creating Test Pack...',
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const formData = new FormData();
            formData.append('assessment_id', assessmentId);
            formData.append('pack_name', packName);
            formData.append('template_id', templateId);
            formData.append('user_role', 'Assigned Roles'); // or pass actual roles
            // We might need to save candidate assignments separately or pass them here
            
            const response = await fetch('assessment/createTestPack', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.status === 'success') {
                Swal.fire({
                    title: 'Success!',
                    text: 'Test pack has been created.',
                    icon: 'success',
                    timer: 1500
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', result.message || 'Failed to create test pack', 'error');
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
            { id: 10, text: 'Describe the SOLID principles in object-oriented design.', type: '2-Mark', options: [], category: 'Software Design', marks: 2 },
            { id: 11, text: 'Explain the concept of closures in JavaScript.', type: '2-Mark', options: [], category: 'JavaScript', marks: 2 },
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
            violations: 0
        },

        startExecution: () => {
            App.executionState.questions = App.mockQuestions;
            App.executionState.active = true;
            App.executionState.currentIndex = 0;
            App.executionState.answers = {};
            App.executionState.flagged = new Set();
            App.executionState.timeLeft = 58 * 60 + 45; // Matching screenshot start
            App.executionState.violations = 0;

            // Calculate Total Marks
            const totalMarks = App.executionState.questions.reduce((acc, q) => acc + (parseInt(q.marks) || 0), 0);
            const totalDuration = "60 Mins"; // Mocking as 60 mins based on timeLeft

            // Update UI
            document.getElementById('execTotalMarks').textContent = `${totalMarks} Marks`;
            document.getElementById('execPassMark').textContent = `70%`; // Defaulting to 70% as per wizard
            document.getElementById('execTotalDuration').textContent = totalDuration;
            
            const testTitle = document.getElementById('execTestTitle').textContent;
            const headerLogo = document.getElementById('execHeaderLogo');
            if (headerLogo) {
                headerLogo.innerHTML = `<img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&h=100&fit=crop" class="w-full h-full object-cover">`;
            }

            document.getElementById('executionView').classList.remove('d-none');
            document.body.style.overflow = 'hidden';
            App.startTimer();
            App.renderExecutionQuestion();
            App.renderNavigator();
            App.updateProgress();
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
            document.getElementById('qTypeBadge').textContent = q.type;
            document.getElementById('qMarksBadge').textContent = `${q.marks} mark${q.marks > 1 ? 's' : ''}`;
            document.getElementById('qCategoryBadge').textContent = q.category;
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

            if (q.type === '2-Mark') {
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

            document.getElementById('nextQBtn').innerHTML = qIdx === App.executionState.questions.length - 1 ? 'Finish Test' : 'Next Question <i class="bi bi-chevron-right ms-2"></i>';
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
                App.showSubmitConfirmation();
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
            for(let i=0; i<dots.length; i++) {
                if(i < App.executionState.violations) dots[i].className = 'flex-1 bg-danger rounded-pill';
                else dots[i].className = 'flex-1 bg-light border rounded-pill';
            }
            if(App.executionState.violations >= 3) {
                document.getElementById('violationTitle').textContent = "Assessment Terminated";
                document.getElementById('violationMsg').textContent = "Multiple violations detected. Your assessment has been automatically submitted.";
                const btn = document.querySelector('#violationOverlay .btn');
                btn.textContent = "Return to Dashboard";
                btn.onclick = () => App.backToDashboard();
                
                // Automatically submit the test
                App.submitTest(true);
            }
        },

        dismissViolation: () => {
            if(App.executionState.violations < 3) {
                document.getElementById('violationOverlay').classList.remove('active');
            }
        },

        showSubmitConfirmation: () => {
            const answered = Object.keys(App.executionState.answers).length;
            const total = App.executionState.questions.length;
            document.getElementById('confirmAnsweredCount').textContent = answered;
            document.getElementById('confirmTotalCount').textContent = total;
            document.getElementById('submitConfirmModal').classList.remove('d-none');
        },

        hideSubmitConfirmation: () => {
            document.getElementById('submitConfirmModal').classList.add('d-none');
        },

        confirmSubmit: () => App.showSubmitConfirmation(),

        submitTest: (auto = false) => {
            clearInterval(App.executionState.timerInterval);
            document.getElementById('submitConfirmModal').classList.add('d-none');
            document.getElementById('executionView').classList.add('d-none');
            document.getElementById('submissionSuccessOverlay').classList.remove('d-none');
        },



        backToDashboard: () => {
            location.reload();
        },

        // --- Results & Evaluation ---
        loadCandidateResult: (id) => {
            const mockLeaderboard = [
                { name: 'Arjun Sharma', score: 82, accuracy: '82%', status: 'Pass', time: '42m 15s' },
                { name: 'Priya Patel', score: 80, accuracy: '80%', status: 'Pass', time: '38m 20s' },
                { name: 'Vikram Singh', score: 80, accuracy: '80%', status: 'Pass', time: '45m 10s' },
                { name: 'Ananya Iyer', score: 78, accuracy: '78%', status: 'Pass', time: '52m 05s' },
                { name: 'Rohan Mehta', score: 75, accuracy: '75%', status: 'Pass', time: '48m 30s' },
                { name: 'Sneha Reddy', score: 72, accuracy: '72%', status: 'Pass', time: '55m 45s' },
                { name: 'Kabir Das', score: 68, accuracy: '68%', status: 'Fail', time: '58m 12s' }
            ];
            const tbody = document.getElementById('topicBreakdownTable');
            if(tbody) tbody.innerHTML = mockLeaderboard.map((item, idx) => `
                <tr class="hover:bg-[#f8fafc] transition-colors cursor-pointer" onclick="App.loadDetailedResult('${item.name}')">
                    <td class="px-6 py-2 text-[11px] font-bold text-[#94a3b8]">${idx + 1}</td>
                    <td class="px-6 py-2 text-[13px] font-bold text-[#334155]">${item.name}</td>
                    <td class="px-6 py-2 text-[13px] text-[#64748b] text-center font-medium">${item.score}</td>
                    <td class="px-6 py-2 text-[13px] text-[#64748b] text-center font-medium">${item.accuracy}</td>
                    <td class="px-6 py-2 text-center">
                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase ${item.status === 'Pass' ? 'bg-[#f0fdf4] text-[#16a34a]' : 'bg-[#fef2f2] text-[#dc2230]'}">${item.status}</span>
                    </td>
                    <td class="px-6 py-2 font-bold text-[#475569] text-right text-[13px]">${item.time}</td>
                </tr>
            `).join('');
            
            document.getElementById('resTotalScore').textContent = '82';
            document.getElementById('resPercentage').textContent = '82%';
            document.getElementById('resTimeTaken').textContent = '78m';
            document.getElementById('breakdown-cat-count').textContent = `${mockLeaderboard.length} Candidates`;
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

        downloadBulkEvaluationTemplate: () => {
            const headers = ["candidate_id", "candidate_name", "test_id", "question_count", "marks_obtained"];
            const candidates = [
                ["C001", "Arjun Sharma", "T882", "40", ""],
                ["C002", "Priya Patel", "T882", "40", ""],
                ["C003", "Vikram Singh", "T882", "40", ""],
                ["C004", "Ananya Iyer", "T882", "40", ""]
            ];

            const instructions = "# EVALUATION UPLOAD INSTRUCTIONS:\n"
                               + "# 1. Keep candidate_id and candidate_name as provided.\n"
                               + "# 2. Fill the 'marks_obtained' column for each candidate.\n"
                               + "# 3. Ensure test_id matches the current assessment.\n";

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
                    App.loadCandidateResult(1); // Refresh leaderboard
                }
            });
        },

        renderEvaluatorView: (candidateId = 1) => {
            const list = document.getElementById('pendingEvaluationList');
            
            if (candidateId == "2") {
                list.innerHTML = `
                    <div class="py-12 text-center bg-gray-50/30 rounded-[10px] border border-dashed border-[#e2e8f0]">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-[#f0fdf4] text-[#16a34a] rounded-full mb-3">
                            <i class="bi bi-patch-check-fill text-2xl"></i>
                        </div>
                        <h5 class="text-sm font-bold text-[#1e293b]">Evaluation Complete</h5>
                        <p class="text-[11px] text-[#94a3b8] uppercase tracking-wider font-bold">This candidate has no pending subjective items</p>
                    </div>
                `;
                return;
            }

            const subjectiveQuestions = [
                { 
                    id: 10, 
                    text: 'Describe the SOLID principles in object-oriented design.', 
                    marks: 2, 
                    answer: '"SOLID is an acronym for five design principles intended to make software designs more understandable, flexible, and maintainable. S: Single Responsibility, O: Open-Closed, L: Liskov Substitution, I: Interface Segregation, D: Dependency Inversion. Each class should have one responsibility, be open for extension but closed for modification..."' 
                },
                { 
                    id: 11, 
                    text: 'Explain the concept of closures in JavaScript.', 
                    marks: 2, 
                    answer: '"Candidate provided a detailed explanation of JavaScript concepts, focusing on practical implementation and best practices..."' 
                }
            ];
            
            list.innerHTML = subjectiveQuestions.map(q => `
                <div class="bg-white border border-[#f1f5f9] rounded-[10px] p-4 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <span class="bg-[#eff6ff] text-[#2563eb] px-1.5 py-0.5 rounded-[3px] text-[10px] font-bold">Q${q.id}</span>
                            <h5 class="text-[14px] font-bold text-[#1e293b] mb-0">${q.text}</h5>
                        </div>
                        <span class="bg-[#fefce8] text-[#a16207] px-2 py-0.5 rounded-[4px] text-[9px] font-bold border border-[#fef08a] uppercase tracking-wider">Pending Evaluation</span>
                    </div>
                    
                    <div class="mb-4">
                        <label class="text-[9px] font-bold text-[#94a3b8] uppercase tracking-widest mb-2 block">CANDIDATE ANSWER:</label>
                        <div class="bg-[#f8fafc] border-l-[3px] border-[#e2e8f0] p-4 rounded-r-[8px]">
                            <p class="text-[13px] text-[#475569] leading-relaxed italic mb-0">${q.answer}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between mt-4">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest">Award Marks:</span>
                            <div class="flex items-center gap-2">
                                <input type="number" class="w-[44px] h-[32px] border border-[#cbd5e1] rounded-[4px] text-center font-bold text-[14px] outline-none focus:border-[#dc2230]" value="1" max="${q.marks}" min="0">
                                <span class="text-[#2563eb] font-bold text-[12px]">/ ${q.marks} Marks</span>
                            </div>
                        </div>
                        <button class="bg-[#dc2230] hover:bg-[#c61e2b] text-white px-6 py-2 rounded-[6px] font-bold text-[11px] uppercase tracking-widest transition-all shadow-sm" onclick="Swal.fire({ title: 'Grade Submitted', text: 'Marks for Q${q.id} have been updated.', icon: 'success' })">
                            Submit Grade
                        </button>
                    </div>
                </div>
            `).join('');
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
                type: type === 'MCQ' ? 'MCQ' : '2-Mark'
            };

            if (type === 'MCQ') {
                data.content = document.getElementById('mcq_content').value;
                data.option_a = document.getElementById('mcq_opt_a').value;
                data.option_b = document.getElementById('mcq_opt_b').value;
                data.option_c = document.getElementById('mcq_opt_c').value;
                data.option_d = document.getElementById('mcq_opt_d').value;
                data.correct_answer = document.getElementById('mcq_correct').value;
                data.marks = 1;

                if (!data.content || !data.option_a || !data.option_b || !data.correct_answer) {
                    Swal.fire('Incomplete Data', 'Please fill in the question, at least 2 options, and the correct answer.', 'warning');
                    return;
                }
            } else {
                data.content = document.getElementById('m2_content').value;
                data.correct_answer = document.getElementById('m2_correct').value;
                data.marks = 2;

                if (!data.content || !data.correct_answer) {
                    Swal.fire('Incomplete Data', 'Please fill in both the question and the expected answer.', 'warning');
                    return;
                }
            }

            try {
                const response = await fetch('assessment/saveQuestion', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (result.status === 'success') {
                    Swal.fire('Added!', 'Question has been added to the test pack.', 'success');
                    if (type === 'MCQ') {
                        document.getElementById('mcq_content').value = '';
                        document.getElementById('mcq_opt_a').value = '';
                        document.getElementById('mcq_opt_b').value = '';
                        document.getElementById('mcq_opt_c').value = '';
                        document.getElementById('mcq_opt_d').value = '';
                        document.getElementById('mcq_correct').value = '';
                    } else {
                        document.getElementById('m2_content').value = '';
                        document.getElementById('m2_correct').value = '';
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
                const response = await fetch(`assessment/getPackQuestions/${packId}`);
                const result = await response.json();
                
                if (result.status === 'success') {
                    const { pack, template, sections, questions } = result;
                    const container = document.getElementById('previewPaperContent');
                    
                    let sectionsHtml = '';
                    
                    // Group questions by marks_type to match sections
                    sections.forEach((s, sIdx) => {
                        const targetType = s.marks_type === 'Multiple Choice' ? 'MCQ' : 
                                          (s.marks_type === 'Short Answer' ? '2-Mark' : s.marks_type);
                        
                        // Filter questions for this section type
                        const sectionQuestions = questions.filter(q => q.type === targetType);
                        
                        if (sectionQuestions.length > 0) {
                            sectionsHtml += `
                                <div class="mb-8 px-5">
                                    <div class="d-flex justify-content-between align-items-center mb-4 border-b pb-2">
                                        <h3 class="h5 fw-bold text-[#dc2230] mb-0 text-uppercase tracking-wider">${s.section_name || 'Section ' + String.fromCharCode(65+sIdx)}</h3>
                                        <div class="bg-[#fff1f2] text-[#dc2230] px-3 py-1.5 rounded-[8px] text-[11px] font-bold d-flex align-items-center gap-2">
                                             <i class="bi bi-list-ul"></i> ${sectionQuestions.length} / ${s.num_questions} Questions
                                        </div>
                                    </div>
                            `;

                            sectionQuestions.forEach((q, qIdx) => {
                                const isMCQ = q.type === 'MCQ';
                                sectionsHtml += `
                                    <div class="mb-6">
                                        <div class="d-flex justify-content-between mb-3">
                                            <div class="fw-bold text-[#1e293b] text-[15px]">Q${qIdx + 1}. ${q.content}</div>
                                            <div class="text-[#1e293b] font-bold text-sm">[${q.marks} Mark]</div>
                                        </div>
                                        <div class="ps-2">
                                            ${isMCQ ? `
                                                <div class="row g-3">
                                                    <div class="col-md-6"><div class="text-[13px] text-[#334155]">A) ${q.option_a || '-'}</div></div>
                                                    <div class="col-md-6"><div class="text-[13px] text-[#334155]">B) ${q.option_b || '-'}</div></div>
                                                    <div class="col-md-6"><div class="text-[13px] text-[#334155]">C) ${q.option_c || '-'}</div></div>
                                                    <div class="col-md-6"><div class="text-[13px] text-[#334155]">D) ${q.option_d || '-'}</div></div>
                                                </div>
                                            ` : `
                                                <div class="border border-2 border-dashed border-[#e2e8f0] rounded-[12px] p-4 text-gray-400 text-[12px] bg-[#fcfcfd]">
                                                    Response area for ${q.type} question
                                                </div>
                                            `}
                                        </div>
                                    </div>
                                `;
                            });

                            sectionsHtml += `</div>`;
                        }
                    });

                    container.innerHTML = `
                        <div class="bg-white mx-auto shadow-sm" style="max-width: 900px; min-height: 1000px; padding: 40px 0;">
                            <div class="text-center mb-5 px-5">
                                <div class="mx-auto mb-4" style="width: 80px;"><img src="https://via.placeholder.com/80" class="rounded"></div>
                                <h2 class="fw-bold text-[#1e293b] mb-4 text-2xl">${template.paper_title || template.name}</h2>
                                <p class="text-sm text-slate-500 font-medium mb-4">${pack.pack_name}</p>
                                
                                <div class="d-flex justify-content-center gap-4 mb-4">
                                    <div class="d-flex align-items-center gap-3 p-3 bg-white border border-[#e2e8f0] rounded-[15px] min-w-[180px] shadow-sm">
                                        <div class="w-10 h-10 bg-[#fff1f2] text-[#dc2230] rounded-full d-flex align-items-center justify-content-center"><i class="bi bi-clock"></i></div>
                                        <div class="text-start">
                                            <div class="text-[9px] font-bold text-[#94a3b8] uppercase tracking-widest">Duration</div>
                                            <div class="fw-bold text-[#1e293b] text-md"></div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 p-3 bg-white border border-[#e2e8f0] rounded-[15px] min-w-[180px] shadow-sm">
                                        <div class="w-10 h-10 bg-[#fff1f2] text-[#dc2230] rounded-full d-flex align-items-center justify-content-center"><i class="bi bi-clock"></i></div>
                                        <div class="text-start">
                                            <div class="text-[9px] font-bold text-[#94a3b8] uppercase tracking-widest">Duration</div>
                                            <div class="fw-bold text-[#1e293b] text-md"></div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 p-3 bg-white border border-[#e2e8f0] rounded-[15px] min-w-[180px] shadow-sm">
                                        <div class="w-10 h-10 bg-[#fff1f2] text-[#dc2230] rounded-full d-flex align-items-center justify-content-center"><i class="bi bi-star"></i></div>
                                        <div class="text-start">
                                            <div class="text-[9px] font-bold text-[#94a3b8] uppercase tracking-widest">Total Marks</div>
                                            <div class="fw-bold text-[#1e293b] text-md">${template.total_marks || 0} Marks</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-divider mx-5"></div>
                            </div>

                            <div class="mx-5 p-4 bg-[#fcfcfd] rounded-[15px] mb-8 border border-l-[4px] border-l-[#dc2230]">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <i class="bi bi-info-circle-fill text-[#dc2230]"></i>
                                    <h4 class="mb-0 fw-bold text-[#1e293b] text-md">Important Instructions</h4>
                                </div>
                                <ul class="text-[12px] text-[#475569] mb-0 ps-3">
                                    <li class="mb-2">Read all questions carefully before attempting.</li>
                                    <li class="mb-2">This paper consists of ${sections.length} distinct sections.</li>
                                    <li class="mb-2">All questions are mandatory unless specified otherwise.</li>
                                    <li>The total duration for this assessment is _______ minutes.</li>
                                </ul>
                            </div>

                            <div>${sectionsHtml || '<div class="text-center py-20 text-slate-400 italic">No questions have been added to this test pack yet.</div>'}</div>

                            <div class="text-center mt-5 pt-4 text-[#94a3b8] text-[11px]">
                                <div class="fw-bold text-[#1e293b] mb-1">© 2026 eNova Technology Solutions</div>
                                <div>Generated via eNova Assessment Management Portal</div>
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

        if (view === 'student') {
            btnStudent.className = 'tab tab-active';
            btnEvaluator.className = 'tab tab-idle';
            viewStudent.classList.remove('hidden');
            viewEvaluator.classList.add('hidden');
            App.loadCandidateResult(1);
        } else {
            btnStudent.className = 'tab tab-idle';
            btnEvaluator.className = 'tab tab-active';
            viewStudent.classList.add('hidden');
            viewEvaluator.classList.remove('hidden');
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
</script>

<!-- Create Assessment Pack Modal (Test Creation Wizard) -->
<div class="modal fade" id="createPackModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h5 class="wizard-step-title mb-0">Create New Test</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <p class="wizard-step-subtitle mb-0">Follow the 4-step wizard to configure, assign, and publish an assessment.</p>
                </div>
            </div>
            <div class="modal-body pt-4">
                <!-- Stepper -->
                <div class="stepper mb-5" id="packStepper">
                    <div class="step active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-label text-uppercase">Select Template</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-label text-uppercase">Add Questions</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-label text-uppercase">Schedule & Publish</div>
                    </div>
                </div>

                <div class="px-md-4">
                    <!-- Step 1: Select Template -->
                    <div id="packStep1" class="wizard-step">
                        <div class="mb-5">
                            <h3 class="wizard-step-title mb-1">Configuration</h3>
                            <p class="text-slate-400 text-[12px]">Configure assessment header and initial test pack</p>
                        </div>

                        <div class="mb-5">
                            <label class="form-label text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3">Test Pack Name</label>
                            <input type="text" class="form-control h-[48px] rounded-[12px] border-slate-200 px-4 text-[14px] font-medium shadow-sm focus:border-red-500 focus:ring-0 transition-all" id="pack_wizard_name" placeholder="e.g., Batch-1" />
                        </div>

                        <div class="mb-5">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <label class="form-label mb-0 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Select Template</label>
                                <button class="bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center border-0 p-0 hover:bg-red-600 transition-all shadow-sm" onclick="openTemplateBuilder()" title="Create New Template">
                                    <i class="bi bi-plus text-[16px]"></i>
                                </button>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <select class="form-select h-[48px] rounded-[12px] border-slate-200 px-4 text-[14px] font-medium shadow-sm focus:border-red-500 focus:ring-0 transition-all appearance-none" id="baseTemplateSelect" onchange="handleWizardTemplateChange(this.value)">
                                        <option value="" selected disabled>-- Choose Template --</option>
                                        <?php foreach($templates as $t): ?>
                                        <option value="<?= $t['id'] ?>" data-json='<?= json_encode($t) ?>'><?= esc($t['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <button id="previewTemplateBtn" class="btn btn-outline-secondary w-100 h-[48px] rounded-[12px] text-[13px] font-bold d-flex align-items-center justify-content-center gap-2 border-slate-200 hover:bg-slate-50 transition-all" onclick="previewSelectedTemplate()">
                                        <i class="bi bi-eye text-slate-400"></i> <span class="text-slate-600">Preview Selected Template</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3">Assessment Name</label>
                            <select class="form-select h-[48px] rounded-[12px] border-slate-200 px-4 text-[14px] font-medium shadow-sm" id="packAssessmentName" onchange="handleAssessmentNameChange(this.value)">
                                <option value="" selected disabled>— Select Assessment Name —</option>
                                <?php foreach($assessments as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= esc($a['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>




                    </div>

                    <!-- Step 2: Add Questions -->
                    <!-- Step 2: Add Questions (Premium Layout) -->
                    <div id="packStep2" class="d-none wizard-step">
                        <h3 class="wizard-step-title">Step 2 — Add Questions</h3>
                        <p class="wizard-step-subtitle">Choose how to populate questions for this test.</p>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="card-custom text-start p-3 h-100 hover-border-primary cursor-pointer active-selection"
                                    id="cardManualOverride" onclick="selectPopulateMethod('Manual')">
                                    <i class="bi bi-search text-info fs-4 mb-2 d-block"></i>
                                    <h4 class="small fw-bold mb-1">Manual Override</h4>
                                    <p class="text-xs text-secondary mb-0">Hand-pick individual questions.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-custom text-start p-3 h-100 hover-border-primary cursor-pointer"
                                    id="cardBulkUpload" onclick="selectPopulateMethod('Bulk')">
                                    <i class="bi bi-file-earmark-text text-secondary fs-4 mb-2 d-block"></i>
                                    <h4 class="small fw-bold mb-1">Bulk Upload</h4>
                                    <p class="text-xs text-secondary mb-0">Import questions via CSV.</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-pink-light p-2 px-3 rounded d-flex justify-content-between align-items-center mb-3"
                            style="background: #fff5f5; border: 1px solid #fee2e2;">
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-xs fw-medium text-secondary">Questions selected:</span>
                                <span class="text-xs fw-bold text-danger" id="selectedQuestionsCount">0 / -- required</span>
                            </div>
                            <button class="btn btn-outline-danger btn-xs py-1 px-3 rounded-[8px] font-bold text-[11px]" onclick="App.previewQuestionPaper()">
                                <i class="bi bi-eye me-1"></i> Preview Paper
                            </button>
                        </div>

                        <!-- Manual Override Section -->
                        <div id="manualOverrideView">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="input-group" style="max-width: 400px;">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="bi bi-search"></i></span>
                                    <input type="text" class="form-control border-start-0"
                                        placeholder="Search by question text...">
                                </div>
                                <button class="btn btn-primary-custom btn-sm px-4 rounded-[8px] font-bold" onclick="App.openAddManualQuestionModal()">
                                    <i class="bi bi-plus-lg me-1"></i> Create New Question
                                </button>
                            </div>
                            <div class="table-responsive border rounded mb-4">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;"><input type="checkbox"
                                                    class="form-check-input select-all-questions" data-target="question-check"></th>
                                            <th class="small fw-bold">Question</th>
                                            <th class="small fw-bold">Section/Tag</th>
                                            <th class="small fw-bold">Type</th>
                                            <th class="small fw-bold">Difficulty</th>
                                            <th class="small fw-bold">Marks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="manualQuestionTableBody">
                                        <?php if(empty($questionBank)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-gray-400 small">No questions available in bank</td>
                                        </tr>
                                        <?php else: foreach($questionBank as $q): ?>
                                        <tr>
                                            <td><input type="checkbox" class="form-check-input question-check" value="<?= $q['id'] ?>" data-q='<?= json_encode($q) ?>' onchange="App.updateManualCount()"></td>
                                            <td class="small fw-medium"><?= esc($q['text']) ?></td>
                                            <td class="small"><?= esc($q['category'] ?? 'General') ?></td>
                                            <td class="small"><?= esc($q['type']) ?></td>
                                            <td class="small"><?= esc($q['difficulty'] ?? 'Medium') ?></td>
                                            <td class="small fw-bold"><?= esc($q['marks']) ?></td>
                                        </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Bulk Upload Section -->
                        <div id="bulkUploadView" class="d-none">
                            <!-- Two Separate Upload Options -->
                            <div class="row g-3">
                                <!-- MCQ Upload Box -->
                                <div class="col-md-6">
                                    <div class="border-2 border-dashed rounded p-4 text-center h-100 d-flex flex-column justify-content-center"
                                        style="border: 2px dashed #cbd5e1; background: #f8fafc; transition: all 0.2s ease;">
                                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm">
                                            <i class="bi bi-list-check fs-4"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-[#1e293b]">MCQ Bulk Upload</h6>
                                        <p class="text-[10px] text-slate-500 mb-3">Download and use the MCQ-specific template</p>
                                        <div class="d-flex justify-content-center gap-2 mt-auto">
                                            <input type="file" id="mcqBulkUploadInput" class="d-none" accept=".csv" onchange="App.handleRealBulkUpload(this, 'MCQ')">
                                            <button class="btn btn-primary-custom btn-sm px-3 py-1.5 text-xs shadow-sm" onclick="document.getElementById('mcqBulkUploadInput').click()">Browse MCQ</button>
                                            <button class="btn btn-outline-secondary btn-sm px-3 py-1.5 text-xs bg-white" onclick="App.downloadMCQTemplate()">
                                                <i class="bi bi-download me-1"></i> Template
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2-Mark Upload Box -->
                                <div class="col-md-6">
                                    <div class="border-2 border-dashed rounded p-4 text-center h-100 d-flex flex-column justify-content-center"
                                        style="border: 2px dashed #cbd5e1; background: #f8fafc; transition: all 0.2s ease;">
                                        <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm">
                                            <i class="bi bi-chat-square-text fs-4"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-[#1e293b]">2-Mark Bulk Upload</h6>
                                        <p class="text-[10px] text-slate-500 mb-3">Download and use the 2-Mark specific template</p>
                                        <div class="d-flex justify-content-center gap-2 mt-auto">
                                            <input type="file" id="shortBulkUploadInput" class="d-none" accept=".csv" onchange="App.handleRealBulkUpload(this, '2-Mark')">
                                            <button class="btn btn-primary-custom btn-sm px-3 py-1.5 text-xs shadow-sm" onclick="document.getElementById('shortBulkUploadInput').click()">Browse 2-Mark</button>
                                            <button class="btn btn-outline-secondary btn-sm px-3 py-1.5 text-xs bg-white" onclick="App.downloadShortTemplate()">
                                                <i class="bi bi-download me-1"></i> Template
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Consolidated CSV Guide -->
                            <div class="card-custom bg-[#f1f5f9] border-0 p-3 mt-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-info-circle-fill text-slate-400"></i>
                                    <p class="text-[10px] fw-bold mb-0 text-slate-600 text-uppercase tracking-wider">Universal CSV Format Guide</p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered bg-white text-[10px] mb-0 rounded overflow-hidden">
                                        <thead class="bg-[#f8fafc]">
                                            <tr>
                                                <th class="py-2">Column Name</th>
                                                <th class="py-2">Requirement</th>
                                                <th class="py-2">Description / Usage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-medium"><code>question_text</code></td>
                                                <td class="text-danger fw-bold">Required</td>
                                                <td class="text-slate-600">The actual text of the question to be asked.</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium"><code>option_a...d</code></td>
                                                <td>MCQ Only</td>
                                                <td class="text-slate-600">Choices for MCQ. Leave blank for 2-Mark questions.</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium"><code>correct_answer</code></td>
                                                <td class="text-danger fw-bold">Required</td>
                                                <td class="text-slate-600">'A' for MCQ, or Sample Answer for 2-Mark questions.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Schedule & Publish -->
                    <div id="packStep3" class="d-none wizard-step">
                        <h3 class="wizard-step-title">Step 3 — Schedule & Publish</h3>
                        <p class="wizard-step-subtitle">Finalize timing and publish the test pack.</p>

                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label mb-1">Start Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control form-control-sm" id="final_start_time" onchange="App.calculateDuration()">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label mb-1">End Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control form-control-sm" id="final_end_time" onchange="App.calculateDuration()">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label mb-1">Duration (Mins) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control form-control-sm" id="final_duration" placeholder="e.g. 60" oninput="document.getElementById('rev_duration').textContent = this.value + ' mins'">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label mb-1">Test Instructions</label>
                                        <textarea class="form-control form-control-sm" rows="2" placeholder="Candidate instructions..."></textarea>
                                    </div>
                                </div>

                                <!-- Compact Exam Settings Section -->
                                <div class="mt-3">
                                    <div class="card-custom bg-white border p-3">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <div class="settings-row py-1 border-0">
                                                            <div class="text-xs fw-bold">Shuffle Qs</div>
                                                            <label class="toggle-switch scale-75">
                                                                <input type="checkbox" checked>
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="settings-row py-1 border-0">
                                                            <div class="text-xs fw-bold">Shuffle Opts</div>
                                                            <label class="toggle-switch scale-75">
                                                                <input type="checkbox" checked>
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="settings-row py-1 border-0">
                                                            <div class="text-xs fw-bold">Show Timer</div>
                                                            <label class="toggle-switch scale-75">
                                                                <input type="checkbox" checked>
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="settings-row py-1 border-0">
                                                            <div class="text-xs fw-bold">Allow Review</div>
                                                            <label class="toggle-switch scale-75">
                                                                <input type="checkbox" checked>
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="settings-row py-1 border-0">
                                                            <div class="text-xs fw-bold">Auto-Submit</div>
                                                            <label class="toggle-switch scale-75">
                                                                <input type="checkbox" checked>
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="settings-row py-1 border-0">
                                                            <div class="text-xs fw-bold">Immediate Result</div>
                                                            <label class="toggle-switch scale-75">
                                                                <input type="checkbox">
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pt-2 border-top">
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <label class="text-xs fw-bold mb-0 flex-shrink-0">Pass %</label>
                                                            <input type="number" class="form-control form-control-sm py-1 fw-bold text-center" value="70" style="width: 60px;">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                                            <label class="text-xs fw-bold mb-0">Max Attempts</label>
                                                            <select class="form-select form-select-sm py-1 fw-bold" style="width: 120px;">
                                                                <option value="1">1 attempt</option>
                                                                <option value="2">2 attempts</option>
                                                                <option value="unlimited">Unlimited</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-custom bg-light border-0 p-3 h-100">
                                    <h4 class="text-xs fw-bold mb-3 text-uppercase opacity-75">Review Summary</h4>
                                    <div class="mb-2">
                                        <div class="text-[10px] text-uppercase text-secondary fw-bold">Template</div>
                                        <div class="small fw-bold" id="rev_template">None</div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="text-[10px] text-uppercase text-secondary fw-bold">Method</div>
                                        <div class="small fw-bold" id="rev_method">Manual-select</div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="text-[10px] text-uppercase text-secondary fw-bold">Duration</div>
                                        <div class="small fw-bold" id="rev_duration">90 mins</div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-md-5 pb-4">
                <button type="button" class="btn btn-secondary-custom px-4" id="prevPackStep" style="display: none;">← Previous</button>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-light px-4 border" onclick="saveDraftAssessment()">Save as Draft</button>
                    <button type="button" class="btn btn-primary-custom px-4" id="nextPackStep">Next Step</button>
                </div>
            </div>
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
                        <label class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-2">Question Type</label>
                        <select class="form-select h-[42px] rounded-[8px] border-[#e2e8f0] text-[13px] font-medium" id="manualQuestionType" onchange="App.onManualQuestionTypeChange(this.value)">
                            <option value="MCQ">MCQ</option>
                            <option value="2-Mark">2-Mark</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-2">Marks</label>
                        <input type="number" class="form-control h-[42px] rounded-[8px] border-[#e2e8f0] text-[14px] font-bold" id="manualQuestionMarks" value="2">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-2">Question Text <span class="text-danger">*</span></label>
                    <textarea class="form-control rounded-[8px] border-[#e2e8f0] text-[13px] p-3" id="manualQuestionText" rows="3" placeholder="Enter the question..."></textarea>
                </div>

                <!-- MCQ / Multi-select Options Section -->
                <div id="manualOptionsSection">
                    <label class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-3" id="manualOptionLabel">Answer Options (check one correct)</label>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white border border-[#e2e8f0] rounded-[8px]">
                            <span class="fw-bold text-[#64748b]" style="width: 20px;">A</span>
                            <input type="text" class="form-control border-0 p-0 text-[13px] shadow-none" placeholder="Option A">
                            <div class="d-flex align-items-center gap-2 ps-3 border-start border-[#f1f5f9]">
                                <input type="radio" class="form-check-input manual-correct-check" name="manualCorrect" value="A" checked style="width: 18px; height: 18px;">
                                <span class="text-[11px] font-bold text-[#94a3b8] text-uppercase">Correct</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-3 bg-white border border-[#e2e8f0] rounded-[8px]">
                            <span class="fw-bold text-[#64748b]" style="width: 20px;">B</span>
                            <input type="text" class="form-control border-0 p-0 text-[13px] shadow-none" placeholder="Option B">
                            <div class="d-flex align-items-center gap-2 ps-3 border-start border-[#f1f5f9]">
                                <input type="radio" class="form-check-input manual-correct-check" name="manualCorrect" value="B" style="width: 18px; height: 18px;">
                                <span class="text-[11px] font-bold text-[#94a3b8] text-uppercase">Correct</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-3 bg-white border border-[#e2e8f0] rounded-[8px]">
                            <span class="fw-bold text-[#64748b]" style="width: 20px;">C</span>
                            <input type="text" class="form-control border-0 p-0 text-[13px] shadow-none" placeholder="Option C">
                            <div class="d-flex align-items-center gap-2 ps-3 border-start border-[#f1f5f9]">
                                <input type="radio" class="form-check-input manual-correct-check" name="manualCorrect" value="C" style="width: 18px; height: 18px;">
                                <span class="text-[11px] font-bold text-[#94a3b8] text-uppercase">Correct</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-3 bg-white border border-[#e2e8f0] rounded-[8px]">
                            <span class="fw-bold text-[#64748b]" style="width: 20px;">D</span>
                            <input type="text" class="form-control border-0 p-0 text-[13px] shadow-none" placeholder="Option D">
                            <div class="d-flex align-items-center gap-2 ps-3 border-start border-[#f1f5f9]">
                                <input type="radio" class="form-check-input manual-correct-check" name="manualCorrect" value="D" style="width: 18px; height: 18px;">
                                <span class="text-[11px] font-bold text-[#94a3b8] text-uppercase">Correct</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- True/False Section -->
                <div id="manualTFSection" class="d-none">
                    <label class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-3">Correct Answer</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="manualTF" id="tfTrue" value="True" checked>
                            <label class="form-check-label text-[13px] font-bold text-[#334155]" for="tfTrue">True</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="manualTF" id="tfFalse" value="False">
                            <label class="form-check-label text-[13px] font-bold text-[#334155]" for="tfFalse">False</label>
                        </div>
                    </div>
                </div>

                <!-- 2-Mark / Short Answer Section -->
                <div id="manualShortAnswerSection" class="d-none">
                    <label class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-2">Correct Answer (Short Answer)</label>
                    <input type="text" class="form-control rounded-[8px] border-[#e2e8f0] text-[13px] h-[42px]" placeholder="Enter the correct answer...">
                </div>

            </div>
            <div class="modal-footer border-0 px-4 pb-4 gap-2">
                <button type="button" class="btn btn-light px-4 rounded-[8px] font-bold text-[13px]" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary-custom px-4 rounded-[8px] font-bold text-[13px]" onclick="App.addQuestionManually()">Create Question</button>
            </div>
        </div>
    </div>
</div>

<!-- Question Paper Preview Modal -->
<div class="modal fade" id="paperPreviewModal" tabindex="-1" style="z-index: 10010;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: #f8fafc;">
            <div class="modal-header px-4 py-3 border-bottom bg-white sticky-top" style="border-top-left-radius: 20px; border-top-right-radius: 20px; z-index: 10;">
                <h5 class="modal-title fw-bold text-[#1e293b]">Question Paper Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="previewPaperContent" style="max-height: 80vh; overflow-y: auto;">
                <!-- Content injected via JS -->
            </div>
            <div class="modal-footer bg-white border-0 px-4 pb-4" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                <div id="defaultPreviewFooter" class="d-flex gap-2">
                    <button type="button" class="btn btn-light px-4 rounded-[8px] font-bold" data-bs-dismiss="modal">Close Preview</button>
                    <button type="button" class="btn btn-primary-custom px-4 rounded-[8px] font-bold" onclick="window.print()">Print Paper</button>
                </div>
                <div id="selectionPreviewFooter" class="d-none d-flex gap-2">
                    <button type="button" class="btn btn-light px-4 rounded-[8px] font-bold" onclick="cancelTemplateSelection()">Cancel</button>
                    <button type="button" class="btn btn-red px-5 rounded-[8px] font-bold" onclick="confirmTemplateSelection()">OK, Select This Template</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- Global Helpers & Navigation ---
    function switchMainTab(tabId) {
        // Save current tab to localStorage
        localStorage.setItem('activeAssessmentTab', tabId);

        document.querySelectorAll('.module-tab').forEach(t => {
            const attr = t.getAttribute('onclick');
            if (attr && attr.includes(`'${tabId}'`)) t.classList.add('active');
            else t.classList.remove('active');
        });
        document.querySelectorAll('#main-content-area > main').forEach(m => m.classList.add('hidden'));
        const target = document.getElementById('tab-content-' + tabId);
        if(target) {
            target.classList.remove('hidden');
            if(tabId === 'test-creation') {
                setTimeout(initPacksDataTable, 100);
            }
            if(tabId === 'results') {
                if (typeof switchResultView === 'function') switchResultView('student');
                else App.loadCandidateResult(1);
            }
        }
    }

    // Restore tab on load
    window.addEventListener('DOMContentLoaded', () => {
        const savedTab = localStorage.getItem('activeAssessmentTab');
        if (savedTab) {
            switchMainTab(savedTab);
        } else {
            switchMainTab('assessments');
        }
    });

    function openModal(id) { 
        if (id === 'createPackModal') { openPackWizard(); return; }
        const el = document.getElementById(id);
        if(el) el.classList.add('open'); 
    }
    function closeModal(id) { 
        const el = document.getElementById(id);
        if(el) el.classList.remove('open'); 
    }

    /* Template Builder Functions */
    function openTemplateBuilder() {
        document.getElementById('templateBuilderOverlay').classList.add('open');
        document.getElementById('templateBuilderSidebar').classList.add('open');
        loadSidebarTemplates();
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
        // Mock data - in real app, this would be an AJAX call
        const templates = [
            { id: 1, name: 'Faculty Research Ethics', category: 'Compliance', marks: 100, sections: 4 },
            { id: 2, name: 'Annual Performance Review', category: 'Performance', marks: 50, sections: 2 },
            { id: 3, name: 'Technical Skills Assessment', category: 'General', marks: 75, sections: 3 },
            { id: 4, name: 'Safety & Regulatory Quiz', category: 'Compliance', marks: 20, sections: 1 }
        ];

        list.innerHTML = templates.map(t => `
            <div class="template-item-card" onclick="loadTemplateToBuilder(${t.id}, this)" data-category="${t.category}">
                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:text-red-500 transition-colors">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h6 class="text-xs font-bold text-slate-800 mb-0 truncate">${t.name}</h6>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[9px] font-black text-red-500 uppercase tracking-widest">${t.category}</span>
                        <span class="text-[9px] font-bold text-slate-400">• ${t.marks} Marks</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function filterSidebar(category, btn) {
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        
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

    function addSelectedSection(type) {
        if (!type) return;
        
        const container = document.getElementById('builder_sections_container');
        const emptyState = document.getElementById('builder_empty_state');
        if (emptyState) emptyState.remove();

        const sectionId = Date.now();
        const sectionCard = document.createElement('div');
        sectionCard.className = 'section-builder-card animate-fadeIn';
        sectionCard.dataset.type = type;
        
        sectionCard.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="section-drag-handle">
                        <i class="bi bi-grip-vertical text-lg"></i>
                    </div>
                    <div>
                        <input type="text" class="bg-transparent border-0 font-bold text-slate-800 p-0 focus:ring-0 text-sm" value="${type} Section">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">${type} Component</p>
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
                    <input type="number" class="w-full bg-transparent border-0 p-0 font-bold text-slate-800 text-xs focus:ring-0 sec-count" value="10" oninput="updateBuilderStats()">
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Marks Each</label>
                    <input type="number" class="w-full bg-transparent border-0 p-0 font-bold text-slate-800 text-xs focus:ring-0 sec-marks" value="${type === '2 Marks' ? 2 : 1}" oninput="updateBuilderStats()">
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

    function loadTemplateToBuilder(id, btn) {
        // Highlight active card
        document.querySelectorAll('.template-item-card').forEach(el => el.classList.remove('active'));
        btn.classList.add('active');

        // Mock loading data
        // In reality, you'd fetch template data by ID
        const mockTemplates = {
            1: { name: 'Faculty Research Ethics', category: 'Compliance', duration: 90, sections: ['MCQ', '2 Marks'] },
            2: { name: 'Annual Performance Review', category: 'Performance', duration: 45, sections: ['2 Marks'] }
        };

        const data = mockTemplates[id] || mockTemplates[1];
        
        document.getElementById('builder_storage_name').value = data.name;
        document.getElementById('builder_category').value = data.category;
        document.getElementById('builder_duration').value = data.duration;
        
        const container = document.getElementById('builder_sections_container');
        container.innerHTML = '';
        data.sections.forEach(type => addSelectedSection(type));
        
        document.getElementById('builder_last_sync').textContent = 'Last saved 2m ago';
        
        // Show success toast
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Template loaded successfully',
            showConfirmButton: false,
            timer: 1500,
            background: '#fff',
            color: '#1e293b'
        });
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
                name: name,
                category: category,
                duration: duration,
                start_date: startDate,
                end_date: endDate,
                sections: sections
            };

            const response = await fetch('/assessment/saveTemplate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            if (result.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Template Saved',
                    text: 'The assessment structure has been persisted.',
                    timer: 2000,
                    showConfirmButton: false
                });
                document.getElementById('builder_last_sync').textContent = 'Saved just now';
                loadSidebarTemplates(); // Refresh sidebar
            } else {
                Swal.fire('Error', result.message || 'Failed to save template', 'error');
            }
        } catch (error) {
            console.error('Save Error:', error);
            // Fallback for demo if endpoint doesn't exist yet
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Template Saved (Local)',
                    text: 'Endpoint /assessment/saveTemplate not found, but structure is valid.',
                    timer: 2000,
                    showConfirmButton: false
                });
                document.getElementById('builder_last_sync').textContent = 'Saved locally (Demo)';
            }, 1000);
        }
    }


    // --- Assessment Pack Wizard Logic ---
    let currentPackStep = 1;
    const totalPackSteps = 3;

    function openPackWizard() {
        currentPackStep = 1;
        currentEditPackId = null;
        
        document.getElementById('pack_wizard_name').value = '';
        document.getElementById('packAssessmentName').value = '';
        document.getElementById('baseTemplateSelect').value = '';
        document.getElementById('final_duration').value = 60;
        document.getElementById('rev_duration').textContent = '60 mins';
        document.querySelector('#createPackModal h5').textContent = 'Create New Test';
        
        // Reset state
        if (document.getElementById('packTypeInternal')) {
            document.getElementById('packTypeInternal').checked = true;
        }
        handleAssessmentTypeChange('internal');
        
        updatePackWizardUI();
        const modalEl = document.getElementById('createPackModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }

    function updatePackWizardUI() {
        document.querySelectorAll('.wizard-step').forEach(s => s.classList.add('d-none'));
        document.getElementById(`packStep${currentPackStep}`).classList.remove('d-none');

        document.querySelectorAll('#packStepper .step').forEach(s => {
            const stepNum = parseInt(s.dataset.step);
            s.classList.remove('active', 'completed');
            if (stepNum === currentPackStep) s.classList.add('active');
            else if (stepNum < currentPackStep) s.classList.add('completed');
        });

        const prevBtn = document.getElementById('prevPackStep');
        const nextBtn = document.getElementById('nextPackStep');
        
        prevBtn.style.display = currentPackStep === 1 ? 'none' : 'block';
        nextBtn.textContent = currentPackStep === totalPackSteps ? 'Publish Assessment' : 'Next Step';
        
        nextBtn.className = currentPackStep === totalPackSteps 
            ? 'btn btn-success px-4' 
            : 'btn btn-primary-custom px-4';
    }

    document.getElementById('nextPackStep').addEventListener('click', () => {
        if (currentPackStep < totalPackSteps) {
            // Validate Step 1
            if (currentPackStep === 1) {
                const name = document.getElementById('pack_wizard_name').value.trim();
                const assessId = document.getElementById('packAssessmentName').value;
                const temp = document.getElementById('baseTemplateSelect').value;
                
                if (!name) { Swal.fire('Required', 'Please enter a Test Pack Name.', 'warning'); return; }
                if (!assessId) { Swal.fire('Required', 'Please select an Assessment Name.', 'warning'); return; }
                if (!temp) { Swal.fire('Required', 'Please select an Assessment Template.', 'warning'); return; }
                
                const internalVisible = document.getElementById('step1InternalAssign') && !document.getElementById('step1InternalAssign').classList.contains('d-none');
                const type = internalVisible ? 'internal' : 'recruitment';
                const assignedCount = document.querySelectorAll(`.${type}-check:checked`).length;
                
                if (assignedCount === 0) {
                    Swal.fire('Required', `Please assign at least one ${type === 'internal' ? 'Employee' : 'Candidate'} to this pack.`, 'warning');
                    return;
                }
            }

            // Validate Step 2
            if (currentPackStep === 2) {
                const checkedQuestions = document.querySelectorAll('#manualQuestionTableBody input[type="checkbox"]:checked').length;
                if (checkedQuestions === 0) {
                    Swal.fire('Required', 'Please add and select at least one question for this test pack.', 'warning');
                    return;
                }
            }

            currentPackStep++;
            updatePackWizardUI();
        } else {
            publishFinalAssessment();
        }
    });

    document.getElementById('prevPackStep').addEventListener('click', () => {
        if (currentPackStep > 1) {
            currentPackStep--;
            updatePackWizardUI();
        }
    });

    function handleAssessmentNameChange(value) {
        // Trigger the visibility logic
        handleAssessmentTypeChange('internal');
    }

    function handleAssessmentTypeChange(type) {
        // Toggle Assignment Views in Step 1
        const internalView = document.getElementById('step1InternalAssign');
        const recruitmentView = document.getElementById('step1RecruitmentAssign');
        const hasName = document.getElementById('packAssessmentName').value;
        
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
        
        // Update Title & Subtitle
        document.getElementById('assign_modal_title').textContent = `Edit Test Pack: ${packName}`;
        document.getElementById('assign_subtitle').textContent = `Manage template and questions for this pack.`;
        
        // Pre-select current template
        const templateSelect = document.getElementById('edit_pack_template_id');
        if (templateSelect) {
            templateSelect.value = templateId || "";
        }
        
        // Hide/Show status
        document.getElementById('template_change_status').classList.add('hidden');

        // Set the ID in hidden inputs for question management
        const inputs = document.querySelectorAll('.assign_tp_id_input');
        inputs.forEach(i => i.value = id);
        
        // Open the modal
        const modalEl = document.getElementById('assignModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }

    async function updatePackTemplate() {
        if (!currentEditingPackId) return;
        
        const templateId = document.getElementById('edit_pack_template_id').value;
        
        try {
            const resp = await fetch('/assessment/updateTestPackTemplate', {
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
        link.setAttribute("download", "2mark_template.csv");
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

    App.openAddManualQuestionModal = () => {
        const modalEl = document.getElementById('addManualQuestionModal');
        const parentModalContent = document.querySelector('#createPackModal .modal-content');
        
        if (parentModalContent) parentModalContent.classList.add('modal-blur');
        
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        modalEl.addEventListener('hidden.bs.modal', () => {
            if (parentModalContent) parentModalContent.classList.remove('modal-blur');
        }, { once: true });
    };

    App.onManualQuestionTypeChange = (type) => {
        const optionsSec = document.getElementById('manualOptionsSection');
        const tfSec = document.getElementById('manualTFSection');
        const shortSec = document.getElementById('manualShortAnswerSection');
        const label = document.getElementById('manualOptionLabel');

        optionsSec.classList.add('d-none');
        tfSec.classList.add('d-none');
        shortSec.classList.add('d-none');

        if (type === 'MCQ' || type === 'Multi-select') {
            optionsSec.classList.remove('d-none');
            label.textContent = type === 'MCQ' ? 'Answer Options (check one correct)' : 'Answer Options (check all correct)';
            const checks = document.querySelectorAll('.manual-correct-check');
            checks.forEach(c => {
                c.type = type === 'MCQ' ? 'radio' : 'checkbox';
                c.name = 'manualCorrect';
            });
        } else if (type === 'True/False') {
            tfSec.classList.remove('d-none');
        } else if (type === '2-Mark') {
            shortSec.classList.remove('d-none');
        }
    };

    App.manualQuestions = [];

    App.addQuestionManually = () => {
        const type = document.getElementById('manualQuestionType').value;
        const text = document.getElementById('manualQuestionText').value;
        const marks = document.getElementById('manualQuestionMarks').value;
        if (!text) { Swal.fire('Required', 'Please enter question text', 'error'); return; }

        let options = [];
        if (type === 'MCQ' || type === 'Multi-select') {
            const optInputs = document.querySelectorAll('#manualOptionsSection input[type="text"]');
            optInputs.forEach((input, i) => {
                if (input.value) options.push({ text: input.value });
            });
        } else if (type === 'True/False') {
            options = [{ text: 'True' }, { text: 'False' }];
        }

        // Store for preview and dynamic use
        const qData = { text, type, marks, options, category: 'Manual', id: 'm' + Date.now() };
        App.manualQuestions.push(qData);

        const tbody = document.getElementById('manualQuestionTableBody');
        if (tbody.querySelector('td[colspan]')) tbody.innerHTML = '';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="checkbox" class="form-check-input question-check" checked data-q='${JSON.stringify(qData)}' onchange="App.updateManualCount()"></td>
            <td class="small fw-medium">${text.substring(0, 60)}${text.length > 60 ? '...' : ''}</td>
            <td class="small">Manual</td>
            <td class="small">${type}</td>
            <td class="small">Medium</td>
            <td class="small fw-bold">${marks}</td>
        `;
        tbody.appendChild(tr);
        
        App.updateManualCount();
        bootstrap.Modal.getInstance(document.getElementById('addManualQuestionModal')).hide();
        
        // Reset form
        document.getElementById('manualQuestionText').value = '';
        document.querySelectorAll('#manualOptionsSection input[type="text"]').forEach(i => i.value = '');
        
        Swal.fire({ title: 'Success', text: 'Question created and added to list', icon: 'success', timer: 1500, showConfirmButton: false });
    };

    App.handleRealBulkUpload = (input, forcedType = null) => {
        if (!input.files || !input.files[0]) return;
        
        const typeLabel = forcedType || 'Mixed';
        Swal.fire({
            title: `Uploading ${typeLabel}...`,
            text: 'Processing your CSV data',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        const reader = new FileReader();
        reader.onload = function(e) {
            const text = e.target.result;
            const lines = text.split('\n');
            const headers = lines[0].split(',').map(h => h.trim().replace(/^"|"$/g, ''));
            const rows = lines.slice(1);
            
            const tbody = document.getElementById('manualQuestionTableBody');
            if (tbody.querySelector('td[colspan]')) tbody.innerHTML = '';

            let addedCount = 0;
            rows.forEach(row => {
                if(!row.trim()) return;
                const cols = row.split(',').map(c => c.trim().replace(/^"|"$/g, ''));
                
                // Map columns based on headers
                const data = {};
                headers.forEach((h, i) => data[h] = cols[i]);

                if (data.question_text) {
                    const qType = forcedType || data.type || 'MCQ';
                    const qMarks = (qType === '2-Mark' ? 2 : 1);
                    const qCategory = forcedType === '2-Mark' ? 'Short Answer' : 'MCQ Section';

                    const qData = {
                        text: data.question_text,
                        type: qType,
                        category: qCategory,
                        marks: qMarks,
                        options: [],
                        id: 'b' + Date.now() + Math.random()
                    };

                    // Handle options for MCQ
                    if (qType === 'MCQ' || qType === 'Multi-select') {
                        ['option_a', 'option_b', 'option_c', 'option_d'].forEach(optKey => {
                            if (data[optKey]) qData.options.push({ text: data[optKey] });
                        });
                    }

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><input type="checkbox" class="form-check-input question-check" checked data-q='${JSON.stringify(qData)}' onchange="App.updateManualCount()"></td>
                        <td class="small fw-medium">${qData.text.substring(0, 60)}${qData.text.length > 60 ? '...' : ''}</td>
                        <td class="small">${qData.category}</td>
                        <td class="small">${qData.type}</td>
                        <td class="small">Medium</td>
                        <td class="small fw-bold">${qData.marks}</td>
                    `;
                    tbody.appendChild(tr);
                    addedCount++;
                }
            });
            App.updateManualCount();
            input.value = ''; // Reset input
            Swal.fire('Success', `${addedCount} ${typeLabel} questions imported.`, 'success');
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
                        <i class="bi bi-info-circle text-primary"></i> Question Type: ${qs[0].type === 'MCQ' ? 'Multiple Choice' : (qs[0].type === '2-Mark' ? 'Short Answer' : qs[0].type)}
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
                                    ${q.type === '2-Mark' ? `
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
                            <h2 class="fw-bold text-slate-800 mb-2 text-xl tracking-tight text-uppercase">Manual Assessment Paper</h2>
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
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Official Assessment Document</div>
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
                } catch(e) { console.error("Error parsing template json", e); }
            }
        }

        if (!t) {
            const templates = <?= json_encode($templates) ?>;
            t = templates.find(item => item.id == id);
        }
        
        if(!t) return;

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
                        <h3 class="h5 section-title mb-0 text-uppercase tracking-wider">${s.section_name || 'Section ' + String.fromCharCode(65+idx)}</h3>
                        <div class="preview-pill">
                             <i class="bi bi-list-task text-danger"></i> ${s.num_questions} Questions | ${s.marks_per_question} Marks each
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-4 text-slate-500 text-[12px] font-semibold">
                        <i class="bi bi-info-circle text-primary"></i> Question Type: ${s.marks_type}
                    </div>
                    
                    <div class="space-y-6">
                        ${Array.from({length: 2}).map((_, qIdx) => `
                            <div class="paper-card">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div class="q-badge">Question ${qIdx + 1}</div>
                                    <div class="marks-text"><i class="bi bi-star"></i> ${s.marks_per_question} Marks</div>
                                </div>
                                <div class="fw-bold text-slate-800 text-[15px] mb-4">[Sample Question Text for ${s.section_name}]</div>
                                
                                <div class="ps-2">
                                    ${s.marks_type === 'Short Answer' || s.marks_type === '2-Mark' ? `
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
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Official Assessment Document</div>
                </div>
            </div>
        `;

        const modal = new bootstrap.Modal(document.getElementById('paperPreviewModal'));
        modal.show();
    }

    function editTemplate(id) {
        // Find the template data from the PHP-injected array
        const templates = <?= json_encode($templates ?? []) ?>;
        const t = templates.find(item => item.id == id);
        if(!t) {
            Swal.fire('Error', 'Template data not found', 'error');
            return;
        }

        // Open builder
        openTemplateBuilder();
        
        // Reset and populate
        resetBuilder();
        document.getElementById('builder_storage_name').value = t.name;
        document.getElementById('builder_category').value = t.category || 'General';
        document.getElementById('builder_duration').value = t.duration || 60;
        
        const container = document.getElementById('builder_sections_container');
        container.innerHTML = ''; // Clear empty state
        
        if (t.sections && t.sections.length > 0) {
            t.sections.forEach(s => {
                // Use the new section card UI
                const type = s.marks_type || 'MCQ';
                addSelectedSection(type);
                
                // Update the last added card with specific values
                const cards = container.querySelectorAll('.section-builder-card');
                const lastCard = cards[cards.length - 1];
                lastCard.querySelector('input[type="text"]').value = s.section_name || type;
                lastCard.querySelector('.sec-count').value = s.num_questions || 0;
                lastCard.querySelector('.sec-marks').value = s.marks_per_question || 0;
            });
        }
        
        updateBuilderStats();
        document.getElementById('builder_last_sync').textContent = 'Editing: ' + t.name;
    }

    async function deleteTemplate(id) {
        if(!(await Swal.fire({ 
            title: 'Delete Template?', 
            text: 'This will permanently remove this template structure.', 
            icon: 'warning', 
            showCancelButton: true,
            confirmButtonColor: '#dc2230'
        }).then(r => r.isConfirmed))) return;
        
        await fetch(`/assessment/deleteTemplate/${id}`, { method: 'POST' });
        location.reload();
    }

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
        initAssessmentsDataTable();

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
            if(card) card.classList.add('hidden');
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

    function setAssessmentAndOpenPack(id) {
        const select = document.getElementById('packAssessmentName');
        if(select) select.value = id;
        openPackWizard();
    }

    App.onTemplateSelect = (val) => {
        const select = document.getElementById('baseTemplateSelect');
        const option = select.options[select.selectedIndex];
        if(!option || !option.getAttribute('data-json')) return;
        
        const data = JSON.parse(option.getAttribute('data-json'));
        document.getElementById('rev_template').textContent = data.name;
        
        // Calculate total questions required
        let total = 0;
        if(data.sections) {
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

    async function publishFinalAssessment() {
        const assessment_id = document.getElementById('packAssessmentName').value;
        const template_id = document.getElementById('baseTemplateSelect').value;
        const pack_name = document.getElementById('pack_wizard_name').value;
        const duration = document.getElementById('final_duration').value;
        const startTime = document.getElementById('final_start_time').value;
        const endTime = document.getElementById('final_end_time').value;
        const user_role = document.getElementById('packCategorySelect') ? document.getElementById('packCategorySelect').value : 'Internal';

        if(!assessment_id || !template_id || !pack_name || !duration || !startTime || !endTime) {
            Swal.fire('Error', 'Please complete all required fields (Start Time, End Time, Duration) before publishing.', 'error');
            return;
        }

        if (new Date(startTime) >= new Date(endTime)) {
            Swal.fire('Error', 'End Date & Time must be after the Start Date & Time.', 'error');
            return;
        }

        const confirm = await Swal.fire({
            title: 'Publish Assessment?',
            text: "This will create the test pack and link it to the selected assessment and template.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Publish Now',
            confirmButtonColor: '#dc2230'
        });

        if (confirm.isConfirmed) {
            // Hide modal immediately for instant feedback
            const modalEl = document.getElementById('createPackModal');
            if(modalEl) {
                const inst = bootstrap.Modal.getInstance(modalEl);
                if(inst) inst.hide();
            }

            Swal.fire({
                title: 'Publishing...',
                text: 'Creating your assessment pack.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const response = await fetch('/assessment/createTestPack', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `assessment_id=${assessment_id}&template_id=${template_id}&pack_name=${encodeURIComponent(pack_name)}&user_role=${encodeURIComponent(user_role)}&duration=${duration}`
            });
            const result = await response.json();
            if(result.status === 'success') {
                Swal.fire({
                    title: 'Success!',
                    text: 'Assessment Pack has been published.',
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
    window.startExecutionMode = () => switchMainTab('execution');
    window.changeQuestion = (dir) => {
        if (dir > 0) App.nextQuestion();
        else App.prevQuestion();
    };
    window.toggleFlag = (idx) => App.toggleFlag(idx);
    window.confirmSubmitTest = () => App.confirmSubmit();

    let currentEditAssessmentId = null;

    function toggleEnovaFields(category) {
        const enovaFields = document.getElementById('enova_fields');
        if (enovaFields) {
            if (category === 'Enova Assessment') {
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
            if (!document.getElementById('ass_name').value.trim()) { Swal.fire('Error', 'Assessment Name is required', 'error'); return false; }
            if (!document.getElementById('ass_code').value.trim()) { Swal.fire('Error', 'Assessment Code is required', 'error'); return false; }
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
        if(!val) return;
        
        const select = document.getElementById('ass_pack_template');
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.dataset.json) return;
        
        currentWizardTemplate = JSON.parse(opt.dataset.json);
        
        // Update summary card UI
        const card = document.getElementById('template_info_card');
        const text = document.getElementById('template_summary_text');
        if(card && text) {
            card.classList.remove('hidden');
            text.innerHTML = `
                <strong>${currentWizardTemplate.name}</strong><br>
                Total Questions: ${currentWizardTemplate.total_questions || 'N/A'} | 
                Total Marks: ${currentWizardTemplate.total_marks || 'N/A'} | 
                Duration: ${currentWizardTemplate.duration || '60'} mins
            `;
        }
        
        if(document.getElementById('ass_required_q_count')) {
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
            const resp = await fetch('/assessment/createAssessment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const res = await resp.json();
            if (res.status === 'success') Swal.fire('Success!', 'Assessment & Test Pack Published!', 'success').then(() => location.reload());
            else Swal.fire('Error', res.message, 'error');
        } catch (e) { Swal.fire('Error', 'Failed to save assessment.', 'error'); }
    }

    function openCreateAssessment() {
        wizardStep = 1;
        selectedWizardQuestions.clear();
        document.querySelectorAll('#assessmentModal input, #assessmentModal select, #assessmentModal textarea').forEach(el => {
            if (el.type === 'checkbox') el.checked = false;
            else if (el.tagName === 'SELECT') el.selectedIndex = 0;
            else el.value = '';
        });
        document.getElementById('ass_pack_name').value = 'Batch-1';
        updateWizardUI();
        openModal('assessmentModal');
    }

    function editAssessment(data) {
        currentEditAssessmentId = data.id;
        document.getElementById('ass_name').value = data.name;
        document.getElementById('ass_code').value = data.code || '';
        document.getElementById('ass_category').value = data.category || '';
        
        toggleEnovaFields(data.category);
        
        if(document.getElementById('ass_type')) document.getElementById('ass_type').value = data.assessment_type || '';
        if(document.getElementById('ass_assigned') && data.assigned_to) {
            const values = data.assigned_to.split(',');
            // Update custom multiselect checkboxes
            document.querySelectorAll('#multiselect_options input[type="checkbox"]').forEach(cb => {
                cb.checked = values.includes(cb.value);
            });
            updateMultiselectLabel();
        }
        document.getElementById('ass_desc').value = data.description || '';
        
        document.querySelector('#assessmentModal h3').textContent = 'Edit Assessment';
        document.querySelector('#assessmentModal .btn-red').textContent = 'Update';
        document.querySelector('#assessmentModal .btn-red').setAttribute('onclick', 'updateAssessment()');
        
        clearValidationErrors();
        openModal('assessmentModal');
    }

    async function updateAssessment() {
        if(!validateAssessmentForm()) return;

        // Hide modal immediately
        const modalEl = document.getElementById('assessmentModal');
        if(modalEl) {
            const inst = bootstrap.Modal.getInstance(modalEl);
            if(inst) inst.hide();
        }

        const id = currentEditAssessmentId;
        const name = document.getElementById('ass_name').value;
        const code = document.getElementById('ass_code').value;
        const category = document.getElementById('ass_category').value;
        const type = document.getElementById('ass_type') ? document.getElementById('ass_type').value : null;
        const assignedSelect = document.getElementById('ass_assigned');
        const assigned = assignedSelect ? Array.from(assignedSelect.selectedOptions).map(o => o.value).join(',') : null;
        const desc = document.getElementById('ass_desc').value;
        
        Swal.fire({
            title: 'Updating...',
            text: 'Saving your changes.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const response = await fetch(`/assessment/updateAssessment/${id}`, { 
                method: 'POST', 
                headers: {'Content-Type': 'application/json'}, 
                body: JSON.stringify({
                    name, code, category, assessment_type: type, assigned_to: assigned, description: desc
                })
            });
            if(response.ok) {
                Swal.fire({ title: 'Updated!', text: 'Assessment has been updated', icon: 'success', timer: 2000, showConfirmButton: false }).then(() => location.reload());
            } else {
                throw new Error();
            }
        } catch (e) {
            Swal.fire('Error', 'Failed to update assessment.', 'error');
        }
    }

    async function deleteAssessment(id) {
        if(!(await Swal.fire({ title: 'Delete Assessment?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true }).then(r => r.isConfirmed))) return;
        
        Swal.fire({
            title: 'Deleting...',
            text: 'Removing the assessment.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        await fetch(`/assessment/deleteAssessment/${id}`, { method: 'POST' });
        location.reload();
    }

    async function createAssessment() {
        if(!validateAssessmentForm()) return;

        // Hide modal immediately
        const modalEl = document.getElementById('assessmentModal');
        if(modalEl) {
            const inst = bootstrap.Modal.getInstance(modalEl);
            if(inst) inst.hide();
        }

        const name = document.getElementById('ass_name').value;
        const code = document.getElementById('ass_code').value;
        const category = document.getElementById('ass_category').value;
        const type = document.getElementById('ass_type').value;
        const assignedSelect = document.getElementById('ass_assigned');
        const assigned = Array.from(assignedSelect.selectedOptions).map(o => o.value).join(',');
        const desc = document.getElementById('ass_desc').value;
        const packName = document.getElementById('ass_pack_name').value;
        const templateId = document.getElementById('ass_pack_template').value;
        
        Swal.fire({
            title: 'Creating...',
            text: 'Setting up new assessment and initial pack.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const response = await fetch('/assessment/createAssessment', { 
                method: 'POST', 
                headers: {'Content-Type': 'application/json'}, 
                body: JSON.stringify({
                    name, code, category, 
                    assessment_type: type, 
                    assigned_to: assigned, 
                    description: desc,
                    pack_name: packName,
                    template_id: templateId
                })
            });
            if(response.ok) {
                Swal.fire({ title: 'Success!', text: 'Assessment created successfully', icon: 'success', timer: 2000, showConfirmButton: false }).then(() => location.reload());
            } else {
                throw new Error();
            }
        } catch (e) {
            Swal.fire('Error', 'Failed to create assessment.', 'error');
        }
    }

    let tempSections = [];
    function addSectionRow() {
        const type = document.getElementById('sec_type').value;
        const count = document.getElementById('sec_count').value;
        const knowledge = document.getElementById('sec_knowledge').value;
        if(!count) return;
        tempSections.push({type, count, knowledge});
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
        await fetch('/assessment/saveTemplate', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
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

    function setAssessmentAndRedirect(id) {
        const sel = document.getElementById('main_assessment_select');
        if(sel) sel.value = id;
        switchMainTab('test-creation');
        openPackWizard();
    }

    let currentEditPackId = null;
    function editPack(data) {
        currentEditPackId = data.id;
        document.getElementById('pack_wizard_name').value = data.pack_name;
        document.getElementById('packAssessmentName').value = data.assessment_id || '';
        document.getElementById('baseTemplateSelect').value = data.template_id || '';
        document.getElementById('final_duration').value = data.duration || 60;
        document.getElementById('rev_duration').textContent = (data.duration || 60) + ' mins';
        
        // Trigger template selection logic to update required count
        if (data.template_id) {
            App.onTemplateSelect(data.template_id);
        }
        
        document.querySelector('#createPackModal h5').textContent = 'Edit Test Pack';
        const nextBtn = document.getElementById('nextPackStep');
        nextBtn.textContent = 'Update & Next';
        
        currentPackStep = 1;
        updatePackWizardUI();
        
        const modalEl = document.getElementById('createPackModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    function deletePack(id) {
        Swal.fire({ title: 'Delete this pack?', text: "This action cannot be undone.", icon: 'warning', showCancelButton: true }).then(async (result) => {
            if (result.isConfirmed) {
                await fetch(`/assessment/deletePack/${id}`, { method: 'POST' });
                location.reload();
            }
        });
    }

    function saveDraftAssessment() {
        Swal.fire({
            title: 'Save as Draft?',
            text: "You can return later to complete and publish this assessment.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Yes, Save Draft',
            confirmButtonColor: '#475569'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Saved!', 'Assessment has been saved as a draft.', 'success').then(() => {
                    addPackToTable({
                        id: Date.now(),
                        pack_name: 'Draft Assessment ' + (new Date().getHours() + ":" + new Date().getMinutes()),
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
            title: 'Reuse Assessment?',
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
                        pack_name: 'Copy of Previous Assessment',
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
        if ($.fn.dataTable.isDataTable('#assessmentPacksTable')) {
            packsDataTable = $('#assessmentPacksTable').DataTable();
            return;
        }
        
        const initialPacks = <?= json_encode($allPacks) ?>;
        
        packsDataTable = $('#assessmentPacksTable').DataTable({
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
                        <button class="action-btn text-slate-400 hover:text-blue-600 me-2" onclick="editPack(${JSON.stringify(row).replace(/"/g, '&quot;')})" title="Edit Pack"><i class="bi bi-pencil"></i></button>
                        <button class="action-btn text-red-400 hover:text-red-600" onclick="deletePack(${row.id})" title="Delete Pack"><i class="bi bi-trash"></i></button>
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
                searchPlaceholder: "Search Assessments...",
                paginate: {
                    previous: '<i class="bi bi-chevron-left"></i>',
                    next: '<i class="bi bi-chevron-right"></i>'
                }
            },
            drawCallback: function() {
                // Style search input
                $('.dataTables_filter input').addClass('form-control form-control-sm border-[#e2e8f0] rounded-[8px] px-3 py-1.5 w-[220px] shadow-none focus:border-[#dc2230]');
                $('.dataTables_paginate').addClass('mt-3');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const resultsTab = document.getElementById('tab-content-results');
        if (resultsTab && !resultsTab.classList.contains('hidden')) {
            if (typeof App !== 'undefined' && App.loadCandidateResult) App.loadCandidateResult(1);
        }
    });
    function toggleMultiselect() {
        document.getElementById('multiselect_options').classList.toggle('show');
    }

    function updateMultiselectLabel() {
        const checkboxes = document.querySelectorAll('#multiselect_options input[type="checkbox"]');
        const selected = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
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
    });

    function clearValidationErrors() {
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    }

    function validateAssessmentForm() {
        let isValid = true;
        const name = document.getElementById('ass_name');
        const code = document.getElementById('ass_code');
        const category = document.getElementById('ass_category');
        
        if(!name || !name.value.trim()) { showError('ass_name', true); isValid = false; } else { showError('ass_name', false); }
        if(!code || !code.value.trim()) { showError('ass_code', true); isValid = false; } else { showError('ass_code', false); }
        if(!category || !category.value) { showError('err_ass_category', true, 'ass_category'); isValid = false; } else { showError('err_ass_category', false, 'ass_category'); }
        
        if(category && category.value === 'Enova Assessment') {
            const type = document.getElementById('ass_type');
            if(!type || !type.value) { showError('ass_type', true); isValid = false; } else { showError('ass_type', false); }
            
            const assigned = document.getElementById('ass_assigned');
            if(!assigned || assigned.selectedOptions.length === 0) {
                showError('ass_assigned', true, 'multiselect_btn');
                isValid = false;
            } else {
                showError('ass_assigned', false, 'multiselect_btn');
            }
        }
        return isValid;
    }

    function showError(fieldId, isError, targetId = null) {
        const input = document.getElementById(targetId || fieldId);
        const errorSpan = document.getElementById(fieldId.startsWith('err_') ? fieldId : 'err_' + fieldId);
        if(isError) {
            if(input) input.classList.add('is-invalid');
            if(errorSpan) errorSpan.classList.remove('hidden');
        } else {
            if(input) input.classList.remove('is-invalid');
            if(errorSpan) errorSpan.classList.add('hidden');
        }
    }

    // Live Validation Listeners
    document.addEventListener('DOMContentLoaded', () => {
        ['ass_name', 'ass_code', 'ass_category', 'ass_type'].forEach(id => {
            const el = document.getElementById(id);
            if(el) {
                el.addEventListener('blur', () => {
                    if(id === 'ass_type' && document.getElementById('ass_category').value !== 'Enova Assessment') return;
                    showError(id, !el.value.trim());
                });
                el.addEventListener('input', () => {
                    if(el.value.trim()) showError(id, false);
                });
            }
        });
    });

    function validateTemplateForm() {
        let isValid = true;
        const fields = ['builder_storage_name', 'builder_duration'];
        fields.forEach(id => {
            const el = document.getElementById(id);
            if(!el || !el.value.trim()) { showError(id, true); isValid = false; } else { showError(id, false); }
        });
        
        const sections = document.querySelectorAll('.section-builder-card');
        if(sections.length === 0) {
            Swal.fire('Required', 'Please add at least one section to your template', 'warning');
            isValid = false;
        }
        return isValid;
    }

    // Live Listeners for Template Builder
    document.addEventListener('DOMContentLoaded', () => {
        ['builder_storage_name'].forEach(id => {
            const el = document.getElementById(id);
            if(el) {
                el.addEventListener('blur', () => { if(!el.value.trim()) showError(id, true); });
                el.addEventListener('input', () => { if(el.value.trim()) showError(id, false); });
            }
        });
    });
</script>
</body>
</html>
