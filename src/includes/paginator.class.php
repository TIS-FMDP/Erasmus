<?php

class Paginator
{
  var $items_per_page;
  var $items_total;
  var $current_page;
  var $num_pages;
  var $mid_range;
  var $low;
  var $high;
  var $limit;
  var $return;
  var $default_ipp = 50;
  var $querystring;
  var $sep;

  function Paginator()
  {
    $this->current_page = 1;
    $this->mid_range = 5;
    $this->items_per_page = (!empty($_GET['ipp'])) ? $_GET['ipp'] : $this->default_ipp;
  }

  function paginate()
  {

    
    if (!is_numeric($this->items_per_page) or $this->items_per_page <= 0) $this->items_per_page = $this->default_ipp;
    $this->num_pages = ceil($this->items_total / $this->items_per_page);
    $this->current_page = (!empty($_GET['p'])) ? $_GET['p'] : 1; // must be numeric > 0
    if ($this->current_page < 1 or !is_numeric($this->current_page)) $this->current_page = 1;
    if ($this->current_page > $this->num_pages) $this->current_page = $this->num_pages;
    $prev_page = $this->current_page - 1;
    $next_page = $this->current_page + 1;

    $queryparts = array();
    if($_GET)
    {
      $args = explode("&", $_SERVER['QUERY_STRING']);
      foreach ($args as $arg)
      {
        $keyval = explode("=", $arg);
        if ($keyval[0] != "p" and $keyval[0] != "ipp")
        {
          $queryparts[] = $arg;
        }
      }
    }

    if ($_POST)
    {
      foreach ($_POST as $key => $val)
      {
        if ($key != "p" and $key != "ipp" and $key != "submit")
        {
          $queryparts[] = $key . "=" . urlencode($val);
        }
      }
    }

    if (!empty($queryparts))
    {
      $this->querystring = '?' . implode('&amp;', $queryparts);
    }
    $this->sep = (empty($this->querystring)) ? '?' : '&amp;';

    if ($this->num_pages > 10)
    {
      $this->return .= ($this->current_page != 1 and $this->items_total >= 10) ? '<a href="' . $_SERVER["PHP_SELF"] . $this->querystring . $this->sep . 'p=' . $prev_page . '&ipp=' . $this->items_per_page . '">&laquo; Predošlá</a>' : '<strong class="inactive">&laquo; Predošlá</strong>';

      $this->start_range = $this->current_page - floor($this->mid_range / 2);
      $this->end_range = $this->current_page + floor($this->mid_range / 2);

      if ($this->start_range <= 0)
      {
        $this->end_range += abs($this->start_range)+1;
        $this->start_range = 1;
      }
      if ($this->end_range > $this->num_pages)
      {
        $this->start_range -= $this->end_range-$this->num_pages;
        $this->end_range = $this->num_pages;
      }
      $this->range = range($this->start_range,$this->end_range);

      for ($i=1;$i<=$this->num_pages;$i++)
      {
        if ($this->range[0] > 2 and $i == $this->range[0]) $this->return .= " ... ";
        // loop through all pages. if first, last, or in range, display
        if ($i == 1 or $i == $this->num_pages or in_array($i, $this->range))
        {
          $this->return .= ($i == $this->current_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $_SERVER["PHP_SELF"] . $this->querystring . $this->sep . 'p=' . $i . '&amp;ipp=' . $this->items_per_page . '">' . $i . '</a>';
        }
        if ($this->range[$this->mid_range - 1] < $this->num_pages - 1 and $i == $this->range[$this->mid_range - 1]) $this->return .= " ... ";
      }
      $this->return .= ($this->current_page != $this->num_pages and $this->items_total >= 10) ? '<a href="' . $_SERVER["PHP_SELF"] . $this->querystring . $this->sep . 'p=' . $next_page . '&amp;ipp=' . $this->items_per_page . '">Ďalšia &raquo;</a>' . "\n" : '<strong class="inactive">&raquo; Ďalšia</strong>' . "\n";
    }
    else
    {
      for($i=1; $i<=$this->num_pages; $i++)
      {
        $this->return .= ($i == $this->current_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $_SERVER["PHP_SELF"] . $this->querystring . $this->sep . 'p=' . $i . '&amp;ipp=' . $this->items_per_page . '">' . $i . '</a>';
      }
    }
    if ($this->num_pages > 0)
    {
      $this->low = ($this->current_page - 1) * $this->items_per_page;
      $this->high = ($this->current_page * $this->items_per_page) - 1;
      $this->limit = ' LIMIT ' . $this->low . ', ' . $this->items_per_page;
    }
    else
    {
      $this->limit = '';
    }
  }

  function display_items_per_page()
  {
    $items = '';
    $ipp_array = array(10, 25, 50, 100);
    foreach ($ipp_array as $ipp_opt) $items .= ($ipp_opt == $this->items_per_page) ? '<option selected="selected" value="' . $ipp_opt . '">' . $ipp_opt . '</option>' . "\n" : '<option value="' . $ipp_opt . '">' . $ipp_opt . '</option>' . "\n";
    return '<label for="ipp">Položiek na stránku:</label><select id="ipp" onchange="window.location=\'' . $_SERVER["PHP_SELF"] . $this->querystring . $this->sep. 'p=1&amp;ipp=\'+this[this.selectedIndex].value; return false;">' . $items . '</select>' . "\n";
  }

  function display_jump_menu()
  {
    $options = '';
    for($i=1; $i<=$this->num_pages; $i++)
    {
      $options .= ($i == $this->current_page) ? '<option value="' . $i . '" selected="selected">' . $i . '</option>' . "\n" : '<option value="' . $i . '">' . $i . '</option>' . "\n";
    }
    return '<label for="jumpto">Skočiť na:</label><select id="jumpto" onchange="window.location=\'' . $_SERVER["PHP_SELF"] . $this->querystring . $this->sep . 'p=\'+this[this.selectedIndex].value+\'&amp;ipp=' . $this->items_per_page . '\'; return false;">' . $options . '</select>' . "\n";
  }

  function display_pages()
  {
    return $this->return;
  }
}
$pagination = new Paginator;