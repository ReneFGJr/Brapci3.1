<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #efe6ff;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #5b2be0, #6f42c1);
            color: #fff;
            position: fixed;
            border-radius: 0 20px 20px 0;
        }

        .sidebar a {
            color: #ddd;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-radius: 10px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,.15);
            color: #fff;
        }

        .content {
            margin-left: 270px;
            padding: 30px;
        }

        .card-dashboard {
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,.1);
        }

        .fake-bars span {
            width: 8px;
            border-radius: 4px;
            background: #ddd;
            display: inline-block;
            margin: 0 3px;
        }

        .fake-bars span.active {
            background: #5b2be0;
        }

        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ccc;
        }
    </style>
</head>
<body>