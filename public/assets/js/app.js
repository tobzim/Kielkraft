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
        var cartSku = k.querySelector("[data-cart-sku]"); if (cartSku) cartSku.value = v.sku;
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

  /* --- Add to cart: submit the buy form (used by the mini-console button) --- */
  document.querySelectorAll("[data-add-to-cart]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var form = document.querySelector("[data-cart-form]");
      if (!form) return;
      if (form.requestSubmit) { form.requestSubmit(); } else { form.submit(); }
    });
  });

  /* --- Cart badge: refresh from server so it is correct even with page cache --- */
  if (document.querySelector("[data-cart-count]") && window.fetch) {
    fetch("/cart/count.json", { headers: { Accept: "application/json" } })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        document.querySelectorAll("[data-cart-count]").forEach(function (b) { b.textContent = d.count; });
        document.querySelectorAll("[data-cart-total]").forEach(function (t) { t.textContent = d.formatted; });
      })
      .catch(function () {});
  }

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

  /* --- Category listing: faceted filter + sort --- */
  var listing = document.querySelector("[data-listing]");
  if (listing) {
    var grid = listing.querySelector("[data-listing-grid]");
    var items = Array.prototype.slice.call(listing.querySelectorAll(".listing-item"));
    var sortSel = listing.querySelector("[data-sort]");
    var countEl = listing.querySelector("[data-count]");
    var countWord = countEl ? countEl.textContent.replace(/^\d+\s*/, "") : "";
    var emptyEl = listing.querySelector("[data-listing-empty]");
    var resetBtn = listing.querySelector("[data-filter-reset]");
    var filters = Array.prototype.slice.call(listing.querySelectorAll("[data-filter]"));

    // group inputs by their data-filter value (= the item dataset key)
    var groups = {};
    filters.forEach(function (f) {
      var g = f.getAttribute("data-filter");
      (groups[g] = groups[g] || []).push(f);
    });

    function matches(it) {
      for (var g in groups) {
        var checked = groups[g].filter(function (f) { return f.checked; });
        if (!checked.length) continue;                 // group inactive = no constraint
        var hit = checked.some(function (f) {
          if (f.hasAttribute("data-min")) {             // numeric range facet
            var v = parseFloat(it.dataset[g] || "0");
            return v >= parseFloat(f.getAttribute("data-min")) && v <= parseFloat(f.getAttribute("data-max"));
          }
          var val = f.getAttribute("data-value");       // token / exact facet
          return (" " + (it.dataset[g] || "") + " ").indexOf(" " + val + " ") !== -1;
        });
        if (!hit) return false;                          // AND across groups
      }
      return true;
    }

    function apply() {
      var visible = items.filter(function (it) {
        var ok = matches(it);
        it.classList.toggle("is-hidden", !ok);
        return ok;
      });
      var sort = sortSel ? sortSel.value : "power";
      visible.sort(function (a, b) {
        if (sort === "price-asc") return (+a.dataset.price) - (+b.dataset.price);
        if (sort === "price-desc") return (+b.dataset.price) - (+a.dataset.price);
        if (sort === "weight") return (+a.dataset.weight) - (+b.dataset.weight);
        return (+a.dataset.power) - (+b.dataset.power);
      });
      if (grid) visible.forEach(function (it) { grid.appendChild(it); });
      if (countEl) countEl.textContent = visible.length + " " + countWord;
      if (emptyEl) emptyEl.hidden = visible.length !== 0;
      if (resetBtn) resetBtn.hidden = !filters.some(function (f) { return f.checked; });
    }

    filters.forEach(function (f) { f.addEventListener("change", apply); });
    if (sortSel) sortSel.addEventListener("change", apply);
    if (resetBtn) resetBtn.addEventListener("click", function () {
      filters.forEach(function (f) { f.checked = false; });
      apply();
    });

    // Mobile: collapse the filter panel by default
    var fpanel = listing.querySelector("[data-filters]");
    if (fpanel && window.matchMedia && window.matchMedia("(max-width: 860px)").matches) {
      fpanel.removeAttribute("open");
    }
  }

  /* --- Cookie consent + consent-gated analytics (GA4 / GTM) --- */
  function loadAnalytics() {
    if (window.__kkAnalyticsLoaded) return;
    var m4 = document.querySelector("meta[name=kk-ga4]");
    var mg = document.querySelector("meta[name=kk-gtm]");
    var ga4 = m4 ? m4.getAttribute("content") : "";
    var gtm = mg ? mg.getAttribute("content") : "";
    if (!ga4 && !gtm) return;
    window.__kkAnalyticsLoaded = true;
    window.dataLayer = window.dataLayer || [];
    if (ga4) {
      var s = document.createElement("script");
      s.async = true; s.src = "https://www.googletagmanager.com/gtag/js?id=" + ga4;
      document.head.appendChild(s);
      window.gtag = function () { window.dataLayer.push(arguments); };
      window.gtag("js", new Date());
      window.gtag("config", ga4, { anonymize_ip: true });
    }
    if (gtm) {
      window.dataLayer.push({ "gtm.start": +new Date(), event: "gtm.js" });
      var g = document.createElement("script");
      g.async = true; g.src = "https://www.googletagmanager.com/gtm.js?id=" + gtm;
      document.head.appendChild(g);
    }
    kkFireEcom();
  }
  try { if (localStorage.getItem("mv-consent") === "all") loadAnalytics(); } catch (e) {}

  /* --- GA4 enhanced e-commerce (consent-gated) --- */
  function kkGa4(name, params) {
    if (window.__kkAnalyticsLoaded && typeof window.gtag === "function") {
      window.gtag("event", name, params || {});
      return true;
    }
    return false;
  }
  // Fire the page's primary e-commerce event (view_item / begin_checkout / purchase)
  function kkFireEcom() {
    if (window.__kkEcomFired) return;
    var el = document.getElementById("kk-ecom");
    if (!el) return;
    var data;
    try { data = JSON.parse(el.textContent); } catch (e) { return; }
    if (data && data.event && kkGa4(data.event, data.params)) { window.__kkEcomFired = true; }
  }
  kkFireEcom();
  // add_to_cart: fire just before the buy form submits
  document.querySelectorAll("[data-cart-form]").forEach(function (form) {
    form.addEventListener("submit", function () {
      var d = form.dataset || {};
      var skuEl = form.querySelector("[data-cart-sku]");
      kkGa4("add_to_cart", {
        currency: "EUR",
        value: parseFloat(d.gaPrice || "0") || 0,
        items: [{
          item_id: (skuEl && skuEl.value) || d.gaId || "",
          item_name: d.gaName || "",
          item_brand: d.gaBrand || "",
          item_category: d.gaCat || "",
          price: parseFloat(d.gaPrice || "0") || 0,
          quantity: 1
        }]
      });
    });
  });

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

  /* --- Register/profile: toggle business fields by account type --- */
  document.querySelectorAll("[data-register]").forEach(function (form) {
    var box = form.querySelector("[data-business-fields]");
    var company = box ? box.querySelector('input[name="company"]') : null;
    var radios = form.querySelectorAll("[data-account-type]");
    function syncType() {
      var sel = form.querySelector("[data-account-type]:checked");
      var business = sel && sel.value === "business";
      if (box) box.hidden = !business;
      if (company) {
        if (business) { company.setAttribute("required", "required"); }
        else { company.removeAttribute("required"); }
      }
    }
    radios.forEach(function (r) { r.addEventListener("change", syncType); });
    if (radios.length) syncType();
  });

  /* --- Address detection: fill city from postal code (zippopotam.us) --- */
  document.querySelectorAll("[data-zip]").forEach(function (zipEl) {
    var scope = zipEl.form || document;
    var cityEl = scope.querySelector("[data-city]");
    if (!cityEl || !window.fetch) return;
    var country = (zipEl.getAttribute("data-zip-country") || "de").toLowerCase();
    var last = "";
    function lookup() {
      var zip = (zipEl.value || "").trim();
      if (zip === last) return;
      if (!/^[0-9]{4,5}$/.test(zip)) return;
      last = zip;
      fetch("https://api.zippopotam.us/" + country + "/" + encodeURIComponent(zip))
        .then(function (r) { return r.ok ? r.json() : null; })
        .then(function (d) {
          if (!d || !d.places || !d.places.length) return;
          var place = d.places[0]["place name"];
          if (place && (cityEl.value.trim() === "" || cityEl.dataset.autofilled === "1")) {
            cityEl.value = place;
            cityEl.dataset.autofilled = "1";
          }
        })
        .catch(function () {});
    }
    zipEl.addEventListener("blur", lookup);
    zipEl.addEventListener("change", lookup);
    cityEl.addEventListener("input", function () { cityEl.dataset.autofilled = "0"; });
  });

  /* --- Account dashboard: tab switching --- */
  var account = document.querySelector("[data-account]");
  if (account) {
    var links = account.querySelectorAll("[data-account-link]");
    var panels = account.querySelectorAll("[data-account-panel]");
    function activate(name) {
      panels.forEach(function (p) { p.classList.toggle("is-active", p.getAttribute("data-account-panel") === name); });
      account.querySelectorAll(".account__navlink[data-account-link]").forEach(function (l) {
        l.classList.toggle("is-active", l.getAttribute("data-account-link") === name);
      });
    }
    links.forEach(function (l) {
      l.addEventListener("click", function () { activate(l.getAttribute("data-account-link")); });
    });
  }

  /* --- Hero product slider --- */
  var hero = document.querySelector("[data-hero]");
  if (hero) {
    var hTrack = hero.querySelector("[data-hero-track]");
    var hSlides = Array.prototype.slice.call(hero.querySelectorAll("[data-hero-slide]"));
    var hDots = Array.prototype.slice.call(hero.querySelectorAll("[data-hero-dot]"));
    var hPrev = hero.querySelector("[data-hero-prev]");
    var hNext = hero.querySelector("[data-hero-next]");
    var hN = hSlides.length, hi = 0, hTimer = null;
    var hReduce = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    function hGo(to) {
      hi = (to + hN) % hN;
      if (hTrack) hTrack.style.transform = "translateX(" + (-hi * 100) + "%)";
      hSlides.forEach(function (s, k) {
        s.classList.toggle("is-active", k === hi);
        if (k === hi) { s.removeAttribute("aria-hidden"); } else { s.setAttribute("aria-hidden", "true"); }
      });
      hDots.forEach(function (d, k) {
        d.classList.toggle("is-active", k === hi);
        if (k === hi) { d.setAttribute("aria-current", "true"); } else { d.removeAttribute("aria-current"); }
      });
    }
    function hStop() { if (hTimer) { clearInterval(hTimer); hTimer = null; } }
    function hStart() { if (hReduce || hN < 2) return; hStop(); hTimer = setInterval(function () { hGo(hi + 1); }, 6000); }

    if (hNext) hNext.addEventListener("click", function () { hGo(hi + 1); hStart(); });
    if (hPrev) hPrev.addEventListener("click", function () { hGo(hi - 1); hStart(); });
    hDots.forEach(function (d) { d.addEventListener("click", function () { hGo(+d.getAttribute("data-hero-dot")); hStart(); }); });
    hero.addEventListener("mouseenter", hStop);
    hero.addEventListener("mouseleave", hStart);
    hero.addEventListener("focusin", hStop);
    hero.addEventListener("focusout", hStart);
    hero.addEventListener("keydown", function (e) {
      if (e.key === "ArrowLeft") { hGo(hi - 1); hStart(); }
      else if (e.key === "ArrowRight") { hGo(hi + 1); hStart(); }
    });
    var hX0 = null;
    hero.addEventListener("touchstart", function (e) { hX0 = e.touches[0].clientX; hStop(); }, { passive: true });
    hero.addEventListener("touchend", function (e) {
      if (hX0 === null) return;
      var dx = e.changedTouches[0].clientX - hX0;
      if (Math.abs(dx) > 40) hGo(hi + (dx < 0 ? 1 : -1));
      hX0 = null; hStart();
    }, { passive: true });
    document.addEventListener("visibilitychange", function () { if (document.hidden) { hStop(); } else { hStart(); } });

    hGo(0); hStart();
  }

  /* --- Search autocomplete --- */
  document.querySelectorAll("[data-search]").forEach(function (form) {
    var input = form.querySelector("[data-search-input]");
    var box = form.querySelector("[data-search-suggest]");
    var catSel = form.querySelector("select[name=cat]");
    if (!input || !box || !window.fetch) return;
    var timer = null, items = [], active = -1, lastQ = "";
    var allLabel = form.getAttribute("data-all") || "Alle Ergebnisse anzeigen";

    function close() { box.hidden = true; box.innerHTML = ""; items = []; active = -1; input.setAttribute("aria-expanded", "false"); }

    function render(results, q) {
      if (!results.length) { close(); return; }
      var html = results.map(function (r, i) {
        var img = r.img ? '<img src="' + r.img + '" alt="">' : "";
        return '<a class="search-sug" href="' + r.url + '" data-i="' + i + '">' + img +
          '<span class="search-sug__b"><span class="search-sug__t">' + r.title + "</span>" +
          '<span class="search-sug__m">' + r.brand + " &middot; " + r.ps + " PS</span></span>" +
          '<span class="search-sug__p">' + r.price + "</span></a>";
      }).join("");
      var allUrl = form.getAttribute("action") + "?q=" + encodeURIComponent(q) + (catSel && catSel.value ? "&cat=" + catSel.value : "");
      html += '<a class="search-sug search-sug--all" href="' + allUrl + '">' + allLabel + "</a>";
      box.innerHTML = html;
      box.hidden = false;
      input.setAttribute("aria-expanded", "true");
      items = Array.prototype.slice.call(box.querySelectorAll(".search-sug"));
      active = -1;
    }

    function query() {
      var q = input.value.trim();
      if (q.length < 2) { close(); return; }
      if (q === lastQ && !box.hidden) return;
      lastQ = q;
      var url = "/search.json?q=" + encodeURIComponent(q) + (catSel && catSel.value ? "&cat=" + catSel.value : "");
      fetch(url, { headers: { Accept: "application/json" } })
        .then(function (r) { return r.json(); })
        .then(function (d) { render(d.results || [], q); })
        .catch(function () {});
    }

    input.addEventListener("input", function () { clearTimeout(timer); timer = setTimeout(query, 180); });
    input.addEventListener("focus", function () { if (input.value.trim().length >= 2) query(); });
    input.addEventListener("keydown", function (e) {
      if (box.hidden) return;
      if (e.key === "ArrowDown" || e.key === "ArrowUp") {
        e.preventDefault();
        active += (e.key === "ArrowDown" ? 1 : -1);
        if (active < 0) active = items.length - 1;
        if (active >= items.length) active = 0;
        items.forEach(function (el, i) { el.classList.toggle("is-active", i === active); });
      } else if (e.key === "Enter") {
        if (active >= 0 && items[active]) { e.preventDefault(); window.location.href = items[active].getAttribute("href"); }
      } else if (e.key === "Escape") { close(); }
    });
    catSel && catSel.addEventListener("change", function () { lastQ = ""; query(); });
    document.addEventListener("click", function (e) { if (!form.contains(e.target)) close(); });
  });

  /* --- Mini-cart drawer (AJAX) --- */
  var mc = document.querySelector("[data-minicart]");
  if (mc && window.fetch) {
    var mcBody = mc.querySelector("[data-minicart-body]");
    var mcFoot = mc.querySelector("[data-minicart-foot]");
    var mcCsrf = (mc.querySelector("[data-minicart-csrf]") || {}).value || "";
    var emptyMsg = mc.getAttribute("data-empty") || "";
    var removeLbl = mc.getAttribute("data-remove") || "Remove";

    function mcEsc(s) { var d = document.createElement("div"); d.textContent = s == null ? "" : s; return d.innerHTML; }
    function mcOpen() { mc.hidden = false; document.body.style.overflow = "hidden"; requestAnimationFrame(function () { mc.classList.add("is-open"); }); }
    function mcClose() { mc.classList.remove("is-open"); document.body.style.overflow = ""; setTimeout(function () { mc.hidden = true; }, 260); }

    function mcRender(d) {
      if (!d) return;
      document.querySelectorAll("[data-cart-count]").forEach(function (b) { b.textContent = d.count; });
      document.querySelectorAll("[data-cart-total]").forEach(function (t) { t.textContent = d.subtotalFormatted; });
      if (!d.items || !d.items.length) {
        mcBody.innerHTML = '<p class="minicart__empty">' + mcEsc(emptyMsg) + "</p>";
        mcFoot.hidden = true;
        return;
      }
      mcBody.innerHTML = d.items.map(function (it) {
        var img = it.img ? '<img src="' + it.img + '" alt="">' : '<span class="minicart-it__ph"></span>';
        return '<div class="minicart-it">' + img +
          '<div class="minicart-it__b"><a href="' + it.url + '" class="minicart-it__t">' + mcEsc(it.title) + "</a>" +
          (it.variant ? '<span class="minicart-it__v">' + mcEsc(it.variant) + "</span>" : "") +
          '<span class="minicart-it__m">' + it.qty + " &times; &middot; " + it.lineFormatted + "</span></div>" +
          '<button class="minicart-it__rm" type="button" data-mc-remove="' + mcEsc(it.sku) + '" aria-label="' + mcEsc(removeLbl) + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg></button></div>';
      }).join("");
      mc.querySelector("[data-minicart-subtotal]").textContent = d.subtotalFormatted;
      var shiprow = mc.querySelector("[data-minicart-shiprow]");
      if (d.shippingFormatted) { mc.querySelector("[data-minicart-shipping]").textContent = d.shippingFormatted; shiprow.hidden = false; }
      else { shiprow.hidden = true; }
      mc.querySelector("[data-minicart-total]").textContent = d.totalFormatted;
      mcFoot.hidden = false;
    }

    mc.querySelectorAll("[data-minicart-close]").forEach(function (b) { b.addEventListener("click", mcClose); });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape" && !mc.hidden) mcClose(); });

    mcBody.addEventListener("click", function (e) {
      var btn = e.target.closest("[data-mc-remove]");
      if (!btn) return;
      fetch("/cart/remove", { method: "POST", headers: { "X-Requested-With": "fetch", "Content-Type": "application/x-www-form-urlencoded" },
        body: "csrf=" + encodeURIComponent(mcCsrf) + "&sku=" + encodeURIComponent(btn.getAttribute("data-mc-remove")) })
        .then(function (r) { return r.json(); }).then(mcRender).catch(function () {});
    });

    document.querySelectorAll(".cart-btn").forEach(function (a) {
      a.addEventListener("click", function (e) {
        e.preventDefault();
        mcOpen();
        fetch("/cart/data.json", { headers: { Accept: "application/json" } })
          .then(function (r) { return r.json(); }).then(mcRender)
          .catch(function () { window.location.href = a.getAttribute("href"); });
      });
    });

    document.querySelectorAll("[data-cart-form]").forEach(function (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        fetch(form.getAttribute("action"), { method: "POST", headers: { "X-Requested-With": "fetch" }, body: new FormData(form) })
          .then(function (r) { return r.json(); })
          .then(function (d) { if (d && d.ok) { mcRender(d); mcOpen(); } else { HTMLFormElement.prototype.submit.call(form); } })
          .catch(function () { HTMLFormElement.prototype.submit.call(form); });
      });
    });
  }

  /* --- Scroll reveal (dezent; respektiert prefers-reduced-motion) --- */
  (function () {
    if (!("IntersectionObserver" in window)) return;
    if (window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    var sel = "[data-reveal], .section__head, .pcard, .feature, .gauge, .ratgeber-card, .guide-card, .step, .featurelist > *";
    var els = [].slice.call(document.querySelectorAll(sel));
    if (!els.length) return;
    var vh = window.innerHeight || document.documentElement.clientHeight;
    var ro = new IntersectionObserver(function (entries, obs) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add("is-in"); obs.unobserve(e.target); }
      });
    }, { rootMargin: "0px 0px -7% 0px", threshold: 0.05 });
    var counts = new Map();
    els.forEach(function (el) {
      el.classList.add("reveal");
      var r = el.getBoundingClientRect();
      // Bereits im Viewport (Above-the-fold): sofort zeigen, kein Flash, keine Animation.
      if (r.top < vh && r.bottom > 0) { el.classList.add("is-in"); return; }
      var p = el.parentNode;
      var i = counts.get(p) || 0;
      if (i > 0 && i < 7) el.style.transitionDelay = (i * 55) + "ms";
      counts.set(p, i + 1);
      ro.observe(el);
    });
  })();

  /* --- Recently viewed (localStorage; rein clientseitig) --- */
  (function () {
    var KEY = "kk_rv";
    function esc(s) {
      return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
        return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
      });
    }
    var list = [];
    try { list = JSON.parse(localStorage.getItem(KEY) || "[]"); } catch (e) { list = []; }
    if (!Array.isArray(list)) list = [];

    var cur = null;
    var curEl = document.querySelector("[data-rv-current]");
    if (curEl) { try { cur = JSON.parse(curEl.textContent); } catch (e) {} }
    if (cur && cur.id) {
      list = list.filter(function (x) { return x && x.id !== cur.id; });
      list.unshift(cur);
      if (list.length > 10) list = list.slice(0, 10);
      try { localStorage.setItem(KEY, JSON.stringify(list)); } catch (e) {}
    }

    var grid = document.querySelector("[data-rv-grid]");
    var section = document.querySelector("[data-rv-section]");
    if (!grid || !section) return;
    var others = list.filter(function (x) { return x && x.url && (!cur || x.id !== cur.id); }).slice(0, 4);
    if (!others.length) return;
    grid.innerHTML = others.map(function (x) {
      var media = x.img ? '<img src="' + esc(x.img) + '" alt="" loading="lazy">' : "";
      return '<a class="rv-card" href="' + esc(x.url) + '">' +
        '<span class="rv-card__media">' + media + "</span>" +
        '<span class="rv-card__title">' + esc(x.title) + "</span>" +
        '<span class="rv-card__price">' + esc(x.price) + "</span></a>";
    }).join("");
    section.hidden = false;
  })();
})();
