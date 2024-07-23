<?php
// require 'test.php';
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "botiga";

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $database);


    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT id, woo_key, secret_key, url , status FROM wp_api_data";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " Key: " . $row["woo_key"]. "<br> Secret Key:  " . $row["secret_key"]. "<br> Url: ". $row["url"]. "<br> Status:" .$row["status"]. "<br>";
      }
    } else {
      echo "0 results";
    }
    // Close the connection
    $conn->close();
?>