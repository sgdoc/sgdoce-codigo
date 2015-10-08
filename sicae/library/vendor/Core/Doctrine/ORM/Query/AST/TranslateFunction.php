<?php
use Doctrine\ORM\Query\Lexer,
    Doctrine\ORM\Query\AST\Functions\FunctionNode;

class Core_Doctrine_ORM_Query_AST_TranslateFunction extends FunctionNode
{
    /**
     * @override
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        $platform = $sqlWalker->getConnection()->getDatabasePlatform();

        if (!method_exists($platform, 'getTranslateExpression')) {
            return $sqlWalker->walkStringPrimary($this->firstStringPrimary);
        }

        return $platform->getTranslateExpression(
                $sqlWalker->walkStringPrimary($this->firstStringPrimary),
                $sqlWalker->walkStringPrimary($this->stringFrom),
                $sqlWalker->walkStringPrimary($this->stringTo)
        );
    }

    /**
     * @override
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->firstStringPrimary = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->stringFrom = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->stringTo   = $parser->StringPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
