<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Our PiggyBank</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 24px;
        }

        /* Card layout */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 12px;
        }

        .read-card {
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .header3 {
            margin: 0 0 8px 0;
            font-size: 3rem;
        }

        .read-card h4 {
            margin: 0 0 8px 0;
            font-size: 3rem;
        }

        .read-card .meta {
            font-size: 2.5rem;
            color: #555;
            margin-bottom: 8px;
        }

        .credit-1 .read-card {
            background: #e6ffed;
            color: #063;
        }

        .credit--1 .read-card {
            background: #ffe6e6;
            color: #900;
        }

        /* Mobile: stacked single column with larger spacing */
        @media (max-width: 1080px) {
            .cards {
                grid-template-columns: 1fr;
            }

            .header3 {
                font-size: 4rem;
            }

            .read-card h4 {
                font-size: 3rem;
            }

            .read-card {
                padding: 16px;
            }

            .read-card .meta {
                font-size: 2.5rem;
            }
        }
    </style>
</head>

<body>
<?php
  /* home path for your kids will be added here
   example:
  require __DIR__ . '/alice/home.php';
  echo '<br>';
  require __DIR__ . '/victor/home.php'; */
?>

</body>

</html>
