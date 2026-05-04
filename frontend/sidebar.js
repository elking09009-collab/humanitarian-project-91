/* sidebar.js — shared sidebar logic for all pages */

function buildSidebar() {
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';

  const links = [
    { href: 'index.html',                 icon: 'fa-house',                   label: 'الرئيسية',                   section: 'main' },
    { href: 'sos-emergency.html',         icon: 'fa-triangle-exclamation',    label: '🆘 الطوارئ السريعة',         section: 'main', extra: 'sidebar-sos' },
    { href: 'heatmap.html',               icon: 'fa-fire',                    label: 'خريطة الاحتياجات الذكية',    section: 'main' },
    { href: 'services.html',              icon: 'fa-hand-holding-heart',      label: 'خدماتنا',                    section: 'main' },
    { href: 'volunteer-investigator.html', icon: 'fa-user-secret',            label: 'المتطوع المحقق',             section: 'volunteer' },
    { href: 'skill-giving.html',          icon: 'fa-screwdriver-wrench',      label: 'التبرع بالمهارات',           section: 'volunteer' },
    { href: 'volunteer.html',             icon: 'fa-hands-praying',           label: 'تطوع الآن',                  section: 'volunteer' },
    { href: 'human-twin.html',            icon: 'fa-people-arrows',           label: 'التوأمة الإنسانية',          section: 'volunteer' },
    { href: 'impact-wall.html',           icon: 'fa-heart-circle-plus',       label: 'المجتمع والقصص',             section: 'volunteer' },
    { href: 'charity-funds.html',         icon: 'fa-piggy-bank',              label: 'صناديق الصدقة',              section: 'give' },
    { href: 'micro-projects.html',        icon: 'fa-seedling',                label: 'مشاريع متناهية الصغر',       section: 'give' },
    { href: 'micro-endowments.html',      icon: 'fa-landmark',                label: 'الاستثمار الخيري',           section: 'give' },
    { href: 'gift-donation.html',         icon: 'fa-gift',                    label: 'هدية الصدقة الجارية',        section: 'give' },
    { href: 'legacy-giving.html',         icon: 'fa-scroll',                  label: 'الوصية الرقمية',             section: 'give' },
    { href: 'donation.html',              icon: 'fa-circle-dollar-to-slot',   label: 'تبرع الآن',                  section: 'give', extra: 'sidebar-donate' },
    { href: 'smart-inventory.html',       icon: 'fa-boxes-stacked',           label: 'بنك الأصول العينية',         section: 'assets' },
    { href: 'csr-portal.html',            icon: 'fa-building',                label: 'الشركات والمسؤولية',         section: 'assets' },
    { href: 'crisis-predictor.html',      icon: 'fa-brain',                   label: 'التنبؤ بالأزمات AI',         section: 'ai',  extra: 'sidebar-ai' },
    { href: 'chatbot.html',               icon: 'fa-robot',                   label: 'المساعد الذكي',              section: 'ai',  extra: 'sidebar-ai' },
    { href: 'request.html',               icon: 'fa-file-medical',            label: 'تقديم طلب',                  section: 'account' },
    { href: 'login.html',                 icon: 'fa-right-to-bracket',        label: 'تسجيل الدخول',               section: 'account' },
    { href: 'about-us.html',              icon: 'fa-circle-info',             label: 'من نحن',                     section: 'account' },
    { href: 'contact-us.html',            icon: 'fa-envelope',                label: 'اتصل بنا',                   section: 'account' },
  ];

  const sections = {
    main:      'التنقل الرئيسي',
    volunteer: 'التطوع والمجتمع',
    give:      'العطاء والتمويل',
    assets:    'الأصول والشراكات',
    ai:        'الذكاء الاصطناعي',
    account:   'الحساب والمزيد',
  };

  let navHTML = '';
  let lastSection = null;
  links.forEach(link => {
    if (link.section !== lastSection) {
      if (lastSection !== null) navHTML += '<div class="sidebar-divider"></div>';
      navHTML += `<div class="sidebar-section-title">${sections[link.section]}</div>`;
      lastSection = link.section;
    }
    const isActive = link.href === currentPage ? ' active' : '';
    const extra = link.extra ? ` ${link.extra}` : '';
    navHTML += `
      <a href="${link.href}" class="sidebar-link${isActive}${extra}">
        <span class="sidebar-icon"><i class="fas ${link.icon}"></i></span>
        <span class="sidebar-label">${link.label}</span>
      </a>`;
  });

  const html = `
    <!-- Sidebar toggle -->
    <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()" aria-label="قائمة">
      <i class="fas fa-bars"></i>
    </button>

    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar" role="navigation" aria-label="القائمة الرئيسية">
      <div class="sidebar-header">
        <div class="sidebar-logo">
          <i class="fas fa-heart-pulse"></i>
          <span>حياة أفضل</span>
        </div>
        <button class="sidebar-close" onclick="toggleSidebar()" aria-label="إغلاق">
          <i class="fas fa-xmark"></i>
        </button>
      </div>
      <nav class="sidebar-nav">${navHTML}</nav>
    </aside>

    <!-- Floating chatbot bubble -->
    <a href="chatbot.html" class="chat-bubble" title="المساعد الذكي">
      <i class="fas fa-robot"></i>
    </a>`;

  document.body.insertAdjacentHTML('afterbegin', html);
}

function toggleSidebar() {
  const sidebar  = document.getElementById('sidebar');
  const overlay  = document.getElementById('sidebarOverlay');
  const isOpen   = sidebar.classList.toggle('open');
  overlay.classList.toggle('show', isOpen);
  document.body.style.overflow = isOpen ? 'hidden' : '';
}

// Close with ESC
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    const sidebar = document.getElementById('sidebar');
    if (sidebar && sidebar.classList.contains('open')) toggleSidebar();
  }
});

// Build on DOM ready
document.addEventListener('DOMContentLoaded', buildSidebar);
