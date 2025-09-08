<?php

namespace Amplify\System\CustomItem\Traits;

use Amplify\System\Cms\Models\Page;

trait CustomDynamicPage
{
    /**
     * This program will set the dynamic page guessed
     * from slug to display on this request
     * N.B: For page type declaration
     *
     * @param  string|null  $slug
     *
     * @throws \ErrorException
     *
     * @see modules/Cms/Config/cms.php
     * @see modules/Frontend/Config/frontend.php
     */
    public function loadPageBySlugandType($slug): void
    {
        if ($slug == null) {
            abort('500', 'Page Slug is missing or does not exists.');
        }
        $page = Page::published()->whereSlug($slug)->wherePageType('custom_product')->first();

        if (! $page) {
            abort(404, 'Page Not Found');
        }

        // if ($page->page_type != 'static_page') {
        //     abort(403, 'Reserved Page cannot be accessed directly.');
        // }

        store()->dynamicPageModel = $page;
    }

    /**
     * @throws \ErrorException
     */
    public function render(): string
    {
        $page = store('dynamicPageModel');

        $pageTitle = store('pageTitle');

        $meta_data = $page->meta_tags;

        $content = $page->content;

        return view(theme_view('index'), compact('meta_data', 'content', 'pageTitle'))->render();
    }
}
