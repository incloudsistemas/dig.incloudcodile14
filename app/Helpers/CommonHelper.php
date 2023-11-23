<?php

use Illuminate\Database\Eloquent\Relations\Relation;

if (!function_exists('ConvertPtBrFloatStringToInt')) {
    /**
     * Transforms the float string value into a int.
     *
     */
    function ConvertPtBrFloatStringToInt(mixed $value): int
    {
        $value = str_replace(".", "", $value);
        $value = str_replace(",", ".", $value);

        return round(floatval($value) * 100);
    }
}

if (!function_exists('ConvertPtBrToEnDate')) {
    /**
     * Convert date from pt-br format to en.
     *
     */
    function ConvertPtBrToEnDate(string $date): string
    {
        return date("Y-m-d", strtotime(str_replace('/', '-', $date)));
    }
}

if (!function_exists('ConvertPtBrToEnDateTime')) {
    /**
     * Convert date from pt-br format to en.
     *
     */
    function ConvertPtBrToEnDateTime(string $date): string
    {
        return date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $date)));
    }
}

if (!function_exists('ConvertPtBrToLongDate')) {
    /**
     * Convert date from pt-br format to long/full.
     *
     */
    function ConvertPtBrToLongDate(string $date): string
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
     */
    function ConvertEnToPtBrDate(string $date): string
    {
        return date("d/m/Y", strtotime($date));
    }
}

if (!function_exists('ConvertEnToPtBrDateTime')) {
    /**
     * Convert date from en format to pt-br.
     *
     */
    function ConvertEnToPtBrDateTime(string $date, bool $showSeconds = false): string
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
     */
    function LimitCharsFromString(string $string, int $numChars = 280): string
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
     */
    function SanitizeVar(string $string): string
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

        $string = preg_replace($search, $replace, $string);
        return $string;
    }
}

if (!function_exists('MorphMapByClass')) {
    /**
     * Get the model type from morphMap
     *
     */
    function MorphMapByClass(string $model): string
    {
        $morphMap = Relation::morphMap();
        return array_search($model, $morphMap, true) ?: $model;
    }
}
