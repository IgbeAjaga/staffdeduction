<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            color: #000000;
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }
        h2 {
            color: #000000;
            font-size: 24px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #000000;
            padding-bottom: 5px;
        }
        p {
            line-height: 1.6;
            color: #333;
            margin: 10px 0;
            font-size: 16px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th, .table td {
            border: 1px solid #000000;
            padding: 10px;
            text-align: left;
        }
        .table th {
            background-color: #000000;
            color: white;
            font-weight: bold;
        }
        .salutation {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        ul {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            margin: 10px 0;
            padding-left: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    @yield('content')
</div>
</body>
</html>
