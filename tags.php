<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2015 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

$_language->readModule('search');
if (isset($_GET[ 'tag' ])) {
    $tag = $_GET[ 'tag' ];
    $sql = safe_query("SELECT * FROM " . PREFIX . "tags WHERE tag='" . $tag . "'");
    if ($sql->num_rows) {
        $data = array();
        while ($ds = mysqli_fetch_assoc($sql)) {
            $data_check = null;
            if ($ds[ 'rel' ] == "news") {
                $data_check = \webspell\Tags::getNews($ds[ 'ID' ]);
            } elseif ($ds[ 'rel' ] == "articles") {
                $data_check = \webspell\Tags::getArticle($ds[ 'ID' ]);
            } elseif ($ds[ 'rel' ] == "static") {
                $data_check = \webspell\Tags::getStaticPage($ds[ 'ID' ]);
            } elseif ($ds[ 'rel' ] == "faq") {
                $data_check = \webspell\Tags::getFaq($ds[ 'ID' ]);
            }
            if (is_array($data_check)) {
                $data[ ] = $data_check;
            }
        }
        echo "<h1>" . $_language->module[ 'search' ] . "</h1>";
        usort($data, array('Tags', 'sortByDate'));
        echo "<p class=\"text-center\"><strong>" . count($data) . "</strong> " . $_language->module[ 'results_found' ] .
            "</p><br><br>";
        foreach ($data as $entry) {
            $date = getformatdate($entry[ 'date' ]);
            $type = $entry[ 'type' ];
            $auszug = $entry[ 'content' ];
            $link = $entry[ 'link' ];
            $title = $entry[ 'title' ];
            $data_array = array();
            $data_array['$date'] = $date;
            $data_array['$link'] = $link;
            $data_array['$title'] = $title;
            $data_array['$auszug'] = $auszug;
            $search_tags = $GLOBALS["_template"]->replaceTemplate("search_tags", $data_array);
            echo $search_tags;
        }
    } else {
        $tag = htmlspecialchars($tag);
        $text = sprintf($_language->module[ 'no_result' ], $tag);
        $data_array = array();
        $data_array['$text'] = $text;
        $search_tags_no_result = $GLOBALS["_template"]->replaceTemplate("search_tags_no_result", $data_array);
        echo $search_tags_no_result;
    }
} else {
    function tags_top_10($a1, $a2)
    {
        if ($a1[ 'count' ] == $a2[ 'count' ]) {
            return 0;
        } else {
            return $a1[ 'count' ] < $a2[ 'count' ] ? -1 : 1;
        }
    }

    $tags = \webspell\Tags::getTagCloud();
    usort($tags[ 'tags' ], "tags_top_10");
    $str = '';

    $counter = min(10, count($tags[ 'tags' ]));
    for ($i = 0; $i < $counter; $i++) {
        $tag = $tags[ 'tags' ][ $i ];
        $size = \webspell\Tags::GetTagSizeLogarithmic($tag[ 'count' ], $tags[ 'min' ], $tags[ 'max' ], 10, 25, 0);
        $str .= " <a href='index.php?site=tags&amp;tag=" . $tag[ 'name' ] . "' style='font-size:" . $size .
            "px;text-decoration:none;'>" . $tag[ 'name' ] . "</a> ";
    }

    echo '<div class="post">
        <h2 class="title">Top Tags</h2>

        <div class="entry">
            <p>
                '.$str.'
            </p>
        </div>
    </div>';
}
