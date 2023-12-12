<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>IIR Final Project</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body>
    <?php
    $sampleData = [
        [
            'id' => 1,
            'title' => 'Sample Title 1',
            'number_citations' => 10,
            'authors' => 'Author A, Author B',
            'abstract' => 'This is the abstract for the first sample entry.',
            'similarity' => 0.85
        ],
        [
            'id' => 2,
            'title' => 'Sample Title 2',
            'number_citations' => 15,
            'authors' => 'Author C, Author D',
            'abstract' => 'This is the abstract for the second sample entry.',
            'similarity' => 0.92
        ],
        [
            'id' => 3,
            'title' => 'Sample Title 3',
            'number_citations' => 8,
            'authors' => 'Author E, Author F',
            'abstract' => 'This is the abstract for the third sample entry.',
            'similarity' => 0.78
        ]
    ];
    ?>
    <form method="POST" action="">
        <div class="page-type">Home|Crawling</div>
        <div class="page-title">Data Crawling from Google Scholar</div>
        <div class="crawl-section">
            <span class="text">Keyword:</span>
            <input type="text" class="crawl-input" name="keyword" placeholder="Input text here">
            <button type="submit" name="crawl_button" class="crawl-button">Crawl</button>
        </div>
        <script src="" async defer></script>
        <?php
        if (isset($_POST['crawl_button'])) {
            foreach ($sampleData as $data) {
                echo "<div class='result' style='font-family: Roboto, sans-serif;'>";
                echo "<p><strong>Title:</strong> <br>" . $data['title'] . "</p>";
                echo "<p><strong>Authors:</strong> <br>" . $data['authors'] . "</p>";
                echo "<p><strong>Abstract:</strong> <br>" . $data['abstract'] . "</p>";
                echo "-----------------------------";
                echo "<p><strong>Number of Citations:</strong> " . $data['number_citations'] . "</p>";
                if ($index !== count($sampleData) - 1) {
                    echo "<div class='separator'></div>";
                }
                echo "</div>";
            }
        }
        ?>
    </form>
</body>

</html>