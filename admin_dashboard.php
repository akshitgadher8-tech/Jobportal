<?php
session_start();
include 'db_connect.php';

// 1. SECURITY: Admin Only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";

// 2. BACKEND LOGIC (Handle Form Submits)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // A. Post a Job
    if (isset($_POST['post_job'])) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $cat = mysqli_real_escape_string($conn, $_POST['category']);
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $salary = mysqli_real_escape_string($conn, $_POST['salary']);

        $sql = "INSERT INTO jobs (title, category, type, salary) VALUES ('$title', '$cat', '$type', '$salary')";
        if ($conn->query($sql)) $msg = "Job Posted Successfully!";
        else $msg = "Error: " . $conn->error;
    }

    // B. Delete a Job
    if (isset($_POST['delete_job'])) {
        $job_id = $_POST['job_id'];
        $conn->query("DELETE FROM jobs WHERE id='$job_id'");
        $msg = "Job Deleted Successfully.";
    }

    // C. Update Application Status
    if (isset($_POST['update_status'])) {
        $app_id = $_POST['app_id'];
        $status = $_POST['status_val'];
        $conn->query("UPDATE applications SET status='$status' WHERE id='$app_id'");
        $msg = "Application status updated to $status";
    }
}

// 3. FETCH DATA (For display)
$job_count = $conn->query("SELECT * FROM jobs")->num_rows;
$app_count = $conn->query("SELECT * FROM applications")->num_rows;
$apps = $conn->query("SELECT * FROM applications ORDER BY applied_at DESC");
$all_jobs = $conn->query("SELECT * FROM jobs ORDER BY posted_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* Card Hover Effect */
        .stat-card { cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        
        /* Delete Button */
        .btn-delete { background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-delete:hover { background: #dc2626; }
    </style>
</head>
<body>

    <div class="dashboard-container">
        
        <nav class="sidebar">
            <h2 style="text-align:center; margin-bottom:30px;">Admin Panel</h2>
            <ul class="nav-links">
                <li><a href="#" id="link-overview" onclick="showSection('overview')" class="active"><i class='bx bxs-dashboard'></i> Overview</a></li>
                <li><a href="#" id="link-jobs" onclick="showSection('jobs')"><i class='bx bxs-briefcase'></i> Manage Jobs</a></li>
                <li><a href="#" id="link-applicants" onclick="showSection('applicants')"><i class='bx bxs-user-detail'></i> View Applicants</a></li>
                
                <li style="margin-top: 50px;">
                    <a href="#" onclick="confirmLogout()"><i class='bx bxs-log-out'></i> Logout</a>
                </li>
            </ul>
        </nav>

        <main class="main-content">
            
            <?php if($msg): ?>
                <div style="background:#dcfce7; color:#166534; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #bbf7d0;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <div id="section-overview">
                <h1>Admin Overview</h1>
                <div class="stats-grid">
                    
                    <div class="stat-card" onclick="showSection('jobs')">
                        <i class='bx bxs-briefcase' style="font-size:30px; color:var(--primary);"></i>
                        <div>
                            <h3><?php echo $job_count; ?></h3>
                            <p>Active Jobs (Click to View)</p>
                        </div>
                    </div>

                    <div class="stat-card" onclick="showSection('applicants')">
                        <i class='bx bxs-group' style="font-size:30px; color:var(--primary);"></i>
                        <div>
                            <h3><?php echo $app_count; ?></h3>
                            <p>Total Applicants (Click to View)</p>
                        </div>
                    </div>

                </div>
            </div>

            <div id="section-jobs" style="display:none;">
                <h1>Manage Jobs</h1>
                
                <div class="card">
                    <h3>Current Active Jobs</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Salary</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($all_jobs->num_rows > 0): ?>
                                <?php while($j = $all_jobs->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $j['title']; ?></td>
                                    <td><?php echo $j['category']; ?></td>
                                    <td><?php echo $j['salary']; ?></td>
                                    <td>
                                        <form action="admin_dashboard.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this job?');">
                                            <input type="hidden" name="delete_job" value="1">
                                            <input type="hidden" name="job_id" value="<?php echo $j['id']; ?>">
                                            <button type="submit" class="btn-delete">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4">No active jobs found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card" style="margin-top: 20px;">
                    <h3>Post a New Job</h3>
                    <form action="admin_dashboard.php" method="POST">
                        <input type="hidden" name="post_job" value="1">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="input-group">
                                <label>Job Title</label>
                                <input type="text" name="title" required placeholder="e.g. UX Designer">
                            </div>
                            <div class="input-group">
                                <label>Category</label>
                                <select name="category">
                                    <option>IT / Software</option>
                                    <option>Teaching</option>
                                    <option>Marketing</option>
                                    <option>Management</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Type</label>
                                <select name="type">
                                    <option>Full Time</option>
                                    <option>Part Time</option>
                                    <option>Remote</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Salary</label>
                                <input type="text" name="salary" required placeholder="e.g. 8 LPA">
                            </div>
                        </div>
                        <button type="submit" class="btn primary-btn" style="margin-top:15px;">Publish Job</button>
                    </form>
                </div>
            </div>

            <div id="section-applicants" style="display:none;">
                <h1>Job Applicants</h1>
                <div class="card">
                    <table>
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Applied For</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($apps->num_rows > 0): ?>
                                <?php while($app = $apps->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $app['applicant_name']; ?></td>
                                    <td><?php echo $app['job_title']; ?></td>
                                    <td><?php echo $app['email']; ?></td>
                                    <td><span class="badge <?php echo $app['status']; ?>"><?php echo $app['status']; ?></span></td>
                                    <td>
                                        <form action="admin_dashboard.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="update_status" value="1">
                                            <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                            <button type="submit" name="status_val" value="Accepted" style="background:#22c55e; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;">Accept</button>
                                            <button type="submit" name="status_val" value="Rejected" style="background:#ef4444; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5">No applications received yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script>
        // 1. SWITCH SECTIONS (Smooth "Single Page" feel)
        function showSection(sectionId) {
            // Hide all sections first
            document.getElementById('section-overview').style.display = 'none';
            document.getElementById('section-jobs').style.display = 'none';
            document.getElementById('section-applicants').style.display = 'none';
            
            // Show the requested section
            document.getElementById('section-' + sectionId).style.display = 'block';

            // Update Sidebar Highlight
            // Remove 'active' class from all links
            document.querySelectorAll('.nav-links a').forEach(link => link.classList.remove('active'));
            
            // Add 'active' class to the correct link based on ID
            if(sectionId === 'overview') document.getElementById('link-overview').classList.add('active');
            if(sectionId === 'jobs') document.getElementById('link-jobs').classList.add('active');
            if(sectionId === 'applicants') document.getElementById('link-applicants').classList.add('active');
        }

        // 2. LOGOUT CONFIRMATION
        function confirmLogout() {
            let result = confirm("Are you sure you want to logout?");
            if (result) {
                // If user clicks OK, go to logout file
                window.location.href = "admin_login.php";
            }
            // If user clicks Cancel, nothing happens (stay on page)
        }
    </script>
</body>
</html>