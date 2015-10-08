<?php
use Doctrine\ORM\Query\Lexer,
    Doctrine\ORM\Query\AST\Functions\FunctionNode;

class Core_Doctrine_ORM_Query_AST_ToCharFunction extends FunctionNode
{
    /**
     * @override
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        $platform = $sqlWalker->getConnection()->getDatabasePlatform();

        if (!method_exists($platform, 'getToChar')) {
            return $sqlWalker->walkStringPrimary($this->firstStringPrimary);
        }

        return $platform->getToChar(
                $sqlWalker->walkStringPrimary($this->firstStringPrimary),
                $sqlWalker->walkStringPrimary($this->secondStringPrimary)
        );
    }

    /**
     * @override
     * to_char("column","pattern")
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->firstStringPrimary = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->secondStringPrimary = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
