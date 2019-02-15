<?php
namespace Puzzle\CalendarBundle\Controller;

use Puzzle\CalendarBundle\Entity\Agenda;
use Puzzle\SchedulingBundle\Util\NotificationUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\CalendarBundle\Entity\Moment;
use Puzzle\SchedulingBundle\Entity\Recurrence;
use Puzzle\SchedulingBundle\Entity\Notification;
use Puzzle\CalendarBundle\Entity\CommentVote;
use Puzzle\CalendarBundle\Entity\Comment;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class AppController extends Controller
{
    public function listAgendasAction(Request $request) {
        return $this->render("AppBundle:Calendar:list_agendas.html.twig", array(
            'agendas' => $this->getDoctrine()->getRepository(Agenda::class)->findAll()
        ));
    }
    
    public function showAgendaAction(Request $request, $id) {
        /** @var Doctrine\ORM\EntityManager **/
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (! $agenda = $em->find(Agenda::class, $id)) {
            $agenda = $em->getRepository(Agenda::class)->findOneBy(['slug' => $id]);
        }
        
    	return $this->render("AppBundle:Calendar:show_agenda.html.twig", array(
    	    'agenda' => $agenda
    	));
    }
    
    public function listMomentsAction(Request $request) {
        $moments = $this->getDoctrine()->getRepository(Moment::class)->findAll();
        if ($request->getMethod() == "POST") {
            $data = [];
            foreach ($moments as $moment) {
                $data[] = [
                    'id' => $moment->getId(),
                    'title' => $moment->getTitle(),
                    'start' => $moment->getStartedAt()->format("Y-m-d H:i"),
                    'end' => $moment->getEndedAt()->format("Y-m-d H:i"),
                    'color' => $moment->getColor(),
                    'url' => $this->generateUrl('app_calendar_moment_show', array('id' => $moment->getId()))
                ];
            }
            
            return new JsonResponse($data);
        }
        
        return $this->render("AppBundle:Calendar:list_moments.html.twig", array(
            'moments' => $moments,
            'mode' => $request->get('mode')
        ));
    }
    
    public function showMomentAction (Request $request, $id) {
        
        /** @var Doctrine\ORM\EntityManager **/
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (! $moment = $em->find(Moment::class, $id)) {
            $moment = $em->getRepository(Moment::class)->findOneBy(['slug' => $id]);
        }
        
        $recurrence = null;
        if ($moment) {
            $recurrence = $em->getRepository(Recurrence::class)->findOneBy(NotificationUtil::constructTargetCriteria($moment));
        }
        
        $notification = null;
        if ($moment) {
            $notification = $em->getRepository(Notification::class)->findOneBy(NotificationUtil::constructTargetCriteria($moment));
        }
        
        return $this->render("AppBundle:Calendar:show_moment.html.twig", array(
            'moment' => $moment,
            'recurrence' => $recurrence,
            'notification' => $notification,
        ));
    }
    
    
    /**
     * List moment comments
     *
     * @param Request $request
     * @param Moment $moment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCommentsAction(Request $request, Moment $moment) {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class)->findBy(['moment' => $moment->getId()]);
        
        if ($request->isXmlHttpRequest() === true) {
            $array = [];
            
            if ($comments) {
                foreach ($comments as $comment) {
                    $item = [
                        'id' => $comment->getId(),
                        'created' => $comment->getCreatedAt()->format("Y-m-d H:i"),
                        'content' => $comment->getContent(),
                        'fullname' => $comment->getCreatedBy(),
                        'upvote_count' => $comment->getVotes() ? count($comment->getVotes()) : 0,
                        'user_has_upvoted' => $em->getRepository(CommentVote::class)->findOneBy([
                            'createdBy' => $this->getUser()->getFullName(),
                            'comment' => $comment->getId()
                        ]) ? true : false
                    ];
                    
                    if ($comment->getParentNode() !== null) {
                        $item['parent'] = $comment->getParentNode()->getId();
                    }
                    
                    $array[] = $item;
                }
            }
            
            return new JsonResponse($array);
        }
        
        return $this->render("AppBundle:Calendar:list_comments.html.twig", array('comments' => $comments));
    }
    
    /**
     * Add comment to moment
     *
     * @param Request $request
     * @param Moment $moment
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createCommentAction(Request $request, Moment $moment) {
        $data = $request->request->all();
        
        $comment = new Comment();
        $comment->setVisible(true);
        $comment->setMoment($moment);
        $comment->setContent($data['content']);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        
        if (isset($data['parent'])) {
            $parent = $em->getRepository(Comment::class)->find($data['parent']);
            $comment->setChildNodeOf($parent);
        }
        
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse([
                'status' => true,
                'id' => $comment->getId(),
                'created' => $comment->getCreatedAt()->format("Y-m-d"),
                'parent' => $comment->getParentNode() !== null ? $comment->getParentNode()->getId() : "",
                'content' => $comment->getContent(),
                'fullname' => $this->getUser()->getFullName(),
                'upvote_count' => 0,
                'user_has_upvoted' => false
            ]);
        }
        
        return $this->redirectToRoute('app_calendar_moment_show', ['id' => $moment->getId()]);
    }
    
    /**
     * Update comment
     *
     * @param Request $request
     * @param Moment $moment
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateCommentAction(Request $request, Comment $comment) {
        $data = $request->request->all();
        $comment->setVisible(true);
        $comment->setContent($data['content']);
        
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse([
                'status' => true,
                'id' => $comment->getId(),
                'created' => $comment->getCreatedAt()->format("Y-m-d"),
                'parent' => $comment->getParentNode() !== null ? $comment->getParentNode()->getId() : "",
                'content' => $comment->getContent(),
                'fullname' => $comment->getCreatedBy(),
                'upvote_count' => $comment->getVotes() ? count($comment->getVotes()) : 0,
                'user_has_upvoted' => $em->getRepository(CommentVote::class)->findOneBy([
                    'createdBy' => $this->getUser()->getFullName(),
                    'comment' => $comment->getId()
                ]) ? true : false
            ]);
        }
        
        return $this->redirectToRoute('app_calendar_moment_show', ['id' => $comment->getMoment()->getId()]);
    }
    
    /**
     * Delete comment
     *
     * @param Request $request
     * @param Moment $moment
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteCommentAction(Request $request, Comment $comment) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(null, 204);
        }
        
        return $this->redirectToRoute('app_calendar_moment_show', ['id' => $comment->getMoment()->getId()]);
    }
    
    
    /**
     * Vote comment
     *
     * @param Request $request
     * @param Moment $moment
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createCommentVoteAction(Request $request, Comment $comment) {
        $vote = new CommentVote();
        $vote->setComment($comment);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($vote);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($vote);
        }
        
        return $this->redirectToRoute('app_calendar_moment_show', ['id' => $comment->getMoment()->getId()]);
    }
    
    /**
     * Vote comment
     *
     * @param Request $request
     * @param Moment $moment
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteCommentVoteAction(Request $request, Comment $comment) {
        $em = $this->getDoctrine()->getManager();
        
        $momentId = $comment->getMoment()->getId();
        $vote = $em->getRepository(CommentVote::class)->findOneBy([
            'createdBy' => $this->getUser()->getFullName(),
            'comment' => $comment->getId()
        ]);
        
        if ($vote) {
            $em->remove($vote);
            $em->flush();
        }
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(null, 204);
        }
        
        return $this->redirectToRoute('app_calendar_moment_show', ['id' => $momentId]);
    }
}
