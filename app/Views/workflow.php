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
    <style>
        .swal2-popup { border-radius: 20px !important; font-family: 'Poppins', sans-serif !important; }
        .swal2-styled.swal2-confirm { background-color: var(--brand) !important; border-radius: 10px !important; padding: 0.6rem 1.5rem !important; font-weight: 600 !important; }
        .swal2-styled.swal2-cancel { border-radius: 10px !important; padding: 0.6rem 1.5rem !important; font-weight: 600 !important; }
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
        .modal-backdrop { position:fixed; inset:0; background:rgba(15,23,42,.45); display:none; align-items:flex-start; justify-content:center; z-index:50; padding:60px 16px; overflow:auto;}
        .modal-backdrop.open { display:flex; }
        .modal { background:#fff; border-radius:16px; width:100%; max-width:720px; padding:28px; box-shadow: 0 25px 60px -20px rgba(15,23,42,.35); }
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
    <button class="btn-outline flex items-center gap-2">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
        Preview User View
    </button>
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
        Create Assessment Pack
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

    <!-- 3. CREATE ASSESSMENT PACK (TEST CREATION) TAB -->
    <main id="tab-content-test-creation" class="hidden pb-20">
        <div class="px-8 mt-10 text-center">
            <h3 class="text-xl font-bold">Create New Assessment</h3>
            <p class="text-gray-500 text-sm mt-1">Configure and launch a new test session.</p>
        </div>

        <!-- Stepper -->
        <div class="stepper px-8" id="creation-stepper">
            <div class="step active" data-step="1">
                <div class="step-circle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                </div>
                <div class="step-label">QP CREATION</div>
            </div>
            <div class="step" data-step="2">
                <div class="step-circle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="step-label">DURATION</div>
            </div>
            <div class="step" data-step="3">
                <div class="step-circle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.51a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <div class="step-label">EXAM SETTING</div>
            </div>
            <div class="step" data-step="4">
                <div class="step-circle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="step-label">SCORING & PASS</div>
            </div>
        </div>

        <!-- Content Card: Step 1 (QP Creation) -->
        <div id="step-1-content" class="card-main">
            <h4 class="text-xl font-bold mb-6">Assessment & Question Paper</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 pt-6">
                <div class="form-group">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Assessment Pack Name</label>
                    <input type="text" id="main_pack_name" class="input border-gray-200 h-12" placeholder="e.g. Q4 Technical Assessment" />
                </div>
                <div class="form-group">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Target Role</label>
                    <select id="main_pack_role" class="select border-gray-200 h-12">
                        <option>Designer</option>
                        <option>Developer</option>
                        <option>HR</option>
                        <option>Digital Marketing</option>
                        <option>Other</option>
                    </select>
                </div>
            </div>

            <div class="mb-8 pt-6 border-t border-gray-50">
                <label class="block text-sm font-semibold text-gray-600 mb-2">Select Parent Assessment</label>
                <select id="main_assessment_select" class="select border-gray-200 h-12">
                    <option value="">-- Choose Assessment --</option>
                    <?php foreach($assessments as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= esc($a['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 pt-6 border-t border-gray-50">
                <div class="form-group">
                    <label>Select Template</label>
                    <select id="main_template_select" class="select border-gray-200 h-12" onchange="updateTemplateDetails(this.value)">
                        <option value="">-- Choose Existing Template --</option>
                        <?php foreach($templates as $t): ?>
                        <option value="<?= $t['id'] ?>" data-json='<?= json_encode($t) ?>'><?= esc($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="btn-outline w-full h-12 flex justify-center gap-2" onclick="openModal('templateModal')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        Create Manual Template
                    </button>
                </div>
            </div>

            <div id="template_details_section" class="details-box hidden">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="font-bold text-gray-700">Creation Summary</h5>
                    <div class="flex gap-2">
                        <span class="chip chip-mcq">MCQ</span>
                        <span class="chip chip-2m">2 Marks</span>
                    </div>
                </div>
                <!-- Logic for manual/bulk upload options -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-white border border-gray-100 rounded-lg">
                        <div class="text-sm font-bold">Manual Creation</div>
                        <div class="text-[11px] text-gray-400 mt-1">Add questions one by one</div>
                    </div>
                    <div class="p-4 bg-white border border-gray-100 rounded-lg cursor-pointer hover:border-blue-400" onclick="Swal.fire('Bulk Upload', 'The bulk upload interface is being prepared.', 'info')">
                        <div class="text-sm font-bold text-blue-600">Bulk Upload</div>
                        <div class="text-[11px] text-gray-400 mt-1">Upload CSV or Excel sheet</div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-12 pt-8 border-t border-gray-100">
                <button class="btn-outline opacity-50 cursor-not-allowed" disabled>Previous</button>
                <div class="flex gap-3">
                    <button class="btn-outline">Save Draft</button>
                    <button class="btn-red-rounded" onclick="goToStep(2)">
                        Next Step
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Card: Step 2 (Duration Setting) -->
        <div id="step-2-content" class="card-main hidden">
            <h4 class="text-xl font-bold mb-6">Duration & Schedule</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <div class="settings-card">
                        <h5 class="font-bold text-sm mb-4 flex items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Timing Details
                        </h5>
                        <div class="form-group mb-4">
                            <label>Duration (Minutes)</label>
                            <input type="number" id="inp_duration" class="input h-11" value="45" oninput="updateLiveSchedule()" />
                        </div>
                        <div class="form-group mb-4">
                            <label>Start Date & Time</label>
                            <input type="datetime-local" id="inp_start" class="input h-11" value="2026-05-05T11:00" onchange="updateLiveSchedule()" />
                        </div>
                        <div class="form-group">
                            <label>End Date & Time</label>
                            <input type="datetime-local" id="inp_end" class="input h-11" value="2026-05-02T17:00" onchange="updateLiveSchedule()" />
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <div class="h-full bg-blue-50/30 border border-blue-100 rounded-2xl p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-blue-500 text-white rounded-lg flex items-center justify-center">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </div>
                            <div>
                                <h5 class="font-bold text-blue-900">Live Schedule Preview</h5>
                                <p class="text-[11px] text-blue-600 font-semibold uppercase tracking-wider">Exam Window Visibility</p>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl p-6 border border-blue-100 shadow-sm">
                            <div class="flex items-center justify-between pb-4 border-b border-gray-50">
                                <div class="text-xs font-bold text-gray-400">STATUS</div>
                                <span class="chip chip-mark bg-blue-100 text-blue-700">Scheduled</span>
                            </div>
                            <div class="grid grid-cols-2 gap-6 mt-6">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Starts On</label>
                                    <div class="font-bold text-gray-800" id="prev_start">May 05, 2026</div>
                                    <div class="text-xs text-gray-500" id="prev_start_time">11:00 AM</div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Ends On</label>
                                    <div class="font-bold text-gray-800" id="prev_end">May 05, 2026</div>
                                    <div class="text-xs text-gray-500" id="prev_end_time">05:00 PM</div>
                                </div>
                            </div>
                            <div class="mt-6 pt-6 border-t border-gray-50 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                                    <span class="text-xs font-bold text-gray-700">Exam Duration: <span id="prev_duration">45</span> Mins</span>
                                </div>
                                <div class="text-[10px] text-gray-400 italic">Auto-calculating window...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-12 pt-8 border-t border-gray-100">
                <button class="btn-outline" onclick="goToStep(1)">Previous</button>
                <div class="flex gap-3">
                    <button class="btn-outline">Save Draft</button>
                    <button class="btn-red-rounded" onclick="goToStep(3)">
                        Next Step
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Card: Step 3 (Exam Setting) -->
        <div id="step-3-content" class="card-main hidden">
            <h4 class="text-xl font-bold mb-6">Exam Settings</h4>
            <div class="max-w-2xl">
                <div class="settings-card">
                    <div class="settings-row">
                        <div>
                            <div class="font-bold text-sm">Shuffle Questions</div>
                            <div class="text-xs text-gray-400">Randomize the order of questions for each user</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="settings-row">
                        <div>
                            <div class="font-bold text-sm">Shuffle Options</div>
                            <div class="text-xs text-gray-400">Randomize MCQ options</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="settings-row">
                        <div>
                            <div class="font-bold text-sm">Show Timer</div>
                            <div class="text-xs text-gray-400">Display a countdown for the user</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="settings-row">
                        <div>
                            <div class="font-bold text-sm">Allow Review</div>
                            <div class="text-xs text-gray-400">Let users go back and change answers</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="settings-row">
                        <div>
                            <div class="font-bold text-sm">Auto Submit on Timeout</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="settings-row">
                        <div>
                            <div class="font-bold text-sm">Show Score Immediately</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-12 pt-8 border-t border-gray-100">
                <button class="btn-outline" onclick="goToStep(2)">Previous</button>
                <div class="flex gap-3">
                    <button class="btn-outline">Save Draft</button>
                    <button class="btn-red-rounded" onclick="goToStep(4)">
                        Next Step
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Card: Step 4 (Scoring) -->
        <div id="step-4-content" class="card-main hidden">
            <h4 class="text-xl font-bold mb-6">Scoring & Pass Criteria</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <div class="settings-card mb-6">
                        <div class="form-group mb-6">
                            <label>Passing Score (%)</label>
                            <input type="number" class="input h-12 text-lg font-bold" value="70" />
                        </div>
                        <div class="form-group">
                            <label>Max Attempts</label>
                            <select class="select h-12">
                                <option>1 attempt</option>
                                <option>2 attempts</option>
                                <option>Unlimited</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
                        <h5 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Summary</h5>
                        <div class="summary-row"><span>Total MCQ</span><span class="font-bold">2 questions</span></div>
                        <div class="summary-row"><span>2-Mark Questions</span><span class="font-bold">1 questions</span></div>
                        <div class="summary-row"><span>Total Marks</span><span class="font-bold">4 marks</span></div>
                        <div class="summary-row summary-total"><span>Passing Marks Required</span><span>3 marks</span></div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-12 pt-8 border-t border-gray-100">
                <button class="btn-outline" onclick="goToStep(3)">Previous</button>
                <div class="flex gap-3">
                    <button class="btn-outline">Save Draft</button>
                    <button class="btn-red-rounded" onclick="publishAssessmentPack()">
                        Publish Assessment
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

    </main>

    <!-- RESULTS TAB -->
    <main id="tab-content-results" class="hidden px-8 py-10">
         <div class="card p-8 text-center">
            <h3 class="text-lg font-bold">Assessment Results</h3>
            <p class="text-gray-500">Track student performance here.</p>
        </div>
    </main>

    <!-- REPORTS TAB -->
    <main id="tab-content-reports" class="hidden px-8 py-10">
         <div class="card p-8 text-center">
            <h3 class="text-lg font-bold">Analytics Reports</h3>
            <p class="text-gray-500">Detailed insights and trends.</p>
        </div>
    </main>

</div>

<!-- MODAL: NEW TEMPLATE -->
<div id="templateModal" class="modal-backdrop" onclick="if(event.target===this)closeModal('templateModal')">
  <div class="modal">
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
<div id="assessmentModal" class="modal-backdrop" onclick="if(event.target===this)closeModal('assessmentModal')">
  <div class="modal max-w-md">
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
<div id="testPackModal" class="modal-backdrop" onclick="if(event.target===this)closeModal('testPackModal')">
  <div class="modal">
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
<div id="assignModal" class="modal-backdrop" onclick="if(event.target===this)closeModal('assignModal')">
  <div class="modal max-w-3xl">
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

<script>
    let tempSections = [];
    function switchMainTab(tabId) {
        // Toggle tabs
        document.querySelectorAll('.module-tab').forEach(t => {
            if (t.getAttribute('onclick').includes(tabId)) t.classList.add('active');
            else t.classList.remove('active');
        });
        
        // Hide all containers
        document.querySelectorAll('#main-content-area > main').forEach(m => m.classList.add('hidden'));
        
        // Show target
        const target = document.getElementById('tab-content-' + tabId);
        if(target) target.classList.remove('hidden');

        // Reset locking if navigating manually
        if(tabId === 'test-creation') {
            const sel = document.getElementById('main_assessment_select');
            // If we didn't just come from setAssessmentAndRedirect (checked via window flag or just reset)
            if(!window.skipSelectReset) {
                sel.disabled = false;
                sel.value = "";
            }
            window.skipSelectReset = false;
        }
    }
    
    function openModal(id) { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    function updateTemplateDetails(tempId) {
        const box = document.getElementById('template_details_section');
        if(!tempId) { box.classList.add('hidden'); return; }
        
        const select = document.getElementById('main_template_select');
        const option = select.options[select.selectedIndex];
        const data = JSON.parse(option.getAttribute('data-json'));
        
        document.getElementById('det_questions').innerText = data.sections.reduce((acc, s) => acc + parseInt(s.num_questions), 0);
        box.classList.remove('hidden');
    }

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
        Swal.fire({
            title: 'Template Saved',
            text: 'Your template has been created successfully',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        }).then(() => location.reload());
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
        Swal.fire({
            title: 'Success!',
            text: 'Assessment category created',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        }).then(() => location.reload());
    }
    function toggleEnovaFields(val) {
        document.getElementById('enova_fields').classList.toggle('hidden', val !== 'Enova Assessment');
    }
    function updateCharCount(el) {
        document.getElementById('char_count').innerText = el.value.length + ' / 500';
    }
    function toggleAccordion(header, contentId) {
        const content = document.getElementById(contentId);
        const chevron = header.querySelector('.chevron');
        content.classList.toggle('open');
        chevron.classList.toggle('rotated');
    }

    function deleteAssessment(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this! This will delete the assessment and all its packs.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                await fetch(`/assessment/deleteAssessment/${id}`, { method: 'POST' });
                location.reload();
            }
        });
    }
    function editAssessment(id) {
        Swal.fire('Coming Soon', 'Edit functionality for Assessment #' + id + ' is currently in development.', 'info');
    }
    function deletePack(id) {
        Swal.fire({
            title: 'Delete this pack?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete pack',
            cancelButtonText: 'Cancel'
        }).then(async (result) => {
            if (result.isConfirmed) {
                await fetch(`/assessment/deletePack/${id}`, { method: 'POST' });
                location.reload();
            }
        });
    }
    function editPack(id) {
        Swal.fire('Coming Soon', 'Edit functionality for Pack #' + id + ' is currently in development.', 'info');
    }
    function setAssessmentAndRedirect(id) {
        const sel = document.getElementById('main_assessment_select');
        sel.value = id;
        sel.disabled = true;
        window.skipSelectReset = true;
        switchMainTab('test-creation');
    }
    async function createTestPack() {
        const ass_id = document.getElementById('tp_ass_id').value;
        const name = document.getElementById('tp_name').value;
        const role = document.getElementById('tp_role').value;
        const temp_id = document.getElementById('tp_template').value;
        await fetch('/assessment/createTestPack', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: `assessment_id=${ass_id}&pack_name=${name}&user_role=${role}&template_id=${temp_id}` });
        Swal.fire({
            title: 'Test Pack Created',
            text: 'Pack saved successfully',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        }).then(() => location.reload());
    }
    async function publishAssessmentPack() {
        const ass_id = document.getElementById('main_assessment_select').value;
        const pack_name = document.getElementById('main_pack_name').value;
        const user_role = document.getElementById('main_pack_role').value;
        const temp_id = document.getElementById('main_template_select').value;

        if(!ass_id || !pack_name || !temp_id) {
            Swal.fire('Error', 'Please complete Step 1 (Assessment, Pack Name, and Template) before publishing.', 'error');
            return;
        }

        await fetch('/assessment/createTestPack', { 
            method: 'POST', 
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}, 
            body: `assessment_id=${ass_id}&pack_name=${encodeURIComponent(pack_name)}&user_role=${encodeURIComponent(user_role)}&template_id=${temp_id}` 
        });

        Swal.fire({
            title: 'Published!',
            text: 'Assessment Pack has been created and published.',
            icon: 'success',
            confirmButtonText: 'View Assessments'
        }).then(() => {
            switchMainTab('assessments');
            location.reload();
        });
    }

    function switchAssignTab(tabId) {
        document.getElementById('assign-mcq').classList.add('hidden');
        document.getElementById('assign-2m').classList.add('hidden');
        document.getElementById(tabId).classList.remove('hidden');
        document.getElementById('btn-assign-mcq').className = (tabId === 'assign-mcq' ? 'tab tab-active' : 'tab tab-idle');
        document.getElementById('btn-assign-2m').className = (tabId === 'assign-2m' ? 'tab tab-active' : 'tab tab-idle');
    }
    function openAssignModal(id, name) {
        document.querySelectorAll('.assign_tp_id_input').forEach(el => el.value = id);
        document.getElementById('assign_subtitle').innerText = 'Template: ' + name;
        openModal('assignModal');
    }
    function goToStep(stepNumber) {
        // Update stepper UI
        document.querySelectorAll('.step').forEach(s => {
            const sn = parseInt(s.getAttribute('data-step'));
            if(sn === stepNumber) s.classList.add('active');
            else s.classList.remove('active');
        });
        
        // Toggle content containers
        for(let i=1; i<=4; i++) {
            const el = document.getElementById('step-' + i + '-content');
            if(el) el.classList.toggle('hidden', i !== stepNumber);
        }
    }

    function updateLiveSchedule() {
        const dur = document.getElementById('inp_duration').value;
        const start = document.getElementById('inp_start').value;
        const end = document.getElementById('inp_end').value;
        
        if(dur) document.getElementById('prev_duration').innerText = dur;
        if(start) {
            const d = new Date(start);
            document.getElementById('prev_start').innerText = d.toLocaleDateString('en-US', {month:'long', day:'numeric', year:'numeric'});
            document.getElementById('prev_start_time').innerText = d.toLocaleTimeString('en-US', {hour:'2-digit', minute:'2-digit'});
        }
        if(end) {
            const d = new Date(end);
            document.getElementById('prev_end').innerText = d.toLocaleDateString('en-US', {month:'long', day:'numeric', year:'numeric'});
            document.getElementById('prev_end_time').innerText = d.toLocaleTimeString('en-US', {hour:'2-digit', minute:'2-digit'});
        }
    }
</script>
</body>
</html>
