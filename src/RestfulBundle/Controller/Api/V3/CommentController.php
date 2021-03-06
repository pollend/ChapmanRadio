<?php

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Comment;
use CoreBundle\Form\CommentType;
use CoreBundle\Repository\CommentRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/v3/")
 */
class CommentController extends FOSRestController
{

    /**
     * @Security("has_role('ROLE_USER')")
     * @Rest\Patch("comment/{token}",
     *     options = { "expose" = true },
     *     name="patch_comment")
     * @Rest\View(serializerGroups={"detail"})
     */
    public function patchCommentAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var CommentRepository $commentRepository */
        $commentRepository = $em->getRepository(Comment::class);

        /** @var Comment $comment */
        if ($comment = $commentRepository->getCommentByToken($token)) {
            $this->denyAccessUnlessGranted('edit', $comment);

            $form = $this->createForm(CommentType::class,$comment,array(
                'method' => 'PATCH',
            ));
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em->persist($comment);
                $em->flush();
                return $this->view(['comment' => $comment]);
            }
            return $this->view($form);
        }
        throw $this->createNotFoundException('Comment Not Found');
    }
}
