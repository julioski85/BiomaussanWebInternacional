const slides = [...document.querySelectorAll('.slide')];
const dotsWrap = document.querySelector('.slider-dots');
let current = 0;
slides.forEach((_, i) => {
  const btn = document.createElement('button');
  if (i === 0) btn.classList.add('active');
  btn.addEventListener('click', () => showSlide(i));
  dotsWrap.appendChild(btn);
});
const dots = [...dotsWrap.children];
function showSlide(index){
  slides[current].classList.remove('active');
  dots[current].classList.remove('active');
  current = index;
  slides[current].classList.add('active');
  dots[current].classList.add('active');
}
setInterval(() => showSlide((current + 1) % slides.length), 4500);

const io = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if(entry.isIntersecting) entry.target.classList.add('in');
  });
}, {threshold: .14});
document.querySelectorAll('.reveal').forEach(el => io.observe(el));

const toggle = document.querySelector('.menu-toggle');
const mobileMenu = document.querySelector('.mobile-menu');
if(toggle){
  toggle.addEventListener('click', () => mobileMenu.classList.toggle('open'));
}
document.querySelectorAll('.mobile-menu a').forEach(link => link.addEventListener('click', () => mobileMenu.classList.remove('open')));

const form = document.getElementById('interestForm');
const success = document.getElementById('formSuccess');
form?.addEventListener('submit', (e) => {
  e.preventDefault();
  success.style.display = 'block';
});

document.querySelectorAll('.tilt-card').forEach(card => {
  card.addEventListener('mousemove', (e) => {
    if(window.innerWidth < 1000) return;
    const r = card.getBoundingClientRect();
    const x = e.clientX - r.left;
    const y = e.clientY - r.top;
    const rx = (y / r.height - .5) * -8;
    const ry = (x / r.width - .5) * 8;
    card.style.transform = `perspective(900px) rotateX(${rx}deg) rotateY(${ry}deg)`;
  });
  card.addEventListener('mouseleave', () => card.style.transform = 'perspective(900px) rotateX(0) rotateY(0)');
});
