/* Case dashboard — summary cards + bar chart, filterable by state.
   Reads window.LEAP_CASES (shared with the about-page map). */
(function () {
  'use strict';

  var DATA = window.LEAP_CASES || [];
  var root = document.getElementById('case-dash');
  if (!root || !DATA.length) return;

  var filtersEl = root.querySelector('#cd-filters');
  var barsEl    = root.querySelector('#cd-bars');
  var elTotal   = root.querySelector('#cd-total');
  var elLoc     = root.querySelector('#cd-locations');
  var elTop     = root.querySelector('#cd-top');
  var elTopSub  = root.querySelector('#cd-top-sub');
  var elAvg     = root.querySelector('#cd-avg');
  var elAvgLbl  = root.querySelector('#cd-avg-label');

  var fmt = function (n) { return n.toLocaleString(); };

  // Totals per state, ordered by volume.
  var stateTotals = {};
  DATA.forEach(function (d) { stateTotals[d.state] = (stateTotals[d.state] || 0) + d.cases; });
  var states = Object.keys(stateTotals).sort(function (a, b) { return stateTotals[b] - stateTotals[a]; });

  var current = 'ALL';

  // Filter buttons.
  filtersEl.innerHTML = ['ALL'].concat(states).map(function (s) {
    return '<button class="case-dash__filter' + (s === 'ALL' ? ' is-active' : '') +
      '" data-state="' + s + '">' + (s === 'ALL' ? 'All states' : s) + '</button>';
  }).join('');

  filtersEl.addEventListener('click', function (e) {
    var b = e.target.closest('.case-dash__filter');
    if (!b) return;
    current = b.dataset.state;
    filtersEl.querySelectorAll('.case-dash__filter').forEach(function (x) {
      x.classList.toggle('is-active', x === b);
    });
    render();
  });

  function render() {
    var rows  = current === 'ALL' ? DATA : DATA.filter(function (d) { return d.state === current; });
    var total = rows.reduce(function (a, d) { return a + d.cases; }, 0);
    var top   = rows.slice().sort(function (a, b) { return b.cases - a.cases; })[0];

    elTotal.textContent = fmt(total);
    elLoc.textContent   = fmt(rows.length);
    elTop.textContent   = top ? top.city : '—';
    elTopSub.textContent = top
      ? fmt(top.cases) + ' cases' + (current === 'ALL' ? ' · ' + top.state : '')
      : 'Top location';

    if (current === 'ALL') {
      elAvgLbl.textContent = 'States';
      elAvg.textContent    = states.length;
    } else {
      elAvgLbl.textContent = 'Avg / location';
      elAvg.textContent    = fmt(Math.round(total / Math.max(rows.length, 1)));
    }

    // Bars: by state when "All", else top 10 cities in the state.
    var bars = current === 'ALL'
      ? states.map(function (s) { return { label: s, value: stateTotals[s], color: '#00d2be' }; })
      : rows.slice().sort(function (a, b) { return b.cases - a.cases; }).slice(0, 10)
          .map(function (d) { return { label: d.city, value: d.cases, color: d.color }; });

    var max = bars.reduce(function (m, b) { return Math.max(m, b.value); }, 1);

    barsEl.innerHTML = bars.map(function (b) {
      var pct = Math.max(2, Math.round(b.value / max * 100));
      return '<div class="cd-bar">' +
        '<span class="cd-bar__label">' + b.label + '</span>' +
        '<span class="cd-bar__track"><span class="cd-bar__fill" data-w="' + pct + '" style="background:' + b.color + '"></span></span>' +
        '<span class="cd-bar__val">' + fmt(b.value) + '</span>' +
        '</div>';
    }).join('');

    // Animate fills from 0 → target width.
    requestAnimationFrame(function () {
      barsEl.querySelectorAll('.cd-bar__fill').forEach(function (f) {
        f.style.width = f.getAttribute('data-w') + '%';
      });
    });
  }

  render();
})();
