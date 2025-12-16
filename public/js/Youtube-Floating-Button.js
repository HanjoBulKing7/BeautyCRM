// Floating Social Buttons
document.addEventListener("DOMContentLoaded", function () {
  // ====== YouTube Button ======
  const ytButton = document.createElement("a");
  ytButton.href = "#";
  ytButton.target = "_blank";
  ytButton.rel = "noopener";
  ytButton.className = "youtube-button";
  ytButton.setAttribute("aria-label", "Subscribe on YouTube");

  ytButton.innerHTML = `
    <svg viewBox="0 0 24 24" aria-hidden="true">
      <path fill="currentColor" d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
    </svg>
    <span class="subscribe-text">Subscribe</span>
  `;

  // ====== Instagram Button ======
  const igButton = document.createElement("a");
  igButton.href = "https://www.instagram.com/beautybonita_studio?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==";
  igButton.target = "_blank";
  igButton.rel = "noopener";
  igButton.className = "instagram-button";
  igButton.setAttribute("aria-label", "Follow on Instagram");

  igButton.innerHTML = `
    <svg viewBox="0 0 24 24" aria-hidden="true">
      <path fill="currentColor" d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm10 2H7a3 3 0 00-3 3v10a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3zm-5 3.5a5.5 5.5 0 110 11 5.5 5.5 0 010-11zm0 2a3.5 3.5 0 100 7 3.5 3.5 0 000-7zM19 6.5a1.25 1.25 0 11-2.5 0 1.25 1.25 0 012.5 0z"/>
    </svg>
    <span class="follow-text">Follow</span>
  `;

  // ====== Styles (kept close to your original) ======
  const style = document.createElement("style");
  style.textContent = `
/* Base badge */
.youtube-button, .instagram-button {
  position: fixed;
  right: 20px;
  background-color: #ff0000;
  color: white;
  border-radius: 30px;
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0px;
  text-decoration: none;
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  z-index: 99999;
  animation: float 3s ease-in-out infinite;
}

/* Individual placement */
.youtube-button { bottom: 20px; background-color: #ff0000; }
.instagram-button { bottom: 80px;
  background: linear-gradient(to right, #833ab4, #fd1d1d, #fcb045);
}

/* Icons */
.youtube-button svg,
.instagram-button svg {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
  transition: transform 0.3s ease;
}

/* Texts (collapsed by default) */
.subscribe-text, .follow-text {
  font-family: Arial, sans-serif;
  font-weight: 500;
  font-size: 0;
  max-width: 0;
  overflow: hidden;
  white-space: nowrap;
  transition: all 0.3s ease;
}

/* Hover expand */
.youtube-button:hover,
.instagram-button:hover {
  width: 140px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.3);
  gap: 8px;
}

.youtube-button:hover { background-color: #e60000; }
.instagram-button:hover { filter: saturate(1.1) brightness(1.05); }

.youtube-button:hover .subscribe-text,
.instagram-button:hover .follow-text {
  font-size: 14px;
  max-width: 100px;
  padding-right: 8px;
}

.youtube-button:hover svg,
.instagram-button:hover svg {
  transform: rotate(360deg);
}

/* Floating animation */
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

/* Ripple on click */
.youtube-button:active::after,
.instagram-button:active::after {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(255,255,255,0.3);
  border-radius: 50%;
  animation: ripple 0.6s linear;
}
@keyframes ripple {
  from { transform: scale(0); opacity: 1; }
  to   { transform: scale(2); opacity: 0; }
}

/* Mobile spacing */
@media (max-width: 480px) {
  .youtube-button, .instagram-button {
    right: 10px;
    width: 45px;
    height: 45px;
  }
  .instagram-button { bottom: 72px; }
  .youtube-button:hover, .instagram-button:hover { width: 120px; }
  .youtube-button svg, .instagram-button svg { width: 20px; height: 20px; }
  .subscribe-text, .follow-text { font-size: 12px; }
}

/* Optional: prefers-reduced-motion */
@media (prefers-reduced-motion: reduce) {
  .youtube-button, .instagram-button { animation: none; }
  .youtube-button:hover svg, .instagram-button:hover svg { transform: none; }
}
  `;

  // ====== Intro overlay (kept from your original) ======
  const overlay = document.createElement("div");
  overlay.style.cssText = `
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,0.3);
    backdrop-filter: blur(5px);
    z-index: 9998;
    transition: opacity 0.5s ease;
  `;

  // ====== Mount ======
  document.head.appendChild(style);
  document.body.appendChild(overlay);
  document.body.appendChild(ytButton);
  document.body.appendChild(igButton);

  // ====== Intro animation (kept) ======
  setTimeout(() => {
    overlay.style.opacity = "0";
    setTimeout(() => overlay.remove(), 500);
    ytButton.style.animation = "float 3s ease-in-out infinite, none";
    igButton.style.animation = "float 3s ease-in-out infinite, none";
  }, 1000);

  // ====== Initial hover simulation (YouTube only, as in original) ======
  setTimeout(() => {
    ytButton.style.cssText = `width: 140px;gap: 8px;`;
    ytButton.querySelector(".subscribe-text").style.cssText = `
      font-size: 14px;
      max-width: 100px;
      padding-right: 8px;
    `;
  }, 100);
  setTimeout(() => {
    ytButton.style.cssText = "";
    ytButton.querySelector(".subscribe-text").style.cssText = "";
  }, 2000);
});