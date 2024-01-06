<?php
include_once('simple_html_dom.php');
require_once __DIR__ . '/vendor/autoload.php';
use markfullmer\porter2\Porter2;

$con = new mysqli("localhost", "root", "mysql", "iirFinal");
if ($con->connect_errno) {
    die("Failed to connect to MySQL: " . $con->connect_errno);
}

$proxy = ''; //proxy3.ubaya.ac.id:8080

function sanitize($sentence)
{
    $sanitized = preg_replace('/[^A-Za-z0-9\s]/', '', $sentence);
    $sanitized = preg_replace('/\s+/', ' ', $sanitized);
    $sanitized = trim($sanitized);
    return $sanitized;
}

function extract_html($url, $proxy)
{

    $response = array();

    $response['code'] = '';

    $response['message'] = '';

    $response['status'] = false;

    $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1';

    // Some websites require referrer
    $url = html_entity_decode($url); //to be updated

    $host = parse_url($url, PHP_URL_HOST);

    $scheme = parse_url($url, PHP_URL_SCHEME);

    $referrer = $scheme . '://' . $host;

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_HEADER, false);

    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_PROXY, $proxy);

    curl_setopt($curl, CURLOPT_USERAGENT, $agent);

    curl_setopt($curl, CURLOPT_REFERER, $referrer);

    curl_setopt($curl, CURLOPT_COOKIESESSION, 0);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

    // allow to crawl https webpages

    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

    // the download speed must be at least 1 byte per second

    curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 1);

    // if the download speed is below 1 byte per second for more than 30 seconds curl will give up

    curl_setopt($curl, CURLOPT_LOW_SPEED_TIME, 30);

    $content = curl_exec($curl);

    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    $response['code'] = $code;

    if ($content === false) {

        $response['status'] = false;

        $response['message'] = curl_error($curl);

    } else {

        $response['status'] = true;

        $response['message'] = $content;

    }

    curl_close($curl);

    return $response;

}

