<?php

/****************************************************************************************************

Creates interactive calendar for calendar's page

****************************************************************************************************/

// set timezone to America: New York
date_default_timezone_set("America/New_York");
/**
* My calendar class
*/
class CalendarScript
{
    private $currentDay;
    private $currentMonth;
    private $currentYear;
    private $hrefNav;
    private $dayArray = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
    private $endCycle = 1;
    private $startCycle;
    private $date2Amp = '';
    private $date2 = '';

    function __construct()
    {        
        // store current link in variable
        $this->hrefNav = htmlentities($_SERVER['PHP_SELF']);
    }

    // prints out calendar
    function create() {

        // get variable for second calendar date
        if (isset($_GET['date2'])) {
            $this->date2Amp = "&date2=".$_GET['date2'];
            $this->date2 = "date2=".$_GET['date2'];
        }

        // Set Year, Month, and Day
        if (isset($_GET['date'])) {
            $chosenDate = $_GET['date'];
            $date = explode('-', $chosenDate);
            $this->currentYear  = $date[0];
            $this->currentMonth = $date[1];
            $this->currentDay   = $date[2];
        }
        else {
            $this->currentYear = date('Y');
            $this->currentMonth = date('m');
            $this->currentDay = date('d');
        }

        // html code to be printed to create the page
        $contents = $this->createForm().
                        '<div class="col-sm-12 table-responsive">'.
                            $this->createNavi().
                            '<table id="calendar" class="table table-borderless">'.
                                '<thead>'.
                                    '<tr>'.
                                        '<th>MON</th>'.
                                        '<th>TUE</th>'.
                                        '<th>WED</th>'.
                                        '<th>THU</th>'.
                                        '<th>FRI</th>'.
                                        '<th>SAT</th>'.
                                        '<th>SUN</th>'.
                                    '</tr>'.
                                '</thead>'.
                                '</tbody>'.
                                    $this->createCells().
                                '</tbody>'.
                            '</table>'.
                        '</div>'.
                    '</div>';
        return $contents;
    }

    // create form to allow user to select a date
    private function createForm() {
        $date = $this->currentYear.'-'.$this->currentMonth.'-'.$this->currentDay;

        // search for date using input bar
        if (isset($_POST['submitCalendarDate'])) {
            $date = $_POST['calendarDate'];
            header('location: '.$this->hrefNav.'?date='.$date.$this->date2Amp.'#calendar');
        }

        $contents = '<div class="col-sm-6">'.
                        '<div class="col-sm-9">'.
                            '<form action="#calendar" class="form text-center" role="form" method="post">'.
                                ' Choose Date: <input type="date" name="calendarDate" />'.
                                '<input type="submit" value="submit" name="submitCalendarDate" />'.
                            '</form>'.
                        '</div>'.
                        '<div class="col-sm-3">'.
                            '<a href="'.$this->hrefNav.'?'.$this->date2.'#calendar"><button>Today</button></a>'.
                        '</div>';
        return $contents;
    }

    // create navigation
    private function createNavi() {
        
        // update month and year by one (including wrap around for months)
        $nextMonth = $this->currentMonth==12?1:intval($this->currentMonth)+1;
        $nextYear = $this->currentMonth==12?intval($this->currentYear)+1:$this->currentYear;
        $preMonth = $this->currentMonth==1?12:intval($this->currentMonth)-1;
        $preYear = $this->currentMonth==1?intval($this->currentYear)-1:$this->currentYear;

        // return html code to include links for next/previous months
        return
            '<ul class="pager">'.
                '<li class="previous"><a href="'.$this->hrefNav.'?date='.sprintf('%02d',$preYear).
                    '-'.$preMonth.'-0'.$this->date2Amp.'#calendar"><span class="glyphicon glyphicon-chevron-left"></span></a></li>'.
                '<li><b style="font-size: 150%">'.date('M Y',strtotime($this->currentYear.'-'.$this->currentMonth.'-1')).'</b></li>'.
                '<li class="next"><a href="'.$this->hrefNav.'?date='.sprintf("%02d", $nextYear).'-'.
                    $nextMonth.'-0'.$this->date2Amp.'#calendar"><span class="glyphicon glyphicon-chevron-right"></span></a></li>'.
            '</ul>';
    }

