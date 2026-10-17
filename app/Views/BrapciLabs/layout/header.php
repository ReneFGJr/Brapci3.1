<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #efe6ff;
            overflow-x: hidden;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            height: 100vh;
            background: linear-gradient(180deg, #5b2be0, #6f42c1);
            color: #fff;
            position: fixed;
            inset: 0 auto 0 0;
            overflow-y: auto;
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
            min-width: 0;
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

        .link {
            text-decoration: none;
            color: #5b2be0;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                width: 64px;
                padding: 12px 8px !important;
                border-radius: 0 14px 14px 0;
            }

            .sidebar a {
                padding: 12px 0;
                overflow: hidden;
                font-size: 0;
                text-align: center;
                white-space: nowrap;
            }

            .sidebar a:first-child {
                padding: 4px;
            }

            .sidebar a i {
                font-size: 1.25rem;
            }

            .sidebar hr {
                margin: 12px 4px;
            }

            .content {
                margin-left: 64px;
                padding: 20px 16px;
            }

            .content > .d-flex:first-child {
                align-items: flex-start !important;
                gap: 12px;
            }

            .card-dashboard {
                border-radius: 14px;
            }
        }

        @media (max-width: 419.98px) {
            .content > .d-flex:first-child {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
