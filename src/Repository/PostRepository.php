<?php

namespace App\Repository;

use App\Entity\Post;
use App\DTO\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    private $paginator; 
    private $searchData; 

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator, SearchData $searchData)
    {
        parent::__construct($registry, Post::class);
        $this->paginator = $paginator;
        $this->searchData = $searchData;
    }


    public function findPublished(int $page,$categorie=null):PaginationInterface
    {
        $data = $this->createQueryBuilder('p')
        ->andWhere('p.state = :state')
        ->setParameter('state', 'PUBLISHED')
        ->orderBy('p.createdAt', 'DESC');
        if($categorie != null){
            $data->join('p.category', 'categ')
                    ->andWhere('categ = :categ')
                    ->setParameter('categ', $categorie);

        };

        $query = $data
        ->getQuery()
        ->getResult();
        $posts = $this->paginator->paginate(
            $query,
            $page,
            10
        );
        return $posts;}

        public function searchResult():PaginationInterface{
              $data = $this->createQueryBuilder('p')
        ->andWhere('p.state = :state')
        ->setParameter('state', 'PUBLISHED')
        ->orderBy('p.createdAt', 'DESC');

        if($this->searchData->query){
            $data->andWhere('p.title LIKE :query')
            ->setParameter('query', '%'.$this->searchData->query.'%');
             }

             if($this->searchData->categories){
                $data->join('p.category', 'categ')
                 ->andWhere('categ.id IN (:categories)')
                 ->setParameter('categories', $this->searchData->categories);

             }

           $query = $data
           ->getQuery()
           ->getResult();

           $query = $this->paginator->paginate($query, $this->searchData->page, 10);
        return $query;

       
        
        
        }

      

    //    /**
    //     * @return Post[] Returns an array of Post objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
