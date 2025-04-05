<?php
  require_once './UsersDAO.php';

  $userDAO = new UsersDAO();

  // Fetch all users
    $users = $userDAO->getById(13);
    print_r($users);
?>