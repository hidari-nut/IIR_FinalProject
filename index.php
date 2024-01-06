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

    $con = new mysqli("localhost", "root", "", "iirfinal");
    if ($con->connect_errno) {
        echo "Failed to connect to MySQL:" . $con->connect_error . "<br>";
    } else {
        // echo "Connection Success. <br>";
    }

    function sanitize($sentence)
    {
        $sanitized = preg_replace('/[^A-Za-z0-9\s]/', '', $sentence);
        $sanitized = preg_replace('/\s+/', ' ', $sanitized);
        $sanitized = trim($sanitized);
        return $sanitized;
    }

    function Minkowski(array $doc, array $query, $p): float
    {

        $sum = 0;

        if (count($doc) !== count($query)) {
            throw new InvalidArgumentException('Jumlah Array Tidak Sama !');
        } else {
            for ($x = 0; $x < count($doc); $x++) {
                $diff = pow(abs($query[$x] - $doc[$x]), $p);
                $sum += $diff;
            }

            $result = pow($sum, 1 / $p);
        }
        return $result;
    }



    function  Cosine(array $doc, array $query): float
    {
        $numerator = 0.0;
        $denom_wkq = 0.0;
        $denom_wkj = 0.0;

        if (count($doc) !== count($query)) {
            throw new InvalidArgumentException('Jumlah Array Tidak Sama !');
        } else {
            for ($x = 0; $x < count($doc); $x++) {
                $numerator += $query[$x] * $doc[$x];
                $denom_wkq += pow($query[$x], 2);
                $denom_wkj += pow($query[$x], 2);
            }

            $denom = sqrt($denom_wkq * $denom_wkj);
            if ($denom != 0) {
                $result = $numerator / $denom;
            } else {
                $result = 0;
            }
        }
        return $result;
    }


    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/vendor2/autoload.php';
    require_once 'Text/LanguageDetect.php';

    use markfullmer\porter2\Porter2;
    use Phpml\FeatureExtraction\TokenCountVectorizer;
    use Phpml\Tokenization\WhitespaceTokenizer;
    use Phpml\FeatureExtraction\TfIdfTransformer;

    use Phpml\Math\Distance\Minkowski;

    use StopWords\StopWords;

    $stopwords = new StopWords('en');

    // Remember to put porter class in here 
    $ld = new Text_LanguageDetect();

    $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
    $stemmer = $stemmerFactory->createStemmer();

    $stopwordFactory = new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
    $stopword = $stopwordFactory->createStopWordRemover();





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
    <form method="GET" action="">
        <div class="page-type"><a href="index.php">Home</a>|<a href="crawl.php">Crawling</a></div>
        <div class="page-title">Welcome to Scientific Journals Search Engine</div>
        <div class="search-section">
            <span class="text">Keyword:</span>
            <input type="text" class="search-input" name="keyword" placeholder="Input text here" required>
            <button type="submit" name="search_button" class="search-button">Search</button>

            <div class="radio-buttons">
                <input type="radio" id="minkowski" name="distance_metric" value="Minkowski" required>
                <label for="minkowski">Minkowski</label>
                <input type="radio" id="cosine" name="distance_metric" value="Cosine" required>
                <label for="cosine">Cosine</label>
            </div>
        </div>
        <script src="" async defer></script>
        <?php

        $search = "";
        $metric = "";
        $page = "1";

        if(isset($_GET['keyword'])){
            $search = $_GET['keyword'];
        }
        else{
            echo "Please enter a keyword";
            echo "<br>";
        }
        if(isset($_GET['distance_metric'])){
            $metric = $_GET['distance_metric'];
        }
        else{
            echo "Please choose a metric";
            echo "<br>";
        }
        if(isset($_GET['page'])){
            $page = $_GET['page'];
        }
        else{
            $page = 1;
        }

        if ($page == 1 && $search != "") {        
            try {
                
                $language = $ld->detectSimple($search);
                $searchStem = '';
                $searchStop = '';
    
                if ($language == "english") {
                    $searchStem = Porter2::stem($search);
                    $searchStop  = $stopwords->clean($searchStem);
                } else if ($language == "indonesian") {
                    $searchStem = $stemmer->stem($search);
                    $searchStop = $stopword->remove($searchStem);
                }
                else{

                    // If not English or Indonesian, Default to English
                    $searchStem = Porter2::stem($search);
                    $searchStop  = $stopwords->clean($searchStem);
                }
    
                $arrDocs = array();
                $arrIds = array();

                $query = "SELECT id,concat(title,' ',abstract) as content FROM article;";
                $res = $con->query($query);
                while ($row = $res->fetch_assoc()) {
                    $contentStem = '';
                    $contentStop = '';                    
                    if ($language == "english") {
                        $contentStem = Porter2::stem($row['content']);
                        $contentStop  = $stopwords->clean($contentStem);
                       
                    } else if ($language == "indonesian") {
                        $contentStem = $stemmer->stem($row['content']);
                        $contentStop = $stopword->remove($contentStem);
                       
                    }
                    $arrDocs[] = $contentStop;
                    $arrIds[] = $row['id'];
                }
    
                $arrDocs[] = $searchStop;
                
                $tf = new TokenCountVectorizer(new WhitespaceTokenizer());
                $tf->fit($arrDocs);
                $tf->transform($arrDocs);

                // Get vocabulary/terms from data.
                $terms = $tf->getVocabulary();
    
                $tfidf = new TfIdfTransformer($arrDocs);
                $tfidf->transform($arrDocs);
    
                $total = count($arrDocs);

                if ($metric == 'Minkowski') {
                    for ($i = 0; $i < $total - 1; $i++) {
                        // $query_terms = count($arrDocs[$total-1]);
                        $minkowski = new Minkowski(2);
                        $result = round($minkowski->distance($arrDocs[$i],$arrDocs[$total-1]),3);
                        // $result = round(Minkowski($arrDocs[$i],$arrDocs[$total-1],1),2);
                        $update = "UPDATE article SET similarity = ? WHERE id = ?";
           
                        $stmt = $con->prepare($update);
                        $stmt->bind_param("di",$result,$arrIds[$i]);
                        $stmt->execute();
                    }
                } else if($metric == 'Cosine') {
    
                    for ($i = 0; $i < $total - 1; $i++) {
                        $result = round(Cosine($arrDocs[$i], $arrDocs[$total - 1]),3);
                        $update = "UPDATE article SET similarity = ? WHERE id = ?";
                     
                        $stmt = $con->prepare($update);
                        $stmt->bind_param("di",$result,$arrIds[$i]);
                        $stmt->execute();
                    }
                }
            } catch (Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }
        
           

            unset($_POST);
        }



        if ($metric != "") {

            echo "KEYWORD: ".$search;

            $order = ($metric == "Minkowski") ? "asc" : "desc";

            $search = "";

            if(isset($_POST['keyword'])){
                $search  = $_POST['keyword'];
            }
            else if(isset($_GET['keyword'])){
                $search  = $_GET['keyword'];
            }

            $sql3 = "SELECT concat(title,' ',abstract) as content FROM article 
            WHERE concat(title,' ',abstract) IS NOT NULL order by similarity $order limit 3";
            $result3 = $con->query($sql3);
            $resultsData = array();

            $language = $ld->detectSimple($search);

            while ($row = $result3->fetch_assoc()) {

                $contentStem = '';
                $contentStop = '';                    
                if ($language == "english") {
                    $contentStem = Porter2::stem($row['content']);
                    $contentStop  = $stopwords->clean($contentStem);
                   
                } else if ($language == "indonesian") {
                    $contentStem = $stemmer->stem($row['content']);
                    $contentStop = $stopword->remove($contentStem);
                   
                }
                else{
                    // If not English or Indonesian, Default to English
                    $contentStem = Porter2::stem($row['content']);
                    $contentStop  = $stopwords->clean($contentStem);
                }

                $resultsData[] = $contentStop;
            }

            // print_r($resultsData);

            // QUERY EXPANSION
            $tf2 = new TokenCountVectorizer(new WhitespaceTokenizer());
            $tf2->fit($resultsData);
            $tf2->transform($resultsData);

            // Get vocabulary/terms from data.
            $terms = $tf2->getVocabulary();

            // $tfidf = new TfIdfTransformer($arrDocs);
            // $tfidf->transform($arrDocs);

            // Get the weights of each terms by summing the tf-idf of each Column (means by each term)
            $termsWeights = array();
            foreach ($resultsData[0] as $key => $value) {
                $termsWeights[$key] = array_sum(array_column($resultsData, $key));
            }

            // Sort the terms to Descending. arsort() supposed to sort it without changing the key.
            arsort($termsWeights);

            // Get the top 3 weights as an array item from $termsWeights
            $topTermsWeights = array_slice($termsWeights, 0, 3, true);

            // Get the terms from the $terms array according to the key.
            $topTerms = array();

            foreach ($topTermsWeights as $key => $value) {
                $topTerms[$key] = $terms[$key];
            }

            // echo $topTerms;
            // print_r($arrDocs);
            // print_r($termsWeights);

            // echo "<br><br> QUERY EXPANSION TERMS:";
            // print_r($topTerms);

            $sql = "SELECT id FROM article";
            $result = $con->query($sql);
            $totalDataCount = $result->num_rows;

            $resultsPerPage = 2;
            $totalPages = ceil($totalDataCount / $resultsPerPage);

            $currentpage = $page;

            $startIndex = ($currentpage - 1) * $resultsPerPage;
            $endIndex = $startIndex + $resultsPerPage;

            $sql2 = "SELECT * FROM article order by similarity $order limit $startIndex, $resultsPerPage";
            $result2 = $con->query($sql2);
            // $stmt->bind_param("ii", $startIndex, $resultsPerPage);
            // $result2 = $stmt->get_result(); 
            // Error Uncaught mysqli_sql_exception: Commands out of sync; you can't run this command now
            $currentData = [];
            while ($row = $result2->fetch_assoc()) {
                $currentData[] = $row;
            }

            echo "<h3 class='text-sub'>The Search Results</h3>";
            foreach ($currentData as $index => $data) {
                echo "<div class='result' style='font-family: Roboto, sans-serif;'>";
                echo "<div class='result-content'>";
                echo "<p><strong>Title:</strong> <br>" . $data['title'] . "</p>";
                echo "<p><strong>Authors:</strong> <br>" . $data['authors'] . "</p>";
                echo "<p><strong>Abstract:</strong> <br>" . $data['abstract'] . "</p>";
                echo "-----------------------------";
                echo "<p><strong>Number of Citations:</strong> " . $data['number_of_citations'] . "</p>";
                echo "</div>";
                if ($index !== count($currentData) - 1) {
                    echo "<div class='separator'></div>";
                }
                echo "</div>";
            }

            // $link = "?keyword=''&distance_metric=''&page=0";

            echo "<div class='pagination'>";


            for ($i = 1; $i <= $totalPages; $i++) {
                $page_link = "?keyword=$search&distance_metric=$metric&page=$i";
                echo "<a href='".$page_link."'" . ($currentpage == $i ? " class='active'" : "") . ">" . $i . "</a>";
            }

            if ($currentpage < $totalPages) {
                $page_link = "?keyword=$search&distance_metric=$metric&page=".($currentpage + 1);
                echo "<a href='$page_link'>Next</a>";
            }
            echo "</div>";

            echo "<div class='related-search'>";
            echo "<h3>Related Search</h3>";
            echo "<ul>";

            foreach($topTerms as $topTerm){
                $expansionLink = "?keyword=".$search." ".$topTerm."&distance_metric=$metric&page=1";
                echo "<li><a href='$expansionLink'>" . $search." <b>".$topTerm . "</b></a></li>";
            }

            // foreach ($sampleLink as $link) {
            //     $shortenedId = strlen($link['id']) > 30 ? substr($link['id'], 0, 30) . '...' : $link['id'];
            //     //$shortenedId = strlen($link['id']) > 30 ? str_replace(' ', '<br>', wordwrap($link['id'], 30)) : $link['id'];
            //     echo "<li><a href='" . $link['link'] . "'>" . $shortenedId . "</a></li>";
            // }
            echo "</ul>";
            echo "</div>";
        }

        ?>
    </form>
</body>

</html>