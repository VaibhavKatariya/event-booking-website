<?php
// Include the database connection
include 'db.php'; // Ensure this path points to your `db.php`

// Fetch all events from the database
$stmt = $conn->prepare("SELECT id, name, event_date, venue, description FROM events ORDER BY event_date ASC");
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConfiConcert - Explore Events</title>
    <link rel="icon" href="https://cdn.discordapp.com/attachments/909763979704930326/1310820371779551253/34764-removebg-preview.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/events.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand"><a class="HeaderTitle" href="index.php">ConfiConcert</a></div>
            <ul class="navbar-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="user/user_dash.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Page Title -->
    <div class="page-header">
        <div class="container">
            <h1>Explore Concerts</h1>
            <p>Find and book the latest concerts happening near you!</p>
        </div>
    </div>

    <!-- Events Section -->
    <div class="events-container">
        <div class="container">
            <?php if (count($events) > 0): ?>
                <table class="events-table">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?= htmlspecialchars($event['name']) ?></td>
                                <td><?= htmlspecialchars(date('F j, Y', strtotime($event['event_date']))) ?></td>
                                <td><?= htmlspecialchars($event['venue']) ?></td>
                                <td><?= htmlspecialchars($event['description']) ?></td>
                                <td>
                                    <a href="php/book_ticket.php?event_id=<?= $event['id'] ?>" class="btn">Book Now</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-events">No events are currently available. Check back later!</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2024 ConfiConcert. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

