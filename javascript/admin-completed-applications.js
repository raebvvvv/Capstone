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

// --- Filters/Search logic (no eval, CSP-safe) ---
function initCompletedAppsFilters() {
	const list = document.getElementById('ipappList');
	if (!list) return; // page not present

	const items = Array.from(list.querySelectorAll('.ipapp-list-item'));
	// Pagination state
	const paginationEl = document.getElementById('ipappPagination');
	const PAGE_SIZE = 10;
	let currentPage = 1;
	const searchInput = document.getElementById('ipappSearch');
	const searchBtn = document.getElementById('ipappSearchBtn');
	const allTimeBtn = document.getElementById('allTimeBtn');
	const allTimeMenu = document.getElementById('allTimeDropdownMenu');
	const calendarSection = document.getElementById('calendarSection');
	const startDateEl = document.getElementById('startDate');
	const endDateEl = document.getElementById('endDate');
	const othersBtn = document.getElementById('othersBtn');
	const filtersBar = document.getElementById('filtersBar');
	const dashboardLink = document.querySelector('a.nav-link[href="admin.php"], a.nav-link[href="./admin.php"], a.nav-link[href="/admin/admin.php"]');
	const downloadBtn = document.getElementById('confirmDownloadSummaryBtn');
	const openDownloadModalBtn = document.getElementById('openDownloadSummaryModal');

	// Dependent dropdowns: College -> Program mapping
	// Catalogs-driven data containers (populated at runtime)
	const Catalogs = {
		levels: [], // [{name, code}]
		campuses: [], // [{name, code}]
		colleges: [], // [{id, name, code, campus_id}]
		departmentsByCollege: {}, // codeLower -> [department names]
		programsByCollege: {} // codeLower -> [program names]
	};

	// Departments per College
	// Fetch catalogs (admin endpoint)
	const CAT_API = (window.CATALOGS_API_URL || 'catalogs_api.php');
	async function fetchCatalogs() {
		async function req(entity) {
			const url = `${CAT_API}?action=list&entity=${encodeURIComponent(entity)}`;
			const r = await fetch(url, { credentials: 'same-origin' });
			if (!r.ok) throw new Error(`HTTP ${r.status}`);
			const d = await r.json();
			if (!d || d.ok !== true || !Array.isArray(d.data)) throw new Error('Bad response');
			return d.data;
		}
		try {
			const [levels, campuses, colleges, departments, programs] = await Promise.all([
				req('level'), req('campus'), req('college'), req('department'), req('program')
			]);
			Catalogs.levels = levels.map(x => ({ name: x.name, code: x.code || x.name })).sort((a,b)=> (a.name||'').localeCompare(b.name||''));
			Catalogs.campuses = campuses.map(x => ({ name: x.name, code: x.code || x.name })).sort((a,b)=> (a.name||'').localeCompare(b.name||''));
			Catalogs.colleges = colleges.map(x => ({ id: x.id, name: x.name, code: x.code || x.name, campus_id: x.campus_id })).sort((a,b)=> (a.name||'').localeCompare(b.name||''));
			const idToCode = Object.fromEntries(Catalogs.colleges.map(c => [String(c.id), (c.code||'').toString()]));
			Catalogs.departmentsByCollege = {};
			(departments||[]).forEach(d => {
				const code = (idToCode[String(d.college_id)] || '').toLowerCase();
				if (!code) return;
				if (!Catalogs.departmentsByCollege[code]) Catalogs.departmentsByCollege[code] = [];
				Catalogs.departmentsByCollege[code].push(d.name);
			});
			Catalogs.programsByCollege = {};
			(programs||[]).forEach(p => {
				const code = (idToCode[String(p.college_id)] || '').toLowerCase();
				if (!code) return;
				if (!Catalogs.programsByCollege[code]) Catalogs.programsByCollege[code] = [];
				Catalogs.programsByCollege[code].push(p.name);
			});
		} catch (e) {
			// Leave Catalogs empty; fallbacks will kick in
		}
	}

	// Map short college codes used in UI to full names used by academicData.program keys
	const CODE_TO_COLLEGE_FULL = {
		'CAF': 'College of Accountancy and Finance (CAF)',
		'CADBE': 'College of Architecture, Design and the Built Environment (CADBE)',
		'CAL': 'College of Arts and Letters (CAL)',
		'CBA': 'College of Business Administration (CBA)',
		'COC': 'College of Communication (COC)',
		'CCIS': 'College of Computer and Information Sciences (CCIS)',
		'COED': 'College of Education (COED)',
		'CE': 'College of Engineering (CE)',
		'CHK': 'College of Human Kinetics (CHK)',
		'CL': 'College of Law (CL)',
		'CPSPA': 'College of Political Science and Public Administration (CPSPA)',
		'CSSD': 'College of Social Sciences and Development (CSSD)',
		'CS': 'College of Science (CS)',
		'CTHTM': 'College of Tourism, Hospitality and Transportation Management (CTHTM)'
		// Note: Institute of Technology isn't in the College menu; add if needed
	};

	// Build union of all programs from catalogs
	function getAllPrograms() {
		const set = new Set();
		if (Catalogs && Catalogs.programsByCollege) {
			Object.values(Catalogs.programsByCollege).forEach(arr => (arr||[]).forEach(p => set.add(p)));
		}
		return Array.from(set);
	}

	const acadLevelBtn = filtersBar ? filtersBar.querySelector('.ipapp-mini-btn[data-target="acadLevelMenu"]') : null;
	const collegeBtn = filtersBar ? filtersBar.querySelector('.ipapp-mini-btn[data-target="collegeMenu"]') : null;
	const programBtn = filtersBar ? filtersBar.querySelector('.ipapp-mini-btn[data-target="programMenu"]') : null;
	const campusBtn = filtersBar ? filtersBar.querySelector('.ipapp-mini-btn[data-target="campusMenu"]') : null;
	const typesBtn = filtersBar ? filtersBar.querySelector('.ipapp-mini-btn[data-target="typesMenu"]') : null;
	const groupBtn = filtersBar ? filtersBar.querySelector('.ipapp-mini-btn[data-target="groupMenu"]') : null;
	// Department filter (added after College)
	const departmentBtn = filtersBar ? filtersBar.querySelector('.ipapp-mini-btn[data-target="departmentMenu"]') : null;
	const campusMenu = document.getElementById('campusMenu');
	const acadLevelMenu = document.getElementById('acadLevelMenu');
	const collegeMenu = document.getElementById('collegeMenu');
	const programMenu = document.getElementById('programMenu');
	const departmentMenu = document.getElementById('departmentMenu');
	const typesMenu = document.getElementById('typesMenu');
	const groupMenu = document.getElementById('groupMenu');

	let selectedAcademicLevel = 'All';
	let selectedCollegeCode = 'All';
	let selectedProgram = 'All';
	let selectedDepartment = 'All';
	let selectedCampus = 'All';
	let selectedType = 'All';
	let selectedGroup = 'All';

	// (removed duplicate getAllPrograms using static maps)

	// Utility: normalize text for matching
	function norm(text){ return (text||'').toLowerCase().replace(/\s+/g,' ').trim(); }

	// Build a mapping of lowercase program name -> display (proper case) per college
	// Display map for programs: lowercase -> original label
	function programDisplayMapFor(code){
		const entries = (code && code !== 'ALL' && Catalogs.programsByCollege[code.toLowerCase()]) ? Catalogs.programsByCollege[code.toLowerCase()] : getAllPrograms();
		const m = {};
		(entries || []).forEach(name => { m[norm(name)] = name; });
		return m;
	}

	function getAllDepartments() {
		const all = new Set();
		Object.values(Catalogs.departmentsByCollege || {}).forEach(arr => (arr||[]).forEach(d => all.add(d)));
		return Array.from(all).sort((a,b)=> (a||'').localeCompare(b||''));
	}

	function setBtnLabel(btn, baseLabel, valueLabel) {
		if (!btn) return;
		const label = valueLabel ? `${baseLabel}: ${valueLabel}` : baseLabel;
		btn.innerHTML = `${label}<span>▼</span>`;
	}

	function buildProgramMenuFromValues(programValues, collegeCode){
		if(!programMenu) return;
		const code = (!collegeCode || collegeCode==='All') ? 'ALL' : (collegeCode||'');
		const dict = programDisplayMapFor(code);
		// Fallbacks: if derived values are empty, try static list for selected college, then ALL union
		let values = Array.isArray(programValues) ? programValues.slice() : [];
		if (values.length === 0) { values = (code !== 'ALL') ? (Catalogs.programsByCollege[code.toLowerCase()] || []) : getAllPrograms(); }
		if (values.length === 0) { values = getAllPrograms(); }
		// Dedupe and sort by display label
		const seen = new Set();
		values = values.filter(v=>{
			const key = norm(v);
			if (seen.has(key)) return false;
			seen.add(key);
			return true;
		}).sort((a,b)=> (a||'').localeCompare(b||''));
		const items = [ `<button class="dropdown-item" type="button" data-value="All">All</button>` ]
			.concat(values.map(v=>{
				const val = norm(v);
				const disp = dict[val] || (v || '');
				return `<button class="dropdown-item" type="button" data-value="${val}">${disp}</button>`;
			}))
			.join('');
		programMenu.innerHTML = items;
	}

	// Helpers to get program lists
	function programsForUndergradCollege(code){
		// Using catalogs: return programs for the given college code; if All/empty, return union
		if (!code || code==='All') return getAllPrograms();
		const arr = Catalogs.programsByCollege[code.toLowerCase()] || [];
		return arr.slice();
	}

	// Derive program list from actual items on the page, filtered by college code if provided
	function collectProgramsFor(collegeCode){
		const set = new Set();
		Array.from(document.querySelectorAll('#ipappList .ipapp-list-item')).forEach(el=>{
			const code = (el.getAttribute('data-college-code')||'').trim();
			if(!collegeCode || collegeCode==='All' || code === (collegeCode||'').toLowerCase()){
				const prog = (el.getAttribute('data-program')||'').trim();
				if(prog) set.add(prog.replace(/\s+/g,' ').trim());
			}
		});
		return Array.from(set).sort((a,b)=> a.localeCompare(b));
	}

	function buildDepartmentMenu(departments) {
		if (!departmentMenu) return;
		const items = [ '<button class="dropdown-item" type="button">All</button>' ]
			.concat((departments||[]).map(d => `<button class="dropdown-item" type="button" data-value="${norm(d)}">${d}</button>`));
		departmentMenu.innerHTML = items.join('');
	}

	function parseCollegeCode(text) {
		if (!text) return 'All';
		const idx = text.indexOf(' - ');
		if (idx > -1) return text.slice(0, idx).trim();
		return text.trim();
	}

	function onCollegeSelected(labelText) {
		selectedCollegeCode = parseCollegeCode(labelText);
		// If graduate/Open University is selected, keep college locked
		const gradLevels = ['masters','doctorate','open university'];
		if (gradLevels.includes((selectedAcademicLevel||'').toLowerCase())) {
			setBtnLabel(collegeBtn, 'College', 'N/A');
			return;
		}
		const programs = programsForUndergradCollege(selectedCollegeCode);
		buildProgramMenuFromValues(programs, selectedCollegeCode);
		setBtnLabel(collegeBtn, 'College', selectedCollegeCode === 'All' ? '' : selectedCollegeCode);
		setBtnLabel(programBtn, 'Program', 'All');
		selectedProgram = 'All';

		// Rebuild Department menu based on selected college using catalogs
		if (departmentMenu) {
			let deps = [];
			if (selectedCollegeCode && selectedCollegeCode !== 'All' && selectedCollegeCode !== 'N/A') {
				deps = (Catalogs.departmentsByCollege[selectedCollegeCode.toLowerCase()] || []).slice();
			} else {
				deps = getAllDepartments();
			}
			buildDepartmentMenu(deps);
			selectedDepartment = 'All';
			setBtnLabel(departmentBtn, 'Department', '');
		}
		updateListVisibility();
	}

		function onAcademicLevelSelected(labelText){
			selectedAcademicLevel = (labelText || 'All').trim();
			setBtnLabel(acadLevelBtn, 'Academic Level', selectedAcademicLevel === 'All' ? '' : selectedAcademicLevel);
			// Constraint: Masters/Doctorate/Open University -> College becomes N/A and disabled; Program pulled from academicData.program[level]
			const gradLevels = ['masters','doctorate','open university'];
			const isGrad = gradLevels.includes((selectedAcademicLevel||'').toLowerCase());
			if (isGrad) {
				// Force College to N/A (display); build program list using catalogs union
				selectedCollegeCode = 'N/A';
				setBtnLabel(collegeBtn, 'College', 'N/A');
				const values = programsForUndergradCollege('All');
				buildProgramMenuFromValues(values, 'ALL');
				// Reset selected program
				selectedProgram = 'All';
				setBtnLabel(programBtn, 'Program', 'All');
			} else {
				// Undergraduate: enable College dropdown semantics; repopulate Program based on College
				if (selectedCollegeCode === 'N/A') selectedCollegeCode = 'All';
				setBtnLabel(collegeBtn, 'College', selectedCollegeCode === 'All' ? '' : selectedCollegeCode);
				buildProgramMenuFromValues(programsForUndergradCollege(selectedCollegeCode), selectedCollegeCode);
			}
			updateListVisibility();
		}

	let selectedRange = 'all';
	let customRange = { start: null, end: null };

	function parseIsoOrFallbackDate(el) {
		// 1) Explicit ISO attribute from server
		const isoAttr = (el.getAttribute('data-application-date') || '').trim();
		if (isoAttr) {
			const d = new Date(isoAttr);
			if (!Number.isNaN(d.getTime())) return d;
		}
		// 2) Embedded details JSON (back-compat)
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
		// 3) Fallback: parse human month label (month granularity only)
		const dateText = (el.getAttribute('data-date') || '').trim();
		if (dateText) {
			const d = new Date(`${dateText} 1`);
			if (!Number.isNaN(d.getTime())) return d;
		}
		return null;
	}

	function isInSelectedRange(d) {
		// If a specific range is chosen and we cannot parse a date, exclude the item.
		const requiresDate = selectedRange !== 'all';
		if (!(d instanceof Date) || Number.isNaN(d.getTime())) return requiresDate ? false : true;
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

	function matchesFilters(el){
		// Academic Level
		if (selectedAcademicLevel !== 'All'){
			const lvl = (el.getAttribute('data-academic-level')||'').trim();
			if (lvl !== selectedAcademicLevel.toLowerCase()) return false;
		}
		// College: ignore when grad/open levels are selected or selection is explicitly N/A
		const isGradLevel = ['masters','doctorate','open university'].includes((selectedAcademicLevel||'').toLowerCase());
		if (!isGradLevel && selectedCollegeCode !== 'All' && selectedCollegeCode !== 'N/A') {
			const code = (el.getAttribute('data-college-code')||'').trim();
			if (code !== selectedCollegeCode.toLowerCase()) return false;
		}
		// Program
		if (selectedProgram !== 'All') {
			const prog = (el.getAttribute('data-program')||'').trim();
			if (prog !== (selectedProgram||'').toLowerCase()) return false;
		}
		// Department
		if (selectedDepartment !== 'All') {
			const dep = norm(el.getAttribute('data-department')||'');
			if (dep !== (selectedDepartment||'').toLowerCase()) return false;
		}
		// Campus (substring match)
		if (selectedCampus !== 'All') {
			const campus = (el.getAttribute('data-campus')||'').trim();
			if (!campus.includes(selectedCampus.toLowerCase())) return false;
		}
		// Types
		if (selectedType !== 'All') {
			const t = (el.getAttribute('data-type')||'').trim();
			if (t !== selectedType.toLowerCase()) return false;
		}
		// Group
		if (selectedGroup !== 'All') {
			const g = (el.getAttribute('data-group')||'').trim();
			if (g !== selectedGroup.toLowerCase()) return false;
		}
		return true;
	}

	function renderPagination(total, page) {
		if (!paginationEl) return;
		const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
		if (totalPages <= 1) {
			paginationEl.innerHTML = '';
			paginationEl.style.display = 'none';
			return;
		}
		paginationEl.style.display = '';
		const clamped = Math.min(Math.max(page, 1), totalPages);
		const windowSize = 5;
		let start = Math.max(1, clamped - Math.floor(windowSize / 2));
		let end = Math.min(totalPages, start + windowSize - 1);
		start = Math.max(1, Math.min(start, end - windowSize + 1));

		function pageBtn(label, targetPage, disabled=false, active=false) {
			const btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'btn btn-sm ' + (active ? 'btn-primary' : 'btn-outline-secondary');
			btn.textContent = label;
			btn.disabled = !!disabled;
			btn.style.margin = '0 4px';
			if (!disabled && !active) {
				btn.addEventListener('click', () => {
					currentPage = targetPage;
					applyPagination();
				});
			}
			return btn;
		}

		paginationEl.innerHTML = '';
		const wrap = document.createElement('div');
		wrap.className = 'd-flex align-items-center';
		wrap.appendChild(pageBtn('Prev', clamped - 1, clamped <= 1));

		// Leading first/ellipsis
		if (start > 1) {
			wrap.appendChild(pageBtn('1', 1, false, clamped === 1));
			if (start > 2) {
				const span = document.createElement('span');
				span.className = 'mx-1 text-muted';
				span.textContent = '…';
				wrap.appendChild(span);
			}
		}
		for (let p = start; p <= end; p++) {
			wrap.appendChild(pageBtn(String(p), p, false, p === clamped));
		}
		// Trailing ellipsis/last
		if (end < totalPages) {
			if (end < totalPages - 1) {
				const span = document.createElement('span');
				span.className = 'mx-1 text-muted';
				span.textContent = '…';
				wrap.appendChild(span);
			}
			wrap.appendChild(pageBtn(String(totalPages), totalPages, false, clamped === totalPages));
		}
		wrap.appendChild(pageBtn('Next', clamped + 1, clamped >= totalPages));
		paginationEl.appendChild(wrap);
	}

	function applyPagination() {
		// Determine matched items from prior filter step
		const matched = items.filter(el => el.dataset && el.dataset.match === '1');
		const total = matched.length;
		const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
		if (currentPage > totalPages) currentPage = totalPages;
		if (currentPage < 1) currentPage = 1;
		const startIdx = (currentPage - 1) * PAGE_SIZE;
		const endIdx = startIdx + PAGE_SIZE;
		// Hide all matched initially; unmatched already hidden in filter step
		matched.forEach((el, idx) => {
			el.style.display = (idx >= startIdx && idx < endIdx) ? '' : 'none';
		});
		renderPagination(total, currentPage);
	}

	function updateListVisibility() {
		// Reset to first page whenever filters/search/range change
		currentPage = 1;
		const q = (searchInput && searchInput.value || '').trim().toLowerCase();
		items.forEach(el => {
			const d = parseIsoOrFallbackDate(el);
			const ok = matchesQuery(el, q) && isInSelectedRange(d) && matchesFilters(el);
			// Mark match result; unmatched are hidden now, matched visibility decided by pagination
			if (ok) {
				el.dataset.match = '1';
			} else {
				el.dataset.match = '0';
				el.style.display = 'none';
			}
		});
		applyPagination();
		updateDashboardLink();
	}

	function updateDashboardLink(){
		if (!dashboardLink) return;
		const params = new URLSearchParams();
		// Always scope to completed applications when jumping to dashboard
		params.set('status','completed');
		// College/program/campus/type/group
		if (selectedCollegeCode && selectedCollegeCode !== 'All') params.set('college', selectedCollegeCode);
		if (selectedProgram && selectedProgram !== 'All') params.set('program', selectedProgram);
		if (selectedCampus && selectedCampus !== 'All') params.set('campus', selectedCampus);
		if (selectedType && selectedType !== 'All') params.set('type', selectedType);
		if (selectedGroup && selectedGroup !== 'All') params.set('group', selectedGroup);
		// Date range
		const now = new Date();
		function fmt(d){ const y=d.getFullYear(); const m=String(d.getMonth()+1).padStart(2,'0'); const day=String(d.getDate()).padStart(2,'0'); return `${y}-${m}-${day}`; }
		if (selectedRange === 'today'){
			const a = new Date(now.getFullYear(), now.getMonth(), now.getDate());
			const b = new Date(now.getFullYear(), now.getMonth(), now.getDate());
			params.set('start', fmt(a)); params.set('end', fmt(b));
		} else if (selectedRange === 'thismonth'){
			const a = new Date(now.getFullYear(), now.getMonth(), 1);
			const b = new Date(now.getFullYear(), now.getMonth()+1, 0);
			params.set('start', fmt(a)); params.set('end', fmt(b));
		} else if (selectedRange === 'thisyear'){
			const a = new Date(now.getFullYear(), 0, 1);
			const b = new Date(now.getFullYear(), 11, 31);
			params.set('start', fmt(a)); params.set('end', fmt(b));
		} else if (selectedRange === 'custom' && customRange.start && customRange.end){
			params.set('start', fmt(customRange.start));
			params.set('end', fmt(customRange.end));
		}
		const base = 'admin.php';
		dashboardLink.href = base + '?' + params.toString();
	}

	// Build Download Summary URL with current filters/date and open
	function openDownloadSummary(){
		const params = new URLSearchParams();
		// summary: national|rmipo from radio (avoid clashing with 'type' filter)
		let t = 'national';
		try {
			const sel = document.querySelector('input[name="summaryType"]:checked');
			if (sel) t = sel.value;
		} catch(_){}
		params.set('summary', t);
		// filters to mirror dashboard link
		if (selectedAcademicLevel && selectedAcademicLevel !== 'All') params.set('level', selectedAcademicLevel);
		if (selectedCollegeCode && selectedCollegeCode !== 'All') params.set('college', selectedCollegeCode);
		if (selectedProgram && selectedProgram !== 'All') params.set('program', selectedProgram);
		if (selectedCampus && selectedCampus !== 'All') params.set('campus', selectedCampus);
		if (selectedType && selectedType !== 'All') params.set('type', selectedType);
		if (selectedGroup && selectedGroup !== 'All') params.set('group', selectedGroup);
		// date range
		const now = new Date();
		function fmt(d){ const y=d.getFullYear(); const m=String(d.getMonth()+1).padStart(2,'0'); const day=String(d.getDate()).padStart(2,'0'); return `${y}-${m}-${day}`; }
		if (selectedRange === 'today'){
			const a = new Date(now.getFullYear(), now.getMonth(), now.getDate());
			const b = new Date(now.getFullYear(), now.getMonth(), now.getDate());
			params.set('start', fmt(a)); params.set('end', fmt(b));
		} else if (selectedRange === 'thismonth'){
			const a = new Date(now.getFullYear(), now.getMonth(), 1);
			const b = new Date(now.getFullYear(), now.getMonth()+1, 0);
			params.set('start', fmt(a)); params.set('end', fmt(b));
		} else if (selectedRange === 'thisyear'){
			const a = new Date(now.getFullYear(), 0, 1);
			const b = new Date(now.getFullYear(), 11, 31);
			params.set('start', fmt(a)); params.set('end', fmt(b));
		} else if (selectedRange === 'custom' && customRange.start && customRange.end){
			params.set('start', fmt(customRange.start));
			params.set('end', fmt(customRange.end));
		}
		const url = `download_summary.php?${params.toString()}`;
		window.open(url, '_blank');
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

	// Download Summary: confirm button opens CSV with current selections
	if (downloadBtn) {
		downloadBtn.addEventListener('click', (e)=>{
			e.preventDefault();
			try { openDownloadSummary(); } catch(_) {}
		});
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

		// Academic Level
		if (acadLevelMenu) {
			acadLevelMenu.addEventListener('click', (e) => {
				const item = e.target.closest('.dropdown-item');
				if (!item) return;
				const label = item.textContent.trim();
				onAcademicLevelSelected(label);
				acadLevelMenu.classList.remove('menu-active');
			});
		}

		// Selection within college/program menus
		if (collegeMenu) {
			collegeMenu.addEventListener('click', (e) => {
				const item = e.target.closest('.dropdown-item');
				if (!item) return;
				// If current level is Masters/Doctorate/Open University, ignore college changes
				const gradLevels = ['masters','doctorate','open university'];
				if (gradLevels.includes((selectedAcademicLevel||'').toLowerCase())) {
					// lock at N/A, do nothing
					return;
				}
				onCollegeSelected(item.textContent.trim());
				collegeMenu.classList.remove('menu-active');
			});
		}
		if (programMenu) {
			programMenu.addEventListener('click', (e) => {
				const item = e.target.closest('.dropdown-item');
				if (!item) return;
				const value = item.getAttribute('data-value') || 'All';
				const label = item.textContent.trim();
				setBtnLabel(programBtn, 'Program', label === 'All' ? 'All' : label);
				selectedProgram = value;
				updateListVisibility();
				programMenu.classList.remove('menu-active');
			});
		}

		// Department
		if (departmentMenu) {
			departmentMenu.addEventListener('click', (e) => {
				const item = e.target.closest('.dropdown-item');
				if (!item) return;
				const label = item.textContent.trim();
				const val = (item.getAttribute('data-value') || norm(label) || 'all');
				selectedDepartment = (label === 'All') ? 'All' : val;
				setBtnLabel(departmentBtn, 'Department', label === 'All' ? '' : label);
				updateListVisibility();
				departmentMenu.classList.remove('menu-active');
			});
		}

		// Campus
		if (campusMenu) {
			campusMenu.addEventListener('click', (e) => {
				const item = e.target.closest('.dropdown-item');
				if (!item) return;
				const label = item.textContent.trim();
				selectedCampus = label;
				setBtnLabel(campusBtn, 'Campus', label === 'All' ? '' : label);
				updateListVisibility();
				campusMenu.classList.remove('menu-active');
			});
		}

		// Types
		if (typesMenu) {
			typesMenu.addEventListener('click', (e) => {
				const item = e.target.closest('.dropdown-item');
				if (!item) return;
				const label = item.textContent.trim();
				selectedType = label;
				setBtnLabel(typesBtn, 'Types', label === 'All' ? '' : label);
				updateListVisibility();
				typesMenu.classList.remove('menu-active');
			});
		}

		// Group
		if (groupMenu) {
			groupMenu.addEventListener('click', (e) => {
				const item = e.target.closest('.dropdown-item');
				if (!item) return;
				const label = item.textContent.trim();
				selectedGroup = label;
				setBtnLabel(groupBtn, 'Group', label === 'All' ? '' : label);
				updateListVisibility();
				groupMenu.classList.remove('menu-active');
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
			const shared = {
				studentName: s.name || '',
				studentNumber: s.number || '',
				email: s.email || '',
				homeAddress: s.homeAddress || '',
				campus: s.campus || '',
				college: s.college || '',
				department: s.department || '',
				program: s.program || '',
				academicLevel: s.academicLevel || '',
				documentTitle: d.title || '',
				workClassification: d.workClassification || '',
				accomplishmentDate: d.dateAccomplished || '',
				files_list: Array.isArray(details.files) ? details.files.map(f=>({ label:f.label, url:f.url, size:null, verified:null })) : []
			};
			// Include authors and adviser in the shared payload if present
			try {
				const add = Array.isArray(details.additionalAuthors) ? details.additionalAuthors : [];
				shared.additionalAuthors = add.map(a => ({
					name: (a && a.name) ? a.name : '',
					is_adviser: (a && (a.is_adviser === 1 || a.is_adviser === true || a.is_adviser === '1')) ? 1 : 0,
					studentNumber: (a && a.studentNumber) ? a.studentNumber : '',
					email: (a && a.email) ? a.email : '',
					address: (a && a.address) ? a.address : '',
					phone: (a && a.phone) ? a.phone : ''
				}));
				shared.adviser = (typeof details.adviser === 'string') ? details.adviser : (details.adviser || '');
			} catch(_) { /* non-fatal */ }
			// Determine if submitter is an employee to adjust ID label in modal
			const itemEl = link.closest('.ipapp-list-item');
			const groupAttr = itemEl ? (itemEl.getAttribute('data-group')||'').trim().toLowerCase() : '';
			const isEmployee = (groupAttr === 'employee') || ((s.academicLevel||'').toString().toLowerCase().includes('employee'));
			if (typeof window.renderSubmissionDetails === 'function') {
				window.renderSubmissionDetails(body, shared, { role: isEmployee ? 'employee' : 'admin' });
			} else {
				body.textContent = 'Details failed to render.';
			}
			const el = document.getElementById('detailsGModal');
			if (el) ModalApi.show(el);

			// Wire the View Certificate button for this selection (embed PDF like ticket page)
			const certBtn = document.getElementById('gViewCertBtn');
			if (certBtn) {
				const certUrlBase = details.certificateUrl || '#';
				const requestId = (details.request_id || details.document?.requestId || '').trim();
				certBtn.onclick = () => {
					const modal = document.getElementById('certificateModalCA');
					if (!modal) return;
					const body = modal.querySelector('.modal-body');
					if (body) body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
					// Build iframe URL. If certUrlBase already has query params append debug/override later if needed
					const viewUrl = certUrlBase === '#' ? '#' : certUrlBase + (certUrlBase.includes('?') ? '' : '');
					if (certUrlBase === '#') {
						if (body) body.innerHTML = '<div class="text-danger">Certificate unavailable.</div>';
					} else {
						const iframeId = 'certCA_' + Date.now();
						const html = `<div class="certificate-preview"><iframe id="${iframeId}" src="${viewUrl}" width="100%" height="500" style="border:none;" loading="lazy" referrerpolicy="no-referrer"></iframe><div class="small text-muted mt-2" id="${iframeId}_status">Loading certificate...</div></div>`;
						if (body) body.innerHTML = html;
						const dl = document.getElementById('downloadCertificateBtnCA');
						if (dl) {
							dl.href = viewUrl + (viewUrl.includes('?') ? '&' : '?') + 'mode=download';
							dl.style.display = '';
							dl.setAttribute('download','certificate.pdf');
						}
						setTimeout(() => {
							const iframe = document.getElementById(iframeId);
							const statusEl = document.getElementById(iframeId + '_status');
							if (!iframe) return;
							let loaded = false;
							try {
								if (iframe.contentDocument || iframe.contentWindow?.document) { loaded = true; }
							} catch (_) {}
							if(!loaded) {
								if (statusEl) statusEl.innerHTML = 'Embedded preview blocked. <a href="'+viewUrl+'" target="_blank" rel="noopener">Open in new tab</a>.';
							} else if (statusEl) {
								statusEl.textContent = '';
							}
						}, 1200);
					}
					ModalApi.show(modal);
				};
			}
		} catch (_) { /* ignore malformed data */ }
	});

	// Comments UI is disabled on Completed Applications; no-op handler retained to avoid errors if legacy elements exist
	document.addEventListener('click', (e) => {
		const cLink = e.target.closest('.ipapp-comments-link');
		if (!cLink) return;
		e.preventDefault();
		// Intentionally do nothing; comments view removed per requirements
	});

	// Initial render
	updateListVisibility();
	updateDashboardLink();

	// Listen for cross-tab submission updates to reflect edits without manual refresh
	try {
		window.addEventListener('storage', (e) => {
			if (e.key !== 'ipmo:lastUpdate' || !e.newValue) return;
			let msg = null;
			try { msg = JSON.parse(e.newValue); } catch(_) {}
			if (!msg || msg.kind !== 'submission-updated') return;
			const rid = (msg.requestId || '').trim();
			if (!rid) return;
			const item = document.querySelector(`.ipapp-list-item[data-request-id="${CSS.escape(rid)}"]`);
			if (!item) return;
			const payload = msg.payload || {};
			// Update title/description
			if (payload.documentTitle) {
				const a = item.querySelector('.ipapp-desc-link');
				if (a) a.textContent = payload.documentTitle;
				item.setAttribute('data-description', (payload.documentTitle || '').toLowerCase());
			}
			// Update student name
			if (payload.studentName) {
				const ud = item.querySelector('.ipapp-userdate .ipapp-user-link');
				if (ud) {
					const cur = ud.textContent || '';
					const parts = cur.split(',');
					const datePart = parts.length > 1 ? parts.slice(1).join(',') : '';
					ud.textContent = payload.studentName + (datePart ? ',' + datePart : '');
				}
				item.setAttribute('data-name', (payload.studentName || '').toLowerCase());
			}
			// Update program/college meta
			if (payload.program) item.setAttribute('data-program', (payload.program || '').toLowerCase());
			if (payload.college) {
				item.setAttribute('data-college', (payload.college || '').toLowerCase());
				// Derive college code when possible (prefix before ' - ')
				const code = payload.college.includes(' - ') ? payload.college.split(' - ')[0] : payload.college;
				item.setAttribute('data-college-code', (code || '').toLowerCase());
			}
			// Keep embedded details JSON in sync so the modal shows updated values
			try {
				const link = item.querySelector('.ipapp-desc-link');
				if (link && link.dataset && link.dataset.details) {
					const obj = JSON.parse(atob(link.dataset.details));
					if (payload.documentTitle) obj.document = Object.assign({}, obj.document, { title: payload.documentTitle });
					if (payload.studentName) {
						obj.student = Object.assign({}, obj.student, { name: payload.studentName });
						if (obj.document && !obj.document.author) obj.document.author = payload.studentName;
					}
					if (payload.program) obj.student = Object.assign({}, obj.student, { program: payload.program });
					if (payload.college) obj.student = Object.assign({}, obj.student, { college: payload.college });
					link.dataset.details = btoa(JSON.stringify(obj));
				}
			} catch(_) {}
			updateListVisibility();
		});
	} catch(_) {}

	// Deep-link: if ?code= is present, highlight and open that item
	try {
		const params = new URLSearchParams(window.location.search);
		const code = params.get('code');
		if (code) {
			const target = items.find(el => (el.getAttribute('data-request-id') || '').trim() === code.trim());
			if (target) {
				// scroll into view and flash highlight
				target.scrollIntoView({ behavior: 'smooth', block: 'center' });
				target.classList.add('bg-warning-subtle');
				setTimeout(() => target.classList.remove('bg-warning-subtle'), 2000);
				const link = target.querySelector('.ipapp-desc-link');
				if (link) {
					const ev = new MouseEvent('click', { bubbles: true, cancelable: true });
					link.dispatchEvent(ev);
				}
			}
		}
	} catch (_) {}

	// Initialize dependent dropdowns with full list
	// Populate menus from catalogs, then initialize labels
	(async () => {
		await fetchCatalogs();
		// Academic Level
		if (acadLevelMenu) {
			const levels = (Catalogs.levels || []).map(l => l.name);
			acadLevelMenu.innerHTML = ['All'].concat(levels).map(lbl => `<button class="dropdown-item" type="button">${lbl}</button>`).join('');
		}
		// Campus
		if (campusMenu) {
			const campuses = (Catalogs.campuses || []).map(c => c.name);
			campusMenu.innerHTML = ['All'].concat(campuses).map(lbl => `<button class="dropdown-item" type="button">${lbl}</button>`).join('');
		}
		// College
		if (collegeMenu) {
			const colleges = (Catalogs.colleges || []).map(c => ({ code: c.code, label: `${c.code} - ${c.name}` }));
			const items = [{code:'All', label:'All'}].concat(colleges);
			collegeMenu.innerHTML = items.map(it => `<button class="dropdown-item" type="button" data-code="${it.code}">${it.label}</button>`).join('');
		}
		// Program/Department initial (All)
		if (programMenu) buildProgramMenuFromValues(getAllPrograms(), 'ALL');
		if (departmentMenu) buildDepartmentMenu(getAllDepartments());
		setBtnLabel(collegeBtn, 'College', '');
		setBtnLabel(programBtn, 'Program', 'All');
		setBtnLabel(campusBtn, 'Campus', '');
		setBtnLabel(typesBtn, 'Types', '');
		setBtnLabel(groupBtn, 'Group', '');
		setBtnLabel(departmentBtn, 'Department', '');
	})();
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initCompletedAppsFilters);
} else {
	initCompletedAppsFilters();
};

