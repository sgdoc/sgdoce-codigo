<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */

namespace br\gov\sial\core\util\file;
use br\gov\sial\core\lang\TFile,
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\validate\String,
    br\gov\sial\core\exception\SIALException,
    br\gov\sial\core\valueObject\ValueObjectAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.util
 * @subpackage file
 * @name Tamburete
 * @author André Borges
 * */
class Tamburete extends SIALAbstract
{
     /**
     * @var array
     */
    private static $_config = array();

    /**
     * Define a estrutura do xml de persistência de arquivos
     * @const
     */
    const XML_PERSIST      = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<file>
    <namespace>%s</namespace>
    <creationdate>%s</creationdate>
    <struct>%s</struct>
    <content>%s</content>
    <upload>
        <reference>%s</reference>
    </upload>
</file>
XML;

   /**
     * Define a estrutura do xml de persistência de Log de arquivos
     * @const
     */
    const XML_PERSIST_LOG  = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<fileLog>
    <log>
        <namespace>%s</namespace>
        <creationdate>%s</creationdate>
        <content>%s</content>
    </log>
</fileLog>
XML;

   /**
     * Define a persistência de arquivos enviados via formulário por Upload
     * @const
     */
    const XML_PERSIST_LEAF = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<file reference="%s">
    <namespace>%s</namespace>
    <path>%s</path>
    <creationdate>%s</creationdate>
    %s
</file>
XML;

    /**
     * Método construtor.
     * Recupera a relação dos campos a realizar o log de auditoria e a relação dos atributos do VO.
     * @param ValueObjectAbstract $valueObject
     * @param string $pathLeaf
     * @param mixed $pathLeaf
     * @throws \br\gov\sial\core\persist\exception\PersistException
     * */
    public function __construct (ValueObjectAbstract $valueObject, $pathLeaf = NULL, $upload = NULL)
    {
        self::$_config['logFields']     = (self::getIfDefined($valueObject->annotation()->load(), 'log')) ?
                                            $valueObject->annotation()->load()->log :
                                            NULL;
        self::$_config['xmlStruct']     = NULL;
        self::$_config['xmlContent']    = NULL;
        self::$_config['xmlContentLog'] = NULL;
        self::$_config['attrs']         = $valueObject->annotation()->load()->attrs;
        self::$_config['timeStamp']     = date("D, d M Y H:i:s");
        self::$_config['namespace']     = $this->getClassName();
        self::$_config['valueObject']   = $valueObject;
        self::$_config['upload']        = $upload;
        # $pathLeaf
        self::$_config['path']          = $pathLeaf;
    }

    /**
     * Verifica o tipo de log a persistir
     * @return string
     */
    private static function setLog ()
    {
       # definido no vo e com valor
       if(!empty(self::$_config['logFields'])) {

           self::$_config['logFields']  = explode(',', self::$_config['logFields']);

            if (strtolower(self::$_config['logFields'][0]) == 'all') {
                # Log de todos os atributos
                return self::createXmlLogPersistence('LogAll');

            } else {
                # Log de atributos anotados no Value Object
                return self::createXmlLogPersistence('LogByAttr');
            }
        }
    }

    /**
     * Estrutura de persistência
     * @return string
     */
    protected function createStruct ()
    {
        $tmp = NULL;

        foreach (self::$_config['attrs'] as $attr) {
            $tmp .= '<attr name="' . $attr->name . '" type="'. $attr->type . '" />';
        }

        return $tmp;
    }

    /**
     * Persistência do arquivo com os valores atuais
     * @return string
     */
    protected function createContent ()
    {
        $tmp = NULL;

        foreach (self::$_config['attrs'] as $attr) {
            $get = $attr->get;
            $tmp .=  '<' . $attr->name . ' type="'. $attr->type . '" value="' .
                 self::$_config['valueObject']->$get() . '" />';
        }

        return $tmp;
    }

    /**
     * Estrutura de log de persistência para todos os atributos
     * @return string
     */
    protected function createLogAll ()
    {
        $tmp = NULL;

        foreach (self::$_config['attrs'] as $attr) {
            $get = $attr->get;
            $tmp .=  '<' . $attr->name . ' type="'. $attr->type . '" value="' .
                self::$_config['valueObject']->$get() . '" />';
        }

        return $tmp;
    }

    /**
     * Estrutura de log de persistência para todos os atributos
     * @return string
     */
    protected function createLogByAttr ()
    {
        $tmp = NULL;

        foreach (self::$_config['attrs'] as $attr) {
            if (in_array($attr->name, self::$_config['logFields'])) {
                $get = $attr->get;
                $tmp .=  '<' . $attr->name . ' type="'. $attr->type . '" value="' .
                    self::$_config['valueObject']->$get() . '" />';
            }
        }

        return $tmp;
    }

    /**
     * Método que gera o conteúdo do XML para persistência de arquivos de Upload
     * @return string
     * @throws SIALException
     */
    protected function createLeaf ()
    {
        $tmp = NULL;

            $type   = self::$_config['attrs']->type->get;
            $name   = self::$_config['attrs']->name->get;
            $source = self::$_config['attrs']->source->get;
            $size   = self::$_config['attrs']->size->get;

            $tmp .= '<type>' . self::$_config['valueObject']->$type() . '</type>';
            $tmp .= '<name>' . self::$_config['valueObject']->$name() . '</name>';
            $tmp .= '<size>' . self::$_config['valueObject']->$size() . '</size>';
            $tmp .= '<content><![CDATA[' . base64_encode(file_get_contents(self::$_config['valueObject']->$source())) .
                    ']]></content>';

        return $tmp;
    }

