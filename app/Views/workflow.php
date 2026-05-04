<?php
// Mock data for demonstration if not provided by controller
if (empty($packs)) {
    $packs = [
        [
            'id' => 1,
            'pack_name' => 'React Frontend Core Assessment',
            'created_at' => '2024-04-15',
            'template_name' => 'Senior Developer Template',
            'status' => 'Live',
            'user_role' => 'Recruitment Team'
        ],
        [
            'id' => 2,
            'pack_name' => 'Data Structures & Algorithms v2',
            'created_at' => '2024-04-20',
            'template_name' => 'Standard DS Template',
            'status' => 'Draft',
            'user_role' => 'Engineering Lead'
        ],
        [
            'id' => 3,
            'pack_name' => 'Node.js Backend Security Pack',
            'created_at' => '2024-04-25',
            'template_name' => 'Security Audit v1',
            'status' => 'Live',
            'user_role' => 'CTO Office'
        ]
    ];
}
?>
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
        .table-responsive { overflow: hidden !important; } /* Remove unwanted scrollbars */
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
        .custom-modal-backdrop { position:fixed; inset:0; background:rgba(15,23,42,.45); display:none; align-items:flex-start; justify-content:center; z-index:50; padding:60px 16px; overflow:auto;}
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
        .accordion-header { cursor: pointer; transition: background 0.2s; }
        .accordion-header:hover { background-color: #f1f5f9; }

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
            justify-content: space-between;
            align-items: center;
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
    <div class="module-tab active" onclick="switchMainTab('assessments')">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-3-3.87"/><path d="M9 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Assessment Names
    </div>
    <div class="module-tab" onclick="switchMainTab('create-template')">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Create Template
    </div>
    <div class="module-tab" onclick="switchMainTab('test-creation')">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Assessment Pack
    </div>
</div>

<!-- MAIN CONTENT CONTAINER -->
<div id="main-content-area">

    <!-- 1. ASSESSMENT NAMES TAB -->
    <main id="tab-content-assessments" class="px-8 py-10">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold">Assessments</h3>
                <p class="text-sm text-gray-500">Manage all assessment headers and their associated test packs.</p>
            </div>
            <button class="btn-red-rounded" onclick="openModal('assessmentModal')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                Create New Assessment Name
            </button>
        </div>

        <div class="space-y-6">
            <?php foreach ($assessments as $a): ?>
            <div class="card overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-bottom accordion-header" onclick="toggleAccordion(this, 'acc-<?= $a['id'] ?>')">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-lg border flex items-center justify-center text-brand font-bold">
                            <?= substr($a['name'], 0, 1) ?>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800"><?= esc($a['name']) ?></h4>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[10px] uppercase font-bold text-gray-400">#<?= esc($a['code'] ?? 'ASS-'.$a['id']) ?></span>
                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                <?php if($a['assessment_type']): ?>
                                    <?php $typeClass = ($a['assessment_type'] == 'Technical') ? 'badge-tech' : 'badge-comp'; ?>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded <?= $typeClass ?>"><?= esc($a['assessment_type']) ?></span>
                                <?php endif; ?>
                                <?php $catClass = ($a['category'] == 'Enova Assessment') ? 'badge-enova' : 'badge-fresher'; ?>
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded <?= $catClass ?>"><?= esc($a['category'] ?? 'General') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="action-reveal flex gap-1 mr-2">
                            <button class="action-btn btn-edit" title="Edit Assessment" onclick="event.stopPropagation(); editAssessment(<?= $a['id'] ?>)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button class="action-btn btn-delete" title="Delete Assessment" onclick="event.stopPropagation(); deleteAssessment(<?= $a['id'] ?>)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </div>
                        <?php if($a['assigned_to']): ?>
                            <?php 
                                $roleClass = 'role-hr';
                                if(str_contains(strtolower($a['assigned_to']), 'dev')) $roleClass = 'role-dev';
                                if(str_contains(strtolower($a['assigned_to']), 'design')) $roleClass = 'role-design';
                                if(str_contains(strtolower($a['assigned_to']), 'test')) $roleClass = 'role-test';
                            ?>
                            <span class="text-[11px] font-extrabold px-2.5 py-1 rounded-lg <?= $roleClass ?>">FOR: <?= esc($a['assigned_to']) ?></span>
                        <?php endif; ?>
                        <span class="chip chip-mark"><?= esc($a['status']) ?></span>
                        <svg class="chevron ml-2 text-gray-400" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
                
                <div id="acc-<?= $a['id'] ?>" class="accordion-content">
                    <?php if($a['description']): ?>
                    <div class="px-6 py-3 bg-white border-b border-gray-50">
                        <p class="text-xs text-gray-500 line-clamp-2 italic leading-relaxed">
                            <svg class="inline-block mr-1 opacity-50" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            <?= esc($a['description']) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h5 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Assessment Packs</h5>
                        <button class="text-sm font-bold text-brand hover:underline" onclick="setAssessmentAndRedirect(<?= $a['id'] ?>)">+ Add Pack</button>
                    </div>
                    <?php if (empty($a['test_packs'])): ?>
                        <div class="py-4 text-center text-sm text-gray-400 italic">No packs created yet for this assessment.</div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($a['test_packs'] as $tp): ?>
                            <div class="pack-card p-4 border rounded-xl hover:border-brand-soft hover:bg-brand-soft transition-colors cursor-pointer relative" onclick="event.stopPropagation()">
                                <div class="action-reveal absolute top-2 right-2 flex gap-1">
                                    <button class="action-btn btn-edit" title="Edit Pack" onclick="editPack(<?= $tp['id'] ?>)">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <button class="action-btn btn-delete" title="Delete Pack" onclick="deletePack(<?= $tp['id'] ?>)">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </div>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="font-bold text-sm"><?= esc($tp['pack_name']) ?></div>
                                    <span class="text-[9px] bg-white px-2 py-0.5 rounded border border-gray-100 uppercase font-extrabold text-gray-500"><?= esc($tp['user_role']) ?></span>
                                </div>
                                <div class="text-[11px] text-gray-500 mb-3">Template: <span class="font-semibold text-gray-700"><?= esc($tp['template']['name'] ?? 'N/A') ?></span></div>
                                <button class="btn-ghost w-full justify-center h-8 text-[11px]" onclick="openAssignModal(<?= $tp['id'] ?>, '<?= esc($tp['template']['name'] ?? '') ?>')">Assign Questions</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- 2. CREATE TEMPLATE TAB -->
    <main id="tab-content-create-template" class="hidden px-8 py-10">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold">Template Management</h3>
                <p class="text-sm text-gray-500">Design question paper structures</p>
            </div>
            <button class="btn-red-rounded" onclick="openModal('templateModal')">+ Design New Template</button>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php foreach ($templates as $t): ?>
            <article class="card p-6">
                <header class="flex items-start justify-between mb-6">
                    <div>
                        <h4 class="text-lg font-bold"><?= esc($t['name']) ?></h4>
                        <p class="text-xs text-gray-400 mt-1">Structured with <?= count($t['sections']) ?> sections</p>
                    </div>
                    <button class="icon-btn text-red-500 border-none bg-red-50 hover:bg-red-100 h-8 w-8">✕</button>
                </header>
                <div class="space-y-3">
                    <?php foreach ($t['sections'] as $s): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="chip <?= $s['marks_type'] == 'MCQ' ? 'chip-mcq' : 'chip-2m' ?>"><?= esc($s['marks_type']) ?></span>
                            <span class="text-xs font-bold"><?= esc($s['num_questions']) ?> Questions</span>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400"><?= esc($s['knowledge_type']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- 3. ASSESSMENT PACK TAB (Updated to trigger Modal) -->
    <main id="tab-content-test-creation" class="hidden px-8 py-10">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold">Assessment Packs</h3>
                <p class="text-sm text-gray-500">Design, deploy, and analyze performance assessments across the organization.</p>
            </div>
            <button class="btn-red-rounded" onclick="openPackWizard()">
                <i class="bi bi-plus-lg me-1"></i> Create Pack
            </button>
        </div>

        <div class="card overflow-hidden p-3 border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 w-full" id="assessmentPacksTable">
                    <thead>
                        <tr class="bg-[#f8fafc] border-b border-[#f1f5f9]">
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase">Pack Name</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase">Template</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase">Assigned</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f1f5f9]">
                        <!-- Dynamic content by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <main id="tab-content-results" class="hidden px-8 py-10">
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
    <main id="tab-content-execution" class="hidden px-8 py-10">
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

<!-- MODAL: CREATE TEMPLATE -->
<div id="templateModal" class="custom-modal-backdrop" onclick="if(event.target===this)closeModal('templateModal')">
  <div class="custom-modal">
    <h3 class="text-xl font-extrabold mb-4">Create Question Paper Template</h3>
    <div class="grid gap-4 mb-4">
        <input id="temp_name" class="input" placeholder="Template Name" />
        <textarea id="temp_desc" class="input" placeholder="Description"></textarea>
    </div>
    <div class="border p-4 rounded-xl mb-4">
        <div class="grid grid-cols-3 gap-3 mb-3">
            <select id="sec_type" class="select"><option>MCQ</option><option>2 Marks</option></select>
            <input id="sec_count" type="number" class="input" placeholder="Questions" />
            <input id="sec_knowledge" class="input" placeholder="Knowledge Type" />
        </div>
        <button class="btn-ghost" onclick="addSectionRow()">+ Add Section</button>
    </div>
    <div id="section_preview" class="space-y-2 mb-4"></div>
    <div class="flex justify-end gap-2">
      <button class="btn-ghost" onclick="closeModal('templateModal')">Cancel</button>
      <button class="btn-red" onclick="saveTemplate()">Save Template</button>
    </div>
  </div>
</div>

<!-- MODAL: NEW ASSESSMENT -->
<div id="assessmentModal" class="custom-modal-backdrop" onclick="if(event.target===this)closeModal('assessmentModal')">
  <div class="custom-modal max-w-md">
    <h3 class="text-xl font-extrabold mb-4">Create Assessment</h3>
    <div class="grid gap-4 mb-4">
        <div class="form-group">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Assessment Name</label>
            <input id="ass_name" class="input" placeholder="e.g. Annual Technical Challenge" />
        </div>
        
        <div class="form-group">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Assessment Code</label>
            <input id="ass_code" class="input" placeholder="e.g. REC2026" />
        </div>
        
        <div class="form-group">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Category</label>
            <select id="ass_category" class="select text-sm h-11" onchange="toggleEnovaFields(this.value)">
                <option value="">-- Choose Category --</option>
                <option value="HR Recruitment-Fresher">HR Recruitment-Fresher</option>
                <option value="Enova Assessment">Enova Assessment</option>
            </select>
        </div>

        <!-- Conditional Enova Fields -->
        <div id="enova_fields" class="hidden grid gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
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
                <select id="ass_assigned" class="select text-sm h-11 bg-white">
                    <option value="">-- Select Role --</option>
                    <option>Developers</option>
                    <option>Designers</option>
                    <option>Testers</option>
                    <option>HR</option>
                    <option>Client Advocate</option>
                </select>
            </div>
        </div>

        <div class="form-group relative">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Short Description</label>
            <textarea id="ass_desc" class="input text-sm p-3" rows="3" placeholder="Briefly describe the assessment goal..." maxlength="500" oninput="updateCharCount(this)"></textarea>
            <span id="char_count" class="absolute bottom-2 right-2 text-[10px] text-gray-400 font-bold">0 / 500</span>
        </div>
    </div>
    <div class="flex justify-end gap-2">
      <button class="btn-ghost" onclick="closeModal('assessmentModal')">Cancel</button>
      <button class="btn-red" onclick="createAssessment()">Create</button>
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

<!-- MODAL: ASSIGN QUESTIONS -->
<div id="assignModal" class="custom-modal-backdrop" onclick="if(event.target===this)closeModal('assignModal')">
  <div class="custom-modal max-w-3xl">
    <h3 class="text-xl font-extrabold mb-2">Assign Questions</h3>
    <p id="assign_subtitle" class="text-sm text-gray-500 mb-4"></p>
    <div id="assign_tp_id_display" class="hidden"></div>
    
    <!-- Sub-tabs per question type -->
    <div class="flex gap-2 mb-4">
      <button id="btn-assign-mcq" class="tab tab-active" onclick="switchAssignTab('assign-mcq')">MCQ</button>
      <button id="btn-assign-2m" class="tab tab-idle" onclick="switchAssignTab('assign-2m')">2 Marks</button>
    </div>

    <!-- MCQ panel -->
    <div id="assign-mcq" class="assign-panel">
      <!-- Bulk Upload Section -->
      <div class="card p-4 mb-5 flex items-center justify-between flex-wrap gap-3" style="background:#fafbfc">
        <div class="text-sm">
          <div class="font-bold flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            MCQ Bulk Upload
          </div>
          <div class="text-gray-400 text-[10px] mt-0.5 uppercase font-bold tracking-wider">CSV format required</div>
        </div>
        <div class="flex items-center gap-2">
          <a href="/assessment/downloadTemplate/mcq" class="btn-ghost h-9 text-[11px]">Download Template</a>
          <form action="/assessment/uploadQuestions" method="POST" enctype="multipart/form-data" class="flex gap-2">
            <input type="hidden" name="test_pack_id" class="assign_tp_id_input" />
            <input type="hidden" name="type" value="MCQ" />
            <input type="file" name="file" class="hidden" id="file_mcq" onchange="this.form.submit()" />
            <label for="file_mcq" class="btn-red h-9 text-[11px] px-4 cursor-pointer">Upload CSV</label>
          </form>
        </div>
      </div>

      <!-- Manual Entry Section -->
      <div class="bg-white border border-gray-100 rounded-xl p-5">
        <h5 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Manual MCQ Entry</h5>
        <div class="grid gap-4">
            <textarea class="input text-sm" placeholder="Question Content" rows="2"></textarea>
            <div class="grid grid-cols-2 gap-3">
                <input class="input h-10 text-sm" placeholder="Option A" />
                <input class="input h-10 text-sm" placeholder="Option B" />
                <input class="input h-10 text-sm" placeholder="Option C" />
                <input class="input h-10 text-sm" placeholder="Option D" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <select class="select h-10 text-sm">
                    <option value="">Correct Answer</option>
                    <option>A</option><option>B</option><option>C</option><option>D</option>
                </select>
                <button class="btn-red-rounded h-10 text-xs justify-center" onclick="Swal.fire('Notice', 'Adding question manually...', 'info')">Add Question</button>
            </div>
        </div>
      </div>
    </div>

    <!-- 2 Marks panel -->
    <div id="assign-2m" class="assign-panel hidden">
      <!-- Bulk Upload Section -->
      <div class="card p-4 mb-5 flex items-center justify-between flex-wrap gap-3" style="background:#fafbfc">
        <div class="text-sm">
          <div class="font-bold flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            2 Marks Bulk Upload
          </div>
          <div class="text-gray-400 text-[10px] mt-0.5 uppercase font-bold tracking-wider">CSV format required</div>
        </div>
        <div class="flex items-center gap-2">
          <a href="/assessment/downloadTemplate/2m" class="btn-ghost h-9 text-[11px]">Download Template</a>
          <form action="/assessment/uploadQuestions" method="POST" enctype="multipart/form-data" class="flex gap-2">
            <input type="hidden" name="test_pack_id" class="assign_tp_id_input" />
            <input type="hidden" name="type" value="2 Marks" />
            <input type="file" name="file" class="hidden" id="file_2m" onchange="this.form.submit()" />
            <label for="file_2m" class="btn-red h-9 text-[11px] px-4 cursor-pointer">Upload CSV</label>
          </form>
        </div>
      </div>

      <!-- Manual Entry Section -->
      <div class="bg-white border border-gray-100 rounded-xl p-5">
        <h5 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Manual 2-Mark Entry</h5>
        <div class="grid gap-4">
            <textarea class="input text-sm" placeholder="Question Content" rows="2"></textarea>
            <textarea class="input text-sm" placeholder="Expected Answer" rows="2"></textarea>
            <div class="flex justify-end">
                <button class="btn-red-rounded h-10 text-xs px-8 justify-center" onclick="Swal.fire('Notice', 'Adding question manually...', 'info')">Add Question</button>
            </div>
        </div>
      </div>
    </div>
    
    <div class="flex justify-end mt-6">
      <button class="btn-ghost" onclick="closeModal('assignModal')">Close</button>
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
    // --- Assessment Execution Engine ---
    const App = {
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

            let csvContent = "data:text/csv;charset=utf-8," 
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
        }
    };

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
                        <div class="step-label text-uppercase">Assign Test</div>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-circle">4</div>
                        <div class="step-label text-uppercase">Schedule & Publish</div>
                    </div>
                </div>

                <div class="px-md-4">
                    <!-- Step 1: Select Template -->
                    <div id="packStep1" class="wizard-step">
                        <h3 class="wizard-step-title">Step 1 — Select Template</h3>
                        <p class="wizard-step-subtitle">Choose an assessment template to configure this test.</p>

                        <div class="mb-4">
                            <label class="form-label">Assessment Template <span class="text-danger">*</span></label>
                            <select class="form-select" id="baseTemplateSelect" onchange="App.onTemplateSelect(this.value)">
                                <option selected disabled>— Select a template —</option>
                                <?php foreach($templates as $t): ?>
                                <option value="<?= $t['id'] ?>" data-json='<?= json_encode($t) ?>'><?= esc($t['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label d-block">Assessment Type <span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="packAssessmentType" id="packTypeInternal" value="internal" checked onchange="handleAssessmentTypeChange(this.value)">
                                    <label class="form-check-label small fw-medium" for="packTypeInternal">Internal</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="packAssessmentType" id="packTypeRecruitment" value="recruitment" onchange="handleAssessmentTypeChange(this.value)">
                                    <label class="form-check-label small fw-medium" for="packTypeRecruitment">Recruitment</label>
                                </div>
                            </div>
                            <div class="col-md-6" id="packCategoryField">
                                <label class="form-label">Category</label>
                                <select class="form-select" id="packCategorySelect">
                                    <option>Technical</option>
                                    <option>Soft Skills</option>
                                    <option>Compliance</option>
                                </select>
                            </div>
                        </div>

                        <div class="card-custom p-0 border-1 mb-4 hidden" id="templateSummaryCard">
                            <div class="px-4 py-3 bg-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="h6 fw-bold mb-0" id="summ_temp_name">Template Name</h4>
                                <span class="badge-custom badge-blue" id="summ_temp_type">Internal</span>
                            </div>
                            <div class="p-4">
                                <div class="row g-4 mb-4">
                                    <div class="col-md-4 border-end">
                                        <div class="label-text mb-1">Total Questions</div>
                                        <div class="h5 fw-bold mb-0" id="summ_q_count">0</div>
                                    </div>
                                    <div class="col-md-4 border-end">
                                        <div class="label-text mb-1">Total Marks</div>
                                        <div class="h5 fw-bold mb-0" id="summ_total_marks">0</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="label-text mb-1">Estimated Duration</div>
                                        <div class="h5 fw-bold mb-0" id="summ_duration">0 min</div>
                                    </div>
                                </div>
                                <div class="pt-3 border-top">
                                    <div class="label-text mb-2">Structure Breakdown</div>
                                    <div class="small fw-medium" id="summ_structure">
                                        <!-- Dynamic structure -->
                                    </div>
                                </div>
                            </div>
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
                                <span class="text-xs fw-bold text-danger" id="selectedQuestionsCount">0 / 40 required</span>
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
                                        placeholder="Search by question text or category...">
                                </div>
                                <button class="btn btn-primary-custom btn-sm px-4 rounded-[8px] font-bold" onclick="App.openAddManualQuestionModal()">
                                    <i class="bi bi-plus-lg me-1"></i> Add Question Manually
                                </button>
                            </div>
                            <div class="table-responsive border rounded mb-4">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;"><input type="checkbox"
                                                    class="form-check-input"></th>
                                            <th class="small fw-bold">Question</th>
                                            <th class="small fw-bold">Category</th>
                                            <th class="small fw-bold">Type</th>
                                            <th class="small fw-bold">Difficulty</th>
                                            <th class="small fw-bold">Marks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="manualQuestionTableBody">
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-gray-400 small">Select a template to view available questions</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Bulk Upload Section -->
                        <div id="bulkUploadView" class="d-none">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <div class="border-2 border-dashed rounded p-4 text-center h-100 d-flex flex-column justify-content-center"
                                        style="border: 2px dashed #e5e7eb; background: #fcfcfd;">
                                        <i class="bi bi-cloud-arrow-up fs-2 text-secondary mb-2"></i>
                                        <h6 class="fw-bold mb-1">Drag & drop CSV</h6>
                                        <p class="text-[10px] text-secondary mb-3">or click to browse</p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-primary-custom btn-sm px-3 py-1 text-xs" onclick="App.handleBulkUploadMock()">Browse</button>
                                            <button class="btn btn-outline-secondary btn-sm px-3 py-1 text-xs" onclick="App.downloadTemplate()">
                                                <i class="bi bi-download"></i> Template
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="card-custom bg-light border-0 p-3 mb-0">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="bi bi-info-circle text-secondary text-xs"></i>
                                            <p class="text-[10px] fw-bold mb-0 text-uppercase">CSV Format Guide</p>
                                        </div>
                                        <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                                            <table class="table table-sm table-bordered bg-white text-[10px] mb-0">
                                                <thead class="table-light sticky-top">
                                                    <tr>
                                                        <th>Column</th>
                                                        <th>Req</th>
                                                        <th>Usage</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><code>question_text</code></td>
                                                        <td class="text-danger">Yes</td>
                                                        <td>Actual question content.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>type</code></td>
                                                        <td class="text-danger">Yes</td>
                                                        <td>MCQ, Multi, T/F, 2-Mark</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>option_a...d</code></td>
                                                        <td>Cond</td>
                                                        <td>Required for MCQ/Multi.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>correct_answer</code></td>
                                                        <td class="text-danger">Yes</td>
                                                        <td>'A', 'A,B', 'True', etc.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Assign Test -->
                    <div id="packStep3" class="d-none wizard-step">
                        <h3 class="wizard-step-title">Step 3 — Assign Test</h3>
                        <p class="wizard-step-subtitle">Assign the test to specific departments, roles, or individuals.</p>

                        <!-- Internal Assignment View -->
                        <div id="assignInternalView">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Department</label>
                                    <select class="form-select">
                                        <option>All Departments</option>
                                        <option>Engineering</option>
                                        <option>HR</option>
                                        <option>Product</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Role</label>
                                    <select class="form-select">
                                        <option>All Roles</option>
                                        <option>Developer</option>
                                        <option>Tester</option>
                                        <option>Designer</option>
                                    </select>
                                </div>
                            </div>

                            <div class="label-text fw-bold mb-2">Target Employees (0 selected)</div>
                            <div class="table-responsive border rounded">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                                            <th class="small fw-bold">Name</th>
                                            <th class="small fw-bold">Email</th>
                                            <th class="small fw-bold">Role</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="checkbox" class="form-check-input" checked></td>
                                            <td class="small fw-medium">John Doe</td>
                                            <td class="small">john.doe@company.com</td>
                                            <td class="small">Developer</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" class="form-check-input"></td>
                                            <td class="small fw-medium">Jane Smith</td>
                                            <td class="small">jane.smith@company.com</td>
                                            <td class="small">Designer</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Recruitment Assignment View -->
                        <div id="assignRecruitmentView" class="d-none">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">College / University</label>
                                    <select class="form-select">
                                        <option>All Colleges</option>
                                        <option>IIT Madras</option>
                                        <option>Anna University</option>
                                        <option>NIT Trichy</option>
                                        <option>PSG College of Technology</option>
                                        <option>Vellore Institute of Technology</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Batch / Passing Year</label>
                                    <select class="form-select">
                                        <option>All Batches</option>
                                        <option>2024 Passouts</option>
                                        <option>2025 Passouts</option>
                                        <option>2023 Passouts</option>
                                    </select>
                                </div>
                            </div>

                            <div class="label-text fw-bold mb-2">Target Freshers / Candidates (0 selected)</div>
                            <div class="table-responsive border rounded">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                                            <th class="small fw-bold">Candidate Name</th>
                                            <th class="small fw-bold">Email Address</th>
                                            <th class="small fw-bold">College</th>
                                            <th class="small fw-bold">GPA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="checkbox" class="form-check-input" checked></td>
                                            <td class="small fw-medium">Aditya Kumar</td>
                                            <td class="small">aditya.k@gmail.com</td>
                                            <td class="small">IIT Madras</td>
                                            <td class="small">8.9</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" class="form-check-input"></td>
                                            <td class="small fw-medium">Priya Sharma</td>
                                            <td class="small">priya.s@outlook.com</td>
                                            <td class="small">Anna University</td>
                                            <td class="small">9.1</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" class="form-check-input"></td>
                                            <td class="small fw-medium">Rahul Verma</td>
                                            <td class="small">rahul.v@gmail.com</td>
                                            <td class="small">NIT Trichy</td>
                                            <td class="small">8.5</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Schedule & Publish -->
                    <div id="packStep4" class="d-none wizard-step">
                        <h3 class="wizard-step-title">Step 4 — Schedule & Publish</h3>
                        <p class="wizard-step-subtitle">Finalize timing and publish the test pack.</p>

                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label mb-1">Start Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control form-control-sm" id="final_start_time">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label mb-1">End Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control form-control-sm" id="final_end_time">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label mb-1">Duration Override (minutes)</label>
                                        <input type="number" class="form-control form-control-sm" id="final_duration" placeholder="Default template duration">
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
                                    <div class="mb-0">
                                        <div class="text-[10px] text-uppercase text-secondary fw-bold">Assigned</div>
                                        <div class="small fw-bold" id="rev_assigned">1 Employee</div>
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
<div class="modal fade" id="addManualQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header px-4 py-3 border-bottom border-gray-100">
                <h5 class="modal-title fw-bold text-[#1e293b]">Add Question Manually</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-2">Question Type</label>
                        <select class="form-select h-[42px] rounded-[8px] border-[#e2e8f0] text-[13px] font-medium" id="manualQuestionType" onchange="App.onManualQuestionTypeChange(this.value)">
                            <option value="MCQ">MCQ</option>
                            <option value="Multi-select">Multi-select</option>
                            <option value="True/False">True/False</option>
                            <option value="2-Mark">2-Mark</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-2">Category</label>
                        <select class="form-select h-[42px] rounded-[8px] border-[#e2e8f0] text-[13px] font-medium" id="manualQuestionCategory">
                            <option>Java Basics</option>
                            <option>Python Core</option>
                            <option>React Hooks</option>
                            <option>SQL Advanced</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-[11px] font-bold text-[#64748b] text-uppercase tracking-wider mb-2">Marks</label>
                    <input type="number" class="form-control h-[42px] rounded-[8px] border-[#e2e8f0] text-[14px] font-bold" id="manualQuestionMarks" value="2">
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
                <button type="button" class="btn btn-primary-custom px-4 rounded-[8px] font-bold text-[13px]" onclick="App.addQuestionManually()">Add Question</button>
            </div>
        </div>
    </div>
</div>

<!-- Question Paper Preview Modal -->
<div class="modal fade" id="paperPreviewModal" tabindex="-1">
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
                <button type="button" class="btn btn-light px-4 rounded-[8px] font-bold" data-bs-dismiss="modal">Close Preview</button>
                <button type="button" class="btn btn-primary-custom px-4 rounded-[8px] font-bold" onclick="window.print()">Print Paper</button>
            </div>
        </div>
    </div>
</div>

<script>
    // --- Global Helpers & Navigation ---
    function switchMainTab(tabId) {
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

    function openModal(id) { 
        if (id === 'createPackModal') { openPackWizard(); return; }
        const el = document.getElementById(id);
        if(el) el.classList.add('open'); 
    }
    function closeModal(id) { 
        const el = document.getElementById(id);
        if(el) el.classList.remove('open'); 
    }

    // --- Assessment Pack Wizard Logic ---
    let currentPackStep = 1;
    const totalPackSteps = 4;

    function openPackWizard() {
        currentPackStep = 1;
        // Reset assessment type to internal on open
        document.getElementById('packTypeInternal').checked = true;
        handleAssessmentTypeChange('internal');
        
        updatePackWizardUI();
        const modalEl = document.getElementById('createPackModal');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
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
            if (currentPackStep === 1) {
                const temp = document.getElementById('baseTemplateSelect').value;
                if (!temp) { Swal.fire('Required', 'Please select a template to continue.', 'warning'); return; }
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

    function handleAssessmentTypeChange(type) {
        // Toggle Category field in Step 1
        const field = document.getElementById('packCategoryField');
        if (field) {
            field.classList.toggle('d-none', type === 'recruitment');
        }

        // Toggle Step 3 Views (Assign Test)
        const internalView = document.getElementById('assignInternalView');
        const recruitmentView = document.getElementById('assignRecruitmentView');
        const subtitle = document.querySelector('#packStep3 .wizard-step-subtitle');

        if (internalView && recruitmentView) {
            if (type === 'recruitment') {
                internalView.classList.add('d-none');
                recruitmentView.classList.remove('d-none');
                if (subtitle) subtitle.textContent = "Assign the test to colleges, batches, or specific fresher candidates.";
            } else {
                internalView.classList.remove('d-none');
                recruitmentView.classList.add('d-none');
                if (subtitle) subtitle.textContent = "Assign the test to specific departments, roles, or individuals.";
            }
        }
        
        // Update summary label in Step 1
        const summTypeBadge = document.getElementById('summ_temp_type');
        if (summTypeBadge) {
            summTypeBadge.textContent = type.charAt(0).toUpperCase() + type.slice(1);
            summTypeBadge.className = type === 'recruitment' ? 'badge-custom badge-purple' : 'badge-custom badge-blue';
        }
    }

    App.downloadTemplate = () => {
        const headers = ["question_text", "type", "section", "marks", "option_a", "option_b", "option_c", "option_d", "correct_answer", "rubrics"];
        const rows = [
            ["What is the capital of France?", "MCQ", "Geography", "1", "Paris", "London", "Berlin", "Madrid", "A", ""],
            ["Select even numbers.", "Multi-select", "Math", "2", "2", "3", "4", "5", "A,C", ""],
            ["The earth is flat.", "True/False", "Science", "1", "", "", "", "", "False", ""],
            ["Explain the water cycle.", "2-Mark", "Science", "2", "", "", "", "", "Evaporation, Condensation, Precipitation", "1 mark for evaporation, 1 mark for overall flow"]
        ];

        let csvContent = "data:text/csv;charset=utf-8,"
            + headers.join(",") + "\n"
            + rows.map(e => e.map(val => `"${val}"`).join(",")).join("\n");

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "assessment_template.csv");
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
        
        const countEl = document.getElementById('selectedQuestionsCount');
        countEl.textContent = '0 / 40 required';
        countEl.classList.replace('text-success', 'text-danger');
        
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
        const category = document.getElementById('manualQuestionCategory').value;
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

        // Store for preview
        App.manualQuestions.push({ text, type, category, marks, options });

        const tbody = document.getElementById('manualQuestionTableBody');
        if (tbody.querySelector('td[colspan]')) tbody.innerHTML = '';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="checkbox" class="form-check-input" checked onchange="App.updateManualCount()"></td>
            <td class="small fw-medium">${text.substring(0, 60)}${text.length > 60 ? '...' : ''}</td>
            <td class="small">${category}</td>
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
        
        Swal.fire({ title: 'Success', text: 'Question added to manual list', icon: 'success', timer: 1500, showConfirmButton: false });
    };

    App.handleBulkUploadMock = () => {
        const mockQuestions = [
            { text: 'Explain the difference between let and const in JavaScript.', type: '2-Mark', category: 'Java Basics', marks: 2, options: [] },
            { text: 'Which hook is used for side effects in React?', type: 'MCQ', category: 'React Hooks', marks: 2, options: [{text: 'useState'}, {text: 'useEffect'}, {text: 'useContext'}, {text: 'useReducer'}] }
        ];
        
        App.manualQuestions = App.manualQuestions.concat(mockQuestions);
        
        const tbody = document.getElementById('manualQuestionTableBody');
        if (tbody.querySelector('td[colspan]')) tbody.innerHTML = '';

        mockQuestions.forEach(q => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="checkbox" class="form-check-input" checked onchange="App.updateManualCount()"></td>
                <td class="small fw-medium">${q.text.substring(0, 60)}...</td>
                <td class="small">${q.category}</td>
                <td class="small">${q.type}</td>
                <td class="small">Medium</td>
                <td class="small fw-bold">${q.marks}</td>
            `;
            tbody.appendChild(tr);
        });
        
        App.updateManualCount();
        Swal.fire('Bulk Upload Success', 'Questions imported successfully from CSV.', 'success');
    };

    App.previewQuestionPaper = () => {
        const questions = App.manualQuestions || [];
        if (questions.length === 0) {
            Swal.fire('No Questions', 'Add at least one question to preview the paper.', 'info');
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
                <div class="mb-5 px-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h5 fw-bold text-[#dc2230] mb-0 text-uppercase tracking-wider">SECTION ${secName}</h3>
                        <div class="bg-[#fff1f2] text-[#dc2230] px-3 py-1.5 rounded-[8px] text-[11px] font-bold d-flex align-items-center gap-2">
                             <i class="bi bi-list-ul"></i> ${qs.length} Questions | ${qs[0].marks} Marks each
                        </div>
                    </div>
                    ${qs.map((q, i) => `
                        <div class="mb-5">
                            <div class="d-flex align-items-center gap-2 mb-3 text-[#64748b] text-[11px] font-bold">
                                <i class="bi bi-info-circle-fill"></i> Question Type: ${q.type === 'MCQ' ? 'Multiple Choice' : (q.type === '2-Mark' ? 'Short Answer' : q.type)}
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <div class="fw-bold text-[#1e293b] text-[16px]">Q${i + 1}. ${q.text}</div>
                                <div class="text-[#1e293b] font-bold text-sm">[${q.marks} Marks]</div>
                            </div>
                            <div class="ps-2">
                                ${q.type === '2-Mark' ? `
                                    <div class="border border-2 border-dashed border-[#e2e8f0] rounded-[12px] p-5 text-gray-400 text-[13px] bg-[#fcfcfd]">
                                        Student response area...
                                    </div>
                                ` : `
                                    <div class="row g-4">
                                        ${q.options.map((opt, oi) => `
                                            <div class="col-md-6">
                                                <div class="text-[14px] text-[#334155] d-flex gap-3">
                                                    <span class="fw-bold text-[#1e293b] min-w-[20px]">${String.fromCharCode(65 + oi)})</span>
                                                    <span>${opt.text}</span>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                `}
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        container.innerHTML = `
            <div class="bg-white mx-auto shadow-sm" style="max-width: 900px; min-height: 1000px; padding: 60px 0;">
                <!-- Header -->
                <div class="text-center mb-5 px-5">
                    <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&h=100&fit=crop" class="mx-auto mb-4 rounded shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    <h2 class="fw-bold text-[#1e293b] mb-5 text-3xl">eNova Technology Solutions</h2>
                    
                    <div class="d-flex justify-content-center gap-4 mb-5">
                        <div class="d-flex align-items-center gap-4 p-3 bg-white border border-[#e2e8f0] rounded-[20px] min-w-[200px] shadow-sm">
                            <div class="w-12 h-12 bg-[#fff1f2] text-[#dc2230] rounded-full d-flex align-items-center justify-content-center shadow-inner"><i class="bi bi-clock"></i></div>
                            <div class="text-start">
                                <div class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest mb-1">Duration</div>
                                <div class="fw-bold text-[#1e293b] text-lg">60 Minutes</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-4 p-3 bg-white border border-[#e2e8f0] rounded-[20px] min-w-[200px] shadow-sm">
                            <div class="w-12 h-12 bg-[#fff1f2] text-[#dc2230] rounded-full d-flex align-items-center justify-content-center shadow-inner"><i class="bi bi-star"></i></div>
                            <div class="text-start">
                                <div class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest mb-1">Total Marks</div>
                                <div class="fw-bold text-[#1e293b] text-lg">${totalMarks} Marks</div>
                            </div>
                        </div>
                    </div>
                    <div class="mx-5 opacity-100 my-5"></div>
                </div>

                <!-- Candidate Info -->
                <div class="row g-4 mb-5 px-5">
                    <div class="col-md-6">
                        <div class="p-4 bg-[#fcfcfd] border border-[#e2e8f0] rounded-[20px]">
                            <label class="text-[11px] font-bold text-[#64748b] uppercase tracking-widest mb-3 d-block">Candidate Name</label>
                            <div class="border-bottom border-dotted border-[#cbd5e1] pb-1 text-gray-300 text-[10px] letter-spacing-[2px]">..........................................................................................</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 bg-[#fcfcfd] border border-[#e2e8f0] rounded-[20px]">
                            <label class="text-[11px] font-bold text-[#64748b] uppercase tracking-widest mb-3 d-block">Roll Number / ID</label>
                            <div class="border-bottom border-dotted border-[#cbd5e1] pb-1 text-gray-300 text-[10px] letter-spacing-[2px]">..........................................................................................</div>
                        </div>
                    </div>
                </div>

                <div id="previewQuestionsList">${sectionsHtml}</div>

                <!-- Instructions -->
                <div class="mx-5 p-5 bg-[#fcfcfd] rounded-[20px] mb-5 shadow-sm">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-[#dc2230] text-white rounded-full d-flex align-items-center justify-content-center">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        <h4 class="mb-0 fw-bold text-[#1e293b] text-lg">Important Instructions</h4>
                    </div>
                    <ul class="text-[14px] text-[#475569] mb-0 ps-3">
                        <li class="mb-3">Read all questions carefully before attempting.</li>
                        <li class="mb-3">This paper consists of ${Object.keys(sections).length} distinct sections.</li>
                        <li class="mb-3">All questions are mandatory unless specified otherwise.</li>
                        <li>The total duration for this assessment is 60 minutes.</li>
                    </ul>
                </div>

                <div class="text-center mt-5 pt-5 mx-5 text-[#94a3b8] text-[12px]">
                    <div class="fw-bold text-[#1e293b] mb-1">© 2026 eNova Technology Solutions</div>
                    <div>Generated via eNova Assessment Management Portal</div>
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

    App.updateManualCount = () => {
        const count = document.querySelectorAll('#manualQuestionTableBody input[type="checkbox"]:checked').length;
        const countEl = document.getElementById('selectedQuestionsCount');
        countEl.textContent = `${count} / 40 required`;
        countEl.classList.toggle('text-danger', count < 40);
        countEl.classList.toggle('text-success', count >= 40);
    };

    App.onTemplateSelect = (val) => {
        const select = document.getElementById('baseTemplateSelect');
        const option = select.options[select.selectedIndex];
        const data = JSON.parse(option.getAttribute('data-json'));
        
        document.getElementById('templateSummaryCard').classList.remove('hidden');
        document.getElementById('summ_temp_name').textContent = data.name;
        document.getElementById('rev_template').textContent = data.name;
        
        const qCount = data.sections.reduce((acc, s) => acc + parseInt(s.num_questions), 0);
        document.getElementById('summ_q_count').textContent = qCount;
        document.getElementById('summ_total_marks').textContent = qCount * 2; // Rough estimate
        document.getElementById('summ_duration').textContent = '90 min';
        
        document.getElementById('summ_structure').innerHTML = data.sections.map(s => `
            <span class="badge bg-gray-100 text-gray-700 me-2">${s.num_questions} ${s.type}</span>
        `).join('');
    };

    function publishFinalAssessment() {
        Swal.fire({
            title: 'Publish Assessment?',
            text: "This will make the test live for assigned employees.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Publish Now',
            confirmButtonColor: '#dc2230'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Success!', 'Assessment Pack has been published.', 'success').then(() => {
                    addPackToTable({
                        id: Date.now(),
                        pack_name: 'New Technical Assessment ' + (new Date().getFullYear()),
                        created_at: new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
                        template_name: document.getElementById('summ_temp_name').textContent || 'Custom Template',
                        status: 'Live',
                        user_role: 'Internal Team'
                    });
                    bootstrap.Modal.getInstance(document.getElementById('createPackModal')).hide();
                });
            }
        });
    }

    // --- Legacy Actions Support ---
    window.startExecutionMode = () => switchMainTab('execution');
    window.changeQuestion = (dir) => {
        if (dir > 0) App.nextQuestion();
        else App.prevQuestion();
    };
    window.toggleFlag = (idx) => App.toggleFlag(idx);
    window.confirmSubmitTest = () => App.confirmSubmit();

    async function deleteAssessment(id) {
        if(!(await Swal.fire({ title: 'Delete Assessment?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true }).then(r => r.isConfirmed))) return;
        await fetch(`/assessment/deleteAssessment/${id}`, { method: 'POST' });
        location.reload();
    }

    async function createAssessment() {
        const name = document.getElementById('ass_name').value;
        const code = document.getElementById('ass_code').value;
        const category = document.getElementById('ass_category').value;
        const type = document.getElementById('ass_type').value;
        const assigned = document.getElementById('ass_assigned').value;
        const desc = document.getElementById('ass_desc').value;
        
        await fetch('/assessment/createAssessment', { 
            method: 'POST', 
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}, 
            body: `name=${encodeURIComponent(name)}&code=${encodeURIComponent(code)}&category=${encodeURIComponent(category)}&assessment_type=${encodeURIComponent(type)}&assigned_to=${encodeURIComponent(assigned)}&description=${encodeURIComponent(desc)}` 
        });
        Swal.fire({ title: 'Success!', text: 'Assessment category created', icon: 'success', timer: 1500, showConfirmButton: false }).then(() => location.reload());
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
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `name=${name}&description=${description}&` + tempSections.map((s,i) => `sections[${i}][type]=${s.type}&sections[${i}][count]=${s.count}&sections[${i}][knowledge]=${s.knowledge}`).join('&')
        });
        Swal.fire({ title: 'Template Saved', text: 'Your template has been created successfully', icon: 'success', timer: 1500, showConfirmButton: false }).then(() => location.reload());
    }

    function setAssessmentAndRedirect(id) {
        const sel = document.getElementById('main_assessment_select');
        if(sel) sel.value = id;
        switchMainTab('test-creation');
        openPackWizard();
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
        if (packsDataTable) return; // Already initialized
        
        const initialPacks = <?= json_encode($packs) ?>;
        
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
                        <button class="action-btn text-blue-600 hover:text-blue-800 me-3" onclick="reusePack(${row.id})" title="Reuse Assessment"><i class="bi bi-arrow-repeat"></i></button>
                        <button class="action-btn btn-delete text-red-500 hover:text-red-700" onclick="deletePack(${row.id})"><i class="bi bi-trash"></i></button>
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

    function addPackToTable(pack) {
        if (!packsDataTable) initPacksDataTable();
        packsDataTable.row.add(pack).draw(false);
    }
    document.addEventListener('DOMContentLoaded', () => {
        const resultsTab = document.getElementById('tab-content-results');
        if (resultsTab && !resultsTab.classList.contains('hidden')) {
            if (typeof App !== 'undefined' && App.loadCandidateResult) App.loadCandidateResult(1);
        }
    });
</script>
</body>
</html>
