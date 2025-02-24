<?php
// Konfigurasi Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ukk_todolist_salwa";

//Buat Koneksi
$conn = new mysqli($servername,$username,$password,$dbname);

//Check connectioon
if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}

//Buat Tabel Database Otomotis dengan Script PHP
$sql= "CREATE TABLE IF NOT EXISTS tasks(
id INT AUTO_INCREMENT PRIMARY KEY,
tasks_name VARCHAR(255)NOT NULL,
status_task ENUM('Biasa,'Cukup,'Penting') DEFAULT'Cukup,'
status_complated ENUM('Selesai,'BelumSelesai') DEFAULT'Belum Selesai',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
tasks_date DATE
)";

$conn->query($sql);

//Penambahan Data Baru
if($_SERVER['REQUEST_METHOD']=='POST'&& isset($_POST['add_task'])) {
   $task_name = $conn->real_escape_string($_POST['task_name']);
   $status_task = $conn->real_escape_string($_POST['status_task']);
   $status_completed = $conn->real_escape_string($_POST['status_completed']);
   $task_date = $conn->real_escape_string($_POST['task_date']);

   if (!empty($task_name) && !empty($status_task) && !empty($task_date)) {
    $stmt = $conn->prepare("INSERT INTO tasks (task_name, status_task, status_completed, task_date) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $task_name, $status_task, $status_completed, $task_date);
    $stmt->execute();
    $stmt->close();
}
}
//Update Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_task'])) {  // Fixed typo from 'issert' to 'isset'
    $id = (int)$_POST['task_id'];  // Casting to integer for safety
    $task_name = $conn->real_escape_string($_POST['task_name']);  // Fixed variable name from $tasks_name to $task_name
    $status_task = $conn->real_escape_string($_POST['status_task']);  // Fixed variable name from $status_tasks to $status_task
    $status_completed = $conn->real_escape_string($_POST['status_completed']);
    $task_date = $conn->real_escape_string($_POST['task_date']);

    // Check that the essential fields are not empty
    if (!empty($task_name) && !empty($status_task) && !empty($task_date)) {
        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("UPDATE tasks SET task_name = ?, status_task = ?, status_completed = ?, task_date = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $task_name, $status_task, $status_completed, $task_date, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Please fill in all required fields.";
    }
}


//Hadle deleting a task
if(isset($_GET['delete'])){
    $id = (int)$_GET['DELETE'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();
}

//Fetsh all take
$result =$conn->query("SELECT*FROM tasks ORDER BY created_at DESC")
?>
<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-initial-scale=1.0">
        <link href="aset/style.css" rel="stylesheet">
        <title>To-Do List UKK RPL 2025 paket 2</title>
        <style>
            /Sama styling as before with minor updates for new"task_date"/
            *{
                margin:0;
                padding:0;
                box-sizing: border-box;
            }


            </style>
            <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width,initial-scale=1.0">
            <title>Aplikasi To Do List UKK Paket 2 RPL 2025</title>
        </head>
        <body>

        <h1>Aplikasi To-Do List UKK Paket 2 RPL 2025</h1>

        <!--from untuk menambah tugas-->
        <div class="from-container">
            <form method="POST"action="">
                <input type="text"name="task_name"placeholder="Add a new tasks todo list..."required>

                <select name="status_task"required>
                    <option value="Biasa">Biasa</option>
                    <option value="Cukup">Cukup</option>
                    <option value="Penting">Penting Sekali</option>
        </select>

        <select name="status_completed"required>
        <option value="Belum Selesai">Belum Selesai</option>
        <option value="Selesai">Selesai</option>
        </select>

        <input type="date"name="task_date"required>

        <button type="submit" name="add_task">Tambah List</button>
        </form>
        </div>
<!-- Menampilkan daftar tugas -->
<div class="tasks-list">
    <?php while($row = $result->fetch_assoc()):?>
        <div class="tasks-item" data-status="<?=$row['status_completed']?>">
            <!-- Edit Task Form -->
            <form method="POST" action="">
                <input type="hidden" name="task_id" value="<?=$row['id']?>">
                <input type="text" name="task_name" value="<?=htmlspecialchars($row['task_name']) ?>" required>

                <select name="status_task" required>
                    <option value="biasa" <?=$row['status_task']=='Biasa'?'selected':''?>>Biasa</option>
                    <option value="cukup" <?=$row['status_task']=='cukup'?'selected':''?>>Cukup</option>
                    <option value="penting" <?=$row['status_task']=='penting'?'selected':''?>>Penting</option>
                </select>

                <select name="status_completed" required>
                    <option value="belum selesai" <?=$row['status_completed']=='Belum Selesai' ?'selected':''?>>Belum Selesai</option>
                    <option value="selesai" <?=$row['status_completed']=='Selesai' ?'selected':''?>>Selesai</option>
                </select>

                <input type="date" name="task_date" value="<?=$row['task_date']?>" required>
                <button type="submit" name="edit_tasks">Edit</button>
            </form>

            <!-- Delete Tasks Form -->
            <form method="POST" action="" onsubmit="return confirm('Apakah yakin menghapus list?');">
                <input type="hidden" name="delete" value="<?=$row['id']?>">
                <button type="submit" name="delete_task">hapus</button>
            </form>

            <div class="task_date">Due Date: <?=date('F j,Y', strtotime($row['task_date']))?></div>
        </div>
    <?php endwhile;?>
</div>

</body>
</html>

<?php
$conn->close();
?>