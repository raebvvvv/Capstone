// Modal API wrapper: use Bootstrap when present; fallback otherwise
const ModalApi = (() => {
  function cleanup() {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('padding-right');
  }
  function backdrop() {
    const bd = document.createElement('div');
    bd.className = 'modal-backdrop fade show';
    document.body.appendChild(bd);
    return bd;
  }
  function show(el) {
    try {
      if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
        new bootstrap.Modal(el).show();
        return;
      }
    } catch (_) {}
    // fallback
    el.style.display = 'block';
    el.removeAttribute('aria-hidden');
    el.setAttribute('aria-modal', 'true');
    el.classList.add('show');
    backdrop();
    document.body.classList.add('modal-open');
    el.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
      btn.addEventListener('click', () => hide(el), { once: true });
    });
  }
  function hide(el) {
    try {
      if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
        const inst = bootstrap.Modal.getInstance(el);
        if (inst) { inst.hide(); return; }
      }
    } catch (_) {}
    el.classList.remove('show');
    el.style.display = 'none';
    el.setAttribute('aria-hidden', 'true');
    cleanup();
  }
  return { show, hide };
})();

function initDownloadSummary() {
	const openBtn = document.getElementById('openDownloadSummaryModal');
	const modalEl = document.getElementById('downloadSummaryModal');
	const confirmBtn = document.getElementById('confirmDownloadSummaryBtn');
	const headerCloseBtn = modalEl ? modalEl.querySelector('[data-bs-dismiss="modal"]') : null;

	function cleanupModalArtifacts() {
		// Remove any stray Bootstrap modal backdrops and reset body state
		document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
		document.body.classList.remove('modal-open');
		document.body.style.removeProperty('overflow');
		document.body.style.removeProperty('padding-right');
	}

	// If the trigger already uses Bootstrap data attributes, let Bootstrap handle opening.
	const usesDataApi = openBtn && openBtn.hasAttribute('data-bs-toggle');
	if (openBtn && modalEl && !usesDataApi) {
		openBtn.addEventListener('click', (e) => {
			e.preventDefault();
			ModalApi.show(modalEl);
		});
	}

	if (confirmBtn) {
		confirmBtn.addEventListener('click', () => {
			const selected = document.querySelector('input[name="summaryType"]:checked');
			const type = selected ? selected.value : 'national';
			const url = `../admin/download_summary.php?type=${encodeURIComponent(type)}`;
			window.location.href = url;
		});
	}

		// When modal fully hides, ensure any lingering backdrops are cleaned up
		if (modalEl) {
			modalEl.addEventListener('hidden.bs.modal', () => {
				// Allow Bootstrap to finish cleanup first, then ensure no leftovers
				setTimeout(cleanupModalArtifacts, 50);
			});
		}

		// Also hook the header close as a safety (in case events were wired oddly)
		if (headerCloseBtn) {
			headerCloseBtn.addEventListener('click', () => {
				setTimeout(cleanupModalArtifacts, 100);
			});
		}
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initDownloadSummary);
} else {
	initDownloadSummary();
}


