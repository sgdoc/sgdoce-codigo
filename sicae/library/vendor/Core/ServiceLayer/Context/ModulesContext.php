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
use Bisna\Service\Context\ContextImpl;
/**
 * @category   Service Layer
 * @package    Core
 * @subpackage ServiceLayer
 * @subpackage Context
 * @name       ModulesContext
 */
class Core_ServiceLayer_Context_ModulesContext extends ContextImpl
{
    /**
     * Constructor.
     *
     * @param  array $config Context configuratio
     * @return void
     */
    public function __construct($path, array $config = array())
    {
        $suffix    = isset($config['suffix']) ? $config['suffix'] : 'Service';
        $namespace = isset($config['namespace']) ? $config['namespace'] : 'Service';

        $folderNameService = isset($config['folderNameService'])
                           ? $config['folderNameService']
                           : 'services';

        $separator  = isset($config['separatorClass'])
                    ? $config['separatorClass']
                    : '\\';

        $path  = realpath($path);
        $files = glob("$path/*/$folderNameService/*.php", GLOB_NOSORT);

        if (empty($files)) {
            return;
        }

        foreach ($files as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }

            $moduleName   = substr($file, strlen($path) + 1);
            $moduleName   = substr($moduleName, 0, strpos($moduleName, '/'));
            $filter       = new Zend_Filter_Word_DashToCamelCase();
            $fileName     = str_replace(array($suffix, '.php'), '', basename($file));
            $serviceClass = $filter->filter($moduleName) . $separator
                          . $namespace . $separator
                          . $fileName;

            unset($config['class']);
            $this->bind($fileName, $serviceClass, $config);
        }
    }
}
