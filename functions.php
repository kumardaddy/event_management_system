<?php
session_start();

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email format
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

// Function to upload profile picture
function upload_profile_picture($file) {
    $target_dir = "uploads/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Check if file was actually uploaded
    if (!isset($file) || $file["error"] !== UPLOAD_ERR_OK) {
        return ["success" => false, "message" => "No file was uploaded or an error occurred."];
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if image file is a actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["success" => false, "message" => "File is not an image."];
    }
    
    // Check file size (limit to 5MB)
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "File is too large. Maximum size is 5MB."];
    }
    
    // Allow certain file formats
    if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg" && $file_extension != "gif" ) {
        return ["success" => false, "message" => "Only JPG, JPEG, PNG & GIF files are allowed."];
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "file_path" => $target_file];
    } else {
        return ["success" => false, "message" => "There was an error uploading your file."];
    }
}

// Function to get user data
function get_user_data($user_id, $conn) {
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in get_user_data: " . $e->getMessage());
        return false;
    }
}

// Function to get events
function get_events($limit = null, $category = null, $conn) {
    try {
        $sql = "SELECT * FROM events";
        $params = [];
        
        if ($category) {
            $sql .= " WHERE category = :category";
            $params[':category'] = $category;
        }
        
        $sql .= " ORDER BY date ASC";
        
        if ($limit && is_numeric($limit)) {
            $sql .= " LIMIT :limit";
            $params[':limit'] = (int)$limit;
        }
        
        $stmt = $conn->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            if ($key === ':limit') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in get_events: " . $e->getMessage());
        return [];
    }
}

// Function to get user events
function get_user_events($user_id, $conn) {
    try {
        $stmt = $conn->prepare("
            SELECT e.* 
            FROM events e
            JOIN user_events ue ON e.id = ue.event_id
            WHERE ue.user_id = :user_id
            ORDER BY e.date ASC
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in get_user_events: " . $e->getMessage());
        return [];
    }
}

// Function to format date
function format_date($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

// Function to get event categories with counts
function get_categories_with_counts($conn) {
    try {
        $stmt = $conn->prepare("
            SELECT category, COUNT(*) as count 
            FROM events 
            GROUP BY category 
            ORDER BY count DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in get_categories_with_counts: " . $e->getMessage());
        return [];
    }
}

// Function to handle errors gracefully
function handle_error($message, $log = true) {
    if ($log) {
        error_log($message);
    }
    return "<div class='alert alert-error'>{$message}</div>";
}

// Function to handle success messages
function handle_success($message) {
    return "<div class='alert alert-success'>{$message}</div>";
}

// Function to check if remember me cookie is valid
function check_remember_me($conn) {
    if (isset($_COOKIE['remember_token']) && !is_logged_in()) {
        // In a real application, you would validate the token against a database
        // This is a simplified example
        $token = $_COOKIE['remember_token'];
        
        try {
            $stmt = $conn->prepare("SELECT * FROM user_tokens WHERE token = :token AND expires_at > NOW()");
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $token_data = $stmt->fetch(PDO::FETCH_ASSOC);
                $user_id = $token_data['user_id'];
                
                // Get user data
                $user = get_user_data($user_id, $conn);
                
                if ($user) {
                    // Set session variables
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["user_name"] = $user["first_name"] . " " . $user["last_name"];
                    
                    // Redirect to home page if on login page
                    $current_page = basename($_SERVER['PHP_SELF']);
                    if ($current_page === 'login.php') {
                        header("Location: home.php");
                        exit();
                    }
                }
            }
        } catch (PDOException $e) {
            error_log("Database error in check_remember_me: " . $e->getMessage());
        }
    }
}
?>