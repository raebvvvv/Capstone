// Academic Program Data for PUP
const academicData = {
  campus: [
    "PUP Main (Sta. Mesa, Manila)"
  ],
  
  // Note: For employee-facing pages, the select has id 'academicLevel'. For student pages, it's 'academic_level'.
  // We keep 'Undergraduate' in data for student flows, but employee pages will render a custom set (handled below).
  academicLevel: [
    "Undergraduate",
    "Masters",
    "Doctorate",
    "Open University",
    "Not Studying"
  ],
  
  // College options will be derived dynamically from program keys at runtime (see init below)
  college: [],
  
  department: {
    // CBA
    "College of Business Administration (CBA)": [
      "Department of Business Administration",
      "Department of Entrepreneurship",
      "Department of Office Administration",
    ],
   
    // CCIS
    "College of Computer and Information Sciences (CCIS)": [
      "Department of Computer Science",
      "Department of Information Technology",
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
  // Support both employee and student pages
  const academicLevelSelect = document.getElementById('academicLevel') || document.getElementById('academic_level');
  const collegeSelect = document.getElementById('college');
  const programSelect = document.getElementById('program');
  const departmentSelect = document.getElementById('department');
  // Remember which fields were disabled when page loaded; never unlock them client-side
  const initiallyLocked = new Set();
  [academicLevelSelect, collegeSelect, programSelect, departmentSelect].forEach(el=>{ if(el && el.disabled){ initiallyLocked.add(el.id); el.dataset.locked = '1'; } });
  
  // When academic level changes
  if (academicLevelSelect) {
    academicLevelSelect.addEventListener('change', function() {
      const selectedLevel = this.value;
      const isGradOrOU = (selectedLevel === 'Masters' || selectedLevel === 'Doctorate' || selectedLevel === 'Open University');
      const isNotStudying = (selectedLevel === 'Not Studying');

      // Do not touch College or Department. Only update Program based on level.
      if (programSelect) {
        const progLocked = programSelect && programSelect.dataset.locked === '1';
        if (isNotStudying) {
          programSelect.disabled = true;
          programSelect.innerHTML = '';
          const progNA = document.createElement('option');
          progNA.value = 'N/A';
          progNA.textContent = 'N/A';
          programSelect.appendChild(progNA);
        } else if (isGradOrOU) {
          // Only enable if it wasn't locked initially
          programSelect.disabled = progLocked ? true : false;
          let options = academicData.program[selectedLevel] || academicData.program.default;
          if (selectedLevel === 'Open University') {
            options = options.filter(p => !/(\(BSIT\)|\(BSBA\))$/.test(p));
          }
          populateDropdown(programSelect, options);
        } else {
          // Undergraduate/other: program depends on currently selected college
          programSelect.disabled = progLocked ? true : false;
          const selCollege = collegeSelect ? collegeSelect.value : null;
          const options = (selCollege && academicData.program[selCollege]) ? academicData.program[selCollege] : academicData.program.default;
          populateDropdown(programSelect, options);
        }
      }
    });
  }
  
  // When college selection changes, update programs
  if (collegeSelect) {
    collegeSelect.addEventListener('change', function() {
    const selectedCollege = this.value;
    
    // Update programs dropdown only for Undergraduate; for Masters/Doctorate/OU keep level-specific list
      if (programSelect) {
        const level = (academicLevelSelect && academicLevelSelect.value) || 'Undergraduate';
        const isGradOrOU = (level === 'Masters' || level === 'Doctorate' || level === 'Open University');
        const isNS = (level === 'Not Studying');
        if (!isGradOrOU && !isNS) {
          populateDropdown(
            programSelect,
            academicData.program[selectedCollege] || academicData.program.default
          );
        }
      }

      // Update departments dropdown (lock to N/A when no mapping exists)
      if (departmentSelect) {
        const depLocked = departmentSelect && departmentSelect.dataset.locked === '1';
        const deps = academicData.department[selectedCollege];
        if (Array.isArray(deps) && deps.length) {
          // Only enable if it wasn't locked initially
          departmentSelect.disabled = depLocked ? true : false;
          populateDropdown(departmentSelect, deps);
        } else {
          departmentSelect.disabled = true;
          departmentSelect.innerHTML = '';
          const na = document.createElement('option');
          na.value = 'N/A';
          na.textContent = 'N/A';
          departmentSelect.appendChild(na);
        }
      }
    });
  }
  
  // Helper function to populate dropdowns
  function populateDropdown(selectElement, options) {
    // Store current selection to preserve if still valid
    const previous = selectElement.value;

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

    // Re-select previous value if it still exists
    if (previous && options.includes(previous)) {
      selectElement.value = previous;
    }
  }
  
  // Initialize all dropdowns
  const campusSelect = document.getElementById('campus');
  const workClassificationSelect = document.getElementById('workClassification');
  
  // Build dynamic college list from program keys, excluding level keys and defaults
  function getDynamicCollegeList() {
    const exclude = new Set(["Masters","Doctorate","Open University","default"]);
    return Object.keys(academicData.program)
      .filter(k => !exclude.has(k))
      .sort();
  }
  
  // Helper: whether select has a single, server-provided value we should keep
  function hasServerSelection(select) {
    if (!select || !select.options || select.options.length !== 1) return false;
    const val = (select.options[0].value || '').trim();
    const txt = (select.options[0].textContent || '').trim();
    if (!val) return false;
    const sentinel = new Set(['Choose...', 'N/A']);
    return !sentinel.has(val) && !sentinel.has(txt);
  }

  // Populate selects even if disabled, but don't overwrite a single server value
  if (campusSelect && (!campusSelect.options || campusSelect.options.length === 0 || (campusSelect.options.length === 1 && !hasServerSelection(campusSelect)))) {
    populateDropdown(campusSelect, academicData.campus);
  }
  // Populate academic level options (respect server-provided selection)
  if (academicLevelSelect && (!academicLevelSelect.options || academicLevelSelect.options.length === 0 || (academicLevelSelect.options.length === 1 && !hasServerSelection(academicLevelSelect)))) {
    // For employee pages (id 'academicLevel'), show requested options only
    const employeeLevelOptions = ["Not Studying", "Doctorate", "Masters", "Open University"];
    populateDropdown(academicLevelSelect, employeeLevelOptions);
  }
  if (collegeSelect && (!collegeSelect.options || collegeSelect.options.length === 0 || (collegeSelect.options.length === 1 && !hasServerSelection(collegeSelect)))) {
    populateDropdown(collegeSelect, getDynamicCollegeList());
  }
  if (programSelect && (!programSelect.options || programSelect.options.length === 0 || (programSelect.options.length === 1 && !hasServerSelection(programSelect)))) {
    populateDropdown(programSelect, academicData.program.default);
  }
  if (departmentSelect && (!departmentSelect.options || departmentSelect.options.length === 0 || (departmentSelect.options.length === 1 && !hasServerSelection(departmentSelect)))) {
    populateDropdown(departmentSelect, academicData.department.default);
  }
  if (workClassificationSelect) populateDropdown(workClassificationSelect, academicData.workClassification);

  // Apply department N/A lock if initial college has no mapping, but don't override server-provided selection
  if (collegeSelect && departmentSelect) {
    const initialCollege = collegeSelect.value;
    if (initialCollege) {
      const deps = academicData.department[initialCollege];
      if ((!Array.isArray(deps) || deps.length === 0) && !hasServerSelection(departmentSelect)) {
        departmentSelect.disabled = true;
        departmentSelect.innerHTML = '';
        const na = document.createElement('option');
        na.value = 'N/A';
        na.textContent = 'N/A';
        departmentSelect.appendChild(na);
      }
    }
  }

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
  document.addEventListener('ipmo:form:show', function () {
    if (campusSelect && (!campusSelect.options || campusSelect.options.length === 0 || (campusSelect.options.length === 1 && !hasServerSelection(campusSelect)))) populateDropdown(campusSelect, academicData.campus);
    if (academicLevelSelect && (!academicLevelSelect.options || academicLevelSelect.options.length === 0 || (academicLevelSelect.options.length === 1 && !hasServerSelection(academicLevelSelect)))) {
      const employeeLevelOptions = ["Not Studying", "Doctorate", "Masters", "Open University"];
      populateDropdown(academicLevelSelect, employeeLevelOptions);
    }
    if (collegeSelect && (!collegeSelect.options || collegeSelect.options.length === 0 || (collegeSelect.options.length === 1 && !hasServerSelection(collegeSelect)))) populateDropdown(collegeSelect, getDynamicCollegeList());
    if (programSelect && (!programSelect.options || programSelect.options.length === 0 || (programSelect.options.length === 1 && !hasServerSelection(programSelect)))) populateDropdown(programSelect, academicData.program.default);
    if (departmentSelect && (!departmentSelect.options || departmentSelect.options.length === 0 || (departmentSelect.options.length === 1 && !hasServerSelection(departmentSelect)))) populateDropdown(departmentSelect, academicData.department.default);

    // If a college is preselected (by server or future prefill), adjust department/program lists accordingly
    const selectedCollege = collegeSelect && collegeSelect.value ? collegeSelect.value : null;
    if (selectedCollege) {
      if (departmentSelect) {
        const deps = (academicData.department && academicData.department[selectedCollege]) ? academicData.department[selectedCollege] : academicData.department.default;
        populateDropdown(departmentSelect, deps);
      }
      if (programSelect) {
        const progs = (academicData.program && academicData.program[selectedCollege]) ? academicData.program[selectedCollege] : academicData.program.default;
        populateDropdown(programSelect, progs);
      }
    }

    // Notify other scripts that academic selects are populated
    document.dispatchEvent(new CustomEvent('ipmo:form:academics:ready'));
  });
});