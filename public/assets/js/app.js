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

  /* --- Buying advisor: real recommendation engine (/kaufberater) --- */
  var adv = document.querySelector("[data-advisor]");
  if (adv) {
    var products = [], labels = {};
    try { products = JSON.parse(adv.querySelector("[data-advisor-products]").textContent); } catch (e) {}
    try { labels = JSON.parse(adv.querySelector("[data-advisor-labels]").textContent); } catch (e) {}
    var aSteps = Array.prototype.slice.call(adv.querySelectorAll("[data-step]"));
    var aResult = adv.querySelector("[data-advisor-result]");
    var aProg = adv.querySelector("[data-advisor-progress]");
    var aAns = {}, aLab = {};

    function aShow(i) {
      aSteps.forEach(function (s, idx) { s.hidden = idx !== i; });
      if (aResult) aResult.hidden = true;
      if (aProg) aProg.style.width = (i / aSteps.length * 100) + "%";
    }

    function recommend(a) {
      var targets = { kajak: 1.5, tender: 3.5, schlauchboot: 6, angel: 8, segel: 6 };
      var ps = targets[a.boot] != null ? targets[a.boot] : 6;
      if (a.size === "gross") ps += 3;
      if (a.size === "klein") ps -= 1;
      if (a.use === "leistung") ps += 4;
      if (a.use === "backup") ps = Math.max(1, ps - 2);
      if (ps < 1) ps = 1;
      var drive = a.drive === "egal" ? (a.use === "leise" ? "elektro" : "benzin") : a.drive;
      var pool = products.filter(function (p) { return p.antrieb === drive; });
      if (!pool.length) pool = products.slice();
      pool.sort(function (x, y) { return Math.abs(x.ps - ps) - Math.abs(y.ps - ps); });
      var best = pool[0];
      var alt = pool[1] || products.filter(function (p) { return p !== best; })[0];
      return { best: best, alt: alt, ps: Math.round(ps), drive: drive };
    }

    function card(p, big) {
      if (!p) return "";
      var media = p.img ? '<img src="' + p.img + '" alt="' + p.title + '" loading="lazy">' : '<span class="acard-rec__ph">' + p.title + '</span>';
      return '<a class="acard-rec' + (big ? ' acard-rec--big' : '') + '" href="' + p.url + '">' +
        '<div class="acard-rec__media">' + media + '</div>' +
        '<div class="acard-rec__body"><span class="acard-rec__brand">' + p.brand + '</span>' +
        '<strong>' + p.title + '</strong>' +
        '<span class="acard-rec__specs">' + p.ps + ' PS · ' + p.kw + ' kW · ' + p.kg + ' kg</span>' +
        '<span class="acard-rec__price">' + p.price + '</span></div></a>';
    }

    function render() {
      var r = recommend(aAns);
      if (!r.best) return;
      var drv = r.drive === "elektro" ? labels.electric : labels.petrol;
      var reason = (labels.reason || "")
        .replace("{boot}", aLab.boot || aAns.boot)
        .replace("{use}", aLab.use || aAns.use)
        .replace("{drive}", drv)
        .replace("{ps}", r.ps);
      aResult.innerHTML =
        '<div class="advisor__rec-head"><span class="eyebrow" style="color:var(--green)">' + labels.rec + '</span><p class="advisor__reason">' + reason + '</p></div>' +
        card(r.best, true) +
        (r.alt ? '<div class="advisor__alt"><span class="eyebrow">' + labels.alt + '</span>' + card(r.alt, false) + '</div>' : '') +
        '<div class="advisor__actions"><a class="btn btn--cta btn--lg" href="' + r.best.url + '">' + labels.view + '</a>' +
        '<a class="btn btn--ghost" href="https://wa.me/4940609019969" rel="nofollow">' + labels.advice + '</a>' +
        '<button class="btn btn--ghost" type="button" data-advisor-restart>' + labels.restart + '</button></div>';
      aSteps.forEach(function (s) { s.hidden = true; });
      aResult.hidden = false;
      if (aProg) aProg.style.width = "100%";
      var rb = aResult.querySelector("[data-advisor-restart]");
      if (rb) rb.addEventListener("click", function () { aAns = {}; aLab = {}; aShow(0); });
    }

    adv.querySelectorAll("[data-choice]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var step = btn.closest("[data-step]"), key = step.getAttribute("data-step");
        aAns[key] = btn.getAttribute("data-choice");
        aLab[key] = (btn.querySelector("b") || {}).textContent || aAns[key];
        var idx = aSteps.indexOf(step);
        if (idx < aSteps.length - 1) { aShow(idx + 1); } else { render(); }
      });
    });
    if (aSteps.length) aShow(0);
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

  /* --- Cookie consent + consent-gated analytics (GA4 / GTM) --- */
  function loadAnalytics() {
    var a = window.__kkAnalytics;
    if (!a || window.__kkAnalyticsLoaded) return;
    window.__kkAnalyticsLoaded = true;
    window.dataLayer = window.dataLayer || [];
    if (a.ga4) {
      var s = document.createElement("script");
      s.async = true; s.src = "https://www.googletagmanager.com/gtag/js?id=" + a.ga4;
      document.head.appendChild(s);
      window.gtag = function () { window.dataLayer.push(arguments); };
      window.gtag("js", new Date());
      window.gtag("config", a.ga4, { anonymize_ip: true });
    }
    if (a.gtm) {
      window.dataLayer.push({ "gtm.start": +new Date(), event: "gtm.js" });
      var g = document.createElement("script");
      g.async = true; g.src = "https://www.googletagmanager.com/gtm.js?id=" + a.gtm;
      document.head.appendChild(g);
    }
  }
  try { if (localStorage.getItem("mv-consent") === "all") loadAnalytics(); } catch (e) {}

  var consent = document.querySelector("[data-consent]");
  if (consent) {
    var KEY = "mv-consent", stored = null;
    try { stored = localStorage.getItem(KEY); } catch (e) {}
    if (!stored) consent.hidden = false;
    var setConsent = function (v) {
      try { localStorage.setItem(KEY, v); } catch (e) {}
      consent.hidden = true;
      if (v === "all") loadAnalytics();
    };
    var acc = consent.querySelector("[data-consent-accept]");
    var dec = consent.querySelector("[data-consent-decline]");
    if (acc) acc.addEventListener("click", function () { setConsent("all"); });
    if (dec) dec.addEventListener("click", function () { setConsent("necessary"); });
  }
})();
