<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
namespace Auxiliar\Service;
/**
 * Classe para Service de Carimbo
 *
 * @category Service
 * @package  Auxiliar
 * @name   Carimbo
 * @version  1.0.0
  */
class VwIntegracaoSistema extends \Core_ServiceLayer_Service_Temp
{
    /**
     * @var string
     */
    protected $_enNameCanie    = 'app:VwIntegracaoCanie';
    protected $_enNameSgca     = 'app:VwIntegracaoSgca';
    protected $_enNameEspecie  = 'app:VwIntegracaoTaxon';
    protected $_enNameUnidade  = 'app:VwIntegracaoUnidade';

    /**
     * Configura uma entidade para inserir no banco de dados
     * @param string $entityName nome da entidade
     */
    public function canieCavernaAutoComplete($dtoSearch)
    {
        $entidade = '';
        switch ($dtoSearch->getExtraParam()) {
            case 0:
                $entidade = $this->_enNameCanie;
                break;
            case 1:
                $entidade = $this->_enNameEspecie;
                break;
            case 2:
                $entidade = $this->_enNameSgca;
                break;
            case 3:
                $entidade = $this->_enNameUnidade;
                break;
        }

        return $this->_getRepository($entidade)->sistemaAutoComplete($dtoSearch,$entidade);
    }
}