?>

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
    <form method="POST" action="">
        <div class="page-type"><a href="index.php">Home</a>|<a href="crawl.php">Crawling</a></div>
        <div class="page-title">Data Crawling from Google Scholar</div>
        <div class="crawl-section">
            <span class="text">Keyword:</span>
            <input type="text" class="crawl-input" name="keyword" placeholder="Input text here">
            <button type="submit" name="crawl_button" class="crawl-button">Crawl</button>
        </div>
        <script src="" async defer></script>
        <?php
        if (isset($_POST['crawl_button'])) {
            if (isset($_POST['keyword'])) {
                $keyword = $_POST['keyword'];

                //-----Stemming New Comment-----
                $newComment = $_POST['keyword'];
                $newComment = sanitize($newComment);
                $commentWords = explode(' ', $newComment);
                for ($i = 0; $i < count($commentWords); $i++) {
                    $commentWords[$i] = Porter2::stem($commentWords[$i]);
                }
                $commentStemmed = implode(' ', $commentWords);

                $processedKeyword = str_replace(" ", "+", $commentStemmed);

                $articleCount = 0;
                $startPage = 10;
                $articleRequested = 5;
                while ($articleCount < $articleRequested) {
                    $finalUrl = "https://scholar.google.com/scholar?start=" . $startPage . "&q=" . $processedKeyword . "&hl=en&as_sdt=0,5";
                    $result = extract_html($finalUrl, $proxy);
                    if ($result['code'] == '200') {
                        $html = new simple_html_dom();
                        $html->load($result['message']);
                        foreach ($html->find('div[class="gs_r gs_or gs_scl"]') as $article) {
                            $authors = $article->find('div[class="gs_a"]', 0);
                            if ($article->find('h3[class="gs_rt"]', 0)->find('a', 0)) {
                                $title = strip_tags($article->find('h3[class="gs_rt"]', 0)->find('a', 0)->innertext);
                                if ($authors) {
                                    $linkToAuthors = $authors->find('a', 0);
                                    if (($linkToAuthors) && ($articleCount < $articleRequested)) {
                                        $startPageAuthor = 0;
                                        $foundStatus = false;
                                        while ($foundStatus != true) {
                                            $linkToAuthorsText = "https://scholar.google.com" . $linkToAuthors->href . "&cstart=" . $startPageAuthor . "&pagesize=100";
                                            // ========= ITERASI DALAM AUTHORS =========
                                            $resultAuthor = extract_html($linkToAuthorsText, $proxy);
                                            if ($resultAuthor['code'] == '200') {
                                                $htmlAuthor = new simple_html_dom();
                                                $htmlAuthor->load($resultAuthor['message']);
                                                foreach ($htmlAuthor->find('td[class="gsc_a_t"]') as $articlesAuthor) {
                                                    if ($articlesAuthor->find('a[class="gsc_a_at"]', 0)) {
                                                        if (strip_tags($articlesAuthor->find('a[class="gsc_a_at"]', 0)->innertext) == $title) {
                                                            $destinedArticleLink = "https://scholar.google.com" . $articlesAuthor->find('a[class="gsc_a_at"]', 0)->href;

                                                            //========= HALAMAN DETAIL ARTIKEL =========
                                                            $resultArticleDetail = extract_html($destinedArticleLink, $proxy);
                                                            if ($resultArticleDetail['code'] == '200') {
                                                                $htmlDetail = new simple_html_dom();
                                                                $htmlDetail->load($resultArticleDetail['message']);

                                                                // TITLE
                                                                if ($htmlDetail->find('a[class="gsc_oci_title_link"]', 0)) {
                                                                    $latestTitle = strip_tags($htmlDetail->find('a[class="gsc_oci_title_link"]', 0)->innertext);
                                                                    $latestLink = $htmlDetail->find('a[class="gsc_oci_title_link"]', 0)->href;
                                                                }
                                                                // AUTHORS
                                                                if ($htmlDetail->find('div[class="gs_scl"]', 0)->find('div[class="gsc_oci_value"]', 0)) {
                                                                    $latestAuthors = $htmlDetail->find('div[class="gs_scl"]', 0)->find('div[class="gsc_oci_value"]', 0)->innertext;
                                                                }
                                                                // ABSTRACT
                                                                if ($htmlDetail->find('div[id="gsc_oci_descr"]', 0)) {
                                                                    $latestAbstract = strip_tags($htmlDetail->find('div[id="gsc_oci_descr"]', 0)->innertext);
                                                                }
                                                                // CITATION
                                                                if ($htmlDetail->find('div[id="gsc_oci_table"]', 0)->find('a', 0)) {
                                                                    $latestNumOfCitation = explode(" ", $htmlDetail->find('div[id="gsc_oci_table"]', 0)->find('a', 0)->innertext)[2];
                                                                }

                                                                //========= DISPLAY DETAIL ARTIKEL =========
                                                                $latestSimilarity = 0;
        
                                                                echo "<div class='result' style='font-family: Roboto, sans-serif;'>";
                                                                echo "<p><strong>Title:</strong> <br>"."<a href='".$latestLink."'>" . $latestTitle . "</a>"."</p>";
                                                                echo "<p><strong>Authors:</strong> <br>" . $latestAuthors . "</p>";
                                                                echo "<p><strong>Abstract:</strong> <br>" . $latestAbstract . "</p>";
                                                                echo "-----------------------------";
                                                                echo "<p><strong>Number of Citations:</strong> " . $latestNumOfCitation . "</p>";
                                                                echo "<div class='separator'></div>";
                                                                echo "</div>";

                                                                //========= INPUT DATABASE =========
                                                                $sql = "INSERT INTO article(title, authors, number_of_citations, abstract, link, similarity) VALUES(?, ?, ?, ?, ?, ?)";
                                                                $resultSql = $con->prepare($sql);
                                                                $resultSql->bind_param('ssissd', $latestTitle, $latestAuthors, $latestNumOfCitation, $latestAbstract, $latestLink, $latestSimilarity);
                                                                $resultSql->execute();

                                                                $articleCount += 1;

                                                                $foundStatus = true;
                                                                break 1;
                                                            }
                                                        }
                                                    }

                                                }
                                            }
                                            if ($foundStatus == false) {
                                                if ($startPageAuthor < 200) {
                                                    $startPageAuthor += 100;
                                                } else {
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $startPage += 10;
                    }
                }
            }
        }
        ?>
    </form>
</body>

</html>