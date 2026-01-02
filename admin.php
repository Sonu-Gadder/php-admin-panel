<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TalentBridge | Enterprise Command</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.01em; }
        [x-cloak] { display: none !important; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-900 antialiased" 
      x-data="adminSystem()" 
      x-init="initChart()">

    <!-- Toast Notifications -->
    <div x-show="notification" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:leave="transition ease-in duration-200"
         class="fixed bottom-5 right-5 z-[100] bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3"
         x-cloak>
        <div class="bg-blue-500 p-1 rounded-full"><i class="fas fa-check text-[10px]"></i></div>
        <span x-text="notification" class="text-sm font-bold"></span>
    </div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed left-0 top-0 h-screen w-72 bg-white border-r border-slate-200 p-6 z-50 transition-transform duration-300 lg:translate-x-0">
        <div class="flex items-center gap-3 mb-10 px-2">
            <div class="bg-blue-600 p-2.5 rounded-xl text-white shadow-lg shadow-blue-200">
                <i class="fas fa-bridge text-lg"></i>
            </div>
            <span class="text-xl font-extrabold tracking-tighter uppercase">Talent<span class="text-blue-600">Bridge</span></span>
        </div>

        <nav class="space-y-1">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-3 mb-4">Core Management</p>
            <button @click="activeTab = 'dashboard'" :class="activeTab === 'dashboard' ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-slate-50'" class="w-full flex items-center gap-3 p-3.5 rounded-xl font-bold transition-all">
                <i class="fas fa-chart-pie text-lg"></i> Dashboard Overview
            </button>
            <button @click="activeTab = 'jobs'" :class="activeTab === 'jobs' ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-slate-50'" class="w-full flex items-center gap-3 p-3.5 rounded-xl font-bold transition-all">
                <i class="fas fa-layer-group text-lg"></i> Job Listings
            </button>
            <button @click="activeTab = 'applicants'" :class="activeTab === 'applicants' ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-slate-50'" class="w-full flex items-center gap-3 p-3.5 rounded-xl font-bold transition-all">
                <i class="fas fa-user-astronaut text-lg"></i> Talent Pipeline
            </button>
            
            <div class="pt-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-3 mb-4">Operations</p>
                <button @click="exportData()" class="w-full flex items-center gap-3 p-3.5 text-slate-500 hover:bg-slate-50 rounded-xl font-bold transition-all">
                    <i class="fas fa-file-export"></i> Export Data (CSV)
                </button>
                <a href="#" class="flex items-center gap-3 p-3.5 text-red-500 hover:bg-red-50 rounded-xl font-bold transition-all mt-2">
                    <i class="fas fa-power-off"></i> Sign Out
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-72 min-h-screen">
        <!-- Top Nav -->
        <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500"><i class="fas fa-bars"></i></button>
            <div class="relative group hidden md:block">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" x-model="searchQuery" placeholder="Search across system..." class="bg-slate-100 border-none rounded-full py-2 pl-10 pr-6 w-64 focus:ring-2 focus:ring-blue-500/20 outline-none text-xs font-semibold">
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] font-black text-slate-900 leading-none tracking-tighter">DEMO MODE</p>
                    <p class="text-[9px] text-green-500 font-bold uppercase mt-1">System Functional</p>
                </div>
                <div class="h-10 w-10 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-xs font-black">JD</div>
            </div>
        </header>

        <div class="p-8 max-w-7xl mx-auto">
            
            <!-- Tab: Dashboard -->
            <div x-show="activeTab === 'dashboard'" x-transition>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-200">
                        <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Total Talent</p>
                        <h3 class="text-4xl font-black mt-2" x-text="applicants.length"></h3>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-200">
                        <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Live Roles</p>
                        <h3 class="text-4xl font-black mt-2 text-blue-600" x-text="jobs.length"></h3>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-200">
                        <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Deployments</p>
                        <h3 class="text-4xl font-black mt-2 text-green-500" x-text="applicants.filter(a => a.status === 'deployed').length"></h3>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-200">
                        <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">In Queue</p>
                        <h3 class="text-4xl font-black mt-2 text-amber-500" x-text="applicants.filter(a => a.status === 'pending').length"></h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] border border-slate-200 h-[400px]">
                        <h4 class="font-black text-slate-800 uppercase tracking-tighter italic mb-4">Application Velocity</h4>
                        <canvas id="leadsChart"></canvas>
                    </div>
                    <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white">
                        <h4 class="font-black uppercase tracking-tighter mb-6 italic text-blue-400">Activity Log</h4>
                        <div class="space-y-4">
                            <template x-for="log in logs" :key="log.id">
                                <div class="flex gap-3 border-l-2 border-slate-700 pl-4 py-1">
                                    <p class="text-xs"><span class="font-bold" x-text="log.user"></span> <span class="text-slate-400" x-text="log.action"></span></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Jobs -->
            <div x-show="activeTab === 'jobs'" x-transition class="space-y-8">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200">
                    <h2 class="font-black uppercase tracking-tighter text-slate-800 italic text-xl mb-6">Create New Requistion</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="text" x-model="newJob.title" placeholder="Job Title" class="bg-slate-50 border rounded-2xl p-4 text-sm font-bold outline-none">
                        <input type="text" x-model="newJob.location" placeholder="Location" class="bg-slate-50 border rounded-2xl p-4 text-sm font-bold outline-none">
                        <input type="text" x-model="newJob.salary" placeholder="Salary Range" class="bg-slate-50 border rounded-2xl p-4 text-sm font-bold outline-none">
                        <button @click="addJob()" class="bg-blue-600 text-white rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-blue-700 transition-all">Post Role</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="job in filteredJobs" :key="job.id">
                        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 hover:shadow-xl transition-all group">
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-blue-50 w-12 h-12 rounded-2xl flex items-center justify-center text-blue-600">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <button @click="deleteJob(job.id)" class="text-slate-300 hover:text-red-500 transition-colors"><i class="fas fa-trash-can"></i></button>
                            </div>
                            <h4 class="font-black text-slate-800 text-lg leading-tight mb-1" x-text="job.title"></h4>
                            <p class="text-xs font-bold text-slate-400 italic mb-4" x-text="job.location"></p>
                            <div class="pt-4 border-t flex justify-between items-center">
                                <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase" x-text="job.salary"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Tab: Pipeline -->
            <div x-show="activeTab === 'applicants'" x-transition>
                <div class="bg-white rounded-[2.5rem] border border-slate-200 overflow-hidden shadow-sm">
                    <div class="p-8 border-b bg-slate-50/50 flex justify-between items-center">
                        <h2 class="font-black uppercase tracking-tighter text-slate-800 italic text-2xl">Talent Pipeline</h2>
                        <div class="flex bg-slate-200 p-1 rounded-xl">
                            <button @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'bg-white shadow-sm' : ''" class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all">All</button>
                            <button @click="statusFilter = 'pending'" :class="statusFilter === 'pending' ? 'bg-white shadow-sm' : ''" class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all">Pending</button>
                            <button @click="statusFilter = 'deployed'" :class="statusFilter === 'deployed' ? 'bg-white shadow-sm' : ''" class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all">Deployed</button>
                        </div>
                    </div>
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-widest text-slate-400 border-b">
                                <th class="p-6">Candidate</th>
                                <th class="p-6">Domain</th>
                                <th class="p-6">Status</th>
                                <th class="p-6">Compliance</th>
                                <th class="p-6">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <template x-for="person in filteredApplicants" :key="person.id">
                                <tr class="hover:bg-slate-50/80 transition-all">
                                    <td class="p-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-900 flex items-center justify-center text-[10px] text-white font-black" x-text="person.name.charAt(0)"></div>
                                            <div>
                                                <p class="font-bold text-sm" x-text="person.name"></p>
                                                <p class="text-[9px] text-slate-400" x-text="person.email"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-6 text-xs font-black uppercase italic" x-text="person.job"></td>
                                    <td class="p-6">
                                        <select @change="updateStatus(person.id, $event.target.value)" class="text-[10px] font-black border rounded-lg p-1 outline-none">
                                            <option :selected="person.status === 'pending'" value="pending">PENDING</option>
                                            <option :selected="person.status === 'shortlisted'" value="shortlisted">SHORTLISTED</option>
                                            <option :selected="person.status === 'deployed'" value="deployed">DEPLOYED</option>
                                        </select>
                                    </td>
                                    <td class="p-6">
                                        <div class="flex gap-1">
                                            <span :class="person.pcc ? 'bg-green-100 text-green-600' : 'bg-red-50 text-red-300'" class="px-2 py-0.5 rounded text-[8px] font-black">PCC</span>
                                            <span :class="person.med ? 'bg-green-100 text-green-600' : 'bg-red-50 text-red-300'" class="px-2 py-0.5 rounded text-[8px] font-black">MED</span>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <button @click="deleteApplicant(person.id)" class="text-slate-300 hover:text-red-500"><i class="fas fa-trash-can"></i></button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        function adminSystem() {
            return {
                sidebarOpen: true,
                activeTab: 'dashboard',
                notification: null,
                searchQuery: '',
                statusFilter: 'all',
                newJob: { title: '', location: '', salary: '' },
                
                // Dummy Data
                jobs: [
                    { id: 1, title: 'Lead Full Stack Developer', location: 'Dubai, UAE', salary: '$8k - $12k' },
                    { id: 2, title: 'Offshore Safety Officer', location: 'Norway', salary: '$6k - $9k' },
                    { id: 3, title: 'Automation Engineer', location: 'Singapore', salary: '$5k - $7k' }
                ],
                applicants: [
                    { id: 1, name: 'John Doe', email: 'john@example.com', job: 'Full Stack Developer', status: 'pending', pcc: true, med: true },
                    { id: 2, name: 'Sarah Connor', email: 'sarah@skynet.com', job: 'Safety Officer', status: 'deployed', pcc: true, med: true },
                    { id: 3, name: 'Mike Ross', email: 'mike@pearson.com', job: 'Automation Engineer', status: 'pending', pcc: false, med: true }
                ],
                logs: [
                    { id: 1, user: 'Admin', action: 'updated system security' },
                    { id: 2, user: 'System', action: 'new application received' }
                ],

                // Computed Filters
                get filteredJobs() {
                    return this.jobs.filter(j => j.title.toLowerCase().includes(this.searchQuery.toLowerCase()));
                },
                get filteredApplicants() {
                    return this.applicants.filter(a => {
                        const matchesSearch = a.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchesStatus = this.statusFilter === 'all' || a.status === this.statusFilter;
                        return matchesSearch && matchesStatus;
                    });
                },

                // Actions
                addJob() {
                    if(!this.newJob.title) return;
                    this.jobs.unshift({ ...this.newJob, id: Date.now() });
                    this.newJob = { title: '', location: '', salary: '' };
                    this.showToast('New Job Requisition Published');
                },
                deleteJob(id) {
                    this.jobs = this.jobs.filter(j => j.id !== id);
                    this.showToast('Job requirement removed');
                },
                deleteApplicant(id) {
                    this.applicants = this.applicants.filter(a => a.id !== id);
                    this.showToast('Candidate record wiped');
                },
                updateStatus(id, newStatus) {
                    const person = this.applicants.find(a => a.id === id);
                    if(person) person.status = newStatus;
                    this.showToast(`Candidate moved to ${newStatus}`);
                },
                showToast(msg) {
                    this.notification = msg;
                    setTimeout(() => this.notification = null, 3000);
                },
                exportData() {
                    let csv = 'Name,Email,Job,Status\n';
                    this.applicants.forEach(a => {
                        csv += `${a.name},${a.email},${a.job},${a.status}\n`;
                    });
                    const blob = new Blob([csv], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'talent_bridge_export.csv';
                    a.click();
                    this.showToast('Pipeline data exported');
                },
                initChart() {
                    setTimeout(() => {
                        const ctx = document.getElementById('leadsChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                                datasets: [{
                                    data: [5, 12, 8, 15, 25, 20, 32],
                                    borderColor: '#2563eb',
                                    tension: 0.4,
                                    borderWidth: 4,
                                    fill: true,
                                    backgroundColor: 'rgba(37, 99, 235, 0.05)',
                                    pointRadius: 0
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: { x: { grid: { display: false } }, y: { display: false } }
                            }
                        });
                    }, 100);
                }
            }
        }
    </script>
</body>
</html>