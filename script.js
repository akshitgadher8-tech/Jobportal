/* =========================
   1. DATA & MOCK JOBS
   ========================= */
const DB_KEY = "JOB_PORTAL_V3"; 
const APP_KEY = "JOB_APPS_V3";

function initDB() {
    // If no jobs exist, create them automatically
    if (!localStorage.getItem(DB_KEY)) {
        const jobs = [
            { title: "Senior Java Developer", category: "IT", type: "Full Time", salary: "12 LPA" },
            { title: "English Professor", category: "Teaching", type: "Contract", salary: "6 LPA" },
            { title: "Lab Assistant", category: "Non-Teaching", type: "Full Time", salary: "4 LPA" },
            { title: "Marketing Manager", category: "Marketing", type: "Remote", salary: "8 LPA" }
        ];
        localStorage.setItem(DB_KEY, JSON.stringify(jobs));
    }
    if (!localStorage.getItem(APP_KEY)) {
        localStorage.setItem(APP_KEY, JSON.stringify([]));
    }
}

// Helpers
function getJobs() { return JSON.parse(localStorage.getItem(DB_KEY)); }
function getApps() { return JSON.parse(localStorage.getItem(APP_KEY)); }
function saveApps(data) { localStorage.setItem(APP_KEY, JSON.stringify(data)); }

/* =========================
   2. AUTHENTICATION (FIXED)
   ========================= */
function goToLogin() { window.location.href = "login.html"; }
function goToAdminLogin() { window.location.href = "admin_login.html"; }
function logout() { window.location.href = "index.html"; }

function handleLogin(event) {
    event.preventDefault();
    const u = document.getElementById("username").value;
    const p = document.getElementById("password").value;

    if (u === "user" && p === "123") {
        window.location.href = "user_dashboard.html";
    } else {
        alert("Invalid credentials! Try: user / 123");
    }
}

function handleAdminLogin(event) {
    event.preventDefault();
    const u = document.getElementById("adminUsername").value;
    const p = document.getElementById("adminPassword").value;

    if (u === "admin" && p === "admin123") {
        window.location.href = "admin_dashboard.html";
    } else {
        alert("Invalid Admin! Try: admin / admin123");
    }
}

/* =========================
   3. USER DASHBOARD LOGIC
   ========================= */
function renderUserJobs() {
    const list = document.getElementById("availableJobsList");
    if(!list) return;

    const jobs = getJobs();
    list.innerHTML = "";
    
    jobs.forEach(job => {
        list.innerHTML += `
        <div style="border:1px solid #e2e8f0; padding:20px; border-radius:8px; margin-bottom:15px; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 style="margin:0 0 5px 0;">${job.title}</h3>
                <p style="margin:0; color:#64748b;">${job.category} • ${job.type} • ${job.salary}</p>
            </div>
            <button class="primary-btn" onclick="openApplyModal('${job.title}')">Apply Now</button>
        </div>`;
    });
}

function renderApps() {
    const tbody = document.getElementById("userAppsTable");
    if(!tbody) return;

    const apps = getApps();
    tbody.innerHTML = "";
    
    if(apps.length === 0) {
        tbody.innerHTML = "<tr><td colspan='3'>No applications found.</td></tr>";
    } else {
        apps.forEach(app => {
            tbody.innerHTML += `
            <tr>
                <td>${app.job}</td>
                <td>${app.date}</td>
                <td><span class="status active">Submitted</span></td>
            </tr>`;
        });
    }
}

function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.nav-links a').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId + '-tab').style.display = 'block';

    if(tabId === 'search') renderUserJobs();
    if(tabId === 'apps') renderApps();
}

/* =========================
   4. APPLY POPUP LOGIC
   ========================= */
function openApplyModal(title) {
    document.getElementById("modalJobTitle").innerText = "Apply for: " + title;
    document.getElementById("jobTarget").value = title;
    document.getElementById("applyModal").style.display = "flex";
}

function closeApplyModal() {
    document.getElementById("applyModal").style.display = "none";
}

function handleApplySubmit(e) {
    e.preventDefault();
    const name = document.getElementById("appName").value;
    const job = document.getElementById("jobTarget").value;
    const date = new Date().toLocaleDateString();

    const apps = getApps();
    apps.push({ name, job, date });
    saveApps(apps);

    alert("Application Submitted!");
    closeApplyModal();
    switchTab('apps');
}

// STARTUP
window.onload = function() {
    initDB();
    renderUserJobs();
};