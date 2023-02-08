<?php

if (basename($_SERVER['REQUEST_URI']) == 'commonfunc.php') {
    die ('Access denied');
}

// Strip slashes and convert all applicable characters to HTML entities for XSS prevention
function ec($input)
{
    $site = get_site_data();
    return stripslashes(htmlentities($input, $ent = ENT_COMPAT, $site['charset']));
}

// --- Query single value from database (one row, first column). Returns False if no value.
function query_single($sql)
{
    global $dbh;
    $result = $dbh->query($sql);
    $value = $result->fetchColumn();
    if ($value) {
        return $value;
    }

    return false;
}

// --- Query single value from database with prepared statement. Takes SQL statement and array of parameters.
function query_prep($sql, $params_array)
{
    global $dbh;
    $stmt = $dbh->prepare($sql);
    if ($stmt->execute($params_array)) {
        $result = $stmt->fetch(PDO::FETCH_NUM);
        return $result[0];
    }
    return null;
}

// --- Returns all the data for site as an array.
function get_site_data()
{
    global $dbh;
    $stmt = $dbh->prepare("SELECT * FROM site WHERE id = 1");
    if ($stmt->execute()) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result[0];
    }
    return [];
}

// --- Returns all the data for page as an array. Takes 1) pageurl 2) onlypublished TRUE/FALSE 3) table name (defaults to page).
function get_page_data($pageurl, $onlypublished = false, $table = 'page')
{
    global $dbh;
    if ($onlypublished == true) {
        $statusquery = ' AND STATUS = 1';
    } else {
        $statusquery = '';
    }
    $stmt = $dbh->prepare("SELECT * FROM " . $table . " WHERE pageurl = ?" . $statusquery);
    if ($stmt->execute(array($pageurl))) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result[0];
    }
    return null;
}

// --- Returns all the data for page as an array. Takes pageurl.
function get_extrapage_data($pageurl, $onlypublished = false)
{
    global $dbh;
    if ($onlypublished == true) {
        $statusquery = ' AND STATUS = 1';
    } else {
        $statusquery = '';
    }
    $stmt = $dbh->prepare("SELECT * FROM extrapage WHERE pageurl = ?" . $statusquery);
    if ($stmt->execute(array($pageurl))) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result[0];
    }
    return null;
}

// --- Returns cmsoptions as an array.
function get_cmsoptions()
{
    global $dbh;
    $stmt = $dbh->prepare("SELECT * FROM options WHERE id = 1");
    if ($stmt->execute()) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result[0];
    }
    return null;
}

// --- Returns single value for frontpage. Takes column name (defaults to pageurl) and table name (defaults to page).
function frontpage_value($column = 'pageurl', $table = 'page')
{
    return query_single(
        "SELECT " . $column . " FROM " . $table . " WHERE levelnum = 1 AND status = 1 ORDER BY ordernum LIMIT 1"
    );
}

// --- Returns page name for a pageurl.
function page_name($pageurl, $table = 'page')
{
    return query_single("SELECT name FROM " . $table . " WHERE pageurl = '" . $pageurl . "' LIMIT 1");
}

// --- Returns clean url for pageurl
function clean_url($pageurl, $table = 'page')
{
    $level = query_prep("SELECT levelnum FROM " . $table . " WHERE pageurl = ?", array($pageurl));
    $siteroot = trim(query_single("SELECT siteroot FROM site WHERE id = 1"), '/');
    $url_suffix = trim(query_single("SELECT url_suffix FROM site WHERE id = 1"), '.');
    if ($url_suffix != "") {
        $url_suffix = '.' . $url_suffix;
    }
    $urlbase = '/' . ($siteroot != "" ? $siteroot . '/' : '') . ($table == 'extrapage' ? 'extra/' : '') . 'index.php/';
    if ($level > 1) {
        $mother = query_prep("SELECT mother FROM " . $table . " WHERE pageurl = ?", array($pageurl));
    }
    if ($level <= 1) {
        $urlrest = $pageurl . $url_suffix;
    } elseif ($level == 2) {
        $urlrest = $mother . '/' . $pageurl . $url_suffix;
    } elseif ($level == 3) {
        $urlrest = query_prep("SELECT mother FROM " . $table . " WHERE pageurl = ?", array($mother)
            ) . '/' . $mother . '/' . $pageurl . $url_suffix;;
    } else {
        return false;
    }
    return $urlbase . $urlrest;
}

// --- Cuts text to certain lenght but not in the middle of a word. Adds suffix from tail.
function snippetstr($text, $length, $tail = "...")
{
    if ($length < 1 or $length > 10000 or !is_numeric($length)) {
        $length = 110;
    } // default invalid to 110 chars
    $text = trim($text);
    $txtl = strlen($text);
    if ($txtl > $length) {
        for ($i = 1; $text[$length - $i] != " "; $i++) {
            if ($i == $length) {
                return substr($text, 0, $length) . $tail;
            }
        }
        for (; $text[$length - $i] == "," || $text[$length - $i] == "." || $text[$length - $i] == " "; $i++) {
            ;
        }
        $text = substr($text, 0, $length - $i + 1) . $tail;
    }
    return $text;
}

// --- Prints content for a given snippet name.
function snippet($name)
{
    echo query_prep("SELECT content FROM snippet WHERE name = ?", array($name));
}

// --- Prints html content and evaluates php code for a given snippet.
function snippet_php($name)
{
    eval("?>" . query_prep("SELECT content FROM snippet WHERE name = ?", array($name)));
}