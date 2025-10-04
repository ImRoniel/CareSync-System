<!DOCTYPE html>
<html>
    <body>
        <?
        //VARIABLE FOR CONNECTING TO SERVER 
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = " caresync_db";

        
        $conn = mysqli_connect($servername, $username, $password, $dbname);

        //CONDTION FOR ERROR
        if(!$conn){
            die("Connection Faild!!" . mysqli_connect_error());
        } else{
            echo "Connected Successfully!";
        }
        ?>
    </body>
</html>
