<?php

namespace App\Controller;

use App\Entity\Approval;
use App\Form\ApprovalType;
use App\Manager\ApprovalManager;
use App\Manager\ContentManager;
use App\Repository\ApprovalRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Api\TumblrApi;

/**
 * @Route("/approval")
 */
class ApprovalController extends AbstractController
{
    /**
     * @Route("/", name="approval_index", methods={"GET"})
     */
    public function index(ApprovalRepository $approvalRepository): Response
    {
        return $this->render('approval/index.html.twig', [
            'approvals' => $approvalRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="approval_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, ContentManager $contentManager, ApprovalManager $approvalManager): Response
    {
        $date = new \DateTime();
        $content = $contentManager->getContentById(intval($request->get('content_id')));
        if (!$content->getApprovalDate()){
            $content->setApprovalDate($date);
        }
        if ($content->getStatut() === 2) {
            return new Response(
                "Ce contenu a été refusé par un autre reviewer, veuillez passer par un communicant si vous souhaitez que ce contenu soit publié."
            );
        }
        $content->setStatut(intval($request->get('status')));
        $approval = new Approval();

        $approval->setUser($this->getUser());
        $approval->setContent($content);

        $review = $approvalManager->alreadyReviewed($approval);

        if (!$review){
            $approval->setStatus(intval($request->get('status')));
            $em->persist($approval);
            $em->flush();

            return $this->redirectToRoute('content_show', [
                'id'=>$content->getId()
            ]);
        }
        $review->setStatus(intval($request->get('status')));
        $em->flush();

        return $this->redirectToRoute('content_show', [
            'id'=>$content->getId()
        ]);
    }

    /**
     * @Route("/{user_id}", name="approval_show", methods={"GET"})
     */
    public function show(ApprovalManager $approvalManager): Response
    {
        $user = $this->getUser();
        $acceptedReviews = $approvalManager->getAccpetedReviewsByUser($user);
        $rejectedReviews = $approvalManager->getRejectedReviewsByUser($user);


        return $this->render('approval/show.html.twig', [
            'acceptedReviews' => $acceptedReviews,
            'rejectedReviews' => $rejectedReviews,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="approval_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Approval $approval): Response
    {
        $form = $this->createForm(ApprovalType::class, $approval);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('approval_index');
        }

        return $this->render('approval/edit.html.twig', [
            'approval' => $approval,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="approval_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Approval $approval): Response
    {
        if ($this->isCsrfTokenValid('delete'.$approval->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($approval);
            $entityManager->flush();
        }

        return $this->redirectToRoute('approval_index');
    }

    /**
     * @Route("/publish_approval", name="publish_approval")
     */
    public function ghjbi(Request $request, ApprovalRepository $approvalRepository, TumblrApi $tumblrApi)
    {
//        $approval = $approvalRepository ->findOneBy(["id" => $request->get("approvalId")]);
//        $content = $approval -> getContent();
//        $user = $this->getUser();
//        $socialsNetworks = $user -> getSocialNetwork();
//
//        $contentValues['title'] = $content -> getFile();
//        if($content ->getFile()){
//            $contentValues['file'] = $content -> getFile();
//            $contentValues['type'] = $content -> getTypeFile();
//        }
//        if($content -> getDescription()){
//            $contentValues['description'] = $content -> getDescription();
//        }
//
//        var_dump($contentValues);
//        foreach($socialsNetworks as $socialsNetwork){
//            if($socialsNetwork -> getName() == 'tumblr'){
//                $consumer_key = $socialsNetwork -> getClientId();
//                $consumer_secret = $socialsNetwork -> getClientSecret();
//                $token = $socialsNetwork -> getToken();
//                $token_secret = $socialsNetwork -> getTokenSecret();
//
//                $data = $tumblrApi->createData($contentValues);
//                $this -> post($data, $consumer_key, $consumer_secret, $token, $token_secret);
//            }
//
//
//        }

        return $this->redirectToRoute('content');
    }

    public function post($data, $consumer_key, $consumer_secret, $token, $token_secret){
        $stack = HandlerStack::create();
        $middleware = new Oauth1([
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret,
            'token' => $token,
            'token_secret' => $token_secret]);
        $stack->push($middleware);

        $client = new Client([
            'base_uri' => 'https://api.tumblr.com/v2/',
            'handler' => $stack,
            'auth' => 'oauth',
            'headers' => [ 'Content-Type' => 'application/json' ]
        ]);


        $res = $client->post('blog/androgynouskingtale/post',['body' => json_encode($data)]);
        $end = json_decode($res->getBody(), true);
//        return $this->render('test.html.twig', [
//            'test' => $end,]);
    }
}
