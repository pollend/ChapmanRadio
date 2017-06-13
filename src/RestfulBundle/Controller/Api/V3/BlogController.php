<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/24/17
 * Time: 10:46 PM
 */

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Post;
use CoreBundle\Entity\Comment;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\DateTimeNormalizer;
use CoreBundle\Normalizer\PostNormalizer;
use CoreBundle\Normalizer\CategoryNormalizer;
use CoreBundle\Normalizer\CommentNormalizer;
use CoreBundle\Normalizer\PaginatorNormalizer;
use CoreBundle\Normalizer\TagNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Repository\PostRepository;
use CoreBundle\Repository\CommentRepository;
use RestfulBundle\Validation\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * @Route("/api/v3/")
 */
class BlogController extends Controller
{

    /**
     * @Route("post",
     *     options = { "expose" = true },
     *     name="get_posts")
     * @Method({"GET"})
     */
    public function getPostsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository =  $em->getRepository(Post::class);

        $pagination = $postRepository->paginator($postRepository->filter($request),
            (int)$request->get('page',0),
            (int)$request->get('entries',10),20);

        return RestfulEnvelope::successResponseTemplate(null,$pagination,
            [new PostNormalizer(),new UserNormalizer(),new PaginatorNormalizer(),new DateTimeNormalizer()])->response();
    }

    /**
     * @Route("post/{token}/{slug}/tags",
     *     options = { "expose" = true },
     *     name="get_post_tags")
     * @Method({"GET"})
     */
    public function getPostTagsAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
            return RestfulEnvelope::successResponseTemplate("Tags",$post->getTags(),[new TagNormalizer()])->response();
        return RestfulEnvelope::errorResponseTemplate("Post not found")->setStatus(410)->response();
    }

    /**
     * @Route("post/{token}/{slug}/categories",
     *     options = { "expose" = true },
     *     name="get_post_categories")
     * @Method({"GET"})
     */
    public function getPostCategoriesAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
            return RestfulEnvelope::successResponseTemplate("Categories",$post->getCategories(),[new CategoryNormalizer()])->response();
        return RestfulEnvelope::errorResponseTemplate("Post not found")->setStatus(410)->response();
    }

    /**
     * @Route("post/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="get_post", )
     * @Method({"GET"})
     */
    public function getPostAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var Post $post */
        if ( $post = $postRepository->getPostByTokenAndSlug($token,$slug))
            return RestfulEnvelope::successResponseTemplate("Post found",$post,[new PostNormalizer(),new UserNormalizer()])->response();
        return RestfulEnvelope::errorResponseTemplate("Post not found")->setStatus(410)->response();
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("post/{token}/{slug}/comment/{comment_token}",
     *     options = { "expose" = true },
     *     name="post_post_comment")
     * @Method({"POST"})
     */
    public function postPostCommentAction(Request $request, $token, $slug, $comment_token = null)
    {

        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var CommentRepository $commentRepository */
        $commentRepository = $em->getRepository(Comment::class);

        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token,$slug))
        {
            $comment = new Comment();
            $comment->setContent($request->get("content"));
            $comment->setUser($this->getUser());
            $post->addComment($comment);

            if ($comment_token !== null) {
                if($c = $commentRepository->getCommentByPostAndToken($post, $comment_token))
                    $comment->setParentComment($c);
                else
                    return RestfulEnvelope::errorResponseTemplate("Unknown comment")->setStatus(410)->response();
            }

            $form = $this->createForm(CommentType::class,$comment);
            $form->submit($request->request->all());
            if($form->isValid())
            {
                $em->persist($comment);
                $em->persist($post);
                $em->flush();
                return RestfulEnvelope::successResponseTemplate('Comment Added',$comment,[new UserNormalizer(),new CommentNormalizer()])->response();
            }
            return RestfulEnvelope::errorResponseTemplate("invalid Comment")->addFormErrors($form)->response();
        }
        return RestfulEnvelope::errorResponseTemplate("comment not found")->setStatus(410)->response();
    }


    /**
     * @Route("post/{token}/{slug}/comment/{comment_token}",
     *     options = { "expose" = true },
     *     name="get_blog_comment")
     * @Method({"GET"})
     */
    public function getPostCommentAction(Request $request, $token, $slug, $comment_token = null)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var CommentRepository $commentRepository */
        $commentRepository = $em->getRepository(Comment::class);

        /** @var Post $post */
        if($post = $postRepository->getPostByTokenAndSlug($token, $slug)) {
            if ($comment_token) {
                return RestfulEnvelope::successResponseTemplate('Comments',
                    $commentRepository->getCommentByPostAndToken($post,$comment_token),
                    [new UserNormalizer(), new CommentNormalizer()])->response();
            }
            else
            {
                return RestfulEnvelope::successResponseTemplate('Comments',
                    $commentRepository->getAllRootCommentsForPost($post),
                    [new UserNormalizer(), new CommentNormalizer()])->response();
            }

        }
        return RestfulEnvelope::errorResponseTemplate("Unknown comment")->setStatus(410)->response();
    }

}
