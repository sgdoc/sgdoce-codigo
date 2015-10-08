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

namespace br\gov\mainapp\application\libcorp\situacaoFuncional\mvcb\business;
use br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

 /**
  * SISICMBio
  *
  * @package br.gov.icmbio.sisicmbio.application.sica.situacaoFuncional.mvcb
  * @subpackage business
  * @author Fabio Lima <fabioolima@gmail.com>
  * */
class SituacaoFuncionalBusiness extends ParentBusiness
{
    /**
     * Lista os tipos de cargo para exibir em uma combo
     * @return array[]
     * @throws BusinessException
     */
    public function getSituacaoCmb ()
    {
        try {
            $result = $this->getModelPersist('libcorp')->orderByNoSituacaoFuncional()->findAll()->getAllDataViewObject();
            return $this->arrayObjectToCombo('noSituacaoFuncional', 'sqSituacaoFuncional', $result);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }
}
