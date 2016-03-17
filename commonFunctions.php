<?php
/*
 * Set of common functions.
 *
 * __DIR__: the path to the web folder, for example: /var/www/html.
 */

/**
 * Sets the error handling options for the site.
 */
function SetErrorsHandling () {

  /* For development: */
  ini_set("display_errors", 1);
  ini_set("display_startup_errors", 1);
  error_reporting(E_ALL);
  
} 

/**
 * Returns the url parameter value
 */
function GetUrlParam ($paramName, $defaultValue) {
  
  if (isset($_GET[$paramName])) {
    return $_GET[$paramName];
  }
  return $defaultValue;
}

/**
 * Returns the value for the select/option attribute.
 */
function IsSelected ($optionValue, $currentValue) {
  
  if ($optionValue == $currentValue) {
    return " selected=\"selected\"";
  }
  return "";
}

/**
 * Returns the html code for the paged list navigation.
 *      Prev 5 6 7 [8] 9 10 11 Next
 * @param $navPages - the number of links before and after the current page.
 */
function HtmlPageNavigation ($page, $pageSize, $totalPages, $totalRecords, $navPages) {

  if ($totalPages < 1) {
    return "";
  }
  
  $res = "";
  $delim = ""; // delimeter before the new page parameter.
  $url = strtok ("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", "?"); // url without the query string

  // 1. get the query string
  $qs = $_SERVER["QUERY_STRING"];
  
  // 2. remote the page=nnn string
  $qs = preg_replace ('/&{0,1}page=\d*/i', '', $qs);
  
  // 3. remove possible & in the string beginning.
  $qs = preg_replace ('/^&/', '', $qs);
  
  // First and Last pages in the control
  // Start navigation from
  $navFrom = $page - $navPages;

  if ($navFrom < 1) {
    $navFrom = 1;
  }

  // End navigation with
  $navTo = $navFrom + $navPages * 2;

  if ($navTo > $totalPages) {

    $navTo = $totalPages;
    
    // Correct NavFrom
    $navFrom = $navTo - $navPages * 2;
    
    if ($navFrom < 1) {
      $navFrom = 1;
    }
    
  }
  
  if ($page > 1) {
    // Prev page, << character.
    $res .= '<a href="' . HtmlMakePagedUrl ($url, $qs, $page - 1) . '">&#171;</a> ';
  }

  if ($navFrom > 1) {
    // First page
    $res .= '<a href="' . HtmlMakePagedUrl ($url, $qs, 1) . '">1</a>…';
  }

  // Page links
  for ($i = $navFrom; $i <= $navTo; $i++) {
    if ($i == $page) {
      $res .= '<b>' . $i . '</b>';
      
    }
    else {
      $res .= '<a href="' . HtmlMakePagedUrl ($url, $qs, $i) . '">' . $i . '</a>';
    }
  }
  
  if ($navTo < $totalPages) {
    // Last page
    $res .= '…<a href="' . HtmlMakePagedUrl ($url, $qs, $totalPages) . '">' . $totalPages . '</a>';
  }

  if ($page < $totalPages) {
    // Next page
    $res .= '<a href="' . HtmlMakePagedUrl ($url, $qs, $page + 1) . '">&#187;</a> ';
  }
  
  return '<div class="page-nav">' . $res . '</div>';
}

// Returns the url for the page navigation link.
function HtmlMakePagedUrl ($url, $qs, $page) {
  
  $pageParam = "page=" . $page;
  $delim = "?";
  
  if (strlen ($qs) > 0) {
    return $url . "?" . $qs . "&amp;" . $pageParam ;
  }
  
  return $url . "?" . $pageParam ;
  
}

/**
 * Represents the records range for paged lists.
 */
class RecordsRange {
  
  public $Page = 0;
  public $PageSize = 25;
  public $TotalPages = 0;
  public $TotalRecords = 0;
  public $StartRecordNumber = 0;
  public $EndRecordNumber = 24;
  
  /**
   * Processes the parameters and set the instance values;
   */
  public function __construct ($totalRecords, $page, $pageSize) {

    $this->Page     = intval ($page);
    $this->PageSize = intval ($pageSize);
    
    if ($this->PageSize < 1) {
      $this->PageSize = 1;
    }
    
    $this->TotalRecords = intval ($totalRecords);
    
    $this->TotalPages = intval (ceil ($this->TotalRecords / $this->PageSize));

    if ($this->Page < 1 || $this->Page > $this->TotalPages) {
       $this->Page = 1;
    }

    // page 1: 0 ... pageSize - 1
    // page 2: pageSize ... pageSize * 2 - 1
    // page i: pageSize * (page - 1) ... pageSize * (page) - 1
    
    $this->StartRecordNumber = ($this->Page - 1) * $this->PageSize;
    $this->EndRecordNumber = $this->StartRecordNumber + $this->PageSize - 1;
    
    if ($this->TotalRecords > 0 && $this->EndRecordNumber >= $this->TotalRecords) {
      $this->EndRecordNumber = $this->TotalRecords - 1;
    }

  }
  
}

?>