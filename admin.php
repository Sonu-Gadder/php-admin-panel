<?php 
require 'db.php'; 

// --- 1. DATA ANALYTICS QUERIES ---

// A. Summary Stats
$totalApplicants = $pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
$totalJobs = $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
$deployedCount = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'deployed'")->fetchColumn();
$pccCleared = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE compliance_pcc = 1")->fetchColumn();

// B. Chart Data: Applicants by Status
$statusData = $pdo->query("SELECT status, COUNT(*) as count FROM inquiries GROUP BY status")->fetchAll();

// C. Chart Data: Top Domains
$domainData = $pdo->query("SELECT domain, COUNT(*) as count FROM inquiries GROUP BY domain LIMIT 5")->fetchAll();

// D. Fetch All Tables for the UI
$jobsData = $pdo->query("SELECT * FROM jobs ORDER BY id DESC")->fetchAll();
$applicantsData = $pdo->query("SELECT * FROM inquiries ORDER BY id DESC")->fetchAll();

// E. Recent Activity (Last 5 inquiries)
$recentActivity = $pdo->query("SELECT fullname, created_at, domain FROM inquiries ORDER BY id DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TalentBridge | Enterprise Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.01em; }
        [x-cloak] { display: none !important; }
        .chart-container { position: relative; height: 250px; width: 100%; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-900" x-data="adminSystem()">

    <!-- Sidebar (Left) -->
    <aside class="fixed left-0 top-0 h-screen w-72 bg-slate-900 border-r border-slate-800 p-6 z-50 hidden lg:block text-white">
        <div class="flex items-center gap-3 mb-10 px-2">
            <div class="bg-blue-600 p-2.5 rounded-xl text-white shadow-lg shadow-blue-500/30">
                <i class="fas fa-bridge text-lg"></i>
            </div>
            <span class="text-xl font-extrabold tracking-tighter uppercase">Talent<span class="text-blue-500">Bridge</span></span>
        </div>
        
        <nav class="space-y-2">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-4 mb-4">Core Management</p>
            <button @click="activeTab = 'dashboard'" :class="activeTab === 'dashboard' ? 'bg-blue-600 shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800'" class="w-full flex items-center gap-3 p-4 rounded-2xl font-bold transition-all">
                <i class="fas fa-chart-line"></i> Analytics Overview
            </button>
            <button @click="activeTab = 'applicants'" :class="activeTab === 'applicants' ? 'bg-blue-600 shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800'" class="w-full flex items-center gap-3 p-4 rounded-2xl font-bold transition-all">
                <i class="fas fa-users-viewfinder"></i> Talent Pipeline
            </button>
            <button @click="activeTab = 'jobs'" :class="activeTab === 'jobs' ? 'bg-blue-600 shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800'" class="w-full flex items-center gap-3 p-4 rounded-2xl font-bold transition-all">
                <i class="fas fa-briefcase"></i> Post New Roles
            </button>
            
            <div class="pt-10">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-4 mb-4">External</p>
                <a href="index.php" class="flex items-center gap-3 p-4 text-slate-400 hover:bg-slate-800 rounded-2xl font-bold transition-all">
                    <i class="fas fa-external-link-alt"></i> Public Portal
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-72 min-h-screen">
        <!-- Top Nav -->
        <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">System Online</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-xs font-black text-slate-900 leading-none">Administrator</p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Super User</p>
                </div>
                <div class="h-10 w-10 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-xs font-black ring-4 ring-blue-50">AD</div>
            </div>
        </header>

        <div class="p-8 max-w-7xl mx-auto">
            
            <!-- TAB 1: DASHBOARD ANALYTICS -->
            <div x-show="activeTab === 'dashboard'" x-transition x-init="initCharts()">
                <!-- Top Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl"><i class="fas fa-users"></i></div>
                        </div>
                        <p class="text-slate-400 text-[10px] font-black uppercase">Total Pipeline</p>
                        <h3 class="text-3xl font-black mt-1"><?= $totalApplicants ?></h3>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl"><i class="fas fa-rocket"></i></div>
                        </div>
                        <p class="text-slate-400 text-[10px] font-black uppercase">Deployed</p>
                        <h3 class="text-3xl font-black mt-1 text-indigo-600"><?= $deployedCount ?></h3>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-green-50 text-green-600 rounded-xl"><i class="fas fa-shield-check"></i></div>
                        </div>
                        <p class="text-slate-400 text-[10px] font-black uppercase">PCC Compliance</p>
                        <h3 class="text-3xl font-black mt-1 text-green-500"><?= round(($pccCleared / ($totalApplicants ?: 1)) * 100) ?>%</h3>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-orange-50 text-orange-600 rounded-xl"><i class="fas fa-briefcase"></i></div>
                        </div>
                        <p class="text-slate-400 text-[10px] font-black uppercase">Active Vacancies</p>
                        <h3 class="text-3xl font-black mt-1 text-orange-500"><?= $totalJobs ?></h3>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm">
                        <h4 class="font-black uppercase tracking-tighter text-slate-800 italic mb-6">Talent Distribution (By Domain)</h4>
                        <div class="chart-container">
                            <canvas id="domainChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm">
                        <h4 class="font-black uppercase tracking-tighter text-slate-800 italic mb-6">Application Status Overview</h4>
                        <div class="chart-container">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity & System Info -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                            <h4 class="font-black uppercase tracking-tighter text-slate-800 italic">Recent Pipeline Activity</h4>
                            <button @click="activeTab = 'applicants'" class="text-blue-600 text-[10px] font-black uppercase">View All</button>
                        </div>
                        <div class="p-4">
                            <table class="w-full text-left">
                                <tbody class="divide-y divide-slate-50">
                                    <?php foreach($recentActivity as $act): ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="p-4 flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black"><?= $act['fullname'][0] ?></div>
                                            <span class="text-sm font-bold"><?= $act['fullname'] ?></span>
                                        </td>
                                        <td class="p-4 text-xs font-bold text-slate-400 uppercase"><?= $act['domain'] ?></td>
                                        <td class="p-4 text-right text-[10px] font-black text-slate-300 uppercase"><?= date('M d, H:i', strtotime($act['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
                        <h4 class="font-black uppercase tracking-tighter italic text-blue-400 mb-6">Security & Logs</h4>
                        <div class="space-y-6 relative z-10">
                            <div class="flex gap-4">
                                <div class="text-blue-500 mt-1"><i class="fas fa-circle-check"></i></div>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-widest">Database Sync</p>
                                    <p class="text-[10px] text-slate-500 mt-1">Last successful sync: 2 mins ago</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="text-blue-500 mt-1"><i class="fas fa-fingerprint"></i></div>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-widest">Encryption</p>
                                    <p class="text-[10px] text-slate-500 mt-1">AES-256 Protocol Active</p>
                                </div>
                            </div>
                            <button @click="exportData()" class="w-full mt-4 bg-white/10 hover:bg-white/20 p-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all">
                                Generate System Audit (CSV)
                            </button>
                        </div>
                        <i class="fas fa-shield-halved absolute -bottom-10 -right-10 text-[10rem] text-white/5 rotate-12"></i>
                    </div>
                </div>
            </div>

            <!-- TAB 2: APPLICANTS PIPELINE (DETAILED) -->
            <div x-show="activeTab === 'applicants'" x-transition>
                <div class="bg-white rounded-[2.5rem] border border-slate-200 overflow-hidden shadow-sm">
                    <div class="p-8 border-b bg-slate-50/50 flex justify-between items-center">
                        <h2 class="font-black uppercase tracking-tighter text-slate-800 italic text-2xl">Talent Pipeline</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] uppercase tracking-widest text-slate-400 border-b">
                                    <th class="p-6">Candidate</th>
                                    <th class="p-6">Job Title</th>
                                    <th class="p-6">Passport / Domain</th>
                                    <th class="p-6">Status</th>
                                    <th class="p-6">Compliance</th>
                                    <th class="p-6 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="person in applicants" :key="person.id">
                                    <tr class="hover:bg-slate-50/80 transition-all">
                                        <td class="p-6">
                                            <p class="font-bold text-sm text-slate-800" x-text="person.fullname"></p>
                                            <p class="text-[10px] text-slate-400 font-bold" x-text="person.email"></p>
                                        </td>
                                        <td class="p-6 text-xs font-black uppercase text-blue-600 italic" x-text="person.job_title"></td>
                                        <td class="p-6">
                                            <p class="text-[10px] font-black text-slate-800" x-text="person.passport_no"></p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase" x-text="person.domain"></p>
                                        </td>
                                        <td class="p-6">
                                            <select @change="updateStatus(person.id, $event.target.value)" 
                                                    :class="person.status === 'deployed' ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-600'"
                                                    class="text-[10px] font-black border-none rounded-lg p-2 outline-none">
                                                <option :selected="person.status === 'pending'" value="pending">PENDING</option>
                                                <option :selected="person.status === 'shortlisted'" value="shortlisted">SHORTLISTED</option>
                                                <option :selected="person.status === 'deployed'" value="deployed">DEPLOYED</option>
                                            </select>
                                        </td>
                                        <td class="p-6">
                                            <div class="flex gap-2">
                                                <span :class="person.compliance_pcc == 1 ? 'text-green-500 bg-green-50' : 'text-red-300 bg-red-50'" class="px-2 py-1 rounded text-[8px] font-black uppercase">PCC</span>
                                                <span :class="person.compliance_medical == 1 ? 'text-green-500 bg-green-50' : 'text-red-300 bg-red-50'" class="px-2 py-1 rounded text-[8px] font-black uppercase">MED</span>
                                            </div>
                                        </td>
                                        <td class="p-6 text-right">
                                            <button @click="deleteApplicant(person.id)" class="text-slate-200 hover:text-red-500 transition-colors">
                                                <i class="fas fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 3: JOBS MANAGEMENT -->
            <div x-show="activeTab === 'jobs'" x-transition>
                <div class="bg-white p-10 rounded-[2.5rem] border border-slate-200 mb-10">
                    <h2 class="font-black uppercase tracking-tighter text-slate-800 italic text-2xl mb-8">Post New Vacancy</h2>
                    <form action="backend_logic.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <input type="hidden" name="action" value="add_job">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Title</label>
                            <input type="text" name="title" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold outline-none focus:ring-2 focus:ring-blue-500/20">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Location</label>
                            <input type="text" name="location" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold outline-none focus:ring-2 focus:ring-blue-500/20">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Salary Range</label>
                            <input type="text" name="salary" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold outline-none focus:ring-2 focus:ring-blue-500/20">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Tag (tech/ops)</label>
                            <input type="text" name="tags" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold outline-none focus:ring-2 focus:ring-blue-500/20">
                        </div>
                        <button type="submit" class="lg:col-span-4 bg-blue-600 text-white p-5 rounded-2xl font-black uppercase text-xs tracking-[0.2em] shadow-xl shadow-blue-500/20 hover:scale-[1.01] transition-all">Publish Requirement</button>
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <template x-for="job in jobs" :key="job.id">
                        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 flex justify-between items-center group hover:border-blue-200 transition-all">
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-all">
                                    <i class="fas fa-briefcase text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-slate-800 text-lg uppercase italic tracking-tighter" x-text="job.title"></h4>
                                    <p class="text-xs font-bold text-slate-400 uppercase" x-text="job.location"></p>
                                </div>
                            </div>
                            <button @click="deleteJob(job.id)" class="text-slate-200 hover:text-red-500 p-2"><i class="fas fa-trash-can"></i></button>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </main>

    <!-- TOAST NOTIFICATION -->
    <div x-show="notification" x-transition class="fixed bottom-10 right-10 z-[100] bg-slate-900 text-white px-8 py-5 rounded-[2rem] shadow-2xl flex items-center gap-4 border border-slate-700" x-cloak>
        <div class="bg-blue-600 p-2 rounded-full"><i class="fas fa-check text-[10px]"></i></div>
        <span x-text="notification" class="text-xs font-black uppercase tracking-widest"></span>
    </div>

    <script>
        function adminSystem() {
            return {
                activeTab: 'dashboard',
                notification: null,
                jobs: <?php echo json_encode($jobsData); ?>,
                applicants: <?php echo json_encode($applicantsData); ?>,

                showToast(msg) {
                    this.notification = msg;
                    setTimeout(() => this.notification = null, 3000);
                },

                // CRUD Actions
                async updateStatus(id, status) {
                    let fd = new FormData();
                    fd.append('action', 'update_status');
                    fd.append('id', id);
                    fd.append('status', status);
                    let res = await fetch('backend_logic.php', { method: 'POST', body: fd });
                    this.showToast('Candidate Status Updated');
                },

                async deleteApplicant(id) {
                    if(!confirm('Archive this candidate?')) return;
                    let fd = new FormData();
                    fd.append('action', 'delete_applicant');
                    fd.append('id', id);
                    await fetch('backend_logic.php', { method: 'POST', body: fd });
                    this.applicants = this.applicants.filter(a => a.id != id);
                    this.showToast('Candidate Record Removed');
                },

                async deleteJob(id) {
                    if(!confirm('Close this vacancy?')) return;
                    let fd = new FormData();
                    fd.append('action', 'delete_job');
                    fd.append('id', id);
                    await fetch('backend_logic.php', { method: 'POST', body: fd });
                    this.jobs = this.jobs.filter(j => j.id != id);
                    this.showToast('Job Listing Closed');
                },

                exportData() {
                    let csv = 'Name,Email,Domain,Status\n';
                    this.applicants.forEach(a => {
                        csv += `${a.fullname},${a.email},${a.domain},${a.status}\n`;
                    });
                    const blob = new Blob([csv], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'talentbridge_audit_log.csv';
                    a.click();
                },

                // Initialize Charts
                initCharts() {
                    this.$nextTick(() => {
                        // 1. Domain Bar Chart
                        const ctxDomain = document.getElementById('domainChart').getContext('2d');
                        new Chart(ctxDomain, {
                            type: 'bar',
                            data: {
                                labels: <?php echo json_encode(array_column($domainData, 'domain')); ?>,
                                datasets: [{
                                    label: 'Applicants',
                                    data: <?php echo json_encode(array_column($domainData, 'count')); ?>,
                                    backgroundColor: '#2563eb',
                                    borderRadius: 10
                                }]
                            },
                            options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
                        });

                        // 2. Status Pie Chart
                        const ctxStatus = document.getElementById('statusChart').getContext('2d');
                        new Chart(ctxStatus, {
                            type: 'doughnut',
                            data: {
                                labels: <?php echo json_encode(array_column($statusData, 'status')); ?>,
                                datasets: [{
                                    data: <?php echo json_encode(array_column($statusData, 'count')); ?>,
                                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981'],
                                    borderWidth: 0
                                }]
                            },
                            options: { maintainAspectRatio: false, cutout: '70%' }
                        });
                    });
                }
            }
        }
    </script>
</body>
</html>