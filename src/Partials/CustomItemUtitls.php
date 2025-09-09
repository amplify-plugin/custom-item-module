<?php

namespace Amplify\System\CustomItem\Partials;

final class CustomItemUtitls
{
    public function customItemPath($path = null)
    {
        if (! empty($path)) {
            return base_path().'/plugins/CustomItem'.$path;
        }

        return base_path().'/plugins/CustomItem';
    }

    /**
     * Custom Item Storage path
     *
     * @param  null  $path
     * @return [type]
     */
    public function storagePath($path = null)
    {
        if (! empty($path)) {
            return base_path().'/plugins/CustomItem/Storage'.$path;
        }

        return base_path().'/plugins/CustomItem/Storage';
    }
}
