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

	// Safe accessor for academicData defined in forms/academic-dropdowns.js
	function getAD(){
		try { if (typeof academicData !== 'undefined') return academicData; } catch(_) {}
		return (window.academicData || null);
	}

	const acadLevelBtn = filtersBar ? filtersBar.querySelector('.ipapp-mini-btn[data-target="acadLevelMenu"]') : null;
	const collegeBtn = filtersBar ? filtersBar.querySelector('.ipapp-mini-btn[data-target="collegeMenu"]') : null;
	const programBtn = filtersBar ? filtersBar.querySelector('.ipapp-mini-btn[data-target="programMenu"]') : null;
	// Department filter removed from UI
	const departmentBtn = null;
	const campusMenu = document.getElementById('campusMenu');
	const acadLevelMenu = document.getElementById('acadLevelMenu');
	const collegeMenu = document.getElementById('collegeMenu');
	const programMenu = document.getElementById('programMenu');
	const departmentMenu = null;
	const typesMenu = document.getElementById('typesMenu');
	const groupMenu = document.getElementById('groupMenu');

	let selectedAcademicLevel = 'All';
	let selectedCollegeCode = 'All';
	let selectedProgram = 'All';
	let selectedDepartment = 'All';
	let selectedCampus = 'All';
	let selectedType = 'All';
	let selectedGroup = 'All';

	function getAllPrograms() {
		const set = new Set();
		Object.values(COLLEGE_PROGRAMS).forEach(arr => arr.forEach(p => set.add(p)));
		return Array.from(set);
	}

	// Utility: normalize text for matching
	function norm(text){ return (text||'').toLowerCase().replace(/\s+/g,' ').trim(); }

	// Build a mapping of lowercase program name -> display (proper case) per college
	const PROGRAM_DISPLAY_MAP = (()=>{
		const map = {};
		Object.keys(COLLEGE_PROGRAMS).forEach(code=>{
			const entries = COLLEGE_PROGRAMS[code] || [];
			const m = {};
			entries.forEach(name=>{ m[norm(name)] = name; });
			map[code] = m;
		});
		// Also create an 'ALL' union map for fallback display across colleges
		const all = {};
		Object.values(map).forEach(m=>{ Object.keys(m).forEach(k=>{ if(!(k in all)) all[k]=m[k]; }); });
		map.ALL = all;
		return map;
	})();

	function getAllDepartments() { return []; }

	function setBtnLabel(btn, baseLabel, valueLabel) {
		if (!btn) return;
		const label = valueLabel ? `${baseLabel}: ${valueLabel}` : baseLabel;
		btn.innerHTML = `${label}<span>▼</span>`;
	}

	function buildProgramMenuFromValues(programValues, collegeCode){
		if(!programMenu) return;
		const code = (!collegeCode || collegeCode==='All') ? 'ALL' : (collegeCode||'');
		const dict = PROGRAM_DISPLAY_MAP[code] || PROGRAM_DISPLAY_MAP.ALL || {};
		// Fallbacks: if derived values are empty, try static list for selected college, then ALL union
		let values = Array.isArray(programValues) ? programValues.slice() : [];
		if (values.length === 0) {
			const staticList = (code !== 'ALL' && COLLEGE_PROGRAMS[code]) ? COLLEGE_PROGRAMS[code] : [];
			values = staticList.slice();
		}
		if (values.length === 0) {
			const all = Object.values(PROGRAM_DISPLAY_MAP.ALL || {});
			values = all.slice();
		}
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

	// Helpers to get program lists from academicData based on selection
	function listUndergradCollegeKeys(){
		const ad = getAD();
		const data = (ad && ad.program) || {};
		return Object.keys(data).filter(k => !['Masters','Doctorate','Open University','default'].includes(k));
	}
	function programsForUndergradCollege(code){
		const ad = getAD();
		const data = (ad && ad.program) || {};
		if(!data || Object.keys(data).length===0){ return []; }
		if(!code || code==='All'){
			// Union of all undergrad colleges
			const keys = listUndergradCollegeKeys();
			const set = new Set();
			keys.forEach(k=> (data[k]||[]).forEach(p=> set.add(p)) );
			return Array.from(set);
		}
		const full = CODE_TO_COLLEGE_FULL[code] || null;
		if(full && data[full]) return data[full].slice();
		// Fallback: collect from dataset on the page
		return collectProgramsFor(code);
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

	function buildDepartmentMenu(departments) { /* no-op */ }

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

		// Rebuild Department menu based on selected college
	// Department filter removed
		updateListVisibility();
	}

		function onAcademicLevelSelected(labelText){
			selectedAcademicLevel = (labelText || 'All').trim();
			setBtnLabel(acadLevelBtn, 'Academic Level', selectedAcademicLevel === 'All' ? '' : selectedAcademicLevel);
			// Constraint: Masters/Doctorate/Open University -> College becomes N/A and disabled; Program pulled from academicData.program[level]
			const gradLevels = ['masters','doctorate','open university'];
			const isGrad = gradLevels.includes((selectedAcademicLevel||'').toLowerCase());
			if (isGrad) {
				// Force College to N/A (display) and program list from academicData
				selectedCollegeCode = 'N/A';
				setBtnLabel(collegeBtn, 'College', 'N/A');
				// Build program menu
				const ad = getAD();
				const list = (ad && ad.program && ad.program[selectedAcademicLevel]) ? ad.program[selectedAcademicLevel] : [];
				// Fall back to undergrad union from academicData (proper case) if list empty
				const values = (list && list.length) ? list : programsForUndergradCollege('All');
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
		// College
		if (selectedCollegeCode !== 'All') {
			const code = (el.getAttribute('data-college-code')||'').trim();
			if (code !== selectedCollegeCode.toLowerCase()) return false;
		}
		// Program
		if (selectedProgram !== 'All') {
			const prog = (el.getAttribute('data-program')||'').trim();
			if (prog !== (selectedProgram||'').toLowerCase()) return false;
		}
		// Department filter removed
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

	function updateListVisibility() {
		const q = (searchInput && searchInput.value || '').trim().toLowerCase();
		items.forEach(el => {
			const d = parseIsoOrFallbackDate(el);
			const ok = matchesQuery(el, q) && isInSelectedRange(d) && matchesFilters(el);
			el.style.display = ok ? '' : 'none';
		});
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

		// Department menu removed

		// Campus
		if (campusMenu) {
			campusMenu.addEventListener('click', (e) => {
				const item = e.target.closest('.dropdown-item');
				if (!item) return;
				const label = item.textContent.trim();
				selectedCampus = label;
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
			// Pretty format for Date Approved (applicationDate is ISO yyyy-mm-dd)
			let approvedPretty = '—';
			try {
				if (d.applicationDate) {
					const dt = new Date(d.applicationDate);
					if (!Number.isNaN(dt.getTime())) {
						approvedPretty = dt.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
					}
				}
			} catch(_) {}
			const files = Array.isArray(details.files) ? details.files : [];
			const fileRows = files.map(f => {
				const exists = !!f.exists;
				const href = exists ? (f.url || '#') : '#';
				const name = f.name || '';
				const label = f.label || name || '';
				const nameNote = name ? ` <small class="text-muted">(${name})</small>` : '';
				const missingNote = exists ? '' : ` <small class="text-danger">(missing)</small>`;
				const downloadAttr = exists && name ? `download="${name}"` : (exists ? 'download' : '');
				const dlBtn = exists ? `<a class="btn btn-sm btn-download" href="${href}" ${downloadAttr}>Download File</a>` : `<button class="btn btn-sm btn-download" disabled>Download File</button>`;
				const viewBtn = exists ? `<a class="btn btn-sm btn-view-file" href="${href}" target="_blank" rel="noopener">View File</a>` : `<button class="btn btn-sm btn-view-file" disabled>View File</button>`;
				return `<li class="d-flex justify-content-between align-items-center mb-2">
					<span>${label}${nameNote}${missingNote}</span>
					<div class="d-flex gap-2">
						${dlBtn}
						${viewBtn}
					</div>
				</li>`;
			}).join('');
			body.innerHTML = `
				<div>
					<h5>Student Information</h5>
					<p><strong>Name:</strong> ${s.name||'—'}</p>
					<p><strong>Student Number:</strong> ${s.number||'—'}</p>
					<p><strong>Email Address:</strong> ${s.email||'—'}</p>
					<p><strong>Home Address:</strong> ${s.homeAddress||'—'}</p>
					<p><strong>Campus:</strong> ${s.campus||'—'}</p>
                    
					<p><strong>College:</strong> ${s.college||'—'}</p>
					<p><strong>Program:</strong> ${s.program||'—'}</p>
					<p><strong>Academic Level:</strong> ${s.academicLevel||'—'}</p>
				</div>
				<div class="mt-3">
					<h5>Document Information</h5>
					<p><strong>Title:</strong> ${d.title||'—'}</p>
					<p><strong>Type (Work Classification):</strong> ${d.workClassification||'—'}</p>
					<p><strong>Date Approved:</strong> ${approvedPretty}</p>
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
	updateDashboardLink();

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
	if (programMenu) {
		// Initialize with all undergraduate programs from academicData for better accuracy
		buildProgramMenuFromValues(programsForUndergradCollege('All'), 'All');
	}
	if (departmentMenu) {
		buildDepartmentMenu(getAllDepartments());
	}
	setBtnLabel(collegeBtn, 'College', '');
	setBtnLabel(programBtn, 'Program', 'All');
	// Department button removed
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initCompletedAppsFilters);
} else {
	initCompletedAppsFilters();
}

