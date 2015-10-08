<?php
/**
 * Copyright 2012 do ICMBio
 *
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
/**
 * Adapter abstrato para geração de documentos em
 * diversos formatos com base em uma massa de dados
 * e um template dado.
 *
 * SOBRESCREVA O MÉTODO convertHtmlToFormat AO CRIAR OUTRO ADAPTER!
 *
 * @package      Core
 * @subpackage   Doc
 * @subpackage   Adapter
 * @name         Abstract
 * @category     Adapter
 */
abstract class Core_Doc_Adapter_Abstract
{
    protected $doc;

    protected $docTemplate;

    /**
     * Inicia a geração do documento
     *
     * @param array  $docdata
     * @param unknown_type $doctemplate
     */
    public final function docGen($doctemplate, array $docdata = array(), $path = NULL, $orientation='P')
    {
        $this->injectDataIntoTemplate($doctemplate, $docdata, $path);

        $this->convertHtmlToFormat($orientation);

        return $this->getDoc();
    }

    /**
     * Insert specified data into template
     *
     * @param unknown_type $docdata
     * @param unknown_type $doctemplate
     */
    public final function injectDataIntoTemplate($doctemplate, array $docdata = array(), $path = NULL)
    {
        $this->docTemplate = new Zend_Layout(NULL, FALSE);
        $this->docTemplate->setLayout($doctemplate)
               ->setView(new Zend_View());

        if (NULL !== $path) {
            $this->docTemplate->setLayoutPath($path);
        }

        $this->docTemplate->assign($docdata);
    }

    /**
     * Gets the content of the generated doc
     */
    public final function getDoc()
    {
        return $this->doc;
    }

    public function getDocTemplate()
    {
        return $this->docTemplate;
    }

    /**
     * This is where the adapter does the convertion voodoo
     */
    abstract public function convertHtmlToFormat($orientation='P');
}