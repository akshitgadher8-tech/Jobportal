<?php
session_start();
include 'db_connect.php';

// 1. SECURITY: Login Check
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['username'];
$msg = "";

// 2. HANDLE APPLY ACTION
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_job'])) {
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title']);
    $email = $user . "@example.com"; // In a real app, fetch real email. For now, we generate one.
    
    // Check if already applied
    $check = $conn->query("SELECT * FROM applications WHERE applicant_name='$user' AND job_title='$job_title'");
    
    if ($check->num_rows > 0) {
        $msg = "You have already applied for this job!";
    } else {
        // Insert Application
        $sql = "INSERT INTO applications (applicant_name, email, job_title, status) VALUES ('$user', '$email', '$job_title', 'Pending')";
        if ($conn->query($sql)) {
            $msg = "Application submitted successfully for $job_title!";
        } else {
            $msg = "Error: " . $conn->error;
        }
    }
}

// 3. FETCH USER STATS
$total_apps = $conn->query("SELECT * FROM applications WHERE applicant_name='$user'")->num_rows;
$pending_apps = $conn->query("SELECT * FROM applications WHERE applicant_name='$user' AND status='Pending'")->num_rows;
$accepted_apps = $conn->query("SELECT * FROM applications WHERE applicant_name='$user' AND status='Accepted'")->num_rows;

// Fetch Tables
$my_apps = $conn->query("SELECT * FROM applications WHERE applicant_name='$user' ORDER BY applied_at DESC");
$all_jobs = $conn->query("SELECT * FROM jobs ORDER BY posted_at DESC");
$recent_jobs = $conn->query("SELECT * FROM jobs ORDER BY posted_at DESC LIMIT 3"); // For Overview
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* Status Badges */
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge.Pending { background: #fff7ed; color: #c2410c; }
        .badge.Accepted { background: #f0fdf4; color: #15803d; }
        .badge.Rejected { background: #fef2f2; color: #b91c1c; }
        
        /* Job Card Style */
        .job-card {
            border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; 
            margin-bottom: 15px; background: white;
            display: flex; justify-content: space-between; align-items: center;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        
        <nav class="sidebar">
            <h2 style="text-align:center; margin-bottom:30px;">JobPortal</h2>
            <ul class="nav-links">
                <li><a href="#" id="link-overview" onclick="showSection('overview')" class="active"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
                <li><a href="#" id="link-search" onclick="showSection('search')"><i class='bx bxs-search'></i> Search Jobs</a></li>
                <li><a href="#" id="link-apps" onclick="showSection('apps')"><i class='bx bxs-file-doc'></i> My Applications</a></li>
                <li style="margin-top: 50px;">
                    <a href="#" onclick="confirmLogout()"><i class='bx bxs-log-out'></i> Logout</a>
                </li>
            </ul>
        </nav>

        <main class="main-content">
            
            <header style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
                <h1>Welcome, <?php echo htmlspecialchars($user); ?>!</h1>
                <span style="color:#64748b;">Today: <?php echo date("F j, Y"); ?></span>
            </header>

            <?php if($msg): ?>
                <div style="background:#dcfce7; color:#166534; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #bbf7d0;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <div id="section-overview">
                <div class="stats-grid">
                    <div class="stat-card" onclick="showSection('apps')" style="cursor:pointer;">
                        <i class='bx bxs-file-doc' style="font-size:30px; color:var(--primary);"></i>
                        <div><h3><?php echo $total_apps; ?></h3><p>Applied</p></div>
                    </div>
                    <div class="stat-card">
                        <i class='bx bxs-time' style="font-size:30px; color:#f59e0b;"></i>
                        <div><h3><?php echo $pending_apps; ?></h3><p>Pending</p></div>
                    </div>
                    <div class="stat-card">
                        <i class='bx bxs-check-circle' style="font-size:30px; color:#22c55e;"></i>
                        <div><h3><?php echo $accepted_apps; ?></h3><p>Accepted</p></div>
                    </div>
                </div>

                <h2 style="margin-top:30px; margin-bottom:15px;">Latest Openings</h2>
                <?php if ($recent_jobs->num_rows > 0): ?>
                    <?php while($job = $recent_jobs->fetch_assoc()): ?>
                        <div class="job-card">
                            <div>
                                <h3 style="margin:0 0 5px 0;"><?php echo $job['title']; ?></h3>
                                <p style="margin:0; color:#64748b; font-size:14px;">
                                    <?php echo $job['category']; ?> • <?php echo $job['salary']; ?>
                                </p>
                            </div>
                            <button class="btn primary-btn" style="width:auto;" onclick="showSection('search')">View</button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No jobs available right now.</p>
                <?php endif; ?>
            </div>

            <div id="section-search" style="display:none;">
                <h1>Available Jobs</h1>
                <div class="card">
                    <?php 
                    // Reset pointer for second loop
                    $all_jobs->data_seek(0); 
                    if ($all_jobs->num_rows > 0): 
                    ?>
                        <?php while($job = $all_jobs->fetch_assoc()): ?>
                            <div class="job-card">
                                <div>
                                    <h3 style="margin:0 0 5px 0; color: #1e293b;"><?php echo $job['title']; ?></h3>
                                    <p style="margin:0; color:#64748b; font-size:14px;">
                                        <?php echo $job['category']; ?> • <?php echo $job['type']; ?> • <strong><?php echo $job['salary']; ?></strong>
                                    </p>
                                    <small style="color:#94a3b8;">Posted: <?php echo date('M d', strtotime($job['posted_at'])); ?></small>
                                </div>
                                
                                <form action="user_dashboard.php" method="POST">
                                    <input type="hidden" name="apply_job" value="1">
                                    <input type="hidden" name="job_title" value="<?php echo $job['title']; ?>">
                                    <button class="btn primary-btn" style="width:auto; padding: 8px 20px;">Apply Now</button>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No jobs posted yet. Please check back later.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div id="section-apps" style="display:none;">
                <h1>My Applications</h1>
                <div class="card">
                    <table>
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Date Applied</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($my_apps->num_rows > 0): ?>
                                <?php while($app = $my_apps->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $app['job_title']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                                    <td><span class="badge <?php echo $app['status']; ?>"><?php echo $app['status']; ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3">You haven't applied to any jobs yet. Go to Search Jobs!</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script>
        function showSection(sectionId) {
            // Hide all
            document.getElementById('section-overview').style.display = 'none';
            document.getElementById('section-search').style.display = 'none';
            document.getElementById('section-apps').style.display = 'none';
            
            // Show target
            document.getElementById('section-' + sectionId).style.display = 'block';

            // Sidebar Active State
            document.querySelectorAll('.nav-links a').forEach(link => link.classList.remove('active'));
            document.getElementById('link-' + sectionId).classList.add('active');
        }

        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "login.php";
            }
        }
    </script>
</body>
</html>