    // create cell
    private function createCells() {

        // first day in the selected month
        $startDate = strtotime($this->currentYear . '-' . $this->currentMonth . '-01');
        // last day in the selected month
        $endDate = strtotime("+1 month", $startDate);
        // today's date
        $currDate = date('d-m-Y', strtotime("now"));
        // html code to be returned
        $contents = '<tr>';

        // find the index of the first day in the month
        $firstDay;
        for ($i=0; $i < 7; $i++) { 
            if ($this->dayArray[$i] == date('D', $startDate))
                $firstDay = $i;
        }

        // create the empty <td> tags that come before the first day of the month
        for ($i=0; $i < $firstDay; $i++)  
           $contents.=$this->emptyCell(false, $firstDay - 1, $i);

        // create the rest of the <td> tags
        for ($row=0; $startDate < $endDate; $row++) { 
            // create new <tr> for each row
            if ($row > 0)
                $contents.='<tr>';
            for ($col=0; $col < 7; $col++) {
                // checks if $startDate is today's date
                $isCurrDate = (date('d-m-Y', $startDate) == $currDate);
                // create tags starting with the first day of the month
                if ($row==0) {
                    $col=$firstDay++;
                    $formattedDay = date('d', $startDate);
                    $contents.=$this->addDigitsClass($formattedDay, $isCurrDate);
                    $startDate = strtotime("+1 day", $startDate); 
                }
                else {
                    $formattedDay = date('d', $startDate);
                    // create empty <td> tags once end of month has been reached
                    if (!($startDate < $endDate)) 
                        $contents.=$this->emptyCell(true, -1, -1);
                    else {
                        $contents.= $this->addDigitsClass($formattedDay, $isCurrDate);
                        $startDate = strtotime("+1 day", $startDate);
                    }
                }
            }
            $contents.='</tr>';
        }
        return $contents;
    }

    // create <td> tags for nonempty cells: dates (1-31)
    private function addDigitsClass($formattedDay, $isCurrDate) {
        // add class for cells with two digits: (dates 10-31)
        $digits = 'double';
        if ($formattedDay < 10) {
            // add class for cells with one digit: (dates 1-9)
            $digits = 'single';
        }
        return $this->addSelectClass($formattedDay, $isCurrDate, $digits);
    }

    private function addSelectClass($formattedDay, $isCurrDate, $digits) {
        $addSelected = '';
        if ($formattedDay == $this->currentDay) {
            // Add class 'selected' to <td>
            $addSelected = 'selected ';
        }
        return $this->createTag($formattedDay, $isCurrDate, $digits, $addSelected);
    }

    private function createTag($formattedDay, $isCurrDate, $digits, $addSelected) {
        $addToday = ' ';
        if ($isCurrDate) {
            $addToday = ' today ';
        }

        $date = $this->currentYear.'-'.$this->currentMonth.'-'.$formattedDay;

        require_once('mysql_connect.php');
        // check if this date has any events
        $query = "SELECT `eventName` FROM `events` where `date`='".$date."'";
        $result = mysql_query($query) or die("Could not search!");
        $count = mysql_num_rows($result);

        $hasEvent = '';
        if ($count != 0) {
            $hasEvent = ' hasEvent';
        }

        return '<td class="day'.$hasEvent.$addToday.$addSelected.$digits.
            '" id="'.$date.'">'.sprintf('%01d',$formattedDay).'</td>';
    }

    // create <td> tags for empty cells
    private function emptyCell($isEnd, $overlap, $i) {
        if ($isEnd)
            return '<td class="day empty">'.$this->endCycle++.'</td>';
        else {
            if ($i == 0) {
                // timestamp for first day of current month
                $thisMonthTimestamp = strtotime('01-'.$this->currentMonth.'-'.$this->currentYear);
                // timestamp for last day of previous month
                $lastDayPreMonthTimestamp = strtotime('-1 day', $thisMonthTimestamp);
                // number of days in previous month
                $numOfDay = intval(date('d', $lastDayPreMonthTimestamp));
                $this->startCycle = $numOfDay - $overlap;
                return '<td class="day empty">'.$this->startCycle++.'</td>';
            }
            else
                return '<td class="day empty">'.$this->startCycle++.'</td>';
        }
    }

    // test function: prints out all the days in the month
    private function test() {
        $startDate = strtotime($this->currentYear . '-' . $this->currentMonth . '-01');
        $endDate = strtotime("+1 month", $startDate);
        while ($startDate < $endDate) {
            echo date('Y M d', $startDate) . ': ' . date('D', $startDate) . "<br>";
            $startDate = strtotime("+1 day", $startDate);
        }       
    }
}
?>