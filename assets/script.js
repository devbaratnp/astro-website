
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
