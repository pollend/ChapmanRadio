/* @flow */
/* global Routing */
/* global FormData */
import axios from 'axios'
import qs from 'qs'
import Pagination from '../entity/pagination'
import Post from './../entity/post'
import Comment from './../entity/comment'
import Datatable from './../entity/dataTable'
import Form from './../entity/form'
import Media from './../entity/media'

export default {
  getPostsDatatable: function (page : number, sort : [], callback : (result: Datatable<Pagination<Post>>) => void, filter: any = {}) {
    const result = Object.assign({ page: page, sort: sort }, filter)
    return axios.get(Routing.generate('get_post_dataTable') + '?' + qs.stringify(result)).then((response) => {
      callback(new Datatable((paginationData) => new Pagination((postData) => new Post(postData), paginationData), response.data.datatable))
    })
  },
  getPosts: function (page : number, callback : (result: Pagination<Post>) => void, filter: any = {}) {
    const result = Object.assign({ page: page }, filter)
    return axios.get(Routing.generate('get_posts') + '?' + qs.stringify(result)).then((response) => {
      callback(new Pagination((postData) => new Post(postData), response.data.payload))
    })
  },
  getPost: function (token: string, slug:string, callback : (result: Post) => void, parse: string = 'HTML') {
    return axios.get(Routing.generate('get_post', { token: token, slug: slug }) + '?' + qs.stringify({ delta: parse })).then((response) => {
      callback(new Post(response.data.post))
    })
  },
  postPost: function (post: Post, callback : (result: Form | Post) => void) {
    return axios.post(Routing.generate('post_post'), qs.stringify({
      post: {
        name: post.name,
        content: post.content,
        excerpt: post.excerpt,
        slug: post.slug,
        isPinned: post.isPinned,
        tags: post.tags,
        categories: post.categories
      }})).then((response) => {
      callback(new Post(response.data.post))
    }).catch((error) => {
      if (error.response) {
        if (error.response.status === 500) {
          callback(new Form(error.response.data))
        }
      }
    })
  },
  patchPost: function (token: string, slug:string, post: Post, callback : (result: Form | Post) => void) {
    return axios.patch(Routing.generate('patch_post', { token: token, slug: slug }), qs.stringify({
      post: {
        name: post.name,
        content: post.content,
        excerpt: post.excerpt,
        slug: post.slug,
        isPinned: post.isPinned,
        tags: post.tags,
        categories: post.categories
      }})).then((response) => {
      callback(new Post(response.data.post))
    }).catch((error) => {
      if (error.response) {
        if (error.response.status === 500) {
          callback(new Form(error.response.data))
        }
      }
    })
  },
  postPostMedia: function (post:Post, media: Media, callback: (result: Media | Form) => void) {
    const formData = new FormData()
    formData.append('media[file]', media.file)
    formData.append('media[title]', media.title)
    formData.append('media[caption]', media.caption)
    formData.append('media[altText]', media.altText)
    formData.append('media[description]', media.description)
    return axios.post(Routing.generate('post_media_post', { token: post.token, slug: post.slug }), formData).then((response) => {
      callback(new Media(response.data.media))
    }).catch((error) => {
      if (error.response) {
        if (error.response.status === 500) {
          callback(new Form(error.response.data))
        }
      }
    })
  },
  getPostComments: function (post:Post, root: (Comment | null), callback : (result: Comment) => void) {
    let commentToken = null
    if (root !== null) {
      commentToken = root.token
    }
    return axios.get(Routing.generate('get_blog_comment', { token: post.token, slug: post.slug, comment_token: commentToken })).then((response) => {
      callback(response.data.comments.map((r) => new Comment(r)))
    })
  },
  postPostComment: function (post: Post, comment: string, root: (Comment | null), callback : (result: Comment) => void) {
    const payload: {
      parentComment?: string,
      content: string
    } = {
      content: comment
    }
    if (root !== null) {
      payload.parentComment = root.token
    }
    return axios.post(Routing.generate('post_post_comment', { token: post.token, slug: post.slug }), qs.stringify({ 'comment': payload })).then((response) => {
      callback(new Comment(response.data.comment))
    }).catch((error) => {
      if (error.response) {
        if (error.response.status === 400) {
          callback(new Form(error.response.data))
        }
      }
    })
  },
  getPostTags: function (token: string, slug: string, responseCallback : (result: [string]) => void) {
    return axios.get(Routing.generate('get_post_tags', { token: token, slug: slug })).then((response) => {
      responseCallback(response.data.tags)
    })
  },
  getPostCategories: function (token: string, slug: string, responseCallback : (result: [string]) => void) {
    return axios.get(Routing.generate('get_post_categories', { token: token, slug: slug })).then((response) => {
      responseCallback(response.data.categories)
    })
  },
  deletePostTag: function (post: Post, tag: string, responseCallback : (result: string) => void) {
    return axios.delete(Routing.generate('delete_tag_post', { token: post.getToken(), slug: post.getSlug(), tag: tag })).then((response) => {
      responseCallback(response.data.tag)
    })
  },
  putPostTag: function (post: Post, tag: string, responseCallback : (result: string) => void) {
    return axios.put(Routing.generate('put_tag_post', { token: post.getToken(), slug: post.getSlug(), tag: tag })).then((response) => {
      responseCallback(response.data.tag)
    })
  },
  deletePostCategory: function (post: Post, category: string, responseCallback : (result: string) => void) {
    return axios.delete(Routing.generate('delete_category_post', { token: post.getToken(), slug: post.getSlug(), category: category })).then((response) => {
      responseCallback(response.data.category)
    })
  },
  putPostCategory: function (post: Post, category: string, responseCallback : (result: string) => void) {
    return axios.put(Routing.generate('put_category_post', { token: post.getToken(), slug: post.getSlug(), category: category })).then((response) => {
      responseCallback(response.data.category)
    })
  }
}
