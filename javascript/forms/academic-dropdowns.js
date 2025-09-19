// Academic Program Data for PUP
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
  const academicLevelSelect = document.getElementById('academicLevel');
  const collegeSelect = document.getElementById('college');
  const programSelect = document.getElementById('program');
  
  // When academic level changes
  academicLevelSelect.addEventListener('change', function() {
    const selectedLevel = this.value;
    
    if (selectedLevel === 'Masters' || selectedLevel === 'Doctorate' || selectedLevel === 'Open University') {
      // Set college to N/A for graduate programs and Open University
      collegeSelect.disabled = true;
      
      // Create and set N/A option for college
      collegeSelect.innerHTML = '';
      const naOption = document.createElement('option');
      naOption.value = 'N/A';
      naOption.textContent = 'N/A';
      collegeSelect.appendChild(naOption);
      
      // Populate program dropdown with appropriate programs
      populateDropdown(programSelect, academicData.program[selectedLevel]);
    } else {
      // Enable college dropdown for undergraduate
      collegeSelect.disabled = false;
      
      // Reset college dropdown
      populateDropdown(collegeSelect, academicData.college);
      
      // Reset program dropdown
      populateDropdown(programSelect, academicData.program.default);
    }
  });
  
  // When college selection changes, update programs
  collegeSelect.addEventListener('change', function() {
    const selectedCollege = this.value;
    
    // Update programs dropdown
    populateDropdown(
      programSelect,
      academicData.program[selectedCollege] || academicData.program.default
    );
  });
  
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
  
  // Initialize all dropdowns
  const campusSelect = document.getElementById('campus');
  const workClassificationSelect = document.getElementById('workClassification');
  
  populateDropdown(campusSelect, academicData.campus);
  populateDropdown(academicLevelSelect, academicData.academicLevel);
  populateDropdown(collegeSelect, academicData.college);
  populateDropdown(programSelect, academicData.program.default);
  populateDropdown(workClassificationSelect, academicData.workClassification);
});