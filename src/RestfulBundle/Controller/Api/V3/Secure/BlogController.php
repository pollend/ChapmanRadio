<?php

namespace RestfulBundle\Controller\Api\V3\Secure;

use CoreBundle\Entity\Category;
use CoreBundle\Entity\Image;
use CoreBundle\Entity\Post;
use CoreBundle\Entity\Tag;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Normalizer\DatatableNormalizer;
use CoreBundle\Normalizer\PaginatorNormalizer;
use CoreBundle\Normalizer\PostNormalizer;
use CoreBundle\Normalizer\CategoryNormalizer;
use CoreBundle\Normalizer\ImageNormalizer;
use CoreBundle\Normalizer\TagNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Repository\CategoryRepository;
use CoreBundle\Repository\PostRepository;
use CoreBundle\Repository\TagRepository;
use CoreBundle\Security\PostVoter;
use CoreBundle\Service\ImageUploadService;
use CoreBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v3/private")
 */
class BlogController extends Controller
{


    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/post/datatable",
     *     options = { "expose" = true },
     *     name="get_post_dataTable")
     * @Method({"GET"})
     */
    public function getPostDatatableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);


        return RestfulEnvelope::successResponseTemplate('', $postRepository->dataTableFilter($request),
            [$this->get(PostNormalizer::class), new PaginatorNormalizer(),new DatatableNormalizer(), new UserNormalizer()])->response();
    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/post",
     *     options = { "expose" = true },
     *     name="post_post")
     * @Method({"POST"})
     */
    public function postPostAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = new Post();

        $form = $this->createForm(PostType::class,$post);
        $form->submit($request->request->all());
        if($form->isValid())
        {
            $em->persist($post);
            $em->flush();
            return RestfulEnvelope::successResponseTemplate('Post Added',$post,[ $this->get(PostNormalizer::class),new UserNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate("Invalid Post")->addFormErrors($form)->response();
    }


    /**
     * @Route("/post/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="patch_post")
     * @Method({"PATCH"})
     */
    public function patchPostAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);

            $form = $this->createForm(PostType::class,$post);
            $form->submit($request->request->all());
            if($form->isValid())
            {
                $em->persist($post);
                $em->flush();
                return RestfulEnvelope::successResponseTemplate('Post Updated',$post,[$this->get(PostNormalizer::class),new UserNormalizer()])->response();
            }
            return RestfulEnvelope::errorResponseTemplate("Invalid Post")->addFormErrors($form)->response();
        }
        return RestfulEnvelope::errorResponseTemplate("Invalid Post")->setStatus(410)->response();

    }


    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/post/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="delete_post")
     * @Method({"DELETE"})
     */
    public function deletePostAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        if($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::DELETE, $post);
            $em->remove($post);
            $em->flush();

            return RestfulEnvelope::successResponseTemplate('Bost post deleted',$post,[new PostNormalizer(),new UserNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Post not found')->response();

    }
    //----------------------------------------------------------------------------------------

    /**
     * @Route("/post/{token}/{slug}/tag/{tag}",
     *     options = { "expose" = true },
     *     name="put_tag_post")
     * @Method({"PUT"})
     */
    public function putTagForPostAction(Request $request, $token, $slug, $tag)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        if($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            if($post->getTags()->containsKey($tag))
                return RestfulEnvelope::errorResponseTemplate('duplicate Tag')->response();

            /** @var TagRepository $tagRepository */
            $tagRepository = $em->getRepository(Tag::class);

            $tag = $tagRepository->getOrCreateTag($tag);
            $em->persist($tag);
            $post->addTag($tag);
            $em->persist($post);
            $em->flush();

            return RestfulEnvelope::successResponseTemplate('Tag added',$tag,[new TagNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Post not found')->setStatus(410)->response();
    }


    /**
     * @Route("/post/{token}/{slug}/image",
     *     options = { "expose" = true },
     *     name="post_image_post")
     * @Method({"POST"})
     */
    public function putImageForPostAction(Request $request, $token, $slug)
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var ImageUploadService $imageService */
        $imageService = $this->get(ImageUploadService::class);

        /** @var Post $post */
        if ( $post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);

            $src = $request->files->get('image', null);
            $image = new Image();
            $image->setImage($src);
            $image->setAuthor($this->getUser());

            $errors = $validator->validate($image);
            if($errors->count() > 0)
                return RestfulEnvelope::errorResponseTemplate('invalid Image')->addErrors($errors)->response();

            $imageService->saveImageToFilesystem($image);
            $em->persist($image);

            $post->addImage($image);
            $em->persist($post);
            $em->flush();
            return RestfulEnvelope::successResponseTemplate('Image Uploaded',$image,[new ImageNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Image error')->response();
    }

    /**
     * @Route("/post/{token}/{slug}/image",
     *     options = { "expose" = true },
     *     name="get_image_post")
     * @Method({"GET"})
     */
    public function getImageForPostAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            return RestfulEnvelope::successResponseTemplate('Image Uploaded',$post->getImages()->toArray(),
                [new ImageNormalizer(),new TagNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Post not found')->response();
    }


    /**
     * @Route("/post/{token}/{slug}/tag/{tag}",
     *     options = { "expose" = true },
     *     name="delete_tag_post")
     * @Method({"DELETE"})
     */
    public function deleteTagForPostAction(Request $request, $token, $slug, $tag)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */

        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            if($t = $post->removeTag($tag)) {
                $em->persist($post);
                $em->flush();
                return RestfulEnvelope::successResponseTemplate('Tag deleted', $t,
                    [new TagNormalizer()])->response();
            }
            return RestfulEnvelope::errorResponseTemplate('Tag not found')->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Post not found')->response();

    }


    /**
     * @Route("/post/{token}/{slug}/category/{category}", options = { "expose" = true }, name="put_category_post")
     * @Method({"PUT"})
     */
    public function putCategoryForPostAction(Request $request, $token, $slug, $category)
    {

        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        if($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            if($post->getCategories()->containsKey($category))
                return RestfulEnvelope::errorResponseTemplate('duplicate Tag')->response();

            /** @var CategoryRepository $categoryRepository */
            $categoryRepository = $em->getRepository(Category::class);

            $category = $categoryRepository->getOrCreateCategory($category);
            $em->persist($category);
            $post->addCategory($category);
            $em->persist($post);
            $em->flush();

            return RestfulEnvelope::successResponseTemplate('Category added',$category,[new CategoryNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Post not found')->response();

    }

    /**
     * @Route("/post/{token}/{slug}/category/{category}",
     *     options = { "expose" = true },
     *     name="delete_category_post")
     * @Method({"DELETE"})
     */
    public function deleteCategoryForPostAction(Request $request, $token, $slug, $category)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            if($c = $post->removeCategory($category)) {
                $em->persist($post);
                $em->flush();
                return RestfulEnvelope::successResponseTemplate('Category deleted', $c,
                    [new CategoryNormalizer()])->response();
            }
            return RestfulEnvelope::errorResponseTemplate('Category not found')->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Post not found')->response();

    }
}

