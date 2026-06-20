/* Kielkraft - progressive enhancement (no framework) */
(function () {
  "use strict";

  /* --- Mobile nav --- */
  var toggle = document.querySelector("[data-nav-toggle]");
  var nav = document.querySelector(".main-nav");
  if (toggle && nav) {
    toggle.setAttribute("aria-expanded", "false");
    toggle.addEventListener("click", function () {
      var open = nav.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
  }

  /* --- Header elevation on scroll --- */
  var header = document.querySelector(".site-header");
  if (header) {
    var onScroll = function () { header.classList.toggle("is-scrolled", window.scrollY > 8); };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  /* --- Konsole: variant selection --- */
  document.querySelectorAll("[data-konsole]").forEach(function (k) {
    var priceEl = k.querySelector("[data-price]");
    var skuEl = k.querySelector("[data-sku]");
    var miniPrice = document.querySelector("[data-mini-price]");
    var data = [];
    try { data = JSON.parse(k.getAttribute("data-variants") || "[]"); } catch (e) {}
    var btns = k.querySelectorAll("[data-variant]");
    btns.forEach(function (btn) {
      btn.addEventListener("click", function () {
        btns.forEach(function (b) { b.setAttribute("aria-pressed", "false"); });
        btn.setAttribute("aria-pressed", "true");
        var v = data[+btn.getAttribute("data-variant")];
        if (!v) return;
        if (priceEl) priceEl.textContent = v.priceFormatted;
        if (skuEl) skuEl.textContent = v.sku;
        if (miniPrice) miniPrice.textContent = v.priceFormatted;
      });
    });
  });

  /* --- Mini console: reveal when the main console scrolls out of view --- */
  var kons = document.querySelector("[data-konsole]");
  var mini = document.querySelector("[data-mini-konsole]");
  if (kons && mini && "IntersectionObserver" in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { mini.classList.toggle("is-visible", !e.isIntersecting); });
    }, { rootMargin: "-140px 0px 0px 0px", threshold: 0 });
    io.observe(kons);
  }

  /* --- Add to cart (visual stub until the shop module is wired in Phase 5) --- */
  function bumpCart() {
    document.querySelectorAll("[data-cart-count]").forEach(function (c) {
      c.textContent = String((parseInt(c.textContent, 10) || 0) + 1);
    });
  }
  document.querySelectorAll("[data-add-to-cart]").forEach(function (btn) {
    var original = btn.textContent;
    btn.addEventListener("click", function () {
      bumpCart();
      btn.textContent = btn.getAttribute("data-added") || "✓";
      setTimeout(function () { btn.textContent = original; }, 1600);
    });
  });

  /* --- Buying advisor (client-side wizard, see /kaufberater) --- */
  var wizard = document.querySelector("[data-wizard]");
  if (wizard) {
    var steps = Array.prototype.slice.call(wizard.querySelectorAll("[data-step]"));
    var result = wizard.querySelector("[data-result]");
    var answers = {};
    function show(i) {
      steps.forEach(function (s, idx) { s.hidden = idx !== i; });
      if (result) result.hidden = true;
    }
    wizard.querySelectorAll("[data-choice]").forEach(function (c) {
      c.addEventListener("click", function () {
        var step = c.closest("[data-step]");
        answers[step.getAttribute("data-step")] = c.getAttribute("data-choice");
        var next = parseInt(step.getAttribute("data-next"), 10);
        if (!isNaN(next) && steps[next]) { show(next); }
        else if (result) {
          steps.forEach(function (s) { s.hidden = true; });
          result.hidden = false;
        }
      });
    });
    if (steps.length) show(0);
  }

  /* --- Category listing: sort + filter --- */
  var listing = document.querySelector("[data-listing]");
  if (listing) {
    var grid = listing.querySelector("[data-listing-grid]");
    var items = Array.prototype.slice.call(listing.querySelectorAll(".listing-item"));
    var sortSel = listing.querySelector("[data-sort]");
    var steerSel = listing.querySelector("[data-filter-steuerung]");
    var countEl = listing.querySelector("[data-count]");
    var countWord = countEl ? countEl.textContent.replace(/^\d+\s*/, "") : "";
    var apply = function () {
      var steer = steerSel ? steerSel.value : "";
      var visible = items.filter(function (it) {
        var ok = !steer || (it.getAttribute("data-steuerung") || "").indexOf(steer) !== -1;
        it.classList.toggle("is-hidden", !ok);
        return ok;
      });
      var sort = sortSel ? sortSel.value : "power";
      visible.sort(function (a, b) {
        if (sort === "price-asc") return (+a.dataset.price) - (+b.dataset.price);
        if (sort === "price-desc") return (+b.dataset.price) - (+a.dataset.price);
        return (+a.dataset.power) - (+b.dataset.power);
      });
      if (grid) visible.forEach(function (it) { grid.appendChild(it); });
      if (countEl) countEl.textContent = visible.length + " " + countWord;
    };
    if (sortSel) sortSel.addEventListener("change", apply);
    if (steerSel) steerSel.addEventListener("change", apply);
  }

  /* --- Cookie consent --- */
  var consent = document.querySelector("[data-consent]");
  if (consent) {
    var KEY = "mv-consent";
    var stored = null;
    try { stored = localStorage.getItem(KEY); } catch (e) {}
    if (!stored) consent.hidden = false;
    var setConsent = function (v) {
      try { localStorage.setItem(KEY, v); } catch (e) {}
      consent.hidden = true;
    };
    var acc = consent.querySelector("[data-consent-accept]");
    var dec = consent.querySelector("[data-consent-decline]");
    if (acc) acc.addEventListener("click", function () { setConsent("all"); });
    if (dec) dec.addEventListener("click", function () { setConsent("necessary"); });
  }
})();
