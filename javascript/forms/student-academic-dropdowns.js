// Academic Program Data for PUP (fallbacks if API unavailable)
const academicData = {
  campus: [
    "PUP Main (Sta. Mesa, Manila)"
  ],
  
  academicLevel: [
    "Undergraduate",
    "Masters",
    "Doctorate",
    "Open University"
  ],
  
  college: [
    "College of Accountancy and Finance (CAF)",
    "College of Architecture, Design and the Built Environment (CADBE)",
    "College of Arts and Letters (CAL)",
    "College of Business Administration (CBA)",
    "College of Communication (COC)",
    "College of Computer and Information Sciences (CCIS)",
    "College of Education (COED)",
    "College of Engineering (CE)",
    "College of Human Kinetics (CHK)",
    "College of Law (CL)",
    "College of Political Science and Public Administration (CPSPA)",
    "College of Social Sciences and Development (CSSD)",
    "College of Science (CS)",
    "College of Tourism, Hospitality and Transportation Management (CTHTM)",
    "Institute of Technology"
  ],
  
  department: {
    "College of Accountancy and Finance (CAF)": [
      "Department of Accountancy",
      "Department of Finance and Economics",
      "Department of Management Accounting"
    ],
    "College of Computer and Information Sciences (CCIS)": [
      "Department of Computer Science",
      "Department of Information Technology"
    ],
    "College of Engineering (CE)": [
      "Department of Civil Engineering",
      "Department of Computer Engineering",
      "Department of Electrical Engineering",
      "Department of Electronics Engineering",
      "Department of Industrial Engineering",
      "Department of Mechanical Engineering",
      "Department of Railway Engineering"
    ],
    // Default departments if college is not selected or not in the list
    "default": [
      "Please select a college first"
    ]
  },
  
  program: {
    "College of Accountancy and Finance (CAF)": [
      "Bachelor of Science in Accountancy (BSA)",
      "Bachelor of Science in Business Administration Major in Financial Management (BSBAFM)",
      "Bachelor of Science in Management Accounting (BSMA)"
    ],
    "College of Architecture, Design and the Built Environment (CADBE)": [
      "Bachelor of Science in Architecture (BS-ARCH)",
      "Bachelor of Science in Interior Design (BSID)",
      "Bachelor of Science in Environmental Planning (BSEP)"
    ],
    "College of Arts and Letters (CAL)": [
      "Bachelor of Arts in English Language Studies (ABELS)",
      "Bachelor of Arts in Filipinology (ABF)",
      "Bachelor of Arts in Literary and Cultural Studies (ABLCS)",
      "Bachelor of Arts in Philosophy (AB-PHILO)",
      "Bachelor of Performing Arts major in Theater Arts (BPEA)"
    ],
    "College of Business Administration (CBA)": [
      "Doctor in Business Administration (DBA)",
      "Master in Business Administration (MBA)",
      "Bachelor of Science in Business Administration major in Human Resource Management (BSBAHRM)",
      "Bachelor of Science in Business Administration major in Marketing Management (BSBA-MM)",
      "Bachelor of Science in Entrepreneurship (BSENTREP)",
      "Bachelor of Science in Office Administration (BSOA)"
    ],
    "College of Communication (COC)": [
      "Bachelor in Advertising and Public Relations (BADPR)",
      "Bachelor of Arts in Broadcasting (BA Broadcasting)",
      "Bachelor of Arts in Communication Research (BACR)",
      "Bachelor of Arts in Journalism (BAJ)"
    ],
    "College of Computer and Information Sciences (CCIS)": [
      "Bachelor of Science in Computer Science (BSCS)",
      "Bachelor of Science in Information Technology (BSIT)"
    ],
    "College of Education (COED)": [
      "Doctor of Philsophy in Education Management (PhDEM)",
      "Master of Arts in Education Management (MAEM)",
      "Master in Business Education (MBE)",
      "Master in Library and Information Science (MLIS)",
      "Master of Arts in English Language Teaching (MAELT)",
      "Master of Arts in Education major in Mathematics Education (MAEd-ME)",
      "Master of Arts in Physical Education and Sports (MAPES)",
      "Master of Arts in Education major in Teaching in the Challenged Areas (MAED-TCA)",
      "Post-Baccalaureate Diploma in Education (PBDE)",
      "Bachelor of Technology and Livelihood Education - Home Economics (BTLEd)",
      "Bachelor of Technology and Livelihood Education - Industrial Arts (BTLEd)",
      "Bachelor of Technology and Livelihood Education - ICT (BTLEd)",
      "Bachelor of Library and Information Science (BLIS)",
      "Bachelor of Secondary Education - English (BSEd)",
      "Bachelor of Secondary Education - Mathematics (BSEd)",
      "Bachelor of Secondary Education - Science (BSEd)",
      "Bachelor of Secondary Education - Filipino (BSEd)",
      "Bachelor of Secondary Education - Social Studies (BSEd)",
      "Bachelor of Elementary Education (BEEd)",
      "Bachelor of Early Childhood Education (BECEd)"
    ],
    "College of Engineering (CE)": [
      "Bachelor of Science in Civil Engineering (BSCE)",
      "Bachelor of Science in Computer Engineering (BSCpE)",
      "Bachelor of Science in Electrical Engineering (BSEE)",
      "Bachelor of Science in Electronics Engineering (BSECE)",
      "Bachelor of Science in Industrial Engineering (BSIE)",
      "Bachelor of Science in Mechanical Engineering (BSME)",
      "Bachelor of Science in Railway Engineering (BSRE)"
    ],
    "College of Human Kinetics (CHK)": [
      "Bachelor of Physical Education (BPE)",
      "Bachelor of Science in Exercises and Sports (BSESS)"
    ],
    "College of Law (CL)": [
      "Juris Doctor (JD)"
    ],
    "College of Political Science and Public Administration (CPSPA)": [
      "Doctor in Public Administration (DPA)",
      "Master in Public Administration (MPA)",
      "Bachelor of Arts in Political Science (BAPS)",
      "Bachelor of Arts in Political Economy (BAPE)",
      "Bachelor of Arts in International Studies (BAIS)",
      "Bachelor of Public Administration (BPA)"
    ],
    "College of Social Sciences and Development (CSSD)": [
      "Bachelor of Arts in History (BAH)",
      "Bachelor of Arts in Sociology (BAS)",
      "Bachelor of Science in Cooperatives (BSC)",
      "Bachelor of Science in Economics (BSE)",
      "Bachelor of Science in Psychology (BSPSY)"
    ],
    "College of Science (CS)": [
      "Bachelor of Science Food Technology (BSFT)",
      "Bachelor of Science in Applied Mathematics (BSAPMATH)",
      "Bachelor of Science in Biology (BSBIO)",
      "Bachelor of Science in Chemistry (BSCHEM)",
      "Bachelor of Science in Mathematics (BSMATH)",
      "Bachelor of Science in Nutrition and Dietetics (BSND)",
      "Bachelor of Science in Physics (BSPHY)",
      "Bachelor of Science in Statistics (BSSTAT)"
    ],
    "College of Tourism, Hospitality and Transportation Management (CTHTM)": [
      "Bachelor of Science in Hospitality Management (BSHM)",
      "Bachelor of Science in Tourism Management (BSTM)",
      "Bachelor of Science in Transportation Management (BSTRM)"
    ],
    "Institute of Technology": [
      "Diploma in Computer Engineering Technology (DCET)",
      "Diploma in Electrical Engineering Technology (DEET)",
      "Diploma in Electronics Engineering Technology (DECET)",
      "Diploma in Information Communication Technology (DICT)",
      "Diploma in Mechanical Engineering Technology (DMET)",
      "Diploma in Office Management (DOMT)"
    ],
    "Masters": [
      "Master in Applied Statistics (MAS)",
      "Master in Business Administration (MBA)",
      "Master in Construction Management (MCM)",
      "Master in Educational Management (MEM)",
      "Master in Public Administration (MPA)",
      "Master of Arts in Communication (MAC)",
      "Master of Arts in English Language Studies (MAELS)",
      "Master of Arts in History (MAH)",
      "Master of Arts in Filipino (MAF)",
      "Master of Arts in Psychology (MAP)",
      "Master of Arts in Technology Management (MATM)",
      "Master of Science in Biology (MSBio)",
      "Master of Science in Civil Engineering (MSCE)",
      "Master of Science in Computer Engineering (MSCpE)",
      "Master of Science in Computer Science (MSCS)",
      "Master of Science in Construction Management (MSCM)",
      "Master of Science in Information Technology (MSIT)",
      "Master of Science in Mathematics (MSM)"
    ],
    "Doctorate": [
      "Doctor of Philosophy in Communication (PhD Com)",
      "Doctor of Philosophy in Economics (PhD Econ)",
      "Doctor of Philosophy in English Language Studies (PhD ELS)",
      "Doctor of Philosophy in Filipino (PhD Fil)",
      "Doctor of Philosophy in Psychology (PhD Psy)"
    ],
    "Open University": [
      "Doctor in Business Administration (DBA)",
      "Doctor in Engineering Management (D.Eng)",
      "Doctor of Philsophy in Education Management (PhDEM)",
      "Doctor in Public Administration (DPA)",
      "Master in Communication (MC)",
      "Master in Business Administration (MBA)",
      "Master of Arts in Education Management (MAEM)",
      "Master in Information Technology (MIT)",
      "Master in Public Administration (MPA)",
      "Master of Science in Construction Management (MSCM)",
      "Post Baccalaureate Diploma in Information Technology (PBDIT)",
      "Bachelor of Science in Entrepreneurship (BSENTREP)",
      "Bachelor of Arts in Broadcasting (BABR)",
      "Bachelor of Science in Business Administration major in Human Resource Management (BSBAHRM)",
      "Bachelor of Science in Business Administration major in Marketing Management (BSBAMM)",
      "Bachelor of Science in Office Administration (BSOA)",
      "Bachelor of Science in Tourism Management (BSTM)",
      "Bachelor of Public Administration (BPA)",
      "Bachelor of Science in Business Administration (BSBA)",
      "Bachelor of Science in Information Technology (BSIT)"
    ],
    // Default programs if college is not selected or not in the list
    "default": [
      "Please select a college first"
    ]
  },
  
  workClassification: [
    "(a) Books, Pamphlets, articles and other writings",
    "(b) Periodicals and newspaper",
    "(c) Lectures, sermons, addresses, dissertations for oral delivery",
    "(d) Letters",
    "(e) Dramatic or dramatic-musical compositions; choreographic works",
    "(f) Musical compositions with or without words",
    "(g) Works of drawing, painting, architecture, sculpture, engraving, lithography",
    "(h) Original ornamental designs or models for articles of manufacture",
    "(i) Illustrations maps, plans, sketches, charts and three-dimensional works",
    "(j) Drawings or plastic works of a scientific or technical character",
    "(k) Photographic works including works produced by a process analogous to photography",
    "(l) Audiovisual works and cinematographic works",
    "(m) Pictorial illustrations and advertisements",
    "(n) Computer Programs",
    "(o) Other literary, scholarly, scientific and artistic works",
    "(p) Sound recordings",
    "(q) Broadcast recordings"
  ]
};

