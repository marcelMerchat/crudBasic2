<?php
    session_start();
    require 'timeout.php';
    session_destroy();
    header("Location: index.php");
