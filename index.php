<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TalentBridge | India-China Secure Recruitment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap');
        :root { --brand-primary: #2563eb; --brand-dark: #0f172a; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        .font-heading { font-family: 'Space Grotesk', sans-serif; }
        [x-cloak] { display: none !important; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .gradient-text { background: linear-gradient(135deg, #1e293b 0%, #2563eb 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .bento-inner { background: white; border: 1px solid #f1f5f9; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .bento-inner:hover { transform: translateY(-5px); border-color: #3b82f6; box-shadow: 0 25px 50px -12px rgba(59, 130, 246, 0.1); }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .animate-marquee { animation: marquee 30s linear infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
        .animate-float { animation: float 6s ease-in-out infinite; }
    </style>
    <script>
        // IMMEDIATELY remove status from URL so it doesn't persist on refresh
        if (window.location.search.includes('status=')) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>
</head>
<body class="bg-[#f8fafc] text-slate-900 antialiased" 
      x-data="{ 
        openModal: false, 
        openDetailModal: false, 
        activeTab: 'all', 
        mobileMenu: false, 
        scrolled: false,
        selectedJob: {},
        faqOpen: null
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
         :class="scrolled ? 'bg-white/80 backdrop-blur-lg py-3 shadow-lg' : 'bg-transparent py-6'"
         class="fixed w-full z-[100] transition-all duration-500 px-6 lg:px-12">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- RECTIFIED: Logo now points to clean index.php -->
            <a href="index.php" class="flex items-center gap-3 group cursor-pointer">
                <div class="bg-blue-600 p-2.5 rounded-xl shadow-lg shadow-blue-600/20 group-hover:rotate-12 transition-transform">
                    <i class="fas fa-bridge-water text-white text-xl"></i>
                </div>
                <span class="text-2xl font-black tracking-tighter text-slate-900 font-heading uppercase">
                    TALENT<span class="text-blue-600">BRIDGE</span>
                </span>
            </a>
            <div class="hidden lg:flex items-center space-x-10">
                <a href="#how" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 hover:text-blue-600 transition-colors">Process</a>
                <a href="#jobs" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 hover:text-blue-600 transition-colors">Vacancies</a>
                <a href="#faq" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 hover:text-blue-600 transition-colors">FAQ</a>
                <button @click="openModal = true" class="bg-slate-900 text-white px-8 py-3.5 rounded-full font-bold text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl hover:scale-105">
                    Apply to Portal
                </button>
            </div>
            <button @click="mobileMenu = !mobileMenu" class="lg:hidden text-slate-900">
                <i class="fas fa-bars-staggered text-2xl"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-48 pb-20 overflow-hidden">
        <div class="absolute top-0 right-0 -z-10 w-full h-full opacity-10" style="background-image: radial-gradient(#2563eb 0.5px, transparent 0.5px); background-size: 24px 24px;"></div>
        <div class="max-w-7xl mx-auto px-8 grid lg:grid-cols-2 gap-20 items-center">
            <div>
                <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-100 px-4 py-2 rounded-full mb-8">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-blue-700">Official India-China Human Capital Bridge</span>
                </div>
                <h1 class="text-6xl lg:text-8xl font-black text-slate-900 leading-[0.85] tracking-tighter mb-8 font-heading uppercase italic">
                    Powering <span class="gradient-text">Global</span><br>Industry.
                </h1>
                <p class="text-xl text-slate-500 mb-10 max-w-md leading-relaxed font-medium">Verified cross-border recruitment infrastructure. Seamlessly deploying Indian talent to Chinese industrial hubs with full legal compliance.</p>
                <div class="flex flex-wrap gap-4">
                    <button @click="openModal = true" class="bg-blue-600 text-white px-10 py-5 rounded-2xl font-bold text-sm uppercase tracking-widest hover:shadow-2xl hover:shadow-blue-500/40 transition-all">
                        Initialize Deployment
                    </button>
                    <div class="flex items-center gap-4 bg-white px-6 py-4 rounded-2xl border border-slate-200">
                        <div class="flex -space-x-2">
                            <img class="w-8 h-8 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?u=1">
                            <img class="w-8 h-8 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?u=2">
                            <img class="w-8 h-8 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?u=3">
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
                    <p class="text-slate-500 font-medium text-lg leading-relaxed">Multi-layer skill and identity verification using Indian Aadhar and biometric assessments to ensure 0% identity fraud.</p>
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
                <div class="bg-slate-100 w-16 h-16 rounded-2xl flex items-center justify-center text-slate-900 mb-8">
                    <i class="fas fa-handshake-angle text-2xl"></i>
                </div>
                <h3 class="text-2xl font-black italic tracking-tighter uppercase mb-4">Ethical Sourcing</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Adhering to ILO standards, ensuring zero recruitment fees for workers and fair living conditions in China.</p>
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

    <!-- Job Feed -->
    <section id="jobs" class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
                <div>
                    <h2 class="text-5xl font-black text-slate-900 italic tracking-tighter uppercase font-heading">Verified Openings</h2>
                    <p class="text-slate-400 font-bold text-[10px] uppercase tracking-[0.3em] mt-2">Active industrial requirements in China</p>
                </div>
                <div class="flex gap-2 bg-slate-50 p-2 rounded-2xl border border-slate-100 shadow-inner">
                    <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-500'" class="px-8 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">All Roles</button>
                    <button @click="activeTab = 'tech'" :class="activeTab === 'tech' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-500'" class="px-8 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Technical</button>
                </div>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
                <?php
                $jobs = $pdo->query("SELECT * FROM jobs ORDER BY created_at DESC")->fetchAll();
                foreach($jobs as $job): 
                    $isTech = (stripos($job['tags'], 'tech') !== false || stripos($job['tags'], 'engineer') !== false) ? 'tech' : 'other';
                    $jobJson = htmlspecialchars(json_encode($job), ENT_QUOTES, 'UTF-8');
                ?>
                <div x-show="activeTab === 'all' || activeTab === '<?= $isTech ?>'" 
                     class="group bg-slate-50/50 border border-slate-100 p-8 rounded-[3rem] transition-all duration-500 hover:bg-white hover:shadow-[0_40px_80px_-20px_rgba(0,0,0,0.08)] hover:-translate-y-2">
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-14 h-14 bg-white rounded-2xl shadow-sm group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center transition-colors">
                            <i class="fas fa-briefcase text-xl"></i>
                        </div>
                        <span class="text-[9px] font-black text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-widest border border-blue-100">Apply Open</span>
                    </div>

                    <h3 class="text-2xl font-black text-slate-900 mb-2 leading-tight tracking-tighter italic uppercase font-heading"><?= htmlspecialchars($job['title']) ?></h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-10"><i class="fas fa-location-dot text-blue-500 mr-2"></i> <?= htmlspecialchars($job['location']) ?></p>

                    <div class="mt-auto flex items-center justify-between">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Annual Compensation</p>
                            <p class="text-xl font-black text-slate-900 tracking-tighter"><?= htmlspecialchars($job['salary']) ?></p>
                        </div>
                        <button @click="selectedJob = <?= $jobJson ?>; openDetailModal = true" 
                                class="w-12 h-12 rounded-full border border-slate-200 bg-white flex items-center justify-center group-hover:bg-slate-900 group-hover:text-white transition-all">
                            <i class="fas fa-arrow-right text-sm"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
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
                {q: 'What are the visa requirements for China?', a: 'All deployments are under the Z-Visa (Working Visa) category. Requirements include a valid Indian Passport, degree attestation by MEA, and a clean PCC.'},
                {q: 'How long does the deployment process take?', a: 'Typically 45-60 days from interview success to landing in China, depending on Provincial Bureau speeds.'},
                {q: 'Are medical facilities provided in China?', a: 'Yes, all partners provide comprehensive health insurance compliant with Chinese Labour Law.'}
            ]">
                <div class="bg-white border border-slate-100 rounded-[2rem] overflow-hidden">
                    <button @click="faqOpen = faqOpen === index ? null : index" class="w-full px-8 py-6 flex justify-between items-center text-left">
                        <span class="font-black uppercase tracking-tight italic text-slate-800" x-text="faq.q"></span>
                        <i class="fas fa-plus text-xs transition-transform" :class="faqOpen === index ? 'rotate-45' : ''"></i>
                    </button>
                    <div x-show="faqOpen === index" x-collapse class="px-8 pb-6 text-slate-500 text-sm font-medium leading-relaxed" x-text="faq.a"></div>
                </div>
            </template>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white pt-32 pb-12">
        <div class="max-w-7xl mx-auto px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-16 mb-20">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-8">
                        <!-- RECTIFIED: Footer Logo also points to index.php -->
                        <a href="index.php" class="bg-slate-900 p-2.5 rounded-xl text-white flex items-center justify-center w-12 h-12 hover:scale-110 transition-transform">
                            <i class="fas fa-bridge-water text-lg"></i>
                        </a>
                        <span class="text-2xl font-black tracking-tighter uppercase font-heading">Talent<span class="text-blue-600">Bridge</span></span>
                    </div>
                    <p class="text-slate-500 max-w-sm font-medium leading-relaxed">The only technology-driven recruitment corridor specializing in high-security, compliant deployments between the Indian labor market and Chinese industrial zones.</p>
                    <div class="flex gap-4 mt-8">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-8">Navigation</h4>
                    <ul class="space-y-4 text-sm font-bold text-slate-600">
                        <li><a href="#how" class="hover:text-blue-600 transition-all">Process Workflow</a></li>
                        <li><a href="#jobs" class="hover:text-blue-600 transition-all">Active Vacancies</a></li>
                        <li><a href="#faq" class="hover:text-blue-600 transition-all">Support & FAQ</a></li>
                        <li><a href="admin.php" class="hover:text-blue-600 transition-all">Admin Console</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-8">Legal & Compliance</h4>
                    <ul class="space-y-4 text-sm font-bold text-slate-600 text-right md:text-left">
                        <li>ISO 27001 Certified</li>
                        <li>MEA Registered</li>
                        <li>Labour Bureau Compliant</li>
                        <li>Privacy Policy</li>
                    </ul>
                </div>
            </div>
            <div class="pt-12 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">© 2025 TalentBridge Technologies. All Rights Reserved.</p>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">System Status: Operational</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Job Detail Modal -->
    <div x-show="openDetailModal" class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-xl" x-cloak x-transition>
        <div @click.away="openDetailModal = false" class="bg-white w-full max-w-3xl rounded-[3rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col relative">
            <div class="p-10 pb-6 border-b border-slate-100 flex justify-between items-start">
                <div>
                    <span class="bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest mb-4 inline-block">Position Breakdown</span>
                    <h2 class="text-4xl font-black text-slate-900 italic tracking-tighter uppercase font-heading" x-text="selectedJob.title"></h2>
                </div>
                <button @click="openDetailModal = false" class="text-slate-300 hover:text-slate-900"><i class="fas fa-circle-xmark text-4xl"></i></button>
            </div>
            <div class="p-10 overflow-y-auto flex-1 space-y-8">
                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-slate-50 p-6 rounded-[2rem] border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Proposed Package</p>
                        <p class="text-2xl font-black text-blue-600 tracking-tighter" x-text="selectedJob.salary"></p>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-[2rem] border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Location</p>
                        <p class="text-2xl font-black text-slate-800 tracking-tighter" x-text="selectedJob.location"></p>
                    </div>
                </div>
                <p class="text-slate-500 font-medium leading-relaxed" x-text="selectedJob.description || 'Verified industrial requirement for Chinese manufacturing hubs. Includes housing allowance and Z-Visa support.'"></p>
            </div>
            <div class="p-10 bg-slate-50 border-t flex gap-4">
                <button @click="openDetailModal = false; openModal = true" class="flex-1 bg-blue-600 text-white py-6 rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-blue-600/20">Apply for Role</button>
            </div>
        </div>
    </div>

    <!-- Deployment Portal Modal -->
    <div x-show="openModal" class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-900/95 backdrop-blur-xl" x-cloak x-transition>
        <div x-data="{ step: 1 }" @click.away="openModal = false" class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl overflow-hidden relative border border-white/20">
            <div class="bg-slate-900 p-10 text-white relative">
                <button @click="openModal = false; step = 1" class="absolute top-8 right-8 text-slate-400 hover:text-white"><i class="fas fa-times text-2xl"></i></button>
                <h3 class="text-2xl font-black uppercase italic font-heading">Secure Deployment Portal</h3>
                <div class="flex items-center gap-4 mt-6">
                    <div class="flex-1 h-1 bg-white/10 rounded-full"><div class="h-full bg-blue-600 transition-all duration-500" :style="'width: ' + (step * 33) + '%'"></div></div>
                    <span class="text-[9px] font-black uppercase text-slate-400">Step <span x-text="step"></span> of 3</span>
                </div>
            </div>
            
            <form action="backend_logic.php" method="POST" class="p-10">
                <input type="hidden" name="action" value="frontend_apply">
                <input type="hidden" name="job_title" :value="selectedJob.title || 'General Pool'">

                <!-- Step 1: Identity -->
                <div x-show="step === 1" x-transition>
                    <h4 class="text-[10px] font-black uppercase text-slate-400 mb-6">01. Identity Verification</h4>
                    <div class="space-y-4">
                        <input type="text" name="fullname" placeholder="Legal Name (Passport)" required class="w-full p-5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-blue-600 font-semibold">
                        <input type="email" name="email" placeholder="Email Address" required class="w-full p-5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-blue-600 font-semibold">
                        <input type="text" name="passport_no" placeholder="Indian Passport Number" required class="w-full p-5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-blue-600 font-semibold text-sm">
                    </div>
                    <button type="button" @click="step = 2" class="w-full mt-8 bg-slate-900 text-white py-6 rounded-2xl font-black text-xs uppercase tracking-widest">Next Phase</button>
                </div>

                <!-- Step 2: Experience -->
                <div x-show="step === 2" x-transition>
                    <h4 class="text-[10px] font-black uppercase text-slate-400 mb-6">02. Professional Domain</h4>
                    <div class="space-y-4">
                        <select name="experience" class="w-full p-5 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-xs uppercase tracking-widest text-slate-700">
                            <option value="0-3 Years">0-3 Years Experience</option>
                            <option value="4-7 Years">4-7 Years Experience</option>
                            <option value="8+ Years">8+ Years Experience</option>
                        </select>
                        <input type="text" name="domain" placeholder="Domain (e.g. Mechanical Engineer)" required class="w-full p-5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-blue-600 font-semibold">
                    </div>
                    <div class="flex gap-4 mt-8">
                        <button type="button" @click="step = 1" class="flex-1 border border-slate-200 text-slate-400 py-6 rounded-2xl font-black text-xs uppercase tracking-widest">Back</button>
                        <button type="button" @click="step = 3" class="flex-2 bg-slate-900 text-white px-10 py-6 rounded-2xl font-black text-xs uppercase tracking-widest">Compliance</button>
                    </div>
                </div>

                <!-- Step 3: Compliance (Consolidated) -->
                <div x-show="step === 3" x-transition class="space-y-6 text-center">
                    <h4 class="text-[10px] font-black uppercase text-slate-400 mb-6 text-left">03. Final Authorization</h4>
                    <div class="bg-blue-50 p-8 rounded-[2.5rem] border border-blue-100 text-left space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="pcc" value="1" required class="w-5 h-5 rounded border-slate-300">
                            <span class="text-xs font-bold text-slate-600 uppercase italic">I possess a valid PCC or can obtain one.</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="medical" value="1" required class="w-5 h-5 rounded border-slate-300">
                            <span class="text-xs font-bold text-slate-600 uppercase italic">I am medically fit for overseas travel.</span>
                        </label>
                        <input type="hidden" name="consent" value="1">
                    </div>
                    <div class="flex gap-4">
                         <button type="button" @click="step = 2" class="flex-1 border border-slate-200 text-slate-400 py-6 rounded-2xl font-black text-xs uppercase tracking-widest">Back</button>
                         <button type="submit" class="flex-[3] bg-blue-600 text-white py-6 rounded-2xl font-black text-sm uppercase tracking-[0.2em] shadow-2xl shadow-blue-600/40">Submit Dossier</button>
                    </div>
                    <p class="text-[8px] font-black uppercase text-slate-400">AES-256 Encrypted Submission</p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
