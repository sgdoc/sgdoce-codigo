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

namespace Artefato\Service;

/**
 * Classe para Service de VwConsultaArtefato
 *
 * @package  Artefato
 * @category Service
 * @name     VwConsultaArtefato
 * @version  1.0.0
 */
class VwConsultaArtefato extends \Core_ServiceLayer_Service_CrudDto
{

      /**
     * @var string
     */
    protected $_entityName = 'app:VwConsultaArtefato';


     /**
     * Método que retorna os dados da list grid padrão
     * @param \Core_Dto_Search  $dto
     * @return array
     */
    public function listGridConsultaArtefatoPadrao(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->searchPageDto('listGridConsultaArtefatoPadrao',$dto);
    }

     /**
     * Método que retorna os dados da list grid avançado
     * @param \Core_Dto_Search  $dto
     * @return array
     */
    public function listGridConsultaArtefatoAvancado(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->searchPageDto('listGridConsultaArtefatoAvancado',$dto);
    }

    /**
     * método que faz o alto complete na consulta artefato
     * @param \Core_Dto_Abstract $dto
     */
    public function searchTituloDossie(\Core_Dto_Search $dto)
    {
        return $this->_getRepository('app:Artefato')->searchTituloDossie($dto);
    }
}