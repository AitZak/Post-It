<?php

namespace App\Controller;

use App\Entity\Content;
use App\Entity\User;
use App\Form\UserAdminType;
use App\Form\UserType;
use App\Manager\ApprovalManager;
use App\Manager\CommentManager;
use App\Manager\ContentManager;
use App\Manager\ModificationManager;
use App\Manager\PublicationManager;
use App\Manager\UserManager;
use App\Repository\SocialNetworkRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($this->getUser()->getRoles() !== ['ROLE_ADMIN']) {
            return $this->render('main/error_role.html.twig');
        }

        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder,
                        \Swift_Mailer $mailer,
                        TokenGeneratorInterface $tokenGenerator): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($this->getUser()->getRoles() !== ['ROLE_ADMIN']) {
            return $this->render('main/error_role.html.twig');
        }

        $user = new User();
        $form = $this->createForm(UserAdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (in_array('ROLE_ADMIN', $user->getRoles())){
                $user->setRoles(['ROLE_ADMIN']);
            }
            if (in_array('ROLE_COMM', $user->getRoles())){
                $user->setRoles(['ROLE_COMM']);
            }
            if (in_array('ROLE_REVIEWER', $user->getRoles())){
                $user->setRoles(['ROLE_REVIEWER']);
            }

//            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword("init");
            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $url = $this->generateUrl('app_reset_password', array('token' => $token),
                UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message('Forgot Password'))
                ->setFrom('post.it.lyz@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    "Voici un lien pour créer votre mot de passe: " . $url,
                    'text/html'
                );

            $mailer->send($message);

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($user !== $this->getUser() && $this->getUser()->getRoles() !== ['ROLE_ADMIN']) {
            return $this->render('main/error_role.html.twig');
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param $socialNetworkRepository
     * @return Response
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder,
                         SocialNetworkRepository $socialNetworkRepository): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($user !== $this->getUser() ) {
            return $this->render('main/error_role.html.twig');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_show',[
                'id' => $user ->getId()
            ]);
        }

        $socialNetworks = $socialNetworkRepository -> findAll();
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'socialNetworks'=> $socialNetworks
        ]);
    }

    /**
     * @Route("/{id}/edit/admin", name="user_edit_admin", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param $socialNetworkRepository
     * @return Response
     */
    public function editAdmin(Request $request, User $user,
                         SocialNetworkRepository $socialNetworkRepository): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($user !== $this->getUser() && $this->getUser()->getRoles() !== ['ROLE_ADMIN']) {
            return $this->render('main/error_role.html.twig');
        }

        $form = $this->createForm(UserAdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        $socialNetworks = $socialNetworkRepository -> findAll();
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'socialNetworks'=> $socialNetworks
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        User $user,
        UserManager $userManager,
        ContentManager $contentManager,
        CommentManager $commentManager,
        ApprovalManager $approvalManager,
        PublicationManager $publicationManager,
        ModificationManager $modificationManager
    ): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($this->getUser()->getRoles() !== ['ROLE_ADMIN']) {
            return $this->render('main/error_role.html.twig');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->removeUserRelatedData($user, $userManager, $contentManager, $commentManager, $approvalManager, $publicationManager, $modificationManager);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/{id}/submissions", name="user_submissions", methods={"GET"})
     */
    public function mySubmissions(User $user, ContentManager $contentManager): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        $submissions = $contentManager->getSubmissionsByUser($user);
        return $this->render('user/submissions.html.twig', [
            'user' => $user,
            'submissions' => $submissions,
        ]);
    }

    /**
     * @Route("/{id}/reviews", name="user_reviews", methods={"GET"})
     */
    public function myReviews(User $user, ApprovalManager $approvalManager): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($this->getUser()->getRoles() !== ['ROLE_ADMIN'] && $this->getUser()->getRoles() !== ['ROLE_COMM'] && $this->getUser()->getRoles() !== ['ROLE_REVIEWER']) {
            return $this->render('main/error_role.html.twig');
        }

        $reviews = $approvalManager->getApprovalsByUser($user);
        return $this->render('user/reviews.html.twig', [
            'user' => $user,
            'reviews' => $reviews,
        ]);
    }

    /**
     * @Route("/{id}/publications", name="user_publications", methods={"GET"})
     */
    public function myPublications(User $user, ContentManager $contentManager): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($this->getUser()->getRoles() !== ['ROLE_ADMIN'] && $this->getUser()->getRoles() !== ['ROLE_COMM']) {
            return $this->render('main/error_role.html.twig');
        }

        $publications = $contentManager->getPublicatedSubmissionsByUser($user);
        return $this->render('user/publications.html.twig', [
            'user' => $user,
            'publicationsInfos' => $publications,
        ]);
    }


    /**
     * @Route("/userSocialNetwork_add", name="userSocialNetwork_add", methods={"POST"})
     */
    public function userSocialNetwork_add(Request $request, UserRepository $userRepository,
                                          SocialNetworkRepository $socialNetworkRepository): Response
    {

        $user = $userRepository -> findOneBy(["id" => $request -> get('idUser')]);
        $socialNetwork = $socialNetworkRepository -> findOneBy(["id" => $request -> get('SN-select')]);
        $socialNetwork -> addUser($user);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('user_edit', ['id' => $request -> get('idUser')] );
    }

    /**
     * @Route("/userSocialNetwork_delete", name="userSocialNetwork_delete", methods={"POST"})
     */
    public function userSocialNetwork_delete(Request $request, UserRepository $userRepository,
                                             SocialNetworkRepository $socialNetworkRepository): Response
    {

        $user = $userRepository -> findOneBy(["id" => $request -> get('idUser')]);
        $socialNetwork = $socialNetworkRepository -> findOneBy(["id" => $request -> get('SN-select')]);
        $socialNetwork -> removeUser($user);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('user_edit', ['id' => $request -> get('idUser')] );
    }

    private function removeUserRelatedData(
        User $user,
        UserManager $userManager,
        ContentManager $contentManager,
        CommentManager $commentManager,
        ApprovalManager $approvalManager,
        PublicationManager $publicationManager,
        ModificationManager $modificationManager
    )
    {
        $toDelete = $userManager->prepareUserToDeletion($user, $contentManager,$commentManager,$approvalManager,$publicationManager,$modificationManager);

        foreach ($toDelete['comments'] as $comment){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        foreach ($toDelete['approvals'] as $approval){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($approval);
            $entityManager->flush();
        }

        foreach ($toDelete['modifications'] as $modification){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($modification);
            $entityManager->flush();
        }

        foreach ($toDelete['publications'] as $publication){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($publication);
            $entityManager->flush();
        }

        foreach ($toDelete['contents'] as $submission){
            $this->removeContentRelatedData($submission,$contentManager,$commentManager,$approvalManager,$publicationManager,$modificationManager);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($submission);
            $entityManager->flush();
        }
    }

    private function removeContentRelatedData(
        Content $content,
        ContentManager $contentManager,
        CommentManager $commentManager,
        ApprovalManager $approvalManager,
        PublicationManager $publicationManager,
        ModificationManager $modificationManager
    )
    {
        $toDelete = $contentManager->prepareContentToDeletion($content, $commentManager, $approvalManager, $publicationManager, $modificationManager);

        foreach ($toDelete['comments'] as $comment){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        foreach ($toDelete['approvals'] as $approval){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($approval);
            $entityManager->flush();
        }

        foreach ($toDelete['modifications'] as $modification){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($modification);
            $entityManager->flush();
        }

        foreach ($toDelete['publications'] as $publication){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($publication);
            $entityManager->flush();
        }
    }

}
