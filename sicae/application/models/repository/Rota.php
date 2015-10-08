<?php
/*
 * Copyright 2012 ICMBio
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

namespace Sica\Model\Repository;

use Doctrine\Common\Util\Debug;
use Sica\Model\Entity;

/**
 * SISICMBio
 *
 * Classe para Repository de Sistemas
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 Sistema
 * @version	 1.0.0
 */

class Rota extends \Sica_Model_Repository
{
    public function save(Entity\Funcionalidade $entity, $dtos)
    {
        $querybuilderUpdate = $this->_em->createQueryBuilder();
        $querybuilderUpdate->update('app:Funcionalidade', 'f')
                          ->set('f.sqRotaPrincipal', 'NULL')
                          ->where($querybuilderUpdate->expr()->eq('f.sqFuncionalidade', ':funcionalidade'))
                          ->setParameter('funcionalidade', $entity->getSqFuncionalidade());

        $querybuilderUpdate->getQuery()->execute();

        $funcionalidades = $this->getEntityManager()->getRepository('app:Rota')->findBySqFuncionalidade($entity->getSqFuncionalidade());

        foreach ($funcionalidades as $funcionalidade){
            $this->getEntityManager()->remove($funcionalidade);
            $this->getEntityManager()->flush();
        }

        foreach ($dtos as $dto) {
            $dto->setSqFuncionalidade($entity);
            $entityRota = $dto->getEntity();
            $this->_em->persist($entityRota);

            if ($entityRota->getInRotaPrincipal()) {
                $entity->setSqRotaPrincipal($entityRota);
            }
        }
    }
}