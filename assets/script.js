
const qs=(s,p=document)=>p.querySelector(s);
const qsa=(s,p=document)=>[...p.querySelectorAll(s)];

const menuBtn=qs('.menu-toggle');
const nav=qs('.nav-links');
if(menuBtn && nav){
  menuBtn.addEventListener('click',()=>{
    nav.classList.toggle('open');
    menuBtn.setAttribute('aria-expanded',nav.classList.contains('open'));
  });
  qsa('.nav-links a').forEach(a=>a.addEventListener('click',()=>nav.classList.remove('open')));
}

const backTop=qs('.back-top');
window.addEventListener('scroll',()=>{
  if(backTop) backTop.classList.toggle('show',scrollY>500);
});
if(backTop) backTop.addEventListener('click',()=>scrollTo({top:0,behavior:'smooth'}));

const revealObserver=new IntersectionObserver(entries=>{
  entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('visible');revealObserver.unobserve(e.target)}});
},{threshold:.12});
qsa('.reveal').forEach(el=>revealObserver.observe(el));

qsa('.faq-question').forEach(btn=>{
  btn.addEventListener('click',()=>{
    const item=btn.closest('.faq-item');
    const ans=qs('.faq-answer',item);
    const open=item.classList.toggle('open');
    ans.style.maxHeight=open?ans.scrollHeight+'px':'0';
    btn.setAttribute('aria-expanded',open);
  });
});

function openWhatsApp(form){
  const data=new FormData(form);
  const title=form.dataset.formTitle || 'वेबसाइटबाट नयाँ सन्देश';
  let lines=[`*${title}*`];
  for(const [k,v] of data.entries()){
    if(String(v).trim()) lines.push(`${k}: ${v}`);
  }
  const url='https://wa.me/9779844639228?text='+encodeURIComponent(lines.join('\n'));
  const status=qs('.form-status',form);
  if(status){status.style.display='block';status.textContent='WhatsApp खुल्दैछ। कृपया सन्देश पठाउनुहोस्।';}
  window.open(url,'_blank','noopener');
}
qsa('form[data-whatsapp]').forEach(form=>{
  form.addEventListener('submit',e=>{e.preventDefault();openWhatsApp(form)});
});

const year=qs('[data-year]');
if(year) year.textContent=new Date().getFullYear();

// Appointment form submission
const apptForm = document.getElementById('appointmentForm');
if (apptForm) {
    apptForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        const status = document.getElementById('formStatus');
        btn.disabled = true;
        btn.textContent = 'पठाउँदै...';
        status.style.display = 'block';
        status.textContent = 'कृपया प्रतिक्षा गर्नुहोस्...';
        status.style.background = '#fff3cd';
        status.style.color = '#66451d';

        const data = Object.fromEntries(new FormData(apptForm).entries());

        try {
            const res = await fetch('https://api.astroshreehari.com/api/appointments.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if (result.success) {
                status.style.background = '#d4edda';
                status.style.color = '#155724';
                status.textContent = '✅ तपाईंको अनुरोध सफलतापूर्वक प्राप्त भयो। हामी चाँडै सम्पर्क गर्नेछौं।';
                apptForm.reset();
            } else {
                throw new Error(result.message || 'Unknown error');
            }
        } catch (err) {
            status.style.background = '#f8d7da';
            status.style.color = '#721c24';
            status.textContent = '❌ समस्या भयो। कृपया WhatsApp मा सिधै सम्पर्क गर्नुहोस्।';
        } finally {
            btn.disabled = false;
            btn.textContent = 'परामर्श अनुरोध पठाउनुहोस्';
        }
    });
}

// Service Worker registration
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js');
}

// Language toggle button
const langToggle = document.createElement('button');
langToggle.className = 'lang-toggle';
langToggle.textContent = localStorage.getItem('lang') === 'en' ? 'ने' : 'EN';
langToggle.addEventListener('click', () => {
    const current = localStorage.getItem('lang') || 'ne';
    const next = current === 'ne' ? 'en' : 'ne';
    localStorage.setItem('lang', next);
    langToggle.textContent = next === 'en' ? 'ने' : 'EN';
    location.reload();
});
const navActions = document.querySelector('.nav-actions');
if (navActions) navActions.prepend(langToggle);
