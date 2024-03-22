<?php

function loadEmployees()
{
    $xml = simplexml_load_file('empList.xml');
    return $xml;
}

function saveEmployees($xml)
{
    $xml->asXML('empList.xml');
}

function displayEmployee($employee)
{
    echo "<h2>Employee Details</h2>";
    echo "Name: {$employee->name}<br>";
    echo "Phone: {$employee->phone}<br>";
    echo "Address: {$employee->address}<br>";
    echo "Email: {$employee->email}<br>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $employees = loadEmployees();
    if (isset($_POST['index']))
        $index = (int)$_POST['index'];

    switch ($action) {
        case 'insert':
            $newEmployee = $employees->addChild('employee');
            $newEmployee->addChild('name', $_POST['name']);
            $newEmployee->addChild('phone', $_POST['phone']);
            $newEmployee->addChild('address', $_POST['address']);
            $newEmployee->addChild('email', $_POST['email']);
            break;
        case 'update':
            if (isset($employees->employee[$index])) {
                $employees->employee[$index]->name = $_POST['name'];
                $employees->employee[$index]->phone = $_POST['phone'];
                $employees->employee[$index]->address = $_POST['address'];
                $employees->employee[$index]->email = $_POST['email'];
            }
            break;
        case 'delete':
            unset($employees->employee[$index]);
            if (count($employees->employee) > 1)
                $index = ($index + 1) % count($employees->employee);
            break;
        case 'search':
            $searchTerm = $_POST['search_term'];
            $foundEmployees = array();
            foreach ($employees->employee as $employee) {
                if (stripos($employee->name, $searchTerm) !== false) {
                    $foundEmployees[] = $employee;
                }
            }
            echo "<h2>Search Results</h2>";
            if (!empty($foundEmployees)) {
                foreach ($foundEmployees as $foundEmployee) {
                    displayEmployee($foundEmployee);
                    echo "<br>";
                }
            } else {
                echo "<p>No employee found matching the search term '{$searchTerm}'.</p>";
            }
            exit;
            break;
        case 'next':
            $index = $index + 1;
            break;
        case 'prev':
            $index = $index - 1;
            break;
    }

    saveEmployees($employees);

    header("Location: {$_SERVER['PHP_SELF']}?index=$index");
    exit;
}

$employees = loadEmployees();
$totalEmployees = count($employees->employee);

if (isset($_GET['index'])) {
    $index = (int)$_GET['index'];
} else {
    $index = 0;
}

$employee = isset($employees->employee[$index]) ? $employees->employee[$index] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #employeeForm, #searchForm {
            max-width: 400px;
            margin: 0 auto;
        }
    </style>
</head>
<body class="container">
    <div class='m-5'>
    <form id="employeeForm" method='post'>
    <input type='hidden' name='index' value='<?php echo $index; ?>'>
    
    <div class="form-group">
        <label for="name">Name:</label>
        <input type='text' class="form-control mb-2" name='name' id="name" placeholder='Enter name' value='<?php echo $employee ? $employee->name : ''; ?>'>
    </div>

    <div class="form-group">
        <label for="phone">Phone:</label>
        <input type='text' class="form-control mb-2" name='phone' id="phone" placeholder='Enter phone' value='<?php echo $employee ? $employee->phone : ''; ?>'>
    </div>

    <div class="form-group">
        <label for="address">Address:</label>
        <input type='text' class="form-control mb-2" name='address' id="address" placeholder='Enter address' value='<?php echo $employee ? $employee->address : ''; ?>'>
    </div>

    <div class="form-group">
        <label for="email">Email:</label>
        <input type='text' class="form-control mb-2" name='email' id="email" placeholder='Enter email' value='<?php echo $employee ? $employee->email : ''; ?>'>
    </div>
    <div class="text-center">
        <input type='submit' class="btn btn-primary mb-2" name='action' value='insert'>
        <input type='submit' class="btn btn-primary mb-2" name='action' value='update'>
        <input type='submit' class="btn btn-danger mb-2" name='action' value='delete'>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-secondary mb-2" name="action" value="prev" <?php echo $index == 0 || $totalEmployees == 0 ? 'disabled' : ''; ?>>Prev</button>
        <button type="submit" class="btn btn-secondary mb-2" name="action" value="next" <?php echo $index == $totalEmployees - 1 || $totalEmployees == 0 ? 'disabled' : ''; ?>>Next</button>
    </div>

</form>

<form id="searchForm" method='post'>
    <input type='text' class="form-control mb-2" name='search_term' placeholder='Search by name'>
    <div class="text-center">
        <input type='submit' class="btn btn-primary mb-2" name='action' value='search'>
    </div>
</form>

    </div>

</body>

