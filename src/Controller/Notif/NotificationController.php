<?php

namespace App\Controller\Notif;

use App\Entity\Article;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManager;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Notif\NotificationRepository;
use FOS\RestBundle\Request\ParamFetcherInterface;
use App\Repository\Notif\NotificationUserRepository;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Notification controller.
 *
 * @Route("/api")
 */
class NotificationController extends AbstractController
{

    private $listOfSharableContent;

    public function __construct(){

        $this->listOfSharableContent = [
            'album'     => \App\Entity\Music\Album::class,
            'playlist'  => \App\Entity\Music\Playlist::class,
            'track'     => \App\Entity\Music\Track::class,
            'user'      => \App\Entity\User\User::class,
            'podcast'   => \App\Entity\Music\Podcast::class,
        ];
    }
    /**
     * Liste des Notifications.
     * @FOSRest\Get(path= "/notifications", name="notif_get_all")
     * @FOSRest\View(
     *     populateDefaultVars=false,
     *     serializerGroups = {"listNotifs"}
     * )
     *
     */
    public function getNotifsAction(UserRepository $userRepo, NotificationUserRepository $nuRepo, EntityManagerInterface $em)
    {
        $datas = [];
        $user = $userRepo->find(1); // on est l'utilisateur 1 pour la demo
        $countAllNotifs = $nuRepo->findListForCounting($user);
        $datas['notifications_count'] = $countAllNotifs;
        if($countAllNotifs<1){
            $datas['message'] = "Aucune notification pour l'instant";
           return View::create($datas, Response::HTTP_OK, []);
        }
        $countAllNotifsNotSeen = $nuRepo->findListForCounting($user, true);
        $notifs = $nuRepo->findList($user);
        foreach($notifs as $notif){
            if($notif->getNotification()->getSharedRef() && $notif->getNotification()->getSharedId() && array_key_exists($notif->getNotification()->getSharedRef(),$this->listOfSharableContent)){
                $content = $em->getRepository($this->listOfSharableContent[$notif->getNotification()->getSharedRef()])->findOneForListNotifs($notif->getNotification()->getSharedId());
                if($content){
                    $notif->getNotification()->setSharedContent($content);
                } 
            }
        }

        $datas['notifications_count_not_seen'] = $countAllNotifsNotSeen;        
        $datas['user'] = $user;
        $datas['notifications'] = $notifs;
        return View::create($datas, Response::HTTP_OK, []);
    }

    /**
     * Set manually seen notif by user.
     * @FOSRest\Post(path="/notification/seen", name="notif_post_one")
     * @RequestParam(
     *     name="id",
     *     requirements="[0-9]",
     *     nullable=false,
     *     description="Id of notification"
     * )
     */
    public function postNotifSeen(ParamFetcherInterface $paramFetcher, UserRepository $userRepo, NotificationRepository $nRepo, NotificationUserRepository $nuRepo, EntityManagerInterface $em)
    {
        $user = $userRepo->find(1);
        $notif = $nRepo->find($paramFetcher->get('id'));
        
        $notif = $nuRepo->findBy(['user'=>$user,'notification'=>$notif]);
        if($notif && $notif[0]->getSeen()===false){
            $notif[0]->setSeen(1);
            $em->persist($notif[0]);
            $em->flush();
        }else{
            return View::create(['message'=>'Opération impossible'], Response::HTTP_BAD_REQUEST, []);
        }

        return View::create(['message'=>'Notification vue'], Response::HTTP_OK, []);
    }

    // /**
    //  * Create Article.
    //  * @FOSRest\Post(path="/article", name="notif_post_one")
    //  *
    //  * @return array
    //  */

    // public function postNotifAction(Request $request)
    // {
    //     $article = new Article();
    //     $article->setName($request->get('name'));
    //     $article->setDescription($request->get('description'));
    //     $em = $this->getDoctrine()->getManager();
    //     $em->persist($article);
    //     $em->flush();

    //     return View::create($article, Response::HTTP_CREATED, []);
    // }
}