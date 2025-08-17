<?php
session_start();
require 'conn.php'; // Include your database connection file

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    // Destroy the session
    session_destroy();
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Fetch admin data from the database
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT username, email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if (!$admin) {
        echo "Admin not found.";
        exit();
    }
} else {
    echo "User ID not set in session.";
    exit();
}

// Fetch pending users from the database
$query_pending = "SELECT user_id, student_number, username, email, cor FROM users WHERE status = 'pending'";
$result_pending = $conn->query($query_pending);

// Handle user update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $student_number = $_POST['student_number'];

    $query_update = "UPDATE users SET username = ?, email = ?, student_number = ? WHERE user_id = ?";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bind_param("sssi", $username, $email, $student_number, $user_id);
    $stmt_update->execute();

    header("Location: manageuser.php");
    exit();
}

// Handle search query
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$query_active = "SELECT user_id, student_number, username, email FROM users WHERE status = 'approved' AND (username LIKE ? OR email LIKE ?)";
$stmt_active = $conn->prepare($query_active);
$search_param = '%' . $search_query . '%';
$stmt_active->bind_param("ss", $search_param, $search_param);
$stmt_active->execute();
$result_active = $stmt_active->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/manageuser.css?v=5">
    <title>User Management</title>
</head>
<body>
    <!-- Navbar -->
    <header class="bg-light border-bottom py-3 shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand d-flex align-items-center" href="#">
                        <img src="images/puplogo.png" alt="Logo" class="center-img" style="height: 30px; margin-right: 10px;">
                        <span class="fw-bold">PUP e-IPMO [Admin.]</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item"><a class="nav-link" href="admin.php">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="completed_applications.php">Completed Applications</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold" aria-current="page" href="manageuser.php">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="ticket.php">Appplications</a></li>
                            <a href="?logout=true" class="btn btn-primary rounded-pill px-4">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- User Management -->
    <div class="container mt-5">
        <h1 class="text-center mb-4" style="font-size:2rem;">Manage Users</h1>
         <div class="d-flex justify-content-center mb-3">
            <form method="GET" action="manageuser.php" class="input-group search-bar" style="max-width:400px;">
                <input class="form-control rounded-pill ps-4" type="search" name="search" placeholder="Search" aria-label="Search" value="<?php echo htmlspecialchars($search_query); ?>" style="border-radius: 50px;">
                <button class="btn btn-outline-secondary rounded-pill" type="submit" style="margin-left:-40px; border-radius: 50px;">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
        <ul class="nav nav-tabs" id="userTabs">
            <!--<li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#pending">Pending Users</a></li>-->
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#active">Active Users</a></li>
        </ul>
        <div class="tab-content mt-3">
            <!-- Pending Users Tab 
            <div class="tab-pane fade show active" id="pending">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Student Number/Teacher Number</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result_pending->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td>
                                        <?php if (!empty($row['cor']) && $_SESSION['is_admin'] == 1): ?>
                                            <a href="view_file.php?user_id=<?php echo urlencode($row['user_id']); ?>" class="btn btn-info btn-sm mb-2" target="_blank">View COR</a>
                                        <?php endif; ?>
                                        <a href="approve_user.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-success btn-sm mb-2">Approve</a>
                                        <a href="delete_user.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm mb-2">Reject</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>-->
            <!-- Active Users Tab -->
            <div class="tab-pane fade show active" id="active">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Student Number/Employee ID</th>
                                <th>Name</th>
                                <th>Classification</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result_active->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                     <td>Student</td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $row['user_id']; ?>">Edit</button>
                                        <a href="delete_user.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm mb-2">Delete</a>
                                    </td>
                                </tr>

                                <!-- Edit User Modal -->
                                <div class="modal fade" id="editUserModal<?php echo $row['user_id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?php echo $row['user_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editUserModalLabel<?php echo $row['user_id']; ?>">Edit User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="manageuser.php">
                                                    <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                                    <div class="mb-3">
                                                        <label for="username" class="form-label">Name</label>
                                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">Webmail</label>
                                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="student_number" class="form-label">Student Number/Employee ID</label>
                                                        <input type="text" class="form-control" id="student_number" name="student_number" value="<?php echo htmlspecialchars($row['student_number']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="home_address" class="form-label">Home Address</label>
                                                        <input type="text" class="form-control" id="home_address" name="home_address" value="<?php echo htmlspecialchars($row['student_number']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="mobile_number" class="form-label">Mobile Number</label>
                                                        <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo htmlspecialchars($row['student_number']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="Campus" class="form-label">PUP Main</label>
                                                        <input type="text" class="form-control" id="Campus" name="Campus" value="<?php echo htmlspecialchars($row['student_number']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="College" class="form-label">CCIS</label>
                                                        <input type="text" class="form-control" id="College" name="College" value="<?php echo htmlspecialchars($row['student_number']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="Department" class="form-label">Department</label>
                                                        <input type="text" class="form-control" id="Department" name="Department" value="<?php echo htmlspecialchars($row['student_number']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="Program" class="form-label">Program</label>
                                                        <input type="text" class="form-control" id="Program" name="Program" value="<?php echo htmlspecialchars($row['student_number']); ?>" required>
                                                    </div>
                                                    <button type="submit" name="update_user" class="btn btn-success">Update</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>