<?php

class MyDate {

    public $day;
    public $month;
    public $year;

    function __construct($year, $month, $day)
    {
        $this->day = (int)$day;
        $this->month = (int)$month;
        $this->year = (int)$year;
    }

    private function getTotalDaysInMonths()
    {
        $total_days = 0;
        for($m = 1; $m <= $this->month; $m++)
        {
            $total_days += $this->getDaysInMonth($m);
        }
        return $total_days;
    }

    private function getTotalDaysInYearsToCurrentYear()
    {
        $leapYears = (int)($this->year / 4);
        $commonYears = $this->year - $leapYears;
        return $leapYears * 366 + $commonYears * 365;
    }

    /**
     * @param int $month
     * @return int
     */
    private function getDaysInMonth($month)
    {
        $month = (int)$month;
        $month_days = [
            1 => 31,
            2 => 28,
            3 => 31,
            4 => 30,
            5 => 31,
            6 => 30,
            7 => 31,
            8 => 31,
            9 => 30,
            10 => 31,
            11 => 30,
            12 => 31,
        ];
        if($month == 2 && $this->isLeapYear())
            return $month_days[$month] + 1;
        return $month_days[$month];
    }

    /**
     * @return bool
     */
    private function isLeapYear()
    {
        return $this->year % 4 == 0;
    }

    private function fromStr($str)
    {
        list($years, $months, $days) = explode('/',$str);
        return new static($years, $months, $days);
    }

    /**
     * @param MyDate $sDate
     * @param MyDate $eDate
     * @return int
     */
    private function diffTotalDays($sDate, $eDate)
    {
        $days = $eDate->day - $sDate->day;
        if($sDate->year != $eDate->year) $days += $eDate->getTotalDaysInYearsToCurrentYear() - $sDate->getTotalDaysInYearsToCurrentYear();
        if($sDate->month != $eDate->month) $days += $eDate->getTotalDaysInMonths() - $sDate->getTotalDaysInMonths();
        return $days;
    }

    public static function diff($start, $end) {
        $sDate = static::fromStr($start);
        $eDate = static::fromStr($end);
        $days = $eDate->day - $sDate->day;
        $months = $eDate->month - $sDate->month;
        if($days < 0)
        {
            if($sDate->year == $eDate->year and $sDate->month == $eDate->month){
                $days = abs($eDate->day - $sDate->day);
            } else {
                $months = ($eDate->month - 1) - $sDate->month;
                $days = abs($days + $eDate->getDaysInMonth($eDate->month));
            }
        }

        if($months < 0)
        {
            $years = ($eDate->year - 1) - $sDate->year;
            $months = abs($months + 12);
        }else
        {
            $years = abs($eDate->year - $sDate->year);
        }
        $total_days = static::diffTotalDays($sDate, $eDate);
        $invert = $total_days < 0 ? true: false;
        $total_days = abs($total_days);
        // Sample object:
        return (object)array(
            'years' => $years,
            'months' => $months,
            'days' => $days,
            'total_days' => $total_days,
            'invert' => $invert
        );
    }
}
