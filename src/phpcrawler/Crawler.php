<?php
    /**
     * Crawler
     *
     * @package phpcrawler
     */

    namespace phpcrawler;

    class Crawler
    {
        private $url;
        private $curl;
        private $database;
        private $linksList;

        /**
         * @param $url
         */
        public function __construct($url)
        {
            $this->url = $url;

            // Instantiate database.
            $this->database = new \Database();

            $this->emptyDbTables();

            echo "\nParsing URL for links.\n\n";
            $this->parseUrlForLinks($this->url);

            echo "Links fetched and stored in database.\n\n";
            $linksList = $this->getAllLinksInDatabase();

            echo "This might take some time, depending "
                . "on number of URL's to be parsed.\n\n";

            echo "Please wait while the crawler is processing.\n\n";

            $this->iterateOverLinks($linksList);

            echo "Crawling completed, here is the final report:\n\n";

            $this->generateReport();

            echo "\n\nTotal size: ";
            $this->generateLastReport();
        }

        /**
         *
         */
        private function initialiseCurl()
        {
            $this->curl = curl_init();
        }

        /**
         *
         */
        private function closeCurl()
        {
            curl_close($this->curl);
        }

        /**
         * @param $url
         * @return mixed|null
         */
        public function getUrlInCurl($url)
        {
            $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml, text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
            $header[] = "Cache-Control: max-age=0";
            $header[] = "Connection: keep-alive";
            $header[] = "Keep-Alive: 300";
            $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
            $header[] = "Accept-Language: en-us,en;q=0.5";

            $curlOptions = array(
                CURLOPT_RETURNTRANSFER => true,     // return web page
                CURLOPT_HEADER => false,    // don't return headers
                CURLOPT_FOLLOWLOCATION => true,     // follow redirects
                CURLOPT_ENCODING => "",       // handle all encodings
                CURLOPT_USERAGENT => "PHP crawler", // USER agent string
                CURLOPT_AUTOREFERER => true,     // set referrer on redirect
                CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
                CURLOPT_TIMEOUT => 120,      // timeout on response
                CURLOPT_MAXREDIRS => 10,       // stop after 10 redirects,
                CURLOPT_URL => $url,
                CURLOPT_SSL_VERIFYPEER => $url,
                CURLOPT_SSL_VERIFYHOST => $url,
            );

            curl_setopt_array($this->curl, $curlOptions);

            $html = curl_exec($this->curl); // execute the curl command

            if ( ! $html) {
                if (DEBUG > 1) {
                    echo "<pre>";
                    var_dump(curl_error($this->curl));
                    die("HTML could not be retrieved.");
                } else {
                    return null;
                }
            } else {
                //Get CURL transfer information
                $info = curl_getinfo($this->curl);

                if ($info['http_code'] == 200) {
                    return $html;
                }
            }
        }

        /**
         * @param $html
         * @return \DOMDocument
         */
        public function createDomDocument($html)
        {
            // Create a new DOM Document
            $dom = new \DOMDocument();

            $dom->recover = true;
            $dom->strictErrorChecking = false;

            // Load the url's contents into the DOM
            $dom->loadHTML($html);

            return $dom;
        }

        /**
         * @param $html
         */
        public function getLinks($html)
        {
            // Create a new DOM Document
            $dom = $this->createDomDocument($html);

            //Loop through each <a> tag in the dom and add it to the link array
            foreach ($dom->getElementsByTagName('a') as $link) {
                $currentHref = $link->getAttribute('href');

                $url_parts = parse_url($currentHref);

                //Ensure that link is not empty, and not a hash link
                if ($this->isLinkAcceptable($currentHref, $url_parts)) {

                    $currentHref = $this->reconstruct_url($url_parts);

                    $this->database->query("INSERT INTO links (url, created) VALUES (:url, now())");

                    $this->database->bind(':url', $currentHref);

                    $this->database->execute();
                }
            }

            // for css
            foreach ($dom->getElementsByTagName('link') as $link) {
                $currentHref = $link->getAttribute('href');

                $url_parts = parse_url($currentHref);

                //Ensure that link is not empty, and not a hash link
                if ($this->isLinkAcceptable($currentHref, $url_parts)) {

                    $currentHref = $this->reconstruct_url($url_parts);

                    $this->database->query("INSERT INTO links (url, created, filetype, issize) VALUES (:url, now(), :fileType, :isSize)");

                    $this->database->bind(':url', $currentHref);
                    $this->database->bind(':fileType', "css");
                    $this->database->bind(':isSize', true);

                    $this->database->execute();
                }
            }

            // for js
            foreach ($dom->getElementsByTagName('script') as $link) {
                $currentHref = $link->getAttribute('src');

                $url_parts = parse_url($currentHref);

                if ($this->isLinkAcceptable($currentHref, $url_parts)) {

                    $currentHref = $this->reconstruct_url($url_parts);

                    $this->database->query("INSERT INTO links (url, created, filetype, issize) VALUES (:url, now(), :fileType, :isSize)");

                    $this->database->bind(':url', $currentHref);
                    $this->database->bind(':fileType', "js");
                    $this->database->bind(':isSize', true);

                    $this->database->execute();
                }
            }

            // for image
            foreach ($dom->getElementsByTagName('img') as $link) {
                $currentHref = $link->getAttribute('src');

                $url_parts = parse_url($currentHref);

                if ($this->isLinkAcceptable($currentHref, $url_parts)) {

                    $currentHref = $this->reconstruct_url($url_parts);

                    $this->database->query("INSERT INTO links (url, created, filetype, issize) VALUES (:url, now(), :fileType, :isSize)");

                    $this->database->bind(':url', $currentHref);
                    $this->database->bind(':fileType', "image");
                    $this->database->bind(':isSize', true);

                    $this->database->execute();
                }
            }
        }

        /**
         * @param $url
         */
        public function parseUrlForLinks($url)
        {
            $this->initialiseCurl();

            //Get relevant links, store in db
            $html = $this->getUrlInCurl($url);

            $this->getLinks($html);

            $this->closeCurl();
        }

        /**
         * @param $link
         */
        public function crawlUrlForImgTags($link)
        {
            //To find the execution time taken
            $start = microtime(true);

            $this->initialiseCurl();

            //Get relevant links, store in db
            $html = $this->getUrlInCurl($link['url']);

            $this->closeCurl();

            // Create a new DOM Document
            $dom = $this->createDomDocument($html);

            $image_count = $dom->getElementsByTagName('img')->length;

            $time_elapsed_secs = microtime(true) - $start;

            if ($image_count > 0) {
                $this->database->query("INSERT INTO files (link_id, time_taken) VALUES (:link_id , :time_taken)");

                $this->database->bind(':link_id', $link['id']);
                $this->database->bind(':time_taken', $time_elapsed_secs);

                $this->database->execute();
            }
        }

        /**
         * @param $link
         * @return bool
         */
        public function getFileSizeFromLink($link)
        {
            //To find the execution time taken
            $start = microtime(true);
            if ($link["issize"]) {


                $this->initialiseCurl();

                $fileSize = strlen(file_get_contents($link['url']));

                $this->closeCurl();

                $time_elapsed_secs = microtime(true) - $start;


                $this->database->query("INSERT INTO files (link_id, time_taken, filesize) VALUES (:link_id, :time_taken, :filesize)");

                $this->database->bind(':link_id', $link['id']);
                $this->database->bind(':time_taken', $time_elapsed_secs);
                $this->database->bind(':filesize', $fileSize);

                $this->database->execute();
            } else {
                return true;
            }
        }

        /**
         * @param $url
         * @return mixed
         */
        private function getFileSizeInCurl($url) {

            $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml, text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
            $header[] = "Cache-Control: max-age=0";
            $header[] = "Connection: keep-alive";
            $header[] = "Keep-Alive: 300";
            $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
            $header[] = "Accept-Language: en-us,en;q=0.5";

            $curlOptions = array(
                CURLOPT_RETURNTRANSFER => true,     // return web page
                CURLOPT_HEADER => false,    // don't return headers
                CURLOPT_FOLLOWLOCATION => true,     // follow redirects
                CURLOPT_ENCODING => "",       // handle all encodings
                CURLOPT_USERAGENT => "PHP crawler", // USER agent string
                CURLOPT_AUTOREFERER => true,     // set referrer on redirect
                CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
                CURLOPT_TIMEOUT => 120,      // timeout on response
                CURLOPT_MAXREDIRS => 10,       // stop after 10 redirects,
                CURLOPT_URL => $url,
                CURLOPT_SSL_VERIFYPEER => $url,
                CURLOPT_SSL_VERIFYHOST => $url,
            );

            curl_setopt_array($this->curl, $curlOptions);

            $resFromCurl = curl_exec($this->curl); // execute the curl command
            $size = curl_getinfo($this->curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            return $size;
        }

        /**
         * @param $link
         * @param $url_parts
         * @return bool
         */
        private function isLinkAcceptable($link, $url_parts)
        {
            if (
                empty($link) ||
                filter_var($link, FILTER_VALIDATE_URL) === FALSE ||
                (substr($link, 0, 1) === '#')
            ) {
                return false;
            }

            $currentHost = $url_parts['scheme'] . '://' . $url_parts['host'] . "/";

            //Check of link is within the given initial URL
            if ($currentHost != $this->url) {
                return false;
            }

            return true;
        }

        /**
         * @param $url_parts
         * @return string
         */
        private function reconstruct_url($url_parts)
        {
            $constructed_url = $url_parts['scheme'] . '://' . $url_parts['host'] . (isset($url_parts['path']) ? $url_parts['path'] : '');

            return $constructed_url;
        }

        /**
         * @return mixed
         */
        private function getAllLinksInDatabase()
        {
            $this->database->query("SELECT id, url, issize FROM links ORDER BY id ASC");

            $links = $this->database->getResultSet();

            return $links;
        }

        /**
         * @param $linksList
         */
        private function iterateOverLinks($linksList)
        {
            foreach ($linksList as $k => $link) {

                $this->getFileSizeFromLink($link);

            }
        }

        /**
         *
         */
        private function generateReport()
        {
            $cellWidth = 0;

            $this->database->query("SELECT url, filesize FROM `links`
                LEFT JOIN `files`
                ON files.link_id = links.id
                ORDER BY url ASC");

            $resultSet = $this->database->getResultSet();

            if (count($resultSet) > 0) {
                foreach ($resultSet as $result) {
                    if (strlen($result['url']) > $cellWidth) {
                        $cellWidth = strlen($result['url']);
                    }
                }

                echo $this->getPaddedString("URL", $cellWidth) . "\t File Size \n";
                echo $this->getPaddedString("========", $cellWidth) . "\t =========== \n";


                foreach ($resultSet as $result) {
                    echo $this->getPaddedString($result['url'], $cellWidth) . "\t "
                        . $result['filesize'] . " \n";
                }
            }
        }

        private function generateLastReport()
        {
            $cellWidth = 0;

            $this->database->query("SELECT url, filesize FROM `files`
                INNER JOIN `links`
                ON files.link_id = links.id
                ORDER BY url ASC");

            $resultSet = $this->database->getResultSet();
            $totalSize = 0;
            if (count($resultSet) > 0) {
                foreach ($resultSet as $result) {
                    if (strlen($result['url']) > $cellWidth) {
                        $cellWidth = strlen($result['url']);
                    }
                }

                foreach ($resultSet as $result) {
                    $totalSize = $totalSize + $result['filesize'];
                }

                echo $totalSize;
            }
        }


        /**
         * @param $text
         * @param $maxCellWidth
         * @return string
         */
        private function getPaddedString($text, $maxCellWidth)
        {
            $textLength = strlen($text);
            $requiredLength = max($maxCellWidth, $textLength);

            if ($requiredLength > $textLength) {
                $diff = $requiredLength - $textLength;

                $text .= str_repeat(' ', $diff);
            }

            return $text;
        }

        /**
         *
         */
        private function emptyDbTables()
        {
            $this->database->query("TRUNCATE links");
            $this->database->execute();

            $this->database->query("TRUNCATE files");
            $this->database->execute();
        }
    }
