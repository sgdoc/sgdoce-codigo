<?php
use Doctrine\ORM\Query\Lexer,
    Doctrine\ORM\Query\AST\Functions\FunctionNode;

class Core_Doctrine_ORM_Query_AST_StringAggFunction extends FunctionNode
{
    /**
     * @override
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        $platform = $sqlWalker->getConnection()->getDatabasePlatform();

        if (!method_exists($platform, 'getStringAgg')) {
            return $sqlWalker->walkStringPrimary($this->firstStringPrimary);
        }

        return $platform->getStringAgg(
                $sqlWalker->walkStringPrimary($this->firstStringPrimary),
                $sqlWalker->walkStringPrimary($this->secondStringPrimary)
        );
    }

    /**
     * @override
     * string_agg("column",  "delimiter" order by)
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->firstStringPrimary = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);

        $parser->match(Lexer::T_NONE);
        $parser->match(Lexer::T_COMMA);
        $parser->match(Lexer::T_NONE);

        $parser->match(Lexer::T_ORDER);
        $parser->match(Lexer::T_BY);

        $this->secondStringPrimary = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
