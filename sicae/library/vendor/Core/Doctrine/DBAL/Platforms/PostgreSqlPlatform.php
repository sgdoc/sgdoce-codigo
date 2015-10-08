<?php

use Doctrine\DBAL\Platforms\PostgreSqlPlatform;

class Core_Doctrine_DBAL_Platforms_PostgreSqlPlatform extends PostgreSqlPlatform
{
    public function getTranslateExpression($value, $from, $to)
    {
        return "TRANSLATE($value, '" . $from . "', '" . $to . "')";
    }

    public function getClearAccetuationExpression($value, $quote = FALSE)
    {
        $from = 'áàâãäéèêëíìîĩïóòôõöúùûũüÁÀÂÃÄÉÈÊËÍÌÎĨÏÓÒÔÕÖÚÙÛŨÜçÇ';
        $to = 'aaaaaeeeeiiiiiooooouuuuuAAAAAEEEEIIIIIOOOOOUUUUUcC';

        if ($quote) {
            $value = "'$value'";
        }

        return $this->getTranslateExpression($value, $from, $to);
    }

    public function getStringAgg($primaryParam, $secondParam)
    {
        return 'STRING_AGG(' . $primaryParam . ', "," ORDER BY ' . $secondParam. ')';
    }
}
