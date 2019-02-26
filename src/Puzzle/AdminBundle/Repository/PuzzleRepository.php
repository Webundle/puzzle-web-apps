<?php

namespace Puzzle\AdminBundle\Repository;

use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\ORM\Tree\Tree;

/**
 * PuzzleRepository
 *
 * This class contains all commons methods and must be extends by all others repositories
 */
class PuzzleRepository extends \Doctrine\ORM\EntityRepository
{
    use Tree;
    
    /**
     * Custom fin by
     * 
     * @param array $fields
     * @param array $joins
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @param bool $useCache
     * @return Collection
     */
    public function customFindBy(
        array $fields = null, 
        array $joins = null, 
        array $criteria = null, 
        array $orderBy = null, 
        int $limit = null, 
        int $offset = null
     ){
         $query= self::customGetQuery($fields, $joins, $criteria, $orderBy, $limit, $offset);
        return $query->getResult();
    }
    
    /**
     * Custom find one by
     * 
     * @param array $fields
     * @param array $joins
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @param bool $useCache
     * @return Collection|NULL
     */
    public function customFindOneBy(
        array $fields = null,
        array $joins = null,
        array $criteria = null,
        array $orderBy = null,
        int $limit = null,
        int $offset = null
     ){
        $query = self::customGetQuery($fields, $joins, $criteria, $orderBy, $limit, $offset);
        return count($query->getResult()) > 0 ? $query->getResult()[0] : null;
    }
    
    /**
     * Count by
     *
     * @param array $joins
     * @param array $criteria
     * @return boolean
     */
    public function countBy(array $joins = null, array $criteria = null) {
        $queryBuilder = $this->_em
                            ->createQueryBuilder()
                            ->select('COUNT (DISTINCT o.id)')
                            ->from($this->_entityName, 'o');
        
        if (count($joins) > 0) {
            foreach ($joins as $join => $alias) {
                $queryBuilder = $queryBuilder->innerJoin('o.'.$join, $alias);
            }
        }
        
        $parameters = [];
        if (count($criteria) > 0) {
            foreach ($criteria as $key => $criterion) {
                $predicates = count(explode('.', $criterion['key'])) > 1 ? $criterion['key'] : 'o.'.$criterion['key'];
                $predicates .= empty($criterion['operator']) === false ?' '.$criterion['operator'] : " =";
                
                if ($criterion['value'] !== null) {
                    $predicates .= " :". str_ireplace('.', '', $criterion['key']);
                    $parameters[str_ireplace('.', '', $criterion['key'])] = $criterion['value'];
                }
                
                if ($key == 0) {
                    $queryBuilder = $queryBuilder->where($predicates);
                }elseif ($key > 0 && count($criterion) < 4) {
                    $queryBuilder = $queryBuilder->andWhere($predicates);
                }else {
                    $queryBuilder = $queryBuilder->orWhere($predicates);
                }
            }
        }
        
        if (count($parameters) > 0) {
            $queryBuilder = $queryBuilder->setParameters($parameters);
        }
        
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
    
    
    /**
     * Get custom query
     * 
     * @param array $fields
     * @param array $joins
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @param bool $useCache
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    public function customGetQuery(
        array $fields = null,
        array $joins = null,
        array $criteria = null,
        array $orderBy = null,
        int $limit = null,
        int $offset = null
        ) {
            if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $key => $field) {
                $fields[$key] = 'o.'.$field;
            }
        }else {
            $fields = ['o'];
        }
        
        $queryBuilder = $this->_em
                            ->createQueryBuilder()
                            ->select(implode(',', $fields))
                            ->from($this->_entityName, 'o');
        
        if (is_array($joins) && count($joins) > 0) {
            foreach ($joins as $join => $alias) {
                $queryBuilder = $queryBuilder->innerJoin('o.'.$join, $alias);
            }
        }
        
        $parameters = [];
        if (is_array($criteria) &&  count($criteria) > 0) {
            foreach ($criteria as $key => $criterion) {
                $predicates = count(explode('.', $criterion['key'])) > 1 ? $criterion['key'] : 'o.'.$criterion['key'];
                $predicates .= empty($criterion['operator']) === false ? ' '.$criterion['operator'] : " =";
                
                if ($criterion['value'] !== null) {
                    $predicates .= " :". str_ireplace('.', '', $criterion['key']);
                    $parameters[str_ireplace('.', '', $criterion['key'])] = $criterion['value'];
                }
                
                if ($key == 0) {
                    $queryBuilder = $queryBuilder->where($predicates);
                }elseif ($key > 0 && count($criterion) < 4) {
                    $queryBuilder = $queryBuilder->andWhere($predicates);
                }else {
                    $queryBuilder = $queryBuilder->orWhere($predicates);
                }
            }
        }
        
        if (count($parameters) > 0) {
            $queryBuilder = $queryBuilder->setParameters($parameters);
        }
        
        if (is_array($orderBy) && count($orderBy) > 0) {
            foreach ($orderBy as $sort => $order) {
                $queryBuilder = $queryBuilder->orderBy('o.'.$sort, $order);
            }
        }
        
        if (is_int($limit)) {
            $queryBuilder = $queryBuilder->setMaxResults($limit);
        }
        
        if (is_int($offset)) {
            $queryBuilder = $queryBuilder->setFirstResult($offset);
        }
        
        return $queryBuilder->getQuery();
    }
}