// --- Filters/Search logic (no eval, CSP-safe) ---
function initCompletedAppsFilters() {
	const list = document.getElementById('ipappList');
	if (!list) return; // page not present

	const items = Array.from(list.querySelectorAll('.ipapp-list-item'));
	const searchInput = document.getElementById('ipappSearch');
	const searchBtn = document.getElementById('ipappSearchBtn');
	const allTimeBtn = document.getElementById('allTimeBtn');
	const allTimeMenu = document.getElementById('allTimeDropdownMenu');
	const calendarSection = document.getElementById('calendarSection');
	const startDateEl = document.getElementById('startDate');
	const endDateEl = document.getElementById('endDate');
	const othersBtn = document.getElementById('othersBtn');
	const filtersBar = document.getElementById('filtersBar');

	// Dependent dropdowns: College -> Program mapping
	const COLLEGE_PROGRAMS = {
		CAF: [
			'Bachelor of Science in Accountancy (BSA)',
			'Bachelor of Science in Management Accounting (BSMA)',
			'BSBA Major in Financial Management (BSBAFM)'
		],
		CADBE: [
			'Bachelor of Science in Architecture (BS-ARCH)',
			'Bachelor of Science in Interior Design (BSID)',
			'Bachelor of Science in Environmental Planning (BSEP)'
		],
		CAL: [
			'BA in English Language Studies (ABELS)',
			'Bachelor of Arts in Filipinology (ABF)',
			'BA in Literary and Cultural Studies (ABLCS)',
			'Bachelor of Arts in Philosophy (AB-PHILO)',
			'Bachelor of Performing Arts major in Theater Arts (BPEA)'
		],
		CBA: [
			'Doctor in Business Administration (DBA)',
			'Master in Business Administration (MBA)',
			'BSBA major in Human Resource Management (BSBAHRM)',
			'BSBA major in Marketing Management (BSBA-MM)',
			'Bachelor of Science in Entrepreneurship (BSENTREP)',
			'Bachelor of Science in Office Administration (BSOA)'
		],
		COC: [
			'Bachelor in Advertising and Public Relations (BADPR)',
			'Bachelor of Arts in Broadcasting',
			'Bachelor of Arts in Communication Research (BACR)',
			'Bachelor of Arts in Journalism (BAJ)'
		],
		CCIS: [
			'Bachelor of Science in Computer Science (BSCS)',
			'Bachelor of Science in Information Technology (BSIT)'
		],
		COED: [
			'Doctor of Philosophy in Education Management (PhDEM)',
			'Master of Arts in Education Management (MAEM)',
			'Master in Business Education (MBE)',
			'Master in Library and Information Science (MLIS)',
			'MA in English Language Teaching (MAELT)',
			'MA in Education major in Mathematics Education (MAEd-ME)',
			'MA in Physical Education and Sports (MAPES)',
			'MA in Education major in Teaching in the Challenged Areas (MAED-TCA)',
			'Post-Baccalaureate Diploma in Education (PBDE)',
			'Bachelor of Technology and Livelihood Education (BTLEd)',
			'Bachelor of Library and Information Science (BLIS)',
			'Bachelor of Secondary Education (BSEd)',
			'Bachelor of Elementary Education (BEEd)',
			'Bachelor of Early Childhood Education (BECEd)'
		],
		CE: [
			'Bachelor of Science in Civil Engineering (BSCE)',
			'Bachelor of Science in Computer Engineering (BSCpE)',
			'Bachelor of Science in Electrical Engineering (BSEE)',
			'Bachelor of Science in Electronics Engineering (BSECE)',
			'Bachelor of Science in Industrial Engineering (BSIE)',
			'Bachelor of Science in Mechanical Engineering (BSME)',
			'Bachelor of Science in Railway Engineering (BSRE)'
		],
		CHK: [
			'Bachelor of Physical Education (BPE)',
			'Bachelor of Science in Exercises and Sports (BSESS)'
		],
		CL: [ 'Juris Doctor (JD)' ],
		CPSPA: [
			'Doctor in Public Administration (DPA)',
			'Master in Public Administration (MPA)',
			'Bachelor of Public Administration (BPA)',
			'Bachelor of Arts in International Studies (BAIS)',
			'Bachelor of Arts in Political Economy (BAPE)',
			'Bachelor of Arts in Political Science (BAPS)'
		],
		CSSD: [
			'Bachelor of Arts in History (BAH)',
			'Bachelor of Arts in Sociology (BAS)',
			'Bachelor of Science in Cooperatives (BSC)',
			'Bachelor of Science in Economics (BSE)',
			'Bachelor of Science in Psychology (BSPSY)'
		],
		CS: [
			'Bachelor of Science in Food Technology (BSFT)',
			'Bachelor of Science in Applied Mathematics (BSAPMATH)',
			'Bachelor of Science in Biology (BSBIO)',
			'Bachelor of Science in Chemistry (BSCHEM)',
			'Bachelor of Science in Mathematics (BSMATH)',
			'Bachelor of Science in Nutrition and Dietetics (BSND)',
			'Bachelor of Science in Physics (BSPHY)',
			'Bachelor of Science in Statistics (BSSTAT)'
		],
		CTHTM: [
			'Bachelor of Science in Hospitality Management (BSHM)',
			'Bachelor of Science in Tourism Management (BSTM)',
			'Bachelor of Science in Transportation Management (BSTRM)'
		]
	};

	// Departments per College
	const COLLEGE_DEPARTMENTS = {
		CBA: [
			'Department of Marketing Management',
			'Department of Human Resource Management',
			'Department of Office Administration',
			'Department of Entrepreneurship'
		],
		CCIS: [
			'Department of Computer Science',
			'Department of Information Technology'
		]
	};

	const collegeBtn = filtersBar ? filtersBar.querySelector('.ipapp-mini-btn[data-target="collegeMenu"]') : null;
	const programBtn = filtersBar ? filtersBar.querySelector('.ipapp-mini-btn[data-target="programMenu"]') : null;
	const departmentBtn = filtersBar ? filtersBar.querySelector('.ipapp-mini-btn[data-target="departmentMenu"]') : null;
	const collegeMenu = document.getElementById('collegeMenu');
	const programMenu = document.getElementById('programMenu');
	const departmentMenu = document.getElementById('departmentMenu');

	let selectedCollegeCode = 'All';

	function getAllPrograms() {
		const set = new Set();
		Object.values(COLLEGE_PROGRAMS).forEach(arr => arr.forEach(p => set.add(p)));
		return Array.from(set);
	}

	function getAllDepartments() {
		const set = new Set();
		Object.values(COLLEGE_DEPARTMENTS).forEach(arr => arr.forEach(d => set.add(d)));
		return Array.from(set);
	}

	function setBtnLabel(btn, baseLabel, valueLabel) {
		if (!btn) return;
		const label = valueLabel ? `${baseLabel}: ${valueLabel}` : baseLabel;
		btn.innerHTML = `${label}<span>▼</span>`;
	}

	function buildProgramMenu(programs) {
		if (!programMenu) return;
		const items = [`<button class="dropdown-item" type="button">All</button>`]
			.concat(programs.map(p => `<button class="dropdown-item" type="button">${p}</button>`))
			.join('');
		programMenu.innerHTML = items;
	}

	function buildDepartmentMenu(departments) {
		if (!departmentMenu) return;
		const items = [`<button class="dropdown-item" type="button">All</button>`]
			.concat(departments.map(d => `<button class="dropdown-item" type="button">${d}</button>`))
			.join('');
		departmentMenu.innerHTML = items;
	}

	function parseCollegeCode(text) {
		if (!text) return 'All';
		const idx = text.indexOf(' - ');
		if (idx > -1) return text.slice(0, idx).trim();
		return text.trim();
	}

	function onCollegeSelected(labelText) {
		selectedCollegeCode = parseCollegeCode(labelText);
		const programs = selectedCollegeCode === 'All' ? getAllPrograms() : (COLLEGE_PROGRAMS[selectedCollegeCode] || []);
		buildProgramMenu(programs);
		setBtnLabel(collegeBtn, 'College', selectedCollegeCode === 'All' ? '' : selectedCollegeCode);
		setBtnLabel(programBtn, 'Program', 'All');

		// Rebuild Department menu based on selected college
		const departments = selectedCollegeCode === 'All' ? getAllDepartments() : (COLLEGE_DEPARTMENTS[selectedCollegeCode] || []);
		if (departmentMenu) buildDepartmentMenu(departments);
		setBtnLabel(departmentBtn, 'Department', 'All');
	}

	let selectedRange = 'all';
	let customRange = { start: null, end: null };

	function parseIsoOrFallbackDate(el) {
		// Prefer ISO date from embedded details JSON if present
		try {
			const descLink = el.querySelector('.ipapp-desc-link');
			if (descLink && descLink.dataset && descLink.dataset.details) {
				const decoded = JSON.parse(atob(descLink.dataset.details));
				const doc = decoded && decoded.document;
				const iso = (doc && (doc.applicationDate || doc.dateAccomplished)) || null;
				if (iso) {
					const d = new Date(iso);
					if (!Number.isNaN(d.getTime())) return d;
				}
			}
		} catch (_) { /* ignore */ }
		// Fallback: try to parse data-date like "May 2025" as first of month
		const dateText = (el.getAttribute('data-date') || '').trim();
		if (dateText) {
			const d = new Date(`${dateText} 1`);
			if (!Number.isNaN(d.getTime())) return d;
		}
		return null;
	}

	function isInSelectedRange(d) {
		if (!(d instanceof Date) || Number.isNaN(d.getTime())) return true; // no date means don't filter it out
		const now = new Date();
		if (selectedRange === 'today') {
			const a = new Date(now.getFullYear(), now.getMonth(), now.getDate());
			const b = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
			return d >= a && d < b;
		}
		if (selectedRange === 'thismonth') {
			const a = new Date(now.getFullYear(), now.getMonth(), 1);
			const b = new Date(now.getFullYear(), now.getMonth() + 1, 1);
			return d >= a && d < b;
		}
		if (selectedRange === 'thisyear') {
			const a = new Date(now.getFullYear(), 0, 1);
			const b = new Date(now.getFullYear() + 1, 0, 1);
			return d >= a && d < b;
		}
		if (selectedRange === 'custom' && customRange.start && customRange.end) {
			const a = new Date(customRange.start.getFullYear(), customRange.start.getMonth(), customRange.start.getDate());
			const b = new Date(customRange.end.getFullYear(), customRange.end.getMonth(), customRange.end.getDate() + 1);
			return d >= a && d < b;
		}
		return true; // 'all'
	}

	function matchesQuery(el, q) {
		if (!q) return true;
		const desc = (el.getAttribute('data-description') || '').toLowerCase();
		const name = (el.getAttribute('data-name') || '').toLowerCase();
		const date = (el.getAttribute('data-date') || '').toLowerCase();
		return desc.includes(q) || name.includes(q) || date.includes(q);
	}

	function updateListVisibility() {
		const q = (searchInput && searchInput.value || '').trim().toLowerCase();
		items.forEach(el => {
			const d = parseIsoOrFallbackDate(el);
			const ok = matchesQuery(el, q) && isInSelectedRange(d);
			el.style.display = ok ? '' : 'none';
		});
	}

	// Search
	if (searchInput) {
		searchInput.addEventListener('input', updateListVisibility);
	}
	if (searchBtn) {
		searchBtn.addEventListener('click', (e) => {
			e.preventDefault();
			updateListVisibility();
		});
	}

	// All time dropdown
	function closeAllTimeMenu() {
		if (allTimeMenu) {
			allTimeMenu.classList.remove('menu-active');
		}
		if (allTimeBtn) allTimeBtn.setAttribute('aria-expanded', 'false');
	}
	if (allTimeBtn && allTimeMenu) {
		allTimeBtn.addEventListener('click', (e) => {
			e.stopPropagation();
			const open = allTimeMenu.classList.toggle('menu-active');
			allTimeBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
		allTimeMenu.addEventListener('click', (e) => {
			const btn = e.target.closest('.dropdown-item');
			if (!btn) return;
			const range = btn.getAttribute('data-range');
			if (!range) return;
			selectedRange = range;
			if (range === 'custom') {
				if (calendarSection) calendarSection.style.display = 'flex';
			} else {
				if (calendarSection) calendarSection.style.display = 'none';
				// Update button label nicely
				const label = {
					all: 'All time',
					today: 'Today',
					thismonth: 'This Month',
					thisyear: 'This Year'
				}[range] || 'All time';
				allTimeBtn.textContent = label + ' ▼';
				allTimeBtn.title = label;
				closeAllTimeMenu();
				updateListVisibility();
			}
		});
		document.addEventListener('click', (e) => {
			if (!allTimeMenu.contains(e.target) && e.target !== allTimeBtn) {
				closeAllTimeMenu();
			}
		});
	}

	// Custom date Apply button
	if (calendarSection) {
		const applyBtn = calendarSection.querySelector('button, .ipapp-apply-btn');
		if (applyBtn) {
			applyBtn.addEventListener('click', () => {
				const s = startDateEl && startDateEl.value ? new Date(startDateEl.value) : null;
				const e = endDateEl && endDateEl.value ? new Date(endDateEl.value) : null;
				if (s && e && !Number.isNaN(s) && !Number.isNaN(e)) {
					customRange = { start: s, end: e };
					selectedRange = 'custom';
					// Format: MMM d, yyyy – MMM d, yyyy
					const fmt = (d) => d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
					const label = `${fmt(s)} – ${fmt(e)}`;
					if (allTimeBtn) {
						allTimeBtn.textContent = label + ' ▼';
						allTimeBtn.title = label;
					}
					updateListVisibility();
				}
			});
		}
	}

	// Others -> show/hide filters bar
	if (othersBtn && filtersBar) {
		othersBtn.addEventListener('click', (e) => {
			e.stopPropagation();
			const visible = filtersBar.style.display === 'flex' || filtersBar.style.display === '';
			filtersBar.style.display = visible ? 'none' : 'flex';
		});
		document.addEventListener('click', (e) => {
			if (!filtersBar.contains(e.target) && e.target !== othersBtn) {
				// Do not forcibly hide; keep state unless click was outside and it was open
				// Uncomment to auto-close when clicking outside:
				// if (filtersBar.style.display !== 'none') filtersBar.style.display = 'none';
			}
		});
	}

	// Mini dropdowns within filters bar
	if (filtersBar) {
		const miniBtns = Array.from(filtersBar.querySelectorAll('.ipapp-mini-btn'));
		function closeAllMini() {
			Array.from(filtersBar.querySelectorAll('.ipapp-mini-menu')).forEach(m => m.classList.remove('menu-active'));
		}
		miniBtns.forEach(btn => {
			btn.addEventListener('click', (e) => {
				e.stopPropagation();
				const targetId = btn.getAttribute('data-target');
				if (!targetId) return;
				const menu = document.getElementById(targetId);
				if (!menu) return;
				const willOpen = !menu.classList.contains('menu-active');
				closeAllMini();
				if (willOpen) menu.classList.add('menu-active');
			});
		});
		document.addEventListener('click', () => closeAllMini());

		// Selection within college/program menus
		if (collegeMenu) {
			collegeMenu.addEventListener('click', (e) => {
				const item = e.target.closest('.dropdown-item');
				if (!item) return;
				onCollegeSelected(item.textContent.trim());
				collegeMenu.classList.remove('menu-active');
			});
		}
		if (programMenu) {
			programMenu.addEventListener('click', (e) => {
				const item = e.target.closest('.dropdown-item');
				if (!item) return;
				setBtnLabel(programBtn, 'Program', item.textContent.trim() === 'All' ? 'All' : item.textContent.trim());
				programMenu.classList.remove('menu-active');
			});
		}

		if (departmentMenu) {
			departmentMenu.addEventListener('click', (e) => {
				const item = e.target.closest('.dropdown-item');
				if (!item) return;
				setBtnLabel(departmentBtn, 'Department', item.textContent.trim() === 'All' ? 'All' : item.textContent.trim());
				departmentMenu.classList.remove('menu-active');
			});
		}

		// Hook Go button to apply filters (currently only search/date affect items in this dataset)
		const goBtn = filtersBar.querySelector('.ipapp-go-btn');
		if (goBtn) {
			goBtn.addEventListener('click', (e) => {
				e.preventDefault();
				updateListVisibility();
			});
		}
	}

	// Handle clicking application description to open Details modal
	document.addEventListener('click', (e) => {
		const link = e.target.closest('.ipapp-desc-link');
		if (!link) return;
		e.preventDefault();
		const detailsAttr = link.getAttribute('data-details');
		if (!detailsAttr) return;
		try {
			const details = JSON.parse(atob(detailsAttr));
			const body = document.getElementById('gmodalBody');
			if (!body) return;
			const s = details.student || {};
			const d = details.document || {};
			const files = Array.isArray(details.files) ? details.files : [];
			const fileRows = files.map(f => `<li class="d-flex justify-content-between align-items-center mb-2"><span>${f.label||''}</span><div class="d-flex gap-2"><a class="btn btn-sm btn-download" href="${f.url||'#'}" download>Download File</a><a class="btn btn-sm btn-view-file" href="${f.url||'#'}" target="_blank" rel="noopener">View File</a></div></li>`).join('');
			body.innerHTML = `
				<div>
					<h5>Student Information</h5>
					<p><strong>Name:</strong> ${s.name||'—'}</p>
					<p><strong>Student Number:</strong> ${s.number||'—'}</p>
					<p><strong>Email Address:</strong> ${s.email||'—'}</p>
					<p><strong>Home Address:</strong> ${s.homeAddress||'—'}</p>
					<p><strong>Campus:</strong> ${s.campus||'—'}</p>
					<p><strong>Department:</strong> ${s.department||'—'}</p>
					<p><strong>College:</strong> ${s.college||'—'}</p>
					<p><strong>Program:</strong> ${s.program||'—'}</p>
				</div>
				<div class="mt-3">
					<h5>Document Information</h5>
					<p><strong>Title:</strong> ${d.title||'—'}</p>
					<p><strong>Author/s Full name/s:</strong> ${d.author||s.name||'—'}</p>
					<p><strong>Date Accomplished:</strong> ${d.dateAccomplished||'—'}</p>
				</div>
				<div class="mt-3">
					<h5>Uploaded Files</h5>
					<ul class="list-unstyled mb-0">${fileRows}</ul>
				</div>`;
			const el = document.getElementById('detailsGModal');
			if (el) ModalApi.show(el);

			// Wire the View Certificate button for this selection
			const certBtn = document.getElementById('gViewCertBtn');
			if (certBtn) {
				const certUrl = details.certificateUrl || '#';
				certBtn.onclick = () => {
					const cEl = document.getElementById('certificateModalCA');
					const dl = document.getElementById('downloadCertificateBtnCA');
					if (dl) {
						if (certUrl && certUrl !== '#') { dl.href = certUrl; dl.style.display = ''; dl.setAttribute('download','certificate.pdf'); }
						else { dl.removeAttribute('href'); dl.style.display = 'none'; dl.removeAttribute('download'); }
					}
					if (cEl) ModalApi.show(cEl);
				};
			}
		} catch (_) { /* ignore malformed data */ }
	});

	// Initial render
	updateListVisibility();

	// Initialize dependent dropdowns with full list
	if (programMenu) {
		buildProgramMenu(getAllPrograms());
	}
	if (departmentMenu) {
		buildDepartmentMenu(getAllDepartments());
	}
	setBtnLabel(collegeBtn, 'College', '');
	setBtnLabel(programBtn, 'Program', 'All');
	setBtnLabel(departmentBtn, 'Department', 'All');
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initCompletedAppsFilters);
} else {
	initCompletedAppsFilters();
}

