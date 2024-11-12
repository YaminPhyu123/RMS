<?php
session_start();

$db = new mysqli('localhost', 'root', '', 'project');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
 

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $num_guests = $_POST['num_guests'];
    $reservation_date = $_POST['reservation_date'];
    $reservation_time = $_POST['reservation_time'];
   
 
    // $_SESSION['reservation_time']= isset($_POST['reservation_time']) ? $_POST['reservation_time'] : null;

    $special_requests = $_POST['special_requests'];
    $pre_choose = isset($_POST['pre_choose']) ? 'yes' : 'no'; // Check if pre_choose checkbox is checked

    // Check  the selected time and table no is available
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM reservations WHERE reservation_date = ? AND reservation_time = ?");
    $stmt->bind_param("ss", $reservation_date, $reservation_time);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];

    $stmt = $db->prepare("SELECT COUNT(*) as tcount FROM tables");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $tcount = $row['tcount'];
    $tc=$tcount-3; // -3 ka pyan pyin mhar

    if ($count>$tc) {
        $_SESSION['message'] = "The selected time slot is already fully booked. Please choose another time.";
        if (isset($_SESSION['message'])) {
            echo '<div style="text-align: center; color: white; background-color: #ff3333; padding: 10px; margin-top: 10px; border-radius: 5px;">' . $_SESSION['message'] . '</div>';
            
            // Clear the message
            unset($_SESSION['message']);
        }
    } else {
        // Insert reservation into database
        $stmt = $db->prepare("INSERT INTO reservations (name, email, phone, num_guests, reservation_date, reservation_time, special_requests, pre_choose) VALUES (?,?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssissss",$name, $email, $phone, $num_guests, $reservation_date, $reservation_time, $special_requests, $pre_choose);
        if ($stmt->execute()) {
            // Get the inserted reservation_id
            $reservation_id = mysqli_insert_id($db);
            $_SESSION['reservation_id'] = $reservation_id; // Store reservation_id in session
            //prechoose htar lr check loke
            
            if( $pre_choose == 'yes') {
                $_SESSION['message'] = "Reservation successful and now choose pre-selected foods!";
                $_SESSION['preorder']='preorder';
                echo '<div style="text-align: center; color: white; background-color: #4CAF50; padding: 10px; margin-top: 10px; border-radius: 5px;">' . $_SESSION['message'] . '</div>';
                unset($_SESSION['message']);
                
                echo '<script>
                        setTimeout(function() {
                            window.location.href = "foods.php";
                        }, 1000); // Redirect after 1 seconds
                      </script>';
                
            }else{
                $_SESSION['message'] = "Reservation successful!";
                echo '<div style="text-align: center; color: white; background-color: #4CAF50; padding: 10px; margin-top: 10px; border-radius: 5px;">' .$_SESSION['message'] . '</div>';
                unset($_SESSION['message']);
                
                echo '<script>
                        setTimeout(function() {
                            window.location.href = "index.php";
                        }, 2000); // Redirect after 1seconds
                      </script>';
                
              
            }
            
            } else {
            $_SESSION['message'] = "Reservation failed.";
            if (isset($_SESSION['message'])) {
                echo '<div style="text-align: center; color: white; background-color: #ff3333; padding: 10px; margin-top: 10px; border-radius: 5px;">' . $_SESSION['message'] . '</div>';
                
                // Clear the message
                unset($_SESSION['message']);
            }
        }
    }

}

