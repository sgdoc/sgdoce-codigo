<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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

namespace br\gov\mainapp\application\libcorp\vinculoFuncional\mvcb\business;
use br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness,
    br\gov\mainapp\application\libcorp\vinculoFuncional\valueObject\VinculoFuncionalValueObject;
    

 /**
  * SISICMBio
  *
  * @package br.gov.icmbio.sisicmbio.application.sica.vinculoFuncional.mvcb
  * @subpackage business
  * @author Fabio Lima <fabioolima@gmail.com>
  * */
class VinculoFuncionalBusiness extends ParentBusiness
{
    /**
     * Insere ou atualiza Vinculo Funcional
     * @param EmailValueObject $email
     */
    public function save (VinculoFuncionalValueObject $voVinculoFuncional)
    {
        try {
            $voTmp = VinculoFuncionalValueObject::factory()->loadData($voVinculoFuncional->toArray());
            $voTmp->setDtInicioVinculo(NULL);
            $voTmp = $this->getModelPersist('libcorp')->findByParam($voTmp)->getDataViewObject();

            if ($voTmp->isEmpty()) {
                $this->getModelPersist('libcorp')->save($voVinculoFuncional);
            } else {
                $voVinculoFuncional->setSqVinculoFuncional($voTmp->getSqVinculoFuncional());
                $this->getModelPersist('libcorp')->update($voVinculoFuncional);
            }
            return $voVinculoFuncional;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }
}