<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\System\CustomItem\Traits\CustomDynamicPage;

class CustomItemController extends BaseController
{
    use CustomDynamicPage;

    /**
     * Get Strips Replacement Product From JSON File
     *
     * @return [type]
     */
    public function index($slug, $id = null)
    {
        try {
            $this->loadPageBySlugandType($slug);

            return $this->render();
        } catch (\Exception $exception) {
            abort(500, $exception->getMessage());
        }
    }
}