// Initialize dropdown relationships on document load
document.addEventListener('DOMContentLoaded', function() {
  const API_URL = window.CATALOGS_API_URL || '/capstone/catalogs_public_api.php';
  const catalogsCache = {
    campuses: null,
    levels: null,
    colleges: null,
    campusNameToId: new Map(),
    collegeNameToId: new Map(),
    departmentsByCollegeId: new Map(),
    programsByCollegeId: new Map()
  };

  async function fetchJSON(params) {
    const qs = new URLSearchParams(params).toString();
    const url = `${API_URL}?${qs}`;
    try {
      const res = await fetch(url, { credentials: 'same-origin', cache: 'no-store' });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      if (!data || data.ok !== true) throw new Error(data && data.error ? data.error : 'Invalid API');
      return data.data || [];
    } catch (e) {
      return null; // signal fallback
    }
  }

  async function loadCampuses() {
    if (catalogsCache.campuses) return catalogsCache.campuses;
    const rows = await fetchJSON({ entity: 'campus' });
    catalogsCache.campuses = rows; // can be null (fallback)
    catalogsCache.campusNameToId.clear();
    if (Array.isArray(rows)) {
      rows.forEach(r => catalogsCache.campusNameToId.set(r.name, r.id));
    }
    return catalogsCache.campuses;
  }
  async function loadLevels() {
    if (catalogsCache.levels) return catalogsCache.levels;
    const rows = await fetchJSON({ entity: 'level' });
    catalogsCache.levels = rows;
    return catalogsCache.levels;
  }
  async function loadColleges() {
    if (catalogsCache.colleges) return catalogsCache.colleges;
    const rows = await fetchJSON({ entity: 'college' });
    catalogsCache.colleges = rows;
    catalogsCache.collegeNameToId.clear();
    if (Array.isArray(rows)) {
      rows.forEach(r => catalogsCache.collegeNameToId.set(r.name, r.id));
    }
    return catalogsCache.colleges;
  }
  async function loadCollegesByCampusName(campusName) {
    const camps = await loadCampuses();
    if (!Array.isArray(camps)) return null;
    const capId = catalogsCache.campusNameToId.get(campusName);
    if (!capId) return null;
    // No cache per-campus for now; small data set
    const rows = await fetchJSON({ entity: 'college', parent_id: String(capId) });
    return rows;
  }
  async function loadDepartmentsByCollegeName(collegeName) {
    const colleges = await loadColleges();
    if (!Array.isArray(colleges)) return null;
    const cid = catalogsCache.collegeNameToId.get(collegeName);
    if (!cid) return null;
    if (catalogsCache.departmentsByCollegeId.has(cid)) return catalogsCache.departmentsByCollegeId.get(cid);
    const rows = await fetchJSON({ entity: 'department', parent_id: String(cid) });
    catalogsCache.departmentsByCollegeId.set(cid, rows);
    return rows;
  }
  async function loadProgramsByCollegeName(collegeName) {
    const colleges = await loadColleges();
    if (!Array.isArray(colleges)) return null;
    const cid = catalogsCache.collegeNameToId.get(collegeName);
    if (!cid) return null;
    if (catalogsCache.programsByCollegeId.has(cid)) return catalogsCache.programsByCollegeId.get(cid);
    const rows = await fetchJSON({ entity: 'program', parent_id: String(cid) });
    catalogsCache.programsByCollegeId.set(cid, rows);
    return rows;
  }
  let academicLevelSelect = document.getElementById('academicLevel') || document.getElementById('academic_level');
  const collegeSelect = document.getElementById('college');
  const programSelect = document.getElementById('program');
  
  // academicLevelSelect already resolved from either academicLevel or academic_level

  // When academic level changes
  if (academicLevelSelect && collegeSelect && programSelect) {
    academicLevelSelect.addEventListener('change', async function() {
      const selectedLevel = this.value;
      const isGradOrOU = (selectedLevel === 'Masters' || selectedLevel === 'Doctorate' || selectedLevel === 'Open University');
      if (isGradOrOU) {
        // Disable college and set to N/A for graduate/OU
        collegeSelect.disabled = true;
        collegeSelect.innerHTML = '';
        const naOption = document.createElement('option');
        naOption.value = 'N/A';
        naOption.textContent = 'N/A';
        collegeSelect.appendChild(naOption);
        // Use predefined level-specific program lists (DB doesn't tag by level yet)
        populateDropdown(programSelect, academicData.program[selectedLevel] || academicData.program.default);
      } else {
        collegeSelect.disabled = false;
        // Populate colleges from API if available; fallback to static list
        let rows = null;
        const campusSelect = document.getElementById('campus');
        const chosenCampus = campusSelect ? campusSelect.value : '';
        if (chosenCampus) {
          rows = await loadCollegesByCampusName(chosenCampus);
        }
        if (!Array.isArray(rows) || rows.length === 0) {
          rows = await loadColleges();
        }
        if (Array.isArray(rows) && rows.length) populateDropdown(collegeSelect, rows.map(r => r.name));
        else populateDropdown(collegeSelect, academicData.college);
        // Reset program dropdown
        populateDropdown(programSelect, academicData.program.default);
      }
    });
  }
  
  // When college selection changes, update programs
  if (collegeSelect && programSelect) {
    collegeSelect.addEventListener('change', async function() {
      const selectedCollege = this.value;
      // If undergrad, try API for programs under the selected college; otherwise fallback to static
      const level = academicLevelSelect ? academicLevelSelect.value : 'Undergraduate';
      const isGradOrOU = (level === 'Masters' || level === 'Doctorate' || level === 'Open University');
      if (!isGradOrOU) {
        const prows = await loadProgramsByCollegeName(selectedCollege);
        if (Array.isArray(prows)) {
          populateDropdown(programSelect, prows.map(p => p.name));
          return;
        }
      }
      populateDropdown(
        programSelect,
        academicData.program[selectedCollege] || academicData.program.default
      );
    });
  }

  // When campus changes, filter colleges (undergraduate only)
  const campusSelect = document.getElementById('campus');
  if (campusSelect && collegeSelect) {
    campusSelect.addEventListener('change', async function () {
      const level = academicLevelSelect ? academicLevelSelect.value : 'Undergraduate';
      const isGradOrOU = (level === 'Masters' || level === 'Doctorate' || level === 'Open University');
      if (isGradOrOU) return; // college is N/A when grad/OU
      const chosenCampus = this.value;
      let rows = null;
      if (chosenCampus) rows = await loadCollegesByCampusName(chosenCampus);
      if (!Array.isArray(rows) || rows.length === 0) rows = await loadColleges();
      if (Array.isArray(rows) && rows.length) populateDropdown(collegeSelect, rows.map(r => r.name));
      else populateDropdown(collegeSelect, academicData.college);
      // Reset programs when college list changes
      if (programSelect) populateDropdown(programSelect, academicData.program.default);
    });
  }
  
  // Helper function to populate dropdowns
  function populateDropdown(selectElement, options) {
    // Clear existing options
    selectElement.innerHTML = '';
    
    // Add default first option
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.disabled = true;
    defaultOption.selected = true;
    defaultOption.textContent = 'Choose...';
    selectElement.appendChild(defaultOption);
    
    // Add options from data
    options.forEach(option => {
      const optElement = document.createElement('option');
      optElement.value = option;
      optElement.textContent = option;
      selectElement.appendChild(optElement);
    });
  }
  // Students should never see "Not Studying" as an academic level
  function filterStudentLevels(levelNames) {
    if (!Array.isArray(levelNames)) return [];
    return levelNames.filter(name =>
      typeof name === 'string' && name.trim().toLowerCase() !== 'not studying'
    );
  }
  
  // Initialize all dropdowns
  // campus select already declared above if present
  const workClassificationSelect = document.getElementById('workClassification');
  
  // Only populate selects that are not fixed/disabled by server profile values
  (async () => {
    if (campusSelect && !campusSelect.disabled) {
      const crows = await loadCampuses();
      if (Array.isArray(crows) && crows.length) populateDropdown(campusSelect, crows.map(r => r.name));
      else populateDropdown(campusSelect, academicData.campus);
    }
    if (academicLevelSelect && !academicLevelSelect.disabled) {
      const lrows = await loadLevels();
      if (Array.isArray(lrows) && lrows.length) {
        const levelNames = lrows.map(r => r.name);
        populateDropdown(academicLevelSelect, filterStudentLevels(levelNames));
      } else {
        populateDropdown(academicLevelSelect, academicData.academicLevel);
      }
    }
    if (collegeSelect && !collegeSelect.disabled) {
      const rows = await loadColleges();
      if (Array.isArray(rows) && rows.length) populateDropdown(collegeSelect, rows.map(r => r.name));
      else populateDropdown(collegeSelect, academicData.college);
    }
    if (programSelect && !programSelect.disabled) populateDropdown(programSelect, academicData.program.default);
  })();

  // Initial sync: if current academic level is Masters/Doctorate/Open University,
  // disable College and show N/A, and populate Program from level list (if editable)
  if (academicLevelSelect && collegeSelect) {
    const currentLevel = academicLevelSelect.value;
    const isGradOrOU = (currentLevel === 'Masters' || currentLevel === 'Doctorate' || currentLevel === 'Open University');
    if (isGradOrOU) {
      collegeSelect.disabled = true;
      collegeSelect.innerHTML = '';
      const naOption = document.createElement('option');
      naOption.value = 'N/A';
      naOption.textContent = 'N/A';
      collegeSelect.appendChild(naOption);
      if (programSelect && !programSelect.disabled) {
        populateDropdown(programSelect, academicData.program[currentLevel] || academicData.program.default);
      }
    }
  }
  if (workClassificationSelect) populateDropdown(workClassificationSelect, academicData.workClassification);

  // If college/program are fixed (disabled), ensure their displayed option remains selected
  if (collegeSelect && collegeSelect.disabled && collegeSelect.options.length === 1) {
    collegeSelect.selectedIndex = 0;
  }
  if (programSelect && programSelect.disabled && programSelect.options.length === 1) {
    programSelect.selectedIndex = 0;
  }
  // Ensure academic level stays on server-provided value when locked
  if (academicLevelSelect && academicLevelSelect.disabled && academicLevelSelect.options.length === 1) {
    academicLevelSelect.selectedIndex = 0;
  }

  // Re-initialize after terms acceptance if form was hidden initially
  document.addEventListener('ipmo:form:show', async function () {
    if (campusSelect && !campusSelect.disabled && campusSelect.options.length <= 1) {
      const crows = await loadCampuses();
      if (Array.isArray(crows) && crows.length) populateDropdown(campusSelect, crows.map(r => r.name));
      else populateDropdown(campusSelect, academicData.campus);
    }
    if (academicLevelSelect && !academicLevelSelect.disabled && academicLevelSelect.options.length <= 1) {
      const lrows = await loadLevels();
      if (Array.isArray(lrows) && lrows.length) populateDropdown(academicLevelSelect, lrows.map(r => r.name));
      else populateDropdown(academicLevelSelect, academicData.academicLevel);
    }
    if (collegeSelect && !collegeSelect.disabled && collegeSelect.options.length <= 1) {
      const rows = await loadColleges();
      if (Array.isArray(rows) && rows.length) populateDropdown(collegeSelect, rows.map(r => r.name));
      else populateDropdown(collegeSelect, academicData.college);
    }
    if (programSelect && !programSelect.disabled && programSelect.options.length <= 1) {
      populateDropdown(programSelect, academicData.program.default);
    }
    if (workClassificationSelect && workClassificationSelect.options.length <= 1) {
      populateDropdown(workClassificationSelect, academicData.workClassification);
    }
  });
});