// Function to fetch reserved times for a specific date
function getReservedTimes($date) {
    global $db;
    $stmt = $db->prepare("SELECT reservation_time FROM reservations WHERE reservation_date = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservedTimes = [];
    while ($row = $result->fetch_assoc()) {
        $reservedTimes[] = $row['reservation_time'];
    }
    return $reservedTimes;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Form</title>
    <link rel="stylesheet" href="./a.css">
    <style>
         .warning-text {
            color: red;
            font-size: 12px;
            font-style: italic;
        }
    </style>
   
</head>
<body>
<button class="btn btn-custom btn-success btn-outline-primary" style="position: absolute; top: 40px; left: 20px; padding: 10px 20px; font-size: 16px;color: #fff; border: none; border-radius: 4px;">
<a href="#" style="color: inherit; text-decoration: none;" onclick="window.history.back();">Back</a>

</button>

<div class="container">
    <h2>Table Reservation</h2>
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <input type="hidden" name="submit" value="true">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name"><br>

        <label for="email">Email:</label>
<input type="email" id="email" name="email"><br>
<span class="warning-text" id="emailError" style="color:red;"></span><br>


        <label for="phone">Phone:</label>    <span class="warning-text">required</span>
<input type="tel" id="phone" name="phone" 
       pattern="^(09)(2|4|5|7|8|9|6)\d{8}$|^(\+?959)(2|4|5|7|8|9|6)\d{6}$|^01\d{7}$|^063\d{6,7}$" 
       placeholder="Enter a valid Myanmar phone number" required>
<span id="phoneError" style="color:red;"></span><br><br>

    

        <label for="num_guests">Number of Guests:</label>  <span class="warning-text">required</span>
        <input type="number" id="num_guests" name="num_guests" min="1" required><br>

        <label for="reservation_date">Date:</label>  <span class="warning-text">required</span>
        <input type="date" id="reservation_date" name="reservation_date" required><br><br>

        <label for="reservation_time">Time:</label>  <span class="warning-text">required</span>
        <select id="reservation_time" name="reservation_time" required>
            <?php
      
            $startTime = strtotime('06:00:00');
            $endTime = strtotime('23:00:00');
            $interval = 1800; 
            while ($startTime <= $endTime) {
                echo '<option value="' . date('H:i:s', $startTime) . '">' . date('h:i A', $startTime) . '</option>';
                $startTime += $interval;
            }
            ?>
            
        </select><br>

        <label for="special_requests">Special Requests:</label>
        <textarea id="special_requests" name="special_requests" rows="4"></textarea><br><br>

        <!-- Checkbox for pre-choose foods -->
        <input type="checkbox" id="pre_choose" name="pre_choose" value="yes">
        <label for="pre_choose">Pre-choose Foods</label><br><br>

        <button type="submit">Submit Reservation</button>
    </form>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message <?= ($_SESSION['message'] == 'Reservation successful!') ? 'success' : 'error' ?>">
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message']); 
        ?>
    <?php endif; ?>
    
</div>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.getElementById('reservation_date');
            const timeSelect = document.getElementById('reservation_time');
            dateInput.setAttribute('min', today);

            // Check if the selected date is today on page load
            if (dateInput.value === today) {
                updateTimesForToday();
            }

            dateInput.addEventListener('change', function() {
                if (this.value === today) {
                    updateTimesForToday();
                } else {
                    resetTimes();
                }
            });

            function updateTimesForToday() {
                const now = new Date();
                const currentHour = now.getHours();
                const currentMinutes = now.getMinutes();
                const options = timeSelect.options;
                
                for (let i = 0; i < options.length; i++) {
                    const [hours, minutes] = options[i].value.split(':');
                    if (parseInt(hours) < currentHour || (parseInt(hours) === currentHour && parseInt(minutes) <= currentMinutes)) {
                        options[i].style.display = 'none';
                    } else {
                        options[i].style.display = '';
                    }
                }

                // Set the first available time as selected
                for (let i = 0; i < options.length; i++) {
                    if (options[i].style.display !== 'none') {
                        options[i].selected = true;
                        break;
                    }
                }
            }

            function resetTimes() {
                const options = timeSelect.options;
                for (let i = 0; i < options.length; i++) {
                    options[i].style.display = '';
                }
                // Reset to the first option
                timeSelect.selectedIndex = 0;
            }
        });

        document.getElementById("email").addEventListener("input", function() {
    const emailInput = document.getElementById("email").value;
    const emailError = document.getElementById("emailError");


    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  
    const specificEmailPattern = /^[a-zA-Z0-9._%+-]+@(e|g)mail\.com$/;


    if (!emailPattern.test(emailInput)) {
        emailError.textContent = "Invalid email format.";
    } else if (!specificEmailPattern.test(emailInput)) {
        emailError.textContent = "Please enter a valid email address or a Gmail";
    } else {
        emailError.textContent = ""; // Clear error message
    }
});


    </script>
</body>
</html>
