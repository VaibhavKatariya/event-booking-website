<?php
// Include the database connection
include '../db.php'; // Adjust the path if necessary

// Initialize variables
$message = "";
$event_id = $_GET['event_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission
    $username = trim($_POST['email'] ?? '');
    $event_id = trim($_POST['event_id'] ?? '');

    // Validate inputs
    if (empty($username) || empty($event_id)) {
        $message = "All fields are required!";
    } elseif (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format!";
    } else {
        // Check if the username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];

            // Generate a unique ticket code
            $ticket_code = "TCK" . rand(10000, 99999);

            // Insert the ticket into the `tickets` table
            $stmt_ticket = $conn->prepare("INSERT INTO tickets (user_id, event_id, ticket_code) VALUES (?, ?, ?)");
            $stmt_ticket->bind_param("iis", $user_id, $event_id, $ticket_code);

            if ($stmt_ticket->execute()) {
                // Send confirmation email
                $subject = "Your Ticket Confirmation";
                $message_body = "Hello $username,\n\nYour ticket has been successfully booked!\n\nEvent ID: $event_id\nTicket Code: $ticket_code\n\nThank you for choosing ConfiConcert!";
                $headers = "From: no-reply@ConfiConcert.com";

                // Send confirmation email
                if (mail($username, $subject, $message_body, $headers)) {
                    $message = "Ticket booked successfully! A confirmation email has been sent.";

                    // Delay for 5 seconds before redirecting
                    header("Refresh: 5; url=/new/index.php"); // Adjust the path to your home page
                    exit;
                } else {
                    $message = "Ticket booked, but the confirmation email could not be sent.";
                }
            } else {
                $message = "Error booking ticket: " . $stmt_ticket->error;
            }
            $stmt_ticket->close();
        } else {
            $message = "Email address not registered!";
        }
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConfiConcert - Book Ticket</title>
    <link rel="icon" href="https://cdn.discordapp.com/attachments/909763979704930326/1310820371779551253/34764-removebg-preview.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/book_ticket.css">
</head>
<body>
    <div class="form-container">
        <form action="" method="POST">
            <h1>Book Your Ticket</h1>
            <?php if (!empty($message)): ?>
                <div class="<?= strpos($message, 'successfully') !== false ? 'success-message' : 'error-message' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            <input type="hidden" name="event_id" value="<?= htmlspecialchars($event_id) ?>">
            <div class="form-group">
                <label for="username">Email Address:</label>
                <input 
                    type="email" 
                    id="username" 
                    name="username" 
                    placeholder="Enter your email address" 
                    required
                >
            </div>
            <button type="submit">Book Now</button>
        </form>
    </div>
</body>
</html>

