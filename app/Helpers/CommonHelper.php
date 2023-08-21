<?php

if (!function_exists('ConvertStringToFloat')) {

    /**
     * Transforms the string value into a float.
     *
     * @param
     * @return
     */
    function ConvertNumStringToFloat($value)
    {
        // removing all chars from the var.
        $value = trim(preg_replace("/[^0-9\s]/", "", $value));

        // Var size.
        $size = strlen($value);

        // Formatting the var with the format supported by the B.D. Float/Double.
        $value = substr_replace($value, '.', $size - 2) . substr($value, $size - 2);

        return (float) $value;
    }
}

if (!function_exists('ConvertPtBrToEnDate')) {

    /**
     * Convert date from pt-br format to en.
     *
     * @param
     * @return
     */
    function ConvertPtBrToEnDate($date)
    {
        return date("Y-m-d", strtotime(str_replace('/', '-', $date)));
    }
}

if (!function_exists('ConvertPtBrToEnDateTime')) {

    /**
     * Convert date from pt-br format to en.
     *
     * @param
     * @return
     */
    function ConvertPtBrToEnDateTime($date)
    {
        return date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $date)));
    }
}

if (!function_exists('ConvertPtBrToLongDate')) {

    /**
     * Convert date from pt-br format to long/full.
     *
     * @param
     * @return
     */
    function ConvertPtBrToLongDate($date)
    {
        $weekday = [

            'Sunday'    => 'Domingo',
            'Monday'    => 'Segunda-Feira',
            'Tuesday'   => 'Terça-Feira',
            'Wednesday' => 'Quarta-Feira',
            'Thursday'  => 'Quinta-Feira',
            'Friday'    => 'Sexta-Feira',
            'Saturday'  => 'Sábado'
        ];

        $month = [

            'January'   => 'Janeiro',
            'February'  => 'Fevereiro',
            'March'     => 'Março',
            'April'     => 'Abril',
            'May'       => 'Maio',
            'June'      => 'Junho',
            'July'      => 'Julho',
            'August'    => 'Agosto',
            'September' => 'Setembro',
            'October'   => 'Outubro',
            'November'  => 'Novembro',
            'December'  => 'Dezembro'
        ];

        $dateFormat = date("l, d \d\e F \d\e Y", strtotime(str_replace('/', '-', $date)));

        foreach ($weekday as $en => $ptBr) {

            $dateFormat = str_replace($en, $ptBr, $dateFormat);
        }

        foreach ($month as $en => $ptBr) {

            $dateFormat = str_replace($en, $ptBr, $dateFormat);
        }

        return $dateFormat;
    }
}

if (!function_exists('ConvertEnToPtBrDate')) {

    /**
     * Convert date from en format to pt-br.
     *
     * @param
     * @return
     */
    function ConvertEnToPtBrDate($date)
    {
        return date("d/m/Y", strtotime($date));
    }
}

if (!function_exists('ConvertEnToPtBrDateTime')) {

    /**
     * Convert date from en format to pt-br.
     *
     * @param
     * @return
     */
    function ConvertEnToPtBrDateTime($date, $showSeconds = null)
    {
        if ($showSeconds) {

            return date("d/m/Y H:i:s", strtotime($date));
        }

        return date("d/m/Y H:i", strtotime($date));
    }
}

if (!function_exists('LimitCharsFromString')) {

    /**
     * Limita a string em relação aos caracteres.
     *
     * @param
     * @return
     */
    function LimitCharsFromString($string, $numChars = 280)
    {
        if (strlen($string) <= $numChars) {

            return $string;
        }

        $string = substr($string, 0, $numChars) . '...';
        return $string;
    }
}

if (!function_exists('SanitizeVar')) {

    /**
     * Clear the variable, removing special characters, spaces, etc...
     *
     * @param
     * @return
     */
    function SanitizeVar($value)
    {
        $search = [

            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        ];

        $replace = [

            '>',
            '<',
            '\\1',
            ''
        ];

        $value = preg_replace($search, $replace, $value);

        return $value;
    }
}


