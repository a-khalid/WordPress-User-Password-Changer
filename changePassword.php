<?php
require_once 'wp-load.php';
global  $wpdb;
$table_name = $wpdb->prefix.'users';
$current_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$current_file= __FILE__;
$current_file= explode('/',$current_file);
$count = count($current_file) - 1;
$current_file = $current_file[$count];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Your Password</title>
    <style>
        *{
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        body{
            margin: 0;
            padding: 0 0 42px;
            font-family: Tahoma, Arial, sans-serif;
            font-size: 13px;
            position: relative;
            min-height: 100vh;
        }
        .wrap{
            width: 950px;
            margin: auto;
            max-width: 100%;
        }
        header{
            text-align: center;
            padding: 20px 0;
            border-bottom:#EEEEEE solid 2px;
            margin-bottom: 20px;;
        }
        header h1{
            font-weight: bold;
            text-transform: uppercase;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
        header h1 a{
            color: grey;
        }
        header h1 a:hover{
            color: black;
        }
        a{
            text-decoration: none;
        }
        table{
            margin: 0;
            padding: 0;
            width: 100%;
            border-collapse:separate;
            border-spacing:0
        }
        table td ,table th{
            border:#EEEEEE solid 1px;
            padding: 5px;
        }
        table th{
            background: #a63408;
            color: #fff;
            border:#a63408 solid 1px;
            text-transform: uppercase;
            font-weight: normal;
        }
        table tr:nth-child(even) td{
            background: #efefef;
            border-color: #d5d5d5;
        }
        table tr td a{
            color: #a63408;
        }
        .text-center{
            text-align: center;
        }
        h1,h2,h3,h4,h5{
            font-weight: normal;
        }
        *:focus{
            outline: 0;
        }
        .opps{
            text-align: center;
            color:red;
            font-size: 18px;
        }
        .form{
            width: 420px;
            max-width: 100%;
            margin:10px auto;
        }
        .form label{
            display: block;
            cursor: pointer;
            font-weight: bold;
        }
        .form input{
            height: 43px;
            border:#EEEEEE solid 1px;
            padding: 5px;
            width: 100%;
        }
        .form .form-row{
            margin-bottom: 10px;
        }
        .form input[type="submit"]{
            cursor: pointer;
            background: #a63408;
            font-size: 16px;;
            color: #fff;
            border: 0;
        }
        .form input[type="submit"]:focus{
            -ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=180, Color=#039d48)";/*IE 8*/
            -moz-box-shadow: inset 0 3px 3px #8d2d08;/*FF 3.5+*/
            -webkit-box-shadow: inset 0 3px 3px #8d2d08;/*Saf3-4, Chrome, iOS 4.0.2-4.2, Android 2.3+*/
            box-shadow: inset 0 3px 3px #8d2d08;/* FF3.5+, Opera 9+, Saf1+, Chrome, IE10 */
            filter: progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=180, Color=#8d2d08); /*IE 5.5-7*/

        }
        footer{
            position: absolute;
            bottom: 0;
            text-align: center;
            width: 100%;
            left: 0;
        }
        .changed{
            color: green;
        }
    </style>
</head>
<body>

<div class="wrap">
    <header>
        <h1><a href="<?php echo $current_file ?>">User Changer Password</a></h1>
    </header>
    <?php
    if(empty($_GET) and empty($_POST)){
        $get_all_users = $wpdb->get_results("SELECT * FROM $table_name");
        ?>
        <h2 class="text-center">Users</h2>
        <table class="users">
            <tr>
                <th style="width: 40px">ID</th>
                <th>Nicename</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
            <?php foreach($get_all_users as $user) {?>
            <tr>
                <td><?php echo $user->ID; ?></td>
                <td><?php echo $user->user_nicename; ?></td>
                <td><?php echo $user->user_email; ?></td>
                <td class="text-center"><a href="<?php echo $current_url; ?>?change=1&user=<?php echo $user->ID; ?>">Change Password</a></td>
            </tr>
            <?php } ?>
        </table>
    <?php } ?>


    <?php
    if(isset($_GET['change']) and $_GET['change']){
        $user_id = intval($_GET['user']);
        $get_user_by_id = $wpdb->get_results("SELECT * FROM $table_name WHERE ID = $user_id");
        if(empty($get_user_by_id)){
            wp_die("<p class='opps'>Opps!.. We can't find any users</p>");
        }
        $current_url = explode('&',$current_url);
        $current_url = explode('?',$current_url[0]);
    ?>
        <h3 class="text-center">Nickname : <?php echo $get_user_by_id[0]->user_nicename ?></h3>
        <form action="<?php echo $current_url[0];?>" method="post" class="form">
            <input type="hidden" name="userid" value="<?php echo $get_user_by_id[0]->ID ?>">
            <div class="form-row">
                <label for="password">Password :</label>
                <div class="input">
                    <input type="password" id="password" value="" name="password">
                </div>
            </div>
            <div class="form-row">
                <input type="submit" class="btn" value="Change">
            </div>
        </form>
    <?php } ?>

    <?php if(!empty($_POST) and isset($_POST['password'])){
        require_once ABSPATH . WPINC . '/class-phpass.php';
        $hasher = new PasswordHash( 8, true );
        $pass =$hasher->HashPassword( wp_unslash( $_POST['password'] ) );
        $password = $wpdb->update(
            $table_name,
            array('user_pass'=>$pass),
            array('id'=>$_POST['userid'])
        );
        if($password){
            echo '<h2 class="text-center changed">Password Has been Changed ..!</h2>';
        }else{
            echo "<h2 class='text-center opps'>Opps.. we can't change password </h2>";
        }

    } ?>

</div>
<footer>
    <p>By Ahmed Khalid . Follow Me <a href="http://www.twitter.com/@ia_khalid" target="_blank">@ia_khalid</a></p>
</footer>
</body>
</html>
