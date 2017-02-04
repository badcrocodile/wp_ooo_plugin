<?php
namespace IngredientManager;


class CreatePostType
{
    /**
     * CreatePostType constructor. Creates custom post types.
     *
     * @param string $slug   Permalink structure slug
     * @param array  $opts   Post type options
     *
     */
    public function __construct($slug, array $opts)
    {
        $this->slug = $slug;
        $this->opts = $opts;
    }

    public function createPostType()
    {
        return register_post_type(strtolower($this->slug), $this->opts);
    }


}