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
    $sampleLink = [
        [
            'id' => "Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro vel, aut quidem sed necessitatibus voluptas veritatis quisquam hic nesciunt maiores nulla sapiente distinctio perspiciatis. Mollitia veniam officiis nam ipsum culpa.",
            'link' => 'https://picsum.photos/200'
        ],
        [
            'id' => "Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro vel, aut quidem sed necessitatibus voluptas veritatis quisquam hic nesciunt maiores nulla sapiente distinctio perspiciatis. Mollitia veniam officiis nam ipsum culpa.",
            'link' => 'https://picsum.photos/200'
        ],
        [
            'id' => "Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro vel, aut quidem sed necessitatibus voluptas veritatis quisquam hic nesciunt maiores nulla sapiente distinctio perspiciatis. Mollitia veniam officiis nam ipsum culpa.",
            'link' => 'https://picsum.photos/200'
        ]
    ];
    ?>
    <form method="POST" action="">
        <div class="page-type">Home|Search</div>
        <div class="page-title">Welcome to Scientific Journals Search Engine</div>
        <div class="search-section">
            <span class="text">Keyword:</span>
            <input type="text" class="search-input" name="keyword" placeholder="Input text here">
            <button type="submit" name="search_button" class="search-button">Search</button>

            <div class="radio-buttons">
                <input type="radio" id="minkowski" name="distance_metric" value="Minkowski">
                <label for="minkowski">Minkowski</label>
                <input type="radio" id="cosine" name="distance_metric" value="Cosine">
                <label for="cosine">Cosine</label>
            </div>
        </div>
        <script src="" async defer></script>
        <?php
        if (isset($_POST['search_button'])) {
            $resultsPerPage = 2;
            $totalDataCount = count($sampleData);
            $totalPages = ceil($totalDataCount / $resultsPerPage);

            $currentpage = isset($_GET['page']) ? $_GET['page'] : 1;

            $startIndex = ($currentpage - 1) * $resultsPerPage;
            $endIndex = $startIndex + $resultsPerPage;
            $currentData = array_slice($sampleData, $startIndex, $resultsPerPage);

            echo "<h3 class='text-sub'>The Search Results</h3>";
            foreach ($currentData as $index => $data) {
                echo "<div class='result' style='font-family: Roboto, sans-serif;'>";
                echo "<div class='result-content'>";
                echo "<p><strong>Title:</strong> <br>" . $data['title'] . "</p>";
                echo "<p><strong>Authors:</strong> <br>" . $data['authors'] . "</p>";
                echo "<p><strong>Abstract:</strong> <br>" . $data['abstract'] . "</p>";
                echo "-----------------------------";
                echo "<p><strong>Number of Citations:</strong> " . $data['number_citations'] . "</p>";
                echo "</div>";
                if ($index !== count($sampleData) - 1) {
                    echo "<div class='separator'></div>";
                }
                echo "</div>";
            }

            echo "<div class='pagination'>";
            if ($currentpage > 1) {
                echo "<a href='?page=" . ($currentpage - 1) . "'>Previous</a>";
            }

            for ($i = 1; $i <= $totalPages; $i++) {
                echo "<a href='?page=" . $i . "'" . ($currentpage == $i ? " class='active'" : "") . ">" . $i . "</a>";
            }

            if ($currentpage < $totalPages) {
                echo "<a href='?page=" . ($currentpage + 1) . "'>Next</a>";
            }
            echo "</div>";

            echo "<div class='related-search'>";
            echo "<h3>Related Search</h3>";
            echo "<ul>";
            foreach ($sampleLink as $link) {
                $shortenedId = strlen($link['id']) > 30 ? substr($link['id'], 0, 30) . '...' : $link['id'];
                //$shortenedId = strlen($link['id']) > 30 ? str_replace(' ', '<br>', wordwrap($link['id'], 30)) : $link['id'];
                echo "<li><a href='" . $link['link'] . "'>" . $shortenedId . "</a></li>";
            }
            echo "</ul>";
            echo "</div>";
        }
        ?>
    </form>
</body>

</html>