<?php
require 'vendor/autoload.php'; 
require_once "vadersentiment.php";


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// from the environment variable
$csvFilePath = $_ENV['CSV_FILE_PATH'];

$fhandle = fopen($csvFilePath, 'r');

// Skip the first line (header)
$header = fgetcsv($fhandle, 1000, ',');

// Read CSV data
$all_record_arr = [];
while (($data = fgetcsv($fhandle, 1000, ',')) !== FALSE) {
    $all_record_arr[] = $data;
}
fclose($fhandle);

// Initialize variables to hold the most positive and most negative descriptions
$most_positive_description = "";
$most_negative_description = "";
$highest_positive_score = -1;
$lowest_negative_score = 1;

foreach ($all_record_arr as $record) {
    
    $product_name = $record[0];
    $description = $record[1];

    // Analyze sentiment of the description
    $sentimenter = new SentimentIntensityAnalyzer(); 
    $sentiment = $sentimenter->getSentiment($description);

    // Check if sentiment score is available
    if (isset($sentiment['compound'])) {
        $sentiment_score = $sentiment['compound'];

        // Update most positive and most negative descriptions
        if ($sentiment_score > $highest_positive_score) {
            $highest_positive_score = $sentiment_score;
            $most_positive_description = $description;
            $most_positive_product = $product_name;
        }
        if ($sentiment_score < $lowest_negative_score) {
            $lowest_negative_score = $sentiment_score;
            $most_negative_description = $description;
            $most_negative_product = $product_name;
        }
    } else {
        // Handle case where sentiment score is not available
    }
}

// Print/render the product names, descriptions, and their sentiment scores
?>
<!DOCTYPE html>
<html>
<head>
    <title>CSV Data</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Case Study</h1>

    <h1>Most Positive Description</h1>
    <p>Product Name: <?php echo $most_positive_product ?? 'N/A'; ?></p>
    <p>Description: <?php echo $most_positive_description ?? 'N/A'; ?></p>
    <p>Sentiment Score: <?php echo $highest_positive_score ?? 'N/A'; ?></p>

    <h1>Most Negative Description</h1>
    <p>Product Name: <?php echo $most_negative_product ?? 'N/A'; ?></p>
    <p>Description: <?php echo $most_negative_description ?? 'N/A'; ?></p>
    <p>Sentiment Score: <?php echo $lowest_negative_score ?? 'N/A'; ?></p>

    <br><br>
   <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($all_record_arr as $record): ?>
                <tr>
                    <td><?php echo $record[0]; ?></td>
                    <td><?php echo $record[1]; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    
</body>
</html>
