<?php
// Database config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ss";

$conn = new mysqli($servername, $username, $password, $dbname, 4306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$contact_count = $conn->query("SELECT COUNT(*) as count FROM contact_queries")->fetch_assoc()['count'];

$career_count = $conn->query("SELECT COUNT(*) as count FROM career")->fetch_assoc()['count'];

$labels = [];
$visitor_data = [];

$today = new DateTime();
$days = [];

for ($i = 6; $i >= 0; $i--) {
    $date = (clone $today)->modify("-$i days");
    $formatted = $date->format('Y-m-d');
    $label = $date->format('D');
    $days[$formatted] = $label;
}

$placeholders = "'" . implode("','", array_keys($days)) . "'";
$query = "SELECT visit_date, count FROM daily_visits WHERE visit_date IN ($placeholders)";
$result = $conn->query($query);

$counts = [];
while ($row = $result->fetch_assoc()) {
    $counts[$row['visit_date']] = (int)$row['count'];
}

foreach ($days as $date => $label) {
    $labels[] = $label;
    $visitor_data[] = $counts[$date] ?? 0;
}

$tot_count = "SELECT SUM(count) AS tot_visit FROM daily_visits";
$Cres = mysqli_query($conn, $tot_count);
$Crow = mysqli_fetch_assoc($Cres);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" type="image/x-icon" href="logo.png">
</head>
<body class="p-4">
    <h2>Admin Dashboard</h2>

    <div class="my-4">
        <canvas id="visitorChart" height="100"></canvas>
    </div>
    <p class="tot_visits">Total Visits : <?php echo $Crow['tot_visit'] ?? '0'; ?></p>

    <div class="d-flex gap-3">
        <button id="showContact" class="btn btn-primary position-relative">
            Contact Queries
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $contact_count; ?>
            </span>
        </button>

        <button id="showCareer" class="btn btn-success position-relative">
            Careers
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $career_count; ?>
            </span>
        </button>
    </div>
    
    <div id="dataTable" class="mt-4"></div>

    <script>
        // Visitors Chart
        const ctx = document.getElementById('visitorChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Visitors Per Day',
                    data: <?php echo json_encode($visitor_data); ?>,
                    fill: true,
                    borderColor: 'blue',
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true
            }
        });

        // AJAX: Contact Queries
        document.getElementById('showContact').addEventListener('click', function(){
            fetch('get_contact.php')
            .then(response => response.json())
            .then(data => {
                let table = '<h4>Contact Queries</h4><table class="table table-bordered"><tr><th>ID</th><th>Full Name</th><th>Phone</th><th>Email</th><th>Idea</th><th>Submitted At</th></tr>';
                data.forEach(row => {
                    table += `<tr>
                        <td>${row.id}</td>
                        <td>${row.full_name}</td>
                        <td>${row.phone}</td>
                        <td>${row.email}</td>
                        <td>${row.idea}</td>
                        <td>${row.submitted_at}</td>
                    </tr>`;
                });
                table += '</table>';
                document.getElementById('dataTable').innerHTML = table;
            });
        });

        // AJAX: Career Applications
        document.getElementById('showCareer').addEventListener('click', function(){
            fetch('get_careers.php')
            .then(response => response.json())
            .then(data => {
                let table = '<h4>Career Applications</h4><table class="table table-bordered"><tr><th>ID</th><th>Name</th><th>Email</th><th>Job Type</th><th>Status</th><th>CV Link</th></tr>';
                data.forEach(row => {
                    table += `<tr>
                        <td>${row.id}</td>
                        <td>${row.name}</td>
                        <td>${row.email}</td>
                        <td>${row.jobtype}</td>
                        <td>${row.currstatus}</td>
                        <td><center><a href="${row.resume_path}" target="_blank"><i class="fa-solid fa-eye"></i></a></center></td>
                    </tr>`;
                });
                table += '</table>';
                document.getElementById('dataTable').innerHTML = table;
            });
        });
    </script>
</body>
</html>
