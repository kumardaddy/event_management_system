<?php
require_once 'db_connect.php';
require_once 'functions.php';

// Check if user is logged in
require_login();

// Get user data
$user = get_user_data($_SESSION["user_id"], $conn);

// Handle profile picture upload
$upload_error = "";
$upload_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"])) {
    if ($_FILES["profile_picture"]["error"] == 0) {
        $upload_result = upload_profile_picture($_FILES["profile_picture"]);
        
        if ($upload_result["success"]) {
            // Update user profile picture in database
            $stmt = $conn->prepare("UPDATE users SET profile_picture = :profile_picture WHERE id = :user_id");
            $stmt->bindParam(':profile_picture', $upload_result["file_path"]);
            $stmt->bindParam(':user_id', $_SESSION["user_id"]);
            $stmt->execute();
            
            // Update user data
            $user["profile_picture"] = $upload_result["file_path"];
            $upload_success = "Profile picture updated successfully!";
        } else {
            $upload_error = $upload_result["message"];
        }
    } else {
        $upload_error = "Error uploading file. Please try again.";
    }
}

// Get user events
$user_events = get_user_events($_SESSION["user_id"], $conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Event Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1 class="sidebar-logo">Event Management</h1>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="active">
                        <a href="home.php">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="far fa-calendar-alt"></i>
                            <span>Events</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-ticket-alt"></i>
                            <span>My Tickets</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-plus-circle"></i>
                            <span>Create Event</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php if ($user["profile_picture"]): ?>
                            <img src="<?php echo $user["profile_picture"]; ?>" alt="<?php echo $user["first_name"] . " " . $user["last_name"]; ?>">
                        <?php else: ?>
                            <div class="avatar-placeholder">
                                <?php echo substr($user["first_name"], 0, 1) . substr($user["last_name"], 0, 1); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="user-details">
                        <p class="user-name"><?php echo $user["first_name"] . " " . $user["last_name"]; ?></p>
                        <p class="user-email"><?php echo $user["email"]; ?></p>
                    </div>
                </div>
                <a href="logout.php" class="btn btn-outline btn-block">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <header class="top-nav">
                <div class="mobile-logo">
                    <h1>Event Management</h1>
                </div>
                <div class="nav-actions">
                    <button class="btn-icon">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="btn-icon">
                        <i class="fas fa-cog"></i>
                    </button>
                    <div class="user-avatar mobile-only">
                        <?php if ($user["profile_picture"]): ?>
                            <img src="<?php echo $user["profile_picture"]; ?>" alt="<?php echo $user["first_name"] . " " . $user["last_name"]; ?>">
                        <?php else: ?>
                            <div class="avatar-placeholder">
                                <?php echo substr($user["first_name"], 0, 1) . substr($user["last_name"], 0, 1); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="page-header">
                    <h1>Welcome, <?php echo $user["first_name"]; ?>!</h1>
                    <p>Manage your profile and explore upcoming events</p>
                </div>

                <div class="tabs">
                    <div class="tab-header">
                        <button class="tab-btn active" data-tab="profile-tab">Profile</button>
                        <button class="tab-btn" data-tab="events-tab">My Events</button>
                        <button class="tab-btn" data-tab="settings-tab">Account Settings</button>
                    </div>

                    <!-- Profile Tab -->
                    <div class="tab-content active" id="profile-tab">
                        <div class="profile-grid">
                            <div class="profile-card">
                                <div class="card-header">
                                    <h2>Profile Picture</h2>
                                    <p>Upload or update your profile picture</p>
                                </div>
                                <div class="card-body">
                                    <div class="profile-picture-container">
                                        <div class="profile-picture">
                                            <?php if ($user["profile_picture"]): ?>
                                                <img src="<?php echo $user["profile_picture"]; ?>" alt="<?php echo $user["first_name"] . " " . $user["last_name"]; ?>">
                                            <?php else: ?>
                                                <div class="avatar-placeholder large">
                                                    <?php echo substr($user["first_name"], 0, 1) . substr($user["last_name"], 0, 1); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="profile-picture-overlay">
                                                <i class="fas fa-upload"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if ($upload_error): ?>
                                        <div class="alert alert-error">
                                            <?php echo $upload_error; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($upload_success): ?>
                                        <div class="alert alert-success">
                                            <?php echo $upload_success; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                                        <label for="profile_picture" class="file-upload-label">
                                            <div class="file-upload-btn">
                                                <i class="fas fa-upload"></i>
                                                <span>Choose a file</span>
                                            </div>
                                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden-file-input">
                                        </label>
                                        
                                        <button type="submit" class="btn btn-primary btn-block">Save Profile</button>
                                    </form>
                                </div>
                            </div>

                            <div class="profile-card">
                                <div class="card-header">
                                    <h2>Personal Information</h2>
                                    <p>Your registration details</p>
                                </div>
                                <div class="card-body">
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-user"></i>
                                                <span>First Name</span>
                                            </div>
                                            <p class="info-value"><?php echo $user["first_name"]; ?></p>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-user"></i>
                                                <span>Last Name</span>
                                            </div>
                                            <p class="info-value"><?php echo $user["last_name"]; ?></p>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-envelope"></i>
                                                <span>Email</span>
                                            </div>
                                            <p class="info-value"><?php echo $user["email"]; ?></p>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-phone"></i>
                                                <span>Contact Number</span>
                                            </div>
                                            <p class="info-value"><?php echo $user["contact_number"]; ?></p>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="far fa-calendar-alt"></i>
                                                <span>Event Interest</span>
                                            </div>
                                            <p class="info-value"><?php echo ucfirst($user["event_interest"]); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Events Tab -->
                    <div class="tab-content" id="events-tab">
                        <div class="card">
                            <div class="card-header">
                                <h2>My Upcoming Events</h2>
                                <p>Events you're registered for</p>
                            </div>
                            <div class="card-body">
                                <?php if (count($user_events) > 0): ?>
                                    <div class="events-list">
                                        <?php foreach ($user_events as $event): ?>
                                            <div class="event-item">
                                                <div class="event-image">
                                                    <?php if ($event["image"]): ?>
                                                        <img src="<?php echo $event["image"]; ?>" alt="<?php echo $event["title"]; ?>">
                                                    <?php else: ?>
                                                        <img src="images/placeholder.jpg" alt="<?php echo $event["title"]; ?>">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="event-info">
                                                    <h3><?php echo $event["title"]; ?></h3>
                                                    <div class="event-meta">
                                                        <div class="event-date">
                                                            <i class="far fa-calendar-alt"></i>
                                                            <span><?php echo date("F j, Y", strtotime($event["date"])); ?></span>
                                                        </div>
                                                        <div class="event-location">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                            <span><?php echo $event["location"]; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button class="btn btn-outline">View Details</button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="far fa-calendar-alt"></i>
                                        <p>You haven't registered for any events yet.</p>
                                        <a href="#" class="btn btn-primary">Browse Events</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Tab -->
                    <div class="tab-content" id="settings-tab">
                        <div class="card">
                            <div class="card-header">
                                <h2>Account Settings</h2>
                                <p>Manage your account preferences</p>
                            </div>
                            <div class="card-body">
                                <div class="settings-list">
                                    <div class="settings-item">
                                        <div class="settings-info">
                                            <label for="email-notifications">Email Notifications</label>
                                            <p>Receive email notifications about events</p>
                                        </div>
                                        <div class="toggle-switch">
                                            <input type="checkbox" id="email-notifications" class="toggle-input">
                                            <label for="email-notifications" class="toggle-label"></label>
                                        </div>
                                    </div>
                                    
                                    <div class="settings-item">
                                        <div class="settings-info">
                                            <label for="two-factor">Two-Factor Authentication</label>
                                            <p>Add an extra layer of security to your account</p>
                                        </div>
                                        <button class="btn btn-outline">Enable</button>
                                    </div>
                                    
                                    <div class="settings-item">
                                        <div class="settings-info">
                                            <label for="change-password">Password</label>
                                            <p>Change your password</p>
                                        </div>
                                        <button class="btn btn-outline">Update</button>
                                    </div>
                                    
                                    <div class="settings-divider"></div>
                                    
                                    <div class="settings-item">
                                        <button class="btn btn-danger">Delete Account</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/script.js"></script>
</body>
</html>