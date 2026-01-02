<?php 
require 'db.php'; 
// Fetch jobs once for the Alpine.js engine
$jobs_query = $pdo->query("SELECT * FROM jobs ORDER BY created_at DESC");
$jobs_data = $jobs_query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TalentBridge Pro | India-China Secure Recruitment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: { primary: '#2563eb', dark: '#0f172a' }
                    }
                }
            }
        }
        // Remove status from URL
        if (window.location.search.includes('status=')) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-heading { font-family: 'Space Grotesk', sans-serif; }
        [x-cloak] { display: none !important; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .dark .glass-card { background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.05); }
        .gradient-text { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .bento-inner { background: white; border: 1px solid #f1f5f9; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .dark .bento-inner { background: #1e293b; border-color: #334155; }
        .bento-inner:hover { transform: translateY(-5px); border-color: #3b82f6; box-shadow: 0 25px 50px -12px rgba(59, 130, 246, 0.1); }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
        .animate-float { animation: float 6s ease-in-out infinite; }
    </style>
</head>

<body class="bg-[#f8fafc] dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased transition-colors duration-300" 
      x-data="{ 
        openModal: false, 
        openDetailModal: false, 
        openTracker: false,
        activeTab: 'all', 
        searchQuery: '',
        currency: 'CNY',
        conversionRate: 11.8,
        scrolled: false,
        selectedJob: {},
        faqOpen: null,
        jobs: <?php echo htmlspecialchars(json_encode($jobs_data), ENT_QUOTES, 'UTF-8'); ?>,
        
        get filteredJobs() {
            return this.jobs.filter(j => {
                const matchSearch = j.title.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                    j.location.toLowerCase().includes(this.searchQuery.toLowerCase());
                if (this.activeTab === 'all') return matchSearch;
                const isTech = j.tags.toLowerCase().includes('tech') || j.tags.toLowerCase().includes('engineer');
                return matchSearch && (this.activeTab === 'tech' ? isTech : !isTech);
            })
        },
        formatSalary(val) {
            let numeric = parseInt(val.toString().replace(/[^0-9]/g, ''));
            if(this.currency === 'INR') {
                return '₹' + Math.floor(numeric * this.conversionRate / 12).toLocaleString() + '/mo';
            }
            return '¥' + numeric.toLocaleString() + '/yr';
        }
      }">

    <!-- Success/Error Notifications -->
    <?php if(isset($_GET['status'])): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
         class="fixed top-24 left-1/2 -translate-x-1/2 z-[200] w-full max-w-md px-6" x-cloak x-transition>
        <div class="<?= $_GET['status'] == 'success' ? 'bg-slate-900' : 'bg-red-600' ?> text-white p-5 rounded-[2rem] shadow-2xl flex justify-between items-center border border-white/10">
            <div class="flex items-center gap-3">
                <i class="fas <?= $_GET['status'] == 'success' ? 'fa-circle-check text-green-400' : 'fa-triangle-exclamation text-white' ?> text-xl"></i>
                <span class="font-bold text-sm tracking-tight">
                    <?= $_GET['status'] == 'success' ? 'Application processed successfully!' : 'System error. Please try again.' ?>
                </span>
            </div>
            <button @click="show = false" class="opacity-50 hover:opacity-100"><i class="fas fa-times"></i></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigation -->
    <nav @scroll.window="scrolled = (window.pageYOffset > 20) ? true : false"
         :class="scrolled ? 'bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg py-3 shadow-lg' : 'bg-transparent py-6'"
         class="fixed w-full z-[100] transition-all duration-500 px-6 lg:px-12">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3 group cursor-pointer">
                <div class="bg-blue-600 p-2.5 rounded-xl shadow-lg group-hover:rotate-12 transition-transform">
                    <i class="fas fa-bridge-water text-white text-xl"></i>
                </div>
                <span class="text-2xl font-black tracking-tighter font-heading uppercase">
                    TALENT<span class="text-blue-600">BRIDGE</span>
                </span>
            </a>
            
            <div class="hidden lg:flex items-center space-x-8">
                <a href="#how" class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:text-blue-600">Process</a>
                <button @click="openTracker = true" class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:text-blue-600">Track Status</button>
                
                <div class="flex items-center gap-4 bg-slate-100 dark:bg-slate-800 p-1.5 rounded-full">
                    <button @click="darkMode = !darkMode" class="w-8 h-8 rounded-full flex items-center justify-center transition-all" :class="darkMode ? 'bg-slate-700 text-yellow-400' : 'bg-white text-slate-400 shadow-sm'">
                        <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
                    </button>
                    <button @click="currency = (currency === 'CNY' ? 'INR' : 'CNY')" class="px-3 py-1 text-[9px] font-black uppercase tracking-tighter" :class="currency === 'INR' ? 'text-blue-600' : 'text-slate-400'">
                        <span x-text="currency"></span>
                    </button>
                </div>

                <button @click="openModal = true" class="bg-slate-900 dark:bg-blue-600 text-white px-8 py-3.5 rounded-full font-bold text-[10px] uppercase tracking-widest hover:scale-105 transition-all">
                    Apply to Portal
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-48 pb-20 overflow-hidden">
        <div class="absolute top-0 right-0 -z-10 w-full h-full opacity-10 dark:opacity-20" style="background-image: radial-gradient(#2563eb 0.5px, transparent 0.5px); background-size: 24px 24px;"></div>
        <div class="max-w-7xl mx-auto px-8 grid lg:grid-cols-2 gap-20 items-center">
            <div>
                <div class="inline-flex items-center gap-2 bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 px-4 py-2 rounded-full mb-8">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-blue-700 dark:text-blue-400">Official India-China Human Capital Bridge</span>
                </div>
                <h1 class="text-6xl lg:text-8xl font-black leading-[0.85] tracking-tighter mb-8 font-heading uppercase italic">
                    Powering <span class="gradient-text">Global</span><br>Industry.
                </h1>
                <p class="text-xl text-slate-500 dark:text-slate-400 mb-10 max-w-md leading-relaxed font-medium">Verified cross-border recruitment infrastructure. Seamlessly deploying Indian talent to Chinese industrial hubs with full legal compliance.</p>
                <div class="flex flex-wrap gap-4">
                    <button @click="openModal = true" class="bg-blue-600 text-white px-10 py-5 rounded-2xl font-bold text-sm uppercase tracking-widest hover:shadow-2xl hover:shadow-blue-500/40 transition-all">
                        Initialize Deployment
                    </button>
                    <div class="flex items-center gap-4 bg-white dark:bg-slate-800 px-6 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <div class="flex -space-x-2">
                            <img class="w-8 h-8 rounded-full border-2 border-white dark:border-slate-800" src="https://i.pravatar.cc/100?u=1">
                            <img class="w-8 h-8 rounded-full border-2 border-white dark:border-slate-800" src="https://i.pravatar.cc/100?u=2">
                            <img class="w-8 h-8 rounded-full border-2 border-white dark:border-slate-800" src="https://i.pravatar.cc/100?u=3">
                        </div>
                        <span class="text-[10px] font-black uppercase text-slate-400">12k+ Verified Workers</span>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="absolute inset-0 bg-blue-600/10 blur-[100px] rounded-full"></div>
                <div class="relative glass-card p-4 rounded-[4rem] shadow-3xl rotate-2 hover:rotate-0 transition-transform duration-700">
                    <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&q=80&w=800" class="rounded-[3.5rem] grayscale hover:grayscale-0 transition duration-1000">
                    <div class="absolute -bottom-10 -right-10 glass-card p-8 rounded-[2.5rem] shadow-2xl animate-float">
                        <p class="text-4xl font-black text-blue-600">450+</p>
                        <p class="text-[9px] font-black uppercase text-slate-400">Chinese Partner Entities</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Values / Bento Grid Section -->
    <section class="py-32 max-w-7xl mx-auto px-8">
        <div class="text-center mb-20">
            <h2 class="text-4xl font-black italic uppercase tracking-tighter font-heading">Core Infrastructure</h2>
            <p class="text-slate-400 mt-2 font-bold uppercase tracking-widest text-xs">Why global enterprises trust TalentBridge</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2 bento-inner p-12 rounded-[3.5rem] flex flex-col justify-between min-h-[400px]">
                <div class="bg-blue-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white mb-8">
                    <i class="fas fa-fingerprint text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-4xl font-black italic tracking-tighter uppercase mb-4">Biometric Verification</h3>
                    <p class="text-slate-500 dark:text-slate-400 font-medium text-lg leading-relaxed">Multi-layer skill and identity verification using Indian Aadhar and biometric assessments to ensure 0% identity fraud.</p>
                </div>
            </div>
            <div class="bento-inner p-12 rounded-[3.5rem] bg-slate-900 text-white">
                <div class="bg-white/10 w-16 h-16 rounded-2xl flex items-center justify-center text-blue-400 mb-8">
                    <i class="fas fa-file-contract text-2xl"></i>
                </div>
                <h3 class="text-3xl font-black italic tracking-tighter uppercase mb-4 leading-tight">Z-Visa Optimization</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Automated document mapping for Chinese Provincial Labour Bureaus, reducing visa processing time by 40%.</p>
            </div>
            <div class="bento-inner p-12 rounded-[3.5rem]">
                <div class="bg-slate-100 dark:bg-slate-700 w-16 h-16 rounded-2xl flex items-center justify-center text-slate-900 dark:text-white mb-8">
                    <i class="fas fa-handshake-angle text-2xl"></i>
                </div>
                <h3 class="text-2xl font-black italic tracking-tighter uppercase mb-4">Ethical Sourcing</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">Adhering to ILO standards, ensuring zero recruitment fees for workers and fair living conditions in China.</p>
            </div>
            <div class="md:col-span-2 bento-inner p-12 rounded-[3.5rem] bg-blue-600 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-4xl font-black italic tracking-tighter uppercase mb-4">Real-time Deployment Queue</h3>
                    <p class="text-blue-100 text-lg">Track your application from Indian PCC clearance to Shanghai Residence Permit landing.</p>
                </div>
                <i class="fas fa-plane absolute -bottom-10 -right-10 text-[15rem] opacity-10 -rotate-12"></i>
            </div>
        </div>
    </section>

    <!-- Job Feed (Advanced) -->
    <section id="jobs" class="py-32 bg-white dark:bg-slate-900 transition-colors">
        <div class="max-w-7xl mx-auto px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
                <div>
                    <h2 class="text-5xl font-black italic tracking-tighter uppercase font-heading">Verified Openings</h2>
                    <p class="text-slate-400 font-bold text-[10px] uppercase tracking-[0.3em] mt-2">Active industrial requirements in China</p>
                </div>
                <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                    <div class="relative">
                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input x-model="searchQuery" type="text" placeholder="Search positions..." 
                               class="w-full md:w-64 pl-12 pr-6 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 outline-none focus:ring-2 ring-blue-500 transition-all">
                    </div>
                    <div class="flex gap-2 bg-slate-50 dark:bg-slate-800 p-2 rounded-2xl border border-slate-100 dark:border-slate-700">
                        <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-white dark:bg-slate-700 shadow-sm text-blue-600' : 'text-slate-500'" class="px-8 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">All</button>
                        <button @click="activeTab = 'tech'" :class="activeTab === 'tech' ? 'bg-white dark:bg-slate-700 shadow-sm text-blue-600' : 'text-slate-500'" class="px-8 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Technical</button>
                    </div>
                </div>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
                <template x-for="job in filteredJobs" :key="job.id">
                    <div class="group bg-slate-50/50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 p-8 rounded-[3rem] transition-all duration-500 hover:bg-white dark:hover:bg-slate-800 hover:shadow-2xl hover:-translate-y-2">
                        <div class="flex justify-between items-start mb-8">
                            <div class="w-14 h-14 bg-white dark:bg-slate-700 rounded-2xl shadow-sm group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center transition-colors">
                                <i class="fas fa-briefcase text-xl"></i>
                            </div>
                            <span class="text-[9px] font-black text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-3 py-1 rounded-full uppercase tracking-widest border border-blue-100 dark:border-blue-800">Apply Open</span>
                        </div>

                        <h3 class="text-2xl font-black mb-2 leading-tight tracking-tighter italic uppercase font-heading" x-text="job.title"></h3>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-10"><i class="fas fa-location-dot text-blue-500 mr-2"></i> <span x-text="job.location"></span></p>

                        <div class="mt-auto flex items-center justify-between">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Proposed Package</p>
                                <p class="text-xl font-black text-slate-900 dark:text-white tracking-tighter" x-text="formatSalary(job.salary)"></p>
                            </div>
                            <button @click="selectedJob = job; openDetailModal = true" 
                                    class="w-12 h-12 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-slate-900 group-hover:text-white transition-all">
                                <i class="fas fa-arrow-right text-sm"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- Governance/Timeline -->
    <section id="how" class="py-32 bg-slate-900 text-white rounded-[4rem] mx-4 overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-8 relative z-10 text-center">
            <h2 class="text-5xl font-black italic tracking-tighter uppercase font-heading">Deployment Workflow</h2>
            <div class="grid md:grid-cols-4 gap-8 mt-20">
                <div class="p-8 bg-white/5 rounded-[2.5rem] border border-white/10 text-left group hover:bg-white/10 transition-all">
                    <div class="text-5xl font-black opacity-10 italic mb-4">01</div>
                    <h3 class="font-black text-xl mb-4 text-blue-400 uppercase italic">Sourcing</h3>
                    <p class="text-xs text-slate-400 font-medium">Worker identity and skills are verified via AI-driven tools in India.</p>
                </div>
                <div class="p-8 bg-white/5 rounded-[2.5rem] border border-white/10 text-left group hover:bg-white/10 transition-all">
                    <div class="text-5xl font-black opacity-10 italic mb-4">02</div>
                    <h3 class="font-black text-xl mb-4 text-blue-400 uppercase italic">Screening</h3>
                    <p class="text-xs text-slate-400 font-medium">Medical checks and PCC tracked in real-time for compliance.</p>
                </div>
                <div class="p-8 bg-white/5 rounded-[2.5rem] border border-white/10 text-left group hover:bg-white/10 transition-all">
                    <div class="text-5xl font-black opacity-10 italic mb-4">03</div>
                    <h3 class="font-black text-xl mb-4 text-blue-400 uppercase italic">Visa Phase</h3>
                    <p class="text-xs text-slate-400 font-medium">Automated document submission to Chinese provincial Labour Bureaus.</p>
                </div>
                <div class="p-8 bg-white/5 rounded-[2.5rem] border border-white/10 text-left group hover:bg-white/10 transition-all">
                    <div class="text-5xl font-black opacity-10 italic mb-4">04</div>
                    <h3 class="font-black text-xl mb-4 text-blue-400 uppercase italic">Post-Landing</h3>
                    <p class="text-xs text-slate-400 font-medium">On-site orientation and residence permit conversion support.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-32 max-w-4xl mx-auto px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-black italic uppercase tracking-tighter font-heading">Common Queries</h2>
            <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px] mt-2">Compliance & Deployment Info</p>
        </div>
        <div class="space-y-4">
            <template x-for="(faq, index) in [
                {q: 'What are the visa requirements for China?', a: 'All deployments are under the Z-Visa category. Requirements include a valid Indian Passport, degree attestation by MEA, and a clean PCC.'},
                {q: 'How long does the deployment process take?', a: 'Typically 45-60 days from interview success to landing in China.'},
                {q: 'Are medical facilities provided in China?', a: 'Yes, all partners provide health insurance compliant with Chinese Labour Law.'}
            ]">
                <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-[2rem] overflow-hidden shadow-sm">
                    <button @click="faqOpen = faqOpen === index ? null : index" class="w-full px-8 py-6 flex justify-between items-center text-left">
                        <span class="font-black uppercase tracking-tight italic text-slate-800 dark:text-slate-200" x-text="faq.q"></span>
                        <i class="fas fa-plus text-xs transition-transform" :class="faqOpen === index ? 'rotate-45' : ''"></i>
                    </button>
                    <div x-show="faqOpen === index" x-collapse class="px-8 pb-6 text-slate-500 dark:text-slate-400 text-sm font-medium leading-relaxed" x-text="faq.a"></div>
                </div>
            </template>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-900 pt-32 pb-12 transition-colors">
        <div class="max-w-7xl mx-auto px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-16 mb-20">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-8">
                        <a href="index.php" class="bg-slate-900 dark:bg-blue-600 p-2.5 rounded-xl text-white flex items-center justify-center w-12 h-12">
                            <i class="fas fa-bridge-water text-lg"></i>
                        </a>
                        <span class="text-2xl font-black tracking-tighter uppercase font-heading">Talent<span class="text-blue-600">Bridge</span></span>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 max-w-sm font-medium leading-relaxed">The technology-driven recruitment corridor specializing in compliant deployments between the Indian labor market and Chinese industrial zones.</p>
                </div>
                <div>
                    <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-8">Navigation</h4>
                    <ul class="space-y-4 text-sm font-bold text-slate-600 dark:text-slate-400">
                        <li><a href="#how" class="hover:text-blue-600 transition-all">Process Workflow</a></li>
                        <li><a href="#jobs" class="hover:text-blue-600 transition-all">Active Vacancies</a></li>
                        <li><a href="admin.php" class="hover:text-blue-600 transition-all">Admin Console</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-8">System Status</h4>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-[10px] font-black uppercase text-slate-400">AES-256 Encrypted</span>
                    </div>
                </div>
            </div>
            <div class="pt-12 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <p class="text-[10px] font-black text-slate-400 uppercase">© 2026 TalentBridge Technologies.</p>
            </div>
        </div>
    </footer>

    <!-- Application Tracker Modal -->
    <div x-show="openTracker" class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-xl" x-cloak x-transition>
        <div @click.away="openTracker = false" class="bg-white dark:bg-slate-900 w-full max-w-md rounded-[3rem] p-10 shadow-2xl relative border border-white/10">
            <h3 class="text-2xl font-black uppercase italic mb-6">Status Tracker</h3>
            <div class="space-y-4">
                <input type="text" placeholder="Passport Number" class="w-full p-5 bg-slate-50 dark:bg-slate-800 rounded-2xl outline-none font-bold">
                <button class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-widest">Check Progress</button>
            </div>
        </div>
    </div>

    <!-- Job Detail Modal -->
    <div x-show="openDetailModal" class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-slate-900/95 backdrop-blur-xl" x-cloak x-transition>
        <div @click.away="openDetailModal = false" class="bg-white dark:bg-slate-900 w-full max-w-3xl rounded-[3rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col relative border border-white/5">
            <div class="p-10 pb-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-start">
                <div>
                    <span class="bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest mb-4 inline-block">Position Breakdown</span>
                    <h2 class="text-4xl font-black italic tracking-tighter uppercase font-heading" x-text="selectedJob.title"></h2>
                </div>
                <button @click="openDetailModal = false" class="text-slate-400 hover:text-slate-900"><i class="fas fa-circle-xmark text-4xl"></i></button>
            </div>
            <div class="p-10 overflow-y-auto flex-1 space-y-8">
                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Package</p>
                        <p class="text-2xl font-black text-blue-600 tracking-tighter" x-text="formatSalary(selectedJob.salary || '0')"></p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Location</p>
                        <p class="text-2xl font-black tracking-tighter" x-text="selectedJob.location"></p>
                    </div>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium leading-relaxed" x-text="selectedJob.description || 'Verified industrial requirement for Chinese manufacturing hubs.'"></p>
            </div>
            <div class="p-10 bg-slate-50 dark:bg-slate-800 border-t dark:border-slate-700 flex gap-4">
                <button @click="openDetailModal = false; openModal = true" class="flex-1 bg-blue-600 text-white py-6 rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-blue-600/20">Initialize Application</button>
            </div>
        </div>
    </div>

    <!-- Deployment Portal Modal -->
    <div x-show="openModal" class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-950/95 backdrop-blur-2xl" x-cloak x-transition>
        <div x-data="{ step: 1 }" @click.away="openModal = false" class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-[3rem] shadow-2xl overflow-hidden relative border border-white/20">
            <div class="bg-slate-900 p-10 text-white">
                <h3 class="text-2xl font-black uppercase italic">Deployment Portal</h3>
                <div class="flex items-center gap-4 mt-6">
                    <div class="flex-1 h-1 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-600 transition-all duration-500" :style="'width: ' + (step * 33.3) + '%'"></div>
                    </div>
                    <span class="text-[9px] font-black uppercase text-slate-400">Step <span x-text="step"></span> of 3</span>
                </div>
            </div>
            
            <form action="backend_logic.php" method="POST" enctype="multipart/form-data" class="p-10">
                <input type="hidden" name="action" value="frontend_apply">
                <input type="hidden" name="job_title" :value="selectedJob.title || 'General Pool'">

                <!-- Step 1: Identity -->
                <div x-show="step === 1" x-transition>
                    <div class="space-y-4">
                        <input type="text" name="fullname" placeholder="Legal Name (Passport)" required class="w-full p-5 bg-slate-50 dark:bg-slate-800 rounded-2xl outline-none border border-transparent focus:border-blue-600 font-bold">
                        <input type="email" name="email" placeholder="Email Address" required class="w-full p-5 bg-slate-50 dark:bg-slate-800 rounded-2xl outline-none border border-transparent focus:border-blue-600 font-bold">
                        <input type="text" name="passport_no" placeholder="Indian Passport Number" required class="w-full p-5 bg-slate-50 dark:bg-slate-800 rounded-2xl outline-none border border-transparent focus:border-blue-600 font-bold">
                    </div>
                    <button type="button" @click="step = 2" class="w-full mt-8 bg-slate-900 dark:bg-blue-600 text-white py-6 rounded-2xl font-black text-xs uppercase tracking-widest">Continue</button>
                </div>

                <!-- Step 2: Credentials -->
                <div x-show="step === 2" x-transition>
                    <div class="space-y-4">
                        <div class="relative border-2 border-dashed border-slate-200 dark:border-slate-700 p-8 rounded-3xl text-center hover:border-blue-500 transition-colors">
                            <input type="file" name="resume" required class="absolute inset-0 opacity-0 cursor-pointer">
                            <i class="fas fa-cloud-arrow-up text-2xl text-blue-500 mb-2"></i>
                            <p class="text-[10px] font-black uppercase text-slate-400">Upload CV / Credentials (PDF)</p>
                        </div>
                        <select name="experience" class="w-full p-5 bg-slate-50 dark:bg-slate-800 rounded-2xl font-bold uppercase text-xs">
                            <option>1-3 Years Experience</option>
                            <option>4-7 Years Experience</option>
                            <option>8+ Years Experience</option>
                        </select>
                    </div>
                    <div class="flex gap-4 mt-8">
                        <button type="button" @click="step = 1" class="flex-1 bg-slate-100 dark:bg-slate-800 py-6 rounded-2xl font-black text-xs uppercase">Back</button>
                        <button type="button" @click="step = 3" class="flex-[2] bg-slate-900 dark:bg-blue-600 text-white py-6 rounded-2xl font-black text-xs uppercase">Next</button>
                    </div>
                </div>

                <!-- Step 3: Compliance -->
                <div x-show="step === 3" x-transition class="space-y-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-8 rounded-3xl space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="pcc" required class="w-5 h-5 rounded accent-blue-600">
                            <span class="text-xs font-bold uppercase italic">I have a valid Indian PCC.</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="medical" required class="w-5 h-5 rounded accent-blue-600">
                            <span class="text-xs font-bold uppercase italic">I am medically fit for deployment.</span>
                        </label>
                    </div>
                    <div class="flex gap-4">
                         <button type="button" @click="step = 2" class="flex-1 bg-slate-100 dark:bg-slate-800 py-6 rounded-2xl font-black text-xs uppercase">Back</button>
                         <button type="submit" class="flex-[3] bg-blue-600 text-white py-6 rounded-2xl font-black text-xs uppercase tracking-widest shadow-2xl">Finalize Submission</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Live Pulse (Social Proof) -->
    <div x-data="{ showPulse: false, workerName: 'Arjun S.' }" 
         x-init="setTimeout(() => { showPulse = true; }, 4000)"
         x-show="showPulse" x-transition.scale
         class="fixed bottom-8 left-8 z-[90] hidden md:flex items-center gap-4 glass-card p-4 pr-8 rounded-full shadow-2xl">
        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white relative">
            <span class="absolute inset-0 bg-green-500 rounded-full animate-ping opacity-25"></span>
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <p class="text-[9px] font-black uppercase text-slate-400">Real-time update</p>
            <p class="text-xs font-bold"><span x-text="workerName"></span> cleared Suzhou Z-Visa check</p>
        </div>
        <button @click="showPulse = false" class="ml-4 opacity-50"><i class="fas fa-times text-[10px]"></i></button>
    </div>

</body>
</html>