    /**
     *
     * Método que cria a estrutura xml de acordo com o ValueObject
     * @param string $type
     * @return string|null
     */
    private static function createXmlStruct ($type)
    {
        $teste = 'create'.$type;
        $tmp = self::$teste();

        return $tmp;
    }

    /**
     * Método que cria a estrutura xml para arquivo envia por Upload
     * @return string
     */
    private static function createXmlLeaf ()
    {
        self::$_config['xmlContent'] = self::createXmlStruct('Leaf');

        return $xmlPersist = sprintf(self::XML_PERSIST_LEAF,
            self::$_config['reference'],
            self::$_config['namespace'],
            self::$_config['path'],
            self::$_config['timeStamp'],
            self::$_config['xmlContent']);
    }

    /**
     * Método responsável por gerar o XML de persistência de arquivos
     * @return string
     */
    private static function createXmlPersistence ()
    {
        self::$_config['xmlStruct']  = self::createXmlStruct('Struct');
        self::$_config['xmlContent'] =  (self::$_config['upload'] != NULL) ? NULL : self::createXmlStruct('Content');

        return $xmlPersist = sprintf(self::XML_PERSIST,
            self::$_config['namespace'],
            self::$_config['timeStamp'],
            self::$_config['xmlStruct'],
            self::$_config['xmlContent'],
            self::$_config['upload']);
    }

    /**
     * Método que prepara o xml para modificação
     * @return \SimpleXMLIterator
     * @throws SIALException
     */
    private static function prepareModificationOnPersist ($content)
    {
        $xmlIterator    = new \SimpleXMLIterator($content);

        return $xmlIterator;
    }

    /**
     * Método que atualiza o conteúdo do Root XML
     * @param string $content
     * @return string
     * @todo refatorar metodo
     * @throws SIALException
     */
    private static function updateXmlPersistence ($content)
    {
        if (NULL != self::$_config['upload']) {
            $arrContent = explode('</reference>',$content);
            $endContent = array_pop($arrContent);
            $reference  = "</reference><reference>" . self::$_config['upload'] . "</reference>";
            array_push($arrContent, $reference);
            array_push($arrContent, $endContent);
            $reference = implode('',$arrContent);
         } else {
            $arrContent = explode('</content>',$content);
            $endContent = array_pop($arrContent);
            $reference  = self::$_config['xmlContent'] . "</content>";
            array_push($arrContent, $reference);
            array_push($arrContent, $endContent);
            $reference = implode('',$arrContent);
        }

       return $reference;
    }

     /**
     * Reseta o <content> do root
     * @todo refatorar
     * @param string $content
     * @return string
     * @throws SIALException
     */
    private static function resetXmlPersistence ($content)
    {
        $arrContent = explode('</content>',$content);
        $endContent = array_pop($arrContent);
        $reference  = "</content>";

        array_push($arrContent, $reference);
        array_push($arrContent, $endContent);
        $reference = implode('',$arrContent);

        return $references;
    }

    /**
     * Método para log da persistência de arquivos
     * @param string $type
     * @return string
     */
    private static function createXmlLogPersistence ($type)
    {
        self::$_config['xmlContentLog'] = self::createXmlStruct($type);

        return $xmlPersistLog = sprintf(self::XML_PERSIST_LOG,
            self::$_config['namespace'],
            self::$_config['path'],
            self::$_config['reference'],
            self::$_config['timeStamp'],
            self::$_config['xmlContentLog']);
    }

    /**
     * Gera a referencia em hash para arquivo de log
     * @return string
     */
    private static function createReference ()
    {
        return hash("md5",self::$_config['namespace'].self::$_config['timeStamp']);
    }

    /**
     * Gera a persistência de arquivo em XML
     * @return \stdClass
     * @throws SIALException
     */
    public function save ()
    {
        self::$_config['reference'] = self::createReference();

        $result                     = new \stdClass();
        $result->root               = self::createXmlPersistence();
        $result->leaf               = (self::$_config['upload'] != NULL) ? self::createXmlLeaf() : NULL;
        $result->log                = self::setLog();

        return $result;
    }

    /**
     *
     * Método que atualiza os valores do conteúdo dos atributos do ValueObject e
     * cria log de auditoria quando solicitado.
     * @param string $content
     * @return \stdClass
     * @throws SIALException
     */
    public function update ($content)
    {
        self::$_config['reference'] = self::createReference();
        $result                     = new \stdClass();
        $result->root               = self::updateXmlPersistence($content);
        $result->leaf               = (self::$_config['upload'] != NULL) ? self::createXmlLeaf() : NULL;
        $result->log                = self::setLog();

        return $result;
    }

    /**
     *
     * Método que reseta os valores do conteúdo dos atributos do ValueObject
     * @param string $content
     * @return \stdClass
     * @throws SIALException
     */
    public function delete ($content)
    {
        self::$_config['reference'] = self::createReference();
        $result                     = new \stdClass();
        $result->root               = self::resetXmlPersistence($content);
        $result->leaf               = NULL;
        $result->log                = self::setLog();

        return $result;
    }

    /**
     *
     * Realiza busca no xmlRoot pelas referências de arquivo
     * @return \stdClass
     */
    public function find ()
    {
        $get            = self::$_config['attrs']->src->root;
        $xmlIterator    = new SimpleXMLIterator(self::$_config['valueObject']->get());
        $references     = "";

        for( $xmlIterator->rewind(); $xmlIterator->valid(); $xmlIterator->next() ) {
            foreach($xmlIterator->getChildren() as $name => $data) {
                $references .= '<reference>' . $data . '</reference>';
            }
        }

        $result = new \stdClass();
        $result->references = $references;

        return $result;
    }
}